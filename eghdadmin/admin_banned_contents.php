<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_banned_contents.php                                ***
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

	check_admin_security("banned_contents");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_banned_contents.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_banned_content_href", "admin_banned_content.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_banned_contents.php");
	$s->set_sorter(ID_MSG, "sorter_content_id", 1, "content_id");
	$s->set_sorter(TEXT_MSG, "sorter_content_text", 2, "content_text");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_banned_contents.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "banned_contents");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "banned_contents" . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", "");
		$t->set_var("no_records", "");
		do {
			$t->set_var("content_id", $db->f("content_id"));
			$t->set_var("content_text", htmlspecialchars($db->f("content_text")));

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