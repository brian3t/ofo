<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  items_properties.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	function show_items_properties($form_id, $item_id, $item_type_id, $item_price, $tax_free, $type, &$product_params, $show_price_matrix = false, $discount_applicable = true, $properties_percent = 0)
	{
	 	global $t, $db, $site_id, $table_prefix;
		global $settings, $currency, $tax_rates, $root_folder_path;
		
		// connection for properties
		$dbp = new VA_SQL();
		$dbp->DBType     = $db->DBType;
		$dbp->DBDatabase = $db->DBDatabase;
		$dbp->DBUser     = $db->DBUser;
		$dbp->DBPassword = $db->DBPassword;
		$dbp->DBHost     = $db->DBHost;
		$dbp->DBPort       = $db->DBPort;
		$dbp->DBPersistent = $db->DBPersistent;
  
		$eol = get_eol();
		$discount_type = get_session("session_discount_type");
		$discount_amount = get_session("session_discount_amount");
		$display_products = get_setting_value($settings, "display_products", 0);
		$tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
		$tax_prices = get_setting_value($settings, "tax_prices", 0);
		$points_conversion_rate = get_setting_value($settings, "points_conversion_rate", 1);
		$points_decimals = get_setting_value($settings, "points_decimals", 0);
		$points_prices = get_setting_value($settings, "points_prices", 0);

		// option price options
		$option_positive_price_right = get_setting_value($settings, "option_positive_price_right", ""); 
		$option_positive_price_left = get_setting_value($settings, "option_positive_price_left", ""); 
		$option_negative_price_right = get_setting_value($settings, "option_negative_price_right", ""); 
		$option_negative_price_left = get_setting_value($settings, "option_negative_price_left", "");
		$option_notes = get_setting_value($settings, "option_notes", 1);

		$components_list_style = get_setting_value($settings, "components_list_style", "");
		
		$user_id = get_session("session_user_id");		
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

		// check product properites
		$properties_ids = "";
		$selected_price = 0;
		$is_properties = false;
		$t->set_var("properties", "");

		$options = array(); $components = array(); $components_price = 0; $components_tax_price = 0; 
		$components_points_price = 0; $components_reward_points = 0; $components_reward_credits = 0;
		$sql  = " SELECT * ";
		$sql .= " FROM " . $table_prefix . "items_properties ";
		$sql .= " WHERE (item_id=" . $db->tosql($item_id, INTEGER) . " OR item_type_id=" . $db->tosql($item_type_id, INTEGER) . ")";
		if ($type == "list") {
			$sql .= " AND use_on_list=1 ";
		} elseif ($type == "options") {
			$sql .= " AND use_on_second=1 ";
		} else {
			$sql .= " AND use_on_details=1 ";
		}
		$sql .= " ORDER BY property_order, property_id ";
		$dbp->query($sql);
		while ($dbp->next_record()) {
			$property_id = $dbp->f("property_id");
			$property_type_id = $dbp->f("property_type_id");
			$usage_type = $dbp->f("usage_type");
			if ($property_type_id == 2) {
				$sub_item_id = $dbp->f("sub_item_id");
				$sub_quantity = $dbp->f("quantity");
				$sub_price = $dbp->f($additional_price_field);				
				$components[$property_id] = array("item_id" => $sub_item_id, "quantity" => $sub_quantity, "price" => $sub_price, "usage_type" => $usage_type);
			} else {
				$option = array(
					"property_id" => $property_id,
					"property_type_id" => $property_type_id,
					"usage_type" => $usage_type,
					"property_name" => get_translation($dbp->f("property_name")),
					"parent_property_id" => $dbp->f("parent_property_id"),
					"parent_value_id" => $dbp->f("parent_value_id"),
					"property_description" => $dbp->f("property_description"),
					"property_style" => $dbp->f("property_style"),
					"property_price_type" => $dbp->f("property_price_type"),
					"property_price" => $dbp->f($additional_price_field),
					"free_price_type" => $dbp->f("free_price_type"),
					"free_price_amount" => $dbp->f("free_price_amount"),
					"max_limit_type" => $dbp->f("max_limit_type"),
					"max_limit_length" => $dbp->f("max_limit_length"),
					"control_type" => $dbp->f("control_type"),
					"control_style" => $dbp->f("control_style"),
					"required" => $dbp->f("required"),
					"start_html" => $dbp->f("start_html"),
					"middle_html" => $dbp->f("middle_html"),
					"before_control_html" => $dbp->f("before_control_html"),
					"after_control_html" => $dbp->f("after_control_html"),
					"end_html" => $dbp->f("end_html"),
					"onchange_code" => $dbp->f("onchange_code"),
					"onclick_code" => $dbp->f("onclick_code"),
					"control_code" => $dbp->f("control_code"),
					"values" => array(),
				);
				$options[$property_id] = $option;
			}
		}

		// check usage option for options and components
		//var_dump($component_info["usage_type"]);
		foreach ($components as $property_id => $component_info) {
			if ($component_info["usage_type"] == 2 || $component_info["usage_type"] == 3) {
				$sql  = " SELECT item_id FROM " . $table_prefix . "items_properties_assigned ";
				$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
				$sql .= " AND property_id=" . $db->tosql($property_id, INTEGER);
				$dbp->query($sql);
				if (!$dbp->next_record()) {
					// remove component if it wasn't assigned to product
					unset($components[$property_id]);
					continue;
				}
			}
			
			$dbp->query(VA_Products::_sql("i.item_id = ". $db->tosql($component_info["item_id"], INTEGER), VIEW_ITEMS_PERM));
			if (!$dbp->next_record()) {
				unset($components[$property_id]);
				continue;
			}
		}

		foreach ($options as $property_id => $option) {
			if ($option["usage_type"] == 2 || $option["usage_type"] == 3) {
				$sql  = " SELECT item_id, property_description FROM " . $table_prefix . "items_properties_assigned ";
				$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
				$sql .= " AND property_id=" . $db->tosql($property_id, INTEGER);
				$dbp->query($sql);
				if (!$dbp->next_record()) {
					// remove option if it wasn't assigned to product
					unset($options[$property_id]);
				}
			}
		}

		$is_quantity_price = false;
		$max_available_quantity = 1;
		// calculate subcomponents price
		if (sizeof($components) > 0) {
			foreach ($components as $property_id => $component_info) {
				// get subcomponent information
				$sub_item_id = $component_info["item_id"];
				$sub_quantity = $component_info["quantity"];
				$component_price = $component_info["price"];
				// get original information for component product
				$price = 0; $buying_price = 0; $points_price = 0; $reward_points = 0; $reward_credits = 0;
				$sql  = " SELECT i.item_type_id, i.buying_price, i." . $price_field . ", i.is_sales, i." . $sales_field . ", i.tax_free, ";
				$sql .= " i.is_points_price, i.points_price, i.reward_type, i.reward_amount, i.credit_reward_type, i.credit_reward_amount ";
				$sql .= " FROM " . $table_prefix . "items i ";
				$sql .= " WHERE i.item_id=" . $db->tosql($sub_item_id, INTEGER);
				$dbp->query($sql);
				if ($dbp->next_record()) {
					$sub_type_id = $dbp->f("item_type_id");
					$sub_tax_free = $dbp->f("tax_free");
					$buying_price = $dbp->f("buying_price");
					$is_points_price = $dbp->f("is_points_price");
					$points_price = $dbp->f("points_price");
					$reward_type = $dbp->f("reward_type");
					$reward_amount = $dbp->f("reward_amount");
					$credit_reward_type = $dbp->f("credit_reward_type");
					$credit_reward_amount = $dbp->f("credit_reward_amount");
					if (!strlen($is_points_price)) {
						$is_points_price = $points_prices;
					}
					if ($sub_quantity < 1) { $sub_quantity = 1; }
					if (strlen($component_price)) {
						$price = $component_price;
					} else {
						$price = $dbp->f($price_field);
						$is_sales = $dbp->f("is_sales");
						$sales_price = $dbp->f($sales_field);
						
						$user_price  = false; 
						$user_price_action = 0;
						$q_prices    = get_quantity_price($sub_item_id, $sub_quantity);
						if ($q_prices) {
							$user_price  = $q_prices [0];
							$user_price_action = $q_prices [2];
						}

						if ($user_price > 0 && ($user_price_action > 0 || !$discount_type)) {
							$price = $user_price;
						} elseif ($is_sales) {
							$price = $sales_price; 
						}
						if ($user_price_action != 1) {
							if ($discount_type == 1 || $discount_type == 3) {
								$price -= round(($price * $discount_amount) / 100, 2);
							} elseif ($discount_type == 2) {
								$price -= round($discount_amount, 2);
							} elseif ($discount_type == 4) {
								$price -= round((($price - $buying_price) * $discount_amount) / 100, 2);
							}
						}
					}
					if ($points_price <= 0) {
						$points_price = $price * $points_conversion_rate;
					}
					$reward_points = calculate_reward_points($reward_type, $reward_amount, $price, $buying_price, $points_conversion_rate, $points_decimals);
					$reward_credits = calculate_reward_credits($credit_reward_type, $credit_reward_amount, $price, $buying_price);
					$components[$property_id]["base_price"] = $price;
					$components[$property_id]["buying_price"] = $buying_price;
					$components[$property_id]["item_type_id"] = $item_type_id;
					$components[$property_id]["tax_free"] = $tax_free;

					// add to total values
					$components_price += ($price * $sub_quantity);
					$tax_amount = get_tax_amount($tax_rates, $sub_type_id, $price, $sub_tax_free, $sub_tax_percent);
					$components_tax_price += ($tax_amount * $sub_quantity);
					$components_points_price += ($points_price * $sub_quantity);
					$components_reward_points += ($reward_points * $sub_quantity);
					$components_reward_credits += ($reward_credits * $sub_quantity);

					// check components quantity prices
					$sql  = " SELECT ip.is_active, ip.min_quantity, ip.max_quantity, ";
					$sql .= " ip.price, ip.properties_discount, ip.discount_action ";
					$sql .= " FROM " . $table_prefix . "items_prices ip ";
					$sql .= " WHERE ip.item_id=" . $db->tosql($sub_item_id, INTEGER);
					$sql .= " AND ip.is_active=1 ";
					if (isset($site_id)) {
						$sql .= " AND (ip.site_id=0 OR ip.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ") ";
					} else {
						$sql .= " AND ip.site_id=0 ";
					}
					if (strlen($user_type_id)) {
						$sql .= " AND (ip.user_type_id=0 OR ip.user_type_id=" . $db->tosql($user_type_id, INTEGER, true, false) . ") ";
					} else {
						$sql .= " AND ip.user_type_id=0 ";
					}
					$sql .= " ORDER BY ip.site_id DESC, ip.user_type_id DESC, ip.min_quantity ";		
					$dbp->query($sql);
					while ($dbp->next_record()) {
						$min_quantity = $dbp->f("min_quantity");
						$max_quantity = $dbp->f("max_quantity");
						$quantity_price = $dbp->f("price");
						$properties_discount = $dbp->f("properties_discount");
						$discount_action = $dbp->f("discount_action");
						if ($discount_type > 0 && $discount_action == 0) {
							// don't use this price as user discount in use
							continue;
						} 
						if ($discount_type > 0 && $discount_action == 2) {
							// apply user discount to quantity price
							if ($discount_type == 1 || $discount_type == 3) {
								$quantity_price -= round(($quantity_price * $discount_amount) / 100, 2);
							} elseif ($discount_type == 2) {
								$quantity_price -= round($discount_amount, 2);
							} elseif ($discount_type == 4) {
								$quantity_price -= round((($quantity_price - $buying_price) * $discount_amount) / 100, 2);
							}
						}
						$is_quantity_price = true;
						$components[$property_id]["quantities"][$min_quantity] = array("min_quantity" => $min_quantity, "max_quantity" => $max_quantity, "quantity_price" => $quantity_price);
						if ($max_quantity > $max_available_quantity) { $max_available_quantity = $max_quantity; }
					}
				}
			}
		}

		// check product prices based on quantity
		$quantity_prices = "";
		$item_quantities = array();
		
		$is_price_matrix = false;				
		$t->set_var("price_matrix", "");
		$t->set_var("matrix_prices", "");
		$t->set_var("matrix_quantities", "");

		$order_by = " ORDER BY ";
		$sql  = " SELECT i.buying_price, ip.is_active, ip.min_quantity, ip.max_quantity, ";
		$sql .= " ip.price, ip.properties_discount, ip.discount_action ";
		$sql .= " FROM " . $table_prefix . "items_prices ip, " . $table_prefix . "items i ";
		$sql .= " WHERE ip.item_id=i.item_id ";
		$sql .= " AND ip.item_id=" . $db->tosql($item_id, INTEGER);
		$sql .= " AND ip.is_active=1 ";
		
		if (isset($site_id)) {
			$sql .= " AND (ip.site_id=0 OR ip.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ") ";
			$order_by .= " ip.site_id DESC, ";
		} else {
			$sql .= " AND ip.site_id=0 ";
		}
		
		if (strlen($user_type_id)) {
			$sql .= " AND (ip.user_type_id=0 OR ip.user_type_id=" . $db->tosql($user_type_id, INTEGER, true, false) . ") ";
			$order_by .= " ip.user_type_id DESC, ";
		} else {
			$sql .= " AND ip.user_type_id=0 ";
		}

		$order_by .= " ip.min_quantity ";		
		$dbp->query($sql . $order_by);
		
		while ($dbp->next_record()) {
			$is_active = $dbp->f("is_active");
			$min_quantity = $dbp->f("min_quantity");
			$max_quantity = $dbp->f("max_quantity");
			$price = $dbp->f("price");
			$properties_discount = $dbp->f("properties_discount");
			$discount_action = $dbp->f("discount_action");
			$buying_price = $dbp->f("buying_price");
			if ($discount_type > 0) {	
				if ($discount_action == 0) {
					$is_active = 0;
				} elseif ($discount_action == 2) {
					if ($discount_type == 1 || $discount_type == 3) {
						$price -= round(($price * $discount_amount) / 100, 2);
					} elseif ($discount_type == 2) {
						$price -= round($discount_amount, 2);
					} elseif ($discount_type == 4) {
						$price -= round((($price - $buying_price) * $discount_amount) / 100, 2);
					}
				}
			}

			if ($is_active) {
				$is_quantity_price = true;
				$item_quantities[$min_quantity] = array(
					"max_quantity" => $max_quantity, "quantity_price" => $price, "properties_discount" => $properties_discount);
				if ($max_quantity > $max_available_quantity) { $max_available_quantity = $max_quantity; }
			}
		}

		$quantities = array();
		$min_quantities = array();
		$max_quantities = array();
		if ($is_quantity_price) {
			// check for min and max values
			$components["parent_product"]["base_price"] = $item_price;
			$components["parent_product"]["quantities"] = $item_quantities;
			$components["parent_product"]["item_type_id"] = $item_type_id;
			$components["parent_product"]["tax_free"] = $tax_free;
		  foreach ($components as $property_id => $component) {
				$component_quantities = isset($components[$property_id]["quantities"]) ? $components[$property_id]["quantities"] : "";
				if (is_array($component_quantities)) {
					ksort($component_quantities);
					$last_min_quantity = 0; $last_max_quantity = 0;
					foreach($component_quantities as $min_quantity => $quantity_info) {
						$max_quantity = $quantity_info["max_quantity"];
						if ($min_quantity > ($last_max_quantity + 1)) {
							if (!in_array(($last_max_quantity + 1), $min_quantities)) { $min_quantities[] = ($last_max_quantity + 1); }
							if (!in_array(($min_quantity - 1), $max_quantities)) { $max_quantities[] = ($min_quantity - 1); }
						}
						if (!in_array($min_quantity, $min_quantities)) { $min_quantities[] = $min_quantity; }
						if (!in_array($max_quantity, $max_quantities)) { $max_quantities[] = $max_quantity; }
						//$last_min_quantity = $min_quantity;
						$last_max_quantity = $max_quantity;
					}
					if ($max_available_quantity > $last_max_quantity) {
						if (!in_array(($last_max_quantity + 1), $min_quantities)) { $min_quantities[] = ($last_max_quantity + 1); }
						if (!in_array($max_available_quantity, $max_quantities)) { $max_quantities[] = $max_available_quantity; }
					}
				}
			}
			
			// prepare prices ranges 
			sort($min_quantities); sort($max_quantities);
			while (sizeof($min_quantities) || sizeof($max_quantities)) {
				$min_quantity = array_shift($min_quantities);
				$max_quantity = array_shift($max_quantities);
				$quantity_price = 0; $quantity_tax = 0; $properties_discount = 0;
				// check components and parent product prices
			  foreach ($components as $property_id => $component) {
					$component_quantities = isset($component["quantities"]) ? $component["quantities"] : "";
					$range_found = false; $component_price = 0;
					if (is_array($component_quantities)) {
						foreach($component_quantities as $component_min => $quantity_info) {
							$component_max = $quantity_info["max_quantity"];
							if ($component_min <= $min_quantity && $component_max >= $max_quantity) {
								$range_found = true;
								$component_price = $quantity_info["quantity_price"];
								if (isset($quantity_info["properties_discount"])) {
									$properties_discount = $quantity_info["properties_discount"];
								}
							}
						}
					}
					if (!$range_found) {
						$component_price = $component["base_price"];
					}
					$component_tax = get_tax_amount($tax_rates, $component["item_type_id"], $component_price, $component["tax_free"], $sub_tax_percent);

					$quantity_price += $component_price;
					$quantity_tax += $component_tax;
				}
				$quantities[$min_quantity] = array(
					"max_quantity" => $max_quantity, "quantity_price" => $quantity_price, "quantity_tax" => $quantity_tax, "properties_discount" => $properties_discount
				);
			}
		}

		// check if we can group some pricing ranges
		$last_min_quantity = ""; $last_price = ""; $last_tax = ""; $last_discount = "";
		foreach ($quantities as $min_quantity => $quantity_info) {
			if ($last_min_quantity && $last_price == $quantity_info["quantity_price"] 
				&& $last_tax == $quantity_info["quantity_tax"]  && $last_discount == $quantity_info["properties_discount"]) {
				$quantities[$last_min_quantity]["max_quantity"] = $quantity_info["max_quantity"];
				unset($quantities[$min_quantity]);
			} else {
				$last_min_quantity = $min_quantity; 
				$last_price = $quantity_info["quantity_price"]; 
				$last_tax = $quantity_info["quantity_tax"]; 
				$last_discount = $quantity_info["properties_discount"];
			}
		}

		foreach ($quantities as $min_quantity => $quantity_info) {
			$max_quantity = $quantity_info["max_quantity"];
			$quantity_price = $quantity_info["quantity_price"];
			$quantity_tax = $quantity_info["quantity_tax"];
			$properties_discount = $quantity_info["properties_discount"];

			if ($quantity_prices) { $quantity_prices .= ","; }
			$quantity_prices .= $min_quantity . "," . $max_quantity . "," . $quantity_price . "," . $quantity_tax . "," . round($properties_discount, 2);

			// parse price matrix
			if ($show_price_matrix) {
				if ($min_quantity > 1 || $max_quantity > 1) {
					$is_price_matrix = true;
				}
				if ($min_quantity == $max_quantity) {
					$matrix_quantity = $min_quantity;
				} else if ($max_quantity == MAX_INTEGER) {
					$matrix_quantity = $min_quantity."+";
				} else {
					$matrix_quantity = $min_quantity."-".$max_quantity;
				}
				$t->set_var("matrix_quantity", $matrix_quantity);
				$t->parse("matrix_quantities", true);
		  
				if ($tax_prices_type == 1) {
					$price_incl = $quantity_price;
					$price_excl = $quantity_price - $quantity_tax;
				} else {
					$price_incl = $quantity_price + $quantity_tax;
					$price_excl = $quantity_price;
				}
				if ($tax_prices == 0 || $tax_prices == 1) {
					$t->set_var("matrix_price", currency_format($price_excl));
				} else {
					$t->set_var("matrix_price", currency_format($price_incl));
				}
				if ($tax_prices == 1) {
					$t->set_var("matrix_tax_price", "(".currency_format($price_incl).")");
				} else if ($tax_prices == 2) {
					$t->set_var("matrix_tax_price", "(".currency_format($price_excl).")");
				} 

				$t->parse("matrix_prices", true);
			}

		}

		if ($is_price_matrix) {
			$t->parse("price_matrix", false);
		}


		$product_params["quantity_price"] = $quantity_prices;

		// show options and components selection
		$open_large_image = get_setting_value($settings, "open_large_image", 0);
		$open_large_image_function = ($open_large_image) ? "popupImage(this); return false;" : "openImage(this); return false;";

		$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
		$watermark                = get_setting_value($settings, "watermark_big_image", 0);
		$tiny_watermark           = get_setting_value($settings, "watermark_tiny_image", 0);
		$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
		$friendly_extension = get_setting_value($settings, "friendly_extension", "");
		$product_link = get_custom_friendly_url("product_details.php") . "?item_id=";
	
		if (sizeof($options) > 0)
		{
			$is_properties = true;
			foreach ($options as $property_id => $option) 
			{
				$property_id = $option["property_id"];
				$usage_type = $option["usage_type"];
				$property_type_id = $option["property_type_id"];
				$object_id = $form_id . "_" . $property_id;
				$property_block_id = "pr_" . $object_id;
				$property_name = $option["property_name"];
				$parent_property_id = $option["parent_property_id"];
				$parent_value_id = $option["parent_value_id"];

				$property_price_type = $option["property_price_type"];
				$property_price = $option["property_price"];
				$free_price_type = $option["free_price_type"];
				$free_price_amount = $option["free_price_amount"];
				$max_limit_type = $option["max_limit_type"];
				$max_limit_length = $option["max_limit_length"];

				$property_description = $option["property_description"];
				$property_style = $option["property_style"];
				$control_type = $option["control_type"];
				$control_style = $option["control_style"];
				$property_required = $option["required"];
				$start_html = $option["start_html"];
				$middle_html = $option["middle_html"];
				$before_control_html = $option["before_control_html"];
				$after_control_html = $option["after_control_html"];
				$end_html = $option["end_html"];
				$onchange_code = $option["onchange_code"];
				$onclick_code = $option["onclick_code"];
				$control_code = $option["control_code"];

				if ($option_notes) {
					$option_notes_html = "";

					if ($property_price != 0) {
						if ($property_price_type == 1) {
							$option_notes_html .= "<br> * ". PRICE_MSG . " : " . currency_format($property_price);
						} else if ($property_price_type == 2) {
							$option_notes_html .= "<br> * " . currency_format($property_price) . " " . PER_LINE_MSG; 
						} else if ($property_price_type == 3) {
							$option_notes_html .= "<br> * " . currency_format($property_price) . " " . PER_LETTER_MSG;
						} else if ($property_price_type == 4) {
							$option_notes_html .= "<br> * " . currency_format($property_price) . " " . PER_NON_SPACE_LETTER_MSG;
						}
					}

					if ($free_price_type == 1) {
						$option_notes_html .= "<br> * " . DISCOUNT_MSG . ": -" . currency_format($free_price_amount);
					} else if ($free_price_type == 2) {
						$option_notes_html .= "<br> * " . str_replace("{free_price_amount}", intval($free_price_amount), FIRST_CONTROLS_ARE_FREE_MSG); //First " . intval($free_price_amount) . " controls are free";
					} else if ($free_price_type == 3) {
						$option_notes_html .= "<br> * " . str_replace("{free_price_amount}", intval($free_price_amount), FIRST_LETTERS_ARE_FREE_MSG); //First " . intval($free_price_amount) . " letters are free";
					} else if ($free_price_type == 4) {
						$option_notes_html .= "<br> * " . str_replace("{free_price_amount}", intval($free_price_amount), FIRST_NONSPACE_LETTERS_ARE_FREE_MSG); //First " . intval($free_price_amount) . " non-space letters are free";
					}

					if ($max_limit_type == 1) {
						$option_notes_html .= "<br> * " . $max_limit_length . " " . LETTERS_ALLOWED_MSG;
					} else if ($max_limit_type == 2) {
						$option_notes_html .= "<br> * " . $max_limit_length . " " . LETTERS_ALLOWED_PER_LINEMSG;
					}
					if ($option_notes_html) {
						$end_html = $option_notes_html . $end_html;
					}
				}
				
				if ($properties_ids) $properties_ids .= ",";
				$properties_ids .= $property_id;
				$tags_replace = array("{form_id}", "{item_id}", "{option_id}", "{property_id}", "{type}");
				$tags_values  = array($form_id, $item_id, $property_id, $property_id, $type);
				if ($onchange_code) {	
					$onchange_code = str_replace($tags_replace, $tags_values, $onchange_code); 
				}
				if ($onclick_code) {	
					$onclick_code = str_replace($tags_replace, $tags_values, $onclick_code); 
				}
				if ($control_code) {	
					$control_code = str_replace($tags_replace, $tags_values, $control_code); 
				}
				if ($start_html) {	
					$start_html = str_replace($tags_replace, $tags_values, $start_html); 
				}
				if ($middle_html) {	
					$middle_html = str_replace($tags_replace, $tags_values, $middle_html); 
				}
				if ($before_control_html) {	
					$before_control_html = str_replace($tags_replace, $tags_values, $before_control_html); 
				}
				if ($after_control_html) {	
					$after_control_html = str_replace($tags_replace, $tags_values, $after_control_html); 
				}
				if ($end_html) {	
					$end_html = str_replace($tags_replace, $tags_values, $end_html); 
				}

				$property_control  = "";
				$property_control .= "<input type=\"hidden\" name=\"property_name_" . $property_id . "\"";
				$property_control .= " value=\"" . strip_tags($property_name) . "\">";
				$property_control .= "<input type=\"hidden\" name=\"property_required_" . $property_id . "\"";
				$property_control .= " value=\"" . intval($property_required) . "\">";
				$property_control .= "<input type=\"hidden\" name=\"property_control_" . $property_id . "\"";
				$property_control .= " value=\"" . strtoupper($control_type) . "\">";
				$property_control .= "<input type=\"hidden\" name=\"property_parent_id_" . $property_id . "\"";
				$property_control .= " value=\"" . $parent_property_id . "\">";
				$property_control .= "<input type=\"hidden\" name=\"property_parent_value_id_" . $property_id . "\"";
				$property_control .= " value=\"" . $parent_value_id . "\">";
				$property_control .= "<input type=\"hidden\" name=\"property_price_type_" . $property_id . "\"";
				$property_control .= " value=\"" . $property_price_type . "\">";
				$property_control .= "<input type=\"hidden\" name=\"property_price_" . $property_id . "\"";
				$property_control .= " value=\"" . $property_price . "\">";
				$property_control .= "<input type=\"hidden\" name=\"property_free_price_type_" . $property_id . "\"";
				$property_control .= " value=\"" . $free_price_type . "\">";
				$property_control .= "<input type=\"hidden\" name=\"property_free_price_amount_" . $property_id . "\"";
				$property_control .= " value=\"" . $free_price_amount . "\">";

				if ($parent_property_id) {
					if (!isset($options[$parent_property_id]) || sizeof($options[$parent_property_id]["values"]) == 0) {
						$property_style = "display: none;" . $property_style;
					} else if ($parent_value_id && !in_array($parent_value_id, $options[$parent_property_id]["values"])) {
						$property_style = "display: none;" . $property_style;
					}
				}

				if ($property_type_id == 3) {
					$sql_params = array();
					$sql_params["brackets"] = "("; 
					$sql_params["join"]     = " INNER JOIN " . $table_prefix . "items_properties_values ipv ON i.item_id=ipv.sub_item_id)";
					$sql_params["where"]    = " ipv.property_id=" . $db->tosql($property_id, INTEGER);
					$sql = VA_Products::_sql($sql_params, VIEW_ITEMS_PERM);
					$dbp->query($sql);
					$ids = array();
					while ($dbp->next_record()) {
						$ids[] = $dbp->f(0);
					}
					if (!$ids) {
						continue;
					}
				}
				
				$sql  = " SELECT ipv.item_property_id, ipv.quantity, ipv.is_default_value, ipv.percentage_price, ";
				$sql .= " ipv.".$additional_price_field.", ipv.property_value, ipv.sub_item_id, ";
				if ($usage_type == 2 || $usage_type == 3) {
					$sql .= " iva.is_default_value AS override_default, ";
				} 
				if ($property_type_id == 3) {
					$sql .= " i.item_type_id, i.buying_price, i.item_code, i.manufacturer_code, i." . $price_field . ", i.is_sales, i." . $sales_field . ", i.tax_free, i.big_image, i.tiny_image, i.friendly_url ";
					$sql .= " FROM ((" . $table_prefix . "items_properties_values ipv ";
					$sql .= " INNER JOIN " . $table_prefix . "items i ON i.item_id=ipv.sub_item_id) ";
				} else {
					$sql .= " ipv.buying_price, ipv.item_code, ipv.manufacturer_code ";
					$sql .= " FROM (" . $table_prefix . "items_properties_values ipv ";
				}
				if ($usage_type == 2) {
					$sql .= " INNER JOIN " . $table_prefix . "items_values_assigned iva ";
					$sql .= " ON (iva.item_id=" . $db->tosql($item_id, INTEGER) . " AND ipv.item_property_id=iva.property_value_id)) ";
				} else if ($usage_type == 3) {
					$sql .= " LEFT JOIN " . $table_prefix . "items_values_assigned iva ";
					$sql .= " ON (iva.item_id=" . $db->tosql($item_id, INTEGER) . " AND ipv.item_property_id=iva.property_value_id)) ";
				} else {
					$sql .= ")";
				}
				$sql .= " WHERE ipv.property_id=" . $db->tosql($property_id, INTEGER);
				$sql .= " AND ipv.hide_value=0 ";
				$sql .= " AND ((ipv.hide_out_of_stock=1 AND ipv.stock_level > 0) OR ipv.hide_out_of_stock=0 OR ipv.hide_out_of_stock IS NULL)";
				if ($property_type_id == 3) {
					$sql .= " AND i.item_id IN (" . $dbp->tosql($ids, INTEGERS_LIST) . ")";
				}
				$sql .= " ORDER BY ipv.value_order, ipv.item_property_id ";

				if (strtoupper($control_type) == "LISTBOX") {
					$properties_prices = "";
					$properties_values = "<option value=\"\">" . SELECT_MSG . " " . $property_name . "</option>" . $eol;
					$dbp->query($sql);
					$default_property = null;
					$property_function = "return false;";
					while ($dbp->next_record())
					{
						$property_value = get_translation($dbp->f("property_value"));
						$sub_quantity = $dbp->f("quantity");

						$option_price = 0; $option_tax = 0; $option_price_incl = 0; $option_price_excl = 0;
						if ($display_products != 2 || strlen($user_id)) {
							$option_price = $dbp->f($additional_price_field);	
							$percentage_price = $dbp->f("percentage_price");
							if ($percentage_price && $item_price) {
								$option_price += round(($item_price * $percentage_price) / 100, 2);
							}
							$buying_price = $dbp->f("buying_price");	
							if ($property_type_id == 3) {
								$sub_item_id = $dbp->f("sub_item_id");
								$sub_type_id = $dbp->f("item_type_id");
								$sub_tax_free = $dbp->f("tax_free");
								if (!strlen($option_price)) {
									$sub_price = $dbp->f($price_field);
									$sub_buying = $dbp->f("buying_price");
									$sub_is_sales = $dbp->f("is_sales");
									$sub_sales = $dbp->f($sales_field);
									
									$sub_user_price  = false; 
									$sub_user_action= 0;
									$q_prices    = get_quantity_price($sub_item_id, 1);
									if ($q_prices) {
										$sub_user_price  = $q_prices [0];
										$sub_user_action = $q_prices [2];
									}
				
									$prices = get_product_price($sub_item_id, $sub_price, $sub_buying, $sub_is_sales, $sub_sales, $sub_user_price, $sub_user_action, $discount_type, $discount_amount);
									$option_price = $prices["base"];	
								}
								if ($sub_quantity > 1) {
									$option_price = $sub_quantity * $option_price; 
								}
								$option_tax = set_tax_price($sub_item_id, $sub_type_id, $option_price, 0, $sub_tax_free);
							} else {
								$option_price = get_option_price($option_price, $buying_price, $properties_percent, $discount_applicable, $discount_type, $discount_amount);
								$option_tax = set_tax_price($item_id, $item_type_id, $option_price, 0, $tax_free);
							}
							if ($tax_prices_type == 1) {
								$option_price_incl = $option_price;
								$option_price_excl = $option_price - $option_tax;
							} else {
								$option_price_incl = $option_price + $option_tax;
								$option_price_excl = $option_price;
							}
						}
						if ($tax_prices == 2 || $tax_prices == 3) {
							$shown_price = $option_price_incl;
						} else {
							$shown_price = $option_price_excl;
						}

						$item_property_id = $dbp->f("item_property_id");
						$is_default_value = $dbp->f("is_default_value");
						$override_default = $dbp->f("override_default");
						if (strlen($override_default)) {
							$is_default_value = $override_default; 
						}
						$sub_item_id      = $dbp->f("sub_item_id");
						$image            = $dbp->f("big_image");
						
						$property_selected  = "";
						$properties_prices .= "<input type=\"hidden\" name=\"option_price_" . $item_property_id . "\"";
						$properties_prices .= " value=\"" . $option_price . "\">";
						//$property_function = "return false;";
						if ($image) {
							$property_function = $open_large_image_function;
							if (!preg_match("/^([a-zA-Z]*):\/\/(.*)/i", $image)) {
								if (!$open_large_image) {
									$image_size = @getimagesize($image);
									if (is_array($image_size)) {																		
										$property_function =  " openImage(this, " . $image_size[0]  . ", " . $image_size[1]  . "); return false;";								
									}
								}
								if ($watermark || $restrict_products_images) { 
									$image = "image_show.php?item_id=" . $sub_item_id . "&type=large&vc=".md5($image); 
								}
							}
							$properties_prices .= "<input type=\"hidden\" name=\"option_image_" . $item_property_id . "\"";
							$properties_prices .= " value=\"" . $image . "\">";
							$properties_prices .= "<input type=\"hidden\" name=\"option_image_action_" . $item_property_id . "\"";
							$properties_prices .= " onClick='" . $property_function . "'>";		
						}
						if ($is_default_value) {
							$property_selected  = "selected ";
							$selected_price += $option_price;
							$options[$property_id]["values"][] = $item_property_id;
						} 

						
						$properties_values .= "<option " . $property_selected . "value=\"" . htmlspecialchars($item_property_id) . "\">";
						$properties_text = "";
						if ($sub_quantity > 1) {
							$properties_text .= $sub_quantity . " x ";
						}
						$properties_text .= htmlspecialchars($property_value);
						if ($option_price > 0) {
							$properties_text .= $option_positive_price_right . currency_format($shown_price) . $option_positive_price_left;
						} elseif ($option_price < 0) {
							$properties_text .= $option_negative_price_right . currency_format(abs($shown_price)) . $option_negative_price_left;
						}
						$properties_values .= $properties_text ."</option>" . $eol;
						if ($is_default_value) {
							$default_property = array("image" => $image, "text" => $properties_text, "function" => $property_function);							
						}
					}
					$property_control .= $before_control_html;
					$property_control .= "<nobr><select name=\"property_" . $property_id . "\" onChange=\"changeProperty(document.form_".$form_id.");";
					if ($onchange_code) {	$property_control .= $onchange_code; }
					$property_control .= "\"";
					if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
					if ($control_code) {	$property_control .= " " . $control_code . " "; }
					if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
					$property_control .= ">" . $properties_values . "</select>";				
					// images button 
					if ($default_property && $default_property["image"] && $default_property["text"]) {
						$property_control .= "<a style='display: inline;' href='" . $default_property["image"] .  "' ";
						$property_control .= " title=\"" . htmlspecialchars($default_property["text"]) . "\" id='option_image_action_" ;
						$property_control .= $object_id . "' onClick='" . $default_property["function"] . "'>";
						$property_control .= "<img src='images/icons/view_page.gif' alt='" . VIEW_MSG . "iew' border='0'></a></nobr>";
					} else {
						$property_control .= "<a style='display: none;' href='#' id='option_image_action_" ;
						$property_control .= $object_id . "' onClick='$property_function'>";
						$property_control .= "<img src='images/icons/view_page.gif' alt='" . VIEW_MSG . "' border='0'></a></nobr>";
					}
					$property_control .= $properties_prices;
					$property_control .= $after_control_html;
				} elseif (strtoupper($control_type) == "RADIOBUTTON" || strtoupper($control_type) == "CHECKBOXLIST") {
					$is_multiple = (strtoupper($control_type) != "RADIOBUTTON");
					if (strtoupper($control_type) == "RADIOBUTTON") {
						$input_type = "radio"; $is_multiple = false;
					} else if (strtoupper($control_type) == "CHECKBOXLIST") {
						$input_type = "checkbox"; $is_multiple = true;
					}
					if ($components_list_style == 2) {
						$property_control .= "<table cell-padding='0px' cell-spacing='0px' ";
					} else {
						$property_control .= "<span";
					}
					if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
					$property_control .= ">";
					if ($components_list_style == 2) {
						$property_control .= "<tr><td>" . ADD_TO_CART_MSG . "</td><td>&nbsp;</td><td>" . PROD_DESCRIPTION_MSG . "</td><td align='center'>" . PRICE_MSG . "</td></tr>";
					}
					
					$value_number = 0;
					$dbp->query($sql);
					while ($dbp->next_record())
					{
						$value_number++;
						$option_price = 0; $option_tax = 0; $option_price_incl = 0; $option_price_excl = 0;
						$sub_quantity = $dbp->f("quantity");
						if ($display_products != 2 || strlen($user_id)) {
							$option_price = $dbp->f($additional_price_field);	
							$percentage_price = $dbp->f("percentage_price");
							if ($percentage_price && $item_price) {
								$option_price += round(($item_price * $percentage_price) / 100, 2);
							}
							$buying_price = $dbp->f("buying_price");	
							if ($property_type_id == 3) {
								$sub_item_id = $dbp->f("sub_item_id");
								$sub_type_id = $dbp->f("item_type_id");
								$sub_tax_free = $dbp->f("tax_free");
								
								$sub_price = $dbp->f($price_field);
								$sub_buying = $dbp->f("buying_price");
								$sub_is_sales = $dbp->f("is_sales");
								$sub_sales = $dbp->f($sales_field);
								$sub_user_price = $dbp->f("user_price");
								$sub_user_action = $dbp->f("user_price_action");
								$prices = get_product_price($sub_item_id, $sub_price, $sub_buying, $sub_is_sales, $sub_sales, $sub_user_price, $sub_user_action, $discount_type, $discount_amount);
								$real_option_price = $prices["base"];
								if (!strlen($option_price)) {
									$option_price = $real_option_price;
								} elseif ($real_option_price && ($real_option_price!=$option_price)) {
									$real_option_tax = set_tax_price($sub_item_id, $sub_type_id, $real_option_price, 0, $sub_tax_free);
									if ($sub_quantity > 1) {
										$real_option_price = $sub_quantity * $real_option_price;
									}							
									if ($tax_prices_type == 1) {
										$real_option_price_incl = $real_option_price;
										$real_option_price_excl = $real_option_price - $real_option_tax;
									} else {
										$real_option_price_incl = $real_option_price + $real_option_tax;
										$real_option_price_excl = $real_option_price;
									}
									if ($tax_prices == 2 || $tax_prices == 3) {
										$real_shown_price = $real_option_price_incl;
									} else {
										$real_shown_price = $real_option_price_excl;
									}
								}
								$option_tax = set_tax_price($sub_item_id, $sub_type_id, $option_price, 0, $sub_tax_free);
								
								if ($sub_quantity > 1) {
									$option_price = $sub_quantity * $option_price;
								}								
								
							} else {
								$option_price = get_option_price($option_price, $buying_price, $properties_percent, $discount_applicable, $discount_type, $discount_amount);
								$option_tax = set_tax_price($item_id, $item_type_id, $option_price, 0, $tax_free);
							}
							if ($tax_prices_type == 1) {
								$option_price_incl = $option_price;
								$option_price_excl = $option_price - $option_tax;
							} else {
								$option_price_incl = $option_price + $option_tax;
								$option_price_excl = $option_price;
							}
						}
						if ($tax_prices == 2 || $tax_prices == 3) {
							$shown_price = $option_price_incl;
						} else {
							$shown_price = $option_price_excl;
						}

						$item_property_id = $dbp->f("item_property_id");
						$item_code = $dbp->f("item_code");
						$manufacturer_code = $dbp->f("manufacturer_code");
						$is_default_value = $dbp->f("is_default_value");
						$override_default = $dbp->f("override_default");
						if (strlen($override_default)) {
							$is_default_value = $override_default; 
						}
						$property_value = get_translation($dbp->f("property_value"));

						$tags_replace = array("{item_code}", "{manufacturer_code}", "{option_value}", "{item_property_id}", "{value_index}",  "{value_number}");
						$tags_values  = array($item_code, $manufacturer_code, $property_value, $item_property_id, ($value_number - 1), $value_number);

						if ($components_list_style == 2) {
							$property_control .= "<tr><td align='center'>";
						}
						
						$property_checked = "";
						$property_control .= $before_control_html;
						$property_control .= "<input type=\"hidden\" name=\"option_price_" . $item_property_id . "\"";
						$property_control .= " value=\"" . $option_price . "\">";
						if ($is_default_value) {
							$property_checked = "checked ";
							$selected_price  += $option_price;
							$options[$property_id]["values"][] = $item_property_id;
						} 
	
						$control_name = ($is_multiple) ? ("property_".$property_id."_".$value_number) : ("property_".$property_id);
						$property_control .= "<input type=\"" . $input_type . "\" id=\"item_property_" . $item_property_id . "\" name=\"" . $control_name . "\" ". $property_checked;
						$property_control .= "value=\"" . htmlspecialchars($item_property_id) . "\" onClick=\"changeProperty(document.form_".$form_id."); ";
						if ($onclick_code) {	$property_control .= $onclick_code; }
						$property_control .= "\"";
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						$property_control .= ">";						
						
						$image       = $dbp->f("big_image");
						$tiny_image  = $dbp->f("tiny_image");
						if ($components_list_style == 2) {
							$property_control .= "</td><td>";

							if ($tiny_image) {
								if (!preg_match("/^([a-zA-Z]*):\/\/(.*)/i", $image)) {
									if ($tiny_watermark || $restrict_products_images) { 
										$tiny_image = $site_url . "image_show.php?item_id=" . $sub_item_id . "&type=tiny&vc=".md5($tiny_image); 
									} else {
										$tiny_image = $root_folder_path . $tiny_image;
									}
								}
								$text = "<img src='" . $tiny_image ."' alt='" . $property_value . "'>";
								if ($image) {
									$property_control .=  product_image_icon($sub_item_id, $property_value, $image, 3, $text);
								} else {
									$property_control .= $text;
								}
							}						
							$property_control .= "</td><td>";							
							
							if ($sub_quantity > 1) {
								$property_value .= " x " . $sub_quantity;
							}
							if ($property_type_id == 3) {
								$friendly_url = $dbp->f("friendly_url");
								if ($friendly_urls && strlen($friendly_url)) {
									$product_details_url = $friendly_url . $friendly_extension;
								} else {
									$product_details_url = $product_link . $sub_item_id;
								}
								$property_value = "<a href='" . $product_details_url . "'>" . $property_value . "</a>";	
								
							}
							$property_control .= $property_value;
							$property_control .= "</td><td align='center'>";
							
							
							if (($property_type_id == 3) && $real_option_price && ($real_option_price != $option_price)) {
								$property_control .= "<nobr><div class='priceBlock'>".PROD_DISCOUNT_PRICE_MSG . "<span class='price'>";
								if ($option_price > 0) {
									$property_control .= $option_positive_price_right . currency_format($shown_price) . $option_positive_price_left;
								} elseif ($option_price < 0) {
									$property_control .= $option_negative_price_right . currency_format(abs($shown_price)) . $option_negative_price_left;
								}
								$property_control .= "</span></div></nobr>";
								$property_control .= "<nobr><div class='priceBlockOld'>" . PROD_PRICE_COLUMN . "<span class='price'>";
								if ($real_option_price > 0) {
									$property_control .= $option_positive_price_right . currency_format($real_shown_price) . $option_positive_price_left;
								} elseif ($real_option_price < 0) {
									$property_control .= $option_negative_price_right . currency_format(abs($real_shown_price)) . $option_negative_price_left;
								}
								$property_control .= "</span></div></nobr>";
								
							} else {
								$property_control .= "<nobr>";
								if ($option_price > 0) {
									$property_control .= $option_positive_price_right . currency_format($shown_price) . $option_positive_price_left;
								} elseif ($option_price < 0) {
									$property_control .= $option_negative_price_right . currency_format(abs($shown_price)) . $option_negative_price_left;
								}
								$property_control .= "</nobr>";
							}
							
							$property_control .= $after_control_html;
							
							$property_control .= "</td></tr>";
						} else {						
							if ($sub_quantity > 1) {
								$property_control .= $sub_quantity . " x ";
							}						
							$property_control .= $property_value;
							if ($image) {
								$property_control .=  product_image_icon($sub_item_id, $property_value, $image, 3);
							}
							if ($option_price > 0) {
								$property_control .= $option_positive_price_right . currency_format($shown_price) . $option_positive_price_left;
							} elseif ($option_price < 0) {
								$property_control .= $option_negative_price_right . currency_format(abs($shown_price)) . $option_negative_price_left;
							}
							$property_control .= $after_control_html;
						}
											
						// added here to have a possibilty to parse different tags like item_property_id for any option in HTML, JavaScript or CSS
						$property_control = str_replace($tags_replace, $tags_values, $property_control); 
					}
					if ($components_list_style == 2) {
						$property_control .= "</table>";
					} else {
						$property_control .= "</span>";
					}
					$property_control .= "<input type=\"hidden\" name=\"property_total_".$property_id."\" value=\"".$value_number."\">";
				} elseif (strtoupper($control_type) == "TEXTBOXLIST") {
					$value_number = 0;
					$dbp->query($sql);
					while ($dbp->next_record())
					{
						$value_number++;
						$option_price = 0; $option_tax = 0; $option_price_incl = 0; $option_price_excl = 0;
						if ($display_products != 2 || strlen($user_id)) {
							$option_price = $dbp->f($additional_price_field);	
							$percentage_price = $dbp->f("percentage_price");
							if ($percentage_price && $item_price) {
								$option_price += round(($item_price * $percentage_price) / 100, 2);
							}
							$buying_price = $dbp->f("buying_price");	
							$option_price = get_option_price($option_price, $buying_price, $properties_percent, $discount_applicable, $discount_type, $discount_amount);
							$option_tax = set_tax_price($item_id, $item_type_id, $option_price, 0, $tax_free);
							if ($tax_prices_type == 1) {
								$option_price_incl = $option_price;
								$option_price_excl = $option_price - $option_tax;
							} else {
								$option_price_incl = $option_price + $option_tax;
								$option_price_excl = $option_price;
							}
						}
						if ($tax_prices == 2 || $tax_prices == 3) {
							$shown_price = $option_price_incl;
						} else {
							$shown_price = $option_price_excl;
						}

						$item_property_id = $dbp->f("item_property_id");
						$item_code = $dbp->f("item_code");
						$manufacturer_code = $dbp->f("manufacturer_code");
						$property_value = get_translation($dbp->f("property_value"));

						$tags_replace = array("{item_code}", "{manufacturer_code}", "{option_value}", "{item_property_id}", "{value_index}",  "{value_number}");
						$tags_values  = array($item_code, $manufacturer_code, $property_value, $item_property_id, ($value_number - 1), $value_number);

						$property_checked = "";
						$property_control .= "<input type=\"hidden\" name=\"option_price_" . $item_property_id . "\"";
						$property_control .= " value=\"" . $option_price . "\">";
						$value_control_name = "property_value_".$property_id."_".$value_number;
						$property_control .= "<input type=\"hidden\" value=\"".$item_property_id."\" name=\"".$value_control_name."\">";

						$property_control .= $before_control_html;
						$property_control .= $property_value . ": ";
						$control_name = "property_".$property_id."_".$value_number;
						$property_control .= "<input type=\"text\" value=\"\" id=\"item_property_" . $item_property_id . "\" name=\"" . $control_name . "\" ";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						$property_control .= " onChange=\"changeProperty(document.form_".$form_id.");";
						if ($onchange_code) {	
							$property_control .= $onchange_code; 
						}
						$property_control .= "\"";
						if ($onclick_code) {	
							$property_control .= " onClick=\"" . $onclick_code . "\"";
						}
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						if (($max_limit_type == 2 || $max_limit_type == 4) && $max_limit_length) {
							$property_control .= " onKeyPress=\"return checkMaxLength(event, this, " . $max_limit_length . ", " . $max_limit_type . ");\"";
						} else if (($max_limit_type == 1 || $max_limit_type == 3) && $max_limit_length) {
							$property_control .= " onKeyPress=\"return checkBoxesMaxLength(event, this, document.form_".$form_id.", ".$property_id.", ".$max_limit_length.",".$max_limit_type.");\"";
						}
						if ($property_price && $property_price_type) {
							$property_control .= " onKeyUp=\"changeProperty(document.form_".$form_id.");\"";
						}

						$property_control .= ">";
						if ($option_price > 0) {
							$property_control .= $option_positive_price_right . currency_format($shown_price) . $option_positive_price_left;
						} elseif ($option_price < 0) {
							$property_control .= $option_negative_price_right . currency_format(abs($shown_price)) . $option_negative_price_left;
						}
						$property_control .= $after_control_html;
						// added here to have a possibilty to parse different tags like item_property_id for any option in HTML, JavaScript or CSS
						$property_control = str_replace($tags_replace, $tags_values, $property_control); 
					}
					$property_control .= "<input type=\"hidden\" name=\"property_total_".$property_id."\" value=\"".$value_number."\">";

				} elseif (strtoupper($control_type) == "TEXTBOX" || strtoupper($control_type) == "IMAGEUPLOAD") {
					$property_control .= $before_control_html;
					$property_control .= "<input type=\"text\" name=\"property_" . $property_id . "\"";
					if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
					if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
					$property_control .= " onChange=\"changeProperty(document.form_".$form_id.");";
					if ($onchange_code) {	
						$property_control .= $onchange_code; 
					}
					$property_control .= "\"";
					if ($control_code) {	$property_control .= " " . $control_code . " "; }
					if ($max_limit_type && $max_limit_length) {
						$property_control .= " onKeyPress=\"return checkMaxLength(event, this, " . $max_limit_length . ", " . $max_limit_type . ");\"";
					}
					if ($property_price && $property_price_type) {
						$property_control .= " onKeyUp=\"changeProperty(document.form_".$form_id.");\"";
					}

					$property_control .= " value=\"".htmlspecialchars(get_translation($property_description))."\">";
					$property_control .= $after_control_html;
					if (strtoupper($control_type) == "IMAGEUPLOAD") {
						$upload_url = "user_upload.php?filetype=option_image&fid=" . $item_id . "&control_name=property_" . $property_id;
						$property_control .= " <a href=\"javascript:properyImageUpload('" . $upload_url . "')\">" . UPLOAD_IMAGE_MSG . "</a>";
					}
				} elseif (strtoupper($control_type) == "TEXTAREA") {
					$property_control .= $before_control_html;
					$property_control .= "<textarea name=\"property_" . $property_id . "\"";
					if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
					if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
					$property_control .= " onChange=\"changeProperty(document.form_".$form_id.");";
					if ($onchange_code) {	
						$property_control .= $onchange_code; 
					}
					$property_control .= "\"";
					if ($control_code) {	$property_control .= " " . $control_code . " "; }
					if ($max_limit_type && $max_limit_length) {
						$property_control .= " onKeyPress=\"return checkMaxLength(event, this, " . $max_limit_length . ", " . $max_limit_type . ");\"";
					}
					if ($property_price && $property_price_type) {
						$property_control .= " onKeyUp=\"changeProperty(document.form_".$form_id.");\"";
					}

					$property_control .= ">".htmlspecialchars(get_translation($property_description))."</textarea>";
					$property_control .= $after_control_html;
				} else {
					$property_control .= $before_control_html;
					if ($property_required) {
						$property_control .= "<input type=\"hidden\" name=\"property_" . $property_id . "\" value=\"" . htmlspecialchars($property_description) . "\">";
					}
					$property_control .= "<span";
					if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
					if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
					if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
					if ($control_code) {	$property_control .= " " . $control_code . " "; }
					$property_control .= ">" . get_translation($property_description) . "</span>";
					$property_control .= $after_control_html;
				}

				$t->set_var("property_id", $property_id);
				$t->set_var("property_block_id", $property_block_id);
				$t->set_var("property_name", $start_html . $property_name);
				$t->set_var("property_style", $property_style);
				$t->set_var("property_control", $middle_html . $property_control . $end_html);

				$t->parse("properties", true);
			} 
		}

		$properties["is_any"] = $is_properties;
		$properties["ids"] = $properties_ids;
		$properties["price"] = $selected_price;
		$properties["components_price"] = $components_price;
		$properties["components_tax_price"] = $components_tax_price;
		$properties["components_points_price"] = $components_points_price;
		$properties["components_reward_points"] = $components_reward_points;
		$properties["components_reward_credits"] = $components_reward_credits;

		$product_params["comp_price"] = $components_price;
		$product_params["comp_tax"] = $components_tax_price;
		$product_params["properties_ids"] = $properties_ids;

		return $properties;
	}

	function calculate_subcomponents_price($item_id, $item_type_id, &$components_price, &$components_tax_price)
	{
	 	global $t, $db, $table_prefix;
		global $settings, $currency;

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

		// connection for subcomponents 
		$dbp = new VA_SQL();
		$dbp->DBType     = $db->DBType;
		$dbp->DBDatabase = $db->DBDatabase;
		$dbp->DBUser     = $db->DBUser;
		$dbp->DBPassword = $db->DBPassword;
		$dbp->DBHost     = $db->DBHost;
		$dbp->DBPort       = $db->DBPort;
		$dbp->DBPersistent = $db->DBPersistent;

		$components = array(); $components_price = 0; $components_tax_price = 0;
		$sql  = " SELECT * ";
		$sql .= " FROM " . $table_prefix . "items_properties ";
		$sql .= " WHERE (item_id=" . $db->tosql($item_id, INTEGER) . " OR item_type_id=" . $db->tosql($item_type_id, INTEGER) . ")";
		$sql .= " AND property_type_id=2 ";
		$sql .= " ORDER BY property_order, property_id ";
		$dbp->query($sql);
		while ($dbp->next_record()) {
			$sub_item_id = $dbp->f("sub_item_id");
			$sub_quantity = $dbp->f("quantity");
			$sub_price = $dbp->f($additional_price_field);
			$components[] = array("item_id" => $sub_item_id, "quantity" => $sub_quantity, "price" => $sub_price);
		}

		// calculate subcomponents price
		if (sizeof($components) > 0) {

			for ($i = 0; $i < sizeof($components); $i++) {
				// get subcomponent information
				$component_info = $components[$i]; 
				$sub_item_id = $component_info["item_id"];
				$sub_quantity = $component_info["quantity"];
				$component_price = $component_info["price"];
				// get original information for component product
				$price = 0; $buying_price = 0; $points_price = 0; $reward_points = 0; $reward_credits = 0;
				$sql  = " SELECT i.item_type_id, i.buying_price, i." . $price_field . ", i.is_sales, i." . $sales_field . ", i.tax_free, ";
				$sql .= " i.is_points_price, i.points_price, i.reward_type, i.reward_amount, i.credit_reward_type, i.credit_reward_amount ";
				$sql .= " FROM " . $table_prefix . "items i ";
				$sql .= " WHERE i.item_id=" . $db->tosql($sub_item_id, INTEGER);
				$dbp->query($sql);
				if ($dbp->next_record()) {
					$sub_type_id = $dbp->f("item_type_id");
					$sub_tax_free = $dbp->f("tax_free");
					$sub_quantity = $dbp->f("quantity");
					if ($sub_quantity < 1) { $sub_quantity = 1; }
					if (strlen($component_price)) {
						$price = $component_price;
					} else {
						$price = $dbp->f($price_field);
						$buying_price = $dbp->f("buying_price");
						$is_sales = $dbp->f("is_sales");
						$sales_price = $dbp->f($sales_field);
						
						$user_price  = false; 
						$user_price_action = 0;
						$q_prices    = get_quantity_price($sub_item_id, 1);
						if ($q_prices) {
							$user_price  = $q_prices [0];
							$user_price_action = $q_prices [2];
						}
				
						if ($user_price > 0 && ($user_price_action > 0 || !$discount_type)) {
							$price = $user_price;
						} elseif ($is_sales) {
							$price = $sales_price; 
						}
						if ($user_price_action != 1) {
							if ($discount_type == 1 || $discount_type == 3) {
								$price -= round(($price * $discount_amount) / 100, 2);
							} elseif ($discount_type == 2) {
								$price -= round($discount_amount, 2);
							} elseif ($discount_type == 4) {
								$price -= round((($price - $buying_price) * $discount_amount) / 100, 2);
							}
						}
					}
				}

				$components_price += ($price * $sub_quantity);
				$tax_amount = set_tax_price($sub_item_id, $sub_type_id, $price, 0, $sub_tax_free);
				$components_tax_price += ($tax_amount * $sub_quantity);
			}
		}


	}

?>