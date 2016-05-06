<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  posnet.php                                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Posnet (setmpos.ykb.com) transaction handler by www.viart.com
 */

	global $is_admin_path, $is_sub_folder;
	$root_folder_path = ((isset($is_admin_path) && $is_admin_path) || (isset($is_sub_folder) && $is_sub_folder)) ? "../" : "./";
	include_once ($root_folder_path . "payments/posnet_functions.php");

	$pass_data = array();
	foreach ($payment_parameters as $parameter_name => $parameter_value) {
		if(isset($pass_parameters[$parameter_name]) && $pass_parameters[$parameter_name] == 1) {
			$pass_data[$parameter_name] = $parameter_value;
		}
	}
	$xml = posnet_payment_request($pass_data);
	$user_agent = "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)";

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$xml);
	curl_setopt($ch, CURLOPT_URL,$advanced_url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  FALSE);
	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	set_curl_options ($ch, $payment_parameters);

	$payment_response=curl_exec ($ch);
	if (curl_errno($ch)) {
		$error_message = curl_errno($ch)." - ".curl_error($ch);
	}
	curl_close ($ch);

	$payment_response = trim($payment_response);
	if ($payment_response) {
		$response_parameters = array();
		preg_match_all ("/<([^>]*?)>([^<]*?)\<\/[^>]*>/", $payment_response, $matches, PREG_SET_ORDER);
		for($i = 0; $i < sizeof($matches); $i++) {
			$response_parameters[$matches[$i][1]] = ($matches[$i][2]);
		}

		if (!isset($response_parameters["approved"])) {
			$error_message = "Can't obtain authorization parameter.";
		} else if ($response_parameters["approved"] != 1) {
			if (isset($response_parameters["respCode"]) && strlen($response_parameters["respCode"])) {
				$error_message = $response_parameters["respCode"];
				if (isset($response_parameters["respText"])) {
					$error_message .= " - ".$response_parameters["respText"];
				}
			} else {
				$error_message = "Your transaction has been declined.";
			}
		} else {
			$transaction_id = $response_parameters["authCode"];
		}

	} else {
		$error_message = "Empty response from gateway. Please check your settings.";
	}

?>