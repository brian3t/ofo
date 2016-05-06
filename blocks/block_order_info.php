<?php

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}
	$t->set_file("block_body", "block_order_info.html");

	$sc_errors = ""; $delivery_errors = "";
	$user_id = get_session("session_user_id");
	if (!$user_id) { $user_id = get_session("session_new_user_id"); }
	$user_type_id = get_session("session_user_type_id");
	if (!$user_type_id) { $user_type_id = get_session("session_new_user_type_id"); }
	$operation = get_param("operation");

	if ($operation == "fast_checkout") {
		$fast_payment_id = get_param("fast_payment_id");
		$sql  = " SELECT ps.payment_id FROM ";
		if (isset($site_id)) {
			$sql .= "(";
		}
		if (strlen($user_type_id)) {
			$sql .= "(";
		}
		$sql .= $table_prefix . "payment_systems ps";
		if (isset($site_id)) {
			$sql .= " LEFT JOIN " . $table_prefix . "payment_systems_sites s ON s.payment_id=ps.payment_id)";			
		}
		if (strlen($user_type_id)) {
			$sql .= " LEFT JOIN " . $table_prefix . "payment_user_types ut ON ut.payment_id=ps.payment_id)";			
		}
		$sql .= " WHERE ps.payment_id=" . $db->tosql($fast_payment_id, INTEGER);
		$sql .= " AND ps.is_active=1 AND ps.fast_checkout_active=1 ";
		if (isset($site_id)) {
			$sql .= " AND (ps.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";			
		} else {
			$sql .= " AND ps.sites_all=1";
		}
		if (strlen($user_type_id)) {
			$sql .= " AND (ps.user_types_all=1 OR ut.user_type_id=" . $db->tosql($user_type_id, INTEGER, true, false) . ")";			
		} else {
			$sql .= " AND ps.user_types_all=1";
		}
		$fast_payment_id = get_db_value($sql);
		if (!$fast_payment_id) {
			$sc_errors = "Can't find Fast Checkout payment module.";
			$operation = "";
		}
	} 

	$user_registration = get_setting_value($settings, "user_registration", 0);
	if ($user_registration == 1 && !strlen($user_id) && $operation != "fast_checkout") {
		// user need to be logged in before proceed
		header("Location: " . get_custom_friendly_url("checkout.php"));
		exit;
	}

	$shopping_cart = get_session("shopping_cart");
	if (!is_array($shopping_cart) || sizeof($shopping_cart) < 1)
	{
		header("Location: " . get_custom_friendly_url("basket.php"));
		exit;
	}

	// get order profile settings
	$sql  = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= "WHERE setting_type='order_info'";
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$order_info[$db->f("setting_name")] = $db->f("setting_value");
	}

	$eol = get_eol();
	$sess_currency = get_currency();
	$currency = get_currency($sess_currency["code"]);
	$shipping_block = get_setting_value($order_info, "shipping_block", 0);
	$default_currency_code = get_db_value("SELECT currency_code FROM ".$table_prefix."currencies WHERE is_default=1");


	$user_info = get_session("session_user_info");
	$user_tax_free = get_setting_value($user_info, "tax_free", 0);
	$points_balance = get_setting_value($user_info, "total_points", 0);

	// check if credit system active 
	$credit_system = get_setting_value($settings, "credit_system", 0);
	$credits_balance_order_profile = get_setting_value($settings, "credits_balance_order_profile", 0);
	$credit_balance = 0; $credit_amount = 0;
	if ($credit_system && $user_id) {
		// check user credit balance
		$sql = " SELECT credit_balance FROM " . $table_prefix . "users WHERE user_id=" . $db->tosql($user_id, INTEGER);
		$credit_balance = get_db_value($sql);
		// check if user decide to pay with credits
		$credit_amount = abs(get_param("credit_amount"));
		if ($credit_amount > $credit_balance) {
			$credit_amount = $credit_balance;
		}
	}
	$user_discount_type = get_session("session_discount_type");
	$user_discount_amount = get_session("session_discount_amount");
	$user_ip = get_ip();
	$referer = get_session("session_referer");
	$initial_ip = get_session("session_initial_ip");
	$cookie_ip = get_session("session_cookie_ip");
	$visit_id = get_session("session_visit_id");
	$visit_number = get_session("session_visit_number");
	if (!$visit_id) { $visit_id = 0; }
	$keywords = get_session("session_kw");
	$affiliate_code = get_session("session_af");
	$affiliate_user_id = 0;
	if (strlen($affiliate_code)) {
		$sql  = " SELECT u.user_id FROM (";
		if (isset($site_id)) { $sql .= "("; }
		$sql .= $table_prefix . "users u";
		$sql .= " LEFT JOIN " . $table_prefix . "user_types ut ON ut.type_id=u.user_type_id)";
		if (isset($site_id)) {
			$sql .= " LEFT JOIN " . $table_prefix . "user_types_sites s ON s.type_id=ut.type_id)";
		}
		$sql .= " WHERE u.affiliate_code=" . $db->tosql($affiliate_code, TEXT);
		if (isset($site_id)) {
			$sql .= " AND (ut.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";			
		} else {
			$sql .= " AND ut.sites_all=1";
		}
		$affiliate_user_id = get_db_value($sql);
	}
	$friend_code = get_session("session_friend");
	$friend_user_id = get_friend_info();

	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");
	$secure_payments = get_setting_value($settings, "secure_payments", 0);
	$secure_order_profile = get_setting_value($settings, "secure_order_profile", 0);
	$show_item_code = get_setting_value($settings, "item_code_checkout", 0);
	$show_manufacturer_code = get_setting_value($settings, "manufacturer_code_checkout", 0);
	$subscribe_block = get_setting_value($order_info, "subscribe_block", 0);
	$subcomponents_show_type = get_setting_value($order_info, "subcomponents_show_type", 0);
	
	$item_name_column = get_setting_value($settings, "checkout_item_name", 1);
	$item_price_column = get_setting_value($settings, "checkout_item_price", 1);
	$item_tax_percent_column = get_setting_value($settings, "checkout_item_tax_percent", 0);
	$item_tax_column = get_setting_value($settings, "checkout_item_tax", 0);
	$item_price_incl_tax_column = get_setting_value($settings, "checkout_item_price_incl_tax", 0);
	$item_quantity_column = get_setting_value($settings, "checkout_item_quantity", 1);
	$item_price_total_column = get_setting_value($settings, "checkout_item_price_total", 1);
	$item_tax_total_column = get_setting_value($settings, "checkout_item_tax_total", 1);
	$item_price_incl_tax_total_column = get_setting_value($settings, "checkout_item_price_incl_tax_total", 1);
	$item_image_column = get_setting_value($settings, "checkout_item_image", 0);
	
	// image settings
	$product_no_image = get_setting_value($settings, "product_no_image", "");
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	product_image_fields($item_image_column, $image_type_name, $image_field, $image_alt_field, $watermark, $product_no_image);
	
	$tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
	$tax_prices = get_setting_value($settings, "tax_prices", 0);
	$tax_note = get_translation(get_setting_value($settings, "tax_note", ""));
	$tax_note_excl = get_translation(get_setting_value($settings, "tax_note_excl", ""));

	// merchant and affiliate settings
	$affiliate_commission_deduct = get_setting_value($settings, "affiliate_commission_deduct", 0);

	// points settings
	$points_system = get_setting_value($settings, "points_system", 0);
	$points_conversion_rate = get_setting_value($settings, "points_conversion_rate", 1);
	$points_decimals = get_setting_value($settings, "points_decimals", 0);
	$reward_points_checkout = get_setting_value($settings, "reward_points_checkout", 0);
	$points_prices = get_setting_value($settings, "points_prices", 0);
	$points_orders_options = get_setting_value($settings, "points_orders_options", 0);
	$points_shipping = get_setting_value($settings, "points_shipping", 0);
	$points_for_points = get_setting_value($settings, "points_for_points", 0);
	$credits_for_points = get_setting_value($settings, "credits_for_points", 0);

	// credit settings
	$reward_credits_users = get_setting_value($settings, "reward_credits_users", 0);
	$reward_credits_checkout = get_setting_value($settings, "reward_credits_checkout", 0);

	// option price options
	$option_positive_price_right = get_setting_value($settings, "option_positive_price_right", ""); 
	$option_positive_price_left = get_setting_value($settings, "option_positive_price_left", ""); 
	$option_negative_price_right = get_setting_value($settings, "option_negative_price_right", ""); 
	$option_negative_price_left = get_setting_value($settings, "option_negative_price_left", "");

	$price_type = get_session("session_price_type");
	if ($price_type == 1) {
		$price_field = "trade_price";
		$sales_field = "trade_sales";
		$additional_price_field = "trade_additional_price";
		$properties_field = "trade_properties_price";
	} else {
		$price_field = "price";
		$sales_field = "sales_price";
		$additional_price_field = "additional_price";
		$properties_field = "properties_price";
	}

	$is_update = ($operation == "save");
	$same_as_personal = get_param("same_as_personal");

	if ($secure_order_profile) {
		$order_info_url = $secure_url . get_custom_friendly_url("order_info.php");
	} else {
		$order_info_url = $site_url . get_custom_friendly_url("order_info.php");
	}

	// prepare state_id, postal_code, city and country_id for use
	$state_id = ""; $postal_code = ""; $country_id = ""; $city=""; $address1="";
	if ($operation == "save" || $operation == "refresh") {
		if (isset($order_info["show_delivery_state_id"]) && $order_info["show_delivery_state_id"] == 1) {
			if ($same_as_personal == 1 && isset($order_info["show_state_id"]) && $order_info["show_state_id"] == 1) {
				$state_id = get_param("state_id");
			} else {
				$state_id = get_param("delivery_state_id");
			}
		} elseif (isset($order_info["show_state_id"]) && $order_info["show_state_id"] == 1) {
			$state_id = get_param("state_id");
		}

		if ($order_info["show_delivery_city"] == 1) {
			if ($same_as_personal == 1 && $order_info["show_city"] == 1) {
				$city = get_param("city");
			} else {
				$city = get_param("delivery_city");
			}
		} elseif ($order_info["show_city"] == 1) {
			$city = get_param("city");
		}
		// ** Begin Added by Egghead Ventures
		if ($order_info["show_delivery_address1"] == 1) {
			if ($same_as_personal == 1 && $order_info["show_address1"] == 1) {
				$address1 = get_param("address1");
			} else {
				$address1 = get_param("delivery_address1");
			}
		} elseif ($order_info["show_address1"] == 1) {
			$address1 = get_param("address1");
		}
		
		if ($order_info["show_delivery_address2"] == 1) {
			if ($same_as_personal == 1 && $order_info["show_address2"] == 1) {
				$address2 = get_param("address2");
			} else {
				$address2 = get_param("delivery_address2");
			}
		} elseif ($order_info["show_address2"] == 1) {
			$address2 = get_param("address2");
		}
		// ** End
		if ($order_info["show_delivery_zip"] == 1) {
			if ($same_as_personal == 1 && $order_info["show_zip"] == 1) {
				$postal_code = get_param("zip");
			} else {
				$postal_code = get_param("delivery_zip");
			}
		} elseif ($order_info["show_zip"] == 1) {
			$postal_code = get_param("zip");
		}

		if (isset($order_info["show_delivery_country_id"]) && $order_info["show_delivery_country_id"] == 1) {
			if ($same_as_personal == 1 && isset($order_info["show_country_id"]) && $order_info["show_country_id"] == 1) {
				$country_id = get_param("country_id");
			} else {
				$country_id = get_param("delivery_country_id");
			}
		} elseif (isset($order_info["show_country_id"]) && $order_info["show_country_id"] == 1) {
			$country_id = get_param("country_id");
		} else {
			$country_id = $settings["country_id"];
		}

	} elseif ($operation == "fast_checkout") {
		$basket_settings = va_page_settings("basket", 0);
		$fast_checkout_country_show = get_setting_value($basket_settings, "fast_checkout_country_show", 0);
		$fast_checkout_country_required = get_setting_value($basket_settings, "fast_checkout_country_required", 0);
		$fast_checkout_state_show = get_setting_value($basket_settings, "fast_checkout_state_show", 0);
		$fast_checkout_state_required = get_setting_value($basket_settings, "fast_checkout_state_required", 0);
		$fast_checkout_postcode_show = get_setting_value($basket_settings, "fast_checkout_postcode_show", 0);
		$fast_checkout_postcode_required = get_setting_value($basket_settings, "fast_checkout_postcode_required", 0);
		$country_id = get_param("fast_checkout_country_id");
		$state_id = get_param("fast_checkout_state_id");
		$postal_code = get_param("fast_checkout_postcode");
		if ($fast_checkout_country_show && $fast_checkout_country_required && !strlen($country_id)) {
			$sc_errors .= str_replace("{field_name}", COUNTRY_FIELD, REQUIRED_MESSAGE) . "<br>\n";
		}
		if ($fast_checkout_state_show && $fast_checkout_state_required && !strlen($state_id)) {
			$sc_errors .= str_replace("{field_name}", STATE_FIELD, REQUIRED_MESSAGE) . "<br>\n";
		}
		if ($fast_checkout_postcode_show && $fast_checkout_postcode_required && !strlen($postal_code)) {
			$sc_errors .= str_replace("{field_name}", ZIP_FIELD, REQUIRED_MESSAGE) . "<br>\n";
		}
	} else {
		$delivery_details = get_delivery_details($order_info);

		$state_id = $delivery_details["state_id"];
		$postal_code = $delivery_details["postal_code"];
		$city = $delivery_details["city"];
		// ** Begin Added by Egghead Ventures
		$address1 = $delivery_details["address1"];
		$address2 = $delivery_details["address2"];
		//** End
		if ((isset($order_info["show_delivery_country_id"]) && $order_info["show_delivery_country_id"] == 1) 
			|| (isset($order_info["show_country_id"]) && $order_info["show_country_id"] == 1)) {
			$country_id = $delivery_details["country_id"];
		} else {
			$country_id = get_setting_value($settings, "country_id", "");
		}
	}

	// determining country_code and state_code that are used by shipping modules, VAT check, etc...
	$country_code = get_db_value("SELECT country_code FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($country_id, INTEGER));
	$state_code = get_db_value("SELECT state_code FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($state_id, INTEGER));
	
	$variables = array();
	$variables["charset"] = CHARSET;
	$variables["site_url"] = $settings["site_url"];
	$variables["secure_url"] = $secure_url;
	$variables["http_host"] = get_var("HTTP_HOST");
	$variables["session_id"] = session_id();
	$variables["user_ip"] = $user_ip;
	$variables["order_ip"] = $user_ip;
	$variables["initial_ip"] = $initial_ip;
	$variables["cookie_ip"] = $cookie_ip;

	$t->set_var("order_info_href", get_custom_friendly_url("order_info.php"));
	$t->set_var("current_href",  get_custom_friendly_url("order_info.php"));
	$t->set_var("order_info_url",  $order_info_url);
	$t->set_var("currency_left", $currency["left"]);
	$t->set_var("currency_right", $currency["right"]);
	$t->set_var("currency_rate", htmlspecialchars($currency["rate"]));
	$t->set_var("currency_decimals", htmlspecialchars($currency["decimals"]));
	$t->set_var("currency_point", htmlspecialchars($currency["point"]));
	$t->set_var("currency_separator", htmlspecialchars($currency["separator"]));
	$t->set_var("tax_prices_type", $tax_prices_type);
	$t->set_var("referer", $referer);
	$t->set_var("referrer", $referer);
	$t->set_var("HTTP_REFERER", $referer);
	$t->set_var("initial_ip", $initial_ip);
	$t->set_var("cookie_ip", $cookie_ip);
	$t->set_var("user_ip", $user_ip);
	$t->set_var("remote_address", $user_ip);
	$t->set_var("visit_number", $visit_number);
	$t->set_var("points_msg", strtolower(POINTS_MSG));
	$t->set_var("points_balance_value", $points_balance);
	$t->set_var("points_rate", $points_conversion_rate);
	$t->set_var("points_decimals", $points_decimals);
	$t->set_var("credit_balance_value", $credit_balance);

	// prepare custom options
	$options_errors = "";
	$properties_total = 0; $properties_taxable = 0; $properties_points_amount = 0; 
	$order_properties = ""; $op_rows = array(); $pn = 0;
	$custom_options = array();
	$sql  = " SELECT * ";
	$sql .= " FROM " . $table_prefix . "order_custom_properties ";
	$sql .= " WHERE property_type IN (1,2,3) AND property_show IN (0,1) "; // show not hidden properties for all orders and web orders
	if (isset($site_id)) {
		$sql .= " AND site_id=" . $db->tosql($site_id, INTEGER, true, false);
	} else {
		$sql .= " AND site_id=1";
	}
	$sql .= " ORDER BY property_order, property_id ";

	$db->query($sql);
	if ($db->next_record()) {
		do {
			$op_rows[$pn]["property_id"] = $db->f("property_id");
			$op_rows[$pn]["property_order"] = $db->f("property_order");
			$op_rows[$pn]["property_name"] = $db->f("property_name");
			$op_rows[$pn]["property_description"] = $db->f("property_description");
			$op_rows[$pn]["default_value"] = $db->f("default_value");
			$op_rows[$pn]["property_type"] = $db->f("property_type");
			$op_rows[$pn]["property_style"] = $db->f("property_style");
			$op_rows[$pn]["control_type"] = $db->f("control_type");
			$op_rows[$pn]["control_style"] = $db->f("control_style");
			$op_rows[$pn]["required"] = $db->f("required");
			$op_rows[$pn]["tax_free"] = $db->f("tax_free");
			$op_rows[$pn]["before_name_html"] = $db->f("before_name_html");
			$op_rows[$pn]["after_name_html"] = $db->f("after_name_html");
			$op_rows[$pn]["before_control_html"] = $db->f("before_control_html");
			$op_rows[$pn]["after_control_html"] = $db->f("after_control_html");
			$op_rows[$pn]["onchange_code"] = $db->f("onchange_code");
			$op_rows[$pn]["onclick_code"] = $db->f("onclick_code");
			$op_rows[$pn]["control_code"] = $db->f("control_code");
			$op_rows[$pn]["validation_regexp"] = $db->f("validation_regexp");
			$op_rows[$pn]["regexp_error"] = ($db->f("regexp_error")) ? get_translation($db->f("regexp_error")) : INCORRECT_VALUE_MESSAGE;

			$pn++;
		} while ($db->next_record());
	}

	// VAT validation
	$tax_free = false; $vat_parameter = ""; $vat_number = ""; $is_vat_valid = false; 
	// add $vat_validation = true; into includes/var_definition.php to activate this validation
	if (isset($vat_validation) && $vat_validation) {
		// check vat_parameter
		if (sizeof($op_rows) > 0) {
			for ($pn = 0; $pn < sizeof($op_rows); $pn++) {
				$property_id = $op_rows[$pn]["property_id"];
				$property_name = $op_rows[$pn]["property_name"];

				if (preg_match("/vat/i", $property_name)) {
					$vat_parameter = "op_" . $property_id;
					break;
				}
			}
		}
		if ($vat_parameter) {
			include("./includes/vat_check.php");
			$vat_number = get_param($vat_parameter);
			
			if ($vat_number) {
				$is_vat_valid = vat_check($vat_number, $country_code);
				if ($is_vat_valid) {
					if (!isset($vat_obligatory_countries) || !is_array($vat_obligatory_countries)
					|| !in_array(strtoupper($country_code), $vat_obligatory_countries)) {
						$tax_free = true; 
					}
				} else {
					$sc_errors .= "Your VAT Number is invalid. Please check it and try again.<br>";
				}
			}
		}
	}

	// get taxes rates
	$tax_available = false; $tax_percent_sum = 0; $tax_names = "";	$taxes_total = 0; 
	$default_tax_rates = get_tax_rates(true); 
	$tax_rates = get_tax_rates(true, $country_id, $state_id, $postal_code);
	if (sizeof($tax_rates) > 0) {
		$tax_available = true;
		foreach ($tax_rates as $tax_id => $tax_info) {
			$tax_percent_sum += $tax_info["tax_percent"];
			if ($tax_names) { $tax_names .= " & "; }
			$tax_names .= get_translation($tax_info["tax_name"]);
		}
	}

	$t->set_var("tax_name", $tax_names);
	$t->set_var("tax_note", $tax_note);
	$t->set_var("tax_note_excl", $tax_note_excl);

	$goods_colspan = 0; $total_columns = 0;
	if ($item_image_column) {
		$goods_colspan++;
		$total_columns++;
		$t->parse("item_image_header", false);
	}
	if ($item_name_column) {
		$goods_colspan++;
		$total_columns++;
		$t->parse("item_name_header", false);
	}
	if ($item_price_column || ($item_price_incl_tax_column && !$tax_available)) {
		$item_price_column = true;
		$goods_colspan++;
		$total_columns++;
		$t->parse("item_price_header", false);
	}
	if ($item_tax_percent_column && $tax_available) {
		$goods_colspan++;
		$total_columns++;
		$t->parse("item_tax_percent_header", false);
	} else {
		$item_tax_percent_column = false;
	}
	if ($item_tax_column && $tax_available) {
		$goods_colspan++;
		$total_columns++;
		$t->parse("item_tax_header", false);
	} else {
		$item_tax_column = false;
	}
	if ($item_price_incl_tax_column && $tax_available) {
		$goods_colspan++;
		$total_columns++;
		$t->parse("item_price_incl_tax_header", false);
	} else {
		$item_price_incl_tax_column = false;
	}
	if ($item_quantity_column) {
		$goods_colspan++;
		$total_columns++;
		$t->parse("item_quantity_header", false);
	}
	if ($item_price_total_column || ($item_price_incl_tax_total_column && !$tax_available)) {
		$item_price_total_column = true;
		$total_columns++;
		$t->parse("item_price_total_header", false);
	}
	if ($item_tax_total_column && $tax_available) {
		$total_columns++;
		$t->parse("item_tax_total_header", false);
	} else {
		$item_tax_total_column = false;
	}
	if ($item_price_incl_tax_total_column && $tax_available) {
		$total_columns++;
		$t->parse("item_price_incl_tax_total_header", false);
	} else {
		$item_price_incl_tax_total_column = false;
	}
	$sc_colspan = $total_columns - 1;
	$t->set_var("goods_colspan", $goods_colspan);
	$t->set_var("sc_colspan", $sc_colspan);
	$t->set_var("total_columns", $total_columns);

	$items_text = ""; $order_coupons_ids = ""; $vouchers_ids = ""; $gift_vouchers = array();

	$total_buying = 0; $total_buying_tax = 0; 
	$goods_total_full = 0; $goods_total = 0; $goods_tax_total = 0; 
	$goods_total_excl_tax = 0; $goods_total_incl_tax = 0;
	$goods_points_amount = 0; 
	$total_items = 0;
	$total_discount = 0; $total_discount_excl_tax = 0; $total_discount_tax = 0; $total_discount_incl_tax = 0;
	$vouchers_amount = 0; $order_total = 0;
	$max_availability_time = 0; $shipping_time = 0;
	$free_postage = false; 
	if ($user_tax_free) { $tax_free = $user_tax_free; }
	$recurring_items = false;

	// show custom options
	if (sizeof($op_rows) > 0)
	{
		for ($pn = 0; $pn < sizeof($op_rows); $pn++) {
			$property_id = $op_rows[$pn]["property_id"];
			$property_order  = $op_rows[$pn]["property_order"];
			$property_name_initial = $op_rows[$pn]["property_name"];
			$property_name = get_translation($property_name_initial);
			$property_description = $op_rows[$pn]["property_description"];
			$default_value = $op_rows[$pn]["default_value"];
			$property_type = $op_rows[$pn]["property_type"];
			$property_style = $op_rows[$pn]["property_style"];
			$control_type = $op_rows[$pn]["control_type"];
			$control_style = $op_rows[$pn]["control_style"];
			$property_required = $op_rows[$pn]["required"];
			$property_tax_free = $op_rows[$pn]["tax_free"];
			if ($tax_free) { $property_tax_free = $tax_free; }
			$before_name_html = $op_rows[$pn]["before_name_html"];
			$after_name_html = $op_rows[$pn]["after_name_html"];
			$before_control_html = $op_rows[$pn]["before_control_html"];
			$after_control_html = $op_rows[$pn]["after_control_html"];
			$onchange_code = $op_rows[$pn]["onchange_code"];
			$onclick_code = $op_rows[$pn]["onclick_code"];
			$control_code = $op_rows[$pn]["control_code"];
			$validation_regexp = $op_rows[$pn]["validation_regexp"];
			$regexp_error = $op_rows[$pn]["regexp_error"];

			if ($property_type > 0) {
				if (strlen($order_properties)) { $order_properties .= ","; }
				$order_properties .= $property_id;
			}

			$selected_price = 0; $selected_points_price = 0; $property_prices = 0; $property_pay_points = 0;
			$property_control  = "";
			$property_control .= "<input type=\"hidden\" name=\"op_name_" . $property_id . "\"";
			$property_control .= " value=\"" . strip_tags($property_name) . "\">";
			$property_control .= "<input type=\"hidden\" name=\"op_required_" . $property_id . "\"";
			$property_control .= " value=\"" . intval($property_required) . "\">";
			$property_control .= "<input type=\"hidden\" name=\"op_control_" . $property_id . "\"";
			$property_control .= " value=\"" . strtoupper($control_type) . "\">";
			$property_control .= "<input type=\"hidden\" name=\"op_tax_free_" . $property_id . "\"";
			$property_control .= " value=\"" . intval($property_tax_free) . "\">";


			$sql  = " SELECT * FROM " . $table_prefix . "order_custom_values ";
			$sql .= " WHERE property_id=" . $property_id . " AND hide_value=0";
			$sql .= " ORDER BY property_value_id ";
			if (strtoupper($control_type) == "LISTBOX") {
				$selected_value = get_param("op_" . $property_id);
				$property_pay_points = get_param("property_pay_points_" . $property_id);
				$properties_prices = "";
				$properties_values = "<option value=\"\">" . SELECT_MSG . " " . $property_name . "</option>" . $eol;
				$db->query($sql);
				while ($db->next_record())
				{
					$property_value_original = $db->f("property_value");
					$property_value = get_translation($property_value_original);
					$property_price = $db->f("property_price");
					if ($property_price != 0) {
						$property_prices = 1;
					}
					$property_value_id = $db->f("property_value_id");
					$is_default_value = $db->f("is_default_value");
					$property_selected  = "";
					$properties_prices .= "<input type=\"hidden\" name=\"op_option_price_" . $property_value_id . "\"";
					$properties_prices .= " value=\"" . $property_price . "\">";
					if (strlen($operation)) {
						if ($selected_value == $property_value_id) {
							$property_selected  = "selected ";
							$selected_price    += $property_price;
							$selected_points_price = round($selected_price * $points_conversion_rate, $points_decimals);
							if (!$points_system || !$points_orders_options || $selected_points_price > $points_balance) {
								$selected_points_price = 0; $property_pay_points = 0;
							}
							$custom_options[$property_id][] = array(
								"type" => $property_type, "order" => $property_order, "name" => $property_name_initial, 
								"value_id" => $property_value_id, "value" => $property_value_original, "price" => $selected_price, "tax_free" => $property_tax_free,
								"points_price" => $selected_points_price, "pay_points" => $property_pay_points
							);
						}
					} elseif ($is_default_value) {
						$property_selected  = "selected ";
						$selected_price    += $property_price;
					}

					$properties_values .= "<option " . $property_selected . "value=\"" . htmlspecialchars($property_value_id) . "\">";
					$properties_values .= htmlspecialchars($property_value);

					$property_tax_percent = $tax_percent_sum;
					// get tax to show price
					$property_tax = get_tax_amount($tax_rates, 0, $property_price, $property_tax_free, $property_tax_percent, $default_tax_rates);
					if ($tax_prices_type == 1) {
						$property_price_incl = $property_price;
						$property_price_excl = $property_price - $property_tax;
					} else {
						$property_price_incl = $property_price + $property_tax;
						$property_price_excl = $property_price;
					}
					if ($tax_prices == 2 || $tax_prices == 3) {
						// show property with tax
						$shown_price = $property_price_incl;
					} else {
						$shown_price = $property_price_excl;
					}

					if ($property_price > 0) {
						$properties_values .= $option_positive_price_right . currency_format($shown_price) . $option_positive_price_left;
					} elseif ($property_price < 0) {
						$properties_values .= $option_negative_price_right . currency_format(abs($shown_price)) . $option_negative_price_left;
					}
					$properties_values .= "</option>" . $eol;
				}
				$property_control .= $before_control_html;
				$property_control .= "<select name=\"op_" . $property_id . "\" onChange=\"changeProperty();";
				if ($onchange_code) {	$property_control .= $onchange_code; }
				$property_control .= "\"";
				if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
				if ($control_code) {	$property_control .= " " . $control_code . " "; }
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				$property_control .= ">" . $properties_values . "</select>";
				$property_control .= $properties_prices;
				$property_control .= $after_control_html;
			} elseif (strtoupper($control_type) == "RADIOBUTTON" || strtoupper($control_type) == "CHECKBOXLIST") {
				$is_radio = (strtoupper($control_type) == "RADIOBUTTON");
				$property_pay_points = get_param("property_pay_points_" . $property_id);

				$selected_value = array();
				if (strlen($operation)) {
					if ($is_radio) {
						$selected_value[] = get_param("op_" . $property_id);
					} else {
						$total_options = get_param("op_total_" . $property_id);
						for ($op = 1; $op <= $total_options; $op++) {
							$selected_value[] = get_param("op_" . $property_id . "_" . $op);
						}
					}
				}

				$input_type = $is_radio ? "radio" : "checkbox";
				$property_control .= "<span";
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				$property_control .= ">";
				$value_number = 0; 
				$db->query($sql);
				while ($db->next_record())
				{
					$value_number++;
					$property_price = $db->f("property_price");
					if ($property_price != 0) {
						$property_prices = 1;
					}
					$property_value_id = $db->f("property_value_id");
					$item_code = $db->f("item_code");
					$manufacturer_code = $db->f("manufacturer_code");
					$is_default_value = $db->f("is_default_value");
					$property_value_original = $db->f("property_value");
					$property_value = get_translation($property_value_original);
					$property_checked = "";
					$property_control .= $before_control_html;
					$property_control .= "<input type=\"hidden\" name=\"op_option_price_" . $property_value_id . "\"";
					$property_control .= " value=\"" . $property_price . "\">";
					if (strlen($operation)) {
						if (in_array($property_value_id, $selected_value)) {
							$property_checked = "checked ";
							$selected_price  += $property_price;
							$property_points_price = round($property_price * $points_conversion_rate, $points_decimals);
							$selected_points_price += $property_points_price;
							if (!$points_system || !$points_orders_options || $selected_points_price > $points_balance) {
								$selected_points_price = 0; $property_pay_points = 0; $property_points_price = 0;
							}
							$custom_options[$property_id][] = array(
								"type" => $property_type, "order" => $property_order, "name" => $property_name_initial, 
								"value_id" => $property_value_id, "value" => $property_value_original, "price" => $property_price, "tax_free" => $property_tax_free,
								"points_price" => $property_points_price, "pay_points" => $property_pay_points
							);
						}
					} elseif ($is_default_value) {
						$property_checked = "checked ";
						$selected_price  += $property_price;
					}

					$control_name = ($is_radio) ? ("op_".$property_id) : ("op_".$property_id."_".$value_number);
					$property_control .= "<input type=\"" . $input_type . "\" name=\"" . $control_name . "\" ". $property_checked;
					$property_control .= "value=\"" . htmlspecialchars($property_value_id) . "\" onClick=\"changeProperty(); ";
					if ($onclick_code) {
						$control_onclick_code = str_replace("{option_value}", $property_value, $onclick_code);
						$property_control .= $control_onclick_code;
					}
					$property_control .= "\"";
					if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
					if ($control_code) {	$property_control .= " " . $control_code . " "; }
					$property_control .= ">";
					$property_control .= $property_value;

					$property_tax_percent = $tax_percent_sum;
					// get tax to show price
					$property_tax = get_tax_amount($tax_rates, 0, $property_price, $property_tax_free, $property_tax_percent, $default_tax_rates);
					if ($tax_prices_type == 1) {
						$property_price_incl = $property_price;
						$property_price_excl = $property_price - $property_tax;
					} else {
						$property_price_incl = $property_price + $property_tax;
						$property_price_excl = $property_price;
					}
					if ($tax_prices == 2 || $tax_prices == 3) {
						// show property with tax
						$shown_price = $property_price_incl;
					} else {
						$shown_price = $property_price_excl;
					}

					if ($property_price > 0) {
						$property_control .= $option_positive_price_right . currency_format($shown_price) . $option_positive_price_left;
					} elseif ($property_price < 0) {
						$property_control .= $option_negative_price_right . currency_format(abs($shown_price)) . $option_negative_price_left;
					}
					$property_control .= $after_control_html;
				}
				$property_control .= "</span>";
				$property_control .= "<input type=\"hidden\" name=\"op_total_".$property_id."\" value=\"".$value_number."\">";
			} elseif (strtoupper($control_type) == "TEXTBOX") {
				if (strlen($operation)) {
					$control_value = get_param("op_" . $property_id);
					if (strlen($control_value)) {
						$custom_options[$property_id][] = array(
							"type" => $property_type, "order" => $property_order, "name" => $property_name_initial, 
							"value_id" => "", "value" => $control_value, "price" => 0, "tax_free" => 0,
							"points_price" => 0, "pay_points" => 0
						);
					}
				} else {
					$control_value = $default_value;
				}
				$property_control .= $before_control_html;
				$property_control .= "<input type=\"text\" name=\"op_" . $property_id . "\"";
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
				if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
				if ($control_code) {	$property_control .= " " . $control_code . " "; }
				$property_control .= " value=\"". htmlspecialchars($control_value) . "\">";
				$property_control .= $after_control_html;
			} elseif (strtoupper($control_type) == "TEXTAREA") {
				if (strlen($operation)) {
					$control_value = get_param("op_" . $property_id);
					if (strlen($control_value)) {
						$custom_options[$property_id][] = array(
							"type" => $property_type, "order" => $property_order, "name" => $property_name_initial, 
							"value_id" => "", "value" => $control_value, "price" => 0, "tax_free" => 0,
							"points_price" => 0, "pay_points" => 0
						);
					}
				} else {
					$control_value = $default_value;
				}
				$property_control .= $before_control_html;
				$property_control .= "<textarea name=\"op_" . $property_id . "\"";
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
				if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
				if ($control_code) {	$property_control .= " " . $control_code . " "; }
				$property_control .= ">". htmlspecialchars($control_value) ."</textarea>";
				$property_control .= $after_control_html;
			} else {
				$property_control .= $before_control_html;
				if ($property_required) {
					$property_control .= "<input type=\"hidden\" name=\"op_" . $property_id . "\" value=\"" . htmlspecialchars($property_description) . "\">";
				}
				if (strlen($default_value)) {
					$custom_options[$property_id][] = array(
						"type" => $property_type, "order" => $property_order, "name" => $property_name_initial, 
						"value_id" => "", "value" => $default_value, "price" => 0, "tax_free" => 0,
						"points_price" => 0, "pay_points" => 0
					);
				}
				$property_control .= "<span";
				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
				if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
				if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
				if ($control_code) {	$property_control .= " " . $control_code . " "; }
				$property_control .= ">" . get_translation($default_value) . "</span>";
				$property_control .= $after_control_html;
			}

			// get taxes for selected properties and add it to total values 
			$selected_tax_amount = get_tax_amount($tax_rates, 0, $selected_price, $property_tax_free, $property_tax_percent, $default_tax_rates);
			$selected_tax_values = get_tax_amount($tax_rates, 0, $selected_price, $property_tax_free, $property_tax_percent, $default_tax_rates, 2);
			$property_tax_total = add_tax_values($tax_rates, $selected_tax_values, "properties");

			if ($property_pay_points) {
				$properties_points_amount += $selected_points_price;
			} else {
				$properties_total += $selected_price;
				if ($property_tax_free != 1) {
					$properties_taxable += $selected_price;
				}
			}
			$t->set_var("property_id", $property_id);
			//$t->set_var("property_block_id", $property_block_id);
			$t->set_var("property_name", $before_name_html . $property_name . $after_name_html);
			if ($selected_price == 0 || $property_pay_points) {
				$t->set_var("op_price_excl_tax", "");
				$t->set_var("op_tax", "");
				$t->set_var("op_price_incl_tax", "");
			} else {
				if ($tax_prices_type == 1) {
					$op_price_excl_tax = $selected_price - $selected_tax_amount;
					$op_price_incl_tax = $selected_price;
				} else {
					$op_price_excl_tax = $selected_price;
					$op_price_incl_tax = $selected_price + $selected_tax_amount;
				}
				$t->set_var("op_price_excl_tax", currency_format($op_price_excl_tax));
				$t->set_var("op_tax", currency_format($selected_tax_amount));
				$t->set_var("op_price_incl_tax", currency_format($op_price_incl_tax));
			}
			$t->set_var("property_style", $property_style);
			$t->set_var("property_control", $property_control);
			if ($property_required) {
				$t->set_var("property_required", "*");
			} else {
				$t->set_var("property_required", "");
			}

			if ($operation == "save" && $property_required && !isset($custom_options[$property_id])) {
				$property_message = str_replace("{field_name}", $property_name, REQUIRED_MESSAGE) . "<br>";
				if ($property_type == 1) {
					$sc_errors .= $property_message;
				} elseif ($property_type == 2 || $property_type == 3) {
					$options_errors .= $property_message;
				}
			}

			// check option with regexp
			$regexp_valid = true;
			if ($operation == "save" && isset($custom_options[$property_id]) && strlen($validation_regexp)) {
				$validation_value = "";
				foreach ($custom_options[$property_id] as $option_id => $option_data) {
					if (strval($validation_value) != "") { $validation_value .= ","; }
					$validation_value .= $option_data["value"];
				}
				if (!preg_match($validation_regexp, $validation_value)) {
					$regexp_valid = false;
				}
			}
			if (!$regexp_valid) {
				$property_message = str_replace("{field_name}", $property_name, $regexp_error) . "<br>";
				if ($property_type == 1) {
					$sc_errors .= $property_message;
				} elseif ($property_type == 2 || $property_type == 3) {
					$options_errors .= $property_message;
				}
			}

			if ($points_system && $points_orders_options && $property_prices && $points_balance > 0) {
				if ($property_pay_points) {
					$t->set_var("property_pay_points_checked", "checked");
				} else {
					$t->set_var("property_pay_points_checked", "");
				}
				$t->parse("property_points_price_block", false);
			} else {
				$t->set_var("property_points_price_block", "");
			}

			if ($property_type == 1) {
				if ($item_price_total_column) {
					$t->parse("property_price_excl_tax_column", false);
				}
				if ($item_tax_total_column) {
					$t->parse("property_tax_column", false);
				}
				if ($item_price_incl_tax_total_column) {
					$t->parse("property_price_incl_tax_column", false);
				}
				$t->parse("cart_properties", true);
			} elseif ($property_type == 2) {
				$t->parse("personal_properties", true);
			} elseif ($property_type == 3) {
				$t->parse("delivery_properties", true);
			}
		}

		$t->set_var("order_properties", $order_properties);
		$t->set_var("properties_total", $properties_total);
		$t->set_var("properties_taxable", $properties_taxable);

	}
	// end custom options

	$coupons = get_session("session_coupons"); $quantities_discounts = array();
	$order_coupons = array();
	$cart_items = array(); $cart_ids = array(); $stock_levels = array(); $options_stock_levels = array();
	if (!strlen($user_id)) $user_id = 0;

	if (is_array($shopping_cart))
	{
		$properties_ids = "";
		// #1 - prepare cart items
		foreach ($shopping_cart as $cart_id => $item)
		{
			$item_id = $item["ITEM_ID"];
			$wishlist_item_id = $item["CART_ITEM_ID"];
			$quantity = $item["QUANTITY"];
			$subscription_id = isset($item["SUBSCRIPTION_ID"]) ? $item["SUBSCRIPTION_ID"] : "";
			// check subscription
			if ($subscription_id) {
				$sql  = " SELECT is_subscription_recurring, user_type_id, subscription_name, subscription_fee, ";
				$sql .= " subscription_period, subscription_interval, subscription_suspend, ";
				$sql .= " subscription_affiliate_type, subscription_affiliate_amount, subscription_points_type, ";
				$sql .= " subscription_points_amount, subscription_credits_type, subscription_credits_amount ";
				$sql .= " FROM " . $table_prefix . "subscriptions ";
				$sql .= " WHERE subscription_id=" . $db->tosql($subscription_id, INTEGER) . " AND is_active=1 ";
				$db->query($sql);
				if ($db->next_record()) {
					$total_items++;
					$is_recurring = $db->f("is_subscription_recurring");
					$subscription_type_id = $db->f("user_type_id");
					$is_account_subscription = ($subscription_type_id) ? 1 : 0;
					$subscription_fee = $db->f("subscription_fee");
					$subscription_name_initial = $db->f("subscription_name");
					$subscription_name = get_translation($subscription_name_initial);
					$subscription_period = $db->f("subscription_period");
					$subscription_interval = $db->f("subscription_interval");
					$subscription_suspend = $db->f("subscription_suspend");

					$subscription_affiliate_type = $db->f("subscription_affiliate_type");
					$subscription_affiliate_amount = $db->f("subscription_affiliate_amount");
					$subscription_points_type = $db->f("subscription_points_type");
					$subscription_points_amount = $db->f("subscription_points_amount");
					$subscription_credits_type = $db->f("subscription_credits_type");
					$subscription_credits_amount = $db->f("subscription_credits_amount");

					if ($is_recurring) {
						$recurring_period = $subscription_period;
						$recurring_interval = $subscription_interval;
					} else {
						$recurring_period = ""; $recurring_interval = "";
					}

					// re-calculate price in case if prices include some default tax rate 
					$subscription_item_tax = get_tax_amount($tax_rates, 0, $subscription_fee, $tax_free, $subscription_tax_percent, $default_tax_rates);

					$cart_item_id = $cart_id;
					$cart_items[$cart_item_id] = array(
						"parent_cart_id" => "", "is_bundle" => 0, "top_order_item_id" => 0,
						"item_id" => 0, "id" => 0, "product_id" => 0, "parent_item_id" => 0,
						"item_user_id" => 0, "item_type_id" => 0, "supplier_id" => 0, "wishlist_item_id" => $wishlist_item_id,
						"item_code" => "", "manufacturer_code" => "", 
						"selection_name" => "", "selection_order" => "",
						"item_image" => "", "item_image_alt" => "",
						"packages_number" => 0, "width" => 0, "height" => 0, "length" => 0,
						"weight" => 0, "shipping_cost" => 0, "is_shipping_free" => 1, "is_country_restriction" => 0,
						"price" => $subscription_fee, "quantity" => $quantity, 
						"tax_free" => $tax_free, "tax_percent" => $subscription_tax_percent,
						"real_price" => $subscription_fee, "discount_amount" => 0, 
						"coupons" => "", "coupons_ids" => "",
						"affiliate_type" => $subscription_affiliate_type, "affiliate_amount" => $subscription_affiliate_amount,
						"merchant_type" => 0, "merchant_amount" => 0,
						"is_points_price" => 0, "points_price" => 0, 
						"reward_type" => $subscription_points_type, "reward_amount" => $subscription_points_amount, 
						"credit_reward_type" => $subscription_credits_type, "credit_reward_amount" => $subscription_credits_amount, 
						"coupons" => "", "coupons_ids" => "", "coupons_discount" => 0, 
						"buying_price" => 0, "item_name" => $subscription_name_initial, "product_name" => $subscription_name_initial,
						"product_title" => $subscription_name_initial, "item_title" => $subscription_name_initial, 
						"discount_applicable" => 0, "properties_discount" => 0, 
						"properties_info" => "", "properties_html" => "", "properties_text" => "",
						"downloadable" => 0, "downloads" => "", 
						"stock_level" => "", "availability_time" => "",
						"short_description" => "", "description" => "", "full_description" => "",
						"generate_serial" => 0, "serial_period" => "", "activations_number" => "", "is_gift_voucher" => 0,
						"is_recurring" => $is_recurring, "recurring_price" => "", "recurring_period" => $recurring_period,
						"recurring_interval" => $recurring_interval, "recurring_payments_total" => "",
						"recurring_start_date" => "", "recurring_end_date" => "",
						"is_subscription" => 1, "is_account_subscription" => $is_account_subscription, 
						"subscription_id" => $subscription_id, "subscription_period" => $subscription_period,
						"subscription_interval" => $subscription_interval, "subscription_suspend" => $subscription_suspend,
					);
				}
				continue;
			}

			$properties_more = $item["PROPERTIES_MORE"];
			if ($properties_more) {
				continue;
			}

			$properties = $item["PROPERTIES"];
			$components = $item["COMPONENTS"];
			$item_coupons = isset($item["COUPONS"]) ? $item["COUPONS"] : "";
			if (VA_Products::check_permissions($item_id, VIEW_ITEMS_PERM)) {
				$sql  = " SELECT i.item_code, i.manufacturer_code, i.item_type_id, i.supplier_id, i.user_id, i.item_name, i.short_description, i.full_description, ";
				$sql .= " i.buying_price, i." . $price_field . ", i.is_price_edit, i.is_sales, i." . $sales_field . ", i.tax_free, ";
				$sql .= " i.is_points_price, i.points_price, i.reward_type, i.reward_amount, i.credit_reward_type, i.credit_reward_amount, ";
				$sql .= " i.packages_number, i.width, i.height, i.length, ";
				$sql .= " i.stock_level, i.use_stock_level, i.hide_out_of_stock, i.disable_out_of_stock, i.weight, ";
				$sql .= " i.merchant_fee_type AS item_merchant_type, i.merchant_fee_amount AS item_merchant_amount, i.affiliate_commission_type AS item_affiliate_type, i.affiliate_commission_amount AS item_affiliate_amount, ";
				$sql .= " it.merchant_fee_type AS type_merchant_type, it.merchant_fee_amount AS type_merchant_amount, it.affiliate_commission_type AS type_affiliate_type, it.affiliate_commission_amount AS type_affiliate_amount, ";
				$sql .= " i.downloadable, i.download_period, i.download_path, i.generate_serial, i.serial_period, i.activations_number, ";
				$sql .= " st_in.availability_time AS in_stock_availability , st_out.availability_time AS out_stock_availability, ";
				$sql .= " i.shipping_rule_id, sr.is_country_restriction, i.shipping_cost, i.is_shipping_free, ";
				$sql .= " i.is_recurring, i.recurring_price, i.recurring_period, i.recurring_interval, ";
				$sql .= " i.recurring_payments_total, i.recurring_start_date, i.recurring_end_date, ";
				$sql .= " it.is_gift_voucher, it.is_bundle, ";
				$sql .= " i.tiny_image, i.tiny_image_alt, i.small_image, i.small_image_alt, i.big_image, i.big_image_alt, i.super_image ";
				$sql .= " FROM ((((" . $table_prefix . "items i ";			
				$sql .= " LEFT JOIN " . $table_prefix . "item_types it ON i.item_type_id=it.item_type_id) ";
				$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_in ON i.shipping_in_stock=st_in.shipping_time_id) ";
				$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_out ON i.shipping_out_stock=st_out.shipping_time_id) ";
				$sql .= " LEFT JOIN " . $table_prefix . "shipping_rules sr ON i.shipping_rule_id=sr.shipping_rule_id) ";
				$sql .= " WHERE i.item_id=" . $db->tosql($item_id, INTEGER);
							
				$db->query($sql);
				if ($db->next_record())
				{
					$total_items++;
	
					$price = $db->f($price_field);
					$is_price_edit = $db->f("is_price_edit");
					if ($is_price_edit) {
						$price = $item["PRICE"];
					}
					$item_type_id = $db->f("item_type_id");
					$supplier_id = $db->f("supplier_id");
					$item_user_id = $db->f("user_id");
					$is_sales = $db->f("is_sales");
					$sales_price = $db->f($sales_field);
					$item_code = $db->f("item_code");
					$manufacturer_code = $db->f("manufacturer_code");
					$buying_price = $db->f("buying_price");
					// points data
					$is_points_price = $db->f("is_points_price");
					$points_price = $db->f("points_price");
					$reward_type = $db->f("reward_type");
					$reward_amount = $db->f("reward_amount");
					$credit_reward_type = $db->f("credit_reward_type");
					$credit_reward_amount = $db->f("credit_reward_amount");
	
					$item_name_initial = $db->f("item_name");
					$item_name = get_translation($item_name_initial);
					$downloadable = $db->f("downloadable");
					$download_period = $db->f("download_period");
					$download_path = $db->f("download_path");
					$generate_serial = $db->f("generate_serial");
					$serial_period = $db->f("serial_period");
					$activations_number = $db->f("activations_number");
					$is_gift_voucher = $db->f("is_gift_voucher");
					$is_bundle = $db->f("is_bundle");
					$stock_level = $db->f("stock_level");
					$use_stock_level = $db->f("use_stock_level");
					$hide_out_of_stock = $db->f("hide_out_of_stock");
					$disable_out_of_stock = $db->f("disable_out_of_stock");
					if ($stock_level > 0) {
						$availability_time = $db->f("in_stock_availability");
					} else {
						$availability_time = $db->f("out_stock_availability");
					}
					if ($availability_time > $max_availability_time) {
						$max_availability_time = $availability_time;
					}
					$shipping_rule_id = $db->f("shipping_rule_id");
					$is_country_restriction = $db->f("is_country_restriction");
	
					$packages_number = $db->f("packages_number");
					$weight = $db->f("weight");
					$width = $db->f("width");
					$height = $db->f("height");
					$length = $db->f("length");
					$is_shipping_free = $db->f("is_shipping_free");
					$shipping_cost = $db->f("shipping_cost");
					if ($is_shipping_free) { $shipping_cost = 0; }
					$item_tax_free = $db->f("tax_free");
					if ($tax_free) { $item_tax_free = $tax_free; }
					$short_description = strip_tags($db->f("short_description"));
					$full_description = strip_tags($db->f("full_description"));
					// get commission fields
					$item_merchant_type = $db->f("item_merchant_type");
					$item_merchant_amount = $db->f("item_merchant_amount");
					$item_affiliate_type = $db->f("item_affiliate_type");
					$item_affiliate_amount = $db->f("item_affiliate_amount");
					if (!strlen($item_merchant_type)) {
						$item_merchant_type = $db->f("type_merchant_type");
						$item_merchant_amount = $db->f("type_merchant_amount");
					}
					if (!strlen($item_affiliate_type)) {
						$item_affiliate_type = $db->f("type_affiliate_type");
						$item_affiliate_amount = $db->f("type_affiliate_amount");
					}
					$is_recurring = $db->f("is_recurring");
					$recurring_items = ($is_recurring || $recurring_items);
					$recurring_price = $db->f("recurring_price");
					$recurring_period = $db->f("recurring_period");
					$recurring_interval = $db->f("recurring_interval");
					$recurring_payments_total = $db->f("recurring_payments_total");
					$recurring_start_date = $db->f("recurring_start_date", DATETIME);
					$recurring_end_date = $db->f("recurring_end_date", DATETIME);
	
					// item image
					$item_image = ""; $item_image_alt = ""; 
					if ($image_field) {
						$item_image = $db->f($image_field);	
						$item_image_alt = get_translation($db->f($image_alt_field));	
					}
					$big_image = $db->f("big_image");	
					$super_image = $db->f("super_image");	
					
					// some price calculation
					$real_price = $price;
					$properties_discount = 0;
					$discount_applicable = 1;
					if (!$is_price_edit) {
						$price = calculate_price($price, $is_sales, $sales_price);
						$real_price = $price;
						$quantity_price = get_quantity_price($item_id, $quantity);
						if (sizeof($quantity_price)) {
							$price = $quantity_price[0];
							$real_price = $price;
							$properties_discount = $quantity_price[1];
							$discount_applicable = $quantity_price[2];
						}
						if ($discount_applicable) {
							if ($user_discount_type == 1 || $user_discount_type == 3) {
								$price -= round(($price * $user_discount_amount) / 100, 2);
							} elseif ($user_discount_type == 2) {
								$price -= round($user_discount_amount, 2);
							} elseif ($user_discount_type == 4) {
								$price -= round((($price - $buying_price) * $user_discount_amount) / 100, 2);
							}
						}
					}
	
					// re-calculate price in case if prices include some default tax rate
					$item_tax = get_tax_amount($tax_rates, $item_type_id, $price, $item_tax_free, $item_tax_percent, $default_tax_rates);
					$item_real_tax = get_tax_amount($tax_rates, $item_type_id, $real_price, $item_tax_free, $item_tax_percent, $default_tax_rates);
					//$item_buying_tax = get_tax_amount($tax_rates, $item_type_id, $buying_price, $item_tax_free, $item_tax_percent, $default_tax_rates);
	
					$cart_item_id = $cart_id;
					$cart_items[$cart_item_id] = array(
						"parent_cart_id" => "", "top_order_item_id" => 0, "is_bundle" => $is_bundle,
						"item_id" => $item_id, "id" => $item_id, "product_id" => $item_id, "parent_item_id" => 0,						
						"selection_name" => "", "selection_order" => "",
						"item_user_id" => $item_user_id, "item_type_id" => $item_type_id, 
						"supplier_id" => $supplier_id, "wishlist_item_id" => $wishlist_item_id,
						"item_code" => $item_code, "manufacturer_code" => $manufacturer_code, 
						"item_image" => $item_image, "item_image_alt" => $item_image_alt, 
						"big_image" => $big_image, "super_image" => $super_image, 
						"packages_number" => $packages_number, "width" => $width, "height" => $height, "length" => $length,
						"weight" => $weight, "price" => $price, "quantity" => $quantity,
						"shipping_cost" => $shipping_cost, "is_shipping_free" => $is_shipping_free, "is_country_restriction" => $is_country_restriction,
						"tax_free" => $item_tax_free, "tax_percent" => $item_tax_percent,
						"real_price" => $real_price, "discount_amount" => ($real_price - $price),  
						"coupons" => $item_coupons, "coupons_ids" => "", "coupons_discount" => 0, 
						"affiliate_type" => $item_affiliate_type, "affiliate_amount" => $item_affiliate_amount,
						"merchant_type" => $item_merchant_type, "merchant_amount" => $item_merchant_amount,
						"is_points_price" => $is_points_price, "points_price" => $points_price, 
						"reward_type" => $reward_type, "reward_amount" => $reward_amount, 
						"credit_reward_type" => $credit_reward_type, "credit_reward_amount" => $credit_reward_amount, 
						"buying_price" => $buying_price, "item_name" => $item_name_initial, "product_name" => $item_name_initial,
						"product_title" => $item_name_initial, "item_title" => $item_name_initial, 
						"discount_applicable" => $discount_applicable, "properties_discount" => $properties_discount, 
						"downloadable" => $downloadable, "downloads" => "", 
						"stock_level" => $stock_level, "availability_time" => $availability_time, 
						"short_description" => $short_description, "description" => $short_description, "full_description" => $full_description,
						"generate_serial" => $generate_serial, "serial_period" => $serial_period, "activations_number" => $activations_number,
						"is_gift_voucher" => $is_gift_voucher,
						"is_recurring" => $is_recurring, "recurring_price" => $recurring_price, "recurring_period" => $recurring_period,
						"recurring_interval" => $recurring_interval, "recurring_payments_total" => $recurring_payments_total,
						"recurring_start_date" => $recurring_start_date, "recurring_end_date" => $recurring_end_date,
						"is_subscription" => 0, "is_account_subscription" => 0, "subscription_period" => "", 
						"subscription_interval" => "", "subscription_suspend" => "",
					);
	
					$cart_ids[] = $cart_id;
					// update stock level information
					if (isset($stock_levels[$item_id])) {
						$stock_levels[$item_id]["quantity"] += $quantity;
						$stock_levels[$item_id]["stock_level"] = $stock_level;
					} else {
						$stock_levels[$item_id] = array(
							"item_name" => $item_name_initial, "quantity" => $quantity, "stock_level" => $stock_level, 
							"use_stock_level" => $use_stock_level, "hide_out_of_stock" => $hide_out_of_stock, "disable_out_of_stock" => $disable_out_of_stock, 
						);
					}
	
					// check components for parent product
					if (sizeof($components) > 0) {
						// check for bundle components
						$parent_item_id = $item_id;
						$component_number = 0;

						$components_ids = array();
						$components_price = 0; $components_base_price = 0; $components_points_price = 0; $components_reward_points = 0; $components_reward_credits = 0;
						foreach ($components as $property_id => $component_values) {
							foreach ($component_values as $item_property_id => $component) {
								
								$property_type_id = $component["type_id"];
								$sub_item_id = $component["sub_item_id"];
								$sub_quantity = $component["quantity"];
								if ($sub_quantity < 1) { $sub_quantity = 1; }
								if ($property_type_id == 2) {
									$sql  = " SELECT i.item_id, i.item_code, i.manufacturer_code, i.user_id, i.item_type_id, i.supplier_id, i.item_name, i.short_description, i.full_description, ";
									$sql .= " i.downloadable, i.download_period, i.download_path, i.generate_serial, i.serial_period, i.activations_number, ";
									$sql .= " st_in.availability_time AS in_stock_availability , st_out.availability_time AS out_stock_availability, ";
									$sql .= " pr.quantity_action, i.packages_number, i.width, i.height, i.length, ";
									$sql .= " i.weight, i.shipping_cost, i.is_shipping_free, it.is_gift_voucher, ";
									$sql .= " i.merchant_fee_type AS item_merchant_type, i.merchant_fee_amount AS item_merchant_amount, i.affiliate_commission_type AS item_affiliate_type, i.affiliate_commission_amount AS item_affiliate_amount, ";
									$sql .= " it.merchant_fee_type AS type_merchant_type, it.merchant_fee_amount AS type_merchant_amount, it.affiliate_commission_type AS type_affiliate_type, it.affiliate_commission_amount AS type_affiliate_amount, ";
									$sql .= " pr.property_name AS component_name, pr.".$additional_price_field." AS component_price, ";
									$sql .= " i.buying_price, i." . $price_field . ", i.is_sales, i." . $sales_field . ", i.tax_free, ";
									$sql .= " i.is_points_price, i.points_price, i.reward_type, i.reward_amount, i.credit_reward_type, i.credit_reward_amount, ";
									$sql .= " i.stock_level, i.use_stock_level, i.hide_out_of_stock, i.disable_out_of_stock, i.shipping_rule_id, sr.is_country_restriction, ";
									$sql .= " i.is_recurring, i.recurring_price, i.recurring_period, i.recurring_interval, ";
									$sql .= " i.recurring_payments_total, i.recurring_start_date, i.recurring_end_date, ";
									$sql .= " i.tiny_image, i.tiny_image_alt, i.small_image, i.small_image_alt, i.big_image, i.big_image_alt, i.super_image ";
									$sql .= " FROM (((((" . $table_prefix . "items_properties pr ";								
									$sql .= " INNER JOIN  " . $table_prefix . "items i ON pr.sub_item_id=i.item_id)";
									$sql .= " LEFT JOIN " . $table_prefix . "item_types it ON i.item_type_id=it.item_type_id) ";
									$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_in ON i.shipping_in_stock=st_in.shipping_time_id) ";
									$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_out ON i.shipping_out_stock=st_out.shipping_time_id) ";
									$sql .= " LEFT JOIN " . $table_prefix . "shipping_rules sr ON i.shipping_rule_id=sr.shipping_rule_id) ";
									$sql .= " WHERE pr.property_id=" . $db->tosql($property_id, INTEGER);									
									//$sql .= " ORDER BY ip.user_type_id DESC ";
									$component_property_id = $property_id ."_0";
								} else {
									$sql  = " SELECT i.item_id, i.item_code, i.manufacturer_code, i.user_id, i.item_type_id, i.supplier_id, i.item_name, i.short_description, i.full_description, ";
									$sql .= " i.downloadable, i.download_period, i.download_path, i.generate_serial, i.serial_period, i.activations_number, ";
									$sql .= " st_in.availability_time AS in_stock_availability , st_out.availability_time AS out_stock_availability, ";
									$sql .= " i.packages_number, i.width, i.height, i.length, ";
									$sql .= " pr.quantity_action, i.weight, i.shipping_cost, i.is_shipping_free, it.is_gift_voucher, ";
									$sql .= " i.merchant_fee_type AS item_merchant_type, i.merchant_fee_amount AS item_merchant_amount, i.affiliate_commission_type AS item_affiliate_type, i.affiliate_commission_amount AS item_affiliate_amount, ";
									$sql .= " it.merchant_fee_type AS type_merchant_type, it.merchant_fee_amount AS type_merchant_amount, it.affiliate_commission_type AS type_affiliate_type, it.affiliate_commission_amount AS type_affiliate_amount, ";
									$sql .= " ipv.property_value AS component_name, ipv.".$additional_price_field." AS component_price, ";
									$sql .= " i.buying_price, i." . $price_field . ", i.is_sales, i." . $sales_field . ", i.tax_free, ";
									$sql .= " i.is_points_price, i.points_price, i.reward_type, i.reward_amount, i.credit_reward_type, i.credit_reward_amount, ";
									$sql .= " i.stock_level, i.use_stock_level, i.hide_out_of_stock, i.disable_out_of_stock, i.shipping_rule_id, sr.is_country_restriction, ";
									$sql .= " i.is_recurring, i.recurring_price, i.recurring_period, i.recurring_interval, ";
									$sql .= " i.recurring_payments_total, i.recurring_start_date, i.recurring_end_date, ";
									$sql .= " i.tiny_image, i.tiny_image_alt, i.small_image, i.small_image_alt, i.big_image, i.big_image_alt, i.super_image ";
									$sql .= " FROM (((((( " . $table_prefix . "items_properties_values ipv ";
									$sql .= " INNER JOIN " . $table_prefix . "items_properties pr ON pr.property_id=ipv.property_id)";
									$sql .= " INNER JOIN " . $table_prefix . "items i ON ipv.sub_item_id=i.item_id)";
									$sql .= " LEFT JOIN " . $table_prefix . "item_types it ON i.item_type_id=it.item_type_id) ";
									$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_in ON i.shipping_in_stock=st_in.shipping_time_id) ";
									$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_out ON i.shipping_out_stock=st_out.shipping_time_id) ";
									$sql .= " LEFT JOIN " . $table_prefix . "shipping_rules sr ON i.shipping_rule_id=sr.shipping_rule_id) ";
									$sql .= " WHERE ipv.item_property_id=" . $db->tosql($item_property_id, INTEGER);
									//$sql .= " ORDER BY ip.user_type_id DESC ";
									$component_property_id = $property_id ."_".$item_property_id;
								}
								$db->query($sql);
								if ($db->next_record()) {
									$component_number++;
									// price calculation
									$sub_item_id = $db->f("item_id");							
									
									$quantity_action = $db->f("quantity_action");
									$item_user_id = $db->f("user_id");
									$item_type_id = $db->f("item_type_id");
									$supplier_id = $db->f("supplier_id");
									$component_price = $db->f("component_price");
									$buying_price = $db->f("buying_price");
									$item_price = $db->f($price_field);
									$is_sales = $db->f("is_sales");
									$sales_price = $db->f($sales_field);
									if ($quantity_action == 2) {
										$component_quantity = $sub_quantity;
									} else {
										$component_quantity = $quantity * $sub_quantity;
									}
									
									$user_price          = false; 
									$properties_discount = 0;
									$user_action         = 0;								
									$quantity_prices     = get_quantity_price($sub_item_id, $component_quantity);
									if ($quantity_prices) {
										$user_price          = $quantity_prices[0];
										$properties_discount = $quantity_prices[1];
										$user_action         = $quantity_prices[2];
									}
									
									$discount_applicable = ($user_action == 2) ? 1 : 0;
									$properties_discount = $db->f("properties_discount");
									// points data
									$is_points_price = $db->f("is_points_price");
									$points_price = $db->f("points_price");
									$reward_type = $db->f("reward_type");
									$reward_amount = $db->f("reward_amount");
									$credit_reward_type = $db->f("credit_reward_type");
									$credit_reward_amount = $db->f("credit_reward_amount");
	
									$prices = get_product_price($sub_item_id, $item_price, $buying_price, $is_sales, $sales_price, $user_price, $user_action, $user_discount_type, $user_discount_amount);
									$base_price = $prices["base"];
									$real_price = $prices["real"];
									if (strlen($component_price)) {
										$price = $component_price;
									} else {
										$price = $base_price;
									}
	
									// re-calculate price in case if prices include some default tax rate
									$item_tax = get_tax_amount($tax_rates, $item_type_id, $price, $item_tax_free, $item_tax_percent, $default_tax_rates);
									$item_real_tax = get_tax_amount($tax_rates, $item_type_id, $real_price, $item_tax_free, $item_tax_percent, $default_tax_rates);
									//$item_buying_tax = get_tax_amount($tax_rates, $item_type_id, $buying_price, $item_tax_free, $item_tax_percent, $default_tax_rates);
	
									$item_code = $db->f("item_code");
									$manufacturer_code = $db->f("manufacturer_code");
									$item_name = $db->f("item_name");
									$downloadable = $db->f("downloadable");
									$download_period = $db->f("download_period");
									$download_path = $db->f("download_path");
									$generate_serial = $db->f("generate_serial");
									$serial_period = $db->f("serial_period");
									$activations_number = $db->f("activations_number");
									$is_gift_voucher = $db->f("is_gift_voucher");
									$stock_level = $db->f("stock_level");
									$use_stock_level = $db->f("use_stock_level");
									$hide_out_of_stock = $db->f("hide_out_of_stock");
									$disable_out_of_stock = $db->f("disable_out_of_stock");
									if ($stock_level > 0) {
										$availability_time = $db->f("in_stock_availability");
									} else {
										$availability_time = $db->f("out_stock_availability");
									}
									if ($availability_time > $max_availability_time) {
										$max_availability_time = $availability_time;
									}
									$shipping_rule_id = $db->f("shipping_rule_id");
									$is_country_restriction = $db->f("is_country_restriction");
									$packages_number = $db->f("packages_number");
									$weight = $db->f("weight");
									$width = $db->f("width");
									$height = $db->f("height");
									$length = $db->f("length");
									$shipping_cost = $db->f("shipping_cost");
									$is_shipping_free = $db->f("is_shipping_free");
									if ($is_shipping_free) { $shipping_cost = 0; }
									$item_tax_free = $db->f("tax_free");
									if ($tax_free) { $item_tax_free = $tax_free; }
									$short_description = strip_tags($db->f("short_description"));
									$full_description = strip_tags($db->f("full_description"));
									// get commission fields
									$item_merchant_type = $db->f("item_merchant_type");
									$item_merchant_amount = $db->f("item_merchant_amount");
									$item_affiliate_type = $db->f("item_affiliate_type");
									$item_affiliate_amount = $db->f("item_affiliate_amount");
									if (!strlen($item_merchant_type)) {
										$item_merchant_type = $db->f("type_merchant_type");
										$item_merchant_amount = $db->f("type_merchant_amount");
									}
									if (!strlen($item_affiliate_type)) {
										$item_affiliate_type = $db->f("type_affiliate_type");
										$item_affiliate_amount = $db->f("type_affiliate_amount");
									}
									$is_recurring = $db->f("is_recurring");
									$recurring_items = ($is_recurring || $recurring_items);
									$recurring_price = $db->f("recurring_price");
									$recurring_period = $db->f("recurring_period");
									$recurring_interval = $db->f("recurring_interval");
									$recurring_payments_total = $db->f("recurring_payments_total");
									$recurring_start_date = $db->f("recurring_start_date", DATETIME);
									$recurring_end_date = $db->f("recurring_end_date", DATETIME);
	
									// item image
									$item_image = ""; $item_image_alt = ""; 
									if ($image_field) {
										$item_image = $db->f($image_field);	
										$item_image_alt = get_translation($db->f($image_alt_field));	
									}
									$big_image = $db->f("big_image");	
									$super_image = $db->f("super_image");	
									$selection_name = ""; $selection_order = 1;
									if (isset($item["PROPERTIES_INFO"][$property_id])) {
										$selection_name = $item["PROPERTIES_INFO"][$property_id]["NAME"];
										$selection_order = $item["PROPERTIES_INFO"][$property_id]["ORDER"];
									} 
	
									$cart_item_id = $cart_id."_".$component_property_id;
									$cart_items[$cart_item_id] = array(
										"parent_cart_id" => $cart_id, "top_order_item_id" => 0, "is_bundle" => 0,
										"item_id" => $sub_item_id, "id" => $sub_item_id, "product_id" => $sub_item_id, 
										"item_user_id" => $item_user_id, "item_type_id" => $item_type_id, 
										"supplier_id" => $supplier_id, "wishlist_item_id" => $wishlist_item_id,
										"parent_item_id" => $parent_item_id, "component_property_id" => $component_property_id,
										"selection_name" => $selection_name, "selection_order" => $selection_order,
										"item_image" => $item_image, "item_image_alt" => $item_image_alt, 
										"big_image" => $big_image, "super_image" => $super_image, 
										"item_code" => $item_code, "manufacturer_code" => $manufacturer_code, 
										"price" => $price, 
										"quantity" => $component_quantity, "parent_quantity" => $quantity,
										"quantity_action" => $quantity_action, "sub_quantity" => $sub_quantity,
										"packages_number" => $packages_number, "width" => $width, "height" => $height, "length" => $length,
										"weight" => $weight, "shipping_cost" => $shipping_cost, 
										"is_shipping_free" => $is_shipping_free, "is_country_restriction" => $is_country_restriction,
										"tax_free" => $item_tax_free, "tax_percent" => $item_tax_percent,
										"affiliate_type" => $item_affiliate_type, "affiliate_amount" => $item_affiliate_amount,
										"merchant_type" => $item_merchant_type, "merchant_amount" => $item_merchant_amount,
										"item_merchant_type" => $item_merchant_type, "item_merchant_amount" => $item_merchant_amount,
										"item_affiliate_type" => $item_affiliate_type, "item_affiliate_amount" => $item_affiliate_amount,
										"base_price" => $base_price, "real_price" => $real_price, "discount_amount" => 0,  
										"coupons" => "", "coupons_ids" => "", "coupons_discount" => 0, 
										"discount_applicable" => $discount_applicable, "properties_discount" => $properties_discount,
										"is_points_price" => $is_points_price, "points_price" => $points_price, 
										"reward_type" => $reward_type, "reward_amount" => $reward_amount, 
										"credit_reward_type" => $credit_reward_type, "credit_reward_amount" => $credit_reward_amount, 
										"buying_price" => $buying_price, "item_name" => $item_name, "product_name" => $item_name,
										"product_title" => $item_name, "item_title" => $item_name, 
										"properties_info" => "", 
										"properties_html" => "", "properties_text" => "",
										"downloadable" => $downloadable, "downloads" => "", 
										"stock_level" => $stock_level, "availability_time" => $availability_time, 
										"short_description" => $short_description, "description" => $short_description, "full_description" => $full_description,
										"generate_serial" => $generate_serial, "serial_period" => $serial_period, "activations_number" => $activations_number,
										"is_gift_voucher" => $is_gift_voucher,
										"is_recurring" => $is_recurring, "recurring_price" => $recurring_price, "recurring_period" => $recurring_period,
										"recurring_interval" => $recurring_interval, "recurring_payments_total" => $recurring_payments_total,
										"recurring_start_date" => $recurring_start_date, "recurring_end_date" => $recurring_end_date,
										"is_subscription" => 0, "is_account_subscription" => 0, "subscription_period" => "",
										"subscription_interval" => "", "subscription_suspend" => "",
									);
									// associate components with parent product
									$cart_items[$cart_id]["components"][] = $cart_item_id; 
									if (isset($cart_items[$cart_id]["components_price"])) {
										$cart_items[$cart_id]["components_price"] += ($price * $sub_quantity); 
										$cart_items[$cart_id]["components_base_price"] += ($base_price * $sub_quantity); 
									} else {
										$cart_items[$cart_id]["components_price"] = ($price * $sub_quantity); 
										$cart_items[$cart_id]["components_base_price"] = ($base_price * $sub_quantity); 
									}
									$components_ids[] = $cart_item_id;
	
									// update stock level information for subcomponents
									if (isset($stock_levels[$sub_item_id])) {
										$stock_levels[$sub_item_id]["quantity"] += ($quantity * $sub_quantity);
										$stock_levels[$sub_item_id]["stock_level"] = $stock_level;
									} else {
										$stock_levels[$sub_item_id] = array(
											"item_name" => $item_name, "quantity" => ($quantity * $sub_quantity), "stock_level" => $stock_level, 
											"use_stock_level" => $use_stock_level, "hide_out_of_stock" => $hide_out_of_stock, "disable_out_of_stock" => $disable_out_of_stock, 
										);
									}
								} else {
									// if some basket items were missed remove from the cart and move user to the basket page
									unset($shopping_cart[$cart_id]);
									set_session("shopping_cart", $shopping_cart);
									header ("Location: " . get_custom_friendly_url("basket.php"));
									exit;
								}
							}
						}
					} // end components checks
				}
			}
		}

		// #2 - prepare items options, check delivery rules
		foreach ($cart_items as $id => $item) {
			$is_bundle = $item["is_bundle"];
			$is_country_restriction = $item["is_country_restriction"];
			// check properties if there are any
			$parent_properties_info = isset($cart_items[$id]["parent_properties_info"]) ? $cart_items[$id]["parent_properties_info"] : "";
			$downloads = array(); $properties_info = array(); $options_code = ""; $options_manufacturer_code = "";
			$properties_values = ""; $properties_values_text = ""; $properties_values_html = "";
			$additional_price = 0; $additional_real_price = 0; $options_buying_price = 0; $additional_weight = 0;

			if ($item["item_id"]) {
				$parent_cart_id = $item["parent_cart_id"];
				order_items_properties($id, $item, $parent_cart_id, $item["is_bundle"], $item["discount_applicable"], $item["properties_discount"], $parent_properties_info);
			}
			$cart_items[$id]["options_price"] = $additional_price;
			$cart_items[$id]["options_buying_price"] = $options_buying_price;
			$cart_items[$id]["options_real_price"] = $additional_real_price;
			$cart_items[$id]["options_weight"] = $additional_weight;
			$cart_items[$id]["properties_html"] = $properties_values_html;
			$cart_items[$id]["properties_text"] = $properties_values_text;
			$cart_items[$id]["properties_info"] = $properties_info;
			$cart_items[$id]["downloads"] = $downloads;
			// add options code to the main product codes 
			$cart_items[$id]["item_code"] .= $options_code;
			$cart_items[$id]["manufacturer_code"] .= $options_manufacturer_code;

			if ($is_bundle && isset($item["components"])) {
				// reassign parent options to first subcomponent
				$component_cart_id = $item["components"][0];
				$cart_items[$component_cart_id]["parent_downloads"] = $downloads;
				$cart_items[$component_cart_id]["parent_properties_info"] = $properties_info;
			}
			// end of properties

			// check delivery rules
			if ($is_country_restriction && $country_id) {
				$sql  = " SELECT shipping_rule_id FROM " . $table_prefix . "shipping_rules_countries ";
				$sql .= " WHERE shipping_rule_id=" . $db->tosql($shipping_rule_id, INTEGER);
				$sql .= " AND country_id=" . $db->tosql($country_id, INTEGER);
				$db->query($sql);
				if (!$db->next_record()) {
					$item_name = $item["item_name"];
					$delivery_errors .= str_replace("{product_name}", get_translation($item_name), PROD_RESTRICTED_DELIVERY_MSG) . "<br>";
				}
			} // end of delivery check 

		}

		// #3 - check items coupons for basic prices and calculate quantity for parent products
		$parent_quantity = 0; 
		foreach ($cart_items as $id => $item) {
			$parent_item_id = $item["parent_item_id"];
			if (!$parent_item_id) {
				$price = $item["price"];
				$quantity = $item["quantity"];
				$parent_quantity += $quantity;
				$item_coupons = isset($item["coupons"]) ? $item["coupons"] : "";
				$options_price = isset($item["options_price"]) ? $item["options_price"] : 0;
				$components = isset($item["components"]) ? $item["components"] : "";
				$components_price = isset($item["components_price"]) ? $item["components_price"] : 0;
				// calculate total product price with options and components
				$item_total_price = $price + $options_price + $components_price;
				$max_item_discount = $item_total_price;

				// show product coupons if available
				if (is_array($item_coupons))
				{
					foreach ($item_coupons as $coupon_id => $coupon_info)
					{
						$sql  = " SELECT c.* ";
						$sql .= " FROM ";
						if (isset($site_id)) {
							$sql .= "(";						
						}
						$sql .= $table_prefix . "coupons c ";
						if (isset($site_id)) {
							$sql .= " LEFT JOIN " . $table_prefix . "coupons_sites s ON s.coupon_id=c.coupon_id) ";
						}
						$sql .= " WHERE c.coupon_id=" . $db->tosql($coupon_id, INTEGER);
						if (isset($site_id)) {
							$sql .= " AND (c.sites_all = 1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
						} else {
							$sql .= " AND c.sites_all = 1 ";
						}
						$db->query($sql);
						if ($db->next_record()) {
							$is_active = $db->f("is_active");
							$coupon_code = $db->f("coupon_code");
							$coupon_title = $db->f("coupon_title");
							$discount_type = $db->f("discount_type");
							$coupon_discount_quantity = $db->f("discount_quantity");
							$coupon_discount = $db->f("discount_amount");
							$min_quantity = $db->f("min_quantity");
							$max_quantity = $db->f("max_quantity");
							$minimum_amount = $db->f("minimum_amount");
							$maximum_amount = $db->f("maximum_amount");
							$quantity_limit = $db->f("quantity_limit");
							$coupon_uses = $db->f("coupon_uses");

							if (!$is_active) {
								remove_coupon($coupon_id);
							} elseif ($quantity_limit > 0 && $coupon_uses >= $quantity_limit) {
								remove_coupon($coupon_id);
							} elseif ($item_total_price < $minimum_amount) {
								remove_coupon($coupon_id);
							} elseif ($maximum_amount && $item_total_price > $maximum_amount) {
								remove_coupon($coupon_id);
							} elseif ($quantity < $min_quantity) {
								remove_coupon($coupon_id);
							} elseif ($max_quantity && $quantity > $max_quantity) {
								remove_coupon($coupon_id);
							} else {
								if ($discount_type == 3) {
									$discount_amount = round(($item_total_price / 100) * $coupon_discount, 2);
								} else {
									$discount_amount = $coupon_discount;
								}
								if ($discount_amount > $max_item_discount) {
									$discount_amount = $max_item_discount;
								}
								$max_item_discount -= $discount_amount;

								if ($coupon_discount_quantity > 1) {
									$discount_number = intval($quantity / $coupon_discount_quantity) * $coupon_discount_quantity;
								} else {
									$discount_number = $quantity;
								}

								if ($discount_number != $quantity) {
									if ($discount_number) {
										$quantities_discounts[] = array(
											"COUPON_ID" => $coupon_id, "COUPON_CODE" => $coupon_code, "COUPON_TITLE" => $coupon_title, "ITEM_NAME" => $item_name, 
											"ITEM_TYPE_ID" => $item_type_id, "TAX_FREE" => $item_tax_free, 
											"DISCOUNT_NUMBER" => $discount_number, "DISCOUNT_PER_ITEM" => $discount_amount, "DISCOUNT_AMOUNT" => ($discount_amount * $discount_number));
									}
								} else {
									// calculate discount for parent product
									$item_price = $price + $options_price;
									$item_discount = round(($item_price * $discount_amount) / ($item_total_price), 2);
									$discount_amount_left = $discount_amount - $item_discount;
									if ($item_discount) {
										if (strlen($cart_items[$id]["coupons_ids"])) { 
											$cart_items[$id]["coupons_ids"] .= ","; 
										}
										$cart_items[$id]["coupons_ids"] .= $coupon_id;
										$cart_items[$id]["coupons_discount"] += $item_discount;
										$cart_items[$id]["coupons_applied"][$coupon_id] = array("title" => $coupon_title, "discount" => $item_discount);
									}

									// calculate discounts for subcomponents if available
									if ($discount_amount_left && is_array($components) && sizeof($components) > 0) {
										for ($c = 0; $c < sizeof($components); $c++) {
											$cc_id = $components[$c];
											$component = $cart_items[$cc_id];
											$component_price = $component["price"];
											$sub_quantity = $component["sub_quantity"];
											if (($c + 1) == sizeof($components)) {
												$component_discount = round($discount_amount_left / $sub_quantity, 2);
											} else {
												$component_discount = round(($component_price * $discount_amount) / ($item_total_price * $sub_quantity), 2);
												$discount_amount_left -= ($component_discount * $sub_quantity);
											}
											if ($component_discount) {
												if (strlen($cart_items[$cc_id]["coupons_ids"])) { 
													$cart_items[$cc_id]["coupons_ids"] .= ","; 
												}
												$cart_items[$cc_id]["coupons_ids"] .= $coupon_id;
												$cart_items[$cc_id]["coupons_discount"] += $component_discount;
												$cart_items[$cc_id]["coupons_applied"][$coupon_id] = array("title" => $coupon_title, "discount" => $component_discount);
											}
										}
									} // end subcomponents discount calculations
								} // end simple coupons applying
							}
						}
					} // end coupons checks for item
				}
			}
		} // end items checks

		// #4 - calculate points, credits and commissions 
		foreach ($cart_items as $id => $item) {
			$price = $item["price"];
			$buying_price = $item["buying_price"];
			$options_price = $item["options_price"];
			$options_buying_price = $item["options_buying_price"];
			$coupons_discount = $item["coupons_discount"];
			$is_points_price = $item["is_points_price"];
			$wishlist_item_id = $item["wishlist_item_id"];
			if (!strlen($is_points_price)) {
				$is_points_price = $points_prices;
			}
			$points_price = $item["points_price"];

			// calculate points price
			if (!strlen($points_price)) {
				$points_price = ($price - $coupons_discount) * $points_conversion_rate;
			}
			$points_price += ($options_price * $points_conversion_rate);
			if (!$points_system || !$is_points_price || $points_price > $points_balance) {
				$is_points_price = 0; $points_price = 0;
			}
			// get pay points parameter
			if ($points_system && $is_points_price) {
				$pay_points = get_param("pay_points_" . $id);
			} else {
				$pay_points = 0;
			}

			$cart_items[$id]["is_points_price"] = $is_points_price;
			$cart_items[$id]["points_price"] = $points_price;
			$cart_items[$id]["pay_points"] = $pay_points;

			// calculate reward points
			$reward_type = $item["reward_type"];
			$reward_amount= $item["reward_amount"];
			if ($points_system) {
				$reward_points = calculate_reward_points($reward_type, $reward_amount, $price, $buying_price, $points_conversion_rate, $points_decimals);
				if ($reward_type == 1 || $reward_type == 4) {
					$properties_reward_points = calculate_reward_points($reward_type, $reward_amount, $options_price, $options_buying_price, $points_conversion_rate, $points_decimals);
					$reward_points += $properties_reward_points;
				}
			} else {
				$reward_points = 0; $reward_type = 0;
			}
			$cart_items[$id]["reward_points"] = $reward_points;
			$cart_items[$id]["reward_type"] = $reward_type;
			// end reward points calculations

			// calculate reward credits
			$credit_reward_type = $item["credit_reward_type"];
			$credit_reward_amount = $item["credit_reward_amount"];
			if ($credit_system) {
				$reward_credits = calculate_reward_credits($credit_reward_type, $credit_reward_amount, $price, $buying_price);
				if ($credit_reward_type == 1 || $credit_reward_type == 4) {
					$properties_reward_credits = calculate_reward_credits($credit_reward_type, $credit_reward_amount, $options_price, $options_buying_price);
					$reward_credits += $properties_reward_credits;
				}
			} else {
				$reward_credits = 0; $credit_reward_type = 0;
			}	
			$cart_items[$id]["reward_credits"] = $reward_credits;
			$cart_items[$id]["credit_reward_type"] = $credit_reward_type;
			// end reward credits calculations

			// calculate commissions
			$item_user_id = $item["item_user_id"];
			$merchant_type = $item["merchant_type"];
			$merchant_amount = $item["merchant_amount"];
			$affiliate_type = $item["affiliate_type"];
			$affiliate_amount = $item["affiliate_amount"];

			$merchant_commission = get_merchant_commission($item_user_id, $price - $coupons_discount, $options_price, $buying_price + $options_buying_price, $merchant_type, $merchant_amount);
			$affiliate_commission = get_affiliate_commission($affiliate_user_id, $price - $coupons_discount, $options_price, $buying_price + $options_buying_price, $affiliate_type, $affiliate_amount);
			if ($merchant_commission && $affiliate_commission) {
				if ($affiliate_commission_deduct) {
					$merchant_fee = ($price - $coupons_discount + $options_price) - $merchant_commission;
					if ($merchant_fee < $affiliate_commission) {
						$merchant_commission -= ($affiliate_commission - $merchant_fee);
					}
				} else {
					$merchant_commission -= $affiliate_commission;
				}
			}

			$cart_items[$id]["merchant_commission"] = $merchant_commission;
			$cart_items[$id]["affiliate_commission"] = $affiliate_commission;
			// end commissions calculations
		}

		// list of fields to share bundle values
		$fields = array(
			"price" => 2,
			"buying_price" => 2,
			"coupons_discount" => 2,
			"points_price" => $points_decimals,
			"reward_points" => $points_decimals,
			"reward_credits" => $points_decimals,
			"affiliate_commission" => 2,
			"merchant_commission" => 2,
			"weight" => 4,
			"real_price" => 2,
			"shipping_cost" => 2,
		);
		// #5 - share parent bundle values among it subcomponents and check quantity for top elements
		$components_items = $cart_items; // temporary table to obain original values for components
		foreach ($cart_items as $id => $item) {
			$is_bundle = $item["is_bundle"];
			$parent_cart_id = $item["parent_cart_id"];
			$components = isset($item["components"]) ? $item["components"] : "";
			if ($is_bundle) {
				$components_price = isset($item["components_price"]) ? $item["components_price"] : 0;
				$components_base_price = isset($item["components_base_price"]) ? $item["components_base_price"] : 0;
				if (is_array($components) && sizeof($components) > 0) {
					if ($components_price > 0) {
						$check_field = "price"; $total_check_value = $components_price;
					} else {
						$check_field = "base_price"; $total_check_value = $components_base_price;
					}
					// added options prices to main price
					$cart_items[$id]["price"] += $cart_items[$id]["options_price"];
					$cart_items[$id]["buying_price"] += $cart_items[$id]["options_buying_price"];
					$cart_items[$id]["weight"] += $cart_items[$id]["options_weight"];
					$cart_items[$id]["real_price"] += $cart_items[$id]["options_real_price"];

					foreach($fields as $field_name => $decimals) {
						$parent_value = $cart_items[$id][$field_name];
						if ($parent_value) {
							$parent_value_left = $parent_value;
							for ($c = 0; $c < sizeof($components); $c++) {
								$cc_id = $components[$c];
								$component = $components_items[$cc_id];
								$sub_quantity = $component["sub_quantity"];
								$component_check_value = $component[$check_field];
								if (($c + 1) == sizeof($components)) {
									$parent_sub_value = round($parent_value_left / $sub_quantity, $decimals);
								} else {
									$parent_sub_value = round(($component_check_value * $parent_value) / ($total_check_value * $sub_quantity), $decimals);
									$parent_value_left -= ($parent_sub_value * $sub_quantity);
								}
								$cart_items[$cc_id][$field_name] += $parent_sub_value; // added parent product value to subcomponent
							}
						}
					}
				}
				// delete bundle product from the final list
				unset($cart_items[$id]);
			} else if ($subcomponents_show_type == 1 && !strlen($parent_cart_id)) {
				// share pay points value among subcomponents if they exists
				$pay_points = $item["pay_points"];
				if ($pay_points && is_array($components) && sizeof($components) > 0) {
					for ($c = 0; $c < sizeof($components); $c++) {
						$cc_id = $components[$c];
						$cart_items[$cc_id]["pay_points"] = $pay_points;
					}
				}
			}
		}

		// #6 - calculate products total values
		$shipping_items_total = 0; $total_quantity = 0; $weight_total = 0; $shipping_weight = 0; $shipping_quantity = 0;
		foreach ($cart_items as $id => $item) {
			$price = $item["price"];
			$real_price = $item["real_price"];
			$options_price = $item["options_price"];
			$buying_price = $item["buying_price"];
			$options_buying_price = $item["options_buying_price"];
			$options_real_price = $item["options_real_price"];

			$coupons_discount = $item["coupons_discount"];
			$quantity = $item["quantity"];
			$full_price = $price + $options_price - $coupons_discount;
			$full_buying_price = $buying_price + $options_buying_price;
			$full_real_price = $real_price + $options_real_price;
			$cart_items[$id]["full_price"] = $full_price;
			$cart_items[$id]["full_buying_price"] = $full_buying_price;
			$cart_items[$id]["full_real_price"] = $full_real_price;

			$item_total = $full_price * $quantity;
			$item_buying_total = $buying_price * $quantity;
			$item_type_id = $item["item_type_id"];
			$item_tax_free = $item["tax_free"];
			$item_tax_percent = $item["tax_percent"];
			$pay_points = $item["pay_points"];
			$packages_number = $item["packages_number"];
			if ($packages_number < 1) { $packages_number = 1; }
			$weight = $item["weight"];
			$options_weight = $item["options_weight"];
			$full_weight = $weight + $options_weight;
			$cart_items[$id]["full_weight"] = $full_weight;
			$is_shipping_free = $item["is_shipping_free"];
			$shipping_cost = $item["shipping_cost"];

			// get taxes for products and add it to total values 
			$item_tax = get_tax_amount($tax_rates, $item_type_id, $full_price, $item_tax_free, $item_tax_percent);
			$item_tax_total_values = get_tax_amount($tax_rates, $item_type_id, $item_total, $item_tax_free, $item_tax_percent, "", 2);
			$item_buying_tax = get_tax_amount($tax_rates, $item_type_id, $item_buying_total, $item_tax_free, $item_tax_percent);
			$item_tax_total = add_tax_values($tax_rates, $item_tax_total_values, "products");

			if ($tax_prices_type == 1) {
				$price_excl_tax = $full_price - $item_tax;
				$price_incl_tax = $full_price;
				$price_excl_tax_total = $item_total - $item_tax_total;
				$price_incl_tax_total = $item_total;
			} else {
				$price_excl_tax = $full_price;
				$price_incl_tax = $full_price + $item_tax;
				$price_excl_tax_total = $item_total;
				$price_incl_tax_total = $item_total + $item_tax_total;
			}
			$cart_items[$id]["price_excl_tax"] = $price_excl_tax;
			$cart_items[$id]["price_incl_tax"] = $price_incl_tax;
			$cart_items[$id]["price_excl_tax_total"] = $price_excl_tax_total;
			$cart_items[$id]["price_incl_tax_total"] = $price_incl_tax_total;
			$cart_items[$id]["item_tax"] = $item_tax;
			$cart_items[$id]["item_tax_total"] = $item_tax_total;
			$cart_items[$id]["item_taxes"] = $item_tax_total_values;

			$weight_total += (($weight + $options_weight) * $quantity * $packages_number);
			$total_quantity += $quantity;
			$goods_total_full += $item_total;
			$total_buying += $item_buying_total;
			$total_buying_tax += $item_buying_tax;
			if (!$pay_points) {
				$goods_total_excl_tax += $price_excl_tax_total;
				$goods_total_incl_tax += $price_incl_tax_total;
				$goods_tax_total += $item_tax_total;
				$goods_total += $item_total;
			}
			if (!$is_shipping_free) {
				$shipping_quantity += $quantity;
				$shipping_items_total += ($shipping_cost * $quantity); 
				$shipping_weight += ($weight + $options_weight) * $quantity * $packages_number;
			}
		}

		// #7 - show information about quantities coupons and order coupons 
		$max_discount = $goods_total; $max_tax_discount = $goods_tax_total; $coupons_param = ""; $vouchers_param = "";
		// check quantities discount coupons
		if (is_array($quantities_discounts) && sizeof($quantities_discounts) > 0) {
			foreach ($quantities_discounts as $coupon_number => $coupon_info) {
				if (strlen($order_coupons_ids)) { $order_coupons_ids .= ","; }
				$order_coupons_ids .= $coupon_id;
				$order_coupons++;
				$coupon_id = $coupon_info["COUPON_ID"];
				$coupon_code = $coupon_info["COUPON_CODE"];
				$coupon_title = $coupon_info["COUPON_TITLE"];
				$item_name = $coupon_info["ITEM_NAME"];
				$discount_number = $coupon_info["DISCOUNT_NUMBER"];
				$discount_per_item = $coupon_info["DISCOUNT_PER_ITEM"];
				$discount_amount = $coupon_info["DISCOUNT_AMOUNT"];
				$item_type_id = $coupon_info["ITEM_TYPE_ID"];
				$item_tax_free = $coupon_info["TAX_FREE"];
				$max_discount -= $discount_amount;

				// check discount tax  TODO
				$discount_tax_amount = get_tax_amount($tax_rates, $item_type_id, $discount_amount, $item_tax_free, $item_tax_percent, $default_tax_rates);
				$max_tax_discount -= $discount_tax_amount;

				if ($tax_prices_type == 1) {
					$discount_amount_excl_tax = $discount_amount - $discount_tax_amount;
					$discount_amount_incl_tax = $discount_amount;
				} else {
					$discount_amount_excl_tax = $discount_amount;
					$discount_amount_incl_tax = $discount_amount + $discount_tax_amount;
				}

				$coupon_title .= " (". $item_name . ")";
				$coupon_title .= " - " . currency_format($discount_per_item) . " x " . $discount_number . "";

				$t->set_var("coupon_id", $coupon_id);
				$t->set_var("coupon_title", $coupon_title);
				$t->set_var("coupon_amount_excl_tax", "- " . currency_format($discount_amount_excl_tax));
				$t->set_var("coupon_tax", "- " . currency_format($discount_tax_amount));
				$t->set_var("coupon_amount_incl_tax", "- " . currency_format($discount_amount_incl_tax));

				if ($goods_colspan > 0) {
					$t->parse("coupon_name_column", false);
				}
				if ($item_price_total_column) {
					$t->parse("coupon_amount_excl_tax_column", false);
				}
				if ($item_tax_total_column) {
					$t->parse("coupon_tax_column", false);
				}
				if ($item_price_incl_tax_total_column) {
					$t->parse("coupon_amount_incl_tax_column", false);
				}

				$total_discount_excl_tax += $discount_amount_excl_tax; 
				$total_discount_incl_tax += $discount_amount_incl_tax;
				$total_discount_tax += $discount_tax_amount;
				$total_discount += $discount_amount;

				$order_coupons[] = array("coupon_id" => $coupon_id, "coupon_code" => $coupon_code, "coupon_title" => $coupon_title, 
					"discount_amount" => $discount_amount, "discount_tax_amount" => $discount_tax_amount);

				$t->parse("coupons", true);
				
				// generate html parameter with all coupons
				if ($coupons_param) { $coupons_param .= "&"; }
				$coupons_param .= "coupon_id=".$coupon_id;
				$coupons_param .= "&title=".prepare_js_value($coupon_title);
				$coupons_param .= "&type=2"; // use amount per order type
				$coupons_param .= "&amount=".prepare_js_value($discount_amount);
				$coupons_param .= "&tax_free=".intval($item_tax_free);
			}
		}

		// #8 - show order coupons and check vouchers
		if (is_array($coupons)) {
			foreach ($coupons as $coupon_id => $coupon_info) {
				$coupon_id = $coupon_info["COUPON_ID"];
				$sql  = " SELECT c.* FROM ";
				if (isset($site_id)) {
					$sql .= "(";
				}
				$sql .= $table_prefix . "coupons c";
				if (isset($site_id)) {
					$sql .= " LEFT JOIN " .  $table_prefix . "coupons_sites s ON s.coupon_id=c.coupon_id) ";
				}
				$sql .= " WHERE c.coupon_id=" . $db->tosql($coupon_id, INTEGER);
				if (isset($site_id)) {
					$sql .= " AND (c.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
				} else {
					$sql .= " AND c.sites_all=1 ";
				}
				$db->query($sql);
				if ($db->next_record()) {
					$is_active = $db->f("is_active");
					$coupon_code = $db->f("coupon_code");
					$coupon_title = $db->f("coupon_title");
					$discount_type = $db->f("discount_type");
					$coupon_discount = $db->f("discount_amount");
					$min_quantity = $db->f("min_quantity");
					$max_quantity = $db->f("max_quantity");
					$minimum_amount = $db->f("minimum_amount");
					$maximum_amount = $db->f("maximum_amount");
					$quantity_limit = $db->f("quantity_limit");
					$coupon_uses = $db->f("coupon_uses");
					$coupon_free_postage = $db->f("free_postage");
					$coupon_tax_free = $db->f("coupon_tax_free");
					$coupon_order_tax_free = $db->f("order_tax_free");
					if (!$is_active) {
						remove_coupon($coupon_id);
					} elseif ($quantity_limit > 0 && $coupon_uses >= $quantity_limit) {
						remove_coupon($coupon_id);
					} elseif ($goods_total_full < $minimum_amount) {
						remove_coupon($coupon_id);
					} elseif ($maximum_amount && $goods_total_full > $maximum_amount) {
						remove_coupon($coupon_id);
					} elseif ($parent_quantity < $min_quantity) {
						remove_coupon($coupon_id);
					} elseif ($max_quantity && $parent_quantity > $max_quantity) {
						remove_coupon($coupon_id);
					} else {
						if ($discount_type == 5) {
							// add voucher to vouchers array to use later after all order calculations 
							$gift_vouchers[$coupon_id] = array(
								"code" => $coupon_code,
								"title" => $coupon_title,
								"max_amount" => $coupon_discount,
							);
							// generate html parameter with all coupons
							if ($vouchers_param) { $vouchers_param .= "&"; }
							$vouchers_param .= "voucher_id=".$coupon_id;
							$vouchers_param .= "&title=".prepare_js_value($coupon_title);
							$vouchers_param .= "&max_amount=".prepare_js_value($coupon_discount);
						} else {
							// show coupon information if no errors occurred
							if ($coupon_free_postage) { $free_postage = true; }
							if ($coupon_order_tax_free) { $tax_free = true; }
							if (strlen($order_coupons_ids)) { $order_coupons_ids .= ","; }
							$order_coupons_ids .= $coupon_id;
							if ($discount_type == 1) {
								$discount_amount = round(($goods_total / 100) * $coupon_discount, 2);
							} else {
								$discount_amount = $coupon_discount;
							}
							if ($discount_amount > $max_discount) {
								$discount_amount = $max_discount;
							}
							$max_discount -= $discount_amount;
							$discount_tax_amount = 0;
							if ($tax_available && !$coupon_tax_free) {
								$discount_tax_amount = round(($discount_amount * $goods_tax_total) / $goods_total, 2);
								if ($discount_tax_amount > $max_tax_discount) {
									$discount_tax_amount = $max_tax_discount;
								}
								$max_tax_discount -= $discount_tax_amount;
							}
							if ($tax_prices_type == 1) {
								$discount_amount_excl_tax = $discount_amount - $discount_tax_amount;
								$discount_amount_incl_tax = $discount_amount;
							} else {
								$discount_amount_excl_tax = $discount_amount;
								$discount_amount_incl_tax = $discount_amount + $discount_tax_amount;
							}
				  
							$t->set_var("coupon_id", $coupon_id);
							$t->set_var("coupon_title", $db->f("coupon_title"));
							if ($discount_amount_excl_tax) {
								$t->set_var("coupon_amount_excl_tax", "- " . currency_format($discount_amount_excl_tax));
								$t->set_var("coupon_tax", "- " . currency_format($discount_tax_amount));
								$t->set_var("coupon_amount_incl_tax", "- " . currency_format($discount_amount_incl_tax));
							} else {
								$t->set_var("coupon_amount_excl_tax", "");
								$t->set_var("coupon_tax", "");
								$t->set_var("coupon_amount_incl_tax", "");
							}
				  
							if ($goods_colspan > 0) {
								$t->parse("coupon_name_column", false);
							}
							if ($item_price_total_column) {
								$t->parse("coupon_amount_excl_tax_column", false);
							}
							if ($item_tax_total_column) {
								$t->parse("coupon_tax_column", false);
							}
							if ($item_price_incl_tax_total_column) {
								$t->parse("coupon_amount_incl_tax_column", false);
							}
				  
							$total_discount_excl_tax += $discount_amount_excl_tax; 
							$total_discount_incl_tax += $discount_amount_incl_tax;
							$total_discount_tax += $discount_tax_amount;
							$total_discount += $discount_amount;
				  
							$order_coupons[] = array("coupon_id" => $coupon_id, "coupon_code" => $coupon_code, "coupon_title" => $coupon_title, 
								"discount_amount" => $discount_amount, "discount_tax_amount" => $discount_tax_amount);
				  
							$t->parse("coupons", true);
				  
							// generate html parameter with all coupons
							if ($coupons_param) { $coupons_param .= "&"; }
							$coupons_param .= "coupon_id=".$coupon_id;
							$coupons_param .= "&title=".prepare_js_value($coupon_title);
							$coupons_param .= "&type=".prepare_js_value($discount_type); 
							$coupons_param .= "&amount=".prepare_js_value($coupon_discount);
							$coupons_param .= "&tax_free=".intval($coupon_tax_free);
						}
					}
				}
			}
		}
		$t->set_var("order_coupons", $coupons_param);
		$t->set_var("order_vouchers", $vouchers_param);

		// value for goods with applied discount
		$goods_value = $goods_total - $total_discount;
		$goods_tax_value = $goods_tax_total - $total_discount_tax;

		// #9 - recalculate commissions and other rewards values if global order discount available and calculate sum for points and credits
		$total_reward_points = 0; $total_reward_credits = 0; 					
		$total_merchants_commission = 0; $total_affiliate_commission = 0; // apply only if user pay with real money
		foreach ($cart_items as $id => $item) {
			$quantity = $item["quantity"];
			$pay_points = $item["pay_points"];
			$points_price = $item["points_price"];
			$affiliate_commission = $item["affiliate_commission"];
			$merchant_commission = $item["merchant_commission"];
			$reward_points = $item["reward_points"];
			$reward_credits = $item["reward_credits"];
			if ($total_discount) {
				$affiliate_commission = round($affiliate_commission * (1 - $total_discount / $goods_total), 2);
				$merchant_commission = round($merchant_commission * (1 - $total_discount / $goods_total), 2);
				$reward_points = round($reward_points * (1 - $total_discount / $goods_total), $points_decimals);
				$reward_credits = round($reward_credits * (1 - $total_discount / $goods_total), 2);
				$cart_items[$id]["affiliate_commission"] = $affiliate_commission;
				$cart_items[$id]["merchant_commission"] = $merchant_commission;
				$cart_items[$id]["reward_points"] = $reward_points;
				$cart_items[$id]["reward_credits"] = $reward_credits;
			}
			if ($pay_points) { 
				$goods_points_amount += $points_price * $quantity;
				if ($points_for_points) {
					$total_reward_points += $reward_points * $quantity;
				}
				if ($credits_for_points) {
					$total_reward_credits += $reward_credits * $quantity;
				}
			} else {
				$total_reward_points += $reward_points * $quantity;
				$total_reward_credits += $reward_credits * $quantity;
				$total_merchants_commission += ($merchant_commission * $quantity);
				$total_affiliate_commission += ($affiliate_commission * $quantity);
			}
		}		

		// #10 - parse order items in one place
		$order_items = ""; // generate html parameter
		foreach ($cart_items as $cart_item_id => $cart_item) {

			$sub_item_id = $cart_item["item_id"];

			$parent_cart_id = $cart_item["parent_cart_id"];
			$wishlist_item_id = $cart_item["wishlist_item_id"];
			$item_user_id = $cart_item["item_user_id"];
			$item_type_id = $cart_item["item_type_id"];
			$parent_item_id = $cart_item["parent_item_id"];
			$item_name_initial = $cart_item["item_name"];
			$item_name = get_translation($item_name_initial);
			$item_tax_free = $cart_item["tax_free"];
 			$quantity = $cart_item["quantity"];
			$price = $cart_item["full_price"];
			$item_total = $price * $quantity;
			$points_price = $cart_item["points_price"];

			// generate html parameter with all order items 
			if ($order_items) { $order_items.= "&"; }
			$order_items .= "cart_item_id=".prepare_js_value($cart_item_id);
			$order_items .= "&item_id=".$sub_item_id;
			$order_items .= "&parent_cart_id=".prepare_js_value($parent_cart_id);
			$order_items .= "&item_type_id=".prepare_js_value($item_type_id);
			$order_items .= "&name=".prepare_js_value($item_name);
			$order_items .= "&tax_free=".intval($item_tax_free);
			$order_items .= "&price=".prepare_js_value($price);
			$order_items .= "&quantity=".prepare_js_value($quantity);
			$order_items .= "&points_price=".prepare_js_value($points_price);
			$order_items .= "&subcomponents_show_type=".intval($subcomponents_show_type);

			if ($subcomponents_show_type == 1 && $parent_item_id && strlen($parent_cart_id) && isset($cart_items[$parent_cart_id])) {
				// component already shown with parent product
				continue;
			}

			//$component_property_id = $cart_item["component_property_id"];
			$item_code = $cart_item["item_code"];
			$manufacturer_code = $cart_item["manufacturer_code"];
			$short_description = get_translation($cart_item["short_description"]);
			$item_image = $cart_item["item_image"];
			$item_image_alt = $cart_item["item_image_alt"];

			$price_excl_tax = $cart_item["price_excl_tax"];
			$price_incl_tax = $cart_item["price_incl_tax"];
			$price_excl_tax_total = $cart_item["price_excl_tax_total"];
			$price_incl_tax_total = $cart_item["price_incl_tax_total"];

			$item_tax_percent = $cart_item["tax_percent"];
			$item_tax = $cart_item["item_tax"];
			$item_tax_total = $cart_item["item_tax_total"];

			$buying_price = $cart_item["buying_price"];
			$weight = $cart_item["weight"];

			$coupons_applied = isset($cart_item["coupons_applied"]) ? $cart_item["coupons_applied"] : "";
			$properties_html = $cart_item["properties_html"];
			$properties_text = $cart_item["properties_text"];

			// points & credits fields
			$pay_points = $cart_item["pay_points"];
			$points_price = $cart_item["points_price"];
			$reward_points = $cart_item["reward_points"];
			$reward_credits = $cart_item["reward_credits"];

			$components = isset($cart_item["components"]) ? $cart_item["components"] : "";
			if ($subcomponents_show_type == 1 && is_array($components) && sizeof($components) > 0) {
				$t->set_var("components", "");
				for ($c = 0; $c < sizeof($components); $c++) {
					$t->set_var("component_codes", "");
					$t->set_var("component_item_code_block", "");
					$t->set_var("component_man_code_block", "");
					$cc_id = $components[$c];
					$component = $cart_items[$cc_id];
					$component_id = $component["item_id"];
					$component_name = get_translation($component["item_name"]);
					$component_price = $component["full_price"];
					$component_quantity = $component["quantity"];
					$component_sub_quantity = $component["sub_quantity"];
					$quantity_action = isset($component["quantity_action"]) ? $component["quantity_action"] : 1;
					$parent_quantity = isset($component["parent_quantity"]) ? $component["parent_quantity"] : $component_quantity;
					$component_item_code = $component["item_code"];
					$component_manufacturer_code = $component["manufacturer_code"];
					$selection_name = "";
					if (isset($component["selection_name"]) && $component["selection_name"]) {
						$selection_name = $component["selection_name"] . ": ";
					}
					// add coupons to parent product
					$component_coupons = isset($component["coupons_applied"]) ? $component["coupons_applied"] : "";
					if (is_array($component_coupons)) {
						foreach($component_coupons as $coupon_id => $coupon_info) {
							if (isset($coupons_applied[$coupon_id])) {
								$coupons_applied[$coupon_id]["discount"] += $coupon_info["discount"];
							} else {
								$coupons_applied[$coupon_id] = $coupon_info;
							}
						}
					}

					$t->set_var("component_order_item_id", $cc_id);
					$t->set_var("component_quantity", $component_quantity);
					$t->set_var("selection_name", $selection_name);
					$t->set_var("component_name", $component_name);
					if ($component_price > 0) {
						$t->set_var("component_price", $option_positive_price_right . currency_format($component_price) . $option_positive_price_left);
					} elseif ($component_price < 0) {
						$t->set_var("component_price", $option_negative_price_right . currency_format(abs($component_price)) . $option_negative_price_left);
					} else {
						$t->set_var("component_price", "");
					}
					if (($show_item_code && strlen($component_item_code)) || ($show_manufacturer_code && strlen($component_manufacturer_code))) {
						if ($show_item_code && strlen($component_item_code)) {
							$t->set_var("component_item_code", $component_item_code);
							$t->sparse("component_item_code_block", false);
						}
						if ($show_manufacturer_code && strlen($component_manufacturer_code)) {
							$t->set_var("component_manufacturer_code", $component_manufacturer_code);
							$t->sparse("component_man_code_block", false);
						}
						$t->sparse("component_codes", false);
					}

					$component_image = $component["super_image"];
					$image_type = 4;
					if (!$component_image) { 
						$component_image = $component["big_image"];
						$image_type = 3;
					}
					if ($component_image) {
						$component_icon = product_image_icon($component_id, $component_name, $component_image, $image_type);
					} else {
						$component_icon = "";
					}
					$t->set_var("component_icon", $component_icon);

					if ($quantity_action == 2) {
						$price += ($component["full_price"] * $sub_quantity / $parent_quantity);
						$price_excl_tax += ($component["price_excl_tax"] * $sub_quantity / $parent_quantity);
						$item_tax += ($component["item_tax"] * $sub_quantity / $parent_quantity);
						$price_incl_tax += ($component["price_incl_tax"] * $sub_quantity / $parent_quantity);
						$price_excl_tax_total += ($component["price_excl_tax_total"] * $sub_quantity);
						$item_tax_total += ($component["item_tax_total"] * $sub_quantity);
						$price_incl_tax_total += ($component["price_incl_tax_total"] * $sub_quantity);
				  
						$points_price += ($component["points_price"] * $sub_quantity / $parent_quantity);
						$reward_points += ($component["reward_points"] * $sub_quantity / $parent_quantity);
						$reward_credits += ($component["reward_credits"] * $sub_quantity / $parent_quantity);
					} else {
						$price += ($component["full_price"] * $sub_quantity);
						$price_excl_tax += ($component["price_excl_tax"] * $sub_quantity);
						$item_tax += ($component["item_tax"] * $sub_quantity);
						$price_incl_tax += ($component["price_incl_tax"] * $sub_quantity);
						$price_excl_tax_total += ($component["price_excl_tax_total"] * $sub_quantity);
						$item_tax_total += ($component["item_tax_total"] * $sub_quantity);
						$price_incl_tax_total += ($component["price_incl_tax_total"] * $sub_quantity);
				  
						$points_price += ($component["points_price"] * $sub_quantity);
						$reward_points += ($component["reward_points"] * $sub_quantity);
						$reward_credits += ($component["reward_credits"] * $sub_quantity);
					}
					$item_total = $price * $quantity;

					$t->parse("components", true);
				}
				$t->parse("components_block", false);
			} else {
				$t->set_var("components_block", "");
			}

			// generate products description in text format
			$item_text  = $item_name;
			if (strlen($properties_text)) {
				$item_text .= " (" .$properties_text. ")";
			}
			$item_text .= " " . PROD_QTY_COLUMN . ":" . $quantity . " " . currency_format($item_total);
			$items_text .= $item_text . $eol;

			$coupons_html = "";
			if (is_array($coupons_applied)) {
				foreach($coupons_applied as $coupon_id => $coupon_info) {
					$coupons_html .= "<br>" . $coupon_info["title"] . " (- " . currency_format($coupon_info["discount"]) . ")";
				}
			}

			$t->set_var("cart_id", $cart_item_id);
			$t->set_var("item_name", $item_name);
			$t->set_var("item_name_strip", htmlspecialchars(strip_tags($item_name)));
			$t->set_var("short_description", $short_description);
			$t->set_var("quantity", $quantity);
			$t->set_var("coupons_list", $coupons_html);
			$t->set_var("properties_values", $properties_html);

			// item image
			$image_exists = false;
			if ($image_field) {
				if (!strlen($item_image)) {
					$item_image = $product_no_image;
				} else {
					$image_exists = true;
				}
			}

			// item image display
			if ($item_image) {
				if (preg_match("/^http\:\/\//", $item_image)) {
					$image_size = "";
				} else {
					$image_size = @getimagesize($item_image);
					if ($image_exists && ($watermark || $restrict_products_images)) {
						$item_image = "image_show.php?item_id=".$sub_item_id."&type=".$image_type_name."&vc=".md5($item_image);
					}
				}
				if (!strlen($item_image_alt)) { $item_image_alt = $item_name; }
				$t->set_var("alt", htmlspecialchars($item_image_alt));
				$t->set_var("src", htmlspecialchars($item_image));
				if (is_array($image_size)) {
					$t->set_var("width", "width=\"" . $image_size[0] . "\"");
					$t->set_var("height", "height=\"" . $image_size[1] . "\"");
				} else {
					$t->set_var("width", "");
					$t->set_var("height", "");
				}
					
				$t->sparse("image_preview", false);
			} else {
				$t->set_var("image_preview", "");
			}	

			// show product code
			$t->set_var("item_code", $item_code);
			$t->set_var("manufacturer_code", $manufacturer_code);
			if ($show_item_code && $item_code) {
				$t->sparse("item_code_block", false);
			} else {
				$t->set_var("item_code_block", "");
			}
			if ($show_manufacturer_code && $manufacturer_code) {
				$t->sparse("manufacturer_code_block", false);
			} else {
				$t->set_var("manufacturer_code_block", "");
			}							

			// show points price
			if ($points_system && $is_points_price) {
				if ($pay_points) {
					$t->set_var("pay_points_checked", "checked");
				} else {
					$t->set_var("pay_points_checked", "");
				}
				$t->set_var("points_price", number_format($points_price * $quantity, $points_decimals));
				$t->parse("points_price_block", false);
			} else {
				$t->set_var("points_price_block", "");
			}
			
			// show reward points
			$t->set_var("reward_points_block", "");
			if ($points_system && $reward_type && $reward_points_checkout) {
				$t->set_var("reward_points", number_format($reward_points * $quantity, $points_decimals));
				$t->parse("reward_points_block", false);
			}
			// show reward credits
			$t->set_var("reward_credits_block", "");
			if ($credit_system && $credit_reward_type) {
				if ($reward_credits_checkout && ($reward_credits_users == 0 || ($reward_credits_users == 1 && $user_id))) {
					$t->set_var("reward_credits", currency_format($reward_credits *  $quantity));
					$t->parse("reward_credits_block", false);
				}
			}

			// show prices
			$t->set_var("item_tax_percent",  $item_tax_percent . "%");
			$t->set_var("price_excl_tax", currency_format($price_excl_tax));
			$t->set_var("price_incl_tax", currency_format($price_incl_tax));
			$t->set_var("item_tax", currency_format($item_tax));
			$t->set_var("price_excl_tax_total", currency_format($price_excl_tax_total));
			$t->set_var("price_incl_tax_total", currency_format($price_incl_tax_total));
			$t->set_var("item_tax_total", currency_format($item_tax_total));

			parse_cart_columns($item_name_column, $item_price_column, $item_tax_percent_column, $item_tax_column, $item_price_incl_tax_column, $item_quantity_column, $item_price_total_column, $item_tax_total_column, $item_price_incl_tax_total_column, $item_image_column);
			$t->parse("items", true);
		}
		$t->set_var("cart_ids", implode(",", $cart_ids));

		// show total reward credits
		if ($credit_system && $reward_credits_checkout && $total_reward_credits && ($reward_credits_users == 0 || ($reward_credits_users == 1 && $user_id))) {
			$t->set_var("reward_credits_total", currency_format($total_reward_credits));
			$t->sparse("reward_credits_total_block", false);
		}
		// show total reward points 
		if ($points_system && $reward_points_checkout && $total_reward_points) {
			$t->set_var("reward_points_total", number_format($total_reward_points, $points_decimals));
			$t->sparse("reward_points_total_block", false);
		}

		if ($is_update) {
			set_session("shopping_cart", $shopping_cart);
		}
		$t->set_var("properties_ids", $properties_ids);

		$t->set_var("total_quantity", $total_quantity);
		$variables["total_quantity"] = $total_quantity;
		$t->set_var("total_items", $total_items);
		$variables["total_items"] = $total_items;

		$t->set_var("goods_value", number_format($goods_value,2));

		$t->set_var("goods_total_excl_tax", currency_format($goods_total_excl_tax));
		$t->set_var("goods_tax_total", currency_format($goods_tax_total));
		$t->set_var("goods_total_incl_tax", currency_format($goods_total_incl_tax));

		if ($goods_colspan > 0) {
			$t->parse("goods_name_column", false);
		}
		if ($item_price_total_column) {
			$t->parse("goods_total_excl_tax_column", false);
		}
		if ($item_tax_total_column) {
			$t->parse("goods_tax_total_column", false);
		}
		if ($item_price_incl_tax_total_column) {
			$t->parse("goods_total_incl_tax_column", false);
		}

		$items_text .= GOODS_TOTAL_MSG . ": " . currency_format($goods_total) . $eol;

		if ($total_discount > 0) {
			$items_text .= TOTAL_DISCOUNT_MSG . ": -" . currency_format($total_discount) . $eol;
			$items_text .= GOODS_WITH_DISCOUNT_MSG. ": " . currency_format(($goods_total - $total_discount)) . $eol;
			$t->set_var("total_discount_excl_tax", "- " . currency_format($total_discount_excl_tax));
			$t->set_var("total_discount_tax", "- " . currency_format($total_discount_tax));
			$t->set_var("total_discount_incl_tax", "- " . currency_format($total_discount_incl_tax));
			$t->set_var("discounted_amount_excl_tax", currency_format(($goods_total_excl_tax - $total_discount_excl_tax)));
			$t->set_var("discounted_tax_amount", currency_format(($goods_tax_total - $total_discount_tax)));
			$t->set_var("discounted_amount_incl_tax", currency_format(($goods_total_incl_tax - $total_discount_incl_tax)));
			if ($goods_colspan > 0) {
				$t->parse("total_discount_name_column", false);
				$t->parse("discounted_name_column", false);
			}
			if ($item_price_total_column) {
				$t->parse("total_discount_amount_excl_tax_column", false);
				$t->parse("discounted_amount_excl_tax_column", false);
			}
			if ($item_tax_total_column) {
				$t->parse("total_discount_tax_column", false);
				$t->parse("discounted_tax_column", false);
			}
			if ($item_price_incl_tax_total_column) {
				$t->parse("total_discount_amount_incl_tax_column", false);
				$t->parse("discounted_amount_incl_tax_column", false);
			}

			$t->parse("discount", false);
		} else {
			$t->set_var("discount", "");
		}
	}
	$t->set_var("order_items", $order_items);
	$basket_parsed = false;


	// group taxes by percentage value
	$items_taxes = array();
	foreach ($cart_items as $ci => $cart_item) {
		$item_taxes = $cart_item["item_taxes"];
		$price = $cart_item["full_price"];
		$item_total = $price * $quantity;
		if (is_array($item_taxes) && sizeof($item_taxes) > 0) {
			foreach ($item_taxes as $tax_id => $tax_values) {
				$item_tax_percent	= $tax_values["tax_percent"];
				$item_tax_amount = $tax_values["tax_amount"];
				if (strlen($item_tax_percent)) {
					$item_tax_text = str_replace(".", "_", strval(round($item_tax_percent, 4)));
					if (isset($items_taxes[$item_tax_text])) {
						$items_taxes[$item_tax_text]["goods_total"] += $item_total;
						$items_taxes[$item_tax_text]["goods_tax"] += $item_tax_amount;
					} else {
						$items_taxes[$item_tax_text] = array(
							"goods_total" => $item_total, "goods_tax" => $item_tax_amount, "tax_percent" => $item_tax_percent,
						);
					}
				}
			}
		} else {
			if (isset($items_taxes["0"])) {
				$items_taxes["0"]["goods_total"] += $item_total;
			} else {
				$items_taxes["0"] = array("goods_total" => $item_total, "goods_tax" => 0, "tax_percent" => 0);
			}
		}
	}

	foreach ($items_taxes as $items_tax_text => $items_tax_data) {
		$t->set_var("goods_total_" . $items_tax_text, currency_format($items_tax_data["goods_total"]));
		$t->set_var("goods_tax_total_" . $items_tax_text, currency_format($items_tax_data["goods_tax"]));
		$t->set_var("goods_with_tax_total_" . $items_tax_text, currency_format(($items_tax_data["goods_total"] + $items_tax_data["goods_tax"])));
	}

	// check stock level restrictions
	foreach ($stock_levels as $item_id => $item_info) {
		$item_name = $item_info["item_name"];
		$quantity = $item_info["quantity"];
		$stock_level = $item_info["stock_level"];
		$use_stock_level = $item_info["use_stock_level"];
		$hide_out_of_stock = $item_info["hide_out_of_stock"];
		$disable_out_of_stock = $item_info["disable_out_of_stock"];

		if (($disable_out_of_stock || $hide_out_of_stock) && $quantity > $stock_level) {
			$stock_error = str_replace("{limit_quantity}", $stock_level, PRODUCT_LIMIT_MSG);
			$stock_error = str_replace("{product_name}", get_translation($item_name), $stock_error);
			$sc_errors .= $stock_error . "<br>";
		}
	}

	// sum of options stock levels
	foreach ($cart_items as $id => $cart_item) {
		$item_name = $cart_item["item_name"]; 
		$quantity = $cart_item["quantity"]; 
		$properties_info = $cart_item["properties_info"]; 
		if (is_array($properties_info) && sizeof($properties_info) > 0) {
			for ($pi = 0; $pi < sizeof($properties_info); $pi++) {
				list($property_id, $control_type, $property_name, $property_value, $pr_add_price, $pr_add_weight, $pr_values, $property_order) = $properties_info[$pi];
				for ($pv = 0; $pv < sizeof($pr_values); $pv++) {
					list($item_property_id, $pr_value, $pr_value_text, $pr_use_stock, $pr_hide_out_stock, $pr_stock_level) = $pr_values[$pv];
					if ($pr_hide_out_stock) {
						if (isset($options_stock_levels[$item_property_id])) {
							$options_stock_levels[$item_property_id]["quantity"] += $quantity;
							$options_stock_levels[$item_property_id]["stock_level"] = $pr_stock_level;
						} else {
							$options_stock_levels[$item_property_id] = array(
								"item_name" => $item_name, "property_name" => $property_name, "property_value" => $pr_value,
								"quantity" => $quantity, "stock_level" => $pr_stock_level,  "hide_out_of_stock" => $pr_hide_out_stock,
							);
						}
					}
				}
			}
		}
	}

	// check options stock level restrictions
	foreach ($options_stock_levels as $item_property_id => $option_info) {
		$item_name = get_translation($option_info["item_name"]);
		$property_name = $option_info["property_name"];
		$property_value = $option_info["property_value"];
		$quantity = $option_info["quantity"];
		$stock_level = $option_info["stock_level"];
		$hide_out_of_stock = $option_info["hide_out_of_stock"];
		if ($hide_out_of_stock && $quantity > $stock_level) {
			$limit_product = get_translation($item_name);
			$limit_product .= " (" . get_translation($property_name) . ": " . get_translation($property_value) . ")";
			$limit_error = str_replace("{limit_quantity}", $stock_level, PRODUCT_LIMIT_MSG);
			$limit_error = str_replace("{product_name}", $limit_product, $limit_error);
			$sc_errors .= $limit_error . "<br>";
		}
	}


	// check order restrictions
	$order_min_goods_cost = get_setting_value($order_info, "order_min_goods_cost", "");
	$order_max_goods_cost = get_setting_value($order_info, "order_max_goods_cost", "");
	$order_min_weight = get_setting_value($order_info, "order_min_weight", "");
	$order_max_weight = get_setting_value($order_info, "order_max_weight", "");
	$weight_measure = get_setting_value($settings, "weight_measure", "");
	$prevent_repurchase = get_setting_value($order_info, "prevent_repurchase", 0);
	$repurchase_period = get_setting_value($order_info, "repurchase_period", "");
	if ($order_min_goods_cost && $goods_total_full < $order_min_goods_cost) {
		$sc_errors .= str_replace("{min_cost}", currency_format($order_min_goods_cost), ORDER_MIN_PRODUCTS_COST_ERROR) . "<br>";
	}
	if ($order_max_goods_cost && $goods_total_full > $order_max_goods_cost) {
		$sc_errors .= str_replace("{max_cost}", currency_format($order_max_goods_cost), ORDER_MAX_PRODUCTS_COST_ERROR) . "<br>";
	}
	if ($order_min_weight && $weight_total < $order_min_weight) {
		$sc_errors .= str_replace("{min_weight}", $order_min_weight." ".$weight_measure, ORDER_MIN_WEIGHT_ERROR) . "<br>";
	}
	if ($order_max_weight && $weight_total > $order_max_weight) {
		$sc_errors .= str_replace("{max_weight}", $order_max_weight." ".$weight_measure, ORDER_MAX_WEIGHT_ERROR) . "<br>";
	}
	if ($credit_system && $credit_amount > $credit_balance) {
	}

	$order_email = get_param("email");
	if ($prevent_repurchase && ($user_id || $order_email)) {
		$current_ts = va_timestamp();
		$repurchase_ts = $current_ts - ($repurchase_period * 86400);
		foreach ($cart_items as $id => $cart_item) {
			$item_id = $cart_item["item_id"];
			$item_name = $cart_item["item_name"];
			if ($item_id > 0) {
				$sql  = " SELECT o.order_placed_date ";
				$sql .= " FROM ((" . $table_prefix . "orders_items oi ";
				$sql .= " INNER JOIN " . $table_prefix . "orders o ON o.order_id=oi.order_id) ";
				$sql .= " INNER JOIN " . $table_prefix . "order_statuses os ON os.status_id=oi.item_status) ";
				$sql .= " WHERE oi.item_id=" . $db->tosql($item_id, INTEGER);
				$sql .= " AND os.paid_status=1 ";
				if ($repurchase_period > 0) {
					$sql .= " AND o.order_placed_date>" . $db->tosql($repurchase_ts, DATETIME);
				}
				$sql .= " AND (";
				if ($user_id) {
					$sql .= " o.user_id=" . $db->tosql($user_id, INTEGER);
				}
				if ($order_email) {
					if ($user_id) { $sql .= " OR "; }
					$sql .= " o.email=" . $db->tosql($order_email, TEXT);
				}
				$sql .= ") ";
				$sql .= " ORDER BY o.order_placed_date DESC ";
				$db->RecordsPerPage = 1; $db->PageNumber = 1;
				$db->query($sql);
				if ($db->next_record()) {
					if ($repurchase_period > 0) {
						$item_purchased = $db->f("order_placed_date", DATETIME);
						$item_purchased_ts = va_timestamp($item_purchased);
						$days_number = ceil($repurchase_period - (($current_ts - $item_purchased_ts) / 86400));
						$sc_error = str_replace("{product_name}", $item_name, PURCHASED_PRODUCT_DAYS_ERROR);
						$sc_error = str_replace("{days_number}", $days_number, $sc_error);
						$sc_errors .= $sc_error."<br>".$eol;
					} else {
						$sc_error = str_replace("{product_name}", $item_name, PURCHASED_PRODUCT_ERROR);
						$sc_errors .= $sc_error."<br>".$eol;
					}
				}
			}
		}
	}


	if (!$total_items) {
		header("Location: " . get_custom_friendly_url("basket.php"));
		exit;
	}

	$r = new VA_Record($table_prefix . "orders");

	// #11 - prepare shipping
	$shipping_control = "";
	$shipping_methods = ""; // variable for HTML form
	$total_shipping_types = 0;
	$shipping_type_id = ""; $shipping_type_code = ""; $shipping_type_desc = ""; $tare_weight = 0; $shipping_cost = 0;
	$shipping_taxable = 0; 
	$shipping_taxable_value = 0; // todo delete 
	$shipping_tax = 0; $shipping_points_amount = 0;

	if ($country_id && $shipping_quantity) {
		$shipping_pay_points = get_param("shipping_pay_points");
		$delivery_site_id = (isset($site_id)) ? $site_id : "";

		include("./includes/shipping_functions.php");
		$shipping_types = get_shipping_types($country_id, $state_id, $delivery_site_id, $user_type_id, $cart_items);

		$shipping_type_id_param = get_param("shipping_type_id");
		$total_shipping_types = sizeof($shipping_types);
		if ($total_shipping_types == 1) {
			$shipping_control = "HIDDEN";
		} else if ($total_shipping_types > 1) {
			if ($shipping_block == 1) {
				$shipping_control = "LISTBOX";
			} else {
				$shipping_control = "RADIO";
			}
		}

		for ($st = 0; $st < $total_shipping_types; $st++) {
			list ($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, $row_shipping_cost, $row_tare_weight, $row_shipping_taxable, $row_shipping_time) = $shipping_types[$st];

			if ($tax_free) { $row_shipping_taxable = 0; }
			if ($shipping_methods) { $shipping_methods .= "&"; }
			$shipping_methods .= "shipping_id=".$row_shipping_type_id;
			$shipping_methods .= "&code=".prepare_js_value($row_shipping_type_code);
			$shipping_methods .= "&desc=".prepare_js_value($row_shipping_type_desc);
			$shipping_methods .= "&cost=".prepare_js_value($row_shipping_cost);
			$shipping_methods .= "&tare=".prepare_js_value($row_tare_weight);
			$shipping_methods .= "&taxable=".intval($row_shipping_taxable);
			$shipping_methods .= "&time=".prepare_js_value($row_shipping_time);

			if ($free_postage) { $row_shipping_cost = 0; }
			$row_shipping_tax_free = (!$row_shipping_taxable);
			// re-calculate shipping cost in case if it include some default tax rate 
			$row_shipping_tax = get_tax_amount($tax_rates, "shipping", $row_shipping_cost, $row_shipping_tax_free, $shipping_tax_percent, $default_tax_rates);
			if ($tax_prices_type == 1) {
				$row_shipping_cost_excl_tax = $row_shipping_cost - $row_shipping_tax;
				$row_shipping_cost_incl_tax = $row_shipping_cost;
			} else {
				$row_shipping_cost_excl_tax = $row_shipping_cost;
				$row_shipping_cost_incl_tax = $row_shipping_cost + $row_shipping_tax;
			}

			if ($row_shipping_type_id == $shipping_type_id_param) {
				$shipping_type_id = $row_shipping_type_id;
				$shipping_type_code = $row_shipping_type_code;
				$shipping_type_desc = $row_shipping_type_desc;
				$shipping_time = $row_shipping_time;
				$tare_weight = $row_tare_weight;
				if ($points_system && $shipping_pay_points) {
					$shipping_points_amount = round($row_shipping_cost * $points_conversion_rate, $points_decimals);
					$shipping_cost_excl_tax = 0;
					$shipping_cost_incl_tax = 0;
				} else {
					$shipping_cost = $row_shipping_cost;
					$shipping_tax = $row_shipping_tax;
					$shipping_cost_excl_tax = $row_shipping_cost_excl_tax;
					$shipping_cost_incl_tax = $row_shipping_cost_incl_tax;
					$shipping_taxable = $row_shipping_taxable;
					if ($shipping_taxable) {
						$shipping_taxable_value = $shipping_cost;
					}
				}
	
				$items_text .= $shipping_type_desc . ": " . currency_format($row_shipping_cost) . $eol;
				$t->set_var("shipping_type_checked", "checked");
				$t->set_var("shipping_type_selected", "selected");
				$t->set_var("shipping_cost_excl_tax_selected", currency_format($shipping_cost_excl_tax));
				$t->set_var("shipping_tax_selected", currency_format($shipping_tax));
				$t->set_var("shipping_cost_incl_tax_selected", currency_format($shipping_cost_incl_tax));
			} else {
				$t->set_var("shipping_type_checked", "");
				$t->set_var("shipping_type_selected", "");
				if ($shipping_block == 0) {
					$t->set_var("shipping_cost_excl_tax_selected", "");
					$t->set_var("shipping_tax_selected", "");
					$t->set_var("shipping_cost_incl_tax_selected", "");
				}
			}
			$t->set_var("shipping_type_id", $row_shipping_type_id);
			$t->set_var("shipping_type_code", $row_shipping_type_code);
			$t->set_var("shipping_value", round($row_shipping_cost, 2));

			if ($tax_prices == 0 || $tax_prices == 1) {
				$t->set_var("shipping_cost_desc", currency_format($row_shipping_cost_excl_tax));
			} else {
				$t->set_var("shipping_cost_desc", currency_format($row_shipping_cost_incl_tax));
			}
			$t->set_var("shipping_type_desc", $row_shipping_type_desc);
			$t->set_var("shipping_taxable", intval($row_shipping_taxable));
			if ($total_shipping_types > 1) {
				if ($shipping_block == 1){
					$t->parse("shipping_option", true);
					$t->parse("input_hidden", true);
				} else {
					if ($points_system && $points_shipping && $points_balance > 0 && $st == 0) {
						if ($shipping_pay_points) {
							$t->set_var("shipping_pay_points_checked", "checked");
						} else {
							$t->set_var("shipping_pay_points_checked", "");
						}
						$t->parse("shipping_types_points_block", false);
					} else {
						$t->set_var("shipping_types_points_block", "");
					}
					if ($item_price_total_column) {
						$t->parse("shipping_radio_cost_excl_tax_column", false);
					}
					if ($item_tax_total_column) {
						$t->parse("shipping_radio_tax_column", false);
					}
					if ($item_price_incl_tax_total_column) {
						$t->parse("shipping_radio_cost_incl_tax_column", false);
					}
					$t->parse("shipping_types", true);
				}
			}
		}

		if ($total_shipping_types == 1) {
			$shipping_type_id = $row_shipping_type_id;
			$shipping_type_code = $row_shipping_type_code;
			$shipping_type_desc = $row_shipping_type_desc;
			$shipping_time = $row_shipping_time;
			$tare_weight = $row_tare_weight;
			if ($points_system && $shipping_pay_points) {
				$shipping_points_amount = round($row_shipping_cost * $points_conversion_rate, $points_decimals);
				$shipping_cost_excl_tax = 0;
				$shipping_cost_incl_tax = 0;
			} else {
				$shipping_cost = $row_shipping_cost;
				$shipping_tax = $row_shipping_tax;
				$shipping_cost_excl_tax = $row_shipping_cost_excl_tax;
				$shipping_cost_incl_tax = $row_shipping_cost_incl_tax;
				$shipping_taxable = $row_shipping_taxable;
				if ($shipping_taxable) {
					$shipping_taxable_value = $shipping_cost;
				}
			}
			$items_text .= $shipping_type_desc . ": " . currency_format($shipping_cost) . $eol;
			$t->set_var("shipping_types", "");
			if ($item_price_total_column) {
				$t->set_var("shipping_cost_excl_tax", currency_format($shipping_cost_excl_tax));
				$t->parse("shipping_cost_excl_tax_column", false);
			}
			if ($item_tax_total_column) {
				$t->set_var("shipping_tax", currency_format($shipping_tax));
				$t->parse("shipping_tax_column", false);
			}
			if ($item_price_incl_tax_total_column) {
				$t->set_var("shipping_cost_incl_tax", currency_format($shipping_cost_incl_tax));
				$t->parse("shipping_cost_incl_tax_column", false);
			}
			if ($points_system && $points_shipping && $points_balance > 0 && $st == 0) {
				if ($shipping_pay_points) {
					$t->set_var("shipping_pay_points_checked", "checked");
				} else {
					$t->set_var("shipping_pay_points_checked", "");
				}
				$t->parse("shipping_type_points_block", false);
			} else {
				$t->set_var("shipping_type_points_block", "");
			}
			$t->set_var("shipping_type_id", $shipping_type_id);
			$t->parse("shipping_type", false);
		} elseif ($total_shipping_types > 1 && $shipping_block == 1) {
			if ($points_system && $points_shipping && $points_balance > 0) {
				if ($shipping_pay_points) {
					$t->set_var("shipping_pay_points_checked", "checked");
				} else {
					$t->set_var("shipping_pay_points_checked", "");
				}
				$t->parse("shipping_list_points_block", false);
			}
			if ($item_price_total_column) {
				$t->parse("shipping_list_cost_column", false);
			}
			if ($item_tax_total_column) {
				$t->parse("shipping_list_tax_column", false);
			}
			if ($item_price_incl_tax_total_column) {
				$t->parse("shipping_list_cost_incl_tax_column", false);
			}
			$t->parse("shipping_list", false);
		}
	}
	if ($total_shipping_types > 1 && $operation == "fast_checkout") {
		// get the chepeast delivery method for Fast Checkout
		list ($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, $row_shipping_cost, $row_tare_weight, $row_shipping_taxable, $row_shipping_time) = $shipping_types[0];
		$shipping_type_id = $row_shipping_type_id;
		$shipping_type_code = $row_shipping_type_code;
		$shipping_type_desc = $row_shipping_type_desc;
		$shipping_time = $row_shipping_time;
		$tare_weight = $row_tare_weight;
		$shipping_cost = $row_shipping_cost;
		$shipping_taxable = $row_shipping_taxable;
		if ($shipping_taxable) {
			$shipping_taxable_value = $shipping_cost;
		}
		for ($st = 1; $st < $total_shipping_types; $st++) {
			list ($row_shipping_type_id, $row_shipping_type_code, $row_shipping_type_desc, $row_shipping_cost, $row_tare_weight, $row_shipping_taxable, $row_shipping_time) = $shipping_types[$st];

			if ($row_shipping_cost < $shipping_cost) {
				$shipping_type_id = $row_shipping_type_id;
				$shipping_type_code = $row_shipping_type_code;
				$shipping_type_desc = $row_shipping_type_desc;
				$shipping_time = $row_shipping_time;
				$tare_weight = $row_tare_weight;
				$shipping_cost = $row_shipping_cost;
				$shipping_taxable = $row_shipping_taxable;
				if ($shipping_taxable) {
					$shipping_taxable_value = $shipping_cost;
				}
			}
		}
	}
	$t->set_var("shipping_control", $shipping_control);
	$t->set_var("shipping_methods", $shipping_methods);
	$t->set_var("shipping_taxable_value", $shipping_taxable_value); // todo delete
	if ($shipping_cost > 0) {
		// get taxes for selected shipping and add it to total values 
		$shipping_tax_free = ($shipping_taxable) ? 0 : 1;
		$shipping_tax_values = get_tax_amount($tax_rates, "shipping", $shipping_cost, $shipping_tax_free, $shipping_tax_percent, "", 2);
		$shipping_tax_total = add_tax_values($tax_rates, $shipping_tax_values, "shipping");
	}

	// #12 - calculate the tax
	if ($tax_available) {
		// get taxes sums for further calculations
		$taxes_sum = 0; $discount_tax_sum = $total_discount_tax;
		foreach($tax_rates as $tax_id => $tax_info) {
			$tax_cost = isset($tax_info["tax_total"]) ? $tax_info["tax_total"] : 0;
			$taxes_sum += va_round($tax_cost, $currency["decimals"]);
		}

		$taxes_param = ""; $tax_number = 0;
		foreach($tax_rates as $tax_id => $tax_info) {
			$tax_number++;
			$tax_name = $tax_info["tax_name"];
			$current_tax_free = isset($tax_info["tax_free"]) ? $tax_info["tax_free"] : 0;
			if ($tax_free) { $current_tax_free = true; }
			$tax_percent = $tax_info["tax_percent"];
			$shipping_tax_percent = $tax_info["shipping_tax_percent"];
			$tax_types = $tax_info["types"];
			$tax_cost = isset($tax_info["tax_total"]) ? $tax_info["tax_total"] : 0;
			if ($total_discount_tax) {
				// in case of order coupons decrease taxes value 
				if ($tax_number == sizeof($tax_rates)) {
					$tax_discount = $discount_tax_sum;
				} else {
					$tax_discount = round(($tax_cost * $total_discount_tax) / $taxes_sum, 2);
				}
				$discount_tax_sum -= $tax_discount;
				$tax_cost -= $tax_discount;
			}

			$taxes_total += va_round($tax_cost, $currency["decimals"]);

			$t->set_var("tax_id", $tax_id);
			$t->set_var("tax_percent", $tax_percent);
			$t->set_var("tax_name", $tax_name);
			$t->set_var("tax_cost", currency_format($tax_cost));
			$t->parse("taxes", true);

			// build param
			if ($taxes_param) { $taxes_param .= "&"; }
			$taxes_param .= "tax_id=".$tax_id;
			$taxes_param .= "&tax_name=".prepare_js_value($tax_name);
			$taxes_param .= "&tax_free=".prepare_js_value($current_tax_free);
			$taxes_param .= "&tax_percent=".prepare_js_value($tax_percent);
			$taxes_param .= "&shipping_tax_percent=".$shipping_tax_percent;
			if (is_array($tax_types) && sizeof($tax_types) > 0) {
				foreach($tax_types as $item_type_id => $item_tax_percent) {
					$taxes_param .= "&item_type_id_".$item_type_id."=".prepare_js_value($item_tax_percent);
				}
			}
		}
		$t->set_var("tax_rates", $taxes_param);
	}

	$order_total = round($goods_total, 2) - round($total_discount, 2) + round($properties_total, 2) + round($shipping_cost, 2);
	if ($tax_prices_type != 1) {
		$order_total += round($taxes_total, 2);
	}

	// #13 - check if vouchers avaialable for this order
	if (is_array($gift_vouchers) && sizeof($gift_vouchers) > 0) {
		foreach ($gift_vouchers as $voucher_id => $voucher_info) {
			$voucher_title = $voucher_info["title"];
			$voucher_max_amount = $voucher_info["max_amount"];
			if ($voucher_max_amount > $order_total) {
				$voucher_amount = $order_total;
			} else {
				$voucher_amount = $voucher_max_amount;
			}
			$order_total -= $voucher_amount;
			$vouchers_amount += $voucher_amount; // calculate total amount for vouchers
			$gift_vouchers[$voucher_id]["amount"] = $voucher_amount;

			$t->set_var("voucher_id", $voucher_id);
			$t->set_var("voucher_title", $voucher_title);
			$t->set_var("voucher_max_amount", $voucher_max_amount);
			if ($voucher_amount > 0) {
				if (strlen($vouchers_ids)) { $vouchers_ids .= ","; }
				$vouchers_ids .= $voucher_id;
				$t->set_var("voucher_amount", "- ".currency_format($voucher_amount));
			} else {
				$t->set_var("voucher_amount", "");
			}
			$t->parse("used_vouchers", true);
		}
		$t->parse("vouchers_block", false);
	}

	$total_points_amount = $goods_points_amount + $properties_points_amount + $shipping_points_amount;

	if ($total_points_amount > 0) {
		// check if user has enough points to pay for goods
		$sql  = " SELECT SUM(points_action * points_amount) ";
		$sql .= " FROM " . $table_prefix . "users_points ";
		$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
		$total_points_sum = get_db_value($sql);

		// update points information in users table if it's has a wrong value
		if ($total_points_sum != $points_balance) {
			$sql  = " UPDATE " . $table_prefix . "users ";
			$sql .= " SET total_points=" . $db->tosql($total_points_sum, NUMBER);
			$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$db->query($sql);
			$user_info["total_points"] = $total_points_sum;
			set_session("session_user_info", $user_info);
		}

		if ($total_points_amount > $points_balance) {
			$error_message = str_replace("{points_amount}", number_format($total_points_amount, $points_decimals), POINTS_ENOUGH_ERROR);
			$sc_errors .= $error_message;
		}
	}

	// #14 - payment systems
	$total_payments = 0; $payments_ids = array();
	$sql  = " SELECT ps.payment_id FROM ";
	if (isset($site_id)) {
		$sql .= "(";
	}
	if (strlen($user_type_id)) {
		$sql .= "(";
	}
	$sql .= $table_prefix . "payment_systems ps ";
	if (isset($site_id)) {
		$sql .= " LEFT JOIN " . $table_prefix . "payment_systems_sites s ON s.payment_id=ps.payment_id) ";
	}
	if (strlen($user_type_id)) {
		$sql .= " LEFT JOIN " . $table_prefix . "payment_user_types ut ON ut.payment_id=ps.payment_id) ";
	}
	$sql .= " WHERE ps.is_active=1 ";
	if (isset($site_id)) {
		$sql .= " AND (ps.sites_all = 1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
	} else {
		$sql .= " AND ps.sites_all = 1 ";
	}
	if (strlen($user_type_id)) {
		$sql .= " AND (ps.user_types_all = 1 OR ut.user_type_id=" . $db->tosql($user_type_id, INTEGER, true, false) . ")";
	} else {
		$sql .= " AND ps.user_types_all = 1 ";
	}
	$sql .= " GROUP BY ps.payment_id ";
	$db->query($sql);
	while ($db->next_record()) {
		$row_payment_id = $db->f("payment_id");
		$payments_ids[] = $row_payment_id;
		$total_payments++;
	}
	$payment_id = ""; $is_processing_fee = false; $processing_fees = ""; $processing_fee = 0; $fee_type = 0; $processing_time = 0;
	$payment_url = ""; $payment_method = "GET"; $payment_advanced = 0;
	if ($total_payments == 1) {
		$sql  = " SELECT payment_id,payment_name,user_payment_name,recurring_method,";
		$sql .= " processing_fee,fee_type,fee_min_goods,fee_max_goods,processing_time, ";
		$sql .= " payment_url, submit_method, is_advanced ";
		$sql .= " FROM " . $table_prefix . "payment_systems ";
		$sql .= " WHERE payment_id IN (" . $db->tosql($payments_ids, INTEGERS_LIST) . ") ";
		$db->query($sql);
		if ($db->next_record()) {
			$payment_id = $db->f("payment_id");
			$payment_name = get_translation($db->f("payment_name"));
			$user_payment_name = get_translation($db->f("user_payment_name"));
			if ($user_payment_name) {
				$payment_name = $user_payment_name;
			}
			$payment_url = $db->f("payment_url");
			$payment_method = $db->f("submit_method");
			$payment_advanced = $db->f("is_advanced");

			$recurring_method = $db->f("recurring_method");
			if ($recurring_items && !$recurring_method) {
				$sc_errors = str_replace("{payment_name}", $row_payment_name, RECURRING_NOT_ALLOWED_ERROR) . "<br>";
			}
			$fee_type = $db->f("fee_type");
			$processing_fee = $db->f("processing_fee");
			$fee_min_goods = $db->f("fee_min_goods");
			$fee_max_goods = $db->f("fee_max_goods");
			if ((strlen($fee_max_goods) && $goods_total > $fee_max_goods) || $goods_total < $fee_min_goods) {
				$processing_fee = 0;
			}
			$processing_time = $db->f("processing_time");
			if ($processing_fee != 0) {
				$is_processing_fee = true;
			}
			$processing_fees = $payment_id . "," . intval($fee_type) . "," . round($processing_fee, 2);
		}
	} elseif ($total_payments > 0) {
		if ($operation == "fast_checkout") {
			$payment_id = $fast_payment_id;
		} elseif ($operation == "save" || $operation == "refresh") {
			$payment_id = get_param("payment_id");
		} else {
			$sql  = " SELECT ps.payment_id FROM ";
			if (isset($site_id)) {
				$sql .= "(";
			}
			if (strlen($user_type_id)) {
				$sql .= "(";
			}
			$sql .= $table_prefix . "payment_systems ps ";
			if (isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "payment_systems_sites s ON s.payment_id=ps.payment_id) ";
			}
			if (strlen($user_type_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "payment_user_types ut ON ut.payment_id=ps.payment_id) ";
			}
			$sql .= " WHERE ps.payment_id IN (" . $db->tosql($payments_ids, INTEGERS_LIST) . ") AND ps.is_default=1 ";
			if (isset($site_id)) {
				$sql .= " AND (ps.sites_all = 1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
			} else {
				$sql .= " AND ps.sites_all = 1 ";
			}
			if (strlen($user_type_id)) {
				$sql .= " AND (ps.user_types_all = 1 OR ut.user_type_id=" . $db->tosql($user_type_id, INTEGER, true, false) . ")";
			} else {
				$sql .= " AND ps.user_types_all = 1 ";
			}
			$payment_id = get_db_value($sql);
		}
		$payment_image = get_setting_value($order_info, "payment_image", "");
		$payment_systems = array(array("", "", "", "", "", ""));
		$sql  = " SELECT payment_id,payment_name,user_payment_name,";
		$sql .= " recurring_method,processing_fee,fee_type,fee_min_goods,fee_max_goods,processing_time,";
		$sql .= " image_small, image_small_alt, image_large, image_large_alt,";
		$sql .= " payment_url, submit_method, is_advanced ";
		$sql .= " FROM " . $table_prefix . "payment_systems ";
		$sql .= " WHERE payment_id IN (" . $db->tosql($payments_ids, INTEGERS_LIST) . ") ";
		$sql .= " ORDER BY payment_order, payment_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$row_payment_id = $db->f("payment_id");
			$row_recurring_method = $db->f("recurring_method");
			$row_payment_name = get_translation($db->f("payment_name"));
			$user_payment_name = get_translation($db->f("user_payment_name"));
			if ($user_payment_name) {
				$row_payment_name = $user_payment_name;
			}
			$row_processing_fee = $db->f("processing_fee");
			$fee_min_goods = $db->f("fee_min_goods");
			$fee_max_goods = $db->f("fee_max_goods");

			$row_image = ""; $row_image_alt = "";
			if ($payment_image == 1) {
				$row_image = $db->f("image_small");
				$row_image_alt = $db->f("image_small_alt");
			} elseif ($payment_image == 2) {
				$row_image = $db->f("image_large");
				$row_image_alt = $db->f("image_large_alt");
			}

			if ((strlen($fee_max_goods) && $goods_total > $fee_max_goods) || $goods_total < $fee_min_goods) {
				$row_processing_fee = 0;
			}
			$row_fee_type = $db->f("fee_type");
			$row_processing_time = $db->f("processing_time");
			if ($processing_fees) { $processing_fees .= ","; }
			$processing_fees .= $row_payment_id . "," . intval($row_fee_type) . "," . round($row_processing_fee, 2);
			if ($row_payment_id == $payment_id) {
				$payment_url = $db->f("payment_url");
				$payment_method = $db->f("submit_method");
				$payment_advanced = $db->f("is_advanced");
				if ($recurring_items && !$row_recurring_method) {
					$sc_errors = str_replace("{payment_name}", $row_payment_name, RECURRING_NOT_ALLOWED_ERROR) . "<br>";
				}
				$processing_fee = $row_processing_fee;
				$fee_type = $row_fee_type;
				$processing_time = $row_processing_time;
			}
			if ($row_processing_fee > 0) {
				$is_processing_fee = true;
				if ($row_fee_type == 1) {
					$row_payment_name .= " (+ " . number_format($row_processing_fee, 2) . "%)";
				} else {
					$row_payment_name .= " (+ " . currency_format($row_processing_fee) . ")";
				}
			} elseif ($row_processing_fee < 0) {
				$is_processing_fee = true;
				if ($row_fee_type == 1) {
					$row_payment_name .= " (- " . abs(number_format($row_processing_fee, 2)) . "%)";
				} else {
					$row_payment_name .= " (- " . currency_format(abs($row_processing_fee)) . ")";
				}
			}
			$payment_systems[] = array($row_payment_id, $row_payment_name, $row_image, $row_image_alt);
		}

		$payment_control = get_setting_value($order_info, "payment_control_type", 0);
		if ($payment_control == 1) {
			$t->set_var("payment_radio_id", "");
			for ($i = 1; $i < sizeof($payment_systems); $i++)
			{
				list($row_payment_id, $row_payment_name, $row_image, $row_image_alt) = $payment_systems[$i];
				$checked = ""; $selected = "";
				if (strval($row_payment_id) == strval($payment_id)) {
					$checked = "checked"; $selected = "selected";
				}
				$t->set_var("payment_radio_id_checked", $checked);
				$t->set_var("payment_radio_id_value", $row_payment_id);
				$t->set_var("payment_radio_id_description", $row_payment_name);

				if ($row_image) {
					$t->set_var("src_image", $row_image);
					$t->set_var("alt_image", $row_image_alt);
					$t->sparse("image_option", false);
				}	 else {
					$t->set_var("image_option", "");
				}

				$t->parse("payment_radio_id", true);
			}
			$t->parse("payment_gateways_radio", false);
		} else {
			set_options($payment_systems, $payment_id, "payment_select_id");
			$t->parse("payment_gateways_select", false);
		}

	} else {
		$payment_id = "";
		$r->errors  = "Sorry, but there is no active payment system.";
	}

	// check credit amount for order before applying fee
	$order_credit_amount = 0; $credit_amount_left = $credit_amount;
	if ($credit_amount_left > 0 && $order_total > 0) {
		if ($credit_amount_left > $order_total) {
			$order_credit_amount = $order_total;
		} else {
			$order_credit_amount = $credit_amount_left;
		}
		$order_total -= $order_credit_amount;
		$credit_amount_left -= $order_credit_amount;
	}
	if ($is_processing_fee) {
		if ($fee_type == 1) {
			$processing_fee = round(($order_total * $processing_fee) / 100, 2);
		}
		$t->set_var("processing_fee_cost", currency_format($processing_fee));
		$t->sparse("fee", false);

		$items_text .= PROCESSING_FEE_MSG . ": " . currency_format($processing_fee);
		$order_total += $processing_fee;
	}
	$t->set_var("processing_fees", $processing_fees);
	// check credit amount for order after applying fee
	if ($credit_amount_left > 0 && $order_total > 0) {
		if ($credit_amount_left > $order_total) {
			$order_credit_amount += $order_total;
			$order_total = 0;
		} else {
			$order_credit_amount += $credit_amount_left;
			$order_total -= $credit_amount_left;
		}
	}

	// order total string
	$items_text .= CART_TOTAL_MSG . ": " . currency_format($order_total);
	$t->set_var("order_total_desc", currency_format($order_total));

	$t->set_var("total_points_amount", number_format($total_points_amount, $points_decimals));
	$t->set_var("goods_points_value", round($goods_points_amount, $points_decimals));
	$t->set_var("properties_points_value", round($properties_points_amount, $points_decimals));
	$t->set_var("shipping_points_value", round($shipping_points_amount, $points_decimals));
	if ($points_system && $user_id) {
		$t->set_var("points_balance", number_format($points_balance, $points_decimals));
		$t->set_var("remaining_points", number_format($points_balance - $total_points_amount, $points_decimals));
		$t->sparse("points_balance_block", false);
		if ($points_balance > 0) {
			$t->sparse("total_points_block", false);
		}
	}
	if ($credit_system && $user_id && $credits_balance_order_profile) {
		$t->set_var("credit_balance", currency_format($credit_balance));
		$t->sparse("credit_balance_block", false);
		if ($credit_balance > 0) {
			if ($credit_amount) {
				$t->set_var("credit_amount", htmlspecialchars($credit_amount));
			} else {
				$t->set_var("credit_amount", "");
			}
			$t->sparse("credit_amount_block", false);
		}
	}



	$r->add_where("order_id", INTEGER);
	$r->add_textbox("invoice_number", TEXT);
	$r->change_property("invoice_number", USE_SQL_NULL, false);
	$r->add_textbox("session_id", TEXT);
	$r->add_textbox("site_id", INTEGER);
	$r->change_property("site_id", USE_SQL_NULL, false);
	$r->add_textbox("user_id", INTEGER);
	$r->add_textbox("user_type_id", INTEGER);
	$r->add_textbox("payment_id", INTEGER, PAYMENT_GATEWAY_MSG);
	if ($total_payments > 1) {
		$r->change_property("payment_id", REQUIRED, true);
	}
	$r->add_textbox("success_message", TEXT);
	$r->add_textbox("error_message", TEXT);
	$r->add_textbox("pending_message", TEXT);
	$r->add_textbox("remote_address", TEXT);
	$r->add_textbox("initial_ip", TEXT);
	$r->add_textbox("cookie_ip", TEXT);
	$r->add_textbox("visit_id", INTEGER);
	$r->change_property("visit_id", USE_SQL_NULL, false);
	$r->add_textbox("affiliate_code", TEXT);
	$r->change_property("affiliate_code", USE_SQL_NULL, false);
	$r->add_textbox("affiliate_user_id", INTEGER);
	$r->change_property("affiliate_user_id", USE_SQL_NULL, false);
	$r->add_textbox("friend_code", TEXT);
	$r->change_property("friend_code", USE_SQL_NULL, false);
	$r->add_textbox("friend_user_id", INTEGER);
	$r->change_property("friend_user_id", USE_SQL_NULL, false);
	$r->add_textbox("keywords", TEXT);
	$r->change_property("keywords", USE_SQL_NULL, false);
	$r->add_textbox("coupons_ids", TEXT);
	$r->add_textbox("vouchers_ids", TEXT);
	$r->add_textbox("default_currency_code", TEXT);
	$r->add_textbox("currency_code", TEXT);
	$r->add_textbox("currency_rate", FLOAT);
	$r->add_textbox("order_status", INTEGER);
	$r->add_textbox("total_buying", NUMBER);
	$r->add_textbox("total_buying_tax", NUMBER);
	$r->add_textbox("total_merchants_commission", NUMBER);
	$r->add_textbox("total_affiliate_commission", NUMBER);
	$r->add_textbox("goods_total", NUMBER);
	$r->add_textbox("goods_tax", NUMBER);
	$r->add_textbox("goods_incl_tax", NUMBER);
	$r->add_textbox("goods_points_amount", NUMBER);
	$r->add_textbox("total_quantity", NUMBER);
	$r->add_textbox("weight_total", NUMBER);
	$r->add_textbox("total_discount", NUMBER);
	$r->add_textbox("total_discount_tax", NUMBER);
	$r->add_textbox("properties_total", NUMBER);
	$r->add_textbox("properties_taxable", NUMBER);
	$r->add_textbox("properties_points_amount", NUMBER);
	$r->add_textbox("shipping_type_id", INTEGER);
	$r->add_textbox("shipping_type_code", TEXT);
	$r->add_textbox("shipping_type_desc", TEXT);
	$r->add_textbox("shipping_cost", NUMBER);
	$r->add_textbox("shipping_taxable", INTEGER);
	$r->add_textbox("shipping_points_amount", NUMBER);
	$r->add_textbox("shipping_expecting_date", DATETIME);
	$r->add_textbox("tax_name", TEXT);
	$r->add_textbox("tax_percent", NUMBER);
	$r->add_textbox("tax_total", NUMBER);
	$r->add_textbox("tax_prices_type", NUMBER);
	$r->add_textbox("vouchers_amount", NUMBER);
	$r->add_textbox("credit_amount", NUMBER);

	$r->add_textbox("processing_fee", NUMBER);
	$r->add_textbox("order_total", NUMBER);
	$r->add_textbox("total_points_amount", NUMBER);
	$r->add_textbox("total_reward_points", NUMBER);
	$r->add_textbox("total_reward_credits", NUMBER);
	$r->add_textbox("order_placed_date", DATETIME);

	$companies = get_db_values("SELECT company_id,company_name FROM " . $table_prefix . "companies ", array(array("", SELECT_COMPANY_MSG)));
	$states = get_db_values("SELECT state_id,state_name FROM " . $table_prefix . "states WHERE country_id=".$country_id." and show_for_user=1 ORDER BY state_name ", array(array("", SELECT_STATE_MSG)));  // *** Egghead Ventures Added **WHERE country_id=".$country_id."**
	$countries = get_db_values("SELECT country_id,country_name FROM " . $table_prefix . "countries WHERE show_for_user=1 ORDER BY country_order, country_name ", array(array("", SELECT_COUNTRY_MSG)));

	$r->add_textbox("name", TEXT, NAME_MSG);
	$r->change_property("name", USE_SQL_NULL, false);
	$r->add_textbox("first_name", TEXT, FIRST_NAME_FIELD);
	$r->change_property("first_name", USE_SQL_NULL, false);
	$r->add_textbox("last_name", TEXT, LAST_NAME_FIELD);
	$r->change_property("last_name", USE_SQL_NULL, false);
	$r->add_select("company_id", INTEGER, $companies, COMPANY_SELECT_FIELD);
	$r->add_textbox("company_name", TEXT, COMPANY_NAME_FIELD);
	$r->add_textbox("email", TEXT, EMAIL_FIELD);
	$r->change_property("email", USE_SQL_NULL, false);
	$r->change_property("email", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("address1", TEXT, STREET_FIRST_FIELD);
	$r->add_textbox("address2", TEXT, STREET_SECOND_FIELD);
	$r->add_textbox("city", TEXT, CITY_FIELD);
	$r->add_textbox("province", TEXT, PROVINCE_FIELD);
	$r->add_select("state_id", INTEGER, $states, STATE_FIELD);
	$r->change_property("state_id", USE_SQL_NULL, false);
	$r->add_textbox("state_code", TEXT);
	$r->add_textbox("zip", TEXT, ZIP_FIELD);
	$r->add_select("country_id", INTEGER, $countries, COUNTRY_FIELD);
	$r->change_property("country_id", USE_SQL_NULL, false);
	$r->add_textbox("country_code", TEXT);
	$r->add_textbox("phone", TEXT, PHONE_FIELD);
	$r->change_property("phone", REGEXP_MASK, PHONE_REGEXP);
	$r->add_textbox("daytime_phone", TEXT, DAYTIME_PHONE_FIELD);
	$r->change_property("daytime_phone", REGEXP_MASK, PHONE_REGEXP);
	$r->add_textbox("evening_phone", TEXT, EVENING_PHONE_FIELD);
	$r->change_property("evening_phone", REGEXP_MASK, PHONE_REGEXP);
	$r->add_textbox("cell_phone", TEXT, CELL_PHONE_FIELD);
	$r->change_property("cell_phone", REGEXP_MASK, PHONE_REGEXP);
	$r->add_textbox("fax", TEXT, FAX_FIELD);
	$r->change_property("fax", REGEXP_MASK, PHONE_REGEXP);

	$r->add_textbox("delivery_name", TEXT, DELIVERY_MSG." ".NAME_MSG);
	$r->add_textbox("delivery_first_name", TEXT, DELIVERY_MSG." ".FIRST_NAME_FIELD);
	$r->add_textbox("delivery_last_name", TEXT, DELIVERY_MSG." ".LAST_NAME_FIELD);
	$r->add_select("delivery_company_id", INTEGER, $companies, DELIVERY_MSG." ".COMPANY_SELECT_FIELD);
	$r->add_textbox("delivery_company_name", TEXT, DELIVERY_MSG." ".COMPANY_NAME_FIELD);
	$r->add_textbox("delivery_email", TEXT, DELIVERY_MSG." ".EMAIL_FIELD);
	$r->change_property("delivery_email", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("delivery_address1", TEXT, DELIVERY_MSG." ".STREET_FIRST_FIELD);
	$r->add_textbox("delivery_address2", TEXT, DELIVERY_MSG." ".STREET_SECOND_FIELD);
	$r->add_textbox("delivery_city", TEXT, DELIVERY_MSG." ".CITY_FIELD);
	$r->add_textbox("delivery_province", TEXT, DELIVERY_MSG." ".PROVINCE_FIELD);
	$r->add_select("delivery_state_id", INTEGER, $states, DELIVERY_MSG." ".STATE_FIELD);
	$r->change_property("delivery_state_id", USE_SQL_NULL, false);
	$r->add_textbox("delivery_state_code", TEXT);
	$r->add_textbox("delivery_zip", TEXT, DELIVERY_MSG." ".ZIP_FIELD);
	$r->add_select("delivery_country_id", INTEGER, $countries, DELIVERY_MSG." ".COUNTRY_FIELD);
	$r->add_textbox("delivery_country_code", TEXT);
	$r->change_property("delivery_country_id", USE_SQL_NULL, false);
	$r->add_textbox("delivery_phone", TEXT, DELIVERY_MSG." ".PHONE_FIELD);
	$r->change_property("delivery_phone", REGEXP_MASK, PHONE_REGEXP);
	$r->add_textbox("delivery_daytime_phone", TEXT, DELIVERY_MSG." ".DAYTIME_PHONE_FIELD);
	$r->change_property("delivery_daytime_phone", REGEXP_MASK, PHONE_REGEXP);
	$r->add_textbox("delivery_evening_phone", TEXT, DELIVERY_MSG." ".EVENING_PHONE_FIELD);
	$r->change_property("delivery_evening_phone", REGEXP_MASK, PHONE_REGEXP);
	$r->add_textbox("delivery_cell_phone", TEXT, DELIVERY_MSG." ".CELL_PHONE_FIELD);
	$r->change_property("delivery_cell_phone", REGEXP_MASK, PHONE_REGEXP);
	$r->add_textbox("delivery_fax", TEXT, DELIVERY_MSG." ".FAX_FIELD);
	$r->change_property("delivery_fax", REGEXP_MASK, PHONE_REGEXP);

	$personal_number = 0;
	$delivery_number = 0;
	for ($i = 0; $i < sizeof($parameters); $i++)
	{
		$param_name = $parameters[$i];
		$r->change_property($param_name, TRIM, true);
		$r->change_property("delivery_" . $param_name, TRIM, true);

		$personal_param = "show_" . $parameters[$i];
		$delivery_param = "show_delivery_" . $parameters[$i];
		if (isset($order_info[$personal_param]) && $order_info[$personal_param] == 1) {
			$personal_number++;
			if ($order_info[$parameters[$i] . "_required"] == 1) {
				$r->parameters[$parameters[$i]][REQUIRED] = true;
			}
		} else {
			$r->parameters[$parameters[$i]][SHOW] = false;
		}
		if (isset($order_info[$delivery_param]) && $order_info[$delivery_param] == 1) {
			$delivery_number++;
			if ($order_info["delivery_" . $parameters[$i] . "_required"] == 1) {
				$r->parameters["delivery_" . $parameters[$i]][REQUIRED] = true;
			}
		} else {
			$r->parameters["delivery_" . $parameters[$i]][SHOW] = false;
		}
	}

	$r->add_checkbox("same_as_personal", INTEGER);
	$r->change_property("same_as_personal", USE_IN_SELECT, false);
	$r->change_property("same_as_personal", USE_IN_INSERT, false);
	$r->change_property("same_as_personal", USE_IN_UPDATE, false);
	if ($personal_number < 1 || $delivery_number < 1) {
		$r->parameters["same_as_personal"][SHOW] = false;
	}
	$r->add_checkbox("subscribe", INTEGER);
	$r->change_property("subscribe", USE_IN_SELECT, false);
	$r->change_property("subscribe", USE_IN_INSERT, false);
	$r->change_property("subscribe", USE_IN_UPDATE, false);
	if (!$subscribe_block) {
		$r->parameters["subscribe"][SHOW] = false;
	}

	$r->get_form_values();
	$r->set_value("session_id", session_id());
	$r->set_value("user_id", $user_id);
	$r->set_value("user_type_id", $user_type_id);
	$r->set_value("payment_id", $payment_id);
	$r->set_value("remote_address", $user_ip);
	$r->set_value("initial_ip", $initial_ip);
	$r->set_value("cookie_ip", $cookie_ip);
	$r->set_value("visit_id", $visit_id);
	$r->set_value("affiliate_code", $affiliate_code);
	$r->set_value("affiliate_user_id", $affiliate_user_id);
	$r->set_value("friend_code", $friend_code);
	$r->set_value("friend_user_id", $friend_user_id);
	$r->set_value("keywords", $keywords);
	$r->set_value("properties_total", $properties_total);
	$r->set_value("properties_taxable", $properties_taxable);
	$r->set_value("properties_points_amount", $properties_points_amount);
	$r->set_value("tax_name", $tax_names);
	$r->set_value("tax_percent", $tax_percent_sum);
	$r->set_value("tax_total", $taxes_total);
	$r->set_value("tax_prices_type", $tax_prices_type);
	$r->set_value("credit_amount", $order_credit_amount);
	$r->set_value("processing_fee", $processing_fee);
	$r->set_value("default_currency_code", $default_currency_code);
	$r->set_value("currency_code", $currency["code"]);
	$r->set_value("currency_rate", $currency["rate"]);
	if (get_setting_value($order_info, "show_delivery_country_id", 0) != 1 && get_setting_value($order_info, "show_country_id", 0) != 1) {
		$r->set_value("country_id", $country_id);
		$r->set_value("delivery_country_id", $country_id);
	}
	if ($operation == "fast_checkout") {
		if ($order_info["show_delivery_country_id"] == 1) {
			$r->set_value("delivery_country_id", $country_id);
		} else {
			$r->set_value("country_id", $country_id);
		}
		if ($order_info["show_delivery_state_id"] == 1) {
			$r->set_value("delivery_state_id", $state_id);
		} else {
			$r->set_value("state_id", $state_id);
		}
		if ($order_info["show_delivery_zip"] == 1) {
			$r->set_value("delivery_zip", $postal_code);
		} else {
			$r->set_value("zip", $postal_code);
		}
	}

	$variables["user_id"] = $r->get_value("user_id");
	$variables["tax_name"] = $tax_names;
	$variables["tax_percent"] = $tax_percent_sum;

	if ($delivery_errors) {
		$delivery_errors = str_replace("{country_name}", get_array_value($country_id, $countries), $delivery_errors);
		$sc_errors .= $delivery_errors;
	}

	if (strlen($operation))
	{
		if ($is_update) {
			if ($total_shipping_types > 1 && !strlen($shipping_type_id)) {
				$r->errors .= REQUIRED_DELIVERY_MSG . "<br>";
			}

			if ($r->get_value("same_as_personal")) {
				for ($i = 0; $i < sizeof($parameters); $i++) {
					$personal_param = "show_" . $parameters[$i];
					$delivery_param = "show_delivery_" . $parameters[$i];
					if (isset($order_info[$delivery_param]) && isset($order_info[$personal_param]) &&
					$order_info[$delivery_param] == 1 && $order_info[$personal_param] == 1) {
						$r->set_value("delivery_" . $parameters[$i], $r->get_value($parameters[$i]));
					}
				}
			}

			$r->validate();
			$r->errors .= $options_errors;
			if (strlen($r->errors) || strlen($sc_errors)) {
				$is_valid = false;
			} else {
				$is_valid = true;
			}
		} elseif ($operation == "fast_checkout") {
			$is_valid = true;
		} else {
			$is_valid = false;
		}

		if ($is_valid && check_black_ip()) {
			$r->errors = BLACK_IP_MSG;
			$is_valid = false;
		}


		if ($is_valid)
		{
			// get payment rate for the selected gateway
			$payment_rate = get_payment_rate($payment_id, $currency["rate"]);
			$payment_decimals = $currency["decimals"];
			$variables["tax_cost"] = number_format($taxes_total * $payment_rate, $payment_decimals, ".", "");
			$variables["tax_total"] = number_format($taxes_total * $payment_rate, $payment_decimals, ".", "");
			$variables["processing_fee"] = number_format($processing_fee * $payment_rate, $payment_decimals, ".", "");

			$new_order_status = 1;
			// set status to zero when adding order
			$r->set_value("order_status", 0);

			$variables["total_buying"] = number_format($total_buying * $payment_rate, $payment_decimals, ".", "");
			$variables["total_buying_tax"] = number_format($total_buying_tax * $payment_rate, $payment_decimals, ".", "");
			$variables["total_merchants_commission"] = number_format($total_merchants_commission * $payment_rate, $payment_decimals, ".", "");
			$variables["total_affiliate_commission"] = number_format($total_affiliate_commission * $payment_rate, $payment_decimals, ".", "");
			$variables["goods_total"] = number_format($goods_total * $payment_rate, $payment_decimals, ".", "");
			$variables["weight_total"] = ($weight_total + $tare_weight);

			$variables["coupons_ids"] = $order_coupons_ids;
			$variables["vouchers_ids"] = $vouchers_ids;
			$variables["vouchers_amount"] = $vouchers_amount;
			$variables["default_currency_code"] = $default_currency_code;
			$variables["currency_code"] = $currency["code"];
			$variables["currency_value"] = $currency["value"];
			$variables["currency_rate"] = $currency["rate"];
			$variables["total_discount"] = number_format($total_discount * $payment_rate, $payment_decimals, ".", "");
			$variables["total_discount_tax"] = number_format($total_discount_tax * $payment_rate, $payment_decimals, ".", "");
			$goods_with_discount = $goods_total - $total_discount;
			$variables["goods_with_discount"] = number_format($goods_with_discount * $payment_rate, $payment_decimals, ".", "");
			$variables["properties_total"] = number_format($properties_total * $payment_rate, $payment_decimals, ".", "");
			$variables["properties_taxable"] = number_format($properties_taxable * $payment_rate, $payment_decimals, ".", "");
			$variables["properties_points_amount"] = number_format($properties_points_amount, $points_decimals);
			$variables["shipping_type_id"] = $shipping_type_id;
			$variables["shipping_type_code"] = $shipping_type_code;
			$variables["shipping_type"] = $shipping_type_desc;
			$variables["shipping_cost"] = number_format($shipping_cost * $payment_rate, $payment_decimals, ".", "");
			$variables["shipping_taxable"] = $shipping_taxable;
			$variables["shipping_points_amount"] = $shipping_points_amount;

			// calculate shipping expecting date excluding sundays
			$handle_hours = $max_availability_time + $shipping_time + $processing_time;
			$shipping_expecting_date = get_expecting_date($handle_hours);
			$variables["shipping_expecting_date"] = $shipping_expecting_date;

			$r->set_value("total_buying", $total_buying);
			$r->set_value("total_buying_tax", $total_buying_tax);
			$r->set_value("total_merchants_commission", $total_merchants_commission);
			$r->set_value("total_affiliate_commission", $total_affiliate_commission);
			$r->set_value("goods_total",  $goods_total);
			$r->set_value("goods_incl_tax",  0); // todo: this field should always has value with tax
			$r->set_value("goods_tax",  $goods_tax_total);
			$r->set_value("goods_points_amount",  $goods_points_amount);
			$r->set_value("total_quantity", $total_quantity);
			$r->set_value("weight_total",  ($weight_total + $tare_weight));

			$r->set_value("coupons_ids", $order_coupons_ids);
			$r->set_value("total_discount", $total_discount);
			$r->set_value("total_discount_tax", $total_discount_tax);
			$r->set_value("vouchers_ids", $vouchers_ids);
			$r->set_value("vouchers_amount", $vouchers_amount);
			$r->set_value("shipping_type_id", $shipping_type_id);
			$r->set_value("shipping_type_code", $shipping_type_code);
			$r->set_value("shipping_type_desc", $shipping_type_desc);
			$r->set_value("shipping_cost", $shipping_cost);
			$r->set_value("shipping_taxable", $shipping_taxable);
			$r->set_value("shipping_points_amount", $shipping_points_amount);
			$r->set_value("shipping_expecting_date", va_time($shipping_expecting_date));
			if (isset($site_id)) {
				$r->set_value("site_id", $site_id);
			} else {
				$r->set_value("site_id", 1);
			}
			for ($i = 0; $i < sizeof($parameters); $i++) {
				$variables[$parameters[$i]] = $r->get_value($parameters[$i]);
				$variables["delivery_" . $parameters[$i]] = $r->get_value("delivery_" . $parameters[$i]);
			}

			// prepare user name variables
			if (strlen($variables["name"]) && !strlen($variables["first_name"]) && !strlen($variables["last_name"])) {
				$name = $variables["name"];
				$name_parts = explode(" ", $name, 2);
				if (sizeof($name_parts) == 2) {
					$variables["first_name"] = $name_parts[0];
					$variables["last_name"] = $name_parts[1];
				} else {
					$variables["first_name"] = $name_parts[0];
					$variables["last_name"] = "";
				}
			} elseif (!strlen($variables["name"]) && (strlen($variables["first_name"]) || strlen($variables["last_name"]))) {
				$variables["name"] = trim($variables["first_name"] . " " . $variables["last_name"]);
			}


			$address = $r->get_value("address2") ? ($r->get_value("address1") . " " . $r->get_value("address2")) : $r->get_value("address1");
			$delivery_address = $r->get_value("delivery_address2") ? ($r->get_value("delivery_address1") . " " . $r->get_value("delivery_address2")) : $r->get_value("delivery_address1");
			$variables["address"] = $address;
			$variables["delivery_address"] = $delivery_address;
			$variables["company_select"] = get_array_value($r->get_value("company_id"), $companies);
			$variables["state"] = get_array_value($r->get_value("state_id"), $states);
			$variables["state_code"] = ""; 
			$sql = "SELECT * FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($variables["state_id"], INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$variables["state_code"] = $db->f("state_code");
				$r->set_value("state_code", $variables["state_code"]);
			}
			if (strlen($variables["state_code"])) {
				$variables["state_code_or_province"] = $variables["state_code"];
				$variables["state_or_province"] = $variables["state"];
			} else {
				$variables["state_code_or_province"] = $variables["province"];
				$variables["state_or_province"] = $variables["province"];
			}
			$variables["country"] = get_array_value($r->get_value("country_id"), $countries);
			$country_code = ""; $country_number = "";
			$sql = "SELECT * FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($variables["country_id"], INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$country_code = $db->f("country_code");
				$country_number = $db->f("country_iso_number");
				$r->set_value("country_code", $country_code);
			}
			$variables["country_code"] = $country_code;
			$variables["country_number"] = $country_number;
			$variables["delivery_company_select"] = get_array_value($r->get_value("delivery_company_id"), $companies);
			$variables["delivery_state"] = get_array_value($r->get_value("delivery_state_id"), $states);
			$variables["delivery_state_code"] = ""; 
			$sql = "SELECT * FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($variables["delivery_state_id"], INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$variables["delivery_state_code"] = $db->f("state_code");
				$r->set_value("delivery_state_code", $variables["delivery_state_code"]);
			}
			if (strlen($variables["delivery_state_code"])) {
				$variables["delivery_state_code_or_province"] = $variables["delivery_state_code"];
				$variables["delivery_state_or_province"] = $variables["delivery_state"];
			} else {
				$variables["delivery_state_code_or_province"] = $variables["delivery_province"];
				$variables["delivery_state_or_province"] = $variables["delivery_province"];
			}
			$variables["delivery_country"] = get_array_value($r->get_value("delivery_country_id"), $countries);
			$delivery_country_code = ""; $delivery_country_number = "";
			$sql = "SELECT * FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($variables["delivery_country_id"], INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$delivery_country_code = $db->f("country_code");
				$delivery_country_number = $db->f("country_iso_number");
				$r->set_value("delivery_country_code", $delivery_country_code);
			}
			$variables["delivery_country_code"] = $delivery_country_code;
			$variables["delivery_country_number"] = $delivery_country_number;
			$t->set_var("company_select", $variables["company_select"]);
			$t->set_var("state", $variables["state"]);
			$t->set_var("country", $variables["country"]);
			$t->set_var("delivery_company_select", $variables["delivery_company_select"]);
			$t->set_var("delivery_state", $variables["delivery_state"]);
			$t->set_var("delivery_country", $variables["delivery_country"]);

			$r->set_value("order_total",  $order_total);
			$r->set_value("total_points_amount",  $total_points_amount);
			$r->set_value("total_reward_points",  $total_reward_points);
			$r->set_value("total_reward_credits",  $total_reward_credits);

			$variables["order_total"] = number_format($order_total * $payment_rate, $payment_decimals, ".", "");
			$variables["order_total_100"] = round($order_total * $payment_rate * 100, 0);
			$variables["total_points_amount"] = $total_points_amount;
			$variables["total_reward_points"] = $total_reward_points;
			$variables["total_reward_credits"] = $total_reward_credits;

			$variables["items"] = $items_text;
			$variables["basket"] = $items_text;
			$variables["description"] = $items_text;

			$t->parse("basket", false);
			$basket_parsed = true;

			$order_placed_date = va_time();
			$order_placed_date_string = va_date($datetime_show_format, $order_placed_date);
			$timestamp = mktime ($order_placed_date[HOUR], $order_placed_date[MINUTE], $order_placed_date[SECOND], $order_placed_date[MONTH], $order_placed_date[DAY], $order_placed_date[YEAR]);

			$variables["timestamp"] = $timestamp;
			$variables["order_placed_timestamp"] = $timestamp;
			$variables["order_placed_date"] = $order_placed_date;
			$r->set_value("order_placed_date", $order_placed_date);
			$order_added = true;
			// check if order was already placed
			$user_order_id = get_session("session_user_order_id");
			if ($user_order_id) {
				$sql  = " SELECT o.transaction_id, o.is_placed, o.order_status, os.paid_status ";
				$sql .= " FROM (" . $table_prefix . "orders o ";
				$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
				$sql .= " WHERE o.order_id=" . $db->tosql($user_order_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$is_placed = $db->f("is_placed");
					$paid_status = $db->f("paid_status");
					$transaction_id = $db->f("transaction_id");
					if ($is_placed || $paid_status || strlen($transaction_id)) {
						$user_order_id = "";
					}
				} else {
					$user_order_id = "";
				}
			}
			if ($user_order_id) {
				$order_id = $user_order_id;
				$variables["order_id"] = $user_order_id;
				$r->set_value("order_id", $user_order_id);
				remove_orders($user_order_id, false);
				$order_added = $r->update_record();
			} else {
				if ($db_type == "postgre") {
					$order_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "orders') ");
					$variables["order_id"] = $order_id;
					$r->change_property("order_id", USE_IN_INSERT, true);
					$r->set_value("order_id", $order_id);
				}
				$order_added = $r->insert_record();
			}
			if ($order_added)
			{
				if (!$user_order_id) {
					if ($db_type == "mysql") {
						$order_id = get_db_value(" SELECT LAST_INSERT_ID() ");
						$r->set_value("order_id", $order_id);
						$variables["order_id"] = $order_id;
					} elseif ($db_type == "access") {
						$order_id = get_db_value(" SELECT @@IDENTITY ");
						$r->set_value("order_id", $order_id);
						$variables["order_id"] = $order_id;
					} elseif ($db_type == "db2") {
						$order_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "orders FROM " . $table_prefix . "orders");
						$r->set_value("order_id", $order_id);
						$variables["order_id"] = $order_id;
					}
				}
				// generate and update invoice number
				$invoice_number = generate_invoice_number($order_id);
				$variables["invoice_number"] = $invoice_number;
				$sql  = " UPDATE " . $table_prefix . "orders ";
				$sql .= " SET invoice_number=" . $db->tosql($invoice_number, TEXT);
				$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
				$db->query($sql);

				// increment used order coupons by one if they exists
				if (strlen($order_coupons_ids)) {
					$sql  = " UPDATE " . $table_prefix . "coupons SET coupon_uses=coupon_uses+1 ";
					$sql .= " WHERE coupon_id IN (" . $db->tosql($order_coupons_ids, INTEGERS_LIST) . ") ";
					$db->query($sql);
				}
				foreach ($gift_vouchers as $voucher_id => $voucher_info) {
					$voucher_amount = $voucher_info["amount"];
					if ($voucher_amount > 0) {
						$sql  = " UPDATE " . $table_prefix . "coupons ";
						$sql .= " SET coupon_uses=coupon_uses+1, discount_amount=discount_amount-" . $db->tosql($voucher_amount, NUMBER);
						$sql .= " WHERE coupon_id=" . $db->tosql($voucher_id, INTEGER);
						$db->query($sql);
					}
				}

				// save tax rates for submitted order
				if ($tax_available && is_array($tax_rates)) {
					$ot = new VA_Record($table_prefix . "orders_taxes");
					$ot->add_where("order_tax_id", INTEGER);
					$ot->add_textbox("order_id", INTEGER);
					$ot->set_value("order_id", $order_id);
					$ot->add_textbox("tax_id", INTEGER);
					$ot->add_textbox("tax_name", TEXT);
					$ot->add_textbox("tax_percent", FLOAT);
					$ot->add_textbox("shipping_tax_percent", FLOAT);

					$oit = new VA_Record($table_prefix . "orders_items_taxes");
					$oit->add_textbox("order_tax_id", INTEGER);
					$oit->add_textbox("item_type_id", INTEGER);
					$oit->add_textbox("tax_percent", FLOAT);

					foreach ($tax_rates as $tax_id => $tax_rate) {
						if ($db_type == "postgre") {
							$order_tax_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "orders_taxes') ");
							$r->change_property("order_tax_id", USE_IN_INSERT, true);
							$r->set_value("order_tax_id", $order_tax_id);
						}
						$ot->set_value("tax_id", $tax_id);
						$ot->set_value("tax_name", $tax_rate["tax_name"]);
						$ot->set_value("tax_percent", $tax_rate["tax_percent"]);
						$ot->set_value("shipping_tax_percent", $tax_rate["shipping_tax_percent"]);
						if ($ot->insert_record()) {
							// save taxes for item types if they available
							$tax_types = isset($tax_rate["types"]) ? $tax_rate["types"] : "";
							if (is_array($tax_types)) {
								if ($db_type == "mysql") {
									$order_tax_id = get_db_value(" SELECT LAST_INSERT_ID() ");
								} elseif ($db_type == "access") {
									$order_tax_id = get_db_value(" SELECT @@IDENTITY ");
								} elseif ($db_type == "db2") {
									$order_tax_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "orders_taxes FROM " . $table_prefix . "orders_taxes");
								}
								$oit->set_value("order_tax_id", $order_tax_id);
								foreach ($tax_types as $item_type_id => $item_tax_percent) {
									$oit->set_value("item_type_id", $item_type_id);
									$oit->set_value("tax_percent", $item_tax_percent);
									$oit->insert_record();
								}
							}
						}
					} 
				} // end of saving order taxes rules

				// subscribe/unsubscribe user from newsletter
				if ($subscribe_block) {
					$subscribe_email = $r->get_value("email");
					if (!$subscribe_email && $r->get_value("delivery_email")) {
						$subscribe_email = $r->get_value("delivery_email");
					}
					if ($subscribe_email) {
						if ($r->get_value("subscribe") == 1) {
							$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "newsletters_users ";
							$sql .= " WHERE email=" . $db->tosql($subscribe_email, TEXT);
							$db->query($sql);
							$db->next_record();
							$email_count = $db->f(0);
							if ($email_count < 1) {
								$sql  = " INSERT INTO " . $table_prefix . "newsletters_users (email, date_added) ";
								$sql .= " VALUES (";
								$sql .= $db->tosql($subscribe_email, TEXT) . ", ";
								$sql .= $db->tosql(va_time(), DATETIME) . ") ";
								$db->query($sql);
							}
						} else {
							$sql  = " DELETE FROM " . $table_prefix . "newsletters_users ";
							$sql .= " WHERE email=" . $db->tosql($subscribe_email, TEXT);
							$db->query($sql);
						}
					}
				}


				$op = new VA_Record($table_prefix . "orders_properties");
				$op->add_textbox("order_id", INTEGER);
				$op->set_value("order_id", $order_id);
				$op->add_textbox("property_id", INTEGER);
				$op->add_textbox("property_order", INTEGER);
				$op->add_textbox("property_type", INTEGER);
				$op->add_textbox("property_name", TEXT);
				$op->add_textbox("property_value_id", INTEGER);
				$op->add_textbox("property_value", TEXT);
				$op->add_textbox("property_price", FLOAT);
				$op->add_textbox("property_points_amount", FLOAT);
				$op->add_textbox("property_weight", FLOAT);
				$op->add_textbox("tax_free", INTEGER);
				foreach ($custom_options as $property_id => $property_values) {
          $property_full_desc = ""; $property_total_price = 0;
					foreach ($property_values as $value_id => $value_data) {
						$property_type = $value_data["type"];
						$property_order = $value_data["order"];
						$property_name = $value_data["name"];
						$property_value_id = $value_data["value_id"];
						$property_value = $value_data["value"];
						$property_price = $value_data["price"];
						$property_tax_free = $value_data["tax_free"];
						$property_points_price = $value_data["points_price"];
						$property_pay_points = $value_data["pay_points"];
						if ($property_pay_points) {
							$property_price = 0;
						} else {
							$property_points_price = 0;
						}
						if ($property_full_desc) { $property_full_desc .= "; "; }
						$property_full_desc .= $property_value;
						$property_total_price += $property_price;
				  
						$op->set_value("property_id", $property_id);
						$op->set_value("property_order", $property_order);
						$op->set_value("property_type", $property_type);
						$op->set_value("property_name", $property_name);
						$op->set_value("property_value_id", $property_value_id);
						$op->set_value("property_value", $property_value);
						$op->set_value("property_price", $property_price);
						$op->set_value("property_points_amount", $property_points_price);
						$op->set_value("property_weight", 0);
						$op->set_value("tax_free", $property_tax_free);
				  
						$op->insert_record();
					}
					$t->set_var("field_name_" . $property_id, $property_name);
					$t->set_var("field_value_" . $property_id, $property_full_desc);
					$t->set_var("field_price_" . $property_id, $property_total_price);
					$t->set_var("field_" . $property_id, $property_full_desc);
				}

				// save order coupons
				$oc = new VA_Record($table_prefix . "orders_coupons");
				$oc->add_textbox("order_id", INTEGER);
				$oc->set_value("order_id", $order_id);
				$oc->add_textbox("coupon_id", INTEGER);
				$oc->add_textbox("coupon_code", TEXT);
				$oc->add_textbox("coupon_title", TEXT);
				$oc->add_textbox("discount_amount", FLOAT);
				$oc->add_textbox("discount_tax_amount", FLOAT);
				for ($i = 0; $i < sizeof($order_coupons); $i++)
				{
					$order_coupon = $order_coupons[$i];
					$oc->set_value("coupon_id", $order_coupon["coupon_id"]);
					$oc->set_value("coupon_code", $order_coupon["coupon_code"]);
					$oc->set_value("coupon_title", $order_coupon["coupon_title"]);
					$oc->set_value("discount_amount", $order_coupon["discount_amount"]);
					$oc->set_value("discount_tax_amount", $order_coupon["discount_tax_amount"]);
					$oc->insert_record();
				}
				foreach ($gift_vouchers as $voucher_id => $voucher_info)
				{
					if (isset($voucher_info["amount"]) && $voucher_info["amount"] > 0) {
						$oc->set_value("coupon_id", $voucher_id);
						$oc->set_value("coupon_code", $voucher_info["code"]);
						$oc->set_value("coupon_title", $voucher_info["title"]);
						$oc->set_value("discount_amount", $voucher_info["amount"]);
						$oc->set_value("discount_tax_amount", 0);
						$oc->insert_record();
					}
				}


				$oi = new VA_Record($table_prefix . "orders_items");
				$oi->add_where("order_item_id", INTEGER);
				$oi->add_textbox("order_id", INTEGER);
				$oi->set_value("order_id", $order_id);

				$oi->add_textbox("site_id", INTEGER);
				$oi->change_property("site_id", USE_SQL_NULL, false);
				if (isset($site_id)) {
					$oi->set_value("site_id", $site_id);
				} else {
					$oi->set_value("site_id", 1);
				}

				$oi->add_textbox("top_order_item_id", INTEGER);
				$oi->change_property("top_order_item_id", USE_SQL_NULL, false);
				$oi->add_textbox("user_id", INTEGER);
				$oi->set_value("user_id", $user_id);
				$oi->add_textbox("user_type_id", INTEGER);
				$oi->set_value("user_type_id", $user_type_id);
				$oi->add_textbox("item_id", INTEGER);
				$oi->add_textbox("parent_item_id", INTEGER);
				$oi->add_textbox("cart_item_id", INTEGER);
				$oi->change_property("cart_item_id", USE_SQL_NULL, false);
				$oi->add_textbox("item_user_id", INTEGER);
				$oi->change_property("item_user_id", USE_SQL_NULL, false);
				$oi->add_textbox("affiliate_user_id", INTEGER);
				$oi->change_property("affiliate_user_id", USE_SQL_NULL, false);
				$oi->add_textbox("friend_user_id", INTEGER);
				$oi->change_property("friend_user_id", USE_SQL_NULL, false);
				$oi->add_textbox("item_type_id", INTEGER);
				$oi->add_textbox("supplier_id", INTEGER);
				$oi->add_textbox("item_code", TEXT);
				$oi->add_textbox("manufacturer_code", TEXT);
				$oi->add_textbox("coupons_ids", TEXT);
				$oi->add_textbox("item_status", INTEGER);
				$oi->set_value("item_status", 0);
				$oi->add_textbox("component_order", INTEGER);
				$oi->add_textbox("component_name", TEXT);
				$oi->add_textbox("item_name", TEXT);
				$oi->add_textbox("item_properties", TEXT);
				$oi->add_textbox("buying_price", NUMBER);
				$oi->add_textbox("real_price", NUMBER);
				$oi->add_textbox("discount_amount", NUMBER);
				$oi->add_textbox("price", NUMBER);
				$oi->add_textbox("tax_free", INTEGER);
				$oi->add_textbox("tax_percent", NUMBER);
				$oi->add_textbox("points_price", NUMBER);
				$oi->add_textbox("reward_points", NUMBER);
				$oi->add_textbox("reward_credits", NUMBER);
				$oi->add_textbox("merchant_commission", NUMBER);
				$oi->add_textbox("affiliate_commission", NUMBER);
				$oi->add_textbox("packages_number", NUMBER);
				$oi->add_textbox("weight", NUMBER);
				$oi->add_textbox("width", NUMBER);
				$oi->add_textbox("height", NUMBER);
				$oi->add_textbox("length", NUMBER);
				$oi->add_textbox("quantity", NUMBER);
				$oi->add_textbox("downloadable", NUMBER);
				$oi->add_textbox("is_shipping_free", INTEGER);
				$oi->add_textbox("shipping_cost", NUMBER);
				$oi->add_textbox("shipping_expecting_date", DATETIME);
				// recurring fields
				$oi->add_textbox("is_recurring", INTEGER);
				$oi->add_textbox("recurring_price", INTEGER);
				$oi->add_textbox("recurring_period", INTEGER);
				$oi->add_textbox("recurring_interval", INTEGER);
				$oi->add_textbox("recurring_payments_total", INTEGER);
				$oi->add_textbox("recurring_payments_made", INTEGER);
				$oi->add_textbox("recurring_payments_failed", INTEGER);
				$oi->add_textbox("recurring_end_date", DATETIME);
				$oi->add_textbox("recurring_last_payment", DATETIME);
				$oi->add_textbox("recurring_next_payment", DATETIME);
				$oi->add_textbox("recurring_plan_payment", DATETIME);
				// recurring fields
				$oi->add_textbox("is_subscription", INTEGER);
				$oi->add_textbox("is_account_subscription", INTEGER);
				$oi->add_textbox("subscription_id", INTEGER);
				$oi->change_property("subscription_id", USE_SQL_NULL, false);
				$oi->add_textbox("subscription_period",   INTEGER);
				$oi->add_textbox("subscription_interval", INTEGER);
				$oi->add_textbox("subscription_suspend",  INTEGER);
				
				$oip = new VA_Record($table_prefix . "orders_items_properties");
				$oip->add_textbox("order_id", INTEGER);
				$oip->set_value("order_id", $order_id);
				$oip->add_textbox("order_item_id", INTEGER);
				$oip->add_textbox("property_id", INTEGER);
				$oip->add_textbox("property_order", INTEGER);
				$oip->add_textbox("property_name", TEXT);
				$oip->add_textbox("property_value", TEXT);
				$oip->add_textbox("property_values_ids", TEXT);
				$oip->add_textbox("additional_price", NUMBER);
				$oip->add_textbox("additional_weight", NUMBER);

				$r_id = new VA_Record($table_prefix . "items_downloads");
				$r_id->add_textbox("order_id", INTEGER);
				$r_id->set_value("order_id", $order_id);
				$r_id->add_textbox("user_id", INTEGER);
				$r_id->set_value("user_id", $user_id);
				$r_id->add_textbox("order_item_id", INTEGER);
				$r_id->add_textbox("item_id", INTEGER);
				$r_id->add_textbox("download_path", TEXT);
				$r_id->add_textbox("activated", INTEGER);
				$r_id->add_textbox("max_downloads", INTEGER); // how many times from different IPs user can download product during the month
				$r_id->add_textbox("download_added", DATETIME);
				$r_id->add_textbox("download_expiry", DATETIME);
				$r_id->add_textbox("download_limit", INTEGER); // how many times user can download product

				$ois = new VA_Record($table_prefix . "orders_items_serials");
				$ois->add_textbox("order_id", INTEGER);
				$ois->set_value("order_id", $order_id);
				$ois->add_textbox("user_id", INTEGER);
				$ois->set_value("user_id", $user_id);
				$ois->add_textbox("order_item_id", INTEGER);
				$ois->add_textbox("item_id", INTEGER);
				$ois->add_textbox("serial_number", TEXT);
				$ois->add_textbox("activated", INTEGER);
				$ois->add_textbox("activations_number", INTEGER);
				$ois->add_textbox("serial_added", DATETIME);
				$ois->add_textbox("serial_expiry", DATETIME);

				$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
				$sql .= " WHERE setting_type='download_info' ";
				$sql .= " AND setting_name='max_downloads' ";
				if (isset($site_id)) {
					$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
					$sql .= " ORDER BY site_id DESC ";
				} else {
					$sql .= " AND site_id=1 ";
				}
				$max_downloads = get_db_value($sql);

				foreach ($cart_items as $id => $cart_item) {
					$item_id = $cart_item["item_id"];
					$parent_item_id = $cart_item["parent_item_id"];
					$top_order_item_id = isset($cart_item["top_order_item_id"]) ? $cart_item["top_order_item_id"] : 0;
					$wishlist_item_id = $cart_item["wishlist_item_id"];
					$item_user_id = $cart_item["item_user_id"];
					$item_type_id = $cart_item["item_type_id"];
					$supplier_id = $cart_item["supplier_id"];
					$item_code = $cart_item["item_code"];
					$manufacturer_code = $cart_item["manufacturer_code"];
					$item_coupons_ids = $cart_item["coupons_ids"];
					$component_order = $cart_item["selection_order"];
					$component_name = $cart_item["selection_name"];
					$item_name = $cart_item["item_name"];
					$properties_info = $cart_item["properties_info"];
					$buying_price = $cart_item["full_buying_price"];
					$price = $cart_item["full_price"];
					$price_incl_tax = $cart_item["price_incl_tax"];
					$real_price = $cart_item["full_real_price"];
					$item_discount = $real_price - $price;

					// check points options
					$pay_points = $cart_item["pay_points"];
					$reward_points = $cart_item["reward_points"];
					$reward_credits = $cart_item["reward_credits"];
					$points_price = $cart_item["points_price"];
					$merchant_commission = $cart_item["merchant_commission"];
					$affiliate_commission = $cart_item["affiliate_commission"];
					if ($pay_points) {
						$price = 0;
						$merchant_commission = 0; $affiliate_commission = 0;
						if (!$points_for_points) { $reward_points = 0; }
						if (!$credits_for_points) { $reward_credits = 0; }
					} else {
						$points_price = 0;
					}

					$item_tax_free = $cart_item["tax_free"];
					$item_tax_percent = $cart_item["tax_percent"];
					if ($tax_free) {
						$item_tax_percent = 0;
					}
					$packages_number = $cart_item["packages_number"];
					$weight = $cart_item["full_weight"];
					$width = $cart_item["width"];
					$height = $cart_item["height"];
					$length = $cart_item["length"];
					$quantity = $cart_item["quantity"];
					$stock_level = $cart_item["stock_level"];
					$availability_time = $cart_item["availability_time"];
					$downloads = $cart_item["downloads"];
					$generate_serial = $cart_item["generate_serial"];
					$serial_period = $cart_item["serial_period"];
					$activations_number = $cart_item["activations_number"];
					$is_gift_voucher = $cart_item["is_gift_voucher"];
					$is_shipping_free = $cart_item["is_shipping_free"];
					$shipping_cost = $cart_item["shipping_cost"];

					//recurring fields
					$is_recurring = $cart_item["is_recurring"];
					$recurring_price = $cart_item["recurring_price"];
					$recurring_period = $cart_item["recurring_period"];
					$recurring_interval = $cart_item["recurring_interval"];
					$recurring_payments_total = $cart_item["recurring_payments_total"];
					$recurring_start_date = $cart_item["recurring_start_date"];
					$recurring_end_date = $cart_item["recurring_end_date"];

					$is_subscription = $cart_item["is_subscription"];
					$is_account_subscription = $cart_item["is_account_subscription"];
					$subscription_id = isset($cart_item["subscription_id"]) ? $cart_item["subscription_id"] : "";
					$subscription_period = $cart_item["subscription_period"];
					$subscription_interval = $cart_item["subscription_interval"];
					$subscription_suspend = $cart_item["subscription_suspend"];

					$components = isset($cart_item["components"]) ? $cart_item["components"] : "";

					$oi->set_value("top_order_item_id", $top_order_item_id);
					$oi->set_value("item_id", $item_id);
					$oi->set_value("parent_item_id", $parent_item_id);
					$oi->set_value("cart_item_id", $wishlist_item_id);
					$oi->set_value("item_user_id", $item_user_id);
					$oi->set_value("affiliate_user_id", $affiliate_user_id);
					$oi->set_value("friend_user_id", $friend_user_id);
					$oi->set_value("item_type_id", $item_type_id);
					$oi->set_value("supplier_id", $supplier_id);
					$oi->set_value("item_code", $item_code);
					$oi->set_value("manufacturer_code", $manufacturer_code);
					$oi->set_value("coupons_ids", $item_coupons_ids);
					$oi->set_value("component_order", $component_order);
					$oi->set_value("component_name", $component_name);
					$oi->set_value("item_name", $item_name);
					$oi->set_value("buying_price", $buying_price);
					$oi->set_value("real_price", $real_price);
					$oi->set_value("discount_amount", $item_discount);
					$oi->set_value("price", $price);
					$oi->set_value("tax_free", $item_tax_free);
					$oi->set_value("tax_percent", $item_tax_percent);
					$oi->set_value("points_price", $points_price);
					$oi->set_value("reward_points", $reward_points);
					$oi->set_value("reward_credits", $reward_credits);

					$oi->set_value("merchant_commission", $merchant_commission);
					$oi->set_value("affiliate_commission", $affiliate_commission);
					$oi->set_value("packages_number", $packages_number);
					$oi->set_value("weight", $weight);
					$oi->set_value("width", $width);
					$oi->set_value("height", $height);
					$oi->set_value("length", $length);
					$oi->set_value("quantity", $quantity);
					//$oi->set_value("downloadable", $downloadable);
					// calculate shipping expecting date excluding sundays
					$handle_hours = $availability_time + $shipping_time + $processing_time;
					$shipping_expecting_date = get_expecting_date($handle_hours);
					$oi->set_value("shipping_expecting_date", va_time($shipping_expecting_date));
					$oi->set_value("is_shipping_free", $is_shipping_free);
					$oi->set_value("shipping_cost", $shipping_cost);

					// set subscription fields
					$oi->set_value("is_subscription", $is_subscription);
					$oi->set_value("is_account_subscription", $is_account_subscription);
					$oi->set_value("subscription_id", $subscription_id);
					$oi->set_value("subscription_period",   $subscription_period);
					$oi->set_value("subscription_interval", $subscription_interval);
					$oi->set_value("subscription_suspend",  $subscription_suspend);

					// set recurring payments
					$oi->set_value("is_recurring", $is_recurring);
					$oi->set_value("recurring_price", $recurring_price);
					$oi->set_value("recurring_payments_made", 0);
					if ($is_recurring) {
						$current_date = va_time();
						$current_ts = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY], $current_date[YEAR]);
						$recurring_next_payment = 0; $recurring_end_ts = 0;
						if (is_array($recurring_start_date)) {
							$recurring_start_ts = mktime (0, 0, 0, $recurring_start_date[MONTH], $recurring_start_date[DAY], $recurring_start_date[YEAR]);
							if ($recurring_start_ts > $current_ts) {
								$recurring_next_payment = $recurring_start_ts;
							}
						}
						if (!$recurring_next_payment) {
							if ($recurring_period == 1) {
								$recurring_next_payment = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY] + $recurring_interval, $current_date[YEAR]);
							} elseif ($recurring_period == 2) {
								$recurring_next_payment = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY] + ($recurring_interval * 7), $current_date[YEAR]);
							} elseif ($recurring_period == 3) {
								$recurring_next_payment = mktime (0, 0, 0, $current_date[MONTH] + $recurring_interval, $current_date[DAY], $current_date[YEAR]);
							} else {
								$recurring_next_payment = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY], $current_date[YEAR] + $recurring_interval);
							}
						}
						if (is_array($recurring_end_date)) {
							$recurring_end_ts = mktime (0, 0, 0, $recurring_end_date[MONTH], $recurring_end_date[DAY], $recurring_end_date[YEAR]);
							if ($recurring_next_payment > $recurring_end_ts) {
								$recurring_next_payment = 0;
							}
						}

						$oi->set_value("recurring_period", $recurring_period);
						$oi->set_value("recurring_interval", $recurring_interval);
						$oi->set_value("recurring_payments_total", $recurring_payments_total);
						$oi->set_value("recurring_end_date", $recurring_end_date);
						if ($recurring_next_payment) {
							$oi->set_value("recurring_next_payment", $recurring_next_payment);
							$oi->set_value("recurring_plan_payment", $recurring_next_payment);
						}
					}

					if ($db_type == "postgre") {
						$order_item_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "orders_items') ");
						$oi->change_property("order_item_id", USE_IN_INSERT, true);
						$oi->set_value("order_item_id", $order_item_id);
					}

					// add products and their options
					if ($oi->insert_record())
					{
						if ($db_type == "mysql") {
							$order_item_id = get_db_value(" SELECT LAST_INSERT_ID() ");
							$oi->set_value("order_item_id", $order_item_id);
						} elseif ($db_type == "access") {
							$order_item_id = get_db_value(" SELECT @@IDENTITY ");
							$oi->set_value("order_item_id", $order_item_id);
						} elseif ($db_type == "db2") {
							$order_item_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "orders_items FROM " . $table_prefix . "orders_items");
							$oi->set_value("order_item_id", $order_item_id);
						}

						// increment used product coupons by one
						if (strlen($item_coupons_ids)) {
							$sql  = " UPDATE " . $table_prefix . "coupons SET coupon_uses=coupon_uses+1 ";
							$sql .= " WHERE coupon_id IN (" . $db->tosql($item_coupons_ids, INTEGERS_LIST) . ") ";
							$db->query($sql);
						}

						// update components with order_item_id for their main product
						if (is_array($components) && sizeof($components) > 0) {
							for ($c = 0; $c < sizeof($components); $c++) {
								$cc_id = $components[$c];
								$cart_items[$cc_id]["top_order_item_id"] = $order_item_id;
							}
						}

						// add download link
						if (is_array($downloads) && sizeof($downloads) > 0) {
							$current_date = va_time();
							foreach ($downloads as $file_id => $download) {
								$download_path = $download["download_path"];
								if ($download_path) {
									$r_id->set_value("order_item_id", $order_item_id);
									$r_id->set_value("item_id", $item_id);
									$r_id->set_value("download_path", $download_path);
									$r_id->set_value("activated", 0);

									$download_period = $download["download_period"];
									$download_interval = $download["download_interval"];
									$download_limit = $download["download_limit"];
									$r_id->set_value("max_downloads", $max_downloads * $quantity);
									$r_id->set_value("download_added", va_time());
									if ($download_interval) {
										if ($download_period == 1) {
											$download_expiry = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY] + $download_interval, $current_date[YEAR]);
										} elseif ($download_period == 2) {
											$download_expiry = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY] + ($download_interval * 7), $current_date[YEAR]);
										} elseif ($download_period == 3) {
											$download_expiry = mktime (0, 0, 0, $current_date[MONTH] + $download_interval, $current_date[DAY], $current_date[YEAR]);
										} else {
											$download_expiry = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY], $current_date[YEAR] + $download_interval);
										}
										$r_id->set_value("download_expiry", $download_expiry);
									}
									if (strlen($download_limit)) {
										$r_id->set_value("download_limit", $download_limit * $quantity);
									} else {
										$r_id->set_value("download_limit", "");
									}
									$r_id->insert_record();
								}
							}
						}

						if ($generate_serial) {
							for ($sn = $quantity; $sn > 0; $sn--) {
								$serial_number = generate_serial($order_item_id, $sn, $cart_item, $generate_serial);
								if ($serial_number) {
									$ois->set_value("order_item_id", $order_item_id);
									$ois->set_value("item_id", $item_id);
									$ois->set_value("serial_number", $serial_number);
									$ois->set_value("activated", 0);
									$ois->set_value("activations_number", $activations_number);
									$ois->set_value("serial_added", va_time());
									if (strlen($serial_period)) {
										$serial_expiry =  va_timestamp() + (intval($serial_period) * 86400);
										$ois->set_value("serial_expiry", va_time($serial_expiry));
									}
									$ois->insert_record();
								}
							}
						}

						if ($is_gift_voucher) {
							for ($gf = $quantity; $gf > 0; $gf--) {
								$gift_voucher = generate_gift_voucher($order_id, $order_item_id, $item_name, $price_incl_tax);
							}
						}
				
						// add properties
						if (is_array($properties_info) && sizeof($properties_info) > 0) {
							$oip->set_value("order_item_id", $order_item_id);
							for ($pi = 0; $pi < sizeof($properties_info); $pi++) {
								list($property_id, $control_type, $property_name, $property_value, $pr_add_price, $pr_add_weight, $pr_values, $property_order) = $properties_info[$pi];
								if ($control_type == "TEXTBOXLIST") {
									// for text boxes list save all data in property value 
									$property_values_ids = ""; $property_values_text = "";
									for ($pv = 0; $pv < sizeof($pr_values); $pv++) {
										list($item_property_id, $pr_value, $pr_value_text, $pr_use_stock, $pr_hide_out_stock, $pr_stock_level) = $pr_values[$pv];
										if ($property_values_ids) { 
											$property_values_ids .= ","; 
										}
										$property_values_text .= "<br>" . $pr_value . ": " . $pr_value_text;
										$property_values_ids .= $item_property_id;
									}
									$property_value = $property_values_text;
								} else {
									// get all property values ids
									$property_values_ids = "";
									for ($pv = 0; $pv < sizeof($pr_values); $pv++) {
										list($item_property_id, $pr_value, $pr_value_text, $pr_use_stock, $pr_hide_out_stock, $pr_stock_level) = $pr_values[$pv];
										if ($property_values_ids) { $property_values_ids .= ","; }
										$property_values_ids .= $item_property_id;
									}
							  
								}
								$oip->set_value("property_id", $property_id);
								$oip->set_value("property_order", $property_order);
								$oip->set_value("property_name", $property_name);
								$oip->set_value("property_value", $property_value);
								$oip->set_value("property_values_ids", $property_values_ids);
								$oip->set_value("additional_price", $pr_add_price);
								$oip->set_value("additional_weight", $pr_add_weight);
								$oip->insert_record();
							}
						}
					} // end of adding products
				}

				// update order status
				update_order_status($order_id, $new_order_status, true, "", $status_error);

				$order_admin_email = $order_info["admin_email"] ? $order_info["admin_email"] : $settings["admin_email"];
				$admin_notification = get_setting_value($order_info, "admin_notification", 0);
				$user_notification  = get_setting_value($order_info, "user_notification", 0);
				$admin_sms = get_setting_value($order_info, "admin_sms_notification", 0);
				$user_sms  = get_setting_value($order_info, "user_sms_notification", 0);

				if ($admin_notification || $user_notification || $admin_sms || $user_sms)
				{
					$r->set_parameters();
					$t->set_var("goods_total", currency_format($goods_total));
					$t->set_var("goods_tax_total", currency_format($goods_tax_total));
					$t->set_var("total_discount", " -" . currency_format($total_discount));
					$t->set_var("shipping_cost", currency_format($shipping_cost));
					$t->set_var("shipping_points_amount", number_format($shipping_points_amount, $points_decimals));

					$t->set_var("tax_percent", number_format($tax_percent_sum, 3) . "%");
					$t->set_var("order_total", currency_format($order_total));
					$t->set_var("total_points_amount", number_format($total_points_amount, $points_decimals));
					$t->set_var("total_reward_points", number_format($total_reward_points, $points_decimals));
					$t->set_var("total_reward_credits", currency_format($total_reward_credits));

					$t->set_var("order_placed_date", $order_placed_date_string);

					$admin_message = get_setting_value($order_info, "admin_message", "");
					$admin_mail_type = get_setting_value($order_info, "admin_message_type");
					$user_message = get_setting_value($order_info, "user_message", "");
					$user_mail_type = get_setting_value($order_info, "user_message_type");

					// parse basket template
					if (($admin_notification && $admin_mail_type && strpos($admin_message, "{basket}") !== false) 
						|| ($user_notification && $user_mail_type && strpos($user_message, "{basket}") !== false))
					{
						$t->set_file("basket_html", "email_basket.html");
						show_order_items($order_id, true, "");
						$t->parse("basket_html", false);
					}
					if (($admin_notification && !$admin_mail_type && strpos($admin_message, "{basket}") !== false) 
						|| ($user_notification && !$user_mail_type && strpos($user_message, "{basket}") !== false))
					{
						$t->set_file("basket_text", "email_basket.txt");
						show_order_items($order_id, true, "");
						$t->parse("basket_text", false);
					}
					// preparing downloadable data
					// get download links
					$links = get_order_links($order_id);
					// get serial numbers
					$order_serials = get_serial_numbers($order_id);
					// get gift vouchers
					$order_vouchers = get_gift_vouchers($order_id);
				}

				if ($admin_notification)
				{
					$admin_subject = get_setting_value($order_info, "admin_subject", "");
					$admin_subject = get_translation($admin_subject);
					$admin_message = get_currency_message(get_translation($admin_message), $currency);

					$t->set_block("admin_subject", $admin_subject);
					$t->set_block("admin_message", $admin_message);

					$mail_to = get_setting_value($order_info, "admin_email", $settings["admin_email"]);
					$mail_to = str_replace(";", ",", $mail_to);
					$email_headers = array();
					$email_headers["from"] = get_setting_value($order_info, "admin_mail_from", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($order_info, "cc_emails");
					$email_headers["bcc"] = get_setting_value($order_info, "admin_mail_bcc");
					$email_headers["reply_to"] = get_setting_value($order_info, "admin_mail_reply_to");
					$email_headers["return_path"] = get_setting_value($order_info, "admin_mail_return_path");
					$email_headers["mail_type"] = $admin_mail_type;

					if (!$admin_mail_type) {
						$t->set_var("basket", $t->get_var("basket_text"));
					} else {
						$t->set_var("basket", $t->get_var("basket_html"));
					}
					// set download links
					if ($admin_mail_type) {
						$t->set_var("links", $links["html"]);
					} else {
						$t->set_var("links", $links["text"]);
					}
					// set serial numbers
					if ($admin_mail_type) {
						$t->set_var("serials", $order_serials["html"]);
						$t->set_var("serial_numbers", $order_serials["html"]);
					} else {
						$t->set_var("serials", $order_serials["text"]);
						$t->set_var("serial_numbers", $order_serials["text"]);
					}
					// set serial numbers
					if ($admin_mail_type) {
						$t->set_var("vouchers", $order_vouchers["html"]);
						$t->set_var("gift_vouchers", $order_vouchers["html"]);
					} else {
						$t->set_var("vouchers", $order_vouchers["text"]);
						$t->set_var("gift_vouchers", $order_vouchers["text"]);
					}

					$t->parse("admin_subject", false);
					$t->parse("admin_message", false);

					$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
					va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers);
				}
				if ($user_notification)
				{
					$user_subject = get_setting_value($order_info, "user_subject", "");
					$user_subject = get_translation($user_subject);
					$user_message = get_currency_message(get_translation($user_message), $currency);

					$t->set_block("user_subject", $user_subject);
					$t->set_block("user_message", $user_message);

					$email_headers = array();
					$email_headers["from"] = get_setting_value($order_info, "user_mail_from", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($order_info, "user_mail_cc");
					$email_headers["bcc"] = get_setting_value($order_info, "user_mail_bcc");
					$email_headers["reply_to"] = get_setting_value($order_info, "user_mail_reply_to");
					$email_headers["return_path"] = get_setting_value($order_info, "user_mail_return_path");
					$email_headers["mail_type"] = $user_mail_type;

					if (!$user_mail_type) {
						$t->set_var("basket", $t->get_var("basket_text"));
					} else {
						$t->set_var("basket", $t->get_var("basket_html"));
					}
					// set download links
					if ($user_mail_type) {
						$t->set_var("links", $links["html"]);
					} else {
						$t->set_var("links", $links["text"]);
					}
					// set serial numbers
					if ($user_mail_type) {
						$t->set_var("serials", $order_serials["html"]);
						$t->set_var("serial_numbers", $order_serials["html"]);
					} else {
						$t->set_var("serials", $order_serials["text"]);
						$t->set_var("serial_numbers", $order_serials["text"]);
					}
					// set serial numbers
					if ($user_mail_type) {
						$t->set_var("vouchers", $order_vouchers["html"]);
						$t->set_var("gift_vouchers", $order_vouchers["html"]);
					} else {
						$t->set_var("vouchers", $order_vouchers["text"]);
						$t->set_var("gift_vouchers", $order_vouchers["text"]);
					}

					$t->parse("user_subject", false);
					$t->parse("user_message", false);

					$user_email = strlen($r->get_value("email")) ? $r->get_value("email") : $r->get_value("delivery_email");
					$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
					va_mail($user_email, $t->get_var("user_subject"), $user_message, $email_headers);
				}
				if ($admin_sms)
				{
					$admin_sms_recipient  = get_setting_value($order_info, "admin_sms_recipient", "");
					$admin_sms_originator = get_setting_value($order_info, "admin_sms_originator", "");
					$admin_sms_message    = get_currency_message(get_translation(get_setting_value($order_info, "admin_sms_message", "")), $currency);

					$t->set_block("admin_sms_recipient",  $admin_sms_recipient);
					$t->set_block("admin_sms_originator", $admin_sms_originator);
					$t->set_block("admin_sms_message",    $admin_sms_message);

					$t->set_var("basket", $items_text);
					$t->set_var("items", $items_text);
					// set download links
					$t->set_var("links",    $links["text"]);
					// set serial numbers
					$t->set_var("serials", $order_serials["text"]);
					$t->set_var("serial_numbers", $order_serials["text"]);
					// set serial numbers
					$t->set_var("vouchers", $order_vouchers["text"]);
					$t->set_var("gift_vouchers", $order_vouchers["text"]);

					$t->parse("admin_sms_recipient", false);
					$t->parse("admin_sms_originator", false);
					$t->parse("admin_sms_message", false);

					sms_send($t->get_var("admin_sms_recipient"), $t->get_var("admin_sms_message"), $t->get_var("admin_sms_originator"));
				}

				if ($user_sms)
				{
					$user_sms_recipient  = get_setting_value($order_info, "user_sms_recipient", $r->get_value("cell_phone"));
					$user_sms_originator = get_setting_value($order_info, "user_sms_originator", "");
					$user_sms_message    = get_currency_message(get_translation(get_setting_value($order_info, "user_sms_message", "")), $currency);

					$t->set_block("user_sms_recipient",  $user_sms_recipient);
					$t->set_block("user_sms_originator", $user_sms_originator);
					$t->set_block("user_sms_message",    $user_sms_message);

					$t->set_var("basket", $items_text);
					$t->set_var("items", $items_text);
					// set download links
					$t->set_var("links",    $links["text"]);
					// set serial numbers
					$t->set_var("serials", $order_serials["text"]);
					$t->set_var("serial_numbers", $order_serials["text"]);
					// set serial numbers
					$t->set_var("vouchers", $order_vouchers["text"]);
					$t->set_var("gift_vouchers", $order_vouchers["text"]);

					$t->parse("user_sms_recipient", false);
					$t->parse("user_sms_originator", false);
					$t->parse("user_sms_message", false);

					if (sms_send_allowed($t->get_var("user_sms_recipient"))) {
						sms_send($t->get_var("user_sms_recipient"), $t->get_var("user_sms_message"), $t->get_var("user_sms_originator"));
					}
				}
			}

			$vc = md5($order_id . $order_placed_date[3].$order_placed_date[4].$order_placed_date[5]);
			set_session("session_order_id", $order_id);
			set_session("session_user_order_id", $order_id);
			set_session("session_vc", $vc);
			set_session("session_payment_id", $payment_id);

			if ($order_total == 0) {
				$payment_url  = get_custom_friendly_url("order_confirmation.php");
				$payment_url .= "?order_id=" . urlencode($order_id) . "&vc=" . urlencode($vc);
			} else {
				if (!$payment_url) { $payment_url = get_custom_friendly_url("credit_card_info.php"); }

				if ($payment_advanced) {
					$payment_url .= "?order_id=" . urlencode($order_id) . "&vc=" . urlencode($vc) . "&payment_id=" . urlencode($payment_id);
				} elseif ($payment_method == "POST") {
					$payment_url  = get_custom_friendly_url("payment.php");
				} else {
					get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $form_params, $pass_data, $variables);

					if ($form_params) {
						$payment_url .= strpos($payment_url,"?") ? "&" : "?";
						$payment_url .= $form_params;
					}
				}
			}

			if (!$user_id) { // set cookies with user info for non-registered users
				$cookie_order_info = "";
				for ($i = 0; $i < sizeof($parameters); $i++) {
					$cookie_order_info .= $parameters[$i] . "=" . $r->get_value($parameters[$i]) . "|";
					$cookie_order_info .= "delivery_" . $parameters[$i] . "=" . $r->get_value("delivery_" . $parameters[$i]) . "|";
				}
				setcookie("cookie_order_info", $cookie_order_info, va_timestamp() + 3600 * 24 * 366);
			}
			if ($payment_url == "credit_card_info.php" || $payment_url == get_custom_friendly_url("credit_card_info.php")) {
				$payment_url .= "?order_id=" . urlencode($order_id) . "&vc=" . urlencode($vc);
			}

			if ($secure_payments && !preg_match("/^http\:\/\//", $payment_url) && !preg_match("/^https\:\/\//", $payment_url)) {
				$payment_url = $secure_url . $payment_url;
			}

			header("Location: " . $payment_url);
			exit;
		}
	} elseif ($user_id) {
		// get user details
		$sql = " SELECT * FROM " . $table_prefix . "users WHERE user_id=" . $db->tosql($user_id, INTEGER);
		$db->query($sql);
		if ($db->next_record())
		{
			$user_login = $db->f("login");
			for ($i = 0; $i < sizeof($parameters); $i++) {
				$r->set_value($parameters[$i], $db->f($parameters[$i]));
				$r->set_value("delivery_" . $parameters[$i], $db->f("delivery_" . $parameters[$i]));
			}
			if ($r->is_empty("email") && preg_match(EMAIL_REGEXP, $user_login)) { 
				$r->set_value("email", $user_login);
			}
		}

	} else { // set default values from cookies
		$cookie_order_info = trim(get_cookie("cookie_order_info"));
		if (strlen($cookie_order_info)) {
			$cookie_pairs = explode("|", $cookie_order_info);
			for ($i = 0; $i < sizeof($cookie_pairs); $i++) {
				$cookie_line = trim($cookie_pairs[$i]);
				if (strlen($cookie_line)) {
					$cookie_values = explode("=", $cookie_line, 2);
					if (isset($r->parameters[$cookie_values[0]])) {
						$r->set_value($cookie_values[0], $cookie_values[1]);
					}
				}
			}
		}
		if ($r->is_empty("country_id") && $r->is_empty("delivery_country_id")) {
			$r->set_value("country_id", $settings["country_id"]);
			$r->set_value("delivery_country_id", $settings["country_id"]);
		}
	}

	if (!strlen($operation)) {
		// check subscribe option
		if ($subscribe_block) {
			$subscribe_email = $r->get_value("email");
			if (!$subscribe_email && $r->get_value("delivery_email")) {
				$subscribe_email = $r->get_value("delivery_email");
			}
			if ($subscribe_email) {
				$sql  = " SELECT email_id FROM " . $table_prefix . "newsletters_users ";
				$sql .= " WHERE email=" . $db->tosql($subscribe_email, TEXT);
				$db->query($sql);
				if ($db->next_record()) {
					$r->set_value("subscribe", 1);
				}
			}
		}
	}

	if (!$basket_parsed) {
		$t->parse("basket", false);
	}

	$r->set_parameters();

	if ($sc_errors) {
		$t->set_var("errors_list", $sc_errors);
		$t->parse("sc_errors", false);
	}

	if ($personal_number > 0) {
		$t->parse("personal", false);
	}

	if ($delivery_number > 0) {
		$t->parse("delivery", false);
	}

	$intro_text = trim($order_info["intro_text"]);
	$intro_text = get_translation($intro_text);
	$intro_text = get_currency_message($intro_text, $currency);

	if ($intro_text) {
		$t->set_var("intro_text", $intro_text);
		$t->parse("intro_block", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>