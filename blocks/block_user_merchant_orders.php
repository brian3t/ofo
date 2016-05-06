<?php

	check_user_security("merchant_orders");

	$orders_currency = get_setting_value($settings, "orders_currency", 0);
	$secure_merchant_order = get_setting_value($settings, "secure_merchant_order", 0);	
	$site_url = get_setting_value($settings, "site_url", "");	
	$secure_url = get_setting_value($settings, "secure_url", "");

	$t->set_file("block_body","block_user_merchant_orders.html");
	$t->set_var("user_merchant_orders_href", get_custom_friendly_url("user_merchant_orders.php"));
	$t->set_var("user_merchant_order_href",  get_custom_friendly_url("user_merchant_order.php"));
	$t->set_var("user_home_href",            get_custom_friendly_url("user_home.php"));

	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", get_custom_friendly_url("user_merchant_orders.php"));
	$s->set_default_sorting(1, "desc");
	$s->set_sorter(ORDER_NUMBER_COLUMN, "sorter_id", "1", "o.order_id");
	$s->set_sorter(ORDER_ADDED_COLUMN, "sorter_date", "2", "o.order_placed_date");
	$s->set_sorter(STATUS_MSG, "sorter_status", "3", "o.order_status");
	$s->set_sorter(GOODS_TOTAL_MSG, "sorter_total", "4", "order_total");

	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", get_custom_friendly_url("user_merchant_orders.php"));

	// set up variables for navigator
	if (strtolower($db_type) == "mysql") {
		$sql  = " SELECT COUNT(DISTINCT oi.order_id) ";
	} else {
		$sql  = " SELECT COUNT(*) "; 
	}
	$sql .= " FROM (" . $table_prefix . "orders_items oi ";
	$sql .= " INNER JOIN " . $table_prefix . "order_statuses os ON oi.item_status=os.status_id) ";
	$sql .= " WHERE oi.item_user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$sql .= " AND os.show_for_user=1 ";
	$total_records = 0;
	if (strtolower($db_type) == "mysql") {
		$db->query($sql);
		$db->next_record();
		$total_records = $db->f(0);
	} else {
		$sql .= " GROUP BY oi.order_id";
		$db->query($sql);
		while ($db->next_record()) {
			$total_records++;
		}
	}
	$records_per_page = 25;
	$pages_number = 5;

	if ($secure_merchant_order) {
		$user_merchant_order_page = $secure_url . get_custom_friendly_url("user_merchant_order.php");
	} else {
		$user_merchant_order_page = $site_url . get_custom_friendly_url("user_merchant_order.php");
	}

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT o.order_id, o.name, o.first_name, o.last_name, o.order_placed_date, os.status_name, ";
	$sql .= " SUM(oi.price * oi.quantity) AS items_total, ";
	$sql .= " o.currency_code, o.currency_rate, c.symbol_right, c.symbol_left, c.decimals_number, c.decimal_point, c.thousands_separator ";
	$sql .= " FROM (((" . $table_prefix . "orders o ";
	$sql .= " INNER JOIN " . $table_prefix . "orders_items oi ON o.order_id=oi.order_id) ";
	$sql .= " INNER JOIN " . $table_prefix . "order_statuses os ON oi.item_status=os.status_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "currencies c ON o.currency_code=c.currency_code) ";
	$sql .= " WHERE oi.item_user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$sql .= " AND os.show_for_user=1 ";
	if (strtolower($db_type) == "access") {
		$sql .= " GROUP BY o.order_id, o.name, o.first_name, o.last_name, o.order_placed_date, os.status_name, o.currency_code, o.currency_rate, c.symbol_right, c.symbol_left ";
	} else {
		$sql .= " GROUP BY o.order_id, os.status_name ";
	}
	$db->query($sql . $s->order_by);
	if ($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$order_id = $db->f("order_id");
			$customer_name = trim($db->f("name"));
			if (!strlen($customer_name)) {
				$customer_name = $db->f("first_name") . " " . $db->f("last_name");
			}
			$items_total = $db->f("items_total");
			$order_total = $db->f("order_total");
			$placed_date = $db->f("order_placed_date", DATETIME);
			// get order currency
			$order_currency = array();
			$order_currency_code = $db->f("currency_code");
			$order_currency["code"] = $db->f("currency_code");
			$order_currency["rate"] = $db->f("currency_rate");
			$order_currency["left"] = $db->f("symbol_left");
			$order_currency["right"] = $db->f("symbol_right");
			$order_currency["decimals"] = $db->f("decimals_number");
			$order_currency["point"] = $db->f("decimal_point");
			$order_currency["separator"] = $db->f("thousands_separator");
			$vc = md5($order_id . $placed_date[3].$placed_date[4].$placed_date[5]);
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
			$t->set_var("order_id", $order_id);
			$t->set_var("customer_name", htmlspecialchars($customer_name));
			$t->set_var("vc", $vc);

			$t->set_var("order_placed_date", va_date($datetime_show_format, $placed_date));

			$t->set_var("order_status", $db->f("status_name"));
			$t->set_var("items_total", currency_format($items_total, $order_currency));

			$t->set_var("user_merchant_order_url", $user_merchant_order_page . "?order_id=" . $order_id);

			$t->parse("records", true);
		} while ($db->next_record());
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>