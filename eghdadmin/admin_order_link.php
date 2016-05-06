<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_link.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/sorter.php");
	include_once ($root_folder_path . "includes/navigator.php");
	include_once ($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once ($root_folder_path . "messages/".$language_code."/download_messages.php");

	include_once("./admin_common.php");

	check_admin_security("sales_orders");
	check_admin_security("order_links");

	$order_id = get_param("order_id");
	$download_id = get_param("download_id");

	$date_format_msg = str_replace("{date_format}", join("", $date_edit_format), DATE_FORMAT_MSG);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_order_link.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_order_href", $order_details_site_url . "admin_order.php");
	$t->set_var("admin_order_link_href", "admin_order_link.php");
	$t->set_var("admin_order_links_href", "admin_order_links.php");
	$t->set_var("date_edit_format", join("", $date_edit_format));
	$t->set_var("date_format_msg", $date_format_msg);
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", LINK_URL_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "items_downloads");
	$r->return_page = "admin_order_links.php?order_id=" . $order_id;

	$items = array();
	$items[] = array("", "");
	$sql  = " SELECT order_item_id,item_name FROM " . $table_prefix . "orders_items oi ";
	$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$db->query($sql);
	while ($db->next_record()) {
		$order_item_id = $db->f("order_item_id");
		$item_name= get_translation($db->f("item_name"));
		if (strlen($item_name) > 100) {
			$item_name = substr($item_name, 0, 100) . " ... ";
		}
		$items[] = array($order_item_id, $item_name);
	}

	$sql  = " SELECT user_id FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$user_id = get_db_value($sql);

	$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='download_info' ";
	$sql .= " AND setting_name='max_downloads' ";
	if ($multisites_version) {
		$sql2  = " SELECT site_id FROM " . $table_prefix . "orders ";
		$sql2 .= " WHERE order_id=" . $db->tosql($order_id,INTEGER); 
		$order_site_id = get_db_value($sql2);
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($order_site_id,INTEGER) . ") ";
		$sql .= " ORDER BY site_id DESC ";
	}
	$max_downloads = get_db_value($sql);


	$r->add_where("download_id", INTEGER);
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
	$r->add_textbox("download_notes", TEXT);
	$r->add_textbox("download_path", TEXT);
	$r->add_textbox("download_added", DATETIME, DATE_ADDED_MSG);
	$r->change_property("download_added", USE_IN_UPDATE, false);
	$r->change_property("download_added", SHOW, false);
	$r->parameters["download_added"][REQUIRED] = true;
	$r->parameters["download_added"][VALUE_MASK] = $datetime_edit_format;
	$r->parameters["download_added"][DEFAULT_VALUE] = va_time();
	$r->add_textbox("download_expiry", DATETIME, EXPIRY_DATE_MSG);
	$r->parameters["download_expiry"][VALUE_MASK] = $date_edit_format;
	$r->add_textbox("max_downloads", INTEGER, MAXIMUM_DOWNLOADS_MSG);
	$r->parameters["max_downloads"][DEFAULT_VALUE] = $max_downloads;
	$r->add_textbox("download_limit", INTEGER, DOWNLOAD_LIMIT_MSG);
	$r->set_event(BEFORE_INSERT, "get_download_item_id");
	$r->set_event(BEFORE_UPDATE, "get_download_item_id");

	$r->process();

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_order_link.php");
	$s->set_default_sorting(1, "desc");
	$s->set_sorter(DOWNLOAD_DATE_MSG, "sorter_downloaded_date", 1, "downloaded_date");
	$s->set_sorter(REMOTE_ADDRESS_MSG, "sorter_remote_address", 2, "remote_address");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_order_link.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "items_downloads_statistic WHERE download_id=" . $db->tosql($download_id, INTEGER));
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "items_downloads_statistic WHERE download_id=" . $db->tosql($download_id, INTEGER) . $s->order_by);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		$t->parse("sorters", false);
		do
		{
			$downloaded_date = $db->f("downloaded_date", DATETIME);
			$t->set_var("downloaded_date", va_date($datetime_show_format, $downloaded_date));
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

	$t->pparse("main");

	function get_download_item_id() {
		global $db, $r;
		global $table_prefix;

		$r->set_value("download_added", va_time());
		$order_item_id = $r->get_value("order_item_id");
		$sql = " SELECT item_id FROM " . $table_prefix . "orders_items WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			$r->set_value("item_id", $db->f("item_id"));
		}
	}

?>