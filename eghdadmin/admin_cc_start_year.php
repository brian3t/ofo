<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_cc_start_year.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("static_tables");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_cc_start_year.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_cc_start_years_href", "admin_cc_start_years.php");
	$t->set_var("admin_cc_start_year_href", "admin_cc_start_year.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", YEAR_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "cc_start_years");
	$r->return_page = "admin_cc_start_years.php";

	$r->add_where("start_year", INTEGER);

	$r->add_textbox("start_year_edit", INTEGER, START_YEAR_MSG);
	$r->parameters["start_year_edit"][COLUMN_NAME] = "start_year";
	$r->parameters["start_year_edit"][REQUIRED] = true;
	$r->parameters["start_year_edit"][UNIQUE] = true;
	$r->parameters["start_year_edit"][MIN_VALUE] = 1990;
	//$r->parameters["start_year_edit"][MAX_VALUE] = date("Y");

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
