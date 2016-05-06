<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  google_charge.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * Google Checkout (https://checkout.google.com/) transaction handler by www.viart.com
 */

	$sql  = " SELECT transaction_id ";
	$sql .= " FROM " . $table_prefix . "orders ";
	$sql .= " WHERE order_id = " . $db->tosql($order_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$transaction_id = $db->f("transaction_id");
	}else{
		$error_message = "Order with ID:".$order_id." doesn't exist.";
		return;
	}

	$headers = array();
	$headers[] = "Authorization: Basic ".base64_encode($payment_parameters['merchant_id'].':'.$payment_parameters['merchant_key']);
	$headers[] = "Content-Type: application/xml;charset=UTF-8";
	$headers[] = "Accept: application/xml;charset=UTF-8";

	$postargs  = '<?xml version="1.0" encoding="UTF-8"?>'; //<?
	$postargs .= '<charge-order xmlns="http://checkout.google.com/schema/2" google-order-number="'.xml_escape_string($transaction_id).'">';
	$postargs .= '<amount currency="'.xml_escape_string($payment_parameters['currency']).'">'.xml_escape_string($variables['order_total']).'</amount>';
	$postargs .= '</charge-order>';
	
	$ch = curl_init();
	if ($ch)
	{
		curl_setopt($ch, CURLOPT_URL, $payment_parameters['request_url']);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postargs);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		$google_response = curl_exec($ch);
		if (curl_errno($ch)) {
			$error_message = curl_errno($ch)." - ".curl_error($ch);
			return;
		}
		curl_close ($ch);

		$google_status_code = array();
		preg_match('/\d\d\d/', $google_response, $google_status_code);

		switch( $google_status_code[0] ) {
			case 200:
				// Success
			break;
			case 503:
				$error_message = "Error 503: Service unavailable.";
			break;
			case 403:
				$error_message = "Error 403: Forbidden.";
			break;
			case 400:
				$error_message = "Error 400: Bad request.";
			break;
			default:
				$error_message = "Error ".$google_status_code[0].":";
		}

	} else {
		$error_message = "Can't initialize cURL.";
	}
?>