<?php
function products_related_purchase($block_name, $page_friendly_url = "", $page_friendly_params = array())
{
	global $t, $db, $site_id, $db_type, $table_prefix;
	global $settings, $page_settings;
	global $language_code;
	global $currency;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$user_info = get_session("session_user_info");
	$user_tax_free = get_setting_value($user_info, "tax_free", 0);
	$discount_type = get_session("session_discount_type");
	$discount_amount = get_session("session_discount_amount");
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$display_products = get_setting_value($settings, "display_products", 0);
	$product_no_image = get_setting_value($settings, "product_no_image", "");
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");

	$rpp_recs = get_setting_value($page_settings, "related_purchase_recs", 4);
	$rpp_cols = get_setting_value($page_settings, "related_purchase_cols", 1);
	$rpp_days = get_setting_value($page_settings, "related_purchase_days", 30);
	$rpp_status = get_setting_value($page_settings, "related_purchase_status", "PAID");
	$rpp_image = get_setting_value($page_settings, "related_purchase_image", 2);
	$rpp_desc = get_setting_value($page_settings, "related_purchase_desc", 0);

	$rpp_time = mktime(0,0,0, date("m"), date("d") - intval($rpp_days), date("Y"));
	$order_placed_date = va_time($rpp_time);

	$user_id = get_session("session_user_id");
	$user_type_id = get_session("session_user_type_id");
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
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	product_image_fields($rpp_image, $image_type_name, $image_field, $image_alt_field, $watermark, $product_no_image);

	$php_in_desc = 0; $desc_field = "";
	if ($rpp_desc == 1) {
		$desc_field = "short_description";
		$php_in_desc = get_setting_value($settings, "php_in_products_short_desc", 0);
	} elseif ($rpp_desc == 2) {
		$desc_field = "full_description";
		$php_in_desc = get_setting_value($settings, "php_in_products_full_desc", 0);
	} elseif ($rpp_desc == 3) {
		$desc_field = "features";
		$php_in_desc = get_setting_value($settings, "php_in_products_features", 0);
	} elseif ($rpp_desc == 4) {
		$desc_field = "special_offer";
		$php_in_desc = get_setting_value($settings, "php_in_products_hot_desc", 0);
	}

	$item_id = get_param("item_id");
	$t->set_file("block_body", "block_products_related_purchase.html");
	$t->set_var("product_details_href", "product_details.php");

	if ($friendly_urls && $page_friendly_url) {
		$pass_parameters = get_transfer_params($page_friendly_params);
		$product_page = $page_friendly_url . $friendly_extension;
	} else {
		$pass_parameters = get_transfer_params();
		$product_page = "product_details.php";
	}

	$sql_params = array();
	$sql_params["brackets"] = "((((";
	$sql_params["join"]  = " INNER JOIN " . $table_prefix . "orders_items oi ON oi.item_id=i.item_id) ";
	$sql_params["join"] .= " INNER JOIN " . $table_prefix . "orders_items bi ON (bi.order_id=oi.order_id AND bi.item_id=";
	$sql_params["join"] .= $db->tosql($item_id, INTEGER) . " AND oi.item_id<>" . $db->tosql($item_id, INTEGER) . ")) ";
	$sql_params["join"] .= " INNER JOIN " . $table_prefix . "orders o ON o.order_id=oi.order_id) ";
	$sql_params["join"] .= " LEFT JOIN " . $table_prefix . "order_statuses os ON oi.item_status=os.status_id) ";
	
	$sql_params["where"] = " o.order_placed_date >=" . $db->tosql($order_placed_date, DATETIME);
	if ($rpp_status == "PAID") {
		$sql_params["where"] .= " AND os.paid_status=1 ";
	} elseif (strlen($rpp_status) && $rpp_status != "ANY") {
		$sql_params["where"] .= " AND o.order_status=" . $db->tosql($rpp_status, INTEGER);
	}
	$sql_params["order"]  = " GROUP BY i.item_id ";
	
	
	if ($db_type == "access") {
		$sql_params["order"] .= " ORDER BY COUNT(i.item_id) DESC, i.item_id ";
	} else {
		$sql_params["order"] .= " ORDER BY count_items DESC, i.item_id ";
	}
	$db->RecordsPerPage = $rpp_recs;
	$db->PageNumber     = 1;
	
	$items = VA_Products::find_all("i.item_id", array("COUNT(i.item_id) AS count_items"), $sql_params, VIEW_CATEGORIES_ITEMS_PERM);
	if (!$items) return;
	$items_ids = array_keys($items);
	$allowed_items_ids = VA_Products::find_all_ids("i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);

		
	$sql  = " SELECT i.item_id, i.item_type_id, i.item_name, i.friendly_url, ";
	$sql .= " i.buying_price, i." . $price_field . ", i.".$properties_field.", i." . $sales_field . ", ";
	$sql .= " i.is_sales, i.tax_free, i.use_stock_level, i.stock_level, ";
	$sql .= " st_in.shipping_time_desc AS in_stock_message, st_out.shipping_time_desc AS out_stock_message ";
	if ($image_field) { $sql .= " , i.".$image_field; }
	if ($image_alt_field) { $sql .= " , i.".$image_alt_field; }
	if ($desc_field) { $sql .= " , i.".$desc_field; }
	$sql .= " FROM ((( " . $table_prefix . "items i  ";
	$sql .= " LEFT JOIN " . $table_prefix . "manufacturers m ON i.manufacturer_id=m.manufacturer_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_in ON i.shipping_in_stock=st_in.shipping_time_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_out ON i.shipping_out_stock=st_out.shipping_time_id) ";
	$sql .= " WHERE i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
		
	$db->query($sql);
	while ($db->next_record()) {
		$item_id = $db->f("item_id");
		$items[$item_id]["item_type_id"] = $db->f("item_type_id");
		$items[$item_id]["item_name"]    = get_translation($db->f("item_name"));
		$items[$item_id]["friendly_url"] = $db->f("friendly_url");
		$items[$item_id]["item_image"] = $db->f($image_field);
		$items[$item_id]["item_image_alt"] = get_translation($db->f($image_alt_field));	
		$items[$item_id]["stock_level"] = $db->f("stock_level");
		$items[$item_id]["use_stock_level"] = $db->f("use_stock_level");
		$items[$item_id]["in_stock_message"] = get_translation($db->f("in_stock_message"));
		$items[$item_id]["out_stock_message"] = get_translation($db->f("out_stock_message"));
		$items[$item_id]["price"] = $db->f($price_field);
		$items[$item_id]["is_sales"] = $db->f("is_sales");
		$items[$item_id]["sales_price"] = $db->f($sales_field);
		$items[$item_id]["buying_price"] = $db->f("buying_price");
		$items[$item_id]["tax_free"] = $db->f("tax_free");	
	}
	
	
	$t->set_var("rpp_rows", "");
	$t->set_var("rpp_column", (100 / $rpp_cols) . "%");
	$rpp_number = 0;
	foreach ($items AS $item_id => $item) {
		$rpp_number++;

		$item_type_id = $item["item_type_id"];
		$item_name    = $item["item_name"];
		$friendly_url = $item["friendly_url"];
		
		$item_image = ""; $item_image_alt = ""; $item_desc = "";
		$image_exists = false;
		if ($image_field) {
			$item_image     = $item["item_image"];	
			$item_image_alt = $item["item_image_alt"];	
			if (!strlen($item_image)) {
				$item_image = $product_no_image;
			} elseif (!image_exists($item_image)) {
				$item_image = $product_no_image;
			} else {
				$image_exists = true;
			}
		}
		if ($desc_field) {
			$item_desc = $db->f($desc_field);
		}
		
		if ($friendly_urls && $friendly_url) {
			$details_url = $friendly_url . $friendly_extension;
		} else {
			$details_url = "product_details.php?item_id=".urlencode($item_id);
		}
		
		if (!$allowed_items_ids || !in_array($item_id, $allowed_items_ids)) {
			$t->set_var("restricted_class", " restrictedItem");
			$t->sparse("restricted_image", false);
		} else {
			$t->set_var("restricted_class", "");
			$t->set_var("restricted_image", "");
		}

		$t->set_var("item_id", $item_id);
		$t->set_var("item_name", $item_name);
		$t->set_var("details_url", $details_url);
		$t->set_var("rpp_tax_price", "");
		$t->set_var("rpp_tax_sales", "");
		
		$stock_level     = $item["stock_level"];
		$use_stock_level = $item["use_stock_level"];
		if (!$use_stock_level || $stock_level > 0) {
			$shipping_time_desc = $item["in_stock_message"];
		} else {
			$shipping_time_desc = $item["out_stock_message"];
		}
		if (strlen($shipping_time_desc)) {
			$t->set_var("shipping_time_desc", $shipping_time_desc);
			$t->parse("rpp_availability", false);
		} else {
			$t->set_var("rpp_availability", "");
		}
		
		
		if ($display_products != 2 || strlen($user_id)) {
			$price        = $item["price"];
			$is_sales     = $item["is_sales"];
			$sales_price  = $item["sales_price"];
			$buying_price = $item["buying_price"];
					
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

			$tax_free = $item["tax_free"];
			if ($user_tax_free) { $tax_free = $user_tax_free; }
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

			if ($sales_price != $price && $is_sales) {
				set_tax_price($item_id, $item_type_id, $price, $sales_price, $tax_free, "rpp_price", "rpp_sales_price", "rpp_tax_sales", false);

				$t->sparse("rpp_price_block", false);
				$t->sparse("rpp_sales", false);
			} else {
				set_tax_price($item_id, $item_type_id, $price, 0, $tax_free, "rpp_price", "", "rpp_tax_price", false);

				$t->sparse("rpp_price_block", false);
				$t->set_var("rpp_sales", "");
			}
		}

		if ($item_image) {
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
			$t->sparse("rpp_image", false);
		} else {
			$t->set_var("rpp_image", "");
		}
		if ($item_desc) {
			if ($php_in_desc) {
				eval_php_code($item_desc);
			}
			$t->set_var("desc_text", $item_desc);
			$t->sparse("rpp_desc", false);
		} else {
			$t->set_var("rpp_desc", "");
		}

		$t->parse("rpp_cols");
		if ($rpp_number % $rpp_cols == 0) {
			$t->parse("rpp_rows");
			$t->set_var("rpp_cols", "");
		}
	}

	if ($rpp_number % $rpp_cols != 0) {
		$t->parse("rpp_rows");
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>