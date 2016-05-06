<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  protx_direct_3d_check.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Protx VSP (www.protx.com) transaction handler by www.viart.com
 */

 	if(!strlen($variables["secure_3d_check"])){
 		$update_order_data = false;
 		$update_order_status = false;
 		return;
	}
	$error_message = '';
	$MD    = get_param("MD");
	$PaRes = get_param("PaRes");
	$PaRes = str_replace(" ", "+", $PaRes);
	
	if(!strlen($MD) || !strlen($PaRes)){
		$error_message = "Empty response from ProTX.";
	}
	if(isset($payment_parameters['CallbackURL']) && strlen($payment_parameters['CallbackURL'])){
		$CallbackURL = $payment_parameters['CallbackURL'];
	}else{
		$error_message = "Empty parameter 'CallbackURL', please check your payment settings.";
	}
	
	if(!strlen($error_message)){
		$payment_response = '';
		$ch = curl_init();
		if ($ch){
			$post_params = "MD=" . $MD."&PaRes=" . $PaRes;
			curl_setopt ($ch, CURLOPT_URL, $CallbackURL);
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
				$error_message = curl_errno($ch) . " " . curl_error($ch);
				return;
			}
			curl_close($ch);
			$payment_response = trim(strip_tags($payment_response));
		}else{
			$error_message = "Can't initialize cURL.";
		}
		if (!strlen($payment_response)) {
			$error_message = "Empty response from ProTX, please check your payment settings.";
		}else{
			$response_parts = explode(chr(10), $payment_response);
			if (sizeof($response_parts) == 1) {
				$error_message = "Bad response from gateway: " . $payment_response;
			} else {
				$response_parameters = array();
				for($i = 0; $i < sizeof($response_parts); $i++) {
					$response_part = explode('=', $response_parts[$i], 2);
					$response_parameters[trim($response_part[0])] = urldecode(trim($response_part[1]));
					$response_parameters[strtoupper(trim($response_part[0]))] = urldecode(trim($response_part[1]));
				}
				$variables["authorization_code"] = isset($response_parameters["TxAuthNo"]) ? $response_parameters["TxAuthNo"] : "";
				$variables["avs_message"] = isset($response_parameters["AVSCV2"]) ? $response_parameters["AVSCV2"] : "";
				$variables["avs_address_match"] = isset($response_parameters["AddressResult"]) ? $response_parameters["AddressResult"] : "";
				$variables["avs_zip_match"] = isset($response_parameters["PostCodeResult"]) ? $response_parameters["PostCodeResult"] : "";
				$variables["cvv2_match"] = isset($response_parameters["CV2Result"]) ? $response_parameters["CV2Result"] : "";
				$variables["secure_3d_status"] = isset($response_parameters["3DSecureStatus"]) ? $response_parameters["3DSecureStatus"] : "";
				$variables["secure_3d_cavv"] = isset($response_parameters["CAVV"]) ? $response_parameters["CAVV"] : "";

				$Status = isset($response_parameters["Status"]) ? $response_parameters["Status"] : "";
				$StatusDetail = isset($response_parameters["StatusDetail"]) ? $response_parameters["StatusDetail"] : "";
				$VPSTxId = isset($response_parameters["VPSTxId"]) ? $response_parameters["VPSTxId"] : "";
				$SecurityKey = isset($response_parameters["SecurityKey"]) ? $response_parameters["SecurityKey"] : "";

				$transaction_id = (strlen($VPSTxId)) ? "VPSTxId=".$VPSTxId : "";
				$transaction_id .= (strlen($VPSTxId)) ? " SecurityKey=".$SecurityKey : "";

				if (!(($Status == 'OK') && (strlen($VPSTxId)))) {
					$error_message =(strlen($StatusDetail)) ? $StatusDetail : 'Transaction could not be authorised';
				}

			}
		}
	}
?>