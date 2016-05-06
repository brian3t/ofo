<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_credit_card_info.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");

	check_admin_security("payment_systems");

	$payment_id = get_param("payment_id");
	$setting_type = "credit_card_info_" . $payment_id;
	$sql = " SELECT payment_name FROM " . $table_prefix . "payment_systems WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$payment_name = get_translation($db->f("payment_name"), $language_code);
	} else {
		header ("Location: admin_payment_systems.php");
		exit;
	}

	if ($sitelist) {
		$sites = array();
		$sql = " SELECT sites_all FROM " . $table_prefix . "payment_systems WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
		$db->query($sql);
		$sites_all = 0;
		if ($db->next_record()) {
			$sites_all = $db->f('sites_all');
		}
		
		$sql  = " SELECT s.site_id, s.site_name FROM " . $table_prefix . "sites AS s ";
		if (!$sites_all) {
			$sql .= " LEFT JOIN " . $table_prefix . "payment_systems_sites AS p ON s.site_id=p.site_id ";
			$sql .= " WHERE p.payment_id=" . $db->tosql($payment_id, INTEGER);	
		}
		$sql .= " ORDER BY s.site_id ";
		$sites = get_db_values($sql, array());
		if (count($sites) == 1) {
			$site_id = $sites[0][0];
		}
	}
	
	$message_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$cc_number_options = 
		array( 
			array(0, DONT_SAVE_MSG), 
			array(2, SAVE_ENCRUPTED_MSG)
		);

	$cc_code_options = 
		array( 
			array(0, DONT_SAVE_MSG), 
			array(2, SAVE_ENCRUPTED_MSG)
		);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_credit_card_info.html");
	$t->set_var("admin_credit_card_info_href", "admin_credit_card_info.php");
	$t->set_var("admin_payment_systems_href", "admin_payment_systems.php");
	$t->set_var("admin_payment_system_href", "admin_payment_system.php");
	$t->set_var("admin_order_property_href", "admin_order_property.php");
	$t->set_var("admin_order_help_href", "admin_order_help.php");
	$t->set_var("admin_email_help_href", "admin_email_help.php");
	$t->set_var("payment_id",   $payment_id);
	$t->set_var("payment_name", $payment_name);

	$r = new VA_Record($table_prefix . "credit_card_info");

	$r->add_textbox("intro_text", TEXT);

	// set up html form parameters
	$r->add_checkbox("show_cc_name", INTEGER);
	$r->add_checkbox("show_cc_first_name", INTEGER);
	$r->add_checkbox("show_cc_last_name", INTEGER);
	$r->add_checkbox("show_cc_number", INTEGER);
	$r->add_checkbox("show_cc_start_date", INTEGER);
	$r->add_checkbox("show_cc_expiry_date", INTEGER);
	$r->add_checkbox("show_cc_type", INTEGER);
	$r->add_checkbox("show_cc_issue_number", INTEGER);
	$r->add_checkbox("show_cc_security_code", INTEGER);
	$r->add_checkbox("show_pay_without_cc", INTEGER);
   
	$r->add_checkbox("cc_name_required", INTEGER);
	$r->add_checkbox("cc_first_name_required", INTEGER);
	$r->add_checkbox("cc_last_name_required", INTEGER);
	$r->add_checkbox("cc_number_required", INTEGER);
	$r->add_checkbox("cc_start_date_required", INTEGER);
	$r->add_checkbox("cc_expiry_date_required", INTEGER);
	$r->add_checkbox("cc_type_required", INTEGER);
	$r->add_checkbox("cc_issue_number_required", INTEGER);
	$r->add_checkbox("cc_security_code_required", INTEGER);
	$r->add_checkbox("pay_without_cc_required", INTEGER);
	
	// add checkboxes for Call Center
	$r->add_checkbox("call_center_show_cc_name", INTEGER);
	$r->add_checkbox("call_center_show_cc_first_name", INTEGER);
	$r->add_checkbox("call_center_show_cc_last_name", INTEGER);
	$r->add_checkbox("call_center_show_cc_number", INTEGER);
	$r->add_checkbox("call_center_show_cc_start_date", INTEGER);
	$r->add_checkbox("call_center_show_cc_expiry_date", INTEGER);
	$r->add_checkbox("call_center_show_cc_type", INTEGER);
	$r->add_checkbox("call_center_show_cc_issue_number", INTEGER);
	$r->add_checkbox("call_center_show_cc_security_code", INTEGER);
	$r->add_checkbox("call_center_show_pay_without_cc", INTEGER);
   
	$r->add_checkbox("call_center_cc_name_required", INTEGER);
	$r->add_checkbox("call_center_cc_first_name_required", INTEGER);
	$r->add_checkbox("call_center_cc_last_name_required", INTEGER);
	$r->add_checkbox("call_center_cc_number_required", INTEGER);
	$r->add_checkbox("call_center_cc_start_date_required", INTEGER);
	$r->add_checkbox("call_center_cc_expiry_date_required", INTEGER);
	$r->add_checkbox("call_center_cc_type_required", INTEGER);
	$r->add_checkbox("call_center_cc_issue_number_required", INTEGER);
	$r->add_checkbox("call_center_cc_security_code_required", INTEGER);
	$r->add_checkbox("call_center_pay_without_cc_required", INTEGER);

	$r->add_textbox("cc_allowed", TEXT);
	$r->add_textbox("cc_forbidden", TEXT);

	$r->add_checkbox("cc_number_split", INTEGER);
	$r->add_radio("cc_number_security", INTEGER, $cc_number_options);
	$r->add_radio("cc_code_security", INTEGER, $cc_code_options);

	$r->add_checkbox("admin_notification", INTEGER);
	// PGP enable
	$r->add_checkbox("admin_notification_pgp", INTEGER);
	
	$r->add_textbox("admin_email", TEXT);
	$r->add_textbox("admin_mail_from", TEXT);
	$r->add_textbox("cc_emails", TEXT);
	$r->add_textbox("admin_mail_bcc", TEXT);
	$r->add_textbox("admin_mail_reply_to", TEXT);
	$r->add_textbox("admin_mail_return_path", TEXT);
	$r->add_textbox("admin_subject", TEXT);
	$r->add_radio("admin_message_type", TEXT, $message_types);
	$r->add_textbox("admin_message", TEXT);

	// sms notification settings
	$r->add_checkbox("admin_sms_notification", INTEGER);
	$r->add_textbox("admin_sms_recipient", TEXT, ADMIN_SMS_RECIPIENT_MSG);
	$r->add_textbox("admin_sms_originator", TEXT, ADMIN_SMS_ORIGINATOR_MSG);
	$r->add_textbox("admin_sms_message", TEXT, ADMIN_SMS_MESSAGE_MSG);

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

		if ($r->get_value("admin_sms_notification")) {
			$r->change_property("admin_sms_recipient", REQUIRED, true);
			$r->change_property("admin_sms_message", REQUIRED, true);
		}

		$r->validate();

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
	else // get credit_order_info settings
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

	$sql  = " SELECT property_id, property_name, property_type ";
	$sql .= " FROM " . $table_prefix . "order_custom_properties ";
	$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
	$sql .= " ORDER BY property_order, property_id ";
	$db->query($sql);
	if ($db->next_record()) {
		$property_types = array("0" => HIDDEN_MSG, "4" => ACTIVE_MSG);

		do {
			$property_id = $db->f("property_id");
			$property_name = $db->f("property_name");
			$property_type = $property_types[$db->f("property_type")];
			$t->set_var("property_id",   $property_id);
			$t->set_var("property_name", $property_name);
			$t->set_var("property_type", $property_type);

			$t->parse("properties", true);
		} while ($db->next_record());
	} else {
		$t->parse("no_properties", false);
	}


	$r->set_parameters();
	$t->set_var("rp", htmlspecialchars($return_page));
	
	// multisites
	if ($sitelist) {
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist");
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>