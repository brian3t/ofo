<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_forum_priorities.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path."includes/sorter.php");
	include_once($root_folder_path."includes/navigator.php");
	include_once($root_folder_path."messages/".$language_code."/forum_messages.php");
	include_once("./admin_common.php");

	check_admin_security("forum");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_forum_priorities.html");

	$t->set_var("admin_forum_href", "admin_forum.php");
	$t->set_var("admin_forum_priority_href", "admin_forum_priority.php");
	$t->set_var("admin_forum_priorities_href", "admin_forum_priorities.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_forum_priorities.php");
	$s->set_sorter(ID_MSG, "sorter_priority_id", "1", "priority_id");
	$s->set_sorter(PRIORITY_NAME_MSG, "sorter_priority_name", "2", "priority_name");
	$s->set_sorter(IS_DEFAULT, "sorter_is_default", "3", "is_default");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_forum_priorities.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "forum_priorities");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "forum_priorities" . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$is_default = ($db->f("is_default") == 1) ? "<b>".YES_MSG."</b>" : NO_MSG;

			$t->set_var("priority_id", $db->f("priority_id"));
			$t->set_var("priority_name", $db->f("priority_name"));
			$t->set_var("is_default", $is_default);
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
