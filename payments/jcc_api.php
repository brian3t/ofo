<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  jcc_api.php                                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * JccSecure (jccsecure.com) transaction handler by www.viart.com
 */

	$pass_data = array();
	foreach ($payment_parameters as $parameter_name => $parameter_value) {
		if (isset($pass_parameters[$parameter_name]) && $pass_parameters[$parameter_name] == 1) {
			if($parameter_name == 'purchaseamt' || strtolower($parameter_name) == 'purchaseamt'){
				$parameter_value = str_pad((float)$parameter_value * pow(10, (int)$payment_parameters["purchasecurrencyexponent"]), 12, "0", STR_PAD_LEFT);
			}
			$pass_data[$parameter_name] = $parameter_value;
		}
	}
	$pass_data['signature'] = base64_encode(sha1($payment_parameters["password"] . $payment_parameters["merid"] . $payment_parameters["acqid"] . $payment_parameters["orderid"] . $pass_data["purchaseamt"] . $payment_parameters["purchasecurrency"], TRUE));

	$ch = curl_init ();
	if ($ch)
	{
		curl_setopt ($ch, CURLOPT_URL, $advanced_url);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $pass_data);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		set_curl_options ($ch, $payment_parameters);
	
		$payment_response = curl_exec($ch);
		if (curl_errno($ch)) {
			$error_message = curl_errno($ch)." - ".curl_error($ch);
		}
		curl_close ($ch);
		$payment_response = trim($payment_response);
		$payment_response = str_replace("&amp;", "&", $payment_response);
		$t->set_var("payment_response", $payment_response);
	
		if ($payment_response) {
			parse_str($payment_response, $response_parameters);

			foreach ($response_parameters as $parameter_name => $parameter_value) {
				$t->set_var($parameter_name, $parameter_value);
			}

			$transaction_id = (isset($response_parameters["ReasonCode"]))?$response_parameters["ReasonCode"]:'';

			if (!isset($response_parameters["ResponseCode"])) {
				$error_message = "Can't obtain authorization parameter.";
			} else if ($response_parameters["ResponseCode"] != "1") {
				if (isset($response_parameters["ReasonCodeDesc"]) && strlen($response_parameters["ReasonCodeDesc"])) {
					$error_message = $response_parameters["ReasonCodeDesc"];
				} else {
					$error_message = "Your transaction has been declined.";
				}
			}
		} else {
			$error_message = "Empty response from gateway. Please check your settings.";
		}
	} else {
		$error_message = "Can't initialize cURL.";
	}
?>