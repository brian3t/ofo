<?php
	
	$va_license_code = va_license_code();

	check_user_session();
	
	$operation = get_param("operation");

	if ($operation == "logout") {
		user_logout();

		header("Location: " . get_custom_friendly_url("index.php"));
		exit;
	}

	$user_info = get_session("session_user_info");
	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");
	$secure_user_profile = get_setting_value($settings, "secure_user_profile", 0);
	if ($secure_user_profile) {
		$user_profile_url = $secure_url . get_custom_friendly_url("user_profile.php");
		$user_change_password_url = $secure_url . get_custom_friendly_url("user_change_password.php");
	} else {
		$user_profile_url = $site_url . get_custom_friendly_url("user_profile.php");
		$user_change_password_url = $site_url . get_custom_friendly_url("user_change_password.php");
	}
	$secure_user_tickets = get_setting_value($settings, "secure_user_tickets", 0);
	if ($secure_user_tickets) {
		$user_support_url = $secure_url . get_custom_friendly_url("user_support.php");
	} else {
		$user_support_url = $site_url . get_custom_friendly_url("user_support.php");
	}

	// points settings
	$points_balance = get_setting_value($user_info, "total_points", 0);
	$points_system = get_setting_value($settings, "points_system", 0);
	$points_decimals = get_setting_value($settings, "points_decimals", 0);
	// credit system settings
	$credit_system = get_setting_value($settings, "credit_system", 0);
	$credits_balance_user_home = get_setting_value($settings, "credits_balance_user_home", 0);
	$credit_balance = get_setting_value($user_info, "credit_balance", 0);


	$user_settings = array();
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "user_types_settings WHERE type_id=" . $db->tosql(get_session("session_user_type_id"), INTEGER);
	$db->query($sql);
	while ($db->next_record()) {
		$user_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$t->set_file("block_body","block_user_home.html");

	$t->set_var("user_profile_href", get_custom_friendly_url("user_profile.php"));
	$t->set_var("user_profile_url",  $user_profile_url);
	$t->set_var("user_orders_href", get_custom_friendly_url("user_orders.php"));
	$t->set_var("user_change_password_href", get_custom_friendly_url("user_change_password.php"));
	$t->set_var("user_change_password_url",  $user_change_password_url);
	$t->set_var("user_support_href", $user_support_url);
	$t->set_var("forum_href", get_custom_friendly_url("forum.php"));
	$t->set_var("user_products_href", get_custom_friendly_url("user_products.php"));
	$t->set_var("user_product_registrations_href", get_custom_friendly_url("user_product_registrations.php"));
	$t->set_var("user_ads_href", get_custom_friendly_url("user_ads.php"));
	$t->set_var("user_merchant_orders_href", get_custom_friendly_url("user_merchant_orders.php"));
	$t->set_var("user_merchant_sales_href", get_custom_friendly_url("user_merchant_sales.php"));
	$t->set_var("user_affiliate_sales_href", get_custom_friendly_url("user_affiliate_sales.php"));
	$t->set_var("user_payments_href", get_custom_friendly_url("user_payments.php"));
	$t->set_var("user_carts_href", get_custom_friendly_url("user_carts.php"));
	$t->set_var("user_wishlist_href", get_custom_friendly_url("user_wishlist.php"));
	$t->set_var("user_reminders_href", "user_reminders.php");
	$t->set_var("user_change_type_url", get_custom_friendly_url("user_change_type.php"));


	$upgrade_downgrade = get_setting_value($user_settings, "upgrade_downgrade", 0);
	$sql = "SELECT is_subscription FROM " . $table_prefix . "user_types WHERE type_id=" . $db->tosql(get_session("session_user_type_id"), INTEGER);
	$is_subscription = get_db_value($sql);
	if ($upgrade_downgrade || $is_subscription) {
		$t->sparse("upgrade_downgrade_block", false);
	}

	$subscription_periods = 
		array( 
			array("", ""), array(1, DAY_MSG), array(2, WEEK_MSG), array(3, MONTH_MSG), array(4, YEAR_MSG)
		);

	$sql  = " SELECT  * FROM " . $table_prefix . "subscriptions s, " . $table_prefix . "users u ";
	$sql .= " WHERE s.subscription_id = u.subscription_id ";
	$db->query($sql);
	if ($db->next_record()) {
		$t->set_var("subscription_name", $db->f("subscription_name"));
		$t->set_var("subscription_fee", currency_format($db->f("subscription_fee")));
		$t->set_var("subscription_interval", $db->f("subscription_interval"));
		$subscription_period = "";
		foreach ($subscription_periods as $key => $sub_array) {
			if ($sub_array[0] == $db->f("subscription_period")) {
				$subscription_period = $sub_array[1];
			}
		}
		$t->set_var("subscription_period", $subscription_period);
		$t->parse("current_subscription", false);						
	} else {
		$t->set_var("current_subscription", "");
	}

	if ($points_system) {
		$t->set_var("points_balance", number_format($points_balance, $points_decimals));
		$t->sparse("points_balance_block", false);
	}
	if ($credit_system && $credits_balance_user_home) {
		$t->set_var("credit_balance", currency_format($credit_balance));
		$t->sparse("credit_balance_block", false);
	}

	$user_login = get_setting_value($user_info, "nickname", "");
	if (!$user_login) { 
		$user_login = get_setting_value($user_info, "login", "");
	}
	$t->set_var("user_login", $user_login);
	$t->set_var("user_name", get_session("session_user_name"));

	$blocks = array(
		"orders_block"          => "my_orders",
		"details_block"         => "my_details",
		"support_block"         => "my_support",
		"forum_block"           => "my_forum",
		"products_block"        => "access_products",
		"product_registrations_block" => "my_product_registrations",
		"ad_block"              => "add_ad",
		"merchant_orders_block" => "merchant_orders",
		"merchant_sales_block"  => "merchant_sales",
		"affiliate_sales_block" => "affiliate_sales",
		"payments_block"        => "my_payments",
		"carts_block"           => "my_carts",
		"wishlist_block"        => "my_wishlist",
		"reminders_block"		    => "reminder_service"
	);

	// shop - 1, cms - 2, helpdesk - 4, forum - 8, ads - 16
	if (!($va_license_code & 1)) {
		unset($blocks["orders_block"]);
		unset($blocks["products_block"]);
		unset($blocks["merchant_sales_block"]);
		unset($blocks["merchant_orders_block"]);
		unset($blocks["affiliate_sales_block"]);
		unset($blocks["payments_block"]);
		unset($blocks["carts_block"]);
		unset($blocks["wishlist_block"]);
		unset($blocks["reminders_block"]);
	}
	if (!($va_license_code & 4)) {
		unset($blocks["support_block"]);
	}
	if (!($va_license_code & 8)) {
		unset($blocks["forum_block"]);
	}
	if (!($va_license_code & 16)) {
		unset($blocks["ad_block"]);
	}

	$block_number = 0;
	foreach ($blocks as $template_block => $permission_name) {
		$permission = get_setting_value($user_settings, $permission_name, 0);
		if ($permission) {
			$block_number++;
			$t->sparse($template_block, false);
			$t->parse("cols", true);
			$t->set_var($template_block, "");
			if ($block_number % 2 == 0) {
				$t->parse("rows", true);
				$t->set_var("cols", "");
			}
		}
	}
	if ($block_number > 0 && $block_number % 2 != 0) {
		$t->parse("rows", true);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>