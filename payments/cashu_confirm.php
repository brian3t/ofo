<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  cashu_confirm.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * Cashu (www.cashu.com) transaction handler by www.viart.com
 */

	$is_admin_path = true;
	$root_folder_path = "../";
	include_once ($root_folder_path ."includes/common_functions.php");
	$session_id = get_param("session_id", POST);
	session_id($session_id);
	include_once ($root_folder_path ."includes/common.php");
	include_once ($root_folder_path ."includes/order_items.php");
	include_once ($root_folder_path ."includes/parameters.php");
	include_once ($root_folder_path ."messages/".$language_code."/cart_messages.php");

	$language   = get_param("language", POST);
	$amount     = get_param("amount", POST);
	$currency   = get_param("currency", POST);
	$txt1       = get_param("txt1", POST);
	$txt2       = get_param("txt2", POST);
	$txt3       = get_param("txt3", POST);
	$txt4       = get_param("txt4", POST);
	$txt5       = get_param("txt5", POST);
	$txt6       = get_param("txt6", POST);
	$txt7       = get_param("txt7", POST);
	$token_in   = get_param("token", POST);
	$test_mode  = get_param("test_mode", POST);
	$trn_id     = get_param("trn_id", POST);

	$order_id = get_session("session_order_id");
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
		$post_parameters = ""; $payment_parameters = array(); $pass_parameters = array(); $pass_data = array(); $variables = array();
		get_payment_parameters($order_id, $payment_parameters, $pass_parameters, $post_parameters, $pass_data, $variables, "");
	
		$token_str = $payment_parameters['merchant_id'].':'.$payment_parameters['amount'].':'.$payment_parameters['currency'].':'.$payment_parameters['keyword'];
		$token = md5(strtolower($token_str));

		// check parameters
		$error_message = "";
		if ($token != $token_in) {
			$error_message = "HASH is corrupted. ";
		} else if (!strlen($trn_id)) {
			$error_message = "Transaction ID has empty value. ";
		} else if ($payment_parameters['response_ip'] != get_ip()) {
			$error_message = "Response IP (".get_ip().") has wrong value. ";
		} else {
			//check amount
			$error_message = check_payment($order_id, $amount, $currency);
		}

		// update transaction information
		$sql  = " UPDATE " . $table_prefix . "orders SET transaction_id=" . $db->tosql($trn_id, TEXT);
		if (!strlen($error_message)) {
			$sql .= ", success_message='OK'";
			if ($success_status_id){
				$sql .= ", order_status=".  $db->tosql($success_status_id, INTEGER);
		
				$sql_items  = " UPDATE " . $table_prefix . "orders_items SET item_status=" . $db->tosql($success_status_id, INTEGER);
				$sql_items .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
				$db->query($sql_items);
			}
		}else{
			$sql .= ", error_message=" . $db->tosql($error_message, TEXT);
			if ($failure_status_id){
				$sql .= ", order_status=".  $db->tosql($failure_status_id, INTEGER);
		
				$sql_items  = " UPDATE " . $table_prefix . "orders_items SET item_status=" . $db->tosql($failure_status_id, INTEGER);
				$sql_items .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
				$db->query($sql_items);
			}
		}
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);
	}
	
	$t = new VA_Template('.'.$settings["templates_dir"]);
	$t->set_file("main","payment.html");
	$goto_payment_message = str_replace("{payment_system}", $settings["site_url"], GOTO_PAYMENT_MSG);
	$goto_payment_message = str_replace("{button_name}", CONTINUE_BUTTON, $goto_payment_message);
	$t->set_var("GOTO_PAYMENT_MSG", $goto_payment_message);
	$t->set_var("payment_url",$settings["site_url"]."order_final.php");
	$t->set_var("submit_method", "post");
	$t->sparse("submit_payment", false);
	$t->pparse("main");
?>