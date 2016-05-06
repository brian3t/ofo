<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_icon.php                                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/friendly_functions.php");

	include_once("./admin_common.php");

	check_admin_security("static_tables");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_icon.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_icons_href", "admin_icons.php");
	$t->set_var("admin_icon_href", "admin_icon.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");
	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$r = new VA_Record($table_prefix . "icons");
	$r->return_page = "admin_icons.php";

	$r->add_where("icon_id", INTEGER);

	$r->add_checkbox("is_active", INTEGER);
	$r->change_property("is_active", DEFAULT_VALUE, 1);
	$r->add_checkbox("show_for_user", INTEGER);
	$r->change_property("show_for_user", DEFAULT_VALUE, 1);
	$r->add_textbox("icon_order", INTEGER, ICON_ORDER_MSG);
	$r->change_property("icon_order", REQUIRED, true);
	$r->add_textbox("icon_code", TEXT, ICON_CODE_MSG);
	$r->change_property("icon_code", REQUIRED, true);
	$r->change_property("icon_code", MAX_LENGTH, 32);
	$r->add_textbox("icon_image", TEXT, ICON_IMAGE_MSG);
	$r->change_property("icon_image", REQUIRED, true);
	$r->add_textbox("icon_width", INTEGER, WIDTH_MSG);
	$r->add_textbox("icon_height", INTEGER, HEIGHT_MSG);
	$r->add_textbox("icon_name", TEXT, ICON_NAME_MSG);

	$r->events[BEFORE_DEFAULT] = "set_icon_order";
	$r->events[BEFORE_INSERT] = "set_icon_size";
	$r->events[BEFORE_UPDATE] = "set_icon_size";

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function set_icon_order()
	{
		global $r, $db, $table_prefix;
		$sql = "SELECT MAX(icon_order) FROM " . $table_prefix . "icons ";	
		$icon_order = get_db_value($sql);
		$r->change_property("icon_order", DEFAULT_VALUE, ($icon_order + 1));
	}

	function set_icon_size()
	{
		global $r;
		$icon_image = $r->get_value("icon_image");
		if ($icon_image) {
			$icon_image = preg_replace("/^images/", "../images", $icon_image);
			$image_size = @getimagesize($icon_image);
			if (is_array($image_size)) {
				$r->set_value("icon_width", $image_size[0]);
				$r->set_value("icon_height", $image_size[1]);
			}
		}
		
	}

?>
