<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  order_items.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
	function strnpos( $haystack, $needle, $nth, $offset = 0 )
	{
	   if( 1 > $nth || 0 === strlen( $needle ) )
	   {
		   return false;
	   }
	
	   //  $offset is incremented in the call to strpos, so make sure that the first call starts at the right position by initially decrementing $offset.
	   --$offset;
	   do
	   {
		  $offset = strpos( $haystack, $needle, ++ $offset );
	   } while( --$nth  && false !== $offset );
	
	   return $offset;
	}

	function show_order_items($order_id, $parse_template = true, $page_type = "")
	{
	 	global $t, $db, $table_prefix, $settings, $date_show_format;
		global $items_text, $order_items, $cart_items, $total_items, $currency;
		global $cart_properties, $personal_properties, $delivery_properties, $payment_properties;
		global $is_admin_path, $root_folder_path ;
		
		$dbd = new VA_SQL();
		$dbd->DBType       = $db->DBType;
		$dbd->DBDatabase   = $db->DBDatabase;
		$dbd->DBUser       = $db->DBUser;
		$dbd->DBPassword   = $db->DBPassword;
		$dbd->DBHost       = $db->DBHost;
		$dbd->DBPort       = $db->DBPort;
		$dbd->DBPersistent = $db->DBPersistent;

		$eol = get_eol();
		// columns settings
		if ($page_type == "admin_invoice_html" || $page_type == "user_invoice_html") {
			$item_name_column = get_setting_value($settings, "invoice_item_name", 1);
			$item_price_column = get_setting_value($settings, "invoice_item_price", 1);
			$item_tax_percent_column = get_setting_value($settings, "invoice_item_tax_percent", 0);
			$item_tax_column = get_setting_value($settings, "invoice_item_tax", 0);
			$item_price_incl_tax_column = get_setting_value($settings, "invoice_item_price_incl_tax", 0);
			$item_quantity_column = get_setting_value($settings, "invoice_item_quantity", 1);
			$item_price_total_column = get_setting_value($settings, "invoice_item_price_total", 1);
			$item_tax_total_column = get_setting_value($settings, "invoice_item_tax_total", 1);
			$item_price_incl_tax_total_column = get_setting_value($settings, "invoice_item_price_incl_tax_total", 1);
			$item_image_column = get_setting_value($settings, "invoice_item_image", 0);
		} else {
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
		}

		// image settings
		if ($item_image_column) {
			$product_no_image = get_setting_value($settings, "product_no_image", "");
			$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");		
			product_image_fields($item_image_column, $image_type_name, $image_field, $image_alt_field, $watermark, $product_no_image);			
		}
		$global_tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
		$global_tax_round = get_setting_value($settings, "tax_round", 1);
		$tax_prices = get_setting_value($settings, "tax_prices", 0);
		$tax_note = get_translation(get_setting_value($settings, "tax_note", ""));
		$tax_note_excl = get_translation(get_setting_value($settings, "tax_note_excl", ""));
		$points_decimals = get_setting_value($settings, "points_decimals", 0);

		// option price options
		$option_positive_price_right = get_setting_value($settings, "option_positive_price_right", ""); 
		$option_positive_price_left = get_setting_value($settings, "option_positive_price_left", ""); 
		$option_negative_price_right = get_setting_value($settings, "option_negative_price_right", ""); 
		$option_negative_price_left = get_setting_value($settings, "option_negative_price_left", "");

		$orders_currency = get_setting_value($settings, "orders_currency", 0);
		if ($page_type == "admin_invoice_html" || $page_type == "user_invoice_html") {
			$show_item_code = get_setting_value($settings, "item_code_invoice", 0);
			$show_manufacturer_code = get_setting_value($settings, "manufacturer_code_invoice", 0);
			$show_points_price = get_setting_value($settings, "points_price_invoice", 0);
			$show_reward_points = get_setting_value($settings, "reward_points_invoice", 0);
			$show_reward_credits = get_setting_value($settings, "reward_credits_invoice", 0);
		} elseif ($page_type == "order_info" || $page_type == "admin_order"  || $page_type == "cc_info" || $page_type == "order_confirmation") {
			$show_item_code = get_setting_value($settings, "item_code_checkout", 0);
			$show_manufacturer_code = get_setting_value($settings, "manufacturer_code_checkout", 0);
			$show_points_price = get_setting_value($settings, "points_price_checkout", 0);
			$show_reward_points = get_setting_value($settings, "reward_points_checkout", 0);
			$show_reward_credits = get_setting_value($settings, "reward_credits_checkout", 0);
		} else {
			$show_item_code = get_setting_value($settings, "item_code_checkout", 0);
			$show_manufacturer_code = get_setting_value($settings, "manufacturer_code_checkout", 0);
			$show_points_price = get_setting_value($settings, "points_price_checkout", 0);
			$show_reward_points = get_setting_value($settings, "reward_points_checkout", 0);
			$show_reward_credits = get_setting_value($settings, "reward_credits_checkout", 0);
		}
		$session_user_id = get_session("session_user_id");
		if ($page_type == "user_invoice_html" || $page_type == "order_info" || $page_type == "cc_info" || $page_type == "order_confirmation") {
			$reward_credits_users = get_setting_value($settings, "reward_credits_users", 0);
		} else {
			$reward_credits_users = 0;
		}

		// get order tax rates
		$tax_available = false; $tax_percent_sum = 0; $tax_names = "";
		$order_tax_rates = order_tax_rates($order_id);
		if (sizeof($order_tax_rates) > 0) {
			$tax_available = true;
			foreach ($order_tax_rates as $tax_id => $tax_info) {
				$tax_percent_sum += $tax_info["tax_percent"];
				if ($tax_names) { $tax_names .= " & "; }
				$tax_names .= get_translation($tax_info["tax_name"]);
			}
		}

		// get information about order
		$sql  = " SELECT o.user_type_id, o.site_id, o.coupons_ids, o.vouchers_ids, o.total_discount, o.total_discount_tax, o.shipping_type_desc, ";
		$sql .= " o.shipping_cost, o.shipping_taxable, o.tax_name, o.tax_percent, o.vouchers_amount, ";
		$sql .= " o.processing_fee, o.shipping_type_id, o.country_id, o.state_id, o.delivery_state_id, ";
		$sql .= " o.tax_prices_type, o.weight_total, o.order_placed_date, ";
		$sql .= " o.currency_code, o.currency_rate, c.symbol_right, c.symbol_left, c.decimals_number, c.decimal_point, c.thousands_separator, ";
		$sql .= " o.shipping_points_amount, o.total_points_amount, o.credit_amount, o.total_reward_credits, o.total_reward_points ";
		$sql .= " FROM (" . $table_prefix . "orders o ";
		$sql .= " LEFT JOIN " . $table_prefix . "currencies c ON o.currency_code=c.currency_code) ";
		$sql .= " WHERE o.order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		$db->next_record();
		// get order values

		$order_user_type_id = $db->f("user_type_id");
		$order_site_id = $db->f("site_id");
		$tax_available = sizeof($order_tax_rates);
		$tax_prices_type = $db->f("tax_prices_type");
		if (!strlen($tax_prices_type)) {
			$tax_prices_type = $global_tax_prices_type;
		}
		$tax_round = $db->f("tax_round_type");
		if (!strlen($tax_round)) {
			$tax_round = $global_tax_round;
		}

		$order_coupons_ids = $db->f("coupons_ids");
		$vouchers_ids = $db->f("vouchers_ids");
		$vouchers_amount = $db->f("vouchers_amount");

		$total_discount = $db->f("total_discount");
		$total_discount_tax = $db->f("total_discount_tax");
		$shipping_type_id = $db->f("shipping_type_id");
		$shipping_type_desc = $db->f("shipping_type_desc");
		$shipping_cost = $db->f("shipping_cost");
		$shipping_taxable = $db->f("shipping_taxable");
		// get taxes for selected shipping and add it to total values 
		$shipping_tax_free = ($shipping_taxable) ? 0 : 1;
		$shipping_tax_values = get_tax_amount($order_tax_rates, "shipping", $shipping_cost, $shipping_tax_free, $shipping_tax_percent, "", 2, $tax_prices_type, $tax_round);
		$shipping_tax_total = add_tax_values($order_tax_rates, $shipping_tax_values, "shipping", $tax_round);

		//$shipping_taxable_value = ($shipping_taxable == 1) ? $shipping_cost : 0;
		if ($tax_prices_type == 1) {
			$shipping_cost_excl_tax = $shipping_cost - $shipping_tax_total;
			$shipping_cost_incl_tax = $shipping_cost;
		} else {
			$shipping_cost_excl_tax = $shipping_cost;
			$shipping_cost_incl_tax = $shipping_cost + $shipping_tax_total;
		}

		$processing_fee = $db->f("processing_fee");
		$credit_amount = $db->f("credit_amount");
		$total_reward_credits = $db->f("total_reward_credits");
		$total_reward_points = $db->f("total_reward_points");

		$country_id = $db->f("delivery_country_id");
		$state_id = $db->f("delivery_state_id");
		$weight_total = $db->f("weight_total");
		$order_placed_date = $db->f("order_placed_date", DATETIME);
		$order_date = va_date($date_show_format, $order_placed_date);
		if (!$country_id) {
			$country_id = $db->f("country_id");
		}
		if (!$state_id) {
			$state_id = $db->f("state_id");
		}
		// get order currency
		$order_currency = array();
		$order_currency_code = $db->f("currency_code");
		$order_currency_rate= $db->f("currency_rate");
		$order_currency["code"] = $db->f("currency_code");
		$order_currency["rate"] = $db->f("currency_rate");
		$order_currency["left"] = $db->f("symbol_left");
		$order_currency["right"] = $db->f("symbol_right");
		$order_currency["decimals"] = $db->f("decimals_number");
		$order_currency["point"] = $db->f("decimal_point");
		$order_currency["separator"] = $db->f("thousands_separator");

		if ($orders_currency != 1) {
			$order_currency["left"] = $currency["left"];
			$order_currency["right"] = $currency["right"];
			$order_currency["decimals"] = $currency["decimals"];
			$order_currency["point"] = $currency["point"];
			$order_currency["separator"] = $currency["separator"];
			if (strtolower($currency["code"]) != strtolower($order_currency_code)) {
				$order_currency["rate"] = $currency["rate"];
			}
		}
		$shipping_points_amount = $db->f("shipping_points_amount");
		$total_points_amount = $db->f("total_points_amount");

		if ($parse_template) {

			$t->set_var("items", "");
			$t->set_var("cart_properties", "");
			$t->set_var("personal_properties", "");
			$t->set_var("delivery_properties", "");
			$t->set_var("order_coupons", "");
			$t->set_var("discount", "");
			$t->set_var("shipping_type", "");
			$t->set_var("taxes", "");
			$t->set_var("credit_amount_block", "");
			$t->set_var("fee", "");
			$t->set_var("total_points_block", "");

			if ($order_currency_rate != 1) {
				$t->set_var("order_currency_code", $order_currency_code);
				$t->set_var("order_currency_rate", $order_currency_rate);
				$t->sparse("order_currency", false);
			}

			$t->set_var("tax_name", $tax_names); 
			$t->set_var("tax_note", $tax_note);
			$t->set_var("tax_note_excl", $tax_note_excl);
			$t->set_var("points_msg", strtolower(POINTS_MSG));
			$goods_colspan = 0; $total_columns = 0;
			if ($item_image_column) {
				$goods_colspan++;
				$total_columns++;
				$t->sparse("item_image_header", false);
			}
			if ($item_name_column) {
				$goods_colspan++;
				$total_columns++;
				$t->sparse("item_name_header", false);
			}
			if ($item_price_column || ($item_price_incl_tax_column && !$tax_available)) {
				$item_price_column = true;
				$goods_colspan++;
				$total_columns++;
				$t->sparse("item_price_header", false);
			}
			if ($item_tax_percent_column && $tax_available) {
				$goods_colspan++;
				$total_columns++;
				$t->sparse("item_tax_percent_header", false);
			} else {
				$item_tax_percent_column = false;
			}
			if ($item_tax_column && $tax_available) {
				$goods_colspan++;
				$total_columns++;
				$t->sparse("item_tax_header", false);
			} else {
				$item_tax_column = false;
			}
			if ($item_price_incl_tax_column && $tax_available) {
				$goods_colspan++;
				$total_columns++;
				$t->sparse("item_price_incl_tax_header", false);
			} else {
				$item_price_incl_tax_column = false;
			}
			if ($item_quantity_column) {
				$goods_colspan++;
				$total_columns++;
				$t->sparse("item_quantity_header", false);
			}
			if ($item_price_total_column || ($item_price_incl_tax_total_column && !$tax_available)) {
				$item_price_total_column = true;
				$total_columns++;
				$t->sparse("item_price_total_header", false);
			}
			if ($item_tax_total_column && $tax_available) {
				$total_columns++;
				$t->sparse("item_tax_total_header", false);
			} else {
				$item_tax_total_column = false;
			}
			if ($item_price_incl_tax_total_column && $tax_available) {
				$total_columns++;
				$t->sparse("item_price_incl_tax_total_header", false);
			} else {
				$item_price_incl_tax_total_column = false;
			}
			$sc_colspan = $total_columns - 1;
			$t->set_var("goods_colspan", $goods_colspan);
			$t->set_var("sc_colspan", $sc_colspan);
			$t->set_var("total_columns", $total_columns);
		}

		// get order profile settings
		$order_info = array();
		$sql  = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= "WHERE setting_type='order_info'";
		if ($order_site_id) {
			$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($order_site_id, INTEGER, true, false) . ")";
			$sql .= " ORDER BY site_id ASC ";
		} else {
			$sql .= " AND site_id=1 ";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$order_info[$db->f("setting_name")] = $db->f("setting_value");
		}
		$subcomponents_show_type = get_setting_value($order_info, "subcomponents_show_type", 0);

		$order_items = array();
		$order_statuses = get_db_values("SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE is_active=1 ORDER BY status_order, status_id ", "");
		$items_text = ""; $order_items_ids = ""; $cart_items = array(); $items_taxes = array();
		$goods_total = 0; $goods_tax_total = 0; $goods_total_excl_tax = 0; $goods_total_incl_tax = 0;
		$total_quantity = 0; $total_items = 0;
		$sql  = " SELECT oi.order_item_id,oi.top_order_item_id,oi.item_id,oi.item_user_id,oi.item_type_id,";
		$sql .= " oi.item_status,oi.item_code,oi.manufacturer_code, oi.component_name, oi.item_name, ";
		$sql .= " oi.is_recurring, oi.recurring_last_payment, oi.recurring_next_payment, oi.downloadable, ";
		$sql .= " oi.price,oi.tax_free,oi.tax_percent,oi.discount_amount,oi.real_price, oi.weight, ";
		$sql .= " oi.buying_price,oi.points_price,oi.reward_points,oi.reward_credits,oi.quantity,oi.coupons_ids, ";
		$sql .= " oi.is_subscription, oi.subscription_id, oi.subscription_start_date, oi.subscription_expiry_date, ";
		$sql .= " mu.email, mu.name, mu.first_name, mu.last_name, mu.cell_phone, ";
		$sql .= " sp.supplier_id, sp.supplier_email, sp.supplier_name, ";
		$sql .= " sp.short_description AS supplier_short_desc, sp.full_description AS supplier_full_desc ";
		$sql .= " FROM ((" . $table_prefix . "orders_items oi ";
		$sql .= " LEFT JOIN " . $table_prefix . "users mu ON oi.item_user_id=mu.user_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "suppliers sp ON oi.supplier_id=sp.supplier_id) ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		// for merchant - show only his products
		if ($page_type == "user_merchant_order") {
			$sql .= " AND oi.item_user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
		}
		$db->query($sql);
		while ($db->next_record()) {
			$order_item_id = $db->f("order_item_id");
			$top_order_item_id = $db->f("top_order_item_id");

			$item_status = $db->f("item_status");
			$item_type_id = $db->f("item_type_id");

			$selection_name = get_translation($db->f("component_name"));
			$item_name = get_translation($db->f("item_name"));

			$item_code = $db->f("item_code");
			$manufacturer_code = $db->f("manufacturer_code");
			$is_recurring = $db->f("is_recurring");
			$recurring_last_payment = $db->f("recurring_last_payment", DATETIME);
			$recurring_next_payment = $db->f("recurring_next_payment", DATETIME);

			$price = $db->f("price");
			$quantity = $db->f("quantity");
			$item_tax_free = $db->f("tax_free");

			$item_total = $price * $quantity;

			// new
			$item_tax = get_tax_amount($order_tax_rates, $item_type_id, $price, $item_tax_free, $item_tax_percent, "", 1, $tax_prices_type, $tax_round);
			$item_tax_values = get_tax_amount($order_tax_rates, $item_type_id, $item_total, $item_tax_free, $item_tax_percent, "", 2, $tax_prices_type, $tax_round);
			$item_tax_total = add_tax_values($order_tax_rates, $item_tax_values, "products", $tax_round);

			if ($tax_prices_type == 1) {
				$price_excl_tax = $price - $item_tax;
				$price_incl_tax = $price;
				$price_excl_tax_total = $item_total - $item_tax_total;
				$price_incl_tax_total = $item_total;
			} else {
				$price_excl_tax = $price;
				$price_incl_tax = $price + $item_tax;
				$price_excl_tax_total = $item_total;
				$price_incl_tax_total = $item_total + $item_tax_total;
			}
			$order_items[$order_item_id] = array(
				"top_order_item_id" => $top_order_item_id,
				"item_id" => $db->f("item_id"), "item_type_id" => $db->f("item_type_id"),
				"item_status" => $item_status, "item_code" => $item_code, "manufacturer_code" => $manufacturer_code,
				"selection_name" => $selection_name, "item_name" => $item_name,
				"is_recurring" => $is_recurring, 
				"recurring_last_payment" => $recurring_last_payment, "recurring_next_payment" => $recurring_next_payment,
				"price" => $price, "quantity" => $quantity, "item_total" => $item_total,
				"price_excl_tax" => $price_excl_tax, "price_incl_tax" => $price_incl_tax,
				"price_excl_tax_total" => $price_excl_tax_total, "price_incl_tax_total" => $price_incl_tax_total,
				"item_tax" => $item_tax, "item_tax_total" => $item_tax_total,
				"tax_free" => $item_tax_free, "tax_percent" => $item_tax_percent,
				"weight" => $db->f("weight"),
				"downloadable" => $db->f("downloadable"),
				"discount_amount" => $db->f("discount_amount"),
				"buying_price" => $db->f("buying_price"),
				"real_price" => $db->f("real_price"),
				"points_price" => $db->f("points_price"),
				"reward_points" => $db->f("reward_points"),
				"reward_credits" => $db->f("reward_credits"),
				"coupons_ids" => $db->f("coupons_ids"),
				"merchant_id" => $db->f("item_user_id"), "merchant_email" => $db->f("email"),
				"merchant_name" => $db->f("name"), "merchant_first_name" => $db->f("first_name"),
				"merchant_last_name" => $db->f("last_name"), "merchant_cell_phone" => $db->f("cell_phone"),
				"supplier_id" => $db->f("supplier_id"), "supplier_email" => $db->f("supplier_email"),
				"supplier_name" => $db->f("supplier_name"), "supplier_short_desc" => $db->f("supplier_short_desc"),
				"supplier_full_desc" => $db->f("supplier_full_desc"), "supplier_cell_phone" => "",
				"is_subscription" => $db->f("is_subscription"), "subscription_id" => $db->f("subscription_id"),
				"subscription_start_date" => $db->f("subscription_start_date", DATETIME), 
				"subscription_expiry_date" => $db->f("subscription_expiry_date", DATETIME),
				"components" => array(),
			);
			if ($top_order_item_id) {
				$order_items[$top_order_item_id]["components"][] = $order_item_id;
			}
		}

		// get additional data from items table
		$items_fields = array(
			"stock_level", "short_description", "full_description", 
			"tiny_image", "small_image", "big_image", "super_image",
		);
		foreach ($order_items as $order_item_id => $item)
		{
			$item_id = $item["item_id"];
			if ($item_id) {
				$sql  = " SELECT stock_level, short_description, full_description, ";
				$sql .= " tiny_image, small_image, big_image, super_image ";
				$sql .= " FROM " . $table_prefix . "items ";
				$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					for($f = 0; $f < sizeof($items_fields); $f++) {
						$field_name = $items_fields[$f];
						$order_items[$order_item_id][$field_name] = $db->f($field_name);
					}
				}			
			} else {
				for($f = 0; $f < sizeof($items_fields); $f++) {
					$field_name = $items_fields[$f];
					$order_items[$order_item_id][$field_name] = "";
				}
			}
		}


		foreach ($order_items as $order_item_id => $item)
		{
			$total_items++;
			$top_order_item_id = $item["top_order_item_id"];
			if ($subcomponents_show_type == 1 && $top_order_item_id && isset($order_items[$top_order_item_id])) {
				// component already shown with parent product
				continue;
			}
			$item_id = $item["item_id"];
			$item_status = $item["item_status"];
			$real_price = $item["real_price"];
			$coupons_ids = $item["coupons_ids"];

			$price = $item["price"];
			$item_tax_free = $item["tax_free"];
			$item_tax_percent = $item["tax_percent"];
			$discount_amount = $item["discount_amount"];
			$real_price = $item["real_price"];
			$quantity = $item["quantity"];
			$weight = $item["weight"];
			$buying_price = $item["buying_price"];
			$points_price = $item["points_price"];
			$reward_points = $item["reward_points"];
			$reward_credits = $item["reward_credits"];
			$downloadable = $item["downloadable"];
			$item_type_id = $item["item_type_id"];
			// obtained variables below from items table
			$stock_level = ""; $short_description = ""; $full_description = "";

			$item_total = $item["item_total"];
			$item_tax = $item["item_tax"];
			$item_tax_total = $item["item_tax_total"];
			$price_excl_tax = $item["price_excl_tax"];
			$price_incl_tax = $item["price_incl_tax"];
			$price_excl_tax_total = $item["price_excl_tax_total"];
			$price_incl_tax_total = $item["price_incl_tax_total"];

			// merchant fields
			$merchant_id = $item["merchant_id"];
			$merchant_email = $item["merchant_email"];
			$merchant_name = $item["merchant_name"];
			$merchant_first_name = $item["merchant_first_name"];
			$merchant_last_name = $item["merchant_last_name"];
			$merchant_cell_phone = $item["merchant_cell_phone"];

			// supplier fields
			$supplier_id = $item["supplier_id"];
			$supplier_email = $item["supplier_email"];
			$supplier_name = $item["supplier_name"];
			$supplier_short_desc = $item["supplier_short_desc"];
			$supplier_full_desc = $item["supplier_full_desc"];
			$supplier_cell_phone = $item["supplier_cell_phone"];

			$item_name = $item["item_name"];
			$item_code = $item["item_code"];
			$manufacturer_code = $item["manufacturer_code"];
			$is_recurring = $item["is_recurring"];
			$recurring_last_payment = $item["recurring_last_payment"];
			$recurring_next_payment = $item["recurring_next_payment"];

			// subscription fields
			$is_subscription = $item["is_subscription"];
			$subscription_id = $item["subscription_id"];
			$subscription_start_date = $item["subscription_start_date"];
			$subscription_expiry_date= $item["subscription_expiry_date"];

			if ($parse_template) {
				$t->set_var("components", "");
				$t->set_var("components_block", "");
			}
			$components = isset($item["components"]) ? $item["components"] : "";
			if ($subcomponents_show_type == 1 && is_array($components) && sizeof($components) > 0) {
				for ($c = 0; $c < sizeof($components); $c++) {
					$cc_id = $components[$c];
					$component = $order_items[$cc_id];
					$component_id = $component["item_id"];
					$selection_name = $component["selection_name"];
					if ($selection_name) { $selection_name .= ": "; }
					$component_name = $component["item_name"];
					$component_price = $component["price"];
					$component_quantity = $component["quantity"];
					$component_sub_quantity = intval($component_quantity / $quantity);
					$component_item_code = $component["item_code"];
					$component_manufacturer_code = $component["manufacturer_code"];

					$component_image = $component["super_image"];
					$image_type = 4;
					if (!$component_image) { 
						$component_image = $component["big_image"];
						$image_type = 3;
					}
					if ($component_image && $parse_template) {
						$component_icon = product_image_icon($component_id, $component_name, $component_image, $image_type);
					} else {
						$component_icon = "";
					}

					$price += ($component["price"] * $component_sub_quantity);
					$item_total += $component["item_total"];
					$item_tax += ($component["item_tax"] * $component_sub_quantity);
					$item_tax_total += $component["item_tax_total"];
					$price_excl_tax += ($component["price_excl_tax"] * $component_sub_quantity);
					$price_incl_tax += ($component["price_incl_tax"] * $component_sub_quantity);
					$price_excl_tax_total += ($component["price_excl_tax_total"]);
					$price_incl_tax_total += ($component["price_incl_tax_total"]);

					$points_price += ($component["points_price"] * $component_sub_quantity);
					$reward_points += ($component["reward_points"] * $component_sub_quantity);
					$reward_credits += ($component["reward_credits"] * $component_sub_quantity);

					if ($parse_template) {
						$t->set_var("component_codes", "");
						$t->set_var("component_item_code_block", "");
						$t->set_var("component_man_code_block", "");
						$t->set_var("component_order_item_id", $cc_id);
						$t->set_var("component_quantity", $component_sub_quantity);
						$t->set_var("selection_name", $selection_name);
						$t->set_var("component_name", $component_name);
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
						$t->set_var("component_icon", $component_icon);
						if ($component_price > 0) {
							$t->set_var("component_price", $option_positive_price_right . currency_format($component_price) . $option_positive_price_left);
						} elseif ($component_price < 0) {
							$t->set_var("component_price", $option_negative_price_right . currency_format(abs($component_price)) . $option_negative_price_left);
						} else {
							$t->set_var("component_price", "");
						}

						$t->sparse("components", true);
					}
				}
				if ($parse_template) {
					$t->sparse("components_block", false);
				}
			}

			if (strlen($order_items_ids)) { $order_items_ids .= ","; }
			$order_items_ids .= $order_item_id;

			if ($page_type == "user_order") {
				$sql  = " SELECT id.download_id,i.item_name,id.download_added, ";
				$sql .= " i.download_path AS product_path, id.download_path, ";
				$sql .= " id.download_expiry ";
				$sql .= " FROM " . $table_prefix . "items_downloads id, " . $table_prefix . "items i ";
				$sql .= " WHERE id.item_id=i.item_id ";
				$sql .= " AND id.order_item_id=" . $db->tosql($order_item_id, INTEGER);
				$sql .= " AND id.order_id=" . $db->tosql($order_id, INTEGER);
				$sql .= " AND id.activated=1";
				$t->set_var("download_links", "");
				$dbd->query($sql);
				while ($dbd->next_record()) {
					$download_id = $dbd->f("download_id");
					$product_path = trim($dbd->f("product_path"));
					$download_path = trim($dbd->f("download_path"));
					if (!$download_path) {
						$download_path = $product_path;
					}
					$download_added = $dbd->f("download_added", DATETIME);
					$download_expiry = $dbd->f("download_expiry", DATETIME);
					$current_date = mktime(0,0,0, date("m"), date("d"), date("Y"));
					$expiry_date = $current_date;
					if (is_array($download_expiry)) {
						$expiry_date = mktime (0,0,0, $download_expiry[MONTH], $download_expiry[DAY], $download_expiry[YEAR]);
					}
					$item_download_url  = $settings["site_url"] . "download.php?download_id=" . $download_id;
					$vc = md5($download_id . $download_added[3].$download_added[4].$download_added[5]);
					$item_download_url .= "&vc=" . urlencode($vc);
					if ($expiry_date >= $current_date) {
						$product_paths = split(";", $download_path);
						for ($di = 0; $di < sizeof($product_paths); $di++) {
							$sub_path = $product_paths[$di];
							if ($sub_path) {
								$sub_url = $item_download_url . "&path_id=" . ($di + 1);
								$t->set_var("filename", basename($sub_path));
								$t->set_var("download_id", $download_id);
								$t->set_var("vc", $vc);
								$t->set_var("download_url", $sub_url);
								$t->parse("download_links");
							}
						}
					}
				}
				$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "releases  ";
				$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);;
				$dbd->query($sql);
				$dbd->next_record();
				$releases_number = $dbd->f(0);
				if ($releases_number > 0) {
					$t->set_var("order_item_id", $order_item_id);
					$t->parse("releases_link", false);
				} else {
					$t->set_var("releases_link", "");
				}
			}

			$serial_numbers = ""; $gift_vouchers = "";
			if ($page_type == "user_order" || $page_type == "user_merchant_order" || $page_type == "admin_order") {
				$t->set_var("serial_numbers", "");
				$sql  = " SELECT serial_id, serial_number, activations_number ";
				$sql .= " FROM " . $table_prefix . "orders_items_serials ";
				$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
				$sql .= " AND order_id=" . $db->tosql($order_id, INTEGER);
				if ($page_type == "user_order") {
					$sql .= " AND activated=1";
				}
				$dbd->query($sql);
				while ($dbd->next_record()) {
					$serial_id = $dbd->f("serial_id");
					$serial_number = $dbd->f("serial_number");
					$t->set_var("serial_id", $serial_id);
					$t->set_var("serial_number", $serial_number);
					$t->sparse("serial_numbers", true);
				}
			}

			if ($page_type == "user_order" || $page_type == "admin_order") {
				$t->set_var("gift_vouchers", "");
				$sql  = " SELECT coupon_id, coupon_code ";
				$sql .= " FROM " . $table_prefix . "coupons ";
				$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
				$sql .= " AND order_id=" . $db->tosql($order_id, INTEGER);
				if ($page_type == "user_order") {
					$sql .= " AND is_active=1";
				}
				$dbd->query($sql);
				while ($dbd->next_record()) {
					$coupon_id = $dbd->f("coupon_id");
					$coupon_code = $dbd->f("coupon_code");
					$t->set_var("coupon_id", $coupon_id);
					$t->set_var("coupon_code", $coupon_code);
					$t->sparse("gift_vouchers", true);
				}
			}

			// show information about coupons used
			if ($parse_template && strlen($coupons_ids)) {
				$t->set_var("item_coupons", "");
				$sql  = " SELECT coupon_id, coupon_code, coupon_title, discount_type, discount_amount ";
				$sql .= " FROM " . $table_prefix . "coupons ";
				$sql .= " WHERE coupon_id IN (" . $db->tosql($coupons_ids, INTEGERS_LIST) . ") ";
				$dbd->query($sql);
				while ($dbd->next_record()) {
					$coupon_id = $dbd->f("coupon_id");
					$coupon_code = $dbd->f("coupon_code");
					$coupon_title = $dbd->f("coupon_title");
					$coupon_type = $dbd->f("discount_type");
					$coupon_discount = $dbd->f("discount_amount");
					if ($coupon_type == 3) {
						$item_discount = round(($real_price / 100) * $coupon_discount, 2);
					} else {
						$item_discount = $coupon_discount;
					}
					$coupon_title .= " (-" . currency_format($item_discount, $order_currency) . ")";

					$t->set_var("coupon_id", $coupon_id);
					$t->set_var("coupon_code", $coupon_code);
					$t->set_var("coupon_title", $coupon_title);

					$t->sparse("item_coupons", true);
				}
			}

			if ($page_type == "admin_order") {
				set_options($order_statuses, $item_status, "item_status");
				$t->set_var("current_item_status", $item_status);
			}			

			$goods_total += $item_total;
			$goods_tax_total += $item_tax_total;
			$goods_total_excl_tax += $price_excl_tax_total;
			$goods_total_incl_tax += $price_incl_tax_total;

			$total_quantity += $quantity;

			// save tax summary data
			$item_tax_text = str_replace(".", "_", strval(round($item_tax_percent, 4)));
			if (isset($items_taxes[$item_tax_text])) {
				$items_taxes[$item_tax_text][0] += $price_excl_tax_total;
				$items_taxes[$item_tax_text][1] += $item_tax_total;
				$items_taxes[$item_tax_text][2] += $price_incl_tax_total;
			} else {
				$items_taxes[$item_tax_text] = array($price_excl_tax_total, $item_tax_total, $price_incl_tax_total, $item_tax_percent);
			}

			$item_text = $item_name;
			// get options for every product
			$item_properties = ""; $item_properties_text = "";
			$sql  = " SELECT property_name, property_value, additional_price ";
			$sql .= " FROM " . $table_prefix . "orders_items_properties ";
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$sql .= " AND order_item_id=" . $db->tosql($order_item_id, INTEGER);
			$dbd->query($sql);
			while ($dbd->next_record()) {
				$property_name = get_translation($dbd->f("property_name"));
				$property_value = get_translation($dbd->f("property_value"));

				$property_price = $dbd->f("additional_price");
				// get tax amount to show for product option
				$property_price_tax = get_tax_amount($order_tax_rates, $item_type_id, $property_price, $item_tax_free, $item_property_tax_percent, "", 1, $tax_prices_type, $tax_round);
				$property_text = $property_name . ": " . $property_value;
				if ($property_price > 0) {
					$property_text .= $option_positive_price_right . currency_format($property_price, $order_currency, $property_price_tax) . $option_positive_price_left;
				} elseif ($property_price < 0) {
					$property_text .= $option_negative_price_right . currency_format(abs($property_price), $order_currency, $property_price_tax) . $option_negative_price_left;
				}
				$item_properties .= "<br>" . $property_text;
				if ($item_properties_text) { $item_properties_text .= "; "; }
				$item_properties_text .= $property_text;
			}

			if ($item_properties) {
				$item_text .= " (" . $item_properties_text . ")";
			}
			$item_text .= " " . PROD_QTY_COLUMN . ": " . $quantity . " " . currency_format($item_total, $order_currency);
			$items_text .= $item_text . $eol;

			// get additional fields
			$sql  = " SELECT short_description, full_description FROM " . $table_prefix . "items ";
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
			$dbd->query($sql);
			if ($dbd->next_record()) {
				$stock_level = $dbd->f("stock_level");
				$short_description = strip_tags($dbd->f("short_description"));
				$full_description = strip_tags($dbd->f("full_description"));
			}			
			
			// image form db
			$item_image = ""; $item_image_alt = "";
			if ($item_image_column && $image_field) { 
				$sql  = " SELECT " . $image_field; 
				if ($image_alt_field) 
					$sql .= " , " . $image_alt_field;
				$sql .= " FROM " . $table_prefix . "items";
				$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);				
				$dbd->query($sql);			
				$image_exists = false;
				if ($dbd->next_record()) {
					$item_image = $dbd->f($image_field);	
					$item_image_alt = get_translation($dbd->f($image_alt_field));	
					if (!strlen($item_image)) {
						$item_image = $product_no_image;
					} else {
						$image_exists = true;
					}
				}
			}
			
			$cart_items[] = array(
				"item_id" => $item_id, "id" => $item_id, "product_id" => $item_id,
				"weight" => $weight, "price" => $price, "quantity" => $quantity, "tax_free" => $item_tax_free,
				"discount_amount" => $discount_amount, "real_price" => $real_price,
				"points_price" => $points_price, "reward_points" => $reward_points, "reward_credits" => $reward_credits,
				"buying_price" => $buying_price, "item_name" => $item_name, "product_name" => $item_name,
				"product_title" => $item_name, "item_title" => $item_name, "item_total" => $item_total,
				"downloadable" => $downloadable, "item_type_id" => $item_type_id, "stock_level" => $stock_level,
				"short_description" => $short_description, "description" => $short_description,
				"full_description" => $full_description, "item_properties_text" => $item_properties_text,
				"merchant_id" => $merchant_id, "merchant_email" => $merchant_email, "merchant_name" => $merchant_name,
				"merchant_first_name" => $merchant_first_name, "merchant_last_name" => $merchant_last_name,
				"merchant_cell_phone" => $merchant_cell_phone, 
				"supplier_id" => $supplier_id, "supplier_email" => $supplier_email, "supplier_name" => $supplier_name,
				"supplier_short_desc" => $supplier_short_desc, "supplier_full_desc" => $supplier_full_desc,
				"supplier_cell_phone" => $supplier_cell_phone, 
				"item_image" => $item_image, "item_image_alt" => $item_image_alt
			);
						
			if ($parse_template) { // set item variables into html
				$t->set_var("item_id", $item_id);
				$t->set_var("order_item_id", $order_item_id);
				$t->set_var("item_name", $item_name);
				$t->set_var("item_title", $item_name);
				$t->set_var("item_name_strip", htmlspecialchars(strip_tags($item_name)));
				$t->set_var("item_code", $item_code);
				$t->set_var("manufacturer_code", $manufacturer_code);

				// show product code
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
				if ($points_price > 0 && $show_points_price) {
					$t->set_var("points_price", number_format($points_price, $points_decimals));
					$t->sparse("points_price_block", false);
				} else {
					$t->set_var("points_price_block", "");
				}
				if ($reward_points > 0 && $show_reward_points) {
					$t->set_var("reward_points", number_format($reward_points, $points_decimals));
					$t->sparse("reward_points_block", false);
				} else {
					$t->set_var("reward_points_block", "");
				}
				if ($reward_credits > 0 && $show_reward_credits 
					&& ($reward_credits_users == 0 || ($reward_credits_users == 1 && $session_user_id))) {
					$t->set_var("reward_credits", currency_format($reward_credits));
					$t->sparse("reward_credits_block", false);
				} else {
					$t->set_var("reward_credits_block", "");
				}

				if ($is_recurring) {
					$t->set_var("next_payment_date", va_date($date_show_format, $recurring_next_payment));
					$t->sparse("next_recurring_payment", false);
				} else {
					$t->set_var("next_recurring_payment", "");
				}
				if (preg_match_all("/http\:\/\/[^\s<>\n]+/mi", $item_properties, $matches)) {
					for ($m = 0; $m < sizeof($matches[0]); $m++) {
						$link_url = $matches[0][$m];
						$html_link_url = "<a href=\"".$link_url."\" target=\"_blank\">" . basename($link_url) . "</a>";
						$item_properties = str_replace($link_url, $html_link_url, $item_properties);
					}
				}
				if ($is_subscription && $t->block_exists("cancel_subscription_link")) {
					$current_datetime = va_time();
					$current_date_ts = mktime (0, 0, 0, $current_datetime[MONTH], $current_datetime[DAY], $current_datetime[YEAR]);
					$subscription_sd_ts = va_timestamp($subscription_start_date);
					$subscription_ed_ts = va_timestamp($subscription_expiry_date);
					$subscription_days = intval(($subscription_ed_ts - $subscription_sd_ts) / 86400); // get int value due to possible 1 hour difference
					// check days difference and add current day as well
					$used_days = intval(($current_date_ts - $subscription_sd_ts) / 86400) + 1;
					$sql  = " SELECT setting_value FROM " . $table_prefix . "user_types_settings ";
					$sql .= " WHERE type_id=" . $db->tosql($order_user_type_id, INTEGER);
					$sql .= " AND setting_name='cancel_subscription'";
					$cancel_subscription = get_db_value($sql);
					if ($cancel_subscription == 1) {
						// return money to credits balance
						$credits_return = round((($price - $reward_credits)/ $subscription_days) * ($subscription_days - $used_days), 2); 
					} else {
						$credits_return = 0; 
					}
					if ($credits_return > 0) {
						$confirm_cancel_subscription = CONFIRM_RETURN_SUBSCRIPTION_MSG;
					} else {
						$confirm_cancel_subscription = CONFIRM_CANCEL_SUBSCRIPTION_MSG;
					}
					$confirm_cancel_subscription = str_replace(array("{credits_amount}", "\'"), array(currency_format($credits_return), "\\'"), $confirm_cancel_subscription);
					$t->set_var("confirm_cancel_subscription", $confirm_cancel_subscription);
					$t->sparse("cancel_subscription_link", false);
				} else {
					$t->set_var("cancel_subscription_link", "");
				}
				$t->set_var("item_properties", $item_properties);
				$t->set_var("item_options", $item_properties);
				$t->set_var("quantity", $quantity);

				$t->set_var("price_excl_tax", currency_format($price_excl_tax, $order_currency));
				$t->set_var("item_tax_percent", $item_tax_percent . "%");
				$t->set_var("item_tax", currency_format($item_tax, $order_currency));
				$t->set_var("price_incl_tax", currency_format($price_incl_tax, $order_currency));
				$t->set_var("price_excl_tax_total", currency_format($price_excl_tax_total, $order_currency));
				$t->set_var("item_tax_total", currency_format($item_tax_total, $order_currency));
				$t->set_var("price_incl_tax_total", currency_format($price_incl_tax_total, $order_currency));
				
				// item image display
				if ($item_image) {
					if (preg_match("/^http\:\/\//", $item_image)) {
						$image_size = "";
					} else {
						$image_size = @getimagesize($item_image);
						if ($image_exists && ($watermark || $restrict_products_images)) {
							$item_image = "image_show.php?item_id=".$item_id."&type=".$image_type_name."&vc=".md5($item_image);
						}
						if ($is_admin_path) {
							$item_image  = $root_folder_path . $item_image;
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
				
				parse_cart_columns($item_name_column, $item_price_column, $item_tax_percent_column, $item_tax_column, $item_price_incl_tax_column, $item_quantity_column, $item_price_total_column, $item_tax_total_column, $item_price_incl_tax_total_column, $item_image_column);
				$t->sparse("items", true);
			}
		}

		if ($parse_template) {
			//$t->set_var("tax_name", $tax_name); TODO change tax name to summary
			$t->set_var("order_date", $order_date);
			$t->set_var("order_items", $order_items_ids);
			$t->set_var("total_quantity", $total_quantity);
			$t->set_var("goods_total_excl_tax", currency_format($goods_total_excl_tax, $order_currency));
			$t->set_var("goods_tax_total", currency_format($goods_tax_total, $order_currency));
			$t->set_var("goods_total_incl_tax", currency_format($goods_total_incl_tax, $order_currency));

			// show total reward credits
			if ($show_reward_credits && $total_reward_credits && ($reward_credits_users == 0 || ($reward_credits_users == 1 && $session_user_id))) {
				$t->set_var("reward_credits_total", currency_format($total_reward_credits));
				$t->sparse("reward_credits_total_block", false);
			}
			// show total reward points 
			if ($show_reward_points && $total_reward_points) {
				$t->set_var("reward_points_total", number_format($total_reward_points, $points_decimals));
				$t->sparse("reward_points_total_block", false);
			}

			if ($goods_colspan > 0) {
				$t->sparse("goods_name_column", false);
			}
			if ($item_price_total_column) {
				$t->sparse("goods_total_excl_tax_column", false);
			}
			if ($item_tax_total_column) {
				$t->sparse("goods_tax_total_column", false);
			}
			if ($item_price_incl_tax_total_column) {
				$t->sparse("goods_total_incl_tax_column", false);
			}

			// parse tax groups
			foreach ($items_taxes as $items_tax_text => $items_tax_data) {
				$t->set_var("goods_total_excl_tax_" . $items_tax_text, currency_format($items_tax_data[0], $order_currency));
				$t->set_var("goods_total_" . $items_tax_text, currency_format($items_tax_data[0], $order_currency));
				$t->set_var("goods_tax_total_" . $items_tax_text,  currency_format($items_tax_data[1], $order_currency));
				$t->set_var("goods_with_tax_total_" . $items_tax_text, currency_format(($items_tax_data[2] + $items_tax_data[1]), $order_currency));
				$t->set_var("goods_total_incl_tax_" . $items_tax_text, currency_format(($items_tax_data[2] + $items_tax_data[1]), $order_currency));
			}

		}

		// show information about order coupons used
		if ($parse_template && strlen($order_coupons_ids)) {
			$max_discount = $goods_total;
			$max_tax_discount = $goods_tax_total;

			$t->set_var("order_coupons", "");
			$sql  = " SELECT * FROM " . $table_prefix . "orders_coupons ";
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$sql .= " AND coupon_id IN (" . $db->tosql($order_coupons_ids, INTEGERS_LIST) . ") ";
			$dbd->query($sql);
			if ($dbd->next_record()) {
				do {
					$coupon_id = $dbd->f("coupon_id");
					$coupon_code = $dbd->f("coupon_code");
					$coupon_title = $dbd->f("coupon_title");
					$discount_amount = $dbd->f("discount_amount");
					$discount_tax_amount = $dbd->f("discount_tax_amount");
					if ($tax_prices_type == 1) {
						$discount_amount_excl_tax = $discount_amount - $discount_tax_amount;
						$discount_amount_incl_tax = $discount_amount;
					} else {
						$discount_amount_excl_tax = $discount_amount;
						$discount_amount_incl_tax = $discount_amount + $discount_tax_amount;
					}

					$t->set_var("coupon_id", $coupon_id);
					$t->set_var("coupon_code", $coupon_code);
					$t->set_var("coupon_title", $coupon_title);
					$t->set_var("discount_amount_excl_tax", "-" . currency_format($discount_amount_excl_tax, $order_currency));
					$t->set_var("discount_tax_amount", "-" . currency_format($discount_tax_amount, $order_currency));
					$t->set_var("discount_amount_incl_tax", "- " . currency_format($discount_amount_incl_tax, $order_currency));

					if ($goods_colspan > 0) {
						$t->sparse("coupon_name_column", false);
					}
					if ($item_price_total_column) {
						$t->sparse("coupon_amount_column", false);
					}
					if ($item_tax_total_column) {
						$t->sparse("coupon_tax_column", false);
					}
					if ($item_price_incl_tax_total_column) {
						$t->sparse("coupon_amount_incl_tax_column", false);
					}

					$t->sparse("order_coupons", true);

				} while ($dbd->next_record());
			} else {
				$sql  = " SELECT coupon_id, coupon_code, coupon_title, discount_type, coupon_tax_free, discount_amount ";
				$sql .= " FROM " . $table_prefix . "coupons ";
				$sql .= " WHERE coupon_id IN (" . $db->tosql($order_coupons_ids, INTEGERS_LIST) . ") ";
				$dbd->query($sql);
				while ($dbd->next_record()) {
					$coupon_id = $dbd->f("coupon_id");
					$coupon_code = $dbd->f("coupon_code");
					$coupon_title = $dbd->f("coupon_title");
					$coupon_type = $dbd->f("discount_type");
					$coupon_tax_free = $dbd->f("coupon_tax_free");
					$coupon_discount = $dbd->f("discount_amount");
					if ($coupon_type == 1) {
						$discount_amount = round(($goods_total / 100) * $coupon_discount, 2);
					} else {
						$discount_amount = $coupon_discount;
					}
					if ($discount_amount > $max_discount) {
						$discount_amount = $max_discount;
					}
					$max_discount -= $discount_amount;

					// check discount tax
					if ($coupon_tax_free && $tax_prices_type != 1) {
						$discount_tax_amount = 0;
					} else {
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
					$t->set_var("coupon_code", $coupon_code);
					$t->set_var("coupon_title", $coupon_title);
					$t->set_var("discount_amount_excl_tax", "-" . currency_format($discount_amount_excl_tax, $order_currency));
					$t->set_var("discount_tax_amount", "-" . currency_format($discount_tax_amount, $order_currency));
					$t->set_var("discount_amount_incl_tax", "- " . currency_format($discount_amount_incl_tax, $order_currency));

					if ($goods_colspan > 0) {
						$t->sparse("coupon_name_column", false);
					}
					if ($item_price_total_column) {
						$t->sparse("coupon_amount_column", false);
					}
					if ($item_tax_total_column) {
						$t->sparse("coupon_tax_column", false);
					}
					if ($item_price_incl_tax_total_column) {
						$t->sparse("coupon_amount_incl_tax_column", false);
					}

					$t->sparse("order_coupons", true);
				}
			}
		}


		if ($total_discount > 0) 
		{
			if ($parse_template) {
				if ($tax_prices_type == 1) {
					$total_discount_excl_tax = $total_discount - $total_discount_tax;
					$total_discount_incl_tax = $total_discount;
				} else {
					$total_discount_excl_tax = $total_discount;
					$total_discount_incl_tax = $total_discount + $total_discount_tax;
				}

				$t->set_var("total_discount_excl_tax", "-" . currency_format($total_discount_excl_tax, $order_currency));
				$t->set_var("total_discount_tax_amount", "- " . currency_format($total_discount_tax, $order_currency));
				$t->set_var("total_discount_incl_tax", "- " . currency_format($total_discount_incl_tax, $order_currency));
				$t->set_var("discounted_amount_excl_tax", currency_format(($goods_total_excl_tax - $total_discount_excl_tax), $order_currency));
				$t->set_var("discounted_tax_amount", currency_format(($goods_tax_total - $total_discount_tax), $order_currency));
				$t->set_var("discounted_amount_incl_tax", currency_format(($goods_total_incl_tax - $total_discount_incl_tax), $order_currency));

				if ($goods_colspan > 0) {
					$t->sparse("total_discount_name_column", false);
					$t->sparse("discounted_name_column", false);
				}
				if ($item_price_total_column) {
					$t->sparse("total_discount_amount_excl_tax_column", false);
					$t->sparse("discounted_amount_excl_tax_column", false);
				}
				if ($item_tax_total_column) {
					$t->sparse("total_discount_tax_column", false);
					$t->sparse("discounted_tax_column", false);
				}
				if ($item_price_incl_tax_total_column) {
					$t->sparse("total_discount_amount_incl_tax_column", false);
					$t->sparse("discounted_amount_incl_tax_column", false);
				}

				$t->sparse("discount", false);
			}
		}
		$goods_with_discount = $goods_total - $total_discount;
		$goods_tax_value = $goods_tax_total - $total_discount_tax;

		$cart_properties = 0; $personal_properties = 0;
		$delivery_properties = 0; $payment_properties = 0;
		$properties_total = 0; $properties_taxable = 0;
		$orders_properties = array();
		$sql  = " SELECT op.property_id, op.property_type, op.property_name, op.property_value, ";
		$sql .= "  op.property_price, op.property_points_amount, op.tax_free ";
		$sql .= " FROM " . $table_prefix . "orders_properties op ";
		$sql .= " WHERE op.order_id=" . $db->tosql($order_id, INTEGER);
		if ($page_type == "cc_info") {
			$sql .= " AND op.property_type IN (1,2,3) ";
		}
		$sql .= " ORDER BY op.property_order, op.property_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$property_id   = $db->f("property_id");
			$property_type = $db->f("property_type");
			$property_name = get_translation($db->f("property_name"));
			$property_value = get_translation($db->f("property_value"));
			$property_price = $db->f("property_price");
			$property_points_amount = $db->f("property_points_amount");
			$property_tax_free = $db->f("tax_free");
			$control_type = $db->f("control_type");

			if (isset($orders_properties[$property_id])) {
				$orders_properties[$property_id]["value"] .= "; " . $property_value;
				$orders_properties[$property_id]["price"] += $property_price;
				$orders_properties[$property_id]["points_amount"] += $property_points_amount;
			} else {
				$orders_properties[$property_id] = array(
					"type" => $property_type, "name" => $property_name, "value" => $property_value,
					"price" => $property_price, "points_amount" => $property_points_amount, "tax_free" => $property_tax_free,
				);
			}
		}
		foreach ($orders_properties as $property_id => $property_values) {
			$property_type = $property_values["type"];
			$property_name = $property_values["name"];
			$property_value = $property_values["value"];
			$property_price = $property_values["price"];
			$property_points_amount = $property_values["points_amount"];
			$property_tax_free = $property_values["tax_free"];

			$properties_total += $property_price;
			if ($property_tax_free != 1) {
				$properties_taxable += $property_price;
			}
			$property_tax_values = get_tax_amount($order_tax_rates, "properties", $property_price, $property_tax_free, $property_tax_percent, "", 2, $tax_prices_type, $tax_round);
			$property_tax = add_tax_values($order_tax_rates, $property_tax_values, "properties", $tax_round);

			if ($tax_prices_type == 1) {
				$property_price_excl_tax = $property_price - $property_tax;
				$property_price_incl_tax = $property_price;
			} else {
				$property_price_excl_tax = $property_price;
				$property_price_incl_tax = $property_price + $property_tax;
			}

			if ($property_type == 1) {
				$items_text .= $property_name . "(" . $property_value . ") " . $eol;
			}
			if ($parse_template) {
				$t->set_var("field_name_" . $property_id, $property_name);
				$t->set_var("field_value_" . $property_id, $property_value);
				$t->set_var("field_price_" . $property_id, $property_price);
				$t->set_var("field_" . $property_id, $property_value);
				$t->set_var("property_name", $property_name);
				$t->set_var("property_value", $property_value);
				if ($property_price != 0) {
					$property_price_text = currency_format($property_price, $order_currency, $property_tax);
				} else {
					$property_price_text = "";
				}
				$t->set_var("property_price", $property_price_text);
				if ($property_points_amount > 0 && $show_points_price) {
					$t->set_var("property_points_price", number_format($property_points_amount, $points_decimals));
					$t->sparse("property_points_price_block", false);
				} else {
					$t->set_var("property_points_price_block", "");
				}

				if ($property_price == 0) {
					$t->set_var("property_price_excl_tax", "");
					$t->set_var("property_tax", "");
					$t->set_var("property_price_incl_tax", "");
				} else {
					$t->set_var("property_price_excl_tax", currency_format($property_price_excl_tax, $order_currency));
					$t->set_var("property_tax", currency_format($property_tax, $order_currency));
					$t->set_var("property_price_incl_tax", currency_format($property_price_incl_tax, $order_currency));
				}
				if ($property_type == 1) {
			    $cart_properties++;
					if ($item_price_total_column) {
						$t->sparse("property_price_excl_tax_column", false);
					}
					if ($item_tax_total_column) {
						$t->sparse("property_tax_column", false);
					}
					if ($item_price_incl_tax_total_column) {
						$t->sparse("property_price_incl_tax_column", false);
					}
					$t->sparse("cart_properties", true);
				} elseif ($property_type == 2) {
					$personal_properties++;
					$t->sparse("personal_properties", true);
				} elseif ($property_type == 3) {
					$delivery_properties++;
					$t->sparse("delivery_properties", true);
				} elseif ($property_type == 4) {
					$payment_properties++;
					$t->sparse("payment_properties", true);
				}
			}
		}
		if ($parse_template) {
			$t->set_var("properties_total", $properties_total);
			$t->set_var("properties_taxable", $properties_taxable);
		}

		if (strlen($shipping_type_desc) || $shipping_cost > 0) {
			if ($parse_template) {
				$t->set_var("current_shipping_id", $shipping_type_id);
				$t->set_var("shipping_type_desc",  $shipping_type_desc);
				$t->set_var("shipping_cost_excl_tax", currency_format($shipping_cost_excl_tax, $order_currency));
				$t->set_var("shipping_cost_desc", currency_format($shipping_cost_excl_tax, $order_currency));
				$t->set_var("shipping_tax", currency_format($shipping_tax_total, $order_currency));
				$t->set_var("shipping_cost_incl_tax", currency_format($shipping_cost_incl_tax, $order_currency));
				if ($shipping_points_amount > 0 && $show_points_price) {
					$t->set_var("shipping_points_price", number_format($shipping_points_amount, $points_decimals));
					$t->sparse("shipping_points_price_block", false);
				} else {
					$t->set_var("shipping_points_price_block", "");
				}
				if ($item_price_total_column) {
					$t->sparse("shipping_cost_excl_tax_column", false);
				}
				if ($item_tax_total_column) {
					$t->sparse("shipping_tax_column", false);
				}
				if ($item_price_incl_tax_total_column) {
					$t->sparse("shipping_cost_incl_tax_column", false);
				}
				$t->sparse("shipping_type", false);
			}
		}

		if ($page_type == "admin_order") {
			$t->set_var("currency_left", $order_currency["left"]);
			$t->set_var("currency_right", $order_currency["right"]);
			$t->set_var("currency_rate", $order_currency["rate"]);
			$t->set_var("goods_value", $goods_with_discount);
			$t->set_var("goods_tax_value", $goods_tax_value);
			$t->set_var("tax_percent", $tax_percent_sum);

			// prepare shipping
			$total_shipping_types = 0;
			if (strlen($country_id) && strlen($shipping_type_id)) {
				$sql  = " SELECT tare_weight ";
				$sql .= " FROM " . $table_prefix . "shipping_types ";
				$sql .= " WHERE shipping_type_id=" . $db->tosql($shipping_type_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$weight_total = $weight_total - $db->f("tare_weight");
				}
			}

			$shipping_type_exists = false; $free_postage = false;
			$sql  = " SELECT st.shipping_type_id, st.shipping_type_desc, st.cost_per_order, ";
			$sql .= " st.cost_per_product, st.cost_per_weight, st.tare_weight, st.is_taxable ";
			$sql .= " FROM (((((";
			$sql .= $table_prefix . "shipping_types st ";
			$sql .= " INNER JOIN " . $table_prefix . "shipping_modules sm ON st.shipping_module_id=sm.shipping_module_id) ";
			$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_countries stc ON st.shipping_type_id=stc.shipping_type_id) ";
			$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_states stt ON st.shipping_type_id=stt.shipping_type_id) ";
			$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_users stu ON st.shipping_type_id=stu.shipping_type_id) ";
			$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_sites sts ON st.shipping_type_id=sts.shipping_type_id) ";
			$sql .= " WHERE sm.is_active=1 ";
/*			$sql .= " AND sm.is_external<>1 "; EGGHEAD VENTURES COMMENTED OUT */
			$sql .= " AND st.is_active=1 ";
/*			$sql .= " AND (st.countries_all=1 OR stc.country_id=" . $db->tosql($country_id, INTEGER, true, false) . ") ";
			$sql .= " AND (st.states_all=1 OR stt.state_id=" . $db->tosql($state_id, INTEGER, true, false) . ") ";
			$sql .= " AND st.min_weight<=" . $db->tosql($weight_total, NUMBER);
			$sql .= " AND st.max_weight>=" . $db->tosql($weight_total, NUMBER);
			$sql .= " AND st.min_goods_cost<=" . $db->tosql($goods_total, NUMBER);
			$sql .= " AND st.max_goods_cost>=" . $db->tosql($goods_total, NUMBER);
			$sql .= " AND (st.sites_all=1 OR sts.site_id=" . $db->tosql($order_site_id, INTEGER, true, false) . ")";
			$sql .= " AND (st.user_types_all=1 OR stu.user_type_id=" . $db->tosql($order_user_type_id, INTEGER, true, false) . ")"; EGGHEAD VENTURES COMMENTED OUT*/
			$sql .= " GROUP BY st.shipping_type_id, st.shipping_order, st.shipping_type_code, st.shipping_type_desc, st.shipping_time, ";
			$sql .= " st.cost_per_order, st.cost_per_product, st.cost_per_weight, st.tare_weight, st.is_taxable ";
			$sql .= " ORDER BY st.shipping_order, st.shipping_type_id ";

			$db->query($sql);
			while ($db->next_record()) {
				$total_shipping_types++;
				$row_shipping_id = $db->f("shipping_type_id");
				$row_shipping_desc = $db->f("shipping_type_desc");
				$cost_per_order = $db->f("cost_per_order");
				$cost_per_product = $db->f("cost_per_product");
				$cost_per_weight = $db->f("cost_per_weight");
				$row_tare_weight = $db->f("tare_weight");
				$row_shipping_taxable = $db->f("is_taxable");
				$row_shipping_cost = $free_postage ? 0 : $cost_per_order + ($cost_per_product * $total_quantity) + ($cost_per_weight * ($weight_total + $row_tare_weight));
				$row_shipping_cost_desc = currency_format($row_shipping_cost, $order_currency);

				if ($row_shipping_id == $shipping_type_id) {
					$shipping_type_exists = true;
					$shipping_selected = "selected";
				} else {
					$shipping_selected = "";
				}
				$t->set_var("row_shipping_id", $row_shipping_id);
				$t->set_var("row_shipping_desc", $row_shipping_desc );  // ** EGGHEAD VENTURES - removed . " (" . $row_shipping_cost_desc . ")"
				$t->set_var("shipping_selected", $shipping_selected);
				$t->set_var("shipping_value", round($row_shipping_cost, 2));
				$t->set_var("shipping_taxable", intval($row_shipping_taxable));
				$t->parse("shipping_types", true);
				$t->parse("shipping_values", true);
			}
			if ($total_shipping_types > 1 && $shipping_type_exists && $shipping_points_amount <= 0) {
				$t->set_var("shipping_type", "");
				if ($item_price_total_column) {
					$t->sparse("shipping_radio_cost_column", false);
				}
				if ($item_tax_total_column) {
					$t->sparse("shipping_radio_tax_column", false);
				}
				if ($item_price_incl_tax_total_column) {
					$t->sparse("shipping_radio_cost_incl_tax_column", false);
				}
				$t->parse("shipping_selection", false);
			}
		}

		$taxes_total = 0;
		// calculate the tax
		if ($tax_available) {

			// get taxes sums for further calculations
			$taxes_sum = 0; $discount_tax_sum = $total_discount_tax;
			foreach($order_tax_rates as $tax_id => $tax_info) {
				$tax_cost = isset($tax_info["tax_total"]) ? $tax_info["tax_total"] : 0;
				$taxes_sum += va_round($tax_cost, $currency["decimals"]);
			}

			// todo
			$taxes_param = ""; $tax_number = 0;
			foreach($order_tax_rates as $tax_id => $tax_info) {
				$tax_name = $tax_info["tax_name"];
				$current_tax_free = isset($tax_info["tax_free"]) ? $tax_info["tax_free"] : 0;
				//if ($tax_free) { $current_tax_free = true; }
				$tax_percent = $tax_info["tax_percent"];
				$shipping_tax_percent = $tax_info["shipping_tax_percent"];
				$tax_types = $tax_info["types"];
				$tax_cost = isset($tax_info["tax_total"]) ? $tax_info["tax_total"] : 0;
				if ($total_discount_tax) {
					// in case if there are any order coupons decrease taxes value 
					if ($tax_number == sizeof($order_tax_rates)) {
						$tax_discount = $discount_tax_sum;
					} elseif ($taxes_sum != 0) {
						$tax_discount = round(($tax_cost * $total_discount_tax) / $taxes_sum, 2);
					} else {
						$tax_discount = 0;
					}
					$discount_tax_sum -= $tax_discount;
					$tax_cost -= $tax_discount;
				}
				$taxes_total += va_round($tax_cost, $currency["decimals"]);
  
				if ($parse_template) {
					$t->set_var("tax_id", $tax_id);
					$t->set_var("tax_percent", $tax_percent);
					$t->set_var("tax_name", $tax_name);
					$t->set_var("tax_cost", currency_format($tax_cost));
					$t->sparse("taxes", true);
  
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
			}
			if ($parse_template) {
				$t->set_var("tax_rates", $taxes_param);
			}
		}

		$order_total = round($goods_with_discount, 2) + round($properties_total, 2) + round($shipping_cost, 2);
		if ($tax_prices_type != 1) {
			$order_total += round($taxes_total, 2);
		}

		if ($processing_fee != 0) {
			if ($parse_template) {
				$t->set_var("fee_value", round($processing_fee, 2));
				$t->set_var("processing_fee_cost", currency_format($processing_fee, $order_currency));
				$t->sparse("fee");
			}
			$order_total += $processing_fee;
		}

		// show information about vouchers used
		if ($parse_template && strlen($vouchers_ids)) {

			$t->set_var("vouchers_block", "");
			$sql  = " SELECT * FROM " . $table_prefix . "orders_coupons ";
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$sql .= " AND coupon_id IN (" . $db->tosql($vouchers_ids, INTEGERS_LIST) . ") ";
			$dbd->query($sql);
			if ($dbd->next_record()) {
				do {
					$voucher_id = $dbd->f("coupon_id");
					$voucher_code = $dbd->f("coupon_code");
					$voucher_title = $dbd->f("coupon_title");
					$voucher_amount = $dbd->f("discount_amount");
					$order_total = round($order_total - $voucher_amount, 2);

					$t->set_var("voucher_id", $voucher_id);
					$t->set_var("voucher_code", $voucher_code);
					$t->set_var("voucher_title", $voucher_title);
					$t->set_var("voucher_amount", "-" . currency_format($voucher_amount, $order_currency));

					$t->sparse("used_vouchers", true);
				} while ($dbd->next_record());

				$t->sparse("vouchers_block", true);
			} 
		}

		if ($credit_amount != 0) {
			if ($parse_template) {
				$t->set_var("credit_amount_value", round($credit_amount, 2));
				$t->set_var("credit_amount_cost", "-" . currency_format($credit_amount, $order_currency));
				$t->sparse("credit_amount_block");
			}
			$order_total -= $credit_amount;
		}

		if ($parse_template) {
			$t->set_var("order_total", currency_format($order_total, $order_currency));
			if ($total_points_amount > 0) {
				$t->set_var("total_points_amount", number_format($total_points_amount, $points_decimals));
				$t->sparse("total_points_block", false);
			} else {
				$t->set_var("total_points_block", "");
			}
			$t->sparse("basket", false);
		}

		return $items_text;
	}

	function check_order($order_id, $vc, $final_check = false)
	{
		global $db, $table_prefix;
		$errors = "";
		$sql  = " SELECT order_placed_date,is_confirmed,is_placed ";
		$sql .= " FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record())
		{
			$is_placed = $db->f("is_placed");
			$is_confirmed = $db->f("is_confirmed");
			$order_placed_date = $db->f("order_placed_date", DATETIME);
			if ($is_confirmed && !$final_check) {
				// order was confirmed and can be already paid so we redirect user to order_final.php page
				header("Location: order_final.php");
				exit;
			}
			if (!$final_check && $vc != md5($order_id . $order_placed_date[3].$order_placed_date[4].$order_placed_date[5])) {
				$errors .= ORDER_CODE_ERROR . "<br>";
			}
			if ($is_placed && (!$final_check || ($final_check && $order_id != get_session("session_order_id")))) {
				$errors .= ORDER_PLACED_ERROR;
			}
		} else {
			$errors .= ORDER_EXISTS_ERROR . "<br>";
		}
		return $errors;
	}

	function get_order_id()
	{
		$order_id = get_session("session_order_id");
		if (!strlen($order_id)) { $order_id = get_param("cart_order_id"); }
		if (!strlen($order_id)) { $order_id = get_param("oid"); }
		if (!strlen($order_id)) { $order_id = get_param("cartId"); }
		if (!strlen($order_id)) { $order_id = get_param("x_invoice_num"); }
		return $order_id;
	}

	function generate_invoice_number($order_id)
	{
		global $db, $table_prefix;

		$invoice_number = $order_id;
		// check if invoice number is unique if it's not the same as order_id
		if ($invoice_number != $order_id) {
			// in case if invoice number not unique add prefix to it to make it unique
			$invoice_exists = true; $intial_number = $invoice_number; $i = 0;
			while ($invoice_exists)
			{
				$i++;
				$sql  = " SELECT order_id FROM " .$table_prefix. "orders ";
				$sql .= " WHERE (invoice_number=" . $db->tosql($invoice_number, TEXT);
				if (preg_match("/^\d+$/", $invoice_number)) {
					$sql .= " OR order_id=" . $db->tosql($invoice_number, INTEGER);
				}
				$sql .= " ) ";
				$sql .= " AND order_id<>" . $db->tosql($order_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$invoice_number = $intial_number . "-" . $i;
				} else {
					$invoice_exists = false;
				}
			}
		}
		return $invoice_number;
	}

	function check_payment($order_id, $payment_total, $payment_currency = "")
	{
		global $db, $table_prefix;
		$errors = "";
		$exchange_rate = 1;
		$currency_decimals = 2;
		if (strlen($payment_currency)) {
			$sql  = " SELECT * FROM " . $table_prefix . "currencies ";
			$sql .= " WHERE currency_code=" . $db->tosql($payment_currency, TEXT);
			$db->query($sql);
			if ($db->next_record()) {
  			$exchange_rate = $db->f("exchange_rate");
  			$currency_decimals = $db->f("decimals_number");
			} else {
				$errors .= CURRENCY_WRONG_VALUE_MSG;
			}
		}
		$sql  = " SELECT order_placed_date,order_total,order_status,currency_code,currency_rate FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record())
		{
			$order_total = $db->f("order_total");						
  			$currency_rate = $db->f("currency_rate");
			$order_total = round(($order_total * $currency_rate), $currency_decimals);
			if ($order_total != $payment_total) {
				$errors .= TRANSACTION_AMOUNT_DOESNT_MATCH_MSG;
			}
		} else {
			$errors .= ORDER_EXISTS_ERROR;
		}

		return $errors;
	}

	function update_order_status($order_id, $status_id, $order_event, $order_items, &$status_error)
	{
		global $t, $db, $db_type, $table_prefix, $settings, $cart_items;
		global $datetime_show_format, $is_admin_path, $order_step;

		$eol = get_eol();
		$is_valid = true; $is_update = false; $status_error = ""; $status_php_lib = "";
		$sql  = " SELECT * FROM " . $table_prefix . "order_statuses ";
		$sql .= " WHERE status_id=" . $db->tosql($status_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$status_id = $db->f("status_id");
			$paid_status = $db->f("paid_status");
			$download_activation = $db->f("download_activation");
			$download_notify = $db->f("download_notify");
			$commission_action = $db->f("commission_action");
			$stock_level_action = $db->f("stock_level_action");
			if (!$stock_level_action) {
				// if there is no active action we assume that it wasn't reserved
				$stock_level_action = -1;
			}
			$points_action = $db->f("points_action");
			$credit_action = $db->f("credit_action");
			$status_name = get_translation($db->f("status_name"));
			$status_type = $db->f("status_type");

			// email settings
			$email_headers = array();
			$mail_notify = $db->f("mail_notify");
			$mail_from = $db->f("mail_from");
			if (!strlen($mail_from)) { $mail_from = $settings["admin_email"]; }
			$email_headers["from"] = $mail_from;
			$email_headers["cc"] = $db->f("mail_cc");
			$email_headers["bcc"] = $db->f("mail_bcc");
			$email_headers["reply_to"] = $db->f("mail_reply_to");
			$email_headers["return_path"] = $db->f("mail_return_path");
			$mail_type = $db->f("mail_type");
			$email_headers["mail_type"] = $mail_type;
			$mail_subject = get_translation($db->f("mail_subject"));
			$mail_body = get_translation($db->f("mail_body"));
			// sms settings
			$sms_notify = $db->f("sms_notify");
			$sms_recipient = $db->f("sms_recipient");
			$sms_originator = $db->f("sms_originator");
			$sms_message = get_translation($db->f("sms_message"));

			// merchant notify settings
			$merchant_headers = array();
			$merchant_notify = $db->f("merchant_notify");
			$merchant_to = $db->f("merchant_to");
			$merchant_from = $db->f("merchant_from");
			if (!strlen($merchant_from)) { $merchant_from = $settings["admin_email"]; }
			$merchant_headers["from"] = $merchant_from;
			$merchant_headers["cc"] = $db->f("merchant_cc");
			$merchant_headers["bcc"] = $db->f("merchant_bcc");
			$merchant_headers["reply_to"] = $db->f("merchant_reply_to");
			$merchant_headers["return_path"] = $db->f("merchant_return_path");
			$merchant_mail_type = $db->f("merchant_mail_type");
			$merchant_headers["mail_type"] = $merchant_mail_type;
			$merchant_subject = get_translation($db->f("merchant_subject"));
			$merchant_body = get_translation($db->f("merchant_body"));
			// merchant sms settings
			$merchant_sms_notify = $db->f("merchant_sms_notify");
			$merchant_sms_recipient = $db->f("merchant_sms_recipient");
			$merchant_sms_originator = $db->f("merchant_sms_originator");
			$merchant_sms_message = get_translation($db->f("merchant_sms_message"));

			// supplier notify settings
			$supplier_headers = array();
			$supplier_notify = $db->f("supplier_notify");
			$supplier_to = $db->f("supplier_to");
			$supplier_from = $db->f("supplier_from");
			if (!strlen($supplier_from)) { $supplier_from = $settings["admin_email"]; }
			$supplier_headers["from"] = $supplier_from;
			$supplier_headers["cc"] = $db->f("supplier_cc");
			$supplier_headers["bcc"] = $db->f("supplier_bcc");
			$supplier_headers["reply_to"] = $db->f("supplier_reply_to");
			$supplier_headers["return_path"] = $db->f("supplier_return_path");
			$supplier_mail_type = $db->f("supplier_mail_type");
			$supplier_headers["mail_type"] = $supplier_mail_type;
			$supplier_subject = get_translation($db->f("supplier_subject"));
			$supplier_body = get_translation($db->f("supplier_body"));
			// supplier sms settings
			$supplier_sms_notify = $db->f("supplier_sms_notify");
			$supplier_sms_recipient = $db->f("supplier_sms_recipient");
			$supplier_sms_originator = $db->f("supplier_sms_originator");
			$supplier_sms_message = get_translation($db->f("supplier_sms_message"));

			// admin notify settings
			$admin_headers = array();
			$admin_notify = $db->f("admin_notify");
			$admin_to = $db->f("admin_to");
			$admin_from = $db->f("admin_from");
			if (!strlen($admin_from)) { $admin_from = $settings["admin_email"]; }
			$admin_headers["from"] = $admin_from;
			$admin_headers["cc"] = $db->f("admin_cc");
			$admin_headers["bcc"] = $db->f("admin_bcc");
			$admin_headers["reply_to"] = $db->f("admin_reply_to");
			$admin_headers["return_path"] = $db->f("admin_return_path");
			$admin_mail_type = $db->f("admin_mail_type");
			$admin_headers["mail_type"] = $admin_mail_type;
			$admin_subject = $db->f("admin_subject");
			$admin_body = $db->f("admin_body");
			// admin sms settings
			$admin_sms_notify = $db->f("admin_sms_notify");
			$admin_sms_recipient = $db->f("admin_sms_recipient");
			$admin_sms_originator = $db->f("admin_sms_originator");
			$admin_sms_message = $db->f("admin_sms_message");
		} else {
			$is_valid = false;
			$status_error = str_replace("{order_id}", $order_id, STATUS_CANT_BE_UPDATED_MSG) . str_replace("{status_id}", $status_id, CANT_FIND_STATUS_MSG);
			//"The status for order No " . $order_id . " can't be updated. Can't find the status with ID: " . $status_id;              			
			return false;
		}

		$sql  = " SELECT o.*,os.status_name,os.paid_status,os.stock_level_action,ps.payment_name,";
		$sql .= " ps.capture_php_lib,ps.refund_php_lib,ps.void_php_lib, ";
		$sql .= " o.currency_code, o.currency_rate, c.symbol_right, c.symbol_left, c.decimals_number, c.decimal_point, c.thousands_separator ";
		$sql .= " FROM (((" . $table_prefix . "orders o ";
		$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON os.status_id=o.order_status) ";
		$sql .= " LEFT JOIN " . $table_prefix . "payment_systems ps ON ps.payment_id=o.payment_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "currencies c ON o.currency_code=c.currency_code) ";
		$sql .= " WHERE o.order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$order_id = $db->f("order_id");
			$order_user_id = $db->f("user_id");
			$current_status_id = $db->f("order_status");
			$current_status = get_translation($db->f("status_name"));
			$current_paid_status = $db->f("paid_status");
			$current_stock_action = $db->f("stock_level_action");
			if (!$current_stock_action) {
				// if there is no active action we assume that it wasn't reserved
				$current_stock_action = -1;
			}
			$payment_name = get_translation($db->f("payment_name"));
			$shipping_points_amount = $db->f("shipping_points_amount");
			$properties_points_amount = $db->f("properties_points_amount");
			$credit_amount = $db->f("credit_amount");

			// get order currency
			$order_currency = array();
			$order_currency_code = $db->f("currency_code");
			$order_currency_rate = $db->f("currency_rate");
			$order_currency["code"] = $db->f("currency_code");
			$order_currency["rate"] = $db->f("currency_rate");
			$order_currency["left"] = $db->f("symbol_left");
			$order_currency["right"] = $db->f("symbol_right");
			$order_currency["decimals"] = $db->f("decimals_number");
			$order_currency["point"] = $db->f("decimal_point");
			$order_currency["separator"] = $db->f("thousands_separator");

			$affiliate_user_id = $db->f("affiliate_user_id");
			$tax_total = $db->f("tax_total");
			$order_info = $db->Record;
			$order_info["tax_cost"] = $tax_total;
			$user_mail = strlen($order_info["email"]) ? $order_info["email"] : $order_info["delivery_email"];

			$t->set_vars($order_info);
			$t->set_var("site_url", $settings["site_url"]);

			$order_placed_date = $db->f("order_placed_date", DATETIME);
			$date_formated = va_date($datetime_show_format, $order_placed_date);
			$t->set_var("order_placed_date", $date_formated);

			// get library to handle status change
			if ($status_type == "CAPTURE" || $status_type == "CAPTURED") {
				$status_php_lib = $db->f("capture_php_lib");
			} elseif ($status_type == "REFUND" || $status_type == "REFUNDED") {
				$status_php_lib = $db->f("refund_php_lib");
			} elseif ($status_type == "VOID" || $status_type == "VOIDED") {
				$status_php_lib = $db->f("void_php_lib");
			}

			// preparing downloadable data
			// get download links
			$links = get_order_links($order_id);
			$links_notify = ($download_notify && $links["text"] != "");
			// get serial numbers
			$order_serials = get_serial_numbers($order_id);
			$serials_notify = ($download_notify && $order_serials["text"] != "");
			// get gift vouchers
			$order_vouchers = get_gift_vouchers($order_id);
			$vouchers_notify = ($download_notify && $order_vouchers["text"] != "");
		} else {
			$is_valid = false;
		}

		// apply a php library for Capture, Refund or Void status
		if ($is_valid && $status_id != $current_status_id && strlen($status_php_lib)) {
			$root_folder_path = $is_admin_path ? "../" : "";
			$error_message = "";
			if (file_exists($root_folder_path . $status_php_lib)) {
				if (!$order_step) { $order_step = "status"; }
				// get payment data
				$post_parameters = ""; $payment_parameters = array(); $pass_parameters = array(); $pass_data = array(); $variables = array();
				get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_params, $pass_data, $variables, $order_step);

				include_once($root_folder_path . $status_php_lib);
			} else {
				$error_message = APPROPRIATE_LIBRARY_ERROR_MSG .": " . $root_folder_path . $status_php_lib;
			}
			if (strlen($error_message)) {
				$is_valid = false;
				$status_error = str_replace("{order_id}", $order_id, STATUS_CANT_BE_UPDATED_MSG) . $error_message;
			}
		}

		$items_statuses = array(); $items_paid = array(); $items_stock_actions = array(); $other_statuses = false; $order_items_ids = "";
		// check information if any of order items has a different status 
		if (strlen($order_items)) {
			$sql  = " SELECT oi.order_item_id, oi.item_name, ";
			$sql .= " os.status_id, os.status_name, os.paid_status, os.download_activation, os.stock_level_action ";
			$sql .= " FROM (" . $table_prefix . "orders_items oi ";
			$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON os.status_id=oi.item_status) ";
			$sql .= " WHERE oi.order_item_id IN (" . $db->tosql($order_items, INTEGERS_LIST) . ")" ;
			$db->query($sql);
			while ($db->next_record()) {
				$order_item_id = $db->f("order_item_id");
				$item_name = get_translation($db->f("item_name"));
				$cur_item_status = $db->f("status_id");
				$item_status_name = get_translation($db->f("status_name"));
				$item_paid_status = $db->f("paid_status");
				$items_paid[$order_item_id] = $item_paid_status;
				$current_stock_action = $db->f("stock_level_action");
				if (!$current_stock_action) {
					$current_stock_action = -1;
				}
				$items_stock_actions[$order_item_id] = $current_stock_action;
				$new_item_status = get_param("item_status_" . $order_item_id);
				if (!strlen($new_item_status)) { 
					// if there is no status parameter for order item use global order status
					$new_item_status = $status_id;
				}
				if ($new_item_status != $status_id || $cur_item_status == $new_item_status) {
					$other_statuses = true;
				}
				// check items with updated statuses
				if ($cur_item_status != $new_item_status) {
					if ($order_items_ids) { $order_items_ids .= ","; }
					$order_items_ids .= $order_item_id;
					$items_statuses[$new_item_status][] = array($order_item_id, $item_name, $item_status_name);
				}
			}
		}

		if ($is_valid) {
			$r = new VA_Record($table_prefix . "orders_events");
			$r->add_textbox("order_id", INTEGER);
			$r->add_textbox("status_id", INTEGER);
			$r->add_textbox("admin_id", INTEGER);
			$r->add_textbox("order_items", TEXT);
			$r->add_textbox("event_date", DATETIME);
			$r->add_textbox("event_type", TEXT);
			$r->add_textbox("event_name", TEXT);
			$r->add_textbox("event_description", TEXT);
			$r->set_value("order_id", $order_id);
			$r->set_value("admin_id", get_session("session_admin_id"));
			$r->set_value("event_date", va_time());

			if ($current_status_id != $status_id) {
				// update status
				$is_update = true;
				$sql  = " UPDATE " . $table_prefix . "orders ";
				$sql .= " SET order_status=" . $db->tosql($status_id, INTEGER);
				$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
				$db->query($sql);

				if (!$other_statuses) {
					// update items status
					$sql  = " UPDATE " . $table_prefix . "orders_items ";
					$sql .= " SET item_status=" . $db->tosql($status_id, INTEGER);
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$db->query($sql);

					if ($download_activation == 1) {
						$sql = "UPDATE " . $table_prefix . "items_downloads SET activated=1 WHERE order_id=" . $db->tosql($order_id, INTEGER);
						$db->query($sql);
						$sql = "UPDATE " . $table_prefix . "orders_items_serials SET activated=1 WHERE order_id=" . $db->tosql($order_id, INTEGER);
						$db->query($sql);
						$sql = "UPDATE " . $table_prefix . "coupons SET is_active=1 WHERE order_id=" . $db->tosql($order_id, INTEGER);
						$db->query($sql);
					} elseif ($download_activation == 0) {
						$sql = "UPDATE " . $table_prefix . "items_downloads SET activated=0 WHERE order_id=" . $db->tosql($order_id, INTEGER);
						$db->query($sql);
						$sql = "UPDATE " . $table_prefix . "orders_items_serials SET activated=0 WHERE order_id=" . $db->tosql($order_id, INTEGER);
						$db->query($sql);
						$sql = "UPDATE " . $table_prefix . "coupons SET is_active=0 WHERE order_id=" . $db->tosql($order_id, INTEGER);
						$db->query($sql);
					}
				}

				if ($order_event) {
					// save event with updated status
					$r->set_value("status_id", $status_id);
					$r->set_value("event_type", "update_order_status"); //"Update status"
					if ($current_status_id > 0) {
						$r->set_value("event_name", $current_status . " &ndash;&gt; " . $status_name);
					} else {
						// new order added
						$r->set_value("event_name", $status_name . " (" . $payment_name . ")");
					}
					$r->insert_record();
				}

				if ($mail_notify || $sms_notify
					|| $merchant_notify || $merchant_sms_notify
					|| $supplier_notify || $supplier_sms_notify
					|| $admin_notify || $admin_sms_notify
					|| $links_notify || $serials_notify || $vouchers_notify) {

					// get the full information about order and prepare basket variable

					if ($is_admin_path) {
						$user_template_path = $settings["templates_dir"];
						if (preg_match("/^\.\//", $user_template_path)) {
							$user_template_path = str_replace("./", "../", $user_template_path);
						} elseif (!preg_match("/^\//", $user_template_path)) {
							$user_template_path = "../" . $user_template_path;
						}
						$t->set_template_path($user_template_path);
					}
					$t->set_file("basket_html", "email_basket.html");
					$basket = show_order_items($order_id, true, "");
					$t->parse("basket_html", false);

					$t->set_file("basket_text", "email_basket.txt");
					show_order_items($order_id, true, "");
					$t->parse("basket_text", false);
					if ($is_admin_path) {
						$t->set_template_path($settings["admin_templates_dir"]);
					}

					$company_select = get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($order_info["company_id"], INTEGER, true, false)));
					$delivery_company_select = get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($order_info["delivery_company_id"], INTEGER, true, false)));
					$state = get_translation(get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($order_info["state_id"], INTEGER, true, false)));
					$delivery_state = get_translation(get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($order_info["delivery_state_id"], INTEGER, true, false)));
					$country = get_translation(get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($order_info["country_id"], INTEGER, true, false)));
					$delivery_country = get_translation(get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($order_info["delivery_country_id"], INTEGER, true, false)));

					$t->set_var("basket", $basket);
					$t->set_var("company_select", $company_select);
					$t->set_var("state", $state);
					$t->set_var("country", $country);
					$t->set_var("delivery_company_select", $delivery_company_select);
					$t->set_var("delivery_state", $delivery_state);
					$t->set_var("delivery_country", $delivery_country);

					// check for merchants products
					$merchants = array();
					if ($merchant_notify || $merchant_sms_notify) {
						for ($ci = 0; $ci < sizeof($cart_items); $ci++) {
							$cart_item = $cart_items[$ci];
							$merchant_id = $cart_item["merchant_id"];
							if ($merchant_id) {
								$item_text = $cart_item["item_title"];
								if ($cart_item["item_properties_text"]) {
									$item_text .= " (" . $cart_item["item_properties_text"] . ")";
								}
								$item_text .= " " . PROD_QTY_COLUMN . ": " . $cart_item["quantity"] . " " . currency_format($cart_item["item_total"], $order_currency);

								if (isset($merchants[$merchant_id])) {
									$merchants[$merchant_id]["merchant_items_text"] .= $eol . $item_text;
									$merchants[$merchant_id]["merchant_items_html"] .= "<br>" . $eol . $item_text;
								} else {
									$merchants[$merchant_id] = array(
										"merchant_id" => $cart_item["merchant_id"],
										"merchant_email" => $cart_item["merchant_email"], "merchant_name" => $cart_item["merchant_name"],
										"merchant_first_name" => $cart_item["merchant_first_name"], "merchant_last_name" => $cart_item["merchant_last_name"],
										"merchant_cell_phone" => $cart_item["merchant_cell_phone"],
										"merchant_items_text" => $item_text, "merchant_items_html" => $item_text,
									);
								}
							}
						}
					} // end check merchants products

					// check for suppliers products
					$suppliers = array();
					if ($supplier_notify || $supplier_sms_notify) {
						for ($ci = 0; $ci < sizeof($cart_items); $ci++) {
							$cart_item = $cart_items[$ci];
							$supplier_id = $cart_item["supplier_id"];
							if ($supplier_id) {
								$item_text = $cart_item["item_title"];
								if ($cart_item["item_properties_text"]) {
									$item_text .= " (" . $cart_item["item_properties_text"] . ")";
								}
								$item_text .= " " . PROD_QTY_COLUMN . ": " . $cart_item["quantity"];

								if (isset($suppliers[$supplier_id])) {
									$suppliers[$supplier_id]["supplier_items_text"] .= $eol . $item_text;
									$suppliers[$supplier_id]["supplier_items_html"] .= "<br>" . $eol . $item_text;
								} else {
									$suppliers[$supplier_id] = array(
										"supplier_id" => $cart_item["supplier_id"],
										"supplier_email" => $cart_item["supplier_email"], "supplier_name" => $cart_item["supplier_name"],
										"supplier_short_desc" => $cart_item["supplier_short_desc"], "supplier_full_desc" => $cart_item["supplier_full_desc"],
										"supplier_cell_phone" => $cart_item["supplier_cell_phone"],
										"supplier_items_text" => $item_text, "supplier_items_html" => $item_text,
									);
								}
							}
						}
					} // end check suppliers products
				}

				// customer notification
				if ($mail_notify) {
					$t->set_block("mail_subject", $mail_subject);
					$t->set_block("mail_body", $mail_body);
					$mail_type_code = ($mail_type == 1) ? "html" : "text";

					// set basket
					$t->set_var("basket", $t->get_var("basket_" . $mail_type_code));
					// set download links
					$t->set_var("links", $links[$mail_type_code]);
					// set serial numbers
					$t->set_var("serials", $order_serials[$mail_type_code]);
					$t->set_var("serial_numbers", $order_serials[$mail_type_code]);
					// set serial numbers
					$t->set_var("vouchers", $order_vouchers[$mail_type_code]);
					$t->set_var("gift_vouchers", $order_vouchers[$mail_type_code]);

					$t->parse("mail_subject", false);
					$t->parse("mail_body", false);
					$mail_body = str_replace("\r", "", $t->get_var("mail_body"));
					$notify_sent = va_mail($user_mail, $t->get_var("mail_subject"), $mail_body, $email_headers);
					if ($notify_sent) {
						$r->set_value("event_date", va_time());
						$r->set_value("event_type", "status_notification_sent"); //"Email notification sent"
						$r->set_value("event_name", $t->get_var("mail_subject"));
						$r->set_value("event_description", $mail_body);
						$r->insert_record();
					}
				}

				if ($sms_notify) {
					if (!$sms_recipient) { $sms_recipient = $order_info["cell_phone"]; }
					$t->set_block("sms_recipient", $sms_recipient);
					$t->set_block("sms_originator", $sms_originator);
					$t->set_block("sms_message", $sms_message);

					// set download links
					$t->set_var("links", $links["text"]);
					// set serial numbers
					$t->set_var("serials", $order_serials["text"]);
					$t->set_var("serial_numbers", $order_serials["text"]);
					// set serial numbers
					$t->set_var("vouchers", $order_vouchers["text"]);
					$t->set_var("gift_vouchers", $order_vouchers["text"]);

					$t->parse("sms_recipient", false);
					$t->parse("sms_originator", false);
					$t->parse("sms_message", false);

					if (sms_send_allowed($t->get_var("sms_recipient"))) {
						$sms_sent = sms_send($t->get_var("sms_recipient"), $t->get_var("sms_message"), $t->get_var("sms_originator"));
					} else {
						$sms_sent = false;
					}
					if ($sms_sent) {
						$event_description = $t->get_var("sms_message");

						$r->set_value("event_date", va_time());
						$r->set_value("event_type", "status_sms_sent"); //"SMS notification sent");
						$r->set_value("event_name", $t->get_var("sms_recipient"));
						$r->set_value("event_description", $event_description);
						$r->insert_record();
					}
				}
				// end user notification

				// merchant and supplier notification
				// don't send information about links, serials and vouchers for merchants as it has the whole order information
				$t->set_var("links",   "");
				$t->set_var("serials", "");
				$t->set_var("serial_numbers", "");
				$t->set_var("vouchers", "");
				$t->set_var("gift_vouchers", "");

				// start merchant notifications
				if ($merchant_notify) {
					// set email templates
					$t->set_block("mail_subject", $merchant_subject);
					$t->set_block("mail_body", $merchant_body);
					foreach ($merchants as $merchant_id => $merchant_info) {
						$t->set_vars($merchant_info);

						if ($merchant_to) {
							$merchant_mail = $merchant_to; 
						} else {
							$merchant_mail = $merchant_info["merchant_email"]; 
						}
						$merchant_type_code = ($merchant_mail_type == 1) ? "html" : "text";

						// set basket
						$t->set_var("basket", "");
						// set merchant items
						$t->set_var("merchant_items", $merchant_info["merchant_items_" . $merchant_type_code]);

						$t->parse("mail_subject", false);
						$t->parse("mail_body", false);
						$mail_body = str_replace("\r", "", $t->get_var("mail_body"));
						$notify_sent = va_mail($merchant_mail, $t->get_var("mail_subject"), $mail_body, $merchant_headers);
						if ($notify_sent) {
							$r->set_value("event_date", va_time());
							$r->set_value("event_type", "status_merchant_email_sent"); //"Merchant email notification sent"
							$r->set_value("event_name", $t->get_var("mail_subject"));
							$r->set_value("event_description", $mail_body);
							$r->insert_record();
						}
					}
				}

				if ($merchant_sms_notify) {
					foreach ($merchants as $merchant_id => $merchant_info) {
						$t->set_vars($merchant_info);
						if ($merchant_sms_recipient) { 
							$sms_recipient = $merchant_sms_recipient; 
						} else {
							$sms_recipient = $merchant_info["merchant_cell_phone"]; 
						}
						$t->set_block("sms_recipient", $sms_recipient);
						$t->set_block("sms_originator", $merchant_sms_originator);
						$t->set_block("sms_message", $merchant_sms_message);

						// set basket
						$t->set_var("basket", "");
						// set merchant items
						$t->set_var("merchant_items", $merchant_info["merchant_items_text"]);

						$t->parse("sms_recipient", false);
						$t->parse("sms_originator", false);
						$t->parse("sms_message", false);

						if (sms_send_allowed($t->get_var("sms_recipient"))) {
							$merchant_sms_sent = sms_send($t->get_var("sms_recipient"), $t->get_var("sms_message"), $t->get_var("sms_originator"));
						} else {
							$merchant_sms_sent = false;
						}
						if ($merchant_sms_sent) {
							$event_description = $t->get_var("sms_message");

							$r->set_value("event_date", va_time());
							$r->set_value("event_type", "status_merchant_sms_sent"); 
							$r->set_value("event_name", $t->get_var("sms_recipient"));
							$r->set_value("event_description", $event_description);
							$r->insert_record();
						}
					}
				}
				// end merchant notifications

				// start supplier notifications
				if ($supplier_notify) {
					// set email templates
					$t->set_block("mail_subject", $supplier_subject);
					$t->set_block("mail_body", $supplier_body);
					foreach ($suppliers as $supplier_id => $supplier_info) {
						$t->set_vars($supplier_info);

						if ($supplier_to) {
							$supplier_mail = $supplier_to; 
						} else {
							$supplier_mail = $supplier_info["supplier_email"]; 
						}
						$supplier_type_code = ($supplier_mail_type == 1) ? "html" : "text";

						// set basket
						$t->set_var("basket", "");
						// set supplier items
						$t->set_var("supplier_items", $supplier_info["supplier_items_" . $supplier_type_code]);

						$t->parse("mail_subject", false);
						$t->parse("mail_body", false);
						$mail_body = str_replace("\r", "", $t->get_var("mail_body"));
						$notify_sent = va_mail($supplier_mail, $t->get_var("mail_subject"), $mail_body, $supplier_headers);
						if ($notify_sent) {
							$r->set_value("event_date", va_time());
							$r->set_value("event_type", "status_supplier_email_sent"); //"supplier email notification sent"
							$r->set_value("event_name", $t->get_var("mail_subject"));
							$r->set_value("event_description", $mail_body);
							$r->insert_record();
						}
					}
				}

				if ($supplier_sms_notify) {
					foreach ($suppliers as $supplier_id => $supplier_info) {
						$t->set_vars($supplier_info);
						if ($supplier_sms_recipient) { 
							$sms_recipient = $supplier_sms_recipient; 
						} else {
							$sms_recipient = $supplier_info["supplier_cell_phone"]; 
						}
						$t->set_block("sms_recipient", $sms_recipient);
						$t->set_block("sms_originator", $supplier_sms_originator);
						$t->set_block("sms_message", $supplier_sms_message);

						// set basket
						$t->set_var("basket", "");
						// set supplier items
						$t->set_var("supplier_items", $supplier_info["supplier_items_text"]);

						$t->parse("sms_recipient", false);
						$t->parse("sms_originator", false);
						$t->parse("sms_message", false);

						if (sms_send_allowed($t->get_var("sms_recipient"))) {
							$supplier_sms_sent = sms_send($t->get_var("sms_recipient"), $t->get_var("sms_message"), $t->get_var("sms_originator"));
						} else {
							$supplier_sms_sent = false;
						}
						if ($supplier_sms_sent) {
							$event_description = $t->get_var("sms_message");

							$r->set_value("event_date", va_time());
							$r->set_value("event_type", "status_supplier_sms_sent"); 
							$r->set_value("event_name", $t->get_var("sms_recipient"));
							$r->set_value("event_description", $event_description);
							$r->insert_record();
						}
					}
				}
				// end supplier notifications

				// admin notification
				if ($admin_notify) {
					if (!$admin_to) {$admin_to = $settings["admin_email"]; } // TODO for testing purposes only
					$t->set_block("mail_subject", $admin_subject);
					$t->set_block("mail_body", $admin_body);
					$admin_type_code = ($admin_mail_type == 1) ? "html" : "text";

					// set basket
					$t->set_var("basket", $t->get_var("basket_" . $admin_type_code));
					// set download links
					$t->set_var("links", $links[$admin_type_code]);
					// set serial numbers
					$t->set_var("serials", $order_serials[$admin_type_code]);
					$t->set_var("serial_numbers", $order_serials[$admin_type_code]);
					// set serial numbers
					$t->set_var("vouchers", $order_vouchers[$admin_type_code]);
					$t->set_var("gift_vouchers", $order_vouchers[$admin_type_code]);

					$t->parse("mail_subject", false);
					$t->parse("mail_body", false);
					$admin_body = str_replace("\r", "", $t->get_var("mail_body"));
					$notify_sent = va_mail($admin_to, $t->get_var("mail_subject"), $admin_body, $admin_headers);
					if ($notify_sent) {
						$r->set_value("event_date", va_time());
						$r->set_value("event_type", "status_admin_email_sent"); 
						$r->set_value("event_name", $t->get_var("mail_subject"));
						$r->set_value("event_description", $admin_body);
						$r->insert_record();
					}
				}

				if ($admin_sms_notify) {
					if (!$admin_sms_recipient) { $admin_sms_recipient = $order_info["cell_phone"]; }
					$t->set_block("sms_recipient", $admin_sms_recipient);
					$t->set_block("sms_originator", $admin_sms_originator);
					$t->set_block("sms_message", $admin_sms_message);

					// set basket
					$t->set_var("basket", $basket);
					// set download links
					$t->set_var("links",    $links["text"]);
					// set serial numbers
					$t->set_var("serials", $order_serials["text"]);
					$t->set_var("serial_numbers", $order_serials["text"]);
					// set serial numbers
					$t->set_var("vouchers", $order_vouchers["text"]);
					$t->set_var("gift_vouchers", $order_vouchers["text"]);

					$t->parse("sms_recipient", false);
					$t->parse("sms_originator", false);
					$t->parse("sms_message", false);

					if (sms_send_allowed($t->get_var("sms_recipient"))) {
						$admin_sms_sent = sms_send($t->get_var("sms_recipient"), $t->get_var("sms_message"), $t->get_var("sms_originator"));
					} else {
						$admin_sms_sent = false;
					}
					if ($admin_sms_sent) {
						$event_description = $t->get_var("sms_message");

						$r->set_value("event_date", va_time());
						$r->set_value("event_type", "status_admin_sms_sent"); 
						$r->set_value("event_name", $t->get_var("sms_recipient"));
						$r->set_value("event_description", $event_description);
						$r->insert_record();
					}
				}
				// end admin notifications

				if ($links_notify || $serials_notify || $vouchers_notify) {
					// prepare download info settings
					$download_info = array();
					$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='download_info'";
					$db->query($sql);
					while ($db->next_record()) {
						$download_info[$db->f("setting_name")] = $db->f("setting_value");
					}
				}

				if ($links_notify) {
					$email_headers = array();
					$email_headers["from"] = get_setting_value($download_info, "links_from", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($download_info, "links_cc");
					$email_headers["bcc"] = get_setting_value($download_info, "links_bcc");
					$email_headers["reply_to"] = get_setting_value($download_info, "links_reply_to");
					$email_headers["return_path"] = get_setting_value($download_info, "links_return_path");
					$mail_type = get_setting_value($download_info, "links_message_type", 0);
					$email_headers["mail_type"] = $mail_type;
					$mail_type_code = ($mail_type == 1) ? "html" : "text";

					// set basket
					$t->set_var("basket", $t->get_var("basket_" . $mail_type_code));
					// set download links
					$t->set_var("links", $links[$mail_type_code]);
					// set serial numbers
					$t->set_var("serials", $order_serials[$mail_type_code]);
					$t->set_var("serial_numbers", $order_serials[$mail_type_code]);
					// set serial numbers
					$t->set_var("vouchers", $order_vouchers[$mail_type_code]);
					$t->set_var("gift_vouchers", $order_vouchers[$mail_type_code]);

					$mail_subject = get_setting_value($download_info, "links_subject", LINKS_FOR_ORDER_MSG . $order_id);
					$mail_body = get_setting_value($download_info, "links_message", $links[$mail_type_code]);

					$t->set_block("mail_subject", $mail_subject);
					$t->set_block("mail_body", $mail_body);
					$t->parse("mail_subject", false);
					$t->parse("mail_body", false);
					$mail_body = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("mail_body"));
					$notify_sent = va_mail($user_mail, $t->get_var("mail_subject"), $mail_body, $email_headers);
					if ($notify_sent) {
						$r->set_value("event_date", va_time());
						$r->set_value("event_type", "links_sent");
						$r->set_value("event_name", $t->get_var("mail_subject"));
						$r->set_value("event_description", $mail_body);
						$r->insert_record();
					}
				}

				if ($serials_notify) {
					$email_headers = array();
					$email_headers["from"] = get_setting_value($download_info, "serials_from", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($download_info, "serials_cc");
					$email_headers["bcc"] = get_setting_value($download_info, "serials_bcc");
					$email_headers["reply_to"] = get_setting_value($download_info, "serials_reply_to");
					$email_headers["return_path"] = get_setting_value($download_info, "serials_return_path");
					$mail_type = get_setting_value($download_info, "serials_message_type", 0);
					$email_headers["mail_type"] = $mail_type;
					$mail_type_code = ($mail_type == 1) ? "html" : "text";

					// set basket
					$t->set_var("basket", $t->get_var("basket_" . $mail_type_code));
					// set download links
					$t->set_var("links", $links[$mail_type_code]);
					// set serial numbers
					$t->set_var("serials", $order_serials[$mail_type_code]);
					$t->set_var("serial_numbers", $order_serials[$mail_type_code]);
					// set serial numbers
					$t->set_var("vouchers", $order_vouchers[$mail_type_code]);
					$t->set_var("gift_vouchers", $order_vouchers[$mail_type_code]);

					$mail_subject = get_translation(get_setting_value($download_info, "serials_subject", SERIAL_NUMBERS_FOR_ORDER_MSG . $order_id));
					$mail_body = get_translation(get_setting_value($download_info, "serials_message", $order_serials[$mail_type_code]));

					$t->set_block("mail_subject", $mail_subject);
					$t->set_block("mail_body", $mail_body);
					$t->parse("mail_subject", false);
					$t->parse("mail_body", false);
					$mail_body = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("mail_body"));
					$notify_sent = va_mail($user_mail, $t->get_var("mail_subject"), $mail_body, $email_headers);
					if ($notify_sent) {
						$r->set_value("event_date", va_time());
						$r->set_value("event_type", "serials_sent");
						$r->set_value("event_name", $t->get_var("mail_subject"));
						$r->set_value("event_description", $mail_body);
						$r->insert_record();
					}
				}

				if ($vouchers_notify) {
					$email_headers = array();
					$email_headers["from"] = get_setting_value($download_info, "vouchers_from", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($download_info, "vouchers_cc");
					$email_headers["bcc"] = get_setting_value($download_info, "vouchers_bcc");
					$email_headers["reply_to"] = get_setting_value($download_info, "vouchers_reply_to");
					$email_headers["return_path"] = get_setting_value($download_info, "vouchers_return_path");
					$mail_type = get_setting_value($download_info, "vouchers_message_type", 0);
					$email_headers["mail_type"] = $mail_type;
					$mail_type_code = ($mail_type == 1) ? "html" : "text";

					// set basket
					$t->set_var("basket", $t->get_var("basket_" . $mail_type_code));
					// set download links
					$t->set_var("links", $links[$mail_type_code]);
					// set serial numbers
					$t->set_var("serials", $order_serials[$mail_type_code]);
					$t->set_var("serial_numbers", $order_serials[$mail_type_code]);
					// set serial numbers
					$t->set_var("vouchers", $order_vouchers[$mail_type_code]);
					$t->set_var("gift_vouchers", $order_vouchers[$mail_type_code]);

					$mail_subject = get_setting_value($download_info, "vouchers_subject", GIFT_VOUCHERS_FOR_ORDERS_MSG . $order_id);
					$mail_body = get_setting_value($download_info, "vouchers_message", $order_vouchers[$mail_type_code]);

					$t->set_block("mail_subject", $mail_subject);
					$t->set_block("mail_body", $mail_body);
					$t->parse("mail_subject", false);
					$t->parse("mail_body", false);
					$mail_body = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("mail_body"));
					$notify_sent = va_mail($user_mail, $t->get_var("mail_subject"), $mail_body, $email_headers);
					if ($notify_sent) {
						$r->set_value("event_date", va_time());
						$r->set_value("event_type", "vouchers_sent");
						$r->set_value("event_name", $t->get_var("mail_subject"));
						$r->set_value("event_description", $mail_body);
						$r->insert_record();
					}
				}
			}

			if ($other_statuses && sizeof($items_statuses) > 0) {
				$is_update = true;

				foreach ($items_statuses as $new_item_status => $items) {
					$items_ids = ""; $items_names = ""; $old_status_name = "";
					for ($i = 0; $i < sizeof($items); $i++) {
						list($order_item_id, $item_name, $item_status_name) = $items[$i];
						if (strlen($items_ids)) {
							$items_ids .= ",";
						}
						$items_ids .= $order_item_id;
						$items_names .= "<br>" . $item_name;
						$old_status_name = $item_status_name;
					}

					// update items statuses
					$sql  = " UPDATE " . $table_prefix . "orders_items ";
					$sql .= " SET item_status=" . $db->tosql($new_item_status, INTEGER);
					$sql .= " WHERE order_item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
					$db->query($sql);

					$sql  = " SELECT status_name, download_activation FROM " . $table_prefix . "order_statuses ";
					$sql .= " WHERE status_id=" . $db->tosql($new_item_status, INTEGER);
					$db->query($sql);
					if ($db->next_record()) {
						$new_status_name = get_translation($db->f("status_name"));
						$item_activation = $db->f("download_activation");
					}

					if ($item_activation == 1) {
						$sql = "UPDATE " . $table_prefix . "items_downloads SET activated=1 WHERE order_item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
						$db->query($sql);
						$sql = "UPDATE " . $table_prefix . "orders_items_serials SET activated=1 WHERE order_item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
						$db->query($sql);
						$sql = "UPDATE " . $table_prefix . "coupons SET is_active=1 WHERE order_item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
						$db->query($sql);
					} elseif ($item_activation == 0) {
						$sql = "UPDATE " . $table_prefix . "items_downloads SET activated=0 WHERE order_item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
						$db->query($sql);
						$sql = "UPDATE " . $table_prefix . "orders_items_serials SET activated=0 WHERE order_item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
						$db->query($sql);
						$sql = "UPDATE " . $table_prefix . "coupons SET is_active=0 WHERE order_item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
						$db->query($sql);
					}

					$r->set_value("status_id", $new_item_status);
					$r->set_value("order_items", $items_ids);
					$r->set_value("event_type", "update_items_status");
					$r->set_value("event_name", $old_status_name . " &ndash;&gt; " . $new_status_name);
					$r->set_value("event_description", $items_names);
					$r->insert_record();
				}

			}

			// update credit amount
			if ($current_status_id != $status_id && $credit_amount > 0) {
				$cdt = new VA_Record($table_prefix . "users_credits");
				$cdt->add_textbox("user_id", INTEGER);
				$cdt->add_textbox("order_id", INTEGER);
				$cdt->add_textbox("order_item_id", INTEGER);
				$cdt->add_textbox("credit_amount", NUMBER);
				$cdt->add_textbox("credit_action", INTEGER);
				$cdt->add_textbox("credit_type", INTEGER);
				$cdt->add_textbox("date_added", DATETIME);

				// subtract or return credit amount from credit balance
				$cdt->set_value("user_id", $order_user_id);
				$cdt->set_value("order_id", $order_id);
				$cdt->set_value("order_item_id", 0);
				$cdt->set_value("credit_amount", $credit_amount);
				$cdt->set_value("credit_type", 1);
				$cdt->set_value("date_added", va_time());

				$credit_user = false;
				$sql  = " SELECT SUM(credit_action) FROM " . $table_prefix . "users_credits ";
				$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
				$sum_credit_action = get_db_value($sql);
				if ($credit_action == 1 && $sum_credit_action == -1) { // return points to account
					$credit_user = true;
					$cdt->set_value("credit_action", 1);
					$cdt->insert_record();
				} elseif ($credit_action == -1 && $sum_credit_action != -1) { // subtract points from account
					$credit_user = true;
					$cdt->set_value("credit_action", -1);
					$cdt->insert_record();
				}

				// update credit balance field in users table
				if ($credit_user) {
					$sql  = " SELECT SUM(credit_action * credit_amount) ";
					$sql .= " FROM " . $table_prefix . "users_credits ";
					$sql .= " WHERE user_id=" . $db->tosql($order_user_id, INTEGER);
					$total_credit_sum = get_db_value($sql);

					$sql  = " UPDATE " . $table_prefix . "users ";
					$sql .= " SET credit_balance=" . $db->tosql($total_credit_sum, NUMBER);
					$sql .= " WHERE user_id=" . $db->tosql($order_user_id, INTEGER);
					$db->query($sql);

					// update user information in session if available
					$user_info = get_session("session_user_info");
					$session_user_id = get_setting_value($user_info, "user_id", 0);
					$session_credit_balance = get_setting_value($user_info, "credit_balance", 0);
					if ($session_user_id == $order_user_id && $total_credit_sum != $session_credit_balance) {
						$user_info["credit_balance"] = $total_credit_sum;
						set_session("session_user_info", $user_info);
					}
				}
			}

			// check product notification, commissions and subscriptions
			if ($order_items_ids || (!$other_statuses && $current_status_id != $status_id)) {
				$events = array();
				$commissions_points = array();
				$parent_items = array();
				$subscriptions = array();
				$items_stock_levels = array();

				$uc = new VA_Record($table_prefix . "users_commissions");
				$uc->add_textbox("payment_id", INTEGER);
				$uc->add_textbox("user_id", INTEGER);
				$uc->add_textbox("order_id", INTEGER);
				$uc->add_textbox("order_item_id", INTEGER);
				$uc->add_textbox("commission_amount", NUMBER);
				$uc->add_textbox("commission_action", INTEGER);
				$uc->add_textbox("commission_type", INTEGER);
				$uc->add_textbox("date_added", DATETIME);

				$uc->set_value("payment_id", 0);
				$uc->set_value("order_id", $order_id);
				$uc->set_value("date_added", va_time());

				$pts = new VA_Record($table_prefix . "users_points");
				$pts->add_textbox("user_id", INTEGER);
				$pts->add_textbox("order_id", INTEGER);
				$pts->add_textbox("order_item_id", INTEGER);
				$pts->add_textbox("points_amount", NUMBER);
				$pts->add_textbox("points_action", INTEGER);
				$pts->add_textbox("points_type", INTEGER);
				$pts->add_textbox("date_added", DATETIME);

				$pts->set_value("order_id", $order_id);
				$pts->set_value("date_added", va_time());

				$cdt = new VA_Record($table_prefix . "users_credits");
				$cdt->add_textbox("user_id", INTEGER);
				$cdt->add_textbox("order_id", INTEGER);
				$cdt->add_textbox("order_item_id", INTEGER);
				$cdt->add_textbox("credit_amount", NUMBER);
				$cdt->add_textbox("credit_action", INTEGER);
				$cdt->add_textbox("credit_type", INTEGER);
				$cdt->add_textbox("date_added", DATETIME);

				$cdt->set_value("order_id", $order_id);
				$cdt->set_value("date_added", va_time());

				$sql  = " SELECT oi.order_item_id, oi.parent_order_item_id, oi.cart_item_id, ";
				$sql .= " os.status_id, os.status_name, os.paid_status, os.item_notify, os.stock_level_action, ";
				$sql .= " os.commission_action, os.points_action, os.credit_action, ";
				$sql .= " oi.item_id, oi.item_name, oi.manufacturer_code, oi.price, oi.quantity, ";
				$sql .= " i.stock_level, i.use_stock_level, i.short_description, i.full_description, ";
				$sql .= " oi.item_user_id, oi.merchant_commission, oi.affiliate_commission, oi.reward_points, oi.reward_credits, oi.points_price, ";
				$sql .= " i.mail_notify, i.mail_to, i.mail_from, i.mail_subject, i.mail_cc, i.mail_bcc, i.mail_reply_to, i.mail_return_path, ";
				$sql .= " i.mail_type, i.mail_subject, i.mail_body, ";
				$sql .= " i.sms_notify, i.sms_recipient, i.sms_originator, i.sms_message, ";
				$sql .= " oi.user_id, oi.is_subscription, oi.is_account_subscription, ";
				$sql .= " oi.subscription_period, oi.subscription_interval, oi.subscription_suspend, ";
				$sql .= " oi.subscription_start_date, oi.subscription_expiry_date ";
				$sql .= " FROM ((" . $table_prefix . "orders_items oi ";
				$sql .= " LEFT JOIN " . $table_prefix . "items i ON i.item_id=oi.item_id) ";
				$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON os.status_id=oi.item_status) ";
				if ($other_statuses && $order_items_ids) {
					$sql .= " WHERE oi.order_item_id IN (" . $db->tosql($order_items_ids, INTEGERS_LIST) . ")" ;
				} else {
					$sql .= " WHERE oi.order_id=" . $db->tosql($order_id, INTEGER);
				}
				$db->query($sql);
				while ($db->next_record()) {

					$order_item_id = $db->f("order_item_id");
					$parent_order_item_id = $db->f("parent_order_item_id");
					$cart_item_id = $db->f("cart_item_id");
					$new_status_id = $db->f("status_id");
					$new_item_paid = $db->f("paid_status");
					$stock_level = $db->f("stock_level");
					$use_stock_level = $db->f("use_stock_level");
					$new_stock_action = $db->f("stock_level_action");
					if (!$new_stock_action) {
						$new_stock_action = -1;
					}
					$item_user_id = $db->f("item_user_id");
					$item_id = $db->f("item_id");
					$item_name = get_translation($db->f("item_name"));
					$manufacturer_code = $db->f("manufacturer_code");
					$price = $db->f("price");
					$merchant_commission = $db->f("merchant_commission");
					$affiliate_commission = $db->f("affiliate_commission");
					$reward_points = $db->f("reward_points");
					$reward_credits = $db->f("reward_credits");
					$points_price = $db->f("points_price");
					$quantity = $db->f("quantity");
					$short_description = $db->f("short_description");
					$full_description = $db->f("full_description");
					$item_notify = $db->f("item_notify");

					$item_commission_action = $db->f("commission_action");
					$item_points_action = $db->f("points_action");
					$item_credit_action = $db->f("credit_action");

					$user_id = $db->f("user_id");
					$is_subscription = $db->f("is_subscription");
					$is_account_subscription = $db->f("is_account_subscription");
					$subscription_period = $db->f("subscription_period");
					$subscription_interval = $db->f("subscription_interval");
					$subscription_suspend = $db->f("subscription_suspend");
					$subscription_start_date = $db->f("subscription_start_date", DATETIME);
					$subscription_expiry_date = $db->f("subscription_expiry_date", DATETIME);

					if ($is_subscription) {
						$old_item_paid = isset($items_paid[$order_item_id]) ? $items_paid[$order_item_id] : $current_paid_status;
						if (($old_item_paid || $new_item_paid) && $old_item_paid != $new_item_paid) {
							$subscriptions[$order_item_id] = array(
								"is_account_subscription" => $is_account_subscription, "user_id" => $user_id, "paid" => $new_item_paid, "period" => $subscription_period, 
								"interval" => $subscription_interval, "suspend" => $subscription_suspend,
								"start_date" => $subscription_start_date, "expiry_date" => $subscription_expiry_date,
							);
						}
					}

					if ($parent_order_item_id) {
						$old_item_paid = isset($items_paid[$order_item_id]) ? $items_paid[$order_item_id] : $current_paid_status;
						if (($old_item_paid || $new_item_paid) && $old_item_paid != $new_item_paid) {
							$parent_items[] = array($parent_order_item_id, $new_item_paid);
						}
					}

					// check if stock action was changed
					if (isset($items_stock_actions[$order_item_id])) {
						$old_stock_action = $items_stock_actions[$order_item_id];
					} else {
						$old_stock_action = $current_stock_action;
					}
					if ($new_stock_action != $old_stock_action) {
						$items_stock_levels[$order_item_id] = array(
							"item_id" => $item_id, "quantity" => $quantity, "stock_action" => $new_stock_action, 
							"stock_level" => $stock_level, "use_stock_level" => $use_stock_level, "cart_item_id" => $cart_item_id,
						);
					}

					if ($item_notify == 1) {
						$email_headers = array();
						$mail_notify = $db->f("mail_notify");
						$mail_to = $db->f("mail_to");
						if (!strlen($mail_to)) { $mail_to = $user_mail; }
						$mail_from = $db->f("mail_from");
						if (!strlen($mail_from)) { $mail_from = $settings["admin_email"]; }
						$email_headers["from"] = $mail_from;
						$email_headers["cc"] = $db->f("mail_cc");
						$email_headers["bcc"] = $db->f("mail_bcc");
						$email_headers["reply_to"] = $db->f("mail_reply_to");
						$email_headers["return_path"] = $db->f("mail_return_path");
						$mail_type = $db->f("mail_type");
						$email_headers["mail_type"] = $mail_type;
						$mail_subject = $db->f("mail_subject");
						$mail_body = $db->f("mail_body");

						// sms settings
						$sms_notify = $db->f("sms_notify");
						$sms_recipient = $db->f("sms_recipient");
						$sms_originator = $db->f("sms_originator");
						$sms_message = $db->f("sms_message");

						$t->set_var("item_name", $item_name);
						$t->set_var("item_title", $item_name);
						$t->set_var("product_title", $item_name);
						$t->set_var("product_name", $item_name);
						$t->set_var("product_code", $manufacturer_code);
						$t->set_var("price", $price);
						$t->set_var("quantity", $quantity);
						$t->set_var("product_quantity", $quantity);
						$t->set_var("short_description", $short_description);
						$t->set_var("full_description", $full_description);

						if ($mail_notify) {
							$t->set_block("mail_subject", $mail_subject);
							$t->set_block("mail_body", $mail_body);

							// set basket
							if ($mail_type) {
								$t->set_var("basket", $t->get_var("basket_html"));
							} else {
								$t->set_var("basket", $t->get_var("basket_text"));
							}

							// set download links
							if (!isset($links["html_" . $order_item_id])) {
								$t->set_var("links", "");
							} elseif ($mail_type) {
								$t->set_var("links", $links["html_" . $order_item_id]);
							} else {
								$t->set_var("links", $links["text_" . $order_item_id]);
							}
							// set serial numbers
							if (!isset($order_serials["html_" . $order_item_id])) {
								$t->set_var("serials", "");
								$t->set_var("serial_numbers", "");
							} elseif ($mail_type) {
								$t->set_var("serials", $order_serials["html_" . $order_item_id]);
								$t->set_var("serial_numbers", $order_serials["html_" . $order_item_id]);
							} else {
								$t->set_var("serials", $order_serials["text_" . $order_item_id]);
								$t->set_var("serial_numbers", $order_serials["text_" . $order_item_id]);
							}
							// set serial numbers
							if (!isset($order_vouchers["html_" . $order_item_id])) {
								$t->set_var("vouchers", "");
								$t->set_var("gift_vouchers", "");
							} elseif ($mail_type) {
								$t->set_var("vouchers", $order_vouchers["html_" . $order_item_id]);
								$t->set_var("gift_vouchers", $order_vouchers["html_" . $order_item_id]);
							} else {
								$t->set_var("vouchers", $order_vouchers["text_" . $order_item_id]);
								$t->set_var("gift_vouchers", $order_vouchers["text_" . $order_item_id]);
							}

							$t->parse("mail_subject", false);
							$t->parse("mail_body", false);

							$mail_subject = $t->get_var("mail_subject");
							$mail_body = str_replace("\r", "", $t->get_var("mail_body"));
							$notify_sent = va_mail($mail_to, $mail_subject, $mail_body, $email_headers);
							if ($notify_sent) {
								$event_name = $mail_subject;
								$event_description = $mail_body;
								$events[] = array($new_status_id, $order_item_id, va_time(), "product_notification_sent", $event_name, $event_description);
							}
						}
						if ($sms_notify) {
							if (!$sms_recipient) { $sms_recipient = $order_info["cell_phone"]; }
							$t->set_block("sms_recipient", $sms_recipient);
							$t->set_block("sms_originator", $sms_originator);
							$t->set_block("sms_message", $sms_message);

							// set basket
							$t->set_var("basket", $basket);

							// set download links
							if (!isset($links["html_" . $order_item_id])) {
								$t->set_var("links", "");
							} else {
								$t->set_var("links", $links["text_" . $order_item_id]);
							}
							// set serial numbers
							if (!isset($order_serials["html_" . $order_item_id])) {
								$t->set_var("serials", "");
								$t->set_var("serial_numbers", "");
							} else {
								$t->set_var("serials", $order_serials["text_" . $order_item_id]);
								$t->set_var("serial_numbers", $order_serials["text_" . $order_item_id]);
							}
							// set serial numbers
							if (!isset($order_vouchers["html_" . $order_item_id])) {
								$t->set_var("vouchers", "");
								$t->set_var("gift_vouchers", "");
							} else {
								$t->set_var("vouchers", $order_vouchers["text_" . $order_item_id]);
								$t->set_var("gift_vouchers", $order_vouchers["text_" . $order_item_id]);
							}

							$t->parse("sms_recipient", false);
							$t->parse("sms_originator", false);
							$t->parse("sms_message", false);

							$sms_message = $t->get_var("sms_message");

							if (sms_send_allowed($t->get_var("sms_recipient"))) {
								$sms_sent = sms_send($t->get_var("sms_recipient"), $sms_message, $t->get_var("sms_originator"));
							} else {
								$sms_sent = false;
							}
							if ($sms_sent) {
								$event_name = $t->get_var("sms_recipient");
								$event_description = $sms_message;
								$events[] = array($new_status_id, $order_item_id, va_time(), "product_sms_sent", $event_name, $event_description);
							}
						}
					}

					// save commisions, reward points and credits information
					if ($affiliate_commission > 0 || $merchant_commission > 0 || $reward_points > 0 || $reward_credits > 0 || $points_price > 0) {
						$commissions_points[$order_item_id] = array(
							"order_item_id" => $order_item_id,
							"order_user_id" => $order_user_id,
							"affiliate_user_id" => $affiliate_user_id,
							"item_user_id" => $item_user_id,
							"quantity" => $quantity,
							"affiliate_commission" => $affiliate_commission,
							"merchant_commission" => $merchant_commission,
							"reward_points" => $reward_points,
							"reward_credits" => $reward_credits,
							"points_price" => $points_price,
							"commission_action" => $item_commission_action,
							"points_action" => $item_points_action,
							"credit_action" => $item_credit_action,
						);
					}
				}
				// add shipping and properties points for order
				if ($points_action && ($shipping_points_amount + $properties_points_amount) > 0) {
					$commissions_points["order"] = array(
						"order_item_id" => 0,
						"order_user_id" => $order_user_id,
						"affiliate_user_id" => $affiliate_user_id,
						"item_user_id" => 0,
						"quantity" => 1,
						"affiliate_commission" => 0,
						"merchant_commission" => 0,
						"reward_points" => 0,
						"reward_credits" => 0,
						"points_price" => ($shipping_points_amount + $properties_points_amount),
						"commission_action" => $commission_action,
						"points_action" => $points_action,
						"credit_action" => $credit_action,
					);
				}

				// update payment plan date for recurring items
				$current_date = va_time();
				$current_ts = mktime (0, 0, 0, $current_date[MONTH], $current_date[DAY], $current_date[YEAR]);
				for ($i = 0; $i < sizeof($parent_items); $i++) {
					list($parent_order_item_id, $new_item_paid) = $parent_items[$i];
					$sql  = " SELECT oi.is_recurring, oi.recurring_period, oi.recurring_interval, ";
					$sql .= " oi.recurring_payments_total, oi.recurring_payments_made, oi.recurring_payments_failed, ";
					$sql .= " oi.recurring_end_date, oi.recurring_last_payment, oi.recurring_next_payment, oi.recurring_plan_payment ";
					$sql .= " FROM " . $table_prefix . "orders_items oi ";
					$sql .= " WHERE oi.order_item_id=" . $db->tosql($parent_order_item_id, INTEGER);
					$db->query($sql);
					if ($db->next_record()) {
						$is_recurring = $db->f("is_recurring");
						$recurring_period = $db->f("recurring_period");
						$recurring_interval = $db->f("recurring_interval");
						$recurring_payments_total = $db->f("recurring_payments_total");
						$recurring_payments_made = $db->f("recurring_payments_made");
						$recurring_payments_failed = $db->f("recurring_payments_failed");
						$recurring_end_date = $db->f("recurring_end_date", DATETIME);
						$recurring_last_payment = $db->f("recurring_last_payment", DATETIME);
						$recurring_next_payment = $db->f("recurring_next_payment", DATETIME);
						$recurring_plan_payment = $db->f("recurring_plan_payment", DATETIME);

						if ($is_recurring) {
							if ($new_item_paid) {
								$recurring_payments_made++;
								$recurring_payments_failed = 0;
								$recurring_last_ts = $current_ts;
							} else {
								$recurring_payments_made--;
								$recurring_interval = -$recurring_interval;
								$recurring_last_ts = 0;
							}

							$recurring_plan_ts = mktime (0, 0, 0, $recurring_plan_payment[MONTH], $recurring_plan_payment[DAY], $recurring_plan_payment[YEAR]);

							if ($recurring_period == 1) {
								$recurring_plan_ts = mktime (0, 0, 0, $recurring_plan_payment[MONTH], $recurring_plan_payment[DAY] + $recurring_interval, $recurring_plan_payment[YEAR]);
							} elseif ($recurring_period == 2) {
								$recurring_plan_ts = mktime (0, 0, 0, $recurring_plan_payment[MONTH], $current_date[DAY] + ($recurring_interval * 7), $recurring_plan_payment[YEAR]);
							} elseif ($recurring_period == 3) {
								$recurring_plan_ts = mktime (0, 0, 0, $recurring_plan_payment[MONTH] + $recurring_interval, $recurring_plan_payment[DAY], $recurring_plan_payment[YEAR]);
							} else {
								$recurring_plan_ts = mktime (0, 0, 0, $recurring_plan_payment[MONTH], $recurring_plan_payment[DAY], $recurring_plan_payment[YEAR] + $recurring_interval);
							}

							$recurring_end_ts = 0;
							if (is_array($recurring_end_date)) {
								$recurring_end_ts = mktime (0, 0, 0, $recurring_end_date[MONTH], $recurring_end_date[DAY], $recurring_end_date[YEAR]);
							}
							if (($recurring_payments_total && $recurring_payments_made >= $recurring_payments_total)
								|| ($recurring_end_ts && $recurring_end_ts < $recurring_plan_ts)) {
								$is_recurring = 0;
							}
							$sql  = " UPDATE " . $table_prefix . "orders_items SET ";
							$sql .= " recurring_payments_failed=" . $db->tosql($recurring_payments_failed, INTEGER) . ", ";
							$sql .= " recurring_payments_made=" . $db->tosql($recurring_payments_made, INTEGER) . ", ";
							if ($recurring_last_ts) {
								$sql .= " recurring_last_payment=" . $db->tosql($recurring_last_ts, DATETIME) . ", ";
							}
							$sql .= " recurring_plan_payment=" . $db->tosql($recurring_plan_ts, DATETIME) . ", ";
							$sql .= " is_recurring=" . $db->tosql($is_recurring, INTEGER);
							$sql .= " WHERE order_item_id=" . $db->tosql($parent_order_item_id, INTEGER);
							$db->query($sql);
						}
					}
				}

				foreach ($subscriptions as $order_item_id => $subscription) {
					$is_account_subscription = $subscription["is_account_subscription"];
					$user_id = $subscription["user_id"];
					$new_item_paid = $subscription["paid"];
					$subscription_period = $subscription["period"];
					$subscription_interval = $subscription["interval"];
					$subscription_suspend = $subscription["suspend"];
					$subscription_start_date = $subscription["start_date"];
					$subscription_expiry_date = $subscription["expiry_date"];

					if ($is_account_subscription) {
						$sql  = " SELECT expiry_date FROM " . $table_prefix . "users ";
						$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
						$db->query($sql);
						if ($db->next_record()) {
							$current_date = va_time();
							$current_date_ts = mktime (0,0,0, $current_date[MONTH], $current_date[DAY], $current_date[YEAR]);
							$expiry_date = $db->f("expiry_date", DATETIME);
							$expiry_date_ts = $current_date_ts;
							if (is_array($expiry_date)) {
								$expiry_date_ts = mktime (0,0,0, $expiry_date[MONTH], $expiry_date[DAY], $expiry_date[YEAR]);
							}
							if ($expiry_date_ts < $current_date_ts) {
								$expiry_date_ts = $current_date_ts;
							}
							$new_expiry_date = va_time($expiry_date_ts);
							if (!$new_item_paid) {
								$subscription_interval = -$subscription_interval;
							}
							if ($subscription_period == 1) {
								$new_expiry_date_ts = mktime (0, 0, 0, $new_expiry_date[MONTH], $new_expiry_date[DAY] + $subscription_interval, $new_expiry_date[YEAR]);
							} elseif ($subscription_period == 2) {
								$new_expiry_date_ts = mktime (0, 0, 0, $new_expiry_date[MONTH], $new_expiry_date[DAY] + ($subscription_interval * 7), $new_expiry_date[YEAR]);
							} elseif ($subscription_period == 3) {
								$new_expiry_date_ts = mktime (0, 0, 0, $new_expiry_date[MONTH] + $subscription_interval, $new_expiry_date[DAY], $new_expiry_date[YEAR]);
							} else {
								$new_expiry_date_ts = mktime (0, 0, 0, $new_expiry_date[MONTH], $new_expiry_date[DAY], $new_expiry_date[YEAR] + $subscription_interval);
							}
							if ($new_item_paid) {
								$subscription_start_date = $expiry_date_ts;
								$subscription_expiry_date = $new_expiry_date_ts;
							} else {
								$subscription_start_date = "";
								$subscription_expiry_date = "";
							}
				  
							$new_suspend_date_ts = $new_expiry_date_ts + (intval($subscription_suspend) * 86400);
							$sql  = " UPDATE " . $table_prefix . "users SET ";
							$sql .= " expiry_date=" . $db->tosql($new_expiry_date_ts, DATETIME);
							$sql .= ", suspend_date=" . $db->tosql($new_suspend_date_ts, DATETIME);
							$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
							$db->query($sql);
				  
							// update order item with subscriptions dates
							$sql  = " UPDATE " . $table_prefix . "orders_items SET ";
							$sql .= " subscription_start_date=" . $db->tosql($subscription_start_date, DATETIME);
							$sql .= ", subscription_expiry_date=" . $db->tosql($subscription_expiry_date, DATETIME);
							$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
							$db->query($sql);
						}
					} else {
					  // set subscription date
						if (!is_array($subscription_start_date)) {
							$subscription_start_date = va_time();
							$subscription_start_date_ts = mktime (0,0,0, $subscription_start_date[MONTH], $subscription_start_date[DAY], $subscription_start_date[YEAR]);
						} else {
							$subscription_start_date_ts = mktime (0,0,0, $subscription_start_date[MONTH], $subscription_start_date[DAY], $subscription_start_date[YEAR]);
						}
						if ($new_item_paid) {
							// update order item with subscriptions dates
							if ($subscription_period == 1) {
								$subscription_expiry_date_ts = mktime (0, 0, 0, $subscription_start_date[MONTH], $subscription_start_date[DAY] + $subscription_interval, $subscription_start_date[YEAR]);
							} elseif ($subscription_period == 2) {
								$subscription_expiry_date_ts = mktime (0, 0, 0, $subscription_start_date[MONTH], $subscription_start_date[DAY] + ($subscription_interval * 7), $subscription_start_date[YEAR]);
							} elseif ($subscription_period == 3) {
								$subscription_expiry_date_ts = mktime (0, 0, 0, $subscription_start_date[MONTH] + $subscription_interval, $subscription_start_date[DAY], $subscription_start_date[YEAR]);
							} else {
								$subscription_expiry_date_ts = mktime (0, 0, 0, $subscription_start_date[MONTH], $subscription_start_date[DAY], $subscription_start_date[YEAR] + $subscription_interval);
							}

							$sql  = " UPDATE " . $table_prefix . "orders_items SET ";
							$sql .= " subscription_start_date=" . $db->tosql($subscription_start_date_ts, DATETIME);
							$sql .= ", subscription_expiry_date=" . $db->tosql($subscription_expiry_date_ts, DATETIME);
							$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
							$db->query($sql);
						} else {
							$sql  = " UPDATE " . $table_prefix . "orders_items SET ";
							$sql .= " subscription_expiry_date='' ";
							$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
							$db->query($sql);
						}
				  
					}
				}

				// update stock levels
				foreach ($items_stock_levels as $order_item_id => $item_info) {
					$item_id = $item_info["item_id"];
					$quantity = $item_info["quantity"];
					$stock_action = $item_info["stock_action"];
					$stock_level = $item_info["stock_level"];
					$use_stock_level = $item_info["use_stock_level"];
					$cart_item_id = $item_info["cart_item_id"];
					// update stock level for product
					if ($use_stock_level) {
						if (strlen($stock_level)) {
							$sql  = " UPDATE " . $table_prefix . "items SET ";
							$sql .= " stock_level=stock_level-" . $db->tosql($stock_action * $quantity, NUMBER);
							$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
							$db->query($sql);
						} else {
							$sql  = " UPDATE " . $table_prefix . "items SET ";
							$sql .= " stock_level=" . $db->tosql(-$stock_action * $quantity, NUMBER);
							$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
							$db->query($sql);
						}
					}
					// update information for saved items
					if ($cart_item_id) {
						$sql  = " UPDATE " . $table_prefix . "saved_items SET ";
						$sql .= " quantity_bought=quantity_bought+" . $db->tosql($stock_action * $quantity, NUMBER);
						$sql .= " WHERE cart_item_id=" . $db->tosql($cart_item_id, INTEGER);
						$db->query($sql);
					}
					// check information for order item properties
					$options_values_ids = array();
					$sql  = " SELECT property_values_ids FROM " . $table_prefix . "orders_items_properties oip ";
					$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
					$db->query($sql);
					while ($db->next_record()) {
						$property_values_ids = $db->f("property_values_ids");
						if ($property_values_ids) {
							$values_ids = explode(",", $property_values_ids);
							for ($v = 0; $v < sizeof($values_ids); $v++) {
								$value_id = $values_ids[$v];
								$options_values_ids[] = $value_id;
							}
						}
					}
					foreach ($options_values_ids as $value_id) {
						$sql  = " SELECT stock_level, use_stock_level ";
						$sql .= " FROM " . $table_prefix . "items_properties_values ";
						$sql .= " WHERE item_property_id=" . $db->tosql($value_id, INTEGER);
						$db->query($sql);
						if ($db->next_record()) {
							$option_stock_level = $db->f("stock_level");
							$option_use_stock_level = $db->f("use_stock_level");
							if ($option_use_stock_level) {
								if (strlen($option_stock_level)) {
									$sql  = " UPDATE " . $table_prefix . "items_properties_values SET ";
									$sql .= " stock_level=stock_level-" . $db->tosql($stock_action * $quantity, NUMBER);
									$sql .= " WHERE item_property_id=" . $db->tosql($value_id, INTEGER);
									$db->query($sql);
								} else {
									$sql  = " UPDATE " . $table_prefix . "items_properties_values SET ";
									$sql .= " stock_level=" . $db->tosql(-$stock_action * $quantity, NUMBER);
									$sql .= " WHERE item_property_id=" . $db->tosql($value_id, INTEGER);
									$db->query($sql);
								}
							}
						}
					}
				}

				calculate_commissions_points($order_id, "", $commissions_points);

				// save events
				for ($i = 0; $i < sizeof($events); $i++) {
					list($new_status_id, $order_item_id, $event_date, $event_type, $event_name, $event_description) = $events[$i];
					$r->set_value("status_id",   $new_status_id);
					$r->set_value("order_items", $order_item_id);
					$r->set_value("event_date",  va_time());
					$r->set_value("event_type",  $event_type);
					$r->set_value("event_name",  $event_name);
					$r->set_value("event_description", $event_description);
					$r->insert_record();
				}

			}

		}

		return $is_update;
	}

	function calculate_commissions_points($order_id, $order_items_ids = "", $commissions_points = array())
	{
		global $db, $datetime_show_format, $table_prefix, $settings;

		if (!is_array($commissions_points)) { $commissions_points = array(); }

		if (sizeof($commissions_points) == 0) {
			$commissions_points = array();

			$sql  = " SELECT oi.order_item_id, oi.user_id, oi.item_user_id, oi.affiliate_user_id, ";
			$sql .= " os.status_id, os.commission_action, os.points_action, os.credit_action, ";
			$sql .= " oi.item_id, oi.item_name, oi.price, oi.quantity, ";
			$sql .= " oi.merchant_commission, oi.affiliate_commission, oi.reward_points, oi.reward_credits, oi.points_price ";
			$sql .= " FROM (" . $table_prefix . "orders_items oi ";
			$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON os.status_id=oi.item_status) ";
			$sql .= " WHERE oi.order_id=" . $db->tosql($order_id, INTEGER);
			if ($order_items_ids) {
				$sql .= " AND oi.order_item_id IN (" . $db->tosql($order_items_ids, INTEGERS_LIST) . ")" ;
			}
			$db->query($sql);
			while ($db->next_record()) {
				$order_item_id = $db->f("order_item_id");
				$status_id = $db->f("status_id");

				$order_user_id = $db->f("user_id");
				$affiliate_user_id = $db->f("affiliate_user_id");
				$item_user_id = $db->f("item_user_id");
				$item_id = $db->f("item_id");
				$price = $db->f("price");
				$merchant_commission = $db->f("merchant_commission");
				$affiliate_commission = $db->f("affiliate_commission");
				$reward_points = $db->f("reward_points");
				$reward_credits = $db->f("reward_credits");
				$points_price = $db->f("points_price");
				$quantity = $db->f("quantity");

				$commission_action = $db->f("commission_action");
				$points_action = $db->f("points_action");
				$credit_action = $db->f("credit_action");

				$commissions_points[$order_item_id] = array(
					"order_item_id" => $order_item_id,
					"order_user_id" => $order_user_id,
					"affiliate_user_id" => $affiliate_user_id,
					"item_user_id" => $item_user_id,
					"quantity" => $quantity,
					"affiliate_commission" => $affiliate_commission,
					"merchant_commission" => $merchant_commission,
					"reward_points" => $reward_points,
					"reward_credits" => $reward_credits,
					"points_price" => $points_price,
					"commission_action" => $commission_action,
					"points_action" => $points_action,
					"credit_action" => $credit_action,
				);
			}

			// check points price for order shipping and properties
			if ($order_items_ids == "") {
				$order_info = array();
				$sql  = " SELECT o.user_id, o.affiliate_user_id, ";
				$sql .= " o.shipping_points_amount, o.properties_points_amount, ";
				$sql .= " os.status_id, os.commission_action, os.points_action, os.credit_action ";
				$sql .= " FROM (" . $table_prefix . "orders o ";
				$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON os.status_id=o.order_status) ";
				$sql .= " WHERE o.order_id=" . $db->tosql($order_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$order_info = $db->Record;
				}
				$shipping_points_amount = get_setting_value($order_info, "shipping_points_amount", 0);
				$properties_points_amount = get_setting_value($order_info, "properties_points_amount", 0);
				$order_points_action = get_setting_value($order_info, "points_action", "");
				if ($order_points_action && ($shipping_points_amount + $properties_points_amount) > 0) {
					$order_user_id = get_setting_value($order_info, "user_id", "");
					$affiliate_user_id = get_setting_value($order_info, "affiliate_user_id", "");
					$order_commission_action = get_setting_value($order_info, "commission_action", "");
					$order_credit_action = get_setting_value($order_info, "credit_action", "");
	  
					$commissions_points["order"] = array(
						"order_item_id" => 0,
						"order_user_id" => $order_user_id,
						"affiliate_user_id" => $affiliate_user_id,
						"item_user_id" => 0,
						"quantity" => 1,
						"affiliate_commission" => 0,
						"merchant_commission" => 0,
						"reward_points" => 0,
						"reward_credits" => 0,
						"points_price" => ($shipping_points_amount + $properties_points_amount),
						"commission_action" => $order_commission_action,
						"points_action" => $order_points_action,
						"credit_action" => $order_credit_action,
					);
				}
			}
		}

		// initialize array to save users ids which should be updated later
		$users_commissions = array(); $users_points = array(); $users_credits = array();

		// add or subtract commisions, points or credits
		if (sizeof($commissions_points) > 0) {
			$uc = new VA_Record($table_prefix . "users_commissions");
			$uc->add_textbox("payment_id", INTEGER);
			$uc->add_textbox("user_id", INTEGER);
			$uc->add_textbox("order_id", INTEGER);
			$uc->add_textbox("order_item_id", INTEGER);
			$uc->add_textbox("commission_amount", NUMBER);
			$uc->add_textbox("commission_action", INTEGER);
			$uc->add_textbox("commission_type", INTEGER);
			$uc->add_textbox("date_added", DATETIME);
	  
			$uc->set_value("payment_id", 0);
			$uc->set_value("order_id", $order_id);
			$uc->set_value("date_added", va_time());
	  
			$pts = new VA_Record($table_prefix . "users_points");
			$pts->add_textbox("user_id", INTEGER);
			$pts->add_textbox("order_id", INTEGER);
			$pts->add_textbox("order_item_id", INTEGER);
			$pts->add_textbox("points_amount", NUMBER);
			$pts->add_textbox("points_action", INTEGER);
			$pts->add_textbox("points_type", INTEGER);
			$pts->add_textbox("date_added", DATETIME);
	  
			$pts->set_value("order_id", $order_id);
			$pts->set_value("date_added", va_time());
	  
			$cdt = new VA_Record($table_prefix . "users_credits");
			$cdt->add_textbox("user_id", INTEGER);
			$cdt->add_textbox("order_id", INTEGER);
			$cdt->add_textbox("order_item_id", INTEGER);
			$cdt->add_textbox("credit_amount", NUMBER);
			$cdt->add_textbox("credit_action", INTEGER);
			$cdt->add_textbox("credit_type", INTEGER);
			$cdt->add_textbox("date_added", DATETIME);
	  
			$cdt->set_value("order_id", $order_id);
			$cdt->set_value("date_added", va_time());
	  
			foreach ($commissions_points as $key => $data) {
				// get general data
				$order_item_id = get_setting_value($data, "order_item_id", 0);
				$order_user_id = get_setting_value($data, "order_user_id", "");
				$item_user_id = get_setting_value($data, "item_user_id", "");
				$affiliate_user_id = get_setting_value($data, "affiliate_user_id", "");
				$quantity = get_setting_value($data, "quantity", 1);
				// get actions
				$commission_action = get_setting_value($data, "commission_action", "");
				$points_action = get_setting_value($data, "points_action", "");
				$credit_action = get_setting_value($data, "credit_action", "");
				// check merchant commissions
				$merchant_commission = get_setting_value($data, "merchant_commission", 0);
				if ($merchant_commission > 0) {
					$uc->set_value("user_id", $item_user_id);
					$uc->set_value("order_item_id", $order_item_id);
					$uc->set_value("commission_type", 1);
	  
					$sql  = " SELECT SUM(commission_action * commission_amount) FROM " . $table_prefix . "users_commissions ";
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$sql .= " AND order_item_id=" . $db->tosql($order_item_id, INTEGER);
					$sql .= " AND commission_type=1 ";
					$sum_commission = get_db_value($sql);
					$item_commission = 0;
					if ($commission_action == 1) { // add merchant commissions
						$item_commission = $merchant_commission * $quantity;
					} else if ($commission_action == -1) { // subtract merchant commissions
						$item_commission = 0;
					}
					if ($commission_action && $sum_commission != $item_commission) {
						$users_commissions[$item_user_id] = true;
						if ($sum_commission > $item_commission) {
							$uc->set_value("commission_action", -1);
							$uc->set_value("commission_amount", ($sum_commission - $item_commission));
							$uc->insert_record();
						} else if ($sum_commission < $item_commission) {		
							$uc->set_value("commission_action", 1);
							$uc->set_value("commission_amount", ($item_commission - $sum_commission));
							$uc->insert_record();
						}
					}
				}

				// check affiliate commissions
				$affiliate_commission = get_setting_value($data, "affiliate_commission", 0);
				if ($affiliate_commission > 0) {
					$uc->set_value("user_id", $affiliate_user_id);
					$uc->set_value("order_item_id", $order_item_id);
					$uc->set_value("commission_amount", ($affiliate_commission * $quantity));
					$uc->set_value("commission_type", 2);
	  
					$sql  = " SELECT SUM(commission_action * commission_amount) FROM " . $table_prefix . "users_commissions ";
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$sql .= " AND order_item_id=" . $db->tosql($order_item_id, INTEGER);
					$sql .= " AND commission_type=2 ";
					$sum_commission = get_db_value($sql);
					$item_commission = 0;
					if ($commission_action == 1) { // add affiliate commissions
						$item_commission = $affiliate_commission * $quantity;
					} else if ($commission_action == -1) { // subtract affiliate commissions
						$item_commission = 0;
					}
					if ($commission_action && $sum_commission != $item_commission) {
						$users_commissions[$affiliate_user_id] = true;
						if ($sum_commission > $item_commission) {
							$uc->set_value("commission_action", -1);
							$uc->set_value("commission_amount", ($sum_commission - $item_commission));
							$uc->insert_record();
						} else if ($sum_commission < $item_commission) {		
							$uc->set_value("commission_action", 1);
							$uc->set_value("commission_amount", ($item_commission - $sum_commission));
							$uc->insert_record();
						}
					}
				}

				// add or subtract reward points if they available for product and user registered
				$reward_points = get_setting_value($data, "reward_points", 0);
				if ($reward_points > 0 && $order_user_id > 0) {
					$pts->set_value("user_id", $order_user_id);
					$pts->set_value("order_item_id", $order_item_id);
					$pts->set_value("points_type", 2);
					$sql  = " SELECT SUM(points_action * points_amount) FROM " . $table_prefix . "users_points ";
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$sql .= " AND order_item_id=" . $db->tosql($order_item_id, INTEGER);
					$sql .= " AND points_type=2 ";
					$sum_points = get_db_value($sql);

					$item_points = 0;
					if ($commission_action == 1) { // add reward points 
						$item_points = $reward_points * $quantity;
					} else if ($commission_action == -1) { // subtract reward points
						$item_points = 0;
					}
					if ($commission_action && $sum_points != $item_points) {
						$users_points[$order_user_id] = true;
						if ($sum_points > $item_points) {
							$pts->set_value("points_action", -1);
							$pts->set_value("points_amount", ($sum_points - $item_points));
							$pts->insert_record();
						} else if ($sum_points < $item_points) {		
							$pts->set_value("points_action", 1);
							$pts->set_value("points_amount", ($item_points - $sum_points));
							$pts->insert_record();
						}
					}
				}

				// add or subtract reward points if they available for product and user registered
				$reward_credits = get_setting_value($data, "reward_credits", 0);
				if ($reward_credits > 0 && $order_user_id > 0) {
					$cdt->set_value("user_id", $order_user_id);
					$cdt->set_value("order_item_id", $order_item_id);
					$cdt->set_value("credit_type", 2);
	  
					$sql  = " SELECT SUM(credit_action * credit_amount) FROM " . $table_prefix . "users_credits ";
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$sql .= " AND order_item_id=" . $db->tosql($order_item_id, INTEGER);
					$sql .= " AND credit_type=2 ";
					$sum_credits = get_db_value($sql);

					$item_credits = 0;
					if ($commission_action == 1) { // add reward credits 
						$item_credits = $reward_credits * $quantity;
					} else if ($commission_action == -1) { // subtract reward credits
						$item_credits = 0;
					}
					if ($commission_action && $sum_credits != $item_credits) {
						$users_credits[$order_user_id] = true;
						if ($sum_credits > $item_credits) {
							$cdt->set_value("credit_action", -1);
							$cdt->set_value("credit_amount", ($sum_credits - $item_credits));
							$cdt->insert_record();
						} else if ($sum_credits < $item_credits) {		
							$cdt->set_value("credit_action", 1);
							$cdt->set_value("credit_amount", ($item_credits - $sum_credits));
							$cdt->insert_record();
						}
					}
				}

				// subtract or return points if they were used to pay for something
				$points_price = get_setting_value($data, "points_price", 0);
				if ($points_price > 0) {
					$pts->set_value("user_id", $order_user_id);
					$pts->set_value("order_item_id", $order_item_id);
					$pts->set_value("points_amount", ($points_price * $quantity));
					$pts->set_value("points_type", 1);
	  
					$sql  = " SELECT SUM(points_action * points_amount) FROM " . $table_prefix . "users_points ";
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$sql .= " AND order_item_id=" . $db->tosql($order_item_id, INTEGER);
					$sql .= " AND points_type=1 ";
					$sum_points = get_db_value($sql);

					$item_points = 0;
					if ($points_action == 1) { // return points to account
						$item_points = 0;
					} else if ($points_action == -1) { // subtract points from account
						$item_points = -($points_price * $quantity);
					}
					if ($points_action && $sum_points != $item_points) {
						$users_points[$order_user_id] = true;
						if ($sum_points > $item_points) {
							$pts->set_value("points_action", -1);
							$pts->set_value("points_amount", ($sum_points - $item_points));
							$pts->insert_record();
						} else if ($sum_points < $item_points) {		
							$pts->set_value("points_action", 1);
							$pts->set_value("points_amount", ($item_points - $sum_points));
							$pts->insert_record();
						}
					}
				}

			} // end of order item cycle
		} // end of adding commissions, points and credits

		// start updating total fields

		// update total_points field in users table
		foreach ($users_points as $user_id => $user_value) {
			$sql  = " SELECT SUM(points_action * points_amount) ";
			$sql .= " FROM " . $table_prefix . "users_points ";
			$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$total_points_sum = get_db_value($sql);

			$sql  = " UPDATE " . $table_prefix . "users ";
			$sql .= " SET total_points=" . $db->tosql($total_points_sum, NUMBER);
			$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$db->query($sql);

			// update user information in session if available
			$user_info = get_session("session_user_info");
			$session_user_id = get_setting_value($user_info, "user_id", 0);
			$session_total_points = get_setting_value($user_info, "total_points", 0);
			if ($session_user_id == $user_id && $total_points_sum != $session_total_points) {
				$user_info["total_points"] = $total_points_sum;
				set_session("session_user_info", $user_info);
			}
		}
		// update credit_balance field in users table
		foreach ($users_credits as $user_id => $user_value) {
			$sql  = " SELECT SUM(credit_action * credit_amount) ";
			$sql .= " FROM " . $table_prefix . "users_credits ";
			$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$total_credit_sum = get_db_value($sql);

			$sql  = " UPDATE " . $table_prefix . "users ";
			$sql .= " SET credit_balance=" . $db->tosql($total_credit_sum, NUMBER);
			$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$db->query($sql);

			// update user information in session if available
			$user_info = get_session("session_user_info");
			$session_user_id = get_setting_value($user_info, "user_id", 0);
			$session_credit_balance = get_setting_value($user_info, "credit_balance", 0);
			if ($session_user_id == $user_id && $total_credit_sum != $session_credit_balance) {
				$user_info["credit_balance"] = $total_credit_sum;
				set_session("session_user_info", $user_info);
			}
		}

		// check if new user payment should be generated
		if (sizeof($users_commissions) > 0) {
			$min_payment_amount = get_setting_value($settings, "min_payment_amount", 100);
			foreach ($users_commissions as $user_id => $user_value) {
				$total_commissions = 0; $commissions_ids = ""; $commission_start = va_timestamp(); $commission_end = 0;
				$sql  = " SELECT commission_id, commission_action, commission_amount, date_added ";
				$sql .= " FROM " . $table_prefix . "users_commissions ";
				$sql .= " WHERE payment_id=0 AND user_id=" . $db->tosql($user_id, INTEGER);
				$db->query($sql);
				while ($db->next_record()) {
					$commission_id = $db->f("commission_id");
					$commission_action = $db->f("commission_action");
					$commission_amount = $db->f("commission_amount");
					$date_added = $db->f("date_added", DATETIME);
					$date_added_ts = mktime ($date_added[HOUR], $date_added[MINUTE], $date_added[SECOND], $date_added[MONTH], $date_added[DAY], $date_added[YEAR]);
					if ($date_added_ts > $commission_end) {
						$commission_end = $date_added_ts;
					}
					if ($date_added_ts < $commission_start) {
						$commission_start = $date_added_ts;
					}
					if ($commissions_ids) { $commissions_ids .= ","; }
					$commissions_ids .= $commission_id;
					$total_commissions += ($commission_action * $commission_amount);
				}
				if ($total_commissions >= $min_payment_amount) {
					$up = new VA_Record($table_prefix . "users_payments");
					if ($db->DBType == "postgre") {
						$payment_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "users_payments') ");
						$up->add_textbox("payment_id", INTEGER);
						$up->set_value("payment_id", $payment_id);
					}
					$up->add_textbox("user_id", INTEGER);
					$up->add_textbox("is_paid", INTEGER);
					$up->add_textbox("transaction_id", TEXT);
					$up->add_textbox("payment_total", NUMBER);
					$up->add_textbox("payment_name", TEXT);
					$up->add_textbox("payment_notes", TEXT);
					$up->add_textbox("date_added", DATETIME);
					$up->add_textbox("admin_id_added_by", INTEGER);
					$up->add_textbox("admin_id_modified_by", INTEGER);


					// generate payment name
					$payment_name = va_date($datetime_show_format, $commission_start) . " - " . va_date($datetime_show_format, $commission_end);

					$up->set_value("user_id", $user_id);
					$up->set_value("is_paid", 0);
					$up->set_value("payment_total", $total_commissions);
					$up->set_value("payment_name", $payment_name);
					$up->set_value("payment_notes", AUTO_SUBMITTED_PAYMENT_MSG); //"Auto-submitted payment"
					$up->set_value("date_added", va_time());
					$up->set_value("admin_id_added_by", 0);
					$up->set_value("admin_id_modified_by", 0);
					$up->insert_record();
					if ($db->DBType == "mysql") {
						$payment_id = get_db_value(" SELECT LAST_INSERT_ID() ");
					} elseif ($db->DBType == "access") {
						$payment_id = get_db_value(" SELECT @@IDENTITY ");
					} elseif ($db->DBType == "db2") {
						$payment_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "users_payments FROM " . $table_prefix . "users_payments");
					} else {
						$payment_id = get_db_value(" SELECT MAX(payment_id) FROM " . $table_prefix . "users_payments");
					}

					$sql  = " UPDATE " . $table_prefix . "users_commissions ";
					$sql .= " SET payment_id=" . $db->tosql($payment_id, INTEGER);
					$sql .= " WHERE payment_id=0 AND user_id=" . $db->tosql($user_id, INTEGER);
					$sql .= " AND commission_id IN (" . $db->tosql($commissions_ids, INTEGERS_LIST) . ")";
					$db->query($sql);

					// check and update total amount for generated payment if it was change
					$sql  = " SELECT SUM(commission_action * commission_amount) ";
					$sql .= " FROM " . $table_prefix . "users_commissions ";
					$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
					$payment_total = get_db_value($sql);
					if ($payment_total != $total_commissions) {
						$sql  = " UPDATE " . $table_prefix . "users_payments ";
						$sql .= " SET payment_total=" . $db->tosql($payment_total, NUMBER);
						$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
						$db->query($sql);
					}
				}
			}
		}
		// end updating total values

	}

	function update_order_shipping($order_id, $shipping_type_id)
	{
		global $db, $table_prefix;

		$sql  = " SELECT site_id, shipping_type_id, shipping_type_code, shipping_type_desc, country_id, delivery_country_id, ";
		$sql .= " state_id, delivery_state_id, weight_total, total_quantity, goods_total, goods_incl_tax, properties_total, ";
		$sql .= " properties_taxable, ";
		$sql .= " total_discount, tax_percent, processing_fee ";
		$sql .= " FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$order_site_id = $db->f("site_id");
			$current_shipping_id = $db->f("shipping_type_id");
			$current_shipping_code = $db->f("shipping_type_code");
			$current_shipping_type = $db->f("shipping_type_desc");
			$weight_total = $db->f("weight_total");
			$total_quantity = $db->f("total_quantity");
			$goods_total = $db->f("goods_total");
			$goods_incl_tax = $db->f("goods_incl_tax");
			$properties_total = $db->f("properties_total");
			$properties_taxable = $db->f("properties_taxable");
			$total_discount = $db->f("total_discount");
			$processing_fee = $db->f("processing_fee");
			$goods_with_discount = $goods_total - $total_discount;

			$tax_percent = $db->f("tax_percent");
			$free_postage = false;
			$country_id = $db->f("delivery_country_id");
			if (!strlen($country_id)) {
				$country_id = $db->f("country_id");
			}
			$state_id = $db->f("delivery_state_id");
			if (!strlen($state_id)) {
				$state_id = $db->f("state_id");
			}
		} else {
			$shipping_type_id = "";
		}
		if (strlen($shipping_type_id) && $shipping_type_id != $current_shipping_id) {
			if (strlen($current_shipping_id)) {
				$sql  = " SELECT tare_weight FROM " . $table_prefix . "shipping_types ";
				$sql .= " WHERE shipping_type_id=" . $db->tosql($current_shipping_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$tare_weight = $db->f("tare_weight");
					if ($tare_weight > 0) {
						$weight_total = $weight_total - $tare_weight;
						$sql  = " UPDATE " . $table_prefix ."orders SET weight_total=weight_total-" . $db->tosql($tare_weight, NUMBER);
						$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
						$db->query($sql);
					}
				} else {
					$shipping_type_id = "";
				}
			}
			if (strlen($shipping_type_id)) {

				$sql  = " SELECT st.shipping_type_id, st.shipping_type_code, st.shipping_type_desc, st.cost_per_order, ";
				$sql .= " st.cost_per_product, st.cost_per_weight, st.tare_weight, st.is_taxable ";
				$sql .= " FROM (((". $table_prefix . "shipping_types st ";
				$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_countries stc ON st.shipping_type_id=stc.shipping_type_id) ";
				$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_states stt ON st.shipping_type_id=stt.shipping_type_id) ";
				$sql .= " LEFT JOIN " . $table_prefix . "shipping_types_sites sts ON st.shipping_type_id=sts.shipping_type_id) ";
				$sql .= " WHERE (st.countries_all=1 OR stc.country_id=" . $db->tosql($country_id, INTEGER, true, false) . ") ";
				$sql .= " AND (st.states_all=1 OR stt.state_id=" . $db->tosql($state_id, INTEGER, true, false) . ") ";
				$sql .= " AND st.min_weight<=" . $db->tosql($weight_total, NUMBER);
				$sql .= " AND st.max_weight>=" . $db->tosql($weight_total, NUMBER);
				$sql .= " AND st.min_goods_cost<=" . $db->tosql($goods_total, NUMBER);
				$sql .= " AND st.max_goods_cost>=" . $db->tosql($goods_total, NUMBER);
				$sql .= " AND st.shipping_type_id=" . $db->tosql($shipping_type_id, INTEGER);
				$sql .= " AND (st.sites_all=1 OR sts.site_id=" . $db->tosql($order_site_id, INTEGER, true, false) . ")";
				$db->query($sql);
				if ($db->next_record()) {
					$shipping_type_code = $db->f("shipping_type_code");
					$shipping_type_desc = $db->f("shipping_type_desc");
					$cost_per_order = $db->f("cost_per_order");
					$cost_per_product = $db->f("cost_per_product");
					$cost_per_weight = $db->f("cost_per_weight");
					$tare_weight = $db->f("tare_weight");
					if (!strlen($tare_weight)) $tare_weight = 0;
					$shipping_taxable = $db->f("is_taxable");
					$shipping_cost = $free_postage ? 0 : $cost_per_order + ($cost_per_product * $total_quantity) + ($cost_per_weight * ($weight_total + $tare_weight));
					$shipping_cost = round($shipping_cost, 2);
					//todo: new taxes algorithm
					//$taxable_amount = ($shipping_taxable == 1) ? (round($goods_taxable, 2) + round($properties_taxable, 2) + round($shipping_cost, 2)) : (round($goods_taxable, 2) + round($properties_taxable, 2));
					$taxable_amount = ($shipping_taxable == 1) ? (round($goods_total, 2) + round($properties_taxable, 2) + round($shipping_cost, 2)) : (round($goods_total, 2) + round($properties_taxable, 2));
					$tax_cost = ($taxable_amount * $tax_percent) / 100;
					$tax_cost = round($tax_cost, 2);
					$order_total = $goods_with_discount + $properties_total + round($shipping_cost, 2) + $tax_cost + $processing_fee;

					$sql  = " UPDATE " . $table_prefix . "orders SET ";
					$sql .= " weight_total=weight_total+" . $db->tosql($tare_weight, NUMBER) . ", ";
					$sql .= " shipping_type_id=" . $db->tosql($shipping_type_id, INTEGER) . ", ";
					$sql .= " shipping_type_code=" . $db->tosql($shipping_type_code, TEXT) . ", ";
					$sql .= " shipping_type_desc=" . $db->tosql($shipping_type_desc, TEXT) . ", ";
					$sql .= " shipping_cost=" . $db->tosql($shipping_cost, NUMBER) . ", ";
					$sql .= " shipping_taxable=" . $db->tosql($shipping_taxable, INTEGER) . ", ";
					$sql .= " tax_total=" . $db->tosql($tax_cost, NUMBER) . ", ";
					$sql .= " order_total=" . $db->tosql($order_total, NUMBER) . " ";
					$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
					$db->query($sql);

					// save event with updated shipping
					$r = new VA_Record($table_prefix . "orders_events");
					$r->add_textbox("order_id", INTEGER);
					$r->add_textbox("status_id", INTEGER);
					$r->add_textbox("admin_id", INTEGER);
					$r->add_textbox("event_date", DATETIME);
					$r->add_textbox("event_type", TEXT);
					$r->add_textbox("event_name", TEXT);
					$r->add_textbox("event_description", TEXT);
					$r->set_value("order_id", $order_id);
					$r->set_value("status_id", 0);
					$r->set_value("admin_id", get_session("session_admin_id"));
					$r->set_value("event_date", va_time());
					$r->set_value("event_type", "update_order_shipping");
					$r->set_value("event_name", $current_shipping_type . " &ndash;&gt; " . $shipping_type_desc);
					$r->insert_record();

				}
			}


		} else {
			$shipping_type_id = "";
		}

		return $shipping_type_id;
	}

	function update_order_items($order_id, $updated_order_item_id = "")
	{
		global $db, $table_prefix;
		$order_tax_rates = order_tax_rates($order_id);

		$sql  = " SELECT o.shipping_cost, o.shipping_taxable, st.tare_weight, o.properties_total, o.properties_taxable, ";
		$sql .= " o.total_discount, o.total_discount_tax, o.tax_percent, o.processing_fee, o.tax_prices_type, o.tax_round_type ";
		$sql .= " FROM (" . $table_prefix . "orders o ";
		$sql .= " LEFT JOIN " . $table_prefix . "shipping_types st ON st.shipping_type_id=o.shipping_type_id) ";
		$sql .= " WHERE o.order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {

			$shipping_cost = $db->f("shipping_cost");
			$shipping_taxable = $db->f("shipping_taxable");
			$tare_weight = $db->f("tare_weight");

			$properties_total = $db->f("properties_total");
			$properties_taxable = $db->f("properties_taxable");
			$total_discount = $db->f("total_discount");
			$total_discount_tax = $db->f("total_discount_tax");
			$tax_percent = $db->f("tax_percent");
			$processing_fee = $db->f("processing_fee");
			
			$tax_prices_type = $db->f("tax_prices_type");
			$tax_round = $db->f("tax_round_type");
		} else {
			return false;
		}

		$total_buying = 0; $goods_total = 0; $goods_tax_total = 0; $total_quantity = 0;
		$weight_total = 0; $tax_total = 0; $order_total = 0;
		$sql  = " SELECT * FROM " . $table_prefix . "orders_items ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$weight = $db->f("weight");
			$buying_price = $db->f("buying_price");
			$price = $db->f("price");
			$quantity = $db->f("quantity");
			$tax_free = $db->f("tax_free");
			$item_tax_percent = $db->f("tax_percent");
			$item_type_id = $db->f("item_type_id");
			if (!strlen($item_tax_percent)) { $item_tax_percent = $tax_percent; }
			
			$item_total = $price * $quantity;
			$item_tax = get_tax_amount($order_tax_rates, $item_type_id, $price, $tax_free, $item_tax_percent, "", 1, $tax_prices_type, $tax_round);
			$item_tax_total = get_tax_amount($order_tax_rates, $item_type_id, $item_total, $tax_free, $item_tax_percent, "", 1, $tax_prices_type, $tax_round);
			
			if ($tax_prices_type == 1) {
				$price_excl_tax = $price - $item_tax;
				$price_incl_tax = $price;
				$price_excl_tax_total = $item_total - $item_tax_total;
				$price_incl_tax_total = $item_total;
			} else {
				$price_excl_tax = $price;
				$price_incl_tax = $price + $item_tax;
				$price_excl_tax_total = $item_total;
				$price_incl_tax_total = $item_total + $item_tax_total;
			}
			$total_quantity  += $quantity;
			$weight_total    += ($weight * $quantity);
			$total_buying    += ($buying_price * $quantity);
			$goods_total     += $price_excl_tax_total;			
			$goods_tax_total +=  $item_tax_total;
		}
		$weight_total += $tare_weight;
		if (!$total_discount_tax && $goods_tax_total > 0) {
			$total_discount_tax = round(($total_discount * $goods_tax_total) / $goods_total, 2);
			if ($tax_prices_type == 1) {
				$total_discount = $total_discount - $total_discount_tax;
			}
		}

		$shipping_tax_free = (!$shipping_taxable);
		$shipping_tax = get_tax_amount($order_tax_rates, "shipping", $shipping_cost, $shipping_tax_free, $shipping_tax_percent, "", 1, $tax_prices_type, $tax_round);
		$properties_tax = get_tax_amount($order_tax_rates, "", $properties_taxable, 0, $properties_tax_percent, "", 1, $tax_prices_type, $tax_round);

		$tax_total = $goods_tax_total - $total_discount_tax + $properties_tax + $shipping_tax;
		$order_total = round($goods_total, 2) - round($total_discount, 2) + round($properties_total, 2) + round($shipping_cost, 2) + $tax_total + $processing_fee;

		$sql  = " UPDATE " . $table_prefix . "orders SET ";
		$sql .= " total_buying=" . $db->tosql($total_buying, FLOAT) . ", ";
		$sql .= " goods_total=" . $db->tosql($goods_total, FLOAT) . ", ";
		$sql .= " total_quantity=" . $db->tosql($total_quantity, FLOAT) . ", ";
		$sql .= " weight_total=" . $db->tosql($weight_total, FLOAT) . ", ";
		$sql .= " tax_total=" . $db->tosql($tax_total, FLOAT) . ", ";
		$sql .= " order_total=" . $db->tosql($order_total, FLOAT);
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		
		if ($updated_order_item_id > 0) {
			$sql = "DELETE FROM " . $table_prefix . "users_points WHERE order_item_id=" . $db->tosql($updated_order_item_id, INTEGER);
			$db->query($sql);
			calculate_commissions_points($order_id, $updated_order_item_id);
		}
	}

	function remove_orders($orders_ids, $delete_orders = true)
	{
		global $db, $table_prefix;

		$downloads_ids = "";
		$sql = "SELECT download_id FROM " . $table_prefix . "items_downloads WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")";
		$db->query($sql);
		while ($db->next_record()) {
			if (strlen($downloads_ids)) { $downloads_ids .= ","; }
			$downloads_ids .= $db->f("download_id");
		}
		$order_tax_ids = "";
		$sql = "SELECT order_tax_id FROM " . $table_prefix . "orders_taxes WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")";
		$db->query($sql);
		while ($db->next_record()) {
			if (strlen($order_tax_ids)) { $order_tax_ids .= ","; }
			$order_tax_ids .= $db->f("order_tax_id");
		}

		$items = array(); $saved_items = array(); $users_points = array(); $users_credits = array();
		$sql  = " SELECT oi.item_id, oi.cart_item_id, oi.quantity, oi.user_id, oi.points_price, oi.reward_points, oi.reward_credits, ";
		$sql .= " os.stock_level_action ";
		$sql .= " FROM (" . $table_prefix . "orders_items oi ";
		$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON os.status_id=oi.item_status) ";
		$sql .= " WHERE oi.order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")";
		$db->query($sql);
		while ($db->next_record()) {
			$item_id  = $db->f("item_id");
			$cart_item_id = $db->f("cart_item_id");
			$quantity = $db->f("quantity");
			$stock_level_action = $db->f("stock_level_action");
			$user_id  = $db->f("user_id");
			$points_price = $db->f("points_price");
			$reward_points = $db->f("reward_points");
			$reward_credits = $db->f("reward_credits");
			// release stock only if it was reserved before
			if ($stock_level_action == 1) {
				if (isset($items[$item_id])) {
					$items[$item_id] += $quantity;
				} else {
					$items[$item_id] = $quantity;
				}
				if ($cart_item_id) {
					if (isset($saved_items[$cart_item_id])) {
						$saved_items[$cart_item_id] += $quantity;
					} else {
						$saved_items[$cart_item_id] = $quantity;
					}
				}
			}
			if ($user_id > 0 && ($points_price > 0 || $reward_points > 0)) {
				$users_points[$user_id] = $user_id;
			}
			if ($user_id > 0 && $reward_credits > 0) {
				$users_credits[$user_id] = $user_id;
			}
		}

		$sql  = " SELECT user_id, total_points_amount, total_reward_points, total_reward_credits, credit_amount ";
		$sql .= " FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")";
		$db->query($sql);
		while ($db->next_record()) {
			$user_id  = $db->f("user_id");
			$total_points_amount = $db->f("total_points_amount");
			$total_reward_points = $db->f("total_reward_points");
			$total_reward_credits = $db->f("total_reward_credits");
			$credit_amount = $db->f("credit_amount");
			if ($user_id > 0 && ($total_points_amount > 0 || $total_reward_points > 0)) {
				$users_points[$user_id] = $user_id;
			}
			if ($user_id > 0 && ($total_reward_credits > 0 || $credit_amount > 0)) {
				$users_credits[$user_id] = $user_id;
			}
		}
		$serials = array();
		$sql  = " SELECT ois.item_id, ois.serial_number ";
		$sql .= " FROM " . $table_prefix . "orders_items_serials ois, " . $table_prefix . "items i ";
		$sql .= " WHERE ois.item_id=i.item_id ";
		$sql .= " AND i.generate_serial=2 ";
		$sql .= " AND ois.order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")";
		$sql .= " GROUP BY ois.item_id, ois.serial_number ";
		$db->query($sql);
		while ($db->next_record()) {
			$item_id  = $db->f("item_id");
			$serial_number = $db->f("serial_number");
			if (isset($serials[$item_id])) {
				$serials[$item_id] .= "," . $db->tosql($serial_number, TEXT);
			} else {
				$serials[$item_id] = $db->tosql($serial_number, TEXT);
			}
		}
		$options_values_ids = array();
		$sql  = " SELECT oip.property_values_ids, oi.quantity, os.stock_level_action ";
		$sql .= " FROM ((" . $table_prefix . "orders_items_properties oip ";
		$sql .= " INNER JOIN " . $table_prefix . "orders_items oi ON oip.order_item_id=oi.order_item_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON os.status_id=oi.item_status) ";
		$sql .= " WHERE oip.order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")";
		$db->query($sql);
		while ($db->next_record()) {
			$property_values_ids = $db->f("property_values_ids");
			$quantity = $db->f("quantity");
			$stock_level_action = $db->f("stock_level_action");
			// release stock only if it was reserved before
			if ($stock_level_action == 1 && $property_values_ids) {
				$values_ids = explode(",", $property_values_ids);
				for ($v = 0; $v < sizeof($values_ids); $v++) {
					$value_id = $values_ids[$v];
					if (isset($options_values_ids[$value_id])) {
						$options_values_ids[$value_id] += $quantity;
					} else {
						$options_values_ids[$value_id] = $quantity;
					}
				}
			}
		}

		if ($delete_orders) {
			// keep original order and it events
			$db->query("DELETE FROM " . $table_prefix . "orders WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");
			$db->query("DELETE FROM " . $table_prefix . "orders_events WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");
		}
		$db->query("DELETE FROM " . $table_prefix . "orders_properties WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "orders_coupons WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "orders_items WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "orders_items_properties WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "users_commissions WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ") AND payment_id=0");
		$db->query("DELETE FROM " . $table_prefix . "users_points WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "users_credits WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");

		foreach ($items as $item_id => $quantity) {
			$sql  = " UPDATE " . $table_prefix . "items SET ";
			$sql .= " stock_level=stock_level+" . $db->tosql($quantity, INTEGER);
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
			$sql .= " AND use_stock_level=1 ";
			$db->query($sql);
		}
		foreach ($saved_items as $cart_item_id => $quantity) {
			$sql  = " UPDATE " . $table_prefix . "saved_items SET ";
			$sql .= " quantity_bought=quantity_bought-" . $db->tosql($quantity, INTEGER);
			$sql .= " WHERE cart_item_id=" . $db->tosql($cart_item_id, INTEGER);
			$db->query($sql);
		}
		foreach ($serials as $item_id => $serial_numbers) {
			$sql  = " UPDATE " . $table_prefix . "items_serials SET ";
			$sql .= " used=0 ";
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
			$sql .= " AND serial_number IN (" . $db->tosql($serial_numbers, INTEGERS_LIST) . ") ";
			$db->query($sql);
		}
		foreach ($options_values_ids as $value_id => $quantity) {
			$sql  = " UPDATE " . $table_prefix . "items_properties_values SET ";
			$sql .= " stock_level=stock_level+" . $db->tosql($quantity, INTEGER);
			$sql .= " WHERE item_property_id=" . $db->tosql($value_id, INTEGER);
			$sql .= " AND use_stock_level=1 ";
			$db->query($sql);
		}
		$user_info = get_session("session_user_info");
		$session_user_id = get_setting_value($user_info, "user_id", 0);
		// update users points
		foreach ($users_points as $user_id => $user_id) {
			$sql  = " SELECT SUM(points_action * points_amount) ";
			$sql .= " FROM " . $table_prefix . "users_points ";
			$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$total_points_sum = get_db_value($sql);

			$sql  = " UPDATE " . $table_prefix . "users ";
			$sql .= " SET total_points=" . $db->tosql($total_points_sum, NUMBER);
			$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$db->query($sql);

			// update user information in session if available
			$session_total_points = get_setting_value($user_info, "total_points", 0);
			if ($session_user_id == $user_id && $total_points_sum != $session_total_points) {
				$user_info["total_points"] = $total_points_sum;
				set_session("session_user_info", $user_info);
			}
		}
		// update user credits balance
		foreach ($users_credits as $user_id => $user_id) {
			$sql  = " SELECT SUM(credit_action * credit_amount) ";
			$sql .= " FROM " . $table_prefix . "users_credits ";
			$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$total_credit_sum = get_db_value($sql);

			$sql  = " UPDATE " . $table_prefix . "users ";
			$sql .= " SET credit_balance=" . $db->tosql($total_credit_sum, NUMBER);
			$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$db->query($sql);

			// update user credit balance information in session if available
			$session_credit_balance = get_setting_value($user_info, "credit_balance", 0);
			if ($session_user_id == $user_id && $total_credit_sum != $session_credit_balance) {
				$user_info["credit_balance"] = $total_credit_sum;
				set_session("session_user_info", $user_info);
			}
		}

		$db->query("DELETE FROM " . $table_prefix . "orders_notes WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "orders_items_serials WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "orders_serials_activations WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "coupons WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");
		if (strlen($downloads_ids)) {
			$db->query("DELETE FROM " . $table_prefix . "items_downloads WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");
			$db->query("DELETE FROM " . $table_prefix . "items_downloads_statistic WHERE download_id IN (" . $db->tosql($downloads_ids, INTEGERS_LIST) . ")");
		}
		if (strlen($order_tax_ids)) {
			$db->query("DELETE FROM " . $table_prefix . "orders_taxes WHERE order_id IN (" . $db->tosql($orders_ids, INTEGERS_LIST) . ")");
			$db->query("DELETE FROM " . $table_prefix . "orders_items_taxes WHERE order_tax_id IN (" . $db->tosql($order_tax_ids, INTEGERS_LIST) . ")");
		}
	}

	function get_payment_rate($payment_id, $currency_rate)
	{
		global $db, $table_prefix;
		$payment_rate = 1;
		$sql  = " SELECT parameter_type,parameter_source FROM " . $table_prefix . "payment_parameters ";
		$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
		$sql .= " AND parameter_name IN ('currency_code', 'x_currency_code', 'currency', 'currencycode', 'currencyid') ";
		$sql .= " AND not_passed<>1 ";
		$db->query($sql);
		if ($db->next_record()) {
			$parameter_type = $db->f("parameter_type");
			$parameter_source = trim($db->f("parameter_source"));
			if ($parameter_source == "currency_code" || $parameter_source == "{currency_code}"
				|| $parameter_source == "currency_value" || $parameter_source == "{currency_value}") {
				$payment_rate = $currency_rate;
			} else {
				$sql  = " SELECT exchange_rate FROM " . $table_prefix . "currencies ";
				$sql .= " WHERE currency_code=" . $db->tosql($parameter_source, TEXT);
				$sql .= " OR currency_value=" . $db->tosql($parameter_source, TEXT);
				$db->query($sql);
				if ($db->next_record()) {
  				$payment_rate = $db->f("exchange_rate");
				}
			}
		}
		return $payment_rate;
	}

	function generate_serial($order_item_id, $sn, $product_info, $generation_type = 1)
	{
		global $db, $table_prefix;

		$serial_number = "";
		if ($generation_type == 1) {
			// random generation
			while ($serial_number == "")
			{
				$random_value  = mt_rand();
				$serial_hash   = strtoupper(md5($order_item_id . $sn . $random_value . va_timestamp()));
				$serial_number = substr($serial_hash,0,4)."-".substr($serial_hash,4,4)."-".substr($serial_hash,8,4)."-".substr($serial_hash,12,4);
				$sql = " SELECT serial_id FROM " .$table_prefix. "orders_items_serials WHERE serial_number=" . $db->tosql($serial_number, TEXT);
				$db->query($sql);
				if ($db->next_record()) {
					$serial_number = "";
				}
			}
		} elseif ($generation_type == 2) {
			// get from predefined list
			$item_id = $product_info["item_id"];
			$sql  = " SELECT serial_id, serial_number FROM " . $table_prefix . "items_serials ";
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
			$sql .= " AND (used=0 OR used IS NULL) ";
			$db->RecordsPerPage = 1;
			$db->PageNumber = 1;
			$db->query($sql);
			if ($db->next_record()) {
				$serial_id = $db->f("serial_id");
				$serial_number = $db->f("serial_number");
				$sql  = " UPDATE " . $table_prefix . "items_serials SET used=1 ";
				$sql .= " WHERE serial_id=" . $db->tosql($serial_id, INTEGER);
				$db->query($sql);
			}
		}

		return $serial_number;
	}

	function generate_gift_voucher($order_id, $order_item_id, $voucher_name, $voucher_price)
	{
		global $db, $table_prefix;

		$voucher_code = "";
		while ($voucher_code == "")
		{
			$random_value = mt_rand();
			$voucher_hash = strtoupper(md5($order_id . $order_item_id . $voucher_price . $random_value . va_timestamp()));
			$voucher_code = substr($voucher_hash, 0, 8);
			$sql = " SELECT coupon_id FROM " .$table_prefix. "coupons WHERE coupon_code=" . $db->tosql($voucher_code, TEXT);
			$db->query($sql);
			if ($db->next_record()) {
				$voucher_code = "";
			}
		}

		$vr = new VA_Record($table_prefix . "coupons");
		$vr->add_textbox("order_id", INTEGER);
		$vr->add_textbox("order_item_id", INTEGER);
		$vr->add_textbox("coupon_code", TEXT);
		$vr->add_textbox("coupon_title", TEXT);
		$vr->add_textbox("is_active", INTEGER);
		$vr->add_textbox("discount_type", INTEGER);
		$vr->add_textbox("discount_amount", NUMBER);
		$vr->add_textbox("quantity_limit", INTEGER);
		$vr->add_textbox("coupon_uses", INTEGER);

		$vr->set_value("order_id", $order_id);
		$vr->set_value("order_item_id", $order_item_id);
		$vr->set_value("coupon_code", $voucher_code);
		$vr->set_value("coupon_title", $voucher_name);
		$vr->set_value("is_active", 0);
		$vr->set_value("discount_type", 5);
		$vr->set_value("discount_amount", $voucher_price);
		$vr->set_value("quantity_limit", 0);
		$vr->set_value("coupon_uses", 0);

		$vr->insert_record();

		return $voucher_code;
	}

	// calculate fingerprint for Authorize.net
	function calculate_fp ($login_id, $trankey, $amount, $sequence, $timestamp, $currency = "")
	{
  	return (hmac_md5 ($login_id."^".$sequence."^".$timestamp."^".$amount."^".$currency, $trankey));
	}


	function get_final_message($message, $message_type)
	{
		$message_type = str_replace("/", "\/", $message_type);
		$message = preg_replace("/\[" . $message_type . "\]/si", "", $message);
		$message = preg_replace("/\[\/" . $message_type . "\]/si", "", $message);
		$message = preg_replace("/\[success].*\[\/success]/s", "", $message);
		$message = preg_replace("/\[pending].*\[\/pending]/s", "", $message);
		$message = preg_replace("/\[failure].*\[\/failure]/s", "", $message);

		return $message;
	}

	function clean_cc_number($cc_number)
	{
		return ereg_replace ("[^0-9]+", "", $cc_number);
	}

	function format_cc_number($cc_number, $delimiter = "-", $hide_first = false)
	{
		$cc_formatted = "";
		$cc_number = preg_replace("/[\s\-]/", "", $cc_number);
		$total_digit = strlen($cc_number);
		if ($total_digit) {
			for ($i = 0; $i < $total_digit; $i++) {
				if ($i && $i % 4 == 0) {
					$cc_formatted .= $delimiter;
				}
				if ($hide_first && ($i + 4) < $total_digit) {
					$cc_formatted .= "*";
				} else {
					$cc_formatted .= $cc_number[$i];
				}
			}
		}
		return $cc_formatted;
	}

	function check_cc_number($cc_number)
	{
		$cc_number = strrev (clean_cc_number($cc_number));

		$digits = ""; $sum = 0;
		// Loop through the number one digit at a time
		// Double the value of every second digit (starting from the right)
		// Concatenate the new values with the unaffected digits
		for ($i = 0; $i < strlen ($cc_number); ++$i) {
			$digits .= ($i % 2) ? $cc_number[$i] * 2 : $cc_number[$i];
		}

		// Add all of the single digits together
		for ($i = 0; $i < strlen ($digits); ++$i) {
			$sum += $digits[$i];
		}

		// Valid card numbers will be transformed into a multiple of 10
		return ($sum % 10) ? false : true;
	}

	function get_expecting_date($handle_hours)
	{
		$expecting_date = va_timestamp();
		// add one day if today is Sunday
		if (date("w", $expecting_date) == 0) {
			$expecting_date += 86400;
		}
		while ($handle_hours > 0) {
			if ($handle_hours < 24) {
				$expecting_date += $handle_hours * 3600;
			} else {
				$expecting_date += 86400;
			}
			$handle_hours -= 24;
			if (date("w", $expecting_date) == 0) {
				$expecting_date += 86400;
			}
		}

		return $expecting_date;
	}

	function get_commission($item_user_id, $affiliate_user_id, $price, $options_price, $buying_price, $item_commision_type, $item_commision_amount)
	{
		global $db, $table_prefix, $settings;
		$item_commissions = 0;
		if ($item_user_id || $affiliate_user_id) {
			$commission_type = ""; $commission_amount = 0;
			if (strlen($item_commision_type)) {
				$commission_type = $item_commision_type;
				$commission_amount = $item_commision_amount;
			} else {
				if ($item_user_id) {
					$sql  = " SELECT u.merchant_fee_type AS user_commision_type, u.merchant_fee_amount AS user_commision_amount, ";
					$sql .= " ut.merchant_fee_type AS type_commision_type, ut.merchant_fee_amount AS type_commision_amount ";
				} else {
					$sql  = " SELECT u.affiliate_commission_type AS user_commision_type, u.affiliate_commission_amount AS user_commision_amount, ";
					$sql .= " ut.affiliate_commission_type AS type_commision_type, ut.affiliate_commission_amount AS type_commision_amount ";
				}
				$sql .= " FROM (" . $table_prefix . "users u ";
				$sql .= " LEFT JOIN " . $table_prefix . "user_types ut ON u.user_type_id=ut.type_id) ";
				if ($item_user_id) {
					$sql .= " WHERE u.user_id=" . $db->tosql($item_user_id, INTEGER);
				} else {
					$sql .= " WHERE u.user_id=" . $db->tosql($affiliate_user_id, INTEGER);
				}
				$db->query($sql);
				if ($db->next_record()) {
					$user_commision_type = $db->f("user_commision_type");
					$user_commision_amount = $db->f("user_commision_amount");
					$type_commision_type = $db->f("type_commision_type");
					$type_commision_amount = $db->f("type_commision_amount");
					if (strlen($user_commision_type)) {
						$commission_type = $user_commision_type;
						$commission_amount = $user_commision_amount;
					} elseif (strlen($type_commision_type)) {
						$commission_type = $type_commision_type;
						$commission_amount = $type_commision_amount;
					} else { // check global products commissions
						if ($item_user_id) {
							$commission_type = get_setting_value($settings, "merchant_fee_type", "");
							$commission_amount = get_setting_value($settings, "merchant_fee_amount", 0);
						} else {
							$commission_type = get_setting_value($settings, "affiliate_commission_type", "");
							$commission_amount = get_setting_value($settings, "affiliate_commission_amount", 0);
						}
					}
				}
			}
			if ($commission_type == 1) { // percentage to the whole price
				$item_commissions = round((($price + $options_price) * $commission_amount) / 100, 2);
			} elseif ($commission_type == 2) { // fixed amount
				$item_commissions = $commission_amount;
			} elseif ($commission_type == 3) { // percentage to the product price
				$item_commissions = round(($price * $commission_amount) / 100, 2);
			} elseif ($commission_type == 4) { // percentage to the margin price
				$item_commissions = round((($price + $options_price - $buying_price) * $commission_amount) / 100, 2);
			}
			if ($item_commissions < 0) { $item_commissions = 0; }
		}
		if ($item_user_id) {
			// for merchant subtract fees to get commision
			$item_commissions = $price + $options_price - $item_commissions;
		}
		return $item_commissions;
	}

	function get_merchant_commission($item_user_id, $price, $options_price, $buying_price, $item_commision_type, $item_commision_amount)
	{
		return get_commission($item_user_id, "", $price, $options_price, $buying_price, $item_commision_type, $item_commision_amount);
	}

	function get_affiliate_commission($affiliate_user_id, $price, $options_price, $buying_price, $item_commision_type, $item_commision_amount)
	{
		return get_commission("", $affiliate_user_id, $price, $options_price, $buying_price, $item_commision_type, $item_commision_amount);
	}

	function get_payment_parameters($order_id, &$payment_parameters, &$pass_parameters, &$post_parameters, &$pass_data, &$variables, $order_step = "")
	{
		global $db, $table_prefix, $settings;
		global $parameters, $cc_parameters;
		global $datetime_show_format, $cart_items, $total_items;

		// get orders variables
		//$items_text = show_order_items($order_id, false);
		$orders_currency = get_setting_value($settings, "orders_currency", 0);

		$variables = array();
		$variables["charset"] = CHARSET;
		$variables["order_id"] = $order_id;
		$variables["session_id"] = session_id();
		$variables["remote_address"] = get_ip();
		$variables["site_url"] = get_setting_value($settings, "site_url", "");

		// get order data
		$sql = "SELECT * FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {

			$tax_prices_type = $db->f("tax_prices_type");

			$variables["user_ip"] = $db->f("remote_address");
			$variables["order_ip"] = $db->f("remote_address");
			$variables["initial_ip"] = $db->f("initial_ip");
			$variables["cookie_ip"] = $db->f("cookie_ip");

			$variables["transaction_id"] = $db->f("transaction_id");
			$variables["authorization_code"] = $db->f("authorization_code");

			// AVS data
			$variables["avs_response_code"] = $db->f("avs_response_code");
			$variables["avs_message"] = $db->f("avs_message");
			$variables["avs_address_match"] = $db->f("avs_address_match");
			$variables["avs_zip_match"] = $db->f("avs_zip_match");
			$variables["cvv2_match"] = $db->f("cvv2_match");

			// 3d fields
			$variables["secure_3d_check"] = $db->f("secure_3d_check");
			$variables["secure_3d_status"] = $db->f("secure_3d_status");
			$variables["secure_3d_md"] = $db->f("secure_3d_md");
			$variables["secure_3d_eci"] = $db->f("secure_3d_eci");
			$variables["secure_3d_cavv"] = $db->f("secure_3d_cavv");
			$variables["secure_3d_xid"] = $db->f("secure_3d_xid");
			$variables["authorization_code"] = $db->f("authorization_code");

			for ($i = 0; $i < sizeof($parameters); $i++) {
				if (in_array($parameters[$i], array("company_name", "province"))) {
					$variables[$parameters[$i]] = get_translation($db->f($parameters[$i]));
					$variables["delivery_" . $parameters[$i]] = get_translation($db->f("delivery_" . $parameters[$i]));
				} else {
					$variables[$parameters[$i]] = $db->f($parameters[$i]);
					$variables["delivery_" . $parameters[$i]] = $db->f("delivery_" . $parameters[$i]);
				}
			}

			for ($i = 0; $i < sizeof($cc_parameters); $i++) {
				$variables[$cc_parameters[$i]] = $db->f($cc_parameters[$i]);
			}

			prepare_user_name($variables["name"], $variables["first_name"], $variables["last_name"]);
			prepare_user_name($variables["delivery_name"], $variables["delivery_first_name"], $variables["delivery_last_name"]);
			prepare_user_name($variables["cc_name"], $variables["cc_first_name"], $variables["cc_last_lname"]);

			$address = $variables["address2"] ? ($variables["address1"] . " " . $variables["address2"]) : $variables["address1"];
			$delivery_address = $variables["delivery_address2"] ? ($variables["delivery_address1"] . " " . $variables["delivery_address2"]) : $variables["delivery_address1"];
			$address_number = (preg_match("/\d+/", $address, $match)) ? $match[0] : "";
			$delivery_address_number = (preg_match("/\d+/", $delivery_address, $match)) ? $match[0] : "";
			$variables["address"] = $address;
			$variables["address_number"] = $address_number;
			$variables["delivery_address"] = $delivery_address;
			$variables["delivery_address_number"] = $delivery_address_number;

			$order_placed_date = $db->f("order_placed_date", DATETIME);
			$cc_start_date = $db->f("cc_start_date", DATETIME);
			$cc_expiry_date = $db->f("cc_expiry_date", DATETIME);

			$timestamp = mktime($order_placed_date[HOUR], $order_placed_date[MINUTE], $order_placed_date[SECOND], $order_placed_date[MONTH], $order_placed_date[DAY], $order_placed_date[YEAR]);
			$vc = md5($order_id . $order_placed_date[HOUR] . $order_placed_date[MINUTE] . $order_placed_date[SECOND]);

			$payment_id = $db->f("payment_id");
			$user_id = $db->f("user_id");
			$affiliate_code = $db->f("affiliate_code");
			$order_currency_code = $db->f("currency_code");
			$order_currency_rate = $db->f("currency_rate");
			$goods_total = $db->f("goods_total");
			$total_discount = $db->f("total_discount");
			$total_discount_tax = $db->f("total_discount_tax");
			$properties_total = $db->f("properties_total");
			$properties_taxable = $db->f("properties_taxable");
			$shipping_type_desc = $db->f("shipping_type_desc");
			$shipping_cost = $db->f("shipping_cost");
			$shipping_taxable = $db->f("shipping_taxable");
			$total_quantity = $db->f("total_quantity");
			$weight_total = $db->f("weight_total");
			$tax_name = get_translation($db->f("tax_name"));
			$tax_percent = $db->f("tax_percent");
			$tax_cost = $db->f("tax_total");
			$processing_fee = $db->f("processing_fee");
			$order_total = $db->f("order_total");
			if($tax_prices_type == 1){
				$total_discount_excl_tax = $total_discount - $total_discount_tax;
				$total_discount_incl_tax = $total_discount;
			}else{
				$total_discount_excl_tax = $total_discount;
				$total_discount_incl_tax = $total_discount + $total_discount_tax;
			}
			$shipping_tax_percent = $tax_percent;
			$shipping_tax = get_tax_amount("", 0, $shipping_cost, !$shipping_taxable, $shipping_tax_percent);
			if($tax_prices_type == 1){
				$shipping_cost_excl_tax = $shipping_cost - $shipping_tax;
				$shipping_cost_incl_tax = $shipping_cost;
			}else{
				$shipping_cost_excl_tax = $shipping_cost;
				$shipping_cost_incl_tax = $shipping_cost + $shipping_tax;
			}

			// get payment rate for the selected gateway
			$payment_rate = get_payment_rate($payment_id, $order_currency_rate);

			// get numeric code
			$order_currency = get_currency($order_currency_code);
			$order_currency_value = $order_currency["value"];
			$order_currency_decimals = $order_currency["decimals"];

			$variables["vc"] = $vc;
			$variables["timestamp"] = $timestamp;
			$variables["order_placed_timestamp"] = $timestamp;
			$variables["order_placed_date"] = va_date($datetime_show_format, $order_placed_date);
			$variables["cc_start_date"] = ""; $variables["cc_start_date_short"] = "";
			$variables["cc_start_year"] = ""; $variables["cc_start_yyyy"] = ""; $variables["cc_start_month"] = "";
			if (is_array($cc_start_date)) {
				$variables["cc_start_date"] = va_date(array("MM"," / ","YYYY"), $cc_start_date);
				$variables["cc_start_date_short"] = va_date(array("MM"," / ","YY"), $cc_start_date);
				$variables["cc_start_year"] = va_date(array("YY"), $cc_start_date);
				$variables["cc_start_yyyy"] = va_date(array("YYYY"), $cc_start_date);
				$variables["cc_start_month"] = va_date(array("MM"), $cc_start_date);
			}
			$variables["cc_expiry_date"] = ""; $variables["cc_expiry_date_short"] = "";
			$variables["cc_expiry_year"] = ""; $variables["cc_expiry_yyyy"] = ""; $variables["cc_expiry_month"] = "";
			if (is_array($cc_expiry_date)) {
				$variables["cc_expiry_date"] = va_date(array("MM"," / ","YYYY"), $cc_expiry_date);
				$variables["cc_expiry_date_short"] = va_date(array("MM"," / ","YY"), $cc_expiry_date);
				$variables["cc_expiry_year"] = va_date(array("YY"), $cc_expiry_date);
				$variables["cc_expiry_yyyy"] = va_date(array("YYYY"), $cc_expiry_date);
				$variables["cc_expiry_month"] = va_date(array("MM"), $cc_expiry_date);
			}

			$variables["user_id"] = $user_id;
			$variables["affiliate_code"] = $affiliate_code;
			$variables["currency_code"] = $order_currency_code;
			$variables["currency_value"] = $order_currency_value;
			$variables["currency_rate"] = $order_currency_rate;
			$variables["goods_total"] = number_format($goods_total * $payment_rate, $order_currency_decimals, ".", "");
			$variables["total_discount"] = number_format($total_discount * $payment_rate, $order_currency_decimals, ".", "");
			$variables["total_discount_tax"] = number_format($total_discount_tax * $payment_rate, $order_currency_decimals, ".", "");
			$variables["total_discount_excl_tax"] = number_format($total_discount_excl_tax * $payment_rate, $order_currency_decimals, ".", "");
			$variables["total_discount_incl_tax"] = number_format($total_discount_incl_tax * $payment_rate, $order_currency_decimals, ".", "");
			$goods_with_discount = $goods_total - $total_discount;
			$variables["goods_with_discount"] = number_format($goods_with_discount * $payment_rate, $order_currency_decimals, ".", "");
			$variables["total_quantity"] = $total_quantity;
			$variables["weight_total"] = $weight_total;
			$variables["total_weight"] = $weight_total;
			$variables["properties_total"] = number_format($properties_total * $payment_rate, $order_currency_decimals, ".", "");
			$variables["properties_taxable"] = $properties_taxable;
			$variables["shipping_type_desc"] = $shipping_type_desc;
			$variables["shipping_cost"] = number_format($shipping_cost * $payment_rate, $order_currency_decimals, ".", "");
			$variables["shipping_taxable"] = $shipping_taxable;
			$variables["shipping_cost_excl_tax"] = number_format($shipping_cost_excl_tax * $payment_rate, $order_currency_decimals, ".", "");
			$variables["shipping_cost_incl_tax"] = number_format($shipping_cost_incl_tax * $payment_rate, $order_currency_decimals, ".", "");
			$variables["total_discount"] = $total_discount;
			$variables["tax_name"] = $tax_name;
			$variables["tax_percent"] = $tax_percent;
			$variables["tax_cost"] = $tax_cost;
			$variables["processing_fee"] = number_format($processing_fee * $payment_rate, $order_currency_decimals, ".", "");
			$variables["order_total"] = number_format($order_total * $payment_rate, $order_currency_decimals, ".", "");
			$variables["order_total_100"] = round($order_total * $payment_rate * 100, 0);

			$variables["company_select"] = get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($variables["company_id"], INTEGER, true, false)));
			$variables["state"] = ""; $variables["state_code"] = ""; 
			$sql = "SELECT * FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($variables["state_id"], INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$variables["state"] = get_translation($db->f("state_name"));
				$variables["state_code"] = $db->f("state_code");
			}
			if (strlen($variables["state_code"])) {
				$variables["state_code_or_province"] = $variables["state_code"];
				$variables["state_or_province"] = $variables["state"];
			} else {
				$variables["state_code_or_province"] = $variables["province"];
				$variables["state_or_province"] = $variables["province"];
			}
			$variables["country"] = ""; $variables["country_code"] = ""; 
			$variables["country_number"] = ""; $variables["country_code_alpha3"] = "";
			$sql = "SELECT * FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($variables["country_id"], INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$variables["country"] = get_translation($db->f("country_name"));
				$variables["country_code"] = $db->f("country_code");
				$variables["country_number"] = $db->f("country_iso_number");
				$variables["country_code_alpha3"] = $db->f("country_code_alpha3");
			}
			$variables["delivery_company_select"] = get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($variables["delivery_company_id"], INTEGER, true, false)));
			$variables["delivery_state"] = ""; $variables["delivery_state_code"] = ""; 
			$sql = "SELECT * FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($variables["delivery_state_id"], INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$variables["delivery_state"] = get_translation($db->f("state_name"));
				$variables["delivery_state_code"] = $db->f("state_code");
			}
			if (strlen($variables["delivery_state_code"])) {
				$variables["delivery_state_code_or_province"] = $variables["delivery_state_code"];
				$variables["delivery_state_or_province"] = $variables["delivery_state"];
			} else {
				$variables["delivery_state_code_or_province"] = $variables["delivery_province"];
				$variables["delivery_state_or_province"] = $variables["delivery_province"];
			}
			$variables["delivery_country"] = ""; $variables["delivery_country_code"] = ""; 
			$variables["delivery_country_number"] = ""; $variables["delivery_country_code_alpha3"] = "";
			$sql = "SELECT * FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($variables["delivery_country_id"], INTEGER, true, false);
			$db->query($sql);
			if ($db->next_record()) {
				$variables["delivery_country"] = get_translation($db->f("country_name"));
				$variables["delivery_country_code"] = $db->f("country_code");
				$variables["delivery_country_number"] = $db->f("country_iso_number");
				$variables["delivery_country_code_alpha3"] = $db->f("country_code_alpha3");
			}
			$variables["cc_type"] = get_db_value("SELECT credit_card_code FROM " . $table_prefix . "credit_cards WHERE credit_card_id=" . $db->tosql($variables["cc_type"], INTEGER, true, false));//, INTEGER));

			$cc_info = array();
			$setting_type = "credit_card_info_" . $payment_id;
			$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
			$db->query($sql);
			while ($db->next_record()) {
				$cc_info[$db->f("setting_name")] = $db->f("setting_value");
			}
			$cc_number_security = get_setting_value($cc_info, "cc_number_security", 1);
			$cc_code_security = get_setting_value($cc_info, "cc_code_security", 1);
			if ($order_step == "recurring") {
				if ($cc_number_security > 0) {
					$variables["cc_number"] = va_decrypt($variables["cc_number"]);
				}
				if ($cc_code_security > 0) {
					$variables["cc_security_code"] = va_decrypt($variables["cc_security_code"]);
				}
			} else {
				$variables["cc_number"] = get_session("session_cc_number");
				$variables["cc_security_code"] = get_session("session_cc_code");
			}
			$cc_number_len = strlen($variables["cc_number"]);
			if ($cc_number_len > 6) {
				$variables["cc_number_first"] = substr($variables["cc_number"], 0, 6);
			} else {
				$variables["cc_number_first"] = $variables["cc_number"];
			}
			if ($cc_number_len > 4) {
				$variables["cc_number_last"] = substr($variables["cc_number"], $cc_number_len - 4);
			} else {
				$variables["cc_number_last"] = $variables["cc_number"];
			}

			// get properties for order
			$order_properties = array();
			$sql  = " SELECT order_property_id, property_name, property_value, property_price, property_points_amount, tax_free ";
			$sql .= " FROM " . $table_prefix . "orders_properties ";
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$order_property_id = $db->f("order_property_id");
				$property_name = $db->f("property_name");
				$property_value = $db->f("property_value");
				$property_price = $db->f("property_price");
				$property_points_amount = $db->f("property_points_amount");
				$tax_free = $db->f("tax_free");
				$order_property_id = $db->f("order_property_id");

				$property_tax_percent = $tax_percent;
				$property_tax = get_tax_amount("", 0, $property_price, $tax_free, $property_tax_percent);
				if ($tax_prices_type == 1) {
					$property_price_excl_tax = $property_price - $property_tax;
					$property_price_incl_tax = $property_price;
				} else {
					$property_price_excl_tax = $property_price;
					$property_price_incl_tax = $property_price + $property_tax;
				}
				$order_properties[$order_property_id] = array(
					"property_name" => $property_name,
					"property_value" => $property_value, 
					"property_price" => $property_price * $payment_rate, 
					"property_tax" => $property_tax * $payment_rate, 
					"property_tax_percent" => $property_tax_percent, 
					"property_price_excl_tax" => $property_price_excl_tax * $payment_rate, 
					"property_price_incl_tax" => $property_price_incl_tax * $payment_rate,
					"property_points_amount" => $property_points_amount, 
					"tax_free" => $tax_free
				);
			}
			$variables["properties"] = $order_properties;
			// get items for order
			$order_items = array(); $total_quantity = 0; $total_items = 0;
			$sql  = " SELECT oi.order_item_id,oi.top_order_item_id,oi.item_id,oi.item_user_id,oi.item_type_id,";
			$sql .= " oi.item_status,oi.item_code,oi.manufacturer_code,oi.item_name, ";
			$sql .= " oi.is_recurring, oi.recurring_last_payment, oi.recurring_next_payment, oi.downloadable, ";
			$sql .= " oi.price,oi.tax_free,oi.tax_percent,oi.discount_amount,oi.real_price, oi.weight, ";
			$sql .= " oi.buying_price,oi.points_price,oi.reward_points,oi.reward_credits,oi.quantity, ";
			$sql .= " oi.is_shipping_free, oi.shipping_cost ";
			$sql .= " FROM " . $table_prefix . "orders_items oi ";
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$total_items++;
				$order_item_id = $db->f("order_item_id");
				$top_order_item_id = $db->f("top_order_item_id");
	  
				$item_name = get_translation($db->f("item_name"));
				$item_code = $db->f("item_code");
				$manufacturer_code = $db->f("manufacturer_code");
				$is_recurring = $db->f("is_recurring");
				$recurring_last_payment = $db->f("recurring_last_payment", DATETIME);
				$recurring_next_payment = $db->f("recurring_next_payment", DATETIME);

				$price = $db->f("price");
				$quantity = $db->f("quantity");
				$tax_free = $db->f("tax_free");
				$item_tax_percent = $db->f("tax_percent");
				if (!strlen($item_tax_percent)) {
					$item_tax_percent = $tax_percent;
				}
	  
				$total_quantity += $quantity;
				$item_total = $price * $quantity;
	  
				$item_tax = get_tax_amount("", 0, $price, $tax_free, $item_tax_percent);
				$item_tax_total = get_tax_amount("", 0, $item_total, $tax_free, $item_tax_percent);
	  
				if ($tax_prices_type == 1) {
					$price_excl_tax = $price - $item_tax;
					$price_incl_tax = $price;
					$price_excl_tax_total = $item_total - $item_tax_total;
					$price_incl_tax_total = $item_total;
				} else {
					$price_excl_tax = $price;
					$price_incl_tax = $price + $item_tax;
					$price_excl_tax_total = $item_total;
					$price_incl_tax_total = $item_total + $item_tax_total;
				}
				$order_items[$order_item_id] = array(
					"top_order_item_id" => $top_order_item_id,
					"item_id" => $db->f("item_id"), "item_type_id" => $db->f("item_type_id"),
					"item_code" => $item_code, "manufacturer_code" => $manufacturer_code,
					"item_name" => $item_name,
					"is_recurring" => $is_recurring, 
					"recurring_last_payment" => $recurring_last_payment, "recurring_next_payment" => $recurring_next_payment,
					"price" => $price * $payment_rate, 
					"quantity" => $quantity, 
					"item_total" => $item_total * $payment_rate,
					"price_excl_tax" => $price_excl_tax * $payment_rate, 
					"price_incl_tax" => $price_incl_tax * $payment_rate,
					"price_excl_tax_total" => $price_excl_tax_total * $payment_rate, 
					"price_incl_tax_total" => $price_incl_tax_total * $payment_rate,
					"item_tax" => $item_tax * $payment_rate, 
					"item_tax_total" => $item_tax_total * $payment_rate,
					"tax_free" => $tax_free, "tax_percent" => $item_tax_percent,
					"weight" => $db->f("weight"),
					"is_shipping_free" => $db->f("is_shipping_free"),
					"shipping_cost" => $db->f("shipping_cost") * $payment_rate, 
					"downloadable" => $db->f("downloadable"),
					"discount_amount" => $db->f("discount_amount") * $payment_rate,
					"buying_price" => $db->f("buying_price") * $payment_rate,
					"real_price" => $db->f("real_price") * $payment_rate,
					"points_price" => $db->f("points_price"),
					"reward_points" => $db->f("reward_points"),
					"reward_credits" => $db->f("reward_credits"),
					"components" => array(),
				);
				if ($top_order_item_id) {
					$order_items[$top_order_item_id]["components"][] = $order_item_id;
				}
			}

			// generate basket description
			$eol = get_eol();
			$items_text = ""; $items_html = "";
			foreach ($order_items as $id => $order_item) {
				if ($items_text) {
					$items_text .= $eol; $items_html .= "<br>";
				}
				$item_name = $order_item["item_name"];
				$quantity = $order_item["quantity"];
				$item_total = $order_item["item_total"];
				$items_text .= $item_name;
				$items_html .= $item_name;

				$properties = array(); $properties_text = ""; $properties_html = "";
				$sql  = " SELECT * FROM " . $table_prefix . "orders_items_properties ";
				$sql .= " WHERE order_item_id=" . $db->tosql($id, INTEGER);
    		$db->query($sql);
				while ($db->next_record()) {
					if ($properties_text) {
						$properties_text .= "; "; $properties_html .= "<br>";
					}
					$item_property_id = $db->f("item_property_id");
					$property_name = get_translation($db->f("property_name"));
					$property_value = get_translation($db->f("property_value"));
					$property_price = $db->f("additional_price");
					$property_weight = $db->f("additional_weight");
					$properties_text .= $property_name . ": " . $property_value; 
					$properties_html .= $property_name . ": " . $property_value; 
					$properties[$item_property_id] = array(
						"name" => $property_name,
						"value" => $property_value,
						"price" => $property_price,
						"weight" => $property_weight,
					);
				}
				if ($properties_text) {
					$items_text .= "(" . $properties_text . ")";
					$items_html .= "<br>" . $properties_html;
				}
				$items_text .= " - " . $quantity . " x " . currency_format($item_total, $order_currency);
				$items_html .= "<br>" . $quantity . " x " . currency_format($item_total, $order_currency);

				$order_items[$id]["properties"] = $properties;
				$order_items[$id]["properties_html"] = $properties_html;
				$order_items[$id]["properties_text"] = $properties_text;
			}
			$variables["items"] = $order_items;
			$variables["items_text"] = $items_text;
			$variables["items_html"] = $items_html;
			$variables["basket"] = $items_text; 
			$variables["total_quantity"] = $total_quantity;
			$variables["total_items"] = $total_items;

			$db->query("SELECT * FROM " . $table_prefix . "payment_systems WHERE payment_id=" . $db->tosql($payment_id, INTEGER));
			if ($db->next_record()) {
				$payment_name = get_translation($db->f("payment_name"));
				$user_payment_name = get_translation($db->f("user_payment_name"));
				if ($user_payment_name) {
					$payment_name = $user_payment_name;
				}
				$variables["payment_url"] = $db->f("payment_url");
				$variables["submit_method"] = $db->f("submit_method");
				$variables["payment_name"] = $db->f("payment_name");
				$variables["is_advanced"] = $db->f("is_advanced");
				$variables["advanced_url"] = $db->f("advanced_url");
				$variables["advanced_php_lib"] = $db->f("advanced_php_lib");
				$variables["failure_action"] = $db->f("failure_action");
				$variables["success_status_id"] = $db->f("success_status_id");
				$variables["pending_status_id"] = $db->f("pending_status_id");
				$variables["failure_status_id"] = $db->f("failure_status_id");
			}

			// Order custom fields
			$db->query("SELECT property_id, property_name, property_value  FROM " . $table_prefix . "orders_properties WHERE order_id=" . $db->tosql($order_id, INTEGER));
			while ($db->next_record()) {
				$property_id = $db->f("property_id");
				$property_name = get_translation($db->f("property_name"));
				$property_value = get_translation($db->f("property_value"));
				$variables["field_" . $property_id] = $property_value;
				$variables["field_name_" . $property_id] = $property_name;
				$variables["field_value_" . $property_id] = $property_value;
			}

			$fp_hash_name = ""; $epdqdata_name = ""; $protx_crypt_name = ""; $gate2shop_name = "";
			$db->query("SELECT * FROM " . $table_prefix . "payment_parameters WHERE payment_id=" . $db->tosql($payment_id, INTEGER));
			while ($db->next_record())
			{
				$parameter_source = $db->f("parameter_source");
				$parameter_name = $db->f("parameter_name");
				$parameter_type = $db->f("parameter_type");
				$not_passed = $db->f("not_passed");
				if (strtolower($parameter_name) == "x_fp_hash" && $parameter_type == "VARIABLE") {
					$fp_hash_name = $parameter_name;
					$fp_not_passed = $not_passed;
				} elseif (strtolower($parameter_name) == "epdqdata" && $parameter_type == "VARIABLE") {
					$epdqdata_name = $parameter_name;
					$epdqdata_not_passed = $not_passed;
				} elseif (strtolower($parameter_name) == "crypt" && strtolower($parameter_source) == "protx_crypt") {
					$protx_crypt_name = $parameter_name;
					$protx_crypt_not_passed = $not_passed;
				} elseif (strtolower($parameter_name) == "numberofitems"){
					$gate2shop_name = $parameter_name;
					$gate2shop_not_passed = $not_passed;
				} elseif (preg_match("/\{digit\}/", $parameter_name) || preg_match("/\{no_digit\}/", $parameter_name)) {
					foreach ($order_items as $id => $order_item) {
						$digit_parameter = str_replace("{digit}", ($i + 1), $parameter_name);
						$digit_parameter = str_replace("{no_digit}", "", $digit_parameter);
						if ($parameter_type == "CONSTANT") {
							$parameter_value = $parameter_source;
						} elseif ($parameter_type == "VARIABLE") {
							if (preg_match_all("/\{(\w+)\}/is", $parameter_source, $matches)) {
								$parameter_value = $parameter_source;
								for ($p = 0; $p < sizeof($matches[1]); $p++) {
									$l_source = strtolower($matches[1][$p]);
									if (isset($order_item[$l_source])) {
										$parameter_value = str_replace("{".$l_source."}", $order_item[$l_source], $parameter_value);
									}
								}
							} else {
								$l_source = strtolower($parameter_source);
								$parameter_value = isset($order_item[$l_source]) ? $order_item[$l_source] : $parameter_source;
							}
						}
						$payment_parameters[strtolower($digit_parameter)] = $parameter_value;
						$payment_parameters[$digit_parameter] = $parameter_value;
						if (!$not_passed) {
							$pass_data[strtolower($digit_parameter)] = $parameter_value;
							$pass_data[$digit_parameter] = $parameter_value;
							$pass_parameters[strtolower($digit_parameter)] = 1;
							$pass_parameters[$digit_parameter] = 1;
							if ($post_parameters) { $post_parameters .= "&"; }
							$post_parameters .= $digit_parameter . "=" . urlencode($parameter_value);
						} else {
							$pass_parameters[strtolower($digit_parameter)] = 0;
							$pass_parameters[$digit_parameter] = 0;
						}
					}
				} else {
					if ($parameter_type == "CONSTANT") {
						$parameter_value = $parameter_source;
					} elseif ($parameter_type == "VARIABLE") {
						if (preg_match_all("/\{(\w+)\}/is", $parameter_source, $matches)) {
							$parameter_value = $parameter_source;
							for ($p = 0; $p < sizeof($matches[1]); $p++) {
								$l_source = strtolower($matches[1][$p]);
								if (isset($variables[$l_source])) {
									$parameter_value = str_replace("{".$l_source."}", $variables[$l_source], $parameter_value);
								}
							}
						} else {
							$l_source = strtolower($parameter_source);
							$parameter_value = isset($variables[$l_source]) ? $variables[$l_source] : $parameter_source;
						}
					}
					$payment_parameters[strtolower($parameter_name)] = $parameter_value;
					$payment_parameters[$parameter_name] = $parameter_value;
					if (!$not_passed) {
						$pass_data[strtolower($parameter_name)] = $parameter_value;
						$pass_data[$parameter_name] = $parameter_value;
						$pass_parameters[strtolower($parameter_name)] = 1;
						$pass_parameters[$parameter_name] = 1;
						if ($post_parameters) { $post_parameters .= "&"; }
						$post_parameters .= $parameter_name . "=" . urlencode($parameter_value);
					} else {
						$pass_parameters[strtolower($parameter_name)] = 0;
						$pass_parameters[$parameter_name] = 0;
					}
				}
			}

			$additional_params = array();
			if (strlen($fp_hash_name)) {
				$x_login = isset($payment_parameters["x_login"]) ? $payment_parameters["x_login"] : "";
				$x_tran_key = isset($payment_parameters["x_tran_key"]) ? $payment_parameters["x_tran_key"] : "";
				$x_currency_code = isset($payment_parameters["x_currency_code"]) ? $payment_parameters["x_currency_code"] : "";

				$fp_hash_value = calculate_fp ($x_login, $x_tran_key, $variables["order_total"], $variables["order_id"], $variables["timestamp"], $x_currency_code);
				$payment_parameters[$fp_hash_name] = $fp_hash_value;
				if (!$fp_not_passed) {
					$pass_data[$fp_hash_name] = $fp_hash_value;
					$additional_params[$fp_hash_name] = $fp_hash_value;
				}
			}
			if (strlen($epdqdata_name) && !$epdqdata_not_passed) {
				include_once("./payments/epdq_cpi_encryption.php");
				$epdqdata_value = get_epdqdata($payment_parameters);
				$additional_params[$epdqdata_name] = $epdqdata_value;
			}
			if (strlen($gate2shop_name)) {
				include_once("./payments/gate2shop_functions.php");
				$gate2shop = get_gate2shop($payment_parameters);
				foreach ($gate2shop as $gate2shop_name => $gate2shop_value) {
					$additional_params[$gate2shop_name] = $gate2shop_value;
				}
			}
			if (strlen($protx_crypt_name) && !$protx_crypt_not_passed) {
				include_once("./payments/protx_form_encryption.php");
				$protx_crypt_value = get_protx_crypt($payment_parameters);
				$additional_params[$protx_crypt_name] = $protx_crypt_value;
			}
			foreach ($additional_params as $param_name => $param_value) {
				$pass_data[$param_name] = $param_value;
				if ($post_parameters) { $post_parameters .= "&"; }
				$post_parameters .= urlencode($param_name) . "=" . urlencode($param_value);
			}
		}
	}

	function parse_cart_columns($name_column, $price_excl_tax_column, $tax_percent_column, $tax_column, $price_incl_tax_column, $quantity_column, $price_excl_tax_total_column, $tax_total_column, $price_incl_tax_total_column, $item_image_column = 0)
	{
		global $t;
		if ($name_column) {
			$t->sparse("item_name_column", false);
		}
		if ($price_excl_tax_column) {
			$t->sparse("item_price_excl_tax_column", false);
		}
		if ($tax_percent_column) {
			$t->sparse("item_tax_percent_column", false);
		}
		if ($tax_column) {
			$t->sparse("item_tax_column", false);
		}
		if ($price_incl_tax_column) {
			$t->sparse("item_price_incl_tax_column", false);
		}
		if ($quantity_column) {
			$t->sparse("item_quantity_column", false);
		}
		if ($price_excl_tax_total_column) {
			$t->sparse("item_price_excl_tax_total_column", false);
		}
		if ($tax_total_column) {
			$t->sparse("item_tax_total_column", false);
		}
		if ($price_incl_tax_total_column) {
			$t->sparse("item_price_incl_tax_total_column", false);
		}
		if ($item_image_column) {
			$t->sparse("item_image_column", false);
		}
	}

	function get_delivery_details($order_info)
	{
		global $db, $table_prefix, $settings;
		$user_id = get_session("session_user_id");
		$delivery_details = array();
		$user_details = array();
		if ($user_id) {
			$sql  = " SELECT state_id, zip, country_id, city, ";
			$sql .= " delivery_state_id, delivery_zip, delivery_country_id, delivery_city, delivery_address1, delivery_address2"; //** Added address1 & 2 Egghead
			$sql .= " FROM " . $table_prefix . "users WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$user_details = $db->Record;
			}
		} else { // get default country and state from cookies
			$cookie_order_info = trim(get_cookie("cookie_order_info"));
			if (strlen($cookie_order_info)) {
				$cookie_pairs = explode("|", $cookie_order_info);
				for ($i = 0; $i < sizeof($cookie_pairs); $i++) {
					$cookie_line = trim($cookie_pairs[$i]);
					if (strlen($cookie_line)) {
						$cookie_values = explode("=", $cookie_line, 2);
						$user_details[$cookie_values[0]] = $cookie_values[1];
					}
				}
			}
		}
		//** Begin Added by Egghead Ventures
		// get address1
		if (get_setting_value($order_info, "show_delivery_address1", 0) == 1) {
			$delivery_details["address1"] = get_setting_value($user_details, "delivery_address1", "");
		} else {
			$delivery_details["address1"] = get_setting_value($user_details, "address1", "");
		}
		// get address1
		if (get_setting_value($order_info, "show_delivery_address2", 0) == 1) {
			$delivery_details["address2"] = get_setting_value($user_details, "delivery_address2", "");
		} else {
			$delivery_details["address2"] = get_setting_value($user_details, "address2", "");
		}
		//** End
		// get state_id from cookies
		if (get_setting_value($order_info, "show_delivery_state_id", 0) == 1) {
			$delivery_details["state_id"] = get_setting_value($user_details, "delivery_state_id", "");
		} else {
			$delivery_details["state_id"] = get_setting_value($user_details, "state_id", "");
		}
		// get postal_code from cookies
		if (get_setting_value($order_info, "show_delivery_zip", 0) == 1) {
			$delivery_details["postal_code"] = get_setting_value($user_details, "delivery_zip", "");
		} else {
			$delivery_details["postal_code"] = get_setting_value($user_details, "zip", "");
		}
		// get city from cookies
		if (get_setting_value($order_info, "show_delivery_city", 0) == 1) {
			$delivery_details["city"] = get_setting_value($user_details, "delivery_city", "");
		} else {
			$delivery_details["city"] = get_setting_value($user_details, "city", "");
		}
		// get country_id from cookies
		if (get_setting_value($order_info, "show_delivery_country_id", 0) == 1) {
			$delivery_details["country_id"] = get_setting_value($user_details, "delivery_country_id", "");
		} else {
			$delivery_details["country_id"] = get_setting_value($user_details, "country_id", "");
		}

		if (!strlen($delivery_details["country_id"])) {
			$delivery_details["country_id"] = get_setting_value($settings, "country_id", "");
		}
		if (!strlen($delivery_details["state_id"])) {
			$delivery_details["state_id"] = get_setting_value($settings, "state_id", "");
		}

		return $delivery_details;
	}

	function order_tax_rates($order_id)
	{
		global $db, $table_prefix, $settings, $db_type;

		$tax_rates = array();
		$order_tax_ids = "";
		$sql  = " SELECT order_tax_id, tax_id, tax_name, tax_percent, shipping_tax_percent ";
		$sql .= " FROM " . $table_prefix . "orders_taxes ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		if ($db_type == "mysql") {
			$sql .= " GROUP BY tax_id ";
		} else {
			$sql .= " GROUP BY tax_id, order_tax_id, tax_name, tax_percent, shipping_tax_percent ";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$tax_id = $db->f("tax_id");
			$order_tax_id = $db->f("order_tax_id");
			$tax_rate = array(
				"tax_name" => $db->f("tax_name"), "tax_percent" => $db->f("tax_percent"), 
				"shipping_tax_percent" => $db->f("shipping_tax_percent"), "types" => array(),
			);
			$tax_rates[$order_tax_id] = $tax_rate;
			if (strval($order_tax_ids) !== "") { $order_tax_ids .= ","; }
			$order_tax_ids .= $order_tax_id;
		}

		if (strlen($order_tax_ids)) {
			$sql  = " SELECT order_tax_id, item_type_id, tax_percent FROM " . $table_prefix . "orders_items_taxes ";
			$sql .= " WHERE order_tax_id IN (" . $db->tosql($order_tax_ids, INTEGERS_LIST) . ") ";
			$db->query($sql);
			while ($db->next_record()) {
				$order_tax_id = $db->f("order_tax_id");
				$item_type_id = $db->f("item_type_id");
				$tax_percent = $db->f("tax_percent");
				if (strlen($tax_percent)) {
					$tax_rates[$order_tax_id]["types"][$item_type_id] = $tax_percent;
				}
			}
		} else {
			// check old taxes
			$sql  = " SELECT o.tax_name, o.tax_percent ";
			$sql .= " FROM " . $table_prefix . "orders o ";
			$sql .= " WHERE o.order_id=" . $db->tosql($order_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$tax_name = get_translation($db->f("tax_name"));
				$tax_percent = $db->f("tax_percent");
				if (strlen($tax_name) || $tax_percent > 0) {
					$tax_rates[0] = array(
						"tax_name" => $tax_name, "tax_percent" => $tax_percent, 
						"shipping_tax_percent" => "", "types" => array(),
					);
				}
			}
			if (sizeof($tax_rates)) {
				$sql  = " SELECT item_type_id, tax_free, tax_percent FROM " . $table_prefix . "orders_items ";
				$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
				$db->query($sql);
				while ($db->next_record()) {
					$item_type_id = $db->f("item_type_id");
					$tax_free = $db->f("tax_free");
					$tax_percent = $db->f("tax_percent");
					if (!$tax_free && strlen($tax_percent)) {
						$tax_rates[0]["types"][$item_type_id] = $tax_percent;
					}
				}
			}
		}

		return $tax_rates;
	}

	function set_basket_tag($order_id, $type, $message)
	{
		global $settings, $t, $is_admin_path;
		if (strpos($message, "{basket}") !== false) {
			if ($is_admin_path) {
				$user_template_path = $settings["templates_dir"];
				if (preg_match("/^\.\//", $user_template_path)) {
					$user_template_path = str_replace("./", "../", $user_template_path);
				} elseif (!preg_match("/^\//", $user_template_path)) {
					$user_template_path = "../" . $user_template_path;
				}
				$t->set_template_path($user_template_path);
			}
			if ($type) {
				if (!$t->block_exists("basket_html")) {
					$t->set_file("basket_html", "email_basket.html");
				}
				if (!$t->var_exists("basket_html")) {
					$items_text = show_order_items($order_id, true, "");
					$t->parse("basket_html", false);
				}
				$t->set_var("basket", $t->get_var("basket_html"));
			} else {
				if (!$t->block_exists("basket_text")) {
					$t->set_file("basket_text", "email_basket.txt");
				}
				if (!$t->var_exists("basket_text")) {
					$items_text = show_order_items($order_id, true, "");
					$t->parse("basket_text", false);
				}
				$t->set_var("basket", $t->get_var("basket_text"));
			}
			if ($is_admin_path) {
				$t->set_template_path($settings["admin_templates_dir"]);
			}
		}
	}

	function unset_basket_tag()
	{
		global $t;
		$t->delete_var("basket_html");
		$t->delete_var("basket_text");
	}

	function cancel_subscription($order_item_id)
	{
		global $db, $table_prefix;

		$current_datetime = va_time();
		$current_date_ts = mktime (0, 0, 0, $current_datetime[MONTH], $current_datetime[DAY], $current_datetime[YEAR]);

		$sql  = " SELECT oi.order_id, oi.order_item_id, oi.item_name, oi.user_id, oi.user_type_id, ";
		$sql .= " oi.subscription_id, oi.price, oi.reward_credits, ";
		$sql .= " oi.subscription_start_date, oi.subscription_expiry_date ";
		$sql .= " FROM (" . $table_prefix . "orders_items oi ";
		$sql .= " INNER JOIN " . $table_prefix . "order_statuses os ON oi.item_status=os.status_id) ";
		$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
		$sql .= " AND oi.is_subscription=1 ";
		$sql .= " AND os.paid_status=1 ";
		$sql .= " AND subscription_expiry_date>" . $db->tosql($current_date_ts, DATETIME);
		$db->query($sql);
		if ($db->next_record()) {
			$order_id = $db->f("order_id");
			$order_item_id = $db->f("order_item_id");
			$subscription_id = $db->f("subscription_id");
			$item_name = $db->f("item_name");
			$user_id = $db->f("user_id");
			$user_type_id = $db->f("user_type_id");
			$price = $db->f("price");
			$reward_credits = $db->f("reward_credits");
			$subscription_sd = $db->f("subscription_start_date", DATETIME);
			$subscription_ed = $db->f("subscription_expiry_date", DATETIME);
			$subscription_sd_ts = va_timestamp($subscription_sd);
			$subscription_ed_ts = va_timestamp($subscription_ed);
			$subscription_days = intval(($subscription_ed_ts - $subscription_sd_ts) / 86400); // get int value due to possible 1 hour difference
			// check days difference and add current day as well
			$used_days = intval(($current_date_ts - $subscription_sd_ts) / 86400) + 1;
			$sql  = " SELECT setting_value FROM " . $table_prefix . "user_types_settings ";
			$sql .= " WHERE type_id=" . $db->tosql($user_type_id, INTEGER);
			$sql .= " AND setting_name='cancel_subscription'";
			$cancel_subscription = get_db_value($sql);
			if ($cancel_subscription == 1) {
				// return money to credits balance
				$credits_return = round((($price - $reward_credits)/ $subscription_days) * ($subscription_days - $used_days), 2); 
			} else {
				$credits_return = 0; 
			}

			// cancel order subscription
			$new_reward_credits = $reward_credits + $credits_return;
			$sql  = " UPDATE " . $table_prefix . "orders_items ";
			$sql .= " SET is_recurring=0, is_subscription=0, ";
			$sql .= " reward_credits=" . $db->tosql($new_reward_credits, NUMBER) . ",";
			$sql .= " subscription_expiry_date=" . $db->tosql($current_date_ts, DATETIME);
			$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
			$sql .= " AND is_subscription=1 ";
			$db->query($sql);

			// save event for subscription cancellation
			$r = new VA_Record($table_prefix . "orders_events");
			$r->add_textbox("order_id", INTEGER);
			$r->add_textbox("status_id", INTEGER);
			$r->add_textbox("admin_id", INTEGER);
			$r->add_textbox("order_items", TEXT);
			$r->add_textbox("event_date", DATETIME);
			$r->add_textbox("event_type", TEXT);
			$r->add_textbox("event_name", TEXT);
			$r->add_textbox("event_description", TEXT);

			// save subscription event
			$r->set_value("order_id", $order_id);
			$r->set_value("order_items", $order_item_id);
			$r->set_value("status_id", 0);
			$r->set_value("admin_id", get_session("session_admin_id"));
			$r->set_value("event_date", va_time());
			$r->set_value("event_type", "cancel_subscription");
			$r->set_value("event_name", $item_name);
			$r->insert_record();

			// update user commissions if reward credits amount changed
			if ($new_reward_credits != $reward_credits) {
				calculate_commissions_points($order_id, $order_item_id);
			}
		}
	}

?>