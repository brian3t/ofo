<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_upload.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/image_functions.php");
	include_once($root_folder_path . "messages/" . $language_code . "/download_messages.php");
	include_once("./admin_common.php");

	check_admin_security();

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_upload.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_select_href", "admin_select.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$upload_select_message = str_replace("{button_name}", UPLOAD_BUTTON, UPLOAD_SELECT_MSG);
	$t->set_var("upload_select_message", $upload_select_message);

	$errors = "";

	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$filetype = get_param("filetype");
	$image_index = get_param("image_index");
	$t->set_var("image_index", $image_index);
	$operation = get_param("operation");
	$current_index = 0;

	$layout_id = get_param("layout_id");
	$style_name = get_db_value("SELECT style_name FROM " . $table_prefix . "layouts WHERE layout_id=" . $db->tosql($layout_id, INTEGER));
	$t->set_var("layout_id", $layout_id);

	$downloads_dir = ""; $files_regexp = "";
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
		$downloads_mask = get_setting_value($download_info, "downloads_admins_mask", "");
		if ($downloads_mask) {
			$files_regexp = preg_replace("/\s/", "", $downloads_mask);
			$s = array("\\","^","\$",".","[","]","|","(",")","+","{","}");
			$r = array("\\\\","\\^","\\\$","\\.","\\[","\\]","\\|","\\(","\\)","\\+","\\{","\\}");
			$files_regexp = str_replace($s, $r, $files_regexp);
			$files_regexp = str_replace(array(",", ";", "*", "?"), array(")|(", ")|(", ".*", "."), $files_regexp);
			$files_regexp = "/^((" . $files_regexp . "))$/i";
		}
	}

	$images_root = "../images/products/";
	$tiny_dir_suffix = "tiny/";
	$small_dir_suffix = "small/";
	$big_dir_suffix = "big/";
	$large_dir_suffix = "large/";
	$super_dir_suffix = "super/";

	if ($filetype == "tiny_image") {
		$filepath = $images_root . $tiny_dir_suffix;
	} elseif ($filetype == "small_image") {
		$filepath = $images_root . $small_dir_suffix;
	} elseif ($filetype == "big_image") {
		$filepath = $images_root . $big_dir_suffix;
	} elseif ($filetype == "super_image") {
		$filepath = $images_root . $super_dir_suffix;
	} elseif ($filetype == "payment_small") {
		$filepath = $images_root . "payments/" . $small_dir_suffix;
	} elseif ($filetype == "payment_large") {
		$filepath = $images_root . "payments/" . $large_dir_suffix;
	} elseif ($filetype == "article_small") {
		$filepath = $images_root . "articles/" . $small_dir_suffix;
	} elseif ($filetype == "preview_video") {
		$filepath = $images_root . "video/preview/";
	} elseif ($filetype == "article_large") {
		$filepath = $images_root . "articles/" . $large_dir_suffix;
	} elseif ($filetype == "article_video") {
		$filepath = "../video/article/";
	} elseif ($filetype == "category" || $filetype == "category_small") {
		$filepath = $images_root . "categories/";
	} elseif ($filetype == "category_large") {
		$filepath = $images_root . "categories/" . $large_dir_suffix;
	} elseif ($filetype == "company_small" || $filetype == "company_large") {
		$filepath = $images_root . "companies/";
	} elseif ($filetype == "document") {
		$filepath = $images_root . "documents/";
	} elseif ($filetype == "ad_small") {
		$filepath = $images_root . "ads/" . $small_dir_suffix;
	} elseif ($filetype == "ad_large") {
		$filepath = $images_root . "ads/" . $large_dir_suffix;
	} elseif ($filetype == "forum_small") {
		$filepath = $images_root . "forum/" . $small_dir_suffix;
	} elseif ($filetype == "forum_large") {
		$filepath = $images_root . "forum/" . $large_dir_suffix;
	} elseif ($filetype == "banner") {
		$filepath = $images_root . "bnrs/";
	} elseif ($filetype == "personal") {
		$filepath = $images_root . "users/";
	} elseif ($filetype == "downloads") {
		$filepath = $downloads_dir;
	} elseif ($filetype == "previews") {
		$filepath = "../previews/";
	} elseif ($filetype == "preview_image") {
		$filepath = "../images/previews/";
	} elseif ($filetype == "manufacturer_small") {
		$filepath = $images_root . "manufacturers/" . $small_dir_suffix;
	} elseif ($filetype == "manufacturer_large") {
		$filepath = $images_root . "manufacturers/" . $large_dir_suffix;
	} elseif ($filetype == "icon") {
		$filepath = "../images/icons/";
	} elseif ($filetype == "emoticon") {
		$filepath = "../images/emoticons/";
	} elseif ($filetype == "language" || $filetype == "language_active") {
		$filepath = "../images/flags/";
	} elseif ($filetype == "currency" || $filetype == "currency_active") {
		$filepath = "../images/currencies/";
	} elseif ($filetype == "menu_image_active" || $filetype == "menu_image") {
		$filepath = $images_root . $style_name . "/";
	} else {
		$filepath = $images_root;
	}
	// add slash at the end of file path if it's absent
	if (substr($filepath, -1) != "/") {
		$filepath .= "/";
	}
	$t->set_var("files_dir", str_replace("../", "", $filepath));
	
	// subdir selection for upload
	$subdir_id = get_param("subdir_id");
	$subdirs    = array();
	$subdirs[0] = array("", SELECT_SUBFOLDER_MSG);
	$i = 0;
	$selected_subdir = "";
	if ($dir = @opendir($filepath)) 
	{
		while ($file = @readdir($dir)) 
		{
			if ($file != "." && $file != ".." && @is_dir($filepath . $file)) 
			{
				$i++;
				$subdirs[$i] = array($i, $file);
			} 
		}
		@closedir($dir);
	}
	if ($i > 0) {
		set_options($subdirs, $subdir_id, "subdir_id");
		if ($subdir_id && isset($subdirs[$subdir_id][1])) {
			$selected_subdir = $subdirs[$subdir_id][1];
		}
		$t->parse("subdir_id_block");
	}

	if ($operation == "1")
	{		
		//BEGIN Image Resizing changes
		$is_generate_tiny_image = get_param("is_generate_tiny_image");
		$is_generate_small_image = get_param("is_generate_small_image");
		$is_generate_big_image = get_param("is_generate_big_image");
		$sql  = "UPDATE " . $table_prefix . "admins SET ";
		$sql .= " is_generate_small_image=" . $db->tosql($is_generate_small_image, INTEGER) . ", ";
		$sql .= " is_generate_big_image=" . $db->tosql($is_generate_big_image, INTEGER);
		$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$db->query($sql);
		//END Image Resizing changes

		if (isset($_FILES)) {
			$tmp_name = $_FILES["newfile"]["tmp_name"];
			$filename = $_FILES["newfile"]["name"];
			$filesize = $_FILES["newfile"]["size"];
			$upload_error = isset($_FILES["newfile"]["error"]) ? $_FILES["newfile"]["error"] : "";
		} else {
			$tmp_name = $HTTP_POST_FILES["newfile"]["tmp_name"];
			$filename = $HTTP_POST_FILES["newfile"]["name"];
			$filesize = $HTTP_POST_FILES["newfile"]["size"];
			$upload_error = isset($HTTP_POST_FILES["newfile"]["error"]) ? $HTTP_POST_FILES["newfile"]["error"] : "";
		}
		$filename = strip($filename);

		if ($upload_error == 1) {
			$errors .= FILESIZE_DIRECTIVE_ERROR_MSG . "<br>\n";
		} elseif ($upload_error == 2) {
			$errors .= FILESIZE_PARAMETER_ERROR_MSG . "<br>\n";
		} elseif ($upload_error == 3) {
			$errors .= PARTIAL_UPLOAD_ERROR_MSG . "<br>\n";
		} elseif ($upload_error == 4) {
			$errors .= UPLOAD_SELECT_ERROR . "<br>\n";
		} elseif ($upload_error == 6) {
			$errors .= TEMPORARY_FOLDER_ERROR_MSG . ".<br>\n";
		} elseif ($upload_error == 7) {
			$errors .= FILE_WRITE_ERROR_MSG . "<br>\n";
		} elseif ($tmp_name == "none" || !strlen($tmp_name)) {
			$errors .= UPLOAD_SELECT_ERROR . "<br>\n";
		} elseif (strlen($files_regexp)) {
			if (!preg_match($files_regexp, $filename)) {
				$errors .= UPLOAD_FORMAT_ERROR . "<br>\n";
			}
		} elseif (!(preg_match("/((.gif)|(.jpg)|(.jpeg)|(.bmp)|(.tiff)|(.tif)|(.png)|(.ico)|(.doc)|(.txt)|(.rtf)|(.pdf)|(.swf)|(.flv)|(.avi)|(.asf)|(.wmv)|(.vma)|(.mpg)|(.mpeg))$/i", $filename)) ) {
			$errors .= UPLOAD_FORMAT_ERROR . "<br>\n";
		}
		if (!strlen($errors))
		{
			$check_filepaths = array();
			if ($filetype == "tiny_image" || $filetype == "small_image" || $filetype == "big_image" || $filetype == "super_image") {
				if ($selected_subdir) {
					$check_filepaths[] = $images_root .	$tiny_dir_suffix . $selected_subdir . "/";
					$check_filepaths[] = $images_root .	$small_dir_suffix . $selected_subdir . "/";
					$check_filepaths[] = $images_root .	$big_dir_suffix . $selected_subdir . "/";
					$check_filepaths[] = $images_root .	$super_dir_suffix . $selected_subdir . "/";
				} else {
					$check_filepaths[] = $images_root .	$tiny_dir_suffix;
					$check_filepaths[] = $images_root .	$small_dir_suffix;
					$check_filepaths[] = $images_root .	$big_dir_suffix;
					$check_filepaths[] = $images_root .	$super_dir_suffix;
				}
			} else {
				if ($selected_subdir) {
					$check_filepaths[] = $filepath . $selected_subdir . "/";
				} else {
					$check_filepaths[] = $filepath;
				}
			}

			$uploaded_filename = get_new_file_name($check_filepaths, $filename);

			if ($selected_subdir) {
				$uploaded_filename = $selected_subdir . "/" . $uploaded_filename;
			}
			if (!@move_uploaded_file($tmp_name, $filepath . $uploaded_filename))
			{
				if (!is_dir($filepath)) {
					$errors .= FOLDER_DOESNT_EXIST_MSG . $filepath ;
				} elseif (!is_writable($filepath)) {
					$errors .= str_replace("{folder_name}", $filepath, FOLDER_PERMISSION_MESSAGE) . "<br>\n";
				} else {
					$errors .= UPLOAD_CREATE_ERROR ." <b>" . $filepath . $uploaded_filename . "</b><br>\n";
				}
			}
			else
			{
				@chmod($filepath . $filename, 0666);
				$filename_js = str_replace("'", "\\'", $uploaded_filename);
				if ($filetype == "downloads") {
					$downloads_dir_js = str_replace("\\", "\\\\", $downloads_dir);
					$downloads_dir_js = preg_replace("/^\.\.[\/|\\\\]/", "", $downloads_dir_js);
					$filename_js = $downloads_dir_js . $filename_js;
				}				
				if ($filetype == "menu_image_active" || $filetype == "menu_image") {
					$filename_js = $style_name . "/" . $filename_js;
				}

				$t->set_var("filename", $filename);
				$t->set_var("filename_js", $filename_js);

				$uploaded_file = str_replace("{filename}", $uploaded_filename, UPLOADED_FILE_MSG);

				$t->set_var("UPLOADED_FILE_MSG", $uploaded_file);

				$t->set_var("generate_tiny", $is_generate_tiny_image);
				$t->set_var("generate_small", $is_generate_small_image);
				$t->set_var("generate_big", $is_generate_big_image);

				$resize_tiny_image = get_setting_value($settings, "resize_tiny_image", 0);
				$resize_small_image = get_setting_value($settings, "resize_small_image", 0);
				$resize_big_image = get_setting_value($settings, "resize_big_image", 0);
				$resize_super_image = get_setting_value($settings, "resize_super_image", 0);

				$gd_loaded = true;
				if ($filetype == "tiny_image" || $filetype == "small_image" || $filetype == "big_image" || $filetype == "super_image") {
					if ($is_generate_tiny_image || $resize_tiny_image) {
						$tiny_width = get_setting_value($settings, "tiny_image_max_width", 32);
						$tiny_height = get_setting_value($settings, "tiny_image_max_height", 32);
						if (@resize($uploaded_filename, $filepath, $images_root.$tiny_dir_suffix, $tiny_width, $tiny_height, $errors))	{
							@chmod($images_root . $tiny_dir_suffix . $uploaded_filename, 0666);
						}
					}
					if ($is_generate_small_image || $resize_small_image) {
						$small_width = get_setting_value($settings, "small_image_max_width", 100);
						$small_height = get_setting_value($settings, "small_image_max_height", 100);
						if (@resize($uploaded_filename, $filepath, $images_root.$small_dir_suffix, $small_width, $small_height, $errors))	{
							@chmod($images_root . $small_dir_suffix . $uploaded_filename, 0666);
						}
					}
					if ($is_generate_big_image || $resize_big_image) {
						$big_width = get_setting_value($settings, "big_image_max_width", 300);
						$big_height = get_setting_value($settings, "big_image_max_height", 300);
						if (@resize($uploaded_filename, $filepath, $images_root.$big_dir_suffix, $big_width, $big_height, $errors))	{
							@chmod($images_root.$big_dir_suffix.$uploaded_filename, 0766);
						}
					}
					if ($resize_super_image) {
						$super_width = get_setting_value($settings, "super_image_max_width", 1024);
						$super_height = get_setting_value($settings, "super_image_max_height", 768);
						if (@resize($uploaded_filename, $filepath, $images_root.$super_dir_suffix, $super_width, $super_height, $errors))	{
							@chmod($images_root.$super_dir_suffix.$uploaded_filename, 0766);
						}
					}
				}

				if ($filetype == "ad_small" || $filetype == "ad_large") {
					$ads_info = array();
					$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='ads_images'";
					if ($multisites_version) {
						$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($site_id,INTEGER). ") ";
						$sql .= "ORDER BY site_id ASC ";
					}
					$db->query($sql);
					while ($db->next_record()) {
						$ads_info[$db->f("setting_name")] = $db->f("setting_value");
					}
					$ads_small_resize = isset($ads_info["image_small_resize"]) ? $ads_info["image_small_resize"] : "";
					$ads_large_resize = isset($ads_info["image_large_resize"]) ? $ads_info["image_large_resize"] : "";

					if ($ads_info["image_small_resize"] && $filetype == "ad_small") {
						if (@resize($uploaded_filename, $filepath, $filepath, $ads_info["image_small_width"], $ads_info["image_small_height"], $errors))	{
							@chmod($images_root.$small_dir_suffix.$uploaded_filename, 0766);
						}
 					}
					if ($ads_info["image_large_resize"] && $filetype == "ad_large") {
						if (@resize($uploaded_filename, $filepath, $filepath, $ads_info["image_large_width"], $ads_info["image_large_height"], $errors))	{
							@chmod($images_root.$big_dir_suffix.$uploaded_filename, 0766);
						}
						if ($ads_info["image_small_resize"]) {
							if (@resize($uploaded_filename, $filepath, $images_root . "ads/" . $small_dir_suffix, $ads_info["image_small_width"], $ads_info["image_small_height"], $errors))	{
								@chmod($images_root . "ads/" . $small_dir_suffix.$uploaded_filename, 0766);
							}	
						}
 					}
				}

			}
		}
	}

	$t->set_var("filetype", $filetype);

	if ($operation == "1" && !strlen($errors)) {
		$t->set_var("before_upload", "");
		$t->parse("after_upload", false);
	} else {
		if ($filetype == "small_image" || $filetype == "big_image" || $filetype == "super_image") {
			$sql  = " SELECT is_generate_small_image, is_generate_big_image FROM " . $table_prefix . "admins ";
			$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$is_generate_tiny = 0;
				$is_generate_small = $db->f("is_generate_small_image");
				$is_generate_big = $db->f("is_generate_big_image");
			}
			$generate_tiny_checked = ($is_generate_tiny) ? "checked" : "";
			$generate_small_checked = ($is_generate_small) ? "checked" : "";
			$generate_big_checked = ($is_generate_big) ? "checked" : "";
			if ($filetype == "small_image" || $filetype == "big_image" || $filetype == "super_image") {
				$t->set_var("is_generate_tiny_image", $generate_tiny_checked);
				$t->parse("generate_tiny_image", false);
			}
			if ($filetype == "big_image" || $filetype == "super_image") {
				$t->set_var("is_generate_small_image", $generate_small_checked);
				$t->parse("generate_small_image", false);
			}
			if ($filetype == "super_image") {
				$t->set_var("is_generate_big_image", $generate_big_checked);
				$t->parse("generate_big_image", false);
			}
		}
		$t->parse("before_upload", false);
		$t->set_var("after_upload", "");
	}

	if (strlen($errors)) {
		$t->set_var("after_upload", "");
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	} else {
		$t->set_var("errors", "");
	}

	$t->pparse("main");

?>