<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  payoffshore_api.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Payoffshore (www.payoffshore.com) transaction handler by www.viart.com
 */

	$ch = curl_init ();
	curl_setopt ($ch, CURLOPT_URL, $advanced_url);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt ($ch, CURLOPT_HEADER, 0);
	curl_setopt ($ch, CURLOPT_POST, 1);
	curl_setopt ($ch, CURLOPT_POSTFIELDS, $post_params);
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
	set_curl_options ($ch, $payment_parameters);

	$payment_response = curl_exec($ch);
	curl_close($ch);
	$payment_response = trim($payment_response);
	$t->set_var("payment_response", $payment_response);

	if ($payment_response) {

		// convert xml into array
		$xml_parameters = array("authcode", "authorised", "transno", array("error", "message"));
		$response_parameters = array();
		for ($x = 0; $x < sizeof($xml_parameters); $x++) {
			$parameter_name = ""; $regexp_start = ""; $regexp_end = "";
			$xml_parameter = $xml_parameters[$x];
			if (!is_array($xml_parameter)) {
				$xml_parameter = array($xml_parameter);
			}
			for ($xs = 0; $xs < sizeof($xml_parameter); $xs++) {
				if ($xs > 0) {
					$regexp_start .= ".*";
					$regexp_end = ".*" . $regexp_end;
				}
				$parameter_name .= $xml_parameter[$xs];
				$regexp_start .= "<".$xml_parameter[$xs].">";
				$regexp_end = "<\\/".$xml_parameter[$xs].">" . $regexp_end;
			}
			if (preg_match ("/".$regexp_start."(.*)".$regexp_end."/si", $payment_response, $match)) {
				$parameter_value = $match[1];
				$parameter_value = str_replace("<![CDATA[", "", $parameter_value);
				$parameter_value = str_replace("]]>", "", $parameter_value);
				$response_parameters[$parameter_name] = $parameter_value;
			}
		}

		foreach ($response_parameters as $parameter_name => $parameter_value) {
			$t->set_var($parameter_name, $parameter_value);
		}

		// check if transaction approved by payment system
		if (!isset($response_parameters["authorised"])) {
			$error_message = "Can't obtain authorization parameter.";
		} else if (strtolower($response_parameters["authorised"]) != "true") {
			if (isset($response_parameters["errormessage"]) && strlen($response_parameters["errormessage"])) {
				$error_message = $response_parameters["errormessage"];
			} else {
				$error_message = "Your transaction has been declined.";
			}
		} else {
			$transaction_id = $response_parameters["transno"];
		}

	} else {
		$error_message = "Empty response from gateway. Please check your settings.";
	}

?>