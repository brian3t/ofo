<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_profile_help.php                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/admin_messages.php");

	check_admin_security("users_groups");

	$type = get_param("type");
	$user_type = get_param("user_type");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_user_profile_help.html");
	$t->show_tags = true;

	if ($type == "reset") {
		$t->parse("reset_info", false);
	} else {
		$t->set_var("reset_info", "");
	}

	if ($type == "reminder") {
		$t->parse("reminder_info", false);
	} else {
		$t->set_var("reminder_info", "");
	}


	$sql  = " SELECT upp.property_id, upp.property_name ";
	$sql .= " FROM " . $table_prefix . "user_profile_properties upp ";
	$sql .= " WHERE upp.user_type_id=" . $db->tosql($user_type, INTEGER);
	$sql .= " ORDER BY upp.property_order, upp.property_id ";
	$db->query($sql);
	if ($db->next_record()) {

		$t->parse("fields_title", true);
		do {
			$field_id = $db->f("property_id");
			$field_name = get_translation($db->f("property_name"));
			$t->set_var("field_id",   $field_id);
			$t->set_var("field_name", $field_name);
			$t->parse("fields", true);
		} while ($db->next_record());
	}

	$t->pparse("main");

?>