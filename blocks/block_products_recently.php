<?php

function products_recently_viewed($block_name)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings;
	global $category_id, $currency;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$user_info = get_session("session_user_info");
	$user_tax_free = get_setting_value($user_info, "tax_free", 0);
	$discount_type = get_session("session_discount_type");
	$discount_amount = get_session("session_discount_amount");

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$recent_records = get_setting_value($page_settings, "products_recent_records", 5);
	$display_products = get_setting_value($settings, "display_products", 0);
	$product_no_image = get_setting_value($settings, "product_no_image", "");
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	$recent_image = get_setting_value($page_settings, "recent_image",  0);
	$recent_desc = get_setting_value($page_settings, "recent_desc", 0);
	$user_id = get_session("session_user_id");

	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	product_image_fields($recent_image, $image_type_name, $image_field, $image_alt_field, $watermark, $product_no_image);

	$php_in_desc = 0; $desc_field = "";
	if ($recent_desc == 1) {
		$desc_field = "short_description";
		$php_in_desc = get_setting_value($settings, "php_in_products_short_desc", 0);
	} elseif ($recent_desc == 2) {
		$desc_field = "full_description";
		$php_in_desc = get_setting_value($settings, "php_in_products_full_desc", 0);
	} elseif ($recent_desc == 3) {
		$desc_field = "features";
		$php_in_desc = get_setting_value($settings, "php_in_products_features", 0);
	} elseif ($recent_desc == 4) {
		$desc_field = "special_offer";
		$php_in_desc = get_setting_value($settings, "php_in_products_hot_desc", 0);
	}
	
	// new product settings	
	$new_product_enable = get_setting_value($settings, "new_product_enable", 0);
	$new_product_order  = get_setting_value($settings, "new_product_order", 0);

	$t->set_file("block_body", "block_products_recently_viewed.html");
	$t->set_var("compare_href",          "compare.php");
	$t->set_var("compare_name",          "products_recent");

	$t->set_var("top_category_name",     PRODUCTS_TITLE);
	$t->set_var("recently_viewed_rows",  "");

	$recently_viewed = get_session("session_recently_viewed");
	if (is_array($recently_viewed)) {
		$recent_columns = get_setting_value($page_settings, "products_recent_cols", 1);
		$t->set_var("recent_viewed_column", (100 / $recent_columns) . "%");
		$recent_number = 0;
		foreach ($recently_viewed as $key => $recent_info) {
			list($item_id, $item_type_id, $item_name, $friendly_url, $recent_price, $buying_price, $tax_free, $is_compared) = $recently_viewed[$key];
			if (!VA_Products::check_permissions($item_id, VIEW_CATEGORIES_ITEMS_PERM)) continue;
						
			$recent_number++;
			if ($recent_number > $recent_records) {
				break;
			}
			if ($user_tax_free) { $tax_free = $user_tax_free; }

			if ($friendly_urls && $friendly_url) {
				$details_href = $friendly_url . $friendly_extension;
			} else {
				$details_href = "product_details.php?item_id=".urlencode($item_id);
			}
			$image_exists = false;
			$item_image = ""; $item_image_alt = ""; $item_desc = "";
			if ($new_product_enable || $image_field || $desc_field) {
				$sql = " SELECT item_id ";
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
				$sql .= " FROM " . $table_prefix . "items ";				
				$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);				
				$db->query($sql);
				if ($db->next_record()) {
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
				}
			}
			// Set value before any blocks would be parsed
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
			if (!VA_Products::check_permissions($item_id, VIEW_ITEMS_PERM)) {
				$t->set_var("restricted_class", " restrictedItem");
				$t->sparse("restricted_image", false);
			} else {
				$t->set_var("restricted_class", "");
				$t->set_var("restricted_image", "");
			}
		
			if($item_image)
			{
				if (preg_match("/^http\:\/\//", $item_image)) {
					$image_size = "";
				} else {
					$image_size = @getimagesize($item_image);
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
				$t->set_var("desc_text", $item_desc);
				$t->sparse("top_desc", false);
			} else {
				$t->set_var("top_desc", "");
			}

			$t->set_var("item_id", $item_id);
			$t->set_var("top_position", $recent_number);
			$t->set_var("top_name", get_translation($item_name));
			if ($is_compared) {
				$t->parse("recent_compare", false);
			} else {
				$t->set_var("recent_compare", "");
			}

			if ($display_products != 2 || strlen($user_id)) {
				set_tax_price($item_id, $item_type_id, $recent_price, 0, $tax_free, "top_value", "", "top_tax_price", false);

				$t->sparse("top_value_block", false);
			}

			$t->parse("recently_viewed_cols");
			if ($recent_number % $recent_columns == 0)
			{
				$t->parse("recently_viewed_rows");
				$t->set_var("recently_viewed_cols", "");
			}
		}

		if ($recent_number % $recent_columns != 0) {
			$t->parse("recently_viewed_rows");
		}

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}
}

?>