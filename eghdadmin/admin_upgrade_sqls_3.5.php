<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_upgrade_sqls_3.5.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	check_admin_security("system_upgrade");

	if (comp_vers("3.4.3", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_reward_credits DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_reward_credits FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_reward_credits FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_reward_credits DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN reward_credits DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN reward_credits FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN reward_credits FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN reward_credits DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN credit_reward_type TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN credit_reward_type SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN credit_reward_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN credit_reward_type SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN credit_reward_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN credit_reward_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN credit_reward_amount FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN credit_reward_amount DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN credit_reward_type TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN credit_reward_type SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN credit_reward_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN credit_reward_type SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN credit_reward_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN credit_reward_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN credit_reward_amount FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN credit_reward_amount DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN credit_reward_type TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN credit_reward_type SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN credit_reward_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "users ADD COLUMN credit_reward_type SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN credit_reward_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN credit_reward_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN credit_reward_amount FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "users ADD COLUMN credit_reward_amount DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN credit_reward_type TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN credit_reward_type SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN credit_reward_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN credit_reward_type SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN credit_reward_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN credit_reward_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN credit_reward_amount FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN credit_reward_amount DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "mysql" || $db_type == "db2" || $db_type == "postgre") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN location_postcode VARCHAR(16) NOT NULL DEFAULT ''";
		} else {
			$sqls[] = "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN location_postcode VARCHAR(16) NOT NULL ";
		}
		$sqls[] = " UPDATE " . $table_prefix . "ads_items SET location_postcode='' ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "ads_items_location_postcode ON " . $table_prefix . "ads_items (location_postcode) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN location_country_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN location_country_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN location_country_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN location_country_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "ads_items SET location_country_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "ads_items_location_country_id ON " . $table_prefix . "ads_items (location_country_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN location_state_id INT(11) NOT NULL DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN location_state_id INT4 NOT NULL DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN location_state_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN location_state_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "ads_items SET location_state_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "ads_items_location_state_id ON " . $table_prefix . "ads_items (location_state_id) ";

		$countries_codes = array();
		$sql = " SELECT * FROM " . $table_prefix . "countries ";
		$db->query($sql);
		while ($db->next_record()) {
			$countries_codes[$db->f("country_code")] = $db->f("country_id");
		}

		$sql = " SELECT location_country FROM " . $table_prefix . "ads_items GROUP BY location_country ";
		$db->query($sql);
		while ($db->next_record()) {
			$location_country = $db->f("location_country");
			if ($location_country && isset($countries_codes[$location_country])) {
				$sql  = " UPDATE " . $table_prefix . "ads_items SET location_country_id=" . $db->tosql($countries_codes[$location_country], INTEGER);
				$sql .= " WHERE location_country=" . $db->tosql($location_country, TEXT);
				$sqls[] = $sql;
			}
		}

		$states_codes = array();
		$sql = " SELECT * FROM " . $table_prefix . "states ";
		$db->query($sql);
		while ($db->next_record()) {
			$states_codes[$db->f("state_code")] = $db->f("state_id");
		}

		$sql = " SELECT location_state FROM " . $table_prefix . "ads_items GROUP BY location_state ";
		$db->query($sql);
		while ($db->next_record()) {
			$location_state = $db->f("location_state");
			if ($location_state && isset($states_codes[$location_state])) {
				$sql  = " UPDATE " . $table_prefix . "ads_items SET location_state_id=" . $db->tosql($states_codes[$location_state], INTEGER);
				$sql .= " WHERE location_state=" . $db->tosql($location_state, TEXT);
				$sqls[] = $sql;
			}
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4.3");
	}

	if (comp_vers("3.4.4", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users_credits ADD COLUMN credit_type TINYINT(1) DEFAULT 0",
			"postgre" => "ALTER TABLE " . $table_prefix . "users_credits ADD COLUMN credit_type SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "users_credits ADD COLUMN credit_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "users_credits ADD COLUMN credit_type SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "users_credits SET credit_type=3 ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4.4");
	}

	if (comp_vers("3.4.5", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users_credits ADD COLUMN order_item_id INT(11) DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "users_credits ADD COLUMN order_item_id INT4 DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "users_credits ADD COLUMN order_item_id INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "users_credits ADD COLUMN order_item_id INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "users_credits SET order_item_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "users_credits_order_item_id ON " . $table_prefix . "users_credits (order_item_id) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4.5");
	}

	if (comp_vers("3.4.6", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_confirmed TINYINT(1) DEFAULT 0",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_confirmed SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_confirmed BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_confirmed SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "orders SET is_confirmed=is_placed  ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN invoice_number VARCHAR(128) ";
		$sqls[] = "UPDATE " . $table_prefix . "orders SET invoice_number=order_id ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "orders_invoice_number ON " . $table_prefix . "orders (invoice_number) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN default_currency_code VARCHAR(4) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_images ADD COLUMN image_position TINYINT(1) DEFAULT 1",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_images ADD COLUMN image_position SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "items_images ADD COLUMN image_position BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_images ADD COLUMN image_position SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items_images SET image_position=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_values_assigned ADD COLUMN is_default_value TINYINT(1) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_values_assigned ADD COLUMN is_default_value SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_values_assigned ADD COLUMN is_default_value BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_values_assigned ADD COLUMN is_default_value SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_assigned ADD COLUMN property_description TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties_assigned ADD COLUMN property_description TEXT",   
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties_assigned ADD COLUMN property_description LONGTEXT",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties_assigned ADD COLUMN property_description LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4.6");
	}

	if (comp_vers("3.4.7", $current_db_version) == 1)
	{
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products', 'credits_balance_user_home', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products', 'credits_balance_order_profile', '1')";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_points_type TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_points_type SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_points_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_points_type SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_points_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_points_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_points_amount FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_points_amount DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_credits_type TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_credits_type SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_credits_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_credits_type SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_credits_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_credits_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_credits_amount FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_credits_amount DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_affiliate_type TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_affiliate_type SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_affiliate_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_affiliate_type SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_affiliate_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_affiliate_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_affiliate_amount FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_affiliate_amount DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "countries ADD COLUMN show_for_user TINYINT(1) DEFAULT 1",
			"postgre" => "ALTER TABLE " . $table_prefix . "countries ADD COLUMN show_for_user SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "countries ADD COLUMN show_for_user BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "countries ADD COLUMN show_for_user SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "countries SET show_for_user=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "states ADD COLUMN show_for_user TINYINT(1) DEFAULT 1",
			"postgre" => "ALTER TABLE " . $table_prefix . "states ADD COLUMN show_for_user SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "states ADD COLUMN show_for_user BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "states ADD COLUMN show_for_user SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "states SET show_for_user=1 ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4.7");
	}

	if (comp_vers("3.4.8", $current_db_version) == 1)
	{
		// add layout for product options page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_options', 'products_options', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_options', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_options', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_options', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_options', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_options', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_options', 'right_column_width', NULL, NULL)";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4.8");
	}


	if (comp_vers("3.4.9", $current_db_version) == 1)
	{
		$product_quantity = get_setting_value($settings, "product_quantity", "");
		$quantity_control = get_setting_value($settings, "quantity_control", "");
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products', 'quantity_control_list', ".$db->tosql($product_quantity, TEXT).")";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products', 'quantity_control_details', ".$db->tosql($product_quantity, TEXT).")";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products', 'quantity_control_basket', ".$db->tosql($quantity_control, TEXT).")";
		$sqls[] = "DELETE FROM " . $table_prefix . "global_settings WHERE setting_name='product_quantity' OR setting_name='quantity_control' ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4.9");
	}

	if (comp_vers("3.4.10", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_shipping_free TINYINT(1) DEFAULT 0",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_shipping_free SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_shipping_free BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_shipping_free SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4.10");
	}

	if (comp_vers("3.4.11", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN quantity_increment INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN quantity_increment INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN quantity_increment INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN quantity_increment INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4.11");
	}

	if (comp_vers("3.4.12", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types  ADD COLUMN min_quantity INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_types  ADD COLUMN min_quantity INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_types  ADD COLUMN min_quantity INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "shipping_types  ADD COLUMN min_quantity INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types  ADD COLUMN max_quantity INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_types  ADD COLUMN max_quantity INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_types  ADD COLUMN max_quantity INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "shipping_types  ADD COLUMN max_quantity INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN top_order_item_id INT(11) DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN top_order_item_id INT4 DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN top_order_item_id INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN top_order_item_id INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "orders_items SET top_order_item_id=0 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "orders_items_top ON " . $table_prefix . "orders_items (top_order_item_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_shipping_free TINYINT(1) DEFAULT 0",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_shipping_free SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_shipping_free BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_shipping_free SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN shipping_cost DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN shipping_cost FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN shipping_cost FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN shipping_cost DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4.12");
	}


	if (comp_vers("3.4.13", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support ADD COLUMN site_id INT(11) NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "support ADD COLUMN site_id INT4 NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "support ADD COLUMN site_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support ADD COLUMN site_id INTEGER NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "support SET site_id=1 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "support_site_id ON " . $table_prefix . "support (site_id) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "items_images ADD COLUMN image_super VARCHAR(255) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "countries ADD COLUMN country_code_alpha3 VARCHAR(4) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4.13");
	}

	if (comp_vers("3.4.15", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN component_order INT(11) DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN component_order INT4 DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN component_order INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN component_order INTEGER DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN component_name VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items_properties ADD COLUMN property_order INT(11) DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items_properties ADD COLUMN property_order INT4 DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items_properties ADD COLUMN property_order INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items_properties ADD COLUMN property_order INTEGER DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN width DOUBLE(16,4) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN width FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN width FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN width DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN height DOUBLE(16,4) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN height FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN height FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN height DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN length DOUBLE(16,4) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN length FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN length FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN length DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN width DOUBLE(16,4) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN width FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN width FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN width DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN height DOUBLE(16,4) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN height FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN height FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN height DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN length DOUBLE(16,4) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN length FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN length FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN length DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4.15");
	}

	if (comp_vers("3.4.16", $current_db_version) == 1)
	{
		$sqls[] = "ALTER TABLE " . $table_prefix . "languages ADD COLUMN language_image_active VARCHAR(255) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.4.16");
	}

	if (comp_vers("3.5", $current_db_version) == 1)
	{
		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5");
	}

?>