<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_notes.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/sorter.php");
	include_once ($root_folder_path . "includes/navigator.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ("../messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");
	check_admin_security("order_notes");

	$order_id = get_param("order_id");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_order_notes.html");
	$t->set_var("site_url", $settings["site_url"]);
	$t->set_var("order_id", htmlspecialchars($order_id));


	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_order_notes.php");
	$s->set_sorter(ID_MSG, "sorter_note_id", "1", "note_id");
	$s->set_sorter(TITLE_MSG, "sorter_note_title", "2", "note_title");
	$s->set_sorter(SHOW_FOR_USER_MSG, "sorter_show_for_user", "3", "show_for_user");
	$s->set_sorter(DATE_ADDED_MSG, "sorter_note_date", "4", "date_added");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_order_notes.php");

	$t->set_var("admin_order_href", $order_details_site_url . "admin_order.php");
	$t->set_var("admin_order_note_href", "admin_order_note.php");
	$t->set_var("admin_order_notes_href", "admin_order_notes.php");
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_orders_href", "admin_orders.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "orders_notes WHERE order_id=" . $db->tosql($order_id, INTEGER));
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$sql  = " SELECT note_id,note_title,show_for_user,date_added FROM " . $table_prefix . "orders_notes ";
	$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
			$note_id = $db->f("note_id");
			$note_title = $db->f("note_title");
			$show_for_user = $db->f("show_for_user") ? "Yes" : "No";

			$note_date = $db->f("date_added", DATETIME);
			$t->set_var("note_date", va_date($datetime_show_format, $note_date));

			$t->set_var("note_id", $note_id);
			$t->set_var("note_title", htmlspecialchars($db->f("note_title")));
			$t->set_var("show_for_user", $show_for_user);
			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->parse("no_records", false);
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>