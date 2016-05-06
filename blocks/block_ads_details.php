<?php                           

function ads_details($block_name, $category_id)
{
	global $t, $db, $site_id, $table_prefix;
	global $settings, $page_settings, $restrict_ads_images;
	global $datetime_show_format, $date_show_format;
	global $html_title, $meta_description, $currency;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_ads_details.html");

	$eol = get_eol();
	$friendly_urls      = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
		
	$use_tabs = get_setting_value($page_settings, "ads_details_tabs", 0);
	$tab      = get_param("tab");
	if(!strlen($tab)) { $tab = "desc"; }
	
	$item_id = get_param("item_id");		
	$t->set_var("category_id", $category_id);
	
	$tell_friend_href = "tell_friend.php?item_id=" . urlencode($item_id) . "&type=ads";
	
	$t->set_var("ads_href", "ads.php");
	$t->set_var("ads_details_href",    "ads_details.php");
	$t->set_var("tab",                 htmlspecialchars($tab));
	$t->set_var("currency_sign",       ($currency["left"] . $currency["right"]));
	$t->set_var("rnd",                 va_timestamp());
	$t->set_var("tell_friend_href", $tell_friend_href);
	$t->set_var("ads_print_href", "ads_print.php");


	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", "ads.php");

	if (!VA_Ads::check_exists($item_id)) {
		$t->set_var("item", "");
		$t->set_var("NO_AD_MSG", NO_AD_MSG);
		$t->parse("no_item", false);		
		$t->parse("block_body", false);
		$t->parse($block_name, true);
		return;
	}
	
	if (!VA_Ads::check_permissions($item_id, $category_id, VIEW_ITEMS_PERM)) {
		header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
		exit;
	}
	
	$sql  = " SELECT i.item_id, it.type_name, i.item_title, i.friendly_url, i.short_description, i.full_description, ";
	$sql .= " i.image_large, i.price, i.quantity, i.availability, i.is_compared, i.user_id, ";	
	$sql .= " u.login, u.name, u.first_name, u.last_name, u.email, ";
	$sql .= " i.location_city, i.location_postcode, s.state_name, c.country_name, i.location_info, ";
	$sql .= " i.date_start, i.date_end, i.date_added, i.date_updated, i.total_views ";
	$sql .= " FROM ((((" . $table_prefix . "ads_items i ";
	$sql .= " INNER JOIN " . $table_prefix . "ads_types it ON i.type_id= it.type_id)";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON i.user_id=u.user_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "states s ON s.state_id=i.location_state_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "countries c ON c.country_id=i.location_country_id) ";
	$sql .= " WHERE i.item_id = " . $db->tosql($item_id, INTEGER);
	$db->query($sql);
	if($db->next_record())
	{
		$tabs = array("desc");

		$item_id   = $db->f("item_id");
		$type_name = $db->f("type_name");
		$item_title_initial = $db->f("item_title");
		$item_title = get_translation($item_title_initial);
		$html_title = $item_title;
		$friendly_url = $db->f("friendly_url");
		$short_description = get_translation($db->f("short_description"));
		$full_description  = get_translation($db->f("full_description"));
		$availability = get_translation($db->f("availability"));
		$price    = $db->f("price");
		$price_db = $db->f("price");
		$quantity = $db->f("quantity");
		$is_compared = $db->f("is_compared");
		
		$user_id    = $db->f("user_id");
		$name       = $db->f("name");
		$login      = $db->f("login");
		$first_name = $db->f("first_name");
		$last_name  = $db->f("last_name");		
		if (strlen($name)) {
			$user_name = $name;
		} else if (strlen($first_name) || strlen($last_name)) {
			$user_name = $first_name." ".$last_name;
		} else {
			$user_name = $login;
		}
		$seller_id     = $user_id;
		$seller_name   = $user_name;
		$seller_email  = $db->f("email");

		$date_start = $db->f("date_start", DATETIME);
		$date_end = $db->f("date_end", DATETIME);
		$date_start_ts = mktime(0,0,0, $date_start[MONTH], $date_start[DAY], $date_start[YEAR]);
		$date_end_ts = mktime(0,0,0, $date_end[MONTH], $date_end[DAY], $date_end[YEAR]);
		$time_to_run = $date_end_ts - $date_start_ts;
		$days_to_run = round($time_to_run / (60 * 60 * 24));
		$date_added = $db->f("date_added", DATETIME);
		$date_updated = $db->f("date_updated", DATETIME);
		$total_views = $db->f("total_views");

		$location_city = get_translation($db->f("location_city"));
		$location_postcode = $db->f("location_postcode");
		$state_name    = get_translation($db->f("state_name"));
		$country_name  = get_translation($db->f("country_name"));
		$location_info = get_translation($db->f("location_info"));
		$location = $location_city;
		if(strlen($location) && strlen($location_postcode)) {
			$location .= ", ";
		}
		$location .= $location_postcode;
		if(strlen($location) && strlen($state_name)) {
			$location .= ", ";
		}
		$location .= $state_name;
		if(strlen($location) && strlen($country_name)) {
			$location .= ", ";
		}
		$location .= $country_name;
		if(strlen($location) && strlen($location_info)) {
			$location .= ", ";
		}
		$location .= $location_info;
	
		if (!$full_description) { $full_description = $short_description; }
		if (strlen($short_description)) {
			$meta_description = $short_description;
		} else if (strlen($full_description)) {
			$meta_description = $full_description;
		} else {
			$meta_description = $item_title;
		}

		$properties = show_ads_properties($item_id);

		$t->set_var("item_id",    $item_id);
		$t->set_var("item_title", $item_title);
		$t->set_var("type_name", $type_name);
		if (strlen($user_id)) {
			$t->set_var("user_id", htmlspecialchars($user_id));
			$t->set_var("user_name", htmlspecialchars($user_name));
			$t->sparse("user_block", false);
		} else {
			$t->set_var("user_block", "");
		}
		
		$t->set_var("price", currency_format($price));
		if (strlen($availability)) {
			$t->set_var("availability", htmlspecialchars($availability));
			$t->sparse("availability_block", false);
		} else {
			$t->set_var("availability_block", "");
		}
		if (strlen($quantity)) {
			$t->set_var("quantity", htmlspecialchars($quantity));
			$t->global_parse("quantity_block", false, false, true);
		} else {
			$t->set_var("quantity_block", "");
		}
		if (strlen($location)) {
			$t->set_var("location", htmlspecialchars($location));
			$t->sparse("location_block", false);
		} else {
			$t->set_var("location_block", "");
		}

		$big_image = $db->f("image_large");
		if($big_image) {
			if (preg_match("/^http\:\/\//", $big_image)) {
				$image_size = "";
			} else {
				$image_size = @GetImageSize($big_image);
				if (isset($restrict_ads_images) && $restrict_ads_images) { 
					$big_image = "image_show.php?ad_id=".$item_id."&type=large"; 
				}
			}
			$t->set_var("alt", htmlspecialchars($item_title));
			$t->set_var("src", htmlspecialchars($big_image));
			if(is_array($image_size)) {
				$t->set_var("width", "width=\"" . $image_size[0] . "\"");
				$t->set_var("height", "height=\"" . $image_size[1] . "\"");
			} else {
				$t->set_var("width", "");
				$t->set_var("height", "");
			}
			$t->sparse("big_image", false);
		} else {
			$t->set_var("big_image", "");
		}

		$price = $db->f("price");
		$t->set_var("price", currency_format($price));

		// description block
		$t->set_var("description_block", "");
		if (!$use_tabs || $tab == "desc") {
			if ($use_tabs) {
				$t->set_var("title_desc", "");
			} else {
				$t->global_parse("title_desc", false, false, true);
			}

			if($full_description) {
				//$full_description = nl2br(htmlspecialchars($full_description));
				$t->set_var("full_description", $full_description);
				$t->parse("description", false);
			} else {
				$t->set_var("description", "");
			}

			$t->global_parse("description_block", false, false, true);
		}

		// specification details
		$t->set_var("specification", "");
		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "ads_features ";
		$sql .= " WHERE item_id=" . intval($item_id);
		$sql .= " AND feature_value IS NOT NULL ";
		$db->query($sql);
		$db->next_record();
		$total_spec = $db->f(0);
		if ($total_spec > 0) {
			$tabs[] = "spec";
			if (!$use_tabs || $tab == "spec") {
				if ($use_tabs) {
					$t->set_var("title_spec", "");
				} else {
					$t->global_parse("title_spec", false, false, true);
				}

				$sql  = " SELECT fg.group_id,fg.group_name,f.feature_name,f.feature_value ";
				$sql .= " FROM " . $table_prefix . "ads_features f, " . $table_prefix . "ads_features_groups fg ";
				$sql .= " WHERE f.group_id=fg.group_id ";
				$sql .= " AND f.item_id=" . intval($item_id);
				$sql .= " AND feature_value IS NOT NULL ";
				$sql .= " ORDER BY fg.group_order, f.feature_id ";
				$db->query($sql);
				if($db->next_record()) {
					$last_group_id = $db->f("group_id");
					do {
						$group_id = $db->f("group_id");
						$feature_name = $db->f("feature_name");
						$feature_value = $db->f("feature_value");
						if ($group_id != $last_group_id) {
							$t->set_var("group_name", $last_group_name);
							$t->parse("groups", true);
							$t->set_var("features", "");
						}
      
						$t->set_var("feature_name", $feature_name);
						$t->set_var("feature_value", $feature_value);
						$t->parse("features", true);
      
						$last_group_id = $group_id;
						$last_group_name = $db->f("group_name");
					} while ($db->next_record());
					$t->set_var("group_name", $last_group_name);
					$t->parse("groups", true);
					$t->parse("specification", false);
				} 
			}
		}
		// end specification


		// product images 
		$t->set_var("images", "");
		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "ads_images ";
		$sql .= " WHERE item_id=" . intval($item_id);
		$sql .= " AND image_small IS NOT NULL ";
		$db->query($sql);
		$db->next_record();
		$total_images = $db->f(0);
		if ($total_images > 0) {
			$tabs[] = "images";
			if (!$use_tabs || $tab == "images") {
				if ($use_tabs) {
					$t->set_var("title_images", "");
				} else {
					$t->global_parse("title_images", false, false, true);
				}

				$image_number = 0;
				$sql  = " SELECT image_id, image_title, image_small, image_large, image_description  ";
				$sql .= " FROM " . $table_prefix . "ads_images ";
				$sql .= " WHERE item_id=" . intval($item_id);
				$sql .= " AND image_small IS NOT NULL ";
				$db->query($sql);
				while ($db->next_record()) {
					$image_number++;
	    
					$image_id = $db->f("image_id");
					$image_title = $db->f("image_title");
					$image_small = $db->f("image_small");
					$image_large = $db->f("image_large");
					if (isset($restrict_ads_images) && $restrict_ads_images) { 
						if ($image_small && !preg_match("/^http\:\/\//", $image_small)) {
							$image_small = "image_show.php?ad_image_id=".$image_id."&type=small"; 
						}
						if ($image_large && !preg_match("/^http\:\/\//", $image_large)) {
							$image_large = "image_show.php?ad_image_id=".$image_id."&type=large"; 
						}
					}
					if (!strlen($image_large)) {
						$image_large = $image_small;
					}
					$image_description = $db->f("image_description");
      
					$t->set_var("image_title", $image_title);
					$t->set_var("image_small", $image_small);
					$t->set_var("image_width", "");
					$t->set_var("image_height", "");
					$t->set_var("image_large", $image_large);
					$t->set_var("image_description", $image_description);
					$t->parse("images_cols", true);
					if ($image_number % 2 == 0) {
						$t->parse("images_rows", true);
						$t->set_var("images_cols", "");
					}
				}	    
				if ($image_number % 2 != 0) {
					$t->parse("images_rows", true);
				}

				$t->parse("images", false);
			} 
		}
		// end images 

		$t->parse("item");
		$t->set_var("no_item", "");

		// parse tabs
		if ($use_tabs) {
			if ($friendly_urls && $friendly_url) {
				$tab_href = $friendly_url . $friendly_extension . "?category_id=" . urlencode($category_id);
			} else {
				$tab_href = "ads_details.php?category_id=" . urlencode($category_id) . "&item_id=" . urlencode($item_id);
			}

			for ($i = 0; $i < sizeof($tabs); $i++) {
				$tab_name = $tabs[$i];
				$tab_style = ($tab == $tab_name) ? "tabActive" : "tab";
				$t->set_var("tab_href", $tab_href . "&tab=" . $tab_name);
				$t->set_var("tab_style", $tab_style);
				$t->global_parse("tab_" . $tab_name, false, false, true);
			}
			$t->global_parse("tabs", false, false, true);
		} else {
			$t->set_var("tabs", false);
		}

		// update total views for ad
		$ads_viewed = get_session("session_ads_viewed");
		if (!isset($ads_viewed[$item_id])) {
			$sql  = " UPDATE " . $table_prefix . "ads_items SET total_views=" . $db->tosql(($total_views + 1), INTEGER);
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
			$db->query($sql);

			$ads_viewed[$item_id] = true;
			set_session("session_ads_viewed", $ads_viewed);
		}

		// fill in recently viewed ads
		$recent_records = get_setting_value($page_settings, "ads_recent_records", 5);
		$recently_viewed = get_session("session_ads_recently_viewed");
		if(!is_array($recently_viewed)) {
			$recently_viewed = array();
		} 
		$recent_index = 0;
		foreach ($recently_viewed as $key => $recent_info) {
			if($recently_viewed[$key][0] == $item_id) {
				unset($recently_viewed[$key]);
			} else {
				$recent_index++;
				if($recent_index >= $recent_records) {
					unset($recently_viewed[$key]);
				}
			}
		}
		$recent_info = array($item_id, $item_title_initial, $friendly_url, $price_db, $is_compared);
		array_unshift($recently_viewed, $recent_info);
		set_session("session_ads_recently_viewed", $recently_viewed);
	}

	$r = new VA_Record($table_prefix);
	$r->add_textbox("offer_price", NUMBER, OFFER_PRICE_MSG);
	$r->add_textbox("offer_message", TEXT, OFFER_MESSAGE_MSG);
	$r->change_property("offer_message", REQUIRED, true);

	$rnd = get_param("rnd");
	$operation = get_param("operation");
	$session_rnd = get_session("session_rnd");
	if($operation && $rnd != $session_rnd)
	{
		set_session("session_rnd", $rnd);
		$remote_address = get_ip();

		$r->get_form_values();
		$r->validate();

		if(!strlen(get_session("session_user_id"))) {
			$r->errors = AD_OFFER_LOGIN_ERROR;
		}

		if(!$r->errors) {

			// send email notification

			$ads_request = array();
			$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type=" . $db->tosql("ads_request", TEXT);
			if (isset($site_id)) {
				$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
				$sql .= " ORDER BY site_id ASC ";
			} else {
				$sql .= " AND site_id=1 ";
			}
			$db->query($sql);
			while($db->next_record()) {
				$ads_request[$db->f("setting_name")] = $db->f("setting_value");
			}

			$t->set_var("offer_price", currency_format($r->get_value("offer_price")));
			$t->set_var("offer_message", $r->get_value("offer_message"));
			$t->set_var("user_id", get_session("session_user_id"));
			$t->set_var("user_name", get_session("session_user_name"));
			$t->set_var("user_email", get_session("session_user_email"));
			$t->set_var("seller_id", $seller_id);
			$t->set_var("seller_name", $seller_name);

			$date_start = va_date($date_show_format, $date_start);
			$date_added = va_date($datetime_show_format, $date_added);
			$date_updated = va_date($datetime_show_format, $date_updated);
			$t->set_var("date_start", $date_start);
			$t->set_var("days_to_run", $days_to_run);
			$t->set_var("date_added", $date_added);
			$t->set_var("date_updated", $date_updated);
			$t->set_var("type", $type_name);
			$t->set_var("category", get_db_value("SELECT category_name FROM " . $table_prefix . "ads_categories WHERE category_id=" . $db->tosql($category_id, INTEGER)));
			$t->set_var("short_description", $short_description);
			$t->set_var("full_description", $full_description);
			$t->set_var("location_postcode", $location_postcode);
			$t->set_var("location_city", $location_city);
			$t->set_var("location_state", $state_name);
			$t->set_var("location_country", $country_name);
			$t->set_var("location_info", $location_info);

			if($ads_request["admin_notification"])
			{
				$t->set_block("admin_subject", $ads_request["admin_subject"]);
				$t->set_block("admin_message", $ads_request["admin_message"]);
				$t->parse("admin_subject", false);
				$t->parse("admin_message", false);

				$mail_to = get_setting_value($ads_request, "admin_email", $settings["admin_email"]);
				$mail_to = str_replace(";", ",", $mail_to);
				$email_headers = array();
				$email_headers["from"] = get_setting_value($ads_request, "admin_mail_from", $settings["admin_email"]);
				$email_headers["cc"] = get_setting_value($ads_request, "cc_emails");
				$email_headers["bcc"] = get_setting_value($ads_request, "admin_mail_bcc");
				$email_headers["reply_to"] = get_setting_value($ads_request, "admin_mail_reply_to");
				$email_headers["return_path"] = get_setting_value($ads_request, "admin_mail_return_path");
				$email_headers["mail_type"] = get_setting_value($ads_request, "admin_message_type");

				$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
				va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers);
			}		 

			// user notification message
			$t->set_block("user_subject", $ads_request["user_subject"]);
			$t->set_block("user_message", $ads_request["user_message"]);
			$t->parse("user_subject", false);
			$t->parse("user_message", false);

			$mail_to = $seller_email;
			$email_headers = array();
			$email_headers["from"] = get_setting_value($ads_request, "user_mail_from", $settings["admin_email"]);
			$email_headers["cc"] = get_setting_value($ads_request, "user_mail_cc");
			$email_headers["bcc"] = get_setting_value($ads_request, "user_mail_bcc");
			$email_headers["reply_to"] = get_setting_value($ads_request, "user_mail_reply_to");
			$email_headers["return_path"] = get_setting_value($ads_request, "user_mail_return_path");
			$email_headers["mail_type"] = get_setting_value($ads_request, "user_message_type");

			$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
			va_mail($seller_email, $t->get_var("user_subject"), $user_message, $email_headers);

			$r->set_value("offer_price", "");
			$r->set_value("offer_message", "");
		} else {
			set_session("session_rnd", "");
		}
	} 

	$r->set_parameters();

	if(!$r->errors && $operation) {
		$t->parse("request_sent", false);
	}


	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>