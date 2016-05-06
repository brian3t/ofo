<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_payment_systems.php                                ***
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

	check_admin_security("payment_systems");

	$operation = get_param("operation");
	$payment_id = get_param("payment_id");
	
	if (strlen($operation) && $payment_id) {
		if (strtolower($operation) == "off") {
			$sql  = " UPDATE " . $table_prefix . "payment_systems SET is_active=0 ";
			$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
			$db->query($sql);
		} elseif (strtolower($operation) == "on") {
			$sql  = " UPDATE " . $table_prefix . "payment_systems SET is_active=1 ";
			$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
			$db->query($sql);
		}
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_payment_systems.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_main", "admin.php");
	$t->set_var("admin_payment_system_href",   "admin_payment_system.php");
	$t->set_var("admin_payment_predefined_href", "admin_payment_predefined.php");
	$t->set_var("admin_credit_card_info_href", "admin_credit_card_info.php");
	$t->set_var("admin_order_final_href",      "admin_order_final.php");
	$t->set_var("admin_recurring_settings_href", "admin_recurring_settings.php");


	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_payment_systems.php");
	$s->set_parameters(false, true, true, false);
	$s->set_default_sorting(4, "asc");
	$s->set_sorter(ID_MSG, "sorter_payment_id", "1", "payment_id");
	$s->set_sorter(PAYMENT_SYSTEM_NAME_MSG, "sorter_payment_name", "2", "payment_name");
	$s->set_sorter(ADMIN_ORDER_MSG, "sorter_payment_order", "3", "payment_order, payment_id");
	$s->set_sorter(ACTIVE_MSG, "sorter_is_active", "4", "is_active", "is_active DESC, payment_order, payment_id ", "is_active ASC");
	$s->set_sorter(DEFAULT_MSG, "sorter_is_default", "5", "is_default");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_payment_systems.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "payment_systems");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "payment_systems " . $s->order_by);
	
	if ($db->next_record())
	{
		$t->set_var("no_records", "");
		do
		{
			$payment_id = $db->f("payment_id");
			$is_active = $db->f("is_active");
			$is_default = $db->f("is_default");
			$active = ($is_active == 1) ? "<b>Yes</b>" : NO_MSG;
			$is_default = ($is_default == 1) ? "<b>Yes</b>" : NO_MSG;
			$operation = ($is_active == 1) ? "Off" : "On";
			$t->set_var("payment_id", $payment_id);
			$t->set_var("payment_name", get_translation($db->f("payment_name")));
			$t->set_var("payment_order", $db->f("payment_order"));
			$t->set_var("active", $active);
			$t->set_var("is_default", $is_default);
			$t->set_var("operation", $operation);
			$t->parse("records", true);
		} while ($db->next_record());
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