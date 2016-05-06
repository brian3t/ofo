<?php
include_once("./includes/articles_functions.php");

function articles_search($block_name, $top_id, $top_name, $current_category_id = 0)
{
	global $t, $db, $table_prefix;
	global $category_id;
	global $page_settings;
	
	$user_id = get_session("session_user_id");		
	$user_info = get_session("session_user_info");
	$user_type_id = get_setting_value($user_info, "user_type_id", "");

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	if (!strlen($top_name) && !VA_Articles_Categories::check_permissions($top_id, VIEW_CATEGORIES_ITEMS_PERM)) {
		$sql  = " SELECT category_name ";
		$sql .= " FROM " . $table_prefix . "articles_categories ";				
		$sql .= " WHERE category_id=" . $db->tosql($top_id, INTEGER);			
						
		$db->query($sql);
		if ($db->next_record()) {
			$top_name         = get_translation($db->f("category_name"));
		} else {
			return false;
		}
	}

	if($block_name) {
		$t->set_file("block_body", "block_search.html");
	}
	$t->set_var("search_href",   "articles.php");
	$t->set_var("search_name",   $top_name);
	$t->set_var("SEARCH_TITLE",  SEARCH_TITLE);
	$t->set_var("GO_BUTTON",     GO_BUTTON);
	$t->set_var("SEARCH_BUTTON", SEARCH_BUTTON);

	$search_string = trim(get_param("search_string"));
	$is_search = strlen($search_string);

	$t->set_var("advanced_search", "");

	$search_categories[] = array($top_id, SEARCH_IN_ALL_MSG);
  
	if($top_id != $category_id && $category_id != 0) {
		$search_categories[] = array($category_id, SEARCH_IN_CURRENT_MSG);
	}
	
	if ($current_category_id) {	
		$where = "c.parent_category_id = " . $db->tosql($current_category_id, INTEGER);
	} else {
		$where = "c.parent_category_id = " . $db->tosql($top_id, INTEGER);
	}
	$categories_ids = VA_Articles_Categories::find_all_ids($where, VIEW_CATEGORIES_ITEMS_PERM);
	if ($categories_ids) {

		$sql  = " SELECT category_id, category_name FROM " . $table_prefix . "articles_categories ";	
		$sql .= " WHERE  category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")";
		$sql .= " ORDER BY category_order ";
		$db->query($sql);
		while ($db->next_record()) {
			$show_category_id  = $db->f("category_id");
			$category_name  = get_translation($db->f("category_name"));
			$search_categories[] = array($show_category_id, $category_name);
		}
	}

	// set up search form parameters
	if (sizeof($search_categories) > 1) {
		set_options($search_categories, $current_category_id, "search_category_id");
		$t->global_parse("search_categories", false, false, true);
		$t->set_var("no_search_categories", "");
	} else {
		$t->set_var("search_categories", "");
		$t->set_var("top_id", $top_id);
		$t->sparse("no_search_categories", false);
	}

	$t->set_var("search_string", htmlspecialchars($search_string));
	if ($current_category_id > 0) {
		$t->set_var("current_category_id", htmlspecialchars($current_category_id));
	} else {
		$t->set_var("current_category_id", htmlspecialchars($top_id));
	}

	if($block_name) {
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>