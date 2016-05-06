<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  hsbc_cpi_functions.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * The Cardholder Payment Interface (CPI) within HSBC Secure ePayments (http://www.hsbc.com/) 
 * transaction handler by www.viart.com
 */
function checkOrder() {	
	global $order_id, $success_message, $error_message, $pending_message;
	global $post_parameters, $payment_params, $pass_parameters, $pass_data, $variables;
	$order_id = get_param("OrderId");
	
	if ($order_id) {		
		$post_parameters = ""; 
		$payment_params = array(); 
		$pass_parameters = array(); 
		$pass_data = array(); 
		$variables = array();
		get_payment_parameters($order_id, $payment_params, $pass_parameters, $post_parameters, $pass_data, $variables, "");
	
		$post_fields = createFields($order_id, $payment_params, $pass_parameters, $post_parameters, $pass_data, $variables, $storefront_id , $cpi_hash_key);
		
		$send_amount   = get_param("PurchaseAmount");
		$send_currency = get_param("PurchaseCurrency");
		$send_email    = get_param("ShopperEmail");
		$send_storefront_id = get_param("StorefrontId");
		$send_hash = get_param("OrderHash");	
				
		if ((int) $post_fields["PurchaseAmount"] != (int)$send_amount) {
			$error_message .=  " Order amount is not valid.";
		}
		if ($post_fields["PurchaseCurrency"] != $send_currency) {
			$error_message .=  " Order currency is not valid.";
		}
		if ($post_fields["ShopperEmail"] != $send_email) {
			$error_message .=  " Order email is not valid.";
		}
		if ($post_fields["StorefrontId"] != $send_storefront_id) {
			$error_message .=  " Order storefront id is not valid.";
		}
		
		$send =  $_POST;	
		unset($send["OrderHash"]);
		$send_hash_check = getHash($cpi_hash_key, $send, $payment_params);
		if ($send_hash_check != $send_hash) {			
			$error_message .=  " Order hash is not valid.";
		}
		
		$cpi_result_code = get_param("CpiResultsCode");
		
		switch ($cpi_result_code) {
			case 0:
				$success_message = " The transaction was approved. ";
			break;
			case 1:
				$error_message .= " The user cancelled the transaction. ";
			break;
			case 2:
				$error_message .= " The processor declined the transaction for an unknown reason. ";
			break;
			case 3:
				$error_message .= " The transaction was declined because of a problem with the card. For example, an invalid card number or expiration date was specified. ";
			break;
			case 4:
				$error_message .=  "The processor did not return a response. ";
			break;
			case 5:
				$error_message .= " The amount specified in the transaction was either too high or too low for the processor. ";
			break;
			case 6:
				$error_message .= " The specified currency is not supported by either the processor or the card. ";
			break;
			case 7:
				$error_message .= " The order is invalid because the order ID is a duplicate. ";
			break;
			case 8:
				$error_message .= " The transaction was rejected by FraudShield. ";
			break;
			case 9:
				$pending_message .= " The transaction was placed in Review state by FraudShield. ";
			break;
			case 10:
				$error_message .= " The transaction failed because of invalid input data. ";
			break;
			case 11:
				$error_message .= " The transaction failed because the CPI was configured incorrectly. ";
			break;
			case 12:
				$error_message .= " The transaction failed because the Storefront was configured incorrectly. ";
			break;
			case 13:
				$error_message .= " The connection timed out. ";
			break;
			case 14:
				$error_message .= " The transaction failed because the cardholder’s browser refused a cookie. ";
				
			break;
			case 15:
				$error_message .= " The customer’s browser does not support 128-bit encryption. ";
			break;
			case 16:
				$error_message .= " The CPI cannot communicate with the Payment Engine. ";
			break;
			case 17:
				$error_message .= " Order CPI code is not valid. ";
			break;
		}
	} else {
		$error_message .= " Order number is not valid. ";
	}
}
function createFields($order_id, $payment_params, $pass_parameters, $post_parameters, $pass_data, $variables, &$storefront_id , &$cpi_hash_key) {
	global $db, $table_prefix;
	if(!is_array($payment_params)) {
		echo 'Payment parameters are not sent!';
		exit;
	}
	if (isset($payment_params["storefront_id"]) && $payment_params["storefront_id"]) {
		$storefront_id = $payment_params["storefront_id"];
	} else {
		echo 'StorefrontId (ClientId – sent by email) is required!';
		exit;
	}
	if (isset($payment_params["cpi_hash_key"]) && $payment_params["cpi_hash_key"]) {
		$cpi_hash_key = $payment_params["cpi_hash_key"];
	} else {
		echo 'CPI Hash Key (“shared secret” – sent by letter) is required!';
		exit;
	}
	
	$post_fields = array();
	
	$post_fields["OrderId"]      = $order_id;
	$post_fields["TimeStamp"]    = time() . "000";
	
	$post_fields["CpiReturnUrl"]       = $payment_params["CpiReturnUrl"];
	$post_fields["CpiDirectResultUrl"] = $payment_params["CpiDirectResultUrl"];
	
	$post_fields["StorefrontId"] = $storefront_id;
	if (isset($payment_params["OrderDesc"])) {
		$post_fields["OrderDesc"] = substr($payment_params["OrderDesc"], 0, 54);
	} else {
		$post_fields["OrderDesc"] = "Payment";
	}
	$post_fields["PurchaseAmount"] = $variables["order_total"] * 100;
	$post_fields["PurchaseCurrency"] = $variables["currency_value"];
	if (isset($payment_params["TransactionType"])) {
		$post_fields["TransactionType"] = $payment_params["TransactionType"];
	} else {
		$post_fields["TransactionType"] = "Auth";
	}
	if (isset($payment_params["Mode"])) {
		$post_fields["Mode"] = $payment_params["Mode"];
	} else {
		$post_fields["Mode"] = "T";
	}
	if ($post_fields["Mode"] == "T") {
		$post_fields["PurchaseAmount"] = 100;
	}
	
	$post_fields["BillingFirstName"]  = substr($variables["first_name"], 0, 32);
	$post_fields["BillingLastName"]   = substr($variables["last_name"], 0, 32);
	$post_fields["ShopperEmail"]      = substr($variables["email"], 0, 64);
	$post_fields["BillingAddress1"]   = substr($variables["address1"], 0, 60);
	$post_fields["BillingAddress2"]   = substr($variables["address2"], 0, 60);
	$post_fields["BillingCity"]       = substr($variables["city"], 0, 25);
	if ($variables["state_code"]) {
		$post_fields["BillingCounty"] = $variables["state_code"];
	} else {
		$post_fields["BillingCounty"]     = substr($variables["province"], 0, 25);
	}
	$post_fields["BillingPostal"]     = substr($variables["zip"], 0, 20);
	
	if (isset($variables["country_id"]) && $variables["country_id"]) {
		$sql  = " SELECT country_iso_number FROM " . $table_prefix . "countries ";
		$sql .= " WHERE country_id=" . $db->tosql($variables["country_id"], INTEGER);
		$post_fields["BillingCountry"]    = get_db_value($sql);
	}
	$post_fields["ShippingFirstName"] = substr($variables["delivery_first_name"], 0, 32);
	$post_fields["ShippingLastName"]  = substr($variables["delivery_last_name"], 0, 32);
	$post_fields["ShippingAddress1"]  = substr($variables["delivery_address1"], 0, 60);
	$post_fields["ShippingAddress2"]  = substr($variables["delivery_address2"], 0, 60);
	$post_fields["ShippingCity"]      = substr($variables["delivery_city"], 0, 25);
	if ($variables["state_code"]) {
		$post_fields["ShippingCounty"] = $variables["delivery_state_code"];
	} else {
		$post_fields["ShippingCounty"]     = substr($variables["delivery_province"], 0, 25);
	}
	$post_fields["ShippingPostal"]    = substr($variables["delivery_zip"], 0, 20);
	
	if (isset($variables["delivery_country_id"]) && $variables["delivery_country_id"]) {
		$sql  = " SELECT country_iso_number FROM " . $table_prefix . "countries ";
		$sql .= " WHERE country_id=" . $db->tosql($variables["delivery_country_id"], INTEGER);
		$post_fields["ShippingCountry"]   = get_db_value($sql);
	}
	
	return $post_fields;
}

function getHash($cpi_hash_key, $post_fields, $payment_params) {
	$hsbc_cpi_dir = "";
	if (isset($payment_params["hsbc_cpi_dir"])) {
		$hsbc_cpi_dir = $payment_params["hsbc_cpi_dir"];
	}
	
	$hsbc_cpi_filepath = "";
	if (isset($payment_params["hsbc_cpi_filepath"])) {
		$hsbc_cpi_filepath = $payment_params["hsbc_cpi_filepath"];
	}
		
	if (isset($payment_params["use_java"])) {
		if ($hsbc_cpi_dir)
			chdir($hsbc_cpi_dir);
		$hash_exec_command = "java -classpath cpitools.jar;. hsbc_cpi";
		if (!exec($hash_exec_command)) {
			echo 'Java CPI Class is required!';
			exit;
		}
	} else {
		if ($hsbc_cpi_filepath) {
			$hash_exec_command = $hsbc_cpi_filepath;
		} else {
			$hash_exec_command = $hsbc_cpi_dir . "hsbc_cpi";
		}
	}
	$hash_exec_command .= " " . $cpi_hash_key;
	foreach ($post_fields AS $value) {
		$value = trim($value);
		if (strlen($value)) {
			if (strpos($value, " ") !== false) {
				$value = "\"" . $value . "\"";
			}
			$hash_exec_command .= " " . $value;
		}
	}
	exec($hash_exec_command, $result);
	$hash_exec = $result[0];
	if (isset($payment_params["use_java"])) {
		if (strpos($hash_exec, "Cant generate hash") !== false) {
			echo $hash_exec;
			exit;
		} else {
			return $hash_exec;
		}
	} else {
		if (strpos($hash_exec, "Hash value: ") === 0) {
			return substr($hash_exec, 12);
		} else {
			echo $hash_exec;
			exit;
		}
	}
}
?>