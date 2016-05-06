<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_user_type.php                                      ***
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

	check_admin_security("users_groups");
	
	$va_license_code = va_license_code();

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_user_type.html");
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$confirm_delete = str_replace(array("{record_name}", "\'"), array(USER_TYPE_MSG, "\\'"), CONFIRM_DELETE_MSG);
	$t->set_var("confirm_delete", $confirm_delete);
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_users_href", "admin_users.php");
	$t->set_var("admin_user_types_href", "admin_user_types.php");
	$t->set_var("admin_user_type_href", "admin_user_type.php");

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

	$cancel_values = 
		array( 
			array(0, DONT_RETURN_MONEY_MSG),
			array(1, RETURN_MONEY_TO_CREDITS_BALANCE_MSG)
			);

	$r = new VA_Record($table_prefix . "user_types");

	$r->add_where("type_id", INTEGER);
	$r->change_property("type_id", USE_IN_INSERT, true);
	$r->add_checkbox("is_default", INTEGER, DEFAULT_TYPE_MSG);
	$r->add_checkbox("is_active", INTEGER);
	$r->add_radio("show_for_user", INTEGER, $yes_no_messages);
	$r->add_textbox("type_name", TEXT, TYPE_NAME_MSG);
	$r->change_property("type_name", REQUIRED, true);
	$r->add_select("price_type", INTEGER, $price_types);
	$r->add_checkbox("tax_free", INTEGER);
	$r->add_radio("is_sms_allowed", INTEGER, $yes_no_messages);
	$r->add_checkbox("is_subscription", INTEGER);

	// product discount and rewards
	$r->add_select("discount_type", INTEGER, $discount_types);
	$r->add_textbox("discount_amount", NUMBER, DISCOUNT_AMOUNT_MSG);
	$r->add_select("merchant_fee_type", INTEGER, $discount_types);
	$r->add_textbox("merchant_fee_amount", NUMBER, MERCHANT_FEE_AMOUNT_MSG);
	$r->add_select("affiliate_commission_type", INTEGER, $discount_types);
	$r->add_textbox("affiliate_commission_amount", NUMBER, AFFILIATE_COMMISSION_AMOUNT_MSG);
	$r->add_select("reward_type", INTEGER, $discount_types, REWARD_POINTS_TYPE_MSG);
	$r->add_textbox("reward_amount", NUMBER, REWARD_POINTS_AMOUNT_MSG);
	$r->add_select("credit_reward_type", INTEGER, $discount_types, REWARD_CREDITS_TYPE_MSG);
	$r->add_textbox("credit_reward_amount", NUMBER, REWARD_CREDITS_AMOUNT_MSG);
	
	$r->add_checkbox("sites_all", INTEGER);

	$rps = new VA_Record($table_prefix . "user_types_settings", "aps");

	$rps->add_radio("cancel_subscription", INTEGER, $cancel_values);
	$rps->add_checkbox("new_profile", INTEGER);
	$rps->add_checkbox("edit_profile", INTEGER);
	$rps->add_checkbox("approve_profile", INTEGER);
	$rps->add_checkbox("upgrade_downgrade", INTEGER);

	$rps->add_checkbox("my_orders", INTEGER);
	$rps->add_checkbox("my_details", INTEGER);
	$rps->add_checkbox("my_support", INTEGER);
	$rps->add_checkbox("my_forum", INTEGER);
	$rps->add_checkbox("my_payments", INTEGER);
	$rps->add_checkbox("my_carts", INTEGER);
	$rps->add_checkbox("my_wishlist", INTEGER);
	$rps->add_checkbox("reminder_service", INTEGER);
	$rps->add_checkbox("my_product_registrations", INTEGER);

	$rps->add_checkbox("access_products", INTEGER);
	$rps->add_checkbox("merchant_sales", INTEGER);
	$rps->add_checkbox("add_product", INTEGER);
	$rps->add_checkbox("edit_product", INTEGER);
	$rps->add_checkbox("delete_product", INTEGER);
	$rps->add_checkbox("approve_product", INTEGER);
	$rps->add_checkbox("access_product_registration", INTEGER);
	$rps->add_checkbox("approve_product_registration", INTEGER);
	$rps->add_checkbox("merchant_orders", INTEGER);
	$rps->add_checkbox("merchant_order_payment_details", INTEGER);
	$rps->add_checkbox("merchant_order_cc_number", INTEGER);
	$rps->add_checkbox("merchant_order_cc_cvv2", INTEGER);

	$rps->add_checkbox("affiliate_join", INTEGER);
	$rps->add_checkbox("affiliate_sales", INTEGER);

	$rps->add_checkbox("add_ad", INTEGER);
	$rps->add_checkbox("edit_ad", INTEGER);
	$rps->add_checkbox("delete_ad", INTEGER);
	$rps->add_checkbox("approve_ad", INTEGER);

	$r->get_form_values();
	$rps->get_form_values();

	$operation = get_param("operation");
	$type_id = get_param("type_id");
	$return_page = get_param("rp");
	if (!strlen($return_page)) { $return_page = "admin_user_types.php"; }
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	
	if ($sitelist) {
		$selected_sites = array();
		if (strlen($operation)) {
			$sites = get_param("sites");
			if ($sites) {
				$selected_sites = split(",", $sites);
			}
		} elseif ($type_id) {
			$sql  = "SELECT site_id FROM " . $table_prefix . "user_types_sites ";
			$sql .= " WHERE type_id=" . $db->tosql($type_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$selected_sites[] = $db->f("site_id");
			}
		}
	}

	if (strlen($operation))
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		elseif ($operation == "delete" && $type_id)
		{
			$r->delete_record();
			$db->query("DELETE FROM " . $table_prefix . "user_types_settings WHERE type_id=" . $db->tosql($type_id, INTEGER));
			$db->query("DELETE FROM " . $table_prefix . "user_types_sites WHERE type_id=" . $db->tosql($type_id, INTEGER));

			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $r->validate();

		if ($is_valid)
		{
			if (!$sitelist) {
				$r->set_value("sites_all", 1);
			}
			if ($r->get_value("is_default") == 1) {
				$sql = " UPDATE " . $table_prefix . "user_types SET is_default=0 ";
				$db->query($sql);
			}
			if (strlen($type_id)) {
				$r->update_record();
				$db->query("DELETE FROM " . $table_prefix . "user_types_settings WHERE type_id=" . $db->tosql($type_id, INTEGER));
			} else {
				$sql = " SELECT MAX(type_id) FROM " . $table_prefix . "user_types ";
				$type_id = get_db_value($sql) + 1;
				$r->set_value("type_id", $type_id);
				$r->insert_record();
				// redirect to user profile settings
				$return_page = "admin_user_profile.php?type_id=" . urlencode($type_id);
			}

			foreach ($rps->parameters as $key => $value)
			{
				$sql  = " INSERT INTO " . $table_prefix . "user_types_settings (type_id, setting_name, setting_value) VALUES (";
				$sql .= $db->tosql($type_id, INTEGER) . ", '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ")";
				$db->query($sql);
			}
			
			// update sites
			if ($sitelist) {
				$db->query("DELETE FROM " . $table_prefix . "user_types_sites WHERE type_id=" . $db->tosql($type_id, INTEGER));
				for ($st = 0; $st < sizeof($selected_sites); $st++) {
					$site_id = $selected_sites[$st];
					if (strlen($site_id)) {
						$sql  = " INSERT INTO " . $table_prefix . "user_types_sites (type_id, site_id) VALUES (";
						$sql .= $db->tosql($type_id, INTEGER) . ", ";
						$sql .= $db->tosql($site_id, INTEGER) . ") ";
						$db->query($sql);
					}
				}
			}
			header("Location: " . $return_page);
			exit;
		}
	} elseif (strlen($type_id)) {
		$r->get_db_values();

		$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "user_types_settings ";
		$sql .= " WHERE type_id=" . $db->tosql($type_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$rps->set_value($db->f("setting_name"), $db->f("setting_value"));
		}
	} else {
		$r->set_value("price_type", 0);
		$r->set_value("is_active", 1);
		$r->set_value("show_for_user", 1);
		$r->set_value("is_sms_allowed", 0);
		$r->set_value("sites_all", 1);
	}

	$r->set_form_parameters();
	$rps->set_form_parameters();
	
	// Show/Hide blocks of settings due to current license
	// shop - 1, cms - 2, helpdesk - 4, forum - 8, ads - 16
	$home_page_settings = array(
		"orders_block"          => "my_orders",
		"details_block"         => "my_details",
		"support_block"         => "my_support",
		"forum_block"           => "my_forum",
		"payments_block"        => "my_payments",
		"carts_block"           => "my_carts",
		"wishlist_block"        => "my_wishlist",
		"reminder_block"		=> "reminder_service",
		"registration_block"    => "my_product_registrations"
	);
	if ($va_license_code & 1) {
		$t->parse("products_block", false);
		$t->parse("affiliate_block", false);
	} else {
		$t->set_var("products_block", "");
		$t->set_var("affiliate_block", "");
		unset($home_page_settings["orders_block"]);
		unset($home_page_settings["payments_block"]);
		unset($home_page_settings["carts_block"]);
		unset($home_page_settings["wishlist_block"]);
	}
	if (!($va_license_code & 4)) {
		unset($home_page_settings["support_block"]);
	}
	if (!($va_license_code & 8)) {
		unset($home_page_settings["forum_block"]);
	}
	if ($va_license_code & 16) {
		$t->parse("ad_block", false);
	} else {
		$t->set_var("ad_block", "");
	}
	$block_number = 0;
	foreach ($home_page_settings as $block_name => $permission_name) {
		$block_number++;
		$t->sparse($block_name, false);
		$t->parse("home_page_cols", true);
		$t->set_var($block_name, "");
		if ($block_number % 2 == 0) {
			$t->parse("home_page_rows", true);
			$t->set_var("home_page_cols", "");
		}
	}
	if ($block_number > 0 && $block_number % 2 != 0) {
		$t->sparse("blank_block", false);
		$t->parse("home_page_cols", true);
		$t->parse("home_page_rows", true);
	}

	if (strlen($type_id))	
	{
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	} else {
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");	
	}

	$t->set_var("rp", htmlspecialchars($return_page));

	// set styles for tabs
	$tabs = array(
		"general" => array("title" => GLOBAL_MSG), 
		"commissions" => array("title" => DISCOUNTS_FEES_COMMISSIONS_MSG), 
		"sites"       => array("title" => ADMIN_SITES_MSG, "show" => $sitelist),
	);
	parse_admin_tabs($tabs, $tab);

	if ($sitelist) {
		$sites = array();
		$sql = " SELECT site_id, site_name FROM " . $table_prefix . "sites ";
		$db->query($sql);
		while ($db->next_record())	{
			$site_id = $db->f("site_id");
			$site_name = get_translation($db->f("site_name"));
			$sites[$site_id] = $site_name;
			$t->set_var("site_id", $site_id);
			$t->set_var("site_name", $site_name);
			if (in_array($site_id, $selected_sites)) {
				$t->parse("selected_sites", true);
			} else {
				$t->parse("available_sites", true);
			}
		}
		$t->parse("sitelist");	
	}

	$t->pparse("main");

?>