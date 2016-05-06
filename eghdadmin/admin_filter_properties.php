<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_filter_properties.php                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("filters");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_filter_properties.html");

	$t->set_var("admin_filter_href",     "admin_filter.php");
	$t->set_var("admin_filters_href",    "admin_filters.php");
	$t->set_var("admin_filter_property_href",   "admin_filter_property.php");
                              
	$filter_id = get_param("filter_id");
	$t->set_var("filter_id", htmlspecialchars($filter_id));

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_filter_properties.php");
	$s->set_parameters(false, true, true, false);
	$s->set_default_sorting(3, "asc");
	$s->set_sorter(ID_MSG, "sorter_property_id", "1", "property_id");
	$s->set_sorter(NAME_MSG, "sorter_property_name", "2", "property_name");
	$s->set_sorter(ADMIN_ORDER_MSG, "sorter_property_order", "3", "property_order");
	$s->set_sorter(TYPE_MSG, "sorter_property_type", "4", "property_type");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_filter_properties.php");

	$sql  = " SELECT filter_name FROM " . $table_prefix . "filters ";
	$sql .= " WHERE filter_id=" . $db->tosql($filter_id, INTEGER);
	$db->query($sql);
	if($db->next_record()) {
		$t->set_var("filter_name", get_translation($db->f("filter_name")));
	} else {
		header("Location: admin_filters.php");
		exit;
	}


	// set up variables for navigator
	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "filters_properties ";
	$sql .= " WHERE filter_id=" . $db->tosql($filter_id, INTEGER);
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 20;
	$pages_number = 10;

	$admin_filter_property_url = new VA_URL("admin_filter_property.php", true);
	$t->set_var("admin_filter_property_new_url", $admin_filter_property_url->get_url());

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT property_id,property_name,property_order,property_type FROM " . $table_prefix . "filters_properties ";
	$sql .= " WHERE filter_id=" . $db->tosql($filter_id, INTEGER);
	$sql .= $s->order_by;
	$db->query($sql);
	if($db->next_record())
	{
		$admin_filter_property_url->add_parameter("property_id", DB, "property_id");
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$property_id= $db->f("property_id");
			$property_order= $db->f("property_order");
			$property_type = $db->f("property_type");

			$t->set_var("property_id", $property_id);
			$t->set_var("property_order", $property_order);
			$t->set_var("property_type", $property_type);
			$t->set_var("property_name", htmlspecialchars(get_translation($db->f("property_name"))));

			$t->set_var("admin_filter_property_edit_url", $admin_filter_property_url->get_url());

			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>