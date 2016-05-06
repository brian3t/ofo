<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_banner.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/sites_table.php");

	check_admin_security("static_tables");

	$banner_id = get_param("banner_id");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_banner.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_banner_href",     "admin_banner.php");
	$t->set_var("admin_banners_href",    "admin_banners.php");
	$t->set_var("admin_select_href",     "admin_select.php");
	$t->set_var("admin_upload_href",     "admin_upload.php");
	$t->set_var("datetime_format", join("", $datetime_edit_format));
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", BANNERS_MSG, CONFIRM_DELETE_MSG));

	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$r = new VA_Record($table_prefix . "banners");
	$r->return_page = "admin_banners.php";
	if(get_param("apply")) {
		$r->redirect = false;
	}

	$r->add_where("banner_id", INTEGER);
	$r->add_checkbox("is_active", INTEGER);
	$r->change_property("is_active", DEFAULT_VALUE, 1);
	$r->add_textbox("banner_rank", INTEGER, RANK_MSG);
	$r->change_property("banner_rank", DEFAULT_VALUE, 1);
	$r->add_textbox("banner_title", TEXT, TITLE_MSG);
	$r->change_property("banner_title", REQUIRED, true);
	$r->add_checkbox("show_title", INTEGER);
	$r->add_textbox("image_src", TEXT);
	$r->add_textbox("image_alt", TEXT);
	$r->add_textbox("html_text", TEXT);
	$r->add_textbox("target_url", TEXT, TARGET_URL_MSG);
	$r->change_property("target_url", REQUIRED, true);
	$r->add_checkbox("is_new_window", INTEGER);
	$r->add_checkbox("show_on_ssl", INTEGER);
	$r->add_textbox("max_impressions", INTEGER, MAX_IMPRESSIONS_MSG);
	$r->change_property("max_impressions", DEFAULT_VALUE, 0);
	$r->change_property("max_impressions", REQUIRED, true);
	$r->add_textbox("max_clicks", INTEGER, MAX_CLICKS_MSG);
	$r->change_property("max_clicks", DEFAULT_VALUE, 0);
	$r->change_property("max_clicks", REQUIRED, true);
	$r->add_textbox("total_impressions", INTEGER);
	$r->change_property("total_impressions", DEFAULT_VALUE, 0);
	$r->change_property("total_impressions", USE_IN_UPDATE, false);
	$r->add_textbox("total_clicks", INTEGER);
	$r->change_property("total_clicks", DEFAULT_VALUE, 0);
	$r->change_property("total_clicks", USE_IN_UPDATE, false);
	$r->add_textbox("expiry_date", DATETIME, ADMIN_EXPIRY_DATE_MSG);
	$r->change_property("expiry_date", VALUE_MASK, $datetime_edit_format);	
	$r->add_checkbox("sites_all", INTEGER);
	$r->change_property("sites_all", DEFAULT_VALUE, 1);
	
	$sites_table = new VA_Sites_Table($settings["admin_templates_dir"], "sites_table.html");
	$sites_table->set_tables("banners", "banners_sites", "banner_id", false, $banner_id);
	
	$r->set_event(BEFORE_INSERT, "set_banner_id");
	$r->set_event(BEFORE_VALIDATE, "reset_defaults");
	$r->set_event(AFTER_INSERT, "after_save");
	$r->set_event(AFTER_UPDATE, "after_save");
	$r->set_event(AFTER_DELETE, "after_delete");

	$r->add_hidden("page", INTEGER);
	
	$r->process();
	
	$sites_table->parse("sites_table", $r->get_value("sites_all"));

	$t->set_var("groups", "");
	$t->set_var("selected_groups", "");

	$operation = get_param("operation");
	$selected_groups = array();
	if ($operation == "save") {
		$groups = get_param("groups");
		if($groups) {
			$selected_groups = split(",", $groups);
		}
	} else if($r->get_value("banner_id")) {
		$sql = " SELECT group_id FROM " . $table_prefix . "banners_assigned WHERE banner_id=" . $db->tosql($r->get_value("banner_id"), INTEGER);
		$db->query($sql);
		while($db->next_record())
		{
			$selected_groups[] = $db->f("group_id");
		}
	}
	
	$sql = " SELECT group_id, group_name FROM " . $table_prefix . "banners_groups ORDER BY group_id ";
	$db->query($sql);
	while($db->next_record()) {
		$group_id = $db->f("group_id");
		$t->set_var("group_id",  $group_id);
		$t->set_var("group_name", get_translation($db->f("group_name")));
		if ($selected_groups && in_array($group_id, $selected_groups)) {
			$t->parse("selected_groups", true);
		} else {
			$t->parse("available_groups", true);
		}
		
	}
	
	$tabs = array(
		"general"  => array( "title" => EDIT_BANNER_MSG),
		"groups"   => array( "title" => BANNERS_GROUPS_MSG),
		"sites"    => array( "title" => ADMIN_SITES_MSG, "show" => $sitelist)
	);
	parse_admin_tabs($tabs, $tab);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");


	$t->pparse("main");

	function set_banner_id()  {
		global $table_prefix, $r;
		$banner_id = get_db_value("SELECT MAX(banner_id) FROM " . $table_prefix . "banners") + 1;
		$r->set_value("banner_id", $banner_id);
	}
	
	function reset_defaults() {
		global $r, $sitelist;
		if (!$sitelist) {
			$r->set_value("sites_all", 1);
		}
	}

	function after_save()  {
		global $db, $table_prefix, $r, $sites_table;

		$banner_id = $r->get_value("banner_id");
		$sites_table->save_values($banner_id, $r->get_value("sites_all"), false);
		
		$db->query("DELETE FROM " . $table_prefix . "banners_assigned WHERE banner_id=" . $db->tosql($banner_id, INTEGER));

		$groups = get_param("groups");
		if (strlen($groups)) {
			$selected_groups = split(",", $groups);
			for($i = 0; $i < sizeof($selected_groups); $i++) {
				$db->query("INSERT INTO " . $table_prefix . "banners_assigned (banner_id, group_id) VALUES (" . $db->tosql($banner_id, INTEGER) . "," . $db->tosql($selected_groups[$i], INTEGER) . ")");
			}
		}
	}

	function after_delete()  {
		global $db, $table_prefix, $r;
		$banner_id = $r->get_value("banner_id");
		$sql = "DELETE FROM " . $table_prefix . "banners_assigned WHERE banner_id=" . $db->tosql($banner_id, INTEGER);
		$db->query($sql);
		$sql = "DELETE FROM " . $table_prefix . "banners_sites WHERE banner_id=" . $db->tosql($banner_id, INTEGER);
		$db->query($sql);
	}

?>