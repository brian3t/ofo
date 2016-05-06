<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_export_custom_help.php                             ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once($root_folder_path . "messages/" . $language_code . "/download_messages.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");

	$cc     = get_param("cc");
	$links  = get_param("links");
	$status = get_param("status");
	$table  = get_param("table");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_export_custom_help.html");
	$t->show_tags = true;

	if ($table == "items") {
		check_admin_security("products_categories");
		include_once("./admin_table_items.php");
	} elseif ($table == "categories") {
		check_admin_security("products_categories");
		include_once("./admin_table_categories.php");
	} elseif ($table == "users") {
		check_admin_security("site_users");
		include_once("./admin_table_users.php");
	} elseif ($table == "orders" || $table == "orders_items") {
		check_admin_security("sales_orders");
		include_once("./admin_table_orders.php");
	} 

	if ($table == "orders_items") {
		$fields = $related_columns;
		$table_alias = $related_table_alias . "_";
	} else {
		$fields = $db_columns;
		$table_alias = "";
	}

	foreach ($fields as $column_name => $column_info) {
		if ($column_info[2] != HIDE_DB_FIELD && $column_info[2] != RELATED_DB_FIELD 
			&& !preg_match("/^order_item_property_/", $column_name)) {
			$t->set_var("field_name", $table_alias . $column_name);
			$t->set_var("field_title", $column_info[0]);
			$t->parse("fields", true);
		}
	}


	$t->pparse("main");

?>