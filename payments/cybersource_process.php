<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  cybersource_process.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Cybersource (www.cybersource.com) SOP transaction handler by ViArt Ltd. (www.viart.com)
 */

	global $is_admin_path, $is_sub_folder;
	$root_folder_path = ((isset($is_admin_path) && $is_admin_path) || (isset($is_sub_folder) && $is_sub_folder)) ? "../" : "./";
	include_once($root_folder_path . "payments/cybersource_functions.php");

	$pass_data = array();
	foreach ($payment_parameters as $parameter_name => $parameter_value) {
		if (isset($pass_parameters[$parameter_name]) && $pass_parameters[$parameter_name] == 1) {
			$pass_data[$parameter_name] = $parameter_value;
		}
	}

	$merchantID = $payment_parameters["merchantID"];
	$amount = isset($pass_data["amount"]) ? $pass_data["amount"] : "0.00";
	$currency = isset($pass_data["currency"]) ? $pass_data["currency"] : "usd";
	$card_type = isset($pass_data["card_cardType"]) ? $pass_data["card_cardType"] : "Visa";
	$timestamp = getmicrotime();
	$data = $merchantID . $amount . $currency . $timestamp;
	$pub = $payment_parameters["PublicKey"];
	$pub_digest = hopHash($data, $pub);

	switch (strtoupper($card_type)) {
		case "VISA":
			$pass_data["card_cardType"] = "001";
			break;
		case "MC":
			$pass_data["card_cardType"] = "002";
			break;
		case "AMEX":
			$pass_data["card_cardType"] = "003";
			break;
		case "DISCOVER":
			$pass_data["card_cardType"] = "004";
			break;
		case "JCB":
			$pass_data["card_cardType"] = "007";
			break;
		case "SOLO":
			$pass_data["card_cardType"] = "024";
			break;
		default:
			$pass_data["card_cardType"] = "001";
	}

	$pass_data["amount"] = $amount;
	$pass_data["currency"] = $currency;
	$pass_data["orderPage_timestamp"] = $timestamp;
	$pass_data["orderPage_signaturePublic"] = $pub_digest;

	$post_params_encoded = "";
	foreach ($pass_data as $parameter_name => $parameter_value) {
		if (strlen($post_params_encoded)) {
			$post_params_encoded .= "&";
		}
		$post_params_encoded .= $parameter_name . "=" . urlencode($parameter_value);
	}

	$ch = @curl_init();
	if ($ch) {
		curl_setopt($ch, CURLOPT_URL, $advanced_url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params_encoded);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, "ViArt SHOP Cybersource payment module");
		set_curl_options($ch, $payment_parameters);

		$payment_response = curl_exec($ch);
		curl_close($ch);
		echo $payment_response; exit;
	} else {
		$error_message .= "Can't initialize cURL.";
	}

?>