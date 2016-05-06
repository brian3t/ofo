<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_common.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	
	// include admin messages
	$root_folder_path = "../";
	include_once($root_folder_path."messages/".$language_code."/admin_messages.php");

	// Admin Site URL settings
	$admin_folder = get_admin_dir();
	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");
	$admin_site_url = $site_url . $admin_folder;
	$admin_secure_url = $secure_url . $admin_folder;

	// check sites number
	$sitelist = false;	
	if (comp_vers(va_version(), "3.3.3") == 1) {
		$sql = " SELECT COUNT(*) FROM " . $table_prefix . "sites";
		$sites_number = get_db_value($sql);
		if ($sites_number > 1) {
			$sitelist = true;	
		}
	}

	// SSL settings
	$ssl_admin_tickets = get_setting_value($settings, "ssl_admin_tickets", 0);
	$ssl_admin_ticket = get_setting_value($settings, "ssl_admin_ticket", 0);
	$ssl_admin_helpdesk = get_setting_value($settings, "ssl_admin_helpdesk", 0);
	$secure_admin_order_create = get_setting_value($settings, "secure_admin_order_create", 0);
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


	$permissions = get_permissions();
	//BEGIN product privileges changes
	$products_categories_perm = get_setting_value($permissions, "products_categories", 0);
	$products_settings_perm = get_setting_value($permissions, "products_settings", 0);
	$product_types_perm = get_setting_value($permissions, "product_types", 0);
	$manufacturers_perm = get_setting_value($permissions, "manufacturers", 0);
	$features_groups_perm = get_setting_value($permissions, "features_groups", 0);
	$products_reviews_perm = get_setting_value($permissions, "products_reviews", 0);
	$products_report_perm = get_setting_value($permissions, "product_report", 0);
	$shipping_methods_perm = get_setting_value($permissions, "shipping_methods", 0);
	$shipping_times_perm = get_setting_value($permissions, "shipping_times", 0);
	$shipping_rules_perm = get_setting_value($permissions, "shipping_rules", 0);
	$downloadable_products_perm = get_setting_value($permissions, "downloadable_products", 0);
	$coupons_perm_perm = get_setting_value($permissions, "coupons", 0);
	$saved_types_perm = get_setting_value($permissions, "saved_types", 0);
	$advanced_search_perm = get_setting_value($permissions, "advanced_search", 0);
	//END product privileges changes

	// CMS permissions
	$layouts_perm = get_setting_value($permissions, "layouts", 0);
	$filemanager_perm = get_setting_value($permissions, "filemanager", 0);
	$polls_perm = get_setting_value($permissions, "polls", 0);
	$filters_perm = get_setting_value($permissions, "filters", 0);
	$custom_blocks_perm = get_setting_value($permissions, "custom_blocks", 0);
	$web_pages_perm = get_setting_value($permissions, "web_pages", 0);
	$custom_friendly_urls_perm = get_setting_value($permissions, "custom_friendly_urls", 0);
	$banners_perm = get_setting_value($permissions, "banners", 0);


?>