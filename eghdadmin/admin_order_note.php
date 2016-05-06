<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_note.php                                     ***
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
	check_admin_security("order_notes");

	$eol = get_eol();
	$order_id = get_param("order_id");
	$note_id = get_param("note_id");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_order_note.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_order_href", $order_details_site_url . "admin_order.php");
	$t->set_var("admin_order_note_href", "admin_order_note.php");
	$t->set_var("admin_order_notes_href", "admin_order_notes.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ORDER_NOTE_MSG, CONFIRM_DELETE_MSG)); 
 
	$r = new VA_Record($table_prefix . "orders_notes");
	$r->return_page = "admin_order_notes.php?order_id=" . $order_id;

	$r->add_where("note_id", INTEGER);
	$r->add_textbox("order_id", INTEGER);
	$r->parameters["order_id"][USE_IN_UPDATE] = false;
	$r->parameters["order_id"][REQUIRED] = true;
	$r->parameters["order_id"][DEFAULT_VALUE] = $order_id;
	$r->add_checkbox("show_for_user", INTEGER);
	$r->add_textbox("note_title", TEXT, TITLE_MSG);
	$r->parameters["note_title"][REQUIRED] = true;
	$r->add_textbox("note_details", TEXT);
	$r->add_textbox("date_added", DATETIME);
	$r->parameters["date_added"][USE_IN_UPDATE] = false;
	$r->add_textbox("date_updated", DATETIME);
	$r->add_checkbox("notify_user", INTEGER);
	$r->parameters["notify_user"][USE_IN_INSERT] = false;
	$r->parameters["notify_user"][USE_IN_UPDATE] = false;
	$r->parameters["notify_user"][USE_IN_SELECT] = false;
	$r->set_event(BEFORE_INSERT, "update_note");
	$r->set_event(BEFORE_UPDATE, "update_note");
	$r->set_event(AFTER_INSERT, "notify_user");
	$r->set_event(AFTER_UPDATE, "notify_user");

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function update_note() 
	{
		global $r;
		$r->set_value("date_added", va_time());
		$r->set_value("date_updated", va_time());
	}

	function notify_user() 
	{
		global $r, $db, $table_prefix;
		global $eol;
		if ($r->get_value("notify_user")) {
			$sql = " SELECT email FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($r->get_value("order_id"), INTEGER);
			$mail_to = get_db_value($sql);
			if(strlen($mail_to)) {
				$sql = " SELECT email FROM " . $table_prefix . "admins WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$email_headers = array();
				$email_headers["from"] = get_db_value($sql);
				$email_headers["mail_type"] = 0;
				$note_details = preg_replace("/\r\n|\r|\n/", $eol, $r->get_value("note_details"));
				$message_sent = va_mail($mail_to, $r->get_value("note_title"), $note_details, $email_headers);
			}
		}
	}

?>
