<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_call.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/order_items.php");
	include_once($root_folder_path . "includes/order_links.php");
	include_once($root_folder_path . "includes/date_functions.php");
	include_once($root_folder_path . "includes/parameters.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("create_orders");

	// General settings
	$settings = va_settings();
	$tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
	
	// Order settings
	$order_info = array();
	$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='order_info'";
	if ($multisites_version) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ") ";
		$sql .= " ORDER BY site_id ASC ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$order_info[$db->f("setting_name")] = $db->f("setting_value");
	}
	
	// Shipping settings
	$shipping_settings = array();
	$sql = "SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='shipping'";
	if ( $multisites_version) {
		$sql .= " AND ( site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER,true,false). " ) ";
		$sql .= " ORDER BY site_id ASC ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$shipping_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$permissions    = get_permissions();
	$eol            = get_eol();

	$operation        = get_param("operation");
	$order_id         = get_param("order_id");
	$same_as_personal = get_param("same_as_personal");
	$shipping_type_id = get_param("shipping_type_id");
	
	$sc_errors = "";

	$t = new VA_Template($settings["admin_templates_dir"]);
	$r = new VA_Record($table_prefix . "orders");
	$r->return_page = "admin_orders.php";
	$r->add_where("order_id", INTEGER);
	$r->add_textbox("invoice_number", TEXT);
	$r->change_property("invoice_number", USE_SQL_NULL, false);
	$r->add_textbox("admin_id_added_by", INTEGER);
	$r->add_textbox("affiliate_user_id", INTEGER);
	$r->change_property("affiliate_user_id", USE_SQL_NULL, false);
	$r->add_textbox("friend_user_id", INTEGER);
	$r->change_property("friend_user_id", USE_SQL_NULL, false);


	$companies = get_db_values("SELECT company_id, company_name FROM " . $table_prefix . "companies ", array(array("", SELECT_COMPANY_MSG)));
	$states    = get_db_values("SELECT state_id, state_name FROM " . $table_prefix . "states ORDER BY state_name ", array(array(0, SELECT_STATE_MSG)));
	$countries = get_db_values("SELECT country_id, country_name FROM " . $table_prefix . "countries ORDER BY country_order, country_name ", array(array("", SELECT_COUNTRY_MSG)));
	if ($multisites_version) {
		$sites = get_db_values("SELECT site_id, site_name FROM " . $table_prefix . "sites ORDER BY site_id ", null);
		$r->add_select("site_id", INTEGER, $sites, ADMIN_SITE_MSG);
	}

	$r->add_textbox("name", TEXT, NAME_MSG);
	$r->change_property("name", USE_SQL_NULL, false);
	$r->add_textbox("first_name", TEXT, FIRST_NAME_FIELD);
	$r->change_property("first_name", USE_SQL_NULL, false);
	$r->add_textbox("last_name", TEXT, LAST_NAME_FIELD);
	$r->change_property("last_name", USE_SQL_NULL, false);
	$r->add_select("company_id", INTEGER, $companies, COMPANY_SELECT_FIELD);
	$r->add_textbox("company_name", TEXT, COMPANY_NAME_FIELD);
	$r->add_textbox("email", TEXT, EMAIL_FIELD);
	$r->change_property("email", USE_SQL_NULL, false);
	$r->change_property("email", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("address1", TEXT, STREET_FIRST_FIELD);
	$r->add_textbox("address2", TEXT, STREET_SECOND_FIELD);
	$r->add_textbox("city", TEXT, CITY_FIELD);
	$r->add_textbox("province", TEXT, PROVINCE_FIELD);
	$r->add_select("state_id", INTEGER, $states, STATE_FIELD);
	$r->change_property("state_id", DEFAULT_VALUE, 0);
	$r->change_property("state_id", USE_SQL_NULL, false);
	$r->add_textbox("state_code", TEXT);
	$r->add_textbox("zip", TEXT, ZIP_FIELD);
	$r->add_select("country_id", INTEGER, $countries, COUNTRY_FIELD);
	$r->change_property("country_id", DEFAULT_VALUE, 0);
	$r->change_property("country_id", USE_SQL_NULL, false);
	$r->add_textbox("country_code", TEXT);

	$r->add_textbox("phone", TEXT, PHONE_FIELD);
	$r->add_textbox("daytime_phone", TEXT, DAYTIME_PHONE_FIELD);
	$r->add_textbox("evening_phone", TEXT, EVENING_PHONE_FIELD);
	$r->add_textbox("cell_phone", TEXT, CELL_PHONE_FIELD);
	$r->add_textbox("fax", TEXT, FAX_FIELD);
	
	$r->add_textbox("delivery_name", TEXT, DELIVERY_MSG." ".NAME_MSG);
	$r->add_textbox("delivery_first_name", TEXT, DELIVERY_MSG." ".FIRST_NAME_FIELD);
	$r->add_textbox("delivery_last_name", TEXT, DELIVERY_MSG." ".LAST_NAME_FIELD);
	$r->add_select("delivery_company_id", INTEGER, $companies, DELIVERY_MSG." ".COMPANY_SELECT_FIELD);
	$r->add_textbox("delivery_company_name", TEXT, DELIVERY_MSG." ".COMPANY_NAME_FIELD);
	$r->add_textbox("delivery_email", TEXT, DELIVERY_MSG." ".EMAIL_FIELD);
	$r->change_property("delivery_email", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("delivery_address1", TEXT, DELIVERY_MSG." ".STREET_FIRST_FIELD);
	$r->add_textbox("delivery_address2", TEXT, DELIVERY_MSG." ".STREET_SECOND_FIELD);
	$r->add_textbox("delivery_city", TEXT, DELIVERY_MSG." ".CITY_FIELD);
	$r->add_textbox("delivery_province", TEXT, DELIVERY_MSG." ".PROVINCE_FIELD);
	$r->add_select("delivery_state_id", INTEGER, $states, DELIVERY_MSG." ".STATE_FIELD);
	$r->change_property("delivery_state_id", DEFAULT_VALUE, 0);
	$r->change_property("delivery_state_id", USE_SQL_NULL, false);
	$r->add_textbox("delivery_state_code", TEXT);
	$r->add_textbox("delivery_zip", TEXT, DELIVERY_MSG." ".ZIP_FIELD);
	$r->add_select("delivery_country_id", INTEGER, $countries, DELIVERY_MSG." ".COUNTRY_FIELD);	
	$r->change_property("delivery_country_id", DEFAULT_VALUE, 0);
	$r->change_property("delivery_country_id", USE_SQL_NULL, false);
	$r->add_textbox("delivery_country_code", TEXT);
	$r->add_textbox("delivery_phone", TEXT, DELIVERY_MSG." ".PHONE_FIELD);
	$r->add_textbox("delivery_daytime_phone", TEXT, DELIVERY_MSG." ".DAYTIME_PHONE_FIELD);
	$r->add_textbox("delivery_evening_phone", TEXT, DELIVERY_MSG." ".EVENING_PHONE_FIELD);
	$r->add_textbox("delivery_cell_phone", TEXT, DELIVERY_MSG." ".CELL_PHONE_FIELD);
	$r->add_textbox("delivery_fax", TEXT, DELIVERY_MSG." ".FAX_FIELD);
	
	$r->add_textbox("tax_prices_type", TEXT);	
	
	$personal_number = 0;
	$delivery_number = 0;
	for ($i = 0; $i < sizeof($parameters); $i++)
	{                                    
		$personal_param = "call_center_show_" . $parameters[$i];
		$delivery_param = "call_center_show_delivery_" . $parameters[$i];
		if (isset($order_info[$personal_param]) && $order_info[$personal_param] == 1) {
			$personal_number++;
			if ($order_info["call_center_" . $parameters[$i] . "_required"] == 1) {
				$r->parameters[$parameters[$i]][REQUIRED] = true;
			}
		} else {
			$r->parameters[$parameters[$i]][SHOW] = false;
		}
		if (isset($order_info[$delivery_param]) && $order_info[$delivery_param] == 1) {
			$delivery_number++;
			if ($order_info["call_center_delivery_" . $parameters[$i] . "_required"] == 1) {
				$r->parameters["delivery_" . $parameters[$i]][REQUIRED] = true;
			}
		} else {
			$r->parameters["delivery_" . $parameters[$i]][SHOW] = false;
		}
	}

	$sql = "SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE is_active=1 ORDER BY status_order, status_id";
	$order_statuses = get_db_values($sql, array(array("", SELECT_ORDER_STATUS_MSG)));
	$r->add_select("order_status", INTEGER, $order_statuses, ORDER_STATUS_MSG);
	$r->change_property("order_status", USE_SQL_NULL, false);
	$r->add_textbox("affiliate_code", TEXT);
	$r->change_property("affiliate_code", USE_SQL_NULL, false);
	$r->add_textbox("coupons_ids", TEXT);
	$r->add_textbox("default_currency_code", TEXT);
	$r->add_textbox("currency_code", TEXT);
	$r->add_textbox("currency_rate", FLOAT);
	$r->add_textbox("total_buying", NUMBER);
	$r->add_textbox("goods_total", NUMBER);
	$r->add_textbox("goods_tax", NUMBER);
	$r->add_textbox("goods_incl_tax", NUMBER);
	$r->add_textbox("total_quantity", NUMBER);
	$r->add_textbox("weight_total", NUMBER);
	$r->add_textbox("total_discount", NUMBER);
	$r->add_textbox("properties_total", NUMBER);
	$r->add_textbox("properties_taxable", NUMBER);
	$r->add_textbox("shipping_type_id", INTEGER);
	$r->add_textbox("shipping_type_code", TEXT);
	$r->add_textbox("shipping_type_desc", TEXT);
	$r->add_textbox("shipping_cost", NUMBER);
	$r->add_textbox("shipping_taxable", INTEGER);
	$r->add_textbox("shipping_expecting_date", DATETIME, SHIPPING_EXPECTING_MSG);
	$r->change_property("shipping_expecting_date", VALUE_MASK, $date_edit_format);
	
	$r->add_textbox("tax_name", TEXT);
	$r->add_textbox("tax_percent", NUMBER);
	$r->add_textbox("tax_total", NUMBER);	
	$r->add_textbox("processing_fee", NUMBER);
	$r->add_textbox("order_total", NUMBER);
	$r->add_textbox("order_placed_date", DATETIME);
	$r->change_property("order_placed_date", VALUE_MASK, $date_show_format);
	$r->change_property("order_placed_date", USE_IN_UPDATE, false);
	$r->add_textbox("transaction_id", TEXT);
	$r->add_textbox("error_message", TEXT);
	$r->add_textbox("pending_message", TEXT);
	$r->add_textbox("shipping_tracking_id", TEXT);
	$r->add_textbox("remote_address", TEXT);

	$r->add_textbox("user_id", INTEGER);	
	$r->add_textbox("is_placed", INTEGER);
	$r->change_property("is_placed", USE_IN_UPDATE, false);
	$r->add_textbox("is_call_center", INTEGER);
	$r->change_property("is_call_center", USE_IN_UPDATE, false);

	// Credit card params
	$r->add_textbox("payment_id", INTEGER);
	$r->add_textbox("cc_name", TEXT, CC_NAME_FIELD);
	$r->add_textbox("cc_first_name", TEXT, CC_FIRST_NAME_FIELD);
	$r->add_textbox("cc_last_name", TEXT, CC_LAST_NAME_FIELD);
	$r->add_textbox("cc_number", TEXT, CC_NUMBER_FIELD);
	$r->change_property("cc_number", MIN_LENGTH, 10);
	$r->add_textbox("cc_start_date", DATETIME, CC_START_DATE_FIELD);
	$r->add_textbox("cc_expiry_date", DATETIME, CC_EXPIRY_DATE_FIELD);
	$credit_cards = get_db_values("SELECT credit_card_id, credit_card_name FROM " . $table_prefix . "credit_cards", array(array("", PLEASE_CHOOSE_MSG)));
	$r->add_select("cc_type", INTEGER, $credit_cards, CC_TYPE_FIELD);
	$issue_numbers = get_db_values("SELECT issue_number AS issue_value, issue_number AS issue_description FROM " . $table_prefix . "issue_numbers", array(array("", NOT_AVAILABLE_MSG)));
	$r->add_select("cc_issue_number", INTEGER, $issue_numbers, CC_ISSUE_NUMBER_FIELD);
	$r->add_textbox("cc_security_code", TEXT, CC_SECURITY_CODE_FIELD);
	$r->add_textbox("pay_without_cc", TEXT, PAY_WITHOUT_CC_FIELD);

	$t->set_file("main", "admin_order_call.html");
	$t->set_var("order_id", $order_id);
	$t->set_var("currency_left", htmlspecialchars($currency["left"]));
	$t->set_var("currency_right", htmlspecialchars($currency["right"]));
	$t->set_var("currency_rate", htmlspecialchars($currency["rate"]));
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_ORDER_MSG, CONFIRM_DELETE_MSG));

	$t->set_var("admin_href",               "admin.php");
	$t->set_var("admin_orders_href",        "admin_orders.php");
	$t->set_var("admin_order_href",         $order_details_site_url . "admin_order.php");
	$t->set_var("admin_order_links_href",   "admin_order_links.php");
	$t->set_var("admin_order_serial_href",  "admin_order_serial.php");
	$t->set_var("admin_order_serials_href", "admin_order_serials.php");
	$t->set_var("admin_order_vouchers_href","admin_order_vouchers.php");
	$t->set_var("admin_user_href",          "admin_user.php");
	$t->set_var("admin_coupon_href",        "admin_coupon.php");
	$t->set_var("admin_coupons_href",       "admin_coupons.php");
	$t->set_var("admin_order_notes_href",   "admin_order_notes.php");
	$t->set_var("admin_order_email_href",   "admin_order_email.php");
	$t->set_var("admin_order_sms_href",     "admin_order_sms.php");
	$t->set_var("admin_order_item_href",    "admin_order_item.php");
	$t->set_var("admin_packing_html_href",  "admin_packing_html.php");
	$t->set_var("admin_packing_pdf_href",   "admin_packing_pdf.php");
	$t->set_var("cc_security_code_help_href", "../cc_security_code_help.php");
	$t->set_var("admin_order_product_select_href", "admin_order_product_select.php");
	$t->set_var("admin_order_user_select_href", "admin_order_user_select.php");
	$t->set_var("date_edit_format", join("", $date_edit_format));


	// Get payment_id used for Call Center
	$sql  = " SELECT p.payment_id FROM " . $table_prefix . "payment_systems AS p ";
	if ($multisites_version) {
		$sql .= " LEFT JOIN " . $table_prefix . "payment_systems_sites AS s ON s.payment_id=p.payment_id ";
		$sql .= " WHERE p.is_call_center=1 ";
		$sql .= " AND ( p.sites_all=1 OR s.site_id=". $db->tosql($site_id,INTEGER) .") ";
	} else {
		$sql .= " WHERE p.is_call_center=1";
	}
	$payment_id = get_db_value($sql);

	// Get CC settings
	$cc_info = array();
	if (strlen($payment_id)) {
		$setting_type = "credit_card_info_" . $payment_id;
		$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
		if ($multisites_version) {
			$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id,INTEGER) . ") ";
			$sql .= " ORDER BY site_id ASC ";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$cc_info[$db->f("setting_name")] = $db->f("setting_value");
		}
	} else { // exit if there is no payment system to process orders
		$error_message = SELECT_PAYMENT_SYSTEM_CALLCENTER_MSG;
		if ($sitelist) {
			$site_name = get_db_value("SELECT site_name FROM " . $table_prefix . "sites WHERE site_id=" . $db->tosql($site_id,INTEGER));
			$error_message .=MAKE_IT_AVAILABLE_CURRENT_SITE_MSG . $site_name . ")";			
		}
		$t->set_var("errors_list", $error_message);
		$t->parse("errors");
		include_once("./admin_header.php");
		include_once("./admin_footer.php");
		$t->pparse("main");
		exit;
	}

	$payment_params = 0;
	for ($i = 0; $i < sizeof($cc_parameters); $i++)
	{                                    
		$payment_param = "call_center_show_" . $cc_parameters[$i];
		if (isset($cc_info[$payment_param]) && $cc_info[$payment_param] == 1) {
			$payment_params++;
			if ($cc_info["call_center_" . $cc_parameters[$i] . "_required"] == 1) {
				$r->parameters[$cc_parameters[$i]][REQUIRED] = true;
			}
		} else {
			$r->parameters[$cc_parameters[$i]][SHOW] = false;
		}
	}
	$cc_number_split = get_setting_value($cc_info, "cc_number_split", 1);
	$cc_number_security = get_setting_value($cc_info, "cc_number_security", 0);
	$cc_code_security = get_setting_value($cc_info, "cc_code_security", 0);

	$r->get_form_values();
		
	// get last customer info
	if (get_param("last_customer")) {
		get_last_customer(get_param("last_customer"));		
	}


	$oi = new VA_Record($table_prefix . "orders_items", "items");
	$oi->add_where("order_item_id", INTEGER);
	$oi->add_hidden("order_id", INTEGER);
	$oi->change_property("order_id", USE_IN_INSERT, true);
	$oi->add_textbox("site_id", INTEGER);
	$oi->add_hidden("item_status", INTEGER);
	$oi->add_textbox("user_id", INTEGER);
	$oi->change_property("item_status", USE_IN_INSERT, true);
	$oi->change_property("item_status", USE_IN_UPDATE, true);
	$oi->change_property("item_status", USE_SQL_NULL, false);
	$oi->add_textbox("item_id", INTEGER, ITEM_ID_MSG);
	$oi->change_property("item_id", REQUIRED, true);
	$oi->add_textbox("item_user_id", INTEGER);
	$oi->change_property("item_user_id", USE_SQL_NULL, false);
	$oi->add_textbox("supplier_id", INTEGER);
	$oi->change_property("supplier_id", USE_SQL_NULL, false);
	$oi->add_textbox("cart_item_id", INTEGER);
	$oi->change_property("cart_item_id", USE_SQL_NULL, false);
	$oi->add_textbox("friend_user_id", INTEGER);
	$oi->change_property("friend_user_id", USE_SQL_NULL, false);
	$oi->add_textbox("affiliate_user_id", INTEGER);
	$oi->change_property("affiliate_user_id", USE_SQL_NULL, false);
	$oi->add_textbox("item_name", TEXT, ITEM_NAME_MSG);
	$oi->change_property("item_name", REQUIRED, true);
	$oi->add_textbox("item_code", TEXT, PROD_CODE_MSG);
	$oi->change_property("item_code", USE_SQL_NULL, false);
	$oi->add_textbox("manufacturer_code", TEXT, MANUFACTURER_CODE_MSG);
	$oi->add_textbox("quantity", INTEGER, QUANTITY_MSG);
	$oi->change_property("quantity", REQUIRED, true);
	$oi->add_textbox("buying_price", NUMBER, PRICE_EACH_MSG);
	$oi->add_textbox("price", NUMBER, PRICE_EACH_MSG);
	$oi->add_textbox("weight", NUMBER, ITEM_WEIGHT_MSG);
	$oi->add_checkbox("tax_free", INTEGER, TAX_FEE_MSG);
	
	$oi->add_textbox("use_stock_level", INTEGER, USE_STOCK_MSG);
	$oi->add_textbox("stock_level", TEXT, STOCK_LEVEL_MSG);
	$oi->add_textbox("stock_level_text", TEXT, STOCK_LEVEL_TEXT_MSG);
	$oi->change_property("use_stock_level", USE_IN_INSERT, false);
	$oi->change_property("use_stock_level", USE_IN_UPDATE, false);
	$oi->change_property("stock_level", USE_IN_INSERT, false);
	$oi->change_property("stock_level", USE_IN_UPDATE, false);
	$oi->change_property("stock_level_text", USE_IN_INSERT, false);
	$oi->change_property("stock_level_text", USE_IN_UPDATE, false);	
	$oi->add_textbox("item_type_id", NUMBER, ITEM_COST_MSG);
	$oi->change_property("item_type_id", USE_IN_INSERT, false);
	$oi->change_property("item_type_id", USE_IN_UPDATE, false);	
	$oi->add_textbox("cost", NUMBER, ITEM_COST_MSG);
	$oi->change_property("cost", USE_IN_INSERT, false);
	$oi->change_property("cost", USE_IN_UPDATE, false);
	$oi->add_textbox("cost_text", TEXT, ITEM_COST_TEXT_MSG);
	$oi->change_property("cost_text", USE_IN_INSERT, false);
	$oi->change_property("cost_text", USE_IN_UPDATE, false);
	// recurring fields
	$oi->add_textbox("is_recurring", INTEGER);
	$oi->change_property("is_recurring", USE_SQL_NULL, false);
	$oi->add_textbox("recurring_price", INTEGER);
	$oi->add_textbox("recurring_period", INTEGER);
	$oi->add_textbox("recurring_interval", INTEGER);
	$oi->add_textbox("recurring_payments_total", INTEGER);
	$oi->add_textbox("recurring_payments_made", INTEGER);
	$oi->add_textbox("recurring_payments_failed", INTEGER);
	$oi->add_textbox("recurring_end_date", DATETIME);
	$oi->add_textbox("recurring_last_payment", DATETIME);
	$oi->add_textbox("recurring_next_payment", DATETIME);
	$oi->add_textbox("recurring_plan_payment", DATETIME);
	// subscription field
	$oi->add_textbox("subscription_id", INTEGER);
	$oi->change_property("subscription_id", USE_SQL_NULL, false);

	
	$more_items = get_param("more_items");
	$number_items = get_param("number_items");

	$eg = new VA_EditGrid($oi, "items");
	$eg->get_form_values($number_items);
	
	// use php functions instead of js
		
	$op = new VA_Record($table_prefix . "orders_properties");
	$op->add_textbox("order_id", INTEGER);
	$op->add_textbox("property_id", INTEGER);
	$op->add_textbox("property_order", INTEGER);
	$op->add_textbox("property_type", INTEGER);
	$op->add_textbox("property_name", TEXT);
	$op->add_textbox("property_value_id", INTEGER);
	$op->add_textbox("property_value", TEXT);
	$op->add_textbox("property_price", FLOAT);
	$op->add_textbox("property_weight", FLOAT);
	$op->add_checkbox("tax_free", INTEGER);
	
	
	$cc_start_year   = get_param("cc_start_year");
	$cc_start_month  = get_param("cc_start_month");
	$cc_expiry_year  = get_param("cc_expiry_year");
	$cc_expiry_month = get_param("cc_expiry_month");

	if (strlen($cc_start_year) && strlen($cc_start_month)) {
		$r->set_value("cc_start_date", array($cc_start_year, $cc_start_month, 1, 0, 0, 0));
	}

	if (strlen($cc_expiry_year) && strlen($cc_expiry_month)) {
		$r->set_value("cc_expiry_date", array($cc_expiry_year, $cc_expiry_month, 1, 0, 0, 0));
	}

	// Prepare custom options 
	$options_errors = "";
	$properties_total = 0; $properties_taxable = 0;
	$personal_properties = 0; $delivery_properties = 0; $payment_properties = 0;
	$custom_options = array();
	$vat_parameter = "";
	$sql  = " SELECT * ";
	$sql .= " FROM " . $table_prefix . "order_custom_properties ";
	$sql .= " WHERE (property_type IN (1,2,3) OR (property_type=4 AND payment_id=" . $db->tosql($payment_id, INTEGER) . ")) AND property_show IN (0,2) ";
	$sql .= " ORDER BY property_order, property_id ";

	$db->query($sql);
	if ($db->next_record()) {
		$order_properties = ""; $op_rows = array(); $rn = 0;
		do {
			$op_rows[$rn]["property_id"] = $db->f("property_id");
			$op_rows[$rn]["property_order"] = $db->f("property_order");
			$op_rows[$rn]["property_name"] = $db->f("property_name");
			$op_rows[$rn]["property_description"] = $db->f("property_description");
			$op_rows[$rn]["default_value"] = $db->f("default_value");
			$op_rows[$rn]["property_type"] = $db->f("property_type");
			$op_rows[$rn]["property_style"] = $db->f("property_style");
			$op_rows[$rn]["control_type"] = $db->f("control_type");
			$op_rows[$rn]["control_style"] = $db->f("control_style");
			$op_rows[$rn]["required"] = $db->f("required");
			$op_rows[$rn]["tax_free"] = $db->f("tax_free");
			$op_rows[$rn]["before_name_html"] = $db->f("before_name_html");
			$op_rows[$rn]["after_name_html"] = $db->f("after_name_html");
			$op_rows[$rn]["before_control_html"] = $db->f("before_control_html");
			$op_rows[$rn]["after_control_html"] = $db->f("after_control_html");
			$op_rows[$rn]["onchange_code"] = $db->f("onchange_code");
			$op_rows[$rn]["onclick_code"] = $db->f("onclick_code");
			$op_rows[$rn]["control_code"] = $db->f("control_code");

			$rn++;
		} while ($db->next_record());

		for ($rn = 0; $rn < sizeof($op_rows); $rn++) {
			$property_id = $op_rows[$rn]["property_id"];
			$property_order  = $op_rows[$rn]["property_order"];
			$property_name_initial = $op_rows[$rn]["property_name"];
			$property_name = get_translation($property_name_initial);
			$property_description = $op_rows[$rn]["property_description"];
			$default_value = $op_rows[$rn]["default_value"];
			$property_type = $op_rows[$rn]["property_type"];
			$property_style = $op_rows[$rn]["property_style"];
			$control_type = $op_rows[$rn]["control_type"];
			$control_style = $op_rows[$rn]["control_style"];
			$property_required = $op_rows[$rn]["required"];
			$property_tax_free = $op_rows[$rn]["tax_free"];
			$before_name_html = $op_rows[$rn]["before_name_html"];
			$after_name_html = $op_rows[$rn]["after_name_html"];
			$before_control_html = $op_rows[$rn]["before_control_html"];
			$after_control_html = $op_rows[$rn]["after_control_html"];
			$onchange_code = $op_rows[$rn]["onchange_code"];
			$onclick_code = $op_rows[$rn]["onclick_code"];
			$control_code = $op_rows[$rn]["control_code"];

			if (preg_match("/vat/i", $property_name_initial)) {
				$vat_parameter = "op_" . $property_id;
			}

			if ($property_type > 0) {
				if (strlen($order_properties)) { $order_properties .= ","; }
				$order_properties .= $property_id;
			}

			$selected_price = 0;
			$property_control  = "";
			$property_control .= "<input type=\"hidden\" name=\"op_name_" . $property_id . "\"";
			$property_control .= " value=\"" . strip_tags($property_name) . "\">";
			$property_control .= "<input type=\"hidden\" name=\"op_required_" . $property_id . "\"";
			$property_control .= " value=\"" . intval($property_required) . "\">";
			$property_control .= "<input type=\"hidden\" name=\"op_control_" . $property_id . "\"";
			$property_control .= " value=\"" . strtoupper($control_type) . "\">";
			$property_control .= "<input type=\"hidden\" name=\"op_tax_free_" . $property_id . "\"";
			$property_control .= " value=\"" . intval($property_tax_free) . "\">";
			

			$sql  = " SELECT * FROM " . $table_prefix . "order_custom_values ";
			$sql .= " WHERE property_id=" . $property_id . " AND hide_value=0";
			$sql .= " ORDER BY property_value_id ";
			if (strtoupper($control_type) == "LISTBOX") {
				$selected_value = get_param("op_" . $property_id);
				$properties_prices = "";
				$properties_values = "<option value=\"\">" . SELECT_MSG . " " . $property_name . "</option>" . $eol;
				$db->query($sql);
				while ($db->next_record())
				{
					$property_value_original = $db->f("property_value");
					$property_value = get_translation($property_value_original);
					$property_price = $db->f("property_price");
					$property_value_id = $db->f("property_value_id");
					$is_default_value = $db->f("is_default_value");
					$property_selected  = "";
					$properties_prices .= "<input type=\"hidden\" name=\"op_option_price_" . $property_value_id . "\"";
					$properties_prices .= " value=\"" . $property_price . "\">";
					if (strlen($operation)) {
						if ($selected_value == $property_value_id) {
							$property_selected  = "selected ";
							$selected_price    += $property_price;
							$custom_options[$property_id][] = array(
								"property_id" => $property_id, "type" => $property_type, 
								"order" => $property_order, "name"=> $property_name_initial, 
								"value_id" => $property_value_id, "value" => $property_value_original, 
								"price" => $property_price, "tax_free" => $property_tax_free
							);
						}
					} elseif ($is_default_value) {
						$property_selected  = "selected ";
						$selected_price    += $property_price;
					} 

					$properties_values .= "<option " . $property_selected . "value=\"" . htmlspecialchars($property_value_id) . "\">";
					$properties_values .= htmlspecialchars($property_value);
					if ($property_price > 0)
						$properties_values .= " (+ " . currency_format($property_price) . ")";
					elseif ($property_price < 0)
						$properties_values .= " (- " . currency_format(abs($property_price)) . ")";
					$properties_values .= "</option>" . $eol;
				}
				$property_control .= $before_control_html;
				$property_control .= "<select name=\"op_" . $property_id . "\" onChange=\"changeProperty(document.record);";
				if ($onchange_code) {	$property_control .= $onchange_code; }
				$property_control .= "\"";
				if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
				if ($control_code) {	$property_control .= " " . $control_code . " "; }
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				$property_control .= ">" . $properties_values . "</select>";
				$property_control .= $properties_prices;
				$property_control .= $after_control_html;
			} elseif (strtoupper($control_type) == "RADIOBUTTON" || strtoupper($control_type) == "CHECKBOXLIST") {
				$is_radio = (strtoupper($control_type) == "RADIOBUTTON");

				$selected_value = array();
				if (strlen($operation)) {
					if ($is_radio) {
						$selected_value[] = get_param("op_" . $property_id);
					} else {
						$total_options = get_param("op_total_" . $property_id);
						for ($i = 1; $i <= $total_options; $i++) {
							$selected_value[] = get_param("op_" . $property_id . "_" . $i);
						}
					}
				}

				$input_type = $is_radio ? "radio" : "checkbox";
				$property_control .= "<span";
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				$property_control .= ">";
				$value_number = 0;
				$db->query($sql);
				while ($db->next_record())
				{
					$value_number++;
					$property_price = $db->f("property_price");
					$property_value_id = $db->f("property_value_id");
					$item_code = $db->f("item_code");
					$manufacturer_code = $db->f("manufacturer_code");
					$is_default_value = $db->f("is_default_value");
					$property_value_original = $db->f("property_value");
					$property_value = get_translation($property_value_original);
					$property_checked = "";
					$property_control .= $before_control_html;
					$property_control .= "<input type=\"hidden\" name=\"op_option_price_" . $property_value_id . "\"";
					$property_control .= " value=\"" . $property_price . "\">";
					if (strlen($operation)) {
						if (in_array($property_value_id, $selected_value)) {
							$property_checked = "checked ";
							$selected_price  += $property_price;
							$custom_options[$property_id][] = array(
								"property_id" => $property_id, "type" => $property_type, 
								"order" => $property_order, "name"=> $property_name_initial, 
								"value_id" => $property_value_id, "value" => $property_value_original, 
								"price" => $property_price, "tax_free" => $property_tax_free
							);
						}
					} elseif ($is_default_value) {
						$property_checked = "checked ";
						$selected_price  += $property_price;
					} 

					$control_name = ($is_radio) ? ("op_".$property_id) : ("op_".$property_id."_".$value_number);
					$property_control .= "<input type=\"" . $input_type . "\" name=\"" . $control_name . "\" ". $property_checked;
					$property_control .= "value=\"" . htmlspecialchars($property_value_id) . "\" onClick=\"changeProperty(document.record); ";
					if ($onclick_code) {	
						$control_onclick_code = str_replace("{option_value}", $property_value, $onclick_code);
						$property_control .= $control_onclick_code; 
					}
					$property_control .= "\"";
					if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
					if ($control_code) {	$property_control .= " " . $control_code . " "; }
					$property_control .= ">";
					$property_control .= $property_value;
					if ($property_price > 0)
						$property_control .= " (+ " . currency_format($property_price) . ")";
					elseif ($property_price < 0)
						$property_control .= " (- " . currency_format(abs($property_price)) . ")";
					$property_control .= $after_control_html;
				}
				$property_control .= "</span>";
				$property_control .= "<input type=\"hidden\" name=\"op_total_".$property_id."\" value=\"".$value_number."\">";
			} elseif (strtoupper($control_type) == "TEXTBOX") {
				if (strlen($operation)) {
					$control_value = get_param("op_" . $property_id);
					if (strlen($control_value)) {
						$custom_options[$property_id][] = array(
							"property_id" => $property_id, "type" => $property_type, 
							"order" => $property_order, "name"=> $property_name_initial, 
							"value_id" => "", "value" => $control_value, 
							"price" => 0, "tax_free" => 0
						);
					}
				} else {
					$control_value = $default_value;
				}
				$property_control .= $before_control_html;
				$property_control .= "<input type=\"text\" name=\"op_" . $property_id . "\"";
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
				if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
				if ($control_code) {	$property_control .= " " . $control_code . " "; }
				$property_control .= " value=\"". htmlspecialchars($control_value) . "\">";
				$property_control .= $after_control_html;
			} elseif (strtoupper($control_type) == "TEXTAREA") {
				if (strlen($operation)) {
					$control_value = get_param("op_" . $property_id);
					if (strlen($control_value)) {
						$custom_options[$property_id][] = array(
							"property_id" => $property_id, "type" => $property_type, 
							"order" => $property_order, "name"=> $property_name_initial, 
							"value_id" => "", "value" => $control_value, 
							"price" => 0, "tax_free" => 0
						);
					}
				} else {
					$control_value = $default_value;
				}
				$property_control .= $before_control_html;
				$property_control .= "<textarea name=\"op_" . $property_id . "\"";
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
				if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
				if ($control_code) {	$property_control .= " " . $control_code . " "; }
				$property_control .= ">". htmlspecialchars($control_value) ."</textarea>";
				$property_control .= $after_control_html;
			} else {
				$property_control .= $before_control_html;
				if ($property_required) {
					$property_control .= "<input type=\"hidden\" name=\"op_" . $property_id . "\" value=\"" . htmlspecialchars($property_description) . "\">";
				}
				if (strlen($default_value)) {
					$custom_options[$property_id][] = array(
						"property_id" => $property_id, "type" => $property_type, 
						"order" => $property_order, "name"=> $property_name_initial, 
						"value_id" => "", "value" => $default_value, 
						"price" => 0, "tax_free" => 0
					);
				}
				$property_control .= "<span";
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
				if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
				if ($control_code) {	$property_control .= " " . $control_code . " "; }
				$property_control .= ">" . get_translation($default_value) . "</span>";
				$property_control .= $after_control_html;
			}

			$properties_total += $selected_price;
			if ($property_tax_free != 1) {
				$properties_taxable += $selected_price;
			}
			$t->set_var("property_id", $property_id);
			//$t->set_var("property_block_id", $property_block_id);
			$t->set_var("property_name", $before_name_html . $property_name . $after_name_html);
			if ($selected_price == 0) {
				$t->set_var("op_price", "");
			} else {
				$t->set_var("op_price", currency_format($selected_price));
			}
			$t->set_var("property_style", $property_style);
			$t->set_var("property_control", $property_control);
			if ($property_required) {
				$t->set_var("property_required", "*");
			} else {
				$t->set_var("property_required", "");
			}

			if (strlen($operation) && $property_required && !isset($custom_options[$property_id])) {
				$property_message = str_replace("{field_name}", $property_name, REQUIRED_MESSAGE) . "<br>";
				if ($property_type == 1 || $property_type == 4) {
					$sc_errors .= $property_message;
				} elseif ($property_type == 2 || $property_type == 3) {
					$options_errors .= $property_message;
				}
			}

			if ($property_type == 1) {
				$t->sparse("cart_properties", true);
			} elseif ($property_type == 2) {
				$personal_properties++;
				$t->sparse("personal_properties", true);
			} elseif ($property_type == 3) {
				$delivery_properties++;
				$t->sparse("delivery_properties", true);
			} elseif ($property_type == 4) {
				$payment_properties++;
				$t->sparse("payment_properties", true);
			}
		}

		$t->set_var("order_properties", $order_properties);
		$t->set_var("properties_total", $properties_total);
		$t->set_var("properties_taxable", $properties_taxable);

	}
	// end custom options
	
	// Prepare state_id, postal_code and country_id for use
	$state_id = ""; $postal_code = ""; $country_id = "";
	if (strlen($operation)) {
		if ((isset($order_info["show_delivery_state_id"]) && $order_info["show_delivery_state_id"] == 1) 
			|| (isset($order_info["show_delivery_state_code"]) && $order_info["show_delivery_state_code"] == 1)) {
			if ($same_as_personal == 1 && ( (isset($order_info["show_state_id"]) && $order_info["show_state_id"] == 1) 
				|| (isset($order_info["show_state_code"]) && $order_info["show_state_code"] == 1) )) {
				$state_id = get_param("state_id");
			} else {
				$state_id = get_param("delivery_state_id");
			}
		} elseif ((isset($order_info["show_state_id"]) && $order_info["show_state_id"] == 1)
			|| (isset($order_info["show_state_code"]) && $order_info["show_state_code"] == 1)) {
			$state_id = get_param("state_id");
		}
		
		if ($order_info["show_delivery_zip"] == 1) {
			if ($same_as_personal == 1 && $order_info["show_zip"] == 1) {
				$postal_code = get_param("zip");
			} else {
				$postal_code = get_param("delivery_zip");
			}
		} elseif ($order_info["show_zip"] == 1) {
			$postal_code = get_param("zip");
		} 

		if ((isset($order_info["show_delivery_country_id"]) && $order_info["show_delivery_country_id"] == 1)
			|| (isset($order_info["show_delivery_country_code"]) && $order_info["show_delivery_country_code"] == 1)) {
			if ($same_as_personal == 1 && ((isset($order_info["show_country_id"]) && $order_info["show_country_id"] == 1)
				|| (isset($order_info["show_country_code"]) && $order_info["show_country_code"] == 1)) ) {
				$country_id = get_param("country_id");
			} else {
				$country_id = get_param("delivery_country_id");
			}
		} elseif ((isset($order_info["show_country_id"]) && $order_info["show_country_id"] == 1)
			|| (isset($order_info["show_country_code"]) && $order_info["show_country_code"] == 1)) {
			$country_id = get_param("country_id");
		} else {
			if (isset($settings["country_id"]) && $settings["country_id"]) {
				$country_id = $settings["country_id"];
			} else {
				if ($countries[1]) {
					$country_id = $countries[1][0];
				} else {
					$country_id = 0;
				}
			} 
		}
	} else {
		if (isset($settings["country_id"]) && $settings["country_id"]) {
			$country_id = $settings["country_id"];
		} else {
			if ($countries[1]) {
				$country_id = $countries[1][0];
			} else {
				$country_id = 0;
			}
		} 
	}
	
	// check user tax free option
	$user_tax_free = false;
	if ($r->get_value("user_id")) {
		$sql  = " SELECT u.tax_free AS user_tax_free, ut.tax_free AS group_tax_free ";
		$sql .= " FROM (" . $table_prefix . "users u ";
		$sql .= " LEFT JOIN " . $table_prefix . "user_types ut ON u.user_type_id=ut.type_id) ";
		$sql .= " WHERE user_id=" . $db->tosql($r->get_value("user_id"), INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$user_tax_free = ($db->f("user_tax_free") || $db->f("group_tax_free"));
		}
	}			
	
	// Prepare taxes			
	include_once($root_folder_path . "includes/shopping_cart.php");
	$tax_available = false; $tax_percent_sum = 0; $tax_names = "";	$taxes_total = 0;
		
	$default_tax_rates = get_tax_rates(true); 
	$tax_rates = get_tax_rates(true, $country_id, $state_id, $postal_code);
	if (sizeof($tax_rates) > 0) {
		$tax_available = true;
		foreach ($tax_rates as $tax_id => $tax_info) {
			$tax_percent_sum += $tax_info["tax_percent"];
			if ($tax_names) { $tax_names .= " & "; }
			$tax_names .= get_translation($tax_info["tax_name"]);
		}
	}
		
	$goods_total    = 0;
	$goods_total_full = 0;
	$total_buying_tax = 0;
	$goods_total_excl_tax = 0;
	$goods_total_incl_tax = 0;
	$goods_tax_total = 0;
	$total_buying   = 0;
	$total_quantity = 0;
	$weight_total   = 0;
	$goods_taxable  = 0;
	$goods_tax      = 0; 
	
	for ($i=1; $i<=sizeof($eg->values); $i++) {
		$item_type_id     = $eg->values[$i]['item_type_id'];
		$price            = $eg->values[$i]['price'];
		$buying_price     = $eg->values[$i]['buying_price'];
		$item_tax_free    = $eg->values[$i]['tax_free'];
		$item_tax_percent = 0;
		$quantity         = $eg->values[$i]['quantity'];									
		
		if ($quantity) {
			
			$item_total = $price * $quantity;
			$item_buying_total = $buying_price * $quantity;
			
			$item_tax = get_tax_amount($tax_rates, $item_type_id, $price, $item_tax_free, $item_tax_percent);
			$item_tax_total_values = get_tax_amount($tax_rates, $item_type_id, $item_total, $item_tax_free, $item_tax_percent, "", 2);
			$item_buying_tax = get_tax_amount($tax_rates, $item_type_id, $item_buying_total, $item_tax_free, $item_tax_percent);
			$item_tax_total = add_tax_values($tax_rates, $item_tax_total_values, "products");
			
			if ($tax_prices_type == 1) {
				$price_excl_tax = $price - $item_tax;
				$price_incl_tax = $price;
				$price_excl_tax_total = $item_total - $item_tax_total;
				$price_incl_tax_total = $item_total;
			} else {
				$price_excl_tax = $price;
				$price_incl_tax = $price + $item_tax;
				$price_excl_tax_total = $item_total;
				$price_incl_tax_total = $item_total + $item_tax_total;
			}
			
			$total_quantity       += $quantity;
			$goods_total_full     += $item_total;
			$total_buying         += $item_buying_total;
			$total_buying_tax     += $item_buying_tax;
			
			$goods_total_excl_tax += $price_excl_tax_total;
			$goods_total_incl_tax += $price_incl_tax_total;
			$goods_tax_total      += $item_tax_total;
			$goods_total          += $item_total;
		
			$eg->values[$i]['cost']      = round($item_total, 2);	
			$eg->values[$i]['cost_text'] = ($item_total)?(currency_format(round($item_total, 2))):'';		
			
			$weight_total   +=$eg->values[$i]['weight'] * $quantity ;		
		} else {
			$eg->values[$i]['cost']      = 0;
			$eg->values[$i]['cost_text'] = "";	
		}
		
		$eg->values[$i]['stock_level_text'] = '';
		if ($eg->values[$i]['use_stock_level']) {
			$eg->values[$i]['stock_level_text'] = ' max. ' . $eg->values[$i]['stock_level'];		
		}
	}
	
	// Prepare shipping

	$shipping_types = array();
	$total_shipping_types = 0;
	$shipping_type_code   = "";
	$shipping_type_desc   = "";
	$shipping_cost        = 0;
	$shipping_taxable     = 0;
	$shipping_tare_weight = 0;
	if ($country_id) {
		$shipping_modules = array();
		$sql = "SELECT * FROM " . $table_prefix . "shipping_modules WHERE is_active=1";
		$db->query($sql);
		while ($db->next_record()) {
			$shipping_module_id   = $db->f("shipping_module_id");
			$shipping_module_name = $db->f("shipping_module_name");
			$is_external          = $db->f("is_external");
			$php_external_lib     = $db->f("php_external_lib");
			// fix path to external library of Shipping Module
			if (strpos($php_external_lib, "./") === 0) {
				$php_external_lib = $root_folder_path . substr($php_external_lib, 2);
			} elseif (strpos($php_external_lib, "../") !== 0 || strpos($php_external_lib, "/") !== 0) {
				$php_external_lib = $root_folder_path . $php_external_lib;
			}
			$external_url         = $db->f("external_url");
			$cost_add_percent     = $db->f("cost_add_percent");
			$shipping_modules[] = array($shipping_module_id, $shipping_module_name, $is_external, $php_external_lib, $external_url, $cost_add_percent);
		}	
		
		for ($sm = 0; $sm < sizeof($shipping_modules); $sm++) {
			list($shipping_module_id, $shipping_module_name, $is_external, $php_external_lib, $external_url, $cost_add_percent) = $shipping_modules[$sm];
			$module_shipping = array();
			$where = array();
			$sql  = " SELECT st.shipping_type_id, st.shipping_type_code, st.shipping_type_desc, st.shipping_time, ";
			$sql .= " st.cost_per_order, st.cost_per_product, st.cost_per_weight, st.tare_weight, st.is_taxable ";
			$sql .= " FROM (" ;
			if ($state_id) {
				$sql .= "(" ;
			}			
			if ($multisites_version) {
				$sql .= "(" ;
			}
			$sql .= $table_prefix . "shipping_types AS st ";						
			$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_countries AS country ON st.shipping_type_id=country.shipping_type_id ) ";
			$where[] = " ( st.countries_all=1 OR country.country_id=" . $db->tosql($country_id, INTEGER) .") ";			
			if ($state_id) {
				$sql  .= " LEFT JOIN " . $table_prefix . "shipping_types_states AS state ON st.shipping_type_id=state.shipping_type_id ) ";
				$where[] = " ( st.states_all=1 OR state.state_id=" . $db->tosql($state_id, INTEGER) .") ";			
			}
			if ($multisites_version) {
				$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_sites AS site ON st.shipping_type_id=site.shipping_type_id ) ";
				$where[] = " ( st.sites_all=1 OR site.site_id=" . $db->tosql($site_id, INTEGER) .") ";					
			}
			$where[] = " st.is_active=1 ";			
			$where[] = " (st.min_weight IS NULL OR st.min_weight<=" . $db->tosql($weight_total, NUMBER) . ")";
			$where[] = " (st.max_weight IS NULL OR st.max_weight>=" . $db->tosql($weight_total, NUMBER) . ")";
			$where[] = " (st.min_goods_cost IS NULL OR st.min_goods_cost<=" . $db->tosql($goods_total, NUMBER) . ")";
			$where[] = " (st.max_goods_cost IS NULL OR st.max_goods_cost>=" . $db->tosql($goods_total, NUMBER) . ")";
			$where[] = " st.shipping_module_id=" . $db->tosql($shipping_module_id, INTEGER);
			$sql .= " WHERE " . implode(' AND ', $where);
			$sql .= " GROUP BY st.shipping_type_id, st.shipping_type_code, st.shipping_type_desc, st.shipping_time, ";
			$sql .= " st.cost_per_order, st.cost_per_product, st.cost_per_weight, st.tare_weight, st.is_taxable, st.shipping_order  ";
			$sql .= " ORDER BY st.shipping_order ";
			
			$db->query($sql);
			while ($db->next_record()) {
				$row_shipping_type_id   = $db->f("shipping_type_id");
				$row_shipping_type_code = $db->f("shipping_type_code");
				$row_shipping_type_desc = $db->f("shipping_type_desc");
				$row_shipping_time      = $db->f("shipping_time");
				
				$cost_per_order       = $db->f("cost_per_order");
				$cost_per_product     = $db->f("cost_per_product");
				$cost_per_weight      = $db->f("cost_per_weight");
				$row_tare_weight      = $db->f("tare_weight");
				$row_shipping_taxable = $db->f("is_taxable");
				$row_shipping_cost    = $cost_per_order + ($cost_per_product * $total_quantity) + ($cost_per_weight * ($weight_total + $row_tare_weight));
				$shipping_type = array(
					'row_shipping_type_id'   => $row_shipping_type_id, 
					'row_shipping_type_code' => $row_shipping_type_code, 
					'row_shipping_type_desc' => $row_shipping_type_desc, 
					'row_shipping_cost'      => $row_shipping_cost,
					'row_tare_weight'        => $row_tare_weight, 
					'row_shipping_taxable'   => $row_shipping_taxable, 
					'row_shipping_time'      => $row_shipping_time
				);
				$module_shipping[] = $shipping_type;
				if (!$is_external) {
					$shipping_types[] = $shipping_type;
					if ($shipping_type_id==$row_shipping_type_id) {
						$shipping_type_code = $row_shipping_type_code;
						$shipping_type_desc = $row_shipping_type_desc;
						$shipping_cost      = $row_shipping_cost;
						$shipping_taxable   = $row_shipping_taxable;
						$shipping_tare_weight= $row_tare_weight;
					}
				}
			}			
			if ($is_external && strlen($php_external_lib)) {
				$module_params = array();
				$sql  = " SELECT * FROM " . $table_prefix . "shipping_modules_parameters ";
				$sql .= " WHERE shipping_module_id=" . $db->tosql($shipping_module_id, INTEGER);
				$sql .= " AND not_passed<>1 ";
				$db->query($sql);
				while ($db->next_record()) {
					$param_name = $db->f("parameter_name");
					$param_source = $db->f("parameter_source");
					$module_params[$param_name] = $param_source;
				}
				if ($operation == "refresh") {
					include_once($php_external_lib);
				}
			}
			
			if ($cost_add_percent && $shipping_types) {
				for($i=0, $ic = count($shipping_types); $i<$ic; $i++) {
					$shipping_types[$i][3] = $shipping_types[$i][3] * (1 + $cost_add_percent/100);
				}
			}
		}
	}
	$total_shipping_types = sizeof($shipping_types);
	if ($total_shipping_types==1){
		$shipping_type_code = $shipping_types[0]['row_shipping_type_code'];
		$shipping_type_desc = $shipping_types[0]['row_shipping_type_desc'];
		$shipping_cost      = $shipping_types[0]['row_shipping_cost'];
		$shipping_taxable   = $shipping_types[0]['row_shipping_taxable'];
		$shipping_tare_weight = $shipping_types[0]['row_tare_weight'];
	}
	
	$weight_total += $shipping_tare_weight;

	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "orders_notes oc ";
	$sql .= " WHERE oc.order_id=" . $db->tosql($order_id, INTEGER);
	$total_notes = get_db_value($sql);
	$t->set_var("total_notes", $total_notes);
	if ($total_notes > 0) {
		$t->set_var("notes_style", "font-weight: bold; color: blue;");
	} else {
		$t->set_var("notes_style", "");
	}

	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "orders_items oi ";
	$sql .= " WHERE oi.order_id=" . $db->tosql($order_id, INTEGER);
	$sql .= " AND oi.downloadable=1 ";
	$downloadable_products = get_db_value($sql);
	if ($downloadable_products) {
		$t->parse("downloadable_links", false);
	}

	$sql  = " SELECT COUNT(*) FROM (" . $table_prefix . "orders_items oi ";
	$sql .= " LEFT JOIN " . $table_prefix . "item_types it ON oi.item_type_id=it.item_type_id) ";
	$sql .= " WHERE oi.order_id=" . $db->tosql($order_id, INTEGER);
	$sql .= " AND it.is_gift_voucher=1 ";
	$vouchers_number = get_db_value($sql);
	if ($vouchers_number) {
		$t->parse("vouchers_link", false);
	}

	if ($r->is_empty("transaction_id")) {
		$r->parameters["transaction_id"][SHOW] = false;
	}

	if ($r->is_empty("error_message")) {
		$r->parameters["error_message"][SHOW] = false;
	}
	if ($r->is_empty("pending_message")) {
		$r->parameters["pending_message"][SHOW] = false;
	}
	
	
	// get payment info if available
	$payment_name    = '';
	$processing_time = 0;
	$processing_fee  = 0;
	$fee_type        = 0;
	$fee_min_goods   = 0;
	$fee_max_goods   = 0;
	$payment_info    = '';
	
	$sql  = " SELECT payment_name, processing_time, processing_fee, fee_type, fee_min_goods, fee_max_goods, payment_info "; 
	$sql .= " FROM " . $table_prefix . "payment_systems ";
	$select_id = $r->get_value("payment_id");
	if (!$select_id) {
		$select_id = $payment_id;
	}
	$sql .= " WHERE payment_id=" . $db->tosql($select_id, INTEGER, true, false);
	$db->query($sql);
	if ($db->next_record()) {
		$payment_name = $db->f("payment_name");
		$processing_time = $db->f("processing_time");
		$processing_fee = $db->f("processing_fee");
		$fee_type = $db->f("fee_type");
		$fee_min_goods = $db->f("fee_min_goods");
		$fee_max_goods = $db->f("fee_max_goods");
		$payment_info = $db->f("payment_info");
		$payment_info = get_translation($payment_info);
		$payment_info = get_currency_message($payment_info,$currency);
	}

	if (trim($payment_info)) {
		$payment_params++;
		$t->set_block("payment_info", $payment_info);
		$t->parse("payment_info", false);
		$t->global_parse("payment_info_block", false, false, true);
	} else {
		$t->set_var("payment_info_block", "");
	}
	/*
	if (strlen($operation) || strlen($order_id)) {
		// Payment systems
		$sql = " SELECT COUNT(*) FROM " . $table_prefix . "payment_systems WHERE is_active=1 ";
		$total_payments = intval(get_db_value($sql));
		if ($total_payments == 1) {
			$sql = "SELECT payment_id FROM " . $table_prefix . "payment_systems WHERE is_active=1 ";
			$payment_id = get_db_value($sql);
			$r->change_property("payment_id", SHOW, false);
		} elseif ($total_payments > 1) {
			$payment_id = get_param("payment_id");
		} else {
			$payment_id = "";
			$r->change_property("payment_id", SHOW, false);
		}
		if ($total_payments <= 1) {
			$r->set_value("payment_id", $payment_id);
		}
	}*/
	
	
	
	// use php functions instead of js
	
	$r->set_value("goods_total",    $goods_total);
	$r->set_value("goods_incl_tax", $goods_total_incl_tax);
	$r->set_value("goods_tax",      $goods_tax);
	
	$shipping_tax = get_tax_amount($tax_rates, "shipping", $shipping_cost, !($shipping_taxable), $shipping_tax_percent, $default_tax_rates);
	$r->set_value("total_buying",   $total_buying);
	$r->set_value("total_quantity", $total_quantity);	
	$r->set_value("shipping_type_code", $shipping_type_code);
	$r->set_value("shipping_type_desc", $shipping_type_desc);
	$r->set_value("shipping_cost",      $shipping_cost);
	$r->set_value("shipping_taxable",   $shipping_taxable);
	
	
	$weight_measure = get_setting_value($settings, "weight_measure", "");
	$weight_total_desc = $weight_total ." ". $weight_measure;	 
	$r->set_value("weight_total", $weight_total);
	$t->set_var("weight_total_desc", $weight_total_desc);
			
	$tax_total = $goods_tax_total + $shipping_tax;
	$r->set_value("tax_name", $tax_names);
	$r->set_value("tax_percent", $tax_percent_sum);
	$r->set_value("tax_total", $tax_total);
	$t->set_var("tax_total_desc", currency_format(round($tax_total,2)));

	$order_total =  $goods_total + $shipping_cost + $properties_total;
	if ($tax_prices_type != 1) {
		$order_total += round($tax_total, 2);
	}
		
	if ((strlen($fee_max_goods) && $goods_total > $fee_max_goods) || $goods_total < $fee_min_goods) {
		$processing_fee = 0;
	} elseif ($fee_type == 1) {
		$processing_fee = round($order_total * $processing_fee / 100, 2);
	}	
		
	$processing_fee_desc = ($processing_fee)? (currency_format($processing_fee)) : "none";
	$r->set_value("processing_fee",  $processing_fee);
	$t->set_var("processing_fee_desc", $processing_fee_desc);
	$t->set_var("payment_name", $payment_name);	

	if ($processing_fee) {
		$order_total += $processing_fee;
	}
	$r->set_value("order_total",  $order_total);
	$t->set_var("order_total_desc", currency_format($order_total));
	


	// Process form submit
	$is_valid = true;
	if (strlen($operation) && !$more_items)
	{
		if ($operation == "cancel") {
			header("Location: " . $r->return_page);
			exit;
		} elseif ($operation == "save" || $operation == "process") {
			if ($cc_number_security > 0) {
				$r->set_value("cc_number", format_cc_number(va_decrypt($r->get_value("cc_number"))));
			}
			if ($cc_code_security > 0) {
				$r->set_value("cc_security_code", va_decrypt($r->get_value("cc_security_code")));
			}
			
			// cross-orders patch
			$sql = "SELECT * FROM " . $table_prefix . "countries WHERE country_id=".$db->tosql($country_id, INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$country_code	= $db->f('country_code');
			}
			$sql = "SELECT * FROM " . $table_prefix . "states WHERE state_id=".$db->tosql($state_id, INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$state_code	= $db->f('state_code');
			}
				
			$r->set_value("site_id", $site_id);			
			$r->set_value("shipping_type_id", $shipping_type_id);
			
			$r->errors .= $sc_errors;
			if ($total_shipping_types > 1 && !strlen($shipping_type_id)) {
				$r->errors .= REQUIRED_DELIVERY_MSG . "<br>";
			}
			$cc_number = $r->get_value("cc_number");
			if (strlen($cc_number) >= 10) {
				if (!check_cc_number($cc_number)) {
					$r->errors .= CC_NUMBER_ERROR_MSG . "<br>" . $eol;
				}
			}
			$r->validate();
			$r->errors .= $options_errors;
			if (strlen($r->errors)) {
				$is_valid = false;
			} else {
				$is_valid = true;
			}
			$is_valid = ($eg->validate() && $is_valid);
			if ($is_valid) {
				// Set user's information
				$user_info = array();
				for ($i = 0; $i < sizeof($call_center_user_parameters); $i++) { 
					$user_param = $call_center_user_parameters[$i];
					$user_info[$user_param] = $r->get_value($user_param);
				}
				
				// Set CC number and security code
				$cc_number = clean_cc_number($cc_number);
				$cc_number_len = strlen($cc_number);
				$cc_security_code = $r->get_value("cc_security_code");
				$r->set_value("cc_number", $cc_number);
				set_session("session_cc_number", $r->get_value("cc_number"));
				set_session("session_cc_code",   $r->get_value("cc_security_code"));
				if ($cc_number_len > 6) {
					$cc_number_first = substr($cc_number, 0, 6);
				} else {
					$cc_number_first = $cc_number;
				}
				if ($cc_number_len > 4) {
					$cc_number_last = substr($cc_number, $cc_number_len - 4);
					if ($cc_info["cc_number_split"]) {
						$r->set_value("cc_number", substr($cc_number, 0, $cc_number_len - 4) . "****");
					}
				} else {
					$cc_number_last = $cc_number;
				}
				set_session("session_cc_number_first", $cc_number_first);
				set_session("session_cc_number_last", $cc_number_last);

				if ($cc_number_security == 0) {
					$r->set_value("cc_number", "");
				} elseif ($cc_number_security > 0) {
					$r->set_value("cc_number", va_encrypt($r->get_value("cc_number")));
				}
	
				if ($cc_code_security == 0) {
					$r->set_value("cc_security_code", "");
				} elseif ($cc_code_security > 0) {
					$r->set_value("cc_security_code", va_encrypt($r->get_value("cc_security_code")));
				}

				if (!isset($permissions["update_orders"]) || $permissions["update_orders"] != 1) {
					$r->errors .= NOT_ALLOWED_UPDATE_ORDERS_INFO_MSG;
				} else {
					if ($db_type == "postgre") {
						$order_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "orders') ");
						$r->change_property("order_id", USE_IN_INSERT, true);
						$r->set_value("order_id", $order_id);
					}
						
					$user_id = insert_user_info($user_info);
					$r->set_value("admin_id_added_by", get_session("session_admin_id"));
					$r->set_value("user_id", $user_id);
					$r->set_value("order_placed_date", va_time());
					$r->set_value("is_placed", "1");
					$r->set_value("tax_prices_type", $tax_prices_type);
					$r->set_value("order_status", 0);
					before_order_save();
					$r->insert_record();

					if ($db_type == "mysql") {
						$order_id = get_db_value(" SELECT LAST_INSERT_ID() ");
						$r->set_value("order_id", $order_id);
					} elseif ($db_type == "access") {
						$order_id = get_db_value(" SELECT @@IDENTITY ");
						$r->set_value("order_id", $order_id);
					} elseif ($db_type == "db2") {
						$order_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "orders FROM " . $table_prefix . "orders");
						$r->set_value("order_id", $order_id);
					}

					// save taxes for order
					save_order_taxes($order_id, $tax_rates);
					
					// Insert properties
					foreach ($custom_options as $property_id => $property_values) {
						for ($pv = 0; $pv < sizeof($property_values); $pv++) {
							$value_info = $property_values[$pv];
							$t->set_var("field_name_" . $property_id, $value_info["name"]);
							$t->set_var("field_value_" . $property_id, $value_info["value"]);
							$t->set_var("field_price_" . $property_id, $value_info["price"]);
							$t->set_var("field_" . $property_id, $value_info["value"]);
							$op->set_value("property_id", $property_id);
							$op->set_value("order_id", $order_id);
							$op->set_value("property_order", $value_info["order"]);
							$op->set_value("property_type", $value_info["type"]);
							$op->set_value("property_name", $value_info["name"]);
							$op->set_value("property_value_id", $value_info["value_id"]);
							$op->set_value("property_value", $value_info["value"]);
							$op->set_value("property_price", $value_info["price"]);
							$op->set_value("property_weight", 0);
							$op->set_value("tax_free", $value_info["tax_free"]);
							$op->insert_record();
						}
					}
					
					// Generate and update invoice number
					$invoice_number = generate_invoice_number($order_id);
					$sql  = " UPDATE " . $table_prefix . "orders ";
					$sql .= " SET invoice_number=" . $db->tosql($invoice_number, TEXT);
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$db->query($sql);					
						
					// Generate Serial Number and Download Links
					$sql = " DELETE FROM " . $table_prefix ."orders_items_serials WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$db->query($sql);
					$sql = " DELETE FROM " . $table_prefix ."items_downloads WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$db->query($sql);
					
					// Insert items
					$order_item_id = get_db_value("SELECT MAX(order_item_id) FROM " . $table_prefix . "orders_items") + 1;
					for ($i=1;$i<=sizeof($eg->values);$i++) {
						$item_id = $eg->values[$i]["item_id"];
						if ($item_id) {
							// if some product selected add it
							$eg->values[$i]["site_id"] = $r->get_value("site_id");
							$eg->values[$i]["order_item_id"] = $order_item_id;
							$eg->values[$i]["friend_user_id"] = $r->get_value("friend_user_id");
							// check some additional data for product
							$sql  = " SELECT * FROM ".$table_prefix."items ";
							$sql .= " WHERE item_id=".$db->tosql($item_id, INTEGER);
							$db->query($sql);
							if ($db->next_record()) {
								$eg->values[$i]["item_user_id"] = $db->f("user_id");
								$eg->values[$i]["supplier_id"] = $db->f("supplier_id");
					  
								// get recurring parameters 
								$is_recurring = $db->f("is_recurring");
								$recurring_price = $db->f("recurring_price");
								$recurring_period = $db->f("recurring_period");
								$recurring_interval = $db->f("recurring_interval");
								$recurring_payments_total = $db->f("recurring_payments_total");
								$recurring_start_date = $db->f("recurring_start_date", DATETIME);
								$recurring_end_date = $db->f("recurring_end_date", DATETIME);
					  
								// set recurring payments
								$eg->values[$i]["is_recurring"] = $is_recurring;
								$eg->values[$i]["recurring_price"] = $recurring_price;
								$eg->values[$i]["recurring_payments_made"] = 0;
								if ($is_recurring) {
									$current_date = va_time();
									$current_ts = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY], $current_date[YEAR]);
									$recurring_next_payment = 0; $recurring_end_ts = 0;
									if (is_array($recurring_start_date)) {
										$recurring_start_ts = mktime (0, 0, 0, $recurring_start_date[MONTH], $recurring_start_date[DAY], $recurring_start_date[YEAR]);
										if ($recurring_start_ts > $current_ts) {
											$recurring_next_payment = $recurring_start_ts;
										}
									}
									if (!$recurring_next_payment) {
										if ($recurring_period == 1) {
											$recurring_next_payment = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY] + $recurring_interval, $current_date[YEAR]);
										} elseif ($recurring_period == 2) {
											$recurring_next_payment = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY] + ($recurring_interval * 7), $current_date[YEAR]);
										} elseif ($recurring_period == 3) {
											$recurring_next_payment = mktime (0, 0, 0, $current_date[MONTH] + $recurring_interval, $current_date[DAY], $current_date[YEAR]);
										} else {
											$recurring_next_payment = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY], $current_date[YEAR] + $recurring_interval);
										}
									}
									if (is_array($recurring_end_date)) {
										$recurring_end_ts = mktime (0, 0, 0, $recurring_end_date[MONTH], $recurring_end_date[DAY], $recurring_end_date[YEAR]);
										if ($recurring_next_payment > $recurring_end_ts) {
											$recurring_next_payment = 0;
										}
									}
				      
									$eg->values[$i]["recurring_period"] = $recurring_period;
									$eg->values[$i]["recurring_interval"] = $recurring_interval;
									$eg->values[$i]["recurring_payments_total"] = $recurring_payments_total;
									$eg->values[$i]["recurring_end_date"] = $recurring_end_date;
									if ($recurring_next_payment) {
										$eg->values[$i]["recurring_next_payment"] = $recurring_next_payment;
										$eg->values[$i]["recurring_plan_payment"] = $recurring_next_payment;
									}
								} // end of recurring parameters
							}
					  
							if ($eg->set_record($i)) {
								before_orders_items_save($eg->values[$i]['item_id']);
								$eg->record->insert_record();
								after_orders_items_save($order_id, $user_id, $order_item_id, $eg->values[$i]['item_id']);	
								$order_item_id++;					
							}
						}
					}					
					
					// All other things
					$status_error = "";
					$order_status = get_param("order_status");
					if(!$order_status) {
						$order_status = 1;
					}
					update_order_status($r->get_value("order_id"), $order_status, false, "", $status_error);				
					if ($status_error) {
						$r->errors = $status_error . "'<br>";
					}
				}
				
				// Processing order
				if ($operation == "process" && !strlen($r->errors)) {
					// Coupon processing
					$coupon_id = "";
					$coupon_errors = "";
					$coupons_enable = get_setting_value($settings, "coupons_enable");
					$coupon_code = trim(get_param("coupon_code"));
					if ($coupons_enable && strlen($coupon_code)) {
						$r->set_value("coupons_ids", "");
					}
					// Payment processing
					if (strlen($r->get_value("order_id"))) {
						include_once("./admin_order_payment.php");
						$sql = "SELECT error_message, pending_message, success_message FROM " . $table_prefix . "orders WHERE order_id = " . $db->tosql($r->get_value("order_id"), INTEGER);
						$db->query($sql);
						if ($db->next_record($sql)) {
							$error_message = $db->f("error_message");
							$pending_message = $db->f("pending_message");
							$success_message = $db->f("success_message");
							if (strlen($error_message)) {
								$r->errors = PROCESSING_ORDER_ERROR_MSG . $error_message . "'<br>";
							} elseif (strlen($pending_message)) {
								$r->errors = "<font color='#808000'>" . ORDER_IS_PENDING_MSG ." '". $pending_message . "'</font><br>";
							} else {
								$r->errors = "<font color='#009900'>" . ORDER_WAS_CHARGED_MSG . $success_message . "</font><br>";
								$r->get_db_values();
							}
						}
					}
				}

			}
		}

		if ($is_valid && !strlen($r->errors) && strlen($r->return_page) && $operation != "refresh")
		{
			header("Location: " . $r->return_page);
			exit;
		}
	}
	elseif ($more_items) 
	{
		$number_items += 5;
	}
	elseif (strlen($r->get_value("order_id")) && !$more_items) // show existing values 
	{
		$r->get_db_values();
		$eg->set_value("order_id", $order_id);
		$eg->change_property("order_item_id", USE_IN_SELECT, true);
		$eg->change_property("order_item_id", USE_IN_WHERE, false);
		$eg->change_property("order_id", USE_IN_WHERE, true);
		$eg->change_property("order_id", USE_IN_SELECT, true);
		$number_items = $eg->get_db_values();
		if ($number_items == 0) $number_items = 5;

		// Month and Year lists
		$start_date = $r->get_value("cc_start_date");
		$expiry_date = $r->get_value("cc_expiry_date");
		if (is_array($start_date)) {
			$cc_start_year   = intval($start_date[0]);
			$cc_start_month  = intval($start_date[1]);
		}
		if (is_array($expiry_date)) {
			$cc_expiry_year  = intval($expiry_date[0]);
			$cc_expiry_month = intval($expiry_date[1]);
		}
	}
	else // set default values
	{
		$r->set_value("user_id", "0");
		$r->set_value("is_placed", "0");
		$r->set_value("is_call_center", "1");
		$number_items = 5;

		$default_currency_code = get_db_value("SELECT currency_code FROM ".$table_prefix."currencies WHERE is_default=1");
		$r->set_value("default_currency_code", $default_currency_code);
		$r->set_value("currency_code", $currency["code"]);
		$r->set_value("currency_rate", $currency["rate"]);	
		$r->set_value("payment_id", $payment_id);
	}

	$t->set_var("number_items", $number_items);
	
	// Credit card date lists
	$current_date = va_time();
	$cc_start_years = get_db_values("SELECT start_year AS year_value, start_year AS year_description FROM " . $table_prefix . "cc_start_years", array(array("", YEAR_MSG)));
	if (sizeof($cc_start_years) < 2) {
		$cc_start_years = array(array("", YEAR_MSG));
		for ($y = 7; $y >= 0; $y--) {
			$cc_start_years[] = array($current_date[YEAR] - $y, $current_date[YEAR] - $y);
		}
	}
	$cc_expiry_years = get_db_values("SELECT expiry_year AS year_value, expiry_year AS year_description FROM " . $table_prefix . "cc_expiry_years", array(array("", YEAR_MSG)));
	if (sizeof($cc_expiry_years) < 2) {
		$cc_expiry_years = array(array("", YEAR_MSG));
		for ($y = 0; $y <= 7; $y++) {
			$cc_expiry_years[] = array($current_date[YEAR] + $y, $current_date[YEAR] + $y);
		}
	}
	set_options($cc_start_years, $cc_start_year, "cc_start_year");
	set_options($cc_expiry_years, $cc_expiry_year, "cc_expiry_year");

	$cc_months = array_merge (array(array("", MONTH_MSG)), $months);
	set_options($cc_months, $cc_start_month, "cc_start_month");
	set_options($cc_months, $cc_expiry_month, "cc_expiry_month");

	$eg->set_parameters_all($number_items);
	$r->set_parameters();
	

	// Show shipping types
	if ($country_id) {		
		for ($st = 0; $st < sizeof($shipping_types); $st++) {			
			if ($shipping_types[$st]['row_shipping_type_id'] == $r->get_value("shipping_type_id")) {
				$t->set_var("shipping_type_checked", "checked");
			} else {
				$t->set_var("shipping_type_checked", "");
				$t->set_var("shipping_cost_selected", "");
			}
			$t->set_var("shipping_type_id",   $shipping_types[$st]['row_shipping_type_id']);
			$t->set_var("shipping_cost_desc", currency_format($shipping_types[$st]['row_shipping_cost']));
			$t->set_var("shipping_type_desc", $shipping_types[$st]['row_shipping_type_desc']);
			$t->parse("shipping_types", true);
		}
		if ($total_shipping_types == 1) {
			$t->set_var("shipping_types", "");
			$t->parse("shipping_type", false);
		}
	}
	if ($total_shipping_types > 0) {
		$t->parse("shipping", false);
	}
	if ($personal_number > 0 || $personal_properties) {
		$t->sparse("personal", false);
	}

	if ($delivery_number > 0 || $delivery_properties) {
		$t->sparse("delivery", false);
	}	

	if ($payment_params > 0 || $payment_properties) {
		$t->parse("payment", false);
	}
	
	// Parse buttons
	if (strlen($r->get_value("order_id"))) {
		if ($r->get_value("is_placed") == 0) {
			$t->set_var("process_button", UPDATE_AND_PROCESS_MSG);
			$t->parse("process_button_block", false);
			$t->set_var("save_button_block", "");
		} else {
			// redirect to order info screen
			header("Location: " . $order_details_site_url . "admin_order.php?order_id=" . $r->get_value("order_id"));
			exit;
		}
		$t->sparse("packing_slip", false);
		$t->sparse("notes", false);
		if (isset($permissions["remove_orders"]) && $permissions["remove_orders"] == 1) {
			$t->sparse("remove_order_link", false);
		}
		$t->set_var("delete", "");
	} else {
		$t->set_var("save_button_block", "");
		$t->set_var("process_button", SAVE_AND_PROCESS_MSG);
		$t->parse("process_button_block", false);
		$t->set_var("delete", "");
		$t->set_var("remove_order_link", "");
	}
	
	$t->parse("order_details", false);
	if ($sitelist) {
		$t->parse('sitelist');		
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	
	function make_seed()
	{
	    list($usec, $sec) = explode(' ', microtime());
    	return (float) $sec + ((float) $usec * 100000);
	}

	function insert_user_info($user_info) 
	{
		global $settings, $table_prefix, $db, $call_center_user_parameters;

		$user_id = intval($user_info["user_id"]);

		if (isset($user_info["phone"]) && strlen($user_info["phone"]) && !$user_id) {
			// erase all symbols from phone number except digits
			$login = preg_replace("/\D+/i", "", $user_info["phone"]);
			$user_id = intval(get_db_value("SELECT user_id FROM " . $table_prefix . "users WHERE login = " . $db->tosql($login, TEXT)));
		} else {
			$login = "";
		}

		$usr = new VA_Record($table_prefix . "users");
		$usr->add_where("user_id", INTEGER);
		$usr->set_value("user_id", $user_id);
		
		if (!$user_id) {
			$usr->add_textbox("login", TEXT, LOGIN_BUTTON);
			$usr->change_property("login", REQUIRED, true);
			$usr->change_property("login", UNIQUE, true);
			//$usr->change_property("login", MIN_LENGTH, 3);
			$usr->set_value("login", $login);
			$usr->add_textbox("password", TEXT, PASSWORD_FIELD);
			//$usr->change_property("password", REQUIRED, true);
			//$usr->change_property("password", MIN_LENGTH, 3);
			mt_srand(make_seed());
			$usr->set_value("password", mt_rand());
			$type_id = intval(get_db_value("SELECT type_id FROM " . $table_prefix . "user_types WHERE is_default = 1"));
			$usr->add_textbox("user_type_id", INTEGER);
			$usr->set_value("user_type_id", $type_id);
		}
		$usr->add_textbox("name", TEXT, NAME_MSG);
		$usr->change_property("name", USE_SQL_NULL, false);
		$usr->add_textbox("first_name", TEXT, FIRST_NAME_FIELD);
		$usr->change_property("first_name", USE_SQL_NULL, false);
		$usr->add_textbox("last_name", TEXT, LAST_NAME_FIELD);
		$usr->change_property("last_name", USE_SQL_NULL, false);
		$usr->add_textbox("company_id", INTEGER, COMPANY_SELECT_FIELD);
		$usr->add_textbox("company_name", TEXT, COMPANY_NAME_FIELD);
		$usr->add_textbox("email", TEXT, EMAIL_FIELD);
		$usr->change_property("email", USE_SQL_NULL, false);
		$usr->change_property("email", REGEXP_MASK, EMAIL_REGEXP);
		$usr->add_textbox("address1", TEXT, STREET_FIRST_FIELD);
		$usr->add_textbox("address2", TEXT, STREET_SECOND_FIELD);
		$usr->add_textbox("city", TEXT, CITY_FIELD);
		$usr->add_textbox("province", TEXT, PROVINCE_FIELD);
		$usr->add_textbox("state_id", INTEGER, STATE_FIELD);
		$usr->change_property("state_id", DEFAULT_VALUE, get_setting_value($settings, "state_id", 0));
		$usr->change_property("state_id", USE_SQL_NULL, false);
		$usr->add_textbox("zip", TEXT, ZIP_FIELD);
		$usr->add_textbox("country_id", INTEGER, COUNTRY_FIELD);
		$usr->change_property("country_id", DEFAULT_VALUE, get_setting_value($settings, "country_id", 0));
		$usr->change_property("country_id", USE_SQL_NULL, false);		
		$usr->add_textbox("phone", TEXT, PHONE_FIELD);
		$usr->add_textbox("daytime_phone", TEXT, DAYTIME_PHONE_FIELD);
		$usr->add_textbox("evening_phone", TEXT, EVENING_PHONE_FIELD);
		$usr->add_textbox("cell_phone", TEXT, CELL_PHONE_FIELD);
		$usr->add_textbox("fax", TEXT, FAX_FIELD);
		
		$usr->add_textbox("delivery_name", TEXT, DELIVERY_MSG." ".NAME_MSG);
		$usr->add_textbox("delivery_first_name", TEXT, DELIVERY_MSG." ".FIRST_NAME_FIELD);
		$usr->add_textbox("delivery_last_name", TEXT, DELIVERY_MSG." ".LAST_NAME_FIELD);
		$usr->add_textbox("delivery_company_id", INTEGER, DELIVERY_MSG." ".COMPANY_SELECT_FIELD);
		$usr->add_textbox("delivery_company_name", TEXT, DELIVERY_MSG." ".COMPANY_NAME_FIELD);
		$usr->add_textbox("delivery_email", TEXT, DELIVERY_MSG." ".EMAIL_FIELD);
		$usr->change_property("delivery_email", REGEXP_MASK, EMAIL_REGEXP);
		$usr->add_textbox("delivery_address1", TEXT, DELIVERY_MSG." ".STREET_FIRST_FIELD);
		$usr->add_textbox("delivery_address2", TEXT, DELIVERY_MSG." ".STREET_SECOND_FIELD);
		$usr->add_textbox("delivery_city", TEXT, DELIVERY_MSG." ".CITY_FIELD);
		$usr->add_textbox("delivery_province", TEXT, DELIVERY_MSG." ".PROVINCE_FIELD);
		$usr->add_textbox("delivery_state_id", INTEGER, DELIVERY_MSG." ".STATE_FIELD);
		$usr->change_property("delivery_state_id", DEFAULT_VALUE, get_setting_value($settings, "state_id", 0));
		$usr->change_property("delivery_state_id", USE_SQL_NULL, false);
		$usr->add_textbox("delivery_zip", TEXT, DELIVERY_MSG." ".ZIP_FIELD);
		$usr->add_textbox("delivery_country_id", INTEGER, DELIVERY_MSG." ".COUNTRY_FIELD);
		$usr->change_property("delivery_country_id", DEFAULT_VALUE, get_setting_value($settings, "country_id", 0));
		$usr->change_property("delivery_country_id", USE_SQL_NULL, false);
		$usr->add_textbox("delivery_phone", TEXT, DELIVERY_MSG." ".PHONE_FIELD);
		$usr->add_textbox("delivery_daytime_phone", TEXT, DELIVERY_MSG." ".DAYTIME_PHONE_FIELD);
		$usr->add_textbox("delivery_evening_phone", TEXT, DELIVERY_MSG." ".EVENING_PHONE_FIELD);
		$usr->add_textbox("delivery_cell_phone", TEXT, DELIVERY_MSG." ".CELL_PHONE_FIELD);
		$usr->add_textbox("delivery_fax", TEXT, DELIVERY_MSG." ".FAX_FIELD);
		
		$usr->add_textbox("friendly_url", TEXT);
		$usr->change_property("friendly_url", USE_SQL_NULL, false);
		$usr->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
		$usr->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
		$usr->add_textbox("affiliate_code", TEXT);
		$usr->change_property("affiliate_code", USE_SQL_NULL, false);
		
		$usr->add_textbox("registration_date", DATETIME);
		$usr->change_property("registration_date", USE_IN_SELECT, false);
		$usr->change_property("registration_date", USE_IN_UPDATE, false);
		$usr->add_textbox("modified_date", DATETIME);
		$usr->change_property("modified_date", USE_IN_SELECT, false);

		for ($i = 0; $i < sizeof($call_center_user_parameters); $i++) { 
			$user_param = $call_center_user_parameters[$i];
			if (isset($user_info[$user_param])) {
				$usr->set_value($user_param, $user_info[$user_param]);
			}
		}

		$usr->validate();

		if (!strlen($usr->errors)) {
			$current_date = va_time();
			if ($user_id) {
				$usr->set_value("modified_date", $current_date);
				$usr->update_record();
			} else {
				$password_encrypt = get_setting_value($settings, "password_encrypt", 0);
				if ($password_encrypt == 1) {
					$usr->set_value("password", md5($usr->get_value("password")));
				}
				$usr->set_value("registration_date", $current_date);
				$usr->set_value("modified_date", $current_date);
				$usr->insert_record();
				$user_id = intval(get_db_value("SELECT MAX(user_id) FROM " . $table_prefix . "users"));
			}
		}
		
		return $user_id;
	}
	
	function get_last_customer($type = 1) 
	{
		global $r, $table_prefix, $db;
		
		if ($type > 1) {
			$sql  = " SELECT u.* ";
			$sql .= " FROM (" . $table_prefix . "orders o ";
			$sql .= " INNER JOIN " . $table_prefix . "users u ON u.user_id=o.user_id) ";
		} else {
			$sql  = " SELECT o.* ";
			$sql .= " FROM " . $table_prefix . "orders o ";
		}
		$sql .= " WHERE o.is_call_center=1 ";
		$sql .= " ORDER BY o.order_id DESC ";
		
		$db->query($sql);
		
		if ($db->next_record()) {

			$r->set_value("user_id",       $db->f('user_id'));
			$r->set_value("name",          $db->f('name'));
			$r->set_value("first_name",    $db->f('first_name'));
			$r->set_value("last_name",     $db->f('last_name'));
			$r->set_value("company_id",    $db->f('company_id'));
			$r->set_value("company_name",  $db->f('company_name'));
			$r->set_value("email",         $db->f('email'));
			$r->set_value("address1",      $db->f('address1'));
			$r->set_value("address2",      $db->f('address2'));
			$r->set_value("city",          $db->f('city'));
			$r->set_value("province",      $db->f('province'));
			$r->set_value("state_id",      $db->f('state_id'));
			$r->set_value("zip",           $db->f('zip'));
			$r->set_value("country_id",    $db->f('country_id'));
			$r->set_value("phone",         $db->f('phone'));
			$r->set_value("daytime_phone", $db->f('daytime_phone'));
			$r->set_value("evening_phone", $db->f('evening_phone'));
			$r->set_value("cell_phone",    $db->f('cell_phone'));
			$r->set_value("fax",           $db->f('fax'));
			
			$r->set_value("delivery_name",          $db->f('delivery_name'));
			$r->set_value("delivery_first_name",    $db->f('delivery_first_name'));
			$r->set_value("delivery_last_name",     $db->f('delivery_last_name'));
			$r->set_value("delivery_company_id",    $db->f('delivery_company_id'));
			$r->set_value("delivery_company_name",  $db->f('delivery_company_name'));
			$r->set_value("delivery_email",         $db->f('delivery_email'));
			$r->set_value("delivery_address1",      $db->f('delivery_address1'));
			$r->set_value("delivery_address2",      $db->f('delivery_address2'));
			$r->set_value("delivery_city",          $db->f('delivery_city'));
			$r->set_value("delivery_province",      $db->f('delivery_province'));
			$r->set_value("delivery_state_id",      $db->f('delivery_state_id'));
			$r->set_value("delivery_zip",           $db->f('delivery_zip'));
			$r->set_value("delivery_country_id",    $db->f('delivery_country_id'));
			$r->set_value("delivery_phone",         $db->f('delivery_phone'));
			$r->set_value("delivery_daytime_phone", $db->f('delivery_daytime_phone'));
			$r->set_value("delivery_evening_phone", $db->f('delivery_evening_phone'));
			$r->set_value("delivery_cell_phone",    $db->f('delivery_cell_phone'));
			$r->set_value("delivery_fax",           $db->f('delivery_fax'));			
		}	
	}

	function before_order_save() 
	{
		global $r, $db, $table_prefix;
		
		$r->set_value("remote_address", get_ip());

		$state_id = $r->get_value("state_id");
		if ($state_id) {
			$sql  = " SELECT * FROM " . $table_prefix . "states ";
			$sql .= " WHERE state_id=" . $db->tosql($state_id, INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$r->set_value("state_code", $db->f("state_code"));
			}
		}
		
		$country_id = $r->get_value("country_id");
		if ($country_id) {
			$sql  = " SELECT * FROM " . $table_prefix . "countries ";
			$sql .= " WHERE country_id=" . $db->tosql($country_id, INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$r->set_value("country_code", $db->f("country_code"));
			}
		}
		
		$delivery_state_id = $r->get_value("delivery_state_id");
		if ($delivery_state_id) {
			$sql  = " SELECT * FROM " . $table_prefix . "states ";
			$sql .= " WHERE state_id=" . $db->tosql($delivery_state_id, INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$r->set_value("delivery_state_code", $db->f("delivery_state_code"));
			}
		}
		
		$delivery_country_id = $r->get_value("delivery_country_id");
		if ($delivery_country_id) {
			$sql  = " SELECT * FROM " . $table_prefix . "countries ";
			$sql .= " WHERE country_id=" . $db->tosql($delivery_country_id, INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$r->set_value("delivery_country_code", $db->f("delivery_country_code"));
			}
		}		
	}
		
	function before_orders_items_save($item_id) {
		global $table_prefix, $db, $eg, $r;	

		$eg->set_value("order_id",     $r->get_value("order_id"));
		$eg->set_value("user_id",      $r->get_value("user_id"));
		$eg->set_values("item_status", $r->get_value("order_status"));
					
		$sql = "SELECT * FROM " . $table_prefix ."items WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {			
			$eg->record->add_textbox("item_type_id", INTEGER);		
			$eg->record->set_value("item_type_id", $db->f("item_type_id"));			
			$eg->record->add_textbox("downloadable", INTEGER);		
			$eg->record->set_value("downloadable", $db->f("downloadable"));			
			$eg->record->add_textbox("real_price", NUMBER);	
			$eg->record->set_value("real_price", $eg->record->get_value("price"));
		}
	}

	
	function after_orders_items_save($order_id, $user_id, $order_item_id, $item_id) 
	{
		global $table_prefix, $db_type, $db, $eg;		
				
		$quantity        = $eg->record->get_value("quantity");
		$generation_type = 0;		
		$downloadable    = 0;
		
		$sql = "SELECT * FROM " . $table_prefix ."items WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {			
			$generation_type = $db->f("generate_serial");			
			$downloadable    = $db->f("downloadable"); 		
			$download_path   = $db->f("download_path");
			$max_downloads   = $db->f("max_downloads");
			$download_period = $db->f("download_period");
		}
				
		$product_info    = array("item_id" => $item_id);		
		
		if ($generation_type > 0) {
			for ($sn = $quantity; $sn > 0; $sn--) {			
				$serial_number = generate_serial($order_item_id, $sn, $product_info, $generation_type);				
				if ($serial_number) {
					$sql  = " INSERT INTO " . $table_prefix ."orders_items_serials (order_id, user_id, order_item_id, item_id, serial_number, serial_added) ";
					$sql .= " VALUES (" . $db->tosql($order_id, INTEGER) . ", ";
					$sql .= $db->tosql($user_id, INTEGER, true, false) . ",";
					$sql .= $db->tosql($order_item_id, INTEGER, true, false) . ",";
					$sql .= $db->tosql($item_id, INTEGER, true, false) . ",";
					$sql .= $db->tosql($serial_number, TEXT) . ",";
					$sql .= $db->tosql(va_time(), DATETIME) . ")";
					$db->query($sql);
				}
			}
		}

		if ($downloadable) {
			$download_paths = split(";", $download_path);
			for ($dl = 0; $dl < sizeof($download_paths); $dl++) {
				$download_path_cur = trim($download_paths[$dl]);
				if ($download_path_cur) {
					$sql  = " INSERT INTO " . $table_prefix ."items_downloads (order_id, user_id, order_item_id, item_id, ";
					$sql .= "download_path, max_downloads, download_added,download_expiry) ";
					$sql .= " VALUES (" . $db->tosql($order_id, INTEGER) . ", ";
					$sql .= $db->tosql($user_id, INTEGER, true, false) . ",";
					$sql .= $db->tosql($order_item_id, INTEGER, true, false) . ",";
					$sql .= $db->tosql($item_id, INTEGER, true, false) . ",";
					$sql .= $db->tosql($download_path_cur, TEXT) . ",";
					$sql .= $db->tosql($max_downloads * $quantity, INTEGER) . ",";
					$sql .= $db->tosql(va_time(), DATETIME) . ",";
					if (strlen($download_period)) {
						$download_expiry =  va_timestamp() + (intval($download_period) * 86400);
						$sql .= $db->tosql(va_time($download_expiry), DATETIME) . ")";
					} else {
						$sql .= "NULL)";
					}
					$db->query($sql);
				}
			}
		}	
	}

	function save_order_taxes($order_id, $tax_rates)
	{
		global $table_prefix, $db_type, $db;		

		// save tax rates for submitted order
		if (is_array($tax_rates)) {
			$ot = new VA_Record($table_prefix . "orders_taxes");
			$ot->add_where("order_tax_id", INTEGER);
			$ot->add_textbox("order_id", INTEGER);
			$ot->set_value("order_id", $order_id);
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

	}

?>