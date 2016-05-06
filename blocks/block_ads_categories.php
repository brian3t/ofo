<?php
include_once("./includes/ads_functions.php");

function ads_categories($block_name, $category_id, $block_prefix="ads_categories")
{
	global $t, $db, $table_prefix;
	global $page_settings, $restrict_categories_images, $list_url, $list_page;
	global $categories, $category_number, $settings;
	global $site_id, $db_type;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$friendly_urls      = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$columns            = get_setting_value($page_settings, $block_prefix."_columns", 1);
	$categories_type    = get_setting_value($page_settings, $block_prefix."_type");

	$search_string = get_param("search_string");
	$is_search     = strlen($search_string);
  
	$t->set_var("list_href",        "ads.php");
	$t->set_var("details_href",     "ads_details.php");
	$t->set_var("top_category_name",ADS_TITLE);

	$list_page = "ads.php";
	$list_url = new VA_URL("ads.php");


	if (($categories_type == 2)||($categories_type == 1)) 
	{
    	$t->set_file("block_body", "block_categories_catalog.html");
		$t->set_var("catalog_sub",      "");
		$t->set_var("catalog_sub_more", "");
		$t->set_var("catalog_rows",     "");
		$t->set_var("catalog_top",      "");
		$t->set_var("catalog_description", "");
  
		$categories_ids = VA_Ads_Categories::find_all_ids("c.parent_category_id=" . $db->tosql($category_id, INTEGER), VIEW_CATEGORIES_PERM);		
		if (!$categories_ids) return;
		$allowed_categories_ids = VA_Ads_Categories::find_all_ids("c.parent_category_id=" . $db->tosql($category_id, INTEGER), VIEW_CATEGORIES_ITEMS_PERM);
				
		if ($categories_type == 2) {
			$sub_categories_ids = VA_Ads_Categories::find_all_ids("c.parent_category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")", VIEW_CATEGORIES_PERM);
			if (!$sub_categories_ids)
				$categories_type = 1;
		}

		if ($categories_type == 1) {
			$sql  = " SELECT category_id as top_category_id, category_name as top_category_name, friendly_url AS top_friendly_url, ";
			$sql .= " short_description, image_small ";	
			$sql .= " FROM " . $table_prefix . "ads_categories ";
			$sql .= " WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ") ";
			$sql .= " ORDER BY category_order ";
		} else {
			// show categories as catalog
			$allowed_sub_categories_ids = VA_Ads_Categories::find_all_ids("c.parent_category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")", VIEW_CATEGORIES_ITEMS_PERM);			
			$sql  = " SELECT c.category_id as top_category_id,c.category_name as top_category_name, c.friendly_url AS top_friendly_url, c.image_small, ";
			$sql .= " s.category_id as sub_category_id,s.category_name as sub_category_name, s.friendly_url AS sub_friendly_url ";
			$sql .= " FROM (" . $table_prefix . "ads_categories c ";
			$sql .= " LEFT JOIN " . $table_prefix . "ads_categories s ON c.category_id=s.parent_category_id) ";	
			$sql .= " WHERE c.category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . " ) ";
			$sql .= " AND (s.category_id IS NULL OR s.category_id IN (" . $db->tosql($sub_categories_ids, INTEGERS_LIST) . ")) ";
			$sql .= " ORDER BY c.category_order, c.category_id, s.category_order ";
		}
		$db->query($sql);
		if($db->next_record())
		{
			$ads_category_number = 0;
			$is_subcategories = true;
			$shown_sub_categories = get_setting_value($page_settings, $block_prefix."_subs"); 
			$catalog_top_number = 0;
			$catalog_sub_number = 0;
			$column_width = intval(100 / $columns);
			$t->set_var("column_width", $column_width . "%");
			do
			{
				$ads_category_number++;
				$catalog_sub_number++;
				$top_category_id = $db->f("top_category_id");
				$top_category_name = get_translation($db->f("top_category_name"));
				$top_friendly_url = $db->f("top_friendly_url");
				$sub_category_id = $db->f("sub_category_id");
				$sub_category_name = get_translation($db->f("sub_category_name"));
				$sub_friendly_url = $db->f("sub_friendly_url");
				$t->set_var("catalog_top_id", $top_category_id);
				$t->set_var("catalog_top_name", $top_category_name);
  				if ($categories_type == 2){
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
				$top_category_name = $db->f("top_category_name");
				$is_next_record = $db->next_record();

				$is_new_top = ($top_category_id != $db->f("top_category_id"));
  
				if ($categories_type == 2){
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
							if (isset($restrict_categories_images) && $restrict_categories_images) { $category_image = "image_show.php?ad_category_id=".$top_category_id."&type=small"; }
						}
						$t->set_var("alt", htmlspecialchars($top_category_name));
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

			if($catalog_top_number % $columns != 0)
			{
				$t->parse("catalog_rows");
			}
  
			$t->parse("block_body", false);
			$t->parse($block_name, true);
		}

	} else { // list type 

		$t->set_file("block_body", "block_categories_list.html");
		$t->set_var("categories_rows", "");
		$t->set_var("categories",      "");

		$categories_image = get_setting_value($page_settings, $block_prefix."_image");
		$category_path = "0";
		if ($categories_type == 4) { // Tree-type structure
			$sql  = " SELECT category_path ";
			$sql .= " FROM " . $table_prefix . "ads_categories ";
			$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$category_path  = $db->f("category_path");
				$category_path .= $category_id;
			}
			$categories_ids = VA_Ads_Categories::find_all_ids("c.parent_category_id IN (" . $db->tosql($category_path, INTEGERS_LIST) . ")", VIEW_CATEGORIES_PERM);
			$allowed_categories_ids = VA_Ads_Categories::find_all_ids("c.parent_category_id IN (" . $db->tosql($category_path, INTEGERS_LIST) . ")", VIEW_CATEGORIES_ITEMS_PERM);
		} else {
			$categories_ids = VA_Ads_Categories::find_all_ids("", VIEW_CATEGORIES_PERM);
			$allowed_categories_ids = VA_Ads_Categories::find_all_ids("", VIEW_CATEGORIES_ITEMS_PERM);
		}
		
		if (!$categories_ids) return;
		
		$categories = array();
		$sql  = " SELECT category_id, category_name, friendly_url, short_description, parent_category_id, image_small ";		
		$sql .= " FROM " . $table_prefix . "ads_categories ";
		$sql .= " WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ") ";
		$sql .= " ORDER BY category_order ";
		$db->query($sql);
		while ($db->next_record()) {
			$cur_category_id = $db->f("category_id");
			$category_name = get_translation($db->f("category_name"));
			$friendly_url = $db->f("friendly_url");
			$short_description = get_translation($db->f("short_description"));
			$image_small = $db->f("image_small");
			$parent_category_id = $db->f("parent_category_id");
			$categories[$cur_category_id]["parent_id"] = $parent_category_id;
			$categories[$cur_category_id]["category_name"] = $category_name;
			$categories[$cur_category_id]["friendly_url"] = $friendly_url;
			$categories[$cur_category_id]["short_description"] = $short_description;
			$categories[$cur_category_id]["image"] = $image_small;
			$categories[$cur_category_id]["image_alt"] = "";
			if (!$allowed_categories_ids || !in_array($cur_category_id, $allowed_categories_ids)) {
				$categories[$cur_category_id]["allowed"] = false;
			} else {
				$categories[$cur_category_id]["allowed"] = true;
			}
			$categories[$parent_category_id]["subs"][] = $cur_category_id;
		}

		
		if (sizeof($categories) > 0 && isset($categories[0]))
		{
			$category_number = 0;
			$column_width = intval(100 / $columns);
			$t->set_var("column_width", $column_width . "%");

			set_categories(0, 0, $columns, 0, $categories_image);

			if($category_number % $columns != 0) {
				$t->parse("categories_rows");
			}
  
			$t->parse("block_body", false);
			$t->parse($block_name, true);
		}
	}

}

?>