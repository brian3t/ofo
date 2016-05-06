<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  sips_request.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * SIPS (www.sips.atosorigin.com) transaction handler by ViArt Ltd. (www.viart.com)
 */
	chdir ('../');
	include_once("./includes/common.php");
	include_once("./includes/shopping_cart.php");
	include_once("./includes/order_items.php");
	include_once("./includes/parameters.php");
	include_once("./messages/".$language_code."/cart_messages.php");

	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main","credit_card_info_cutted.html");
	
	$vc = get_session("session_vc");
	$order_id = get_session("session_order_id");
	
	$order_errors = check_order($order_id, $vc);
	if($order_errors) {
		echo $order_errors;
		exit;
	}	
	
	$post_parameters = ""; 
	$payment_params = array(); 
	$pass_parameters = array(); 
	$pass_data = array(); 
	$variables = array();
	get_payment_parameters($order_id, $payment_params, $pass_parameters, $post_parameters, $pass_data, $variables, "");
		
	$amount     = number_format($variables["order_total"] * $variables["currency_rate"], 2);
	switch (strtoupper($variables["currency_code"])) {
		case "YEN":
			$sips_currency_code   = 392;
			$sips_currency_digits = 0;
		break;
		case "EU": case "EUR": case "EURO":
			$sips_currency_code   = 978;
			$sips_currency_digits = 2;
		break;
		case "DOL": case "DOLLAR": 
			$sips_currency_code   = 840;
			$sips_currency_digits = 2;
		break;
		default:				
			if ($variables["currency_value"]) {
				$sips_currency_code   = $variables["currency_value"];
			} else {
				$sips_currency_code   = 840;
			}
			$sips_currency_digits = 2;
		break;			
	}
	$amount  = round($amount * pow(10, $sips_currency_digits));
				
	$params  = " order_id=" . $order_id;
	$params .= " amount=" . $amount ;	
	$params .= " merchant_id=" . $payment_params['merchant_id'];
	$params .= " merchant_country=" . $payment_params['merchant_country'];
	$params .= " currency_code=" . $sips_currency_code ;
	$params .= " pathfile=" .  $payment_params['pathfile'];
	$params .= " normal_return_url=" .  $payment_params['normal_return_url'];
	$params .= " cancel_return_url=" . $payment_params['cancel_return_url'];
	$params .= " automatic_response_url=" . $payment_params['automatic_response_url'];

	if (isset($payment_params['payment_means'])) {
		$params .= " payment_means=" . $payment_params['payment_means'];
	}	
	if (isset($payment_params['return_context'])) {
		$params .= " return_context=" . $payment_params['return_context'];
	}		
	if (isset($payment_params['language'])) {
		$params .= " language=" . $payment_params['language'];
	} else {
		$params .= " language=" . $language_code;
	}
	if ($variables["email"]) {
		$params .= " customer_email=" . $variables["email"];
	}
	if ($variables["user_id"]) {
		$params .= " customer_id=" . $variables["user_id"];
	}		
	$params .= " customer_ip_address=" . get_ip();
	
	exec($payment_params['path_bin'] . $params, $result);
		
	if ($result && $result[0]) {
		list($empty, $code, $error, $message) = explode ("!", $result[0]);	
		if (($code == "") && ($error == "")) {
			$html  = "<h2>ERROR</h2>";
			$html .= "executable request didnt found " . $payment_params['path_bin'];
	 	} else if ($code != 0) {
			$html  = "<h2>ERROR while payment process</h2>";
			$html .= $error;
		} else {
			$html  = $error . "<br/>";
			$html .= $message . "<br/>";
		}
	} else {
		$html  = "<h2>ERROR</h2>";
		$html .= "executable request didnt found " . $payment_params['path_bin'];
	}
	
	$t->set_var('html_snippet', $html);
	
	include("./header.php");
	include("./footer.php");
	
	$t->pparse("main");
?>