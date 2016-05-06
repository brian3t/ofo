<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user.php                                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/parameters.php");
	include_once($root_folder_path . "includes/friendly_functions.php");
	include_once($root_folder_path . "messages/" . $language_code . "/download_messages.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("site_users");
	$permissions = get_permissions();
	$add_users = get_setting_value($permissions, "add_users", 0);
	$update_users = get_setting_value($permissions, "update_users", 0);
	$remove_users = get_setting_value($permissions, "remove_users", 0);
	$subscriptions_access = get_setting_value($permissions, "subscriptions", 0);
	$orders_access = get_setting_value($permissions, "sales_orders", 0);

	$type_id = get_param("type_id");
	$user_id = get_param("user_id");
	if (strlen($user_id)) {
		$sql  = " SELECT user_type_id FROM " . $table_prefix . "users ";
		$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
		$type_id = get_db_value($sql);
	}

	$group_is_sms_allowed = 0;
	$sql = " SELECT * FROM " . $table_prefix . "user_types WHERE type_id=" . $db->tosql($type_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$group_is_sms_allowed = $db->f("is_sms_allowed");
	}

	$setting_type = "user_profile_" . $type_id;
	$html_editor = get_setting_value($settings, "html_editor", 1);
	$site_url = get_setting_value($settings, "site_url", "");

	$user_settings = array();
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "user_types_settings WHERE type_id=" . $db->tosql($type_id, INTEGER);
	$db->query($sql);
	while ($db->next_record()) {
		$user_settings[$db->f("setting_name")] = $db->f("setting_value");
	}
	$affiliate_join = get_setting_value($user_settings, "affiliate_join", 0);

	$date_format_msg = str_replace("{date_format}", join("", $date_edit_format), DATE_FORMAT_MSG);

	$yes_no_messages = 
		array( 
			array(1, YES_MSG),
			array(0, NO_MSG)
		);


	$user_profile = array();
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
	if ($multisites_version) {
		$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($site_id,INTEGER). ") ";
		$sql .= "ORDER BY site_id ASC ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$user_profile[$db->f("setting_name")] = $db->f("setting_value");
	}
	$login_field_type = get_setting_value($user_profile, "login_field_type", 1);

	if ($login_field_type == 2) {
		$login_desc = " (".EMAIL_FIELD.")";
	} else {
		$login_desc = "";
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_user.html");
	$site_url_path = $settings["site_url"] ? $settings["site_url"] : "../";
	$t->set_var("css_file", $site_url_path . "styles/" . $settings["style_name"] . ".css");
	$t->set_var("html_editor", $html_editor);
	$t->set_var("site_url", $site_url);
	$t->set_var("login_desc", $login_desc);

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_users_href", "admin_users.php");
	$t->set_var("admin_user_href", "admin_user.php");
	$t->set_var("admin_select_href", "admin_select.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_order_href", "admin_order.php");
	$t->set_var("date_edit_format", join("", $date_edit_format));
	$t->set_var("date_format_msg", $date_format_msg);
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_USER_MSG, CONFIRM_DELETE_MSG));

	$sections = array();
	$sql = "SELECT section_id, section_name FROM " . $table_prefix . "user_profile_sections WHERE is_active=1 ORDER BY section_order, section_id ";
	$db->query($sql);
	while ($db->next_record()) {
		$section_id = $db->f("section_id");
		$section_name = get_translation($db->f("section_name"));
		$sections[$section_id] = $section_name;
	}

	// prepare custom options 
	$pp = array(); $pn = 0;
	$sql  = " SELECT upp.* ";
	$sql .= " FROM (" . $table_prefix . "user_profile_properties upp ";
	$sql .= " INNER JOIN " . $table_prefix . "user_profile_sections ups ON upp.section_id=ups.section_id) ";
	$sql .= " WHERE user_type_id=" . $db->tosql($type_id, INTEGER);
	$sql .= " AND ups.is_active=1 ";
	$sql .= " AND property_show IN (1,2,3) "; 
	$sql .= " ORDER BY property_order, property_id ";
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$pp[$pn]["property_id"] = $db->f("property_id");
			$pp[$pn]["property_order"] = $db->f("property_order");
			$pp[$pn]["property_name"] = $db->f("property_name");
			$pp[$pn]["property_description"] = $db->f("property_description");
			$pp[$pn]["default_value"] = $db->f("default_value");
			$pp[$pn]["property_style"] = $db->f("property_style");
			$pp[$pn]["section_id"] = $db->f("section_id");
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


	$r = new VA_Record($table_prefix . "users");
	$r->return_page = "admin_users.php";

	$r->add_where("user_id", INTEGER);

	$yes_no_values = array(array("1", YES_MSG), array("0", NO_MSG));
	$r->add_radio("is_approved", INTEGER, $yes_no_values, IS_APPROVED_MSG);
	$r->change_property("is_approved", DEFAULT_VALUE, 1);
	$r->add_radio("is_hidden", INTEGER, $yes_no_values);
	$r->change_property("is_hidden", DEFAULT_VALUE, 0);
	
	$r->add_textbox("expiry_date", DATETIME, ADMIN_EXPIRY_DATE_MSG);
	$r->change_property("expiry_date", VALUE_MASK, $date_edit_format);
	$r->add_textbox("suspend_date", DATETIME, SUSPEND_DATE_MSG);
	$r->change_property("suspend_date", VALUE_MASK, $date_edit_format);

	$r->add_textbox("registration_last_step", INTEGER, REGISTRATION_STEP_MSG);
	$r->add_textbox("registration_total_steps", INTEGER, TOTAL_STEPS_MSG);
	$r->change_property("registration_last_step", DEFAULT_VALUE, 1);
	$r->change_property("registration_last_step", MIN_VALUE, 1);
	$r->change_property("registration_total_steps", DEFAULT_VALUE, 1);
	$r->change_property("registration_total_steps", MIN_VALUE, 1);

	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);
	$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
	$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);

	$r->add_textbox("affiliate_code", TEXT, AFFILIATE_CODE_FIELD);
	$r->change_property("affiliate_code", USE_SQL_NULL, false);
	if ($affiliate_join) {
		$r->change_property("affiliate_code", REQUIRED, true);
			$r->change_property("affiliate_code", REGEXP_MASK, ALPHANUMERIC_REGEXP);
			$r->change_property("affiliate_code", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
			$r->change_property("affiliate_code", TRIM, true);
	}
	$r->add_checkbox("tax_free", INTEGER);
	$r->add_radio("is_sms_allowed", INTEGER, $yes_no_messages);
	$r->change_property("is_sms_allowed", DEFAULT_VALUE, $group_is_sms_allowed);

	$discount_types = array(
		array("", ""), array(0, NOT_AVAILABLE_MSG), array(1, PERCENT_PER_PROD_FULL_PRICE_MSG),
		array(2, FIXED_AMOUNT_PER_PROD_MSG), array(3, PERCENT_PER_PROD_SELL_PRICE_MSG),
		array(4, PERCENT_PER_PROD_SELL_BUY_MSG)
	);
	$r->add_select("discount_type", INTEGER, $discount_types);
	$r->add_textbox("discount_amount", NUMBER, DISCOUNT_AMOUNT_MSG);

	$r->add_select("merchant_fee_type", INTEGER, $discount_types);
	$r->add_textbox("merchant_fee_amount", NUMBER, MERCHANT_FEE_AMOUNT_MSG);

	$r->add_select("affiliate_commission_type", INTEGER, $discount_types);
	$r->add_textbox("affiliate_commission_amount", NUMBER, AFFILIATE_COMMISSION_AMOUNT_MSG);

	$r->add_select("reward_type", INTEGER, $discount_types, REWARD_POINTS_TYPE_MSG);
	$r->add_textbox("reward_amount", NUMBER, REWARD_POINTS_AMOUNT_MSG);
	$r->add_select("credit_reward_type", INTEGER, $discount_types, REWARD_CREDITS_TYPE_MSG);
	$r->add_textbox("credit_reward_amount", NUMBER, REWARD_CREDITS_AMOUNT_MSG);

	$login_params = array();
	$companies = get_db_values("SELECT company_id,company_name FROM " . $table_prefix . "companies ", array(array("", SELECT_COMPANY_MSG)));
	$states = get_db_values("SELECT state_id,state_name FROM " . $table_prefix . "states ORDER BY state_name ", array(array(0, SELECT_STATE_MSG)));
	$countries = get_db_values("SELECT country_id,country_name FROM " . $table_prefix . "countries ORDER BY country_order, country_name ", array(array("", SELECT_COUNTRY_MSG)));

	// add controls by sections
	foreach ($sections as $section_id => $section_name) 
	{
		if ($section_id == 1) 
		{
			if (!$user_id) {
				$r->add_textbox("login", TEXT, $section_name.": ".LOGIN_FIELD);
				$r->parameters["login"][REQUIRED] = true;
				$r->parameters["login"][UNIQUE] = true;
				$r->parameters["login"][MIN_LENGTH] = 3;
				$r->change_property("login", TRIM, true);
				if ($login_field_type == 2) {
					$r->change_property("login", REGEXP_MASK, EMAIL_REGEXP);
					$r->change_property("login", REGEXP_ERROR, INCORRECT_EMAIL_MESSAGE);
				} else {
					$r->change_property("login", REGEXP_MASK, ALPHANUMERIC_REGEXP);
					$r->change_property("login", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
				}
				$r->add_textbox("password", TEXT, $section_name.": ".PASSWORD_FIELD);
				$r->parameters["password"][REQUIRED] = true;
				$r->parameters["password"][MIN_LENGTH] = 3;
				$r->change_property("password", TRIM, true);

				$r->add_textbox("confirm", TEXT, $section_name.": ".CONFIRM_PASS_FIELD);
				$r->change_property("confirm", USE_IN_SELECT, false);
				$r->change_property("confirm", USE_IN_INSERT, false);
				$r->change_property("confirm", USE_IN_UPDATE, false);
				$r->change_property("confirm", TRIM, true);
				$r->change_property("password", MATCHED, "confirm");
				$login_params = array("login", "password", "confirm");
			}
		} 
		elseif ($section_id == 2) 
		{
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
			$r->change_property("email", UNIQUE, true);
			if ($login_field_type == 2 && $user_profile["show_email"]==0) {
				$r->change_property("email", USE_IN_UPDATE, false); 
			}
			$r->add_textbox("address1", TEXT, $section_name.": ".STREET_FIRST_FIELD);
			$r->add_textbox("address2", TEXT, $section_name.": ".STREET_SECOND_FIELD);
			$r->add_textbox("city", TEXT, $section_name.": ".CITY_FIELD);
			$r->add_textbox("province", TEXT, $section_name.": ".PROVINCE_FIELD);
			$r->add_select("state_id", INTEGER, $states, $section_name.": ".STATE_FIELD);
			$r->change_property("state_id", USE_SQL_NULL, false);
			$r->add_textbox("state_code", TEXT);
			$r->change_property("state_code", USE_SQL_NULL, false);
			$r->add_textbox("zip", TEXT, $section_name.": ".ZIP_FIELD);
			$r->add_select("country_id", INTEGER, $countries, $section_name.": ".COUNTRY_FIELD);
			$r->change_property("country_id", USE_SQL_NULL, false);
			$r->add_textbox("country_code", TEXT);
			$r->change_property("country_code", USE_SQL_NULL, false);
			$r->add_textbox("phone", TEXT, PHONE_FIELD);
			$r->add_textbox("daytime_phone", TEXT, $section_name.": ".DAYTIME_PHONE_FIELD);
			$r->add_textbox("evening_phone", TEXT, $section_name.": ".EVENING_PHONE_FIELD);
			$r->add_textbox("cell_phone", TEXT, $section_name.": ".CELL_PHONE_FIELD);
			$r->add_textbox("fax", TEXT, $section_name.": ".FAX_FIELD);
			if (isset($user_profile["show_birth_date"]) && $user_profile["show_birth_date"] == 1) {
				$parameters[] = "birth_date";
				$months = array_merge (array(array("", "")), $months);
				$r->add_select("birth_month", INTEGER, $months, $section_name.": ".BIRTH_MONTH_MSG);
				$r->add_textbox("birth_day", INTEGER, $section_name.": ".BIRTH_DAY_MSG);
				$r->change_property("birth_day", MIN_VALUE, 1);
				$r->change_property("birth_day", MAX_VALUE, 31);
				$r->add_textbox("birth_year", INTEGER, $section_name.": ".BIRTH_YEAR_MSG);
				$r->change_property("birth_year", MIN_VALUE, 1900);
				$r->change_property("birth_year", MAX_VALUE, date("Y") - 10);
				if (isset($user_profile["birth_date_required"]) && $user_profile["birth_date_required"] == 1) {
					$r->change_property("birth_month", REQUIRED, true);
					$r->change_property("birth_day", REQUIRED, true);
					$r->change_property("birth_year", REQUIRED, true);
				}
				$r->add_textbox("birth_date", DATETIME, $section_name.": ".BIRTHDAY_MSG);
				$r->change_property("birth_date", VALUE_MASK, array("YYYY","-","M","-","D"));
				$r->change_property("birth_date", USE_IN_INSERT, false);
				$r->change_property("birth_date", USE_IN_UPDATE, false);
				$r->change_property("birth_date", USE_IN_SELECT, false);
			}
			$parameters[] = "personal_image";
			$r->add_textbox("personal_image", TEXT, $section_name.": ".PERSONAL_IMAGE_FIELD);
			if(!isset($user_profile["show_personal_image"]) || $user_profile["show_personal_image"] != 1) {
				$r->parameters["personal_image"][SHOW] = false;
			}
		} 
		elseif ($section_id == 3) 
		{
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
			$r->change_property("delivery_state_id", USE_SQL_NULL, false);
			$r->add_textbox("delivery_state_code", TEXT);
			$r->change_property("delivery_state_code", USE_SQL_NULL, false);
			$r->add_textbox("delivery_zip", TEXT, $section_name.": ". ZIP_FIELD);
			$r->add_select("delivery_country_id", INTEGER, $countries, $section_name.": ". COUNTRY_FIELD);
			$r->change_property("delivery_country_id", USE_SQL_NULL, false);
			$r->add_textbox("delivery_country_code", TEXT);
			$r->change_property("delivery_country_code", USE_SQL_NULL, false);
			$r->add_textbox("delivery_phone", TEXT, $section_name.": ". PHONE_FIELD);
			$r->add_textbox("delivery_daytime_phone", TEXT, $section_name.": ". DAYTIME_PHONE_FIELD);
			$r->add_textbox("delivery_evening_phone", TEXT, $section_name.": ". EVENING_PHONE_FIELD);
			$r->add_textbox("delivery_cell_phone", TEXT, $section_name.": ". CELL_PHONE_FIELD);
			$r->add_textbox("delivery_fax", TEXT, $section_name.": ". FAX_FIELD);
		} 
		elseif ($section_id == 4) 
		{
			// additional fields
			$r->add_textbox("nickname", TEXT, NICKNAME_FIELD);
			$r->change_property("nickname", USE_SQL_NULL, false);
			$r->change_property("nickname", AFTER_VALIDATE, "validate_nickname");
			//$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
			//$r->change_property("friendly_url", USE_SQL_NULL, false);
			//$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
			$r->add_textbox("tax_id", TEXT, TAX_ID_FIELD);
			$r->add_textbox("paypal_account", TEXT, PAYPAL_ACCOUNT_FIELD);
			$r->change_property("paypal_account", REGEXP_MASK, EMAIL_REGEXP);
			$r->add_textbox("msn_account", TEXT, MSN_ACCOUNT_FIELD);
			$r->change_property("msn_account", REGEXP_MASK, EMAIL_REGEXP);
			$r->add_textbox("icq_number", TEXT, ICQ_NUMBER_FIELD);
			$r->change_property("icq_number", REGEXP_MASK, "/^\d+$/");
			$r->add_textbox("user_site_url", TEXT, USER_SITE_URL_FIELD);
			$r->add_textbox("short_description", TEXT, SHORT_DESCRIPTION_MSG);
			$r->add_textbox("full_description", TEXT, FULL_DESCRIPTION_MSG);
		}

		foreach ($pp as $id => $pp_row) 
		{
			if ($pp_row["section_id"] == $section_id) {
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
						$sql  = " SELECT property_value_id, property_value FROM " . $table_prefix . "user_profile_values ";
						$sql .= " WHERE property_id=" . $db->tosql($pp_row["property_id"], INTEGER) . " AND hide_value=0";
						$sql .= " ORDER BY property_value_id ";
					}
					$r->change_property($param_name, VALUES_LIST, get_db_values($sql, ""));
				}
				if ($pp_row["required"] == 1) {
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

	// admin notes 
	$r->add_textbox("admin_notes", TEXT);

	// stats data
	$r->add_textbox("registration_date", DATETIME);
	$r->change_property("registration_date", USE_IN_UPDATE, false);
	$r->add_textbox("registration_ip", TEXT);
	$r->change_property("registration_ip", USE_IN_UPDATE, false);
	$r->add_textbox("modified_date", DATETIME);
	$r->change_property("modified_date", USE_IN_INSERT, false);
	$r->change_property("modified_date", USE_IN_UPDATE, false);
	$r->add_textbox("modified_ip", TEXT);
	$r->change_property("modified_ip", USE_IN_INSERT, false);
	$r->change_property("modified_ip", USE_IN_UPDATE, false);
	$r->add_textbox("admin_modified_date", DATETIME);
	$r->change_property("admin_modified_date", USE_IN_INSERT, false);
	$r->add_textbox("admin_modified_ip", TEXT);
	$r->change_property("admin_modified_ip", USE_IN_INSERT, false);
	$r->add_textbox("last_logged_date", DATETIME);
	$r->change_property("last_logged_date", USE_IN_INSERT, false);
	$r->change_property("last_logged_date", USE_IN_UPDATE, false);
	$r->add_textbox("last_logged_ip", TEXT);
	$r->change_property("last_logged_ip", USE_IN_INSERT, false);
	$r->change_property("last_logged_ip", USE_IN_UPDATE, false);
	$r->add_textbox("last_visit_date", DATETIME);
	$r->change_property("last_visit_date", USE_IN_INSERT, false);
	$r->change_property("last_visit_date", USE_IN_UPDATE, false);
	$r->add_textbox("last_visit_ip", TEXT);
	$r->change_property("last_visit_ip", USE_IN_INSERT, false);
	$r->change_property("last_visit_ip", USE_IN_UPDATE, false);
	$r->add_textbox("last_visit_page", TEXT);
	$r->change_property("last_visit_page", USE_IN_INSERT, false);
	$r->change_property("last_visit_page", USE_IN_UPDATE, false);
	
	$r->add_textbox("user_type_id", INTEGER);
	$r->change_property("user_type_id", USE_IN_UPDATE, false);
	
	$r->events[BEFORE_INSERT] = "check_insert_data";
	$r->events[AFTER_INSERT]  = "process_inserted_data";
	$r->events[AFTER_UPDATE]  = "update_user_data";
	$r->events[AFTER_REQUEST] = "set_user_fields";
	$r->events[AFTER_SELECT]  = "get_additional_data";
	$r->events[BEFORE_SHOW]   = "hide_custom_fields";

	$r->operations[INSERT_ALLOWED] = $add_users;
	$r->operations[UPDATE_ALLOWED] = $update_users;
	$r->operations[DELETE_ALLOWED] = $remove_users;

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
			if (isset($user_profile[$show_personal]) && $user_profile[$show_personal] == 1) {
				$personal_number++;
				if (isset($user_profile[$param_name . "_required"]) && $user_profile[$param_name . "_required"] == 1) {
					$r->parameters[$param_name][REQUIRED] = true;
				}
			} else {
				$r->parameters[$param_name][SHOW] = false;
			}
		}

		if ($r->parameter_exists($delivery_param)) {
			$r->change_property($delivery_param, TRIM, true);
			if (isset($user_profile[$show_delivery]) && $user_profile[$show_delivery] == 1) {
				$delivery_number++;
				if (isset($user_profile[$delivery_param . "_required"]) && $user_profile[$delivery_param . "_required"] == 1) {
					$r->parameters[$delivery_param][REQUIRED] = true;
				}
			} else {
				$r->parameters[$delivery_param][SHOW] = false;
			}
		}
	}

	$additional_number = 0;
	for ($i = 0; $i < sizeof($additional_parameters); $i++)
	{                                    
		$param_name = $additional_parameters[$i];
		$show_param_name = "show_" . $additional_parameters[$i];
		$param_required = $additional_parameters[$i] . "_required";
		if ($r->parameter_exists($param_name)) {
			if (isset($user_profile[$show_param_name]) && $user_profile[$show_param_name] == 1) {
				$additional_number++;
				if($user_profile[$param_required] == 1) {
					$r->parameters[$additional_parameters[$i]][REQUIRED] = true;
				}
			} elseif ($param_name != "is_hidden") {
				$r->parameters[$additional_parameters[$i]][SHOW] = false;
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

	$tab = get_param("tab");
	$operation = get_param("operation");
	if (!$tab || $operation) { $tab = "general"; }
	$user_id = get_param("user_id");

	//$r->events[BEFORE_INSERT] = "set_friendly_url";
	//$r->events[BEFORE_UPDATE] = "set_friendly_url";

	$r->process();

	$affiliate_code = $r->get_value("affiliate_code");
	if (!strlen($affiliate_code)) {
		$affiliate_code = "type_your_code_here";
	}
	$affiliate_url = $site_url . "?af=" . $affiliate_code;
	$affiliate_code_help = str_replace("{affiliate_url}", $affiliate_url, AFFILIATE_CODE_HELP_MSG);
	$t->set_var("affiliate_code_help", $affiliate_code_help);

	$r->set_form_parameters();
	$t->set_var("type_id", htmlspecialchars($type_id));

	$eol = get_eol();

	$properties_ids = "";
	foreach ($sections as $section_id => $section_name) {
		$t->set_var("profile_section", "");
		$t->set_var("profile_properties", "");
		$section_properties = 0;

	  if ($section_id == 1) {
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
		} elseif ($section_id == 2) {
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
		} elseif ($section_id == 3) {
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
		} elseif ($section_id == 4) {
			for ($i = 0; $i < sizeof($additional_parameters); $i++)
			{                                    
				$param_name = $additional_parameters[$i];
				if ($r->get_property_value($param_name, SHOW) && $param_name != "is_hidden") {
					$section_properties++;
					$t->copy_var($param_name . "_block", "profile_properties");
					$t->set_var($param_name . "_block", "");
				}
			}
			$t->set_var("ADDITIONAL_DETAILS_MSG", $section_name);
			$t->parse_to("additional", "profile_section");
		} else {
			$t->set_var("section_name", $section_name);
			$t->parse("profile_section", false);
		}

		// show custom options 
		if (sizeof($pp) > 0) 
		{
			for ($pn = 0; $pn < sizeof($pp); $pn++) {
				if ($pp[$pn]["section_id"] == $section_id) {
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
						$sql  = " SELECT * FROM " . $table_prefix . "user_profile_values ";
						$sql .= " WHERE property_id=" . $property_id . " AND hide_value=0";
						$sql .= " ORDER BY property_value_id ";
					}
					if (strtoupper($control_type) == "LISTBOX") {
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
							if (strlen($operation) || $user_id) {
								if ($selected_value == $property_value_id) {
									$property_selected  = "selected ";
									//$custom_options[$property_id] = array($property_type, $property_order, $property_name_initial, $property_value);
								}
							} elseif ($is_default_value) {
								$property_selected  = "selected ";
							} 
        
							$properties_values .= "<option " . $property_selected . "value=\"" . htmlspecialchars($property_value_id) . "\">";
							$properties_values .= htmlspecialchars($property_value);
							$properties_values .= "</option>" . $eol;
						}
						$property_control .= $before_control_html;
						$property_control .= "<select name=\"pp_" . $property_id . "\" ";
						if ($onchange_code) { $property_control .= " onChange=\"" . $onchange_code. "\""; }
						if ($onclick_code) { $property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($control_code) { $property_control .= " " . $control_code . " "; }
						if ($control_style) { $property_control .= " style=\"" . $control_style . "\""; }
						$property_control .= ">" . $properties_values . "</select>";
						$property_control .= $after_control_html;
					} elseif (strtoupper($control_type) == "RADIOBUTTON" || strtoupper($control_type) == "CHECKBOXLIST") {
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
							if (strlen($operation) || $user_id) {
								if (is_array($selected_value) && in_array($property_value_id, $selected_value)) {
									$property_checked = "checked ";
								}
							} elseif ($is_default_value) {
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
					} elseif (strtoupper($control_type) == "TEXTBOX") {
						if (strlen($operation) || $user_id) {
							$control_value = $r->get_value($param_name);
						} else {
							$control_value = $default_value;
						}
						$property_control .= $before_control_html;
						$property_control .= "<input type=\"text\" name=\"pp_" . $property_id . "\"";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						$property_control .= " value=\"". htmlspecialchars($control_value) . "\">";
						$property_control .= $after_control_html;
					} elseif (strtoupper($control_type) == "TEXTAREA") {
						if (strlen($operation) || $user_id) {
							$control_value = $r->get_value($param_name);
						} else {
							$control_value = $default_value;
						}
						$property_control .= $before_control_html;
						$property_control .= "<textarea name=\"pp_" . $property_id . "\"";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						$property_control .= ">". htmlspecialchars($control_value) ."</textarea>";
						$property_control .= $after_control_html;
					} else {
						$property_control .= $before_control_html;
						if ($property_required) {
							$property_control .= "<input type=\"hidden\" name=\"pp_" . $property_id . "\" value=\"" . htmlspecialchars($property_description) . "\">";
						}
						$property_control .= "<span";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						$property_control .= ">" . get_translation($default_value) . "</span>";
						$property_control .= $after_control_html;
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
        
					$t->parse("profile_properties", true);
				}
			}
  
			$t->set_var("properties_ids", $properties_ids);
		}
		// end custom options

		if ($section_properties) {
			$t->parse("profile_sections", true);
		}
	}

	$stats = false;
	$admin_user_ip_url = new VA_URL("admin_user.php", true, array("ip", "operation"));
	$admin_user_ip_url->add_parameter("user_id", CONSTANT, $user_id);

	$stats_data = array(
		array("registration_info", "registration_date", "registration_ip"),
		array("modified_info", "modified_date", "modified_ip"),
		array("admin_modified_info", "admin_modified_date", "admin_modified_ip"),
		array("last_logged_info", "last_logged_date", "last_logged_ip"),
		array("last_visit_info", "last_visit_date", "last_visit_ip"),
	);

	for ($i = 0; $i < sizeof($stats_data); $i++) {
		list ($block_name, $date_field, $ip_field) = $stats_data[$i];
		if ($r->get_value($date_field)) {
			$stats = true;
			$date_info = $r->get_value($date_field);
			$ip_address = $r->get_value($ip_field);
  
			$t->set_var("black_ip", "");
			$t->set_var("ip_info", "");
			$t->set_var("date_info", va_date($datetime_show_format, $date_info));
			if ($ip_address) {
				$t->set_var("ip_address", $ip_address);
				$admin_user_ip_url->add_parameter("ip", CONSTANT, $ip_address);
				$sql  = " SELECT ip_address FROM " . $table_prefix . "black_ips ";
				$sql .= " WHERE ip_address=" . $db->tosql($ip_address, TEXT);
				$db->query($sql);
				if ($db->next_record()) {
					$admin_user_ip_url->add_parameter("operation", CONSTANT, "remove_ip");
					$t->set_var("admin_user_ip_url", $admin_user_ip_url->get_url());
					$t->parse("black_ip", false);
				} else {
					$admin_user_ip_url->add_parameter("operation", CONSTANT, "add_ip");
					$t->set_var("admin_user_ip_url", $admin_user_ip_url->get_url());
					$t->parse("ip_info", false);
				}
			}
			$t->parse($block_name, false);
		}
	}
	if ($stats) {
		$t->parse("stats_title", false);
	}

	$total_order_items = 0; $total_subscriptions = 0;
	if ($orders_access) {
		$total_order_items = user_orders_items();
	}
	if ($subscriptions_access) {
		$total_subscriptions = user_subscriptions();
	}

	// set styles for tabs
	$tabs = array(
		"general" => array("title" => EDIT_USER_INFO_MSG), 
		"notes" => array("title" => NOTES_MSG), 
		"stats" => array("title" => ADMIN_STATISTIC_MSG, "show" => $stats), 
		"orders_items" => array("title" => ORDERS_ITEMS_MSG, "show" => $total_order_items), 
		"subscriptions" => array("title" => SUBSCRIPTIONS_MSG, "show" => $total_subscriptions), 
	);

	parse_admin_tabs($tabs, $tab, 5);


	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function check_insert_data()
	{
		global $r, $db_type, $table_prefix, $settings, $type_id;
		$password_encrypt = get_setting_value($settings, "password_encrypt", 0);
		if ($password_encrypt == 1) {
			$r->set_value("password", md5($r->get_value("password")));
		}
		
		if ($db_type == "postgre") {
			$user_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "users') ");
			$r->change_property("user_id", USE_IN_INSERT, true);
			$r->set_value("user_id", $user_id);
		}
		
		$r->set_value("user_type_id", $type_id);
	}

	function process_inserted_data()
	{
		global $r, $db_type, $table_prefix;

		if ($db_type == "mysql") {
			$user_id = get_db_value(" SELECT LAST_INSERT_ID() ");
			$r->set_value("user_id", $user_id);
		} elseif ($db_type == "access") {
			$user_id = get_db_value(" SELECT @@IDENTITY ");
			$r->set_value("user_id", $user_id);
		} elseif ($db_type == "db2") {
			$user_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "users FROM " . $table_prefix . "users");
			$r->set_value("user_id", $user_id);
		}
		update_user_properties();
		update_user_status($user_id, $r->get_value("is_approved"));
	}

	function get_additional_data()
	{
		global $r, $pp, $db, $table_prefix;
  
		$user_id = $r->get_value("user_id");
  
		$sql  = " SELECT property_id, property_value FROM " . $table_prefix . "users_properties ";
		$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$property_id = $db->f("property_id");
			$param_name = "pp_" . $property_id;
			$property_value = $db->f("property_value");
			if ($r->parameter_exists($param_name)) {
				$r->set_value($param_name, $property_value);
			}
		}
	}

	function set_user_fields()
	{
		global $r, $db, $parameters, $user_profile, $table_prefix, $update_users, $user_id;
	
		$current_date = va_time();
		$user_ip = get_ip();
		$r->set_value("registration_date", $current_date);
		$r->set_value("registration_ip", $user_ip);
		$r->set_value("admin_modified_date", $current_date);
		$r->set_value("admin_modified_ip", $user_ip);
		if ($r->get_value("same_as_personal")) {
			for ($i = 0; $i < sizeof($parameters); $i++) { 
				$personal_param = "show_" . $parameters[$i];
				$delivery_param = "show_delivery_" . $parameters[$i];
				if (isset($user_profile[$delivery_param]) && isset($user_profile[$personal_param]) &&
					$user_profile[$delivery_param] == 1 && $user_profile[$personal_param] == 1) {
					$r->set_value("delivery_" . $parameters[$i], $r->get_value($parameters[$i]));
				}
			}
		}
	
		if ($r->parameter_exists("birth_date")) {
			$r->change_property("birth_date", REQUIRED, false);
			if (!$r->is_empty("birth_month") || !$r->is_empty("birth_day") || !$r->is_empty("birth_year")) {
				$r->change_property("birth_month", REQUIRED, true);
				$r->change_property("birth_day", REQUIRED, true);
				$r->change_property("birth_year", REQUIRED, true);
				$birth_month = $r->get_value("birth_month");
				$birth_day = $r->get_value("birth_day");
				$birth_year = $r->get_value("birth_year");
				if ($birth_month && $birth_day > 0 && $birth_day < 32 && $birth_year > 1900 && $birth_year < date("Y")) {
					$birth_date = $birth_year."-".$birth_month."-".$birth_day;
					$r->set_value("birth_date", $birth_date);
				}
			}
		}

		// update state and country codes
		if (!$r->is_empty("state_id")) {
			$sql = " SELECT state_code FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("state_id"), INTEGER);
			$r->set_value("state_code", get_db_value($sql));
		}
		if (!$r->is_empty("country_id")) {
			$sql = " SELECT country_code FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("country_id"), INTEGER);
			$r->set_value("country_code", get_db_value($sql));
		}
		if (!$r->is_empty("delivery_state_id")) {
			$sql = " SELECT state_code FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("delivery_state_id"), INTEGER);
			$r->set_value("delivery_state_code", get_db_value($sql));
		}
		if (!$r->is_empty("delivery_country_id")) {
			$sql = " SELECT country_code FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("delivery_country_id"), INTEGER);
			$r->set_value("delivery_country_code", get_db_value($sql));
		}
	
		// check for ip operation
		$operation = get_param("operation");
		if($operation == "add_ip" || $operation == "remove_ip") {
			$ip = get_param("ip");
			if (!strlen($ip)) {
				$r->errors .= MISSING_IP_ADDRESS_MSG;
			} elseif($update_users != 1) {
				$r->errors = $r->errors_messages[UPDATE_ALLOWED] . "<br>";
			} elseif ($operation == "add_ip") {
				$sql  = "SELECT ip_address FROM " . $table_prefix . "black_ips WHERE ip_address=" . $db->tosql($ip, TEXT);
				$db->query($sql);
				if (!$db->next_record()) {
					$sql  = " INSERT INTO " . $table_prefix . "black_ips (ip_address, address_action) VALUES (";
					$sql .= $db->tosql($ip, TEXT) . ", 1)";
					$db->query($sql);
				}
			} elseif ($operation == "remove_ip") {
				$sql  = " DELETE FROM " . $table_prefix . "black_ips WHERE ip_address=" . $db->tosql($ip, TEXT);
				$db->query($sql);
			}
		}
	}

	function update_user_data()
	{
		global $r;
		update_user_properties();
		update_user_status($r->get_value("user_id"), $r->get_value("is_approved"));
	}
	
	function update_user_properties()
	{
		global $r, $pp, $db, $table_prefix;
	
		$user_id = $r->get_value("user_id");
	
		foreach ($pp as $id => $data) {
			$property_id =$data["property_id"];
			$param_name = "pp_" . $property_id;
			$values = array();
			if ($r->get_property_value($param_name, CONTROL_TYPE) == CHECKBOXLIST) {
				$values = $r->get_value($param_name);
			} else {
				$values[] = $r->get_value($param_name);
			}
			$sql  = " DELETE FROM " . $table_prefix . "users_properties ";
			$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$sql .= " AND property_id=" . $db->tosql($property_id, INTEGER);
			$db->query($sql);
			if (is_array($values)) {
				for ($i = 0; $i < sizeof($values); $i++) {
					$property_value = $values[$i];
					if (strlen($property_value)) {
						$sql  = " INSERT INTO " . $table_prefix . "users_properties (user_id, property_id, property_value) VALUES (";
						$sql .= $db->tosql($user_id, INTEGER) . ", ";
						$sql .= $db->tosql($property_id, INTEGER) . ", ";
						$sql .= $db->tosql($property_value, TEXT) . ") ";
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
	
	function validate_nickname()
	{
		global $r, $db, $eol, $table_prefix;

		$nickname = $r->get_value("nickname");
		if (strlen($nickname)) {
			$user_id = $r->get_value("user_id");
			$sql  = " SELECT user_id FROM " . $table_prefix . "users ";
			$sql .= " WHERE (nickname=" . $db->tosql($nickname, TEXT) . " OR login=" . $db->tosql($nickname, TEXT) . ") ";
			if (strlen($user_id)) {
				$sql .= " AND NOT (user_id=" . $db->tosql($user_id, INTEGER) . ")";
			}
			$db->query($sql);
			if ($db->next_record()) {
				$error_message = str_replace("{field_name}", $r->parameters["nickname"][CONTROL_DESC], UNIQUE_MESSAGE);
				$r->errors .= $error_message . "<br>" . $eol;
			} else {
				// check nickname in admins table
				$sql  = " SELECT admin_id FROM " . $table_prefix . "admins ";
				$sql .= " WHERE (nickname=" . $db->tosql($nickname, TEXT) . " OR admin_name=" . $db->tosql($nickname, TEXT) . ") ";
				$db->query($sql);
				if ($db->next_record()) {
					$error_message = str_replace("{field_name}", $r->parameters["nickname"][CONTROL_DESC], UNIQUE_MESSAGE);
					$r->errors .= $error_message . "<br>" . $eol;
				}
			}
		}
	}

	function user_orders_items()
	{
		global $r, $t, $db, $eol, $settings, $table_prefix;

		$user_id = $r->get_value("user_id");
		$total_order_items = 0;
		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "orders_items ";
		$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
		$sql .= " AND is_subscription=0 ";
		$sql .= " ORDER BY order_id DESC, order_item_id DESC ";
		$total_order_items = get_db_value($sql);

		if ($total_order_items) {
			// set navigator
			$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_user.php");
			$records_per_page = 25;
			$pages_number = 5;
			$pass_parameters = array("user_id" => $user_id, "tab" => "orders_items");
			$page_number = $n->set_navigator("oi_navigator", "oi_page", MOVING, $pages_number, $records_per_page, $total_order_items, false, $pass_parameters);

			$sql  = " SELECT * FROM " . $table_prefix . "orders_items ";
			$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$sql .= " AND is_subscription=0 ";
			$db->RecordsPerPage = $records_per_page;
			$db->PageNumber = $page_number;              
			$db->query($sql);
			while ($db->next_record()) {
				$order_id = $db->f("order_id");
				$item_name = get_translation($db->f("item_name"));
				$quantity = $db->f("quantity");
				$price = $db->f("price");
				$price_total = $quantity * $price;

				$t->set_var("order_id", $order_id);
				$t->set_var("item_name", $item_name);
				$t->set_var("quantity", $quantity);
				$t->set_var("price", currency_format($price));
				$t->set_var("price_total", currency_format($price_total));
				$t->parse("orders_items", true);
			}
		}
		return $total_order_items;
	}

	function user_subscriptions()
	{
		global $r, $t, $db, $eol, $settings, $date_show_format, $table_prefix, $orders_access;

		$user_id = $r->get_value("user_id");
		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "orders_items ";
		$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
		$sql .= " AND is_subscription=1 ";
		$total_subscriptions = get_db_value($sql);

		if ($total_subscriptions) {
			// set navigator
			$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_user.php");
			$records_per_page = 25;
			$pages_number = 5;
			$pass_parameters = array("user_id" => $user_id, "tab" => "subscriptions");
			$page_number = $n->set_navigator("sbs_navigator", "sbs_page", MOVING, $pages_number, $records_per_page, $total_subscriptions, false, $pass_parameters);

			$sql  = " SELECT * FROM " . $table_prefix . "orders_items ";
			$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$sql .= " AND is_subscription=1 ";
			$sql .= " ORDER BY subscription_expiry_date DESC ";
			$db->RecordsPerPage = $records_per_page;
			$db->PageNumber = $page_number;              
			$db->query($sql);
			while ($db->next_record()) {
				$order_id = $db->f("order_id");
				$item_name = get_translation($db->f("item_name"));
				$price = $db->f("price");
				$start_date = $db->f("subscription_start_date", DATETIME);
				$expiry_date = $db->f("subscription_expiry_date", DATETIME);

				$t->set_var("order_id", $order_id);
				$t->set_var("subscription_order_link", "");
				$t->set_var("subscription_order_id", "");
				if ($orders_access) {
					$t->parse("subscription_order_link", false);
				} else {
					$t->parse("subscription_order_id", false);
				}

				$t->set_var("item_name", $item_name);
				$t->set_var("price", currency_format($price));

				if(is_array($start_date) && is_array($expiry_date)) {
					$current_date_ts = va_timestamp();
					$start_date_ts = va_timestamp($start_date);
					$expiry_date_ts = va_timestamp($expiry_date);
					$t->set_var("subscription_start_date", va_date($date_show_format, $start_date));
					$t->set_var("subscription_expiry_date", va_date($date_show_format, $expiry_date));

					if ($current_date_ts < $start_date_ts) {
						$subscription_status = "<font color=red>" . UPCOMING_MSG . "</font>";
					} else if ($current_date_ts > $expiry_date_ts) {
						$subscription_status = "<font color=red>" . EXPIRED_MSG . "</font>";
					} else {
						$subscription_status = "<font color=blue>" . ACTIVE_MSG . "</font>";
					}
				} else {
					$t->set_var("subscription_start_date", "");
					$t->set_var("subscription_expiry_date", "");
					$subscription_status = "<font color=silver>" . INACTIVE_MSG . "</font>";
				}

				$t->set_var("subscription_status", $subscription_status);
				$t->parse("subscriptions", true);
			}
		}
		return $total_subscriptions;
	}

?>