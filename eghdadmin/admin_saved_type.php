<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_saved_type.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("saved_types");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_saved_type.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_saved_type_href", "admin_saved_type.php");
	$t->set_var("admin_saved_types_href", "admin_saved_types.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CREDIT_CARD_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "saved_types");
	$r->return_page = "admin_saved_types.php";

	$r->add_where("type_id", INTEGER);

	$r->add_checkbox("is_active", INTEGER);
	$r->change_property("is_active", DEFAULT_VALUE, 1);
	$r->add_checkbox("allowed_search", INTEGER);
	$r->add_textbox("type_name", TEXT, TYPE_NAME_MSG);
	$r->change_property("type_name", REQUIRED, true);
	$r->add_textbox("type_desc", TEXT);

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
