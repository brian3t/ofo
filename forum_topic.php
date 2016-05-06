<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  forum_topic.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");
	include_once("./includes/sorter.php");
	include_once("./includes/navigator.php");
	include_once("./includes/record.php");
	include_once("./includes/icons_functions.php");
	include_once("./includes/products_functions.php");
	include_once("./includes/shopping_cart.php");
	include_once("./includes/forums_functions.php");
	include_once("./messages/" . $language_code . "/cart_messages.php");
	include_once("./messages/" . $language_code . "/forum_messages.php");
	include_once("./messages/" . $language_code . "/reviews_messages.php");

	// include blocks
	include_once("./blocks/block_custom.php");
	include_once("./blocks/block_banners.php");

	$display_forums = get_setting_value($settings, "display_forums", 0);
	if ($display_forums == 1) {
		// user need to be logged in before viewing forum 
		check_user_session();
	}
	$user_id = get_session("session_user_id");
	$user_info = get_session("session_user_info");
	$user_type_id = get_setting_value($user_info, "user_type_id", "");
	
	$page_settings = va_page_settings("forum_topic", 0);
	$currency = get_currency();
	$thread_id = get_param("thread_id");

	$page_friendly_url = ""; 
	$page_friendly_params = array("thread_id");

	$forum_image = ""; $forum_description = "";
	$desc_image = get_setting_value($page_settings, "forum_description_image", 3);
	$desc_type = get_setting_value($page_settings, "forum_description_type", 2);
	
	// retrieve info about current forum
	// if forum is hidden, thread is hidden too
	$sql  = " SELECT fl.category_id, fl.forum_id, fl.forum_name,fl. short_description, fl.full_description, fl.small_image, fl.large_image ";
	$sql .= " FROM (" . $table_prefix . "forum_list fl";
	$sql .= " INNER JOIN " . $table_prefix . "forum f ON  f.forum_id=fl.forum_id)";
	$sql .= " WHERE f.thread_id=" . $db->tosql($thread_id, INTEGER);
		
	$db->query($sql);
	if ($db->next_record()) {
		$category_id = $db->f("category_id");
		$forum_id    = $db->f("forum_id");
		$forum_name  = get_translation($db->f("forum_name"));
		
		$page_friendly_url = $db->f("friendly_url");
		if ($desc_image == 3) {
			$forum_image = $db->f("large_image");
		} elseif ($desc_image == 2) {
			$forum_image = $db->f("small_image");
		}
		
		if ($desc_type == 2) {
			$forum_description = get_translation($db->f("full_description"));
		} elseif ($desc_type == 1) {
			$forum_description = get_translation($db->f("short_description"));
		}
		
		if (!VA_Forum_Categories::check_exists($category_id)) {
			header ("Location: " . get_custom_friendly_url("forums.php"));
			exit;
		}
			
		if (!VA_Forums::check_permissions($forum_id, VIEW_FORUM_PERM)) {
			header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
			exit;
		}
	} else {
		header ("Location: " . get_custom_friendly_url("forums.php"));
		exit;
	}
	
	if (!VA_Forums::check_permissions($forum_id, VIEW_TOPIC_PERM)) {
		header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
		exit;
	}

	// prepare icons to replace in the text
	prepare_icons($icons, $icons_codes, $icons_tags);

	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main","forum_topic.html");

	$html_title = ""; $meta_description = ""; $meta_keywords = ""; 
	$t->set_var("current_href", get_custom_friendly_url("forum_topic.php"));
	include_once("./header.php");
	if (is_array($page_settings)) {
		foreach($page_settings as $setting_name => $setting_value)
		{
			if ($setting_name == "top_products_block") {
				include_once("./blocks/block_top_products.php");
				top_products($setting_value);
			} elseif ($setting_name == "products_top_sellers") {
				include_once("./blocks/block_products_top_sellers.php");
				products_top_sellers($setting_value);
			} elseif ($setting_name == "search_block") {
				include_once("./blocks/block_search.php");
				search_form($setting_value);
			} elseif ($setting_name == "products_recently_viewed") {
				include_once("./blocks/block_products_recently.php");
				products_recently_viewed($setting_value);
			} elseif ($setting_name == "products_latest") {
				include_once("./blocks/block_products_latest.php");
				products_latest($setting_value);
			} elseif ($setting_name == "products_top_viewed") {
				include_once("./blocks/block_products_top_viewed.php");
				products_top_viewed($setting_value);
			} elseif ($setting_name == "login_block") {
				login_form($setting_value);
			} elseif ($setting_name == "cart_block") {
				include_once("./blocks/block_cart.php");
				small_cart($setting_value);
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
			} elseif ($setting_name == "forum_search_block") {
				include_once("./blocks/block_forum_search.php");
				forum_search($setting_value);
			} elseif ($setting_name == "forum_latest") {
				include_once("./blocks/block_forum_latest.php");
				forum_latest($setting_value);
			} elseif ($setting_name == "forum_top_viewed") {
				include_once("./blocks/block_forum_top_viewed.php");
				forum_top_viewed($setting_value);
			} elseif ($setting_name == "forum_breadcrumb") {
				include_once("./blocks/block_forum_breadcrumb.php");
				forum_breadcrumb($setting_value);
			} elseif ($setting_name == "forum_description") {
				include_once("./blocks/block_forum_description.php");
				forum_description($setting_value, $forum_id, $forum_name, $forum_description, $forum_image);
			} elseif ($setting_name == "forum_view_topic") {
				include_once("./blocks/block_forum_topic.php");
				forum_show_topic($setting_value, $forum_id, $page_friendly_url, $page_friendly_params);
			} elseif ($setting_name == "forum_item_related_block") {
				include_once("./blocks/block_products_related.php");
				related_products($setting_value, "items_forum_topics", $page_friendly_url, $page_friendly_params);
			} elseif ($setting_name == "forum_articles_related_block") {
				include_once("./blocks/block_articles_related.php");
				related_articles($setting_value, "articles_forum_topics", $page_friendly_url, $page_friendly_params);
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

	$t->set_var("html_title", $html_title);
	$t->set_var("meta_keywords", $meta_keywords);
	$t->set_var("meta_description", get_meta_desc($meta_description));
	$t->pparse("main");
	
?>