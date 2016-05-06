<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_login.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("users_login");

	$user_id = get_param("user_id");

	$sql  = " SELECT user_type_id,login,password,name,first_name,last_name FROM " . $table_prefix . "users ";
	$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
	$db->query($sql);
	if($db->next_record()) {
		$type_id = $db->f("user_type_id");
		$login = $db->f("login");
		$current_password = $db->f("password");
		$user_name = $db->f("name");
		if (!$user_name) {
			$user_name = trim($db->f("first_name") . " " . $db->f("last_name"));
		}
		if (!$user_name) {
			$user_name = $login;
		}
	} else {
		die(OBJECT_NO_EXISTS_MSG);
	}

	$setting_type = "user_profile_" . $type_id;
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
	$t->set_file("main","admin_user_login.html");
	$t->set_var("login_desc",         $login_desc);
	$t->set_var("user_name",          htmlspecialchars($user_name));
	$t->set_var("admin_user_href",   "admin_user.php");
	$t->set_var("admin_users_href",  "admin_users.php");
	$t->set_var("admin_user_login_href", "admin_user_login.php");

	$r = new VA_Record($table_prefix . "users");

	$r->add_where("user_id", INTEGER);
	$r->add_textbox("login", TEXT, LOGIN_FIELD);
	$r->change_property("login", REQUIRED, true);
	$r->change_property("login", UNIQUE, true);
	if ($login_field_type == 2) {
		$r->change_property("login", REGEXP_MASK, EMAIL_REGEXP);
		$r->change_property("login", REGEXP_ERROR, INCORRECT_EMAIL_MESSAGE);
	} else {
		$r->change_property("login", REGEXP_MASK, ALPHANUMERIC_REGEXP);
		$r->change_property("login", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	}
	$r->change_property("login", TRIM, true);

	$r->add_textbox("password", TEXT, NEW_PASS_FIELD);
	$r->change_property("password", MIN_LENGTH, 3);
	$r->change_property("password", USE_IN_UPDATE, false);
	$r->change_property("password", TRIM, true);
	$r->add_textbox("confirm", TEXT, CONFIRM_PASS_FIELD);
	$r->change_property("confirm", USE_IN_UPDATE, false);
	$r->change_property("confirm", TRIM, true);
	$r->change_property("password", MATCHED, "confirm");

	$operation = get_param("operation");
	$return_page = "admin_users.php";
	$errors = "";
	$r->get_form_values();


	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		if (!$r->is_empty("password")) {
			$r->change_property("password", USE_IN_UPDATE, true);
		}
		
		$r->validate();

		if(!strlen($r->errors))
		{
			$password_encrypt = get_setting_value($settings, "password_encrypt", 0);
			if ($password_encrypt) {
				$r->set_value("password", md5($r->get_value("password")));
			}
			$r->update_record();
			header("Location: " . $return_page);
			exit;
		}
	} else {
		$r->set_value("login", $login);
	}

	$r->set_parameters();
	$t->set_var("current_password", htmlspecialchars($current_password));

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>