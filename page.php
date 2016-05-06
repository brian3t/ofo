<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  page.php                                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");
	include_once("./messages/" . $language_code . "/cart_messages.php");
	include_once("./messages/" . $language_code . "/forum_messages.php");
	include_once("./includes/products_functions.php");
	include_once("./includes/shopping_cart.php");
	include_once("./includes/ads_functions.php");
	include_once("./includes/navigator.php");
	include_once("./blocks/block_custom.php");

	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main", "page.html");
	include_once("./header.php");
	include_once("./footer.php");

	$page_code = get_param("page");
	$user_id = get_session("session_user_id");		
	$user_info = get_session("session_user_info");
	$user_type_id = get_setting_value($user_info, "user_type_id", "");
	
	$html_title = ""; $meta_description = ""; $meta_keywords = "";
	$page_friendly_url = ""; $page_friendly_params = array();
	if (strlen($page_code))
	{
		$sql  = " SELECT p.page_id, p.friendly_url, p.meta_title,p.meta_description,p.meta_keywords,p.is_html,p.page_type,p.page_url,p.page_path,";
		$sql .= " p.page_title,p.page_body FROM ";
		if (isset($site_id)) {
			$sql .= "(";
		}
		if (strlen($user_id)) {
			$sql .= "(";
		}
		$sql .= $table_prefix . "pages p ";
		if (isset($site_id)) {
			$sql .= " LEFT JOIN " . $table_prefix . "pages_sites ps ON ps.page_id=p.page_id) ";
		}
		if (strlen($user_id)) {
			$sql .= " LEFT JOIN " . $table_prefix . "pages_user_types ut ON ut.page_id=p.page_id) ";
		}
		$sql .= " WHERE p.is_showing=1 AND p.page_code=" . $db->tosql($page_code, TEXT);
		if (isset($site_id)) {
			$sql .= " AND (p.sites_all=1 OR ps.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
		} else {
			$sql .= " AND p.sites_all=1 ";					
		}		
		if (strlen($user_id)) {
			$sql .= " AND ( p.user_types_all=1 OR ut.user_type_id=". $db->tosql($user_type_id , INTEGER) . " )";
		} else {
			$sql .= " AND p.user_types_all=1 ";
		}
		$db->query($sql);
		if ($db->next_record())
		{
			$page_friendly_url = $db->f("friendly_url");
			if ($page_friendly_url) {
				$page_friendly_params[] = "page";
				friendly_url_redirect($page_friendly_url, $page_friendly_params);
			}
			// meta data
			$html_title = get_translation($db->f("meta_title"));
			$meta_description = get_translation($db->f("meta_description"));
			$meta_keywords = get_translation($db->f("meta_keywords"));

			$is_html = $db->f("is_html");
			$page_id = $db->f("page_id");
			$page_type = $db->f("page_type");
			$page_url = $db->f("page_url");
			$page_path = $db->f("page_path");
			if (strlen($page_url))
			{
				header("HTTP/1.0 302 OK");
				header("Status: 302 OK");
				header("Location: " . $page_url);
				exit;
			}
			$page_title = get_translation($db->f("page_title"));
			$page_title = get_currency_message($page_title, $currency);
			$page_body = get_translation($db->f("page_body"));
			$page_body = strlen($page_path) ? @join("", file($page_path)) : $page_body;
			$page_body = get_currency_message($page_body, $currency);
			if (get_setting_value($settings, "php_in_custom_pages", 0)) {
				eval_php_code($page_body);
			}

			if (!strlen($html_title)) {
				$html_title = $page_title;
			}
			if (!strlen($meta_description)) {
				$meta_description = $page_body;
			}
			$page_body = $is_html ? $page_body : "<div align=\"justify\">" . nl2br(htmlspecialchars($page_body)) . "</div>";

		}
		else
		{
			//$page_title = "Page Error";
			//$page_body = "<div align=\"center\"><font color=\"red\"><b>Page '" . htmlspecialchars($page_code) . "' was not found</b></font></div>";
			header ("Location: index.php");
			exit;
		}
	}
	else
	{
		header ("Location: index.php");
		exit;
	}

	if ($page_type == 2) { 
		$t->set_file("main", "page_popup.html");
		include_once("./header.php");
		$t->set_var("page_title", $page_title);
		$t->set_var("page_body", $page_body);			
		$t->pparse("main");
		return;
	}

	$custom_page_name = "custom_page_" . $page_id;
	$sql  = " SELECT setting_value FROM " . $table_prefix . "page_settings ";
	$sql .= " WHERE page_name=" . $db->tosql($custom_page_name, TEXT);
	$sql .= " AND setting_name='layout_type'";
	if ($site_id) {
		$sql .= " AND site_id=" . $db->tosql($site_id, INTEGER);
	} else {
		$sql .= " AND site_id=1 ";
	}
	$layout_type = get_db_value($sql);
	if ($layout_type == "custom") {
		$setting_type = $custom_page_name;
	} else {
		$setting_type = "custom_page";
	}
	$page_settings = va_page_settings($setting_type, 0);

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
			} elseif ($setting_name == "wishlist_items") {
				include_once("./blocks/block_wishlist_items.php");
				wishlist_items($setting_value);
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
				offers($setting_value, $page_friendly_url, $page_friendly_params);
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
			} elseif ($setting_name == "custom_page_body") {
				include_once("./blocks/block_custom_page_body.php");
				custom_page_body($setting_value, $page_title, $page_body);
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

	$t->set_var("html_title", $html_title);
	$t->set_var("meta_description", get_meta_desc($meta_description));
	$t->set_var("meta_keywords", $meta_keywords);
	$t->pparse("main");

?>