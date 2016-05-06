<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_google_base_types.php                              ***
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

	check_admin_security("static_google_base_types");	

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_google_base_types.html");

	$t->set_var("admin_google_base_type_href", "admin_google_base_type.php");
	$t->set_var("admin_google_base_type_attr_href", "admin_google_base_type_attr.php");
	$t->set_var("admin_google_base_types_href", "admin_google_base_types.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	
	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_google_base_types.php");
	$s->set_sorter(ID_MSG, "sorter_type_id", "1", "type_id");
	$s->set_sorter(NAME_MSG, "sorter_type_name", "2", "type_name");
	$s->set_sorter(ATTRIBUTES_MSG, "sorter_a_count", "3", "a_count");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_google_base_types.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$operation = get_param("operation");

	$error_message = ""; $success_message = "";

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "google_base_types ");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	
	$sql  = " SELECT t.type_id, t.type_name, COUNT(ta.attribute_id) AS a_count FROM ( " . $table_prefix . "google_base_types t ";
	$sql .= " LEFT JOIN " . $table_prefix . "google_base_types_attributes ta ON  ta.type_id = t.type_id) ";
	$sql .= " GROUP BY t.type_id, t.type_name ";
	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$t->set_var("type_id", $db->f("type_id"));
			$t->set_var("type_name", htmlspecialchars($db->f("type_name")));
			$t->set_var("a_count", htmlspecialchars($db->f("a_count")));
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