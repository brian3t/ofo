<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  protx_server_functions.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * VSP (www.protx.com) transaction handler by www.viart.com
 */

	function protx_vsp_get_associative_array($separator, $input)
	{
		for ($i=0; $i < count($input); $i++) {
			$splitAt = strpos($input[$i], $separator);
			$output[trim(substr($input[$i], 0, $splitAt))] = trim(substr($input[$i], ($splitAt+1)));
		}
		return $output;
	}

	function protx_vsp_get_eol()
	{
		if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
			$eol = "\r\n";
		} else if (strtoupper(substr(PHP_OS,0,3)=='MAC')) {
			$eol = "\r";
		} else {
			$eol = "\n";
		}
		return $eol;
	}
  
	function protx_vsp_payment_variables($order_id) 
	{
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

			$currency_code = $db->f("currency_code");
			$currency_rate = $db->f("currency_rate");
			$shipping_type_desc = $db->f("shipping_type_desc");
			$tax_name = $db->f("tax_name");
			$tax_cost = $db->f("tax_total");

		}
	
		$variables["state"] = get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_code=" . $db->tosql($variables["state_code"], TEXT));
		$variables["country"] = get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_code=" . $db->tosql($variables["country_code"], TEXT));

		$eol = protx_vsp_get_eol();
		$currency = get_currency($currency_code);
		$currency_left = $currency["left"];
		$currency_right = $currency["right"];
		$currency_rate = $currency_rate;
		$currency_code = $currency["code"];
		$currency_value = $currency["value"];

		$items_text = "";
		$line_count = 0;
		$sql  = " SELECT * FROM " . $table_prefix . "orders_items ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$item_text = get_translation($db->f("item_name"));
			$quantity = $db->f("quantity");
			$price = $db->f("price");
			$tax_percent = ($db->f("tax_free"))?0:$db->f("tax_percent");
			$tax = $price/100*$tax_percent;
			$tax_format = number_format($tax, 2);
			$item_total = number_format(($price+$tax), 2);
			$line_total = number_format(($price+$tax) * $quantity, 2);
			$item_text = str_replace(":", " " , $item_text);
			$items_text .= ":".$item_text.":".$quantity.":".$price.":".$tax_format.":".$item_total.":".$line_total;
			$line_count ++;
		}
		if ($line_count && strlen($variables["total_discount"] != 0)) {
			$TOTAL_DISCOUNT_MSG = str_replace(":", " ", TOTAL_DISCOUNT_MSG);
			$items_text .= ":".TOTAL_DISCOUNT_MSG.":---:---:---:---:".$variables["total_discount"];
			$line_count ++;
		}
		if ($line_count && strlen($shipping_type_desc)) {
			$shipping_type_desc = str_replace(":", " ", $shipping_type_desc);
			$items_text .= ":".$shipping_type_desc.":---:---:---:---:".$variables["shipping_cost"];
			$line_count ++;
 		}
		if ($line_count && $variables["processing_fee"] != 0) {
			$PROCESSING_FEE_MSG = str_replace(":", " ", PROCESSING_FEE_MSG);
			$items_text .= ":".PROCESSING_FEE_MSG . ":---:---:---:---:".$variables["processing_fee"];
			$line_count ++;
		}

		if ($line_count){
			$variables["basket"] = $line_count.$items_text;
		}else{
			$variables["basket"] = "";
		}
		return $variables;
	}

	function protx_vsp_payment_params($order_id){
    	global $db, $table_prefix;
		$sql  = " SELECT * FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$payment_id = $db->f("payment_id");
		}
		if (strlen($payment_id)) {
			$variables = protx_vsp_payment_variables($order_id);
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
			$sql  = " SELECT success_status_id, pending_status_id, failure_status_id, advanced_url, failure_action ";
			$sql .= " FROM " . $table_prefix . "payment_systems ";
			$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$payment_params['success_status_id'] = $db->f("success_status_id");
				$payment_params['pending_status_id'] = $db->f("pending_status_id");
				$payment_params['failure_status_id'] = $db->f("failure_status_id");
				$payment_params['advanced_url']      = $db->f("advanced_url");
				$payment_params['failure_action']    = $db->f("failure_action");
			}
		} else {
			return;
		}
		return $payment_params;
	}

	function protx_vsp_post_parameters($order_id){
    	global $db, $table_prefix;
		$sql  = " SELECT * FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$payment_id = $db->f("payment_id");
		}
		if (strlen($payment_id)) {
			$variables = protx_vsp_payment_variables($order_id);
			if(!is_array($variables)){
				return;
			}
			$post_parameters = '';
			$sql  = " SELECT parameter_name, parameter_source, not_passed ";
			$sql .= " FROM " . $table_prefix . "payment_parameters ";
			$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$parameter_name = trim($db->f("parameter_name"));
				$parameter_source = trim($db->f("parameter_source"));
				$not_passed = trim($db->f("not_passed"));
				if (!$not_passed){
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
					if ($post_parameters) { $post_parameters .= "&"; }
					if ($parameter_name == 'Basket' && strlen($variables["basket"]) && strlen($variables["basket"]) <= 7500){
						$parameter_value = $variables["basket"];
					}
					if ($parameter_name == 'BillingAddress'){
						$parameter_value = substr($parameter_value, 0, 200);
					}
					if ($parameter_name == 'BillingPostCode'){
						$parameter_value = substr($parameter_value, 0, 10);
					}
					if ($parameter_name == 'DeliveryAddress'){
						$parameter_value = substr($parameter_value, 0, 200);
					}
					if ($parameter_name == 'DeliveryPostCode'){
						$parameter_value = substr($parameter_value, 0, 10);
					}
					if ($parameter_name == 'CustomerName'){
						$parameter_value = substr($parameter_value, 0, 100);
					}
					if ($parameter_name == 'ContactNumber'){
						$parameter_value = substr($parameter_value, 0, 20);
					}
					if ($parameter_name == 'ContactFax'){
						$parameter_value = substr($parameter_value, 0, 20);
					}
					if ($parameter_name == 'CustomerEMail'){
						$parameter_value = substr($parameter_value, 0, 255);
					}
					$post_parameters .= $parameter_name . "=" . urlencode($parameter_value);
				}
			}
		} else {
			return;
		}
		return $post_parameters;
	}

    function protx_vsp_set_order_message($order_id, $db_field, $input_message){
    	global $db, $table_prefix;
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET ".$db_field."=" . $db->tosql($input_message, TEXT) ;
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;
		$db->query($sql);
	}

?>