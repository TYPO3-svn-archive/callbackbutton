#
#	Security Table 
#
CREATE TABLE tx_callbackbutton_secu (
	fe_user_id int(11) 	unsigned NOT NULL,
	
	 vc_per_period int(11) unsigned DEFAULT '0' NOT NULL,
	 period_start datetime NOT NULL,
	 period_end datetime NOT NULL,
	PRIMARY KEY (fe_user_id)

);
#
# Table for Statistics
#
CREATE TABLE tx_callbackbutton_statistics (
	vc_nr    MEDIUMINT  NOT NULL AUTO_INCREMENT,
	vc_hour  int(11) unsigned NOT NULL,
	vc_day   int(11) unsigned NOT NULL,
	vc_month int(11) unsigned NOT NULL,
	vc_year  int(11) unsigned NOT NULL,
	vc_dur   int(11) unsigned NOT NULL,
	PRIMARY KEY (vc_nr)
);