<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  vxsbill.php                                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * VXSBill (www.vxsbill.com) transaction handler by www.viart.com
 */

	global $is_admin_path, $is_sub_folder;
	$root_folder_path = ((isset($is_admin_path) && $is_admin_path) || (isset($is_sub_folder) && $is_sub_folder)) ? "../" : "./";
	include_once ($root_folder_path . "payments/vxsbill_functions.php");

	//if (!strlen($country_code)) {return;}
 	$error_message = "";
	$pass_data = array();
	foreach ($payment_parameters as $parameter_name => $parameter_value) {
		if(isset($pass_parameters[$parameter_name]) && $pass_parameters[$parameter_name] == 1) {
			$pass_data[$parameter_name] = $parameter_value;
		}
	}
	$request_string = vxsbill_payment_request($pass_data);
	//echo nl2br(htmlspecialchars($request_string)) . "<hr>";

	$ch = curl_init();
	if ($ch) {
		curl_setopt($ch, CURLOPT_URL, $advanced_url . $request_string);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		set_curl_options ($ch, $payment_parameters);

		// send request string to gateway
		$payment_response = curl_exec($ch);
		curl_close($ch);
		$payment_response = trim($payment_response);
		$t->set_var("payment_response", $payment_response);
		if ($payment_response) {
			//echo nl2br(htmlspecialchars($payment_response)) . "<hr>";

			// check if transaction approved by payment system
			if (strstr($payment_response, "OK")) {
				$transaction_id = substr($result, 4);
			} elseif (stristr($payment_response, "Error")) {
				$error_message = $payment_response;
			} else {
				$error_message = "Transaction declined.";
			}
		} else {
			$error_message = "Empty response from gateway. Please check your settings.";
		}
	} else {
		$error_message = "Can't initialize cURL.";
	}

?>