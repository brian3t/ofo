<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_info.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ("../messages/".$language_code."/cart_messages.php");

	include_once("./admin_common.php");

	check_admin_security("sales_orders");
	check_admin_security("order_profile");

	$message_types =
		array(
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$currency_block =
		array(
			array(0, DONT_SHOW_MSG),
			array(1, SHOW_IMAGE_SELECTION_MSG),
			array(2, SHOW_LISTBOX_SELECTION_MSG)
		);
	
	$shipping_block =
		array(
			array(0, RADIOBUTTON_MSG),
			array(1, LISTBOX_MSG)
		);

	$subcomponents_values =
		array(
			array(0, EACH_SUBCOMP_SEPARATE_MSG),
			array(1, SUBCOMP_SHOWN_UNDERNEATH_MSG),
		);

	$payment_control_types =
		array(
			array(0, LISTBOX_MSG),
			array(1, RADIOBUTTON_MSG)
		);

	$payment_image_options =
		array(
			array(0, NO_IMAGE_MSG),
			array(1, IMAGE_SMALL_MSG),
			array(2, IMAGE_LARGE_MSG)
		);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_order_info.html");

	$t->set_var("admin_order_info_href", "admin_order_info.php");
	$t->set_var("admin_order_help_href", "admin_order_help.php");
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_email_help_href", "admin_email_help.php");
	$t->set_var("admin_order_property_href", "admin_order_property.php");
	$t->set_var("days_msg", strtolower(DAYS_MSG));


	$r = new VA_Record($table_prefix . "global_settings");

	$r->add_textbox("intro_text", TEXT);
	$r->add_radio("currency_block", TEXT, $currency_block);
	$r->add_radio("shipping_block", TEXT, $shipping_block);
	$r->add_radio("subcomponents_show_type", TEXT, $subcomponents_values);

	// payment system fields
	$r->add_radio("payment_control_type", TEXT, $payment_control_types);
	$r->add_select("payment_image", INTEGER, $payment_image_options);

	// restriction fields
	$r->add_textbox("order_min_goods_cost", FLOAT, ORDER_MIN_PRODUCTS_COST_FIELD);
	$r->add_textbox("order_max_goods_cost", FLOAT, ORDER_MAX_PRODUCTS_COST_FIELD);
	$r->add_textbox("order_min_weight", FLOAT, ORDER_MIN_WEIGHT_FIELD);
	$r->add_textbox("order_max_weight", FLOAT, ORDER_MAX_WEIGHT_FIELD);
	$r->add_checkbox("prevent_repurchase", INTEGER);
	$r->add_textbox("repurchase_period", FLOAT, REPURCHASE_PERIOD_MSG);

	// set up html form parameters
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

	// add checkboxes for Call Center
	$r->add_checkbox("call_center_show_name", INTEGER);
	$r->add_checkbox("call_center_show_first_name", INTEGER);
	$r->add_checkbox("call_center_show_last_name", INTEGER);
	$r->add_checkbox("call_center_show_company_id", INTEGER);
	$r->add_checkbox("call_center_show_company_name", INTEGER);
	$r->add_checkbox("call_center_show_email", INTEGER);
	$r->add_checkbox("call_center_show_address1", INTEGER);
	$r->add_checkbox("call_center_show_address2", INTEGER);
	$r->add_checkbox("call_center_show_city", INTEGER);
	$r->add_checkbox("call_center_show_province", INTEGER);
	$r->add_checkbox("call_center_show_state_id", INTEGER);
	$r->add_checkbox("call_center_show_zip", INTEGER);
	$r->add_checkbox("call_center_show_country_id", INTEGER);
	$r->add_checkbox("call_center_show_phone", INTEGER);
	$r->add_checkbox("call_center_show_daytime_phone", INTEGER);
	$r->add_checkbox("call_center_show_evening_phone", INTEGER);
	$r->add_checkbox("call_center_show_cell_phone", INTEGER);
	$r->add_checkbox("call_center_show_fax", INTEGER);

	$r->add_checkbox("call_center_show_delivery_name", INTEGER);
	$r->add_checkbox("call_center_show_delivery_first_name", INTEGER);
	$r->add_checkbox("call_center_show_delivery_last_name", INTEGER);
	$r->add_checkbox("call_center_show_delivery_company_id", INTEGER);
	$r->add_checkbox("call_center_show_delivery_company_name", INTEGER);
	$r->add_checkbox("call_center_show_delivery_email", INTEGER);
	$r->add_checkbox("call_center_show_delivery_address1", INTEGER);
	$r->add_checkbox("call_center_show_delivery_address2", INTEGER);
	$r->add_checkbox("call_center_show_delivery_city", INTEGER);
	$r->add_checkbox("call_center_show_delivery_province", INTEGER);
	$r->add_checkbox("call_center_show_delivery_state_id", INTEGER);
	$r->add_checkbox("call_center_show_delivery_zip", INTEGER);
	$r->add_checkbox("call_center_show_delivery_country_id", INTEGER);
	$r->add_checkbox("call_center_show_delivery_phone", INTEGER);
	$r->add_checkbox("call_center_show_delivery_daytime_phone", INTEGER);
	$r->add_checkbox("call_center_show_delivery_evening_phone", INTEGER);
	$r->add_checkbox("call_center_show_delivery_cell_phone", INTEGER);
	$r->add_checkbox("call_center_show_delivery_fax", INTEGER);

	$r->add_checkbox("call_center_name_required", INTEGER);
	$r->add_checkbox("call_center_first_name_required", INTEGER);
	$r->add_checkbox("call_center_last_name_required", INTEGER);
	$r->add_checkbox("call_center_company_id_required", INTEGER);
	$r->add_checkbox("call_center_company_name_required", INTEGER);
	$r->add_checkbox("call_center_email_required", INTEGER);
	$r->add_checkbox("call_center_address1_required", INTEGER);
	$r->add_checkbox("call_center_address2_required", INTEGER);
	$r->add_checkbox("call_center_city_required", INTEGER);
	$r->add_checkbox("call_center_province_required", INTEGER);
	$r->add_checkbox("call_center_state_id_required", INTEGER);
	$r->add_checkbox("call_center_zip_required", INTEGER);
	$r->add_checkbox("call_center_country_id_required", INTEGER);
	$r->add_checkbox("call_center_phone_required", INTEGER);
	$r->add_checkbox("call_center_daytime_phone_required", INTEGER);
	$r->add_checkbox("call_center_evening_phone_required", INTEGER);
	$r->add_checkbox("call_center_cell_phone_required", INTEGER);
	$r->add_checkbox("call_center_fax_required", INTEGER);

	$r->add_checkbox("call_center_delivery_name_required", INTEGER);
	$r->add_checkbox("call_center_delivery_first_name_required", INTEGER);
	$r->add_checkbox("call_center_delivery_last_name_required", INTEGER);
	$r->add_checkbox("call_center_delivery_company_id_required", INTEGER);
	$r->add_checkbox("call_center_delivery_company_name_required", INTEGER);
	$r->add_checkbox("call_center_delivery_email_required", INTEGER);
	$r->add_checkbox("call_center_delivery_address1_required", INTEGER);
	$r->add_checkbox("call_center_delivery_address2_required", INTEGER);
	$r->add_checkbox("call_center_delivery_city_required", INTEGER);
	$r->add_checkbox("call_center_delivery_province_required", INTEGER);
	$r->add_checkbox("call_center_delivery_state_id_required", INTEGER);
	$r->add_checkbox("call_center_delivery_zip_required", INTEGER);
	$r->add_checkbox("call_center_delivery_country_id_required", INTEGER);
	$r->add_checkbox("call_center_delivery_phone_required", INTEGER);
	$r->add_checkbox("call_center_delivery_daytime_phone_required", INTEGER);
	$r->add_checkbox("call_center_delivery_evening_phone_required", INTEGER);
	$r->add_checkbox("call_center_delivery_cell_phone_required", INTEGER);
	$r->add_checkbox("call_center_delivery_fax_required", INTEGER);

	$r->add_checkbox("subscribe_block", INTEGER);

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

	// predefined email
	$r->add_textbox("predefined_mail_from", TEXT);
	$r->add_textbox("predefined_mail_cc", TEXT);
	$r->add_textbox("predefined_mail_bcc", TEXT);
	$r->add_textbox("predefined_mail_reply_to", TEXT);
	$r->add_textbox("predefined_mail_return_path", TEXT);
	$r->add_textbox("predefined_mail_subject", TEXT);
	$r->add_radio("predefined_mail_type", TEXT, $message_types);
	$r->add_textbox("predefined_mail_body", TEXT);

	$r->get_form_values();

	$param_site_id = get_session("session_site_id");
	$operation = get_param("operation");	
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }	
	$return_page = get_param("rp");
	if (!strlen($return_page)) $return_page = "admin.php";
	$errors = "";

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
		if ($r->get_value("user_sms_notification")) {
			$r->change_property("user_sms_message", REQUIRED, true);
		}

		$r->validate();

		if (!strlen($r->errors))
		{
			$sql = "DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='order_info' ";
			if ($multisites_version) {
				$sql .= " AND site_id=" . $db->tosql($param_site_id,INTEGER);
			}
			$db->query($sql);
			foreach ($r->parameters as $key => $value)
			{
				if ($multisites_version) {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
					$sql .= "'order_info', '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
					$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				} else {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES (";
					$sql .= "'order_info', '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ")";					
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
			$sql .= "WHERE setting_type='order_info' AND setting_name='" . $key . "'";
			if ($multisites_version) {
				$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
				$sql .= "ORDER BY site_id DESC ";
			}
			$r->set_value($key, get_db_value($sql));
		}
	}

	$sql  = " SELECT property_id, property_name, property_type, property_show, control_type ";
	$sql .= " FROM " . $table_prefix . "order_custom_properties ";
	$sql .= " WHERE (payment_id=0 OR payment_id IS NULL) ";
	if ($sitelist) {
		$sql .= " AND site_id=" . $db->tosql($param_site_id, INTEGER, true, false);
	} else {
		$sql .= " AND site_id=1";
	}
	$sql .= " ORDER BY property_order, property_id ";
	$db->query($sql);
	if ($db->next_record()) {
		$property_types = array("0" => HIDDEN_MSG, "1" => ADMIN_CART_MSG, "2" => PERSONAL_DETAILS_MSG, "3" => DELIVERY_DETAILS_MSG);
		$property_show_values = array("0" => FOR_ALL_ORDERS_MSG, "1" => ONLY_WEB_ORDERS_MSG, "2" => ONLY_FOR_CALL_CENTRE_MSG);
		$controls = array(
			"CHECKBOXLIST" => CHECKBOXLIST_MSG, "LABEL" => LABEL_MSG, "LISTBOX" => LISTBOX_MSG,
			"RADIOBUTTON" => RADIOBUTTON_MSG, "TEXTAREA" => TEXTAREA_MSG, "TEXTBOX" => TEXTBOX_MSG);

		$t->parse("name_properties", false);
		do {
			$property_id = $db->f("property_id");
			$property_name = get_translation($db->f("property_name"));
			$property_type = $property_types[$db->f("property_type")];
			$property_show = get_setting_value($property_show_values, $db->f("property_show"), "");
			$control_type = $controls[$db->f("control_type")];

			$t->set_var("property_id",   $property_id);
			$t->set_var("property_name", $property_name);
			$t->set_var("property_type", $property_type);
			$t->set_var("property_show", $property_show);
			$t->set_var("control_type",  $control_type);

			$t->parse("properties", true);
		} while ($db->next_record());
	} else {
		$t->parse("no_properties", false);
	}

	$r->set_parameters();
	$t->set_var("rp", htmlspecialchars($return_page));

	// set styles for tabs
	$tabs = array(
		"general" => array("title" => ADMIN_GENERAL_MSG), 
		"predefined_fields" => array("title" => PREDEFINED_FIELDS_MSG), 
		"custom_fields" => array("title" => CUSTOM_ORDER_FILEDS_MSG), 
		"notification_email" => array("title" => NOTIFICATION_EMAIL_MSG), 
		"predefined_email" => array("title" => PREDEFINED_ORDER_EMAIL_MSG), 
	);

	$tabs_in_row = 6; 
	parse_admin_tabs($tabs, $tab, 6);
	$t->set_var("tab", $tab);


	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	
	// multisites
	if ($sitelist) {
		$sql = " SELECT site_id, site_name FROM " . $table_prefix . "sites ORDER BY site_id ";
		$sites = get_db_values($sql, array());
		set_options($sites, $param_site_id, "param_site_id");		
		$t->parse("sitelist");
	}
	
	$t->pparse("main");

?>