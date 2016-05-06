<?php
include_once("./includes/manuals_functions.php");

function manuals_search($block_name) {
	global $t, $db, $table_prefix;
	global $page_settings;
	
	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_manuals_search.html");

	$search_string   = get_param("manuals_search");
	$manual_id       = intval(get_param("manual_id"));
	$search_type     = intval(get_param("manuals_search_type"));
	$advanced_search = intval(get_param("manuals_advanced_search"));

	if ($search_type > 0) {
		$advanced_search = 1;
	}
	

	$manuals = array();
	$manuals[] = array(0, MANUALS_SEARCH_IN_FIRST_VARIANT);
	$manuals_ids = VA_Manuals::find_all_ids("", VIEW_CATEGORIES_ITEMS_PERM);
	if ($manuals_ids) {
		$sql  = " SELECT manual_id, manual_title ";
		$sql .= " FROM " . $table_prefix . "manuals_list ";
		$sql .= " WHERE manual_id IN (" . $db->tosql($manuals_ids, INTEGERS_LIST) . ") ";
		$sql .= " ORDER BY manual_title ";
		$manuals = get_db_values($sql, $manuals);
	}
	set_options($manuals, $manual_id, "manual_id");
	
	$search_types = array(
		array(0, SEARCH_EXACT_WORD_OR_PHRASE),
		array(1, SEARCH_ONE_OR_MORE),
		array(2, SEARCH_ALL)
	);
	set_options($search_types, $search_type, "search_type");	
	
	if ($advanced_search) {
		$t->set_var("manuals_advanced_search_style", "style='display:block'");
	} else {
		$t->set_var("manuals_advanced_search_style", "style='display:none'");
	}
	$t->set_var("manuals_search", htmlspecialchars($search_string));
	$t->set_var("manuals_advanced_search", $advanced_search);
	
	
	$t->parse("block_body", false);
	$t->parse($block_name, true);
}
?>