<?php

function products_compare($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id, $currency;
	global $column_width, $items_number, $fields_values;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_products_compare.html");

	$t->set_var("product_details_href", "product_details.php");

	$tax_rates = get_tax_rates();
	$price_type = get_session("session_price_type");
	if ($price_type == 1) {
		$price_field = "trade_price";
		$sales_field = "trade_sales";
	} else {
		$price_field = "price";
		$sales_field = "sales_price";
	}
	$user_info = get_session("session_user_info");
	$user_tax_free = get_setting_value($user_info, "tax_free", 0);
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	$product_no_image = get_setting_value($settings, "product_no_image", "");
	$watermark = get_setting_value($settings, "watermark_small_image", 0);
	$image_type_name = "small";

	$features = array();
	$items = get_param("items");
	$t->set_var("items_html", htmlspecialchars($items));
	$t->set_var("items_url", urlencode($items));

	$errors = "";
	if (!preg_match("/^(\d+)(,\d+)+$/", $items))	{
		$errors = COMPARE_PARAM_ERROR_MSG;
	}

	// preparing data
	$items_number = 0; $row = 0;
	if (!strlen($errors)) {
		$sql  = " SELECT * ";
		$sql .= " FROM " . $table_prefix . "items i ";
		$sql .= " WHERE i.item_id IN (" . $db->tosql($items, INTEGERS_LIST) . ")";
		$sql .= " AND i.is_showing=1 AND i.is_approved=1 ";
		$sql .= " AND ((i.hide_out_of_stock=1 AND i.stock_level > 0) OR i.hide_out_of_stock=0 OR i.hide_out_of_stock IS NULL)";
		$db->query($sql);
		while ($db->next_record()) {
			$items_number++;
  
			$item_id = $db->f("item_id");
			$price = $db->f($price_field);
			$is_sales = $db->f("is_sales");
			$sales_price = $db->f($sales_field);
			$buying_price = $db->f("buying_price");
			$tax_free = $db->f("tax_free");
			$item_name = get_translation($db->f("item_name"));
			if ($user_tax_free) { $tax_free = $user_tax_free; }
			$image_src = $db->f("small_image");
			$image_alt = get_translation($db->f("small_image_alt"));
			if (!strlen($image_alt)) {
				$image_alt = $item_name;
			}
			if (!strlen($image_src)) {
				$image_exists = false;
				$image_src = $product_no_image;
			} elseif (!image_exists($image_src)) {
				$image_exists = false;
				$image_src = $product_no_image; 
			} else {
				$image_exists = true;
			}
  
			$fields_values["item_id"][$row] = $item_id;
			$fields_values["item_type_id"][$row] = $db->f("item_type_id");
			$fields_values["item_name"][$row] = $item_name;
			$fields_values["price"][$row] = $price;
			$fields_values["buying_price"][$row] = $buying_price;
			$fields_values["is_sales"][$row] = $is_sales;
			$fields_values["tax_free"][$row] = $tax_free;
			$fields_values["sales_price"][$row] = $sales_price;
			
			$fields_values["image_src"][$row] = $image_src;
			$fields_values["image_alt"][$row] = $image_alt;
			$fields_values["image_width"][$row] = "";
			$fields_values["image_height"][$row] = "";
			if ($image_src)
			{
				if (preg_match("/^http\:\/\//", $image_src)) {
					$image_size = "";
				} else {
					$image_size = @GetImageSize($image_src);
					if ($image_exists && ($watermark || $restrict_products_images)) {
						$image_src = "image_show.php?item_id=".$item_id."&type=".$image_type_name."&vc=".md5($image_src);
						$fields_values["image_src"][$row] = $image_src;
					}
				}
				if (is_array($image_size)) {
					$fields_values["image_width"][$row] = "width=\"" . $image_size[0] . "\"";
					$fields_values["image_width"][$row] = "height=\"" . $image_size[1] . "\"";
				}
			}
  
			$row++;
		}

		if ($items_number < 2) {
			$errors = COMPARE_MIN_ALLOWED_MSG;
		} elseif ($items_number > 5) {
			$errors = COMPARE_MAX_ALLOWED_MSG;
		}


	}


	if (!strlen($errors)) 
	{
		for ($j = 0; $j < $items_number; $j++) 
		{	
			$item_id = $fields_values["item_id"][$j];
			$item_type_id = $fields_values["item_type_id"][$j];
  
			// get all properties
			$sql  = " SELECT ip.property_name, ip.property_description, ipv.property_value  ";
			$sql .= " FROM (" . $table_prefix . "items_properties ip ";
			$sql .= " LEFT JOIN " . $table_prefix . "items_properties_values ipv ON ip.property_id=ipv.property_id) ";
			$sql .= " WHERE (ip.item_id=" . intval($item_id) . " OR ip.item_type_id=" . $db->tosql($item_type_id, INTEGER) . ") ";
			$sql .= " ORDER BY ip.property_order, ip.property_id ";
			$db->query($sql);
			while ($db->next_record()) {
				$group_id = "options";
				$group_name = PROD_OPTIONS_MSG;
				$feature_name = get_translation($db->f("property_name"));
				$property_value = get_translation($db->f("property_value"));
				$property_description = get_translation($db->f("property_description"));
				$feature_value = strlen($property_value) ? $property_value : $property_description;
				$feature_groups[$group_id] = $group_name;
				if (isset($features[$group_id][$feature_name][$j])) {
					$features[$group_id][$feature_name][$j] .= "; " . $feature_value;
				} else {
					$features[$group_id][$feature_name][$j] = $feature_value;
				}
			}
  
			// get features list
			$sql  = " SELECT fg.group_id,fg.group_name,f.feature_name,f.feature_value ";
			$sql .= " FROM " . $table_prefix . "features f, " . $table_prefix . "features_groups fg ";
			$sql .= " WHERE f.group_id=fg.group_id ";
			$sql .= " AND f.item_id=" . intval($item_id);
			$sql .= " ORDER BY fg.group_order ";
			$db->query($sql);
			while ($db->next_record()) {
				$group_id = $db->f("group_id");
				$group_name = get_translation($db->f("group_name"));
				$feature_name = get_translation($db->f("feature_name"));
				$feature_value = get_translation($db->f("feature_value"));
				$feature_groups[$group_id] = $group_name;
				if (isset($features[$group_id][$feature_name][$j])) {
					$features[$group_id][$feature_name][$j] .= "; " . $feature_value;
				} else {
					$features[$group_id][$feature_name][$j] = $feature_value;
				}
			}
		}

		$column_width = round(85 / $items_number);
		show_title();

		$t->set_var("column_width", $column_width . "%");
		$t->set_var("colspan", ($items_number + 1));

		foreach ($features as $group_id => $group_features)
		{
			$t->set_var("features", "");
			foreach ($group_features as $feature_name => $features_values)
			{		
				$t->set_var("features_values", "");
				for ($p = 0; $p < $items_number; $p++) {
					$feature_value = isset($features_values[$p]) ? $features_values[$p] : "";
					$t->set_var("feature_value", $feature_value);
					$t->parse("features_values", true);
				}
				$t->set_var("feature_name", $feature_name);
				$t->parse("features", true);
			}

			$t->set_var("group_name", $feature_groups[$group_id]);
			$t->parse("features_groups", true);
		}

		$t->parse("compared", true);
		$t->set_var("errors_block", "");
	} 
	else 
	{
		$t->set_var("compared", "");
		$t->set_var("errors", $errors);
		$t->parse("errors_block", true);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

function show_title()
{
	global $t, $settings, $currency;
	global $column_width, $items_number, $fields_values;
	global $restrict_products_images, $product_no_image, $watermark, $image_type_name;

	$discount_type = get_session("session_discount_type");
	$discount_amount = get_session("session_discount_amount");
	$display_products = get_setting_value($settings, "display_products", 0);
	$user_id = get_session("session_user_id");		
	
	if ($items_number > 2) {
		for ($i = 0; $i < $items_number; $i++) {
			$products = array();
			for ($j = 0; $j < $items_number; $j++) {
				if ($i != $j) { $products[] = $fields_values["item_id"][$j]; }
			}		
			$exclude_link = "compare.php?items=" . urlencode(join($products, ","));
			$fields_values["exclude_link"][$i] = $exclude_link;
		}
	}

	for ($j = 0; $j < $items_number; $j++) {
		$item_id = $fields_values["item_id"][$j];
		$item_type_id = $fields_values["item_type_id"][$j];
		$item_name = $fields_values["item_name"][$j];
		$image_src = $fields_values["image_src"][$j];
		$image_alt = $fields_values["image_alt"][$j];
		$image_width = $fields_values["image_width"][$j];
		$image_height = $fields_values["image_height"][$j];

		$t->set_var("item_id", $item_id);
		$t->set_var("item_name", $item_name);
		$t->set_var("tax_price", "");
		$t->set_var("tax_sales", "");
		if (strlen($image_src)) {
			$t->set_var("image_src", $image_src);
			$t->set_var("image_width", $image_width);
			$t->set_var("image_height", $image_height);
			$t->set_var("image_alt", htmlspecialchars($image_alt));
			$t->parse("image_block", false);
		} else {
			$t->set_var("image_block", "");
		}

		if ($items_number > 2) {
			$t->set_var("exclude_href", $fields_values["exclude_link"][$j]);
			$t->parse("exclude_link", "");
		} else {
			$t->set_var("exclude_link", "");
		}

		if ($display_products != 2 || strlen($user_id)) {
			$price = $fields_values["price"][$j];
			$is_sales = $fields_values["is_sales"][$j];
			$sales_price = $fields_values["sales_price"][$j];
			$buying_price = $fields_values["buying_price"][$j];
			$tax_free = $fields_values["tax_free"][$j];

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

			// get price for subcomponents
			calculate_subcomponents_price($item_id, $item_type_id, $components_price, $components_tax_price);
	  
			if ($sales_price != $price && $is_sales) {
				set_tax_price($item_id, $item_type_id, $price, $sales_price, $tax_free, "price", "sales_price", "tax_sales", false, $components_price, $components_tax_price);

				$t->sparse("price_block", false);
				$t->sparse("sales", false);
			} else {
				set_tax_price($item_id, $item_type_id, $price, 0, $tax_free, "price", "", "tax_price", false, $components_price, $components_tax_price);

				$t->sparse("price_block", false);
				$t->set_var("sales", "");
			}
		}

		$t->parse("top_title", true);		
	}
}

?>