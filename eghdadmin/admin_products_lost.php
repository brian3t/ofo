<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_products_lost.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/shopping_cart.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");

	check_admin_security("products_categories");
	
	$permissions = get_permissions();
	$product_prices = get_setting_value($permissions, "product_prices", 0);
	$product_images = get_setting_value($permissions, "product_images", 0);
	$product_properties = get_setting_value($permissions, "product_properties", 0);
	$product_features = get_setting_value($permissions, "product_features", 0);
	$product_related = get_setting_value($permissions, "product_related", 0);
	$product_categories = get_setting_value($permissions, "product_categories", 0);
	$product_accessories = get_setting_value($permissions, "product_accessories", 0);
	$product_releases = get_setting_value($permissions, "product_releases", 0);
	
	$update_products = get_setting_value($permissions, "update_products", 0);
	$remove_products = get_setting_value($permissions, "remove_products", 0);
	$approve_products = get_setting_value($permissions, "approve_products", 0);
	$view_only_products = !$update_products && $view_products;
	$read_only_products = !$update_products && !$view_products;
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_products_lost.html");
	$t->set_var("admin_products_href", "admin_items_list.php");
	$t->set_var("admin_products_lost_href", "admin_products_lost.php");
	
	$parent_category_id = get_param("parent_category_id");
	$operation          = get_param("operation");
	$products_ids       = get_param("products_ids");
	if ($operation == "delete") {
		if ($remove_products && strlen($products_ids)) {
			delete_products($products_ids);
		}	
	} elseif ($operation == "move") {
		if ($products_ids && $parent_category_id) {
			$products_ids = explode(",", $products_ids);
			$sql  = " SELECT MAX(item_order) FROM ". $table_prefix . "items_categories";
			$sql .= " WHERE category_id=" . $db->tosql($parent_category_id, INTEGER);			
			$product_order = get_db_value($sql);
			foreach ($products_ids AS $product_id) {
				$product_order++;
				$sql  = " INSERT INTO " . $table_prefix . "items_categories";
				$sql .= " (item_id, category_id, item_order) VALUES (";
				$sql .= $db->tosql($product_id, INTEGER) . ",";
				$sql .= $db->tosql($parent_category_id, INTEGER) . ",";
				$sql .= $db->tosql($product_order, INTEGER) . ")";
				$db->query($sql);				
			}
		}
	}
	
	
	//BEGIN product privileges changes
	$set_delimiter = false;
	if ($product_prices) {
		$set_delimiter = true;
	}
	if ($product_images && $set_delimiter) {
		$t->set_var("product_images_delimiter", " | ");
	} elseif ($product_images) {
		$set_delimiter = true;
	}
	if ($product_properties && $set_delimiter) {
		$t->set_var("product_properties_delimiter", " | ");
	} elseif ($product_properties) {
		$set_delimiter = true;
	}
	if ($product_features && $set_delimiter) {
		$t->set_var("product_features_delimiter", " | ");
	} elseif ($product_features) {
		$set_delimiter = true;
	}
	if ($product_related && $set_delimiter) {
		$t->set_var("product_related_delimiter", " | ");
	} elseif ($product_related) {
		$set_delimiter = true;
	}
	if ($product_categories && $set_delimiter) {
		$t->set_var("product_categories_delimiter", " | ");
	} elseif ($product_categories) {
		$set_delimiter = true;
	}
	if ($product_accessories && $set_delimiter) {
		$t->set_var("product_accessories_delimiter", " | ");
	} elseif ($product_accessories) {
		$set_delimiter = true;
	}
	if ($product_releases && $set_delimiter) {
		$t->set_var("product_releases_delimiter", " | ");
	}
	//END product privileges changes
	
	$t->set_var("admin_layout_page_href",      "admin_layout_page.php");
	$t->set_var("admin_reviews_href",          "admin_reviews.php");
	$t->set_var("admin_category_edit_href",    "admin_category_edit.php");
	$t->set_var("admin_product_href",          "admin_product.php");
	$t->set_var("admin_properties_href",       "admin_properties.php");
	$t->set_var("admin_releases_href",         "admin_releases.php");
	$t->set_var("admin_item_related_href",     "admin_item_related.php");
	$t->set_var("admin_item_categories_href",  "admin_item_categories.php");
	$t->set_var("admin_category_items_href",  "admin_category_items.php");
	$t->set_var("admin_categories_order_href", "admin_categories_order.php");
	$t->set_var("admin_products_order_href",   "admin_products_order.php");
	$t->set_var("admin_item_types_href",       "admin_item_types.php");
	$t->set_var("admin_features_groups_href",  "admin_features_groups.php");
	$t->set_var("admin_item_prices_href",      "admin_item_prices.php");
	$t->set_var("admin_item_features_href",    "admin_item_features.php");
	$t->set_var("admin_item_images_href",      "admin_item_images.php");
	$t->set_var("admin_item_accessories_href", "admin_item_accessories.php");
	$t->set_var("admin_export_google_base_href", "admin_export_google_base.php");
	$t->set_var("admin_search_href",           "admin_search.php");
	$t->set_var("admin_tell_friend_href",      "admin_tell_friend.php");
	$t->set_var("admin_products_edit_href",  "admin_products_edit.php");
	
	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_products_lost.php");
	$s->set_sorter(ID_MSG, "sorter_item_id", 1, "i.item_id");
	$s->set_sorter(PROD_TITLE_COLUMN, "sorter_item_name", 2, "i.item_name");
	$s->set_sorter(PROD_PRICE_COLUMN, "sorter_price", 3, "i.price");
	$s->set_sorter(PROD_QTY_COLUMN, "sorter_qty", 4, "i.stock_level");
	$s->set_sorter(DATE_ADDED_MSG, "sorter_date_added", 5, "i.date_added");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_products_lost.php");
	
	
	$sql  = " SELECT COUNT(i.item_id) ";
	$sql .= " FROM ((" . $table_prefix . "items i ";
	$sql .= " LEFT OUTER JOIN " . $table_prefix . "items_categories ic ON i.item_id = ic.item_id) ";
	$sql .= " LEFT OUTER JOIN " . $table_prefix . "categories c ON c.category_id = ic.category_id) ";
	$sql .= " WHERE c.category_id IS NULL ";
	$total_records = get_db_value($sql);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	
	$sql  = " SELECT i.item_id,i.item_code, i.manufacturer_code, i.item_name, i.price, i.sales_price, i.is_sales, i.stock_level, i.date_added ";
	$sql .= " FROM ((" . $table_prefix . "items i ";
	$sql .= " LEFT OUTER JOIN " . $table_prefix . "items_categories ic ON i.item_id = ic.item_id) ";
	$sql .= " LEFT OUTER JOIN " . $table_prefix . "categories c ON c.category_id = ic.category_id) ";
	$sql .= " WHERE c.category_id IS NULL ";
	$db->query($sql . $s->order_by);
	$products_ids = array();
	if ($db->next_record()) {
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		$index = 0;
		do {
			$index++;
			$t->set_var("index", $index);
			$t->set_var("row_class", $index % 2 + 1);
			$item_id = $db->f("item_id");
			$products_ids[] = $item_id;
			
			$item_code = $db->f("item_code");
			$manufacturer_code = $db->f("manufacturer_code");
			$item_name = get_translation($db->f("item_name"));
			$price = $db->f("price");
			$is_sales = $db->f("is_sales");
			$sales_price = $db->f("sales_price");
			$stock_level = $db->f("stock_level");
			$item_codes = "";
			if ($item_code && $manufacturer_code) {
				$item_codes = "(" . $item_code . ", " . $manufacturer_code . ")";
			} elseif ($item_code) {
				$item_codes = "(" . $item_code . ")";
			} elseif ($manufacturer_code) {
				$item_codes = "(" . $manufacturer_code . ")";
			}

			$price = calculate_price($price, $is_sales, $sales_price);

			$t->set_var("item_id", $item_id);
			$t->set_var("item_code", htmlspecialchars($item_code));
			$t->set_var("manufacturer_code", htmlspecialchars($manufacturer_code));
			$t->set_var("item_codes", htmlspecialchars($item_codes));

			$item_name = htmlspecialchars($item_name);
			$t->set_var("item_name", $item_name);
			$t->set_var("price", currency_format($price));
			if ($stock_level < 0) {
				$stock_level = "<font color=red>" . $stock_level . "</font>";
			}
			$t->set_var("stock_level", $stock_level);
			
			$date_added = $db->f("date_added");
			if ($date_added) {				
				$date_added = va_date($datetime_show_format, $date_added);
			}
			$t->set_var("date_added", $date_added);
			
			
			// BEGIN product privileges changes
			if ($product_prices) {
				$t->parse("product_prices_priv", false);
			} else {
				$t->set_var("product_prices_priv", "");
			}
			if ($product_images) {
				$t->parse("product_images_priv", false);
			} else {
				$t->set_var("product_images_priv", "");
			}
			if ($product_properties) {
				$t->parse("product_properties_priv", false);
			} else {
				$t->set_var("product_properties_priv", "");
			}
			if ($product_features) {
				$t->parse("product_features_priv", false);
			} else {
				$t->set_var("product_features_priv", "");
			}
			if ($product_related) {
				$t->parse("product_related_priv", false);
			} else {
				$t->set_var("product_related_priv", "");
			}
			if ($product_categories) {
				$t->parse("product_categories_priv", false);
			} else {
				$t->set_var("product_categories_priv", "");
			}
			if ($product_accessories) {
				$t->parse("product_accessories_priv", false);
			} else {
				$t->set_var("product_accessories_priv", "");
			}
			if ($product_releases) {
				$t->parse("product_releases_priv", false);
			} else {
				$t->set_var("product_releases_priv", "");
			}
			if ($read_only_products) {
				$t->parse("read_only_products_priv", false);
				$t->set_var("update_products_priv", "");
			} elseif ($view_only_products) {
				$t->set_var("product_edit_msg", VIEW_MSG);
				$t->parse("update_products_priv", false);
				$t->set_var("read_only_products_priv", "");
			} else {
				$t->set_var("product_edit_msg", EDIT_MSG);
				$t->parse("update_products_priv", false);
				$t->set_var("read_only_products_priv", "");
			}
				
			$row_style = ($index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);
				
			$t->parse("records");			
		} while ($db->next_record());	
		
		$sql  = " SELECT category_id, category_name FROM " . $table_prefix . "categories ";
		$sql .= " WHERE parent_category_id = 0";
		$sql .= " ORDER BY category_order";
		$parent_categories = get_db_values($sql, null);
	
		if ($parent_categories) {
			set_options($parent_categories, $parent_category_id, "parent_category_id");
			$t->parse("move_products_block");
		}
		$t->set_var("products_ids", implode(",", $products_ids));
		$t->set_var("products_number", count($products_ids));
	} else {
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	$t->pparse("main");
?>