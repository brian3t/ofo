<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_orders_recurring.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	@set_time_limit (300);
	include_once ("./admin_config.php");
	include_once($root_folder_path . "includes/var_definition.php");
	include_once($root_folder_path . "includes/constants.php");
	include_once($root_folder_path . "includes/common_functions.php");
	include_once($root_folder_path . "includes/va_functions.php");
	include_once($root_folder_path . "includes/sms_functions.php");
	$language_code = get_language("messages.php");
	include_once($root_folder_path . "messages/".$language_code."/messages.php");
	include_once($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once($root_folder_path . "messages/".$language_code."/admin_messages.php");
	include_once($root_folder_path . "includes/date_functions.php");
	include_once($root_folder_path . "includes/url.php");
	include_once($root_folder_path . "includes/template.php");
	include_once($root_folder_path . "includes/db_$db_lib.php");
	if (file_exists($root_folder_path . "includes/license.php") ) {
		include_once($root_folder_path . "includes/license.php");
	} 
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/parameters.php");
	include_once ($root_folder_path . "includes/shopping_cart.php");
	include_once ($root_folder_path . "includes/order_links.php");
	include_once ($root_folder_path . "includes/order_items.php");
	include_once ($root_folder_path . "includes/vat_check.php");

	// Database Initialize
	$db = new VA_SQL();
	$db->DBType      = $db_type;
	$db->DBDatabase  = $db_name;
	$db->DBHost      = $db_host;
	$db->DBPort      = $db_port;
	$db->DBUser      = $db_user;
	$db->DBPassword  = $db_password;
	$db->DBPersistent= $db_persistent;

	// get site properties
	$settings = va_settings();
	// check ssl connection
	$is_ssl = (strtoupper(get_var("HTTPS")) == "ON" || get_var("SERVER_PORT") == 443);

	// get default currency 
	$currency = get_currency();

	check_admin_security("create_orders");

	$tax_prices_type_global = get_setting_value($settings, "tax_prices_type", 0);

	// settings for errors notifications 
	$eol = get_eol();
	$recipients     = $settings["admin_email"];
	$email_headers  = "From: ". $settings["admin_email"] . $eol;
	$email_headers .= "Content-Type: text/plain";
	$recurring_errors = ""; $recurring_success = ""; $orders_added = 0;

	// initiliaze template if it doesn't exists
	if (!isset($t)) {
		$t = new VA_Template($settings["admin_templates_dir"]);
	}

	// Database Initialize
	$dbi = new VA_SQL();
	$dbi->DBType      = $db_type;
	$dbi->DBDatabase  = $db_name;
	$dbi->DBHost      = $db_host;
	$dbi->DBPort      = $db_port;
	$dbi->DBUser      = $db_user;
	$dbi->DBPassword  = $db_password;
	$dbi->DBPersistent= $db_persistent;

	$order_params = array(
		"site_id" => INTEGER, "user_id" => INTEGER, "user_type_id" => INTEGER, "payment_id" => INTEGER, "visit_id" => INTEGER,
		"friend_user_id" => INTEGER,
		"remote_address" => TEXT, "initial_ip" => TEXT, "cookie_ip" => TEXT,
		"affiliate_code" => TEXT, "affiliate_user_id" => INTEGER, "keywords" => TEXT,
		"coupons_ids" => TEXT, "default_currency_code" => TEXT, "currency_code" => TEXT, "keywords" => TEXT,
		"tax_name" => TEXT, "tax_percent" => NUMBER, "tax_total" => NUMBER, "tax_prices_type" => INTEGER,
		"name" => TEXT, "first_name" => TEXT, "last_name" => TEXT,
		"company_id" => INTEGER, "company_name" => TEXT, "email" => TEXT,
		"address1" => TEXT, "address2" => TEXT, "city" => TEXT,
		"province" => TEXT, "state_id" => INTEGER, "state_code" => TEXT, "zip" => TEXT,
		"country_id" => INTEGER, "country_code" => TEXT, "phone" => TEXT, "daytime_phone" => TEXT,
		"evening_phone" => TEXT, "cell_phone" => TEXT, "fax" => TEXT,
		"delivery_name" => TEXT, "delivery_first_name" => TEXT, "delivery_last_name" => TEXT,
		"delivery_company_id" => INTEGER, "delivery_company_name" => TEXT, "delivery_email" => TEXT,
		"delivery_address1" => TEXT, "delivery_address2" => TEXT, "delivery_city" => TEXT,
		"delivery_province" => TEXT, "delivery_state_id" => INTEGER, "delivery_state_code" => TEXT,
		"delivery_zip" => TEXT,
		"delivery_country_id" => INTEGER, "delivery_country_code" => TEXT, 
		"delivery_phone" => TEXT, "delivery_daytime_phone" => TEXT,
		"delivery_evening_phone" => TEXT, "delivery_cell_phone" => TEXT, "delivery_fax" => TEXT,
		"cc_name" => TEXT, "cc_first_name" => TEXT, "cc_last_name" => TEXT,
		"cc_number" => TEXT, "cc_start_date" => DATETIME, "cc_expiry_date" => DATETIME,
		"cc_type" => INTEGER, "cc_issue_number" => INTEGER, "cc_security_code" => TEXT,
		"pay_without_cc" => TEXT
	);
  //success_message, pending_message, error_message
	$r = new VA_Record($table_prefix . "orders");

	$r->add_where("order_id", INTEGER);
	$r->add_textbox("parent_order_id", INTEGER);
	$r->add_textbox("invoice_number", TEXT);
	$r->change_property("invoice_number", USE_SQL_NULL, false);
	$r->add_textbox("transaction_id", TEXT);

	$r->add_textbox("currency_rate", NUMBER);
	$r->add_textbox("currency_rate", NUMBER);
	$r->add_textbox("order_status", INTEGER);
	$r->set_value("order_status", 0);
	$r->add_textbox("total_buying", NUMBER);
	$r->add_textbox("total_merchants_commission", NUMBER);
	$r->add_textbox("total_affiliate_commission", NUMBER);

	$r->add_textbox("goods_total", NUMBER);
	$r->add_textbox("goods_incl_tax", NUMBER);
	$r->add_textbox("goods_points_amount", NUMBER);
	$r->add_textbox("total_quantity", INTEGER);
	$r->add_textbox("weight_total", NUMBER);
	$r->add_textbox("total_discount", NUMBER);
	$r->add_textbox("total_discount_tax", NUMBER);

	$r->add_textbox("shipping_type_id", INTEGER);
	$r->add_textbox("shipping_type_code", TEXT);
	$r->add_textbox("shipping_type_desc", TEXT);
	$r->add_textbox("shipping_cost", NUMBER);
	$r->add_textbox("shipping_taxable", INTEGER);
	$r->add_textbox("shipping_points_amount", NUMBER);
	$r->add_textbox("shipping_tracking_id", TEXT);
	$r->add_textbox("shipping_expecting_date", DATETIME);

	$r->add_textbox("properties_total", NUMBER);
	$r->add_textbox("properties_taxable", NUMBER);

	$r->add_textbox("credit_amount", NUMBER); // for future use in case user can pay recurring orders with credits
	$r->add_textbox("processing_fee", NUMBER);
	$r->add_textbox("order_total", NUMBER);
	$r->add_textbox("total_points_amount", NUMBER);
	$r->add_textbox("total_reward_points", NUMBER);
	$r->add_textbox("total_reward_credits", NUMBER);
	$r->add_textbox("order_placed_date", DATETIME);

	$r->add_textbox("modified_date", DATETIME);

	$r->add_textbox("is_placed", INTEGER);
	$r->set_value("is_placed", 0);
	$r->add_textbox("is_exported", INTEGER);
	$r->set_value("is_exported", 0);
	$r->add_textbox("is_call_center", INTEGER);
	$r->set_value("is_call_center", 0);
	$r->add_textbox("is_recurring", INTEGER);
	$r->set_value("is_recurring", 1);

	foreach ($order_params as $parameter_name => $parameter_type) {
		$r->add_textbox($parameter_name, $parameter_type);
	}
	$r->change_property("visit_id", USE_SQL_NULL, false);
	$r->change_property("affiliate_code", USE_SQL_NULL, false);
	$r->change_property("affiliate_user_id", USE_SQL_NULL, false);
	$r->change_property("keywords", USE_SQL_NULL, false);
	$r->change_property("name", USE_SQL_NULL, false);
	$r->change_property("first_name", USE_SQL_NULL, false);
	$r->change_property("last_name", USE_SQL_NULL, false);
	$r->change_property("email", USE_SQL_NULL, false);

	// order items fields
	$oi = new VA_Record($table_prefix . "orders_items");
	$oi->add_where("order_item_id", INTEGER);
	$oi->add_textbox("parent_order_item_id", INTEGER);
	$oi->add_textbox("order_id", INTEGER);
	$oi->add_textbox("site_id", INTEGER);
	$oi->add_textbox("item_id", INTEGER);
	$oi->add_textbox("parent_item_id", INTEGER);
	$oi->add_textbox("user_id", INTEGER);
	$oi->add_textbox("user_type_id", INTEGER);
	$oi->add_textbox("subscription_id", INTEGER);
	$oi->change_property("subscription_id", USE_SQL_NULL, false);
	$oi->add_textbox("cart_item_id", INTEGER);
	$oi->change_property("cart_item_id", USE_SQL_NULL, false);
	$oi->add_textbox("item_user_id", INTEGER);
	$oi->change_property("item_user_id", USE_SQL_NULL, false);
	$oi->add_textbox("affiliate_user_id", INTEGER);
	$oi->change_property("affiliate_user_id", USE_SQL_NULL, false);
	$oi->add_textbox("friend_user_id", INTEGER);
	$oi->change_property("friend_user_id", USE_SQL_NULL, false);
	$oi->add_textbox("item_type_id", INTEGER);
	$oi->add_textbox("item_code", TEXT);
	$oi->change_property("item_code", USE_SQL_NULL, false);
	$oi->add_textbox("manufacturer_code", TEXT);
	$oi->add_textbox("supplier_id", INTEGER);
	$oi->change_property("supplier_id", USE_SQL_NULL, false);
	$oi->add_textbox("coupons_ids", TEXT);
	$oi->add_textbox("item_status", INTEGER);
	$oi->set_value("item_status", 0);
	$oi->add_textbox("item_name", TEXT);
	$oi->add_textbox("item_properties", TEXT);
	$oi->add_textbox("buying_price", NUMBER);
	$oi->add_textbox("real_price", NUMBER);
	$oi->add_textbox("discount_amount", NUMBER);
	$oi->add_textbox("price", NUMBER);
	$oi->add_textbox("tax_free", INTEGER);
	$oi->add_textbox("tax_percent", NUMBER);
	$oi->add_textbox("points_price", NUMBER);
	$oi->add_textbox("reward_points", NUMBER);
	$oi->add_textbox("reward_credits", NUMBER);
	$oi->add_textbox("merchant_commission", NUMBER);
	$oi->add_textbox("affiliate_commission", NUMBER);
	$oi->add_textbox("weight", NUMBER);
	$oi->add_textbox("quantity", NUMBER);
	$oi->add_textbox("downloadable", NUMBER);
	$oi->add_textbox("is_shipping_free", INTEGER);
	$oi->add_textbox("shipping_cost", NUMBER);
	$oi->add_textbox("shipping_expecting_date", DATETIME);
	// subscription fields
	$oi->add_textbox("is_subscription", INTEGER);
	$oi->add_textbox("subscription_period",   INTEGER);
	$oi->add_textbox("subscription_interval", INTEGER);
	$oi->add_textbox("subscription_suspend",  INTEGER);
	// recurring fields
	$oi->add_textbox("is_recurring", INTEGER);
	$oi->set_value("is_recurring", 0);
	$oi->add_textbox("recurring_price", NUMBER);
	$oi->add_textbox("recurring_period", INTEGER);
	$oi->add_textbox("recurring_interval", INTEGER);
	$oi->add_textbox("recurring_payments_total", INTEGER);
	$oi->add_textbox("recurring_payments_made", INTEGER);
	$oi->add_textbox("recurring_payments_failed", INTEGER);
	$oi->add_textbox("recurring_end_date", DATETIME);
	$oi->add_textbox("recurring_last_payment", DATETIME);
	$oi->add_textbox("recurring_next_payment", DATETIME);
	$oi->add_textbox("recurring_plan_payment", DATETIME);

	// order items properties fields
	$oip = new VA_Record($table_prefix . "orders_items_properties");
	$oip->add_textbox("order_id", INTEGER);
	$oip->add_textbox("order_item_id", INTEGER);
	$oip->add_textbox("property_id", INTEGER);
	$oip->add_textbox("property_values_ids", TEXT);
	$oip->add_textbox("property_name", TEXT);
	$oip->add_textbox("property_value", TEXT);
	$oip->add_textbox("additional_price", FLOAT);
	$oip->add_textbox("additional_weight", FLOAT);

	// orders properies fields
	$op = new VA_Record($table_prefix . "orders_properties");
	$op->add_textbox("order_id", INTEGER);
	$op->add_textbox("property_id", INTEGER);
	$op->add_textbox("property_order", INTEGER);
	$op->add_textbox("property_type", INTEGER);
	$op->add_textbox("property_name", TEXT);
	$op->add_textbox("property_value", TEXT);
	$op->add_textbox("property_price", FLOAT);
	$op->add_textbox("property_points_amount", FLOAT);
	$op->add_textbox("property_weight", FLOAT);
	$op->add_textbox("tax_free", INTEGER);

	$error_message = ""; $success_message = "";
	$last_order_id = ""; $order_items = array();

	$sql  = " SELECT oi.* ";
	$sql .= " FROM (" . $table_prefix . "orders_items oi ";
	$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON os.status_id=oi.item_status) ";
	$sql .= " WHERE oi.is_recurring=1 ";
	$sql .= " AND os.paid_status=1 ";
	$sql .= " AND oi.recurring_next_payment IS NOT NULL ";
	$sql .= " AND oi.recurring_next_payment<=" . $dbi->tosql(va_time(), DATE);
	$sql .= " ORDER BY oi.order_id ";
	$dbi->query($sql);
	if ($dbi->next_record()) {
		$last_order_id = $dbi->f("order_id");
		do {
			$order_id = $dbi->f("order_id");

			if ($order_id != $last_order_id && sizeof($order_items) > 0) {
				recurring_order($last_order_id, $order_items);
			}

			$order_item_id = $dbi->f("order_item_id");
			$recurring_period = $dbi->f("recurring_period");
			$recurring_interval = $dbi->f("recurring_interval");
			$recurring_payments_total = $dbi->f("recurring_payments_total");
			$recurring_payments_made = $dbi->f("recurring_payments_made");
			$recurring_payments_failed = $dbi->f("recurring_payments_failed");
			$recurring_end_date = $dbi->f("recurring_end_date", DATETIME);
			$recurring_last_payment = $dbi->f("recurring_last_payment", DATETIME);
			$recurring_next_payment = $dbi->f("recurring_next_payment", DATETIME);
			$recurring_plan_payment = $dbi->f("recurring_plan_payment", DATETIME);

			$recurring_plan_ts = 0; $recurring_next_ts = 0; $recurring_end_ts = 0;
			if (is_array($recurring_next_payment)) {
				$recurring_next_ts = mktime (0, 0, 0, $recurring_next_payment[MONTH], $recurring_next_payment[DAY], $recurring_next_payment[YEAR]);
			}
			if (is_array($recurring_plan_payment)) {
				$recurring_plan_ts = mktime (0, 0, 0, $recurring_plan_payment[MONTH], $recurring_plan_payment[DAY], $recurring_plan_payment[YEAR]);
			}
			if (is_array($recurring_end_date)) {
				$recurring_end_ts = mktime (0, 0, 0, $recurring_end_date[MONTH], $recurring_end_date[DAY], $recurring_end_date[YEAR]);
			}
			if ((!$recurring_payments_total || $recurring_payments_total > $recurring_payments_made) &&
				(!$recurring_end_ts || $recurring_end_ts >= $recurring_plan_ts))
			{
				$order_items[] = array(
					"order_id" => $dbi->f("order_id"), 
					"site_id" => $dbi->f("site_id"), 
					"order_item_id" => $dbi->f("order_item_id"),
					"item_id" => $dbi->f("item_id"),
					"user_id" => $dbi->f("user_id"),
					"user_type_id" => $dbi->f("user_type_id"),
					"subscription_id" => $dbi->f("subscription_id"),
					"cart_item_id" => $dbi->f("cart_item_id"),
					"item_user_id" => $dbi->f("item_user_id"),
					"affiliate_user_id" => $dbi->f("affiliate_user_id"),
					"friend_user_id" => $dbi->f("friend_user_id"),
					"parent_item_id" => $dbi->f("parent_item_id"),
					"item_type_id" => $dbi->f("item_type_id"),
					"item_code" => $dbi->f("item_code"),
					"manufacturer_code" => $dbi->f("manufacturer_code"),
					"supplier_id" => $dbi->f("supplier_id"),
					"coupons_ids" => $dbi->f("coupons_ids"),
					"item_name" => $dbi->f("item_name"),
					"item_properties" => $dbi->f("item_properties"),
					"buying_price" => $dbi->f("buying_price"),
					"real_price" => $dbi->f("real_price"),
					"discount_amount" => $dbi->f("discount_amount"),
					"points_price" => $dbi->f("points_price"),
					"reward_points" => $dbi->f("reward_points"),
					"reward_credits" => $dbi->f("reward_credits"),
					"affiliate_commission" => $dbi->f("affiliate_commission"),
					"merchant_commission" => $dbi->f("merchant_commission"),
					"price" => $dbi->f("price"),
					"tax_free" => $dbi->f("tax_free"),
					"tax_percent" => 0,
					"weight" => $dbi->f("weight"),
					"quantity" => $dbi->f("quantity"),
					"is_shipping_free" => $dbi->f("is_shipping_free"),
					"shipping_cost" => $dbi->f("shipping_cost"),
					"downloadable" => $dbi->f("downloadable"),
					"is_subscription" => $dbi->f("is_subscription"),
					"subscription_period" => $dbi->f("subscription_period"),
					"subscription_interval" => $dbi->f("subscription_interval"),
					"subscription_suspend" => $dbi->f("subscription_suspend"),
					"recurring_price" => $dbi->f("recurring_price"),
					"recurring_period" => $recurring_period,
					"recurring_interval" => $recurring_interval,
					"recurring_payments_total" => $recurring_payments_total,
					"recurring_payments_made" => $recurring_payments_made,
					"recurring_payments_failed" => $recurring_payments_failed,
					"recurring_end_date" => $recurring_end_date,
					"recurring_last_payment" => $recurring_last_payment,
					"recurring_next_payment" => $recurring_next_payment,
					"recurring_plan_payment" => $recurring_plan_payment,
				);
			} else {
				// cancel recurring payments if the end date passed or all payments made
				$sql  = " UPDATE " . $table_prefix . "orders_items ";
				$sql .= " SET is_recurring=0 ";
				$sql .= " WHERE order_item_id=" . $db->f($order_item_id, INTEGER);
				$db->query($sql);
			}

			$last_order_id = $order_id;
		} while ($dbi->next_record());

		// add last order data
		if (sizeof($order_items) > 0) {
			recurring_order($last_order_id, $order_items);
		}
	}

	if ($orders_added) {
		$recurring_success = str_replace("{orders_added}", $orders_added, ORDERS_ADDED_RECURRING_MSG);
	} else if (!$recurring_errors) {
		$recurring_success = NO_RECURRING_ORDER_AVAILABLE_MSG;
	}


function recurring_order($parent_order_id)
{
	global $db, $table_prefix, $db_type;
	global $t, $r, $order_params, $oi, $op, $oip, $order_items;
	global $eol, $settings, $recipients, $email_headers, $tax_prices_type_global;
	global $root_folder_path, $datetime_show_format, $vat_validation, $vat_obligatory_countries;
	global $recurring_errors, $recurring_success, $orders_added;

	$recurring_error = false;

	$sql = " SELECT * FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($parent_order_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$r->set_value("parent_order_id", $parent_order_id);
		foreach ($order_params as $parameter_name => $parameter_type) {
			$r->set_value($parameter_name, $db->f($parameter_name, $parameter_type));
		}
		$tax_prices_type = $r->get_value("tax_prices_type");
		if (!strlen($tax_prices_type)) {
			$tax_prices_type = $tax_prices_type_global;
			$r->set_value("tax_prices_type", $tax_prices_type);
		}

		$parent_shipping_type_id = $db->f("shipping_type_id");
		$email = $r->get_value("email");
		$delivery_email = $r->get_value("delivery_email");;
		$user_email = strlen($email) ? $email : $delivery_email;
		$user_id = $r->get_value("user_id");
		$user_type_id = $r->get_value("user_type_id");
		

		$r->set_value("coupons_ids", "");
		// check payment settings
		$sql  = " SELECT * FROM " . $table_prefix . "payment_systems ";
		$sql .= " WHERE payment_id=" . $db->tosql($r->get_value("payment_id"), INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$payment_name = $db->f("payment_name");
			$processing_time = $db->f("processing_time");
			$processing_fee = $db->f("processing_fee");
			$fee_type = $db->f("fee_type");
			$fee_min_goods = $db->f("fee_min_goods");
			$fee_max_goods = $db->f("fee_max_goods");
			$recurring_method = $db->f("recurring_method");
			$is_advanced = $db->f("is_advanced");
			$advanced_url = $db->f("advanced_url");
			$advanced_php_lib = $db->f("advanced_php_lib");
			$success_status_id = $db->f("success_status_id");
			$pending_status_id = $db->f("pending_status_id");
			$failure_status_id = $db->f("failure_status_id");
			$is_active = $db->f("is_active");
			if (!$recurring_method) {
				$recurring_error = true;
				$error_subject = "RECURRING PAYMENT ERROR";
				$error_body = str_replace("{payment_name}", $payment_name, PAYMENT_SYSTEM_IS_NOT_ALLOWED_MSG) . $eol;
			}
		} else {
			$recurring_error = true;
			$error_subject = "RECURRING PAYMENT ERROR";
			$error_body = PAYMENT_SYSTEM_NOT_FOUND_MSG . $eol;
		}

		if (!$recurring_error) {

			// recurring settings
			$recurring_settings = array();
			$setting_type = "recurring_" . $r->get_value("payment_id");
			$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
			$db->query($sql);
			while($db->next_record()) {
				$recurring_settings[$db->f("setting_name")] = $db->f("setting_value");
			}
			$preserve_item_options = get_setting_value($recurring_settings, "preserve_item_options", "0");
			$preserve_cart_options = get_setting_value($recurring_settings, "preserve_cart_options", "0");
			$preserve_shipping = get_setting_value($recurring_settings, "preserve_shipping", "0");

			$default_currency_code = get_db_value("SELECT currency_code FROM ".$table_prefix."currencies WHERE is_default=1");
			$r->set_value("default_currency_code", $default_currency_code);

			$currency = get_currency($r->get_value("currency_code"));
			$r->set_value("currency_code", $currency["code"]);
			$r->set_value("currency_rate", $currency["rate"]);

			// check user tax free option
			$tax_free = false;
			if ($r->get_value("user_id")) {
				$sql  = " SELECT u.tax_free AS user_tax_free, ut.tax_free AS group_tax_free ";
				$sql .= " FROM (" . $table_prefix . "users u ";
				$sql .= " LEFT JOIN " . $table_prefix . "user_types ut ON u.user_type_id=ut.type_id) ";
				$sql .= " WHERE user_id=" . $db->tosql($r->get_value("user_id"), INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$user_tax_free = $db->f("user_tax_free");
					$group_tax_free = $db->f("group_tax_free");
					$tax_free = ($user_tax_free || $group_tax_free);
				}
			}

			// check main delivery details
			if ($r->get_value("delivery_country_id")) {
				$country_id = $r->get_value("delivery_country_id");
			} else {
				$country_id = $r->get_value("country_id");
			}
			if ($r->get_value("delivery_state_id")) {
				$state_id = $r->get_value("delivery_state_id");
			} else {
				$state_id = $r->get_value("state_id");
			}
			if ($r->get_value("delivery_zip")) {
				$postal_code = $r->get_value("delivery_zip");
			} else {
				$postal_code = $r->get_value("zip");
			}
			// check $country_code and $state_code variables
			$sql = "SELECT country_code FROM " . $table_prefix . "countries WHERE country_id=".$db->tosql($country_id,INTEGER,true,false);
			$country_code = get_db_value($sql);
			$sql = "SELECT state_code FROM " . $table_prefix . "states WHERE state_id=".$db->tosql($state_id,INTEGER,true,false);
			$state_code = get_db_value($sql);

			// initialize array for cart custom fields
			$custom_options = array();
			// VAT validation
			if (!$tax_free && isset($vat_validation) && $vat_validation) {
				$sql  = " SELECT * FROM " . $table_prefix . "orders_properties ";
				$sql .= " WHERE order_id=" . $db->tosql($parent_order_id, INTEGER);
				$sql .= " AND property_type IN (1,2,3)";
				$sql .= " AND property_name LIKE '%VAT%' ";
				$sql .= " ORDER BY property_type ";
				$db->query($sql);
				if ($db->next_record()) {
					$property_id = $db->f("property_id");
					$property_type = $db->f("property_type");
					$property_order = $db->f("property_order");
					$property_name = $db->f("property_name");
					$vat_number = $db->f("property_value");

					// vat_check 
					if ($vat_number) {
						$is_vat_valid = vat_check($vat_number, $country_code);
						if ($is_vat_valid) {
							if (!isset($vat_obligatory_countries) || !is_array($vat_obligatory_countries)
							|| !in_array(strtoupper($country_code), $vat_obligatory_countries)) {
								$tax_free = true; 
								// Add VAT Number custom field to the order even without preserve option set
								$custom_options[$property_id] = array(
									"type" => $property_type, "order" => $property_order, "name" => $property_name, 
									"value" => $vat_number, "price" => 0, "tax_free" => 0,
									"points_amount" => 0, "weight" => 0
								);
							}
						}
					}
				}
			}
 
			// get taxes rates
			$tax_available = false; $tax_percent_sum = 0; $tax_names = ""; $tax_cost = 0;
			$tax_rates = get_tax_rates(true, $country_id, $state_id, $postal_code);
			if (sizeof($tax_rates) > 0) {
				$tax_available = true;
				foreach ($tax_rates as $tax_id => $tax_info) {
					$tax_percent_sum += $tax_info["tax_percent"];
					if ($tax_names) { $tax_names .= " & "; }
					$tax_names .= get_translation($tax_info["tax_name"]);
				}
			}
			$r->set_value("tax_name", $tax_names);
			$r->set_value("tax_percent", $tax_percent_sum);

			// calculate summary for order
			$total_buying = 0;
			$goods_total = 0; $goods_incl_tax = 0; $goods_tax_total = 0;
			$goods_points_amount = 0; 
			$total_discount = 0; $total_discount_tax = 0;
			$properties_total = 0; $properties_taxable = 0;
			$order_total = 0;
			$properties_total = 0; $properties_tax_total = 0; $properties_taxable = 0;
			$shipping_items_total = 0; $total_quantity = 0; $weight_total = 0; $shipping_weight = 0; $shipping_quantity = 0;
			//$total_discount = 0; $total_discount_tax = 0;
			//$total_points_amount = $goods_points_amount + $properties_points_amount + $shipping_points_amount;
	  
			$total_reward_points = 0; $total_reward_credits = 0; 					
			$total_merchants_commission = 0; $total_affiliate_commission = 0;

			for($i = 0; $i < sizeof($order_items); $i++)
			{
				$order_item = $order_items[$i];
	  
				$item_type_id = $order_item["item_type_id"];
				$buying_price = $order_item["buying_price"];

				$points_price = $order_item["points_price"];
				$reward_points = $order_item["reward_points"];
				$reward_credits = $order_item["reward_credits"];
				$affiliate_commission = $order_item["affiliate_commission"];
				$merchant_commission = $order_item["merchant_commission"];
				$price = $order_item["price"];
				$recurring_price = $order_item["recurring_price"];
				$price += $recurring_price;
				$item_tax_free = $order_item["tax_free"];
				$item_tax_percent = $order_item["tax_percent"];
				if ($tax_free) { 
					$item_tax_free = $tax_free; 
					$order_items[$i]["tax_free"] = $tax_free;
					$order_items[$i]["tax_percent"] = 0;
				}

				$weight = $order_item["weight"];
				$quantity = $order_item["quantity"];
				$shipping_cost = $order_item["shipping_cost"];
				$is_shipping_free = $order_item["is_shipping_free"];
				if ($is_shipping_free) { $shipping_cost = 0; }
	    
				$total_buying += ($buying_price * $quantity);
				$goods_points_amount += $points_price * $quantity;
				$total_reward_points += $reward_points * $quantity;
				$total_reward_credits += $reward_credits * $quantity;
				$total_merchants_commission += ($buying_price * $merchant_commission);
				$total_affiliate_commission += ($buying_price * $affiliate_commission);
				$item_total = $price * $quantity;
				$goods_total += $item_total;
	  
				if ($tax_available && !$item_tax_free) {
					$item_tax_total = get_tax_amount($tax_rates, $item_type_id, $item_total, $item_tax_free, $item_tax_percent, "", 1, $tax_prices_type);
					$goods_tax_total += $item_tax_total;
					$order_items[$i]["tax_free"] = $item_tax_free;
					$order_items[$i]["tax_percent"] = $item_tax_percent;
				}
	    
				$total_quantity += $quantity;
				$weight_total += ($weight * $quantity);
				if (!$is_shipping_free) {
					$shipping_quantity += $quantity;
					$shipping_items_total += ($shipping_cost * $quantity); 
					$shipping_weight += ($weight * $quantity);
				}
	  
			}

			// cart properties
			$custom_options = array();
			$sql  = " SELECT * FROM " . $table_prefix . "orders_properties ";
			$sql .= " WHERE order_id=" . $db->tosql($parent_order_id, INTEGER);
			$sql .= " AND (property_type IN (2,3,4)";
			if ($preserve_cart_options) {
				$sql .= " OR property_type=1 ";
			}
			$sql .= " )";
			$db->query($sql);
			while ($db->next_record()) {
				$property_id = $db->f("property_id");
				$property_type = $db->f("property_type");
				$property_order = $db->f("property_order");
				$property_name = $db->f("property_name");
				$property_value = $db->f("property_value");
				$property_price = $db->f("property_price");
				$property_tax_free = $db->f("tax_free");
				if ($tax_free) { $property_tax_free = true; }
				$property_points_amount = $db->f("property_points_amount");
				$property_weight = $db->f("property_weight");

				$property_tax_percent = $tax_percent_sum;
				$weight_total += $property_weight;
				$properties_total += $property_price;
				if (!$property_tax_free) {
					$property_tax = get_tax_amount($tax_rates, 0, $property_price, $property_tax_free, $property_tax_percent, "", 1, $tax_prices_type);
					$properties_taxable += $property_price;
					$properties_tax_total += $property_tax;
				}
//todo
//$default_tax_rates = "", $return_type = 1, $tax_prices_type

				$custom_options[$property_id] = array(
					"type" => $property_type, "order" => $property_order, "name" => $property_name, 
					"value" => $property_value, "price" => $property_price, "tax_free" => $property_tax_free,
					"points_amount" => $property_points_amount, "weight" => $property_weight
				);
			}
			// check for shipping method
			$goods_total_full = $goods_total;
			$shipping_type_id = ""; $shipping_type_code = ""; $shipping_type_desc = ""; $tare_weight = 0; $shipping_cost = 0;
			$shipping_taxable = 0; $shipping_tax = 0; $shipping_points_amount = 0; $shipping_time = 0;
			if ($parent_shipping_type_id && $shipping_quantity && $preserve_shipping) {
				$sql  = " SELECT st.shipping_type_id, st.shipping_module_id, st.shipping_type_code, st.shipping_type_desc,  ";
				$sql .= " st.cost_per_order, st.cost_per_product, st.cost_per_weight, st.tare_weight, st.is_taxable, st.shipping_time ";
				$sql .= " FROM (((" . $table_prefix . "shipping_types st ";
				$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_countries stc ON st.shipping_type_id=stc.shipping_type_id) ";
				$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_states stt ON st.shipping_type_id=stt.shipping_type_id) ";
				$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_users stu ON st.shipping_type_id=stu.shipping_type_id) ";
				$sql .= " WHERE st.is_active=1 ";
				$sql .= " AND (st.countries_all=1 OR stc.country_id=" . $db->tosql($country_id, INTEGER, true, false) . ") ";
				$sql .= " AND (st.states_all=1 OR stt.state_id=" . $db->tosql($state_id, INTEGER, true, false) . ") ";
				$sql .= " AND (st.min_weight IS NULL OR st.min_weight<=" . $db->tosql($shipping_weight, NUMBER) . ") ";
				$sql .= " AND (st.max_weight IS NULL OR st.max_weight>=" . $db->tosql($shipping_weight, NUMBER) . ") ";
				$sql .= " AND (st.min_goods_cost IS NULL OR st.min_goods_cost<=" . $db->tosql($goods_total_full, NUMBER) . ") ";
				$sql .= " AND (st.max_goods_cost IS NULL OR st.max_goods_cost>=" . $db->tosql($goods_total_full, NUMBER) . ") ";
				$sql .= " AND (st.min_quantity IS NULL OR st.min_quantity<=" . $db->tosql($shipping_quantity, NUMBER) . ") ";
				$sql .= " AND (st.max_quantity IS NULL OR st.max_quantity>=" . $db->tosql($shipping_quantity, NUMBER) . ") ";
				$sql .= " AND (st.user_types_all=1 ";
				if (strlen($user_type_id)){
					$sql .= "OR stu.user_type_id=" . $db->tosql($user_type_id, INTEGER);
				}
				$sql .= ") ";
				$sql .= " AND st.shipping_type_id=" . $db->tosql($parent_shipping_type_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$shipping_type_id = $db->f("shipping_type_id");
					$shipping_module_id = $db->f("shipping_module_id");
					$shipping_type_code = $db->f("shipping_type_code");
					$shipping_type_desc = $db->f("shipping_type_desc");
					$shipping_time = $db->f("shipping_time");
					$cost_per_order = $db->f("cost_per_order");
					$cost_per_product = $db->f("cost_per_product");
					$cost_per_weight = $db->f("cost_per_weight");
					$tare_weight = $db->f("tare_weight");
					$shipping_taxable = $db->f("is_taxable");
					$shipping_cost = $shipping_items_total + $cost_per_order + ($cost_per_product * $shipping_quantity) + ($cost_per_weight * ($shipping_weight + $tare_weight));

					$shipping_tax_free = (!$shipping_taxable || $tax_free);
					$shipping_tax = get_tax_amount($tax_rates, "shipping", $shipping_cost, $shipping_tax_free, $shipping_tax_percent, "", 1, $tax_prices_type);
					$shipping_tax_values = get_tax_amount($tax_rates, "shipping", $shipping_cost, $shipping_tax_free, $shipping_tax_percent, "", 2, $tax_prices_type);
				}
			}
			
			// calculate order total information
			$weight_total += $tare_weight;
			$tax_total = $goods_tax_total + $properties_tax_total + $shipping_tax;
			$order_total = round($goods_total, 2) + round($properties_total, 2) + round($shipping_cost, 2);
			if ($tax_prices_type != 1) {
				$order_total += round($tax_total, 2);
			}
			//$total_points_amount = $goods_points_amount + $properties_points_amount + $shipping_points_amount;
			$total_points_amount = $goods_points_amount;

			if ((strlen($fee_max_goods) && $goods_total > $fee_max_goods) || $goods_total < $fee_min_goods) {
				$processing_fee = 0;
			} else if ($fee_type == 1) {
				$processing_fee = round(($order_total * $processing_fee) / 100, 2);
			} 

			$order_total += $processing_fee;
	  
			$r->set_value("total_buying", $total_buying);
			$r->set_value("total_merchants_commission", $total_merchants_commission);
			$r->set_value("total_affiliate_commission", $total_affiliate_commission);
			$r->set_value("goods_total", $goods_total);
			$r->set_value("goods_incl_tax", $goods_incl_tax);
			$r->set_value("goods_points_amount", $goods_points_amount);

			$r->set_value("properties_total", $properties_total);
			$r->set_value("properties_taxable", $properties_taxable);

			$r->set_value("total_quantity", $total_quantity);
			$r->set_value("weight_total", $weight_total);

			$r->set_value("shipping_type_id", $shipping_type_id);
			$r->set_value("shipping_type_code", $shipping_type_code);
			$r->set_value("shipping_type_desc", $shipping_type_desc);
			$r->set_value("shipping_cost", $shipping_cost);
			$r->set_value("shipping_taxable", $shipping_taxable);
			$r->set_value("shipping_points_amount", $shipping_points_amount);
			//$handle_hours = $max_availability_time + $shipping_time + $processing_time;
			//$shipping_expecting_date = get_expecting_date($handle_hours);
			//$r->set_value("shipping_expecting_date", va_time($shipping_expecting_date));

			$r->set_value("tax_total", $tax_total);
			$r->set_value("processing_fee", $processing_fee);
			$r->set_value("order_total", $order_total);
			$r->set_value("total_points_amount",  $total_points_amount);
			$r->set_value("total_reward_points",  $total_reward_points);
			$r->set_value("total_reward_credits",  $total_reward_credits);


			// insert recurring order
			if ($db_type == "postgre") {
				$new_order_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "orders') ");
				$r->change_property("order_id", USE_IN_INSERT, true);
				$r->set_value("order_id", $new_order_id);
			}
			$order_placed_date = va_time();
			$order_placed_date_string = va_date($datetime_show_format, $order_placed_date);
			$r->set_value("order_placed_date", $order_placed_date);

			if($r->insert_record()) 
			{
				if ($db_type == "mysql") {
					$new_order_id = get_db_value(" SELECT LAST_INSERT_ID() ");
					$r->set_value("order_id", $new_order_id);
				} else if ($db_type == "access") {
					$new_order_id = get_db_value(" SELECT @@IDENTITY ");
					$r->set_value("order_id", $new_order_id);
				} else if ($db_type == "db2") {
					$new_order_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "orders FROM " . $table_prefix . "orders");
					$r->set_value("order_id", $new_order_id);
				}
				$vc = md5($new_order_id . $order_placed_date[3].$order_placed_date[4].$order_placed_date[5]);

				// generate and update invoice number
				$invoice_number = generate_invoice_number($new_order_id);
				$variables["invoice_number"] = $invoice_number;
				$sql  = " UPDATE " . $table_prefix . "orders ";
				$sql .= " SET invoice_number=" . $db->tosql($invoice_number, TEXT);
				$sql .= " WHERE order_id=" . $db->tosql($new_order_id, INTEGER);
				$db->query($sql);

				// save tax rates for submitted order
				if ($tax_available && is_array($tax_rates)) {
					$ot = new VA_Record($table_prefix . "orders_taxes");
					$ot->add_where("order_tax_id", INTEGER);
					$ot->add_textbox("order_id", INTEGER);
					$ot->set_value("order_id", $new_order_id);
					$ot->add_textbox("tax_id", INTEGER);
					$ot->add_textbox("tax_name", TEXT);
					$ot->add_textbox("tax_percent", FLOAT);
					$ot->add_textbox("shipping_tax_percent", FLOAT);

					$oit = new VA_Record($table_prefix . "orders_items_taxes");
					$oit->add_textbox("order_tax_id", INTEGER);
					$oit->add_textbox("item_type_id", INTEGER);
					$oit->add_textbox("tax_percent", FLOAT);

					foreach ($tax_rates as $tax_id => $tax_rate) {
						if ($db_type == "postgre") {
							$order_tax_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "orders_taxes') ");
							$r->change_property("order_tax_id", USE_IN_INSERT, true);
							$r->set_value("order_tax_id", $order_tax_id);
						}
						$ot->set_value("tax_id", $tax_id);
						$ot->set_value("tax_name", $tax_rate["tax_name"]);
						$ot->set_value("tax_percent", $tax_rate["tax_percent"]);
						$ot->set_value("shipping_tax_percent", $tax_rate["shipping_tax_percent"]);
						if ($ot->insert_record()) {
							// save taxes for item types if they available
							$tax_types = isset($tax_rate["types"]) ? $tax_rate["types"] : "";
							if (is_array($tax_types)) {
								if ($db_type == "mysql") {
									$order_tax_id = get_db_value(" SELECT LAST_INSERT_ID() ");
								} elseif ($db_type == "access") {
									$order_tax_id = get_db_value(" SELECT @@IDENTITY ");
								} elseif ($db_type == "db2") {
									$order_tax_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "orders_taxes FROM " . $table_prefix . "orders_taxes");
								}
								$oit->set_value("order_tax_id", $order_tax_id);
								foreach ($tax_types as $item_type_id => $item_tax_percent) {
									$oit->set_value("item_type_id", $item_type_id);
									$oit->set_value("tax_percent", $item_tax_percent);
									$oit->insert_record();
								}
							}
						}
					} 
				} // end of saving order taxes rules

				// add orders items
				for($i = 0; $i < sizeof($order_items); $i++)
				{
					$order_item = $order_items[$i];
					$order_item_id = $order_item["order_item_id"];

					$oi->set_value("parent_order_item_id", $order_item_id);
					$oi->set_value("order_id", $new_order_id);

					$oi->set_value("site_id", $order_item["site_id"]);
					$oi->set_value("item_id", $order_item["item_id"]);
					$oi->set_value("parent_item_id", $order_item["parent_item_id"]);
					$oi->set_value("user_id", $order_item["user_id"]);
					$oi->set_value("user_type_id", $order_item["user_type_id"]);
					$oi->set_value("subscription_id", $order_item["subscription_id"]);
					$oi->set_value("cart_item_id", $order_item["cart_item_id"]);
					$oi->set_value("item_user_id", $order_item["item_user_id"]);
					$oi->set_value("affiliate_user_id", $order_item["affiliate_user_id"]);
					$oi->set_value("friend_user_id", $order_item["friend_user_id"]);
					$oi->set_value("item_type_id", $order_item["item_type_id"]);
					$oi->set_value("item_code", $order_item["item_code"]);
					$oi->set_value("manufacturer_code", $order_item["manufacturer_code"]);
					$oi->set_value("supplier_id", $order_item["supplier_id"]);
					//$oi->set_value("coupons_ids", $order_item["coupons_ids"]);
					$oi->set_value("item_name", $order_item["item_name"]);
					$oi->set_value("item_properties", $order_item["item_properties"]);
					$oi->set_value("buying_price", $order_item["buying_price"]);
					$oi->set_value("real_price", $order_item["real_price"]);
					$oi->set_value("discount_amount", $order_item["discount_amount"]);
					$oi->set_value("price", $order_item["price"] + $order_item["recurring_price"]);
					$oi->set_value("tax_free", $order_item["tax_free"]);
					$oi->set_value("tax_percent", $order_item["tax_percent"]);
					$oi->set_value("points_price", $order_item["points_price"]);
					$oi->set_value("reward_points", $order_item["reward_points"]);
					$oi->set_value("reward_credits", $order_item["reward_credits"]);
					$oi->set_value("merchant_commission", $order_item["merchant_commission"]);
					$oi->set_value("affiliate_commission", $order_item["affiliate_commission"]);
					$oi->set_value("weight", $order_item["weight"]);
					$oi->set_value("quantity", $order_item["quantity"]);
					$oi->set_value("is_shipping_free", $order_item["is_shipping_free"]);
					$oi->set_value("shipping_cost", $order_item["shipping_cost"]);
					$oi->set_value("downloadable", $order_item["downloadable"]);
					$oi->set_value("is_subscription", $order_item["is_subscription"]);
					$oi->set_value("subscription_period", $order_item["subscription_period"]);
					$oi->set_value("subscription_interval", $order_item["subscription_interval"]);
					$oi->set_value("subscription_suspend", $order_item["subscription_suspend"]);
					//$oi->set_value("shipping_expecting_date", "");

					if ($db_type == "postgre") {
						$new_order_item_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "orders_items') ");
						$oi->change_property("order_item_id", USE_IN_INSERT, true);
						$oi->set_value("order_item_id", $new_order_item_id);
					}
	      
					if($oi->insert_record())
					{
						if($db_type == "mysql") {
							$new_order_item_id = get_db_value(" SELECT LAST_INSERT_ID() ");
							$oi->set_value("order_item_id", $new_order_item_id);
						} else if ($db_type == "access") {
							$new_order_item_id = get_db_value(" SELECT @@IDENTITY ");
							$oi->set_value("order_item_id", $new_order_item_id);
						}
						// add product options if preserve option is set
						if ($preserve_item_options) {
							$items_properties = array(); // clear array
							$sql  = " SELECT * FROM " . $table_prefix . "orders_items_properties ";
							$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
							$db->query($sql);
							while ($db->next_record()) {
								$items_properties[]	= array(
									"property_id" => $db->f("property_id"), 
									"property_values_ids" => $db->f("property_values_ids"), 
									"property_name" => $db->f("property_name"), 
									"property_value" => $db->f("property_value"), 
									"additional_price" => $db->f("additional_price"), 
									"additional_weight" => $db->f("additional_weight"), 
								);
							}
							for ($ip = 0; $ip < sizeof($items_properties); $ip++) {
								$item_property = $items_properties[$ip];
								$oip->set_value("order_id", $new_order_id);
								$oip->set_value("order_item_id", $new_order_item_id);
								$oip->set_value("property_id", $item_property["property_id"]);
								$oip->set_value("property_values_ids", $item_property["property_values_ids"]);
								$oip->set_value("property_name", $item_property["property_name"]);
								$oip->set_value("property_value", $item_property["property_value"]);
								$oip->set_value("additional_price", $item_property["additional_price"]);
								$oip->set_value("additional_weight", $item_property["additional_weight"]);
								$oip->insert_record();
							}
						}
					}
				}
				// end of adding items

				// adding order custom fields values 
				foreach ($custom_options as $property_id => $property_info) {
					$property_type = $property_info["type"];
					$property_order = $property_info["order"];
					$property_name = $property_info["name"];
					$property_value = $property_info["value"];
					$property_weight = $property_info["weight"];
					$property_price = $property_info["price"];
					$property_tax_free = $property_info["tax_free"];
					$property_points_amount = $property_info["points_amount"];

					$t->set_var("field_name_" . $property_id, $property_name);
					$t->set_var("field_value_" . $property_id, $property_value);
					$t->set_var("field_price_" . $property_id, $property_price);
					$t->set_var("field_" . $property_id, $property_value);
					$op->set_value("order_id", $new_order_id);
					$op->set_value("property_id", $property_id);
					$op->set_value("property_order", $property_order);
					$op->set_value("property_type", $property_type);
					$op->set_value("property_name", $property_name);
					$op->set_value("property_value", $property_value);
					$op->set_value("property_price", $property_price);
					$op->set_value("property_points_amount", $property_points_amount);
					$op->set_value("property_weight", $property_weight);
					$op->set_value("tax_free", $property_tax_free);

					$op->insert_record();
				}
				// end of adding custom order values 

				$new_status_id = get_setting_value($recurring_settings, "new_status_id", "");
				$recurring_attempts = get_setting_value($recurring_settings, "recurring_attempts", 3);
				$recurring_next_attempt = get_setting_value($recurring_settings, "recurring_next_attempt", 1);
      
				// update recurring order status
				if ($new_status_id) {
					update_order_status($new_order_id, $new_status_id, false, "", $status_error);
				}

				// prepare data for sending notifications
				$r->set_parameters();
				$t->set_var("vc", $vc);

				// check notification for order creation
				$admin_notify_new = get_setting_value($recurring_settings, "admin_notify_new", 0);
				$user_notify_new  = get_setting_value($recurring_settings, "user_notify_new", 0);
				$admin_sms_new    = get_setting_value($recurring_settings, "admin_sms_new", 0);
				$user_sms_new     = get_setting_value($recurring_settings, "user_sms_new", 0);

				if ($admin_notify_new) {
					$admin_subject = get_setting_value($recurring_settings, "admin_mail_subject_new");
					$admin_message = get_setting_value($recurring_settings, "admin_mail_body_new");
					$admin_subject = get_translation($admin_subject);
					$admin_message = get_currency_message(get_translation($admin_message), $currency);
	    
					$t->set_block("admin_subject", $admin_subject);
					$t->set_block("admin_message", $admin_message);
	    
					$mail_to = get_setting_value($recurring_settings, "admin_mail_to_new", $settings["admin_email"]);
					$mail_to = str_replace(";", ",", $mail_to);
					$email_headers = array();
					$email_headers["from"] = get_setting_value($recurring_settings, "admin_mail_from_new", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($recurring_settings, "admin_mail_cc_new");
					$email_headers["bcc"] = get_setting_value($recurring_settings, "admin_mail_bcc_new");
					$email_headers["reply_to"] = get_setting_value($recurring_settings, "admin_mail_reply_to_new");
					$email_headers["return_path"] = get_setting_value($recurring_settings, "admin_mail_return_path_new");
					$email_headers["mail_type"] = get_setting_value($recurring_settings, "admin_mail_type_new");
	    
					set_basket_tag($new_order_id, $email_headers["mail_type"], $admin_message);
	      
					$t->parse("admin_subject", false);
					$t->parse("admin_message", false);
					$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
					va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers);
				}

				if ($user_notify_new)
				{
					$user_subject = get_setting_value($recurring_settings, "user_mail_subject_new");
					$user_message = get_setting_value($recurring_settings, "user_mail_body_new");
					$user_subject = get_translation($user_subject);
					$user_message = get_currency_message(get_translation($user_message), $currency);

					$t->set_block("user_subject", $user_subject);
					$t->set_block("user_message", $user_message);

					$email_headers = array();
					$email_headers["from"] = get_setting_value($recurring_settings, "user_mail_from_new", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($recurring_settings, "user_mail_cc_new");
					$email_headers["bcc"] = get_setting_value($recurring_settings, "user_mail_bcc_new");
					$email_headers["reply_to"] = get_setting_value($recurring_settings, "user_mail_reply_to_new");
					$email_headers["return_path"] = get_setting_value($recurring_settings, "user_mail_return_path_new");
					$email_headers["mail_type"] = get_setting_value($recurring_settings, "user_mail_type_new");

					set_basket_tag($new_order_id, $email_headers["mail_type"], $user_message);
	  
					$t->parse("user_subject", false);
					$t->parse("user_message", false);

					$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
					va_mail($user_email, $t->get_var("user_subject"), $user_message, $email_headers);
				}		 
			
				if ($admin_sms_new)
				{
					$admin_sms_recipient  = get_setting_value($recurring_settings, "admin_sms_recipient_new", "");
					$admin_sms_originator = get_setting_value($recurring_settings, "admin_sms_originator_new", "");
					$admin_sms_message    = get_setting_value($recurring_settings, "admin_sms_message_new", "");
	    
					$t->set_block("admin_sms_recipient",  $admin_sms_recipient);
					$t->set_block("admin_sms_originator", $admin_sms_originator);
					$t->set_block("admin_sms_message",    $admin_sms_message);
	    
					set_basket_tag($new_order_id, 0, $admin_sms_message);
	    
					$t->parse("admin_sms_recipient", false);
					$t->parse("admin_sms_originator", false);
					$t->parse("admin_sms_message", false);
	    
					sms_send($t->get_var("admin_sms_recipient"), $t->get_var("admin_sms_message"), $t->get_var("admin_sms_originator"));
				}		 
	    
				if ($user_sms_new)
				{
					$user_sms_recipient  = get_setting_value($recurring_settings, "user_sms_recipient_new", $cell_phone);
					$user_sms_originator = get_setting_value($recurring_settings, "user_sms_originator_new", "");
					$user_sms_message    = get_setting_value($recurring_settings, "user_sms_message_new", "");
	    
					$t->set_block("user_sms_recipient",  $user_sms_recipient);
					$t->set_block("user_sms_originator", $user_sms_originator);
					$t->set_block("user_sms_message",    $user_sms_message);
	    
					set_basket_tag($new_order_id, 0, $user_sms_message);
	    
					$t->parse("user_sms_recipient", false);
					$t->parse("user_sms_originator", false);
					$t->parse("user_sms_message", false);
	    
					if (sms_send_allowed($t->get_var("user_sms_recipient"))) {
						sms_send($t->get_var("user_sms_recipient"), $t->get_var("user_sms_message"), $t->get_var("user_sms_originator"));
					}
				}

				$payment_type = "new";
				if ($recurring_method == 2 && $is_advanced && strlen($advanced_php_lib)) {
					$post_parameters = ""; $payment_parameters = array(); $pass_parameters = array(); $pass_data = array(); $variables = array();
					get_payment_parameters($new_order_id, $payment_parameters, $pass_parameters, $post_params, $pass_data, $variables, "recurring");

					$success_message = ""; $error_message = ""; $pending_message = ""; $transaction_id = "";
					// use foreign php library to handle transaction
					if (file_exists($root_folder_path . $advanced_php_lib)) {
						$order_step = "recurring";
						include ($root_folder_path . $advanced_php_lib);
					} else {
						$error_message = APPROPRIATE_LIBRARY_ERROR_MSG . $advanced_php_lib;
					}
	      
					if (strlen($error_message)) {
						$payment_type = "failure";
						$order_status = $failure_status_id;
					} else if (strlen($pending_message)) {
						$payment_type = "pending";
						$order_status = $pending_status_id; 
					} else {
						$payment_type = "success";
						$order_status = $success_status_id; 
					}

					$sql  = " UPDATE " . $table_prefix . "orders ";
					$sql .= " SET error_message=" . $db->tosql($error_message, TEXT) ;
					$sql .= " , pending_message=" . $db->tosql($pending_message, TEXT);
					$sql .= " , success_message=" . $db->tosql($success_message, TEXT);
					$sql .= " , transaction_id=" . $db->tosql($transaction_id, TEXT);
					$sql .= " , is_placed=1 ";
					$sql .= " WHERE order_id=" . $db->tosql($new_order_id, INTEGER) ;
					$db->query($sql);
					
					// update order status for payment
					update_order_status($new_order_id, $order_status, true, "", $status_error);

					// check notification for order payment
					$admin_notify_payment = get_setting_value($recurring_settings, "admin_notify_" . $payment_type, 0);
					$user_notify_payment  = get_setting_value($recurring_settings, "user_notify_" . $payment_type, 0);
					$admin_sms_payment    = get_setting_value($recurring_settings, "admin_sms_" . $payment_type, 0);
					$user_sms_payment     = get_setting_value($recurring_settings, "user_sms_" . $payment_type, 0);

					if ($admin_notify_payment) {
						$admin_subject = get_setting_value($recurring_settings, "admin_mail_subject_" . $payment_type);
						$admin_message = get_setting_value($recurring_settings, "admin_mail_body_" . $payment_type);
						$admin_subject = get_translation($admin_subject);
						$admin_message = get_currency_message(get_translation($admin_message), $currency);
	      
						$t->set_block("admin_subject", $admin_subject);
						$t->set_block("admin_message", $admin_message);
	      
						$mail_to = get_setting_value($recurring_settings, "admin_mail_to_" . $payment_type, $settings["admin_email"]);
						$mail_to = str_replace(";", ",", $mail_to);
						$email_headers = array();
						$email_headers["from"] = get_setting_value($recurring_settings, "admin_mail_from_" . $payment_type, $settings["admin_email"]);
						$email_headers["cc"] = get_setting_value($recurring_settings, "admin_mail_cc_" . $payment_type);
						$email_headers["bcc"] = get_setting_value($recurring_settings, "admin_mail_bcc_" . $payment_type);
						$email_headers["reply_to"] = get_setting_value($recurring_settings, "admin_mail_reply_to_" . $payment_type);
						$email_headers["return_path"] = get_setting_value($recurring_settings, "admin_mail_return_path_" . $payment_type);
						$email_headers["mail_type"] = get_setting_value($recurring_settings, "admin_mail_type_" . $payment_type);
	      
						set_basket_tag($new_order_id, $email_headers["mail_type"], $admin_message);
	        
						$t->parse("admin_subject", false);
						$t->parse("admin_message", false);
						$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
						va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers);
					}
			  
					if ($user_notify_payment)
					{
						$user_subject = get_setting_value($recurring_settings, "user_mail_subject_" . $payment_type);
						$user_message = get_setting_value($recurring_settings, "user_mail_body_" . $payment_type);
						$user_subject = get_translation($user_subject);
						$user_message = get_currency_message(get_translation($user_message), $currency);
			  
						$t->set_block("user_subject", $user_subject);
						$t->set_block("user_message", $user_message);
			  
						$email_headers = array();
						$email_headers["from"] = get_setting_value($recurring_settings, "user_mail_from_" . $payment_type, $settings["admin_email"]);
						$email_headers["cc"] = get_setting_value($recurring_settings, "user_mail_cc_" . $payment_type);
						$email_headers["bcc"] = get_setting_value($recurring_settings, "user_mail_bcc_" . $payment_type);
						$email_headers["reply_to"] = get_setting_value($recurring_settings, "user_mail_reply_to_" . $payment_type);
						$email_headers["return_path"] = get_setting_value($recurring_settings, "user_mail_return_path_" . $payment_type);
						$email_headers["mail_type"] = get_setting_value($recurring_settings, "user_mail_type_" . $payment_type);
			  
						set_basket_tag($new_order_id, $email_headers["mail_type"], $user_message);
	      
						$t->parse("user_subject", false);
						$t->parse("user_message", false);
			  
						$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
						va_mail($user_email, $t->get_var("user_subject"), $user_message, $email_headers);
					}		 
			  
					if ($admin_sms_payment)
					{
						$admin_sms_recipient  = get_setting_value($recurring_settings, "admin_sms_recipient_" . $payment_type, "");
						$admin_sms_originator = get_setting_value($recurring_settings, "admin_sms_originator_" . $payment_type, "");
						$admin_sms_message    = get_setting_value($recurring_settings, "admin_sms_message_" . $payment_type, "");
	      
						$t->set_block("admin_sms_recipient",  $admin_sms_recipient);
						$t->set_block("admin_sms_originator", $admin_sms_originator);
						$t->set_block("admin_sms_message",    $admin_sms_message);
	      
						set_basket_tag($new_order_id, 0, $admin_sms_message);
	      
						$t->parse("admin_sms_recipient", false);
						$t->parse("admin_sms_originator", false);
						$t->parse("admin_sms_message", false);
	      
						sms_send($t->get_var("admin_sms_recipient"), $t->get_var("admin_sms_message"), $t->get_var("admin_sms_originator"));
					}		 
	      
					if ($user_sms_payment)
					{
						$user_sms_recipient  = get_setting_value($recurring_settings, "user_sms_recipient_" . $payment_type, $cell_phone);
						$user_sms_originator = get_setting_value($recurring_settings, "user_sms_originator_" . $payment_type, "");
						$user_sms_message    = get_setting_value($recurring_settings, "user_sms_message_" . $payment_type, "");
	      
						$t->set_block("user_sms_recipient",  $user_sms_recipient);
						$t->set_block("user_sms_originator", $user_sms_originator);
						$t->set_block("user_sms_message",    $user_sms_message);
	      
						set_basket_tag($new_order_id, 0, $user_sms_message);
	      
						$t->parse("user_sms_recipient", false);
						$t->parse("user_sms_originator", false);
						$t->parse("user_sms_message", false);
	      
						sms_send($t->get_var("user_sms_recipient"), $t->get_var("user_sms_message"), $t->get_var("user_sms_originator"));
					}
				}

				// delete basket tags for current order
				unset_basket_tag();

				// update next payment date 
				$current_date = va_time();
				$current_ts = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY], $current_date[YEAR]);
				for($i = 0; $i < sizeof($order_items); $i++)
				{
					$is_recurring = 1;
					$order_item_id = $order_item["order_item_id"];
					$recurring_period = $order_item["recurring_period"];
					$recurring_interval = $order_item["recurring_interval"];
					$recurring_plan_payment = $order_item["recurring_plan_payment"];
					$recurring_payments_failed = $order_item["recurring_payments_failed"];

					if ($payment_type == "failure") {
						$recurring_payments_failed++;
						$recurring_next_ts = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY] + $recurring_next_attempt, $current_date[YEAR]);
						if ($recurring_payments_failed > $recurring_attempts) {
							$is_recurring = 0;
						}
						$sql  = " UPDATE " . $table_prefix . "orders_items SET ";
						$sql .= " recurring_payments_failed=" . $db->tosql($recurring_payments_failed, INTEGER) . ", ";
						$sql .= " recurring_next_payment=" . $db->tosql($recurring_next_ts, DATETIME) . ", ";
						$sql .= " is_recurring=" . $db->tosql($is_recurring, INTEGER);
						$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
					} else {

						if ($recurring_period == 1) {
							$recurring_plan_ts = mktime (0, 0, 0, $recurring_plan_payment[MONTH], $recurring_plan_payment[DAY] + $recurring_interval, $recurring_plan_payment[YEAR]);
						} elseif ($recurring_period == 2) {
							$recurring_plan_ts = mktime (0, 0, 0, $recurring_plan_payment[MONTH], $recurring_plan_payment[DAY] + ($recurring_interval * 7), $recurring_plan_payment[YEAR]);
						} elseif ($recurring_period == 3) {
							$recurring_plan_ts = mktime (0, 0, 0, $recurring_plan_payment[MONTH] + $recurring_interval, $recurring_plan_payment[DAY], $recurring_plan_payment[YEAR]);
						} else {
							$recurring_plan_ts = mktime (0, 0, 0, $recurring_plan_payment[MONTH], $recurring_plan_payment[DAY], $recurring_plan_payment[YEAR] + $recurring_interval);
						}
						$recurring_next_ts = $recurring_plan_ts;
						if ($current_ts >= $recurring_next_ts) {
							$recurring_next_ts = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY] + 1, $current_date[YEAR]);
						}
				  
						$sql  = " UPDATE " . $table_prefix . "orders_items SET ";
						$sql .= " recurring_next_payment=" . $db->tosql($recurring_next_ts, DATETIME);
						$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
					}
					// update next payment date
					$db->query($sql);
				}

			} else {
				$recurring_error = true;
				$error_subject = "RECURRING DATABASE ERROR";
				$error_body = CANT_ADD_RECURRING_MSG . $eol;
			}

/*
  //order_id, parent_order_id, transaction_id
  //success_message, pending_message, error_message
  //coupons_ids
  //total_discount, total_discount_tax
	//shipping_expecting_date
  //order_total
//*/
		}

	} else {
		$recurring_error = true;
		$error_subject = "RECURRING ORDER ERROR";
		$error_body = PARENT_ORDER_WASNT_FOUND_MSG . $eol;
	}
	
	if ($recurring_error) {
		$recurring_errors .= PARENT_ORDER_NUMBER_MSG . ": " . $parent_order_id . " " . $error_body . "<br>";

		// check for errors
		$error_body .= PARENT_ORDER_NUMBER_MSG . ": " . $parent_order_id . $eol;
		mail($recipients, $error_subject, $error_body, $email_headers);
	} else {
		$orders_added++;
	}

	// clear error flag and order_items array
	$order_items = array();
}
	
?>