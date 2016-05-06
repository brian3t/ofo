<?php

function related_products($block_name, $related_type = "items_related", $page_friendly_url = "", $page_friendly_params = array())
{
	global $t, $db, $db_type, $table_prefix;
	global $settings, $page_settings;
	global $language_code, $currency;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}
	
	$user_id      = get_session("session_user_id");	
	$user_info    = get_session("session_user_info");
	
	$discount_type   = get_session("session_discount_type");
	$discount_amount = get_session("session_discount_amount");	
	$price_type      = get_session("session_price_type");

	$user_tax_free = get_setting_value($user_info, "tax_free", 0);
	
	$friendly_urls      = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$display_products   = get_setting_value($settings, "display_products", 0);
	
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	$product_no_image         = get_setting_value($settings, "product_no_image", "");
	$watermark                = get_setting_value($settings, "watermark_small_image", 0);
	$image_type_name          = "small";
	
	$php_in_short_desc = get_setting_value($settings, "php_in_products_short_desc", 0);

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
			
	$item_id     = get_param("item_id");
	$article_id  = get_param("article_id");
	$thread_id   = get_param("thread_id");	
	$category_id = get_param("category_id");
	
	$related_type_join  = "";
	$related_type_where = "";
	$related_type_order = "";
	if ($related_type == "product_related") {
		$related_type_join  = " LEFT JOIN " . $table_prefix . "items_related rel";
		$related_type_join .= " ON i.item_id=rel.related_id)";
		$related_type_where = " rel.item_id=" . $db->tosql($item_id, INTEGER);
		$related_type_order = " ORDER BY rel.related_order, i.item_id ";
		
		$t->set_var("related_products_title", RELATED_PRODUCTS_TITLE);
		$product_page = "product_details.php";
		
		$records_per_page = get_setting_value($page_settings, "related_per_page", 10);
		$related_columns_param = "related_columns";
	} elseif ($related_type == "items_forum_topics") {
		$related_type_join  = " LEFT JOIN " . $table_prefix . "items_forum_topics rel";
		$related_type_join .= " ON i.item_id=rel.item_id)";
		$related_type_where = " rel.thread_id=" . $db->tosql($thread_id, INTEGER);
		$related_type_order = " ORDER BY rel.item_order, i.item_id ";
		
		$t->set_var("related_products_title", RELATED_PRODUCTS_TITLE);
		$product_page = "forum_topic.php";
		
		$records_per_page = get_setting_value($page_settings, "related_per_page", 10);
		$related_columns_param = "related_columns";
	} elseif ($related_type == "article_items_related") {
		$related_type_join  = " LEFT JOIN " . $table_prefix . "articles_items_related rel";
		$related_type_join .= " ON i.item_id=rel.item_id)";
		$related_type_where = " rel.article_id=" . $db->tosql($article_id, INTEGER);
		$related_type_order = " ORDER BY rel.article_order, i.item_id ";
		
		$t->set_var("related_products_title", ARTICLE_RELATED_PRODUCTS_TITLE);		
		$product_page = "article.php";								
		$sql  = " SELECT ac.category_path, ac.category_id FROM " . $table_prefix . "articles_categories ac ";
		$sql .= " INNER JOIN " . $table_prefix . "articles_assigned aas ON aas.category_id=ac.category_id ";
		$sql .= " WHERE aas.article_id=" . $db->tosql($article_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$category_id   = $db->f("category_id");
			$category_path = $db->f("category_path");
			if ("0," == $category_path) {
				$top_category_id = $category_id;
			} else {
				$category_path_parts = explode(",", $category_path);
				if (isset($category_path_parts[1])) {
					$top_category_id = $category_path_parts[1];
				} else {
					$top_category_id = $category_id;
				}
			}
		} else {
			$top_category_id = "0";
		}
			
		$records_per_page      = get_setting_value($page_settings, "a_item_related_recs_" . $top_category_id, 5);
		$related_columns_param = "a_item_related_cols_" . $top_category_id;
		
	} elseif (($related_type == "article_category_items_related") || ($related_type == "articles_category_items_related")) {
		$related_type_join  = " LEFT JOIN " . $table_prefix . "articles_categories_items rel";
		$related_type_join .= " ON i.item_id=rel.item_id)";		
		$related_type_where = " rel.category_id=" . $db->tosql($category_id, INTEGER);
		$related_type_order = " ORDER BY rel.related_order, i.item_id ";
		
		$t->set_var("related_products_title", CATEGORY_RELATED_PRODUCTS_TITLE);
		
		if ($related_type == "article_category_items_related") {
			$product_page = "article.php";			
			$sql = "SELECT ac.category_path, ac.category_id FROM " . $table_prefix . "articles_categories ac ";
			$sql .= " INNER JOIN " . $table_prefix . "articles_assigned aas ON aas.category_id=ac.category_id ";
			$sql .= " WHERE aas.article_id=" . $db->tosql($article_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$category_id = $db->f("category_id");
				$category_path = $db->f("category_path");
				if ("0," == $category_path) {
					$top_category_id = $category_id;
				} else {
					$category_path_parts = explode(",", $category_path);
					if (isset($category_path_parts[1])) {
						$top_category_id = $category_path_parts[1];
					} else {
						$top_category_id = $category_id;
					}
				}
			} else {
				$top_category_id = "0";
			}
		} else {
			$product_page = "articles.php";			
			$sql = "SELECT category_path FROM " . $table_prefix . "articles_categories WHERE category_id=" . $db->tosql($category_id, INTEGER);
			$category_path = get_db_value($sql);
			if ("0," == $category_path) {
				$top_category_id = $category_id;
			} else {
				$category_path_parts = explode(",", $category_path);
				if (isset($category_path_parts[1])) {
					$top_category_id = $category_path_parts[1];
				} else {
					$top_category_id = $category_id;
				}
			}
		}		
		
		$records_per_page      = get_setting_value($page_settings, "a_cat_item_related_recs_" . $top_category_id, 5);
		$related_columns_param = "a_cat_item_related_cols_" . $top_category_id;
	}
	
	
	$t->set_file("block_body", "block_related_products.html");
	$t->set_var("product_details_href", "product_details.php");
	
	if ($friendly_urls && $page_friendly_url) {
		$pass_parameters = get_transfer_params($page_friendly_params);
		$main_page = $page_friendly_url . $friendly_extension;
	} else {
		$pass_parameters = get_transfer_params();
		$main_page = get_custom_friendly_url($product_page);
	}
	
	
	$sql_params = array();
	$sql_params["brackets"] = "("; 
	$sql_params["join"]   = $related_type_join;
	$sql_params["where"]  = $related_type_where;
	
	$items_ids = VA_Products::find_all_ids($sql_params, VIEW_CATEGORIES_ITEMS_PERM);
	if(!$items_ids) return;	
	$total_records = count($items_ids);
	
	$allowed_items_ids = VA_Products::find_all_ids("i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);

	$pages_number = 5;
	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $main_page);
	$page_number = $n->set_navigator("ri_navigator", "ri_page", SIMPLE, $pages_number, $records_per_page, $total_records, false, $pass_parameters, array(), "#related-products");
	
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber     = $page_number;
		
	$sql  = " SELECT i.item_id, i.item_type_id, i.item_name, i.friendly_url, i.short_description, i.small_image, i.small_image_alt, ";
	$sql .= " i.buying_price, i." . $price_field . ", i.".$properties_field.", i." . $sales_field . ", i.is_sales, i.tax_free, ";
	$sql .= " i.use_stock_level, i.stock_level, st_in.shipping_time_desc AS in_stock_message, st_out.shipping_time_desc AS out_stock_message ";
	$sql .= $new_product_sql;		
	$sql .= " FROM (((";
	$sql .= $table_prefix . "items i ";
	$sql .= $related_type_join;
	$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_in ON i.shipping_in_stock=st_in.shipping_time_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_out ON i.shipping_out_stock=st_out.shipping_time_id) ";
	$sql .= " WHERE i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")";
	$sql .= " AND " . $related_type_where;
	$sql .= $related_type_order;
	$db->query($sql);
		
	$t->set_var("ri_rows", "");
	$ri_columns = get_setting_value($page_settings, $related_columns_param, 1);
	$t->set_var("ri_column", (100 / $ri_columns) . "%");
	$ri_number = 0;
		
	while ($db->next_record())
	{
		$ri_number++;
		$item_id      = $db->f("item_id");
		$item_type_id = $db->f("item_type_id");
		$item_name    = get_translation($db->f("item_name"));
		$friendly_url = $db->f("friendly_url");
		$short_description = get_translation($db->f("short_description"));
		if ($php_in_short_desc) {
			eval_php_code($short_description);
		}
		if ($friendly_urls && $friendly_url) {
			$details_url = $friendly_url . $friendly_extension;
		} else {
			$details_url = "product_details.php?item_id=".urlencode($item_id);
		}

		$t->set_var("item_id", $item_id);
		$t->set_var("item_name", $item_name);
		$t->set_var("details_url", $details_url);
		$t->set_var("short_description", $short_description);
		$t->set_var("ri_tax_price", "");
		$t->set_var("ri_tax_sales", "");

		$stock_level = $db->f("stock_level");
		$use_stock_level = $db->f("use_stock_level");
		if (!$use_stock_level || $stock_level > 0) {
			$shipping_time_desc = get_translation($db->f("in_stock_message"));
		} else {
			$shipping_time_desc = get_translation($db->f("out_stock_message"));
		}
		if (strlen($shipping_time_desc)) {
			$t->set_var("shipping_time_desc", $shipping_time_desc);
			$t->parse("ri_availability", false);
		} else {
			$t->set_var("ri_availability", "");
		}

		if ($display_products != 2 || strlen($user_id)) {
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

			$tax_free = $db->f("tax_free");
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
				set_tax_price($item_id, $item_type_id, $price, $sales_price, $tax_free, "ri_price", "ri_sales_price", "ri_tax_sales", false);

				$t->sparse("ri_price_block", false);
				$t->sparse("ri_sales", false);
			} else {
				set_tax_price($item_id, $item_type_id, $price, 0, $tax_free, "ri_price", "", "ri_tax_price", false);

				$t->sparse("ri_price_block", false);
				$t->set_var("ri_sales", "");
			}
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
		} else {
			$t->set_var("restricted_class", "");
			$t->set_var("restricted_image", "");
		}
		
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
			if (is_array($image_size)){
				$t->set_var("width", "width=\"" . $image_size[0] . "\"");
				$t->set_var("height", "height=\"" . $image_size[1] . "\"");
			} else {
				$t->set_var("width", "");
				$t->set_var("height", "");
			}
			$t->parse("ri_small_image", false);
		}
		else
		{
			$t->set_var("ri_small_image", "");
		}

		$t->parse("ri_cols");
		if ($ri_number % $ri_columns == 0)
		{
			$t->parse("ri_rows");
			$t->set_var("ri_cols", "");
		}

	}

	if ($ri_number % $ri_columns != 0) {
		$t->parse("ri_rows");
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>