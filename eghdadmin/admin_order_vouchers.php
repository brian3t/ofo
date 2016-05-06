<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_vouchers.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/sorter.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/order_items.php");
	include_once ($root_folder_path . "includes/order_links.php");
	include_once ("../messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");
	check_admin_security("order_vouchers");

	$eol = get_eol();

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_order_vouchers.html");
	$t->set_var("site_url", $settings["site_url"]);

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

	$site_url = $settings["site_url"];

	$operation = get_param("operation");
	$order_id = get_param("order_id");

	$message_sent = false;

	$r->get_form_values();

	if(strlen($operation))
	{
		$is_valid = $r->validate();

		if($is_valid) {

			$email_headers = array();
			$email_headers["from"] = $r->get_value("message_from");
			$email_headers["cc"] = $r->get_value("message_cc");
			$email_headers["bcc"] = $r->get_value("message_bcc");
			$email_headers["reply_to"] = $r->get_value("message_reply_to");
			$email_headers["return_path"] = $r->get_value("message_return_path");
			$email_headers["mail_type"] = $r->get_value("message_type");
			$message_body = preg_replace("/\r\n|\r|\n/", $eol, $r->get_value("message_body"));
			$message_sent = va_mail($r->get_value("message_to"), $r->get_value("message_subject"), $message_body, $email_headers);
			if(!$message_sent) {
				$r->errors = MAIL_SERVER_ERROR_MSG;
			} else {
				$oe = new VA_Record($table_prefix . "orders_events");
				$oe->add_textbox("order_id", INTEGER);
				$oe->add_textbox("status_id", INTEGER);
				$oe->add_textbox("admin_id", INTEGER);
				$oe->add_textbox("order_items", TEXT);
				$oe->add_textbox("event_date", DATETIME);
				$oe->add_textbox("event_type", TEXT);
				$oe->add_textbox("event_name", TEXT);
				$oe->add_textbox("event_description", TEXT);
				$oe->set_value("order_id", $order_id);
				$oe->set_value("status_id", 0);
				$oe->set_value("admin_id", get_session("session_admin_id"));
				$oe->set_value("event_date", va_time());
				$oe->set_value("event_type", "vouchers_sent");
				$oe->set_value("event_name", $r->get_value("message_subject"));
				$oe->set_value("event_description", $message_body);
				$oe->insert_record();
			}
		}

	} else {

		$download_info = array();
		$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='download_info'";
		if ($multisites_version) {
			$sql2  = " SELECT site_id FROM " . $table_prefix . "orders ";
			$sql2 .= " WHERE order_id=" . $db->tosql($order_id,INTEGER,true,false); 
			$order_site_id = get_db_value($sql2);
			$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($order_site_id,INTEGER) . ") ";
			$sql .= " ORDER BY site_id ASC ";
		}
		$db->query($sql);
		while($db->next_record()) {
			$download_info[$db->f("setting_name")] = $db->f("setting_value");
		}


		// prepare email fields
		$mail_from = get_setting_value($download_info, "vouchers_from", $settings["admin_email"]);
		$mail_cc = get_setting_value($download_info, "vouchers_cc");
		$mail_bcc = get_setting_value($download_info, "vouchers_bcc");
		$mail_reply_to = get_setting_value($download_info, "vouchers_reply_to");
		$mail_return_path = get_setting_value($download_info, "vouchers_return_path");
		$mail_type = get_setting_value($download_info, "vouchers_message_type");

		$r->set_value("message_from", $mail_from);
		$r->set_value("message_cc", $mail_cc);
		$r->set_value("message_bcc", $mail_bcc);
		$r->set_value("message_reply_to", $mail_reply_to);
		$r->set_value("message_return_path", $mail_return_path);
		$r->set_value("message_type", $mail_type);

	  // get customer email
		$sql = "SELECT delivery_email, email FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			$message_to = strlen($db->f("delivery_email")) ? $db->f("delivery_email") : $db->f("email");
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

		if(isset($download_info["vouchers_subject"]) && $download_info["vouchers_subject"]) {
			$vouchers_subject = $download_info["vouchers_subject"];
		} else {
			$vouchers_subject = GIFT_VOUCHERS_FOR_ORDERS_MSG . $order_id;
		}

		if(isset($download_info["vouchers_message"]) && $download_info["vouchers_message"]) {
			$vouchers_message = $download_info["vouchers_message"];
		} else {
			$vouchers_message = $gift_vouchers;
		}

		$t->set_block("message_subject", $vouchers_subject);
		$t->set_block("message_body",    $vouchers_message);

		$items_text = show_order_items($order_id, false);
		$t->set_var("basket", $items_text);
		$t->set_var("items", $items_text);
		$t->set_var("products", $items_text);
		$sql = "SELECT * FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER);
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
		if(strlen($tax_name) || $tax_percent > 0) {
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

		$t->parse("message_subject", false);
		$t->parse("message_body", false);

		$r->set_value("message_subject", $t->get_var("message_subject"));
		$r->set_value("message_body",  $t->get_var("message_body"));
	}

	$r->set_parameters();

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_order_vouchers.php");
	$s->set_sorter(ID_MSG, "sorter_coupon_id", "1", "coupon_id");
	$s->set_sorter(TITLE_MSG, "sorter_coupon_title", "2", "coupon_title");
	$s->set_sorter(CODE_MSG, "sorter_coupon_code", "3", "coupon_code");
	$s->set_sorter(ADMIN_ACTIVATED_MSG, "sorter_is_active", "4", "is_active");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_order_href", $order_details_site_url . "admin_order.php");
	$t->set_var("admin_coupon_href", "admin_coupon.php");
	$t->set_var("admin_order_vouchers_href", "admin_order_vouchers.php");
	$t->set_var("admin_orders_href", "admin_orders.php");

	$sql  = " SELECT c.coupon_id,c.coupon_title,c.is_active,c.coupon_code ";
	$sql .= " FROM " . $table_prefix . "coupons c ";
	$sql .= " WHERE c.order_id=" . $db->tosql($order_id, INTEGER);
	//$db->RecordsPerPage = $records_per_page;
	//$db->PageNumber = $page_number;
	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
			$coupon_id = $db->f("coupon_id");
			$coupon_code = $db->f("coupon_code");
			$is_active = $db->f("is_active") ? "Yes" : "No";
			$coupon_title= get_translation($db->f("coupon_title"));

			$t->set_var("coupon_id", $coupon_id);
			$t->set_var("coupon_code", $coupon_code);
			$t->set_var("coupon_title", htmlspecialchars($coupon_title));
			$t->set_var("is_active", $is_active);
			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->parse("no_records", false);
	}

	$sql  = " SELECT COUNT(*) FROM (" . $table_prefix . "orders_items oi ";
	$sql .= " LEFT JOIN " . $table_prefix . "item_types it ON oi.item_type_id=it.item_type_id) ";
	$sql .= " WHERE oi.order_id=" . $db->tosql($order_id, INTEGER);
	$sql .= " AND it.is_gift_voucher=1 ";
	$vouchers_number = get_db_value($sql);
	if($vouchers_number) {
		$t->parse("add_voucher_link", false);
	}


	if(strlen($message_sent)) {
		$t->parse("message_sent", false);
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>