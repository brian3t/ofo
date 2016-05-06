<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_state.php                                          ***
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
  $t->set_file("main","admin_state.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_states_href", "admin_states.php");
	$t->set_var("admin_state_href", "admin_state.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", STATE_FIELD, CONFIRM_DELETE_MSG));

	$countries = get_db_values("SELECT country_id,country_name FROM " . $table_prefix . "countries ORDER BY country_order, country_name ", array(array(0, SELECT_COUNTRY_MSG)));

	$r = new VA_Record($table_prefix . "states");
	$r->return_page = "admin_states.php";
	$r->add_where("state_id", TEXT);

	$r->add_checkbox("show_for_user", INTEGER);
	$r->change_property("show_for_user", DEFAULT_VALUE, 1);
	$r->add_select("country_id", INTEGER, $countries, COUNTRY_FIELD);
	$r->change_property("country_id", REQUIRED, true);
	$r->change_property("country_id", USE_SQL_NULL, false);
	$r->add_textbox("state_code", TEXT, STATE_CODE_MSG);
	$r->change_property("state_code", REQUIRED, true);
	$r->add_textbox("state_name", TEXT, STATE_NAME_MSG);
	$r->change_property("state_name", REQUIRED, true);

	$r->set_event(AFTER_UPDATE, "update_order_codes");
		
	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
	
	function update_order_codes() {
		global $r, $db, $table_prefix;
		$state_id = $r->get_value("state_id");
		$state_code = $r->get_value("state_code");
		
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET state_code=" . $db->tosql($state_code, TEXT);
		$sql .= " WHERE state_id=" . $db->tosql($state_id, INTEGER, true, false);
		$db->query($sql);
		
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET delivery_state_code=" . $db->tosql($state_code, TEXT);
		$sql .= " WHERE delivery_state_id=" . $db->tosql($state_id, INTEGER, true, false);
		$db->query($sql);
	}
?>
