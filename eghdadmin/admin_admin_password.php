<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_admin_password.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("admins_login");

	$admin_id = get_param("admin_id");
	$permissions = get_permissions();
	$update_admins = get_setting_value($permissions, "update_admins", 0);
	$admins_hidden_permission = get_setting_value($permissions, "admins_hidden", 0);

	$sql  = " SELECT a.login, a.password, a.admin_name, a.is_hidden AS admin_hidden, ap.is_hidden AS group_hidden ";
	$sql .= " FROM " . $table_prefix . "admins a, " . $table_prefix . "admin_privileges ap ";
	$sql .= " WHERE a.privilege_id=ap.privilege_id ";
	$sql .= " AND a.admin_id=" . $db->tosql($admin_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$login = $db->f("login");
		$current_password = $db->f("password");
		$admin_name = $db->f("admin_name");
		$admin_hidden = $db->f("admin_hidden");
		$group_hidden = $db->f("group_hidden");
		if (!$admins_hidden_permission && ($admin_hidden || $group_hidden)) {
			header ("Location: admin_admins.php");
			exit;
		}
	} else {
		header ("Location: admin_admins.php");
		exit;
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_admin_password.html");
	$t->set_var("admin_name",         $admin_name);
	$t->set_var("admin_admin_href",   "admin_admin.php");
	$t->set_var("admin_admins_href",  "admin_admins.php");
	$t->set_var("admin_admin_password_href", "admin_admin_password.php");

	$r = new VA_Record($table_prefix . "admins");
	$r->return_page = "admin_admins.php";

	$r->add_where("admin_id", INTEGER);
	$r->add_textbox("login", TEXT, LOGIN_FIELD);
	$r->change_property("login", REQUIRED, true);
	$r->change_property("login", UNIQUE, true);
	$r->add_textbox("password", TEXT, NEW_PASS_FIELD);
	$r->change_property("password", MIN_LENGTH, 3);
	$r->change_property("password", USE_IN_UPDATE, false);
	$r->add_textbox("confirm", TEXT, CONFIRM_PASS_FIELD);
	$r->change_property("confirm", USE_IN_UPDATE, false);
	$r->change_property("confirm", USE_IN_SELECT, false);
	$r->change_property("password", MATCHED, "confirm");

	$r->events[BEFORE_UPDATE] = "set_password_field";
	$r->events[AFTER_SELECT] = "clear_password_field";

	$r->operations[INSERT_ALLOWED] = false;
	$r->operations[UPDATE_ALLOWED] = $update_admins;

	$r->process();

	$r->set_parameters();
	$t->set_var("current_password", htmlspecialchars($current_password));

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

function set_password_field()
{
	global $r, $settings;

	if (!$r->is_empty("password")) {
		$password_encrypt = get_setting_value($settings, "password_encrypt", 0);
		$admin_password_encrypt = get_setting_value($settings, "admin_password_encrypt", $password_encrypt);
		$r->change_property("password", USE_IN_UPDATE, true);
		if ($admin_password_encrypt) {
			$r->set_value("password", md5($r->get_value("password")));
		}
	}
}

function clear_password_field()
{
	global $r;
	$r->set_value("password", "");
}

?>