<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  verisign_pro.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * VeriSign Pro (www.verisign.com) handler by ViArt Ltd (http://www.viart.com/)
 */

	// Get login parameters
	$user      = isset($payment_parameters["user"]) ? $payment_parameters["user"] : "";
	$vendor    = isset($payment_parameters["vendor"]) ? $payment_parameters["vendor"] : "";
	$partner   = isset($payment_parameters["partner"]) ? $payment_parameters["partner"] : "";
	$password  = isset($payment_parameters["password"]) ? $payment_parameters["password"] : "";

	// get libraries parameters to send requests
	$binary    = isset($payment_parameters["binary"]) ? $payment_parameters["binary"] : "";
	$library   = isset($payment_parameters["library"]) ? $payment_parameters["library"] : "";
	$cert_path = isset($payment_parameters["certpath"]) ? $payment_parameters["certpath"] : "";
	$ld_path   = dirname($library);
	$timeout = isset($payment_parameters["timeout"]) ? $payment_parameters["timeout"] : "30";
	$test = isset($payment_parameters["test"]) ? $payment_parameters["test"] : 0;

	// proxy parameters
	$proxyaddress = isset($payment_parameters["proxyaddress"]) ? $payment_parameters["proxyaddress"] : "";
	$proxyport  = isset($payment_parameters["proxyport"]) ? $payment_parameters["proxyport"] : "";
	$proxylogon = isset($payment_parameters["proxylogon"]) ? $payment_parameters["proxylogon"] : "";
	$proxypassword = isset($payment_parameters["proxypassword"]) ? $payment_parameters["proxypassword"] : "";

	$port = "443";
	if ($advanced_url) {
		$host = $advanced_url;
	} else {
		if ($test == 1) {
			$host = "test-payflow.verisign.com";
		} else {
			$host = "payflow.verisign.com";
		}
	}

	putenv("PFPRO_CERT_PATH=" . $cert_path);
	putenv("LD_LIBRARY_PATH=" . $ld_path);

	// prepare params string
	$params = "";
	foreach ($payment_parameters as $parameter_name => $parameter_value) {
		if(strlen($parameter_value) && isset($pass_parameters[$parameter_name]) && $pass_parameters[$parameter_name] == 1) {
			if ($params) { $params .= "&"; }
			$parameter_value = str_replace("\"", "", $parameter_value);
			if (preg_match("/[\&\=]/", $parameter_value)) {
				$params .= strtoupper($parameter_name) . "[" . strlen($parameter_name) . "]=" . $parameter_value;
			} else {
				$params .= strtoupper($parameter_name) . "=" . $parameter_value;
			}
		}
	}

	$exec_string = "$binary $host $port \"$params\" $timeout";
	if ($proxyaddress) { $exec_string .= " " . $proxyaddress; }
	if ($proxyport) { $exec_string .= " " . $proxyport; }
	if ($proxylogon) { $exec_string .= " " . $proxylogon; }
	if ($proxypassword) { $exec_string .= " " . $proxypassword; }

	$payment_response = exec($exec_string, $result_array, $exit_value);

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
			set_session("session_payment_response", $response_parameters);

			// check if transaction approved by payment system
			if (!isset($response_parameters["result"])) {
				$error_message = "Can't obtain authorization parameter 'result'.";
			} else if ($response_parameters["result"] == 126) {
				if (isset($response_parameters["respmsg"]) && strlen($response_parameters["respmsg"])) {
					$pending_message = $response_parameters["respmsg"];
				} else {
					$pending_message = "Your transaction wasn't finished and waiting for approval.";
				}
			} else if ($response_parameters["result"] != 0) {
				if (isset($response_parameters["respmsg"]) && strlen($response_parameters["respmsg"])) {
					$error_message = $response_parameters["respmsg"];
				} else {
					$error_message = "Your transaction has been declined.";
				}
			} else {
				$transaction_id = $response_parameters["pnref"];
			}
		}

	} elseif ($exit_value !== 0) {
		$error_message = "Cannot execute Verisign module.";
	} else {
		$error_message = "Empty response from payment gateway.";
	}

?>