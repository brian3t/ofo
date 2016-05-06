<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_point.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");

	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

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
	$t->set_file("main","admin_user_point.html");
	$t->set_var("user_id",           htmlspecialchars($user_id));
	$t->set_var("user_name",         htmlspecialchars($user_name));
	$t->set_var("admin_user_href",   "admin_user.php");
	$t->set_var("admin_users_href",  "admin_users.php");
	$t->set_var("admin_user_point_href",  "admin_user_point.php");
	$t->set_var("admin_user_points_href", "admin_user_points.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", POINTS_MSG, CONFIRM_DELETE_MSG));

	$points_action_values = 
		array( 
			array(-1, POINTS_SUBTRACT_MSG), array(1, ADD_POINTS_TO_BALANCE_MSG),
		);

	$r = new VA_Record($table_prefix . "users_points");
	$r->return_page = "admin_user_points.php";

	$r->add_where("points_id", INTEGER);
	$r->add_hidden("user_id", INTEGER, USER_ID_MSG);
	$r->change_property("user_id", USE_IN_INSERT, true);
	$r->change_property("user_id", DEFAULT_VALUE, $user_id);
	$r->add_hidden("order_id", INTEGER, ORDER_NUMBER_MSG);
	$r->change_property("order_id", USE_IN_INSERT, true);
	$r->change_property("order_id", TRANSFER, false);
	$r->add_hidden("order_item_id", INTEGER);
	$r->change_property("order_item_id", USE_IN_INSERT, true);
	$r->change_property("order_item_id", TRANSFER, false);
	$r->add_radio("points_action", INTEGER, $points_action_values, POINTS_ACTION_MSG);
	$r->change_property("points_action", REQUIRED, true);
	$r->add_textbox("points_amount", NUMBER, POINTS_AMOUNT_MSG);
	$r->change_property("points_amount", REQUIRED, true);
	$r->change_property("points_amount", MIN_VALUE, 0);
	$r->add_hidden("points_type", INTEGER);
	$r->change_property("points_type", USE_IN_INSERT, true);
	$r->change_property("points_type", TRANSFER, false);
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

	$r->events[BEFORE_INSERT] = "prepare_points_data";
	$r->events[BEFORE_UPDATE] = "prepare_points_data";
	$r->events[AFTER_INSERT] = "update_user_points";
	$r->events[AFTER_UPDATE] = "update_user_points";
	$r->events[AFTER_DELETE] = "update_user_points";

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function prepare_points_data()
	{
		global $r;
		$r->set_value("order_id", 0);
		$r->set_value("order_item_id", 0);
		$r->set_value("points_type", 3);
		$r->set_value("admin_id_added_by", get_session("session_admin_id"));
		$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
		$r->set_value("date_added", va_time());
		$r->set_value("date_modified", va_time());
	}

	function update_user_points()
	{
		global $r, $db, $table_prefix;

		$user_id = $r->get_value("user_id");

		$sql  = " SELECT SUM(points_action * points_amount) ";
		$sql .= " FROM " . $table_prefix . "users_points ";
		$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
		$points_total = get_db_value($sql);

		$sql  = " UPDATE " . $table_prefix . "users ";
		$sql .= " SET total_points=" . $db->tosql($points_total, NUMBER);
		$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
		$db->query($sql);
	}
?>