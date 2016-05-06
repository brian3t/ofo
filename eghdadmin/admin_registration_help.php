<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_registration_help.php                              ***
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
	
	check_admin_security("admin_registration");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_registration_help.html");
	$t->show_tags = true;

	$sql  = " SELECT property_id, property_name, property_order, property_show, control_type ";
	$sql .= " FROM " . $table_prefix . "registration_custom_properties upp ";
	$sql .= " ORDER BY property_order ";
	$db->query($sql);
	while ($db->next_record()) {
		$field_id = $db->f("property_id");
		$field_name = get_translation($db->f("property_name"));
		$t->set_var("field_id",   $field_id);
		$t->set_var("field_name", $field_name);
		$t->parse("custom_fields", true);		
	}
	
	$t->pparse("main");
?>