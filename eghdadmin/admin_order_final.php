<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_final.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");
	check_admin_security("payment_systems");

	$payment_id = get_param("payment_id");
	$setting_type = "order_final_" . $payment_id;
	$sql = " SELECT payment_name FROM " . $table_prefix . "payment_systems WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$payment_name = get_translation($db->f("payment_name"), $language_code);
	} else {
		header("Location: admin_payment_systems.php");
		exit;
	}

	$message_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_order_final.html");
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_order_final_href", "admin_order_final.php");
	$t->set_var("admin_order_help_href", "admin_order_help.php");
	$t->set_var("admin_email_help_href", "admin_email_help.php");
	$t->set_var("admin_payment_system_href",  "admin_payment_system.php");
	$t->set_var("admin_payment_systems_href", "admin_payment_systems.php");

	$t->set_var("payment_id",   $payment_id);
	$t->set_var("payment_name", $payment_name);

	$sql = "SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE is_active=1 ORDER BY status_order, status_id";
	$order_statuses = get_db_values($sql, array(array("", "")));

	$r = new VA_Record($table_prefix . "global_settings");
	$r->add_checkbox("is_validation", TEXT);
	$r->add_textbox("validation_php_lib", TEXT, VALIDATION_SCRIPT_MSG);
	$r->add_select("success_status_id", TEXT, $order_statuses, SUCCESS_STATUS_MSG);
	$r->add_select("pending_status_id", TEXT, $order_statuses, PENDING_STATUS_MSG);
	$r->add_select("failure_status_id", TEXT, $order_statuses, FAILURE_STATUS_MSG);
	$is_validation = get_param("is_validation");
	if ($is_validation) {
		$r->change_property("validation_php_lib", REQUIRED, true);
		$r->change_property("success_status_id", REQUIRED, true);
		$r->change_property("failure_status_id", REQUIRED, true);
	}

	$r->add_textbox("success_message", TEXT);
	$r->add_textbox("pending_message", TEXT);
	$r->add_textbox("failure_message", TEXT);

	// set up html form parameters
	$r->add_checkbox("admin_notification", INTEGER);
	// PGP enable
	$r->add_checkbox("admin_notification_pgp", INTEGER);	
	$r->add_checkbox("admin_pending_notify", INTEGER);
	$r->add_checkbox("admin_failure_notify", INTEGER);
	$r->add_textbox("admin_email", TEXT);
	$r->add_textbox("admin_mail_from", TEXT);
	$r->add_textbox("cc_emails", TEXT);
	$r->add_textbox("admin_mail_bcc", TEXT);
	$r->add_textbox("admin_mail_reply_to", TEXT);
	$r->add_textbox("admin_mail_return_path", TEXT);
	$r->add_checkbox("admin_mail_pdf_invoice", INTEGER);
	$r->add_textbox("admin_subject", TEXT);
	$r->add_radio("admin_message_type", TEXT, $message_types);
	$r->add_textbox("admin_message", TEXT);

	$r->add_checkbox("product_individual_notification", INTEGER);	
	$r->add_checkbox("user_notification", INTEGER);
	$r->add_checkbox("user_pending_notify", INTEGER);
	$r->add_checkbox("user_failure_notify", INTEGER);
	$r->add_textbox("user_mail_from", TEXT);
	$r->add_textbox("user_mail_cc", TEXT);
	$r->add_textbox("user_mail_bcc", TEXT);
	$r->add_textbox("user_mail_reply_to", TEXT);
	$r->add_textbox("user_mail_return_path", TEXT);
	$r->add_checkbox("user_mail_pdf_invoice", INTEGER);
	$r->add_textbox("user_subject", TEXT);
	$r->add_radio("user_message_type", TEXT, $message_types);
	$r->add_textbox("user_message", TEXT);

	// sms notification settings
	$r->add_checkbox("admin_sms_success", INTEGER);
	$r->add_checkbox("admin_sms_pending", INTEGER);
	$r->add_checkbox("admin_sms_failure", INTEGER);
	$r->add_textbox("admin_sms_recipient", TEXT, ADMIN_SMS_RECIPIENT_MSG);
	$r->add_textbox("admin_sms_originator", TEXT, ADMIN_SMS_ORIGINATOR_MSG);
	$r->add_textbox("admin_sms_message", TEXT, ADMIN_SMS_MESSAGE_MSG);

	$r->add_checkbox("user_sms_success", INTEGER);
	$r->add_checkbox("user_sms_pending", INTEGER);
	$r->add_checkbox("user_sms_failure", INTEGER);
	$r->add_textbox("user_sms_recipient", TEXT, USER_SMS_RECIPIENT_MSG);
	$r->add_textbox("user_sms_originator", TEXT, USER_SMS_ORIGINATOR_MSG);
	$r->add_textbox("user_sms_message", TEXT, USER_SMS_MESSAGE_MSG);

	$r->get_form_values();

	$param_site_id = get_session("session_site_id");
	$operation = get_param("operation");
	$return_page = get_param("rp");
	if (!strlen($return_page)) $return_page = "admin_payment_systems.php";

	if (strlen($operation))
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		if ($r->get_value("admin_sms_success") || $r->get_value("admin_sms_pending") || $r->get_value("admin_sms_failure")) {
			$r->change_property("admin_sms_recipient", REQUIRED, true);
			$r->change_property("admin_sms_message", REQUIRED, true);
		}
		if ($r->get_value("user_sms_success") || $r->get_value("user_sms_pending") || $r->get_value("user_sms_failure")) {
			$r->change_property("user_sms_message", REQUIRED, true);
		}
		
		$is_valid = $r->validate();

		if (!strlen($r->errors))
		{
			$sql  = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
			$sql .= " AND site_id=" . $db->tosql($param_site_id,INTEGER);
			$db->query($sql);
			foreach ($r->parameters as $key => $value)
			{
				$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
				$sql .= $db->tosql($setting_type, TEXT) . ", '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
				$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
	}
	else // get order_info settings
	{
		foreach ($r->parameters as $key => $value)
		{
			$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT) . " AND setting_name='" . $key . "'";
			$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
			$sql .= " ORDER BY site_id DESC ";
			$r->set_value($key, get_db_value($sql));
		}
	}

	$r->set_parameters();
	$t->set_var("rp", htmlspecialchars($return_page));
	
	// multisites
	if ($sitelist) {

		$sites_all = 0;
		$sql = " SELECT sites_all FROM " . $table_prefix . "payment_systems WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$sites_all = $db->f("sites_all");
		}
		
		$sql  = " SELECT s.site_id, s.site_name FROM " . $table_prefix . "sites AS s ";
		if (!$sites_all) {
			$sql .= " LEFT JOIN " . $table_prefix . "payment_systems_sites AS p ON s.site_id=p.site_id ";
			$sql .= " WHERE p.payment_id=" . $db->tosql($payment_id, INTEGER);	
		}
		$sql .= " ORDER BY s.site_id ";
		$sites  = get_db_values($sql, "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}

	$t->pparse("main");

?>