<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

/**
 * Description of DbFormatter
 DB FUNCTIONS---------------------------------------------------------------

/*
*Funktionen für Datenbankabfragen mit Typo3-Globals

*	
*		CreateUser,
*		SelectVCperPeriod
*		Update 
*		und vergleichsfunktion der Periode
*		
*@param $fe_user eingelogter User
*@param $period_start legt Neuen Startpunkt des Zeitintervalls fest
*@param $period_end   legt Neuen Endpunkt des Zeitintervalls fest
*@param $table Zieldatenbanktabelle
*@param $vc_per_period VoiceCalls innerhalb einer Zeiteinheit

 * @author Landkommunenhippie
 */
class DbFormatter {
	var $newPeriodStart;
	var $newPeriodEnd;
	var $period;
	var $useAmount;
	var $vcNr;
	var $params;
	
	
	function init_secu($fe_user, $table, $callsLeftText = null){
		
		$retArray = array(
			'checkSecu' => $this->checkSecu($fe_user, $table),
			'data' => $this->getDBStuff($fe_user, $table),
			'callsLeft' => $this->getCallsLeft($fe_user, $table, $callsLeftText),
			
		);
		return $retArray ;
		
	}
	
	/*
	* User anlegen
	*
	*/
		function createUser($fe_user, $period_start , $period_end, $table) {
					
			$GLOBALS['TSFE']->fe_user = tslib_eidtools::initFeUser();		
			//$this->pi_setPiVarDefaults();
			//$this->pi_loadLL();
			$fields = array(

				'fe_user_id' => $fe_user,
				'vc_per_period'=>0 ,
				'period_start'=>$period_start ,
				'period_end'=>$period_end ,
			) ;
			$GLOBALS['TYPO3_DB']->exec_INSERTquery(

				$table ,
				$fields
			);

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
		
		/*
		* Datenbankupdate
		*/
		function updateDB($fe_user , $vc_per_period , $period_start , $period_end , $table) {

		$field = array(
			'vc_per_period' =>$vc_per_period ,
			'period_start' => $period_start ,
			'period_end' => $period_end ,
		) ;

		$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			$table ,
			'fe_user_id = ' . $fe_user ,
			$field
		) ;

		}
		/*
		* kreiert neuen eintrag ind Statistikdatenbank für BE-Modul 
		* 
		*@param $date Datum und UHrzeit
		*/
		function createNewStatistic($date, $dur) {
		
			$field = array(
				'vc_hour'=>$date['hours'] ,
				'vc_day'=>$date['mday'] ,
				'vc_month'=>$date['mon'] ,
				'vc_year'=>$date['year'] ,
				'vc_dur'=>$dur
			) ;

			$GLOBALS['TYPO3_DB']->exec_INSERTquery(
				'tx_callbackbutton_statistics' ,
				$field
			);
			
		}
		
		/*
	* Get's Database Data of tx_voicecall_secu and checks
	* wether further Voicecalls are allowed to do, only if activated 
	*/
		function checkSecu($fe_user, $table){
			
			$date = new DateTime();
			$date = getDate();
			$this->newPeriodStart = new DateTime();
			$this->newPeriodEnd = new DateTime();
			//im Backendübergeben
			switch($this->period){
				////Stundenvergleich
				case '0':
					$this->newPeriodStart->setTime($date['hours'], 0, 0);
					$this->newPeriodEnd->setTime($date['hours'], 59, 59);
					break;
				//Tagesvergleich	
				case '1':
					$this->newPeriodStart->setTime(0, 0, 0);
					$this->newPeriodEnd->setTime(23, 59, 59);
					break;
				}
			$DB_Stuff = $this->getDBStuff($fe_user, $table);
			////Falls neuer User wird Datenbankeintrag angelegt
			if(is_null($DB_Stuff)) {
				
				$this->createUser($fe_user,$this->newPeriodStart->format(DateTime::ISO8601),
				$this->newPeriodEnd->format(DateTime::ISO8601),$table);
				$periodStart = clone $this->newPeriodStart;
				$periodEnd = clone $this->newPeriodEnd;
				$ret = true;	

			} else {
			
				$periodStart = new DateTime($DB_Stuff['period_start']);
				$periodEnd = new DateTime($DB_Stuff['period_end']);
				$this->vcNr = $DB_Stuff['vc_per_period'];

			}
			////Test ob noch erlaubt
			if($periodEnd->getTimestamp() <= $this->newPeriodStart->getTimestamp()) {

				////Neue Periode';
				$this->vcNr = 0;
				$periodStart = clone $this->newPeriodStart ;
				$periodEnd = clone $this->newPeriodEnd;
				$ret = true;

			}
			if ($periodStart->getTimestamp() >= $this->newPeriodStart->getTimestamp() &&
				$periodEnd->getTimestamp() <= $this->newPeriodEnd->getTimestamp()) {
				////Alte Periode falsch';
				$ret =  true;
				if ($this->vcNr >= $this->useAmount) {
						//$this->FE_SessionData['call']['error'] = "0071";
					$ret =  false; 
				}
				
			} else {
				if ($periodStart->getTimestamp() <= $this->newPeriodStart->getTimestamp() &&
				$periodEnd->getTimestamp() >= $this->newPeriodEnd->getTimestamp()) {
				//"alte zwei");
				$this->vcNr = 0;
				$ret =  true;
				}
			}

			$this->updateDB($fe_user, $this->vcNr, $this->newPeriodStart->format(DateTime::ISO8601), $this->newPeriodEnd->format(DateTime::ISO8601), $table);			
			return $ret;
		}
		
		/*
	* Creates DB-Entry that stores the length of the element
	* necessary for BE-Module
	*/
		function setCallLength($min, $sec){

			//Aufrunden der Minuten, da auch minütliche Abrechnung! 
		
			if($sec == 0){

				$a = $min;


			}else{

				$a = $min + 1;

			}
			return $a;
		}
		
		/*
	* Get's the Nr of calls the user is still allowed to init
	* 
	* @return leftDiv html-Div containing the calls left
	*/
		function getCallsLeft($fe_user_id, $table, $text = null){
			
			$DB_Stuff = $this->getDBStuff($fe_user_id, $table );
			$vc_left = $this->useAmount - $DB_Stuff['vc_per_period'];
			$leftDiv = '<div id="left"> ' . $text . $vc_left . ' </div>';
			return $leftDiv;
		}
		
		

	
}

?>
