<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  protx_server_process.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * VSP (www.protx.com) transaction handler by www.viart.com
 */

	$is_admin_path = true;
	$root_folder_path = "../";

	include_once ($root_folder_path ."includes/common.php");
	include_once ($root_folder_path ."includes/order_items.php");
	include_once ($root_folder_path ."includes/date_functions.php");
//	include_once ($root_folder_path ."includes/parameters.php");
    include_once ($root_folder_path ."messages/".$language_code."/cart_messages.php");
	include_once ($root_folder_path ."payments/protx_server_functions.php");

	$vc = get_session("session_vc");
	$order_id = get_session("session_order_id");

	$order_errors = check_order($order_id, $vc);
	if($order_errors) {
		echo $order_errors;
		exit;
	}

	$payment_params = array();
	$payment_params = protx_vsp_payment_params($order_id);

	$post_params = protx_vsp_post_parameters($order_id);
	$advanced_url = trim($payment_params['advanced_url']);
	$success_status_id = $payment_params['success_status_id'];
	$failure_status_id = $payment_params['failure_status_id'];
	$failure_action = $payment_params['failure_action'];


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $advanced_url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 90);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_params);
	curl_setopt($ch, CURLOPT_NOBODY, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_ENCODING, "x-www-form-urlencoded");

	$error_message='';
	$transaction_id='';
	$result=curl_exec ($ch);
	if (curl_errno($ch)){
		$error_message = "Curl error: " . curl_errno($ch)." - ".curl_error($ch)." ";
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET error_message=" . $db->tosql($error_message, TEXT) ;
		$sql .= ", order_status=".  $db->tosql($failure_status_id, INTEGER);
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;
		$db->query($sql);
		$sql  = " UPDATE " . $table_prefix . "orders_items SET item_status=" . $db->tosql($failure_status_id, INTEGER);
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
	}
	curl_close ($ch);

	if (strlen($error_message)){
		if ($failure_action == 1){
			header("Location: ".$root_folder_path."credit_card_info.php?payment_error=1");
			exit;
		} else {
			header("Location: ".$root_folder_path."order_final.php");
			exit;
		}
	}

	if (strlen($result)) {
		$output = split(chr(10),$result);
		
		$response = array();
		$response = protx_vsp_get_associative_array('=', $output);
	
		switch($response["Status"]) {
			case 'OK':
				if (isset($response['VPSTxId'])){
					$transaction_id = "VPSTxId=".$response['VPSTxId'];
				}else{
					$error_message = "'VPSTxId' is not found ";
				}
				if (isset($response['SecurityKey'])){
					$transaction_id .= " SecurityKey=".$response['SecurityKey'];
				}else{
					$error_message .= "'SecurityKey'  is not found ";
				}
				protx_vsp_set_order_message($order_id, 'transaction_id', $transaction_id);
				if (!strlen($error_message) && isset($response['NextURL'])){
					header("Location: " . $response["NextURL"]);
					exit;
				}else{
					$error_message .= "'NextURL' is not found ";
				}
		
				if (strlen($error_message) && isset($response['StatusDetail'])){
					$error_message .= $response['StatusDetail'];
				}
			break;
		
			case 'FAIL':
				if (isset($response['StatusDetail'])){
					$error_message .= $response['StatusDetail'];
				}else{
					$error_message .= "Status=FAIL";
				}
			break;
		
			default:
				$error_message .= "Status=".$response['Status'];
				if (isset($response['StatusDetail'])){
					$error_message .= ' '.$response['StatusDetail'];
				}
				if (isset($response['VPSTxId'])){
					$error_message .= "VPSTxId=".$response['VPSTxId'];
				}
				if (isset($response['SecurityKey'])){
					$error_message .= " SecurityKey=".$response['SecurityKey'];
				}
				if (isset($response['NextURL'])){
					$error_message .= " NextURL=".$response["NextURL"];
				}
			break;
		}
	}else{
		echo "Empty response from ProTX Server, please check your payment settings.";
		exit;
	}

	if (strlen($error_message)){
		$error_message .= " ";
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET error_message=" . $db->tosql($error_message, TEXT) ;
		$sql .= ", order_status=".  $db->tosql($failure_status_id, INTEGER);
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;
		$db->query($sql);
		$sql  = " UPDATE " . $table_prefix . "orders_items SET item_status=" . $db->tosql($failure_status_id, INTEGER);
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($failure_action == 1){
			header("Location: ".$root_folder_path."credit_card_info.php?payment_error=1");
			exit;
		} else {
			header("Location: ".$root_folder_path."order_final.php");
			exit;
		}
	}
?>