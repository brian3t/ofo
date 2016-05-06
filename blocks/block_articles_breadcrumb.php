<?php
include_once("./includes/articles_functions.php");

function articles_breadcrumb($block_name, $category_id, $top_id, $rss_on_breadcrumb = false, $erase_tags = true)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings;
	global $is_reviews;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$t->set_file("block_body", "block_articles_breadcrumb.html");

	$article_id = get_param("article_id");
	$search_category_id = get_param("search_category_id");
	if (strlen($search_category_id)) {
		$category_id = $search_category_id;
	}

	$breadcrumbs_tree_array = array();
	$t->set_var("index_href", get_custom_friendly_url("index.php"));
	$t->set_var("HOME_PAGE_TITLE", HOME_PAGE_TITLE);

	if ($category_id) {
		$current_id = $category_id;		
		while ($current_id) {
			$category_values = VA_Articles_Categories::find_all(false, 
				array("c.category_name", "c.friendly_url", "c.parent_category_id"),
				"c.category_id=" . $db->tosql($current_id, INTEGER), VIEW_CATEGORIES_PERM);
			if ($category_values) {
				$category_name = $category_values[0]["c.category_name"];
				$category_name = get_translation($category_name);
				$friendly_url  = $category_values[0]["c.friendly_url"];;
				if ($friendly_urls && $friendly_url) {
					$tree_url = $friendly_url . $friendly_extension;
				} else {
					$tree_url = "articles.php?category_id=". $current_id;
				}
				$tree_title = $category_name;
				if ($erase_tags) { $tree_title = strip_tags($tree_title); }
				array_unshift($breadcrumbs_tree_array, array($tree_url, $tree_title));
				$current_id=  $category_values[0]["c.parent_category_id"];
			} else {
				$current_id = "0";
			}
		}
	}

	// check search
	$ps_parameters = array();
	$search_params = array(
		"search_string"
	);
	for ($si = 0; $si < sizeof($search_params); $si++) {
		$search_param = $search_params[$si];
		$param_value  = get_param($search_param);
		if (strlen($param_value)) {
			$ps_parameters[$search_param] = $param_value;
		}
	}

	// Proceed products search parameters
	if (sizeof($ps_parameters) > 0) {
		$ps_parameters["s_tit"] = get_param("s_tit");
		$ps_parameters["s_cod"] = get_param("s_cod");
		$ps_parameters["s_sds"] = get_param("s_sds");
		$ps_parameters["s_fds"] = get_param("s_fds");
		$ps_parameters["category_id"] = $category_id;
		$query_string = get_query_string($ps_parameters, "", "", false);
		$tree_url = "articles.php" . $query_string;
		$tree_title = SEARCH_RESULTS_MSG;
		if ($erase_tags) { $tree_title = strip_tags($tree_title); }		
		$breadcrumbs_tree_array[] = array($tree_url, $tree_title);
	}

	if (strlen($article_id) && VA_Articles::check_permissions($article_id, false, VIEW_ITEMS_PERM)) {
		$ps_parameters["category_id"] = $category_id;
		$sql = "SELECT article_title, friendly_url FROM " . $table_prefix . "articles WHERE article_id=" . $db->tosql($article_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$article_title = get_translation($db->f("article_title"));
			$friendly_url = $db->f("friendly_url");
			if ($friendly_urls && $friendly_url) {
				$query_string = get_query_string($ps_parameters, "", "", false);
				$tree_url = $friendly_url . $friendly_extension . $query_string;
			} else {
				$ps_parameters["article_id"] = $article_id;
				$query_string = get_query_string($ps_parameters, "", "", false);
				$tree_url = "article.php" . $query_string;
			}

			$tree_title = $article_title;
			if ($erase_tags) { $tree_title = strip_tags($tree_title); }
			$breadcrumbs_tree_array[] = array($tree_url, $tree_title);
		}
	}

	if ($is_reviews) {
		$tree_url = "articles_reviews.php?category_id=" . urlencode($category_id) . "&article_id=" . urlencode($article_id);
		$tree_title = REVIEWS_MSG;
		if ($erase_tags) { $tree_title = strip_tags($tree_title); }
		$breadcrumbs_tree_array[] = array($tree_url, $tree_title);
	}

	if ($rss_on_breadcrumb) {
		$t->set_var("tree_current_id", $category_id);
		$t->set_var("rss_href","articles_rss.php");
		$t->set_var("rss_url","articles_rss.php?category_id=" . urlencode($category_id));
		$t->parse("rss", false);
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