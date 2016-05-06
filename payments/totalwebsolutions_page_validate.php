<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  totalwebsolutions_page_validate.php                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Total Web Solutions (www.totalwebsolutions.com) transaction handler by www.viart.com
 */

	$status = get_param("status");
	$post_params = "CustomerID=".$payment_parameters["CustomerID"]."&Notes=".$payment_parameters["Notes"];
	$ch = curl_init();
	if ($ch){
		$confirm_url = isset($payment_parameters["confirm_url"]) ? $payment_parameters["confirm_url"]: "https://secure.totalwebsecure.com/paypage/confirm.asp";
		curl_setopt ($ch, CURLOPT_URL, $confirm_url);
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

		if(preg_match_all("/<strong>(.*)\<\/strong>/Uis", $payment_response, $response_matches, PREG_SET_ORDER)){
			$success_response = false;
			$fail_response = false;
			$response_string = "";
			if(strtoupper($response_matches[0][1]) == 'FAIL'){
				$fail_response = true;
			}
			if(strtoupper($response_matches[0][1]) == 'SUCCESS'){
				$success_response = true;
				if(isset($response_matches[1][1])){
					$transaction_id = $response_matches[1][1];
				}
			}
			if($success_response && !$fail_response){
				$transaction_id = (strlen($transaction_id))? $transaction_id: va_date(array("YY", "-", "MM", "-", "DD", " ", "HH", ":", "mm", ":", "ss"), $variables["order_placed_timestamp"]);
			}elseif(!$success_response && $fail_response){
				$error_message = "Payment response: 'FAIL'. Your transaction has been declined.";
			}else{
				$pending_message = "This order will be reviewed manually.";
			}
		}else{
			$pending_message = "This order will be reviewed manually.";
		}
	}else{
		$error_message = "Can't initialize cURL.";
	}
	
?>