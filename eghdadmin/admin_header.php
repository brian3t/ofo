<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_header.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");

	if (!isset($va_type) || !$va_type) {
		$va_type = defined("VA_TYPE") ? strtolower(VA_TYPE) : "standard";
	}
	if (!isset($va_name) || !$va_name) {
		$va_name = defined("VA_PRODUCT") ? strtolower(VA_PRODUCT) : "shop";
	}
	$va_license_code = va_license_code();

	// Admin Site URL settings
	$admin_folder = get_admin_dir();
	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");
	$admin_site_url = $site_url . $admin_folder;
	$admin_secure_url = $secure_url . $admin_folder;

	// SSL settings
	$ssl_admin_tickets = get_setting_value($settings, "ssl_admin_tickets", 0);
	$ssl_admin_ticket = get_setting_value($settings, "ssl_admin_ticket", 0);
	$ssl_admin_helpdesk = get_setting_value($settings, "ssl_admin_helpdesk", 0);
	if ($ssl_admin_tickets && strlen($secure_url)) {
		$tickets_site_url = $admin_secure_url;
	} else {
		$tickets_site_url = $admin_site_url;
	}
	if ($ssl_admin_ticket && strlen($secure_url)) {
		$ticket_site_url = $admin_secure_url;
	} else {
		$ticket_site_url = $admin_site_url;
	}
	if ($ssl_admin_helpdesk && strlen($secure_url)) {
		$helpdesk_site_url = $admin_secure_url;
	} else {
		$helpdesk_site_url = $admin_site_url;
	}

	// orders SSL settings
	$ssl_admin_orders_list = get_setting_value($settings, "ssl_admin_orders_list", 0);
	$ssl_admin_order_details = get_setting_value($settings, "ssl_admin_order_details", 0);
	$ssl_admin_orders_pages = get_setting_value($settings, "ssl_admin_orders_pages", 0);
	$secure_admin_order_create = get_setting_value($settings, "secure_admin_order_create", 0);
	if ($ssl_admin_orders_list && strlen($secure_url)) {
		$orders_list_site_url = $admin_secure_url;
	} else {
		$orders_list_site_url = $admin_site_url;
	}
	if ($ssl_admin_order_details && strlen($secure_url)) {
		$order_details_site_url = $admin_secure_url;
	} else {
		$order_details_site_url = $admin_site_url;
	}
	if ($ssl_admin_orders_pages && strlen($secure_url)) {
		$orders_pages_site_url = $admin_secure_url;
	} else {
		$orders_pages_site_url = $admin_site_url;
	}
	if ($secure_admin_order_create && strlen($secure_url)) {
		$admin_order_call_url = $admin_secure_url . "admin_order_call.php";
	} else {
		$admin_order_call_url = $admin_site_url . "admin_order_call.php";
	}


	// common permissions
	$permissions = get_permissions();
	$forum_perm = get_setting_value($permissions, "forum", 0);
	$static_tables_perm = get_setting_value($permissions, "static_tables", 0);
	$global_settings_perm = get_setting_value($permissions, "site_settings", 0);
	$sites_perm = get_setting_value($permissions, "admin_sites", 0);
	$ads_perm = get_setting_value($permissions, "ads", 0);
	$black_ips_perm = get_setting_value($permissions, "black_ips", 0);
	$visits_report_perm = get_setting_value($permissions, "visits_report", 0);
	$db_management_perm = get_setting_value($permissions, "db_management", 0);
	$manual_perm = get_setting_value($permissions, "manual", 0);
	$newsletter_perm = get_setting_value($permissions, "newsletter", 0);
	$banned_contents_perm = get_setting_value($permissions, "banned_contents", 0);
	$import_export_perm = get_setting_value($permissions, "import_export", 0);
	$system_upgrade_perm = get_setting_value($permissions, "system_upgrade", 0);
	// end common permissions

	// cms permissions
	$cms_settings_perm = get_setting_value($permissions, "cms_settings", 0);
	$layouts_perm = get_setting_value($permissions, "layouts", 0);
	$site_navigation_perm = get_setting_value($permissions, "site_navigation", 0);
	$footer_links_perm = get_setting_value($permissions, "footer_links", 0);
	$filters_perm = get_setting_value($permissions, "filters", 0);
	$filemanager_perm = get_setting_value($permissions, "filemanager", 0);
	$web_pages_perm = get_setting_value($permissions, "web_pages", 0);
	$custom_blocks_perm = get_setting_value($permissions, "custom_blocks", 0);
	$static_messages_perm = get_setting_value($permissions, "static_messages", 0);
	$banners_perm = get_setting_value($permissions, "banners", 0);
	$polls_perm = get_setting_value($permissions, "polls", 0);
	$custom_friendly_urls_perm = get_setting_value($permissions, "custom_friendly_urls", 0);
	// end cms permissions

	// products permissions
	$products_categories_perm = get_setting_value($permissions, "products_categories", 0);
	$products_settings_perm = get_setting_value($permissions, "products_settings", 0);
	$product_types_perm = get_setting_value($permissions, "product_types", 0);
	$manufacturers_perm = get_setting_value($permissions, "manufacturers", 0);
	$suppliers_perm = get_setting_value($permissions, "suppliers", 0);
	$products_reviews_perm = get_setting_value($permissions, "products_reviews", 0);
	$products_reviews_sets_perm = get_setting_value($permissions, "products_reviews_settings", 0);
	$shipping_methods_perm = get_setting_value($permissions, "shipping_methods", 0);
	$shipping_times_perm = get_setting_value($permissions, "shipping_times", 0);
	$shipping_rules_perm = get_setting_value($permissions, "shipping_rules", 0);
	$downloadable_products_perm = get_setting_value($permissions, "downloadable_products", 0);
	$coupons_perm = get_setting_value($permissions, "coupons", 0);
	$saved_types_perm = get_setting_value($permissions, "saved_types", 0);
	$advanced_search_perm = get_setting_value($permissions, "advanced_search", 0);
	$products_report_perm = get_setting_value($permissions, "products_report", 0);
	$features_groups_perm = get_setting_value($permissions, "features_groups", 0);
	$tell_friend_perm = get_setting_value($permissions, "tell_friend", 0);
	// end products permissions

	// articles permissions
	$articles_perm = get_setting_value($permissions, "articles", 0);
	$articles_statuses_perm = get_setting_value($permissions, "articles_statuses", 0);
	$articles_reviews_perm = get_setting_value($permissions, "articles_reviews", 0);
	$articles_reviews_sets_perm = get_setting_value($permissions, "articles_reviews_settings", 0);
	$articles_lost_perm = get_setting_value($permissions, "articles_lost", 0);
	// end articles permissions

	// order permissions
	$sales_orders_perm = get_setting_value($permissions, "sales_orders", 0);
	$order_statuses_perm = get_setting_value($permissions, "order_statuses", 0);
	$order_profile_perm = get_setting_value($permissions, "order_profile", 0);
	$order_confirmation_perm = get_setting_value($permissions, "order_confirmation", 0);
	$tax_rates_perm = get_setting_value($permissions, "tax_rates", 0);
	$payment_systems_perm = get_setting_value($permissions, "payment_systems", 0);
	$orders_stats_perm = get_setting_value($permissions, "orders_stats", 0);
	$create_orders_perm = get_setting_value($permissions, "create_orders", 0); // for call center orders
	$orders_recover_perm = get_setting_value($permissions, "orders_recover", 0);
	// end order permissions

	// begin helpdesk permissions
	$support_perm = get_setting_value($permissions, "support", 0);
	$support_settings_perm = get_setting_value($permissions, "support_settings", 0);
	$support_users_perm = get_setting_value($permissions, "support_users", 0);
	$support_departments_perm = get_setting_value($permissions, "support_departments", 0);
	$support_predefined_reply_perm = get_setting_value($permissions, "support_predefined_reply", 0);
	$support_static_data_perm = get_setting_value($permissions, "support_static_data", 0);
	$support_users_stats_perm = get_setting_value($permissions, "support_users_stats", 0);
	$support_users_priorities_perm = get_setting_value($permissions, "support_users_priorities", 0);
	// end helpdesk permissions

	// begin ads permissions
	$ads_tell_friend_perm = get_setting_value($permissions, "ads", 0);
	// end ads permissions
	
	// begin registrations permissions
	$admin_registration_perm = get_setting_value($permissions, "admin_registration", 0);
	// end registrations permissions

	// begin site users permissions
	$site_users_perm = get_setting_value($permissions, "site_users", 0);
	$users_groups_perm = get_setting_value($permissions, "users_groups", 0);
	$users_forgot_perm = get_setting_value($permissions, "users_forgot", 0);
	$users_payments_perm = get_setting_value($permissions, "users_payments", 0);
	$subscriptions_perm = get_setting_value($permissions, "subscriptions", 0);
	$subscriptions_groups_perm = get_setting_value($permissions, "subscriptions_groups", 0);
	// end site users permissions

	// begin administrators permissions
	$admin_users_perm = get_setting_value($permissions, "admin_users", 0);
	$admins_groups_perm = get_setting_value($permissions, "admins_groups", 0);
	// end administrators permissions

	$t->set_file("admin_header", "admin_header.html");

	$t->set_var("CHARSET", CHARSET);
	$t->set_var("site_url", $site_url);
	$t->set_var("index_href", $site_url . "index.php");	
	$t->set_var("admin_href", $admin_site_url . "admin.php");
	$t->set_var("admin_orders_href", $admin_site_url . "admin_orders.php");	

	// System settings
	$t->set_var("admin_global_settings_href",	"admin_global_settings.php");
	$t->set_var("admin_sites_href",         	$admin_site_url . "admin_sites.php");
	$t->set_var("admin_admins_href",         	$admin_site_url . "admin_admins.php");
	$t->set_var("admin_privileges_href",     	$admin_site_url . "admin_privileges.php");
	$t->set_var("admin_black_ips_href",      	$admin_site_url . "admin_black_ips.php");
	$t->set_var("admin_banned_contents_href",	$admin_site_url . "admin_banned_contents.php");
	$t->set_var("admin_dump_href",           	$admin_site_url . "admin_dump.php");
	$t->set_var("admin_visits_report_href",  	$admin_site_url . "admin_visits_report.php");
	$t->set_var("admin_filemanager_href",    	$admin_site_url . "admin_fm.php");
	$t->set_var("admin_lookup_tables_href",  	$admin_site_url . "admin_lookup_tables.php");
	$t->set_var("admin_messages_href",          $admin_site_url . "admin_messages.php");
	$t->set_var("admin_upgrade_href",        	"admin_upgrade.php");

	// Products section
	$t->set_var("admin_items_list_href",       $admin_site_url . "admin_items_list.php");
	$t->set_var("admin_products_settings_href",$admin_site_url . "admin_products_settings.php");
	$t->set_var("admin_shipping_modules_href", $admin_site_url . "admin_shipping_modules.php");
	$t->set_var("admin_shipping_times_href",   $admin_site_url . "admin_shipping_times.php");
	$t->set_var("admin_shipping_rules_href",   $admin_site_url . "admin_shipping_rules.php");
	$t->set_var("admin_search_href",           $admin_site_url . "admin_search.php");
	$t->set_var("admin_download_info_href",    $admin_site_url . "admin_download_info.php");
	$t->set_var("admin_products_notify_href",  $admin_site_url . "admin_products_notify.php");
	$t->set_var("admin_products_report_href",  $admin_site_url . "admin_products_report.php");
	$t->set_var("admin_coupons_href",          $admin_site_url . "admin_coupons.php");
	$t->set_var("admin_products_edit_href",    $admin_site_url . "admin_products_edit.php");
	$t->set_var("admin_tell_friend_href",      $admin_site_url . "admin_tell_friend.php");
	$t->set_var("admin_item_types_href",            $admin_site_url . "admin_item_types.php");
	$t->set_var("admin_manufacturers_href",         $admin_site_url . "admin_manufacturers.php");
	$t->set_var("admin_suppliers_href",             $admin_site_url . "admin_suppliers.php");
	$t->set_var("admin_features_groups_href",       $admin_site_url . "admin_features_groups.php");
	$t->set_var("admin_saved_types_href",           $admin_site_url . "admin_saved_types.php");
	$t->set_var("admin_review_href",                $admin_site_url . "admin_review.php");
	$t->set_var("admin_reviews_href",               $admin_site_url . "admin_reviews.php");
	$t->set_var("admin_products_reviews_href",      $admin_site_url . "admin_products_reviews.php");
	$t->set_var("admin_products_reviews_sets_href", $admin_site_url . "admin_products_reviews_sets.php");

	// Orders management
	$t->set_var("admin_orders_href",             $orders_list_site_url . "admin_orders.php");
	$t->set_var("admin_coupons_href",            $orders_pages_site_url . "admin_coupons.php");
	$t->set_var("admin_order_info_href",         $orders_pages_site_url . "admin_order_info.php");
	$t->set_var("admin_order_statuses_href",     $orders_pages_site_url . "admin_order_statuses.php");
	$t->set_var("admin_tax_rates_href",          $orders_pages_site_url . "admin_tax_rates.php");
	$t->set_var("admin_orders_report_href",      $orders_pages_site_url . "admin_orders_report.php");
	$t->set_var("admin_order_confirmation_href", $orders_pages_site_url . "admin_order_confirmation.php");
	$t->set_var("admin_order_final_href",        $orders_pages_site_url . "admin_order_final.php");
	$t->set_var("admin_payment_systems_href",    $orders_pages_site_url . "admin_payment_systems.php");
	$t->set_var("admin_currencies_href",         $orders_pages_site_url . "admin_currencies.php");
	$t->set_var("admin_orders_products_report_href", $orders_pages_site_url . "admin_orders_products_report.php");
	$t->set_var("admin_orders_tax_report_href",      $orders_pages_site_url . "admin_orders_tax_report.php");
	$t->set_var("admin_order_printable_href", 	     $orders_pages_site_url . "admin_order_printable.php");
	$t->set_var("admin_orders_bom_settings_href", 	 $orders_pages_site_url . "admin_orders_bom_settings.php");
	$t->set_var("admin_orders_recover_href",         $orders_pages_site_url . "admin_orders_recover.php");	

	// helpdesk links
	$t->set_var("admin_support_href",               $tickets_site_url . "admin_support.php");
	$t->set_var("admin_support_settings_href",		$helpdesk_site_url . "admin_support_settings.php");
	$t->set_var("admin_support_ranks_href",			$helpdesk_site_url . "admin_support_ranks.php");
	$t->set_var("admin_support_priorities_href",	$helpdesk_site_url . "admin_support_priorities.php");
	$t->set_var("admin_support_statuses_href",		$helpdesk_site_url . "admin_support_statuses.php");
	$t->set_var("admin_support_products_href",		$helpdesk_site_url . "admin_support_products.php");
	$t->set_var("admin_support_types_href",         $helpdesk_site_url . "admin_support_types.php");
	$t->set_var("admin_support_settings_href",     	$helpdesk_site_url . "admin_support_settings.php");
	$t->set_var("admin_support_departments_href",  	$helpdesk_site_url . "admin_support_departments.php");
	$t->set_var("admin_support_prereplies_href",   	$helpdesk_site_url . "admin_support_prereplies.php");
	$t->set_var("admin_support_pretypes_href",   	$helpdesk_site_url . "admin_support_pretypes.php");
	$t->set_var("admin_support_admins_href",       	$helpdesk_site_url . "admin_support_admins.php");
	$t->set_var("admin_support_static_tables_href",	$helpdesk_site_url . "admin_support_static_tables.php");
	$t->set_var("admin_support_users_report_href",  $helpdesk_site_url . "admin_support_users_report.php");
	$t->set_var("admin_support_dep_edit_href",      $helpdesk_site_url . "admin_support_dep_edit.php");
	$t->set_var("admin_support_admin_edit_href",    $helpdesk_site_url . "admin_support_admin_edit.php");

	// forum
	$t->set_var("admin_forum_href",           $admin_site_url . "admin_forum.php");
	$t->set_var("admin_forum_settings_href",  $admin_site_url . "admin_forum_settings.php");
	$t->set_var("admin_forum_priorities_href",$admin_site_url . "admin_forum_priorities.php");
	$t->set_var("admin_icons_href",           $admin_site_url . "admin_icons.php");

	// CMS settings
	$t->set_var("admin_cms_href",            $admin_site_url . "admin_cms.php");
	$t->set_var("admin_layouts_href",        $admin_site_url . "admin_layouts.php");
	$t->set_var("admin_layout_page_href",    $admin_site_url . "admin_layout_page.php");
	$t->set_var("admin_header_menus_href",   $admin_site_url . "admin_header_menus.php");
	$t->set_var("admin_footer_links_href",   $admin_site_url . "admin_footer_links.php");
	$t->set_var("admin_custom_menus_href",   $admin_site_url . "admin_custom_menus.php");
	$t->set_var("admin_custom_blocks_href",  $admin_site_url . "admin_custom_blocks.php");
	$t->set_var("admin_friendly_urls_href",  $admin_site_url . "admin_friendly_urls.php");
	$t->set_var("admin_layout_header_href",  $admin_site_url . "admin_layout_header.php");
	$t->set_var("admin_pages_href",          $admin_site_url . "admin_pages.php");
	$t->set_var("admin_polls_href",          $admin_site_url . "admin_polls.php");
	$t->set_var("admin_filters_href",        $admin_site_url . "admin_filters.php");
	$t->set_var("admin_banners_href",        $admin_site_url . "admin_banners.php");

	// Articles & Manuals
	$t->set_var("admin_manual_href",                $admin_site_url . "admin_manual.php");
	$t->set_var("admin_articles_top_href",          $admin_site_url . "admin_articles_top.php");
	$t->set_var("admin_articles_statuses_href",     $admin_site_url . "admin_articles_statuses.php");
	$t->set_var("admin_articles_reviews_href",      $admin_site_url . "admin_articles_reviews.php");
	$t->set_var("admin_articles_reviews_sets_href", $admin_site_url . "admin_articles_reviews_sets.php");
	$t->set_var("admin_articles_lost_href",         $admin_site_url . "admin_articles_lost.php");


	// Site Users
	$t->set_var("admin_users_href",              $admin_site_url . "admin_users.php");
	$t->set_var("admin_user_types_href",         $admin_site_url . "admin_user_types.php");
	$t->set_var("admin_user_sections_href",      $admin_site_url . "admin_user_sections.php");
	$t->set_var("admin_forgotten_password_href", $admin_site_url . "admin_forgotten_password.php");
	$t->set_var("admin_user_payments_href",      $admin_site_url . "admin_user_payments.php");
	$t->set_var("admin_user_commissions_href",   $admin_site_url . "admin_user_commissions.php");
	$t->set_var("admin_newsletters_href",        $admin_site_url . "admin_newsletters.php");
	$t->set_var("admin_newsletter_users_href",   $admin_site_url . "admin_newsletter_users.php");
	$t->set_var("admin_subscriptions_href",      $admin_site_url . "admin_subscriptions.php");
	$t->set_var("admin_subscriptions_groups_href",$admin_site_url ."admin_subscriptions_groups.php");

	// Classified Ads
	$t->set_var("admin_ads_href",                 $admin_site_url . "admin_ads.php");
	$t->set_var("admin_ads_settings_href",        $admin_site_url . "admin_ads_settings.php");
	$t->set_var("admin_ads_notify_href",          $admin_site_url . "admin_ads_notify.php");
	$t->set_var("admin_ads_settings_href",        $admin_site_url . "admin_ads_settings.php");
	$t->set_var("admin_ads_search_href",          $admin_site_url . "admin_ads_search.php");
	$t->set_var("admin_ads_request_href",         $admin_site_url . "admin_ads_request.php");
	$t->set_var("admin_ads_features_groups_href", $admin_site_url . "admin_ads_features_groups.php");
	$t->set_var("admin_ads_types_href",           $admin_site_url . "admin_ads_types.php");
	$t->set_var("admin_ads_days_href",            $admin_site_url . "admin_ads_days.php");
	$t->set_var("admin_ads_hot_days_href",        $admin_site_url . "admin_ads_hot_days.php");
	$t->set_var("admin_ads_special_days_href",    $admin_site_url . "admin_ads_special_days.php");

	// Registrations
	$t->set_var("admin_registrations_href",         $admin_site_url . "admin_registrations.php");
	$t->set_var("admin_registration_products_href", $admin_site_url . "admin_registration_products.php");
	$t->set_var("admin_registration_settings_href", $admin_site_url . "admin_registration_settings.php");
	
	// bookmarks
	$t->set_var("admin_bookmark_href",  $admin_site_url . "admin_bookmark.php");
	$t->set_var("admin_bookmark_hrefs", $admin_site_url ."admin_bookmarks.php");


	// BEGIN product privileges
	if ($products_settings_perm) {
		$t->parse("products_settings_priv", false);
		$t->parse("products_settings_priv_sub", false);
	}

	if ($product_types_perm) {
		$t->parse("product_types_priv", false);
		$t->parse("product_types_priv_sub", false);
	}
	if ($manufacturers_perm) {
		$t->parse("manufacturers_priv", false);
	}
	if ($suppliers_perm) {
		$t->parse("suppliers_priv", false);
	}
	if ($products_reviews_perm) {
		$t->parse("products_reviews_priv", false);
		$t->parse("products_reviews_priv_sub", false);
	}
	if ($products_reviews_sets_perm) {
		$t->parse("products_reviews_sets_priv", false);
		$t->parse("products_reviews_sets_priv_sub", false);
	}
	if ($articles_statuses_perm) {
		$t->parse("articles_statuses_priv", false);
		$t->parse("articles_statuses_priv_sub", false);
	}
	if ($articles_reviews_perm) {
		$t->parse("articles_reviews_priv", false);
		$t->parse("articles_reviews_priv_sub", false);
	}
	if ($articles_reviews_sets_perm) {
		$t->parse("articles_reviews_sets_priv", false);
		$t->parse("articles_reviews_sets_priv_sub", false);
	}
	if ($articles_lost_perm) {
		$t->parse("articles_lost_priv", false);
		$t->parse("articles_lost_priv_sub", false);
	}
	if ($shipping_methods_perm) {
		$t->parse("shipping_methods_priv", false);
		$t->parse("shipping_methods_priv_sub", false);
	}
	if ($shipping_times_perm) {
		$t->parse("shipping_times_priv", false);
	}
	if ($shipping_rules_perm) {
		$t->parse("shipping_rules_priv", false);
	}
	if ($downloadable_products_perm) {
		$t->parse("downloadable_products_priv", false);
		$t->parse("downloadable_products_priv_sub", false);
	}
	if ($coupons_perm) {
		$t->parse("coupons_priv", false);
		$t->parse("coupons_priv_sub", false);
	}
	if ($saved_types_perm) {
		$t->parse("saved_types_priv", false);
		$t->parse("saved_types_priv_sub", false);
	}
	if ($advanced_search_perm) {
		$t->parse("advanced_search_priv", false);
		$t->parse("advanced_search_priv_sub", false);
	}
	if ($products_report_perm) {
		$t->parse("products_report_priv", false);
		$t->parse("products_report_priv_sub", false);
	}
	if ($features_groups_perm) {
		$t->parse("features_groups_priv", false);
		$t->parse("features_groups_priv_sub", false);
	}
	if ($tell_friend_perm) {
		$t->parse("products_tell_friend_priv", false);
		$t->parse("products_tell_friend_sub", false);
	}
	if ($layouts_perm) {
		$t->parse("list_page_priv_sub", false);
		$t->parse("details_page_priv_sub", false);
	}
	// END product privileges

	// BEGIN parse privileges
	if ($ads_tell_friend_perm) {
		$t->parse("ads_tell_friend_priv", false);
		$t->parse("ads_tell_friend_sub", false);
	}


	// set Call Center link
	$t->set_var("admin_order_call_href", $admin_order_call_url);

	$ads_menu = array(
		"admin_ads.php" => true, "admin_ads_assign.php" => true, "admin_ads_categories.php" => true,
		"admin_ads_category.php" => true, "admin_ads_edit.php" => true,
		"admin_ads_features.php" => true, "admin_ads_features_default.php" => true, "admin_ads_features_group.php" => true,
		"admin_ads_features_groups.php" => true, "admin_ads_image.php" => true, "admin_ads_images.php" => true,
		"admin_ads_settings.php" => true, "admin_ads_notify.php" => true, "admin_ads_notify_help.php" => true,
		"admin_ads_properties.php" => true, "admin_ads_properties_default.php" => true, "admin_ads_request.php" => true,
		"admin_ads_search.php" => true, "admin_ads_type.php" => true, "admin_ads_types.php" => true,
		"admin_ads_days.php" => true, "admin_ads_hot_days.php" => true, "admin_ads_special_days.php" => true,
		"admin_ads_day.php" => true, "admin_ads_hot_day.php" => true, "admin_ads_special_day.php" => true,
	);

	$users_menu = array(
		"admin_user.php" => true, "admin_user_login.php" => true, "admin_users.php" => true,
		"admin_user_types.php" => true, "admin_user_type.php" => true,
		"admin_forgotten_password.php" => true, "admin_registration.php" => true,
		"admin_user_profile_help.php" => true, "admin_user_profile.php" => true,
		"admin_newsletters.php" => true, "admin_newsletter.php" => true,
		"admin_user_payments.php" => true, "admin_user_payment.php" => true,
		"admin_user_commissions.php" => true, "admin_newsletter_users.php" => true,
		"admin_user_sections.php" => true, "admin_user_section.php" => true,
		"admin_user_property.php" => true, "admin_user_product.php" => true,
		"admin_newsletter_users_edit.php" => true, "admin_user_change_type.php" => true, 
		"admin_subscriptions.php" => true, "admin_subscription.php" => true, 
		"admin_subscriptions_groups.php" => true, "admin_subscriptions_group.php" => true, 
		"admin_user_contact.php" => true,
	);

	$shop_menu = array(
		"admin_categories_order.php" => true, "admin_category_edit.php" => true, 
		"admin_item_categories.php" => true, "admin_items_list.php" => true, "admin_product.php" => true,
		"admin_products_order.php" => true, "admin_properties.php" => true, "admin_properties_add.php" => true,
		"admin_property.php" => true, "admin_release.php" => true, "admin_release_changes.php" => true,
		"admin_component_single.php" => true, "admin_component_selection.php" => true,
		"admin_release_type.php" => true, "admin_release_types.php" => true, "admin_releases.php" => true,
		"admin_select.php" => true, "admin_search.php" => true, "admin_download_info.php" => true,
		"admin_shipping_rule.php" => true, "admin_shipping_rules.php" => true, "admin_shipping_time.php" => true,
		"admin_shipping_times.php" => true, "admin_shipping_modules.php" => true, "admin_shipping_module.php" => true,
		"admin_shipping_types.php" => true, "admin_shipping_type.php" => true,
		"admin_table_categories.php" => true, "admin_table_items.php" => true,
		"admin_coupons.php" => true, "admin_default_features.php" => true,
		"admin_item_type.php" => true, "admin_item_types.php" => true,
		"admin_manufacturer.php" => true, "admin_manufacturers.php" => true,
		"admin_supplier.php" => true, "admin_suppliers.php" => true,
		"admin_features_group.php" => true, "admin_features_groups.php" => true,
		"admin_item_features.php" => true, "admin_item_accessories.php" => true,
		"admin_item_images.php" => true, "admin_item_image.php" => true,
		"admin_products_report.php" => true, "admin_item_prices.php" => true,
		"admin_products_settings.php" => true, "admin_products_notify.php" => true,
		"admin_saved_types.php" => true, "admin_saved_type.php" => true, "admin_products_copy_properties.php" => true,
		"admin_item_related.php" => true,
		"admin_item_articles_related.php" => true,
		"admin_item_forums_related.php" => true,
		"admin_review.php" => true,
		"admin_reviews.php" => true,
		"admin_products_reviews.php" => true,
		"admin_products_reviews_sets.php" => true,
	);

	$forum_menu = array(
		"admin_forum.php" => true, "admin_forum_help.php" => true, "admin_forum_message.php" => true,
		"admin_forum_settings.php" => true, "admin_forum_thread.php" => true, "admin_forum_topic.php" => true,
		"admin_forum_category.php" => true, "admin_forum_edit.php" => true, "admin_forum_topic.php" => true,
		"admin_forum_priorities.php" => true, "admin_forum_priority.php" => true,
	);

	$orders_menu = array(
		"admin_cc_expiry_year.php" => true, "admin_cc_expiry_years.php" => true,
		"admin_cc_start_year.php" => true, "admin_cc_start_years.php" => true, "admin_credit_card.php" => true,
		"admin_payment_help.php" => true , "admin_payment_system.php" => true, "admin_payment_systems.php" => true,
		"admin_credit_cards.php" => true, "admin_credit_card_info.php" => true, "admin_order.php" => true,
		"admin_order_confirmation.php" => true, "admin_order_final.php" => true, "admin_order_help.php" => true,
		"admin_order_info.php" => true, "admin_order_link.php" => true, "admin_order_links.php" => true,
		"admin_order_status.php" => true, "admin_order_statuses.php" => true, "admin_orders.php" =>true,
		"admin_order_notes.php" => true, "admin_order_note.php" => true, "admin_order_property.php" => true,
		"admin_tax_rate.php" => true, "admin_tax_rates.php" => true,
		"admin_orders_report.php" => true, "admin_orders_products_report.php" => true, "admin_orders_tax_report.php" => true,
		"admin_order_printable.php" => true, "admin_invoice_html.php" => true,
		"admin_orders_bom_settings.php" => true, "admin_orders_bom_column.php" => true,
		"admin_order_serials.php" => true, "admin_order_serial.php" => true,
		"admin_order_vouchers.php" => true, "admin_order_email.php" => true, "admin_order_call.php" => true,
		"admin_recurring_settings.php" => true,
		"admin_orders_recover.php" => true, "admin_orders_recover_settings.php" => true,
	);

	$cms_menu = array(
		"admin_custom_block.php" => true, "admin_custom_blocks.php" => true,
		"admin_friendly_urls.php" => true, "admin_friendly_url.php" => true,
		"admin_layout.php" => true, "admin_layout_header.php" => true,
		"admin_layout_scheme.php" => true, "admin_cms.php" => true, "admin_layouts.php" => true,
		"admin_custom_menus.php" => true, "admin_custom_menu.php" => true,
		"admin_menu_items.php" => true, "admin_menu_item_edit.php" => true, "admin_menu_datasheet.php" => true,
		"admin_header_menus.php" => true, "admin_menu_item.php" => true,
		"admin_page.php" => true, "admin_pages.php" =>true, 
		"admin_poll.php" => true, "admin_polls.php" => true,
		"admin_filters.php" => true, "admin_filter.php" => true,
		"admin_filter_properties.php" => true, "admin_filter_property.php" => true,
		"admin_filemanager.php" => true,
		"admin_banners.php" => true, "admin_banner.php" => true,
		"admin_banners_groups.php" => true, "admin_banners_group.php" => true,
		"admin_footer_links.php" => true, "admin_footer_link.php" => true,
	);

	$articles_menu = array(
		"admin_article.php" => true, "admin_article_related.php" => true, "admin_articles.php" => true,
		"admin_articles_assign.php" => true, "admin_articles_categories.php" => true, "admin_articles_category.php" => true,
		"admin_articles_order.php" => true, "admin_articles_top.php" => true,
		"admin_articles_statuses.php" => true, "admin_articles_status.php" => true,
		"admin_article_review.php" => true,
		"admin_articles_reviews.php" => true,
		"admin_articles_reviews_sets.php" => true,
	);

	$helpdesk_menu = array(
		"admin_support.php" => true, "admin_support_help.php" => true,
		"admin_support_message.php" => true, "admin_support_priorities.php" => true,
		"admin_support_priority.php" => true, "admin_support_product.php" => true,
		"admin_support_products.php" => true, "admin_support_reply.php" => true,
		"admin_support_request.php" => true, "admin_support_settings.php" => true,
		"admin_support_status.php" => true, "admin_support_statuses.php" => true,
		"admin_support_static_tables.php" => true, "admin_support_types.php" => true,
		"admin_support_departments.php" => true, "admin_support_admins.php" => true,
		"admin_support_prereplies.php" => true, "admin_support_prereply.php" => true,
		"admin_support_pretypes.php" => true, "admin_support_pretype.php" => true,
		"admin_support_dep_edit.php" => true, "admin_support_admin_edit.php" => true,
		"admin_support_type.php" => true, "admin_support_password.php" => true,
		"admin_support_ranks.php" => true, "admin_support_rank.php" => true,
		"admin_support_users_report.php" => true
	);

	$manual_menu = array(
		"admin_manual.php" => true, "admin_manual_article.php" => true,
		"admin_manual_category.php" => true, "admin_manual_edit.php" => true
	);

	$system_menu = array(
		"admin_admin.php" => true, "admin_admins.php" => true,
		"admin_admin_password.php" => true,
		"admin_registration_help.php" => true, "admin_global_settings.php" => true,"admin_sites.php" => true,
		"admin_lookup_tables.php" => true,
		"admin_countries.php" => true, "admin_country.php" => true,
		"admin_messages.php" => true,	"admin_message.php" => true,
		"admin_languages.php" => true, "admin_language.php" => true,
		"admin_change_type.php" => true, "admin_change_types.php" => true,
		"admin_privileges.php" => true, "admin_privileges_edit.php" => true,
		"admin_issue_number.php" => true, "admin_issue_numbers.php" => true,
		"admin_state.php" => true, "admin_states.php" => true, "admin_upgrade.php" => true,
		"admin_currencies.php" => true, "admin_currency.php" => true,
		"admin_companies.php" => true, "admin_company.php" => true,
		"admin_black_ips.php" => true, "admin_black_ip.php" => true,
		"admin_banned_contents.php" => true, "admin_banned_content.php" => true,
		"admin_visits_report.php" => true, "admin_dump.php" => true,
		"admin_dump_apply.php" => true, "admin_dump_create.php" => true
	);


	$table = get_param("table");
	if ($table == "users") {
		$system_menu["admin_import.php"] = true;
		$system_menu["admin_export.php"] = true;
	} elseif ($table == "items" || $table == "categories") {
		$shop_menu["admin_import.php"] = true;
		$shop_menu["admin_export.php"] = true;
	}
	$art_cat_id = get_param("art_cat_id");
	$type = get_param("type");
	if (strlen($art_cat_id)) {
		$articles_menu["admin_tell_friend.php"] = true;
	} else {
		if($type == "products") {
			$shop_menu["admin_tell_friend.php"] = true;
		} elseif ($type == "ads") {
			$ads_menu["admin_tell_friend.php"] = true;
		}
	}
	$rp = get_param("rp");
	$page_name = get_param("page_name");
	if ($rp == "admin_layouts.php" || $rp == "admin_cms.php") {
		$cms_menu["admin_layout_page.php"] = true;
	} elseif ($page_name == "products_list" || $page_name == "products_details") {
		$shop_menu["admin_layout_page.php"] = true;
	} elseif ($page_name == "ads_list" || $page_name == "ads_list") {
		$ads_menu["admin_layout_page.php"] = true;
	} elseif (strlen($art_cat_id)) {
		$articles_menu["admin_layout_page.php"] = true;
	} else {
		$cms_menu["admin_layout_page.php"] = true;
	}
	$order_id = get_param("order_id");
	if ($order_id > 0) {
		$orders_menu["admin_coupon.php"] = true;
	} else {
		$shop_menu["admin_coupon.php"] = true;
	}

	$cur_script_name = get_script_name();

	if (isset($system_menu[$cur_script_name]) && $system_menu[$cur_script_name]) { $section = "system"; }
	elseif (isset($cms_menu[$cur_script_name]) && $cms_menu[$cur_script_name]) { $section = "cms"; }
	elseif (isset($helpdesk_menu[$cur_script_name]) && $helpdesk_menu[$cur_script_name]) { $section = "helpdesk"; }
	elseif (isset($shop_menu[$cur_script_name]) && $shop_menu[$cur_script_name]) { $section = "shop"; }
	elseif (isset($orders_menu[$cur_script_name]) && $orders_menu[$cur_script_name]) { $section = "orders"; }
	elseif (isset($forum_menu[$cur_script_name]) && $forum_menu[$cur_script_name]) { $section = "forum"; }
	elseif (isset($ads_menu[$cur_script_name]) && $ads_menu[$cur_script_name]) { $section = "ads"; }
	elseif (isset($users_menu[$cur_script_name]) && $users_menu[$cur_script_name]) { $section = "users"; }
	elseif (isset($articles_menu[$cur_script_name]) && $articles_menu[$cur_script_name]) { $section = "articles"; }
	elseif (isset($manual_menu[$cur_script_name]) && $manual_menu[$cur_script_name]) { $section = "manual"; }
	else { $section = ""; }

	$version_number = get_db_value("SELECT setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='version' AND setting_name='number'");
	if (!$version_number) $version_number = VA_RELEASE;
	$version_name = ucfirst($va_name);
	$version_type = ucfirst($va_type);
	$t->set_var("version_number", $version_number);
	$t->set_var("version_name", $version_name);
	if ($version_type != $version_name) {
		$t->set_var("version_type", $version_type);
	}

	/* system menu */
	if ($global_settings_perm) {
		$t->parse("global_settings", false);
		$t->parse("global_settings_menu", false);
	} else {
		$t->set_var("global_settings", "");
		$t->set_var("global_settings_menu", "");
	}
	if ($sites_perm) {
		$t->parse("sites", false);
		$t->parse("sites_menu", false);
	} else {
		$t->set_var("sites", "");
		$t->set_var("sites_menu", "");
	}
	if ($admin_users_perm) {
		$t->parse("admin_users_submenu", false);
		$t->parse("admin_users_menu", false);
	} else {
		$t->set_var("admin_users_submenu", "");
		$t->set_var("admin_users_menu", "");
	}
	if ($newsletter_perm) {
		$t->parse("newsletter_menu", false);
		$t->parse("newsletter_submenu", false);
	}



	if ($admins_groups_perm) {
		$t->parse("admins_groups_submenu", false);
		$t->parse("admins_groups_menu", false);
	} else {
		$t->set_var("admins_groups_submenu", "");
		$t->set_var("admins_groups_menu", "");
	}
	if ($static_tables_perm) {
		$t->parse("static_tables_submenu", false);
		$t->parse("static_tables_menu", false);
	} else {
		$t->set_var("static_tables_submenu", "");
		$t->set_var("static_tables_menu", "");
	}
	if ($black_ips_perm) {$t->parse("black_ips_submenu",false); $t->parse("black_ips_menu",false);}
		else {$t->set_var("black_ips_submenu",""); $t->set_var("black_ips_menu","");}
	if ($static_messages_perm) {$t->parse("static_messages_menu",false);$t->parse("static_messages_submenu",false);}
		else {$t->set_var("static_messages_menu","");$t->set_var("static_messages_submenu","");}
	if ($visits_report_perm) {$t->parse("visits_report_submenu",false); $t->parse("visits_report_menu",false);}
		else {$t->set_var("visits_report_submenu",""); $t->set_var("visits_report_menu","");}
	if ($db_management_perm) {$t->parse("db_management_submenu",false); $t->parse("db_management_menu",false);}
		else {$t->set_var("db_management_submenu",""); $t->set_var("db_management_menu","");}
	if ($system_upgrade_perm) {$t->parse("system_upgrade_submenu",false); $t->parse("system_upgrade_menu",false);}
		else {$t->set_var("system_upgrade_submenu",""); $t->set_var("system_upgrade_menu","");}
	if ($banned_contents_perm) {$t->parse("banned_contents_submenu",false); $t->parse("banned_contents_menu",false);}
		else {$t->set_var("banned_contents_submenu",""); $t->set_var("banned_contents_menu","");}

	/* cms menu */
	if ($layouts_perm) {	
		$t->parse("custom_menus_submenu",false); 
		$t->parse("custom_menus_menu",false);
	}
	if ($custom_blocks_perm) {	
		$t->parse("custom_blocks_submenu",false); 
		$t->parse("custom_blocks_menu",false);
	}	
	if ($custom_friendly_urls_perm) {
		$t->parse("custom_friendly_urls_submenu",false); 
		$t->parse("custom_friendly_urls_menu",false);
	}
	if ($filters_perm) {
		$t->parse("filters_submenu",false); 
		$t->parse("filters_menu",false);
	}
	if ($cms_settings_perm) {
		$t->parse("cms_settings_menu",false); 
		$t->parse("cms_settings_submenu",false); 
	}
	if ($layouts_perm) {
		$t->parse("layouts_submenu",false); 
		$t->parse("layouts_menu",false);
	}
	if ($site_navigation_perm) {
		$t->parse("site_nav_submenu",false); 
		$t->parse("site_nav_menu",false);
	}
	if ($footer_links_perm) {
		$t->parse("footer_links_submenu",false); 
		$t->parse("footer_links_menu",false);
	}
	if ($filemanager_perm) {$t->parse("filemanager_submenu",false); $t->parse("filemanager_menu",false);}
		else {$t->set_var("filemanager_submenu",""); $t->set_var("filemanager_menu","");}
	if ($web_pages_perm) {$t->parse("web_pages_submenu",false); $t->parse("web_pages_menu",false);}
		else {$t->set_var("web_pages_submenu",""); $t->set_var("web_pages_menu","");}
	if ($polls_perm) {$t->parse("polls_submenu",false); $t->parse("polls_menu",false);}
		else {$t->set_var("polls_submenu",""); $t->set_var("polls_menu","");}
	if ($banners_perm) {$t->parse("banners_submenu",false); $t->parse("banners_menu",false);}
		else {$t->set_var("banners_submenu",""); $t->set_var("banners_menu","");}

	/* helpdesk menu*/
	if ($support_perm) {$t->parse("support_submenu",false); $t->parse("support_menu",false);}
		else {$t->set_var("support_submenu",""); $t->set_var("support_menu","");}
	if ($support_settings_perm) {$t->parse("support_settings_submenu",false); $t->parse("support_settings_menu",false);}
		else {$t->set_var("support_settings_submenu",""); $t->set_var("support_settings_menu","");}
	if ($support_users_perm) {$t->parse("support_users_submenu",false); $t->parse("support_users_menu",false);}
		else {$t->set_var("support_users_submenu",""); $t->set_var("support_users_menu","");}
	if ($support_departments_perm) {$t->parse("support_departments_submenu",false); $t->parse("support_departments_menu",false);}
	if ($support_predefined_reply_perm) {
		$t->parse("support_predefined_reply_submenu",false); 
		$t->parse("support_predefined_reply_menu",false);
		$t->parse("support_pretypes_submenu",false); 
		$t->parse("support_pretypes_menu",false);
	}
	if ($support_static_data_perm) {$t->parse("support_static_data_submenu",false); $t->parse("support_static_data_menu",false);}
		else {$t->set_var("support_static_data_submenu",""); $t->set_var("support_static_data_menu","");}
	if ($support_users_stats_perm) {$t->parse("support_users_stats_submenu",false); $t->parse("support_users_stats_menu",false);}
		else {$t->set_var("support_users_stats_submenu",""); $t->set_var("support_users_stats_menu","");}
	if ($support_users_priorities_perm) {$t->parse("support_users_priorities_submenu",false); $t->parse("support_users_priorities_menu",false);}
		else {$t->set_var("support_users_priorities_submenu",""); $t->set_var("support_users_priorities_menu","");}

	/* site users menu */
	if ($site_users_perm) {$t->parse("site_users_submenu",false); $t->parse("site_users_menu",false);}
	if ($users_groups_perm) {
		$t->parse("users_groups_submenu",false); 
		$t->parse("users_groups_menu",false);
	} 
	if ($users_forgot_perm) {$t->parse("users_forgot_submenu",false); $t->parse("users_forgot_menu",false);}
	if ($users_payments_perm) {$t->parse("users_payments_submenu",false); $t->parse("users_payments_menu",false);}
	if ($subscriptions_perm) {
		$t->parse("subscriptions_submenu",false); 
		$t->parse("subscriptions_menu",false);
	} 
	if ($subscriptions_groups_perm) {
		$t->parse("subscriptions_groups_submenu",false); 
		$t->parse("subscriptions_groups_menu",false);
	} 


	/* orders menu */
	if ($sales_orders_perm) {$t->parse("sales_orders_submenu",false); $t->parse("sales_orders_menu",false);}
		else {$t->set_var("sales_orders_submenu",""); $t->set_var("sales_orders_menu","");}
	if ($order_statuses_perm) {$t->parse("order_statuses_submenu",false); $t->parse("order_statuses_menu",false);}
		else {$t->set_var("order_statuses_submenu",""); $t->set_var("order_statuses_menu","");}
	if ($order_profile_perm) {$t->parse("order_profile_submenu",false); $t->parse("order_profile_menu",false);}
		else {$t->set_var("order_profile_submenu",""); $t->set_var("order_profile_menu","");}
	if ($order_confirmation_perm) {$t->parse("order_confirmation_submenu",false); $t->parse("order_confirmation_menu",false);}
		else {$t->set_var("order_confirmation_submenu",""); $t->set_var("order_confirmation_menu","");}
	if ($tax_rates_perm) {$t->parse("tax_rates_submenu",false); $t->parse("tax_rates_menu",false);}
		else {$t->set_var("tax_rates_submenu",""); $t->set_var("tax_rates_menu","");}
	if ($payment_systems_perm) {$t->parse("payment_systems_submenu",false); $t->parse("payment_systems_menu",false);}
		else {$t->set_var("payment_systems_submenu",""); $t->set_var("payment_systems_menu","");}
	if ($orders_stats_perm) {$t->parse("orders_stats_submenu",false); $t->parse("orders_stats_menu",false);}
		else {$t->set_var("orders_stats_submenu",""); $t->set_var("orders_stats_menu","");}
	if ($create_orders_perm) {$t->parse("create_orders_submenu",false); $t->parse("create_orders_menu",false);}
		else {$t->set_var("create_orders_submenu",""); $t->set_var("create_orders_menu","");}
	if ($orders_recover_perm) {$t->parse("sales_orders_submenu",false); $t->parse("orders_recover_menu",false);}
		else {$t->set_var("sales_orders_submenu",""); $t->set_var("orders_recover_menu","");}
	// parse articles menu
	if (($va_license_code & 2) && ($articles_perm || $articles_statuses_perm || $articles_reviews_perm || $articles_reviews_sets_perm || $articles_lost_perm )) {
		if ($articles_perm) {
			$sql  = " SELECT ac.category_id, ac.category_name ";
			$sql .= " FROM " . $table_prefix . "articles_categories ac ";
			$sql .= " WHERE ac.parent_category_id=0 ";
			$sql .= " ORDER BY ac.category_order ";
			$db->query($sql);
			if ($db->next_record()) {
				do {
					$articles_category_id = $db->f("category_id");
					$admin_menu_articles_url = "admin_articles.php?category_id=" . $articles_category_id;
					$t->set_var("menu_top_article", get_translation($db->f("category_name")));
					$t->set_var("admin_menu_articles_url", $admin_menu_articles_url);
					$t->parse("menu_top_articles", true);
					$t->parse("submenu_top_articles", true);
				} while($db->next_record());
			}
		}
		$t->parse("menu_articles",false);
	}

	// shop - 1, cms - 2, helpdesk - 4, forum - 8, ads - 16, manuals - 32
 	if (($va_license_code & 1) && ($products_categories_perm || $products_settings_perm || $product_types_perm
 		|| $manufacturers_perm || $suppliers_perm || $products_reviews_perm || $products_reviews_sets_perm || $shipping_methods_perm || $shipping_times_perm || $shipping_rules_perm
 		|| $downloadable_products_perm || $coupons_perm || $saved_types_perm || $advanced_search_perm || $products_report_perm))
 	{
		$t->parse("menu_shop", false);
	}

	if (($va_license_code & 1) && ($sales_orders_perm || $order_statuses_perm || $order_profile_perm || $order_confirmation_perm
		|| $tax_rates_perm || $payment_systems_perm || $orders_stats_perm || $create_orders_perm))
	{
		$t->parse("menu_orders", false);
	}
	if (($va_license_code & 4) && ($support_perm || $support_settings_perm || $support_users_perm || $support_departments_perm
		|| $support_predefined_reply_perm || $support_static_data_perm || $support_users_stats_perm || $support_users_priorities_perm))
	{
		$t->parse("menu_helpdesk", false);
	}
	if (($va_license_code & 8) && $forum_perm) {
		$t->parse("menu_forum", false);
	}
	if (($va_license_code & 16) && $ads_perm) {
		$t->parse("menu_ads", false);
	}
	if ($admin_registration_perm) {
		$t->parse("menu_registrations", false);
	}
	if (($va_license_code & 32) && $manual_perm) {
		$t->parse("menu_manual", false);
	}
	if ($custom_blocks_perm || $custom_friendly_urls_perm || $layouts_perm || $cms_settings_perm || $site_navigation_perm 
		|| $footer_links_perm || $filemanager_perm || $web_pages_perm || $polls_perm || $filters_perm || $banners_perm) {
		$t->parse("menu_cms", false);
	}
	if ($global_settings_perm || $sites_perm || $admin_users_perm || $static_tables_perm || $black_ips_perm || $static_messages_perm
		|| $visits_report_perm || $db_management_perm ||  $banned_contents_perm || $admins_groups_perm)
	{
		$t->parse("menu_system", false);
	}
	if ($site_users_perm || $users_groups_perm || $users_forgot_perm || $users_payments_perm) {
		$t->parse("menu_site_users", false);
	}


	$titles = array(
		"system"   => SYSTEM_MSG,
		"cms"      => CMS_MSG,
		"helpdesk" => HELPDESK_MSG,
		"shop"     => PRODUCTS_MSG,
		"forum"    => ADMIN_FORUM_TITLE,
		"orders"   => ORDERS_MSG,
		"ads"      => CLASSIFIED_ADS_MSG,
		"users"    => SITE_USERS_MSG,
		"articles" => ARTICLES_TITLE,
		"manual"   => ADMIN_MANUAL_MSG
	);

	if ($section) {
		$t->set_var("header_menu_title", $titles[$section]);
		$t->sparse("submenu_".$section,false);
	} else {
		$t->set_var("header_menu_title", ADMINISTRATION_MENU_MSG);
	}


	// begin bookmarks
	$session_admin_id = get_session("session_admin_id");
	$current_version = va_version();
	// bookmarks are available only from version 2.8.1
	if (comp_vers($current_version, "2.8.1") <= 1) {
		$t->set_var("bookmark", "");
		$bookmarks = array();
		$sql  = " SELECT bookmark_id, title, url, notes, is_popup, image_path ";
		$sql .= " FROM " . $table_prefix . "bookmarks ";
		$sql .= " WHERE admin_id = " . $db->tosql($session_admin_id, INTEGER, true, false);
		$sql .= " ORDER BY title ";
		$db->query($sql);
		while ($db->next_record()) {
			$bookmark_id = $db->f("bookmark_id");
			$bookmark_values = array(
				"bookmark_id" => $bookmark_id,
				"url" => $db->f("url"),
				"title" => $db->f("title"),
				"notes" => $db->f("notes"),
				"is_popup" => $db->f("is_popup"),
				"image_path" => $db->f("image_path")
			);
			$bookmarks[$bookmark_id][] = $bookmark_values;
		}

		foreach ($bookmarks as $bookmark_id => $bookmark_values)
		{
			$count = sizeof($bookmark_values);
			for ($i = 0; $i < $count; $i++)
			{
				$bookmark_id = $bookmark_values[$i]["bookmark_id"];
				$url = $bookmark_values[$i]["url"];
				$title = $bookmark_values[$i]["title"];
				$notes = $bookmark_values[$i]["notes"];
				$is_popup = $bookmark_values[$i]["is_popup"];
				$src = $bookmark_values[$i]["image_path"];

				$t->set_var("header_bookmark_id",  $bookmark_id);
				$t->set_var("header_bookmark_url",  $url);
				$t->set_var("header_bookmark_title", $title);
				$t->set_var("header_bookmark_notes", $notes);

				if (strlen($src)!=0) {
					$t->set_var("src", $src);
				} else {
					$t->set_var("src", "../images/icons/no-img.gif");
				}

				if ($is_popup == 1) {
					$t->set_var("target", "_blank");
				} else {
					$t->set_var("target", "_self");
				}

				$t->sparse("bookmark", true);
			}
		}
		$t->sparse("bookmarks", false);
	} else {
		$t->set_var("bookmarks", "");
	}
	// end bookmarks

	$t->parse("admin_header", false);

?>