<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin.php                                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");

	if (!strlen(get_session("session_admin_id")) || !strlen(get_session("session_admin_privilege_id"))) {
		// admin is not logged in, redirect him to login form
		header ("Location: admin_login.php");
		exit;
	}

	check_admin_security();

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin.html");

	$t->set_var("admin_href", $admin_site_url . "admin.php");
	$t->set_var("admin_href_encode", urlencode($admin_site_url."admin.php"));

	// System settings
	$t->set_var("admin_global_settings_href","admin_global_settings.php");
	$t->set_var("admin_admins_href",         $admin_site_url . "admin_admins.php");
	$t->set_var("admin_privileges_href",     $admin_site_url . "admin_privileges.php");
	$t->set_var("admin_black_ips_href",      $admin_site_url . "admin_black_ips.php");
	$t->set_var("admin_banned_contents_href",$admin_site_url . "admin_banned_contents.php");
	$t->set_var("admin_dump_href",           $admin_site_url . "admin_dump.php");
	$t->set_var("admin_upgrade_href",        "admin_upgrade.php");
	$t->set_var("admin_visits_report_href",  $admin_site_url . "admin_visits_report.php");
	$t->set_var("admin_filemanager_href",    $admin_site_url . "admin_fm.php");
	$t->set_var("admin_lookup_tables_href",  $admin_site_url . "admin_lookup_tables.php");
	$t->set_var("admin_static_messages_href",$admin_site_url . "admin_messages.php");

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
	$t->set_var("admin_item_types_href",       $admin_site_url . "admin_item_types.php");
	$t->set_var("admin_manufacturers_href",    $admin_site_url . "admin_manufacturers.php");
	$t->set_var("admin_suppliers_href",        $admin_site_url . "admin_suppliers.php");
	$t->set_var("admin_features_groups_href",  $admin_site_url . "admin_features_groups.php");
	$t->set_var("admin_saved_types_href",      $admin_site_url . "admin_saved_types.php");

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

	// helpdesk links
	$t->set_var("admin_support_href",               $tickets_site_url . "admin_support.php");
	$t->set_var("admin_support_settings_href",			$helpdesk_site_url . "admin_support_settings.php");
	$t->set_var("admin_support_ranks_href",					$helpdesk_site_url . "admin_support_ranks.php");
	$t->set_var("admin_support_priorities_href",		$helpdesk_site_url . "admin_support_priorities.php");
	$t->set_var("admin_support_statuses_href",			$helpdesk_site_url . "admin_support_statuses.php");
	$t->set_var("admin_support_products_href",			$helpdesk_site_url . "admin_support_products.php");
	$t->set_var("admin_support_types_href",					$helpdesk_site_url . "admin_support_types.php");
	$t->set_var("admin_support_settings_href",     	$helpdesk_site_url . "admin_support_settings.php");
	$t->set_var("admin_support_departments_href",  	$helpdesk_site_url . "admin_support_departments.php");
	$t->set_var("admin_support_prereplies_href",   	$helpdesk_site_url . "admin_support_prereplies.php");
	$t->set_var("admin_support_pretypes_href",   	  $helpdesk_site_url . "admin_support_pretypes.php");
	$t->set_var("admin_support_admins_href",       	$helpdesk_site_url . "admin_support_admins.php");
	$t->set_var("admin_support_static_tables_href",	$helpdesk_site_url . "admin_support_static_tables.php");
	$t->set_var("admin_support_users_report_href",   $helpdesk_site_url . "admin_support_users_report.php");
	$t->set_var("admin_support_dep_edit_href",       $helpdesk_site_url . "admin_support_dep_edit.php");
	$t->set_var("admin_support_admin_edit_href",     $helpdesk_site_url . "admin_support_admin_edit.php");

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
	$t->set_var("admin_pages_href",          $admin_site_url . "admin_pages.php");
	$t->set_var("admin_friendly_urls_href",  $admin_site_url . "admin_friendly_urls.php");
	$t->set_var("admin_layout_header_href",  $admin_site_url . "admin_layout_header.php");
	$t->set_var("admin_polls_href",          $admin_site_url . "admin_polls.php");
	$t->set_var("admin_filters_href",        $admin_site_url . "admin_filters.php");
	$t->set_var("admin_banners_href",        $admin_site_url . "admin_banners.php");

	// Articles & Manuals                     
	$t->set_var("admin_reviews_href",           $admin_site_url . "admin_reviews.php");
	$t->set_var("admin_products_reviews_sets_href",$admin_site_url . "admin_products_reviews_sets.php");
	$t->set_var("admin_manual_href",            $admin_site_url . "admin_manual.php");
	$t->set_var("admin_articles_top_href",      $admin_site_url . "admin_articles_top.php");
	$t->set_var("admin_articles_statuses_href", $admin_site_url . "admin_articles_statuses.php");

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
	
	// Registrations
	$t->set_var("admin_registrations_href",         $admin_site_url . "admin_registrations.php");
	$t->set_var("admin_registration_products_href", $admin_site_url . "admin_registration_products.php");
	$t->set_var("admin_registration_settings_href", $admin_site_url . "admin_registration_settings.php");


	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$admin_blocks = array();
	if ($va_license_code & 1) {
		if ($products_categories_perm) {
			$admin_blocks[] = "products_categories";
		}
		$admin_blocks[] = "sales_orders";
	}
	$admin_blocks[] = "cms_settings";
	if ($va_license_code & 4) {
		$admin_blocks[] = "support";
	}
	$admin_blocks[] = "site_users";
	if ($va_license_code & 8) {
		$admin_blocks[] = "forum";
	}
	if ($va_license_code & 32) {
		$admin_blocks[] = "manual";
	}
	if ($va_license_code & 2) {
		$admin_blocks[] = "articles";
	}
	if ($va_license_code & 16) {
		$admin_blocks[] = "ads";
	}

	$admin_blocks[] = "site_settings";


	for ($i = 0; $i < sizeof($admin_blocks); $i++) {
		$t->set_var($admin_blocks[$i], "");
	}

	$blocks_permissions = array();
	$sql  = " SELECT block_name, permission FROM " . $table_prefix . "admin_privileges_settings ";
	$sql .= " WHERE privilege_id=" . $db->tosql(get_session("session_admin_privilege_id"), INTEGER);
	$db->query($sql);
	while ($db->next_record())
	{
		$block_name = $db->f("block_name");
		$permission = $db->f("permission");
		$blocks_permissions[$block_name] = $permission;
	}

	// parse articles menu
	if (isset($blocks_permissions["articles"]) && $blocks_permissions["articles"] == 1) {
		$sql  = " SELECT ac.category_id, ac.category_name ";
		$sql .= " FROM " . $table_prefix . "articles_categories ac ";
		$sql .= " WHERE ac.parent_category_id=0 ";
		$sql .= " ORDER BY ac.category_order ";
		$db->query($sql);
		if ($db->next_record()) {
			do {
				$category_id = $db->f("category_id");
				$admin_articles_url = "admin_articles.php?category_id=" . $category_id;
				$t->set_var("top_article", get_translation($db->f("category_name")));
				$t->set_var("admin_articles_url", $admin_articles_url);

				$t->parse("top_articles", true);
			} while ($db->next_record());
		}
	}
	// articles privileges
	if ($articles_statuses_perm) {
		$t->parse("articles_statuses", false);
	}
	if ($articles_reviews_perm) {
		$t->parse("articles_reviews", false);
	}
	if ($articles_reviews_sets_perm) {
		$t->parse("articles_reviews_settings", false);
	}


	//BEGIN product privileges changes
	if ($products_settings_perm) {
		$t->parse("products_settings", false);
	}
	if ($product_types_perm) {
		$t->parse("products_types", false);
	}
	if ($manufacturers_perm) {
		$t->parse("manufacturers", false);
	}
	if ($suppliers_perm) {
		$t->parse("suppliers", false);
	}
	if ($features_groups_perm) {
		$t->parse("features_groups_link", false);
	}
	if ($products_reviews_perm) {
		$t->parse("products_reviews", false);
	}
	if ($products_reviews_sets_perm) {
		$t->parse("products_reviews_settings", false);
	}
	if ($products_report_perm) {
		$t->parse("products_report", false);
	}
	if ($shipping_methods_perm) {
		$t->parse("shipping_methods", false);
	}
	if ($shipping_times_perm) {
		$t->parse("shipping_times", false);
	}
	if ($shipping_rules_perm) {
		$t->parse("shipping_rules", false);
	}                          
	if ($downloadable_products_perm) {
		$t->parse("downloadable_products", false);
	}
	if ($coupons_perm) {
		$t->parse("coupons", false);
	}
	if ($saved_types_perm) {
		$t->parse("saved_types", false);
	}
	if ($advanced_search_perm) {
		$t->parse("advanced_search", false);
	}
	if ($tell_friend_perm) {
		$t->parse("products_tell_friend_link", false);
	}
	if ($support_predefined_reply_perm) {
		$t->parse("support_prereplies_link", false);
		$t->parse("support_pretypes_link", false);
	}
	//END product privileges changes

	//BEGIN ads privileges changes
	if ($ads_tell_friend_perm) {
		$t->parse("ads_tell_friend_link", false);
	}
	//END ads privileges changes

	// CMS links privileges check
	if ($layouts_perm) {
		$t->sparse("custom_menus_link", false);
		$t->sparse("layouts_link", false);
	}
	if ($cms_settings_perm) {
		$t->sparse("cms_settings_link", false);
	}
	if ($site_navigation_perm) {
		$t->sparse("site_nav_link", false);
	}
	if ($footer_links_perm) {
		$t->sparse("footer_links_link", false);
	}
	if ($filters_perm) {
		$t->sparse("filters_link", false);
	}

	$block_number = 0;
	for ($i = 0; $i < sizeof($admin_blocks); $i++) {
		$block_name = $admin_blocks[$i];
		$permission = isset($blocks_permissions[$block_name]) ? $blocks_permissions[$block_name] : "";
		if ($permission) {
			$block_number++;
			$t->parse($block_name, false);
			$t->parse("cols", true);
			$t->set_var($block_name, "");
/*
			if ($block_number % 3 == 0) {
				$t->parse("rows", true);
				$t->set_var("cols", "");
			}
*/
			if (($block_number == 3) || ($block_number == 7)) {
				$t->parse("rows", true);
				$t->set_var("cols", "");
			}

		}
	}
	if ($block_number > 0 && $block_number % 4 != 0) {
		$t->parse("rows", true);
	}

	$t->pparse("main");

?>