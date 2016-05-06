<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_google_base_type.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	
	check_admin_security("static_google_base_types");
	
	$type_id = get_param("type_id");
			
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_google_base_type.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_google_base_type_href", "admin_google_base_type.php");
	$t->set_var("admin_google_base_types_href", "admin_google_base_types.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", PRICE_CODE_MSG, CONFIRM_DELETE_MSG));
	
	$r = new VA_Record($table_prefix . "google_base_types");
	$r->return_page  = "admin_google_base_types.php";

	$r->add_where("type_id", INTEGER);

	$r->add_textbox("type_name", TEXT, NAME_MSG);
	$r->change_property("type_name", REQUIRED, true);
	
	$r->events[BEFORE_DELETE] = "delete_type";
	
	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
	
	function delete_type() {
		global $r, $db, $table_prefix;
		$type_id = $db->tosql($r->get_value("type_id"), INTEGER, true, false);
		
		$sql = "DELETE FROM " . $table_prefix. "google_base_types_attributes WHERE type_id=" . $type_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "categories SET google_base_type_id=0 WHERE google_base_type_id=" . $type_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "items SET google_base_type_id=0 WHERE google_base_type_id=" . $type_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "item_types SET google_base_type_id=0 WHERE google_base_type_id=" . $type_id;
		$db->query($sql);
	}
	
?>