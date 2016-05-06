<?php
	check_user_security("access_products");
	include_once($root_folder_path . "includes/access_table.php");
	include_once($root_folder_path . "includes/sites_table.php");
	
	$eol = get_eol();
	$html_editor   = get_setting_value($settings, "user_html_editor", 0);
	$friendly_auto = get_setting_value($settings, "friendly_auto", 0);
	$user_type_id  = get_session("session_user_type_id");

	$user_settings = array();
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "user_types_settings ";
	$sql .= " WHERE type_id=" . $db->tosql($user_type_id, INTEGER);
	$db->query($sql);
	while($db->next_record()) {
		$user_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$duplicate_properties   = get_param("duplicate_properties");
	$allowed_add_product    = get_setting_value($user_settings, "add_product", 0);
	$allowed_edit_product   = get_setting_value($user_settings, "edit_product", 0);
	$allowed_delete_product = get_setting_value($user_settings, "delete_product", 0);

	// get product settings
	$setting_type     = "user_product_" . $user_type_id;
	$product_settings = array();
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT);
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while($db->next_record()) {
		$product_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$products_limit = get_setting_value($product_settings, "products_limit", "");
	$categories_number = get_setting_value($product_settings, "categories_number", 1);
	$min_price_limit = get_setting_value($product_settings, "min_price_limit", "");
	$max_price_limit = get_setting_value($product_settings, "max_price_limit", "");
	$show_downloadable_files = get_setting_value($product_settings, "show_downloadable", 0);
	$show_predefined_serials = get_setting_value($product_settings, "show_predefined_serials", 0);
	$features_editor = get_setting_value($product_settings, "features_editor", 0);
	$short_description_editor = get_setting_value($product_settings, "short_description_editor", 0);
	$full_description_editor = get_setting_value($product_settings, "full_description_editor", 0);
	$special_offer_editor = get_setting_value($product_settings, "special_offer_editor", 0);
	$notes_editor = get_setting_value($product_settings, "notes_editor", 0);
	$show_terms = get_setting_value($product_settings, "show_terms", 0);
	$generate_tiny_image = get_setting_value($settings, "user_generate_tiny_image", 0);
	$generate_small_image = get_setting_value($settings, "user_generate_small_image", 0);
	$generate_large_image = get_setting_value($settings, "user_generate_large_image", 0);

	$date_format_msg = str_replace("{date_format}", join("", $date_edit_format), DATE_FORMAT_MSG);

	$approve_values = array(
		array(1, YES_MSG), 
		array(0, NO_MSG)
	);

	$content_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$generate_serial_values = 
		array( 
			array(0, SERIAL_DONT_GENERATE_MSG), array(1, SERIAL_RANDOM_GENERATE_MSG), array(2, SERIAL_FROM_PREDEFINED_MSG)
		);

	$time_periods =
		array(
			array("", ""), array(1, DAY_MSG), array(2, WEEK_MSG), array(3, MONTH_MSG), array(4, YEAR_MSG)
		);

	$download_types = array(
		array("", ""), 
		array(0, INACTIVE_MSG), 
		array(1, ACTIVE_MSG),
		array(2, USE_WITH_OPTIONS_MSG), 
	);

	$preview_types = array(
		array("", ""), 
		array(0, NOT_AVAILABLE_MSG), 
		array(1, PREVIEW_AS_DOWNLOAD_MSG),
		array(2, PREVIEW_USE_PLAYER_MSG), 
	);

	$preview_positions = array(
		array("", ""), 
		array(0, NOT_AVAILABLE_MSG), 
		array(1, PREVIEW_IN_SEPARATE_SECTION_MSG),
		array(2, PREVIEW_BELOW_DETAILS_IMAGE_MSG),
		array(3, PREVIEW_BELOW_LIST_IMAGE_MSG),
	);

	$t->set_file("block_body","block_user_product.html");
	$t->set_var("site_url",        	$settings["site_url"]);
	$t->set_var("user_home_href",  	   get_custom_friendly_url("user_home.php"));
	$t->set_var("user_products_href",  get_custom_friendly_url("user_products.php"));
	$t->set_var("user_product_href",   get_custom_friendly_url("user_product.php"));
	$t->set_var("user_upload_href",    get_custom_friendly_url("user_upload.php"));
	$t->set_var("user_select_href",    get_custom_friendly_url("user_select.php"));
	$t->set_var("user_product_terms_href", get_custom_friendly_url("user_product_terms.php"));

	$t->set_var("currency_sign",    $currency["left"].$currency["right"]);
	$t->set_var("currency_left",    $currency["left"]);
	$t->set_var("currency_right",   $currency["right"]);
	$t->set_var("currency_rate",    $currency["rate"]);
	$t->set_var("features_editor",  $features_editor);
	$t->set_var("short_description_editor", $short_description_editor);
	$t->set_var("full_description_editor",  $full_description_editor);
	$t->set_var("special_offer_editor",  $special_offer_editor);
	$t->set_var("notes_editor",  $notes_editor);
	$t->set_var("date_edit_format", join("", $date_edit_format));
	$t->set_var("date_format_msg",  $date_format_msg);

	$t->set_var("HIDE_ADD_BUTTON_MSG",         str_replace("{ADD_TO_CART_MSG}", ADD_TO_CART_MSG, HIDE_ADD_BUTTON_MSG));
	$t->set_var("DISABLE_OUT_STOCK_DESC",      str_replace("{ADD_TO_CART_MSG}", ADD_TO_CART_MSG, DISABLE_OUT_STOCK_DESC));

	$item_types = get_db_values("SELECT item_type_id,item_type_name FROM " . $table_prefix . "item_types WHERE is_user=1 ", array(array("", "")));
	$states     = get_db_values("SELECT state_id,state_name FROM " . $table_prefix . "states WHERE show_for_user=1 ORDER BY state_name ", array(array("", SELECT_STATE_MSG)));
	$countries  = get_db_values("SELECT country_id,country_name FROM " . $table_prefix . "countries WHERE show_for_user=1 ORDER BY country_order ", array(array("", SELECT_COUNTRY_MSG)));
	
	
	// prepare categories options	
	$categories_ids = VA_Categories::find_all_ids("", ADD_ITEMS_PERM);	
	$categories = array();
	if ($categories_ids) {
		$sql  = " SELECT category_id, category_name, category_order, category_path ";
		$sql .= " FROM " . $table_prefix . "categories ";
		$sql .= " WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")";
		$sql .= " ORDER BY category_path, category_order, category_name ";
		$db->query($sql);
		while ($db->next_record()) {
			$category_id = $db->f("category_id");
			$category_order = $db->f("category_order");
			$category_name = get_translation($db->f("category_name"));
			$category_path = $db->f("category_path");
			$categories[$category_id] = array($category_name, $category_order, $category_path);
		}
	}
	
	// check for subcategories 
	$subcategories_ids = VA_Categories::find_all_ids("c.allowed_post_subcategories=1", ADD_ITEMS_PERM);	
	if ($subcategories_ids) {
		$subcategories = array();
		$sql  = " SELECT category_id, category_path ";
		$sql .= " FROM " . $table_prefix . "categories ";
		$sql .= " WHERE category_id IN (" . $db->tosql($subcategories_ids, INTEGERS_LIST) . ")";
		$db->query($sql);
		while ($db->next_record()) {
			$category_id = $db->f("category_id");
			$category_path = $db->f("category_path");
			$subcategories[] = $category_path . $category_id . ",";
		}
		if (sizeof($subcategories) > 0) {
			for ($sc = 0; $sc < sizeof($subcategories); $sc++) {
				$sql  = " SELECT c.category_id,c.category_name,c.category_order,c.category_path ";
				$sql .= " FROM " . $table_prefix . "categories c ";
				$sql .= " WHERE c.category_path LIKE '" . $db->tosql($subcategories[$sc], TEXT, false) . "%'";
				$sql .= " ORDER BY c.category_path, c.category_order, c.category_name ";
				$db->query($sql);
				while ($db->next_record()) {
					$category_id = $db->f("category_id");
					$category_order = $db->f("category_order");
					$category_name = get_translation($db->f("category_name"));
					$category_path = $db->f("category_path");
					$categories_ids = explode(",", $category_path);
					$categories[$category_id] = array($category_name, $category_order, $category_path);
				}
			}
		}
	}
	
	// redirect
	if (!$categories) {
		header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=3");
		exit;
	}

	// build categories with full paths
	$categories_unsorted = array();	
	foreach ($categories as $category_id => $category_info) {
		$category_name = $category_info[0];
		$category_order = $category_info[1];
		$category_path = $category_info[2];
		if ($category_path != "0,") {
			$categories_ids = explode(",", $category_path);
			$max_index = sizeof($categories_ids) - 2;
			for($i = $max_index; $i > 0; $i--) {
				$parent_id = $categories_ids[$i];
				if(isset($categories[$parent_id])) {
					$parent_name  = $categories[$parent_id][0];
					$parent_order = $categories[$parent_id][1];
				} else {
					$sql  = " SELECT category_name, category_order FROM " . $table_prefix . "categories ";
					$sql .= " WHERE category_id=" . $db->tosql($parent_id, INTEGER);
					$db->query($sql);
					if ($db->next_record()) {
						$parent_name  = get_translation($db->f("category_name"));
						$parent_order = $db->f("category_order");
						$categories[$parent_id] = array($parent_name, $parent_order);
					}
				}
				$category_order = $parent_order. " > " . $category_order;
				$category_name  = $parent_name . " > " . $category_name;
			}
		}
		$categories_unsorted[$category_order] = array($category_id, $category_name);
	}
	ksort($categories_unsorted);

	$categories_options = array();
	$categories_options[] = array("", SELECT_CATEGORY_MSG);
	foreach ($categories_unsorted as $category_order => $category_info) {
		$categories_options[] = array($category_info[0], $category_info[1]);
	}

	$r = new VA_Record($table_prefix . "items");

	// set up html form parameters
	$r->add_where("item_id", INTEGER);

	$r->add_where("user_id", INTEGER);
	$r->change_property("user_id", USE_IN_INSERT, true);

	$r->add_textbox("is_approved", INTEGER);

	// fields
	$r->add_checkbox("is_showing", INTEGER);

	$r->add_select("item_type_id", INTEGER, $item_types, PROD_TYPE_MSG);
	$r->parameters["item_type_id"][REQUIRED] = true;
	$r->add_textbox("item_code", TEXT, PROD_CODE_MSG);
	$r->add_textbox("item_name", TEXT, PROD_NAME_MSG);
	$r->parameters["item_name"][REQUIRED] = true;
	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);
	$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
	$r->change_property("friendly_url", REGEXP_MASK, ALPHANUMERIC_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	$r->add_textbox("item_order", INTEGER, PROD_ORDER_MSG);
	$r->parameters["item_order"][REQUIRED] = true;
	$manufacturers = get_db_values("SELECT manufacturer_id,manufacturer_name FROM " . $table_prefix . "manufacturers", array(array("", "")));
	$r->add_select("manufacturer_id", INTEGER, $manufacturers, PROD_MANUFACTURER_MSG);
	$r->add_textbox("manufacturer_code", TEXT, MANUFACTURER_CODE_MSG);
	$r->add_textbox("weight", NUMBER, WEIGHT_MSG);
	$r->add_textbox("issue_date", DATETIME, PROD_ISSUE_DATE_MSG);
	$r->change_property("issue_date", VALUE_MASK, $date_edit_format);
	$r->add_checkbox("is_compared", INTEGER, PROD_ALLOWED_COMPARISON_MSG);
	$r->add_checkbox("tax_free", INTEGER, PROD_TAX_FREE_MSG);

	$languages = get_db_values("SELECT language_code, language_name FROM " . $table_prefix . "languages", array(array("", "")));
	$r->add_select("language_code", TEXT, $languages);
	$r->change_property("language_code", USE_SQL_NULL, false);

	$r->add_checkbox("is_price_edit", NUMBER, PROD_CHANGE_PRICE_MSG);
	$r->add_textbox("price", NUMBER, PROD_LIST_PRICE_MSG);
	$r->parameters["price"][REQUIRED] = true;
	$r->add_textbox("trade_price", NUMBER, PROD_TRADE_PRICE_MSG);
	$r->add_checkbox("is_sales", INTEGER, PROD_ACTIVATE_DISCOUNT_MSG);
	$r->add_textbox("sales_price", NUMBER, PROD_DISCOUNT_PRICE_MSG);
	$r->add_textbox("trade_sales", NUMBER, PROD_DISCOUNT_TRADE_MSG);
	$r->add_textbox("discount_percent", NUMBER);
	$r->add_textbox("buying_price", NUMBER, PROD_BUYING_PRICE_MSG);
	$r->add_textbox("properties_price", NUMBER, PROD_OPTIONS_PRICE_MSG);
	$r->add_textbox("trade_properties_price", NUMBER, OPTIONS_TRADE_PRICE_MSG);
	if (strlen($min_price_limit)) {
		$r->change_property("price", MIN_VALUE, $min_price_limit);
		$r->change_property("trade_price", MIN_VALUE, $min_price_limit);
		$r->change_property("sales_price", MIN_VALUE, $min_price_limit);
		$r->change_property("trade_sales", MIN_VALUE, $min_price_limit);
		$r->change_property("properties_price", MIN_VALUE, $min_price_limit);
		$r->change_property("trade_properties_price", MIN_VALUE, $min_price_limit);
	}
	if (strlen($max_price_limit)) {
		$r->change_property("price", MAX_VALUE, $max_price_limit);
		$r->change_property("trade_price", MAX_VALUE, $max_price_limit);
		$r->change_property("sales_price", MAX_VALUE, $max_price_limit);
		$r->change_property("trade_sales", MAX_VALUE, $max_price_limit);
		$r->change_property("properties_price", MAX_VALUE, $max_price_limit);
		$r->change_property("trade_properties_price", MAX_VALUE, $max_price_limit);
	}

	// descriptions
	$r->add_textbox("features", TEXT, HIGHLIGHTS_MSG);
	$r->add_textbox("short_description", TEXT, SHORT_DESCRIPTION_MSG);
	$r->add_textbox("full_desc_type", INTEGER);
	$r->add_textbox("full_description", TEXT, FULL_DESCRIPTION_MSG);

	// meta data
	$r->add_textbox("meta_title", TEXT, META_TITLE_MSG);
	$r->add_textbox("meta_keywords", TEXT, META_KEYWORDS_MSG);
	$r->add_textbox("meta_description", TEXT, META_DESCRIPTION_MSG);

	// images
	$r->add_textbox("tiny_image", TEXT, IMAGE_TINY_MSG);
	$r->add_textbox("tiny_image_alt", TEXT, IMAGE_TINY_ALT_MSG);
	$r->add_textbox("small_image", TEXT, IMAGE_SMALL_MSG);
	$r->add_textbox("small_image_alt", TEXT, IMAGE_SMALL_ALT_MSG);
	$r->add_textbox("big_image", TEXT, IMAGE_LARGE_MSG);
	$r->add_textbox("big_image_alt", TEXT, IMAGE_LARGE_ALT_MSG);
	$r->add_textbox("super_image", TEXT, IMAGE_SUPER_MSG);

	// appearance
	$r->add_textbox("template_name", TEXT, CUSTOM_TEMPLATE_MSG);
	$r->add_checkbox("hide_add_list", INTEGER, HIDE_ADD_BUTTON_MSG . " " . ON_PROD_LIST_MSG);
	$r->add_checkbox("hide_add_details", INTEGER, HIDE_ADD_BUTTON_MSG . " " . ON_PROD_DETAILS_MSG);
	$r->add_checkbox("hide_add_table", INTEGER, HIDE_ADD_BUTTON_MSG . " " . "on table view");
	$r->add_checkbox("hide_add_grid", INTEGER, HIDE_ADD_BUTTON_MSG . " " . "on grid view");
	$r->add_textbox("preview_url", TEXT, PROD_PREVIEW_URL_MSG);
	$r->add_textbox("preview_width", INTEGER, PROD_PREVIEW_URL_MSG . " " . WIDTH_MSG);
	$r->add_textbox("preview_height", INTEGER, PROD_PREVIEW_URL_MSG . " " . HEIGHT_MSG);

	// stock
	$r->add_checkbox("use_stock_level", INTEGER, USE_STOCK_MSG);
	$r->add_textbox("stock_level", NUMBER, STOCK_QUANTITY_MSG);
	$r->add_checkbox("hide_out_of_stock", INTEGER, HIDE_OUT_STOCK_MSG);
	$r->change_property("hide_out_of_stock", USE_SQL_NULL, false);
	$r->add_checkbox("disable_out_of_stock", INTEGER, DISABLE_OUT_STOCK_MSG);
	$r->add_textbox("min_quantity", NUMBER, MINIMUM_ITEMS_QTY_MSG);
	$r->add_textbox("max_quantity", NUMBER, MAXIMUM_ITEMS_QTY_MSG);
	$r->add_textbox("quantity_increment", NUMBER, QTY_INCREMENT_MSG);

	// shipping
	$r->add_checkbox("is_shipping_free", INTEGER, FREE_SHIPPING_MSG);
	$r->add_textbox("shipping_cost", NUMBER, SHIPPING_COST_MSG);
	$times = get_db_values("SELECT shipping_time_id,shipping_time_desc FROM " . $table_prefix . "shipping_times", array(array("", "None")), 90);
	$r->add_select("shipping_in_stock", INTEGER, $times, IN_STOCK_AVAILABILITY_MSG);
	$r->add_select("shipping_out_stock", INTEGER, $times, OUT_STOCK_AVAILABILITY_MSG);

	$rules = get_db_values("SELECT shipping_rule_id,shipping_rule_desc FROM " . $table_prefix . "shipping_rules", array(array("", "None")), 90);
	$r->add_select("shipping_rule_id", INTEGER, $rules, SHIPPING_RESTRICTIONS_MSG);

	// terms and conditions
	if ($show_terms) {
		$r->add_checkbox("terms", INTEGER);
		$r->change_property("terms", USE_IN_INSERT, false);
		$r->change_property("terms", USE_IN_UPDATE, false);
		$r->change_property("terms", USE_IN_SELECT, false);
	}

	// downloadable options
	$r->add_checkbox("download_show_terms", INTEGER, DOWNLOAD_SHOW_TERMS_MSG);
	$r->add_textbox("download_terms_text", TEXT, DOWNLOAD_TERMS_MSG);

	// set up html form parameters for downloadable files
	$itf = new VA_Record($table_prefix . "items_files", "files");
	$itf->add_where("file_id", INTEGER);
	$itf->add_hidden("item_id", INTEGER);
	$itf->change_property("item_id", USE_IN_INSERT, true);
	$itf->change_property("item_id", PARSE_NAME, "download_item_id");
	$itf->add_hidden("item_type_id", INTEGER);
	$itf->change_property("item_type_id", USE_IN_INSERT, true);
	$itf->change_property("item_type_id", PARSE_NAME, "download_item_type_id");

	$itf->add_select("download_type", INTEGER, $download_types);
	$itf->add_textbox("download_title", TEXT, DOWNLOAD_TITLE_MSG);
	$itf->change_property("download_title", REQUIRED, true);
	$itf->add_textbox("download_path", TEXT, DOWNLOAD_PATH_MSG);
	$itf->add_select("download_period", INTEGER, $time_periods, DOWNLOAD_PERIOD_MSG);
	$itf->add_textbox("download_interval", INTEGER, DOWNLOAD_INTERVAL_MSG);
	$itf->add_textbox("download_limit", INTEGER, DOWNLOAD_LIMIT_MSG);
	$itf->add_select("preview_type", INTEGER, $preview_types, PREVIEW_TYPE_MSG);
	$itf->add_textbox("preview_title", TEXT, PREVIEW_TITLE_MSG);
	$itf->add_textbox("preview_path", TEXT, PREVIEW_PATH_MSG);
	$itf->add_textbox("preview_image", TEXT, PREVIEW_IMAGE_MSG);
	$itf->add_select("preview_position", INTEGER, $preview_positions, PREVIEW_POSITION_MSG);

	$number_files = get_param("number_files");
	$itf_eg = new VA_EditGrid($itf, "files");
	$itf_eg->get_form_values($number_files);

	// general serial numbers parameters
	$r->add_radio("generate_serial", INTEGER, $generate_serial_values, SERIAL_GENERATE_MSG);
	$r->add_textbox("serial_period", INTEGER, SERIAL_PERIOD_MSG);
	$r->add_textbox("activations_number", INTEGER, ACTIVATION_MAX_NUMBER_MSG);

	// set up html form parameters
	$is = new VA_Record($table_prefix . "items_serials", "serials");
	$is->add_where("serial_id", INTEGER);
	$is->add_hidden("item_id", INTEGER);
	$is->change_property("item_id", USE_IN_INSERT, true);
	$is->change_property("item_id", PARSE_NAME, "serial_item_id");

	$is->add_textbox("serial_number", TEXT, SERIAL_NUMBER_COLUMN);
	$is->parameters["serial_number"][REQUIRED] = true;
	$is->add_checkbox("used", INTEGER);

	$number_serials = get_param("number_serials");
	$is_eg = new VA_EditGrid($is, "serials");
	$is_eg->get_form_values($number_serials);

	// special
	$r->add_checkbox("is_special_offer", INTEGER, PROD_OFFER_ACTIVATE_MSG);
	$r->add_textbox("special_offer", TEXT, SPECIAL_OFFER_MSG);

	// notification fields
	$r->add_checkbox("mail_notify", INTEGER, EMAIL_USER_IF_STATUS_MSG);
	$r->add_textbox("mail_to", TEXT, EMAIL_TO_MSG);
	$r->add_textbox("mail_from", TEXT, EMAIL_FROM_MSG);
	$r->add_textbox("mail_cc", TEXT, EMAIL_CC_MSG);
	$r->add_textbox("mail_bcc", TEXT, EMAIL_BCC_MSG);
	$r->add_textbox("mail_reply_to", TEXT, EMAIL_REPLY_TO_MSG);
	$r->add_textbox("mail_return_path", TEXT, EMAIL_RETURN_PATH_MSG);
	$r->add_textbox("mail_subject", TEXT, EMAIL_SUBJECT_MSG);
	$r->add_radio("mail_type", INTEGER, $content_types, EMAIL_MESSAGE_TYPE_MSG);
	$r->parameters["mail_type"][DEFAULT_VALUE] = 0;
	$r->add_textbox("mail_body", TEXT, EMAIL_MESSAGE_MSG);

	$r->add_checkbox("sms_notify", INTEGER, SMS_USER_IF_STATUS_MSG);
	$r->add_textbox("sms_recipient", TEXT, SMS_RECIPIENT_MSG);
	$r->add_textbox("sms_originator",TEXT, SMS_ORIGINATOR_MSG);
	$r->add_textbox("sms_message",   TEXT, SMS_MESSAGE_MSG);

	// rating / notes
	$r->add_textbox("votes", INTEGER, TOTAL_VOTES_MSG);
	$r->add_textbox("points", INTEGER, TOTAL_POINTS_MSG);
	$r->add_textbox("notes", TEXT, NOTES_MSG);
	$r->add_textbox("buy_link", TEXT, PROD_DIRECT_LINK_MSG);

	// editing information
	$r->add_textbox("admin_id_added_by", INTEGER);
	$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
	$r->change_property("admin_id_added_by", USE_SQL_NULL, false);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->change_property("admin_id_modified_by", USE_SQL_NULL, false);
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_modified", DATETIME);
	// end of fields
	
	$r->add_checkbox("access_level", INTEGER);	
	$r->add_checkbox("guest_access_level", INTEGER);	
	$r->add_checkbox("sites_all", INTEGER);
		
	$common_fields = array("item_id", "user_id", "is_approved", "item_type_id", "item_name", "full_desc_type", "admin_id_added_by", "admin_id_modified_by", "date_added", "date_modified", "access_level", "guest_access_level", "sites_all");
	foreach($r->parameters as $parameter_name => $parameter_info) {
		if (!in_array($parameter_name, $common_fields)) {
			$show_parameter = get_setting_value($product_settings, "show_" . $parameter_name, 0);
			if ($show_parameter) {
				$parameter_required = get_setting_value($product_settings, $parameter_name . "_required", 0);
				$r->change_property($parameter_name, REQUIRED, $parameter_required);
			} else {
				$r->change_property($parameter_name, SHOW, false);
				$r->change_property($parameter_name, USE_IN_INSERT, false);
				$r->change_property($parameter_name, USE_IN_UPDATE, false);
			}
		}
	}

	if (!$r->parameters["tiny_image"][SHOW] && $generate_tiny_image) {
		$r->add_textbox("tiny_image_hidden", TEXT);
		$r->change_property("tiny_image_hidden", COLUMN_NAME, "tiny_image");

	}
	if (!$r->parameters["small_image"][SHOW] && $generate_small_image) {
		$r->add_textbox("small_image_hidden", TEXT);
		$r->change_property("small_image_hidden", COLUMN_NAME, "small_image");

	}
	if (!$r->parameters["big_image"][SHOW] && $generate_large_image) {
		$r->add_textbox("big_image_hidden", TEXT);
		$r->change_property("big_image_hidden", COLUMN_NAME, "big_image");
	}

	$generate_tiny_image  = get_setting_value($settings, "user_generate_tiny_image", 0);
	$generate_small_image = get_setting_value($settings, "user_generate_small_image", 0);
	$generate_large_image = get_setting_value($settings, "user_generate_large_image", 0);

	$r->get_form_values();
	$r->set_value("user_id", get_session("session_user_id"));
	$r->set_value("full_desc_type", 1);
	if (!$r->parameters["is_showing"][SHOW]) {
		$r->change_property("is_showing", USE_IN_INSERT, true);
		$r->set_value("is_showing", 1);
	}
	if (!$r->parameters["friendly_url"][SHOW]) {
		$r->change_property("friendly_url", USE_IN_INSERT, true);
	}
	if (!$r->parameters["item_order"][SHOW]) {
		$r->change_property("item_order", USE_IN_INSERT, true);
		$r->set_value("item_order", 1);
	}
	if (!$r->parameters["is_sales"][SHOW]) {
		$r->change_property("is_sales", USE_IN_INSERT, true);
		$r->set_value("is_sales", 0);
	}
	$r->change_property("price", USE_IN_INSERT, true);
	$r->change_property("trade_price", USE_IN_INSERT, true);
	$r->change_property("sales_price", USE_IN_INSERT, true);
	$r->change_property("trade_sales", USE_IN_INSERT, true);
	$r->change_property("properties_price", USE_IN_INSERT, true);
	$r->change_property("trade_properties_price", USE_IN_INSERT, true);

	if (!$r->parameters["hide_out_of_stock"][SHOW]) {
		$r->change_property("hide_out_of_stock", USE_IN_INSERT, true);
		$r->set_value("hide_out_of_stock", 0);
	}

	$item_id = get_param("item_id");
	$operation = get_param("operation");
	$current_tab = get_param("current_tab");
	if (!$current_tab) { $current_tab = "general"; }
	$return_page = get_custom_friendly_url("user_products.php");
	$downloads_errors = "";
	$product_categories = array();
	
	$sql  = " SELECT sites_all FROM " . $table_prefix . "user_types ";
	$sql .= " WHERE type_id=" . $db->tosql($user_type_id, INTEGER);
	$sites_all = get_db_value($sql);
	
	$user_allow_select_sites      = get_setting_value($product_settings, "user_allow_select_sites", "");
	$user_allow_select_user_types = get_setting_value($product_settings, "user_allow_select_user_types", "");
		
	$access_table = new VA_Access_Table($settings["templates_dir"], "access_table.html");
	$access_table->set_access_levels(
		array(
			VIEW_CATEGORIES_ITEMS_PERM => array(VIEW_MSG, VIEW_ITEM_IN_THE_LIST_MSG), 
			VIEW_ITEMS_PERM => array(ACCESS_DETAILS_MSG, ACCESS_ITEMS_DETAILS_MSG)
		)
	);
	$sql = false;
	if ($user_allow_select_user_types) {
		if ($user_allow_select_sites) {
			if ($sites_all) {
				$sql  = " SELECT type_id, type_name FROM " . $table_prefix . "user_types ";
			} else {
				$sql  = " SELECT t.type_id, t.type_name FROM (" . $table_prefix . "user_types t ";
				$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites ut ON (ut.type_id = t.type_id AND t.sites_all=0)) ";
				$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites ut2 ON (ut2.site_id = ut.site_id ) ";
				$sql .= " WHERE (t.sites_all=1 OR ";
				$sql .= " ut2.type_id=" . $db->tosql($user_type_id, INTEGER) . ")";
				$sql .= " GROUP BY t.type_id, t.type_name ";
			}
		} else {
			if (isset($site_id)) {
				$sql  = " SELECT t.type_id, t.type_name FROM (" . $table_prefix . "user_types t ";
				$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites ut ON (ut.type_id = t.type_id AND t.sites_all=0)) ";
				$sql .= " WHERE (t.sites_all=1 OR ut.site_id=" . $db->tosql($site_id, INTEGER) . ")";
				$sql .= " GROUP BY t.type_id, t.type_name ";
			} else {
				$sql  = " SELECT type_id, type_name FROM " . $table_prefix . "user_types ";
				$sql .= " WHERE sites_all=1";
			}			
		}
	}
	$access_table->set_tables("items", "items_user_types",  "items_subscriptions", "item_id", false, $item_id, $sql);

	$sites_table = new VA_Sites_Table($settings["templates_dir"], "sites_table.html");
	$sql = false;
	if ($user_allow_select_sites && !$sites_all) {
		$sql  = " SELECT s.site_id, s.site_name FROM (" . $table_prefix . "sites s ";
		$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites ut ON ut.site_id = s.site_id) ";
		$sql .= " WHERE ut.type_id=". $db->tosql($user_type_id, INTEGER);
	}
	$sites_table->set_tables("items", "items_sites", "item_id", false, $item_id, $sql);	
		

	if(strlen($operation))
	{
		// get categories values
		for ($i = 1; $i <= $categories_number; $i++) {
			$category_id = get_param("category_id_" . $i);
			if (strlen($category_id)) {
				$category_allowed = false;
				for($co = 0; $co < sizeof($categories_options); $co++) {
					if (strval($categories_options[$co][0]) == strval($category_id)) {
						$category_allowed = true;
					}
				}
				if ($category_allowed && !in_array($category_id, $product_categories)) {
					$product_categories[] = $category_id;
				}
			}
		}

		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		else if($operation == "delete" && $item_id)
		{
			if (!$allowed_delete_product) {
				$r->errors = PROD_DELETE_ERROR;
			} else {
				delete_products($item_id);

				header("Location: " . $return_page);
				exit;
			} 
		} elseif ($operation == "more_prices") {
			$number_prices += 5;
		} elseif ($operation == "more_files") {
			$number_files += 4;
		} elseif ($operation == "more_serials") {
			$number_serials += 5;
		} else if ($operation == "save" || $operation == "duplicate") {
			$is_valid = true;

			$r->set_value("access_level",       $access_table->all_selected_access_level);
			$r->set_value("guest_access_level", $access_table->guest_selected_access_level);
			
			if(strlen($item_id)) {
				if (!$allowed_edit_product) {
					$is_valid = false;
					$r->errors = PROD_EDIT_ERROR;
				}
			} else {
				if (!$allowed_add_product) {
					$is_valid = false;
					$r->errors = PROD_NEW_ERROR;
				} else if (strlen($products_limit)) {
					$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "items ";
					$sql .= " WHERE user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
					$products_number = get_db_value($sql);
					if ($products_number >= $products_limit) {
						$products_limit_error = str_replace("{products_limit}", $products_limit, MERCHANT_PRODUCTS_LIMIT_ERROR);
						$is_valid = false;
						$r->errors = $products_limit_error . "<br>";
					}
				}
			}

			if ($is_valid) {
				if (sizeof($product_categories) < 1) {
					$error_message = str_replace("{field_name}", CATEGORY_MSG, REQUIRED_MESSAGE);
					$r->errors = $error_message . "<br>";
				}
				$r->validate();
				if (strlen($r->errors)) {
					$is_valid = false;
				}
			}

			if ($show_terms) {
				if ($r->get_value("terms") != 1) {
					$is_valid = false;
					$r->errors .= PROD_TERMS_USER_ERROR;
				}
			}

			$itf_valid = $itf_eg->validate();
			$serials_valid = $is_eg->validate();
			if ($is_valid) {
				if (!$itf_valid || !$serials_valid) {
					$current_tab = "downloads";
				}
			} else {
				$current_tab = "general";
			}
			
			if ($is_valid && $itf_valid && $serials_valid) {
				$r->set_value("admin_id_added_by", get_session("session_admin_id"));
				$r->set_value("admin_id_modified_by", get_session("session_admin_id"));

				if ($r->is_empty("price")) {
					$r->set_value("price", 0);
				}
				if ($r->is_empty("trade_price")) {
					$r->set_value("trade_price", 0);
				}
				if ($r->is_empty("sales_price")) {
					$r->set_value("sales_price", 0);
				}
				if ($r->is_empty("trade_sales")) {
					$r->set_value("trade_sales", 0);
				}
				if ($r->is_empty("properties_price")) {
					$r->set_value("properties_price", 0);
				}
				if ($r->is_empty("trade_properties_price")) {
					$r->set_value("trade_properties_price", 0);
				}

				/* convert prices to default currency
				$price = $r->get_value("price");
				$price = round($price / $currency["rate"], 2); 
				$r->set_value("price", $price);*/
	  
				$is_approved = (isset($user_settings["approve_product"]) && $user_settings["approve_product"] == 1) ? 1 : 0;
				$r->set_value("is_approved", $is_approved);
	  
				if (!strlen($item_id) || $operation == "duplicate") {
					if ($operation == "duplicate") {
						$copy_item_id = $item_id;
						$item_id = "";
						$r->set_value("item_name", $r->get_value("item_name") . " (Duplicate)");
						$r->set_value("friendly_url", "");
					}
					set_friendly_url();

					if (!get_setting_value($product_settings, "show_use_stock_level", 0)) {
						$r->change_property("use_stock_level", USE_IN_INSERT, true);
						$r->set_value("use_stock_level", 0);
					}

					$r->set_value("date_added", va_time());
					$r->set_value("date_modified", va_time());
													
					update_product_by_categories($product_categories, false);									
					$record_updated = $r->insert_record();
					if ($record_updated) {						
						if ($db_type == "mysql") {
							$item_id = get_db_value(" SELECT LAST_INSERT_ID() ");
							$r->set_value("item_id", $item_id);
						} else if ($db_type == "access") {
							$item_id = get_db_value(" SELECT @@IDENTITY ");
							$r->set_value("item_id", $item_id);
						} else if ($db_type == "db2") {
							$item_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "items FROM " . $table_prefix . "items");
							$r->set_value("item_id", $item_id);
						}
						
						update_product_categories($item_id, $product_categories);
						$access_table->save_values($item_id);
						$sites_table->save_values($item_id);
						// duplicate some options and components
						if ($operation == "duplicate") {

							// duplicate all properties
							if ($duplicate_properties == 1) {
								$item_properties = array();
								$sql  = " SELECT * FROM " . $table_prefix . "items_properties ";
								$sql .= " WHERE item_id=" . $db->tosql($copy_item_id, INTEGER);
								$db->query($sql);
								if ($db->next_record()) {
									do {
										$item_properties[] = array(
											"property_id" => $db->f("property_id"), "property_name" => $db->f("property_name"), 
											"property_description" => $db->f("property_description"),
											"control_type" => $db->f("control_type"), "control_style" => $db->f("control_style"), 
											"required" => $db->f("required"),
											"use_on_list" => $db->f("use_on_list"), "use_on_details" => $db->f("use_on_details"), 
											"use_on_table" => $db->f("use_on_table"), "use_on_grid" => $db->f("use_on_grid"), 
											"use_on_checkout" => $db->f("use_on_checkout"),
											"start_html" => $db->f("start_html"), "middle_html" => $db->f("middle_html"), "end_html" => $db->f("end_html"),
											"control_code" => $db->f("control_code"), "onchange_code" => $db->f("onchange_code"), "onclick_code" => $db->f("onclick_code"),
											"property_type_id" => $db->f("property_type_id"), "sub_item_id" => $db->f("sub_item_id"), 
											"additional_price" => $db->f("additional_price"), "trade_additional_price" => $db->f("trade_additional_price"), 
											"quantity" => $db->f("quantity"),
											"use_on_second" => $db->f("use_on_second"), "property_order" => $db->f("property_order"), "property_style" => $db->f("property_style"),
											"before_control_html" => $db->f("before_control_html"), "after_control_html" => $db->f("after_control_html")
										);
									} while ($db->next_record());
				    
									$ip = new VA_Record($table_prefix . "items_properties");
									$ip->add_textbox("property_id", INTEGER);
									$ip->add_textbox("item_id", INTEGER);
									$ip->add_textbox("property_name", TEXT);
									$ip->add_textbox("property_description", TEXT);
									$ip->add_textbox("control_type", TEXT);
									$ip->change_property("control_type", USE_SQL_NULL, false);
									$ip->add_textbox("control_style", TEXT);
									$ip->add_textbox("required", INTEGER);
									$ip->add_textbox("use_on_list", INTEGER);
									$ip->add_textbox("use_on_details", INTEGER);
									$ip->add_textbox("use_on_table", INTEGER);
									$ip->add_textbox("use_on_grid", INTEGER);
									$ip->add_textbox("use_on_checkout", INTEGER);
									$ip->add_textbox("start_html", TEXT);
									$ip->add_textbox("middle_html", TEXT);
									$ip->add_textbox("end_html", TEXT);
									$ip->add_textbox("control_code", TEXT);
									$ip->add_textbox("onchange_code", TEXT);
									$ip->add_textbox("onclick_code", TEXT);
									$ip->add_textbox("property_type_id", INTEGER);
									$ip->add_textbox("sub_item_id", INTEGER);
									$ip->add_textbox("additional_price", FLOAT);
									$ip->add_textbox("trade_additional_price", FLOAT);
									$ip->add_textbox("quantity", INTEGER);
									$ip->add_textbox("use_on_second", INTEGER);
									$ip->add_textbox("property_order", INTEGER);
									$ip->add_textbox("property_style", TEXT);
									$ip->add_textbox("before_control_html", TEXT);
									$ip->add_textbox("after_control_html", TEXT);
									$ip->set_value("item_id", $item_id);
				    
									$ipv = new VA_Record($table_prefix . "items_properties_values");
									$ipv->add_textbox("property_id", INTEGER);
									$ipv->add_textbox("value_order", INTEGER);
									$ipv->add_textbox("property_value", TEXT);
									$ipv->add_textbox("additional_price", NUMBER);
									$ipv->add_textbox("trade_additional_price", NUMBER);
									$ipv->add_textbox("quantity", INTEGER);
									$ipv->add_textbox("additional_weight", NUMBER);
									$ipv->add_textbox("hide_value", INTEGER);
									$ipv->add_textbox("is_default_value", INTEGER);
									$ipv->add_textbox("item_code", TEXT);
									$ipv->add_textbox("manufacturer_code", TEXT);
									$ipv->add_textbox("buying_price", NUMBER);
									$ipv->add_textbox("stock_level", INTEGER);
									$ipv->add_textbox("use_stock_level", INTEGER);
									$ipv->add_textbox("hide_out_of_stock", INTEGER);
									$ipv->add_textbox("download_path", TEXT);
									$ipv->add_textbox("download_period", INTEGER);
									$ipv->add_textbox("sub_item_id", INTEGER);
									$ipv->add_textbox("percentage_price", NUMBER);
				    
									for ($i = 0; $i < sizeof($item_properties); $i++) {
										$property_id = $item_properties[$i]["property_id"];
										$db->query("SELECT MAX(property_id) FROM " . $table_prefix . "items_properties ");
										$db->next_record();
										$new_property_id = $db->f(0) + 1;
										$ip->set_value("property_id", $new_property_id);
										$ip->set_value("property_name", $item_properties[$i]["property_name"]);
										$ip->set_value("property_description", $item_properties[$i]["property_description"]);
										$ip->set_value("control_type", $item_properties[$i]["control_type"]);
										$ip->set_value("control_style", $item_properties[$i]["control_style"]);
										$ip->set_value("required", $item_properties[$i]["required"]);
										$ip->set_value("use_on_list", $item_properties[$i]["use_on_list"]);
										$ip->set_value("use_on_details", $item_properties[$i]["use_on_details"]);
										$ip->set_value("use_on_table", $item_properties[$i]["use_on_table"]);
										$ip->set_value("use_on_grid", $item_properties[$i]["use_on_grid"]);
										$ip->set_value("use_on_checkout", $item_properties[$i]["use_on_checkout"]);
										$ip->set_value("start_html", $item_properties[$i]["start_html"]);
										$ip->set_value("middle_html", $item_properties[$i]["middle_html"]);
										$ip->set_value("end_html", $item_properties[$i]["end_html"]);
										$ip->set_value("control_code", $item_properties[$i]["control_code"]);
										$ip->set_value("onchange_code", $item_properties[$i]["onchange_code"]);
										$ip->set_value("onclick_code", $item_properties[$i]["onclick_code"]);
										$ip->set_value("property_type_id", $item_properties[$i]["property_type_id"]);
										$ip->set_value("sub_item_id", $item_properties[$i]["sub_item_id"]);
										$ip->set_value("additional_price", $item_properties[$i]["additional_price"]);
										$ip->set_value("trade_additional_price", $item_properties[$i]["trade_additional_price"]);
										$ip->set_value("quantity", $item_properties[$i]["quantity"]);
										$ip->set_value("use_on_second", $item_properties[$i]["use_on_second"]);
										$ip->set_value("property_order", $item_properties[$i]["property_order"]);
										$ip->set_value("property_style", $item_properties[$i]["property_style"]);
										$ip->set_value("before_control_html", $item_properties[$i]["before_control_html"]);
										$ip->set_value("after_control_html", $item_properties[$i]["after_control_html"]);
				    
										$ip->insert_record();
										// duplicate property values
										$property_values = array();
										$sql  = " SELECT * FROM " . $table_prefix . "items_properties_values ";
										$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER);
										$db->query($sql);
										while ($db->next_record()) {
											$property_values[] = array(
												"value_order" => $db->f("value_order"), "property_value" => $db->f("property_value"), 
												"additional_price" => $db->f("additional_price"), "trade_additional_price" => $db->f("trade_additional_price"), 
												"quantity" => $db->f("quantity"), "additional_weight" => $db->f("additional_weight"), 
												"hide_value" => $db->f("hide_value"), "is_default_value" => $db->f("is_default_value"),
												"item_code" => $db->f("item_code"), "manufacturer_code" => $db->f("manufacturer_code"), "buying_price" => $db->f("buying_price"), 
												"stock_level" => $db->f("stock_level"), "use_stock_level" => $db->f("use_stock_level"), 
												"hide_out_of_stock" => $db->f("hide_out_of_stock"), "download_path" => $db->f("download_path"),
												"download_period" => $db->f("download_period"), "sub_item_id" => $db->f("sub_item_id"), 
												"percentage_price" => $db->f("percentage_price")
											);
										}
										for ($pi = 0; $pi < sizeof($property_values); $pi++) {
											$ipv->set_value("property_id", $new_property_id);
											$ipv->set_value("value_order", $property_values[$pi]["value_order"]);
											$ipv->set_value("property_value", $property_values[$pi]["property_value"]);
											$ipv->set_value("additional_price", $property_values[$pi]["additional_price"]);
											$ipv->set_value("trade_additional_price", $property_values[$pi]["trade_additional_price"]);
											$ipv->set_value("quantity", $property_values[$pi]["quantity"]);
											$ipv->set_value("additional_weight", $property_values[$pi]["additional_weight"]);
											$ipv->set_value("hide_value", $property_values[$pi]["hide_value"]);
											$ipv->set_value("is_default_value", $property_values[$pi]["is_default_value"]);
				    
											$ipv->set_value("item_code", $property_values[$pi]["item_code"]);
											$ipv->set_value("manufacturer_code", $property_values[$pi]["manufacturer_code"]);
											$ipv->set_value("buying_price", $property_values[$pi]["buying_price"]);
											$ipv->set_value("stock_level", $property_values[$pi]["stock_level"]);
											$ipv->set_value("use_stock_level", $property_values[$pi]["use_stock_level"]);
											$ipv->set_value("hide_out_of_stock", $property_values[$pi]["hide_out_of_stock"]);
											$ipv->set_value("download_path", $property_values[$pi]["download_path"]);
											$ipv->set_value("download_period", $property_values[$pi]["download_period"]);
											$ipv->set_value("sub_item_id", $property_values[$pi]["sub_item_id"]);
											$ipv->set_value("percentage_price", $property_values[$pi]["percentage_price"]);
				    
											$ipv->insert_record();
										}
									}
								}
							}
							// end of saving properties

						}
					} else {
						$item_id = "";
						$r->set_value("item_id", "");
					}
				} else {
					if ($friendly_auto) {
						$r->change_property("friendly_url", USE_IN_UPDATE, true);
						set_friendly_url();
					}
					update_product_by_categories($product_categories, false);
					$r->set_value("date_modified", va_time());
					$record_updated = $r->update_record();
					if ($record_updated) {
						update_product_categories($item_id, $product_categories);
						$access_table->save_values($item_id);
						$sites_table->save_values($item_id);										
					}
				}

				if ($operation == "save" || $operation == "apply") {
					// update downloadable files
					$itf_eg->set_values("item_id", $item_id);
					$itf_eg->set_values("item_type_id", 0);
					$itf_eg->update_all($number_files);
				}

				if ($operation == "save" || $operation == "duplicate") {
					// update serial numbers
					$is_eg->set_values("item_id", $item_id);
					$is_eg->update_all($number_serials);
				}

				if ($record_updated) {

					$admin_notification     = get_setting_value($product_settings, "admin_notification", 0);
					$user_notification      = get_setting_value($product_settings, "user_notification", 0);
					$admin_sms_notification = get_setting_value($product_settings, "admin_sms_notification", 0);
					$user_sms_notification  = get_setting_value($product_settings, "user_sms_notification", 0);

					if ($admin_notification || $user_notification || $admin_sms_notification || $user_sms_notification) {
						$r->set_parameters();
					}
			  
					if ($admin_notification) {
						$t->set_block("admin_subject", $product_settings["admin_subject"]);
						$t->set_block("admin_message", $product_settings["admin_message"]);
						$t->parse("admin_subject", false);
						$t->parse("admin_message", false);
			  
						$mail_to = get_setting_value($product_settings, "admin_email", $settings["admin_email"]);
						$mail_to = str_replace(";", ",", $mail_to);
						$email_headers["from"] = get_setting_value($product_settings, "admin_mail_from", $settings["admin_email"]);
						$email_headers["cc"] = get_setting_value($product_settings, "cc_emails");
						$email_headers["bcc"] = get_setting_value($product_settings, "admin_mail_bcc");
						$email_headers["reply_to"] = get_setting_value($product_settings, "admin_mail_reply_to");
						$email_headers["return_path"] = get_setting_value($product_settings, "admin_mail_return_path");
						$email_headers["mail_type"] = get_setting_value($product_settings, "admin_message_type");
			  
						$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
						va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers);
					}		 
					if ($user_notification) {
						$t->set_block("user_subject", $product_settings["user_subject"]);
						$t->set_block("user_message", $product_settings["user_message"]);
						$t->parse("user_subject", false);
						$t->parse("user_message", false);
			  
			      $mail_to = get_session("session_user_email");
						$email_headers = array();
						$email_headers["from"] = get_setting_value($product_settings, "user_mail_from", $settings["admin_email"]);
						$email_headers["cc"] = get_setting_value($product_settings, "user_mail_cc");
						$email_headers["bcc"] = get_setting_value($product_settings, "user_mail_bcc");
						$email_headers["reply_to"] = get_setting_value($product_settings, "user_mail_reply_to");
						$email_headers["return_path"] = get_setting_value($product_settings, "user_mail_return_path");
						$email_headers["mail_type"] = get_setting_value($product_settings, "user_message_type");
			  
						$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
						va_mail($mail_to, $t->get_var("user_subject"), $user_message, $email_headers);
					}	
			  
					if ($admin_sms_notification) 
					{
						$admin_sms_recipient  = get_setting_value($order_info, "admin_sms_recipient", "");
						$admin_sms_originator = get_setting_value($order_info, "admin_sms_originator", "");
						$admin_sms_message    = get_setting_value($order_info, "admin_sms_message", "");
			  
						$t->set_block("admin_sms_recipient",  $admin_sms_recipient);
						$t->set_block("admin_sms_originator", $admin_sms_originator);
						$t->set_block("admin_sms_message",    $admin_sms_message);
			  
						$t->parse("admin_sms_recipient", false);
						$t->parse("admin_sms_originator", false);
						$t->parse("admin_sms_message", false);
			  
						sms_send($t->get_var("admin_sms_recipient"), $t->get_var("admin_sms_message"), $t->get_var("admin_sms_originator"));
					}		 
			  
					if ($user_sms_notification) 
					{
						$cell_phone = get_db_value("SELECT cell_phone FROM " . $table_prefix . "users WHERE user_id=" . $db->tosql(get_session("session_user_id"), INTEGER));

						$user_sms_recipient  = get_setting_value($order_info, "user_sms_recipient", $cell_phone);
						$user_sms_originator = get_setting_value($order_info, "user_sms_originator", "");
						$user_sms_message    = get_setting_value($order_info, "user_sms_message", "");
			      
						$t->set_block("user_sms_recipient",  $user_sms_recipient);
						$t->set_block("user_sms_originator", $user_sms_originator);
						$t->set_block("user_sms_message",    $user_sms_message);
			      
						$t->parse("user_sms_recipient", false);
						$t->parse("user_sms_originator", false);
						$t->parse("user_sms_message", false);
			      
						if (sms_send_allowed($t->get_var("user_sms_recipient"))) {
							sms_send($t->get_var("user_sms_recipient"), $t->get_var("user_sms_message"), $t->get_var("user_sms_originator"));
						}
					}		 
	      
					header("Location: " . $return_page);
					exit;
				}

			}
		}
	} else if(strlen($item_id)) {
		$is_record = $r->get_db_values();
		if (!$is_record) {
			$item_id = "";
			$r->set_value("item_id", "");
		}

		if ($r->get_value("trade_price") == 0) { $r->set_value("trade_price", ""); }
		if ($r->get_value("sales_price") == 0) { $r->set_value("sales_price", ""); }
		if ($r->get_value("trade_sales") == 0) { $r->set_value("trade_sales", ""); }
		if ($r->get_value("buying_price") == 0) {	$r->set_value("buying_price", ""); }
		if ($r->get_value("properties_price") == 0) {	$r->set_value("properties_price", ""); }
		if ($r->get_value("trade_properties_price") == 0) {	$r->set_value("trade_properties_price", ""); }

		// check data for downloadable files
		$itf_eg->set_value("item_id", $item_id);
		$itf_eg->change_property("file_id", USE_IN_SELECT, true);
		$itf_eg->change_property("file_id", USE_IN_WHERE, false);
		$itf_eg->change_property("item_id", USE_IN_WHERE, true);
		$itf_eg->change_property("item_id", USE_IN_SELECT, true);
		$number_files = $itf_eg->get_db_values();
		if ($number_files == 0) {
			$number_files = 1;
		}

		// check data for serial numbers
		$is_eg->set_value("item_id", $item_id);
		$is_eg->change_property("serial_id", USE_IN_SELECT, true);
		$is_eg->change_property("serial_id", USE_IN_WHERE, false);
		$is_eg->change_property("item_id", USE_IN_WHERE, true);
		$is_eg->change_property("item_id", USE_IN_SELECT, true);
		$number_serials = $is_eg->get_db_values();
		if ($number_serials == 0) {
			$number_serials = 5;
		}

		/* convert prices to selected currency
		$price = $r->get_value("price");
		$price = round($price * $currency["rate"], 2); 
		$r->set_value("price", $price);//*/

		$product_categories = array();
		$sql  = " SELECT category_id FROM " . $table_prefix . "items_categories ";
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$category_id = $db->f("category_id");
			if (!in_array($category_id, $product_categories)) {
				$product_categories[] = $category_id;
			}
		}

		$duplicate_properties    = ($duplicate_properties == 1) ? " checked " : "";
		$t->set_var("duplicate_properties",    $duplicate_properties);

	}
	else // new record (set default values)
	{
		$sql  = " SELECT MAX(item_order) FROM " . $table_prefix . "items i ";
		$sql .= " WHERE user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
		$item_order = get_db_value($sql);
		$item_order = ($item_order) ? ($item_order + 1) : 1;
		$r->set_value("item_order", $item_order);
		//$r->set_value("use_stock_level", 1);
		//$r->set_value("hide_out_of_stock", 1);
		$r->set_value("is_showing", 1);

		// quantity pricesfiles 
		$number_prices = 5;
		// downloadable files 
		$number_files = 5;
		// serial numbers
		$number_serials = 5;
	}

	$r->set_form_parameters();

	$tabs_parse["general"] = true;

	// set errors
	if ($downloads_errors) {
		$t->set_var("errors_list", $downloads_errors);
		$t->parse("downloads_errors", false);
	}

	// set downloadable files
	$t->set_var("number_files", $number_files);
	$itf_eg->set_parameters_all($number_files);

	// set serial numbers
	$t->set_var("number_serials", $number_serials);
	$is_eg->set_parameters_all($number_serials);

	// set categories
	for ($i = 0; $i < $categories_number; $i++) {
		$index = $i + 1;
		$category_param = "category_id_" . $index;
		$t->set_var("category_param", $category_param);
		if ($index == 1) {
			$t->set_var("category_required", "*");
		} else {
			$t->set_var("category_required", "");
		}
		if ($categories_number > 1) {
			$t->set_var("category_index", "#".$index);
		}
		if (isset($product_categories[$i])) {
			$category_id = $product_categories[$i];
		} else {
			$category_id = "";
		}
		set_options($categories_options, $category_id, "category_id");
		$t->parse("categories", true);
	}


	if (strlen($item_id)) {
		if ($allowed_edit_product) {
			$t->set_var("save_button_title", UPDATE_BUTTON);
			$t->global_parse("save_button", false, false, true);
		}
		if ($allowed_add_product) {
			$t->sparse("duplicate", false);
		}
		if ($allowed_delete_product) {
			$t->parse("delete", false);	
		}
		$t->set_var("default_checked", "");
	} else {
		if ($allowed_add_product) {
			$t->set_var("save_button_title", ADD_BUTTON);
			$t->global_parse("save_button", false, false, true);
		}
		$t->set_var("delete", "");
		$t->set_var("default_checked", "checked");
	}

	// parse sections
	if ($r->parameters["manufacturer_id"][SHOW] || $r->parameters["manufacturer_code"][SHOW]) {
		$t->sparse("manufacturer_section", false);
	}
	if ($r->parameters["is_compared"][SHOW] || $r->parameters["tax_free"][SHOW]) {
		$t->sparse("additional_options_section", false);
	}
	if ($r->parameters["features"][SHOW] || $r->parameters["short_description"][SHOW] || $r->parameters["full_description"][SHOW]) {
		$t->sparse("description_section", false);
	}
	if ($r->parameters["price"][SHOW] && $r->parameters["is_sales"][SHOW] && $r->parameters["sales_price"][SHOW] && $r->parameters["discount_percent"][SHOW]) {
		$t->sparse("preview_price_title", false);
		$t->sparse("preview_price_block", false);
	}
	if ($r->parameters["properties_price"][SHOW] || $r->parameters["trade_properties_price"][SHOW]) {
		$t->sparse("properties_block", false);
	}
	if (
		$r->parameters["is_price_edit"][SHOW] || $r->parameters["price"][SHOW] || $r->parameters["trade_price"][SHOW] ||
		$r->parameters["is_sales"][SHOW] || $r->parameters["sales_price"][SHOW] || $r->parameters["trade_sales"][SHOW] ||
		$r->parameters["discount_percent"][SHOW] || $r->parameters["buying_price"][SHOW] || $r->parameters["properties_price"][SHOW] ||
		$r->parameters["trade_properties_price"][SHOW]
	) {
		$t->sparse("pricing_section", false);
	}
	if ($r->parameters["meta_title"][SHOW] || $r->parameters["meta_keywords"][SHOW] || $r->parameters["meta_description"][SHOW]) {
		$t->sparse("meta_section", false);
	}

	if (
		$r->parameters["tiny_image"][SHOW] || $r->parameters["tiny_image_alt"][SHOW] || 
		$r->parameters["small_image"][SHOW] || $r->parameters["small_image_alt"][SHOW] || 
		$r->parameters["big_image"][SHOW] || $r->parameters["big_image_alt"][SHOW] || $r->parameters["super_image"][SHOW]
	) {
		$t->sparse("images_section", false);
	}

	if ($r->parameters["hide_add_list"][SHOW] || $r->parameters["hide_add_details"][SHOW] ||
		$r->parameters["hide_add_table"][SHOW] || $r->parameters["hide_add_grid"][SHOW]
	) {
		$t->sparse("hide_add_section", false);
	}
	if ($r->parameters["preview_url"][SHOW] || $r->parameters["preview_width"][SHOW] || $r->parameters["preview_height"][SHOW]) {
		$t->sparse("preview_url_section", false);
	}
	if (
		$r->parameters["template_name"][SHOW] || 
		$r->parameters["hide_add_list"][SHOW] || $r->parameters["hide_add_details"][SHOW] || 
		$r->parameters["hide_add_table"][SHOW] || $r->parameters["hide_add_grid"][SHOW] || 
		$r->parameters["preview_url"][SHOW] || $r->parameters["preview_width"][SHOW] || $r->parameters["preview_height"][SHOW]
	) {
		$t->sparse("appearance_section", false);
	}

	if (
		$r->parameters["use_stock_level"][SHOW] || $r->parameters["stock_level"][SHOW] || 
		$r->parameters["hide_out_of_stock"][SHOW] || $r->parameters["disable_out_of_stock"][SHOW] ||
		$r->parameters["min_quantity"][SHOW] || $r->parameters["max_quantity"][SHOW] || $r->parameters["quantity_increment"][SHOW]
	) {
		$t->sparse("stock_section", false);
	}

	if (
		$r->parameters["is_shipping_free"][SHOW] || $r->parameters["shipping_cost"][SHOW] || 
		$r->parameters["shipping_in_stock"][SHOW] || $r->parameters["shipping_out_stock"][SHOW] || $r->parameters["shipping_rule_id"][SHOW]
	) {
		$t->sparse("shipping_section", false);
	}

	$download_section = false; $serial_section = false;
	if ($r->parameters["download_show_terms"][SHOW] || $r->parameters["download_terms_text"][SHOW]) {
		$download_section = true;
		$t->sparse("download_section", false);
	}
	if ($show_downloadable_files) {
		$download_section = true;
		$t->sparse("downloadable_files", false);
	}
	if ($r->parameters["generate_serial"][SHOW] || $r->parameters["serial_period"][SHOW] || $r->parameters["activations_number"][SHOW] || $show_predefined_serials) {
		$serial_section = true;
		$t->sparse("serial_section", false);
	}
	if ($show_predefined_serials) {
		$t->sparse("serial_predefined_section", false);
	}
	$tabs_parse["downloads"] = ($download_section || $serial_section);

	$tabs_parse["special_offer"] = ($r->parameters["is_special_offer"][SHOW] || $r->parameters["special_offer"][SHOW]);

	$email_notify_section = false; $sms_notify_section = false;
	if (
		$r->parameters["mail_notify"][SHOW] || $r->parameters["mail_to"][SHOW] || $r->parameters["mail_from"][SHOW] ||
		$r->parameters["mail_cc"][SHOW] || $r->parameters["mail_bcc"][SHOW] || $r->parameters["mail_reply_to"][SHOW] ||
		$r->parameters["mail_return_path"][SHOW] || $r->parameters["mail_subject"][SHOW] || $r->parameters["mail_type"][SHOW] ||
		$r->parameters["mail_body"][SHOW]
	) {
		$email_notify_section = true; 
		$t->sparse("email_notify_section", false);
	}
	if (
		$r->parameters["sms_notify"][SHOW] || $r->parameters["sms_recipient"][SHOW] || 
		$r->parameters["sms_originator"][SHOW] || $r->parameters["sms_message"][SHOW] 
	) {
		$sms_notify_section = true;
		$t->sparse("sms_notify_section", false);
	}
	$tabs_parse["notifications"] = ($email_notify_section || $sms_notify_section);


	$tabs_parse["subscriptions"] = false;
	if ($user_allow_select_user_types) {			
		$has_any_subscriptions = $access_table->parse("subscriptions_table", $r->get_value("access_level"), $r->get_value("guest_access_level"));
		if ($has_any_subscriptions) {
			$tabs_parse["subscriptions"] = true;
		}
	}

	$tabs_parse["sites"] = false;
	if ($user_allow_select_sites) {
		$sites_table->message = USE_ITEM_ALL_SITES_MSG;
		$sites_table->parse("sites_table", $r->get_value("sites_all"));
		$tabs_parse["sites"] = true;
	}
	
	$rating_section = false; $other_section = false;
	if ($r->parameters["votes"][SHOW] || $r->parameters["points"][SHOW]) {
		$rating_section = true; 
		$t->sparse("rating_section", false);
	}
	if ($r->parameters["notes"][SHOW] || $r->parameters["buy_link"][SHOW]) {
		$other_section = true;
		$t->sparse("other_section", false);
	}
	$tabs_parse["other"] = ($rating_section || $other_section);

	// set styles for tabs
	$tabs = array(
		"general" => PROD_GENERAL_TAB, 
		"downloads" => PROD_DOWNLOADABLE_TAB, 
		"special_offer" => PROD_SPECIAL_OFFER_TAB,
	 	"notifications" => PROD_NOTIFICATION_TAB, 
	 	"other" => PROD_NOTES_TAB,
	 	"sites" => SITES_MSG,
		"subscriptions" => ACCESS_LEVELS_MSG
	 );
	foreach ($tabs as $tab_name => $tab_title) {
		$t->set_var("tab_id", "tab_" . $tab_name);
		$t->set_var("tab_name", $tab_name);
		$t->set_var("tab_title", $tab_title);
		if ($tab_name == $current_tab) {
			$t->set_var("tab_class", "adminTabActive");
			$t->set_var($tab_name . "_style", "display: block;");
		} else {
			$t->set_var("tab_class", "adminTab");
			$t->set_var($tab_name . "_style", "display: none;");
		}
		if ($tabs_parse[$tab_name]) {
			$t->parse("tabs", true);
		}
	}
	$t->set_var("current_tab", $current_tab);

	$t->parse("block_body", false);
	$t->parse($block_name, true);

	function update_product_categories($item_id, $product_categories) {
		global $db, $table_prefix;

		$sql  = " DELETE FROM " . $table_prefix . "items_categories";
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
		
		for ($ci = 0; $ci < sizeof($product_categories); $ci++) {
			$category_id = $product_categories[$ci];
			if (strlen($category_id)) {
				$sql  = " INSERT INTO " . $table_prefix . "items_categories (item_id,category_id) VALUES (";
				$sql .= $db->tosql($item_id, INTEGER) . ",";
				$sql .= $db->tosql($category_id, INTEGER) . ")";
				$db->query($sql);
			}
		}
	}
	
	function update_product_by_categories($categories = false, $only_display = false) {
		global $table_prefix, $db, $r, $access_table, $sites_table, $product_settings;
		
		$save_subscriptions_by_category = get_param("save_subscriptions_by_category");
		$save_sites_by_category         = get_param("save_sites_by_category");
		$user_allow_select_user_types   = get_setting_value($product_settings, "user_allow_select_user_types", "");
		$user_allow_select_sites        = get_setting_value($product_settings, "user_allow_select_sites", "");
		if (!$user_allow_select_user_types)
			$save_subscriptions_by_category = true;
		if (!$user_allow_select_sites)
			$save_sites_by_category = true;
		
		$is_in_top = false;
		
		if ($only_display || $save_subscriptions_by_category || $save_sites_by_category) {
			if (!$only_display && !$categories) {
				$is_in_top = true;
			} elseif (in_array(0, $categories)) {
				$is_in_top = true;
			}
					
			if ($is_in_top) {
				if ($only_display || $save_subscriptions_by_category) {
					$r->set_value("access_level", VIEW_ITEMS_PERM + VIEW_CATEGORIES_ITEMS_PERM);
					$r->set_value("guest_access_level", VIEW_ITEMS_PERM + VIEW_CATEGORIES_ITEMS_PERM);
				}
				if ($only_display || $save_sites_by_category) {
					$r->set_value("sites_all", 1);
				}
			} elseif ($categories) {
				if ($only_display || $save_subscriptions_by_category) {
					$access_level       = 0;
					$guest_access_level = 0;					
					
					foreach ($access_table->access_levels_keys AS $value) {
						$sql  = " SELECT category_id FROM " . $table_prefix . "categories ";
						$sql .= " WHERE category_id IN (" . $db->tosql($categories, INTEGERS_LIST) . ")";
						$sql .= " AND " . format_binary_for_sql("access_level", $value);
						$db->query($sql);
						if ($db->next_record()) {
							$access_level += $value;
						}
						$sql  = " SELECT category_id FROM " . $table_prefix . "categories ";
						$sql .= " WHERE category_id IN (" . $db->tosql($categories, INTEGERS_LIST) . ")";
						$sql .= " AND " . format_binary_for_sql("guest_access_level", $value);
						$db->query($sql);
						if ($db->next_record()) {
							$guest_access_level += $value;
						}						
					}
					$r->set_value("access_level", $access_level);
					$r->set_value("guest_access_level", $guest_access_level);
					$access_table->access_level = $access_level;
					$access_table->guest_access_level = $guest_access_level;
						
					$access_table->selected_user_access_levels = array();
					$sql  = " SELECT user_type_id, access_level FROM " . $table_prefix . "categories_user_types";
					$sql .= " WHERE category_id IN (" . $db->tosql($categories, INTEGERS_LIST) . ")";
					$db->query($sql);
					while ($db->next_record()) {
						$user_type = $db->f("user_type_id");
						$value     = $db->f("access_level");
						if (! ($access_level&$value)) {
							if (isset($access_table->selected_user_access_levels[$user_type])) {
								if (!($access_table->selected_user_access_levels[$user_type]&$value))
									$access_table->selected_user_access_levels[$user_type] += $value;
							} else {
								$access_table->selected_user_access_levels[$user_type] = $value;
							}
						}
					}
					
					$access_table->selected_access_levels = array();
					$sql  = " SELECT subscription_id, access_level FROM " . $table_prefix . "categories_subscriptions";
					$sql .= " WHERE category_id IN (" . $db->tosql($categories, INTEGERS_LIST) . ")";
					$db->query($sql);
					while ($db->next_record()) {
						$subscription_id = $db->f("subscription_id");
						$value     = $db->f("access_level");
						if (!($access_level&$value)) {
							if (isset($access_table->selected_access_levels[$subscription_id])) {
								if (!($access_table->selected_access_levels[$subscription_id]&$value))
									$access_table->selected_access_levels[$subscription_id] += $value;
							} else {
								$access_table->selected_access_levels[$subscription_id] = $value;
							}
						}
					}
				}
				
				if($only_display || $save_sites_by_category) {
					$sites_all = 0;
					$sql  = " SELECT category_id FROM " . $table_prefix . "categories ";
					$sql .= " WHERE category_id IN (" . $db->tosql($categories, INTEGERS_LIST) . ")";
					$sql .= " AND sites_all = 1";
					$db->query($sql);
					if ($db->next_record()) {
						$sites_all = 1;
					}
					$r->set_value("sites_all", $sites_all);
					$sites_table->sites_all = $sites_all;
					$sites_table->selected_sites = array();
					$sql  = " SELECT site_id FROM " . $table_prefix . "categories_sites";
					$sql .= " WHERE category_id IN (" . $db->tosql($categories, INTEGERS_LIST) . ")";
					$db->query($sql);
					while ($db->next_record()) {
						$site_id = $db->f("site_id");
						$sites_table->selected_sites[] = $site_id;
					}
				}
			}
		}
	}
?>