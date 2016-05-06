<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_orders_products_report.php                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
                                   	

	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/shopping_cart.php");
	include_once($root_folder_path . "includes/order_items.php");
	include_once($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("orders_stats");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_orders_products_report.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// prepare list values
	$sql = "SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE is_active=1 ORDER BY status_order, status_id";
	$order_statuses = get_db_values($sql, array(array("", "")));
	$periods = array(array("", ""), array("1", TODAY_MSG), array("2", YESTERDAY_MSG), array("3", LAST_7DAYS_MSG), array("4", THIS_MONTH_MSG), array("5", LAST_MONTH_MSG), array("6", THIS_QUARTER_MSG), array("7", THIS_YEAR_MSG));
	$credit_card_types = get_db_values("SELECT credit_card_id, credit_card_name FROM " . $table_prefix . "credit_cards ORDER BY credit_card_name", array(array("", "")));

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

	$t->set_var("admin_orders_products_report_href", "admin_orders_products_report.php");
	$t->set_var("admin_product_href", "admin_product.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_orders_products_report.php");
	$s->set_parameters(false, true, true, false);
	if ($db_type == "access") {
		$s->set_default_sorting(4, "desc");
		$s->set_sorter(ID_MSG, "sorter_id", "1", "item_id");
		$s->set_sorter(PROD_CODE_MSG, "sorter_item_code", "2", "item_code");
		$s->set_sorter(MANUFACTURER_CODE_MSG, "sorter_manufacturer_code", "3", "manufacturer_code");
		$s->set_sorter(PROD_NAME_MSG, "sorter_name", "4", "item_name");
		$s->set_sorter(AVERAGE_PRICE_COLUMN, "sorter_avg_price", "5", "SUM(oi.quantity * oi.price)/SUM(oi.quantity)");
		$s->set_sorter(QUANTITY_MSG, "sorter_qty", "6", "SUM(oi.quantity)");
		$s->set_sorter(SALES_MSG, "sorter_sales", "7", "SUM(oi.quantity * oi.price)");
		$s->set_sorter(BUYING_MSG, "sorter_buying", "8", "SUM(oi.quantity * oi.buying_price)");
		$t->set_var("sorter_margin", ADMIN_MARGIN_MSG);
	} else {
		$s->set_default_sorting(4, "desc");
		$s->set_sorter(ID_MSG, "sorter_id", "1", "item_id");
		$s->set_sorter(PROD_CODE_MSG, "sorter_item_code", "2", "item_code");
		$s->set_sorter(MANUFACTURER_CODE_MSG, "sorter_manufacturer_code", "3", "manufacturer_code");
		$s->set_sorter(PROD_NAME_MSG, "sorter_name", "4", "item_name");
		$s->set_sorter(AVERAGE_PRICE_COLUMN, "sorter_avg_price", "5", "avg_price");
		$s->set_sorter(QUANTITY_MSG, "sorter_qty", "6", "item_qty");
		$s->set_sorter(SALES_MSG, "sorter_sales", "7", "sales");
		$s->set_sorter(BUYING_MSG, "sorter_buying", "8", "buying");
		$s->set_sorter(ADMIN_MARGIN_MSG, "sorter_margin", "9", "margin");
	}

	//$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_orders_products_report.php");

	$where = "";
	$r = new VA_Record("");
	$r->add_select("s_tp", INTEGER, $periods);
	$r->add_textbox("s_sd", DATE, FROM_DATE_MSG);
	$r->change_property("s_sd", VALUE_MASK, $date_edit_format);
	$r->add_textbox("s_ed", DATE, END_DATE_MSG);
	$r->change_property("s_ed", VALUE_MASK, $date_edit_format);
	$r->add_select("s_os", INTEGER, $order_statuses);
	$r->add_select("s_cct", INTEGER, $order_statuses);
	$r->get_form_parameters();
	$r->validate();
	$r->set_form_parameters();
	$t->set_var("s_tp_url", $r->get_value("s_tp"));
	$t->set_var("s_os_url", $r->get_value("s_os"));
	
	if (!strlen(get_param("filter")) && $r->is_empty("s_sd") && $r->is_empty("s_ed")) {
		$t->set_var("search_results", "");
		$t->pparse("main");
		exit;
	}

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
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$where .= " o.order_placed_date < " . $db->tosql($day_after_end, DATE);
		}

		if (!$r->is_empty("s_os")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " o.order_status = " . $db->tosql($r->get_value("s_os"), INTEGER);
		}

		if (!$r->is_empty("s_cct")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " o.cc_type= " . $db->tosql($r->get_value("s_cct"), INTEGER);
		}
	}

	$where_count = ""; 
	$where_sql = "";
	if (strlen($where)) {
		$where_count = " WHERE " . $where;
		$where_sql   = " AND " . $where;
	}

	/*
	// set up variables for navigator
	$sql = "SELECT COUNT(oi.order_id) FROM " . $table_prefix . "orders_items oi, " . $table_prefix . "orders o ";
	$sql .= " WHERE oi.order_id = o.order_id " . $where_sql . " GROUP BY oi.item_id";
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);

	$records_per_page = (get_param("q")) ? abs(intval(get_param("q"))) : 25;
	$pages_number = 5;

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	*/

	$sum_qty = 0;
	$sum_buying = 0;
	$sum_sales = 0;
	$sum_margin = 0;
	$sum_avg_price = 0;
	$sql  = " SELECT oi.item_id, oi.item_code, oi.manufacturer_code, oi.item_name, ";
	$sql .= " SUM(oi.quantity) AS item_qty, SUM(oi.quantity * oi.price) AS sales, ";
	$sql .= " SUM(oi.quantity * oi.price)/SUM(oi.quantity) AS avg_price, ";
	$sql .= " SUM(oi.quantity * oi.buying_price) AS buying, ";
	$sql .= " SUM(oi.quantity * oi.price) - SUM(oi.quantity * oi.buying_price) AS margin ";
	$sql .= " FROM " . $table_prefix . "orders_items oi, " . $table_prefix . "orders o ";
	$sql .= " WHERE oi.order_id = o.order_id " . $where_sql;
	$sql .= " GROUP BY oi.item_id, oi.item_code, oi.manufacturer_code, oi.item_name ";
	$sql .= $s->order_by;
	$db->query($sql);
	if ($db->next_record()) {
		$item_index = 0;
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
			$item_index++;
			$item_id = intval($db->f("item_id"));
			$manufacturer_code = $db->f("manufacturer_code");
			$item_code = $db->f("item_code");
			$item_name = get_translation($db->f("item_name"));
			$avg_price = doubleval($db->f("avg_price"));
			$item_qty = intval($db->f("item_qty"));
			$sales = doubleval($db->f("sales"));
			$buying = doubleval($db->f("buying"));
			if ($db_type = "access") {
				$margin = $sales - $buying;
			} else {
				$margin = doubleval($db->f("margin"));
			}
			$margin_percent = ($sales != 0) ? number_format($margin / $sales * 100, 2) : 0;
			$t->set_var("item_id", $item_id);
			$t->set_var("item_code", $item_code);
			$t->set_var("manufacturer_code", $manufacturer_code);
			$t->set_var("item_name", $item_name);
			$t->set_var("avg_price", currency_format($avg_price));
			$t->set_var("item_qty", $item_qty);
			$t->set_var("sales", currency_format($sales));
			$t->set_var("buying", currency_format($buying));
			$t->set_var("margin", currency_format($margin));
			$t->set_var("margin_percent", $margin_percent);

			$row_style = ($item_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);

			$t->parse("records", true);

			$sum_qty += $item_qty;
			$sum_sales += $sales;
			$sum_buying += $buying;
			$sum_margin += $margin;
		} while ($db->next_record());
		$sum_avg_price = ($sum_qty != 0) ? number_format($sum_sales / $sum_qty, 2) : 0;
		$sum_margin_percent = ($sum_sales != 0) ? number_format($sum_margin / $sum_sales * 100, 2) : 0;
		$t->set_var("sum_avg_price", currency_format($sum_avg_price));
		$t->set_var("sum_qty", $sum_qty);
		$t->set_var("sum_sales", currency_format($sum_sales));
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