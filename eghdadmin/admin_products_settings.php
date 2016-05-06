<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_products_settings.php                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once($root_folder_path . "messages/" . $language_code . "/download_messages.php");
	include_once("./admin_common.php");

	check_admin_security("products_settings");

	// additional connection
	$dbs = new VA_SQL();
	$dbs->DBType      = $db_type;
	$dbs->DBDatabase  = $db_name;
	$dbs->DBUser      = $db_user;
	$dbs->DBPassword  = $db_password;
	$dbs->DBHost      = $db_host;
	$dbs->DBPort      = $db_port;
	$dbs->DBPersistent= $db_persistent;

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_products_settings.html");

	include_once("./admin_header.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_products_settings_href", "admin_products_settings.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");
	$t->set_var("admin_tax_rates_href", "admin_tax_rates.php");
	$t->set_var("hide_add_message", str_replace("{button_name}", ADD_TO_CART_MSG, HIDE_BUTTON_MSG));
	$t->set_var("hide_view_message", str_replace("{button_name}", VIEW_CART_MSG, HIDE_BUTTON_MSG));
	$t->set_var("hide_goto_message", str_replace("{button_name}", GOTO_CHECKOUT_MSG, HIDE_BUTTON_MSG));
	$t->set_var("hide_wish_message", str_replace("{button_name}", ADD_TO_WISHLIST_MSG, HIDE_BUTTON_MSG));

	$t->set_var("date_edit_format", join("", $date_edit_format));

	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$r = new VA_Record($table_prefix . "global_settings");

	// load data to listbox
	$countries = get_db_values("SELECT country_id,country_name FROM " . $table_prefix . "countries ORDER BY country_order ", array(array("", "")));
	$admin_templates_dir_values = get_db_values("SELECT layout_id,layout_name FROM " . $table_prefix . "layouts", "");

	$records_per_page =
		array(
			array(5, 5), array(10, 10), array(15, 15),
			array(20, 20), array(25, 25), array(50, 50),
			array(75, 75), array(100, 100)
			);

	$product_controls =
		array(
			array("NONE",    NONE_MSG),
			array("LABEL",   LABEL_MSG),
			array("LISTBOX", LISTBOX_MSG),
			array("TEXTBOX", TEXTBOX_MSG)
			);

	$controls =
		array(
			array("NONE",  NONE_MSG),
			array("LISTBOX", LISTBOX_MSG),
			array("TEXTBOX", TEXTBOX_MSG)
			);

	$yes_no =
		array(
			array(1, YES_MSG), array(0, NO_MSG)
			);

	$confirm_add =
		array(
			array(0, ADD_TO_CART_WITHOUT_CONFIRM_MSG),
			array(1, ADD_TO_CART_SHOW_JS_CONFIRM_MSG)
			);

	$basket_actions =
		array(
			array(0, REMAIN_ON_THE_SAME_PAGE_MSG),
			array(1, GOTO_BASKET_PAGE_MSG),
			array(2, GOTO_CHECKOUT_PAGE_MSG)
			);

	$user_registration =
		array(
			array(0, USER_CAN_BUY_WITHOUT_REGISTRATION_MSG),
			array(1, USER_MUST_HAVE_ACCOUNT_TO_BUY_MSG)
			);

	$subscription_page =
		array(
			array(0, SUBSCRIPTION_WITHOUT_REGISTRATION_MSG),
			array(1, SUBSCRIPTION_REQUIRE_REGISTRATION_MSG)
			);

	$display_products =
		array(
			array(0, FOR_ALL_USERS_MSG),
			array(1, ONLY_FOR_LOGGED_IN_USERS_MSG),
			array(2, WITHOUT_PRICES_FOR_NON_LOGGED_MSG)
			);

	$show_currency =
		array(
			array(0, USE_ACTIVE_CURRENCY_MSG),
			array(1, USE_ORDER_CURRENCY_MSG)
			);

	$new_product_ranges =
		array(			
			array(0, LAST_7DAYS_MSG),
			array(1, LAST_MONTH_MSG),
			array(2, LAST_PAGE_MSG . " X " . DAYS_MSG),			
			array(3, FROM_DATE_MSG)
		);
		
	$new_product_orders =
		array(			
			array(0, PROD_ISSUE_DATE_MSG),
			array(1, DATE_ADDED_MSG),
			array(2, DATE_MSG . " " .ADMIN_MODIFIED_MSG)
		);
		
	$tax_prices_types =
		array(
			array(0, PRICE_EXCL_TAX_MSG),
			array(1, PRICE_INCL_TAX_MSG)
			);

	$tax_types =
		array(
			array(0, PRICE_EXCL_TAX_MSG),
			array(1, PRICE_EXCL_INCL_TAX_MSG),
			array(2, PRICE_INCL_EXCL_TAX_MSG),
			array(3, PRICE_INCL_TAX_MSG)
			);

	$commission_types = array(
		array("", ""), array(0, NOT_AVAILABLE_MSG), array(1, PERCENT_PER_PROD_FULL_PRICE_MSG),
		array(2, FIXED_AMOUNT_PER_PROD_MSG), array(3, PERCENT_PER_PROD_SELL_PRICE_MSG),
		array(4, PERCENT_PER_PROD_SELL_BUY_MSG)
	);

	$active_values = array(
		array(1, ACTIVE_MSG), array(0, INACTIVE_MSG), 
	);

	$points_price_types = array(
		array("", ""), array(0, POINTS_NOT_ALLOWED_MSG), array(1, POINTS_ALLOWED_MSG), 
	);

	$zero_price_types = array(
		array(0, SHOW_ZERO_PRICE_MSG), 
		array(1, HIDE_ZERO_PRICE_MSG), 
		array(2, SHOW_ZERO_PRICE_MESSAGE_MSG), 
	);
	
	$zero_product_actions = array(
		array(1, ALLOW_ADD_ZERO_PRODUCTS_MSG), 
		array(2, SHOW_WARNING_FOR_ZERO_PRODUCTS_MSG), 
	);
	
	$components_list_styles = array(
		array(1, AS_LIST_MSG), 
		array(2, AS_TABLE_MSG), 
	);
	
	$show_reward_credits = array(
		array(0, FOR_ALL_USERS_MSG),
		array(1, ONLY_FOR_LOGGED_IN_USERS_MSG),
	);


	$open_large_image = array(
		array(0, IN_POPUP_WINDOW_MSG),
		array(1, IN_ACTIVE_WINDOW_MSG)
	);
	$watermark_positions = array(
		array("", ""),
		array("TL", TOP_LEFT_MSG),
		array("TC", TOP_CENTER_MSG),
		array("TR", TOP_RIGHT_MSG),
		array("ML", MIDDLE_LEFT_MSG),
		array("C",  CENTER_OF_IMAGE_MSG),
		array("MR", MIDDLE_RIGHT_MSG),
		array("BL", BOTTOM_LEFT_MSG),
		array("BC", BOTTOM_CENTER_MSG),
		array("BR", BOTTOM_RIGHT_MSG),
		array("RND", RANDOM_POSITION_MSG),
	);

	$google_base_export_types =
		array(
			array(0, MANUALLY_DOWNLOAD_XML_FILE_MSG),
			array(1, USE_FTP_TO_UPLOAD_TO_GOOGLE_MSG)
		);

	$prod_image_types =
		array(
			array(0, DONT_SHOW_IMAGE_MSG),
			array(1, IMAGE_TINY_MSG),
			array(2, IMAGE_SMALL_MSG),
			array(3, IMAGE_LARGE_MSG)
		);
	// set up parameters
	$r->add_select("quantity_control_list", TEXT, $product_controls);
	$r->add_select("quantity_control_details", TEXT, $product_controls);
	$r->add_select("quantity_control_basket", TEXT, $controls);
	$r->add_radio("confirm_add", TEXT, $confirm_add);
	$r->add_radio("redirect_to_cart", TEXT, $basket_actions);
	$r->add_checkbox("coupons_enable", INTEGER);
	$r->add_select("user_registration", TEXT, $user_registration);
	$r->add_select("subscription_page", TEXT, $subscription_page);
	$r->add_select("display_products", TEXT, $display_products);
	$r->add_checkbox("logout_cart_clear", INTEGER);
	$r->add_radio("orders_currency", TEXT, $show_currency);

	// run php code
	$r->add_checkbox("php_in_products_short_desc", INTEGER);
	$r->add_checkbox("php_in_products_full_desc", INTEGER);
	$r->add_checkbox("php_in_products_features", INTEGER);
	$r->add_checkbox("php_in_products_hot_desc", INTEGER);
	$r->add_checkbox("php_in_products_notes", INTEGER);
	$r->add_checkbox("php_in_products_download_terms", INTEGER);

	//New Product Functionality
	$r->add_checkbox("new_product_enable", INTEGER);
	$r->add_select("new_product_order", INTEGER, $new_product_orders);
	$r->add_select("new_product_range", INTEGER, $new_product_ranges);
	$r->add_textbox("new_product_from_date", TEXT);
	$r->change_property("new_product_from_date", VALUE_MASK, $date_edit_format);
	$r->add_textbox("new_product_x_days", INTEGER);
	
	// Tax
	$r->add_select("tax_prices_type", TEXT, $tax_prices_types);
	$r->add_select("tax_prices", TEXT, $tax_types);
	$r->add_textbox("tax_note", TEXT);
	$r->add_textbox("tax_note_excl", TEXT);

	// commissions
	$r->add_select("merchant_fee_type", INTEGER, $commission_types);
	$r->add_textbox("merchant_fee_amount", NUMBER, MERCHANT_FEE_AMOUNT_MSG);
	$r->add_select("affiliate_commission_type", INTEGER, $commission_types);
	$r->add_textbox("affiliate_commission_amount", NUMBER, AFFILIATE_COMMISSION_AMOUNT_MSG);
	$r->add_checkbox("affiliate_commission_deduct", NUMBER);
	$r->add_textbox("affiliate_cookie_expire", NUMBER, AFFILIATE_COOKIE_EXPIRES_MSG);
	$r->add_textbox("min_payment_amount", NUMBER, MINIMUM_PAYMENT_AMOUNT_MSG);
	$r->add_checkbox("tell_friend_param", NUMBER, TELL_FRIEND_PARAM_MSG);
	$r->add_textbox("friend_cookie_expire", NUMBER, FRIEND_COOKIE_EXPIRES_MSG);

	// Appearance
	$r->add_radio("zero_price_type", INTEGER, $zero_price_types);
	$r->add_textbox("zero_price_message", TEXT);
	$r->add_radio("zero_product_action", INTEGER, $zero_product_actions);
	$r->add_textbox("zero_product_warn", TEXT);
	
	$r->add_radio("components_list_style", INTEGER, $components_list_styles);

	$r->add_checkbox("price_matrix_list", INTEGER);
	$r->add_checkbox("price_matrix_details", INTEGER);

	$r->add_checkbox("item_code_list", INTEGER);
	$r->add_checkbox("item_code_details", INTEGER);
	$r->add_checkbox("item_code_basket", INTEGER);
	$r->add_checkbox("item_code_checkout", INTEGER);
	$r->add_checkbox("item_code_invoice", INTEGER);
	$r->add_checkbox("item_code_reports", INTEGER);

	$r->add_checkbox("manufacturer_code_list", INTEGER);
	$r->add_checkbox("manufacturer_code_details", INTEGER);
	$r->add_checkbox("manufacturer_code_basket", INTEGER);
	$r->add_checkbox("manufacturer_code_checkout", INTEGER);
	$r->add_checkbox("manufacturer_code_invoice", INTEGER);
	$r->add_checkbox("manufacturer_code_reports", INTEGER);

	$r->add_checkbox("stock_level_list", INTEGER);
	$r->add_checkbox("stock_level_details", INTEGER);

	$r->add_checkbox("hide_add_list", INTEGER);
	$r->add_checkbox("hide_add_details", INTEGER);
	$r->add_checkbox("hide_view_list", INTEGER);
	$r->add_checkbox("hide_view_details", INTEGER);
	$r->add_checkbox("hide_checkout_list", INTEGER);
	$r->add_checkbox("hide_checkout_details", INTEGER);
	$r->add_checkbox("hide_wishlist_list", INTEGER);
	$r->add_checkbox("hide_wishlist_details", INTEGER);
	$r->add_checkbox("hide_weight_details", INTEGER);

	// options price appearance
	$r->add_textbox("option_positive_price_right", TEXT);
	$r->add_textbox("option_positive_price_left", TEXT);
	$r->add_textbox("option_negative_price_right", TEXT);
	$r->add_textbox("option_negative_price_left", TEXT);

	// rss settings
	$r->add_checkbox("is_rss", INTEGER);

	// columns for basket page
	$r->add_checkbox("basket_item_name", INTEGER);
	$r->add_checkbox("basket_item_price", INTEGER);
	$r->add_checkbox("basket_item_tax_percent", INTEGER);
	$r->add_checkbox("basket_item_tax", INTEGER);
	$r->add_checkbox("basket_item_price_incl_tax", INTEGER);
	$r->add_checkbox("basket_item_quantity", INTEGER);
	$r->add_checkbox("basket_item_price_total", INTEGER);
	$r->add_checkbox("basket_item_tax_total", INTEGER);
	$r->add_checkbox("basket_item_price_incl_tax_total", INTEGER);

	// columns for basket page
	$r->add_checkbox("checkout_item_name", INTEGER);
	$r->add_checkbox("checkout_item_price", INTEGER);
	$r->add_checkbox("checkout_item_tax_percent", INTEGER);
	$r->add_checkbox("checkout_item_tax", INTEGER);
	$r->add_checkbox("checkout_item_price_incl_tax", INTEGER);
	$r->add_checkbox("checkout_item_quantity", INTEGER);
	$r->add_checkbox("checkout_item_price_total", INTEGER);
	$r->add_checkbox("checkout_item_tax_total", INTEGER);
	$r->add_checkbox("checkout_item_price_incl_tax_total", INTEGER);
	$r->add_select("checkout_item_image", INTEGER, $prod_image_types);

	// columns for invoice page
	$r->add_checkbox("invoice_item_name", INTEGER);
	$r->add_checkbox("invoice_item_price", INTEGER);
	$r->add_checkbox("invoice_item_tax_percent", INTEGER);
	$r->add_checkbox("invoice_item_tax", INTEGER);
	$r->add_checkbox("invoice_item_price_incl_tax", INTEGER);
	$r->add_checkbox("invoice_item_quantity", INTEGER);
	$r->add_checkbox("invoice_item_price_total", INTEGER);
	$r->add_checkbox("invoice_item_tax_total", INTEGER);
	$r->add_checkbox("invoice_item_price_incl_tax_total", INTEGER);
	$r->add_select("invoice_item_image", INTEGER, $prod_image_types);

	// points
	$r->add_radio("points_system", INTEGER, $active_values);
	$r->add_textbox("points_conversion_rate", NUMBER, POINTS_CONVERSION_RATE_MSG);
	$r->add_textbox("points_decimals", INTEGER, POINTS_DECIMALS_MSG);
	$r->add_checkbox("points_price_list", INTEGER);
	$r->add_checkbox("points_price_details", INTEGER);
	$r->add_checkbox("points_price_basket", INTEGER);
	$r->add_checkbox("points_price_checkout", INTEGER);
	$r->add_checkbox("points_price_invoice", INTEGER);
	$r->add_select("points_prices", INTEGER, $points_price_types);
	$r->add_select("points_shipping", INTEGER, $points_price_types);
	$r->add_select("points_orders_options", INTEGER, $points_price_types);
	$r->add_select("reward_type", INTEGER, $commission_types, REWARD_POINTS_TYPE_MSG);
	$r->add_textbox("reward_amount", NUMBER, REWARD_POINTS_AMOUNT_MSG);
	$r->add_checkbox("reward_points_list", INTEGER);
	$r->add_checkbox("reward_points_details", INTEGER);
	$r->add_checkbox("reward_points_basket", INTEGER);
	$r->add_checkbox("reward_points_checkout", INTEGER);
	$r->add_checkbox("reward_points_invoice", INTEGER);
	$r->add_select("credit_reward_type", INTEGER, $commission_types, REWARD_CREDITS_TYPE_MSG);
	$r->add_textbox("credit_reward_amount", NUMBER, REWARD_CREDITS_AMOUNT_MSG);
	$r->add_checkbox("credits_balance_user_home", INTEGER);
	$r->add_checkbox("credits_balance_order_profile", INTEGER);
	$r->add_radio("reward_credits_users", INTEGER, $show_reward_credits);
	$r->add_checkbox("reward_credits_list", INTEGER);
	$r->add_checkbox("reward_credits_details", INTEGER);
	$r->add_checkbox("reward_credits_basket", INTEGER);
	$r->add_checkbox("reward_credits_checkout", INTEGER);
	$r->add_checkbox("reward_credits_invoice", INTEGER);
	$r->add_checkbox("points_for_points", INTEGER);
	$r->add_checkbox("credits_for_points", INTEGER);

	// credit system
	$r->add_radio("credit_system", INTEGER, $active_values);

	// Image settings
	$r->add_textbox("product_no_image_large", TEXT);
	$r->add_textbox("product_no_image", TEXT);
	$r->add_textbox("product_no_image_tiny", TEXT);
	$r->add_radio("open_large_image", TEXT, $open_large_image);
	$r->add_textbox("jpeg_quality", NUMBER);
	$r->change_property("jpeg_quality", MIN_VALUE, 0);
	$r->change_property("jpeg_quality", MAX_VALUE, 100);

	$r->add_checkbox("resize_tiny_image", INTEGER);
	$r->add_checkbox("resize_small_image", INTEGER);
	$r->add_checkbox("resize_big_image", INTEGER);
	$r->add_checkbox("resize_super_image", INTEGER);
	$r->add_checkbox("show_preview_image", INTEGER);
	$r->add_textbox("tiny_image_max_width", INTEGER);
	$r->add_textbox("tiny_image_max_height", INTEGER);
	$r->add_textbox("small_image_max_width", INTEGER);
	$r->add_textbox("small_image_max_height", INTEGER);
	$r->add_textbox("big_image_max_width", INTEGER);
	$r->add_textbox("big_image_max_height", INTEGER);
	$r->add_textbox("super_image_max_width", INTEGER);
	$r->add_textbox("super_image_max_height", INTEGER);

	// customer images restrictions
	$r->add_textbox("user_tiny_image_width", INTEGER);
	$r->add_textbox("user_tiny_image_height", INTEGER);
	$r->add_textbox("user_tiny_image_size", INTEGER);
	$r->add_checkbox("user_resize_tiny_image", INTEGER);
	$r->add_checkbox("user_generate_tiny_image", INTEGER);
	$r->add_textbox("user_small_image_width", INTEGER);
	$r->add_textbox("user_small_image_height", INTEGER);
	$r->add_textbox("user_small_image_size", INTEGER);
	$r->add_checkbox("user_resize_small_image", INTEGER);
	$r->add_checkbox("user_generate_small_image", INTEGER);
	$r->add_textbox("user_large_image_width", INTEGER);
	$r->add_textbox("user_large_image_height", INTEGER);
	$r->add_textbox("user_large_image_size", INTEGER);
	$r->add_checkbox("user_resize_large_image", INTEGER);
	$r->add_checkbox("user_generate_large_image", INTEGER);
	$r->add_textbox("user_super_image_width", INTEGER);
	$r->add_textbox("user_super_image_height", INTEGER);
	$r->add_textbox("user_super_image_size", INTEGER);
	$r->add_checkbox("user_resize_super_image", INTEGER);
	$r->add_checkbox("user_generate_super_image", INTEGER);

	// watermark settings
	$r->add_textbox("watermark_image", TEXT);
	$r->add_select("watermark_image_pos", TEXT, $watermark_positions);
	$r->add_textbox("watermark_image_pct", INTEGER, IMAGE_TRANSPARENCY_MSG);
	$r->change_property("watermark_image_pct", MIN_VALUE, 0);
	$r->change_property("watermark_image_pct", MAX_VALUE, 100);
	$r->add_checkbox("watermark_is_transparent", INTEGER);

	$r->add_textbox("watermark_text", TEXT);
	$r->add_select("watermark_text_pos", TEXT, $watermark_positions);
	$r->add_textbox("watermark_text_size", INTEGER);
	$r->add_textbox("watermark_text_color", TEXT);
	$r->add_textbox("watermark_text_angle", INTEGER);
	$r->add_textbox("watermark_text_pct", INTEGER, TEXT_TRANSPARENCY_MSG);
	$r->change_property("watermark_text_pct", MIN_VALUE, 0);
	$r->change_property("watermark_text_pct", MAX_VALUE, 100);

	$r->add_checkbox("watermark_tiny_image", INTEGER);
	$r->add_checkbox("watermark_small_image", INTEGER);
	$r->add_checkbox("watermark_big_image", INTEGER);
	$r->add_checkbox("watermark_super_image", INTEGER);
	
	$r->add_textbox("products_title", TEXT);
	$r->add_textbox("products_keywords", TEXT);
	$r->add_textbox("products_description", TEXT);

	// google base settings
	$r->add_textbox("google_base_ftp_login", TEXT);
	$r->add_textbox("google_base_ftp_password", TEXT);
	$r->add_textbox("google_base_filename", TEXT);
	$r->add_textbox("google_base_title", TEXT);
	$r->add_textbox("google_base_description", TEXT);
	$google_base_encodings = 
		array(
			array("UTF-8", "UTF-8"),
			array("ISO-8859-1", "Latin-1 (ISO-8859-1)")
		);
	$r->add_select("google_base_encoding", TEXT, $google_base_encodings);
	$r->add_select("google_base_export_type", TEXT, $google_base_export_types );
	$r->add_textbox("google_base_save_path", TEXT);
	$r->add_checkbox("google_base_tax", INTEGER);
	$r->add_textbox("google_base_days_expiry", INTEGER);
	
	$google_base_product_conditions = 
		array(
			array("new",  NEW_MSG),
			array("used", USED_MSG),
			array("refurbished",  REFURBISHED_MSG)
		);	
	$google_base_product_types = get_db_values ("SELECT type_id, type_name FROM " . $table_prefix . "google_base_types ORDER BY type_name", array(array(-1, NOT_EXPORTED_MSG)));
	$r->add_select("google_base_product_type_id", INTEGER, $google_base_product_types);
	$r->add_select("google_base_product_condition", TEXT, $google_base_product_conditions);

	// import/export options
	$r->add_checkbox("match_item_code", INTEGER);
	$r->add_checkbox("match_manufacturer_code", INTEGER);
	
	$r->get_form_values();

	$param_site_id = get_session("session_site_id");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	$operation = get_param("operation");
	$return_page = get_param("rp");
	if (!strlen($return_page)) $return_page = "admin.php";
	if (strlen($operation))
	{
		$tab = "general";
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $r->validate();

		if (!strlen($r->errors))
		{
			$sql  = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='products'";
			$sql .= " AND site_id=" . $db->tosql($param_site_id,INTEGER);
			$db->query($sql);
			foreach ($r->parameters as $key => $value)
			{
				$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
				$sql .= "'products', '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
				$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				$db->query($sql);
			}
			set_session("session_settings", "");
			session_unregister("session_settings");

			header("Location: " . $return_page);
			exit;
		}
	}
	else // get products settings
	{
		foreach ($r->parameters as $key => $value)
		{
			$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type='products' AND setting_name='" . $key . "'";
			$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
			$sql .= " ORDER BY site_id DESC ";
			$r->set_value($key, get_db_value($sql));
		}
	}

	$r->set_parameters();
	$t->set_var("rp", htmlspecialchars($return_page));

	// set styles for tabs
	$tabs = array(
		"general" => array("title" => ADMIN_GENERAL_MSG), 
		"tax" => array("title" => TAX_SETTINGS_MSG), 
		"appearance" => array("title" => PROD_APPEARANCE_MSG), 
		"merchants_affiliates" => array("title" => MERCHANTS_AFFILIATES_MSG), 
		"points" => array("title" => POINTS_AND_CREDITS_MSG), 
		"images" => array("title" => IMAGES_MSG),
		"google_base" => array("title" => GOOGLE_BASE_SETTINGS_MSG),
		"import_export" => array("title" => IMPORT_EXPORT_MSG),
	);

	parse_admin_tabs($tabs, $tab, 5);

	// multisites
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ", "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}	

	include_once("./admin_footer.php");
	
	$t->pparse("main");

?>