<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_payment.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/sorter.php");
	include_once ($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once ($root_folder_path . "messages/".$language_code."/download_messages.php");

	include_once("./admin_common.php");

	check_admin_security("users_payments");

	$operation = get_param("operation");
	$payment_id = get_param("payment_id");
	$payment_user_id = get_param("payment_user_id");
	$commissions_number = get_param("commissions_number");
	$commissions = array();
	$permissions = get_permissions();
	$add_payments = get_setting_value($permissions, "add_payments", 0);
	$update_payments = get_setting_value($permissions, "update_payments", 0);
	$remove_payments = get_setting_value($permissions, "remove_payments", 0);
	if (strlen($operation)) {
		$all_commissions_checked = " checked ";
		for ($i = 1; $i <= $commissions_number; $i++) {
			$commission_id = get_param("commission_id_" . $i);
			if ($commission_id) {
				$commissions[] = $commission_id;
			} else {
				$all_commissions_checked = "";
			}
		}
	} else {
		$all_commissions_checked = " checked ";
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_user_payment.html");

	$t->set_var("currency_left", $currency["left"]);
	$t->set_var("currency_right", $currency["right"]);
	$t->set_var("currency_rate", $currency["rate"]);
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_user_payments_href", "admin_user_payments.php");
	$t->set_var("admin_user_payment_href",  "admin_user_payment.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_PAYMENT_MSG, CONFIRM_DELETE_MSG));

	$paid_options = array(array(1, PAID_MSG), array(0, NOT_PAID_MSG));

	$r = new VA_Record($table_prefix . "users_payments");
	$r->return_page = "admin_user_payments.php";

	$r->add_where("payment_id", INTEGER);

	$r->add_textbox("user_id", INTEGER, USER_ID_MSG);
	$r->change_property("user_id", USE_IN_UPDATE, false);
	if ($payment_id || $payment_user_id) {
		$r->change_property("user_id", SHOW, false);
	} 
	if (!$payment_id) {
		$r->change_property("user_id", REQUIRED, true);
	}

	$r->add_radio("is_paid", INTEGER, $paid_options);
	$r->change_property("is_paid", DEFAULT_VALUE, 0);

	$r->add_textbox("transaction_id", TEXT);
	$r->add_textbox("payment_total", NUMBER, PAYMENT_AMOUNT_MSG);
	$r->change_property("payment_total", REQUIRED, true);
	if (!$payment_id && $payment_user_id) {
		$r->change_property("payment_total", SHOW, false);
	}
	$r->add_textbox("payment_name", TEXT, PAYMENT_NAME_COLUMN);
	$r->change_property("payment_name", REQUIRED, true);
	$r->add_textbox("payment_notes", TEXT);

	// editing information
	$r->add_textbox("admin_id_added_by", INTEGER);
	$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_modified", DATETIME);
	$r->add_textbox("date_paid", DATETIME);

	$r->events[AFTER_REQUEST] = "set_payment_fields";
	$r->events[AFTER_DELETE] = "clear_payment_commissions";

	$r->events[BEFORE_INSERT] = "before_insert_payment";
	$r->events[AFTER_INSERT] = "after_insert_payment";

	$r->operations[INSERT_ALLOWED] = $add_payments;
	$r->operations[UPDATE_ALLOWED] = $update_payments;
	$r->operations[DELETE_ALLOWED] = $remove_payments;
	$r->process();

	$index = 0;
	if ($payment_id || $payment_user_id) {
		if ($payment_id) {
			$user_id = $r->get_value("user_id");
		} else {
			$user_id = $payment_user_id;
		}
		$sql  = " SELECT u.login, u.name, u.first_name, u.last_name, u.paypal_account ";
		$sql .= " FROM " . $table_prefix . "users u ";
		$sql .= " WHERE u.user_id=" . $db->tosql($user_id, INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			$user_name = $db->f("name");
			if(!strlen($user_name)) {
				$user_name = $db->f("first_name") . " " . $db->f("last_name");
			}
			if(!strlen($user_name)) {
				$user_name = $db->f("login");
			}
			$user_name .= " (id: " . $user_id . ")";
			$t->set_var("user_name", $user_name);
			$t->parse("user_block", false);

			$paypal_account = $db->f("paypal_account");
			$t->set_var("paypal_account", $paypal_account);
			$t->parse("paypal_account_block", false);
		} else {
			$payment_user_id = "";
		}

		$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_user_payment.php");
		$s->set_default_sorting(4, "asc");
		$s->set_sorter(PROD_NAME_MSG, "sorter_item_name", 1, "oi.item_name");
		$s->set_sorter(COMMISSION_AMOUNT_COLUMN, "sorter_commission_amount", 2, "uc.commission_amount");
		$s->set_sorter(TYPE_MSG, "sorter_commission_type", 3, "uc.commission_type");
		$s->set_sorter(COMMISSION_DATE_COLUMN, "sorter_commission_added", 4, "uc.date_added");

		// check for available commissions
		$commission_start = va_timestamp(); $commission_end = 0;
		$sql  = " SELECT uc.commission_id, oi.item_name, uc.commission_amount, uc.commission_action, uc.commission_type, uc.date_added ";
		$sql .= " FROM (" . $table_prefix . "users_commissions uc ";
		$sql .= " LEFT JOIN " . $table_prefix . "orders_items oi ON uc.order_item_id=oi.order_item_id) ";
		if ($payment_id) {
			$sql .= " WHERE uc.payment_id=" . $db->tosql($payment_id, INTEGER);
		} else {
			$sql .= " WHERE uc.payment_id=0 ";
			$sql .= " AND uc.user_id=" . $db->tosql($payment_user_id, INTEGER);
		}
		$sql .= $s->order_by;
		$db->query($sql);
		if ($db->next_record()) {
			$total_amount = 0; 
			if ($payment_user_id) {
				$t->set_var("all_commissions_checked", $all_commissions_checked);
				$t->parse("all_commissions_block", false);
			}
			$t->parse("sorters", false);
			do {
				$index++;
				$commission_id = $db->f("commission_id");
				$item_name = $db->f("item_name");
				$commission_amount = $db->f("commission_amount");
				$commission_action = $db->f("commission_action");
				$commission_type = $db->f("commission_type");
				$commission_added = $db->f("date_added", DATETIME);
				$date_added_ts = mktime ($commission_added[HOUR], $commission_added[MINUTE], $commission_added[SECOND], $commission_added[MONTH], $commission_added[DAY], $commission_added[YEAR]);
				if ($date_added_ts > $commission_end) {
					$commission_end = $date_added_ts;
				}
				if ($date_added_ts < $commission_start) {
					$commission_start = $date_added_ts;
				}

				$commission_checked = "";
				if (strlen($operation)) {
					if (in_array($commission_id, $commissions)) {
						$commission_checked = " checked ";
						$total_amount += ($commission_amount * $commission_action);
					} 
				} else {
					$commission_checked = " checked ";
					$total_amount += ($commission_amount * $commission_action);
				}

				if ($commission_type == 1) {
					$commission_type = MERCHANT_MSG;
				} else if ($commission_type == 2) {
					$commission_type = AFFILIATE_MSG;
				} else {
					$commission_type = "";
				}

				$t->set_var("index", $index);
				$t->set_var("commission_id", $commission_id);
				$t->set_var("item_name", $item_name);
				$t->set_var("commission_checked", $commission_checked);
				$t->set_var("commission_amount_value", ($commission_action * $commission_amount));
				if ($commission_action > 0) {
					$t->set_var("commission_amount", currency_format($commission_amount));
				} else if ($commission_action < 0) {
					$t->set_var("commission_amount", "- " . currency_format($commission_amount));
				} else {
					$t->set_var("commission_amount", "");
				}
				$t->set_var("commission_type", $commission_type);
				$t->set_var("commission_added", va_date($datetime_show_format, $commission_added));
				if ($payment_user_id) {
					$t->parse("commission_checkbox", false);
				}

				$t->parse("records", true);
			} while ($db->next_record());
			if ($payment_user_id) {
				$t->parse("commission_footer", false);
			}

			$t->set_var("total_amount", currency_format($total_amount));
			$t->parse("commission_info", false);
		}

		if (!$payment_id && $payment_user_id) {
			$t->set_var("payment_name", va_date($datetime_show_format, $commission_start) . " - " . va_date($datetime_show_format, $commission_end));
			$t->sparse("payment_amount", false);

		}
	}	

	$t->set_var("payment_user_id", htmlspecialchars($payment_user_id));
	$t->set_var("commissions_number", $index);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function set_payment_fields()
	{
		global $r, $db, $table_prefix, $payment_user_id;

		$current_date = va_time();
		$r->set_value("date_added", $current_date);
		$r->set_value("date_modified", $current_date);
		$payment_id = $r->get_value("payment_id");
		$date_paid = "";
		if ($payment_id) {
			$sql = " SELECT date_paid FROM " . $table_prefix . "users_payments WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
			$date_paid = get_db_value($sql);
		}
		if ($r->get_value("is_paid") == 1 && !strlen($date_paid)) {
			$r->set_value("date_paid", $current_date);
		} else if ($r->get_value("is_paid") == 1) {
			$r->change_property("date_paid", USE_IN_UPDATE, false);
		}
		if (!$payment_id && $payment_user_id) {
			$r->set_value("user_id", $payment_user_id);
			$r->set_value("payment_total", 0);
		}

		$r->set_value("admin_id_added_by", get_session("session_admin_id"));
		$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
	}

	function clear_payment_commissions()
	{
		global $r, $db, $table_prefix;

		$payment_id = $r->get_value("payment_id");		
		if ($payment_id) {
			$sql  = " UPDATE " . $table_prefix . "users_commissions ";
			$sql .= " SET payment_id=0";
			$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
			$db->query($sql);
		}
	}

	function before_insert_payment()
	{
		global $r, $db, $table_prefix, $db_type, $payment_user_id;

		if ($db_type == "postgre") {
			$payment_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "users_payments') ");
			$r->change_property("payment_id", USE_IN_INSERT, true);
			$r->set_value("payment_id", $payment_id);
		}
		if ($payment_user_id) {
			$r->set_value("user_id", $payment_user_id);
		}
	}

	function after_insert_payment()
	{
		global $r, $db, $table_prefix, $db_type, $commissions, $payment_user_id;
		if ($db_type == "mysql") {
			$payment_id = get_db_value(" SELECT LAST_INSERT_ID() ");
			$r->set_value("payment_id", $payment_id);
		} else if ($db_type == "access") {
			$payment_id = get_db_value(" SELECT @@IDENTITY ");
			$r->set_value("payment_id", $payment_id);
		} else if ($db_type == "db2") {
			$payment_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "users_payments FROM " . $table_prefix . "users_payments");
			$r->set_value("payment_id", $payment_id);
		}
		// update commissions
		if (sizeof($commissions) > 0) {
			$payment_id = $r->get_value("payment_id");
			$commissions_ids = implode(",", $commissions);		
			$sql  = " UPDATE " . $table_prefix . "users_commissions ";
			$sql .= " SET payment_id=" . $db->tosql($payment_id, INTEGER);
			$sql .= " WHERE payment_id=0 AND user_id=" . $db->tosql($payment_user_id, INTEGER);
			$sql .= " AND commission_id IN (" . $commissions_ids . ")";
			$db->query($sql);

			// check and update total amount for generated payment if it was change
			$sql  = " SELECT SUM(commission_action * commission_amount) ";
			$sql .= " FROM " . $table_prefix . "users_commissions ";
			$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
			$payment_total = get_db_value($sql);

			$sql  = " UPDATE " . $table_prefix . "users_payments ";
			$sql .= " SET payment_total=" . $db->tosql($payment_total, NUMBER);
			$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
			$db->query($sql);
		}

	}

?>