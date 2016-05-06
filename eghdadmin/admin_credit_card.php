<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_credit_card.php                                    ***
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
  $t->set_file("main","admin_credit_card.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_credit_cards_href", "admin_credit_cards.php");
	$t->set_var("admin_credit_card_href", "admin_credit_card.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CREDIT_CARD_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "credit_cards");
	$r->return_page = "admin_credit_cards.php";

	$r->add_where("credit_card_id", INTEGER);

	$r->add_textbox("credit_card_code", TEXT, CREDIT_CARD_CODE_MSG);
	$r->change_property("credit_card_code", REQUIRED, true);
	$r->add_textbox("credit_card_name", TEXT, CREDIT_CARD_NAME_MSG);
	$r->change_property("credit_card_name", REQUIRED, true);

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
