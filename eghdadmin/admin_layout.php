<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_layout.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("layouts");

	$set_default_layout_id = get_param("set_default_layout_id");
	if ($set_default_layout_id) {
		$sql  = " SELECT layout_id FROM " . $table_prefix . "layouts WHERE layout_id=" . intval($set_default_layout_id);
		$db->query($sql);
		if($db->next_record()) {
			
			$sql  = " DELETE FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type='global' AND setting_name='layout_id' AND site_id=" . $db->tosql($site_id, INTEGER);
			$db->query($sql);	
			
			$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
			$sql .= "'global', 'layout_id'," . intval($set_default_layout_id) . "," . $db->tosql($site_id, INTEGER) . ")";				
			$db->query($sql);
			set_session("session_settings", "");
		}
		header("Location: admin_layouts.php");
		exit;
	}

	$top_menu_types = 
		array( 
			array(0, DONT_SHOW_LINKS_MSG), array(1, IMAGE_LINKS_FIRST_MSG), array(2, TEXT_LINKS_FIRST_MSG), array(3, IMAGE_AND_LINKS_MSG)
		);


	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_layout.html");

	$t->set_var("admin_layout_href", "admin_layout.php");
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_layouts_href", "admin_layouts.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", LAYOUT_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "layouts");
	$r->return_page = "admin_layouts.php";

	// load data to listbox
	$dir_index = 0;
	$directory_list_values[$dir_index] = array("", " --- Select from list --- ");
	$dir_index++;

	if ($dir = @opendir("../templates")) 
	{
		while ($file = readdir($dir)) 
		{
			if ($file != "." && $file != ".." && is_dir("../templates/" . $file) && $file != "admin") 
			{ 
				$directory_list_values[$dir_index] = array($file, $file);
				$dir_index++;
			} 
		}
	closedir($dir);
	}

	$r->add_where("layout_id", INTEGER);
	$r->change_property("layout_id", USE_IN_INSERT, true);
	$r->add_checkbox("show_for_user", INTEGER);               
	$r->add_textbox("layout_name", TEXT, LAYOUT_NAME_MSG);
	$r->change_property("layout_name", REQUIRED, true);
	$r->add_textbox("user_layout_name", TEXT);
	$r->add_textbox("style_name", TEXT);
	$r->add_radio("top_menu_type", INTEGER, $top_menu_types);
	$r->add_textbox("templates_dir", TEXT, TEMPLATES_DIRECTORY_MSG);
	$r->change_property("templates_dir", REQUIRED, true);
	$r->add_textbox("admin_templates_dir", TEXT, ADMIN_TEMPLATES_DIRECTORY_MSG);
	$r->change_property("admin_templates_dir", REQUIRED, true);

	set_options($directory_list_values, "", "directory_list");

	$r->add_checkbox("sites_all", INTEGER);
	
	$r->get_form_values();

	$operation = get_param("operation");
	$layout_id = get_param("layout_id");
	$return_page = "admin_layouts.php";
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }

	if ($sitelist) {
		$selected_sites = array();
		if (strlen($operation)) {
			$sites = get_param("sites");
			if ($sites) {
				$selected_sites = split(",", $sites);
			}
		} elseif ($layout_id) {
			$sql  = "SELECT site_id FROM " . $table_prefix . "layouts_sites ";
			$sql .= " WHERE layout_id=" . $db->tosql($layout_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$selected_sites[] = $db->f("site_id");
			}
		}
	}
	
	if (strlen($operation))
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		elseif ($operation == "delete" && $layout_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "layouts WHERE layout_id=" . $db->tosql($layout_id, INTEGER));		
			$db->query("DELETE FROM " . $table_prefix . "header_links WHERE layout_id=" . $db->tosql($layout_id, INTEGER));		
			$db->query("DELETE FROM " . $table_prefix . "page_settings WHERE layout_id=" . $db->tosql($layout_id, INTEGER));
			$db->query("DELETE FROM " . $table_prefix . "layouts_sites WHERE layout_id=" . $db->tosql($layout_id, INTEGER));
		
			header("Location: " . $return_page);
			exit;
		}

		$r->validate();

		if (!$r->is_empty("templates_dir") && !file_exists("../" . $r->get_value("templates_dir"))) {
			$r->errors .= FOLDER_DOESNT_EXIST_MSG . " <b>" . $r->get_value("templates_dir") . "</b><br>";
		}

		if (!$r->is_empty("admin_templates_dir") && !file_exists($r->get_value("admin_templates_dir"))) {
			$r->errors .= FOLDER_DOESNT_EXIST_MSG ." <b>" . $r->get_value("admin_templates_dir") . "</b><br>";
		}
		
		if (!strlen($r->errors))
		{
			if (!$sitelist) {
				$r->set_value("sites_all", 1);
			}
			if (strlen($r->get_value("layout_id"))) {
				$r->update_record();
				set_session("session_settings", "");
			} else {
				$db->query("SELECT MAX(layout_id) FROM " . $table_prefix . "layouts");
				$db->next_record();
				$layout_id = $db->f(0) + 1;
				$r->set_value("layout_id", $layout_id);

				$r->insert_record();
			}
			
			// update sites
			if ($sitelist) {
				$db->query("DELETE FROM " . $table_prefix . "layouts_sites WHERE layout_id=" . $db->tosql($layout_id, INTEGER));
				for ($st = 0; $st < sizeof($selected_sites); $st++) {
					$site_id = $selected_sites[$st];
					if (strlen($site_id)) {
						$sql  = " INSERT INTO " . $table_prefix . "layouts_sites (layout_id, site_id) VALUES (";
						$sql .= $db->tosql($layout_id, INTEGER) . ", ";
						$sql .= $db->tosql($site_id, INTEGER) . ") ";
						$db->query($sql);
					}
				}
			}

			header("Location: " . $return_page);
			exit;
		}
	}
	elseif (strlen($r->get_value("layout_id")))
	{
		$r->get_db_values();
	}
	else // new layout (set default values)
	{
		$r->set_value("admin_templates_dir", "../templates/admin");
		$r->set_value("top_menu_type", 1);
		$r->set_value("sites_all", 1);
	}

	$r->set_form_parameters();
	
	if (strlen($layout_id))	
	{
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	}
	else
	{
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");	
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
	
	$tabs = array("general" => EDIT_LAYOUT_MSG);
	if ($sitelist) {
		$tabs["sites"] = 'Sites';
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
	
	if ($sitelist) {
		$t->parse('sitelist');
	}
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>