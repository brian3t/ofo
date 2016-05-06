<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  ogone_request.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * oGone (http://ogone.com) transaction handler by www.viart.com
 */

	$is_admin_path = true;
	$root_folder_path = "../";

	include_once ($root_folder_path ."includes/common.php");
	include_once ($root_folder_path ."includes/shopping_cart.php");
	include_once ($root_folder_path ."includes/order_items.php");
	include_once ($root_folder_path ."includes/parameters.php");
	include_once ($root_folder_path ."messages/".$language_code."/cart_messages.php");

	$vc = get_session("session_vc");
	$order_id = get_session("session_order_id");

	$order_errors = check_order($order_id, $vc);
	if ($order_errors) {
		echo $order_errors;
		exit;
	}
	
	$post_parameters = "";
	$payment_params  = array();
	$pass_parameters = array();
	$pass_data = array();
	$variables = array();
	get_payment_parameters($order_id, $payment_params, $pass_parameters, $post_parameters, $pass_data, $variables, "");

	if (isset($payment_params["URL"])) {
		$payment_url = $payment_params["URL"];
	} else {
		$payment_url = "https://secure.ogone.com/ncol/test/orderstandard.asp";
	}
	
	$hash_fields = "";
	$post_fields = "";
	$post_fields .= "orderID=" . $order_id;
	$hash_fields .= $order_id;
	$post_fields .= "&amount=" . $variables["order_total"] * 100;
	$hash_fields .= $variables["order_total"] * 100;
	$post_fields .= "&currency=" . $variables["currency_code"];
	$hash_fields .=  $variables["currency_code"];
	if (isset($payment_params["PSPID"])) {
		$post_fields .= "&PSPID=" . $payment_params["PSPID"];
		$hash_fields .= $payment_params["PSPID"];
	} else {
		$error_message = 'PSPID (Your affiliation name in our system) is required!';
		exit;
	}
	if (isset($payment_params["SHAsignature"])) {
		$hash_fields .= $payment_params["SHAsignature"];
	} else {
		$error_message = 'SHAsignature(Your affiliation name in our system) is required!';
		exit;
	}
	if (isset($payment_params["language"])) {
		$post_fields.= "&language=" . $payment_params["language"];
	} else {
		$post_fields.= "&language=" . $language_code;
	}
	if (isset($payment_params["CN"])) {
		$post_fields.= "&CN=" . $payment_params["CN"];
	} else {
		$post_fields.= "&CN=" . $variables["last_name"];
	}
	$post_fields.= "&email=" . $variables["email"];

	$hash = sha1($hash_fields);	
	$post_fields .= "&SHASign=" .  strtoupper($hash);
	
	if (isset($payment_params["accepturl"]))
		$post_fields.= "&accepturl=" . $payment_params["accepturl"];
	if (isset($payment_params["declineurl"]))
		$post_fields.= "&declineurl=" . $payment_params["declineurl"];
	if (isset($payment_params["exceptionurl"]))
		$post_fields.= "&exceptionurl=" . $payment_params["exceptionurl"];
	if (isset($payment_params["cancelurl"]))
		$post_fields.= "&cancelurl=" . $payment_params["cancelurl"];	
	
	$header = "Location: " .$payment_url . "?" . $post_fields;
	header($header);
?>