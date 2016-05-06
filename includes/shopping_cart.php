<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  shopping_cart.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	$eol = get_eol();
	$sc_errors = "";
	$sc_item_id = "";
	$cart = get_param("cart");
	$cart_id = get_param("cart_id");
	if ($cart)
	{
		$placed_ids = get_session("placed_ids");
		if (!is_array($placed_ids)) {
			$placed_ids = array();
		}
		$random_id = get_param("rnd");

		//-- checking if such page has been already called
		if (!strlen($random_id) || !isset($placed_ids[$random_id]))
		{
			$placed_ids[$random_id] = $random_id;

			switch (strtoupper($cart))
			{
				case "ADD": // add item to the cart
				case "WISHLIST": // add item to wish list
					$item_id = get_param("add_id");
					if (!strlen($item_id)) {
						$item_id = get_param("item_id");
					}
					if (!strlen($item_id)) {
						$item_code = get_param("item_code");
						$manufacturer_code = get_param("manufacturer_code");
						if (strlen($item_code)) {
							$sql = " SELECT item_id FROM " . $table_prefix . "items WHERE item_code=" . $db->tosql($item_code, TEXT);
							$item_id = get_db_value($sql);
						} else if (strlen($manufacturer_code)) {
							$sql = " SELECT item_id FROM " . $table_prefix . "items WHERE manufacturer_code=" . $db->tosql($manufacturer_code, TEXT);
							$item_id = get_db_value($sql);
						}
						if ($item_id) {
							$_GET["item_id"] = $item_id;
						}
					}
					$accessory_id = get_param("accessory_id");
					$sc_item_id = $accessory_id ? $accessory_id : $item_id;
					$sc_price = get_param("price");
					$sc_quantity = get_param("quantity");

					$type_param_value = get_param("type");
					if ($type_param_value) { $type = $type_param_value; }
					/* start of adding item to the cart */
					$item_added = add_to_cart($sc_item_id, $sc_price, $sc_quantity, $type, $cart, $new_cart_id, $second_page_options, $sc_errors);
					/* end of adding item to the cart */
					// check if any coupons can be added or removed
					check_coupons();

					if ($item_added) {
						$rp = get_param("rp");
						if ($type == "options") {
							if ($cart == "WISHLIST") {
								$cart_page = strlen($rp) ? $rp : get_custom_friendly_url("user_wishlist.php");
							} else {
								$cart_page = strlen($rp) ? get_custom_friendly_url("basket.php") . "?rp=" . urlencode($rp) : get_custom_friendly_url("basket.php");
							}
							header("Location: " . $cart_page);
							exit;
						} elseif ($second_page_options) {
							$product_options_page = get_custom_friendly_url("product_options.php") . "?cart_id=" . $new_cart_id;
							if (strlen($rp)) {
								$product_options_page .= "&rp=" . urlencode($rp);
							}
							header("Location: " . $product_options_page);
							exit;
						} elseif ($cart != "WISHLIST" && isset($settings["redirect_to_cart"])) {
							if ($settings["redirect_to_cart"] == 1) {
								$cart_page = strlen($rp) ? get_custom_friendly_url("basket.php") . "?rp=" . urlencode($rp) : get_custom_friendly_url("basket.php");
								header("Location: " . $cart_page);
								exit;
							} elseif ($settings["redirect_to_cart"] == 2) {
								header("Location: " . get_custom_friendly_url("checkout.php"));
								exit;
							}
						}
					}
					break;
				case "SUBSCRIPTION": // add subscription to the cart
					$sc_subscription_id = get_param("subscription_id");
					$sc_group_id = get_param("group_id");

					/* start of adding item to the cart */
					$subscription_added = add_subscription(0, $sc_subscription_id, $sc_subscription_name, $sc_group_id);
					/* end of adding item to the cart */

					if ($subscription_added) {
						$rp = get_param("rp");
						if (isset($settings["redirect_to_cart"])) {
							if ($settings["redirect_to_cart"] == 1) {
								$cart_page = strlen($rp) ? get_custom_friendly_url("basket.php") . "?rp=" . urlencode($rp) : get_custom_friendly_url("basket.php");
								header("Location: " . $cart_page);
								exit;
							} elseif ($settings["redirect_to_cart"] == 2) {
								header("Location: " . get_custom_friendly_url("checkout.php"));
								exit;
							}
						}
					}

					break;
				case "RM": // remove the item from the cart
					$shopping_cart = get_session("shopping_cart");
					if (is_array($shopping_cart))
					{
						$cart_id = get_param("cart_id");
						unset($shopping_cart[$cart_id]);
						if (sizeof($shopping_cart) == 0) {
							unset($shopping_cart);
							set_session("shopping_cart", "");
							set_session("session_coupons", "");
						} else {
							set_session("shopping_cart", $shopping_cart);
							// check if any coupons can be added or removed
							check_coupons();
						}
					}
					break;

				case "QTY": // update item quantity in the cart
					$shopping_cart = get_session("shopping_cart");
					if (is_array($shopping_cart))
					{
						get_stock_levels($items_stock, $options_stock);
						$cart_id = get_param("cart_id");
						$new_quantity = get_param("new_quantity");
						$new_quantity = abs($new_quantity);
						if (isset($shopping_cart[$cart_id]))
						{
							$item_id = $shopping_cart[$cart_id]["ITEM_ID"];
							$old_quantity = $shopping_cart[$cart_id]["QUANTITY"];
							$change_quantity = $new_quantity - $old_quantity;
							$sql  = " SELECT item_name, stock_level, use_stock_level, hide_out_of_stock, disable_out_of_stock, ";
							$sql .= " min_quantity, max_quantity, quantity_increment ";
							$sql .= " FROM " . $table_prefix . "items ";
							$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
							$db->query($sql);
							if ($db->next_record()) {
								$item_name = $db->f("item_name");
								$stock_level = $db->f("stock_level");
								$use_stock_level = $db->f("use_stock_level");
								$hide_out_of_stock = $db->f("hide_out_of_stock");
								$disable_out_of_stock = $db->f("disable_out_of_stock");
								$min_quantity = $db->f("min_quantity");
								$max_quantity = $db->f("max_quantity");
								$quantity_increment = $db->f("quantity_increment");
							} else {
								// item doesn't exists or unavailable
								$shopping_cart[$cart_id]["ERROR"] = PROD_NOT_AVAILABLE_ERROR;
								return;
							}

							$check_quantity = $items_stock[$item_id] + $change_quantity;
							$available_quantity = $new_quantity;
							// check products availability
							$quantity_limit = ($use_stock_level && ($hide_out_of_stock || $disable_out_of_stock));
							if ($quantity_limit && $stock_level < $check_quantity && ($stock_level < $max_quantity || !$max_quantity))
							{
								if ($stock_level > 0) {
									$available_quantity = $stock_level - $items_stock[$item_id] + $old_quantity;
								} else {
									$available_quantity = 0;
								}
								if ($available_quantity > 0) {
									$stock_error = str_replace("{limit_quantity}", $stock_level, PRODUCT_LIMIT_MSG);
									$stock_error = str_replace("{product_name}", get_translation($item_name), $stock_error);
									$sc_errors .= $stock_error . "<br>";
								} else {
									$shopping_cart[$cart_id]["ERROR"] = PRODUCT_OUT_STOCK_MSG . "<br>";
								}
							}
							if ($min_quantity && (($min_quantity > $new_quantity) || ($quantity_limit && $stock_level < $min_quantity))) {
								// check the minimum allowed quantity
								$quantity_error = str_replace("{limit_quantity}", $min_quantity, PRODUCT_MIN_LIMIT_MSG);
								$quantity_error = str_replace("{product_name}", get_translation($item_name), $quantity_error);
								if ($quantity_limit && $stock_level < $min_quantity) {
									// additional check if we have less items in stock than it's allowed to buy then we just remove the item from the cart
									$shopping_cart[$cart_id]["ERROR"] = $quantity_error . "<br>";
								} else {
									$sc_errors .= $quantity_error . "<br>";
								}
								$available_quantity = $min_quantity;
							} elseif ($max_quantity && $max_quantity < $check_quantity && ($max_quantity < $stock_level || !$quantity_limit)) {
								// check the maximum allowed quantity
								$quantity_error = str_replace("{limit_quantity}", $max_quantity, PRODUCT_LIMIT_MSG);
								$quantity_error = str_replace("{product_name}", get_translation($item_name), $quantity_error);
								$sc_errors .= $quantity_error . "<br>";
								$available_quantity = $max_quantity - $items_stock[$item_id] + $old_quantity;
							}


							// change product quantity available in stock
							$shopping_cart[$cart_id]["QUANTITY"] = $available_quantity;
							$change_quantity = $available_quantity - $old_quantity;

							// check options availability
							$options_errors = "";
							$min_available_quantity = $available_quantity;
							$properties_info = $shopping_cart[$cart_id]["PROPERTIES_INFO"];
							$item_more_properties = $shopping_cart[$cart_id]["PROPERTIES_MORE"];
							if (is_array($properties_info) && !$item_more_properties) {
								foreach ($properties_info as $property_id => $property_info) {
									$ct = strtoupper($property_info["CONTROL"]);
									$property_type_id = $property_info["TYPE"];
									$property_name = $property_info["NAME"];
									$property_values = $property_info["VALUES"];
									if ($property_type_id == 1) {
										if (strtoupper($ct) == "LISTBOX" || strtoupper($ct) == "RADIOBUTTON" || strtoupper($ct) == "CHECKBOXLIST") {
											for ($pv = 0; $pv < sizeof($property_values); $pv++) {
												$option_value_id = $property_values[$pv];
												$sql  = " SELECT property_value, stock_level, use_stock_level, hide_out_of_stock ";
												$sql .= " FROM " . $table_prefix . "items_properties_values ipv ";
												$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER);
												$sql .= " AND item_property_id=" . $db->tosql($option_value_id, INTEGER);
												$db->query($sql);
												if ($db->next_record()) {
													$option_value = get_translation($db->f("property_value"));
													$option_stock_level = $db->f("stock_level");
													$option_use_stock = $db->f("use_stock_level");
													$option_hide_stock = $db->f("hide_out_of_stock");

													if ($option_use_stock && $option_stock_level < ($options_stock[$option_value_id] + $change_quantity) && $option_hide_stock) {
														$available_option_quantity = $option_stock_level - $options_stock[$option_value_id] + $old_quantity;
														if ($min_available_quantity > $available_option_quantity) { $min_available_quantity = $available_option_quantity; }

														if ($option_stock_level > 0) {
															$limit_product = get_translation($item_name) . " (" . $property_name . ": " . $option_value . ")";
															$limit_error = str_replace("{limit_quantity}", $option_stock_level, PRODUCT_LIMIT_MSG);
															$limit_error = str_replace("{product_name}", $limit_product, $limit_error);
															$options_errors .= $limit_error . "<br>";
														} else {
															$shopping_cart[$cart_id]["ERROR"] = PRODUCT_OUT_STOCK_MSG . "<br>";
														}
													}
												} else {
													$shopping_cart[$cart_id]["ERROR"] = PROD_NOT_AVAILABLE_ERROR;
												}
											}
										}
									}

								}
							}
							// if any of product option is out of stock we restore old quantity value
							if ($options_errors) {
								if ($min_available_quantity > 0) {
									$shopping_cart[$cart_id]["QUANTITY"] = $min_available_quantity;
									$sc_errors .= $options_errors;
								} else {
									$shopping_cart[$cart_id]["ERROR"] = PROD_NOT_AVAILABLE_ERROR;
								}
							}

							// update cart data price
							if (!$shopping_cart[$cart_id]["PRICE_EDIT"]) {
								$cart_quantity = $shopping_cart[$cart_id]["QUANTITY"];
								$quantity_price = get_quantity_price($shopping_cart[$cart_id]["ITEM_ID"], $cart_quantity);
								if (sizeof($quantity_price) > 0) {
									// quantity price available
									$product_price = $quantity_price[0];
									$properties_discount = $quantity_price[1];
									$discount_applicable = $quantity_price[2];
								} else {
									// check original price
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
	              
									$sql  = " SELECT ".$price_field.",".$sales_field.",is_sales ";
									$sql .= " FROM " . $table_prefix . "items ";
									$sql .= " WHERE item_id=" . $db->tosql($shopping_cart[$cart_id]["ITEM_ID"], INTEGER);
									$db->query($sql);
									if ($db->next_record()) {
										$product_price = calculate_price($db->f($price_field), $db->f("is_sales"), $db->f($sales_field));
									} else {
										$product_price = $shopping_cart[$cart_id]["PRICE"];
									}
									$properties_discount = 0; $discount_applicable = 1;
								}
								$shopping_cart[$cart_id]["PRICE"] = $product_price;
								$shopping_cart[$cart_id]["PROPERTIES_DISCOUNT"] = $properties_discount;
								$shopping_cart[$cart_id]["DISCOUNT"] = $discount_applicable;
							}

							set_session("shopping_cart", $shopping_cart);
							// check if any coupons can be added or removed
							check_coupons();
						}

					}
					break;

				case "CLR": // remove all items from the cart
					$shopping_cart = get_session("shopping_cart");
					if (is_array($shopping_cart)) {
						set_session("shopping_cart", "");
						set_session("session_coupons", "");
					}
					break;
			}
			set_session("placed_ids", $placed_ids);
		}
	}

	function add_to_cart($sc_item_id, $sc_price, $sc_quantity, $type, $cart, &$new_cart_id, &$second_page_options, &$sc_errors, $cart_item_id = "", $sc_item_name = "")
	{
		global $db, $table_prefix, $settings, $eol, $currency;

		$item_added = false;

		$shopping_cart = get_session("shopping_cart");
		if (!is_array($shopping_cart)) {
			$shopping_cart = array();
		}

		$discount_type = get_session("session_discount_type");
		$discount_amount = get_session("session_discount_amount");
		$user_type_id = get_session("session_user_type_id");
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

		$is_error = false;
		if (VA_Products::check_permissions($sc_item_id, VIEW_ITEMS_PERM)) {
			$sql  = " SELECT item_type_id,item_name," . $price_field . ",is_price_edit,is_sales," . $sales_field . ",buying_price,tax_free,stock_level,";
			$sql .= " use_stock_level,hide_out_of_stock,disable_out_of_stock,min_quantity,max_quantity,quantity_increment ";
			$sql .= " FROM " . $table_prefix . "items ";
			$sql .= " WHERE item_id=" . $db->tosql($sc_item_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$item_type_id = $db->f("item_type_id");
				$item_name = $db->f("item_name");
				$stock_level = $db->f("stock_level");
				$use_stock_level = $db->f("use_stock_level");
				$hide_out_of_stock = $db->f("hide_out_of_stock");
				$disable_out_of_stock = $db->f("disable_out_of_stock");
				$min_quantity = $db->f("min_quantity");
				if (!strlen($sc_quantity) && $min_quantity) {
					$sc_quantity = $min_quantity;
				} 
				if ($sc_quantity < 1) {
					$sc_quantity = 1;
				}
				$max_quantity = $db->f("max_quantity");
				$quantity_increment = $db->f("quantity_increment");
				$buying_price = $db->f("buying_price");
				$tax_free = $db->f("tax_free");
				$is_price_edit = $db->f("is_price_edit");
				if ($is_price_edit) {
					$price = $sc_price;
				} else {
					$price = calculate_price($db->f($price_field), $db->f("is_sales"), $db->f($sales_field));
				}
				$properties_buying = 0;
				$properties_discount = 0;
				$discount_applicable = 1;
			} else {
				$is_error = true;
			}
		} else {
			$is_error = true;
		}
		
		
		if ($is_error){
			// item doesn't exists or no longer unavailable
			if ($type == "db") {
				$item = array (
				"ITEM_ID"	=> intval($sc_item_id),
				"ITEM_TYPE_ID"	=> 0,
				"ITEM_NAME" => $sc_item_name,
				"ERROR" => PROD_NOT_AVAILABLE_ERROR,
				"PROPERTIES"	=> "", "PROPERTIES_PRICE"	=> 0, "PROPERTIES_PERCENTAGE"	=> 0,
				"PROPERTIES_BUYING"	=> 0, "PROPERTIES_DISCOUNT" => 0, "PROPERTIES_MORE" => 0,
				"COMPONENTS" => "",
				"QUANTITY"	=> $sc_quantity, // only one item can be placed
				"TAX_FREE" => 0, "DISCOUNT" => 0, "BUYING_PRICE" => 0, "PRICE_EDIT"	=> 0,
				"PRICE"	=> $sc_price
				);
				//-- add to cart with error
				$shopping_cart[] = $item;
				end($shopping_cart);
				$new_cart_id = key($shopping_cart);
				set_session("shopping_cart", $shopping_cart);

				return true;
			} else {
				return false;
			}
		}

		// calculate summary stock levels for products and options available in the cart
		$stock_levels = array();
		foreach ($shopping_cart as $cart_id => $cart_info) {
			$item_id = $cart_info["ITEM_ID"];
			$item_quantity = $cart_info["QUANTITY"];
			$item_properties = $cart_info["PROPERTIES"];
			$item_more_properties = $cart_info["PROPERTIES_MORE"];
			if (!$item_more_properties) {
				if (isset($stock_levels[$item_id])) {
					$stock_levels[$item_id] += $item_quantity;
				} else {
					$stock_levels[$item_id] = $item_quantity;
				}
				$item_components = $cart_info["COMPONENTS"];
				if (is_array($item_components) && sizeof($item_components) > 1) {
					foreach ($item_components as $property_id => $component_values) {
						foreach ($component_values as $property_item_id => $component) {
							$sub_item_id = $component["sub_item_id"];
							$sub_quantity = $component["quantity"];
							if ($sub_quantity < 1) { $sub_quantity = 1; }
							if (isset($stock_levels[$sub_item_id])) {
								$stock_levels[$sub_item_id] += ($item_quantity * $sub_quantity);
							} else {
								$stock_levels[$sub_item_id] = ($item_quantity * $sub_quantity);
							}
						}
					}
				}
			}
		}

		// check stock level for parent product
		if (isset($stock_levels[$sc_item_id])) {
			$total_quantity = $stock_levels[$sc_item_id] + $sc_quantity;
		} else {
			$total_quantity = $sc_quantity;
		}

/*
			//PRODUCT_MIN_LIMIT_MSG
			$min_quantity = $db->f("min_quantity");
			$max_quantity = $db->f("max_quantity");
			$quantity_increment = $db->f("quantity_increment");
*/

		// check stock levels only if product added to the shopping cart
		if ($cart == "ADD" && $use_stock_level && $stock_level < $total_quantity && ($hide_out_of_stock || $disable_out_of_stock)) {
			if ($stock_level > 0) {
				$limit_error = str_replace("{limit_quantity}", $stock_level, PRODUCT_LIMIT_MSG);
				$limit_error = str_replace("{product_name}", get_translation($item_name), $limit_error);
				$sc_errors .= $limit_error . "<br>";
			} else {
				$sc_errors .= PRODUCT_OUT_STOCK_MSG . "<br>";
			}
			if ($type != "db") {
				return false;
			}
		} elseif ($cart == "ADD" && $min_quantity && $total_quantity < $min_quantity) {
			$limit_error = str_replace("{limit_quantity}", $min_quantity, PRODUCT_MIN_LIMIT_MSG);
			$limit_error = str_replace("{product_name}", get_translation($item_name), $limit_error);
			$sc_errors .= $limit_error . "<br>";
			if ($type != "db") { return false; }
		} elseif ($cart == "ADD" && $max_quantity && $total_quantity > $max_quantity) {
			$limit_error = str_replace("{limit_quantity}", $max_quantity, PRODUCT_LIMIT_MSG);
			$limit_error = str_replace("{product_name}", get_translation($item_name), $limit_error);
			$sc_errors .= $limit_error . "<br>";
			if ($type != "db") { return false; }
		} elseif ($is_price_edit && $type != "options") {
			$error_message = "";
			if (!strlen($price)) {
				$error_message = str_replace("{field_name}", PRICE_MSG, REQUIRED_MESSAGE);
			} elseif (!is_numeric($price)) {
				$error_message = str_replace("{field_name}", PRICE_MSG, INCORRECT_VALUE_MESSAGE);
			} elseif ($price < 0) {
				$error_message = str_replace("{field_name}", PRICE_MSG, MIN_VALUE_MESSAGE);
				$error_message = str_replace("{min_value}", "0.01", $error_message);
			}
			if ($error_message) {
				$sc_errors .= $error_message . "<br>" . $eol;
				if ($type != "db") {
					return false;
				}
			} else {
				// convert value to basic currency
				$price = $price / $currency["rate"];
			}
		}

		// get properties from db
		$db_properties = array();
		if ($type == "db") {
			$sql  = " SELECT property_id, property_value, property_values_ids FROM " . $table_prefix . "saved_items_properties ";
			$sql .= " WHERE cart_item_id=" . $db->tosql($cart_item_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$property_id = $db->f("property_id");
				$property_value = $db->f("property_value");
				$property_values_ids = $db->f("property_values_ids");
				if (strlen($property_value)) {
					$db_properties[$property_id] = array($property_value);
				} elseif (strlen($property_values_ids)) {
					$db_properties[$property_id] = explode(",", $property_values_ids);
				}
			}
		}

		$components = array(); $components_values = array();
		$components_price = 0; $controls_price = 0;
		$properties = "";
		$properties_ids = "";
		$properties_info = "";
		$sql  = " SELECT property_type_id, property_order, usage_type, property_id, sub_item_id, property_name, ";
		$sql .= " quantity, quantity_action, property_price_type, additional_price, trade_additional_price, control_type, required, ";
		$sql .= " parent_property_id, parent_value_id, free_price_type, free_price_amount ";
		$sql .= " FROM " . $table_prefix . "items_properties ";
		$sql .= " WHERE (item_id=" . $db->tosql($sc_item_id, INTEGER) . " OR item_type_id=" . $db->tosql($item_type_id, INTEGER) . ") ";
		if ($type == "db") {
			$sql .= " AND (use_on_details=1 OR use_on_list=1 OR use_on_second=1)";
		} elseif ($type == "list") {
			$sql .= " AND use_on_list=1 ";
		} elseif ($type == "table") {
			$sql .= " AND use_on_table=1 ";
		} elseif ($type == "grid") {
			$sql .= " AND use_on_grid=1 ";
		} elseif ($type == "options") {
			$sql .= " AND use_on_second=1 ";
		} else {
			$sql .= " AND use_on_details=1 ";
		}
		$sql .= " ORDER BY property_order, property_id ";
		$db->query($sql);
		while ($db->next_record())
		{
			$property_id = $db->f("property_id");
			$property_name = $db->f("property_name");
			$property_order = $db->f("property_order");
			$usage_type = $db->f("usage_type");
			$parent_property_id = $db->f("parent_property_id");
			$parent_value_id = $db->f("parent_value_id");
			$property_type_id = $db->f("property_type_id");
			$property_name = get_translation($db->f("property_name"));
			$property_price_type = $db->f("property_price_type");
			$additional_price = $db->f($additional_price_field);
			$free_price_type = $db->f("free_price_type");
			$free_price_amount = $db->f("free_price_amount");
			$property_quantity_action = $db->f("quantity_action");
			if ($property_type_id == 2) {
				$sub_item_id = $db->f("sub_item_id");
				$sub_quantity = $db->f("quantity");
				if ($sub_quantity < 1) { $sub_quantity = 1; }
				$components[$property_id][0] = array("type_id" => 2, "usage_type" => $usage_type, "sub_item_id" => $sub_item_id, "quantity" => $sub_quantity, "quantity_action" => $property_quantity_action, "price" => $additional_price);
			} else {
				$property_type = $db->f("control_type");
				$property_required = $db->f("required");
				$property_values = array();
				$values_text = array();
				if ($properties_ids) { $properties_ids .= ","; }
				$properties_ids .= $property_id;
				if ($type == "db") {
					// get properties from db
					if (isset($db_properties[$property_id])) {
						$property_values = $db_properties[$property_id];
					}
				} else {
					// get properties from form
					if ($property_type == "CHECKBOXLIST") {
						$property_total = get_param("property_total_" . $property_id);
						for ($i = 1; $i <= $property_total; $i++) {
							$property_value = get_param("property_" . $property_id . "_" . $i);
							if ($property_value) { $property_values[] = $property_value; }
						}
					} else if ($property_type == "TEXTBOXLIST") {
						$property_total = get_param("property_total_" . $property_id);
						for ($i = 1; $i <= $property_total; $i++) {
							$property_value = get_param("property_" . $property_id . "_" . $i);
							if ($property_value) { 
								$value_id = get_param("property_value_" . $property_id . "_" . $i);
								$property_values[] = $value_id; 
								$values_text[$value_id] = $property_value; 
							}
						}
					} else {
						$property_value = get_param("property_" . $property_id);
						if (strlen($property_value)) {
							if ($property_type == "IMAGEUPLOAD" && !preg_match("/^http\:\/\//", $property_value)) {
								$property_value = $settings["site_url"] . "images/options/" . $property_value;
							}
							$property_values[] = $property_value;
							if ($property_type == "TEXTBOX" || $property_type == "TEXTAREA") {
								$values_text[$property_value] = $property_value; 
							}
						}
					}
				}
				$control_price = calculate_control_price($property_values, $values_text, $property_price_type, $additional_price, $free_price_type, $free_price_amount);
				$controls_price += $control_price;
				// add all properties for further checks for their different use
				$properties_info[$property_id] = array(
					"USAGE_TYPE" => $usage_type, "CONTROL" => $property_type, "TYPE" => $property_type_id, 
					"NAME" => $property_name, "VALUES" => $property_values, "REQUIRED" => $property_required,
					"PARENT_PROPERTY_ID" => $parent_property_id, "PARENT_VALUE_ID" => $parent_value_id, 
					"TEXT" => $values_text, "CONTROL_PRICE" => $control_price, "ORDER" => $property_order,
					"QUANTITY_ACTION" => $property_quantity_action,
				);
			}
		}

		// check components
		foreach ($components as $property_id => $component_values) {
			$component = $component_values[0];
			if ($component["usage_type"] == 2) {
				$sql  = " SELECT item_id FROM " . $table_prefix . "items_properties_assigned ";
				$sql .= " WHERE item_id=" . $db->tosql($sc_item_id, INTEGER);
				$sql .= " AND property_id=" . $db->tosql($property_id, INTEGER);
				$db->query($sql);
				if (!$db->next_record()) {
					// remove component if it wasn't assigned to product
					unset($components[$property_id]);
					continue;
				}
			}			
			if (isset($component["sub_item_id"]) && $component["sub_item_id"]) {
				if (!VA_Products::check_permissions($component["sub_item_id"], VIEW_ITEMS_PERM)) {
					unset($components[$property_id]);
					continue;
				}
			}
		}

		// check usage and required settings for product options and populate $product_properties array
		$product_properties = "";
		if (isset($properties_info) && is_array($properties_info)) {
			foreach ($properties_info as $property_id => $property_info) {
				$property_exists = true;			
				if ($property_info["USAGE_TYPE"] == 2) {
					// check if option should be assigned to product first
					$sql  = " SELECT item_id FROM " . $table_prefix . "items_properties_assigned ";
					$sql .= " WHERE item_id=" . $db->tosql($sc_item_id, INTEGER);
					$sql .= " AND property_id=" . $db->tosql($property_id, INTEGER);
					$db->query($sql);
					if (!$db->next_record()) {
						// remove option if it wasn't assigned to product
						$property_exists = false;
						unset($properties_info[$property_id]);	
					}
				}
				$parent_property_id = $property_info["PARENT_PROPERTY_ID"];
				$parent_value_id = $property_info["PARENT_VALUE_ID"];
				if ($property_exists && $parent_property_id) {
					$values = array();
					if (isset($properties_info[$parent_property_id]["VALUES"])) {
						$values = $properties_info[$parent_property_id]["VALUES"];
					}
					if (!isset($properties_info[$parent_property_id]) || sizeof($values) == 0) {
						$property_exists = false;
						unset($properties_info[$property_id]);	
					} else if ($parent_value_id && !in_array($parent_value_id, $values)) {
						$property_exists = false;
						unset($properties_info[$property_id]);	
					}
				}
	  
				if ($property_exists) {
					$property_values = $property_info["VALUES"];
					$property_required = $property_info["REQUIRED"];
					if (sizeof($property_values) > 0) {
						$properties[$property_id] = $property_values;
						if ($property_info["TYPE"] == 3) {
							$components_values[$property_id] = $property_values;
						}
						$product_properties[$property_id] = $property_info;
					} else if ($property_required) {
						$property_error = str_replace("{property_name}", $property_info["NAME"], REQUIRED_PROPERTY_MSG);
						$property_error = str_replace("{product_name}", get_translation($item_name), $property_error);
						$sc_errors .= $property_error . "<br>";
					}
				}
			}
		}

		// calculate summary stock levels for options recently selected
		$options_levels = array();
		foreach ($shopping_cart as $cart_id => $cart_info) {
			$item_id = $cart_info["ITEM_ID"];
			$item_quantity = $cart_info["QUANTITY"];
			$item_properties = $cart_info["PROPERTIES"];
			$item_more_properties = $cart_info["PROPERTIES_MORE"];
			if (!$item_more_properties) {
				if (is_array($item_properties)) {
					foreach ($item_properties as $property_id => $property_values) {
						if (isset($product_properties[$property_id]["CONTROL"])) {
							$ct = $product_properties[$property_id]["CONTROL"];
							if (strtoupper($ct) == "LISTBOX"
							|| strtoupper($ct) == "RADIOBUTTON"
							|| strtoupper($ct) == "CHECKBOXLIST"
							|| strtoupper($ct) == "TEXTBOXLIST") {
								for ($ov = 0; $ov < sizeof($property_values); $ov++) {
									$option_value_id = $property_values[$ov];
									if (isset($options_levels[$option_value_id])) {
										$options_levels[$option_value_id] += $item_quantity;
									} else {
										$options_levels[$option_value_id] = $item_quantity;
									}
								}
							}
						}
					}
				}
			}
		}

		// check components values for selection
		if (sizeof($components_values)) {
			foreach ($components_values as $property_id => $values) {
				for ($v = 0; $v < sizeof($values); $v++) {
					$item_property_id = $values[$v];
					$sql  = " SELECT ipv.sub_item_id, ipv.quantity, ipv.additional_price, ipv.trade_additional_price ";
					$sql .= " FROM " . $table_prefix . "items_properties_values ipv ";
					$sql .= " WHERE ipv.item_property_id=" . $db->tosql($item_property_id, INTEGER);
					$db->query($sql);
					if ($db->next_record()) {
						$sub_item_id = $db->f("sub_item_id");
						$sub_quantity = $db->f("quantity");
						if ($sub_quantity < 1) { $sub_quantity = 1; }
						$additional_price = $db->f($additional_price_field);
						$components[$property_id][$item_property_id] = array(
							"type_id" => 3, "sub_item_id" => $sub_item_id, 
							"quantity" => $sub_quantity, 
							"quantity_action" => $properties_info[$property_id]["QUANTITY_ACTION"], 
							"price" => $additional_price);
					}
				}
			}
		}

		$second_page_options = 0;
		if ($type != "options" && $type != "db") {
			$sql  = " SELECT COUNT(*) ";
			$sql .= " FROM " . $table_prefix . "items_properties ";
			$sql .= " WHERE (item_id=" . $db->tosql($sc_item_id, INTEGER) . " OR item_type_id=" . $db->tosql($item_type_id, INTEGER) . ") ";
			$sql .= " AND use_on_second=1 AND property_type_id<>2 ";
			if ($properties_ids) {
				$sql .= " AND property_id NOT IN (" . $properties_ids . ") ";
			}
			$second_page_options = get_db_value($sql);
		}
		if ($sc_errors && $type != "db") {
			// error occurred can't continue process
			return false;
		}

		// check if the item already in the cart than increase quantity
		$in_cart = false;
		if ($cart == "ADD" && !$second_page_options && $type != "options") {
			foreach ($shopping_cart as $in_cart_id => $item)
			{
				if ($item["ITEM_ID"] == $sc_item_id && !$item["PROPERTIES_MORE"])
				{
					$item_properties = $item["PROPERTIES"];
					$item_properties_info = $item["PROPERTIES_INFO"];
					if (!is_array($item_properties) && !is_array($properties)) {
						$in_cart = true;
						break;
					} elseif (is_array($item_properties) && is_array($properties) && $item_properties_info == $product_properties) {
						// compare if new product and product in the cart has the same options values
						$in_cart = true;
						break;
					}
				}
			}
		}

		if ($in_cart) {
			$new_quantity = $shopping_cart[$in_cart_id]["QUANTITY"] + $sc_quantity;
		} else {
			$new_quantity = $sc_quantity;
		}

		// check components prices and stock levels
		if (sizeof($components) > 0) {
			foreach ($components as $property_id => $component_values) {
				foreach ($component_values as $item_property_id => $component) {
					$sub_item_id = $component["sub_item_id"];
					$sub_quantity = $component["quantity"];
					if ($sub_quantity < 1) { $sub_quantity = 1; }
					$component_price = $component["price"];
					if (isset($stock_levels[$sub_item_id])) {
						$component_quantity = $stock_levels[$sub_item_id] + ($sc_quantity * $sub_quantity);
					} else {
						$component_quantity = ($sc_quantity * $sub_quantity);
					}
					$sql  = " SELECT i.item_type_id, i.item_name, i.buying_price, i." . $price_field . ", i.is_sales, i." . $sales_field . ", i.tax_free, ";
					$sql .= " i.stock_level, i.use_stock_level, i.hide_out_of_stock, i.disable_out_of_stock ";
					$sql .= " FROM " . $table_prefix . "items i ";
					$sql .= " WHERE i.item_id=" . $db->tosql($sub_item_id, INTEGER);
					$db->query($sql);
					if ($db->next_record()) {
						$sub_type_id = $db->f("item_type_id");
						$sub_tax_free = $db->f("tax_free");
						$sub_stock_level = $db->f("stock_level");
						$sub_use_stock = $db->f("use_stock_level");
						$sub_hide_stock = $db->f("hide_out_of_stock");
						$sub_disable_stock = $db->f("disable_out_of_stock");
						$sub_item_name = get_translation($db->f("item_name"));
						// check stock levels only if product added to shopping cart
						if ($cart == "ADD" && $sub_use_stock && $sub_stock_level < $component_quantity && ($sub_hide_stock || $sub_disable_stock)) {
							if ($sub_stock_level > 0) {
								$limit_product = get_translation($item_name);
								if (isset($product_properties[$property_id]["NAME"])) {
									$limit_product .= " (" . $product_properties[$property_id]["NAME"] . ": " . $sub_item_name . ")";
								}
								$limit_error = str_replace("{limit_quantity}", $sub_stock_level, PRODUCT_LIMIT_MSG);
								$limit_error = str_replace("{product_name}", $limit_product, $limit_error);
								$sc_errors .= $limit_error . "<br>";
							} else {
								$sc_errors .= PRODUCT_OUT_STOCK_MSG . "<br>";
							}
							if ($type != "db") {
								return false;
							}
						}
						$components[$property_id][$item_property_id]["item_type_id"] = $sub_type_id;
						$components[$property_id][$item_property_id]["tax_free"] = $sub_tax_free;
						if (!strlen($component_price)) {
							$sub_price = $db->f($price_field);
							$sub_is_sales = $db->f("is_sales");
							$sub_sales = $db->f($sales_field);
							if ($sub_is_sales && $sub_sales > 0) {
								$components[$property_id][$item_property_id]["base_price"] = $sub_sales;
							} else {
								$components[$property_id][$item_property_id]["base_price"] = $sub_price;
							}
							
							$user_price  = false; 
							$user_price_action = 0;
							$q_prices    = get_quantity_price($sub_item_id, $new_quantity * $sub_quantity);
							if ($q_prices) {
								$user_price  = $q_prices [0];
								$user_price_action = $q_prices [2];
							}				
				
							$components[$property_id][$item_property_id]["buying"] = $db->f("buying_price");
							$components[$property_id][$item_property_id]["user_price"] = $user_price;
							$components[$property_id][$item_property_id]["user_price_action"] = $user_price_action;
							if ($in_cart) {
								$shopping_cart[$in_cart_id]["COMPONENTS"][$property_id][$item_property_id] = $components[$property_id][$item_property_id];
							}
						}
					} else { // there is no such subcomponent
						$sc_errors .= "Component is missing.<br>";
						if ($type != "db") {
							return false;
						}
					}
				}
			}
		}

		//-- check for additional price for product and options availabiltiy
		$properties_price = 0; $properties_percentage = 0;
		if (is_array($properties)) {
			foreach ($properties as $property_id => $property_values) {
				if (strtoupper($product_properties[$property_id]["CONTROL"]) == "LISTBOX"
				|| strtoupper($product_properties[$property_id]["CONTROL"]) == "RADIOBUTTON"
				|| strtoupper($product_properties[$property_id]["CONTROL"]) == "CHECKBOXLIST"
				|| strtoupper($product_properties[$property_id]["CONTROL"]) == "TEXTBOXLIST") {
					for ($pv = 0; $pv < sizeof($property_values); $pv++) {
						if ($product_properties[$property_id]["TYPE"] == 3) {

						} else {
							$item_property_id = $property_values[$pv];
							if (isset($options_levels[$item_property_id])) {
								$option_quantity = $options_levels[$item_property_id] + $sc_quantity;
							} else {
								$option_quantity = $sc_quantity;
							}
							$sql  = " SELECT buying_price, additional_price, trade_additional_price, percentage_price, additional_weight, ";
							$sql .= " property_value, stock_level, use_stock_level, hide_out_of_stock ";
							$sql .= " FROM " . $table_prefix . "items_properties_values ipv ";
							$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER);
							$sql .= " AND item_property_id=" . $db->tosql($property_values[$pv], INTEGER);
							$sql .= " ORDER BY item_property_id ";
							$db->query($sql);
							if ($db->next_record()) {
								$additional_price = $db->f($additional_price_field);
								$percentage_price = $db->f("percentage_price");
								$properties_price += $additional_price;
								$properties_percentage += $percentage_price;
								$properties_buying += $db->f("buying_price");
								$option_value = get_translation($db->f("property_value"));
								$option_stock_level = $db->f("stock_level");
								$option_use_stock = $db->f("use_stock_level");
								$option_hide_stock = $db->f("hide_out_of_stock");
							}
							// check stock levels only if product added to shopping cart
							if ($cart == "ADD" && $option_use_stock && $option_stock_level < $option_quantity && $option_hide_stock) {
								if ($option_stock_level > 0) {
									$limit_product = get_translation($item_name) . " (" . $product_properties[$property_id]["NAME"] . ": " . $option_value . ")";
									$limit_error = str_replace("{limit_quantity}", $option_stock_level, PRODUCT_LIMIT_MSG);
									$limit_error = str_replace("{product_name}", $limit_product, $limit_error);
									$sc_errors .= $limit_error . "<br>";
								} else {
									$sc_errors .= PRODUCT_OUT_STOCK_MSG . "<br>";
								}
								if ($type != "db") {
									return false;
								}
							}
						}
					}
				}
			}
		}

		if ($in_cart && !$is_price_edit)
		{
			$shopping_cart[$in_cart_id]["QUANTITY"] += $sc_quantity;
			$quantity_price = get_quantity_price($item["ITEM_ID"], $shopping_cart[$in_cart_id]["QUANTITY"]);
			if (sizeof($quantity_price) > 0) {
				$shopping_cart[$in_cart_id]["PRICE"] = $quantity_price[0];
				$shopping_cart[$in_cart_id]["PROPERTIES_DISCOUNT"] = $quantity_price[1];
				$shopping_cart[$in_cart_id]["DISCOUNT"] = $quantity_price[2];
			}
			$item_added = true;
		} else {
			if ($type == "options") {
				$shopping_cart[$cart_id]["PROPERTIES_PRICE"] += $properties_price;
				$shopping_cart[$cart_id]["PROPERTIES_PERCENTAGE"] += $properties_percentage;
				$shopping_cart[$cart_id]["PROPERTIES_BUYING"] += $properties_buying;
				$shopping_cart[$cart_id]["PROPERTIES_MORE"] = 0;
				$all_properties = $shopping_cart[$cart_id]["PROPERTIES"];
				$all_properties_info = $shopping_cart[$cart_id]["PROPERTIES_INFO"];
				if (is_array($properties)) {
					foreach ($properties as $property_id => $property_values) {
						$all_properties[$property_id] = $property_values;
					}
					foreach ($product_properties as $property_id => $property_info) {
						$all_properties_info[$property_id] = $property_info;
					}
				}
				$shopping_cart[$cart_id]["PROPERTIES"] = $all_properties;
				$shopping_cart[$cart_id]["PROPERTIES_INFO"] = $all_properties_info;
				if ($cart == "WISHLIST") {
					add_to_saved_items($shopping_cart, $new_cart_id, 0, true);
				}
			} else {
				if (!$is_price_edit) {
					$quantity_price = get_quantity_price($sc_item_id, $sc_quantity);
					if (sizeof($quantity_price) > 0) {
						$price = $quantity_price[0];
						$properties_discount = $quantity_price[1];
						$discount_applicable = $quantity_price[2];
					}
				}
				$item = array (
					"ITEM_ID"	=> intval($sc_item_id),
					"ITEM_TYPE_ID"	=> $item_type_id,
					"CART_ITEM_ID"	=> $cart_item_id,
					"SAVED_TYPE_ID" => get_param("saved_type_id"),
					"ITEM_NAME" => $item_name,
					"ERROR" => $sc_errors,
					"PROPERTIES"	=> $properties,
					"PROPERTIES_INFO"	=> $product_properties,
					"PROPERTIES_PRICE"	=> ($properties_price + $controls_price),
					"PROPERTIES_PERCENTAGE"	=> $properties_percentage,
					"PROPERTIES_BUYING"	=> $properties_buying,
					"PROPERTIES_DISCOUNT" => $properties_discount,
					"PROPERTIES_MORE" => $second_page_options,
					"COMPONENTS" => $components,
					"QUANTITY"	=> $sc_quantity, // only one item can be placed
					"TAX_FREE" => $tax_free,
					"DISCOUNT" => $discount_applicable,
					"BUYING_PRICE" => $buying_price,
					"PRICE_EDIT"	=> $is_price_edit,
					"PRICE"	=> $price
				);
				//-- add to cart
				$shopping_cart[] = $item;
				end($shopping_cart);
				$new_cart_id = key($shopping_cart);
				if ($cart == "WISHLIST" && !$second_page_options) {
					add_to_saved_items($shopping_cart, $new_cart_id, 0, true);
				}
			}
			$item_added = true;
		}
		set_session("shopping_cart", $shopping_cart);

		return $item_added;
	}

	function add_to_saved_items(&$shopping_cart, $cart_id, $db_cart_id, $clear_cart = true)
	{
		global $db, $db_type, $table_prefix, $settings, $eol, $site_id;

		if (isset($shopping_cart[$cart_id])) {
			$item = $shopping_cart[$cart_id];
		} else {
			return false;
		}
		if (!isset($site_id) || !$site_id) {
			$site_id = 1;
		}
  
		// save cart item
		$price = $item["PRICE"] + $item["PROPERTIES_PRICE"];
		$type_id = $item["SAVED_TYPE_ID"]; // get saved type for wishlist

		$sql = " INSERT INTO " . $table_prefix . "saved_items (";
		if ($db_type == "postgre") {
			$sql .= "cart_item_id, ";
			$cart_item_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "saved_items') ");
		}
		$sql .= "item_id, cart_id, site_id, user_id, type_id, item_name, quantity, quantity_bought, price, date_added) VALUES (";
		if ($db_type == "postgre") {
			$sql .= $db->tosql($cart_item_id, INTEGER) . ", ";
		}
		$sql .= $db->tosql($item["ITEM_ID"], INTEGER) . ", ";
		$sql .= $db->tosql($db_cart_id, INTEGER) . ", ";
		$sql .= $db->tosql($site_id, INTEGER) . ", ";
		$sql .= $db->tosql(get_session("session_user_id"), INTEGER) . ", ";
		$sql .= $db->tosql($type_id, INTEGER, true, false) . ", ";
		$sql .= $db->tosql($item["ITEM_NAME"], TEXT) . ", ";
		$sql .= $db->tosql($item["QUANTITY"], NUMBER) . ", ";
		$sql .= $db->tosql(0, NUMBER) . ", ";
		$sql .= $db->tosql($price, NUMBER) . ", ";
		$sql .= $db->tosql(va_time(), DATETIME) . ") ";

		if ($db->query($sql)) {
			// save properties
			if ($db_type == "mysql") {
				$cart_item_id = get_db_value(" SELECT LAST_INSERT_ID() ");
			} else if ($db_type == "access") {
				$cart_item_id = get_db_value(" SELECT @@IDENTITY ");
			} else if ($db_type == "db2") {
				$cart_item_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "saved_items FROM " . $table_prefix . "saved_items");
			}

			$properties = $item["PROPERTIES"];
			if (is_array($properties)) {
		
				foreach($properties as $property_id => $property_values) {
					$psql  = " INSERT INTO " . $table_prefix . "saved_items_properties ";
					$psql .= " (cart_item_id, cart_id, property_id, property_value, property_values_ids) VALUES (";
					$psql .= $db->tosql($cart_item_id, INTEGER) . ", ";
					$psql .= $db->tosql($db_cart_id, INTEGER) . ", ";
					$psql .= $db->tosql($property_id, INTEGER) . ", ";

					$sql  = " SELECT control_type ";
					$sql .= " FROM " . $table_prefix . "items_properties ";
					$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER);
					$db->query($sql);
					if ($db->next_record()) {
						$control_type = $db->f("control_type");
						if (strtoupper($control_type) == "RADIOBUTTON" 
							|| strtoupper($control_type) == "CHECKBOXLIST" 
							|| strtoupper($control_type) == "LISTBOX") {
							$psql .= $db->tosql("", TEXT) . ", ";
							$psql .= $db->tosql(implode(",", $property_values), TEXT) . ") ";
						} else {
							$psql .= $db->tosql($property_values[0], TEXT) . ", ";
							$psql .= $db->tosql("", TEXT) . ") ";
						}
						$db->query($psql);
					}
				}
			}
			// end save properties
		}
		// clear cart if option set
		if ($clear_cart) {
			unset($shopping_cart[$cart_id]);
		}
		return true;
	}

	function add_subscription($user_type_id, $subscription_id, &$subscription_name, $group_id = "")
	{
		global $db, $table_prefix;

		$subscription_added = false;

		$shopping_cart = get_session("shopping_cart");
		if (!is_array($shopping_cart)) {
			$shopping_cart = array();
		}

		foreach ($shopping_cart as $cart_id => $item) {
			$cart_subscription_group_id = isset($item["SUBSCRIPTION_GROUP_ID"]) ? $item["SUBSCRIPTION_GROUP_ID"] : "";
			$cart_subscription_type_id = isset($item["SUBSCRIPTION_TYPE_ID"]) ? $item["SUBSCRIPTION_TYPE_ID"] : "";
			$cart_subscription_id = isset($item["SUBSCRIPTION_ID"]) ? $item["SUBSCRIPTION_ID"] : "";
			if ($cart_subscription_type_id && $cart_subscription_id) {
				// remove all subscriptions related for user type
				unset($shopping_cart[$cart_id]);
			} else if ($cart_subscription_id == $subscription_id) {
				// remove subscription from the cart if it was previously added
				unset($shopping_cart[$cart_id]);
			} else if ($group_id && $cart_subscription_group_id == $group_id) {
				// remove subscription from the cart if it was previously added
				unset($shopping_cart[$cart_id]);
			}
		}

		if (!$subscription_id) {
			$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "subscriptions ";
			$sql .= " WHERE user_type_id=" . $db->tosql($user_type_id, INTEGER) . " AND is_active=1 ";
			$total_subscriptions = get_db_value($sql);
			if ($total_subscriptions == 1) {
				$sql  = " SELECT subscription_id FROM " . $table_prefix . "subscriptions ";
				$sql .= " WHERE user_type_id=" . $db->tosql($user_type_id, INTEGER) . " AND is_active=1 ";
				$subscription_id = get_db_value($sql);
			} else if ($user_type_id) {
				// redirect user to page to select subscription option
				header("Location: user_change_type.php");
				exit;
			}
		}

		$sql  = " SELECT group_id, subscription_name, subscription_fee, subscription_period, subscription_interval ";
		$sql .= " FROM " . $table_prefix . "subscriptions ";
		$sql .= " WHERE subscription_id=" . $db->tosql($subscription_id, INTEGER);
		if ($user_type_id) {
			$sql .= " AND user_type_id=" . $db->tosql($user_type_id, INTEGER);
		}
		$sql .= " AND is_active=1 ";
		$db->query($sql);
		if ($db->next_record()) {
			$group_id = $db->f("group_id");
			$is_subscription = $db->f("is_subscription");
			$subscription_fee = $db->f("subscription_fee");
			$subscription_name = $db->f("subscription_name");
			$subscription_period = $db->f("subscription_period");
			$subscription_interval = $db->f("subscription_interval");

			$item = array (
				"ITEM_ID"	=> 0,
				"CART_ITEM_ID" => "",
				"ITEM_TYPE_ID"	=> 0,
				"SUBSCRIPTION_TYPE_ID" => $user_type_id,
				"SUBSCRIPTION_GROUP_ID" => $group_id,
				"SUBSCRIPTION_ID"	=> $subscription_id,
				"ITEM_NAME" => $subscription_name,
				"PROPERTIES"	=> "", "PROPERTIES_PRICE"	=> 0, "PROPERTIES_PERCENTAGE"	=> 0,
				"PROPERTIES_BUYING"	=> 0, "PROPERTIES_DISCOUNT" => 0, "PROPERTIES_MORE" => 0,
				"TAX_FREE"	=> 0,
				"DISCOUNT"	=> 0,
				"COMPONENTS" => "",
				"QUANTITY"	=> 1,
				"PRICE_EDIT"	=> 0,
				"BUYING_PRICE"	=> 0,
				"PRICE"	=> $subscription_fee,
			);
			//-- add to cart
			$shopping_cart[] = $item;
			end($shopping_cart);
			$new_cart_id = key($shopping_cart);

			$subscription_added = true;
		}

		set_session("shopping_cart", $shopping_cart);
		return $subscription_added;
	}

	function calculate_price($price, $is_sales, $sales_price)
	{
		if ($is_sales) {
			$price = $sales_price;
		}
		return $price;
	}

	function calculate_reward_points(&$reward_type, &$reward_amount, $price, $buying_price, $conversion_rate = 1, $points_decimals = 0)
	{
		global $settings;
		if (!strlen($reward_type)) {
			$user_info = get_session("session_user_info");
			$reward_type = get_setting_value($user_info, "reward_type", "");
			if (strlen($reward_type)) {
				$reward_amount = get_setting_value($user_info, "reward_amount", "");
			} else {
				$reward_type = get_setting_value($settings, "reward_type", "");
				$reward_amount = get_setting_value($settings, "reward_amount", "");
			}
		}
		if ($reward_type == 1 || $reward_type == 3) {
			$reward_points = round(($price * $reward_amount * $conversion_rate) / 100, $points_decimals);
		} elseif ($reward_type == 2) {
			$reward_points = round($reward_amount, $points_decimals);
		} elseif ($reward_type == 4) {
			$reward_points = round((($price - $buying_price) * $reward_amount * $conversion_rate) / 100, $points_decimals);
		} else {
			$reward_points = 0;
		}

		return $reward_points;
	}

	function calculate_reward_credits(&$credit_reward_type, &$credit_reward_amount, $price, $buying_price)
	{
		global $settings;
		if (!strlen($credit_reward_type)) {
			$user_info = get_session("session_user_info");
			$credit_reward_type = get_setting_value($user_info, "credit_reward_type", "");
			if (strlen($credit_reward_type)) {
				$credit_reward_amount = get_setting_value($user_info, "credit_reward_amount", "");
			} else {
				$credit_reward_type = get_setting_value($settings, "credit_reward_type", "");
				$credit_reward_amount = get_setting_value($settings, "credit_reward_amount", "");
			}
		}
		if ($credit_reward_type == 1 || $credit_reward_type == 3) {
			$reward_credits = round(($price * $credit_reward_amount) / 100, 2);
		} elseif ($credit_reward_type == 2) {
			$reward_credits = round($credit_reward_amount, 2);
		} elseif ($credit_reward_type == 4) {
			$reward_credits = round((($price - $buying_price) * $credit_reward_amount) / 100, 2);
		} else {
			$reward_credits = 0;
		}

		return $reward_credits;
	}

	function set_quantity_control($quantity_limit, $stock_level, $control_type, $min_quantity = 1, $max_quantity = "", $quantity_increment = 1)
	{
		global $settings, $t;
		$quantity_control = "";
		if (!$min_quantity) { $min_quantity = 1; }
		if (!$quantity_limit || $stock_level > $min_quantity) {
			if ($quantity_increment < 1) { $quantity_increment = 1; }
			if (strtoupper($control_type) == "LISTBOX") {
				$increment_limit = 9;
				$show_max_quantity = $min_quantity + ($quantity_increment * $increment_limit);
				if ($max_quantity > 0 && $show_max_quantity > $max_quantity) {
					$show_max_quantity = $max_quantity;
				}
				if ($quantity_limit && $show_max_quantity > $stock_level) {
					$show_max_quantity = $stock_level;
				}
				$quantity_control .= "<select name=\"quantity\" onChange=\"changeQuantity(this.form)\">";
				for ($i = $min_quantity; $i <= $show_max_quantity; $i = $i + $quantity_increment) {
					$quantity_control .= "<option value=\"" . $i ."\">" . $i . "</option>";
				}
				$quantity_control .= "</select>";
			} elseif (strtoupper($control_type) == "TEXTBOX") {
				$quantity_control .= "<input type=\"text\" name=\"quantity\" class=\"field\"";
				$quantity_control .= " value=\"" . $min_quantity . "\" size=\"4\" maxlength=\"6\"";
				$quantity_control .= " onChange=\"changeQuantity(this.form)\">";
			} elseif (strtoupper($control_type) == "LABEL") {
				$quantity_control = $min_quantity;
			}
		}
		if (strlen($quantity_control)) {
			$t->set_var("quantity_control", $quantity_control);
			$t->sparse("quantity", false);
		} else {
			$t->set_var("quantity", "");
		}
	}

	function get_quantity_price($item_id, $quantity)
	{
		global $db, $table_prefix, $site_id;
		
		$dbp = new VA_SQL();
		$dbp->DBType      = $db->DBType;
		$dbp->DBDatabase  = $db->DBDatabase;
		$dbp->DBHost      = $db->DBHost;
		$dbp->DBPort      = $db->DBPort;
		$dbp->DBUser      = $db->DBUser;
		$dbp->DBPassword  = $db->DBPassword;
		$dbp->DBPersistent= $db->DBPersistent;

		$price = array();
		$discount_type = get_session("session_discount_type");
		$user_type_id  = get_session("session_user_type_id");
		
		$order_by = " ORDER BY ";
			
		$sql  = " SELECT site_id, user_type_id, price_id, price, properties_discount, discount_action FROM " . $table_prefix . "items_prices ";
		$sql .= " WHERE is_active=1 AND item_id=" . $dbp->tosql($item_id, INTEGER);
		$sql .= " AND min_quantity<=" . $dbp->tosql($quantity, INTEGER);
		$sql .= " AND max_quantity>=" . $dbp->tosql($quantity, INTEGER);
		
		if (isset($site_id)) {
			$sql .= " AND (site_id=0 OR site_id=" . $dbp->tosql($site_id, INTEGER, true, false) . ") ";
			$order_by .= " site_id DESC, ";
		} else {
			$sql .= " AND site_id=0 ";
		}
		
		if (strlen($user_type_id)) {
			$sql .= " AND (user_type_id=0 OR user_type_id=" . $dbp->tosql($user_type_id, INTEGER, true, false) . ") ";
			$order_by .= " user_type_id DESC, ";
		} else {
			$sql .= " AND user_type_id=0 ";
		}
		
		if ($discount_type > 0) {
			$sql .= " AND discount_action>0 ";
		}
		
		$order_by .= " price_id DESC ";
		$dbp->query($sql . $order_by);
		
		if ($dbp->next_record()) {
			$max_site_id = $dbp->f("site_id");
			$max_type_id = $dbp->f("user_type_id");
			$price[0] = $dbp->f("price");
			$price[1] = $dbp->f("properties_discount");
			$discount_action = $dbp->f("discount_action");
			$price[2] = ($discount_action == 1) ? 0 : 1;
		}
		if ( isset($site_id) && strlen($user_type_id) ) {
			while ($dbp->next_record()) {		
				if ( ($max_site_id <= $dbp->f("site_id")) && ($max_type_id <= $dbp->f("user_type_id")) ) {
					$max_site_id = $dbp->f("site_id");
					$max_type_id = $dbp->f("user_type_id");				
					$price[0] = $dbp->f("price");
					$price[1] = $dbp->f("properties_discount");
					$discount_action = $dbp->f("discount_action");
					$price[2] = ($discount_action == 1) ? 0 : 1;
				
				}
			}			
		}
		return $price;
	}

	function get_item_info(&$item, $item_id = "", $quantity = "") 
	{
		global $db, $table_prefix, $site_id;
		
		$dbp = new VA_SQL();
		$dbp->DBType      = $db->DBType;
		$dbp->DBDatabase  = $db->DBDatabase;
		$dbp->DBHost      = $db->DBHost;
		$dbp->DBPort      = $db->DBPort;
		$dbp->DBUser      = $db->DBUser;
		$dbp->DBPassword  = $db->DBPassword;
		$dbp->DBPersistent= $db->DBPersistent;

		$item_id = isset($item["ITEM_ID"]) ? $item["ITEM_ID"] : $item_id;
		$quantity = isset($item["QUANTITY"]) ? $item["QUANTITY"] : $quantity;
		$is_price_edit = isset($item["PRICE_EDIT"]) ? $item["PRICE_EDIT"] : 0;

		if (!$is_price_edit) {
			$quantity_price = get_quantity_price($item_id, $quantity);
			if (is_array($quantity_price) && sizeof($quantity_price) == 3) {
				$item["ITEM_ID"] = $item_id;
				$item["PRICE"] = $quantity_price[0];
				$item["PROPERTIES_DISCOUNT"] = $quantity_price[1];
				$item["DISCOUNT"] = $quantity_price[2];
			} else {
				// check original price
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
	      
				$sql  = " SELECT ".$price_field.",".$sales_field.",is_sales ";
				$sql .= " FROM " . $table_prefix . "items ";
				$sql .= " WHERE item_id=" . $dbp->tosql($item_id, INTEGER);
				$dbp->query($sql);
				if ($dbp->next_record()) {
					$product_price = calculate_price($dbp->f($price_field), $dbp->f("is_sales"), $dbp->f($sales_field));
				} else {
					$product_price = isset($item["PRICE"]) ? $item["PRICE"] : "";
				}
				$item["ITEM_ID"] = $item_id;
				$item["PRICE"] = $product_price;
				$item["PROPERTIES_DISCOUNT"] = 0;
				$item["DISCOUNT"] = 1; // discount applicable
			}
		}
	}

	function get_product_price($item_id, $price, $buying, $is_sales, $sales, $user_price, $user_action, $discount_type, $discount_amount)
	{
		$prices = array();

		if ($user_price > 0 && ($user_action > 0 || !$discount_type)) {
			if ($is_sales && $sales > 0) {
				$sales = $user_price;
			} else {
				$price = $user_price;
			}
		}
		if ($is_sales && $sales > 0) {
			$real_price = $sales;
		} else {
			$real_price = $price;
		}

		if ($user_action != 1) {
			if ($discount_type == 1 || $discount_type == 3) {
				$price -= round(($price * $discount_amount) / 100, 2);
				$sales -= round(($sales * $discount_amount) / 100, 2);
			} elseif ($discount_type == 2) {
				$price -= round($discount_amount, 2);
				$sales -= round($discount_amount, 2);
			} elseif ($discount_type == 4) {
				$price -= round((($price - $buying) * $discount_amount) / 100, 2);
				$sales -= round((($sales - $buying) * $discount_amount) / 100, 2);
			}
		}

		if ($is_sales && $sales > 0) {
			$prices["base"] = $sales;
		} else {
			$prices["base"] = $price;
		}
		$prices["price"] = $price;
		$prices["sales"] = $price;
		$prices["real"] = $real_price;

		return $prices;
	}

	function get_option_price($additional_price, $buying_price, $properties_percent, $discount_applicable, $discount_type, $discount_amount)
	{
		if ($properties_percent) {
			$additional_price -= round(($additional_price * $properties_percent) / 100, 2);
		}
		if ($discount_applicable) {
			if ($discount_type == 1) {
				$additional_price -= round(($additional_price * $discount_amount) / 100, 2);
			} elseif ($discount_type == 4) {
				$additional_price -= round((($additional_price - $buying_price) * $discount_amount) / 100, 2);
			}
		}

		return $additional_price;
	}

	function get_stock_levels(&$items_stock, &$options_stock)
	{
		global $db, $table_prefix, $shopping_cart;

		$items_stock = array();
		$options_stock = array();
		foreach ($shopping_cart as $cart_id => $cart_info) {
			$item_id = $cart_info["ITEM_ID"];
			$item_quantity = $cart_info["QUANTITY"];
			if (isset($items_stock[$item_id])) {
				$items_stock[$item_id] += $item_quantity;
			} else {
				$items_stock[$item_id] = $item_quantity;
			}
			$item_properties = $cart_info["PROPERTIES"];
			$properties_info = $cart_info["PROPERTIES_INFO"];
			$item_more_properties = $cart_info["PROPERTIES_MORE"];
			if (is_array($item_properties) && !$item_more_properties) {
				foreach ($properties_info as $property_id => $property_info) {
					$ct = strtoupper($property_info["CONTROL"]);
					$property_type_id = $property_info["TYPE"];
					$property_name = $property_info["NAME"];
					$property_values = $property_info["VALUES"];
					if ($property_type_id == 1) {
						if (strtoupper($ct) == "LISTBOX"
						|| strtoupper($ct) == "RADIOBUTTON"
						|| strtoupper($ct) == "CHECKBOXLIST") {
							for ($ov = 0; $ov < sizeof($property_values); $ov++) {
								$option_value_id = $property_values[$ov];
								if (isset($options_stock[$option_value_id])) {
									$options_stock[$option_value_id] += $item_quantity;
								} else {
									$options_stock[$option_value_id] = $item_quantity;
								}
							}
						}
					}

				}
			}
		}
	}

	function remove_coupon($coupon_id)
	{
		global $shopping_cart, $coupons;
		if (!isset($shopping_cart)) {
			$shopping_cart = get_session("shopping_cart");
		}
		if (!isset($coupons)) {
			$coupons = get_session("session_coupons");
		}
		if (is_array($coupons) && isset($coupons[$coupon_id])) {
			unset($coupons[$coupon_id]);
			if (sizeof($coupons) == 0) {
				set_session("session_coupons", "");
			} else {
				set_session("session_coupons", $coupons);
			}
		}
		if (is_array($shopping_cart)) {
			foreach ($shopping_cart as $cart_id => $item) {
				if (isset($shopping_cart[$cart_id]["COUPONS"]) && isset($shopping_cart[$cart_id]["COUPONS"][$coupon_id])) {
					unset($shopping_cart[$cart_id]["COUPONS"][$coupon_id]);
					if (sizeof($shopping_cart[$cart_id]["COUPONS"]) == 0) {
						unset($shopping_cart[$cart_id]["COUPONS"]);
					}
				}
			}
		}
		set_session("shopping_cart", $shopping_cart);
		set_session("session_coupons", $coupons);
	}

	function get_tax_rates($live_taxes = false, $country_id = "", $state_id = "", $postal_code = "")
	{
		global $db, $table_prefix, $settings;

		if (!$live_taxes) {
			$tax_rates = get_session("session_tax_rates");
		} else {
			$tax_rates = "";
		}
		if (!is_array($tax_rates)) {
			$tax_rates = array();
			$tax_ids = "";
			$sql  = " SELECT tax_id, tax_name, tax_percent, shipping_tax_percent ";
			$sql .= " FROM " . $table_prefix . "tax_rates ";
			if ($country_id) {
				$sql .= " WHERE country_id=" . $db->tosql($country_id, INTEGER, true, false);
				$sql .= " AND (state_id=0 OR state_id=" . $db->tosql($state_id, INTEGER, true, false) . ")";
			} else {
				$sql .= " WHERE is_default=1 ";
			}
			$sql .= " ORDER BY state_id DESC ";
			$db->query($sql);
			while ($db->next_record()) {
				$tax_id = $db->f("tax_id");
				$tax_rate = array(
					"tax_name" => $db->f("tax_name"), "tax_percent" => $db->f("tax_percent"), 
					"shipping_tax_percent" => $db->f("shipping_tax_percent"), "types" => array(),
				);
				$tax_rates[$tax_id] = $tax_rate;
				if (strval($tax_ids) !== "") { $tax_ids .= ","; }
				$tax_ids .= $tax_id;
			}

			if (strlen($tax_ids)) {
				$sql  = " SELECT tax_id, item_type_id, tax_percent FROM " . $table_prefix . "tax_rates_items ";
				$sql .= " WHERE tax_id IN (" . $db->tosql($tax_ids, INTEGERS_LIST) . ") ";
				$db->query($sql);
				while ($db->next_record()) {
					$tax_id = $db->f("tax_id");
					$item_type_id = $db->f("item_type_id");
					$tax_percent = $db->f("tax_percent");
					if (strlen($tax_percent)) {
						$tax_rates[$tax_id]["types"][$item_type_id] = $tax_percent;
					}
				}
			}
			if ($live_taxes) {
				set_session("session_tax_rates", "");
			} else {
				set_session("session_tax_rates", $tax_rates);
			}
		}

		return $tax_rates;
	}

	function set_tax_price($item_id, $item_type_id, $price, $sales, $tax_free, $price_tag = "", $sales_tag = "", $tax_tag = "", $tag_id = false, $comp_price = 0, $comp_tax = 0)
	{
		global $t, $settings, $tax_rates, $currency;

		$zero_price_type = get_setting_value($settings, "zero_price_type", 0);
		$zero_price_message = get_translation(get_setting_value($settings, "zero_price_message", ""));
		$tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
		$tax_prices = get_setting_value($settings, "tax_prices", 0);
		$tax_note_excl = get_translation(get_setting_value($settings, "tax_note_excl", ""));
		$tax_note_incl = get_translation(get_setting_value($settings, "tax_note", ""));
		$price_tax = get_tax_amount($tax_rates, $item_type_id, $price, $tax_free, $tax_percent);
		$sales_tax = get_tax_amount($tax_rates, $item_type_id, $sales, $tax_free, $tax_percent);
		$tax_amount = $price_tax;

		if ($tax_prices_type == 1) {
			$price_incl = $price + $comp_price;
			$price_excl = $price - $price_tax + $comp_price - $comp_tax;
			$sales_incl = $sales + $comp_price;
			$sales_excl = $sales - $sales_tax + $comp_price - $comp_tax;
		} else {
			$price_incl = $price + $price_tax + $comp_price + $comp_tax;
			$price_excl = $price + $comp_price;
			$sales_incl = $sales + $sales_tax + $comp_price + $comp_tax;
			$sales_excl = $sales + $comp_price;
		}

		if ($tax_prices == 0 || $tax_prices == 3) {
			$tax_tag = "";
		}

		// set some product settings
		$t->set_var("price_block_class", "priceBlock");
		if ($price_tag) {
			if ($tax_prices == 0 || $tax_prices == 1) {
				$product_price = $price_excl;
				$product_sales = $sales_excl;
			} else {
				$product_price = $price_incl;
				$product_sales = $sales_incl;
			}

			$t->set_var("tax_percent", $tax_percent);
			$t->set_var("tax_prices", $tax_prices);
			if ($zero_price_type && $product_price == 0) {
				if ($zero_price_type == 1) {
					$t->set_var("price_block_class", "priceBlockHidden");
				}
				$t->set_var($price_tag, $zero_price_message);
				$t->set_var($price_tag . "_control", $zero_price_message);
			} else {
				$t->set_var("price_block_class", "priceBlock");
				$t->set_var($price_tag, currency_format($product_price));
				$t->set_var($price_tag . "_control", currency_format($product_price));
			}
			if ($sales_tag) {
				$t->set_var("price_block_class", "priceBlockOld");
				$t->set_var($sales_tag, currency_format($product_sales));
				$t->set_var($sales_tag. "_control", currency_format($product_sales));
				$t->set_var("you_save", currency_format($product_price - $product_sales));
			}
		}
		if ($tax_tag) {
			if ($tax_prices == 1) {
				$product_price = $price_incl;
				$product_sales = $sales_incl;
				$tax_note = $tax_note_incl;
			} else {
				$product_price = $price_excl;
				$product_sales = $sales_excl;
				$tax_note = $tax_note_excl;
			}
			$tax_price = ($sales_tag) ? $product_sales : $product_price;
			if ($tax_note) { $tax_note = " " . $tax_note; }
			if ($zero_price_type && $product_price == 0) {
				if ($tag_id) {
					$t->set_var($tax_tag, "<span id=\"tax_price_" . $item_id . "\"></span>");
				} else {
					$t->set_var($tax_tag, "");
				}
			} else {
				if ($tag_id) {
					$t->set_var($tax_tag, "<span id=\"tax_price_" . $item_id . "\">(" . currency_format($tax_price) . $tax_note . ")" . "</span>");
				} else {
					$t->set_var($tax_tag, "(" . currency_format($tax_price) . $tax_note . ")");
				}
			}
		}

		return $tax_amount;
	}

	function delete_products($items_ids)
	{
		global $db, $table_prefix;

		// delete all properties
		$properties_ids = "";
		$sql = " SELECT property_id FROM " . $table_prefix ."items_properties WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")";
		$db->query($sql);
		while ($db->next_record()) {
			if (strlen($properties_ids)) { $properties_ids .= ","; }
			$properties_ids .= $db->f("property_id");
		}
		if (strlen($properties_ids)) {
			$db->query("DELETE FROM " . $table_prefix . "items_properties_values WHERE property_id IN (" . $db->tosql($properties_ids, INTEGERS_LIST) . ") ");
			$db->query("DELETE FROM " . $table_prefix . "items_properties WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ");
		}
		// delete properties and values where it's a subcomponent
		$db->query("DELETE FROM " . $table_prefix . "items_properties_values WHERE sub_item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ");
		$db->query("DELETE FROM " . $table_prefix . "items_properties WHERE sub_item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ");

		// delete all releases
		$releases_ids = "";
		$sql = " SELECT release_id FROM " . $table_prefix ."releases WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")";
		$db->query($sql);
		while ($db->next_record()) {
			if (strlen($releases_ids)) { $releases_ids .= ","; }
			$releases_ids .= $db->f("release_id");
		}
		if (strlen($releases_ids)) {
			$db->query("DELETE FROM " . $table_prefix . "release_changes WHERE release_id  IN (" . $db->tosql($releases_ids, INTEGERS_LIST) . ") ");
			$db->query("DELETE FROM " . $table_prefix . "releases WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ");
		}

		// delete from other tables
		$db->query("DELETE FROM " . $table_prefix . "items_sites WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "items_subscriptions WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "items_user_types WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "reviews WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "items_categories WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "items_related WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "articles_categories_items WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "articles_items_related WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "features WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "items_images WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "items_accessories WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "items_properties_assigned WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "items_values_assigned WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
		$db->query("DELETE FROM " . $table_prefix . "items WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")");
	}

	function delete_categories($categories_ids)
	{
		global $db, $table_prefix;

		// additional connection
		$dbs = new VA_SQL();
		$dbs->DBType       = $db->DBType;
		$dbs->DBDatabase   = $db->DBDatabase;
		$dbs->DBHost       = $db->DBHost;
		$dbs->DBPort       = $db->DBPort;
		$dbs->DBUser       = $db->DBUser;
		$dbs->DBPassword   = $db->DBPassword;
    	$dbs->DBPersistent = $db->DBPersistent;

		$categories = array();
		$sql  = " SELECT category_id,category_path FROM " . $table_prefix . "categories ";
		$sql .= " WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ") ";
		$dbs->query($sql);
		while ($dbs->next_record()) {
			$category_id = $dbs->f("category_id");
			$category_path = $dbs->f("category_path");
			if (!in_array($category_id, $categories)) {
				$categories[] = $category_id;
				$sql  = " SELECT category_id FROM " . $table_prefix . "categories ";
				$sql .= " WHERE category_path LIKE '" . $db->tosql($category_path.$category_id.",", TEXT, false) . "%'";
				$db->query($sql);
				while($db->next_record()) {
					$categories[] = $db->f("category_id");
				}
			}
		}

		if (is_array($categories) && sizeof($categories) > 0) {
			$categories_ids = join(",", $categories);
			$db->query("DELETE FROM " . $table_prefix . "categories WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")");
			$db->query("DELETE FROM " . $table_prefix . "items_categories WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")");
			$db->query("DELETE FROM " . $table_prefix . "categories_user_types WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")");
			$db->query("DELETE FROM " . $table_prefix . "categories_subscriptions WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")");
			$db->query("DELETE FROM " . $table_prefix . "categories_sites WHERE category_id IN (" . $db->tosql($categories_ids, INTEGERS_LIST) . ")");
		}

		// delete products that are not assigned to any category 
		$sql  = " SELECT i.item_id FROM (" . $table_prefix ."items i ";
		$sql .= " LEFT JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id) ";
		$sql .= " WHERE ic.category_id IS NULL ";
		$dbs->query($sql);
		while ($dbs->next_record()) {
			$item_id = $dbs->f("item_id");
			delete_products($item_id);
		}
	}

	function check_coupons($auto_apply = true)
	{
		check_add_coupons($auto_apply, "", $coupon_error);
	}

	function check_add_coupons($auto_apply, $new_coupon_code, &$new_coupon_error)
	{
		global $db, $site_id, $table_prefix, $date_show_format;
		global $currency;

		$shopping_cart = get_session("shopping_cart");
		$order_coupons = get_session("session_coupons");
		$user_info = get_session("session_user_info");
		$user_id = get_setting_value($user_info, "user_id", "");
		$user_type_id = get_setting_value($user_info, "user_type_id", "");
		$user_tax_free = get_setting_value($user_info, "tax_free", 0);
		$user_discount_type = get_session("session_discount_type");
		$user_discount_amount = get_session("session_discount_amount");

		if (!is_array($shopping_cart) || sizeof($shopping_cart) < 1) {
			return;
		}

		// check if any product coupons should be removed
		$total_quantity = 0; $total_price = 0; $exclusive_applied = false; $new_coupons_total = 0; $coupons_total = 0;
		foreach($shopping_cart as $cart_id => $item)
		{
			$item_id = $item["ITEM_ID"];
			$properties_more = $item["PROPERTIES_MORE"];
			if (!$item_id || $properties_more > 0) { 
				continue;
			}

			$item_type_id = $item["ITEM_TYPE_ID"];
			$properties = $item["PROPERTIES"];
			$quantity = $item["QUANTITY"];
			$tax_free = $item["TAX_FREE"];
			$discount_applicable = $item["DISCOUNT"];
			$buying_price = $item["BUYING_PRICE"];
			$price = $item["PRICE"];
			$is_price_edit = $item["PRICE_EDIT"];
			$properties_price = $item["PROPERTIES_PRICE"];
			$properties_percentage = $item["PROPERTIES_PERCENTAGE"];
			$properties_buying = $item["PROPERTIES_BUYING"];
			$properties_discount = $item["PROPERTIES_DISCOUNT"];
			$components = $item["COMPONENTS"];
			if ($discount_applicable) {
				if (!$is_price_edit) {
					if ($user_discount_type == 1) {
						$price -= round(($price * $user_discount_amount) / 100, 2);
					} else if ($user_discount_type == 2) {
						$price -= round($user_discount_amount, 2);
					} else if ($user_discount_type == 3) {
						$price -= round(($price * $user_discount_amount) / 100, 2);
					} else if ($user_discount_type == 4) {
						$price -= round((($price - $buying_price) * $user_discount_amount) / 100, 2);
					}
				}
			} 
			if ($properties_percentage && $price) {
				$properties_price += round(($price * $properties_percentage) / 100, 2);
			}
			if ($properties_discount > 0) {
				$properties_price -= round(($properties_price * $properties_discount) / 100, 2);
			}
			if ($discount_applicable) {
				if ($user_discount_type == 1) {
					$properties_price -= round((($properties_price) * $user_discount_amount) / 100, 2);
				} else if ($user_discount_type == 4) {
					$properties_price -= round((($properties_price - $properties_buying) * $user_discount_amount) / 100, 2);
				}
			}
			$price += $properties_price;

			// add components prices
			if (is_array($components) && sizeof($components) > 0) {
				foreach ($components as $property_id => $component_values) {
					foreach ($component_values as $property_item_id => $component) {
						$component_price = $component["price"];
						$component_tax_free = $component["tax_free"];
						if ($user_tax_free) { $component_tax_free = $user_tax_free; }
						$sub_item_id = $component["sub_item_id"];
						$sub_quantity = $component["quantity"];
						if ($sub_quantity < 1)  { $sub_quantity = 1; }
						$sub_type_id = $component["item_type_id"];
						if (!strlen($component_price)) {
							$sub_price = $component["base_price"];
							$sub_buying = $component["buying"];
							$sub_user_price = $component["user_price"];
							$sub_user_action = $component["user_price_action"];
							$sub_prices = get_product_price($sub_item_id, $sub_price, $sub_buying, 0, 0, $sub_user_price, $sub_user_action, $user_discount_type, $user_discount_amount);
							$component_price = $sub_prices["base"];
						}
						// add to the item price component price
						$price += $component_price;
					}
				}
			}
			$check_price = $price; // price to check product coupons
			$shopping_cart[$cart_id]["CHECK_PRICE"] = $price;
			$shopping_cart[$cart_id]["MAX_ITEM_DISCOUNT"] = $price;

			// product coupons
			if (isset($item["COUPONS"]) && is_array($item["COUPONS"])) {
				foreach ($item["COUPONS"] as $coupon_id => $coupon_info) {

					if ($auto_apply && $coupon_info["AUTO_APPLY"]) {
						// always remove auto-apply coupons
						unset($shopping_cart[$cart_id]["COUPONS"][$coupon_id]);
					} else {
						$sql  = " SELECT * FROM " . $table_prefix . "coupons ";
						$sql .= " WHERE coupon_id=" . $db->tosql($coupon_id, INTEGER);
						$db->query($sql);
						if ($db->next_record()) {
							$discount_type = $db->f("discount_type");
							$coupon_discount = $db->f("discount_amount");
							$min_quantity = $db->f("min_quantity");
							$max_quantity = $db->f("max_quantity");
							$minimum_amount = $db->f("minimum_amount");
							$maximum_amount = $db->f("maximum_amount");
							$is_exclusive = $db->f("is_exclusive");
							if ($quantity < $min_quantity || $check_price < $minimum_amount ||
								($max_quantity && $max_quantity < $quantity) ||
								($maximum_amount && $maximum_amount < $check_price)) {
								unset($shopping_cart[$cart_id]["COUPONS"][$coupon_id]);
							} else {
								// descrease product price for coupon discount
								$price -= $coupon_info["DISCOUNT_AMOUNT"];
								if ($is_exclusive) { $exclusive_applied = true; }
								$coupons_total++;
							}
						} else {
							unset($shopping_cart[$cart_id]["COUPONS"][$coupon_id]);
						}
					}
				}
			}

			$shopping_cart[$cart_id]["CART_PRICE"] = $price; // price with coupons discounts
			$total_quantity += $quantity;
			$total_price += ($quantity * $price);
		}
		// check if any order coupons should be removed
		// total_price variable is used to check order coupons
		if (is_array($order_coupons)) {
			foreach ($order_coupons as $coupon_id => $coupon_info) {
				if ($auto_apply && $coupon_info["AUTO_APPLY"]) {
					// always remove auto-apply coupons
					unset($order_coupons[$coupon_id]);
				} else {
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
					$sql .= " ORDER BY c.apply_order ";
					$db->query($sql);
					if ($db->next_record()) {
						$discount_type = $db->f("discount_type");
						$coupon_discount = $db->f("discount_amount");
						$min_quantity = $db->f("min_quantity");
						$max_quantity = $db->f("max_quantity");
						$minimum_amount = $db->f("minimum_amount");
						$maximum_amount = $db->f("maximum_amount");
						$is_exclusive = $db->f("is_exclusive");
						if ($total_quantity < $min_quantity || $total_price < $minimum_amount ||
							($max_quantity && $max_quantity < $total_quantity) ||
							($maximum_amount && $maximum_amount < $total_price)) {
							unset($order_coupons[$coupon_id]);
						} else {
							if ($is_exclusive) { $exclusive_applied = true; }
							$coupons_total++;
						}
					} else {
						unset($order_coupons[$coupon_id]);
					}
				}
			}
		}

		// check if new coupons could be added
		$new_coupons = array();
		if (strlen($new_coupon_code)) {
			$sql  = " SELECT c.* FROM (" . $table_prefix . "coupons c";
			if (isset($site_id)) {
				$sql .= " LEFT JOIN  " . $table_prefix . "coupons_sites s ON s.coupon_id=c.coupon_id)";
			} else {
				$sql .= ")";
			}
			$sql .= " WHERE c.coupon_code=" . $db->tosql($new_coupon_code, TEXT);
			if (isset($site_id)) {
				$sql .= " AND (c.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
			} else {
				$sql .= " AND c.sites_all=1 ";
			}
			$sql .= " ORDER BY c.apply_order ";
			$db->query($sql);
			if ($db->next_record()) {
				$new_coupon_id = $db->f("coupon_id");
				$start_date_db = $db->f("start_date", DATETIME);
				$expiry_date_db = $db->f("expiry_date", DATETIME);
				$new_coupons[$new_coupon_id] = $db->Record;
				$new_coupons[$new_coupon_id]["start_date_db"] = $start_date_db;
				$new_coupons[$new_coupon_id]["expiry_date_db"] = $expiry_date_db;
			}
		}

		$discount_types = array("3,4", "1,2", "5"); // check products coupons, then order coupons and only then vouchers 

		if ($auto_apply) {
			for ($dt = 0; $dt < sizeof($discount_types); $dt++) {
				$sql  = " SELECT c.* FROM ";
				if (isset($site_id)) {
					$sql .= " ( ";
				}
				$sql .= $table_prefix . "coupons c";
				if (isset($site_id)) {
					$sql .= " LEFT JOIN  " . $table_prefix . "coupons_sites s ON s.coupon_id=c.coupon_id)";
				}
				$sql .= " WHERE c.is_auto_apply=1 ";
				$sql .= " AND c.discount_type IN (" . $discount_types[$dt] . ") ";
				if (isset($site_id)) {
					$sql .= " AND (c.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
				} else {
					$sql .= " AND c.sites_all=1 ";
				}
				$sql .= " ORDER BY c.apply_order ";
				$db->query($sql);
				while ($db->next_record()) {
					$new_coupon_id = $db->f("coupon_id");
					$start_date_db = $db->f("start_date", DATETIME);
					$expiry_date_db = $db->f("expiry_date", DATETIME);
					$new_coupons[$new_coupon_id] = $db->Record;
					$new_coupons[$new_coupon_id]["start_date_db"] = $start_date_db;
					$new_coupons[$new_coupon_id]["expiry_date_db"] = $expiry_date_db;
				}
			}
		}
		
		// check if new coupons could be added
		if (sizeof($new_coupons) > 0) {
			foreach ($new_coupons as $new_coupon_id => $data) {
				$coupon_error = "";
				$is_active = $data["is_active"];
				$new_coupon_id = $data["coupon_id"];
				$coupon_auto_apply = $data["is_auto_apply"];
				$coupon_code = $data["coupon_code"];
				$coupon_title = $data["coupon_title"];
				$discount_type = $data["discount_type"];
				$discount_quantity = $data["discount_amount"];
				$coupon_discount = $data["discount_amount"];
				$free_postage = $data["free_postage"];
				$coupon_tax_free = $data["coupon_tax_free"];
				$coupon_order_tax_free = $data["order_tax_free"];
				$items_all = $data["items_all"];
				$items_ids = $data["items_ids"];
				$items_types_ids = $data["items_types_ids"];
				$search_items_ids = explode(",", $items_ids);
				$search_items_types_ids = explode(",", $items_types_ids);
				$users_all = $data["users_all"];
				$users_use_limit = $data["users_use_limit"];
				$users_ids = $data["users_ids"];
				$users_types_ids = $data["users_types_ids"];
				$search_users_ids = explode(",", $users_ids);
				$search_users_types_ids = explode(",", $users_types_ids);

				$expiry_date = "";
				$is_expired = false;
				$expiry_date_db = $data["expiry_date_db"];
				if (is_array($expiry_date_db)) {
					$expiry_date = va_date($date_show_format, $expiry_date_db);
					$expiry_date_ts = mktime (0,0,0, $expiry_date_db[MONTH], $expiry_date_db[DAY], $expiry_date_db[YEAR]);
					$current_date_ts = va_timestamp();
					if ($current_date_ts > $expiry_date_ts) {
						$is_expired = true;
					}
				}
				$start_date = "";
				$is_upcoming = false;
				$start_date_db = $data["start_date_db"];
				if (is_array($start_date_db)) {
					$start_date = va_date($date_show_format, $start_date_db);
					$start_date_ts = mktime (0,0,0, $start_date_db[MONTH], $start_date_db[DAY], $start_date_db[YEAR]);
					$current_date_ts = va_timestamp();
					if ($current_date_ts < $start_date_ts) {
						$is_upcoming = true;
					}
				}
				// check number how many times user can use coupon
				$user_not_limited = false;
				if ($users_use_limit && $user_id) {
					$sql  = " SELECT COUNT(*) FROM ((" . $table_prefix . "orders o ";
					$sql .= " INNER JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
					$sql .= " INNER JOIN " . $table_prefix . "orders_coupons oc ON o.order_id=oc.order_id) ";
					$sql .= " WHERE o.user_id=" . $db->tosql($user_id, INTEGER);
					$sql .= " AND oc.coupon_id=" . $db->tosql($new_coupon_id, INTEGER);
					$sql .= " AND os.paid_status=1 ";
					$user_uses = get_db_value($sql);
					if ($users_use_limit > $user_uses) {
						$user_not_limited = true;
					}
				}

				// check goods cost limits
				$orders_period = $data["orders_period"];
				$orders_interval = $data["orders_interval"];
				$orders_min_goods = $data["orders_min_goods"];
				$orders_max_goods = $data["orders_max_goods"];
				$orders_goods_coupon = false;				
				if ($user_id && ($orders_min_goods || $orders_max_goods)) {
					// check if user buy something in the past
					$sql  = " SELECT SUM(o.goods_total) FROM (" . $table_prefix . "orders o ";
					$sql .= " INNER JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
					$sql .= " WHERE o.user_id=" . $db->tosql($user_id, INTEGER);
					$sql .= " AND os.paid_status=1 ";
					if ($orders_period && $orders_interval) {
						$cd = va_time();
						if ($orders_period == 1) {
							$od = mktime (0, 0, 0, $cd[MONTH], $cd[DAY] - $orders_interval, $cd[YEAR]);
						} elseif ($orders_period == 2) {
							$od = mktime (0, 0, 0, $cd[MONTH], $cd[DAY] - ($orders_interval * 7), $cd[YEAR]);
						} elseif ($orders_period == 3) {
							$od = mktime (0, 0, 0, $cd[MONTH] - $orders_interval, $cd[DAY], $cd[YEAR]);
						} else {
							$od = mktime (0, 0, 0, $cd[MONTH], $cd[DAY], $cd[YEAR] - $orders_interval);
						}
						$sql .= " AND order_placed_date>=" . $db->tosql($od, DATETIME);
					}
					$user_goods_cost= get_db_value($sql);
					if ($user_goods_cost >= $orders_min_goods && ($user_goods_cost <= $orders_max_goods || !strlen($orders_max_goods))) {
						$orders_goods_coupon = true;
					}
				}

				// check for friends coupons
				$friends_coupon = false;
				$friends_discount_type = $data["friends_discount_type"];
				$friends_all = $data["friends_all"];
				$friends_ids = $data["friends_ids"];
				$friends_types_ids = $data["friends_types_ids"];
				$friends_period = $data["friends_period"];
				$friends_interval = $data["friends_interval"];
				$friends_min_goods = $data["friends_min_goods"];
				$friends_max_goods = $data["friends_max_goods"];
				$search_friends_ids = explode(",", $friends_ids);
				$search_friends_types_ids = explode(",", $friends_types_ids);
				if ($friends_discount_type == 1) {
					// check if user friends buy something
					$user_friends_goods = 0;
					if ($user_id) {
						$sql  = " SELECT SUM(o.goods_total) FROM (" . $table_prefix . "orders o ";
						$sql .= " INNER JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
						$sql .= " WHERE o.friend_user_id=" . $db->tosql($user_id, INTEGER);
						$sql .= " AND os.paid_status=1 ";
						if ($friends_period && $friends_interval) {
							$cd = va_time();
							if ($friends_period == 1) {
								$od = mktime (0, 0, 0, $cd[MONTH], $cd[DAY] - $friends_interval, $cd[YEAR]);
							} elseif ($friends_period == 2) {
								$od = mktime (0, 0, 0, $cd[MONTH], $cd[DAY] - ($friends_interval * 7), $cd[YEAR]);
							} elseif ($friends_period == 3) {
								$od = mktime (0, 0, 0, $cd[MONTH] - $friends_interval, $cd[DAY], $cd[YEAR]);
							} else {
								$od = mktime (0, 0, 0, $cd[MONTH], $cd[DAY], $cd[YEAR] - $friends_interval);
							}
							$sql .= " AND order_placed_date>=" . $db->tosql($od, DATETIME);
						}
						$user_friends_goods = get_db_value($sql);
					}
					if ($user_friends_goods >= $friends_min_goods && ($user_friends_goods <= $friends_max_goods || !strlen($friends_max_goods))) {
						$friends_coupon = true;
					}
				} elseif ($friends_discount_type == 2) {
					$friend_code = get_session("session_friend");
					$friend_user_id = get_friend_info();
					$friend_type_id = get_session("session_friend_type_id");
					// check whose friends could use coupon
					if ($friends_all || ($friend_user_id && in_array($friend_user_id, $search_friends_ids)) 
						|| ($friend_type_id && in_array($friend_type_id, $search_friends_types_ids))) {

						$friends_coupon = true;
					}
				}

				$min_quantity = $data["min_quantity"];
				$max_quantity = $data["max_quantity"];
				$minimum_amount = $data["minimum_amount"];
				$maximum_amount = $data["maximum_amount"];
				$is_exclusive = $data["is_exclusive"];
				$quantity_limit = $data["quantity_limit"];
				$coupon_uses = $data["coupon_uses"];
				// check if coupon can be applied
				if (!$is_active) {
					$coupon_error = COUPON_NON_ACTIVE_MSG;
				} elseif ($quantity_limit > 0 && $coupon_uses >= $quantity_limit) {
					$coupon_error = COUPON_USED_MSG;
				} elseif ($is_expired) {
					$coupon_error = COUPON_EXPIRED_MSG;
				} elseif ($is_upcoming) {
					$coupon_error = COUPON_UPCOMING_MSG;
				} elseif ($exclusive_applied || ($is_exclusive && $coupons_total > 0))  {
					$coupon_error = COUPON_EXCLUSIVE_MSG;
				} elseif ($discount_type <= 2 && $minimum_amount > $total_price) {
					$coupon_error = str_replace("{minimum_amount}", currency_format($minimum_amount), COUPON_ORDER_AMOUNT_MSG);
				} elseif ($discount_type <= 2 && $maximum_amount && $maximum_amount < $total_price) {
					$coupon_error = str_replace("{minimum_amount}", currency_format($maximum_amount), COUPON_ORDER_AMOUNT_MSG);
					$coupon_error = str_replace("minimum", "maximum", $coupon_error);
				} elseif ($discount_type <= 2 && $min_quantity > $total_quantity) {
					$coupon_error = str_replace("{min_quantity}", $min_quantity, COUPON_MIN_QTY_ERROR);
				} elseif ($discount_type <= 2 && $max_quantity && $max_quantity < $total_quantity) {
					$coupon_error = str_replace("{max_quantity}", $max_quantity, COUPON_MAX_QTY_ERROR);
				} elseif (!($users_all || ($user_id && in_array($user_id, $search_users_ids)) 
					|| ($user_type_id && in_array($user_type_id, $search_users_types_ids)))) {
					$coupon_error = COUPON_CANT_BE_USED_MSG; // coupon can't be used for current user
				} elseif ($users_use_limit && !$user_not_limited) {
					// coupon can't be used more times
					if ($users_use_limit == 1) {
						$coupon_error = COUPON_CAN_BE_USED_ONCE_MSG; 
					} else {
						$coupon_error = str_replace("{use_limit}", $users_use_limit, COUPON_SAME_USE_LIMIT_MSG);
					}
				} elseif ($friends_discount_type > 0 && !$friends_coupon) {
					$coupon_error = COUPON_CANT_BE_USED_MSG; // coupon has friends options which can't be used for current user
				} elseif (($orders_min_goods || $orders_max_goods) && !$orders_goods_coupon) {
					$coupon_error = COUPON_CANT_BE_USED_MSG; // the sum of user purchased goods doesn't match with goods values for this coupon
				} // end coupons checks

				if (!$coupon_error) {
					// check products coupons 
					$coupon_items = false;
					foreach($shopping_cart as $cart_id => $item)
					{
						$item_id = $item["ITEM_ID"];
						$properties_more = $item["PROPERTIES_MORE"];
						if (!$item_id || $properties_more > 0) { 
							// ignore the products which has options to be added first
							continue;
						}
						$quantity = $item["QUANTITY"];
						$check_price = $item["CHECK_PRICE"];
						$max_item_discount = $item["MAX_ITEM_DISCOUNT"];
						// add a new coupon
						if ($discount_type == 3 || $discount_type == 4) {
							if ($check_price >= $minimum_amount && 
								$quantity >= $min_quantity && 
								(!$maximum_amount || $check_price <= $maximum_amount) && 
								(!$max_quantity || $quantity <= $max_quantity) && 
								($items_all || in_array($item_id, $search_items_ids) || in_array($item_type_id, $search_items_types_ids)) ) {
								// add coupon to products
								$coupon_items = true;
								if ($discount_type == 3) {
									$discount_amount = round(($check_price / 100) * $coupon_discount, 2);
								} else {
									$discount_amount = $coupon_discount;
								}
								if ($discount_amount > $max_item_discount) {
									$discount_amount = $max_item_discount;
								}
								//$max_item_discount -= $discount_amount;
								$shopping_cart[$cart_id]["MAX_ITEM_DISCOUNT"] -= $discount_amount;
								if (!isset($shopping_cart[$cart_id]["COUPONS"][$new_coupon_id])) {
									// descrease total_price variable for coupon discount
									$total_price -= ($quantity * $discount_amount);
									$new_coupons_total++;
								}
								$shopping_cart[$cart_id]["COUPONS"][$new_coupon_id] = array(
									"COUPON_ID" => $new_coupon_id, "EXCLUSIVE" => $is_exclusive, 
									"DISCOUNT_QUANTITY" => $discount_quantity,
									"DISCOUNT_AMOUNT" => $discount_amount, "AUTO_APPLY" => $coupon_auto_apply,
								);
								if ($is_exclusive) { $exclusive_applied = true; }
								$coupons_total++;
							}
						}
					} 
					if (($discount_type == 3 || $discount_type == 4) && !$coupon_items) {
						$coupon_error = COUPON_PRODUCTS_MSG;
					}
					// end products checks 
	    
					// check order coupons
					if ($discount_type <= 2 || $discount_type == 5) {
						if (!isset($order_coupons[$new_coupon_id])) {
							$new_coupons_total++;
						}
						// add new coupon to system
						$order_coupons[$new_coupon_id] = array(
							"COUPON_ID" => $new_coupon_id, "DISCOUNT_TYPE" => $discount_type, 
							"EXCLUSIVE" => $is_exclusive, "COUPON_TAX_FREE" => $coupon_tax_free, 
							"MIN_QUANTITY" => $min_quantity, "MAX_QUANTITY" => $max_quantity, 
							"MIN_AMOUNT" => $minimum_amount, "MAX_AMOUNT" => $maximum_amount, 
							"ORDER_TAX_FREE" => $coupon_order_tax_free, "AUTO_APPLY" => $coupon_auto_apply,
						);
						if ($is_exclusive) { $exclusive_applied = true; }
						$coupons_total++;
					}
					// end order coupons checks
				}
	  
				if (strtolower($coupon_code) == strtolower($new_coupon_code) && $coupon_error) {
					$new_coupon_error = $coupon_error;
				}
			} 
			
		}
		// end check a new coupons and auto-applied coupons

		// update shopping cart and order coupons
		set_session("shopping_cart", $shopping_cart);
		set_session("session_coupons", $order_coupons);

		// return number of applied coupons
		return $new_coupons_total;
	}

	function prepare_saved_types() 
	{
		global $t, $db, $table_prefix, $saved_types_parsed;

		$total_types = 0;
		if (!$saved_types_parsed) {

			$t->set_file("saved_types_block", "block_saved_types.html");
			$t->set_var("saved_types_options", "");
			$t->set_var("saved_types_descs", "");
			$t->set_var("saved_type_info", "");
			$t->set_var("saved_types_selection", "");
			// check saved types
			$sql  = " SELECT * FROM " .$table_prefix . "saved_types ";
			$sql .= " WHERE is_active=1 ";
			$db->query($sql);
			if ($db->next_record()) {
				$active_type_id = $db->f("type_id");
				do {
					$total_types++;
					$type_id = $db->f("type_id");
					$type_name = $db->f("type_name");
					$type_desc = $db->f("type_desc");
					$t->set_var("type_id", $type_id);
					$t->set_var("type_name", htmlspecialchars($type_name));
					$t->set_var("type_desc", $type_desc);
					if ($active_type_id == $type_id) {
						$active_type_id = $db->f("type_id");
						$t->set_var("type_id_selected", "selected");
						$t->set_var("type_desc_style", "display: block;");
					} else {
						$t->set_var("type_id_selected", "");
						$t->set_var("type_desc_style", "display: none;");
					}
	  
					$t->parse("saved_types_options", true);
					$t->parse("saved_types_descs", true);
					
				} while ($db->next_record());
				$t->set_var("prev_type_id", $active_type_id);
				if ($total_types == 1) {
					$t->parse("saved_type_info", false);
				} else if ($total_types > 1) {
					$t->parse("saved_types_selection", false);
				}
			}
	  
			$t->set_var("saved_types_total", $total_types);
			$t->parse("saved_types_block", false);
			$saved_types_parsed = true;
		}

		return $total_types;
	}

	function prepare_product_params()
	{
		global $currency, $settings;

		$product_params["amp"] = "+&\"'";
		$product_params["cleft"] = $currency["left"];
		$product_params["cright"] = $currency["right"];
		$product_params["crate"] = $currency["rate"];
		$product_params["cdecimals"] = $currency["decimals"];
		$product_params["cpoint"] = $currency["point"];
		$product_params["cseparator"] = $currency["separator"];

		$show_prices = get_setting_value($settings, "tax_prices", 0);
		$product_params["show_prices"] = $show_prices; 
		$product_params["tax_prices_type"] = get_setting_value($settings, "tax_prices_type", 0); 
		$product_params["points_rate"] = get_setting_value($settings, "points_conversion_rate", 1); 
		$product_params["points_decimals"] = get_setting_value($settings, "points_decimals", 0);
		$product_params["zero_price_type"] = get_setting_value($settings, "zero_price_type", 0);
		$product_params["zero_price_message"] = get_translation(get_setting_value($settings, "zero_price_message", "")); 
		$product_params["zero_product_action"] = get_setting_value($settings, "zero_product_action", 1); 
		$product_params["zero_product_warn"] = get_translation(get_setting_value($settings, "zero_product_warn", "")); 
		if ($show_prices == 2) {
			$tax_note = get_translation(get_setting_value($settings, "tax_note_excl", ""));
		} else {
			$tax_note = get_translation(get_setting_value($settings, "tax_note", ""));
		}
		$product_params["tax_note"] = $tax_note;

		return $product_params;
	}

	function set_product_params($product_params)
	{
		global $t, $currency, $settings;
		$params = "";
		foreach($product_params as $param_name => $param_value) {
			if ($params) { $params .= "&"; }
			$param_value = prepare_js_value($param_value);
			$params .= $param_name."=".$param_value;
		}
		$t->set_var("product_params", $params);
	}

	function prepare_js_value($js_value)
	{
		$find = array("%", "+", "&", "\"", "'", "\n", "\r", "=");
		$replace = array("%25", "%2B", "%26", "%22", "%27", "%0A", "%0D", "%3D");
		$js_value = str_replace($find, $replace, $js_value);
		return $js_value;
	}

	function calculate_control_price($values_ids, $values_text, $property_price_type, $property_price_amount, $free_price_type, $free_price_amount)
	{
		$controls_price = 0;
		$used_controls = 0; $free_controls = 0;
		$controls_text = ""; $free_letters = 0;
		// if property has some specified values
		if (sizeof($values_ids) > 0) {
			if ($free_price_amount != 1) {
				$free_price_amount = intval($free_price_amount);
			}
			if ($free_price_type == 2) {
				$free_controls = $free_price_amount;
			} else if ($free_price_type == 3 || $free_price_type == 4) {
				$free_letters = $free_price_amount;
			}
	  
			foreach ($values_ids as $id => $value) {
				$used_controls++;
				if (isset($values_text[$value])) {
					$controls_text .= $values_text[$value];
				}
				if ($free_controls >= $used_controls) {
					if ($property_price_type == 3) {
						$free_letters = strlen($controls_text);
					} else if ($property_price_type == 4) {
						$non_space_text = preg_replace("/[\n\r\s]/", "", $controls_text);
						$free_letters = strlen($non_space_text);
					}
				}
			}	
			if ($property_price_type == 1) {
				$controls_price += $property_price_amount;
			} else if ($property_price_type == 2) {
				if ($used_controls > $free_letters) {
					$controls_price += ($property_price_amount * ($used_controls - $free_controls));
				}
			} else if ($property_price_type == 3) {
				$text_length = strlen($controls_text);
				if ($text_length > $free_letters) {
					$controls_price += ($property_price_amount * ($text_length - $free_letters));
				}
			} else if ($property_price_type == 4) {
				$text_length = strlen(preg_replace("/[\n\r\s]/", "", $controls_text));
				if ($text_length > $free_letters) {
					$controls_price += ($property_price_amount * ($text_length - $free_letters));
				}
			}
			if ($free_price_type == 1) {
				$controls_price -= $free_price_amount;
			}
		}
		return $controls_price;
	}

	function product_image_fields($image_type, &$image_type_name, &$image_field, &$image_alt_field, &$watermark, &$product_no_image)
	{
		global $settings;
		if ($image_type == 1) {
			$image_type_name = "tiny";
			$image_field = "tiny_image";
			$image_alt_field = "tiny_image_alt";
			$watermark = get_setting_value($settings, "watermark_tiny_image", 0);
			$product_no_image = get_setting_value($settings, "product_no_image_tiny", "");
		} elseif ($image_type == 2) {
			$image_type_name = "small";
			$image_field = "small_image";
			$image_alt_field = "small_image_alt";
			$watermark = get_setting_value($settings, "watermark_small_image", 0);
			$product_no_image = get_setting_value($settings, "product_no_image", "");
		} elseif ($image_type == 3) {
			$image_type_name = "large";
			$image_field = "big_image";
			$image_alt_field = "big_image_alt";
			$watermark = get_setting_value($settings, "watermark_big_image", 0);
			$product_no_image = get_setting_value($settings, "product_no_image", "");
		} elseif ($image_type == 4) {
			$image_type_name = "super";
			$image_field = "super_image";
			$image_alt_field = "big_image_alt";
			$watermark = get_setting_value($settings, "watermark_super_image", 0);
			$product_no_image = get_setting_value($settings, "product_no_image", "");
		} else {
			$image_field = ""; $image_alt_field = "";
			$watermark = ""; $product_no_image = "";
		}
	}
	
	function product_image_icon($item_id, $title, $image, $image_type, $text = false) 
	{
		global $settings, $root_folder_path, $is_ssl;

		$site_url = get_setting_value($settings, "site_url", "");
		$secure_url = get_setting_value($settings, "secure_url", "");
		if ($is_ssl) {
			$absolute_url = $secure_url;		
		} else {
			$absolute_url = $site_url;
		}
		$open_large_image = get_setting_value($settings, "open_large_image", 0);
		$property_function = ($open_large_image) ? "popupImage(this, '".$site_url."'); return false;" : "openImage(this); return false;";			
		$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
		
		if ($image_type == 1) {
			$type = "tiny";
			$watermark = get_setting_value($settings, "watermark_tiny_image", 0);
		} elseif ($image_type == 2) {
			$type = "small";
			$watermark = get_setting_value($settings, "watermark_small_image", 0);
		} elseif ($image_type == 3) {
			$type = "large";
			$watermark = get_setting_value($settings, "watermark_big_image", 0);
		} elseif ($image_type == 4) {
			$type = "super";
			$watermark = get_setting_value($settings, "watermark_super_image", 0);
		}
		
		if (!preg_match("/^([a-zA-Z]*):\/\/(.*)/i", $image)) {			
			if (!$open_large_image) {
				$image_size = @getimagesize($root_folder_path . $image);
				if (is_array($image_size)) {																		
					$property_function =  " openImage(this, " . $image_size[0]  . ", " . $image_size[1]  . "); return false;";	
				}
			}
			if ($watermark || $restrict_products_images) { 
				$image = $site_url . "image_show.php?item_id=" . $item_id . "&type=" . $type . "&vc=".md5($image); 
			} else {
				$image = $root_folder_path . $image;
			}
		}
		$property_control  = "<a style='display: inline;' href='" . $image .  "' ";
		$property_control .= " title=\"" . htmlspecialchars($title) . "\" onClick=\"" . $property_function . "\">";
		if ($text) {
			$property_control .= $text;
		} else {
			$property_control .= "<img src='". $absolute_url . "images/icons/view_page.gif' width='16' height='16' alt='View' border='0'>";
		}
		$property_control .= "</a>";				
		
		return $property_control;
	}
	
	function is_new_product($new_product_date = false) 
	{
		global $settings, $table_prefix, $db;
		$new_product_enable = get_setting_value($settings, "new_product_enable", 0);
		if (!$new_product_enable) return false;		
		if (!$new_product_date) return false;
		
		$new_date = strtotime($new_product_date);		
		
		$new_product_range = get_setting_value($settings, "new_product_range", 0);
		switch ($new_product_range) {
			case 0:
				// last week
				$limit_date = strtotime("-7 days");
			break;
			case 1:
				// last month
				$limit_date = strtotime("-30 days");				
			break;
			case 2:
				// last x days
				$new_product_x_days = get_setting_value($settings, "new_product_x_days", 0);
				$limit_date = strtotime("-" . $new_product_x_days ." days");				
			break;
			case 3:
				// from date
				$new_product_from_date = get_setting_value($settings, "new_product_from_date", "");
				$limit_date = strtotime($new_product_from_date);				
			break;
		}
		
		return ($limit_date < $new_date);		
	}

	function recalculate_shopping_cart()
	{
		$shopping_cart = get_session("shopping_cart");
		if (is_array($shopping_cart) && sizeof($shopping_cart) > 0) {
			foreach($shopping_cart as $cart_id => $item) {
				get_item_info($item);
				$shopping_cart[$cart_id] = $item;
			}
			set_session("shopping_cart", $shopping_cart);
		}
	}

?>