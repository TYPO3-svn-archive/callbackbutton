<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Tobias Hahn <thahn@telekom.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('callbackbutton','pi1/createHTMLTags.php'));
require_once(t3lib_extMgm::extPath('callbackbutton','pi1/DbFormatter.php'));

				

/**
 * Plugin 'DG callbackbutton' for the 'callbackbutton' extension.
 *
 * @author	Tobias Hahn <thahn@telekom.de>
 * @package	TYPO3
 * @subpackage	tx_callback
 */
class tx_callbackbutton_pi1 extends tslib_pibase {
	var $prefixId      = 'tx_callbackbutton_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_callbackbutton_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'callbackbutton';	// The extension key.
	var $pi_checkCHash = true;
	var $flexFormData;
	var $vcClient;
	var $newCallSessionID;
	var $feUserNr;
	var $fe_user_id;
	var $continue;
	var $infoText;
	var $freeCap;
	var $voiceCallInst;
	var $freeCapMarkerArray;
	var $fakeId;
	var $dbFormatter;
	var $newPeriodStart;
	var $newPeriodEnd;
	var $vcNr;
	private  $divLeft;
	private $hidden;
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj = 1;
		$this->continue = true; 
		$this->fe_user_id = $GLOBALS['TSFE']->fe_user->user['uid'] ;
		$this->flexFormData = $this->initFF();
		$callAllowed = true;
		$callsLeftText = htmlspecialchars($this->pi_getLL('callsLeftText'));
		$this->hidden = 'hidden';
		//echo '<pre>'.print_r(get_defined_constants(),true).'</pre>';
		//Captcha einbinden
		if($this->flexFormData['captcha']) {
			
			$this->hidden = 'text';
			require_once('voiceCallClass.php');
			$this->voiceCallInst = t3lib_div::makeInstance('voiceCallClass');
			$this->freeCapMarkerArray = $this->voiceCallInst->createCaptcha();
			
			if (is_object($this->voiceCallInst->freeCap)) {
				
				$captchaCheck  = true;
				
			}
		}
		//Temlate
		$dg_callbackbutton_template = $this->cObj->fileResource($conf['templateFile']);
		$subPart = $this->cObj->getSubpart($dg_callbackbutton_template,'###DEFAULT###');
		$FE_sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'callbackbutton');
		
		//Checks whether there was an error before, set's the text and clears the sessiondata
		if(isset($FE_sessionData['error'])){
			$this->infoText = $this->getError($FE_sessionData['error']);
			$GLOBALS['TSFE']->fe_user->setKey('ses', 'callbackbutton', null);
			$GLOBALS['TSFE']->fe_user->storeSessionData();
		}
		//creating user to test the functions
				
		$callBackData = array(
			'environment' => $this->flexFormData['environment'],
			'user' => $this->flexFormData['user'], 
			'password' => $this->flexFormData['password'],
			'number1' => $this->flexFormData['number1'],
			'number2' => $this->flexFormData['number2'],
			'maxDur' => $this->flexFormData['maxDur'],
			'maxWait' => $this->flexFormData['maxWait'],
			'countries' => $this->flexFormData['countries'],
			'captchaCheck' => $captchaCheck,
			'fe_user' => $this->fe_user_id,
			'stats' => array(
				'IDLE' => htmlspecialchars($this->pi_getLL('stat_idle')),
				'CONNECTING' => htmlspecialchars($this->pi_getLL('stat_connecting')), 
				'DISCONNECTING' => htmlspecialchars($this->pi_getLL('stat_disconnecting')), 
				'DISCONNECTED' => htmlspecialchars($this->pi_getLL('stat_disconnected')), 
				'CONNECTED' => htmlspecialchars($this->pi_getLL('stat_connected')), 
				'RINGING' => htmlspecialchars($this->pi_getLL('stat_ringing')), 
				'DEFAULT' => htmlspecialchars($this->pi_getLL('stat_default')), 
			),
			'stopStat' => false,
			
			
		); 
		
		
		$this->setSessionData($callBackData);
		
		
		if($this->flexFormData['secure']){
			
			if(!($this->fe_user_id)){
				$this->continue = false;
			}else{
			$this->dbFormatter = t3lib_div::makeInstance('DbFormatter');
			$this->dbFormatter->period = $this->flexFormData['period'];
			$this->dbFormatter->useAmount = $this->flexFormData['useAmount'];
			
			$db_Array = array(
				'secu' => $this->dbFormatter->init_secu($this->fe_user_id, 
					'tx_callbackbutton_secu', $callsLeftText)
				);
			//	var_dump($db_Array);
			$this->divLeft = $db_Array['secu']['callsLeft'];
			if(!($db_Array['secu']['checkSecu'])){
				$callAllowed = false; 
			}
		
			}
		}
		
		$this->feUserNr = htmlspecialchars($this->piVars['FEuserNumber']);
		

		$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId ] = '<link rel="stylesheet" type="text/css" href="typo3conf/ext/callbackbutton/pi1/css/class.tx_developergardenCallBackButton_pi1.css" >' ;
		
		if($this->continue){
			
			if($callAllowed){
		
				$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId + 1] = '<script type="text/javascript" src="typo3conf/ext/callbackbutton/pi1/js/jquery-1.6.2.js"></script>';
				$GLOBALS['TSFE']->additionalHeaderData[$this->prefixId + 2] = '<script type="text/javascript" src="typo3conf/ext/callbackbutton/pi1/js/control.js"></script>';
			}else{
				$this->infoText = htmlspecialchars($this->pi_getLL('notAllowed')); 
			}
		
		}else{
			
			$this->infoText = htmlspecialchars($this->pi_getLL('logIn')); 
		}
		$this->fakeId = $this->voiceCallInst->freeCapId;
		
		$content= $this->cObj->substituteMarkerArray($subPart,$this->getMarker());
	
		return $this->pi_wrapInBaseClass($content);
	}
	
	
	/*
	 * function to fetch the data given by the FleyForm (Backend) 
	 * 
	 * @return $ff_data -> array containing Data of FLexForm
	 */
	function initFF(){
		$this->pi_initPIflexform() ;

		//Element
		$ff_user = 'DCLogin' ;
		$ff_password = 'DCPassword' ;
		$ff_maxDur = 'maxDur' ;       //maximaldauer des Gespräcs
		$ff_maxWait = 'maxWait' ;
		$ff_env = 'environment' ;
		$ff_countries = 'countries' ;
		$ff_secure = 'secure' ;        //Sicherheitsmechanismus?
		$ff_intervalle = 'timeInt' ;	 //Zeitintervall des SM	
		$ff_useAmount = 'UseAmount' ; //Anrufe innerhalb des Zeitintervalles
		$ff_captcha = 'captcha' ;
		$ff_number1 = 'BENumber';
		$ff_number2 = 'BENumberAlter';
		$ff_link = 'ContactLink';

		//Reiter
		$configSheet = 'Konfiguration';
		$limitsheet = 'Limits';

		$ff_data = array(

			'secure' => $this->pi_getFFValue($this->cObj->data['pi_flexform'],$ff_secure,$limitsheet),
			'captcha' => $this->pi_getFFValue($this->cObj->data['pi_flexform'],$ff_captcha,$limitsheet) ,
			'duratinBE' => $this->pi_getFFValue($this->cObj->data['pi_flexform'], $ff_dur,$configSheet) ,
			'useAmount' => $this->pi_getFFValue($this->cObj->data['pi_flexform'],$ff_useAmount,$limitsheet) ,
			'user' => $this->pi_getFFValue($this->cObj->data['pi_flexform'], $ff_user, $configSheet) ,
			'password' => $this->pi_getFFValue($this->cObj->data['pi_flexform'], $ff_password, $configSheet) ,
			'number1' => $this->pi_getFFValue($this->cObj->data['pi_flexform'], $ff_number1, $configSheet) ,
			'number2' => $this->pi_getFFValue($this->cObj->data['pi_flexform'], $ff_number2, $configSheet) ,
			'maxDur' => $this->pi_getFFValue($this->cObj->data['pi_flexform'], $ff_maxDur, $configSheet) ,
			'maxWait' => $this->pi_getFFValue($this->cObj->data['pi_flexform'], $ff_maxWait,$configSheet) ,
			'environment' => $this->pi_getFFValue($this->cObj->data['pi_flexform'], $ff_env, $configSheet) ,
			'countries' => $this->pi_getFFValue($this->cObj->data['pi_flexform'], $ff_countries, $configSheet) ,
			'period' => $this->pi_getFFValue($this->cObj->data['pi_flexform'], "timeInt",$limitsheet) ,
			'durationBE' => $this->pi_getFFValue($this->cObj->data['pi_flexform'], $ff_maxDur, $configSheet) ,
			'link' => $this->pi_getFFValue($this->cObj->data['pi_flexform'], $ff_link, $configSheet),

		);
		
		return $ff_data;
		
	
		
	}
	
	/*
	 * Set's the SessionData with the data in $array
	 * 
	 * @param $array -> data to be stored in the session 'callbackbutton'
	 */
	function setSessionData($array){
		
		$GLOBALS['TSFE']->fe_user->setKey('ses', 'callbackbutton', $array);
		$GLOBALS['TSFE']->fe_user->storeSessionData();
	}
	
	/*
	 * Creates MARKER-array, containing every Marker needed for the template default.xhtml
	 * 
	 * @return $retArray -> MarkerArry containing data to be displayed
	 */
	function getMarker(){
		
		$markerRet = array(
			'###MARKERPREFIXID###' => $this->prefixId,
			'###MARKER_PAGELINK###' => $this->pi_getPageLink($GLOBALS['TSFE']->id),
			'###MARKER_FE_NR###' => htmlspecialchars($this->piVars['FEuserNumber']) ,
			'###MARKER_INFO###' => $this->infoText,
			'###MARKER_DIV_LEFT###' => $this->divLeft,
			'###MARKER_BUTTON_START###' => htmlspecialchars($this->pi_getLL('buttonStart')),
			'###MARKER_BUTTON_STOP###' => htmlspecialchars($this->pi_getLL('buttonStop')),
			
			'###MARKER_CAPTCHA###'=>htmlspecialchars($this->pi_getLL('form_captcha')) ,
			'###MARKER_CAPTCHA_FREECAPNOTICE###' => $this->freeCapMarkerArray['###SR_FREECAP_NOTICE###'] ,
			'###MARKER_CAPTCHA_IMAGE###'=> $this->freeCapMarkerArray['###SR_FREECAP_IMAGE###'] ,
			'###MARKER_CAPTCHA_CANTREAD###' => $this->freeCapMarkerArray['###SR_FREECAP_CANT_READ###'] ,
			'###MARKER_CAPTCHA_INSERT###' => $this->freeCapMarkerArray['###CAPTCHA_INSERT###'] ,
			'###MARKER_CAPTCHA_ACCESS###' =>  $this->freeCapMarkerArray['###SR_FREECAP_ACCESSIBLE###'] ,
			
			'###MARKER_VALUE_HIDDEN###' => $this->hidden,
			'###MARKER_VALUE_FAKEID###' => $this->fakeId,
			
			'###MARKER_CONNTIME###' => htmlspecialchars($this->pi_getLL('label_connTime')),
			'###MARKER_CONNSTAT###' => htmlspecialchars($this->pi_getLL('label_connStat')),
			'###MARKER_HEADER###' => htmlspecialchars($this->pi_getLL('label_header')),
			'###MARKER_HEADERTAG###' => htmlspecialchars($this->pi_getLL('label_headerTag')),
			'###MARKER_HEADERTAGCLOSING###' => htmlspecialchars($this->pi_getLL('label_headerTag_closing')),
		);
		
		return $markerRet; 
	}
	
	function getError($error){
			switch ($error){ 

				case "0004":
					return htmlspecialchars($this->pi_getLL('failure_format1')) ;
					break;
				case "0007":
					return htmlspecialchars($this->pi_getLL('failure_format2')) ;  
					break;

				case "0005":
					$HTMLCreator = t3lib_div::makeInstance('createHTMLTags');
					$link = $this->flexFormData['link'];
					$val = htmlspecialchars($this->pi_getLL('failure_number1'));
					$val .= ' '.htmlspecialchars($this->pi_getLL('failure_plus'));
					$val .= ' ' . $HTMLCreator->getLink('here', $link). ')';
					return $val;
					break;

				case "0008":
					$link = $this->flexFormData['link'];
					$HTMLCreator = t3lib_div::makeInstance('createHTMLTags');
					$val = htmlspecialchars($this->pi_getLL('failure_number2'));
					$val .= ' '.htmlspecialchars($this->pi_getLL('failure_plus'));
					$val .= ' ' . $HTMLCreator->getLink('here', $link). ')';
					return $val;
					break;

				case "0006":
					return  htmlspecialchars($this->pi_getLL('failure_blocked1')) ;
					break;

				case "0009":
					return  htmlspecialchars($this->pi_getLL('failure_blocked2')) ;
					break;
					//da selbst kreirte Fehler manuelles stopen der Verbindung																		
				case "0066" :
					$link = $this->flexFormData['link'];
					$HTMLCreator = t3lib_div::makeInstance('createHTMLTags');
					$val = htmlspecialchars($this->pi_getLL('failure_countrie')) ;
					$val .= ' '.htmlspecialchars($this->pi_getLL('failure_plus'));
					$val .= ' ' . $HTMLCreator->getLink('here', $link). ')';
					return $val;
					break;

				case "0069" :
					return htmlspecialchars($this->pi_getLL('failure_captcha')) ;
					break;

				case "0071" :
					$this->voiceCall->stopVoiceCall() ;
					if($ff_data['period'] == 0){
					return htmlspecialchars($this->pi_getLL('failure_limit_hour')) ;
					}else{
					return htmlspecialcharsstopVoiceCall($this->pi_getLL('failure_limit_day')) ;
					}
					break ;
				case '0099': 
					return htmlspecialchars($this->pi_getLL('failure_connectionEnd'));
					break;
				case 'anumber':
					return htmlspecialchars($this->pi_getLL('failure_aNumber'));
					break;
				
				default: 
					return htmlspecialchars($this->pi_getLL('failure_default'));
					break ;
				}
		}
		
	
	/*
	* User anlegen
	*
	*/
		function createUser($fe_user, $period_start , $period_end, $table) {
			print $table;
			$fields = array(

				'fe_user_id' => $fe_user,
				'vc_per_period'=>0 ,
				'period_start'=>$period_start ,
				'period_end'=>$period_end ,
			) ;
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(

				$table ,
				$fields
			) ;

		}	
		
		/* Gibt Datensatz des Users zurück */

		function getDBStuff($fe_user , $table) {

			$value = null ;

			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*' ,
				$table ,
				'fe_user_id = ' . $fe_user
			);	
			if($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {

				$value = $row ;

			}
			//gibt speicher frei 
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			return $value;
		}
		
		
	
	 
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/callbackbutton/pi1/class.tx_callback_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/callbackbutton/pi1/class.tx_callback_pi1.php']);
}

?>