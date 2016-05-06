<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_fm_newfile.php                                     ***
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
	$t->set_file("main","admin_fm_newfile.html");
	$new_file = terminator(get_param("new_file"));
	$dir_path = check_dir_path(get_param("dir_path"));
	$file_content = get_param("file_content");
	$op = terminator(get_param("op"));

	$content_file = $file_content;

	if ($new_file && $dir_path){
		$error = check_dir_and_file($dir_path, $new_file, "new");
	}
	
	if((strlen($op) > 0) && $op == 1 && isset($file_content) && $new_file && !$error) {
		if(in_array(get_ext_file($new_file), $array_text_files)) {
			if (!file_exists($dir_path . "/" . $new_file)){
				if(@$fp = fopen($dir_path . "/" . $new_file, "w")) {
					fwrite($fp, stripslashes($file_content));
					fclose($fp);
					$service_message = FILE_CREATED_MSG . $new_file ;
				}
				else {
					$error .= fm_errors(114,$val_file["name"]);
				}
			} else {
				$error .= fm_errors(104,$new_file);
			}
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
	$t->set_var("admin_filemanager_fm_href", "admin_fm.php");
	$t->set_var("admin_newfile_href", "admin_fm_newfile.php");
	$t->set_var("dir_path", $dir_path);
	$t->set_var("content_file", $content_file);
	$t->set_var("new_file", $new_file);
	$t->set_var("host", $host);
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
?>