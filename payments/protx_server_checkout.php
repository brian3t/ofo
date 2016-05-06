<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  protx_server_checkout.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Protx VSP (www.protx.com) transaction handler by www.viart.com
 */

	$is_admin_path = true;
	$root_folder_path = "../";

	include_once($root_folder_path ."includes/common.php");
	include_once($root_folder_path ."includes/date_functions.php");
	include_once($root_folder_path ."messages/".$language_code."/cart_messages.php");
	include_once($root_folder_path ."payments/protx_server_functions.php");

	$VPSProtocol    = get_param("VPSProtocol");
	$TxType         = get_param("TxType");
	$VendorTxCode   = get_param("VendorTxCode");
	$VPSTxId        = get_param("VPSTxId");
	$Status         = get_param("Status");
	$StatusDetail   = get_param("StatusDetail");
	$TxAuthNo       = get_param("TxAuthNo");
	$AVSCV2         = get_param("AVSCV2");
	$AddressResult  = get_param("AddressResult");
	$PostCodeResult = get_param("PostCodeResult");
	$CV2Result      = get_param("CV2Result");
	$GiftAid        = get_param("GiftAid");
	$D3SecureStatus = get_param("3DSecureStatus");
	$CAVV           = get_param("CAVV");
	$VPSSignature   = get_param("VPSSignature");

	$sql  = " SELECT setting_type FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_value LIKE '%protx_server_check.php'";
	$order_final = get_db_value($sql);
   	$idx = strrpos($order_final, "_");
   	$payment_id = substr($order_final, $idx+1);

	if (!strlen($VendorTxCode)) {
		$sql  = " SELECT parameter_source FROM " . $table_prefix . "payment_parameters ";
		$sql .= " WHERE payment_id=".$db->tosql($payment_id, INTEGER)." and parameter_name='RedirectURL'";
		$redirect_url = get_db_value($sql);
		$redirect_url = str_replace("{site_url}", $settings["site_url"], $redirect_url);

		$error_message = "'VendorTxCode' doesn't exist.";
		if (strlen($TxAuthNo)) {
			$error_message .= ' TxAuthNo:' . $TxAuthNo;
		}
		if (strlen($Status)) {
			$error_message .= ' Status:' . $Status;
		}
		if (strlen($StatusDetail)) {
			$error_message .= ' StatusDetail:' . $StatusDetail;
		}
		if (strlen($VPSTxId)) {
			$error_message .= ' VPSTxId:' . $VPSTxId;
		}
		$response  = "Status=INVALID\r\n";
		$response .= "RedirectURL=" . $redirect_url;
		$response .= "\r\n";
		$response .= "StatusDetail=" . $error_message;
		echo $response;
		exit;
	}

	$sql  = " SELECT parameter_source FROM " . $table_prefix . "payment_parameters ";
	$sql .= " WHERE payment_id=".$db->tosql($payment_id, INTEGER)." and parameter_name='VendorTxCode'";
	$VendorTxCode_mask = get_db_value($sql);

   	$prefix_length = strpos($VendorTxCode_mask, "{order_id}");
   	$order_id_length = strlen($VendorTxCode) - strlen($VendorTxCode_mask) + strlen("{order_id}");
   	$order_id = substr($VendorTxCode, $prefix_length, $order_id_length);

	$error_message = '';

	$payment_params = protx_vsp_payment_params($db->tosql($order_id, INTEGER));
	$sql  = " SELECT transaction_id FROM " . $table_prefix . "orders ";
	$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$str_transaction_id = get_db_value($sql);
	$array_transaction_id = explode(' ', $str_transaction_id);
	$array_transaction_id = protx_vsp_get_associative_array('=', $array_transaction_id);

	$signature_src  = $VPSTxId.$VendorTxCode.$Status.$TxAuthNo.$payment_params['Vendor'].$AVSCV2.$array_transaction_id['SecurityKey'];
	$signature_src .= $AddressResult.$PostCodeResult.$CV2Result.$GiftAid.$D3SecureStatus.$CAVV;
	$str_VPSSignature = strtoupper(md5($signature_src));

	if($str_VPSSignature == $VPSSignature) {

		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET authorization_code=" . $db->tosql($TxAuthNo, TEXT);
		$sql .= ", avs_message=" . $db->tosql($AVSCV2, TEXT);
		$sql .= ", avs_address_match=" . $db->tosql($AddressResult, TEXT);
		$sql .= ", avs_zip_match=" . $db->tosql($PostCodeResult, TEXT);
		$sql .= ", cvv2_match=" . $db->tosql($CV2Result, TEXT);
		$sql .= ", secure_3d_status=" . $db->tosql($D3SecureStatus, TEXT);
		$sql .= ", secure_3d_cavv=" . $db->tosql($CAVV, TEXT);
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);

		if ($Status == 'OK') {
			$sql  = " UPDATE " . $table_prefix . "orders ";
			$sql .= " SET order_status=" . $db->tosql($payment_params['success_status_id'], INTEGER);
			$sql .= ", success_message='OK'";
			$sql .= ", error_message=''";
			$sql .= ", pending_message=''";
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$db->query($sql);
			$sql  = " UPDATE " . $table_prefix . "orders_items SET item_status=" . $db->tosql($payment_params['success_status_id'], INTEGER);
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$db->query($sql);
	
			$response  = "Status=OK\r\n";
			$response .= "RedirectURL=" . $payment_params['RedirectURL'];
			$response .= "?oid=" . $order_id;
			$response .= "\r\n";
			$response .= "StatusDetail=";
			echo $response;
		} else {
			$error_message = "";
			if (strlen($StatusDetail)) {
				$error_message .= $StatusDetail;
			}else{
				$error_message .= 'Transaction could not be authorised';
			}
			$error_message .= ' ';
			$sql  = " UPDATE " . $table_prefix . "orders ";
			$sql .= " SET order_status=" . $db->tosql($payment_params['failure_status_id'], INTEGER);
			$sql .= ", error_message=" . $db->tosql($error_message, TEXT);
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$db->query($sql);
			$sql  = " UPDATE " . $table_prefix . "orders_items SET item_status=" . $db->tosql($payment_params['failure_status_id'], INTEGER);
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$db->query($sql);
	
			$response  = "Status=INVALID\r\n";
			$response .= "RedirectURL=" . $payment_params['RedirectURL'];
			if (strlen($order_id)) {
				$response .= "?oid=" . $order_id;
			}
			$response .= "\r\n";
			$response .= "StatusDetail=" . $error_message;
			echo $response;
		}
	}else{
		$error_message = "HASH is corrupted ";
		$response  = "Status=INVALID\r\n";
		$response .= "RedirectURL=" . $payment_params['RedirectURL'];
		if (strlen($order_id)){
			$response .= "?oid=" . $order_id;
		}
		$response .= "\r\n";
		$response .= "StatusDetail=" . $error_message;
		echo $response;
	}

?>