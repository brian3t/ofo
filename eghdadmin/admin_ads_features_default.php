<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads_features_default.php                           ***
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
  $t->set_file("main","admin_ads_features_default.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_ads_href", "admin_ads.php");
	$t->set_var("admin_ads_types_href", "admin_ads_types.php");
	$t->set_var("admin_ads_type_href", "admin_ads_type.php");
	$t->set_var("admin_ads_features_default_href", "admin_ads_features_default.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", DEFAULT_SPECIFICATION_MSG, CONFIRM_DELETE_MSG));

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$operation = get_param("operation");
	$type_id = get_param("type_id");

	$return_page = get_param("rp");
	if(!strlen($return_page)) $return_page = "admin_ads_types.php";
	$errors = "";

	$sql = "SELECT type_name FROM " . $table_prefix . "ads_types WHERE type_id=" . $db->tosql($type_id, INTEGER);
	$db->query($sql);
	if($db->next_record()) {
		$type_name = $db->f("type_name");
		$t->set_var("type_name", htmlspecialchars($type_name));
	} else {
		header("Location: " . $return_page);
		exit;
	}


	// set up html form parameters
	$r = new VA_Record($table_prefix . "ads_features_default", "features");
	$r->add_where("feature_id", INTEGER);
	$r->add_hidden("type_id", INTEGER);
	$r->change_property("type_id", USE_IN_INSERT, true);

	$features_groups = get_db_values("SELECT group_id,group_name FROM " . $table_prefix . "ads_features_groups ORDER BY group_order ", array(array("", "")));
	$r->add_select("group_id", INTEGER, $features_groups, GROUP_MSG);
	$r->parameters["group_id"][REQUIRED] = true;
	$r->add_textbox("feature_name", TEXT, NAME_MSG);
	$r->parameters["feature_name"][REQUIRED] = true;
	$r->add_textbox("feature_value", TEXT, VALUE_MSG);

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
		else if($operation == "delete" && $type_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "ads_features_default WHERE type_id=" . $db->tosql($type_id, INTEGER));		
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $eg->validate(); 

		if($is_valid)
		{
			$eg->set_values("type_id", $type_id);
			$eg->update_all($number_features);
			header("Location: " . $return_page);
			exit;
		}
	}
	else if(strlen($type_id) && !$more_features)
	{
		$eg->set_value("type_id", $type_id);
		$eg->change_property("feature_id", USE_IN_SELECT, true);
		$eg->change_property("feature_id", USE_IN_WHERE, false);
		$eg->change_property("type_id", USE_IN_WHERE, true);
		$eg->change_property("type_id", USE_IN_SELECT, true);
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

	$t->set_var("type_id", $type_id);
	$t->set_var("rp", htmlspecialchars($return_page));

	$t->pparse("main");

?>