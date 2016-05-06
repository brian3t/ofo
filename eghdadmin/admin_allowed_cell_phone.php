<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_allowed_cell_phone.php                             ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("static_tables");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_allowed_cell_phone.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_allowed_cell_phones_href", "admin_allowed_cell_phones.php");
	$t->set_var("admin_allowed_cell_phone_href", "admin_allowed_cell_phone.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CELL_PHONE_NUMBER_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "allowed_cell_phones");
	$r->return_page = "admin_allowed_cell_phones.php";
	$r->add_where("cell_phone_id", TEXT);

	$r->add_textbox("cell_phone_number", TEXT, CELL_PHONE_FIELD);
	$r->change_property("cell_phone_number", REQUIRED, true);
	$r->change_property("cell_phone_number", UNIQUE, true);
	$r->change_property("cell_phone_number", REGEXP_MASK, "/^[\d]+$/");
	$r->change_property("cell_phone_number", MIN_LENGTH, 3);
	$r->change_property("cell_phone_number", MAX_LENGTH, 15);

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
