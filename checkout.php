<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  checkout.php                                             ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");
	include_once("./includes/record.php");
	include_once("./includes/products_functions.php");
	include_once("./includes/shopping_cart.php");
	include_once("./includes/ads_functions.php");
	include_once("./includes/navigator.php");
	include_once("./messages/" . $language_code . "/cart_messages.php");

	$current_page = "checkout.php";
	$tax_rates = get_tax_rates();

	$layout_id = 0;
	$page_settings = va_page_settings("checkout_login", $layout_id);

	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main", "checkout.html");

	include_once("./header.php");
	include_once("./blocks/block_custom.php");

	if (is_array($page_settings)) {
		foreach ($page_settings as $setting_name => $setting_value)
		{
			if ($setting_name == "site_map_block") {
				include_once("./blocks/block_site_map.php");
				site_map($setting_value);
			} elseif ($setting_name == "top_products_block") {
				include_once("./blocks/block_products_top_rated.php");
				top_products($setting_value);
			} elseif ($setting_name == "products_top_sellers") {
				include_once("./blocks/block_products_top_sellers.php");
				products_top_sellers($setting_value);
			} elseif ($setting_name == "products_latest") {
				include_once("./blocks/block_products_latest.php");
				products_latest($setting_value);
			} elseif ($setting_name == "products_top_viewed") {
				include_once("./blocks/block_products_top_viewed.php");
				products_top_viewed($setting_value);
			} elseif ($setting_name == "wishlist_search") {
				include_once("./blocks/block_wishlist_search.php");
				wishlist_search($setting_value);
			} elseif ($setting_name == "search_block") {
				include_once("./blocks/block_search.php");
				search_form($setting_value);
			} elseif ($setting_name == "products_recently_viewed") {
				include_once("./blocks/block_products_recently.php");
				products_recently_viewed($setting_value);
			} elseif ($setting_name == "products_recommended") {
				include_once("./blocks/block_products_recommended.php");
				products_recommended($setting_value);
			} elseif ($setting_name == "categories_block") {
				include_once("./blocks/block_categories_list.php");
				categories($setting_value, "categories");
			} elseif ($setting_name == "subcategories_block") {
				include_once("./blocks/block_categories_list.php");
				categories($setting_value, "subcategories");
			} elseif ($setting_name == "manufacturers_block") {
				include_once("./blocks/block_manufacturers.php");
				manufacturers($setting_value);
			} elseif ($setting_name == "merchants_block") {
				include_once("./blocks/block_merchants.php");
				merchants($setting_value);
			} elseif ($setting_name == "offers_block") {
				include_once("./blocks/block_offers.php");
				offers($setting_value);
			} elseif ($setting_name == "products_releases") {
				include_once("./blocks/block_products_releases.php");
				products_releases($setting_value);
			} elseif ($setting_name == "support_block") {
				include_once("./includes/record.php");
				include_once("./messages/".$language_code."/support_messages.php");
				include_once("./blocks/block_support.php");
				support_block($setting_value);
			} elseif ($setting_name == "forum_search_block") {
				include_once("./blocks/block_forum_search.php");
				forum_search($setting_value);
			} elseif ($setting_name == "forum_latest") {
				include_once("./blocks/block_forum_latest.php");
				forum_latest($setting_value);
			} elseif ($setting_name == "forum_top_viewed") {
				include_once("./blocks/block_forum_top_viewed.php");
				forum_top_viewed($setting_value);
			} elseif ($setting_name == "ads_search") {
				include_once("./blocks/block_ads_search.php");
				ads_search($setting_value, 0);
			} elseif ($setting_name == "ads_hot") {
				include_once("./blocks/block_ads_hot.php");
				ads_hot($setting_value, 0, ADS_TITLE);
			} elseif ($setting_name == "ads_special") {
				include_once("./blocks/block_ads_special.php");
				ads_special($setting_value, 0, ADS_TITLE);
			} elseif ($setting_name == "ads_categories") {
				include_once("./blocks/block_ads_categories.php");
				ads_categories($setting_value, 0, "ads_categories");
			} elseif ($setting_name == "ads_subcategories") {
				include_once("./blocks/block_ads_categories.php");
				ads_categories($setting_value, 0, "ads_subcategories");
			} elseif ($setting_name == "ads_recently_viewed") {
				include_once("./blocks/block_ads_recently.php");
				ads_recently_viewed($setting_value);
			} elseif ($setting_name == "ads_sellers") {
				include_once("./blocks/block_ads_sellers.php");
				ads_sellers($setting_value, 0);
			} elseif ($setting_name == "ads_latest") {
				include_once("./blocks/block_ads_latest.php");
				ads_latest($setting_value);
			} elseif ($setting_name == "ads_top_viewed") {
				include_once("./blocks/block_ads_top_viewed.php");
				ads_top_viewed($setting_value);
			} elseif ($setting_name == "subscribe_block") {
				include_once("./blocks/block_subscribe.php");
				subscribe_form($setting_value);
			} elseif ($setting_name == "sms_test_block") {
				include_once("./blocks/block_sms_test.php");
				sms_test_form($setting_value);
			} elseif ($setting_name == "login_block") {
				login_form($setting_value);
			} elseif ($setting_name == "userhome_breadcrumb") {
				include_once("./blocks/block_userhome_breadcrumb.php");
				userhome_breadcrumb($setting_value);
			} elseif ($setting_name == "checkout_login") {
				include_once("./blocks/block_checkout_login.php");
				checkout_login($setting_value);
			} elseif ($setting_name == "cart_block") {
				include_once("./blocks/block_cart.php");
				small_cart($setting_value);
			} elseif ($setting_name == "coupon_form") {
				include_once("./blocks/block_coupon_form.php");
				coupon_form($setting_value);
			} elseif ($setting_name == "poll_block") {
				include_once("./blocks/block_poll.php");
				poll_form($setting_value);
			} elseif ($setting_name == "language_block") {
				include_once("./blocks/block_language.php");
				language_form($setting_value, $page_settings["language_selection"]);
			} elseif ($setting_name == "currency_block") {
				include_once("./blocks/block_currency.php");
				currency_form($setting_value);
			} elseif ($setting_name == "layouts_block") {
				include_once("./blocks/block_layouts.php");
				layouts($setting_value);
			} elseif ($setting_name == "site_search_form") {
				include_once("./blocks/block_site_search_form.php");
				site_search_form($setting_value);
			} elseif (preg_match("/^navigation_block_(\d+)$/", $setting_name, $matches)) {
				include_once("./blocks/block_navigation.php");
				navigation_menu($setting_value, $matches[1]);
			} elseif (preg_match("/^custom_block_/", $setting_name)) {
				custom_block($setting_value, substr($setting_name, 13));
			} elseif (preg_match("/^banners_group_/", $setting_name)) {
				include_once ("./blocks/block_banners.php");
				banners_group($setting_value, substr($setting_name, 14));
			} elseif (preg_match("/^a_latest_(\d+)$/", $setting_name, $matches)) {
				articles_latest($setting_value, $matches[1], "");
			} elseif (preg_match("/^a_top_rated_(\d+)$/", $setting_name, $matches)) {
				articles_top_rated($setting_value, $matches[1], "");
			} elseif (preg_match("/^a_top_viewed_(\d+)$/", $setting_name, $matches)) {
				articles_top_viewed($setting_value, $matches[1], "");
			} elseif (preg_match("/^a_hot_(\d+)$/", $setting_name, $matches)) {
				articles_hot($setting_value, $matches[1]);
			} elseif (preg_match("/^a_cats_(\d+)$/", $setting_name, $matches)) {
				articles_categories($setting_value, $matches[1], "", "a_cats");
			} elseif (preg_match("/^a_subcats_(\d+)$/", $setting_name, $matches)) {
				articles_categories($setting_value, $matches[1], "", "a_subcats");
			} elseif (preg_match("/^a_search_(\d+)$/", $setting_name, $matches)) {
				articles_search($setting_value, $matches[1], "");
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

	$html_title = get_translation(get_setting_value($page_settings, "html_title", CHECKOUT_LOGIN_TITLE));
	$meta_description = get_translation(get_setting_value($page_settings, "index_description"));
	$meta_keywords = get_translation(get_setting_value($page_settings, "index_keywords"));

	$t->set_var("html_title", $html_title);
	$t->set_var("meta_description", $meta_description);
	$t->set_var("meta_keywords", $meta_keywords);
	$t->pparse("main");

?>