<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_types.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once("./admin_common.php");

	check_admin_security("users_groups");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_user_types.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_user_types_href",    "admin_user_types.php");
	$t->set_var("admin_user_type_href",     "admin_user_type.php");
	$t->set_var("admin_user_profile_href",  "admin_user_profile.php");
	$t->set_var("admin_user_product_href",  "admin_user_product.php");
	$t->set_var("admin_user_contact_href",  "admin_user_contact.php");
	$t->set_var("admin_subscriptions_href", "admin_subscriptions.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_user_types.php");
	$s->set_parameters(false, true, true, false);
	$s->set_sorter(ID_MSG, "sorter_type_id", "1", "type_id");
	$s->set_sorter(USER_TYPE_MSG, "sorter_type_name", "2", "type_name");
	$s->set_sorter(ACTIVE_MSG, "sorter_is_active", "3", "is_active");
	$s->set_sorter(DEFAULT_MSG, "sorter_is_default", "4", "is_default");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_user_types.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "user_types");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$sql  = " SELECT * FROM " . $table_prefix . "user_types ";
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql . $s->order_by);
	if ($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$is_default = ($db->f("is_default") == 1) ? "<b>" . YES_MSG . "</b>" : NO_MSG;
			$is_active = ($db->f("is_active") == 1) ? "<b>" . YES_MSG . "</b>" : NO_MSG;
			$t->set_var("type_id", $db->f("type_id"));
			$t->set_var("type_name", get_translation($db->f("type_name")));
			$t->set_var("is_default", $is_default);
			$t->set_var("is_active", $is_active);
			$t->parse("records", true);
		} while($db->next_record());
		$t->parse("titles", false);
	}
	else
	{
		$t->set_var("titles", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>