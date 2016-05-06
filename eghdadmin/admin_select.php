<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_select.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "messages/" . $language_code . "/download_messages.php");
	include_once("./admin_common.php");

	check_admin_security();

	$show_preview_image = get_setting_value($settings, "show_preview_image_admin", 0);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_select.html");
	$confirm_delete = str_replace(array("{record_name}", "\'"), array(IMAGE_MSG, "\\'"), CONFIRM_DELETE_MSG);
	$t->set_var("confirm_delete", $confirm_delete);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");

	$filetype = get_param("filetype");
	$sort_dir = get_param("sort_dir");
	if (!$sort_dir) { $sort_dir = "asc"; }
	$search_image = get_param("s_im");
	$image_index = get_param("image_index");
	$operation = get_param("operation");

	$layout_id = get_param("layout_id");
	$style_name = get_db_value("SELECT style_name FROM " . $table_prefix . "layouts WHERE layout_id=" . $db->tosql($layout_id, INTEGER));
	$t->set_var("layout_id", $layout_id);

	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$downloads_dir = "";
	if ($filetype == "downloads") {
		$download_info = array();
		$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='download_info'";
		if ($multisites_version) {
			$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($site_id,INTEGER). ") ";
			$sql .= "ORDER BY site_id ASC ";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$download_info[$db->f("setting_name")] = $db->f("setting_value");
		}
		$downloads_dir = get_setting_value($download_info, "downloads_admins_dir", "../");
		if (!preg_match("/[\/\\\\]$/", $downloads_dir)) { $downloads_dir .= "/"; }
	}

	$t->set_var("s_im", $search_image);

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_select.php");
	$s->set_parameters(false, true, true, true);
	$s->set_default_sorting("1", "asc");
	$s->set_sorter(FILENAME_MSG, "sorter_filename", "1", "");

	$t->set_var("filetype", $filetype);
	$t->set_var("image_index", $image_index);
	$images_root = "../images/products";

	if ($filetype == "tiny_image") {
		$files_dir = "../images/tiny/";
	} elseif ($filetype == "small_image") {
		$files_dir = "../images/small/";
	} elseif ($filetype == "big_image") {
		$files_dir = "../images/big/";
	} elseif ($filetype == "super_image") {
		$files_dir = "../images/super/";
	} elseif ($filetype == "article_small") {
		$files_dir = "../images/articles/small/";
	} elseif ($filetype == "preview_video") {
		$files_dir = "../images/video/preview/";
	} elseif ($filetype == "article_large") {
		$files_dir = "../images/articles/large/";
	} elseif ($filetype == "category" || $filetype == "category_small") {
		$files_dir = "../images/categories/";
	} elseif ($filetype == "category_large") {
		$files_dir = "../images/categories/large/";
	} elseif ($filetype == "company_small" || $filetype == "company_large") {
		$files_dir = "../images/companies/";
	} elseif ($filetype == "ad_small") {
		$files_dir = "../images/ads/small/";
	} elseif ($filetype == "ad_large") {
		$files_dir = "../images/ads/large/";
	} elseif ($filetype == "forum_small") {
		$files_dir = "../images/forum/small/";
	} elseif ($filetype == "forum_large") {
		$files_dir = "../images/forum/large/";
	} elseif ($filetype == "banner") {
		$files_dir = "../images/bnrs/";
	} elseif ($filetype == "personal") {
		$files_dir = "../images/users/";
	} elseif ($filetype == "downloads") {
		$files_dir = $downloads_dir;
	} elseif ($filetype == "previews") {
		$files_dir = "../previews/";
	} elseif ($filetype == "preview_image") {
		$files_dir = "../images/previews/";
	} elseif ($filetype == "manufacturer_small") {
		$files_dir = "../images/manufacturers/small/";
	} elseif ($filetype == "manufacturer_large") {
		$files_dir = "../images/manufacturers/large/";
	} elseif ($filetype == "icon") {
		$files_dir = "../images/icons/";
	} elseif ($filetype == "emoticon") {
		$files_dir = "../images/emoticons/";
	} elseif ($filetype == "payment_small") {
		$files_dir = "../images/payments/small/";
	} elseif ($filetype == "payment_large") {
		$files_dir = "../images/payments/large/";
	} elseif ($filetype == "language" || $filetype == "language_active") {
		$files_dir = "../images/flags/";
	} elseif ($filetype == "currency" || $filetype == "currency_active") {
		$files_dir = "../images/currencies/";
	} elseif ($filetype == "article_video") {
		$files_dir = "../video/article/";
	} elseif ($filetype == "menu_image_active" || $filetype == "menu_image") {
		$files_dir = $images_root . $style_name . "/";
	} else {
		$files_dir = "../images/";
	}
	$t->set_var("files_dir", str_replace("../", "", $files_dir));

	$search_regexp = "";
	if (strlen($search_image)) {
		$search_regexp = preg_quote($search_image, "/");
	}
	
	// subdir selection for upload
	$subdir_id = get_param("subdir_id");
	$subdirs    = array();
	$subdirs[0] = array("", SELECT_SUBFOLDER_MSG);
	$i = 0;
	$selected_subdir = "";
	
	// check sub dirs
	if ($dir = @opendir($files_dir))
	{
		$dir_index = 0;
		while ($file = readdir($dir))
		{
			if ($file != "." && $file != "..")			
			{
				if (@is_dir($files_dir . $file)) {
					$i++;
					$subdirs[$i] = array($i, $file);
				}
			}
		}
		closedir($dir);
	}

	if ($i > 0) {
		set_options($subdirs, $subdir_id, "subdir_id");
		if ($subdir_id && isset($subdirs[$subdir_id][1])) {
			$selected_subdir = $subdirs[$subdir_id][1];
			$files_dir .= $selected_subdir . "/";
		}		
		$t->parse("subdir_id_block");
	}

	if ($operation == "delete") {
		$file = get_param("file");
		if (strlen($file)) {	
			// try to delete file
			$file_deleted = @unlink($files_dir.$file);
		}
	}
	
	$dir_values = array();
	if ($dir = @opendir($files_dir))
	{
		$dir_index = 0;
		while ($file = readdir($dir))
		{
			if ($file != "." && $file != ".." && @is_file($files_dir . $file)) {
				if (preg_match("/" . $search_regexp . "/i", $file)) {
					$dir_values[$dir_index] = $file;
					$dir_index++;
				}
			}
		}
		closedir($dir);
	}

	if ($sort_dir == "desc") {
		array_multisort($dir_values, SORT_DESC);
	} else {
		array_multisort($dir_values, SORT_ASC);
	}

	$total_files = sizeof($dir_values);

	// set up variables for navigator
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_select.php");
	$records_per_page = 10;
	$pages_number = 10;
	$page_number = $n->set_navigator("navigator", "page", MOVING, $pages_number, $records_per_page, $total_files, false);

	if ($total_files)
	{
		$image_delete_url = new VA_URL("admin_select.php", false);
		$image_delete_url->add_parameter("operation", CONSTANT, "delete");
		$image_delete_url->add_parameter("filetype", CONSTANT, $filetype);
		$image_delete_url->add_parameter("subdir_id", CONSTANT, $subdir_id);
		$image_delete_url->add_parameter("image_index", CONSTANT, $image_index);
		$image_delete_url->add_parameter("s_im", REQUEST, "s_im");
		$image_delete_url->add_parameter("page", REQUEST, "page");
		$image_delete_url->add_parameter("sort_dir", REQUEST, "sort_dir");

		$firt_index = ($page_number - 1) * $records_per_page;
		$last_index = $page_number * $records_per_page;
		if ($last_index > $total_files) { $last_index = $total_files; }
		for ($i = $firt_index; $i < $last_index; $i++)
		{
			$image_name = $dir_values[$i];
			if (strval($search_regexp) == "") {
				$image_name_html = $image_name;
			} else {
				$image_name_html = preg_replace ("/(" . $search_regexp . ")/i", "<font color=blue><b>\\1</b></font>", $image_name);
			}
			$image_name_js = str_replace("'", "\\'", $image_name);
			if ($filetype == "downloads") {
				$downloads_dir_js = str_replace("\\", "\\\\", $downloads_dir);
				$downloads_dir_js = preg_replace("/^\.\.[\/|\\\\]/", "", $downloads_dir_js);
				if ($selected_subdir) {
					$image_name_js = $downloads_dir_js . $selected_subdir . "/" . $image_name_js;
				} else {
					$image_name_js = $downloads_dir_js . $image_name_js;
				}
			}	else if ($filetype == "menu_image_active" || $filetype == "menu_image") {
				$image_name_js = $style_name . "/" . $image_name_js;
			} else if ($selected_subdir) {
				$image_name_js = $selected_subdir . "/" . $image_name_js;
 			}

			if (preg_match("/((.flv)|(.avi)|(.asf)|(.wmv)|(.vma)|(.mpg)|(.mpeg))$/i", $image_name)){
				$t->set_var("image_href", "admin_video.php?file=" . $files_dir . $dir_values[$i]);
			} else {
				$t->set_var("image_href", $files_dir . $dir_values[$i]);
			}
			$image_delete_url->add_parameter("file", CONSTANT, $dir_values[$i]);
			$t->set_var("image_delete_url", $image_delete_url->get_url());

			$t->set_var("image_name", $image_name);
			$t->set_var("image_name_html", $image_name_html);
			$t->set_var("image_name_js", $image_name_js);
			$t->set_var("image_id", $i);

			if ($show_preview_image == 1){
			  $t->parse("image_row", true);
			} else {
			  $t->parse("image_row_no_preview", true);
			}
		}

		$t->set_var("no_images", "");
		$t->parse("images", false);
	} else {
		$t->parse("no_images", false);
		$t->set_var("images", "");
	}

	if (!$total_files && !strlen($search_image)) {
		$t->set_var("search_images", "");
	} else {
		$t->parse("search_images", false);
	}

	$t->pparse("main");

?>