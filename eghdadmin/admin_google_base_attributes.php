<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_google_base_attributes.php                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("static_google_base_attributes");

	$attribute_types =
		array(
			"g"=>GLOBAL_MSG, "c"=>CUSTOM_MSG
		);
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_google_base_attributes.html");

	$t->set_var("admin_google_base_attribute_href", "admin_google_base_attribute.php");
	$t->set_var("admin_google_base_attributes_href", "admin_google_base_attributes.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	
	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_google_base_attributes.php");
	$s->set_sorter(ID_MSG, "sorter_attribute_id", "1", "attribute_id");
	$s->set_sorter(NAME_MSG, "sorter_attribute_name", "2", "attribute_name");
	$s->set_sorter(TYPE_MSG, "sorter_attribute_type", "3", "attribute_type");
	$s->set_sorter(VALUE_TYPE_MSG, "sorter_value_type", "3", "value_type");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_google_base_attributes.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$operation = get_param("operation");

	$error_message = ""; $success_message = "";

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "google_base_attributes ");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	
	$sql  = " SELECT * FROM " . $table_prefix . "google_base_attributes ";
	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$t->set_var("attribute_id", $db->f("attribute_id"));
			$t->set_var("attribute_name", htmlspecialchars($db->f("attribute_name")));
			$attribute_type = $db->f("attribute_type");
			if (isset($attribute_types[$attribute_type])) {
				$attribute_type = $attribute_types[$attribute_type];
			}
			$t->set_var("attribute_type", htmlspecialchars($attribute_type));
			$value_type = $db->f("value_type");
			$t->set_var("value_type", htmlspecialchars($value_type));			
			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	if ($error_message) {
		$t->set_var("errors_list", $error_message);
		$t->sparse("errors", false);
	} 

	if ($success_message) {
		$t->set_var("success_message", $success_message);
		$t->sparse("success", false);
	} 

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");
?>