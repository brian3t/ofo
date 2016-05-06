<?php

function user_profile_form($block_name)
{
	global $r, $pp, $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id;
	global $months, $parameters, $cc_parameters, $additional_parameters;
	global $datetime_show_format, $date_show_format;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}
	
	$t->set_file("block_body", "block_user_profile.html");

	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");
	$secure_redirect = get_setting_value($settings, "secure_redirect", 0);
	$secure_user_profile = get_setting_value($settings, "secure_user_profile", 0);
	if ($secure_user_profile) {
		$user_profile_url = $secure_url . get_custom_friendly_url("user_profile.php");
	} else {
		$user_profile_url = $site_url . get_custom_friendly_url("user_profile.php");
	}
	$type = get_param("type");
	if (!$is_ssl && $secure_user_profile && $secure_redirect && preg_match("/^https/i", $secure_url)) {
		if ($type) {
			$user_profile_url .= "?type=" . urlencode($type);
		}
		header("Location: " . $user_profile_url);
		exit;
	}

	// get user type settings
	$type_id = ""; $group_sms_allowed = 0; $user_email = ""; $is_subscription = 0;  
	$registration_last_step = 0; $registration_total_steps = 0;
	$user_id = get_session("session_user_id");
	$new_user_id = get_session("session_new_user_id");
	if (strlen($user_id) || strlen($new_user_id)) {
		$sql  = " SELECT ut.type_id, u.email, u.delivery_email, u.registration_last_step, u.registration_total_steps, ";
		$sql .= " u.login, u.name, u.first_name, u.last_name, ";
		$sql .= " ut.is_subscription, ut.is_sms_allowed ";
		if (isset($site_id)) {
			$sql .= " FROM ((" . $table_prefix . "users u ";
		} else {
			$sql .= " FROM (" . $table_prefix . "users u ";
		}
		$sql .= " INNER JOIN  " . $table_prefix . "user_types ut ON u.user_type_id=ut.type_id) ";
		if (isset($site_id)) {
			$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites uts ON uts.type_id=ut.type_id)";
			$sql .= " WHERE (ut.sites_all=1 OR uts.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
		} else {
			$sql .= " WHERE ut.sites_all=1 ";					
		}		
		if (strlen($user_id)) {
			$sql .= " AND u.user_id=" . $db->tosql($user_id, INTEGER);
		} else {
			$sql .= " AND  u.user_id=" . $db->tosql($new_user_id, INTEGER);
		}
		$db->query($sql);
		if ($db->next_record()) {
			$type_id = $db->f("type_id");
			$user_email = $db->f("email");
			$user_login = $db->f("login");
			if (!$user_email) { $user_email = $db->f("delivery_email"); }
			if (!$user_email && preg_match(EMAIL_REGEXP, $user_login)) { $user_email = $user_login; }

			$registration_last_step = $db->f("registration_last_step");
			$registration_total_steps = $db->f("registration_total_steps");
			$group_sms_allowed = $db->f("is_sms_allowed");
			$is_subscription = $db->f("is_subscription");
		}
	} else {
		if (strlen($type)) {
			$sql  = " SELECT ut.type_id, ut.is_sms_allowed, ut.is_subscription ";
			if (isset($site_id)) {
				$sql .= " FROM (" . $table_prefix . "user_types ut";
				$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites uts ON uts.type_id=ut.type_id)";
				$sql .= " WHERE (ut.sites_all=1 OR uts.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
			} else {
				$sql .= " FROM " . $table_prefix . "user_types ut";
				$sql .= " WHERE ut.sites_all=1 ";					
			}
			$sql .= " AND ut.type_id=" . $db->tosql($type, INTEGER) . " AND ut.is_active=1 ";
			$db->query($sql);
			if($db->next_record()) {
				$type_id = $db->f("type_id");
				$group_sms_allowed = $db->f("is_sms_allowed");
				$is_subscription = $db->f("is_subscription");
			}
		} else {
			$sql  = " SELECT ut.type_id,ut.is_sms_allowed, ut.is_subscription ";
			if (isset($site_id)) {
				$sql .= " FROM (" . $table_prefix . "user_types ut"; 
				$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites uts ON uts.type_id=ut.type_id)";
				$sql .= " WHERE (ut.sites_all=1 OR uts.site_id=". $db->tosql($site_id, INTEGER, true, false) . ")";
			} else {
				$sql .= " FROM " . $table_prefix . "user_types ut"; 
				$sql .= " WHERE ut.sites_all=1";					
			}			
			$sql .= " AND ut.is_default=1 AND ut.is_active=1";
			$db->query($sql);
			if ($db->next_record()) {
				$type_id = $db->f("type_id");
				$group_sms_allowed = $db->f("is_sms_allowed");
				$is_subscription = $db->f("is_subscription");
			}
		}
	}
	if (!$registration_last_step) {
		$registration_last_step = 1;
	} elseif ($registration_last_step < $registration_total_steps) {
		$registration_last_step++;
	}

	if (!strlen($type_id)) {
		header ("Location: " . get_custom_friendly_url("user_login.php"));
		exit;
	}

	$setting_type = "user_profile_" . $type_id;
	$user_profile = array();
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT);
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ") ";
		$sql .= " ORDER BY site_id ASC";
	} else {
		$sql .= " AND site_id=1";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$user_profile[$db->f("setting_name")] = $db->f("setting_value");
	}

	$user_settings = array();
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "user_types_settings ";
	$sql .= " WHERE type_id=" . $db->tosql($type_id, INTEGER);
	$db->query($sql);
	while ($db->next_record()) {
		$user_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$sections = array();
	$sql  = " SELECT section_id, step_number, section_name FROM " . $table_prefix . "user_profile_sections ";
	$sql .= " WHERE is_active=1 ORDER BY section_order, section_id ";

	$db->query($sql);
	while ($db->next_record()) {
		$section_id = $db->f("section_id");
		$step_number = $db->f("step_number");
		$section_name = get_translation($db->f("section_name"));
		if (!strlen($user_id)) {
			if ($registration_total_steps < $step_number) {
				$registration_total_steps = $step_number;
			}
		}
		if ($user_id || $step_number == $registration_last_step) {
			$sections[$section_id] = $section_name;
		}
	}

	// prepare custom options
	$pp = array(); $pn = 0;
	$sql  = " SELECT * ";
	$sql .= " FROM " . $table_prefix . "user_profile_properties ";
	$sql .= " WHERE user_type_id=" . $db->tosql($type_id, INTEGER);
	if ($user_id) {
		$sql .= " AND property_show IN (1,3) ";
	} else {
		$sql .= " AND property_show IN (1,2) ";
	}
	$sql .= " ORDER BY property_order, property_id ";
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$pp[$pn]["property_id"] = $db->f("property_id");
			$pp[$pn]["property_order"] = $db->f("property_order");
			$pp[$pn]["property_name"] = get_translation($db->f("property_name"));
			$pp[$pn]["property_description"] = get_translation($db->f("property_description"));
			$pp[$pn]["default_value"] = get_translation($db->f("default_value"));
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
			$pp[$pn]["regexp_error"] = get_translation($db->f("regexp_error"));
			$pp[$pn]["options_values_sql"] = $db->f("options_values_sql");

			$pn++;
		} while ($db->next_record());
	}

	$yes_no_messages =
		array(
			array(1, YES_MSG),
			array(0, NO_MSG)
		);

	$user_ip = get_ip();
	$referer = get_session("session_referer");
	$initial_ip = get_session("session_initial_ip");
	$cookie_ip = get_session("session_cookie_ip");
	$visit_number = get_session("session_visit_number");

	$affiliate_join = get_setting_value($user_settings, "affiliate_join", 0);
	$login_field_type = get_setting_value($user_profile, "login_field_type", 1);
	$short_description_editor = get_setting_value($user_profile, "short_description_editor", 0);
	$full_description_editor = get_setting_value($user_profile, "full_description_editor", 0);
	$subscribe_block = get_setting_value($user_profile, "subscribe_block", 0);

	if ($login_field_type == 2) {
		$login_desc = " (".EMAIL_FIELD.")";
	} else {
		$login_desc = "";
	}

	$use_random_image = get_setting_value($user_profile, "use_random_image", 0);
	if (($use_random_image == 2 && !strlen($new_user_id)) || ($use_random_image == 1 && !strlen($user_id) && !strlen($new_user_id))) {
		$use_validation = true;
	} else {
		$use_validation = false;
	}

	$t->set_var("type", $type_id);

	$t->set_var("site_url", $settings["site_url"]);
	$t->set_var("user_profile_href", get_custom_friendly_url("user_profile.php"));
	$t->set_var("user_profile_url",  $user_profile_url);
	$t->set_var("user_upload_href", get_custom_friendly_url("user_upload.php"));
	$t->set_var("user_home_href", get_custom_friendly_url("user_home.php"));
	$t->set_var("referer", $referer);
	$t->set_var("referrer", $referer);
	$t->set_var("HTTP_REFERER", $referer);
	$t->set_var("initial_ip", $initial_ip);
	$t->set_var("cookie_ip", $cookie_ip);
	$t->set_var("visit_number", $visit_number);
	$t->set_var("login_desc", $login_desc);
	$t->set_var("short_description_editor", $short_description_editor);
	$t->set_var("full_description_editor",  $full_description_editor);

	$subscribe = get_param("subscribe");

	$r = new VA_Record($table_prefix . "users");
	$r->add_where("user_id", INTEGER);
	$r->add_hidden("type", INTEGER);
	$r->add_textbox("user_type_id", INTEGER, "User Type");
	$r->change_property("user_type_id", REQUIRED, true);
	$r->change_property("user_type_id", USE_IN_UPDATE, false);
	$r->add_textbox("is_approved", INTEGER);
	$r->add_textbox("registration_last_step", INTEGER);
	$r->add_textbox("registration_total_steps", INTEGER);
	$r->add_textbox("login", TEXT);
	$r->change_property("login", USE_IN_UPDATE, false);
	$r->change_property("login", SHOW, false);

	// subscription information
	$r->add_textbox("subscription_id", INTEGER, SUBSCRIPTION_MSG);
	$r->change_property("subscription_id", USE_SQL_NULL, false);
	$r->change_property("subscription_id", USE_IN_UPDATE, false);
	if ($is_subscription && !$user_id && !$new_user_id)	{
		$r->change_property("subscription_id", REQUIRED, true);
	}
	if ($is_subscription) {
		$r->add_textbox("expiry_date", DATETIME);
		$r->change_property("expiry_date", USE_IN_UPDATE, false);
		$r->add_textbox("suspend_date", DATETIME);
		$r->change_property("suspend_date", USE_IN_UPDATE, false);
	}
	$r->add_textbox("is_sms_allowed", INTEGER);
	$r->change_property("is_sms_allowed", USE_IN_UPDATE, false);


	// prepare lists for companies, states and countries
	$companies = get_db_values("SELECT company_id,company_name FROM " . $table_prefix . "companies ", array(array("", SELECT_COMPANY_MSG)));
	$states = get_db_values("SELECT state_id,state_name FROM " . $table_prefix . "states WHERE show_for_user=1 ORDER BY state_name ", array(array("", SELECT_STATE_MSG)));
	$countries = get_db_values("SELECT country_id,country_name FROM " . $table_prefix . "countries WHERE show_for_user=1 ORDER BY country_order, country_name ", array(array("", SELECT_COUNTRY_MSG)));

	$login_params = array();
	$affiliate_code_name = "affiliate_code";
	// add controls by sections
	foreach ($sections as $section_id => $section_name) {
		if ($section_id == 1) 
		{
			if (!$user_id && !$new_user_id)
			{
				$r->remove_parameter("login");
				$r->add_textbox("login", TEXT, $section_name.": ".LOGIN_FIELD);
				$r->change_property("login", REQUIRED, true);
				$r->change_property("login", UNIQUE, true);
				$r->change_property("login", MIN_LENGTH, 3);
				if ($login_field_type == 2) {
					$r->change_property("login", REGEXP_MASK, EMAIL_REGEXP);
					$r->change_property("login", REGEXP_ERROR, INCORRECT_EMAIL_MESSAGE);
				} else {
					$r->change_property("login", REGEXP_MASK, ALPHANUMERIC_REGEXP);
					$r->change_property("login", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
				}

				$r->change_property("login", TRIM, true);
				$r->add_textbox("affiliate_code", TEXT, $section_name . ": " . AFFILIATE_CODE_FIELD);
				$r->change_property("affiliate_code", UNIQUE, true);
				$r->change_property("affiliate_code", MIN_LENGTH, 3);
				$r->change_property("affiliate_code", REGEXP_MASK, ALPHANUMERIC_REGEXP);
				$r->change_property("affiliate_code", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
				$r->change_property("affiliate_code", USE_SQL_NULL, false);
				$r->change_property("affiliate_code", TRIM, true);
				if ($affiliate_join) {
					$r->change_property("affiliate_code", REQUIRED, true);
				} else {
					$r->change_property("affiliate_code", SHOW, false);
				}
				$r->add_textbox("password", TEXT, $section_name.": ".PASSWORD_FIELD);
				$r->change_property("password", REQUIRED, true);
				$r->change_property("password", MIN_LENGTH, 5);
				//$r->change_property("password", REGEXP_MASK, ALPHANUMERIC_REGEXP);
				//$r->change_property("password", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
				$r->change_property("password", TRIM, true);

				$r->add_textbox("confirm", TEXT, $section_name.": ".CONFIRM_PASS_FIELD);
				$r->change_property("confirm", USE_IN_SELECT, false);
				$r->change_property("confirm", USE_IN_INSERT, false);
				$r->change_property("confirm", USE_IN_UPDATE, false);
				$r->change_property("confirm", TRIM, true);
				$r->change_property("password", MATCHED, "confirm");

				$r->add_textbox("security_question", TEXT, "Security Question");
				$r->change_property("security_question", USE_SQL_NULL, false);

				$r->add_textbox("security_answer", TEXT, "Security Answer");
				$r->change_property("security_answer", USE_SQL_NULL, false);
				$login_params = array("login", "affiliate_code", "password", "confirm");
				$affiliate_code_name = "affiliate_code";
			} else {
				$r->add_textbox("login", TEXT, LOGIN_FIELD);
				$r->change_property("login", USE_IN_UPDATE, false);
				$r->change_property("login", SHOW, false);
				if ($user_id) {
					$r->add_textbox("affiliate_code_info", TEXT, AFFILIATE_CODE_FIELD);
					$r->change_property("affiliate_code_info", USE_IN_UPDATE, false);
					$r->change_property("affiliate_code_info", COLUMN_NAME, "affiliate_code");
					if (!$affiliate_join) {
						$r->change_property("affiliate_code_info", SHOW, false);
					}
					$login_params = array("affiliate_code_info");
					$affiliate_code_name = "affiliate_code_info";
				}
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
			$r->add_textbox("phone", TEXT, $section_name.": ".PHONE_FIELD);
			$r->change_property("phone", REGEXP_MASK, PHONE_REGEXP);
			$r->add_textbox("daytime_phone", TEXT, $section_name.": ".DAYTIME_PHONE_FIELD);
			$r->change_property("daytime_phone", REGEXP_MASK, PHONE_REGEXP);
			$r->add_textbox("evening_phone", TEXT, $section_name.": ".EVENING_PHONE_FIELD);
			$r->change_property("evening_phone", REGEXP_MASK, PHONE_REGEXP);
			$r->add_textbox("cell_phone", TEXT, $section_name.": ".CELL_PHONE_FIELD);
			$r->change_property("cell_phone", REGEXP_MASK, PHONE_REGEXP);
			$r->add_textbox("fax", TEXT, $section_name.": ".FAX_FIELD);
			$r->change_property("fax", REGEXP_MASK, PHONE_REGEXP);
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
			$r->change_property("personal_image", REGEXP_MASK, "/^\\.?\\/?images\\/users\\//i");
			if (!$user_id || !isset($user_profile["show_personal_image"]) || $user_profile["show_personal_image"] != 1) {
				$r->change_property("personal_image", SHOW, false);
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
			$r->change_property("delivery_phone", REGEXP_MASK, PHONE_REGEXP);
			$r->add_textbox("delivery_daytime_phone", TEXT, $section_name.": ". DAYTIME_PHONE_FIELD);
			$r->change_property("delivery_daytime_phone", REGEXP_MASK, PHONE_REGEXP);
			$r->add_textbox("delivery_evening_phone", TEXT, $section_name.": ". EVENING_PHONE_FIELD);
			$r->change_property("delivery_evening_phone", REGEXP_MASK, PHONE_REGEXP);
			$r->add_textbox("delivery_cell_phone", TEXT, $section_name.": ". CELL_PHONE_FIELD);
			$r->change_property("delivery_cell_phone", REGEXP_MASK, PHONE_REGEXP);
			$r->add_textbox("delivery_fax", TEXT, $section_name.": ". FAX_FIELD);
			$r->change_property("delivery_fax", REGEXP_MASK, PHONE_REGEXP);
		} 
		elseif ($section_id == 4) 
		{
			// additional fields
			$r->add_textbox("nickname", TEXT, NICKNAME_FIELD);
			$r->change_property("nickname", USE_SQL_NULL, false);
			$r->change_property("nickname", REGEXP_MASK, ALPHANUMERIC_REGEXP);
			$r->change_property("nickname", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
			$r->change_property("nickname", AFTER_VALIDATE, "validate_nickname");
			$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
			$r->change_property("friendly_url", USE_SQL_NULL, false);
			$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
			$r->change_property("friendly_url", REGEXP_MASK, ALPHANUMERIC_REGEXP);
			$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
			$r->change_property("friendly_url", TRIM, true);
			$r->add_textbox("paypal_account", TEXT, PAYPAL_ACCOUNT_FIELD);
			$r->change_property("paypal_account", REGEXP_MASK, EMAIL_REGEXP);
			$r->add_textbox("msn_account", TEXT, MSN_ACCOUNT_FIELD);
			$r->change_property("msn_account", REGEXP_MASK, EMAIL_REGEXP);
			$r->add_textbox("icq_number", TEXT, ICQ_NUMBER_FIELD);
			$r->change_property("icq_number", REGEXP_MASK, "/^\d+$/");
			$r->add_textbox("user_site_url", TEXT, USER_SITE_URL_FIELD);
			$r->add_textbox("tax_id", TEXT, TAX_ID_FIELD);
			$r->add_textbox("short_description", TEXT, SHORT_DESCRIPTION_MSG);
			$r->add_textbox("full_description", TEXT, FULL_DESCRIPTION_MSG);
			$r->add_radio("is_hidden", INTEGER, $yes_no_messages, HIDE_MY_ONLINE_STATUS_MSG);
		}

		foreach ($pp as $id => $pp_row) {
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


	$r->add_textbox("validation_number", TEXT, VALIDATION_CODE_FIELD);
	$r->change_property("validation_number", USE_IN_INSERT, false);
	$r->change_property("validation_number", USE_IN_UPDATE, false);
	$r->change_property("validation_number", USE_IN_SELECT, false);
	if ($use_validation) {
		$r->change_property("validation_number", REQUIRED, true);
		$r->change_property("validation_number", SHOW, true);
	} else {
		$r->change_property("validation_number", REQUIRED, false);
		$r->change_property("validation_number", SHOW, false);
	}

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
					$r->change_property($param_name, REQUIRED, true);
				}
			} else {
				$r->change_property($param_name, SHOW, false);
			}
		}

		if ($r->parameter_exists($delivery_param)) {
			$r->change_property($delivery_param, TRIM, true);
			if (isset($user_profile[$show_delivery]) && $user_profile[$show_delivery] == 1) {
				$delivery_number++;
				if ($user_profile[$delivery_param . "_required"] == 1) {
					$r->change_property($delivery_param, REQUIRED, true);
				}
			} else {
				$r->change_property($delivery_param, SHOW, false);
			}
		}
	}

	$additional_number = 0;
	for ($i = 0; $i < sizeof($additional_parameters); $i++)
	{
		$param_name = $additional_parameters[$i];
		if ($r->parameter_exists($param_name)) {
			$r->change_property($param_name, TRIM, true);

			$additional_param = "show_" . $additional_parameters[$i];
			$param_required = $additional_parameters[$i] . "_required";
			if (isset($user_profile[$additional_param]) && $user_profile[$additional_param] == 1) {
				$additional_number++;
				$r->change_property($additional_parameters[$i], SHOW, true);
				if ($user_profile[$param_required] == 1) {
					$r->change_property($additional_parameters[$i], REQUIRED, true);
				}
			} else {
				$r->change_property($additional_parameters[$i], SHOW, false);
			}
		}
	}

	$r->add_textbox("registration_date", DATETIME);
	$r->change_property("registration_date", USE_IN_SELECT, false);
	$r->change_property("registration_date", USE_IN_UPDATE, false);
	$r->add_textbox("registration_ip", TEXT);
	$r->change_property("registration_ip", USE_IN_SELECT, false);
	$r->change_property("registration_ip", USE_IN_UPDATE, false);
	$r->add_textbox("modified_date", DATETIME);
	$r->change_property("modified_date", USE_IN_SELECT, false);
	$r->change_property("modified_date", USE_IN_INSERT, false);
	$r->add_textbox("modified_ip", TEXT);
	$r->change_property("modified_ip", USE_IN_SELECT, false);
	$r->change_property("modified_ip", USE_IN_INSERT, false);
	$r->add_textbox("last_visit_date", DATETIME);
	$r->add_textbox("last_visit_ip", TEXT);
	$r->add_textbox("last_visit_page", TEXT);
	$r->add_textbox("last_logged_date", DATETIME);
	$r->change_property("last_logged_date", USE_IN_INSERT, false);
	$r->change_property("last_logged_date", USE_IN_UPDATE, false);
	$r->add_textbox("last_logged_ip", TEXT);
	$r->change_property("last_logged_ip", USE_IN_INSERT, false);
	$r->change_property("last_logged_ip", USE_IN_UPDATE, false);


	$r->add_checkbox("same_as_personal", INTEGER);
	$r->change_property("same_as_personal", USE_IN_SELECT, false);
	$r->change_property("same_as_personal", USE_IN_INSERT, false);
	$r->change_property("same_as_personal", USE_IN_UPDATE, false);
	if ($personal_number < 1 || $delivery_number < 1) {
		$r->change_property("same_as_personal", SHOW, false);
	}
	$r->add_checkbox("subscribe", INTEGER);
	$r->change_property("subscribe", USE_IN_SELECT, false);
	$r->change_property("subscribe", USE_IN_INSERT, false);
	$r->change_property("subscribe", USE_IN_UPDATE, false);

	if($subscribe_block && 
		(($login_field_type == 2) || ($r->parameter_exists("email") && $r->get_property_value("email", SHOW)) 
		|| ($r->parameter_exists("delivery_email") && $r->get_property_value("delivery_email", SHOW)))) {
		$r->change_property("subscribe", SHOW, true);
	} else {
		$r->change_property("subscribe", SHOW, false);
	}

	$r->get_form_values();
	$r->set_value("user_type_id", $type_id);
	$r->set_value("type", $type_id);
	$r->set_value("registration_last_step", $registration_last_step);
	$r->set_value("registration_total_steps", $registration_total_steps);
	$r->set_value("is_sms_allowed", $group_sms_allowed);

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
	// get name
	if (!$user_email) {
		if ($r->parameter_exists("email")) { $user_email = $r->get_value("email"); }
		if (!$user_email && $r->parameter_exists("delivery_email")) {
			$user_email = $r->get_value("delivery_email");
		}
		if (!$user_email && $login_field_type == 2 && $r->parameter_exists("login")) { 
			$user_email = $r->get_value("login");
		}

	}

	$operation = get_param("operation");
	$return_page = get_param("return_page");
	if (strlen($user_id)) {
		$redirect_page = get_setting_value($user_profile, "update_redirect", get_custom_friendly_url("user_home.php"));
	} elseif ($registration_last_step == $registration_total_steps) {
		if (strlen($return_page)) {
			$redirect_page = $return_page;
		} elseif ($is_subscription) {
			$secure_order_profile = get_setting_value($settings, "secure_order_profile", 0);
			$redirect_page = get_custom_friendly_url("order_info.php");
		} else {
			$redirect_page = get_setting_value($user_profile, "registration_redirect", get_custom_friendly_url("user_home.php"));
		}
	} else {
		$redirect_page = get_custom_friendly_url("user_profile.php");
		if (strlen($return_page)) {
			$redirect_page .= "?return_page=" . urlencode($return_page);
		}
	}
	if ($secure_user_profile && !preg_match("/^http\:\/\//", $redirect_page) && !preg_match("/^https\:\/\//", $redirect_page)) {
		if ($registration_last_step == $registration_total_steps) {
			$redirect_page = $site_url . $redirect_page;
		}
	}

	if (strlen($operation))
	{
		if ($operation == "cancel") {
			header("Location: " . $redirect_page);
			exit;
		} elseif ($operation == "delete" && $user_id) {
			// delete operation disabled for users
			// $r->delete_record();
			header("Location: " . $redirect_page);
			exit;
		}

		if ($r->get_value("same_as_personal")) {
			for ($i = 0; $i < sizeof($parameters); $i++) {
				$show_personal = "show_" . $parameters[$i];
				$show_delivery = "show_delivery_" . $parameters[$i];
				if (isset($user_profile[$show_delivery]) && isset($user_profile[$show_personal]) &&
					$user_profile[$show_delivery] == 1 && $user_profile[$show_personal] == 1) {
					$r->set_value("delivery_" . $parameters[$i], $r->get_value($parameters[$i]));
				}
			}
		}

		if (strlen($user_id)) {
			$r->set_value("user_id", $user_id);
			$r->where_set = true;
		} elseif (strlen($new_user_id)) {
			$r->set_value("user_id", $new_user_id);
			$r->where_set = true;
		}

		$r->validate();

		if ($use_validation) {
			if ($r->is_empty("validation_number")) {
				$r->errors .= str_replace("{field_name}", VALIDATION_CODE_FIELD, VALIDATION_MESSAGE);
			} else {
				$validated_number = check_image_validation($r->get_value("validation_number"));
				if (!$validated_number) {
					$r->errors .= str_replace("{field_name}", VALIDATION_CODE_FIELD, VALIDATION_MESSAGE);
				} elseif ($r->errors) {
					// saved validated number for following submits	
					set_session("session_validation_number", $validated_number);
				}
			} 
		}

		if (strlen($user_id)) {
			if (!isset($user_settings["edit_profile"]) || $user_settings["edit_profile"] != 1) {
				$r->errors = EDIT_PROFILE_ERROR;
			}
		} else {
			if (!isset($user_settings["new_profile"]) || $user_settings["new_profile"] != 1) {
				$r->errors = NEW_PROFILE_ERROR;
			}
		}

		if (!$r->errors && check_black_ip()) {
			$r->errors = BLACK_IP_MSG;
		}
		
		if (!strlen($r->errors))
		{
			// subscribe/unsubscribe user from newsletter
			if ($user_email) {
				if ($r->get_value("subscribe") == 1) {
					$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "newsletters_users ";
					$sql .= " WHERE email=" . $db->tosql($user_email, TEXT);
					$db->query($sql);
					$db->next_record();
					$email_count = $db->f(0);
					if ($email_count < 1) {
						$sql  = " INSERT INTO " . $table_prefix . "newsletters_users (email, date_added) ";
						$sql .= " VALUES (";
						$sql .= $db->tosql($user_email, TEXT) . ", ";
						$sql .= $db->tosql(va_time(), DATETIME) . ") ";
						$db->query($sql);
					}
				} else {
					$sql  = " DELETE FROM " . $table_prefix . "newsletters_users ";
					$sql .= " WHERE email=" . $db->tosql($user_email, TEXT);
					$db->query($sql);
				}
			}
			if (isset($user_settings["approve_profile"]) && $user_settings["approve_profile"] == 1) {
				$r->set_value("is_approved", 1);
			} else {
				$r->set_value("is_approved", 0);
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

			$friendly_auto = get_setting_value($settings, "friendly_auto", 0);
			$show_friendly_url = get_setting_value($user_profile, "show_friendly_url", 0);
			if (strlen($user_id) || strlen($new_user_id))
			{
				if (strlen($new_user_id)) {
					if ($friendly_auto == 2 && $r->parameter_exists("friendly_url") && $r->is_empty("friendly_url")) {
						set_friendly_url();
					}
				} else {
					if ($friendly_auto == 1) {
						$r->remove_parameter("friendly_url");
						$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
						$r->change_property("friendly_url", USE_SQL_NULL, false);
						set_friendly_url();
					} elseif ($friendly_auto == 2 && $r->parameter_exists("friendly_url") && $r->is_empty("friendly_url")) {
						set_friendly_url();
					}
				}
				$r->set_value("modified_date", va_time());
				$r->set_value("modified_ip", $user_ip);
				$r->set_value("last_visit_date", va_time());
				$r->set_value("last_visit_ip", $user_ip);
				$r->set_value("last_visit_page", get_custom_friendly_url("user_profile.php"));
				$r->update_record();
				update_user_properties();

			} else {
				if ($friendly_auto == 1 || !$show_friendly_url) {
					$r->remove_parameter("friendly_url");
					$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
					$r->change_property("friendly_url", USE_SQL_NULL, false);
					if ($friendly_auto) { set_friendly_url(); }
				} elseif (!$r->parameter_exists("friendly_url")) {
					$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
					$r->change_property("friendly_url", USE_SQL_NULL, false);
					if ($friendly_auto) { set_friendly_url(); }
				} elseif ($friendly_auto == 2 && $r->is_empty("friendly_url")) {
					set_friendly_url();
				}

				// get email from login data
				if ($login_field_type == 2 && !get_setting_value($user_profile, "show_email", 0)) {
					$r->set_value("email", $r->get_value("login"));
				}

				if ($db_type == "postgre") {
					$user_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "users') ");
					$r->change_property("user_id", USE_IN_INSERT, true);
					$r->set_value("user_id", $user_id);
				}
				$registration_date = va_time();
				$r->set_value("registration_date", $registration_date);
				$r->set_value("registration_ip", $user_ip);
				$r->set_value("last_visit_date", $registration_date);
				$r->set_value("last_visit_ip", $user_ip);
				$r->set_value("last_visit_page", get_custom_friendly_url("user_profile.php"));
				if ($is_subscription) {
					$expiry_date = va_time();
					// set expiry_date and suspend_date as yesterday
					$expiry_date_ts = mktime (0,0,0, $expiry_date[MONTH], $expiry_date[DAY] - 1, $expiry_date[YEAR]);
					$r->set_value("expiry_date", $expiry_date_ts); 
					$r->set_value("suspend_date", $expiry_date_ts);
				}

				$password_encrypt = get_setting_value($settings, "password_encrypt", 0);
				$plain_password = $r->get_value("password");
				set_session("session_plain_password", $plain_password);
				if ($password_encrypt == 1) {
					$r->set_value("password", md5($plain_password));
				}

				if ($r->insert_record())
				{
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
					$new_user_id = $user_id;
					set_session("session_new_user_id", $user_id);
					set_session("session_new_user_type_id", $type_id);
				}
			}
			if ($new_user_id && ($registration_total_steps == 1 || $registration_last_step == $registration_total_steps)) {
				$new_user_added = true;
			} else {
				$new_user_added = false;
			}

			// if user pass all steps
			if ($new_user_added) {
				// add subscription to the cart
				if ($is_subscription) {
					set_session("session_new_user", "expired");
					include_once("./includes/shopping_cart.php");
					add_subscription($type_id, $r->get_value("subscription_id"), $subscription_name);
				}

				// if user approved and he don't need to pay for his account login him automatically
				if ($r->get_value("is_approved") == 1 && !$is_subscription) {
					if ($user_id) {
						user_login("", "", $user_id, 0, "", false, $errors);
					} else {
						user_login("", "", $new_user_id, 0, "", false, $errors);
					}
				}
			}

			// notifications block
			if ($new_user_added) {
				$registration_date = $r->get_value("registration_date");
				$registration_date_string = va_date($datetime_show_format, $registration_date);
				$admin_notification = get_setting_value($user_profile, "admin_notification", 0);
				$user_notification  = get_setting_value($user_profile, "user_notification", 0);
				$admin_sms = get_setting_value($user_profile, "admin_sms_notification", 0);
				$user_sms  = get_setting_value($user_profile, "user_sms_notification", 0);
				if ($admin_notification || $user_notification || $admin_sms || $user_sms)
				{
					foreach ($r->parameters as $key => $parameter)
					{
						$value = $r->get_value_desc($key);
						$t->set_var($key, $value);
					}

					$company_id = ""; $state_id = 0; $country_id = 0;
					$delivery_company_id = ""; $delivery_state_id = 0; $delivery_country_id = 0;
					$sql = " SELECT * FROM " . $table_prefix . "users ";
					if (strlen($user_id)) {
						$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
					} else {
						$sql .= " WHERE user_id=" . $db->tosql($new_user_id, INTEGER);
					}
					$db->query($sql);
					if ($db->next_record()) {
						$t->set_vars($db->Record);
						$company_id = $db->f("company_id");
						$state_id = $db->f("state_id");
						$country_id = $db->f("country_id");
						$delivery_company_id = $db->f("delivery_company_id");
						$delivery_state_id   = $db->f("delivery_state_id");
						$delivery_country_id = $db->f("delivery_country_id");
					}
					$t->set_var("registration_date", $registration_date_string);
					$plain_password = get_session("session_plain_password");
					if ($plain_password) {
						$t->set_var("password", $plain_password);
						set_session("session_plain_password", "");
					}

					$company_select = $company_id ? get_array_value($company_id, $companies) : "";
					$state = $state_id ? get_array_value($state_id, $states) : "";
					$country = $country_id ? get_array_value($country_id, $countries) : "";
					$delivery_company_select = $delivery_company_id ? get_array_value($delivery_company_id, $companies) : "";
					$delivery_state = $delivery_state_id ? get_array_value($delivery_state_id, $states) : "";
					$delivery_country = $delivery_country_id ? get_array_value($delivery_country_id, $countries) : "";

					$t->set_var("company_select", $company_select);
					$t->set_var("state", $state);
					$t->set_var("country", $country);
					$t->set_var("delivery_company_select", $delivery_company_select);
					$t->set_var("delivery_state", $delivery_state);
					$t->set_var("country", $country);
					$t->set_var("delivery_country", $delivery_country);

					// parse custom fields
					$custom_fields = array();
					$sql  = " SELECT upp.property_id, upp.control_type, upp.property_name, upp.property_description ";
					$sql .= " FROM " . $table_prefix . "user_profile_properties upp ";
					$sql .= " WHERE upp.user_type_id=" . $db->tosql($type_id, INTEGER);
					$sql .= " ORDER BY upp.property_order, upp.property_id ";
					$db->query($sql);
					while ($db->next_record()) {
						$field_id = $db->f("property_id");
						$control_type = $db->f("control_type");
						$property_name = get_translation($db->f("property_name"));

						$custom_fields[$field_id] = array(
							"name" => $property_name, "type" => $control_type, "values" => array(),
						);
					}
					foreach($custom_fields as $field_id => $field) {
						$sql  = " SELECT up.property_value ";
						$sql .= " FROM " . $table_prefix . "users_properties up ";
						$sql .= " WHERE up.property_id=" . $db->tosql($field_id, INTEGER);
						if (strlen($user_id)) {
							$sql .= " AND up.user_id=" . $db->tosql($user_id, INTEGER);
						} else {
							$sql .= " AND  up.user_id=" . $db->tosql($new_user_id, INTEGER);
						}
						$db->query($sql);
						while ($db->next_record()) {
							$property_value = get_translation($db->f("property_value"));
							$custom_fields[$field_id]["values"][] = $property_value;
						}
					}
					// check values for listbox values
					foreach($custom_fields as $field_id => $field) {
						$control_type = $field["type"];
						if ($control_type == "CHECKBOXLIST" || $control_type == "LISTBOX" || $control_type == "RADIOBUTTON") {
							$values = $field["values"];
							foreach($values as $value_id => $property_value_id) {
								if (is_numeric($property_value_id)) {
									$sql  = " SELECT upv.property_value ";
									$sql .= " FROM " . $table_prefix . "user_profile_values upv ";
									$sql .= " WHERE upv.property_value_id=" . $db->tosql($property_value_id, INTEGER);
									$db->query($sql);
									if ($db->next_record()) {
										$property_value = get_translation($db->f("property_value"));
										$custom_fields[$field_id]["values"][$value_id] = $property_value;
									}
								}
							}
						}
					}
					foreach($custom_fields as $field_id => $field) {
						$field_name = $field["name"];
						$field_value = join("; ", $field["values"]);
						$t->set_var("field_name_" . $field_id, $field_name);
						$t->set_var("field_value_" . $field_id, $field_value);
						$t->set_var("field_" . $field_id, $field_value);
					}
					// end parsing custom fields
				}
				$eol = get_eol();
				if ($admin_notification)
				{
					$admin_subject = get_setting_value($user_profile, "admin_subject", "");
					$admin_message = get_setting_value($user_profile, "admin_message", "");
					$admin_subject = get_translation($admin_subject);
					$admin_message = get_translation($admin_message);

					$t->set_block("admin_subject", $admin_subject);
					$t->set_block("admin_message", $admin_message);

					$t->parse("admin_subject", false);
					$t->parse("admin_message", false);

					$mail_to = get_setting_value($user_profile, "admin_email", $settings["admin_email"]);
					$email_headers = array();
					$email_headers["from"] = get_setting_value($user_profile, "admin_mail_from", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($user_profile, "cc_emails");
					$email_headers["bcc"] = get_setting_value($user_profile, "admin_mail_bcc");
					$email_headers["reply_to"] = get_setting_value($user_profile, "admin_mail_reply_to");
					$email_headers["return_path"] = get_setting_value($user_profile, "admin_mail_return_path");
					$email_headers["mail_type"] = get_setting_value($user_profile, "admin_message_type");

					$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
					va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers);
				}
				if ($user_notification && $user_email)
				{
					$user_subject = get_setting_value($user_profile, "user_subject", "");
					$user_message = get_setting_value($user_profile, "user_message", "");
					$user_subject = get_translation($user_subject);
					$user_message = get_translation($user_message);

					$t->set_block("user_subject", $user_subject);
					$t->set_block("user_message", $user_message);
					$t->parse("user_subject", false);
					$t->parse("user_message", false);
					$email_headers = array();
					$email_headers["from"] = get_setting_value($user_profile, "user_mail_from", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($user_profile, "user_mail_cc");
					$email_headers["bcc"] = get_setting_value($user_profile, "user_mail_bcc");
					$email_headers["reply_to"] = get_setting_value($user_profile, "user_mail_reply_to");
					$email_headers["return_path"] = get_setting_value($user_profile, "user_mail_return_path");
					$email_headers["mail_type"] = get_setting_value($user_profile, "user_message_type");

					$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
					va_mail($user_email, $t->get_var("user_subject"), $user_message, $email_headers);
				}

				if ($admin_sms)
				{
					$admin_sms_recipient  = get_setting_value($user_profile, "admin_sms_recipient", "");
					$admin_sms_originator = get_setting_value($user_profile, "admin_sms_originator", "");
					$admin_sms_message    = get_setting_value($user_profile, "admin_sms_message", "");

					$t->set_block("admin_sms_recipient",  $admin_sms_recipient);
					$t->set_block("admin_sms_originator", $admin_sms_originator);
					$t->set_block("admin_sms_message",    $admin_sms_message);

					$t->parse("admin_sms_recipient", false);
					$t->parse("admin_sms_originator", false);
					$t->parse("admin_sms_message", false);

					sms_send($t->get_var("admin_sms_recipient"), $t->get_var("admin_sms_message"), $t->get_var("admin_sms_originator"));
				}

				if ($user_sms)
				{
					$user_sms_recipient  = get_setting_value($user_profile, "user_sms_recipient", $r->get_value("cell_phone"));
					$user_sms_originator = get_setting_value($user_profile, "user_sms_originator", "");
					$user_sms_message    = get_setting_value($user_profile, "user_sms_message", "");

					$t->set_block("user_sms_recipient",  $user_sms_recipient);
					$t->set_block("user_sms_originator", $user_sms_originator);
					$t->set_block("user_sms_message",    $user_sms_message);

					$t->parse("user_sms_recipient", false);
					$t->parse("user_sms_originator", false);
					$t->parse("user_sms_message", false);

					if (sms_send_allowed($t->get_var("user_sms_recipient"))) {
						sms_send($t->get_var("user_sms_recipient"), $t->get_var("user_sms_message"), $t->get_var("user_sms_originator"));
					}
				}
			}

			header("Location: " . $redirect_page);
			exit;
		}
	} elseif (strlen($user_id) || strlen($new_user_id))	{
		if (strlen($user_id)) {
			$r->set_value("user_id", $user_id);
		} else {
			$r->set_value("user_id", $new_user_id);
		}
		$r->get_db_values();
		select_user_properties();
		if ($user_email) {
			$sql  = " SELECT email_id FROM " . $table_prefix . "newsletters_users ";
			$sql .= " WHERE email=" . $db->tosql($user_email, TEXT);
			$db->query($sql);
			if ($db->next_record()) {
				$r->set_value("subscribe", 1);
			}
		}
	} else { // new record (set default values)
	}

	$t->set_var("return_page", htmlspecialchars($return_page));
	if ($r->parameter_exists($affiliate_code_name)) {
		$af_param = $r->get_value($affiliate_code_name);
		if (!strlen($af_param)) {
			$af_param = "type_your_code_here";
			if ($user_id) {
				$r->change_property($affiliate_code_name, SHOW, false);
			}
		}
		$affiliate_url = $site_url . "?af=" . $af_param;
		$affiliate_code_help = str_replace("{affiliate_url}", $affiliate_url, AFFILIATE_CODE_HELP_MSG);
		$t->set_var("affiliate_code_help", $affiliate_code_help);
	}

	foreach ($pp as $id => $pp_row) {
		$param_name = "pp_" . $pp_row["property_id"];
		if ($r->parameter_exists($param_name)) {
			$r->change_property($param_name, SHOW, false);
		}
	}
	$r->set_parameters();

	$eol = get_eol();

	$properties_ids = "";
	foreach ($sections as $section_id => $section_name) {
		$t->set_var("profile_section", "");
		$t->set_var("profile_properties", "");
		$section_properties = 0;

		$displayed_profile_properties = array();
		$displayed_profile_properties_orders = array();
		if ($section_id == 1) {
			for ($i = 0; $i < sizeof($login_params); $i++)
			{
				$param_name = $login_params[$i];
				if ($r->get_property_value($param_name, SHOW)) {
					$section_properties++;					
					$order = get_setting_value($user_profile, $param_name . "_order", $section_properties);
					$displayed_profile_properties[$param_name] = $t->get_var($param_name . "_block");		
					$displayed_profile_properties_orders[$param_name] = $order;
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
					$order = get_setting_value($user_profile, $param_name . "_order", $section_properties);
					$displayed_profile_properties[$param_name] = $t->get_var($param_name . "_block");
					$displayed_profile_properties_orders[$param_name] = $order;
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
					$order = get_setting_value($user_profile, $param_name . "_order", $section_properties);
					$displayed_profile_properties[$param_name] = $t->get_var($param_name . "_block");
					$displayed_profile_properties_orders[$param_name] = $order;
					$t->set_var($param_name . "_block", "");
				}
			}
			$t->set_var("DELIVERY_DETAILS_MSG", $section_name);
			$t->parse_to("delivery", "profile_section");
		} elseif ($section_id == 4) {
			for ($i = 0; $i < sizeof($additional_parameters); $i++)
			{
				$param_name = $additional_parameters[$i];
				if ($r->get_property_value($param_name, SHOW)) {
					$section_properties++;
					$order = get_setting_value($user_profile, $param_name . "_order", $section_properties);
					$displayed_profile_properties[$param_name] = $t->get_var($param_name . "_block");
					$displayed_profile_properties_orders[$param_name] = $order;
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
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code. "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
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

					$t->parse("profile_properties", false);
					$displayed_profile_properties["pp_" . $property_id] = $t->get_var("profile_properties");
					$displayed_profile_properties_orders["pp_" . $property_id] = $property_order;
				}
			}

			$t->set_var("properties_ids", $properties_ids);
		}
		// end custom options

		$profile_properties = "";
		asort($displayed_profile_properties_orders);
		foreach ($displayed_profile_properties_orders AS $key=>$order) {
			$profile_properties .= $displayed_profile_properties[$key];
		}
		$t->set_var("profile_properties", $profile_properties);
		if ($section_properties) {
			$t->parse("profile_sections", true);
		}
	}

	$t->set_var("save_button", "");
	$t->set_var("update_button", "");
	$t->set_var("register_button", "");
	if (strlen($user_id)) {
		if (isset($user_settings["edit_profile"]) && $user_settings["edit_profile"] == 1) {
			$t->set_var("save_button_title", UPDATE_BUTTON);
			$t->global_parse("save_button", false, false, true);
			$t->global_parse("update_button", false, false, true);
		}
	} else {
		if ($registration_total_steps != 1) {
			$regisration_step = str_replace("{current_step}", $registration_last_step, STEP_NUMBER_MSG);
			$regisration_step = str_replace("{total_steps}", $registration_total_steps, $regisration_step);
			$t->set_var("regisration_step", $regisration_step);
		}
		if (isset($user_settings["new_profile"]) && $user_settings["new_profile"] == 1) {
			if ($registration_total_steps == 1 || $registration_last_step == $registration_total_steps) {
				$t->set_var("save_button_title", REGISTER_BUTTON);
			} else {
				$t->set_var("save_button_title", CONTINUE_BUTTON);
			}
			$t->global_parse("save_button", false, false, true);
			$t->global_parse("register_button", false, false, true);
		}
	}

	if ($is_subscription) {

		$t->set_var("subscription_id", "");
		$subscriptions_values = array();
		$sql  = " SELECT * FROM " . $table_prefix . "subscriptions ";
		$sql .= " WHERE user_type_id=" . $db->tosql($type_id, INTEGER);
		if (strlen($user_id))	{
			$sql .= " AND subscription_id=" . $db->tosql($r->get_value("subscription_id"), INTEGER);
		}
		$sql .= " AND is_active=1 ";
		$db->query($sql);
		while ($db->next_record()) {
			$type_subscription_id = $db->f("subscription_id");
			$subscription_name = get_translation($db->f("subscription_name"));
			$subscription_fee = $db->f("subscription_fee");
			$subscription_period = $db->f("subscription_period");
			$subscription_interval = $db->f("subscription_interval");
			$subscription_suspend = $db->f("subscription_suspend");
			if (strlen($user_id) || strlen($new_user_id))	{
				$subscription_id_checked = ($type_subscription_id == $r->get_value("subscription_id")) ? " checked " : "";
			} else {
				$subscription_id_checked = ($db->f("is_default") == 1) ? " checked " : "";
			}

			if ($subscription_interval == 1) {
				$subscription_periods = array(1 => DAY_MSG, 2 => WEEK_MSG, 3 => MONTH_MSG, 4 => YEAR_MSG);
				$period_message = "1 " . $subscription_periods[$subscription_period];
			} else {
				$subscription_periods = array(1 => DAYS_QTY_MSG, 2 => WEEKS_QTY_MSG, 3 => MONTHS_QTY_MSG, 4 => YEARS_QTY_MSG);
				$period_message = $subscription_periods[$subscription_period];
				$period_message = str_replace("{quantity}", $subscription_interval, $period_message);
			}
			$t->set_var("subscription_id_value", $type_subscription_id);
			$t->set_var("subscription_id_checked", $subscription_id_checked);
			$t->set_var("subscription_name", $subscription_name);
			$t->set_var("subscription_fee", currency_format($subscription_fee));
			$t->set_var("subscription_period", $period_message);
			$t->parse("subscription_id", true);
		}

		if (strlen($user_id))	{
			$expiry_date = $r->get_value("expiry_date");
			if (is_array($expiry_date)) {
				$t->set_var("expiry_date", va_date($date_show_format, $expiry_date));
				$t->parse("expiry_date_info", false);
			}
			if ($r->get_value("subscription_id")) {
				$t->parse("current_subscription", false);
			}
			if (is_array($expiry_date) || $r->get_value("subscription_id")) {
				$t->parse("subscription_title", false);
			}
		} else {
			$t->parse("subscription_title", false);
			$t->parse("subscription_options", false);
		}
	}

	if (strlen($user_id) && strlen(trim(get_setting_value($user_profile, "intro_text_registered"))))
	{
		$t->set_var("intro_text", get_translation($user_profile["intro_text_registered"]));
		$t->parse("intro_block", false);
	} elseif (!strlen($user_id) && strlen(trim(get_setting_value($user_profile, "intro_text_new")))) {
		$t->set_var("intro_text", get_translation($user_profile["intro_text_new"]));
		$t->parse("intro_block", false);
	}
	
	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

function update_user_properties()
{
	global $r, $pp, $db, $table_prefix;

	$user_id = $r->get_value("user_id");

	foreach ($pp as $id => $data) {
		$property_id = $data["property_id"];
		$param_name = "pp_" . $property_id;
		if ($r->parameter_exists($param_name)) {
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
				for($i = 0; $i < sizeof($values); $i++) {
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
}

function select_user_properties()
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
			$error_message = str_replace("{field_name}", $r->get_property_value("nickname", CONTROL_DESC), UNIQUE_MESSAGE);
			$r->errors .= $error_message . "<br>" . $eol;
		} else {
			// check nickname in admins table
			$sql  = " SELECT admin_id FROM " . $table_prefix . "admins ";
			$sql .= " WHERE (nickname=" . $db->tosql($nickname, TEXT) . " OR admin_name=" . $db->tosql($nickname, TEXT) . ") ";
			$db->query($sql);
			if ($db->next_record()) {
				$error_message = str_replace("{field_name}", $r->get_property_value("nickname", CONTROL_DESC), UNIQUE_MESSAGE);
				$r->errors .= $error_message . "<br>" . $eol;
			}
		}

	}

}

?>