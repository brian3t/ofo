<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_registration_property.php                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");
	
	check_admin_security("admin_registration");
	
	$param_site_id = get_param("param_site_id");
	if (!$param_site_id) {
		$param_site_id = get_session("session_site_id");
	}
	$return_page = "admin_registration_settings.php?tab=custom_fields";
			
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_registration_property.html");	
	$t->set_var("admin_registration_href", "admin_registration.php");
	$t->set_var("admin_registration_settings_href", "admin_registration_settings.php");
	
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

	$property_show =
		array(
			array(0, DONT_SHOW_MSG),
			array(1, FOR_ALL_USERS_MSG),
			array(2, NON_REGISTERED_USERS_MSG),
			array(3, REGISTERED_USERS_ONLY_MSG)
			);

	$r = new VA_Record($table_prefix . "registration_custom_properties");
	$r->return_page = $return_page;
	if (get_param("apply")) {
		$r->redirect = false;
	}
	$r->add_where("property_id", INTEGER);
	$r->change_property("property_id", USE_IN_INSERT, true);
	$r->add_textbox("property_order", INTEGER, FIELD_ORDER_MSG);
	$r->change_property("property_order", REQUIRED, true);
	$r->add_textbox("property_name", TEXT, FIELD_NAME_MSG);
	$r->change_property("property_name", REQUIRED, true);
	$r->add_textbox("property_description", TEXT, FIELD_TEXT_MSG);
	$r->add_textbox("default_value", TEXT, DEFAULT_VALUE_MSG);
	$r->add_textbox("property_style", TEXT);
	$r->add_textbox("control_style", TEXT);
	$r->add_select("control_type", TEXT, $controls, FIELD_CONTROL_MSG);
	$r->change_property("control_type", REQUIRED, true);
	$r->add_radio("property_show", INTEGER, $property_show, SHOW_FIELD_MSG);
	$r->change_property("property_show", REQUIRED, true);
	$r->add_checkbox("required", INTEGER);
	
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
	$r->add_textbox("site_id", INTEGER);
			
	$ipv = new VA_Record($table_prefix . "registration_custom_values", "properties");
	$ipv->add_where("property_value_id", INTEGER);
	$ipv->add_hidden("property_id", INTEGER);
	$ipv->change_property("property_id", USE_IN_INSERT, true);
	$ipv->add_textbox("property_value", TEXT, OPTION_VALUE_MSG);
	$ipv->change_property("property_value", REQUIRED, true);
	$ipv->add_checkbox("hide_value", INTEGER);
	$ipv->add_checkbox("is_default_value", INTEGER);
	
	$r->set_event(AFTER_SELECT,   "after_select");
	$r->set_event(BEFORE_SHOW,   "before_show");
	$r->set_event(BEFORE_DELETE, "before_delete");
	$r->set_event(BEFORE_VALIDATE, "before_validate");
	$r->set_event(AFTER_UPDATE, "after_update");
	$r->set_event(AFTER_INSERT, "after_insert");
	$r->set_event(AFTER_DEFAULT, "after_default");	
	
	$more_properties   = get_param("more_properties");
	$number_properties = get_param("number_properties");
	if ($more_properties) {
		$number_properties += 5;
	}
	
	$eg = new VA_EditGrid($ipv, "properties");
	$eg->get_form_values($number_properties);
	
	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ",null);
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse('sitelist');
	}
		
	$t->set_var("number_properties", $number_properties);
		
	$t->pparse("main");
	
	function after_select() {
		global $r, $eg, $number_properties;
		$eg->set_value("property_id", $r->get_value("property_id"));
		$eg->change_property("property_value_id", USE_IN_SELECT, true);
		$eg->change_property("property_value_id", USE_IN_WHERE, false);
		$eg->change_property("property_id", USE_IN_WHERE, true);
		$eg->change_property("property_id", USE_IN_SELECT, true);
		$number_properties = $eg->get_db_values();
		if ($number_properties == 0)
			$number_properties = 5;
		
	}
	function before_show() {
		global $r,  $param_site_id, $eg, $number_properties;
		if ($r->get_value("site_id")) {
			$param_site_id = $r->get_value("site_id");
		}
		$eg->set_parameters_all($number_properties);
	}
	
	function before_delete() {
		global $db, $table_prefix, $r;
		$property_id = $r->get_value("property_id");
		if ($property_id) {
			$db->query("DELETE FROM " . $table_prefix . "registration_custom_properties WHERE property_id=" . $db->tosql($property_id, INTEGER));		
			$db->query("DELETE FROM " . $table_prefix . "registration_custom_values WHERE property_id=" . $db->tosql($property_id, INTEGER));
		}
		return true;		
	}
	
	function before_validate() {
		global $table_prefix, $r, $sitelist, $param_site_id;
		if ($sitelist && $param_site_id) {
			$r->set_value("site_id", $param_site_id);
		} else {
			$r->set_value("site_id", 1);
		}
		$property_id = $r->get_value("property_id");
		if (!$property_id) {
			$property_id = get_db_value("SELECT MAX(property_id) FROM " . $table_prefix . "registration_custom_properties") + 1;
			$r->set_value("property_id", $property_id);
		}
	}
	
	function after_update() {
		global $r, $eg, $number_properties;
		$property_id = $r->get_value("property_id");
		$eg->set_values("property_id", $property_id);
		$eg->update_all($number_properties);
	}
	
	function after_insert() {
		global $r, $eg, $number_properties;
		$property_id = $r->get_value("property_id");
		$eg->set_values("property_id", $property_id);
		$eg->insert_all($number_properties);
	}
	
	function after_default() {
		global $table_prefix, $r, $number_properties;
		$sql  = " SELECT MAX(property_order) FROM " . $table_prefix . "registration_custom_properties ";
		$property_order = get_db_value($sql);
		$property_order = ($property_order) ? ($property_order + 1) : 1;
		$r->set_value("property_order", $property_order);
		$number_properties = 5;
	}
	
?>