<?php                           

function product_options($block_name)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings;
	global $sc_item_id, $item_added, $sc_errors;
	global $html_title, $meta_keywords, $meta_description;
	global $currency;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_product_options.html");
	
	$user_info = get_session("session_user_info");
	$user_tax_free = get_setting_value($user_info, "tax_free", 0);
	$discount_type = get_session("session_discount_type");
	$discount_amount = get_session("session_discount_amount");
	$quantity_control = get_setting_value($settings, "quantity_control_details", "");
	$display_products = get_setting_value($settings, "display_products", 0);
	$product_no_image_large = get_setting_value($settings, "product_no_image_large", "");
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	$watermark = get_setting_value($settings, "watermark_big_image", 0);
	$show_item_code = get_setting_value($settings, "item_code_details", 0);
	$show_manufacturer_code = get_setting_value($settings, "manufacturer_code_details", 0);
	$user_id = get_session("session_user_id");		
	$user_type_id = get_session("session_user_type_id");

	$product_params = prepare_product_params();

	$item_id = ""; $quantity = 1; $properties_more = 0;
	$rp = get_param("rp");
	$cart_id = get_param("cart_id");	
	$shopping_cart = get_session("shopping_cart");
	if (isset($shopping_cart[$cart_id])) {
		$item_id = $shopping_cart[$cart_id]["ITEM_ID"];
		$quantity = $shopping_cart[$cart_id]["QUANTITY"];
		$properties_more = $shopping_cart[$cart_id]["PROPERTIES_MORE"];
	}

	if (!$properties_more) {
		if ($rp) {
			header("Location: " . $rp);
		} else {
			header("Location: " . get_custom_friendly_url("products.php"));
		}
		exit;
	}
	
	if (!VA_Products::check_exists($item_id)) {
		$t->set_var("item", "");
		$t->set_var("NO_PRODUCT_MSG", NO_PRODUCT_MSG);
		$t->parse("no_item", false);		
		$t->parse("block_body", false);
		$t->parse($block_name, true);
		return;
	}
	
	if (!VA_Products::check_permissions($item_id, VIEW_ITEMS_PERM)) {
		header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
		exit;
	}

	$t->set_var("products_href",       get_custom_friendly_url("products.php"));
	$t->set_var("product_options_href",get_custom_friendly_url("product_options.php"));
	$t->set_var("cart_id",  $cart_id);
	$t->set_var("quantity", $quantity);

	$t->set_var("PRODUCT_OUT_STOCK_MSG", htmlspecialchars(PRODUCT_OUT_STOCK_MSG));
	$t->set_var("out_stock_alert",       str_replace("'", "\\'", htmlspecialchars(PRODUCT_OUT_STOCK_MSG)));

	srand((double) microtime() * 1000000);
	$rnd = rand();
	$t->set_var("rnd", $rnd);
	$t->set_var("rp", htmlspecialchars($rp));

	$sql  = " SELECT i.item_id, i.item_type_id, i.item_code, i.special_offer,i.item_name, i.full_desc_type, i.short_description, i.full_description, ";
	$sql .= " i.big_image, i.big_image_alt, i.meta_title, i.meta_keywords, i.meta_description, ";
	$sql .= " i.manufacturer_code, m.manufacturer_name, i.hide_add_details, i.stock_level ";
	$sql .= " FROM (" . $table_prefix . "items i ";
	$sql .= " LEFT JOIN " . $table_prefix . "manufacturers m ON i.manufacturer_id=m.manufacturer_id) ";
	$sql .= " WHERE i.item_id = " . $db->tosql($item_id, INTEGER);
	
	$db->query($sql);
	if ($db->next_record())
	{
		$item_id = $db->f("item_id");
		$form_id = $item_id;
		$product_params["form_id"] = $form_id;
		$item_type_id = $db->f("item_type_id");
		$item_code = $db->f("item_code");
		$item_name_initial = $db->f("item_name");
		$item_name = get_translation($item_name_initial);
		$product_params["item_name"] = strip_tags($item_name);
		$short_description = get_translation($db->f("short_description"));
		$full_description = get_translation($db->f("full_description"));
		$special_offer = get_translation($db->f("special_offer"));
		$full_desc_type = $db->f("full_desc_type");
		$is_compared = $db->f("is_compared");
		$notes = get_translation($db->f("notes"));
		$buying_price = $db->f("buying_price");
		$weight = $db->f("weight");
		$tax_free = $db->f("tax_free");
		$manufacturer_code = $db->f("manufacturer_code");
		$manufacturer_name = $db->f("manufacturer_name");
		$stock_level = $db->f("stock_level");
		$use_stock_level = $db->f("use_stock_level");
		$disable_out_of_stock = $db->f("disable_out_of_stock");
		$hide_out_of_stock = $db->f("hide_out_of_stock");
		$hide_add_details = $db->f("hide_add_details");
		$quantity_limit = ($use_stock_level && ($disable_out_of_stock || $hide_out_of_stock));
		// meta files
		$html_title = $db->f("meta_title");
		$meta_keywords = $db->f("meta_keywords");
		$meta_description = $db->f("meta_description");

		$price = $shopping_cart[$cart_id]["PRICE"];
		$is_price_edit = $shopping_cart[$cart_id]["PRICE_EDIT"];
		$properties_price = $shopping_cart[$cart_id]["PROPERTIES_PRICE"];
		$properties_buying = $shopping_cart[$cart_id]["PROPERTIES_BUYING"];
		$properties_discount = $shopping_cart[$cart_id]["PROPERTIES_DISCOUNT"];
		$discount_applicable = $shopping_cart[$cart_id]["DISCOUNT"];
		$tax_free = $shopping_cart[$cart_id]["TAX_FREE"];
		if ($user_tax_free) { $tax_free = $user_tax_free;}

		if (!$full_description) { $full_description = $short_description; }

		if (!strlen($html_title)) { $html_title = $item_name; }
		if (!strlen($meta_description)) {
			if (strlen($short_description)) {
				$meta_description = $short_description;
			} else if (strlen($full_description)) {
				$meta_description = $full_description;
			} else {
				$meta_description = $item_name;
			}
		}

		$properties = show_items_properties($item_id, $item_id, $item_type_id, $price, $tax_free, "options", $product_params, false, $discount_applicable, $properties_discount);
		$is_properties  = $properties["is_any"];
		$properties_ids = $properties["ids"];
		$selected_price = $properties["price"];
		$components_price = $properties["components_price"];
		$components_tax_price = $properties["components_tax_price"];

		$t->set_var("item_id", $item_id);
		$t->set_var("item_name", $item_name);
		$t->set_var("product_name", $item_name);
		$t->set_var("product_title", $item_name);
		$t->set_var("item_name_strip", htmlspecialchars(strip_tags($item_name)));
		$t->set_var("manufacturer_code", htmlspecialchars($manufacturer_code));
		$t->set_var("manufacturer_name", htmlspecialchars($manufacturer_name));

		// show item code
		if ($show_item_code && $item_code) {
			$t->set_var("item_code", htmlspecialchars($item_code));
			$t->sparse("item_code_block", false);
		} else {
			$t->set_var("item_code_block", "");
		}
		// show manufacturer code
		if ($show_manufacturer_code && $manufacturer_code) {
			$t->set_var("manufacturer_code", htmlspecialchars($manufacturer_code));
			$t->sparse("manufacturer_code_block", false);
		} else {
			$t->set_var("product_code", "");
		}

		$t->set_var("item_added", "");
		$t->set_var("sc_errors", "");
		if ($item_id == $sc_item_id) {
			if ($sc_errors) {
				$t->set_var("errors_list", $sc_errors);
				$t->parse("sc_errors", false);
			} elseif ($item_added) {
				$cart = get_param("cart");
				if ($cart == "WISHLIST") {
					$added_message = str_replace("{product_name}", $item_name, "{product_name} was added to your Wishlist.");
				} else {
					$added_message = str_replace("{product_name}", $item_name, ADDED_PRODUCT_MSG);
				}
				$t->set_var("added_message", $added_message);
				$t->parse("item_added", false);
			}
		} 

		$big_image = $db->f("big_image");
		$big_image_alt = get_translation($db->f("big_image_alt"));
		if (!$big_image) { 
			$image_exists = false;
			$big_image = $product_no_image_large;
		} elseif (!image_exists($big_image)) {
			$image_exists = false;
			$big_image = $product_no_image_large; 
		} else {
			$image_exists = true;
		}
		if ($big_image)
		{
			if (preg_match("/^http\:\/\//", $big_image)) {
				$image_size = "";
			} else {
				$image_size = @GetImageSize($big_image);
				if ($image_exists && ($watermark || $restrict_products_images)) { 
					$big_image = "image_show.php?item_id=".$item_id."&type=large&vc=".md5($big_image); 
				}
			}
			if (!strlen($big_image_alt)) { $big_image_alt = $item_name; } 
			$t->set_var("alt", htmlspecialchars($big_image_alt));
			$t->set_var("src", htmlspecialchars($big_image));
			if (is_array($image_size)) {
				$t->set_var("width", "width=\"" . $image_size[0] . "\"");
				$t->set_var("height", "height=\"" . $image_size[1] . "\"");
			} else {
				$t->set_var("width", "");
				$t->set_var("height", "");
			}
			$t->sparse("big_image", false);
		}
		else
		{
			$t->set_var("big_image", "");
		}

		if ($display_products != 2 || strlen($user_id)) {
			//set_quantity_control($quantity_limit, $stock_level, $quantity_control);

			if ($properties_discount > 0) {
				$properties_price -= round(($properties_price * $properties_discount) / 100, 2);
			}
			$discount_total = 0;
			if ($discount_applicable) {
				if (!$is_price_edit) {
					if ($discount_type == 1) {
						$price -= round(($price * $discount_amount) / 100, 2);
					} elseif ($discount_type == 2) {
						$price -= round($discount_amount, 2);
					} elseif ($discount_type == 3) {
						$price -= round(($price * $discount_amount) / 100, 2);
					} elseif ($discount_type == 4) {
						$price -= round((($price - $buying_price) * $discount_amount) / 100, 2);
					}
				}
				if ($discount_type == 1) {
					$properties_price -= round(($properties_price * $discount_amount) / 100, 2);
				} elseif ($discount_type == 4) {
					$properties_price -= round((($properties_price - $properties_buying) * $discount_amount) / 100, 2);
				}
			} 
			$price += $properties_price;
			//$price -= $discount_total;

			set_tax_price($item_id, $item_type_id, $price + $selected_price, 0, $tax_free, "price", "", "tax_price", true, $components_price, $components_tax_price);

			$product_params["base_price"] = $price;
			$t->set_var("properties_discount", $properties_discount);
	  
			$t->set_var("buy_href", "javascript:document.form_" . $item_id . ".submit();");
	  
			// parse 'add to cart' button
			if ($hide_add_details) {
				$t->set_var("add_button", "");
				$t->set_var("add_button_disabled", "");
			} else {
				if ($use_stock_level && $stock_level < 1 && $disable_out_of_stock) {
					$t->set_var("add_button", "");
					$t->sparse("add_button_disabled", false);
				} else {
					$t->set_var("add_button_disabled", "");
					$t->sparse("add_button", false);
				}
			}
		}
		set_product_params($product_params);

		$t->parse("item");
		$t->set_var("no_item", "");
		
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>