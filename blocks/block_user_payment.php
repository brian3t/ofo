<?php

	check_user_security("my_payments");

	$payment_id = get_param("payment_id");

  $t->set_file("block_body","block_user_payment.html");
	$t->set_var("user_payments_href", get_custom_friendly_url("user_payments.php"));
	$t->set_var("user_payment_href",  get_custom_friendly_url("user_payment.php"));
	$t->set_var("user_home_href",     get_custom_friendly_url("user_home.php"));

	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", get_custom_friendly_url("user_payments.php"));
	$s->set_default_sorting(4, "desc");
	$s->set_sorter(PROD_NAME_MSG, "sorter_item_name", "1", "uc.item_name");
	$s->set_sorter(COMMISSION_AMOUNT_COLUMN, "sorter_amount", "2", "uc.commission_amount");
	$s->set_sorter(TYPE_MSG, "sorter_type", "3", "uc.commission_type");
	$s->set_sorter(COMMISSION_DATE_COLUMN, "sorter_date", "4", "uc.date_added");

	$sql  = " SELECT uc.commission_id, oi.item_name, uc.commission_amount, uc.commission_action, uc.commission_type, uc.date_added ";
	$sql .= " FROM ((" . $table_prefix . "users_commissions uc ";
	$sql .= " INNER JOIN " . $table_prefix . "users_payments up ON uc.payment_id=up.payment_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "orders_items oi ON uc.order_item_id=oi.order_item_id) ";
	$sql .= " WHERE uc.payment_id=" . $db->tosql($payment_id, INTEGER);
	$sql .= " AND uc.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$sql .= " AND up.is_paid=1 ";
	$sql .= $s->order_by;
	$db->query($sql);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		$commissions_total = 0;
		do
		{
			$commission_id = $db->f("commission_id");
			$commission_amount = $db->f("commission_amount");
			$commission_type = $db->f("commission_type");
			$date_added = $db->f("date_added", DATETIME);
			$commissions_total += $commission_amount;
			if ($commission_type == 1) {
				$commission_type = MERCHANT_MSG;
			} else if ($commission_type == 2) {
				$commission_type = AFFILIATE_MSG;
			} else {
				$commission_type = "";
			}

			$t->set_var("commission_id", $commission_id);
			$t->set_var("item_name", $db->f("item_name"));
			$t->set_var("commission_type", $commission_type);
			$t->set_var("commission_amount", currency_format($commission_amount));

			$t->set_var("date_added", va_date($datetime_show_format, $date_added));


			$t->parse("records", true);
		} while($db->next_record());
		$t->set_var("commissions_total", currency_format($commissions_total));
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