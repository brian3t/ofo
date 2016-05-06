<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_commissions.php                               ***
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

	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("users_payments");

	$permissions = get_permissions();

	$current_date = va_time();
	$operation = get_param("operation");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_user_commissions.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_user_href", "admin_user.php");
	$t->set_var("admin_user_payment_href", "admin_user_payment.php");
	$t->set_var("admin_user_commissions_href", "admin_user_commissions.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_user_commissions.php");
	$s->set_parameters(false, true, true, false);
	$s->set_default_sorting(3, "desc");
	$s->set_sorter(ADMIN_USER_MSG, "sorter_user", "1", "u.name", "u.name, u.first_name, u.last_name, u.login", "u.name DESC, u.first_name DESC, u.last_name DESC, u.login DESC");
	if ($db_type == "access") {
		$s->set_sorter(FROM_DATE_MSG, "sorter_from_date", "2", "MIN(uc.date_added)");
		$s->set_sorter(TO_DATE_MSG, "sorter_to_date", "3", "MAX(uc.date_added)");
		$s->set_sorter(TOTAL_MSG, "sorter_commission_total", "4", "SUM(uc.commission_action * uc.commission_amount)");
	} else {
		$s->set_sorter(FROM_DATE_MSG, "sorter_from_date", "2", "from_date");
		$s->set_sorter(TO_DATE_MSG, "sorter_to_date", "3", "to_date");
		$s->set_sorter(TOTAL_MSG, "sorter_commission_total", "4", "commission_total");
	}
	$s->set_sorter(STATUS_MSG, "sorter_status", "5", "uc.payment_id" , "uc.payment_id, up.is_paid ASC ", "up.is_paid DESC");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_user_commissions.php");

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

	$types = array( 
		array (0, NEW_MSG, " WHERE uc.payment_id=0 "), 
		array (1, PAID_MSG, " , " . $table_prefix . "users_payments up WHERE uc.payment_id=up.payment_id AND up.is_paid=1 "), 
		array (2, NOT_PAID_MSG, " , " . $table_prefix . "users_payments up WHERE uc.payment_id=up.payment_id AND up.is_paid=0 ")
	);
	$commissions_total_today = 0; $commissions_total_yesterday = 0; $commissions_total_week = 0; $commissions_total_month = 0;
	// show commissions stats 
	for($i = 0; $i < sizeof($types); $i++) {
		$type_id = $types[$i][0];
		$type_name = $types[$i][1];
		$type_where = $types[$i][2];

		$t->set_var("type_id",   $type_id);
		$t->set_var("type_name", $type_name);

		$sql  = " SELECT SUM(uc.commission_action * uc.commission_amount) FROM " . $table_prefix . "users_commissions uc ";
		$sql .= $type_where;
		$sql .= " AND uc.date_added>=" . $db->tosql($today_ts, DATE);
		$commissions_today = get_db_value($sql);
		$commissions_total_today += $commissions_today;
		if ($commissions_today > 0) {
			$commissions_today = "<a href=\"admin_user_commissions.php?s_st=".$type_id."&s_sd=".$today_date."&s_ed=".$today_date."\"><b>" . currency_format($commissions_today) ."</b></a>";
		}

		$sql  = " SELECT SUM(uc.commission_action * uc.commission_amount) FROM " . $table_prefix . "users_commissions uc ";
		$sql .= $type_where;
		$sql .= " AND uc.date_added>=" . $db->tosql($yesterday_ts, DATE) . " AND uc.date_added<" . $db->tosql($today_ts, DATE);
		$commissions_yesterday = get_db_value($sql);
		$commissions_total_yesterday += $commissions_yesterday;
		if($commissions_yesterday > 0) {
			$commissions_yesterday = "<a href=\"admin_user_commissions.php?s_st=".$type_id."&s_sd=".$yesterday_date."&s_ed=".$yesterday_date."\"><b>" . currency_format($commissions_yesterday) ."</b></a>";
		}

		$sql  = " SELECT SUM(uc.commission_action * uc.commission_amount) FROM " . $table_prefix . "users_commissions uc ";
		$sql .= $type_where;
		$sql .= " AND uc.date_added>=" . $db->tosql($week_ts, DATE);
		$commissions_week = get_db_value($sql);
		$commissions_total_week += $commissions_week;
		if($commissions_week > 0) {
			$commissions_week = "<a href=\"admin_user_commissions.php?s_st=".$type_id."&s_sd=".$week_date."&s_ed=".$today_date."\"><b>" . currency_format($commissions_week) ."</b></a>";
		}

		$sql  = " SELECT SUM(uc.commission_action * uc.commission_amount) FROM " . $table_prefix . "users_commissions uc ";
		$sql .= $type_where;
		$sql .= " AND uc.date_added>=" . $db->tosql($month_ts, DATE);
		$commissions_month = get_db_value($sql);
		$commissions_total_month += $commissions_month;
		if ($commissions_month > 0) {
			$commissions_month = "<a href=\"admin_user_commissions.php?s_st=".$type_id."&s_sd=".$month_date."&s_ed=".$today_date."\"><b>" . currency_format($commissions_month) ."</b></a>";
		}

		$t->set_var("commissions_today", $commissions_today);
		$t->set_var("commissions_yesterday", $commissions_yesterday);
		$t->set_var("commissions_week", $commissions_week);
		$t->set_var("commissions_month", $commissions_month);

		$t->parse("types_stats", true);
	}
	$t->set_var("commissions_total_today", currency_format($commissions_total_today));
	$t->set_var("commissions_total_yesterday", currency_format($commissions_total_yesterday));
	$t->set_var("commissions_total_week", currency_format($commissions_total_week));
	$t->set_var("commissions_total_month", currency_format($commissions_total_month));

	$payment_statuses = array(array("", ALL_MSG), array("0", NEW_MSG), array("1", PAID_MSG), array("2", NOT_PAID_MSG));

	$r = new VA_Record($table_prefix . "users_commissions");
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
			$where .= " commission_total>=" . $db->tosql($r->get_value("s_min"), NUMBER);
		}

		if(!$r->is_empty("s_max")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " commission_total<=" . $db->tosql($r->get_value("s_max"), NUMBER);
		}

		if(!$r->is_empty("s_sd")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " uc.date_added>=" . $db->tosql($r->get_value("s_sd"), DATE);
		}

		if(!$r->is_empty("s_ed")) {
			if (strlen($where)) { $where .= " AND "; }
			$end_date = $r->get_value("s_ed");
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$where .= " uc.date_added<" . $db->tosql($day_after_end, DATE);
		}

		if(!$r->is_empty("s_st")) {
			if (strlen($where)) { $where .= " AND "; }
			$s_st = $r->get_value("s_st");
			if ($s_st == 1) {
				$where .= " up.is_paid=1 ";
			} else if ($s_st == 2) {
				$where .= " up.is_paid=0 ";
			} else {
				$where .= " uc.payment_id=0 ";
			}
		}

	}
	$where_sql = ""; 
	if (strlen($where)) {
		$where_sql = " WHERE " . $where;
	}

	// set up variables for navigator
	$total_records = 0;
	$sql  = " SELECT COUNT(*) ";
	$sql .= " FROM ((" . $table_prefix . "users_commissions uc ";
	$sql .= " LEFT JOIN " . $table_prefix . "users_payments up ON uc.payment_id=up.payment_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON u.user_id=uc.user_id) ";
	$sql .= $where_sql;
	$sql .= " GROUP BY uc.payment_id, uc.user_id ";
	//$db->query($sql);
	while ($db->next_record()) {
		$total_records++;
	}

	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT SUM(uc.commission_action * uc.commission_amount) AS commission_total, ";
	$sql .= " MIN(uc.date_added) AS from_date, MAX(uc.date_added) AS to_date, ";
	$sql .= " uc.payment_id, up.is_paid, u.user_id, u.login, u.name, u.first_name, u.last_name ";
	$sql .= " FROM ((" . $table_prefix . "users_commissions uc ";
	$sql .= " LEFT JOIN " . $table_prefix . "users_payments up ON uc.payment_id=up.payment_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON u.user_id=uc.user_id) ";
	$sql .= $where_sql;
	$sql .= " GROUP BY uc.payment_id, up.is_paid, u.user_id, u.login, u.name, u.first_name, u.last_name";

	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		$commission_index = 0;
		do {
			$commission_total = $db->f("commission_total");
			$payment_id = $db->f("payment_id");
			$user_id = $db->f("user_id");
			$user_name = $db->f("name");
			if(!strlen($user_name)) {
				$user_name = $db->f("first_name") . " " . $db->f("last_name");
			}
			if(!strlen($user_name)) {
				$user_name = $db->f("login");
			}
			$user_name .= " (id: " . $user_id . ")";
			if ($payment_id) {
				$commissions_operation = VIEW_PAYMENT_MSG;
				$commissions_operation_url = "admin_user_payment.php?payment_id=" . $payment_id;
				$status = ($db->f("is_paid") == 1) ? "<font color=green>Paid</font>" : "<font color=red>Not Paid</font>";
			} else {
				$commissions_operation = CREATE_PAYMENT_MSG;
				$commissions_operation_url = "admin_user_payment.php?payment_user_id=" . $user_id;
				$status = "<font color=blue>".NEW_MSG."</font>";
			}
			$from_date = $db->f("from_date", DATETIME);
			$to_date = $db->f("to_date", DATETIME);

			$commission_index++;
			$row_style = ($commission_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);

			$t->set_var("from_date", va_date($datetime_show_format, $from_date));
			$t->set_var("to_date", va_date($datetime_show_format, $to_date));
			$t->set_var("name", htmlspecialchars($user_name));
			$t->set_var("commission_total", currency_format($commission_total));
			$t->set_var("status", $status);
			$t->set_var("commissions_operation", $commissions_operation);
			$t->set_var("commissions_operation_url", $commissions_operation_url);

			$t->parse("records", true);
		} while($db->next_record());

		$t->parse("sorters", false);
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->set_var("s_st_search", $r->get_value("s_st"));

	$t->pparse("main");

?>