<?php	
include_once($root_folder_path . "includes/forums_functions.php");

function related_forums($block_name, $related_type = "items_forum_topics", $page_friendly_url = "", $page_friendly_params = array())
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings, $site_id, $datetime_show_format;
	
	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}
	
	// get forum settings
	$forum_settings = array();
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='forum'";
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$forum_settings[$db->f("setting_name")] = $db->f("setting_value");
	}
	$user_images     = get_setting_value($forum_settings, "user_images", 1);
	$forum_user_info = get_setting_value($forum_settings, "user_info", 1);
	$user_no_image   = get_setting_value($forum_settings, "user_no_image", "");
	$icons_enable    = get_setting_value($forum_settings, "icons_enable", 0);
					
	$user_id      = get_session("session_user_id");	
	$user_info    = get_session("session_user_info");
	$user_type_id = get_setting_value($user_info, "user_type_id", "");
	
	$friendly_urls      = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	
	// additional connection to get forum details
	$db2 = new VA_SQL();
	$db2->DBType      = $db->DBType;
	$db2->DBDatabase  = $db->DBDatabase;
	$db2->DBHost      = $db->DBHost;
	$db2->DBPort      = $db->DBPort;
	$db2->DBUser      = $db->DBUser;
	$db2->DBPassword  = $db->DBPassword;
	$db2->DBPersistent= $db->DBPersistent;	
	
	$item_id     = get_param("item_id");
	$article_id  = get_param("article_id");
	$category_id = (int) get_param("category_id");
	
	$related_type_table = "";
	$related_type_where = "";
	if ($related_type == "items_forum_topics") {
		$related_type_table = $table_prefix . "items_forum_topics rel";
		$related_type_where = " AND  rel.item_id=" . $db->tosql($item_id, INTEGER);
		
		$forums_related_columns  = get_setting_value($page_settings, "forums_related_columns", 1);
		$forums_related_per_page = get_setting_value($page_settings, "forums_related_per_page", 4);
		$forums_related_desc     = get_setting_value($page_settings, "forums_related_desc", 0);
		$forums_related_user_info = get_setting_value($page_settings, "forums_related_user_info", 0);	
	
	} elseif ($related_type == "articles_forum_topics") {
		$related_type_table = $table_prefix . "articles_forum_topics rel";
		$related_type_where = " AND  rel.article_id=" . $db->tosql($article_id, INTEGER);
		
		
		$sql = "SELECT ac.category_path, ac.category_id FROM " . $table_prefix . "articles_categories ac ";
		$sql .= " INNER JOIN " . $table_prefix . "articles_assigned aas ON aas.category_id=ac.category_id ";
		$sql .= " WHERE aas.article_id=" . $db->tosql($article_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$category_id = $db->f("category_id");
			$category_path = $db->f("category_path");
			if ("0," == $category_path) {
				$top_category_id = $category_id;
			} else {
				$category_path_parts = explode(",", $category_path);
				if (isset($category_path_parts[1])) {
					$top_category_id = $category_path_parts[1];
				} else {
					$top_category_id = $category_id;
				}
			}
		} else {
			$top_category_id = "0";
		}
		
		$forums_related_columns  = get_setting_value($page_settings, "a_forums_related_columns_" . $top_category_id, 1);
		$forums_related_per_page = get_setting_value($page_settings, "a_forums_related_per_page_" . $top_category_id, 4);
		$forums_related_desc     = get_setting_value($page_settings, "a_forums_related_desc_" . $top_category_id, 0);
		$forums_related_user_info = get_setting_value($page_settings, "a_forums_related_user_info_" . $top_category_id, 0);	
	}
	
	$t->set_file("block_body", "block_forums_related.html");

	if ($friendly_urls && $page_friendly_url) {
		$pass_parameters = get_transfer_params($page_friendly_params);
		$main_page = $page_friendly_url . $friendly_extension;
	} else if ($related_type == "items_forum_topics") {
		$pass_parameters = get_transfer_params();
		$main_page = get_custom_friendly_url("product_details.php");
	} elseif ($related_type == "articles_forum_topics") {
		$pass_parameters = get_transfer_params();
		$main_page = get_custom_friendly_url("article.php");
	}
	
	// main sql query
	
	$forums_ids = VA_Forums::find_all_ids("fl.threads_number > 0", VIEW_TOPICS_PERM);
	if (!$forums_ids) return;	
	$allowed_topic_view = VA_Forums::find_all_ids(" fl.forum_id IN (" . $db->tosql($forums_ids, INTEGERS_LIST) . ") ", VIEW_TOPIC_PERM);
	
	$sql  = " SELECT rel.thread_id ";
	$sql .= " FROM (";
	$sql .= $related_type_table;
	$sql .= " INNER JOIN " . $table_prefix . "forum f ON f.thread_id = rel.thread_id) ";
	$sql .= " WHERE f.forum_id IN ( ". $db->tosql($forums_ids, INTEGERS_LIST) . ")";
	$sql .= $related_type_where;
	$db->query($sql);
	$thread_ids = array();
	while ($db->next_record()) {
		$thread_ids[] = $db->f("thread_id");
	}
	$total_records = count($thread_ids);
	if ($total_records) {	
		$pages_number = 5;
		$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $main_page);
		$page_number = $n->set_navigator("forums_related_navigator", "related_forum_page", SIMPLE, $pages_number, $forums_related_per_page, $total_records, false, $pass_parameters);
	
		$db->RecordsPerPage = $forums_related_per_page;
		$db->PageNumber     = $page_number;
		
		$sql  = " SELECT f.* ";
		$sql .= " FROM (";
		$sql .= $related_type_table;
		$sql .= " INNER JOIN " . $table_prefix . "forum f ON f.thread_id = rel.thread_id) ";
		$sql .= " WHERE rel.thread_id IN (" . $db->tosql($thread_ids, INTEGERS_LIST) . ")";
		$sql .= $related_type_where;
		$sql .= " ORDER BY rel.thread_order";
		$db->query($sql);
		$topic_is_blocked = array();
		
		$t->set_var("forums_related_rows", "");
		$t->set_var("forums_related_column", (100 / $forums_related_columns) . "%");
		$forums_related_number = 0;
		while ($db->next_record())
		{
			$forums_related_number++;
			$thread_id    = $db->f("thread_id");
			$forum_id     = $db->f("forum_id");
			$topic_title  = get_translation($db->f("topic"));			
			$friendly_url = $db->f("friendly_url");
			$replies	  = $db->f("replies");		
	
			if ($friendly_urls && $friendly_url) {
				$t->set_var("forum_topic_url", $friendly_url . $friendly_extension);
				$forum_thread_page_url = $friendly_url . $friendly_extension . "?page=";
			} else {
				$t->set_var("forum_topic_url", "forum_topic.php?thread_id=" . $thread_id);
				$forum_thread_page_url = "forum_topic.php?thread_id=" . $thread_id . "&page=";
			}
							
			if ($allowed_topic_view && in_array($forum_id, $allowed_topic_view)) {
				$t->set_var("block_topic", "");
			} else {
				$t->sparse("block_topic", false);
			}
							
			if ($forums_related_user_info) {
				$thread_admin_id             = $db->f("admin_id_added_by");
				$thread_admin_id_modified_by = $db->f("admin_id_modified_by");
				$thread_admin_image          = $db->f("admin_image");
				$thread_user_id              = $db->f("user_id");
				$thread_user_name            = $db->f("user_name");
				$thread_user_email           = $db->f("user_email");
				$thread_user_image           = $db->f("user_image");
			}
				
			$topic_recs = 25;
			$topic_desc = "";
			$forum_message_page_url = $forum_thread_page_url;
			if ($forums_related_desc == 2) {
				$topic_desc = get_translation($db->f("description"));
			} elseif ($forums_related_desc == 3) {
				$sql  = " SELECT * FROM " . $table_prefix . "forum_messages ";
				$sql .= " WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
				$db2->query($sql);
				if ($db2->next_record()) {
					$topic_desc = get_translation($db2->f("message_text"));
					if ($forums_related_user_info) {
						$thread_admin_id             = $db->f("admin_id_added_by");
						$thread_admin_id_modified_by = $db->f("admin_id_modified_by");
						$thread_admin_image          = $db->f("admin_image");
						$thread_user_id              = $db->f("user_id");
						$thread_user_name            = $db->f("user_name");
						$thread_user_email           = $db->f("user_email");
						$thread_user_image           = $db->f("user_image");
					}
					$total_pages = ceil($replies / $topic_recs);
					$forum_message_page_url .= $total_pages;
				} else {
					$topic_desc = get_translation($db->f("description"));
				}
			}			
			$t->set_var("forum_message_page_url", $forum_message_page_url);			
			if ($topic_desc) {
				$topic_desc = process_message($topic_desc, $icons_enable);
				split_long_words($topic_desc);
				$t->set_var("topic_desc", $topic_desc);
				$t->sparse("topic_desc_block", false);
			} else {
				$t->set_var("topic_desc", $topic_desc);
				$t->set_var("topic_desc_block", "");
			}

			
			if ($forums_related_user_info) {
				
				$thread_user_name .= " (" . GUEST_MSG . ")";
				$thread_user_class = "forumGuest";
				$thread_personal_image = "";

				if ($thread_user_id) {
					$sql  = " SELECT login, nickname, email, personal_image FROM " . $table_prefix . "users ";
					$sql .= " WHERE user_id=" . $db->tosql($thread_user_id, INTEGER);
					$db2->query($sql);
					if ($db2->next_record()) {
						$thread_personal_image = $db2->f("personal_image");
						if ($db2->f("email")) { $thread_user_email = $db2->f("email"); }
						$thread_user_name = $db2->f("nickname");
						if (!strlen($thread_user_name)) { $thread_user_name = $db2->f("login"); }
						$thread_user_class = "forumUser";
					}
				} else if ($thread_admin_id) {
					$sql  = " SELECT admin_name, nickname, email, personal_image FROM " . $table_prefix . "admins ";
					$sql .= " WHERE admin_id=" . $db->tosql($thread_admin_id, INTEGER);
					$db2->query($sql);
					if ($db2->next_record()) {
						$thread_personal_image = $db2->f("personal_image");
						if ($db2->f("email")) { $thread_user_email = $db2->f("email"); }
						$thread_user_name = $db2->f("nickname");
						if (!strlen($thread_user_name)) { $thread_user_name = $db2->f("admin_name"); }
						$thread_user_class = "forumAdmin";	
					}
				}
				$t->set_var("thread_user_class", $thread_user_class);
				$t->set_var("thread_user_name", htmlspecialchars($thread_user_name));
				if (!$thread_personal_image && $forum_user_info == 1) {
					$thread_personal_image = $user_no_image;
				}
				if (strlen($thread_personal_image)) {
					if (preg_match("/^http\:\/\//", $thread_personal_image)) {
						$image_size = "";
					} else {
						$image_size = @GetImageSize($thread_personal_image);
					}
					if (is_array($image_size)) {
						$t->set_var("image_size", $image_size[3]);
					} else {
						$t->set_var("image_size", "");
					}
			
					$t->set_var("personal_image", $thread_personal_image);
					$t->set_var("forum_user_class", $thread_user_class);
			
					$t->sparse("user_image_block", false);
				} else {
					$t->set_var("user_image_block", "");
				}
				$t->parse("forums_related_user_info_block", false);
			}
				
			$t->set_var("thread_id",   $thread_id);
			$t->set_var("topic_title", $topic_title);
			
			$t->parse("forums_related_cols");
			if ($forums_related_number % $forums_related_columns == 0)
			{
				$t->parse("forums_related_rows");
				$t->set_var("forums_related_cols", "");
			}
		}

		if ($forums_related_number % $forums_related_columns != 0) {
			$t->parse("forums_related_rows");
		}

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}
}
?>