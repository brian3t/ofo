<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  korta_process.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	/*
 	* KORTA in Iceland transaction handler by www.viart.com
 	*/

	$is_admin_path = true;
	$root_folder_path = "../";

	$root_folder_path = "../";

	include_once ($root_folder_path ."includes/common.php");
	include_once ($root_folder_path ."includes/order_items.php");
    include_once ($root_folder_path ."messages/".$language_code."/cart_messages.php");
	include_once ($root_folder_path ."payments/korta_functions.php");
	include_once ($root_folder_path ."includes/parameters.php");

	$t = new VA_Template($root_folder_path . $settings["templates_dir"]);
	$t->set_file("main","payment.html");
	$t->set_var("CHARSET",                CHARSET);
	$t->set_var("CHECKOUT_PAYMENT_TITLE", CHECKOUT_PAYMENT_TITLE);
	$t->set_var("CONTINUE_BUTTON",        CONTINUE_BUTTON);

	$vc = get_session("session_vc");
	$order_id = get_session("session_order_id");
	$payment_id = get_session("session_payment_id");

	$order_errors = check_order($order_id, $vc);
	if($order_errors) {
		echo $order_errors;
		exit;
	}

	$payment_name = get_translation("KORTA in Iceland");
	$t->set_var("payment_name", $payment_name);

	$goto_payment_message = str_replace("{payment_system}", $payment_name, GOTO_PAYMENT_MSG);
	$goto_payment_message = str_replace("{button_name}", CONTINUE_BUTTON, $goto_payment_message);
	$t->set_var("GOTO_PAYMENT_MSG", $goto_payment_message);

	$post_parameters = ""; $payment_parameters = array(); $pass_parameters = array(); $pass_data = array(); $variables = array();
	$korta_crypt_name = "";
	get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_params, $pass_data, $variables, "");
	if(!is_array($payment_parameters)){
		$error_message = 'Payment parameters are not sent!';
		korta_set_error($order_id, $error_message);
		echo $error_message;
		exit;
	}

	if (isset($payment_parameters["korta_payment_url"])) {
		$payment_url = $payment_parameters["korta_payment_url"];
	} else {
		if (isset($payment_parameters["is_test"]) && $payment_parameters["is_test"]) {
			$payment_url = "https://netgreidslur.korta.is/testing/";
		} else {
			$payment_url = "https://netgreidslur.korta.is/";
		}
	}
	$t->set_var("payment_url", htmlspecialchars($payment_url));

	$parameter_number = 0;

	if (isset($payment_parameters["downloadurl"])) {
		$payment_parameters["downloadurl"] = str_replace("{site_url}", $settings["site_url"], $payment_parameters["downloadurl"]);
	}
	if (isset($payment_parameters["continueurl"])) {
		$payment_parameters["continueurl"] = str_replace("{site_url}", $settings["site_url"], $payment_parameters["continueurl"]);
	}
	if (isset($payment_parameters["description"])) {
		$payment_parameters["description"] = str_replace("\n", " ", $payment_parameters["description"]);
	}
	if (isset($payment_parameters["checkvaluemd5"])) {
		$payment_parameters["checkvaluemd5"] = 	get_korta_crypt($payment_parameters);
	}

	foreach ($payment_parameters as $parameter_name => $parameter_value) {
		if (isset($pass_parameters[$parameter_name]) && $pass_parameters[$parameter_name]) {
			$parameter_number++;
			$payment_url .= ($parameter_number == 1) ? "?" : "&";
			$payment_url .= $parameter_name . "=" . urlencode($parameter_value);
			$t->set_var("parameter_name", $parameter_name);
			$t->set_var("parameter_value", htmlspecialchars($parameter_value));
			$t->parse("parameters", true);
		}
	}

	if (isset($payment_parameters["korta_submit_method"]) && strlen($payment_parameters["korta_submit_method"])) {
		$submit_method = $payment_parameters["korta_submit_method"];
	} else {
		$submit_method = "POST";
	}

	$t->set_var("submit_method", $submit_method);

	if($submit_method == "GET") {
		header("Location: " . $payment_url);
		exit;
	}

	$t->pparse("main");
?>