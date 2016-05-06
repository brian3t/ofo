<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  cybersource_notify.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Cybersource (www.cybersource.com) SOP response handler by ViArt Ltd. (www.viart.com)
 */
	$is_admin_path = true;
	$root_folder_path = "../";
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/parameters.php");
	include_once($root_folder_path . "includes/order_items.php");
	include_once($root_folder_path . "payments/cybersource_functions.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");

	$decision = get_param("decision");
	$decision_publicSignature = get_param("decision_publicSignature");
	$orderAmount = get_param("orderAmount");
	$orderAmount_publicSignature = get_param("orderAmount_publicSignature");
	$orderNumber = get_param("orderNumber");
	$orderNumber_publicSignature = get_param("orderNumber_publicSignature");
	$orderCurrency = get_param("orderCurrency");
	$orderCurrency_publicSignature = get_param("orderCurrency_publicSignature");

	$post_parameters = ""; $payment_parameters = array(); $pass_parameters = array(); $pass_data = array(); $variables = array();
	get_payment_parameters($orderNumber, $payment_parameters, $pass_parameters, $post_params, $pass_data, $variables, "");
	$success_status_id = isset($variables["success_status_id"]) ? $variables["success_status_id"] : 0;
	$pending_status_id = isset($variables["pending_status_id"]) ? $variables["pending_status_id"] : 0;
	$failure_status_id = isset($variables["failure_status_id"]) ? $variables["failure_status_id"] : 0;
	$pub = isset($payment_parameters["PublicKey"]) ? $payment_parameters["PublicKey"] : "";

	$success_message = ""; $pending_message = ""; $error_message = ""; $order_status_id = 0;
	if (strlen($pub))
	{
		if (VerifySignature($decision, $decision_publicSignature, $pub)
			&& VerifySignature($orderAmount, $orderAmount_publicSignature, $pub)
			&& VerifySignature($orderNumber, $orderNumber_publicSignature, $pub)
			&& VerifySignature($orderCurrency, $orderCurrency_publicSignature, $pub))
		{
			if ($decision == "ACCEPT") {
				$success_message = "Your order has been accepted.";
			} elseif ($decision == "REVIEW") {
				$pending_message = "Your order will be reviewed.";
			} else {
				$error_message .= " Your order has been rejected.";
			}
		}
		else
		{
			if (!VerifySignature($decision, $decision_publicSignature, $pub)) {
				$error_message .= " Order decision is not valid.";
			}
			if (!VerifySignature($orderAmount, $orderAmount_publicSignature, $pub))	{
				$error_message .=  " Order amount is not valid.";
			}
			if (!VerifySignature($orderNumber, $orderNumber_publicSignature, $pub))	{
				$error_message .= " Order number is not valid.";
			}
			if (!VerifySignature($orderCurrency, $orderCurrency_publicSignature, $pub)) {
				$error_message .= " Order currency is not valid.";
			}
		}
	}
	else
	{
		$error_message .= " Public Key is empty.";
	}
	if (strlen($error_message)) {
		$order_status_id = $failure_status_id;
	} elseif (strlen($pending_message)) {
		$order_status_id = $pending_status_id;
	} elseif (strlen($success_message)) {
		$order_status_id = $success_status_id;
	} else {
		$order_status_id = $failure_status_id;
		$error_message = "Unknown error occured while processing Cybersource response.";
	}

	if ($order_status_id) {
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET order_status = " . $db->tosql($order_status_id, INTEGER);
		$sql .= ", success_message=" . $db->tosql($success_message, TEXT);
		$sql .= ", pending_message=" . $db->tosql($pending_message, TEXT);
		$sql .= ", error_message=" . $db->tosql($error_message, TEXT);
		$sql .= " WHERE order_id=" . $db->tosql($orderNumber, INTEGER);
		$db->query($sql);
	}

?>