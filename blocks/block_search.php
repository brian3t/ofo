<?php
include_once("./includes/products_functions.php");
	
function search_form($block_name = "")
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $category_id;
	global $page_settings;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}
	
	if ($block_name) {
		$t->set_file("block_body", "block_search.html");
	}

	$t->set_var("search_href",   get_custom_friendly_url("products_search.php"));
	$t->set_var("search_name",   PRODUCTS_TITLE);

	$category_id = get_param("category_id");
	if (!$category_id) {
		$category_id = get_param("search_category_id");
	}
	$search_string = trim(get_param("search_string"));
	if (!strlen($category_id)) { $category_id = 0; }

	$query_string = transfer_params("", false);
	$t->set_var("advanced_search_href", get_custom_friendly_url("search.php") . $query_string);
	$t->global_parse("advanced_search", false, false, true);

	$search_categories[] = array(0, SEARCH_IN_ALL_MSG);
  
	if($category_id != 0) {
		$search_categories[] = array($category_id, SEARCH_IN_CURRENT_MSG);
	}

	$categories_ids = VA_Categories::find_all_ids("c.parent_category_id = " . $db->tosql($category_id, INTEGER), VIEW_CATEGORIES_ITEMS_PERM);
	if ($categories_ids) {
		$sql  = " SELECT category_id, category_name ";
		$sql .= " FROM " . $table_prefix . "categories ";
		$sql .= " WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ") ";
		$sql .= " ORDER BY category_order ";
		$search_categories = get_db_values($sql, $search_categories);
	}

	// set up search form parameters
	$t->set_var("no_search_categories", "");
	if (sizeof($search_categories) > 1) {
		set_options($search_categories, $category_id, "search_category_id");
		$t->global_parse("search_categories", false, false, true);
	} else {
		$t->set_var("search_categories", "");
	}
	$t->set_var("search_string", htmlspecialchars($search_string));
	$t->set_var("current_category_id", htmlspecialchars($category_id));

	if ($block_name) {
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>