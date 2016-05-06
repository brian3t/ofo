<?php

function forum_breadcrumb($block_name, $erase_tags = true)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings;
	global $category_id, $is_reviews, $thread_id;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$forum_id = get_param("forum_id");
	$category_id = get_param("category_id");
	$thread_id = get_param("thread_id");
	$sf = get_param("sf");
	$sw = trim(get_param("sw"));
	$u = get_param("u");
	if (!$forum_id && preg_match("/^f(\d+)$/i", $sf, $match)) {
		$forum_id = $match[1];
	} else if (!$category_id && preg_match("/^c(\d+)$/i", $sf, $match)) {
		$category_id = $match[1];
	}

	$breadcrumbs_tree_array = array();
		
	$t->set_file("block_body", "block_forum_breadcrumb.html");
	$t->set_var("list_href",   get_custom_friendly_url("forums.php"));
	$t->set_var("forum_href",  "forum.php");
	$t->set_var("forum_topic_href", "forum_topic.php");
	$t->set_var("index_href", get_custom_friendly_url("index.php"));
	$t->set_var("HOME_PAGE_TITLE", HOME_PAGE_TITLE);

	$tree_title = FORUM_TITLE;
	if ($erase_tags) { $tree_title = strip_tags($tree_title); }
	$breadcrumbs_tree_array[] = array(get_custom_friendly_url("forums.php"), $tree_title);
	
	$topic_name = ""; $forum_friendly_url = ""; $topic_friendly_url = "";
	if ($thread_id) {
		$sql  = " SELECT fc.category_id, fc.category_name, fc.friendly_url AS category_friendly_url, ";
		$sql .= " fl.forum_id, fl.forum_name, fl.friendly_url AS forum_friendly_url, ";
		$sql .= " f.topic, f.friendly_url AS topic_friendly_url ";
		$sql .= " FROM " . $table_prefix . "forum_categories fc, " . $table_prefix . "forum_list fl, " . $table_prefix . "forum f ";
		$sql .= " WHERE fl.category_id=fc.category_id AND f.forum_id=fl.forum_id AND f.thread_id=" . $db->tosql($thread_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$topic_name = get_translation($db->f("topic"));
			$topic_friendly_url = $db->f("topic_friendly_url");
			$forum_id = $db->f("forum_id");
			$forum_name = get_translation($db->f("forum_name"));
			$forum_friendly_url = $db->f("forum_friendly_url");
			$category_id = $db->f("category_id");
			$category_name = get_translation($db->f("category_name"));
			$category_friendly_url = $db->f("category_friendly_url");
		} else {
			$thread_id = ""; $forum_id = ""; $category_id = "";
		}
	} else if ($forum_id) {
		$sql  = " SELECT fc.category_id, fc.category_name, fc.friendly_url AS category_friendly_url, ";
		$sql .= " fl.forum_name, fl.friendly_url AS forum_friendly_url ";
		$sql .= " FROM " . $table_prefix . "forum_categories fc, " . $table_prefix . "forum_list fl ";
		$sql .= " WHERE fl.category_id=fc.category_id AND fl.forum_id=" . $db->tosql($forum_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$forum_name = get_translation($db->f("forum_name"));
			$forum_friendly_url = $db->f("forum_friendly_url");
			$category_id = $db->f("category_id");
			$category_name = get_translation($db->f("category_name"));
			$category_friendly_url = $db->f("category_friendly_url");
		} else {
			$thread_id = ""; $forum_id = ""; $category_id = "";
		}
	} else if ($category_id) {
		$sql  = " SELECT fc.category_name, fc.friendly_url AS category_friendly_url ";
		$sql .= " FROM " . $table_prefix . "forum_categories fc ";
		$sql .= " WHERE fc.category_id=" . $db->tosql($category_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$category_name = get_translation($db->f("category_name"));
			$category_friendly_url = $db->f("category_friendly_url");
		} else {
			$thread_id = ""; $forum_id = ""; $category_id = "";
		}
	}
	
	if ($category_id) {
		if ($friendly_urls && $category_friendly_url) {
			$tree_url = $category_friendly_url . $friendly_extension;
		} else {
			$tree_url = "forums.php?category_id=" . urlencode($category_id);
		}
		
		$tree_title = $category_name;
		if ($erase_tags) { $tree_title = strip_tags($tree_title); }
		$breadcrumbs_tree_array[] = array($tree_url, $tree_title);
	}

	if ($forum_id) {
		if ($friendly_urls && $forum_friendly_url) {
			$tree_url = $forum_friendly_url . $friendly_extension;
		} else {
			$tree_url = "forum.php?forum_id=" . urlencode($forum_id);
		}
		$tree_title = $forum_name;
		if ($erase_tags) { $tree_title = strip_tags($tree_title); }		
		$breadcrumbs_tree_array[] = array($tree_url, $tree_title);
	}


	// check search
	$ps_parameters = array();
	if ($sf || strlen($sw) || $u) {
		$ps_parameters["sf"] = get_param("sf");
		$ps_parameters["sw"] = get_param("sw");
		$ps_parameters["u"] = get_param("u");
		if ($friendly_urls && $forum_friendly_url) {
			$query_string = get_query_string($ps_parameters, "", "", false);
			$tree_url = $forum_friendly_url . $friendly_extension . $query_string;
		} else {
			$ps_parameters["forum_id"] = $forum_id;
			$query_string = get_query_string($ps_parameters, "", "", false);
			$tree_url = "forum.php" . $query_string;
		}
		$tree_title = SEARCH_RESULTS_MSG;
		if ($erase_tags) { $tree_title = strip_tags($tree_title); }
		$breadcrumbs_tree_array[] = array($tree_url, $tree_title);
	}


	if ($thread_id) {
		if ($friendly_urls && $topic_friendly_url) {
			$query_string = get_query_string($ps_parameters, "", "", false);
			$tree_url = $topic_friendly_url . $friendly_extension . $query_string;
		} else {
			$ps_parameters["thread_id"] = $thread_id;
			$query_string = get_query_string($ps_parameters, "", "", false);
			$tree_url = "forum_topic.php" . $query_string;
		}
		$tree_title = $topic_name;
		if ($erase_tags) { $tree_title = strip_tags($tree_title); }
		$breadcrumbs_tree_array[] = array($tree_url, htmlspecialchars($tree_title));
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