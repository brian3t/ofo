<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads_edit.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/ads_functions.php");
	include_once($root_folder_path . "includes/friendly_functions.php");

	check_admin_security("ads");

	$category_id = get_param("category_id");
	
	$content_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_ads_edit.html");

	$t->set_var("admin_ads_edit_href", "admin_ads_edit.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");
	$t->set_var("admin_user_href", "admin_user.php");
	$t->set_var("admin_users_select_href", "admin_users_select.php");

	$t->set_var("admin_ads_href", "admin_ads.php");

	$date_format_msg = str_replace("{date_format}", join("", $date_edit_format), DATE_FORMAT_MSG);
	$t->set_var("date_format_msg", $date_format_msg);
	$t->set_var("date_edit_format", join("", $date_edit_format));

	$t->set_var("currency_sign",   $currency["left"].$currency["right"]);
	$t->set_var("currency_left",   $currency["left"]);
	$t->set_var("currency_right",  $currency["right"]);
	$t->set_var("currency_rate",   $currency["rate"]);
	$t->set_var("currency_decimals",   $currency["decimals"]);
	$t->set_var("currency_point",   $currency["point"]);
	$t->set_var("currency_separator",   $currency["separator"]);

	$duplicate_properties = get_param("duplicate_properties");
	$duplicate_specification = get_param("duplicate_specification");
	$duplicate_categories = get_param("duplicate_categories");
	$duplicate_images = get_param("duplicate_images");

	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "ads_categories", "tree", "Ads");
	$tree->show($category_id);

	$approve_values = array(array(1, YES_MSG), array(0, NO_MSG));
	$item_types = get_db_values("SELECT * FROM " . $table_prefix . "ads_types", array(array("", "")));
	$states = get_db_values("SELECT state_id,state_name FROM " . $table_prefix . "states ORDER BY state_name ", array(array("", SELECT_STATE_MSG)));
	$countries = get_db_values("SELECT country_id,country_name FROM " . $table_prefix . "countries ORDER BY country_order, country_name ", array(array("", SELECT_COUNTRY_MSG)));

	$r = new VA_Record($table_prefix . "ads_items");

	// set up html form parameters
	$r->add_where("item_id", INTEGER);
	$r->change_property("item_id", USE_IN_INSERT, true);
	$r->add_hidden("category_id", INTEGER);

	$r->add_checkbox("is_shown", INTEGER);
	$r->add_checkbox("is_shown_internal", INTEGER);
	$r->add_radio("is_approved", INTEGER, $approve_values, IS_APPROVED_MSG);

	$r->add_textbox("user_id", INTEGER, OWNER_MSG);
	//$r->change_property("user_id", REQUIRED, true);
	$r->change_property("user_id", USE_SQL_NULL, false);

	$r->add_select("type_id", INTEGER, $item_types, TYPE_MSG);
	$r->parameters["type_id"][REQUIRED] = true;
	$r->add_textbox("language_code", TEXT);
	$r->change_property("language_code", USE_SQL_NULL, false);
	//$r->add_textbox("item_order", INTEGER);
	$r->add_textbox("item_title", TEXT, TITLE_MSG);
	$r->parameters["item_title"][REQUIRED] = true;
	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);
	$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
	$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	$r->add_textbox("admin_id", INTEGER);

	$days_list = VA_Ads::get_days_list($table_prefix . "ads_days");
	$r->add_textbox("date_start",   DATETIME);
	$r->change_property("date_start", REQUIRED, true);
	$r->change_property("date_start", VALUE_MASK, $date_edit_format);
	$r->add_select("days_run", INTEGER, $days_list, AD_RUNS_MSG);
	$r->change_property("days_run",   REQUIRED, true);

	$r->add_textbox("date_end",     DATETIME);
	$r->change_property("date_end", VALUE_MASK, $date_edit_format);
	$r->add_textbox("date_added",   DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_updated", DATETIME);

	$r->add_textbox("price", NUMBER, PRICE_MSG);
	$r->parameters["price"][REQUIRED] = true;
	$r->add_textbox("quantity", NUMBER);
	$r->add_textbox("availability", TEXT);
	$r->add_checkbox("is_compared", INTEGER);
	$r->add_textbox("total_views", INTEGER);
	$r->change_property("total_views", USE_IN_INSERT, false);
	$r->change_property("total_views", USE_IN_UPDATE, false);

	$r->add_textbox("short_description", TEXT);
	$r->add_textbox("full_description", TEXT);
	$r->add_textbox("image_small", TEXT);
	$r->add_textbox("image_large", TEXT);

	// location fields
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
	$r->add_textbox("hot_date_end", DATETIME);
	$r->change_property("hot_date_end ", VALUE_MASK, $date_edit_format);
	$r->add_select("hot_days_run", INTEGER, $hot_days_list, ADS_HOT_DAYS_MSG);
	$r->add_textbox("hot_description", TEXT);

	// special offer fields
	$special_days_list = VA_Ads::get_days_list($table_prefix . "ads_special_days");
	$r->add_checkbox("is_special", INTEGER);
	$r->add_textbox("special_date_start", DATETIME, AD_SPECIAL_START_MSG);
	$r->change_property("special_date_start", VALUE_MASK, $date_edit_format);
	$r->add_textbox("special_date_end", DATETIME);
	$r->change_property("special_date_end", VALUE_MASK, $date_edit_format);
	$r->add_select("special_days_run", INTEGER, $special_days_list, ADS_SPECIAL_DAYS_MSG);
	$r->add_textbox("special_description", TEXT);

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


	$item_id = get_param("item_id");
	$operation = get_param("operation");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	$return_page = "admin_ads.php?category_id=" . $category_id;

	if (strlen($operation))
	{
		$tab = "general";
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		elseif ($operation == "delete" && $item_id)
		{
			// ads tables with item_id: 
			$db->query("DELETE FROM " . $table_prefix . "ads_properties WHERE item_id=" . $db->tosql($item_id, INTEGER));		
			$db->query("DELETE FROM " . $table_prefix . "ads_assigned WHERE item_id=" . $db->tosql($item_id, INTEGER));		
			$db->query("DELETE FROM " . $table_prefix . "ads_features WHERE item_id=" . $db->tosql($item_id, INTEGER));		
			$db->query("DELETE FROM " . $table_prefix . "ads_images WHERE item_id=" . $db->tosql($item_id, INTEGER));		
			$db->query("DELETE FROM " . $table_prefix . "ads_items WHERE item_id=" . $db->tosql($item_id, INTEGER));		

			header("Location: " . $return_page);
			exit;
		}
		if ($r->get_value("is_hot")) {
			$r->change_property("hot_days_run", REQUIRED, true);
			$r->change_property("hot_date_start", REQUIRED, true);
		}
		if ($r->get_value("is_special")) {
			$r->change_property("special_days_run", REQUIRED, true);
			$r->change_property("special_date_start", REQUIRED, true);
		}
		$r->set_value("is_shown_internal", $r->get_value("is_shown")); // always update internal field when the record updates

		$is_valid = $r->validate();
		
		if ($is_valid)
		{
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

			$r->set_value("admin_id", get_session("session_admin_id"));

			if ($operation == "duplicate" && $item_id) {
				// subtract credits before adding/updating ad
				VA_Ads::credits($r, false, true);

				// duplicate product with new id 
				$db->query("SELECT MAX(item_id) FROM " . $table_prefix . "ads_items");
				$db->next_record();
				$new_item_id = $db->f(0) + 1;
				$r->set_value("item_title", $r->get_value("item_title") . " (".DUPLICATE_BUTTON.")");
				$r->set_value("item_id", $new_item_id);
				$record_updated = $r->insert_record();


				// duplicate product features
				if ($record_updated && $duplicate_specification == 1) {
					$item_features = array();
					$sql  = " SELECT group_id, feature_name, feature_value FROM " . $table_prefix . "features ";
					$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
					$sql .= " ORDER BY feature_id ";
					$db->query($sql);
					while ($db->next_record()) {
						$item_features[] = array($db->f("group_id"), $db->f("feature_name"), $db->f("feature_value"));
					}
					for ($i = 0; $i < sizeof($item_features); $i++) {
						$group_id = $item_features[$i][0];
						$feature_name = $item_features[$i][1];
						$feature_value = $item_features[$i][2];
						$sql  = " INSERT INTO " . $table_prefix . "ads_features (item_id, group_id, feature_name, feature_value) VALUES (";
						$sql .= $db->tosql($new_item_id, INTEGER) . "," . $db->tosql($group_id, INTEGER) . "," . $db->tosql($feature_name, TEXT) . "," . $db->tosql($feature_value, TEXT) . ")";
						$db->query($sql);
					}
				}
				
				// duplicate product categories
				if ($record_updated && $duplicate_categories == 1) {
					$item_categories = array();
					$sql  = " SELECT category_id FROM " . $table_prefix . "ads_assigned ";
					$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
					$db->query($sql);
					while ($db->next_record()) {
						$item_categories[] = $db->f("category_id");
					}
					for ($i = 0; $i < sizeof($item_categories); $i++) {
						$item_category_id = $item_categories[$i];
						$sql  = " INSERT INTO " . $table_prefix . "ads_assigned (item_id,category_id) VALUES (";
						$sql .= $db->tosql($new_item_id, INTEGER) . ",";
						$sql .= $db->tosql($item_category_id, INTEGER) . ")";
						$db->query($sql);
					}
				} else {
					$sql  = " INSERT INTO " . $table_prefix . "ads_assigned (item_id,category_id) VALUES (";
					$sql .= $db->tosql($new_item_id, INTEGER) . ",";
					$sql .= $db->tosql($category_id, INTEGER) . ")";
					$db->query($sql);
				}

				// duplicate product images
				if ($record_updated && $duplicate_images == 1) {
					$item_images = array();
					$sql  = " SELECT image_small, small_width, small_height, ";
					$sql .= " image_large, image_title, image_description ";
					$sql .= " FROM " . $table_prefix . "ads_images ";
					$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
					$db->query($sql);
					while ($db->next_record()) {
						$item_images[] = array(
							$db->f("image_small"), $db->f("small_width"), $db->f("small_height"),
							$db->f("image_large"), $db->f("image_title"), $db->f("image_description")
						);
					}
					for ($i = 0; $i < sizeof($item_images); $i++) {
						list ($image_small, $small_width, $small_height, $image_large, $image_title, $image_description) = $item_images[$i];
						$sql  = " INSERT INTO " . $table_prefix . "ads_images ";
						$sql .= " (item_id, image_small, small_width, small_height, image_large, image_title, image_description) VALUES (";
						$sql .= $db->tosql($new_item_id, INTEGER) . "," . $db->tosql($image_small, TEXT) . ",";
						$sql .= $db->tosql($small_width, INTEGER) . "," . $db->tosql($small_height, INTEGER) . ",";
						$sql .= $db->tosql($image_large, TEXT) . "," . $db->tosql($image_title, TEXT) . "," . $db->tosql($image_description, TEXT) . ")";
						$db->query($sql);
					}
				}

				// duplicate all properties
				if ($record_updated && $duplicate_properties == 1) {
					$item_properties = array();
					$sql  = " SELECT * FROM " . $table_prefix . "ads_properties ";
					$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
					$db->query($sql);
					if ($db->next_record()) {
						do {
							$item_properties[] = array($db->f("property_id"), $db->f("property_name"), $db->f("property_value"));
						} while ($db->next_record());
			  
						$ip = new VA_Record($table_prefix . "ads_properties");
						$ip->add_textbox("property_id", INTEGER);
						$ip->add_textbox("item_id", INTEGER);
						$ip->add_textbox("property_name", TEXT);
						$ip->add_textbox("property_value", TEXT);
						$ip->set_value("item_id", $new_item_id);
						
						for ($i = 0; $i < sizeof($item_properties); $i++) {
							$property_id = $item_properties[$i][0];
							$db->query("SELECT MAX(property_id) FROM " . $table_prefix . "ads_properties ");
							$db->next_record();
							$new_property_id = $db->f(0) + 1;
							$ip->set_value("property_id", $new_property_id);
							$ip->set_value("property_name", $item_properties[$i][1]);
							$ip->set_value("property_value", $item_properties[$i][2]);
							$ip->insert_record();
						}
					}
				}
				// end of saving properties

			} elseif (strlen($item_id)) {
				// subtract credits before adding/updating ad
				VA_Ads::credits($r, true, true);
				set_friendly_url();
				$r->set_value("date_updated", va_time());
				$record_updated = $r->update_record();
			} else {
				// subtract credits before adding/updating ad
				VA_Ads::credits($r, false, true);
				set_friendly_url();
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
					$sql .= $db->tosql($category_id, INTEGER) . ")";
					$db->query($sql);
				} else {
					$item_id = "";
					$r->set_value("item_id", "");
				}
			}
	
			if ($record_updated) {
				header("Location: " . $return_page);
				exit;
			}
		}
	}
	elseif (strlen($item_id))
	{
		$r->get_db_values();
		$date_start = $r->get_value("date_start");
		$date_end = $r->get_value("date_end");
		/*
		$date_start_ts = mktime(0,0,0, $date_start[MONTH], $date_start[DAY], $date_start[YEAR]);
		$date_end_ts = mktime(0,0,0, $date_end[MONTH], $date_end[DAY], $date_end[YEAR]);
		$time_to_run = $date_end_ts - $date_start_ts;
		$days_to_run = round($time_to_run / (60 * 60 * 24));
		$r->set_value("days_to_run", $days_to_run);//*/

		$r->set_value("current_date_start", $r->get_value("date_start"));
		$r->set_value("current_days_run", $r->get_value("days_run"));
		$r->set_value("current_hot_date_start", $r->get_value("hot_date_start"));
		$r->set_value("current_hot_days_run", $r->get_value("hot_days_run"));
		$r->set_value("current_special_date_start", $r->get_value("special_date_start"));
		$r->set_value("current_special_days_run", $r->get_value("special_days_run"));
	}
	else // new record (set default values)
	{
		$r->set_value("is_approved", 1);
		$r->set_value("is_shown", 1);
		$r->set_value("date_start", va_time());
		/*
		$sql  = " SELECT MAX(item_order) FROM " . $table_prefix . "ads_items i, " . $table_prefix . "ads_assigned ic ";
		$sql .= " WHERE i.item_id=ic.item_id ";
		$sql .= " AND ic.category_id=" . $db->tosql($category_id, INTEGER);
		$item_order = get_db_value($sql);
		$item_order = ($item_order) ? ($item_order + 1) : 1;
		$r->set_value("item_order", $item_order);//*/
	}

	$r->set_form_parameters();

	// check if user information available
	$user_id = $r->get_value("user_id");
	if ($user_id) {
		$sql  = " SELECT user_id, login, email, name, first_name, last_name, nickname, company_name ";
		$sql .= " FROM " . $table_prefix . "users u ";
		$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
		$db->query($sql);
		if ($db->next_record())
		{
			$user_name = $db->f("name");
			if (!strlen($user_name)) { $user_name = trim($db->f("first_name") . " " . $db->f("last_name")); }
			if (!strlen($user_name)) { $user_name = trim($db->f("nickname")); }
			if (!strlen($user_name)) { $user_name = $db->f("company_name"); }
			$t->set_var("user_id", $user_id);
			$t->set_var("user_name", $user_name);
			$t->parse("selected_user", false);
		}
	}

	
	// check assigned categories
	$post_price = 0;
	if ($item_id) {
		$sql  = " SELECT ac.category_id,ac.publish_price ";
		$sql .= " FROM (" . $table_prefix . "ads_assigned aa ";
		$sql .= " INNER JOIN " . $table_prefix . "ads_categories ac ON aa.category_id=ac.category_id) ";
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	} else {
		$sql  = " SELECT category_id,publish_price FROM " . $table_prefix . "ads_categories ";
		$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
	}
	$categories_ids = "";
	$db->query($sql);
	while ($db->next_record()) {
		$js_id = $db->f("category_id");
		if ($categories_ids) { $categories_ids .= ","; }
		$categories_ids .= $js_id;
		$publish_price = $db->f("publish_price");
		if ($publish_price > 0) {
			if (!$item_id) {
				$post_price += $publish_price;
			}
			$t->set_var("js_id", $category_id);
			$t->set_var("publish_price", number_format($publish_price, 2, ".", ""));
			$t->parse("categories_js", true);
		}
	}
	$t->set_var("categories_ids", $categories_ids);
	if ($item_id) {
		$t->set_var("current_categories_ids", $categories_ids);
	} else {
		$t->set_var("current_categories_ids", "");
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

	if (strlen($item_id))	
	{
		$duplicate_properties    = ($duplicate_properties == 1) ? " checked " : "";
		$duplicate_specification = ($duplicate_specification == 1) ? " checked " : "";
		$duplicate_categories    = ($duplicate_categories == 1) ? " checked " : "";
		$duplicate_images        = ($duplicate_images == 1) ? " checked " : "";

		$t->set_var("duplicate_properties",    $duplicate_properties);
		$t->set_var("duplicate_specification", $duplicate_specification);
		$t->set_var("duplicate_categories",    $duplicate_categories);
		$t->set_var("duplicate_images",        $duplicate_images);

		$t->set_var("edit_title", $r->get_value("item_title"));
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
		$t->parse("duplicate", false);	
	}
	else
	{
		$t->set_var("edit_title", NEW_MSG);
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");	
		$t->set_var("duplicate", "");	
	}

	// set styles for tabs
	$tabs = array(
		"general" => array("title" => AD_GENERAL_MSG), 
		"ad_desc" => array("title" => AD_DESCRIPTION_MSG), 
		"location" => array("title" => AD_LOCATION_MSG), 
		"images" => array("title" => IMAGES_MSG), 
		"ad_hot" => array("title" => AD_HOT_OFFER_MSG), 
		"ad_special" => array("title" => AD_SPECIAL_OFFER_MSG), 
	);
	parse_admin_tabs($tabs, $tab);


	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>