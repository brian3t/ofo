<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_section.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("static_tables");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_user_section.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_user_sections_href", "admin_user_sections.php");
	$t->set_var("admin_user_section_href", "admin_user_section.php");

	$r = new VA_Record($table_prefix . "user_profile_sections");
	$r->return_page = "admin_user_sections.php";
	
	$r->add_where("section_id", INTEGER);
	$r->add_checkbox("is_active", INTEGER);
	$r->change_property("is_active", DEFAULT_VALUE, 1);
	$r->add_textbox("step_number", INTEGER, ADMIN_STEP_NUMBER_MSG);
	$r->change_property("step_number", MIN_VALUE, 1);
	$r->change_property("step_number", REQUIRED, true);
	$r->change_property("step_number", DEFAULT_VALUE, 1);
	$r->add_textbox("section_order", INTEGER, SECTION_ORDER_MSG);
	$r->change_property("section_order", REQUIRED, true);
	$r->add_textbox("section_name", TEXT, SECTION_NAME_MSG);
	$r->change_property("section_name", REQUIRED, true);

	$r->events[AFTER_SELECT] = "check_delete_allowed";
	$r->events[AFTER_REQUEST] = "check_delete_allowed";

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function check_delete_allowed()
	{
		global $r;
		if ($r->get_value("section_id") <= 4) {
			$r->operations[DELETE_ALLOWED] = false;
		}
		if ($r->get_value("section_id") == 1) {
			$r->change_property("step_number", MAX_VALUE, 1);
		}
	}


?>