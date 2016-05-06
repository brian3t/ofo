<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  order_items_properties.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	function order_items_properties($cart_id, $item, $parent_cart_id, $is_bundle, $discount_applicable = true, $properties_discount = 0, $parent_properties_info = array())
	{
	 	global $t, $db, $table_prefix;
		global $settings, $tax_rates, $default_tax_rates, $currency;
		global $shopping_cart; // shopping cart variables
		global $options_code, $options_manufacturer_code; 
		global $downloads, $properties_ids; 
		global $sc_errors; // errors about required properties
		global $properties_info; // array where all the option data will be saved
		global $properties_values, $properties_values_text, $properties_values_html; // text variables for showing option data
		global $additional_price, $additional_real_price, $options_buying_price, $additional_weight; // variables for adding to product totals

		$item_id = $item["item_id"];
		$item_type_id = $item["item_type_id"];
		$item_price = $item["price"];
		$item_tax_free = $item["tax_free"];
		$item_name = $item["item_name"];
		$item_code = $item["item_code"];
		$manufacturer_code = $item["manufacturer_code"];
		$options_downloads = array();
		$downloads = isset($item["parent_downloads"]) ? $item["parent_downloads"] : array();

		$eol = get_eol();
		$operation = get_param("operation");
		$is_update = strlen($operation);

		$tax_prices = get_setting_value($settings, "tax_prices", 0);
		$tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
		$price_type = get_session("session_price_type");
		if ($price_type == 1) {
			$additional_price_field = "trade_additional_price";
		} else {
			$additional_price_field = "additional_price";
		}
		$user_discount_type = get_session("session_discount_type");
		$user_discount_amount = get_session("session_discount_amount");

		// option price options
		$option_positive_price_right = get_setting_value($settings, "option_positive_price_right", ""); 
		$option_positive_price_left = get_setting_value($settings, "option_positive_price_left", ""); 
		$option_negative_price_right = get_setting_value($settings, "option_negative_price_right", ""); 
		$option_negative_price_left = get_setting_value($settings, "option_negative_price_left", "");

		if (is_array($parent_properties_info) && sizeof($parent_properties_info) > 0) {
			for ($p = 0; $p < sizeof($parent_properties_info); $p++) {
				list ($property_id, $control_type, $property_name_initial, $values_list, $pr_add_price, $pr_add_weight, $pr_values, $property_order) = $parent_properties_info[$p];
				$properties_info[] = array ($property_id, $control_type, $property_name_initial, $values_list, 0, $pr_add_weight, $pr_values, $property_order);
				$property_name = get_translation($property_name_initial);
				$properties_values .= "<br>" . $property_name . ": " . $values_list; 
				$properties_values_text .= $property_name .": " . $values_list; 
				$properties_values_html .= "<br>" . $property_name . ": " . $values_list;
			}
		}
		$pr_rows = array(); 
		$sql  = " SELECT * ";
		$sql .= " FROM " . $table_prefix . "items_properties ";
		$sql .= " WHERE (item_id=" . $db->tosql($item_id, INTEGER) . " OR item_type_id=" . $db->tosql($item_type_id, INTEGER) . ") ";
		$sql .= " AND property_type_id=1 ";
		$sql .= " ORDER BY use_on_checkout, property_order, property_id ";
		$db->query($sql);
		if ($db->next_record()) {
			do {
				$property_id = $db->f("property_id");
				$option = array(
					"property_id" => $db->f("property_id"),
					"property_type_id" => $db->f("property_type_id"),
					"property_order" => $db->f("property_order"),
					"usage_type" => $db->f("usage_type"),
					"property_name" => $db->f("property_name"),
					"parent_property_id" => $db->f("parent_property_id"),
					"parent_value_id" => $db->f("parent_value_id"),
					"property_description" => $db->f("property_description"),
					"property_style" => $db->f("property_style"),
					"property_price_type" => $db->f("property_price_type"),
					"property_price" => $db->f($additional_price_field),
					"free_price_type" => $db->f("free_price_type"),
					"free_price_amount" => $db->f("free_price_amount"),
					"max_limit_type" => $db->f("max_limit_type"),
					"max_limit_length" => $db->f("max_limit_length"),
					"control_type" => $db->f("control_type"),
					"control_style" => $db->f("control_style"),
					"required" => $db->f("required"),
					"use_on_checkout" => $db->f("use_on_checkout"),
					"start_html" => $db->f("start_html"),
					"middle_html" => $db->f("middle_html"),
					"before_control_html" => $db->f("before_control_html"),
					"after_control_html" => $db->f("after_control_html"),
					"end_html" => $db->f("end_html"),
					"onchange_code" => $db->f("onchange_code"),
					"onclick_code" => $db->f("onclick_code"),
					"control_code" => $db->f("control_code"),
				);
        $pr_rows[$property_id] = $option;
			} while ($db->next_record());
		}

		foreach ($pr_rows as $property_id => $option) {
			if ($option["usage_type"] == 2 || $option["usage_type"] == 3) {
				$sql  = " SELECT item_id FROM " . $table_prefix . "items_properties_assigned ";
				$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
				$sql .= " AND property_id=" . $db->tosql($property_id, INTEGER);
				$db->query($sql);
				if (!$db->next_record()) {
					// remove option if it wasn't assigned to product
					unset($pr_rows[$property_id]);
				}
			}
		}

		if (sizeof($pr_rows) > 0) {
			foreach ($pr_rows as $property_id => $option) 
			{
				$property_id = $option["property_id"];
				$property_type_id = $option["property_type_id"];
				$property_order = $option["property_order"];
				$property_name_initial = $option["property_name"];
				$property_name = get_translation($property_name_initial);
				$property_description = $option["property_description"];
				$parent_property_id = $option["parent_property_id"];
				$parent_value_id = $option["parent_value_id"];
				$property_price_type = $option["property_price_type"];
				$property_price = $option["property_price"];
				$free_price_type = $option["free_price_type"];
				$free_price_amount = $option["free_price_amount"];
				$max_limit_type = $option["max_limit_type"];
				$max_limit_length = $option["max_limit_length"];
				$control_type = $option["control_type"];
				$control_style = $option["control_style"];
				$property_required = $option["required"];
				$use_on_checkout = $option["use_on_checkout"];
				$start_html = $option["start_html"];
				$middle_html = $option["middle_html"];
				$before_control_html = $option["before_control_html"];
				$after_control_html = $option["after_control_html"];
				$end_html = $option["end_html"];
				$onchange_code = $option["onchange_code"];
				$onclick_code = $option["onclick_code"];
				$control_code = $option["control_code"];

				$properties = "";
				if (strlen($parent_cart_id)) {
					$cp_id = $cart_id . "_" . $property_id;
					if (isset($shopping_cart[$parent_cart_id]["COMPONENTS_PROPERTIES"][$cart_id])) {
						$properties = $shopping_cart[$parent_cart_id]["COMPONENTS_PROPERTIES"][$cart_id];
					}
				} else {
					$cp_id = $cart_id . "_" . $property_id;
					$properties = $shopping_cart[$cart_id]["PROPERTIES"];
				}

				$property_value_param = ""; $property_value_params = array(); $property_value_texts = array();
				$property_value = ""; $pr_add_weight = 0; $pr_add_price = 0; $pr_add_real_price = 0; 
				$pr_buy_price = 0; $pr_values = array(); 
				if ($use_on_checkout && !$is_bundle) {
					if ($is_update) {
						if (strtoupper($control_type) == "CHECKBOXLIST") {
							$property_total = get_param("property_total_" . $cp_id);
							for ($i = 1; $i <= $property_total; $i++) {
								$checkbox_value = get_param("property_" . $cp_id . "_" . $i);
								if ($checkbox_value) { $property_value_params[] = $checkbox_value; }
							}
						} else if (strtoupper($control_type) == "TEXTBOXLIST") {
							$property_total = get_param("property_total_" . $cp_id);
							for ($i = 1; $i <= $property_total; $i++) {
								$value_id = get_param("property_value_" . $cp_id . "_" . $i);
								$value_text = get_param("property_" . $cp_id . "_" . $i);
								if (strlen($value_text)) { 
									$property_value_params[] = $value_id; 
									$property_value_texts[$value_id] = $value_text; 
								}
							}
						} else {
							$property_value_param = get_param("property_" . $cp_id);
							$property_value_params = array($property_value_param);
							if (strlen($property_value_param) && (strtoupper($control_type) == "TEXTBOX" || strtoupper($control_type) == "TEXTAREA")) {
								$property_value_texts[$property_value_param] = $property_value_param; 
							}
						}
					} else if (is_array($properties) && isset($properties[$property_id])) {
						$property_value_param  = isset($properties[$property_id][0]) ? $properties[$property_id][0] : "";
						$property_value_params = $properties[$property_id];
						if (strlen($parent_cart_id)) {
							if (isset($shopping_cart[$parent_cart_id]["COMPONENTS_PROPERTIES_TEXT"]) && 
								isset($shopping_cart[$parent_cart_id]["COMPONENTS_PROPERTIES_TEXT"][$cart_id])) {
								$property_value_texts = $shopping_cart[$parent_cart_id]["COMPONENTS_PROPERTIES_TEXT"][$cart_id][$property_id];
							}
						} else {
							$property_value_texts  = $shopping_cart[$cart_id]["PROPERTIES_INFO"][$property_id]["TEXT"];
						}
					}
					// check and add control price if available
					$control_price = calculate_control_price($property_value_params, $property_value_texts, $property_price_type, $property_price, $free_price_type, $free_price_amount);
					$pr_add_price += $control_price;
					$additional_price += $control_price;
					$additional_real_price += $control_price;

					if($properties_ids) $properties_ids .= ",";
					$properties_ids .= $cp_id;
					$property_control  = "<input type=\"hidden\" name=\"property_name_" . $cp_id . "\"";
					$property_control .= " value=\"" . htmlspecialchars($property_name) . "\">";
					$property_control .= "<input type=\"hidden\" name=\"property_required_" . $cp_id . "\"";
					$property_control .= " value=\"" . intval($property_required) . "\">";
					$property_control .= "<input type=\"hidden\" name=\"property_control_" . $cp_id . "\"";
					$property_control .= " value=\"" . strtoupper($control_type) . "\">";
					$property_control .= "<input type=\"hidden\" name=\"property_parent_id_" . $cp_id. "\"";
					$property_control .= " value=\"" . $parent_property_id . "\">";
					$property_control .= "<input type=\"hidden\" name=\"property_parent_value_id_" . $cp_id. "\"";
					$property_control .= " value=\"" . $parent_value_id . "\">";
					$property_control .= "<input type=\"hidden\" name=\"property_price_type_" . $cp_id. "\"";
					$property_control .= " value=\"" . $property_price_type . "\">";
					$property_control .= "<input type=\"hidden\" name=\"property_price_" . $cp_id. "\"";
					$property_control .= " value=\"" . $property_price . "\">";
					$property_control .= "<input type=\"hidden\" name=\"property_free_price_type_" . $cp_id. "\"";
					$property_control .= " value=\"" . $free_price_type . "\">";
					$property_control .= "<input type=\"hidden\" name=\"property_free_price_amount_" . $cp_id. "\"";
					$property_control .= " value=\"" . $free_price_amount . "\">";


					if (strtoupper($control_type) == "LISTBOX") {
						$properties_options = "<option value=\"\">" . SELECT_MSG . " " . $property_name . "</option>" . $eol;
						$sql  = " SELECT * FROM " . $table_prefix . "items_properties_values ";
						$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER) . " AND hide_value=0 ";
						$sql .= " AND ((hide_out_of_stock=1 AND stock_level > 0) OR hide_out_of_stock=0 OR hide_out_of_stock IS NULL) ";
						$sql .= " ORDER BY item_property_id ";
						$db->query($sql);
						while($db->next_record())
						{
							$item_property_id = $db->f("item_property_id");
							$option_price    = round($db->f($additional_price_field), 2);
							$percentage_price = $db->f("percentage_price");
							if ($percentage_price && $item_price) {
								$option_price += round(($item_price * $percentage_price) / 100, 2);
							}

							$opt_buy_price    = round($db->f("buying_price"), 2);
							if ($properties_discount > 0) {
								$option_price -= round(($option_price * $properties_discount) / 100, 2);
							}
							$option_real_price = $option_price;
							if ($discount_applicable && $user_discount_type == 1) {
								$option_price -= round(($option_price * $user_discount_amount) / 100, 2);
							} else if ($discount_applicable && $user_discount_type == 4) {
								$option_price -= round((($option_price - $opt_buy_price) * $user_discount_amount) / 100, 2);
							}
							$opt_add_weight   = round($db->f("additional_weight"), 4);
							$opt_value        = $db->f("property_value");
							$opt_item_code    = $db->f("item_code");
							$opt_manufacturer_code = $db->f("manufacturer_code");
							$option_selected = "";
							if($item_property_id == $property_value_param) {
								$additional_weight += $opt_add_weight;
								$additional_price += $option_price;
								$additional_real_price += $option_real_price;
								$options_buying_price += $opt_buy_price;
								$pr_add_weight += $opt_add_weight; 
								$pr_add_price += $option_price;
								$options_code .= trim($opt_item_code);
								$options_manufacturer_code .= trim($opt_manufacturer_code);
								$option_selected = " selected ";
								$property_value = $opt_value;

								$pr_values[] = array($item_property_id, $db->f("property_value"), "", $db->f("use_stock_level"), $db->f("hide_out_of_stock"), $db->f("stock_level"));
								$download_files_ids = $db->f("download_files_ids");
								if ($download_files_ids) { $options_downloads[] = $download_files_ids; }
							}
							$properties_options .= "<option " . $option_selected . " value=\"" . htmlspecialchars($item_property_id) . "\">";
							$properties_options .= htmlspecialchars(get_translation($opt_value));

							$option_tax = get_tax_amount($tax_rates, $item_type_id, $option_price, $item_tax_free, $item_tax_percent, $default_tax_rates);
							if ($tax_prices_type == 1) {
								$option_price_incl = $option_price;
								$option_price_excl = $option_price - $option_tax;
							} else {
								$option_price_incl = $option_price + $option_tax;
								$option_price_excl = $option_price;
							}
							if ($tax_prices == 2 || $tax_prices == 3) {
								$shown_price = $option_price_incl;
							} else {
								$shown_price = $option_price_excl;
							}

							if ($option_price > 0) {
								$properties_options .= $option_positive_price_right . currency_format($shown_price) . $option_positive_price_left;
							} else if ($option_price < 0) {
								$properties_options .= $option_negative_price_right . currency_format(abs($shown_price)) . $option_negative_price_left;
							}
							$properties_options .= "</option>" . $eol;
						}
						$property_control .= $before_control_html;
						$property_control .= "<select name=\"property_" . $cp_id . "\"";
    				if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($control_code) {	$property_control .= " " . $control_code . " "; }
						$property_control .= ">" . $properties_options . "</select>";
						$property_control .= $after_control_html;
					} else if(strtoupper($control_type) == "RADIOBUTTON" || strtoupper($control_type) == "CHECKBOXLIST") {
						$is_radio = (strtoupper($control_type) == "RADIOBUTTON");
						$input_type = $is_radio ? "radio" : "checkbox";
						$property_control .= "<span";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						$property_control .= ">";
						$value_number = 0;
						$sql  = " SELECT * FROM " . $table_prefix . "items_properties_values ";
						$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER) . " AND hide_value=0 ";
						$sql .= " AND ((hide_out_of_stock=1 AND stock_level > 0) OR hide_out_of_stock=0 OR hide_out_of_stock IS NULL) ";
						$sql .= " ORDER BY item_property_id ";
						$db->query($sql);
						while($db->next_record())
						{
							$value_number++;
							$item_property_id = $db->f("item_property_id");
							$option_price    = round($db->f($additional_price_field), 2);
							$percentage_price = $db->f("percentage_price");
							if ($percentage_price && $item_price) {
								$option_price += round(($item_price * $percentage_price) / 100, 2);
							}
							$opt_buy_price    = round($db->f("buying_price"), 2);
							if ($properties_discount > 0) {
								$option_price -= round(($option_price * $properties_discount) / 100, 2);
							}
							$option_real_price = $option_price;
							if ($discount_applicable && $user_discount_type == 1) {
								$option_price -= round(($option_price * $user_discount_amount) / 100, 2);
							} else if ($discount_applicable && $user_discount_type == 4) {
								$option_price -= round((($option_price - $opt_buy_price) * $user_discount_amount) / 100, 2);
							}
							$opt_add_weight   = round($db->f("additional_weight"), 4);
							$opt_value        = $db->f("property_value");
							$opt_item_code    = $db->f("item_code");
							$opt_manufacturer_code = $db->f("manufacturer_code");
							$option_checked   = "";
							if (in_array($item_property_id, $property_value_params)) {
								$additional_weight += $opt_add_weight;
								$additional_price += $option_price;
								$additional_real_price += $option_real_price;
								$options_buying_price += $opt_buy_price;
								$pr_add_weight += $opt_add_weight; 
								$pr_add_price += $option_price;
								$options_code .= trim($opt_item_code);
								$options_manufacturer_code .= trim($opt_manufacturer_code);
								$option_checked = " checked ";
								if ($property_value) { $property_value .= ", "; }
								$property_value .= get_translation($opt_value);
								$pr_values[] = array($item_property_id, $db->f("property_value"), "", $db->f("use_stock_level"), $db->f("hide_out_of_stock"), $db->f("stock_level"));
								$download_files_ids = $db->f("download_files_ids");
								if ($download_files_ids) { $options_downloads[] = $download_files_ids; }
							}
							$control_name = ($is_radio) ? ("property_".$cp_id) : ("property_".$cp_id."_".$value_number);
							$property_control .= $before_control_html;
							$property_control .= "<input type=\"". $input_type . "\" name=\"" . $control_name . "\" ". $option_checked;
							if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
							if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
							if ($control_code) {	$property_control .= " " . $control_code . " "; }
							$property_control .= "value=\"" . htmlspecialchars($item_property_id) . "\">";
							$property_control .= get_translation($opt_value);

							$option_tax = get_tax_amount($tax_rates, $item_type_id, $option_price, $item_tax_free, $item_tax_percent, $default_tax_rates);
							if ($tax_prices_type == 1) {
								$option_price_incl = $option_price;
								$option_price_excl = $option_price - $option_tax;
							} else {
								$option_price_incl = $option_price + $option_tax;
								$option_price_excl = $option_price;
							}
							if ($tax_prices == 2 || $tax_prices == 3) {
								$shown_price = $option_price_incl;
							} else {
								$shown_price = $option_price_excl;
							}

							if ($option_price > 0) {
								$property_control .= $option_positive_price_right . currency_format($shown_price) . $option_positive_price_left;
							} else if ($option_price < 0) {
								$property_control .= $option_negative_price_right . currency_format(abs($shown_price)) . $option_negative_price_left;
							}
							$property_control .= $after_control_html;
						}
						$property_control .= "</span>";
						$property_control .= "<input type=\"hidden\" name=\"property_total_".$cp_id."\" value=\"".$value_number."\">";
					} else if (strtoupper($control_type) == "TEXTBOXLIST") {
						$value_number = 0;
						$sql  = " SELECT * FROM " . $table_prefix . "items_properties_values ";
						$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER) . " AND hide_value=0 ";
						$sql .= " AND ((hide_out_of_stock=1 AND stock_level > 0) OR hide_out_of_stock=0 OR hide_out_of_stock IS NULL) ";
						$sql .= " ORDER BY item_property_id ";
						$db->query($sql);
						while($db->next_record())
						{
							$value_number++;
							$item_property_id = $db->f("item_property_id");
							$option_price    = round($db->f($additional_price_field), 2);
							$percentage_price = $db->f("percentage_price");
							if ($percentage_price && $item_price) {
								$option_price += round(($item_price * $percentage_price) / 100, 2);
							}
							$opt_buy_price    = round($db->f("buying_price"), 2);
							if ($properties_discount > 0) {
								$option_price -= round(($option_price * $properties_discount) / 100, 2);
							}
							$option_real_price = $option_price;
							if ($discount_applicable && $user_discount_type == 1) {
								$option_price -= round(($option_price * $user_discount_amount) / 100, 2);
							} else if ($discount_applicable && $user_discount_type == 4) {
								$option_price -= round((($option_price - $opt_buy_price) * $user_discount_amount) / 100, 2);
							}
							$opt_add_weight   = round($db->f("additional_weight"), 4);
							$opt_value        = $db->f("property_value");
							$opt_item_code    = $db->f("item_code");
							$opt_manufacturer_code = $db->f("manufacturer_code");
							$specified_value = "";
							if (in_array($item_property_id, $property_value_params)) {
								$additional_weight += $opt_add_weight;
								$additional_price += $option_price;
								$additional_real_price += $option_real_price;
								$options_buying_price += $opt_buy_price;
								$pr_add_weight += $opt_add_weight; 
								$pr_add_price += $option_price;
								$options_code .= trim($opt_item_code);
								$options_manufacturer_code .= trim($opt_manufacturer_code);
								$specified_value = $property_value_texts[$item_property_id];
								if ($property_value) { $property_value .= ", "; }
								$property_value .= get_translation($opt_value);
								$pr_values[] = array($item_property_id, $db->f("property_value"), $specified_value, $db->f("use_stock_level"), $db->f("hide_out_of_stock"), $db->f("stock_level"));
								$download_files_ids = $db->f("download_files_ids");
								if ($download_files_ids) { $options_downloads[] = $download_files_ids; }
							}
							$value_control_name = "property_value_".$cp_id."_".$value_number;
							$property_control .= "<input type=\"hidden\" value=\"".$item_property_id."\" name=\"".$value_control_name."\">";
				  
							$property_control .= $before_control_html;
							$property_control .= get_translation($opt_value) . ": ";
							$control_name = "property_".$cp_id."_".$value_number;
							$property_control .= "<input type=\"text\" value=\"" .htmlspecialchars($specified_value) . "\" id=\"item_property_" . $item_property_id . "\" name=\"" . $control_name . "\" ";
							if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
							if ($onchange_code) {	
								$control_onchange_code = str_replace("{item_code}", $item_code, $onchange_code);
								$control_onchange_code = str_replace("{manufacturer_code}", $manufacturer_code, $onchange_code);
								$control_onchange_code = str_replace("{option_value}", $property_value, $control_onchange_code);
								$property_control .= $control_onchange_code; 
								$property_control .= " onChange=\"" . $onchange_code . "\""; 
							}
							if ($onclick_code) {	
								$control_onclick_code = str_replace("{item_code}", $item_code, $onclick_code);
								$control_onclick_code = str_replace("{manufacturer_code}", $manufacturer_code, $onclick_code);
								$control_onclick_code = str_replace("{option_value}", $property_value, $control_onclick_code);
								$property_control .= " onClick=\"" . $control_onclick_code . "\"";
							}
							if ($control_code) {	$property_control .= " " . $control_code . " "; }
							if ($max_limit_type == 2 && $max_limit_length) {
								$property_control .= " maxlength=\"" . $max_limit_length . "\"";
							} else if ($max_limit_type == 1 && $max_limit_length) {
								$property_control .= " onKeyPress=\"return checkBoxesMaxLength(event, document.order_info, '".$cp_id."', ".$max_limit_length.");\"";
							}
							$property_control .= ">";

							$option_tax = get_tax_amount($tax_rates, $item_type_id, $option_price, $item_tax_free, $item_tax_percent, $default_tax_rates);
							if ($tax_prices_type == 1) {
								$option_price_incl = $option_price;
								$option_price_excl = $option_price - $option_tax;
							} else {
								$option_price_incl = $option_price + $option_tax;
								$option_price_excl = $option_price;
							}
							if ($tax_prices == 2 || $tax_prices == 3) {
								$shown_price = $option_price_incl;
							} else {
								$shown_price = $option_price_excl;
							}

							if ($option_price > 0) {
								$property_control .= $option_positive_price_right . currency_format($shown_price) . $option_positive_price_left;
							} else if ($option_price < 0) {
								$property_control .= $option_negative_price_right . currency_format(abs($shown_price)) . $option_negative_price_left;
							}
							$property_control .= $after_control_html;
						}
						$property_control .= "<input type=\"hidden\" name=\"property_total_".$cp_id."\" value=\"".$value_number."\">";
					} else if (strtoupper($control_type) == "TEXTBOX") {
						$property_value = $property_value_param;
						$property_control .= $before_control_html;
						$property_control .= "<input type=\"text\" name=\"property_" . $cp_id . "\"";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						$property_control .= " value=\"" . htmlspecialchars($property_value) . "\">";
						$property_control .= $after_control_html;
					} else if (strtoupper($control_type) == "TEXTAREA") {
						$property_value = $property_value_param;
						$property_control .= $before_control_html;
						$property_control .= "<textarea name=\"property_" . $cp_id . "\"";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						$property_control .= ">" . htmlspecialchars($property_value) . "</textarea>";
						$property_control .= $after_control_html;
					} else {
						$property_control .= $before_control_html;
						if ($property_required) {
							$property_value = $property_value_param;
							$property_control .= "<input type=\"hidden\" name=\"property_" . $cp_id . "\" value=\"" . htmlspecialchars($property_description) . "\">";
						}
						$property_control .= "<span";
						if ($control_style) {	$property_control .= " style=\"" . $control_style . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						if ($onclick_code) {	$property_control .= " onClick=\"" . $onclick_code . "\""; }
						if ($onchange_code) {	$property_control .= " onChange=\"" . $onchange_code . "\""; }
						$property_control .= ">" . get_translation($property_description) . "</span>";
						$property_control .= $after_control_html;
					}
					if($is_update) {
						if (strlen($parent_cart_id)) {
							$shopping_cart[$parent_cart_id]["COMPONENTS_PROPERTIES"][$cart_id][$property_id] = $property_value_params;
							$shopping_cart[$parent_cart_id]["COMPONENTS_PROPERTIES_TEXT"][$cart_id][$property_id] = $property_value_texts;
						} else {
							$shopping_cart[$cart_id]["PROPERTIES"][$property_id] = $property_value_params;
							$shopping_cart[$cart_id]["PROPERTIES_INFO"][$property_id]["VALUES"] = $property_value_params;
							$shopping_cart[$cart_id]["PROPERTIES_INFO"][$property_id]["TEXT"] = $property_value_texts;
						}
						if ($property_required && !strlen($property_value)) {
							$property_error = str_replace("{property_name}", $property_name, REQUIRED_PROPERTY_MSG);
							$property_error = str_replace("{product_name}", $item_name, $property_error);
							$sc_errors .= $property_error . "<br>";
						}
					}
					$control_price_tax = get_tax_amount($tax_rates, $item_type_id, $control_price, $item_tax_free, $item_tax_percent, $default_tax_rates);
					if ($control_type == "TEXTBOXLIST") {
						$properties_values_html .= "<br>" . $start_html . $property_name . ": ";
						if ($control_price > 0) {
							$properties_values_html .= $option_positive_price_right . currency_format($control_price, "", $control_price_tax) . $option_positive_price_left;
						} else if ($option_price < 0) {
							$properties_values_html .= $option_negative_price_right . currency_format($control_price, "", $control_price_tax) . $option_negative_price_left;
						}
						$properties_values_html .= $middle_html . $property_control . $end_html;
					} else {
						$properties_values_html .= "<br>" . $start_html . $property_name . ": " . $middle_html . $property_control . $end_html;
					}
					if (strlen($property_value)) {
						if(strlen($properties_values_text)) $properties_values_text .= "; ";
						$properties_values .= "<br>" . $property_name . ": " . get_translation($property_value);
						$properties_values_text .= $property_name . ": " . get_translation($property_value);
						$properties_info[] = array($property_id, $control_type, $property_name_initial, $property_value, $pr_add_price, $pr_add_weight, $pr_values, $property_order);
					}

				} else if (is_array($properties) && isset($properties[$property_id])) {
					// options added previously when adding product
					$property_values = $properties[$property_id];
					$values_list = ""; $values_list_translation = ""; 
					if(strtoupper($control_type) == "LISTBOX" || strtoupper($control_type) == "RADIOBUTTON" 
						|| strtoupper($control_type) == "CHECKBOXLIST" || strtoupper($control_type) == "TEXTBOXLIST") {
						for ($pv = 0; $pv < sizeof($property_values); $pv++) {
							$sql  = " SELECT item_code, manufacturer_code, property_value, ".$additional_price_field.", percentage_price, buying_price, ";
							$sql .= " additional_weight, use_stock_level, hide_out_of_stock, stock_level, download_files_ids ";
							$sql .= " FROM " . $table_prefix . "items_properties_values ipv ";
							$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER);
							$sql .= " AND item_property_id=" . $db->tosql($property_values[$pv], INTEGER);
							$db->query($sql);
							if ($db->next_record()) {
								$pr_item_code = $db->f("item_code");
								$pr_manufacturer_code = $db->f("manufacturer_code");
								$option_price = $db->f($additional_price_field);
								$percentage_price = $db->f("percentage_price");
								if ($percentage_price && $item_price) {
									$option_price += round(($item_price * $percentage_price) / 100, 2);
								}
								$opt_buy_price = $db->f("buying_price");
								if ($properties_discount > 0) {
									$option_price -= round(($option_price * $properties_discount) / 100, 2);
								}
								$option_real_price = $option_price;
								if ($discount_applicable && $user_discount_type == 1) {
									$option_price -= round(($option_price * $user_discount_amount) / 100, 2);
								} else if ($discount_applicable && $user_discount_type == 4) {
									$option_price -= round((($option_price - $opt_buy_price) * $user_discount_amount) / 100, 2);
								}
								$pr_add_price += $option_price;
								$pr_add_real_price += $option_real_price;
								$pr_buy_price += $opt_buy_price;
								$pr_add_weight += $db->f("additional_weight");
								if (strtoupper($control_type) == "TEXTBOXLIST") {
									$value_text = $shopping_cart[$cart_id]["PROPERTIES_INFO"][$property_id]["TEXT"][$property_values[$pv]];
									$values_list .= "<br>"; $values_list_translation .= "<br>";
									$values_list .= $db->f("property_value") . ": ";
									$values_list .= $value_text;
									$values_list_translation .= get_translation($db->f("property_value"));
									$values_list_translation .= $value_text;
								} else {
									$value_text = "";
									if ($values_list) { $values_list .= ", "; $values_list_translation .= ", "; }
									$values_list .= $db->f("property_value");
									$values_list_translation .= get_translation($db->f("property_value"));
								}

								$options_code .= $pr_item_code;
								$options_manufacturer_code .= $pr_manufacturer_code;
								$pr_values[] = array($property_values[$pv], $db->f("property_value"), $value_text, $db->f("use_stock_level"), $db->f("hide_out_of_stock"), $db->f("stock_level"));
								$download_files_ids = $db->f("download_files_ids");
								if ($download_files_ids) { $options_downloads[] = $download_files_ids; }
							} else {
								if (strlen($parent_cart_id)) {
									// delete property for subcomponent
									$shopping_cart[$parent_cart_id]["COMPONENTS_PROPERTIES"][$cart_id][$property_id] = "";
									unset($shopping_cart[$parent_cart_id]["COMPONENTS_PROPERTIES"][$cart_id][$property_id]);
								} else {
									// delete property for product
									$shopping_cart[$cart_id]["PROPERTIES"][$property_id] = "";
									unset($shopping_cart[$cart_id]["PROPERTIES"][$property_id]);
								}
							}
						}

					} else {
						$values_list = $property_values[0];
						$values_list_translation = get_translation($property_values[0]);
					}
					// calculate control price
					if (strlen($parent_cart_id)) {
						$control_price = calculate_control_price($shopping_cart[$parent_cart_id]["COMPONENTS_PROPERTIES"][$cart_id][$property_id], $shopping_cart[$parent_cart_id]["COMPONENTS_PROPERTIES_TEXT"][$cart_id][$property_id], $property_price_type, $property_price, $free_price_type, $free_price_amount);
						$pr_add_price += $control_price;
						$pr_add_real_price += $control_price;
					} else {
						$control_price = calculate_control_price($shopping_cart[$cart_id]["PROPERTIES_INFO"][$property_id]["VALUES"], $shopping_cart[$cart_id]["PROPERTIES_INFO"][$property_id]["TEXT"], $property_price_type, $property_price, $free_price_type, $free_price_amount);
						$pr_add_price += $control_price;
						$pr_add_real_price += $control_price;
					}


					$additional_price += $pr_add_price;
					$additional_real_price += $pr_add_real_price;
					$options_buying_price += $pr_buy_price;
					$additional_weight += $pr_add_weight;

					$pr_add_tax = get_tax_amount($tax_rates, $item_type_id, $pr_add_price, $item_tax_free, $item_tax_percent, $default_tax_rates);
					if ($tax_prices_type == 1) {
						$pr_price_incl = $pr_add_price;
						$pr_price_excl = $pr_add_price - $pr_add_tax;
					} else {
						$pr_price_incl = $pr_add_price + $pr_add_tax;
						$pr_price_excl = $pr_add_price;
					}
					if ($tax_prices == 2 || $tax_prices == 3) {
						$pr_shown_price = $pr_price_incl;
					} else {
						$pr_shown_price = $pr_price_excl;
					}


					if (strlen($properties_values_text)) $properties_values_text .= "; ";

					if (strtoupper($control_type) == "TEXTBOXLIST") {
						$properties_values .= "<br>" . $property_name . ": ";
						$properties_values_html .= "<br>" . $property_name . ": ";
						$properties_values_text .= $property_name . ": ";
					} else {
						$properties_values .= "<br>" . $property_name . ": " . $values_list_translation;
						$properties_values_html .= "<br>" . $property_name . ": " . $values_list_translation;
						$properties_values_text .= $property_name . ": " . $values_list_translation;
					}
					if ($pr_add_price > 0) {
						$properties_values .= $option_positive_price_right . currency_format($pr_shown_price) . $option_positive_price_left;
						$properties_values_html .= $option_positive_price_right . currency_format($pr_shown_price) . $option_positive_price_left;
						$properties_values_text .= $option_positive_price_right . currency_format($pr_shown_price) . $option_positive_price_left;
					} else if ($pr_add_price < 0) {
						$properties_values .= $option_negative_price_right . currency_format(abs($pr_shown_price)) . $option_negative_price_left;
						$properties_values_html .= $option_negative_price_right . currency_format(abs($pr_shown_price)) . $option_negative_price_left;
						$properties_values_text .= $option_negative_price_right . currency_format(abs($pr_shown_price)) . $option_negative_price_left;
					}
					if (strtoupper($control_type) == "TEXTBOXLIST") {
						$properties_values .= $values_list;
						$properties_values_text .= $values_list;
						$properties_values_html .= $values_list;
					}

					if ($control_type == "IMAGEUPLOAD" && preg_match("/^http\:\/\//", $values_list_translation)) { 
						$values_list_translation = "<a href=\"".$values_list_translation."\" target=\"_blank\">" . basename($values_list_translation) . "</a>";
					}

					$properties_info[] = array($property_id, $control_type, $property_name_initial, $values_list, $pr_add_price, $pr_add_weight, $pr_values, $property_order);
				}
			}
		}

		// check downloads for product
		$sql  = " SELECT * FROM " . $table_prefix . "items_files ";
		$sql .= " WHERE (item_id=" . $db->tosql($item_id, INTEGER);
		$sql .= " AND download_type=1) ";
		if (sizeof($options_downloads)) {
			$files_ids = join(",", $options_downloads);
			$sql .= " OR (download_type=2 AND ";
			$sql .= " file_id IN (" . $db->tosql($files_ids, INTEGERS_LIST) . "))";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$file_id = $db->f("file_id");
			$downloads[$file_id] = $db->Record;
		}

		set_session("shopping_cart", $shopping_cart);

	}

?>