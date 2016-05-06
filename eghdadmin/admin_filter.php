<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_filter.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("filters");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_filter.html");

	$admin_filters_url = new VA_URL("admin_filters.php", false);
	$admin_filters_url->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_filters_url->add_parameter("sort_dir", REQUEST, "sort_dir");
	$admin_filters_url->add_parameter("page", REQUEST, "page");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_filters_href", "admin_filters.php");
	$t->set_var("admin_filter_href", "admin_filter.php");
	$t->set_var("admin_filters_url", $admin_filters_url->get_url());
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_FILTER_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "filters");
	$r->return_page = "admin_filters.php";

	$filter_types = 
		array(			
			array("", ""),  
			array("products",  PRODUCTS_MSG),
		);

	$r->add_where("filter_id", INTEGER);
	$r->add_select("filter_type", TEXT, $filter_types, FILTER_TYPE_MSG);
	$r->parameters["filter_type"][REQUIRED] = true;
	$r->add_textbox("filter_name", TEXT, FILTER_NAME_MSG);
	$r->parameters["filter_name"][REQUIRED] = true;
	$r->add_textbox("filter_desc", TEXT, FILTER_DESC_MSG);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("sort_dir", TEXT);
	$r->set_event(AFTER_DELETE, "remove_filter_properties");

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");



	function remove_filter_properties()
	{
		global $r, $db, $table_prefix;
		$filter_id = $r->get_value("filter_id");
		$properties_ids = "";
		$sql = "SELECT property_id FROM " . $table_prefix . "filters_properties WHERE filter_id=" . $db->tosql($filter_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			if(strlen($properties_ids)) { $properties_ids.= ","; }
			$properties_ids .= $db->f("property_id");
		}
		if (strlen($properties_ids)) {
			$db->query("DELETE FROM " . $table_prefix . "filters_properties_values WHERE property_id IN (" . $db->tosql($properties_ids, TEXT, false) . ")");
			$db->query("DELETE FROM " . $table_prefix . "filters_properties WHERE filter_id=" . $db->tosql($filter_id, INTEGER));
		}
	}


?>