<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_points.php                                    ***
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
	$points_decimals = get_setting_value($settings, "points_decimals", 0);
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
	$t->set_file("main","admin_user_points.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_order_href", $order_details_site_url . "admin_order.php");
	$t->set_var("admin_user_href", "admin_user.php");
	$t->set_var("admin_users_href", "admin_users.php");
	$t->set_var("admin_user_points_href", "admin_user_points.php");

	// set transfer parameters
	$t->set_var("user_id", $user_id);
	$t->set_var("user_name", htmlspecialchars($user_name));


	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_user_points.php", "points_sort");
	$s->set_parameters(false, true, true, false);
	$s->set_default_sorting(2, "desc");
	$s->set_sorter(ORDER_NUMBER_MSG, "sorter_order_id", "1", "order_id");
	$s->set_sorter(DATE_ADDED_MSG, "sorter_date_added", "2", "date_added");
	$s->set_sorter(POINTS_MSG, "sorter_points_amount", "3", "points_amount");
	$s->set_sorter(TYPE_MSG, "sorter_points_type", "4", "points_type");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_user_points.php");

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
		array (1, SALES_MSG, " WHERE points_type=1 "), 
		array (2, REWARD_POINTS_MSG, " WHERE points_type=2 "), 
		array (3, OTHER_MSG, " WHERE points_type=3 ")
	);
	$points_total_today = 0; $points_total_yesterday = 0; $points_total_week = 0; $points_total_month = 0;

	$user_where = " AND user_id=" . $db->tosql($user_id, INTEGER);
	// show points stats 
	for($i = 0; $i < sizeof($types); $i++) {
		$type_id = $types[$i][0];
		$type_name = $types[$i][1];
		$type_where = $types[$i][2];

		$t->set_var("type_id",   $type_id);
		$t->set_var("type_name", $type_name);

		$sql  = " SELECT SUM(up.points_action * up.points_amount) FROM " . $table_prefix . "users_points up ";
		$sql .= $type_where . $user_where;
		$sql .= " AND up.date_added>=" . $db->tosql($today_ts, DATE);
		$points_today = get_db_value($sql);
		$points_total_today += $points_today;
		if ($points_today != 0) {
			$points_today = "<a href=\"admin_user_points.php?user_id=".$user_id."&points_tp=".$type_id."&points_sd=".$today_date."&points_ed=".$today_date."\"><b>" . number_format($points_today, $points_decimals) . "</b></a>";
		}

		$sql  = " SELECT SUM(up.points_action * up.points_amount) FROM " . $table_prefix . "users_points up ";
		$sql .= $type_where . $user_where;
		$sql .= " AND up.date_added>=" . $db->tosql($yesterday_ts, DATE) . " AND up.date_added<" . $db->tosql($today_ts, DATE);
		$points_yesterday = get_db_value($sql);
		$points_total_yesterday += $points_yesterday;
		if($points_yesterday != 0) {
			$points_yesterday = "<a href=\"admin_user_points.php?user_id=".$user_id."&points_tp=".$type_id."&points_sd=".$yesterday_date."&points_ed=".$yesterday_date."\"><b>" . number_format($points_yesterday, $points_decimals) ."</b></a>";
		}

		$sql  = " SELECT SUM(up.points_action * up.points_amount) FROM " . $table_prefix . "users_points up ";
		$sql .= $type_where . $user_where;
		$sql .= " AND up.date_added>=" . $db->tosql($week_ts, DATE);
		$points_week = get_db_value($sql);
		$points_total_week += $points_week;
		if($points_week != 0) {
			$points_week = "<a href=\"admin_user_points.php?user_id=".$user_id."&points_tp=".$type_id."&points_sd=".$week_date."&points_ed=".$today_date."\"><b>" . number_format($points_week, $points_decimals) ."</b></a>";
		}

		$sql  = " SELECT SUM(up.points_action * up.points_amount) FROM " . $table_prefix . "users_points up ";
		$sql .= $type_where . $user_where;
		$sql .= " AND up.date_added>=" . $db->tosql($month_ts, DATE);
		$points_month = get_db_value($sql);
		$points_total_month += $points_month;
		if ($points_month != 0) {
			$points_month = "<a href=\"admin_user_points.php?user_id=".$user_id."&points_tp=".$type_id."&points_sd=".$month_date."&points_ed=".$today_date."\"><b>" . number_format($points_month, $points_decimals) ."</b></a>";
		}

		$t->set_var("points_today", $points_today);
		$t->set_var("points_yesterday", $points_yesterday);
		$t->set_var("points_week", $points_week);
		$t->set_var("points_month", $points_month);

		$t->parse("types_stats", true);
	}
	$t->set_var("points_total_today", number_format($points_total_today, $points_decimals));
	$t->set_var("points_total_yesterday", number_format($points_total_yesterday, $points_decimals));
	$t->set_var("points_total_week", number_format($points_total_week, $points_decimals));
	$t->set_var("points_total_month", number_format($points_total_month, $points_decimals));

	$payment_statuses = array(array("", ALL_MSG), array("1", SALES_MSG), array("2", REWARD_POINTS_MSG), array("3", OTHER_MSG));

	$r = new VA_Record($table_prefix . "users_points");
	$r->add_textbox("points_sd", DATE, FROM_DATE_MSG);
	$r->change_property("points_sd", VALUE_MASK, $date_edit_format);
	$r->change_property("points_sd", TRIM, true);
	$r->add_textbox("points_ed", DATE, END_DATE_MSG);
	$r->change_property("points_ed", VALUE_MASK, $date_edit_format);
	$r->change_property("points_ed", TRIM, true);
	$r->add_select("points_tp", TEXT, $payment_statuses);
	$r->get_form_parameters();
	$r->validate();
	$r->set_form_parameters();

	$where = " user_id=" . $db->tosql($user_id, INTEGER);
	$product_search = false;

	if(!$r->errors) {
		if(!$r->is_empty("points_sd")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " up.date_added>=" . $db->tosql($r->get_value("points_sd"), DATE);
		}

		if(!$r->is_empty("points_ed")) {
			if (strlen($where)) { $where .= " AND "; }
			$end_date = $r->get_value("points_ed");
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$where .= " up.date_added<" . $db->tosql($day_after_end, DATE);
		}

		if(!$r->is_empty("points_tp")) {
			if (strlen($where)) { $where .= " AND "; }
			$points_tp = $r->get_value("points_tp");
			$where .= " up.points_type=" . $db->tosql($r->get_value("points_tp"), INTEGER);
		}
	}
	$where_sql = ""; 
	if (strlen($where)) {
		$where_sql = " WHERE " . $where;
	}

	$admin_user_point = new VA_URL("admin_user_point.php", false);
	$admin_user_point->add_parameter("s_ne", REQUEST, "s_ne");
	$admin_user_point->add_parameter("s_ad", REQUEST, "s_ad");
	$admin_user_point->add_parameter("s_sd", REQUEST, "s_sd");
	$admin_user_point->add_parameter("s_ed", REQUEST, "s_ed");
	$admin_user_point->add_parameter("s_ut", REQUEST, "s_ut");
	$admin_user_point->add_parameter("s_ap", REQUEST, "s_ap");
	$admin_user_point->add_parameter("page", REQUEST, "page");
	$admin_user_point->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_user_point->add_parameter("sort_dir", REQUEST, "sort_dir");
	$admin_user_point->add_parameter("user_id", REQUEST, "user_id");
	$admin_user_point->add_parameter("points_sd", REQUEST, "points_sd");
	$admin_user_point->add_parameter("points_ed", REQUEST, "points_ed");
	$admin_user_point->add_parameter("points_tp", REQUEST, "points_tp");
	$admin_user_point->add_parameter("points_page", REQUEST, "points_page");
	$admin_user_point->add_parameter("points_sort_ord", REQUEST, "points_sort_ord");
	$admin_user_point->add_parameter("points_sort_dir", REQUEST, "points_sort_dir");

	$t->set_var("admin_user_point_new_url", $admin_user_point->get_url());

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) ";
	$sql .= " FROM " . $table_prefix . "users_points up ";
	$sql .= $where_sql;
	$total_records = get_db_value($sql);

	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "points_page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT points_id, order_id, (points_action * up.points_amount) AS points_total, ";
	$sql .= " points_type, up.date_added ";
	$sql .= " FROM " . $table_prefix . "users_points up ";
	$sql .= $where_sql;

	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$admin_user_point->add_parameter("points_id", DB, "points_id");

		$t->set_var("no_records", "");
		$points_index = 0;
		do {
			$order_id= $db->f("order_id");
			$points_total = $db->f("points_total");
			$user_id = $db->f("user_id");
			$points_type = $db->f("points_type");
			$date_added = $db->f("date_added", DATETIME);

			$points_index++;
			$row_style = ($points_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);

			if ($order_id) {
				$t->set_var("order_id", $order_id);
				$t->parse("order_link", false);
			} else {
				$t->set_var("order_link", "");
			}
			$t->set_var("points_amount", number_format($points_total, $points_decimals));
			$t->set_var("date_added", va_date($datetime_show_format, $date_added));
			$t->set_var("points_type", get_array_value($points_type, $payment_statuses));
			$t->set_var("admin_user_point_url", $admin_user_point->get_url());

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