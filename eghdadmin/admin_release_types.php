<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_release_types.php                                  ***
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
  $t->set_file("main","admin_release_types.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_release_type_href", "admin_release_type.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_release_types.php");
	$s->set_sorter(ID_MSG, "sorter_type_id", 1, "type_id");
	$s->set_sorter(TYPE_NAME_MSG , "sorter_type_name", 2, "type_name");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_release_types.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "release_types");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "release_types" . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$t->set_var("type_id", $db->f("type_id"));
			$t->set_var("type_name", $db->f("type_name"));
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
