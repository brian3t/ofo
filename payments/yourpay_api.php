<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  yourpay_api.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * YourPay (www.yourpay.com) transaction handler by www.viart.com
 */

	global $is_admin_path, $is_sub_folder;
	$root_folder_path = ((isset($is_admin_path) && $is_admin_path) || (isset($is_sub_folder) && $is_sub_folder)) ? "../" : "./";
	include_once($root_folder_path . "payments/linkpoint_functions.php");

	$pass_data = array();
	foreach ($payment_parameters as $parameter_name => $parameter_value) {
		if(isset($pass_parameters[$parameter_name]) && $pass_parameters[$parameter_name] == 1) {
			$pass_data[$parameter_name] = $parameter_value;
		}
	}
	if (isset($pass_data["cvmvalue"]))  {
		if (strlen($pass_data["cvmvalue"])) {
			$pass_data["cvmindicator"] = "provided";
		} else {
			$pass_data["cvmindicator"] = "not_provided";
		}
	} else {
		$pass_data["cvmindicator"] = "not_provided";
	}

	$xml = linkpoint_order_xml($pass_data);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $advanced_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $xml); // the string we built above
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSLCERT, $payment_parameters["keyfile"]);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	set_curl_options($ch, $payment_parameters);

	// send the string to LSGS
	$payment_response = curl_exec($ch);
	curl_close($ch);
	$payment_response = trim($payment_response);
	$t->set_var("payment_response", $payment_response);
	if ($payment_response) {
		$response_parameters = array();
		// convert xml into array
		preg_match_all ("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", $payment_response, $matches, PREG_SET_ORDER);
		for ($i = 0; $i < sizeof($matches); $i++) {
			$response_parameters[$matches[$i][1]] = ($matches[$i][2]);
		}

		foreach ($response_parameters as $parameter_name => $parameter_value) {
			$t->set_var($parameter_name, $parameter_value);
		}

		// check if transaction approved by payment system
		if (!isset($response_parameters["r_approved"])) {
			$error_message = "Can't obtain authorization parameter.";
		} elseif (strtoupper($response_parameters["r_approved"]) != "APPROVED") {
			if (isset($response_parameters["r_error"]) && strlen($response_parameters["r_error"])) {
				$error_message = $response_parameters["r_error"];
			} else {
				$error_message = "Your transaction has been declined.";
			}
		} else {
			$transaction_id = $response_parameters["r_code"];
		}

	} else {
		$error_message = "Empty response from gateway. Please check your settings.";
	}

?>