<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_layout_page.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once("../messages/" . $language_code . "/cart_messages.php");
	include_once("../messages/" . $language_code . "/forum_messages.php");
	include_once("./admin_common.php");

	check_admin_security("cms_settings");

	$param_site_id = get_session("session_site_id");
	$va_license_code = va_license_code();
	$layout_id = get_param("layout_id");
	if (!strlen($layout_id))  { $layout_id = 0; }
	$art_cat_id = get_param("art_cat_id");
	$page_name = get_param("page_name");
	$layout_name = "";	$art_cat_name = "";	$index_page = ""; $global_page = ""; $products_page = ""; $basket_page = ""; $user_page = ""; $ads_page = ""; $forums_page = "";
	$is_layout = false;

	if (strlen($art_cat_id)) {
		$sql = "SELECT category_name FROM " . $table_prefix . "articles_categories WHERE category_id=" . $db->tosql($art_cat_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$art_cat_name = get_translation($db->f("category_name"));
			$is_layout = true;
		}
	} elseif ($page_name == "index") {
		$index_page = USER_HOME_TITLE;
		$is_layout = true;
	} elseif ($page_name == "products_list") {
		$products_page = PRODUCTS_LISTING_PAGE_MSG;
		$is_layout = true;
	} elseif (preg_match("/^products_list_(\d+)$/", $page_name, $matches)) {
		$category_id = $matches[1];
		$sql = "SELECT category_name FROM " . $table_prefix . "categories WHERE category_id=" . $db->tosql($category_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$page_title = get_translation($db->f("category_name"));
			$global_page = $page_title;
			$is_layout = true;
		}
	} elseif ($page_name == "products_details") {
		$products_page = PRODUCTS_DETAILS_PAGE_MSG;
		$is_layout = true;
	} elseif ($page_name == "products_options") {
		$products_page = str_replace("{page_title}", PRODUCT_OPTIONS_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "products_search") {
		$products_page = str_replace("{page_title}", PRODUCTS_SEARCH_RESULTS_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "products_advanced_search") {
		$products_page = str_replace("{page_title}", PRODUCTS_SEARCH_ADVANCED_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "products_compare") {
		$products_page = str_replace("{page_title}", PRODUCTS_COMPARE_RESULTS_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "products_releases") {
		$products_page = str_replace("{page_title}", RELEASES_TITLE, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "products_changes_log") {
		$products_page = str_replace("{page_title}", CHANGES_LOG_TITLE, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "basket") {
		$basket_page = str_replace("{page_title}", ADMIN_BASKET_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "cart_save") {
		$basket_page = str_replace("{page_title}", SAVE_CART_BUTTON, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "cart_retrieve") {
		$basket_page = str_replace("{page_title}", RETRIEVE_CART_BUTTON, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "user_list") {
		$user_page = USER_LISTING_PAGE_MSG;
		$is_layout = true;
	} elseif ($page_name == "userhome_pages") {
		$user_page = DEFAULT_USERHOME_PAGE_MSG;
		$is_layout = true;
	} elseif ($page_name == "ads_list") {
		$ads_page = str_replace("{page_title}", ADS_LISTING_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "ads_details") {
		$ads_page = str_replace("{page_title}", ADS_DETAILS_SETTINGS_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "ads_compare") {
		$ads_page = str_replace("{page_title}", ADS_COMPARE_RESULTS_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "ads_search") {
		$ads_page = str_replace("{page_title}", ADS_ADVANCED_SEARCH_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "manuals_list") {
		$manual_page = MANUAL_LIST_MSG;
		$is_layout = true;
	} elseif ($page_name == "manuals_articles") {
		$manual_page = MANUAL_ARTICLES_MSG;
		$is_layout = true;
	} elseif ($page_name == "manuals_article_details") {
		$manual_page = MANUAL_ARTICLES_DETAILS_MSG;
		$is_layout = true;
	} elseif ($page_name == "manuals_search") {
		$manual_page = MANUAL_SEARCH_RESULTS_MSG;
		$is_layout = true;
	} elseif ($page_name == "support_new") {
		$support_page = NEW_TICKET_PAGE_MSG;
		$is_layout = true;
	} elseif ($page_name == "support_reply") {
		$support_page = str_replace("{page_title}", REPLYING_TICKETS_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	}	elseif ($page_name == "forum_list") {
		$forums_page = FORUMS_LIST_MSG;
		$is_layout = true;
	} elseif ($page_name == "forum_topics") {
		$forums_page = FORUM_TOPICS_MSG;
		$is_layout = true;
	} elseif ($page_name == "forum_topic") {
		$forums_page = FORUM_TOPICS_THREAD_MSG;
		$is_layout = true;
	} elseif ($page_name == "site_map") {
		$global_page = str_replace("{page_title}", SITE_MAP_TITLE, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "subscriptions") {
		$global_page = str_replace("{page_title}", SUBSCRIPTIONS_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "custom_page") {
		$global_page = CUSTOM_PAGE_LAYOUT_MSG;
		$is_layout = true;
	} elseif (preg_match("/^custom_page_(\d+)$/", $page_name, $matches)) {
		$page_id = $matches[1];
		$sql = "SELECT page_title FROM " . $table_prefix . "pages WHERE page_id=" . $db->tosql($page_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$page_title = get_translation($db->f("page_title"));
			$global_page = $page_title;
			$is_layout = true;
		}
	} elseif ($page_name == "wishlist") {
		$products_page = WISHLIST_MSG;
		$is_layout = true;
	} elseif ($page_name == "site_search") {
		$global_page = FULL_SITE_SEARCH_MSG;
		$is_layout = true;
	} elseif ($page_name == "user_login") {
		$global_page = str_replace("{page_title}", LOGIN_TITLE, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "user_profile") {
		$global_page = str_replace("{page_title}", CUSTOMER_PROFILE_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "forgot_password") {
		$global_page = str_replace("{page_title}", FORGOTTEN_PASSWORD_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "reset_password") {
		$global_page = str_replace("{page_title}", RESET_PASSWORD_INFO_MSG, PAGE_TITLE_PAGE_MSG);
		$is_layout = true;
	} elseif ($page_name == "polls_previous") {
		$global_page = str_replace("{page_title}", PREVIOUS_POLLS_MSG, PAGE_TITLE_PAGE_MSG);

		$is_layout = true;
	} elseif ($page_name == "checkout_login") {
		$global_page = str_replace("{page_title}", CHECKOUT_LOGIN_TITLE, PAGE_TITLE_PAGE_MSG);

		$is_layout = true;
	} elseif ($page_name == "order_info") {
		$global_page = str_replace("{page_title}", CHECKOUT_PERSONAL_DELIVERY_DATA_MSG, PAGE_TITLE_PAGE_MSG);

		$is_layout = true;
	} elseif ($page_name == "order_payment_details") {
		$global_page = PAYMENT_DETAILS_PAGE_MSG;
		$is_layout = true;
	} elseif ($page_name == "order_confirmation") {
		$global_page = ORDER_CONFIRMATION_PAGE_MSG;
		$is_layout = true;
	} elseif ($page_name == "order_final") {
		$global_page = FINAL_CHECKOUT_PAGE_MSG;
		$is_layout = true;
	} elseif ($layout_id) {
		$sql = "SELECT layout_name FROM " . $table_prefix . "layouts WHERE layout_id=" . $db->tosql($layout_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$layout_name = $db->f("layout_name");
			$is_layout = true;
		}
	}

	$rp = get_param("rp");
	if (strlen($rp)) {
		$return_page = $rp;
	} else {
		$return_page = "admin_cms.php";
	}

	if (!$is_layout) {
		header("Location: " . $return_page);
		exit;
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_layout_page.html");

	$t->set_var("admin_href",              "admin.php");
	$t->set_var("admin_layouts_href",      "admin_layouts.php");
	$t->set_var("admin_cms_href",          "admin_cms.php");
	$t->set_var("admin_layout_href",       "admin_layout.php");
	$t->set_var("admin_layout_page_href",  "admin_layout_page.php");
	$t->set_var("admin_items_list_href",   "admin_items_list.php");
	$t->set_var("admin_ads_href",          "admin_ads.php");
	$t->set_var("admin_articles_href",     "admin_articles.php");
	$t->set_var("admin_articles_top_href", "admin_articles_top.php");
	$t->set_var("show_add_message", str_replace("{button_name}", ADD_TO_CART_MSG, SHOW_BUTTON_MSG));
	$t->set_var("show_view_message", str_replace("{button_name}", VIEW_CART_MSG, SHOW_BUTTON_MSG));
	$t->set_var("show_goto_message", str_replace("{button_name}", GOTO_CHECKOUT_MSG, SHOW_BUTTON_MSG));
	$t->set_var("show_wish_message", str_replace("{button_name}", ADD_TO_WISHLIST_MSG, SHOW_BUTTON_MSG));

	$layout_types =
		array(
			array("default", DEFAULT_PAGE_LAYOUT_MSG),
			array("custom", CUSTOM_PAGE_LAYOUT_MSG),
		);

	$categories_layout_types =
		array(
			array("default", DEFAULT_PAGE_LAYOUT_MSG),
			array("category", OVERRIDE_CATEGORY_LAYOUT_MSG),
			array("all", OVERRIDE_ALL_CATEGORIES_LAYOUT_MSG),
		);

	$r = new VA_Record($table_prefix . "page_settings");

	if (preg_match("/^custom_page_(\d+)$/", $page_name, $matches)) {
		$r->add_select("layout_type", TEXT, $layout_types, LAYOUT_TYPE_MSG);
	} else if (preg_match("/^products_list_(\d+)$/", $page_name, $matches)) {
		$r->add_select("layout_type", TEXT, $categories_layout_types, LAYOUT_TYPE_MSG);
	}

	$r->add_textbox("left_column_width", TEXT, LEFT_COLUMN_WIDTH_MSG);
	$r->add_textbox("middle_column_width", TEXT, MIDDLE_COLUMN_WIDTH_MSG);
	$r->add_textbox("right_column_width", TEXT, RIGHT_COLUMN_WIDTH_MSG);
	$r->add_checkbox("left_column_hide", TEXT);
	$r->add_checkbox("middle_column_hide", TEXT);
	$r->add_checkbox("right_column_hide", TEXT);

	$columns_values =
		array(
			array(1, 1), array(2, 2), array(3, 3),
			array(4, 4), array(5, 5), array(6, 6)
		);

	$categories_types =
		array(
			array(3, MULTILEVEL_LIST_MSG),
			array(4, TREETYPE_STRUCTURE_MSG)
		);

	$subcategories_types =
		array(
			array(1, ONELEVEL_LIST_MSG),
			array(2, TWOLEVEL_LIST_MSG),
		);

	$categories_images =
		array(
			array(1, DEFAULT_IMAGE_MSG . "(tree_top.gif)"),
			array(2, IMAGE_SMALL_MSG),
			array(3, IMAGE_LARGE_MSG),
		);

	$subcategories_images =
		array(
			array(1, DEFAULT_IMAGE_MSG . "(category_image.gif)"),
			array(2, IMAGE_SMALL_MSG),
			array(3, IMAGE_LARGE_MSG),
		);

	$prod_image_types =
		array(
			array(0, DONT_SHOW_IMAGE_MSG),
			array(1, IMAGE_TINY_MSG),
			array(2, IMAGE_SMALL_MSG),
			array(3, IMAGE_LARGE_MSG)
		);
		
	$prod_desc_types =
		array(
			array(0, DONT_SHOW_DESC_MSG),
			array(1, SHORT_DESCRIPTION_MSG),
			array(2, FULL_DESCRIPTION_MSG),
			array(3, HIGHLIGHTS_MSG),
			array(4, SPECIAL_OFFER_MSG),
		);

	$prod_order_types =
		array(			
			array(0, PROD_ISSUE_DATE_MSG),
			array(1, DATE_ADDED_MSG),
			array(2, DATE_MSG . " " .ADMIN_MODIFIED_MSG)
		);
		
	$cat_desc_types =
		array(
			array(0, DONT_SHOW_DESC_MSG),
			array(1, SHORT_DESCRIPTION_MSG),
			array(2, FULL_DESCRIPTION_MSG),
		);

	$cat_desc_images =
		array(
			array(0, DONT_SHOW_IMAGE_MSG),
			array(2, IMAGE_SMALL_MSG),
			array(3, IMAGE_LARGE_MSG)
		);

	$man_desc_types =
		array(
			array(0, DONT_SHOW_DESC_MSG),
			array(1, SHORT_DESCRIPTION_MSG),
			array(2, FULL_DESCRIPTION_MSG),
		);

	$man_desc_images =
		array(
			array(0, DONT_SHOW_IMAGE_MSG),
			array(2, IMAGE_SMALL_MSG),
			array(3, IMAGE_LARGE_MSG)
		);

	$articles_image_types =
		array(
			array(0, DONT_SHOW_IMAGE_MSG),
			array(2, IMAGE_SMALL_MSG),
			array(3, IMAGE_LARGE_MSG)
		);

	$articles_desc_types =
		array(
			array(0, DONT_SHOW_DESC_MSG),
			array(1, SHORT_DESCRIPTION_MSG),
			array(2, FULL_DESCRIPTION_MSG),
			array(3, HOT_DESCRIPTION_MSG)
		);
	
	$articles_date_types =
		array(
			array(0, DONT_SHOW_MSG),
			array(1, DATE_MSG),
			array(2, DATE_END_MSG)
		);

	$subs =
		array(
			array("0", ALL_MSG), array(1, 1), array(2, 2),
			array(3, 3), array(4, 4), array(5, 5), array(6, 6),
			array(7, 7), array(8, 8), array(9, 9), array(10, 10)
		);

	$records_per_page =
		array(
			array(1, 1), array(2, 2), array(3, 3), array(4, 4),
			array(5, 5), array(6, 6), array(8, 8), array(9, 9),
			array(10, 10), array(12, 12), array(15, 15), array(16, 16),
			array(20, 20), array(25, 25), array(30, 30), array(50, 50),
			array(75, 75), array(100, 100)
		);

	$top_records =
		array(
			array(1, 1), array(2, 2), array(3, 3), array(4, 4),
			array(5, 5), array(6, 6), array(7, 7), array(8, 8),
			array(9, 9), array(10, 10)
		);

	$use_tabs =
		array(
			array(1, ON_DIFFERENT_PAGES_MSG),
			array(0, ON_ONE_PAGE_MSG)
		);

	$language_selection =
		array(
			array(1, SHOW_IMAGES_LANGUAGE_MSG),
			array(2, SHOW_LISTBOX_LANGUAGE_MSG)
		);

	$currency_selection =
		array(
			array(1, SHOW_IMAGES_CURRENCY_MSG),
			array(2, SHOW_LISTBOX_CURRENCY_MSG)
		);

	$layouts_selection =
		array(
			array(1, SHOW_LAYOUTS_PER_ROW_MSG),
			array(2, USE_LISTBOX_LAYOUTS_MSG)
		);

	$manufacturers_selection =
		array(
			array(1, SHOW_MANUFACTURERS_MSG),
			array(2, USE_LISTBOX_MANUFACTURERS_MSG)
		);
	$manufacturers_direction_types =
		array(
			array(1, ASC_MSG),
			array(2, DESC_MSG)
		);
	$manufacturers_order_types =
		array(
			array(1, MANUFACTURER_NAME_MSG),
			array(2, MANUFACTURER_ORDER_MSG)
		);

	$merchants_selection =
		array(
			array(1, SHOW_MERCHANTS_PER_ROW_MSG),
			array(2, USE_LISTBOX_MERCHANTS_MSG)
		);

	$period_days =
		array(
			array(1,  DAY_MSG),
			array(7,  WEEK_MSG),
			array(14, str_replace("{quantity}", "2", WEEKS_QTY_MSG)),
			array(30, MONTH_MSG),
			array(60, str_replace("{quantity}", "2", MONTHS_QTY_MSG)),
			array(91, str_replace("{quantity}", "3", MONTHS_QTY_MSG)),
			array(182, str_replace("{quantity}", "6", MONTHS_QTY_MSG)),
			array(365, YEAR_MSG)
		);

	$group_by_categories =
		array(
			array(0, NO_GROUPING_MSG),
			array(1, TOP_CATEGORIES_ONLY_MSG),
			array(2, AVAILABLE_CATEGORIES_MSG),
			array(3, SELECTED_CATEGORIES_MSG)
		);

	$navigator_types =
		array(
			array(1, SIMPLE_CURRENT_PAGE_ONLY_MSG),
			array(2, CURRENT_PAGE_SHOWN_MSG),
			array(3, NAVIGATOR_SPLITS_MSG),
			array(4, SHOWS_LINKS_PAGES_MSG)
		);

	$navigation_visible_depth_levels =
		array(
			array(0, ALL_MSG),
			array(1, "1"),
			array(2, "2"),
			array(3, "3"),
			array(4, "4"),
			array(5, "5"),
			array(6, "6"),
			array(7, "7")
		);

	$products_view_types =
		array(
			array("list", DETAILED_LISTING_MSG),
			array("table", TABLE_VIEW_MSG),
		);

	$shopping_cart_preview = //
		array(
			array(0, DONT_SHOW_IMAGE_MSG),
			array(1, IMAGE_TINY_MSG),
			array(2, IMAGE_SMALL_MSG),
			array(3, IMAGE_LARGE_MSG)
		);		//

	/*$details_block_image = //
		array(
			array(1, DONT_SHOW_MANUFACTURERS_IMAGES_MSG),
			array(2, SHOW_MANUFACTURERS_IMAGES_MSG),
		);*/

	$details_manufacturer_image =
	  	array(
			array(0, DONT_SHOW_IMAGE_MSG),
			array(2, IMAGE_SMALL_MSG),
			array(3, IMAGE_LARGE_MSG)
		);
		
	$forum_thread_descriptions =
		array(
			array(0, DONT_SHOW_DESC_MSG),
			array(2, FULL_DESCRIPTION_MSG),
			array(3, LAST_POST_ADDED_MSG)			
		);
	
	$quantity_controls =
		array(
			array("NONE",    NONE_MSG),
			array("LABEL",   LABEL_MSG),
			array("LISTBOX", LISTBOX_MSG),
			array("TEXTBOX", TEXTBOX_MSG)
			);

	if ($va_license_code & 1) {
		$order_statuses = array();
		$order_statuses[] = array("ANY", ANY_STATUS_MSG);
		$order_statuses[] = array("PAID", ANY_PAID_STATUS_MSG);
		$sql = "SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE is_active=1 ORDER BY status_order, status_id";
		$order_statuses = get_db_values($sql, $order_statuses);
	}

	$user_types = array(); $block_user_types = array(); $user_bought_types = array();
	$user_types[] = array("", ANY_MSG);
	$block_user_types[] = array("", "");
	$block_user_types[] = array("NON", NON_REGISTERED_USERS_MSG);
	$block_user_types[] = array("ANY", ANY_REGISTERED_USERS_MSG);
	$user_bought_types[] = array("ALL", ALL_USERS_INCLUDING_UNREGISTERED_MSG);
	$user_bought_types[] = array("NON", UNREGISTERED_USER_ONLY_MSG);
	$user_bought_types[] = array("REG", ANY_REGISTERED_USERS_MSG);

	$sql = "SELECT type_id, type_name FROM " . $table_prefix . "user_types ";
	$db->query($sql);
	while ($db->next_record()) {
		$type_id = $db->f("type_id");
		$type_name = $db->f("type_name");
		$user_types[] = array($type_id, $type_name);
		$block_user_types[] = array($type_id, $type_name);
		$user_bought_types[] = array($type_id, $type_name);
	}

	$admin_types = get_db_values("SELECT privilege_id, privilege_name FROM " . $table_prefix . "admin_privileges", array(array("", ""), array("ANY", ANY_REGISTERED_ADMIN_MSG)));

	$ps = array(); $ps_checkboxes = array();
	$ps["category_description_type"] = 2;
	$ps["category_description_image"] = 3;
	$ps["manufacturer_info_type"] = 2;
	$ps["manufacturer_info_image"] = 3;
	$ps["forum_description_type"] = 2;
	$ps["forum_description_image"] = 3;
	$ps["forum_item_related_columns"] = 1;
	$ps["forum_item_related_per_page"] = 4;
	$ps["forum_articles_related_columns"] = 1;
	$ps["forum_articles_related_per_page"] = 4;	
	$ps["forum_articles_related_image"] = 0;
	$ps["forum_articles_related_desc"] = 0;
	$ps["forum_articles_related_date"] = 0;

	$ps["categories_type"] = 1;
	$ps["categories_image"] = 1;
	$ps["subcategories_image"] = 1;
	$ps["categories_columns"] = 2;
	$ps["categories_subs"] = 0;
	$ps["subcategories_type"] = 1;
	$ps["subcategories_columns"] = 2;
	$ps["subcategories_subs"] = 0;
	$ps["products_recent_records"] = 5;
	$ps["products_recent_cols"] = 1;
	$ps["recent_image"] = 0;
	$ps["recent_desc"] = 0;
	$ps["bestsellers_records"] = 10;
	$ps["bestsellers_days"] = 7;
	$ps["bestsellers_status"] = "PAID";
	$ps["bestsellers_image"] = 0;
	$ps["details_manufacturer_image"] = 0;
	$ps["bestsellers_desc"] = 0;
	$ps["top_rated_image"] = 0;
	$ps["top_rated_desc"] = 0;
	$ps["top_viewed_image"] = 0;
	$ps["top_viewed_desc"] = 0;
	$ps["prod_latest_image"] = 0;
	$ps["prod_latest_desc"] = 0;
	$ps["prod_latest_order"] = 0;
	$ps["products_latest_cols"] = 1;
	$ps["products_latest_recs"] = 10;
	$ps["prod_top_viewed_cols"] = 1;
	$ps["prod_top_viewed_recs"] = 10;
	$ps["language_selection"] = 1;
	$ps["layouts_selection"] = 1;
	$ps["currency_selection"] = 1;
	$ps["manufacturers_selection"] = 1;
	$ps["manufacturers_image"] = 0;
	$ps["manufacturers_desc"] = 0;
	$ps["manufacturers_order"] = 1;
	$ps["manufacturers_direction"] = 1;
	$ps["merchants_selection"] = 1;
	$ps["wl_image"] = 1;
	$ps["wl_recs"] = 10;
	if ($page_name == "index" || preg_match("/^products_list/", $page_name) || $page_name == "products_search" || $page_name == "user_list" || $page_name == "basket") {
		$ps["products_columns"] = 1;
		$ps["products_per_page"] = 10;
		$ps["products_nav_type"] = 1;
		$ps["products_default_view"] = "list";
		$ps["products_nav_first_last"] = 0;
		$ps["products_nav_prev_next"] = 1;
		$ps_checkboxes[] = "products_nav_first_last";
		$ps_checkboxes[] = "products_nav_prev_next";
		$ps["products_nav_pages"] = 5;
		$ps["products_sortings"] = 1;
		$ps["products_group_by_cats"] = 0;
		$ps_checkboxes[] = "products_sortings";
		$ps_checkboxes[] = "products_group_by_cats";

		$ps["prod_recom_cols"] = 1;
		$ps["prod_recom_per_page"] = 10;
		$ps["basket_prod_recom_cols"] = 1; //
		$ps["basket_prod_recom_per_page"] = 10;	//

		// bakset block settings
  	$ps["shopping_cart_preview"] = 1;
  	$ps["fast_checkout_country_show"] = 0;
  	$ps["fast_checkout_country_required"] = 0;
  	$ps["fast_checkout_state_show"] = 0;
  	$ps["fast_checkout_state_required"] = 0;
  	$ps["fast_checkout_postcode_show"] = 0;
  	$ps["fast_checkout_postcode_required"] = 0;

	} elseif ($page_name == "products_details") {
  		$ps["details_manufacturer_image"] = 2;//
		//$ps_checkboxes[] = "details_block_image";

		$ps["related_columns"] = 1;
		$ps["related_per_page"] = 4;
		$ps["use_tabs"] = 0;
		$ps["prod_recom_cols"] = 1;
		$ps["prod_recom_per_page"] = 10;
		$ps["basket_prod_recom_cols"] = 1; 
		$ps["basket_prod_recom_per_page"] = 10;	
		// related purchase settings
		$ps["related_purchase_recs"] = 4;
		$ps["related_purchase_cols"] = 1;
		$ps["related_purchase_days"] = 30;
		$ps["related_purchase_status"] = "PAID";
		$ps["related_purchase_image"] = 2;
		$ps["related_purchase_desc"] = 0;

		// users bought item settings
		$ps["users_bought_item_recs"] = 10;
		$ps["users_bought_item_cols"] = 1;
		$ps["users_bought_item_days"] = 30;
		$ps["users_bought_status"] = "PAID";
		$ps["users_bought_type"] = 2;

		$ps["users_bought_item_fn"] = 0;
		$ps["users_bought_item_ln"] = 0;
		$ps["users_bought_item_name"] = 0;
		$ps["users_bought_item_nickname"] = 1;
		$ps["users_bought_item_cn"] = 0;
		$ps["users_bought_item_email"] = 0;
		$ps["users_bought_item_country"] = 1;
		$ps["users_bought_item_state"] = 1;
		$ps["users_bought_item_od"] = 1;

		$ps_checkboxes[] ="users_bought_item_fn";
		$ps_checkboxes[] ="users_bought_item_ln";
		$ps_checkboxes[] ="users_bought_item_name";
		$ps_checkboxes[] ="users_bought_item_nickname";
		$ps_checkboxes[] ="users_bought_item_cn";
		$ps_checkboxes[] ="users_bought_item_email";
		$ps_checkboxes[] ="users_bought_item_country";
		$ps_checkboxes[] ="users_bought_item_state";
		$ps_checkboxes[] ="users_bought_item_od";

		$ps["forums_related_columns"] = 1;
		$ps["forums_related_per_page"] = 4;
		$ps["forums_related_desc"] = 0;
		$ps["forums_related_user_info"] = 1;
		
		$ps["articles_related_columns"]  = 1;
		$ps["articles_related_per_page"] = 4;
		$ps["articles_related_image"] = 0;
		$ps["articles_related_desc"] = 0;
		$ps["articles_related_date"] = 0;
					
		$ps_checkboxes[] = "forums_related_user_info";
	}
		
	// forum default settings
	$ps["forum_latest_cols"] = 1;
	$ps["forum_latest_recs"] = 10;
	$ps["forum_top_viewed_cols"] = 1;
	$ps["forum_top_viewed_recs"] = 10;

	// ads default settings
	$ps["ads_categories_type"] = 1;
	$ps["ads_categories_image"] = 1;
	$ps["ads_subcategories_type"] = 1;
	$ps["ads_subcategories_columns"] = 2;
	$ps["ads_subcategories_subs"] = 0;
	$ps["ads_cat_desc_type"] = 2;
	$ps["ads_cat_desc_image"] = 3;
	$ps["ads_recent_records"] = 5;
	$ps["ads_hot_recs"] = 10;
	$ps["ads_hot_cols"] = 1;
	$ps["ads_special_recs"] = 10;
	$ps["ads_special_cols"] = 1;
	$ps["ads_list_per_page"] = 10;
	$ps["ads_list_columns"] = 1;
	$ps["ads_details_tabs"] = 0;
	$ps["ads_user_type_id"] = "";
	$ps["ads_latest_cols"] = 1;
	$ps["ads_latest_recs"] = 10;
	$ps["ads_top_viewed_cols"] = 1;
	$ps["ads_top_viewed_recs"] = 10;

	// SMS block default settings
	$ps["sms_originator"] = "";
	$ps["sms_test_message"] = SMS_TEST_MESSAGE_MSG;

	if ($page_name == "index") {
		$blocks["manuals_search"] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", ADMIN_MANUAL_MSG, MODULE_NAME_BLOCK_MSG)));
		
		$blocks["support_block"] =  str_replace("{block_name}", SUBMIT_TICKET_MSG, (str_replace("{module_name}", HELPDESK_MSG, MODULE_NAME_BLOCK_MSG)));//str_replace("{block_name}", SUBMIT_TICKET_MSG, (str_replace("{module_name}", HELPDESK_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_search_block"] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_latest"] = str_replace("{block_name}", LATEST_TITLE, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_top_viewed"] = str_replace("{block_name}", TOP_VIEWED_TITLE, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));

		$blocks["ads_search"] = str_replace("{block_name}", SEARCH_FORM_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_hot"] = str_replace("{block_name}", HOT_OFFERS_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG))); 
		$blocks["ads_special"] = str_replace("{block_name}", SPECIAL_OFFER_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG))); 
		$blocks["ads_recently_viewed"] = str_replace("{block_name}", RECENTLY_VIEWED_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG))); 
		$blocks["ads_categories"] = str_replace("{block_name}", CATEGORIES_LIST_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));  
		$blocks["ads_subcategories"] = str_replace("{block_name}", SUBCATEGORIES_LIST_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_sellers"] = str_replace("{block_name}", SELLERS_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_latest"] = str_replace("{block_name}", LATEST_TITLE, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_top_viewed"] = str_replace("{block_name}", TOP_VIEWED_TITLE, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
	} elseif (preg_match("/^products_list/", $page_name) || $page_name == "user_list" || $page_name == "products_search") {
		if ($va_license_code & 1) {
			$blocks["products_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			if ($page_name == "user_list") {
				$blocks["merchant_info_block"] = str_replace("{block_name}", MERCHANT_INFO_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
				$blocks["merchant_contact_block"] = CONTACT_MERCHANT_TITLE;
			} else {
				$blocks["category_description_block"] = str_replace("{block_name}", CATEGORY_INFO_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
				$blocks["manufacturer_info_block"] = str_replace("{block_name}", MANUFACTURER_INFO_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			}
			$blocks["products_block"] = PRODUCTS_LISTING_PAGE_MSG;
		}
	} elseif ($page_name == "products_details") {
		if ($va_license_code & 1) {
			$blocks["products_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["category_description_block"] = str_replace("{block_name}", CATEGORY_INFO_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["details_block"] = PRODUCT_DETAILS_MSG;
			$blocks["related_block"] = str_replace("{block_name}", RELATED_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["forums_related_block"] = str_replace("{block_name}", RELATED_FORUMS_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["articles_related_block"] = str_replace("{block_name}", RELATED_ARTICLES_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["related_purchase"] = str_replace("{block_name}", WHO_BOUGHT_THIS_SHORT_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["users_bought_item"] = str_replace("{block_name}", CUSTOMERS_LIST_BOUGHT_ITEM_TITLE, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
		}
	} elseif ($page_name == "products_options") {
		$blocks["products_options"] = str_replace("{block_name}", OPTIONS_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "products_advanced_search") {
		$blocks["products_advanced_search"] = str_replace("{block_name}", ADVANCED_SEARCH_TITLE, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "products_compare") {
		$blocks["products_compare"] = str_replace("{block_name}", PRODUCTS_COMPARE_RESULTS_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "products_releases") {
		$blocks["products_releases"] = str_replace("{block_name}", RELEASES_TITLE, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "products_changes_log") {
		$blocks["products_changes_log"] = str_replace("{block_name}", CHANGES_LOG_TITLE, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "basket") {
		if ($va_license_code & 1) {
			$blocks["basket_block"] = CART_TITLE;
			$blocks["basket_recommended_block"] = str_replace("{block_name}", PRODUCTS_RECOMMENDED_TITLE, (str_replace("{module_name}", CART_TITLE, MODULE_NAME_BLOCK_MSG)));
		}
		$blocks["support_block"] = str_replace("{block_name}", SUBMIT_TICKET_MSG, (str_replace("{module_name}", HELPDESK_MSG, MODULE_NAME_BLOCK_MSG)));

		$blocks["forum_search_block"] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", HELPDESK_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_latest"] = str_replace("{block_name}", LATEST_TITLE, (str_replace("{module_name}", HELPDESK_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_top_viewed"] = str_replace("{block_name}", TOP_VIEWED_TITLE, (str_replace("{module_name}", HELPDESK_MSG, MODULE_NAME_BLOCK_MSG)));

		$blocks["ads_search"] = str_replace("{block_name}", SEARCH_FORM_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_hot"] = str_replace("{block_name}", HOT_OFFERS_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));		
		$blocks["ads_special"] = str_replace("{block_name}", SPECIAL_OFFER_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG))); 
		$blocks["ads_recently_viewed"] = str_replace("{block_name}", RECENTLY_VIEWED_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_categories"] = str_replace("{block_name}", CATEGORIES_LIST_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_subcategories"] = str_replace("{block_name}", SUBCATEGORIES_LIST_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_sellers"] = str_replace("{block_name}", SELLERS_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_latest"] = str_replace("{block_name}", LATEST_TITLE, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_top_viewed"] = str_replace("{block_name}", TOP_VIEWED_TITLE, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "cart_save") {
		$blocks["cart_save"] = SAVE_CART_BUTTON;
	} elseif ($page_name == "cart_retrieve") {
		$blocks["cart_retrieve"] = RETRIEVE_CART_BUTTON;
	} elseif ($page_name == "subscriptions") {
		$blocks["subscriptions"] = SUBSCRIPTIONS_MSG;
		$blocks["subscriptions_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", SUBSCRIPTIONS_MSG, MODULE_NAME_BLOCK_MSG)));
	
	} elseif ($page_name == "ads_list") {
		if ($va_license_code & 1) {
			$blocks["cart_block"] = SMALL_CART_MSG;
		}

		$blocks["ads_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_search"] = str_replace("{block_name}", SEARCH_FORM_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_hot"] = str_replace("{block_name}", HOT_OFFERS_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_special"] = str_replace("{block_name}", SPECIAL_OFFER_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG))); 
		$blocks["ads_recently_viewed"] = str_replace("{block_name}", RECENTLY_VIEWED_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_category_info"] = str_replace("{block_name}", CATEGORY_INFO_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_categories"] = str_replace("{block_name}", CATEGORIES_LIST_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_subcategories"] = str_replace("{block_name}", SUBCATEGORIES_LIST_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_sellers"] = str_replace("{block_name}", SELLERS_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_latest"] = str_replace("{block_name}", LATEST_TITLE, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_top_viewed"] = str_replace("{block_name}", TOP_VIEWED_TITLE, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_list"] = ADS_LISTING_MSG;
		$blocks["ads_add"]  = NEW_AD_MSG;

	} elseif ($page_name == "ads_details") {
		if ($va_license_code & 1) {
			$blocks["cart_block"] = SMALL_CART_MSG;
		}

		$blocks["ads_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_search"] = str_replace("{block_name}", SEARCH_FORM_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_hot"] = str_replace("{block_name}", HOT_OFFERS_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_special"] = str_replace("{block_name}", SPECIAL_OFFER_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG))); 
		$blocks["ads_recently_viewed"] = str_replace("{block_name}", RECENTLY_VIEWED_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_category_info"] = str_replace("{block_name}", CATEGORY_INFO_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_categories"] = str_replace("{block_name}", CATEGORIES_LIST_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_subcategories"] = str_replace("{block_name}", SUBCATEGORIES_LIST_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_sellers"] = str_replace("{block_name}", SELLERS_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_latest"] = str_replace("{block_name}", LATEST_TITLE, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_top_viewed"] = str_replace("{block_name}", TOP_VIEWED_TITLE, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["ads_details"] = ADS_DETAILS_MSG;
	} elseif ($page_name == "ads_compare") {
		$blocks["ads_compare"] = str_replace("{block_name}", PRODUCTS_COMPARE_RESULTS_MSG, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "ads_search") {
		$blocks["ads_search_advanced"] = str_replace("{block_name}", ADVANCED_SEARCH_TITLE, (str_replace("{module_name}", ADS_TITLE, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "forum_list") {
		$blocks["forum_list"] = FORUMS_LIST_PAGE_MSG;
		$blocks["forum_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_search_block"] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_latest"] = str_replace("{block_name}", LATEST_TITLE, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_top_viewed"] = str_replace("{block_name}", TOP_VIEWED_TITLE, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
	}	elseif ($page_name == "forum_topics") {
		$blocks["forum_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_search_block"] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_latest"] = str_replace("{block_name}", LATEST_TITLE, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_top_viewed"] = str_replace("{block_name}", TOP_VIEWED_TITLE, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_description"] = str_replace("{block_name}", DESCRIPTION_MSG, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_topics_block"] = str_replace("{block_name}", TOPICS_LIST_NEW_TOPIC_MSG, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "forum_topic") {
		$blocks["forum_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_search_block"] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_latest"] = str_replace("{block_name}", LATEST_TITLE, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_top_viewed"] = str_replace("{block_name}", TOP_VIEWED_TITLE, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_description"] = str_replace("{block_name}", DESCRIPTION_MSG, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_view_topic"] = str_replace("{block_name}", FORUM_TOPICS_THREAD_MSG, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_item_related_block"] = str_replace("{block_name}", RELATED_PRODUCTS_TITLE, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["forum_articles_related_block"] = str_replace("{block_name}", RELATED_ARTICLES_MSG, (str_replace("{module_name}", FORUM_TITLE, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "support_new") {
		// Support block
		$blocks["userhome_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", USER_HOME_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["support_block"] = str_replace("{block_name}", SUBMIT_TICKET_MSG, (str_replace("{module_name}", HELPDESK_MSG, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "support_reply") {
		$blocks["userhome_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", USER_HOME_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["support_reply"] = str_replace("{block_name}", REPLYING_TICKETS_MSG, (str_replace("{module_name}", HELPDESK_MSG, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "manuals_list" && ($va_license_code & 32) ) {
		// Add manuals block
		$blocks["manuals_list"] = MANUALS_LIST_PAGE_MSG;
		$blocks["manuals_search"] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", ADMIN_MANUAL_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["manuals_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", ADMIN_MANUAL_MSG, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "manuals_articles" && ($va_license_code & 32) ) {
		// Add manuals block
		$blocks["manuals_articles"] = MANUALS_LIST_PAGE_MSG;
		$blocks["manuals_search"] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", ADMIN_MANUAL_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["manuals_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", ADMIN_MANUAL_MSG, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "manuals_article_details" && ($va_license_code & 32) ) {
		// Add manuals block
		$blocks["manuals_article_details"] = MANUAL_ARTICLE_DETAILS_MSG;
		$blocks["manuals_articles"] = MANUALS_LIST_PAGE_MSG;
		$blocks["manuals_search"] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", ADMIN_MANUAL_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["manuals_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", ADMIN_MANUAL_MSG, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "manuals_search" && ($va_license_code & 32) ) {
		// Add manuals block
		$blocks["manuals_search"] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", ADMIN_MANUAL_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["manuals_search_results"] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", ADMIN_MANUAL_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["manuals_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", ADMIN_MANUAL_MSG, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "site_map") {
		$blocks["site_map_block"] = SITE_MAP_TITLE;
	} elseif (preg_match("/^custom_page/", $page_name)) {
		$blocks["custom_page_body"] = CUSTOM_PAGE_BODY_MSG;
		$blocks["cart_block"] = SMALL_CART_MSG;
		$blocks["coupon_form"] = COUPON_INFO_MSG;
	} elseif ($page_name == "user_login") {
		$blocks["advanced_login"] = LOGIN_DETAILS_MSG;
	} elseif ($page_name == "user_profile") {
		$blocks["userhome_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", USER_HOME_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["user_profile_form"] = PROFILE_SETTINGS_INFO_MSG;
	} elseif ($page_name == "userhome_pages") {
		$blocks["userhome_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", USER_HOME_TITLE, MODULE_NAME_BLOCK_MSG)));
		$blocks["userhome_main_block"] = str_replace("{block_name}", MAIN_BLOCK_MSG, (str_replace("{module_name}", USER_HOME_TITLE, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "forgot_password") {
		$blocks["forgot_password"] = FORGOTTEN_PASSWORD_SETTINGS_MSG;
	} elseif ($page_name == "reset_password") {
		$blocks["reset_password"] = RESET_PASSWORD_INFO_MSG;
	} elseif ($page_name == "polls_previous") {
		$blocks["polls_previous_list"] = PREVIOUS_POLLS_MSG;
	} elseif ($page_name == "wishlist") {
		$blocks["wishlist_search"] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", WISHLIST_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["wishlist_items"] = WISHLIST_ITEMS_SETTINGS_MSG;
	} elseif ($page_name == "site_search") {
		$blocks["site_search_form"] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", ADMIN_SITE_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["site_search_results"] = str_replace("{block_name}", SEARCH_RESULTS_MSG, (str_replace("{module_name}", ADMIN_SITE_MSG, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "checkout_login") {
		$blocks["checkout_login"] = str_replace("{block_name}", LOGIN_TITLE, (str_replace("{module_name}", ADMIN_CHECKOUT_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["cart_block"] = SMALL_CART_MSG;
		$blocks["coupon_form"] = COUPON_INFO_MSG;
	} elseif ($page_name == "order_info") {
		$blocks["checkout_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", ADMIN_CHECKOUT_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["order_data_form"] = str_replace("{block_name}", ADMIN_ORDER_MSG, (str_replace("{module_name}", ADMIN_CHECKOUT_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["coupon_form"] = COUPON_INFO_MSG;
	} elseif ($page_name == "order_payment_details") {
		$blocks["checkout_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", ADMIN_CHECKOUT_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["order_cart"] = str_replace("{block_name}", ADMIN_CART_MSG, (str_replace("{module_name}", ADMIN_CHECKOUT_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["order_payment_details_form"] = str_replace("{block_name}", PAYMENT_DETAILS_MSG, (str_replace("{module_name}", ADMIN_CHECKOUT_MSG, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "order_confirmation") {
		$blocks["checkout_breadcrumb"] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", ADMIN_CHECKOUT_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["order_cart"] = str_replace("{block_name}", ADMIN_CART_MSG, (str_replace("{module_name}", ADMIN_CHECKOUT_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["order_data_preview"] = str_replace("{block_name}", ORDER_DETAILS_MSG, (str_replace("{module_name}", ADMIN_CHECKOUT_MSG, MODULE_NAME_BLOCK_MSG)));
	} elseif ($page_name == "order_final") {
		$blocks["checkout_final"] = str_replace("{block_name}", FINAL_CHECKOUT_MSG, (str_replace("{module_name}", ADMIN_CHECKOUT_MSG, MODULE_NAME_BLOCK_MSG)));
		$blocks["order_cart"] = str_replace("{block_name}", ADMIN_CART_MSG, (str_replace("{module_name}", ADMIN_CHECKOUT_MSG, MODULE_NAME_BLOCK_MSG)));
	} else {
		if ($va_license_code & 1) {
			$blocks["cart_block"] = SMALL_CART_MSG;
		}
	}
	
	// Add general products blocks to different pages
	$page_names = array(
		"index", "products_list", "products_details", "products_search", "user_list", "basket", "products_advanced_search", "products_compare", 
		"products_releases", "products_changes_log", "cart_save", "cart_retrieve", "support_new", "support_reply", "site_map", "site_search", 
		"wishlist", "custom_page", "forgot_password", "reset_password", "polls_previous", "user_login", "user_profile", "userhome_pages", 
		"checkout_login", "order_info", "order_payment_details", "order_confirmation", "order_final", "subscriptions", "details"
	);
	if (preg_match("/^custom_page_(\d+)$/", $page_name)) {
		$page_names[] = $page_name;
	} else if (preg_match("/^products_list_(\d+)$/", $page_name)) {
		$page_names[] = $page_name;
	}
	if (in_array($page_name, $page_names)) {
		if ($va_license_code & 1) {
			// offer settings
			$ps["prod_offers_cols"] = 1;
			$ps["prod_offers_recs"] = 10;
	  
			$ps["prod_offers_points_price"] = 0;
			$ps["prod_offers_reward_points"] = 0;
			$ps["prod_offers_reward_credits"] = 0;
			$ps_checkboxes[] = "prod_offers_points_price";
			$ps_checkboxes[] = "prod_offers_reward_points";
			$ps_checkboxes[] = "prod_offers_reward_credits";
	  
			$ps["prod_offers_add_button"] = 0;
			$ps["prod_offers_view_button"] = 0;
			$ps["prod_offers_goto_button"] = 0;
			$ps["prod_offers_wish_button"] = 0;
			$ps["prod_offers_quantity_control"] = 0;
	  
			$ps_checkboxes[] = "prod_offers_add_button";
			$ps_checkboxes[] = "prod_offers_view_button";
			$ps_checkboxes[] = "prod_offers_goto_button";
			$ps_checkboxes[] = "prod_offers_wish_button";
			
			$ps["prod_fast_add_points_price"] = 0;
			$ps["prod_fast_add_reward_points"] = 0;
			$ps["prod_fast_add_reward_credits"] = 0;
			$ps_checkboxes[] = "prod_fast_add_points_price";
			$ps_checkboxes[] = "prod_fast_add_reward_points";
			$ps_checkboxes[] = "prod_fast_add_reward_credits";
	  
			$ps["prod_fast_add_add_button"] = 0;
			$ps["prod_fast_add_view_button"] = 0;
			$ps["prod_fast_add_goto_button"] = 0;
			$ps["prod_fast_add_wish_button"] = 0;
	  
			$ps_checkboxes[] = "prod_fast_add_add_button";
			$ps_checkboxes[] = "prod_fast_add_view_button";
			$ps_checkboxes[] = "prod_fast_add_goto_button";
			$ps_checkboxes[] = "prod_fast_add_wish_button";

			$blocks["offers_block"] = str_replace("{block_name}", SPECIAL_OFFER_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["top_products_block"] = str_replace("{block_name}", TOP_RATED_TITLE, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["products_fast_add"] = str_replace("{block_name}", FAST_PRODUCT_ADDING_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["products_top_sellers"] = str_replace("{block_name}", TOP_SELLERS_TITLE, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["products_latest"] = str_replace("{block_name}", LATEST_TITLE, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["products_top_viewed"] = str_replace("{block_name}", TOP_VIEWED_TITLE, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["categories_block"] = str_replace("{block_name}", CATEGORIES_LIST_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["subcategories_block"] = str_replace("{block_name}", SUBCATEGORIES_LIST_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["manufacturers_block"] = str_replace("{block_name}", MANUFACTURERS_TITLE, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["merchants_block"] = str_replace("{block_name}", MERCHANTS_TITLE, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["search_block"] = str_replace("{block_name}", SEARCH_FORM_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["products_recently_viewed"] = str_replace("{block_name}", RECENTLY_VIEWED_MSG, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["products_recommended"] = str_replace("{block_name}", PRODUCTS_RECOMMENDED_TITLE, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["products_releases_hot"] = str_replace("{block_name}", RELEASES_TITLE, (str_replace("{module_name}", PRODUCTS_TITLE, MODULE_NAME_BLOCK_MSG)));
			$blocks["cart_block"] = SMALL_CART_MSG;
			$blocks["coupon_form"] = COUPON_INFO_MSG;
		}
	}

	$blocks["login_block"] = LOGIN_TITLE;
	$blocks["subscribe_block"] = NEWSLETTER_SUBSRIPTION_MSG;
	$blocks["poll_block"] = POLL_TITLE;
	$blocks["language_block"] = LANGUAGE_TITLE;
	$blocks["currency_block"] = CURRENCIES_MSG;
	$blocks["layouts_block"] = LAYOUTS_MSG;
	$blocks["site_search_form"] = FULL_SITE_SEARCH_MSG;

	// Get all menus and add as blocks next name "navigation_block_<menu_id>"
	$sql  = " SELECT * FROM " . $table_prefix . "menus";
	$sql .= " ORDER BY menu_title ";
	$db->query($sql);
	$menus = array();
	while ($db->next_record()) {
		$navblock_title = $db->f("menu_title");
		$navblcok_notes = $db->f("menu_notes");
		$navigation_menu_id = $db->f("menu_id");
		$menus[$navigation_menu_id] = $navblock_title;
		if ($navblcok_notes != "") {
			$navblock_title .= " (" . $navblcok_notes . ")";
		}
		$ps["navigation_visible_depth_level_" . $navigation_menu_id] = 2;

		$blocks["navigation_block_" . $navigation_menu_id] = $navblock_title;
	}

	$articles_categories = array();

	if ($va_license_code & 2) {
		if (strlen($art_cat_id) == 0) {
			$sql  = " SELECT ac.category_id, ac.category_name ";
			$sql .= " FROM " . $table_prefix . "articles_categories ac ";
			$sql .= " LEFT JOIN " . $table_prefix . "articles_categories_sites AS st ON st.category_id = ac.category_id ";
			$sql .= " WHERE ac.parent_category_id=0 ";
			$sql .= " AND (ac.sites_all=1 OR st.site_id=".$db->tosql($param_site_id, INTEGER).") ";
		} else {
			$sql  = " SELECT ac.category_id, ac.category_name ";
			$sql .= " FROM " . $table_prefix . "articles_categories ac ";
			$sql .= " LEFT JOIN " . $table_prefix . "articles_categories_sites AS st ON st.category_id = ac.category_id ";
			$sql .= " WHERE ac.category_id=" . $db->tosql($art_cat_id, INTEGER);
			$sql .= " AND (ac.sites_all=1 OR st.site_id=".$db->tosql($param_site_id, INTEGER).") ";
		}
		$sql .= " GROUP BY ac.category_id, ac.category_name";
		$db->query($sql);
		while ($db->next_record()) {
			$row_cat_id = $db->f("category_id");
			$row_cat_name = get_translation($db->f("category_name"));

			if (strlen($art_cat_id) != 0 || $page_name == "index" || $page_name == "support_new" || $page_name == "basket") {
				$articles_categories[$row_cat_id] = $row_cat_name;
				$blocks["a_cats_" . $row_cat_id] = str_replace("{block_name}", CATEGORIES_LIST_MSG, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
				$ps["a_cats_type_" . $row_cat_id] = 1;
				$ps["a_cats_image_" . $row_cat_id] = 1;
				$ps["a_subcats_cols_" . $row_cat_id] = 2;
				$ps["a_subcats_subs_" . $row_cat_id] = 0;
				$ps["a_subcats_type_" . $row_cat_id] = 1;
				$blocks["a_subcats_" . $row_cat_id] = str_replace("{block_name}", SUBCATEGORIES_LIST_MSG, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));

				$ps["a_cat_desc_image_" . $row_cat_id] = 2;
				$ps["a_cat_desc_type_" . $row_cat_id] = 3;

				$blocks["a_hot_" . $row_cat_id] = str_replace("{block_name}", HOT_TITLE, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
				$ps["a_hot_cols_" . $row_cat_id] = 1;
				$ps["a_hot_recs_" . $row_cat_id] = 5;

				$ps["a_cat_item_related_cols_" . $row_cat_id] = 1;
				$ps["a_cat_item_related_recs_" . $row_cat_id] = 5;

				$ps["a_item_related_cols_" . $row_cat_id] = 1;
				$ps["a_item_related_recs_" . $row_cat_id] = 5;

				$blocks["a_latest_" . $row_cat_id] = str_replace("{block_name}", LATEST_TITLE, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
				$ps["a_latest_group_by_" . $row_cat_id] = 0;
				$ps["a_latest_cats_" . $row_cat_id] = "";
				$ps["a_latest_subcats_" . $row_cat_id] = 0;
				$ps_checkboxes[] = "a_latest_subcats_" . $row_cat_id;
				$ps["a_latest_recs_" . $row_cat_id] = 10;
				$ps["a_latest_subrecs_" . $row_cat_id] = 0;
				$ps["a_latest_cols_" . $row_cat_id] = 1;
				$ps["a_latest_image_" . $row_cat_id] = 0;
				$ps["a_latest_desc_" . $row_cat_id] = 1;

				$blocks["a_top_rated_" . $row_cat_id] = str_replace("{block_name}", TOP_RATED_TITLE, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
				$blocks["a_top_viewed_" . $row_cat_id] = str_replace("{block_name}", TOP_VIEWED_TITLE, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
				$ps["a_top_viewed_recs_" . $row_cat_id] = 10;
				$ps["a_top_viewed_cols_" . $row_cat_id] = 1;

				$blocks["a_search_" . $row_cat_id] = str_replace("{block_name}", SEARCH_TITLE, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));

				if ($page_name == "list") {
					$ps["a_list_cols_" . $row_cat_id] = 1;
					$ps["a_list_recs_" . $row_cat_id] = 10;
					$blocks["a_breadcrumb_" . $row_cat_id] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));$row_cat_name . " ";
					$blocks["a_list_" . $row_cat_id] = str_replace("{block_name}", ARTICLES_TITLE, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
					$blocks["a_content_" . $row_cat_id] = str_replace("{block_name}", CONTENT_TITLE, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
					$blocks["a_cat_desc_" . $row_cat_id] = str_replace("{block_name}", CATEGORY_INFO_MSG, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
					$blocks["a_cat_item_related_" . $row_cat_id] = str_replace("{block_name}", CATEGORY_RELATED_PRODUCTS_TITLE, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
				} elseif ($page_name == "details") {
					$ps["a_forums_related_columns_" . $row_cat_id] = 1;
					$ps["a_forums_related_per_page_" . $row_cat_id] = 4;
					$ps["a_forums_related_desc_" . $row_cat_id] = 0;
					$ps["a_forums_related_user_info_" . $row_cat_id] = 1;					
					$ps["a_related_columns_" . $row_cat_id]  = 1;
					$ps["a_related_per_page_" . $row_cat_id] = 4;
					$ps["a_related_image_" . $row_cat_id] = 0;
					$ps["a_related_desc_" . $row_cat_id] = 0;
					$ps["a_related_date_" . $row_cat_id] = 0;
					
					$ps_checkboxes[] = "a_forums_related_user_info_" . $row_cat_id;		
					$blocks["a_breadcrumb_" . $row_cat_id] = str_replace("{block_name}", BREADCRUMP_MSG, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
					$blocks["a_details_" . $row_cat_id] = str_replace("{block_name}", ARTICLES_TITLE, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
					
					$blocks["a_related_" . $row_cat_id] = str_replace("{block_name}", RELATED_ARTICLES_MSG, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
					$blocks["a_cat_desc_" . $row_cat_id] = str_replace("{block_name}", CATEGORY_INFO_MSG, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));$row_cat_name . " ";
					$blocks["a_cat_item_related_" . $row_cat_id] = str_replace("{block_name}", CATEGORY_RELATED_PRODUCTS_TITLE, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
					$blocks["a_item_related_" . $row_cat_id] = str_replace("{block_name}", ARTICLE_RELATED_PRODUCTS_TITLE, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
					$blocks["a_forums_related_" . $row_cat_id] = str_replace("{block_name}", RELATED_FORUMS_MSG, (str_replace("{module_name}", $row_cat_name, MODULE_NAME_BLOCK_MSG)));
				}

			}
		}
	}

	// get filters
	if (preg_match("/^products_list/", $page_name) || $page_name == "products_search" || $page_name == "user_list") {
		$filters = array();
		$sql = " SELECT filter_id, filter_name FROM " . $table_prefix . "filters ";
		$db->query($sql);
		while ($db->next_record()) {
			$filter_id = $db->f("filter_id");
			$filter_name = get_translation($db->f("filter_name"));
  
			$filters[$filter_id] = $filter_name;
			$blocks["filter_" . $filter_id] = str_replace("{block_name}", $filter_name, (str_replace("{module_name}", FILTER_BUTTON, MODULE_NAME_BLOCK_MSG)));
			$ps["filter_values_limit_" . $filter_id] = "10";
		}
	}

	// get all custom blocks
	$custom_blocks = array();
	$sql = " SELECT block_id, block_name FROM " . $table_prefix . "custom_blocks ";
	$db->query($sql);
	while ($db->next_record()) {
		$block_id = $db->f("block_id");
		$block_name = get_translation($db->f("block_name"));

		$custom_blocks[$block_id] = $block_name;
		$blocks["custom_block_" . $block_id] = str_replace("{block_name}", $block_name, (str_replace("{module_name}", CUSTOM_BLOCKS_MSG, MODULE_NAME_BLOCK_MSG)));
		$ps["cb_css_class_" . $block_id] = "";
		$ps["cb_user_type_" . $block_id] = "";
		$ps["cb_admin_type_" . $block_id] = "";
		$ps["cb_params_" . $block_id] = "";
	}

	// get all banners groups
	$banners_groups = array();
	$sql = " SELECT * FROM " . $table_prefix . "banners_groups ";
	$db->query($sql);
	while ($db->next_record()) {
		$group_id = $db->f("group_id");
		$group_name = get_translation($db->f("group_name"));

		$banners_groups[$group_id] = $group_name;
		$blocks["banners_group_" . $group_id] = str_replace("{block_name}", $group_name, (str_replace("{module_name}", BANNERS_MSG, MODULE_NAME_BLOCK_MSG)));
		$ps["bg_params_" . $group_id] = "";
		$ps["bg_limit_" . $group_id] = "";
	}

	// SMS block
	$blocks["sms_test_block"] = SMS_TEST_TITLE;

	$left_blocks = array();
	$middle_blocks = array();
	$right_blocks = array();
	$available_blocks = array();

	$r->get_form_parameters();

	$operation = get_param("operation");

	if (strlen($operation))
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		$available_list = get_param("available_list");
		$left_list = get_param("left_list");
		$middle_list = get_param("middle_list");
		$right_list = get_param("right_list");
		if ($available_list) {
			$available_array = split(",", $available_list);
			for ($i = 0; $i < sizeof($available_array); $i++) {
				$available_blocks[] = array($available_array[$i], $blocks[$available_array[$i]]);
			}
		}
		if ($left_list) {
			if ($r->get_value("left_column_hide") == 0) {
				$r->parameters["left_column_width"][REQUIRED] = true;
			}
			$left_array = split(",", $left_list);
			for ($i = 0; $i < sizeof($left_array); $i++) {
				$left_blocks[] = array($left_array[$i], $blocks[$left_array[$i]]);
			}
		}
		if ($middle_list) {
			if ($r->get_value("middle_column_hide") == 0) {
				$r->parameters["middle_column_width"][REQUIRED] = true;
			}
			$middle_array = split(",", $middle_list);
			for ($i = 0; $i < sizeof($middle_array); $i++) {
				$middle_blocks[] = array($middle_array[$i], $blocks[$middle_array[$i]]);
			}
		}
		if ($right_list) {
			if ($r->get_value("right_column_hide") == 0) {
				$r->parameters["right_column_width"][REQUIRED] = true;
			}
			$right_array = split(",", $right_list);
			for ($i = 0; $i < sizeof($right_array); $i++) {
				$right_blocks[] = array($right_array[$i], $blocks[$right_array[$i]]);
			}
		}

		$r->validate();

		if (!strlen($r->errors))
		{
			if (strlen($art_cat_id)) {
				$page_name_value = "a_" . $page_name . "_" . $art_cat_id;
				$sql  = " DELETE FROM " . $table_prefix . "page_settings WHERE layout_id=" . $db->tosql($layout_id, INTEGER);
				$sql .= " AND page_name=" . $db->tosql($page_name_value, TEXT);
				$sql .= " AND site_id=".$db->tosql($param_site_id, INTEGER);
			} else {
				$page_name_value = $page_name;
				$sql  = " DELETE FROM " . $table_prefix . "page_settings WHERE layout_id=" . $db->tosql($layout_id, INTEGER);
				$sql .= " AND page_name=" . $db->tosql($page_name_value, TEXT);
				$sql .= " AND site_id=".$db->tosql($param_site_id, INTEGER);
			}
			set_session("session_" . $page_name_value .  "_settings", "");
			$db->query($sql);
			foreach ($r->parameters as $key => $value)
			{
				$sql  = " INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_value, site_id) VALUES (";
				$sql .= $db->tosql($layout_id, INTEGER) . ",";
				$sql .= $db->tosql($page_name_value, TEXT) . ",";
				$sql .= $db->tosql($key, TEXT) . ",";
				$sql .= $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
				$sql .= $db->tosql($param_site_id, INTEGER) . ")";
				$db->query($sql);
			}
			foreach ($ps as $key => $value)
			{
				$value = get_param($key);
				if (!$value && in_array($key, $ps_checkboxes)) {
					$value = "0";
				}
				$ps[$key] = $value;

				$sql  = " INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_value, site_id) VALUES (";
				$sql .= $db->tosql($layout_id, INTEGER) . ",";
				$sql .= $db->tosql($page_name_value, TEXT) . ",";
				$sql .= $db->tosql($key, TEXT) . ",";
				$sql .= $db->tosql($value, TEXT) . ",";
				$sql .= $db->tosql($param_site_id, INTEGER) . ")";
				$db->query($sql);
			}
			for ($i = 0; $i < sizeof($left_blocks); $i++) {
				$sql  = " INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value, site_id) VALUES (";
				$sql .= $db->tosql($layout_id, INTEGER) . ",";
				$sql .= $db->tosql($page_name_value, TEXT) . ",";
				$sql .= $db->tosql($left_blocks[$i][0], TEXT) . "," . $i . ",'left',";
				$sql .= $db->tosql($param_site_id, INTEGER) . ")";
				$db->query($sql);
			}
			for ($i = 0; $i < sizeof($middle_blocks); $i++) {
				$sql  = " INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value, site_id) VALUES (";
				$sql .= $db->tosql($layout_id, INTEGER) . ",";
				$sql .= $db->tosql($page_name_value, TEXT) . ",";
				$sql .= $db->tosql($middle_blocks[$i][0], TEXT) . "," . $i . ",'middle',";
				$sql .= $db->tosql($param_site_id, INTEGER) . ")";
				$db->query($sql);
			}
			for ($i = 0; $i < sizeof($right_blocks); $i++) {
				$sql  = " INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value, site_id) VALUES (";
				$sql .= $db->tosql($layout_id, INTEGER) . ",";
				$sql .= $db->tosql($page_name_value, TEXT) . ",";
				$sql .= $db->tosql($right_blocks[$i][0], TEXT) . "," . $i . ",'right',";
				$sql .= $db->tosql($param_site_id, INTEGER) . ")";
				$db->query($sql);
			}
			
			if ($operation == "save") {
				header("Location: " . $return_page);
				exit;
			}
		}
	}
	else
	{
		if (strlen($art_cat_id)) {
			$page_name_value = "a_" . $page_name . "_" . $art_cat_id;
		} else {
			$page_name_value = $page_name;
		}
		$sql  = " SELECT setting_name, setting_value, site_id FROM " . $table_prefix . "page_settings ";
		$sql .= " WHERE layout_id=" . $db->tosql($layout_id, INTEGER);
		$sql .= " AND page_name=" . $db->tosql($page_name_value, TEXT);
		$sql2 = "";
		if ($multisites_version) {
			$sql2 .= " AND site_id=" . $db->tosql($param_site_id, INTEGER);
		}
		$sql2 .= " ORDER BY setting_order ";		
		
		$db->query($sql . $sql2);
		
		$tmp_settings = array();
		if ($db->next_record()) {
				$tmp_settings[$db->f("setting_name")] = $db->f("setting_value");	
				while ($db->next_record()) {
					$tmp_settings[$db->f("setting_name")] = $db->f("setting_value");	
				}
		} elseif ($multisites_version && isset($param_site_id) && $param_site_id>1) {
			$sql2  = " AND site_id=1 ";			
			$sql2 .= " ORDER BY setting_order ";
			$db->query($sql . $sql2);
			while ($db->next_record()) {
				$tmp_settings[$db->f("setting_name")] = $db->f("setting_value");
			}
		}
		
		foreach($tmp_settings AS $setting_name=>$setting_value) {
			if (isset($blocks[$setting_name])) {
			  if ($setting_value == "left") {
					$left_blocks[] = array($setting_name, $blocks[$setting_name]);
					$left_blocks_values[$setting_name] = $setting_value;
				} elseif ($setting_value == "middle") {
					$middle_blocks[] = array($setting_name, $blocks[$setting_name]);
					$middle_blocks_values[$setting_name] = $setting_value;
				} elseif ($setting_value == "right") {
					$right_blocks[] = array($setting_name, $blocks[$setting_name]);
					$right_blocks_values[$setting_name] = $setting_value;
				}
			} elseif (isset($r->parameters[$setting_name])) {
				$r->set_value($setting_name, $setting_value);
			} else {
				$ps[$setting_name] = $setting_value;
			}
		}

		foreach ($blocks as $block_name => $block_title)
		{
			if (!isset($left_blocks_values[$block_name]) &&
				!isset($middle_blocks_values[$block_name]) &&
				!isset($right_blocks_values[$block_name])) {
				$available_blocks[] = array($block_name, $block_title);
			}
		}

	}

	$r->set_form_parameters();

	// parse custom block options
	if (sizeof($custom_blocks) > 0) {
		foreach ($custom_blocks as $block_id => $block_name) {
			$t->set_var("block_id", $block_id);
			$t->set_var("block_name", $block_name);

			$t->set_var("cb_css_class", htmlspecialchars($ps["cb_css_class_" . $block_id]));
			set_options($block_user_types, $ps["cb_user_type_" . $block_id], "cb_user_type");
			set_options($admin_types, $ps["cb_admin_type_" . $block_id], "cb_admin_type");
			$t->set_var("cb_params", htmlspecialchars($ps["cb_params_" . $block_id]));

			$t->parse("custom_options", true);
		}
	}

	// parse banners options
	if (sizeof($banners_groups) > 0) {
		foreach ($banners_groups as $group_id => $group_name) {
			$t->set_var("group_id", $group_id);
			$t->set_var("group_name", $group_name);

			$t->set_var("bg_params", htmlspecialchars($ps["bg_params_" . $group_id]));
			$t->set_var("bg_limit", htmlspecialchars($ps["bg_limit_" . $group_id]));

			$t->parse("banners_options", true);
		}
	}

	// set products categories settings
	set_options($categories_types, $ps["categories_type"], "categories_type");
	set_options($categories_images, $ps["categories_image"], "categories_image");
	set_options($language_selection, $ps["language_selection"], "language_selection");
	set_options($currency_selection, $ps["currency_selection"], "currency_selection");
	set_options($layouts_selection, $ps["layouts_selection"], "layouts_selection");
	set_options($manufacturers_selection, $ps["manufacturers_selection"], "manufacturers_selection");
	set_options($cat_desc_images, $ps["manufacturers_image"], "manufacturers_image");
	set_options($cat_desc_types, $ps["manufacturers_desc"], "manufacturers_desc");
	set_options($manufacturers_order_types, $ps["manufacturers_order"], "manufacturers_order");
	set_options($manufacturers_direction_types, $ps["manufacturers_direction"], "manufacturers_direction");
	set_options($merchants_selection, $ps["merchants_selection"], "merchants_selection");

	// set products subcategories settings
	set_options($subcategories_types, $ps["subcategories_type"], "subcategories_type");
	set_options($columns_values, $ps["subcategories_columns"], "subcategories_columns");
	set_options($subs, $ps["subcategories_subs"], "subcategories_subs");
	set_options($subcategories_images, $ps["subcategories_image"], "subcategories_image");

	// set products blocks settings
	$t->set_var("products_recent_records", $ps["products_recent_records"]);
	set_options($columns_values, $ps["products_recent_cols"], "products_recent_cols");
	set_options($top_records, $ps["bestsellers_records"], "bestsellers_records");
	set_options($period_days, $ps["bestsellers_days"], "bestsellers_days");
	if ($va_license_code & 1) {
		set_options($prod_image_types, $ps["recent_image"], "recent_image");
		set_options($prod_desc_types, $ps["recent_desc"], "recent_desc");
		set_options($order_statuses, $ps["bestsellers_status"], "bestsellers_status");
		set_options($prod_image_types, $ps["bestsellers_image"], "bestsellers_image");
		set_options($prod_desc_types, $ps["bestsellers_desc"], "bestsellers_desc");
		set_options($prod_image_types, $ps["top_rated_image"], "top_rated_image");
		set_options($prod_desc_types, $ps["top_rated_desc"], "top_rated_desc");
		set_options($prod_image_types, $ps["top_viewed_image"], "top_viewed_image");
		set_options($prod_desc_types, $ps["top_viewed_desc"], "top_viewed_desc");
		set_options($prod_image_types, $ps["prod_latest_image"], "prod_latest_image");
		set_options($prod_desc_types, $ps["prod_latest_desc"], "prod_latest_desc");
		set_options($prod_order_types, $ps["prod_latest_order"], "prod_latest_order");
		set_options($cat_desc_types, $ps["category_description_type"], "category_description_type");
		set_options($cat_desc_images, $ps["category_description_image"], "category_description_image");
		set_options($man_desc_types, $ps["manufacturer_info_type"], "manufacturer_info_type");
		set_options($man_desc_images, $ps["manufacturer_info_image"], "manufacturer_info_image");
		set_options($prod_image_types, $ps["wl_image"], "wl_image");
		$t->set_var("wl_recs", $ps["wl_recs"]);
	}
	set_options($columns_values, $ps["products_latest_cols"], "products_latest_cols");
	set_options($records_per_page, $ps["products_latest_recs"], "products_latest_recs");

	$t->set_var("prod_top_viewed_recs", $ps["prod_top_viewed_recs"]);
	set_options($columns_values, $ps["prod_top_viewed_cols"], "prod_top_viewed_cols");
	set_options($quantity_controls, $ps["prod_offers_quantity_control"], "prod_offers_quantity_control");
			
	// set ads categories settings
	set_options($categories_types, $ps["ads_categories_type"], "ads_categories_type");
	set_options($categories_images, $ps["ads_categories_image"], "ads_categories_image");

	// set ads subcategories settings
	set_options($subcategories_types, $ps["ads_subcategories_type"], "ads_subcategories_type");
	set_options($columns_values, $ps["ads_subcategories_columns"], "ads_subcategories_columns");
	set_options($subs, $ps["ads_subcategories_subs"], "ads_subcategories_subs");

	// set ads category description settings
	set_options($cat_desc_types, $ps["ads_cat_desc_type"], "ads_cat_desc_type");
	set_options($cat_desc_images, $ps["ads_cat_desc_image"], "ads_cat_desc_image");

	// set forum blocks settings
	$t->set_var("forum_latest_recs", $ps["forum_latest_recs"]);
	$t->set_var("forum_latest_cols", $ps["forum_latest_cols"]);
	$t->set_var("forum_top_viewed_recs", $ps["forum_top_viewed_recs"]);
	$t->set_var("forum_top_viewed_cols", $ps["forum_top_viewed_cols"]);
	set_options($cat_desc_types, $ps["forum_description_type"], "forum_description_type");
	set_options($cat_desc_images, $ps["forum_description_image"], "forum_description_image");
	set_options($columns_values, $ps["forum_item_related_columns"], "forum_item_related_columns");
	set_options($records_per_page, $ps["forum_item_related_per_page"], "forum_item_related_per_page");
	set_options($columns_values, $ps["forum_articles_related_columns"], "forum_articles_related_columns");
	set_options($records_per_page, $ps["forum_articles_related_per_page"], "forum_articles_related_per_page");
	set_options($articles_image_types, $ps["forum_articles_related_image"], "forum_articles_related_image");
	set_options($articles_desc_types, $ps["forum_articles_related_desc"], "forum_articles_related_desc");
	set_options($articles_date_types, $ps["forum_articles_related_date"], "forum_articles_related_date");
	
	
	// set ads blocks settings
	set_options($top_records, $ps["ads_recent_records"], "ads_recent_records");
	set_options($records_per_page, $ps["ads_hot_recs"], "ads_hot_recs");
	set_options($columns_values, $ps["ads_hot_cols"], "ads_hot_cols");
	set_options($records_per_page, $ps["ads_special_recs"], "ads_special_recs");
	set_options($columns_values, $ps["ads_special_cols"], "ads_special_cols");
	set_options($records_per_page, $ps["ads_list_per_page"], "ads_list_per_page");
	set_options($columns_values, $ps["ads_list_columns"], "ads_list_columns");
	set_options($use_tabs, $ps["ads_details_tabs"], "ads_details_tabs");
	set_options($user_types, $ps["ads_user_type_id"], "ads_user_type_id");
	set_options($columns_values, $ps["ads_latest_cols"], "ads_latest_cols");
	set_options($records_per_page, $ps["ads_latest_recs"], "ads_latest_recs");
	$t->set_var("ads_top_viewed_recs", $ps["ads_top_viewed_recs"]);
	$t->set_var("ads_top_viewed_cols", $ps["ads_top_viewed_cols"]);

	// SMS block settings
	$t->set_var("sms_originator", $ps["sms_originator"]);
	$t->set_var("sms_test_message", $ps["sms_test_message"]);

	if (in_array($page_name, $page_names)) {
		if ($va_license_code & 1) {
			$t->set_var("prod_offers_recs", $ps["prod_offers_recs"]);
			$t->set_var("prod_offers_cols", $ps["prod_offers_cols"]);
		}
	}

	// parse checkboxes
	for ($s = 0; $s < sizeof($ps_checkboxes); $s++) {
		$checkbox_name = $ps_checkboxes[$s];
		if (isset($ps[$checkbox_name]) && $ps[$checkbox_name] == 1) {
			$t->set_var($checkbox_name, "checked");
		} else {
			$t->set_var($checkbox_name, "");
		}
	}
	
	if ($page_name == "index" || $page_name == "basket") {
		$t->set_var("prod_recom_per_page", $ps["prod_recom_per_page"]);
		$t->set_var("prod_recom_cols", $ps["prod_recom_cols"]);
		$t->set_var("basket_prod_recom_per_page", $ps["basket_prod_recom_per_page"]);
		$t->set_var("basket_prod_recom_cols", $ps["basket_prod_recom_cols"]);
		set_options($shopping_cart_preview, $ps["shopping_cart_preview"], "shopping_cart_preview"); 

		if ($page_name == "basket") {
			$fast_checkout_params = array(
				"fast_checkout_country_show", "fast_checkout_country_required", 
		  	"fast_checkout_state_show", "fast_checkout_state_required", 
				"fast_checkout_postcode_show", "fast_checkout_postcode_required"
			);
			for ($i = 0; $i < sizeof($fast_checkout_params); $i++) {
				$param_name = $fast_checkout_params[$i];
				if ($ps[$param_name] == 1) {
					$t->set_var($param_name, "checked");
				} else {
					$t->set_var($param_name, "");
				}	
			}
		}

	} elseif (preg_match("/^products_list/", $page_name) || $page_name == "products_search" || $page_name == "user_list") {
		$t->set_var("prod_recom_per_page", $ps["prod_recom_per_page"]);
		$t->set_var("prod_recom_cols", $ps["prod_recom_cols"]);
		//$t->set_var("basket_prod_recom_per_page", $ps["basket_prod_recom_per_page"]);
		//$t->set_var("basket_prod_recom_cols", $ps["basket_prod_recom_cols"]);	//
		$t->set_var("products_per_page", $ps["products_per_page"]);
		$t->set_var("products_columns", $ps["products_columns"]);
		set_options($navigator_types, $ps["products_nav_type"], "products_nav_type");
		set_options($products_view_types, $ps["products_default_view"], "products_default_view");
		$t->set_var("products_nav_pages", $ps["products_nav_pages"]);
		if ($ps["products_nav_first_last"] == 1) {
			$t->set_var("products_nav_first_last", "checked");
		} else {
			$t->set_var("products_nav_first_last", "");
		}
		if ($ps["products_nav_prev_next"] == 1) {
			$t->set_var("products_nav_prev_next", "checked");
		} else {
			$t->set_var("products_nav_prev_next", "");
		}
		if ($ps["products_sortings"] == 1) {
			$t->set_var("products_sortings", "checked");
		} else {
			$t->set_var("products_sortings", "");
		}
		if ($ps["products_group_by_cats"] == 1) {
			$t->set_var("products_group_by_cats", "checked");
		} else {
			$t->set_var("products_group_by_cats", "");
		}

		//set_options($records_per_page, $ps["products_per_page"], "products_per_page");
		$t->parse("products_settings", false);
	} elseif ($page_name == "products_details") {
		$t->set_var("prod_recom_per_page", $ps["prod_recom_per_page"]);
		$t->set_var("prod_recom_cols", $ps["prod_recom_cols"]);
		$t->set_var("basket_prod_recom_per_page", $ps["basket_prod_recom_per_page"]);
		$t->set_var("basket_prod_recom_cols", $ps["basket_prod_recom_cols"]);

		/*if ($ps["details_block_image"] == 1) {
			$t->set_var("details_block_image", "checked");
		} else {
			$t->set_var("details_block_image", "");
		}*/

		set_options($details_manufacturer_image, $ps["details_manufacturer_image"], "details_manufacturer_image");
		set_options($columns_values, $ps["related_columns"], "related_columns");
		set_options($records_per_page, $ps["related_per_page"], "related_per_page");
		set_options($use_tabs, $ps["use_tabs"], "use_tabs");

		// related purchase options
		$t->set_var("related_purchase_recs", $ps["related_purchase_recs"]);
		$t->set_var("related_purchase_cols", $ps["related_purchase_cols"]);
		set_options($period_days, $ps["related_purchase_days"], "related_purchase_days");
		set_options($order_statuses, $ps["related_purchase_status"], "related_purchase_status");
		set_options($prod_image_types, $ps["related_purchase_image"], "related_purchase_image");
		set_options($prod_desc_types, $ps["related_purchase_desc"], "related_purchase_desc");

		// related purchase options
		$t->set_var("users_bought_item_recs", $ps["users_bought_item_recs"]);
		$t->set_var("users_bought_item_cols", $ps["users_bought_item_cols"]);
		$t->set_var("users_bought_item_days", $ps["users_bought_item_days"]);
		set_options($order_statuses, $ps["users_bought_status"], "users_bought_status");
		set_options($user_bought_types, $ps["users_bought_type"], "users_bought_type");

		set_options($columns_values, $ps["forums_related_columns"], "forums_related_columns");
		set_options($records_per_page, $ps["forums_related_per_page"], "forums_related_per_page");
		set_options($forum_thread_descriptions, $ps["forums_related_desc"], "forums_related_desc");	
		
		set_options($columns_values, $ps["articles_related_columns"], "articles_related_columns");
		set_options($records_per_page, $ps["articles_related_per_page"], "articles_related_per_page");
		set_options($articles_image_types, $ps["articles_related_image"], "articles_related_image");
		set_options($articles_desc_types, $ps["articles_related_desc"], "articles_related_desc");
		set_options($articles_date_types, $ps["articles_related_date"], "articles_related_date");
		
		$t->parse("product_settings", false);
	}

	foreach ($articles_categories as $row_cat_id => $row_cat_name)
	{
		$t->set_var("row_cat_id", $row_cat_id);
		$t->set_var("row_cat_name", $row_cat_name);

		set_options($categories_types, $ps["a_cats_type_" . $row_cat_id], "a_cats_type");
		set_options($categories_images, $ps["a_cats_image_" . $row_cat_id], "a_cats_image");

		set_options($subcategories_types, $ps["a_subcats_type_" . $row_cat_id], "a_subcats_type");
		set_options($columns_values, $ps["a_subcats_cols_".$row_cat_id], "a_subcats_cols");
		set_options($subs, $ps["a_subcats_subs_" . $row_cat_id], "a_subcats_subs");

		set_options($cat_desc_images, $ps["a_cat_desc_image_".$row_cat_id], "a_cat_desc_image");
		set_options($cat_desc_types, $ps["a_cat_desc_type_".$row_cat_id], "a_cat_desc_type");

		set_options($group_by_categories, $ps["a_latest_group_by_".$row_cat_id], "a_latest_group_by");
		$t->set_var("a_latest_cats", $ps["a_latest_cats_".$row_cat_id]);
		if ($ps["a_latest_subcats_".$row_cat_id] == 1) {
			$t->set_var("a_latest_subcats", "checked");
		} else {
			$t->set_var("a_latest_subcats", "");
		}
		$t->set_var("a_latest_recs", $ps["a_latest_recs_".$row_cat_id]);
		$t->set_var("a_latest_subrecs", $ps["a_latest_subrecs_".$row_cat_id]);
		$t->set_var("a_latest_cols", $ps["a_latest_cols_".$row_cat_id]);

		set_options($articles_image_types, $ps["a_latest_image_".$row_cat_id], "a_latest_image");
		set_options($articles_desc_types, $ps["a_latest_desc_".$row_cat_id], "a_latest_desc");

		$t->set_var("a_top_viewed_cols", $ps["a_top_viewed_cols_".$row_cat_id]);
		$t->set_var("a_top_viewed_recs", $ps["a_top_viewed_recs_".$row_cat_id]);

		set_options($columns_values, $ps["a_hot_cols_".$row_cat_id], "a_hot_cols");
		set_options($records_per_page, $ps["a_hot_recs_".$row_cat_id], "a_hot_recs");

		set_options($columns_values, $ps["a_cat_item_related_cols_".$row_cat_id], "a_cat_item_related_cols");
		set_options($records_per_page, $ps["a_cat_item_related_recs_".$row_cat_id], "a_cat_item_related_recs");

		if ($page_name == "list") {		
			set_options($columns_values, $ps["a_list_cols_".$row_cat_id], "a_list_cols");
			set_options($records_per_page, $ps["a_list_recs_".$row_cat_id], "a_list_recs");
			$t->parse("articles_list_settings", true);
		} elseif ($page_name == "details") {
			set_options($columns_values, $ps["a_forums_related_columns_".$row_cat_id], "a_forums_related_columns");
			set_options($records_per_page, $ps["a_forums_related_per_page_".$row_cat_id], "a_forums_related_per_page");
			set_options($forum_thread_descriptions, $ps["a_forums_related_desc_".$row_cat_id], "a_forums_related_desc");
			set_options($columns_values, $ps["a_related_columns_".$row_cat_id], "a_related_columns");
			set_options($records_per_page, $ps["a_related_per_page_".$row_cat_id], "a_related_per_page");
			set_options($columns_values, $ps["a_item_related_cols_".$row_cat_id], "a_item_related_cols");
			set_options($records_per_page, $ps["a_item_related_recs_".$row_cat_id], "a_item_related_recs");
			set_options($articles_image_types, $ps["a_related_image_".$row_cat_id], "a_related_image");
			set_options($articles_desc_types, $ps["a_related_desc_".$row_cat_id], "a_related_desc");
			set_options($articles_date_types, $ps["a_related_date_".$row_cat_id], "a_related_date");
	
			$t->parse("articles_details_settings", true);
		}

		$t->parse("articles_settings", true);
	}

	if (preg_match("/^products_list/", $page_name) || $page_name == "products_search" || $page_name == "user_list") {
		foreach ($filters as $filter_id => $filter_name)
		{
			$t->set_var("filter_id", $filter_id);
			$t->set_var("filter_name", $filter_name);

			$t->set_var("filter_values_limit", $ps["filter_values_limit_".$filter_id]);
			$t->parse("filters_settings", true);
		}
	}

	// Menus parameters
	foreach ($menus as $menu_id => $menu_title) {
		$t->set_var("menu_id", $menu_id);
		$t->set_var("menu_title", $menu_title);
		set_options($navigation_visible_depth_levels, $ps["navigation_visible_depth_level_" . $menu_id], "navigation_visible_depth_level");
		$t->parse("navigation_block_settings", true);
	}


	set_options($available_blocks, "", "available_blocks");
	set_options($left_blocks, "", "left_blocks");
	set_options($middle_blocks, "", "middle_blocks");
	set_options($right_blocks, "", "right_blocks");


	if ($page_name == "index") {
		$t->set_var("page_name_desc", MENU_HOME);
	} elseif (strlen($art_cat_name)) {
		if ($page_name == "list") {
			$t->set_var("page_name_desc", LIST_MSG);
		} elseif ($page_name == "details") {
			$t->set_var("page_name_desc", DETAILED_MSG);
		}
	} elseif ($products_page) {
		$t->set_var("page_name_desc", $products_page);
	} elseif ($basket_page) {
		$t->set_var("page_name_desc", $basket_page);
	} elseif ($user_page) {
		$t->set_var("page_name_desc", $user_page);
	} elseif ($ads_page) {
		$t->set_var("page_name_desc", $ads_page);
	} elseif ($forums_page) {
		$t->set_var("page_name_desc", $forums_page);
	} elseif ($global_page) {
		$t->set_var("page_name_desc", $global_page);
	}

	$t->set_var("layout_id", htmlspecialchars($layout_id));
	$t->set_var("art_cat_id", htmlspecialchars($art_cat_id));
	$t->set_var("page_name", htmlspecialchars($page_name));
	$t->set_var("rp", htmlspecialchars($return_page));


	$t->set_var("cms_path", "");
	$t->set_var("articles_path", "");
	$t->set_var("products_path", "");
	$t->set_var("ads_path", "");
	if ($return_page == "admin_layouts.php") {
		if ($layout_name) {
			$t->set_var("layout_name", $layout_name);
			$t->parse("layout_name_link", false);
		}
		$t->parse("cms_path", false);
	} elseif (strlen($art_cat_name)) {
	  $t->set_var("art_cat_name", $art_cat_name);
		$t->parse("articles_path", false);
	} elseif (strlen($index_page) || strlen($global_page)) {
		$t->parse("cms_path", false);
	} elseif (strlen($products_page)) {
		$t->parse("products_path", false);
	} elseif (strlen($user_page)) {
		$t->parse("user_path", false);
	} elseif (strlen($ads_page)) {
		$t->parse("ads_path", false);
	} else {
		if ($layout_name) {
			$t->set_var("layout_name", $layout_name);
			$t->parse("layout_name_link", false);
		}
		$t->parse("cms_path", false);
	}
	
	// multisites
	if ($sitelist) {
		$sites = array();
		if($art_cat_id){
			$sql  = " SELECT sites_all FROM " . $table_prefix . "articles_categories ";
			$sql .= " WHERE sites_all=1 AND category_id=".$db->tosql($art_cat_id, INTEGER);
			$db->query($sql);
			if($db->next_record()){
				$sql = " SELECT site_id, site_name FROM " . $table_prefix . "sites ORDER BY site_id ";
			} else {
				$sql  = " SELECT s.site_id, s.site_name FROM ( " . $table_prefix . "sites AS s ";	
				$sql .= " LEFT JOIN " . $table_prefix . "articles_categories_sites AS st ON st.site_id = s.site_id)";
				$sql .= " WHERE st.category_id=".$db->tosql($art_cat_id, INTEGER);
				$sql .= " GROUP BY s.site_id, s.site_name";
			}
		} else {
			$sql = " SELECT site_id, site_name FROM " . $table_prefix . "sites ORDER BY site_id ";
		}
		$sites   = get_db_values($sql, "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}	
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>