<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_subscriptions_group.php                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once($root_folder_path . "messages/" . $language_code . "/support_messages.php");
	include_once($root_folder_path . "messages/" . $language_code . "/forum_messages.php");
	include_once("./admin_common.php");

	check_admin_security("subscriptions_groups");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_subscriptions_group.html");
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$confirm_delete = str_replace(array("{record_name}", "\'"), array(SUBSCRIPTIONS_GROUP_MSG, "\\'"), CONFIRM_DELETE_MSG);
	$t->set_var("confirm_delete", $confirm_delete);
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_users_href", "admin_users.php");
	$t->set_var("admin_user_types_href", "admin_user_types.php");
	$t->set_var("admin_user_type_href", "admin_user_type.php");
	$t->set_var("admin_subscriptions_group_href",  "admin_subscriptions_group.php");
	$t->set_var("admin_subscriptions_groups_href",  "admin_subscriptions_groups.php");

	$yes_no_messages = 
		array( 
			array(1, YES_MSG),
			array(0, NO_MSG)
			);

	$r = new VA_Record($table_prefix . "subscriptions_groups");
	$r->return_page = "admin_subscriptions_groups.php";

	$r->add_where("group_id", INTEGER);
	$r->add_radio("is_active", INTEGER, $yes_no_messages, IS_ACTIVE_MSG);
	$r->change_property("is_active", DEFAULT_VALUE, 1);

	$r->add_textbox("group_name", TEXT, SUBSCRIPTION_NAME_MSG);
	$r->change_property("group_name", REQUIRED, true);

	$r->add_hidden("page", TEXT);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("sort_dir", TEXT);
	$r->add_hidden("s_ut", INTEGER);
	$r->add_hidden("s_g", INTEGER);

	$r->process();

	$t->pparse("main");

?>