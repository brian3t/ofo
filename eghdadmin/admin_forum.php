<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_forum.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once("./admin_common.php");

	include_once($root_folder_path."messages/".$language_code."/forum_messages.php");

	$permissions = get_permissions();
	$products_categories = get_setting_value($permissions, "products_categories", 0);
	$product_related = get_setting_value($permissions, "product_related", 0);
	$related_articles = get_setting_value($permissions, "articles", 0);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_forum.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_forum_href", "admin_forum.php");
	$t->set_var("admin_forum_thread_href", "admin_forum_thread.php");
	$t->set_var("admin_forum_topic_href", "admin_forum_topic.php");
	$t->set_var("admin_forum_settings_href", "admin_forum_settings.php");
	$t->set_var("admin_forum_edit_href", "admin_forum_edit.php");
	$t->set_var("admin_forum_category_href", "admin_forum_category.php");
	$t->set_var("admin_forum_items_related_href", "admin_forum_items_related.php");
	$t->set_var("admin_forum_articles_related_href", "admin_forum_articles_related.php");
	
	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_forum.php");
	$s->set_parameters(false, true, true, false);
	$s->set_sorter(NO_MSG, "sorter_id", "1", "thread_id");
	$s->set_sorter(TOPIC_NAME_COLUMN, "sorter_topic", "2", "topic");
	$s->set_sorter(OWNER_MSG, "sorter_owner", "3", "user_name");
	$s->set_sorter(TOPIC_REPLIES_COLUMN, "sorter_replies", "4", "replies");
	$s->set_sorter(UPDATED_MSG, "sorter_updated", "5", "thread_updated");
	if (!$s->order_by) {
		$s->order_by = " ORDER BY fp.priority_rank, f.thread_updated DESC ";
	}

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_forum.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$forum_id = get_param("forum_id");
	$category_id = get_param("category_id");	

	// Show forum categories in the left
	$sql  = " SELECT fc.category_id, fc.category_name, fl.forum_id, fl.forum_name ";
	$sql .= " FROM (" . $table_prefix . "forum_categories fc ";
	$sql .= " LEFT JOIN " . $table_prefix . "forum_list fl ON fc.category_id=fl.category_id) ";
	if ($category_id) {
		$sql .= " WHERE fc.category_id = " . $db->tosql($category_id, INTEGER);
	}
	$sql .= " ORDER BY fc.category_order, fc.category_id, fl.forum_order ";
	$db->query($sql);
	if ($db->next_record()) {
		$last_category_id = "";
		$current_category = "";
		do {
			$list_forum_id = $db->f("forum_id");
			$list_category_id = $db->f("category_id");
			$list_category_name = get_translation($db->f("category_name"));
			if ($last_category_id != $list_category_id) {
				$last_category_id = $list_category_id;
				$t->set_var("list_category_id", $db->f("category_id"));
				$t->set_var("list_category_name", htmlspecialchars($list_category_name));
				$t->parse("list_category", false);
				if ($category_id == $list_category_id) {
					$current_category_id = $list_category_id;
					$current_category = $list_category_name;
				}
			} else {
				$t->set_var("list_category", "");
			}
			if ($list_forum_id) {
				if (!$forum_id){ // if forum wasn't selected get the top one
				  $forum_id = $list_forum_id;
				}
				$list_forum_name = get_translation($db->f("forum_name"));
				$list_forum_url = "admin_forum.php";
				if ($category_id) {
					$list_forum_url .= "?category_id=" . $category_id . "&forum_id=" . $list_forum_id;
				} else {
					$list_forum_url .= "?forum_id=" . $list_forum_id;
				}
				$t->set_var("list_forum_id", $list_forum_id);
				$t->set_var("list_forum_url", $list_forum_url);
				$t->set_var("list_forum_name", htmlspecialchars($list_forum_name));
				$t->parse("list_forum", false);
				if ($forum_id == $list_forum_id){
					$current_forum = $list_forum_name;
					$current_category_id = $list_category_id;
					$current_category = $list_category_name;
				}
			} else {
				$t->set_var("list_forum", "");
			}

			$t->parse("list_block", true);

		} while ($db->next_record());
		$t->parse("new_forum_link", false);
		$t->set_var("block_no_categories", "");
	} else {
		$t->set_var("new_forum_link", "");
		$t->set_var("block_threads", "");
		$t->set_var("message_list", NO_CATEGORIES_MSG);
		$t->parse("block_message", false);
	}

	// Set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "forum WHERE forum_id = " . $db->tosql($forum_id, INTEGER));
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	if (isset($current_category_id) && $current_category_id) {
		$t->set_var("category_id", $current_category_id);
		$t->set_var("current_category", $current_category);
		$t->parse("current_category_block", false);
	}

	if ($product_related && $products_categories && $related_articles) {
		$t->set_var("delimiter", "| ");
	}
	// Show threads for selected forum
	if ($forum_id) {
		$sql  = " SELECT f.thread_id, f.topic, f.user_name, f.user_email, f.replies, f.thread_updated, ";
		$sql .= " fp.html_before_title, fp.html_after_title ";
		$sql .= " FROM (" . $table_prefix . "forum f ";
		$sql .= " LEFT JOIN " . $table_prefix . "forum_priorities fp ON f.priority_id=fp.priority_id) ";
		$sql .= " WHERE forum_id = " . $db->tosql($forum_id, INTEGER);
		$sql .= $s->order_by;
		$db->RecordsPerPage = $records_per_page;
		$db->PageNumber = $page_number;
		$db->query($sql);
		if ($db->next_record()) {
			do {
				$topic = get_translation($db->f("topic"));
				$html_before_title = get_translation($db->f("html_before_title"));
				$html_after_title = get_translation($db->f("html_after_title"));
				$t->set_var("thread_id", $db->f("thread_id"));
				$t->set_var("topic", htmlspecialchars($topic));
				$t->set_var("html_before_title", $html_before_title);
				$t->set_var("html_after_title", $html_after_title);
				$t->set_var("user_name", htmlspecialchars($db->f("user_name")));
				$t->set_var("user_email", htmlspecialchars($db->f("user_email")));
				$t->set_var("replies", htmlspecialchars($db->f("replies")));
				$t->set_var("thread_updated", va_date($datetime_show_format, $db->f("thread_updated", DATETIME)));
				if ($product_related && $products_categories) {
					$t->parse("product_related_priv", false);
				} else {
					$t->set_var("product_related_priv", "");
				}				
				if ($related_articles) {
					$t->parse("related_articles_priv", false);
				} else {
					$t->set_var("related_articles_priv", "");
				}
				$t->parse("records", true);
			} while ($db->next_record());
			$t->parse("sorters", false);
		} else {
			$t->set_var("sorters", "");
			$t->set_var("message_list", NO_TOPICS_MSG);
			$t->parse("block_message", false);
		}
		$t->set_var("title_block", $current_forum);
		$t->set_var("current_forum", $current_forum);
		$t->parse("current_forum_block", false);

		$admin_forum_topic_url = "admin_forum_topic.php?forum_id=" . urlencode($forum_id) . "&rp=" . urlencode("admin_forum.php?forum_id=" . $forum_id);
		$t->set_var("admin_forum_topic_url", $admin_forum_topic_url);
		$t->parse("forum_links", false);
	} else {
		$t->set_var("current_forum_block", "");
		$t->set_var("block_threads", "");
		if ($category_id) {
			$t->set_var("message_list", NO_FORUMS_SELECTED_MSG);
		} else {
			$t->set_var("message_list", NO_FORUMS_CATEGORY_MSG);
		}
		$t->parse("block_message", false);
		$t->set_var("title_block", NO_ACTIVE_FORUM_MSG);
		$t->set_var("current_forum_block", "");
		$t->set_var("admin_forum_topic_url", "");
	}

	$t->set_var("title_left_block", FORUMS_MSG);
	$t->pparse("main");

?>