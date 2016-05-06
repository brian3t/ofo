<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_privileges.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/sorter.php");
	include_once ($root_folder_path . "includes/navigator.php");

	include_once("./admin_common.php");

	check_admin_security("admins_groups");

	$permissions = get_permissions();
	$admins_hidden_permission = get_setting_value($permissions, "admins_hidden", 0);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_privileges.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_privileges_href", "admin_privileges.php");
	$t->set_var("admin_privileges_edit_href", "admin_privileges_edit.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_privileges.php");
	$s->set_sorter(ID_MSG, "sorter_privilege_id", "1", "privilege_id");
	$s->set_sorter(PRIVILEGES_MSG, "sorter_privilege_name", "2", "privilege_name");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_privileges.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "admin_privileges ";
	if (!$admins_hidden_permission) {
		$sql .= " WHERE is_hidden=0 OR is_hidden IS NULL ";
	}
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$sql  = " SELECT privilege_id,privilege_name FROM " . $table_prefix . "admin_privileges ";
	if (!$admins_hidden_permission) {
		$sql .= " WHERE is_hidden=0 OR is_hidden IS NULL ";
	}
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$t->set_var("privilege_id", $db->f("privilege_id"));
			$t->set_var("privilege_name", $db->f("privilege_name"));
			$t->parse("records", true);
		} while($db->next_record());
		$t->parse("titles", false);
	}
	else
	{
		$t->set_var("titles", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>
