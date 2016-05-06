<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_registration_view.php                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/registration_functions.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");
	
	check_admin_security("admin_registration");
		
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_registration_view.html");	
	$t->set_var("admin_registration_href", "admin_registration.php");
	$t->set_var("admin_registrations_href", "admin_registrations.php");
	$t->set_var("admin_registration_view_href", "admin_registration_view.php");
	$t->set_var("admin_registration_edit_href", "admin_registration_edit.php");	
	

	$registration_id = get_param("registration_id");
	if (!$registration_id || !get_all_product_registration_variables($registration_id)) {
		header("Location: admin_registrations.php");
		exit;
	}
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	
	$t->pparse("main");
?>