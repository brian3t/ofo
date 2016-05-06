<?php

function wishlist_items($block_name)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings;
	global $date_show_format;
	global $language_code, $currency;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$operation = get_param("operation");
	if ($operation == "add") {
		$user_id = get_param("user_id");
		$cart_item_id = get_param("cart_item_id");

		// retrieve cart
		$sql  = " SELECT * FROM " . $table_prefix . "saved_items ";
		$sql .= " WHERE cart_item_id=" . $db->tosql($cart_item_id, INTEGER);
		$sql .= " AND user_id=" . $db->tosql($user_id, INTEGER);
		$sql .= " ORDER BY cart_item_id ";
		$db->query($sql);
		if ($db->next_record()) {
			do {
				$sc_errors = "";
				$cart_item_id = $db->f("cart_item_id");
				$item_id = $db->f("item_id");
				$item_name = $db->f("item_name");
				$quantity = $db->f("quantity");
				$price = $db->f("price");

				// add to cart
				add_to_cart($item_id, $price, $quantity, "db", "ADD", $new_cart_id, $second_page_options, $sc_errors, $cart_item_id, $item_name);

			} while ($db->next_record());
		}
		// check if any coupons can be added or removed
		check_coupons();

		$rp_url = new VA_URL(get_custom_friendly_url("wishlist.php"), false);
		$rp_url->add_parameter("se", REQUEST, "se");
		$rp_url->add_parameter("page", REQUEST, "page");

		header("Location: " . get_custom_friendly_url("basket.php") . "?rp=" . urlencode($rp_url->get_url()));
		exit;
	}


	$t->set_file("block_body", "block_wishlist_items.html");
	$t->set_var("wl_rows", "");

	$se = get_param("se");
	$user_ids = "";
	if ($se) {
		$sql  = " SELECT user_id FROM " . $table_prefix . "users ";
		$sql .= " WHERE email=" . $db->tosql($se, TEXT);
		$db->query($sql);
		if ($db->next_record()) {
			$ids = array();
			do {
				$ids[] = $db->f("user_id"); 
			} while ($db->next_record());
			$user_ids = implode(",", $ids);
		}
	}

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$records_per_page = get_setting_value($page_settings, "wl_recs", 10);
	$display_products = get_setting_value($settings, "display_products", 0);
	$product_no_image = get_setting_value($settings, "product_no_image", "");
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	$wl_image = get_setting_value($page_settings, "wl_image",  1);

	$user_id = get_session("session_user_id");		
	$user_type_id = get_session("session_user_type_id");
	$price_type = get_session("session_price_type");
	if ($price_type == 1) {
		$price_field = "trade_price";
		$sales_field = "trade_sales";
	} else {
		$price_field = "price";
		$sales_field = "sales_price";
	}
	$image_field = ""; $image_alt_field = ""; $desc_field = "";
	if ($wl_image == 1) {
		$image_field = "tiny_image";
		$image_alt_field = "tiny_image_alt";
	} else if ($wl_image == 2) {
		$image_field = "small_image";
		$image_alt_field = "small_image_alt";
	} else if ($wl_image == 3) {
		$image_field = "big_image";
		$image_alt_field = "big_image_alt";
	} else if ($wl_image == 4) {
		$image_field = "super_image";
		$image_alt_field = "big_image_alt";
	}

	if ($user_ids) {
		// get data for navigator
		$sql  = " SELECT COUNT(*) ";
		$sql .= " FROM ((" . $table_prefix . "saved_items si ";
		$sql .= " LEFT JOIN " . $table_prefix . "saved_types st ON si.type_id=st.type_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "items i ON si.item_id=i.item_id) ";
		$sql .= " WHERE si.user_id IN (" . $db->tosql($user_ids, INTEGERS_LIST) . ") ";
		$sql .= " AND si.cart_id=0 ";
		$sql .= " AND st.allowed_search=1 ";
		$total_records = get_db_value($sql);

		$n = new VA_Navigator($settings["templates_dir"], "navigator.html", "wishlist.php");
		$nav_type = 1; $nav_pages = 10; 
		$nav_first_last = true; $nav_prev_next = true; $inactive_links = false;
		$n->set_parameters($nav_first_last, $nav_prev_next, $inactive_links);
		$page_number = $n->set_navigator("navigator", "page", 2, $nav_pages, $records_per_page, $total_records, false);


		$sql  = " SELECT i.item_id, i.friendly_url, st.type_name, ";
		$sql .= " si.user_id, si.cart_item_id, si.item_name AS saved_name, ";
		$sql .= " si.price AS saved_price, si.quantity AS quantity_wants, si.quantity_bought, si.date_added ";
		if ($image_field) { $sql .= " , i." . $image_field; }
		if ($image_alt_field) { $sql .= " , i." . $image_alt_field; }
		$sql .= " FROM (((" . $table_prefix . "saved_items si ";
		$sql .= " LEFT JOIN " . $table_prefix . "saved_types st ON si.type_id=st.type_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "items i ON si.item_id=i.item_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "manufacturers m ON i.manufacturer_id=m.manufacturer_id) ";
		$sql .= " WHERE si.user_id IN (" . $db->tosql($user_ids, INTEGERS_LIST) . ") ";
		$sql .= " AND si.cart_id=0 ";
		$sql .= " AND st.allowed_search=1 ";
		$sql .= " ORDER BY si.cart_item_id ";
  
		$db->RecordsPerPage = $records_per_page;
		$db->PageNumber = $page_number;
		$db->query($sql);
		if($db->next_record())
		{
			$cart_url = new VA_URL("wishlist.php", false);
			$cart_url->add_parameter("se", REQUEST, "se");
			$cart_url->add_parameter("page", REQUEST, "page");
			$cart_url->add_parameter("cart_item_id", DB, "cart_item_id");
			$cart_url->add_parameter("user_id", DB, "user_id");
			$cart_url->add_parameter("operation", CONSTANT, "add");

			$latest_number = 0;
			do
			{
				$latest_number++;
				$item_id = $db->f("item_id");
				$price = $db->f("saved_price");
				$quantity_wants = $db->f("quantity_wants");
				$quantity_bought = $db->f("quantity_bought");
				$item_name = get_translation($db->f("saved_name"));
				$friendly_url = $db->f("friendly_url");
				$type_name = $db->f("type_name");
				$item_image = ""; $item_image_alt = ""; $item_desc = "";
				if ($image_field) {
					$item_image = $db->f($image_field);	
					$item_image_alt = $db->f($image_alt_field);	
					if (!$item_image) {
						$item_image = $product_no_image;
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
						if (isset($restrict_products_images) && $restrict_products_images) { $item_image = "image_show.php?item_id=".$item_id."&type=small"; }
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
					$t->sparse("wl_image", false);
				} else {
					$t->set_var("wl_image", "");
				}
  
				$t->set_var("wl_price", currency_format($price));
				$t->set_var("wl_type", $type_name);
				$t->set_var("quantity_wants", $quantity_wants);
				$t->set_var("quantity_bought", $quantity_bought);

				$t->set_var("cart_url", $cart_url->get_url());
  
				$date_added = $db->f("date_added", DATETIME);
				$date_added_string  = va_date($date_show_format, $date_added);
				$t->set_var("date_added", $date_added_string);
  
				$t->parse("wl_rows");
				
			} while ($db->next_record());              	

			$t->parse("wl_items", false);
		} else {
			$t->parse("no_wishlist", false);
		}

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	} else if ($se) {
		$t->parse("no_wishlist", false);

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>