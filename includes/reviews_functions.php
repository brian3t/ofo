<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  reviews_functions.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
                                        

function update_product_rating($items_ids)
{
	global $db, $table_prefix;

	$ids = explode(",", $items_ids);
	for ($i = 0; $i < sizeof($ids); $i++) {
		$item_id = $ids[$i];

		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "reviews ";
		$sql .= " WHERE approved=1 AND rating <> 0 AND item_id=" . $db->tosql($item_id, INTEGER);
		$total_rating_votes = get_db_value($sql);
  
		$sql  = " SELECT SUM(rating) FROM " . $table_prefix . "reviews ";
		$sql .= " WHERE approved=1 AND rating <> 0 AND item_id=" . $db->tosql($item_id, INTEGER);
		$total_rating_sum = get_db_value($sql);
		if(!strlen($total_rating_sum)) $total_rating_sum = 0;
  
		$average_rating = $total_rating_votes ? $total_rating_sum / $total_rating_votes : 0;
  
		$sql  = " UPDATE " . $table_prefix . "items ";
		$sql .= " SET votes=" . $total_rating_votes . ", points=" . $total_rating_sum . ", ";
		$sql .= " rating=" . $db->tosql($average_rating, NUMBER);
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
	}
}

function update_article_rating($articles_ids)
{
	global $db, $table_prefix;

	$ids = explode(",", $articles_ids);
	for ($i = 0; $i < sizeof($ids); $i++) {
		$article_id = $ids[$i];

		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "articles_reviews ";
		$sql .= " WHERE approved=1 AND rating <> 0 AND article_id=" . $db->tosql($article_id, INTEGER);
		$total_rating_votes = get_db_value($sql);
  
		$sql  = " SELECT SUM(rating) FROM " . $table_prefix . "articles_reviews ";
		$sql .= " WHERE approved=1 AND rating <> 0 AND article_id=" . $db->tosql($article_id, INTEGER);
		$total_rating_sum = get_db_value($sql);
		if(!strlen($total_rating_sum)) $total_rating_sum = 0;
  
		$average_rating = $total_rating_votes ? $total_rating_sum / $total_rating_votes : 0;

		$sql  = " UPDATE " . $table_prefix . "articles ";
		$sql .= " SET total_votes=" . $total_rating_votes . ", total_points=" . $total_rating_sum . ", ";
		$sql .= " rating=" . $db->tosql($average_rating, NUMBER);
		$sql .= " WHERE article_id=" . $db->tosql($article_id, INTEGER);
		$db->query($sql);
	}
}

function check_add_review($id, $type)
{
	global $db, $table_prefix, $reviews_settings, $articles_reviews_settings;
		
	if ($type == "article") {
		$allowed_post = get_setting_value($articles_reviews_settings, "allowed_post", 0);
		$reviews_per_user = get_setting_value($articles_reviews_settings, "reviews_per_user", "");
		$reviews_interval = get_setting_value($articles_reviews_settings, "reviews_interval", "");
		$reviews_period = get_setting_value($articles_reviews_settings, "reviews_period", "");
	} else {
		$allowed_post = get_setting_value($reviews_settings, "allowed_post", 0);
		$reviews_per_user = get_setting_value($reviews_settings, "reviews_per_user", "");
		$reviews_interval = get_setting_value($reviews_settings, "reviews_interval", "");
		$reviews_period = get_setting_value($reviews_settings, "reviews_period", "");
	}

	$new_review = true;
	if (strlen($reviews_per_user)) {
		$ip_address = get_ip();
		$user_id = get_session("session_user_id");
	  
		if ($type == "article") {
			$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "articles_reviews ";
			$sql .= " WHERE article_id=" . $db->tosql($id, INTEGER);
		} else {
			$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "reviews ";
			$sql .= " WHERE item_id=" . $db->tosql($id, INTEGER);
		}
		if ($reviews_period && $reviews_interval) {
			// check time restrictions
			$cd = va_time();
			if ($reviews_period == 1) {
				$rd = mktime (0, 0, 0, $cd[MONTH], $cd[DAY] - $reviews_interval, $cd[YEAR]);
			} elseif ($reviews_period == 2) {
				$rd = mktime (0, 0, 0, $cd[MONTH], $cd[DAY] - ($reviews_interval * 7), $cd[YEAR]);
			} elseif ($reviews_period == 3) {
				$rd = mktime (0, 0, 0, $cd[MONTH] - $reviews_interval, $cd[DAY], $cd[YEAR]);
			} else {
				$rd = mktime (0, 0, 0, $cd[MONTH], $cd[DAY], $cd[YEAR] - $reviews_interval);
			}
			$sql .= " AND date_added>" . $db->tosql($rd, DATETIME);		
		}
		if ($allowed_post == 2) {
			$sql .= " AND user_id=" . $db->tosql($user_id, INTEGER);		
		} else {
			$sql .= " AND remote_address=" . $db->tosql($ip_address, TEXT);		
		}
		$posted_reviews = get_db_value($sql);
		if ($posted_reviews >= $reviews_per_user) {
			$new_review = false;
		}
	}
	return $new_review;
}

function check_add_product_review($item_id)
{
	return check_add_review($item_id, "product");
}

function check_add_article_review($article_id)
{
	return check_add_review($article_id, "article");
}

?>