<?php
include_once("./includes/articles_functions.php");

function articles_latest($block_name, $top_id, $top_name)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings;
	global $datetime_show_format;
	
	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	if (!strlen($top_name) && !VA_Articles_Categories::check_permissions($top_id, VIEW_CATEGORIES_ITEMS_PERM)) {
		$sql  = " SELECT category_name, friendly_url ";
		$sql .= " FROM " . $table_prefix . "articles_categories ";				
		$sql .= " WHERE category_id=" . $db->tosql($top_id, INTEGER);			
						
		$db->query($sql);
		if ($db->next_record()) {
			$top_name         = get_translation($db->f("category_name"));
			$top_friendly_url = $db->f("friendly_url");
		} else {
			return false;
		}
	} else {
		$top_friendly_url = "";
	}

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$t->set_file("block_body", "block_articles_latest.html");
	$t->set_var("latest_rows", "");
	$t->set_var("latest_cols", "");
	$t->set_var("articles_category", "");
	$t->set_var("articles_top", "");
	$t->set_var("articles_sub", "");

	$t->set_var("top_category_name",$top_name);

	$latest_group   = get_setting_value($page_settings, "a_latest_group_by_" . $top_id, 0);
	$latest_cats    = get_setting_value($page_settings, "a_latest_cats_" . $top_id, "");
	$latest_subcats = get_setting_value($page_settings, "a_latest_subcats_" . $top_id, 0);
	$latest_recs    = get_setting_value($page_settings, "a_latest_recs_" . $top_id, 1);
	$latest_subrecs = get_setting_value($page_settings, "a_latest_subrecs_" . $top_id, 0);
	$latest_columns = get_setting_value($page_settings, "a_latest_cols_" . $top_id, 1);
	$latest_image   = get_setting_value($page_settings, "a_latest_image_" . $top_id,  0);
	$latest_desc    = get_setting_value($page_settings, "a_latest_desc_" . $top_id, 1);
	$t->set_var("latest_column", (100 / $latest_columns) . "%");
	$category_number = 0;

	$image_field = ""; $image_alt_field = ""; $desc_field = "";
	if ($latest_image == 1) {
		$image_field = "image_tiny";
		$image_alt_field = "image_tiny_alt";
	} else if ($latest_image == 2) {
		$image_field = "image_small";
		$image_alt_field = "image_small_alt";
	} else if ($latest_image == 3) {
		$image_field = "image_large";
		$image_alt_field = "image_large_alt";
	} else if ($latest_image == 4) {
		$image_field = "image_super";
		$image_alt_field = "image_large_alt";
	}
	if ($latest_desc == 1) {
		$desc_field = "short_description";
	} else if ($latest_desc == 2) {
		$desc_field = "full_description";
	} else if ($latest_desc == 3) {
		$desc_field = "hot_description";
	}	
	
	if ($latest_group) {
		if ($latest_group == 3) {
			$cats_ids = explode(",", $latest_cats);
		} else {
			$cats_ids = array($top_id);
		}		
		if ($latest_group == 1) {
			$where = "";
			foreach ($cats_ids AS $cat_id) {
				if ($where) $where .= " OR ";
				$where .= " c.category_path LIKE '0," . $cat_id . ",' ";
			}
			$where = "(" . $where . ")";
		} else if ($latest_group == 2) {
			$where = "";
			foreach ($cats_ids AS $cat_id) {
				if ($where) $where .= " OR ";
				$where .= " c.category_path LIKE '0," . $cat_id . ",%' ";
			}
			$where = "(" . $where . ")";
		} else if ($latest_group == 3) {
			$where = " c.category_id IN (" . $db->tosql($cats_ids, INTEGERS_LIST) .")";
		}
			
		$categories = VA_Articles_Categories::find_all(
			"c.category_id", 
			array("c.category_name", "c.category_path", "c.friendly_url"),
			$where,
			VIEW_CATEGORIES_ITEMS_PERM
		);
	} else {
		$categories[$top_id] = array($top_name, "0,", $top_friendly_url);
		$latest_subcats = 1;
	}

	foreach ($categories as $category_id => $category_info) {
		list($category_name, $category_path, $category_friendly_url) = array_values($category_info);
		
		$where  = " (c.category_id=" . $db->tosql($category_id, INTEGER);
		if ($latest_subcats) {
			$where .= " OR c.category_path LIKE '" . $category_path . $category_id . ",%') ";
		} else {
			$where .= " ) ";
		}
		
		$db->RecordsPerPage = $latest_recs + $latest_subrecs ;
		$db->PageNumber = 1;
		$articles_ids = VA_Articles::find_all_ids(
			array(
				"order" => " ORDER BY a.article_date DESC, a.article_order ",
				"where" => $where
			),
			VIEW_CATEGORIES_ITEMS_PERM
		);
		if (!$articles_ids) continue;
		
		$allowed_articles_ids = VA_Articles::find_all_ids("a.article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);	
		
		$sql  = " SELECT article_id, article_title, friendly_url, article_date, is_remote_rss, details_remote_url ";
		if ($image_field) { $sql .= " , " . $image_field; }
		if ($image_alt_field) { $sql .= " , " . $image_alt_field; }
		if ($desc_field) { $sql .= " , " . $desc_field; }			
		$sql .= " FROM " . $table_prefix . "articles a ";
		$sql .= " WHERE article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")";
		$sql .= " ORDER BY article_date DESC, article_order ";	
		$db->query($sql);
		if($db->next_record()) {
			$category_number++;
			$latest_number = 0;
			if ($friendly_urls && $category_friendly_url) {
				$t->set_var("list_url", $category_friendly_url . $friendly_extension);
			} else {
				$t->set_var("list_url", "articles.php?category_id=" . $category_id);
			}
			$t->set_var("articles_top", "");
			$t->set_var("articles_sub", "");
			do {
				$latest_number++;
				$article_id = $db->f("article_id");
				$article_title = get_translation($db->f("article_title"));
				$friendly_url = $db->f("friendly_url");
				$is_remote_rss = $db->f("is_remote_rss");
				$details_remote_url = $db->f("details_remote_url");
	
				if ($is_remote_rss == 0){
					if ($friendly_urls && $friendly_url) {
						$t->set_var("details_url", $friendly_url . $friendly_extension);
					} else {
						$t->set_var("details_url", "article.php?article_id=" . $article_id);
					}
				} else {
					$t->set_var("details_url", $details_remote_url);
				}
				
				if (!$allowed_articles_ids || !in_array($article_id, $allowed_articles_ids)) {
					$t->set_var("restricted_class", " restrictedItem");
					$t->sparse("restricted_image", false);
				} else {
					$t->set_var("restricted_class", "");
					$t->set_var("restricted_image", "");
				}
				
				$t->set_var("article_id", $article_id);
				$t->set_var("latest_item_name", $article_title);
	
				$article_image = ""; $article_image_alt = ""; $article_desc = "";
				if ($image_field) {
					$article_image = $db->f($image_field);	
					$article_image_alt = $db->f($image_alt_field);	
				}
				if ($desc_field) {
					$article_desc = get_translation($db->f($desc_field));
				}
	
				if (strlen($article_image)) {
					if (preg_match("/^http\:\/\//", $article_image)) {
						$image_size = "";
					} else {
						$image_size = @GetImageSize($article_image);
						if (isset($restrict_articles_images) && $restrict_articles_images) { 
							$article_image = "image_show.php?article_id=".$article_id."&type=small"; 
						}
					}
					if (!strlen($article_image_alt)) { 
						$article_image_alt = $article_title; 
					}
					$t->set_var("alt", htmlspecialchars($article_image_alt));
					$t->set_var("src", htmlspecialchars($article_image));
					if(is_array($image_size)) {
						$t->set_var("width", "width=\"" . $image_size[0] . "\"");
	  		     		$t->set_var("height", "height=\"" . $image_size[1] . "\"");
					} else {
		          		$t->set_var("width", "");
	  		     		$t->set_var("height", "");
					}
					$t->sparse("image_small_block", false);
				} else {
					$t->set_var("image_small_block", "");
				}
				if ($article_desc) {
					$t->set_var("short_description", $article_desc);
					$t->set_var("desc_text", $article_desc);
					$t->sparse("article_desc", false);
				} else {
					$t->set_var("article_desc", "");
				}
	
	  
				$article_date = $db->f("article_date", DATETIME);
				$article_date_string  = va_date($datetime_show_format, $article_date);
				$t->set_var("article_date", $article_date_string);
				if ($latest_number <= $latest_recs) {
					$t->parse("articles_top", true);
				} else {
					$t->parse("articles_sub", true);
				}
	  
				if (!$latest_group) { // parse columns for simple list
					$t->parse("latest_cols");
					$t->set_var("articles_top", "");
					$t->set_var("articles_sub", "");
					if ($latest_number % $latest_columns == 0) {
						$t->parse("latest_rows");
						$t->set_var("latest_cols", "");
					}
				}
	
			} while ($db->next_record());              	
	
			if ($latest_group) {
				$t->set_var("category_name", $category_name);
				$t->parse("articles_category", false);
				
				$t->parse("latest_cols");
				if($category_number % $latest_columns == 0) {
					$t->parse("latest_rows");
					$t->set_var("latest_cols", "");
				}
			} else {	
				if ($latest_number % $latest_columns != 0) {
					$t->parse("latest_rows");
				}
			}
		}		
	}

	if ($latest_group && $category_number % $latest_columns != 0) {
		$t->parse("latest_rows");
	}

	if ($category_number) {
			$t->parse("block_body", false);
			$t->parse($block_name, true);
	}

}

?>