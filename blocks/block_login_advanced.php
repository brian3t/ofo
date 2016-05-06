<?php

function login_advanced_form($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_login_advanced.html");

	// clear session data for new user if he go to login page
	$new_user_id = get_session("session_new_user_id");
	if ($new_user_id) {
		set_session("session_new_user", "");
		set_session("session_new_user_id", "");
		set_session("session_new_user_type_id", "");
	}

	$user_profile = array();
	$sql  = " SELECT setting_type, setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type LIKE 'user_profile_%'";
	$sql .= " AND setting_name='login_field_type' ";
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ") ";
		$sql .= " ORDER BY site_id ASC";
	} else {
		$sql .= " AND site_id=1";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$user_profile[$db->f("setting_type")] = $db->f("setting_value");
	}
	if (in_array("2", $user_profile)) {
		$login_desc = " (".EMAIL_FIELD.")";
	} else {
		$login_desc = "";
	}

	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");
	$secure_redirect = get_setting_value($settings, "secure_redirect", 0);
	$secure_user_login = get_setting_value($settings, "secure_user_login", 0);
	$secure_user_profile = get_setting_value($settings, "secure_user_profile", 0);
	if ($secure_user_login) {
		$user_login_url = $secure_url . get_custom_friendly_url("user_login.php");
		$forgot_password_url = $secure_url . get_custom_friendly_url("forgot_password.php");
	} else {
		$user_login_url = $site_url . get_custom_friendly_url("user_login.php");
		$forgot_password_url = $site_url . get_custom_friendly_url("forgot_password.php");
	}
	if ($secure_user_profile) {
		$user_profile_url = $secure_url . get_custom_friendly_url("user_profile.php");
	} else {
		$user_profile_url = $site_url . get_custom_friendly_url("user_profile.php");
	}
	$user_home_url = $site_url . get_custom_friendly_url("user_home.php");
	$return_page = get_param("return_page");
	if (!$is_ssl && $secure_user_login && $secure_redirect && preg_match("/^https/i", $secure_url)) {
		$ulu = new VA_URL($user_login_url, false);
		$ulu->add_parameter("return_page", REQUEST, "return_page");
		$ulu->add_parameter("type_error", REQUEST, "type_error");
		
		header("Location: " . $ulu->get_url());
		exit;
	}
	if (!strlen($return_page)) {
		$return_page = $user_home_url;
	}

	$t->set_var("login_desc", $login_desc);
	$t->set_var("user_login_href", $user_login_url);
	$t->set_var("user_home_href", get_custom_friendly_url("user_home.php"));
	$t->set_var("user_profile_href", $user_profile_url);
	$t->set_var("user_profile_url",  $user_profile_url);
	$t->set_var("forgot_password_href", $forgot_password_url);


	$login = get_cookie("cookie_user_login");
	$password = get_cookie("cookie_user_password");
	if (strlen($login) && strlen($password)) {
		$cookie_login = true;
	} else {
		$cookie_login = false;
		$login = "";
		$password = "";
	}

	$remember_me = get_param("remember_me");
	$operation = get_param("operation");
	$errors = "";
	if (strlen($operation) || ($cookie_login && !get_session("session_user_id")))
	{
		if ($operation == "cancel")
		{
			header("Location: " . $site_url . get_custom_friendly_url("index.php"));
			exit;
		}
		elseif($operation == "logout")
		{
			user_logout();
		}
		else
		{
			if (!$cookie_login) {
				$login = get_param("login");
				$password = get_param("password");

				if (!strlen($login)) {
					$error_message = str_replace("{field_name}", LOGIN_FIELD, REQUIRED_MESSAGE);
					$errors .= $error_message . "<br>";
				}

				if (!strlen($password)) {
					$error_message = str_replace("{field_name}", PASSWORD_FIELD, REQUIRED_MESSAGE);
					$errors .= $error_message . "<br>";
				}
			}

			if (!$errors && check_black_ip()) {
				$errors = BLACK_IP_MSG;
			}

			if (!$errors) {
				user_login($login, $password, "", $remember_me, $return_page, true, $errors);
			}
		}
	}

	if ($remember_me) {
		$t->set_var("remember_me", "checked");
	} else {
		$t->set_var("remember_me", "");
	}

	
	$type_error = get_param("type_error");
	if ($type_error == 2) {
		$errors .= ACCESS_DENIED_MSG;
	} elseif ($type_error == 3) {
		$errors .= NO_AVAILIABLE_CATEGORIES_MSG;
	}
	
	if ($errors) {
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	}	else {
		$t->set_var("errors", "");
	}

	
	if (get_session("session_user_id"))	{
		$user_info = get_session("session_user_info");
		$user_login = get_setting_value($user_info, "nickname", "");
		if (!$user_login) {
			$user_login = get_setting_value($user_info, "login", "");
		}
		$t->set_var("user_login", $user_login);
		$t->set_var("user_name", get_session("session_user_name"));
		$t->set_var("operation", "logout");
		$t->set_var("login_form", "");
		$t->parse("logout_form", false);
	} else {
		// parse user types allowed for registration
		$sql  = " SELECT ut.type_id, ut.type_name ";
		if (isset($site_id)) {
			$sql .= " FROM (" . $table_prefix . "user_types ut ";
			$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites uts ON uts.type_id=ut.type_id)";
			$sql .= " WHERE (ut.sites_all=1 OR uts.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
		} else {
			$sql .= " FROM " . $table_prefix . "user_types ut ";
			$sql .= " WHERE ut.sites_all=1 ";					
		}

		$sql .= " AND ut.is_active=1 AND ut.show_for_user=1";
		
		$db->query($sql);
		if ($db->next_record()) {
			do {
				$type_id = $db->f("type_id");
				$type_name = get_translation($db->f("type_name"));
				$t->set_var("user_type_name",  $type_name);
				$t->set_var("user_profile_url",  $user_profile_url . "?type=" . $type_id);
				$t->parse("user_types", true);
			} while ($db->next_record());

			$t->sparse("new_user_block", false);
		}

		$t->set_var("return_page", htmlspecialchars($return_page));
		$t->set_var("login", htmlspecialchars($login));
		$t->set_var("operation", "login");
		$t->set_var("logout_form", "");
		$t->parse("login_form", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}
?>