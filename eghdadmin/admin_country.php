<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_country.php                                        ***
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
	$t->set_file("main", "admin_country.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_countries_href", "admin_countries.php");
	$t->set_var("admin_country_href", "admin_country.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", COUNTRY_FIELD, CONFIRM_DELETE_MSG));

	$sp = get_param("sp");
	$sort_ord = get_param("sort_ord");
	$sort_dir = get_param("sort_dir");
	$page = get_param("page");

	$r = new VA_Record($table_prefix . "countries");
	$r->return_page = "admin_countries.php?sort_ord=".urlencode($sort_ord)."&sort_dir=".urlencode($sort_dir)."&page=".urlencode($page)."&sp=".urlencode($sp);

	$r->add_where("country_id", INTEGER);
	$r->add_checkbox("show_for_user", INTEGER);
	$r->change_property("show_for_user", DEFAULT_VALUE, 1);
	$r->add_textbox("country_order", INTEGER, ADMIN_ORDER_MSG);
	$r->add_textbox("country_iso_number", TEXT, ISO_NUMBER_MSG);
	$r->change_property("country_iso_number", REQUIRED, true);
	$r->change_property("country_iso_number", REGEXP_MASK, "/\\d+/");
	$r->add_textbox("country_code", TEXT, COUNTRY_CODE_MSG);
	$r->change_property("country_code", REQUIRED, true);
	$r->add_textbox("country_code_alpha3", TEXT, COUNTRY_CODE_ALPHA3_MSG);

	$currencies = get_db_values("SELECT currency_code,currency_title FROM " . $table_prefix . "currencies", array(array("", "")));
	$r->add_select("currency_code", TEXT, $currencies, CURRENCY_CODE_MSG);
	$r->add_textbox("country_name", TEXT, COUNTRY_NAME_MSG);
	$r->change_property("country_name", REQUIRED, true);
	
	$r->set_event(AFTER_UPDATE, "update_order_codes");

	$r->process();

	$t->set_var("sp", htmlspecialchars($sp));
	$t->set_var("sort_ord", htmlspecialchars($sort_ord));
	$t->set_var("sort_dir", htmlspecialchars($sort_dir));
	$t->set_var("page", htmlspecialchars($page));

	$t->pparse("main");

	function update_order_codes() {
		global $r, $db, $table_prefix;
		$country_id = $r->get_value("country_id");
		$country_code = $r->get_value("country_code");
		
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET country_code=" . $db->tosql($country_code, TEXT);
		$sql .= " WHERE country_id=" . $db->tosql($country_id, INTEGER, true, false);
		$db->query($sql);
		
		$sql  = " UPDATE " . $table_prefix . "orders ";
		$sql .= " SET delivery_country_code=" . $db->tosql($country_code, TEXT);
		$sql .= " WHERE delivery_country_id=" . $db->tosql($country_id, INTEGER, true, false);
		$db->query($sql);
	}
?>