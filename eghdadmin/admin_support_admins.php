<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_admins.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support_users");

	// get permissions
	$permissions = get_permissions();
	$admin_users = get_setting_value($permissions, "admin_users", 0);

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_support_admins.html");

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_admin_edit_href", "admin_support_admin_edit.php");
	$t->set_var("admin_support_admins_href", "admin_support_admins.php");
	$t->set_var("admin_support_password_href", "admin_support_password.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_support_admins.php");
	$s->set_sorter(ID_MSG, "sorter_admin_id", "1", "admin_id");
	$s->set_sorter(LOGIN_BUTTON, "sorter_admin_login", "2", "admin_name");
	$s->set_sorter(USER_NAME_MSG, "sorter_admin_name", "3", "admin_name");
	$s->set_sorter(PRIVILEGE_MSG, "sorter_admin_privilege", "4", "admin_name");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_support_admins.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) ";
	$sql .= " FROM " . $table_prefix . "admins a, " . $table_prefix . "admin_privileges ap ";
	$sql .= " WHERE a.privilege_id=ap.privilege_id ";
	if (!$admin_users) {
		$sql .= " AND ap.support_privilege=1 ";
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
	if (!$admin_users) {
		$sql .= " AND ap.support_privilege=1 ";
	}
	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
			$t->set_var("admin_id", $db->f("admin_id"));
			$t->set_var("admin_name", $db->f("admin_name"));
			$t->set_var("admin_login", $db->f("login"));
			$t->set_var("admin_privilege", $db->f("privilege_name"));
			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

?>