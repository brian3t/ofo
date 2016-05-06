<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_payment.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	check_admin_security("create_orders");
	
	$rp = new VA_Record($table_prefix . "orders");
	$rp->errors = "";

	$rp->add_where("order_id", INTEGER);
	$rp->set_value("order_id", $order_id);

	$rp->add_textbox("error_message", TEXT);
	$rp->add_textbox("pending_message", TEXT);
	$rp->add_textbox("transaction_id", TEXT);
	$rp->change_property("transaction_id", USE_IN_UPDATE, false);

	if ($rp->is_empty("order_id")) {
		$rp->errors .= MISSING_ORDER_NUMBER_MSG."<br>";
	}
	
	if (!strlen($rp->errors))
	{
		$payment_id = "";
		$sql  = " SELECT ps.payment_id ";
		$sql .= " FROM " . $table_prefix . "orders o, " . $table_prefix . "payment_systems ps ";
		$sql .= " WHERE o.payment_id=ps.payment_id ";
		$sql .= " AND o.order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$payment_id = $db->f("payment_id");
		}

		// get orders variables
		if (!strlen($payment_id)) { // check for payment_id if we lost it somewhere
			$sql = "SELECT payment_id FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$payment_id = get_db_value($sql);
			if (!strlen($payment_id)) { // if we missed it again look for it in payment_systems table
				$sql = "SELECT payment_id FROM " . $table_prefix . "payment_systems WHERE is_call_center=1 AND is_advanced=1 ";
				$payment_id = get_db_value($sql);
			}
		}
		
		$is_advanced = false;
		if (strlen($payment_id))
		{
			$db->query("SELECT * FROM " . $table_prefix . "payment_systems WHERE is_call_center=1 AND payment_id=" . $db->tosql($payment_id, INTEGER));
			if ($db->next_record()) {
				$is_advanced  = $db->f("is_advanced");
				$advanced_url = $db->f("advanced_url");
				$advanced_php_lib = $db->f("advanced_php_lib");
				$success_status_id = $db->f("success_status_id");
				$pending_status_id = $db->f("pending_status_id");
				$failure_status_id = $db->f("failure_status_id");
				$failure_action = $db->f("failure_action");
			}
		}
		
		$error_message = ""; $pending_message = ""; $transaction_id = "";
		if ($is_advanced && strlen($advanced_php_lib))
		{
			// get payment parameters
			$post_params = ""; $payment_parameters = array(); $pass_parameters = array(); $variables = array();
			get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_params, $pass_data, $variables);
		
			// use foreign php library to handle transaction
			include_once($root_folder_path . $advanced_php_lib);
		
			if (strlen($error_message)) {
				$order_status = $failure_status_id;
			} elseif (strlen($pending_message)) {
				$order_status = $pending_status_id;
			} else {
				$order_status = $success_status_id;
			}
		
			$rp->set_value("error_message", $error_message);
			$rp->set_value("pending_message", $pending_message);
			if (strlen($transaction_id)) {
				$rp->set_value("transaction_id", $transaction_id);
				$rp->change_property("transaction_id", USE_IN_UPDATE, true);
			}
		
			// update order status
			update_order_status($order_id, $order_status, true, "", $status_error);
		
		}
		$rp->update_record();
		
	}
		
?>