<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_lookup_tables.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");

	include_once("./admin_common.php");

	check_admin_security("static_tables");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_lookup_tables.html");

	$t->set_var("admin_href",                 "admin.php");
	$t->set_var("admin_item_types_href",      "admin_item_types.php");
	$t->set_var("admin_admins_href",          "admin_admins.php");
	$t->set_var("admin_privileges_href",      "admin_privileges.php");
	$t->set_var("admin_countries_href",       "admin_countries.php");
	$t->set_var("admin_credit_cards_href",    "admin_credit_cards.php");
	$t->set_var("admin_cc_start_years_href",  "admin_cc_start_years.php");
	$t->set_var("admin_cc_expiry_years_href", "admin_cc_expiry_years.php");
	$t->set_var("admin_issue_numbers_href",   "admin_issue_numbers.php");
	$t->set_var("admin_manufacturers_href",   "admin_manufacturers.php");
	$t->set_var("admin_shipping_rules_href",  "admin_shipping_rules.php");
	$t->set_var("admin_shipping_times_href",  "admin_shipping_times.php");
	$t->set_var("admin_states_href",          "admin_states.php");
	$t->set_var("admin_events_href",          "admin_events.php");
	$t->set_var("admin_tax_rates_href",       "admin_tax_rates.php");
	$t->set_var("admin_release_types_href",   "admin_release_types.php");
	$t->set_var("admin_change_types_href",    "admin_change_types.php");
	$t->set_var("admin_currencies_href",      "admin_currencies.php");
	$t->set_var("admin_companies_href",       "admin_companies.php");
	$t->set_var("admin_languages_href",       "admin_languages.php");
	$t->set_var("admin_search_engines_href",  "admin_search_engines.php");
	$t->set_var("admin_authors_href",         "admin_authors.php");
	$t->set_var("admin_allowed_cell_phones_href", "admin_allowed_cell_phones.php");
	$t->set_var("admin_icons_href", "admin_icons.php");	
	$t->set_var("admin_google_base_types_href", "admin_google_base_types.php");
	$t->set_var("admin_google_base_attributes_href", "admin_google_base_attributes.php");
	$t->set_var("admin_price_codes_href", "admin_price_codes.php");
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>