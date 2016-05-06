<?php

	$is_admin_path = true;
	include("../includes/common.php");
	include_once ("../includes/sorter.php");
	include_once("../messages/".$language_code."/download_messages.php");
	include_once("../messages/".$language_code."/messages.php");

	$t = new VA_Template("./");
	$t->set_file("main","editor_select.html");

	if ((get_session("session_user_id") == '') && (get_session("session_admin_id") == '')) {
		$t->set_var("search_images", "");
		$t->set_var("images", "");
		$t->set_var("no_images", "");
		$t->set_var("errors_list", REGISTERED_ACCESS_MSG);
		$t->parse("errors", false);
		$t->pparse("main");
		exit;
	}

	$t->set_var("admin_select_href", "editor_select.php");

	$filetype = get_param("filetype");
	$t->set_var("filetype", htmlspecialchars($filetype));

	$sort_dir = get_param("sort_dir");
	if (!$sort_dir) { $sort_dir = "asc"; }
	$search_image = get_param("s_im");
	$root_dir = terminator(get_param("root_dir"));
	if(strlen($root_dir) == 0) {
		$root_dir = "../images";
	}
	if ($filetype == "product_editor") {
		$root_dir = "../images/products/editor";
	} else if ($filetype == "article_editor") {
		$root_dir = "../images/articles/editor";
	} else if ($filetype == "ad_editor") {
		$root_dir = "../images/ads/editor/";
	} else if ($filetype == "forum_editor") {
		$root_dir = "../images/forum/editor";
	} else if ($filetype == "user_editor") {
		$root_dir = "../images/users/editor";
	}

	$path_root_dir = explode('/', $root_dir);
	if (sizeof($path_root_dir) < 2){
		$root_dir = "../images";
	}else{
		if ($path_root_dir[0] != '..' || $path_root_dir[1] != 'images') {
			$root_dir = "../images";
		}
	}
	$back_dir = '..';
	for($i = 0; $i < sizeof($path_root_dir)-1; $i++)
	{
		if ($i > 0 && $i < sizeof($path_root_dir)-1) {
			$back_dir = $back_dir.'/'.$path_root_dir[$i];
		}
		if ($path_root_dir[$i] == ".." && $i != 0) {
			$root_dir = '../images';
			$back_dir = '../images';
			break;
		}
	}
	$path_root_dir = explode('/', $root_dir);
	$image_path = '';
	for($i = 1; $i < sizeof($path_root_dir); $i++)
	{
		$image_path = strlen($image_path) ? $image_path.'/'.$path_root_dir[$i] : $path_root_dir[$i];
	}

	if ((get_session("session_user_id") != '') && (get_session("session_admin_id") == '')) {
		$sql  = " SELECT file_path FROM " . $table_prefix . "users_files ";
		$sql .= " WHERE user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
		$sql .= " AND file_type=" . $db->tosql($filetype, TEXT) . " ";
		$db->query($sql);
		$user_files = array();
		while ($db->next_record()) {
			$user_files[] = $db->f("file_path");
		}
	}
	
	$t->set_var("s_im", $search_image);

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "editor_select.php");
	$s->set_parameters(false, true, true, true);
	$s->set_default_sorting("1", "asc");
	$s->set_sorter("Image Filename", "image_filename", "1", "");

	$files_dir = $root_dir.'/';

	$dir_name_values = array();
	$dir_values = array();
	if ($dir = @opendir($files_dir)) 
	{
		if ((get_session("session_user_id") == '') && (get_session("session_admin_id") != '')){
			$dir_index = 0;
			while($file = readdir($dir)) 
			{
				if ($file != "." && is_dir($files_dir . $file)) 
				{
					if ($root_dir == '../images' && $file == "..") {
					}else{
						$dir_name_values[$dir_index] = $file;
						$dir_index++;
					}
				}
			}
			closedir($dir);
		}
		$dir = @opendir($files_dir);
		$dir_index = 0;
		while($file = readdir($dir)) 
		{
			if ($file != "." && $file != ".." && is_file($files_dir . $file))
			{
				if ((get_session("session_user_id") != '') && (get_session("session_admin_id") == '')){
					if ( isset($user_files) && in_array($image_path.'/'.$file, $user_files)) {
						$dir_values[$dir_index] = $file;
						$dir_index++;
					}
				}else{
					$dir_values[$dir_index] = $file;
					$dir_index++;
				}
			}
		}
		closedir($dir);
	}

	if ($sort_dir == "desc") {
		array_multisort($dir_name_values, SORT_DESC);
		array_multisort($dir_values, SORT_DESC);
	}
	if ($sort_dir == "asc") {
		array_multisort($dir_name_values, SORT_ASC);
		array_multisort($dir_values, SORT_ASC);
	}

	if (strlen($search_image)) {
		$found_image=array();
		for($i = 0; $i < sizeof($dir_name_values); $i++)
		{
			if (strlen($search_image) <= strlen($dir_name_values[$i])) {
				if (preg_match("/".$search_image."/i", $dir_name_values[$i])) {
					$found_image[]=$dir_name_values[$i];
				}
			}
		}
		$dir_name_values = $found_image;
		$found_image=array();
		for($i = 0; $i < sizeof($dir_values); $i++)
		{
			if (strlen($search_image) <= strlen($dir_values[$i])) {
				if (preg_match("/".$search_image."/i", $dir_values[$i])) {
					$found_image[]=$dir_values[$i];
				}
			}
		}
		$dir_values = $found_image;
	}

	$dir_number = isset($dir_name_values) ? sizeof($dir_name_values) : 0;

	if($dir_number)
	{
		for($i = 0; $i < sizeof($dir_name_values); $i++)
		{
			$dir_name = $dir_name_values[$i];
			if ($dir_name_values[$i] == '..') {
				$t->set_var("dir_name_href", "editor_select.php?root_dir=".$back_dir);
			}else{
				$t->set_var("dir_name_href", "editor_select.php?root_dir=".$root_dir.'/'.$dir_name);
			}
			$t->set_var("dir_name", $dir_name);
			$t->parse("dir_row", true);
		}

		$t->parse("images", false);
	}

	$image_number = isset($dir_values) ? sizeof($dir_values) : 0;
	
	if($image_number)
	{
		for($i = 0; $i < sizeof($dir_values); $i++)
		{
			$image_name = $dir_values[$i];
			$t->set_var("image_href", $files_dir . $dir_values[$i]);
			$t->set_var("image_name", $image_name);
			$t->set_var("filename", $settings["site_url"].$image_path.'/'.$image_name);
			$image_params = @getimagesize($files_dir . $dir_values[$i]);
			$t->set_var("image_width", $image_params[0]);
			$t->set_var("image_height", $image_params[1]);
			$t->parse("image_row", true);
		}

		$t->parse("images", false);
	}

	if ($dir_number == 1 && !$image_number) {	
		$t->parse("no_images", false);
	} else {
		$t->set_var("no_images", "");
	}

	if ($dir_number == 1 && !$image_number && !strlen($search_image)) {	
		$t->set_var("search_images", "");
	} else {
		$t->parse("search_images", false);
	}

	$t->pparse("main");

function terminator($string) {
	return trim(strip_tags($string));
}

?>