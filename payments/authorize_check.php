<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  authorize_check.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Authorize.net SIM (www.authorize.net) transaction handler by http://www.viart.com/
 */

	// get payments parameters for validation
	$x_login 	= isset($payment_params["x_login"]) ? $payment_params["x_login"] : "";
	$x_secret 	= isset($payment_params["x_secret"]) ? $payment_params["x_secret"] : "";

	// convert authorize parameters into lowercase
	$x_params = array();
	if (isset($_POST)) {
		foreach ($_POST as $param_name => $param_value) {
			$lower_name = strtolower($param_name);
			$x_params[$lower_name] = $param_value;
			$t->set_var($lower_name, $param_value);
		}
	} else {
		foreach ($HTTP_POST_VARS as $param_name => $param_value) {
			$lower_name = strtolower($param_name);
			$x_params[$lower_name] = $param_value;
			$t->set_var($lower_name, $param_value);
		}
	}

	// get parameters passed from Authorize.net
	$transaction_id  = isset($x_params["x_trans_id"]) ? $x_params["x_trans_id"] : ""; // Authorize.net transaction number
	$order_id        = isset($x_params["x_invoice_num"]) ? $x_params["x_invoice_num"] : ""; // Our order number
	$response_code   = isset($x_params["x_response_code"]) ? $x_params["x_response_code"] : ""; // 1 - Approved, 2 - Declined, 3 - Error, 4 - Held for review
	$reason_code     = isset($x_params["x_response_reason_code"]) ? $x_params["x_response_reason_code"] : ""; // Reason code
	$reason_text     = isset($x_params["x_response_reason_text"]) ? $x_params["x_response_reason_text"] : ""; // Reason text
	$amount          = isset($x_params["x_amount"]) ? $x_params["x_amount"] : ""; // Total purchase amount.
	$x_md5_hash      = isset($x_params["x_md5_hash"]) ? $x_params["x_md5_hash"] : ""; // Hash from Authorize.net

	$our_md5_hash = md5($x_secret.$x_login.$transaction_id.$amount); // Our key

	// check parameters
	if (!strlen($response_code)) {
		$error_message = str_replace("{param_name}", "response code", CANNOT_OBTAIN_PARAMETER_MSG);
	} elseif (!strlen($order_id)) {
		$error_message = str_replace("{param_name}", "invoice number", CANNOT_OBTAIN_PARAMETER_MSG);
	} elseif (!strlen($amount)) {
		$error_message = str_replace("{param_name}", "amount", CANNOT_OBTAIN_PARAMETER_MSG);
	} elseif (!strlen($x_login)) {
		$error_message = str_replace("{param_name}", "login", CANNOT_OBTAIN_PARAMETER_MSG);
	} elseif (!strlen($x_secret)) {
		$error_message = str_replace("{param_name}", "secret", CANNOT_OBTAIN_PARAMETER_MSG);
	} elseif ($response_code == "2") {
		if ($reason_text) { 
			$error_message = $reason_text; 
		} else { 
			$error_message = TRANSACTION_DECLINED_MSG; 
		}
		if ($reason_code) { $error_message .= " (" . $reason_code . ")"; }
	} elseif ($response_code == "3") {
		if ($reason_text) { 
			$error_message = $reason_text; 
		} else { 
			$error_message = PROCESSING_TRANSACTION_ERROR_MSG;
		}
		if ($reason_code) { $error_message .= " (" . $reason_code . ")"; }
	} elseif ($response_code == "4") {
		$pending_message = "Your transaction is being held for review.";
	} elseif ($response_code != "1") {
		$error_message = "Your transaction has been declined. Wrong response code. ";
	} elseif (strtoupper($our_md5_hash) != strtoupper($x_md5_hash)) {
		$error_message = "'Hash' parameter has wrong value.";
	} else {
		$error_message = check_payment($order_id, $amount);
	}

	// set available parameters
	$remote_address   = get_ip();
	$t->set_var("remote_address", $remote_address);
	$t->set_var("x_response_code", $response_code);
	$t->set_var("our_md5_hash", $our_md5_hash);
	$t->set_var("x_md5_hash", $x_md5_hash);
	$t->set_var("x_amount", $amount);

?>