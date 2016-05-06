<?php
include_once($root_folder_path . "includes/forums_functions.php");
	
function forum_topics_show($block_name, $forum_name, $page_friendly_url = "", $page_friendly_params = array()) 
{ 
	global $t, $db, $table_prefix, $settings;
	global $datetime_show_format;
	global $site_id, $db_type;

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$forum_id    = get_param("forum_id");
	$category_id = get_param("category_id");
	
	$sf = get_param("sf");
	$sw = trim(get_param("sw"));
	$u  = get_param("u");
	if (!$forum_id && preg_match("/^f(\d+)$/i", $sf, $match)) {
		$forum_id = $match[1];
	} else if (!$category_id && preg_match("/^c(\d+)$/i", $sf, $match)) {
		$category_id = $match[1];
	}

	if ($friendly_urls && $page_friendly_url) {
		$pass_parameters = get_transfer_params($page_friendly_params);
		$remove_parameters = $page_friendly_params;
		$forum_page = $page_friendly_url . $friendly_extension;
	} else {
		$pass_parameters = get_transfer_params();
		$remove_parameters = array();
		$forum_page = get_custom_friendly_url("forum.php");
	}

	$t->set_file("block_body","block_forum_topics.html");

	$t->set_var("forum_href", get_custom_friendly_url("forum_categories.php"));
	$t->set_var("user_home_href", get_custom_friendly_url("user_home.php"));
	$t->set_var("all_forum_href", get_custom_friendly_url("forum.php"));
	$t->set_var("user_forum_href", get_custom_friendly_url("forum.php") . "?u=1");
	$t->set_var("forum_name", htmlspecialchars($forum_name));


	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", $forum_page, "sort", $remove_parameters);
	$s->set_parameters(false, true, true, false);
	$s->set_sorter(TOPIC_NAME_COLUMN, "sorter_topic", "1", "topic");
	$s->set_sorter(TOPIC_AUTHOR_COLUMN, "sorter_user_name", "2", "user_name");
	$s->set_sorter(TOPIC_VIEWS_COLUMN, "sorter_views", "3", "views");
	$s->set_sorter(TOPIC_REPLIES_COLUMN, "sorter_replies", "4", "replies");
	$s->set_sorter(TOPIC_UPDATED_COLUMN, "sorter_date_modified", "5", "thread_updated");
	$s->set_sorter(TOPIC_UPDATED_COLUMN, "sorter_thread_updated", "5", "thread_updated");
	if (!$s->order_by) {
		$s->order_by = " ORDER BY fp.priority_rank, f.thread_updated DESC ";
	}

	$sw = trim(get_param("sw"));
	
	$sql_where = "";
	if ($category_id) {
		if (strlen($sql_where)) $sql_where .= " AND ";
		$sql_where .= " c.category_id=" . $db->tosql($category_id, INTEGER);
	} else if (preg_match("/^c(\d+)$/i", $sf, $match)) {
		if (strlen($sql_where)) $sql_where .= " AND ";
		$sql_where .= " c.category_id=" . $db->tosql($match[1], INTEGER);
	}
	if ($forum_id) {
		if (strlen($sql_where)) $sql_where .= " AND ";
		$sql_where .= " fl.forum_id=" . $db->tosql($forum_id, INTEGER);
	} else if (preg_match("/^f(\d+)$/i", $sf, $match)) {
		if (strlen($sql_where)) $sql_where .= " AND ";
		$sql_where .= " fl.forum_id=" . $db->tosql($match[1], INTEGER);
	}	
	$forums_ids = VA_Forums::find_all_ids($sql_where, VIEW_TOPICS_PERM);
	if (!$forums_ids) return;
	
	$sql_where = " WHERE f.forum_id IN (" . $db->tosql($forums_ids, INTEGERS_LIST) . ")";	
	if ($u && strlen(get_session("session_user_id"))) {
		$sql_where .= " AND f.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	}	
	if (strlen($sw)) {
		$search_values = split(" ", $sw);
		for($si = 0; $si < sizeof($search_values); $si++) {		
			$sql_where .= " AND ( f.topic LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%' OR f.description LIKE '%" . $db->tosql($search_values[$si], TEXT, false) . "%'  ) ";
		}
	}
		
	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $forum_page);
	$n->set_parameters(false, true, false);

	$sql  = " SELECT COUNT(thread_id) FROM " . $table_prefix . "forum f ";
	$sql .= $sql_where;
	$total_records = get_db_value($sql);
	
	$records_per_page = 25;
	$pages_number = 10;
	$page_number = $n->set_navigator("navigator", "page", CENTERED, $pages_number, $records_per_page, $total_records, false, $pass_parameters);

	$sql  = " SELECT f.forum_id, f.thread_id, f.friendly_url, f.topic, f.user_id, f.admin_id_added_by, ";
	$sql .= " f.user_name, f.views, f.replies, f.thread_updated, ";
	$sql .= " f.last_post_added, f.last_post_user_id, f.last_post_admin_id, f.last_post_message_id, ";
	$sql .= " fp.html_before_title, fp.html_after_title ";
	$sql .= " FROM (" . $table_prefix . "forum f ";
	$sql .= " LEFT JOIN " . $table_prefix . "forum_priorities fp ON f.priority_id=fp.priority_id) ";
	$sql .= $sql_where;
	
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber     = $page_number;
	$db->query($sql . $s->order_by );
	
	$topics = array(); $tp = 0;
	while ($db->next_record()) {
		$topics[$tp]["forum_id"] = $db->f("forum_id");
		$topics[$tp]["thread_id"] = $db->f("thread_id");
		$topics[$tp]["friendly_url"] = $db->f("friendly_url");
		$topics[$tp]["topic"] = get_translation($db->f("topic"));
		$topics[$tp]["html_before_title"] = get_translation($db->f("html_before_title"));
		$topics[$tp]["html_after_title"] = get_translation($db->f("html_after_title"));
		$topics[$tp]["user_id"] = $db->f("user_id");
		$topics[$tp]["admin_id_added_by"] = $db->f("admin_id_added_by");
		$topics[$tp]["user_name"] = $db->f("user_name");
		$topics[$tp]["views"] = $db->f("views");
		$topics[$tp]["replies"] = $db->f("replies");
		$topics[$tp]["thread_updated"] = $db->f("thread_updated", DATETIME);
		$topics[$tp]["last_post_added"] = $db->f("last_post_added", DATETIME);
		$topics[$tp]["last_post_user_id"] = $db->f("last_post_user_id");
		$topics[$tp]["last_post_admin_id"] = $db->f("last_post_admin_id");
		$topics[$tp]["last_post_message_id"] = $db->f("last_post_message_id");		
		$tp++;
	}

	$topic_is_blocked = array();
	if ($tp > 0) {
		$allowed_topic_view  = VA_Forums::find_all_ids(" fl.forum_id IN (" . $db->tosql($forums_ids, INTEGERS_LIST) . ") ", VIEW_TOPIC_PERM);
		
		$t->set_var("no_records", "");
		$t->parse("sorters", false);
		for ($i = 0; $i < $tp; $i++) {
			$current_forum_id = $topics[$i]["forum_id"];
			$thread_id = $topics[$i]["thread_id"];
			$friendly_url = $topics[$i]["friendly_url"];
			$topic = $topics[$i]["topic"];
			$html_before_title = $topics[$i]["html_before_title"];
			$html_after_title = $topics[$i]["html_after_title"];
			$user_id = $topics[$i]["user_id"];
			$admin_id_added_by = $topics[$i]["admin_id_added_by"];
			$topic_author = $topics[$i]["user_name"];
			$views = $topics[$i]["views"];
			$replies = $topics[$i]["replies"];
			$thread_updated = $topics[$i]["thread_updated"];
			$last_post_added = $topics[$i]["last_post_added"];
			$last_post_user_id = $topics[$i]["last_post_user_id"];
			$last_post_admin_id = $topics[$i]["last_post_admin_id"];
			$last_post_message_id = $topics[$i]["last_post_message_id"];

			if ($friendly_urls && $friendly_url) {
				$t->set_var("forum_thread_href", $friendly_url . $friendly_extension);
				$forum_thread_page_url = $friendly_url . $friendly_extension . "?page=";
			} else {
				$t->set_var("forum_thread_href", get_custom_friendly_url("forum_topic.php") . "?thread_id=" . $thread_id);
				$forum_thread_page_url = get_custom_friendly_url("forum_topic.php") . "?thread_id=" . $thread_id . "&page=";
			}
			if (isset($allowed_view_topic)) {
				if ($allowed_view_topic) {
					$t->set_var("block_topic", "");
				} else {
					$t->sparse('block_topic', false);				
				}
			} else {
				
				if ($allowed_topic_view && in_array($current_forum_id, $allowed_topic_view)) {
					$t->set_var("block_topic", "");
				} else {
					$t->sparse('block_topic', false);				
				}
				
			}
			$t->set_var("thread_id", $thread_id);
			$t->set_var("topic", htmlspecialchars($topic));
			$t->set_var("html_before_title", $html_before_title);
			$t->set_var("html_after_title", $html_after_title);
			$t->set_var("views", intval($views));
			$t->set_var("replies", intval($replies));
			$t->set_var("date_modified", va_date($datetime_show_format, $thread_updated));
			$t->set_var("thread_updated", va_date($datetime_show_format, $thread_updated));
			// check if need to show pages number 
			$topic_recs = 25;
			if ($replies > $topic_recs) {
				$t->set_var("topic_pages", "");
				$total_pages = ceil($replies / $topic_recs);
				if ($total_pages > 5) {
					$start_page = $total_pages - 2;
					$t->set_var("forum_thread_page_url", $forum_thread_page_url . "2");
					$t->set_var("page_number", "&nbsp;...");
					$t->sparse("topic_pages", true);
				} else {
					$start_page = 2;
				}
				for ($p = $start_page; $p <= $total_pages; $p++) {
					$t->set_var("forum_thread_page_url", $forum_thread_page_url . $p);
					$t->set_var("page_number", "&nbsp;".$p);
					$t->sparse("topic_pages", true);
				}
				$t->sparse("topic_pages_block", false);
			} else {
				$t->set_var("topic_pages_block", "");
			}

			// topic author
			$topic_author .= " (" . GUEST_MSG . ")";
			$topic_author_class = "forumGuest";
			if ($user_id) {
				$sql  = " SELECT login, nickname FROM " . $table_prefix . "users ";
				$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$topic_author = $db->f("nickname");
					if (!strlen($topic_author)) { $topic_author = $db->f("login"); }
					$topic_author_class = "forumUser";
				}
			} else if ($admin_id_added_by) {
				$sql  = " SELECT admin_name, nickname FROM " . $table_prefix . "admins ";
				$sql .= " WHERE admin_id=" . $db->tosql($admin_id_added_by, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$topic_author = $db->f("nickname");
					if (!strlen($topic_author)) { $topic_author = $db->f("admin_name"); }
					$topic_author_class = "forumAdmin";
				}
			}
			$t->set_var("user_name", htmlspecialchars($topic_author));
			$t->set_var("topic_author", htmlspecialchars($topic_author));
			$t->set_var("topic_author_class", $topic_author_class);

			// last post by
			$t->set_var("last_post_by_block", "");
			if ($last_post_user_id) {
				$sql  = " SELECT login, nickname FROM " . $table_prefix . "users ";
				$sql .= " WHERE user_id=" . $db->tosql($last_post_user_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$nickname = $db->f("nickname");
					if (!strlen($nickname)) { $nickname = $db->f("login"); }
					$t->set_var("forum_user_class", "forumUser");
					$t->set_var("last_post_by", $nickname);
					$t->parse("last_post_by_block", false);
				}
			} else if ($last_post_admin_id) {
				$sql  = " SELECT admin_name, nickname FROM " . $table_prefix . "admins ";
				$sql .= " WHERE admin_id=" . $db->tosql($last_post_admin_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$nickname = $db->f("nickname");
					if (!strlen($nickname)) { $nickname = $db->f("admin_name"); }
					$t->set_var("forum_user_class", "forumAdmin");
					$t->set_var("last_post_by", $nickname);
					$t->parse("last_post_by_block", false);
				}
				$t->parse("last_post_by_block", false);
			}

			$t->parse("records", true);
		} 
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	if (VA_Forums::check_permissions($forum_id, POST_TOPICS_PERM)) {
		$forum_topic_new_url = get_custom_friendly_url("forum_topic_new.php") . "?forum_id=" . urlencode($forum_id);
		$t->set_var("forum_topic_new_url", $forum_topic_new_url);
		$t->parse("new_topic_block", false);
	}

	// show search results information
	if(strlen($sw) || $u) {
		$found_message = str_replace("{found_records}", $total_records, FOUND_TOPICS_MSG);
		$found_message = str_replace("{search_string}", htmlspecialchars($sw), $found_message);
		$t->set_var("found_message", $found_message);
		$t->parse("search_results", false);
	} 

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>