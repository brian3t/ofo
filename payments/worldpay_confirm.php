<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  worldpay_confirm.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Worldpay (www.worldpay.com) transaction handler by www.viart.com
 */

	$is_admin_path = true;
	$root_folder_path = "../";
	include_once ($root_folder_path ."includes/common.php");
	include_once ($root_folder_path ."includes/record.php");
	include_once ($root_folder_path ."includes/order_links.php");
	include_once ($root_folder_path ."includes/order_items.php");
	include_once ($root_folder_path ."includes/parameters.php");
	include_once ($root_folder_path ."messages/".$language_code."/cart_messages.php");

	$status_error = '';

	// get parameters passed from 2Checkout.com
	$inst_id        = get_param("instId", POST); // our installation id
	$trans_id       = get_param("transId", POST); // worldpay transaction id
	$order_id       = get_param("cartId", POST); // our cart id number passed in.
	$trans_status   = get_param("transStatus", POST); // Y - successful, C - cancelled
	$amount         = get_param("amount", POST); // amount.
	$currency       = get_param("currency", POST); // currency
	$auth_amount    = get_param("authAmount", POST); // Total purchase amount.
	$auth_currency  = get_param("authCurrency", POST); // currency
	$call_back_pass = get_param("callbackPW", POST); // callbackPW is returned if you have set a Callback password for your installation on the WorldPay Customer Management System.

	$t = new VA_Template('.'.$settings["templates_dir"]);

	if (strlen($order_id)) {
		$failure_status_id = 0;
		$success_status_id = 0;
		$sql  = " SELECT * ";
		$sql .= " FROM " . $table_prefix . "orders ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$payment_id = $db->f("payment_id");
			$sql = "SELECT setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql("order_final_".$payment_id, TEXT) . " AND setting_name='failure_status_id'";
			$db->query($sql);
			if ($db->next_record()) {
				$failure_status_id = $db->f("setting_value");
			}
			$sql = "SELECT setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql("order_final_".$payment_id, TEXT) . " AND setting_name='success_status_id'";
			$db->query($sql);
			if ($db->next_record()) {
				$success_status_id = $db->f("setting_value");
			}
		}
	
		$payment_parameters = array();
		$pass_parameters = array();
		$post_parameters = '';
		$pass_data = array();
		$variables = array();
		get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_parameters, $pass_data, $variables);
	
		$our_pass = isset($payment_parameters["callbackPW"]) ? trim($payment_parameters["callbackPW"]) : "";
		// check parameters
		$error_message = "";
		if (!strlen($inst_id)) {
			$error_message = "Can't obtain installation id.";
		} else if (strlen($our_pass) && strtoupper($our_pass) != strtoupper($call_back_pass)) {
			$error_message = "Callback Password has wrong value.";
		} else if ($trans_status != "Y") {
			$error_message = "Your transaction has been declined.";
		} else {
			//check amount
			$error_message = check_payment($order_id, $amount, $currency);
		}

		// update transaction information
		$order_status = 0;
		$sql  = " UPDATE " . $table_prefix . "orders SET transaction_id=" . $db->tosql($trans_id, TEXT);
		if (!strlen($error_message)) {
			$sql .= ", success_message='OK'";
			if ($success_status_id){
				$order_status = $success_status_id;
			}
		}else{
			$sql .= ", error_message=" . $db->tosql($error_message, TEXT);
			if ($failure_status_id){
				$order_status = $failure_status_id;
			}
		}
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
		if ($order_status) {
			update_order_status($order_id, $order_status, true, "", $status_error);
		}
	
	}

	$t->set_file("main","payment.html");
	$goto_payment_message = str_replace("{payment_system}", $settings["site_url"], GOTO_PAYMENT_MSG);
	$goto_payment_message = str_replace("{button_name}", CONTINUE_BUTTON, $goto_payment_message);
	$t->set_var("GOTO_PAYMENT_MSG", $goto_payment_message);
	$t->set_var("payment_url",$settings["site_url"]."order_final.php");
	$t->set_var("submit_method", "post");
	$t->sparse("submit_payment", false);
	$t->pparse("main");

?>