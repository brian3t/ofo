<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_status.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");
	check_admin_security("order_statuses");

	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	$operation = get_param("operation");
	if ($operation) { $tab = "general"; }

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_order_status.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");  
	$t->set_var("admin_lookup_tables_href",  "admin_lookup_tables.php");
	$t->set_var("admin_order_statuses_href", "admin_order_statuses.php");
	$t->set_var("admin_order_status_href",   "admin_order_status.php");
	$t->set_var("admin_email_help_href",     "admin_email_help.php");
	$t->set_var("admin_order_help_href",     "admin_order_help.php");
	$t->set_var("admin_download_info_href",  "admin_download_info.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", STATUS_MSG, CONFIRM_DELETE_MSG));

	$paid_statuses = 
		array( 
			array(1, PAID_MSG), array(0, NOT_PAID_MSG)
		);

	$activations_values = 
		array( 
			array(1, ACTIVATE_MSG), array(0, DISABLE_MSG)
		);

	$commission_values = 
		array( 
			array(1, COMMISSION_REWARD_ADD_MSG), array(-1, COMMISSION_REWARD_SUBTRACT_MSG),
		);

	$stock_level_values = 
		array( 
			array(1, STOCK_LEVEL_RESERVE_MSG), array(-1, STOCK_LEVEL_RELEASE_MSG),
		);

	$points_action_values = 
		array( 
			array(-1, POINTS_SUBTRACT_MSG), array(1, POINTS_RETURN_MSG),
		);

	$credit_action_values = 
		array( 
			array(-1, SUBSTRACT_CREDIT_AMOUNT_MSG), array(1, RETURN_CREDIT_BALANCE_MSG),
		);

	$mail_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$status_types = array(
		array("", ""), 
		array("NEW", "NEW"), 
		array("PAYMENT_INFO", "PAYMENT_INFO"), 
		array("CONFIRMED", "CONFIRMED"), 
		array("PAID", "PAID"), 
		array("SHIPPED", "SHIPPED"), 
		array("PENDING", "PENDING"), 
		array("DECLINED", "DECLINED"), 
		array("VALIDATED", "VALIDATED"), 
		array("FAILED", "FAILED"), 
		array("DISPATCHED", "DISPATCHED"), 
		array("REFUNDED", "REFUNDED"), 
		array("CAPTURED", "CAPTURED"), 
		array("VOIDED", "VOIDED"), 
		array("AUTHORIZED", "AUTHORIZED"), 
		array("CANCELLED", "CANCELLED"), 
		array("OTHER", "OTHER"), 
	);

	$r = new VA_Record($table_prefix . "order_statuses");
	$r->return_page = "admin_order_statuses.php";

	$r->add_where("status_id", INTEGER);
	$r->add_checkbox("is_active", INTEGER);
	$r->add_textbox("status_order", INTEGER, STATUS_ORDER_MSG);
	$r->parameters["status_order"][REQUIRED] = true;
	$r->add_textbox("status_name", TEXT, STATUS_NAME_MSG);
	$r->parameters["status_name"][REQUIRED] = true;
	$r->add_select("status_type", TEXT, $status_types, STATUS_TYPE_MSG);
	$r->change_property("status_type", REQUIRED, true);
	$r->add_checkbox("allow_user_cancel", INTEGER);
	$r->add_checkbox("is_user_cancel", INTEGER);
	$r->add_checkbox("user_invoice_activation", INTEGER);
	$r->add_checkbox("is_dispatch", INTEGER);
	$r->add_checkbox("is_list", INTEGER);
	$r->change_property("is_list", DEFAULT_VALUE, 1);
	$r->add_checkbox("show_for_user", INTEGER);
	$r->add_checkbox("item_notify", INTEGER);
	$r->add_radio("paid_status", INTEGER, $paid_statuses, PAID_STATUS_MSG);
	$r->add_radio("download_activation", INTEGER, $activations_values);
	$r->add_checkbox("download_notify", INTEGER);
	$r->add_radio("commission_action", INTEGER, $commission_values);
	$r->add_radio("stock_level_action", INTEGER, $stock_level_values);
	$r->add_radio("points_action", INTEGER, $points_action_values);
	$r->add_radio("credit_action", INTEGER, $credit_action_values);

	// customer notification fields
	$r->add_checkbox("mail_notify", INTEGER);
	$r->add_textbox("mail_from", TEXT);
	$r->add_textbox("mail_cc", TEXT);
	$r->add_textbox("mail_bcc", TEXT);
	$r->add_textbox("mail_reply_to", TEXT);
	$r->add_textbox("mail_return_path", TEXT);
	$r->add_textbox("mail_subject", TEXT);
	$r->add_radio("mail_type", INTEGER, $mail_types);
	$r->parameters["mail_type"][DEFAULT_VALUE] = 0;
	$r->add_textbox("mail_body", TEXT);

	$r->add_checkbox("sms_notify", INTEGER);
	$r->add_textbox("sms_recipient", TEXT, USER_SMS_RECIPIENT_MSG);
	$r->add_textbox("sms_originator",TEXT, USER_SMS_ORIGINATOR_MSG);
	$r->add_textbox("sms_message",   TEXT, USER_SMS_MESSAGE_MSG);

	// merchant notification fields
	$r->add_checkbox("merchant_notify", INTEGER);
	$r->add_textbox("merchant_to", TEXT);
	$r->add_textbox("merchant_from", TEXT);
	$r->add_textbox("merchant_cc", TEXT);
	$r->add_textbox("merchant_bcc", TEXT);
	$r->add_textbox("merchant_reply_to", TEXT);
	$r->add_textbox("merchant_return_path", TEXT);
	$r->add_textbox("merchant_subject", TEXT);
	$r->add_radio("merchant_mail_type", INTEGER, $mail_types);
	$r->parameters["merchant_mail_type"][DEFAULT_VALUE] = 0;
	$r->add_textbox("merchant_body", TEXT);

	$r->add_checkbox("merchant_sms_notify", INTEGER);
	$r->add_textbox("merchant_sms_recipient", TEXT, MERCHANT_SMS_RECIPIENT_MSG);
	$r->add_textbox("merchant_sms_originator",TEXT, MERCHANT_SMS_ORIGINATOR_MSG);
	$r->add_textbox("merchant_sms_message",   TEXT, MERCHANT_SMS_MESSAGE_MSG);


	// supplier notification fields
	$r->add_checkbox("supplier_notify", INTEGER);
	$r->add_textbox("supplier_to", TEXT);
	$r->add_textbox("supplier_from", TEXT);
	$r->add_textbox("supplier_cc", TEXT);
	$r->add_textbox("supplier_bcc", TEXT);
	$r->add_textbox("supplier_reply_to", TEXT);
	$r->add_textbox("supplier_return_path", TEXT);
	$r->add_textbox("supplier_subject", TEXT);
	$r->add_radio("supplier_mail_type", INTEGER, $mail_types);
	$r->parameters["supplier_mail_type"][DEFAULT_VALUE] = 0;
	$r->add_textbox("supplier_body", TEXT);

	$r->add_checkbox("supplier_sms_notify", INTEGER);
	$r->add_textbox("supplier_sms_recipient", TEXT, SMS_RECIPIENT_MSG);
	$r->add_textbox("supplier_sms_originator",TEXT, SMS_ORIGINATOR_MSG);
	$r->add_textbox("supplier_sms_message",   TEXT, SMS_MESSAGE_MSG);

	// admin notification fields
	$r->add_checkbox("admin_notify", INTEGER);
	$r->add_textbox("admin_to", TEXT);
	$r->add_textbox("admin_from", TEXT);
	$r->add_textbox("admin_cc", TEXT);
	$r->add_textbox("admin_bcc", TEXT);
	$r->add_textbox("admin_reply_to", TEXT);
	$r->add_textbox("admin_return_path", TEXT);
	$r->add_textbox("admin_subject", TEXT);
	$r->add_radio("admin_mail_type", INTEGER, $mail_types);
	$r->parameters["admin_mail_type"][DEFAULT_VALUE] = 0;
	$r->add_textbox("admin_body", TEXT);

	$r->add_checkbox("admin_sms_notify", INTEGER);
	$r->add_textbox("admin_sms_recipient", TEXT, ADMIN_SMS_RECIPIENT_MSG);
	$r->add_textbox("admin_sms_originator",TEXT, ADMIN_SMS_ORIGINATOR_MSG);
	$r->add_textbox("admin_sms_message",   TEXT, ADMIN_SMS_MESSAGE_MSG);

	$r->add_textbox("final_message",   TEXT);
	$r->set_event(BEFORE_VALIDATE, "check_status_options");
	$r->set_event(BEFORE_DEFAULT, "set_status_order");

	$r->process();

	$tabs = array(
		"general" => ADMIN_GENERAL_MSG, "user_notify" => USER_NOTIFICATION_MSG, 
		"merchant_notify" => MERCHANT_NOTIFY_MSG, 
		"supplier_notify" => SUPPLIER_NOTIFY_MSG, 
		"admin_notify" => ADMINISTRATOR_NOTIFICATION_MSG, "final_checkout" => FINAL_CHECKOUT_SETTINGS_MSG
	);
	foreach ($tabs as $tab_name => $tab_title) {
		$t->set_var("tab_id", "tab_" . $tab_name);
		$t->set_var("tab_name", $tab_name);
		$t->set_var("tab_title", $tab_title);
		if ($tab_name == $tab) {
			$t->set_var("tab_class", "adminTabActive");
			$t->set_var($tab_name . "_style", "display: block;");
		} else {
			$t->set_var("tab_class", "adminTab");
			$t->set_var($tab_name . "_style", "display: none;");
		}
		$t->parse("tabs", $tab_title);
	}
	$t->set_var("tab", $tab);

	$t->pparse("main");

	function check_status_options()
	{
		global $r;
		if ($r->get_value("sms_notify")) {
			$r->change_property("sms_message", REQUIRED, true);
		} 
		if ($r->get_value("merchant_sms_notify")) {
			$r->change_property("merchant_sms_message", REQUIRED, true);
		} 
		if ($r->get_value("admin_sms_notify")) {
			$r->change_property("admin_sms_message", REQUIRED, true);
		} 
	}

	function set_status_order()  
	{
		global $db, $table_prefix, $r;
		$sql = "SELECT MAX(status_order) FROM " . $table_prefix . "order_statuses ";
		$db->query($sql);
		if ($db->next_record()) {
			$status_order = $db->f(0) + 1;
			$r->change_property("status_order", DEFAULT_VALUE, $status_order);
		}	
	}

?>