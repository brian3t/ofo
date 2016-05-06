<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_subscription.php                                   ***
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

	check_admin_security("subscriptions");

	$subscription_id = get_param("subscription_id");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_subscription.html");
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$confirm_delete = str_replace(array("{record_name}", "\'"), array(SUBSCRIPTION_MSG, "\\'"), CONFIRM_DELETE_MSG);
	$t->set_var("confirm_delete", $confirm_delete);
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_users_href", "admin_users.php");
	$t->set_var("admin_user_types_href", "admin_user_types.php");
	$t->set_var("admin_user_type_href", "admin_user_type.php");
	$t->set_var("admin_subscription_href",  "admin_subscription.php");
	$t->set_var("admin_subscriptions_href",  "admin_subscriptions.php");

	$yes_no_messages = 
		array( 
			array(1, YES_MSG),
			array(0, NO_MSG)
			);

	$price_types = 
		array( 
			array(0, PRICE_MSG),
			array(1, PROD_TRADE_PRICE_MSG)
			);

	$discount_types = array(
		array("", ""), array(0, NOT_AVAILABLE_MSG), array(1, PERCENT_PER_PROD_FULL_PRICE_MSG),
		array(2, FIXED_AMOUNT_PER_PROD_MSG), array(3, PERCENT_PER_PROD_SELL_PRICE_MSG),
		array(4, PERCENT_PER_PROD_SELL_BUY_MSG)
	);

	$subsription_reward_types = array(
		array("", ""), 
		array(0, NOT_AVAILABLE_MSG), 
		array(1, SUBSCRIPTION_FEE_PERCENTAGE_MSG),
		array(2, SUBSCRIPTION_FIXED_AMOUNT_MSG), 
	);


	$subscription_periods = 
		array( 
			array("", ""), array(1, DAY_MSG), array(2, WEEK_MSG), array(3, MONTH_MSG), array(4, YEAR_MSG)
		);

	$user_types = get_db_values("SELECT type_id, type_name FROM " . $table_prefix . "user_types", array(array("", "")));
	$subscriptions_groups = get_db_values("SELECT group_id, group_name FROM " . $table_prefix . "subscriptions_groups", array(array("", "")));

	$r = new VA_Record($table_prefix . "subscriptions");
	$r->return_page = "admin_subscriptions.php";

	$r->add_where("subscription_id", INTEGER);
	$r->add_radio("is_active", INTEGER, $yes_no_messages, IS_ACTIVE_MSG);
	$r->change_property("is_active", DEFAULT_VALUE, 1);
	$r->add_radio("is_default", INTEGER, $yes_no_messages, DEFAULT_MSG);
	$r->change_property("is_default", DEFAULT_VALUE, 0);
	$r->add_select("user_type_id", INTEGER, $user_types, USER_TYPE_MSG);
	$r->change_property("user_type_id", DEFAULT_VALUE, get_param("s_ut"));
	$r->add_select("group_id", INTEGER, $subscriptions_groups, GROUP_MSG);
	$r->change_property("group_id", DEFAULT_VALUE, get_param("s_g"));
	if(sizeof($subscriptions_groups) < 2) {
		$r->change_property("group_id", SHOW, false);
	}

	// subscription options 
	$r->add_checkbox("is_subscription_recurring", INTEGER);
	$r->add_textbox("subscription_name", TEXT, SUBSCRIPTION_NAME_MSG);
	$r->change_property("subscription_name", REQUIRED, true);
	$r->add_textbox("subscription_fee", NUMBER, SUBSCRIPTION_FEE_MSG);
	$r->change_property("subscription_fee", REQUIRED, true);
	$r->add_select("subscription_period", INTEGER, $subscription_periods, SUBSCRIPTION_PERIOD_MSG);
	$r->change_property("subscription_period", REQUIRED, true);
	$r->add_textbox("subscription_interval", INTEGER, SUBSCRIPTION_INTERVAL_MSG);
	$r->change_property("subscription_interval", REQUIRED, true);
	$r->add_textbox("subscription_suspend", NUMBER, SUBSCRIPTION_SUSPEND_MSG);
	$r->change_property("subscription_fee", REQUIRED, true);

	$r->change_property("subscription_name", REQUIRED, true);
	$r->change_property("subscription_fee", REQUIRED, true);
	$r->change_property("subscription_period", REQUIRED, true);
	$r->change_property("subscription_interval", REQUIRED, true);
	$r->change_property("subscription_interval", MIN_VALUE, 1);

	// subscription rewards
	$r->add_select("subscription_affiliate_type", INTEGER, $subsription_reward_types);
	$r->add_textbox("subscription_affiliate_amount", NUMBER, AFFILIATE_COMMISSION_AMOUNT_MSG);
	$r->add_select("subscription_points_type", INTEGER, $subsription_reward_types, REWARD_POINTS_TYPE_MSG);
	$r->add_textbox("subscription_points_amount", NUMBER, REWARD_POINTS_AMOUNT_MSG);
	$r->add_select("subscription_credits_type", INTEGER, $subsription_reward_types, REWARD_CREDITS_TYPE_MSG);
	$r->add_textbox("subscription_credits_amount", NUMBER, REWARD_CREDITS_AMOUNT_MSG);

	$r->add_hidden("page", TEXT);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("sort_dir", TEXT);
	$r->add_hidden("s_ut", INTEGER);
	$r->add_hidden("s_g", INTEGER);

	$r->set_event(BEFORE_INSERT, "check_default_subscription");
	$r->set_event(BEFORE_UPDATE, "check_default_subscription");


	function check_default_subscription()
	{
		global $r, $db, $table_prefix;
		if ($r->get_value("is_default") == 1 && (!$r->is_empty("user_type_id") || !$r->is_empty("group_id"))) {
			$sql  = " UPDATE " . $table_prefix . "subscriptions SET is_default=0 ";
			if (!$r->is_empty("user_type_id")) {
				$sql .= " WHERE user_type_id=" . $db->tosql($r->get_value("user_type_id"), INTEGER);
			} else {
				$sql .= " WHERE group_id=" . $db->tosql($r->get_value("group_id"), INTEGER);
			}
			$db->query($sql);
		}
	}

	$r->process();

	$t->pparse("main");

?>