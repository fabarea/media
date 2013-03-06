#
# Table structure for table 'sys_file'
#
CREATE TABLE sys_file (

	status varchar(24) DEFAULT '' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	keywords text NOT NULL,
	creator_tool varchar(255) DEFAULT '' NOT NULL,
	download_name varchar(255) DEFAULT '' NOT NULL,
	identifier varchar(255) DEFAULT '' NOT NULL,
	creator varchar(255) DEFAULT '' NOT NULL,
	publisher varchar(45) DEFAULT '' NOT NULL,
	published int(11) unsigned DEfAULT '0' NOT NULL,
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
	variants int(11) unsigned DEfAULT '0' NOT NULL,
	is_variant tinyint(1) unsigned DEFAULT '0' NOT NULL,
	categories int(11) unsigned DEfAULT '0' NOT NULL,

	# TEXT + IMAGE + VIDEO
	# 21 cm, 29.7 cm: A4
	width float unsigned DEFAULT '0' NOT NULL,
	height float unsigned DEFAULT '0' NOT NULL,
	# px,mm,cm,m,p, ...
	unit char(3) DEFAULT '' NOT NULL,

	# AUDIO + VIDEO
	duration float unsigned DEFAULT '0' NOT NULL,

	# VIDEO
	preview_image int(11) unsigned DEFAULT '0' NOT NULL,

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

CREATE TABLE sys_file_variants (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	role int(11) unsigned DEFAULT '0' NOT NULL,
	original int(11) unsigned DEFAULT '0' NOT NULL,
	variant int(11) unsigned DEFAULT '0' NOT NULL,
	variation varchar(255) DEFAULT '' NOT NULL,
	is_dummy_record tinyint(1) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY phpunit_dummy (is_dummy_record)
);
