<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_page.php                                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/friendly_functions.php");
	include_once("./admin_common.php");

	check_admin_security("web_pages");
	
	$operation = get_param("operation");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }

	$html_editor = get_setting_value($settings, "html_editor", 1);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$site_url_path = $settings["site_url"] ? $settings["site_url"] : "../";
	$t->set_var("css_file", $site_url_path . "styles/" . $settings["style_name"] . ".css");
	$t->set_var("html_editor", $html_editor);
	$t->set_file("main", "admin_page.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$confirm_delete = str_replace(array("{record_name}", "\'"), array(CUSTOM_PAGE_MSG, "\\'"), CONFIRM_DELETE_MSG);
	$t->set_var("confirm_delete", $confirm_delete);
	$t->set_var("admin_href", "admin.php");
	$t->set_var("page_href", "page.php");
	$t->set_var("admin_pages_href", "admin_pages.php");
	$t->set_var("admin_page_href", "admin_page.php");

	$page_id = get_param("page_id");
	
	$return_page = "admin_pages.php";
	$r = new VA_Record($table_prefix . "pages");
	$r->return_page = $return_page;

	$is_html_options = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$page_types = 
		array( 
			array(1, USUAL_WINDOW_MSG), array(2, POPUP_WINDOW_MSG)
		);

	$r->add_where("page_id", INTEGER);
	$r->change_property("page_id", USE_IN_INSERT, true);
	$r->add_textbox("page_code", TEXT, PAGE_CODE_MSG);
	$r->change_property("page_code", REQUIRED, true);
	$r->change_property("page_code", UNIQUE, true);
	$r->add_textbox("page_title", TEXT, META_TITLE_MSG);
	$r->change_property("page_title", REQUIRED, true);
	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);
	$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
	$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	$r->add_textbox("page_url", TEXT);
	$r->add_textbox("page_order", INTEGER, PAGE_ORDER_MSG);
	$r->change_property("page_order", REQUIRED, true);
	$r->change_property("page_order", DEFAULT_VALUE, get_db_value("SELECT MAX(page_order) FROM " . $table_prefix . "pages") + 1);
	$r->add_checkbox("is_showing", INTEGER);
	$r->change_property("is_showing", DEFAULT_VALUE, 1);
	$r->add_checkbox("is_site_map", INTEGER);
	$r->add_radio("is_html", INTEGER, $is_html_options);
	if ($html_editor){
		$r->change_property("is_html", SHOW, false);
	}
	$r->add_radio("page_type", INTEGER, $page_types);
	$r->change_property("page_type", DEFAULT_VALUE, 1);
	$r->add_textbox("page_path", TEXT);
	$r->add_textbox("page_body", TEXT);
	$r->add_textbox("meta_title", TEXT);
	$r->add_textbox("meta_keywords", TEXT);
	$r->add_textbox("meta_description", TEXT);
	
	$r->add_checkbox("user_types_all", INTEGER);
	$r->change_property("user_types_all", DEFAULT_VALUE, 1);
	$selected_user_types = array();
	if (strlen($operation)) {
		$user_types = get_param("user_types");
		if ($user_types) {
			$selected_user_types = split(",", $user_types);
		}
	} elseif ($page_id) {
		$sql  = "SELECT user_type_id FROM " . $table_prefix . "pages_user_types ";
		$sql .= " WHERE page_id=" . $db->tosql($page_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$selected_user_types[] = $db->f("user_type_id");
		}
	}	

	$r->add_checkbox("sites_all", INTEGER);
	$r->change_property("sites_all", DEFAULT_VALUE, 1);
	if ($sitelist) {
		$selected_sites = array();
		if (strlen($operation)) {
			$sites = get_param("sites");
			if ($sites) {
				$selected_sites = split(",", $sites);
			}
		} elseif ($page_id) {
			$sql  = "SELECT site_id FROM " . $table_prefix . "pages_sites ";
			$sql .= " WHERE page_id=" . $db->tosql($page_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$selected_sites[] = $db->f("site_id");
			}
		}
	}

	$r->set_event(BEFORE_INSERT, "set_db_values_before_changes");
	$r->set_event(BEFORE_UPDATE, "set_db_values_before_changes");
	$r->set_event(BEFORE_DELETE, "before_delete_page");
	$r->set_event(AFTER_UPDATE,  "save_other_values_after_save");
	$r->set_event(AFTER_INSERT,  "save_other_values_after_save");

	$r->get_form_values();
	if ($html_editor){
		$r->set_value("is_html", 1);
	}
	if(strlen($operation))
	{
		if($operation == "cancel") {
			header("Location: " . $return_page);
			exit;
		} //cancel
		else if($operation == "delete" && $page_id) {			
			before_delete_page();
			$r->delete_record();
			header("Location: " . $return_page);
			exit;
		} //delete

		$is_valid = $r->validate();
		
		if($is_valid) {
			if(strlen($page_id)) {
				set_friendly_url();
				set_db_values_before_changes();
				$record_updated = $r->update_record();
				save_other_values_after_save();
			} else {
				set_friendly_url();
				$db->query("SELECT MAX(page_id) FROM " . $table_prefix . "pages");
				$db->next_record();
				$page_id = $db->f(0) + 1;
				set_db_values_before_changes();
				$r->set_value("page_id", $page_id);
				$record_updated = $r->insert_record();
				save_other_values_after_save();
			}
			if ($record_updated) {
				header("Location: " . $return_page);
				exit;
			}
		}
	} else {
		$r->set_default_values();
	}
	$r->get_db_values();
	$r->set_form_parameters();
	
	$user_types = array();
	$sql = " SELECT type_id, type_name FROM " . $table_prefix . "user_types ";
	$db->query($sql);
	while ($db->next_record())	{
		$type_id = $db->f("type_id");
		$type_name = $db->f("type_name");
		$user_types[$type_id] = $type_name;
	}
	foreach($user_types as $type_id => $type_name) {
		$t->set_var("type_id", $type_id);
		$t->set_var("type_name", $type_name);
		if (in_array($type_id, $selected_user_types)) {
			$t->parse("selected_user_types", true);
		} else {
			$t->parse("available_user_types", true);
		}
	}
	if ($sitelist) {
		$sites = array();
		$sql = " SELECT site_id, site_name FROM " . $table_prefix . "sites ";
		$db->query($sql);
		while ($db->next_record())	{
			$site_id   = $db->f("site_id");
			$site_name = $db->f("site_name");
			$sites[$site_id] = $site_name;
			$t->set_var("site_id", $site_id);
			$t->set_var("site_name", $site_name);
			if (in_array($site_id, $selected_sites)) {
				$t->parse("selected_sites", true);
			} else {
				$t->parse("available_sites", true);
			}
		}
	}

	$tabs = array("general" => EDIT_PAGE_MSG, "user_types" => USERS_TYPES_MSG);
	if ($sitelist) {
		$tabs["sites"] = ADMIN_SITES_MSG;
	}
	foreach ($tabs as $tab_name => $tab_title) {
		$t->set_var("tab_id", "tab_" . $tab_name);
		$t->set_var("tab_name", $tab_name);
		$t->set_var("tab_title", $tab_title);
		if ($tab_name == $tab) {
			$t->set_var("tab_class", "adminTabActive");
			$t->set_var($tab_name . "_style", "display: block;");
		} else {
			$t->set_var("tab_class", "adminTab");
			$t->set_var($tab_name . "_style", "display: none;");
		}
		$t->parse("tabs", $tab_title);
	}
	$t->set_var("tab", $tab);	

	if(strlen($page_id)){
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	}else{
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");	
	}
	
	if ($sitelist) {
		$t->parse("sitelist");
	}

	$t->pparse("main");
	
	function set_db_values_before_changes(){
		global $db, $table_prefix, $r;
		global $page_id, $sitelist;
		if (!$sitelist) {
			$r->set_value("sites_all", 1);
		}
		set_friendly_url();
		if (!$page_id) {
			$db->query("SELECT MAX(page_id) FROM " . $table_prefix . "pages");
			$db->next_record();
			$page_id = $db->f(0) + 1;
			$r->set_value("page_id", $page_id);		
		}	
	}
	
	function before_delete_page(){
		global $db, $table_prefix;
		global $page_id;
		$db->query("DELETE FROM " . $table_prefix . "pages_sites WHERE page_id=" . $db->tosql($page_id, INTEGER));	
		$db->query("DELETE FROM " . $table_prefix . "pages_user_types WHERE page_id=" . $db->tosql($page_id, INTEGER));		
	}
	
	function save_other_values_after_save() {
		global $db, $table_prefix;
		global $page_id, $sitelist, $selected_sites, $selected_user_types;
		if ($sitelist) {
			$db->query("DELETE FROM " . $table_prefix . "pages_sites WHERE page_id=" . $db->tosql($page_id, INTEGER));
			for ($st = 0; $st < sizeof($selected_sites); $st++) {
				$site_id = $selected_sites[$st];
				if (strlen($site_id)) {
					$sql  = " INSERT INTO " . $table_prefix . "pages_sites (page_id, site_id) VALUES (";
					$sql .= $db->tosql($page_id, INTEGER) . ", ";
					$sql .= $db->tosql($site_id, INTEGER) . ") ";
					$db->query($sql);
				}
			}
		}
		$db->query("DELETE FROM " . $table_prefix . "pages_user_types WHERE page_id=" . $db->tosql($page_id, INTEGER));
		for ($ut = 0; $ut < sizeof($selected_user_types); $ut++) {
			$type_id = $selected_user_types[$ut];
			if (strlen($type_id)) {
				$sql  = " INSERT INTO " . $table_prefix . "pages_user_types (page_id, user_type_id) VALUES (";
				$sql .= $db->tosql($page_id, INTEGER) . ", ";
				$sql .= $db->tosql($type_id, INTEGER) . ") ";
				$db->query($sql);
			}
		}	
	}

?>