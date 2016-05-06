<?php
include_once($root_folder_path . "includes/forums_functions.php");
	
function forum_list($block_name) 
{
	global $t, $db, $table_prefix, $settings;
	global $datetime_show_format;
	
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$t->set_file("block_body", "block_forum_list.html");

	$t->set_var("forum_categories_href", "forums.php");
	$t->set_var("register_href",  get_custom_friendly_url("user_profile.php"));
	$t->set_var("home_page_href", get_custom_friendly_url("user_home.php"));
	$t->set_var("my_topics_href", get_custom_friendly_url("forum.php") . "?u=1");

	$user_id = get_session("session_user_id");	
	$user_info = get_session("session_user_info");
	$user_type_id = get_setting_value($user_info, "user_type_id", "");	
	$category_id = get_param("category_id");	
	
	$where = "";
	if ($category_id) { 		
		$where = " c.category_id=" . $db->tosql($category_id, INTEGER);
	}
	$forums_ids = VA_Forums::find_all_ids($where, VIEW_FORUM_PERM);	
	
	if ($forums_ids) {
		$where = " fl.forum_id IN (" . $db->tosql($forums_ids, INTEGERS_LIST) . ") ";
		$allowed_topics_view = VA_Forums::find_all_ids($where, VIEW_TOPICS_PERM);
		$allowed_topic_view  = VA_Forums::find_all_ids($where, VIEW_TOPIC_PERM);
		
		$sql  = " SELECT fc.category_id, fc.category_name, fc.friendly_url AS category_friendly_url, ";
		$sql .= " fl.forum_id, fl.forum_name, fl.friendly_url AS forum_friendly_url, ";
		$sql .= " fl.small_image, fl.short_description, fl.threads_number, fl.messages_number, ";
		$sql .= " fl.last_post_added, fl.last_post_user_id, fl.last_post_admin_id, fl.last_post_thread_id, fl.last_post_message_id ";
		$sql .= " FROM (" . $table_prefix . "forum_list fl ";
		$sql .= " INNER JOIN " . $table_prefix . "forum_categories fc ON fc.category_id=fl.category_id)";
		$sql .= " WHERE fl.forum_id IN (" . $db->tosql($forums_ids, INTEGERS_LIST) . ") ";
		$sql .= " ORDER BY fc.category_order, fc.category_id, fl.forum_order ";
	
		$db->query($sql);
		$forums = array(); $f = 0;
		while ($db->next_record()) {
			$forums[$f]["category_id"] = $db->f("category_id");
			$forums[$f]["category_name"] = get_translation($db->f("category_name"));
			$forums[$f]["category_friendly_url"] = $db->f("category_friendly_url");
			$forums[$f]["forum_id"] = $db->f("forum_id");
			$forums[$f]["forum_name"] = get_translation($db->f("forum_name"));
			$forums[$f]["forum_friendly_url"] = $db->f("forum_friendly_url");
			$forums[$f]["small_image"] = $db->f("small_image");
			$forums[$f]["short_description"] = $db->f("short_description");
			$forums[$f]["threads_number"] = $db->f("threads_number");
			$forums[$f]["messages_number"] = $db->f("messages_number");
			$forums[$f]["last_post_added"] = $db->f("last_post_added", DATETIME);
			$forums[$f]["last_post_user_id"] = $db->f("last_post_user_id");
			$forums[$f]["last_post_admin_id"] = $db->f("last_post_admin_id");
			$forums[$f]["last_post_thread_id"] = $db->f("last_post_thread_id");
			$forums[$f]["last_post_message_id"] = $db->f("last_post_message_id");
			$f++;
		}
	
		$last_category_id   = $forums[0]["category_id"];
		$last_category_name = $forums[0]["category_name"];
		$last_friendly_url  = $forums[0]["category_friendly_url"];
		for ($i = 0; $i < $f; $i++) {
			$current_category_id = $forums[$i]["category_id"];
			$current_category_name = $forums[$i]["category_name"];
			$category_friendly_url = $forums[$i]["category_friendly_url"];
			$current_forum_id = $forums[$i]["forum_id"];
			$forum_name = $forums[$i]["forum_name"];
			$forum_friendly_url = $forums[$i]["forum_friendly_url"];
			$small_image = $forums[$i]["small_image"];
			$short_description = $forums[$i]["short_description"];
			$threads_number = $forums[$i]["threads_number"];
			$messages_number = $forums[$i]["messages_number"];
			$last_post_added = $forums[$i]["last_post_added"];
			$last_post_user_id = $forums[$i]["last_post_user_id"];
			$last_post_admin_id = $forums[$i]["last_post_admin_id"];
			$last_post_thread_id = $forums[$i]["last_post_thread_id"];
			$last_post_message_id = $forums[$i]["last_post_message_id"];
					
						
			if ($last_category_id != $current_category_id) {
				if ($friendly_urls && $last_friendly_url) {
					$t->set_var("category_href", $last_friendly_url . $friendly_extension);
				} else {
					$t->set_var("category_href", get_custom_friendly_url("forums.php") . "?category_id=" . $last_category_id);
				}
				$t->set_var("cat_name", $last_category_name);
				$t->parse("categories", true);
				$t->set_var("forums", "");
			}
			$t->set_var("forum_name", $forum_name);
			if (strlen($small_image)) {
				if (preg_match("/^http\:\/\//", $small_image)) {
					$image_size = "";
				} else {
					$image_size = @GetImageSize($small_image);
				}
				if (is_array($image_size)) {
					$t->set_var("image_size", $image_size[3]);
				} else {
					$t->set_var("image_size", "");
				}

				$t->set_var("small_image", $small_image);
				$t->parse("small_image_block", false);
			} else {
				$t->set_var("small_image_block", "");
			}
			
			$t->set_var("threads_number", $threads_number);
			$t->set_var("messages_number", $messages_number);
			$t->set_var("short_description", $short_description);
			
			if ($friendly_urls && $forum_friendly_url) {
				$t->set_var("forum_href", $forum_friendly_url . $friendly_extension);
			} else {
				$t->set_var("forum_href", get_custom_friendly_url("forum.php") . "?forum_id=" . $current_forum_id);
			}
			
			// hide last post in blocked forum			
			if ($allowed_topics_view && in_array($current_forum_id, $allowed_topics_view)) {
				$t->set_var("block_forum", "");
				
				// show status of last topic
				if ($allowed_topic_view && in_array($current_forum_id, $allowed_topic_view)) {
					$t->set_var("block_topic", "");
				} else {
					$t->sparse("block_topic", false);
				}
				
				$t->set_var("last_post", va_date($datetime_show_format, $last_post_added));
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
				$t->set_var("last_post_in_block", "");
				if ($last_post_thread_id) {
					$sql  = " SELECT friendly_url, topic FROM " . $table_prefix . "forum ";
					$sql .= " WHERE thread_id=" . $db->tosql($last_post_thread_id, INTEGER);
					$db->query($sql);
					if ($db->next_record()) {
						$friendly_url = $db->f("friendly_url");
						$last_post_in = get_translation($db->f("topic"));
						if (strlen($last_post_in) > 20) {
							$last_post_in = substr($last_post_in, 0, 18) . "...";
						}
						if ($friendly_urls && $friendly_url) {
							$t->set_var("forum_thread_url", $friendly_url . $friendly_extension);
						} else {
							$t->set_var("forum_thread_url", get_custom_friendly_url("forum_topic.php") . "?thread_id=" . $last_post_thread_id);
						}
						$t->set_var("last_post_in", htmlspecialchars($last_post_in));
						$t->parse("last_post_in_block", false);
					}
				}				
			} else {
				$t->sparse("block_forum", false);
				$t->set_var("last_post", "");
				$t->set_var("last_post_in_block", "");
				$t->set_var("last_post_by_block", "");
			}

			// check moderators
			$moderators = "";
			$sql  = " SELECT a.admin_name FROM ( " . $table_prefix . "forum_moderators fm ";
			$sql .= " LEFT JOIN " . $table_prefix . "admins a ON a.admin_id=fm.admin_id) ";
			$sql .= " WHERE fm.forum_id=" . $db->tosql($current_forum_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				if ($moderators) { $moderators .= ", "; }
				$moderators .= $db->f("admin_name");
			}	
			if ($moderators) {
				$t->set_var("moderators", $moderators);
				$t->parse("moderators_block", false);
			} else {
				$t->set_var("moderators_block", "");
			}

			$t->parse("forums", true);
  
			$last_category_id   = $current_category_id;
			$last_category_name = $current_category_name;
			$last_friendly_url  = $category_friendly_url;
		}

		if ($friendly_urls && $last_friendly_url) {
			$t->set_var("category_href", $last_friendly_url . $friendly_extension);
		} else {
			$t->set_var("category_href", get_custom_friendly_url("forums.php") . "?category_id=" . $last_category_id);
		}
		$t->set_var("cat_name", $last_category_name);
		$t->parse("categories", true);
		$t->set_var("no_forums", "");
		$t->set_var("forums", "");

	} else {
		$t->parse("no_forums", false);
		$t->set_var("categories", "");
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>