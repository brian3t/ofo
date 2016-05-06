<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  reviews.php                                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");
	include_once("./includes/navigator.php");
	include_once("./includes/record.php");
	include_once("./includes/products_functions.php");
	include_once("./includes/reviews_functions.php");
	include_once("./includes/shopping_cart.php");
	include_once("./messages/" . $language_code . "/cart_messages.php");
	include_once("./messages/" . $language_code . "/reviews_messages.php");

	$display_products = get_setting_value($settings, "display_products", 0);
	if ($display_products == 1) {
		// user need to be logged in before viewing products
		check_user_session();
	}
	$item_id = get_param("item_id");
	if (!VA_Products::check_permissions($item_id, VIEW_ITEMS_PERM)) {
		header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
		exit;
	}

	// include blocks
	include_once("./blocks/block_products_breadcrumb.php");
	include_once("./blocks/block_categories_list.php");
	include_once("./blocks/block_products_top_rated.php");
	include_once("./blocks/block_products_top_sellers.php");
	include_once("./blocks/block_products_recently.php");
	include_once("./blocks/block_reviews.php");
	include_once("./blocks/block_custom.php");
	include_once("./blocks/block_banners.php");
	include_once("./blocks/block_cart.php");
	include_once("./blocks/block_poll.php");
	include_once("./blocks/block_currency.php");
	include_once("./blocks/block_search.php");

	$current_page = "reviews.php";
	$page_settings = va_page_settings("products_details", 0);
	$tax_rates = get_tax_rates();
	$is_reviews = true;
		
	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main","reviews.html");
	$t->set_var("current_href", "reviews.php");

	include_once("./header.php");

	$meta_description = "";
	if (is_array($page_settings)) {
		foreach ($page_settings as $setting_name => $setting_value)
		{
			if ($setting_name == "top_products_block") {
				top_products($setting_value);
			} elseif ($setting_name == "top_products_block") {
				include_once("./blocks/block_products_top_rated.php");
				top_products($setting_value);
			} elseif ($setting_name == "products_top_sellers") {
				products_top_sellers($setting_value);
			} elseif ($setting_name == "products_latest") {
				include_once("./blocks/block_products_latest.php");
				products_latest($setting_value);
			} elseif ($setting_name == "products_top_viewed") {
				include_once("./blocks/block_products_top_viewed.php");
				products_top_viewed($setting_value);
			} elseif ($setting_name == "search_block") {
				search_form($setting_value);
			} elseif ($setting_name == "products_recently_viewed") {
				products_recently_viewed($setting_value);
			} elseif ($setting_name == "login_block") {
				login_form($setting_value);
			} elseif ($setting_name == "cart_block") {
				small_cart($setting_value);
			} elseif ($setting_name == "products_breadcrumb") {
				products_breadcrumb($setting_value);
			} elseif ($setting_name == "categories_block") {
				categories($setting_value, "categories");
			} elseif ($setting_name == "subcategories_block") {
				categories($setting_value, "subcategories");
			} elseif ($setting_name == "manufacturers_block") {
				include_once("./blocks/block_manufacturers.php");
				manufacturers($setting_value);
			} elseif ($setting_name == "manufacturer_info_block") {
				include_once("./blocks/block_manufacturer_info.php");
				manufacturer_info($setting_value);
			} elseif ($setting_name == "merchants_block") {
				include_once("./blocks/block_merchants.php");
				merchants($setting_value);
			} elseif ($setting_name == "category_description_block") {
				include_once("./blocks/block_category_description.php");
				category_description($setting_value);
			} elseif ($setting_name == "details_block") {
				reviews($setting_value);
			} elseif ($setting_name == "related_block") {
				include_once("./blocks/block_products_related.php");
				related_products($setting_value, "product_related");
			} elseif ($setting_name == "subscribe_block") {
				include_once("./blocks/block_subscribe.php");
				subscribe_form($setting_value);
			} elseif ($setting_name == "poll_block") {
				poll_form($setting_value);
			} elseif ($setting_name == "language_block") {
				include_once("./blocks/block_language.php");
				language_form($setting_value, $page_settings["language_selection"], "");
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

	$t->set_var("meta_description", get_meta_desc($meta_description));
	$t->pparse("main");

?>