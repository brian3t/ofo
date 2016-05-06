<?php
include_once("./includes/articles_functions.php");

function articles_hot($block_name, $top_id, $top_name = "", $list_fields = "", $articles_order_column = "", $articles_order_direction = "", $current_category_id = 0, $page_friendly_url = "", $page_friendly_params = array())
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings, $restrict_articles_images;
	global $datetime_show_format;
	global $current_page;


	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}
	
	if (!strlen($top_name) && !VA_Articles_Categories::check_permissions($top_id, VIEW_CATEGORIES_ITEMS_PERM)) {
		$sql  = " SELECT category_name, article_list_fields, articles_order_column, articles_order_direction ";
		$sql .= " FROM " . $table_prefix . "articles_categories ";				
		$sql .= " WHERE category_id=" . $db->tosql($top_id, INTEGER);			
						
		$db->query($sql);
		if ($db->next_record()) {
			$top_name                 = get_translation($db->f("category_name"));
			$articles_order_column    = $db->f("articles_order_column");
			$articles_order_direction = $db->f("articles_order_direction");
			$list_fields              = $db->f("article_list_fields");
		} else {
			return false;
		}
	}

	if (strlen($articles_order_column)) {
		$articles_order = " ORDER BY a." . $articles_order_column . " " . $articles_order_direction;
	} else {
		$articles_order_column = "article_order";
		$articles_order = " ORDER BY a.article_order ";
	}

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	if ($friendly_urls && $page_friendly_url) {
		$pass_parameters = get_transfer_params($page_friendly_params);
		$current_page = $page_friendly_url . $friendly_extension;
	} else {
		$pass_parameters = get_transfer_params();
	}

	$t->set_file("block_body", "block_hot.html");
	$t->set_var("hot_rows", "");
	$t->set_var("hot_cols", "");
	$t->set_var("top_category_name",$top_name);

	if ($current_category_id > 0)	{
		$sql  = " SELECT category_path FROM " . $table_prefix . "articles_categories ";
		$sql .= " WHERE category_id=" . $db->tosql($current_category_id, INTEGER);
		$current_category_path = get_db_value($sql);
		$current_category_path .= $current_category_id . ",";
	} else {
		$current_category_path = "0," . $top_id . ",";
		$current_category_id = $top_id;
	}

	$where  = " (ac.category_id = " . $db->tosql($current_category_id, INTEGER);
	$where .= " OR c.category_path LIKE '" . $db->tosql($current_category_path, TEXT, false) . "%')";
	$where .= " AND a.is_hot = 1";
	$articles_ids = VA_Articles::find_all_ids($where, VIEW_CATEGORIES_ITEMS_PERM);
	if (!$articles_ids) return false;
	
	$allowed_articles_ids = VA_Articles::find_all_ids("a.article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);
	$total_records = count($articles_ids);
		
	// set up variables for navigator
	$records_per_page = get_setting_value($page_settings, "a_hot_recs_" . $top_id, 10);
	$pages_number = 5;
	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $current_page);
	$page_number = $n->set_navigator("hot_navigator", "hot_page", SIMPLE, $pages_number, $records_per_page, $total_records, false, $pass_parameters);
	
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	
	$sql  = " SELECT article_id, article_title, friendly_url, article_date, short_description, ";
	$sql .= " image_small, image_small_alt, hot_description, is_remote_rss, details_remote_url ";
	$sql .= " FROM " .  $table_prefix . "articles ";
	$sql .= " WHERE article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")";
	$db->query($sql);
	
	$hot_columns = get_setting_value($page_settings, "a_hot_cols_" . $top_id, 1);
	$t->set_var("hot_column", (100 / $hot_columns) . "%");
	$hot_number = 0;
	while ($db->next_record()){
		$hot_number++;
		$article_id         = $db->f("article_id");
		$article_title      = get_translation($db->f("article_title"));
		$friendly_url       = $db->f("friendly_url");
		$is_remote_rss      = $db->f("is_remote_rss");
		$details_remote_url = $db->f("details_remote_url");
		$image_small        = $db->f("image_small");
		$image_small_alt    = $db->f("image_small_alt");
		$hot_description    = get_translation($db->f("hot_description"));
		if (!strlen($hot_description)) {
			$hot_description = get_translation($db->f("short_description"));
		}
		if ($is_remote_rss == 0){
			if ($friendly_urls && $friendly_url) {
				$t->set_var("details_href", $friendly_url . $friendly_extension);
			} else {
				$t->set_var("details_href", "article.php?article_id=" . $article_id);
			}
		} else {
			$t->set_var("details_href", $details_remote_url);
		}

		$t->set_var("article_id", $article_id);
		$t->set_var("hot_item_name", $article_title);
		$t->set_var("hot_description", $hot_description);

		if (strpos(",," . $list_fields . ",,", ",article_date,")) {
			$article_date = $db->f("article_date", DATETIME);
			$article_date_string  = va_date($datetime_show_format, $article_date);
			$t->set_var("article_date", $article_date_string);
			$t->global_parse("article_date_block", false, false, true);
		} else {
			$t->set_var("article_date_block", "");
		}

		if($image_small) {
			if (preg_match("/^http\:\/\//", $image_small)) {
				$image_size = "";
			} else {
				$image_size = @GetImageSize($image_small);
				if (isset($restrict_articles_images) && $restrict_articles_images) { 
					$image_small = "image_show.php?article_id=" . $article_id . "&type=small"; 
				}
			}
			if (!strlen($image_small_alt)) { 
				$image_small_alt = $article_title;
			}
			$t->set_var("alt", htmlspecialchars($image_small_alt));
			$t->set_var("src", htmlspecialchars($image_small));
			if(is_array($image_size)) {
				$t->set_var("width", "width=\"" . $image_size[0] . "\"");
				$t->set_var("height", "height=\"" . $image_size[1] . "\"");
			} else {
				$t->set_var("width", "");
				$t->set_var("height", "");
			}
			$t->parse("image_small", false);
		} else {
			$t->set_var("image_small", "");
		}
		
		if (!$allowed_articles_ids || !in_array($article_id, $allowed_articles_ids)) {
			$t->set_var("restricted_class", " restrictedItem");
			$t->sparse("restricted_image", false);
		} else {
			$t->set_var("restricted_class", "");
			$t->set_var("restricted_image", "");
		}

		$t->parse("hot_cols");
		if($hot_number % $hot_columns == 0) {
			$t->parse("hot_rows");
			$t->set_var("hot_cols", "");
		}
	}

	if ($hot_number % $hot_columns != 0) {
		$t->parse("hot_rows");
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>