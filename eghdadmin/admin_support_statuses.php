<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_statuses.php                               ***
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

	check_admin_security("support_static_data");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_support_statuses.html");

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_status_href", "admin_support_status.php");
	$t->set_var("admin_support_statuses_href", "admin_support_statuses.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_support_statuses.php");
	$s->set_sorter(ID_MSG, "sorter_status_id", "1", "status_id");
	$s->set_sorter(STATUS_NAME_MSG, "sorter_status_name", "2", "status_name");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_support_statuses.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "support_statuses");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT status_id, status_name, is_internal FROM " . $table_prefix . "support_statuses" . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$t->set_var("status_id", $db->f("status_id"));
						if ($db->f("is_internal") == "1") {
							$t->set_var("status_name", $db->f("status_name") . " (Internal)");
		    	}
				else {
							$t->set_var("status_name", $db->f("status_name"));
				}
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