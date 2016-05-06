<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  product_print.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
                           

	include_once("./includes/common.php");
	include_once("./messages/" . $language_code . "/cart_messages.php");
	include_once("./includes/products_functions.php");
	include_once("./includes/shopping_cart.php");
	

	$user_id = get_session("session_user_id");		
	$user_type_id = get_session("session_user_type_id");	
	
	$tax_rates = get_tax_rates();
	$user_info = get_session("session_user_info");
	$user_tax_free = get_setting_value($user_info, "tax_free", 0);
	$discount_type = get_session("session_discount_type");
	$discount_amount = get_session("session_discount_amount");
	$display_products = get_setting_value($settings, "display_products", 0);
	$product_no_image = get_setting_value($settings, "product_no_image", "");
	$tax_prices = get_setting_value($settings, "tax_prices", 0);
	$weight_measure = get_setting_value($settings, "weight_measure", "");
	$user_id = get_session("session_user_id");		
	$user_type_id = get_session("session_user_type_id");
	$price_type = get_session("session_price_type");
	if ($price_type == 1) {
		$price_field = "trade_price";
		$sales_field = "trade_sales";
		$additional_price_field = "trade_additional_price";
		$properties_field = "trade_properties_price";
	} else {
		$price_field = "price";
		$sales_field = "sales_price";
		$additional_price_field = "additional_price";
		$properties_field = "properties_price";
	}
	// settings for product option price 
	$option_positive_price_right = get_setting_value($settings, "option_positive_price_right", ""); 
	$option_positive_price_left = get_setting_value($settings, "option_positive_price_left", ""); 
	$option_negative_price_right = get_setting_value($settings, "option_negative_price_right", ""); 
	$option_negative_price_left = get_setting_value($settings, "option_negative_price_left", "");

	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main", "product_print.html");

	$item_id = get_param("item_id");
	
	if (!VA_Products::check_exists($item_id)) {
		$t->set_var("item", "");
		$t->set_var("NO_PRODUCT_MSG", NO_PRODUCT_MSG);
		$t->sparse("no_item", false);		
		$t->pparse("main", false);
		exit;
	}
	
	if (!VA_Products::check_permissions($item_id, VIEW_ITEMS_PERM)) {
		header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
		exit;
	}

	$t->set_var("product_details_href", "product_details.php");
	$t->set_var("product_print_href", "product_print.php");
	$t->set_var("cl",               $currency["left"]);
	$t->set_var("cr",               $currency["right"]);

	$sql  = " SELECT i.item_id, i.item_type_id, i.item_code, i.special_offer,i.item_name, i.features, i.full_desc_type, i.short_description, i.full_description, i.big_image, ";
	$sql .= " i." . $price_field . ", i." . $sales_field . ", i.buying_price, i.discount_percent, i.tax_free, i.votes, i.points, i.is_sales, i.is_compared, ";
	$sql .= " i.manufacturer_code, m.manufacturer_name, ";
	$sql .= " i.stock_level, st_in.shipping_time_desc AS in_stock_message, st_out.shipping_time_desc AS out_stock_message, ";
	$sql .= " sr.shipping_rule_desc, notes, weight ";
	$sql .= " FROM ((((";
	$sql .= $table_prefix . "items i ";
	$sql .= " LEFT JOIN " . $table_prefix . "manufacturers m ON i.manufacturer_id=m.manufacturer_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_in ON i.shipping_in_stock=st_in.shipping_time_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "shipping_times st_out ON i.shipping_out_stock=st_out.shipping_time_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "shipping_rules sr ON i.shipping_rule_id=sr.shipping_rule_id) ";
	$sql .= " WHERE i.item_id = " . $db->tosql($item_id, INTEGER);
	$db->query($sql);
	if ($db->next_record())
	{
		$item_number = 0;
		$tabs = array("desc");

		$item_number++;
		$item_id = $db->f("item_id");
		$item_type_id = $db->f("item_type_id");
		$item_code = $db->f("item_code");
		$item_name = get_translation($db->f("item_name"));
		$manufacturer_code = $db->f("manufacturer_code");
		$manufacturer_name = $db->f("manufacturer_name");
		$form_id = $item_id;
		$short_description = get_translation($db->f("short_description"));
		$full_description = get_translation($db->f("full_description"));
		$full_desc_type = $db->f("full_desc_type");
		$is_compared = $db->f("is_compared");
		$notes = get_translation($db->f("notes"));

		$price = $db->f($price_field);
		$is_sales = $db->f("is_sales");
		$sales_price = $db->f($sales_field);
		$buying_price = $db->f("buying_price");
		
		// special prices
		$user_price        = false; 
		$user_price_action = 0;
		$q_prices   = get_quantity_price($item_id, 1);
		if ($q_prices) {
			$user_price        = $q_prices [0];
			$user_price_action = $q_prices [2];
		}

		$weight = $db->f("weight");
		$tax_free = $db->f("tax_free");
		if ($user_tax_free) { $tax_free = $user_tax_free; }

		if (!$full_description) { $full_description = $short_description; }
		if (strlen($short_description)) {
			$meta_description = $short_description;
		} elseif (strlen($full_description)) {
			$meta_description = $full_description;
		} else {
			$meta_description = $item_name;
		}
		$t->set_var("meta_description", get_meta_desc($meta_description));


		// calculate price
		if ($user_price > 0 && ($user_price_action > 0 || !$discount_type)) {
			if ($is_sales && $sales_price > 0) {
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

		// connection for properties
		$dbp = new VA_SQL();
		$dbp->DBType     = $db->DBType;
		$dbp->DBDatabase = $db->DBDatabase;
		$dbp->DBUser     = $db->DBUser;
		$dbp->DBPassword = $db->DBPassword;
		$dbp->DBHost     = $db->DBHost;
		$dbp->DBPort       = $db->DBPort;
		$dbp->DBPersistent = $db->DBPersistent;
  
		// connection for properies values
		$dbpv = new VA_SQL();
		$dbpv->DBType     = $db->DBType;
		$dbpv->DBDatabase = $db->DBDatabase;
		$dbpv->DBUser     = $db->DBUser;
		$dbpv->DBPassword = $db->DBPassword;
		$dbpv->DBHost     = $db->DBHost;
		$dbpv->DBPort       = $db->DBPort;
		$dbpv->DBPersistent = $db->DBPersistent;

		$sql  = " SELECT property_id, property_name, property_description, control_type FROM " . $table_prefix . "items_properties";
		$sql .= " WHERE (item_id=". $db->tosql($item_id, INTEGER) . " OR item_type_id=" . $db->tosql($item_type_id, INTEGER) . ") ";
		$sql .= " AND control_type <> 'TEXTAREA' AND control_type <> 'TEXTBOX' AND control_type <> 'TEXTBOXLIST'";
		$sql .= " AND (use_on_list=1 OR use_on_details=1 OR use_on_checkout=1 OR use_on_second=1) ";		
		$sql .= " ORDER BY property_id";		
		$dbp->query($sql);

		while ($dbp->next_record()) {
			if ($dbp->f("control_type") == "LABEL"){
				$t->set_var("property_name", get_translation($dbp->f("property_name")));
				$t->set_var("property_control", get_translation($dbp->f("property_description")));
				$t->parse("properties",true);
			} elseif ($dbp->f("control_type") == "LISTBOX" || $dbp->f("control_type") == "RADIOBUTTON" || $dbp->f("control_type") == "CHECKBOXLIST"){
				$sql_pv  = " SELECT property_value, buying_price, additional_price, trade_additional_price, percentage_price ";
				$sql_pv .= " FROM " . $table_prefix . "items_properties_values";
				$sql_pv .= " WHERE property_id=". $dbp->f("property_id") . " AND hide_value=0";
				$dbpv->query($sql_pv);
				$property_control="";
					while ($dbpv->next_record()) {
						if ($display_products != 2 || strlen($user_id)) {
							$price_property = $dbpv->f($additional_price_field);
							$percentage_price = $dbpv->f("percentage_price");
							if ($percentage_price && $item_price) {
								$price_property += round(($item_price * $percentage_price) / 100, 2);
							}
						} else {
							$price_property = 0;
						}
						if ($discount_type == 1) {
							$price_property -= round(($price_property * $discount_amount) / 100, 2);
						} elseif ($discount_type == 4) {
							$buying_price = $dbpv->f("buying_price");	
							$price_property -= round((($price_property - $buying_price) * $discount_amount) / 100, 2);
						}
						$price_property_tax = 0;
						if ($tax_prices == 2 || $tax_prices == 3) {
							$price_property_tax = set_tax_price($item_id, $item_type_id, $price_property, 0, $tax_free);
						}

						if ($price_property > 0) {
							$price_property = $option_positive_price_right . currency_format($price_property + $price_property_tax) . $option_positive_price_left;
						} elseif ($price_property < 0) {
							$price_property = $option_negative_price_right . currency_format(abs($price_property + $price_property_tax)) . $option_negative_price_left;
						}
						if ($price_property == "0") {$price_property="";}
						else {$price_property = " ".$price_property;}
						if (strlen($property_control)) {
							$property_control .= "; ";
						}
						$property_control .= get_translation($dbpv->f("property_value")).$price_property;
					}
				$t->set_var("property_name", get_translation($dbp->f("property_name")));
				$t->set_var("property_control", $property_control);
		
				$t->parse("properties",true);
			}

			
		}

		$t->set_var("item_id", $item_id);
		$t->set_var("item_code", htmlspecialchars($item_code));
		$t->set_var("item_name", $item_name);
		$t->set_var("product_name", $item_name);
		$t->set_var("product_title", $item_name);
		$t->set_var("item_name_strip", htmlspecialchars(strip_tags($item_name)));
		$t->set_var("manufacturer_code", htmlspecialchars($manufacturer_code));
		$t->set_var("manufacturer_name", htmlspecialchars($manufacturer_name));

		$stock_level = $db->f("stock_level");
		if ($stock_level > 0) {
			$shipping_time_desc = get_translation($db->f("in_stock_message"));
		} else {
			$shipping_time_desc = get_translation($db->f("out_stock_message"));
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

		$features_list = get_translation($db->f("features"));
		if ($features_list) 
		{
			$t->set_var("features_list", $features_list);
			$t->parse("features_list_block", false);
		}

		$special_offer = $db->f("special_offer");
		$t->set_var("special_offer", $special_offer);

		$big_image = $db->f("big_image");
		if (!$big_image) {
			$big_image = $product_no_image;
		}
		if ($big_image)
		{
			$image_size = preg_match("/^http\:\/\//", $big_image) ? "" : @GetImageSize($big_image);
			$t->set_var("alt", htmlspecialchars($item_name));
			$t->set_var("src", htmlspecialchars($big_image));
			if (is_array($image_size)) {
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

		if ($display_products != 2 || strlen($user_id)) {

			$base_price = calculate_price($price, $is_sales, $sales_price);
			$t->set_var("base_price", $base_price);
			if ($sales_price > 0 && $sales_price != $price && $is_sales)
			{
				$discount_percent = round($db->f("discount_percent"), 0);
				if (!$discount_percent) 
					$discount_percent = round(($price - $sales_price) / ($price / 100), 0);
	  
				$t->set_var("discount_percent", $discount_percent);

				set_tax_price($item_id, $item_type_id, $price, $sales_price, $tax_free, "price", "sales_price", "tax_sales", true);
	  
				$t->sparse("price_block", false);
				$t->sparse("sales", false);
				$t->sparse("save", false);
			}
			else
			{
				set_tax_price($item_id, $item_type_id, $price, 0, $tax_free, "price", "", "tax_price", true);

				$t->sparse("price_block", false);
				$t->set_var("sales", "");
				$t->set_var("save", "");
			}
		}

		// description block
		$t->set_var("description_block", "");
		if ($full_description) {
			$t->global_parse("title_desc", false, false, true);
			if ($full_desc_type != 1)
				$full_description = nl2br(htmlspecialchars($full_description));
			$t->set_var("full_description", $full_description);
			$t->parse("description", false);
		} else {
			$t->set_var("title_desc", "");
			$t->set_var("description", "");
		}

		if (strlen($notes)) {
			$t->set_var("notes", $notes);
			$t->parse("notes_block", false);
		}
		if ($weight > 0) {
			if (strpos ($weight, ".") !== false) {
				while(substr($weight, strlen($weight) - 1) == "0")
					$weight = substr($weight, 0, strlen($weight) - 1);
			}
			if (substr($weight, strlen($weight) - 1) == ".")
				$weight = substr($weight, 0, strlen($weight) - 1);
			$t->set_var("weight", $weight . " " . $weight_measure);
			$t->parse("weight_block", false);
		}
		$t->global_parse("description_block", false, false, true);

		// specification details
		$t->set_var("specification", "");
		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "features WHERE item_id=" . intval($item_id);
		$db->query($sql);
		$db->next_record();
		$total_spec = $db->f(0);
		if ($total_spec > 0) {
			$tabs[] = "spec";
			$sql  = " SELECT fg.group_id,fg.group_name,f.feature_name,f.feature_value ";
			$sql .= " FROM " . $table_prefix . "features f, " . $table_prefix . "features_groups fg ";
			$sql .= " WHERE f.group_id=fg.group_id ";
			$sql .= " AND f.item_id=" . intval($item_id);
			$sql .= " ORDER BY fg.group_order, f.feature_id ";
			$db->query($sql);
			$t->global_parse("title_spec", false, false, true);
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
				$t->parse("specification", false);
			} 
		}
		// end specification


		// product images 
		$t->set_var("images", "");
		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "items_images WHERE item_id=" . intval($item_id);
		$db->query($sql);
		$db->next_record();
		$total_images = $db->f(0);
		if ($total_images > 0) {
			$tabs[] = "images";

			$image_number = 0;
			$t->global_parse("title_images", false, false, true);
			$sql  = " SELECT image_title, image_small, image_large, image_description  ";
			$sql .= " FROM " . $table_prefix . "items_images ";
			$sql .= " WHERE item_id=" . intval($item_id);
			$db->query($sql);
			while ($db->next_record()) {
				$image_number++;
    
				$image_title = $db->f("image_title");
				$image_small = $db->f("image_small");
				$image_large = $db->f("image_large");
				if (!strlen($image_large)) {
					$image_large = $image_small;
				}
				$image_description = $db->f("image_description");
     
				$t->set_var("image_title", $image_title);
				$t->set_var("image_small", $image_small);
				$t->set_var("image_width", "");
				$t->set_var("image_height", "");
				$t->set_var("image_large", $image_large);
				$t->set_var("image_description", $image_description);
				$t->parse("images_cols", true);
				if ($image_number % 2 == 0) {
					$t->parse("images_rows", true);
					$t->set_var("images_cols", "");
				}
			}	    
			if ($image_number % 2 != 0) {
				$t->parse("images_rows", true);
			}
			$t->parse("images", false);
		}
		// end images 


		// product accessories
		$t->set_var("accessories_block", "");
		$sql_params = array();
		$sql_params["brackets"] = "("; 
		$sql_params["join"]   = " INNER JOIN " . $table_prefix . "items_accessories ia ON i.item_id=ia.accessory_id) ";	
		$sql_params["where"]  = " ia.item_id=" . $db->tosql($item_id, INTEGER);		
		$accessories_ids = VA_Products::find_all_ids($sql_params, VIEW_CATEGORIES_ITEMS_PERM);
				
		if ($accessories_ids) {
			$total_accessories = count($accessories_ids);
			$allowed_accessories_ids = VA_Products::find_all_ids("i.item_id IN (" . $db->tosql($accessories_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);
			$tabs[] = "accessories";

			$accessory_number = 0;
			$t->global_parse("title_accessories", false, false, true);
			$sql  = " SELECT i.item_id, i.item_type_id, i.item_name,i.short_description, ";
			$sql .= " i.buying_price, i.".$properties_field.", i." . $price_field . ", i." . $sales_field . ", i.is_sales, i.tax_free ";
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
				$accessory_name = $db->f("item_name");
				$accessory_description = $db->f("short_description");

				$t->set_var("accessory_id", $accessory_id);
				$t->set_var("accessory_name", $accessory_name);
				$t->set_var("accessory_description", $accessory_description);

				if (!$allowed_accessories_ids || !in_array($accessory_id, $allowed_accessories_ids)) {
					$t->set_var("restricted_class", " restrictedItem");
					$t->sparse("restricted_image", false);
				} else {
					$t->set_var("restricted_class", "");
					$t->set_var("restricted_image", "");
				}
				if ($display_products != 2 || strlen($user_id)) {
					$buying_price = $db->f("buying_price");
					$properties_price = $db->f($properties_field);
					$price = $db->f($price_field);
					$sales_price = $db->f($sales_field);
					$is_sales = $db->f("is_sales");
					
					// special prices
					$user_price        = false; 
					$user_price_action = 0;
					$q_prices   = get_quantity_price($item_id, 1);
					if ($q_prices) {
						$user_price        = $q_prices [0];
						$user_price_action = $q_prices [2];
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
					set_tax_price($accessory_id, $accessory_type_id, $accessory_price, 0, $accessory_tax_free, "accessory_price", "", "accessory_tax_price", false);

					$t->sparse("accessory_price_block", false);
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
			$t->parse("accessories_block", false);
		}

		$t->parse("item");
		$t->set_var("no_item", "");

		$t->set_var("reviews", "");
	}

	$t->pparse("main", false);
	
?>