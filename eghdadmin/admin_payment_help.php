<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_payment_help.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("payment_systems");

	$payment_id = get_param("payment_id");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->show_tags = true;
	$t->set_file("main", "admin_payment_help.html");
	$t->set_var("cart_fields", "");
	$t->set_var("details_fields", "");
	$t->set_var("payment_fields", "");

	$sql  = " SELECT property_id, property_type, property_name ";
	$sql .= " FROM " . $table_prefix . "order_custom_properties ";
	if ($payment_id > 0) {
		$sql .= " WHERE (property_type=4 AND payment_id=" . $db->tosql($payment_id, INTEGER) . ") ";
		$sql .= " OR property_type IN (1,2,3) ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$field_id = $db->f("property_id");
		$field_type = $db->f("property_type");
		$field_name = get_translation($db->f("property_name"));
		$t->set_var("field_id",   $field_id);
		$t->set_var("field_name", $field_name);
		if ($field_type == 1) {
			$t->parse("cart_fields", true);
		} elseif ($field_type == 2 || $field_type == 3) {
			$t->parse("details_fields", true);
		} elseif ($field_type == 4) {
			$t->parse("payment_fields", true);
		}
	}

	$t->pparse("main");

?>