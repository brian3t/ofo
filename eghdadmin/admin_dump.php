<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_dump.php                                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");

	include_once("./admin_common.php");

	check_admin_security("db_management");

	$file_name_search = get_param("file_name_search");
	$sort_dir = get_param("sort_dir");
	if (!$sort_dir) { $sort_dir = "asc"; }

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_dump.html");
	$t->set_var("admin_dump_href", "admin_dump.php");
	$t->set_var("admin_db_query_href", "admin_db_query.php");

	$t->set_var("admin_dump_apply_html_href", "admin_dump_apply.php");
	$t->set_var("admin_dump_create_href", "admin_dump_create.php");
	$t->set_var("file_name_search", $file_name_search);

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_dump.php");
	$s->set_parameters(false, true, true, true);
	$s->set_default_sorting("1", "asc");
	$s->set_sorter(DUMP_FILENAME_MSG, "dump_filename", "1", "");

	$files_dir = "../db/";
	$dir_values = array();
	if ($dir = @opendir($files_dir)){
		$dir_index = 0;
		while($file = readdir($dir)){
			if ($file != "." && $file != ".." && is_file($files_dir . $file)){
				$dir_values[$dir_index] = $file;
				$dir_index++;
			}
		}
		closedir($dir);
	}

	if (strlen($file_name_search)) {
		$file_name_search = preg_quote($file_name_search, "/");
		$found_file=array();
		for($i = 0; $i < sizeof($dir_values); $i++)
		{
			if (strlen($file_name_search) <= strlen($dir_values[$i])) {
				if (preg_match("/".$file_name_search."/i", $dir_values[$i])) {
					$found_file[]=$dir_values[$i];
				}
			}
		}
		$dir_values = $found_file;
	}

	$image_number = isset($dir_values) ? sizeof($dir_values) : 0;

	if($image_number){
		if ($sort_dir == "desc") {
			array_multisort($dir_values, SORT_DESC);
		}
		if ($sort_dir == "asc") {
			array_multisort($dir_values, SORT_ASC);
		}
		for($i = 0; $i < sizeof($dir_values); $i++){
			$file_name = $dir_values[$i];
			$file_name_js = str_replace("'", "\\'", $file_name);
			$t->set_var("file_name", $file_name);
			$t->set_var("file_name_js", $file_name_js);
			$t->parse("dump_row", true);
		}

//		$t->set_var("no_images", "");
		$t->parse("dumps", false);
	}else{
//		$t->parse("no_images", false);
		$t->set_var("dumps", "");
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>