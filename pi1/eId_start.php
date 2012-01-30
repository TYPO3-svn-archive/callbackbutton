<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


require_once (PATH_tslib.'class.tslib_pibase.php');
require_once (t3lib_extMgm::extPath('callbackbutton','pi1/voiceCallClass.php'));
require_once (t3lib_extMgm::extPath('callbackbutton','pi1/class.tx_callbackbutton_pi1.php'));

class eId_start {
	var $stopNow = false;
	
	function start(){
		$FE_sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'callbackbutton');
		$voiceCallInst = t3lib_div::makeInstance('voiceCallClass');
		$FE_sessionData['feUserNr'] = $_GET['plus'];

		
		$voiceCallInst->environment = $FE_sessionData['environment']; 
		$voiceCallInst->user = $FE_sessionData['user'];
		$voiceCallInst->password = $FE_sessionData['password'];
		$voiceCallInst->phoneNr1 = $FE_sessionData['feUserNr'];
		$voiceCallInst->phoneNr2 = $FE_sessionData['number1'];
		$voiceCallInst->phoneNr3 = $FE_sessionData['number2'];
		
		$countriprefix = $voiceCallInst->getCountryArray($FE_sessionData['countries']);
		
		
		if(!($voiceCallInst->checkNr($voiceCallInst->phoneNr1,$countriprefix)) || 
			!($voiceCallInst->checkNr($voiceCallInst->phoneNr2,$countriprefix) )){
			
			$FE_sessionData['error'] = '0066';
			
			
			}else{
				//sessionData of sr_freeCap Extension to get the hashcode of displayed word
				$freeCapSession = $GLOBALS['TSFE']->fe_user->getKey('ses','tx_sr_freecap');
				if(!($FE_sessionData['captchaCheck']) || 
					$freeCapSession['sr_freecap_word_hash'] ==  md5(strtolower(utf8_decode($_GET['ct'])))){
					
					$voiceCallInst->startVoiceCall();
					$FE_sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'callbackbutton');
					$FE_sessionData['newCallSessionID'] = $voiceCallInst->newCallSessionID;
					$FE_sessionData['client'] = serialize($voiceCallInst->client);
					//print $FE_sessionData['fe_user'];
					$voiceCallInst->setVC($FE_sessionData['fe_user'], 'tx_callbackbutton_secu');
					

					//unserialize($FE_sessionData['freeCap'])->makeCaptcha(); 
					} else {
						
						$FE_sessionData['error'] = '0069';
					}

				

				}
				$GLOBALS['TSFE']->fe_user->setKey('ses', 'callbackbutton', $FE_sessionData);
				$GLOBALS['TSFE']->fe_user->storeSessionData();
	}
	
	function calcData(){
		
		$FE_sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'callbackbutton');
		
		if(isset($FE_sessionData['newCallSessionID'])){
			
			$connTime = unserialize($FE_sessionData['client'])->callStatus($FE_sessionData['newCallSessionID'])->getConnectionTimeB();

			$sec = $connTime % 60; 
			if($sec < '10'){
				$sec = '0' . $sec;
			}

			$min = floor($connTime/60);
			if($min < '10'){
				$min = '0' . $min;
			}
			$connTime = $min . ":" . $sec;
			$FE_sessionData['min'] = $min;
			$FE_sessionData['sec'] = $sec;
			$stat = unserialize($FE_sessionData['client'])->callStatus($FE_sessionData['newCallSessionID'])->getStateB();
			
			$GLOBALS['TSFE']->fe_user->setKey('ses', 'callbackbutton', $FE_sessionData);
			$GLOBALS['TSFE']->fe_user->storeSessionData();
			if($stat == 'DISCONNECTED'){
				$voiceCallInst = t3lib_div::makeInstance('voiceCallClass');
				$voiceCallInst->FE_sessionData = $FE_sessionData;
				$voiceCallInst->setStatistic();
				$this->stopNow = true; 
			}
			$stat = $FE_sessionData['stats'][$stat];
			$ret = array(
				'stat' =>$stat, 
				'conntime' => $connTime,
				'stop' => $this->stopNow
				);

		}else{
			$ret = $fehler; 
		}
		
		return $ret;
		
	}
	
	function stop(){
		
		$FE_sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'callbackbutton');
		$voiceCallInst = t3lib_div::makeInstance('voiceCallClass');
		$voiceCallInst->FE_sessionData = $FE_sessionData;
		
		$voiceCallInst->stopVoiceCall();
		$voiceCallInst->setStatistic();
		
		echo('Vielen Dank für ihr Interesse');
	}
	
	
	
	function checkCaptcha(){
		
		$FE_sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'callbackbutton');	
		
		if (!(unserialize($FE_sessionData['freeCap'])->checkWord($_GET['ct']))) {

				return false;
			}
			else{
				return true; 
			}
		}
		
}

	$GLOBALS['TSFE']->fe_user = tslib_eidtools::initFeUser();
	$obj = t3lib_div::makeInstance('eId_start');
			

	switch($_GET['fkt']){

		case 'start':	

			$obj->start();
			break;

		case 'run':

			$data = $obj->calcData();
			$data_json = json_encode($data);
			echo $data_json;
			break;
		case 'stop':
			$obj->stop();
			break;

	}
?>  
