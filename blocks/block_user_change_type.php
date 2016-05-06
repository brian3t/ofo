<?php

	$user_id = get_session("session_user_id");
	$new_user_id = get_session("session_new_user_id");
	if ($user_id) {
		$current_user_id = $user_id;
		$current_user_type_id = get_session("session_user_type_id");
	} else if ($new_user_id) {
		$current_user_id = $new_user_id;
		$current_user_type_id = get_session("session_new_user_type_id");
	} else {
		header ("Location: index.php");
		exit;
	}
	$user_type_id = $current_user_type_id;

	$current_user_settings = array();
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "user_types_settings WHERE type_id=" . $db->tosql($current_user_type_id, INTEGER);
	$db->query($sql);
	while ($db->next_record()) {
		$current_user_settings[$db->f("setting_name")] = $db->f("setting_value");
	}
	$upgrade_downgrade = get_setting_value($current_user_settings, "upgrade_downgrade", 0);
	$cancel_subscription = get_setting_value($current_user_settings, "cancel_subscription", 0);

	$current_subscription_id = ""; $current_is_approved = 0;
	$sql  = " SELECT is_approved, subscription_id FROM " . $table_prefix . "users ";
	if ($user_id) {
		$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
	} else {
		$sql .= " WHERE user_id=" . $db->tosql($new_user_id, INTEGER);
	}
	$db->query($sql);
	if ($db->next_record()) {
		$current_is_approved = $db->f("is_approved");
		$current_subscription_id = $db->f("subscription_id");
	}

	//check_user_session();

	$t->set_file("block_body","block_user_change_type.html");
	$t->set_var("user_change_type_href", get_custom_friendly_url("user_change_type.php"));
	$t->set_var("user_home_href", get_custom_friendly_url("user_home.php"));
	$t->set_var("currency_left", htmlspecialchars($currency["left"]));
	$t->set_var("currency_right", htmlspecialchars($currency["right"]));
	$t->set_var("currency_rate", htmlspecialchars($currency["rate"]));
	$t->set_var("currency_decimals", htmlspecialchars($currency["decimals"]));
	$t->set_var("currency_point", htmlspecialchars($currency["point"]));
	$t->set_var("currency_separator", htmlspecialchars($currency["separator"]));

	$user_types = array(); $subscription_types = array();
	$sql  = " SELECT ut.type_id, ut.type_name, ut.is_subscription ";
	if (isset($site_id)) {
		$sql .= " FROM (" . $table_prefix . "user_types ut ";
		$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites uts ON uts.type_id=ut.type_id)";
		$sql .= " WHERE (ut.sites_all=1 OR uts.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
	} else {
		$sql .= " FROM " . $table_prefix . "user_types ut ";
		$sql .= " WHERE ut.sites_all=1 ";					
	}
	$sql .= " AND ut.is_active=1 AND ut.show_for_user=1";
	if (!$upgrade_downgrade) {
		$sql .= " AND ut.type_id=" . $db->tosql($current_user_type_id, INTEGER);
	}
	$db->query($sql);
	while ($db->next_record()) {
		$type_id = $db->f("type_id");
		$type_name = get_translation($db->f("type_name"));
		$is_subscription = $db->f("is_subscription");
		if ($is_subscription) {
			$subscription_types[] = $type_id;
		}
		$user_types[$type_id] = $type_name;
	}

	$subscriptions = array();
	if ($subscription_types) {
		$sql  = " SELECT * FROM " . $table_prefix . "subscriptions ";
		$sql .= " WHERE user_type_id IN (" . $db->tosql($subscription_types, INTEGERS_LIST) . ") ";
		$sql .= " AND is_active=1 ";
		$db->query($sql);
		while ($db->next_record()) {
			$subscription_user_type_id = $db->f("user_type_id");
			$type_subscription_id = $db->f("subscription_id");
			$subscriptions[$subscription_user_type_id][$type_subscription_id] = array(
				"subscription_name" =>get_translation($db->f("subscription_name")),
				"subscription_fee" =>$db->f("subscription_fee"),
				"subscription_period" =>$db->f("subscription_period"),
				"subscription_interval" =>$db->f("subscription_interval"),
				"subscription_suspend" =>$db->f("subscription_suspend"),
				"subscription_is_default" =>$db->f("is_default"),
			);
		}
	}

	$orders_subscriptions = array();
	$current_datetime = va_time();
	$current_date_ts = mktime (0, 0, 0, $current_datetime[MONTH], $current_datetime[DAY], $current_datetime[YEAR]);

	$sql  = " SELECT oi.order_id, oi.order_item_id, oi.item_name, oi.subscription_id, oi.price, oi.reward_credits, ";
	$sql .= " oi.subscription_start_date, oi.subscription_expiry_date ";
	$sql .= " FROM (" . $table_prefix . "orders_items oi ";
	$sql .= " INNER JOIN " . $table_prefix . "order_statuses os ON oi.item_status=os.status_id) ";
	$sql .= " WHERE user_id=" . $db->tosql($current_user_id, INTEGER);
	$sql .= " AND oi.is_subscription=1 ";
	$sql .= " AND oi.is_account_subscription=1 ";
	$sql .= " AND os.paid_status=1 ";
	$sql .= " AND subscription_expiry_date>" . $db->tosql($current_date_ts, DATETIME);
	$db->query($sql);
	while ($db->next_record()) {
		$order_id = $db->f("order_id");
		$order_item_id = $db->f("order_item_id");
		$subscription_id = $db->f("subscription_id");
		$item_name = $db->f("item_name");
		$price = $db->f("price");
		$reward_credits = $db->f("reward_credits");
		$subscription_sd = $db->f("subscription_start_date", DATETIME);
		$subscription_ed = $db->f("subscription_expiry_date", DATETIME);
		$subscription_sd_ts = va_timestamp($subscription_sd);
		$subscription_ed_ts = va_timestamp($subscription_ed);
		$subscription_days = intval(($subscription_ed_ts - $subscription_sd_ts) / 86400); // get int value due to possible 1 hour difference
		// check days difference and add current day as well
		$used_days = intval(($current_date_ts - $subscription_sd_ts) / 86400) + 1;
		if ($cancel_subscription == 1) {
			// return money to credits balance
			$credits_return = round((($price - $reward_credits)/ $subscription_days) * ($subscription_days - $used_days), 2); 
		} else {
			$credits_return = 0; 
		}

		$orders_subscriptions[$order_item_id] = array(
			"order_id" => $order_id,
			"order_item_id" => $order_item_id,
			"subscription_id" => $subscription_id,
			"item_name" => $item_name,
			"start_date" => $subscription_sd,
			"expiry_date" => $subscription_ed,
			"current_date_ts" => $current_date_ts,
			"price" => $price,
			"reward_credits" => $reward_credits,
			"credits_return" => $credits_return,
		);
	}

	$errors = "";
	$operation = get_param("operation");
	$site_url = get_setting_value($settings, "site_url", "");
	$secure_user_profile = get_setting_value($settings, "secure_user_profile", 0);
	$return_page = $site_url . get_custom_friendly_url("user_home.php");

	if(strlen($operation))
	{
		if($operation == "cancel") {
			header("Location: " . $return_page);
			exit;
		}
		$type_id = get_param("type_id");
		$subscription_id = get_param("subscription_id");
		if (sizeof($user_types) > 0) {
			if (strlen($type_id) && isset($user_types[$type_id])) {
				$user_type_id = $type_id;
			} else {
				$error_message = str_replace("{field_name}", GROUP_MSG, REQUIRED_MESSAGE);
				$errors .= $error_message . "<br>";
			}
		}		
		if (!$errors) {
			if (isset($subscriptions[$type_id])) {
				if (strlen($subscription_id) && isset($subscriptions[$type_id][$subscription_id])) {
					// subscription selected
				} else {
					$error_message = str_replace("{field_name}", SUBSCRIPTION_MSG, REQUIRED_MESSAGE);
					$errors .= $error_message . "<br>";
				}
			}
		}
		if (!$errors) {
			$user_settings = array();
			$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "user_types_settings ";
			$sql .= " WHERE type_id=" . $db->tosql($type_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$user_settings[$db->f("setting_name")] = $db->f("setting_value");
			}
			$is_approved = get_setting_value($user_settings, "approve_profile", 0);
			if ($current_user_type_id != $type_id) {
				$user_is_approved = $is_approved;
			} else {
				$user_is_approved = $current_is_approved;
			}
			// update information if user type or subscription changed
			if ($current_user_type_id != $type_id || $current_subscription_id != $subscription_id) {
				// update user information
				$sql  = " UPDATE " . $table_prefix . "users ";
				$sql .= " SET user_type_id=" . $db->tosql($type_id, INTEGER);
				if ($current_user_type_id != $type_id) {
					$sql .= " , is_approved=" . $db->tosql($is_approved, INTEGER);
				}
				if ($current_subscription_id != $subscription_id) {
					if (strlen($subscription_id)) {
						$sql .= " , subscription_id=" . $db->tosql($subscription_id, INTEGER);
						// set expiry_date and suspend_date as yesterday
						$expiry_date = va_time();
						$expiry_date_ts = mktime (0,0,0, $expiry_date[MONTH], $expiry_date[DAY] - 1, $expiry_date[YEAR]);
						$sql .= " , expiry_date=" . $db->tosql($expiry_date_ts, DATETIME);
						$sql .= " , suspend_date=" . $db->tosql($expiry_date_ts, DATETIME);
					} else {
						$sql .= " , expiry_date=NULL, suspend_date=NULL ";
					}
				}
				$sql .= " WHERE user_id=" . $db->tosql($current_user_id, INTEGER);
				$db->query($sql);

				if ($current_subscription_id != $subscription_id) {
					$r = new VA_Record($table_prefix . "orders_events");
					$r->add_textbox("order_id", INTEGER);
					$r->add_textbox("status_id", INTEGER);
					$r->add_textbox("admin_id", INTEGER);
					$r->add_textbox("order_items", TEXT);
					$r->add_textbox("event_date", DATETIME);
					$r->add_textbox("event_type", TEXT);
					$r->add_textbox("event_name", TEXT);
					$r->add_textbox("event_description", TEXT);

					// if subscription changed cancel all previous recurring subscriptions and return credits
					foreach($orders_subscriptions as $order_item_id => $item_info) {
						$new_reward_credits = $item_info["reward_credits"]+$item_info["credits_return"];
						$sql  = " UPDATE " . $table_prefix . "orders_items ";
						$sql .= " SET is_recurring=0, is_subscription=0, ";
						$sql .= " reward_credits=" . $db->tosql($new_reward_credits, NUMBER) . ",";
						$sql .= " subscription_expiry_date=" . $db->tosql($current_date_ts, DATETIME);
						$sql .= " WHERE user_id=" . $db->tosql($current_user_id, INTEGER);
						$sql .= " AND is_subscription=1 ";
						$sql .= " AND is_account_subscription=1 ";
						$db->query($sql);

						// save subscription event
						$r->set_value("order_id", $item_info["order_id"]);
						$r->set_value("order_items", $item_info["order_item_id"]);
						$r->set_value("status_id", 0);
						$r->set_value("admin_id", get_session("session_admin_id"));
						$r->set_value("event_date", va_time());
						$r->set_value("event_type", "cancel_subscription");
						$r->set_value("event_name", $item_info["item_name"]);
						$r->insert_record();
		
						// update user commissions
						calculate_commissions_points($item_info["order_id"], $item_info["order_item_id"]);
					}
				}
				//if ($current_user_type_id != $type_id) { // check if profile should be updated }

			} 

			if (strlen($subscription_id) && ($current_subscription_id != $subscription_id || $new_user_id)) {
				// forward user to pay for new subscription
				if ($user_id) {
					user_logout();
					set_session("session_new_user", "expired");
					set_session("session_new_user_id", $user_id);
					set_session("session_new_user_type_id", $type_id);
				}
				add_subscription($type_id, $subscription_id, $subscription_name);
				header("Location: order_info.php");
				exit;
			}	if ($user_is_approved && $current_subscription_id && !$subscription_id && $new_user_id) {
				// forward user to home in case he disabled which to pay for new subscription
				user_login("", "", $new_user_id, 0, "", false, $errors);
				header("Location: user_home.php");
				exit;
			}

			// redirect to page
			if ($user_id) {
				header("Location: user_home.php");
				exit;
			} else {
				header("Location: user_login.php");
				exit;
			}
		}
	}

	if ($errors) {
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	}	else {
		$t->set_var("errors", "");
	}

	if (sizeof($user_types) > 0) {
		foreach($user_types as $type_id => $type_name) {
			$type_selected = ($type_id == $user_type_id) ? " selected " : "";
			$t->set_var("type_id_value", $type_id);
			$t->set_var("type_id_js", $type_id);
			$t->set_var("type_id_description", $type_name);
			$t->set_var("type_name_js", str_replace("\"", "&quot;", $type_name));
			$t->set_var("type_id_selected", $type_selected);

			$t->parse("type_id", true);
			$t->parse("js_types", true);
		}
		$t->parse("user_type_block", false);
	}


	$sql  = " SELECT subscription_id FROM " . $table_prefix . "users ";
	$sql .= " WHERE user_id=" . $db->tosql($current_user_id, INTEGER);
	$user_subscription_id = get_db_value($sql);
	$t->set_var("user_subscription_id", $user_subscription_id);

	$current_subscriptions = 0;
	if ($subscription_types) {
		$sql  = " SELECT * FROM " . $table_prefix . "subscriptions ";
		$sql .= " WHERE user_type_id IN (" . $db->tosql($subscription_types, INTEGERS_LIST) . ") ";
		$sql .= " AND is_active=1 ";
		$db->query($sql);
		while ($db->next_record()) {
			$subscription_user_type_id = $db->f("user_type_id");
			$type_subscription_id = $db->f("subscription_id");
			$subscription_name = get_translation($db->f("subscription_name"));
			$subscription_fee = $db->f("subscription_fee");
			$subscription_period = $db->f("subscription_period");
			$subscription_interval = $db->f("subscription_interval");
			$subscription_suspend = $db->f("subscription_suspend");
			$subscription_is_default = $db->f("is_default");
			if (strlen($user_id) || strlen($new_user_id))	{
				$subscription_id_checked = ($type_subscription_id == $user_subscription_id) ? " checked " : "";
			}
			if ($subscription_interval == 1) {
				$subscription_periods = array(1 => DAY_MSG, 2 => WEEK_MSG, 3 => MONTH_MSG, 4 => YEAR_MSG);
				$period_message = "1 " . $subscription_periods[$subscription_period];
			} else {
				$subscription_periods = array(1 => DAYS_QTY_MSG, 2 => WEEKS_QTY_MSG, 3 => MONTHS_QTY_MSG, 4 => YEARS_QTY_MSG);
				$period_message = $subscription_periods[$subscription_period];
				$period_message = str_replace("{quantity}", $subscription_interval, $period_message);
			}
			// parse current subscriptions options
			if ($subscription_user_type_id == $user_type_id) {
				$current_subscriptions++;
				$t->set_var("subscription_id_value", $type_subscription_id);
				$t->set_var("subscription_id_checked", $subscription_id_checked);
				$t->set_var("subscription_name", $subscription_name);
				$t->set_var("subscription_fee", currency_format($subscription_fee));
				$t->set_var("subscription_period", $period_message);
				$t->parse("subscriptions", true);
			}
	
			// set variables for JavaScript
			$t->set_var("type_id_js", $subscription_user_type_id);
			$t->set_var("subscription_id_js", $type_subscription_id);
			$t->set_var("subscription_name_js", str_replace("\"", "&quot;", $subscription_name));
			$t->set_var("subscription_fee_js", str_replace("\"", "&quot;", currency_format($subscription_fee)));
			$t->set_var("subscription_period_js", str_replace("\"", "&quot;", $period_message));
	
			$t->parse("js_subscriptions", true);
		}
	}
	if ($current_subscriptions) {
		$subscription_info_style = "display:table-row;";
	} else {
		$subscription_info_style = "display:none;";
	}
	$t->set_var("subscription_info_style", $subscription_info_style);

	$total_credits_return = 0;
	foreach ($orders_subscriptions as $order_item_id => $item_info) {
		$t->set_var("order_item_id_js", $order_item_id);
		$t->set_var("subscription_id_js", $item_info["subscription_id"]);
		$t->set_var("price_js", str_replace("\"", "&quot;", $item_info["price"]));
		$t->set_var("credits_return_js", str_replace("\"", "&quot;", $item_info["credits_return"]));

		$t->parse("js_orders_subscriptions", true);
	}
	if ($total_credits_return) {
		$money_back_style = "display:table-row;";
	} else {
		$money_back_style = "display:none;";
	}
	$t->set_var("total_credits_return", currency_format($total_credits_return));
	$t->set_var("money_back_style", $money_back_style);


	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>