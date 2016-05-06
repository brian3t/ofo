<?php
include_once("./includes/products_functions.php");

function basket_recommended($block_name)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings, $current_page;
	global $language_code;
	global $sc_item_id, $item_added, $sc_errors;
	global $currency;

	$current_ts = va_timestamp();
	$records_per_page = get_setting_value($page_settings, "basket_prod_recom_per_page", 10);
	$pr_columns = get_setting_value($page_settings, "basket_prod_recom_cols", 1);

	$user_info = get_session("session_user_info");
	$user_tax_free = get_setting_value($user_info, "tax_free", 0);
	$user_id = get_session("session_user_id");
	$user_name = get_session("session_user_name");
	$discount_type = get_session("session_discount_type");
	$discount_amount = get_session("session_discount_amount");
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$quantity_control = get_setting_value($settings, "quantity_control_list", "");
	$display_products = get_setting_value($settings, "display_products", 0);
	$product_no_image = get_setting_value($settings, "product_no_image", "");
	$watermark = get_setting_value($settings, "watermark_small_image", 0);
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	$price_type = get_session("session_price_type");
	$image_type_name = "small";

	$product_params = prepare_product_params();

	if ($price_type == 1) {
		$price_field = "trade_price";
		$sales_field = "trade_sales";
	} else {
		$price_field = "price";
		$sales_field = "sales_price";
	}

	// new product settings	
	$new_product_enable = get_setting_value($settings, "new_product_enable", 0);	
	$new_product_order  = get_setting_value($settings, "new_product_order", 0);	
	$new_product_sql    = "";
	if ($new_product_enable) {
		switch ($new_product_order) {
			case 0:
				$new_product_sql = ", i.issue_date AS new_product_date ";
			break;
			case 1:
				$new_product_sql = ", i.date_added AS new_product_date ";
			break;
			case 2:
				$new_product_sql = ", i.date_modified AS new_product_date ";
			break;
		}		
	}
	
	$item_id = get_param("item_id");
	$t->set_file("block_body", "block_basket_recommended.html");
	$t->set_var("product_details_href", "product_details.php");

	$recommended_title = str_replace("{user_name}", $user_name, PRODUCTS_RECOMMENDED_TITLE);
	$t->set_var("PRODUCTS_RECOMMENDED_TITLE", $recommended_title);

	$items_ids = "";
	$shopping_cart = get_session("shopping_cart");
	if (is_array($shopping_cart)) {
		foreach ($shopping_cart as $cart_id => $item) {
			$item_id = intval($item["ITEM_ID"]);
			$properties_more = isset($item["PROPERTIES_MORE"]) ? $item["PROPERTIES_MORE"] : false;
			if ($item_id && !$properties_more) { 
				if (strlen($items_ids)) { $items_ids .= ",";	}
				$items_ids .= $item_id;
			}
		}
	}

	if (!strlen($items_ids)) {
		return;
	}
	
	$sql_params = array();
	$sql_params["brackets"] = "("; 
	$sql_params["join"]   = " LEFT JOIN " . $table_prefix . "items_related ir ON i.item_id=ir.related_id) ";
	$sql_params["where"]  = " ir.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")";
	$sql_params["where"] .= " AND ir.related_id NOT IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")";

	$recom_products_ids = VA_Products::find_all_ids($sql_params, VIEW_CATEGORIES_ITEMS_PERM);	
	
	$sql_params = array();
	$sql_params["brackets"] = "("; 
	$sql_params["join"]   = " LEFT JOIN " . $table_prefix . "items_accessories ia ON i.item_id=ia.accessory_id) ";	
	$sql_params["where"]  = " ia.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")";
	$sql_params["where"] .= " AND ia.accessory_id NOT IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")";
		
	$recom_accessories_ids = VA_Products::find_all_ids($sql_params, VIEW_CATEGORIES_ITEMS_PERM);	
	
	$recom_ids = array_merge($recom_products_ids, $recom_accessories_ids);
	if (!$recom_ids) return;
	array_unique($recom_ids);
	$total_records = count($recom_ids);
	
	$allowed_items_ids = VA_Products::find_all_ids("i.item_id IN (" . $db->tosql($recom_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);

	// prepare navigator for recommended products
	$pages_number = 5;
	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $current_page);
	$page_number = $n->set_navigator("pr_navigator", "rpage", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$t->set_var("rpage", $page_number);

	$sql  = " SELECT i.item_id, i.item_type_id, i.tax_free, i.item_name, i.friendly_url, i.short_description, i.small_image, i.small_image_alt, ";
	$sql .= " i.buying_price, i." . $price_field . ", i.is_price_edit, i." . $sales_field . ", i.is_sales, i.tax_free, ";
	$sql .= " i.use_stock_level, i.disable_out_of_stock, i.hide_out_of_stock, i.hide_add_list, ";
	$sql .= " i.issue_date, i.stock_level, st_in.shipping_time_desc AS in_stock_message, st_out.shipping_time_desc AS out_stock_message ";
	$sql .= $new_product_sql;
	$sql .= " FROM (((" . $table_prefix . "items i ";
	$sql .= " LEFT JOIN " . $table_prefix . "manufacturers m ON i.manufacturer_id=m.manufacturer_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_in ON i.shipping_in_stock=st_in.shipping_time_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_out ON i.shipping_out_stock=st_out.shipping_time_id) ";
	$sql .= " WHERE i.item_id IN (" . $db->tosql($recom_ids, INTEGERS_LIST) . ")";
	//$sql .= " ORDER BY related_order ";
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql);
	if ($db->next_record())
	{
		$t->set_var("pr_column", (100 / $pr_columns) . "%");
		$pr_number = 0;
		do
		{
			$pr_number++;
			$item_id = $db->f("item_id");
			$form_id = $item_id;
			$product_params["form_id"] = $form_id;
			$item_type_id = $db->f("item_type_id");
			$item_name = get_translation($db->f("item_name"));
			$product_params["item_name"] = strip_tags($item_name);
			$friendly_url = $db->f("friendly_url");
			$short_description = get_translation($db->f("short_description"));
			$tax_free = $db->f("tax_free");
			if ($user_tax_free) { $tax_free = $user_tax_free; }
			$issue_date_ts = 0;
			$issue_date = $db->f("issue_date", DATETIME);
			if (is_array($issue_date)) {
				$issue_date_ts = va_timestamp($issue_date);
			}

			$price = $db->f($price_field);
			$is_price_edit = $db->f("is_price_edit");
			$is_sales = $db->f("is_sales");
			$sales_price = $db->f($sales_field);
			$buying_price = $db->f("buying_price");
			
			$user_price  = false; 
			$user_price_action = 0;
			$q_prices    = get_quantity_price($item_id, 1);
			if ($q_prices) {
				$user_price  = $q_prices [0];
				$user_price_action = $q_prices [2];
			}				

			$stock_level = $db->f("stock_level");
			$use_stock_level = $db->f("use_stock_level");
			$disable_out_of_stock = $db->f("disable_out_of_stock");
			$hide_out_of_stock = $db->f("hide_out_of_stock");
			$hide_add_list = $db->f("hide_add_list");
			$quantity_limit = ($use_stock_level && ($disable_out_of_stock || $hide_out_of_stock));
			if (!$use_stock_level || $stock_level > 0) {
				$shipping_time_desc = get_translation($db->f("in_stock_message"));
			} else {
				$shipping_time_desc = get_translation($db->f("out_stock_message"));
			}

			if ($friendly_urls && $friendly_url) {
				$details_url = $friendly_url . $friendly_extension;
			} else {
				$details_url = "product_details.php?item_id=".urlencode($item_id);
			}
			
			if ($new_product_enable) {
				$new_product_date = $db->f("new_product_date");
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
				$hide_add_list = true;
			} else {
				$t->set_var("restricted_class", "");
				$t->set_var("restricted_image", "");
			}

			// calculate price
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
			$item_price = calculate_price($price, $is_sales, $sales_price);

			$properties = show_items_properties($item_id, $item_id, $item_type_id, $item_price, $tax_free, "list", $product_params);
			$is_properties  = $properties["is_any"];
			$properties_ids = $properties["ids"];
			$selected_price = $properties["price"];
			$components_price = $properties["components_price"];
			$components_tax_price = $properties["components_tax_price"];

			$t->set_var("item_id", $item_id);
			$t->set_var("item_name", $item_name);
			$t->set_var("item_name_strip", htmlspecialchars(strip_tags($item_name)));
			$t->set_var("details_url", $details_url);
			$t->set_var("short_description", $short_description);
			$t->set_var("pr_tax_price", "");
			$t->set_var("pr_tax_sales", "");

			$t->set_var("sc_errors", "");
			if ($item_id == $sc_item_id) {
				if ($sc_errors) {
					$t->set_var("errors_list", $sc_errors);
					$t->parse("sc_errors", false);
				}
			} 

			if(strlen($shipping_time_desc)) {
				$t->set_var("shipping_time_desc", $shipping_time_desc);
				$t->parse("pr_availability", false);
			} else {
				$t->set_var("pr_availability", "");
			}


			$product_params["pe"] = 0;
			if ($display_products != 2 || strlen($user_id)) 
			{
				set_quantity_control($quantity_limit, $stock_level, $quantity_control);

				$base_price = calculate_price($price, $is_sales, $sales_price);
				$product_params["base_price"] = $base_price;
				if ($is_price_edit) 
				{
					$t->set_var("price_block_class", "priceBlockEdit");
					if ($price > 0) {
						$control_price = number_format($price, 2);
					} else {
						$control_price = "";
					}
					$product_params["pe"] = 1;
					$t->set_var("pr_price", $control_price);
					$t->set_var("pr_price_control", "<input name=\"price\" type=\"text\" class=\"price\" value=\"" . $control_price . "\">");
					$t->sparse("pr_price_block", false);
					$t->set_var("pr_sales", "");
				} 
				elseif ($sales_price != $price && $is_sales) 
				{
					set_tax_price($item_id, $item_type_id, $price, $sales_price + $selected_price, $tax_free, "pr_price", "pr_sales_price", "pr_tax_sales", true, $components_price, $components_tax_price);

					$product_params["pe"] = 0;
					$t->sparse("pr_price_block", false);
					$t->sparse("pr_sales", false);
				}
				else
				{
					set_tax_price($item_id, $item_type_id, $price + $selected_price, 0, $tax_free, "pr_price", "", "pr_tax_price", true, $components_price, $components_tax_price);

					$product_params["pe"] = 0;
					$t->sparse("pr_price_block", false);
					$t->set_var("pr_sales", "");
				}
				srand ((double) microtime() * 1000000);
				$rnd = rand();
				$buy_link = $db->f("buy_link");
				if ($buy_link) {
					$t->set_var("buy_href", $db->f("buy_link") . $db->f("affiliate_code"));
				} elseif ($is_properties || $quantity_control == "LISTBOX" || $quantity_control == "TEXTBOX") {
					$t->set_var("buy_href", "javascript:document.form_" . $item_id . ".submit();");
				} else {
					$buy_link = $current_page . "?item_id=" . $item_id . "&cart=ADD&rnd=" . $rnd . "&rpage=" . $page_number . "&rp=" . urlencode($current_page) . "#p" . $item_id;
					$t->set_var("buy_href", $buy_link);
				}

				if ($hide_add_list) {
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
			}
			set_product_params($product_params);

			$small_image = $db->f("small_image");
			$small_image_alt = get_translation($db->f("small_image_alt"));
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
				$t->sparse("pr_small_image", false);
			}
			else
			{
				$t->set_var("pr_small_image", "");
			}

			$t->parse("pr_cols");
			if($pr_number % $pr_columns == 0)
			{
				$t->parse("pr_rows");
				$t->set_var("pr_cols", "");
			}
			
		} while ($db->next_record());              	

		if ($pr_number % $pr_columns != 0) {
			$t->parse("pr_rows");
		}

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}
}

?>