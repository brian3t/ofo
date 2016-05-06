<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  proxypay_confirm.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
* ProxyPay (www.clear2pay.com) transaction handler by http://www.viart.com/
*/

	$root_folder_path = "../";
	include_once ($root_folder_path . "includes/var_definition.php");
	include_once ($root_folder_path . "includes/constants.php");
	include_once ($root_folder_path . "includes/common_functions.php");
	include_once ($root_folder_path . "includes/va_functions.php");
	include_once ($root_folder_path . "includes/db_$db_lib.php");

	// Database Initialize
	$db = new VA_SQL();
	$db->DBType      = $db_type;
	$db->DBDatabase  = $db_name;
	$db->DBHost      = $db_host;
	$db->DBPort      = $db_port;
	$db->DBUser      = $db_user;
	$db->DBPassword  = $db_password;
	$db->DBPersistent= $db_persistent;

	$shop = get_param("Shop");
	$ref = get_param("Ref");
	$amount = get_param("Amount");
	$currency = get_param("Currency");

	if (!strlen($ref)) {
		return;
	}

	$sql  = " SELECT payment_id, order_total FROM " . $table_prefix . "orders ";
	$sql .= " WHERE order_id=" . $db->tosql($ref, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$payment_id = $db->f("payment_id");
		$order_total = $db->f("order_total");
	}

	$sql  = " SELECT parameter_source FROM " . $table_prefix . "payment_parameters ";
	$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
	$sql .= " AND parameter_name='merchantID'";
	$merchant_id = get_db_value($sql);

	$order_final = array();
	$setting_type = "order_final_" . $payment_id;
	$sql  = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings";
	$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT);
	$db->query($sql);
	while($db->next_record()) {
		$order_final[$db->f("setting_name")] = $db->f("setting_value");
	}

	// get statuses
	$success_status_id = get_setting_value($order_final, "success_status_id", "");
	$failure_status_id = get_setting_value($order_final, "failure_status_id", "");
	$error_message = "Validation failed";
	$success_message = "Ok";

	if (($amount == $order_total) && ($currency == 978) && ($shop = $merchant_id)) {
		$order_status = $success_status_id;
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET order_status=" . $db->tosql($order_status, INTEGER) ;
		$sql .= " , success_message=" . $db->tosql($success_message, TEXT);
		$sql .= " WHERE order_id=" . $db->tosql($ref, INTEGER) ;
		$db->query($sql);
		echo "[OK]";
	} else {
		$order_status = $failure_status_id;
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET order_status=" . $db->tosql($order_status, INTEGER) ;
		$sql .= " , error_message=" . $db->tosql($error_message, TEXT);
		$sql .= " WHERE order_id=" . $db->tosql($ref, INTEGER) ;
		$db->query($sql);
		echo "[NOT]";
	}

?>