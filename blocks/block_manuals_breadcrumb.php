<?php

function manuals_breadcrumb($block_name, $erase_tags = true)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings;
	global $category_id, $is_reviews, $thread_id;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$manual_id = get_param("manual_id");
	$category_id = get_param("category_id");
	$article_id = get_param("article_id");

	$manuals_search = get_param("manuals_search");

	$breadcrumbs_tree_array = array();
		
	$t->set_file("block_body", "block_manuals_breadcrumb.html");
	$t->set_var("list_href",   get_custom_friendly_url("manuals.php"));
	$t->set_var("article_href", "manuals_article_detailes.php");

	$t->set_var("index_href", get_custom_friendly_url("index.php"));
	$t->set_var("HOME_PAGE_TITLE", HOME_PAGE_TITLE);
	$tree_title = MANUALS_TITLE;
	if ($erase_tags) { $tree_title = strip_tags($tree_title); }
	$breadcrumbs_tree_array[] = array( get_custom_friendly_url("manuals.php"), $tree_title);

	$article_title = ""; 
	$manual_title = "";
	$category_name = "";
	$manual_friendly_url = ""; 
	$article_friendly_url = "";
	
	if ($article_id) {
		$sql  = " SELECT ma.article_id, ma.friendly_url AS article_friendly_url, ma.article_title,";
		$sql .= " ml.manual_id, ml.friendly_url AS manual_friendly_url, ml.manual_title, ";
		$sql .= " mc.category_id, mc.friendly_url AS category_friendly_url, mc.category_name";
		$sql .= " FROM " . $table_prefix . "manuals_articles ma,";
		$sql .= $table_prefix . "manuals_list ml, ";
		$sql .= $table_prefix . "manuals_categories mc ";
		$sql .= " WHERE ma.article_id = " . $db->tosql($article_id, INTEGER);
		$sql .= " AND ma.manual_id = ml.manual_id AND mc.category_id = ml.category_id";

		$db->query($sql);
		if ($db->next_record()) {
			$article_id = $db->f("article_id");
			$article_title = $db->f("article_title");
			$article_friendly_url = $db->f("article_friendly_url");
			
			$category_id = $db->f("category_id");
			$category_name = $db->f("category_name");
			$category_friendly_url = $db->f("category_friendly_url");

			$manual_id = $db->f("manual_id");
			$manual_title = $db->f("manual_title");
			$manual_friendly_url = $db->f("manual_friendly_url");
		} else {
			$article_id = 0;
			$manual_id = 0;
			$category_id = 0;
		}
	} else if ($manual_id) {
		$sql  = " SELECT ml.manual_id, ml.friendly_url AS manual_friendly_url, ml.manual_title, ";
		$sql .= " mc.category_id, mc.friendly_url AS category_friendly_url, mc.category_name";
		$sql .= " FROM " . $table_prefix . "manuals_list ml, ";
		$sql .= $table_prefix . "manuals_categories mc ";
		$sql .= " WHERE ml.manual_id = " . $db->tosql($manual_id, INTEGER);
		$sql .= " AND mc.category_id = ml.category_id";

		$db->query($sql);
		if ($db->next_record()) {
			$category_id = $db->f("category_id");
			$category_name = $db->f("category_name");
			$category_friendly_url = $db->f("category_friendly_url");

			$manual_id = $db->f("manual_id");
			$manual_title = $db->f("manual_title");
			$manual_friendly_url = $db->f("manual_friendly_url");
		} else {
			$article_id = 0;
			$manual_id = 0;
			$category_id = 0;
		}		
	} elseif ($category_id) {
		$sql  = " SELECT mc.category_id, mc.friendly_url AS category_friendly_url, mc.category_name";
		$sql .= " FROM " . $table_prefix . "manuals_categories mc ";
		$sql .= " WHERE mc.category_id = " . $db->tosql($category_id, INTEGER);

		$db->query($sql);
		if ($db->next_record()) {
			$category_id = $db->f("category_id");
			$category_name = $db->f("category_name");
			$category_friendly_url = $db->f("category_friendly_url");
		} else {
			$article_id = 0;
			$manual_id = 0;
			$category_id = 0;
		}		
	}
	
	if ($category_id) {
		if ($friendly_urls && $category_friendly_url) {
			$tree_url = $category_friendly_url . $friendly_extension;
		} else {
			$tree_url = "manuals.php?category_id=" . $category_id;
		}
		
		$tree_title = $category_name;
		if ($erase_tags) { $tree_title = strip_tags($tree_title); }
		$breadcrumbs_tree_array[] = array($tree_url, $tree_title);
	}

	if ($manual_id) {
		if ($friendly_urls && $manual_friendly_url) {
			$tree_url = $manual_friendly_url . $friendly_extension;
		} else {
			$tree_url = "manuals_articles.php?manual_id=" . $manual_id;
		}
		$tree_title = $manual_title;
		if ($erase_tags) { $tree_title = strip_tags($tree_title); }
		$breadcrumbs_tree_array[] = array($tree_url, $tree_title);
	}
	
	// check search
	if (strlen($manuals_search)) {
		$ps_parameters["manuals_search"] = get_param("manuals_search");
		$ps_parameters["manual_id"] = $manual_id;
		$query_string = get_query_string($ps_parameters, "", "", false);
		$tree_url = "manuals_search.php" . $query_string;
		$tree_title = SEARCH_RESULTS_MSG;
		if ($erase_tags) { $tree_title = strip_tags($tree_title); }
		$breadcrumbs_tree_array[] = array($tree_url, $tree_title);
	}
	if ($article_id) {
		if ($friendly_urls && $article_friendly_url) {
			$tree_url = $article_friendly_url . $friendly_extension;
		} else {
			$tree_url = "manuals_article_detailes.php?article_id=" . $article_id;
		}
		$tree_title = $article_title;
		if ($erase_tags) { $tree_title = strip_tags($tree_title); }
		$breadcrumbs_tree_array[] = array($tree_url, $tree_title);
	}
	
	$ic = count($breadcrumbs_tree_array) - 1;
	for ($i=0; $i<$ic; $i++) {
		$t->set_var("tree_url", $breadcrumbs_tree_array[$i][0]);
		$t->set_var("tree_title", $breadcrumbs_tree_array[$i][1]);
		$t->set_var("tree_class", "");
		$t->parse("tree", true);
	}
	if ($ic>=0) {
		$t->set_var("tree_url", $breadcrumbs_tree_array[$ic][0]);
		$t->set_var("tree_title", $breadcrumbs_tree_array[$ic][1]);
		$t->set_var("tree_class", "treeItemLast");
		$t->parse("tree", true);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>