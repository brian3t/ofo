<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_property.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");

	$payment_id    = get_param("payment_id");
	$param_site_id = get_param("param_site_id");
	if (!$param_site_id) {
		$param_site_id = get_session("session_site_id");
	}
	if ($payment_id > 0) {

		check_admin_security("payment_systems");
		$setting_type = "credit_card_info_" . $payment_id;
		$sql = " SELECT payment_name FROM " . $table_prefix . "payment_systems WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$payment_name = get_translation($db->f("payment_name"), $language_code);
		} else {
			header ("Location: admin_payment_systems.php");
			exit;
		}
	} else {
		check_admin_security("order_profile");
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_order_property.html");
	$t->set_var("admin_order_info_href", "admin_order_info.php");
	$t->set_var("admin_order_property_href", "admin_order_property.php");
	$t->set_var("admin_payment_systems_href", "admin_payment_systems.php");
	$t->set_var("admin_payment_system_href", "admin_payment_system.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CUSTOM_FIELDS_MSG, CONFIRM_DELETE_MSG));	

	$controls = 
		array(			
			array("", ""),  
			array("CHECKBOXLIST", CHECKBOXLIST_MSG),
			array("LABEL",        LABEL_MSG),
			array("LISTBOX",      LISTBOX_MSG),
			array("RADIOBUTTON",  RADIOBUTTON_MSG),
			array("TEXTAREA",     TEXTAREA_MSG),
			array("TEXTBOX",      TEXTBOX_MSG)
		);

	if ($payment_id > 0) {
		$types = 
			array(			
				array("", ""),
				array("0", HIDDEN_MSG),
				array("4", ACTIVE_MSG)
			);
	} else {
		$types = 
			array(			
				array("", ""),
				array("0", HIDDEN_MSG),
				array("1", ADMIN_CART_MSG),
				array("2", PERSONAL_DETAILS_MSG),
				array("3", DELIVERY_DETAILS_MSG)
			);
	}

	$property_show =
		array(
			array(0, FOR_ALL_ORDERS_MSG),
			array(1, ONLY_WEB_ORDERS_MSG),
			array(2, ONLY_FOR_CALL_CENTRE_MSG)
		);

	// set up html form parameters
	$r = new VA_Record($table_prefix . "order_custom_properties");
	$r->add_where("property_id", INTEGER);
	$r->change_property("property_id", USE_IN_INSERT, true);
	$r->add_textbox("payment_id", INTEGER);
	$r->add_textbox("property_order", INTEGER, OPTION_ORDER_MSG);
	$r->change_property("property_order", REQUIRED, true);
	$r->add_textbox("property_name", TEXT, OPTION_NAME_MSG);
	$r->change_property("property_name", REQUIRED, true);
	$r->add_textbox("property_description", TEXT, OPTION_TEXT_MSG);
	$r->add_textbox("default_value", TEXT, DEFAULT_VALUE_MSG);
	$r->add_textbox("property_style", TEXT);
	$r->add_textbox("control_style", TEXT);
	$r->add_select("property_type", INTEGER, $types, OPTION_TYPE_MSG);
	$r->change_property("property_type", REQUIRED, true);
	// multisites
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ",null);
		$r->add_select("site_id", INTEGER, $sites, ADMIN_SITE_MSG);
		$r->change_property("site_id", REQUIRED, true);
	} else {
		$r->add_textbox("site_id", INTEGER);
	}
	$r->add_select("control_type", TEXT, $controls, OPTION_CONTROL_MSG);
	$r->change_property("control_type", REQUIRED, true);
	$r->add_radio("property_show", INTEGER, $property_show);
	$r->add_checkbox("required", INTEGER);
	$r->add_checkbox("tax_free", INTEGER);
	if ($payment_id > 0) {
		$r->change_property("tax_free", SHOW, false);
	}

	$r->add_textbox("before_name_html", TEXT);
	$r->add_textbox("after_name_html", TEXT);
	$r->add_textbox("before_control_html", TEXT);
	$r->add_textbox("after_control_html", TEXT);
	$r->add_textbox("control_code", TEXT);
	$r->add_textbox("onchange_code", TEXT);
	$r->add_textbox("onclick_code", TEXT);

	$r->add_textbox("validation_regexp", TEXT);
	$r->add_textbox("regexp_error", TEXT);
	$r->add_textbox("options_values_sql", TEXT);

	$r->get_form_values();
	if ($payment_id > 0) {
		$r->set_value("payment_id", $payment_id);
	} else {
		$r->set_value("payment_id", 0);
	}
	if ($sitelist && $param_site_id) {
		$r->set_value("site_id", $param_site_id);
	} else {
		$r->set_value("site_id", 1);
	}
	

	$ipv = new VA_Record($table_prefix . "order_custom_values", "properties");
	$ipv->add_where("property_value_id", INTEGER);
	$ipv->add_hidden("property_id", INTEGER);
	$ipv->change_property("property_id", USE_IN_INSERT, true);
	$ipv->add_textbox("property_value", TEXT, OPTION_VALUE_MSG);
	$ipv->change_property("property_value", REQUIRED, true);
	$ipv->add_textbox("property_price", NUMBER, OPTION_PRICE_MSG);
	if ($payment_id > 0) {
		$ipv->change_property("property_price", SHOW, false);
	}
	$ipv->add_textbox("property_weight", NUMBER, OPTION_WEIGHT_MSG);
	$ipv->add_checkbox("hide_value", INTEGER);
	$ipv->add_checkbox("is_default_value", INTEGER);
	
	$property_id = get_param("property_id");

	$more_properties = get_param("more_properties");
	$number_properties = get_param("number_properties");

	$eg = new VA_EditGrid($ipv, "properties");
	$eg->get_form_values($number_properties);

	$operation = get_param("operation");

	if ($payment_id > 0) {
		$return_page = "admin_credit_card_info.php?payment_id=" . $payment_id;
	} else {
		$return_page = "admin_order_info.php?tab=custom_fields";
	}

	if (strlen($operation) && !$more_properties)
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		elseif ($operation == "delete" && $property_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "order_custom_properties WHERE property_id=" . $db->tosql($property_id, INTEGER));		
			$db->query("DELETE FROM " . $table_prefix . "order_custom_values WHERE property_id=" . $db->tosql($property_id, INTEGER));		
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $r->validate();
		$is_valid = ($eg->validate() && $is_valid); 

		if ($is_valid)
		{
			if (strlen($property_id))
			{
				$r->update_record();
				$eg->set_values("property_id", $property_id);
				$eg->update_all($number_properties);
			}
			else
			{
				$db->query("SELECT MAX(property_id) FROM " . $table_prefix . "order_custom_properties");
				$db->next_record();
				$property_id = $db->f(0) + 1;
				$r->set_value("property_id", $property_id);
				$r->insert_record();
				$eg->set_values("property_id", $property_id);
				$eg->insert_all($number_properties);
			}
			header("Location: " . $return_page);
			exit;
		}
	}
	elseif (strlen($property_id) && !$more_properties)
	{
		$r->get_db_values();
		$eg->set_value("property_id", $property_id);
		$eg->change_property("property_value_id", USE_IN_SELECT, true);
		$eg->change_property("property_value_id", USE_IN_WHERE, false);
		$eg->change_property("property_id", USE_IN_WHERE, true);
		$eg->change_property("property_id", USE_IN_SELECT, true);
		$number_properties = $eg->get_db_values();
		if ($number_properties == 0)
			$number_properties = 5;
	}
	elseif ($more_properties)
	{
		$number_properties += 5;
	}
	else // set default values
	{
		$sql  = " SELECT MAX(property_order) FROM " . $table_prefix . "order_custom_properties ";
		$property_order = get_db_value($sql);
		$property_order = ($property_order) ? ($property_order + 1) : 1;
		$r->set_value("property_order", $property_order);

		$number_properties = 5;
	}

/*
	if (strlen($errors))
	{
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	}
	else
	{
		$t->set_var("errors", "");
	}
*/

	$t->set_var("number_properties", $number_properties);

	$eg->set_parameters_all($number_properties);
	$r->set_parameters();

	if (strlen($property_id))	
	{
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	}
	else
	{
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");	
	}

	if ($r->get_value("payment_id") > 0) {
		$t->set_var("price_title",  "");
		$t->set_var("payment_id",   $payment_id);
		$t->set_var("payment_name", $payment_name);
		$t->parse("payment_breadcrumb", false);
	} else {
		$t->parse("price_title",  false);
		$t->parse("order_breadcrumb", false);
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	if ($sitelist) {
		$t->parse('sitelist');
	}
	
	$t->pparse("main");

?>