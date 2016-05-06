<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  chronopay_confirm.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Chronopay (www.chronopay.com) transaction handler by http://www.viart.com/
 */

	$is_admin_path = true;
	$root_folder_path = "../";
	include_once ($root_folder_path ."includes/common.php");
	include_once ($root_folder_path ."includes/order_items.php");
	include_once ($root_folder_path ."includes/parameters.php");
	include_once ($root_folder_path ."messages/".$language_code."/cart_messages.php");

	$order_id         = get_param("cs1");
	$transaction_type = get_param("transaction_type");
	$transaction_id   = get_param("transaction_id");

	if (strlen($order_id)) {
		$post_parameters = ""; $payment_parameters = array(); $pass_parameters = array(); $pass_data = array(); $variables = array();
		get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_parameters, $pass_data, $variables, "");
		$error_message = "";
		if ($transaction_type == 'decline') {
			$error_message = "Your transaction has been declined. ";
		} else if ($payment_parameters['response_ip'] != get_ip()) {
			$error_message = "Response IP (".get_ip().") has wrong value. ";
		}

		$sql  = " SELECT payment_id FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$payment_id = $db->f("payment_id");
		}
		$order_final = array();
		$setting_type = "order_final_" . $payment_id;
		$sql  = "SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings";
		$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT);
		$db->query($sql);
		while($db->next_record()) {
			$order_final[$db->f("setting_name")] = $db->f("setting_value");
		}
		$success_status_id = get_setting_value($order_final, "success_status_id", "");
		$failure_status_id = get_setting_value($order_final, "failure_status_id", "");

		$order_status = $success_status_id;
		if (strlen($error_message)) {
			$order_status = $failure_status_id;
		}
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET order_status=" . $db->tosql($order_status, INTEGER) ;
		$sql .= " , transaction_id=" . $db->tosql($transaction_id, TEXT);
		if (strlen($error_message)) {
			$sql .= " , error_message=" . $db->tosql($error_message, TEXT);
		} else {
			$sql .= " , success_message=" . $db->tosql("Order was successfully submitted.", TEXT);
		}
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;
		$db->query($sql);
	}
?>