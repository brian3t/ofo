<?php
include_once($root_folder_path . "includes/forums_functions.php");

function forum_latest($block_name)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings;
	global $datetime_show_format;
	
	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}
	
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$records_per_page = get_setting_value($page_settings, "forum_latest_recs", 10);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = 1;
	
	$forums_ids = VA_Forums::find_all_ids(
		array(
			"where"    => " fl.threads_number > 0",
			"brackets" => " (",
			"join"     => " LEFT JOIN " . $table_prefix . "forum f ON f.forum_id=fl.forum_id)",
			"order"    => " ORDER BY f.thread_updated DESC "
		),
		 VIEW_TOPICS_PERM
	);	
	if (!$forums_ids) return;
	$allowed_topic_view = VA_Forums::find_all_ids(" fl.forum_id IN (" . $db->tosql($forums_ids, INTEGERS_LIST) . ") ", VIEW_TOPIC_PERM);
	
	$t->set_file("block_body", "block_forum_latest.html");
	$t->set_var("latest_rows", "");
	$t->set_var("latest_cols", "");

	$sql  = " SELECT forum_id, thread_id, topic, replies, friendly_url, thread_updated ";
	$sql .= " FROM " . $table_prefix . "forum ";
	$sql .= " WHERE forum_id IN (" . $db->tosql($forums_ids, INTEGERS_LIST) . ") ";	
	$sql .= " ORDER BY thread_updated DESC ";
	
	$db->query($sql);
	if($db->next_record())
	{		
		$latest_columns = get_setting_value($page_settings, "forum_latest_cols", 1);
		$t->set_var("latest_column", (100 / $latest_columns) . "%");
		$latest_number = 0;
		do
		{
			$latest_number++;
			$forum_id = $db->f("forum_id");
			$thread_id = $db->f("thread_id");
			$topic_title = get_translation($db->f("topic"));
			$friendly_url = $db->f("friendly_url");
			$replies = $db->f("replies");
			
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
		
			$t->set_var("thread_id", $thread_id);
			$t->set_var("topic_title", $topic_title);

			$latest_updated = $db->f("thread_updated", DATETIME);
			$t->set_var("latest_updated", va_date($datetime_show_format, $latest_updated));

			// check if need to show pages number 
			$topic_recs = 25;
			if ($replies > $topic_recs) {
				$t->set_var("latest_topic_pages", "");
				$total_pages = ceil($replies / $topic_recs);
				if ($total_pages > 5) {
					$start_page = $total_pages - 2;
					$t->set_var("forum_topic_page_url", $forum_thread_page_url . "2");
					$t->set_var("page_number", "&nbsp;...");
					$t->sparse("latest_topic_pages", true);
				} else {
					$start_page = 2;
				}
				for ($p = $start_page; $p <= $total_pages; $p++) {
					$t->set_var("forum_topic_page_url", $forum_thread_page_url . $p);
					$t->set_var("page_number", "&nbsp;".$p);
					$t->sparse("latest_topic_pages", true);
				}
				$t->sparse("latest_topic_pages_block", false);
			} else {
				$t->set_var("latest_topic_pages_block", "");
			}

			$t->parse("latest_cols");
			if($latest_number % $latest_columns == 0)
			{
				$t->parse("latest_rows");
				$t->set_var("latest_cols", "");
			}
			
		} while ($db->next_record());              	

		if ($latest_number % $latest_columns != 0) {
			$t->parse("latest_rows");
		}

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>