<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_sms.php                                      ***
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

	$operation = get_param("operation");
	$order_id = get_param("order_id");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_order_sms.html");
	$t->set_var("site_url", $settings["site_url"]);
	$t->set_var("order_id", $order_id);

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_order_href", "$order_details_site_url . admin_order.php");
	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_order_sms_href", "admin_order_sms.php");


	$message_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$r = new VA_Record("");

	$r->add_where("order_id", INTEGER);
	$r->add_textbox("sms_recipient", TEXT, EMAIL_TO_MSG);
	$r->change_property("sms_recipient", REQUIRED, true);
	$r->add_textbox("sms_originator", TEXT, EMAIL_TO_MSG);
	$r->add_textbox("sms_message", TEXT, MESSAGE_BODY_MSG);
	$r->change_property("sms_message", REQUIRED, true);

	$site_url = $settings["site_url"];

	$message_sent = false;

	$r->get_form_values();

	if(strlen($operation))
	{
		$is_valid = $r->validate();

		if($is_valid) {

			$message_sent = sms_send($r->get_value("sms_recipient"), $r->get_value("sms_message"), $r->get_value("sms_originator"));
			if($message_sent) {
				$event_description = $r->get_value("sms_message");
				// save event for sent SMS message
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
				$oe->set_value("event_type", sms_sent);
				$oe->set_value("event_name", $r->get_value("sms_recipient"));
				$oe->set_value("event_description", $event_description);
				$oe->insert_record();
			} else {
				$r->errors = SMS_GATEWAY_ERROR_MSG;
			}
		}

	} else {

	  // get customer cell phone number
		$sql = "SELECT cell_phone FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			$sms_recipient = $db->f("cell_phone");
			$r->set_value("sms_recipient", $sms_recipient);
		} 

		// get download links
		$order_links = get_order_links($order_id);
		$links = $order_links["text"];

		// get serial numbers 
		$order_serials = get_serial_numbers($order_id);
		$serial_numbers = $order_serials["text"];

		// get gift vouchers
		$order_vouchers = get_gift_vouchers($order_id);
		$gift_vouchers = $order_vouchers["text"];

		$sql  = " SELECT o.*,os.status_name FROM (" . $table_prefix . "orders o ";
		$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		$db->next_record();

		$order_placed_date = $db->f("order_placed_date", DATETIME);
		$order_total = $db->f("order_total");
		$status_name = $db->f("status_name");

		$mail_message  = ADMIN_ORDER_MSG . " #" . $order_id;

		$t->set_block("sms_message", $mail_message);

		$t->parse("sms_message", false);

		$r->set_value("sms_message",  $t->get_var("sms_message"));
	}

	$r->set_parameters();

	if(strlen($message_sent)) {
		$t->parse("message_sent", false);
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>