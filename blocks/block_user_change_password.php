<?php

	check_user_session();

	$t->set_file("block_body","block_user_change_password.html");
	$t->set_var("user_change_password_href", get_custom_friendly_url("user_change_password.php"));
	$t->set_var("user_home_href", get_custom_friendly_url("user_home.php"));


	$r = new VA_Record($table_prefix . "users");

	$r->add_where("user_id", INTEGER);
	$r->add_textbox("modified_date", DATETIME);
	$r->add_textbox("current_password", TEXT, CURRENT_PASS_FIELD);
	$r->change_property("current_password", USE_IN_UPDATE, false);
	$r->change_property("current_password", REQUIRED, true);
	$r->add_textbox("password", TEXT, NEW_PASS_FIELD);
	$r->change_property("password", REQUIRED, true);
	$r->change_property("password", MIN_LENGTH, 5);
	$r->add_textbox("confirm", TEXT, CONFIRM_PASS_FIELD);
	$r->change_property("confirm", USE_IN_UPDATE, false);
	$r->change_property("password", MATCHED, "confirm");

	$action = get_param("action");
	$user_id = get_session("session_user_id");
	$site_url = get_setting_value($settings, "site_url", "");
	$secure_user_profile = get_setting_value($settings, "secure_user_profile", 0);
	$return_page = $site_url . get_custom_friendly_url("user_home.php");
	$errors = "";
	$r->get_form_values();


	if(strlen($action))
	{
		if($action == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		
		$r->validate();
		$password_encrypt = get_setting_value($settings, "password_encrypt", 0);

		if(!$r->is_empty("current_password")) {
			$current_password = $r->get_value("current_password");
			if ($password_encrypt == 1) {
				$password_match = md5($current_password);
			} else {
				$password_match = $current_password;
			}

			$sql  = " SELECT password FROM " . $table_prefix . "users WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$sql .= " AND password=" . $db->tosql($password_match, TEXT);
			$db->query($sql);
			if(!$db->next_record()) {
				$r->errors .= str_replace("{field_name}", $r->parameters["current_password"][CONTROL_DESC], INCORRECT_VALUE_MESSAGE);
			}
		} 

		if(!strlen($r->errors))
		{
			if ($password_encrypt) {
				$r->set_value("password", md5($r->get_value("password")));
			}
			$r->set_value("user_id", $user_id);
			$r->set_value("modified_date", va_time());
			$r->update_record();
			header("Location: " . $return_page);
			exit;
		}
	}

	$r->set_parameters();

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>