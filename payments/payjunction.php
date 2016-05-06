<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  payjunction.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * PayJunction (http://www.PayJunction.com) handler by James McGuire, PayJunction, Inc.
 */

	$error_message = "";
	$ch = curl_init();
	if($ch) {
		
		curl_setopt($ch, CURLOPT_URL, $advanced_url);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_params);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		set_curl_options ($ch, $payment_parameters);
		$payment_response = curl_exec($ch);

		if (curl_error($ch)) {
			$error_message = curl_error($ch);
		}

		curl_close($ch);
		$payment_response = trim($payment_response);
		$t->set_var("payment_response", $payment_response);
		if(!strlen($error_message)){
			if (strlen($payment_response)) {
				$content = explode(chr (28), $payment_response);
				foreach ($content as $key_value) {
					list ($key, $value) = explode("=", $key_value);
					$response[$key] = $value;
				}
				$transaction_id = isset($response["dc_transaction_id"]) ? $response["dc_transaction_id"] : "";
				if (isset($response['dc_response_code'])) {
					if ($response['dc_response_code']!='00' && $response['dc_response_code']!='85') {
						$error_message = isset($response["dc_response_message"]) ? $response["dc_response_message"] : "error code ".$response['dc_response_code'];
					}
				} else {
					$error_message = "Can't obtain data for your transaction.";
				}
			} else {
				$error_message = "Can't obtain data for your transaction.";
			}
		}
	} else {
		$error_message = "Can't initialize cURL.";
	}
?>