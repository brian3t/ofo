<?php                           

function cart_save($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_cart_save.html");

	$current_page = "cart_save.php";

	$shopping_cart = get_session("shopping_cart");
	$total_items = 0;
	if(is_array($shopping_cart)) {
		// check for active products in the cart
		foreach($shopping_cart as $cart_id => $item) {
			$item_id = $item["ITEM_ID"];
			$properties_more = isset($item["PROPERTIES_MORE"]) ? $item["PROPERTIES_MORE"] : false;
			if (!$properties_more) {  
				$total_items++;
			}
		}
	}
	if (!$total_items) {
		$rp = get_param("rp");
		$basket_page = strlen($rp) ? get_custom_friendly_url("basket.php") . "?rp=" . urlencode($rp) : get_custom_friendly_url("basket.php");
		header("Location: " . $basket_page);
		exit;
	}

	$t->set_var("basket_href",   get_custom_friendly_url("basket.php"));
	$t->set_var("current_href",  get_custom_friendly_url("basket.php"));
	$t->set_var("checkout_href", get_custom_friendly_url("checkout.php"));
	$t->set_var("products_href", get_custom_friendly_url("products.php"));
	$t->set_var("cart_save_href",get_custom_friendly_url("cart_save.php"));

	srand ((double) microtime() * 1000000);
	$new_random_value = rand();

	$discount_type = get_session("session_discount_type");
	$discount_amount = get_session("session_discount_amount");

	// set up return page
	$rp = get_param("rp");
	if(!$rp) { $rp = get_custom_friendly_url("products.php"); }
	$t->set_var("rp", htmlspecialchars($rp));

	$operation = get_param("operation");
	$rnd = get_param("rnd");
	$session_rnd = get_session("session_rnd");

	$r = new VA_Record($table_prefix . "saved_carts");
	$r->add_where("cart_id", INTEGER);
	$r->add_textbox("site_id", INTEGER);
	$r->add_textbox("user_id", INTEGER);
	$r->add_textbox("cart_name", TEXT, CART_NAME_FIELD);
	$r->change_property("cart_name", REQUIRED, true);
	$r->add_textbox("cart_total", NUMBER);
	$r->add_textbox("cart_added", DATETIME);

	$si = new VA_Record($table_prefix . "saved_items");
	$si->add_where("cart_item_id", INTEGER);
	$si->add_textbox("site_id", INTEGER);
	$si->add_textbox("item_id", INTEGER);
	$si->add_textbox("cart_id", INTEGER);
	$si->add_textbox("user_id", INTEGER);
	$si->add_textbox("item_name", TEXT);
	$si->add_textbox("quantity", INTEGER);
	$si->add_textbox("price", NUMBER);
	$si->add_textbox("date_added", DATETIME);

	$sip = new VA_Record($table_prefix . "saved_items_properties");
	$sip->add_where("item_property_id", INTEGER);
	$sip->add_textbox("cart_item_id", INTEGER);
	$sip->add_textbox("cart_id", INTEGER);
	$sip->add_textbox("property_id", INTEGER);
	$sip->add_textbox("property_value", TEXT);
	$sip->add_textbox("property_values_ids", TEXT);

	$rnd = get_param("rnd");
	$session_rnd = get_session("session_rnd");
	$session_cart_id = get_session("session_cart_id");
	$success_message = "";
	$cart_id = "";
	if(strlen($operation) && $rnd != $session_rnd) 
	{
		if ($operation == "cancel") {
			header("Location: " . get_custom_friendly_url("basket.php") . "?rp=" . urlencode($rp));
			exit;
		} 
		$r->get_form_values();

		$is_valid = $r->validate();

		if ($is_valid) {
			set_session("session_rnd", $rnd);
			// prepare total products quantities
			if (!isset($site_id) || !$site_id) {
				$site_id = 1;
			}
			$cart_items = array(); $cart_total = 0;
			if(is_array($shopping_cart))
			{
				foreach ($shopping_cart as $cart_id => $item) {

					$item_id = $item["ITEM_ID"];
					$properties_more = $item["PROPERTIES_MORE"];
					if (!$item_id && $properties_more > 0) { continue; }
			  
					$item_type_id = $item["ITEM_TYPE_ID"];
					$item_name = $item["ITEM_NAME"];
					$properties = $item["PROPERTIES"];
					$quantity = $item["QUANTITY"];
					$tax_free = isset($item["TAX_FREE"]) ? $item["TAX_FREE"] : 0;
					$discount_applicable = isset($item["DISCOUNT"]) ? $item["DISCOUNT"] : 0;
					$buying_price = isset($item["BUYING_PRICE"]) ? $item["BUYING_PRICE"] : 0;
					$price = $item["PRICE"];
					$is_price_edit = isset($item["PRICE_EDIT"]) ? $item["PRICE_EDIT"] : 0;
					$properties_price = $item["PROPERTIES_PRICE"];
					$properties_percentage = $item["PROPERTIES_PERCENTAGE"];
					$properties_buying = $item["PROPERTIES_BUYING"];
					$properties_discount = $item["PROPERTIES_DISCOUNT"];
					$components = $item["COMPONENTS"];
					if ($discount_applicable) {
						if (!$is_price_edit) {
							if ($discount_type == 1) {
								$price -= round(($price * $discount_amount) / 100, 2);
							} else if ($discount_type == 2) {
								$price -= round($discount_amount, 2);
							} else if ($discount_type == 3) {
								$price -= round(($price * $discount_amount) / 100, 2);
							} else if ($discount_type == 4) {
								$price -= round((($price - $buying_price) * $discount_amount) / 100, 2);
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
						if ($discount_type == 1) {
							$properties_price -= round((($properties_price) * $discount_amount) / 100, 2);
						} else if ($discount_type == 4) {
							$properties_price -= round((($properties_price - $properties_buying) * $discount_amount) / 100, 2);
						}
					}
		    
					$price += $properties_price;
			  
					// add components prices
					if (is_array($components) && sizeof($components) > 0) {
						foreach ($components as $property_id => $component_values) {
							foreach ($component_values as $property_item_id => $component) {
								$component_price = $component["price"];
								$sub_item_id = $component["sub_item_id"];
								$sub_type_id = $component["item_type_id"];
								if (!strlen($component_price)) {
									$sub_price = $component["base_price"];
									$sub_buying = $component["buying"];
									$sub_user_price = $component["user_price"];
									$sub_user_action = $component["user_price_action"];
									$sub_prices = get_product_price($sub_item_id, $sub_price, $sub_buying, 0, 0, $sub_user_price, $sub_user_action, $discount_type, $discount_amount);
									$component_price = $sub_prices["base"];
								}
		    
								$price += $component_price;
							}
						}
					}
					$cart_total += ($price * $quantity);

					$cart_items[] = array($item_id, $item_name, $quantity, $price, $properties);
				}
			}

			if ($db_type == "postgre") {
				$cart_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "saved_carts') ");
				$r->change_property("cart_id", USE_IN_INSERT, true);
				$r->set_value("cart_id", $cart_id);
			}
			$r->set_value("site_id", $site_id);
			$r->set_value("user_id", intval(get_session("session_user_id")));
			$r->set_value("cart_added", va_time());
			$r->set_value("cart_total", $cart_total);
			if ($r->insert_record()) {
				if ($db_type == "mysql") {
					$cart_id = get_db_value(" SELECT LAST_INSERT_ID() ");
				} else if ($db_type == "access") {
					$cart_id = get_db_value(" SELECT @@IDENTITY ");
				} else if ($db_type == "db2") {
					$cart_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "saved_carts FROM " . $table_prefix . "saved_carts");
				}
				set_session("session_cart_id", $cart_id);

				// save cart items
				$si->set_value("site_id", $site_id);
				$si->set_value("cart_id", $cart_id);
				$si->set_value("cart_id", $cart_id);
				$si->set_value("date_added", va_time());
				$si->set_value("user_id", intval(get_session("session_user_id")));

				for ($ci = 0; $ci < sizeof($cart_items); $ci++) {
					list ($item_id, $item_name, $quantity, $price, $properties) = $cart_items[$ci];
					if ($db_type == "postgre") {
						$cart_item_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "saved_items') ");
						$si->change_property("cart_item_id", USE_IN_INSERT, true);
						$si->set_value("cart_item_id", $cart_item_id);
					}
					$si->set_value("item_id", $item_id);
					$si->set_value("item_name", $item_name);
					$si->set_value("quantity", $quantity);
					$si->set_value("price", $price);
					if ($si->insert_record()) {
						// save properties
						if ($db_type == "mysql") {
							$cart_item_id = get_db_value(" SELECT LAST_INSERT_ID() ");
						} else if ($db_type == "access") {
							$cart_item_id = get_db_value(" SELECT @@IDENTITY ");
						} else if ($db_type == "db2") {
							$cart_item_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "saved_items FROM " . $table_prefix . "saved_items");
						}

						if (is_array($properties)) {
							$sip->set_value("cart_item_id", $cart_item_id);
							$sip->set_value("cart_id", $cart_id);
							foreach($properties as $property_id => $property_values) {
								$sip->set_value("property_id", $property_id);
								$sql  = " SELECT control_type ";
								$sql .= " FROM " . $table_prefix . "items_properties ";
								$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER);
								$db->query($sql);
								if ($db->next_record()) {
									$control_type = $db->f("control_type");
									if (strtoupper($control_type) == "RADIOBUTTON" 
										|| strtoupper($control_type) == "CHECKBOXLIST" 
										|| strtoupper($control_type) == "LISTBOX") {
										$sip->set_value("property_value", "");
										$sip->set_value("property_values_ids", implode(",", $property_values));
									} else {
										$sip->set_value("property_value", $property_values[0]);
										$sip->set_value("property_values_ids", "");
									}
									$sip->insert_record();

								}
							}
						}
						// end save properties
					}
				}
				// end save cart items
			}
		}
	} else if ($session_cart_id) {
		$cart_id = $session_cart_id;
	}

	$r->set_parameters();

	if ($cart_id) {
		$success_message = str_replace("{cart_id}", $cart_id, CART_SAVED_MSG);
		$r->set_value("cart_name", "");
		$t->set_var("success_message", $success_message);
		$t->parse("success_block", false);
	}

	$t->set_var("rp", htmlspecialchars($rp));
	$t->set_var("random_value", htmlspecialchars($new_random_value));

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>