<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_activation.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once("./admin_common.php");

	check_admin_security("sales_orders");
	check_admin_security("order_serials");

	$operation = get_param("operation");
	$order_id  = get_param("order_id");
	$serial_id = get_param("serial_id");
	if (strlen($serial_id) && !strlen($order_id)) {
		$sql  = " SELECT order_id FROM " . $table_prefix . "orders_items_serials WHERE serial_id=" . $db->tosql($serial_id, INTEGER);
		$order_id = get_db_value($sql);
	}
	if (!strlen($serial_id)) {
		header("Location: admin_orders.php");
		exit;
	}
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", SERIAL_NUMBER_COLUMN, CONFIRM_DELETE_MSG));

	$oe = new VA_Record($table_prefix . "orders_events");
	$oe->add_textbox("order_id", INTEGER);
	$oe->add_textbox("status_id", INTEGER);
	$oe->add_textbox("admin_id", INTEGER);
	$oe->add_textbox("order_items", TEXT);
	$oe->add_textbox("event_date", DATETIME);
	$oe->add_textbox("event_type", TEXT);
	$oe->add_textbox("event_name", TEXT);
	$oe->add_textbox("event_description", TEXT);
	$oe->set_value("order_id", $order_id);
	$oe->set_value("status_id", 0);
	$oe->set_value("admin_id", get_session("session_admin_id"));
	$oe->set_value("event_date", va_time());

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_order_activation.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_order_href", $order_details_site_url . "admin_order.php");
	$t->set_var("admin_order_serial_href", "admin_order_serial.php");
	$t->set_var("admin_order_serials_href", "admin_order_serials.php");
	$t->set_var("admin_order_activation_href", "admin_order_activation.php");

	$r = new VA_Record($table_prefix . "orders_serials_activations");
	$r->return_page = "admin_order_serial.php?order_id=" . $order_id . "&serial_id=" . $serial_id;

	$r->add_where("activation_id", INTEGER);
	$r->add_textbox("serial_id", INTEGER);
	$r->parameters["serial_id"][USE_IN_UPDATE] = false;
	$r->parameters["serial_id"][REQUIRED] = true;
	$r->parameters["serial_id"][DEFAULT_VALUE] = $serial_id;
	$r->add_textbox("order_id", INTEGER);
	$r->parameters["order_id"][USE_IN_UPDATE] = false;
	$r->parameters["order_id"][REQUIRED] = true;
	$r->parameters["order_id"][DEFAULT_VALUE] = $order_id;

	$r->add_textbox("generation_key", TEXT, GENERATION_KEY_MSG);
	$r->change_property("generation_key", REQUIRED, true);
	$r->add_textbox("activation_key", TEXT, ACTIVATION_KEY_MSG);

	$r->add_textbox("remote_address", TEXT, REMOTE_ADDRESS_MSG);
	$r->parameters["remote_address"][USE_IN_UPDATE] = false;
	$r->add_textbox("date_added", DATETIME, DATE_ADDED_MSG);
	$r->parameters["date_added"][VALUE_MASK] = $datetime_edit_format;
	$r->set_event(BEFORE_INSERT, "set_activation_fields");
	$r->set_event(BEFORE_UPDATE, "update_activation");
	$r->set_event(BEFORE_DELETE, "delete_activation");

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function set_activation_fields() {
		global $db, $r, $oe;

		$r->set_value("remote_address", get_ip());
		$r->set_value("date_added", va_time());

		$oe->set_value("event_type", "activation_added");
		$oe->set_value("event_name", $r->get_value("generation_key"));
		$oe->insert_record();
	}

	function update_activation() {
		global $db, $r, $table_prefix, $oe;

		$new_generation_key = $r->get_value("generation_key");
		$activation_id = $r->get_value("activation_id");
		$sql  = " SELECT generation_key FROM " . $table_prefix . "orders_serials_activations ";
		$sql .= " WHERE activation_id=" . $db->tosql($activation_id, INTEGER);
		$old_generation_key = get_db_value($sql);
	
		$oe->set_value("event_type", "activation_updated");
		$oe->set_value("event_name", $old_generation_key . " &ndash;&gt; " . $new_generation_key);
		$oe->insert_record();
	}

	function delete_activation() {
		global $db, $r, $table_prefix, $oe;

		$activation_id = $r->get_value("activation_id");
		$sql  = " SELECT generation_key FROM " . $table_prefix . "orders_serials_activations ";
		$sql .= " WHERE activation_id=" . $db->tosql($activation_id, INTEGER);
		$old_generation_key = get_db_value($sql);
	
		$oe->set_value("event_type", "activation_removed");
		$oe->set_value("event_name", $old_generation_key);
		$oe->insert_record();
	}

?>