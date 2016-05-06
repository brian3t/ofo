<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_issue_number.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");
	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");

	check_admin_security("static_tables");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_issue_number.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_issue_numbers_href", "admin_issue_numbers.php");
	$t->set_var("admin_issue_number_href", "admin_issue_number.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CC_ISSUE_NUMBER_FIELD, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "issue_numbers");
	$r->return_page = "admin_issue_numbers.php";

	$r->add_where("issue_number", INTEGER);

	$r->add_textbox("issue_number_edit", INTEGER, CC_ISSUE_NUMBER_FIELD);
	$r->change_property("issue_number_edit", COLUMN_NAME, "issue_number");
	$r->change_property("issue_number_edit", REQUIRED, true);
	$r->change_property("issue_number_edit", UNIQUE, true);

	$r->process();


	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
