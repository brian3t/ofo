<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_orders.php                                         ***
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
	include_once($root_folder_path . "includes/order_links.php");
	include_once($root_folder_path . "includes/parameters.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");

	$orders_currency = get_setting_value($settings, "orders_currency", 0);

	$permissions = get_permissions();
	$operation  = get_param("operation");
	$orders_ids = get_param("orders_ids");
	$status_id	= get_param("status_id");

	$orders_errors = "";
	$recurring_errors = ""; $recurring_success = "";
	if ($operation == "recurring") {
		include_once("./admin_orders_recurring.php");
		if ($recurring_errors) {
			$orders_errors = $recurring_errors;
		}
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_orders.html");
	$t->set_var("date_edit_format", join("", $date_edit_format));

	if ($operation == "update_status") {
		if (isset($permissions["update_orders"]) && $permissions["update_orders"] == 1) {
			if (strlen($orders_ids) && strlen($status_id)) {
				$ids = explode(",", $orders_ids);
				for ($i = 0; $i < sizeof($ids); $i++) {
					update_order_status($ids[$i], $status_id, true, "", $status_error);
					if ($status_error) {
						$orders_errors .= $status_error . "<br>";
					}
				}
			}
		} else {
			$orders_errors .= NOT_ALLOWED_UPDATE_ORDERS_MSG;
		}
	} elseif ($operation == "remove_orders") {
		if (isset($permissions["remove_orders"]) && $permissions["remove_orders"] == 1) {
			remove_orders($orders_ids);
		} else {
			$orders_errors .= NOT_ALLOWED_REMOVE_ORDERS_MSG;
		}
	}

	// prepare list values 
	$sql = "SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE is_active=1 ORDER BY status_order, status_id";
	$order_statuses = get_db_values($sql, array());
	$countries = get_db_values("SELECT country_id, country_name FROM " . $table_prefix . "countries ORDER BY country_order, country_name ", array(array("", "")));
	$states = get_db_values("SELECT state_id, state_name FROM " . $table_prefix . "states ORDER BY state_name ", array(array("", "")));
	$cc_default_types = array(array("", ""), array("blank", WITHOUT_CARD_TYPE_MSG));
	$credit_card_types = get_db_values("SELECT credit_card_id, credit_card_name FROM " . $table_prefix . "credit_cards ORDER BY credit_card_name", $cc_default_types);
	$export_options = array(array("", ALL_MSG), array("1", EXPORTED_MSG), array("0", NOT_EXPORTED_MSG));
	if ($sitelist) {
		$sites = get_db_values("SELECT site_id, site_name FROM " . $table_prefix . "sites ORDER BY site_id ", array(array("", "")));
	}
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='order_info' ";
	//$sql .= " AND setting_name LIKE '%country_id%'";		
	if ($multisites_version) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id,INTEGER) . ") ";
		$sql .= " ORDER BY site_id ASC ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$order_info[$db->f("setting_name")] = $db->f("setting_value");
	}

	// prepare dates for stats
	$current_date = va_time();
	$cyear = $current_date[YEAR]; $cmonth = $current_date[MONTH]; $cday = $current_date[DAY]; 
	$today_ts = mktime (0, 0, 0, $cmonth, $cday, $cyear);
	$tomorrow_ts = mktime (0, 0, 0, $cmonth, $cday + 1, $cyear);
	$yesterday_ts = mktime (0, 0, 0, $cmonth, $cday - 1, $cyear);
	$week_ts = mktime (0, 0, 0, $cmonth, $cday - 6, $cyear);
	$month_ts = mktime (0, 0, 0, $cmonth, 1, $cyear);
	$last_month_ts = mktime (0, 0, 0, $cmonth - 1, 1, $cyear);
	$last_month_days = date("t", $last_month_ts);
	$last_month_end = mktime (0, 0, 0, $cmonth - 1, $last_month_days, $cyear);
	$today_date = va_date($date_edit_format, $today_ts);

	$stats = array(
		array("title" => TODAY_MSG, "date_start" => $today_ts, "date_end" => $today_ts),
		array("title" => YESTERDAY_MSG, "date_start" => $yesterday_ts, "date_end" => $yesterday_ts),
		array("title" => LAST_SEVEN_DAYS_MSG, "date_start" => $week_ts, "date_end" => $today_ts),
		array("title" => THIS_MONTH_MSG, "date_start" => $month_ts, "date_end" => $today_ts),
		array("title" => LAST_MONTH_MSG, "date_start" => $last_month_ts, "date_end" => $last_month_end),
	);

	// get orders stats
	for ($i = 0; $i < sizeof($order_statuses); $i++) {
		$status_id = $order_statuses[$i][0];
		$status_name = $order_statuses[$i][1];

		$t->set_var("status_id",   $status_id);
		$t->set_var("status_name", $status_name);

		$t->set_var("stats_periods", "");
		foreach ($stats as $key => $stat_info) {
			$start_date = $stat_info["date_start"];
			$end_date = va_time($stat_info["date_end"]);
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "orders ";
			$sql .= " WHERE order_status=" . $db->tosql($status_id, INTEGER);
			$sql .= " AND order_placed_date>=" . $db->tosql($start_date, DATE);
			$sql .= " AND order_placed_date<" . $db->tosql($day_after_end, DATE);
			$period_orders = get_db_value($sql);
			if (isset($stats[$key]["total"])) {
				$stats[$key]["total"] += $period_orders;
			} else {
				$stats[$key]["total"] = $period_orders;
			}
			if($period_orders > 0) {
				$period_orders = "<a href=\"admin_orders.php?s_os=".$status_id."&s_sd=".va_date($date_edit_format, $start_date)."&s_ed=".va_date($date_edit_format, $end_date)."\"><b>" . $period_orders."</b></a>";
			}
			$t->set_var("period_orders", $period_orders);
			$t->parse("stats_periods", true);
		}

		$t->parse("statuses_stats", true);
	}

	foreach ($stats as $key => $stat_info) {
		$t->set_var("start_date", va_date($date_edit_format, $stat_info["date_start"]));
		$t->set_var("end_date", va_date($date_edit_format, $stat_info["date_end"]));
		$t->set_var("stat_title", $stat_info["title"]);
		$t->set_var("period_total", $stat_info["total"]);
		$t->parse("stats_titles", true);
		$t->parse("stats_totals", true);
	}

	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_order_href",  $order_details_site_url . "admin_order.php");
	$t->set_var("admin_invoice_html_href","admin_invoice_html.php");
	$t->set_var("admin_invoice_pdf_href","admin_invoice_pdf.php");
	$t->set_var("admin_href",        "admin.php");
	$t->set_var("admin_import_href", "admin_import.php");
	$t->set_var("admin_export_href", "admin_export.php");
	$t->set_var("admin_invoice_pdf_href", "admin_invoice_pdf.php");
	$t->set_var("admin_packing_pdf_href", "admin_packing_pdf.php");
	$t->set_var("admin_orders_bom_pdf_href", "admin_orders_bom_pdf.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_orders.php");
	$s->set_parameters(false, true, true, false);
	$s->set_default_sorting(1, "desc");
	$s->set_sorter(ORDER_NUMBER_COLUMN, "sorter_id", "1", "o.order_id");
	$s->set_sorter(ORDER_ADDED_COLUMN, "sorter_date", "2", "o.order_placed_date");
	$s->set_sorter(STATUS_MSG, "sorter_status", "3", "o.order_status");
	$s->set_sorter(ADMIN_ORDER_TOTAL_MSG, "sorter_total", "4", "o.order_total");
	if (get_setting_value($order_info, "show_delivery_country_id", 0) == 1) {
		$s->set_sorter(EMAIL_TO_MSG, "sorter_ship_to", "5", "o.delivery_country_id");
	} else {
		$s->set_sorter(EMAIL_TO_MSG, "sorter_ship_to", "5", "o.country_id");
	}
	$s->set_sorter(SITE_NAME_MSG, "sorter_site_name", "6", "sti.site_name");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_orders.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$where = "";
	$r = new VA_Record($table_prefix . "orders");
	$r->add_textbox("s_on", TEXT, ORDER_NUMBER_MSG);
	$r->change_property("s_on", TRIM, true);
	$r->add_textbox("s_ne", TEXT);
	$r->change_property("s_ne", TRIM, true);
	$r->add_textbox("s_kw", TEXT);
	$r->change_property("s_kw", TRIM, true);
	$r->add_textbox("s_sd", DATE, FROM_DATE_MSG);
	$r->change_property("s_sd", VALUE_MASK, $date_edit_format);
	$r->change_property("s_sd", TRIM, true);
	$r->add_textbox("s_ed", DATE, END_DATE_MSG);
	$r->change_property("s_ed", VALUE_MASK, $date_edit_format);
	$r->change_property("s_ed", TRIM, true);		
	$r->add_checkboxlist("s_os_list", INTEGER, $order_statuses);
	array_unshift($order_statuses, array("", ""));
	$r->add_select("s_os", INTEGER, $order_statuses);
	$r->add_select("s_ci", TEXT, $countries);
	$r->add_select("s_si", TEXT, $states);
	$r->add_select("s_cct", TEXT, $credit_card_types);
	$r->add_select("s_ex", TEXT, $export_options);
	if ($sitelist) {
		$r->add_select("s_sti", TEXT, $sites);
	}
	$r->get_form_parameters();
	$r->validate();

	$where = ""; $product_search = false;
	if (!$r->errors) {
		if (!$r->is_empty("s_on")) {
			$s_on = $r->get_value("s_on");
			if (preg_match("/^(\d+)(,\d+)*$/", $s_on))	{
				$where  = " (o.order_id IN (" . $s_on . ") ";
				$where .= " OR o.invoice_number=" . $db->tosql($s_on, TEXT);
				$where .= " OR o.transaction_id=" . $db->tosql($s_on, TEXT) . ") ";
			} else {
				$where .= " (o.invoice_number=" . $db->tosql($s_on, TEXT);
				$where .= " OR o.transaction_id=" . $db->tosql($s_on, TEXT) . ") ";
			}
		}

		if (!$r->is_empty("s_ne")) {
			if (strlen($where)) { $where .= " AND "; }
			$s_ne = $r->get_value("s_ne");
			$s_ne_sql = $db->tosql($s_ne, TEXT, false);
			if (preg_match(EMAIL_REGEXP, $s_ne)) {
				$where .= " o.email=" . $db->tosql($s_ne, TEXT);
			} else {
				$where .= " (o.email LIKE '%" . $s_ne_sql . "%'";
				$where .= " OR o.name LIKE '%" . $s_ne_sql . "%'";
				$name_parts = explode(" ", $s_ne, 2);
				if (sizeof($name_parts) == 1) {
					$where .= " OR o.first_name LIKE '%" . $s_ne_sql . "%'";
					$where .= " OR o.last_name LIKE '%" . $s_ne_sql . "%'";
				} else {
					$where .= " OR (first_name LIKE '%" . $db->tosql($name_parts[0], TEXT, false) . "%' ";
					$where .= " AND last_name LIKE '%" . $db->tosql($name_parts[1], TEXT, false) . "%') ";
				}
				$where .= ") ";
			}
		}

		if (!$r->is_empty("s_kw")) {
			$product_search = true;
			if (strlen($where)) { $where .= " AND "; }
			$where .= " (oi.item_name LIKE '%" . $db->tosql($r->get_value("s_kw"), TEXT, false) . "%'";
			$where .= " OR oi.item_properties LIKE '%" . $db->tosql($r->get_value("s_kw"), TEXT, false) . "%'";
			$where .= " OR ois.serial_number=" . $db->tosql($r->get_value("s_kw"), TEXT);
			$where .= " OR osa.generation_key=" . $db->tosql($r->get_value("s_kw"), TEXT);
			$where .= " OR osa.activation_key=" . $db->tosql($r->get_value("s_kw"), TEXT);
			$where .= " OR o.shipping_type_desc LIKE '%" . $db->tosql($r->get_value("s_kw"), TEXT, false) . "%')";
		}

		if (!$r->is_empty("s_sd")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " o.order_placed_date>=" . $db->tosql($r->get_value("s_sd"), DATE);
		}

		if (!$r->is_empty("s_ed")) {
			if (strlen($where)) { $where .= " AND "; }
			$end_date = $r->get_value("s_ed");
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$where .= " o.order_placed_date<" . $db->tosql($day_after_end, DATE);
		}

		$t->set_var("status_select_style", "");
		$t->set_var("status_checkboxes_style", "style='display:none;'");
		if (!$r->is_empty("s_os_list")) {
			if (strlen($where)) { $where .= " AND "; }
			$s_os_list = $r->get_value("s_os_list");
			if (count($s_os_list) >1) {
				$where .= " o.order_status IN(" . $db->tosql($s_os_list, INTEGERS_LIST) . ")";
				$t->set_var("status_select_style", "style='display:none;'");
				$t->set_var("status_checkboxes_style", "");
			} else {
				$s_os = $s_os_list[0];
				$r->set_value("s_os", $s_os);
				$r->parameters["s_os_list"][3] = array();
				$where .= " o.order_status=" . $db->tosql($s_os, INTEGER);				
			}
		} elseif (!$r->is_empty("s_os")) {
			$s_os = $r->get_value("s_os");
			if (strlen($where)) { $where .= " AND "; }			
			$where .= " o.order_status=" . $db->tosql($s_os, INTEGER);
		} else if ($r->is_empty("s_on")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " (os.is_list=1 OR os.is_list IS NULL) ";
		}		

		if (!$r->is_empty("s_ci")) {
			if ($order_info["show_delivery_country_id"] == 1) {
				if (strlen($where)) { $where .= " AND "; }
				$where .= " o.delivery_country_id=" . $db->tosql($r->get_value("s_ci"), TEXT);
			} elseif ($order_info["show_country_id"] == 1) {
				if (strlen($where)) { $where .= " AND "; }
				$where .= " o.country_id=" . $db->tosql($r->get_value("s_ci"), TEXT);
			} 
		}

		if (!$r->is_empty("s_si")) {
			if ($order_info["show_delivery_state_id"] == 1) {
				if (strlen($where)) { $where .= " AND "; }
				$where .= " o.delivery_state_id=" . $db->tosql($r->get_value("s_si"), TEXT);
			} elseif ($order_info["show_state_id"] == 1) {
				if (strlen($where)) { $where .= " AND "; }
				$where .= " o.state_id=" . $db->tosql($r->get_value("s_si"), TEXT);
			} 
		}

		if (!$r->is_empty("s_cct")) {
			if (strlen($where)) { $where .= " AND "; }
			if ($r->get_value("s_cct") == "blank") {
				$where .= " o.cc_type IS NULL ";
			} else {
				$where .= " o.cc_type=" . $db->tosql($r->get_value("s_cct"), INTEGER);
			}
		}

		if (!$r->is_empty("s_ex")) {
			if (strlen($where)) { $where .= " AND "; }
			$s_ex = $r->get_value("s_ex");
			$where .= ($s_ex == 1) ? " o.is_exported=1 " : " (o.is_exported<>1 OR o.is_exported IS NULL) ";
		}

		if (!$r->is_empty("s_sti")) {
			if (strlen($where)) { $where .= " AND "; }
			$s_sti = $r->get_value("s_sti");
			$where .= " o.site_id=" . $db->tosql($r->get_value("s_sti"), INTEGER);
		}

	}

	$r->set_form_parameters();
		
	$where_sql = ""; 
	if (strlen($where)) {
		$where_sql = " WHERE " . $where;
	}

	set_options($order_statuses, "status_id", "status_id");

	// set up variables for navigator
	if ($product_search) {
		$total_records = 0;
		$sql  = " SELECT COUNT(*) FROM ((((" . $table_prefix . "orders o ";
		$sql .= " INNER JOIN " . $table_prefix . "orders_items oi ON o.order_id=oi.order_id)";
		$sql .= " LEFT JOIN " . $table_prefix . "orders_items_serials ois ON o.order_id=ois.order_id)";
		$sql .= " LEFT JOIN " . $table_prefix . "orders_serials_activations osa ON o.order_id=osa.order_id)";
		$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
		$sql .= $where_sql;
		$sql .= " GROUP BY o.order_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$total_records++;
		}
	} else {
		$sql  = " SELECT COUNT(*) FROM (" . $table_prefix . "orders o ";
		$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
		$sql .= $where_sql;
		$db->query($sql);
		$db->next_record();
		$total_records = $db->f(0);
	}

	$records_per_page = 25;
	$pages_number = 5;

	$orders = array();
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT o.order_id, o.order_placed_date, os.status_name, o.goods_total, o.order_total, o.remote_address, ";
	$sql .= " o.name, o.first_name, o.last_name, o.country_id, o.delivery_country_id, o.state_id, o.delivery_state_id, ";
	$sql .= " o.shipping_tracking_id, n.note_title, n.note_details,"; //** EGGHEAD VENTURES ADD
	$sql .= " o.currency_code, o.currency_rate, c.symbol_right, c.symbol_left, c.decimals_number, c.decimal_point, c.thousands_separator ";
	if($sitelist) {
		$sql .= ", sti.site_name ";
	}
	$sql .= " FROM (((((((" . $table_prefix . "orders o ";//** EGGHEAD VENTURES ADD extra (
	$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "currencies c ON o.currency_code=c.currency_code) ";
	$sql .= " LEFT JOIN " . $table_prefix . "orders_notes n ON n.order_id=o.order_id) "; //** EGGHEAD VENTURES ADD			   
	if ($product_search) {
		$sql .= " INNER JOIN " . $table_prefix . "orders_items oi ON o.order_id=oi.order_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "orders_items_serials ois ON o.order_id=ois.order_id)";
		$sql .= " LEFT JOIN " . $table_prefix . "orders_serials_activations osa ON o.order_id=osa.order_id)";
	} else {
		$sql .= ")))";
	}
	if($sitelist) {
		$sql .= " LEFT JOIN " . $table_prefix . "sites sti ON sti.site_id=o.site_id)";
	} else {
		$sql .= " )";
	}
	$sql .= $where_sql;
	if ($product_search) {
		$sql .= " GROUP BY o.order_id, o.order_placed_date, os.status_name, o.goods_total, o.order_total, o.name, o.remote_address, ";
		$sql .= " o.first_name, o.last_name, o.country_id, o.delivery_country_id, o.state_id, o.delivery_state_id, ";
		$sql .= " o.currency_code, o.currency_rate, c.symbol_right, c.symbol_left, c.decimals_number, c.decimal_point, c.thousands_separator "; //**Removed sti.site_name
	}
	$sql .= $s->order_by;
	$db->query($sql);
	if ($db->next_record())
	{
		$admin_order = new VA_URL($order_details_site_url . "admin_order.php", false);
		$admin_order->add_parameter("s_on", REQUEST, "s_on");
		$admin_order->add_parameter("s_ne", REQUEST, "s_ne");
		$admin_order->add_parameter("s_kw", REQUEST, "s_kw");
		$admin_order->add_parameter("s_sd", REQUEST, "s_sd");
		$admin_order->add_parameter("s_ed", REQUEST, "s_ed");
		$admin_order->add_parameter("s_os", REQUEST, "s_os");
		$admin_order->add_parameter("s_ci", REQUEST, "s_ci");
		$admin_order->add_parameter("s_si", REQUEST, "s_si");
		$admin_order->add_parameter("s_cct", REQUEST, "s_cct");
		$admin_order->add_parameter("s_ex", REQUEST, "s_ex");
		$admin_order->add_parameter("s_sti", REQUEST, "s_sti");
		$admin_order->add_parameter("page", REQUEST, "page");
		$admin_order->add_parameter("sort_ord", REQUEST, "sort_ord");
		$admin_order->add_parameter("sort_dir", REQUEST, "sort_dir");
		$admin_order->add_parameter("order_id", DB, "order_id");

		$order_index = 0;
		do
		{
			//$order_index++;
			$order_id    = $db->f("order_id");
			$order_total = $db->f("order_total");
			$shipping_tracking_id = $db->f("shipping_tracking_id");  //** EGGHEAD VENTURES ADD
			$note_title = $db->f("note_title"); //** EGGHEAD VENTURES ADD
			$note_details = $db->f("note_details"); //** EGGHEAD VENTURES ADD
			// get order currency
			$order_currency = array();
			$order_currency_code = $db->f("currency_code");
			$order_currency_rate= $db->f("currency_rate");
			$order_currency["code"] = $db->f("currency_code");
			$order_currency["rate"] = $db->f("currency_rate");
			$order_currency["left"] = $db->f("symbol_left");
			$order_currency["right"] = $db->f("symbol_right");
			$order_currency["decimals"] = $db->f("decimals_number");
			$order_currency["point"] = $db->f("decimal_point");
			$order_currency["separator"] = $db->f("thousands_separator");
	  
			if ($orders_currency != 1) {
				$order_currency["left"] = $currency["left"];
				$order_currency["right"] = $currency["right"];
				$order_currency["decimals"] = $currency["decimals"];
				$order_currency["point"] = $currency["point"];
				$order_currency["separator"] = $currency["separator"];
				if (strtolower($currency["code"]) != strtolower($order_currency_code)) {
					$order_currency["rate"] = $currency["rate"];
				}
			}
			$user_name = $db->f("name");
			if(!strlen($user_name)) {
				$user_name = $db->f("first_name") . " " . $db->f("last_name");
			}
			$order_placed_date = $db->f("order_placed_date", DATETIME);
			$order_placed_date = va_date($datetime_show_format, $order_placed_date);

			if (get_setting_value($order_info, "show_delivery_country_id", 0) == 1) {
				$country_id = $db->f("delivery_country_id");
				$state_id = $db->f("delivery_state_id");
			} elseif (get_setting_value($order_info, "show_country_id", 0) == 1) {
				$country_id = $db->f("country_id");
				$state_id = $db->f("state_id");
			} else {
				$country_id = $settings["country_id"];
				$state_id = get_setting_value($settings, "state_id", "");
			}
			$status_name = $db->f("status_name");
			$admin_order_url   = $admin_order->get_url();
			$remote_address = $db->f("remote_address");
			$site_name = $db->f("site_name");
			
			$orders[] = array($order_id, $order_total, $user_name, $order_placed_date, $status_name, $country_id, $state_id, $admin_order_url, $remote_address, $order_currency, $site_name, $shipping_tracking_id, $note_title, $note_details);  //** EGGHEAD VENTURES ADD $shipping_tracking_id, $note_title, $note_details
		} while ($db->next_record());
	}

	$colspan = 11; // ** EGGHEAD VENTURES ADD change from 9 to 11
	if ($sitelist) {
		$colspan++;	
	}
	$t->set_var("colspan", $colspan);
	if (sizeof($orders) > 0)
	{
		$order_index = 0;
		if ($sitelist) {
			$t->parse("site_name_header", false);
		}
 		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		for ($i = 0; $i < sizeof($orders); $i++) {
			$order_index++;

			list($order_id, $order_total, $user_name, $order_placed_date, $status_name, $country_id, $state_id, $admin_order_url, $remote_address, $order_currency, $site_name, $shipping_tracking_id, $note_title, $note_details) = $orders[$i];//** EGGHEAD VENTURES ADDED $shipping_tracking_id, $note_title, $note_details

			$ship_to = "";
			if ($country_id) {
				$sql = " SELECT country_code FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($country_id, INTEGER);
				$ship_to = get_db_value($sql);
			}
			if ($state_id) {
				$sql = " SELECT state_code FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($state_id, INTEGER);
				$state_code = get_db_value($sql);
				if ($ship_to) {
					$ship_to .= "," . $state_code;
				} else {
					$ship_to  = $state_code;
				}
			}

			$t->set_var("order_index", $order_index);
			$t->set_var("order_id", $order_id);
			$t->set_var("shipping_tracking_id", $shipping_tracking_id); //** EGGHEAD VENTURES ADD
			if($note_title) {  //** EGGHEAD VENTURES ADD
				$t->set_var("notes", "*");
				$t->set_var("note_title", $note_title);
				$t->set_var("note_details", $note_details);
				$t->set_var("note_url", "admin_order_notes.php?order_id=".$order_id);
				$t->set_var("note_style", "font-weight: bold; color: blue;text-decoration:none;");
			}else{
				$t->set_var("notes", "");
				$t->set_var("note_title", "Add Note");
				$t->set_var("note_details", "");
				$t->set_var("note_url", "admin_order_note.php?order_id=".$order_id);
				$t->set_var("note_style", "");
			}
			$t->set_var("user_name", htmlspecialchars($user_name));
			$t->set_var("order_placed_date", $order_placed_date);

			$t->set_var("order_status", $status_name);

			$t->set_var("order_total", currency_format($order_total, $order_currency));
			$t->set_var("ship_to", $ship_to);
			$t->set_var("admin_order_url", $admin_order_url);
			
			if ($sitelist) {
				$t->set_var("site_name", $site_name);
				$t->parse("site_name_block", false);
			}

			$sql  = "SELECT ip_address FROM " . $table_prefix . "black_ips WHERE ip_address=" . $db->tosql($remote_address, TEXT);
			$db->query($sql);
			if ($db->next_record()) {
				$row_style = "rowWarn";
			} else {
				$row_style = ($order_index % 2 == 0) ? "row1" : "row2";
			}
			$t->set_var("row_style", $row_style);

			$t->set_var("order_items", "");
			$total_quantity = 0;
			$total_price = 0;
			$sql  = " SELECT item_name, quantity, price ";
			$sql .= " FROM " . $table_prefix . "orders_items ";
			$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$item_name = get_translation($db->f("item_name"));
/*				if (strlen($item_name) > 20) {
					$item_name = substr($item_name, 0, 20) . "...";
				}*/
				$quantity = $db->f("quantity");
				$price = $db->f("price");

				$total_quantity += $quantity;
				$total_price += ($price * $quantity);

				$t->set_var("item_name", $item_name);
				$t->set_var("quantity",  $quantity);
				$t->set_var("price", currency_format($price, $order_currency));
				$t->parse("order_items", true);
			}
			$t->set_var("total_quantity", $total_quantity);
			$t->set_var("total_price", currency_format($total_price, $order_currency));

			$t->parse("records", true);
		} 
		$t->set_var("orders_number", $order_index);
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->set_var("page", $page_number);
	$t->set_var("s_os_search", $r->get_value("s_os"));
	$t->set_var("s_ci_search", $r->get_value("s_ci"));
	$t->set_var("s_si_search", $r->get_value("s_si"));
	$t->set_var("s_ex_search", $r->get_value("s_ex"));

	if (sizeof($orders) > 0) 
	{
		if (isset($permissions["update_orders"]) && $permissions["update_orders"] == 1) {
			$t->parse("update_status", false);
		}
		if (isset($permissions["remove_orders"]) && $permissions["remove_orders"] == 1) {
			$t->parse("remove_orders_button", false);
		}
	}

	if (strlen($orders_errors)) {
		$t->set_var("errors_list", $orders_errors);
		$t->parse("orders_errors", false);
	}

	if (strlen($recurring_success)) {
		$t->set_var("messages_list", $recurring_success);
		$t->parse("orders_messages", false);
	}


	if (strlen($where) && $total_records > 0) {
		$admin_export_filtered_url = new VA_URL("admin_export.php", true);
		$admin_export_filtered_url->add_parameter("table", CONSTANT, "orders");
		$t->set_var("admin_export_filtered_url", $admin_export_filtered_url->get_url());
		$t->set_var("total_filtered", $total_records);
		$t->parse("export_filtered", false);
	}
  
	if (isset($permissions["create_orders"]) && $permissions["create_orders"] == 1) {
		$t->parse("generate_recurring", false);
	}
	
	$sql  = " SELECT exported_order_id FROM " . $table_prefix . "admins ";
	$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
	$exported_order_id = intval(get_db_value($sql));

	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "orders ";
	$sql .= " WHERE order_id>" . $db->tosql($exported_order_id, INTEGER);
	$total_new = get_db_value($sql);
	if ($total_new > 0) {
		$t->set_var("exported_order_id", urlencode($exported_order_id));
		$t->set_var("total_new", $total_new);
		$t->parse("export_new", false);
	}

	$sql  = " SELECT MAX(order_id) FROM " . $table_prefix . "orders ";
	$max_order_id = get_db_value($sql);

	if ($max_order_id > get_session("session_last_order_id") && $max_order_id > get_session("session_max_order_id")) {
		set_session("session_max_order_id", $max_order_id);
		$sql = " UPDATE " . $table_prefix . "admins SET last_order_id=" . $db->tosql($max_order_id, INTEGER);
		$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$db->query($sql);
	}

	if ($sitelist) {
		$t->parse('sitelist');		
	}
	$t->pparse("main");

?>