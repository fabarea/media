#
# Table structure for table 'sys_file'
#
CREATE TABLE sys_file (

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,

	status varchar(24) DEFAULT '' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	keywords text NOT NULL,
	creator_tool varchar(255) DEFAULT '' NOT NULL,
	download_name varchar(255) DEFAULT '' NOT NULL,
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
	variants int(11) unsigned DEfAULT '0' NOT NULL,
	is_variant tinyint(1) unsigned DEFAULT '0' NOT NULL,
	fe_groups int(11) unsigned DEfAULT '0' NOT NULL,

	# TEXT + IMAGE + VIDEO
	# 21 cm, 29.7 cm: A4
	width float unsigned DEFAULT '0' NOT NULL,
	height float unsigned DEFAULT '0' NOT NULL,
	# px,mm,cm,m,p, ...
	unit char(3) DEFAULT '' NOT NULL,

	# AUDIO + VIDEO
	duration float unsigned DEFAULT '0' NOT NULL,

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
	upuser_id int(11) unsigned DEFAULT '0' NOT NULL,
	visible tinyint(4) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
);

#
# Table structure for table 'sys_file_fegroups_mm'
#
CREATE TABLE sys_file_fegroups_mm (
	uid_local int(11) unsigned DEFAULT '0' NOT NULL,
	uid_foreign int(11) unsigned DEFAULT '0' NOT NULL,
	sorting int(11) unsigned DEFAULT '0' NOT NULL,
	sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

	KEY uid_local (uid_local),
	KEY uid_foreign (uid_foreign)
);

#
# Table structure for table 'sys_file_storage'
#
CREATE TABLE sys_file_storage (
	mount_point_file_type_1 int(11) unsigned DEFAULT '0' NOT NULL,
	mount_point_file_type_2 int(11) unsigned DEFAULT '0' NOT NULL,
	mount_point_file_type_3 int(11) unsigned DEFAULT '0' NOT NULL,
	mount_point_file_type_4 int(11) unsigned DEFAULT '0' NOT NULL,
	mount_point_file_type_5 int(11) unsigned DEFAULT '0' NOT NULL,
	mount_point_file_type_5 int(11) unsigned DEFAULT '0' NOT NULL,
	mount_point_variant int(11) unsigned DEFAULT '0' NOT NULL,

	maximum_dimension_original_image varchar(24) DEFAULT '' NOT NULL,
	default_variations varchar(24) DEFAULT '' NOT NULL,

	is_protected  int(11) unsigned DEFAULT '0' NOT NULL,
	extension_allowed_file_type_1 varchar(255) DEFAULT '' NOT NULL,
	extension_allowed_file_type_2 varchar(255) DEFAULT '' NOT NULL,
	extension_allowed_file_type_3 varchar(255) DEFAULT '' NOT NULL,
	extension_allowed_file_type_4 varchar(255) DEFAULT '' NOT NULL,
	extension_allowed_file_type_5 varchar(255) DEFAULT '' NOT NULL,
);

##
# Table structure for table 'sys_file_variants'
#
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
