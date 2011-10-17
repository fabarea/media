#
# Table structure for table 'tx_media'
#
CREATE TABLE sys_file (

	status varchar(24) DEFAULT '' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	keywords text NOT NULL,
	creation_date int(11) DEFAULT '0' NOT NULL,
	modification_date int(11) DEFAULT '0' NOT NULL,
	creator_tool varchar(255) DEFAULT '' NOT NULL,
	download_name varchar(255) DEFAULT '' NOT NULL,
	identifier varchar(255) DEFAULT '' NOT NULL,
	creator varchar(255) DEFAULT '' NOT NULL,
	publisher varchar(45) DEFAULT '' NOT NULL,
	source varchar(255) DEFAULT '' NOT NULL,
	alternative varchar(255) DEFAULT '' NOT NULL,
	caption varchar(255) DEFAULT '' NOT NULL,
	location_country varchar(45) DEFAULT '' NOT NULL,
	location_region varchar(45) DEFAULT '' NOT NULL,
	location_city varchar(45) DEFAULT '' NOT NULL,
	latitude decimal(24,14) DEFAULT '0.00000000000000' NOT NULL,
	longitude decimal(24,14) DEFAULT '0.00000000000000' NOT NULL,
	ranking int(11) unsigned DEFAULT '0',
	note text NOT NULL,
	file int(11) unsigned DEFAULT '0',
	thumbnail int(11) unsigned DEFAULT '0',

	# TEXT + IMAGE + VIDEO
	# 21 cm, 29.7 cm: A4
	width float unsigned DEFAULT '0' NOT NULL,
	height float unsigned DEFAULT '0' NOT NULL,
	# px,mm,cm,m,p, ...
	unit char(3) DEFAULT '' NOT NULL,

	# AUDIO + VIDEO
	duration float unsigned DEFAULT '0' NOT NULL,

	# IMAGE
	horizontal_resolution int(11) unsigned DEFAULT '0' NOT NULL,
	vertical_resolution int(11) unsigned DEFAULT '0' NOT NULL,
	# RGB,sRGB,YUV, ...
	color_space varchar(4) DEFAULT '' NOT NULL,

	# TEXT ASSET
	# text document include x pages
	pages int(4) unsigned DEFAULT '0' NOT NULL,

	# TEXT + AUDIO + VIDEO
	# document language
	language varchar(12) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	# @todo implement the auto update of "upuser_id"
	upuser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,
);

