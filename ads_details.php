<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  ads_details.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
                           

	include_once("./includes/common.php");
	include_once("./includes/navigator.php");
	include_once("./includes/products_functions.php");
	include_once("./includes/shopping_cart.php");
	include_once("./includes/ads_properties.php");
	include_once("./includes/ads_functions.php");
	include_once("./includes/record.php");
	include_once("./messages/" . $language_code . "/cart_messages.php");

	// include blocks
	include_once("./blocks/block_custom.php");
	include_once("./blocks/block_banners.php");

	$page_settings = va_page_settings("ads_details", 0);
	$currency = get_currency();

	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main","ads_details.html");
	$t->set_var("current_href", "ads_details.php");

	include_once("./header.php");

	$current_page = "ads_details.php";
	$item_id      = get_param("item_id");	
	$category_id  = get_param("category_id");
	if (!strlen($category_id) && strlen($item_id)) {
		$category_id = VA_Ads::get_category_id($item_id, VIEW_ITEMS_PERM);		
	}
	
	$category_description = ""; $category_large = "";
	$desc_image = get_setting_value($page_settings, "ads_cat_desc_image", 3);
	$desc_type = get_setting_value($page_settings, "ads_cat_desc_type", 2);
	
	$sql  = " SELECT category_name, short_description, full_description, image_small, image_large ";
	$sql .= " FROM " . $table_prefix . "ads_categories ";
	$sql .= " WHERE category_id = " . $db->tosql($category_id, INTEGER);
	
	$db->query($sql);
	if ($db->next_record()) {
		$category_id = $db->f("category_id");
		$current_category = get_translation($db->f("category_name"));
		if ($desc_image == 3) {
			$category_image = $db->f("image_large");
		} elseif ($desc_image == 2) {
			$category_image = $db->f("image_small");
		}
		if ($desc_type == 2) {
			$category_description = get_translation($db->f("full_description"));
		} elseif ($desc_type == 1) {
			$category_description = get_translation($db->f("short_description"));
		}
	} else {
		$current_category = "";
		$category_image   = "";
	}

	$page_friendly_url = ""; 
	$page_friendly_params = array("item_id");
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	if ($friendly_urls) {
		// retrieve info about friendly url
		$sql  = " SELECT friendly_url FROM " . $table_prefix . "ads_items WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$page_friendly_url = $db->f("friendly_url");
			friendly_url_redirect($page_friendly_url, $page_friendly_params);
		}
	}
 
	$meta_description = "";
	if (is_array($page_settings)) {
		foreach($page_settings as $setting_name => $setting_value)
		{
			if ($setting_name == "ads_search") {
				include_once("./blocks/block_ads_search.php");
				ads_search($setting_value, $category_id);
			} elseif ($setting_name == "ads_recently_viewed") {
				include_once("./blocks/block_ads_recently.php");
				ads_recently_viewed($setting_value);
			} elseif ($setting_name == "ads_breadcrumb") {
				include_once("./blocks/block_ads_breadcrumb.php");
				ads_breadcrumb($setting_value);
			} elseif ($setting_name == "ads_categories") {
				include_once("./blocks/block_ads_categories.php");
				ads_categories($setting_value, $category_id, "ads_categories");
			} elseif ($setting_name == "ads_subcategories") {
				include_once("./blocks/block_ads_categories.php");
				ads_categories($setting_value, $category_id, "ads_subcategories");
			} elseif ($setting_name == "ads_hot") {
				include_once("./blocks/block_ads_hot.php");
				ads_hot($setting_value, $category_id, $current_category, $page_friendly_url, $page_friendly_params);
			} elseif ($setting_name == "ads_special") {
				include_once("./blocks/block_ads_special.php");
				ads_special($setting_value, $category_id, $current_category, $page_friendly_url, $page_friendly_params);
			} elseif ($setting_name == "ads_users") {
				include_once("./blocks/block_ads_users.php");
				ads_user ($setting_value);
			} elseif ($setting_name == "ads_sellers") {
				include_once("./blocks/block_ads_sellers.php");
				ads_sellers($setting_value, $category_id);
			} elseif ($setting_name == "ads_latest") {
				include_once("./blocks/block_ads_latest.php");
				ads_latest($setting_value);
			} elseif ($setting_name == "ads_top_viewed") {
				include_once("./blocks/block_ads_top_viewed.php");
				ads_top_viewed($setting_value);
			} elseif ($setting_name == "ads_category_info") {
				include_once("./blocks/block_ads_category.php");
				ads_category($setting_value, $category_id, $current_category, $category_description, $category_image);
			} elseif ($setting_name == "ads_details") {
				include_once("./blocks/block_ads_details.php");
				ads_details($setting_value, $category_id);
			} elseif ($setting_name == "cart_block") {
				include_once("./blocks/block_cart.php");
				small_cart($setting_value);
			} elseif ($setting_name == "login_block") {
				login_form($setting_value);
			} elseif ($setting_name == "subscribe_block") {
				include_once("./blocks/block_subscribe.php");
				subscribe_form($setting_value);
			} elseif ($setting_name == "sms_test_block") {
				include_once("./blocks/block_sms_test.php");
				sms_test_form($setting_value);
			} elseif ($setting_name == "poll_block") {
				include_once("./blocks/block_poll.php");
				poll_form($setting_value);
			} elseif ($setting_name == "language_block") {
				include_once("./blocks/block_language.php");
				language_form($setting_value, $page_settings["language_selection"]);
			} elseif ($setting_name == "layouts_block") {
				include_once("./blocks/block_layouts.php");
				layouts($setting_value);
			} elseif ($setting_name == "site_search_form") {
				include_once("./blocks/block_site_search_form.php");
				site_search_form($setting_value);
			} elseif ($setting_name == "currency_block") {
				include_once("./blocks/block_currency.php");
				currency_form($setting_value);
			} elseif (preg_match("/^navigation_block_(\d+)$/", $setting_name, $matches)) {
				include_once("./blocks/block_navigation.php");
				navigation_menu($setting_value, $matches[1]);
			} elseif (preg_match("/^custom_block_/", $setting_name)) {
				custom_block($setting_value, substr($setting_name, 13));
			} elseif (preg_match("/^banners_group_/", $setting_name)) {
				banners_group($setting_value, substr($setting_name, 14));
			} 
		}
	}

	if (!get_setting_value($page_settings, "left_column_hide", 0)) {
		$t->set_var("left_column_width", get_setting_value($page_settings, "left_column_width", "20%"));
		$t->parse("left_column", false);
	}
	if (!get_setting_value($page_settings, "middle_column_hide", 0)) {
		$t->set_var("middle_column_width", get_setting_value($page_settings, "middle_column_width", "60%"));
		$t->parse("middle_column", false);
	}
	if (!get_setting_value($page_settings, "right_column_hide", 0)) {
		$t->set_var("right_column_width", get_setting_value($page_settings, "right_column_width", "20%"));
		$t->parse("right_column", false);
	}

	include_once("./footer.php");

	$t->set_var("html_title", htmlspecialchars($html_title));
	$t->set_var("meta_description", get_meta_desc($meta_description));
	$t->pparse("main");

?>