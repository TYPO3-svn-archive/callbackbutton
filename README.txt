This is an typo3-extension which follows the extension-codeing guidelines as much as possible, I hope. 
It is used as a frontend-plugin and I recommend to load and install the sr_freecap Extension. It is used for the captcha. If you don't need an Captcha you also can deactivate it in the backend under 'Limits'. 

Before installing you shpould make sure, that you have an account at http://www.developercenter.telekom.com/ registered the voice Call API and added some money to this account. If not it will be useless to you. 
If yes you just have to add it to a new record at your website and fill in the accountdata + the number which should be called in the backend of typo3. 


Fell free to add some functions or tell me what should be added. 

The most important files are located in pi1/ :

pi1/eId_start.php -> eid-file containing functions used by for ajax

pi1/dbFormatter.php -> database functions 
		->createUser(), updateDB(), getDBStff(), checkSecu(), createStatistics()

pi1/locallang.xml -> language file

pi1/vocieCallClass.php -> contianing methods using the API of developergarden.com to initiate the voicecall und functions used by the eId
		   -> startVoiceCall(), stopVoiceCall(), checkNr() (checks wether nr is allowed to call), 			getCountryArray(), setVC(), setStatistic() (using db)

pi1/createHTMLtags.php -> file that returns used HTML-Tags to keep the mainclass free from this

pi1/class.tx_callback_pi1.php -> Main Class, Controller, responsible for the main part of the logic side of the extension

directories: 

src/ : containing sourcefiles of voiceCallClass.php published by developergarden.com

templates/ :containing the html-template of the extension 

js/ : jquery.js and control.js used for ajax like showing the stats and connectiontime 

css/ : contianing stylesheets of extension

/mod1/ : containing files for statistics

/doc/ : basic information and users manual 

/ext_icon.gif Icon shown in the typo3-backend

/ext_tables.sql needed to create the datatables during the installation-> just creating

/ext_typoscript_setup.txt some static typoscript to register the template and make the jqeury able to be deactivated via typoscript in the backend. If you alreadey added a jquery File just set it to 1

/flexform_ds_pi1.xml Fleyform creating the backend, there you find the names of properties used in tx_callback_pi1

/locallang.xml languagefile for flexform 