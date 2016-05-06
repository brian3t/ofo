<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_profile.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once("./admin_common.php");

	check_admin_security("users_groups");

	$type_id = get_param("type_id");
	$setting_type = "user_profile_" . $type_id;
	$sql = " SELECT type_name FROM " . $table_prefix . "user_types WHERE type_id=" . $db->tosql($type_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$type_name = get_translation($db->f("type_name"));
	} else {
		header ("Location: admin_user_types.php");
		exit;
	}

	$validation_types = 
		array( 
			array(2, FOR_ALL_USERS_MSG), array(1, UNREGISTERED_USER_ONLY_MSG), array(0, NOT_USED_MSG)
		);

	$message_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$html_editors = 
		array( 
			array(1, WYSIWYG_HTML_EDITOR_MSG),
			array(0, TEXTAREA_EDITOR_MSG)
			);

	$login_field_types = 
		array( 
			array(1, ALPHANUMERICAL_CHARS_MSG),
			array(2, CONTACT_EMAIL_ADDRESS_MSG)
			);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_user_profile.html");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", PROFILE_SECTION_MSG, CONFIRM_DELETE_MSG));

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href",              "admin.php");
	$t->set_var("admin_users_href",        "admin_users.php");
	$t->set_var("admin_user_types_href",   "admin_user_types.php");
	$t->set_var("admin_user_type_href",    "admin_user_type.php");
	$t->set_var("admin_user_property_href","admin_user_property.php");
	$t->set_var("admin_user_profile_href", "admin_user_profile.php");
	$t->set_var("admin_user_profile_help_href", "admin_user_profile_help.php");
	$t->set_var("admin_user_sections_href","admin_user_sections.php");
	$t->set_var("admin_email_help_href",   "admin_email_help.php");
	$t->set_var("user_home_href",          "user_home.php");
	$t->set_var("type_id",   $type_id);
	$t->set_var("type_name", $type_name);

	$r = new VA_Record($table_prefix . "global_settings");

	// set up html form parameters
	$r->add_radio("use_random_image", TEXT, $validation_types);
	$r->add_textbox("intro_text_new", TEXT);
	$r->add_textbox("registration_redirect", TEXT);
	$r->add_textbox("intro_text_registered", TEXT);
	$r->add_textbox("update_redirect", TEXT);

	// login fields
	$r->add_radio("login_field_type", TEXT, $login_field_types);

	$r->add_checkbox("show_nickname", INTEGER);
	$r->add_checkbox("nickname_required", INTEGER);

	// personal details
	$r->add_checkbox("show_name", INTEGER);
	$r->add_checkbox("show_first_name", INTEGER);
	$r->add_checkbox("show_last_name", INTEGER);
	$r->add_checkbox("show_company_id", INTEGER);
	$r->add_checkbox("show_company_name", INTEGER);
	$r->add_checkbox("show_email", INTEGER);
	$r->add_checkbox("show_address1", INTEGER);
	$r->add_checkbox("show_address2", INTEGER);
	$r->add_checkbox("show_city", INTEGER);
	$r->add_checkbox("show_province", INTEGER);
	$r->add_checkbox("show_state_id", INTEGER);
	$r->add_checkbox("show_zip", INTEGER);
	$r->add_checkbox("show_country_id", INTEGER);
	$r->add_checkbox("show_phone", INTEGER);
	$r->add_checkbox("show_daytime_phone", INTEGER);
	$r->add_checkbox("show_evening_phone", INTEGER);
	$r->add_checkbox("show_cell_phone", INTEGER);
	$r->add_checkbox("show_fax", INTEGER);

	$r->add_checkbox("show_delivery_name", INTEGER);
	$r->add_checkbox("show_delivery_first_name", INTEGER);
	$r->add_checkbox("show_delivery_last_name", INTEGER);
	$r->add_checkbox("show_delivery_company_id", INTEGER);
	$r->add_checkbox("show_delivery_company_name", INTEGER);
	$r->add_checkbox("show_delivery_email", INTEGER);
	$r->add_checkbox("show_delivery_address1", INTEGER);
	$r->add_checkbox("show_delivery_address2", INTEGER);
	$r->add_checkbox("show_delivery_city", INTEGER);
	$r->add_checkbox("show_delivery_province", INTEGER);
	$r->add_checkbox("show_delivery_state_id", INTEGER);
	$r->add_checkbox("show_delivery_zip", INTEGER);
	$r->add_checkbox("show_delivery_country_id", INTEGER);
	$r->add_checkbox("show_delivery_phone", INTEGER);
	$r->add_checkbox("show_delivery_daytime_phone", INTEGER);
	$r->add_checkbox("show_delivery_evening_phone", INTEGER);
	$r->add_checkbox("show_delivery_cell_phone", INTEGER);
	$r->add_checkbox("show_delivery_fax", INTEGER);

	$r->add_checkbox("name_required", INTEGER);
	$r->add_checkbox("first_name_required", INTEGER);
	$r->add_checkbox("last_name_required", INTEGER);
	$r->add_checkbox("company_id_required", INTEGER);
	$r->add_checkbox("company_name_required", INTEGER);
	$r->add_checkbox("email_required", INTEGER);
	$r->add_checkbox("address1_required", INTEGER);
	$r->add_checkbox("address2_required", INTEGER);
	$r->add_checkbox("city_required", INTEGER);
	$r->add_checkbox("province_required", INTEGER);
	$r->add_checkbox("state_id_required", INTEGER);
	$r->add_checkbox("zip_required", INTEGER);
	$r->add_checkbox("country_id_required", INTEGER);
	$r->add_checkbox("phone_required", INTEGER);
	$r->add_checkbox("daytime_phone_required", INTEGER);
	$r->add_checkbox("evening_phone_required", INTEGER);
	$r->add_checkbox("cell_phone_required", INTEGER);
	$r->add_checkbox("fax_required", INTEGER);
	
	$r->add_textbox("name_order", INTEGER);
	$r->add_textbox("first_name_order", INTEGER);
	$r->add_textbox("last_name_order", INTEGER);
	$r->add_textbox("company_id_order", INTEGER);
	$r->add_textbox("company_name_order", INTEGER);
	$r->add_textbox("email_order", INTEGER);
	$r->add_textbox("address1_order", INTEGER);
	$r->add_textbox("address2_order", INTEGER);
	$r->add_textbox("city_order", INTEGER);
	$r->add_textbox("province_order", INTEGER);
	$r->add_textbox("state_id_order", INTEGER);
	$r->add_textbox("zip_order", INTEGER);
	$r->add_textbox("country_id_order", INTEGER);
	$r->add_textbox("phone_order", INTEGER);
	$r->add_textbox("daytime_phone_order", INTEGER);
	$r->add_textbox("evening_phone_order", INTEGER);
	$r->add_textbox("cell_phone_order", INTEGER);
	$r->add_textbox("fax_order", INTEGER);

	$r->add_checkbox("delivery_name_required", INTEGER);
	$r->add_checkbox("delivery_first_name_required", INTEGER);
	$r->add_checkbox("delivery_last_name_required", INTEGER);
	$r->add_checkbox("delivery_company_id_required", INTEGER);
	$r->add_checkbox("delivery_company_name_required", INTEGER);
	$r->add_checkbox("delivery_email_required", INTEGER);
	$r->add_checkbox("delivery_address1_required", INTEGER);
	$r->add_checkbox("delivery_address2_required", INTEGER);
	$r->add_checkbox("delivery_city_required", INTEGER);
	$r->add_checkbox("delivery_province_required", INTEGER);
	$r->add_checkbox("delivery_state_id_required", INTEGER);
	$r->add_checkbox("delivery_zip_required", INTEGER);
	$r->add_checkbox("delivery_country_id_required", INTEGER);
	$r->add_checkbox("delivery_phone_required", INTEGER);
	$r->add_checkbox("delivery_daytime_phone_required", INTEGER);
	$r->add_checkbox("delivery_evening_phone_required", INTEGER);
	$r->add_checkbox("delivery_cell_phone_required", INTEGER);
	$r->add_checkbox("delivery_fax_required", INTEGER);
	
	$r->add_textbox("delivery_name_order", INTEGER);
	$r->add_textbox("delivery_first_name_order", INTEGER);
	$r->add_textbox("delivery_last_name_order", INTEGER);
	$r->add_textbox("delivery_company_id_order", INTEGER);
	$r->add_textbox("delivery_company_name_order", INTEGER);
	$r->add_textbox("delivery_email_order", INTEGER);
	$r->add_textbox("delivery_address1_order", INTEGER);
	$r->add_textbox("delivery_address2_order", INTEGER);
	$r->add_textbox("delivery_city_order", INTEGER);
	$r->add_textbox("delivery_province_order", INTEGER);
	$r->add_textbox("delivery_state_id_order", INTEGER);
	$r->add_textbox("delivery_zip_order", INTEGER);
	$r->add_textbox("delivery_country_id_order", INTEGER);
	$r->add_textbox("delivery_phone_order", INTEGER);
	$r->add_textbox("delivery_daytime_phone_order", INTEGER);
	$r->add_textbox("delivery_evening_phone_order", INTEGER);
	$r->add_textbox("delivery_cell_phone_order", INTEGER);
	$r->add_textbox("delivery_fax_order", INTEGER);

	$r->add_checkbox("show_birth_date", INTEGER);
	$r->add_checkbox("birth_date_required", INTEGER);
	$r->add_checkbox("birth_date_order", INTEGER);

	$r->add_checkbox("show_personal_image", INTEGER);
	$r->add_textbox("personal_image_width", INTEGER, PERSONAL_IMAGE_WIDTH_MSG);
	$r->add_textbox("personal_image_height", INTEGER, PERSONAL_IMAGE_HEIGHT_MSG);
	$r->add_textbox("personal_image_size", INTEGER, PERSONAL_IMAGE_SIZE_MSG);
	$r->add_checkbox("personal_image_resize", INTEGER);

	$r->add_checkbox("subscribe_block", INTEGER);
	
	// additional fields
	$r->add_checkbox("show_friendly_url", INTEGER);
	$r->add_checkbox("friendly_url_required", INTEGER);
	$r->add_textbox("friendly_url_order", INTEGER);
	
	$r->add_checkbox("show_tax_id", INTEGER);
	$r->add_checkbox("tax_id_required", INTEGER);
	$r->add_textbox("tax_id_order", INTEGER);
	
	$r->add_checkbox("show_paypal_account", INTEGER);
	$r->add_checkbox("paypal_account_required", INTEGER);
	$r->add_textbox("paypal_account_order", INTEGER);
	
	$r->add_checkbox("show_msn_account", INTEGER);
	$r->add_checkbox("msn_account_required", INTEGER);
	$r->add_textbox("msn_account_order", INTEGER);
	
	$r->add_checkbox("show_icq_number", INTEGER);
	$r->add_checkbox("icq_number_required", INTEGER);
	$r->add_textbox("icq_number_order", INTEGER);
	
	$r->add_checkbox("show_user_site_url", INTEGER);
	$r->add_checkbox("user_site_url_required", INTEGER);
	$r->add_textbox("user_site_url_order", INTEGER);
	
	$r->add_checkbox("show_short_description", INTEGER);
	$r->add_checkbox("short_description_required", INTEGER);	
	$r->add_radio("short_description_editor", INTEGER, $html_editors);
	$r->add_textbox("short_description_order", INTEGER);
	
	$r->add_checkbox("show_full_description", INTEGER);	
	$r->add_checkbox("full_description_required", INTEGER);
	$r->add_radio("full_description_editor", INTEGER, $html_editors);
	$r->add_textbox("full_description_order", INTEGER);
	
	$r->add_checkbox("show_is_hidden", INTEGER);
	$r->add_checkbox("is_hidden_required", INTEGER);
	$r->add_textbox("is_hidden_order", INTEGER);

	// email notification settings
	$r->add_checkbox("admin_notification", INTEGER);
	$r->add_textbox("admin_email", TEXT);
	$r->add_textbox("admin_mail_from", TEXT);
	$r->add_textbox("cc_emails", TEXT);
	$r->add_textbox("admin_mail_bcc", TEXT);
	$r->add_textbox("admin_mail_reply_to", TEXT);
	$r->add_textbox("admin_mail_return_path", TEXT);
	$r->add_textbox("admin_subject", TEXT);
	$r->add_radio("admin_message_type", TEXT, $message_types);
	$r->add_textbox("admin_message", TEXT);

	$r->add_checkbox("user_notification", INTEGER);
	$r->add_textbox("user_mail_from", TEXT);
	$r->add_textbox("user_mail_cc", TEXT);
	$r->add_textbox("user_mail_bcc", TEXT);
	$r->add_textbox("user_mail_reply_to", TEXT);
	$r->add_textbox("user_mail_return_path", TEXT);
	$r->add_textbox("user_subject", TEXT);
	$r->add_radio("user_message_type", TEXT, $message_types);
	$r->add_textbox("user_message", TEXT);

	// sms notification settings
	$r->add_checkbox("admin_sms_notification", INTEGER);
	$r->add_textbox("admin_sms_recipient", TEXT, ADMIN_SMS_RECIPIENT_MSG);
	$r->add_textbox("admin_sms_originator", TEXT, ADMIN_SMS_ORIGINATOR_MSG);
	$r->add_textbox("admin_sms_message", TEXT, ADMIN_SMS_MESSAGE_MSG);

	$r->add_checkbox("user_sms_notification", INTEGER);
	$r->add_textbox("user_sms_recipient", TEXT, USER_SMS_RECIPIENT_MSG);
	$r->add_textbox("user_sms_originator", TEXT, USER_SMS_ORIGINATOR_MSG);
	$r->add_textbox("user_sms_message", TEXT, USER_SMS_MESSAGE_MSG);


	// email birthday greetings settings
	$r->add_checkbox("user_birth_mail_greetings", INTEGER);
	$r->add_textbox("user_birth_mail_from", TEXT);
	$r->add_textbox("user_birth_mail_cc", TEXT);
	$r->add_textbox("user_birth_mail_bcc", TEXT);
	$r->add_textbox("user_birth_mail_reply_to", TEXT);
	$r->add_textbox("user_birth_mail_return_path", TEXT);
	$r->add_textbox("user_birth_subject", TEXT);
	$r->add_radio("user_birth_message_type", TEXT, $message_types);
	$r->add_textbox("user_birth_message", TEXT);

	// sms birthday greetings settings
	$r->add_checkbox("user_birth_sms_greetings", INTEGER);
	$r->add_textbox("user_birth_sms_recipient", TEXT, USER_SMS_RECIPIENT_MSG);
	$r->add_textbox("user_birth_sms_originator", TEXT, USER_SMS_ORIGINATOR_MSG);
	$r->add_textbox("user_birth_sms_message", TEXT, USER_SMS_MESSAGE_MSG);
	
	// email reminder service settings
	$r->add_checkbox("user_reminder_mail", INTEGER);
	$r->add_textbox("user_reminder_mail_from", TEXT);
	$r->add_textbox("user_reminder_mail_cc", TEXT);
	$r->add_textbox("user_reminder_mail_bcc", TEXT);
	$r->add_textbox("user_reminder_mail_reply_to", TEXT);
	$r->add_textbox("user_reminder_mail_return_path", TEXT);
	$r->add_textbox("user_reminder_subject", TEXT);
	$r->add_radio("user_reminder_message_type", TEXT, $message_types);
	$r->add_textbox("user_reminder_message", TEXT);

	// sms reminder service settings
	$r->add_checkbox("user_reminder_sms", INTEGER);
	$r->add_textbox("user_reminder_sms_recipient", TEXT, USER_SMS_RECIPIENT_MSG);
	$r->add_textbox("user_reminder_sms_originator", TEXT, USER_SMS_ORIGINATOR_MSG);
	$r->add_textbox("user_reminder_sms_message", TEXT, USER_SMS_MESSAGE_MSG);

	$r->get_form_values();

	$param_site_id = get_session("session_site_id");
		
	$operation = get_param("operation");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	$return_page = get_param("rp");
	if (!strlen($return_page)) {
		$return_page = "admin_user_types.php";
	}
	$t->set_var("rp", htmlspecialchars($return_page));

	if (strlen($operation))
	{
		$tab = "general";
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		if ($r->get_value("admin_sms_notification")) {
			$r->change_property("admin_sms_recipient", REQUIRED, true);
			$r->change_property("admin_sms_message", REQUIRED, true);
		}
		if ($r->get_value("user_sms_notification")) {
			$r->change_property("user_sms_message", REQUIRED, true);
		}

		if (!function_exists('imagecreate') && $r->get_value("use_random_image")) {	
			$r->errors .= RANDOM_IMAGE_VALIDATION_ERROR_MSG;
			$r->set_value("use_random_image",0);
		} 
 		
		$r->validate();

		if (!strlen($r->errors))
		{
			$sql = "DELETE FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
			if ($multisites_version) {
				$sql .= " AND site_id=" . $db->tosql($param_site_id,INTEGER);
			}
			$db->query($sql);
			foreach($r->parameters as $key => $value)
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
	else // get user_profile settings
	{
		$sql  = "SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= "WHERE setting_type=" . $db->tosql($setting_type, TEXT);
		if ($multisites_version) {
			$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
			$sql .= "ORDER BY site_id ASC ";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$setting_name = $db->f("setting_name");
			$setting_value = $db->f("setting_value");
			if ($r->parameter_exists($setting_name)) {
				$r->set_value($setting_name, $setting_value);
			}
		}
	}

	$sections = array();
	$sql = "SELECT section_id, section_name FROM " . $table_prefix . "user_profile_sections ORDER BY section_order, section_id ";
	$db->query($sql);
	while ($db->next_record()) {
		$section_id = $db->f("section_id");
		$section_name = get_translation($db->f("section_name"));
		$sections[$section_id] = $section_name;
	}

	$sql  = " SELECT upp.property_id, upp.property_name, upp.property_order, upp.property_show, ups.section_name ";
	$sql .= " FROM (" . $table_prefix . "user_profile_properties upp ";
	$sql .= " LEFT JOIN " . $table_prefix . "user_profile_sections ups ON ups.section_id=upp.section_id) ";
	$sql .= " WHERE user_type_id=" . $db->tosql($type_id, INTEGER);
	$sql .= " ORDER BY ups.section_order, upp.property_order ";
	$db->query($sql);
	if ($db->next_record()) {
		
		$show_options = array(0 => DONT_SHOW_MSG, 1 => FOR_ALL_USERS_MSG, 2 => NEW_USERS_ONLY_MSG, 3 => REGISTERED_USERS_ONLY_MSG);
		do {
			$property_id = $db->f("property_id");
			$property_name = $db->f("property_name");
			$property_order = $db->f("property_order");
			$property_show = $db->f("property_show");

			$section_name = get_translation($db->f("section_name"));

			$t->set_var("property_id",   $property_id);
			$t->set_var("property_name", $property_name);
			$t->set_var("property_order", $property_order);
			$t->set_var("property_show", $show_options[$property_show]);

			$t->set_var("section_name",  $section_name);

			$t->parse("properties", true);
		} while ($db->next_record());

		$t->parse("properties_titles", false);
	} else {
		$t->parse("no_properties", false);
	}

	$r->set_parameters();
	$t->set_var("type_id", htmlspecialchars($type_id));

	$t->set_var("LOGIN_INFO_MSG", $sections[1]);
	$t->set_var("PERSONAL_DETAILS_MSG", $sections[2]);
	$t->set_var("DELIVERY_DETAILS_MSG", $sections[3]);
	$t->set_var("ADDITIONAL_DETAILS_MSG", $sections[4]);

	$tabs = array("general" => ADMIN_GENERAL_MSG, "predefined_fields" => PREDEFINED_FIELDS_MSG, "custom_fields" => CUSTOM_FIELDS_MSG,
		"registration_notify" => REGITRATION_NOTIFY_MSG, "birthday_greetings" => BIRTHDAY_GREETINGS_MSG, "reminder_service" => REMINDER_SERVICE_MSG
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
	
	// multisites	
	if ($sitelist) {
		$sql = " SELECT sites_all FROM " . $table_prefix . "user_types WHERE type_id=" . $db->tosql($type_id, INTEGER);
		$sites_all = get_db_value($sql);
		$sql  = " SELECT s.site_id, s.site_name FROM " . $table_prefix . "sites AS s ";
		if (!$sites_all) {
			$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites AS t ON s.site_id=t.site_id ";
			$sql .= " WHERE t.type_id=" . $db->tosql($type_id, INTEGER);	
		}
		$sql .= " ORDER BY s.site_id ";
		$sites = get_db_values($sql, array());
		set_options($sites, $param_site_id, "param_site_id");		
		$t->parse("sitelist");
	}

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

?>