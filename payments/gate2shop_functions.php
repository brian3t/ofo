<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  gate2shop_functions.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Gate2Shop (www.g2s.com) handler by ViArt Ltd (http://www.viart.com/)
 */

	function get_gate2shop($payment_parameters){
		global $db,$order_id,$table_prefix,$tax_prices_type,$currency_rate,$currency_left,$currency_right,$eol;
	
		$post_parameters = ""; 
		$payment_params = array(); 
		$pass_parameters = array(); 
		$pass_data = array(); 
		$variables = array();
		get_payment_parameters($order_id, $payment_params, $pass_parameters, $post_parameters, $pass_data, $variables, "");
		
		$payment_parameters = $payment_params;
	
		$merchant_site_id   = isset($payment_parameters["merchant_site_id"]) ? $payment_parameters["merchant_site_id"] : "2302";
		$merchantIp      	= isset($payment_parameters["merchantIp"]) ? $payment_parameters["merchantIp"] : "";//
		$merchant_id      	= isset($payment_parameters["merchant_id"]) ? $payment_parameters["merchant_id"] : "1";
		$checksum      		= isset($payment_parameters["checksum"]) ? $payment_parameters["checksum"] : "";
		$time_stamp      	= isset($payment_parameters["time_stamp"]) ? $payment_parameters["time_stamp"] : "";
		$currency     	 	= isset($payment_parameters["currency"]) ? $payment_parameters["currency"] : "";
		$total_amount      	= isset($payment_parameters["total_amount"]) ? $payment_parameters["total_amount"] : "";
		$numberofitems      = isset($payment_parameters["numberofitems"]) ? $payment_parameters["numberofitems"] : "";
		$merchant_unique_id	= isset($payment_parameters["merchant_unique_id"]) ? $payment_parameters["merchant_unique_id"] : "";//
		$invoice_id      	= isset($payment_parameters["invoice_id"]) ? $payment_parameters["invoice_id"] : "";//
		$total_tax      	= isset($payment_parameters["total_tax"]) ? $payment_parameters["total_tax"] : "";//
		$item_name_N      	= isset($payment_parameters["item_name_N"]) ? $payment_parameters["item_name_N"] : "";
		$item_number_N      = isset($payment_parameters["item_number_N"]) ? $payment_parameters["item_number_N"] : "";//
		$quantity_N      	= isset($payment_parameters["quantity_N"]) ? $payment_parameters["quantity_N"] : "";//
		$item_amount_N      = isset($payment_parameters["item_amount_N"]) ? $payment_parameters["item_amount_N"] : "";
		$shipping_N      	= isset($payment_parameters["shipping_N"]) ? $payment_parameters["shipping_N"] : "";//
		$payment_method     = isset($payment_parameters["payment_method"]) ? $payment_parameters["payment_method"] : "";//
		$payment_method     = strtolower($payment_method);
		$Token      		= isset($payment_parameters["Token"]) ? $payment_parameters["Token"] : "";
		$cc_card_number     = isset($payment_parameters["cc_card_number"]) ? $payment_parameters["cc_card_number"] : "";
		$cc_name_on_card    = isset($payment_parameters["cc_name_on_card"]) ? $payment_parameters["cc_name_on_card"] : "";//
		$cc_exp_year      	= isset($payment_parameters["cc_exp_year"]) ? $payment_parameters["cc_exp_year"] : "";//
		$cc_exp_month      	= isset($payment_parameters["cc_exp_month"]) ? $payment_parameters["cc_exp_month"] : "";//
		$cc_cvv2      		= isset($payment_parameters["cc_cvv2"]) ? $payment_parameters["cc_cvv2"] : "";//
		$secret      		= isset($payment_parameters["secret"]) ? $payment_parameters["secret"] : "";//
		
		$dbp = new VA_SQL();
		$dbp->DBType       = $db->DBType;
		$dbp->DBDatabase   = $db->DBDatabase;
		$dbp->DBUser       = $db->DBUser;
		$dbp->DBPassword   = $db->DBPassword;
		$dbp->DBHost       = $db->DBHost;
		$dbp->DBPort       = $db->DBPort;
		$dbp->DBPersistent = $db->DBPersistent;
		
		$variables["items"] = array();
		$items_text = "";
		$sql  = " SELECT * FROM " . $table_prefix . "orders_items ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$items = array();
			$item_text = get_translation($db->f("item_name"));
			$sql  = " SELECT * FROM " . $table_prefix . "orders_items_properties ";
			$sql .= " WHERE order_item_id=" . $dbp->tosql($db->f("order_item_id"), INTEGER);
			$dbp->query($sql);
			while ($dbp->next_record()) {
				$item_text .= ' '.get_translation($dbp->f("property_name")).' ('.get_translation($dbp->f("property_value")).')';
			}
			$items['item_text'] = $item_text;
			$items['item_id'] = $db->f("item_id");
			$quantity = $db->f("quantity");
			$items['quantity'] = $quantity;
			$price = $db->f("price");
			$price_with_tax = $price;
			$items['tax'] = ($db->f("tax_free"))?0:$db->f("tax_percent");
			if($tax_prices_type == 1){
				$price = $price - get_tax_amount("", 0, $price, 0, $items['tax']);
				if($price_with_tax != round($price + $price/100*$items['tax'], 2) ){
					$items['tax'] = ($price_with_tax/$price - 1)*100;
				}
			}
			$items['price'] = round($price, 2);
			$items['price_cr'] = round($price * $currency_rate, 2);
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

		$count = 0;

		$total_amount = $variables["order_total"];
		$total_tax_with_shipping = $total_amount;

		foreach ($variables as $v_id => $v_value){
			if ($v_id == "items"){
				foreach ($v_value as $v2_id => $v2_value){
					$items[$count]["item_id"]= $v2_value["item_id"];
					$items[$count]["price"] = $v2_value["price"];
					$items[$count]["quantity"] = $v2_value["quantity"];
					$items[$count]["item_name"] = $v2_value["item_text"];
					$count++;
				}
			}
		}

		for ($i=0;$i<$count;$i++){
			$total_tax_with_shipping -= round($items[$i]["price"]*$items[$i]["quantity"],2);
		}

		if ($total_tax_with_shipping > 0){
			$items[$count]["item_id"]= 32176000;
			$items[$count]["price"] = $total_tax_with_shipping;
			$items[$count]["quantity"] = 1;
			$items[$count]["item_name"] = "Shipping and TAX";
			$count++;
		}

		$param = "";
		for($i=0;$i<$count;$i++){
			$num = $i + 1;
			$param .= "&item_name_". $num ."=".$items[$i]["item_name"]."&item_number_". $num ."=".$items[$i]["item_id"]."&quantity_". $num ."=".$items[$i]["quantity"]."&item_amount_". $num ."=".$items[$i]["price"];
		}
		
		$num = $i+1;
		
		$time_stamp = parse_date($time_stamp, array("D", " ", "MMM", " ", "YYYY", ", ", "h", ":", "mm", " ", "AM"), &$date_errors, $control_name = "");
		$time_stamp = va_date(array("YYYY","-","MM","-","DD",".","HH",":","mm",":","ss"),$time_stamp);
		
		$checksum = $secret;
		$checksum.= $merchant_id;
		$checksum.= $currency;
		$checksum.= $total_amount;
		$total_tax_with_shipping = $total_amount;
		for ($i=0;$i<$count;$i++){
			$checksum.= $items[$i]["item_name"];
			$checksum.= $items[$i]["price"];
			$checksum.= $items[$i]["quantity"];
		}

		$checksum.= $time_stamp;

		$checksum = md5($checksum);
		
		$param = str_replace(" ","%20",$param);
		$param = str_replace(":","%3A",$param);
		
		$mass["checksum"] = $checksum;
		$mass["time_stamp"] = $time_stamp;
		$mass["merchant_site_id"] = $merchant_site_id;
		$mass["merchant_id"] = $merchant_id;
		$mass["currency"] = $currency;
		$mass["numberofitems"] = $count;
		$mass["invoice_id"] = $invoice_id;
		$mass["total_amount"] = $total_amount;
		$mass["version"] = "1.0.0";
		
		for ($i=0; $i<$count; $i++){
			$j = $i+1;
			$mass["item_name_".$j] = $items[$i]["item_name"];
			$mass["item_number_".$j] = $items[$i]["item_id"];
			$mass["quantity_".$j] = $items[$i]["quantity"];
			$mass["item_amount_".$j] = $items[$i]["price"];
		}

		//foreach ($mass as $id => $value){	echo $id . " - ". $value . "<br>";}
		
		return $mass;
		
	}

?>