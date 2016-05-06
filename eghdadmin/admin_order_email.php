<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_email.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/order_items.php");
	include_once($root_folder_path . "includes/order_links.php");
	include_once($root_folder_path . "includes/shopping_cart.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");

	$eol = get_eol();

	$operation = get_param("operation");
	$order_id = get_param("order_id");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_order_email.html");
	$t->set_var("site_url", $settings["site_url"]);
	$t->set_var("order_id", $order_id);

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_order_href", $order_details_site_url . "admin_order.php");
	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_order_email_href", "admin_order_email.php");


	$message_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$r = new VA_Record("");

	$r->add_where("order_id", INTEGER);
	$r->add_textbox("message_from", TEXT, EMAIL_FROM_MSG);
	$r->change_property("message_from", REQUIRED, true);
	$r->add_textbox("message_to", TEXT, EMAIL_TO_MSG);
	$r->change_property("message_to", REQUIRED, true);
	$r->add_textbox("message_cc", TEXT);
	$r->add_textbox("message_bcc", TEXT);
	$r->add_textbox("message_reply_to", TEXT);
	$r->add_textbox("message_return_path", TEXT);
	$r->add_textbox("message_subject", TEXT);
	$r->add_radio("message_type", TEXT, $message_types);
	$r->add_textbox("message_body", TEXT, MESSAGE_BODY_MSG);
	$r->change_property("message_body", REQUIRED, true);
	
	// PGP disable
	$r->add_checkbox("message_pgp", INTEGER);
	$r->change_property("pgp_enable", SHOW, false); 

	$site_url = $settings["site_url"];
	$message_sent = false;

	$r->get_form_values();

	if (strlen($operation))
	{
		$is_valid = $r->validate();

		if ($is_valid) {
			$email_headers = array();
			$email_headers["from"] = $r->get_value("message_from");
			$email_headers["cc"] = $r->get_value("message_cc");
			$email_headers["bcc"] = $r->get_value("message_bcc");
			$email_headers["reply_to"] = $r->get_value("message_reply_to");
			$email_headers["return_path"] = $r->get_value("message_return_path");
			$email_headers["mail_type"] = $r->get_value("message_type");
			
			$message_body = preg_replace("/\r\n|\r|\n/", $eol, $r->get_value("message_body"));			
			
			// PGP enable
			if ( $r->get_value("message_pgp") && $message_body) {	
				include_once ($root_folder_path . "includes/pgp_functions.php");
				if (pgp_test()) {
					$message_body = pgp_encrypt($message_body, $r->get_value("message_to"));
					if ($message_body){
						$message_sent = va_mail($r->get_value("message_to"), $r->get_value("message_subject"), $message_body, $email_headers);
					}
				}
			} else {
				$message_sent = va_mail($r->get_value("message_to"), $r->get_value("message_subject"), $message_body, $email_headers);		
			}
			
			if ($message_sent) {
				// save event for sent message
				$oe = new VA_Record($table_prefix . "orders_events");
				$oe->add_textbox("order_id", INTEGER);
				$oe->add_textbox("status_id", INTEGER);
				$oe->add_textbox("admin_id", INTEGER);
				$oe->add_textbox("event_date", DATETIME);
				$oe->add_textbox("event_type", TEXT);
				$oe->add_textbox("event_name", TEXT);
				$oe->add_textbox("event_description", TEXT);
				$oe->set_value("order_id", $order_id);
				$oe->set_value("status_id", 0);
				$oe->set_value("admin_id", get_session("session_admin_id"));
				$oe->set_value("event_date", va_time());
				$oe->set_value("event_type", "email_sent");
				$oe->set_value("event_name", $r->get_value("message_subject"));
				$oe->set_value("event_description", $message_body);
				$oe->insert_record();
			} else {
				$r->errors = MAIL_SERVER_ERROR_MSG;
			}
		}
	} 
	else 
	{
		// get predefined data
		$order_info = array();
		$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings";
		$sql .= " WHERE setting_type='order_info'";
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id,INTEGER) . ") ";
		$sql .= " ORDER BY site_id ASC ";
		$db->query($sql);
		while ($db->next_record()) {
			$order_info[$db->f("setting_name")] = $db->f("setting_value");
		}

		$sql  = " SELECT email FROM " . $table_prefix . "admins ";
		$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$admin_email = $db->f("email");
		}

		// prepare email fields
		$mail_from = get_setting_value($order_info, "predefined_mail_from", $admin_email);
		$mail_cc = get_setting_value($order_info, "predefined_mail_cc");
		$mail_bcc = get_setting_value($order_info, "predefined_mail_bcc");
		$mail_reply_to = get_setting_value($order_info, "predefined_mail_reply_to");
		$mail_return_path = get_setting_value($order_info, "predefined_mail_return_path");
		$mail_type = get_setting_value($order_info, "predefined_mail_type", 0);
		$mail_subject	= get_setting_value($order_info, "predefined_mail_subject", ADMIN_ORDER_MSG . " #" . $order_id);

		$r->set_value("message_from", $mail_from);
		$r->set_value("message_cc", $mail_cc);
		$r->set_value("message_bcc", $mail_bcc);
		$r->set_value("message_reply_to", $mail_reply_to);
		$r->set_value("message_return_path", $mail_return_path);
		$r->set_value("message_type", $mail_type);

	  // get customer email
		$sql = "SELECT email, delivery_email FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$message_to = strlen($db->f("email")) ? $db->f("email") : $db->f("delivery_email");
			$r->set_value("message_to", $message_to);
		} 

		// get download links
		$order_links = get_order_links($order_id);
		$links = ($mail_type) ? $order_links["html"] : $order_links["text"];

		// get serial numbers 
		$order_serials = get_serial_numbers($order_id);
		$serial_numbers = ($mail_type) ? $order_serials["html"] : $order_serials["text"];

		// get gift vouchers
		$order_vouchers = get_gift_vouchers($order_id);
		$gift_vouchers = ($mail_type) ? $order_vouchers["html"] : $order_vouchers["text"];

		$items_text = show_order_items($order_id, false);
		$t->set_var("basket", $items_text);
		$t->set_var("items", $items_text);
		$t->set_var("products", $items_text);
		$sql  = " SELECT o.*,os.status_name FROM (" . $table_prefix . "orders o ";
		$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		$db->next_record();
		$state_id = $db->f("state_id");
		$country_id = $db->f("country_id");
		$delivery_state_id = $db->f("delivery_state_id");
		$delivery_country_id = $db->f("delivery_country_id");
		$cc_type = $db->f("cc_type");
		$order_placed_date = $db->f("order_placed_date", DATETIME);
		$goods_total = $db->f("goods_total");
		$total_discount = $db->f("total_discount");
		$shipping_cost = $db->f("shipping_cost");
		$tax_name = $db->f("tax_name");
		$tax_percent = $db->f("tax_percent");
		$order_total = round($goods_total, 2) - round($total_discount, 2) + round($shipping_cost, 2);
		if (strlen($tax_name) || $tax_percent > 0) {
			$tax_cost = ($order_total / 100) * $tax_percent;
			$tax_cost = round($tax_cost, 2);
			$order_total += $tax_cost;
			$t->set_var("tax_cost", currency_format($tax_cost));
		}

		$t->set_vars($db->Record);
		$t->set_var("goods_total", currency_format($goods_total));
		$t->set_var("total_discount", currency_format($total_discount));
		$t->set_var("shipping_cost", currency_format($shipping_cost));
		$t->set_var("order_total", currency_format($order_total));
		$t->set_var("tax_percent", number_format($db->f("tax_percent"), 2) . "%");

		$date_formated = va_date($datetime_show_format, $order_placed_date);
		$cc_start = va_date(array("MM", " / ", "YYYY"), $db->f("cc_start_date", DATETIME));
		$cc_expiry = va_date(array("MM", " / ", "YYYY"), $db->f("cc_expiry_date", DATETIME));

		$state = get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($state_id, INTEGER));
		$delivery_state = get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($delivery_state_id, INTEGER));
		$country = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($country_id, INTEGER));
		$delivery_country = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($delivery_country_id, INTEGER));
		$cc_type = get_db_value("SELECT credit_card_name FROM " . $table_prefix . "credit_cards WHERE credit_card_id=" . $db->tosql($cc_type, INTEGER));

		$t->set_var("order_placed_date", $date_formated);
		$t->set_var("order_placed", $date_formated);
		$t->set_var("links", $links);
		$t->set_var("serials", $serial_numbers);
		$t->set_var("serial_numbers", $serial_numbers);
		$t->set_var("vouchers", $gift_vouchers);
		$t->set_var("gift_vouchers", $gift_vouchers);

		$t->set_var("state", $state);
		$t->set_var("country", $country);
		$t->set_var("delivery_state", $delivery_state);
		$t->set_var("delivery_country", $delivery_country);
		$t->set_var("cc_type", $cc_type);
		$t->set_var("cc_start_date", $cc_start);
		$t->set_var("cc_expiry_date", $cc_expiry);

		if (isset($order_info["predefined_mail_body"]) && $order_info["predefined_mail_body"]) {
			$mail_message = $order_info["predefined_mail_body"];
		} else {
			$mail_message  = ADMIN_ORDER_MSG . " #" . $order_id . $eol;
			$mail_message .= STATUS_NAME_FIELD_MSG . $eol;
			$mail_message .= FULL_NAME_FIELD_MSG . $eol . $eol;
			$mail_message .= $items_text;

		}

		$t->set_block("message_subject", $mail_subject);
		$t->set_block("message_body", $mail_message);

		$t->parse("message_subject", false);
		$t->parse("message_body", false);

		$r->set_value("message_subject", $t->get_var("message_subject"));
		$r->set_value("message_body",  $t->get_var("message_body"));
	}

	$r->set_parameters();

	if (strlen($message_sent)) {
		$t->parse("message_sent", false);
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>