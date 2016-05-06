<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_polls.php                                          ***
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

	check_admin_security("polls");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_polls.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_poll_href", "admin_poll.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_polls.php");
	$s->set_default_sorting(4, "desc");
	$s->set_sorter(ID_MSG, "sorter_poll_id", "1", "poll_id");
	$s->set_sorter(QUESTION_MSG, "sorter_question", "2", "question");
	$s->set_sorter(IS_ACTIVE_MSG, "sorter_is_active", "3", "is_active");
	$s->set_sorter(POLL_DATE_MSG, "sorter_date_added", "4", "date_added");


	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_polls.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "polls");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "polls " . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$question = get_translation($db->f("question"), $language_code);
			$t->set_var("poll_id", $db->f("poll_id"));
			$t->set_var("question", htmlspecialchars(strip_tags($question)));
			$poll_date = $db->f("date_added", DATETIME);
			$t->set_var("poll_date", va_date($date_show_format, $poll_date));

			$is_active = $db->f("is_active") ? "Yes" : "No";
			$t->set_var("is_active", $is_active);
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