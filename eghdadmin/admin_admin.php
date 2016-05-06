<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_admin.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/download_messages.php");
	include_once("./admin_common.php");

	check_admin_security("admin_users");

	$param_admin_id = get_param("admin_id");
	$permissions = get_permissions();
	$add_admins = get_setting_value($permissions, "add_admins", 0);
	$update_admins = get_setting_value($permissions, "update_admins", 0);
	$remove_admins = get_setting_value($permissions, "remove_admins", 0);
	$admins_hidden_permission = get_setting_value($permissions, "admins_hidden", 0);

	if ($param_admin_id && !$admins_hidden_permission) {
		$sql  = " SELECT a.is_hidden AS admin_hidden, ap.is_hidden AS group_hidden ";
		$sql .= " FROM " . $table_prefix . "admins a, " . $table_prefix . "admin_privileges ap ";
		$sql .= " WHERE a.privilege_id=ap.privilege_id ";
		$sql .= " AND a.admin_id=" . $db->tosql($param_admin_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$admin_hidden = $db->f("admin_hidden");
			$group_hidden = $db->f("group_hidden");
			if ($admin_hidden || $group_hidden) {
				header("Location: admin_admins.php");
				exit;
			}
		} else {
			header("Location: admin_admins.php");
			exit;
		}
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_admin.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_admins_href", "admin_admins.php");
	$t->set_var("admin_admin_href",  "admin_admin.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");
	$t->set_var("PERSONAL_IMAGE_FIELD", PERSONAL_IMAGE_FIELD);
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMINISTRATORS_MSG, CONFIRM_DELETE_MSG));

	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$r = new VA_Record($table_prefix . "admins");
	$r->return_page = "admin_admins.php";
	$r->add_where("admin_id", INTEGER);

	if (!$param_admin_id)
	{
		$r->add_textbox("login", TEXT, LOGIN_BUTTON);
		$r->parameters["login"][REQUIRED] = true;
		$r->parameters["login"][UNIQUE] = true;
		$r->parameters["login"][MIN_LENGTH] = 3;
		$r->add_textbox("password", TEXT, PASSWORD_FIELD);
		$r->parameters["password"][REQUIRED] = true;
		$r->parameters["password"][MIN_LENGTH] = 3;
		$r->add_textbox("confirm", TEXT, CONFIRM_PASS_FIELD);
		$r->change_property("confirm", USE_IN_SELECT, false);
		$r->change_property("confirm", USE_IN_INSERT, false);
		$r->change_property("confirm", USE_IN_UPDATE, false);
		$r->change_property("password", MATCHED, "confirm");
	}

	if ($admins_hidden_permission) {
		$r->add_checkbox("is_hidden", INTEGER);
	}
	$r->add_textbox("admin_name", TEXT, ADMINISTRATOR_NAME_MSG);
	$r->change_property("admin_name", REQUIRED, true);
	$r->add_textbox("nickname", TEXT, NICKNAME_FIELD);
	$r->change_property("nickname", USE_SQL_NULL, false);
	$r->change_property("nickname", AFTER_VALIDATE, "validate_nickname");
	$r->add_textbox("personal_image", TEXT, ADMINISTRATOR_NAME_MSG);
	$r->add_textbox("admin_alias", TEXT, INITIALS_OR_ALIAS_MSG);
	$r->change_property("admin_alias", UNIQUE, true);
	$r->change_property("admin_alias", REQUIRED, true);

	$privileges = get_db_values("SELECT privilege_id, privilege_name FROM " . $table_prefix . "admin_privileges", "");
	$r->add_select("privilege_id", INTEGER, $privileges, PRIVILEGE_TYPE_MSG);
	$r->change_property("privilege_id", REQUIRED, true);

	$r->add_textbox("email", TEXT, EMAIL_MSG);
	$r->change_property("email", REQUIRED, true);
	$r->change_property("email", REGEXP_MASK, EMAIL_REGEXP);

	$r->add_textbox("signature", TEXT, SIGNATURE_MSG);

	$r->events[BEFORE_INSERT] = "encrypt_admin_password";
	$r->events[AFTER_SELECT] = "check_hidden_permissions";

	$r->operations[INSERT_ALLOWED] = $add_admins;
	$r->operations[UPDATE_ALLOWED] = $update_admins;
	$r->operations[DELETE_ALLOWED] = $remove_admins;
	$r->process();

	if (!$param_admin_id)
	{
		$t->parse("login_information", false);
	}	

	$t->pparse("main");

	function encrypt_admin_password()
	{
		global $r, $settings;
		$password_encrypt = get_setting_value($settings, "password_encrypt", 0);
		$admin_password_encrypt = get_setting_value($settings, "admin_password_encrypt", $password_encrypt);
		if ($admin_password_encrypt == 1) {
			$r->set_value("password", md5($r->get_value("password")));
		}
	}

	function validate_nickname()
	{
		global $r, $db, $eol, $table_prefix;
		$nickname = $r->get_value("nickname");
		if (strlen($nickname)) {
			$param_admin_id = $r->get_value("admin_id");
			$sql  = " SELECT admin_id FROM " . $table_prefix . "admins ";
			$sql .= " WHERE (nickname=" . $db->tosql($nickname, TEXT) . " OR admin_name=" . $db->tosql($nickname, TEXT) . ") ";
			if (strlen($param_admin_id)) { 
				$sql .= " AND NOT (admin_id=" . $db->tosql($param_admin_id, INTEGER) . ")"; 
			} 
			$db->query($sql);
			if ($db->next_record()) {
				$error_message = str_replace("{field_name}", $r->parameters["nickname"][CONTROL_DESC], UNIQUE_MESSAGE);
				$r->errors .= $error_message . "<br>" . $eol;
			} else {
				// check nickname in users table
				$sql  = " SELECT user_id FROM " . $table_prefix . "users ";
				$sql .= " WHERE (nickname=" . $db->tosql($nickname, TEXT) . " OR login=" . $db->tosql($nickname, TEXT) . ") ";
				$db->query($sql);
				if ($db->next_record()) {
					$error_message = str_replace("{field_name}", $r->parameters["nickname"][CONTROL_DESC], UNIQUE_MESSAGE);
					$r->errors .= $error_message . "<br>" . $eol;
				}
			}
		}
	}


?>