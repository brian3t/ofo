<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  akbank.php                                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Akbank (www.est.com.tr) transaction handler by www.viart.com
 */

	global $is_admin_path, $is_sub_folder;
	$root_folder_path = ((isset($is_admin_path) && $is_admin_path) || (isset($is_sub_folder) && $is_sub_folder)) ? "../" : "./";
	include_once($root_folder_path . "payments/akbank_functions.php");

 	$error_message = "";
	$pass_data = array();
	foreach ($payment_parameters as $parameter_name => $parameter_value) {
		if (isset($pass_parameters[$parameter_name]) && $pass_parameters[$parameter_name] == 1) {
			$pass_data[$parameter_name] = $parameter_value;
		}
	}
	$xml = akbank_cc5_request($pass_data);
	$xml = "DATA=" . $xml;

	$ch = curl_init();
	if ($ch) {
		curl_setopt($ch, CURLOPT_URL, $advanced_url);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml); // the string we built above
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		set_curl_options($ch, $payment_parameters);

		// send the string to Garanti
		$payment_response = curl_exec($ch);
		curl_close($ch);
		$payment_response = trim($payment_response);
		$t->set_var("payment_response", $payment_response);
		if ($payment_response) {
			$response_parameters = array();
			if (preg_match("/<CC5Response>(.*)<\/CC5Response>/sm", $payment_response, $matches)) {
				$payment_response = $matches[1];
				// convert xml into array
				preg_match_all ("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", $payment_response, $matches, PREG_SET_ORDER);
				for($i = 0; $i < sizeof($matches); $i++) {
					$response_parameters[$matches[$i][1]] = ($matches[$i][2]);
				}

				foreach ($response_parameters as $parameter_name => $parameter_value) {
					$t->set_var($parameter_name, $parameter_value);
				}

				// check if transaction approved by payment system
				if (!isset($response_parameters["ProcReturnCode"])) {
					$error_message = str_replace("{param_name}", "Return Code", CANNOT_OBTAIN_PARAMETER_MSG);
				} elseif ($response_parameters["ProcReturnCode"] != "0") {
					switch ($response_parameters["ProcReturnCode"]) {
						case "12": $error_message = "Invalid transaction."; break;
						case "51": $error_message = "Insufficient funds."; break;
						case "54": $error_message = "Card Expired."; break;
						case "82": $error_message = "Incorrect CVV."; break;
						case "93": $error_message = "Transaction can't be completed (violation of law)."; break;
						case "99":
							$error_message = "Transaction declined. Possible reasons: The card failed compliancy checks, The card has expired, Insufficient permissions to perform requested operation, Value for element 'Total' is not valid.";
							break;
						default: $error_message = TRANSACTION_DECLINED_MSG;
					}
					if (isset($response_parameters["ErrMsg"]) && strlen($response_parameters["ErrMsg"])) {
						$error_message .= " " . $response_parameters["ErrMsg"];
					}
				} else {
					$transaction_id = isset($response_parameters["TransId"]) ? $response_parameters["TransId"] : "0";
				}
			} else {
				$error_message = UNEXPECTED_GATEWAY_RESPONSE_MSG;
			}
		} else {
			$error_message = EMPTY_GATEWAY_RESPONSE_MSG;
		}
	} else {
		$error_message = CURL_INIT_ERROR_MSG;
	}

?>