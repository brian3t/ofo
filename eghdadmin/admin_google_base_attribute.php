<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_google_base_attribute.php                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	
	check_admin_security("static_google_base_attributes");

	$attribute_types =
		array(
			array("g", GLOBAL_MSG), array("c", CUSTOM_MSG),
		);
	
	$value_types =	
		array(
			array("", ""), array("string", "string"), array("int", "int"),  array("float", "float"),
			array("intUnit", "intUnit"),  array("floatUnit", "floatUnit"),
			array("dateTime", "dateTime"), array("dateTimeRange", "dateTimeRange"),
			array("location", "location"), array("url", "url"), array("boolean", "boolean")
		);
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_google_base_attribute.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_google_base_attribute_href", "admin_google_base_attribute.php");
	$t->set_var("admin_google_base_attributes_href", "admin_google_base_attributes.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", GOOGLE_BASE_ATTRIBUTE_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "google_base_attributes");
	$r->return_page  = "admin_google_base_attributes.php";

	$r->add_where("attribute_id", INTEGER);

	$r->add_textbox("attribute_name", TEXT, NAME_MSG);
	$r->change_property("attribute_name", REQUIRED, true);
	
	$r->add_select("attribute_type", TEXT, $attribute_types, TYPE_MSG);
	$r->add_select("value_type", TEXT, $value_types, VALUE_TYPE_MSG);
	
	$r->events[BEFORE_DELETE] = "delete_type";
	
	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
	
	function delete_type()
	{
		global $r, $db, $table_prefix;
		$attribute_id = $db->tosql($r->get_value("attribute_id"), INTEGER, true, false);
		
		$sql = "DELETE FROM " . $table_prefix. "google_base_types_attributes WHERE attribute_id=" . $attribute_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "features SET google_base_attribute_id=0 WHERE google_base_attribute_id=" . $attribute_id;
		$db->query($sql);
		
		$sql = "UPDATE " . $table_prefix. "features_default SET google_base_attribute_id=0 WHERE google_base_attribute_id=" . $attribute_id;
		$db->query($sql);

	}
	
?>