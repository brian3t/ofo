<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.5                                                  ***
  ***      File:  ogone_basic.php                                          ***
  ***      Built: Tue Aug 19 15:38:10 2008                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * oGone Basic (http://ogone.com) transaction handler by www.viart.com
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
	
	$settings = va_settings();
	$tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
		
	if (isset($payment_params["URL"])) {
		$payment_url = $payment_params["URL"];
	} else {
		$payment_url = "https://secure.ogone.com/ncol/test/orderstandard.asp";
	}
	
	$post_parameters = ""; 
	$payment_params  = array(); 
	$pass_parameters = array(); 
	$pass_data = array(); 
	$variables = array();
	get_payment_parameters($order_id, $payment_params, $pass_parameters, $post_parameters, $pass_data, $variables, "");
		
	$hash_fields = "";
	$post_fields = "";
	/*
	<form method="post"
action="https://secure.ogone.com/ncol/XXXX/orderstandard.asp" id=form1
name=form1>
<!-- general parameters -->
<input type="hidden" name="PSPID" value="">
<input type="hidden" name="orderID" value="">
<input type="hidden" name="amount" value="">
<input type="hidden" name="currency" value="">
<input type="hidden" name="language" value="">
<input type="hidden" name="CN" value="">
<input type="hidden" name="EMAIL" value="">
<input type="hidden" name="ownerZIP" value="">
<input type="hidden" name="owneraddress" value="">
<input type="hidden" name="ownercty" value="">
<input type="hidden" name="ownertown" value="">
<input type="hidden" name="ownertelno" value="">
<!-- check before the payment: see chapter 5 -->
<input type="hidden" name="SHASign" value="">
<!-- layout information: see chapter 6 -->
<input type="hidden" name="TITLE" value="">
<input type="hidden" name="BGCOLOR" value="">
<input type="hidden" name="TXTCOLOR" value="">
<input type="hidden" name="TBLBGCOLOR" value="">
<input type="hidden" name="TBLTXTCOLOR" value="">
<input type="hidden" name="BUTTONBGCOLOR" value="">
<input type="hidden" name="BUTTONTXTCOLOR" value="">
<!-- post payment redirection: see chapter 7 -->
<input type="hidden" name="accepturl" value="">
<input type="hidden" name="declineurl" value="">
<input type="hidden" name="exceptionurl" value="">
<input type="hidden" name="cancelurl" value="">
<input type="submit" value="" id=submit2 name=submit2>
</form>*/
	
	$hash_fields = "";
	$post_fields = "";
	$post_fields .= "order_id=" . $order_id;
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