<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_login.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");

	$secure_admin_login = get_setting_value($settings, "secure_admin_login", 0);
	$secure_url = get_setting_value($settings, "secure_url", "");
	$site_url = get_setting_value($settings, "site_url", "");
	
	$current_version = va_version();

	// check admin folder	
	$admin_folder = "";
	$request_uri = get_request_uri();
	$request_uri = preg_replace("/\/+/", "/", $request_uri);
	$slash_position = strrpos ($request_uri, "/");
	if ($slash_position !== false) {
		$request_path = substr($request_uri, 0, $slash_position);
		$slash_position = strrpos ($request_path, "/");
		if ($slash_position !== false) {
			$admin_folder = substr($request_path, $slash_position + 1);
		}
	}
	if ($admin_folder) {
		$admin_folder .= "/";
	} else {
		$admin_folder  = "admin/";
	}

	if ($secure_admin_login && $secure_url) {
		//$admin_login_url = $secure_url . $admin_folder . "admin_login.php";
		$admin_login_url = $admin_secure_url . "admin_login.php";
	} else {
		$admin_login_url = "admin_login.php";
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_login.html");
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_login_href", "admin_login.php");
	$t->set_var("admin_login_url", $admin_login_url);
	$t->set_var("admin_privileges_href", "admin_privileges.php");

	$return_page = get_param("return_page");
	if (!strlen($return_page)) { $return_page = "index.php"; }
	if ($secure_admin_login && $secure_url) {
		$slash_position = strrpos ($return_page, "/");
		$redirect_page = ($slash_position === false) ? $return_page : substr($return_page, $slash_position + 1);
		//$redirect_url = $site_url . $admin_folder . $redirect_page;
		$redirect_url = $admin_secure_url . $redirect_page;
	} else {
		$redirect_url = $return_page;
	}

	$operation = get_param("operation");
	$errors = false; $errors_list = ""; $login = ""; $post_data = "";
	if (strlen($operation))
	{
		if ($operation == "cancel_login")
		{
			header("Location: " . $site_url . "index.php");
			exit;
		}
		elseif ($operation == "logout")
		{
			set_session("session_admin_id", "");
			set_session("session_admin_privilege_id", "");
			set_session("session_admin_name", "");
			set_session("session_admin_permissions", "");
			set_session("session_last_order_id", "");
			set_session("session_last_user_id", "");
			set_session("session_warn_permission", "");
		}
		elseif ($operation == "login")
		{
			$login     = get_param("login");
			$password  = get_param("password");
			$post_data = get_param("post_data");
			
			if (!strlen($login)) {
				$error_message = str_replace("{field_name}", LOGIN_FIELD, REQUIRED_MESSAGE);
				$errors_list .= $error_message . "<br>";
				$errors = true;
			}
	  
			if (!strlen($password)) {
				$error_message = str_replace("{field_name}", PASSWORD_FIELD, REQUIRED_MESSAGE);
				$errors_list .= $error_message . "<br>";
				$errors = true;
			}

			/* check for black ips
			if (!$errors && check_black_ip()) {
				$errors_list = BLACK_IP_MSG;
				$errors = true;
			}
			*/

			if (!$errors)
			{
				$password_encrypt = get_setting_value($settings, "password_encrypt", 0);
				$admin_password_encrypt = get_setting_value($settings, "admin_password_encrypt", $password_encrypt);
				if ($admin_password_encrypt == 1) {
					$password_match = md5($password);
				} else {
					$password_match = $password;
				}

				// prepare information for statistics
				$ip_address = get_ip();
				$forwarded_ips = get_var("HTTP_X_FORWARDED_FOR");
				$date_added = va_time();

				$sql  = " SELECT * FROM " . $table_prefix . "admins WHERE ";
				$sql .= " login = " . $db->tosql($login, TEXT);
				$sql .= " AND password = " . $db->tosql($password_match, TEXT);
				$db->query($sql);
				if ($db->next_record()) {
					
					$admin_id = $db->f("admin_id");
					$privilege_id = $db->f("privilege_id");
					set_session("session_admin_id", $db->f("admin_id"));
					set_session("session_admin_privilege_id", $db->f("privilege_id"));
					set_session("session_admin_name", $db->f("admin_name"));
					set_session("session_last_order_id", $db->f("last_order_id"));
					set_session("session_last_user_id", $db->f("last_user_id"));
					
					// save login statistics
					if (comp_vers(va_version(), "3.5.24") == 1) {
						$sql  = " INSERT INTO " . $table_prefix . "admins_login_stats ";
						$sql .= " (admin_id, login_status, ip_address, forwarded_ips, date_added) VALUES (";
						$sql .= $db->tosql($admin_id, INTEGER) . ", ";
						$sql .= $db->tosql(1, INTEGER) . ", ";
						$sql .= $db->tosql($ip_address, TEXT) . ", ";
						$sql .= $db->tosql($forwarded_ips, TEXT) . ", ";
						$sql .= $db->tosql($date_added, DATETIME) . ") ";
						$db->query($sql);
					}

					$permissions = array();
					$sql  = " SELECT block_name, permission FROM " . $table_prefix . "admin_privileges_settings ";
					$sql .= " WHERE privilege_id=" . $db->tosql($privilege_id, INTEGER, true, false);
					$db->query($sql);
					while ($db->next_record()) {
						$block_name = $db->f("block_name");
						$permissions[$block_name] = $db->f("permission");
					}
					set_session("session_admin_permissions", $permissions);	
					
					if ((comp_vers($current_version, "2.8.1") <= 1) && (strpos($redirect_url, "admin.php"))) {
						$sql  = " SELECT url ";
						$sql .= " FROM " . $table_prefix . "bookmarks ";
						$sql .= " WHERE is_start_page=1 AND admin_id=" . $db->tosql($admin_id, INTEGER);
						$start_url = get_db_value($sql);
						if ($start_url) {
							$redirect_url = $start_url;
						}
					}

					if ($post_data && $return_page) {
						// clear all data and re-submit post data
						foreach ($_GET as $key => $value) {
							unset($_GET[$key]);
						}
						foreach ($_POST as $key => $value) {
							unset($_POST[$key]);
						}
						$admin_page = basename($return_page);
						if (preg_match("/\?(.*)$/", $admin_page, $matches)) {
							$get_data = $matches[1];
							$admin_page = preg_replace("/\?.*$/", "", $admin_page);
							if ($get_data) {
								// set GET data
								$query_params = explode("&", $get_data);
								for ($qp = 0; $qp < sizeof($query_params); $qp++) {
									$query_param = $query_params[$qp];
									if (preg_match("/^([^=]+)=(.*)$/", $query_param, $matches)) {
										$_GET[urldecode($matches[1])] = urldecode(($matches[2]));
									} else {
										$_GET[$param_name] = "";
									}
								}
							}
						}
						// set POST data
						$query_params = explode("&", $post_data);
						for ($qp = 0; $qp < sizeof($query_params); $qp++) {
							$query_param = $query_params[$qp];
							if (preg_match("/^([^=]+)=(.*)$/", $query_param, $matches)) {
								$_POST[urldecode($matches[1])] = urldecode(($matches[2]));
							} else {
								$_POST[$param_name] = "";
							}
						}

						include($admin_page);
						exit;
					}

					header("Location: " . $redirect_url); 
					exit;

				} else {
					$errors_list .= LOGIN_PASSWORD_ERROR . "<br>";
					$errors = true;

					if (comp_vers(va_version(), "3.5.24") == 1) {
						// check if we can save statistics
						$sql  = " SELECT admin_id FROM " . $table_prefix . "admins WHERE ";
						$sql .= " login = " . $db->tosql($login, TEXT);
						$db->query($sql);
						if ($db->next_record()) {
							// save login statistics
							$stat_admin_id = $db->f("admin_id");
							$sql  = " INSERT INTO " . $table_prefix . "admins_login_stats ";
							$sql .= " (admin_id, login_status, ip_address, forwarded_ips, date_added) VALUES (";
							$sql .= $db->tosql($stat_admin_id, INTEGER) . ", ";
							$sql .= $db->tosql(0, INTEGER) . ", ";
							$sql .= $db->tosql($ip_address, TEXT) . ", ";
							$sql .= $db->tosql($forwarded_ips, TEXT) . ", ";
							$sql .= $db->tosql($date_added, DATETIME) . ") ";
							$db->query($sql);
						}
					}

					// make a delay to prevent automatic passwords checks
					sleep(3);
				}
			}
		}
	}

	if (get_session("session_admin_id"))	
	{
		$t->set_var("admin_name", get_session("session_admin_name"));
		$t->set_var("operation", "logout");
		$t->set_var("login_form", "");
		$t->parse("logout_form", false);
	}
	else
	{
		$t->set_var("return_page", htmlspecialchars($return_page));
		$t->set_var("login", htmlspecialchars($login));
		$t->set_var("operation", "login");
		$t->set_var("logout_form", "");
		$t->parse("login_form", false);
	}

	$type_error = get_param("type_error");
	if ($type_error == 1) {
		$t->parse("session_expired", false);
		$errors = true;
		// check if post data available to save and pass it
		if (is_array($_POST) && sizeof($_POST) > 0) {
			foreach($_POST as $key => $value) {
				if ($post_data) { $post_data .= "&"; }
				$post_data .= urlencode($key)."=".urlencode($value);
			}
		}
	} else if ($type_error == 2) {
		$t->parse("access_error", false);
		$errors = true;
	}
	// set post data if available
	$t->set_var("post_data", $post_data);
	if ($errors) {
		$t->set_var("errors_list", $errors_list);
		$t->parse("errors", false);
	}	else {
		$t->set_var("errors", "");
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>