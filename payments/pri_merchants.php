<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  pri_merchants.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * PRI Merchants (www.primerchants.com) transaction handler by www.viart.com
 */

	$ch = curl_init();
	if ($ch)
	{
		curl_setopt ($ch, CURLOPT_URL, $advanced_url);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_params);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		set_curl_options ($ch, $payment_parameters);

		$payment_response = curl_exec($ch);
		curl_close($ch);
		$payment_response = trim(strip_tags($payment_response));
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
				if (!isset($response_parameters["auth"])) {
					$error_message = "Can't obtain authorization parameter.";
				} else if (strtoupper($response_parameters["auth"]) == "DECLINED") {
					if (isset($response_parameters["notes"]) && strlen($response_parameters["notes"])) {
						$error_message = $response_parameters["notes"];
					} else {
						$error_message = "Your transaction has been declined.";
					}
				}
			}

		} else {
			$error_message = "Can't obtain data for your transaction.";
		}
	} else {
		$error_message = "Can't initialize cURL.";
	}

?>