<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_serial.php                                   ***
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

	include_once($root_folder_path."messages/".$language_code."/download_messages.php");
	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
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

	$date_format_msg = str_replace("{date_format}", join("", $date_edit_format), DATE_FORMAT_MSG);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_order_serial.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_order_href", $order_details_site_url . "admin_order.php");
	$t->set_var("admin_order_serial_href", "admin_order_serial.php");
	$t->set_var("admin_order_serials_href", "admin_order_serials.php");
	$t->set_var("admin_order_activation_href", "admin_order_activation.php");
	$t->set_var("date_edit_format", join("", $date_edit_format));
	$t->set_var("date_format_msg", $date_format_msg);
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", SERIAL_NUMBER_COLUMN, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "orders_items_serials");
	$r->return_page = "admin_order_serials.php?order_id=" . $order_id;

	$items = array(array("", ""));
	$sql  = " SELECT item_id,order_item_id,item_name FROM " . $table_prefix . "orders_items oi ";
	$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$db->query($sql); 
	while ($db->next_record()) {
		$order_item_id = $db->f("order_item_id");
		$item_id = $db->f("item_id");
		$item_name = $db->f("item_name");
		if (strlen($item_name) > 100) {
			$item_name = substr($item_name, 0, 100) . "... (ID: " . $item_id . ")";
		}
		$items[] = array($order_item_id, $item_name);
	}

	$sql  = " SELECT user_id FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$user_id = get_db_value($sql);

	$defaul_serial_number = "";
	if (!strlen($serial_id) && !strlen($operation)) {
		while ($defaul_serial_number == "") 
		{
			$random_value  = mt_rand();
			$serial_hash   = strtoupper(md5($order_id . $random_value . va_timestamp()));
			$defaul_serial_number = substr($serial_hash,0,4)."-".substr($serial_hash,4,4)."-".substr($serial_hash,8,4)."-".substr($serial_hash,12,4);
			$sql = " SELECT serial_id FROM " .$table_prefix. "orders_items_serials WHERE serial_number=" . $db->tosql($defaul_serial_number, TEXT);
			$db->query($sql);
			if ($db->next_record()) {
				$defaul_serial_number = "";
			}
		}
	}


	$r->add_where("serial_id", INTEGER);
	$r->add_textbox("order_id", INTEGER);
	$r->parameters["order_id"][USE_IN_UPDATE] = false;
	$r->parameters["order_id"][REQUIRED] = true;
	$r->parameters["order_id"][DEFAULT_VALUE] = $order_id;
	$r->add_textbox("user_id", INTEGER);
	$r->parameters["user_id"][USE_IN_UPDATE] = false;
	$r->parameters["user_id"][REQUIRED] = true;
	$r->parameters["user_id"][DEFAULT_VALUE] = $user_id;
	$r->add_checkbox("activated", INTEGER);
	$r->add_select("order_item_id", INTEGER, $items, PRODUCT_MSG);
	$r->parameters["order_item_id"][REQUIRED] = true;
	$r->add_textbox("item_id", INTEGER);
	$r->add_textbox("serial_number", TEXT, SERIAL_NUMBER_COLUMN);
	$r->parameters["serial_number"][REQUIRED] = true;
	$r->parameters["serial_number"][UNIQUE] = true;
	$r->parameters["serial_number"][DEFAULT_VALUE] = $defaul_serial_number;
	$r->add_textbox("serial_added", DATETIME, DATE_ADDED_MSG);
	$r->change_property("serial_added", USE_IN_UPDATE, false);
	$r->change_property("serial_added", SHOW, false);
	$r->parameters["serial_added"][REQUIRED] = true;
	$r->parameters["serial_added"][VALUE_MASK] = $datetime_edit_format;
	$r->parameters["serial_added"][DEFAULT_VALUE] = va_time();
	$r->add_textbox("serial_expiry", DATETIME, EXPIRY_DATE_MSG);
	$r->parameters["serial_expiry"][VALUE_MASK] = $date_edit_format;
	$r->add_textbox("activations_number", INTEGER);
	$r->set_event(BEFORE_INSERT, "set_serial_params");
	$r->set_event(BEFORE_UPDATE, "set_serial_params");

	$r->process();

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_order_serial.php");
	$s->set_default_sorting(2, "desc");
	$s->set_sorter(GENERATION_KEY_MSG, "sorter_generation_key", 1, "generation_key");
	$s->set_sorter(ACTIVATION_KEY_MSG, "sorter_activation_key", 2, "activation_key");
	$s->set_sorter(DATE_MSG, "sorter_date_added", 3, "date_added");
	$s->set_sorter(REMOTE_ADDRESS_MSG, "sorter_remote_address", 4, "remote_address");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_order_serial.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "orders_serials_activations WHERE serial_id=" . $db->tosql($serial_id, INTEGER));
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "orders_serials_activations WHERE serial_id=" . $db->tosql($serial_id, INTEGER) . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		$t->parse("sorters", false);
		do
		{
			$date_added = $db->f("date_added", DATETIME);
			$t->set_var("date_added", va_date($datetime_show_format, $date_added));
			$t->set_var("activation_id", $db->f("activation_id"));
			$t->set_var("remote_address", $db->f("remote_address"));
			$t->set_var("generation_key", $db->f("generation_key"));
			$t->set_var("activation_key", $db->f("activation_key"));
			$t->set_var("remote_address", $db->f("remote_address"));

			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	if ($serial_id) {
		$t->parse("add_activation", false);
	}


	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function set_serial_params() {
		global $db, $r;
		global $table_prefix;

		$r->set_value("serial_added", va_time());
		$order_item_id = $r->get_value("order_item_id");
		$sql = " SELECT item_id FROM " . $table_prefix . "orders_items WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			$r->set_value("item_id", $db->f("item_id"));
		}
	}

?>