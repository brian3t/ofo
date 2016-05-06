<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  netbilling.php                                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Netbilling (www.netbilling.com) transaction handler by www.viart.com
 */

	$pass_data = array();
	foreach ($payment_parameters as $parameter_name => $parameter_value) {
		if(isset($pass_parameters[$parameter_name]) && $pass_parameters[$parameter_name] == 1) {
			$pass_data[$parameter_name] = $parameter_value;
		}
	}

	$params ='';
	if (isset($pass_data["cardexpmonth"]) && isset($pass_data["cardexpyear"])) {
		$pass_data["card_expire"] = $pass_data["cardexpmonth"].$pass_data["cardexpyear"];
		unset($pass_data["cardexpmonth"]);
		unset($pass_data["cardexpyear"]);
	}
	foreach($pass_data as $k => $v) {
		if(!empty($params))
			$params .= '&';
		$params .= $k.'='.urlencode($v);
	}

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $advanced_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 90);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	curl_setopt($ch, CURLOPT_NOBODY, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_ENCODING, "x-www-form-urlencoded");
	set_curl_options ($ch, $payment_parameters);

	$result=curl_exec ($ch);
	$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if (curl_errno($ch))
		$error_message = curl_errno($ch)." - ".curl_error($ch);
	curl_close ($ch);

	$resp = explode("\n\r\n", $result);
	$header = explode("\n", $resp[0]);
	parse_str($resp[1], $result);

	if($http_code == "200") {
		if (isset($result['status_code'])) {
			$status_code = $result['status_code'];
		} else {
			$status_code = '';
		}
		if (isset($result['auth_code'])) {
			$auth_code = $result['auth_code'];
		} else {
			$auth_code = '';
		}
		if (isset($result['auth_msg'])) {
			$auth_msg = $result['auth_msg'];
		} else {
			$auth_msg = '';
		}

		if($status_code == '0' || $status_code == 'F') {
			if($auth_msg == 'BAD ADDRESS') {
				$error_message = "Invalid Address";
			} else if($auth_msg == 'CVV2 MISMATCH') {
				$error_message = "Invalid CVV2";
			} else if($auth_msg == 'A/DECLINED') {
				$error_message = "You have tried too many times.  Please contact support.";
			} else if($auth_msg == 'B/DECLINED') {
				$error_message = "Please contact support.";
			} else if($auth_msg == 'C/DECLINED') {
				$error_message = "Please contact support.";
			} else if($auth_msg == 'E/DECLINED') {
				$error_message = "Your email address is invalid.";
			} else if($auth_msg == 'J/DECLINED') {
				$error_message = "Your information is invalid.  Please correct";
			} else if($auth_msg == 'L/DECLINED') {
				$error_message = "Invalid Address";
			} else {
				$error_message = "Your card was declined.  Please try again.";
			}
		} else if($status_code == 'I') {
			$pending_message = "Pending transaction.";
			$transaction_id = $auth_code;
		} else if($status_code == '1' || $status_code == 'T' || $status_code == 'D') {
			// transaction was approved
			$transaction_id = $auth_code;
		} else {
			$error_message = 'Unknown Status.';
		}

	} else {
		$gateway_error_msg = substr($header[0], 13);
		$error_message = "Please contact support: " . $gateway_error_msg;
	}

?>