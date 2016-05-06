<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_registration_settings.php                          ***
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

	check_admin_security("admin_registration");

	$setting_type = "registration";
	$message_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);
		
	$r = new VA_Record($table_prefix . "global_settings");
	
	$r->add_checkbox("show_empty_categories", INTEGER);
	$r->add_textbox("intro_text", TEXT);
	$r->add_textbox("final_text", TEXT);
		
	$r->add_checkbox("category_id_required", INTEGER);
	$r->add_checkbox("onlist_category_id", INTEGER);
	
	$r->add_checkbox("show_item_id", INTEGER);
	$r->add_checkbox("item_id_required", INTEGER);
	$r->add_checkbox("onlist_item_id", INTEGER);
	
	$r->add_checkbox("show_item_name", INTEGER);
	$r->add_checkbox("item_name_required", INTEGER);
	$r->add_checkbox("onlist_item_name", INTEGER);
	
	$r->add_checkbox("show_item_code", INTEGER);
	$r->add_checkbox("item_code_required", INTEGER);
	$r->add_checkbox("onlist_item_code", INTEGER);
	
	$r->add_checkbox("show_serial_number", INTEGER);
	$r->add_checkbox("serial_number_required", INTEGER);
	$r->add_checkbox("onlist_serial_number", INTEGER);
	
	$r->add_checkbox("show_invoice_number", INTEGER);
	$r->add_checkbox("invoice_number_required", INTEGER);
	$r->add_checkbox("onlist_invoice_number", INTEGER);
	
	$r->add_checkbox("show_store_name", INTEGER);
	$r->add_checkbox("store_name_required", INTEGER);
	$r->add_checkbox("onlist_store_name", INTEGER);
	
	$r->add_checkbox("show_purchased_day", INTEGER);
	$r->add_checkbox("purchased_day_required", INTEGER);
	$r->add_checkbox("onlist_purchased_day", INTEGER);
	
	$r->add_checkbox("show_purchased_month", INTEGER);
	$r->add_checkbox("purchased_month_required", INTEGER);
	$r->add_checkbox("onlist_purchased_month", INTEGER);
	
	$r->add_checkbox("show_purchased_year", INTEGER);
	$r->add_checkbox("purchased_year_required", INTEGER);
	$r->add_checkbox("onlist_purchased_year", INTEGER);
	
	$r->add_checkbox("placed_admin_notification", INTEGER);
	$r->add_textbox("placed_admin_email", TEXT);
	$r->add_textbox("placed_admin_mail_from", TEXT);
	$r->add_textbox("placed_admin_mail_cc", TEXT);
	$r->add_textbox("placed_admin_mail_bcc", TEXT);
	$r->add_textbox("placed_admin_mail_reply_to", TEXT);
	$r->add_textbox("placed_admin_mail_return_path", TEXT);
	$r->add_textbox("placed_admin_subject", TEXT);
	$r->add_textbox("placed_admin_message_type", TEXT);
	$r->add_textbox("placed_admin_mail_return_path", TEXT);
	$r->add_textbox("placed_admin_subject", TEXT);
	$r->add_radio("placed_admin_message_type", TEXT, $message_types);
	$r->add_textbox("placed_admin_message", TEXT);
	
	$r->add_checkbox("placed_user_notification", INTEGER);
	$r->add_textbox("placed_user_email", TEXT);
	$r->add_textbox("placed_user_mail_from", TEXT);
	$r->add_textbox("placed_user_mail_cc", TEXT);
	$r->add_textbox("placed_user_mail_bcc", TEXT);
	$r->add_textbox("placed_user_mail_reply_to", TEXT);
	$r->add_textbox("placed_user_mail_return_path", TEXT);
	$r->add_textbox("placed_user_subject", TEXT);
	$r->add_textbox("placed_user_message_type", TEXT);
	$r->add_textbox("placed_user_mail_return_path", TEXT);
	$r->add_textbox("placed_user_subject", TEXT);
	$r->add_radio("placed_user_message_type", TEXT, $message_types);
	$r->add_textbox("placed_user_message", TEXT);
	
	$r->add_checkbox("approved_admin_notification", INTEGER);
	$r->add_textbox("approved_admin_email", TEXT);
	$r->add_textbox("approved_admin_mail_from", TEXT);
	$r->add_textbox("approved_admin_mail_cc", TEXT);
	$r->add_textbox("approved_admin_mail_bcc", TEXT);
	$r->add_textbox("approved_admin_mail_reply_to", TEXT);
	$r->add_textbox("approved_admin_mail_return_path", TEXT);
	$r->add_textbox("approved_admin_subject", TEXT);
	$r->add_textbox("approved_admin_message_type", TEXT);
	$r->add_textbox("approved_admin_mail_return_path", TEXT);
	$r->add_textbox("approved_admin_subject", TEXT);
	$r->add_radio("approved_admin_message_type", TEXT, $message_types);
	$r->add_textbox("approved_admin_message", TEXT);
	
	$r->add_checkbox("approved_user_notification", INTEGER);
	$r->add_textbox("approved_user_email", TEXT);
	$r->add_textbox("approved_user_mail_from", TEXT);
	$r->add_textbox("approved_user_mail_cc", TEXT);
	$r->add_textbox("approved_user_mail_bcc", TEXT);
	$r->add_textbox("approved_user_mail_reply_to", TEXT);
	$r->add_textbox("approved_user_mail_return_path", TEXT);
	$r->add_textbox("approved_user_subject", TEXT);
	$r->add_textbox("approved_user_message_type", TEXT);
	$r->add_textbox("approved_user_mail_return_path", TEXT);
	$r->add_textbox("approved_user_subject", TEXT);
	$r->add_radio("approved_user_message_type", TEXT, $message_types);
	$r->add_textbox("approved_user_message", TEXT);
	
	$r->add_checkbox("declined_admin_notification", INTEGER);
	$r->add_textbox("declined_admin_email", TEXT);
	$r->add_textbox("declined_admin_mail_from", TEXT);
	$r->add_textbox("declined_admin_mail_cc", TEXT);
	$r->add_textbox("declined_admin_mail_bcc", TEXT);
	$r->add_textbox("declined_admin_mail_reply_to", TEXT);
	$r->add_textbox("declined_admin_mail_return_path", TEXT);
	$r->add_textbox("declined_admin_subject", TEXT);
	$r->add_textbox("declined_admin_message_type", TEXT);
	$r->add_textbox("declined_admin_mail_return_path", TEXT);
	$r->add_textbox("declined_admin_subject", TEXT);
	$r->add_radio("declined_admin_message_type", TEXT, $message_types);
	$r->add_textbox("declined_admin_message", TEXT);
	
	$r->add_checkbox("declined_user_notification", INTEGER);
	$r->add_textbox("declined_user_email", TEXT);
	$r->add_textbox("declined_user_mail_from", TEXT);
	$r->add_textbox("declined_user_mail_cc", TEXT);
	$r->add_textbox("declined_user_mail_bcc", TEXT);
	$r->add_textbox("declined_user_mail_reply_to", TEXT);
	$r->add_textbox("declined_user_mail_return_path", TEXT);
	$r->add_textbox("declined_user_subject", TEXT);
	$r->add_textbox("declined_user_message_type", TEXT);
	$r->add_textbox("declined_user_mail_return_path", TEXT);
	$r->add_textbox("declined_user_subject", TEXT);
	$r->add_radio("declined_user_message_type", TEXT, $message_types);
	$r->add_textbox("declined_user_message", TEXT);
	
	$r->get_form_values();
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_registration_settings.html");
	$t->set_var("admin_href", "admin.php");	
	$t->set_var("admin_registration_href", "admin_registration.php");
	$t->set_var("admin_registration_settings_href", "admin_registration_settings.php");
	$t->set_var("admin_registration_property_href", "admin_registration_property.php");
	$t->set_var("placed_admin_email_help_href", "admin_email_help.php");
	$t->set_var("placed_admin_order_help_href", "admin_registration_help.php");
	$t->set_var("approved_admin_email_help_href", "admin_email_help.php");
	$t->set_var("approved_admin_order_help_href", "admin_registration_help.php");
	$t->set_var("declined_admin_email_help_href", "admin_email_help.php");
	$t->set_var("declined_admin_order_help_href", "admin_registration_help.php");
	
	$param_site_id = get_session("session_site_id");
	
	$operation = get_param("operation");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	$return_page = get_param("rp");
	if (!strlen($return_page)) $return_page = "admin_registrations.php";
	$errors = "";
	
	
	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		if(!strlen($r->errors))
		{
			$sql = "DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='registration'";
			if ($multisites_version) {
				$sql .= " AND site_id=" . $db->tosql($param_site_id,INTEGER);
			}
			$db->query($sql);
			foreach($r->parameters as $key => $value)
			{
				if ($multisites_version) {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
					$sql .= "'registration', '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
					$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				} else {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES (";
					$sql .= "'registration', '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ")";
				}
				$db->query($sql);
			}

			if($operation !== "apply") {			
				header("Location: " . $return_page);
				exit;
			}
		}
	}
	else
	{
		foreach($r->parameters as $key => $value)
		{
			$sql  = "SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= "WHERE setting_type='registration' AND setting_name='" . $key . "'";
			if ($multisites_version) {
				$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
				$sql .= "ORDER BY site_id DESC ";
			}
			$r->set_value($key, get_db_value($sql));
		}
	}
	
	$sql  = " SELECT property_id, property_name, property_order, property_show, control_type ";
	$sql .= " FROM " . $table_prefix . "registration_custom_properties upp ";
	if ($sitelist) {
		$sql .= " WHERE site_id=" . $db->tosql($param_site_id, INTEGER, true, false);
	} else {
		$sql .= " WHERE site_id=1";
	}
	$sql .= " ORDER BY property_order ";
	$db->query($sql);
	if ($db->next_record()) {
		$t->parse("name_properties", false);
		$show_options = array(0 => DONT_SHOW_MSG, 1 => FOR_ALL_USERS_MSG, 2 => NEW_USERS_ONLY_MSG, 3 => REGISTERED_USERS_ONLY_MSG);
		$controls = array(
			"CHECKBOXLIST" => CHECKBOXLIST_MSG, "LABEL" => LABEL_MSG, "LISTBOX" => LISTBOX_MSG,
			"RADIOBUTTON" => RADIOBUTTON_MSG, "TEXTAREA" => TEXTAREA_MSG, "TEXTBOX" => TEXTBOX_MSG);

		do {
			$property_id = $db->f("property_id");
			$property_name = $db->f("property_name");
			$property_order = $db->f("property_order");
			$property_show = $db->f("property_show");
			$control_type = $db->f("control_type");

			$t->set_var("property_id",   $property_id);
			$t->set_var("property_name", $property_name);
			$t->set_var("property_order", $property_order);
			$t->set_var("property_show", $show_options[$property_show]);
			$t->set_var("control_type", $controls[$control_type]);

			$t->set_var("section_name",  ALL_MSG);

			$t->parse("properties", true);
		} while ($db->next_record());
	} else {
		$t->parse("no_properties", false);
	}
			
	$r->set_parameters();
	$t->set_var("rp", htmlspecialchars($return_page));	
	
	$tabs = array("general" => ADMIN_GENERAL_MSG, 
		"predefined_fields" => PREDEFINED_FIELDS_MSG,
		"custom_fields"  => CUSTOM_FIELDS_MSG, 
		"placed_email"   => PRODUCT_REGISTRATION_PLACED_EMAIL_MSG,
		"approved_email" => PRODUCT_REGISTRATION_APPROVED_EMAIL_MSG,
		"declined_email" => PRODUCT_REGISTRATION_DECLINED_EMAIL_MSG	
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
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	
	// multisites
	if ($sitelist) {
		$sql = " SELECT site_id, site_name FROM " . $table_prefix . "sites ORDER BY site_id ";
		$sites = get_db_values($sql, array());		
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist");
	}	
	$t->pparse("main");
?>