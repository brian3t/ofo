<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_subscriptions.php                                  ***
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

	check_admin_security("subscriptions");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_subscriptions.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_user_types_href",    "admin_user_types.php");
	$t->set_var("admin_user_type_href",     "admin_user_type.php");
	$t->set_var("admin_user_profile_href",  "admin_user_profile.php");
	$t->set_var("admin_user_product_href",  "admin_user_product.php");
	$t->set_var("admin_user_contact_href",  "admin_user_contact.php");
	$t->set_var("admin_subscription_href",  "admin_subscription.php");
	$t->set_var("admin_subscriptions_href",  "admin_subscriptions.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_subscriptions.php");
	$s->set_parameters(false, true, true, false);
	$s->set_sorter(ID_MSG, "sorter_subscription_id", "1", "s.subscription_id");
	$s->set_sorter(SUBSCRIPTION_NAME_MSG, "sorter_subscription_name", "2", "s.subscription_name");
	$s->set_sorter(USER_TYPE_MSG, "sorter_type_name", "3", "ut.type_name");
	$s->set_sorter(GROUP_MSG, "sorter_group_name", "4", "sg.group_name");
	$s->set_sorter(ACTIVE_MSG, "sorter_is_active", "5", "s.is_active");
	$s->set_sorter(DEFAULT_MSG, "sorter_is_default", "6", "s.is_default");

	$user_type = get_db_values("SELECT type_id, type_name FROM " . $table_prefix . "user_types", array(array("", "")));
	$subscriptions_groups = get_db_values("SELECT group_id, group_name FROM " . $table_prefix . "subscriptions_groups", array(array("", "")));

	$r = new VA_Record($table_prefix . "subscriptions");
	$r->add_select("s_ut", INTEGER, $user_type, USER_TYPE_MSG);
	$r->add_select("s_g", INTEGER, $subscriptions_groups, SUBSCRIPTIONS_GROUP_MSG);
	if (sizeof($subscriptions_groups) < 2) {
		$r->change_property("s_g", SHOW, false);
	}
	$r->get_form_parameters();
	$r->validate();
	$r->set_form_parameters();

	$where = "";
	if (!$r->errors)
	{
		if (!$r->is_empty("s_ut")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " s.user_type_id=" . $db->tosql($r->get_value("s_ut"), INTEGER);
		}

		if (!$r->is_empty("s_g")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " s.group_id=" . $db->tosql($r->get_value("s_g"), INTEGER);
		}
		if ($where) { $where = " WHERE " . $where; }
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$admin_subscription = new VA_URL("admin_subscription.php");
	$admin_subscription->add_parameter("page", REQUEST, "page");
	$admin_subscription->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_subscription->add_parameter("sort_dir", REQUEST, "sort_dir");
	$admin_subscription->add_parameter("s_ut", REQUEST, "s_ut");
	$admin_subscription->add_parameter("s_g", REQUEST, "s_g");
	$t->set_var("admin_subscription_new_url", $admin_subscription->get_url());

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_subscriptions.php");
	// set up variables for navigator
	$sql  = "SELECT COUNT(*) FROM " . $table_prefix . "subscriptions s ";
	$sql .= $where;
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$sql  = " SELECT s.is_default, s.is_active, ";
	$sql .= " s.subscription_id, s.subscription_name, ut.type_name, sg.group_name ";
	$sql .= " FROM ((" . $table_prefix . "subscriptions s ";
	$sql .= " LEFT JOIN " . $table_prefix . "user_types ut ON s.user_type_id=ut.type_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "subscriptions_groups sg ON s.group_id=sg.group_id) ";
	$sql .= $where;
	$sql .= $s->order_by;
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql);
	if ($db->next_record())
	{
		$admin_subscription->add_parameter("subscription_id", DB, "subscription_id");
		$t->set_var("no_records", "");
		do {
			$is_default = ($db->f("is_default") == 1) ? "<b>" . YES_MSG . "</b>" : NO_MSG;
			$is_active = ($db->f("is_active") == 1) ? "<b>" . YES_MSG . "</b>" : NO_MSG;
			$t->set_var("subscription_id", $db->f("subscription_id"));
			$t->set_var("subscription_name", get_translation($db->f("subscription_name")));
			$t->set_var("type_name", get_translation($db->f("type_name")));
			$t->set_var("group_name", get_translation($db->f("group_name")));
			$t->set_var("is_default", $is_default);
			$t->set_var("is_active", $is_active);
			$t->set_var("admin_subscription_url", $admin_subscription->get_url());

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