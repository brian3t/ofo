<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_newsletters.php                                    ***
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

	check_admin_security("newsletter");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_newsletters.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_newsletter_href",  "admin_newsletter.php");
	$t->set_var("admin_newsletters_href", "admin_newsletters.php");
	$t->set_var("admin_newsletter_send_href", "admin_newsletter_send.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_newsletters.php");
	$s->set_default_sorting(3, "desc");
	$s->set_sorter(ID_MSG, "sorter_newsletter_id", "1", "newsletter_id");
	$s->set_sorter(EMAIL_SUBJECT_MSG, "sorter_newsletter_subject", "2", "newsletter_subject");
	$s->set_sorter(DATE_MSG, "sorter_newsletter_date", "3", "newsletter_date");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_newsletters.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "newsletters ");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT * FROM " . $table_prefix . "newsletters ";
	$db->query($sql . $s->order_by);
	if ($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
			$newsletter_subject = $db->f("newsletter_subject");
			$newsletter_date = $db->f("newsletter_date", DATETIME);
			$newsletter_date = va_date($datetime_show_format, $newsletter_date);
			$is_sent = $db->f("is_sent");
			$is_active = $db->f("is_active");
			$emails_sent = $db->f("emails_sent");
			if ($is_sent) {
				$status = SENT_MSG;
			} elseif (!$is_active) {
				$status = INACTIVE_MSG;
			} elseif ($emails_sent > 0) {
				$status = SENDING_NOT_FINISHED_MSG;
			} else {
				$status = READY_FOR_SENDING_MSG;
			}
			$t->set_var("newsletter_id", $db->f("newsletter_id"));
			$t->set_var("newsletter_subject", $newsletter_subject);
			$t->set_var("newsletter_date", $newsletter_date);
			$t->set_var("newsletter_status", $status);
			$t->parse("records", true);
		} while ($db->next_record());
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