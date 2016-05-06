<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  garanti_3d_pay.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * Garanti 3D Pay (www.garanti.com.tr) transaction handler by ViArt Ltd. (www.viart.com)
 */

	if(!isset($payment_parameters['clientid'])){
		$error_message = "'clientid' parameter is required for Garanti 3D Pay.";
		return;
	}
	if(!isset($payment_parameters['oid'])){
		$error_message = "'oid' parameter is required for Garanti 3D Pay.";
		return;
	}
	if(!isset($payment_parameters['amount'])){
		$error_message = "'amount' parameter is required for Garanti 3D Pay.";
		return;
	}
	if(!isset($payment_parameters['okUrl'])){
		$error_message = "'okUrl' parameter is required for Garanti 3D Pay.";
		return;
	}
	if(!isset($payment_parameters['failUrl'])){
		$error_message = "'failUrl' parameter is required for Garanti 3D Pay.";
		return;
	}
	if(!isset($payment_parameters['islemtipi'])){
		$error_message = "'islemtipi' parameter is required for Garanti 3D Pay.";
		return;
	}
	if(!isset($payment_parameters['taksit'])){
		$error_message = "'taksit' parameter is required for Garanti 3D Pay.";
		return;
	}
	if(!isset($payment_parameters['rnd'])){
		$error_message = "'rnd' parameter is required for Garanti 3D Pay.";
		return;
	}
	if(!isset($payment_parameters['storekey'])){
		$error_message = "'storekey' parameter is required for Garanti 3D Pay.";
		return;
	}
	$hashstr = $payment_parameters['clientid'] . $payment_parameters['oid'] . $payment_parameters['amount'] . $payment_parameters['okUrl'] . $payment_parameters['failUrl'] . $payment_parameters['islemtipi'] . $payment_parameters['taksit']  . $payment_parameters['rnd'] . $payment_parameters['storekey'];
	$hash = base64_encode(pack('H*',sha1($hashstr)));

	$t = new VA_Template('.'.$settings["templates_dir"]);
	$t->set_file("main","payment.html");
	$payment_name = 'Garanti 3D Pay';
	$goto_payment_message = str_replace("{payment_system}", $payment_name, GOTO_PAYMENT_MSG);
	$goto_payment_message = str_replace("{button_name}", CONTINUE_BUTTON, $goto_payment_message);
	$t->set_var("GOTO_PAYMENT_MSG", $goto_payment_message);
	$t->set_var("payment_url", $advanced_url);
	$t->set_var("submit_method", "post");

	foreach ($payment_parameters as $parameter_name => $parameter_value) {
		if(isset($pass_parameters[$parameter_name]) && $pass_parameters[$parameter_name] == 1) {
			if($parameter_name == 'cardtype'){
				if(strtoupper($parameter_value) == 'VISA'){
					$parameter_value = 1;
				}
				if(strtoupper($parameter_value) == 'MASTERCARD' || strtoupper($parameter_value) == 'MC'){
					$parameter_value = 2;
				}
			}
			$t->set_var("parameter_name", $parameter_name);
			$t->set_var("parameter_value", $parameter_value);
			$t->parse("parameters", true);
		}
	}
	$t->set_var("parameter_name", 'hash');
	$t->set_var("parameter_value", $hash);
	$t->parse("parameters", true);

	$t->sparse("submit_payment", false);
	$t->pparse("main");
	exit;
?>