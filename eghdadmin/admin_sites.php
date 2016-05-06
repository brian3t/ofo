<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_sites.php                                          ***
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

	check_admin_security("admin_sites");

	$permissions = get_permissions();
	$add_sites   = get_setting_value($permissions, "add_sites", 0);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_sites.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_site_href", "admin_site.php");
	$t->set_var("admin_sites_href", "admin_sites.php");
	$t->set_var("admin_global_settings_href", "admin_global_settings.php");
	$t->set_var("admin_cms_href", "admin_cms.php");
	$t->set_var("admin_site_items_href", "admin_site_items.php");
	

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_sites.php");
	$s->set_sorter(ID_MSG, "sorter_param_site_id", "1", "site_id");
	$s->set_sorter(NAME_MSG, "sorter_site_name", "2", "site_name");
	$s->set_sorter(DESCRIPTION_MSG, "sorter_site_description", "3", "site_description");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_sites.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) ";
	$sql .= " FROM " . $table_prefix . "sites s";

	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT s.site_id, s.site_name, s.site_description ";
	$sql .= " FROM " . $table_prefix . "sites s";

	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$t->set_var("param_site_id", $db->f("site_id"));
			$t->set_var("site_name", $db->f("site_name"));
			$t->set_var("site_description", $db->f("site_description"));
			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	if ($add_sites) {
		$t->parse("new_site_link", false);
	}

	$t->pparse("main");

?>