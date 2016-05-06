<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_supplier.php                                       ***
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

	check_admin_security("suppliers");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_supplier.html");

	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$confirm_delete = str_replace(array("{record_name}", "\'"), array(SUPPLIER_MSG, "\\'"), CONFIRM_DELETE_MSG);
	$t->set_var("confirm_delete", $confirm_delete);
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_suppliers_href", "admin_suppliers.php");
	$t->set_var("admin_supplier_href", "admin_supplier.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");

	$r = new VA_Record($table_prefix . "suppliers");
	$r->return_page = "admin_suppliers.php";

	$r->add_where("supplier_id", INTEGER);

	$r->add_textbox("supplier_order", INTEGER, SUPPLIER_ORDER_MSG);
	$r->change_property("supplier_order", REQUIRED, true);
	$r->add_textbox("supplier_name", TEXT, SUPPLIER_NAME_MSG);
	$r->change_property("supplier_name", REQUIRED, true);
	$r->add_textbox("supplier_email", TEXT, EMAIL_FIELD);
	$r->change_property("supplier_email", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("short_description", TEXT, AD_SHORT_DESC_MSG);
	$r->add_textbox("full_description", TEXT, FULL_DESCRIPTION_MSG);
	$r->add_textbox("image_small", TEXT, IMAGE_SMALL_MSG);
	$r->add_textbox("image_small_alt", TEXT, IMAGE_SMALL_ALT_MSG);
	$r->add_textbox("image_large", TEXT, IMAGE_LARGE_MSG);
	$r->add_textbox("image_large_alt", TEXT, IMAGE_LARGE_ALT_MSG);

	$r->process();

	$t->pparse("main");

?>