<?php

include_once("./includes/shopping_cart.php");
include_once("./messages/" . $language_code . "/cart_messages.php");

function small_cart($block_name = "", $block_prefix = "")
{
	global $t, $db, $table_prefix;
	global $category_id;
	global $page_settings, $settings;

	// ** EGGHEAD VENTURES ADD
	if($_SESSION["make"]) {
		$t->set_var("ff_search_results", "<div align=\"center\" id=\"cart-search\">Search Results For: <br /><a href=\"/\">".$_SESSION["year"]." ".$_SESSION["make"]." ".$_SESSION["model"]."</a></div>");
	}
	// **
		
	if ($block_name) {
		if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
			return;
		}
		$t->set_file("block_body", "block_cart.html");
	}

	$tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
	$tax_prices = get_setting_value($settings, "tax_prices", 0);

	// check if there are any coupon with order tax free option
	$order_tax_free = false;
	$coupons = get_session("session_coupons");
	if (is_array($coupons)) {
		foreach ($coupons as $coupon_id => $coupon_info) {
			$coupon_order_tax_free = $coupon_info["ORDER_TAX_FREE"];
			if ($coupon_order_tax_free) {
				$order_tax_free = true;
				break;
			}
		}
	}

	$tax_rates     = get_session("session_tax_rates");
	$shopping_cart = get_session("shopping_cart");
	$total_quantity = 0; $total_price = 0; $goods_excl_tax = 0; $goods_incl_tax = 0;
	if(is_array($shopping_cart) && sizeof($shopping_cart) > 0) {
		
		$t->set_var($block_prefix ."empty_small_cart", "");
		$t->set_var("small_cart_items", "");

		$user_info = get_session("session_user_info");
		$user_tax_free = get_setting_value($user_info, "tax_free", 0);
		$discount_type = get_session("session_discount_type");
		$discount_amount = get_session("session_discount_amount");

		foreach($shopping_cart as $cart_id => $item)
		{
			if (!$item || !(isset($item["ITEM_ID"]))) {
				continue;
			}
			$item_id = $item["ITEM_ID"];
			
			$properties_more = $item["PROPERTIES_MORE"];
			if ($properties_more > 0) { continue; }

			$item_type_id = $item["ITEM_TYPE_ID"];
			$item_name = get_translation($item["ITEM_NAME"]);
			if (strlen($item_name) < 20) {
				$short_name = $item_name;
			} else if (preg_match("/^.{10}[^\s\&\+\-\_\.\(,]{0,8}/", $item_name, $matches)) {
				$short_name = $matches[0];
			} else {
				$short_name = substr($item_name, 0, 18);
			}
			$properties = $item["PROPERTIES"];
			$quantity = $item["QUANTITY"];
			$tax_free = $item["TAX_FREE"];
			if ($user_tax_free || $order_tax_free) { $tax_free = true; }
			$discount_applicable = $item["DISCOUNT"];
			$buying_price = $item["BUYING_PRICE"];
			$price = $item["PRICE"];
			$is_price_edit = $item["PRICE_EDIT"];
			$properties_price = $item["PROPERTIES_PRICE"];
			$properties_percentage = $item["PROPERTIES_PERCENTAGE"];
			$properties_buying = $item["PROPERTIES_BUYING"];
			$properties_discount = $item["PROPERTIES_DISCOUNT"];
			$components = $item["COMPONENTS"];
			//$discount_total = 0;
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

			// check the tax for basic price
			$tax_amount = get_tax_amount($tax_rates, $item_type_id, $price, $tax_free, $tax_percent);

			if ($tax_prices_type == 1) {
				$price_excl_tax = $price - $tax_amount;
				$price_incl_tax = $price;
			} else {
				$price_excl_tax = $price;
				$price_incl_tax = $price + $tax_amount;
			}

			// total goods values
			$goods_excl_tax += ($price_excl_tax * $quantity); 
			$goods_incl_tax += ($price_incl_tax * $quantity);			

			// add components prices
			if (is_array($components) && sizeof($components) > 0) {
				foreach ($components as $property_id => $component_values) {
					foreach ($component_values as $property_item_id => $component) {
						$component_price = $component["price"];
						$component_tax_free = $component["tax_free"];
						if ($user_tax_free) { $component_tax_free = $user_tax_free; }
						$sub_item_id = $component["sub_item_id"];
						$sub_quantity = $component["quantity"];
						$sub_qty_action = isset($component["quantity_action"]) ? $component["quantity_action"] : 1;
						if ($sub_quantity < 1)  { $sub_quantity = 1; }
						$sub_type_id = $component["item_type_id"];
						if (!strlen($component_price)) {
							$sub_price = $component["base_price"];
							$sub_buying = $component["buying"];
							$sub_user_price = $component["user_price"];
							$sub_user_action = $component["user_price_action"];
							$sub_prices = get_product_price($sub_item_id, $sub_price, $sub_buying, 0, 0, $sub_user_price, $sub_user_action, $discount_type, $discount_amount);
							$component_price = $sub_prices["base"];
						}
						// check the price including the tax
						$component_tax_amount = get_tax_amount($tax_rates, $sub_type_id, $component_price, $component_tax_free, $component_tax_percent); 
						if ($tax_prices_type == 1) {
							$component_price_excl_tax = $component_price - $component_tax_amount;
							$component_price_incl_tax = $component_price;
						} else {
							$component_price_excl_tax = $component_price;
							$component_price_incl_tax = $component_price + $component_tax_amount;
						}

						if ($sub_qty_action == 2) {
							$goods_excl_tax += ($component_price_excl_tax * $sub_quantity); 
							$goods_incl_tax += ($component_price_incl_tax * $sub_quantity);
							$price_excl_tax += ($component_price_excl_tax * $sub_quantity / $quantity); 
							$price_incl_tax += ($component_price_incl_tax * $sub_quantity / $quantity);
						} else {
							$goods_excl_tax += ($component_price_excl_tax * $sub_quantity * $quantity); 
							$goods_incl_tax += ($component_price_incl_tax * $sub_quantity * $quantity);
							$price_excl_tax += ($component_price_excl_tax * $sub_quantity); 
							$price_incl_tax += ($component_price_incl_tax * $sub_quantity);
						}
					}
				}
			}

			if ($tax_prices > 0) {
				$price = $price_incl_tax;
				$total_price += $goods_incl_tax;
			} else {
				$price = $price_excl_tax;
				$total_price += $goods_excl_tax;
			}

			if (isset($item["COUPONS"]) && is_array($item["COUPONS"])) {
				foreach ($item["COUPONS"] as $coupon_id => $coupon_info) {
					$price -= $coupon_info["DISCOUNT_AMOUNT"];
					$total_price -= ($coupon_info["DISCOUNT_AMOUNT"] * $quantity);
				}
			}

			$total_quantity += $quantity;
			//$total_price += ($quantity * $price);

			$t->set_var("short_name", $short_name);
			$t->set_var("quantity", $quantity);
			$t->set_var("price", currency_format($price));

			$t->sparse($block_prefix . "small_cart_items", true);
		}
	}

	if ($total_quantity > 0) {

		$t->set_var("total_quantity", $total_quantity);
		$t->set_var("total_price", currency_format($total_price));

		$t->set_var("checkout_href", get_custom_friendly_url("checkout.php"));
		$t->set_var("basket_href", get_custom_friendly_url("basket.php"));
		
		
		$t->parse($block_prefix . "small_cart", false);
		
	} else {
		$t->set_var("total_quantity", 0);
		$t->set_var("total_price", currency_format(0));

		$t->set_var("EMPTY_CART_MSG", EMPTY_CART_MSG);
		$t->sparse($block_prefix . "empty_small_cart", false);
		$t->set_var($block_prefix . "small_cart", "");
	}

	if ($block_name) {
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>