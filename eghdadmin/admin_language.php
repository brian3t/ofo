<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_language.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once("./admin_common.php");

	check_admin_security("static_tables");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_language.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_languages_href", "admin_languages.php");
	$t->set_var("admin_language_href", "admin_language.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");
	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", LANGUAGE_MSG, CONFIRM_DELETE_MSG));

	$sp = get_param("sp");
	$sort_ord = get_param("sort_ord");
	$sort_dir = get_param("sort_dir");
	$page = get_param("page");

	$r = new VA_Record($table_prefix . "languages");
	$r->return_page = "admin_languages.php?sort_ord=" . urlencode($sort_ord) . "&sort_dir=" . urlencode($sort_dir) . "&page=" . urlencode($page) . "&sp=" . urlencode($sp);

	$r->add_where("language_edit", TEXT);
	$r->change_property("language_edit", COLUMN_NAME, "language_code");
	$r->add_textbox("language_code_edit", TEXT, LANGUAGE_CODE_MSG);
	$r->change_property("language_code_edit", COLUMN_NAME, "language_code");
	$r->change_property("language_code_edit", REQUIRED, true);
	$r->change_property("language_code_edit", UNIQUE, true);
	$r->add_textbox("language_order", INTEGER, LANGUAGE_ORDER_MSG);
	$r->change_property("language_order", REQUIRED, true);
	$r->add_checkbox("show_for_user", INTEGER);
	$r->add_textbox("language_name", TEXT, LANGUAGE_NAME_MSG);
	$r->change_property("language_name", REQUIRED, true);
	$r->add_textbox("language_image", TEXT, LANGUAGE_IMAGE_MSG);
	$r->add_textbox("language_image_active", TEXT, LANGUAGE_IMAGE_ACTIVE_MSG);
	//$currencies = get_db_values("SELECT currency_code,currency_title FROM " . $table_prefix . "currencies", array(array("", "")));
	//$r->add_select("currency_code", TEXT, $currencies, "Currency Code");

	$r->process();

	$t->set_var("sp", htmlspecialchars($sp));
	$t->set_var("sort_ord", htmlspecialchars($sort_ord));
	$t->set_var("sort_dir", htmlspecialchars($sort_dir));
	$t->set_var("page", htmlspecialchars($page));

	$t->pparse("main");

?>
