<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Tobias <thahn@telekom.de>
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


$LANG->includeLLFile('EXT:callbackbutton/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]


require_once('tx_callbackbutton_dia.php');
/**
 * Module 'Call Back' for the 'dgcallbackbutton' extension.
 *
 * @author	Tobias <thahn@telekom.de>
 * @package	TYPO3
 * @subpackage	tx_dgcallbackbutton
 */
class  tx_callbackbutton_module1 extends t3lib_SCbase {
				var $pageinfo;

				/**
				 * Initializes the Module
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					parent::init();

					/*
					if (t3lib_div::_GP('clear_all_cache'))	{
						$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
					}
					*/
				}

				/**
				 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
				 *
				 * @return	void
				 */
				 
				 
				 function selectedMonth($s) {
						global $LANG;
						$retValue='';
						for ($i=1; $i<13; $i++) {
							if ($i != $s) {
								$retValue.='<option value="'.$i.'">'.$LANG->getLL('m'.$i).'</option>';
							} else {
								$retValue.='<option selected="selected" value="'.$i.'">'.$LANG->getLL('m'.$i).'</option>';
							}
						}
						return $retValue;
					}
				 
				 
				 
				 
				function menuConfig()	{
					global $LANG;
					$this->MOD_MENU = Array (
						'function' => Array (
							'1' => $LANG->getLL('function1'),
							'2' => $LANG->getLL('function2'),
							'3' => $LANG->getLL('function3'),
						)
					);
					parent::menuConfig();
				}

				/**
				 * Main function of the module. Write the content to $this->content
				 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
				 *rewritten by Alexander Kraskov
				 * @return	[type]		...
				 */
				function main()    {
        global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

        // Access check!
        // The page will show only if there is a valid page and if this page may be viewed by the user
        $this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
        $access = is_array($this->pageinfo) ? 1 : 0;

        if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))    {

            // Draw the header.
            $this->doc = t3lib_div::makeInstance('template');
            $this->doc->backPath = $BACK_PATH;
            $this->doc->form = '<div class="typo3-fullDoc">' .
            '<div id="typo3-docheader">' .
                '<div id="typo3-docheader-row1">' .
                    '<div class="buttonsleft"></div>' .
                    '<div class="buttonsright">';
                            // ShortCut
                            if ($BE_USER->mayMakeShortcut()) {
                                $this->doc->form .= $this->doc->makeShortcutIcon('id', implode(',', array_keys($this->MOD_MENU)), $this->MCONF['name']);
                            }
$this->doc->form .= '</div>' .
                '</div>' .
                '<div id="typo3-docheader-row2">' .
                    '<div class="docheader-row2-left">' .
                        '<div class="docheader-funcmenu">';
                            // Control
                            $this->doc->form .= '<form action="" method="post" enctype="multipart/form-data">';
                            // JavaScript
                            $this->doc->JScode = '
                                <script language="javascript" type="text/javascript">
                                    script_ended = 0;
                                    function jumpToUrl(URL)    {
                                        document.location = URL;
                                    }
                                </script>
                                ';
                            $this->doc->postCode='
                                <script language="javascript" type="text/javascript">
                                    script_ended = 1;
                                    if (top.fsMod) top.fsMod.recentIds["web"] = 0;
                                </script>
                                ';
                            $this->doc->form .= $this->doc->funcMenu('', t3lib_BEfunc::getFuncMenu($this->id,'SET[function]', $this->MOD_SETTINGS['function'], $this->MOD_MENU['function']));
                            $this->doc->form .= '</form>' .
                        '</div>' .
                    '</div>' .
                    '<div class="docheader-row2-right"></div>' .
                '</div>' .
            '</div>';
            
            $this->content .= $this->doc->startPage($LANG->getLL('title'));
            $this->content .= '<div id="typo3-docbody"><div id="typo3-inner-docbody">';
            $this->content .= $this->doc->header($LANG->getLL('title'));
            $this->content .= $this->doc->spacer(5);
            $this->content .= $LANG->getLL('important_links') . ' ';
            $this->content .= '<a href="http://www.developergarden.com" style="color:green;" target="_blank">Developer Garden</a> & ';
            $this->content .= '<a href="http://www.developercenter.telekom.com" style="color:#E20074;" target="_blank">Developer Center</a><br />';
            $this->content .= $this->doc->spacer(5);
            $this->content .= $this->doc->divider(5);
            
            // Render content:
            $this->moduleContent();
            
            $this->content .= '</div></div></div>';
            
        } else {
            // If no access or if ID == zero
            // Draw the header.
            $this->doc = t3lib_div::makeInstance('template');
            $this->doc->backPath = $BACK_PATH;
            $this->doc->form = '<div class="typo3-fullDoc">' .
                '<div id="typo3-docheader">' .
                    '<div id="typo3-docheader-row1">' .
                        '<div class="buttonsleft"></div>' .
                        '<div class="buttonsright"></div>' .
                    '</div>' .
                    '<div id="typo3-docheader-row2">' .
                        '<div class="docheader-row2-left"></div>' .
                        '<div class="docheader-row2-right"></div>' .
                    '</div>' .
                '</div>';
            $this->content .= $this->doc->startPage($LANG->getLL('title'));
            $this->content .= '<div id="typo3-docbody"><div id="typo3-inner-docbody">';
            $this->content .= $this->doc->header($LANG->getLL('title'));
            $this->content .= $this->doc->spacer(5);
            $this->content .= $LANG->getLL('important_links') . ' ';
            $this->content .= '<a href="http://www.developergarden.com" style="color:green;" target="_blank">Developer Garden</a> & ';
            $this->content .= '<a href="http://www.developercenter.telekom.com" style="color:#E20074;" target="_blank">Developer Center</a><br />';
            $this->content .= $this->doc->spacer(5);
            $this->content .= $this->doc->divider(5);
            
            $this->content .= '<span class="t3-icon t3-icon-status t3-icon-status-status t3-icon-status-permission-denied">&nbsp;</span><span style="vertical-align:bottom;">' . $LANG->getLL('access_denied') . '</span>';
            
            $this->content .= '</div></div></div>';
        }
    }

				/**
				 * Prints out the module HTML
				 *
				 * @return	void
				 */
				function printContent()	{

					$this->content.=$this->doc->endPage();
					echo $this->content;
				}
				//$time = hour||day||month||year
				//$time_exact == monday, 13, 2011 etc
				function getAllVC(){
					$vc_per_time = 0;
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						'tx_callbackbutton_statistics',
						''
					);
					
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
								$vc_per_time++;
							}
				return $vc_per_time;
				}

				/**
				 * Generates the module content
				 *
				 * @return	void
				 */
				function moduleContent()	{
					Global $LANG;
					
					$vc_general = 0;
						$fe_user_general = 0;
						
							$DB_Stuff = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
								'*',
								'tx_callbackbutton_statistics',
								''
							);
							while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($DB_Stuff)){
								$vc_general++;
							}
							 $GLOBALS['TYPO3_DB']->sql_free_result($DB_Stuff);
							 
							
							$DB_Stuff = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
								'*',
								'tx_callbackbutton_secu',
								''
							 );
							 while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($DB_Stuff)){
								$fe_user_general++;
							}
						$vc_all = $this->getAllVC();
						
						
					
					
					switch((string)$this->MOD_SETTINGS['function'])	{
						
						
						case 1:
						
							 $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
									'COUNT(vc_nr), vc_hour',
									'tx_callbackbutton_statistics',
									'',
									'vc_hour',
									'vc_hour'
								);
								$rows = array();
								$max = 0;
								$arr  = array();
								while(($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
									$rows[] = $row;
									$max += $row['COUNT(vc_nr)'];
								}
								$GLOBALS['TYPO3_DB']->sql_free_result($res);
								foreach ($rows as $row)    {
									$arr[$row['vc_hour']] = round($row['COUNT(vc_nr)']*100/$max);
								}
								
								$diagramm = t3lib_div::makeInstance('tx_callbackbutton_dia');
								$diagramm->value_text='';

								//$registry = t3lib_div::makeInstance('t3lib_Registry');
								//$count = $registry->get('tx_'.$this->extKey, 'sms', 0);
								
										
						
						
						
						
							$content='<div style="position:relative; left:15%;"><strong>User Statistics</strong></div><br />
								VoiceCalls in general: '.$vc_general.'<br/>
								Users using Call Back Widget:'.$fe_user_general.'<br/>
								
								<br />
								
								Nr of all Vociecalls: '.$vc_all.'</br>'.$diagramm->draw($arr);
								
								
							
							$this->content.=$this->doc->section('Message #1:',$content,0,1);
						break;
						case 2:
						
							$month = date('m');
									if ($_POST['month']) {
										$month = $_POST['month'];
									}
									$year = date('Y');
									if ($_POST['year'])    {
										if (is_numeric($_POST['year'])) {
											$year = $_POST['year'];
										}
									}

									$var_date = mktime(0, 0, 0, $month, 1, $year);
									$days = date('t', $var_date);

									// tx_dgcallbackbutton_statistics
									$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
										'COUNT(vc_nr), vc_day',
										'tx_callbackbutton_statistics',
										'vc_month='.$month.' and vc_year='.$year,
										'vc_day',
										'vc_day'
									);
									$rows = array();
									$max = 0;
									while(($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
										$rows[] = $row;
										if ($max < $row['COUNT(vc_nr)']) {
											$max = $row['COUNT(vc_nr)'];
										}
									}
									$GLOBALS['TYPO3_DB']->sql_free_result($res);

									$cy=11;
									$ly=20;
									$my=10;

									if ($max <= 100) {
										$my=10;
									} else {
										$len = strlen((string) $max);
										$my=floor((floor($max/pow(10, $len-1))+1)*pow(10, $len-2));
									}

									$arr  = array();
									foreach ($rows as $row)    {
										$arr[$row['vc_day']-1] = $row['COUNT(vc_nr)'];
									}

									$diagramm = t3lib_div::makeInstance('tx_callbackbutton_dia');
									$diagramm->x0 = 30;
									$diagramm->cx = $days + 1;
									$diagramm->lx = 14;
									$diagramm->kx = -7;
									$diagramm->kv = -2;
									$diagramm->my = $my;
									$diagramm->cy = $cy;
									$diagramm->ly = $ly;
									$diagramm->value_text = '';
									$diagramm->axis_y_text = 'voicecalls';
									
									
									$vc_all = $this->getAllVC();

							$content='<div style="position:relative; left:15%;"><strong>User Statistics</strong></div><br />
								VoiceCalls in general: '.$vc_general.'<br/>
								Users using Call Back Widget:'.$fe_user_general.'<br />
								Nr of all Vociecalls:'.$vc_all.'<br />
								'.$diagramm->draw($arr).'
									<br />
									<div style="postition:relative; ">
									<form>
									<select name=month>'.$this->selectedMonth($month).'</select>
									<input type="text" name="year" maxlength=4 size=4 value="'.$year.'" />
									<input type="submit" value="'.$LANG->getLL('function2_button').'"/>
									</form>
									</div>
									';
							$this->content.=$this->doc->section('Message #2:',$content,0,1);
						break;
						case 3:
							 $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
									'COUNT(vc_dur), vc_dur',
									'tx_callbackbutton_statistics',
									'',
									'vc_dur',
									'vc_dur'
								);
								$rows = array();
								$max = 0;
								$arr  = array();
								while(($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))) {
									$rows[] = $row;
									$max += $row['COUNT(vc_dur)'];
								}
								$GLOBALS['TYPO3_DB']->sql_free_result($res);
								foreach ($rows as $row)    {
									$arr[$row['vc_dur']] = round($row['COUNT(vc_dur)']*100/$max);
								}
								
								
								
								$diagramm = t3lib_div::makeInstance('tx_callbackbutton_dia');
								$diagramm->value_text='';
								
									$cy=11;
									$ly=20;
									$my=10;
									$diagramm->x0 =45;
									$diagramm->cx = 60;
									
									$diagramm->lx = 18.5;
									$diagramm->kx = 1;
									$diagramm->kv = 5;
									$diagramm->my = $my;
									$diagramm->cy = $cy;
									$diagramm->ly = $ly;
									$diagramm->value_text = '';
									$diagramm->axis_y_text = ' %';
									$diagramm->width=1145;
																		
									
									$vc_all = $this->getAllVC();
								
										
						
						
						
						
							$content='<div align="center"><strong>User Statistics</strong></div><br />
								VoiceCalls in general: '.$vc_general.'<br/>
								Users using Call Back Widget:'.$fe_user_general.'<br/>
								
								<br />
								
								Nr of all Vociecalls: '.$vc_all.'</br>'.$diagramm->draw($arr);
							$this->content.=$this->doc->section('Message #3:',$content,0,1);
						break;
					}
				}
				
		}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/callbackbutton/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/callbackbutton/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_callbackbutton_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>