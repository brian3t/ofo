<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/order_items.php");
	include_once($root_folder_path . "includes/order_links.php");
	include_once($root_folder_path . "includes/parameters.php");
	include_once($root_folder_path . "includes/shopping_cart.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");
	$order_id = get_param("order_id");
	$currency = get_currency();
	$orders_currency = get_setting_value($settings, "orders_currency", 0);

	// connection to delete items
	$dbi = new VA_SQL();
	$dbi->DBType      = $db_type;
	$dbi->DBDatabase  = $db_name;
	$dbi->DBUser      = $db_user;
	$dbi->DBPassword  = $db_password;
	$dbi->DBHost      = $db_host;
	$dbi->DBPort      = $db_port;
	$dbi->DBPersistent= $db_persistent;

	$sql = " SELECT site_id FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$site_id = get_db_value($sql);

	$order_info = array();
	$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings";
	$sql .= " WHERE setting_type='order_info'";
	$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id,INTEGER) . ") ";
	$sql .= " ORDER BY site_id ASC ";
	$db->query($sql);
	while ($db->next_record()) {
		$order_info[$db->f("setting_name")] = $db->f("setting_value");
	}

	$permissions = get_permissions();

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_order.html");
	$t->set_var("order_id", $order_id);
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_ORDER_MSG, CONFIRM_DELETE_MSG));

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$r = new VA_Record($table_prefix . "orders");
	$r->return_page = $orders_list_site_url . "admin_orders.php";
	$r->add_hidden("s_on", TEXT);
	$r->add_hidden("s_ne", TEXT);
	$r->add_hidden("s_kw", TEXT);
	$r->add_hidden("s_sd", TEXT);
	$r->add_hidden("s_ed", TEXT);
	$r->add_hidden("s_os", TEXT);
	$r->add_hidden("s_cc", TEXT);
	$r->add_hidden("s_sc", TEXT);
	$r->add_hidden("s_ex", TEXT);
	$r->add_hidden("page", TEXT);
	$r->add_hidden("sort_dir", TEXT);
	$r->add_hidden("sort_ord", TEXT);

	$r->add_where("order_id", INTEGER);
	$sql = "SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE is_active=1 ORDER BY status_order, status_id";
	$order_statuses = get_db_values($sql, "");
	$r->add_select("order_status", INTEGER, $order_statuses);
	$r->get_form_values();

	$operation = get_param("operation");
	$order_id = get_param("order_id");

	$admin_orders_url = $r->get_return_url();
	$return_page = $admin_orders_url;

	$t->set_var("admin_href",               "admin.php");
	$t->set_var("admin_orders_href",        $orders_list_site_url . "admin_orders.php");
	$t->set_var("admin_order_href",         $order_details_site_url . "admin_order.php");
	$t->set_var("admin_order_edit_href",    $order_details_site_url . "admin_order_edit.php");
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
	$t->set_var("admin_orders_url", 		$admin_orders_url);
	
	if (strlen($operation))
	{
		if ($operation == "delete") {
			if (!isset($permissions["remove_orders"]) || $permissions["remove_orders"] != 1) {
				$r->errors .= NOT_ALLOWED_REMOVE_ORDERS_INFO_MSG;
			} elseif (!strlen($order_id)) {
				$r->errors .= MISSING_ORDER_NUMBER_MSG."<br>";
			} else {
				remove_orders($order_id);
			}
		} elseif ($operation == "save") {
			if (!isset($permissions["update_orders"]) || $permissions["update_orders"] != 1) {
				$r->errors .= NOT_ALLOWED_UPDATE_ORDERS_INFO_MSG;
			} elseif (!strlen($order_id)) {
				$r->errors .= MISSING_ORDER_NUMBER_MSG."<br>";
			} else {
				update_order_status($order_id, $r->get_value("order_status"), true, "", $status_error);
			}
		} elseif ($operation == "update") {
			if (!isset($permissions["update_orders"]) || $permissions["update_orders"] != 1) {
				$r->errors .= NOT_ALLOWED_UPDATE_ORDERS_INFO_MSG;
			} else {
				$updated_fields = "";

				// update shipping tracking information
				$current_tracking_id = get_param("current_tracking_id");
				$shipping_tracking_id = get_param("shipping_tracking_id");
				if ($current_tracking_id != $shipping_tracking_id) {
					$sql  = " UPDATE " . $table_prefix . "orders SET shipping_tracking_id=" . $db->tosql($shipping_tracking_id, TEXT);
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$db->query($sql);
					if ($updated_fields) { $updated_fields .= ", "; }
					$updated_fields .= SHIPPING_TRACKING_NUMBER_MSG;
				}

				// update shipping type 
				$shipping_type_id = get_param("shipping_type_id");
				// ** EGGHEAD VENTURES ADD
				$sql = "SELECT shipping_type_code, shipping_type_desc from " . $table_prefix . "shipping_types where shipping_type_id = " . $shipping_type_id;
				$db->query($sql);
				while ($db->next_record()) {
					$shipping_type_code = $db->f("shipping_type_code");
					$shipping_type_desc = $db->f("shipping_type_desc");
				}
				$sql = "SELECT shipping_type_desc from " . $table_prefix . "orders where order_id = " . $order_id;
				$db->query($sql);
				while ($db->next_record()) {
					$current_shipping_type = $db->f("shipping_type_desc");
				}
				$sql = "UPDATE " . $table_prefix . "orders set shipping_type_code = '" . $shipping_type_code . "', shipping_type_desc = '" . $shipping_type_desc . "', shipping_type_id = ". $shipping_type_id . " where order_id = " . $order_id;
				$db->query($sql);
				$shipping_updated = "updated";
				$eghd = new VA_Record($table_prefix . "orders_events");
					$eghd->add_textbox("order_id", INTEGER);
					$eghd->add_textbox("status_id", INTEGER);
					$eghd->add_textbox("admin_id", INTEGER);
					$eghd->add_textbox("event_date", DATETIME);
					$eghd->add_textbox("event_type", TEXT);
					$eghd->add_textbox("event_name", TEXT);
					$eghd->add_textbox("event_description", TEXT);
					$eghd->set_value("order_id", $order_id);
					$eghd->set_value("status_id", 0);
					$eghd->set_value("admin_id", get_session("session_admin_id"));
					$eghd->set_value("event_date", va_time());
					$eghd->set_value("event_type", "update_order_shipping");
					$eghd->set_value("event_name", $current_shipping_type . " &ndash;&gt; " . $shipping_type_desc);
					$eghd->insert_record();
				// ** END
				//$shipping_updated = update_order_shipping($order_id, $shipping_type_id);  ** EGGHEAD VENTURES Commented Out
				if (strlen($shipping_updated)) {
					if ($updated_fields) { $updated_fields .= ", "; }
					$updated_fields .= SHIPPING_METHOD_MSG;
				}

				// update order status
				$order_items = get_param("order_items");
				$status_updated = update_order_status($order_id, $r->get_value("order_status"), true, $order_items, $status_error);
				if ($status_updated) {
					if ($updated_fields) { $updated_fields .= ", "; }
					$updated_fields .= STATUS_MSG;
				} 

				if (strlen($status_error)) {
					$r->errors .= $status_error . "<br>";
				} else {
					$return_page = "";
					if ($updated_fields) {
						$r->errors  = "<font color=blue><b>";
						$r->errors .= $updated_fields . "</b>";
						$r->errors .= " successfully updated.</font>";
					}
				}
			}
		} elseif ($operation == "add_ip") {
			$ip = get_param("ip");
			if (!strlen($ip)) {
				$r->errors .= MISSING_IP_ADDRESS_MSG;
			} elseif (!isset($permissions["update_orders"]) || $permissions["update_orders"] != 1) {
				$r->errors .= NOT_ALLOWED_UPDATE_ORDERS_INFO_MSG;
			} else {
				$sql  = "SELECT ip_address FROM " . $table_prefix . "black_ips WHERE ip_address=" . $db->tosql($ip, TEXT);
				$db->query($sql);
				if (!$db->next_record()) {
					$sql  = " INSERT INTO " . $table_prefix . "black_ips (ip_address, address_action) VALUES (";
					$sql .= $db->tosql($ip, TEXT) . ", 1)";
					$db->query($sql);
				}
				$return_page = "";
			}
		} elseif ($operation == "remove_ip") {
			$ip = get_param("ip");
			if (!strlen($ip)) {
				$r->errors .= MISSING_IP_ADDRESS_MSG;
			} elseif (!isset($permissions["update_orders"]) || $permissions["update_orders"] != 1) {
				$r->errors .= NOT_ALLOWED_UPDATE_ORDERS_INFO_MSG;
			} else {
				$sql  = " DELETE FROM " . $table_prefix . "black_ips WHERE ip_address=" . $db->tosql($ip, TEXT);
				$db->query($sql);
				$return_page = "";
			}
		}

		if (!strlen($r->errors) && strlen($return_page)) {
			header("Location: " . $return_page);
			exit;
		}
	}

	// set file once more time to load basket tag properly
	$t->set_file("main", "admin_order.html");
	$items_text = show_order_items($order_id, true, "admin_order");

	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "orders_notes oc ";
	$sql .= " WHERE oc.order_id=" . $db->tosql($order_id, INTEGER);
	$total_notes = get_db_value($sql);
	$t->set_var("total_notes", $total_notes);
	if ($total_notes > 0) {
		$t->set_var("notes_style", "font-weight: bold; color: blue;");
	} else {
		$t->set_var("notes_style", "");
	}

	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "items_downloads id ";
	$sql .= " WHERE id.order_id=" . $db->tosql($order_id, INTEGER);
	$total_links = get_db_value($sql);
	$t->set_var("total_links", $total_links);

	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "items_downloads id ";
	$sql .= " WHERE id.order_id=" . $db->tosql($order_id, INTEGER);
	$total_links = get_db_value($sql);
	$t->set_var("total_links", $total_links);

	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "orders_items_serials ois ";
	$sql .= " WHERE ois.order_id=" . $db->tosql($order_id, INTEGER);
	$total_serials = get_db_value($sql);
	$t->set_var("total_serials", $total_serials);

	$sql  = " SELECT COUNT(*) FROM (" . $table_prefix . "orders_items oi ";
	$sql .= " LEFT JOIN " . $table_prefix . "item_types it ON oi.item_type_id=it.item_type_id) ";
	$sql .= " WHERE oi.order_id=" . $db->tosql($order_id, INTEGER);
	$sql .= " AND it.is_gift_voucher=1 ";
	$vouchers_number = get_db_value($sql);
	if ($vouchers_number) {
		$t->parse("vouchers_link", false);
	}

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

	$r->add_textbox("parent_order_id", INTEGER);
	$r->add_textbox("invoice_number", TEXT);
	$r->add_textbox("order_placed_date", DATETIME);
	$r->change_property("order_placed_date", VALUE_MASK, $datetime_show_format);
	$r->add_textbox("user_id", INTEGER);
	$r->add_textbox("payment_id", INTEGER);
	$r->add_textbox("transaction_id", TEXT);
	$r->add_textbox("affiliate_code", TEXT);
	$r->add_textbox("error_message", TEXT);
	$r->add_textbox("pending_message", TEXT);
	$r->add_textbox("currency_code", TEXT);
	$r->add_textbox("shipping_tracking_id", TEXT);
	$r->add_textbox("remote_address", TEXT);
	$r->add_textbox("cc_name", TEXT);
	$r->add_textbox("cc_first_name", TEXT);
	$r->add_textbox("cc_last_name", TEXT);
	$r->add_textbox("cc_number", TEXT);
	$r->add_textbox("cc_start_date", DATETIME);
	$r->change_property("cc_start_date", VALUE_MASK, array("MM", " / ", "YYYY"));
	$r->add_textbox("cc_expiry_date", DATETIME);
	$r->change_property("cc_expiry_date", VALUE_MASK, array("MM", " / ", "YYYY"));
	$r->add_textbox("cc_type", INTEGER);
	$r->add_textbox("cc_issue_number", INTEGER);
	$r->add_textbox("cc_security_code", TEXT);
	$r->add_textbox("pay_without_cc", TEXT);

	$r->get_db_values();

	$sql  = " SELECT payment_name ";
	$sql .= " FROM " . $table_prefix . "payment_systems ";
	$sql .= " WHERE payment_id=" . $db->tosql($r->get_value("payment_id"), INTEGER);
	$payment_name = get_db_value($sql);
	$payment_name = get_translation($payment_name);
	$t->set_var("payment_name", $payment_name);
	if (!$r->get_value("parent_order_id")) {
		$r->parameters["parent_order_id"][SHOW] = false;
	}
	if ($r->get_value("invoice_number") == $r->get_value("order_id") || $r->is_empty("invoice_number")) {
		$r->parameters["invoice_number"][SHOW] = false;
	}
	if ($r->is_empty("transaction_id")) {
		$r->parameters["transaction_id"][SHOW] = false;
	}

	$cc_info = array();
	$setting_type = "credit_card_info_" . $r->get_value("payment_id");
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
	$db->query($sql);
	while ($db->next_record()) {
		$cc_info[$db->f("setting_name")] = $db->f("setting_value");
	}
	$cc_number_security = get_setting_value($cc_info, "cc_number_security", 0);
	$cc_code_security = get_setting_value($cc_info, "cc_code_security", 0);
	if ($cc_number_security > 0) {
		$cc_number = $r->get_value("cc_number");
		if (!preg_match("/^[\d\s\*\-]+$/", $cc_number)) {
			$cc_number = va_decrypt($cc_number);
		}
		$r->set_value("cc_number", format_cc_number($cc_number));
	}
	if ($cc_code_security > 0) {
		$r->set_value("cc_security_code", va_decrypt($r->get_value("cc_security_code")));
	}

	$user_id = $r->get_value("user_id");
	$user_email = $r->get_value("email");
	$sql  = " SELECT COUNT(*) AS status_orders, SUM(goods_total) AS status_goods, o.order_status, os.status_name ";
	$sql .= " FROM (" . $table_prefix . "orders o ";
	$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id)";
	$sql .= " WHERE order_id<>" . $db->tosql($order_id, INTEGER);
	if ($user_id > 0) {
		$sql .= " AND (user_id=" . $db->tosql($user_id, INTEGER) . " OR email=" . $db->tosql($user_email, TEXT) . ") ";
	} else {
		$sql .= " AND email=" . $db->tosql($user_email, TEXT);
	}
	$sql .= " GROUP BY o.order_status, os.status_name ";
	$db->query($sql);
	if ($db->next_record()) {
		$total_orders = 0; $total_goods = 0;
		do {
			$user_status = $db->f("status_name");
			if (!$user_status) { $user_status = $db->f("order_status"); }
			$status_orders = $db->f("status_orders");
			$status_goods = $db->f("status_goods");
			$total_orders += $status_orders; $total_goods += $status_goods;
			$t->set_var("user_status", $user_status);
			$t->set_var("status_orders", $status_orders);
			$t->set_var("status_goods", $currency["left"] . number_format($status_goods * $currency["rate"], 2) . $currency["right"]);
			$t->parse("user_statuses", true);
		} while ($db->next_record());

		$t->set_var("total_orders", $total_orders);
		$t->set_var("total_goods", $currency["left"] . number_format($total_goods * $currency["rate"], 2) . $currency["right"]);
		$t->parse("user_stats", false);
	}
	

	// parse url to change currency
	$t->set_var("currency_url", "");
	if ($orders_currency != 1) {
		$order_currency_code = $r->get_value("currency_code");
		if (strlen($order_currency_code) && $currency["code"] != $order_currency_code) {
			$admin_order_currency_url = new VA_URL($order_details_site_url . "admin_order.php", true, array("currency_code", "operation"));
			$admin_order_currency_url->add_parameter("currency_code", CONSTANT, $order_currency_code);
			$t->set_var("currency_code", $order_currency_code);
			$t->set_var("admin_order_currency_url", $admin_order_currency_url->get_url());
			$t->parse("currency_url", false);
		} else {
			$sql = "SELECT currency_code FROM " . $table_prefix . "currencies WHERE is_default=1 ";
			$db->query($sql);
			if ($db->next_record()) {
				$default_currency = $db->f("currency_code");
				if ($currency["code"] != $default_currency) {
					$admin_order_currency_url = new VA_URL($order_details_site_url . "admin_order.php", true, array("currency_code", "operation"));
					$admin_order_currency_url->add_parameter("currency_code", CONSTANT, $default_currency);
					$t->set_var("currency_code", $default_currency);
					$t->set_var("admin_order_currency_url", $admin_order_currency_url->get_url());
					$t->parse("currency_url", false);
				}
			}
		}
	}

	if ($r->is_empty("remote_address")) {
		$r->parameters["remote_address"][SHOW] = false;
	}
	if ($r->get_value("user_id") == 0) {
		$r->parameters["user_id"][SHOW] = false;
	}
	if ($r->is_empty("affiliate_code")) {
		$r->parameters["affiliate_code"][SHOW] = false;
	}

	$payment_params = 0;
	for ($i = 0; $i < sizeof($cc_parameters); $i++) { 
		if ($r->is_empty($cc_parameters[$i])) {
			$r->parameters[$cc_parameters[$i]][SHOW] = false;
		} else {
			$payment_params++;
		}
	}

	$t->set_var("current_tracking_id", $r->get_value("shipping_tracking_id"));
	$t->set_var("current_status", $r->get_value("order_status"));
	$r->set_value("company_id", get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("company_id"), INTEGER, true, false)));
	$r->set_value("delivery_company_id", get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("delivery_company_id"), INTEGER)));
	
	if ($r->parameter_exists("state_id") && $r->get_value("state_id")) {
		$state_name = get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("state_id"), INTEGER, true, false));
	} elseif ($r->parameter_exists("state_code") && $r->get_value("state_code")) {
		$state_name = get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_code=" . $db->tosql($r->get_value("state_code"), TEXT, true, false));	
	} else {
		$state_name = "";
	}
	
	if ($r->parameter_exists("delivery_state_id") && $r->get_value("delivery_state_id")) {
		$delivery_state_name = get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("delivery_state_id"), INTEGER, true, false));
	} elseif ($r->parameter_exists("delivery_state_code") && $r->get_value("delivery_state_code")) {
		$delivery_state_name = get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_code=" . $db->tosql($r->get_value("delivery_state_code"), TEXT, true, false));	
	} else {
		$delivery_state_name = "";
	}
		
	if ($r->parameter_exists("country_id") && $r->get_value("country_id")) {
		$country_name = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("country_id"), INTEGER, true, false));
	} elseif ($r->parameter_exists("country_code") && $r->get_value("country_code")) {
		$country_name = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_code=" . $db->tosql($r->get_value("country_code"), TEXT, true, false));	
	} else {
		$country_name = "";
	}
	
	if ($r->parameter_exists("delivery_country_id") && $r->get_value("delivery_country_id")) {
		$delivery_country_name = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("delivery_country_id"), INTEGER, true, false));
	} elseif ($r->parameter_exists("delivery_country_code") && $r->get_value("delivery_country_code")) {
		$delivery_country_name = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_code=" . $db->tosql($r->get_value("delivery_country_code"), TEXT, true, false));	
	} else {
		$delivery_country_name = "";
	}
	
	$r->set_value("state_id", get_translation($state_name));	
	$r->set_value("country_id", get_translation($country_name));
	$r->set_value("delivery_state_id", get_translation($delivery_state_name));
	$r->set_value("delivery_country_id", get_translation($delivery_country_name));
	$r->change_property("delivery_country_id", SHOW, true);
	
	$r->set_value("cc_type", get_translation(get_db_value("SELECT credit_card_name FROM " . $table_prefix . "credit_cards WHERE credit_card_id=" . $db->tosql($r->get_value("cc_type"), INTEGER))));

	// get payment info if available
	$sql = "SELECT payment_info FROM " . $table_prefix . "payment_systems WHERE payment_id=" . $db->tosql($r->get_value("payment_id"), INTEGER);
	$payment_info = get_db_value($sql);
	$payment_info = get_translation($payment_info);
	$payment_info = get_currency_message($payment_info, $currency);
	if (trim($payment_info)) {
		$payment_params++;
		$t->set_block("payment_info", $payment_info);
		$t->parse("payment_info", false);
		$t->global_parse("payment_info_block", false, false, true);
	} else {
		$t->set_var("payment_info_block", "");
	}
	
	$r->set_parameters();

	if ($r->is_empty("error_message")) {
		$t->set_var("error_message_block", "");
	} else {
		$t->set_var("error_message", $r->get_value("error_message"));
		$t->parse("error_message_block", false);
	}
	if ($r->is_empty("pending_message")) {
		$t->set_var("pending_message_block", "");
	} else {
		$t->set_var("pending_message", $r->get_value("pending_message"));
		$t->parse("pending_message_block", false);
	}

	$remote_address = $r->get_value("remote_address");
	if (strlen($remote_address)) {
		$admin_order_black_url = new VA_URL($order_details_site_url . "admin_order.php", true, array("ip", "operation", "currency_code"));
		$admin_order_black_url->add_parameter("ip", CONSTANT, $remote_address);
		$sql  = " SELECT ip_address FROM " . $table_prefix . "black_ips ";
		$sql .= " WHERE ip_address=" . $db->tosql($remote_address, TEXT);
		$db->query($sql);
		if ($db->next_record()) {
			$admin_order_black_url->add_parameter("operation", CONSTANT, "remove_ip");
			$t->set_var("admin_order_black_url", $admin_order_black_url->get_url());
			$t->parse("black_remote_address", false);
		} else {
			$admin_order_black_url->add_parameter("operation", CONSTANT, "add_ip");
			$t->set_var("admin_order_black_url", $admin_order_black_url->get_url());
			$t->parse("remote_address_info", false);
		}
	}


	if ($personal_number > 0 || $personal_properties) {
		$t->parse("personal", false);
	}

	if ($delivery_number > 0 || $delivery_properties) {
		$t->parse("delivery", false);
	}
	
	$payment_details = get_setting_value($permissions, "order_payment", 0);
	if ($payment_details && ($payment_params > 0 || $payment_properties)) {
		$t->parse("payment", false);
	}

	if (isset($permissions["remove_orders"]) && $permissions["remove_orders"] == 1) {
		$t->parse("remove_order_link", false);
	}

	$events_types = array(
		"activation_added" => ACTIVATION_ADDED_MSG,
		"activation_updated" => ACTIVATION_UPDATED_MSG,
		"activation_removed" => ACTIVATION_REMOVED_MSG,
		"links_sent" => LINKS_SENT_MSG,
		"serials_sent" => SERIAL_NUMBERS_SENT_MSG,
		"vouchers_sent" => GIFT_VOUCHERS_SENT_MSG,
		"sms_sent" => SEND_SMS_MESSAGE_MSG,
		"email_sent" => SEND_EMAIL_MESSAGE_MSG,
		"update_product" => UPDATE_PRODUCT_MSG,
		"cancel_subscription" => SUBSCRIPTION_CANCELLATION_MSG,
  
		"update_order" => UPDATE_BUTTON,
		"update_order_status" => CHANGE_STATUS_MSG, 
		"update_items_status" => CHANGE_STATUS_MSG, 
		"update_order_shipping" => UPDATE_BUTTON, 
  
		"notification_sent" => NOTIFICATION_SENT_MSG,
		"status_notification_sent" => NOTIFICATION_SENT_MSG,
		"status_sms_sent" => NOTIFICATION_SENT_MSG, 
		"status_merchant_email_sent" => NOTIFICATION_SENT_MSG, 
		"status_merchant_sms_sent" => NOTIFICATION_SENT_MSG, 
		"status_supplier_email_sent" => NOTIFICATION_SENT_MSG, 
		"status_supplier_sms_sent" => NOTIFICATION_SENT_MSG, 
		"status_admin_email_sent" => NOTIFICATION_SENT_MSG, 
		"status_admin_sms_sent" => NOTIFICATION_SENT_MSG, 
		"product_notification_sent" => NOTIFICATION_SENT_MSG,
		"product_sms_sent" => NOTIFICATION_SENT_MSG,
	);

	$sql  = " SELECT oe.event_id, oe.event_date, oe.event_type, oe.event_name, oe.event_description, a.admin_name ";
	$sql .= " FROM (" . $table_prefix . "orders_events oe ";
	$sql .= " LEFT JOIN " . $table_prefix . "admins a ON a.admin_id=oe.admin_id) ";
	$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$sql .= " ORDER BY oe.event_date ASC ";
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$event_id = $db->f("event_id");
			$event_date = $db->f("event_date", DATETIME);
			$t->set_var("event_date", va_date($datetime_show_format, $event_date));

			$event_type = $db->f("event_type");
			$event_name = get_translation($db->f("event_name"));
			$event_description = get_translation($db->f("event_description"));
			$event_type_desc = isset($events_types[$event_type]) ? $events_types[$event_type] : OTHER_MSG;
			$t->set_var("event_id", $event_id);
			$t->set_var("event_type", $event_type_desc);
			$t->set_var("event_name", $event_name);
			$t->set_var("event_description", nl2br($event_description));
			$t->set_var("admin_name", $db->f("admin_name"));

			if ($event_description) {
				$t->parse("event_more", false);
			} else {
				$t->set_var("event_more", "");
			}

			$t->parse("events", true);
		} while ($db->next_record());
		$t->parse("order_log", false);
	} else {
		$t->set_var("order_log", "");
	}

	$t->pparse("main");

?>