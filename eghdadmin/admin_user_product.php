<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_product.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once($root_folder_path . "messages/" . $language_code . "/download_messages.php");
	include_once("./admin_common.php");

	check_admin_security("users_groups");

	$type_id = get_param("type_id");
	$setting_type = "user_product_" . $type_id;
	$sql = " SELECT type_name FROM " . $table_prefix . "user_types WHERE type_id=" . $db->tosql($type_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$type_name = get_translation($db->f("type_name"));
	} else {
		header ("Location: admin_user_types.php");
		exit;
	}

	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }

	$validation_types =
		array(
			array(1, ACTIVE_MSG), array(0, NOT_USED_MSG)
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

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_user_product.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href",              "admin.php");
	$t->set_var("admin_users_href",        "admin_users.php");
	$t->set_var("admin_user_types_href",   "admin_user_types.php");
	$t->set_var("admin_user_type_href",    "admin_user_type.php");
	$t->set_var("admin_user_product_href", "admin_user_product.php");
	$t->set_var("admin_user_product_help_href", "admin_user_product_help.php");
	$t->set_var("admin_email_help_href",   "admin_email_help.php");
	$t->set_var("user_home_href",          "user_home.php");
	$t->set_var("type_id",   $type_id);
	$t->set_var("type_name", $type_name);
	$t->set_var("HIDE_ADD_BUTTON_MSG", str_replace("{ADD_TO_CART_MSG}", ADD_TO_CART_MSG, HIDE_ADD_BUTTON_MSG));

	$r = new VA_Record($table_prefix . "global_settings");

	$r->add_textbox("products_limit", INTEGER, NUMBER_OF_PRODUCTS_MSG);
	$r->add_textbox("categories_number", INTEGER, NUMBER_CATEGORIES_MSG);
	$r->add_checkbox("can_select_folder", INTEGER, USER_CAN_SELECT_FOLDER_MSG);	
	$r->add_textbox("uploads_subfolder", TEXT, UPLOADS_SUBFOLDER_MSG);	
	$r->add_textbox("min_price_limit", NUMBER, MINIMUM_ALLOWED_PRICE_MSG);
	$r->add_textbox("max_price_limit", NUMBER, MAXIMUM_ALLOWED_PRICE_MSG);		
	$r->add_checkbox("activate_products", NUMBER);
	$r->add_checkbox("deactivate_products", NUMBER);
	$r->add_checkbox("show_terms", INTEGER);
	$r->add_textbox("terms_text", TEXT);

	// product fields
	$product_fields = array(
		// general information
		"is_showing", "item_order", "item_code", "friendly_url", "manufacturer_id", "manufacturer_code",
		"weight", "issue_date", "is_compared", "tax_free", "language_code",
		// pricing
		"is_price_edit", "price", "trade_price", "is_sales", "buying_price", "properties_price", "trade_properties_price", "is_sales",
		"sales_price", "trade_sales",	"discount_percent",
		// descriptions
		"features", "short_description", "full_description",
		// meta
		"meta_title", "meta_keywords", "meta_description",
		// images
		"tiny_image", "tiny_image_alt", "small_image", "small_image_alt", "big_image", "big_image_alt", "super_image",
		// appearance
		"template_name", "hide_add_list", "hide_add_details", "hide_add_table", "hide_add_grid", "preview_url", "preview_width", "preview_height",
		// stock
		"use_stock_level", "stock_level", "hide_out_of_stock", "disable_out_of_stock", "min_quantity", "max_quantity", "quantity_increment",
		// shipping
		"is_shipping_free", "shipping_cost", "shipping_in_stock", "shipping_out_stock", "shipping_rule_id",

		// Downloadable / Software Options
		"downloadable", "download_show_terms", "download_terms_text", "generate_serial", "serial_period", "activations_number", "predefined_serials",

		// Special Offer
		"is_special_offer", "special_offer",

		// Notifications
		"mail_notify", "mail_to", "mail_from", "mail_cc", "mail_bcc", "mail_reply_to", "mail_return_path", "mail_subject",
		"mail_type", "mail_body", "sms_notify", "sms_recipient", "sms_originator", "sms_message",
		// Rating Notes
		"votes", "points", "notes",	"buy_link"
	);
	for ($i = 0; $i < sizeof($product_fields); $i++) {
		$field_name = $product_fields[$i];
		$r->add_checkbox("show_" . $field_name, INTEGER);
		$r->add_checkbox($field_name . "_required", INTEGER);
	}

	$r->add_radio("features_editor", INTEGER, $html_editors);
	$r->add_radio("short_description_editor", INTEGER, $html_editors);
	$r->add_radio("full_description_editor", INTEGER, $html_editors);
	$r->add_radio("special_offer_editor", INTEGER, $html_editors);
	$r->add_radio("notes_editor", INTEGER, $html_editors);

	// options fields
	$option_fields = array(
		"allow_options",
		"show_option_property_style",
		"option_on_list_default", "option_on_details_default", "option_on_table_default", "option_on_grid_default", "option_on_second_default", "option_on_checkout_default",
		"show_option_on_list", "show_option_on_details", "show_option_on_table", "show_option_on_grid", "show_option_on_list", "show_option_on_second", "show_option_on_checkout",
		"show_option_control_style", "show_option_start_html", "show_option_middle_html",
		"show_option_before_control_html", "show_option_after_control_html", "show_option_end_html",
		"show_option_onchange_code", "show_option_onclick_code", "show_option_control_code",
		"show_option_values", "show_option_value_prices", "show_option_value_trade_prices", 
		"show_option_value_weight", "show_option_value_levels", "show_option_value_downloads",
	);
	for ($i = 0; $i < sizeof($option_fields); $i++) {
		$field_name = $option_fields[$i];
		$r->add_checkbox($field_name, INTEGER);
	}

	// subcomponents fields
	$component_fields = array(
		"allow_subcomponents", "allow_subcomponents_selection",
		"show_component_property_style",
		"component_on_list_default", "component_on_details_default", "component_on_table_default",
		"component_on_grid_default", "component_on_second_default", "component_on_checkout_default",
		"show_component_on_list", "show_component_on_details", "show_component_on_table",
		"show_component_on_grid","show_component_on_second", "show_component_on_checkout",
		"show_component_control_style", "show_component_start_html", "show_component_middle_html",
		"show_component_before_control_html", "show_component_after_control_html", "show_component_end_html",
		"show_component_onchange_code", "show_component_onclick_code", "show_component_control_code",	
		"show_component_trade_price"
	);
	for ($i = 0; $i < sizeof($component_fields); $i++) {
		$field_name = $component_fields[$i];
		$r->add_checkbox($field_name, INTEGER);
	}

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

	$r->add_checkbox("user_allow_select_sites", INTEGER);
	$r->add_checkbox("user_allow_select_user_types", INTEGER);
	
	$r->get_form_values();

	$param_site_id = get_session("session_site_id");
	$operation = get_param("operation");
	$return_page = get_param("rp");
	if (!strlen($return_page)) {
		$return_page = "admin_user_types.php";
	}
	$t->set_var("rp", htmlspecialchars($return_page));

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
			$sql  = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
			$sql .= " AND site_id=" . $db->tosql($param_site_id, INTEGER);
			$db->query($sql);
			foreach($r->parameters as $key => $value)
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
	else // get user_product settings
	{
		foreach($r->parameters as $key => $value)
		{
			$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT) . " AND setting_name='" . $key . "'";
			$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
			$sql .= " ORDER BY site_id DESC ";
			$r->set_value($key, get_db_value($sql));
		}
	}

	$r->set_parameters();
	$t->set_var("type_id", htmlspecialchars($type_id));

	// set styles for tabs
	$tabs = array("general" => PROD_GENERAL_TAB, "fields" => PROD_FIELDS_TAB,
		"options" => OPTIONS_AND_COMPONENTS_MSG, "notifications" => PROD_NOTIFICATION_TAB, "additional" => PROD_ADDITIONAL_OPTIONS_MSG);
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
		$sites_all = 0;
		$sql = " SELECT sites_all FROM " . $table_prefix . "user_types WHERE type_id=" . $db->tosql($type_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$sites_all = $db->f("sites_all");
		}
		
		$sql  = " SELECT s.site_id, s.site_name FROM " . $table_prefix . "sites AS s ";
		if (!$sites_all) {
			$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites AS t ON s.site_id=t.site_id ";
			$sql .= " WHERE t.type_id=" . $db->tosql($type_id, INTEGER);	
		}
		$sql .= " ORDER BY s.site_id ";
		$sites   = get_db_values($sql, "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

?>