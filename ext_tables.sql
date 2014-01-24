#
# Table structure for table 'sys_file'
#
CREATE TABLE sys_file (
	variants int(11) unsigned DEfAULT '0' NOT NULL,
	is_variant tinyint(1) unsigned DEFAULT '0' NOT NULL,
	variation varchar(255) DEFAULT '' NOT NULL,
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

	extension_allowed_file_type_1 varchar(255) DEFAULT '' NOT NULL,
	extension_allowed_file_type_2 varchar(255) DEFAULT '' NOT NULL,
	extension_allowed_file_type_3 varchar(255) DEFAULT '' NOT NULL,
	extension_allowed_file_type_4 varchar(255) DEFAULT '' NOT NULL,
	extension_allowed_file_type_5 varchar(255) DEFAULT '' NOT NULL,
);

##
# Table structure for table 'sys_file_variants'
# variant_file int(11) unsigned DEFAULT '0' NOT NULL,
#
CREATE TABLE sys_file_variants (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	role int(11) unsigned DEFAULT '0' NOT NULL,
	original_resource int(11) unsigned DEFAULT '0' NOT NULL,
	variant_resource int(11) unsigned DEFAULT '0' NOT NULL,
	is_dummy_record tinyint(1) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY phpunit_dummy (is_dummy_record)
);
