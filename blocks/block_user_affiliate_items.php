<?php

	check_user_security("affiliate_sales");

	$t->set_file("block_body","block_user_affiliate_items.html");
	$t->set_var("user_products_href",  get_custom_friendly_url("user_products.php"));
	$t->set_var("user_product_href",   get_custom_friendly_url("user_product.php"));
	$t->set_var("user_home_href",      get_custom_friendly_url("user_home.php"));
	$t->set_var("user_affiliate_sales_href",get_custom_friendly_url("user_affiliate_sales.php"));
	$t->set_var("user_affiliate_items_href",get_custom_friendly_url("user_affiliate_items.php"));

	// prepare list values 
	$periods = array(array("", ""), array("1", TODAY_MSG), array("2", YESTERDAY_MSG), array("3", LAST_7DAYS_MSG), array("4", THIS_MONTH_MSG), array("5", LAST_MONTH_MSG), array("6", THIS_QUARTER_MSG), array("7", THIS_YEAR_MSG));
	$order_statuses = get_db_values("SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE show_for_user=1 AND is_active=1 ORDER BY status_order ", array(array("", "")));

	// prepare dates for stats
	$current_date = va_time();
	$cyear = $current_date[YEAR]; 
	$cmonth = $current_date[MONTH]; 
	$cday = $current_date[DAY]; 
	$today_ts = mktime (0, 0, 0, $cmonth, $cday, $cyear);
	$tomorrow_ts = mktime (0, 0, 0, $cmonth, $cday + 1, $cyear);
	$yesterday_ts = mktime (0, 0, 0, $cmonth, $cday - 1, $cyear);
	$week_ts = mktime (0, 0, 0, $cmonth, $cday - 6, $cyear);
	$month_ts = mktime (0, 0, 0, $cmonth, 1, $cyear);
	$last_month_start_ts = mktime (0, 0, 0, $cmonth - 1, 1, $cyear);
	$last_month_end_ts = mktime (0, 0, 0, $cmonth, 0, $cyear);
	$quarter_ts = mktime (0, 0, 0, intval(($cmonth - 1) / 3) * 3 + 1, 1, $cyear);
	$year_ts = mktime (0, 0, 0, 1, 1, $cyear);
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

	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", get_custom_friendly_url("user_affiliate_items.php"));
	$s->set_parameters(false, true, true, false);
	if ($db_type == "access") {
		$s->set_default_sorting(4, "desc");
		$s->set_sorter(ID_MSG, "sorter_id", "1", "item_id");
		$s->set_sorter(PROD_NAME_MSG, "sorter_name", "2", "item_name");
		$s->set_sorter(AVERAGE_PRICE_COLUMN, "sorter_avg_price", "3", "SUM(oi.quantity * oi.price)/SUM(oi.quantity)");
		$s->set_sorter(QUANTITY_MSG, "sorter_qty", "4", "SUM(oi.quantity)");

		$s->set_sorter(GROSS_SALES_MSG, "sorter_sales", "5", "SUM(oi.price * oi.quantity)");
		$s->set_sorter(COMMISSIONS_MSG, "sorter_commissions", "6", "SUM(oi.affiliate_commission * oi.quantity)");
	} else {
		$s->set_default_sorting(4, "desc");
		$s->set_sorter(ID_MSG, "sorter_id", "1", "item_id");
		$s->set_sorter(PROD_NAME_MSG, "sorter_name", "2", "item_name");
		$s->set_sorter(AVERAGE_PRICE_COLUMN, "sorter_avg_price", "3", "avg_price");
		$s->set_sorter(QUANTITY_MSG, "sorter_qty", "4", "item_qty");

		$s->set_sorter(GROSS_SALES_MSG, "sorter_sales", "5", "sales");
		$s->set_sorter(COMMISSIONS_MSG, "sorter_commissions", "6", "commissions");
	}

	$where = "";
	$r = new VA_Record("");
	$r->add_select("s_tp", INTEGER, $periods);
	$r->add_textbox("s_sd", DATE, "From Date");
	$r->change_property("s_sd", VALUE_MASK, $date_edit_format);
	$r->add_textbox("s_ed", DATE, "End Date");
	$r->change_property("s_ed", VALUE_MASK, $date_edit_format);
	$r->add_select("s_os", INTEGER, $order_statuses);
	$r->get_form_parameters();
	$r->validate();
	$r->set_form_parameters();
	$t->set_var("s_tp_url", $r->get_value("s_tp"));
	$t->set_var("s_os_url", $r->get_value("s_os"));
	
	if (!strlen(get_param("filter")) && $r->is_empty("s_sd") && $r->is_empty("s_ed")) {
		$t->set_var("search_results", "");
		$t->parse("block_body", false);
		$t->parse($block_name, true);
		exit;
	}

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

	$sum_qty = 0; $sum_sales = 0; $sum_avg_price = 0; $sum_commissions = 0;

	$sql  = " SELECT oi.item_id, oi.item_name, ";
	$sql .= " SUM(oi.quantity) AS item_qty, SUM(oi.quantity * oi.price) AS sales, ";
	$sql .= " SUM(oi.quantity * oi.price)/SUM(oi.quantity) AS avg_price, ";
	$sql .= " SUM(oi.affiliate_commission * oi.quantity) AS commissions  ";
	$sql .= " FROM ((" . $table_prefix . "orders o ";
	$sql .= " INNER JOIN " . $table_prefix . "orders_items oi ON o.order_id=oi.order_id) ";
	$sql .= " INNER JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
	$sql .= " WHERE oi.affiliate_user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$sql .= " AND os.show_for_user=1 ";
	$sql .= $where;
	$sql .= " GROUP BY oi.item_id, oi.item_name ";
	$sql .= $s->order_by;

	$db->query($sql);
	if ($db->next_record()) {
		$item_index = 0;
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
			$item_index++;
			$item_id = intval($db->f("item_id"));
			$item_name = get_translation($db->f("item_name"));
			$avg_price = doubleval($db->f("avg_price"));
			$item_qty = intval($db->f("item_qty"));
			$sales = doubleval($db->f("sales"));
			$commissions = doubleval($db->f("commissions"));

			$t->set_var("item_id", $item_id);
			$t->set_var("item_name", $item_name);
			$t->set_var("avg_price", currency_format($avg_price));
			$t->set_var("item_qty", $item_qty);
			$t->set_var("sales", currency_format($sales));
			$t->set_var("commissions", currency_format($commissions));
			$t->set_var("product_details_url", get_custom_friendly_url("product_details.php") . "?item_id=" . $item_id);
			
			$row_style = ($item_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);

			$t->parse("records", true);

			$sum_qty += $item_qty;
			$sum_sales += $sales;
			$sum_commissions += $commissions;
		} while ($db->next_record());
		$sum_avg_price = ($sum_qty != 0) ? ($sum_sales / $sum_qty) : 0;
		$t->set_var("sum_avg_price", currency_format($sum_avg_price));
		$t->set_var("sum_qty", $sum_qty);
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