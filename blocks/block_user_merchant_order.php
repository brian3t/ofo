<?php

	check_user_security("merchant_orders");

	$secure_user_home = get_setting_value($settings, "secure_user_home", 0);	
	$secure_merchant_orders = get_setting_value($settings, "secure_merchant_orders", 0);	
	$secure_merchant_order = get_setting_value($settings, "secure_merchant_order", 0);	
	$site_url = get_setting_value($settings, "site_url", "");	
	$secure_url = get_setting_value($settings, "secure_url", "");
	if ($secure_user_home) {
		$user_home_page = $secure_url. get_custom_friendly_url("user_home.php");
	} else {
		$user_home_page = $site_url . get_custom_friendly_url("user_home.php");
	}
	if ($secure_merchant_orders) {
		$user_merchant_orders_page = $secure_url . get_custom_friendly_url("user_merchant_orders.php");
	} else {
		$user_merchant_orders_page = $site_url . get_custom_friendly_url("user_merchant_orders.php");
	}
	if ($secure_merchant_order) {
		$user_merchant_order_page = $secure_url . get_custom_friendly_url("user_merchant_order.php");
	} else {
		$user_merchant_order_page = $site_url . get_custom_friendly_url("user_merchant_order.php");
	}

	$user_settings = array();
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "user_types_settings WHERE type_id=" . $db->tosql(get_session("session_user_type_id"), INTEGER);
	$db->query($sql);
	while ($db->next_record()) {
		$user_settings[$db->f("setting_name")] = $db->f("setting_value");
	}
	$merchant_order_payment_details = get_setting_value($user_settings, "merchant_order_payment_details", 0);
	$merchant_order_cc_number = get_setting_value($user_settings, "merchant_order_cc_number", 0);
	$merchant_order_cc_cvv2 = get_setting_value($user_settings, "merchant_order_cc_cvv2", 0);

	$t->set_file("block_body","block_user_merchant_order.html");
	$t->set_var("user_home_href", $user_home_page);
	$t->set_var("user_merchant_order_href", $user_merchant_order_page);
	$t->set_var("user_merchant_orders_href", $user_merchant_orders_page);


	$errors = "";
	$payment_id = "";
	$order_id = get_param("order_id");
	$user_id = get_session("session_user_id");
	$operation = get_param("operation");
	$order_status = "";

	$sql  = " SELECT o.order_status, o.payment_id ";
	$sql .= " FROM ((" . $table_prefix . "orders o ";
	$sql .= " INNER JOIN " . $table_prefix . "orders_items oi ON o.order_id=oi.order_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
	$sql .= " WHERE o.order_id=" . $db->tosql($order_id, INTEGER);
	$sql .= " AND oi.item_user_id=" . $db->tosql($user_id, INTEGER);
	if (isset($site_id)) {
		$sql .= " AND o.site_id=" . $db->tosql($site_id, INTEGER, true, false);	
	} else {
		$sql .= " AND o.site_id=1";	
	}
	$sql .= " GROUP BY o.order_id, o.order_status, o.payment_id ";
	$db->query($sql);
	if ($db->next_record())  {
		$order_status = $db->f("order_status");
		$payment_id = $db->f("payment_id");
	} else {
		$errors = "Such <b>Order</b> doesn't exists.<br>";
		header("Location: " . $user_merchant_orders_page);
		exit;
	}

	$order_info = array();
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='order_info'";
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$order_info[$db->f("setting_name")] = $db->f("setting_value");
	}

	$r = new VA_Record($table_prefix . "orders");
	$r->add_where("order_id", INTEGER);
	$r->set_value("order_id", $order_id);
	$r->add_textbox("order_placed_date", DATETIME);
	$r->change_property("order_placed_date", VALUE_MASK, $datetime_show_format);
	$r->add_textbox("currency_code", TEXT);

	$personal_number = 0;
	$delivery_number = 0;
	for ($i = 0; $i < sizeof($parameters); $i++)
	{                                    
		$personal_param = "show_" . $parameters[$i];
		$delivery_param = "show_delivery_" . $parameters[$i];
		$r->add_textbox($parameters[$i], TEXT);
		$r->add_textbox("delivery_" . $parameters[$i], TEXT);
		if (isset($order_info[$personal_param]) && $order_info[$personal_param] == 1) {
			$personal_number++;
		} else {
			$r->parameters[$parameters[$i]][SHOW] = false;
		}
		if (isset($order_info[$delivery_param]) && $order_info[$delivery_param] == 1) {
			$delivery_number++;
		} else {
			$r->parameters["delivery_" . $parameters[$i]][SHOW] = false;
		}
	}

	$r->add_textbox("cc_name", TEXT);
	$r->add_textbox("cc_first_name", TEXT);
	$r->add_textbox("cc_last_name", TEXT);
	$r->add_textbox("cc_number", TEXT);
	$r->parameters["cc_number"][SHOW] = $merchant_order_cc_number;
	$r->add_textbox("cc_start_date", DATETIME);
	$r->change_property("cc_start_date", VALUE_MASK, array("MM", " / ", "YYYY"));
	$r->add_textbox("cc_expiry_date", DATETIME);
	$r->change_property("cc_expiry_date", VALUE_MASK, array("MM", " / ", "YYYY"));
	$r->add_textbox("cc_type", INTEGER);
	$r->add_textbox("cc_issue_number", INTEGER);
	$r->add_textbox("cc_security_code", TEXT);
	$r->parameters["cc_security_code"][SHOW] = $merchant_order_cc_cvv2;
	$r->add_textbox("pay_without_cc", TEXT);

	$payment_params = 0;
	if (!$errors) {
		$r->get_db_values();
		$user_currency_code = $r->get_value("currency_code");
		// get payment info if available
		$sql = "SELECT payment_info FROM " . $table_prefix . "payment_systems WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
		$payment_info = get_db_value($sql);
		$payment_info = get_translation($payment_info);
		$user_currency = get_currency($user_currency_code);
		$payment_info = get_currency_message($payment_info, $user_currency);
		if (trim($payment_info)) {
			$payment_params++;
			$t->set_block("payment_info", $payment_info);
			$t->parse("payment_info", false);
			$t->global_parse("payment_info_block", false, false, true);
		}

		$cc_info = array();
		$setting_type = "credit_card_info_" . $payment_id;
		$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT);
		if (isset($site_id)) {
			$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
			$sql .= " ORDER BY site_id ASC ";
		} else {
			$sql .= " AND site_id=1 ";
		}
		$db->query($sql);
		while($db->next_record()) {
			$cc_info[$db->f("setting_name")] = $db->f("setting_value");
		}
		$cc_number_security = get_setting_value($cc_info, "cc_number_security", 0);
		$cc_code_security = get_setting_value($cc_info, "cc_code_security", 0);
		if ($cc_number_security > 0) {
			$r->set_value("cc_number", format_cc_number(va_decrypt($r->get_value("cc_number"))));
		}
		if ($cc_code_security > 0) {
			$r->set_value("cc_security_code", va_decrypt($r->get_value("cc_security_code")));
		}

	}

	for ($i = 0; $i < sizeof($cc_parameters); $i++) { 
		if ($r->is_empty($cc_parameters[$i])) {
			$r->parameters[$cc_parameters[$i]][SHOW] = false;
		} else {
			$payment_params++;
		}
	}

	$r->set_value("company_id", get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("company_id"), INTEGER)));
	$r->set_value("delivery_company_id", get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("delivery_company_id"), INTEGER)));
	
	if ($r->parameter_exists("state_id") && $r->get_value("state_id")) {
		$state_name = get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("state_id"), INTEGER, true, false));
	} else {
		$state_name = "";
	}
	
	if ($r->parameter_exists("delivery_state_id") && $r->get_value("delivery_state_id")) {
		$delivery_state_name = get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("delivery_state_id"), INTEGER, true, false));
	} else {
		$delivery_state_name = "";
	}
		
	if ($r->parameter_exists("country_id") && $r->get_value("country_id")) {
		$country_name = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("country_id"), INTEGER, true, false));
	} else {
		$country_name = "";
	}
	
	if ($r->parameter_exists("delivery_country_id") && $r->get_value("delivery_country_id")) {
		$delivery_country_name = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("delivery_country_id"), INTEGER, true, false));
	} else {
		$delivery_country_name = "";
	}

	$r->set_value("state_id", get_translation($state_name));	
	$r->set_value("country_id", get_translation($country_name));
	$r->set_value("delivery_state_id", get_translation($delivery_state_name));
	$r->set_value("delivery_country_id", get_translation($delivery_country_name));
	
	
	$r->set_value("cc_type", get_translation(get_db_value("SELECT credit_card_name FROM " . $table_prefix . "credit_cards WHERE credit_card_id=" . $db->tosql($r->get_value("cc_type"), INTEGER))));

	$r->set_parameters();

	if (!$errors) {
		show_order_items($order_id, true, "user_merchant_order");
	}

	if ($personal_number > 0) {
		$t->parse("personal", false);
	}
	if ($delivery_number > 0) {
		$t->parse("delivery", false);
	}

	if ($payment_params > 0 && $merchant_order_payment_details) {
		// don't show customer payment information for merchants
		$t->parse("payment", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>