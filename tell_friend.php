<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  tell_friend.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php"); 
	include_once("./includes/record.php");
	include_once("./includes/url.php");

	$site_url = get_setting_value($settings, "site_url", 0);
	$secure_url = get_setting_value($settings, "secure_url", 0);
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$tell_friend_param = get_setting_value($settings, "tell_friend_param", 1);

	$user_id = get_session("session_user_id");		
	$user_info = get_session("session_user_info");
	$user_type_id = get_setting_value($user_info, "user_type_id", "");	
	$user_login = get_setting_value($user_info, "login", "");	
	$user_nickname = get_setting_value($user_info, "nickname", "");	
	$friend_code = get_setting_value($user_info, "nickname", $user_login);	

	if ($is_ssl) {
		$absolute_url = $secure_url;
	} else {
		$absolute_url = $site_url;
	}
  	
	$t = new VA_Template($settings["templates_dir"]);
	$t->show_tags=true;
	$t->set_file("main", "tell_friend.html");
	$t->set_var("tell_friend_href", "tell_friend.php");
	$t->set_var("site_url", $settings["site_url"]);
	$t->set_var("absolute_url", $absolute_url);
	
	$css_file = "";
	if (isset($settings["style_name"]) && $settings["style_name"]) {
		$css_file = "styles/" . $settings["style_name"];
		if (isset($settings["scheme_name"]) && $settings["scheme_name"]) {
			$css_file .= "_" . $settings["scheme_name"];
		}
		$css_file .= ".css";
	}
	$t->set_var("css_file", $css_file);

	$friend_settings = array();
	$type = get_param("type");
	$item_id = get_param("item_id");
	switch ($type) {
		case "ads":			
			include_once("./includes/ads_functions.php");
			$category_id = VA_Ads::get_category_id($item_id, VIEW_ITEMS_PERM);
			
			if (!VA_Ads::check_permissions($item_id, $category_id, VIEW_ITEMS_PERM)) {
				header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
				exit;
			}
			$setting_type = "ads_tell_friend";
			break;
		case "products":
			include_once("./includes/products_functions.php");
			if (!VA_Products::check_permissions($item_id, VIEW_ITEMS_PERM)) {
				header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
				exit;
			}
			$category_id = VA_Products::get_category_id($item_id, VIEW_ITEMS_PERM);
			$setting_type = "products_tell_friend";
			break;
		case "articles":
			include_once("./includes/articles_functions.php");
			if (!VA_Articles::check_permissions($item_id, VIEW_ITEMS_PERM)) {
				header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
				exit;
			}	
			$category_id = VA_Articles::get_top_id($item_id, VIEW_ITEMS_PERM);
			$setting_type = "articles_" . $category_id . "_tell_friend";
			break;
		default:
			echo "This is not a product, ad, article.";
			exit;
	}

	$sql  = " SELECT setting_name, setting_value FROM ". $table_prefix ."global_settings ";
	$sql .= " WHERE setting_type=" . $db->tosql($setting_type,TEXT);
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);

	while ($db->next_record()) {
		$friend_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$use_random_image = get_setting_value($friend_settings, "use_random_image", 0);
	if (($use_random_image == 2) || ($use_random_image == 1 && !strlen(get_session("session_user_id")))) { 
		$use_validation = true;
	} else {
		$use_validation = false;
	}

	$r = new VA_Record("");

	$r->add_textbox("from_name", TEXT);
	$r->add_textbox("from_email", TEXT, TELL_YOUR_EMAIL_FIELD);
	$r->change_property("from_email", REQUIRED, true);
	$r->change_property("from_email", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("to_name", TEXT, TELL_FRIENDS_NAME_FIELD);
	$r->add_textbox("to_email", TEXT, TELL_FRIENDS_EMAIL_FIELD);
	$r->change_property("to_email", REQUIRED, true);
	$r->change_property("to_email", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("comment", TEXT);
	$r->add_hidden("item_id", TEXT);
	$r->change_property("item_id", REQUIRED, true);
	$r->add_hidden("type", TEXT);
	$r->add_textbox("validation_number", TEXT, VALIDATION_CODE_FIELD);
	$r->change_property("validation_number", USE_IN_INSERT, false);
	$r->change_property("validation_number", USE_IN_UPDATE, false);
	$r->change_property("validation_number", USE_IN_SELECT, false);
	if ($use_validation) {
		$r->change_property("validation_number", REQUIRED, true);
		$r->change_property("validation_number", SHOW, true);
	} else {
		$r->change_property("validation_number", REQUIRED, false);
		$r->change_property("validation_number", SHOW, false);
	}

	$r->get_form_values();

	switch ($r->get_value("type"))
	{
		case "products":
			// get information for product 
			$setting_type="'products_tell_friend'";
			$price_type = get_session("session_price_type");
			if ($price_type == 1) {
				$price_field = "trade_price";
				$sales_field = "trade_sales";
			} else {
				$price_field = "price";
				$sales_field = "sales_price";
			}
		
			$sql  = " SELECT i.item_id, i.item_code, i.item_name, m.manufacturer_name, i.manufacturer_code, i." . $price_field . ",";
			$sql .= " i.is_sales, i." . $sales_field . ", i.short_description, i.full_description, i.friendly_url ";
			$sql .= " FROM (". $table_prefix ."items i ";
			$sql .= " LEFT JOIN ". $table_prefix ."manufacturers m ON i.manufacturer_id=m.manufacturer_id)";
			$sql .= " WHERE i.item_id=".$db->tosql($r->get_value("item_id"), INTEGER);
			$db->query($sql);
			if (!$db->next_record()){
				$error = str_replace("{item_id}", htmlspecialchars($r->get_value("item_id")), PRODUCT_ID_NO_LONGER_EXISTS_MSG);
				$r->errors = $error . "<br>";
			}
			$t->set_vars($db->Record);
			$item_name = get_translation($db->f("item_name"));
			$friendly_url = $db->f("friendly_url");
			$t->set_var("item_name", $item_name);
			$t->set_var("item_title", $item_name);
			$t->set_var("short_description", get_translation($db->f("short_description")));
			$t->set_var("full_description", get_translation($db->f("full_description")));
	
			$price = $db->f($price_field);
			$is_sales = $db->f("is_sales");
			$sales_price = $db->f($sales_field);
			if ($is_sales && $sales_price > 0) {
				$price = $sales_price;
			}
			$t->set_var("price", currency_format($price));

			if ($friendly_urls && $friendly_url) {
				$url = new VA_URL($settings["site_url"].$friendly_url.$friendly_extension);	
			} else {
				$url = new VA_URL($settings["site_url"]."product_details.php");	
				$url->add_parameter("item_id", REQUEST, "item_id");
			}
			if ($tell_friend_param) {
				$url->add_parameter("friend", CONSTANT, $friend_code);
			}

			$t->set_var("item_url", $url->get_url());
	
			break;
		case "ads":
			// Select global settings
			$setting_type="'ads_tell_friend'";
	
			// Select this category
			$sql  = " SELECT c.category_name FROM ((". $table_prefix ."ads_items i ";
			$sql .= " LEFT JOIN ". $table_prefix ."ads_assigned a ON i.item_id=a.item_id) ";
			$sql .= " LEFT JOIN ". $table_prefix ."ads_categories c ON a.category_id=c.category_id) ";
			$sql .= " WHERE i.item_id=".$db->tosql($r->get_value("item_id"), INTEGER);
			$db->query($sql);
			$category_name = "";
			if ($db->next_record()) {
				$category_name = get_translation($db->f("category_name"));
			}
			$t->set_var("category",	$category_name);
	
			// Select all fields
			$sql  = " SELECT i.item_id, t.type_name AS type, u.user_id AS seller_id, u.name AS seller_name, i.item_title, i.friendly_url, i.date_start, ";
			$sql .= " i.date_end, i.date_added, i.date_updated, i.short_description, i.full_description, i.price, quantity, i.availability, ";
			$sql .= " i.location_info, i.location_city, st.state_name AS location_state, co.country_name AS location_country, i.is_compared ";
			$sql .= " FROM ((((". $table_prefix ."ads_items i ";
			$sql .= " LEFT JOIN " . $table_prefix . "ads_types t ON i.type_id=t.type_id) ";
			$sql .= " LEFT JOIN ". $table_prefix ."users u ON u.user_id=i.user_id) ";
			$sql .= " LEFT JOIN ". $table_prefix ."countries co ON i.location_country=co.country_code) ";
			$sql .= " LEFT JOIN ". $table_prefix ."states st ON i.location_state=st.state_code) ";
			$sql .= " WHERE i.item_id=".$db->tosql($r->get_value("item_id"), INTEGER);
			$db->query($sql);
			if (!$db->next_record()){
				$r->errors="Ad with such ID no longer exists in database.<br>";
			}
			$t->set_vars($db->Record);
			$item_title = get_translation($db->f("item_title"));
			$friendly_url = $db->f("friendly_url");
			$t->set_var("item_title", $item_title);
			$t->set_var("item_name", $item_title);
			$t->set_var("short_description", get_translation($db->f("short_description")));
			$t->set_var("full_description", get_translation($db->f("full_description")));
			$t->set_var("availability", get_translation($db->f("availability")));
			$t->set_var("location_info", get_translation($db->f("location_info")));
			$t->set_var("location_city", get_translation($db->f("location_city")));
	
			$date_start = $db->f("date_start", DATETIME);
			$date_end = $db->f("date_end", DATETIME);
			$date_added = $db->f("date_added", DATETIME);
			$date_updated = $db->f("date_updated", DATETIME);
			$date_start_ts = mktime(0,0,0, $date_start[MONTH], $date_start[DAY], $date_start[YEAR]);
			$date_end_ts = mktime(0,0,0, $date_end[MONTH], $date_end[DAY], $date_end[YEAR]);
			$time_to_run = $date_end_ts - $date_start_ts;
			$days_to_run = round($time_to_run / (60 * 60 * 24));
			$date_start = va_date($date_show_format, $date_start);
			$date_added = va_date($datetime_show_format, $date_added);
			$date_updated = va_date($datetime_show_format, $date_updated);
			$t->set_var("date_start", $date_start);
			$t->set_var("days_to_run", $days_to_run);
			$t->set_var("date_added", $date_added);
			$t->set_var("date_updated", $date_updated);

			if ($friendly_urls && $friendly_url) {
				$url = new VA_URL($settings["site_url"].$friendly_url.$friendly_extension);	
			} else {
				$url = new VA_URL($settings["site_url"]."ads_details.php");	
				$url->add_parameter("item_id", REQUEST, "item_id");
			}
			if ($tell_friend_param) {
				$url->add_parameter("friend", CONSTANT, $friend_code);
			}
	
			$t->set_var("item_url", $url->get_url());
	
			break;
		case "articles":
			// Select global settings
			// Get article category
			$category_id = "";
			$top_id = "";
			$sql  = " SELECT category_id FROM " . $table_prefix . "articles_assigned ";
			$sql .= " WHERE article_id=" . $db->tosql($r->get_value("item_id"), INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$category_id = $db->f("category_id");
			}
			// retrieve info about current category
			$sql  = " SELECT  category_path, parent_category_id ";
			$sql .= " FROM " . $table_prefix . "articles_categories WHERE category_id = " . $db->tosql($category_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$parent_category_id = $db->f("parent_category_id");
				if ($parent_category_id == 0) {
					$top_id = $category_id;
				} else {
					$categories_ids = explode(",", $db->f("category_path"));
					$top_id = $categories_ids[1];
				}
			}
			$category_id = $top_id;
	
			$setting_type = "'articles_".$category_id."_tell_friend'";
	
			// Get article info 
			$sql  = " SELECT article_id, friendly_url, article_title, article_date, date_end, author_name, author_email, author_url, short_description, full_description ";
			$sql .= " FROM ". $table_prefix ."articles WHERE article_id=".$db->tosql($r->get_value("item_id"), INTEGER);
			$db->query($sql);
			if (!$db->next_record()){
				$r->errors="Article with such ID no longer exists in database.<br>";
			}
			$t->set_var("article_id", $db->f("article_id"));
			$article_title = get_translation($db->f("article_title"));
			$friendly_url = $db->f("friendly_url");
			$t->set_var("article_title", $article_title);
			$t->set_var("item_title", $article_title);
			$t->set_var("item_name", $article_title);
			$t->set_var("short_description", get_translation($db->f("short_description")));
			$t->set_var("full_description", get_translation($db->f("full_description")));
			$t->set_var("author_name", get_translation($db->f("author_name")));
			$t->set_var("author_email", $db->f("author_email"));
			$t->set_var("author_url", $db->f("author_url"));
			$article_date = $db->f("article_date", DATETIME);
			$date_end = $db->f("date_end", DATETIME);
			$article_date = va_date($datetime_show_format, $article_date);
			$date_end = va_date($datetime_show_format, $date_end);
			$t->set_var("article_date", $article_date);
			$t->set_var("date_end", $date_end);

			if ($friendly_urls && $friendly_url) {
				$url = new VA_URL($settings["site_url"].$friendly_url.$friendly_extension);	
			} else {
				$url = new VA_URL($settings["site_url"]."article.php");	
				$url->add_parameter("article_id", REQUEST, "item_id");
			}
			if ($tell_friend_param) {
				$url->add_parameter("friend", CONSTANT, $friend_code);
			}
	
			$t->set_var("article_url", $url->get_url());
			$t->set_var("item_url", $url->get_url());
	
			break;
		default:
			echo "This is not a product, ad or article.";
			exit;
	}

	// get tell_a_friend settings
	$sql  = " SELECT setting_name, setting_value FROM ". $table_prefix ."global_settings ";
	$sql .= " WHERE setting_type=".$db->tosql($setting_type,TEXT);
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$friend_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$t->set_block("default_comment", get_setting_value($friend_settings, "default_comment", TELL_FRIEND_DEFAULT_MSG));
	$t->parse("default_comment", false);
	$default_comment = get_translation($t->get_var("default_comment"));

	$operation = get_param("operation");
	$message_sent = false;

	if (strlen($operation))
	{
		if ($use_validation && !$r->is_empty("validation_number")) { 
			if (!check_image_validation($r->get_value("validation_number"))) {
				$r->errors .= str_replace("{field_name}", VALIDATION_CODE_FIELD, VALIDATION_MESSAGE);
			}
		}

		$is_valid = $r->validate();
		if ($is_valid)
		{
			$t->show_tags = false;
			$t->set_block("user_comment", $r->get_value("comment"));
			if ($r->is_empty("from_name")) {
				$t->set_var("user_name", $user_nickname);
			} else {
				$t->set_var("user_name", $r->get_value("from_name"));
			}
			$t->set_var("user_email", $r->get_value("from_email"));
			$t->set_var("friend_name", $r->get_value("to_name"));
			$t->set_var("friend_email", $r->get_value("to_email"));
			$t->parse("user_comment", false);
			$t->set_var("user_comment", $t->get_var("user_comment"));

			$mail_message = get_translation(get_setting_value($friend_settings, "user_message", TELL_FRIEND_MESSAGE_MSG));
			$t->set_block("mail_message", $mail_message);
			$t->parse("mail_message", false);

			$mail_message = $t->get_var("mail_message");
			$email_headers = array();
			$email_headers["from"] = get_setting_value($friend_settings, "user_mail_from", $r->get_value("from_email"));
			$email_headers["cc"] = get_setting_value($friend_settings, "user_mail_cc");
			$email_headers["bcc"] = get_setting_value($friend_settings, "user_mail_bcc");
			$email_headers["reply_to"] = get_setting_value($friend_settings, "user_mail_reply_to", $r->get_value("from_email"));
			$email_headers["return_path"] = get_setting_value($friend_settings, "user_mail_return_path");
			$email_headers["mail_type"] = get_setting_value($friend_settings, "user_message_type");
			$mail_subject = get_translation(get_setting_value($friend_settings, "user_subject", TELL_FRIEND_SUBJECT_MSG));

			$t->set_block("email_subject", $mail_subject);
			$t->parse("email_subject", false);
			$mail_subject=$t->get_var("email_subject");
			

			if (va_mail($r->get_value("to_email"), $mail_subject, $mail_message, $email_headers)) {
				$message_sent = true;
				$r->set_value("to_name",    "");
				$r->set_value("to_email",   "");
				$r->set_value("from_name",  "");
				$r->set_value("from_email", "");
				$r->set_value("validation_number", "");
				$r->set_value("comment", $default_comment);
			} else {
				$r->errors = "Sorry there was internal server error while sending email, please try later.<br>";
			}
		}
	}
	else // new record (set default values)
	{
		$r->set_value("comment", $default_comment);
	}

	$r->set_parameters();

	if ($message_sent) {
		$t->parse("message_sent", false);
	} else {
		$t->set_var("message_sent", "");
	}

	$t->pparse("main", false);

?>