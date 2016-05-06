<?php

function ads_sellers($block_name, $category_id)
{
	global $t, $db, $db_type, $table_prefix;
	global $page_settings;
	global $site_id;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body",        "block_ads_sellers.html");

	$search_tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "ads_categories", "tree", ADS_TITLE);

	
	$sql_params = array();
	$sql_params["brackets"] = "((";	
	if (isset($site_id)) {
		$sql_params["brackets"] .= "(";
	}
	$sql_params["join"]  = " INNER JOIN " . $table_prefix . "users u ON i.user_id=u.user_id) ";
	$sql_params["join"] .= " INNER JOIN " . $table_prefix . "user_types t ON t.type_id=u.user_type_id) ";
	if (isset($site_id)) {
		$sql_params["join"] .=	" LEFT JOIN " . $table_prefix . "user_types_sites uts ON uts.type_id=t.type_id) ";
	}	
	if (isset($site_id)) {
		$sql_params["where"] = " ( t.sites_all=1 OR uts.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ") ";
	} else {
		$sql_params["where"] = " t.sites_all=1 ";
	}	
	$sql_params["order"] = " GROUP BY u.user_id ";
	if ($db_type == "access" || $db_type == "db2" || $db_type == "postgre") {
		$sql_params["order"] .= ", u.name, u.login, u.first_name, u.last_name ";
	}
	
	$merchants = VA_Ads::find_all(
		"u.user_id",
		array("u.name", "u.login", "u.first_name", "u.last_name", "COUNT(*)"),
		$sql_params, VIEW_CATEGORIES_ITEMS_PERM
	);
	if (!$merchants) return;
	
	foreach ($merchants AS $user_id => $merchant){
		$name  = $merchant["u.name"];
		$login = $merchant["u.login"];
		$first_name = $merchant["u.first_name"];
		$last_name  = $merchant["u.last_name"];
		$user_ads   = count(VA_Ads::find_all_ids("i.user_id=" . $user_id));		
		
		if (strlen($name)) {
			$user_name = $name;
		} else if (strlen($first_name) || strlen($last_name)) {
			$user_name = $first_name." ".$last_name;
		} else {
			$user_name = $login;
		}

		$user_href = "ads.php?category_id=" . urlencode($category_id) . "&user=" . $user_id;

		$t->set_var("user_name", $user_name);
		$t->set_var("user_href", $user_href);
		$t->set_var("user_ads", $user_ads);

		$t->parse("users", true);		
	}
	
	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>