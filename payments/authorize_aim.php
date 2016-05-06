<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  authorize_aim.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Authorize.net AIM (www.authorize.net) transaction handler by http://www.viart.com/
 */

	$pass_data = array();
	foreach ($payment_parameters as $parameter_name => $parameter_value) {
		if (isset($pass_parameters[$parameter_name]) && $pass_parameters[$parameter_name] == 1) {
			$pass_data[$parameter_name] = $parameter_value;
		}
	}

	$ch = curl_init();
	if ($ch) 
	{
		curl_setopt($ch, CURLOPT_URL, $advanced_url);
		if (isset($payment_parameters["sslcert"]) && strlen(trim($payment_parameters["sslcert"]))) {
			curl_setopt ($ch, CURLOPT_SSLCERT, $payment_parameters["sslcert"]);
		}
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		set_curl_options($ch, $payment_parameters);

		$payment_response = curl_exec($ch);
		curl_close($ch);
		$payment_response = trim($payment_response);
		$t->set_var("payment_response", $payment_response);
		if ($payment_response) {
			$response_parameters = array();
			$delimiter = (isset($pass_data["x_delim_char"]) && strlen($pass_data["x_delim_char"])) ? $pass_data["x_delim_char"] : ",";
			$response_parameters = explode($delimiter, $payment_response);
			for ($i = 0; $i < sizeof($response_parameters); $i++) {
				$parameter_name = ($i + 1);
				$t->set_var($parameter_name, $response_parameters[$i]);
			}

			// check if transaction approved by payment system
			$transaction_status = $response_parameters[0];
			if (isset($response_parameters[6])){
				$transaction_id  = strlen($response_parameters[6]) ? $response_parameters[6] : "";
			}
			if ($response_parameters[0] != 1) {
				if (isset($response_parameters[3]) && strlen($response_parameters[3])) {
					$error_message = $response_parameters[3];
					$error_message .= strlen($response_parameters[2]) ? " Error code: " . $response_parameters[2] : ""; 
				} else {
					$error_message = TRANSACTION_DECLINED_MSG;
				}
			}
		} else {
			$error_message = EMPTY_GATEWAY_RESPONSE_MSG;
		}
	} else {
		$error_message = CURL_INIT_ERROR_MSG;
	}

?>