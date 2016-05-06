<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  paypal_pdt.php                                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
* PayPal PDT (www.paypal.com) transaction handler by http://www.viart.com/
*/

	// get payments parameters for validation
	$auth_token     = isset($payment_params["at"]) ? $payment_params["at"] : "";
	$tx_token       = get_param("tx");
	$return_action  = get_param("return_action");
	$business_email = isset($payment_params["business"]) ? $payment_params["business"] : 0;
	$sandbox        = isset($payment_params["sandbox"]) ? $payment_params["sandbox"] : 0;
	$ssl            = isset($payment_params["ssl"]) ? $payment_params["ssl"] : 0;

	/*
	tx - transaction token
	st - status
	amt - transaction amount
	cc - currency code
	cm - Custom message
	sig - signature
	*/

	// check parameters
	if (strtolower($return_action) == 'cancel') {
		$error_message = "Your transaction has been cancelled.";
	}else if (!strlen($auth_token)) {
		$error_message = "Can't obtain your identity token parameter.";
	} else if (!strlen($tx_token)) {
		$error_message = "Can't obtain transaction token parameter.";
	}

	if (strlen($error_message)) {
		return;
	}

	if ($sandbox == 1) {
		$paypal_url = "www.sandbox.paypal.com";
	} else {
		$paypal_url = "www.paypal.com";
	}

	// request params for sending to paypal
	$request_params = "cmd=_notify-synch&tx=" . $tx_token . "&at=" . $auth_token;

	// request header to PayPal system to validate
	$request_header  = "POST /cgi-bin/webscr HTTP/1.0\r\n";
	$request_header .= "Host: " . $paypal_url . "\r\n";
	$request_header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	$request_header .= "Content-Length: " . strlen($request_params) . "\r\n\r\n";

	if($ssl == 1) {
		// If possible, securely post back to paypal using HTTPS
		// Your PHP server will need to be SSL enabled
		$fp = fsockopen ("ssl://" . $paypal_url, 443, $errno, $errstr, 30);
	} else {
		$fp = fsockopen ($paypal_url, 80, $errno, $errstr, 30);
	}

	if (!$fp) {
		// HTTP ERROR
		$pending_message = "Can't connect to PayPal. This order will be reviewed manually. ";
		return;
	}


	fputs ($fp, $request_header . $request_params);
	// read the body data
	$paypal_response = "";
	$header_done = false;
	while (!feof($fp)) {
		$line = fgets ($fp, 1024);
		if (strcmp($line, "\r\n") == 0) {
			// read the header
			$header_done = true;
		} else if ($header_done) {
			// header has been read. now read the contents
			$paypal_response .= $line;
		}
	}
	fclose ($fp);

	// parse the data
	$lines = explode("\n", $paypal_response);
	$status = $lines[0];

	$t->set_var("status", $status);
	$t->set_var("paypal_response", $paypal_response);
	if (strtoupper($status) != "SUCCESS") {
		if (strlen($status)) {
			$error_message = "Transaction status is " . $status;
		} else {
			$error_message = "Can't obtain transaction status";
		}
		return;
	}

	$paypal_params = array();
	for ($i = 1; $i < sizeof($lines); $i++) {
		$param_line = trim($lines[$i]);
		if (strlen($param_line)) {
			list($param_name, $param_value) = explode("=", $param_line);
			$param_name = urldecode($param_name);
			$param_value = urldecode($param_value);
			$paypal_params[$param_name] = $param_value;
			$t->set_var($param_name, $param_value);
		}
	}

	$transaction_id   = $paypal_params["txn_id"];
	$payment_status   = $paypal_params["payment_status"];
	$payment_currency = $paypal_params["mc_currency"];
	$payment_amount   = $paypal_params["mc_gross"];
	$receiver_email   = $paypal_params["receiver_email"];
	$pending_reason   = isset($paypal_params["pending_reason"]) ? $paypal_params["pending_reason"] : "Pending";

	if (strtolower($payment_status) == "pending") {
		$pending_message = $pending_reason;
	}

	if (strtolower($payment_status) != "completed" && strtolower($payment_status) != "pending") {	// check the payment_status is Completed
		$error_message = "Your payment status is " . $payment_status;
	} else if (strtolower(trim($business_email)) != strtolower(trim($receiver_email))) {	// check that receiver_email is your Primary PayPal email
		$error_message = "Wrong receiver email - " . $receiver_email;
	} else {
		// check that payment_amount/payment_currency are correct
		$error_message = check_payment($order_id, $payment_amount, $payment_currency);
	}

	// update transaction information
	$sql  = " UPDATE " . $table_prefix . "orders SET transaction_id=" . $db->tosql($transaction_id, TEXT);
	$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$db->query($sql);

?>