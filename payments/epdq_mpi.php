<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  epdq_mpi.php                                             ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * ePDQ MPI (www.tele-pro.co.uk/epdq/) transaction handler by http://www.viart.com/
 */

	global $is_admin_path, $is_sub_folder;
	$root_folder_path = ((isset($is_admin_path) && $is_admin_path) || (isset($is_sub_folder) && $is_sub_folder)) ? "../" : "./";

	include_once($root_folder_path . "payments/epdq_mpi_functions.php");

	// get some variables from our payment settings
	$sslcert        = isset($payment_parameters["sslcert"]) ? $payment_parameters["sslcert"] : "";

	$pass_data = array();
	foreach ($payment_parameters as $parameter_name => $parameter_value) {
		if(isset($pass_parameters[$parameter_name]) && $pass_parameters[$parameter_name] == 1) {
			$pass_data[$parameter_name] = $parameter_value;
		}
	}

	$secure_3d = false;
	if (isset($payment_parameters["3dauthentication"])) {
		$secure_3d = preg_match("/yes|true|1/i", $payment_parameters["3dauthentication"]);
	}

	if ($secure_3d && $order_step == "confirmation") {
		// check credit card data
		$cc_number = ""; $cc_type_code = "";
		if (isset($pass_data["ccnumber"]) && $pass_data["ccnumber"]) {
			$cc_number = $pass_data["ccnumber"];
		}
		if (isset($pass_data["cctype"]) && $pass_data["cctype"]) {
			$cc_type_code = strtolower($pass_data["cctype"]);
			$cc_type_codes = array ("visa" => "visa", "mc" => "mc", "mastercard" => "mc", 
				"solo" => "mc", "switch" => "mc", "maestro" => "mc", "electron" => "visa"
			);
			if (isset($cc_type_codes[$cc_type_code])) {
				$cc_type_code = $cc_type_codes[$cc_type_code];
			}
		}

		if (!$cc_type_code) {
			$error_message = "Can't obtain credit card type.";
			return;
		} else if ($cc_type_code != "visa" && $cc_type_code != "mc") {
			$variables["secure_3d_check"] = 0;
			return;
		}

		// send epdq_3d_check request to check if cardholder enrolled in 3D Secure
		$update_order_data = false;
		$exec_string = "java";
		if (isset($payment_parameters["javaclasspath"]) && $payment_parameters["javaclasspath"]) {
			$exec_string .= " -classpath \"" . $payment_parameters["javaclasspath"] . "\"";
		}

		$exec_string .= " epdq_3d_check";
		$data_params = array("PurchaseAmount", "CardExpiryDate", "PurchaseCurrency", "PurchaseCurrencyExponent", 
			"PurchaseDate", "MerchantName", "MerchantID", "MerchantCountryCode", "MerchantUrl", "PurchaseRecurringFrequency",
			"PurchaseRecurringExpiry", "PurchaseInstallment", "PurchaseDescription", "HTTPAccept", "HTTPUserAgent", 
			"DeviceCategory", "AcquirerBIN", "DSLoginID", "DSPassword", "CardNumber");
		$data_string = "";
		for ($d = 0; $d < sizeof($data_params); $d++) {
			$param_name = $data_params[$d];
			$param_lower = strtolower($param_name);
			$param_value = "";
			if (isset($pass_data[$param_lower]) && strlen($pass_data[$param_lower])) {
				$param_value = $pass_data[$param_lower];
			} else if ($param_name == "PurchaseAmount") {
				if (isset($pass_data["ordertotal"]) && $pass_data["ordertotal"]) {
					$param_value = $pass_data["ordertotal"] / 100;
				}
			} else if ($param_name == "CardExpiryDate") {
				if (isset($pass_data["ccexpires"]) && strlen($pass_data["ccexpires"]) == 5) {
					$param_value = substr($pass_data["ccexpires"], 3, 2) . substr($pass_data["ccexpires"], 0, 2);
				}
			} else if ($param_name == "PurchaseCurrency") {
				if (isset($pass_data["currency"]) && $pass_data["currency"]) {
					$param_value = $pass_data["currency"];
				} else {
					$param_value = "840"; // USD
				}
			} else if ($param_name == "PurchaseCurrencyExponent") {
				$param_value = "2";
			} else if ($param_name == "PurchaseDate") {
				$param_value = va_date(array("YYYY","MM","DD"," ","HH",":","mm",":","ss"));
			} else if ($param_name == "MerchantName") {
				$param_value = $settings["site_url"];
			} else if ($param_name == "MerchantID") {
				if ($cc_type_code == "visa") {
					if (isset($pass_data["visamerchantid"]) && $pass_data["visamerchantid"]) {
						$param_value = $pass_data["visamerchantid"];
					} else if (isset($pass_data["visadsloginid"]) && $pass_data["visadsloginid"]) {
						$param_value = $pass_data["visadsloginid"];
					}
				} else if ($cc_type_code == "mc") {
					if (isset($pass_data["mcmerchantid"]) && $pass_data["mcmerchantid"]) {
						$param_value = $pass_data["mcmerchantid"];
					} else if (isset($pass_data["mcdsloginid"]) && $pass_data["mcdsloginid"]) {
						$param_value = $pass_data["mcdsloginid"];
					}
				}
			} else if ($param_name == "MerchantCountryCode") {
				$param_value = "840"; // US
			} else if ($param_name == "MerchantUrl") {
				$param_value = $settings["site_url"];
			} else if ($param_name == "HTTPAccept") {
				$param_value = get_var("HTTP_ACCEPT");
			} else if ($param_name == "HTTPUserAgent") {
				$param_value = get_var("HTTP_USER_AGENT");
			} else if ($param_name == "DeviceCategory") {
				$param_value = "0";
			} else if ($param_name == "AcquirerBIN") {
				if ($cc_type_code == "visa") {
					if (isset($pass_data["visaacquirerbin"]) && $pass_data["visaacquirerbin"]) {
						$param_value = $pass_data["visaacquirerbin"];
					}
				} else if ($cc_type_code == "mc") {
					if (isset($pass_data["mcacquirerbin"]) && $pass_data["mcacquirerbin"]) {
						$param_value = $pass_data["mcacquirerbin"];
					}
				}
			} else if ($param_name == "DSLoginID") {
				if ($cc_type_code == "visa") {
					if (isset($pass_data["visadsloginid"]) && $pass_data["visadsloginid"]) {
						$param_value = $pass_data["visadsloginid"];
					}
				} else if ($cc_type_code == "mc") {
					if (isset($pass_data["mcdsloginid"]) && $pass_data["mcdsloginid"]) {
						$param_value = $pass_data["mcdsloginid"];
					}
				}
			} else if ($param_name == "DSPassword") {
				if ($cc_type_code == "visa") {
					if (isset($pass_data["visadspassword"]) && $pass_data["visadspassword"]) {
						$param_value = $pass_data["visadspassword"];
					}
				} else if ($cc_type_code == "mc") {
					if (isset($pass_data["mcdspassword"]) && $pass_data["mcdspassword"]) {
						$param_value = $pass_data["mcdspassword"];
					}
				}
			} else if ($param_name == "CardNumber") {
				$param_value = $cc_number;
			} else if ($param_name == "PurchaseRecurringFrequency") {
				$param_value = "";
			} else if ($param_name == "PurchaseRecurringExpiry") {
				$param_value = "";
			} else if ($param_name == "PurchaseInstallment") {
				$param_value = "";
			} else if ($param_name == "PurchaseDescription") {
				$param_value = "";
			}

			if (strlen($param_value)) {
				if ($data_string) { $data_string .= "&"; }
				$data_string .= $param_name . "=" . urlencode($param_value);
			}
		}
		if ($data_string) {
			$exec_string .= " -d \"" . $data_string . "\"";
		}
		$exec_string .= " -c \"" . CHARSET . "\"";

		if (isset($payment_parameters["3dclassesdir"]) && $payment_parameters["3dclassesdir"]) {
			chdir($payment_parameters["3dclassesdir"]);
		} else {
			chdir("./payments");
		}
 	  $some = exec ($exec_string, $output, $return_value);
		if (isset($payment_parameters["webdir"]) && $payment_parameters["webdir"]) {
			chdir($payment_parameters["webdir"]);
		} else {
			chdir("../");
		}
		$response = join("", $output);
		if ($response) {
			//$error_message = $response;
			$response_parameters = array();
			$response_parts = explode("&", $response);
			for ($i = 0; $i < sizeof($response_parts); $i++) {
				$response_part = explode('=', $response_parts[$i]);
				$response_parameters[$response_part[0]] = urldecode($response_part[1]);
				$response_parameters[strtolower($response_part[0])] = urldecode($response_part[1]);
			}
			if (isset($response_parameters["AuthRequired"]) && strlen($response_parameters["AuthRequired"])) {
				$variables["secure_3d_check"] = $response_parameters["AuthRequired"];
				if ($variables["secure_3d_check"]) {
					if (isset($response_parameters["ACSUrl"]) && $response_parameters["ACSUrl"]) {
						$variables["secure_3d_acsurl"] = $response_parameters["ACSUrl"];
					} else {
						$error_message = "Can't obtain authentication parameter 'ACSUrl'.";
					}
					if (isset($response_parameters["PAReq"]) && $response_parameters["PAReq"]) {
						$variables["secure_3d_pareq"] = $response_parameters["PAReq"];
					} else {
						$error_message = "Can't obtain authentication parameter 'PAReq'.";
					}
					if (isset($response_parameters["XID"]) && $response_parameters["XID"]) {
						$variables["secure_3d_xid"] = $response_parameters["XID"];
					}
				}
			} else {
				if (isset($response_parameters["Error"])) {
					$error_message = $response_parameters["Error"];
				} else {
					$error_message = "Can't obtain authentication parameter 'AuthRequired'.";
				}
			}
		} else if ($return_value) {
			$error_message = "Please check if Merchant Java SDK installed correctly ($return_value).";
		} else {
			$error_message = "Empty response from Merchant Java SDK.";
		}

	} else {

		// don't need to check the order
		if ($order_step == "final" && !$secure_3d) {
			$update_order_status = false; $update_order_data = false;
			return;
		}

		if ($secure_3d && $order_step == "final") {
			// 3d checks
			if ($variables["secure_3d_check"]) {
				// need to verify message
				$pares = get_param("PaRes");

				if ($pares) {
					$data_string = "PaRes=" . urlencode($pares);
					if (isset($pass_data["cardnumber"]) && strlen($pass_data["cardnumber"])) {
						$cc_number = $pass_data["cardnumber"];
					} else {
						$cc_number = get_setting_value($pass_data, "ccnumber", "");
					}
					$data_string .= "&CardNumber=" . urlencode($cc_number);

					$exec_string = "java";
					if (isset($payment_parameters["javaclasspath"]) && $payment_parameters["javaclasspath"]) {
						$exec_string .= " -classpath \"" . $payment_parameters["javaclasspath"] . "\"";
					}
					$exec_string .= " epdq_3d_verify";
					$exec_string .= " -d \"" . $data_string . "\"";
					$exec_string .= " -c \"" . CHARSET . "\"";
	      
					if (isset($payment_parameters["3dclassesdir"]) && $payment_parameters["3dclassesdir"]) {
						chdir($payment_parameters["3dclassesdir"]);
					} else {
						chdir("./payments");
					}
 	        $some = exec ($exec_string, $output, $return_value);
					if (isset($payment_parameters["webdir"]) && $payment_parameters["webdir"]) {
						chdir($payment_parameters["webdir"]);
					} else {
						chdir("../");
					}
					$response = join("", $output);
					if ($response) {
						$response_parameters = array();
						$response_parts = explode("&", $response);
						for ($i = 0; $i < sizeof($response_parts); $i++) {
							$response_part = explode('=', $response_parts[$i]);
							$response_parameters[$response_part[0]] = urldecode($response_part[1]);
							$response_parameters[strtolower($response_part[0])] = urldecode($response_part[1]);
						}

						// "Y" corresponds to 0 - the cardholder completed authentication correctly.
						// "A" corresponds to 1 - the attempted authentication was recorded.
						// "U" corresponds to 6 - a system error prevented authentication from
						// "N" corresponds to 9 - the cardholder did not complete authentication and the card should not be accepted for payment.
						// "" corresponds to -1 - the digital certificate was invalid
	
						if (isset($response_parameters["Error"]) && $response_parameters["Error"]) {
							$error_message = $response_parameters["Error"];
						} else {
							$signatureCheckResult = get_setting_value($response_parameters, "signatureCheckResult", "");
							if (!strlen($signatureCheckResult)) {
								$error_message = "Can't find digital signature.";
							} else if ($signatureCheckResult) {
								$error_message = "Digital signature is not valid (" . $signatureCheckResult . ")";
							} else {
								$authenticationResult = get_setting_value($response_parameters, "authenticationResult", "");
								$authenticationStatusMsg = get_setting_value($response_parameters, "authenticationStatusMsg", "");
								$purchaseAmount = get_setting_value($response_parameters, "purchaseAmount", "");
								$variables["secure_3d_xid"] = get_setting_value($response_parameters, "XID", "");
								$variables["secure_3d_eci"] = get_setting_value($response_parameters, "ECI", "");
								$variables["secure_3d_cavv"] = get_setting_value($response_parameters, "ACSVerificationID", "");

								if (strlen($authenticationStatusMsg)) {
									$variables["secure_3d_status"] = $authenticationStatusMsg;
								} else if (strlen($authenticationResult)) {
									$variables["secure_3d_status"] = $authenticationResult;
								}
								if (!strlen($authenticationResult)) {
									$error_message = "Can't obtain authentication parameter 'authenticationResult'.";
								} else if ($authenticationResult == -1) {
									$error_message = "Issuer certificate is incorrect.";
								} else if ($authenticationResult == 6) {
									$error_message = "Authentication test failed.";
								} else if ($authenticationResult == 9) {
									$error_message = "System error prevented completion of authentication.";
								} else if ($authenticationResult == 1 || $authenticationResult == 0) {
									// prepare data for transaction
									if ($authenticationResult == 1) {
										// Attempts Authentication
										$pass_data["PayerSecurityLevel"] = 6;
										$pass_data["payersecuritylevel"] = 6;
									} else {
										// Authentication Successful
										$pass_data["PayerSecurityLevel"] = 2;
										$pass_data["payersecuritylevel"] = 2;
									}
									$pass_data["CardholderPresentCode"] = 13;
									$pass_data["cardholderpresentcode"] = 13;
									$pass_data["PayerAuthenticationCode"] = $variables["secure_3d_cavv"];
									$pass_data["payerauthenticationcode"] = $variables["secure_3d_cavv"];
									$pass_data["PayerTxnId"] = $variables["secure_3d_xid"];
									$pass_data["payertxnid"] = $variables["secure_3d_xid"];

								} else {
									$error_message = "Unknown authentication status (" . $authenticationResult . ")";
								}
							}
						}

					} else if ($return_value) {
						$error_message = "Please check if Merchant Java SDK installed correctly ($return_value).";
					} else {
						$error_message = "Empty response from Merchant Java SDK.";
					}

				} else {
					$error_message = "Can't obtain authentication parameter 'PaRes'.";
				}
			} else if (!strlen($variables["secure_3d_check"])) {
				$error_message = "Can't obtain 3D authentication check result.";
			} else {
				// Cardholder not enrolled
				$pass_data["PayerSecurityLevel"] = 1;
				$pass_data["payersecuritylevel"] = 1;
			}
		}

		if (!$error_message) {

			// check different account for difference currencies
			$currency_value = get_setting_value($pass_data, "currency", "");
			$currency_clientid = get_setting_value($pass_data, "clientid" . $currency_value, "");
			$currency_password = get_setting_value($pass_data, "password" . $currency_value, "");
			if (strlen($currency_clientid)) {
				$pass_data["clientid"] = $currency_clientid;
			}
			if (strlen($currency_password)) {
				$pass_data["password"] = $currency_password;
			}

			$advanced_url = get_setting_value($variables, "advanced_url", "https://secure2.epdq.co.uk:11500/");
			$xml = epdq_build_xml($pass_data);
    
			$ch = curl_init();
			curl_setopt ($ch, CURLOPT_URL, $advanced_url);
			curl_setopt ($ch, CURLOPT_POST, 1);
			curl_setopt ($ch, CURLOPT_POSTFIELDS, "CLRCMRC_XML=" . $xml);
			curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
			if ($sslcert) {
				curl_setopt ($ch, CURLOPT_SSLCERT, $sslcert);
			}
			curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
			set_curl_options ($ch, $payment_parameters);
    
			$epdq_response = curl_exec ($ch);
			curl_close ($ch);
    
			if (strlen($epdq_response)) {
				$CcErrCode = ""; $CcReturnMsg = "";
				$ProcReturnCode = ""; $ProcReturnMsg = "";
				$transaction_id = "";
				if (preg_match("/<CcErrCode[^>]*>(.*)<\/CcErrCode>/i", $epdq_response, $match)) {
					$CcErrCode = $match[1];
				}
				if (preg_match("/<CcReturnMsg[^>]*>(.*)<\/CcReturnMsg>/i", $epdq_response, $match)) {
					$CcReturnMsg = $match[1];
				}
				if (preg_match("/<ProcReturnCode[^>]*>(.*)<\/ProcReturnCode>/i", $epdq_response, $match)) {
					$ProcReturnCode = $match[1];
				}
				if (preg_match("/<ProcReturnMsg[^>]*>(.*)<\/ProcReturnMsg>/i", $epdq_response, $match)) {
					$ProcReturnMsg = $match[1];
				}
				if (preg_match("/<Transaction[^>]*>.*<Id[^>]*>(.*)<\/Id>.*<\/Transaction>/is", $epdq_response, $match)) {
					$transaction_id = $match[1];
				}
    
				if (!strlen($ProcReturnCode) && !strlen($CcErrCode)) {
					if (preg_match("/<MessageList>(.*)<\/MessageList>/is", $epdq_response, $match)) {
						$messages = trim($match[1]);
						if (preg_match_all("/<Message>.*<Text[^>]*>(.*)<\/Text>.*<\/Message>/isU", $messages, $matches)) {
							for($p = 0; $p < sizeof($matches[1]); $p++) {
								$message = trim($matches[1][$p]);
								if ($message) {
									$error_message .= $message . "<br>";
								}
							}
						}
					}
					if (!$error_message) {
						$error_message = "Can't obtain transaction status.";
					}
				} else if (!strlen($ProcReturnCode)) {
					$error_message = "Can't obtain transaction status.";
				} else if ($ProcReturnCode != "00") {
					$error_message  = "Some errors occurred during handling your transaction:<br>";
					$error_message .= $ProcReturnCode . ": " . $ProcReturnMsg;
				} else if (!strlen($CcErrCode)) {
					$error_message = "Can't obtain engine status.";
				} else if ($CcErrCode != 1) {
					$error_message  = "Some engine errors occurred during handling your transaction:<br>";
					$error_message .= $CcErrCode . ": " . $CcReturnMsg;
				} else if (!$transaction_id) {
					$error_message  = "Can't obtain transaction parameter.";
				} else {
					// transaction was approved
					$avs_response_code = ""; $cvv2_response = ""; $avs_message = "";
					if (preg_match("/<AvsDisplay [^>]*>(.*)<\/AvsDisplay>/i", $epdq_response, $match)) {
						$variables["avs_response_code"] = $match[1];
					}
					if (preg_match("/<Cvv2Resp[^>]*>(.*)<\/Cvv2Resp>/i", $epdq_response, $match)) {
						$variables["cvv2_response"] = $match[1];
					}
					if (preg_match("/<ProcAvsRespCode[^>]*>(.*)<\/ProcAvsRespCode>/i", $epdq_response, $match)) {
						$variables["avs_message"] = $match[1];
					}
				}
			} else {
				$error_message  = "Empty response from engine, please check your payment settings.";
			}
		}
	}

	if ($error_message) {
		$error_message = str_replace("&apos;", "'", $error_message);
	}

?>