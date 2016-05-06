<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  invoice_functions.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	$root_folder_path = (isset($is_admin_path) && $is_admin_path) ? "../" : "./";
	include_once($root_folder_path . "includes/pdflib.php");
	include_once($root_folder_path . "includes/pdf.php");
	include_once($root_folder_path . "includes/shopping_cart.php");

	@ini_set("max_execution_time", 200);
	function pdf_invoice($orders_ids)
	{
		global $db, $table_prefix, $settings, $currency, $parameters, $site_id;
		global $is_admin_path, $root_folder_path;
		
		// output buffer
		$pdf_buffer = "";

		// additional connection
		$dbi = new VA_SQL();
		$dbi->DBType      = $db->DBType      ;
		$dbi->DBDatabase  = $db->DBDatabase  ;
		$dbi->DBUser      = $db->DBUser      ;
		$dbi->DBPassword  = $db->DBPassword  ;
		$dbi->DBHost      = $db->DBHost      ;
		$dbi->DBPort      = $db->DBPort      ;
		$dbi->DBPersistent= $db->DBPersistent;

		$show_item_code = get_setting_value($settings, "item_code_invoice", 0);
		$show_manufacturer_code = get_setting_value($settings, "manufacturer_code_invoice", 0);
		$show_points_price = get_setting_value($settings, "points_price_invoice", 0);
		$show_reward_points = get_setting_value($settings, "reward_points_invoice", 0);
		$show_reward_credits = get_setting_value($settings, "reward_credits_invoice", 0);
		$item_name_column = get_setting_value($settings, "invoice_item_name", 1);
		$item_price_column = get_setting_value($settings, "invoice_item_price", 1);
		$item_tax_percent_column = get_setting_value($settings, "invoice_item_tax_percent", 0);
		$item_tax_column = get_setting_value($settings, "invoice_item_tax", 0);
		$item_price_incl_tax_column = get_setting_value($settings, "invoice_item_price_incl_tax", 0);
		$item_quantity_column = get_setting_value($settings, "invoice_item_quantity", 1);
		$item_price_total_column = get_setting_value($settings, "invoice_item_price_total", 1);
		$item_tax_total_column = get_setting_value($settings, "invoice_item_tax_total", 1);
		$item_price_incl_tax_total_column = get_setting_value($settings, "invoice_item_price_incl_tax_total", 1);
		$global_tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
		$global_tax_round = get_setting_value($settings, "tax_round", 1);
		$tax_prices = get_setting_value($settings, "tax_prices", 0);
		//$tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
		$tax_note = get_translation(get_setting_value($settings, "tax_note", ""));
		$tax_note_excl = get_translation(get_setting_value($settings, "tax_note_excl", ""));		
		$points_decimals = get_setting_value($settings, "points_decimals", 0);
		$item_image_column = get_setting_value($settings, "invoice_item_image", 0);

		// option price options
		$option_positive_price_right = get_setting_value($settings, "option_positive_price_right", ""); 
		$option_positive_price_left = get_setting_value($settings, "option_positive_price_left", ""); 
		$option_negative_price_right = get_setting_value($settings, "option_negative_price_right", ""); 
		$option_negative_price_left = get_setting_value($settings, "option_negative_price_left", "");

		// image settings
		if ($item_image_column) {
			$site_url = get_setting_value($settings, "site_url", "");
			$product_no_image = get_setting_value($settings, "product_no_image", "");
			$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");		
			product_image_fields($item_image_column, $image_type_name, $image_field, $image_alt_field, $watermark, $product_no_image);			
			$item_image_tmp_dir  = get_setting_value($settings, "tmp_dir", $root_folder_path);
			$item_image_position = 0;
			if ($item_image_column == 1) {
				$item_image_width  = get_setting_value($settings, "tiny_image_max_width", 40);
				$item_image_height = get_setting_value($settings, "tiny_image_max_height", 40);
				$item_image_position = 1;
			} elseif ($item_image_column == 2) {
				$item_image_width  = get_setting_value($settings, "small_image_max_width", 100);
				$item_image_height = get_setting_value($settings, "small_image_max_height", 100);
				$item_image_position = 1;
			} elseif ($item_image_column == 3) {
				$item_image_width  = get_setting_value($settings, "big_image_max_width", 300);
				$item_image_height = get_setting_value($settings, "big_image_max_height", 300);
				$item_image_position = 2;
			}			
		}
		
		// get initial invoice settings
		$invoice = array();
		$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings";
		$sql .= " WHERE setting_type='printable'";
		if (isset($site_id)) {
			$sql .= "AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
			$sql .= "ORDER BY site_id ASC";
		} else {
			$sql .= "AND site_id=1";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$invoice[$db->f("setting_name")] = $db->f("setting_value");
		}

		// get order profile settings
		$order_info = array();
		$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings";
		$sql .= " WHERE setting_type='order_info'";
		if (isset($site_id)) {
			$sql .= "AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
			$sql .= "ORDER BY site_id ASC";
		} else {
			$sql .= "AND site_id=1";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$order_info[$db->f("setting_name")] = $db->f("setting_value");
		}
		$subcomponents_show_type = get_setting_value($order_info, "subcomponents_show_type", 0);

		// General PDF settings
		$pdf_library = isset($invoice["pdf_lib"]) ? $invoice["pdf_lib"] : 1;
		if ($pdf_library == 2) {
			$pdf = new VA_PDFLib();
		} else {
			$pdf = new VA_PDF();
		}
		$pdf->set_creator("admin_invoice_pdf.php");
		$pdf->set_author("ViArt Ltd");
		$pdf->set_title("Invoice No: " . $orders_ids);
		$pdf->set_font_encoding(CHARSET);
		$page_number = 0;

		// general order fields settings
		$r = new VA_Record($table_prefix . "orders");
		$r->add_where("order_id", INTEGER);
		$r->add_textbox("site_id", INTEGER);
		for ($i = 0; $i < sizeof($parameters); $i++) {
			$r->add_textbox($parameters[$i], TEXT);
			$r->add_textbox("delivery_" . $parameters[$i], TEXT);
		}
		$r->add_textbox("invoice_number", TEXT);
		$r->add_textbox("user_id", INTEGER);
		$r->add_textbox("payment_id", INTEGER);
		$r->add_textbox("order_placed_date", DATETIME);
		$r->add_textbox("currency_code", TEXT);
		$r->add_textbox("currency_rate", NUMBER);
		$r->add_textbox("shipping_tracking_id", TEXT);
		$r->add_textbox("remote_address", TEXT);
		$r->add_textbox("cc_name", TEXT);
		$r->add_textbox("cc_first_name", TEXT);
		$r->add_textbox("cc_last_name", TEXT);
		$r->add_textbox("cc_number", TEXT);
		$r->add_textbox("cc_start_date", DATETIME);
		$r->change_property("cc_start_date", VALUE_MASK, array("MM", " / ", "YYYY"));
		$r->add_textbox("cc_expiry_date", DATETIME);
		$r->change_property("cc_expiry_date", VALUE_MASK, array("MM", " / ", "YYYY"));
		$r->add_textbox("cc_type", INTEGER);
		$r->add_textbox("cc_issue_number", INTEGER);
		$r->add_textbox("cc_security_code", TEXT);
		$r->add_textbox("pay_without_cc", TEXT);
		$r->add_textbox("tax_name", TEXT);
		$r->add_textbox("tax_percent", NUMBER);
		$r->add_textbox("tax_total", NUMBER);
		$r->add_textbox("tax_prices_type", INTEGER);
		$r->add_textbox("tax_round", INTEGER);
		$r->change_property("tax_round", USE_IN_SELECT, false);
		$r->add_textbox("total_discount", NUMBER);
		$r->add_textbox("total_discount_tax", NUMBER);
		$r->add_textbox("shipping_type_desc", TEXT);
		$r->add_textbox("shipping_cost", NUMBER);
		$r->add_textbox("shipping_taxable", NUMBER);
		$r->add_textbox("credit_amount", NUMBER);
		$r->add_textbox("processing_fee", NUMBER);
		$r->add_textbox("order_total", NUMBER);

		$ids = explode(",", $orders_ids);
		if (isset($site_id)) {
			$previous_site_id = $site_id;
		} else {
			$previous_site_id = 1;
		}
		for ($id = 0; $id < sizeof($ids); $id++)
		{
			$order_id = $ids[$id];
			$r->set_value("order_id", $order_id);
			$r->get_db_values();
			$order_site_id = $r->get_value("site_id");

			if ($previous_site_id != $order_site_id) {
				// get invoice settings for current order
				$invoice = array();
				$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings";
				$sql .= " WHERE setting_type='printable'";
				if ($order_site_id) {
					$sql .= "AND (site_id=1 OR site_id=" . $db->tosql($order_site_id, INTEGER, true, false) . ")";
					$sql .= "ORDER BY site_id ASC";
				} else {
					$sql .= "AND site_id=1";
				}
				$db->query($sql);
				while ($db->next_record()) {
					$invoice[$db->f("setting_name")] = $db->f("setting_value");
				}

				// get order fields settings for current order
				$order_info = array();
				$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings";
				$sql .= " WHERE setting_type='order_info'";
				if (isset($order_site_id)) {
					$sql .= "AND (site_id=1 OR site_id=" . $db->tosql($order_site_id, INTEGER, true, false) . ")";
					$sql .= "ORDER BY site_id ASC";
				} else {
					$sql .= "AND site_id=1";
				}
				$db->query($sql);
				while ($db->next_record()) {
					$order_info[$db->f("setting_name")] = $db->f("setting_value");
				}
			}
			$previous_site_id = $order_site_id;

			// check parameters list to hide
			$personal_number = 0; $delivery_number = 0;
			for ($i = 0; $i < sizeof($parameters); $i++)
			{
				$personal_param = "show_" . $parameters[$i];
				$delivery_param = "show_delivery_" . $parameters[$i];
				if (isset($order_info[$personal_param]) && $order_info[$personal_param] == 1) {
					$personal_number++;
					$r->parameters[$parameters[$i]][SHOW] = true;
				} else {
					$r->parameters[$parameters[$i]][SHOW] = false;
				}
				if (isset($order_info[$delivery_param]) && $order_info[$delivery_param] == 1) {
					$delivery_number++;
					$r->parameters["delivery_" . $parameters[$i]][SHOW] = true;
				} else {
					$r->parameters["delivery_" . $parameters[$i]][SHOW] = false;
				}
			}

			// get order tax rates
			$tax_available = false; $tax_percent_sum = 0; $tax_names = "";
			$order_tax_rates = order_tax_rates($order_id);
			if (sizeof($order_tax_rates) > 0) {
				$tax_available = true;
				foreach ($order_tax_rates as $tax_id => $tax_info) {
					$tax_percent_sum += $tax_info["tax_percent"];
					if ($tax_names) { $tax_names .= " & "; }
					$tax_names .= get_translation($tax_info["tax_name"]);
				}
			}

			$tax_available = sizeof($order_tax_rates);
			$tax_prices_type = $r->get_value("tax_prices_type");
			if (!strlen($tax_prices_type)) {
				$tax_prices_type = $global_tax_prices_type;
			}
			$tax_round = $r->get_value("tax_round");
			if (!strlen($tax_round)) {
				$tax_round = $global_tax_round;
			}

			$tax_total = $r->get_value("tax_total");
			$total_discount = $r->get_value("total_discount");
			$total_discount_tax = $r->get_value("total_discount_tax");
			$shipping_type_desc = strip_tags(get_translation($r->get_value("shipping_type_desc")));
			$shipping_cost = $r->get_value("shipping_cost");
			$shipping_taxable = $r->get_value("shipping_taxable");
			$shipping_tax_free = ($shipping_taxable) ? 0 : 1;
			// get taxes for selected shipping and add it to total values 
			$shipping_tax_values = get_tax_amount($order_tax_rates, "shipping", $shipping_cost, $shipping_tax_free, $shipping_tax_percent, "", 2, $tax_prices_type, $tax_round);
			$shipping_tax_total = add_tax_values($order_tax_rates, $shipping_tax_values, "shipping", $tax_round);
			if ($tax_prices_type == 1) {
				$shipping_cost_excl_tax = $shipping_cost - $shipping_tax_total;
				$shipping_cost_incl_tax = $shipping_cost;
			} else {
				$shipping_cost_excl_tax = $shipping_cost;
				$shipping_cost_incl_tax = $shipping_cost + $shipping_tax_total;
			}

			$credit_amount = $r->get_value("credit_amount");
			$processing_fee = $r->get_value("processing_fee");
			$order_total = $r->get_value("order_total");
	  
			// get order currency
			$order_currency_code = $r->get_value("currency_code");
			$order_currency_rate= $r->get_value("currency_rate");

	  	// get order currency
			$orders_currency = get_setting_value($settings, "orders_currency", 0);
			if ($orders_currency != 1) {
				$order_currency = $currency;
				$order_currency["rate"] = $order_currency_rate;
				if (strtolower($currency["code"]) != strtolower($order_currency_code)) {
					$order_currency["rate"] = $currency["rate"]; // in case if active currency different from the order was placed use current exchange rate
				}
			} else {
				$order_currency = get_currency($order_currency_code);
				$order_currency["rate"] = $order_currency_rate; // show order with exchange rate it was placed
				$order_currency["left"] = html_entity_decode($order_currency["left"], ENT_QUOTES, "ISO-8859-15");
				$order_currency["right"] = html_entity_decode($order_currency["right"], ENT_QUOTES, "ISO-8859-15");
			}

			// check what columns to show
			$goods_colspan = 0; $total_columns = 0;
			if ($item_image_column) {
				$goods_colspan++;
				$total_columns++;
			}
			if ($item_name_column) {
				$goods_colspan++;
				$total_columns++;
			}
			if ($item_price_column || ($item_price_incl_tax_column && !$tax_available)) {
				$item_price_column = true;
				$goods_colspan++;
				$total_columns++;
			}
			if ($item_tax_percent_column && $tax_available) {
				$goods_colspan++;
				$total_columns++;
			} else {
				$item_tax_percent_column = false;
			}
			if ($item_tax_column && $tax_available) {
				$goods_colspan++;
				$total_columns++;
			} else {
				$item_tax_column = false;
			}
			if ($item_price_incl_tax_column && $tax_available) {
				$goods_colspan++;
				$total_columns++;
			} else {
				$item_price_incl_tax_column = false;
			}
			if ($item_quantity_column) {
				$goods_colspan++;
				$total_columns++;
			}
			if ($item_price_total_column || ($item_price_incl_tax_total_column && !$tax_available)) {
				$item_price_total_column = true;
				$total_columns++;
			}
			if ($item_tax_total_column && $tax_available) {
				$total_columns++;
			} else {
				$item_tax_total_column = false;
			}
			if ($item_price_incl_tax_total_column && $tax_available) {
				$total_columns++;
			} else {
				$item_price_incl_tax_total_column = false;
			}

			$columns = array(
				"item_name" => array("name" => PROD_TITLE_COLUMN, "active" => $item_name_column, "align" => "left"), 
				"item_price" => array("name" => PROD_PRICE_COLUMN . " " . $tax_note_excl, "active" => $item_price_column, "align" => "right"), 
				"item_tax_percent" => array("name" => $tax_names . " (%)", "active" => $item_tax_percent_column, "align" => "center"),
				"item_tax" => array("name" => $tax_names, "active" => $item_tax_column, "align" => "right"),
				"item_price_incl_tax" => array("name" => PROD_PRICE_COLUMN . " " . $tax_note, "active" => $item_price_incl_tax_column, "align" => "right"),
				"item_quantity" => array("name" => PROD_QTY_COLUMN, "active" => $item_quantity_column, "align" => "center"),
				"item_price_total" => array("name" => PROD_TOTAL_COLUMN . " " . $tax_note_excl, "active" => $item_price_total_column, "align" => "right"),
				"item_tax_total" => array("name" => $tax_names . " " .PROD_TAX_TOTAL_COLUMN, "active" => $item_tax_total_column, "align" => "right"),
				"item_price_incl_tax_total" => array("name" => PROD_TOTAL_COLUMN . " " . $tax_note, "active" => $item_price_incl_tax_total_column, "align" => "right"),
			);
			foreach ($columns as $column_name => $column_values) {
				$columns[$column_name]["width"] = 0;
				$columns[$column_name]["start"] = 0;
			}

			$columns_left = $total_columns;
			$column_end = 40;
			
			// left space for image
			if ($item_image_column && $item_image_position == 1) {
				$column_end += $item_image_width;
			}
			
			$item_name_column = true; // always show product title
			if ($item_name_column) {
				$columns["item_name"]["start"] = $column_end;
				if ($total_columns <= 6) {
					$columns["item_name"]["width"] = 240;
				} elseif ($total_columns == 7) {
					$columns["item_name"]["width"] = 200;
				} elseif ($total_columns == 8) {
					$columns["item_name"]["width"] = 160;
				} elseif ($total_columns == 9) {
					$columns["item_name"]["width"] = 120;
				}
				$columns_left--;
				$column_end += $columns["item_name"]["width"];
			}
			$width_left = 515 - $columns["item_name"]["width"];
			$average_width = intval($width_left / $columns_left);
			if ($item_price_column) {
				if ($average_width > 50) {
					$columns["item_price"]["width"] = $average_width;
				} else {
					$columns["item_price"]["width"] = 50;
				}
				$columns["item_price"]["start"] = $column_end;
				$column_end += $columns["item_price"]["width"];
			}
			if ($item_tax_percent_column) {
				if ($average_width > 50) {
					$columns["item_tax_percent"]["width"] = $average_width;
				} else {
					$columns["item_tax_percent"]["width"] = 45;
				}
				$columns["item_tax_percent"]["start"] = $column_end;
				$column_end += $columns["item_tax_percent"]["width"];
			}
			if ($item_tax_column) {
				if ($average_width > 50) {
					$columns["item_tax"]["width"] = $average_width;
				} else {
					$columns["item_tax"]["width"] = 50;
				}
				$columns["item_tax"]["start"] = $column_end;
				$column_end += $columns["item_tax"]["width"];
			}
			if ($item_price_incl_tax_column) {
				if ($average_width > 50) {
					$columns["item_price_incl_tax"]["width"] = $average_width;
				} else {
					$columns["item_price_incl_tax"]["width"] = 50;
				}
				$columns["item_price_incl_tax"]["start"] = $column_end;
				$column_end += $columns["item_price_incl_tax"]["width"];
			}
			if ($item_quantity_column) {
				if ($average_width > 50) {
					$columns["item_quantity"]["width"] = $average_width;
				} else {
					$columns["item_quantity"]["width"] = 45;
				}
				$columns["item_quantity"]["start"] = $column_end;
				$column_end += $columns["item_quantity"]["width"];
			}
			if ($item_price_total_column) {
				if ($average_width > 50) {
					$columns["item_price_total"]["width"] = $average_width;
				} else {
					$columns["item_price_total"]["width"] = 50;
				}
				$columns["item_price_total"]["start"] = $column_end;
				$column_end += $columns["item_price_total"]["width"];
			}
			if ($item_tax_total_column) {
				if ($average_width > 50) {
					$columns["item_tax_total"]["width"] = $average_width;
				} else {
					$columns["item_tax_total"]["width"] = 50;
				}
				$columns["item_tax_total"]["start"] = $column_end;
				$column_end += $columns["item_tax_total"]["width"];
			}
			if ($item_price_incl_tax_total_column) {
				if ($average_width > 50) {
					$columns["item_price_incl_tax_total"]["width"] = $average_width;
				} else {
					$columns["item_price_incl_tax_total"]["width"] = 50;
				}
				$columns["item_price_incl_tax_total"]["start"] = $column_end;
				$column_end += $columns["item_price_incl_tax_total"]["width"];
			}
			$last_column_name = "item_name";
			foreach ($columns as $column_name => $values) {
				if ($values["active"]) {
					$last_column_name = $column_name;
				}
			}
			$columns[$last_column_name]["width"] = 555 - $columns[$last_column_name]["start"];

			// set values from list
			$r->set_value("company_id", get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("company_id"), INTEGER))));
			$r->set_value("state_id", get_translation(get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("state_id"), INTEGER))));
			$r->set_value("country_id", get_translation(get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("country_id"), INTEGER))));
			$r->set_value("delivery_company_id", get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("delivery_company_id"), INTEGER))));
			$r->set_value("delivery_state_id", get_translation(get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("delivery_state_id"), INTEGER))));
			$r->set_value("delivery_country_id", get_translation(get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("delivery_country_id"), INTEGER))));
			$r->set_value("cc_type", get_translation(get_db_value("SELECT credit_card_name FROM " . $table_prefix . "credit_cards WHERE credit_card_id=" . $db->tosql($r->get_value("cc_type"), INTEGER))));

			// get all order properties
			$orders_properties = array(); $cart_properties = array(); $personal_properties = array();
			$delivery_properties = array(); $payment_properties = array();
			$properties_total = 0; $properties_taxable = 0;
			$sql  = " SELECT op.property_id, op.property_type, op.property_name, op.property_value, ";
			$sql .= " op.property_value, op.property_price, op.property_points_amount, op.tax_free, ocp.control_type ";
			$sql .= " FROM (" . $table_prefix . "orders_properties op ";
			$sql .= " INNER JOIN " . $table_prefix . "order_custom_properties ocp ON op.property_id=ocp.property_id)";
			$sql .= " WHERE op.order_id=" . $db->tosql($order_id, INTEGER);
			$sql .= " ORDER BY op.property_order, op.property_id ";
			$db->query($sql);
			while ($db->next_record()) {
				$property_id = $db->f("property_id");
				$property_type = $db->f("property_type");
				$property_name = get_translation($db->f("property_name"));
				$property_value = get_translation($db->f("property_value"));
				$property_price = $db->f("property_price");
				$property_points_amount = $db->f("property_points_amount");
				$property_tax_free = $db->f("tax_free");
				$control_type = $db->f("control_type");
				$properties_total += $property_price;
				if ($property_tax_free != 1) {
					$properties_taxable += $property_price;
				}
    
				$property_tax_values = get_tax_amount($order_tax_rates, "properties", $property_price, $property_tax_free, $property_tax_percent, "", 2, $tax_prices_type, $tax_round);
				$property_tax = add_tax_values($order_tax_rates, $property_tax_values, "properties", $tax_round);
		  
				if ($tax_prices_type == 1) {
					$property_price_excl_tax = $property_price - $property_tax;
					$property_price_incl_tax = $property_price;
				} else {
					$property_price_excl_tax = $property_price;
					$property_price_incl_tax = $property_price + $property_tax;
				}

				// check value description
				if (($control_type == "CHECKBOXLIST" ||  $control_type == "RADIOBUTTON" || $control_type == "LISTBOX") && is_numeric($property_value)) {
					$sql  = " SELECT property_value FROM " . $table_prefix . "order_custom_values ";
					$sql .= " WHERE property_value_id=" . $dbi->tosql($property_value, INTEGER);
					$dbi->query($sql);
					if ($dbi->next_record()) {
						$property_value = get_translation($dbi->f("property_value"));
					}
				}
				if (isset($orders_properties[$property_id])) {
					$orders_properties[$property_id]["value"] .= "; " . $property_value;
					$orders_properties[$property_id]["price"] += $property_price;
					$orders_properties[$property_id]["points_amount"] += $property_points_amount;
				} else {
					$orders_properties[$property_id] = array(
						"type" => $property_type, "name" => $property_name, "value" => $property_value, 
						"price" => $property_price, "points_amount" => $property_points_amount, "tax_free" => $property_tax_free,
						"tax" => $property_tax, "property_price_excl_tax" => $property_price_excl_tax, "property_price_incl_tax" => $property_price_incl_tax,
					);
				}
	  
				// save data by arrays
				if ($property_type == 1) {
				  $cart_properties[$property_id] = $orders_properties[$property_id];
				} elseif ($property_type == 2) {
					$personal_properties[$property_id] = $orders_properties[$property_id];
				} elseif ($property_type == 3) {
					$delivery_properties[$property_id] = $orders_properties[$property_id];
				} elseif ($property_type == 4) {
					$payment_properties[$property_id] = $orders_properties[$property_id];
				}
			}

			begin_new_page($pdf, $height_position, $page_number);
			set_invoice_header($pdf, $height_position, $invoice, $r);
			set_user_info($pdf, $height_position, $r, $personal_number, $delivery_number, $personal_properties, $delivery_properties, $order_currency);
			set_table_header($pdf, $height_position, $r, $columns);

			// show order items
			$goods_total = 0; $goods_tax_total = 0;
			$goods_total_excl_tax = 0; $goods_total_incl_tax = 0;

			$orders_items = array();
			$sql  = " SELECT * FROM " . $table_prefix . "orders_items ";
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$sql .= " ORDER BY order_item_id ";
			$db->query($sql);
			while ($db->next_record()) {
				$order_item_id = $db->f("order_item_id");
				$top_order_item_id = $db->f("top_order_item_id");
				$item_type_id = $db->f("item_type_id");
				$price = $db->f("price");
				$quantity = $db->f("quantity");
				$item_tax_free = $db->f("tax_free");
				$item_total = $price * $quantity;
		  
				// new
				$item_tax = get_tax_amount($order_tax_rates, $item_type_id, $price, $item_tax_free, $item_tax_percent, "", 1, $tax_prices_type, $tax_round);
				$item_tax_values = get_tax_amount($order_tax_rates, $item_type_id, $item_total, $item_tax_free, $item_tax_percent, "", 2, $tax_prices_type, $tax_round);
				$item_tax_total = add_tax_values($order_tax_rates, $item_tax_values, "products", $tax_round);
		  
				if ($tax_prices_type == 1) {
					$price_excl_tax = $price - $item_tax;
					$price_incl_tax = $price;
					$price_excl_tax_total = $item_total - $item_tax_total;
					$price_incl_tax_total = $item_total;
				} else {
					$price_excl_tax = $price;
					$price_incl_tax = $price + $item_tax;
					$price_excl_tax_total = $item_total;
					$price_incl_tax_total = $item_total + $item_tax_total;
				}

				$orders_items[$order_item_id] = $db->Record;
				$orders_items[$order_item_id]["item_total"] = $item_total;
				$orders_items[$order_item_id]["price_excl_tax"] = $price_excl_tax;
				$orders_items[$order_item_id]["price_incl_tax"] = $price_incl_tax;
				$orders_items[$order_item_id]["price_excl_tax_total"] = $price_excl_tax_total;
				$orders_items[$order_item_id]["price_incl_tax_total"] = $price_incl_tax_total;
				$orders_items[$order_item_id]["item_tax"] = $item_tax;
				$orders_items[$order_item_id]["item_tax_total"] = $item_tax_total;
				$orders_items[$order_item_id]["tax_percent"] = $item_tax_percent;
				$orders_items[$order_item_id]["component"] = array();
				if ($top_order_item_id) {
					$orders_items[$top_order_item_id]["components"][] = $order_item_id;
				}
			}

			foreach ($orders_items as $order_item_id => $item) {
				if ($height_position < 200) {
					$pdf->end_page();
					begin_new_page($pdf, $height_position, $page_number);
					set_table_header($pdf, $height_position, $r, $columns);
				}
	  
				$top_order_item_id = $item["top_order_item_id"];
				if ($subcomponents_show_type == 1 && $top_order_item_id && isset($orders_items[$top_order_item_id])) {
					// component already shown with parent product
					continue;
				}
				$item_id = $item["item_id"];
				$order_item_id = $item["order_item_id"];
				$quantity = $item["quantity"];
				$selection_name = get_translation($item["component_name"]);
				$item_name = strip_tags(get_translation($item["item_name"]));
				$item_code = $item["item_code"];
				$manufacturer_code = $item["manufacturer_code"];
	  
				$price = $item["price"];
				$tax_free = $item["tax_free"];
				$item_tax_percent = $item["tax_percent"];
				$discount_amount = $item["discount_amount"];  
				$item_total = $item["item_total"];
				$item_tax = $item["item_tax"];
				$item_tax_total = $item["item_tax_total"];
				$price_excl_tax = $item["price_excl_tax"];
				$price_incl_tax = $item["price_incl_tax"];
				$price_excl_tax_total = $item["price_excl_tax_total"];
				$price_incl_tax_total = $item["price_incl_tax_total"];

				// points and credits
				$points_price = $item["points_price"];  
				$reward_points = $item["reward_points"];  
				$reward_credits = $item["reward_credits"];  

				$components_strings = array();
				$components = isset($item["components"]) ? $item["components"] : "";
				if ($subcomponents_show_type == 1 && is_array($components) && sizeof($components) > 0) {
					for ($c = 0; $c < sizeof($components); $c++) {
						$cc_id = $components[$c];
						$component = $orders_items[$cc_id];
						$component_id = $component["item_id"];
						$selection_name = get_translation($component["component_name"]);
						$component_name = get_translation($component["item_name"]);
						$component_price = $component["price"];
						$component_quantity = $component["quantity"];
						$component_sub_quantity = intval($component_quantity / $quantity);
						$component_item_code = $component["item_code"];
						$component_manufacturer_code = $component["manufacturer_code"];
		  
						$price += ($component["price"] * $component_sub_quantity);
						$item_total += $component["item_total"];
						$item_tax += ($component["item_tax"] * $component_sub_quantity);
						$item_tax_total += $component["item_tax_total"];
						$price_excl_tax += ($component["price_excl_tax"] * $component_sub_quantity);
						$price_incl_tax += ($component["price_incl_tax"] * $component_sub_quantity);
						$price_excl_tax_total += ($component["price_excl_tax_total"] );
						$price_incl_tax_total += ($component["price_incl_tax_total"] );
		  
						$points_price += ($component["points_price"] * $component_sub_quantity);
						$reward_points += ($component["reward_points"] * $component_sub_quantity);
						$reward_credits += ($component["reward_credits"] * $component_sub_quantity);

						$component_string = "";
						if (strlen($selection_name)) {
							$component_string .= $selection_name . ": ";
						}
						$component_string .= $component_sub_quantity . " x " . $component_name;
						if ($component_price > 0) {
							$component_string .= $option_positive_price_right . currency_format($component_price) . $option_positive_price_left;
						} elseif ($component_price < 0) {
							$component_string .= $option_negative_price_right . currency_format(abs($component_price)) . $option_negative_price_left;
						}
						$components_strings[] = $component_string;
					}
				}

				$columns["item_name"]["value"] = "";
				$columns["item_price"]["value"] = currency_format($price_excl_tax, $order_currency);
				$columns["item_tax_percent"]["value"] = $item_tax_percent . "%";
				$columns["item_tax"]["value"] = currency_format($item_tax, $order_currency);
				$columns["item_price_incl_tax"]["value"] = currency_format($price_incl_tax, $order_currency);
				$columns["item_quantity" ]["value"] = $quantity;
				$columns["item_price_total"]["value"] = currency_format($price_excl_tax_total, $order_currency);
				$columns["item_tax_total"]["value"] = currency_format($item_tax_total, $order_currency);
				$columns["item_price_incl_tax_total"]["value"] = currency_format($price_incl_tax_total, $order_currency);
		  
				$goods_total += $item_total;
				$goods_tax_total += $item_tax_total;
				$goods_total_excl_tax += $price_excl_tax_total;
				$goods_total_incl_tax += $price_incl_tax_total;

				$price_formatted = currency_format($price, $order_currency);
				$total_formatted = currency_format($item_total, $order_currency);
				$tax_formatted = currency_format($item_tax_total, $order_currency);
	  
	  
				$pdf->setfont("helvetica", "", 9);
				$fontsize = 9;
	  
				$item_height = $pdf->show_xy($item_name, $columns["item_name"]["start"] + 4, $height_position - 2, $columns["item_name"]["width"] - 6, 0);
				// set smaller font for product additional information
				$pdf->setfont("helvetica", "", 8);
				// show product code
				if ($show_item_code && strlen($item_code)) {
					$item_height += 2;
					$code_height = $pdf->show_xy(PROD_CODE_MSG .": " . $item_code, $columns["item_name"]["start"] + 4, $height_position - $item_height - 2, $columns["item_name"]["width"] - 6, 0);
					$item_height += $code_height;
				}
				// show manufacturer code
				if ($show_manufacturer_code && strlen($manufacturer_code)) {
					$item_height += 2;
					$code_height = $pdf->show_xy(MANUFACTURER_CODE_MSG .": " . $manufacturer_code, $columns["item_name"]["start"] + 4, $height_position - $item_height - 2, $columns["item_name"]["width"] - 6, 0);
					$item_height += $code_height;
				}
				// show points price
				if ($points_price > 0 && $show_points_price) {
					$item_height += 2;
					$code_height = $pdf->show_xy(POINTS_PRICE_MSG.": " . number_format($points_price, $points_decimals), $columns["item_name"]["start"] + 4, $height_position - $item_height - 2, $columns["item_name"]["width"] - 6, 0);
					$item_height += $code_height;
				}
				// show reward points 
				if ($reward_points > 0 && $show_reward_points) {
					$item_height += 2;
					$code_height = $pdf->show_xy(REWARD_POINTS_MSG.": " . number_format($reward_points, $points_decimals), $columns["item_name"]["start"] + 4, $height_position - $item_height - 2, $columns["item_name"]["width"] - 6, 0);
					$item_height += $code_height;
				}
				// show reward credits 
				if ($reward_credits > 0 && $show_reward_credits) {
					$item_height += 2;
					$code_height = $pdf->show_xy(REWARD_CREDITS_MSG.": " . currency_format($reward_credits), $columns["item_name"]["start"] + 4, $height_position - $item_height - 2, $columns["item_name"]["width"] - 6, 0);
					$item_height += $code_height;
				}
				// show components 
				for ($cs = 0; $cs < sizeof($components_strings); $cs++) {
					$item_height += 2;
					$code_height = $pdf->show_xy($components_strings[$cs], $columns["item_name"]["start"] + 4, $height_position - $item_height - 2, $columns["item_name"]["width"] - 6, 0);
					$item_height += $code_height;
				}

				// return original font for product information
				$pdf->setfont("helvetica", "", 9);
		
				// show product image
				$item_image = "";
				if ($item_image_column && $image_field) { 
					$sql  = " SELECT " . $image_field; 
					$sql .= " FROM " . $table_prefix . "items";
					$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);				
					$dbi->query($sql);			
					$image_exists = false;
					if ($dbi->next_record()) {
						$item_image = $dbi->f($image_field);
						if (!strlen($item_image)) {
							$item_image = $product_no_image;
						} else {
							$image_exists = true;
						}
					}
				}
				$item_image_tmp_created = false;
				if ($item_image) {
					$pos = strrpos($item_image, '.');
					if (!$pos) {
						$item_image_type = "jpg";
					}
					$item_image_type = substr($item_image, $pos+1);
					
					$item_image_tmp_name = $item_image_tmp_dir . $item_id . '-4pdf.' . $item_image_type;
					$item_image = str_replace($settings['site_url'], '', $item_image);
					if (preg_match("/^http\:\/\//", $item_image)) {
						$item_image  = "";
					} else {						
						if ($site_url && $image_exists && ($watermark || $restrict_products_images)) {
							if ($item_image_tmp_dir) {
								if (!file_exists($item_image_tmp_name)) {
									$item_image = $site_url . "image_show.php?item_id=".$item_id."&type=".$image_type_name."&vc=".md5($item_image);
									$out = fopen($item_image_tmp_name, 'wb');
									$item_image_tmp_created = true;
									if (function_exists("curl_init") && $out) {	
									    $ch = curl_init();
									    curl_setopt($ch, CURLOPT_FILE, $out);
									    curl_setopt($ch, CURLOPT_HEADER, 0);
									    curl_setopt($ch, CURLOPT_URL, $item_image);
										curl_exec($ch);
										if (curl_errno($ch)) {
											$item_image = "";
										} else {
											$item_image = $item_image_tmp_name;
										}
										curl_close($ch);
										fclose($out);
									} else {
										$item_image = "";
									}
								} else {
									$item_image  = $item_image_tmp_name;
								}
							} else {
								$item_image = "";
							}
						} else {
							if ($is_admin_path) {
								$item_image  = $root_folder_path . $item_image;
							}
						}
					}
				}
				$item_height += 6;	  
				$pdf->setfont("helvetica", "", 8);
				if ($item_image && $item_image_position == 1) {
					$image_size = @getimagesize($item_image);
					$item_height += $image_size[1];
					$pdf->place_image($item_image, 40, $height_position - $image_size[1], $item_image_type);	
				}
				foreach ($columns as $column_name => $values) {
					if ($values["active"] && strlen($values["value"])) {
						$pdf->show_xy($values["value"], $values["start"]+2, $height_position - 2, $values["width"]-4, 0, $values["align"]);
					}
				}
				$height_position -= $item_height;
				
				$pdf->setfont("helvetica", "", 8);
				$properties_height = 0;
				
								
				if ($item_image && $item_image_position == 2) {
					$image_size = @getimagesize($item_image);
					$properties_height += $image_size[1];
					$pdf->place_image($item_image, 40, $height_position - $image_size[1], $item_image_type);
				}
				if ($item_image_tmp_created) {
					@unlink($item_image_tmp_name);
				}
	  
				$sql  = " SELECT property_name, property_value FROM " . $table_prefix . "orders_items_properties ";
				$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
				$dbi->query($sql);
				while ($dbi->next_record()) {
					$property_name = strip_tags(get_translation($dbi->f("property_name")));
					$property_value = strip_tags(get_translation($dbi->f("property_value")));
					$property_line = $property_name . ": " . $property_value;
					$property_height = $pdf->show_xy($property_line, $columns["item_name"]["start"] + 4, $height_position - $properties_height + 2, $columns["item_name"]["width"] - 14, 0);
					$properties_height += $property_height;
				}
				if ($properties_height > 0) {
					$properties_height += 2;
					$height_position -= $properties_height;
				}
					  
				$pdf->setlinewidth(1.0);
				$pdf->rect (40, $height_position, 515, $item_height + $properties_height);
				foreach ($columns as $column_name => $values) {
					if ($values["active"]) {
						$pdf->line( $values["start"], $height_position, $values["start"], $height_position + $item_height + $properties_height);
					}
				}
			}

			// set total fields
			$height_position -= 14;
			$goods_total_formatted = currency_format($goods_total, $order_currency);
			$goods_tax_formatted = currency_format($goods_tax_total, $order_currency);

			$total_name_width = $columns["item_name"]["width"] + $columns["item_quantity"]["width"];
			$total_name_width+= $columns["item_price"]["width"] + $columns["item_tax_percent"]["width"];
			$total_name_width+= $columns["item_tax"]["width"] + $columns["item_price_incl_tax"]["width"];

			$pdf->setfont("helvetica", "B", 8);
			$pdf->rect (40, $height_position, 515, 14);
			$pdf->show_xy(GOODS_TOTAL_MSG, 40, $height_position + 14, $total_name_width - 5, 12, "right");
	  
			if ($item_price_total_column) {
				$pdf->line( $columns["item_price_total"]["start"], $height_position, $columns["item_price_total"]["start"], $height_position + 14);
				$pdf->show_xy(currency_format($goods_total_excl_tax, $order_currency), $columns["item_price_total"]["start"], $height_position + 14, $columns["item_price_total"]["width"] - 2, 12, "right");
			}
			if ($item_tax_total_column) {
				$pdf->line( $columns["item_tax_total"]["start"], $height_position, $columns["item_tax_total"]["start"], $height_position + 14);
				$pdf->show_xy(currency_format($goods_tax_total, $order_currency), $columns["item_tax_total"]["start"], $height_position + 14, $columns["item_tax_total"]["width"] - 2, 12, "right");
			}
			if ($item_price_incl_tax_total_column) {
				$pdf->line( $columns["item_price_incl_tax_total"]["start"], $height_position, $columns["item_price_incl_tax_total"]["start"], $height_position + 14);
				$pdf->show_xy(currency_format($goods_total_incl_tax, $order_currency), $columns["item_price_incl_tax_total"]["start"], $height_position + 14, $columns["item_price_incl_tax_total"]["width"] - 2, 12, "right");
			}

			$height_position -= 6;
			$desc_length = $total_name_width - 5;
			$price_start = $total_name_width + 40;
			if ($average_width > 50) {
				$price_length = $average_width - 1;
			} else {
				$price_length = 49;
			}
	  
			if ($total_discount > 0) {
				if(!$tax_prices_type){
					$total_discount += $total_discount_tax;
				}
				$total_discount_formatted = "-" . currency_format($total_discount, $order_currency);
				$pdf->show_xy(TOTAL_DISCOUNT_MSG, 40, $height_position, $desc_length, 0, "right");
				$pdf->show_xy($total_discount_formatted, $price_start, $height_position, $price_length, 0, "right");
				$height_position -= 12;
			}
	  
			$pdf->setfont("helvetica", "", 8);
	  
			foreach ($cart_properties as $property_id => $property_values) {
				$property_name = strip_tags($property_values["name"]);
				$property_value = strip_tags($property_values["value"]);
				$property_price = $property_values["price"];
				$property_tax = $property_values["tax"];
				$property_price_excl_tax = $property_values["property_price_excl_tax"];
				$property_price_incl_tax = $property_values["property_price_incl_tax"];

				$property_tax_free = $property_values["tax_free"];
				$property_line  = $property_name . " (" . $property_value . ")";
				if (strlen($property_price)){
					$price_formatted = currency_format($property_price, $order_currency);
				} else {
					$price_formatted = "";
				}
	  
				$property_height = $pdf->show_xy($property_line, 40, $height_position, $desc_length, 0, "right");
				if ($item_price_total_column && $property_price_excl_tax) {
					$pdf->show_xy(currency_format($property_price_excl_tax, $order_currency), $columns["item_price_total"]["start"], $height_position, $columns["item_price_total"]["width"] - 2, 0, "right");
				}
				if ($item_tax_total_column && $property_tax) {
					$pdf->show_xy(currency_format($property_tax, $order_currency), $columns["item_tax_total"]["start"], $height_position, $columns["item_tax_total"]["width"] - 2, 0, "right");
				}
				if ($item_price_incl_tax_total_column && $property_price_incl_tax) {
					$pdf->show_xy(currency_format($property_price_incl_tax, $order_currency), $columns["item_price_incl_tax_total"]["start"], $height_position, $columns["item_price_incl_tax_total"]["width"] - 2, 0, "right");
				}
				$height_position -= ($property_height + 4);
			}
	    
			if ($shipping_cost > 0 || strlen($shipping_type_desc)) {
				if ($item_price_total_column) {
					$pdf->show_xy(currency_format($shipping_cost_excl_tax, $order_currency), $columns["item_price_total"]["start"], $height_position, $columns["item_price_total"]["width"] - 2, 0, "right");
				}
				if ($item_tax_total_column) {
					$pdf->show_xy(currency_format($shipping_tax_total, $order_currency), $columns["item_tax_total"]["start"], $height_position, $columns["item_tax_total"]["width"] - 2, 0, "right");
				}
				if ($item_price_incl_tax_total_column) {
					$pdf->show_xy(currency_format($shipping_cost_incl_tax, $order_currency), $columns["item_price_incl_tax_total"]["start"], $height_position, $columns["item_price_incl_tax_total"]["width"] - 2, 0, "right");
				}
				$pdf->show_xy($shipping_type_desc, 40, $height_position, $desc_length, 0, "right");
				$height_position -= 12;
			}
	  
			$pdf->setfont("helvetica", "B", 8);
			$height_position -= 10;
			$taxes_total = 0;
			// calculate the tax
			if ($tax_available) {
				foreach($order_tax_rates as $tax_id => $tax_info) {
					$tax_name = $tax_info["tax_name"];
					$current_tax_free = isset($tax_info["tax_free"]) ? $tax_info["tax_free"] : 0;
					//if ($tax_free) { $current_tax_free = true; }
					$tax_percent = $tax_info["tax_percent"];
					$shipping_tax_percent = $tax_info["shipping_tax_percent"];
					$tax_types = $tax_info["types"];
					$tax_cost = isset($tax_info["tax_total"]) ? $tax_info["tax_total"] : 0;
					$taxes_total += va_round($tax_cost, $currency["decimals"]);

					$tax_cost_formatted = currency_format($tax_cost, $order_currency);
					$pdf->show_xy($tax_name, 40, $height_position, $desc_length, 0, "right");
					$pdf->show_xy($tax_cost_formatted, $price_start, $height_position, $price_length, 0, "right");
					$height_position -= 12;
				}
			}
	  
			if ($credit_amount != 0) {
				$credit_amount_formatted = "-".currency_format($credit_amount, $order_currency);
				$pdf->show_xy("Credit Amount", 40, $height_position, $desc_length, 0, "right");
				$pdf->show_xy($credit_amount_formatted, $price_start, $height_position, $price_length, 0, "right");
				$height_position -= 12;
			}
	  
			if ($processing_fee != 0) {
				$processing_fee_formatted = currency_format($processing_fee, $order_currency);
				$pdf->show_xy(PROCESSING_FEE_MSG, 40, $height_position, $desc_length, 0, "right");
				$pdf->show_xy($processing_fee_formatted, $price_start, $height_position, $price_length, 0, "right");
				$height_position -= 12;
			}
	  
			$height_position -= 12;
			$order_total_formatted = currency_format($order_total, $order_currency);
			$pdf->setfont("helvetica", "BU", 8);
			$pdf->show_xy(PROD_TOTAL_COLUMN, 40, $height_position, $desc_length, 0, "right");
			$pdf->show_xy($order_total_formatted, $price_start, $height_position, $price_length, 0, "right");
			$height_position -= 12;
	  
			set_invoice_footer($pdf, $height_position, $invoice);
			$pdf->end_page();
			// end of current order generation
		}
	
		$pdf_buffer = $pdf->get_buffer();
		return $pdf_buffer;
	}

	function begin_new_page(&$pdf, &$height_position, &$page_number)
	{
		$page_number++;
		$pdf->begin_page(595, 842);
		$height_position = 800;
	
		$pdf->setfont ("helvetica", "", 8);
		if ($page_number > 1) {
			//$pdf->show_xy("- " . $page_number . " -", 40, 20, 555, 0, "center");
		}
	}
	
	function set_invoice_header(&$pdf, &$height_position, $invoice, $r)
	{
		global $date_show_format;
	
		$order_id = $r->get_value("order_id");
		$invoice_number = $r->get_value("invoice_number");
		if (!$invoice_number) { $invoice_number = $order_id; }
		$order_placed_date = $r->get_value("order_placed_date");
		$order_date = va_date($date_show_format, $order_placed_date);
	
		$image_height = 0;
		$start_position = $height_position;
		if (isset($invoice["invoice_logo"]) && strlen($invoice["invoice_logo"])) {
			$image_path = $invoice["invoice_logo"];
			if (!file_exists($image_path)) {
				if (preg_match("/^\.\.\//", $image_path)) {
					if (@file_exists(preg_replace("/^\.\.\//", "", $image_path))) {
						$image_path = preg_replace("/^\.\.\//", "", $image_path);
					}
				} else if (@file_exists("../".$image_path)) {
					$image_path = "../" . $image_path;
				}
			}
			$image_size = @getimagesize($image_path);
			$image_width = $image_size[0];
			$image_height = $image_size[1];
			if ($image_width > 0 && $image_height > 0) {
				if (preg_match("/((\.jpeg)|(\.jpg))$/i", $image_path)) {
					$image_type = "jpeg";
				} elseif (preg_match("/(\.gif)$/i", $image_path)) {
					$image_type = "gif";
				} elseif (preg_match("/((\.tif)|(\.tiff))$/i", $image_path)) {
					$image_type = "tiff";
				} elseif (preg_match("/(\.png)$/i", $image_path)) {
					$image_type = "png";
				}
			  $pdf->place_image($image_path, 555 - $image_width, $height_position - $image_height, $image_type);
			}
		}	
	
		if (isset($invoice["invoice_header"])) {
			$invoice_header = strip_tags($invoice["invoice_header"]);
			if (strlen($invoice_header)) {
				$pdf->setfont("helvetica", "", 10);
				$header_lines = explode("\n", $invoice_header);
				for ($i = 0; $i < sizeof($header_lines); $i++) {
					$header_line = $header_lines[$i];
					$line_height = $pdf->show_xy($header_line, 40, $height_position, 200, 0, "left");
					$height_position -= ($line_height + 2);
				}
			}
		}
	
		$height_position -= 12;
		$pdf->setfont("helvetica", "B", 10);
		$pdf->show_xy(INVOICE_DATE_MSG . ":", 40, $height_position, 80, 0, "left");
		$pdf->setfont("helvetica", "", 10);
		$pdf->show_xy($order_date, 125, $height_position, 200, 0, "left");
	
		$height_position -= 12;
		$pdf->setfont("helvetica", "B", 10);
		$pdf->show_xy(INVOICE_NUMBER_MSG . ":", 40, $height_position, 80, 0, "left");
		$pdf->setfont("helvetica", "", 10);
		$pdf->show_xy($invoice_number, 125, $height_position, 200, 0, "left");
	
		if ($height_position > ($start_position - $image_height)) {
			$height_position = $start_position - $image_height;
		}
	
	}
	
	function set_table_header(&$pdf, &$height_position, $r, $columns)
	{
		$tax_name = $r->get_value("tax_name");
		$tax_percent = $r->get_value("tax_percent");
	
		$pdf->setlinewidth(1.0);
	
		$pdf->setfont("helvetica", "B", 8);
		$height_position -= 12;
	
		$max_height = 12;
		foreach ($columns as $column_name => $values) {
			if ($values["active"]) {
				$column_height = $pdf->show_xy($values["name"], $values["start"] + 1, $height_position - 2, $values["width"] - 2, 0, "center");
				if ($column_height > $max_height) {
					$max_height = $column_height;
				}
			}
		}
		$max_height += 6;
		$pdf->rect ( 40, $height_position - $max_height, 515, $max_height);
		foreach ($columns as $column_name => $values) {
			if ($values["active"]) {
				$pdf->line( $values["start"], $height_position - $max_height, $values["start"], $height_position);
			}
		}
		$height_position -= $max_height;
	}
	
	
	
	function set_user_info(&$pdf, &$height_position, $r, $personal_number, $delivery_number, $personal_properties, $delivery_properties, $currency)
	{
		$property_tax_percent = $r->get_value("tax_percent");

		$pdf->setfont("helvetica", "BU", 10);
		$height_position -= 24;

		$pdf->show_xy(INVOICE_TO_MSG.":", 40, $height_position, 250, 0, "left");
		if ($delivery_number > 0) {
			$pdf->show_xy(DELIVERY_TO_MSG.":", 300, $height_position, 250, 0, "left");
		}
	
		$personal_height = $height_position;
		$pdf->setfont("helvetica", "", 10);
	
		$name = "";
		if ($r->parameters["name"][SHOW]) {
			$name = $r->get_value("name");
		}
		if ($r->parameters["first_name"][SHOW] && $r->get_value("first_name")) {
			if ($name) { $name .= " "; }
			$name .= $r->get_value("first_name");
		}
		if ($r->parameters["last_name"][SHOW] && $r->get_value("last_name")) {
			if ($name) { $name .= " "; }
			$name .= $r->get_value("last_name");
		}
	
		if (strlen($name)) {
			$personal_height -= 12;
			$pdf->show_xy($name, 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["company_id"][SHOW] && $r->get_value("company_id")) {
			$personal_height -= 12;
			$pdf->show_xy($r->get_value("company_id"), 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["company_name"][SHOW] && $r->get_value("company_name")) {
			$personal_height -= 12;
			$pdf->show_xy($r->get_value("company_name"), 40, $personal_height, 250, 0, "left");
		}
	
		if ($r->parameters["address1"][SHOW] && $r->get_value("address1")) {
			$personal_height -= 12;
			$pdf->show_xy($r->get_value("address1"), 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["address2"][SHOW] && $r->get_value("address2")) {
			$personal_height -= 12;
			$pdf->show_xy($r->get_value("address2"), 40, $personal_height, 250, 0, "left");
		}
	
		$city = ""; $address_line = "";
		if ($r->parameters["city"][SHOW]) {
			$city = $r->get_value("city");
		}
		if ($r->parameters["province"][SHOW]) {
			$address_line = $r->get_value("province");
		}
		if ($r->parameters["state_id"][SHOW] && $r->get_value("state_id")) {
			if ($address_line) { $address_line .= " "; }
			$address_line .= $r->get_value("state_id");
		}
		if ($r->parameters["zip"][SHOW] && $r->get_value("zip")) {
			if ($address_line) { $address_line .= " "; }
			$address_line .= $r->get_value("zip");
		}
		if ($city && $address_line) {
			$address_line = $city . ", " . $address_line;
		} elseif ($city) {
			$address_line = $city;
		}
	
		if (strlen($address_line)) {
			$personal_height -= 12;
			$pdf->show_xy($address_line, 40, $personal_height, 250, 0, "left");
		}
	
		if ($r->parameters["country_id"][SHOW] && $r->get_value("country_id")) {
			$personal_height -= 12;
			$pdf->show_xy($r->get_value("country_id"), 40, $personal_height, 250, 0, "left");
		}
	
		if ($r->parameters["phone"][SHOW] && !$r->is_empty("phone")) {
			$personal_height -= 12;
			$pdf->show_xy(PHONE_FIELD.": ".$r->get_value("phone"), 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["daytime_phone"][SHOW] && !$r->is_empty("daytime_phone")) {
			$personal_height -= 12;
			$pdf->show_xy(DAYTIME_PHONE_FIELD.": ".$r->get_value("daytime_phone"), 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["evening_phone"][SHOW] && !$r->is_empty("evening_phone")) {
			$personal_height -= 12;
			$pdf->show_xy(EVENING_PHONE_FIELD.": ".$r->get_value("evening_phone"), 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["cell_phone"][SHOW] && !$r->is_empty("cell_phone")) {
			$personal_height -= 12;
			$pdf->show_xy(CELL_PHONE_FIELD.": ".$r->get_value("cell_phone"), 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["fax"][SHOW] && !$r->is_empty("fax")) {
			$personal_height -= 12;
			$pdf->show_xy(FAX_FIELD.": ".$r->get_value("fax"), 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["email"][SHOW] && strlen($r->get_value("email"))) {
			$personal_height -= 12;
			$pdf->show_xy(EMAIL_FIELD.": " . $r->get_value("email"), 40, $personal_height, 250, 0, "left");
		}
	
		foreach ($personal_properties as $property_id => $property_values) {
			$property_price = $property_values["price"];
			$property_tax = get_tax_amount("", 0, $property_price, $property_values["tax_free"], $property_tax_percent);
			if (floatval($property_price) != 0.0) {
				$property_price_text = " " . currency_format($property_price, $currency, $property_tax);
			} else {
				$property_price_text = "";
			}
			$property_height = $pdf->show_xy($property_values["name"] . ": " . $property_values["value"] . $property_price_text, 40, $personal_height - 12, 250, 0, "left");
			$personal_height -= $property_height;
		}
	
	
		$delivery_height = $height_position;
	
		$delivery_name = "";
		if ($r->parameters["delivery_name"][SHOW]) {
			$delivery_name = $r->get_value("delivery_name");
		}
		if ($r->parameters["delivery_first_name"][SHOW] && $r->get_value("delivery_first_name")) {
			if ($delivery_name) { $delivery_name .= " "; }
			$delivery_name .= $r->get_value("delivery_first_name");
		}
		if ($r->parameters["delivery_last_name"][SHOW] && $r->get_value("delivery_last_name")) {
			if ($delivery_name) { $delivery_name .= " "; }
			$delivery_name .= $r->get_value("delivery_last_name");
		}
	
		if (strlen($delivery_name)) {
			$delivery_height -= 12;
			$pdf->show_xy($delivery_name, 300, $delivery_height, 250, 0, "left");
		}
	
		if ($r->parameters["delivery_company_id"][SHOW] && $r->get_value("delivery_company_id")) {
			$delivery_height -= 12;
			$pdf->show_xy($r->get_value("delivery_company_id"), 300, $delivery_height, 250, 0, "left");
		}
		if ($r->parameters["delivery_company_name"][SHOW] && $r->get_value("delivery_company_name")) {
			$delivery_height -= 12;
			$pdf->show_xy($r->get_value("delivery_company_name"), 300, $delivery_height, 250, 0, "left");
		}
	
		if ($r->parameters["delivery_address1"][SHOW] && $r->get_value("delivery_address1")) {
			$delivery_height -= 12;
			$pdf->show_xy($r->get_value("delivery_address1"), 300, $delivery_height, 250, 0, "left");
		}
		if ($r->parameters["delivery_address2"][SHOW] && $r->get_value("delivery_address2")) {
			$delivery_height -= 12;
			$pdf->show_xy($r->get_value("delivery_address2"), 300, $delivery_height, 250, 0, "left");
		}
	
		$delivery_city = ""; $delivery_address = "";
		if ($r->parameters["delivery_city"][SHOW]) {
			$delivery_city = $r->get_value("delivery_city");
		}
		if ($r->parameters["delivery_province"][SHOW]) {
			$delivery_address = $r->get_value("delivery_province");
		}
		if ($r->parameters["delivery_state_id"][SHOW] && $r->get_value("delivery_state_id")) {
			if ($delivery_address) { $delivery_address .= " "; }
			$delivery_address .= $r->get_value("delivery_state_id");
		}
		if ($r->parameters["delivery_zip"][SHOW] && $r->get_value("delivery_zip")) {
			if ($delivery_address) { $delivery_address .= " "; }
			$delivery_address .= $r->get_value("delivery_zip");
		}
		if ($delivery_city && $delivery_address) {
			$delivery_address = $delivery_city . ", " . $delivery_address;
		} elseif ($delivery_city) {
			$delivery_address = $delivery_city;
		}
	
		if (strlen($delivery_address)) {
			$delivery_height -= 12;
			$pdf->show_xy($delivery_address, 300, $delivery_height, 250, 0, "left");
		}
	
		if ($r->parameters["delivery_country_id"][SHOW] && $r->get_value("delivery_country_id")) {
			$delivery_height -= 12;
			$pdf->show_xy($r->get_value("delivery_country_id"), 300, $delivery_height, 250, 0, "left");
		}
	
		if ($r->parameters["delivery_phone"][SHOW] && !$r->is_empty("delivery_phone")) {
			$delivery_height -= 12;
			$pdf->show_xy(PHONE_FIELD.": ".$r->get_value("delivery_phone"), 300, $delivery_height, 250, 0, "left");
		}
		if ($r->parameters["delivery_daytime_phone"][SHOW] && !$r->is_empty("delivery_daytime_phone")) {
			$delivery_height -= 12;
			$pdf->show_xy(DAYTIME_PHONE_FIELD.": ".$r->get_value("delivery_daytime_phone"), 300, $delivery_height, 250, 0, "left");
		}
		if ($r->parameters["delivery_evening_phone"][SHOW] && !$r->is_empty("delivery_evening_phone")) {
			$delivery_height -= 12;
			$pdf->show_xy(EVENING_PHONE_FIELD.": ".$r->get_value("delivery_evening_phone"), 300, $delivery_height, 250, 0, "left");
		}
		if ($r->parameters["delivery_cell_phone"][SHOW] && !$r->is_empty("delivery_cell_phone")) {
			$delivery_height -= 12;
			$pdf->show_xy(CELL_PHONE_FIELD.": ".$r->get_value("delivery_cell_phone"), 300, $delivery_height, 250, 0, "left");
		}
		if ($r->parameters["delivery_fax"][SHOW] && !$r->is_empty("delivery_fax")) {
			$delivery_height -= 12;
			$pdf->show_xy(FAX_FIELD.": ".$r->get_value("delivery_fax"), 300, $delivery_height, 250, 0, "left");
		}
	
		if ($r->parameters["delivery_email"][SHOW] && strlen($r->get_value("delivery_email"))) {
			$delivery_height -= 12;
			$pdf->show_xy(EMAIL_FIELD.": " . $r->get_value("delivery_email"), 300, $delivery_height, 250, 0, "left");
		}
	
		foreach ($delivery_properties as $property_id => $property_values) {
			$property_price = $property_values["price"];
			$property_tax = get_tax_amount("", 0, $property_price, $property_values["tax_free"], $property_tax_percent);
			if (floatval($property_price) != 0.0) {
				$property_price_text = " " . currency_format($property_price, $currency, $property_tax);
			} else {
				$property_price_text = "";
			}
			$property_height = $pdf->show_xy($property_values["name"] . ": " . $property_values["value"] . $property_price_text, 300, $delivery_height - 12, 250, 0, "left");
			$delivery_height -= $property_height;
		}
	
	
		if ($personal_height > $delivery_height) {
			$height_position = $delivery_height;
		} else {
			$height_position = $personal_height;
		}
		$height_position -= 12;
	
	}
	
	
	function set_invoice_footer(&$pdf, &$height_position, $invoice)
	{
		$pdf->setfont("helvetica", "", 8);

		if (isset($invoice["invoice_footer"])) {
			$invoice_footer = strip_tags($invoice["invoice_footer"]);
			if (strlen($invoice_footer)) {
				$footer_lines = explode("\n", $invoice_footer);
				$height_position = 40 + sizeof($footer_lines) * 10;
				for ($i = 0; $i < sizeof($footer_lines); $i++) {
					$height_position -= 10;
					$footer_line = $footer_lines[$i];
					$pdf->show_xy($footer_line, 40, $height_position, 555, 0, "center");
				}
			}
		}
	
	}

?>