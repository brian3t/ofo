<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_orders_tax_report.php                              ***
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
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("orders_stats");

	// Database Initialize
	$dbi = new VA_SQL();
	$dbi->DBType      = $db_type;
	$dbi->DBDatabase  = $db_name;
	$dbi->DBHost      = $db_host;
	$dbi->DBPort      = $db_port;
	$dbi->DBUser      = $db_user;
	$dbi->DBPassword  = $db_password;
	$dbi->DBPersistent= $db_persistent;

	$tax_note_incl = get_translation(get_setting_value($settings, "tax_note", ""));
	$tax_note_excl = get_translation(get_setting_value($settings, "tax_note_excl", ""));

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_orders_tax_report.html");
	$t->set_var("tax_note_excl", $tax_note_excl);
	$t->set_var("tax_note_incl", $tax_note_incl);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// prepare list values 
	$sql = "SELECT status_id, status_name FROM " . $table_prefix . "order_statuses WHERE is_active=1 ORDER BY status_order, status_id";
	$order_statuses = get_db_values($sql, array(array("", "")));
	$periods = array(array("", ""), array("1", TODAY_MSG), array("2", YESTERDAY_MSG), array("3", LAST_7DAYS_MSG), array("4", THIS_MONTH_MSG), array("5", LAST_MONTH_MSG), array("6", THIS_QUARTER_MSG), array("7", THIS_YEAR_MSG));

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

	$t->set_var("admin_orders_tax_report_href", "admin_orders_tax_report.php");
	$t->set_var("admin_product_href", "admin_product.php");

	$where = "";
	$r = new VA_Record("");
	$r->add_select("s_tp", INTEGER, $periods);
	$r->add_textbox("s_sd", DATE, FROM_DATE_MSG);
	$r->change_property("s_sd", VALUE_MASK, $date_edit_format);
	$r->add_textbox("s_ed", DATE, END_DATE_MSG);
	$r->change_property("s_ed", VALUE_MASK, $date_edit_format);
	$r->add_select("s_os", INTEGER, $order_statuses);
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

	}

	$where_sql = ""; $where_and_sql = "";
	if (strlen($where)) {
		$where_sql = " WHERE " . $where;
		$where_and_sql = " AND " . $where;
	}

	// group taxes
	$items_taxes = array();
	$orders_taxes = array();
	$tax_index = 0; $total_tax = 0; 

	$global_tax_prices_type = get_setting_value($settings, "tax_prices_type", 0);
	$global_tax_round = get_setting_value($settings, "tax_round", 1);

	$sql  = " SELECT o.order_id, o.tax_prices_type, o.tax_round_type, o.shipping_cost, o.shipping_taxable, ";
	$sql .= " oi.item_id, oi.item_type_id, oi.price, oi.quantity, oi.tax_free ";
	$sql .= " FROM " . $table_prefix . "orders_items oi, " . $table_prefix . "orders o ";
	$sql .= " WHERE oi.order_id = o.order_id " . $where_and_sql;
	$sql .= " ORDER BY o.order_id ";
	$dbi->query($sql);
	if ($dbi->next_record()) {
		$last_order_id = $dbi->f("order_id");
		$order_tax_rates = order_tax_rates($last_order_id);
//print_r($order_tax_rates);
		$tax_prices_type = $dbi->f("tax_prices_type");
		if (!strlen($tax_prices_type)) {
			$tax_prices_type = $global_tax_prices_type;
		}
		$tax_round = $dbi->f("tax_round_type");
		if (!strlen($tax_round)) {
			$tax_round = $global_tax_round;
		}

		do {
			$order_id = $dbi->f("order_id");
			if ($last_order_id != $order_id) {
				// add shipping 
				$shipping_tax_total = sum_order_taxes($order_tax_rates, $totals, $shipping_cost, $shipping_tax_values, "shipping", $tax_prices_type, $tax_round);
				sum_orders_taxes($orders_taxes, $order_tax_rates);

				$order_tax_rates = order_tax_rates($order_id);
				$tax_prices_type = $dbi->f("tax_prices_type");
				if (!strlen($tax_prices_type)) {
					$tax_prices_type = $global_tax_prices_type;
				}
				$tax_round = $dbi->f("tax_round_type");
				if (!strlen($tax_round)) {
					$tax_round = $global_tax_round;
				}
			}

			$shipping_cost = $dbi->f("shipping_cost");
			$shipping_taxable = $dbi->f("shipping_taxable");
			$shipping_tax_free = ($shipping_taxable) ? 0 : 1;

			$item_type_id = $dbi->f("item_type_id");
			$price = $dbi->f("price");
			$quantity = $dbi->f("quantity");
			$item_tax_free = $dbi->f("tax_free");
			$item_total = $price * $quantity;

			$item_tax_values = get_tax_amount($order_tax_rates, $item_type_id, $item_total, $item_tax_free, $item_tax_percent, "", 2, $tax_prices_type, $tax_round);
			$item_tax_total = sum_order_taxes($order_tax_rates, $totals, $item_total, $item_tax_values, "products", $tax_prices_type, $tax_round);

			$shipping_tax_values = get_tax_amount($order_tax_rates, "shipping", $shipping_cost, $shipping_tax_free, $shipping_tax_percent, "", 2, $tax_prices_type, $tax_round);

			$last_order_id = $order_id;
		} while ($dbi->next_record());
		// add last order info
		$shipping_tax_total = sum_order_taxes($order_tax_rates, $totals, $shipping_cost, $shipping_tax_values, "shipping", $tax_prices_type, $tax_round);
		sum_orders_taxes($orders_taxes, $order_tax_rates);
	}


	$sum_tax_total = 0;
	foreach ($orders_taxes as $tax_id => $tax_info) {
		$tax_name = $tax_info["tax_name"];	
		foreach ($tax_info as $tax_key => $tax_value) {
			if (preg_match("/^tax_total$/", $tax_key, $matches)) {
				$tax_index++;

				$sum_tax_total += $tax_value;
				$t->set_var("tax_name", $tax_name);
				$t->set_var("tax_total", currency_format($tax_value));

				$row_style = ($tax_index % 2 == 0) ? "row1" : "row2";
				$t->set_var("row_style", $row_style);

				$t->parse("taxes_records", true);
			}
		}
	}
	$t->set_var("sum_tax_total", currency_format($sum_tax_total));


	$tax_index = 0;
	$sum_goods_tax_total = 0;
	foreach ($orders_taxes as $tax_id => $tax_info) {
		$tax_name = $tax_info["tax_name"];	
		foreach ($tax_info as $tax_key => $tax_value) {
			if (preg_match("/^products_([\d\.]+)$/", $tax_key, $matches)) {
				$tax_index++;

				$goods_total = $tax_info["products_total_".$matches[1]];
				$sum_goods_tax_total += $tax_value;
				$t->set_var("tax_percent", $matches[1]."%");
				$t->set_var("tax_name", $tax_name);
				$t->set_var("goods_total", currency_format($goods_total));
				$t->set_var("goods_tax_total", currency_format($tax_value));
				$t->set_var("goods_with_tax_total", currency_format($goods_total + $tax_value));

				$row_style = ($tax_index % 2 == 0) ? "row1" : "row2";
				$t->set_var("row_style", $row_style);

				$t->parse("products_taxes", true);
			}
		}
	}
	$t->set_var("sum_goods_total", currency_format($totals["products_excl_tax"]));
	$t->set_var("sum_goods_tax_total", currency_format($totals["products_tax"]));
	$t->set_var("sum_goods_with_tax_total", currency_format($totals["products_incl_tax"]));

	$tax_index = 0;
	$sum_shipping_tax_total = 0;
	foreach ($orders_taxes as $tax_id => $tax_info) {
		$tax_name = $tax_info["tax_name"];	
		foreach ($tax_info as $tax_key => $tax_value) {
			if (preg_match("/^shipping_([\d\.]+)$/", $tax_key, $matches)) {
				$shipping_total = $tax_info["shipping_total_".$matches[1]];
				if ($shipping_total) {
					$tax_index++;
			  
					$sum_shipping_tax_total += $tax_value;
					$t->set_var("tax_percent", $matches[1]."%");
					$t->set_var("tax_name", $tax_name);
					$t->set_var("shipping_total", currency_format($shipping_total));
					$t->set_var("shipping_tax_total", currency_format($tax_value));
					$t->set_var("shipping_with_tax_total", currency_format($shipping_total + $tax_value));
			  
					$row_style = ($tax_index % 2 == 0) ? "row1" : "row2";
					$t->set_var("row_style", $row_style);
			  
					$t->parse("shipping_taxes", true);
				}
			}
		}
	}
	if ($tax_index) {
		$t->set_var("sum_shipping_total", currency_format($totals["shipping_excl_tax"]));
		$t->set_var("sum_shipping_tax_total", currency_format($totals["shipping_tax"]));
		$t->set_var("sum_shipping_with_tax_total", currency_format($totals["shipping_incl_tax"]));

		$t->parse("shipping_report", false);
	}


	$t->parse("search_results", false);
	$t->pparse("main");


	function sum_order_taxes(&$tax_rates, &$totals, $total_amount, $tax_values, $tax_type, $tax_prices_type, $tax_round)
	{
		global $currency;

		$decimals = get_setting_value($currency, "decimals", 2);
		// calculate totals
		$total_tax = 0;
		if (!isset($totals[$tax_type])) {
			$totals[$tax_type] = 0;
			$totals[$tax_type."_excl_tax"] = 0;
			$totals[$tax_type."_tax"] = 0;
			$totals[$tax_type."_incl_tax"] = 0;
		}
		foreach($tax_values as $tax_id => $tax_info) {
			$tax_amount = $tax_info["tax_amount"];
			$total_tax += $tax_amount;
		}
		$totals[$tax_type] += $total_amount;
		$totals[$tax_type."_tax"] += $total_tax;

		if ($tax_prices_type == 1) {
			// include tax
			$totals[$tax_type."_excl_tax"] += ($total_amount - $total_tax);
			$totals[$tax_type."_incl_tax"] += $total_amount;
		} else {
			// exclude tax
			$totals[$tax_type."_excl_tax"] += $total_amount;
			$totals[$tax_type."_incl_tax"] += ($total_amount + $total_tax);
		}

		if (is_array($tax_values)) {
			foreach($tax_values as $tax_id => $tax_info) {
				$tax_amount = $tax_info["tax_amount"];
				$tax_percent = $tax_info["tax_percent"];
				if ($tax_round == 1) {
					$tax_amount = round($tax_amount, $decimals);
				}
				//$total_tax += $tax_amount;
				if (!isset($tax_rates[$tax_id]["percent_" . $tax_percent])) {
					$tax_rates[$tax_id]["percent_" . $tax_percent] = 0;
				}
				if (!isset($tax_rates[$tax_id][$tax_type."_".$tax_percent])) {
					$tax_rates[$tax_id][$tax_type."_".$tax_percent] = 0;
				}
				if (!isset($tax_rates[$tax_id][$tax_type."_total_".$tax_percent])) {
					$tax_rates[$tax_id][$tax_type."_total_".$tax_percent] = 0;
				}
				if (!isset($tax_rates[$tax_id][$tax_type])) {
					$tax_rates[$tax_id][$tax_type] = 0;
				}
				if (!isset($tax_rates[$tax_id]["tax_total"])) {
					$tax_rates[$tax_id]["tax_total"] = 0;
				}
				$tax_rates[$tax_id]["percent_" . $tax_percent] += $tax_amount;
				$tax_rates[$tax_id][$tax_type."_".$tax_percent] += $tax_amount;
				$tax_rates[$tax_id]["tax_total"] += $tax_amount;
				if ($tax_prices_type == 1) {
					$tax_rates[$tax_id][$tax_type] += ($total_amount - $total_tax);
					$tax_rates[$tax_id][$tax_type."_total_".$tax_percent] += ($total_amount - $total_tax);
				} else {
					$tax_rates[$tax_id][$tax_type] += $total_amount;
					$tax_rates[$tax_id][$tax_type."_total_".$tax_percent] += $total_amount;
				}
			}
		}
		return $total_tax;
	}

	function sum_orders_taxes(&$orders_taxes, $order_tax_rates)
	{
		foreach ($order_tax_rates as $order_tax_id => $tax_info) {
			// check if such tax already exists
			$tax_id = "";
			foreach ($orders_taxes as $orders_tax_id => $order_tax) {
				if ($order_tax["tax_name"] == $tax_info["tax_name"]
					&& $order_tax["tax_percent"] == $tax_info["tax_percent"]
				) {
					$tax_id = $orders_tax_id;
					break;
				}
			}
			if (!strlen($tax_id)) {
				$orders_taxes[] = array(
					"tax_name" => $tax_info["tax_name"],
					"tax_percent" => $tax_info["tax_percent"],
				);
				end($orders_taxes);
				$tax_id = key($orders_taxes);
			}
			foreach ($tax_info as $tax_key => $tax_value) {
				if ( preg_match("/^(percent|products|shipping|tax_total)/", $tax_key) && !preg_match("/shipping_tax_percent/", $tax_key)) {
					if (!isset($orders_taxes[$tax_id][$tax_key])) {
						$orders_taxes[$tax_id][$tax_key] = 0;
					}
					$orders_taxes[$tax_id][$tax_key] += $tax_value;
				}
			}
		}
	}

?>