<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  gate2shop_check.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	$par = array("nameOnCard", "cardNumber", "cvv2", "expMonth", "expYear", "first_name", "last_name", "address1", "address2", "city", 
	"country", "email", "state", "zip", "phone1", "phone2", "phone3", "currency", "customField1", "customField2", "customField3", 
	"customField4", "customField5", "merchant_unique_id", "merchant_site_id", "merchant_id", "requestVersion", "PPP_TransactionID", 
	"productId", "userid", "message", "Error", "Status", "ClientUniqueID", "ExErrCode", "ErrCode", "AuthCode", "Reason", "ReasonCode", 
	"Token", "responsechecksum", "totalAmount", "TransactionID", "ppp_status", "invoice_id", "payment_method", "unknownParameters", 
	"merchantLocale", "customData");
	
	for ($i=0;$i<count($par);$i++){
		$answer[$par[$i]] = get_param($par[$i]);
	}
	
	$error_message = "";
	$post_parameters = ""; 
	$payment_params = array(); 
	$pass_parameters = array(); 
	$pass_data = array(); 
	$variables = array();
	get_payment_parameters($order_id, $payment_params, $pass_parameters, $post_parameters, $pass_data, $variables, "");
	
	$checksum = $answer["TransactionID"].$answer["ErrCode"].$answer["ExErrCode"].$answer["Status"];
	$secret = $payment_parameters["secret"];
	
	if (strlen($secret)){
		$checksum = $secret.$checksum;
	} else {
		$error_message .= "Need Secret Code. ";
	}
	
	//echo "<!-- ".$answer["Status"]." -->";
	
	$checksum = md5($checksum);
	
	if ($checksum != $answer["responsechecksum"] || !strlen($answer["responsechecksum"])){
		$error_message .= "Checksum don't consist with response. ";
	}
	if (strlen($answer["Error"])){
		$error_message = $answer["Error"].". ";
	}
	if (!strlen($answer["invoice_id"])) {
		$error_message = "Can't obtain invoice number parameter.";
	}
	if (!strlen($answer["totalAmount"])) {
		$error_message = "Can't obtain amount parameter.";
	}

?>