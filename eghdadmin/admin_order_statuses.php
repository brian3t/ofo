<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_statuses.php                                 ***
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

	check_admin_security("sales_orders");
	check_admin_security("order_statuses");

	$operation = get_param("operation");
	$status_id = get_param("status_id");
	
	if (strlen($operation) && $status_id) {
		if (strtolower($operation) == "off") {
			$sql  = " UPDATE " . $table_prefix . "order_statuses SET is_active=0 ";
			$sql .= " WHERE status_id=" . $db->tosql($status_id, INTEGER);
			$db->query($sql);
		} elseif (strtolower($operation) == "on") {
			$sql  = " UPDATE " . $table_prefix . "order_statuses SET is_active=1 ";
			$sql .= " WHERE status_id=" . $db->tosql($status_id, INTEGER);
			$db->query($sql);
		}
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_order_statuses.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_order_status_href", "admin_order_status.php");
	$t->set_var("admin_order_statuses_href", "admin_order_statuses.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_order_statuses.php");
	$s->set_sorter(ID_MSG, "sorter_status_id", "1", "status_id");
	$s->set_sorter(STATUS_MSG, "sorter_status_name", "2", "status_name");
	$s->set_sorter(ACTIVE_MSG, "sorter_is_active", "3", "is_active");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_order_statuses.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "order_statuses");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT status_id, status_name, is_active FROM " . $table_prefix . "order_statuses" . $s->order_by);
	if ($db->next_record())
	{
		$status_active_url = new VA_URL("admin_order_statuses.php", true, array("status_id", "operation"));
		$status_active_url->add_parameter("status_id", DB, "status_id");

		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
			$is_active = ($db->f("is_active") == 1) ? "<b>" . YES_MSG . "</b>" : NO_MSG;
			$operation = ($db->f("is_active") == 1) ? "off" : "on";
			$status_active_url->add_parameter("operation", CONSTANT, $operation);
			$t->set_var("is_active", $is_active);
			$t->set_var("status_active_url", $status_active_url->get_url());
			$t->set_var("status_id", $db->f("status_id"));
			$t->set_var("status_name", $db->f("status_name"));
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