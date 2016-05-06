<?php                           

function userhome_breadcrumb($block_name)
{
	global $t, $db, $site_id, $table_prefix;
	global $settings, $page_settings, $current_page;

	$user_id = get_session("session_user_id");		

	if(!$user_id || get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");

	$t->set_file("block_body", "block_userhome_breadcrumb.html");
	$t->set_var("user_home_href", $site_url . get_custom_friendly_url("user_home.php"));

	$links = array();
	if ($current_page == "user_profile.php") {
		$links[] = array("user_profile.php", EDIT_PROFILE_MSG, "");
	} else if ($current_page == "support.php") {
		$links[] = array("user_support.php", MY_SUPPORT_ISSUES_MSG, "");
		$links[] = array("support.php", NEW_SUPPORT_REQUEST_MSG, "");
	} else if ($current_page == "support_messages.php") {
		$links[] = array("user_support.php", MY_SUPPORT_ISSUES_MSG, "");
		$links[] = array("support_messages.php", VIEW_MSG, array("support_id", "vc"));
	} else if ($current_page == "user_ads.php") {
		$links[] = array("user_ads.php", MY_ADS_MSG, "");
	} else if ($current_page == "user_ad.php") {
		$links[] = array("user_ads.php", MY_ADS_MSG, "");
		$link_url = "user_ad.php";
		$item_id = get_param("item_id");
		if ($item_id) {
			$params = array("item_id");
		} else {
			$params = array("type_id");
		}
		$links[] = array($link_url, EDIT_MSG, $params);
	} else if ($current_page == "user_affiliate_sales.php") {
		$links[] = array("user_affiliate_sales.php", AFFILIATE_SALES_MSG, array("s_tp", "s_sd", "s_ed", "s_os"));
	} else if ($current_page == "user_affiliate_items.php") {
		$links[] = array("user_affiliate_sales.php", AFFILIATE_SALES_MSG, array("s_tp", "s_sd", "s_ed", "s_os"));
		$links[] = array("user_affiliate_items.php", PRODUCTS_REPORT_MSG, array("s_tp", "s_sd", "s_ed", "s_os"));
	} else if ($current_page == "user_change_password.php") {
		$links[] = array("user_change_password.php", CHANGE_PASSWORD_MSG, "");
	} else if ($current_page == "user_carts.php") {
		$links[] = array("user_carts.php", MY_SAVED_CARTS_MSG, "");
	} else if ($current_page == "user_merchant_sales.php") {
		$links[] = array("user_merchant_sales.php", MERCHANT_SALES_MSG, array("s_tp", "s_sd", "s_ed", "s_os"));
	} else if ($current_page == "user_merchant_items.php") {
		$links[] = array("user_merchant_sales.php", MERCHANT_SALES_MSG, array("s_tp", "s_sd", "s_ed", "s_os"));
		$links[] = array("user_merchant_items.php", PRODUCTS_REPORT_MSG, array("s_tp", "s_sd", "s_ed", "s_os"));
	} else if ($current_page == "user_merchant_orders.php") {
		$links[] = array("user_merchant_orders.php", MY_SALES_ORDERS_MSG, "");
	} else if ($current_page == "user_merchant_order.php") {
		$links[] = array("user_merchant_orders.php", MY_SALES_ORDERS_MSG, "");
		$links[] = array("user_merchant_order.php", ORDER_DETAILS_MSG, array("order_id"));
	} else if ($current_page == "user_reminders.php") {
		$links[] = array("user_reminders.php", MY_REMINDERS_MSG, "");
	} else if ($current_page == "user_reminder.php") {
		$links[] = array("user_reminders.php", MY_REMINDERS_MSG, "");
		$links[] = array("user_reminder.php", EDIT_REMINDER_MSG, array("reminder_id"));
	} else if ($current_page == "user_orders.php") {
		$links[] = array("user_orders.php", MY_ORDERS_MSG, "");
	} else if ($current_page == "user_order.php") {
		$links[] = array("user_orders.php", MY_ORDERS_MSG, "");
		$links[] = array("user_order.php", ORDER_DETAILS_MSG, array("order_id"));
	} else if ($current_page == "user_payments.php") {
		$links[] = array("user_payments.php", COMMISSION_PAYMENTS_MSG, "");
	} else if ($current_page == "user_payment.php") {
		$links[] = array("user_payments.php", COMMISSION_PAYMENTS_MSG, "");
		$links[] = array("user_payment.php", COMMISSIONS_MSG, array("payment_id"));
	} else if ($current_page == "user_wishlist.php") {
		$links[] = array("user_wishlist.php", MY_WISHLIST_MSG, "");
	} else if ($current_page == "user_support.php") {
		$links[] = array("user_support.php", MY_SUPPORT_ISSUES_MSG, "");
	} else if ($current_page == "user_products.php") {
		$links[] = array("user_products.php", MY_PRODUCTS_MSG, "");
	} else if ($current_page == "user_product.php") {
		$links[] = array("user_products.php", MY_PRODUCTS_MSG, "");
		$links[] = array("user_product.php", EDIT_PRODUCT_MSG, array("item_id"));
	} else if ($current_page == "user_product_options.php") {
		$item_id = get_param("item_id");
		$item_name = get_db_value("SELECT item_name FROM " . $table_prefix . "items WHERE item_id=" . $db->tosql($item_id, INTEGER));
		$links[] = array("user_products.php", MY_PRODUCTS_MSG, "");
		$links[] = array("user_product.php", $item_name, array("item_id"));
		$links[] = array("user_product_options.php", OPTIONS_AND_COMPONENTS_MSG, array("item_id"));
	} else if ($current_page == "user_product_option.php") {
		$item_id = get_param("item_id");
		$item_name = get_db_value("SELECT item_name FROM " . $table_prefix . "items WHERE item_id=" . $db->tosql($item_id, INTEGER));
		$links[] = array("user_products.php", MY_PRODUCTS_MSG, "");
		$links[] = array("user_product.php", $item_name, array("item_id"));
		$links[] = array("user_product_options.php", OPTIONS_AND_COMPONENTS_MSG, array("item_id"));
		$links[] = array("user_product_option.php", EDIT_OPTION_MSG, array("item_id", "property_id"));
	} else if ($current_page == "user_product_subcomponent.php") {
		$item_id = get_param("item_id");
		$item_name = get_db_value("SELECT item_name FROM " . $table_prefix . "items WHERE item_id=" . $db->tosql($item_id, INTEGER));
		$links[] = array("user_products.php", MY_PRODUCTS_MSG, "");
		$links[] = array("user_product.php", $item_name, array("item_id"));
		$links[] = array("user_product_options.php", OPTIONS_AND_COMPONENTS_MSG, array("item_id"));
		$links[] = array("user_product_option.php", EDIT_SUBCOMP_MSG, array("item_id", "property_id"));
	} else if ($current_page == "user_product_subcomponents.php") {
		$item_id = get_param("item_id");
		$item_name = get_db_value("SELECT item_name FROM " . $table_prefix . "items WHERE item_id=" . $db->tosql($item_id, INTEGER));
		$links[] = array("user_products.php", MY_PRODUCTS_MSG, "");
		$links[] = array("user_product.php", $item_name, array("item_id"));
		$links[] = array("user_product_options.php", OPTIONS_AND_COMPONENTS_MSG, array("item_id"));
		$links[] = array("user_product_option.php", EDIT_SUBCOMP_SELECTION_MSG, array("item_id", "property_id"));
	} else if ($current_page == "user_product_registrations.php") {
		$links[] = array("user_product_registrations.php", MY_PRODUCT_REGISTRATIONS_MSG, "");
	} else if ($current_page == "user_product_registration.php") {
		$links[] = array("user_product_registrations.php", MY_PRODUCT_REGISTRATIONS_MSG, "");
		$links[] = array("user_product_registration.php", REGISTER_PRODUCT_MSG, array("registration_id"));
	}

	$lc= sizeof($links) - 1;
	for ($l = 0; $l < $lc; $l++) {
		$link = $links[$l];
		$url = new VA_URL(get_custom_friendly_url($link[0]));
		$params = $link[2];
		if (is_array($params)) {
			for ($p = 0; $p < sizeof($params); $p++) {
				$url->add_parameter($params[$p], REQUEST, $params[$p]);
			}
		}
		$t->set_var("tree_url", $url->get_url());
		$t->set_var("tree_title", $link[1]);
		$t->set_var("tree_class", "");
		$t->parse("tree", true);
	}
	if ($lc>=0) {
		$link = $links[$lc];
		$url = new VA_URL(get_custom_friendly_url($link[0]));
		$params = $link[2];
		if (is_array($params)) {
			for ($p = 0; $p < sizeof($params); $p++) {
				$url->add_parameter($params[$p], REQUEST, $params[$p]);
			}
		}
		$t->set_var("tree_url", $url->get_url());
		$t->set_var("tree_title", $link[1]);
		$t->set_var("tree_class", "treeItemLast");
		$t->parse("tree", true);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>