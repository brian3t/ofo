<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_edit.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/parameters.php");
	include_once($root_folder_path . "includes/shopping_cart.php");
	include_once($root_folder_path . "includes/order_items.php");
	include_once($root_folder_path . "includes/order_links.php");
	include_once($root_folder_path . "includes/friendly_functions.php");
	include_once($root_folder_path . "messages/" . $language_code . "/download_messages.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");
	$permissions = get_permissions();

	$order_id = get_param("order_id");
	$p_d = get_param("p_d");
	$operation = get_param("operation");

	$r = new VA_Record($table_prefix . "orders");
	$r->return_page = $order_details_site_url . "admin_order.php?order_id=".$order_id;

	$r->add_where("order_id", INTEGER);
	$r->change_property("order_id", USE_IN_SELECT, true);
	$r->change_property("order_id", USE_IN_UPDATE, false);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->add_textbox("date_modified", DATETIME);

	if ($operation == "cancel"){
		header("Location: " . $r->return_page);
		exit;
	}
	
	$html_editor = get_setting_value($settings, "html_editor", 1);
	$site_url = get_setting_value($settings, "site_url", "");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_order_edit.html");
	$site_url_path = $settings["site_url"] ? $settings["site_url"] : "../";
	$t->set_var("css_file", $site_url_path . "styles/" . $settings["style_name"] . ".css");
	$t->set_var("html_editor", $html_editor);
	$t->set_var("site_url", $site_url);
	$t->set_var("p_d", $p_d);

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_order_edit_href",  $order_details_site_url . "admin_order_edit.php");
	
	
	$sql  = " SELECT site_id FROM " . $table_prefix . "orders ";
	$sql .= " WHERE order_id=" . $db->tosql($order_id,INTEGER); 
	$order_site_id = get_db_value($sql);
		
	$order_profile = array();
	if ($p_d != 4){
		$sql = "SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='order_info'";
		if ($multisites_version) {
			$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($order_site_id,INTEGER, true, false) . ") ";
			$sql .= " ORDER BY site_id ASC ";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$order_profile[$db->f("setting_name")] = $db->f("setting_value");
		}
	} else {
		$payment_id = get_db_value("SELECT payment_id FROM " . $table_prefix . "orders WHERE order_id = " . $order_id);
		$setting_type = "credit_card_info_" . $payment_id;
		$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
		if ($multisites_version) {
			$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($order_site_id,INTEGER, true, false) . ") ";
			$sql .= " ORDER BY site_id ASC ";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$order_profile[$db->f("setting_name")] = $db->f("setting_value");
		}
	}
	$cc_number_security = get_setting_value($order_profile, "cc_number_security", 0);
	
	$cc_code_security = get_setting_value($order_profile, "cc_code_security", 0);

	$sections = array();
	$sql = "SELECT section_id, section_name FROM " . $table_prefix . "user_profile_sections WHERE is_active=1 ORDER BY section_order, section_id ";
	$db->query($sql);
	while ($db->next_record()) {
		$section_id = $db->f("section_id");
		$section_name = get_translation($db->f("section_name"));
		$sections[$section_id] = $section_name;
	}
	
	$pp = array(); $pn = 0;

	// prepare custom options 
	$sql  = " SELECT * "; 
	$sql .= " FROM " . $table_prefix . "order_custom_properties "; 
	$sql .= " WHERE property_type = " . $db->tosql($p_d,INTEGER,true,false); 
	if ($p_d == 4) {
		$sql .= " AND payment_id=" . $db->tosql($payment_id, INTEGER); 
	}
	$sql .= " AND site_id = " . $db->tosql($order_site_id, INTEGER);
	$sql .= " ORDER BY property_order, property_id "; 
		
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$pp[$pn]["property_id"] = $db->f("property_id");
			$pp[$pn]["property_order"] = $db->f("property_order");
			$pp[$pn]["property_name"] = $db->f("property_name");
			$pp[$pn]["payment_id"] = $db->f("payment_id");
			$pp[$pn]["property_description"] = $db->f("property_description");
			$pp[$pn]["default_value"] = $db->f("default_value");
			$pp[$pn]["property_style"] = $db->f("property_style");
			$pp[$pn]["section_id"] = $db->f("property_type");
			$pp[$pn]["control_type"] = $db->f("control_type");
			$pp[$pn]["control_style"] = $db->f("control_style");
			$pp[$pn]["control_code"] = $db->f("control_code");
			$pp[$pn]["onchange_code"] = $db->f("onchange_code");
			$pp[$pn]["onclick_code"] = $db->f("onclick_code");
			$pp[$pn]["required"] = $db->f("required");
			$pp[$pn]["before_name_html"] = $db->f("before_name_html");
			$pp[$pn]["after_name_html"] = $db->f("after_name_html");
			$pp[$pn]["before_control_html"] = $db->f("before_control_html");
			$pp[$pn]["after_control_html"] = $db->f("after_control_html");
			$pp[$pn]["validation_regexp"] = $db->f("validation_regexp");
			$pp[$pn]["regexp_error"] = $db->f("regexp_error");
			$pp[$pn]["options_values_sql"] = $db->f("options_values_sql");

			$pn++;
		} while ($db->next_record());
	}

	$login_params = array();
	$companies = get_db_values("SELECT company_id,company_name FROM " . $table_prefix . "companies ", array(array("", SELECT_COMPANY_MSG)));
	$states = get_db_values("SELECT state_id,state_name FROM " . $table_prefix . "states ORDER BY state_name ", array(array(0, SELECT_STATE_MSG)));
	$countries = get_db_values("SELECT country_id,country_name FROM " . $table_prefix . "countries ORDER BY country_order, country_name ", array(array("", SELECT_COUNTRY_MSG)));

	// add controls by sections
	foreach ($sections as $section_id => $section_name) 
	{
		if ($p_d == 2) {
			$r->add_textbox("name", TEXT, $section_name.": ".NAME_MSG);
			$r->change_property("name", USE_SQL_NULL, false);
			$r->add_textbox("first_name", TEXT, $section_name.": ".FIRST_NAME_FIELD);
			$r->change_property("first_name", USE_SQL_NULL, false);
			$r->add_textbox("last_name", TEXT, $section_name.": ".LAST_NAME_FIELD);
			$r->change_property("last_name", USE_SQL_NULL, false);
			$r->add_select("company_id", INTEGER, $companies, $section_name.": ".COMPANY_SELECT_FIELD);
			$r->add_textbox("company_name", TEXT, $section_name.": ".COMPANY_NAME_FIELD);
			$r->add_textbox("email", TEXT, $section_name.": ".EMAIL_FIELD);
			$r->change_property("email", USE_SQL_NULL, false);
			$r->change_property("email", REGEXP_MASK, EMAIL_REGEXP);
			$r->change_property("email", UNIQUE, false);
			$r->add_textbox("address1", TEXT, $section_name.": ".STREET_FIRST_FIELD);
			$r->add_textbox("address2", TEXT, $section_name.": ".STREET_SECOND_FIELD);
			$r->add_textbox("city", TEXT, $section_name.": ".CITY_FIELD);
			$r->add_textbox("province", TEXT, $section_name.": ".PROVINCE_FIELD);
			$r->add_select("state_id", INTEGER, $states, $section_name.": ".STATE_FIELD);
			$r->add_textbox("state_code", TEXT);
			$r->add_textbox("zip", TEXT, $section_name.": ".ZIP_FIELD);
			$r->add_select("country_id", INTEGER, $countries, $section_name.": ".COUNTRY_FIELD);
			$r->add_textbox("country_code", TEXT);
			$r->add_textbox("phone", TEXT);//, PHONE_FIELD);
			$r->add_textbox("daytime_phone", TEXT, $section_name.": ".DAYTIME_PHONE_FIELD);
			$r->add_textbox("evening_phone", TEXT, $section_name.": ".EVENING_PHONE_FIELD);
			$r->add_textbox("cell_phone", TEXT, $section_name.": ".CELL_PHONE_FIELD);
			$r->add_textbox("fax", TEXT, $section_name.": ".FAX_FIELD);
		} elseif ($p_d == 3) {
			$r->add_textbox("delivery_name", TEXT, $section_name.": ". NAME_MSG);
			$r->add_textbox("delivery_first_name", TEXT, $section_name.": ". FIRST_NAME_FIELD);
			$r->add_textbox("delivery_last_name", TEXT, $section_name.": ". LAST_NAME_FIELD);
			$r->add_select("delivery_company_id", INTEGER, $companies, $section_name.": ". COMPANY_SELECT_FIELD);
			$r->add_textbox("delivery_company_name", TEXT, $section_name.": ". COMPANY_NAME_FIELD);
			$r->add_textbox("delivery_email", TEXT, $section_name.": ". EMAIL_FIELD);
			$r->change_property("delivery_email", REGEXP_MASK, EMAIL_REGEXP);
			$r->add_textbox("delivery_address1", TEXT, $section_name.": ". STREET_FIRST_FIELD);
			$r->add_textbox("delivery_address2", TEXT, $section_name.": ". STREET_SECOND_FIELD);
			$r->add_textbox("delivery_city", TEXT, $section_name.": ". CITY_FIELD);
			$r->add_textbox("delivery_province", TEXT, $section_name.": ". PROVINCE_FIELD);
			$r->add_select("delivery_state_id", INTEGER, $states, $section_name.": ". STATE_FIELD);
			$r->add_textbox("delivery_state_code", TEXT);
			$r->add_textbox("delivery_zip", TEXT, $section_name.": ". ZIP_FIELD);
			$r->add_select("delivery_country_id", INTEGER, $countries, $section_name.": ". COUNTRY_FIELD);
			$r->add_textbox("delivery_country_code", TEXT);
			$r->add_textbox("delivery_phone", TEXT, $section_name.": ". PHONE_FIELD);
			$r->add_textbox("delivery_daytime_phone", TEXT, $section_name.": ". DAYTIME_PHONE_FIELD);
			$r->add_textbox("delivery_evening_phone", TEXT, $section_name.": ". EVENING_PHONE_FIELD);
			$r->add_textbox("delivery_cell_phone", TEXT, $section_name.": ". CELL_PHONE_FIELD);
			$r->add_textbox("delivery_fax", TEXT, $section_name.": ". FAX_FIELD);
		} elseif ($p_d == 4) {
			$r->add_textbox("cc_name", TEXT, CC_NAME_FIELD);
			
			$r->add_textbox("cc_first_name", TEXT, CC_FIRST_NAME_FIELD);
			$r->add_textbox("cc_last_name", TEXT, CC_LAST_NAME_FIELD);
				$r->add_textbox("cc_number", TEXT, CC_NUMBER_FIELD);
				$r->parameters["cc_number"][MIN_LENGTH] = 10;
			if ($cc_number_security != 2){
				$r->change_property("cc_number", SHOW, false);
				$r->change_property("cc_number", REQUIRED, false);
				$r->change_property("cc_number", USE_IN_UPDATE, false);
			}
			$r->add_textbox("cc_start_date", DATETIME, CC_START_DATE_FIELD);
			$r->change_property("cc_start_date", VALUE_MASK, array("MM", " / ", "YYYY"));
			$r->add_textbox("cc_expiry_date", DATETIME, CC_EXPIRY_DATE_FIELD);
			$r->change_property("cc_expiry_date", VALUE_MASK, array("MM", " / ", "YYYY"));
			$credit_cards = get_db_values("SELECT credit_card_id, credit_card_name FROM " . $table_prefix . "credit_cards", array(array("", PLEASE_CHOOSE_MSG)));
			$r->add_select("cc_type", INTEGER, $credit_cards, CC_TYPE_FIELD);
			$issue_numbers = get_db_values("SELECT issue_number AS issue_value, issue_number AS issue_description FROM " . $table_prefix . "issue_numbers", array(array("", NOT_AVAILABLE_MSG)));
			$r->add_select("cc_issue_number", INTEGER, $issue_numbers, CC_ISSUE_NUMBER_FIELD);
			$r->add_textbox("cc_security_code", TEXT, CC_SECURITY_CODE_FIELD);
			if ($cc_code_security != 2){
				$r->change_property("cc_security_code", SHOW, false);
				$r->change_property("cc_security_code", REQUIRED, false);
				$r->change_property("cc_security_code", USE_IN_UPDATE, false);
			}
			$r->add_textbox("pay_without_cc", TEXT, PAY_WITHOUT_CC_FIELD);
			
			// 3D fields 
			$r->add_textbox("secure_3d_check", TEXT);
			$r->add_textbox("secure_3d_status", TEXT);
			$r->add_textbox("secure_3d_md", TEXT);
			$r->add_textbox("secure_3d_xid", TEXT);
			$r->add_textbox("secure_3d_eci", TEXT);
			$r->add_textbox("secure_3d_cavv", TEXT);
		}

		foreach ($pp as $id => $pp_row) {
			if ($pp_row["section_id"] == $section_id && $p_d == $section_id) {
				$control_type = $pp_row["control_type"];
				$param_name = "pp_" . $pp_row["property_id"];
				$param_title = $pp_row["property_name"];

				if ($control_type == "CHECKBOXLIST") {
					$r->add_checkboxlist($param_name, TEXT, "", $section_name . ": " . $param_title);
				} elseif ($control_type == "RADIOBUTTON") {
					$r->add_radio($param_name, TEXT, "", $section_name . ": " . $param_title);
				} elseif ($control_type == "LISTBOX") {
					$r->add_select($param_name, TEXT, "", $section_name . ": " . $param_title);
				} else {
					$r->add_textbox($param_name, TEXT, $section_name . ": " . $param_title);
				}
				if ($control_type == "CHECKBOXLIST" || $control_type == "RADIOBUTTON" || $control_type == "LISTBOX") {
					if ($pp_row["options_values_sql"]) {
						$sql = $pp_row["options_values_sql"];
					} else {
						$sql  = " SELECT property_value_id, property_value FROM " . $table_prefix . "order_custom_values ";
						$sql .= " WHERE property_id=" . $db->tosql($pp_row["property_id"], INTEGER) . " AND hide_value=0";
						$sql .= " ORDER BY property_value_id ";
					}
					$r->change_property($param_name, VALUES_LIST, get_db_values($sql, ""));
				}
				if ($pp_row["required"] == 1 && $control_type != "LABEL") {
					$r->change_property($param_name, REQUIRED, true);
				}
				if ($pp_row["validation_regexp"]) {
					$r->change_property($param_name, REGEXP_MASK, $pp_row["validation_regexp"]);
					if ($pp_row["regexp_error"]) {
						$r->change_property($param_name, REGEXP_ERROR, $pp_row["regexp_error"]);
					}
				}
				$r->change_property($param_name, USE_IN_SELECT, false);
				$r->change_property($param_name, USE_IN_INSERT, false);
				$r->change_property($param_name, USE_IN_UPDATE, false);
			}
		}
	}

	$r->events[AFTER_UPDATE] = "update_order_properties";
	if ($p_d == 2) {
		$r->events[BEFORE_UPDATE] = "get_country_codes";		
	} elseif ($p_d == 3) {
		$r->events[BEFORE_UPDATE] = "get_delivery_codes";		
	} elseif ($p_d == 4) {
		$r->events[BEFORE_UPDATE] = "encrypt_update_cc_number";
	}
	$r->events[AFTER_REQUEST] = "set_order_fields";
	$r->events[AFTER_SELECT] = "get_additional_data";
	$r->events[BEFORE_SHOW] = "hide_custom_fields";
	$r->operations[UPDATE_ALLOWED] = 1;
	
	$personal_number = 0;
	$delivery_number = 0;
	for ($i = 0; $i < sizeof($parameters); $i++)
	{                                    
		$param_name = $parameters[$i];
		$delivery_param = "delivery_" . $parameters[$i];
		$show_personal = "show_" . $parameters[$i];
		$show_delivery = "show_delivery_" . $parameters[$i];

		if ($r->parameter_exists($param_name)) {
			$r->change_property($param_name, TRIM, true);
			if (isset($order_profile[$show_personal]) && $order_profile[$show_personal] == 1) {
				$personal_number++;
				if (isset($order_profile[$param_name . "_required"]) && $order_profile[$param_name . "_required"] == 1) {
					$r->parameters[$param_name][REQUIRED] = true;
				}
			} else {
				$r->parameters[$param_name][SHOW] = false;
			}
		}

		if ($r->parameter_exists($delivery_param)) {
			$r->change_property($delivery_param, TRIM, true);
			if (isset($order_profile[$show_delivery]) && $order_profile[$show_delivery] == 1) {
				$delivery_number++;
				if ($order_profile[$delivery_param . "_required"] == 1) {
					$r->parameters[$delivery_param][REQUIRED] = true;
				}
			} else {
				$r->parameters[$delivery_param][SHOW] = false;
			}
		}
	}

	if ($p_d == 4)
	{
		$cc_info = array();
		$setting_type = "credit_card_info_" . $payment_id;
		$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
		if ($multisites_version) {
			$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($order_site_id,INTEGER, true, false) . ") ";
			$sql .= " ORDER BY site_id ASC ";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$cc_info[$db->f("setting_name")] = $db->f("setting_value");
		}
		
		$parameters_number = 0;
		for ($i = 0; $i < sizeof($cc_parameters); $i++)
		{            
			$show_param = "show_" . $cc_parameters[$i];
			if (isset($cc_info[$show_param]) && $cc_info[$show_param] == 1) {
				$parameters_number++;
				if ($cc_info[$cc_parameters[$i] . "_required"] == 1) {
					$r->parameters[$cc_parameters[$i]][REQUIRED] = true;
				}
			} else {
				$r->parameters[$cc_parameters[$i]][SHOW] = false;
			}
		}
	}

	$r->add_checkbox("same_as_personal", INTEGER);
	$r->change_property("same_as_personal", USE_IN_SELECT, false);
	$r->change_property("same_as_personal", USE_IN_INSERT, false);
	$r->change_property("same_as_personal", USE_IN_UPDATE, false);
	if ($personal_number < 1 || $delivery_number < 1) {
		$r->parameters["same_as_personal"][SHOW] = false;
	}

	if ($p_d == 4) 
	{
		if (!isset($cc_number)){
			$cc_number = get_param('cc_number');
		}

		$sql = "SELECT cc_start_date, cc_expiry_date FROM " . $table_prefix . "orders WHERE order_id=".$order_id;
		$db->query($sql);
		if ($db->next_record()){
			$cc_start_date = $db->f('cc_start_date',DATETIME);
			$cc_expiry_date = $db->f('cc_expiry_date',DATETIME);
		}
		
		$cc_start_year   = get_param("cc_start_year");
		$cc_start_month  = get_param("cc_start_month");
		$cc_expiry_year  = get_param("cc_expiry_year");
		$cc_expiry_month = get_param("cc_expiry_month");
		
		if (count($cc_start_date) == 6 && !strlen($operation)){
			$cc_start_year   = $cc_start_date[0];
			$cc_start_month  = $cc_start_date[1];
		}
		if (count($cc_expiry_date) == 6 && !strlen($operation)){
			$cc_expiry_year   = $cc_expiry_date[0];
			$cc_expiry_month  = $cc_expiry_date[1];
		}
		
		$current_date = va_time();
		$cc_start_years = get_db_values("SELECT start_year AS year_value, start_year AS year_description FROM " . $table_prefix . "cc_start_years", array(array("", YEAR_MSG)));
		if (sizeof($cc_start_years) < 2) {
			$cc_start_years = array(array("", YEAR_MSG));
			for ($y = 7; $y >= 0; $y--) {
				$cc_start_years[] = array($current_date[YEAR] - $y, $current_date[YEAR] - $y);
			}
		}
		$cc_expiry_years = get_db_values("SELECT expiry_year AS year_value, expiry_year AS year_description FROM " . $table_prefix . "cc_expiry_years", array(array("", YEAR_MSG)));
		if (sizeof($cc_expiry_years) < 2) {
			$cc_expiry_years = array(array("", YEAR_MSG));
			for ($y = 0; $y <= 7; $y++) {
				$cc_expiry_years[] = array($current_date[YEAR] + $y, $current_date[YEAR] + $y);
			}
		}
		
		if (strlen($cc_start_year)){
			$date_present = false;
			for ($y = 0; $y < count($cc_start_years); $y++) {
				if ($cc_start_year == $cc_start_years[$y][0]){
					$date_present = true;
				}
			}
			if (!$date_present){
				if ($cc_start_year < $cc_start_years[1][0]){
					for ($y=count($cc_start_years);$y>0;$y--){
						$cc_start_years[$y] = $cc_start_years[$y-1];
					}
					$cc_start_years[1] = array($cc_start_year,$cc_start_year);
				} else {
					$cc_start_years[count($cc_start_years)] = array($cc_start_year,$cc_start_year);
				}
			}
		}
		if (strlen($cc_expiry_year)){
			$date_present = FALSE;
			for ($y = 1; $y < count($cc_expiry_years); $y++){
				if ($cc_expiry_year == $cc_expiry_years[$y][0]){
					$date_present = TRUE;
				}
			}
			if (!$date_present){
				if ($cc_expiry_year < $cc_expiry_years[1][0]){
					for ($y=count($cc_expiry_years);$y>1;$y--){
						$cc_expiry_years[$y] = $cc_expiry_years[$y-1];
					}
					$cc_expiry_years[1] = array($cc_expiry_year,$cc_expiry_year);
				} else {
					$cc_expiry_years[count($cc_expiry_years)] = array($cc_expiry_year,$cc_expiry_year);
				}
			}
		}
		
		set_options($cc_start_years, $cc_start_year, "cc_start_year");
		set_options($cc_expiry_years, $cc_expiry_year, "cc_expiry_year");

		$cc_months = array_merge (array(array("", MONTH_MSG)), $months);
		set_options($cc_months, $cc_start_month, "cc_start_month");
		set_options($cc_months, $cc_expiry_month, "cc_expiry_month");
	}
	
	$r->process();

	$eol = get_eol();
	
	$properties_ids = "";
	
	foreach ($sections as $section_id => $section_name) {
		$t->set_var("profile_section", "");
		$t->set_var("profile_properties", "");
		$section_properties = 0;

		if ($section_id == 1 && $p_d == $section_id) {
			for ($i = 0; $i < sizeof($login_params); $i++)
			{                                    
				$param_name = $login_params[$i];
				if ($r->get_property_value($param_name, SHOW)) {
					$section_properties++;
					$t->copy_var($param_name . "_block", "profile_properties");
					$t->set_var($param_name . "_block", "");
				}
			}
			$t->set_var("LOGIN_INFO_MSG", $section_name);
			$t->parse_to("login_info", "profile_section");
		} elseif ($section_id == 2 && $p_d == $section_id) {
			for ($i = 0; $i < sizeof($parameters); $i++)
			{                                    
				$param_name = $parameters[$i];
				if ($r->get_property_value($param_name, SHOW)) {
					$section_properties++;
					$t->copy_var($param_name . "_block", "profile_properties");
					$t->set_var($param_name . "_block", "");
				}
			}
			$t->set_var("PERSONAL_DETAILS_MSG", $section_name);
			$t->parse_to("personal", "profile_section");
		} elseif ($section_id == 3 && $p_d == $section_id) {
			for ($i = 0; $i < sizeof($parameters); $i++)
			{                                    
				$param_name = "delivery_" . $parameters[$i];
				if ($r->get_property_value($param_name, SHOW)) {
					$section_properties++;
					$t->copy_var($param_name . "_block", "profile_properties");
					$t->set_var($param_name . "_block", "");
				}
			}
			$t->set_var("DELIVERY_DETAILS_MSG", $section_name);
			$t->parse_to("delivery", "profile_section");
		} elseif ($section_id == 4 && $p_d == $section_id) {
			for ($i = 0; $i < sizeof($additional_parameters); $i++)
			{                                    
				$param_name = $additional_parameters[$i];
				if ($r->get_property_value($param_name, SHOW) && $param_name != "is_hidden") {
					$section_properties++;
					$t->copy_var($param_name . "_block", "profile_properties");
					$t->set_var($param_name . "_block", "");
				}
			}
			$t->set_var("PAYMENT_DETAILS_MSG", $section_name);
			$t->parse_to("payment", "profile_section");
		} else {
			$t->set_var("section_name", $section_name);
			$t->parse("profile_section", false);
		}
		
		// show custom options 
		if (sizeof($pp) > 0) 
		{
			for ($pn = 0; $pn < sizeof($pp); $pn++) {
				if ($pp[$pn]["section_id"] == $section_id && ($pp[$pn]["payment_id"] == 0 || $pp[$pn]["payment_id"] == $payment_id) && $p_d == $section_id) {
					$section_properties++;
					$property_id = $pp[$pn]["property_id"];
					$param_name = "pp_" . $property_id;
					$property_order  = $pp[$pn]["property_order"];
					$property_name_initial = $pp[$pn]["property_name"];
					$property_name = get_translation($property_name_initial);
					$property_description = $pp[$pn]["property_description"];
					$default_value = $pp[$pn]["default_value"];
					$property_style = $pp[$pn]["property_style"];
					$control_type = $pp[$pn]["control_type"];
					$control_style = $pp[$pn]["control_style"];
					$property_required = $pp[$pn]["required"];
					$before_name_html = $pp[$pn]["before_name_html"];
					$after_name_html = $pp[$pn]["after_name_html"];
					$before_control_html = $pp[$pn]["before_control_html"];
					$after_control_html = $pp[$pn]["after_control_html"];
					$onchange_code = $pp[$pn]["onchange_code"];
					$onclick_code = $pp[$pn]["onclick_code"];
					$control_code = $pp[$pn]["control_code"];
					$validation_regexp = $pp[$pn]["validation_regexp"];
					$regexp_error = $pp[$pn]["regexp_error"];
					$options_values_sql = $pp[$pn]["options_values_sql"];
        
					if (strlen($properties_ids)) { $properties_ids .= ","; }
					$properties_ids .= $property_id;
        
					$property_control  = "";
					$property_control .= "<input type=\"hidden\" name=\"pp_name_" . $property_id . "\"";
					$property_control .= " value=\"" . strip_tags($property_name) . "\">";
					$property_control .= "<input type=\"hidden\" name=\"pp_required_" . $property_id . "\"";
					$property_control .= " value=\"" . intval($property_required) . "\">";
					$property_control .= "<input type=\"hidden\" name=\"pp_control_" . $property_id . "\"";
					$property_control .= " value=\"" . strtoupper($control_type) . "\">";
					
					if ($options_values_sql) {
						$sql = $options_values_sql;
					} else {
						$sql  = " SELECT * FROM " . $table_prefix . "order_custom_values ";
						$sql .= " WHERE property_id=" . $property_id . " AND hide_value=0";
						$sql .= " ORDER BY property_value_id ";
					}
					if (strtoupper($control_type) == "LISTBOX") 
					{
						$selected_value = $r->get_value($param_name);
						$properties_values = "<option value=\"\">" . SELECT_MSG . " " . $property_name . "</option>" . $eol;
						$db->query($sql);
						while ($db->next_record())
						{
							if ($options_values_sql) {
								$property_value_id = $db->f(0);
								$property_value = get_translation($db->f(1));
							} else {
								$property_value_id = $db->f("property_value_id");
								$property_value = get_translation($db->f("property_value"));
							} 
							$is_default_value = $db->f("is_default_value");
							$property_selected  = "";
							
							if ($selected_value == $property_value_id) {
								$property_selected  = "selected ";
							}
        
							$properties_values .= "<option " . $property_selected . "value=\"" . htmlspecialchars($property_value_id) . "\">";
							$properties_values .= htmlspecialchars($property_value);
							$properties_values .= "</option>" . $eol;
						}
						$property_control .= $before_control_html;
						$property_control .= "<select name=\"pp_" . $property_id . "\" ";
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code. "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						$property_control .= ">" . $properties_values . "</select>";
						$property_control .= $after_control_html;						
					} 
					elseif (strtoupper($control_type) == "RADIOBUTTON" || strtoupper($control_type) == "CHECKBOXLIST") 
					{
						$is_radio = (strtoupper($control_type) == "RADIOBUTTON");
        
						$selected_value = array();
						if ($is_radio) {
							$selected_value[] = $r->get_value($param_name);
						} else {
							$selected_value = $r->get_value($param_name);
						}
        
						$input_type = $is_radio ? "radio" : "checkbox";
						$property_control .= "<span";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						$property_control .= ">";
						$value_number = 0;
						$db->query($sql);
						while ($db->next_record())
						{
							$value_number++;
							if ($options_values_sql) {
								$property_value_id = $db->f(0);
								$property_value = get_translation($db->f(1));
							} else {
								$property_value_id = $db->f("property_value_id");
								$property_value = get_translation($db->f("property_value"));
							} 
							$is_default_value = $db->f("is_default_value");
							$property_checked = "";
							$property_control .= $before_control_html;
							if (is_array($selected_value) && in_array($property_value_id, $selected_value)) {
								$property_checked = "checked ";
							}

							$control_name = ($is_radio) ? ("pp_".$property_id) : ("pp_".$property_id."_".$value_number);
							$property_control .= "<input type=\"" . $input_type . "\" name=\"" . $control_name . "\" ". $property_checked;
							$property_control .= "value=\"" . htmlspecialchars($property_value_id) . "\" ";
							if ($onclick_code) {	
								$control_onclick_code = str_replace("{option_value}", $property_value, $onclick_code);
								$property_control .= " onClick=\"" . $control_onclick_code. "\""; 
							}
							if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
							if ($control_code) {	$property_control .= " " . $control_code . " "; }
							$property_control .= ">";
							$property_control .= $property_value;
							$property_control .= $after_control_html;
						}
						$property_control .= "</span>";
						if (!$is_radio) {
							$property_control .= "<input type=\"hidden\" name=\"pp_".$property_id."\" value=\"".$value_number."\">";
						}
					} 
					elseif (strtoupper($control_type) == "TEXTBOX") 
					{
						if (strlen($operation) || $order_id) {
							$control_value = $r->get_value($param_name);
						} else {
							$control_value = $default_value;
						}
						$property_control .= $before_control_html;
						$property_control .= "<input class=\"field\" type=\"text\" name=\"pp_" . $property_id . "\"";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						$property_control .= " value=\"". htmlspecialchars($control_value) . "\">";
						$property_control .= $after_control_html;
					} 
					elseif (strtoupper($control_type) == "TEXTAREA") 
					{
						if (strlen($operation) || $order_id) {
							$control_value = $r->get_value($param_name);
						} else {
							$control_value = $default_value;
						}
						$property_control .= $before_control_html;
						$property_control .= "<textarea  class=\"field\" name=\"pp_" . $property_id . "\"";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						$property_control .= ">". htmlspecialchars($control_value) ."</textarea>";
						$property_control .= $after_control_html;
					} 
					else 
					{
						$property_control .= $before_control_html;
						if ($property_required) {
							if (!strlen($property_description)){
								$property_description = $default_value;
							}
							$property_control .= "<input type=\"hidden\" name=\"pp_" . $property_id . "\" value=\"" . htmlspecialchars($property_description) . "\">";
						}
						$property_control .= "<span";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						$property_control .= ">" . get_translation($default_value) . "</span>";
						$property_control .= $after_control_html;

						$property_control .= "<input type=\"hidden\" name=\"pp_".$property_id."\" value='".$default_value."'>";

						$custom_options[$property_id] = array($property_order, $property_name_initial, $default_value);
					}
        
					$t->set_var("property_id", $property_id);
					$t->set_var("property_name", $before_name_html . $property_name . $after_name_html);
					$t->set_var("property_style", $property_style);
					$t->set_var("property_control", $property_control);
					if ($property_required) {
						$t->set_var("property_required", "*");
					} else {
						$t->set_var("property_required", "");
					}
        
					if ($p_d == 4) {
						$t->parse("payment_properties", true);
					} else {
						$t->parse("profile_properties", true);
					}
				}
			}
  
			$t->set_var("properties_ids", $properties_ids);
		}
		// end custom options

		if ($section_properties) {
			$t->parse("profile_sections", true);
		}
	}
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function get_additional_data()
	{
		global $r, $pp, $db, $table_prefix, $order_id, $operation, $p_d, $cc_number_security, $cc_code_security;
  
		$order_id = $r->get_value("order_id");
  
		$orders_properties = array();
		$sql  = " SELECT op.property_id, op.property_type, op.property_name, op.property_value, ";
		$sql .= " op.property_price, op.property_points_amount, op.tax_free, ocp.control_type ";
		$sql .= " FROM (" . $table_prefix . "orders_properties op ";
		$sql .= " INNER JOIN " . $table_prefix . "order_custom_properties ocp ON op.property_id=ocp.property_id)";
		$sql .= " WHERE op.order_id=" . $db->tosql($order_id, INTEGER);
		$sql .= " ORDER BY op.property_order, op.property_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$property_id   = $db->f("property_id");
			$property_type = $db->f("property_type");
			$property_name = $db->f("property_name");
			$property_value = $db->f("property_value");
			$property_price = $db->f("property_price");
			$property_points_amount = $db->f("property_points_amount");
			$property_tax_free = $db->f("tax_free");
			$control_type = $db->f("control_type");
			$param_name = "pp_" . $property_id;

			if ($r->parameter_exists($param_name)) {
				if (($control_type == "CHECKBOXLIST" ||  $control_type == "RADIOBUTTON" || $control_type == "LISTBOX") && !is_numeric($property_value)) {
					$property_value = explode(";", $property_value);
				} else {
					$property_value = array($property_value);
				}
				for ($op = 0; $op < sizeof($property_value); $op++) {
					if ($op > 0) {
						$property_price = 0; $property_points_amount = 0;
					}
					$option_value = $property_value[$op];
					$orders_properties[$property_id][] = array(
						"type" => $property_type, "name" => $property_name, "value" => $option_value, "price" => $property_price, 
						"points_amount" => $property_points_amount, "tax_free" => $property_tax_free, "control" => $control_type
					);
				}
			}
		}

		foreach ($orders_properties as $property_id => $property_values) 
		{
			$param_name = "pp_" . $property_id;
			foreach ($property_values as $option_id => $option_data) {
				$control_type = $option_data["control"];
				$option_value = $option_data["value"];
				// check value from the description
				if (($control_type == "CHECKBOXLIST" ||  $control_type == "RADIOBUTTON" || $control_type == "LISTBOX") && !is_numeric($option_value)) {
					$sql  = " SELECT property_value_id FROM " . $table_prefix . "order_custom_values ";
					$sql .= " WHERE property_value=" . $db->tosql(trim($option_value), TEXT);
					$db->query($sql);
					if ($db->next_record()) {
						$option_value = $db->f("property_value_id");
					}
				}
				$r->set_value($param_name, $option_value);
			}
		}

		if ($p_d == 4 && !strlen($operation)) 
		{
			$cc_number = $r->get_value("cc_number");
			if (!preg_match("/^[\d\s\*\-]+$/", $cc_number)) {
				$cc_number = va_decrypt($cc_number);
			}
			$r->set_value("cc_number", format_cc_number($cc_number));
			$cc_security_code = $r->get_value("cc_security_code");
			if (!preg_match("/^[\d]+$/", $cc_security_code)) {
				$cc_security_code = va_decrypt($cc_security_code);
			}
			$r->set_value("cc_security_code", $cc_security_code);
		}
	}

	function set_order_fields()
	{
		global $r, $db, $parameters, $order_profile, $table_prefix, $update_orders;
		global $cc_start_month, $cc_start_year, $cc_expiry_month, $cc_expiry_year, $p_d;

		$current_date = va_time();
		$order_ip = get_ip();
	
		$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
		$r->set_value("date_modified", va_time());

		if ($r->get_value("same_as_personal")) {
			for ($i = 0; $i < sizeof($parameters); $i++) { 
				$personal_param = "show_" . $parameters[$i];
				$delivery_param = "show_delivery_" . $parameters[$i];
				if (isset($order_profile[$delivery_param]) && isset($order_profile[$personal_param]) &&
					$order_profile[$delivery_param] == 1 && $order_profile[$personal_param] == 1) {
					$r->set_value("delivery_" . $parameters[$i], $r->get_value($parameters[$i]));
				}
			}
		}
		
		if ($p_d == 4) {
			if (strlen($cc_start_year) && strlen($cc_start_month)) {
				$r->set_value("cc_start_date", array($cc_start_year, $cc_start_month, 1, 0, 0, 0));
			}
			if (strlen($cc_expiry_year) && strlen($cc_expiry_month)) {
				$r->set_value("cc_expiry_date", array($cc_expiry_year, $cc_expiry_month, 1, 0, 0, 0));
			}
		}
	
	}

	function encrypt_update_cc_number()
	{
		global $r;
		global $cc_number_security,	$cc_code_security;
	
		if ($cc_number_security == 0) {
			$r->set_value("cc_number", "");
		} elseif ($cc_number_security > 0) {
			$r->set_value("cc_number", va_encrypt(ereg_replace("[^0-9\*]+", "",$r->get_value("cc_number"))));
		}
	
		if ($cc_code_security == 0) {
			$r->set_value("cc_security_code", "");
		} elseif ($cc_code_security > 0) {
			$r->set_value("cc_security_code", va_encrypt($r->get_value("cc_security_code")));
		}
	}
	
	function update_order_properties()
	{
		global $r, $pp, $db, $table_prefix, $order_id;
	
		$order_id = $r->get_value("order_id");
	
		foreach ($pp as $id => $data) {
			$property_id =$data["property_id"];
			$property_name =$data["property_name"];
			$property_type =$data["section_id"];
			$param_name = "pp_" . $property_id;
			$values = array();
			$control_type = $r->get_property_value($param_name, CONTROL_TYPE);
			if ($control_type == CHECKBOXLIST) {
				$values = $r->get_value($param_name);
			} else {
				$values[] = $r->get_value($param_name);
			}
			$sql  = " DELETE FROM " . $table_prefix . "orders_properties ";
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$sql .= " AND property_id=" . $db->tosql($property_id, INTEGER);
			$db->query($sql);
			if (is_array($values)) {
				for ($i = 0; $i < sizeof($values); $i++) {
					$property_value_id = ""; $property_value = ""; $property_price = 0;
					if ($control_type == CHECKBOXLIST || $control_type == RADIOBUTTON || $control_type == LISTBOX) {
						$property_value_id = $values[$i];
						$sql  = " SELECT property_value, property_price FROM " . $table_prefix . "order_custom_values ";
						$sql .= " WHERE property_id = ".$db->tosql($property_id,INTEGER,true,false)." ";
						$sql .= " AND property_value_id = ".$db->tosql($property_value_id, INTEGER,true,false);
						$db->query($sql);
						if ($db->next_record()){
							$property_value = $db->f("property_value");
							$property_price = $db->f("property_price");
						}
					} else {
						$property_value = $values[$i];
					}

					if (strlen($property_value) || $property_value_id) {
						$sql  = " INSERT INTO " . $table_prefix . "orders_properties ";
						$sql .= " (order_id, property_id, property_name, property_type, property_value_id, property_value, property_price) VALUES (";
						$sql .= $db->tosql($order_id, INTEGER) . ", ";
						$sql .= $db->tosql($property_id, INTEGER) . ", ";
						$sql .= $db->tosql($property_name, TEXT) . ", ";
						$sql .= $db->tosql($property_type, INTEGER) . ", ";
						$sql .= $db->tosql($property_value_id, INTEGER) . ", ";
						$sql .= $db->tosql($property_value, TEXT) . ", ";
						$sql .= $db->tosql($property_price, FLOAT, true, false) . ") ";
						$db->query($sql);
					}
				}
			}
		}
	} 
	
	function hide_custom_fields()
	{
		global $r, $pp;

		foreach ($pp as $id => $pp_row) {
			$param_name = "pp_" . $pp_row["property_id"];
			$r->change_property($param_name, SHOW, false);
		}
	}

	function get_country_codes()
	{
		global $r, $db, $table_prefix;
		
		$state_id = $r->get_value("state_id");
		$sql  = " SELECT state_code FROM " . $table_prefix . "states ";
		$sql .= " WHERE state_id=" . $db->tosql($state_id, INTEGER, true, false);
		$db->query($sql);
		if ($db->next_record()) {
			$r->set_value("state_code", $db->f("state_code"));
		}
		
		$country_id = $r->get_value("country_id");
		$sql  = " SELECT country_code FROM " . $table_prefix . "countries ";
		$sql .= " WHERE country_id=" . $db->tosql($country_id, INTEGER, true, false);
		$db->query($sql);
		if ($db->next_record()) {
			$r->set_value("country_code", $db->f("country_code"));
		}
	}
	
	function get_delivery_codes()
	{
		global $r, $db, $table_prefix;
		
		$delivery_state_id = $r->get_value("delivery_state_id");
		$sql  = " SELECT state_code FROM " . $table_prefix . "states ";
		$sql .= " WHERE state_id=" . $db->tosql($delivery_state_id, INTEGER, true, false);
		$db->query($sql);
		if ($db->next_record()) {
			$r->set_value("delivery_state_code", $db->f("state_code"));
		}
		
		$delivery_country_id = $r->get_value("delivery_country_id");
		$sql  = " SELECT country_code FROM " . $table_prefix . "countries ";
		$sql .= " WHERE country_id=" . $db->tosql($delivery_country_id, INTEGER, true, false);
		$db->query($sql);
		if ($db->next_record()) {
			$r->set_value("delivery_country_code", $db->f("country_code"));
		}
	}
?>