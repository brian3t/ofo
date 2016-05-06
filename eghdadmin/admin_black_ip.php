<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_black_ip.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("black_ips");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_black_ip.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_black_ips_href", "admin_black_ips.php");
	$t->set_var("admin_black_ip_href", "admin_black_ip.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", IP_ADDRESS_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "black_ips");
	$r->return_page = "admin_black_ips.php";
	$r->add_where("ip_address", TEXT);

	$r->add_textbox("ip_address_edit", TEXT, IP_ADDRESS_MSG);
	$r->change_property("ip_address_edit", COLUMN_NAME, "ip_address");
	$r->change_property("ip_address_edit", REQUIRED, true);
	$r->change_property("ip_address_edit", UNIQUE, true);
	$r->change_property("ip_address_edit", REGEXP_MASK, "/^[\d\.]+$/");
	$r->change_property("ip_address_edit", MIN_LENGTH, 3);
	$r->change_property("ip_address_edit", MAX_LENGTH, 15);

	$address_actions = array(
		array(0, RED_WARNING_ADMIN_MSG),
		array(1, BLOCK_ALL_ACTIVITIES_MSG),
	);
	$r->add_radio("address_action", INTEGER, $address_actions, ACTION_MSG);
	$r->change_property("address_action", REQUIRED, true);
	$r->add_textbox("address_notes", TEXT, NOTES_MSG);

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
