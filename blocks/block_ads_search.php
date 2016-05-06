<?php
include_once("./includes/ads_functions.php");

function ads_search($block_name, $category_id)
{
	global $t, $db, $table_prefix;
	global $category_id;
	global $page_settings;
	global $site_id, $db_type;
	
	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	if($block_name) {
		$t->set_file("block_body", "block_search.html");
	}

	$t->set_var("search_href",   "ads.php");
	$t->set_var("search_name",   ADS_TITLE);

	$search_category_id = get_param("search_category_id");
	$search_string = trim(get_param("search_string"));
	$is_search = strlen($search_string);
	$pq = get_param("pq");
	$fq = get_param("fq");
	$s_tit = get_param("s_tit");
	$s_sds = get_param("s_sds");
	$s_fds = get_param("s_fds");
	$user = get_param("user");
	$lprice = get_param("lprice");
	$hprice = get_param("hprice");
	$country = get_param("country");
	$state = get_param("state");
	$zip = get_param("zip");

	$pass_parameters = array(
		"search_string" => $search_string,
		"search_category_id" => $search_category_id, "pq" => $pq, "fq" => $fq,
		"s_tit" => $s_tit, "s_sds" => $s_sds, "s_fds" => $s_fds,
		"user" => $user, "lprice" => $lprice, "hprice" => $hprice,
		"country" => $country, "state" => $state, "zip" => $zip,
	);
	if ($pq > 0) {
		for($pi = 1; $pi <= $pq; $pi++) {
			$property_name = get_param("pn_" . $pi);
			$property_value = get_param("pv_" . $pi);
			if (strlen($property_name) && strlen($property_value)) {
				$pass_parameters["pn_" . $pi] = $property_name;
				$pass_parameters["pv_" . $pi] = $property_value;
			}
		}
	}
	if ($fq > 0) {
		for($fi = 1; $fi <= $fq; $fi++) {
			$feature_name = get_param("fn_" . $fi);
			$feature_value = get_param("fv_" . $fi);
			if (strlen($feature_name) && strlen($feature_value)) {
				$pass_parameters["fn_" . $fi] = $feature_name;
				$pass_parameters["fv_" . $fi] = $feature_value;
			}
		}
	}
	$query_string = get_query_string($pass_parameters, "", "", false);
	$t->set_var("advanced_search_href", "ads_search.php" . $query_string);
	$t->global_parse("advanced_search", false, false, true);

	$search_categories = array();
	$search_categories[] = array(0, SEARCH_IN_ALL_MSG);
  
	if($category_id != 0) {
		$search_categories[] = array($category_id, SEARCH_IN_CURRENT_MSG);
	}

	$categories_ids = VA_Ads_Categories::find_all_ids("c.parent_category_id = " . $db->tosql($category_id, INTEGER), VIEW_CATEGORIES_ITEMS_PERM);
	if ($categories_ids) {
		$sql  = " SELECT category_id, category_name ";
		$sql .= " FROM " . $table_prefix . "ads_categories ";
		$sql .= " WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ") ";
		$sql .= " ORDER BY category_order ";
		$search_categories = get_db_values($sql, $search_categories);
	}

	// set up search form parameters
	if (sizeof($search_categories) > 1) {
		set_options($search_categories, $search_category_id, "search_category_id");
		$t->global_parse("search_categories", false, false, true);
	} else {
		$t->set_var("search_categories", "");
	}
	$t->set_var("search_string", htmlspecialchars($search_string));
	$t->set_var("current_category_id", htmlspecialchars($category_id));

	if($block_name) {
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>