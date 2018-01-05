<?php
require_once dirname(__DIR__) . "/common/Product.php";
function basket($block_name)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings, $current_page;
	global $language_code;
	global $sc_errors;
	global $date_show_format;
	global $shopping_cart, $coupons, $tax_rates, $site_id;
	// ** EGGHEAD VENTURES ADD
	if($_SESSION["make"]) {
		$t->set_var("search_results", "<div align=\"center\" id=\"basket-search\">Back To Your Search Results For: <a href=\"/\">".$_SESSION["year"]." ".$_SESSION["make"]." ".$_SESSION["model"]."</a></div>");
	}
	// **
	$t->set_file("block_body", "block_basket.html");

	$t->set_var("basket_href",  get_custom_friendly_url("basket.php"));
	$t->set_var("current_href", $current_page);
	$t->set_var("checkout_href", get_custom_friendly_url("checkout.php"));
	$t->set_var("products_href", get_custom_friendly_url("products.php"));
	$t->set_var("cart_save_href", get_custom_friendly_url("cart_save.php"));
	$t->set_var("cart_retrieve_href", get_custom_friendly_url("cart_retrieve.php"));

	$confirm_add = get_setting_value($settings, "confirm_add", 1);
	$t->set_var("confirm_add", $confirm_add);

	// generate page link with query parameters
	$page = $current_page;
	$remove_parameters = array("rnd", "cart", "item_id", "type", "cart_id", "quantity", "new_quantity", "operation", "coupon_code", "coupon_id");
	$get_vars = isset($_GET) ? $_GET : $HTTP_GET_VARS;
	$query_string = get_query_string($get_vars, $remove_parameters, "", true);
	$page	.= $query_string;
	$page_link  = $page;
	$page_link .= strlen($query_string) ? "&" : "?";

	srand((double) microtime() * 1000000);
	$random_value = rand();

	$user_id = get_session("session_user_id");
	$user_info = get_session("session_user_info");
	$user_tax_free = get_setting_value($user_info, "tax_free", 0);
	$user_type_id = get_session("session_user_type_id");
	$user_discount_type = get_session("session_discount_type");
	$user_discount_amount = get_session("session_discount_amount");
	$tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
	$tax_prices = get_setting_value($settings, "tax_prices", 0);
	$tax_note = get_translation(get_setting_value($settings, "tax_note", ""));
	$tax_note_excl = get_translation(get_setting_value($settings, "tax_note_excl", ""));
	$show_item_code = get_setting_value($settings, "item_code_basket", 0);
	$show_manufacturer_code = get_setting_value($settings, "manufacturer_code_basket", 0);
	$product_no_image = get_setting_value($settings, "product_no_image", "");
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	$quantity_control_basket = get_setting_value($settings, "quantity_control_basket", "");
	$price_type = get_session("session_price_type");
	if ($price_type == 1) {
		$price_field = "trade_price";
		$sales_field = "trade_sales";
		$additional_price_field = "trade_additional_price";
	} else {
		$price_field = "price";
		$sales_field = "sales_price";
		$additional_price_field = "additional_price";
	}
	if ($user_tax_free) { $tax_prices = 0; }
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	// option price options
	$option_positive_price_right = get_setting_value($settings, "option_positive_price_right", ""); 
	$option_positive_price_left = get_setting_value($settings, "option_positive_price_left", ""); 
	$option_negative_price_right = get_setting_value($settings, "option_negative_price_right", ""); 
	$option_negative_price_left = get_setting_value($settings, "option_negative_price_left", "");

	$cart_image = get_setting_value($page_settings, "shopping_cart_preview", 0);
	$basket_item_name = get_setting_value($settings, "basket_item_name", 1);
	$basket_item_price = get_setting_value($settings, "basket_item_price", 1);
	$basket_item_tax_percent = get_setting_value($settings, "basket_item_tax_percent", 0);
	$basket_item_tax = get_setting_value($settings, "basket_item_tax", 0);
	$basket_item_price_incl_tax = get_setting_value($settings, "basket_item_price_incl_tax", 0);
	$basket_item_quantity = get_setting_value($settings, "basket_item_quantity", 1);
	$basket_item_price_total = get_setting_value($settings, "basket_item_price_total", 1);
	$basket_item_tax_total = get_setting_value($settings, "basket_item_tax_total", 1);
	$basket_item_price_incl_tax_total = get_setting_value($settings, "basket_item_price_incl_tax_total", 1);

	// points settings
	$points_system = get_setting_value($settings, "points_system", 0);
	$points_conversion_rate = get_setting_value($settings, "points_conversion_rate", 1);
	$points_decimals = get_setting_value($settings, "points_decimals", 0);
	$points_price_basket = get_setting_value($settings, "points_price_basket", 0);
	$reward_points_basket = get_setting_value($settings, "reward_points_basket", 0);
	$points_prices = get_setting_value($settings, "points_prices", 0);

	// credit settings
	$credit_system = get_setting_value($settings, "credit_system", 0);
	$reward_credits_users = get_setting_value($settings, "reward_credits_users", 0);
	$reward_credits_basket = get_setting_value($settings, "reward_credits_basket", 0);

	$remove_link = $page_link . "cart=RM&rnd=" . $random_value . "&cart_id=";
	$remove_all_link = $page_link . "cart=CLR&rnd=" . $random_value;

	$t->set_var("random_value", $random_value);
	$t->set_var("tax_note", $tax_note);
	$t->set_var("tax_note_excl", $tax_note_excl);
	if (isset($tax_rates["tax_name"])) {
		$t->set_var("tax_name", $tax_rates["tax_name"]);
	}

	// set up return page
	$rp_link = get_param("rp");
	if (!$rp_link) $rp_link = get_custom_friendly_url("products.php");
	$t->set_var("rp_href", htmlspecialchars($rp_link));

	$coupon_id = "";
	$coupon_errors = "";
	$operation = get_param("operation");
	$coupons_enable = get_setting_value($settings, "coupons_enable");
	// add or remove coupon
	if ($coupons_enable && $operation == "add") {
		$coupon_code = trim(get_param("coupon_code"));
		if (!strlen($coupon_code)) {
			$required_msg = str_replace("{field_name}", COUPON_CODE_FIELD, REQUIRED_MESSAGE);
			$coupon_errors = $required_msg;
		} else {
			$sql  = " SELECT c.* FROM ";
			if (isset($site_id)) {
				$sql .= "(";
			}
			$sql .= $table_prefix . "coupons c";
			if (isset($site_id)) {
					$sql .= " LEFT JOIN  " . $table_prefix . "coupons_sites s ON s.coupon_id=c.coupon_id)";
			}
			$sql .= " WHERE c.coupon_code=" . $db->tosql($coupon_code, TEXT);
			if (isset($site_id)) {
				$sql .= " AND (c.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
			} else {
				$sql .= " AND c.sites_all=1 ";
			}				
			$db->query($sql);
			if ($db->next_record()) {
				check_add_coupons(true, $coupon_code, $coupon_errors);
			} else {
				$coupon_errors = COUPON_NOT_FOUND_MSG;
			}
		}
	} elseif ($operation == "rm_coupon") {
		$coupon_id = get_param("coupon_id");
		remove_coupon($coupon_id);
	} else {
		// check coupons without adding new coupons
		check_coupons(false);
	}
	// get shopping cart and order coupons
	$shopping_cart = get_session("shopping_cart");

	$coupons = get_session("session_coupons");
	$quantities_discounts = array(); 

	// check if there are any coupon with order tax free
	$order_tax_free = false;
	if (is_array($coupons)) {
		foreach ($coupons as $coupon_id => $coupon_info) {
			$coupon_order_tax_free = $coupon_info["ORDER_TAX_FREE"];
			if ($coupon_order_tax_free) {
				$order_tax_free = true;
				break;
			}
		}
	}

	// prepare total products quantities
	$goods_total = 0;
	$goods_total_excl_tax = 0;
	$goods_total_incl_tax = 0;
	$goods_tax_total = 0;
	$total_reward_points = 0; $total_reward_credits = 0;
	$total_quantity = 0;
	$total_items = 0;
	$columns_colspan = 0;
	if (is_array($shopping_cart))
	{
		$t->set_var("empty", "");
		$t->set_var("items", "");
		$t->set_var("coupons", "");

		if ($cart_image) {
			$columns_colspan++;
			$t->parse("item_image_header", false);
		}
		if ($basket_item_name) {
			$columns_colspan++;
			$t->parse("item_name_header", false);
		}
		if ($basket_item_price) {
			$columns_colspan++;
			$t->parse("item_price_header", false);
		}
		if ($basket_item_tax_percent) {
			$columns_colspan++;
			$t->parse("item_tax_percent_header", false);
		}
		if ($basket_item_tax) {
			$columns_colspan++;
			$t->parse("item_tax_header", false);
		}
		if ($basket_item_price_incl_tax) {
			$columns_colspan++;
			$t->parse("item_price_incl_tax_header", false);
		}
		if ($basket_item_quantity) {
			$columns_colspan++;
			$t->parse("item_quantity_header", false);
		}
		if ($basket_item_price_total) {
			$t->parse("item_price_total_header", false);
		}
		if ($basket_item_tax_total) {
			$t->parse("item_tax_total_header", false);
		}
		if ($basket_item_price_incl_tax_total) {
			$t->parse("item_price_incl_tax_total_header", false);
		}
		$t->set_var("columns_colspan", $columns_colspan);

		foreach ($shopping_cart as $cart_id => $item) {
			$item_id = $item["ITEM_ID"];
			$quantity = $item["QUANTITY"];
			$subscription_id = isset($item["SUBSCRIPTION_ID"]) ? $item["SUBSCRIPTION_ID"] : "";
			// check subscription
			if ($subscription_id) {
				$sql  = " SELECT subscription_name, subscription_fee, subscription_period, subscription_interval ";
				$sql .= " FROM " . $table_prefix . "subscriptions ";
				$sql .= " WHERE subscription_id=" . $db->tosql($subscription_id, INTEGER) . " AND is_active=1 ";
				$db->query($sql);
				if ($db->next_record()) {
					$total_items++;
					$subscription_fee = $db->f("subscription_fee");
					$subscription_name = get_translation($db->f("subscription_name"));
					$subscription_period = $db->f("subscription_period");
					$subscription_interval = $db->f("subscription_interval");
					$t->set_var("item_code_block", "");
					$t->set_var("manufacturer_code_block", "");
					$t->set_var("item_error", "");
					$t->set_var("components_block", "");
					$t->set_var("item_name", $subscription_name);
					$t->set_var("product_code", "");
					$t->set_var("product_url", "#");
					$t->set_var("quantity", $quantity);
					$t->set_var("properties_values", "");

					// remove link
					$t->set_var("item_type", SUBSCRIPTION_MSG);
					$t->set_var("REMOVE_FROM_CART_MSG", REMOVE_FROM_CART_MSG);
					$t->set_var("remove_href", $remove_link . $cart_id);

					$tax_free = ($user_tax_free || $order_tax_free);
					$tax_amount = get_tax_amount($tax_rates, 0, $subscription_fee, $tax_free, $tax_percent);
					if ($tax_prices_type == 1) {
						$price_incl_tax = $subscription_fee;
						$price_excl_tax = $subscription_fee - $tax_amount;
					} else {
						$price_incl_tax = $subscription_fee + $tax_amount;
						$price_excl_tax = $subscription_fee;
					}

					$price_excl_tax_total = $price_excl_tax * $quantity;
					$price_incl_tax_total = $price_incl_tax * $quantity;
					$item_tax_total = $tax_amount * $quantity;

					$goods_total_excl_tax += $price_excl_tax_total;
					$goods_total_incl_tax += $price_incl_tax_total;
					$goods_tax_total += $item_tax_total;

					$total_quantity += 1;

					if ($cart_image) {
						$t->set_var("image_preview", "");
						$t->parse("item_image_column", false);
					}
					if ($basket_item_name) {
						$t->parse("item_name_column", false);
					}
					if ($basket_item_price) {
						$t->set_var("price_excl_tax", currency_format($price_excl_tax));
						$t->parse("item_price_column", false);
					}
					if ($basket_item_tax_percent) {
						$t->set_var("tax_percent", $tax_percent . "%");
						$t->parse("item_tax_percent_column", false);
					}
					if ($basket_item_tax) {
						$t->set_var("tax", currency_format($tax_amount));
						$t->parse("item_tax_column", false);
					}
					if ($basket_item_price_incl_tax) {
						$t->set_var("price_incl_tax", currency_format($price_incl_tax));
						$t->parse("item_price_incl_tax_column", false);
					}
					if ($basket_item_quantity) {
						$t->parse("item_quantity_column", false);
					}

					if ($basket_item_price_total) {
						$t->set_var("price_excl_tax_total", currency_format($price_excl_tax_total));
						$t->parse("item_price_excl_tax_total_column", false);
					}
					if ($basket_item_tax_total) {
						$t->set_var("tax_total", currency_format($item_tax_total));
						$t->parse("item_tax_total_column", false);
					}
					if ($basket_item_price_incl_tax_total) {
						$t->set_var("price_incl_tax_total", currency_format($price_incl_tax_total));
						$t->parse("item_price_incl_tax_total_column", false);
					}
					
					$t->parse("items", true);

				}
				continue;
			}

			$properties_more = $item["PROPERTIES_MORE"];
			if ($properties_more > 0) {
				continue;
			}
			$properties = $item["PROPERTIES"];
			$components = $item["COMPONENTS"];
			$item_coupons = isset($item["COUPONS"]) ? $item["COUPONS"] : "";

			$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
			product_image_fields($cart_image, $image_type_name, $image_field, $image_alt_field, $watermark, $product_no_image);

			$sql  = " SELECT item_name, friendly_url, item_type_id, item_code, manufacturer_code, manufacturer_id, short_description, ";
			$sql .= " " . $price_field . ", is_price_edit, is_sales, " . $sales_field . ", buying_price, ";
			$sql .= " is_points_price, points_price, reward_type, reward_amount, credit_reward_type, credit_reward_amount, ";
			$sql .= " tax_free, stock_level, use_stock_level, hide_out_of_stock, disable_out_of_stock, ";
			//***Egghead Ventures Start***//
			$sql .= " shipping_in_stock, shipping_out_stock, ";
			//***Egghead Ventures End***//
			$sql .= " min_quantity, max_quantity, quantity_increment ";
			if ($image_field) { $sql .= " , " . $image_field; }
			if ($image_alt_field) { $sql .= " , " . $image_alt_field; }
			$sql .= " FROM " . $table_prefix . "items ";
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);

			$db->query($sql);
			if ($db->next_record())
			{
				$total_items++;
				$item_type_id = $db->f("item_type_id");
				$item_code = $db->f("item_code");
				$manufacturer_code = $db->f("manufacturer_code");
				//$item_image = $db->f($image_field);
				$price = $db->f($price_field);
				$is_price_edit = $db->f("is_price_edit");
				if ($is_price_edit) {
					$price = $item["PRICE"];
				}
				$is_sales = $db->f("is_sales");
				$sales_price = $db->f($sales_field);
				$buying_price = $db->f("buying_price");

				// points data
				$is_points_price = $db->f("is_points_price");
				$points_price = $db->f("points_price");
				$reward_type = $db->f("reward_type");
				$reward_amount = $db->f("reward_amount");
				$credit_reward_type = $db->f("credit_reward_type");
				$credit_reward_amount = $db->f("credit_reward_amount");
				if (!strlen($is_points_price)) {
					$is_points_price = $points_prices;
				}

				$item_name = get_translation($db->f("item_name"));
				$item_name_js = str_replace("'", "\\'", htmlspecialchars($item_name));
				$short_description = get_translation($db->f("short_description"));
				$tax_free = ($user_tax_free || $order_tax_free || $db->f("tax_free"));
				$stock_level = $db->f("stock_level");
				$use_stock_level = $db->f("use_stock_level");
				$hide_out_of_stock = $db->f("hide_out_of_stock");
				$disable_out_of_stock = $db->f("disable_out_of_stock");
				$min_quantity = $db->f("min_quantity");
				$max_quantity = $db->f("max_quantity");
				$quantity_increment = $db->f("quantity_increment");
				$item_image = ""; $item_image_alt = ""; 
				$image_exists = false;
				if ($image_field) {
					$item_image = $db->f($image_field);	
					$item_image_alt = get_translation($db->f($image_alt_field));
                    $prod = new Product(['item_code'=>$db->f('item_code'), 'manufacturer_id'=>$db->f('manufacturer_id')]);
                    if (!$item_image){
                        $item_image = $prod->default_img();
                    }
                    if (!strlen($item_image)) {
						$item_image = $product_no_image;
					} else {
						$image_exists = true;
					}
				}
				$product_friendly_url = $db->f("friendly_url");
				if ($friendly_urls && $product_friendly_url) {
					$product_url = $product_friendly_url . $friendly_extension;
				} else {
					$product_url = get_custom_friendly_url("product_details.php") . "?item_id=" . $item_id;
				}
				$properties_discount = 0;
				$discount_applicable = 1;
				if (!$is_price_edit) {
					$price = calculate_price($price, $is_sales, $sales_price);
					$quantity_price = get_quantity_price($item_id, $quantity);
					if (sizeof($quantity_price)) {
						$price = $quantity_price[0];
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
				//***Egghead Ventures Start ***//
				if ($use_stock_level == 1 && $stock_level > 0) {
					$availability = $db->f("shipping_in_stock");
				} else if ($use_stock_level != 1) {
					$availability = $db->f("shipping_out_stock");
				} else {
					$availability = $db->f("shipping_out_stock");
				}
				$sql = "Select shipping_time_desc from ".$table_prefix."shipping_times where shipping_time_id=".$availability;
				$db->query($sql);
				if ($db->next_record()) {
					$shipping_time_desc = $db->f("shipping_time_desc");
				}
				$t->set_var("availability",$shipping_time_desc);
				//***Egghead Ventures End ***//
				}

				// check product subcomponents
				$components_price = 0; $components_tax_amount = 0; 
				$components_total_excl = 0; $components_total_incl = 0; $components_total_tax = 0;
				$components_excl_tax = 0; $components_incl_tax = 0;  $components_tax_amount = 0;
				$components_points_price = 0; $components_reward_points = 0; $components_reward_credits = 0;
				if (is_array($components) && sizeof($components) > 0) {
					$t->set_var("components", "");
					foreach ($components as $property_id => $component_values) {
						foreach ($component_values as $item_property_id => $component) {
							$property_type_id = $component["type_id"];
							$sub_item_id = $component["sub_item_id"];
							$sub_quantity = $component["quantity"];
							if ($property_type_id == 2) {
								$sql  = " SELECT pr.property_name AS component_name, pr.quantity, pr.quantity_action, ";
								$sql .= " pr.".$additional_price_field." AS component_price, ";
								$sql .= " i.item_type_id, i.buying_price, i." . $price_field . ", i.is_sales, i." . $sales_field . ", i.tax_free, ";
								$sql .= " i.is_points_price, i.points_price, i.reward_type, i.reward_amount, i.credit_reward_type, i.credit_reward_amount, ";
								$sql .= " i.stock_level, i.use_stock_level, i.hide_out_of_stock, i.disable_out_of_stock, i.big_image ";
								$sql .= " FROM (" . $table_prefix . "items_properties pr ";
								$sql .= " INNER JOIN " . $table_prefix . "items i ON pr.sub_item_id=i.item_id)";
								$sql .= " WHERE pr.property_id=" . $db->tosql($property_id, INTEGER);

							} else {
								$sql  = " SELECT ipv.property_value AS component_name, ipv.quantity, pr.quantity_action, ";
								$sql .= " ipv.".$additional_price_field." AS component_price, ";
								$sql .= " i.item_type_id, i.buying_price, i." . $price_field . ", i.is_sales, i." . $sales_field . ", i.tax_free, ";
								$sql .= " i.is_points_price, i.points_price, i.reward_type, i.reward_amount, i.credit_reward_type, i.credit_reward_amount, ";
								$sql .= " i.stock_level, i.use_stock_level, i.hide_out_of_stock, i.disable_out_of_stock, i.big_image ";
								$sql .= " FROM ((" . $table_prefix . "items_properties_values ipv ";
								$sql .= " INNER JOIN " . $table_prefix . "items_properties pr ON ipv.property_id=pr.property_id) ";
								$sql .= " INNER JOIN " . $table_prefix . "items i ON ipv.sub_item_id=i.item_id)";
								$sql .= " WHERE ipv.item_property_id=" . $db->tosql($item_property_id, INTEGER);

							}
							$db->query($sql);
							if ($db->next_record()) {
								$sub_type_id = $db->f("item_type_id");
								$sub_tax_free = ($user_tax_free || $order_tax_free || $db->f("tax_free"));
								$component_name = $db->f("component_name");
								$component_quantity = $db->f("quantity");
								$component_qty_action = $db->f("quantity_action");
								if ($component_quantity < 1) { $component_quantity = 1; }
								$sub_buying = $db->f("buying_price");
								$component_price = $db->f("component_price");
								// points data
								$sub_is_points_price = $db->f("is_points_price");
								$sub_points_price = $db->f("points_price");
								$sub_reward_type = $db->f("reward_type");
								$sub_reward_amount = $db->f("reward_amount");
								$sub_credit_reward_type = $db->f("credit_reward_type");
								$sub_credit_reward_amount = $db->f("credit_reward_amount");
								if (!strlen($sub_is_points_price)) {
									$sub_is_points_price = $points_prices;
								}

								if (!strlen($component_price)) {
									$sub_price = $db->f($price_field);
									$sub_is_sales = $db->f("is_sales");
									$sub_sales = $db->f($sales_field);

									// check price for selected quantity for current user
									$sub_user_price  = ""; 
									$sub_user_action = 0;							
									$q_prices    = get_quantity_price($sub_item_id, $quantity * $sub_quantity);
									if ($q_prices) {
										$sub_user_price   = $q_prices[0];
										$sub_user_action  = $q_prices[2];
									}
																		
									$sub_prices = get_product_price($sub_item_id, $sub_price, $sub_buying, $sub_is_sales, $sub_sales, $sub_user_price, $sub_user_action, $user_discount_type, $user_discount_amount);
									$component_price = $sub_prices["base"];
									// update information in the cart as well
									if ($sub_is_sales && $sub_sales > 0) {
										$component["base_price"] = $sub_sales;
									} else {
										$component["base_price"] = $sub_price;
									}
									$component["buying"] = $db->f("buying_price");
									$component["user_price"] = $sub_user_price;
									$component["user_price_action"] = $sub_user_action;
									$shopping_cart[$cart_id]["COMPONENTS"][$property_id][$item_property_id] = $component;
								}
								if ($sub_points_price <= 0) {
									$sub_points_price = $component_price * $points_conversion_rate;
								}
								$sub_reward_points = calculate_reward_points($sub_reward_type, $sub_reward_amount, $component_price, $sub_buying, $points_conversion_rate, $points_decimals);
								$sub_reward_credits = calculate_reward_credits($sub_credit_reward_type, $sub_credit_reward_amount, $component_price, $sub_buying);

								$component_tax_amount = set_tax_price($sub_item_id, $sub_type_id, $component_price, 0, $sub_tax_free);
								if ($tax_prices_type == 1) {
									$component_incl = $component_price;
									$component_excl = $component_price - $component_tax_amount;
								} else {
									$component_incl = $component_price + $component_tax_amount;
									$component_excl = $component_price;
								}

								if ($component_qty_action == 2) {
									$components_price += ($component_price * $component_quantity / $quantity);

									$components_incl_tax += ($component_incl * $component_quantity / $quantity);
									$components_excl_tax += ($component_excl * $component_quantity / $quantity);
									$components_tax_amount += ($component_tax_amount * $component_quantity / $quantity);
									$components_total_incl += ($component_incl * $component_quantity);
									$components_total_excl += ($component_excl * $component_quantity);
									$components_total_tax += ($component_tax_amount * $component_quantity);

									$components_points_price += ($sub_points_price * $component_quantity); 
									$components_reward_points += ($sub_reward_points * $component_quantity);
									$components_reward_credits += ($sub_reward_credits * $component_quantity);
								} else {
									$components_price += ($component_price * $component_quantity);

									$components_incl_tax += ($component_incl * $component_quantity);
									$components_excl_tax += ($component_excl * $component_quantity);
									$components_tax_amount += ($component_tax_amount * $component_quantity);
									$components_total_incl += ($component_incl * $component_quantity * $quantity);
									$components_total_excl += ($component_excl * $component_quantity * $quantity);
									$components_total_tax += ($component_tax_amount * $component_quantity * $quantity);
									$components_points_price += ($sub_points_price * $component_quantity * $quantity); 
									$components_reward_points += ($sub_reward_points * $component_quantity * $quantity);
									$components_reward_credits += ($sub_reward_credits * $component_quantity * $quantity);
								}

								$selection_name = "";
								if (isset($item["PROPERTIES_INFO"][$property_id])) {
									$selection_name = $item["PROPERTIES_INFO"][$property_id]["NAME"] . ": ";
								} 
								$t->set_var("selection_name", $selection_name);
								if ($component_qty_action == 2) {
									$t->set_var("component_quantity", $component_quantity);
								} else {
									$t->set_var("component_quantity", $component_quantity * $quantity);
								}
								$t->set_var("component_name", $component_name);

								$image  = $db->f("big_image");
								if ($image) {
									$component_icon = product_image_icon($sub_item_id, $component_name, $image, 3);
								} else {
									$component_icon = "";
								}
								$t->set_var("component_icon", $component_icon);
								
								if ($tax_prices == 0 || $tax_prices == 1) {
									$component_shown_price = $component_excl;
								} else {
									$component_shown_price = $component_incl;
								}

								if ($component_price > 0) {
									$t->set_var("component_price", $option_positive_price_right . currency_format($component_shown_price) . $option_positive_price_left);
								} elseif ($component_price < 0) {
									$t->set_var("component_price", $option_negative_price_right . currency_format(abs($component_shown_price)) . $option_negative_price_left);
								} else {
									$t->set_var("component_price", "");
								}
								$t->sparse("components", true);
							}
						}
					}
					$t->sparse("components_block", false);
				} else {
					$t->set_var("components_block", "");
				}

				// set item variables into html
				$t->set_var("item_name", $item_name);

				if ($item_image)
				{
					if (preg_match("/^http\:\/\//", $item_image)) {
						$image_size = "";
					} else {
						$image_size = @getimagesize($item_image);
						if ($image_exists && ($watermark || $restrict_products_images)) {
							$item_image = "image_show.php?item_id=".$item_id."&type=".$image_type_name."&vc=".md5($item_image);
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
				
				$t->set_var("short_description", $short_description);
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
				
				$t->set_var("product_url", $product_url);
				if (!$min_quantity) { $min_quantity = 1; }
				if (!$quantity_increment) { $quantity_increment = 1; }
				if (strtoupper($quantity_control_basket) == "LISTBOX")
				{
					$increment_limit = intval($quantity / $quantity_increment) + 8;
					$show_max_quantity = $min_quantity + ($quantity_increment * $increment_limit);
					if ($max_quantity > 0 && $show_max_quantity > $max_quantity) {
						$show_max_quantity = $max_quantity;
					}
					if (($disable_out_of_stock || $hide_out_of_stock) && $show_max_quantity > $stock_level && $use_stock_level) {
						$show_max_quantity = $stock_level;
					}
					// load data for listbox
					$quantity_control  = "<select name=\"quantity\" ";
					$quantity_control .= " onChange=\"return changeListbox(";
					$quantity_control .= "'" . $cart_id . "', this, ";
					$quantity_control .= "'" . $item_name_js . "', '" . $quantity . "')\">";
					for ($i = $min_quantity; $i <= $show_max_quantity; $i = $i + $quantity_increment) {
						$selected = ($i == $quantity) ? "selected" : "";
						$quantity_control .= "<option " . $selected . " value=\"" . $i ."\">" . $i . "</option>";
					}
					$quantity_control .= "</select>";
					$t->set_var("quantity", $quantity_control);
				}
				elseif (strtoupper($quantity_control_basket) == "TEXTBOX")
				{
					$quantity_textbox = "<input type=\"text\" name=\"quantity\" class=\"field\"";
					$quantity_textbox .= " value=\"" . $quantity . "\" size=\"4\" maxlength=\"6\"";
					$quantity_textbox .= " onChange=\"changeTextbox(";
					$quantity_textbox .= "'" . $cart_id . "', this, ";
					$quantity_textbox .= "'" . $item_name_js . "', '" . $quantity . "')\"";
					$quantity_textbox .= " onKeyPress=\"return checkChanges(event, ";
					$quantity_textbox .= "'" . $cart_id . "', this, ";
					$quantity_textbox .= "'" . $item_name_js . "', '" . $quantity . "')\"";
					$quantity_textbox .= ">";

					$t->set_var("quantity", $quantity_textbox);
				} else {
					$t->set_var("quantity", $quantity);
				}

				// show properties if they available
				$properties_values = "";
				$properties_price = 0; $properties_buying_price = 0;
				if (is_array($properties))
				{
					reset($properties);
					while(list($property_id, $property_values) = each($properties))
					{
						$sql  = " SELECT property_type_id, property_name, control_type, ";
						$sql .= " property_price_type, additional_price, trade_additional_price, free_price_type, free_price_amount ";
						$sql .= " FROM " . $table_prefix . "items_properties ";
						$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER);
						$db->query($sql);
						if ($db->next_record()) {
							$property_type_id = $db->f("property_type_id");
							// show only product options and subcomponents separately
							if ($property_type_id == 1) {
								$property_name = get_translation($db->f("property_name"));
								$control_type = $db->f("control_type");

								$property_price_type = $db->f("property_price_type");
								$additional_price = $db->f($additional_price_field);
								$free_price_type = $db->f("free_price_type");
								$free_price_amount = $db->f("free_price_amount");
								$property_price = calculate_control_price($item["PROPERTIES_INFO"][$property_id]["VALUES"], $item["PROPERTIES_INFO"][$property_id]["TEXT"], $property_price_type, $additional_price, $free_price_type, $free_price_amount);
								$properties_price += $property_price;

								if (strtoupper($control_type) == "LISTBOX" || strtoupper($control_type) == "RADIOBUTTON"
									|| strtoupper($control_type) == "CHECKBOXLIST" || strtoupper($control_type) == "TEXTBOXLIST") {
									$values_list = "";
									for($pv = 0; $pv < sizeof($property_values); $pv++) {
										$sql  = " SELECT property_value, ".$additional_price_field.", percentage_price, buying_price ";
										$sql .= " FROM " . $table_prefix . "items_properties_values ipv ";
										$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER);
										$sql .= " AND item_property_id=" . $db->tosql($property_values[$pv], INTEGER);
										$db->query($sql);
										if ($db->next_record()) {
											$additional_price = $db->f($additional_price_field);
											$percentage_price = $db->f("percentage_price");
											if ($percentage_price && $price) {
												$additional_price += round(($price * $percentage_price) / 100, 2);
											}
											$property_buying_price = $db->f("buying_price");
											if ($properties_discount > 0) {
												$additional_price -= round(($additional_price * $properties_discount) / 100, 2);
											}
											if ($discount_applicable && $user_discount_type == 1) {
												$additional_price -= round(($additional_price * $user_discount_amount) / 100, 2);
											} elseif ($discount_applicable && $user_discount_type == 4) {
												$additional_price -= round((($additional_price - $property_buying_price) * $user_discount_amount) / 100, 2);
											}
											$property_price += $additional_price;
											$properties_price += $additional_price;
											$properties_buying_price += $property_buying_price;
											if (strtoupper($control_type) == "TEXTBOXLIST") {
												$values_list .= "<br>";
												$values_list .= get_translation($db->f("property_value")) . ": ";
												$values_list .= $item["PROPERTIES_INFO"][$property_id]["TEXT"][$property_values[$pv]];
											} else {
												if ($values_list) { $values_list .= ", "; }
												$values_list .= get_translation($db->f("property_value"));
											}
										}
									}
									$property_tax = get_tax_amount($tax_rates, $item_type_id, $property_price, $tax_free, $tax_percent);
									if ($tax_prices_type == 1) {
										$option_price_incl = $property_price;
										$option_price_excl = $property_price - $property_tax;
									} else {
										$option_price_incl = $property_price + $property_tax;
										$option_price_excl = $property_price;
									}

									if ($tax_prices == 2 || $tax_prices == 3) {
										$shown_price = $option_price_incl;
									} else {
										$shown_price = $option_price_excl;
									}

									if (strtoupper($control_type) == "TEXTBOXLIST") {
										$properties_values .= "<br>" . $property_name . ": ";
									} else {
										$properties_values .= "<br>" . $property_name . ": " . $values_list;
									}
									if ($property_price > 0) {
										$properties_values .= $option_positive_price_right . currency_format($shown_price) . $option_positive_price_left;
									} elseif ($property_price < 0) {
										$properties_values .= $option_negative_price_right . currency_format(abs($shown_price)) . $option_negative_price_left;
									}
									if (strtoupper($control_type) == "TEXTBOXLIST") {
										$properties_values .= $values_list;
									}
								} elseif ($property_values[0]) {
									$property_value = get_translation($property_values[0]);
									if (preg_match("/^http\:\/\//", $property_value)) {
										$property_value = "<a href=\"".$property_value."\" target=\"_blank\">" . basename($property_value) . "</a>";
									}
									$properties_values .= "<br>" . $property_name . ": " . $property_value;
									$property_tax = get_tax_amount($tax_rates, $item_type_id, $property_price, $tax_free, $tax_percent);
									if ($tax_prices_type == 1) {
										$option_price_incl = $property_price;
										$option_price_excl = $property_price - $property_tax;
									} else {
										$option_price_incl = $property_price + $property_tax;
										$option_price_excl = $property_price;
									}

									if ($tax_prices == 2 || $tax_prices == 3) {
										$shown_price = $option_price_incl;
									} else {
										$shown_price = $option_price_excl;
									}
									if ($property_price > 0) {
										$properties_values .= $option_positive_price_right . currency_format($shown_price) . $option_positive_price_left;
									} elseif ($property_price < 0) {
										$properties_values .= $option_negative_price_right . currency_format(abs($shown_price)) . $option_negative_price_left;
									}

								}
							}
						}
					}
				}

				// calculate total price for product
				$total_item_price = $price + $properties_price + $components_price;
				$max_item_discount = $total_item_price;
				// show product coupons if availble
				$product_discount = 0; 
				if (is_array($item_coupons))
				{
					foreach ($item_coupons as $coupon_id => $coupon_info)
					{
						$sql  = " SELECT c.* FROM ";
						if (isset($site_id)) {
							$sql .= "(";
						}
						$sql .= $table_prefix . "coupons c";
						if (isset($site_id)) {
							$sql .= " LEFT JOIN " . $table_prefix . "coupons_sites s ON s.coupon_id=c.coupon_id)";
						}
						$sql .= " WHERE c.coupon_id=" . $db->tosql($coupon_info["COUPON_ID"], INTEGER);
						if (isset($site_id)) {
							$sql .= " AND (c.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
						} else {
							$sql .= " AND c.sites_all=1 ";
						}
						$db->query($sql);
						if ($db->next_record()) {
							$remove_coupon_link = $page_link . "operation=rm_coupon&coupon_id=" . $coupon_id;
							$is_active = $db->f("is_active");
							$coupon_title = $db->f("coupon_title");
							$discount_type = $db->f("discount_type");
							$coupon_discount_quantity = $db->f("discount_quantity");
							$coupon_discount = $db->f("discount_amount");

							if ($discount_type == 3) {
								$discount_amount = round(($total_item_price / 100) * $coupon_discount, 2);
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
										"COUPON_ID" => $coupon_id, "COUPON_TITLE" => $coupon_title, "ITEM_NAME" => $item_name, 
										"ITEM_TYPE_ID" => $item_type_id, "TAX_FREE" => $tax_free, 
										"DISCOUNT_NUMBER" => $discount_number, "DISCOUNT_PER_ITEM" => $discount_amount, "DISCOUNT_AMOUNT" => ($discount_amount * $discount_number));
								}
							} else {
								$product_discount += $discount_amount;
								$properties_values .= "<br>" . $coupon_title . " (- " . currency_format($discount_amount) . ")";
								$properties_values .= "<br><a href=\"" . $remove_coupon_link . "\" onClick=\"return confirmDelete('".COUPON_MSG."');\">" . COUPON_REMOVE_MSG . "</a>";
							}
						}
					}
				}
				$t->set_var("properties_values", $properties_values);

				// show points price
				if ($points_system && $points_price_basket) {
					if ($points_price <= 0) {
						$points_price = $price * $points_conversion_rate;
					}
					// multiply by quantity
					$points_price *= $quantity;
					$points_price += ($properties_price * $quantity * $points_conversion_rate);
					$points_price += $components_points_price;

					if ($is_points_price) {
						$t->set_var("points_price", number_format($points_price, $points_decimals));
						$t->parse("points_price_block", false);
					} else {
						$t->set_var("points_price_block", "");
					}
				}

				// show reward points
				if ($points_system && $reward_points_basket) {
					$reward_points = calculate_reward_points($reward_type, $reward_amount, $price, $buying_price, $points_conversion_rate, $points_decimals);
					// multiply by quantity
					$reward_points *= $quantity;
					$reward_points += $components_reward_points;
					if ($reward_type == 1 || $reward_type == 4) {
						$properties_reward_points = calculate_reward_points($reward_type, $reward_amount, $properties_price, $properties_buying_price, $points_conversion_rate, $points_decimals);
						$reward_points += ($properties_reward_points * $quantity);
					}

					if ($reward_type) {
						$t->set_var("reward_points", number_format($reward_points, $points_decimals));
						$t->parse("reward_points_block", false);
					} else {
						$t->set_var("reward_points_block", "");
					}
					$total_reward_points += $reward_points;
				}


				// show reward credits
				if ($credit_system && $reward_credits_basket && ($reward_credits_users == 0 || ($reward_credits_users == 1 && $user_id))) {
					$reward_credits = calculate_reward_credits($credit_reward_type, $credit_reward_amount, $price, $buying_price);
					// multiply by quantity
					$reward_credits *= $quantity;
					$reward_credits += $components_reward_credits;
					if ($credit_reward_type == 1 || $credit_reward_type == 4) {
						$properties_reward_credits = calculate_reward_credits($reward_type, $reward_amount, $properties_price, $properties_buying_price);
						$reward_credits += ($properties_reward_credits * $quantity);
					}

					if ($credit_reward_type) {
						$t->set_var("reward_credits", currency_format($reward_credits));
						$t->parse("reward_credits_block", false);
					} else {
						$t->set_var("reward_credits_block", "");
					}
					$total_reward_credits += $reward_credits;
				}


				$price = round($price + $properties_price - $product_discount, 2);
				$tax_amount = get_tax_amount($tax_rates, $item_type_id, $price, $tax_free, $tax_percent);

				if ($tax_prices_type == 1) {
					$price_incl_tax = $price;
					$price_excl_tax = $price - $tax_amount;
				} else {
					$price_incl_tax = $price + $tax_amount;
					$price_excl_tax = $price;
				}

				// summary calculations
				$price_excl_tax_total = $price_excl_tax * $quantity;
				$price_incl_tax_total = $price_incl_tax * $quantity;
				$item_tax_total = $tax_amount * $quantity;

				// add components price to the total product price
				$price_incl_tax += $components_incl_tax;
				$price_excl_tax += $components_excl_tax;
				$tax_amount += $components_tax_amount;
				$price_incl_tax_total += $components_total_incl;
				$price_excl_tax_total += $components_total_excl;
				$item_tax_total += $components_total_tax;

				$goods_total_excl_tax += $price_excl_tax_total;
				$goods_total_incl_tax += $price_incl_tax_total;
				$goods_tax_total += $item_tax_total;

				$t->set_var("item_type", PRODUCT_MSG);
				$t->set_var("REMOVE_FROM_CART_MSG", REMOVE_FROM_CART_MSG);
				$t->set_var("remove_href", $remove_link . $cart_id);

				$errors_list = isset($item["ERROR"]) ? $item["ERROR"] : "";
				if ($errors_list)	{
					$t->set_var("errors_list", $errors_list);
					$t->parse("item_error", false);
					unset($shopping_cart[$cart_id]);
				} else {
					$t->set_var("item_error", "");
				}

				if ($cart_image) {
					$t->parse("item_image_column", false);
				}
				if ($basket_item_name) {
					$t->parse("item_name_column", false);
				}
				if ($basket_item_price) {
					$t->set_var("price_excl_tax", currency_format($price_excl_tax));
					$t->parse("item_price_column", false);
				}
				if ($basket_item_tax_percent) {
					$t->set_var("tax_percent", $tax_percent . "%");
					$t->parse("item_tax_percent_column", false);
				}
				if ($basket_item_tax) {
					$t->set_var("tax", currency_format($tax_amount));
					$t->parse("item_tax_column", false);
				}
				if ($basket_item_price_incl_tax) {
					$t->set_var("price_incl_tax", currency_format($price_incl_tax));
					$t->parse("item_price_incl_tax_column", false);
				}
				if ($basket_item_quantity) {
					$t->parse("item_quantity_column", false);
				}
				if ($basket_item_price_total) {
					$t->set_var("price_excl_tax_total", currency_format($price_excl_tax_total));
					$t->parse("item_price_excl_tax_total_column", false);
				}
				if ($basket_item_tax_total) {
					$t->set_var("tax_total", currency_format($item_tax_total));
					$t->parse("item_tax_total_column", false);
				}
				if ($basket_item_price_incl_tax_total) {
					$t->set_var("price_incl_tax_total", currency_format($price_incl_tax_total));
					$t->parse("item_price_incl_tax_total_column", false);
				}

				$t->parse("items", true);
			}
			else
			{
				// show product with error message
				$item_name = $item["ITEM_NAME"];
				$price = $item["PRICE"];
				$quantity = $item["QUANTITY"];
				$errors_list = isset($item["ERROR"]) ? $item["ERROR"] : PROD_NOT_AVAILABLE_ERROR;

				$tax_amount = get_tax_amount($tax_rates, 0, $price, 0, $tax_percent);
				if ($tax_prices_type == 1) {
					$price_incl_tax = $price;
					$price_excl_tax = $price - $tax_amount;
				} else {
					$price_incl_tax = $price + $tax_amount;
					$price_excl_tax = $price;
				}

				// summary calculations
				$price_excl_tax_total = $price_excl_tax * $quantity;
				$price_incl_tax_total = $price_incl_tax * $quantity;
				$item_tax_total = $tax_amount * $quantity;

				$t->set_var("item_code_block", "");
				$t->set_var("manufacturer_code_block", "");
				$t->set_var("properties_values", "");
				$t->set_var("components_block", "");
				$t->set_var("item_name", $item_name);
				$t->set_var("quantity", $quantity);
				$t->set_var("errors_list", $errors_list);
				$t->parse("item_error", false);

				if ($cart_image) {
					$t->parse("item_image_column", false);
				}
				if ($basket_item_name) {
					$t->parse("item_name_column", false);
				}
				if ($basket_item_price) {
					$t->set_var("price_excl_tax", currency_format($price_excl_tax));
					$t->parse("item_price_column", false);
				}
				if ($basket_item_tax_percent) {
					$t->set_var("tax_percent", $tax_percent . "%");
					$t->parse("item_tax_percent_column", false);
				}
				if ($basket_item_tax) {
					$t->set_var("tax", currency_format($tax_amount));
					$t->parse("item_tax_column", false);
				}
				if ($basket_item_price_incl_tax) {
					$t->set_var("price_incl_tax", currency_format($price_incl_tax));
					$t->parse("item_price_incl_tax_column", false);
				}
				if ($basket_item_quantity) {
					$t->parse("item_quantity_column", false);
				}
				if ($basket_item_price_total) {
					$t->set_var("price_excl_tax_total", currency_format($price_excl_tax_total));
					$t->parse("item_price_excl_tax_total_column", false);
				}
				if ($basket_item_tax_total) {
					$t->set_var("tax_total", currency_format($item_tax_total));
					$t->parse("item_tax_total_column", false);
				}
				if ($basket_item_price_incl_tax_total) {
					$t->set_var("price_incl_tax_total", currency_format($price_incl_tax_total));
					$t->parse("item_price_incl_tax_total_column", false);
				}

				$t->set_var("item_type", PRODUCT_MSG);
				$t->set_var("remove_href", $remove_link . $cart_id);

				$t->parse("items", true);
				unset($shopping_cart[$cart_id]);
			}
		}
	}
	set_session("shopping_cart", $shopping_cart);

	// show summary information and discounts if there are any products
	if ($total_items > 0) {

		// show information about order coupons
		if ($tax_prices_type == 1) {
			$goods_total = $goods_total_incl_tax;
		} else {
			$goods_total = $goods_total_excl_tax;
		}
		$max_discount = $goods_total;
		$max_tax_discount = $goods_tax_total;
		$order_coupons = 0;
		$total_discount_excl_tax = 0; $total_tax_discount = 0; $total_discount_incl_tax = 0;
		if (is_array($quantities_discounts) && sizeof($quantities_discounts) > 0) {
			foreach ($quantities_discounts as $coupon_number => $coupon_info) {
				// show coupon information 
				$order_coupons++;
				$coupon_id = $coupon_info["COUPON_ID"];
				$coupon_title = $coupon_info["COUPON_TITLE"];
				$item_name = $coupon_info["ITEM_NAME"];
				$discount_number = $coupon_info["DISCOUNT_NUMBER"];
				$discount_per_item = $coupon_info["DISCOUNT_PER_ITEM"];
				$discount_amount = $coupon_info["DISCOUNT_AMOUNT"];
				$item_type_id = $coupon_info["ITEM_TYPE_ID"];
				$item_tax_free = $coupon_info["TAX_FREE"];
				$max_discount -= $discount_amount;

				// check discount tax
				$discount_tax_amount = get_tax_amount($tax_rates, $item_type_id, $discount_amount, $item_tax_free, $item_tax_percent);
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

				$t->set_var("coupon_title", $coupon_title);
				if ($basket_item_price_total) {
					$t->set_var("discount_amount_excl_tax", "- " . currency_format($discount_amount_excl_tax));
					$t->parse("discount_amount_excl_tax_column", false);
				}
				if ($basket_item_tax_total) {
					$t->set_var("discount_tax_amount", "- " . currency_format($discount_tax_amount));
					$t->parse("discount_tax_column", false);
				}
				if ($basket_item_price_incl_tax_total) {
					$t->set_var("discount_amount_incl_tax", "- " . currency_format($discount_amount_incl_tax));
					$t->parse("discount_amount_incl_tax_column", false);
				}

				$total_discount_excl_tax += $discount_amount_excl_tax; 
				$total_discount_incl_tax += $discount_amount_incl_tax;
				$total_tax_discount += $discount_tax_amount;

				$remove_coupon_link = $page_link . "operation=rm_coupon&coupon_id=" . $coupon_id;
				$t->set_var("item_type", COUPON_MSG);
				$t->set_var("remove_href", $remove_coupon_link);

				$t->parse("coupons", true);
			}
		}

		$vouchers = array();
		if (is_array($coupons)) {
			foreach ($coupons as $coupon_id => $coupon_info) {
				$coupon_id = $coupon_info["COUPON_ID"];
				$sql  = " SELECT c.* FROM ";
				if (isset($site_id)) {
					$sql .= "(";
				}
				$sql .= $table_prefix . "coupons c";
				if (isset($site_id)) {
					$sql .= " LEFT JOIN  " . $table_prefix . "coupons_sites s ON s.coupon_id=c.coupon_id)";
				}
				$sql .= " WHERE c.coupon_id=" . $db->tosql($coupon_id, INTEGER);
				if (isset($site_id)) {
					$sql .= " AND (c.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
				} else {
					$sql .= " AND c.sites_all=1 ";
				}
				$db->query($sql);
				if ($db->next_record()) {
					$coupon_tax_free = $db->f("coupon_tax_free");
					$discount_type = $db->f("discount_type");
					$coupon_discount = $db->f("discount_amount");
					$coupon_title = $db->f("coupon_title");
					if ($discount_type == 5) {
						// add gift vouchers to array to use later 
						$vouchers[$coupon_id] = array(
							"title" => $coupon_title,
							"max_amount" => $coupon_discount,
						);
					} else {
						// show coupon information 
						$order_coupons++;
						if ($discount_type == 1) {
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
							if ($goods_total != 0) {
								$discount_tax_amount = round(($discount_amount * $goods_tax_total) / $goods_total, 2);	
							} else {
								$discount_tax_amount = 0;
							}
							if ($discount_tax_amount > $max_tax_discount) {
								$discount_tax_amount = $max_tax_discount;
							}
						}
						$max_tax_discount -= $discount_tax_amount;
						if ($tax_prices_type == 1) {
							$discount_amount_excl_tax = $discount_amount - $discount_tax_amount;
							$discount_amount_incl_tax = $discount_amount;
						} else {
							$discount_amount_excl_tax = $discount_amount;
							$discount_amount_incl_tax = $discount_amount + $discount_tax_amount;
						}
				  
						$t->set_var("coupon_title", $coupon_title);
						if ($basket_item_price_total) {
							$t->set_var("discount_amount_excl_tax", "- " . currency_format($discount_amount_excl_tax));
							$t->parse("discount_amount_excl_tax_column", false);
						}
						if ($basket_item_tax_total) {
							$t->set_var("discount_tax_amount", "- " . currency_format($discount_tax_amount));
							$t->parse("discount_tax_column", false);
						}
						if ($basket_item_price_incl_tax_total) {
							$t->set_var("discount_amount_incl_tax", "- " . currency_format($discount_amount_incl_tax));
							$t->parse("discount_amount_incl_tax_column", false);
						}
				  
						$total_discount_excl_tax += $discount_amount_excl_tax; 
						$total_discount_incl_tax += $discount_amount_incl_tax;
						$total_tax_discount += $discount_tax_amount;
				  
						$remove_coupon_link = $page_link . "operation=rm_coupon&coupon_id=" . $coupon_id;
						$t->set_var("item_type", COUPON_MSG);
						$t->set_var("remove_href", $remove_coupon_link);
				  
						$t->parse("coupons", true);
					}
				}
			}
		}

		if ($coupon_errors) {
			$t->set_var("errors_list", $coupon_errors);
			$t->set_var("coupon_code", htmlspecialchars($coupon_code));
			$t->parse("coupon_errors", false);
		} else {
			$t->set_var("coupon_code", "");
			$t->set_var("coupon_errors", "");
		}

		// parse goods total values
		$t->set_var("total_quantity", $total_quantity);
		// show total reward credits
		if ($credit_system && $reward_credits_basket && $total_reward_credits && ($reward_credits_users == 0 || ($reward_credits_users == 1 && $user_id))) {
			$t->set_var("reward_credits_total", currency_format($total_reward_credits));
			$t->sparse("reward_credits_total_block", false);
		}
		// show total reward points 
		if ($points_system && $reward_points_basket && $total_reward_points) {
			$t->set_var("reward_points_total", number_format($total_reward_points, $points_decimals));
			$t->sparse("reward_points_total_block", false);
		}
		if ($basket_item_price_total) {
			$t->set_var("goods_total_excl_tax", currency_format($goods_total_excl_tax));
			$t->parse("goods_total_excl_tax_column", false);
		}
		if ($basket_item_tax_total) {
			$t->set_var("goods_tax_total", currency_format($goods_tax_total));
			$t->parse("goods_tax_total_column", false);
		}
		if ($basket_item_price_incl_tax_total) {
			$t->set_var("goods_total_incl_tax", currency_format($goods_total_incl_tax));
			$t->parse("goods_total_incl_tax_column", false);
		}
		$t->set_var("remove_all_href", $remove_all_link);

		$t->set_var("discount", "");
		if ($total_discount_excl_tax || $order_coupons > 0) {
			if ($basket_item_price_total) {
				$t->set_var("total_discount_excl_tax", "- " . currency_format($total_discount_excl_tax));
				$t->parse("total_discount_amount_excl_tax_column", false);
			}                     
			if ($basket_item_tax_total) {
				$t->set_var("total_tax_discount_amount", "- " . currency_format($total_tax_discount));
				$t->parse("total_discount_tax_column", false);
			}
			if ($basket_item_price_incl_tax_total) {
				$t->set_var("total_discount_incl_tax", "- " . currency_format($total_discount_incl_tax));
				$t->parse("total_discount_amount_incl_tax_column", false);
			}
			$t->parse("discount", false);
		}


		$discounted_amount_excl_tax = $goods_total_excl_tax - $total_discount_excl_tax;
		$discounted_tax_amount = ($goods_tax_total - $total_tax_discount);
		$discounted_amount_incl_tax = ($goods_total_incl_tax - $total_discount_incl_tax);

		// redeem vouchers
		$vouchers_number = 0;
		foreach ($vouchers as $voucher_id => $voucher_info) {
			$vouchers_number++;
			$voucher_max_amount = $voucher_info["max_amount"];
			$voucher_amount_incl_tax = $voucher_max_amount;
			if ($voucher_max_amount > $discounted_amount_incl_tax) {
				$voucher_amount_incl_tax = $discounted_amount_incl_tax;
				$voucher_tax_amount = $discounted_tax_amount;
			} else {
				$voucher_amount_incl_tax = $voucher_max_amount;
				$voucher_tax_amount = round(($discounted_tax_amount * $voucher_amount_incl_tax) / $discounted_amount_incl_tax, 2);	
			}
			$voucher_amount_excl_tax = $voucher_amount_incl_tax - $voucher_tax_amount;
			// decrease discounted prices
			$discounted_amount_excl_tax = round($discounted_amount_excl_tax - $voucher_amount_excl_tax, 2);
			$discounted_tax_amount = round($discounted_tax_amount - $voucher_tax_amount, 2);
			$discounted_amount_incl_tax = round($discounted_amount_incl_tax - $voucher_amount_incl_tax, 2);

			$t->set_var("voucher_title", $voucher_info["title"]);
			$t->set_var("voucher_max_amount", currency_format($voucher_max_amount));
			if ($basket_item_price_total) {
				$t->set_var("voucher_amount_excl_tax", "- ".currency_format($voucher_amount_excl_tax));
				$t->parse("voucher_excl_tax_column", false);
			}                     
			if ($basket_item_tax_total) {
				$t->set_var("voucher_tax_amount", "- ".currency_format($voucher_tax_amount));
				$t->parse("voucher_tax_column", false);
			}
			if ($basket_item_price_incl_tax_total) {
				$t->set_var("voucher_amount_incl_tax", "- ".currency_format($voucher_amount_incl_tax));
				$t->parse("voucher_incl_tax_column", false);
			}

			$remove_coupon_link = $page_link . "operation=rm_coupon&coupon_id=" . $voucher_id;
			$t->set_var("item_type", COUPON_MSG);
			$t->set_var("remove_href", $remove_coupon_link);

			$t->parse("vouchers", true);
		}


		$t->set_var("discounted", "");
		if ($total_discount_excl_tax || $order_coupons > 0 || $vouchers_number > 0) {
			if ($basket_item_price_total) {
				$t->set_var("discounted_amount_excl_tax", currency_format($discounted_amount_excl_tax));
				$t->parse("discounted_amount_excl_tax_column", false);
			}                     
			if ($basket_item_tax_total) {
				$t->set_var("discounted_tax_amount", currency_format($discounted_tax_amount));
				$t->parse("discounted_tax_column", false);
			}
			if ($basket_item_price_incl_tax_total) {
				$t->set_var("discounted_amount_incl_tax", currency_format($discounted_amount_incl_tax));
				$t->parse("discounted_amount_incl_tax_column", false);
			}
			$t->parse("discounted", false);
		}


		$t->parse("basket", false);
		$t->parse("basket_links", false);
		if ($coupons_enable) {
			$t->parse("coupon_form", false);
		} else {
			$t->set_var("coupon_form", "");
		}

		$fast_checkouts = array();
		$sql  = " SELECT payment_id, payment_name, user_payment_name, ";
		$sql .= " fast_checkout_image, fast_checkout_width, fast_checkout_height, fast_checkout_alt ";
		$sql .= " FROM " . $table_prefix . "payment_systems ";
		$sql .= " WHERE fast_checkout_active=1 ";
		$sql .= " AND is_active=1 ";
		$db->query($sql);
		while($db->next_record()) {
			$payment_id = $db->f("payment_id");
			$fast_checkout_alt = get_translation($db->f("fast_checkout_alt"));
			if (!$fast_checkout_alt) {
				$fast_checkout_alt = get_translation($db->f("user_payment_name"));
			}
			if (!$fast_checkout_alt) {
				$fast_checkout_alt = get_translation($db->f("payment_name"));
			}
			$fast_checkout_image = get_translation($db->f("fast_checkout_image"));
			$fast_checkout_width = $db->f("fast_checkout_width");
			$fast_checkout_height = $db->f("fast_checkout_height");
			$fast_checkouts[$payment_id] = array(
				"alt" => $fast_checkout_alt, "src" => $fast_checkout_image,
				"width" => $fast_checkout_width, "height" => $fast_checkout_height,
			);
		}
		
		if (sizeof($fast_checkouts) > 0) {
			// get order profile settings
			$order_info = array();
			$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type='order_info'";
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
			$user_details = get_delivery_details($order_info);

			$fast_checkout_country_show = get_setting_value($page_settings, "fast_checkout_country_show", 0);
			$fast_checkout_country_required = get_setting_value($page_settings, "fast_checkout_country_required", 0);
			$fast_checkout_state_show = get_setting_value($page_settings, "fast_checkout_state_show", 0);
			$fast_checkout_state_required = get_setting_value($page_settings, "fast_checkout_state_required", 0);
			$fast_checkout_postcode_show = get_setting_value($page_settings, "fast_checkout_postcode_show", 0);
			$fast_checkout_postcode_required = get_setting_value($page_settings, "fast_checkout_postcode_required", 0);

			$user_id = get_session("session_user_id");
			if ($fast_checkout_country_show) {
				$countries = get_db_values("SELECT country_id,country_name FROM " . $table_prefix . "countries WHERE show_for_user=1 ORDER BY country_order, country_name ", array(array("", SELECT_COUNTRY_MSG)));
				set_options($countries, $user_details["country_id"], "fast_checkout_country_id");
				if ($fast_checkout_country_required == 1) {
					$t->set_var("country_required", "*");
				}
				$t->parse("fast_checkout_country_select", false);
			} else {
				$t->set_var("fast_checkout_country_id_value", htmlspecialchars($user_details["country_id"]));
				$t->parse("fast_checkout_country_hidden", false);
			}
			if ($fast_checkout_state_show) {
				$states = get_db_values("SELECT state_id,state_name FROM " . $table_prefix . "states WHERE show_for_user=1 ORDER BY state_name ", array(array("", SELECT_STATE_MSG)));
				set_options($states, $user_details["state_id"], "fast_checkout_state_id");
				if ($fast_checkout_state_required == 1) {
					$t->set_var("state_required", "*");
				}
				$t->parse("fast_checkout_state_select", false);
			} else {
				$t->set_var("fast_checkout_state_id_value", htmlspecialchars($user_details["state_id"]));
				$t->parse("fast_checkout_state_hidden", false);
			}
			if ($fast_checkout_postcode_show) {
				$t->set_var("fast_checkout_postcode", $user_details["postal_code"]);
				if ($fast_checkout_postcode_required == 1) {
					$t->set_var("postcode_required", "*");
				}
				$t->parse("fast_checkout_postcode_textbox", false);
			} else {
				$t->set_var("fast_checkout_postcode_value", htmlspecialchars($user_details["postal_code"]));
				$t->parse("fast_checkout_postcode_hidden", false);
			}

			// get order info url
			$site_url = get_setting_value($settings, "site_url", "");
			$secure_url = get_setting_value($settings, "secure_url", "");
			$secure_order_profile = get_setting_value($settings, "secure_order_profile", 0);
			if ($secure_order_profile) {
				$order_info_url = $secure_url . get_custom_friendly_url("order_info.php");
			} else {
				$order_info_url = $site_url . get_custom_friendly_url("order_info.php");
			}

			// parse fast checkout options
			foreach($fast_checkouts as $payment_id => $fast_checkout) {
				$fast_checkout_src = $fast_checkout["src"];
				$t->set_var("fast_payment_id", $payment_id);
				$t->set_var("fast_checkout_alt", $fast_checkout["alt"]);

				$t->set_var("fast_checkout_image", "");
				$t->set_var("fast_checkout_button", "");
				$t->set_var("fast_checkout_width", "");
				$t->set_var("fast_checkout_height", "");
				if (strlen($fast_checkout_src)) {
					// check if image src require some replacements
					if (preg_match("/\{[\w\d\_\-]+\}/i", $fast_checkout_src)) {
						$sql  = " SELECT parameter_name, parameter_source ";
						$sql .= " FROM " . $table_prefix . "payment_parameters ";
						$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
						$sql .= " AND parameter_type='CONSTANT' ";
						$db->query($sql);
						while ($db->next_record()) {
							$fast_checkout_src = str_replace("{".$db->f("parameter_name")."}", $db->f("parameter_source"), $fast_checkout_src);
						}
					}	

					$t->set_var("fast_checkout_src", $fast_checkout_src);
					if ($fast_checkout["width"]) {
						$t->set_var("fast_checkout_width", " width=\"".$fast_checkout["width"]."\" ");
					}
					if ($fast_checkout["height"]) {
						$t->set_var("fast_checkout_height", " height=\"".$fast_checkout["height"]."\" ");
					}
					$t->parse("fast_checkout_image", false);
				} else {
					$t->parse("fast_checkout_button", false);
				}

				$t->parse("fast_checkout_payments", true);
			}

			$t->set_var("order_info_url", $order_info_url);
			$t->parse("fast_checkout_form", false);
		}
		

	} else {
		$t->set_var("coupon_form", "");
		$t->set_var("basket", "");
		$t->parse("empty", false);
	}

	if ($sc_errors) {
		$t->set_var("errors_list", $sc_errors);
		$t->parse("sc_errors", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>