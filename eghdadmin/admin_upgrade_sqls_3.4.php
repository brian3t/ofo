<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_upgrade_sqls_3.4.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	check_admin_security("system_upgrade");

	if (comp_vers("3.3.1", $current_db_version) == 1)
	{
		if ($db_type == "mysql") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "admin_privileges MODIFY COLUMN is_hidden TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "admin_privileges MODIFY COLUMN support_privilege TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "admin_privileges_settings MODIFY COLUMN permission TINYINT";
			$sqls[] = "ALTER TABLE " . $table_prefix . "admins MODIFY COLUMN is_hidden TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "admins MODIFY COLUMN is_generate_big_image TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "admins MODIFY COLUMN is_generate_small_image TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "ads_categories MODIFY COLUMN allowed_view TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "ads_categories MODIFY COLUMN allowed_post TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "ads_items MODIFY COLUMN is_hot TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "ads_items MODIFY COLUMN is_approved TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "ads_items MODIFY COLUMN is_compared TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles MODIFY COLUMN is_html TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles MODIFY COLUMN allowed_rate TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles MODIFY COLUMN is_hot TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles MODIFY COLUMN is_remote_rss TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories MODIFY COLUMN is_hot TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories MODIFY COLUMN allowed_view TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories MODIFY COLUMN allowed_post TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories MODIFY COLUMN allowed_rate TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories MODIFY COLUMN is_rss TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories MODIFY COLUMN is_remote_rss TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_reviews MODIFY COLUMN recommended TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_reviews MODIFY COLUMN approved TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_statuses MODIFY COLUMN is_shown TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_statuses MODIFY COLUMN allowed_view TINYINT NOT NULL default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "banners MODIFY COLUMN is_new_window TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "banners MODIFY COLUMN is_active TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "banners MODIFY COLUMN show_on_ssl TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "banners_groups MODIFY COLUMN is_active TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "bookmarks MODIFY COLUMN is_start_page TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "bookmarks MODIFY COLUMN is_popup TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "categories MODIFY COLUMN is_showing TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "categories MODIFY COLUMN allowed_post TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "categories MODIFY COLUMN show_sub_products TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "coupons MODIFY COLUMN is_active TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "coupons MODIFY COLUMN discount_type TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "coupons MODIFY COLUMN coupon_tax_free TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "coupons MODIFY COLUMN order_tax_free TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "coupons MODIFY COLUMN items_all TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "coupons MODIFY COLUMN is_exclusive TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "currencies MODIFY COLUMN is_default TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_categories MODIFY COLUMN allowed_view TINYINT NOT NULL default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_list MODIFY COLUMN allowed_view TINYINT NOT NULL default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_list MODIFY COLUMN allowed_view_topics TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_list MODIFY COLUMN allowed_view_topic TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_list MODIFY COLUMN allowed_post_topics TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_list MODIFY COLUMN allowed_post_replies TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_moderators MODIFY COLUMN is_default_forum TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "header_links MODIFY COLUMN show_non_logged TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "header_links MODIFY COLUMN show_logged TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "header_submenus MODIFY COLUMN show_for_user TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "header_submenus MODIFY COLUMN match_type TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "icons MODIFY COLUMN is_active TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "icons MODIFY COLUMN show_for_user TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "item_types MODIFY COLUMN is_gift_voucher TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "item_types MODIFY COLUMN is_bundle TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "item_types MODIFY COLUMN is_user TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items MODIFY COLUMN hide_add_list TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items MODIFY COLUMN hide_add_details TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items MODIFY COLUMN is_special_offer TINYINT NOT NULL default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items MODIFY COLUMN is_price_edit TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items MODIFY COLUMN tax_free TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items MODIFY COLUMN disable_out_of_stock TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items MODIFY COLUMN is_points_price TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items MODIFY COLUMN is_recurring TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items MODIFY COLUMN is_showing TINYINT NOT NULL default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items MODIFY COLUMN is_approved TINYINT NOT NULL default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items MODIFY COLUMN is_compared TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties MODIFY COLUMN use_on_list TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties MODIFY COLUMN use_on_details TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties MODIFY COLUMN use_on_checkout TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties MODIFY COLUMN use_on_second TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties MODIFY COLUMN required TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties_values MODIFY COLUMN use_stock_level TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties_values MODIFY COLUMN hide_out_of_stock TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties_values MODIFY COLUMN hide_value TINYINT NOT NULL default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties_values MODIFY COLUMN is_default_value TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_serials MODIFY COLUMN used TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "languages MODIFY COLUMN show_for_user TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "layouts MODIFY COLUMN show_for_user TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "manuals_articles MODIFY COLUMN allowed_view TINYINT NOT NULL default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "manuals_articles MODIFY COLUMN shown_in_contents TINYINT NOT NULL default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "manuals_categories MODIFY COLUMN allowed_view TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "manuals_list MODIFY COLUMN allowed_view TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "menus_items MODIFY COLUMN show_non_logged TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "menus_items MODIFY COLUMN show_logged TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "newsletters MODIFY COLUMN mail_type TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "newsletters MODIFY COLUMN is_active TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "newsletters MODIFY COLUMN is_sent TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "newsletters MODIFY COLUMN is_prepared TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_custom_properties MODIFY COLUMN tax_free TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_custom_properties MODIFY COLUMN required TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_custom_values MODIFY COLUMN hide_value TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_custom_values MODIFY COLUMN is_default_value TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses MODIFY COLUMN is_user_cancel TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses MODIFY COLUMN allow_user_cancel TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses MODIFY COLUMN is_dispatch TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses MODIFY COLUMN is_list TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses MODIFY COLUMN show_for_user TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses MODIFY COLUMN paid_status TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses MODIFY COLUMN download_notify TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses MODIFY COLUMN item_notify TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses MODIFY COLUMN mail_notify TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses MODIFY COLUMN sms_notify TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders MODIFY COLUMN is_placed TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders MODIFY COLUMN is_exported TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders MODIFY COLUMN is_call_center TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders MODIFY COLUMN is_recurring TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_items MODIFY COLUMN is_recurring TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_items MODIFY COLUMN is_subscription TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_items_serials MODIFY COLUMN activated TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_notes MODIFY COLUMN show_for_user TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_properties MODIFY COLUMN tax_free TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "pages MODIFY COLUMN is_showing TINYINT NOT NULL default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "pages MODIFY COLUMN link_in_footer TINYINT NOT NULL default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_parameters MODIFY COLUMN not_passed TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems MODIFY COLUMN is_advanced TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems MODIFY COLUMN is_active TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems MODIFY COLUMN is_default TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems MODIFY COLUMN is_call_center TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems MODIFY COLUMN user_types_all TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "polls MODIFY COLUMN is_active TINYINT NOT NULL default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "polls_options MODIFY COLUMN is_default_value TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "quotes MODIFY COLUMN is_paid TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "release_changes MODIFY COLUMN is_showing TINYINT NOT NULL default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "releases MODIFY COLUMN is_showing TINYINT NOT NULL default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "releases MODIFY COLUMN show_on_index TINYINT NOT NULL default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "reviews MODIFY COLUMN recommended TINYINT NOT NULL default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "reviews MODIFY COLUMN approved TINYINT NOT NULL default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_modules MODIFY COLUMN is_external TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_modules MODIFY COLUMN is_active TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_modules_parameters MODIFY COLUMN not_passed TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_rules MODIFY COLUMN is_country_restriction TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_types MODIFY COLUMN is_taxable TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_types MODIFY COLUMN is_active TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_types MODIFY COLUMN user_types_all TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_departments MODIFY COLUMN show_for_user TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_messages MODIFY COLUMN internal TINYINT NOT NULL default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_messages MODIFY COLUMN is_user_reply TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_priorities MODIFY COLUMN is_default TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_products MODIFY COLUMN show_for_user TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses MODIFY COLUMN show_for_user TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses MODIFY COLUMN is_user_new TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses MODIFY COLUMN is_user_reply TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses MODIFY COLUMN is_operation TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses MODIFY COLUMN is_reassign TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses MODIFY COLUMN is_internal TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses MODIFY COLUMN is_list TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses MODIFY COLUMN is_closed TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses MODIFY COLUMN is_add_knowledge TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_types MODIFY COLUMN show_for_user TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_users_departments MODIFY COLUMN is_default_dep TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_profile_properties MODIFY COLUMN property_show TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_profile_properties MODIFY COLUMN required TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_profile_sections MODIFY COLUMN is_active TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_profile_values MODIFY COLUMN hide_value TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_profile_values MODIFY COLUMN is_default_value TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types MODIFY COLUMN is_default TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types MODIFY COLUMN is_active TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types MODIFY COLUMN is_sms_allowed TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types MODIFY COLUMN show_for_user TINYINT default '1'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types MODIFY COLUMN price_type TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types MODIFY COLUMN tax_free TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types MODIFY COLUMN is_subscription TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users MODIFY COLUMN is_approved TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users MODIFY COLUMN is_hidden TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users MODIFY COLUMN is_sms_allowed TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users MODIFY COLUMN tax_free TINYINT default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users_payments MODIFY COLUMN is_paid TINYINT default '0'";
		} elseif ($db_type == "access") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "admin_privileges ALTER COLUMN is_hidden BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "admin_privileges ALTER COLUMN support_privilege BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "admin_privileges_settings ALTER COLUMN permission BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "admins ALTER COLUMN is_hidden BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "admins ALTER COLUMN is_generate_big_image BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "admins ALTER COLUMN is_generate_small_image BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "ads_categories ALTER COLUMN allowed_view BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "ads_categories ALTER COLUMN allowed_post BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "ads_items ALTER COLUMN is_hot BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "ads_items ALTER COLUMN is_approved BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "ads_items ALTER COLUMN is_compared BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles ALTER COLUMN is_html BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles ALTER COLUMN allowed_rate BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles ALTER COLUMN is_hot BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles ALTER COLUMN is_remote_rss BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories ALTER COLUMN is_hot BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories ALTER COLUMN allowed_view BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories ALTER COLUMN allowed_post BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories ALTER COLUMN allowed_rate BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories ALTER COLUMN is_rss BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories ALTER COLUMN is_remote_rss BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_reviews ALTER COLUMN recommended BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_reviews ALTER COLUMN approved BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_statuses ALTER COLUMN is_shown BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "articles_statuses ALTER COLUMN allowed_view BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "banners ALTER COLUMN is_new_window BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "banners ALTER COLUMN is_active BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "banners ALTER COLUMN show_on_ssl BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "banners_groups ALTER COLUMN is_active BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "bookmarks ALTER COLUMN is_start_page BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "bookmarks ALTER COLUMN is_popup BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "categories ALTER COLUMN is_showing BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "categories ALTER COLUMN allowed_post BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "categories ALTER COLUMN show_sub_products BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "coupons ALTER COLUMN is_active BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "coupons ALTER COLUMN discount_type BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "coupons ALTER COLUMN coupon_tax_free BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "coupons ALTER COLUMN order_tax_free BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "coupons ALTER COLUMN items_all BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "coupons ALTER COLUMN is_exclusive BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "currencies ALTER COLUMN is_default BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_categories ALTER COLUMN allowed_view BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_list ALTER COLUMN allowed_view BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_list ALTER COLUMN allowed_view_topics BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_list ALTER COLUMN allowed_view_topic BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_list ALTER COLUMN allowed_post_topics BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_list ALTER COLUMN allowed_post_replies BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "forum_moderators ALTER COLUMN is_default_forum BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "header_links ALTER COLUMN show_non_logged BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "header_links ALTER COLUMN show_logged BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "header_submenus ALTER COLUMN show_for_user BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "header_submenus ALTER COLUMN match_type BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "icons ALTER COLUMN is_active BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "icons ALTER COLUMN show_for_user BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "item_types ALTER COLUMN is_gift_voucher BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "item_types ALTER COLUMN is_bundle BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "item_types ALTER COLUMN is_user BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items ALTER COLUMN hide_add_list BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items ALTER COLUMN hide_add_details BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items ALTER COLUMN is_special_offer BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items ALTER COLUMN is_price_edit BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items ALTER COLUMN tax_free BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items ALTER COLUMN disable_out_of_stock BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items ALTER COLUMN is_points_price BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items ALTER COLUMN is_recurring BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items ALTER COLUMN is_showing BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items ALTER COLUMN is_approved BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items ALTER COLUMN is_compared BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties ALTER COLUMN use_on_list BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties ALTER COLUMN use_on_details BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties ALTER COLUMN use_on_checkout BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties ALTER COLUMN use_on_second BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties ALTER COLUMN required BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties_values ALTER COLUMN use_stock_level BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties_values ALTER COLUMN hide_out_of_stock BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties_values ALTER COLUMN hide_value BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_properties_values ALTER COLUMN is_default_value BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "items_serials ALTER COLUMN used BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "languages ALTER COLUMN show_for_user BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "layouts ALTER COLUMN show_for_user BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "manuals_articles ALTER COLUMN allowed_view BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "manuals_articles ALTER COLUMN shown_in_contents BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "manuals_categories ALTER COLUMN allowed_view BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "manuals_list ALTER COLUMN allowed_view BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "menus_items ALTER COLUMN show_non_logged BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "menus_items ALTER COLUMN show_logged BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "newsletters ALTER COLUMN mail_type BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "newsletters ALTER COLUMN is_active BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "newsletters ALTER COLUMN is_sent BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "newsletters ALTER COLUMN is_prepared BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_custom_properties ALTER COLUMN tax_free BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_custom_properties ALTER COLUMN required BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_custom_values ALTER COLUMN hide_value BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_custom_values ALTER COLUMN is_default_value BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ALTER COLUMN is_user_cancel BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ALTER COLUMN allow_user_cancel BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ALTER COLUMN is_dispatch BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ALTER COLUMN is_list BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ALTER COLUMN show_for_user BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ALTER COLUMN paid_status BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ALTER COLUMN download_notify BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ALTER COLUMN item_notify BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ALTER COLUMN mail_notify BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ALTER COLUMN sms_notify BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders ALTER COLUMN is_placed BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders ALTER COLUMN is_exported BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders ALTER COLUMN is_call_center BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders ALTER COLUMN is_recurring BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_items ALTER COLUMN is_recurring BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_items ALTER COLUMN is_subscription BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_items_serials ALTER COLUMN activated BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_notes ALTER COLUMN show_for_user BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_properties ALTER COLUMN tax_free BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "pages ALTER COLUMN is_showing BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "pages ALTER COLUMN link_in_footer BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_parameters ALTER COLUMN not_passed BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ALTER COLUMN is_advanced BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ALTER COLUMN is_active BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ALTER COLUMN is_default BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ALTER COLUMN is_call_center BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ALTER COLUMN user_types_all BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "polls ALTER COLUMN is_active BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "polls_options ALTER COLUMN is_default_value BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "quotes ALTER COLUMN is_paid BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "release_changes ALTER COLUMN is_showing BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "releases ALTER COLUMN is_showing BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "releases ALTER COLUMN show_on_index BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "reviews ALTER COLUMN recommended BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "reviews ALTER COLUMN approved BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_modules ALTER COLUMN is_external BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_modules ALTER COLUMN is_active BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_modules_parameters ALTER COLUMN not_passed BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_rules ALTER COLUMN is_country_restriction BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_types ALTER COLUMN is_taxable BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_types ALTER COLUMN is_active BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_types ALTER COLUMN user_types_all BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_departments ALTER COLUMN show_for_user BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_messages ALTER COLUMN internal BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_messages ALTER COLUMN is_user_reply BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_priorities ALTER COLUMN is_default BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_products ALTER COLUMN show_for_user BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ALTER COLUMN show_for_user BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ALTER COLUMN is_user_new BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ALTER COLUMN is_user_reply BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ALTER COLUMN is_operation BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ALTER COLUMN is_reassign BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ALTER COLUMN is_internal BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ALTER COLUMN is_list BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ALTER COLUMN is_closed BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ALTER COLUMN is_add_knowledge BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_types ALTER COLUMN show_for_user BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_users_departments ALTER COLUMN is_default_dep BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_profile_properties ALTER COLUMN property_show BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_profile_properties ALTER COLUMN required BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_profile_sections ALTER COLUMN is_active BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_profile_values ALTER COLUMN hide_value BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_profile_values ALTER COLUMN is_default_value BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types ALTER COLUMN is_default BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types ALTER COLUMN is_active BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types ALTER COLUMN is_sms_allowed BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types ALTER COLUMN show_for_user BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types ALTER COLUMN price_type BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types ALTER COLUMN tax_free BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "user_types ALTER COLUMN is_subscription BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users ALTER COLUMN is_approved BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users ALTER COLUMN is_hidden BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users ALTER COLUMN is_sms_allowed BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users ALTER COLUMN tax_free BYTE";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users_payments ALTER COLUMN is_paid BYTE";
		}
		$sqls[] = "CREATE INDEX " . $table_prefix . "items_is_special_offer ON " . $table_prefix . "items (is_special_offer)";
		$sqls[] = "CREATE INDEX " . $table_prefix . "items_hide_out_of_stock ON " . $table_prefix . "items (hide_out_of_stock)";
		$sqls[] = "CREATE INDEX " . $table_prefix . "items_is_showing ON " . $table_prefix . "items (is_showing)";
		$sqls[] = "CREATE INDEX " . $table_prefix . "categories_is_showing ON " . $table_prefix . "categories (is_showing)";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.1");
	}

	/*
	// fix field name	
	if (comp_vers("3.3.2", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "header_links CHANGE COLUMN sudmenu_style_name submenu_style_name VARCHAR(64)",
			"postgre" => "ALTER TABLE " . $table_prefix . "header_links RENAME COLUMN sudmenu_style_name TO submenu_style_name",
			"access"  => "ALTER TABLE " . $table_prefix . "header_links RENAME COLUMN sudmenu_style_name TO submenu_style_name",
			"db2"     => "ALTER TABLE " . $table_prefix . "header_links ADD COLUMN submenu_style_name VARCHAR(64)"
		);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == 'db2'){
			$sqls[] = "UPDATE " . $table_prefix . "header_links set submenu_style_name = sudmenu_style_name";
			$sqls[] = "ALTER TABLE " . $table_prefix . "header_links DROP COLUMN sudmenu_style_name";
		}
		
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "tracking_visits MODIFY COLUMN week_added INT(11) NOT NULL default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "tracking_visits ALTER COLUMN week_added TYPE INT4",
			"access"  => "ALTER TABLE " . $table_prefix . "tracking_visits ALTER COLUMN week_added INTEGER",
			"db2"     => "ALTER TABLE " . $table_prefix . "tracking_visits ALTER COLUMN week_added SET DATA TYPE INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.2");
	}//*/

	if (comp_vers("3.3.3", $current_db_version) == 1)
	{
	  // multi-site functionality and states by countries
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "sites (";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `site_name` VARCHAR(255),";
		$mysql_sql .= "  `site_description` TEXT";
		$mysql_sql .= "  ,PRIMARY KEY (site_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "sites START 2";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "sites (";
		$postgre_sql .= "  site_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "sites'),";
		$postgre_sql .= "  site_name VARCHAR(255),";
		$postgre_sql .= "  site_description TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "sites (";
		$access_sql .= "  [site_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [site_name] VARCHAR(255),";
		$access_sql .= "  [site_description] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "sites (";
		$db2_sql .= "  site_id INTEGER NOT NULL,";
		$db2_sql .= "  site_name VARCHAR(255),";
		$db2_sql .= "  site_description LONG VARCHAR";
		$db2_sql .= "  ,PRIMARY KEY (site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "sites AS INTEGER START WITH 2 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "135 NO CASCADE BEFORE INSERT ON " . $table_prefix . "sites REFERENCING NEW AS newr_" . $table_prefix . "sites FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "sites.site_id IS NULL ) begin atomic set newr_" . $table_prefix . "sites.site_id = nextval for seq_" . $table_prefix . "sites; end";
		}

		$sqls[] = "INSERT INTO " . $table_prefix . "sites (site_id, site_name, site_description) VALUES (1, 'Default Site', '')";

		// primary keys changes
		if ($db_type == "mysql" || $db_type == "db2") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "global_settings DROP PRIMARY KEY";
			$sqls[] = "ALTER TABLE " . $table_prefix . "page_settings DROP PRIMARY KEY";
			$sqls[] = "ALTER TABLE " . $table_prefix . "states DROP PRIMARY KEY";
		} else if ($db_type == "postgre") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "global_settings DROP CONSTRAINT " . $table_prefix . "global_settings_pkey ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "page_settings DROP CONSTRAINT " . $table_prefix . "page_settings_pkey ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "states DROP CONSTRAINT " . $table_prefix . "states_pkey ";
		} else if ($db_type == "access") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "global_settings DROP CONSTRAINT PrimaryKey";
			$sqls[] = "ALTER TABLE " . $table_prefix . "page_settings DROP CONSTRAINT PrimaryKey";
			$sqls[] = "ALTER TABLE " . $table_prefix . "states DROP CONSTRAINT PrimaryKey";
		} 

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "global_settings ADD COLUMN site_id INT(11) NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "global_settings ADD COLUMN site_id INT4 NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "global_settings ADD COLUMN site_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "global_settings ADD COLUMN site_id INTEGER NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "global_settings SET site_id=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "page_settings ADD COLUMN site_id INT(11) NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "page_settings ADD COLUMN site_id INT4 NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "page_settings ADD COLUMN site_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "page_settings ADD COLUMN site_id INTEGER NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "page_settings SET site_id=1 ";

		if ($db_type == "mysql" || $db_type == "db2" || $db_type == "postgre") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "states ADD COLUMN country_code VARCHAR(4) NOT NULL DEFAULT ''";
		} else {
			$sqls[] = "ALTER TABLE " . $table_prefix . "states ADD COLUMN country_code VARCHAR(4) NOT NULL ";
		}
		$sqls[] = " UPDATE " . $table_prefix . "states SET country_code='' ";

		if ($db_type == "mysql" || $db_type == "db2" || $db_type == "postgre") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "global_settings ADD PRIMARY KEY (site_id,setting_type,setting_name)";
			$sqls[] = "ALTER TABLE " . $table_prefix . "page_settings ADD PRIMARY KEY (site_id,layout_id,page_name,setting_name)";
			$sqls[] = "ALTER TABLE " . $table_prefix . "states ADD PRIMARY KEY (state_code,country_code)";
		} else if ($db_type == "access") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "global_settings ADD CONSTRAINT PrimaryKey PRIMARY KEY (site_id,setting_type,setting_name)";
			$sqls[] = "ALTER TABLE " . $table_prefix . "page_settings ADD CONSTRAINT PrimaryKey PRIMARY KEY (site_id,layout_id,page_name,setting_name)";
			$sqls[] = "ALTER TABLE " . $table_prefix . "states ADD CONSTRAINT PrimaryKey PRIMARY KEY (state_code,country_code)";
		} 
		// end primary keys changes

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "ads_categories_sites (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_categories_sites (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_categories_sites (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "ads_categories_sites (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "ads_categories SET sites_all=1 ";

		// articles categories
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_sites (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_sites (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_sites (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_sites (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "articles_categories SET sites_all=1 ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_types (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_types (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_types (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_types (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN user_types_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN user_types_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN user_types_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN user_types_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "articles_categories SET user_types_all=1 ";

		// categories
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "categories_sites (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "categories_sites (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "categories_sites (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "categories_sites (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "categories SET sites_all=1 ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "categories_user_types (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "categories_user_types (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "categories_user_types (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "categories_user_types (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN user_types_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN user_types_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN user_types_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN user_types_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "categories SET user_types_all=1 ";

		// product items
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "items_sites (";
		$mysql_sql .= "  `item_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (item_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "items_sites (";
		$postgre_sql .= "  item_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (item_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "items_sites (";
		$access_sql .= "  [item_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (item_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "items_sites (";
		$db2_sql .= "  item_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (item_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items SET sites_all=1 ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "items_user_types (";
		$mysql_sql .= "  `item_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (item_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "items_user_types (";
		$postgre_sql .= "  item_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (item_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "items_user_types (";
		$access_sql .= "  [item_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (item_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "items_user_types (";
		$db2_sql .= "  item_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (item_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN user_types_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN user_types_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN user_types_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN user_types_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items SET user_types_all=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN min_quantity INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN min_quantity INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN min_quantity INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN min_quantity INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN max_quantity INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN max_quantity INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN max_quantity INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN max_quantity INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		// end product items changes

		// forum categories
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "forum_categories_sites (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "forum_categories_sites (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "forum_categories_sites (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "forum_categories_sites (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_categories ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_categories ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_categories ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "forum_categories ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "forum_categories SET sites_all=1 ";

		// manuals categories
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories_sites (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories_sites (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories_sites (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories_sites (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "manuals_categories SET sites_all=1 ";

		// custom pages
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "pages_sites (";
		$mysql_sql .= "  `page_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (page_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "pages_sites (";
		$postgre_sql .= "  page_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (page_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "pages_sites (";
		$access_sql .= "  [page_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (page_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "pages_sites (";
		$db2_sql .= "  page_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (page_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "pages ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "pages ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "pages ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "pages ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "pages SET sites_all=1 ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "pages_user_types (";
		$mysql_sql .= "  `page_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (page_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "pages_user_types (";
		$postgre_sql .= "  page_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (page_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "pages_user_types (";
		$access_sql .= "  [page_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (page_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "pages_user_types (";
		$db2_sql .= "  page_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (page_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "pages ADD COLUMN user_types_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "pages ADD COLUMN user_types_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "pages ADD COLUMN user_types_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "pages ADD COLUMN user_types_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "pages SET user_types_all=1 ";

		// layouts 
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "layouts_sites (";
		$mysql_sql .= "  `layout_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (layout_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "layouts_sites (";
		$postgre_sql .= "  layout_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (layout_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "layouts_sites (";
		$access_sql .= "  [layout_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (layout_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "layouts_sites (";
		$db2_sql .= "  layout_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (layout_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "layouts SET sites_all=1 ";

		// shipping types
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_sites (";
		$mysql_sql .= "  `shipping_type_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (shipping_type_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_sites (";
		$postgre_sql .= "  shipping_type_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (shipping_type_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_sites (";
		$access_sql .= "  [shipping_type_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (shipping_type_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_sites (";
		$db2_sql .= "  shipping_type_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (shipping_type_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "shipping_types SET sites_all=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN countries_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN countries_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN countries_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN countries_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "shipping_types SET countries_all=0 ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_states (";
		$mysql_sql .= "  `shipping_type_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `state_code` VARCHAR(8) NOT NULL ";
		$mysql_sql .= "  ,PRIMARY KEY (shipping_type_id,state_code))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_states (";
		$postgre_sql .= "  shipping_type_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  state_code VARCHAR(8) NOT NULL ";
		$postgre_sql .= "  ,PRIMARY KEY (shipping_type_id,state_code))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_states (";
		$access_sql .= "  [shipping_type_id] INTEGER NOT NULL,";
		$access_sql .= "  [state_code] VARCHAR(8) NOT NULL ";
		$access_sql .= "  ,PRIMARY KEY (shipping_type_id,state_code))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_states (";
		$db2_sql .= "  shipping_type_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  state_code VARCHAR(8) NOT NULL ";
		$db2_sql .= "  ,PRIMARY KEY (shipping_type_id,state_code))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN states_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN states_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN states_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN states_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "shipping_types SET states_all=1 ";

		// user types 
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "user_types_sites (";
		$mysql_sql .= "  `type_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (type_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "user_types_sites (";
		$postgre_sql .= "  type_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (type_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "user_types_sites (";
		$access_sql .= "  [type_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (type_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "user_types_sites (";
		$db2_sql .= "  type_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (type_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "user_types SET sites_all=1 ";

		// payment systems 
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "payment_systems_sites (";
		$mysql_sql .= "  `payment_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (payment_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "payment_systems_sites (";
		$postgre_sql .= "  payment_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (payment_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "payment_systems_sites (";
		$access_sql .= "  [payment_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (payment_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "payment_systems_sites (";
		$db2_sql .= "  payment_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (payment_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "payment_systems SET sites_all=1 ";

		// support departments 
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "support_departments_sites (";
		$mysql_sql .= "  `dep_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (dep_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "support_departments_sites (";
		$postgre_sql .= "  dep_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (dep_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "support_departments_sites (";
		$access_sql .= "  [dep_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (dep_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "support_departments_sites (";
		$db2_sql .= "  dep_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (dep_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "support_departments SET sites_all=1 ";

		// support products
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "support_products_sites (";
		$mysql_sql .= "  `product_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (product_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "support_products_sites (";
		$postgre_sql .= "  product_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (product_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "support_products_sites (";
		$access_sql .= "  [product_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (product_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "support_products_sites (";
		$db2_sql .= "  product_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (product_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_products ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_products ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "support_products ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_products ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "support_products SET sites_all=1 ";

		// add site_id field to existed tables
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN site_id INT(11) NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN site_id INT4 NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN site_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN site_id INTEGER NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "orders SET site_id=1 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "orders_site_id ON " . $table_prefix . "orders (site_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN site_id INT(11) NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN site_id INT4 NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN site_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN site_id INTEGER NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "orders_items SET site_id=1 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "orders_items_site_id ON " . $table_prefix . "orders_items (site_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_prices ADD COLUMN site_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_prices ADD COLUMN site_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items_prices ADD COLUMN site_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_prices ADD COLUMN site_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items_prices SET site_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "items_prices_site_id ON " . $table_prefix . "items_prices (site_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_custom_properties ADD COLUMN site_id INT(11) NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_custom_properties ADD COLUMN site_id INT4 NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_custom_properties ADD COLUMN site_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "order_custom_properties ADD COLUMN site_id INTEGER NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "order_custom_properties SET site_id=1 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "order_custom_props_site_id ON " . $table_prefix . "order_custom_properties (site_id) ";
		// end multi-site changes

		// merchant user types who can post to categories
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "categories_post_types (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "categories_post_types (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "categories_post_types (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "categories_post_types (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN allowed_post_types_all TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN allowed_post_types_all SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN allowed_post_types_all BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN allowed_post_types_all SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "categories SET allowed_post_types_all=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN allowed_post_subcategories TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN allowed_post_subcategories SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN allowed_post_subcategories BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN allowed_post_subcategories SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "categories SET allowed_post_subcategories=0 ";

		// custom fields for support form
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "support_custom_properties (";
		$mysql_sql .= "  `property_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `site_id` INT(11) default '1',";
		$mysql_sql .= "  `property_order` INT(11) default '0',";
		$mysql_sql .= "  `property_name` VARCHAR(255),";
		$mysql_sql .= "  `property_description` TEXT,";
		$mysql_sql .= "  `default_value` TEXT,";
		$mysql_sql .= "  `property_style` VARCHAR(255),";
		$mysql_sql .= "  `section_id` INT(11) default '0',";
		$mysql_sql .= "  `property_show` TINYINT default '0',";
		$mysql_sql .= "  `control_type` VARCHAR(16),";
		$mysql_sql .= "  `control_style` VARCHAR(255),";
		$mysql_sql .= "  `control_code` TEXT,";
		$mysql_sql .= "  `onchange_code` TEXT,";
		$mysql_sql .= "  `onclick_code` TEXT,";
		$mysql_sql .= "  `required` TINYINT default '0',";
		$mysql_sql .= "  `before_name_html` TEXT,";
		$mysql_sql .= "  `after_name_html` TEXT,";
		$mysql_sql .= "  `before_control_html` TEXT,";
		$mysql_sql .= "  `after_control_html` TEXT,";
		$mysql_sql .= "  `validation_regexp` TEXT,";
		$mysql_sql .= "  `regexp_error` TEXT,";
		$mysql_sql .= "  `options_values_sql` TEXT";
		$mysql_sql .= "  ,KEY payment_id (site_id)";
		$mysql_sql .= "  ,PRIMARY KEY (property_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "support_custom_properties START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "support_custom_properties (";
		$postgre_sql .= "  property_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "support_custom_properties'),";
		$postgre_sql .= "  site_id INT4 default '1',";
		$postgre_sql .= "  property_order INT4 default '0',";
		$postgre_sql .= "  property_name VARCHAR(255),";
		$postgre_sql .= "  property_description TEXT,";
		$postgre_sql .= "  default_value TEXT,";
		$postgre_sql .= "  property_style VARCHAR(255),";
		$postgre_sql .= "  section_id INT4 default '0',";
		$postgre_sql .= "  property_show SMALLINT default '0',";
		$postgre_sql .= "  control_type VARCHAR(16),";
		$postgre_sql .= "  control_style VARCHAR(255),";
		$postgre_sql .= "  control_code TEXT,";
		$postgre_sql .= "  onchange_code TEXT,";
		$postgre_sql .= "  onclick_code TEXT,";
		$postgre_sql .= "  required SMALLINT default '0',";
		$postgre_sql .= "  before_name_html TEXT,";
		$postgre_sql .= "  after_name_html TEXT,";
		$postgre_sql .= "  before_control_html TEXT,";
		$postgre_sql .= "  after_control_html TEXT,";
		$postgre_sql .= "  validation_regexp TEXT,";
		$postgre_sql .= "  regexp_error TEXT,";
		$postgre_sql .= "  options_values_sql TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (property_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "support_custom_properties (";
		$access_sql .= "  [property_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [site_id] INTEGER,";
		$access_sql .= "  [property_order] INTEGER,";
		$access_sql .= "  [property_name] VARCHAR(255),";
		$access_sql .= "  [property_description] LONGTEXT,";
		$access_sql .= "  [default_value] LONGTEXT,";
		$access_sql .= "  [property_style] VARCHAR(255),";
		$access_sql .= "  [section_id] INTEGER,";
		$access_sql .= "  [property_show] BYTE,";
		$access_sql .= "  [control_type] VARCHAR(16),";
		$access_sql .= "  [control_style] VARCHAR(255),";
		$access_sql .= "  [control_code] LONGTEXT,";
		$access_sql .= "  [onchange_code] LONGTEXT,";
		$access_sql .= "  [onclick_code] LONGTEXT,";
		$access_sql .= "  [required] BYTE,";
		$access_sql .= "  [before_name_html] LONGTEXT,";
		$access_sql .= "  [after_name_html] LONGTEXT,";
		$access_sql .= "  [before_control_html] LONGTEXT,";
		$access_sql .= "  [after_control_html] LONGTEXT,";
		$access_sql .= "  [validation_regexp] LONGTEXT,";
		$access_sql .= "  [regexp_error] LONGTEXT,";
		$access_sql .= "  [options_values_sql] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (property_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "support_custom_properties (";
		$db2_sql .= "  property_id INTEGER NOT NULL,";
		$db2_sql .= "  site_id INTEGER default 1,";
		$db2_sql .= "  property_order INTEGER default 0,";
		$db2_sql .= "  property_name VARCHAR(255),";
		$db2_sql .= "  property_description LONG VARCHAR,";
		$db2_sql .= "  default_value LONG VARCHAR,";
		$db2_sql .= "  property_style VARCHAR(255),";
		$db2_sql .= "  section_id INTEGER default 0,";
		$db2_sql .= "  property_show SMALLINT default 0,";
		$db2_sql .= "  control_type VARCHAR(16),";
		$db2_sql .= "  control_style VARCHAR(255),";
		$db2_sql .= "  control_code LONG VARCHAR,";
		$db2_sql .= "  onchange_code LONG VARCHAR,";
		$db2_sql .= "  onclick_code LONG VARCHAR,";
		$db2_sql .= "  required SMALLINT default 0,";
		$db2_sql .= "  before_name_html LONG VARCHAR,";
		$db2_sql .= "  after_name_html LONG VARCHAR,";
		$db2_sql .= "  before_control_html LONG VARCHAR,";
		$db2_sql .= "  after_control_html LONG VARCHAR,";
		$db2_sql .= "  validation_regexp LONG VARCHAR,";
		$db2_sql .= "  regexp_error LONG VARCHAR,";
		$db2_sql .= "  options_values_sql LONG VARCHAR";
		$db2_sql .= "  ,PRIMARY KEY (property_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "support_custom_propertie_55 ON " . $table_prefix . "support_custom_properties (site_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "support_custom_properties AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "139 NO CASCADE BEFORE INSERT ON " . $table_prefix . "support_custom_properties REFERENCING NEW AS newr_" . $table_prefix . "support_custom_properties FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "support_custom_properties.property_id IS NULL ) begin atomic set newr_" . $table_prefix . "support_custom_properties.property_id = nextval for seq_" . $table_prefix . "support_custom_properties; end";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "support_custom_values (";
		$mysql_sql .= "  `property_value_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `property_id` INT(11) default '0',";
		$mysql_sql .= "  `property_value` VARCHAR(255),";
		$mysql_sql .= "  `hide_value` TINYINT default '0',";
		$mysql_sql .= "  `is_default_value` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (property_value_id)";
		$mysql_sql .= "  ,KEY property_id (property_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "support_custom_values START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "support_custom_values (";
		$postgre_sql .= "  property_value_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "support_custom_values'),";
		$postgre_sql .= "  property_id INT4 default '0',";
		$postgre_sql .= "  property_value VARCHAR(255),";
		$postgre_sql .= "  hide_value SMALLINT default '0',";
		$postgre_sql .= "  is_default_value SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (property_value_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "support_custom_values (";
		$access_sql .= "  [property_value_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [property_id] INTEGER,";
		$access_sql .= "  [property_value] VARCHAR(255),";
		$access_sql .= "  [hide_value] BYTE,";
		$access_sql .= "  [is_default_value] BYTE";
		$access_sql .= "  ,PRIMARY KEY (property_value_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "support_custom_values (";
		$db2_sql .= "  property_value_id INTEGER NOT NULL,";
		$db2_sql .= "  property_id INTEGER default 0,";
		$db2_sql .= "  property_value VARCHAR(255),";
		$db2_sql .= "  hide_value SMALLINT default 0,";
		$db2_sql .= "  is_default_value SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (property_value_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "support_custom_values_pr_56 ON " . $table_prefix . "support_custom_values (property_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "support_custom_values AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "140 NO CASCADE BEFORE INSERT ON " . $table_prefix . "support_custom_values REFERENCING NEW AS newr_" . $table_prefix . "support_custom_values FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "support_custom_values.property_value_id IS NULL ) begin atomic set newr_" . $table_prefix . "support_custom_values.property_value_id = nextval for seq_" . $table_prefix . "support_custom_values; end";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "support_properties (";
		$mysql_sql .= "  `support_property_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `support_id` INT(11) default '0',";
		$mysql_sql .= "  `property_id` INT(11) default '0',";
		$mysql_sql .= "  `property_value` TEXT";
		$mysql_sql .= "  ,PRIMARY KEY (support_property_id)";
		$mysql_sql .= "  ,KEY property_id (property_id)";
		$mysql_sql .= "  ,KEY support_id (support_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "support_properties START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "support_properties (";
		$postgre_sql .= "  support_property_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "support_properties'),";
		$postgre_sql .= "  support_id INT4 default '0',";
		$postgre_sql .= "  property_id INT4 default '0',";
		$postgre_sql .= "  property_value TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (support_property_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "support_properties (";
		$access_sql .= "  [support_property_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [support_id] INTEGER,";
		$access_sql .= "  [property_id] INTEGER,";
		$access_sql .= "  [property_value] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (support_property_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "support_properties (";
		$db2_sql .= "  support_property_id INTEGER NOT NULL,";
		$db2_sql .= "  support_id INTEGER default 0,";
		$db2_sql .= "  property_id INTEGER default 0,";
		$db2_sql .= "  property_value LONG VARCHAR";
		$db2_sql .= "  ,PRIMARY KEY (support_property_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "support_properties_order_id ON " . $table_prefix . "support_properties (support_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "support_properties_prope_63 ON " . $table_prefix . "support_properties (property_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "support_properties AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "148 NO CASCADE BEFORE INSERT ON " . $table_prefix . "support_properties REFERENCING NEW AS newr_" . $table_prefix . "support_properties FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "support_properties.support_property_id IS NULL ) begin atomic set newr_" . $table_prefix . "support_properties.support_property_id = nextval for seq_" . $table_prefix . "support_properties; end";
		}
		// end support custom fields changes

		// order_statuses notification fields
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_notify TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_notify SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_notify BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_notify SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_to VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_from VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_cc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_bcc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_reply_to VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_return_path VARCHAR(64) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_mail_type TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_mail_type SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_mail_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_mail_type SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_subject VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_body TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_body TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_body LONGTEXT",
			"db2"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_body LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_sms_notify TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_sms_notify SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_sms_notify BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_sms_notify SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_sms_recipient VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_sms_originator VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_sms_message TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_sms_message TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_sms_message LONGTEXT",
			"db2"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN merchant_sms_message LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_notify TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_notify SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_notify BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_notify SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_to VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_from VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_cc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_bcc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_reply_to VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_return_path VARCHAR(64) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_mail_type TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_mail_type SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_mail_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_mail_type SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_subject VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_body TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_body TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_body LONGTEXT",
			"db2"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_body LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_sms_notify TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_sms_notify SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_sms_notify BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_sms_notify SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_sms_recipient VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_sms_originator VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_sms_message TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_sms_message TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_sms_message LONGTEXT",
			"db2"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN admin_sms_message LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];
		// end order_statuses notification fields

		// changes for saved items
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "saved_types (";
		$mysql_sql .= "  `type_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `type_name` VARCHAR(64),";
		$mysql_sql .= "  `type_desc` TEXT,";
		$mysql_sql .= "  `is_active` TINYINT default '1',";
		$mysql_sql .= "  `allowed_search` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (type_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "saved_types START 2";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "saved_types (";
		$postgre_sql .= "  type_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "saved_types'),";
		$postgre_sql .= "  type_name VARCHAR(64),";
		$postgre_sql .= "  type_desc TEXT,";
		$postgre_sql .= "  is_active SMALLINT default '1',";
		$postgre_sql .= "  allowed_search SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "saved_types (";
		$access_sql .= "  [type_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [type_name] VARCHAR(64),";
		$access_sql .= "  [type_desc] LONGTEXT,";
		$access_sql .= "  [is_active] BYTE,";
		$access_sql .= "  [allowed_search] BYTE";
		$access_sql .= "  ,PRIMARY KEY (type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "saved_types (";
		$db2_sql .= "  type_id INTEGER NOT NULL,";
		$db2_sql .= "  type_name VARCHAR(64),";
		$db2_sql .= "  type_desc LONG VARCHAR,";
		$db2_sql .= "  is_active SMALLINT default 1,";
		$db2_sql .= "  allowed_search SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "saved_types AS INTEGER START WITH 2 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "123 NO CASCADE BEFORE INSERT ON " . $table_prefix . "saved_types REFERENCING NEW AS newr_" . $table_prefix . "saved_types FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "saved_types.type_id IS NULL ) begin atomic set newr_" . $table_prefix . "saved_types.type_id = nextval for seq_" . $table_prefix . "saved_types; end";
		}

		$sqls[] = "INSERT INTO " . $table_prefix . "saved_types (type_id, type_name, type_desc, is_active, allowed_search) VALUES (1, 'Wish List', '', 1, 0)";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "saved_items ADD COLUMN type_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "saved_items ADD COLUMN type_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "saved_items ADD COLUMN type_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "saved_items ADD COLUMN type_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "saved_items SET type_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "saved_items_type_id ON " . $table_prefix . "saved_items (type_id) ";
		// end changes for saved items

		// coupons changes
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN is_auto_apply TINYINT NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN is_auto_apply SMALLINT NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN is_auto_apply BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN is_auto_apply SMALLINT NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "coupons SET is_auto_apply=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "coupons_is_auto_apply ON " . $table_prefix . "coupons (is_auto_apply) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN cart_items_all TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN cart_items_all SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN cart_items_all BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN cart_items_all SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "coupons SET cart_items_all=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN cart_items_ids TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN cart_items_ids TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN cart_items_ids LONGTEXT",
			"db2"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN cart_items_ids LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN min_quantity INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN min_quantity INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN min_quantity INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN min_quantity INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN max_quantity INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN max_quantity INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN max_quantity INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN max_quantity INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN discount_quantity INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN discount_quantity INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN discount_quantity INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN discount_quantity INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN maximum_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN maximum_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN maximum_amount FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN maximum_amount DOUBLE",
		);
		$sqls[] = $sql_types[$db_type];
		// end coupons changes

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.3");
	}

	if (comp_vers("3.3.4", $current_db_version) == 1)
	{
		// recreate shipping_types_states support_products_sites tables
		$sqls[] = " DROP TABLE " . $table_prefix . "support_products_sites ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "support_products_sites (";
		$mysql_sql .= "  `product_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (product_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "support_products_sites (";
		$postgre_sql .= "  product_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (product_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "support_products_sites (";
		$access_sql .= "  [product_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (product_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "support_products_sites (";
		$db2_sql .= "  product_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (product_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " DROP TABLE " . $table_prefix . "shipping_types_states ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_states (";
		$mysql_sql .= "  `shipping_type_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `state_id` INT(11) NOT NULL DEFAULT '0' ";
		$mysql_sql .= "  ,PRIMARY KEY (shipping_type_id,state_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_states (";
		$postgre_sql .= "  shipping_type_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  state_id INT4 NOT NULL DEFAULT '0' ";
		$postgre_sql .= "  ,PRIMARY KEY (shipping_type_id,state_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_states (";
		$access_sql .= "  [shipping_type_id] INTEGER NOT NULL,";
		$access_sql .= "  [state_id] INTEGER NOT NULL ";
		$access_sql .= "  ,PRIMARY KEY (shipping_type_id,state_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_states (";
		$db2_sql .= "  shipping_type_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  state_id INTEGER NOT NULL DEFAULT 0 ";
		$db2_sql .= "  ,PRIMARY KEY (shipping_type_id,state_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		// alter orders tables with new fields session_id,country_id,state_id
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN session_id VARCHAR(32) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN state_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN state_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN state_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN state_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "orders SET state_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "orders_state_id ON " . $table_prefix . "orders (state_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN country_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN country_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN country_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN country_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "orders SET country_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "orders_country_id ON " . $table_prefix . "orders (country_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN delivery_state_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN delivery_state_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN delivery_state_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN delivery_state_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "orders SET delivery_state_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "orders_delivery_state_id ON " . $table_prefix . "orders (delivery_state_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN delivery_country_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN delivery_country_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN delivery_country_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN delivery_country_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "orders SET delivery_country_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "orders_delivery_country_id ON " . $table_prefix . "orders (delivery_country_id) ";

		// alter users tables with new fields country_id,state_id
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN state_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN state_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN state_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "users ADD COLUMN state_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "users SET state_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "users_state_id ON " . $table_prefix . "users (state_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN country_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN country_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN country_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "users ADD COLUMN country_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "users SET country_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "users_country_id ON " . $table_prefix . "users (country_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN delivery_state_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN delivery_state_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN delivery_state_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "users ADD COLUMN delivery_state_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "users SET delivery_state_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "users_delivery_state_id ON " . $table_prefix . "users (delivery_state_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN delivery_country_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN delivery_country_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN delivery_country_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "users ADD COLUMN delivery_country_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "users SET delivery_country_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "users_delivery_country_id ON " . $table_prefix . "users (delivery_country_id) ";
		// end users table changes

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN decimals_number TINYINT DEFAULT '2'",
			"postgre" => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN decimals_number SMALLINT DEFAULT '2'",
			"access"  => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN decimals_number BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN decimals_number SMALLINT DEFAULT 2"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN decimal_point VARCHAR(16) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN thousands_separator VARCHAR(16) ";

		$sqls[] = " UPDATE " . $table_prefix . "currencies SET decimals_number=2 ";
		$sqls[] = " UPDATE " . $table_prefix . "currencies SET decimal_point='.' ";
		$sqls[] = " UPDATE " . $table_prefix . "currencies SET thousands_separator=',' ";

		// coupons sites 
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "coupons_sites (";
		$mysql_sql .= "  `coupon_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (coupon_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "coupons_sites (";
		$postgre_sql .= "  coupon_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (coupon_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "coupons_sites (";
		$access_sql .= "  [coupon_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (coupon_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "coupons_sites (";
		$db2_sql .= "  coupon_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (coupon_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "coupons SET sites_all=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_rules_countries ADD COLUMN country_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_rules_countries ADD COLUMN country_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_rules_countries ADD COLUMN country_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "shipping_rules_countries ADD COLUMN country_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "shipping_rules_countries SET country_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "shipping_rules_country_id ON " . $table_prefix . "shipping_rules_countries (country_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types_countries ADD COLUMN country_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_types_countries ADD COLUMN country_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_types_countries ADD COLUMN country_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "shipping_types_countries ADD COLUMN country_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "shipping_types_countries SET country_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "shipping_types_country_id ON " . $table_prefix . "shipping_types_countries (country_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN country_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN country_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN country_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN country_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "tax_rates SET country_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "tax_rates_country_id ON " . $table_prefix . "tax_rates (country_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN state_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN state_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN state_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN state_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "tax_rates SET state_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "tax_rates_state_id ON " . $table_prefix . "tax_rates (state_id) ";

		// recreate states tables
		$sql = " SELECT COUNT(*) FROM " . $table_prefix . "states ";
		$states_sequence = get_db_value($sql) + 1;

		$sqls[] = " DROP TABLE " . $table_prefix . "states ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "states (";
		$mysql_sql .= "  `state_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `state_code` VARCHAR(8),";
		$mysql_sql .= "  `country_id` INT(11) NOT NULL DEFAULT '0',";
		$mysql_sql .= "  `state_name` VARCHAR(64) NOT NULL";
		$mysql_sql .= "  ,KEY country_id (country_id)";
		$mysql_sql .= "  ,PRIMARY KEY (state_id)";
		$mysql_sql .= "  ,KEY state_code (state_code))";

		if ($db_type == "postgre") {
			$sqls[] = "DROP SEQUENCE seq_" . $table_prefix . "states ";
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "states START " . $states_sequence;
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "states (";
		$postgre_sql .= "  state_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "states'),";
		$postgre_sql .= "  state_code VARCHAR(8),";
		$postgre_sql .= "  country_id INT4 NOT NULL DEFAULT '0',";
		$postgre_sql .= "  state_name VARCHAR(64) NOT NULL";
		$postgre_sql .= "  ,PRIMARY KEY (state_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "states (";
		$access_sql .= "  [state_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [state_code] VARCHAR(8),";
		$access_sql .= "  [country_id] INTEGER NOT NULL,";
		$access_sql .= "  [state_name] VARCHAR(64)";
		$access_sql .= "  ,PRIMARY KEY (state_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "states (";
		$db2_sql .= "  state_id INTEGER NOT NULL,";
		$db2_sql .= "  state_code VARCHAR(8),";
		$db2_sql .= "  country_id INTEGER NOT NULL DEFAULT 0,";
		$db2_sql .= "  state_name VARCHAR(64) NOT NULL";
		$db2_sql .= "  ,PRIMARY KEY (state_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "states_country_id ON " . $table_prefix . "states (country_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "states_state_code ON " . $table_prefix . "states (state_code)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "states AS INTEGER START WITH " . $states_sequence . " INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "states NO CASCADE BEFORE INSERT ON " . $table_prefix . "states REFERENCING NEW AS newr_" . $table_prefix . "states FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "states.state_id IS NULL ) begin atomic set newr_" . $table_prefix . "states.state_id = nextval for seq_" . $table_prefix . "states; end";
		}

		$state_index = 1; $states_codes = array();
		$sql = " SELECT * FROM " . $table_prefix . "states ";
		$db->query($sql);
		while ($db->next_record()) {
			$sql  = " INSERT INTO " . $table_prefix . "states (state_id, state_code, country_id, state_name) VALUES (";
			$sql .= $db->tosql($state_index, INTEGER) . ", ";
			$sql .= $db->tosql($db->f("state_code"), TEXT) . ", ";
			$sql .= $db->tosql(0, INTEGER) . ", ";
			$sql .= $db->tosql($db->f("state_name"), TEXT) . ") ";
			$sqls[] = $sql;
			$states_codes[$db->f("state_code")] = $state_index;
			$state_index++;
		}
		// end recreate states


		// recreate countries tables
		$sql = " SELECT COUNT(*) FROM " . $table_prefix . "countries ";
		$countries_sequence = get_db_value($sql) + 1;

		$sqls[] = " DROP TABLE " . $table_prefix . "countries ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "countries (";
		$mysql_sql .= "  `country_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `country_code` VARCHAR(4) NOT NULL,";
		$mysql_sql .= "  `country_iso_number` VARCHAR(4),";
		$mysql_sql .= "  `country_order` INT(11) default '1',";
		$mysql_sql .= "  `country_name` VARCHAR(64) NOT NULL,";
		$mysql_sql .= "  `currency_code` VARCHAR(4)";
		$mysql_sql .= "  ,KEY country_code (country_code)";
		$mysql_sql .= "  ,PRIMARY KEY (country_id))";

		if ($db_type == "postgre") {
			$sqls[] = "DROP SEQUENCE seq_" . $table_prefix . "countries ";
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "countries START " . $countries_sequence;
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "countries (";
		$postgre_sql .= "  country_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "countries'),";
		$postgre_sql .= "  country_code VARCHAR(4) NOT NULL,";
		$postgre_sql .= "  country_iso_number VARCHAR(4),";
		$postgre_sql .= "  country_order INT4 default '1',";
		$postgre_sql .= "  country_name VARCHAR(64) NOT NULL,";
		$postgre_sql .= "  currency_code VARCHAR(4)";
		$postgre_sql .= "  ,PRIMARY KEY (country_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "countries (";
		$access_sql .= "  [country_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [country_code] VARCHAR(4),";
		$access_sql .= "  [country_iso_number] VARCHAR(4),";
		$access_sql .= "  [country_order] INTEGER,";
		$access_sql .= "  [country_name] VARCHAR(64),";
		$access_sql .= "  [currency_code] VARCHAR(4)";
		$access_sql .= "  ,PRIMARY KEY (country_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "countries (";
		$db2_sql .= "  country_id INTEGER NOT NULL,";
		$db2_sql .= "  country_code VARCHAR(4) NOT NULL,";
		$db2_sql .= "  country_iso_number VARCHAR(4),";
		$db2_sql .= "  country_order INTEGER default 1,";
		$db2_sql .= "  country_name VARCHAR(64) NOT NULL,";
		$db2_sql .= "  currency_code VARCHAR(4)";
		$db2_sql .= "  ,PRIMARY KEY (country_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "countries_country_code ON " . $table_prefix . "countries (country_code)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "countries AS INTEGER START WITH " . $countries_sequence . " INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "countries NO CASCADE BEFORE INSERT ON " . $table_prefix . "countries REFERENCING NEW AS newr_" . $table_prefix . "countries FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "countries.country_id IS NULL ) begin atomic set newr_" . $table_prefix . "countries.country_id = nextval for seq_" . $table_prefix . "countries; end";
		}

		$country_index = 1; $countries_codes = array();
		$sql = " SELECT * FROM " . $table_prefix . "countries ";
		$db->query($sql);
		while ($db->next_record()) {
			$sql  = " INSERT INTO " . $table_prefix . "countries (country_id, country_code, country_iso_number, country_order, country_name, currency_code) VALUES (";
			$sql .= $db->tosql($country_index, INTEGER) . ", ";
			$sql .= $db->tosql($db->f("country_code"), TEXT) . ", ";
			$sql .= $db->tosql($db->f("country_iso_number"), TEXT) . ", ";
			$sql .= $db->tosql($db->f("country_order"), INTEGER) . ", ";
			$sql .= $db->tosql($db->f("country_name"), TEXT) . ", ";
			$sql .= $db->tosql($db->f("currency_code"), TEXT) . ") ";
			$sqls[] = $sql;
			$countries_codes[$db->f("country_code")] = $country_index;
			$country_index++;
		}

		// update old structure with new data and delete old country_code and state_code columns
		$updated_settings = array(
			"show_country_code" => "show_country_id",
			"show_state_code" => "show_state_id",
			"show_delivery_country_code" => "show_delivery_country_id",
			"show_delivery_state_code" => "show_delivery_state_id",
			"country_code_required" => "country_id_required",
			"state_code_required" => "state_id_required",
			"delivery_country_code_required" => "delivery_country_id_required",
			"delivery_state_code_required" => "delivery_state_id_required",
		);
		foreach($updated_settings as $old_setting_name => $new_setting_name) {
			$sql  = " UPDATE " . $table_prefix . "global_settings ";
			$sql .= " SET setting_name=" . $db->tosql($new_setting_name, TEXT);
			$sql .= " WHERE setting_name=" . $db->tosql($old_setting_name, TEXT);
			$sqls[] = $sql;
		}

		$sql = " SELECT country_code FROM " . $table_prefix . "shipping_rules_countries GROUP BY country_code ";
		$db->query($sql);
		while ($db->next_record()) {
			$country_code = $db->f("country_code");
			if ($country_code && isset($countries_codes[$country_code])) {
				$sql  = " UPDATE " . $table_prefix . "shipping_rules_countries SET country_id=" . $db->tosql($countries_codes[$country_code], INTEGER);
				$sql .= " WHERE country_code=" . $db->tosql($country_code, TEXT);
				$sqls[] = $sql;
			} else {
				$sqls[] = " DELETE FROM " . $table_prefix . "shipping_rules_countries WHERE country_code=" . $db->tosql($country_code, TEXT);
			}
		}

		$sql = " SELECT country_code FROM " . $table_prefix . "shipping_types_countries GROUP BY country_code ";
		$db->query($sql);
		while ($db->next_record()) {
			$country_code = $db->f("country_code");
			if ($country_code && isset($countries_codes[$country_code])) {
				$sql  = " UPDATE " . $table_prefix . "shipping_types_countries SET country_id=" . $db->tosql($countries_codes[$country_code], INTEGER);
				$sql .= " WHERE country_code=" . $db->tosql($country_code, TEXT);
				$sqls[] = $sql;
			} else {
				$sqls[] = " DELETE FROM " . $table_prefix . "shipping_types_countries WHERE country_code=" . $db->tosql($country_code, TEXT);
			}
		}

		$sql = " SELECT country_code, state_code FROM " . $table_prefix . "tax_rates GROUP BY country_code, state_code ";
		$db->query($sql);
		while ($db->next_record()) {
			$country_code = $db->f("country_code");
			$state_code = $db->f("state_code");
			if ($country_code && isset($countries_codes[$country_code])) {
				$state_id = 0;
				if ($state_code && isset($states_codes[$state_code])) {
					$state_id = $states_codes[$state_code];
				}
				$sql  = " UPDATE " . $table_prefix . "tax_rates SET country_id=" . $db->tosql($countries_codes[$country_code], INTEGER);
				$sql .= " , state_id=" . $db->tosql($state_id, INTEGER);
				$sql .= " WHERE country_code=" . $db->tosql($country_code, TEXT);
				$sqls[] = $sql;
			} else {
				$sqls[] = " DELETE FROM " . $table_prefix . "tax_rates WHERE country_code=" . $db->tosql($country_code, TEXT);
			}
		}

		// drop old columns
		$sqls[] = " ALTER TABLE ". $table_prefix . "shipping_rules_countries DROP COLUMN country_code ";
		$sqls[] = " ALTER TABLE ". $table_prefix . "shipping_types_countries DROP COLUMN country_code ";
		$sqls[] = " ALTER TABLE ". $table_prefix . "tax_rates DROP COLUMN country_code ";
		$sqls[] = " ALTER TABLE ". $table_prefix . "tax_rates DROP COLUMN state_code ";

		// update orders data
		$sql = " SELECT country_code FROM " . $table_prefix . "orders GROUP BY country_code ";
		$db->query($sql);
		while ($db->next_record()) {
			$country_code = $db->f("country_code");
			if ($country_code && isset($countries_codes[$country_code])) {
				$sql  = " UPDATE " . $table_prefix . "orders SET country_id=" . $db->tosql($countries_codes[$country_code], INTEGER);
				$sql .= " WHERE country_code=" . $db->tosql($country_code, TEXT);
				$sqls[] = $sql;
			}
		}

		$sql = " SELECT delivery_country_code FROM " . $table_prefix . "orders GROUP BY delivery_country_code ";
		$db->query($sql);
		while ($db->next_record()) {
			$country_code = $db->f("delivery_country_code");
			if ($country_code && isset($countries_codes[$country_code])) {
				$sql  = " UPDATE " . $table_prefix . "orders SET delivery_country_id=" . $db->tosql($countries_codes[$country_code], INTEGER);
				$sql .= " WHERE delivery_country_code=" . $db->tosql($country_code, TEXT);
				$sqls[] = $sql;
			}
		}

		$sql = " SELECT state_code FROM " . $table_prefix . "orders GROUP BY state_code ";
		$db->query($sql);
		while ($db->next_record()) {
			$state_code = $db->f("state_code");
			if ($state_code && isset($states_codes[$state_code])) {
				$sql  = " UPDATE " . $table_prefix . "orders SET state_id=" . $db->tosql($states_codes[$state_code], INTEGER);
				$sql .= " WHERE state_code=" . $db->tosql($state_code, TEXT);
				$sqls[] = $sql;
			}
		}

		$sql = " SELECT delivery_state_code FROM " . $table_prefix . "orders GROUP BY delivery_state_code ";
		$db->query($sql);
		while ($db->next_record()) {
			$state_code = $db->f("delivery_state_code");
			if ($state_code && isset($states_codes[$state_code])) {
				$sql  = " UPDATE " . $table_prefix . "orders SET delivery_state_id=" . $db->tosql($states_codes[$state_code], INTEGER);
				$sql .= " WHERE delivery_state_code=" . $db->tosql($state_code, TEXT);
				$sqls[] = $sql;
			}
		}

		// update users data
		$sql = " SELECT country_code FROM " . $table_prefix . "users GROUP BY country_code ";
		$db->query($sql);
		while ($db->next_record()) {
			$country_code = $db->f("country_code");
			if (isset($countries_codes[$country_code])) {
				$sql  = " UPDATE " . $table_prefix . "users SET country_id=" . $db->tosql($countries_codes[$country_code], INTEGER);
				$sql .= " WHERE country_code=" . $db->tosql($country_code, TEXT);
				$sqls[] = $sql;
			}
		}

		$sql = " SELECT delivery_country_code FROM " . $table_prefix . "users GROUP BY delivery_country_code ";
		$db->query($sql);
		while ($db->next_record()) {
			$country_code = $db->f("delivery_country_code");
			if (isset($countries_codes[$country_code])) {
				$sql  = " UPDATE " . $table_prefix . "users SET delivery_country_id=" . $db->tosql($countries_codes[$country_code], INTEGER);
				$sql .= " WHERE delivery_country_code=" . $db->tosql($country_code, TEXT);
				$sqls[] = $sql;
			}
		}

		$sql = " SELECT state_code FROM " . $table_prefix . "users GROUP BY state_code ";
		$db->query($sql);
		while ($db->next_record()) {
			$state_code = $db->f("state_code");
			if ($state_code && isset($states_codes[$state_code])) {
				$sql  = " UPDATE " . $table_prefix . "users SET state_id=" . $db->tosql($states_codes[$state_code], INTEGER);
				$sql .= " WHERE state_code=" . $db->tosql($state_code, TEXT);
				$sqls[] = $sql;
			}
		}

		$sql = " SELECT delivery_state_code FROM " . $table_prefix . "users GROUP BY delivery_state_code ";
		$db->query($sql);
		while ($db->next_record()) {
			$state_code = $db->f("delivery_state_code");
			if ($state_code && isset($states_codes[$state_code])) {
				$sql  = " UPDATE " . $table_prefix . "users SET delivery_state_id=" . $db->tosql($states_codes[$state_code], INTEGER);
				$sql .= " WHERE delivery_state_code=" . $db->tosql($state_code, TEXT);
				$sqls[] = $sql;
			}
		}

		// update global settings
		$sql = " SELECT * FROM " . $table_prefix . "global_settings WHERE setting_type='global' AND setting_name='country_code' ";
		$db->query($sql);
		if ($db->next_record()) {
			$country_code = $db->f("setting_value");
			if (isset($countries_codes[$country_code])) {
				$sql  = " UPDATE " . $table_prefix . "global_settings ";
				$sql .= " SET setting_value=" . $db->tosql($countries_codes[$country_code], TEXT);
				$sql .= " , setting_name='country_id' ";
				$sql .= " WHERE setting_type='global' AND setting_name='country_code' ";
				$sqls[] = $sql;
			} else {
				$sqls[] = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='global' AND setting_name='country_code' ";
			}
		}

		$sql = " SELECT * FROM " . $table_prefix . "global_settings WHERE setting_type='global' AND setting_name='state_code' ";
		$db->query($sql);
		if ($db->next_record()) {
			$state_code = $db->f("setting_value");
			if (isset($countries_codes[$state_code])) {
				$sql  = " UPDATE " . $table_prefix . "global_settings ";
				$sql .= " SET setting_value=" . $db->tosql($countries_codes[$state_code], TEXT);
				$sql .= " , setting_name='state_id' ";
				$sql .= " WHERE setting_type='global' AND setting_name='state_code' ";
				$sqls[] = $sql;
			} else {
				$sqls[] = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='global' AND setting_name='state_code' ";
			}
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.4");
	}


	if (comp_vers("3.3.5", $current_db_version) == 1)
	{
		// tables to assign users groups for each forum
		// table for view forums by different user types
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "forum_view_types (";
		$mysql_sql .= "  `forum_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "forum_view_types (";
		$postgre_sql .= "  forum_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "forum_view_types (";
		$access_sql .= "  [forum_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "forum_view_types (";
		$db2_sql .= "  forum_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN view_forum_types_all TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN view_forum_types_all SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN view_forum_types_all BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN view_forum_types_all SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET view_forum_types_all=1 ";

		// table for view topics by different user types
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "forum_view_topics (";
		$mysql_sql .= "  `forum_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "forum_view_topics (";
		$postgre_sql .= "  forum_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "forum_view_topics (";
		$access_sql .= "  [forum_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "forum_view_topics (";
		$db2_sql .= "  forum_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN view_topics_types_all TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN view_topics_types_all SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN view_topics_types_all BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN view_topics_types_all SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET view_topics_types_all=1 ";

		// table for view topic by different user types
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "forum_view_topic (";
		$mysql_sql .= "  `forum_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "forum_view_topic (";
		$postgre_sql .= "  forum_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "forum_view_topic (";
		$access_sql .= "  [forum_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "forum_view_topic (";
		$db2_sql .= "  forum_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN view_topic_types_all TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN view_topic_types_all SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN view_topic_types_all BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN view_topic_types_all SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET view_topic_types_all=1 ";

		// table for post topics by different user types
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "forum_post_topics (";
		$mysql_sql .= "  `forum_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "forum_post_topics (";
		$postgre_sql .= "  forum_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "forum_post_topics (";
		$access_sql .= "  [forum_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "forum_post_topics (";
		$db2_sql .= "  forum_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN post_topics_types_all TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN post_topics_types_all SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN post_topics_types_all BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN post_topics_types_all SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET post_topics_types_all=1 ";


		// table for post replies by different user types
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "forum_post_replies (";
		$mysql_sql .= "  `forum_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "forum_post_replies (";
		$postgre_sql .= "  forum_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "forum_post_replies (";
		$access_sql .= "  [forum_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "forum_post_replies (";
		$db2_sql .= "  forum_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN post_replies_types_all TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN post_replies_types_all SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN post_replies_types_all BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN post_replies_types_all SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET post_replies_types_all=1 ";

		// table for attachments by different user types
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "forum_attachments_types (";
		$mysql_sql .= "  `forum_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "forum_attachments_types (";
		$postgre_sql .= "  forum_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "forum_attachments_types (";
		$access_sql .= "  [forum_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "forum_attachments_types (";
		$db2_sql .= "  forum_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_attachments TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_attachments SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_attachments BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_attachments SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET allowed_attachments=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN attachments_types_all TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN attachments_types_all SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN attachments_types_all BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN attachments_types_all SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET attachments_types_all=1 ";


		$mysql_sql  = "CREATE TABLE " . $table_prefix . "forum_attachments (";
		$mysql_sql .= "  `attachment_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `forum_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `thread_id` INT(11) default '0',";
		$mysql_sql .= "  `message_id` INT(11) default '0',";
		$mysql_sql .= "  `admin_id` INT(11) default '0',";
		$mysql_sql .= "  `user_id` INT(11) default '0',";
		$mysql_sql .= "  `attachment_status` INT(11) default '0',";
		$mysql_sql .= "  `file_name` VARCHAR(255),";
		$mysql_sql .= "  `file_path` VARCHAR(255),";
		$mysql_sql .= "  `date_added` DATETIME";
		$mysql_sql .= "  ,KEY admin_id (admin_id)";
		$mysql_sql .= "  ,KEY message_id (message_id)";
		$mysql_sql .= "  ,PRIMARY KEY (attachment_id)";
		$mysql_sql .= "  ,KEY support_id (forum_id)";
		$mysql_sql .= "  ,KEY thread_id (thread_id)";
		$mysql_sql .= "  ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "forum_attachments START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "forum_attachments (";
		$postgre_sql .= "  attachment_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "forum_attachments'),";
		$postgre_sql .= "  forum_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  thread_id INT4 default '0',";
		$postgre_sql .= "  message_id INT4 default '0',";
		$postgre_sql .= "  admin_id INT4 default '0',";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  attachment_status INT4 default '0',";
		$postgre_sql .= "  file_name VARCHAR(255),";
		$postgre_sql .= "  file_path VARCHAR(255),";
		$postgre_sql .= "  date_added TIMESTAMP";
		$postgre_sql .= "  ,PRIMARY KEY (attachment_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "forum_attachments (";
		$access_sql .= "  [attachment_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [forum_id] INTEGER,";
		$access_sql .= "  [thread_id] INTEGER,";
		$access_sql .= "  [message_id] INTEGER,";
		$access_sql .= "  [admin_id] INTEGER,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [attachment_status] INTEGER,";
		$access_sql .= "  [file_name] VARCHAR(255),";
		$access_sql .= "  [file_path] VARCHAR(255),";
		$access_sql .= "  [date_added] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (attachment_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "forum_attachments (";
		$db2_sql .= "  attachment_id INTEGER NOT NULL,";
		$db2_sql .= "  forum_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  thread_id INTEGER default 0,";
		$db2_sql .= "  message_id INTEGER default 0,";
		$db2_sql .= "  admin_id INTEGER default 0,";
		$db2_sql .= "  user_id INTEGER default 0,";
		$db2_sql .= "  attachment_status INTEGER default 0,";
		$db2_sql .= "  file_name VARCHAR(255),";
		$db2_sql .= "  file_path VARCHAR(255),";
		$db2_sql .= "  date_added TIMESTAMP";
		$db2_sql .= "  ,PRIMARY KEY (attachment_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "forum_attachments_admin_id ON " . $table_prefix . "forum_attachments (admin_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "forum_attachments_message_id ON " . $table_prefix . "forum_attachments (message_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "forum_attachments_support_id ON " . $table_prefix . "forum_attachments (forum_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "forum_attachments_thread_id ON " . $table_prefix . "forum_attachments (thread_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "forum_attachments_user_id ON " . $table_prefix . "forum_attachments (user_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "forum_attachments AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "forum_at_31 NO CASCADE BEFORE INSERT ON " . $table_prefix . "forum_attachments REFERENCING NEW AS newr_" . $table_prefix . "forum_attachments FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "forum_attachments.attachment_id IS NULL ) begin atomic set newr_" . $table_prefix . "forum_attachments.attachment_id = nextval for seq_" . $table_prefix . "forum_attachments; end";
		}
		// end forum changes

		// items properties changes
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN property_price_type TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN property_price_type SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN property_price_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN property_price_type SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN free_price_type TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN free_price_type SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN free_price_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN free_price_type SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN free_price_length INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN free_price_length INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN free_price_length INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN free_price_length INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN max_limit_type TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN max_limit_type SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN max_limit_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN max_limit_type SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN max_limit_length INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN max_limit_length INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN max_limit_length INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN max_limit_length INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		// end items properties changes

		// support changes
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN user_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN user_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN user_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN user_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "support_attachments SET user_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "support_attachments_user_id ON " . $table_prefix . "support_attachments (user_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN priority_expiry DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN priority_expiry TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN priority_expiry DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN priority_expiry TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN admin_id_added_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN admin_id_added_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN admin_id_added_by INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN admin_id_added_by INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN admin_id_modified_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN admin_id_modified_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN admin_id_modified_by INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN admin_id_modified_by INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN date_added DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN date_added TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN date_added DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN date_added TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN date_modified DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN date_modified TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN date_modified DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_users_priorities ADD COLUMN date_modified TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];
		// end support changes	

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.5");
	}

	if (comp_vers("3.3.6", $current_db_version) == 1)
	{
		// new global price table and related fields table
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "prices (";
		$mysql_sql .= "  `price_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `price_title` VARCHAR(64),";
		$mysql_sql .= "  `price_amount` DOUBLE(16,2) default '0',";
		$mysql_sql .= "  `price_description` TEXT";
		$mysql_sql .= "  ,PRIMARY KEY (price_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "prices START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "prices (";
		$postgre_sql .= "  price_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "prices'),";
		$postgre_sql .= "  price_title VARCHAR(64),";
		$postgre_sql .= "  price_amount FLOAT4 default '0',";
		$postgre_sql .= "  price_description TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (price_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "prices (";
		$access_sql .= "  [price_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [price_title] VARCHAR(64),";
		$access_sql .= "  [price_amount] FLOAT,";
		$access_sql .= "  [price_description] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (price_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "prices (";
		$db2_sql .= "  price_id INTEGER NOT NULL,";
		$db2_sql .= "  price_title VARCHAR(64),";
		$db2_sql .= "  price_amount DOUBLE default 0,";
		$db2_sql .= "  price_description LONG VARCHAR";
		$db2_sql .= "  ,PRIMARY KEY (price_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "prices AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "prices NO CASCADE BEFORE INSERT ON " . $table_prefix . "prices REFERENCING NEW AS newr_" . $table_prefix . "prices FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "prices.price_id IS NULL ) begin atomic set newr_" . $table_prefix . "prices.price_id = nextval for seq_" . $table_prefix . "prices; end";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN price_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN price_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN price_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN price_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items SET price_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "items_price_id ON " . $table_prefix . "items (price_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_price_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_price_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_price_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_price_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items SET trade_price_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "items_trade_price_id ON " . $table_prefix . "items (trade_price_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN buying_price_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN buying_price_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN buying_price_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN buying_price_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items SET buying_price_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "items_buying_price_id ON " . $table_prefix . "items (buying_price_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN properties_price_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN properties_price_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN properties_price_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN properties_price_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items SET properties_price_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "items_properties_price_id ON " . $table_prefix . "items (properties_price_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sales_price_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sales_price_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sales_price_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sales_price_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items SET sales_price_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "items_sales_price_id ON " . $table_prefix . "items (sales_price_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_sales_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_sales_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_sales_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_sales_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items SET trade_sales_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "items_trade_sales_id ON " . $table_prefix . "items (trade_sales_id) ";
		// end prices changes

		// trade prices for properites
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN trade_additional_price DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN trade_additional_price FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN trade_additional_price FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN trade_additional_price DOUBLE",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN trade_additional_price DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN trade_additional_price FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN trade_additional_price FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN trade_additional_price DOUBLE",
		);
		$sqls[] = $sql_types[$db_type];
		// end trade prices for properites

		// custom friendly urls
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "friendly_urls (";
		$mysql_sql .= "  `friendly_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `script_name` VARCHAR(255),";
		$mysql_sql .= "  `friendly_url` VARCHAR(255),";
		$mysql_sql .= "  `sites_all` TINYINT default '1'";
		$mysql_sql .= "  ,KEY friendly_url (friendly_url)";
		$mysql_sql .= "  ,PRIMARY KEY (friendly_id)";
		$mysql_sql .= "  ,KEY script_name (script_name)";
		$mysql_sql .= "  ,KEY sites_all (sites_all))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "friendly_urls START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "friendly_urls (";
		$postgre_sql .= "  friendly_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "friendly_urls'),";
		$postgre_sql .= "  script_name VARCHAR(255),";
		$postgre_sql .= "  friendly_url VARCHAR(255),";
		$postgre_sql .= "  sites_all SMALLINT default '1'";
		$postgre_sql .= "  ,PRIMARY KEY (friendly_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "friendly_urls (";
		$access_sql .= "  [friendly_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [script_name] VARCHAR(255),";
		$access_sql .= "  [friendly_url] VARCHAR(255),";
		$access_sql .= "  [sites_all] BYTE";
		$access_sql .= "  ,PRIMARY KEY (friendly_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "friendly_urls (";
		$db2_sql .= "  friendly_id INTEGER NOT NULL,";
		$db2_sql .= "  script_name VARCHAR(255),";
		$db2_sql .= "  friendly_url VARCHAR(255),";
		$db2_sql .= "  sites_all SMALLINT default 1";
		$db2_sql .= "  ,PRIMARY KEY (friendly_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "friendly_urls_friendly_url ON " . $table_prefix . "friendly_urls (friendly_url)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "friendly_urls_script_name ON " . $table_prefix . "friendly_urls (script_name)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "friendly_urls_sites_all ON " . $table_prefix . "friendly_urls (sites_all)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "friendly_urls AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "friendly_42 NO CASCADE BEFORE INSERT ON " . $table_prefix . "friendly_urls REFERENCING NEW AS newr_" . $table_prefix . "friendly_urls FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "friendly_urls.friendly_id IS NULL ) begin atomic set newr_" . $table_prefix . "friendly_urls.friendly_id = nextval for seq_" . $table_prefix . "friendly_urls; end";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "friendly_urls_sites (";
		$mysql_sql .= "  `friendly_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (friendly_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "friendly_urls_sites (";
		$postgre_sql .= "  friendly_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (friendly_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "friendly_urls_sites (";
		$access_sql .= "  [friendly_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (friendly_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "friendly_urls_sites (";
		$db2_sql .= "  friendly_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (friendly_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];
		// end custom friendly urls

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.6");
	}

	if (comp_vers("3.3.7", $current_db_version) == 1)
	{
		// add value_order field into items_properties_values
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN value_order INT(11) DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN value_order INT4 DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN value_order INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN value_order INTEGER DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items_properties_values SET value_order=1 ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "reminders (";
		$mysql_sql .= "  `reminder_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `user_id` INT(11) default '0',";
		$mysql_sql .= "  `start_date` DATETIME,";
		$mysql_sql .= "  `end_date` DATETIME,";
		$mysql_sql .= "  `reminder_year` INT(11) default '0',";
		$mysql_sql .= "  `reminder_month` TINYINT default '0',";
		$mysql_sql .= "  `reminder_day` TINYINT default '0',";
		$mysql_sql .= "  `reminder_weekdays` TINYINT default '0',";
		$mysql_sql .= "  `reminder_title` VARCHAR(255),";
		$mysql_sql .= "  `reminder_notes` TEXT,";
		$mysql_sql .= "  `date_added` DATETIME,";
		$mysql_sql .= "  `date_modified` DATETIME";
		$mysql_sql .= "  ,KEY end_date (end_date)";
		$mysql_sql .= "  ,PRIMARY KEY (reminder_id)";
		$mysql_sql .= "  ,KEY reminder_day (reminder_day)";
		$mysql_sql .= "  ,KEY reminder_month (reminder_month)";
		$mysql_sql .= "  ,KEY reminder_weekdays (reminder_weekdays)";
		$mysql_sql .= "  ,KEY reminder_year (reminder_year)";
		$mysql_sql .= "  ,KEY start_date (start_date)";
		$mysql_sql .= "  ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "reminders START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "reminders (";
		$postgre_sql .= "  reminder_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "reminders'),";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  start_date TIMESTAMP,";
		$postgre_sql .= "  end_date TIMESTAMP,";
		$postgre_sql .= "  reminder_year INT4 default '0',";
		$postgre_sql .= "  reminder_month SMALLINT default '0',";
		$postgre_sql .= "  reminder_day SMALLINT default '0',";
		$postgre_sql .= "  reminder_weekdays SMALLINT default '0',";
		$postgre_sql .= "  reminder_title VARCHAR(255),";
		$postgre_sql .= "  reminder_notes TEXT,";
		$postgre_sql .= "  date_added TIMESTAMP,";
		$postgre_sql .= "  date_modified TIMESTAMP";
		$postgre_sql .= "  ,PRIMARY KEY (reminder_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "reminders (";
		$access_sql .= "  [reminder_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [start_date] DATETIME,";
		$access_sql .= "  [end_date] DATETIME,";
		$access_sql .= "  [reminder_year] INTEGER,";
		$access_sql .= "  [reminder_month] BYTE,";
		$access_sql .= "  [reminder_day] BYTE,";
		$access_sql .= "  [reminder_weekdays] BYTE,";
		$access_sql .= "  [reminder_title] VARCHAR(255),";
		$access_sql .= "  [reminder_notes] LONGTEXT,";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [date_modified] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (reminder_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "reminders (";
		$db2_sql .= "  reminder_id INTEGER NOT NULL,";
		$db2_sql .= "  user_id INTEGER default 0,";
		$db2_sql .= "  start_date TIMESTAMP,";
		$db2_sql .= "  end_date TIMESTAMP,";
		$db2_sql .= "  reminder_year INTEGER default 0,";
		$db2_sql .= "  reminder_month SMALLINT default 0,";
		$db2_sql .= "  reminder_day SMALLINT default 0,";
		$db2_sql .= "  reminder_weekdays SMALLINT default 0,";
		$db2_sql .= "  reminder_title VARCHAR(255),";
		$db2_sql .= "  reminder_notes LONG VARCHAR,";
		$db2_sql .= "  date_added TIMESTAMP,";
		$db2_sql .= "  date_modified TIMESTAMP";
		$db2_sql .= "  ,PRIMARY KEY (reminder_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "reminders_end_date ON " . $table_prefix . "reminders (end_date)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "reminders_reminder_day ON " . $table_prefix . "reminders (reminder_day)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "reminders_reminder_month ON " . $table_prefix . "reminders (reminder_month)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "reminders_reminder_weekdays ON " . $table_prefix . "reminders (reminder_weekdays)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "reminders_reminder_year ON " . $table_prefix . "reminders (reminder_year)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "reminders_start_date ON " . $table_prefix . "reminders (start_date)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "reminders_user_id ON " . $table_prefix . "reminders (user_id)";

		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "reminders AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "reminders NO CASCADE BEFORE INSERT ON " . $table_prefix . "reminders REFERENCING NEW AS newr_" . $table_prefix . "reminders FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "reminders.reminder_id IS NULL ) begin atomic set newr_" . $table_prefix . "reminders.reminder_id = nextval for seq_" . $table_prefix . "reminders; end";
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.7");
	}

	if (comp_vers("3.3.8", $current_db_version) == 1)
	{
		// add filters tables
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "filters (";
		$mysql_sql .= "  `filter_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `filter_type` VARCHAR(32),";
		$mysql_sql .= "  `filter_name` VARCHAR(255),";
		$mysql_sql .= "  `filter_desc` TEXT";
		$mysql_sql .= "  ,PRIMARY KEY (filter_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "filters START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "filters (";
		$postgre_sql .= "  filter_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "filters'),";
		$postgre_sql .= "  filter_type VARCHAR(32),";
		$postgre_sql .= "  filter_name VARCHAR(255),";
		$postgre_sql .= "  filter_desc TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (filter_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "filters (";
		$access_sql .= "  [filter_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [filter_type] VARCHAR(32),";
		$access_sql .= "  [filter_name] VARCHAR(255),";
		$access_sql .= "  [filter_desc] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (filter_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "filters (";
		$db2_sql .= "  filter_id INTEGER NOT NULL,";
		$db2_sql .= "  filter_type VARCHAR(32),";
		$db2_sql .= "  filter_name VARCHAR(255),";
		$db2_sql .= "  filter_desc LONG VARCHAR";
		$db2_sql .= "  ,PRIMARY KEY (filter_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "filters AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "filters NO CASCADE BEFORE INSERT ON " . $table_prefix . "filters REFERENCING NEW AS newr_" . $table_prefix . "filters FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "filters.filter_id IS NULL ) begin atomic set newr_" . $table_prefix . "filters.filter_id = nextval for seq_" . $table_prefix . "filters; end";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "filters_properties (";
		$mysql_sql .= "  `property_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `filter_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `property_order` INT(11) NOT NULL default '1',";
		$mysql_sql .= "  `property_name` VARCHAR(255),";
		$mysql_sql .= "  `property_type` VARCHAR(64),";
		$mysql_sql .= "  `property_value` VARCHAR(255),";
		$mysql_sql .= "  `filter_from_sql` TEXT,";
		$mysql_sql .= "  `filter_join_sql` TEXT,";
		$mysql_sql .= "  `filter_where_sql` TEXT,";
		$mysql_sql .= "  `list_table` VARCHAR(64),";
		$mysql_sql .= "  `list_field_id` VARCHAR(64),";
		$mysql_sql .= "  `list_field_title` VARCHAR(64),";
		$mysql_sql .= "  `list_field_total` VARCHAR(64),";
		$mysql_sql .= "  `list_sql` TEXT";
		$mysql_sql .= "  ,KEY filter_id (filter_id)";
		$mysql_sql .= "  ,PRIMARY KEY (property_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "filters_properties START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "filters_properties (";
		$postgre_sql .= "  property_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "filters_properties'),";
		$postgre_sql .= "  filter_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  property_order INT4 NOT NULL default '1',";
		$postgre_sql .= "  property_name VARCHAR(255),";
		$postgre_sql .= "  property_type VARCHAR(64),";
		$postgre_sql .= "  property_value VARCHAR(255),";
		$postgre_sql .= "  filter_from_sql TEXT,";
		$postgre_sql .= "  filter_join_sql TEXT,";
		$postgre_sql .= "  filter_where_sql TEXT,";
		$postgre_sql .= "  list_table VARCHAR(64),";
		$postgre_sql .= "  list_field_id VARCHAR(64),";
		$postgre_sql .= "  list_field_title VARCHAR(64),";
		$postgre_sql .= "  list_field_total VARCHAR(64),";
		$postgre_sql .= "  list_sql TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (property_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "filters_properties (";
		$access_sql .= "  [property_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [filter_id] INTEGER NOT NULL,";
		$access_sql .= "  [property_order] INTEGER NOT NULL,";
		$access_sql .= "  [property_name] VARCHAR(255),";
		$access_sql .= "  [property_type] VARCHAR(64),";
		$access_sql .= "  [property_value] VARCHAR(255),";
		$access_sql .= "  [filter_from_sql] LONGTEXT,";
		$access_sql .= "  [filter_join_sql] LONGTEXT,";
		$access_sql .= "  [filter_where_sql] LONGTEXT,";
		$access_sql .= "  [list_table] VARCHAR(64),";
		$access_sql .= "  [list_field_id] VARCHAR(64),";
		$access_sql .= "  [list_field_title] VARCHAR(64),";
		$access_sql .= "  [list_field_total] VARCHAR(64),";
		$access_sql .= "  [list_sql] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (property_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "filters_properties (";
		$db2_sql .= "  property_id INTEGER NOT NULL,";
		$db2_sql .= "  filter_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  property_order INTEGER NOT NULL default 1,";
		$db2_sql .= "  property_name VARCHAR(255),";
		$db2_sql .= "  property_type VARCHAR(64),";
		$db2_sql .= "  property_value VARCHAR(255),";
		$db2_sql .= "  filter_from_sql LONG VARCHAR,";
		$db2_sql .= "  filter_join_sql LONG VARCHAR,";
		$db2_sql .= "  filter_where_sql LONG VARCHAR,";
		$db2_sql .= "  list_table VARCHAR(64),";
		$db2_sql .= "  list_field_id VARCHAR(64),";
		$db2_sql .= "  list_field_title VARCHAR(64),";
		$db2_sql .= "  list_field_total VARCHAR(64),";
		$db2_sql .= "  list_sql LONG VARCHAR";
		$db2_sql .= "  ,PRIMARY KEY (property_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "filters_properties_filter ON " . $table_prefix . "filters_properties (filter_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "filters_properties AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "filters__31 NO CASCADE BEFORE INSERT ON " . $table_prefix . "filters_properties REFERENCING NEW AS newr_" . $table_prefix . "filters_properties FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "filters_properties.property_id IS NULL ) begin atomic set newr_" . $table_prefix . "filters_properties.property_id = nextval for seq_" . $table_prefix . "filters_properties; end";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "filters_properties_values (";
		$mysql_sql .= "  `value_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `property_id` INT(11) default '0',";
		$mysql_sql .= "  `value_order` INT(11),";
		$mysql_sql .= "  `list_value_id` VARCHAR(128),";
		$mysql_sql .= "  `list_value_title` VARCHAR(255),";
		$mysql_sql .= "  `filter_where_sql` TEXT";
		$mysql_sql .= "  ,KEY property_id (property_id)";
		$mysql_sql .= "  ,PRIMARY KEY (value_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "filters_properties_values START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "filters_properties_values (";
		$postgre_sql .= "  value_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "filters_properties_values'),";
		$postgre_sql .= "  property_id INT4 default '0',";
		$postgre_sql .= "  value_order INT4,";
		$postgre_sql .= "  list_value_id VARCHAR(128),";
		$postgre_sql .= "  list_value_title VARCHAR(255),";
		$postgre_sql .= "  filter_where_sql TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (value_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "filters_properties_values (";
		$access_sql .= "  [value_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [property_id] INTEGER,";
		$access_sql .= "  [value_order] INTEGER,";
		$access_sql .= "  [list_value_id] VARCHAR(128),";
		$access_sql .= "  [list_value_title] VARCHAR(255),";
		$access_sql .= "  [filter_where_sql] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (value_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "filters_properties_values (";
		$db2_sql .= "  value_id INTEGER NOT NULL,";
		$db2_sql .= "  property_id INTEGER default 0,";
		$db2_sql .= "  value_order INTEGER,";
		$db2_sql .= "  list_value_id VARCHAR(128),";
		$db2_sql .= "  list_value_title VARCHAR(255),";
		$db2_sql .= "  filter_where_sql LONG VARCHAR";
		$db2_sql .= "  ,PRIMARY KEY (value_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "filters_properties_value_14 ON " . $table_prefix . "filters_properties_values (property_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "filters_properties_values AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "filters__32 NO CASCADE BEFORE INSERT ON " . $table_prefix . "filters_properties_values REFERENCING NEW AS newr_" . $table_prefix . "filters_properties_values FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "filters_properties_values.value_id IS NULL ) begin atomic set newr_" . $table_prefix . "filters_properties_values.value_id = nextval for seq_" . $table_prefix . "filters_properties_values; end";
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.8");
	}

	if (comp_vers("3.3.9", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN is_default TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN is_default SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN is_default BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN is_default SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		// update default currency
		$default_country_id = get_setting_value($settings, "country_id", "");
		$default_country_code = get_setting_value($settings, "country_code", "");
		if ($default_country_id) {
			$default_state_id = get_setting_value($settings, "state_id", "");
			$sql  = " SELECT tax_id ";
			$sql .= " FROM " . $table_prefix . "tax_rates ";
			$sql .= " WHERE country_id=" . $db->tosql($default_country_id, INTEGER);
			if ($default_state_id) {
				$sql .= " AND state_id=" . $db->tosql($default_state_id, INTEGER);
			} else {
				$sql .= " AND state_id=0 ";
			}
			$db->query($sql);
			if ($db->next_record()) {
				$tax_id = $db->f("tax_id");
				$sqls[] = " UPDATE " . $table_prefix . "tax_rates SET is_default=1 WHERE tax_id=" . $db->tosql($tax_id, INTEGER);
			}
		} else if ($default_country_code) {
			$default_state_code = get_setting_value($settings, "state_code", "");
			$sql  = " SELECT tax_id ";
			$sql .= " FROM " . $table_prefix . "tax_rates ";
			$sql .= " WHERE country_code=" . $db->tosql($default_country_code, TEXT);
			if ($default_state_code) {
				$sql .= " AND state_code=" . $db->tosql($default_state_code, TEXT);
			} else {
				$sql .= " AND state_code IS NULL ";
			}
			$db->query($sql);
			if ($db->next_record()) {
				$tax_id = $db->f("tax_id");
				$sqls[] = " UPDATE " . $table_prefix . "tax_rates SET is_default=1 WHERE tax_id=" . $db->tosql($tax_id, INTEGER);
			}
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_table TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_table SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_table BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_table SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_grid TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_grid SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_grid BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_grid SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "items SET hide_add_table=1, hide_add_grid=1 WHERE hide_add_list=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN use_on_table TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN use_on_table SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN use_on_table BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN use_on_table SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN use_on_grid TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN use_on_grid SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN use_on_grid BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN use_on_grid SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "items_properties SET use_on_table=1, use_on_grid=1 WHERE use_on_list=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN user_invoice_activation TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN user_invoice_activation SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN user_invoice_activation BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN user_invoice_activation SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET user_invoice_activation=1 ";


		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products', 'option_positive_price_right', ' (+ ')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products', 'option_positive_price_left', ')')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products', 'option_negative_price_right', ' (- ')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products', 'option_negative_price_left', ')')";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.9");
	}

	if (comp_vers("3.3.10", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "saved_carts ADD COLUMN site_id INT(11) NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "saved_carts ADD COLUMN site_id INT4 NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "saved_carts ADD COLUMN site_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "saved_carts ADD COLUMN site_id INTEGER NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "saved_carts SET site_id=1 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "saved_carts_site_id ON " . $table_prefix . "saved_carts (site_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "saved_items ADD COLUMN site_id INT(11) NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "saved_items ADD COLUMN site_id INT4 NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "saved_items ADD COLUMN site_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "saved_items ADD COLUMN site_id INTEGER NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "saved_items SET site_id=1 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "saved_items_site_id ON " . $table_prefix . "saved_items (site_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "saved_items ADD COLUMN quantity_bought INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "saved_items ADD COLUMN quantity_bought INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "saved_items ADD COLUMN quantity_bought INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "saved_items ADD COLUMN quantity_bought INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN cart_item_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN cart_item_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN cart_item_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN cart_item_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " CREATE INDEX " . $table_prefix . "orders_items_cart_item_id ON " . $table_prefix . "orders_items (cart_item_id) ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "users_credits (";
		$mysql_sql .= "  `credit_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `user_id` INT(11) default '0',";
		$mysql_sql .= "  `order_id` INT(11) default '0',";
		$mysql_sql .= "  `credit_amount` DOUBLE(16,4) default '0',";
		$mysql_sql .= "  `credit_action` INT(11) default '0',";
		$mysql_sql .= "  `admin_id_added_by` INT(11) default '0',";
		$mysql_sql .= "  `admin_id_modified_by` INT(11) default '0',";
		$mysql_sql .= "  `date_added` DATETIME,";
		$mysql_sql .= "  `date_modified` DATETIME";
		$mysql_sql .= "  ,KEY date_added (date_added)";
		$mysql_sql .= "  ,KEY order_id (order_id)";
		$mysql_sql .= "  ,PRIMARY KEY (credit_id)";
		$mysql_sql .= "  ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "users_credits START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "users_credits (";
		$postgre_sql .= "  credit_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "users_credits'),";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  order_id INT4 default '0',";
		$postgre_sql .= "  credit_amount FLOAT4 default '0',";
		$postgre_sql .= "  credit_action INT4 default '0',";
		$postgre_sql .= "  admin_id_added_by INT4 default '0',";
		$postgre_sql .= "  admin_id_modified_by INT4 default '0',";
		$postgre_sql .= "  date_added TIMESTAMP,";
		$postgre_sql .= "  date_modified TIMESTAMP";
		$postgre_sql .= "  ,PRIMARY KEY (credit_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "users_credits (";
		$access_sql .= "  [credit_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [order_id] INTEGER,";
		$access_sql .= "  [credit_amount] FLOAT,";
		$access_sql .= "  [credit_action] INTEGER,";
		$access_sql .= "  [admin_id_added_by] INTEGER,";
		$access_sql .= "  [admin_id_modified_by] INTEGER,";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [date_modified] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (credit_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "users_credits (";
		$db2_sql .= "  credit_id INTEGER NOT NULL,";
		$db2_sql .= "  user_id INTEGER default 0,";
		$db2_sql .= "  order_id INTEGER default 0,";
		$db2_sql .= "  credit_amount DOUBLE default 2,";
		$db2_sql .= "  credit_action INTEGER default 0,";
		$db2_sql .= "  admin_id_added_by INTEGER default 0,";
		$db2_sql .= "  admin_id_modified_by INTEGER default 0,";
		$db2_sql .= "  date_added TIMESTAMP,";
		$db2_sql .= "  date_modified TIMESTAMP";
		$db2_sql .= "  ,PRIMARY KEY (credit_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_credits_date_added ON " . $table_prefix . "users_credits (date_added)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_credits_order_id ON " . $table_prefix . "users_credits (order_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_credits_user_id ON " . $table_prefix . "users_credits (user_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "users_credits AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "users_cr_117 NO CASCADE BEFORE INSERT ON " . $table_prefix . "users_credits REFERENCING NEW AS newr_" . $table_prefix . "users_credits FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "users_credits.credit_id IS NULL ) begin atomic set newr_" . $table_prefix . "users_credits.credit_id = nextval for seq_" . $table_prefix . "users_credits; end";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN credit_balance DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN credit_balance FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN credit_balance FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "users ADD COLUMN credit_balance DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN credit_amount DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN credit_amount FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN credit_amount FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN credit_amount DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "google_base_attributes (";
		$mysql_sql .= "  `attribute_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `attribute_name` VARCHAR(255),";
		$mysql_sql .= "  `attribute_type` VARCHAR(16),";
		$mysql_sql .= "  `value_type` VARCHAR(32)";
		$mysql_sql .= "  ,PRIMARY KEY (attribute_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "google_base_attributes START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "google_base_attributes (";
		$postgre_sql .= "  attribute_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "google_base_attributes'),";
		$postgre_sql .= "  attribute_name VARCHAR(255),";
		$postgre_sql .= "  attribute_type VARCHAR(16),";
		$postgre_sql .= "  value_type VARCHAR(32)";
		$postgre_sql .= "  ,PRIMARY KEY (attribute_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "google_base_attributes (";
		$access_sql .= "  [attribute_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [attribute_name] VARCHAR(255),";
		$access_sql .= "  [attribute_type] VARCHAR(16),";
		$access_sql .= "  [value_type] VARCHAR(32)";
		$access_sql .= "  ,PRIMARY KEY (attribute_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "google_base_attributes (";
		$db2_sql .= "  attribute_id INTEGER NOT NULL,";
		$db2_sql .= "  attribute_name VARCHAR(255),";
		$db2_sql .= "  attribute_type VARCHAR(16),";
		$db2_sql .= "  value_type VARCHAR(32)";
		$db2_sql .= "  ,PRIMARY KEY (attribute_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "google_base_attributes AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "google_b_46 NO CASCADE BEFORE INSERT ON " . $table_prefix . "google_base_attributes REFERENCING NEW AS newr_" . $table_prefix . "google_base_attributes FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "google_base_attributes.attribute_id IS NULL ) begin atomic set newr_" . $table_prefix . "google_base_attributes.attribute_id = nextval for seq_" . $table_prefix . "google_base_attributes; end";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "google_base_types (";
		$mysql_sql .= "  `type_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `type_name` VARCHAR(255)";
		$mysql_sql .= "  ,PRIMARY KEY (type_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "google_base_types START 23";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "google_base_types (";
		$postgre_sql .= "  type_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "google_base_types'),";
		$postgre_sql .= "  type_name VARCHAR(255)";
		$postgre_sql .= "  ,PRIMARY KEY (type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "google_base_types (";
		$access_sql .= "  [type_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [type_name] VARCHAR(255)";
		$access_sql .= "  ,PRIMARY KEY (type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "google_base_types (";
		$db2_sql .= "  type_id INTEGER NOT NULL,";
		$db2_sql .= "  type_name VARCHAR(255)";
		$db2_sql .= "  ,PRIMARY KEY (type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "google_base_types AS INTEGER START WITH 23 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "google_b_47 NO CASCADE BEFORE INSERT ON " . $table_prefix . "google_base_types REFERENCING NEW AS newr_" . $table_prefix . "google_base_types FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "google_base_types.type_id IS NULL ) begin atomic set newr_" . $table_prefix . "google_base_types.type_id = nextval for seq_" . $table_prefix . "google_base_types; end";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "google_base_types_attributes (";
		$mysql_sql .= "  `type_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `attribute_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `required` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (type_id,attribute_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "google_base_types_attributes (";
		$postgre_sql .= "  type_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  attribute_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  required SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (type_id,attribute_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "google_base_types_attributes (";
		$access_sql .= "  [type_id] INTEGER NOT NULL,";
		$access_sql .= "  [attribute_id] INTEGER NOT NULL,";
		$access_sql .= "  [required] BYTE";
		$access_sql .= "  ,PRIMARY KEY (type_id,attribute_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "google_base_types_attributes (";
		$db2_sql .= "  type_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  attribute_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  required SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (type_id,attribute_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN google_base_type_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN google_base_type_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN google_base_type_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN google_base_type_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " CREATE INDEX " . $table_prefix . "categories_google_base_type ON " . $table_prefix . "categories (google_base_type_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN google_base_type_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN google_base_type_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN google_base_type_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN google_base_type_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " CREATE INDEX " . $table_prefix . "item_types_google_base_t_18 ON " . $table_prefix . "item_types (google_base_type_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN google_base_type_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN google_base_type_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN google_base_type_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN google_base_type_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " CREATE INDEX " . $table_prefix . "items_google_base_type_id ON " . $table_prefix . "items (google_base_type_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "features ADD COLUMN google_base_attribute_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "features ADD COLUMN google_base_attribute_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "features ADD COLUMN google_base_attribute_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "features ADD COLUMN google_base_attribute_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " CREATE INDEX " . $table_prefix . "features_google_base_att_14 ON " . $table_prefix . "features (google_base_attribute_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "features_default ADD COLUMN google_base_attribute_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "features_default ADD COLUMN google_base_attribute_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "features_default ADD COLUMN google_base_attribute_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "features_default ADD COLUMN google_base_attribute_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " CREATE INDEX " . $table_prefix . "features_default_google__15 ON " . $table_prefix . "features_default (google_base_attribute_id) ";

		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (1 , 'Apparel' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (2 , 'Books' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (3 , 'Consumer Electronics: Cell Phones' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (4 , 'Consumer Electronics: Computers' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (5 , 'Consumer Electronics: Digital Cameras' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (6 , 'Consumer Electronics: Monitors' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (7 , 'Consumer Electronics: MP3 Players' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (8 , 'Consumer Electronics: Printers' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (9 , 'Consumer Electronics: Televisions' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (10 , 'Consumer Electronics: Video Cameras and Camcorders' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (11 , 'Consumer Electronics: Washers' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (12 , 'Consumer Electronics: Other' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (13 , 'Home and Garden: Furniture' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (14 , 'Home and Garden: Kitchen Appliances' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (15 , 'Home and Garden: Rugs' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (16 , 'Home and Garden: Other' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (17 , 'Jewelry' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (18 , 'Music' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (19 , 'Movies' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (20 , 'Shoes' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (21 , 'Toys' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types (type_id,type_name) VALUES (22 , 'Video and PC Games' )";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.10");
	}
	
	if (comp_vers("3.3.11", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "pages ADD COLUMN is_site_map TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "pages ADD COLUMN is_site_map SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "pages ADD COLUMN is_site_map BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "pages ADD COLUMN is_site_map SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "pages SET is_site_map=1 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "pages_is_site_map ON " . $table_prefix . "pages (is_site_map) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN tax_prices_type TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN tax_prices_type SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN tax_prices_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN tax_prices_type SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
		$sqls[] = " UPDATE " . $table_prefix . "orders SET tax_prices_type=" . $db->tosql($tax_prices_type, INTEGER);

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.11");
	}

	if (comp_vers("3.3.12", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN credit_action INT(11) DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN credit_action INT4 DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN credit_action INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN credit_action INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET credit_action=points_action";
		
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(1, 'actor', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(2, 'age_range', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(3, 'artist', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(4, 'aspect_ratio', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(5, 'author', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(6, 'battery_life', 'g', 'int')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(7, 'binding', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(8, 'capacity', 'g', 'intUnit')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(9, 'color', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(10, 'color_output', 'g', 'boolean')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(11, 'department', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(12, 'director', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(13, 'display_type', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(14, 'edition', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(15, 'feature', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(16, 'focus_type', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(17, 'format', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(18, 'functions', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(19, 'genre', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(20, 'heel_height', 'g', 'floatUnit')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(21, 'height', 'g', 'floatUnit')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(22, 'installation', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(23, 'length', 'g', 'floatUnit')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(24, 'load_type', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(25, 'made_in', 'g', 'location')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(26, 'material', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(27, 'megapixels', 'g', 'floatUnit')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(28, 'memory_card_slot', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(29, 'occasion', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(30, 'operating_system', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(31, 'optical_drive', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(32, 'pages', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(33, 'payment_accepted', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(34, 'payment_notes', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(35, 'pickup', 'g', 'boolean')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(36, 'platform', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(37, 'processor_speed', 'g', 'floatUnit')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(38, 'publisher', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(39, 'rating', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(40, 'recommended_usage', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(41, 'resolution', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(42, 'screen_size', 'g', 'intUnit')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(43, 'shoe_width', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(44, 'size', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(45, 'style', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(46, 'tech_spec_link', 'g', 'url')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(47, 'width', 'g', 'intUnit')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(48, 'wireless_interface', 'g', 'string')";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_attributes VALUES(49, 'zoom', 'g', 'string')";

		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(1, 9, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(1, 11, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(1, 25, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(1, 26, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(1, 44, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(1, 45, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(2, 5, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(2, 7, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(2, 14, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(2, 19, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(2, 32, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(2, 38, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(3, 9, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(3, 18, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(3, 21, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(3, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(3, 46, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(3, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(3, 48, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(4, 6, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(4, 8, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(4, 9, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(4, 21, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(4, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(4, 30, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(4, 31, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(4, 37, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(4, 40, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(4, 42, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(4, 46, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(4, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(5, 9, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(5, 16, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(5, 21, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(5, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(5, 27, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(5, 41, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(5, 46, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(5, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(5, 49, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(6, 4, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(6, 9, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(6, 13, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(6, 21, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(6, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(6, 41, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(6, 42, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(6, 46, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(6, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(7, 8, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(7, 9, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(7, 18, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(7, 21, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(7, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(7, 46, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(7, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(8, 9, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(8, 10, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(8, 18, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(8, 21, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(8, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(8, 28, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(8, 46, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(8, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(9, 4, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(9, 9, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(9, 13, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(9, 21, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(9, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(9, 41, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(9, 42, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(9, 46, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(9, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(10, 9, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(10, 21, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(10, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(10, 42, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(10, 46, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(10, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(10, 49, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(11, 8, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(11, 9, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(11, 21, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(11, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(11, 24, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(11, 46, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(11, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(12, 9, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(12, 21, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(12, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(12, 46, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(12, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(13, 3, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(13, 11, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(13, 15, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(13, 21, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(13, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(13, 25, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(13, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(14, 8, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(14, 21, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(14, 22, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(14, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(14, 25, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(14, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(15, 15, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(15, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(15, 25, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(15, 26, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(15, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(16, 11, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(16, 21, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(16, 23, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(16, 25, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(16, 47, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(17, 3, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(17, 11, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(17, 26, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(17, 29, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(17, 45, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(18, 3, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(18, 14, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(18, 17, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(18, 19, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(19, 1, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(19, 12, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(19, 17, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(19, 19, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(19, 39, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(19, 49, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(20, 9, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(20, 11, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(20, 20, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(20, 25, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(20, 26, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(20, 43, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(20, 44, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(20, 45, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(21, 2, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(21, 25, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(22, 19, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(22, 36, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "google_base_types_attributes VALUES(22, 39, 1)";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.12");
	}

	if (comp_vers("3.3.13", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "newsletters_emails ADD COLUMN is_sent TINYINT(1) DEFAULT 0",
			"postgre" => "ALTER TABLE " . $table_prefix . "newsletters_emails ADD COLUMN is_sent SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "newsletters_emails ADD COLUMN is_sent BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "newsletters_emails ADD COLUMN is_sent SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "newsletters_emails ADD COLUMN is_custom TINYINT(1) DEFAULT 0",
			"postgre" => "ALTER TABLE " . $table_prefix . "newsletters_emails ADD COLUMN is_custom SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "newsletters_emails ADD COLUMN is_custom BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "newsletters_emails ADD COLUMN is_custom SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.13");
	}

	if (comp_vers("3.3.14", $current_db_version) == 1)
	{
		$sql  = " UPDATE " . $table_prefix . "order_statuses SET stock_level_action=1 ";
		$sqls[] = $sql;

		$sql  = " UPDATE " . $table_prefix . "order_statuses SET stock_level_action=-1, points_action=-1 ";
		$sql .= " WHERE status_type='CANCELLED' OR status_type='REFUNDED' OR status_type='VOIDED' ";
		$sqls[] = $sql;

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.14");
	}

	if (comp_vers("3.3.15", $current_db_version) == 1)
	{
		// add layout for default custom page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'custom_page', 'custom_page_body', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'custom_page', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'custom_page', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'custom_page', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'custom_page', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'custom_page', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'custom_page', 'right_column_width', NULL, NULL)";

		// add layout for wishlist page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'wishlist', 'wishlist_search', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'wishlist', 'wishlist_items', 1, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'wishlist', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'wishlist', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'wishlist', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'wishlist', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'wishlist', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'wishlist', 'right_column_width', NULL, NULL)";


		$mysql_sql  = "CREATE TABLE " . $table_prefix . "forum_priorities (";
		$mysql_sql .= "  `priority_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `priority_name` VARCHAR(255),";
		$mysql_sql .= "  `priority_rank` INT(11) default '0',";
		$mysql_sql .= "  `html_before_title` TEXT,";
		$mysql_sql .= "  `html_after_title` TEXT,";
		$mysql_sql .= "  `is_default` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (priority_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "forum_priorities START 3";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "forum_priorities (";
		$postgre_sql .= "  priority_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "forum_priorities'),";
		$postgre_sql .= "  priority_name VARCHAR(255),";
		$postgre_sql .= "  priority_rank INT4 default '0',";
		$postgre_sql .= "  html_before_title TEXT,";
		$postgre_sql .= "  html_after_title TEXT,";
		$postgre_sql .= "  is_default SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (priority_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "forum_priorities (";
		$access_sql .= "  [priority_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [priority_name] VARCHAR(255),";
		$access_sql .= "  [priority_rank] INTEGER,";
		$access_sql .= "  [html_before_title] LONGTEXT,";
		$access_sql .= "  [html_after_title] LONGTEXT,";
		$access_sql .= "  [is_default] BYTE";
		$access_sql .= "  ,PRIMARY KEY (priority_id))";


		$db2_sql  = "CREATE TABLE " . $table_prefix . "forum_priorities (";
		$db2_sql .= "  priority_id INTEGER NOT NULL,";
		$db2_sql .= "  priority_name VARCHAR(255),";
		$db2_sql .= "  priority_rank INTEGER default 0,";
		$db2_sql .= "  html_before_title LONG VARCHAR,";
		$db2_sql .= "  html_after_title LONG VARCHAR,";
		$db2_sql .= "  is_default SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (priority_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "forum_priorities AS INTEGER START WITH 3 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "forum_pr_41 NO CASCADE BEFORE INSERT ON " . $table_prefix . "forum_priorities REFERENCING NEW AS newr_" . $table_prefix . "forum_priorities FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "forum_priorities.priority_id IS NULL ) begin atomic set newr_" . $table_prefix . "forum_priorities.priority_id = nextval for seq_" . $table_prefix . "forum_priorities; end";
		}

		$sqls[] = "INSERT INTO " . $table_prefix . "forum_priorities (priority_id,priority_name,priority_rank,html_before_title,html_after_title,is_default) VALUES (1, 'Normal', 3, NULL, NULL, 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "forum_priorities (priority_id,priority_name,priority_rank,html_before_title,html_after_title,is_default) VALUES (2, 'Important' , 1, '<b>Important:</b> ', NULL, 0)";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN priority_id INT(11) DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN priority_id INT4 DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN priority_id INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN priority_id INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "forum SET priority_id=1 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "forum_priority_id ON " . $table_prefix . "forum (priority_id)";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN usage_type TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN usage_type SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN usage_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN usage_type SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items_properties SET usage_type=1 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "items_properties_usage_type ON " . $table_prefix . "items_properties (usage_type) ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "items_properties_assigned (";
		$mysql_sql .= "  `item_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `property_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (item_id,property_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "items_properties_assigned (";
		$postgre_sql .= "  item_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  property_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (item_id,property_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "items_properties_assigned (";
		$access_sql .= "  [item_id] INTEGER NOT NULL,";
		$access_sql .= "  [property_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (item_id,property_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "items_properties_assigned (";
		$db2_sql .= "  item_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  property_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (item_id,property_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "items_values_assigned (";
		$mysql_sql .= "  `item_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `property_value_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (item_id,property_value_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "items_values_assigned (";
		$postgre_sql .= "  item_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  property_value_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (item_id,property_value_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "items_values_assigned (";
		$access_sql .= "  [item_id] INTEGER NOT NULL,";
		$access_sql .= "  [property_value_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (item_id,property_value_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "items_values_assigned (";
		$db2_sql .= "  item_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  property_value_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (item_id,property_value_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.15");
	}

	if (comp_vers("3.3.16", $current_db_version) == 1)
	{
		// add layout for user login page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_login', 'advanced_login', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_login', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_login', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_login', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_login', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_login', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_login', 'right_column_width', NULL, NULL)";

		// add layout for user profile page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_profile', 'userhome_breadcrumb', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_profile', 'user_profile_form', 1, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_profile', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_profile', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_profile', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_profile', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_profile', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'user_profile', 'right_column_width', NULL, NULL)";

		// add layout for forgot password page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'forgot_password', 'forgot_password', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'forgot_password', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'forgot_password', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'forgot_password', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'forgot_password', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'forgot_password', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'forgot_password', 'right_column_width', NULL, NULL)";

		// add layout for reset password page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'reset_password', 'reset_password', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'reset_password', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'reset_password', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'reset_password', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'reset_password', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'reset_password', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'reset_password', 'right_column_width', NULL, NULL)";

		// add layout for support reply page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'support_reply', 'support_reply', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'support_reply', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'support_reply', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'support_reply', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'support_reply', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'support_reply', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'support_reply', 'right_column_width', NULL, NULL)";

		// add layout for advanced search page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_advanced_search', 'products_advanced_search', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_advanced_search', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_advanced_search', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_advanced_search', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_advanced_search', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_advanced_search', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_advanced_search', 'right_column_width', NULL, NULL)";

		// add layout for compare page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_compare', 'products_compare', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_compare', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_compare', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_compare', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_compare', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_compare', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_compare', 'right_column_width', NULL, NULL)";

		// add layout for releases page
		$sqls[]  = " UPDATE " . $table_prefix . "page_settings SET setting_name='products_releases_hot' WHERE setting_name='products_releases' ";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_releases', 'products_releases', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_releases', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_releases', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_releases', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_releases', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_releases', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_releases', 'right_column_width', NULL, NULL)";

		// add layout for changes log page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_changes_log', 'products_changes_log', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_changes_log', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_changes_log', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_changes_log', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_changes_log', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_changes_log', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_changes_log', 'right_column_width', NULL, NULL)";

		// add layout for cart save page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_save', 'cart_save', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_save', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_save', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_save', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_save', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_save', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_save', 'right_column_width', NULL, NULL)";

		// add layout for cart retrieve page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_retrieve', 'cart_retrieve', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_retrieve', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_retrieve', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_retrieve', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_retrieve', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_retrieve', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'cart_retrieve', 'right_column_width', NULL, NULL)";

		// add layout for user home pages
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'userhome_pages', 'userhome_breadcrumb', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'userhome_pages', 'userhome_main_block', 1, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'userhome_pages', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'userhome_pages', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'userhome_pages', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'userhome_pages', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'userhome_pages', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'userhome_pages', 'right_column_width', NULL, NULL)";

		// add layout for checkout login page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'checkout_login', 'checkout_login', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'checkout_login', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'checkout_login', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'checkout_login', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'checkout_login', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'checkout_login', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'checkout_login', 'right_column_width', NULL, NULL)";

		// add layout for checkout order data page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_info', 'checkout_breadcrumb', 0, 'middle')";
		// show currency block on first checkout page
		$sql  = "SELECT setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= "WHERE setting_type='order_info' AND setting_name='currency_block' ";
		$currency_block = get_db_value($sql);
		if ($currency_block) {
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_info', 'currency_block', 1, 'middle')";
		}
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_info', 'order_data_form', 2, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_info', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_info', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_info', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_info', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_info', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_info', 'right_column_width', NULL, NULL)";

		// add layout for checkout payment details page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_payment_details', 'checkout_breadcrumb', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_payment_details', 'order_cart', 1, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_payment_details', 'order_payment_details_form', 2, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_payment_details', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_payment_details', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_payment_details', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_payment_details', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_payment_details', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_payment_details', 'right_column_width', NULL, NULL)";

		// add layout for checkout confirmation page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_confirmation', 'checkout_breadcrumb', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_confirmation', 'order_cart', 1, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_confirmation', 'order_data_preview', 2, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_confirmation', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_confirmation', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_confirmation', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_confirmation', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_confirmation', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_confirmation', 'right_column_width', NULL, NULL)";

		// add layout for checkout final page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_final', 'checkout_final', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_final', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_final', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_final', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_final', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_final', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'order_final', 'right_column_width', NULL, NULL)";

		// add layout for ads compare page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_compare', 'ads_compare', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_compare', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_compare', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_compare', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_compare', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_compare', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_compare', 'right_column_width', NULL, NULL)";

		// add layout for ads advanced search page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_search', 'ads_search_advanced', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_search', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_search', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_search', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_search', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_search', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'ads_search', 'right_column_width', NULL, NULL)";

		// add layout for previous polls page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'polls_previous', 'polls_previous_list', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'polls_previous', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'polls_previous', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'polls_previous', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'polls_previous', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'polls_previous', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'polls_previous', 'right_column_width', NULL, NULL)";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN parent_property_id INT(11)",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN parent_property_id INT4",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN parent_property_id INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN parent_property_id INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN parent_value_id INT(11)",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN parent_value_id INT4",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN parent_value_id INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN parent_value_id INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN shipping_trade_cost DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN shipping_trade_cost FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN shipping_trade_cost FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN shipping_trade_cost DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "filters_properties ADD COLUMN list_group_fields TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "filters_properties ADD COLUMN list_group_fields TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "filters_properties ADD COLUMN list_group_fields LONGTEXT",
			"db2"     => "ALTER TABLE " . $table_prefix . "filters_properties ADD COLUMN list_group_fields LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "filters_properties ADD COLUMN list_group_where TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "filters_properties ADD COLUMN list_group_where TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "filters_properties ADD COLUMN list_group_where LONGTEXT",
			"db2"     => "ALTER TABLE " . $table_prefix . "filters_properties ADD COLUMN list_group_where LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.16");
	}

	if (comp_vers("3.3.17", $current_db_version) == 1)
	{
		$sqls[] = " ALTER TABLE ". $table_prefix . "items_properties DROP COLUMN free_price_length ";
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN free_price_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN free_price_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN free_price_amount FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN free_price_amount DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.17");
	}

	if (comp_vers("3.3.18", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN manufacturer_order INT(11) NOT NULL DEFAULT 1 ",
			"postgre" => "ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN manufacturer_order INT4 NOT NULL DEFAULT 1 ",
			"access"  => "ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN manufacturer_order INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN manufacturer_order INTEGER NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3.18");
	}


	if (comp_vers("3.4", $current_db_version) == 1)
	{
		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4");
	}

?>