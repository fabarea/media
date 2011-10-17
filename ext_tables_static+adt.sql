#
# Table structure for table 'sys_file_type'
#
CREATE TABLE sys_file_type (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	type int(11) DEFAULT '0' NOT NULL,
	type_name varchar(255) DEFAULT '' NOT NULL,

	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

INSERT INTO sys_file_type VALUES ('1', '0', '1', 'Text', '0', '0');
INSERT INTO sys_file_type VALUES ('2', '0', '2', 'Image', '0', '0');
INSERT INTO sys_file_type VALUES ('3', '0', '3', 'Audio', '0', '0');
INSERT INTO sys_file_type VALUES ('4', '0', '4', 'Video', '0', '0');
INSERT INTO sys_file_type VALUES ('5', '0', '5', 'Software', '0', '0');

#
# Table structure for table 'sys_file_mimetype'
#
DROP TABLE IF EXISTS sys_file_mimetype;
CREATE TABLE sys_file_mimetype (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	mime_type varchar(255) DEFAULT '' NOT NULL,
	file_type int(11) unsigned DEFAULT '0',

	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);


INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/basic', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/basic', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/midi', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/midi', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/midi', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/mpeg', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/mpeg', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/mpegurl', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/prs.sid', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-aiff', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-aiff', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-aiff', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-aiff', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-epac', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-gsm', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-mod', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-mod', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-mod', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-mod', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-mod', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-mod', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-mod', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-mod', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-mpeg', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-mpeg', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-ms-wax', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-ms-wma', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-pac', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-pn-realaudio', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-pn-realaudio', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-pn-realaudio', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-wav', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/x-m4a', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/mp4a-latm', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/mp4a-latm', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/aac', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/ogg', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'audio/ogg', '3', '0', '0');

INSERT INTO sys_file_mimetype VALUES ('', '0', 'encoding/x-compress', '0', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'encoding/x-gzip', '0', '0', '0');

INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/bitmap', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/gif', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/ief', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/jpeg', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/jpeg', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/jpeg', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/pcx', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/png', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/tiff', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/tiff', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/vnd.wap.wbmp', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-cmu-raster', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-coreldraw', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-coreldrawpattern', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-coreldrawtemplate', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-corelphotopaint', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-jng', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-photo-cd', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-portable-anymap', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-portable-bitmap', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-portable-graymap', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-portable-pixmap', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-rgb', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-xbitmap', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-xpixmap', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/x-xwindowdump', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/svg+xml', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'image/xcf', '2', '0', '0');

INSERT INTO sys_file_mimetype VALUES ('', '0', 'model/iges', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'model/iges', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'model/mesh', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'model/mesh', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'model/mesh', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'model/vrml', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'model/vrml', '5', '0', '0');

INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/calendar', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/calendar', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/calendar', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/comma-separated-values', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/css', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/diff', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/html', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/html', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/html', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/html', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/mathml', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/plain', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/plain', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/plain', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/plain', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/plain', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/plain', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/richtext', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/sgml', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/sgml', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/tab-separated-values', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/vnd.wap.wml', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/vnd.wap.wmlscript', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-c++hdr', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-c++hdr', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-c++hdr', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-c++hdr', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-c++src', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-c++src', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-c++src', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-c++src', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-chdr', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-csrc', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-java', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-pascal', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-pascal', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-setext', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-tcl', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-tex', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-tex', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-tex', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-vcalendar', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/x-vcard', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/xml', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'text/xml', '1', '0', '0');

INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/dl', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/gl', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/mpeg', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/mpeg', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/mpeg', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/quicktime', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/quicktime', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/vnd.mpegurl', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-anim', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-anim', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-anim', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-anim', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-anim', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-flc', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-fli', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-flv', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-mng', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-ms-asf', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-ms-asf', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-ms-wm', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-ms-wmv', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-ms-wmx', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-ms-wvx', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-msvideo', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-rad-screenplay', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-sgi-movie', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-sgi-movie', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-sgi-movie', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-sunvideo', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/mp4v-es', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/x-m4v', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/mp4v-es', '4', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'video/ogg', '4', '0', '0');

INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/andrew-inset', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/cu-seeme', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/cu-seeme', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/dsptype', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/fractals', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/futuresplash', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/mac-binhex40', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/msaccess', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/msexcel', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/msexcel', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/mshelp', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/mspowerpoint', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/msproject', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/msproject', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/msproject', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/msproject', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/msproject', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/msword', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/octet-stream', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/octet-stream', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/octet-stream', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/oda', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/pdf', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/pgp-signature', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/postscript', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/postscript', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/postscript', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/rtf', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/smil', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/smil', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.ms-excel', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.ms-powerpoint', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.ms-powerpoint', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.sun.xml.calc', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.sun.xml.calc.template', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.sun.xml.draw', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.sun.xml.draw.template', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.sun.xml.impress', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.sun.xml.impress.template', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.sun.xml.math', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.sun.xml.writer', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.sun.xml.writer.global', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.sun.xml.writer.template', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.visio', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.wap.wbxml', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.wap.wmlc', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.wap.wmlscriptc', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/wordperfect5.1', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-123', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-applix', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-bcpio', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-cdlink', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-chess-pgn', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-compress', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-cpio', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-csh', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-debian-package', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-director', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-director', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-director', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-dms', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-dot', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-dvi', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-fmr', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-font', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-font', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-font', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-font', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-font', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-fr', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-gnumeric', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-gtar', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-gtar', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-hdf', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-httpd-php', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-httpd-php', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-httpd-php', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-httpd-php3', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-httpd-php3-preprocessed', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-httpd-php3-source', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-httpd-php4', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-ica', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-java', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-javascript', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-kchart', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-killustrator', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-koan', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-koan', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-koan', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-koan', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-kpresenter', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-kpresenter', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-kspread', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-kword', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-kword', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-latex', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-lha', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-lzh', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-lzx', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-maker', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-maker', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-maker', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-maker', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-maker', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-maker', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-maker', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-mif', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-mif', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-ms-wmd', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-ms-wmz', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-msdos-program', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-msdos-program', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-msdos-program', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-msi', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-netcdf', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-netcdf', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-ns-proxy-autoconfig', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-object', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-ogg', '3', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-oz-application', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-perl', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-perl', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-perl', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-quark-xpress-3', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-redhat-package-manager', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-sh', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-shar', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-shockwave-flash', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-shockwave-flash', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-stuffit', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-tar', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-tcl', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-tex', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-tex-gf', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-tex-pk', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-tex-pk', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-texinfo', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-texinfo', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-tkined', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-tkined', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-trash', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-trash', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-trash', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-trash', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-trash', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-troff', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-troff', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-troff', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-troff-man', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-troff-me', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-troff-ms', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/x-zip-compressed', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/xhtml+xml', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/photoshop', '2', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.text', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.formula-template', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.text-template', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.text-web', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.text-master', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.graphics', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.graphics-template', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.presentation', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.presentation-template', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.spreadsheet', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.spreadsheet-template', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.chart', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.formula', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.database', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.oasis.opendocument.image', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.openofficeorg.extension', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.ms-word.document.macroEnabled.12', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '1', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.ms-word.template.macroEnabled.12', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.openxmlformats-officedocument.wordprocessingml.template', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.openxmlformats-officedocument.presentationml.slideshow', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.ms-powerpoint.presentation.macroEnabled.12', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.ms-excel.sheet.binary.macroEnabled.12', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.ms-excel.sheet.macroEnabled.12', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', '5', '0', '0');
INSERT INTO sys_file_mimetype VALUES ('', '0', 'application/vnd.ms-xpsdocument', '5', '0', '0');
