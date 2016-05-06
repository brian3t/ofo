<?php

	check_user_security("add_ad");

	$categories_ids = VA_Ads_Categories::find_all_ids("", ADD_ITEMS_PERM);

	if (!$categories_ids) {
		// no categories where user could add new ad
		header("Location: " . get_custom_friendly_url("user_home.php"));
		exit;
	}
	
	// get user type settings
	$type_id = "";
	$item_id = get_param("item_id");
	if (strlen($item_id)) {
		$sql  = " SELECT t.type_id, t.type_name FROM " . $table_prefix . "ads_items i, " . $table_prefix . "ads_types t ";
		$sql .= " WHERE i.type_id=t.type_id ";
		$sql .= " AND i.item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$type_id = $db->f("type_id");
			$type_name = get_translation($db->f("type_name"));
		}
	} else {
		$type_id = get_param("type_id");
		$db->query("SELECT type_id, type_name FROM " . $table_prefix . "ads_types WHERE type_id=" . $db->tosql($type_id, INTEGER));
		if($db->next_record()) {
			$type_id = $db->f("type_id");
			$type_name = get_translation($db->f("type_name"));
		} else {
			$type_id = "";
		}
	}

	if (!strlen($type_id)) {
		header("Location: " . get_custom_friendly_url("user_ads.php"));
		exit;
	}

	$eol = get_eol();

	$user_id = get_session("session_user_id");
	$user_settings = array();
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "user_types_settings WHERE type_id=" . $db->tosql(get_session("session_user_type_id"), INTEGER);
	$db->query($sql);
	while($db->next_record()) {
		$user_settings[$db->f("setting_name")] = $db->f("setting_value");
	}


	$ads_settings = array();
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type=" . $db->tosql("ads", TEXT);
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while($db->next_record()) {
		$ads_settings[$db->f("setting_name")] = $db->f("setting_value");
	}
	$hot_offer = get_setting_value($ads_settings, "hot_offer", 0);
	$special_offer = get_setting_value($ads_settings, "special_offer", 0);

	$date_format_msg = str_replace("{date_format}", join("", $date_edit_format), DATE_FORMAT_MSG);

	$t->set_file("block_body","block_user_ad.html");
	$t->set_var("site_url",        $settings["site_url"]);
	$t->set_var("user_home_href",  get_custom_friendly_url("user_home.php"));
	$t->set_var("user_ads_href",   get_custom_friendly_url("user_ads.php"));
	$t->set_var("user_ad_href",    get_custom_friendly_url("user_ad.php"));
	$t->set_var("user_upload_href",get_custom_friendly_url("user_upload.php"));
	$t->set_var("user_select_href",get_custom_friendly_url("user_select.php"));
	$t->set_var("type_name",       $type_name);
	$t->set_var("currency_sign",   $currency["left"].$currency["right"]);
	$t->set_var("currency_left",   $currency["left"]);
	$t->set_var("currency_right",  $currency["right"]);
	$t->set_var("currency_rate",   $currency["rate"]);
	$t->set_var("currency_decimals",   $currency["decimals"]);
	$t->set_var("currency_point",   $currency["point"]);
	$t->set_var("currency_separator",   $currency["separator"]);

	$t->set_var("date_edit_format",join("", $date_edit_format));
	$t->set_var("date_format_msg", $date_format_msg);
	$t->set_var("AD_DATE_FORMAT_MSG",    $date_format_msg);

	$days_list = VA_Ads::get_days_list($table_prefix . "ads_days");

	$item_types = get_db_values("SELECT * FROM " . $table_prefix . "ads_types", array(array("", "")));
	$states     = get_db_values("SELECT state_id,state_name FROM " . $table_prefix . "states WHERE show_for_user=1 ORDER BY state_name ", array(array("", SELECT_STATE_MSG)));
	$countries  = get_db_values("SELECT country_id,country_name FROM " . $table_prefix . "countries WHERE show_for_user=1 ORDER BY country_order, country_name ", array(array("", SELECT_COUNTRY_MSG)));
	
	// prepare categories options
	$categories = array();
	$sql  = " SELECT category_id, category_name, category_path, publish_price ";
	$sql .= " FROM " . $table_prefix . "ads_categories ";
	$sql .= " WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")";
	$sql .= " ORDER BY category_path, category_order ";
	
	$db->query($sql);
	while ($db->next_record()) {
		$category_id = $db->f("category_id");
		$category_name = get_translation($db->f("category_name"));
		$category_path = $db->f("category_path");
		$publish_price = $db->f("publish_price");
		$categories[$category_id] = array($category_name, $category_path, $publish_price);
	}

	$categories_options = array();
	$categories_options[] = array("", SELECT_CATEGORY_MSG);
	foreach ($categories as $category_id => $category_info) {
		$category_name = $category_info[0];
		$category_path = $category_info[1];
		$publish_price = $category_info[2];
		if ($category_path != "0,") {
			$categories_ids = explode(",", $category_path);
			$max_index = sizeof($categories_ids) - 2;
			for($i = $max_index; $i > 0; $i--) {
				$parent_id = $categories_ids[$i];
				if(isset($categories[$parent_id])) {
					$parent_name = $categories[$parent_id][0];
				} else {
					$sql = " SELECT category_name FROM " . $table_prefix . "ads_categories WHERE category_id=" . $db->tosql($parent_id, INTEGER);
					$parent_name = get_translation(get_db_value($sql));
					$categories[$parent_id] = array($parent_name);
				}
				$category_name = $parent_name . " > " . $category_name;
			}
		}
		if ($publish_price > 0) {
			$category_name .= " (".currency_format($publish_price).")";
		}
		$categories_options[] = array($category_id, $category_name);
	}

	$r = new VA_Record($table_prefix . "ads_items");

	// set up html form parameters
	$r->add_where("item_id", INTEGER);
	$r->change_property("item_id", USE_IN_INSERT, true);

	$r->add_where("user_id", INTEGER);
	$r->change_property("user_id", USE_IN_INSERT, true);

	$r->add_checkbox("is_shown", INTEGER);
	$r->add_checkbox("is_shown_internal", INTEGER);
	$r->add_select("category_id", INTEGER, $categories_options, CATEGORY_MSG);
	$r->change_property("category_id", REQUIRED, true);
	$r->change_property("category_id", USE_IN_INSERT, false);
	$r->change_property("category_id", USE_IN_UPDATE, false);
	$r->change_property("category_id", USE_IN_SELECT, false);
	$r->change_property("category_id", AFTER_VALIDATE, "check_category_post");


	$r->add_textbox("is_approved", INTEGER);
	$r->add_textbox("type_id",     INTEGER);
	$r->change_property("type_id", REQUIRED, true);
	$r->change_property("type_id", USE_IN_UPDATE, false);

	$r->add_textbox("language_code", TEXT);
	$r->change_property("language_code", USE_SQL_NULL, false);
	$r->add_textbox("item_title", TEXT, TITLE_MSG);
	$r->parameters["item_title"][REQUIRED] = true;

	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);

	$r->add_textbox("admin_id", INTEGER);

	$r->add_textbox("date_start",     DATETIME, AD_START_MSG);
	$r->change_property("date_start", REQUIRED, true);
	$r->change_property("date_start", VALUE_MASK, $date_edit_format);
	$r->add_select("days_run", INTEGER, $days_list, AD_RUNS_MSG);
	$r->change_property("days_run", REQUIRED, true);

	$r->add_textbox("date_end",     DATETIME);
	$r->change_property("date_end", VALUE_MASK, $date_edit_format);
	$r->add_textbox("date_added",   DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_updated", DATETIME);

	$r->add_textbox("price", NUMBER, PRICE_MSG);
	$r->parameters["price"][REQUIRED] = true;
	$r->add_textbox("quantity", NUMBER, AD_QTY_MSG);
	$r->add_textbox("availability", TEXT);
	$r->add_checkbox("is_compared", INTEGER);

	// images
	$r->add_textbox("image_small", TEXT);
	$r->add_textbox("image_large", TEXT);

	// description
	$r->add_textbox("short_description", TEXT);
	$r->add_textbox("full_description", TEXT);

	// location
	$r->add_textbox("location_info", TEXT);
	$r->add_textbox("location_city", TEXT);
	$r->add_textbox("location_postcode", ZIP_FIELD);
	$r->change_property("location_postcode", USE_SQL_NULL, false);
	$r->add_select("location_state_id", INTEGER, $states, STATE_FIELD);
	$r->change_property("location_state_id", USE_SQL_NULL, false);
	$r->add_select("location_country_id", INTEGER, $countries, COUNTRY_FIELD);
	$r->change_property("location_country_id", USE_SQL_NULL, false);

	// hot offer fields
	$hot_days_list = VA_Ads::get_days_list($table_prefix . "ads_hot_days");
	$r->add_checkbox("is_hot", INTEGER);
	$r->add_textbox("hot_date_start", DATETIME, AD_HOT_START_MSG);
	$r->change_property("hot_date_start", VALUE_MASK, $date_edit_format);
	$r->add_select("hot_days_run", INTEGER, $hot_days_list, ADS_HOT_DAYS_MSG);
	$r->add_textbox("hot_date_end", DATETIME);
	$r->change_property("hot_date_end", VALUE_MASK, $date_edit_format);
	$r->add_textbox("hot_description", TEXT);
	if (!$hot_offer) {
		$r->change_property("is_hot", USE_IN_UPDATE, false);
		$r->change_property("hot_date_start", USE_IN_UPDATE, false);
		$r->change_property("hot_days_run", USE_IN_UPDATE, false);
		$r->change_property("hot_description", USE_IN_UPDATE, false);
	}


	// special offer fields
	$special_days_list = VA_Ads::get_days_list($table_prefix . "ads_special_days");
	$r->add_checkbox("is_special", INTEGER);
	$r->add_textbox("special_date_start", DATETIME, AD_SPECIAL_START_MSG);
	$r->change_property("special_date_start", VALUE_MASK, $date_edit_format);
	$r->add_select("special_days_run", INTEGER, $special_days_list, ADS_SPECIAL_DAYS_MSG);
	$r->add_textbox("special_date_end", DATETIME);
	$r->change_property("special_date_end", VALUE_MASK, $date_edit_format);
	$r->add_textbox("special_description", TEXT);
	if (!$special_offer) {
		$r->change_property("is_special", USE_IN_UPDATE, false);
		$r->change_property("special_date_start", USE_IN_UPDATE, false);
		$r->change_property("special_days_run", USE_IN_UPDATE, false);
		$r->change_property("special_description", USE_IN_UPDATE, false);
	}

	// hidden fields for current record
	$r->add_hidden("current_date_start", DATETIME);
	$r->change_property("current_date_start", VALUE_MASK, $date_edit_format);
	$r->add_hidden("current_days_run", INTEGER);
	$r->add_hidden("current_hot_date_start", DATETIME);
	$r->change_property("current_hot_date_start", VALUE_MASK, $date_edit_format);
	$r->add_hidden("current_hot_days_run", INTEGER);
	$r->add_hidden("current_special_date_start", DATETIME);
	$r->change_property("current_special_date_start", VALUE_MASK, $date_edit_format);
	$r->add_hidden("current_special_days_run", INTEGER);


	$r->get_form_values();
	$r->set_value("user_id", get_session("session_user_id"));

	$item_id = get_param("item_id");
	$operation = get_param("operation");
	$current_tab = get_param("current_tab");
	if (!$current_tab) { $current_tab = "general"; }
	$return_page = get_custom_friendly_url("user_ads.php");
	$properties = array();
	$features = array();
	$images = array();

	if(strlen($operation))
	{
		$current_tab = "general";
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		else if($operation == "delete" && $item_id)
		{
			if (!isset($user_settings["delete_ad"]) || $user_settings["delete_ad"] != 1) {
				$r->errors = AD_DELETE_ERROR;
			} else {
				// ads tables with item_id: 
				$db->query("DELETE FROM " . $table_prefix . "ads_properties WHERE item_id=" . $db->tosql($item_id, INTEGER));		
				$db->query("DELETE FROM " . $table_prefix . "ads_assigned WHERE item_id=" . $db->tosql($item_id, INTEGER));		
				$db->query("DELETE FROM " . $table_prefix . "ads_features WHERE item_id=" . $db->tosql($item_id, INTEGER));		
				$db->query("DELETE FROM " . $table_prefix . "ads_images WHERE item_id=" . $db->tosql($item_id, INTEGER));		
				$db->query("DELETE FROM " . $table_prefix . "ads_items WHERE item_id=" . $db->tosql($item_id, INTEGER));		
		  
				header("Location: " . $return_page);
				exit;
			} 
		} else if ($operation == "save") {
			$r->set_value("is_shown_internal", $r->get_value("is_shown")); // always update internal field when the record updates

			if ($r->get_value("is_hot") || $r->get_value("hot_days_run")) {
				$r->change_property("hot_days_run", REQUIRED, true);
				$r->change_property("hot_date_start", REQUIRED, true);
			}
			if ($r->get_value("is_special")  || $r->get_value("special_days_run")) {
				$r->change_property("special_days_run", REQUIRED, true);
				$r->change_property("special_date_start", REQUIRED, true);
			}

			$is_valid = $r->validate();
			if(strlen($item_id)) {
				if (!isset($user_settings["edit_ad"]) || $user_settings["edit_ad"] != 1) {
					$is_valid = false;
					$r->errors = AD_EDIT_ERROR;
				}
			} else {
				if (!isset($user_settings["add_ad"]) || $user_settings["add_ad"] != 1) {
					$is_valid = false;
					$r->errors = AD_NEW_ERROR;
				}
			} 

			$publish_price = 0;
			if ($is_valid) {
				$category_id = $r->get_value("category_id");
				$category_info = $categories[$category_id];
				$category_price = $category_info[2];
				if ($category_price > 0) {
					$publish_price += $category_price;
				}
				$list_price = VA_Ads::get_list_price($days_list, $r->get_value("days_run"));
				if ($list_price > 0) {
					$publish_price += $list_price;
				}

				$is_hot = $r->get_value("is_hot");
				if ($is_hot) {
					$list_price = VA_Ads::get_list_price($hot_days_list, $r->get_value("hot_days_run"));
					if ($list_price > 0) {
						$publish_price += $list_price;
					}
				}

				$is_special = $r->get_value("is_special");
				if ($is_special) {
					$list_price = VA_Ads::get_list_price($special_days_list, $r->get_value("special_days_run"));
					if ($list_price > 0) {
						$publish_price += $list_price;
					}
				}
				if ($publish_price > 0) {
					// check user credit balance
					$sql = " SELECT credit_balance FROM " . $table_prefix . "users WHERE user_id=" . $db->tosql($user_id, INTEGER);
					$credit_balance = get_db_value($sql);
					if ($publish_price > $credit_balance) {
						$is_valid = false;
						$r->errors = str_replace("{more_credits}", currency_format($publish_price - $credit_balance), AD_CREDITS_BALANCE_ERROR);
					}
				}				
			}
			
			$properties_number = get_param("properties_number");
			$features_number = get_param("features_number");
			$images_number = get_param("images_number");
			if($is_valid)
			{
				// subtract credits before adding/updating ad
				if (strlen($item_id)) {
					$post_price = VA_Ads::credits($r, true, true);
				} else {
					$post_price = VA_Ads::credits($r, false, true);
				}
	
				set_friendly_url();
				// set end date for ad
				$date_start = $r->get_value("date_start");
				$days_run = $r->get_value("days_run");
				$sql = " SELECT days_number FROM " . $table_prefix . "ads_days WHERE days_id=" . $db->tosql($days_run, INTEGER);
				$days_number = get_db_value($sql);
				$date_end_ts = mktime(0,0,0, $date_start[MONTH], $date_start[DAY] + $days_number, $date_start[YEAR]);
				$r->set_value("date_end", va_time($date_end_ts));
				// set end date for hot ad
				$hot_days_run = $r->get_value("hot_days_run");
				if ($hot_days_run) {
					$sql = " SELECT days_number FROM " . $table_prefix . "ads_hot_days WHERE days_id=" . $db->tosql($hot_days_run, INTEGER);
					$hot_days_number = get_db_value($sql);
					$hot_date_start = $r->get_value("hot_date_start");
					$hot_date_end_ts = mktime(0,0,0, $hot_date_start[MONTH], $hot_date_start[DAY] + $hot_days_number, $hot_date_start[YEAR]);
					$r->set_value("hot_date_end", va_time($hot_date_end_ts));
				}
				// set end date for special ad
				$special_days_run = $r->get_value("special_days_run");
				if ($special_days_run) {
					$sql = " SELECT days_number FROM " . $table_prefix . "ads_special_days WHERE days_id=" . $db->tosql($special_days_run, INTEGER);
					$special_days_number = get_db_value($sql);
					$special_date_start = $r->get_value("special_date_start");
					$special_date_end_ts = mktime(0,0,0, $special_date_start[MONTH], $special_date_start[DAY] + $special_days_number, $special_date_start[YEAR]);
					$r->set_value("special_date_end", va_time($special_date_end_ts));
				}

				$price = $r->get_value("price");
				$price = round($price / $currency["rate"], 2); 
				$r->set_value("price", $price);
	  
				$is_approved = (isset($user_settings["approve_ad"]) && $user_settings["approve_ad"] == 1) ? 1 : 0;
				$r->set_value("is_approved", $is_approved);
					  
				if (strlen($item_id)) {
					$r->set_value("date_updated", va_time());
					$record_updated = $r->update_record();
					if ($record_updated) {
						$sql  = " DELETE FROM " . $table_prefix . "ads_assigned ";
						$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
						$db->query($sql);
						$sql  = " INSERT INTO " . $table_prefix . "ads_assigned (item_id,category_id) VALUES (";
						$sql .= $db->tosql($item_id, INTEGER) . ",";
						$sql .= $db->tosql($r->get_value("category_id"), INTEGER) . ")";
						$db->query($sql);
						for ($i = 1; $i <= $properties_number; $i++) {
							$property_id = get_param("property_id_" . $i);
							$property_value = get_param("property_value_" . $i);
							$sql  = " UPDATE " . $table_prefix . "ads_properties ";
							$sql .= " SET property_value=" . $db->tosql($property_value, TEXT);
							$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
							$sql .= " AND property_id=" . $db->tosql($property_id, INTEGER);
							$db->query($sql);
						}
						for ($i = 1; $i <= $features_number; $i++) {
							$feature_id = get_param("feature_id_" . $i);
							$feature_value = get_param("feature_value_" . $i);
							$sql  = " UPDATE " . $table_prefix . "ads_features ";
							$sql .= " SET feature_value=" . $db->tosql($feature_value, TEXT);
							$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
							$sql .= " AND feature_id=" . $db->tosql($feature_id, INTEGER);
							$db->query($sql);
						}
						for ($i = 1; $i <= $images_number; $i++) {
							$image_id = get_param("image_id_" . $i);
							$picture_small = get_param("image_small_" . $i);
							$picture_large = get_param("image_large_" . $i);
							$image_title = get_param("image_title_" . $i);
							$image_description = get_param("image_description_" . $i);
							$sql  = " UPDATE " . $table_prefix . "ads_images SET ";
							$sql .= " image_small=" . $db->tosql($picture_small, TEXT) . ",";
							$sql .= " image_large=" . $db->tosql($picture_large, TEXT) . ",";
							$sql .= " image_title=" . $db->tosql($image_title, TEXT) . ",";
							$sql .= " image_description=" . $db->tosql($image_description, TEXT);
							$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
							$sql .= " AND image_id=" . $db->tosql($image_id, INTEGER);
							$db->query($sql);
						}
	        }
				} else {
					$r->set_value("date_added", va_time());
					$r->set_value("date_updated", va_time());
					$db->query("SELECT MAX(item_id) FROM " . $table_prefix . "ads_items");
					$db->next_record();
					$item_id = $db->f(0) + 1;
					$r->set_value("item_id", $item_id);
					$record_updated = $r->insert_record();
					if ($record_updated) {
						$sql  = " INSERT INTO " . $table_prefix . "ads_assigned (item_id,category_id) VALUES (";
						$sql .= $db->tosql($item_id, INTEGER) . ",";
						$sql .= $db->tosql($r->get_value("category_id"), INTEGER) . ")";
						$db->query($sql);
						for ($i = 1; $i <= $properties_number; $i++) {
							$property_name = get_param("property_name_" . $i);
							$property_value = get_param("property_value_" . $i);
							$sql  = " INSERT INTO " . $table_prefix . "ads_properties ";
							$sql .= " (item_id, property_name, property_value) VALUES (";
							$sql .= $db->tosql($item_id, INTEGER) . ", ";
							$sql .= $db->tosql($property_name, TEXT) . ", ";
							$sql .= $db->tosql($property_value, TEXT) . ") ";
							$db->query($sql);
						}
	        
						for ($i = 1; $i <= $features_number; $i++) {
							$group_id = get_param("group_id_" . $i);
							$feature_name = get_param("feature_name_" . $i);
							$feature_value = get_param("feature_value_" . $i);
							$sql  = " INSERT INTO " . $table_prefix . "ads_features ";
							$sql .= " (item_id, group_id, feature_name, feature_value) VALUES (";
							$sql .= $db->tosql($item_id, INTEGER) . ", ";
							$sql .= $db->tosql($group_id, INTEGER) . ", ";
							$sql .= $db->tosql($feature_name, TEXT) . ", ";
							$sql .= $db->tosql($feature_value, TEXT) . ") ";
							$db->query($sql);
						}
						for ($i = 1; $i <= $images_number; $i++) {
							$picture_small = get_param("image_small_" . $i);
							$picture_large = get_param("image_large_" . $i);
							$image_title = get_param("image_title_" . $i);
							$image_description = get_param("image_description_" . $i);
							$sql  = " INSERT INTO " . $table_prefix . "ads_images ";
							$sql .= " (item_id, image_small,image_large, image_title, image_description) VALUES (";
							$sql .= $db->tosql($item_id, TEXT) . ",";
							$sql .= $db->tosql($picture_small, TEXT) . ",";
							$sql .= $db->tosql($picture_large, TEXT) . ",";
							$sql .= $db->tosql($image_title, TEXT) . ",";
							$sql .= $db->tosql($image_description, TEXT) . ")";
							$db->query($sql);
						}
					} else {
						$item_id = "";
						$r->set_value("item_id", "");
					}
				}

				if ($record_updated) {
					$ads_notify = array();
					$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
					$sql .= " WHERE setting_type=" . $db->tosql("ads_notify", TEXT);
					if (isset($site_id)) {
						$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
						$sql .= " ORDER BY site_id ASC ";
					} else {
						$sql .= " AND site_id=1 ";
					}
					$db->query($sql);
					while($db->next_record()) {
						$ads_notify[$db->f("setting_name")] = $db->f("setting_value");
					}
			  
					if($ads_notify["admin_notification"] || $ads_notify["user_notification"]) {
						$r->set_parameters();
						$date_start = va_date($date_show_format, $r->get_value("date_start"));
						$days_run = $r->get_value("days_run");
						$sql = "SELECT date_added FROM " . $table_prefix . "ads_items WHERE item_id=" . $db->tosql($item_id, INTEGER);
						$db->query($sql);
						if ($db->next_record()) {
							$date_added = $db->f("date_added", DATETIME);
						}
						$date_added = va_date($datetime_show_format, $date_added);
						$date_updated = va_date($datetime_show_format, $r->get_value("date_updated"));
						$t->set_var("date_start", $date_start);
						$t->set_var("days_run", $days_run);
						$t->set_var("date_added", $date_added);
						$t->set_var("date_updated", $date_updated);
						$t->set_var("seller_id", get_session("session_user_id"));
						$t->set_var("seller_name", get_session("session_user_name"));
						$t->set_var("type", $type_name);
						$t->set_var("category", get_db_value("SELECT category_name FROM " . $table_prefix . "ads_categories WHERE category_id=" . $db->tosql($r->get_value("category_id"), INTEGER)));
						$t->set_var("location_state", get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("location_state_id"), INTEGER)));
						$t->set_var("location_country", get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("location_country_id"), INTEGER)));
					}
			  
					if($ads_notify["admin_notification"])
					{
						$t->set_block("admin_subject", $ads_notify["admin_subject"]);
						$t->set_block("admin_message", $ads_notify["admin_message"]);
						$t->parse("admin_subject", false);
						$t->parse("admin_message", false);
			  
						$mail_to = get_setting_value($ads_notify, "admin_email", $settings["admin_email"]);
						$mail_to = str_replace(";", ",", $mail_to);
						$email_headers = array();
						$email_headers["from"] = get_setting_value($ads_notify, "admin_mail_from", $settings["admin_email"]);
						$email_headers["cc"] = get_setting_value($ads_notify, "cc_emails");
						$email_headers["bcc"] = get_setting_value($ads_notify, "admin_mail_bcc");
						$email_headers["reply_to"] = get_setting_value($ads_notify, "admin_mail_reply_to");
						$email_headers["return_path"] = get_setting_value($ads_notify, "admin_mail_return_path");
						$email_headers["mail_type"] = get_setting_value($ads_notify, "admin_message_type");
			  
						$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
						va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers);
					}		 
					if($ads_notify["user_notification"])
					{
						$t->set_block("user_subject", $ads_notify["user_subject"]);
						$t->set_block("user_message", $ads_notify["user_message"]);
						$t->parse("user_subject", false);
						$t->parse("user_message", false);
			  
			      $mail_to = get_session("session_user_email");
						$email_headers = array();
						$email_headers["from"] = get_setting_value($ads_notify, "user_mail_from", $settings["admin_email"]);
						$email_headers["cc"] = get_setting_value($ads_notify, "user_mail_cc");
						$email_headers["bcc"] = get_setting_value($ads_notify, "user_mail_bcc");
						$email_headers["reply_to"] = get_setting_value($ads_notify, "user_mail_reply_to");
						$email_headers["return_path"] = get_setting_value($ads_notify, "user_mail_return_path");
						$email_headers["mail_type"] = get_setting_value($ads_notify, "user_message_type");
			  
						$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
						va_mail($mail_to, $t->get_var("user_subject"), $user_message, $email_headers);
					}		 
			  
	      
					header("Location: " . $return_page);
					exit;
				}

			} else {
				for ($i = 1; $i <= $properties_number; $i++) {
					$property_id = get_param("property_id_" . $i);
					$property_name = get_param("property_name_" . $i);
					$property_value = get_param("property_value_" . $i);
					$property_values = get_param("property_values_" . $i);
					$properties[] = array($property_id, $property_name, $property_value, $property_values);
				}
				for ($i = 1; $i <= $features_number; $i++) {
					$feature_id = get_param("feature_id_" . $i);
					$group_id = get_param("group_id_" . $i);
					$group_name = get_param("group_name_" . $i);
					$feature_name = get_param("feature_name_" . $i);
					$feature_value = get_param("feature_value_" . $i);
					$feature_values = get_param("feature_values_" . $i);
					$features[] = array($feature_id, $group_id, $group_name, $feature_name, $feature_value, $feature_values);
				}
				for ($i = 1; $i <= $images_number; $i++) {
					$image_id = get_param("image_id_" . $i);
					$picture_small = get_param("image_small_" . $i);
					$picture_large = get_param("image_large_" . $i);
					$image_title = get_param("image_title_" . $i);
					$image_description = get_param("image_description_" . $i);
					$images[] = array($image_id, $image_title, $picture_small, $picture_large, $image_description);
				}
			}
		}
	}
	else if(strlen($item_id))
	{
		$r->get_db_values();
		$date_start = $r->get_value("date_start");
		$date_end = $r->get_value("date_end");
		$date_start_ts = mktime(0,0,0, $date_start[MONTH], $date_start[DAY], $date_start[YEAR]);
		$date_end_ts = mktime(0,0,0, $date_end[MONTH], $date_end[DAY], $date_end[YEAR]);
		$price = $r->get_value("price");
		$price = round($price * $currency["rate"], 2); 
		$r->set_value("price", $price);

		$r->set_value("current_date_start", $r->get_value("date_start"));
		$r->set_value("current_days_run", $r->get_value("days_run"));
		$r->set_value("current_hot_date_start", $r->get_value("hot_date_start"));
		$r->set_value("current_hot_days_run", $r->get_value("hot_days_run"));
		$r->set_value("current_special_date_start", $r->get_value("special_date_start"));
		$r->set_value("current_special_days_run", $r->get_value("special_days_run"));

		$sql  = " SELECT ac.category_id FROM (";
		if(isset($site_id)) {
			$sql .= "(";
		}
		$sql .= $table_prefix . "ads_assigned ac ";
		$sql .= " LEFT JOIN " . $table_prefix . "ads_categories c ON ac.category_id = c.category_id)";
		if(isset($site_id)) {
			$sql .= " LEFT JOIN " . $table_prefix . "ads_categories_sites s ON s.category_id=c.category_id)";
			$sql .= " WHERE (c.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		} else {
			$sql .= " WHERE c.sites_all=1";
		}
		$sql .= " AND ac.item_id=" . $db->tosql($item_id, INTEGER);	
		$category_id = get_db_value($sql);
		if (!$category_id) {
			header ("Location: " . get_custom_friendly_url("user_ads.php"));
			exit;
		}

		$r->set_value("category_id", $category_id);
		// get properties
		$sql  = " SELECT * FROM " . $table_prefix . "ads_properties ";
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$sql .= " ORDER BY property_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$property_id = $db->f("property_id");
			$property_name = $db->f("property_name");
			$property_value = $db->f("property_value");
			$properties[] = array($property_id, $property_name, $property_value, "");
		}
		for($i = 0; $i < sizeof($properties); $i++) {
			$property_name = $properties[$i][1];
			$sql  = " SELECT property_value FROM " . $table_prefix . "ads_properties_default ";
			$sql .= " WHERE type_id=" . $db->tosql($type_id, INTEGER);
			$sql .= " AND property_name=" . $db->tosql($property_name, TEXT);
			$properties[$i][3] = get_db_value($sql);
		}
		// get features 
		$sql  = " SELECT f.feature_id, f.group_id, fg.group_name, f.feature_name, f.feature_value ";
		$sql .= " FROM " . $table_prefix . "ads_features f, " . $table_prefix . "ads_features_groups fg ";;
		$sql .= " WHERE f.group_id=fg.group_id ";
		$sql .= " AND item_id=" . $db->tosql($item_id, INTEGER);
		$sql .= " ORDER BY fg.group_order, f.feature_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$feature_id = $db->f("feature_id");
			$group_id = $db->f("group_id");
			$group_name = $db->f("group_name");
			$feature_name = $db->f("feature_name");
			$feature_value = $db->f("feature_value");
			$features[] = array($feature_id, $group_id, $group_name, $feature_name, $feature_value, "");
		}
		for($i = 0; $i < sizeof($features); $i++) {
			$group_id = $features[$i][1];
			$feature_name = $features[$i][3];
			$sql  = " SELECT feature_value FROM " . $table_prefix . "ads_features_default ";
			$sql .= " WHERE type_id=" . $db->tosql($type_id, INTEGER);
			$sql .= " AND group_id=" . $db->tosql($group_id, INTEGER);
			$sql .= " AND feature_name=" . $db->tosql($feature_name, TEXT);
			$features[$i][5] = get_db_value($sql);
		}
		// get images 
		$sql  = " SELECT image_id,image_small,image_large,image_title,image_description ";
		$sql .= " FROM " . $table_prefix . "ads_images  ";
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$sql .= " ORDER BY item_id  ";
		$db->query($sql);
		while ($db->next_record()) {
			$image_id = $db->f("image_id");
			$picture_small = $db->f("image_small");
			$picture_large = $db->f("image_large");
			$image_title = $db->f("image_title");
			$image_description = $db->f("image_description");
			$images[] = array($image_id, $image_title, $picture_small, $picture_large, $image_description);
		}
	}
	else // new record (set default values)
	{
		$r->set_value("is_shown", 1);
		$r->set_value("date_start", va_time());
		// get default properties
		$sql  = " SELECT * FROM " . $table_prefix . "ads_properties_default ";
		$sql .= " WHERE type_id=" . $db->tosql($type_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$property_name = $db->f("property_name");
			$property_values = $db->f("property_value");
			$properties[] = array("", $property_name, "", $property_values);
		}
		// get default features
		$sql  = " SELECT fd.group_id, fg.group_name, fd.feature_name, fd.feature_value ";
		$sql .= " FROM " . $table_prefix . "ads_features_default fd ";
		$sql .= " , " . $table_prefix . "ads_features_groups fg ";
		$sql .= " WHERE fd.group_id=fg.group_id ";
		$sql .= " AND fd.type_id=" . $db->tosql($type_id, INTEGER);
		$sql .= " ORDER BY fg.group_order, fd.feature_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$group_id = $db->f("group_id");
			$group_name = $db->f("group_name");
			$feature_name = $db->f("feature_name");
			$feature_values = $db->f("feature_value");
			$features[] = array("", $group_id, $group_name, $feature_name, "", $feature_values);
		}
		// get default images
		$images_number = get_setting_value($ads_settings, "images_number", 4);
		for($i = 0; $i < $images_number; $i++) {
			$images[] = array("", "", "", "", "");
		}


	}

	$r->set_form_parameters();

	// set properties
	if (sizeof($properties) > 0) {
		$property_index = 0;
		for ($i = 0; $i < sizeof($properties); $i++) {
			$property_index++;
			$property_id = $properties[$i][0];
			$property_name = $properties[$i][1];
			$property_value = $properties[$i][2];
			$property_values = $properties[$i][3];

			$default_values  = array();
			if (strlen(trim($property_values))) {
				$default_values[] = array("", "");
				$property_values_array = explode("\n", $property_values);
				for ($j = 0; $j < sizeof($property_values_array); $j++) {
					if (strlen(trim($property_values_array[$j]))) {
						$default_values[] = array($property_values_array[$j], $property_values_array[$j]);
					}
				}
			}
			$t->set_var("property_index", htmlspecialchars($property_index));
			$t->set_var("property_id", htmlspecialchars($property_id));
			$t->set_var("property_name", htmlspecialchars($property_name));
			$t->set_var("property_values", htmlspecialchars($property_values));
			$t->set_var("property_select", "");
			$t->set_var("property_textbox", "");
			if (sizeof($default_values) > 0) {
				set_options($default_values, $property_value, "property_value_list");
				$t->parse("property_select", false);
			} else {
				$t->set_var("property_value", htmlspecialchars($property_value));
				$t->parse("property_textbox", false);
			}

			$t->parse("properties", true);
		}

		$t->set_var("properties_number", sizeof($properties));
		$t->parse("properties_block", false);
	} else {
		$t->set_var("properties_block", "");
	}

	// set features 
	if (sizeof($features) > 0) {
		$feature_index = 0;
		for ($i = 0; $i < sizeof($features); $i++) {
			$feature_index++;
			$feature_id = $features[$i][0];
			$group_id = $features[$i][1];
			$group_name = $features[$i][2];
			$feature_name = $features[$i][3];
			$feature_value = $features[$i][4];
			$feature_values = $features[$i][5];

			$default_values  = array();
			if (strlen(trim($feature_values))) {
				$default_values[] = array("", "");
				$feature_values_array = explode("\n", $feature_values);
				for ($j = 0; $j < sizeof($feature_values_array); $j++) {
					if (strlen(trim($feature_values_array[$j]))) {
						$default_values[] = array($feature_values_array[$j], $feature_values_array[$j]);
					}
				}
			}
			$t->set_var("feature_index", htmlspecialchars($feature_index));
			$t->set_var("feature_id", htmlspecialchars($feature_id));
			$t->set_var("group_id", htmlspecialchars($group_id));
			$t->set_var("group_name", htmlspecialchars($group_name));
			$t->set_var("feature_name", htmlspecialchars($feature_name));
			$t->set_var("feature_values", htmlspecialchars($feature_values));
			$t->set_var("feature_select", "");
			$t->set_var("feature_textbox", "");
			if (sizeof($default_values) > 0) {
				set_options($default_values, $feature_value, "feature_value_list");
				$t->parse("feature_select", false);
			} else {
				$t->set_var("feature_value", htmlspecialchars($feature_value));
				$t->parse("feature_textbox", false);
			}

			$t->parse("features", true);
		}

		$t->set_var("features_number", sizeof($features));
		$t->parse("features_block", false);
	} else {
		$t->set_var("features_block", "");
	}


	// set images 
	if (sizeof($images) > 0) {
		$image_index = 0;
		for ($i = 0; $i < sizeof($images); $i++) {
			$image_index++;
			$image_id = $images[$i][0];
			$image_title = $images[$i][1];
			$picture_small = $images[$i][2];
			$picture_large = $images[$i][3];
			$image_description = $images[$i][4];

			$t->set_var("image_index", htmlspecialchars($image_index));
			$t->set_var("image_id", htmlspecialchars($image_id));
			$t->set_var("image_title", htmlspecialchars($image_title));
			$t->set_var("picture_small", htmlspecialchars($picture_small));
			$t->set_var("picture_large", htmlspecialchars($picture_large));
			$t->set_var("image_description", htmlspecialchars($image_description));

			$t->parse("images", true);
		}

		$t->set_var("images_number", sizeof($images));
		$t->parse("images_block", false);
	} else {
		$t->set_var("images_block", "");
	}


	if(strlen($item_id)) {
		if (isset($user_settings["edit_ad"]) && $user_settings["edit_ad"] == 1) {
			$t->set_var("save_button_title", UPDATE_BUTTON);
			$t->global_parse("save_button", false, false, true);
		}
		if (isset($user_settings["delete_ad"]) && $user_settings["delete_ad"] == 1) {
			$t->parse("delete", false);	
		}
	} else {
		if (isset($user_settings["add_ad"]) && $user_settings["add_ad"] == 1) {
			$t->set_var("save_button_title", ADD_BUTTON);
			$t->global_parse("save_button", false, false, true);
		}
		$t->set_var("delete", "");	
	}

	$post_price = 0;
	$param_category_id = get_param("category_id");
	// set JS arrays to calculate prices
	foreach ($categories as $js_id => $category) {
		$publish_price = $category[2];
		if ($publish_price > 0) {
			if ($js_id == $param_category_id && !$item_id) {
				$post_price += $publish_price;
			}
			$t->set_var("js_id", $js_id);
			$t->set_var("publish_price", number_format($publish_price, 2, ".", ""));
			$t->parse("categories_js", true);
		}
	}
	foreach ($days_list as $key => $day_info) {
		$days_id = $day_info[0];
		$publish_price = $day_info[2];
		if ($publish_price > 0) {
			if ($days_id == $r->get_value("days_run") && 
				($r->get_value("days_run") != $r->get_value("current_days_run") || $r->get_value("date_start") != $r->get_value("current_date_start"))) {
				$post_price += $publish_price;
			}
			$t->set_var("days_id", $days_id);
			$t->set_var("publish_price", number_format($publish_price, 2, ".", ""));
			$t->parse("days_js", true);
		}
	}
	foreach ($hot_days_list as $key => $day_info) {
		$days_id = $day_info[0];
		$publish_price = $day_info[2];
		if ($publish_price > 0) {
			if ($days_id == $r->get_value("hot_days_run") && 
				($r->get_value("hot_days_run") != $r->get_value("current_hot_days_run") || $r->get_value("hot_date_start") != $r->get_value("current_hot_date_start"))) {
				$post_price += $publish_price;
			}
			$t->set_var("days_id", $days_id);
			$t->set_var("publish_price", number_format($publish_price, 2, ".", ""));
			$t->parse("hot_days_js", true);
		}
	}
	foreach ($special_days_list as $key => $day_info) {
		$days_id = $day_info[0];
		$publish_price = $day_info[2];
		if ($publish_price > 0) {
			if ($days_id == $r->get_value("special_days_run") && 
				($r->get_value("special_days_run") != $r->get_value("current_special_days_run") || $r->get_value("special_date_start") != $r->get_value("current_special_date_start"))) {
				$post_price += $publish_price;
			}
			$t->set_var("days_id", $days_id);
			$t->set_var("publish_price", number_format($publish_price, 2, ".", ""));
			$t->parse("special_days_js", true);
		}
	}

	if ($post_price > 0) {
		$save_note = ADS_PUBLISH_PRICE_MSG.": ".currency_format($post_price);
		$t->set_var("save_note_general", $save_note);
		$t->set_var("save_note_general_style", "display: block;");
	} else {
		$t->set_var("save_note_general_style", "display: none;");
	}

	// set styles for tabs
	$tabs = array(
		"general" => array("title" => AD_GENERAL_MSG), 
		"ad_desc" => array("title" => AD_DESCRIPTION_MSG), 
		"location" => array("title" => AD_LOCATION_MSG), 
		"images" => array("title" => IMAGES_MSG), 
		"ad_hot" => array("title" => AD_HOT_OFFER_MSG, "show" => $hot_offer), 
		"ad_special" => array("title" => AD_SPECIAL_OFFER_MSG, "show" => $special_offer), 
	);
	parse_tabs($tabs, $current_tab);

	$t->parse("block_body", false);
	$t->parse($block_name, true);

	function check_category_post()
	{
		global $r, $categories, $type_name;
		if (!$r->is_empty("category_id")) {
			if (!isset($categories[$r->get_value("category_id")])) {
				$r->errors .= "You can't assign your " . $type_name . " to this category.<br>";
			}
		}
	}

?>