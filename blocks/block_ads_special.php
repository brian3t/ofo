<?php
include_once("./includes/ads_functions.php");

function ads_special($block_name, $category_id, $category_name, $page_friendly_url = "", $page_friendly_params = array())
{
	global $t, $db, $db_type, $table_prefix;
	global $settings, $page_settings, $restrict_ads_images;
	global $datetime_show_format;
	global $current_page;
	global $site_id;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_ads_special.html");
	$t->set_var("special_rows", "");
	$t->set_var("special_cols", "");
	$t->set_var("top_category_name",$category_name);

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	if ($friendly_urls && $page_friendly_url) {
		$pass_parameters = get_transfer_params($page_friendly_params);
		$current_page = $page_friendly_url . $friendly_extension;
	} else {
		$pass_parameters = get_transfer_params();
	}
	
	$search_tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "ads_categories", "tree", "");

	$sql_where  = " i.is_approved=1 ";
	$sql_where .= " AND i.is_special=1 ";
	$sql_where .= " AND i.special_date_start<=" . $db->tosql(va_time(), DATETIME);
	$sql_where .= " AND i.special_date_end>" . $db->tosql(va_time(), DATETIME);
	if ($category_id > 0)	{
		$sql_where .= " AND (c.category_id = " . $db->tosql($category_id, INTEGER);
		$sql_where .= " OR c.category_path LIKE '" . $db->tosql($search_tree->get_path($category_id), TEXT, false) . "%')";
	}
	$items_ids = VA_Ads::find_all_ids($sql_where, VIEW_CATEGORIES_ITEMS_PERM);
	
	if (!$items_ids) return;
	
	$allowed_items_ids = VA_Ads::find_all_ids("i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")", VIEW_CATEGORIES_ITEMS_PERM);
	$total_records = count($items_ids);
	
	$records_per_page = get_setting_value($page_settings, "ads_special_recs", 10);
	$pages_number = 5;
	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $current_page);
	$page_number = $n->set_navigator("special_navigator", "special_page", SIMPLE, $pages_number, $records_per_page, $total_records, false, $pass_parameters);

	$sql  = " SELECT item_id, item_title, friendly_url, short_description, image_small, special_description ";
	$sql .= " FROM " . $table_prefix . "ads_items ";
	$sql .= " WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
	$sql .= " ORDER BY date_start DESC, date_added ";

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql);
	if($db->next_record())
	{
		$special_columns = get_setting_value($page_settings, "ads_special_cols", 1);
		$t->set_var("special_column", (100 / $special_columns) . "%");
		$special_number = 0;
		do
		{
			$special_number++;
			$item_id = $db->f("item_id");
			$item_title = get_translation($db->f("item_title"));
			$friendly_url = $db->f("friendly_url");
			$special_description = get_translation($db->f("special_description"));
			if (!strlen($special_description)) {
				$special_description = get_translation($db->f("short_description"));
			}

			$t->set_var("item_id", $item_id);
			$t->set_var("special_item_name", $item_title);
			$t->set_var("special_description", $special_description);
			if ($friendly_urls && $friendly_url) {
				$t->set_var("details_href", $friendly_url . $friendly_extension);
			} else {
				$t->set_var("details_href", "ads_details.php?item_id=" . $item_id);
			}

			$image_small = $db->f("image_small");
			if($image_small) {
				if (preg_match("/^http\:\/\//", $image_small)) {
					$image_size = "";
				} else {
	        		$image_size = @GetImageSize($image_small);
					if (isset($restrict_ads_images) && $restrict_ads_images) { 
						$image_small = "image_show.php?ad_id=".$item_id."&type=small"; 
					}
				}
				$t->set_var("alt", htmlspecialchars($item_title));
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

			if (!$allowed_items_ids || !in_array($item_id, $allowed_items_ids)) {
				$t->set_var("restricted_class", " restrictedItem");
				$t->sparse("restricted_image", false);
			} else {
				$t->set_var("restricted_class", "");
				$t->set_var("restricted_image", "");
			}
			
			$t->parse("special_cols");
			if($special_number % $special_columns == 0) {
				$t->parse("special_rows");
				$t->set_var("special_cols", "");
			}

		} while ($db->next_record());

		if ($special_number % $special_columns != 0) {
			$t->parse("special_rows");
		}

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>