<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  paypal_payflow_pro_direct.php                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * PayPal Payflow Pro Direct (www.paypal.com) transaction handler by http://www.viart.com/
 */

	$invnum = (isset($payment_parameters['INVNUM']))? $payment_parameters['INVNUM']: "";
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$headers[] = "Content-Type: text/namevalue";
	$headers[] = "X-VPS-Timeout: 45";
	$headers[] = "X-VPS-Request-ID:" . $invnum;

	$post_parameters = "";
	$error_message = "";

	if(isset($payment_parameters['USER'])){
		$post_parameters .= 'USER['.strlen(urlencode($payment_parameters['USER'])).']='.urlencode($payment_parameters['USER']);
	}
	if(isset($payment_parameters['VENDOR'])){
		$post_parameters .= (strlen($post_parameters))? "&": "";
		$post_parameters .= 'VENDOR['.strlen(urlencode($payment_parameters['VENDOR'])).']='.urlencode($payment_parameters['VENDOR']);
	}
	if(isset($payment_parameters['PARTNER'])){
		$post_parameters .= (strlen($post_parameters))? "&": "";
		$post_parameters .= 'PARTNER['.strlen(urlencode($payment_parameters['PARTNER'])).']='.urlencode($payment_parameters['PARTNER']);
	}
	if(isset($payment_parameters['PWD'])){
		$post_parameters .= (strlen($post_parameters))? "&": "";
		$post_parameters .= 'PWD['.strlen(urlencode($payment_parameters['PWD'])).']='.urlencode($payment_parameters['PWD']);
	}
	$last_key = "";
	foreach ($pass_data as $key => $value) {
		if (strtoupper($key) != strtoupper($last_key)){
			$post_parameters .= (strlen($post_parameters))? "&": "";
			$post_parameters .= $key.'['.strlen($value).']='.urlencode($value);
			$last_key = $key;
		}
	}

	$ch = curl_init();
	if ($ch){
		curl_setopt($ch, CURLOPT_URL, $advanced_url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 90);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_parameters);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, TRUE);
		curl_setopt($ch, CURLOPT_POST, 1);
		set_curl_options ($ch, $payment_parameters);
	
		$payment_response = curl_exec ($ch);
		if (curl_errno($ch)){
			$error_message .= curl_errno($ch)." - ".curl_error($ch);
		}
		curl_close($ch);
		if(strlen($error_message)){
			$error_message .= "";
		}elseif(strlen($payment_response)){
			$resp = explode("\n\r\n", $payment_response);
			$header = explode("\n", $resp[0]);
			parse_str($resp[1], $payment_response);
			$transaction_id = (isset($payment_response['PNREF']))? $payment_response['PNREF']: "";
			if($payment_response['RESULT'] == 0){
				$transaction_id .= (strlen($transaction_id))? "": "Is approved.";
				if(isset($payment_response['AVSADDR'])){
					$error_message .= ($payment_response['AVSADDR'] != "Y")? "Your street information does not match. Please re-enter.": "";
				}
				if(isset($payment_response['AVSZIP'])){
					$error_message .= ($payment_response['AVSZIP'] != "Y")? "Your zip information does not match. Please re-enter.": "";
				}
				if(isset($payment_response['CVV2MATCH'])){
					$error_message .= ($payment_response['CVV2MATCH'] != "Y")? "Your cvv2 information does not match. Please re-enter.": "";
				}
			}else{
				$error_message .= (strlen($payment_response['RESULT']))? "Result code:".$payment_response['RESULT']." ": "";
				if(isset($payment_response['RESPMSG'])){
					$error_message .= (strlen($payment_response['RESPMSG']))? $payment_response['RESPMSG']: "Your transaction was declined!";
				}else{
					$error_message .= "Your transaction was declined!";
				}
			}
		}else{
			$error_message .= "Can't obtain data for your transaction.";
		}
	}else{
		$error_message .= "Can't initialize cURL.";
	}

?>