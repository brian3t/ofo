<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  protx_direct.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Protx (www.protx.com) transaction handler by www.viart.com
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
		curl_setopt ($ch, CURLOPT_TIMEOUT,30);
		set_curl_options ($ch, $payment_parameters);

		$payment_response = curl_exec($ch);

		if (curl_error($ch)) {
			$error_message = curl_error($ch);
		}

		curl_close($ch);
		$payment_response = trim(strip_tags($payment_response));

		$t->set_var("payment_response", $payment_response);
		if ($payment_response) {
			$response_parameters = array();
			$response_parts = explode(chr(10), $payment_response);
			if (sizeof($response_parts) == 1) {
				$error_message = "Bad response from gateway: " . $payment_response;
			} else {

				for($i = 0; $i < sizeof($response_parts); $i++) {
					$response_part = explode('=', $response_parts[$i], 2);
					$response_parameters[trim($response_part[0])] = urldecode(trim($response_part[1]));
					$response_parameters[strtolower(trim($response_part[0]))] = urldecode(trim($response_part[1]));
				}
				foreach ($response_parameters as $parameter_name => $parameter_value) {
					$t->set_var($parameter_name, $parameter_value);
				}
				set_session("session_payment_response", $response_parameters);

				// check if transaction approved by payment system
				$status = isset($response_parameters["status"]) ? $response_parameters["status"] : "";
				$status_detail = isset($response_parameters["statusdetail"]) ? $response_parameters["statusdetail"] : "";
				$transaction_id = isset($response_parameters["vpstxid"]) ? $response_parameters["vpstxid"] : "";
				if (!strlen($status)) {
					$error_message = "Can't obtain status authorization parameter.";
				} else if (strtoupper($status) == "NOTAUTHED") {
					$error_message = "Transaction was not authorised.";
				} else if (strtoupper($status) == "REJECTED") {
					$error_message = "Transaction was rejected.";
				} else if (strtoupper($status) == "MALFORMED") {
					$error_message = (strlen($status_detail)) ? $status_detail : "You have missed important fields, or formatted the POST badly.";
				} else if (strtoupper($status) == "INVALID") {
					$error_message = (strlen($status_detail)) ? $status_detail : "You have send badly formatted or incorrect data.";
				} else if (strtoupper($status) == "ERROR") {
					$error_message = (strlen($status_detail)) ? $status_detail : "Some errors occurred during handling your transaction.";
				} else if (strtoupper($status) == "3DAUTH") {
					$variables["secure_3d_check"] = $status;
					if (isset($response_parameters["ACSURL"]) && $response_parameters["ACSURL"]) {
						$variables["secure_3d_acsurl"] = $response_parameters["ACSURL"];
					} else {
						$error_message = "Can't obtain authentication parameter 'ACSURL'.";
					}
					if (isset($response_parameters["PAReq"]) && $response_parameters["PAReq"]) {
						$variables["secure_3d_pareq"] = str_replace(" ", "+", $response_parameters["PAReq"]);
					} else {
						$error_message = "Can't obtain authentication parameter 'PAReq'.";
					}
					if (isset($response_parameters["MD"]) && $response_parameters["MD"]) {
						$variables["secure_3d_md"] = $response_parameters["MD"];
					} else {
						$error_message = "Can't obtain authentication parameter 'MD'.";
					}
					$update_order_status = false;
				} else if (strtoupper($status) != "OK") {
					$error_message = "Unknown transaction status.";
				} else if (strtoupper($status) == "OK") {
					// update transaction information
					if (strlen($transaction_id)) {
						$sql  = " UPDATE " . $table_prefix . "orders SET transaction_id=" . $db->tosql($transaction_id, TEXT);
						$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
						$db->query($sql);

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