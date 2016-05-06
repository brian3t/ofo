<?php

	check_user_security("affiliate_sales");

	$t->set_file("block_body","block_user_affiliate_sales.html");
	$t->set_var("user_products_href",  get_custom_friendly_url("user_products.php"));
	$t->set_var("user_product_href",   get_custom_friendly_url("user_product.php"));
	$t->set_var("user_home_href",      get_custom_friendly_url("user_home.php"));
	$t->set_var("user_affiliate_sales_href",get_custom_friendly_url("user_affiliate_sales.php"));
	$t->set_var("user_affiliate_items_href",get_custom_friendly_url("user_affiliate_items.php"));

	// prepare list values 
	$date_show_format_custom = array("D", " ", "MMM", " ", "YYYY");
	$groupby = array(array("1", YEAR_MSG), array("2", MONTH_MSG), array("3", WEEK_MSG), array("4", DAY_MSG));
	$periods = array(array("", ""), array("1", TODAY_MSG), array("2", YESTERDAY_MSG), array("3", LAST_7DAYS_MSG), array("4", THIS_MONTH_MSG), array("5", LAST_MONTH_MSG), array("6", THIS_QUARTER_MSG), array("7", THIS_YEAR_MSG));
	$order_statuses = get_db_values("SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE show_for_user=1 AND is_active=1 ORDER BY status_order ", array(array("", "")));

	// prepare dates for stats
	$current_date = va_time();
	$cyear = $current_date[YEAR]; 
	$cmonth = $current_date[MONTH]; 
	$cday = $current_date[DAY]; 
	$today_ts = mktime(0, 0, 0, $cmonth, $cday, $cyear);
	$tomorrow_ts = mktime(0, 0, 0, $cmonth, $cday + 1, $cyear);
	$yesterday_ts = mktime(0, 0, 0, $cmonth, $cday - 1, $cyear);
	$week_ts = mktime(0, 0, 0, $cmonth, $cday - 6, $cyear);
	$month_ts = mktime(0, 0, 0, $cmonth, 1, $cyear);
	$last_month_start_ts = mktime(0, 0, 0, $cmonth - 1, 1, $cyear);
	$last_month_end_ts = mktime(0, 0, 0, $cmonth, 0, $cyear);
	$quarter_ts = mktime(0, 0, 0, intval(($cmonth - 1) / 3) * 3 + 1, 1, $cyear);
	$year_ts = mktime(0, 0, 0, 1, 1, $cyear);
	$today_date = va_date($date_edit_format, $today_ts);
	$tomorrow_date = va_date($date_edit_format, $tomorrow_ts);
	$yesterday_date = va_date($date_edit_format, $yesterday_ts);
	$week_start_date = va_date($date_edit_format, $week_ts);
	$month_start_date = va_date($date_edit_format, $month_ts);
	$last_month_start_date = va_date($date_edit_format, $last_month_start_ts);
	$last_month_end_date = va_date($date_edit_format, $last_month_end_ts);
	$quarter_start_date = va_date($date_edit_format, $quarter_ts);
	$year_start_date = va_date($date_edit_format, $year_ts);

	$t->set_var("date_edit_format", join("", $date_edit_format));
	$t->set_var("today_date", $today_date);
	$t->set_var("yesterday_date", $yesterday_date);
	$t->set_var("week_start_date", $week_start_date);
	$t->set_var("month_start_date", $month_start_date);
	$t->set_var("last_month_start_date", $last_month_start_date);
	$t->set_var("last_month_end_date", $last_month_end_date);
	$t->set_var("quarter_start_date", $quarter_start_date);
	$t->set_var("year_start_date", $year_start_date);


	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", get_custom_friendly_url("user_affiliate_sales.php"));
	$s->set_parameters(false, true, true, false);
	if ($db_type == "access" || $db_type == "postgre") {
		$t->set_var("sorter_time", TIME_PERIOD_MSG);
		$s->set_sorter(ITEMS_NUMBER_COLUMN, "sorter_products_qty", "2", "SUM(oi.quantity)");
		$s->set_sorter(GROSS_SALES_MSG, "sorter_sales", "3", "SUM(oi.price * oi.quantity)");
		$s->set_sorter(COMMISSIONS_MSG, "sorter_commissions", "4", "SUM(oi.affiliate_commission * oi.quantity)");
	} elseif ($db_type == "mysql") {
		$s->set_default_sorting(1, "asc");
		$s->set_sorter(TIME_PERIOD_MSG, "sorter_time", "1", "order_placed_date");
		$s->set_sorter(ITEMS_NUMBER_COLUMN, "sorter_products_qty", "2", "products_qty");
		$s->set_sorter(GROSS_SALES_MSG, "sorter_sales", "3", "sales");
		$s->set_sorter(COMMISSIONS_MSG, "sorter_commissions", "4", "commissions");
	}


	$r = new VA_Record("");
	$r->add_hidden("s_form", INTEGER);
	$r->add_select("s_gr", INTEGER, $groupby);
	$r->add_select("s_tp", INTEGER, $periods);
	$r->add_textbox("s_sd", DATE, "From Date");
	$r->change_property("s_sd", VALUE_MASK, $date_edit_format);
	$r->add_textbox("s_ed", DATE, "End Date");
	$r->change_property("s_ed", VALUE_MASK, $date_edit_format);
	$r->add_select("s_os", INTEGER, $order_statuses);
	$r->get_form_parameters();
	$r->validate();
	if (!($r->get_value("s_form"))) {
		$r->set_value("s_gr", 2);
		if ($r->is_empty("s_sd") && $r->is_empty("s_ed")) {
			$r->set_value("s_tp", 7);
			$r->set_value("s_sd", va_time($year_ts));
			$r->set_value("s_ed", va_time($today_ts));
		}
	}
	$r->set_form_parameters();
	$s_gr = $r->get_value("s_gr");
	
	if (!strlen(get_param("filter")) && $r->get_value("s_form")) {
		$t->set_var("search_results", "");
		$t->parse("block_body", false);
		$t->parse($block_name, true);
		exit;
	}

	$sum_products_qty = 0;
	$sum_sales = 0; $sum_commissions = 0;

	$where = ""; 
	if (!$r->errors) 
	{
		if (!$r->is_empty("s_sd")) {
			$where .= " AND o.order_placed_date >= " . $db->tosql($r->get_value("s_sd"), DATE);
		}
		if (!$r->is_empty("s_ed")) {
			$end_date = $r->get_value("s_ed");
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$where .= " AND o.order_placed_date < " . $db->tosql($day_after_end, DATE);
		}
		if (!$r->is_empty("s_os")) {
			$where .= " AND o.order_status = " . $db->tosql($r->get_value("s_os"), INTEGER);
		}
	}

	$sql = "";
	if ($db_type == "access") {
		$sql = "SELECT Year(o.order_placed_date) AS year, ";
		switch ($s_gr) {
			case 2:
				$sql .= "Month(o.order_placed_date) AS month, ";
				break;
			case 3:
				$sql = " SELECT IIf(Month(o.order_placed_date) = 1 And DatePart('ww', o.order_placed_date, 1, 2) > 51, Year(o.order_placed_date) - 1, Year(o.order_placed_date)) AS year, DatePart('ww', o.order_placed_date, 1, 2) AS week,";
				break;
			case 4:
				$sql .= "Month(o.order_placed_date) AS month, Day(o.order_placed_date) AS day, ";
				break;
		}
	} elseif ($db_type == "mysql") {
		$sql = "SELECT YEAR(o.order_placed_date) AS year, ";
		switch ($s_gr) {
			case 2:
				$sql .= " MONTH(o.order_placed_date) AS month, ";
				break;
			case 3:
				$sql = " SELECT IF(WEEK(o.order_placed_date, 0) = 0, YEAR(o.order_placed_date) - 1, YEAR(o.order_placed_date)) AS year, IF(WEEK(o.order_placed_date, 0) = 0, WEEK(CONCAT(YEAR(o.order_placed_date) - 1, '-12-31')), WEEK(o.order_placed_date)) AS week, ";
				break;
			case 4:
				$sql .= " MONTH(o.order_placed_date) AS month, DAYOFMONTH(o.order_placed_date) AS day, ";
				break;
		}
	} else if ($db_type == "postgre") {
		$sql = "SELECT date_part('year', o.order_placed_date) AS year, ";
		switch ($s_gr) {
			case 2:
				$sql .= " date_part('month', o.order_placed_date) AS month, ";
				break;
			case 3:
				$sql = " SELECT CASE WHEN date_part('month', o.order_placed_date) = 1 AND date_part('week', o.order_placed_date) > 51 THEN date_part('year', o.order_placed_date) - 1 ELSE date_part('year', o.order_placed_date) END AS year, date_part('week', o.order_placed_date) AS week, ";
				break;
			case 4:
				$sql .= " date_part('month', o.order_placed_date) AS month, date_part('day', o.order_placed_date) AS day, ";
				break;
		}
	}
	$sql .= " SUM(oi.quantity) AS products_qty, ";
	$sql .= " SUM(oi.price * oi.quantity) AS sales, ";
	$sql .= " SUM(oi.affiliate_commission * oi.quantity) AS commissions  ";
	$sql .= " FROM ((" . $table_prefix . "orders o ";
	$sql .= " INNER JOIN " . $table_prefix . "orders_items oi ON o.order_id=oi.order_id) ";
	$sql .= " INNER JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
	$sql .= " WHERE oi.affiliate_user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$sql .= " AND os.show_for_user=1 ";
	$sql .= $where;
	$sql .= " GROUP BY ";
	if ($db_type == "access") {
		switch ($s_gr) {
			case 1:
				$sql .= " Year(o.order_placed_date)";
				break;
			case 2:
				$sql .= " Year(o.order_placed_date), Month(o.order_placed_date)";
				break;
			case 3:
				$sql .= " IIf(Month(o.order_placed_date) = 1 And DatePart('ww', o.order_placed_date, 1, 2) > 51, Year(o.order_placed_date) - 1, Year(o.order_placed_date)), DatePart('ww', o.order_placed_date, 1, 2)";
				break;
			case 4:
				$sql .= " Year(o.order_placed_date), Month(o.order_placed_date), Day(o.order_placed_date)";
				break;
		}
	} elseif ($db_type == "mysql" || $db_type == "postgre") {
		$sql .= " year";
		switch ($s_gr) {
			case 2:
				$sql .= ", month";
				break;
			case 3:
				$sql .= ", week";
				break;
			case 4:
				$sql .= ", month, day";
				break;
		}
	}
	$sql .= $s->order_by;
	$db->query($sql);
	if ($db->next_record()) {
		$order_index = 0;
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
			$order_index++;
			$year = intval($db->f("year"));
			if ($s_gr == 2) {
				$month = intval($db->f("month"));
			}
			if ($s_gr == 3) {
				$week = intval($db->f("week"));
			}
			else if ($s_gr == 4) {
				$month = intval($db->f("month"));
				$day = intval($db->f("day"));
			}
			$products_qty = intval($db->f("products_qty"));
			$sales = doubleval($db->f("sales"));
			$commissions = doubleval($db->f("commissions"));

			if ($s_gr == 1) {
				$t->set_var("time_period", $year . " " . YEAR_MSG);
			} elseif ($s_gr == 2) {
				$t->set_var("time_period", get_array_value($month, $months) . ", " . $year);
			} elseif ($s_gr == 3) {
				$t->set_var("time_period", $year . " " . YEAR_MSG . ", " . $week . " Week");
			} elseif ($s_gr == 4) {
				$t->set_var("time_period", va_date($date_show_format_custom, mktime(0, 0, 0, $month, $day, $year)));
			}
			$t->set_var("products_qty", $products_qty);
			$t->set_var("sales", currency_format($sales));
			$t->set_var("commissions", currency_format($commissions));

			$row_style = ($order_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);
			$t->set_var("order_index", $order_index);

			if ($s_gr == 1) {
				$sd_period_ts = mktime(0, 0, 0, 1, 1, $year); // year start timestamp
				$ed_period_ts = mktime(0, 0, 0, 1, 0, $year + 1); // year end timestamp
			} elseif ($s_gr == 2) {
				$sd_period_ts = mktime(0, 0, 0, $month, 1, $year); // month start timestamp
				$ed_period_ts = mktime(0, 0, 0, $month + 1, 0, $year); // month end timestamp
			} elseif ($s_gr == 3) {
				$year_start_weekday = date("w", mktime(0, 0, 0, 1, 1, $year));
				if ($db_type == "postgre") $year_start_weekday--; // in Postgre week always starts from Monday
				if ($year_start_weekday >= 4) {
					$day_number = 7 * ($week + 1) - $year_start_weekday;
				} else {
					$day_number = 7 * $week - $year_start_weekday;
				}
				$sd_period_ts = mktime(0, 0, 0, 1, $day_number - 6, $year); // week start timestamp
				$ed_period_ts = mktime(0, 0, 0, 1, $day_number, $year); // week end timestamp
			} elseif ($s_gr == 4) {
				$sd_period_ts = mktime(0, 0, 0, $month, $day, $year); // day start timestamp
				$ed_period_ts = $sd_period_ts; // day end timestamp
			}
			$t->set_var("s_sd_m", va_date($date_edit_format, $sd_period_ts));
			$t->set_var("s_ed_m", va_date($date_edit_format, $ed_period_ts));
			$t->set_var("s_os_m", $r->get_value("s_os"));
			
			$date_period_start = va_date($date_show_format_custom, $sd_period_ts);
			$date_period_end = va_date($date_show_format_custom, $ed_period_ts);
			$period_start_time = va_time($sd_period_ts);
			$period_end_time = va_time($ed_period_ts);
			
			if ($s_gr == 3) {
				if ($period_start_time[YEAR] == $period_end_time[YEAR]) {
					if ($period_start_time[MONTH] != $period_end_time[MONTH]) {
						$t->set_var("time_period", intval($period_start_time[DAY]) . " " . $short_months[intval($period_start_time[MONTH]) - 1][1] . " - " . $date_period_end);						
					} else{
						$t->set_var("time_period", intval($period_start_time[DAY]) . " - " . $date_period_end);
					}
				} else {
					$t->set_var("time_period", $date_period_start . " - " . $date_period_end);
				}
			}

			if (!$r->is_empty("s_sd")) {
				$start_date = $r->get_value("s_sd");
				$s_sd = va_date($date_show_format_custom, $start_date); // start date search param
				if ($s_sd != $date_period_start && $start_date[YEAR] == $year) {
					if ($s_gr == 1) {
						$t->set_var("time_period", $s_sd . " - " . $date_period_end);
						$t->set_var("s_sd_m", va_date($date_edit_format, $start_date));
					} elseif ($s_gr == 2 && $start_date[MONTH] == $month) {
						$t->set_var("time_period", intval($start_date[DAY]) . " - " . $date_period_end);
						$t->set_var("s_sd_m", va_date($date_edit_format, $start_date));
					}
				}
			}
			if (!$r->is_empty("s_ed")) {
				$end_date = $r->get_value("s_ed");
				$s_ed = va_date($date_show_format_custom, $end_date); // end date search param
				if ($s_ed != $date_period_end && $end_date[YEAR] == $year 
					&& (($s_gr == 1) || ($s_gr == 2 && $end_date[MONTH] == $month))) 
				{
					if ($s_gr == 1) {
						if ($period_start_time[MONTH] == $end_date[MONTH]) {
							$t->set_var("time_period", intval($period_start_time[DAY]) . " - " . $s_ed);
						} else {
							$t->set_var("time_period", $date_period_start . " - " . $s_ed);
						}
					} elseif ($s_gr == 2 && $end_date[MONTH] == $month) {
						$t->set_var("time_period", intval($period_start_time[DAY]) . " - " . $s_ed);
					}
					if (!$r->is_empty("s_sd")) {
						if ($s_sd != $date_period_start && $start_date[YEAR] == $year) {
							if ($s_gr == 1) {
								if ($start_date[MONTH] == $end_date[MONTH]) {
									$t->set_var("time_period", intval($start_date[DAY]) . " - " . $s_ed);
								} else {
									$t->set_var("time_period", $s_sd . " - " . $s_ed);
								}
							} elseif ($s_gr == 2 && $start_date[MONTH] == $month) {
								$t->set_var("time_period", intval($start_date[DAY]) . " - " . $s_ed);
							}
						}
					}
					$t->set_var("s_ed_m", va_date($date_edit_format, $end_date));
				}
			}

			$t->parse("records", true);

			$sum_products_qty += $products_qty;
			$sum_sales += $sales;
			$sum_commissions += $commissions;
		} while ($db->next_record());

		$t->set_var("sum_products_qty", $sum_products_qty);
		$t->set_var("sum_sales", currency_format($sum_sales));
		$t->set_var("sum_commissions", currency_format($sum_commissions));
		$t->parse("summary", false);
		$t->parse("summary_bottom", false);
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("summary", "");
		$t->set_var("summary_bottom", "");
		$t->parse("no_records", false);
	}
	
	$t->parse("search_results", false);

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>