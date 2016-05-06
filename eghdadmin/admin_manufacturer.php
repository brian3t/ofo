<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_manufacturer.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/friendly_functions.php");
	include_once("../messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("manufacturers");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_manufacturer.html");

	$confirm_delete = str_replace(array("{record_name}", "\'"), array(PROD_MANUFACTURER_MSG, "\\'"), CONFIRM_DELETE_MSG);
	$t->set_var("confirm_delete", $confirm_delete);
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_manufacturers_href", "admin_manufacturers.php");
	$t->set_var("admin_manufacturer_href", "admin_manufacturer.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");
	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$r = new VA_Record($table_prefix . "manufacturers");
	$r->return_page = "admin_manufacturers.php";

	$r->add_where("manufacturer_id", INTEGER);

	$r->add_textbox("manufacturer_name", TEXT, MANUFACTURER_NAME_MSG);
	$r->change_property("manufacturer_name", REQUIRED, true);
	$r->add_textbox("manufacturer_order", INTEGER, MANUFACTURER_ORDER_MSG);
	$r->change_property("manufacturer_order", REQUIRED, true);
	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);
	$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
	$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	$r->add_textbox("affiliate_code", TEXT, AFFILIATE_CODE_FIELD);
	$r->add_textbox("short_description", TEXT, AD_SHORT_DESC_MSG);
	$r->add_textbox("full_description", TEXT, FULL_DESCRIPTION_MSG);
	$r->add_textbox("image_small", TEXT, IMAGE_SMALL_MSG);
	$r->add_textbox("image_small_alt", TEXT, IMAGE_SMALL_ALT_MSG);
	$r->add_textbox("image_large", TEXT, IMAGE_LARGE_MSG);
	$r->add_textbox("image_large_alt", TEXT, IMAGE_LARGE_ALT_MSG);

	$r->events[BEFORE_INSERT] = "set_friendly_url";
	$r->events[BEFORE_UPDATE] = "set_friendly_url";

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>