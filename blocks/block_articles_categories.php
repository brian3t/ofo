<?php
include_once("./includes/articles_functions.php");

function articles_categories($block_name, $top_id, $top_name, $block_prefix="a_cats", $category_id = 0)
{
	global $t, $db, $table_prefix, $restrict_categories_images;
	global $page_settings, $restrict_categories_images;
	global $categories, $category_number, $settings, $list_url, $list_page;
	
	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$columns = get_setting_value($page_settings, $block_prefix."_cols_" . $top_id, 1);
	$categories_type = get_setting_value($page_settings, $block_prefix."_type_" . $top_id);

	if (!strlen($top_name) && !VA_Articles_Categories::check_permissions($top_id, VIEW_CATEGORIES_PERM)) {
		$sql  = " SELECT category_name, article_list_fields, articles_order_column, articles_order_direction ";
		$sql .= " FROM " . $table_prefix . "articles_categories ";				
		$sql .= " WHERE category_id=" . $db->tosql($top_id, INTEGER);			
						
		$db->query($sql);
		if ($db->next_record()) {
			$top_name                 = get_translation($db->f("category_name"));
		} else {
			return false;
		}
	}

	$t->set_var("articles_href",    "articles.php");
	$t->set_var("list_href",        "articles.php");
	$t->set_var("details_href",     "article.php");
	$t->set_var("rss_href",     "articles_rss.php");
	$t->set_var("top_category_name",$top_name);

	$list_page = "articles.php";
	$list_url = new VA_URL("articles.php");

	if (($categories_type == 2)||($categories_type == 1)) {
		
		$t->set_file("block_body", "block_categories_catalog.html");
		$t->set_var("catalog_sub",      "");
		$t->set_var("catalog_sub_more", "");
		$t->set_var("catalog_rows",     "");
		$t->set_var("catalog_top",      "");
		$t->set_var("catalog_description", "");
		
		if ($category_id > 0) {
			$where = " c.parent_category_id = " . $db->tosql($category_id, INTEGER);
		} else {
			$where = " c.parent_category_id = " . $db->tosql($top_id, INTEGER);
		}
			
		$categories_ids = VA_Articles_Categories::find_all_ids($where, VIEW_CATEGORIES_PERM);
		if (!$categories_ids) return;
		$allowed_categories_ids = VA_Articles_Categories::find_all_ids($where, VIEW_CATEGORIES_ITEMS_PERM);
		
		if ($categories_type == 2) {
			$sub_categories_ids = VA_Articles_Categories::find_all_ids("c.parent_category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")", VIEW_CATEGORIES_PERM);
			if (!$sub_categories_ids)
				$categories_type = 1;
		}
		
		if ($categories_type == 1) {
			$sql  = " SELECT category_id AS top_category_id, category_name AS top_category_name, friendly_url AS top_friendly_url, ";
			$sql .= " short_description, image_small, image_small_alt, is_rss, rss_on_list ";
			$sql .= " FROM " . $table_prefix . "articles_categories";
			$sql .= " WHERE category_id IN ( " . $db->tosql($categories_ids, INTEGERS_LIST) . ")";
			$sql .= " ORDER BY category_order, category_name ";
		} else {
			// show categories as catalog
			$allowed_sub_categories_ids = VA_Articles_Categories::find_all_ids("c.parent_category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")", VIEW_CATEGORIES_ITEMS_PERM);
			
			$sql = $table_prefix . "articles_categories ac ";
			$sql = " (" . $sql . " LEFT JOIN " . $table_prefix . "articles_categories sc ON ac.category_id=sc.parent_category_id) ";
			
			$sql  = " SELECT c.category_id AS top_category_id, c.category_name AS top_category_name, c.friendly_url AS top_friendly_url,";
			$sql .= " c.image_small, c.image_small_alt,";
			$sql .= " s.category_id AS sub_category_id, s.category_name AS sub_category_name, ";
			$sql .= " s.friendly_url AS sub_friendly_url, c.is_rss, c.rss_on_list ";
			$sql .= " FROM ( " . $table_prefix . "articles_categories c ";
			$sql .= " LEFT JOIN " . $table_prefix . "articles_categories s ON c.category_id=s.parent_category_id) ";
			$sql .= " WHERE (s.category_id IN (" . $db->tosql($sub_categories_ids, INTEGERS_LIST) . ")";
			$sql .= " OR s.category_id IS NULL)";				
			$sql .= " AND c.category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")";
			$sql .= " ORDER BY c.category_order, c.category_name, c.category_id, s.category_order, s.category_name ";		
		}
		$db->query($sql);
		if($db->next_record())
		{
			$category_number = 0;
			$is_subcategories = true;
			$shown_sub_categories = get_setting_value($page_settings, $block_prefix."_subs_" . $top_id); 
			$catalog_top_number = 0;
			$catalog_sub_number = 0;
			$column_width = intval(90 / $columns);
			$t->set_var("column_width", $column_width . "%");
			do
			{
				$category_number++;
				$catalog_sub_number++;
				$top_category_id = $db->f("top_category_id");
				$top_category_name = get_translation($db->f("top_category_name"));
				$top_friendly_url = $db->f("top_friendly_url");
				$sub_category_id = $db->f("sub_category_id");
				$sub_category_name = get_translation($db->f("sub_category_name"));
				$sub_friendly_url = $db->f("sub_friendly_url");

				$t->set_var("catalog_top_id", $top_category_id);
				if ($db->f("is_rss") && $db->f("rss_on_list")){
					$t->parse("category_rss", false);
				} else {
					$t->set_var("category_rss", "");
				}
				$t->set_var("catalog_top_name", $top_category_name);
				if ($categories_type == 2) {
					$t->set_var("catalog_sub_id",   $sub_category_id);
					$t->set_var("catalog_sub_name", $sub_category_name);
				} else {
					if (strlen($db->f("short_description"))) {
						$t->set_var("short_description", get_translation($db->f("short_description")));
						$t->parse("catalog_description", false);
					} else {
						$t->set_var("catalog_description", "");
					}
				}
  
				$category_image = $db->f("image_small");
				$image_alt      = $db->f("image_small_alt");
				$category_name  = $db->f("top_category_name");
				$is_next_record = $db->next_record();

				$is_new_top = ($top_category_id != $db->f("top_category_id"));
  
				if ($categories_type == 2) {
					if($shown_sub_categories >= $catalog_sub_number || !$shown_sub_categories)
					{
						if ($sub_category_id && (!$allowed_sub_categories_ids || !in_array($sub_category_id, $allowed_sub_categories_ids))) {
							$t->set_var("restricted_sub_class", " restrictedSubCategory");
							$t->sparse("restricted_sub_image", false);
						} else {
							$t->set_var("restricted_sub_class", "");
							$t->set_var("restricted_sub_image", "");
						}
						if ($friendly_urls && $sub_friendly_url) {
							$list_url->remove_parameter("category_id");
							$t->set_var("list_url", $list_url->get_url($sub_friendly_url. $friendly_extension));
						} else {
							$list_url->add_parameter("category_id", CONSTANT, $sub_category_id);
							$t->set_var("list_url", $list_url->get_url($list_page));
						}
						if($is_next_record && !$is_new_top){
							$t->parse("catalog_sub_separator", false);
						} else {
							$t->set_var("catalog_sub_separator", "");
						}
						$t->parse("catalog_sub", true);
					} else if(($shown_sub_categories + 1) == $catalog_sub_number) {
						if ($friendly_urls && $top_friendly_url) {
							$list_url->remove_parameter("category_id");
							$t->set_var("list_url", $list_url->get_url($top_friendly_url . $friendly_extension));
						} else {
							$list_url->add_parameter("category_id", CONSTANT, $top_category_id);
							$t->set_var("list_url", $list_url->get_url($list_page));
						}

						$t->parse("catalog_sub_more", false);
					}
				}
  
				if($is_new_top)
				{
					$catalog_top_number++;

					if ($friendly_urls && $top_friendly_url) {
						$list_url->remove_parameter("category_id");
						$t->set_var("list_url", $list_url->get_url($top_friendly_url . $friendly_extension));
					} else {
						$list_url->add_parameter("category_id", CONSTANT, $top_category_id);
						$t->set_var("list_url", $list_url->get_url($list_page));
					}

					if ($category_image)
					{
						if (preg_match("/^http\:\/\//", $category_image)) {
							$image_size = "";
						} else {
							$image_size = @GetImageSize($category_image);
							if (isset($restrict_categories_images) && $restrict_categories_images) { $category_image = "image_show.php?art_cat_id=".$top_category_id."&type=small"; }
						}
            //$image_size = @GetImageSize($category_image);
						if (!strlen($image_alt)) { $image_alt = $category_name; }
							$t->set_var("alt", htmlspecialchars($image_alt));
							$t->set_var("src", htmlspecialchars($category_image));
						if(is_array($image_size)) {
							$t->set_var("width", "width=\"" . $image_size[0] . "\"");
							$t->set_var("height", "height=\"" . $image_size[1] . "\"");
						} else {
							$t->set_var("width", "");
							$t->set_var("height", "");
						}
						$t->parse("catalog_image", false);
					} else {
						$t->set_var("catalog_image", "");
					}
					
					if (!$allowed_categories_ids || !in_array($top_category_id, $allowed_categories_ids)) {
						$t->set_var("restricted_class", " restrictedCategory");
						$t->sparse("restricted_image", false);
					} else {
						$t->set_var("restricted_class", "");
						$t->set_var("restricted_image", "");
					}

					$t->parse("catalog_top");
					$t->set_var("catalog_sub", "");
					$t->set_var("catalog_sub_more", "");
					if($catalog_top_number % $columns == 0)
					{
						$t->parse("catalog_rows");
						$t->set_var("catalog_top", "");
					}
					$catalog_sub_number = 0;
				}

			} while ($is_next_record);

			if($catalog_top_number % $columns != 0) {
				$t->parse("catalog_rows");
			}
  
			$t->parse("block_body", false);
			$t->parse($block_name, true);
		}

	} else { // list type 

		$t->set_file("block_body", "block_categories_list.html");
		$t->set_var("categories_rows", "");
		$t->set_var("categories",      "");

		$categories_image = get_setting_value($page_settings, $block_prefix . "_image_" . $top_id);
		if (!$category_id) { $category_id = $top_id; }
		$category_path = "0," . $category_id;
		if ($categories_type == 4) { // Tree-type structure
			$sql  = " SELECT category_path ";
			$sql .= " FROM " . $table_prefix . "categories ";
			$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$category_path  = $db->f("category_path");
				$category_path .= $category_id;
			}
			$categories_ids = VA_Articles_Categories::find_all_ids("c.parent_category_id IN (" . $db->tosql($category_path, INTEGERS_LIST) . ")", VIEW_CATEGORIES_PERM);
			$allowed_categories_ids = VA_Articles_Categories::find_all_ids("c.parent_category_id IN (" . $db->tosql($category_path, INTEGERS_LIST) . ")", VIEW_CATEGORIES_ITEMS_PERM);
		} else {
			$categories_ids = VA_Articles_Categories::find_all_ids("", VIEW_CATEGORIES_PERM);
			$allowed_categories_ids = VA_Articles_Categories::find_all_ids("", VIEW_CATEGORIES_ITEMS_PERM);
		}

		if (!$categories_ids) return;
		
		$categories = array();
		$sql  = " SELECT category_id, category_name, friendly_url, short_description, parent_category_id, ";
		$sql .= " image_small, image_small_alt, is_rss, rss_on_list ";
		$sql .= " FROM " . $table_prefix . "articles_categories ";	
		$sql .= " WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ") ";		
		$sql .= " ORDER BY category_order, category_name ";
		$db->query($sql);
		while ($db->next_record()) {
			$cur_category_id = $db->f("category_id");
			$category_name = get_translation($db->f("category_name"));
			$friendly_url = $db->f("friendly_url");
			$short_description = get_translation($db->f("short_description"));
			$image_small = $db->f("image_small");
			$image_small_alt = $db->f("image_small_alt");
			$is_rss = ($db->f("is_rss") && $db->f("rss_on_list"));

			$parent_category_id = $db->f("parent_category_id");
			$categories[$cur_category_id]["parent_id"] = $parent_category_id;
			$categories[$cur_category_id]["category_name"] = $category_name;
			$categories[$cur_category_id]["friendly_url"] = $friendly_url;
			$categories[$cur_category_id]["short_description"] = $short_description;
			$categories[$cur_category_id]["image"] = $image_small;
			$categories[$cur_category_id]["image_alt"] = $image_small_alt;
			$categories[$cur_category_id]["is_rss"] = $is_rss;
			if (!$allowed_categories_ids || !in_array($cur_category_id, $allowed_categories_ids)) {
				$categories[$cur_category_id]["allowed"] = false;
			} else {
				$categories[$cur_category_id]["allowed"] = true;
			}
			$categories[$parent_category_id]["subs"][] = $cur_category_id;
		}
  
		if (sizeof($categories) > 0 && isset($categories[$top_id]))
		{
			$category_number = 0;
			$column_width = intval(100 / $columns);
			$t->set_var("column_width", $column_width . "%");

			set_categories($top_id, 0, $columns, $top_id, $categories_image);

			if ($category_number % $columns != 0) {
				$t->parse("categories_rows");
			}
  
			$t->parse("block_body", false);
			$t->parse($block_name, true);
		}
	}
}
?>