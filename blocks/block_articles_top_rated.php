<?php
include_once("./includes/articles_functions.php");

function articles_top_rated($block_name, $top_id, $top_name)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings;

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

	$t->set_file("block_body", "block_top_rated.html");
	$t->set_var("top_category_name",$top_name);
	$t->set_var("TOP_RATED_TITLE",  TOP_RATED_TITLE);
	$t->set_var("top_rated_items",  "");
	$t->set_var("top_image", "");
	$t->set_var("top_desc", "");

	$db->RecordsPerPage = 10;
	$db->PageNumber = 1;
	
	$params = array();
	$params["where"]  = " (c.category_id = " . $db->tosql($top_id, INTEGER);
	$params["where"] .= " OR c.category_path LIKE '0," . $top_id . ",%') ";
	$params["where"] .= " AND a.total_votes>=" . $db->tosql(get_setting_value($settings, "min_votes", 10), INTEGER);
	$params["where"] .= " AND a.rating>=" . $db->tosql(get_setting_value($settings, "min_rating", 1), FLOAT);
	if ($db->DBType == "mysql") {
		$params["order"] = " GROUP BY a.article_id ORDER BY a.rating DESC, a.article_order, a.article_title ";	
	} else {
		$params["order"] = " GROUP BY a.article_id,a.rating,a.article_order,a.article_title ORDER BY a.rating DESC, a.article_order, a.article_title ";	
	}
	$articles_ids = VA_Articles::find_all_ids($params, VIEW_CATEGORIES_ITEMS_PERM);
	if (!$articles_ids) return false;
	
	$allowed_articles_ids = VA_Articles::find_all_ids("a.article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);
		
	$sql  = " SELECT article_id, article_title, friendly_url, rating, is_remote_rss, details_remote_url ";
	$sql .= " FROM " . $table_prefix . "articles";
	$sql .= " WHERE article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")";
	$sql .= " ORDER BY rating DESC, article_order, article_title ";
	$db->query($sql);
	if($db->next_record())
	{
		$item_number = 0;
		do
		{
			$item_number++;
			$article_id = $db->f("article_id");
			$friendly_url = $db->f("friendly_url");
			$is_remote_rss = $db->f("is_remote_rss");
			$details_remote_url = $db->f("details_remote_url");

			$top_rating = $db->f("rating");

			if ($is_remote_rss == 0){
				if ($friendly_urls && $friendly_url) {
					$t->set_var("details_href", $friendly_url . $friendly_extension);
				} else {
					$t->set_var("details_href", "article.php?article_id=" . $article_id);
				}
			} else {
				$t->set_var("details_href", $details_remote_url);
			}
			
			if (!$allowed_articles_ids || !in_array($article_id, $allowed_articles_ids)) {
				$t->set_var("restricted_class", " restrictedItem");
				$t->sparse("restricted_image", false);
			} else {
				$t->set_var("restricted_class", "");
				$t->set_var("restricted_image", "");
			}
			
			$t->set_var("top_position", $item_number);
			$t->set_var("top_name", get_translation($db->f("article_title")));
			$t->set_var("top_rating", number_format($top_rating, 2));

			$t->parse("top_rated_items", true);
		} while ($db->next_record());              	
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>