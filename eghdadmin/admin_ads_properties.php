<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads_properties.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once ($root_folder_path."messages/".$language_code."/cart_messages.php");


	check_admin_security("ads");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_ads_properties.html");

	$t->set_var("admin_href",               "admin.php");
	$t->set_var("admin_ads_href",           "admin_ads.php");
	$t->set_var("admin_ads_properties_href","admin_ads_properties.php");
	$t->set_var("admin_ads_edit_href",      "admin_ads_edit.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$operation = get_param("operation");
	$item_id = get_param("item_id");
	$category_id = get_param("category_id");
	$property_values = array();

	$return_page = get_param("rp");
	if(!strlen($return_page)) $return_page = "admin_ads.php?category_id=" . urlencode($category_id);
	$errors = "";

	$sql = "SELECT type_id, item_title FROM " . $table_prefix . "ads_items WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$db->query($sql);
	if($db->next_record()) {
		$type_id = $db->f("type_id");
		$item_title = get_translation($db->f("item_title"));

		$t->set_var("item_title", htmlspecialchars($item_title));
	} else {
		header("Location: " . $return_page);
		exit;
	}

	// set up html form parameters
	$r = new VA_Record($table_prefix . "ads_properties", "properties");
	$r->add_where("property_id", INTEGER);
	$r->add_hidden("item_id", INTEGER);
	$r->change_property("item_id", USE_IN_INSERT, true);

	$r->add_textbox("property_name", TEXT, NAME_MSG);
	$r->parameters["property_name"][REQUIRED] = true;
	$r->add_select("default_value", TEXT, array());
	$r->parameters["default_value"][USE_IN_INSERT] = false;
	$r->parameters["default_value"][USE_IN_UPDATE] = false;
	$r->parameters["default_value"][USE_IN_SELECT] = false;
	$r->add_textbox("property_value", TEXT, VALUE_MSG);
	$r->parameters["property_value"][REQUIRED] = true;

	$more_properties = get_param("more_properties");
	$number_properties = get_param("number_properties");

	$eg = new VA_EditGrid($r, "properties");
	$eg->get_form_values($number_properties);

	if(strlen($operation) && !$more_properties)
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		else if($operation == "delete" && $item_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "ads_properties WHERE item_id=" . $db->tosql($item_id, INTEGER));		
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $eg->validate(); 

		if($is_valid)
		{
			$eg->set_values("item_id", $item_id);
			$eg->update_all($number_properties);
			header("Location: " . $return_page);
			exit;
		}
	}
	else if(strlen($item_id) && !$more_properties)
	{
		$eg->set_value("item_id", $item_id);
		$eg->change_property("property_id", USE_IN_SELECT, true);
		$eg->change_property("property_id", USE_IN_WHERE, false);
		$eg->change_property("item_id", USE_IN_WHERE, true);
		$eg->change_property("item_id", USE_IN_SELECT, true);
		//$number_properties= $eg->get_db_values();
		// manually get properties
		$number_properties = 0;
		$sql  = " SELECT f.property_id, f.property_name, f.property_value ";
		$sql .= " FROM " . $table_prefix . "ads_properties f ";
		$sql .= " WHERE f.item_id=" . $db->tosql($item_id, INTEGER);
		$sql .= " ORDER BY f.property_id ";
		$db->query($sql);
		while($db->next_record())
		{
			$number_properties++;
			$eg->values[$number_properties]["property_id"] = $db->f("property_id");
			$eg->values[$number_properties]["item_id"] = $item_id;
			$eg->values[$number_properties]["property_name"] = $db->f("property_name");
			$eg->values[$number_properties]["property_value"] = $db->f("property_value");
		}

		if ($number_properties == 0) {
			$sql  = " SELECT fd.property_name, fd.property_value ";
			$sql .= " FROM " . $table_prefix . "ads_properties_default fd ";
			$sql .= " WHERE fd.type_id=" . $db->tosql($type_id, INTEGER);
			$sql .= " ORDER BY fd.property_id ";
			$db->query($sql);
			if ($db->next_record()) {
				do {
					$number_properties++;
					$eg->values[$number_properties]["property_name"] = $db->f("property_name");
					$property_values[$number_properties] = $db->f("property_value");
				} while ($db->next_record());
			} else {
				$number_properties = 5;
			}
		}
	}
	else if($more_properties)
	{
		$number_properties += 5;
	}
	else
	{
		$number_properties = 5;
	}

	$t->set_var("number_properties", $number_properties);

	for($i = 1; $i <= $number_properties; $i++)
	{
		if (isset($property_values[$i])) {
			$property_values_list = $property_values[$i];
		} else {
			$property_values_list = get_param("property_values_" . $i);
			if (!strlen($property_values_list)) {
				// check for default values
				$property_name = ""; 
				if(isset($eg->values[$i])) {
					$property_name = $eg->values[$i]["property_name"];
				} 
				if (strlen($property_name)) {
					$sql  = " SELECT property_value FROM " . $table_prefix . "ads_properties_default ";
					$sql .= " WHERE type_id=" . $db->tosql($type_id, INTEGER);
					$sql .= " AND property_name=" . $db->tosql($property_name, TEXT);
					$db->query($sql);
					if ($db->next_record()) {
						$property_values_list = $db->f("property_value");
					} 
				}
			}
		}
		if (strlen(trim($property_values_list))) {
			$t->set_var("property_values", $property_values_list);
			$default_values = array();
			$default_values[] = array("", "--- " . SELECT_FROM_LIST_MSG . " ---");
			//$default_values[] = array("", "");
			$property_values_array = explode("\n", $property_values_list);
			for ($j = 0; $j < sizeof($property_values_array); $j++) {
				if (strlen(trim($property_values_array[$j]))) {
					$default_values[] = array($property_values_array[$j], $property_values_array[$j]);
				}
			}
			$eg->change_property("default_value", VALUES_LIST, $default_values);
			$eg->change_property("default_value", SHOW, true);
		} else {
			$eg->change_property("default_value", SHOW, false);
		}
		$eg->set_parameters($i);
	}
	$t->set_var("item_id", $item_id);
	$t->set_var("category_id", $category_id);
	$t->set_var("rp", htmlspecialchars($return_page));

	$t->pparse("main");

?>