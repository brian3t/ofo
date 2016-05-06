<?php
include_once("./includes/products_functions.php");

function products_latest($block_name)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings;
	global $date_show_format;
	global $language_code;
	global $currency;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_products_latest.html");
	$t->set_var("lp_rows", "");
	$t->set_var("lp_cols", "");

	$user_info = get_session("session_user_info");
	$user_tax_free = get_setting_value($user_info, "tax_free", 0);
	$discount_type = get_session("session_discount_type");
	$discount_amount = get_session("session_discount_amount");
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$records_per_page = get_setting_value($page_settings, "products_latest_recs", 10);
	$display_products = get_setting_value($settings, "display_products", 0);
	$prod_latest_image = get_setting_value($page_settings, "prod_latest_image",  0);
	$prod_latest_desc = get_setting_value($page_settings, "prod_latest_desc", 0);
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	product_image_fields($prod_latest_image, $image_type_name, $image_field, $image_alt_field, $watermark, $product_no_image);

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
	if ($prod_latest_desc == 1) {
		$desc_field = "short_description";
		$php_in_desc = get_setting_value($settings, "php_in_products_short_desc", 0);
	} elseif ($prod_latest_desc == 2) {
		$desc_field = "full_description";
		$php_in_desc = get_setting_value($settings, "php_in_products_full_desc", 0);
	} elseif ($prod_latest_desc == 3) {
		$desc_field = "features";
		$php_in_desc = get_setting_value($settings, "php_in_products_features", 0);
	} elseif ($prod_latest_desc == 4) {
		$desc_field = "special_offer";
		$php_in_desc = get_setting_value($settings, "php_in_products_hot_desc", 0);
	}

	$order =  get_setting_value($page_settings, "prod_latest_order", 0);
	switch ($order) {
		case 2:
			$order_field = "i.date_modified";
		break;		
		case 1:
			$order_field = "i.date_added";
		break;
		case 0: default:
			$order_field = "i.issue_date";
		break;
	
	}
	
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber     = 1;
	
	$items_ids = VA_Products::find_all_ids(
		array(
			"where" => $order_field . " IS NOT NULL AND $order_field<=" . $db->tosql(va_time(), DATETIME),
			"order" => " ORDER BY $order_field DESC "
		),
		VIEW_CATEGORIES_ITEMS_PERM
	);
	if (!$items_ids) return;
	
	$allowed_items_ids = VA_Products::find_all_ids("i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);
	
	$sql  = " SELECT i.item_id, i.item_type_id, i.item_name, i.friendly_url, i.issue_date, ";
	$sql .= " i.buying_price, i." . $price_field . ", i.".$properties_field.", i." . $sales_field . ", i.is_sales, i.tax_free ";
	if ($image_field) { $sql .= " , i." . $image_field; }
	if ($image_alt_field) { $sql .= " , i." . $image_alt_field; }
	if ($desc_field) { $sql .= " , i." . $desc_field; }	
	$sql .= " FROM " . $table_prefix . "items i ";
	$sql .= " WHERE i.item_id IN(" . $db->tosql($items_ids, INTEGERS_LIST) . ")";
	$sql .= " ORDER BY $order_field DESC ";
	
	$db->query($sql);
	if($db->next_record())
	{
		$latest_columns = get_setting_value($page_settings, "products_latest_cols", 1);
		$t->set_var("latest_column", (100 / $latest_columns) . "%");
		$latest_number = 0;
		do
		{
			$latest_number++;
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
				$item_desc = $db->f($desc_field);	
			}

			$t->set_var("item_id", $item_id);
			$t->set_var("item_name", $item_name);
			if ($friendly_urls && $friendly_url) {
				$t->set_var("details_url", $friendly_url . $friendly_extension);
			} else {
				$t->set_var("details_url", "product_details.php?item_id=" . $item_id);
			}
		
			if($item_image)
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
				if(is_array($image_size)) {
					$t->set_var("width", "width=\"" . $image_size[0] . "\"");
					$t->set_var("height", "height=\"" . $image_size[1] . "\"");
				} else {
					$t->set_var("width", "");
					$t->set_var("height", "");
				}
				$t->sparse("lp_image", false);
			} else {
				$t->set_var("lp_image", "");
			}
			if ($item_desc) {
				if ($php_in_desc) {
					eval_php_code($item_desc);
				}
				$t->set_var("desc_text", get_translation($item_desc));
				$t->sparse("lp_desc", false);
			} else {
				$t->set_var("lp_desc", "");
			}
			
			if (!$allowed_items_ids || !in_array($item_id, $allowed_items_ids)) {
				$t->set_var("restricted_class", " restrictedItem");
				$t->sparse("restricted_image", false);
			} else {
				$t->set_var("restricted_class", "");
				$t->set_var("restricted_image", "");
			}

			if ($display_products != 2 || strlen($user_id)) {
				$price = $db->f($price_field);
				$is_sales = $db->f("is_sales");
				$sales_price = $db->f($sales_field);
				if ($is_sales) {
					$price = $sales_price;
				}
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
					$price = $user_price;
				}

				$tax_free = $db->f("tax_free");
				if ($user_tax_free) { $tax_free = $user_tax_free; }
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
		  
				set_tax_price($item_id, $item_type_id, $price, 0, $tax_free, "lp_price", "", "lp_tax_price", false);

				$t->sparse("lp_price_block", false);
			}

			$issue_date = $db->f("issue_date", DATETIME);
			$issue_date_string  = va_date($date_show_format, $issue_date);
			$t->set_var("issue_date", $issue_date_string);

			$t->parse("lp_cols");
			if($latest_number % $latest_columns == 0)
			{
				$t->parse("lp_rows");
				$t->set_var("lp_cols", "");
			}
			
		} while ($db->next_record());              	

		if ($latest_number % $latest_columns != 0) {
			$t->parse("lp_rows");
		}

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}
}

?>