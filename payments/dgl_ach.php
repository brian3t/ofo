<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  dgl_ach.php                                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * DGL ACH (2000charge.com) transaction handler by www.viart.com
 */

	$payment_parameters = array();
	$pass_parameters = array();
	$post_parameters = '';
	$pass_data = array();
	$variables = array();
	get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_parameters, $pass_data, $variables);
	if (isset($pass_data['Country']) && isset($pass_data['State'])) {
		$state = 'State='.$pass_data['State'];
		if(($pass_data['Country'] != 'US') && ($pass_data['Country'] != 'CA')){
			$post_params = str_replace($state, 'State=XX', $post_params);
		}
	}

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
		$payment_response = trim($payment_response);
		$t->set_var("payment_response", $payment_response);
	   	if(strlen($payment_response)){
		   	$statys_response = substr($payment_response, 0, 1);
			if(strtoupper($statys_response)=='Y'){
				$transaction_id = $db->tosql($payment_response, TEXT);
			}else{
				$error_message = $db->tosql($payment_response, TEXT);
			}
		}else{
			$error_message = "Can't obtain data for your transaction.";
		}
	} else {
		$error_message = "Can't initialize cURL.";
	}
?>