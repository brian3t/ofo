<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_credits.php                                   ***
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

	check_admin_security("site_users");

	$permissions = get_permissions();
	$user_id = get_param("user_id");

	$sql  = " SELECT login,password,name,first_name,last_name FROM " . $table_prefix . "users ";
	$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
	$db->query($sql);
	if($db->next_record()) {
		$login = $db->f("login");
		$current_password = $db->f("password");
		$user_name = $db->f("name");
		if (!$user_name) {
			$user_name = trim($db->f("first_name") . " " . $db->f("last_name"));
		}
		if (!$user_name) {
			$user_name = $login;
		}
	} else {
		header("Location: admin_users.php");
		exit;
	}

	$current_date = va_time();
	$operation = get_param("operation");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_user_credits.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_order_href", $order_details_site_url . "admin_order.php");
	$t->set_var("admin_user_href", "admin_user.php");
	$t->set_var("admin_users_href", "admin_users.php");
	$t->set_var("admin_user_credits_href", "admin_user_credits.php");

	// set transfer parameters
	$t->set_var("user_id", $user_id);
	$t->set_var("user_name", htmlspecialchars($user_name));


	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_user_credits.php", "credits_sort");
	$s->set_parameters(false, true, true, false);
	$s->set_default_sorting(3, "desc");
	$s->set_sorter(ORDER_NUMBER_MSG, "sorter_order_id", "1", "order_id");
	$s->set_sorter(CREDITS_MSG, "sorter_credit_amount", "2", "credit_amount");
	$s->set_sorter(DATE_ADDED_MSG, "sorter_date_added", "3", "date_added");
	$s->set_sorter(TYPE_MSG, "sorter_credit_type", "4", "credit_type");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_user_credits.php");

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
		array (1, SALES_MSG, " WHERE credit_type=1 "), 
		array (2, REWARD_CREDITS_MSG, " WHERE credit_type=2 "), 
		array (4, AD_CREDITS_MSG, " WHERE credit_type=4 "),
		array (3, OTHER_MSG, " WHERE credit_type=3 ")
	);
	$credits_total_today = 0; $credits_total_yesterday = 0; $credits_total_week = 0; $credits_total_month = 0;

	$user_where = " AND user_id=" . $db->tosql($user_id, INTEGER);
	// show credits stats 
	for($i = 0; $i < sizeof($types); $i++) {
		$type_id = $types[$i][0];
		$type_name = $types[$i][1];
		$type_where = $types[$i][2];

		$t->set_var("type_id",   $type_id);
		$t->set_var("type_name", $type_name);

		$sql  = " SELECT SUM(up.credit_action * up.credit_amount) FROM " . $table_prefix . "users_credits up ";
		$sql .= $type_where . $user_where;
		$sql .= " AND up.date_added>=" . $db->tosql($today_ts, DATE);
		$credits_today = get_db_value($sql);
		$credits_total_today += $credits_today;
		if ($credits_today != 0) {
			$credits_today = "<a href=\"admin_user_credits.php?user_id=".$user_id."&credits_tp=".$type_id."&credits_sd=".$today_date."&credits_ed=".$today_date."\"><b>" . currency_format($credits_today) . "</b></a>";
		}

		$sql  = " SELECT SUM(up.credit_action * up.credit_amount) FROM " . $table_prefix . "users_credits up ";
		$sql .= $type_where . $user_where;
		$sql .= " AND up.date_added>=" . $db->tosql($yesterday_ts, DATE) . " AND up.date_added<" . $db->tosql($today_ts, DATE);
		$credits_yesterday = get_db_value($sql);
		$credits_total_yesterday += $credits_yesterday;
		if($credits_yesterday != 0) {
			$credits_yesterday = "<a href=\"admin_user_credits.php?user_id=".$user_id."&credits_tp=".$type_id."&credits_sd=".$yesterday_date."&credits_ed=".$yesterday_date."\"><b>" . currency_format($credits_yesterday) ."</b></a>";
		}

		$sql  = " SELECT SUM(up.credit_action * up.credit_amount) FROM " . $table_prefix . "users_credits up ";
		$sql .= $type_where . $user_where;
		$sql .= " AND up.date_added>=" . $db->tosql($week_ts, DATE);
		$credits_week = get_db_value($sql);
		$credits_total_week += $credits_week;
		if($credits_week != 0) {
			$credits_week = "<a href=\"admin_user_credits.php?user_id=".$user_id."&credits_tp=".$type_id."&credits_sd=".$week_date."&credits_ed=".$today_date."\"><b>" . currency_format($credits_week) ."</b></a>";
		}

		$sql  = " SELECT SUM(up.credit_action * up.credit_amount) FROM " . $table_prefix . "users_credits up ";
		$sql .= $type_where . $user_where;
		$sql .= " AND up.date_added>=" . $db->tosql($month_ts, DATE);
		$credits_month = get_db_value($sql);
		$credits_total_month += $credits_month;
		if ($credits_month != 0) {
			$credits_month = "<a href=\"admin_user_credits.php?user_id=".$user_id."&credits_tp=".$type_id."&credits_sd=".$month_date."&credits_ed=".$today_date."\"><b>" . currency_format($credits_month) ."</b></a>";
		}

		$t->set_var("credits_today", $credits_today);
		$t->set_var("credits_yesterday", $credits_yesterday);
		$t->set_var("credits_week", $credits_week);
		$t->set_var("credits_month", $credits_month);

		$t->parse("types_stats", true);
	}
	$t->set_var("credits_total_today", currency_format($credits_total_today));
	$t->set_var("credits_total_yesterday", currency_format($credits_total_yesterday));
	$t->set_var("credits_total_week", currency_format($credits_total_week));
	$t->set_var("credits_total_month", currency_format($credits_total_month));

	$credit_types = array(
		array("", ALL_MSG), array("1", SALES_MSG), 
		array("2", REWARD_CREDITS_MSG), array("4", AD_CREDITS_MSG), array("3", OTHER_MSG)
	);


	$r = new VA_Record($table_prefix . "users_credits");
	$r->add_textbox("credits_sd", DATE, FROM_DATE_MSG);
	$r->change_property("credits_sd", VALUE_MASK, $date_edit_format);
	$r->change_property("credits_sd", TRIM, true);
	$r->add_textbox("credits_ed", DATE, END_DATE_MSG);
	$r->change_property("credits_ed", VALUE_MASK, $date_edit_format);
	$r->change_property("credits_ed", TRIM, true);
	$r->add_select("credits_tp", TEXT, $credit_types);
	$r->get_form_parameters();
	$r->validate();
	$r->set_form_parameters();

	$where = " user_id=" . $db->tosql($user_id, INTEGER);
	$product_search = false;

	if(!$r->errors) {
		if(!$r->is_empty("credits_sd")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " up.date_added>=" . $db->tosql($r->get_value("credits_sd"), DATE);
		}

		if(!$r->is_empty("credits_ed")) {
			if (strlen($where)) { $where .= " AND "; }
			$end_date = $r->get_value("credits_ed");
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$where .= " up.date_added<" . $db->tosql($day_after_end, DATE);
		}

		if(!$r->is_empty("credits_tp")) {
			if (strlen($where)) { $where .= " AND "; }
			$credits_tp = $r->get_value("credits_tp");
			$where .= " up.credit_type=" . $db->tosql($r->get_value("credits_tp"), INTEGER);
		}
	}

	$where_sql = ""; 
	if (strlen($where)) {
		$where_sql = " WHERE " . $where;
	}

	$admin_user_credit = new VA_URL("admin_user_credit.php", false);
	$admin_user_credit->add_parameter("s_ne", REQUEST, "s_ne");
	$admin_user_credit->add_parameter("s_ad", REQUEST, "s_ad");
	$admin_user_credit->add_parameter("s_sd", REQUEST, "s_sd");
	$admin_user_credit->add_parameter("s_ed", REQUEST, "s_ed");
	$admin_user_credit->add_parameter("s_ut", REQUEST, "s_ut");
	$admin_user_credit->add_parameter("s_ap", REQUEST, "s_ap");
	$admin_user_credit->add_parameter("page", REQUEST, "page");
	$admin_user_credit->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_user_credit->add_parameter("sort_dir", REQUEST, "sort_dir");
	$admin_user_credit->add_parameter("user_id", REQUEST, "user_id");
	$admin_user_credit->add_parameter("credits_sd", REQUEST, "credits_sd");
	$admin_user_credit->add_parameter("credits_ed", REQUEST, "credits_ed");
	$admin_user_credit->add_parameter("credits_tp", REQUEST, "credits_tp");
	$admin_user_credit->add_parameter("credits_page", REQUEST, "credits_page");
	$admin_user_credit->add_parameter("credits_sort_ord", REQUEST, "credits_sort_ord");
	$admin_user_credit->add_parameter("credits_sort_dir", REQUEST, "credits_sort_dir");

	$t->set_var("admin_user_credit_new_url", $admin_user_credit->get_url());

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) ";
	$sql .= " FROM " . $table_prefix . "users_credits up ";
	$sql .= $where_sql;
	$total_records = get_db_value($sql);

	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "credits_page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT credit_id, order_id, credit_type, credit_action, credit_amount, date_added ";
	$sql .= " FROM " . $table_prefix . "users_credits up ";
	$sql .= $where_sql;

	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$admin_user_credit->add_parameter("credit_id", DB, "credit_id");

		$t->set_var("no_records", "");
		$credits_index = 0;
		do {
			$order_id= $db->f("order_id");
			$credit_action = $db->f("credit_action");
			$credit_amount = $db->f("credit_amount");
			$user_id = $db->f("user_id");
			$credit_type = $db->f("credit_type");
			$date_added = $db->f("date_added", DATETIME);

			$credits_index++;
			$row_style = ($credits_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);

			if ($order_id) {
				$t->set_var("order_id", $order_id);
				$t->parse("order_link", false);
			} else {
				$t->set_var("order_link", "");
			}
			if ($credit_action < 0) {
				$t->set_var("credit_amount", "-" . currency_format($credit_amount));
			} else {
				$t->set_var("credit_amount", currency_format($credit_amount));
			}
			$t->set_var("date_added", va_date($datetime_show_format, $date_added));
			$t->set_var("credit_type", get_array_value($credit_type, $credit_types));
			$t->set_var("admin_user_credit_url", $admin_user_credit->get_url());

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

	$t->pparse("main");

?>