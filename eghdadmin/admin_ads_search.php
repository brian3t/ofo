<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads_search.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("site_settings");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_ads_search.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_ads_href", "admin_ads.php");
	$t->set_var("admin_ads_search_href", "admin_ads_search.php");

	$r = new VA_Record($table_prefix . "global_settings");

	// set up html form parameters
	$r->add_textbox("intro_text", TEXT);

	$r->add_checkbox("search_for", INTEGER);
	$r->add_checkbox("search_in", INTEGER);
	$r->add_checkbox("search_in_category", INTEGER);
	$r->add_checkbox("user", INTEGER);
	$user_types = get_db_values("SELECT type_id, type_name FROM " . $table_prefix . "user_types ", array(array("", ANY_MSG)));
	$r->add_select("user_type_id", INTEGER, $user_types);
	$r->add_checkbox("price", INTEGER);
	$r->add_checkbox("search_by_country", INTEGER);
	$r->add_checkbox("search_by_state", INTEGER);
	$r->add_checkbox("search_by_zip", INTEGER);

	$r->get_form_values();

	$param_site_id = get_session("session_site_id");
	$operation = get_param("operation");
	$return_page = get_param("rp");
	if(!strlen($return_page)) $return_page = "admin_ads.php";
	$t->set_var("rp", htmlspecialchars($return_page));
	$properties_selected = array();

	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		if(!strlen($r->errors))
		{
			$sql  = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type LIKE 'ads_search%'";
			$sql .= " AND site_id=" . $db->tosql($param_site_id, INTEGER);
			$db->query($sql);
			foreach($r->parameters as $key => $value)
			{
				$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
				$sql .= $db->tosql("ads_search", TEXT) . ", '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
				$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				$db->query($sql);
			}
			$properties_number = get_param("properties_number");
			for ($i = 1; $i <= $properties_number; $i++) {
				$property_show = get_param("property_show_" . $i);
				$property_name = get_param("property_name_" . $i);
				$property_value = get_param("property_value_" . $i);
				if ($property_show == 1) {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
					$sql .= "'ads_search_properties', " . $db->tosql($property_name, TEXT) . "," . $db->tosql(1, TEXT) . ",";
					$sql .= $db->tosql($param_site_id, INTEGER) . ") ";
					$db->query($sql);
				}

				$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
				$sql .= "'ads_search_properties_values', " . $db->tosql($property_name, TEXT) . "," . $db->tosql($property_value, TEXT) . ",";
				$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				$db->query($sql);
			}

			$features_number = get_param("features_number");
			for ($i = 1; $i <= $features_number; $i++) {
				$feature_show = get_param("feature_show_" . $i);
				$feature_name = get_param("feature_name_" . $i);
				$feature_value = get_param("feature_value_" . $i);
				if ($feature_show == 1) {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
					$sql .= "'ads_search_features', " . $db->tosql($feature_name, TEXT) . "," . $db->tosql(1, TEXT) . ",";
					$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
					$db->query($sql);
				}
				$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
				$sql .= "'ads_search_features_values', " . $db->tosql($feature_name, TEXT) . "," . $db->tosql($feature_value, TEXT) . ",";
				$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
	}
	else // get search settings
	{
		$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type='ads_search'";
		$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
		$sql .= " ORDER BY site_id DESC ";
		$db->query($sql);
		while ($db->next_record()) {
			$setting_name = $db->f("setting_name");
			$setting_value = $db->f("setting_value");
			$r->set_value($setting_name, $setting_value);
		}
		$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type='ads_search_properties'";
		$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
		$sql .= " ORDER BY site_id DESC ";
		$db->query($sql);
		while ($db->next_record()) {
			$setting_name = $db->f("setting_name");
			$setting_value = $db->f("setting_value");
			if($setting_value == 1) {
				$properties_selected[$setting_name] = 1;
			}
		}
		$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type='ads_search_properties_values'";
		$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
		$sql .= " ORDER BY site_id DESC ";
		$db->query($sql);
		while ($db->next_record()) {
			$setting_name = $db->f("setting_name");
			$setting_value = $db->f("setting_value");
			$properties_values[$setting_name] = $setting_value;
		}
		$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type='ads_search_features'";
		$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
		$sql .= " ORDER BY site_id DESC ";
		$db->query($sql);
		while ($db->next_record()) {
			$setting_name = $db->f("setting_name");
			$setting_value = $db->f("setting_value");
			if($setting_value == 1) {
				$features_selected[$setting_name] = 1;
			}
		}
		$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type='ads_search_features_values'";
		$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
		$sql .= " ORDER BY site_id DESC ";
		$db->query($sql);
		while ($db->next_record()) {
			$setting_name = $db->f("setting_name");
			$setting_value = $db->f("setting_value");
			$features_values[$setting_name] = $setting_value;
		}
	}

	$r->set_parameters();

	$properties_number = 0;
	$sql  = " SELECT property_name FROM " . $table_prefix . "ads_properties ";
	$sql .= " GROUP BY property_name ";
	$db->query($sql);
	if ($db->next_record()) {
		
		do {
			$properties_number++;
			$property_name = $db->f("property_name");
			$property_checked = (isset($properties_selected[$property_name])) ? "checked" : "";
			$property_value = (isset($properties_values[$property_name])) ? $properties_values[$property_name] : "";

			$t->set_var("property_name",    htmlspecialchars($property_name));
			$t->set_var("property_value",   htmlspecialchars($property_value));
			$t->set_var("property_number",  $properties_number);
			$t->set_var("property_checked", $property_checked);

			$t->parse("properties_rows", true);
			
		} while ($db->next_record());

		$t->parse("properties", false);
	} else {
		$t->set_var("properties", "");
	}
	$t->set_var("properties_number", $properties_number);

	$features_number = 0;
	$sql  = " SELECT feature_name FROM " . $table_prefix . "ads_features ";
	$sql .= " GROUP BY feature_name ";
	$db->query($sql);
	if ($db->next_record()) {
		
		do {
			$features_number++;
			$feature_name = $db->f("feature_name");
			$feature_checked = (isset($features_selected[$feature_name])) ? "checked" : "";
			$feature_value = (isset($features_values[$feature_name])) ? $features_values[$feature_name] : "";

			$t->set_var("feature_name", htmlspecialchars($feature_name));
			$t->set_var("feature_value", htmlspecialchars($feature_value));
			$t->set_var("feature_number", $features_number);
			$t->set_var("feature_checked", $feature_checked);

			$t->parse("features_rows", true);
			
		} while ($db->next_record());

		$t->parse("features", false);
	} else {
		$t->set_var("features", "");
	}
	$t->set_var("features_number", $features_number);

	// multisites
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ", "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}	
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

?>