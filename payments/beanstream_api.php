<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  beanstream_api.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Beanstream (www.beanstream.com) transaction handler by www.viart.com
 */

	$ch = curl_init();
	if($ch) {

		if (preg_match("/ordProvince=&/", $post_params) || preg_match("/ordProvince=$/", $post_params)) {
			$post_params = str_replace("ordProvince=", "ordProvince=--", $post_params);
		}

		curl_setopt ($ch, CURLOPT_URL, $advanced_url);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_params);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		set_curl_options ($ch, $payment_parameters);

		$payment_response = curl_exec($ch);
		curl_close($ch);
		$payment_response = trim($payment_response);
		$t->set_var("payment_response", $payment_response);

		if ($payment_response) {
			$response_parameters = array();
			$response_parts = explode("&", $payment_response);
			if (sizeof($response_parts) == 1) {
				$error_message = "Bad response from gateway: " . $payment_response;
			} else {

				for($i = 0; $i < sizeof($response_parts); $i++) {
					$response_part = explode('=', $response_parts[$i]);
					$response_parameters[$response_part[0]] = urldecode($response_part[1]);
					$response_parameters[strtolower($response_part[0])] = urldecode($response_part[1]);
				}
				foreach ($response_parameters as $parameter_name => $parameter_value) {
					$t->set_var($parameter_name, $parameter_value);
				}

				// check if transaction approved by payment system
				if (!isset($response_parameters["trnApproved"])) {
					$error_message = "Can't obtain authorization parameter 'trnApproved'.";
				} else if ($response_parameters["trnApproved"] != 1) {
					if (isset($response_parameters["messageText"]) && strlen($response_parameters["messageText"])) {
						$error_message = $response_parameters["messageText"];
					} else {
						$error_message = "Your transaction has been declined.";
					}
				} else {
					$transaction_id = $response_parameters["trnId"];
				}
			}

		} else {
			$error_message = "Empty response from payment gateway.";
		}
	} else {
		$error_message = "Can't initialize cURL.";
	}


?>