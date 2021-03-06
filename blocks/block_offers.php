<?php

include_once("./includes/products_functions.php");
include_once("./includes/shopping_cart.php");
include_once("./includes/navigator.php");
include_once("./messages/" . $language_code . "/cart_messages.php");

function offers($block_name, $page_friendly_url = "", $page_friendly_params = array())
{
	/*global $t, $db, $db_type, $table_prefix;
	global $settings, $page_settings;
	global $language_code, $current_page;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$shopping_cart = get_session("shopping_cart");
	$user_info = get_session("session_user_info");
	$user_tax_free = get_setting_value($user_info, "tax_free", 0);
	$discount_type = get_session("session_discount_type");
	$discount_amount = get_session("session_discount_amount");
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$display_products = get_setting_value($settings, "display_products", 0);
	$product_no_image = get_setting_value($settings, "product_no_image", "");
	$watermark = get_setting_value($settings, "watermark_small_image", 0);

	$prod_offers_add_button = get_setting_value($page_settings, "prod_offers_add_button", "");
	$prod_offers_view_button = get_setting_value($page_settings, "prod_offers_view_button", 0);
	$prod_offers_goto_button = get_setting_value($page_settings, "prod_offers_goto_button", 0);
	$prod_offers_wish_button = get_setting_value($page_settings, "prod_offers_wish_button", 0);
	$quantity_control = get_setting_value($page_settings, "prod_offers_quantity_control", "");

	$prod_offers_points_price = get_setting_value($page_settings, "prod_offers_points_price", 0);
	$prod_offers_reward_points = get_setting_value($page_settings, "prod_offers_reward_points", 0);
	$prod_offers_reward_credits = get_setting_value($page_settings, "prod_offers_reward_credits", 0);

	$current_ts = va_timestamp();

	// global points settings
	$points_system = get_setting_value($settings, "points_system", 0);
	$points_conversion_rate = get_setting_value($settings, "points_conversion_rate", 1);
	$points_decimals = get_setting_value($settings, "points_decimals", 0);
	$points_prices = get_setting_value($settings, "points_prices", 0);

	// global credit settings
	$credit_system = get_setting_value($settings, "credit_system", 0);
	$reward_credits_users = get_setting_value($settings, "reward_credits_users", 0);

	$image_type_name = "small";
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	$user_id = get_session("session_user_id");
	$price_type = get_session("session_price_type");
	if ($price_type == 1) {
		$price_field = "trade_price";
		$sales_field = "trade_sales";
		$properties_field = "trade_properties_price";
	} else {
		$price_field = "price";
		$sales_field = "sales_price";
		$properties_field = "properties_price";
	}
	
	// new product settings	
	$new_product_enable = get_setting_value($settings, "new_product_enable", 0);	
	$new_product_order  = get_setting_value($settings, "new_product_order", 0);	
	$new_product_field = "";
	if ($new_product_enable) {
		if ($new_product_order == 0) {
			$new_product_field = "issue_date";
		} elseif ($new_product_order == 1) {
			$new_product_field = "date_added";
		} elseif ($new_product_order == 2) {
			$new_product_field = "date_modified";
		}
	}

	if ($friendly_urls && $page_friendly_url) {
		$pass_parameters = get_transfer_params($page_friendly_params);
		$current_page = $page_friendly_url . $friendly_extension;
	} else {
		$pass_parameters = get_transfer_params();
	}

	srand((double) microtime() * 1000000);
	$rnd = rand();

	$query_string = get_query_string($pass_parameters, "", "", true);
	$rp = $current_page . $query_string;
	$cart_link  = $rp;
	$cart_link .= strlen($query_string) ? "&" : "?";
	$cart_link .= "rnd=" . $rnd . "&";

	$t->set_file("block_body", "block_offers.html");
	$t->set_var("items_cols",  "");
	$t->set_var("items_rows",  "");
	$t->set_var("product_details_href", "product_details.php");
	$t->set_var("basket_href",   get_custom_friendly_url("basket.php"));
	$t->set_var("checkout_href", get_custom_friendly_url("checkout.php"));
	$t->set_var("rp_url", urlencode($rp));
	$t->set_var("rnd", $rnd);

	$items_ids = VA_Products::find_all_ids("i.is_special_offer=1", VIEW_CATEGORIES_ITEMS_PERM);
	if (!$items_ids) return;
	$allowed_items_ids = VA_Products::find_all_ids("i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);
	$total_records = count($items_ids);

	$records_per_page = get_setting_value($page_settings, "prod_offers_recs", 10);
	$pages_number = 5;
	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $current_page);
	$page_number = $n->set_navigator("navigator", "offers_page", SIMPLE, $pages_number, $records_per_page, $total_records, false, $pass_parameters);

	$php_in_hot_desc = get_setting_value($settings, "php_in_products_hot_desc", 0);

	$so_columns = get_setting_value($page_settings, "prod_offers_cols", 1);
	$t->set_var("offer_column", (100 / $so_columns) . "%");
	$t->set_var("so_columns", $so_columns);
	$so_number = 0;

	$sql  = " SELECT i.item_id, i.item_type_id, i.item_name, i.friendly_url, i.short_description, i.features, ";
	$sql .= " i.buying_price, i." . $price_field . ", i.".$properties_field.", i." . $sales_field . ", i.is_sales, i.tax_free, ";
	$sql .= " i.manufacturer_code, m.manufacturer_name, m.affiliate_code, ";
	$sql .= " i.is_points_price, i.points_price, i.reward_type, i.reward_amount, i.credit_reward_type, i.credit_reward_amount, ";
	$sql .= " i.issue_date, i.stock_level, i.use_stock_level, i.disable_out_of_stock, i.hide_out_of_stock, i.hide_add_list, ";
	if ($new_product_field) {
		$sql .= "i." . $new_product_field . ",";
	}
	$sql .= " i.small_image, i.small_image_alt, i.special_offer ";
	$sql .= " FROM (" . $table_prefix . "items i ";
 	$sql .= " LEFT JOIN " . $table_prefix . "manufacturers m ON i.manufacturer_id=m.manufacturer_id) ";
	$sql .= " WHERE i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
	$sql .= " ORDER BY i.item_order, i.item_id ";
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql);
	if ($db->next_record()) {
		do 
		{
			$so_number++;
			$item_id = $db->f("item_id");
			$form_id = "so_" . $item_id;
			$item_type_id = $db->f("item_type_id");
			$item_name = get_translation($db->f("item_name"));
			$friendly_url = $db->f("friendly_url");
			$special_offer = get_translation($db->f("special_offer"));
			if ($php_in_hot_desc) {
				eval_php_code($special_offer);
			}
			$short_description = get_translation($db->f("short_description"));
			$highlights = get_translation($db->f("features"));
			$small_image = $db->f("small_image");
			$small_image_alt = get_translation($db->f("small_image_alt"));
			$buy_link = $db->f("buy_link");
			$affiliate_code = $db->f("affiliate_code");
			$manufacturer_code = $db->f("manufacturer_code");
			$manufacturer_name = $db->f("manufacturer_name");
			$issue_date_ts = 0;
			$issue_date = $db->f("issue_date", DATETIME);
			if (is_array($issue_date)) {
				$issue_date_ts = va_timestamp($issue_date);
			}
			$stock_level = $db->f("stock_level");
			$use_stock_level = $db->f("use_stock_level");
			$disable_out_of_stock = $db->f("disable_out_of_stock");
			$hide_out_of_stock = $db->f("hide_out_of_stock");
			$hide_add_list = $db->f("hide_add_list");

	  		$min_quantity = $db->f("min_quantity");
			$max_quantity = $db->f("max_quantity");
			$quantity_increment = $db->f("quantity_increment");
			$quantity_limit = ($use_stock_level && ($disable_out_of_stock || $hide_out_of_stock));

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
	  
			if ($friendly_urls && $friendly_url) {
				$details_url = $friendly_url . $friendly_extension;
			} else {
				$details_url = "product_details.php?item_id=".urlencode($item_id);
			}
				
			if ($new_product_enable) {
				$new_product_date = $db->f($new_product_field);			
				$is_new_product   = is_new_product($new_product_date);
			} else {
				$is_new_product = false;
			}
			if ($is_new_product) {
				$t->set_var("product_new_class", " newProduct");
				$t->sparse("product_new_image", false);			
			} else {
				$t->set_var("product_new_class", "");
				$t->set_var("product_new_image", "");
			}
			
			if (!$allowed_items_ids || !in_array($item_id, $allowed_items_ids)) {
				$t->set_var("restricted_class", " restrictedItem");
				$t->sparse("restricted_image", false);
				$prod_offers_add_button = false;
			} else {
				$t->set_var("restricted_class", "");
				$t->set_var("restricted_image", "");
			}
	  
			$t->set_var("item_id", $item_id);
			$t->set_var("form_id", $form_id);
			$t->set_var("item_name", $item_name);
			$t->set_var("details_url", $details_url);
			$t->set_var("special_offer", $special_offer);
			$t->set_var("short_description", $short_description);
			$t->set_var("highlights", $highlights);
			$t->set_var("sp_tax_price", "");
			$t->set_var("sp_tax_sales", "");
	  
			if ($display_products != 2 || strlen($user_id)) {
				$price = $db->f($price_field);
				$sales_price = $db->f($sales_field);
				$is_sales = $db->f("is_sales");
				$buying_price = $db->f("buying_price");
				$tax_free = $db->f("tax_free");
				if ($user_tax_free) { $tax_free = $user_tax_free; }
					
				$user_price  = false; 
				$user_price_action = 0;
				$q_prices    = get_quantity_price($item_id, 1);
				if ($q_prices) {
					$user_price  = $q_prices [0];
					$user_price_action = $q_prices [2];
				}
					
				$properties_price = $db->f($properties_field);
				if ($user_price > 0 && ($user_price_action > 0 || !$discount_type)) {
					if ($is_sales) {
						$sales_price = $user_price;
					} else {
						$price = $user_price;
					}
				}
	  
				if ($user_price_action != 1) {
					if ($discount_type == 1 || $discount_type == 3) {
						$price -= round(($price * $discount_amount) / 100, 2);
						$sales_price -= round(($sales_price * $discount_amount) / 100, 2);
					} elseif ($discount_type == 2) {
						$price -= round($discount_amount, 2);
						$sales_price -= round($discount_amount, 2);
					} elseif ($discount_type == 4) {
						$price -= round((($price - $buying_price) * $discount_amount) / 100, 2);
						$sales_price -= round((($sales_price - $buying_price) * $discount_amount) / 100, 2);
					}
				}
				// add options and components prices
				$price += $properties_price;
				$sales_price += $properties_price;
	  
				if ($is_sales && $sales_price != $price) {
					set_tax_price($item_id, $item_type_id, $price, $sales_price, $tax_free, "sp_price", "sp_sales_price", "sp_tax_sales", false);
	  
					$t->sparse("sp_price_block", false);
					$t->sparse("sp_sales", false);
				} else {
					set_tax_price($item_id, $item_type_id, $price, 0, $tax_free, "sp_price", "", "sp_tax_price", false);
	  
					$t->sparse("sp_price_block", false);
					$t->set_var("sp_sales", "");
				}
	  
				$item_price = calculate_price($price, $is_sales, $sales_price);
				// show points price
				if ($points_system && $prod_offers_points_price) {
					if ($points_price <= 0) {
						$points_price = $item_price * $points_conversion_rate;
					}
					//$points_price += $components_points_price;
					//$selected_points_price = $selected_price * $points_conversion_rate;
					$product_params["base_points_price"] = $points_price;
					if ($is_points_price) {
						$t->set_var("points_rate", $points_conversion_rate);
						$t->set_var("points_decimals", $points_decimals);
						//$t->set_var("points_price", number_format($points_price + $selected_points_price, $points_decimals));
						$t->set_var("points_price", number_format($points_price, $points_decimals));
						$t->sparse("points_price_block", false);
					} else {
						$t->set_var("points_price_block", "");
					}
				}
	  
				// show reward points
				if ($points_system && $prod_offers_reward_points) {
					$reward_points = calculate_reward_points($reward_type, $reward_amount, $item_price, $buying_price, $points_conversion_rate, $points_decimals);
					//$reward_points += $components_reward_points;
	  
					$product_params["base_reward_points"] = $reward_points;
					if ($reward_type) {
						$t->set_var("reward_points", number_format($reward_points, $points_decimals));
						$t->sparse("reward_points_block", false);
					} else {
						$t->set_var("reward_points_block", "");
					}
				}
	  
				// show reward credits
				if ($credit_system && $prod_offers_reward_credits && ($reward_credits_users == 0 || ($reward_credits_users == 1 && $user_id))) {
					$reward_credits = calculate_reward_credits($credit_reward_type, $credit_reward_amount, $item_price, $buying_price);
					//$reward_credits += $components_reward_credits;
	  
					$product_params["base_reward_credits"] = $reward_credits;
					if ($credit_reward_type) {
						$t->set_var("reward_credits", currency_format($reward_credits));
						$t->sparse("reward_credits_block", false);
					} else {
						$t->set_var("reward_credits_block", "");
					}
				}

				// show quantity control
				set_quantity_control($quantity_limit, $stock_level, $quantity_control, $min_quantity, $max_quantity, $quantity_increment);
			
				// show buttons				
				if ($buy_link) {
					$t->set_var("buy_href", $buy_link . $affiliate_code);
				//} elseif ($is_properties || $product_quantity == "LISTBOX" || $product_quantity == "TEXTBOX" || $is_price_edit) {
				} elseif ($quantity_control == "LISTBOX" || $quantity_control == "TEXTBOX") {
					$t->set_var("buy_href", "javascript:document.form_" . $form_id . ".submit();");
					$t->set_var("wishlist_href", "javascript:document.form_" . $form_id . ".submit();");
				} else {
					$t->set_var("buy_href", $cart_link . "cart=ADD&add_id=" . $item_id . "&rp=". urlencode($rp). "#p" . $item_id);
					$t->set_var("wishlist_href", $cart_link . "cart=WISHLIST&add_id=" . $item_id . "&rp=". urlencode($rp). "#p" . $item_id);
				}
	  
				if (!$prod_offers_add_button) {
					$t->set_var("add_button_disabled", "");
					$t->set_var("add_button", "");
				} else {
					if ($use_stock_level && $stock_level < 1 && $disable_out_of_stock) {
						$t->set_var("add_button", "");
						$t->sparse("add_button_disabled", false);
					} else {
						$t->set_var("add_button_disabled", "");
						if (($use_stock_level && $stock_level < 1) || $issue_date_ts > $current_ts) {
							$t->set_var("ADD_TO_CART_MSG", PRE_ORDER_MSG);
						} else {
							$t->set_var("ADD_TO_CART_MSG", ADD_TO_CART_MSG);
						}
						$t->sparse("add_button", false);
					}
				}
				if (!$prod_offers_view_button) {
					$t->set_var("view_button", "");
				} else {
					$t->sparse("view_button", false);
				}
				if ($prod_offers_goto_button && is_array($shopping_cart) && sizeof($shopping_cart) > 0) {
					$t->sparse("checkout_button", false);
				} else {
					$t->set_var("checkout_button", "");
				}
				if ($user_id && !$buy_link && $prod_offers_wish_button) {
					$t->sparse("wishlist_button", false);
				} else {
					$t->set_var("wishlist_button", "");
				}
			}
	  
			if (!strlen($small_image)) {
				$image_exists = false;
				$small_image = $product_no_image;
			} elseif (!image_exists($small_image)) {
				$image_exists = false;
				$small_image = $product_no_image;
			} else {
				$image_exists = true;
			}
			if ($small_image)
			{
				if (preg_match("/^http\:\/\//", $small_image)) {
					$image_size = "";
				} else {
					$image_size = @GetImageSize($small_image);
					if ($image_exists && ($watermark || $restrict_products_images)) {
						$small_image = "image_show.php?item_id=".$item_id."&type=".$image_type_name."&vc=".md5($small_image);
					}
				}
				if (!strlen($small_image_alt)) { $small_image_alt = $item_name; }
				$t->set_var("alt", htmlspecialchars($small_image_alt));
				$t->set_var("src", htmlspecialchars($small_image));
				if (is_array($image_size)) {
					$t->set_var("width", "width=\"" . $image_size[0] . "\"");
					$t->set_var("height", "height=\"" . $image_size[1] . "\"");
				} else {
					$t->set_var("width", "");
					$t->set_var("height", "");
				}
				$t->parse("small_image", false);
			}
			else
			{
				$t->set_var("small_image", "");
			}
	  
			$is_next_record = $db->next_record();
			if ($so_number % $so_columns == 0 || (!$is_next_record && $so_number < $so_columns)) {
				$t->set_var("class_item_td", "");
			} else {
				$t->set_var("class_item_td", "vDelimiter");
			}
			$t->parse("items_cols");
			if($so_number % $so_columns == 0)
			{
				if ($is_next_record) {
					$t->sparse("delimiter", false);
				} else {
					$t->set_var("delimiter", "");
				}
				$t->parse("items_rows");
				$t->set_var("items_cols", "");
			}
		} while ($is_next_record);
	}

	if ($so_number % $so_columns != 0) {
		$t->parse("items_rows");
	}

	if ($user_id && $prod_offers_wish_button) {
		prepare_saved_types();
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);*/
}

?>