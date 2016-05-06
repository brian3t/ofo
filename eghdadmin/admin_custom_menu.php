<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_custom_menu.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("layouts");
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_custom_menu.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_custom_menu_href", "admin_custom_menu.php");
	$t->set_var("admin_custom_menus_href", "admin_custom_menus.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_MENU_ITEM_MSG, CONFIRM_DELETE_MSG));
	
	$r = new VA_Record($table_prefix . "menus");
	$r->return_page = "admin_custom_menus.php";

	$r->add_where("menu_id", INTEGER);
	$r->add_checkbox("show_title", INTEGER, SHOW_MENU_TITLE_MSG);
	$r->add_textbox("menu_title", TEXT, MENU_TITLE_MSG);
	$r->change_property("menu_title", REQUIRED, true);
	$r->add_textbox("menu_name", TEXT, NAME_MSG);
	$r->change_property("menu_name", REQUIRED, true);
	$r->add_textbox("menu_notes", TEXT, NOTES_MSG);
	$r->set_event(BEFORE_DELETE, "delete_menu_items");

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
	
	/**
	 * Remove items of the removed menu
	 *
	 */
	function delete_menu_items() {
		global $db, $r, $table_prefix;
		$menu_id = $r->get_value("menu_id");
		if (intval($menu_id) > 0) {
			$sql = "DELETE FROM ".$table_prefix."menus_items ";
			$sql .= "WHERE menu_id = ".$db->tosql($menu_id, INTEGER);
			
			$db->query($sql);
		}
	}
?>