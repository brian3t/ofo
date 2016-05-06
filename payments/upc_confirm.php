<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  upc_confirm.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * eCommerceConnect Gateway (http://ecommerce.upc.ua/) transaction handler by www.viart.com
 */

	$is_admin_path = true;
	$root_folder_path = "../";

	include_once ($root_folder_path ."includes/common.php");
	include_once ($root_folder_path ."includes/date_functions.php");
	include_once ($root_folder_path ."messages/".$language_code."/cart_messages.php");
	include_once ($root_folder_path ."payments/payment_functions.php");

	$MerchantID     = get_param("MerchantID");
	$TerminalID     = get_param("TerminalID");
	$OrderID        = get_param("OrderID");
	$Currency       = get_param("Currency");
	$AltCurrency    = get_param("AltCurrency");
	$SD             = get_param("SD");
	$TotalAmount    = get_param("TotalAmount");
	$AltTotalAmount = get_param("AltTotalAmount");
	$PurchaseTime   = get_param("PurchaseTime");
	$ProxyPan       = get_param("ProxyPan");
	$TranCode       = get_param("TranCode");
	$ApprovalCode   = get_param("ApprovalCode");
	$Rrn            = get_param("Rrn");
	$XID            = get_param("XID");
	$Signature      = get_param("Signature");
	$Delay          = get_param("Delay");
	
	if(!strlen($OrderID)){
		$response = 
			"MerchantID=".$MerchantID."\r\n" .
			"TerminalID=".$TerminalID."\r\n" .
			"OrderID=".$OrderID."\r\n" .
			"Currency=".$Currency."\r\n" .
			"TotalAmount=".$TotalAmount."\r\n" .
			"XID=".$XID."\r\n" .
			"PurchaseTime=".$PurchaseTime."\r\n" .
			"Response.action=error\r\n" .
			"Response.reason=Not exist 'OrderID'\r\n" .
			"Response.forwardUrl=\r\n";
		echo ($response);
		exit;
	}

	$error_message = '';

	$payment_params = upc_payment_params($db->tosql($OrderID, INTEGER));

	if ($TranCode == '000'){

		$data  = $MerchantID.";".$TerminalID.";".$PurchaseTime.";".$OrderID;
		$data .= (strlen($Delay)) ? ','.$Delay.';' : ';';
		$data .= $XID.";".$Currency;
		$data .= (strlen($AltCurrency)) ? ','.$AltCurrency.';' : ';';
		if(strlen($AltTotalAmount)){
			$data .= $TotalAmount.','.$AltTotalAmount.';';
		}else{
			$data .= $TotalAmount.';';
		}
		$data .= $SD.';'.$TranCode.';'.$ApprovalCode.';';
		$b64sign = base64_decode($Signature);
		$fp = fopen($payment_params['PublicServerCert'], "r");
		$cert = fread($fp, 8192);
		fclose($fp);
		$pubkeyid = openssl_get_publickey($cert);
		$status = openssl_verify($data, $b64sign, $pubkeyid);
		openssl_free_key($pubkeyid);
		if($status == 1){
			$sql  = " UPDATE " . $table_prefix . "orders ";
			$sql .= " SET order_status=" . $db->tosql($payment_params['success_status_id'], INTEGER);
			$sql .= ", transaction_id=" . $db->tosql("ApprovalCode=".$ApprovalCode, TEXT);
			$sql .= ", success_message='OK'";
			$sql .= " WHERE order_id=" . $db->tosql($OrderID, INTEGER);
			$db->query($sql);
			$response  = "MerchantID=".$MerchantID."\r\n";
			$response .= "TerminalID=".$TerminalID."\r\n";
			$response .= "OrderID=".$OrderID."\r\n";
			$response .= "Currency=".$Currency."\r\n";
			$response .= "TotalAmount=".$TotalAmount."\r\n";
			$response .= "XID=".$XID."\r\n";
			$response .= "PurchaseTime=".$PurchaseTime."\r\n";
			$response .= "Response.action=approve\r\n";
			$response .= "Response.reason=\r\n";
			$response .= "Response.forwardUrl=".$payment_params['forwardUrl']."\r\n";
			echo ($response);
		}else{
			$error_message = "Signature corrupted ";
			$sql  = " UPDATE " . $table_prefix . "orders ";
			$sql .= " SET order_status=" . $db->tosql($payment_params['failure_status_id'], INTEGER);
			$sql .= ", error_message=" . $db->tosql($error_message, TEXT);
			$sql .= " WHERE order_id=" . $db->tosql($OrderID, INTEGER);
			$db->query($sql);
			$response  = "MerchantID=".$MerchantID."\r\n";
			$response .= "TerminalID=".$TerminalID."\r\n";
			$response .= "OrderID=".$OrderID."\r\n";
			$response .= "Currency=".$Currency."\r\n";
			$response .= "TotalAmount=".$TotalAmount."\r\n";
			$response .= "XID=".$XID."\r\n";
			$response .= "PurchaseTime=".$PurchaseTime."\r\n";
			$response .= "Response.action=reverse\r\n";
			$response .= "Response.reason=".$error_message."\r\n";
			$response .= "Response.forwardUrl=".$payment_params['forwardUrl']."\r\n";
			echo ($response);
		}
	}else{
		$error_message = "Transaction is failure, TranCode: ".$TranCode." ";
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET order_status=" . $db->tosql($payment_params['failure_status_id'], INTEGER);
		$sql .= ", error_message=" . $db->tosql($error_message, TEXT);
		$sql .= " WHERE order_id=" . $db->tosql($OrderID, INTEGER) ;
		$db->query($sql);
		$response  = "MerchantID=".$MerchantID."\r\n";
		$response .= "TerminalID=".$TerminalID."\r\n";
		$response .= "OrderID=".$OrderID."\r\n";
		$response .= "Currency=".$Currency."\r\n";
		$response .= "TotalAmount=".$TotalAmount."\r\n";
		$response .= "XID=".$XID."\r\n";
		$response .= "PurchaseTime=".$PurchaseTime."\r\n";
		$response .= "Response.action=error\r\n";
		$response .= "Response.reason=".$error_message."\r\n";
		$response .= "Response.forwardUrl=".$payment_params['forwardUrl']."\r\n";
		echo ($response);
	}
?>