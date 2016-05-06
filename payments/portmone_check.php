<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  portmone_check.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
 
/*
 * portmone (www.portmone.com.ua) transaction handler by ViArt Ltd. (www.viart.com)
 */

	$pending_message = "";
	if(!isset($payment_parameters['payee_id']) || !strlen($payment_parameters['payee_id'])){
		$pending_message .= (strlen($pending_message))? ", ": "";
		$pending_message .= "'payee_id'";
	}
	if(!isset($payment_parameters['shopordernumber']) || !strlen($payment_parameters['shopordernumber'])){
		$pending_message .= (strlen($pending_message))? ", ": "";
		$pending_message .= "'shopordernumber'";
	}
	if(!isset($payment_parameters['log']) || !strlen($payment_parameters['log'])){
		$pending_message .= (strlen($pending_message))? ", ": "";
		$pending_message .= "'log'";
	}
	if(!isset($payment_parameters['pass']) || !strlen($payment_parameters['pass'])){
		$pending_message .= (strlen($pending_message))? ", ": "";
		$pending_message .= "'pass'";
	}
	if(strlen($pending_message)){
		$pending_message = "Can't obtain payment parameter(s) ".$pending_message.". This order will be reviewed manually.";
		return;
	}
	$params = 'PAYEE_ID='.$payment_parameters['payee_id'].'&LOG='.$payment_parameters['log'].'&PASS='.$payment_parameters['pass'].'&STATUS=PAYED&SHOPORDERNUMBER='.$payment_parameters['shopordernumber'];

	$ch = curl_init();
	if ($ch)
	{
		curl_setopt ($ch, CURLOPT_URL, $payment_parameters['check_url']);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$payment_response = curl_exec ($ch);
		if (curl_errno($ch)){
			$error_message = curl_errno($ch)." - ".curl_error($ch);
			return;
		}
		curl_close($ch);
		$matches = array();
		if (preg_match('/\<order\>(.*)\<\/order\>/iU', $payment_response, $matches)){
			$order_response = $matches[1];
			$matches = array();
			if (preg_match('/\<status\>(.*)\<\/status\>/iU', $order_response, $matches)){
				$status = $matches[1];
				if(strtoupper($status) == 'CREATED'){
					$pending_message = "Order status is 'CREATED'. This order will be reviewed manually.";
				}elseif(strtoupper($status) == 'PAYED'){
					$matches = array();
					if (preg_match('/\<pay_order_number\>(.*)\<\/pay_order_number\>/iU', $order_response, $matches)){
						$transaction_id = $matches[1];
					}else{
						$transaction_id = $payment_parameters['shopordernumber'];
					}
				}else{
					$pending_message = "Order status is '".$status."'.";
				}
			}else{
				$error_message  = "Can't obtain transaction information from portmone.";
			}
		}else{
			$error_message .= "Can't obtain data for your transaction.";
		}
	} else {
		$error_message = "Can't initialize cURL.";
	}

?>