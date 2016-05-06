<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_google_base_type_attr.php                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	
	check_admin_security("static_google_base_types");
	
	$operation = get_param("operation");
	
	$type_id = get_param("type_id");
	$type_name = get_db_value("SELECT type_name FROM " . $table_prefix . "google_base_types WHERE type_id=" . $db->tosql($type_id, INTEGER, true, false));
	
	$return_page = "admin_google_base_types.php";
	if (!$type_id || !$type_name) {
		header("Location: " . $return_page);
		exit;		
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_google_base_type_attr.html");
		
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_google_base_type_href", "admin_google_base_type.php");
	$t->set_var("admin_google_base_types_href", "admin_google_base_types.php");
	$t->set_var("admin_google_base_type_attr_href","admin_google_base_type_attr.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
		
	$attributes = get_db_values ("SELECT attribute_id, attribute_name FROM " . $table_prefix . "google_base_attributes ORDER BY attribute_name", array(array("", "")));
	$r = new VA_Record($table_prefix . "google_base_types_attributes", "attributes");
	$r->add_hidden("type_id", INTEGER);
	$r->add_select("attribute_id", INTEGER, $attributes, NAME_MSG);
	$r->add_checkbox("required", INTEGER);
	
	$number_attributes = get_param("number_attributes");
	$more_attributes   = get_param("more_attributes");
	
	$attribute = new VA_EditGrid($r, "attributes");
	$attribute->get_form_values($number_attributes);

	if (strlen($operation) && !$more_attributes)	{
		if($operation == "cancel") {
			header("Location: " . $return_page);
			exit;
		} elseif ($operation == "delete" && $type_id) {
			$db->query("DELETE FROM " . $table_prefix . "google_base_types_attributes WHERE type_id=" . $db->tosql($type_id, INTEGER));
			header("Location: " . $return_page);
			exit;
		}
		$is_valid = $attribute->validate();
		if($is_valid)
		{
			$db->query("DELETE FROM " . $table_prefix . "google_base_types_attributes WHERE type_id=" . $db->tosql($type_id, INTEGER));
			$attribute->set_values("type_id", $type_id);
			$attribute->change_property("type_id", USE_IN_INSERT, true);
			for ($i = 1; $i <= $number_attributes; $i++) {
				if ($attribute->set_record($i) && !get_param($attribute->block_name . "_delete_" . $i)) {
					$attribute->record->insert_record();
				}
			}
			header("Location: " . $return_page);
			exit;
		}
	} elseif (strlen($type_id) && !$more_attributes) {
		$attribute->set_value("type_id", $type_id);
		$attribute->change_property("attribute_id", USE_IN_SELECT, true);
		$attribute->change_property("type_id", USE_IN_WHERE, true);
		$attribute->change_property("type_id", USE_IN_SELECT, true);
		$number_attributes = $attribute->get_db_values();
		if($number_attributes == 0)
			$number_attributes = 5;

	} elseif ($more_attributes) {
		$number_attributes += 5;
	} else {
		$number_prices = 5;
	}
	$t->set_var("number_attributes", $number_attributes);
	$attribute->set_parameters_all($number_attributes);
	$t->set_var("type_id", $type_id);
	$t->set_var("type_name", $type_name);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
?>