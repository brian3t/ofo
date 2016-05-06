<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_fm_view_file.php                                   ***
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

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_fm_view_file.html");

	$t->set_var("admin_view_file_href", "admin_fm_view_file.php");
	$t->set_var("admin_download_file_href", "admin_fm_download_file.php");
	$t->set_var("admin_filemanager_href", "admin_fm.php");
	$t->set_var("admin_upload_href", "admin_fm_upload_files.php");
	$t->set_var("admin_edit_file_href", "admin_fm_edit_file.php");
	$t->set_var("admin_conf_href", "admin_conf_filemanager.php");
	$t->set_var("admin_newfile_href", "admin_fm_newfile.php");

	$view_file = terminator(get_param("view_file"));
	$dir_path = check_dir_path(get_param("dir_path"));
	
	$error = check_dir_and_file($dir_path, $view_file, "view");
	
	$ext = get_ext_file($view_file);
	
	if (!$error){
		if(in_array($ext, $array_text_files)) {
			if($array_text = file($dir_path . "/" . $view_file)) {
				if(count($array_text) > 0) {
					$view_content = "";
					$view_content .= "<table>";
					$view_content .=  "<tr bgcolor=#DEDEDE><td>#</td><td><b>" . $view_file . "</b></td></tr>";
					foreach($array_text as $k => $v) {
						$view_content .=  "<tr><td bgcolor=#DEDEDE>" . $k . "</td><td bgcolor=#e5e5e5>" . htmlspecialchars($v) . "</td></tr>";
					}
					$view_content .=  "</table>";
				}
				else {
					$view_content = "";
				}
			}
			$t->set_var("view_content", $view_content);
		}
		elseif(in_array($ext, $array_image_file)) {
			$t->set_var("view_content", "<img src='" . $dir_path . "/" . $view_file . "'>");;
		}
		elseif(in_array($ext, $array_download_file)) {
			$t->set_var("view_content", "");
			header("Content-Disposition: attachment; filename=" . $view_file); 
			$file = fread(fopen($dir_path . "/" . $view_file, "rb"), filesize($dir_path . "/" . $view_file)); 
			print $file;
			exit();
		}
	} else {
		$view_content = "<div class=\"error\">".$error."</div>";
		$t->set_var("view_content", $view_content);
	}
	$t->set_var("view_file", $view_file);
	$t->set_var("dir_path", $dir_path);
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	$t->pparse("main");
?>