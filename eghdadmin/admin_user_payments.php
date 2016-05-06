<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_payments.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/sorter.php");
	include_once ($root_folder_path . "includes/navigator.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once ($root_folder_path . "messages/".$language_code."/download_messages.php");

	include_once("./admin_common.php");

	check_admin_security("users_payments");

	$permissions = get_permissions();

	$current_date = va_time();
	$operation = get_param("operation");
	$payments_ids = get_param("payments_ids");
	$status_id = get_param("status_id");

	// update paid/not paid statuses
	$payments_errors = "";
	if (strlen($operation)) {
		if ($operation == "update_status" && strlen($payments_ids) && strlen($status_id)){
			if (isset($permissions["update_payments"]) && $permissions["update_payments"] == 1) {
				$sql  = " UPDATE " . $table_prefix . "users_payments SET is_paid=" . $db->tosql($status_id, INTEGER) . ",";
				$sql .= " date_modified=" . $db->tosql($current_date, DATETIME) . ",";
				$sql .= " admin_id_modified_by=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$sql .= " WHERE payment_id IN (" . $db->tosql($payments_ids, TEXT, false) . ")";
				$db->query($sql);
				if ($status_id == 1) {
					$sql  = " UPDATE " . $table_prefix . "users_payments SET ";
					$sql .= " date_paid=" . $db->tosql($current_date, DATETIME);
					$sql .= " WHERE payment_id IN (" . $db->tosql($payments_ids, TEXT, false) . ")";
					$sql .= " AND date_paid IS NULL ";
					$db->query($sql);
				} else {
					$sql  = " UPDATE " . $table_prefix . "users_payments SET ";
					$sql .= " date_paid=NULL ";
					$sql .= " WHERE payment_id IN (" . $db->tosql($payments_ids, TEXT, false) . ")";
					$db->query($sql);
				}
			} else {
				$payments_errors .= NOT_ALLOWED_UPDATE_PAYMENTS_INFO_MSG;
			}
		}
		if ($operation == "remove_payments" && strlen($payments_ids)){
			if (isset($permissions["remove_payments"]) && $permissions["remove_payments"] == 1) {
				$sql  = " DELETE FROM " . $table_prefix . "users_payments ";
				$sql .= " WHERE payment_id IN (" . $db->tosql($payments_ids, TEXT, false) . ")";
				$db->query($sql);
				$sql  = " UPDATE " . $table_prefix . "users_commissions ";
				$sql .= " SET payment_id=0";
				$sql .= " WHERE payment_id IN (" . $db->tosql($payments_ids, TEXT, false) . ")";
				$db->query($sql);
			} else {
				$payments_errors .= NOT_ALLOWED_REMOVE_PAYMENTS_INFO_MSG;
			}
		}
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_user_payments.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_user_href", "admin_user.php");
	$t->set_var("admin_user_payment_href", "admin_user_payment.php");
	$t->set_var("admin_user_payments_href", "admin_user_payments.php");
	$t->set_var("admin_export_payments_href", "admin_export_payments.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_user_payments.php");
	$s->set_parameters(false, true, true, false);
	$s->set_default_sorting(1, "desc");
	$s->set_sorter(ID_MSG, "sorter_payment_id", "1", "up.payment_id");
	$s->set_sorter(PAYMENT_NAME_COLUMN, "sorter_payment_name", "2", "up.payment_name");
	$s->set_sorter("PayPal", "sorter_paypal_account", "3", "u.paypal_account");
	$s->set_sorter(AMOUNT_MSG, "sorter_payment_total", "4", "up.payment_total");
	$s->set_sorter(PAID_MSG, "sorter_is_paid", "5", "up.is_paid");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_user_payments.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// prepare dates for stats
	$cyear = $current_date[YEAR]; $cmonth = $current_date[MONTH]; $cday = $current_date[DAY]; 
	$today_ts = mktime (0, 0, 0, $cmonth, $cday, $cyear);
	$tomorrow_ts = mktime (0, 0, 0, $cmonth, $cday + 1, $cyear);
	$yesterday_ts = mktime (0, 0, 0, $cmonth, $cday - 1, $cyear);
	$week_ts = mktime (0, 0, 0, $cmonth, $cday - 6, $cyear);
	$month_ts = mktime (0, 0, 0, $cmonth, 1, $cyear);
	$today_date = va_date($date_edit_format, $today_ts);
	$tomorrow_date = va_date($date_edit_format, $tomorrow_ts);
	$yesterday_date = va_date($date_edit_format, $yesterday_ts);
	$week_date = va_date($date_edit_format, $week_ts);
	$month_date = va_date($date_edit_format, $month_ts);

	$t->set_var("date_edit_format", join("", $date_edit_format));
	$t->set_var("today_date",     $today_date);
	$t->set_var("yesterday_date", $yesterday_date);
	$t->set_var("week_date",      $week_date);
	$t->set_var("month_date",     $month_date);

	$types = array( array ("1", PAID_MSG), array ("0", NOT_PAID_MSG));
	$payments_total_today = 0; $payments_total_yesterday = 0; $payments_total_week = 0; $payments_total_month = 0;
	// show payments stats 
	for($i = 0; $i < sizeof($types); $i++) {
		$type_id = $types[$i][0];
		$type_name = $types[$i][1];

		$t->set_var("type_id",   $type_id);
		$t->set_var("type_name", $type_name);

		$sql  = " SELECT SUM(payment_total) FROM " . $table_prefix . "users_payments ";
		$sql .= " WHERE is_paid=" . $db->tosql($type_id, INTEGER);
		$sql .= " AND date_added>=" . $db->tosql($today_ts, DATE);
		$payments_today = get_db_value($sql);
		$payments_total_today += $payments_today;
		if ($payments_today > 0) {
			$payments_today = "<a href=\"admin_user_payments.php?s_st=".$type_id."&s_sd=".$today_date."&s_ed=".$today_date."\"><b>" . currency_format($payments_today ) ."</b></a>";
		}

		$sql  = " SELECT SUM(payment_total) FROM " . $table_prefix . "users_payments ";
		$sql .= " WHERE is_paid=" . $db->tosql($type_id, INTEGER);
		$sql .= " AND date_added>=" . $db->tosql($yesterday_ts, DATE) . " AND date_added<" . $db->tosql($today_ts, DATE);
		$payments_yesterday = get_db_value($sql);
		$payments_total_yesterday += $payments_yesterday;
		if($payments_yesterday > 0) {
			$payments_yesterday = "<a href=\"admin_user_payments.php?s_st=".$type_id."&s_sd=".$yesterday_date."&s_ed=".$yesterday_date."\"><b>" . currency_format($payments_yesterday ) ."</b></a>";
		}

		$sql  = " SELECT SUM(payment_total) FROM " . $table_prefix . "users_payments ";
		$sql .= " WHERE is_paid=" . $db->tosql($type_id, INTEGER);
		$sql .= " AND date_added>=" . $db->tosql($week_ts, DATE);
		$payments_week = get_db_value($sql);
		$payments_total_week += $payments_week;
		if($payments_week > 0) {
			$payments_week = "<a href=\"admin_user_payments.php?s_st=".$type_id."&s_sd=".$week_date."&s_ed=".$today_date."\"><b>" . currency_format($payments_week ) ."</b></a>";
		}

		$sql  = " SELECT SUM(payment_total) FROM " . $table_prefix . "users_payments ";
		$sql .= " WHERE is_paid=" . $db->tosql($type_id, INTEGER);
		$sql .= " AND date_added>=" . $db->tosql($month_ts, DATE);
		$payments_month = get_db_value($sql);
		$payments_total_month += $payments_month;
		if ($payments_month > 0) {
			$payments_month = "<a href=\"admin_user_payments.php?s_st=".$type_id."&s_sd=".$month_date."&s_ed=".$today_date."\"><b>" . currency_format($payments_month ) ."</b></a>";
		}

		$t->set_var("payments_today", $payments_today);
		$t->set_var("payments_yesterday", $payments_yesterday);
		$t->set_var("payments_week", $payments_week);
		$t->set_var("payments_month", $payments_month);

		$t->parse("types_stats", true);
	}
	$t->set_var("payments_total_today", currency_format($payments_total_today ));
	$t->set_var("payments_total_yesterday", currency_format($payments_total_yesterday ));
	$t->set_var("payments_total_week", currency_format($payments_total_week ));
	$t->set_var("payments_total_month", currency_format($payments_total_month ));

	$payment_statuses = array(array("", ALL_MSG), array("1", PAID_MSG), array("0", NOT_PAID_MSG));

	$r = new VA_Record($table_prefix . "users_payments");
	$r->add_textbox("s_ne", TEXT);
	$r->change_property("s_ne", TRIM, true);
	$r->add_textbox("s_min", TEXT);
	$r->change_property("s_min", TRIM, true);
	$r->add_textbox("s_max", TEXT);
	$r->change_property("s_max", TRIM, true);
	$r->add_textbox("s_sd", DATE, FROM_DATE_MSG);
	$r->change_property("s_sd", VALUE_MASK, $date_edit_format);
	$r->change_property("s_sd", TRIM, true);
	$r->add_textbox("s_ed", DATE, END_DATE_MSG);
	$r->change_property("s_ed", VALUE_MASK, $date_edit_format);
	$r->change_property("s_ed", TRIM, true);
	$r->add_select("s_st", TEXT, $payment_statuses);
	$r->get_form_parameters();
	$r->validate();
	$payment_statuses = array(array("", ""), array("1", PAID_MSG), array("0", NOT_PAID_MSG));
	$r->add_select("status_id", TEXT, $payment_statuses);
	$r->set_form_parameters();

	$where = "";
	$product_search = false;

	if(!$r->errors) {

		if(!$r->is_empty("s_ne")) {
			if (strlen($where)) { $where .= " AND "; }
			$s_ne_sql = $db->tosql($r->get_value("s_ne"), TEXT, false);
			$where .= " (u.email LIKE '%" . $s_ne_sql . "%'";
			$where .= " OR u.paypal_account LIKE '%" . $s_ne_sql . "%'";
			$where .= " OR u.login LIKE '%" . $s_ne_sql . "%'";
			$where .= " OR u.name LIKE '%" . $s_ne_sql . "%'";
			$where .= " OR u.first_name LIKE '%" . $s_ne_sql . "%'";
			$where .= " OR u.last_name LIKE '%" . $s_ne_sql . "%')";
		}

		if(!$r->is_empty("s_min")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " up.payment_total>=" . $db->tosql($r->get_value("s_min"), NUMBER);
		}

		if(!$r->is_empty("s_max")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " up.payment_total<=" . $db->tosql($r->get_value("s_max"), NUMBER);
		}

		if(!$r->is_empty("s_sd")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " up.date_added>=" . $db->tosql($r->get_value("s_sd"), DATE);
		}

		if(!$r->is_empty("s_ed")) {
			if (strlen($where)) { $where .= " AND "; }
			$end_date = $r->get_value("s_ed");
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$where .= " up.date_added<" . $db->tosql($day_after_end, DATE);
		}

		if(!$r->is_empty("s_st")) {
			if (strlen($where)) { $where .= " AND "; }
			$s_st = $r->get_value("s_st");
			$where .= ($s_st == 1) ? " up.is_paid=1 " : " up.is_paid=0 ";
		}

	}

	$where_sql = ""; 
	if (strlen($where)) {
		$where_sql = " WHERE " . $where;
	}

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) ";
	$sql .= " FROM (" . $table_prefix . "users_payments up ";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON u.user_id=up.user_id) ";
	$sql .= $where_sql;
	$total_records = get_db_value($sql);

	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT up.payment_id, up.payment_name, up.payment_total, up.is_paid, ";
	$sql .= " u.user_id, u.login, u.name, u.first_name, u.last_name, u.paypal_account ";
	$sql .= " FROM (" . $table_prefix . "users_payments up ";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON u.user_id=up.user_id) ";
	$sql .= $where_sql;

	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		$payment_index = 0;
		do {
			$payment_total = $db->f("payment_total");
			$user_id = $db->f("user_id");
			$user_name = $db->f("name");
			if(!strlen($user_name)) {
				$user_name = $db->f("first_name") . " " . $db->f("last_name");
			}
			if(!strlen($user_name)) {
				$user_name = $db->f("login");
			}
			$user_name .= " (id: " . $user_id . ")";
			$is_paid = ($db->f("is_paid") == 1) ? YES_MSG : NO_MSG;

			$payment_index++;
			$row_style = ($payment_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);

			$t->set_var("payment_index", $payment_index);
			$t->set_var("payment_id", $db->f("payment_id"));
			$t->set_var("payment_name", htmlspecialchars($db->f("payment_name")));
			$t->set_var("name", htmlspecialchars($user_name));
			$t->set_var("paypal_account", $db->f("paypal_account"));
			$t->set_var("payment_total", currency_format($payment_total ));
			$t->set_var("is_paid", $is_paid);

			$t->parse("records", true);
		} while($db->next_record());
		$t->set_var("payments_number", $payment_index);

		if (isset($permissions["update_payments"]) && $permissions["update_payments"] == 1) {
			$t->parse("update_status", false);
		}

		if(isset($permissions["remove_payments"]) && $permissions["remove_payments"] == 1) {
			$t->parse("remove_payments_button", false);
		}
		//$t->parse("update_status", false);
		//$t->parse("remove_payments_button", false);
		$t->parse("sorters", false);
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->set_var("update_status", "");
		$t->set_var("remove_payments_button", "");
		$t->parse("no_records", false);
	}

	if(isset($permissions["add_payments"]) && $permissions["add_payments"] == 1) {
		$t->parse("new_payment_link", false);
	}
	
	if (strlen($payments_errors)) {
		$t->set_var("errors_list", $payments_errors);
		$t->parse("payments_errors", false);
	}

	if ($total_records > 0) {
		$admin_export_filtered_url = new VA_URL("admin_export_payments.php", false);
		$admin_export_filtered_url->add_parameter("s_ne", GET, "s_ne");
		$admin_export_filtered_url->add_parameter("s_min", GET, "s_min");
		$admin_export_filtered_url->add_parameter("s_max", GET, "s_max");
		$admin_export_filtered_url->add_parameter("s_sd", GET, "s_sd");
		$admin_export_filtered_url->add_parameter("s_ed", GET, "s_ed");
		$admin_export_filtered_url->add_parameter("s_st", GET, "s_st");

		$t->set_var("admin_export_filtered_url", $admin_export_filtered_url->get_url());
		$t->set_var("total_filtered", $total_records);
		$t->parse("export_filtered", false);
	}

	$t->set_var("s_st_search", $r->get_value("s_st"));

	$t->pparse("main");

?>