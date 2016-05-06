<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_export_payments.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/shopping_cart.php");
	include_once ($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("export_payments");

	$eol = get_eol();

	$ids = get_param("ids");
	$s_ne = get_param("s_ne");
	$s_min = get_param("s_min");
	$s_max = get_param("s_max");
	$s_sd = get_param("s_sd");
	$s_ed = get_param("s_ed");
	$s_st = get_param("s_st");

	// build where
	$where = "";
	if(strlen($ids)) {
		if (strlen($where)) { $where .= " AND "; }
		$where .= " up.payment_id IN (" . $db->tosql($ids, TEXT, false) . ") ";
	}

	if(strlen($s_ne)) {
		if (strlen($where)) { $where .= " AND "; }
		$s_ne_sql = $db->tosql($s_ne, TEXT, false);
		$where .= " (u.email LIKE '%" . $s_ne_sql . "%'";
		$where .= " OR u.paypal_account LIKE '%" . $s_ne_sql . "%'";
		$where .= " OR u.login LIKE '%" . $s_ne_sql . "%'";
		$where .= " OR u.name LIKE '%" . $s_ne_sql . "%'";
		$where .= " OR u.first_name LIKE '%" . $s_ne_sql . "%'";
		$where .= " OR u.last_name LIKE '%" . $s_ne_sql . "%')";
	}

	if(strlen($s_min)) {
		if (strlen($where)) { $where .= " AND "; }
		$where .= " up.payment_total>=" . $db->tosql($s_min, NUMBER);
	}

	if(strlen($s_max)) {
		if (strlen($where)) { $where .= " AND "; }
		$where .= " up.payment_total<=" . $db->tosql($s_max, NUMBER);
	}

	if(strlen($s_sd)) {
		if (strlen($where)) { $where .= " AND "; }
		$start_date = parse_date($s_sd, $date_edit_format, $date_errors);
		$where .= " up.date_added>=" . $db->tosql($start_date, DATE);
	}

	if(strlen($s_ed)) {
		if (strlen($where)) { $where .= " AND "; }
		$end_date = parse_date($s_ed, $date_edit_format, $date_errors);
		$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
		$where .= " up.date_added<" . $db->tosql($day_after_end, DATE);
	}

	if(strlen($s_st)) {
		if (strlen($where)) { $where .= " AND "; }
		$s_st = $s_st;
		$where .= ($s_st == 1) ? " up.is_paid=1 " : " up.is_paid=0 ";
	}

	$where_sql = ""; 
	if (strlen($where)) {
		$where_sql = " WHERE " . $where;
	}

	$csv_filename = "commission_payments.csv";
	header("Pragma: private");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header("Content-Type: application/octet-stream"); 
	header("Content-Disposition: attachment; filename=" . $csv_filename); 
	header("Content-Transfer-Encoding: binary"); 

	$date_columns  = "\"".ID_MSG."\",\"".PAYMENT_NAME_COLUMN."\",\"".ADMIN_USER_MSG."\",\"".PAYPAL_ACCOUNT_FIELD."\",\"".TOTAL_AMOUNT_MSG."\",\"".PAID_MSG."\"";
	echo $date_columns . $eol;

	$sql  = " SELECT up.payment_id, up.payment_name, up.payment_total, up.is_paid, ";
	$sql .= " u.user_id, u.login, u.name, u.first_name, u.last_name, u.paypal_account ";
	$sql .= " FROM (" . $table_prefix . "users_payments up ";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON u.user_id=up.user_id) ";
	$sql .= $where_sql;

	$expiration_date = date("Y-m-d");

	$db->query($sql);
	while ($db->next_record()) {

		$payment_id = $db->f("payment_id");
		$user_id = $db->f("user_id");
		$payment_name = $db->f("payment_name");
		$payment_total = $db->f("payment_total");
		$paypal_account = $db->f("paypal_account");

		$user_name = $db->f("name");
		if(!strlen($user_name)) {
			$user_name = $db->f("first_name") . " " . $db->f("last_name");
		}
		if(!strlen($user_name)) {
			$user_name = $db->f("login");
		}
		$user_name .= " (id: " . $user_id . ")";
		$is_paid = ($db->f("is_paid") == 1) ? YES_MSG : NO_MSG;

		if(preg_match("/[,\"\n\r\t\s]/", $user_name)) {
			$user_name = "\"" . str_replace("\"", "\"\"", $user_name) . "\"";
		}
		if(preg_match("/[,\"\n\r\t\s]/", $payment_name)) {
			$payment_name = "\"" . str_replace("\"", "\"\"", $payment_name) . "\"";
		}

		$payment_total = round($payment_total, 2);

		$data_row  = "" . $payment_id;
		$data_row .= "," . $payment_name;
		$data_row .= "," . $user_name;
		$data_row .= "," . $paypal_account;
		$data_row .= "," . $payment_total;
		$data_row .= "," . $is_paid;
	  
		echo $data_row . $eol;
	}

?>