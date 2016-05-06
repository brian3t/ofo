<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_subscriptions_groups.php                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");

	include_once("./admin_common.php");

	check_admin_security("subscriptions_groups");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_subscriptions_groups.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_user_types_href",    "admin_user_types.php");
	$t->set_var("admin_user_type_href",     "admin_user_type.php");
	$t->set_var("admin_subscriptions_group_href",  "admin_subscriptions_group.php");
	$t->set_var("admin_subscriptions_groups_href",  "admin_subscriptions_groups.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_subscriptions_groups.php");
	$s->set_parameters(false, true, true, false);
	$s->set_sorter(ID_MSG, "sorter_group_id", "1", "sg.group_id");
	$s->set_sorter(GROUP_MSG, "sorter_group_name", "2", "sg.group_name");
	$s->set_sorter(ACTIVE_MSG, "sorter_is_active", "3", "sg.is_active");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$admin_subscriptions_group = new VA_URL("admin_subscriptions_group.php");
	$admin_subscriptions_group->add_parameter("page", REQUEST, "page");
	$admin_subscriptions_group->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_subscriptions_group->add_parameter("sort_dir", REQUEST, "sort_dir");
	$admin_subscriptions_group->add_parameter("s_ut", REQUEST, "s_ut");
	$admin_subscriptions_group->add_parameter("s_g", REQUEST, "s_g");
	$t->set_var("admin_subscriptions_group_new_url", $admin_subscriptions_group->get_url());

	// set up variables for navigator
	$where = "";
	$sql  = "SELECT COUNT(*) ";
	$sql .= " FROM " . $table_prefix . "subscriptions_groups sg ";
	$sql .= $where;
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_subscriptions_groups.php");
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$sql  = " SELECT sg.group_id, sg.group_name, sg.is_active ";
	$sql .= " FROM " . $table_prefix . "subscriptions_groups sg ";
	$sql .= $where;
	$sql .= $s->order_by;
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql);
	if ($db->next_record())
	{
		$admin_subscriptions_group->add_parameter("group_id", DB, "group_id");
		$t->set_var("no_records", "");
		do {
			$is_active = ($db->f("is_active") == 1) ? "<b>" . YES_MSG . "</b>" : NO_MSG;
			$t->set_var("group_id", $db->f("group_id"));
			$t->set_var("group_name", get_translation($db->f("group_name")));
			$t->set_var("is_active", $is_active);
			$t->set_var("admin_subscriptions_group_url", $admin_subscriptions_group->get_url());

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