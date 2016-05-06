<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_shipping_modules.php                               ***
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

	check_admin_security("shipping_methods");

	$operation = get_param("operation");
	$module_id = get_param("module_id");
	
	if (strlen($operation) && $module_id) {
		if (strtolower($operation) == "off") {
			$sql  = " UPDATE " . $table_prefix . "shipping_modules SET is_active=0 ";
			$sql .= " WHERE shipping_module_id=" . $db->tosql($module_id, INTEGER);
			$db->query($sql);
		} elseif (strtolower($operation) == "on") {
			$sql  = " UPDATE " . $table_prefix . "shipping_modules SET is_active=1 ";
			$sql .= " WHERE shipping_module_id=" . $db->tosql($module_id, INTEGER);
			$db->query($sql);
		}
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_shipping_modules.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_shipping_module_href", "admin_shipping_module.php");
	$t->set_var("admin_shipping_types_href", "admin_shipping_types.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_shipping_modules.php");
	$s->set_sorter(ID_MSG, "sorter_shipping_module_id", "1", "shipping_module_id");
	$s->set_sorter(SHIPPING_MODULE_MSG, "sorter_shipping_module_name", "2", "shipping_module_name");
	$s->set_sorter(ACTIVE_MSG, "sorter_is_active", "3", "is_active");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_shipping_modules.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "shipping_modules");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT shipping_module_id, shipping_module_name, is_active FROM " . $table_prefix . "shipping_modules" . $s->order_by);
	if ($db->next_record())
	{
		$shipping_active_url = new VA_URL("admin_shipping_modules.php", true, array("module_id", "operation"));
		$shipping_active_url->add_parameter("module_id", DB, "shipping_module_id");

		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
			$is_active = ($db->f("is_active") == 1) ? "<b>" . YES_MSG . "</b>" : NO_MSG;
			$operation = ($db->f("is_active") == 1) ? "off" : "on";
			$shipping_active_url->add_parameter("operation", CONSTANT, $operation);
			$t->set_var("is_active", $is_active);
			$t->set_var("shipping_active_url", $shipping_active_url->get_url());
			$t->set_var("shipping_module_id", $db->f("shipping_module_id"));
			$t->set_var("shipping_module_name", $db->f("shipping_module_name"));		
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