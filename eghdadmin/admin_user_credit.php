<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_credit.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path."includes/common.php");
	include_once ($root_folder_path."includes/record.php");
	include_once ($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once ("./admin_common.php");

	check_admin_security("site_users");

	$user_id = get_param("user_id");

	$sql  = " SELECT login,password,name,first_name,last_name FROM " . $table_prefix . "users ";
	$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
	$db->query($sql);
	if($db->next_record()) {
		$login = $db->f("login");
		$current_password = $db->f("password");
		$user_name = $db->f("name");
		if (!$user_name) {
			$user_name = trim($db->f("first_name") . " " . $db->f("last_name"));
		}
		if (!$user_name) {
			$user_name = $login;
		}
	} else {
		header("Location: admin_users.php");
		exit;
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_user_credit.html");
	$t->set_var("user_id",           htmlspecialchars($user_id));
	$t->set_var("user_name",         htmlspecialchars($user_name));
	$t->set_var("admin_user_href",   "admin_user.php");
	$t->set_var("admin_users_href",  "admin_users.php");
	$t->set_var("admin_user_credit_href",  "admin_user_credit.php");
	$t->set_var("admin_user_credits_href", "admin_user_credits.php");

	$credit_action_values = 
		array( 
			array(-1, SUBSTRACT_CREDIT_AMOUNT_MSG), array(1, ADD_CREDIT_AMOUNT_MSG),
		);

	$r = new VA_Record($table_prefix . "users_credits");
	$r->return_page = "admin_user_credits.php";

	$r->add_where("credit_id", INTEGER);
	$r->add_hidden("user_id", INTEGER, USER_ID_MSG);
	$r->change_property("user_id", USE_IN_INSERT, true);
	$r->change_property("user_id", DEFAULT_VALUE, $user_id);
	$r->add_hidden("order_id", INTEGER, ORDER_NUMBER_MSG);
	$r->change_property("order_id", USE_IN_INSERT, true);
	$r->change_property("order_id", TRANSFER, false);
	$r->add_hidden("order_item_id", INTEGER);
	$r->change_property("order_item_id", USE_IN_INSERT, true);
	$r->change_property("order_item_id", TRANSFER, false);
	$r->add_radio("credit_action", INTEGER, $credit_action_values, CREDIT_ACTION_MSG);
	$r->change_property("credit_action", REQUIRED, true);
	$r->add_textbox("credit_amount", NUMBER, CREDIT_AMOUNT_MSG);
	$r->change_property("credit_amount", REQUIRED, true);
	$r->change_property("credit_amount", MIN_VALUE, 0);
	$r->add_hidden("credit_type", INTEGER);
	$r->change_property("credit_type", USE_IN_INSERT, true);
	$r->change_property("credit_type", TRANSFER, false);
	$r->add_hidden("admin_id_added_by", INTEGER);
	$r->change_property("admin_id_added_by", USE_IN_INSERT, true);
	$r->change_property("admin_id_added_by", TRANSFER, false);
	$r->add_hidden("admin_id_modified_by", INTEGER);
	$r->change_property("admin_id_modified_by", USE_IN_UPDATE, true);
	$r->change_property("admin_id_modified_by", TRANSFER, false);
	$r->add_hidden("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_INSERT, true);
	$r->change_property("date_added", TRANSFER, false);
	$r->add_hidden("date_modified", DATETIME);
	$r->change_property("date_modified", USE_IN_UPDATE, true);
	$r->change_property("date_modified", TRANSFER, false);

	$r->events[BEFORE_INSERT] = "prepare_credits_data";
	$r->events[BEFORE_UPDATE] = "prepare_credits_data";
	$r->events[AFTER_INSERT] = "update_user_credits";
	$r->events[AFTER_UPDATE] = "update_user_credits";
	$r->events[AFTER_DELETE] = "update_user_credits";

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function prepare_credits_data()
	{
		global $r;
		$r->set_value("order_id", 0);
		$r->set_value("order_item_id", 0);
		$r->set_value("credit_type", 3);
		$r->set_value("admin_id_added_by", get_session("session_admin_id"));
		$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
		$r->set_value("date_added", va_time());
		$r->set_value("date_modified", va_time());
	}

	function update_user_credits()
	{
		global $r, $db, $table_prefix;

		$user_id = $r->get_value("user_id");

		$sql  = " SELECT SUM(credit_action * credit_amount) ";
		$sql .= " FROM " . $table_prefix . "users_credits ";
		$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
		$credits_total = get_db_value($sql);

		$sql  = " UPDATE " . $table_prefix . "users ";
		$sql .= " SET credit_balance=" . $db->tosql($credits_total, NUMBER);
		$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
		$db->query($sql);
	}
?>