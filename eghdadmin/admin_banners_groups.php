<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_banners_groups.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	check_admin_security("banners");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_banners_groups.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_banners_groups_href",    "admin_banners_groups.php");
	$t->set_var("admin_banners_group_href",     "admin_banners_group.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_banners_groups.php");
	$s->set_sorter(ID_MSG, "sorter_group_id", "1", "group_id");
	$s->set_sorter(GROUP_NAME_MSG, "sorter_group_name", "2", "group_name");
	$s->set_sorter(IS_ACTIVE_MSG, "sorter_is_active", "3", "is_active");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_banners_groups.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "banners_groups");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$sql  = " SELECT * FROM " . $table_prefix . "banners_groups ";
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		$admin_banners_group_url = new VA_URL("admin_banners_group.php", true);
		$admin_banners_group_url->add_parameter("group_id", DB, "group_id");
		do {
			$is_active = ($db->f("is_active") == 1) ? "<b>".YES_MSG."</b>" : NO_MSG;
			//$operation = ($db->f("is_active") == 1) ? "off" : "on";
			//$shipping_active_url->add_parameter("operation", CONSTANT, $operation);

			$t->set_var("group_id", $db->f("group_id"));
			$t->set_var("group_name", $db->f("group_name"));
			$t->set_var("is_active", $is_active);
			$t->set_var("admin_banners_group_url", $admin_banners_group_url->get_url());

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
