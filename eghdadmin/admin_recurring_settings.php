<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_recurring_settings.php                             ***
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
	$setting_type = "recurring_" . $payment_id;
	$sql = " SELECT payment_name FROM " . $table_prefix . "payment_systems WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$payment_name = get_translation($db->f("payment_name"), $language_code);
	} else {
		header ("Location: admin_payment_systems.php");
		exit;
	}

	$message_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_recurring_settings.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_recurring_settings_href", "admin_recurring_settings.php");
	$t->set_var("admin_order_help_href", "admin_order_help.php");
	$t->set_var("admin_email_help_href", "admin_email_help.php");
	$t->set_var("admin_payment_system_href",  "admin_payment_system.php");
	$t->set_var("admin_payment_systems_href", "admin_payment_systems.php");

	$t->set_var("payment_id",   $payment_id);
	$t->set_var("payment_name", $payment_name);

	$sql = "SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE is_active=1 ORDER BY status_order, status_id";
	$order_statuses = get_db_values($sql, array(array("", "")));

	$r = new VA_Record($table_prefix . "global_settings");
	$r->add_select("new_status_id", TEXT, $order_statuses, SUCCESS_STATUS_MSG);
	$r->add_textbox("recurring_attempts", INTEGER);
	$r->add_textbox("recurring_next_attempt", INTEGER);
	// preserve options
	$r->add_checkbox("preserve_item_options", INTEGER);
	$r->add_checkbox("preserve_cart_options", INTEGER);
	$r->add_checkbox("preserve_shipping", INTEGER);

	// on creation parameters
	$r->add_checkbox("admin_notify_new", INTEGER);
	$r->add_textbox("admin_mail_to_new", TEXT);
	$r->add_textbox("admin_mail_from_new", TEXT);
	$r->add_textbox("admin_mail_cc_new", TEXT);
	$r->add_textbox("admin_mail_bcc_new", TEXT);
	$r->add_textbox("admin_mail_reply_to_new", TEXT);
	$r->add_textbox("admin_mail_return_path_new", TEXT);
	$r->add_textbox("admin_mail_subject_new", TEXT);
	$r->add_radio("admin_mail_type_new", TEXT, $message_types);
	$r->add_textbox("admin_mail_body_new", TEXT);

	$r->add_checkbox("user_notify_new", INTEGER);
	$r->add_textbox("user_mail_from_new", TEXT);
	$r->add_textbox("user_mail_cc_new", TEXT);
	$r->add_textbox("user_mail_bcc_new", TEXT);
	$r->add_textbox("user_mail_reply_to_new", TEXT);
	$r->add_textbox("user_mail_return_path_new", TEXT);
	$r->add_textbox("user_mail_subject_new", TEXT);
	$r->add_radio("user_mail_type_new", TEXT, $message_types);
	$r->add_textbox("user_mail_body_new", TEXT);

	$r->add_checkbox("admin_sms_new", INTEGER);
	$r->add_textbox("admin_sms_recipient_new", TEXT, ADMIN_SMS_RECIPIENT_MSG . "(".ONCREATION_MSG .")");
	$r->add_textbox("admin_sms_originator_new", TEXT, ADMIN_SMS_ORIGINATOR_MSG . "(".ONCREATION_MSG .")");
	$r->add_textbox("admin_sms_message_new", TEXT, ADMIN_SMS_MESSAGE_MSG . "(".ONCREATION_MSG .")");

	$r->add_checkbox("user_sms_new", INTEGER);
	$r->add_textbox("user_sms_recipient_new", TEXT, USER_SMS_RECIPIENT_MSG . "(".ONCREATION_MSG .")");
	$r->add_textbox("user_sms_originator_new", TEXT, USER_SMS_ORIGINATOR_MSG . "(".ONCREATION_MSG .")");
	$r->add_textbox("user_sms_message_new", TEXT, USER_SMS_MESSAGE_MSG . "(".ONCREATION_MSG .")");

	// on success parameters
	$r->add_checkbox("admin_notify_success", INTEGER);
	$r->add_textbox("admin_mail_to_success", TEXT);
	$r->add_textbox("admin_mail_from_success", TEXT);
	$r->add_textbox("admin_mail_cc_success", TEXT);
	$r->add_textbox("admin_mail_bcc_success", TEXT);
	$r->add_textbox("admin_mail_reply_to_success", TEXT);
	$r->add_textbox("admin_mail_return_path_success", TEXT);
	$r->add_textbox("admin_mail_subject_success", TEXT);
	$r->add_radio("admin_mail_type_success", TEXT, $message_types);
	$r->add_textbox("admin_mail_body_success", TEXT);

	$r->add_checkbox("user_notify_success", INTEGER);
	$r->add_textbox("user_mail_from_success", TEXT);
	$r->add_textbox("user_mail_cc_success", TEXT);
	$r->add_textbox("user_mail_bcc_success", TEXT);
	$r->add_textbox("user_mail_reply_to_success", TEXT);
	$r->add_textbox("user_mail_return_path_success", TEXT);
	$r->add_textbox("user_mail_subject_success", TEXT);
	$r->add_radio("user_mail_type_success", TEXT, $message_types);
	$r->add_textbox("user_mail_body_success", TEXT);

	$r->add_checkbox("admin_sms_success", INTEGER);
	$r->add_textbox("admin_sms_recipient_success", TEXT, ADMIN_SMS_RECIPIENT_MSG."(".ON_SUCCESS_MSG .")");
	$r->add_textbox("admin_sms_originator_success", TEXT, ADMIN_SMS_ORIGINATOR_MSG."(".ON_SUCCESS_MSG .")");
	$r->add_textbox("admin_sms_message_success", TEXT, ADMIN_SMS_MESSAGE_MSG."(".ON_SUCCESS_MSG .")");

	$r->add_checkbox("user_sms_success", INTEGER);
	$r->add_textbox("user_sms_recipient_success", TEXT, USER_SMS_RECIPIENT_MSG."(".ON_SUCCESS_MSG .")");
	$r->add_textbox("user_sms_originator_success", TEXT, USER_SMS_ORIGINATOR_MSG."(".ON_SUCCESS_MSG .")");
	$r->add_textbox("user_sms_message_success", TEXT, USER_SMS_MESSAGE_MSG."(".ON_SUCCESS_MSG .")");

	// on pending parameters
	$r->add_checkbox("admin_notify_pending", INTEGER);
	$r->add_textbox("admin_mail_to_pending", TEXT);
	$r->add_textbox("admin_mail_from_pending", TEXT);
	$r->add_textbox("admin_mail_cc_pending", TEXT);
	$r->add_textbox("admin_mail_bcc_pending", TEXT);
	$r->add_textbox("admin_mail_reply_to_pending", TEXT);
	$r->add_textbox("admin_mail_return_path_pending", TEXT);
	$r->add_textbox("admin_mail_subject_pending", TEXT);
	$r->add_radio("admin_mail_type_pending", TEXT, $message_types);
	$r->add_textbox("admin_mail_body_pending", TEXT);

	$r->add_checkbox("user_notify_pending", INTEGER);
	$r->add_textbox("user_mail_from_pending", TEXT);
	$r->add_textbox("user_mail_cc_pending", TEXT);
	$r->add_textbox("user_mail_bcc_pending", TEXT);
	$r->add_textbox("user_mail_reply_to_pending", TEXT);
	$r->add_textbox("user_mail_return_path_pending", TEXT);
	$r->add_textbox("user_mail_subject_pending", TEXT);
	$r->add_radio("user_mail_type_pending", TEXT, $message_types);
	$r->add_textbox("user_mail_body_pending", TEXT);

	$r->add_checkbox("admin_sms_pending", INTEGER);
	$r->add_textbox("admin_sms_recipient_pending", TEXT, ADMIN_SMS_RECIPIENT_MSG."(".ON_PENDING_MSG .")");
	$r->add_textbox("admin_sms_originator_pending", TEXT, ADMIN_SMS_ORIGINATOR_MSG."(".ON_PENDING_MSG .")");
	$r->add_textbox("admin_sms_message_pending", TEXT, ADMIN_SMS_MESSAGE_MSG."(".ON_PENDING_MSG .")");

	$r->add_checkbox("user_sms_pending", INTEGER);
	$r->add_textbox("user_sms_recipient_pending", TEXT, USER_SMS_RECIPIENT_MSG."(".ON_PENDING_MSG .")");
	$r->add_textbox("user_sms_originator_pending", TEXT, USER_SMS_ORIGINATOR_MSG."(".ON_PENDING_MSG .")");
	$r->add_textbox("user_sms_message_pending", TEXT, USER_SMS_MESSAGE_MSG."(".ON_PENDING_MSG .")");

	// on failure parameters
	$r->add_checkbox("admin_notify_failure", INTEGER);
	$r->add_textbox("admin_mail_to_failure", TEXT);
	$r->add_textbox("admin_mail_from_failure", TEXT);
	$r->add_textbox("admin_mail_cc_failure", TEXT);
	$r->add_textbox("admin_mail_bcc_failure", TEXT);
	$r->add_textbox("admin_mail_reply_to_failure", TEXT);
	$r->add_textbox("admin_mail_return_path_failure", TEXT);
	$r->add_textbox("admin_mail_subject_failure", TEXT);
	$r->add_radio("admin_mail_type_failure", TEXT, $message_types);
	$r->add_textbox("admin_mail_body_failure", TEXT);

	$r->add_checkbox("user_notify_failure", INTEGER);
	$r->add_textbox("user_mail_from_failure", TEXT);
	$r->add_textbox("user_mail_cc_failure", TEXT);
	$r->add_textbox("user_mail_bcc_failure", TEXT);
	$r->add_textbox("user_mail_reply_to_failure", TEXT);
	$r->add_textbox("user_mail_return_path_failure", TEXT);
	$r->add_textbox("user_mail_subject_failure", TEXT);
	$r->add_radio("user_mail_type_failure", TEXT, $message_types);
	$r->add_textbox("user_mail_body_failure", TEXT);

	$r->add_checkbox("admin_sms_failure", INTEGER);
	$r->add_textbox("admin_sms_recipient_failure", TEXT, ADMIN_SMS_RECIPIENT_MSG."(".ON_FAILURE_MSG .")");
	$r->add_textbox("admin_sms_originator_failure", TEXT, ADMIN_SMS_ORIGINATOR_MSG."(".ON_FAILURE_MSG .")");
	$r->add_textbox("admin_sms_message_failure", TEXT, ADMIN_SMS_MESSAGE_MSG."(".ON_FAILURE_MSG .")");

	$r->add_checkbox("user_sms_failure", INTEGER);
	$r->add_textbox("user_sms_recipient_failure", TEXT, USER_SMS_RECIPIENT_MSG."(".ON_FAILURE_MSG .")");
	$r->add_textbox("user_sms_originator_failure", TEXT, USER_SMS_ORIGINATOR_MSG."(".ON_FAILURE_MSG .")");
	$r->add_textbox("user_sms_message_failure", TEXT, USER_SMS_MESSAGE_MSG."(".ON_FAILURE_MSG .")");

	$r->get_form_values();

	$param_site_id = get_session("session_site_id");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	$operation = get_param("operation");
	$return_page = get_param("rp");
	if (!strlen($return_page)) { $return_page = "admin_payment_systems.php"; }

	if (strlen($operation))
	{
		$tab = "general";
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		if ($r->get_value("admin_sms_new")) {
			$r->change_property("admin_sms_recipient_new", REQUIRED, true);
			$r->change_property("admin_sms_message_new", REQUIRED, true);
		}
		if ($r->get_value("user_sms_new")) {
			$r->change_property("user_sms_message_new", REQUIRED, true);
		}

		if ($r->get_value("admin_sms_success")) {
			$r->change_property("admin_sms_recipient_success", REQUIRED, true);
			$r->change_property("admin_sms_message_success", REQUIRED, true);
		}
		if ($r->get_value("user_sms_success")) {
			$r->change_property("user_sms_message_success", REQUIRED, true);
		}

		if ($r->get_value("admin_sms_pending")) {
			$r->change_property("admin_sms_recipient_pending", REQUIRED, true);
			$r->change_property("admin_sms_message_pending", REQUIRED, true);
		}
		if ($r->get_value("user_sms_pending")) {
			$r->change_property("user_sms_message_pending", REQUIRED, true);
		}

		if ($r->get_value("admin_sms_failure")) {
			$r->change_property("admin_sms_recipient_failure", REQUIRED, true);
			$r->change_property("admin_sms_message_failure", REQUIRED, true);
		}
		if ($r->get_value("user_sms_failure")) {
			$r->change_property("user_sms_message_failure", REQUIRED, true);
		}
		
		$is_valid = $r->validate();

		if (!strlen($r->errors))
		{
			$sql = "DELETE FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
			if ($multisites_version) {
				$sql .= " AND site_id=" . $db->tosql($param_site_id,INTEGER);
			}
			$db->query($sql);
			foreach ($r->parameters as $key => $value)
			{
				if ($multisites_version) {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
					$sql .= $db->tosql($setting_type, TEXT) . ", '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
					$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				} else {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES (";
					$sql .= $db->tosql($setting_type, TEXT) . ", '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ")";				
				}
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
			$sql  = "SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= "WHERE setting_type=" . $db->tosql($setting_type, TEXT) . " AND setting_name='" . $key . "'";
			if ($multisites_version) {
				$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
				$sql .= "ORDER BY site_id DESC ";
			}
			$r->set_value($key, get_db_value($sql));
		}
	}

	$r->set_parameters();
	$t->set_var("rp", htmlspecialchars($return_page));

	// set styles for tabs
	$tabs = array(
		"general" => array("title" => ADMIN_GENERAL_MSG), 
		"notify_new" => array("title" => ONCREATION_MSG), 
		"notify_success" => array("title" => ON_SUCCESS_MSG), 
		"notify_pending" => array("title" => ON_PENDING_MSG), 
		"notify_failure" => array("title" => ON_FAILURE_MSG), 
	);

	parse_admin_tabs($tabs, $tab, 5);

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
		$sites   = get_db_values($sql, "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}	
	
	$t->pparse("main");

?>