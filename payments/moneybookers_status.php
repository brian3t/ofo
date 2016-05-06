<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  moneybookers_status.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/*
 * Moneybookers (www.moneybookers.com) transaction handler by ViArt Ltd. (www.viart.com)
 */

	$is_admin_path = true;
	$root_folder_path = "../";
		
	include_once($root_folder_path ."includes/common.php");
	include_once($root_folder_path ."includes/order_items.php");
	include_once($root_folder_path ."includes/parameters.php");

	$order_id       = get_param('transaction_id', INTEGER);
	$merchant_id    = get_param('merchant_id');
	$mb_amount      = get_param('mb_amount');
	$mb_currency    = get_param('mb_currency');
	$status         = get_param('status', INTEGER);	
	$pay_from_email = get_param('pay_from_email', INTEGER);
	$md5sig         = get_param('md5sig', INTEGER);

	$status_error = '';
	$payment_parameters = array();
	$pass_parameters = array();
	$post_parameters = '';
	$pass_data = array();
	$variables = array();
	get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_parameters, $pass_data, $variables);
	$secret_word = $payment_parameters['secret_word'];
	$my_md5sig = strtoupper(md5($merchant_id . $order_id . strtoupper(md5($secret_word)) . $mb_amount . $mb_currency . $status));
	if($my_md5sig == $md5sig) {
		switch ($status) {
			case -2:
				$order_status = $variables["failure_status_id"]; //failed 
				$error_message = "Transaction status is failed.";
				$sql  = " UPDATE " . $table_prefix . "orders ";
				$sql .= " SET order_status=" . $db->tosql($order_status, INTEGER);
				$sql .= " , error_message=" . $db->tosql($error_message, TEXT);
				$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
				$db->query($sql);
			break;
			case -1:
				$order_status = $variables["failure_status_id"]; //cancelled
				$error_message = "Transaction status is cancelled.";
				$sql  = " UPDATE " . $table_prefix . "orders ";
				$sql .= " SET order_status=" . $db->tosql($order_status, INTEGER);
				$sql .= " , error_message=" . $db->tosql($error_message, TEXT);
				$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
				$db->query($sql);
			break;
			case 2:
				$order_status = $variables["success_status_id"]; //processed 
			break;
			case 0:
			default:
				$order_status = $variables["pending_status_id"]; //pending
				$pending_message = "Transaction status is pending.";
				$sql  = " UPDATE " . $table_prefix . "orders ";
				$sql .= " SET order_status=" . $db->tosql($order_status, INTEGER);
				$sql .= " , pending_message=" . $db->tosql($pending_message, TEXT);
				$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
				$db->query($sql);
			break;
		}
		update_order_status($order_id, $order_status_id, true, "", $status_error);
		if(strlen($status_error)){
			$error_message  = "Moneybookers md5sig checkout failed\n";
			$error_message .= "order_id: $order_id\n";
			$error_message .= "merchant_id: $merchant_id\n";
			$error_message .= "mb_amount: $mb_amount\n";
			$error_message .= "mb_currency: $mb_currency\n";
			$error_message .= "status: $status\n";
			$error_message .= "pay_from_email: $pay_from_email\n";
			$error_message .= "md5sig: $md5sig\n";
			$error_message .= $status_error."\n";
			error_log($error_message);
		}
	} else {
		$error_message  = "Moneybookers md5sig checkout failed\n";
		$error_message .= "order_id: $order_id\n";
		$error_message .= "merchant_id: $merchant_id\n";
		$error_message .= "mb_amount: $mb_amount\n";
		$error_message .= "mb_currency: $mb_currency\n";
		$error_message .= "status: $status\n";
		$error_message .= "pay_from_email: $pay_from_email\n";
		$error_message .= "md5sig: $md5sig\n";
		error_log($error_message);
	}
?>