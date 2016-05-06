<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_friendly_urls.php                                  ***
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

	check_admin_security("custom_friendly_urls");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_friendly_urls.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_friendly_url_href", "admin_friendly_url.php");
	$t->set_var("admin_friendly_urls_href", "admin_friendly_urls.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_friendly_urls.php");
	$s->set_sorter(ID_MSG, "sorter_friendly_id", 1, "friendly_id");
	$s->set_sorter(SCRIPT_NAME_MSG, "sorter_script_name", 2, "script_name");
	$s->set_sorter(FRIENDLY_URL_MSG, "sorter_friendly_url", 3, "friendly_url");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_friendly_urls.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "friendly_urls ";
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = "SELECT * FROM " . $table_prefix . "friendly_urls ";
	$sql .= $s->order_by;
	$db->query($sql);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		$t->parse("sorters", false);
		do {

			$t->set_var("friendly_id", $db->f("friendly_id"));
			$t->set_var("script_name", $db->f("script_name"));
			$t->set_var("friendly_url", $db->f("friendly_url"));

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
