<?php
 session_start();
					require_once(t3lib_extMgm::extPath('callbackbutton','pi1/src/voicecall/client/VoiceCallClient.php'));
					require_once (t3lib_extMgm::extPath('callbackbutton','pi1/src/voicecall/data/VoiceCallStatusConstants.php'));
					require_once (t3lib_extMgm::extPath('callbackbutton','pi1/DbFormatter.php'));
					

                
    class voiceCallClass {
		var $newCallSessionID;
		var $user;
		var $password;
		var $maxDur = null;
		var $maxWait = null;
		var $environment;
		var $client;  
		var $phoneNr1;
		var $phoneNr2;
		var $phoneNr3;
		var $newCallresponse;
		var $tearDownResponse;
		var $stateA;
		var $stateB;
		var $connTime;
		var $nr_allowed;
		var $FE_sessionData;
		var $freeCap;
		var $freeCapId;
		var $increment; 
		
				
			/*
			 * Function to initiate the Voice Call by the Developer Garden Voice Call API 
			 * Using global parameter set by the mainfunction of class.tx_callbackbutton_pi1.php
			 */
               function startVoiceCall() {
                //Erzeugung des VoiceCallClient's
				 $this->increment = 0;
				 try { 
					
					 $this->client = new VoiceCallClient($this->environment,$this->user,$this->password);
				
					 //prüft ob dritte Nummer angegeben ist.
					if($this->phoneNr3!= ""){
						//Kombinierung der Nummern 2 & 3 zur richtigen Angabe in der Parameterübergabe (nummer2,nummer3)
						$this->phoneNr2 .=",".$this->phoneNr3;


					  } 
					  else {
						$this->phoneNr2 = $this->phoneNr2;
						}

					//Rufaufabau geswitcht durch privacy parameter
					//maxDur und maxWait im backend überggeben
					
					$this->newCallResponse = $this->client->newCall($this->phoneNr1, $this->phoneNr2, 
					null, $this->maxDur,$this->maxWait,null,"devGarden",null);


					$this->newCallSessionID = $this->newCallResponse->getSessionID();
					
					if(!($this->newCallResponse->getStatus()->getStatusConstant() == VoiceCallStatusConstants::SUCCESS)) {

					  $errorMessage = $this->newCallResponse->getStatus()->getStatusCode();
					 
					  throw new Exception($errorMessage);
					  } else{
						 $this->increment = 1; 
						  
					  }

				  }

				  catch(Exception $e){
					  /*
					   * Fehlerbehandlung
					   * Fehlercode wird zur weiterverarbeitung in
					   * getError() in der Session gespeichert
					   */    
					  
					  $GLOBALS['TSFE']->fe_user = tslib_eidtools::initFeUser();
					  $this->FE_sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', 'callbackbutton');
					  $this->FE_sessionData['error'] = $e->getMessage();
					  $GLOBALS['TSFE']->fe_user->setKey('ses', 'callbackbutton', $this->FE_sessionData);
					  $GLOBALS['TSFE']->fe_user->storeSessionData();
						
				  }
				  
			}
            
           
         
		   /*
		   * Funktion zum Stoppen des VoiceCalls
		   *
		   *@save head for getDiscon
		   */
		  function stopVoiceCall() {
		  
			if(isset($this->FE_sessionData['newCallSessionID'])||$this->FE_sessionData['newCallSessionID']!=0) {              
			try {            

				//TearDownaufruf -> Verbindungstrennung
			  
				$this->tearDownResponse = unserialize($this->FE_sessionData['client'])->tearDown($this->FE_sessionData['newCallSessionID']);

				//Exceptionangabe falls TearDown nicht erfolgreich ist
				if(!($this->tearDownResponse->getStatus()->getStatusConstant() == VoiceCallStatusConstants::SUCCESS)) {
				  $errorMessage = "Verbindung sollte bereits beendet sein.";
				  throw new Exception($errorMessage);
					  } else 
						{
						 //Ausgabe falls beendet
					

						}
				  }
				 //Exeptionbehandlung
				 catch(Exception $e) {
					$this->FE_sessionData['error'] = '0099';
					$GLOBALS['TSFE']->fe_user = tslib_eidtools::initFeUser();
					$GLOBALS['TSFE']->fe_user->setKey('ses', 'callbackbutton', $this->FE_sessionData);
					$GLOBALS['TSFE']->fe_user->storeSessionData();
				 }
			 }	

         }
		 
	/*
	* Creates an Array with the FFValue, containing the numberprefixes
	* which are allowed to call to
	* 
	* @param countriesAllowed Array made from String of FlexForm
	* @param pair every single value in $countriesAllowed
	*/
		function getCountryArray($data){

			//splittet Übergebenen String anhand des ',' und erzeugt ein Array
			//welches beide Vorwahlformate aller im BE übergebener Länder beinhaltet (Format: +xx_00xx)
			//diese Elemente werden weiterhin anhand des '_' dem array $nr_allowed hinzugefügt
			$countriesAllowed = explode(",",$data);
			foreach($countriesAllowed as $nr) {
				$pair = explode("_", $nr);
				$ret[] = $pair[0];
				$ret[] = $pair[1];	
				unset($pair);						
			}
			return $ret; 
		}
         
         
			   /*
				*Methode zum Feststellen, ob die Nummer angerufen werden darf ( erlaubte Vorwahlen werden im Backend festgelegt)
				*
				*@param $nums über Formular übergebene Rufnummern
				*@param $ref_nums Ländervorwahlen im BE übergeben
				*/
			   function checkNr($nums, $ref_nums) {
					$check_num7 = $nums[0];
					$check_num7 .= $nums[1];
					$check_num7 .= $nums[2];
					$check_num7 .= $nums[3];
					$check_num7 .= $nums[4];
					$check_num7 .= $nums[5];
					$check_num7 .= $nums[6];
				   
					$check_num6 = $nums[0];
					$check_num6 .= $nums[1];
					$check_num6 .= $nums[2];
					$check_num6 .= $nums[3];
					$check_num6 .= $nums[4];
					$check_num6 .= $nums[5];


					$check_num5 = $nums[0];
					$check_num5 .= $nums[1];
					$check_num5 .= $nums[2];
					$check_num5 .= $nums[3];
					$check_num5 .= $nums[4];

					$check_num4 = $nums[0];
					$check_num4 .= $nums[1];
					$check_num4 .= $nums[2];
					$check_num4 .= $nums[3];

					$check_num3 = $nums[0];
					$check_num3 .= $nums[1];
					$check_num3 .= $nums[2];

					$check_num2 = $nums[0];
					$check_num2 .= $nums[1];

					$check_num1 = $nums[0];



					foreach($ref_nums as $value){

					 if($value == $check_num4 || $value == $check_num3|| $value == $check_num5 ||
						 $value = $checkNum6|| $value==$checkNum7 || ($check_num2 != "00" && $check_num1 != "+")) {

							return true;
							break;
						} else {
					  } 
					}
					return false;
			   }
		
	    /*
		 * Function to create a new captcha. on one side it creates the marker Array, used
		 * by the sr_freecap extension to create the securitycode.
		 * On the other it get's the fakeId of the captcha Image. Therefore The tx_srfreecap_pi2.php
		 * was a bit modified. With it an javascriptfunction can be called, that refresh's the captcha. 
		 * For mre information please check the readme || Documentation
		 * 
		 * @return $retArray -> MArkerArray needed by sr_freecap extension to create captcha
		 */
		function createCaptcha(){
			
			//prüft ob die sr_freecap extension (captcha) geladen ist, falls ja Wird eine Instanz erzeugt
			if (t3lib_extMgm::isLoaded('sr_freecap')) {
			
				require_once(t3lib_extMgm::extPath('sr_freecap').'pi2/class.tx_srfreecap_pi2.php');
				$this->freeCap = t3lib_div::makeInstance('tx_srfreecap_pi2');
				
				$retArray = $this->freeCap->makeCaptcha();
				$this->freeCapId = $this->freeCap->getFakeID(); 
			
				return $retArray;
				
			}
		}
		
		function setVC($fe_user, $table){
			$dbFormatter = t3lib_div::makeInstance('DbFormatter');
			$dbContent = $dbFormatter->getDBStuff($fe_user, $table);
			//var_dump($dbContent);
			$vcLeft = $dbContent['vc_per_period'] + $this->increment;
			
			$dbFormatter->updateDB($fe_user , $vcLeft , $dbContent['period_start'] , 
				$dbContent['period_end'] , $table);
		}
		
		function setStatistic(){
			$dbFormatter = t3lib_div::makeInstance('DbFormatter');
			$dur = $dbFormatter->setCallLength($this->FE_sessionData['min'],
				$this->FE_sessionData['sec']);
			
			$dateNow =  new DateTime();
			$dateNow = getDate();
			if(!($this->FE_sessionData['stopStat'] )){
			$dbFormatter->createNewStatistic($dateNow, $dur);
			
			}
			$this->FE_sessionData['stopStat'] = true;
			$GLOBALS['TSFE']->fe_user->setKey('ses','callbackbutton' , $this->FE_sessionData);
			$GLOBALS['TSFE']->fe_user->storeSessionData();
		}
		
				
	}
?>