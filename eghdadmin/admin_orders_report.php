<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_orders_report.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	
	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/sorter.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/shopping_cart.php");
	include_once ($root_folder_path . "includes/order_items.php");
	include_once ($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("orders_stats");

	$date_show_format_custom = array("D", " ", "MMM", " ", "YYYY");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_orders_report.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_orders_products_report_href", "admin_orders_products_report.php");
	$t->set_var("admin_orders_tax_report_href", "admin_orders_tax_report.php");

	// prepare list values
	$groupby = array(array("1", YEAR_MSG), array("2", MONTH_MSG), array("3", WEEK_MSG), array("4", DAY_MSG));
	$periods = array(array("", ""), array("1", TODAY_MSG), array("2", YESTERDAY_MSG), array("3", LAST_7DAYS_MSG), array("4", THIS_MONTH_MSG), array("5", LAST_MONTH_MSG), array("6", THIS_QUARTER_MSG), array("7", THIS_YEAR_MSG));
	$sql = "SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE is_active=1 ORDER BY status_order, status_id";
	$order_statuses = get_db_values($sql, array(array("", "")));
	$cc_default_types = array(array("", ""), array("blank", WITHOUT_CARD_TYPE_MSG));
	$credit_card_types = get_db_values("SELECT credit_card_id, credit_card_name FROM " . $table_prefix . "credit_cards ORDER BY credit_card_name", $cc_default_types);

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
	$yes_no_all = 
		array( 
			array(1, YES_MSG), array(0, NO_MSG), array("", ALL_MSG)
		);

	$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE (setting_type='order_recover') ";
	$recover_settings = array();
	if ($multisites_version) {
		if (isset($site_id) && ($site_id>1) )  {
			$sql .= "AND ( site_id=1 OR site_id = " . $db->tosql($site_id, INTEGER) ." ) ";
			$sql .= "ORDER BY site_id ASC ";
		} else {
			$sql .= "AND site_id=1 ";
		}
	}		
	$db->query($sql);
	while ($db->next_record()) {
		$recover_settings[$db->f("setting_name")] = $db->f("setting_value");
	}	
		
	$t->set_var("date_edit_format", join("", $date_edit_format));
	$t->set_var("today_date", $today_date);
	$t->set_var("yesterday_date", $yesterday_date);
	$t->set_var("week_start_date", $week_start_date);
	$t->set_var("month_start_date", $month_start_date);
	$t->set_var("last_month_start_date", $last_month_start_date);
	$t->set_var("last_month_end_date", $last_month_end_date);
	$t->set_var("quarter_start_date", $quarter_start_date);
	$t->set_var("year_start_date", $year_start_date);

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_orders_report.php");
	$s->set_parameters(false, true, true, false);
	if ($db_type == "access" || $db_type == "postgre") 
	{
		$t->set_var("sorter_time", PERIOD_MSG);
		$s->set_sorter("#".ORDERS_MSG, "sorter_orders_qty", "2", "COUNT(o.order_id)");
		$s->set_sorter("#".ADMIN_ITEMS_MSG, "sorter_products_qty", "3", "SUM(o.total_quantity)");
		$s->set_sorter(TOTAL_MSG, "sorter_sales", "4", "SUM(o.order_total)");
		$s->set_sorter(PROD_SHIPPING_MSG, "sorter_shipping", "5", "SUM(o.shipping_cost)");
		$s->set_sorter(TAX_RATES_MSG, "sorter_tax", "6", "SUM(o.tax_total)");
		$s->set_sorter(DISCOUNT_MSG, "sorter_discount", "7", "SUM(o.total_discount)");
		$s->set_sorter(ADMIN_GOODS_MSG, "sorter_goods", "8", "SUM(o.goods_total)");
		$s->set_sorter(BUYING_MSG, "sorter_buying", "9", "SUM(o.total_buying)");
		$s->set_sorter(ADMIN_MARGIN_MSG, "sorter_margin", "10", "SUM(o.goods_total - o.total_buying)");
	}
	elseif ($db_type == "mysql") 
	{
		$s->set_default_sorting(1, "asc");
		$s->set_sorter(PERIOD_MSG, "sorter_time", "1", "order_placed_date");
		$s->set_sorter("#".ORDERS_MSG, "sorter_orders_qty", "2", "orders_qty");
		$s->set_sorter("#".ADMIN_ITEMS_MSG, "sorter_products_qty", "3", "products_qty");
		$s->set_sorter(TOTAL_MSG, "sorter_sales", "4", "sales");
		$s->set_sorter(PROD_SHIPPING_MSG, "sorter_shipping", "5", "shipping");
		$s->set_sorter(TAX_RATES_MSG, "sorter_tax", "6", "tax");
		$s->set_sorter(DISCOUNT_MSG, "sorter_discount", "7", "discount");
		$s->set_sorter(ADMIN_GOODS_MSG, "sorter_goods", "8", "goods");
		$s->set_sorter(BUYING_MSG, "sorter_buying", "9", "buying");
		$s->set_sorter(ADMIN_MARGIN_MSG, "sorter_margin", "10", "margin");
	}

	$r = new VA_Record("");
	$r->add_hidden("s_form", INTEGER);
	$r->add_select("s_gr", INTEGER, $groupby);
	$r->add_select("s_tp", INTEGER, $periods);
	$r->add_textbox("s_sd", DATE, FROM_DATE_MSG);
	$r->change_property("s_sd", VALUE_MASK, $date_edit_format);
	$r->add_textbox("s_ed", DATE, END_DATE_MSG);
	$r->change_property("s_ed", VALUE_MASK, $date_edit_format);
	$r->add_select("s_os", INTEGER, $order_statuses);
	$r->add_select("s_cct", TEXT, $credit_card_types);
	$r->add_select("s_rs", TEXT, $yes_no_all);
	$r->change_property("s_rs", DEFAULT_VALUE, 0);
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
		$t->pparse("main");
		exit;
	}

	$sum_orders_qty = 0;
	$sum_products_qty = 0;
	$sum_goods = 0;
	$sum_buying = 0;
	$sum_sales = 0;
	$sum_tax = 0;
	$sum_discount= 0;
	$sum_shipping = 0;
	$sum_margin = 0;

	$where = ""; 
	if (!$r->errors) 
	{
		if (!$r->is_empty("s_sd")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " o.order_placed_date >= " . $db->tosql($r->get_value("s_sd"), DATE);
		}

		if (!$r->is_empty("s_ed")) {
			if (strlen($where)) { $where .= " AND "; }
			$end_date = $r->get_value("s_ed");
			$day_after_end = mktime(0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$where .= " o.order_placed_date < " . $db->tosql($day_after_end, DATE);
		}

		if (!$r->is_empty("s_os")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " o.order_status=" . $db->tosql($r->get_value("s_os"), INTEGER);
		}

		if (!$r->is_empty("s_cct")) {
			if (strlen($where)) { $where .= " AND "; }
			if ($r->get_value("s_cct") == "blank") {
				$where .= " o.cc_type IS NULL ";
			} else {
				$where .= " o.cc_type=" . $db->tosql($r->get_value("s_cct"), INTEGER);
			}
		}
		
		if (!$r->is_empty("s_rs")) {
			if (strlen($where)) { $where .= " AND "; }
			$email_live_days = get_setting_value($recover_settings, "email_live_days", 0);
			if ($email_live_days) {
				if ($r->get_value("s_rs")) {
					$where .= " o.is_reminder_send=1 " ;
					$where .= " AND o.reminder_send_date>=" . $db->tosql(time() - 60*60*24*$email_live_days, DATETIME) ;
				} else {
					$where .= " (o.is_reminder_send=0 " ;
					$where .= " OR o.reminder_send_date<" . $db->tosql(time() - 60*60*24*$email_live_days, DATETIME) . ")";				
				}
			} else {			
				$where .= " o.is_reminder_send=" . $db->tosql($r->get_value("s_rs"), INTEGER);
			}
		}
	}

	if(strlen($where)) {
		$where = " WHERE " . $where;
	}

	$sql = "";
	if ($db_type == "access") {
		$sql = "SELECT Year(o.order_placed_date) AS year, ";
		switch ($s_gr) {
			case 2:
				$sql .= "Month(o.order_placed_date) AS month, ";
				break;
			case 3:
				$sql = "SELECT IIf(Month(o.order_placed_date) = 1 And DatePart('ww', o.order_placed_date, 1, 2) > 51, Year(o.order_placed_date) - 1, Year(o.order_placed_date)) AS year, DatePart('ww', o.order_placed_date, 1, 2) AS week,";
				break;
			case 4:
				$sql .= "Month(o.order_placed_date) AS month, Day(o.order_placed_date) AS day, ";
				break;
		}
	}
	elseif ($db_type == "mysql") 
	{
		$sql = "SELECT YEAR(o.order_placed_date) AS year, ";
		switch ($s_gr) {
			case 2:
				$sql .= " MONTH(o.order_placed_date) AS month, ";
				break;
			case 3:
				$sql = "SELECT IF(WEEK(o.order_placed_date, 0) = 0, YEAR(o.order_placed_date) - 1, YEAR(o.order_placed_date)) AS year, IF(WEEK(o.order_placed_date, 0) = 0, WEEK(CONCAT(YEAR(o.order_placed_date) - 1, '-12-31')), WEEK(o.order_placed_date)) AS week, ";
				break;
			case 4:
				$sql .= " MONTH(o.order_placed_date) AS month, DAYOFMONTH(o.order_placed_date) AS day, ";
				break;
		}
	}
	elseif ($db_type == "postgre") 
	{
		$sql = "SELECT date_part('year', o.order_placed_date) AS year, ";
		switch ($s_gr) {
			case 2:
				$sql .= " date_part('month', o.order_placed_date) AS month, ";
				break;
			case 3:
				$sql = "SELECT CASE WHEN date_part('month', o.order_placed_date) = 1 AND date_part('week', o.order_placed_date) > 51 THEN date_part('year', o.order_placed_date) - 1 ELSE date_part('year', o.order_placed_date) END AS year, date_part('week', o.order_placed_date) AS week, ";
				break;
			case 4:
				$sql .= " date_part('month', o.order_placed_date) AS month, date_part('day', o.order_placed_date) AS day, ";
				break;
		}
	}
	elseif ($db_type == "db2") 
	{
		$sql = "SELECT Year(o.order_placed_date) AS year, ";
		switch ($s_gr) {
			case 2:
				$sql .= "Month(o.order_placed_date) AS month, ";
				break;
			case 3:
				$sql = "SELECT IIf(Month(o.order_placed_date) = 1 And DatePart('ww', o.order_placed_date, 1, 2) > 51, Year(o.order_placed_date) - 1, Year(o.order_placed_date)) AS year, DatePart('ww', o.order_placed_date, 1, 2) AS week,";
				break;
			case 4:
				$sql .= "Month(o.order_placed_date) AS month, Day(o.order_placed_date) AS day, ";
				break;
		}
	}
	$sql .= " COUNT(o.order_id) AS orders_qty, SUM(o.total_quantity) AS products_qty, SUM(o.goods_total) AS goods, ";
	$sql .= " SUM(o.order_total) AS sales, SUM(o.tax_total) AS tax, SUM(o.shipping_cost) AS shipping, ";
	$sql .= " SUM(o.total_discount) AS discount, SUM(o.total_buying) AS buying, ";
	$sql .= " SUM(o.goods_total - o.total_buying - o.total_discount) AS margin ";
	$sql .= " FROM " . $table_prefix . "orders o ";
	$sql .= $where;
	$sql .= " GROUP BY ";
	if ($db_type == "access" || $db_type == "db2") 
	{
		switch ($s_gr) {
			case 1:
				$sql .= "Year(o.order_placed_date)";
				break;
			case 2:
				$sql .= "Year(o.order_placed_date), Month(o.order_placed_date)";
				break;
			case 3:
				$sql .= "IIf(Month(o.order_placed_date) = 1 And DatePart('ww', o.order_placed_date, 1, 2) > 51, Year(o.order_placed_date) - 1, Year(o.order_placed_date)), DatePart('ww', o.order_placed_date, 1, 2)";
				break;
			case 4:
				$sql .= "Year(o.order_placed_date), Month(o.order_placed_date), Day(o.order_placed_date)";
				break;
		}
	}
	elseif ($db_type == "mysql" || $db_type == "postgre") 
	{
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
			} elseif ($s_gr == 4) {
				$month = intval($db->f("month"));
				$day = intval($db->f("day"));
			}
			$orders_qty = intval($db->f("orders_qty"));
			$products_qty = intval($db->f("products_qty"));
			$goods = doubleval($db->f("goods"));
			$sales = doubleval($db->f("sales"));
			$tax = doubleval($db->f("tax"));
			$discount = doubleval($db->f("discount"));
			$shipping = doubleval($db->f("shipping"));
			$buying = doubleval($db->f("buying"));
			$margin = doubleval($db->f("margin"));
			$margin_percent = ($goods != 0) ? number_format($margin / $goods * 100, 2) : 0;
			if ($s_gr == 1) {
				$t->set_var("time_period", $year . " " . YEAR_MSG);
			} elseif ($s_gr == 2) {
				$t->set_var("time_period", get_array_value($month, $months) . ", " . $year);
			} elseif ($s_gr == 3) {
				$t->set_var("time_period", $year . " " . YEAR_MSG . ", " . $week . WEEK_MSG);
			} elseif ($s_gr == 4) {
				$t->set_var("time_period", va_date($date_show_format_custom, mktime(0, 0, 0, $month, $day, $year)));
			}
			$t->set_var("orders_qty", $orders_qty);
			$t->set_var("products_qty", $products_qty);
			$t->set_var("goods", currency_format($goods));
			$t->set_var("sales", currency_format($sales));
			$t->set_var("tax", currency_format($tax));
			$t->set_var("discount", currency_format($discount));
			$t->set_var("shipping", currency_format($shipping));
			$t->set_var("buying", currency_format($buying));
			$t->set_var("margin", currency_format($margin));
			$t->set_var("margin_percent", $margin_percent);

			$row_style = ($order_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);
			$t->set_var("order_index", $order_index);

			if ($s_gr == 1) {
				$sd_period_ts = mktime(0, 0, 0, 1, 1, $year); // year start timestamp
				$ed_period_ts = mktime(0, 0, 0, 1, 0, $year + 1); // year end timestamp
			}
			else if ($s_gr == 2) {
				$sd_period_ts = mktime(0, 0, 0, $month, 1, $year); // month start timestamp
				$ed_period_ts = mktime(0, 0, 0, $month + 1, 0, $year); // month end timestamp
			}
			else if ($s_gr == 3) {
				$year_start_weekday = date("w", mktime(0, 0, 0, 1, 1, $year));
				if ($db_type == "postgre") $year_start_weekday--; // in Postgre week always starts from Monday
				if ($year_start_weekday >= 4) {
					$day_number = 7 * ($week + 1) - $year_start_weekday;
				}
				else {
					$day_number = 7 * $week - $year_start_weekday;
				}
				$sd_period_ts = mktime(0, 0, 0, 1, $day_number - 6, $year); // week start timestamp
				$ed_period_ts = mktime(0, 0, 0, 1, $day_number, $year); // week end timestamp
			}
			else if ($s_gr == 4) {
				$sd_period_ts = mktime(0, 0, 0, $month, $day, $year); // day start timestamp
				$ed_period_ts = $sd_period_ts; // day end timestamp
			}
			$t->set_var("s_sd_m", va_date($date_edit_format, $sd_period_ts));
			$t->set_var("s_ed_m", va_date($date_edit_format, $ed_period_ts));
			$t->set_var("s_os_m", $r->get_value("s_os"));
			$t->set_var("s_cct_m", $r->get_value("s_cct"));

			$date_period_start = va_date($date_show_format_custom, $sd_period_ts);
			$date_period_end = va_date($date_show_format_custom, $ed_period_ts);
			$period_start_time = va_time($sd_period_ts);
			$period_end_time = va_time($ed_period_ts);

			if ($s_gr == 3) {
				if ($period_start_time[YEAR] == $period_end_time[YEAR]) {
					if ($period_start_time[MONTH] != $period_end_time[MONTH]) {
						$t->set_var("time_period", intval($period_start_time[DAY]) . " " . $short_months[intval($period_start_time[MONTH]) - 1][1] . " - " . $date_period_end);
					}
					else{
						$t->set_var("time_period", intval($period_start_time[DAY]) . " - " . $date_period_end);
					}
				}
				else {
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
					}
					else if ($s_gr == 2 && $start_date[MONTH] == $month) {
						$t->set_var("time_period", intval($start_date[DAY]) . " - " . $date_period_end);
						$t->set_var("s_sd_m", va_date($date_edit_format, $start_date));
					}
				}
			}
			if (!$r->is_empty("s_ed")) {
				$end_date = $r->get_value("s_ed");
				$s_ed = va_date($date_show_format_custom, $end_date); // end date search param
				if ($s_ed != $date_period_end && $end_date[YEAR] == $year &&
					(($s_gr == 1) || ($s_gr == 2 && $end_date[MONTH] == $month)))
				{
					if ($s_gr == 1) {
						if ($period_start_time[MONTH] == $end_date[MONTH]) {
							$t->set_var("time_period", intval($period_start_time[DAY]) . " - " . $s_ed);
						}
						else {
							$t->set_var("time_period", $date_period_start . " - " . $s_ed);
						}
					}
					else if ($s_gr == 2 && $end_date[MONTH] == $month) {
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

			$sum_orders_qty += $orders_qty;
			$sum_products_qty += $products_qty;
			$sum_goods += $goods;
			$sum_buying += $buying;
			$sum_sales += $sales;
			$sum_tax += $tax;
			$sum_discount += $discount;
			$sum_shipping += $shipping;
			$sum_margin += $margin;
			$sum_margin_percent = ($sum_goods != 0) ? number_format($sum_margin / $sum_goods * 100, 2) : 0;
		} while ($db->next_record());

		$t->set_var("sum_orders_qty", $sum_orders_qty);
		$t->set_var("sum_products_qty", $sum_products_qty);
		$t->set_var("sum_goods", currency_format($sum_goods));
		$t->set_var("sum_sales", currency_format($sum_sales));
		$t->set_var("sum_tax", currency_format($sum_tax));
		$t->set_var("sum_discount", currency_format($sum_discount));
		$t->set_var("sum_shipping", currency_format($sum_shipping));
		$t->set_var("sum_buying", currency_format($sum_buying));
		$t->set_var("sum_margin", currency_format($sum_margin));
		$t->set_var("sum_margin_percent", $sum_margin_percent);
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
	$t->pparse("main");

?>