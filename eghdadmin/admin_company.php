<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_company.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("static_tables");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_company.html");
	$t->set_var("admin_company_href",   "admin_company.php");
	$t->set_var("admin_companies_href", "admin_companies.php");
	$t->set_var("admin_upload_href",    "admin_upload.php");
	$t->set_var("admin_select_href",    "admin_select.php");
	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", COMPANY_SELECT_FIELD, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "companies");
	$r->return_page  = "admin_companies.php";

	$yes_no = 
		array( 
			array(1, YES_MSG), array(0, NO_MSG)
		);

	$r->add_where("company_id", INTEGER);
	$r->add_textbox("company_name", TEXT, COMPANY_NAME_FIELD);
	$r->change_property("company_name", REQUIRED, true);
	$r->add_textbox("image_small", TEXT);
	$r->add_textbox("image_large", TEXT);
	$r->add_textbox("address_info", TEXT);
	$r->add_textbox("phone_number", TEXT);
	$r->add_textbox("fax_number", TEXT);
	$r->add_textbox("site_url", TEXT);
	$r->add_textbox("contact_email", TEXT, CONTACT_EMAIL_ADDRESS_MSG);
	$r->change_property("contact_email", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("short_description", TEXT);
	$r->add_textbox("full_description", TEXT);

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_companies_href", "admin_companies.php");
	$t->pparse("main");

?>