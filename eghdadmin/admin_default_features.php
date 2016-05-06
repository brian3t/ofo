<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_default_features.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once("./admin_common.php");

	check_admin_security("products_categories");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_default_features.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_item_types_href", "admin_item_types.php");
	$t->set_var("admin_item_type_href", "admin_item_type.php");
	$t->set_var("admin_default_features_href", "admin_default_features.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_SPECIFICATION_MSG, CONFIRM_DELETE_MSG));

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$operation = get_param("operation");
	$item_type_id = get_param("item_type_id");

	$return_page = get_param("rp");
	if(!strlen($return_page)) $return_page = "admin_item_types.php";
	$errors = "";

	$sql = "SELECT item_type_name, google_base_type_id FROM " . $table_prefix . "item_types WHERE item_type_id=" . $db->tosql($item_type_id, INTEGER);
	$db->query($sql);
	$google_base_type_id = 0;
	if($db->next_record()) {
		$item_type_name = get_translation($db->f("item_type_name"));
		$google_base_type_id = $db->f("google_base_type_id");		
		$t->set_var("item_type_name", htmlspecialchars($item_type_name));
	} else {
		header("Location: " . $return_page);
		exit;
	}


	// set up html form parameters
	$r = new VA_Record($table_prefix . "features_default", "features");
	$r->add_where("feature_id", INTEGER);
	$r->add_hidden("item_type_id", INTEGER);
	$r->change_property("item_type_id", USE_IN_INSERT, true);

	$features_groups = get_db_values("SELECT group_id,group_name FROM " . $table_prefix . "features_groups ORDER BY group_order ", array(array("", "")));
	$r->add_select("group_id", INTEGER, $features_groups, GROUP_MSG);
	$r->parameters["group_id"][REQUIRED] = true;
	$r->add_textbox("feature_name", TEXT, NAME_MSG);
	$r->parameters["feature_name"][REQUIRED] = true;
	$r->add_textbox("feature_value", TEXT, VALUE_MSG);

	if ($google_base_type_id == 0) {
		if (isset($settings['google_base_product_type_id'])) {
			$google_base_type_id = $settings['google_base_product_type_id'];
		}
	}	
	$google_base_attributes = array(array('','Not Exported'));
	$google_base_by_type = array();
	if ($google_base_type_id > 0) {
		$sql  = " SELECT * ";
		$sql .= " FROM (" . $table_prefix . "google_base_types_attributes atype ";
		$sql .= " LEFT JOIN " . $table_prefix . "google_base_attributes a ON a.attribute_id=atype.attribute_id) ";
		$sql .= " WHERE atype.type_id=" . $db->tosql($google_base_type_id, INTEGER);
		$sql .= " ORDER BY atype.required, a.attribute_name ";
		$db->query($sql);
		
		while ($db->next_record()) {
			$attribute_id   = $db->f('attribute_id');
			$attribute_name = $db->f('attribute_name');
			$attribute_type = $db->f('attribute_type');
			$value_type = $db->f('value_type');
			$required = $db->f('required');	
			$google_base_by_type[] = $attribute_id;		
			$attribute_desc  = $attribute_name;
			if ($required) {
				$attribute_desc .= "*";
			}
			$attribute_desc .= " (" . $attribute_type . ", " . $value_type . ")";			
			$google_base_attributes[] = array ($attribute_id, $attribute_desc);
		}
		$google_base_attributes[] = array ('', '-----Additional Attribute----');
	}	
	
	$sql  = " SELECT * ";
	$sql .= " FROM " . $table_prefix . "google_base_attributes ";
	if ($google_base_by_type) {
		$sql .= " WHERE attribute_id NOT IN (" . implode(',', $google_base_by_type) . ") ";
	}
	$sql .= " ORDER BY attribute_name ";
	$db->query($sql);
			
	while ($db->next_record()) {
		$attribute_id   = $db->f('attribute_id');
		$attribute_name = $db->f('attribute_name');
		$attribute_type = $db->f('attribute_type');
		$value_type = $db->f('value_type');			
		$attribute_desc  = $attribute_name . " (" . $attribute_type . ", " . $value_type . ")";					
		$google_base_attributes[] = array ($attribute_id, $attribute_desc);
	}
	
	if ($google_base_attributes>=0) {
		$r->add_select("google_base_attribute_id", INTEGER, $google_base_attributes, GOOGLE_BASE_ATTRIBUTE_MSG);
		$r->parameters["google_base_attribute_id"][USE_SQL_NULL] = false;
	}
	
	
	$more_features = get_param("more_features");
	$number_features = get_param("number_features");

	$eg = new VA_EditGrid($r, "features");
	$eg->get_form_values($number_features);

	if(strlen($operation) && !$more_features)
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		else if($operation == "delete" && $item_type_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "features_default WHERE item_type_id=" . $db->tosql($item_type_id, INTEGER));		
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $eg->validate(); 

		if($is_valid)
		{
			$eg->set_values("item_type_id", $item_type_id);
			$eg->update_all($number_features);
			header("Location: " . $return_page);
			exit;
		}
	}
	else if(strlen($item_type_id) && !$more_features)
	{
		$eg->set_value("item_type_id", $item_type_id);
		$eg->change_property("feature_id", USE_IN_SELECT, true);
		$eg->change_property("feature_id", USE_IN_WHERE, false);
		$eg->change_property("item_type_id", USE_IN_WHERE, true);
		$eg->change_property("item_type_id", USE_IN_SELECT, true);
		$number_features= $eg->get_db_values();
		if($number_features == 0)
			$number_features = 5;
	}
	else if($more_features)
	{
		$number_features += 5;
	}
	else
	{
		$number_features = 5;
	}
	$t->set_var("number_features", $number_features);

	$eg->set_parameters_all($number_features);

	$t->set_var("item_type_id", $item_type_id);
	$t->set_var("rp", htmlspecialchars($return_page));

	$t->pparse("main");

?>