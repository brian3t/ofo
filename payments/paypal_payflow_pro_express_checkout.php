<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  paypal_payflow_pro_express_checkout.php                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * PayPal Payflow Pro Express Checkout (www.paypal.com) transaction handler by http://www.viart.com/
 */

	$invnum = (isset($payment_parameters['INVNUM']))? $payment_parameters['INVNUM']: "";
	$user_agent = $_SERVER['HTTP_USER_AGENT'];
	$headers[] = "Content-Type: text/namevalue";
	$headers[] = "X-VPS-Timeout: 45";
	$headers[] = "X-VPS-Request-ID:" . $invnum;

	$post_parameters = "";
	$error_message = "";

	if (isset($payment_parameters['USER'])) {
		$post_parameters .= 'USER['.strlen(urlencode($payment_parameters['USER'])).']='.urlencode($payment_parameters['USER']);
	}
	if (isset($payment_parameters['VENDOR'])) {
		$post_parameters .= (strlen($post_parameters)) ? "&" : "";
		$post_parameters .= 'VENDOR[' . strlen(urlencode($payment_parameters['VENDOR'])) . ']=' . urlencode($payment_parameters['VENDOR']);
	}
	if (isset($payment_parameters['PARTNER'])) {
		$post_parameters .= (strlen($post_parameters)) ? "&" : "";
		$post_parameters .= 'PARTNER[' . strlen(urlencode($payment_parameters['PARTNER'])) . ']=' . urlencode($payment_parameters['PARTNER']);
	}
	if (isset($payment_parameters['PWD'])) {
		$post_parameters .= (strlen($post_parameters))? "&": "";
		$post_parameters .= 'PWD[' . strlen(urlencode($payment_parameters['PWD'])) . ']=' . urlencode($payment_parameters['PWD']);
	}
	if (isset($payment_parameters['TENDER'])) {
		$post_parameters .= (strlen($post_parameters))? "&": "";
		$post_parameters .= 'TENDER[' . strlen(urlencode($payment_parameters['TENDER'])) . ']=' . urlencode($payment_parameters['TENDER']);
	}
	if (isset($payment_parameters['TRXTYPE'])) {
		$post_parameters .= (strlen($post_parameters))? "&": "";
		$post_parameters .= 'TRXTYPE[' . strlen(urlencode($payment_parameters['TRXTYPE'])) . ']=' . urlencode($payment_parameters['TRXTYPE']);
	}
	$post_parameters .= (strlen($post_parameters)) ? "&" : "";
	$post_parameters .= 'ACTION[1]=D';
	$post_parameters .= (strlen($post_parameters)) ? "&" : "";
	$post_parameters .= 'TOKEN[' . strlen(urlencode(get_param('token'))) . ']=' . urlencode(get_param('token'));
	$post_parameters .= (strlen($post_parameters)) ? "&" : "";
	$post_parameters .= 'PAYERID[' . strlen(urlencode(get_param('PayerID'))) . ']=' . urlencode(get_param('PayerID'));
	$post_parameters .= (strlen($post_parameters)) ? "&" : "";
	$post_parameters .= 'IPADDRESS[' . strlen(urlencode(get_ip())) . ']=' . urlencode(get_ip());
	if (isset($payment_parameters['AMT'])) {
		$post_parameters .= (strlen($post_parameters)) ? "&" : "";
		$post_parameters .= 'AMT[' . strlen(urlencode($payment_parameters['AMT'])) . ']=' . urlencode($payment_parameters['AMT']);
	}
	if (isset($payment_parameters['CURRENCY'])) {
		$post_parameters .= (strlen($post_parameters)) ? "&" : "";
		$post_parameters .= 'CURRENCY[' . strlen(urlencode($payment_parameters['CURRENCY'])) . ']=' . urlencode($payment_parameters['CURRENCY']);
	}
	if (isset($payment_parameters['INVNUM'])) {
		$post_parameters .= (strlen($post_parameters)) ? "&" : "";
		$post_parameters .= 'INVNUM[' . strlen(urlencode($payment_parameters['INVNUM'])) . ']=' . urlencode($payment_parameters['INVNUM']);
	}
	if (isset($payment_parameters['ORDERDESC'])) {
		$post_parameters .= (strlen($post_parameters)) ? "&" : "";
		$post_parameters .= 'ORDERDESC[' . strlen(urlencode($payment_parameters['ORDERDESC'])) . ']=' . urlencode($payment_parameters['ORDERDESC']);
	}

	$ch = curl_init();
	if ($ch) {
		curl_setopt($ch, CURLOPT_URL, $payment_parameters['Advanced_URL']);
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
		set_curl_options($ch, $payment_parameters);

		$payment_response = curl_exec($ch);
		if (curl_errno($ch)) {
			$error_message .= curl_errno($ch) . " - " . curl_error($ch) . "<br>\n";
		} elseif (strlen($payment_response)){
			$resp = explode("\n\r\n", $payment_response);
			$header = explode("\n", $resp[0]);
			parse_str($resp[1], $payment_response);
			$transaction_id = (isset($payment_response['PNREF']))? $payment_response['PNREF']: "";
			if ($payment_response['RESULT'] == "0") {
				$transaction_id .= (strlen($transaction_id)) ? "" : "Is approved.";
				if (isset($payment_response['AVSADDR'])) {
					$error_message .= ($payment_response['AVSADDR'] != "Y") ? "Your street information does not match. Please re-enter." . "<br>\n" : "";
				}
				if (isset($payment_response['AVSZIP'])) {
					$error_message .= ($payment_response['AVSZIP'] != "Y") ? "Your zip information does not match. Please re-enter." . "<br>\n" : "";
				}
				if (isset($payment_response['CVV2MATCH'])) {
					$error_message .= ($payment_response['CVV2MATCH'] != "Y") ? "Your cvv2 information does not match. Please re-enter." . "<br>\n" : "";
				}
			} else {
				if (isset($payment_response['RESPMSG']) && strlen($payment_response['RESPMSG'])) {
					$error_message .= $payment_response['RESPMSG'] . "<br>\n";
				} else {
					$error_message .= "Your transaction was declined!" . "<br>\n";
				}
				if (strlen($payment_response['RESULT'])) {
					$error_message .= " Result code:" . $payment_response['RESULT'] . "<br>\n";
				}
			}
		} else {
			$error_message .= "Can't obtain data for your transaction." . "<br>\n";
		}
		curl_close($ch);
	} else {
		$error_message .= "Can't initialize cURL." . "<br>\n";
	}

?>