<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads_features.php                                   ***
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

	check_admin_security("ads");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_ads_features.html");

	$t->set_var("admin_href",               "admin.php");
	$t->set_var("admin_ads_href",           "admin_ads.php");
	$t->set_var("admin_ads_features_href",  "admin_ads_features.php");
	$t->set_var("admin_ads_edit_href",      "admin_ads_edit.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", DEFAULT_SPECIFICATION_MSG, CONFIRM_DELETE_MSG));

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$operation = get_param("operation");
	$item_id = get_param("item_id");
	$category_id = get_param("category_id");
	$feature_values = array();

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
	$r = new VA_Record($table_prefix . "ads_features", "features");
	$r->add_where("feature_id", INTEGER);
	$r->add_hidden("item_id", INTEGER);
	$r->change_property("item_id", USE_IN_INSERT, true);

	$ads_features_groups = get_db_values("SELECT group_id, group_name FROM " . $table_prefix . "ads_features_groups ORDER BY group_order ", array(array("", "")));
	$r->add_select("group_id", INTEGER, $ads_features_groups, GROUP_MSG);
	$r->parameters["group_id"][REQUIRED] = true;
	$r->add_textbox("feature_name", TEXT, NAME_MSG);
	$r->parameters["feature_name"][REQUIRED] = true;
	$r->add_select("default_value", TEXT, array());
	$r->parameters["default_value"][USE_IN_INSERT] = false;
	$r->parameters["default_value"][USE_IN_UPDATE] = false;
	$r->parameters["default_value"][USE_IN_SELECT] = false;
	$r->add_textbox("feature_value", TEXT, VALUE_MSG);
	$r->parameters["feature_value"][REQUIRED] = true;

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
		else if($operation == "delete" && $item_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "ads_features WHERE item_id=" . $db->tosql($item_id, INTEGER));		
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $eg->validate(); 

		if($is_valid)
		{
			$eg->set_values("item_id", $item_id);
			$eg->update_all($number_features);
			header("Location: " . $return_page);
			exit;
		}
	}
	else if(strlen($item_id) && !$more_features)
	{
		$eg->set_value("item_id", $item_id);
		$eg->change_property("feature_id", USE_IN_SELECT, true);
		$eg->change_property("feature_id", USE_IN_WHERE, false);
		$eg->change_property("item_id", USE_IN_WHERE, true);
		$eg->change_property("item_id", USE_IN_SELECT, true);
		//$number_features= $eg->get_db_values();
		// manually get features
		$number_features = 0;
		$sql  = " SELECT f.feature_id, f.group_id, f.feature_name, f.feature_value ";
		$sql .= " FROM " . $table_prefix . "ads_features f ";
		$sql .= " , " . $table_prefix . "ads_features_groups fg ";
		$sql .= " WHERE f.group_id=fg.group_id ";
		$sql .= " AND f.item_id=" . $db->tosql($item_id, INTEGER);
		$sql .= " ORDER BY fg.group_order, f.feature_id ";
		$db->query($sql);
		while($db->next_record())
		{
			$number_features++;
			$eg->values[$number_features]["feature_id"] = $db->f("feature_id");
			$eg->values[$number_features]["item_id"] = $item_id;
			$eg->values[$number_features]["group_id"] = $db->f("group_id");
			$eg->values[$number_features]["feature_name"] = $db->f("feature_name");
			$eg->values[$number_features]["feature_value"] = $db->f("feature_value");
		}

		if ($number_features == 0) {
			$sql  = " SELECT fd.group_id, fd.feature_name, fd.feature_value ";
			$sql .= " FROM " . $table_prefix . "ads_features_default fd ";
			$sql .= " , " . $table_prefix . "ads_features_groups fg ";
			$sql .= " WHERE fd.group_id=fg.group_id ";
			$sql .= " AND fd.type_id=" . $db->tosql($type_id, INTEGER);
			$sql .= " ORDER BY fg.group_order, fd.feature_id ";
			$db->query($sql);
			if ($db->next_record()) {
				do {
					$number_features++;
					$eg->values[$number_features]["group_id"] = $db->f("group_id");
					$eg->values[$number_features]["feature_name"] = $db->f("feature_name");
					$feature_values[$number_features] = $db->f("feature_value");
				} while ($db->next_record());
			} else {
				$number_features = 5;
			}
		}
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

	for($i = 1; $i <= $number_features; $i++)
	{
		if (isset($feature_values[$i])) {
			$feature_values_list = $feature_values[$i];
		} else {
			$feature_values_list = get_param("feature_values_" . $i);
			if (!strlen($feature_values_list)) {
				// check for default values
				$feature_name = ""; $group_id = "";
				if(isset($eg->values[$i])) {
					$feature_name = $eg->values[$i]["feature_name"];
					$group_id = $eg->values[$i]["group_id"];
				} 
				if (strlen($feature_name) && strlen($group_id)) {
					$sql  = " SELECT feature_value FROM " . $table_prefix . "ads_features_default ";
					$sql .= " WHERE type_id=" . $db->tosql($type_id, INTEGER);
					$sql .= " AND group_id=" . $db->tosql($group_id, INTEGER);
					$sql .= " AND feature_name=" . $db->tosql($feature_name, TEXT);
					$db->query($sql);
					if ($db->next_record()) {
						$feature_values_list = $db->f("feature_value");
					} 
				}
			}
		}
		if (strlen(trim($feature_values_list))) {
			$t->set_var("feature_values", $feature_values_list);
			$default_values = array();
			$default_values[] = array("", SELECT_FROM_LIST_MSG);
			//$default_values[] = array("", "");
			$feature_values_array = explode("\n", $feature_values_list);
			for ($j = 0; $j < sizeof($feature_values_array); $j++) {
				if (strlen(trim($feature_values_array[$j]))) {
					$default_values[] = array($feature_values_array[$j], $feature_values_array[$j]);
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
