<?php

	check_user_security("my_payments");

  $t->set_file("block_body","block_user_payments.html");
	$t->set_var("user_payments_href", get_custom_friendly_url("user_payments.php"));
	$t->set_var("user_payment_href",  get_custom_friendly_url("user_payment.php"));
	$t->set_var("user_home_href",     get_custom_friendly_url("user_home.php"));

	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", get_custom_friendly_url("user_payments.php"));
	$s->set_default_sorting(4, "desc");
	$s->set_sorter(ID_MSG, "sorter_id", "1", "payment_id");
	$s->set_sorter(PAYMENT_NAME_COLUMN, "sorter_payment_name", "2", "payment_name");
	$s->set_sorter(PAYMENT_TOTAL_COLUMN, "sorter_total", "3", "payment_total");
	$s->set_sorter(PAYMENT_DATE_COLUMN, "sorter_date", "4", "date_paid");

	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", get_custom_friendly_url("user_payments.php"));

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "users_payments up ";
	$sql .= " WHERE up.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER) . " AND is_paid=1 ";
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT up.payment_id, up.payment_name, up.payment_total, up.is_paid, up.date_paid ";
	$sql .= " FROM " . $table_prefix . "users_payments up ";
	$sql .= " WHERE up.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$sql .= " AND is_paid=1 ";
	$sql .= $s->order_by;
	$db->query($sql);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$payment_total = $db->f("payment_total");
			$t->set_var("payment_id", $db->f("payment_id"));

			$date_paid = $db->f("date_paid", DATETIME);
			$t->set_var("date_paid", va_date($datetime_show_format, $date_paid));

			$t->set_var("payment_name", $db->f("payment_name"));
			$t->set_var("payment_total", currency_format($payment_total));

			$t->parse("records", true);
		} while($db->next_record());
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