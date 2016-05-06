<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  upc_functions.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


    function upc_payment_variables($order_id){
    	global $db, $table_prefix, $settings;
		$variables = array();
		$variables["site_url"] = $settings["site_url"];
		$variables["order_id"] = $order_id;
		$sql  = " SELECT * FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			// payment information
			$payment_id = $db->f("payment_id");
	
			$variables["user_id"] = $db->f("user_id");
			$variables["total_quantity"] = $db->f("total_quantity");
			// email variables
			$variables["email"] = $db->f("email");
			$delivery_email["delivery_email"] = $db->f("delivery_email");
	
			// shopping variables
			$variables["goods_total"] = $db->f("goods_total");
			$variables["total_discount"] = $db->f("total_discount");
			$variables["total_discount_tax"] = $db->f("total_discount_tax");
			$variables["goods_with_discount"] = $variables["goods_total"] - $variables["total_discount"];
			$variables["shipping_cost"] = $db->f("shipping_cost");
			$variables["tax_percent"] = $db->f("tax_percent");
			$variables["tax_total"] = $db->f("tax_total");
			$variables["tax_name"] = $db->f("tax_name");
			$variables["processing_fee"] = $db->f("processing_fee");
			$variables["order_total"] = $db->f("order_total");
			$variables["order_placed_date"] = $db->f("order_placed_date", DATETIME);
	
			$variables["name"] = $db->f("name");
			$variables["first_name"] = $db->f("first_name");
			$variables["last_name"] = $db->f("last_name");
			$variables["company_name"] = $db->f("company_name");
			$variables["address1"] = $db->f("address1");
			$variables["address2"] = $db->f("address2");
			$variables["city"] = $db->f("city");
			$variables["province"] = $db->f("province");
			$variables["zip"] = $db->f("zip");
			$variables["phone"] = $db->f("phone");
			$variables["daytime_phone"] = $db->f("daytime_phone");
			$variables["evening_phone"] = $db->f("evening_phone");
			$variables["cell_phone"] = $db->f("cell_phone");
			$variables["fax"] = $db->f("fax");
			$variables["state_code"] = $db->f("state_code");
			$variables["country_code"] = $db->f("country_code");

			$shipping_type_desc = $db->f("shipping_type_desc");
			$variables["shipping_type_desc"] = $shipping_type_desc;
			$variables["shipping_tax"] = ($db->f("shipping_taxable"))?$variables["tax_percent"]:0;
			$tax_name = $db->f("tax_name");
			$tax_cost = $db->f("tax_total");

			$currency_code = $db->f("currency_code");
			$currency_rate = $db->f("currency_rate");
			$currency = get_currency($currency_code);
			$currency_left = $currency["left"];
			$currency_right = $currency["right"];
			$currency_rate = $currency_rate;
			$currency_code = $currency["code"];
			$currency_value = $currency["value"];

			$variables["shipping_cost_cr"] = round($variables["shipping_cost"] * $currency_rate, 2);
			$variables["shipping_cost_with_tax_cr"] = round(($variables["shipping_cost"]+$variables["shipping_cost"] / 100 * $variables["shipping_tax"]) * $currency_rate, 2);
			$variables["total_discount_with_tax"] = round(($variables["total_discount"]+$variables["total_discount_tax"]), 2);
			$variables["total_discount_with_tax_cr"] = round(($variables["total_discount"]+$variables["total_discount_tax"]) * $currency_rate, 2);
		}
	
		$variables["state"] = get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_code=" . $db->tosql($variables["state_code"], TEXT));
		$variables["country"] = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_code=" . $db->tosql($variables["country_code"], TEXT));

		$eol = get_eol();

		$items_text = "";
		$variables["items"] = array();
		$sql  = " SELECT * FROM " . $table_prefix . "orders_items ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$items = array();
			$item_text = get_translation($db->f("item_name"));
			$items['item_text'] = $item_text;
			$quantity = $db->f("quantity");
			$items['quantity'] = $quantity;
			$price = $db->f("price");
			$items['price'] = round($price, 2);
			$items['price_cr'] = round($price * $currency_rate, 2);
			$items['tax'] = ($db->f("tax_free"))?0:$db->f("tax_percent");
			$items['price_with_tax'] = round($items['price'] + $items['price'] / 100 * $items['tax'], 2);
			$items['price_with_tax_cr'] = round(($items['price'] + $items['price'] / 100 * $items['tax'])*$currency_rate, 2);
			$items['total_tax'] = round($quantity * $items['price'] / 100 * $items['tax'], 2);
			$items['total_tax_cr'] = round(($quantity * $items['price'] / 100 * $items['tax'])*$currency_rate, 2);
			$items['total_price_with_tax'] = round($quantity * ($items['price'] + $items['price'] / 100 * $items['tax']), 2);
			$items['total_price_with_tax_cr'] = round($quantity * ($items['price'] + $items['price'] / 100 * $items['tax'])*$currency_rate, 2);
			$item_total = $price * $quantity;
			$item_text .= " " . PROD_QTY_COLUMN . ": " . $quantity . " " . $currency_left . number_format($item_total * $currency_rate, 2) . $currency_right;
			$items_text .= $item_text . $eol;
			$variables["items"][]=$items;

		}

		$items_text .= GOODS_TOTAL_MSG . ": " . $currency_left . number_format($variables["goods_total"] * $currency_rate, 2) . $currency_right . $eol;
		if (strlen($variables["total_discount"] != 0)) {
			$items_text .= TOTAL_DISCOUNT_MSG . ": -" . $currency_left . number_format($variables["total_discount"] * $currency_rate, 2) . $currency_right . $eol;
			$items_text .= GOODS_WITH_DISCOUNT_MSG. ": " . $currency_left . number_format(($variables["goods_with_discount"]) * $currency_rate, 2) . $currency_right . $eol;
		}
		if (strlen($shipping_type_desc)) {
			$items_text .= $shipping_type_desc . ": " . $currency_left . number_format($variables["shipping_cost"] * $currency_rate, 2) . $currency_right . $eol;
 		}
		if (strlen($tax_name)) {
			$tax_cost = round($tax_cost, 2);
			$items_text .= $tax_name . ": " . $currency_left . number_format($tax_cost * $currency_rate, 2) . $currency_right . $eol;
		}
		if ($variables["processing_fee"] != 0) {
			$items_text .= PROCESSING_FEE_MSG . ": " . $currency_left . number_format($variables["processing_fee"] * $currency_rate, 2) . $currency_right;
			$variables["processing_fee_cr"] = number_format($variables["processing_fee"] * $currency_rate, 2);
		}
		$items_text .= CART_TOTAL_MSG . ": " . $currency_left . number_format($variables["order_total"] * $currency_rate, 2) . $currency_right;

		$variables["basket"] = $items_text;
		return $variables;
	}

	function upc_payment_params($order_id){
    	global $db, $table_prefix;
		$sql  = " SELECT * FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$payment_id = $db->f("payment_id");
		}
		if (strlen($payment_id)) {
			$variables = upc_payment_variables($order_id);
			if(!is_array($variables)){
				return;
			}
			$payment_params = array();
			$sql  = " SELECT parameter_name, parameter_source, not_passed ";
			$sql .= " FROM " . $table_prefix . "payment_parameters ";
			$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$parameter_name = trim($db->f("parameter_name"));
				$parameter_source = trim($db->f("parameter_source"));
				$not_passed = trim($db->f("not_passed"));
				if (preg_match_all("/\{(\w+)\}/is", $parameter_source, $matches)) {
					$parameter_value = $parameter_source;
					for($p = 0; $p < sizeof($matches[1]); $p++) {
						$l_source = strtolower($matches[1][$p]);
						if (isset($variables[$l_source])) {
							$parameter_value = str_replace("{".$l_source."}", $variables[$l_source], $parameter_value);
						}
					}
				} else if (isset($variables[$parameter_source])) {
					$parameter_value = $variables[$parameter_source];
				} else {
					$parameter_value = $parameter_source;
				}
				$payment_params[$parameter_name] = $parameter_value;
			}
			$sql  = " SELECT success_status_id, pending_status_id, failure_status_id ";
			$sql .= " FROM " . $table_prefix . "payment_systems ";
			$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$payment_params['success_status_id'] = $db->f("success_status_id");
				$payment_params['pending_status_id'] = $db->f("pending_status_id");
				$payment_params['failure_status_id'] = $db->f("failure_status_id");
			}
		} else {
			return;
		}
		return $payment_params;
	}
?>