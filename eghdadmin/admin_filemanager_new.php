<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_filemanager_new.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include($root_folder_path . "includes/fmanager_functions.php");

	include_once("./admin_common.php");

	check_admin_security("filemanager");
	//config variable
	$conf_root_dir = "..";
	$_SESSION["conf_root_dir"] = $conf_root_dir;
	//allow folders
	$conf_array_dirs = array("templates", "styles", "images");
	$_SESSION["conf_array_dirs"] = $conf_array_dirs;
	//allow extnsion
	$conf_array_files_ext = array("gif", "jpg", "jpeg", "bmp", "png", "doc", "pdf", "xls", "html", "htm", "css");
	$_SESSION["conf_array_files_ext"] = $conf_array_files_ext;
	$tmp_dir_arch = "va_temp";
	$_SESSION["tmp_dir_arch"] = $tmp_dir_arch;
	//end config variable
	//mediate dir

	$atom_mdir = explode("/", $_SERVER["PHP_SELF"]);
	if(!isset($mediate_dir)) $mediate_dir = "";
	if(is_array($atom_mdir) && (count($atom_mdir) > 0)) {
		for($i = 0; $i < (count($atom_mdir) - 1); $i++) {
			$mediate_dir .= $atom_mdir[$i] . "/";
		}
	}
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_filemanager.html");

	$t->set_var("admin_href",                   "admin.php");
	$t->set_var("admin_view_file_href", "admin_view_file.php");
	$t->set_var("admin_download_file_href", "admin_download_file.php");
	$t->set_var("admin_filemanager_href", "admin_filemanager.php");
	$t->set_var("admin_upload_href", "admin_upload_files.php");
	$t->set_var("admin_edit_file_href", "admin_edit_file.php");
	$t->set_var("admin_conf_href", "admin_conf_filemanager.php");
	$t->set_var("admin_newfile_href", "admin_newfile.php");
	
	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_articles_top.php");
	$s->set_sorter(ID_MSG, "sorter_category_id", "1", "category_id");
	$s->set_sorter(ARTICLES_TYPE_MSG, "sorter_category_name", "2", "category_name");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_articles_top.php");

	$r = new VA_Record("");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	//exec
	if(is_array($_GET)) {
		foreach($_GET as $k => $v)
			$_GET[$k] = terminator($v);
	}
	if(is_array($_POST)) {
		foreach($_POST as $k => $v)
			$_POST[$k] = terminator($v);
	}
	//select root dir
	if(!isset($_GET["root_dir"])) {
		$_GET["root_dir"] = $conf_root_dir;
	}
	$root_dir = $_GET["root_dir"];
	$root_dir = chek_dir_path($root_dir);
	//delete file or dir
	if(isset($_GET['del_name'])) {
		rm($root_dir . "/" . $_GET["del_name"]);
	}
	//rename file
	if(isset($_GET["old_name"]) && $_GET["file_rename"] && in_array(get_ext_file($_GET["file_rename"]), $_SESSION["conf_array_files_ext"])) {
		@rename($root_dir . "/" . $_GET["old_name"], $root_dir . "/" . $_GET["file_rename"]);

	}
	//creat new  folder
	if(isset($_GET["new_dname"])) {
		mkdir($root_dir . "/" . $_GET["new_dname"], 0755);
	}
	//change mode
	if(isset($_GET["new_mode"]) && isset($_GET["file_name"])) {
		chmod($root_dir . "/" . $_GET["file_name"], $_GET["new_mode"]);
	}
	//creat directory tree
	$t->set_var("host", $HTTP_HOST . $mediate_dir);
	$_SESSION["host"] = $HTTP_HOST . $mediate_dir;
	$array_dirs = read_dir($conf_root_dir, $conf_array_dirs);
	foreach($array_dirs as $v) {
		$array_dir_tree[] = array($v["path"], $v["name"]);
	}
	asort($array_dir_tree);
	$k = 2;
	$array_complete_dir = array("", "");
	foreach($array_dir_tree as $dir_name) {
		$array_dir = explode("/", $dir_name[0]);
		for($i = 1; $i < count($array_dir); $i++) {
			if(!isset($array_complete_dir[$k][1])) $array_complete_dir[$k][1] = "";
			$array_complete_dir[$k][1] .= " - ";			
		}
		if(!isset($array_complete_dir[$k][0])) $array_complete_dir[$k][0] = "";
		$array_complete_dir[$k][0] .= $dir_name[0];
		$array_complete_dir[$k][1] .= $dir_name[1];
		$k++;
	}
	$array_complete_dir[0][0] = "";
	$array_complete_dir[0][1] = "--" . PLEASE_SELECT_FOLDER_MSG . "--";

	$array_complete_dir[1][0] = $conf_root_dir;
	$array_complete_dir[1][1] = "root";
	$r->add_select("dir_tree", INTEGER, $array_complete_dir, TYPE_MSG);
	$r->set_form_parameters();
//var for curent dir
	$curent_dir_name = explode("/", $root_dir);
	$curent_dir_name = $curent_dir_name[(count($curent_dir_name) - 1)];
	$t->set_var("curent_dir_name", $curent_dir_name);
	$curent_dir_perm =  get_perm_file($root_dir);
	$t->set_var("curent_dir_perm", $curent_dir_perm);
	$digit_curent_dir_perm = 0 . chmodnum($curent_dir_perm);
	$t->set_var("digit_curent_dir_perm", $digit_curent_dir_perm);
	$curent_dir_root = get_people_size(get_size($root_dir, $conf_array_files_ext));
	$t->set_var("curent_dir_root", $curent_dir_root);
	$curent_num_files = get_num_files($root_dir);
	$t->set_var("curent_num_files", $curent_num_files);
//for sort
	if(!isset($_GET["name_sort"])) {
		$_GET["name_sort"] = "SORT_ASC";
	}
	$name_sort = $_GET["name_sort"];

	if(!isset($_GET["size_sort"])) {
		$size_sort = "SORT_ASC";
	}
	else {
		$size_sort = $_GET["size_sort"];
	}
//end exec

	$t->set_var("no_records", "");

//creat folders info
	$array_cont_dir = read_current_dir($root_dir, $conf_array_dirs, $conf_root_dir);

	if(is_array($array_cont_dir) && count($array_cont_dir) > 0) {

		foreach($array_cont_dir as $res) {
			 $sortAux[] = $res['dir_name'];
		}
		if($name_sort == "SORT_DESC") {
			array_multisort($sortAux, SORT_DESC, $array_cont_dir);
		}
		else {
			array_multisort($sortAux, SORT_ASC, $array_cont_dir);
		}

		foreach($array_cont_dir as $k => $v) {
			if(($k == 0) && ($root_dir != $_SESSION["conf_root_dir"])) {
				$array_up_dir = explode("/", $root_dir);
				unset($array_up_dir[(count($array_up_dir) - 1)]);
				$up_dir = implode("/", $array_up_dir);
				$t->set_var("dir_id", "<a href='http://" . $HTTP_HOST . $mediate_dir . "admin_filemanager.php?root_dir=" . $up_dir . "'><img src='images/folder.gif' border=0 width=18 height=14 border=0>&nbsp;" . $v["dir_name"] . "</a>");
				foreach($v as $id =>$val) {
					$t->set_var($id, $val);
				}
				$t->parse("records_dir", true);
			}
			elseif($k != 0) {
				$t->set_var("dir_id", "<a href='http://" . $HTTP_HOST . $mediate_dir . "admin_filemanager.php?root_dir=" . $root_dir . "/" . $v["dir_name"] . "'><img src='images/folder.gif' border=0 width=18 height=14 border=0>&nbsp;" . $v["dir_name"] . "</a>");
				if(in_array($v["dir_name"], $_SESSION["conf_array_dirs"])) {
					$t->set_var("dir_name", $v["dir_name"]);
					$t->set_var("rename", "");
					$t->set_var("delete", "");
					$t->set_var("dir_delete", "");
					$t->set_var("dir_path", $v["dir_path"]);
				}
				else {
					foreach($v as $id =>$val) {
						$t->set_var($id, $val);
					}
				}
				$t->parse("records_dir", true);
			}
		}
	}
	else {
		$t->set_var("records_dir", "");
	}
//creat files info
	$array_cont_file = read_cur_files($root_dir, $conf_array_files_ext, $conf_root_dir);

	if(is_array($array_cont_file) && count($array_cont_file) > 0) {

		if(isset($_GET["name_sort"])) {
			foreach($array_cont_file as $res) {
				 $sortfile[] = $res['file_name'];
			}
			if($name_sort == "SORT_DESC") {
				array_multisort($sortfile, SORT_DESC, $array_cont_file);
			}
			else {
				array_multisort($sortfile, SORT_ASC, $array_cont_file);
			}
		}

		if(isset($_GET["size_sort"])) {

			foreach($array_cont_file as $res) {
				 $sortfile_size[] = $res['byte_size'];
			}

			if($size_sort == "SORT_DESC") {
				array_multisort($sortfile_size, SORT_DESC, $array_cont_file);
			}
			else {
				array_multisort($sortfile_size, SORT_ASC, $array_cont_file);
			}
		}
		foreach($array_cont_file as $k => $v) {
			foreach($v as $id =>$val) {
				$t->set_var($id, $val);
			}
			$t->parse("records_file", true);				
		}
	}
	else {
		$t->set_var("records_file", "");
	}
	
	if($name_sort == "SORT_ASC") {
		$t->set_var("name_sort", "SORT_DESC");
	}
	else {
		$t->set_var("name_sort", "SORT_ASC");
	}

	if($size_sort == "SORT_ASC") {
		$t->set_var("size_sort", "SORT_DESC");
	}
	else {
		$t->set_var("size_sort", "SORT_ASC");
	}
	if($root_dir == $conf_root_dir) {
		$t->set_var("bottom", "");
		$t->set_var("records_file", "");
	}
	else {
		$t->parse("bottom", false);
	}
	$t->pparse("main");
?>
