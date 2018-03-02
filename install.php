<?php

/**
 *
 * 
 *  @module         MPForm
 *  @author         Frank Heyne, Dietrich Roland Pehlke, erpe
 *  @license        http://www.gnu.org/licenses/gpl.htm
 *  @platform       see info.php of this addon
 *  @license terms  see info.php of this addon
 *  @version        see info.php of this module
 *  
 *
 */

// Rename files to enable custom entries
if (!file_exists(LEPTON_PATH."/modules/mpform/backend.css")) {
	if (file_exists(LEPTON_PATH."/modules/mpform/backend.css.txt")) {
		rename(LEPTON_PATH."/modules/mpform/backend.css.txt",  LEPTON_PATH."/modules/mpform/backend.css");
	}
}
	
if (!file_exists(LEPTON_PATH."/modules/mpform/frontend.css")) {
	if (file_exists(LEPTON_PATH."/modules/mpform/frontend.css.txt")) {
		rename(LEPTON_PATH."/modules/mpform/frontend.css.txt", LEPTON_PATH."/modules/mpform/frontend.css");
	}
}
	
if (!file_exists(LEPTON_PATH."/modules/mpform/custom.php")) {
	if (file_exists(LEPTON_PATH."/modules/mpform/custom.php.txt")) {
		rename(LEPTON_PATH."/modules/mpform/custom.php.txt",  LEPTON_PATH."/modules/mpform/custom.php");
	}
}
	
// Create tables
$table_fields="
	`field_id` INT NOT NULL AUTO_INCREMENT,
    `section_id` INT NULL DEFAULT '0',
    `page_id` INT NOT NULL DEFAULT '0',
    `position` INT NOT NULL DEFAULT '0',
    `title` VARCHAR(255) NOT NULL DEFAULT '',
	`type` VARCHAR(255) NOT NULL DEFAULT '',
	`required` INT NOT NULL DEFAULT '0',
	`value` TEXT NOT NULL ,
	`extra` TEXT NOT NULL ,
	`help` TEXT NULL ,
    PRIMARY KEY (`field_id`)
";
LEPTON_handle::install_table('mod_mpform_fields', $table_fields);


$table_fields="
    `section_id` INT NULL DEFAULT '0',
    `page_id` INT NOT NULL DEFAULT '0',
    `header` TEXT NOT NULL ,
    `field_loop` TEXT NOT NULL ,
	`footer` TEXT NOT NULL ,
	`email_to` TEXT NOT NULL ,
	`email_from` VARCHAR(255) NOT NULL DEFAULT '',
	`email_fromname` VARCHAR(255) NOT NULL DEFAULT '',
	`email_subject` VARCHAR(255) NOT NULL DEFAULT '',
	`email_text` TEXT NOT NULL ,
	`success_page` TEXT NOT NULL ,
	`success_text` TEXT NOT NULL ,
	`submissions_text` TEXT NOT NULL ,
	`success_email_to` TEXT NOT NULL ,
	`success_email_from` VARCHAR(255) NOT NULL DEFAULT '',
	`success_email_fromname` VARCHAR(255) NOT NULL DEFAULT '',
	`success_email_text` TEXT NOT NULL ,
	`success_email_subject` VARCHAR(255) NOT NULL DEFAULT '',
	`stored_submissions` INT NOT NULL DEFAULT '0',
	`max_submissions` INT NOT NULL DEFAULT '0',
	`heading_html` TEXT NOT NULL ,
	`short_html` TEXT NOT NULL ,
	`long_html` TEXT NOT NULL ,
	`email_html` TEXT NOT NULL ,
	`uploadfile_html` TEXT NOT NULL ,
	`use_captcha` INT NOT NULL DEFAULT '0',
	`upload_files_folder` TEXT NOT NULL ,
	`date_format` TEXT NOT NULL ,
	`max_file_size_kb` INT NOT NULL DEFAULT '0',
	`attach_file` INT NOT NULL DEFAULT '0',
	`upload_file_mask` VARCHAR(4) NOT NULL DEFAULT '',
	`upload_dir_mask` VARCHAR(4) NOT NULL DEFAULT '',
	`upload_only_exts` VARCHAR(255) DEFAULT '',
	`is_following` BOOL NOT NULL DEFAULT '0',
	`tbl_suffix` VARCHAR(100) DEFAULT '',
	`enum_start` VARCHAR(1) DEFAULT '',
    PRIMARY KEY (`section_id`)
";
LEPTON_handle::install_table('mod_mpform_settings', $table_fields);


$table_fields="
	`submission_id` INT NOT NULL AUTO_INCREMENT,'
    `section_id` INT NULL DEFAULT '0',
    `page_id` INT NOT NULL DEFAULT '0',
	`started_when` INT NOT NULL DEFAULT '0',
	`submitted_when` INT NOT NULL DEFAULT '0',
	`submitted_by` INT NOT NULL DEFAULT '0',
	`session_id` TEXT NOT NULL ,
	`ip_addr` TEXT NOT NULL ,
	`body` TEXT NOT NULL,
	`upload_filename` TEXT NOT NULL ,
	`upload_data_serialized` LONGTEXT NULL ,
    PRIMARY KEY (`submission_id`)
";
LEPTON_handle::install_table('mod_mpform_submissions', $table_fields);
	

// Insert info into the search table
// Module query info
$field_info = array();
$field_info['page_id'] = 'page_id';
$field_info['title'] = 'page_title';
$field_info['link'] = 'link';
$field_info['description'] = 'description';
$field_info['modified_when'] = 'modified_when';
$field_info['modified_by'] = 'modified_by';
$field_info = serialize($field_info);

// Query start
$query_start_code = "SELECT [TP]pages.page_id, [TP]pages.page_title,	[TP]pages.link, [TP]pages.description, [TP]pages.modified_when, [TP]pages.modified_by	FROM [TP]mod_mpform_fields, [TP]mod_mpform_settings, [TP]pages WHERE ";

// Query body
$query_body_code = " 
	[TP]pages.page_id = [TP]mod_mpform_settings.page_id AND [TP]mod_mpform_settings.header LIKE \'%[STRING]%\' AND [TP]pages.searching = \'1\'
	OR [TP]pages.page_id = [TP]mod_mpform_settings.page_id AND [TP]mod_mpform_settings.footer LIKE \'%[STRING]%\' AND [TP]pages.searching = \'1\'
	OR [TP]pages.page_id = [TP]mod_mpform_fields.page_id AND [TP]mod_mpform_fields.title LIKE \'%[STRING]%\' AND [TP]pages.searching = \'1\'
";
	
$field_values="
	('','module', 'mpform', '".$field_info."'),	
	('','query_start', '".$query_start_code."', 'mpform'),
	('','query_body', '".$query_body_code."', 'mpform'),
	('','query_end', '', 'mpform')
";
LEPTON_handle::insert_values('search', $field_values);

?>