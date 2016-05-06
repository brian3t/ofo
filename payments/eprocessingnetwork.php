<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  eprocessingnetwork.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * eProcessingNetwork (www.eprocessingnetwork.com) transaction handler by www.viart.com
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
		curl_setopt ($ch, CURLOPT_TIMEOUT, 60);
		set_curl_options ($ch, $payment_parameters);

		$payment_response = curl_exec($ch);
		if (curl_errno($ch)){
			$error_message = curl_errno($ch)." - ".curl_error($ch);
			return;
		}
		curl_close($ch);
		$payment_response = trim($payment_response);
		$t->set_var("payment_response", $payment_response);

		if (strlen($payment_response)){
			$auth=substr($payment_response,1,1);
			$response_parts = explode(",", $payment_response);
			$transaction_id = $response_parts[0];
			if($auth!="Y"){
				if($auth=="N"){
					$error_message = "Your transaction has been declined.";
				}elseif($auth=="U"){
					$error_message = "Your transaction is unable.";
				}else{
					if(strlen($auth)){
						$error_message = "Unknown transaction status, '".$auth."'.";
					}else{
						$error_message = "Can't obtain status for your transaction.";
					}
				}
			}
		} else {
			$error_message = "Empty response from gateway. Please check your settings.";
		}
	} else {
		$error_message = "Can't initialize cURL.";
	}

?>