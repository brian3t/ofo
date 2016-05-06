<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  2checkout.php                                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * 2Checkout (www.2checkout.com) transaction handler by www.viart.com
 */

	// get payments parameters for validation
	$vendor_number  = isset($payment_params["sid"]) ? $payment_params["sid"] : "";
	$secret_word    = isset($payment_params["secret_word"]) ? $payment_params["secret_word"] : "";
	$demo_mode      = isset($payment_params["demo"]) ? $payment_params["demo"] : "";
	$demo_passed    = isset($pass_parameters["demo"]) ? $pass_parameters["demo"] : "";

	// get parameters passed from 2Checkout.com
	$transaction_id = get_param("order_number"); // 2Checkout.com order number
	$cart_order_id  = get_param("cart_order_id"); // Your cart ID number passed in.
	$cc_processed   = get_param("credit_card_processed"); // Y if successful, K if waiting for approval
	$total          = get_param("total"); // Total purchase amount.
	$co_key         = get_param("key"); // Key from 2Checkout.com

	// check parameters
	if (!strlen($vendor_number)) {
		$error_message = str_replace("{param_name}", "sid", CANNOT_OBTAIN_PARAMETER_MSG);
	} elseif (!strlen($secret_word)) {
		$error_message = str_replace("{param_name}", "secret_word", CANNOT_OBTAIN_PARAMETER_MSG);
	} elseif (!strlen($transaction_id)) {
		$error_message = str_replace("{param_name}", "order_number", CANNOT_OBTAIN_PARAMETER_MSG);
	} elseif (!strlen($cart_order_id)) {
		$error_message = str_replace("{param_name}", "cart_order_id", CANNOT_OBTAIN_PARAMETER_MSG);
	} elseif (!strlen($total)) {
		$error_message = str_replace("{param_name}", "total", CANNOT_OBTAIN_PARAMETER_MSG);
	}

	if (strlen($error_message)) {
 		return;
	}

	if (strtoupper($demo_mode) == "Y" && $demo_passed == 1) {
		$transaction_id = "1"; // in demo mode it's always 1
	}
	$our_key = md5($secret_word . $vendor_number . $transaction_id . $total); // Our key

	if ($cc_processed == "K") {
		$pending_message = CHECKOUT_PENDING_MSG;
	}

	// use some checks on placed order
	if(strtoupper($co_key) != strtoupper($our_key)) {
		$error_message = str_replace("{param_name}", "'Verification Key'", PARAMETER_WRONG_VALUE_MSG);
	} elseif ($cc_processed != "Y" && $cc_processed != "K") {
		$error_message = TRANSACTION_DECLINED_MSG;
	} else {
		$error_message = check_payment($cart_order_id, $total);
	}

	if (strlen($error_message)) {
 		return;
	}

	// get parameters from 2Checkout.com
	$remote_address   = get_ip();
	$card_holder_name = get_param("card_holder_name"); // Card holder's name
	$street_address   = get_param("street_address"); // Card holder's address
	$city             = get_param("city"); // Card holder's city
	$state            = get_param("state"); // Card holder's state
	$zip              = get_param("zip"); // Card holder's zip
	$country          = get_param("country"); // Card holder's country
	$email            = get_param("email"); // Card holder's email
	$phone            = get_param("phone"); // Card holder's phone

	$ship_name           = get_param("ship_name"); // Shipping information
	$ship_street_address = get_param("ship_street_address"); // Shipping information
	$ship_city           = get_param("ship_city"); // Shipping information
	$ship_state          = get_param("ship_state"); // Shipping information
	$ship_zip            = get_param("ship_zip"); // Shipping information
	$ship_country        = get_param("ship_country"); // Shipping information

	// set variables to parse
	$t->set_var("card_total",           $total);
	$t->set_var("order_number",         $transaction_id);
	$t->set_var("transaction_id",       $transaction_id);
	$t->set_var("credit_card_processed",$credit_card_processed);
	$t->set_var("key",                  $key);
	$t->set_var("our_key",              $our_key);
	$t->set_var("remote_address",       $remote_address);


	$t->set_var("card_holder_name",     $card_holder_name);
	$t->set_var("card_street_address",  $street_address);
	$t->set_var("card_city",            $city);
	$t->set_var("card_state",           $state);
	$t->set_var("card_zip",             $zip);
	$t->set_var("card_country",         $country);
	$t->set_var("card_email",           $email);
	$t->set_var("card_phone",           $phone);
	$t->set_var("ship_name",            $ship_name);
	$t->set_var("ship_street_address",  $ship_street_address);
	$t->set_var("ship_city",            $ship_city);
	$t->set_var("ship_state",           $ship_state);
	$t->set_var("ship_zip",             $ship_zip);
	$t->set_var("ship_country",         $ship_country);

?>