<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_articles_statuses.php                              ***
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

	check_admin_security("articles_statuses");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_articles_statuses.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_articles_top_href", "admin_articles_top.php");
	$t->set_var("admin_articles_statuses_href", "admin_articles_statuses.php");
	$t->set_var("admin_articles_status_href", "admin_articles_status.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_articles_statuses.php");
	$s->set_sorter(ID_MSG, "sorter_status_id", "1", "status_id");
	$s->set_sorter(STATUS_MSG, "sorter_status_name", "2", "status_name");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_articles_statuses.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "articles_statuses ");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "articles_statuses " . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
			$t->set_var("status_id", $db->f("status_id"));
			$t->set_var("status_name", $db->f("status_name"));
			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>
