<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_departments.php                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support_departments");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_support_departments.html");

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_departments_href", "admin_support_departments.php");
	$t->set_var("admin_support_dep_edit_href", "admin_support_dep_edit.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_support_departments.php");
	$s->set_sorter(ID_MSG, "sorter_dep_id", "1", "dep_id");
	$s->set_sorter(SUPPORT_DEPARTMENT_FIELD, "sorter_short_title", "2", "short_title");
	$s->set_sorter(INCOMING_ACCOUNT_MSG, "sorter_inc_acc", "3", "incoming_account");
	$s->set_sorter(OUTGOING_ACCOUNT_MSG, "sorter_out_acc", "4", "outgoing_account");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_support_departments.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "support_departments");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "support_departments" . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$t->set_var("dep_id", $db->f("dep_id"));
			$t->set_var("short_title", $db->f("short_title"));
			$t->set_var("incoming_account", $db->f("incoming_account"));
			$t->set_var("outgoing_account", $db->f("outgoing_account"));
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
