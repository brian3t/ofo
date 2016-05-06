<?php
include_once("./includes/products_functions.php");

function products_top_sellers($block_name)
{
	global $t, $db, $db_type, $table_prefix;
	global $settings, $page_settings;
	global $language_code, $currency;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$user_info = get_session("session_user_info");
	$user_tax_free = get_setting_value($user_info, "tax_free", 0);
	$discount_type = get_session("session_discount_type");
	$discount_amount = get_session("session_discount_amount");
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$bestsellers_records = get_setting_value($page_settings, "bestsellers_records", 10);
	$bestsellers_days    = get_setting_value($page_settings, "bestsellers_days",    7);
	$bestsellers_status  = get_setting_value($page_settings, "bestsellers_status",  "");
	$bestsellers_image = get_setting_value($page_settings, "bestsellers_image",  0);
	$bestsellers_desc = get_setting_value($page_settings, "bestsellers_desc", 0);
	$display_products = get_setting_value($settings, "display_products", 0);
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	product_image_fields($bestsellers_image, $image_type_name, $image_field, $image_alt_field, $watermark, $product_no_image);
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

	$php_in_desc = 0; $desc_field = "";
	if ($bestsellers_desc == 1) {
		$desc_field = "short_description";
		$php_in_desc = get_setting_value($settings, "php_in_products_short_desc", 0);
	} elseif ($bestsellers_desc == 2) {
		$desc_field = "full_description";
		$php_in_desc = get_setting_value($settings, "php_in_products_full_desc", 0);
	} elseif ($bestsellers_desc == 3) {
		$desc_field = "features";
		$php_in_desc = get_setting_value($settings, "php_in_products_features", 0);
	} elseif ($bestsellers_desc == 4) {
		$desc_field = "special_offer";
		$php_in_desc = get_setting_value($settings, "php_in_products_hot_desc", 0);
	}

	// new product settings	
	$new_product_enable = get_setting_value($settings, "new_product_enable", 0);
	$new_product_order  = get_setting_value($settings, "new_product_order", 0);
	
	
	$bestsellers_time = mktime(0,0,0, date("m"), date("d") - intval($bestsellers_days), date("Y"));
	$order_placed_date = va_time($bestsellers_time);

	$t->set_file("block_body", "block_top_sellers.html");
	$t->set_var("top_category_name", PRODUCTS_TITLE);
	$t->set_var("TOP_SELLERS_TITLE", TOP_SELLERS_TITLE);
	$t->set_var("top_sellers_items",  "");
	
	$db->RecordsPerPage = $bestsellers_records;
	$db->PageNumber = 1;
	
	$sql_params = array();
	$sql_params["brackets"] = "((";			
	$sql_params["join"]  = " INNER JOIN " . $table_prefix . "orders_items oi ON i.item_id=oi.item_id) ";
	$sql_params["join"] .= " INNER JOIN " . $table_prefix . "orders o ON oi.order_id=o.order_id) ";
	$sql_params["where"] = " o.order_placed_date >=" . $db->tosql($order_placed_date, DATETIME);
	$sql_params["order"]  = " GROUP BY i.item_id ";
	$sql_params["order"] .= " ORDER BY item_id_counts DESC ";
	if ($bestsellers_status == "PAID") {
		$sql_params["brackets"] .= "(";
		$sql_params["join"]     .= " INNER JOIN " . $table_prefix . "order_statuses os ON oi.item_status=os.status_id) ";
		$sql_params["where"]    .= " AND os.paid_status=1 ";
	} elseif (strlen($bestsellers_status) && $bestsellers_status != "ANY") {
		$sql_params["where"] .= " AND o.order_status=" . $db->tosql($bestsellers_status, INTEGER);
	}	
	$items = VA_Products::find_all_ids(array("COUNT(i.item_id) AS item_id_counts"), $sql_params);
	if (!$items) return;
	
	$items_ids = array_keys($items);
	if (!$items_ids) return;
	$allowed_items_ids = VA_Products::find_all_ids("i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);
	
	$sql  = " SELECT item_id, item_type_id, item_name, friendly_url,";
	$sql .= " " . $price_field . ", ".$properties_field.", is_sales, " . $sales_field . ", buying_price, tax_free ";
	// new product db
	if ($new_product_enable) {
		switch ($new_product_order) {
			case 0:
				$sql .= ", issue_date AS new_product_date ";
			break;
			case 1:
				$sql .= ", date_added AS new_product_date ";
			break;
			case 2:
				$sql .= ", date_modified AS new_product_date ";
			break;
		}		
	}
	if ($image_field) { $sql .= " , " . $image_field; }
	if ($image_alt_field) { $sql .= " , " . $image_alt_field; }
	if ($desc_field) { $sql .= " , " . $desc_field; }
	$sql .= " FROM ";
	$sql .= $table_prefix . "items ";
	$sql .= " WHERE item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")";	
	$db->query($sql);
	
	if ($db->next_record())
	{
		$item_number = 0;
		do
		{
			$item_number++;
			$item_id = $db->f("item_id");
			$item_type_id = $db->f("item_type_id");
			$item_name = get_translation($db->f("item_name"));
			$friendly_url = $db->f("friendly_url");
			$item_image = ""; $item_image_alt = ""; $item_desc = "";
			$image_exists = false;
			if ($image_field) {
				$item_image = $db->f($image_field);
				$item_image_alt = get_translation($db->f($image_alt_field));
				if (!strlen($item_image)) {
					$item_image = $product_no_image;
				} elseif (!image_exists($item_image)) {
					$item_image = $product_no_image;
				} else {
					$image_exists = true;
				}
			}
			if ($desc_field) {
				$item_desc = get_translation($db->f($desc_field));
			}

			if ($friendly_urls && $friendly_url) {
				$details_href = $friendly_url . $friendly_extension;
			} else {
				$details_href = "product_details.php?item_id=".urlencode($item_id);
			}
			$t->set_var("details_href", $details_href);
			
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
			} else {
				$t->set_var("restricted_class", "");
				$t->set_var("restricted_image", "");
			}
			
			if ($item_image)
			{
				if (preg_match("/^http\:\/\//", $item_image)) {
					$image_size = "";
				} else {
					$image_size = @GetImageSize($item_image);
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
				$t->sparse("top_image", false);
			} else {
				$t->set_var("top_image", "");
			}
			if ($item_desc) {
				if ($php_in_desc) {
					eval_php_code($item_desc);
				}
				$t->set_var("desc_text", $item_desc);
				$t->sparse("top_desc", false);
			} else {
				$t->set_var("top_desc", "");
			}

			$t->set_var("top_position", $item_number);
			$t->set_var("top_name", $item_name);

			if ($display_products != 2 || strlen($user_id))
			{
				$tax_free = $db->f("tax_free");
				if ($user_tax_free) { $tax_free = $user_tax_free; }
				$price = $db->f($price_field);
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
				
				$properties_price = $db->f($properties_field);
				if ($user_price > 0 && ($user_price_action > 0 || !$discount_type)) {
					if ($is_sales) {
						$sales_price = $user_price;
					} else {
						$price = $user_price;
					}
				}

				if ($is_sales) {
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
				// add options and components prices
				$price += $properties_price;

				set_tax_price($item_id, $item_type_id, $price, 0, $tax_free, "top_value", "", "top_tax_price", false);

				$t->sparse("top_value_block", false);
			}

			$t->parse("top_sellers_items", true);
		} while ($db->next_record());

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}
}

?>