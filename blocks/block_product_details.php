<?php                           

function product_details($block_name)
{
	global $t, $db, $site_id, $table_prefix;
	global $settings, $page_settings;
	global $HTTP_GET_VARS;
	global $datetime_show_format, $language_code;
	global $sc_item_id, $item_added, $sc_errors;
	global $html_title, $meta_keywords, $meta_description;
	global $currency;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_product_details.html");
	
	$shopping_cart = get_session("shopping_cart");
	$user_info = get_session("session_user_info");
	$user_tax_free = get_setting_value($user_info, "tax_free", 0);
	$discount_type = get_session("session_discount_type");
	$discount_amount = get_session("session_discount_amount");
	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$quantity_control = get_setting_value($settings, "quantity_control_details", "");
	$price_matrix_details = get_setting_value($settings, "price_matrix_details", 0);
	$tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
	$points_system = get_setting_value($settings, "points_system", 0);
	$points_conversion_rate = get_setting_value($settings, "points_conversion_rate", 1);
	$points_decimals = get_setting_value($settings, "points_decimals", 0);
	$points_price_details = get_setting_value($settings, "points_price_details", 0);
	$reward_points_details = get_setting_value($settings, "reward_points_details", 0);
	$points_prices = get_setting_value($settings, "points_prices", 0);
	
	// credit settings
	$credit_system = get_setting_value($settings, "credit_system", 0);
	$reward_credits_users = get_setting_value($settings, "reward_credits_users", 0);
	$reward_credits_details = get_setting_value($settings, "reward_credits_details", 0);

	// new product settings	
	$new_product_enable = get_setting_value($settings, "new_product_enable", 0);	
	$new_product_order  = get_setting_value($settings, "new_product_order", 0);	
	
	$use_tabs = get_setting_value($page_settings, "use_tabs", 0);
	$details_manufacturer_image = get_setting_value($page_settings, "details_manufacturer_image", 0);
	$display_products = get_setting_value($settings, "display_products", 0);
	$product_no_image_large = get_setting_value($settings, "product_no_image_large", "");
	$show_item_code = get_setting_value($settings, "item_code_details", 0);
	$show_manufacturer_code = get_setting_value($settings, "manufacturer_code_details", 0);
	$php_in_full_desc = get_setting_value($settings, "php_in_products_full_desc", 0);
	$php_in_hot_desc = get_setting_value($settings, "php_in_products_hot_desc", 0);
	$php_in_features = get_setting_value($settings, "php_in_products_features", 0);
	$php_in_notes = get_setting_value($settings, "php_in_products_notes", 0);

	$hide_weight_details = get_setting_value($settings, "hide_weight_details", 0);
	$shop_hide_add_details = get_setting_value($settings, "hide_add_details", 0);
	$shop_hide_view_details = get_setting_value($settings, "hide_view_details", 0);
	$shop_hide_checkout_details = get_setting_value($settings, "hide_checkout_details", 0);
	$shop_hide_wishlist_details = get_setting_value($settings, "hide_wishlist_details", 0);
	$weight_measure = get_setting_value($settings, "weight_measure", "");
	$stock_level_details = get_setting_value($settings, "stock_level_details", 0);

	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	$watermark_small_image = get_setting_value($settings, "watermark_small_image", 0);
	$watermark_big_image = get_setting_value($settings, "watermark_big_image", 0);
	$watermark_super_image = get_setting_value($settings, "watermark_super_image", 0);

	// get products reviews settings
	$reviews_settings = get_settings("products_reviews");
	$reviews_allowed_view = get_setting_value($reviews_settings, "allowed_view", 0);
	$reviews_allowed_post = get_setting_value($reviews_settings, "allowed_post", 0);

	$product_params = prepare_product_params();

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

	$category_id = get_param("category_id");
	$item_id = get_param("item_id");
	if (!strlen($category_id) && strlen($item_id)) {		
		$category_id = VA_Products::get_category_id($item_id, VIEW_ITEMS_PERM);
	}

	// build query string
	$transfer_query = transfer_params("", true);
	$reviews_url = get_custom_friendly_url("reviews.php") . $transfer_query;
	$tell_friend_href = get_custom_friendly_url("tell_friend.php") . "?item_id=" . urlencode($item_id) . "&type=products";

	$t->set_var("products_href", get_custom_friendly_url("products.php"));
	$t->set_var("product_details_href", get_custom_friendly_url("product_details.php"));
	$t->set_var("basket_href",      get_custom_friendly_url("basket.php"));
	$t->set_var("checkout_href",    get_custom_friendly_url("checkout.php"));
	$t->set_var("reviews_url",      $reviews_url);
	$t->set_var("reviews_href",     $reviews_url);
	$t->set_var("tell_friend_href", $tell_friend_href);
	$t->set_var("product_print_href", get_custom_friendly_url("product_print.php"));
	$t->set_var("cl",               $currency["left"]);
	$t->set_var("cr",               $currency["right"]);
	$t->set_var("tax_prices_type",  $tax_prices_type);
	$t->set_var("PRODUCT_OUT_STOCK_MSG", htmlspecialchars(PRODUCT_OUT_STOCK_MSG));
	$t->set_var("out_stock_alert",       str_replace("'", "\\'", htmlspecialchars(PRODUCT_OUT_STOCK_MSG)));

	// random value
	srand ((double) microtime() * 1000000);
	$random_value = rand();
	$current_ts = va_timestamp();

	// generate page link with query parameters
	$page = get_custom_friendly_url("product_details.php");
	$remove_parameters = array("rnd", "cart", "item_id");
	$get_vars = isset($_GET) ? $_GET : $HTTP_GET_VARS;
	$query_string = get_query_string($get_vars, $remove_parameters, "", false);
	$page	.= $query_string;
	$page .= strlen($query_string) ? "&" : "?";
	$cart_link  = $page;
	$page .= "item_id=" . urlencode($item_id);
	$cart_link .= "rnd=" . $random_value . "&";

	$t->set_var("rnd", $random_value);
	$t->set_var("rp_url", urlencode($page));
	$t->set_var("rp", htmlspecialchars($page));

	$t->set_var("current_category_id", $category_id);
	
	if (!VA_Products::check_exists($item_id)) {
		$t->set_var("item", "");
		$t->set_var("links_block", "");		
		$t->set_var("item_name", "&nbsp;");
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
	
	$sql  = " SELECT i.item_id, i.item_type_id, i.special_offer, i.item_code, i.item_name, i.friendly_url, ";
	$sql .= " i.features, i.full_desc_type, i.short_description, i.full_description, ";
	$sql .= " i.big_image, i.big_image_alt, i.small_image, i.small_image_alt, i.super_image, ";
	$sql .= " i.meta_title, i.meta_keywords, i.meta_description, ";
	$sql .= " i.buying_price, i." . $price_field . ", i.is_price_edit, i.".$properties_field.", i." . $sales_field . ", i.discount_percent, i.tax_free, i.buy_link, ";
	$sql .= " i.total_views, i.votes, i.points, i.is_sales, i.is_compared, ";
	$sql .= " i.is_points_price, i.points_price, i.reward_type, i.reward_amount, i.credit_reward_type, i.credit_reward_amount, ";
	$sql .= " i.manufacturer_code, m.manufacturer_name, m.affiliate_code, m.image_large AS manufacturer_image_src, m.image_large_alt AS manufacturer_image_alt, m.image_small AS manufacturer_image_small_src, m.image_small_alt AS manufacturer_image_small_alt,";
	$sql .= " i.template_name, i.hide_add_details, i.preview_url, i.preview_width, i.preview_height, ";
	$sql .= " i.issue_date, i.stock_level, st_in.shipping_time_desc AS in_stock_message, st_out.shipping_time_desc AS out_stock_message, ";
	$sql .= " sr.shipping_rule_desc, i.notes, i.weight, ";
	$sql .= " i.use_stock_level, i.disable_out_of_stock, i.hide_out_of_stock, i.min_quantity, i.max_quantity, i.quantity_increment ";
	// new product db
	if ($new_product_enable) {
		switch ($new_product_order) {
			case 0:
				$sql .= ", i.issue_date AS new_product_date ";
			break;
			case 1:
				$sql .= ", i.date_added AS new_product_date ";
			break;
			case 2:
				$sql .= ", i.date_modified AS new_product_date ";
			break;
		}		
	}
	$sql .= " FROM ((((";
	$sql .= $table_prefix . "items i ";
	$sql .= " LEFT JOIN " . $table_prefix . "manufacturers m ON i.manufacturer_id=m.manufacturer_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_in ON i.shipping_in_stock=st_in.shipping_time_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_out ON i.shipping_out_stock=st_out.shipping_time_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "shipping_rules sr ON i.shipping_rule_id=sr.shipping_rule_id) ";
	$sql .= " WHERE i.item_id = " . $db->tosql($item_id, INTEGER);

	$t->set_var("category_id", $category_id);	
	$db->query($sql);
	if ($db->next_record())
	{
		$item_number = 0;

		// set custom template for specific product
		$template_name = $db->f("template_name");
		if (strlen($template_name) && @file_exists($settings["templates_dir"]."/".$template_name)) {
			$t->set_file("block_body", $template_name);
		}
		
		$item_number++;

		$item_id = $db->f("item_id");
		$item_type_id = $db->f("item_type_id");
		$item_code = $db->f("item_code");

		$item_name_initial = $db->f("item_name");
		$item_name = get_translation($item_name_initial);
		$product_params["item_name"] = strip_tags($item_name);
		$friendly_url = $db->f("friendly_url");
		$form_id = $item_id;
		$product_params["form_id"] = $form_id;
		$short_description = get_translation($db->f("short_description"));
		$full_description = get_translation($db->f("full_description"));
		$special_offer = get_translation($db->f("special_offer"));
		$full_desc_type = $db->f("full_desc_type");
		if ($full_desc_type != 1) {
			$full_description = nl2br(htmlspecialchars($full_description));
		}
		$is_compared = $db->f("is_compared");
		$notes = get_translation($db->f("notes"));
		$issue_date_ts = 0;
		$issue_date = $db->f("issue_date", DATETIME);
		if (is_array($issue_date)) {
			$issue_date_ts = va_timestamp($issue_date);
		}

		$price = $db->f($price_field);
		$is_price_edit = $db->f("is_price_edit");
		$is_sales = $db->f("is_sales");
		$sales_price = $db->f($sales_field);
		
		$user_price  = false; 
		$user_price_action = 0;
		$q_prices    = get_quantity_price($item_id, 1);
		if ($q_prices) {
			$user_price  = $q_prices [0];
			$user_price_action = $q_prices [2];
		}
				
		$properties_price = $db->f($properties_field);
		$buying_price = $db->f("buying_price");

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

		$weight = $db->f("weight");
		$item_tax_free = $db->f("tax_free");
		$tax_free = ($item_tax_free || $user_tax_free);
		$manufacturer_code = $db->f("manufacturer_code");
		$manufacturer_name = get_translation($db->f("manufacturer_name"));
		// show manufactures image
		if ($details_manufacturer_image == 2){
			$manufacturer_image_src = $db->f("manufacturer_image_small_src");
			$manufacturer_image_alt = get_translation($db->f("manufacturer_image_small_alt"));
		} else if ($details_manufacturer_image == 3){
			$manufacturer_image_src = $db->f("manufacturer_image_src");
			$manufacturer_image_alt = get_translation($db->f("manufacturer_image_alt"));
		} else {
			$manufacturer_image_src = "";
			$manufacturer_image_alt = "";
		}
		$stock_level = $db->f("stock_level");
		$use_stock_level = $db->f("use_stock_level");
		$disable_out_of_stock = $db->f("disable_out_of_stock");
		$hide_out_of_stock = $db->f("hide_out_of_stock");
		$hide_add_details = $db->f("hide_add_details");
		$min_quantity = $db->f("min_quantity");
		$max_quantity = $db->f("max_quantity");
		$quantity_increment = $db->f("quantity_increment");
		$quantity_limit = ($use_stock_level && ($disable_out_of_stock || $hide_out_of_stock));
		$total_views = $db->f("total_views");
		// meta files
		$html_title = get_translation($db->f("meta_title"));
		$meta_keywords = get_translation($db->f("meta_keywords"));
		$meta_description = get_translation($db->f("meta_description"));
		// preview data
		$preview_url = $db->f("preview_url");
		$preview_width = $db->f("preview_width");
		$preview_height = $db->f("preview_height");		
		
		if (!$full_description) { $full_description = $short_description; }

		if (!strlen($html_title)) { $html_title = $item_name; }
		if (!strlen($meta_description)) {
			if (strlen($short_description)) {
				$meta_description = $short_description;
			} elseif (strlen($full_description)) {
				$meta_description = $full_description;
			} else {
				$meta_description = $item_name;
			}
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

		$properties = show_items_properties($item_id, $item_id, $item_type_id, $item_price, $tax_free, "details", $product_params, $price_matrix_details);
		$is_properties  = $properties["is_any"];
		$properties_ids = $properties["ids"];
		$selected_price = $properties["price"];
		$components_price = $properties["components_price"];
		$components_tax_price = $properties["components_tax_price"];
		$components_points_price = $properties["components_points_price"];
		$components_reward_points = $properties["components_reward_points"];
		$components_reward_credits = $properties["components_reward_credits"];

		if ($new_product_enable) {
			$new_product_date = $db->f("new_product_date");			
			$is_new_product = is_new_product($new_product_date);
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
		
				
		$t->set_var("item_id", $item_id);
		$t->set_var("form_id", $form_id);
		$t->set_var("item_name", $item_name);
		$t->set_var("product_name", $item_name);
		$t->set_var("product_title", $item_name);
		$t->set_var("item_name_strip", htmlspecialchars(strip_tags($item_name)));
		$t->set_var("manufacturer_code", htmlspecialchars($manufacturer_code));
		$t->set_var("manufacturer_name", htmlspecialchars($manufacturer_name));
		$t->set_var("manufacturer_image_src", htmlspecialchars($manufacturer_image_src));
		$t->set_var("manufacturer_image_alt", htmlspecialchars($manufacturer_image_alt));
		$t->set_var("total_views", $total_views);
		$t->set_var("tax_price", "");
		$t->set_var("tax_sales", "");

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

		// show manufacturer's image
		if (strlen($manufacturer_image_src)) {
			$t->sparse("manufacturer_image", false);
		} else {
			$t->set_var("manufacturer_image", "");
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

		$t->set_var("stock_level", $stock_level);
		if (!$use_stock_level || $stock_level > 0) {
			$shipping_time_desc = $db->f("in_stock_message");
		} else {
			$shipping_time_desc = $db->f("out_stock_message");
		}
		if (strlen($shipping_time_desc))
		{
			$t->set_var("shipping_time_desc", get_translation($shipping_time_desc));
			$t->parse("availability", false);
		}
		if (strlen($db->f("shipping_rule_desc")))
		{
			$t->set_var("shipping_rule_desc", get_translation($db->f("shipping_rule_desc")));
			$t->parse("shipping_block", false);
		}
		if ($stock_level_details && $use_stock_level) {
			$t->set_var("stock_level", $stock_level);
			$t->parse("stock_level_block", false);
		} else {
			$t->set_var("stock_level_block", "");
		}


		$features_list = get_translation($db->f("features"));
		if ($features_list)
		{
			if ($php_in_features) {
				eval_php_code($features_list);
			}
			$features_list = str_replace("{item_name}", $item_name, $features_list);
			$features_list = str_replace("{item_code}", $item_code, $features_list);
			$features_list = str_replace("{manufacturer_code}", $manufacturer_code, $features_list);
			$features_list = str_replace("{manufacturer_image_src}", $manufacturer_image_src, $features_list);
			$features_list = str_replace("{manufacturer_image_alt}", $manufacturer_image_alt, $features_list);
			$t->set_var("features_list", $features_list);
			$t->sparse("features_list_block", false);
		}
		if ($php_in_hot_desc) {
			eval_php_code($special_offer);
		}
		$t->set_var("special_offer", $special_offer);

		$big_image = $db->f("big_image");
		if (!$big_image) { 
			$big_image = $db->f("small_image"); 
			$watermark = $watermark_small_image;
		} else {
			$watermark = $watermark_big_image;
		}
		if (!$big_image) { 
			$image_exists = false;
			$big_image = $product_no_image_large; 
		} elseif (!image_exists($big_image)) {
			$image_exists = false;
			$big_image = $product_no_image_large;
		} else {
			$image_exists = true;
		}
		
		$big_image_alt = get_translation($db->f("big_image_alt"));
		if (!$big_image_alt) { $big_image_alt = get_translation($db->f("small_image_alt")); }
		$product_image_width = 0;
		if ($big_image)
		{
			if (preg_match("/^http(s)?:\/\//", $big_image)) {
				$image_size = "";
			} else {
				$image_size = @getimagesize($big_image);
				if ($image_exists && ($watermark || $restrict_products_images)) { 
					$big_image = "image_show.php?item_id=".$item_id."&type=large&vc=".md5($big_image); 
				}
			}
			if (!strlen($big_image_alt)) {
				$big_image_alt = $item_name;
			}
			$t->set_var("alt", htmlspecialchars($big_image_alt));
			$t->set_var("src", htmlspecialchars($big_image));
			if (is_array($image_size)) {
				$product_image_width = $image_size[0];
				$t->set_var("width", "width=\"" . $image_size[0] . "\"");
				$t->set_var("height", "height=\"" . $image_size[1] . "\"");
			} else {
				$t->set_var("width", "");
				$t->set_var("height", "");
			}
			$t->parse("big_image", false);
		}
		else
		{
			$t->set_var("big_image", "");
		}

		$open_large_image = get_setting_value($settings, "open_large_image", 0);
		
		$super_image = $db->f("super_image");
		if (strlen($super_image))
		{
			if (preg_match("/^http(s)?:\/\//", $super_image)) {
				$image_size = "";
			} else {
				$image_size = @getimagesize($super_image);
				if ($watermark_super_image || $restrict_products_images) {
					$super_image = "image_show.php?item_id=".$item_id."&type=super&vc=".md5($super_image);
				}
			}
			$src = htmlspecialchars($super_image);
			if (is_array($image_size)) {
				$width  = $image_size[0];
				$height = $image_size[1];
			} else {
				$width  = 0;
				$height = 0;
			}
			
			$t->set_var("src", $src);
			$t->set_var("width", $width);
			$t->set_var("height", $height);
			
			if ($open_large_image) {
				$open_large_image_function = "popupImage(this); return false;";
			} elseif ($width) {
				$open_large_image_function = "return openSuperImage('$src', $width, $height);";
			} else {
				$open_large_image_function = "return openSuperImage('$src');";
			}

			$t->set_var("open_large_image_function", $open_large_image_function);
				
			$t->sparse("super_image", false);
		}
		else
		{
			$t->set_var("super_image", "");
		}

		if (strlen($preview_url)) {
			if (!$preview_width) { $preview_width = 500; }
			if (!$preview_height) { $preview_height = 400; }
			$t->set_var("preview_url", $preview_url);
			$t->set_var("preview_width", $preview_width);
			$t->set_var("preview_height", $preview_height);
			$t->sparse("product_preview", false);
		} else {
			$t->set_var("product_preview", "");
		}

		// show points price
		if ($points_system && $points_price_details) {
			if ($points_price <= 0) {
				$points_price = $item_price * $points_conversion_rate;
			}
			$points_price += $components_points_price;
			$selected_points_price = $selected_price * $points_conversion_rate;
			$product_params["base_points_price"] = $points_price;
			if ($is_points_price) {
				$t->set_var("points_rate", $points_conversion_rate);
				$t->set_var("points_decimals", $points_decimals);
				$t->set_var("points_price", number_format($points_price + $selected_points_price, $points_decimals));
				$t->parse("points_price_block", false);
			} else {
				$t->set_var("points_price_block", "");
			}
		}

		// show reward points
		if ($points_system && $reward_points_details) {
			$reward_points = calculate_reward_points($reward_type, $reward_amount, $item_price, $buying_price, $points_conversion_rate, $points_decimals);
			$reward_points += $components_reward_points;

			$product_params["base_reward_points"] = $reward_points;
			if ($reward_type) {
				$t->set_var("reward_points", number_format($reward_points, $points_decimals));
				$t->parse("reward_points_block", false);
			} else {
				$t->set_var("reward_points_block", "");
			}
		}

		// show reward credits
		if ($credit_system && $reward_credits_details && ($reward_credits_users == 0 || ($reward_credits_users == 1 && $user_id))) {
			$reward_credits = calculate_reward_credits($credit_reward_type, $credit_reward_amount, $item_price, $buying_price);
			$reward_credits += $components_reward_credits;

			$product_params["base_reward_credits"] = $reward_credits;
			if ($credit_reward_type) {
				$t->set_var("reward_credits", currency_format($reward_credits));
				$t->parse("reward_credits_block", false);
			} else {
				$t->set_var("reward_credits_block", "");
			}
		}

		$recent_price = 0;
		$product_params["pe"] = 0;
		if ($display_products != 2 || strlen($user_id)) {
			set_quantity_control($quantity_limit, $stock_level, $quantity_control, $min_quantity, $max_quantity, $quantity_increment);

			// calculate recent price
			$recent_price  = calculate_price($price, $is_sales, $sales_price);
			$recent_price += $properties_price;
	  
			$base_price = calculate_price($price, $is_sales, $sales_price);
			$product_params["base_price"] = $base_price;
			if ($is_price_edit) {
				$t->set_var("price_block_class", "priceBlockEdit");
				if ($price > 0) {
					$control_price = number_format($price, 2);
				} else {
					$control_price = "";
				}
				$product_params["pe"] = 1;
				$t->set_var("price", $control_price);
				$t->set_var("price_control", "<input name=\"price\" type=\"text\" class=\"price\" value=\"" . $control_price . "\">");
				$t->sparse("price_block", false);
				$t->set_var("sales", "");
				$t->set_var("save", "");
			} elseif ($sales_price != $price && $is_sales) {
				$discount_percent = round($db->f("discount_percent"), 0);
				if (!$discount_percent && $price > 0) 
					$discount_percent = round(($price - $sales_price) / ($price / 100), 0);
	  
				$t->set_var("discount_percent", $discount_percent);

				set_tax_price($item_id, $item_type_id, $price, $sales_price + $selected_price, $tax_free, "price", "sales_price", "tax_sales", true, $components_price, $components_tax_price);
	  
				$product_params["pe"] = 0;
				$t->sparse("price_block", false);
				$t->sparse("sales", false);
				$t->sparse("save", false);
			}
			else
			{
				set_tax_price($item_id, $item_type_id, $price + $selected_price, 0, $tax_free, "price", "", "tax_price", true, $components_price, $components_tax_price);

				$product_params["pe"] = 0;
				$t->sparse("price_block", false);
				$t->set_var("sales", "");
				$t->set_var("save", "");
			}
	  
			$buy_link = $db->f("buy_link");
			if ($buy_link) {
				$t->set_var("buy_href", $db->f("buy_link") . $db->f("affiliate_code"));
			} elseif ($is_properties || $quantity_control == "LISTBOX" || $quantity_control == "TEXTBOX" || $is_price_edit) {
				$t->set_var("buy_href", "javascript:document.form_" . $item_id . ".submit();");
				$t->set_var("wishlist_href", "javascript:document.form_" . $item_id . ".submit();");
			} else {
				$t->set_var("buy_href", $cart_link . "cart=ADD&item_id=" . $item_id . "&rp=" . urlencode($page));
				$t->set_var("wishlist_href", $cart_link . "cart=WISHLIST&item_id=" . $item_id . "&rp=" . urlencode($page));
			}
	  
			// parse 'add to cart' button
			if ($hide_add_details || $shop_hide_add_details) {
				$t->set_var("add_button", "");
				$t->set_var("add_button_disabled", "");
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
			if (!$shop_hide_view_details) {
				$t->sparse("view_button", false);
			}
			if (!$shop_hide_checkout_details && is_array($shopping_cart)) {
				$t->sparse("checkout_button", false);
			}
			if ($user_id && !$buy_link && !$shop_hide_wishlist_details) {
				$t->sparse("wishlist_button", false);
			}
		}
		set_product_params($product_params);

		$tabs = array();

		// parse description block
		$parse_description = false;
		if (!$use_tabs) {
			$t->sparse("title_desc", false);
		}

		if ($full_description) {
			if ($php_in_full_desc) {
				eval_php_code($full_description);
			}
			$t->set_var("full_description", $full_description);
			$t->parse("description", false);
			$parse_description = true;
		} else {
			$t->set_var("description", "");
		}

		if (strlen($notes)) {
			if ($php_in_notes) {
				eval_php_code($notes);
			}
			$t->set_var("notes", $notes);
			$t->parse("notes_block", false);
			$parse_description = true;
		}

		if (!$hide_weight_details && $weight > 0) {
			if (strpos ($weight, ".") !== false) {
				while (substr($weight, strlen($weight) - 1) == "0") {
					$weight = substr($weight, 0, strlen($weight) - 1);
				}
			}
			if (substr($weight, strlen($weight) - 1) == ".") {
				$weight = substr($weight, 0, strlen($weight) - 1);
			}
			$t->set_var("weight", $weight . " " . $weight_measure);
			$t->sparse("weight_block", false);
			$parse_description = true;
		}
		if ($parse_description) {
			$tabs["desc"] = PROD_DESCRIPTION_MSG;
		}
		// end description block
		

		// specification details
		$t->set_var("specification", "");
		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "features WHERE item_id=" . intval($item_id);
		$db->query($sql);
		$db->next_record();
		$total_spec = $db->f(0);
		if ($total_spec > 0) {
			$tabs["spec"] = PROD_SPECIFICATION_MSG;
			if (!$use_tabs) {
				$t->sparse("title_spec", false);
			}

			$sql  = " SELECT fg.group_id,fg.group_name,f.feature_name,f.feature_value ";
			$sql .= " FROM " . $table_prefix . "features f, " . $table_prefix . "features_groups fg ";
			$sql .= " WHERE f.group_id=fg.group_id ";
			$sql .= " AND f.item_id=" . intval($item_id);
			$sql .= " ORDER BY fg.group_order, f.feature_id ";
			$db->query($sql);
			if ($db->next_record()) {
				$last_group_id = $db->f("group_id");
				do {
					$group_id = $db->f("group_id");
					$feature_name = get_translation($db->f("feature_name"));
					$feature_value = get_translation($db->f("feature_value"));
					if ($group_id != $last_group_id) {
						$t->set_var("group_name", $last_group_name);
						$t->parse("groups", true);
						$t->set_var("features", "");
					}
      
					$t->set_var("feature_name", $feature_name);
					$t->set_var("feature_value", $feature_value);
					$t->parse("features", true);
      
					$last_group_id = $group_id;
					$last_group_name = get_translation($db->f("group_name"));
				} while ($db->next_record());
				$t->set_var("group_name", $last_group_name);
				$t->parse("groups", true);
			} 
		}
		// end specification

		// item previews 
		$previews = new VA_Previews();
		$previews->item_id          = $item_id;
		$previews->preview_type     = array(1,2);
		$previews->preview_position = 2;
		$previews->showAll("product_previews_under_large");
		$previews->preview_position = 1;
		$total_previews = $previews->showAll("product_previews_tab");

		if ($total_previews ) {
			$tabs["previews"] = PROD_PREVIEWS_MSG;
			if (!$use_tabs) {
				$t->sparse("title_previews", false);
			}
		}
		
		// product images 
		$t->set_var("images", "");

		$image_number = 0;
		$image_section_number = 0;
		$image_below_large = 0;
		$images_section_cols = 5;
		$images_below_cols = 5;
		$sql  = " SELECT image_id, image_position, image_title, image_small, image_large, image_super, image_description  ";
		$sql .= " FROM " . $table_prefix . "items_images ";
		$sql .= " WHERE item_id=" . intval($item_id);
		$db->query($sql);
		while ($db->next_record()) {
			$image_number++;
	  
			$image_id = $db->f("image_id");
			$image_position = $db->f("image_position");
			$image_title = get_translation($db->f("image_title"));
			$image_description = get_translation($db->f("image_description"));
			$image_small = $db->f("image_small");
			$image_large = $db->f("image_large");
			$image_super = $db->f("image_super");
			$image_small_size  = ""; $image_small_width = 0;
			if (!preg_match("/^http(s)?:\/\//", $image_small)) {
				$image_small_size = @getimagesize($image_small);
				if (is_array($image_small_size)) {	
					$image_small_width = $image_small_size[0];
					$image_small_size = $image_small_size[3];
				}
			}
			// check what section use to parse image
			if ($image_position == 1) {
				$image_name = "rollover_image";
				$super_id = "rollover_super";
				$image_section_number++;
			} else {
				$image_name = "image_" . $form_id;
				$super_id = "super_" . $form_id;
				$image_below_large++;
			}
			// check possible columns number
			if ($image_section_number == 1) {
				if ($image_section_number == 1 && $image_small_width && !preg_match("/^http(s)?:\/\//", $image_large)) {
					$image_large_size = @getimagesize($image_large);
					if (is_array($image_large_size)) {	
						$image_large_width = $image_large_size[0];
						$images_section_cols = intval($image_large_width / $image_small_width);
						if ($images_section_cols < 2) { $images_section_cols = 2; }
					}
				}
			} else if ($image_section_number == 2) {
				// images below main image
				if ($image_below_large == 1) {
					if ($product_image_width && $image_small_width) {
						$images_below_cols = intval($product_image_width / $image_small_width);
						if ($images_below_cols < 2) { $images_below_cols = 2; }
					}
				}
			}


			if ($restrict_products_images || $watermark_small_image) { 
				if ($image_small && !preg_match("/^http(s)?:\/\//", $image_small)) {
					$image_small = "image_show.php?image_id=".$image_id."&type=small&vc=".md5($image_small); 
				}
			}
			if ($restrict_products_images || $watermark_big_image) { 
				if ($image_large && !preg_match("/^http(s)?:\/\//", $image_large)) {
					$image_large = "image_show.php?image_id=".$image_id."&type=large&vc=".md5($image_large); 
				}
			}
			if ($restrict_products_images || $watermark_super_image) { 
				if ($image_super && !preg_match("/^http(s)?:\/\//", $image_super)) {
					$image_super = "image_show.php?image_id=".$image_id."&type=super&vc=".md5($image_super); 
				}
			}
			if (!strlen($image_large)) {
				$image_large = $image_small;
			}
			$rollover_js = ""; $image_click_js = "";
    
			$t->set_var("image_id", $image_id);
			$t->set_var("image_title", $image_title);
			$t->set_var("image_alt", htmlspecialchars($image_title));
			$t->set_var("image_small", $image_small);
			$t->set_var("image_size",  $image_small_size);
			$t->set_var("image_large", $image_large);
			if ($image_super) {
				$t->set_var("image_super", $image_super);
			} else {
				$t->set_var("image_large", $image_large);
			}

			$t->set_var("image_description", $image_description);

			$rollover_js = "rolloverImage(".$image_id.", '".$image_large."', '".$image_name."', '".$super_id."', '".$image_super."'); return false;";
			$image_click_js = $rollover_js;
			if ($image_section_number == 1) {
				$t->set_var("rollover_image", $image_large);
				$t->set_var("rollover_super_src", $image_super);
				if ($open_large_image) {
					$rollover_super_click = "popupImage(this); return false;";
				} else {
					$rollover_super_click  = "openImage(this); return false;";
				}
				$t->set_var("rollover_super_click", $rollover_super_click);
				if (!$image_super) {
					$t->set_var("rollover_super_style", "display: none;");
				}
			}

			if ($image_super) {
				$image_click_js = ($open_large_image) ? "popupImage(this); return false;" : "openSuperImage(this); return false;	";
			} else {
				$image_click_js = "return false;";
			}

			$t->set_var("rollover_js", $rollover_js);
			$t->set_var("image_click_js", $image_click_js);

			if ($image_position == 1) {
				$t->parse("images_cols", true);
				if ($image_section_number % $images_section_cols == 0) {
					$t->parse("images_rows", true);
					$t->set_var("images_cols", "");
				}
			} else {
				$t->parse("main_images_cols", true);
				if ($image_below_large % $images_below_cols == 0) {
					$t->parse("main_images_rows", true);
					$t->set_var("main_images_cols", "");
				}
			}
		}	    
		// parse row if columns left  
		if ($image_section_number && $image_section_number % $images_section_cols != 0) {
			$t->parse("images_rows", true);
		}
		if ($image_below_large && $image_below_large % $images_below_cols != 0) {
			$t->parse("main_images_rows", true);
		}

		if ($image_section_number) {
			$tabs["images"] = PROD_IMAGES_MSG;
			if (!$use_tabs) {
				$t->sparse("title_images", false);
			}
		}
		if ($image_below_large) {
			$t->parse("main_images", false);
		}
		// end images


		// product accessories
		$t->set_var("accessories_block", "");
		$sql_params = array();
		$sql_params["brackets"] = "("; 
		$sql_params["join"]   = " INNER JOIN " . $table_prefix . "items_accessories ia ON i.item_id=ia.accessory_id) ";	
		$sql_params["where"]  = " ia.item_id=" . $db->tosql($item_id, INTEGER);		
		$accessories_ids   = VA_Products::find_all_ids($sql_params, VIEW_CATEGORIES_ITEMS_PERM);
		$total_accessories = 0;
		if ($accessories_ids) {
			$total_accessories = count($accessories_ids);
			$allowed_accessories_ids = VA_Products::find_all_ids("i.item_id IN (" . $db->tosql($accessories_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);
			
			$tabs["accessories"] = PROD_ACCESSORIES_MSG;
			if (!$use_tabs) {
				$t->sparse("title_accessories", false);
			}

			$accessory_number = 0;
			$sql  = " SELECT i.item_id, i.item_type_id, i.item_name, i.friendly_url, i.short_description, ";
			$sql .= " i.buying_price, i." . $price_field . ", i.".$properties_field.", i." . $sales_field . ", i.is_sales, i.tax_free ";
			$sql .= " FROM ((" . $table_prefix . "items i ";
			$sql .= " INNER JOIN " . $table_prefix . "items_accessories ia ON i.item_id=ia.accessory_id)";
			$sql .= " LEFT JOIN " . $table_prefix . "manufacturers m ON i.manufacturer_id=m.manufacturer_id) ";
			$sql .= " WHERE ia.item_id=" . $db->tosql($item_id, INTEGER);
			$sql .= " AND i.item_id IN (" . $db->tosql($accessories_ids, INTEGERS_LIST) . ")";
			$sql .= " ORDER BY ia.accessory_order ";
			$db->query($sql);
			while ($db->next_record()) {
				$accessory_number++;
				$accessory_id = $db->f("item_id");
				$accessory_type_id = $db->f("item_type_id");
				$accessory_name = get_translation($db->f("item_name"));
				$accessory_friendly_url = $db->f("friendly_url");
				$accessory_description = get_translation($db->f("short_description"));
				$buy_accessory_href = $page . "&rnd=" . $random_value . "&cart=ADD&accessory_id=" . $accessory_id;
				if ($friendly_urls && $accessory_friendly_url) {
					$t->set_var("accessory_details_url", $accessory_friendly_url . $friendly_extension);
				} else {
					$t->set_var("accessory_details_url", get_custom_friendly_url("product_details.php") . "?item_id=" . $accessory_id);
				}

				$price = $db->f($price_field);
				$buying_price = $db->f("buying_price");
				$sales_price = $db->f($sales_field);
				$is_sales = $db->f("is_sales");
				
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
				$accessory_tax_free = $db->f("tax_free");
				if ($user_tax_free) { $accessory_tax_free = $user_tax_free; }
				$accessory_price = calculate_price($price, $is_sales, $sales_price);
				if ($user_price_action != 1) {
					if ($discount_type == 1 || $discount_type == 3) {
						$accessory_price -= round(($accessory_price * $discount_amount) / 100, 2);
					} elseif ($discount_type == 2) {
						$accessory_price -= round($discount_amount, 2);
					} elseif ($discount_type == 4) {
						$accessory_price -= round((($accessory_price - $buying_price) * $discount_amount) / 100, 2);
					}
				}
				// add properties and components prices
				$accessory_price += $properties_price;

				set_tax_price($accessory_id, $accessory_type_id, $accessory_price, 0, $accessory_tax_free, "accessory_price", "", "accessory_tax_price");
				
				$t->set_var("accessory_id", $accessory_id);
				$t->set_var("accessory_name", $accessory_name);
				$t->set_var("accessory_description", $accessory_description);
				if ($display_products != 2 || strlen($user_id)) {
					$t->set_var("buy_accessory_href", $buy_accessory_href);
					$t->sparse("accessory_price_block", false);
				}
				if (!$allowed_accessories_ids || !in_array($accessory_id, $allowed_accessories_ids)) {
					$t->set_var("restricted_class", " restrictedItem");
					$t->sparse("restricted_image", false);
				} else {
					$t->set_var("restricted_class", "");
					$t->set_var("restricted_image", "");
				}
	    
				$t->parse("accessories_cols", true);
				if ($accessory_number % 2 == 0) {
					$t->parse("accessories_rows", true);
					$t->set_var("accessories_cols", "");
				}
			} while ($db->next_record());

			if ($accessory_number % 2 != 0) {
				$t->parse("accessories_rows", true);
			}
		}


		$t->set_var("reviews", "");
		if ($reviews_allowed_view == 1 || ($reviews_allowed_view == 2 && strlen($user_id))
			|| $reviews_allowed_post == 1 || ($reviews_allowed_post == 2 && strlen($user_id))) {
			$tabs["reviews"] = REVIEWS_MSG;
			if (!$use_tabs) {
				$t->sparse("title_reviews", false);
			}
			// count reviews
			$sql = " SELECT COUNT(*) FROM " . $table_prefix . "reviews WHERE approved=1 AND item_id=" . $db->tosql($item_id, INTEGER);
			$total_votes = get_db_value($sql);
			
			if ($total_votes)
			{
				// parse summary statistic
				$t->set_var("total_votes", $total_votes);
				$sql = " SELECT COUNT(*) FROM " . $table_prefix . "reviews WHERE approved=1 AND rating <> 0 AND item_id=" . $db->tosql($item_id, INTEGER);
				$total_rating_votes = get_db_value($sql);
	    
				$average_rating_float = 0;
				if ($total_rating_votes)
				{
					$sql = " SELECT SUM(rating) FROM " . $table_prefix . "reviews WHERE approved=1 AND rating <> 0 AND item_id=" . $db->tosql($item_id, INTEGER);
					$average_rating_float = round(get_db_value($sql) / $total_rating_votes, 2);
				}
				$average_rating = round($average_rating_float, 0);
				$average_rating_image = $average_rating ? "rating-" . $average_rating : "not-rated";
				$t->set_var("average_rating_image", $average_rating_image);
				$t->set_var("average_rating_alt", $average_rating_float);
	    
				$based_on_message = str_replace("{total_votes}", $total_votes, BASED_ON_REVIEWS_MSG);
				$t->set_var("BASED_ON_REVIEWS_MSG", $based_on_message);
				$t->parse("summary_statistic", false);
	    
				$is_reviews = false;
				// show last positive and negative reviews only if it allowed to see them
				if ($reviews_allowed_view == 1 || ($reviews_allowed_view == 2 && strlen($user_id))) {
					$sql  = " SELECT * FROM " . $table_prefix . "reviews ";
					$sql .= " WHERE recommended=1 AND approved=1 AND comments IS NOT NULL ";
					$sql .= " AND item_id=" . $db->tosql($item_id, INTEGER);
					$sql .= " ORDER BY date_added DESC";  
					$db->RecordsPerPage = 1;
					$db->PageNumber = 1;
					$db->query($sql);
					if ($db->next_record())
					{
						$is_reviews = true;
						do {
							$review_user_id = $db->f("user_id");
							$review_user_name = htmlspecialchars($db->f("user_name"));
							if (!$review_user_id) {
								$review_user_name .= " (" . GUEST_MSG . ")";
							}
							$review_user_class = $review_user_id ? "forumUser" : "forumGuest";
							$rating = round($db->f("rating"), 0);
							$rating_image = $rating ? "rating-" . $rating : "not-rated";
							$t->set_var("rating_image", $rating_image);
							$t->set_var("review_user_class", $review_user_class);
							$t->set_var("review_user_name", $review_user_name);
							$date_added = $db->f("date_added", DATETIME);
							$date_added_string = va_date($datetime_show_format, $date_added);
							$t->set_var("review_date_added", $date_added_string);
							$t->set_var("review_summary", htmlspecialchars($db->f("summary")));
							$t->set_var("review_comments", nl2br(htmlspecialchars($db->f("comments"))));
							$t->parse("positive_review", true);
						} while ($db->next_record());
					}
	      
					$sql  = " SELECT * FROM " . $table_prefix . "reviews ";
					$sql .= " WHERE recommended=-1 AND approved=1 AND comments IS NOT NULL ";
					$sql .= " AND item_id=" . $db->tosql($item_id, INTEGER);
					$sql .= " ORDER BY date_added DESC";  
					$db->RecordsPerPage = 1;
					$db->PageNumber = 1;
					$db->query($sql);
					if ($db->next_record())
					{
						$is_reviews = true;
						do {
							$review_user_id = $db->f("user_id");
							$review_user_name = htmlspecialchars($db->f("user_name"));
							if (!$review_user_id) {
								$review_user_name .= " (" . GUEST_MSG . ")";
							}
							$review_user_class = $review_user_id ? "forumUser" : "forumGuest";
							$rating = round($db->f("rating"), 0);
							$rating_image = $rating ? "rating-" . $rating : "not-rated";
							$t->set_var("rating_image", $rating_image);
							$t->set_var("review_user_class", $review_user_class);
							$t->set_var("review_user_name", $review_user_name);
							$date_added = $db->f("date_added", DATETIME);
							$date_added_string = va_date($datetime_show_format, $date_added);
							$t->set_var("review_date_added", $date_added_string);
							$t->set_var("review_summary", htmlspecialchars($db->f("summary")));
							$t->set_var("review_comments", nl2br(htmlspecialchars($db->f("comments"))));
          
							$t->parse("negative_review", true);
						} while ($db->next_record());
					}
				}
	    
				if ($is_reviews) {
					$t->set_var("SEE_ALL_REVIEWS_MSG",  SEE_ALL_REVIEWS_MSG);
					$t->parse("all_reviews_link", false);
				}
	    
			}
			else
			{
				$t->parse("not_rated", false);
			}
		}
		
		// ** EGGHEAD VENTURES ADD **
		$tabs["willfit"] = "Applications";
		// ** END **
		
		// parse tabs
		$tab = get_param("tab");
		if (!strlen($tab) && count($tabs) > 0) { 
			$tab_keys = array_keys($tabs);
			$tab = $tab_keys[0]; 
		}
		$t->set_var("tab", htmlspecialchars($tab));

		if ($use_tabs) {
			if ($friendly_urls && $friendly_url) {
				$tab_transfer_query = transfer_params(array("item_id"), false);
				$tab_href = $friendly_url . $friendly_extension . $tab_transfer_query;
			} else {
				$tab_href = get_custom_friendly_url("product_details.php") . $transfer_query;
			}
			if (strrpos($tab_href, "?")) {
				$tab_href .= "&tab=";
			} else {
				$tab_href .= "?tab=";
			}

			foreach ($tabs as $tab_name => $tab_title) {
				if ($tab == $tab_name) {
					$tab_style = "tabActive";
					$data_style = "display: block;";
				} else {
					$tab_style = "tab";
					$data_style = "display: none;";
				}
				$t->set_var("tab_id", $tab_name . "_tab");
				$t->set_var("tab_td_id", $tab_name . "_td_tab");
				$t->set_var("tab_a_id", $tab_name . "_a_tab");
				$t->set_var("tab_name", $tab_name);
				$t->set_var("tab_title", $tab_title);
				$t->set_var("tab_href", $tab_href . $tab_name);
				$t->set_var("tab_style", $tab_style);
				$t->set_var($tab_name . "_style", $data_style);

				$t->parse("tabs", true);
			}
			$t->parse("tabs_block", false);
		} else {
			$t->set_var("tabs_block", "");
		}
		
		// parse all sections/tabs
		if ($parse_description) {
			$t->sparse("description_block", false);
		}
		if ($total_spec > 0) {
			$t->sparse("specification_block", false);
		}
		if ($image_section_number) {
			$t->sparse("images_block", false);
		}
		if ($total_previews) {
			$t->sparse("previews_block", false);
		}		
		if ($total_accessories > 0) {
			$t->sparse("accessories_block", false);
		}
		if ($reviews_allowed_view == 1 || ($reviews_allowed_view == 2 && strlen($user_id)) 
			|| $reviews_allowed_post == 1 || ($reviews_allowed_post == 2 && strlen($user_id))) {
			$t->sparse("reviews_block", false);
		}

		// update total views for product
		$products_viewed = get_session("session_products_viewed");
		if (!isset($products_viewed[$item_id])) {
			$sql  = " UPDATE " . $table_prefix . "items SET total_views=" . $db->tosql(($total_views + 1), INTEGER);
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
			$db->query($sql);

			$products_viewed[$item_id] = true;
			set_session("session_products_viewed", $products_viewed);
		}


		// fill in recently viewed products
		$recent_records = get_setting_value($page_settings, "recent_records", 10);
		$recently_viewed = get_session("session_recently_viewed");
		if (!is_array($recently_viewed)) {
			$recently_viewed = array();
		} 
		$recent_index = 0;
		foreach ($recently_viewed as $key => $recent_info) {
			if ($recently_viewed[$key][0] == $item_id) {
				unset($recently_viewed[$key]);
			} else {
				$recent_index++;
				if ($recent_index >= $recent_records) {
					unset($recently_viewed[$key]);
				}
			}
		}
		$t->parse("links_block");
		$t->parse("item");
		$t->set_var("no_item", "");
		$recent_info = array($item_id, $item_type_id, $item_name_initial, $friendly_url, $recent_price, $buying_price, $item_tax_free, $is_compared);
		array_unshift($recently_viewed, $recent_info);
		set_session("session_recently_viewed", $recently_viewed);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>