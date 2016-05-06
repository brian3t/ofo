<?php
include_once("./includes/ads_functions.php");

function ads_latest($block_name)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings;
	global $date_show_format;
	global $site_id, $db_type;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_ads_latest.html");
	$t->set_var("latest_rows", "");
	$t->set_var("latest_cols", "");
	$t->set_var("ADS_TITLE",        ADS_TITLE);
	$t->set_var("LATEST_TITLE",     LATEST_TITLE);
	$t->set_var("MORE_MSG",         MORE_MSG);
	$t->set_var("READ_MORE_MSG",    READ_MORE_MSG);
	$t->set_var("CLICK_HERE_MSG",   CLICK_HERE_MSG);

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$records_per_page = get_setting_value($page_settings, "ads_latest_recs", 10);
	
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = 1;
	
	$items_ids = VA_Ads::find_all_ids(
		array(
			"order" => " ORDER BY i.date_start DESC, i.date_added "
		),
		VIEW_CATEGORIES_ITEMS_PERM
	);
	if (!$items_ids) return;
	
	$allowed_items_ids = VA_Ads::find_all_ids("i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);
	
	$sql  = " SELECT item_id, item_title, friendly_url, date_start, short_description ";
	$sql .= " FROM " . $table_prefix . "ads_items ";
	$sql .= " WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
	$sql .= " ORDER BY date_start DESC, date_added ";

	$db->query($sql);
	if($db->next_record())
	{
		$latest_columns = get_setting_value($page_settings, "ads_latest_cols", 1);
		$t->set_var("latest_column", (100 / $latest_columns) . "%");
		$latest_number = 0;
		do
		{
			$latest_number++;
			$item_id = $db->f("item_id");
			$item_title = get_translation($db->f("item_title"));
			$friendly_url = $db->f("friendly_url");
			$short_description = get_translation($db->f("short_description"));
		
			$t->set_var("item_id", $item_id);
			$t->set_var("latest_item_title", $item_title);
			$t->set_var("short_description", nl2br($short_description));
			if ($friendly_urls && $friendly_url) {
				$t->set_var("details_href", $friendly_url . $friendly_extension);
			} else {
				$t->set_var("details_href", "ads_details.php?item_id=" . $item_id);
			}

			$date_start = $db->f("date_start", DATETIME);
			$date_start_string  = va_date($date_show_format, $date_start);
			$t->set_var("date_start", $date_start_string);

			if (!$allowed_items_ids || !in_array($item_id, $allowed_items_ids)) {
				$t->set_var("restricted_class", " restrictedItem");
				$t->sparse("restricted_image", false);
			} else {
				$t->set_var("restricted_class", "");
				$t->set_var("restricted_image", "");
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