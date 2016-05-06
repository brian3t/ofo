<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_friendly_url.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/friendly_functions.php");

	check_admin_security("custom_friendly_urls");
	
	$operation = get_param("operation");
	$friendly_id = get_param("friendly_id");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_friendly_url.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_friendly_urls_href", "admin_friendly_urls.php");
	$t->set_var("admin_friendly_url_href", "admin_friendly_url.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", FRIENDLY_URL_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "friendly_urls");
	$r->return_page = "admin_friendly_urls.php";
	$r->add_where("friendly_id", INTEGER);

	$r->add_textbox("script_name", TEXT, SCRIPT_NAME_MSG);
	$r->change_property("script_name", REQUIRED, true);
	$r->change_property("script_name", UNIQUE, true);

	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", REQUIRED, true);
	$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
	$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);

	$r->add_checkbox("sites_all", INTEGER);
	$r->change_property("sites_all", DEFAULT_VALUE, 1);
	if ($sitelist) {
		$selected_sites = array();
		if (strlen($operation)) {
			$sites = get_param("sites");
			if ($sites) {
				$selected_sites = split(",", $sites);
			}
		} elseif ($friendly_id) {
			$sql  = " SELECT site_id FROM " . $table_prefix . "friendly_urls_sites ";
			$sql .= " WHERE friendly_id=" . $db->tosql($friendly_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$selected_sites[] = $db->f("site_id");
			}
		}
	}
	
	$r->set_event(BEFORE_DELETE, "delete_other_values");
	$r->set_event(AFTER_UPDATE,  "save_other_values");
	$r->set_event(AFTER_INSERT,  "save_other_values");
	$r->set_event(AFTER_REQUEST,  "set_friendly_url_data");
	
	$r->process();	
	
	if ($sitelist) {
		$sql = " SELECT site_id, site_name FROM " . $table_prefix . "sites ";
		$db->query($sql);
		while ($db->next_record())	{
			$site_id   = $db->f("site_id");
			$site_name = $db->f("site_name");
			$t->set_var("site_id", $site_id);
			$t->set_var("site_name", $site_name);
			if (in_array($site_id, $selected_sites)) {
				$t->parse("selected_sites", true);
			} else {
				$t->parse("available_sites", true);
			}
		}
	}
	
	if ($sitelist) {
		$t->parse("sitelist");
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function delete_other_values() 
	{
		global $db, $friendly_id, $table_prefix;
		$db->query("DELETE FROM " . $table_prefix . "friendly_urls_sites WHERE friendly_id=" . $db->tosql($friendly_id, INTEGER));
	}

	function save_other_values() 
	{
		global $db, $friendly_id, $table_prefix, $sitelist, $selected_sites;
		// update sites
		if ($sitelist) {
			$db->query("DELETE FROM " . $table_prefix . "friendly_urls_sites WHERE friendly_id=" . $db->tosql($friendly_id, INTEGER));
			for ($st = 0; $st < sizeof($selected_sites); $st++) {
				$site_id = $selected_sites[$st];
				if (strlen($site_id)) {
					$sql  = " INSERT INTO " . $table_prefix . "friendly_urls_sites (friendly_id, site_id) VALUES (";
					$sql .= $db->tosql($friendly_id, INTEGER) . ", ";
					$sql .= $db->tosql($site_id, INTEGER) . ") ";
					$db->query($sql);
				}
			}
		}
	}

	function set_friendly_url_data()  
	{
		global $r, $sitelist;
		if (!$sitelist) {
			$r->set_value("sites_all", 1);
		}
	}

?>