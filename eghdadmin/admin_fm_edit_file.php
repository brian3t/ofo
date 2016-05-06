<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_fm_edit_file.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/admin_fm_functions.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once("./admin_common.php");
	include_once("./admin_fm_config.php");

	check_admin_security("filemanager");

	$error = "";
	$service_message = "";
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_fm_edit_file.html");
	
	$edit_file = terminator(get_param("edit_file"));
	$old_edit_file = terminator(get_param("old_edit_file"));
	$dir_path = get_param("dir_path");
	$dir_path = check_dir_path($dir_path);
	$error .= check_dir_and_file($dir_path, $edit_file, "edit");
	$op = terminator(get_param("op"));
	$file_content = get_param("file_content");
	
	if((strlen($op) > 0) && $op == 1 && isset($file_content) && !$error) {

		rename($dir_path . "/" . $old_edit_file, $dir_path . "/" . $edit_file);
		if(@$fp = fopen($dir_path . "/" . $edit_file, "w")) {
			fwrite($fp, stripslashes($file_content));
			fclose($fp);
			$service_message .= FILE_SAVED_MSG . $edit_file ;
		}
		else {
			$error .= fm_errors(116,$edit_file);
		}
	}
	if(strlen($error) > 0) {
		$t->set_var("error", $error);
		$t->parse("errors", true);
	}
	else {
		$t->set_var("errors", "");
	}
	if(strlen($service_message) > 0) {
		$t->set_var("service_message", $service_message);
		$t->parse("service_messages", true);
	}
	else {
		$t->set_var("service_messages", "");
	}
	if(isset($edit_file) && isset($dir_path) && !$error) {
		$content_file =  htmlspecialchars(file_get_contents($dir_path . "/" . $edit_file));
	} else {
		$content_file = "";
	}

	$t->set_var("admin_filemanager_fm_href", "admin_fm.php");
	$t->set_var("admin_edit_file_href", "admin_fm_edit_file.php");
	$t->set_var("dir_path", $dir_path);
	$t->set_var("content_file", $content_file);
	$t->set_var("edit_file", $edit_file);
	$t->set_var("host", $host);
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
?>