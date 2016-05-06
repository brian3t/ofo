<?php

function checkout_login($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_checkout_login.html");

	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");
	$secure_redirect = get_setting_value($settings, "secure_redirect", 0);
	$secure_user_login = get_setting_value($settings, "secure_user_login", 0);
	$secure_order_profile = get_setting_value($settings, "secure_order_profile", 0);
	if ($secure_user_login) {
		$checkout_url = $secure_url . get_custom_friendly_url("checkout.php");
		$forgot_password_url = $secure_url . get_custom_friendly_url("forgot_password.php");
	} else {
		$checkout_url = $site_url . get_custom_friendly_url("checkout.php");
		$forgot_password_url = $site_url . get_custom_friendly_url("forgot_password.php");
	}
	if ($secure_order_profile) {
		$order_info_url = $secure_url . get_custom_friendly_url("order_info.php");
	} else {
		$order_info_url = $site_url . get_custom_friendly_url("order_info.php");
	}
	if (!$is_ssl && $secure_user_login && $secure_redirect && preg_match("/^https/i", $secure_url)) {
		header("Location: " . $checkout_url);
		exit;
	}

	// check if user need further actions to finish registration
	$new_user_id = get_session("session_new_user_id");
	if ($new_user_id) {
		$new_user = get_session("session_new_user");
		if ($new_user == "registration") {
			// check secure option
			$secure_url = get_setting_value($settings, "secure_url", "");
			$secure_user_profile = get_setting_value($settings, "secure_user_profile", 0);
			if ($secure_user_profile) {
				$user_profile_url = $secure_url . get_custom_friendly_url("user_profile.php");
			} else {
				$user_profile_url = $site_url . get_custom_friendly_url("user_profile.php");
			}
			header ("Location: " . $user_profile_url);
			exit;
		} else if ($new_user == "expired") {
			// check secure option
			header("Location: " . $order_info_url);
			exit;
		}
	}

	$shopping_cart = get_session("shopping_cart");
	$total_items = 0;
	if(is_array($shopping_cart)) {
		// check for active products in the cart
		foreach($shopping_cart as $cart_id => $item) {
			$properties_more = isset($item["PROPERTIES_MORE"]) ? $item["PROPERTIES_MORE"] : false;
			if (!$properties_more) {  
				$total_items++;
			}
		}
	}
	if (!$total_items) {
		$basket_url = $site_url . get_custom_friendly_url("basket.php");
		$rp = get_param("rp");
		if (strlen($rp)) {
			$basket_url .=  "?rp=" . urlencode($rp);
		}
		header("Location: " . $basket_url);
		exit;
	}

	$secure_user_profile = get_setting_value($settings, "secure_user_profile", 0);
	if ($secure_user_profile) {
		$user_profile_url = $secure_url . get_custom_friendly_url("user_profile.php") . "?return_page=" . urlencode($order_info_url);
	} else {
		$user_profile_url = $site_url . get_custom_friendly_url("user_profile.php") . "?return_page=" . urlencode($order_info_url);
	}

	set_session("session_vc", "");
	set_session("session_order_id", "");
	set_session("session_payment_id", "");
	if (get_session("session_user_id"))
	{
		header("Location: " . $order_info_url);
		exit;
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

	$t->set_var("login_desc", $login_desc);
	$t->set_var("order_info_href", $order_info_url);
	$t->set_var("order_info_url",  $order_info_url);
	$t->set_var("user_profile_href", $user_profile_url);
	$t->set_var("user_profile_url",  $user_profile_url);
	$t->set_var("forgot_password_href", $forgot_password_url);
	$t->set_var("checkout_url", $checkout_url);

	$login = get_cookie("cookie_user_login");
	$password = get_cookie("cookie_user_password");
	if (strlen($login) && strlen($password))	{
		$cookie_login = true;
	} else {
		$cookie_login = false;
		$login = "";
		$password = "";
	}

	$errors = "";
	$remember_me = get_param("remember_me");
	$action = get_param("action");
	if ($action == "login" || $cookie_login)
	{
		if (!$cookie_login)
		{
			$login = get_param("login");
			$password = get_param("password");

			if(!strlen($login)) {
				$error_message = str_replace("{field_name}", LOGIN_FIELD, REQUIRED_MESSAGE);
				$errors .= $error_message . "<br>";
			}
	  
			if(!strlen($password)) {
				$error_message = str_replace("{field_name}", PASSWORD_FIELD, REQUIRED_MESSAGE);
				$errors .= $error_message . "<br>";
			}
		}

		if (!$errors && check_black_ip()) {
			$errors = BLACK_IP_MSG;
		}
		
		if (!strlen($errors)) {
			user_login($login, $password, "", $remember_me, $order_info_url, true, $errors);
		}
	}

	if ($remember_me) {
		$t->set_var("remember_me", "checked");
	} else {
		$t->set_var("remember_me", "");
	}

	$t->set_var("login", htmlspecialchars($login));

	$user_registration = get_setting_value($settings, "user_registration", 0);
	if ($user_registration == 1) {
		$t->set_var("checkout_without_link", "");
	} else {
		$t->parse("checkout_without_link", false);
	}

	$types_number = 0;
	// parse user types allowed for registration
	$sql  = " SELECT ut.type_id, ut.type_name ";
	if (isset($site_id)) {
		$sql .= " FROM (" . $table_prefix . "user_types ut ";
		$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites uts ON uts.type_id=ut.type_id) ";
		$sql .= " WHERE (ut.sites_all=1 OR uts.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
	} else {
		$sql .= " FROM " . $table_prefix . "user_types ut ";
		$sql .= " WHERE ut.sites_all=1 ";					
	}
	$sql .= " AND ut.is_active=1 AND ut.show_for_user=1";
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$types_number++;
			$type_id = $db->f("type_id");
			$type_name = get_translation($db->f("type_name"));
			$t->set_var("user_type_name",  $type_name);
			$t->set_var("user_profile_type_url",  $user_profile_url . "&type=" . $type_id);
			$t->parse("user_types", true);
		} while ($db->next_record());
	}

	if (!$user_registration || $types_number) {
		$t->sparse("new_user_block", false);
	}


	if (strlen($errors)) {
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	} else {
		$t->set_var("errors", "");
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>