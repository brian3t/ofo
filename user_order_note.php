<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  user_order_note.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");
	include_once("./messages/" . $language_code . "/cart_messages.php");

	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main","user_order_note.html");
	$t->set_var("CHARSET", CHARSET);
	$t->set_var("ORDER_NOTE_MSG",    ORDER_NOTE_MSG);
	$t->set_var("CLOSE_WINDOW_MSG",  CLOSE_WINDOW_MSG);

	$order_id = get_param("order_id");
	$note_id  = get_param("note_id");
	$user_id  = get_session("session_user_id");

	$sql  = " SELECT n.note_title,n.note_details,n.date_added ";
	$sql .= " FROM " . $table_prefix . "orders_notes n, ";
	$sql .= $table_prefix . "orders o ";
	$sql .= " WHERE n.order_id=o.order_id ";
	$sql .= " AND o.order_id=" . $db->tosql($order_id, INTEGER);
	$sql .= " AND o.user_id=" . $db->tosql($user_id, INTEGER);
	$sql .= " AND n.show_for_user=1 ";
	$sql .= " AND n.note_id=" . $db->tosql($note_id, INTEGER);;
	$db->query($sql);
	if ($db->next_record()) {
		$note_title = $db->f("note_title");
		$note_details = $db->f("note_details");
		$note_date = $db->f("date_added", DATETIME);

		$t->set_var("note_title", $note_title);
		$t->set_var("note_details", nl2br(htmlspecialchars($note_details)));
		$t->set_var("note_date", va_date($datetime_show_format, $note_date));
	}


	$t->pparse("main");

?>