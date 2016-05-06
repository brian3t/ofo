<?php

	check_user_security("my_orders");

	$orders_currency = get_setting_value($settings, "orders_currency", 0);

	$t->set_file("block_body","block_user_orders.html");
	$t->set_var("user_orders_href", get_custom_friendly_url("user_orders.php"));
	$t->set_var("user_order_href",  get_custom_friendly_url("user_order.php"));
	$t->set_var("user_home_href",   get_custom_friendly_url("user_home.php"));
	$t->set_var("user_order_payment_href", get_custom_friendly_url("user_order_payment.php"));
	$t->set_var("user_invoice_pdf_href",   get_custom_friendly_url("user_invoice_pdf.php"));
	$t->set_var("user_invoice_html_href",  get_custom_friendly_url("user_invoice_html.php"));

	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", get_custom_friendly_url("user_orders.php"));
	$s->set_default_sorting(1, "desc");
	$s->set_sorter(ORDER_NUMBER_COLUMN, "sorter_id", "1", "order_id");
	$s->set_sorter(ORDER_ADDED_COLUMN, "sorter_date", "2", "order_placed_date");
	$s->set_sorter(STATUS_MSG, "sorter_status", "3", "order_status");
	$s->set_sorter(ORDER_TOTAL_COLUMN, "sorter_total", "4", "order_total");

	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", get_custom_friendly_url("user_orders.php"));

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "orders o ";
	$sql .= " WHERE o.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	if (isset($site_id)) {
		$sql .= " AND o.site_id=" . $db->tosql($site_id, INTEGER, true, false);
	} else {
		$sql .= " AND o.site_id=1";
	}
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT o.order_id, o.order_placed_date, os.status_name, o.goods_total, o.order_total, o.is_placed, os.paid_status, ";
	$sql .= " o.currency_code, o.currency_rate, c.symbol_right, c.symbol_left, c.decimals_number, c.decimal_point, c.thousands_separator, ";
	$sql .= " os.user_invoice_activation ";
	$sql .= " FROM ((" . $table_prefix . "orders o ";
	$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON o.order_status=os.status_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "currencies c ON o.currency_code=c.currency_code) ";
	$sql .= " WHERE o.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	if (isset($site_id)) {
		$sql .= " AND o.site_id=" . $db->tosql($site_id, INTEGER, true, false);
	} else {
		$sql .= " AND o.site_id=1";
	}
	$db->query($sql . $s->order_by);
	if ($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$order_id = $db->f("order_id");
			$is_placed = $db->f("is_placed");
			$paid_status = $db->f("paid_status");
			$user_invoice_activation = $db->f("user_invoice_activation");
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
			$t->set_var("vc", $vc);

			$t->set_var("order_placed_date", va_date($datetime_show_format, $placed_date));

			$t->set_var("order_status", $db->f("status_name"));
			$t->set_var("order_total", currency_format($order_total, $order_currency));
			if ($is_placed || $paid_status) {
				$t->set_var("pay_link", "");
			} else {
				$t->sparse("pay_link", false);
			}

			if ($user_invoice_activation) {
				$t->sparse("invoice_links", false);
			} else {
				$t->set_var("invoice_links", "");
			}

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