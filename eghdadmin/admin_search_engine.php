<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_search_engine.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("static_tables");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_search_engine.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_search_engines_href", "admin_search_engines.php");
	$t->set_var("admin_search_engine_href", "admin_search_engine.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", SEARCH_ENGINE_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "search_engines");
	$r->return_page = "admin_search_engines.php";

	$r->add_where("engine_id", INTEGER);

	$r->add_textbox("engine_name", TEXT, ENGINE_NAME_MSG);
	$r->change_property("engine_name", REQUIRED, true);
	$r->add_textbox("keywords_parameter", TEXT);
	$r->add_textbox("referer_regexp", TEXT);
	$r->add_textbox("user_agent_regexp", TEXT);
	$r->add_textbox("ip_regexp", TEXT);

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
