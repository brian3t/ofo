<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_shipping_types.php                                 ***
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
	include_once ($root_folder_path."messages/".$language_code."/cart_messages.php");

	check_admin_security("shipping_methods");

	$shipping_module_id = get_param("shipping_module_id");
	$operation = get_param("operation");
	$type_id = get_param("type_id");

	$sql = " SELECT shipping_module_name FROM " . $table_prefix . "shipping_modules WHERE shipping_module_id=" . $db->tosql($shipping_module_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$shipping_module_name = get_translation($db->f("shipping_module_name"));
	} else {
		header ("Location: admin_shipping_modules.php");
		exit;
	}

	
	if (strlen($operation) && $type_id) {
		if (strtolower($operation) == "off") {
			$sql  = " UPDATE " . $table_prefix . "shipping_types SET is_active=0 ";
			$sql .= " WHERE shipping_type_id=" . $db->tosql($type_id, INTEGER);
			$db->query($sql);
		} elseif (strtolower($operation) == "on") {
			$sql  = " UPDATE " . $table_prefix . "shipping_types SET is_active=1 ";
			$sql .= " WHERE shipping_type_id=" . $db->tosql($type_id, INTEGER);
			$db->query($sql);
		}
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_shipping_types.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href",    "admin_lookup_tables.php");
	$t->set_var("admin_shipping_modules_href", "admin_shipping_modules.php");
	$t->set_var("admin_shipping_module_href",  "admin_shipping_module.php");
	$t->set_var("admin_shipping_type_href",    "admin_shipping_type.php");
	$t->set_var("shipping_module_id",          $shipping_module_id);
	$t->set_var("shipping_module_name",        $shipping_module_name);

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_shipping_types.php");
	$s->set_sorter(ID_MSG, "sorter_shipping_type_id", "1", "shipping_type_id");
	$s->set_sorter(SHIPPING_TYPE_MSG, "sorter_shipping_type_desc", "2", "shipping_type_desc");
	$s->set_sorter(ACTIVE_MSG, "sorter_is_active", "3", "is_active");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_shipping_types.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "shipping_types WHERE shipping_module_id=" . $db->tosql($shipping_module_id, INTEGER));
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "shipping_types WHERE shipping_module_id=" . $db->tosql($shipping_module_id, INTEGER) . $s->order_by);
	if ($db->next_record())
	{
		$shipping_active_url = new VA_URL("admin_shipping_types.php", true, array("type_id", "operation"));
		$shipping_active_url->add_parameter("type_id", DB, "shipping_type_id");

		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
			$is_active = ($db->f("is_active") == 1) ? "<b>".YES_MSG."</b>" : NO_MSG;
			$operation = ($db->f("is_active") == 1) ? "off" : "on";
			$shipping_active_url->add_parameter("operation", CONSTANT, $operation);

			$t->set_var("shipping_type_id", $db->f("shipping_type_id"));
			$t->set_var("shipping_type_desc", get_translation($db->f("shipping_type_desc")));
			$t->set_var("is_active", $is_active);
			$t->set_var("shipping_active_url", $shipping_active_url->get_url());
			
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
