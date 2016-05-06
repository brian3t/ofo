<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_sections.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once("./admin_common.php");

	check_admin_security("static_tables");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_user_sections.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_user_section_href", "admin_user_section.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_user_sections.php");
	$s->set_default_sorting("1", "asc");
	$s->set_sorter(ADMIN_ORDER_MSG, "sorter_section_order", 1, "section_order", "section_order, section_id", "section_order DESC, section_id");
	$s->set_sorter(SECTION_NAME_MSG, "sorter_section_name", 2, "section_name");
	$s->set_sorter(IS_ACTIVE_MSG, "sorter_is_active", 3, "is_active");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_user_sections.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "user_profile_sections");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "user_profile_sections" . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$section_name = get_translation($db->f("section_name"));
			$is_active = ($db->f("is_active") == 1) ? YES_MSG : NO_MSG;
			
			$t->set_var("section_id", $db->f("section_id"));
			$t->set_var("section_order", $db->f("section_order"));
			$t->set_var("section_name", $section_name);
			$t->set_var("is_active", $is_active);
			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>
