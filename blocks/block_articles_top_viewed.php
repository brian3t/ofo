<?php
include_once("./includes/articles_functions.php");

function articles_top_viewed($block_name, $top_id, $top_name)
{
	global $t, $db, $db_type, $table_prefix;
	global $settings, $page_settings;
	global $datetime_show_format;
	
	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}
		
	if (!strlen($top_name) && !VA_Articles_Categories::check_permissions($top_id, VIEW_CATEGORIES_ITEMS_PERM)) {
		$sql  = " SELECT category_name ";
		$sql .= " FROM " . $table_prefix . "articles_categories ";				
		$sql .= " WHERE category_id=" . $db->tosql($top_id, INTEGER);			
						
		$db->query($sql);
		if ($db->next_record()) {
			$top_name                 = get_translation($db->f("category_name"));
		} else {
			return false;
		}
	}
	
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$t->set_file("block_body", "block_articles_top_viewed.html");
	$t->set_var("top_viewed_rows", "");
	$t->set_var("top_viewed_rows", "");
	$t->set_var("top_category_name",$top_name);
	

	$records_per_page = get_setting_value($page_settings, "a_top_viewed_recs_" . $top_id, 10);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = 1;
	
	$params = array();
	$params["where"]  = " (c.category_id = " . $db->tosql($top_id, INTEGER);
	$params["where"] .= " OR c.category_path LIKE '0," . $top_id . ",%') ";
	$params["order"] = " GROUP BY a.article_id ORDER BY a.total_views DESC, a.article_order, a.article_title ";	
	$articles_ids = VA_Articles::find_all_ids($params, VIEW_CATEGORIES_ITEMS_PERM);
	if (!$articles_ids) return false;
	
	$allowed_articles_ids = VA_Articles::find_all_ids("a.article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);
	
	$sql  = " SELECT article_id, article_title, total_views, friendly_url, article_date, short_description, is_remote_rss, details_remote_url ";
	$sql .= " FROM " . $table_prefix . "articles  ";
	$sql .= " WHERE article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")"; 
	$sql .= " ORDER BY total_views DESC, article_order, article_title";
	
	$db->query($sql);
	if($db->next_record())
	{
		$top_columns = get_setting_value($page_settings, "a_top_viewed_cols_" . $top_id, 1);
		$t->set_var("top_viewed_column", (100 / $top_columns) . "%");
		$top_number = 0;
		do
		{
			$top_number++;
			$article_id = $db->f("article_id");
			$article_title = get_translation($db->f("article_title"));
			$friendly_url = $db->f("friendly_url");
			$is_remote_rss = $db->f("is_remote_rss");
			$details_remote_url = $db->f("details_remote_url");
			$total_views = $db->f("total_views");
			$short_description = get_translation($db->f("short_description"));

			if ($is_remote_rss == 0){
				if ($friendly_urls && $friendly_url) {
					$t->set_var("article_url", $friendly_url . $friendly_extension);
				} else {
					$t->set_var("article_url", "article.php?article_id=" . $article_id);
				}
			} else {
				$t->set_var("article_url", $details_remote_url);
			}
			
			if (!$allowed_articles_ids || !in_array($article_id, $allowed_articles_ids)) {
				$t->set_var("restricted_class", " restrictedItem");
				$t->sparse("restricted_image", false);
			} else {
				$t->set_var("restricted_class", "");
				$t->set_var("restricted_image", "");
			}

			$t->set_var("top_position", $top_number);
			$t->set_var("article_id", $article_id);
			$t->set_var("article_title", $article_title);
			$t->set_var("total_views", $total_views);
			$t->set_var("short_description", $short_description);

			$article_date = $db->f("article_date", DATETIME);
			$article_date_string  = va_date($datetime_show_format, $article_date);
			$t->set_var("article_date", $article_date_string);

			$t->parse("top_viewed_cols");
			if($top_number % $top_columns == 0)
			{
				$t->parse("top_viewed_rows");
				$t->set_var("top_viewed_cols", "");
			}
			
		} while ($db->next_record());              	

		if ($top_number % $top_columns != 0) {
			$t->parse("top_viewed_rows");
		}

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>