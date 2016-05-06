<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  download.php                                             ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");
	include_once("./includes/products_functions.php");
	include_once("./messages/" . $language_code . "/download_messages.php");

	$download_id = get_param("download_id");
	$release_id = get_param("release_id");
	$path_id = get_param("path_id");
	$order_item_id = get_param("order_item_id");
	$vc_parameter = get_param("vc");
	$terms_agreed = get_param("terms_agreed");
	$user_id = get_session("session_user_id");
	$operation = get_param("operation");
	$download_show_terms = 0; $download_terms_text = "";
	if (!$path_id) { $path_id = 1; }

	$errors = "";
	$user_type_id = get_session("session_user_type_id");

	if ($release_id) {
		$sql  = " SELECT download_type, path_to_file ";
		$sql .= " FROM " . $table_prefix . "releases ";
		$sql .= " WHERE release_id=". $db->tosql($release_id, INTEGER);
		$sql .= " AND is_showing=1 ";
		$db->query($sql);
		if ($db->next_record()) {
			$download_type = $db->f("download_type");
			$path_to_file  = $db->f("path_to_file");
			$paths = split(";", $path_to_file);
			if ($path_id <= sizeof($paths)) {
				$download_path = trim($paths[$path_id - 1]);
			} 
			if ($path_id > sizeof($paths)) {
				$errors = DOWNLOAD_WRONG_PARAM;
			} elseif ($download_type == 1) {
				$order_item_id = "";
			} elseif ($download_type == 2 && !$user_id) {
				$order_item_id = "";
				$errors = DOWNLOAD_USER_ERROR;
			} elseif ($download_type == 3) {
				if (!$user_id) {
					$errors = DOWNLOAD_USER_ERROR;
				} elseif (!$order_item_id) {
					$errors = DOWNLOAD_MISS_PARAM;
				} else {
					$sql  = " SELECT i.item_id, i.download_period, o.order_status, o.order_placed_date, ";
					$sql .= " i.download_show_terms, i.download_terms_text ";
					$sql .= " FROM " . $table_prefix . "items i, " . $table_prefix . "orders_items oi, " . $table_prefix . "orders o ";
					$sql .= " WHERE oi.item_id=i.item_id ";
					$sql .= " AND o.order_id=oi.order_id ";
					$sql .= " AND oi.order_item_id=" . $db->tosql($order_item_id, INTEGER);
					$sql .= " AND o.user_id=" . $db->tosql($user_id, INTEGER);
					if (isset($site_id)) {
						$sql .= " AND o.site_id=" . $db->tosql($site_id, INTEGER, true, false);
					} else {
						$sql .= " AND o.site_id=1";					
					}
					$db->query($sql);
					if ($db->next_record()) {
						$download_period = $db->f("download_period");
						$download_show_terms = $db->f("download_show_terms");
						$download_terms_text = $db->f("download_terms_text");
						$order_status = $db->f("order_status");
						$order_placed_date = $db->f("order_placed_date", DATETIME);
						$current_date = va_time();
						$current_date_ts = mktime (0,0,0, $current_date[MONTH], $current_date[DAY], $current_date[YEAR]);
						if ($download_period) {
							$expiry_date_ts = mktime (0,0,0, $order_placed_date[MONTH], $order_placed_date[DAY] + $download_period, $order_placed_date[YEAR]);
						} else {
							$expiry_date_ts = $current_date_ts;
						}
						$sql  = " SELECT download_activation FROM " . $table_prefix . "order_statuses ";
						$sql .= " WHERE status_id=" . $db->tosql($order_status, INTEGER);
						$db->query($sql);
						if ($db->next_record()) {
							$download_activation = $db->f("download_activation");
						} else {
							$download_activation = 0;
						}
						if ($download_activation == 0) {
							$errors = DOWNLOAD_INACTIVE;
						} elseif ($current_date_ts > $expiry_date_ts) {
							$errors = DOWNLOAD_EXPIRED;
						} else {
							$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
							$sql .= " WHERE setting_type='download_info' ";
							$sql .= " AND setting_name='max_downloads' ";
							if (isset($site_id)) {
								$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
								$sql .= " ORDER BY site_id DESC ";
							} else {
								$sql .= " AND site_id=1 ";
							}
							$max_downloads = get_db_value($sql);

							$remote_address = get_ip();
							$downloads_number = 0;
							$month_ago = mktime(0,0,0, date("m") - 1, date("d"), date("Y"));
							$sql  = " SELECT remote_address FROM " . $table_prefix . "items_downloads_statistic ";
							$sql .= " WHERE download_id=0 ";
							$sql .= " AND order_item_id=" . $db->tosql($order_item_id, INTEGER);
							$sql .= " AND remote_address <> " . $db->tosql($remote_address, TEXT);
							$sql .= " AND downloaded_date > " . $db->tosql($month_ago, DATETIME);
							$sql .= " GROUP BY remote_address ";
							$db->query($sql);
							while ($db->next_record()) {
								$downloads_number++;
							}
							if ($downloads_number >= $max_downloads && $max_downloads !=0 ) {
								$errors = DOWNLOAD_LIMITED . "  Max downloads: $max_downloads";
							}
						}
					} else {
						$errors = DOWNLOAD_RELEASE_ERROR;
					}
				}
			}
		} else {
			$errors = DOWNLOAD_RELEASE_ERROR;
		}
	} elseif (strlen($download_id) && strlen($vc_parameter)) {
				
		$sql  = " SELECT id.order_item_id, id.activated, id.max_downloads, id.download_limit, ";
		$sql .= " id.download_added, id.download_expiry, i.download_path AS product_path, id.download_path, ";
		$sql .= " i.item_id, i.download_show_terms, i.download_terms_text ";
		$sql .= " FROM (";
		$sql .= $table_prefix . "items i ";
		$sql .= "LEFT JOIN " . $table_prefix . "items_downloads id ON id.item_id=i.item_id)";
		$sql .= " WHERE id.download_id=" . $db->tosql($download_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$item_id = $db->f("item_id");
			$order_item_id = $db->f("order_item_id");
			$product_path = trim($db->f("product_path"));
			$path_to_file = trim($db->f("download_path"));
			$download_show_terms = $db->f("download_show_terms");
			$download_terms_text = $db->f("download_terms_text");
			
			if (!$path_to_file) {
				$path_to_file = $product_path;
			} 
			$paths = split(";", $path_to_file);
			if ($path_id <= sizeof($paths)) {
				$download_path = trim($paths[$path_id - 1]);
			} 
			$current_date = va_time();
			$current_date_ts = mktime (0,0,0, $current_date[MONTH], $current_date[DAY], $current_date[YEAR]);

			$activated = $db->f("activated");
			$max_downloads = $db->f("max_downloads");
			$download_limit = $db->f("download_limit");
			$download_added = $db->f("download_added", DATETIME);
			$download_expiry = $db->f("download_expiry", DATETIME);
			if (is_array($download_expiry)) {
				$expiry_date_ts = mktime (0,0,0, $download_expiry[MONTH], $download_expiry[DAY], $download_expiry[YEAR]);
			} else {
				$expiry_date_ts = $current_date_ts;
			}
			
			if (!VA_Products::check_permissions($item_id, VIEW_ITEMS_PERM)) {
				header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
				exit;
			}
			
			$vc = md5($download_id . $download_added[3] . $download_added[4] . $download_added[5]);
			if ($vc_parameter != $vc || $path_id > sizeof($paths)) {
				$errors = DOWNLOAD_WRONG_PARAM;
			} elseif ($activated != 1) {
				$errors = DOWNLOAD_INACTIVE;
			} elseif ($current_date_ts > $expiry_date_ts) {
				$errors = DOWNLOAD_EXPIRED;
			} else {
				if (strlen($download_limit)) {
					$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "items_downloads_statistic ";
					$sql .= " WHERE download_id=" . $db->tosql($download_id, INTEGER);
					$total_downloads = get_db_value($sql);
					if ($total_downloads >= $download_limit) {
						$errors = DOWNLOAD_LIMITED." (".$download_limit.")";
					}
				}
				if (!$errors) {
					$remote_address = get_ip();
					$downloads_number = 0;
					$month_ago = mktime(0,0,0, date("m") - 1, date("d"), date("Y"));
					$sql  = " SELECT remote_address FROM " . $table_prefix . "items_downloads_statistic ";
					$sql .= " WHERE download_id=" . $db->tosql($download_id, INTEGER);
					$sql .= " AND remote_address <> " . $db->tosql($remote_address, TEXT);
					$sql .= " AND downloaded_date > " . $db->tosql($month_ago, DATETIME);
					$sql .= " GROUP BY remote_address ";
					$db->query($sql);
					while ($db->next_record()) {
						$downloads_number++;
					}
					if ($downloads_number >= $max_downloads && $max_downloads!=0) {
						$errors = DOWNLOAD_LIMITED  . "  Max downloads: $max_downloads";;
					}
				}
			}
		} else {
			$errors = DOWNLOAD_WRONG_PARAM;
		}
	} else {
		$errors = DOWNLOAD_MISS_PARAM;
	}
	if (!$errors) {
		$fp = @fopen($download_path, "rb");
		if (!$fp) {
			$errors = DOWNLOAD_PATH_ERROR;
		}
	}

	if ($errors) 
	{
		$t = new VA_Template($settings["templates_dir"]);
		$t->set_file("main","download.html");
		$t->set_var("download_errors", $errors);
		$t->parse("errors_block", false);

		include_once("./header.php");
		include_once("./footer.php");

		$t->pparse("main");

		// send notification about bad download
		$remote_address = get_ip();
		$eol = get_eol();
		$subject = "Download Error";
		$message  = "IP Address: " . $remote_address . $eol;
		$message .= "Download ID: " . $download_id . $eol;
		$message .= "User ID: " . get_session("session_user_id") . $eol;
		$message .= "Error: " . $errors . $eol;
		$email = $settings["admin_email"];
		$email_headers = array();
		$email_headers["from"] = $email;
		//va_mail($email, $subject, $message,	"From: " . $settings["admin_email"]);
	} elseif ($download_show_terms == 1 && $terms_agreed != 1) {
		$t = new VA_Template($settings["templates_dir"]);
		$t->set_file("main","download.html");
		$t->set_var("download_href", "download.php");

		$t->set_var("download_id", htmlspecialchars($download_id));
		$t->set_var("release_id", htmlspecialchars($release_id));
		$t->set_var("path_id", htmlspecialchars($path_id));
		$t->set_var("order_item_id", htmlspecialchars($order_item_id));
		$t->set_var("vc", htmlspecialchars($vc_parameter));

		if ($operation == "download") {
			$t->set_var("download_errors", DOWNLOAD_TERMS_USER_ERROR);
			$t->parse("errors_block", false);
		}

		$php_in_download_terms = get_setting_value($settings, "php_in_products_download_terms", 0);
		if ($php_in_download_terms) {
			eval_php_code($download_terms_text);
		}
		$t->set_var("terms_text", $download_terms_text);
		$t->parse("terms_form", false);

		include_once("./header.php");
		include_once("./footer.php");

		$t->pparse("main");
	} else {
		if (preg_match("/^(http|ftp)(s)?:\/\//i", $download_path)) {
			$is_remote = true;
		} else {
			$is_remote = false;
		}
		if ($is_remote) {
			$filesize = remote_filesize($download_path);
		} else {
			$filesize = @filesize($download_path);
		}
		$download_path = str_replace("\\", "/", $download_path);
		$slash_position = strrpos($download_path, "/");
		$filename = ($slash_position === false) ? $download_path : substr($download_path, $slash_position + 1);

		// check if partial content requested
		$content_length = $filesize; $seek_position = 0; 
		$range = get_var("HTTP_RANGE");
		if ($range && $filesize) {
			if (preg_match("/^bytes=(\d+)\-(\d+)$/", $range, $matches)) {
				$seek_position = $matches[1];
				$content_length = $matches[2] + 1;
			} elseif (preg_match("/^bytes=(\d+)\-$/", $range, $matches)) {
				$seek_position = $matches[1];
				$content_length = $filesize - $seek_position;
			} elseif (preg_match("/^bytes=\-(\d+)$/", $range, $matches)) {
				$seek_position = $filesize - $matches[0];
				$content_length = $matches[2];
			}
		}

		if ($filesize) {
			if ($filesize != $content_length) {
				header("HTTP/1.1 206 Partial content");
				header("Content-Length: " . $content_length); 
				header("Content-Range: bytes " . $seek_position . "-" . ($content_length - 1) . "/" . $filesize); 
			} else {
				header("Content-Length: " . $filesize); 
			}
		}
		if (ini_get("zlib.output_compression")) {
			ini_set("zlib.output_compression", "Off");
		}
		header("Pragma: private");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Accept-Ranges: bytes");
		header("Content-Type: application/octet-stream"); 
		header("Content-Disposition: attachment; filename=$filename"); 
		header("Content-Transfer-Encoding: binary"); 

		// seek to start of missing part for local files
		if ($seek_position > 0) {
			if ($is_remote) {
				// imitate fseek for remote files
				@set_time_limit(300);
				fread($fp, $seek_position);
			} else {
				fseek($fp, $seek_position);
			}
		}
		// start buffered download
		while (!feof($fp)){
			// reset time limit for big files
			@set_time_limit(30);
			print(fread($fp, 1024*8));
			if (function_exists("ob_flush")) { 
				if (ob_get_length()) {
					@ob_flush(); 
				}
			}
			flush();
		}
		fclose($fp);

		if ($order_item_id) {
			if (!$download_id) { $download_id = 0; }
			$ct = va_time();
			$sql  = " INSERT INTO " . $table_prefix . "items_downloads_statistic (download_id, order_item_id, remote_address, downloaded_date) VALUES ";
			$sql .= "(" . $db->tosql($download_id, INTEGER) . "," . $db->tosql($order_item_id, INTEGER) . "," . $db->tosql($remote_address, TEXT) . "," . $db->tosql($ct, DATETIME) . ")";
			$db->query($sql);
		}
	}

?>