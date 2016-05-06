<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_bookmark.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security();
	
	$title = get_param("title");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_bookmark.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_bookmark_href", "admin_bookmark.php");
	$t->set_var("admin_bookmarks_href", "admin_bookmarks.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", BOOKMARKS_MSG, CONFIRM_DELETE_MSG));
	
	$r = new VA_Record($table_prefix . "bookmarks");
	$r->add_where("bookmark_id", INTEGER);
	$r->add_textbox("admin_id", INTEGER);
	$r->change_property("admin_id", USE_IN_UPDATE, false);
	$r->add_textbox("title", TEXT);
	$r->change_property("title", REQUIRED, true);
	$r->change_property("title", DEFAULT_VALUE, $title);
	$r->add_textbox("url", TEXT);
	$r->change_property("url", REQUIRED, true);
	$r->add_textbox("image_path", TEXT);
	$r->add_textbox("notes", TEXT);
	$r->add_checkbox("is_start_page", INTEGER);
	$r->add_checkbox("is_popup", INTEGER);

	$r->events[BEFORE_INSERT] = "update_start_page";
	$r->events[BEFORE_UPDATE] = "update_start_page";

	$r->get_form_values();
//	$referer_url = $r->get_value("referer_hidden");
	$is_start_page = $r->get_value("is_start_page");
	
//	$return_page = $referer_url;
	$return_page = "admin_bookmarks.php";
	$r->return_page = $return_page;
	
	$r->process();
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
	
	function update_start_page() {
		global $r, $is_start_page, $db;
		
		$admin_id = get_session("session_admin_id");
		if ($is_start_page == 1){			
			$sql  = " UPDATE va_bookmarks SET is_start_page=0 "; 
			$sql .= " WHERE is_start_page=1 AND admin_id=" . $db->tosql($admin_id, INTEGER);
			$db->query($sql);
		}
		
		$r->set_value("admin_id", $admin_id);
	}

?>