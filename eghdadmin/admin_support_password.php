<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.5                                                  ***
  ***      File:  admin_support_password.php                               ***
  ***      Built: Tue Aug 19 15:38:10 2008                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support_users");

	// get permissions
	$admin_id = get_param("admin_id");
	$permissions = get_permissions();
	$admin_users = get_setting_value($permissions, "admin_users", 0);
	$support_privilege = 0;

	$sql  = " SELECT a.admin_name, ap.support_privilege ";
	$sql .= " FROM " . $table_prefix . "admins a, " . $table_prefix . "admin_privileges ap ";
	$sql .= " WHERE a.privilege_id=ap.privilege_id ";
	$sql .= " AND a.admin_id=" . $db->tosql($admin_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$support_privilege = $db->f("support_privilege");
		$admin_name = $db->f("admin_name");
	}
	if (!$admin_users && $support_privilege != 1) {
		header ("Location: admin_support_admins.php");
		exit;
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_support_password.html");
	$t->set_var("admin_name",         $admin_name);
	$t->set_var("admin_support_href",   "admin_support.php");
	$t->set_var("admin_support_admins_href",  "admin_support_admins.php");
	$t->set_var("admin_support_admin_edit_href", "admin_support_admin_edit.php");
	$t->set_var("MY_ACCOUNT_MSG",      MY_ACCOUNT_MSG);
	$t->set_var("CHANGE_PASSWORD_MSG", CHANGE_PASSWORD_MSG);
	$t->set_var("CURRENT_PASS_FIELD",  CURRENT_PASS_FIELD);
	$t->set_var("NEW_PASS_FIELD",      NEW_PASS_FIELD);
	$t->set_var("CONFIRM_PASS_FIELD",  CONFIRM_PASS_FIELD);
	$t->set_var("UPDATE_BUTTON",       UPDATE_BUTTON);
	$t->set_var("CANCEL_BUTTON",       CANCEL_BUTTON);
	$t->set_var("ENTER_YOUR_MSG",      ENTER_YOUR_MSG);
	$t->set_var("CHOOSE_A_MSG",        CHOOSE_A_MSG);

	$r = new VA_Record($table_prefix . "admins");

	$r->add_where("admin_id", INTEGER);
	$r->add_textbox("password", TEXT, NEW_PASS_FIELD);
	$r->change_property("password", REQUIRED, true);
	$r->change_property("password", MIN_LENGTH, 5);
	$r->add_textbox("confirm", TEXT, CONFIRM_PASS_FIELD);
	$r->change_property("confirm", USE_IN_UPDATE, false);
	$r->change_property("password", MATCHED, "confirm");

	$operation = get_param("operation");
	$return_page = "admin_support_admins.php";
	$errors = "";
	$r->get_form_values();


	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
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
	}

	$r->set_parameters();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>