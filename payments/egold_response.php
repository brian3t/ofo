<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  egold_response.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


/*
 * e-gold (www.e-gold.com) transaction handler by http://www.viart.com/
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

    $payee_account = get_param("PAYEE_ACCOUNT");
    $order_id = get_param("PAYMENT_ID");
    $payment_amount = get_param("PAYMENT_AMOUNT");
    $payment_units = get_param("PAYMENT_UNITS");
    $payment_metal_id = get_param("PAYMENT_METAL_ID");
    $payment_batch_num = get_param("PAYMENT_BATCH_NUM");
    $payer_account = get_param("PAYER_ACCOUNT");
    $handshake_hash = get_param("HANDSHAKE_HASH");
    $actual_payment_ounces = get_param("ACTUAL_PAYMENT_OUNCES");
    $usd_per_ounce = get_param("USD_PER_OUNCE");
    $feeweight = get_param("FEEWEIGHT");
    $timestampgmt = get_param("TIMESTAMPGMT");
    $v2_hash = get_param("V2_HASH");

	if (!strlen($order_id)) {
		return;
	}

	$sql  = " SELECT payment_id FROM " . $table_prefix . "orders ";
	$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$payment_id = $db->f("payment_id");
	}

	$sql  = " SELECT parameter_source FROM " . $table_prefix . "payment_parameters ";
	$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
	$sql .= " AND parameter_name='PAYEE_PASSPHRASE'";
    $str_pwd = strtoupper(MD5(get_db_value($sql)));

    $str_v2_hash = $order_id.":".$payee_account.":".$payment_amount.":".
    $payment_units.":".$payment_metal_id.":".$payment_batch_num.":".
    $payer_account.":".$str_pwd.":".$actual_payment_ounces.":".
    $usd_per_ounce.":".$feeweight.":".$timestampgmt;

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

	$error_message = "HASH corrupted";
	$success_message = "Ok";

	// update order status
	if (strtoupper(MD5($str_v2_hash)) == $v2_hash) {
		$order_status = $success_status_id;
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET order_status=" . $db->tosql($order_status, INTEGER) ;
		$sql .= " , success_message=" . $db->tosql($success_message, TEXT);
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;
	} else {
		$order_status = $failure_status_id;
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET order_status=" . $db->tosql($order_status, INTEGER) ;
		$sql .= " , error_message=" . $db->tosql($error_message, TEXT);
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER) ;
	}
	$db->query($sql);

?>