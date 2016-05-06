<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  multisafepay_check.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * MultiSafePay (http://www.multisafepay.nl/) transaction handler by www.viart.com
 */

	$account = isset($payment_parameters['account'])?xml_escape_string($payment_parameters['account']):'';
	$site_id = isset($payment_parameters['site_id'])?xml_escape_string($payment_parameters['site_id']):'';
	$site_secure_code = isset($payment_parameters['site_secure_code'])?xml_escape_string($payment_parameters['site_secure_code']):'';
	
	$id = isset($payment_parameters['id'])?xml_escape_string($payment_parameters['id']):'';

	$xml  = '<?xml version="1.0" encoding="UTF-8"?>'; //<?
	$xml .= '<status ua="custom-1.1">';
	$xml .= '	<merchant>';
	$xml .= '		<account>'.$account.'</account>';
	$xml .= '		<site_id>'.$site_id.'</site_id>';
	$xml .= '		<site_secure_code>'.$site_secure_code.'</site_secure_code>';
	$xml .= '	</merchant>';
	$xml .= '	<transaction>';
	$xml .= '		<id>'.$id.'</id>';
	$xml .= '	</transaction>';
	$xml .= '</status>';

	$ch = curl_init();
	if ($ch){
	
		$parsed_url = parse_url($payment_parameters['action_url']);
	
		if (empty($parsed_url['port'])) {
			$parsed_url['port'] = strtolower($parsed_url['scheme']) == 'https' ? 443 : 80;
		}
	
		$url = $parsed_url['scheme'] . "://" . $parsed_url['host'] . ":" . $parsed_url['port'] . "/";
		
		// generate request
		$header  = "POST " . $parsed_url['path'] ." HTTP/1.1\r\n";
		$header .= "Host: " . $parsed_url['host'] . "\r\n";
		$header .= "Content-Type: text/xml\r\n";
		$header .= "Content-Length: " . strlen($xml) . "\r\n";
		$header .= "Connection: close\r\n";
		$header .= "\r\n";
		$request = $header . $xml;

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_URL,            $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT,        30);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  $request);
		set_curl_options ($ch, $payment_parameters);
	
		$payment_response = curl_exec ($ch);
		if (curl_errno($ch)){
			$error_message = curl_errno($ch)." - ".curl_error($ch);
			return;
		}
		$response_info = curl_getinfo($ch);
		curl_close($ch);

		if ($response_info['http_code'] != 200) {
			$error_message = 'HTTP code is ' . $response_info['http_code'] . ', expected 200';
			return;
		}
		if (strstr($response_info['content_type'], "/xml") === false) {
			$error_message = 'Content type is ' . $response_info['content_type'] . ', expected */xml';
			return;
		}
		$ewallet = array();
		if (preg_match('/\<ewallet\>(.*)\<\/ewallet\>/U', $payment_response, $ewallet)){
			$matches = array();
			if(preg_match('/\<id\>(.*)\<\/id\>/U', $ewallet[1], $matches)){
				$transaction_id = $matches[1];
			}
			$matches = array();
			if(preg_match('/\<status\>(.*)\<\/status\>/U', $ewallet[1], $matches)){
				if(strtolower($matches[1]) != "completed"){
					$error_message = 'Your transaction status is ' . $matches[1];
					return;
				}
			}else{
				$error_message = 'Invalid status.';
				return;
			}
		}else{
			$matches = array();
			if (preg_match('/\<error\>.*\<description\>(.*)\<\/description\>.*\<\/error\>/U', $payment_response, $matches)){
				$error_code = '';
				$error_description = $matches[1];
				$matches = array();
				preg_match('/\<error\>.*\<code\>(.*)\<\/code\>.*\<\/error\>/U', $payment_response, $matches);
				if ($matches > 0) {
					$error_code = 'Error code: '.$matches[1].' ';
				}
				$error_message = $error_code.$error_description;
				return;
			}else{
				$error_message = 'Invalid response.';
				return;
			}
		}
	}else{
		$error_message = "Can't initialize cURL.";
	}
?>