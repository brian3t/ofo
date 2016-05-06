<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_admins.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	check_admin_security("admin_users");

	$permissions = get_permissions();
	$add_admins = get_setting_value($permissions, "add_admins", 0);
	$admins_hidden_permission = get_setting_value($permissions, "admins_hidden", 0);

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_admins.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_admin_href", "admin_admin.php");
	$t->set_var("admin_admins_href", "admin_admins.php");
	$t->set_var("admin_admin_password_href", "admin_admin_password.php");
	$t->set_var("admin_settings_list", "admin_settings_list.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_admins.php");
	$s->set_sorter(ID_MSG, "sorter_admin_id", "1", "admin_id");
	$s->set_sorter(LOGIN_BUTTON, "sorter_login", "2", "login");
	$s->set_sorter(NAME_MSG, "sorter_admin_name", "3", "admin_name");
	$s->set_sorter(TYPE_MSG, "sorter_privilege_name", "4", "privilege_name");
	$s->set_sorter(EMAIL_MSG, "sorter_email", "5", "email");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_admins.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) ";
	$sql .= " FROM " . $table_prefix . "admins a, " . $table_prefix . "admin_privileges ap ";
	$sql .= " WHERE a.privilege_id=ap.privilege_id ";
	if (!$admins_hidden_permission) {
		$sql .= " AND (a.is_hidden=0 OR a.is_hidden IS NULL) AND (ap.is_hidden=0 OR ap.is_hidden IS NULL) ";
	}
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT a.admin_id, a.admin_name, a.login, a.email, ap.privilege_name ";
	$sql .= " FROM " . $table_prefix . "admins a, " . $table_prefix . "admin_privileges ap ";
	$sql .= " WHERE a.privilege_id=ap.privilege_id ";
	if (!$admins_hidden_permission) {
		$sql .= " AND (a.is_hidden=0 OR a.is_hidden IS NULL) AND (ap.is_hidden=0 OR ap.is_hidden IS NULL) ";
	}

	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$t->set_var("admin_id", $db->f("admin_id"));
			$t->set_var("login", $db->f("login"));
			$t->set_var("admin_name", $db->f("admin_name"));
			$t->set_var("privilege_name", $db->f("privilege_name"));
			$t->set_var("email", $db->f("email"));
			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	if ($add_admins) {
		$t->parse("new_admin_link", false);
	}

	$t->pparse("main");

?>