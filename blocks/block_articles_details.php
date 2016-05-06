<?php
include_once("./includes/articles_functions.php");

function articles_details($block_name, $article_id, $category_id, $details_fields, $details_template)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings, $restrict_articles_images;
	global $datetime_show_format, $currency;
	global $html_title, $meta_keywords, $meta_description;
	global $sc_item_id, $item_added, $sc_errors;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	if (!strlen($details_template)) {
		$details_template = "block_articles_details.html";
	}
	$t->set_file("block_body", $details_template);

	$tell_friend_href = get_custom_friendly_url("tell_friend.php") . "?item_id=" . urlencode($article_id) . "&type=articles";

	$t->set_var("tell_friend_href", $tell_friend_href);
	$t->set_var("articles_print_href", get_custom_friendly_url("article_print.php"));
	$t->set_var("article_id", htmlspecialchars($article_id));
	$t->set_var("item_id", htmlspecialchars($article_id));

	$rp = get_custom_friendly_url("article.php") . "?category_id=" . urlencode($category_id) . "&article_id=" . urlencode($article_id);
	$reviews_href = get_custom_friendly_url("articles_reviews.php") . "?category_id=" . urlencode($category_id) . "&article_id=" . urlencode($article_id);

	$t->set_var("rp_url", urlencode($rp));
	$t->set_var("rp", htmlspecialchars($rp));
	$t->set_var("reviews_href", $reviews_href);

	$details_fields = ",," . $details_fields . ",,";
	$article_fields = array(
		"author_name", "author_email", "author_url", "link_url", "download_url",
		"short_description", "full_description", "keywords", "notes"
	);
	
	if (!VA_Articles::check_exists($article_id)) {
		$t->set_var("item", "");
		$t->set_var("NO_ARTICLE_MSG", NO_ARTICLE_MSG);
		$t->parse("no_item", false);		
		$t->parse("block_body", false);
		$t->parse($block_name, true);
		return;
	}
	
	if (!VA_Articles::check_permissions($article_id, false, VIEW_ITEMS_PERM)) {
		header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
		exit;
	}
	
	// retrieve info for article 
	$sql  = " SELECT article_id, friendly_url, article_title, article_date, date_end, ";
	$sql .= " author_name, author_email, author_url, link_url, download_url, ";
	$sql .= " short_description, is_html, full_description, ";
	$sql .= " image_small,  image_small_alt, image_large, image_large_alt, stream_video, stream_video_width, stream_video_height, stream_video_preview, ";
	$sql .= " meta_title, meta_keywords, meta_description, ";
	$sql .= " total_views, total_votes, total_points, allowed_rate, ";
	$sql .= " keywords, notes, is_remote_rss, details_remote_url ";
	$sql .= " FROM " . $table_prefix . "articles a ";
	$sql .= " WHERE article_id= " . $db->tosql($article_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {

		$article_id    = $db->f("article_id");
		$article_title = get_currency_message(get_translation($db->f("article_title")), $currency);
		$short_description = get_currency_message(get_translation($db->f("short_description")), $currency);
		$full_description  = get_currency_message(get_translation($db->f("full_description")), $currency);
		if (!$full_description) { $full_description = $short_description; }
		$allowed_rate      = $db->f("allowed_rate");
		$total_views       = $db->f("total_views");		
		
		// meta files
		$html_title = get_translation($db->f("meta_title"));
		$meta_keywords = get_translation($db->f("meta_keywords"));
		$meta_description = get_translation($db->f("meta_description"));

		if (!strlen($html_title)) { 
			$html_title = $article_title; 
		}
		if (!strlen($meta_description)) {
			if (strlen($short_description)) {
				$meta_description = $short_description;
			} elseif (strlen($full_description)) {
				$meta_description = $full_description;
			} else {
				$meta_description = $article_title;
			}
		}		

		$t->set_var("article_id", $article_id);
		$t->set_var("article_name", $article_title);
		$t->set_var("article_title", $article_title);

		// get fields values
		$article_date_string = ""; $date_end_string = "";
		if (strpos($details_fields, ",article_date,")) {
			$article_date = $db->f("article_date", DATETIME);
			$article_date_string  = va_date($datetime_show_format, $article_date);
			$t->set_var("article_date", $article_date_string);
			$t->global_parse("article_date_block", false, false, true);
		} else {
			$t->set_var("article_date_block", "");
		}
		if (strpos($details_fields, ",date_end,")) {
			$date_end = $db->f("date_end", DATETIME);
			$date_end_string = va_date($datetime_show_format, $date_end);
			$t->set_var("date_end", $date_end_string);
			$t->global_parse("date_end_block", false, false, true);
		} else {
			$t->set_var("date_end_block", "");
		}
		if (strlen($article_date_string) || strlen($date_end_string)) {
			$t->global_parse("date_block", false, false, true);
		}

		for ($i = 0; $i < sizeof($article_fields); $i++) {
			$field_name = $article_fields[$i];
			$fields[$field_name] = get_currency_message(get_translation($db->f($field_name)), $currency);
			if (strlen($fields[$field_name]) && strpos($details_fields, "," . $field_name . ",")) {
				$t->set_var($field_name, $fields[$field_name]);
				$t->global_parse($field_name . "_block", false, false, true);
			} else {
				$fields[$field_name] = "";
				$t->set_var($field_name, "");
				$t->set_var($field_name . "_block", "");
			}
		}

		if (strlen($fields["author_name"]) || strlen($fields["author_email"]) || strlen($fields["author_url"])) {
			$t->global_parse("author_block", false, false, true);
		} else {
			$t->set_var("author_block", false);
		}

		if (strpos($details_fields, ",full_description,")) {
			if ($db->f("is_html") != 1) {
				$full_description = nl2br(htmlspecialchars($full_description));
			}
			$t->set_var("full_description", $full_description);
		} else {
			$t->set_var("full_description", "");
		}

		$image_small     = $db->f("image_small");
		$image_small_alt = $db->f("image_small_alt");
		if (strpos($details_fields, ",image_small,") && strlen($image_small)) {
			if (preg_match("/^http\:\/\//", $image_small)) {
				$image_size = "";
			} else {
				$image_size = @getimagesize($image_small);
				if (isset($restrict_articles_images) && $restrict_articles_images) {
					$image_small = "image_show.php?article_id=".$article_id."&type=small";
				}
			}
			if (!strlen($image_small_alt)) { $image_small_alt = $article_title; }
			$t->set_var("alt", htmlspecialchars($image_small_alt));
			$t->set_var("src", htmlspecialchars($image_small));
			if (is_array($image_size)) {
				$t->set_var("width", "width=\"" . $image_size[0] . "\"");
				$t->set_var("height", "height=\"" . $image_size[1] . "\"");
			} else {
				$t->set_var("width", "");
				$t->set_var("height", "");
			}
			$t->parse("image_small_block", false);
		} else {
			$t->set_var("image_small_block", "");
		}

		$image_large = $db->f("image_large");
		$image_large_alt = $db->f("image_large_alt");
		if (strpos($details_fields, ",image_large,") && strlen($image_large)) {
			if (preg_match("/^http\:\/\//", $image_large)) {
				$image_size = "";
			} else {
				$image_size = @getimagesize($image_large);
				if (isset($restrict_articles_images) && $restrict_articles_images) { $image_large = "image_show.php?article_id=".$article_id."&type=large"; }
			}
			if (!strlen($image_large_alt)) { $image_large_alt = $article_title; }
			$t->set_var("alt", htmlspecialchars($image_large_alt));
			$t->set_var("src", htmlspecialchars($image_large));
			if (is_array($image_size)) {
				$t->set_var("width", "width=\"" . $image_size[0] . "\"");
				$t->set_var("height", "height=\"" . $image_size[1] . "\"");
			} else {
				$t->set_var("width", "");
				$t->set_var("height", "");
			}
			$t->parse("image_large_block", false);
		} else {
			$t->set_var("image_large_block", "");
		}

		$stream_video         = $db->f("stream_video");
		$stream_video_width   = $db->f("stream_video_width");
		$stream_video_height  = $db->f("stream_video_height");
		$stream_video_preview = $db->f("stream_video_preview");
		if (strlen($stream_video) && strpos($details_fields, "stream_video")){
			$path_parts = pathinfo($stream_video);
			$ext = strtolower($path_parts['extension']);
			if ($ext == "flv") {
				if (!strlen($stream_video_width) && !strlen($stream_video_height)){
					$stream_video_width = '';
					$stream_video_height = '';
				}
				$t->set_var("stream_video_width", htmlspecialchars($stream_video_width));
				$t->set_var("stream_video_height", htmlspecialchars($stream_video_height));
				$t->set_var("stream_video_preview", htmlspecialchars($stream_video_preview));
				$t->set_var("stream_video", htmlspecialchars($stream_video));

				$t->global_parse("flash_player_block", false, false, true);
			} else {
				if (!strlen($stream_video_width) && !strlen($stream_video_height)){
					$stream_video_width = 230;
					$stream_video_height = 140;
				}
				if ($stream_video_width < 230){
					$stream_video_height = $stream_video_height * 230 / $stream_video_width;
					$stream_video_width = 230;
				}
				$stream_video_height += 70;
				$t->set_var("stream_video_width", htmlspecialchars($stream_video_width));
				$t->set_var("stream_video_height", htmlspecialchars($stream_video_height));
				$t->set_var("stream_video", htmlspecialchars($stream_video));

				$t->global_parse("windows_media_block", false, false, true);
			}
		} else {
			$t->set_var("flash_player_block", "");
			$t->set_var("windows_media_block", "");
		}

		// update total views for article
		$articles_viewed = get_session("session_articles_viewed");
		if (!isset($articles_viewed[$article_id])) {
			$sql  = " UPDATE " . $table_prefix . "articles SET total_views=" . $db->tosql(($total_views + 1), INTEGER);
			$sql .= " WHERE article_id=" . $db->tosql($article_id, INTEGER);
			$db->query($sql);

			$articles_viewed[$article_id] = true;
			set_session("session_articles_viewed", $articles_viewed);
		}

		$t->parse("item");
		$t->set_var("no_item", "");

		if ($allowed_rate) {

			// get articles reviews settings
			$articles_reviews_settings = get_settings("articles_reviews");
			$reviews_allowed_view = get_setting_value($articles_reviews_settings, "allowed_view", 0);
			$reviews_allowed_post = get_setting_value($articles_reviews_settings, "allowed_post", 0);


			if ($reviews_allowed_view == 1 || ($reviews_allowed_view == 2 && strlen($user_id))
				|| $reviews_allowed_post == 1 || ($reviews_allowed_post == 2 && strlen($user_id))) {

				// count reviews
				$sql = " SELECT COUNT(*) FROM " . $table_prefix . "articles_reviews WHERE approved=1 AND article_id=" . $db->tosql($article_id, INTEGER);
				$total_votes = get_db_value($sql);
		  
				if ($total_votes)
				{
					// parse summary statistic
					$t->set_var("total_votes", $total_votes);
					$sql = " SELECT COUNT(*) FROM " . $table_prefix . "articles_reviews WHERE approved=1 AND rating <> 0 AND article_id=" . $db->tosql($article_id, INTEGER);
					$total_rating_votes = get_db_value($sql);
		  
					$average_rating_float = 0;
					if ($total_rating_votes)
					{
						$sql = " SELECT SUM(rating) FROM " . $table_prefix . "articles_reviews WHERE approved=1 AND rating <> 0 AND article_id=" . $db->tosql($article_id, INTEGER);
						$average_rating_float = round(get_db_value($sql) / $total_rating_votes, 2);
					}
					$average_rating = round($average_rating_float, 0);
					$average_rating_image = $average_rating ? "rating-" . $average_rating : "not-rated";
					$t->set_var("average_rating_image", $average_rating_image);
					$t->set_var("average_rating_alt", $average_rating_float);
		  
					$based_on_message = str_replace("{total_votes}", $total_votes, BASED_ON_REVIEWS_MSG);
					$t->set_var("BASED_ON_REVIEWS_MSG", $based_on_message);
					$t->parse("summary_statistic", false);
		  
					$is_reviews = false;
					if ($reviews_allowed_view == 1 || ($reviews_allowed_view == 2 && strlen($user_id))) {
						$sql  = " SELECT * FROM " . $table_prefix . "articles_reviews ";
						$sql .= " WHERE recommended=1 AND approved=1 AND comments IS NOT NULL ";
						$sql .= " AND article_id=" . $db->tosql($article_id, INTEGER);
						$sql .= " ORDER BY date_added DESC";
						$db->RecordsPerPage = 1;
						$db->PageNumber = 1;
						$db->query($sql);
						if ($db->next_record())
						{
							$is_reviews = true;
							$review_user_id = $db->f("user_id");
							$review_user_name = htmlspecialchars($db->f("user_name"));
							if (!$review_user_id) {
								$review_user_name .= " (" . GUEST_MSG . ")";
							}
							$review_user_class = $review_user_id ? "forumUser" : "forumGuest";
							$rating = round($db->f("rating"), 0);
							$rating_image = $rating ? "rating-" . $rating : "not-rated";
							$t->set_var("rating_image", $rating_image);
							$t->set_var("review_user_class", $review_user_class);
							$t->set_var("review_user_name", $review_user_name);
							$date_added = $db->f("date_added", DATETIME);
							$date_added_string = va_date($datetime_show_format, $date_added);
							$t->set_var("review_date_added", $date_added_string);
							$t->set_var("review_summary", htmlspecialchars($db->f("summary")));
							$t->set_var("review_comments", nl2br(htmlspecialchars($db->f("comments"))));
		      
							$t->set_var("POSITIVE_REVIEW_MSG",  POSITIVE_REVIEW_MSG);
							$t->parse("positive_review", false);
						}
		      
						$sql  = " SELECT * FROM " . $table_prefix . "articles_reviews ";
						$sql .= " WHERE recommended=-1 AND approved=1 AND comments IS NOT NULL ";
						$sql .= " AND article_id=" . $db->tosql($article_id, INTEGER);
						$sql .= " ORDER BY date_added DESC";
						$db->RecordsPerPage = 1;
						$db->PageNumber = 1;
						$db->query($sql);
						if ($db->next_record())
						{
							$is_reviews = true;
							$review_user_id = $db->f("user_id");
							$review_user_name = htmlspecialchars($db->f("user_name"));
							if (!$review_user_id) {
								$review_user_name .= " (" . GUEST_MSG . ")";
							}
							$review_user_class = $review_user_id ? "forumUser" : "forumGuest";
							$rating = round($db->f("rating"), 0);
							$rating_image = $rating ? "rating-" . $rating : "not-rated";
							$t->set_var("rating_image", $rating_image);
							$t->set_var("review_user_class", $review_user_class);
							$t->set_var("review_user_name", $review_user_name);
							$date_added = $db->f("date_added", DATETIME);
							$date_added_string = va_date($datetime_show_format, $date_added);
							$t->set_var("review_date_added", $date_added_string);
							$t->set_var("review_summary", htmlspecialchars($db->f("summary")));
							$t->set_var("review_comments", nl2br(htmlspecialchars($db->f("comments"))));
		      
							$t->set_var("NEGATIVE_REVIEW_MSG",  NEGATIVE_REVIEW_MSG);
							$t->parse("negative_review", false);
						}
					}
		  
					if ($is_reviews) {
						$t->set_var("SEE_ALL_REVIEWS_MSG",  SEE_ALL_REVIEWS_MSG);
						$t->parse("all_reviews_link", false);
					}
				} else {
					$t->parse("not_rated", false);
				}
				$t->parse("reviews", false);
			}
		}
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>