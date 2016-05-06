<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  egold_check.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * e-gold (www.e-gold.com) transaction handler by http://www.viart.com/
 */

	$payee_account = get_param("PAYEE_ACCOUNT");
    $order_id = get_param("PAYMENT_ID");
    $payment_amount = get_param("PAYMENT_AMOUNT");
    $payment_units = get_param("PAYMENT_UNITS");
    $payment_metal_id = get_param("PAYMENT_METAL_ID");
    $payment_batch_num = get_param("PAYMENT_BATCH_NUM");
    $payer_account = get_param("PAYER_ACCOUNT");
    $nopayment = get_param("nopayment");
    $payment = get_param("payment");

	$sql  = " SELECT payment_id FROM " . $table_prefix . "orders ";
	$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$payment_id = $db->f("payment_id");
	}
	$order_final = array();
	$setting_type = "order_final_" . $payment_id;
	$sql = "SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
	$db->query($sql);
	while ($db->next_record()) {
		$order_final[$db->f("setting_name")] = $db->f("setting_value");
	}
	$success_status_id = get_setting_value($order_final, "success_status_id", "");
	$failure_status_id = get_setting_value($order_final, "failure_status_id", "");
	$pending_status_id = get_setting_value($order_final, "pending_status_id", "");

	if (strtoupper($nopayment) == "CANCEL") {
		$error_message = "The client has cancelled transaction.";
		$failed_message = "Transaction is failure.";
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET order_status=" . $db->tosql($failure_status_id, INTEGER) ;
		$sql .= " , error_message=" . $db->tosql($error_message, TEXT);
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;
		$db->query($sql);
		return;
	}

	if (strtoupper($payment) == "CONTINUE") {
		$sql  = " SELECT success_message FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$success_message = get_db_value($sql);
		if (!strlen($success_message)) {
			$pending_message = "There are no answer from payment gateway. This order will be reviewed manually.";
			$sql  = " UPDATE " . $table_prefix . "orders ";
			$sql .= " SET order_status=" . $db->tosql($pending_status_id, INTEGER) ;
			$sql .= " , pending_message=" . $db->tosql($pending_message, TEXT);
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;
			$db->query($sql);
		}
	}

?>