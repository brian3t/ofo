<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_fm_upload_files.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/admin_fm_functions.php");
	include_once($root_folder_path . "includes/zip_class.php");

	include_once("./admin_common.php");
	include_once("./admin_fm_config.php");

	check_admin_security("filemanager");

	$dir_root = check_dir_path(get_param("dir_root"));
	$error = check_dir_and_file($dir_root, "", "chmod");
	if (strlen($error)){
		set_session("fm_error",$error);
		header("Location: " . $host."admin_fm.php");
		exit;
	}
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	
	$op = terminator(get_param("op"));

	if(strlen($op) == 0) {
		$op = 1;
	}
	
	if($op == 1) {
		$t->set_file("main","admin_fm_upload_files.html");
	}
	else {
		$t->set_file("main","admin_fm_upload_archive.html");		
	}
	$error = "";
	$service_message = "";

	$tmp_dir_arch = $tmp_dir_arch;
	$t->set_var("dir_root", $dir_root);
	if(is_array($_FILES)) {
		foreach($_FILES as $k_file => $val_file) {
			if($val_file["error"] == 0) {
				$val_file["name"] = terminator($val_file["name"]);
				$ext = get_ext_file($val_file["name"]);
				if((in_array($ext, $conf_array_files_ext) && $op == 1)|| (in_array($ext, $array_archive_file) && $op == 2)) {
					if(is_uploaded_file($val_file["tmp_name"])) {
						copy($val_file["tmp_name"], $dir_root . "/" . $val_file["name"]);
						if($op == 1) {
							$service_message = FILE_SAVED_MSG . $val_file["name"] ;
						}
						elseif($op == 2) {
							$array_files = array();
							$flname = $dir_root . "/" .  $val_file["name"];
							$archive = new XPRAPTORZIP($flname);
							if ($archive->extract(XPrptrZIP_OPT_PATH, $dir_root . "/" . $tmp_dir_arch) != 0) {
								$error_files = check_error_files($dir_root . "/" . $tmp_dir_arch);
								rm($dir_root . "/" . $tmp_dir_arch);
								if(strlen($error_files) > 0) {
									$error_files = substr($error_files, 0, -2);
									$error = INCORRECT_EXTENSIONS_MSG  . $error_files . "<br>" . ARCHIVE_MSG . $val_file["name"] . ".";
									//$t->set_var("error", );
								}
								else {
									$archive->extract(XPrptrZIP_OPT_PATH, $dir_root);
									$service_message = FILE_SAVED_MSG . $val_file["name"] ;
								}
							}
							else {
								$error = $archive->errorInfo(true);
							}
							rm($flname);
						}
					}
					else {
						$error = fm_errors(114,$val_file["name"]);
					}
				}
				else {
					$error = fm_errors(115,$val_file["name"]);
				}
				$t->parse("errors", true);
				$t->parse("service_messages", true);
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
	$t->set_var("admin_select_href", "admin_select.php");
	$t->set_var("admin_upload_href", "admin_fm_upload_files.php");
	$t->set_var("admin_filemanager_href", "admin_fm.php");
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>