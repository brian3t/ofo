<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  product_details.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	$type = "details";
	include_once("./includes/common.php");
	include_once("./messages/" . $language_code . "/cart_messages.php");
	include_once("./messages/" . $language_code . "/reviews_messages.php");
	include_once("./messages/" . $language_code . "/download_messages.php");
	include_once("./includes/navigator.php");
	include_once("./includes/items_properties.php");
	include_once("./includes/products_functions.php");
	include_once("./includes/shopping_cart.php");
	include_once("./includes/previews_functions.php");
	
	$display_products = get_setting_value($settings, "display_products", 0);
	if ($display_products == 1) {
		// user need to be logged in before viewing products
		check_user_session();
	}

	// include blocks
	include_once("./blocks/block_products_breadcrumb.php");
	include_once("./blocks/block_categories_list.php");
	include_once("./blocks/block_products_top_rated.php");
	include_once("./blocks/block_products_top_sellers.php");
	include_once("./blocks/block_product_details.php");
	include_once("./blocks/block_custom.php");
	include_once("./blocks/block_banners.php");
	include_once("./blocks/block_cart.php");
	include_once("./blocks/block_poll.php");
	include_once("./blocks/block_currency.php");
	include_once("./blocks/block_search.php");

	$current_page  = "product_details.php";
	$confirm_add   = get_setting_value($settings, "confirm_add", 1);
	$page_settings = va_page_settings("products_details", 0);
	$tax_rates     = get_tax_rates();

	$page_friendly_url = ""; 
	$page_friendly_params = array("item_id");
	$item_id = get_param("item_id");
	if (!strlen($item_id)) {
		// check item_id by code
		$item_code = get_param("item_code");
		$manufacturer_code = get_param("manufacturer_code");
		if (strlen($item_code)) {
			$sql = " SELECT item_id FROM " . $table_prefix . "items WHERE item_code=" . $db->tosql($item_code, TEXT);
			$item_id = get_db_value($sql);
		} elseif (strlen($manufacturer_code)) {
			$sql = " SELECT item_id FROM " . $table_prefix . "items WHERE manufacturer_code=" . $db->tosql($manufacturer_code, TEXT);
			$item_id = get_db_value($sql);
		}
		if ($item_id) {
			$_GET["item_id"] = $item_id;
		}
	}

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	if ($friendly_urls) {
		// retrieve info about friendly url
		$sql  = " SELECT friendly_url FROM " . $table_prefix . "items WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			$page_friendly_url = $db->f("friendly_url");
			friendly_url_redirect($page_friendly_url, $page_friendly_params);
		}
	}

	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main","product_details.html");
	$t->set_var("current_href", "product_details.php");
	$t->set_var("confirm_add", $confirm_add);

	include_once("./header.php");

	$html_title = ""; $meta_keywords = ""; $meta_description = "";
	if(is_array($page_settings)) {
		foreach($page_settings as $setting_name => $setting_value)
		{
			if ($setting_name == "top_products_block") {
				top_products($setting_value);
			} elseif ($setting_name == "offers_block") {
				include_once("./blocks/block_offers.php");
				offers($setting_value, $page_friendly_url, $page_friendly_params);
			} elseif ($setting_name == "products_latest") {
				include_once("./blocks/block_products_latest.php");
				products_latest($setting_value);
			} elseif ($setting_name == "products_top_viewed") {
				include_once("./blocks/block_products_top_viewed.php");
				products_top_viewed($setting_value);
			} elseif ($setting_name == "products_top_sellers") {
				products_top_sellers($setting_value);
			} elseif ($setting_name == "search_block") {
				search_form($setting_value);
			} elseif ($setting_name == "products_recently_viewed") {
				include_once("./blocks/block_products_recently.php");
				products_recently_viewed($setting_value);
			} elseif ($setting_name == "products_recommended") {
				include_once("./blocks/block_products_recommended.php");
				products_recommended($setting_value);
			} elseif ($setting_name == "login_block") {
				login_form($setting_value);
			} elseif ($setting_name == "cart_block") {
				small_cart($setting_value);
			} elseif ($setting_name == "coupon_form") {
				include_once("./blocks/block_coupon_form.php");
				coupon_form($setting_value);
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
				product_details($setting_value);
			} elseif ($setting_name == "related_block") {
				include_once("./blocks/block_products_related.php");
				related_products($setting_value, "product_related", $page_friendly_url, $page_friendly_params);
			} elseif ($setting_name == "articles_related_block") {
				include_once("./blocks/block_articles_related.php");
				related_articles($setting_value, "articles_items_related", $page_friendly_url, $page_friendly_params);
			} elseif ($setting_name == "forums_related_block") {
				include_once("./blocks/block_forums_related.php");
				related_forums($setting_value, "items_forum_topics", $page_friendly_url, $page_friendly_params);
			} elseif ($setting_name == "related_purchase") {
				include_once("./blocks/block_products_related_purchase.php");
				products_related_purchase($setting_value, $page_friendly_url, $page_friendly_params);
			} elseif ($setting_name == "users_bought_item") {
				include_once("./blocks/block_products_users_bought.php");
				products_users_bought($setting_value, $page_friendly_url, $page_friendly_params);
			} elseif ($setting_name == "products_fast_add") {
				include_once("./blocks/block_products_fast_add.php");
				products_fast_add_form($setting_value);
			} elseif ($setting_name == "subscribe_block") {
				include_once("./blocks/block_subscribe.php");
				subscribe_form($setting_value);
			} elseif ($setting_name == "sms_test_block") {
				include_once("./blocks/block_sms_test.php");
				sms_test_form($setting_value);
			} elseif ($setting_name == "poll_block") {
				poll_form($setting_value);
			} elseif ($setting_name == "language_block") {
				include_once("./blocks/block_language.php");
				language_form($setting_value, $page_settings["language_selection"], "", $page_friendly_url, $page_friendly_params);
			} elseif ($setting_name == "currency_block") {
				include_once("./blocks/block_currency.php");
				currency_form($setting_value);
			} elseif ($setting_name == "layouts_block") {
				include_once("./blocks/block_layouts.php");
				layouts($setting_value, "", $page_friendly_url, $page_friendly_params);
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

	if(!get_setting_value($page_settings, "left_column_hide", 0)) {
		$t->set_var("left_column_width", get_setting_value($page_settings, "left_column_width", "20%"));
		$t->parse("left_column", false);
	}
	if(!get_setting_value($page_settings, "middle_column_hide", 0)) {
		$t->set_var("middle_column_width", get_setting_value($page_settings, "middle_column_width", "60%"));
		$t->parse("middle_column", false);
	}
	if(!get_setting_value($page_settings, "right_column_hide", 0)) {
		$t->set_var("right_column_width", get_setting_value($page_settings, "right_column_width", "20%"));
		$t->parse("right_column", false);
	}

	prepare_saved_types();
	include_once("./footer.php");

	$t->set_var("html_title", $html_title);
	$t->set_var("meta_keywords", $meta_keywords);
	$t->set_var("meta_description", get_meta_desc($meta_description));
	$t->pparse("main");

?>