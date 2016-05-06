<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_manufacturers.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once("./admin_common.php");

	check_admin_security("manufacturers");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_manufacturers.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_manufacturer_href", "admin_manufacturer.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_manufacturers.php");
	$s->set_sorter(ID_MSG, "sorter_manufacturer_id", "1", "manufacturer_id");
	$s->set_sorter(ADMIN_ORDER_MSG, "sorter_manufacturer_order", "2", "manufacturer_order");
	$s->set_sorter(MANUFACTURER_NAME_MSG, "sorter_manufacturer_name", "3", "manufacturer_name");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_manufacturers.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "manufacturers");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "manufacturers" . $s->order_by);
	if ($db->next_record())
	{
		$t->set_var("no_records", "");
		$t->parse("sorters", false);
		do {
			$t->set_var("manufacturer_id", $db->f("manufacturer_id"));
			$t->set_var("manufacturer_order", $db->f("manufacturer_order"));
			$t->set_var("manufacturer_name", $db->f("manufacturer_name"));
			$t->parse("records", true);
		} while ($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>