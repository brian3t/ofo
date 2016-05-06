<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_shipping_time.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("shipping_times");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_shipping_time.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_shipping_times_href", "admin_shipping_times.php");
	$t->set_var("admin_shipping_time_href", "admin_shipping_time.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", SHIPPING_TIME_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "shipping_times");
	$r->return_page = "admin_shipping_times.php";
	

	$r->add_where("shipping_time_id", INTEGER);

	$r->add_textbox("shipping_time_desc", TEXT, SHIPPING_TIME_MSG);
	$r->change_property("shipping_time_desc", REQUIRED, true);
	$r->add_textbox("availability_time", TEXT, AVAILABILITY_TIME_MSG);


	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
