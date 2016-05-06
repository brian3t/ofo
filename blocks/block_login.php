<?php

function login_form($block_name = "", $block_prefix = "")
{
	global $t, $db, $table_prefix, $settings, $is_ssl, $current_page;

	if($block_name) {
	  $t->set_file("block_body", "block_login.html");
	}

	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");
	$secure_user_login = get_setting_value($settings, "secure_user_login", 0);
	if ($secure_user_login && !get_session("session_user_id")) {
		// make secure login if user is not logged in
		$user_login_url = $secure_url . get_custom_friendly_url("user_login.php");
		$forgot_password_url = $secure_url . get_custom_friendly_url("forgot_password.php");
		$login_form_url = $secure_url . $current_page;
	} else {
		$user_login_url = $site_url . get_custom_friendly_url("user_login.php");
		$forgot_password_url = $site_url . get_custom_friendly_url("forgot_password.php");
		$login_form_url = $site_url . $current_page;
	}
	$user_home_url = $site_url . get_custom_friendly_url("user_home.php");
	$query_string = transfer_params("", true);
	$return_page = get_param("return_page");
	if (!$return_page) {
		if ($is_ssl) {
			$return_page = $secure_url . $current_page . $query_string;
		} else {
			$return_page = $site_url . $current_page . $query_string;
		}
		$return_page .= "#block_login";
	}

  $t->set_var("user_home_href", $user_home_url);
  $t->set_var("forgot_password_href", $forgot_password_url);
  $t->set_var("login_form_url", $login_form_url);
  $t->set_var("return_page", htmlspecialchars($return_page));

	$login_action = get_param("login_action");
	$login_errors = ""; $user_login = "";
	if(strlen($login_action))
	{
		if ($login_action == "logout") {
			user_logout();
		} else {

			$user_login = get_param("user_login");
			$user_password = get_param("user_password");
			
			if(!strlen($user_login)) {
				$error_message = str_replace("{field_name}", LOGIN_FIELD, REQUIRED_MESSAGE);
				$login_errors .= $error_message . "<br>";
			}
	  
			if(!strlen($user_password)) {
				$error_message = str_replace("{field_name}", PASSWORD_FIELD, REQUIRED_MESSAGE);
				$login_errors .= $error_message . "<br>";
			}

			if(!$login_errors && check_black_ip()) {
				$login_errors = BLACK_IP_MSG;
			}
			
			if(!strlen($login_errors)) {
				user_login($user_login, $user_password, "", 0, "", false, $login_errors);
			}
		}

		if (!$login_errors) {
			// make redirect to original page after successful login/logout operations
			header("Location: " . $return_page);
			exit;
		}
	}

	if (get_session("session_user_id"))	{
		$user_info = get_session("session_user_info");
		$user_login = get_setting_value($user_info, "nickname", "");
		if (!$user_login) { 
			$user_login = get_setting_value($user_info, "login", "");
		}
		$t->set_var("user_login", $user_login);
		$t->set_var("user_name", get_session("session_user_name"));
		$t->set_var("login_action", "logout");
		$t->set_var("login_form", "");
		$t->parse($block_prefix . "logout_form", false);
	}	else {
		$t->set_var("user_login", htmlspecialchars($user_login));
		$t->set_var("login_action", "login");
		$t->set_var("logout_form", "");
		$t->parse($block_prefix . "login_form", false);
	}

	if(strlen($login_errors))
	{
		$t->set_var("errors_list", $login_errors);
		$t->parse($block_prefix . "login_errors", false);
	}

	if($block_name) {
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>