<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  user_upload.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	include_once("./includes/common.php");
	include_once("./messages/" . $language_code . "/download_messages.php");
	include_once("./includes/image_functions.php");

	define("SELECT_SUBFOLDER_MSG", "Select Subfolder");
	define("CURRENT_DIR_MSG", "Current directory");
	
	$filetype = get_param("filetype");
	$display_products = get_setting_value($settings, "display_products", 0);
	if ($display_products == 1 || $display_products == 2 || $filetype != "option_image") {
		check_user_session();
	}

	
	$product_settings  = get_settings("user_product_" .  get_session("session_user_type_id"));
	$can_select_folder = get_setting_value($product_settings, "can_select_folder", 0);
	$uploads_subfolder = get_setting_value($product_settings, "uploads_subfolder", "");
		
	$image_tiny_generated = 0;
	$image_small_generated = 0;
	$image_large_generated = 0;

	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main", "user_upload.html");
	$t->set_var("user_upload_href", "user_upload.php");
	$upload_msg = str_replace("{button_name}", UPLOAD_BUTTON, UPLOAD_SELECT_MSG);
	$t->set_var("UPLOAD_SELECT_MSG", $upload_msg);
	$css_file = "";
	if (isset($settings["style_name"]) && $settings["style_name"]) {
		$css_file = "styles/" . $settings["style_name"];
		if (isset($settings["scheme_name"]) && $settings["scheme_name"]) {
			$css_file .= "_" . $settings["scheme_name"];
		}
		$css_file .= ".css";
	}
	$t->set_var("css_file", $css_file);

	$user_id = get_session("session_user_id");
	$type = get_session("session_user_type_id");
	$fid = get_param("fid");
	$control_name = get_param("control_name");
	$operation = get_param("operation");
	$is_generate_tiny_image = get_param("is_generate_tiny_image");
	$is_generate_small_image = get_param("is_generate_small_image");
	$is_generate_big_image = get_param("is_generate_big_image");

	if (!strlen($type)) {
		$sql  = " SELECT ut.type_id ";
		if (isset($site_id)) {
			$sql .= " FROM (" . $table_prefix . "user_types ut ";
			$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites s ON s.type_id=ut.type_id) ";
			$sql .= " WHERE ut.is_default=1 AND (ut.sites_all OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		} else {
			$sql .= " FROM " . $table_prefix . "user_types ut ";
			$sql .= " WHERE ut.is_default=1 AND ut.sites_all";
		}
		$type = get_db_value($sql);
	}

	$downloads_dir = ""; $files_regexp = "";
	if ($filetype == "downloads" || $filetype == "previews") {
		$download_info = array();
		$sql = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings ";
		$sql.= " WHERE setting_type='download_info'";
		if (isset($site_id)) {
			$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
			$sql .= " ORDER BY site_id ASC ";
		} else {
			$sql .= " AND site_id=1 ";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$download_info[$db->f("setting_name")] = $db->f("setting_value");
		}
		$downloads_dir = get_setting_value($download_info, "downloads_users_dir", "./");
		if (!preg_match("/\/|\\$/", $downloads_dir)) { $downloads_dir .= "/"; }
		$downloads_mask = get_setting_value($download_info, "downloads_users_mask", "");
		if ($downloads_mask) {
			$files_regexp = preg_replace("/\s/", "", $downloads_mask);
			$s = array("\\","^","\$",".","[","]","|","(",")","+","{","}");
			$r = array("\\\\","\\^","\\\$","\\.","\\[","\\]","\\|","\\(","\\)","\\+","\\{","\\}");
			$files_regexp = str_replace($s, $r, $files_regexp);
			$files_regexp = str_replace(array(",", ";", "*", "?"), array(")|(", ")|(", ".*", "."), $files_regexp);
			$files_regexp = "/^((" . $files_regexp . "))$/i";
		}
	}
	
	$product_tiny_path = "./images/products/tiny/";
	$product_small_path = "./images/products/small/";
	$product_large_path = "./images/products/large/";
	$product_super_path = "./images/products/super/";
	if ($filetype == "article_small") {
		$filepath = "./images/articles/small/";
	} elseif ($filetype == "article_large") {
		$filepath = "./images/articles/large/";
	} elseif ($filetype == "ad_small") {
		$filepath = "./images/ads/small/";
	} elseif ($filetype == "ad_large") {
		$filepath = "./images/ads/large/";
	} elseif ($filetype == "personal_image") {
		$filepath = "./images/users/";
	} elseif ($filetype == "option_image") {
		$filepath = "./images/options/";
	} elseif ($filetype == "product_tiny") {
		$filepath = $product_tiny_path;
	} elseif ($filetype == "product_small") {
		$filepath = $product_small_path;
	} elseif ($filetype == "product_large") {
		$filepath = $product_large_path;
	} elseif ($filetype == "product_super") {
		$filepath = $product_super_path;
	} elseif ($filetype == "downloads") {
		$filepath = $downloads_dir;
	} elseif ($filetype == "previews") {
		$filepath = "./previews/";
	} elseif ($filetype == "preview_image") {
		$filepath = "./images/previews/";
	} else {
		echo "Incorrect file type: ". $filetype;
		exit;
	}
	if ($uploads_subfolder) {
		$filepath .= $uploads_subfolder . "/";
		if (!@is_dir($filepath) && !@is_dir(dirname(__FILE__) . "/" . $filepath)) {
			@mkdir($filepath);
			@chmod($filepath, 0755);
		}
	}
	if ($can_select_folder) {
		$t->set_var("files_dir", str_replace("./", "", $filepath));		
		// subdir selection for upload
		$subdir_id = get_param("subdir_id");
		$subdirs    = array();
		$subdirs[0] = array("", SELECT_SUBFOLDER_MSG);
		$i = 0;
		$selected_subdir = "";
		
		// check sub dirs
		if ($dir = @opendir($filepath))
		{
			$dir_index = 0;
			while ($file = readdir($dir))
			{
				if ($file != "." && $file != "..")			
				{
					if (@is_dir($filepath . $file)) {
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
				$filepath .= $selected_subdir . "/";
			}		
			$t->parse("subdir_id_block");
		}
		$t->parse("can_select_folder");
	}
	
	$errors = "";

	if ($operation == "1") {
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

		if ($upload_error == 1) {
			$errors = "The uploaded file exceeds the max filesize directive.";
		} elseif ($upload_error == 2) {
			$errors = "The uploaded file exceeds the max filesize parameter.";
		} elseif ($upload_error == 3) {
			$errors = "The uploaded file was only partially uploaded.";
		} elseif ($upload_error == 4) {
			$errors = UPLOAD_SELECT_ERROR;
		} elseif ($upload_error == 6) {
			$errors = "Missing a temporary folder.";
		} elseif ($upload_error == 7) {
			$errors = "Failed to write file to disk.";
		} elseif ($tmp_name == "none" || !strlen($tmp_name)) {
			$errors = UPLOAD_SELECT_ERROR;
		} elseif (strlen($files_regexp)) {
			if (!preg_match($files_regexp, $filename)) {
				$errors = UPLOAD_FORMAT_ERROR;
			}
		} elseif (!(preg_match("/((.gif)|(.jpg)|(.jpeg)|(.bmp)|(.tiff)|(.tif)|(.png)|(.ico)|(.doc)|(.txt)|(.flv)|(.rtf)|(.pdf))$/i", $filename)) ) {
			$errors = UPLOAD_FORMAT_ERROR;
		}

		$ads_images = array();
		if (!strlen($errors) && ($filetype == "ad_small" || $filetype == "ad_large")) {
			$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type=" . $db->tosql("ads_images", TEXT);
			if (isset($site_id)) {
				$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
				$sql .= " ORDER BY site_id ASC ";
			} else {
				$sql .= " AND site_id=1 ";
			}
			$db->query($sql);
			while ($db->next_record()) {
				$ads_images[$db->f("setting_name")] = $db->f("setting_value");
			}
		}

		if (!strlen($errors))
		{
			$check_filepaths = array();
			if ($filetype == "product_tiny" || $filetype == "product_small" || $filetype == "product_large" || $filetype == "product_super") {
				$check_filepaths[] = $product_tiny_path;
				$check_filepaths[] = $product_small_path;
				$check_filepaths[] = $product_large_path;
				$check_filepaths[] = $product_super_path;
			} else {
				$check_filepaths[] = $filepath;
			}

			$new_filename = get_new_file_name ($check_filepaths, $filename);

			if (!move_uploaded_file($tmp_name, $filepath . $new_filename))
			{
				$errors = UPLOAD_CREATE_ERROR;
			}
			else
			{
				// change permissions to uploaded file
				@chmod($filepath . $new_filename, 0766);


				if ($filetype == "personal_image") {
					$user_profile = array();
					$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
					$sql .= " WHERE setting_type=" . $db->tosql("user_profile_" . $type, TEXT) . " AND setting_name LIKE 'personal_image_%'";
					if (isset($site_id)) {
						$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
						$sql .= " ORDER BY site_id ASC ";
					} else {
						$sql .= " AND site_id=1 ";
					}
					$db->query($sql);
					while ($db->next_record()) {
						$user_profile[$db->f("setting_name")] = $db->f("setting_value");
					}
					$max_image_size = get_setting_value($user_profile, "personal_image_size", 16384);
					$max_image_width = get_setting_value($user_profile, "personal_image_width", 100);
					$max_image_height = get_setting_value($user_profile, "personal_image_height", 100);
					$personal_image_resize = get_setting_value($user_profile, "personal_image_resize", 0);
					
					$image_params = @getimagesize($filepath . $new_filename);
					if (($image_params[0] > $max_image_width || $image_params[1] > $max_image_height) && $personal_image_resize) {
						if (resize($new_filename, $filepath, $filepath, $max_image_width, $max_image_height, $errors))	{
							@chmod($filepath . $new_filename, 0766);
						}
					}

					if (!$errors) {
						if (filesize($filepath . $new_filename) > $max_image_size) {
							$errors = str_replace("{filesize}", intval($max_image_size / 1024) . "kb", UPLOAD_SIZE_ERROR);
						} else {
							$image_params = @getimagesize($filepath . $new_filename);
							if($image_params[0] > $max_image_width || $image_params[1] > $max_image_height) {
								$errors = str_replace("{dimension}", $max_image_width . "x" . $max_image_height, UPLOAD_DIMENSION_ERROR);
							}
						}
					}
				}

				// resize and generate ads images
				if ($filetype == "ad_small" || $filetype == "ad_large") {
					$image_small_resize = get_setting_value($ads_images, "image_small_resize", 0);
					$image_small_size = get_setting_value($ads_images, "image_small_size", 16384);
					$image_small_width = get_setting_value($ads_images, "image_small_width", 100);
					$image_small_height = get_setting_value($ads_images, "image_small_height", 100);

					$image_large_resize = get_setting_value($ads_images, "image_large_resize", 0);
					$image_large_size = get_setting_value($ads_images, "image_large_size", 204800);
					$image_large_width = get_setting_value($ads_images, "image_large_width", 800);
					$image_large_height = get_setting_value($ads_images, "image_large_height", 600);

					if ($image_small_resize && $filetype == "ad_small") {
						if (resize($new_filename, $filepath, $filepath, $image_small_width, $image_small_height, $errors))	{
							@chmod($filepath . $new_filename, 0766);
						}
					}
					if (!$errors && $filetype == "ad_small") {
						if (filesize($filepath . $new_filename) > $image_small_size) {
							$errors = str_replace("{filesize}", intval($image_small_size / 1024) . "kb", UPLOAD_SIZE_ERROR);
						} else {
							$image_params = @getimagesize($filepath . $new_filename);
							if($image_params[0] > $image_small_width || $image_params[1] > $image_small_height) {
								$errors = str_replace("{dimension}", $image_small_width. "x" . $image_small_height, UPLOAD_DIMENSION_ERROR);
							}
						}
					}

					if ($image_large_resize && $filetype == "ad_large") {
						if (resize($new_filename, $filepath, $filepath, $image_large_width, $image_large_height, $errors))	{
							@chmod($filepath . $new_filename, 0766);
						}
					}
					if (!$errors && $filetype == "ad_large") {
						if (filesize($filepath . $new_filename) > $image_large_size) {
							$errors = str_replace("{filesize}", intval($image_large_size / 1024) . "kb", UPLOAD_SIZE_ERROR);
						} else {
							$image_params = @getimagesize($filepath . $new_filename);
							if($image_params[0] > $image_large_width || $image_params[1] > $image_large_height) {
								$errors = str_replace("{dimension}", $image_large_width. "x" . $image_large_height, UPLOAD_DIMENSION_ERROR);
							}
						}
					}
				}

				// resize and generate product images
				if ($filetype == "product_tiny" || $filetype == "product_small"
					|| $filetype == "product_large" || $filetype == "product_super")
				{
					$resize_tiny_image = get_setting_value($settings, "user_resize_tiny_image", 0);
					$resize_small_image = get_setting_value($settings, "user_resize_small_image", 0);
					$resize_large_image = get_setting_value($settings, "user_resize_large_image", 0);
					$resize_super_image = get_setting_value($settings, "user_resize_super_image", 0);

					$generate_tiny_image = get_setting_value($settings, "user_generate_tiny_image", 0);
					$generate_small_image = get_setting_value($settings, "user_generate_small_image", 0);
					$generate_large_image = get_setting_value($settings, "user_generate_large_image", 0);

					$image_tiny_exists = get_param("image_tiny_exists");
					$image_small_exists = get_param("image_small_exists");
					$image_large_exists = get_param("image_large_exists");

					// product tiny image
					$tiny_image_width = get_setting_value($settings, "user_tiny_image_width", 64);
					$tiny_image_height= get_setting_value($settings, "user_tiny_image_height", 64);
					$tiny_image_size  = get_setting_value($settings, "user_tiny_image_size", 8192);
					if (($resize_tiny_image && $filetype == "product_tiny") || ($generate_tiny_image && !$image_tiny_exists)) {
						if (resize($new_filename, $filepath, $product_tiny_path, $tiny_image_width, $tiny_image_height, $errors))	{
							@chmod($product_tiny_path . $new_filename, 0766);
						}
						if (!$errors && $filetype != "product_tiny") {
							$image_tiny_generated = 1;
						}
					}
					if (!$errors && ($filetype == "product_tiny" || $image_tiny_generated))  {
						if (filesize($product_tiny_path . $new_filename) > $tiny_image_size) {
							$errors = str_replace("{filesize}", intval($tiny_image_size / 1024) . "kb", UPLOAD_SIZE_ERROR);
						} else {
							$image_params = @getimagesize($product_tiny_path . $new_filename);
							if($image_params[0] > $tiny_image_width || $image_params[1] > $tiny_image_height) {
								$errors = str_replace("{dimension}", $tiny_image_width . "x" . $tiny_image_height, UPLOAD_DIMENSION_ERROR);
							}
						}
					}

					// product small image
					$small_image_width = get_setting_value($settings, "user_small_image_width", 128);
					$small_image_height= get_setting_value($settings, "user_small_image_height", 128);
					$small_image_size  = get_setting_value($settings, "user_small_image_size", 16384);
					if (($resize_small_image && $filetype == "product_small") || ($generate_small_image && !$image_small_exists)) {
						if (resize($new_filename, $filepath, $product_small_path, $small_image_width, $small_image_height, $errors))	{
							@chmod($product_small_path . $new_filename, 0766);
						}
						if (!$errors && $filetype != "product_small") {
							$image_small_generated = 1;
						}
					}
					if (!$errors && ($filetype == "product_small" || $image_small_generated))  {
						if (filesize($product_small_path . $new_filename) > $small_image_size) {
							$errors = str_replace("{filesize}", intval($small_image_size / 1024) . "kb", UPLOAD_SIZE_ERROR);
						} else {
							$image_params = @getimagesize($product_small_path . $new_filename);
							if($image_params[0] > $small_image_width || $image_params[1] > $small_image_height) {
								$errors = str_replace("{dimension}", $small_image_width . "x" . $small_image_height, UPLOAD_DIMENSION_ERROR);
							}
						}
					}

					// product large image
					$large_image_width = get_setting_value($settings, "user_large_image_width", 300);
					$large_image_height= get_setting_value($settings, "user_large_image_height", 300);
					$large_image_size  = get_setting_value($settings, "user_large_image_size", 65536);
					if (!$errors && (($resize_large_image && $filetype == "product_large")|| ($generate_large_image && !$image_large_exists))) {
						if(resize($new_filename, $filepath, $product_large_path, $large_image_width, $large_image_height, $errors))	{
							@chmod($product_large_path . $new_filename, 0766);
						}
						if (!$errors && $filetype != "product_large") {
							$image_large_generated = 1;
						}
					}
					if (!$errors && ($filetype == "product_large" || $image_large_generated))  {
						if (filesize($product_large_path . $new_filename) > $large_image_size) {
							$errors = str_replace("{filesize}", intval($large_image_size / 1024) . "kb", UPLOAD_SIZE_ERROR);
						} else {
							$image_params = @getimagesize($product_large_path . $new_filename);
							if($image_params[0] > $large_image_width || $image_params[1] > $large_image_height) {
								$errors = str_replace("{dimension}", $large_image_width . "x" . $large_image_height, UPLOAD_DIMENSION_ERROR);
							}
						}
					}

					// product super image
					$super_image_width = get_setting_value($settings, "user_super_image_width", 1024);
					$super_image_height= get_setting_value($settings, "user_super_image_height", 768);
					$super_image_size  = get_setting_value($settings, "user_super_image_size", 196608);
					if (!$errors && $resize_super_image) {
						if(resize($new_filename, $filepath, $product_super_path, $super_image_width, $super_image_height, $errors))	{
							@chmod($product_super_path.$new_filename, 0766);
						}
					}
					if (!$errors && $filetype == "product_super")  {
						if (filesize($product_super_path . $new_filename) > $super_image_size) {
							$errors = str_replace("{filesize}", intval($super_image_size / 1024) . "kb", UPLOAD_SIZE_ERROR);
						} else {
							$image_params = @getimagesize($product_super_path . $new_filename);
							if($image_params[0] > $super_image_width || $image_params[1] > $super_image_height) {
								$errors = str_replace("{dimension}", $super_image_width . "x" . $super_image_height, UPLOAD_DIMENSION_ERROR);
							}
						}
					}

					// in case of errors delete all generated files
					if ($errors && $image_tiny_generated && file_exists($product_tiny_path . $new_filename)) {
						@unlink($product_tiny_path . $new_filename);
					}
					if ($errors && $image_small_generated && file_exists($product_small_path . $new_filename)) {
						@unlink($product_small_path . $new_filename);
					}
					if ($errors && $image_large_generated && file_exists($product_large_path . $new_filename)) {
						@unlink($product_large_path . $new_filename);
					}
				}


				if (strlen($errors)) {
					// delete uploaded file in case of errors
					if (file_exists($filepath . $new_filename)) {
						@unlink($filepath . $new_filename);
					}
				} else {
					$user_path = preg_replace("/^\.\//", "", $filepath);
					$filename_js = str_replace("'", "\\'", $new_filename);
					if ($filetype != "option_image") {
						$filename_js = $user_path . $filename_js;
					}
					$t->set_var("filename", 	$new_filename);
					$t->set_var("filename_js", $filename_js);

					$uploaded_msg = str_replace("{filename}", $new_filename, UPLOADED_FILE_MSG);
					$t->set_var("UPLOADED_FILE_MSG", $uploaded_msg);

					if (get_session("session_user_id")) {
						$sql  = " INSERT INTO " . $table_prefix . "users_files (user_id, file_type, file_name, file_path) VALUES (";
						$sql .= $db->tosql(get_session("session_user_id"), INTEGER) . ", ";
						$sql .= $db->tosql($filetype, TEXT) . ", ";
						$sql .= $db->tosql($filename, TEXT) . ", ";
						$sql .= $db->tosql($user_path . $new_filename, TEXT) . ") ";
						$db->query($sql);
						if ($image_tiny_generated) {
							$user_path = preg_replace("/^\.\//", "", $product_tiny_path);
							$sql  = " INSERT INTO " . $table_prefix . "users_files (user_id, file_type, file_name, file_path) VALUES (";
							$sql .= $db->tosql(get_session("session_user_id"), INTEGER) . ", ";
							$sql .= $db->tosql("product_tiny", TEXT) . ", ";
							$sql .= $db->tosql($filename, TEXT) . ", ";
							$sql .= $db->tosql($user_path . $new_filename, TEXT) . ") ";
							$db->query($sql);
						}
						if ($image_small_generated) {
							$user_path = preg_replace("/^\.\//", "", $product_small_path);
							$sql  = " INSERT INTO " . $table_prefix . "users_files (user_id, file_type, file_name, file_path) VALUES (";
							$sql .= $db->tosql(get_session("session_user_id"), INTEGER) . ", ";
							$sql .= $db->tosql("product_small", TEXT) . ", ";
							$sql .= $db->tosql($filename, TEXT) . ", ";
							$sql .= $db->tosql($user_path . $new_filename, TEXT) . ") ";
							$db->query($sql);
						}
						if ($image_large_generated) {
							$user_path = preg_replace("/^\.\//", "", $product_large_path);
							$sql  = " INSERT INTO " . $table_prefix . "users_files (user_id, file_type, file_name, file_path) VALUES (";
							$sql .= $db->tosql(get_session("session_user_id"), INTEGER) . ", ";
							$sql .= $db->tosql("product_large", TEXT) . ", ";
							$sql .= $db->tosql($filename, TEXT) . ", ";
							$sql .= $db->tosql($user_path . $new_filename, TEXT) . ") ";
							$db->query($sql);
						}
					}
				}
			}
		} else {
			if (strlen($tmp_name)) {
				@unlink($tmp_name);
			}
		}
	}

	if (strlen($errors))
	{
		$t->set_var("after_upload", "");
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	} else {
		$t->set_var("errors", "");
	}

	$t->set_var("filetype", htmlspecialchars($filetype));
	$t->set_var("type", htmlspecialchars($type));
	$t->set_var("fid", htmlspecialchars($fid));
	$t->set_var("control_name", htmlspecialchars($control_name));
	$t->set_var("image_tiny_generated", $image_tiny_generated);
	$t->set_var("image_small_generated", $image_small_generated);
	$t->set_var("image_large_generated", $image_large_generated);


	if ($operation == "1" && !strlen($errors))
	{
		$t->set_var("before_upload", "");
		$t->parse("after_upload", false);
	} else {
		$t->parse("before_upload", false);
		$t->set_var("after_upload", "");
	}

	$t->pparse("main");

?>