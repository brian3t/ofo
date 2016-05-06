<?php

function ads_search_advanced($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id, $currency;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}
	$t->set_file("block_body", "block_ads_search_advanced.html");

	$t->set_var("search_href", "ads_search.php");
	$t->set_var("ads_href",    "ads.php");

	$currency = get_currency();
	$weight_measure = get_setting_value($settings, "weight_measure", "");

	$search_category_id = get_param("search_category_id");
	$search_string = trim(get_param("search_string"));
	$s_tit = get_param("s_tit");
	$s_sds = get_param("s_sds");
	$s_fds = get_param("s_fds");
	$user = get_param("user");
	$lprice = get_param("lprice");
	$hprice = get_param("hprice");
	if ($s_sds == 1) { $s_sds = "checked"; }
	if ($s_fds == 1) { $s_fds = "checked"; }
		
	$t->set_var("search_string", htmlspecialchars($search_string));
	$t->set_var("s_sds", $s_sds);
	$t->set_var("s_fds", $s_fds);
	$t->set_var("lprice", htmlspecialchars($lprice));
	$t->set_var("hprice", htmlspecialchars($hprice));
	$t->set_var("currency_symbol", $currency["left"].$currency["right"]);
	$t->set_var("weight_measure", htmlspecialchars($weight_measure));

	$categories_ids = VA_Ads_Categories::find_all_ids("c.parent_category_id = 0", VIEW_CATEGORIES_ITEMS_PERM);
	if ($categories_ids) {
		$sql  = " SELECT category_id, category_name ";
		$sql .= " FROM " . $table_prefix . "ads_categories ";
		$sql .= " WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ") ";
		$sql .= " ORDER BY category_order ";
		$search_categories = get_db_values($sql, array(array("", "")));
	}

	$search_categories = get_db_values($sql, array(array("", "")));
	set_options($search_categories, $search_category_id, "search_category_id");

	$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='ads_search' AND setting_name='user_type_id' ";
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id DESC ";	
	} else {
		$sql .= " AND site_id=1 ";
	}
	$user_type_id = get_db_value($sql);
	
	$sql  = " SELECT user_id,login FROM " . $table_prefix . "users ";
	if (strlen($user_type_id)) {
		$sql .= " WHERE user_type_id=" . $db->tosql($user_type_id, INTEGER);
	}
	$users = get_db_values($sql, array(array("", "")));
	set_options($users, $user, "user_id");

	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='ads_search'";
	if (isset($site_id)) {
		$sql .= " AND ( site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	$settings_array = array();
	while($db->next_record()) {		
		$setting_name  = $db->f("setting_name");
		$setting_value = $db->f("setting_value");
		$settings_array[$setting_name] = $setting_value;		
	}	
	if ($settings_array) {
		foreach ($settings_array AS $setting_name=>$setting_value) {
			if ($setting_name == "intro_text") {
				if(strlen(trim($setting_value))) {
					$t->set_var("intro_text", $setting_value);
					$t->parse("intro_block", false);
				} 
			} else if ($setting_value == 1) {
				$t->set_var($setting_name, get_param($setting_name));
				$t->global_parse($setting_name . "_block", false, false, true);
			}
		} 
	}

	$search_by_country = get_setting_value($settings_array, "search_by_country", "");
	if ($search_by_country) {
		$country = get_param("country");
		$countries = get_db_values("SELECT country_id,country_name FROM " . $table_prefix . "countries WHERE show_for_user=1 ORDER BY country_order, country_name ", array(array("", SELECT_COUNTRY_MSG)));
		set_options($countries, $country, "country");
		$t->parse("country_block");
	}
	$search_by_state = get_setting_value($settings_array, "search_by_state", "");
	if ($search_by_state) {
		$state = get_param("state");
		$states = get_db_values("SELECT state_id,state_name FROM " . $table_prefix . "states WHERE show_for_user=1 ORDER BY state_name ", array(array("", SELECT_STATE_MSG)));
		set_options($states, $state, "state");
		$t->parse("state_block");
	}
	$search_by_zip = get_setting_value($settings_array, "search_by_zip", "");
	if ($search_by_zip) {
		$zip = get_param("zip");
		$t->set_var("zip", htmlspecialchars($zip));
		$t->parse("zip_block");
	}

	$predefined_values = array();
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='ads_search_properties_values'";
	if (isset($site_id)) {
		$sql .= " AND ( site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";		
		$sql .= " ORDER BY site_id ASC ";		
	} else {
		$sql .= " AND site_id=1";		
	}
	$db->query($sql);
	while($db->next_record()) {
		$predefined_values[$db->f("setting_name")] = $db->f("setting_value");
	}

	$property_number = 0;
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='ads_search_properties'";
	if (isset($site_id)) {
		$sql .= " AND ( site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";		
		$sql .= " ORDER BY site_id ASC ";		
	} else {
		$sql .= " AND site_id=1";		
	}
	$db->query($sql);
	$settings_array = array();
	while($db->next_record()) {
		$setting_name = $db->f("setting_name");
		$setting_value = $db->f("setting_value");
		$settings_array[$setting_name] = $setting_value;		
	}	
	if ($settings_array) {
		foreach ($settings_array AS $setting_name=>$setting_value) {
			if ($setting_value == 1) {
				$property_number++;
				$property_values_list = $predefined_values[$setting_name];
				$property_value = get_param("pv_" . $property_number);
				$t->set_var("property_number", $property_number);
				$t->set_var("property_name", htmlspecialchars($setting_name));
				$t->set_var("property_select", "");
				$t->set_var("property_text", "");
				if (strlen(trim($property_values_list))) {
					$values_array = explode("\n", $property_values_list);
					$property_values = array();
					$property_values[] = array("", "");
					for($i = 0; $i < sizeof($values_array); $i++) {
						if(strlen(trim($values_array[$i]))) {
							$property_values[] = array(trim($values_array[$i]), trim($values_array[$i]));
						}
					}
					set_options($property_values, $property_value, "property_value");
					$t->parse("property_select", false);
				} else {
					$t->set_var("property_value", $property_value);
					$t->parse("property_text", false);
				}
	
				$t->parse("properties", true);
			}
		}
	}
	$t->set_var("pq", $property_number);

	$predefined_values = array();
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='ads_search_features_values'";
	if (isset($site_id)) {
		$sql .= " AND ( site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";		
		$sql .= " ORDER BY site_id ASC ";		
	} else {
		$sql .= " AND site_id=1";		
	}
	$db->query($sql);
	while($db->next_record()) {
		$predefined_values[$db->f("setting_name")] = $db->f("setting_value");
	}

	$feature_number = 0;
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='ads_search_features'";
	if (isset($site_id)) {
		$sql .= " AND ( site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";		
		$sql .= " ORDER BY site_id ASC ";		
	} else {
		$sql .= " AND site_id=1";	
	}
	$db->query($sql);
	$settings_array = array();
	while($db->next_record()) {
		$setting_name = $db->f("setting_name");
		$setting_value = $db->f("setting_value");
		$settings_array[$setting_name] = $setting_value;		
	}	
	if ($settings_array) {
		foreach ($settings_array AS $setting_name=>$setting_value) {
			if ($setting_value == 1) {
				$feature_number++;
				$feature_values_list = $predefined_values[$setting_name];
				$feature_value = get_param("fv_" . $feature_number);
				$t->set_var("feature_number", $feature_number);
				$t->set_var("feature_name", htmlspecialchars($setting_name));
				$t->set_var("feature_select", "");
				$t->set_var("feature_text", "");
				if (strlen(trim($feature_values_list))) {
					$values_array = explode("\n", $feature_values_list);
					$feature_values = array();
					$feature_values[] = array("", "");
					for($i = 0; $i < sizeof($values_array); $i++) {
						if(strlen(trim($values_array[$i]))) {
							$feature_values[] = array(trim($values_array[$i]), trim($values_array[$i]));
						}
					}
					set_options($feature_values, $feature_value, "feature_value");
					$t->parse("feature_select", false);
				} else {
					$t->set_var("feature_value", $feature_value);
					$t->parse("feature_text", false);
				}
				$t->parse("features", true);
			}
		}
	}
	$t->set_var("fq", $feature_number);

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}
?>