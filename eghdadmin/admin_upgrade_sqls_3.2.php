<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_upgrade_sqls_3.2.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	check_admin_security("system_upgrade");

	if (comp_vers("3.1.1", $current_db_version) == 1)
	{
		// profile custom fields
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "user_profile_properties (";
		$mysql_sql .= "  `property_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `user_type_id` INT(11) default '0',";
		$mysql_sql .= "  `property_order` INT(11) default '0',";
		$mysql_sql .= "  `property_name` VARCHAR(255),";
		$mysql_sql .= "  `property_description` TEXT,";
		$mysql_sql .= "  `default_value` TEXT,";
		$mysql_sql .= "  `property_style` VARCHAR(255),";
		$mysql_sql .= "  `section_id` INT(11) default '0',";
		$mysql_sql .= "  `property_show` INT(11) default '0',";
		$mysql_sql .= "  `control_type` VARCHAR(16),";
		$mysql_sql .= "  `control_style` VARCHAR(255),";
		$mysql_sql .= "  `control_code` TEXT,";
		$mysql_sql .= "  `onchange_code` TEXT,";
		$mysql_sql .= "  `onclick_code` TEXT,";
		$mysql_sql .= "  `required` INT(11) default '0',";
		$mysql_sql .= "  `before_name_html` TEXT,";
		$mysql_sql .= "  `after_name_html` TEXT,";
		$mysql_sql .= "  `before_control_html` TEXT,";
		$mysql_sql .= "  `after_control_html` TEXT";
		$mysql_sql .= "  ,KEY payment_id (user_type_id)";
		$mysql_sql .= "  ,PRIMARY KEY (property_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "user_profile_properties START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "user_profile_properties (";
		$postgre_sql .= "  property_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "user_profile_properties'),";
		$postgre_sql .= "  user_type_id INT4 default '0',";
		$postgre_sql .= "  property_order INT4 default '0',";
		$postgre_sql .= "  property_name VARCHAR(255),";
		$postgre_sql .= "  property_description TEXT,";
		$postgre_sql .= "  default_value TEXT,";
		$postgre_sql .= "  property_style VARCHAR(255),";
		$postgre_sql .= "  section_id INT4 default '0',";
		$postgre_sql .= "  property_show INT4 default '0',";
		$postgre_sql .= "  control_type VARCHAR(16),";
		$postgre_sql .= "  control_style VARCHAR(255),";
		$postgre_sql .= "  control_code TEXT,";
		$postgre_sql .= "  onchange_code TEXT,";
		$postgre_sql .= "  onclick_code TEXT,";
		$postgre_sql .= "  required INT4 default '0',";
		$postgre_sql .= "  before_name_html TEXT,";
		$postgre_sql .= "  after_name_html TEXT,";
		$postgre_sql .= "  before_control_html TEXT,";
		$postgre_sql .= "  after_control_html TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (property_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "user_profile_properties (";
		$access_sql .= "  [property_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER,";
		$access_sql .= "  [property_order] INTEGER,";
		$access_sql .= "  [property_name] VARCHAR(255),";
		$access_sql .= "  [property_description] LONGTEXT,";
		$access_sql .= "  [default_value] LONGTEXT,";
		$access_sql .= "  [property_style] VARCHAR(255),";
		$access_sql .= "  [section_id] INTEGER,";
		$access_sql .= "  [property_show] INTEGER,";
		$access_sql .= "  [control_type] VARCHAR(16),";
		$access_sql .= "  [control_style] VARCHAR(255),";
		$access_sql .= "  [control_code] LONGTEXT,";
		$access_sql .= "  [onchange_code] LONGTEXT,";
		$access_sql .= "  [onclick_code] LONGTEXT,";
		$access_sql .= "  [required] INTEGER,";
		$access_sql .= "  [before_name_html] LONGTEXT,";
		$access_sql .= "  [after_name_html] LONGTEXT,";
		$access_sql .= "  [before_control_html] LONGTEXT,";
		$access_sql .= "  [after_control_html] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (property_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "user_profile_properties__61 ON " . $table_prefix . "user_profile_properties (user_type_id)";
		}


		$mysql_sql  = "CREATE TABLE " . $table_prefix . "user_profile_sections (";
		$mysql_sql .= "  `section_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `section_order` INT(11) default '0',";
		$mysql_sql .= "  `section_name` VARCHAR(64)";
		$mysql_sql .= "  ,PRIMARY KEY (section_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "user_profile_sections START 5";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "user_profile_sections (";
		$postgre_sql .= "  section_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "user_profile_sections'),";
		$postgre_sql .= "  section_order INT4 default '0',";
		$postgre_sql .= "  section_name VARCHAR(64)";
		$postgre_sql .= "  ,PRIMARY KEY (section_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "user_profile_sections (";
		$access_sql .= "  [section_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [section_order] INTEGER,";
		$access_sql .= "  [section_name] VARCHAR(64)";
		$access_sql .= "  ,PRIMARY KEY (section_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "user_profile_values (";
		$mysql_sql .= "  `property_value_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `property_id` INT(11) default '0',";
		$mysql_sql .= "  `property_value` VARCHAR(255),";
		$mysql_sql .= "  `hide_value` INT(11) default '0',";
		$mysql_sql .= "  `is_default_value` INT(11) default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (property_value_id)";
		$mysql_sql .= "  ,KEY property_id (property_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "user_profile_values START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "user_profile_values (";
		$postgre_sql .= "  property_value_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "user_profile_values'),";
		$postgre_sql .= "  property_id INT4 default '0',";
		$postgre_sql .= "  property_value VARCHAR(255),";
		$postgre_sql .= "  hide_value INT4 default '0',";
		$postgre_sql .= "  is_default_value INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (property_value_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "user_profile_values (";
		$access_sql .= "  [property_value_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [property_id] INTEGER,";
		$access_sql .= "  [property_value] VARCHAR(255),";
		$access_sql .= "  [hide_value] INTEGER,";
		$access_sql .= "  [is_default_value] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (property_value_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "user_profile_values_prop_62 ON " . $table_prefix . "user_profile_values (property_id)";
		}


		$mysql_sql  = "CREATE TABLE " . $table_prefix . "users_properties (";
		$mysql_sql .= "  `user_property_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `user_id` INT(11) default '0',";
		$mysql_sql .= "  `property_id` INT(11) default '0',";
		$mysql_sql .= "  `property_value` TEXT";
		$mysql_sql .= "  ,KEY order_id (user_id)";
		$mysql_sql .= "  ,KEY order_property_id (user_property_id)";
		$mysql_sql .= "  ,PRIMARY KEY (user_property_id)";
		$mysql_sql .= "  ,KEY property_id (property_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "users_properties START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "users_properties (";
		$postgre_sql .= "  user_property_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "users_properties'),";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  property_id INT4 default '0',";
		$postgre_sql .= "  property_value TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (user_property_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "users_properties (";
		$access_sql .= "  [user_property_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [property_id] INTEGER,";
		$access_sql .= "  [property_value] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (user_property_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_properties_order_id ON " . $table_prefix . "users_properties (user_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_properties_property_id ON " . $table_prefix . "users_properties (property_id)";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN is_hidden INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN is_hidden INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN is_hidden INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN is_sms_allowed INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN is_sms_allowed INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN is_sms_allowed INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN birth_year INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN birth_year INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN birth_year INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN birth_month INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN birth_month INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN birth_month INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN birth_day INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN birth_day INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN birth_day INTEGER"
		);
		$sqls[] = $sql_types[$db_type];
	}

	if (comp_vers("3.1.2", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "menus ADD COLUMN show_title INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "menus ADD COLUMN show_title INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "menus ADD COLUMN show_title INTEGER"
		);
		$sqls[] = $sql_types[$db_type];
	}

	if (comp_vers("3.1.3", $current_db_version) == 1)
	{
		$sqls[] = " INSERT INTO " . $table_prefix . "user_profile_sections (section_id,section_order,section_name) VALUES (1, 1, 'LOGIN_INFO_MSG')";
		$sqls[] = " INSERT INTO " . $table_prefix . "user_profile_sections (section_id,section_order,section_name) VALUES (2, 2, 'PERSONAL_DETAILS_MSG')";
		$sqls[] = " INSERT INTO " . $table_prefix . "user_profile_sections (section_id,section_order,section_name) VALUES (3, 3, 'DELIVERY_DETAILS_MSG')";
		$sqls[] = " INSERT INTO " . $table_prefix . "user_profile_sections (section_id,section_order,section_name) VALUES (4, 4, 'ADDITIONAL_DETAILS_MSG')";
	}

	if (comp_vers("3.1.4", $current_db_version) == 1)
	{
		if ($db_type == "access") {
			$sqls[] = " ALTER TABLE " . $table_prefix . "order_custom_properties ADD COLUMN validation_regexp LONGTEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "order_custom_properties ADD COLUMN regexp_error LONGTEXT";
		} else {
			$sqls[] = " ALTER TABLE " . $table_prefix . "order_custom_properties ADD COLUMN validation_regexp TEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "order_custom_properties ADD COLUMN regexp_error TEXT";
		}
	}

	if (comp_vers("3.1.5", $current_db_version) == 1)
	{
		if ($db_type == "access") {
			$sqls[] = " ALTER TABLE " . $table_prefix . "order_custom_properties ADD COLUMN options_values_sql LONGTEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "user_profile_properties ADD COLUMN validation_regexp LONGTEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "user_profile_properties ADD COLUMN regexp_error LONGTEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "user_profile_properties ADD COLUMN options_values_sql LONGTEXT";
		} else {
			$sqls[] = " ALTER TABLE " . $table_prefix . "order_custom_properties ADD COLUMN options_values_sql TEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "user_profile_properties ADD COLUMN validation_regexp TEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "user_profile_properties ADD COLUMN regexp_error TEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "user_profile_properties ADD COLUMN options_values_sql TEXT";
		}
	}

	if (comp_vers("3.1.6", $current_db_version) == 1)
	{
		$sqls[] = " ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN image_small VARCHAR(255)";
		$sqls[] = " ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN image_small_alt VARCHAR(255)";
		$sqls[] = " ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN image_large VARCHAR(255)";
		$sqls[] = " ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN image_large_alt VARCHAR(255)";
	}

	if (comp_vers("3.1.7", $current_db_version) == 1)
	{
		// add fields for subscriptions
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN admin_modified_ip VARCHAR(32) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN admin_modified_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN admin_modified_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN admin_modified_date DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN expiry_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN expiry_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN expiry_date DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN suspend_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN suspend_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN suspend_date DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN registration_last_step INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN registration_last_step INT4 default '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN registration_last_step INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN registration_total_steps INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN registration_total_steps INT4 default '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN registration_total_steps INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_subscription INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_subscription INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_subscription INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_period INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_period INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_period INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_interval INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_interval INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_interval INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_suspend INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_suspend INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_suspend INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN is_subscription INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN is_subscription INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN is_subscription INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN is_subscription_recurring INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN is_subscription_recurring INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN is_subscription_recurring INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_name VARCHAR(32) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_fee DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_fee FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_fee FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_period INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_period INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_period INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_interval INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_interval INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_interval INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_suspend INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_suspend INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN subscription_suspend INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

	}

	if (comp_vers("3.1.8", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_profile_sections ADD COLUMN is_active INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_profile_sections ADD COLUMN is_active INT4 default '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "user_profile_sections ADD COLUMN is_active INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_profile_sections ADD COLUMN step_number INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_profile_sections ADD COLUMN step_number INT4 default '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "user_profile_sections ADD COLUMN step_number INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "user_profile_sections SET is_active=1, step_number=1 ";

	}

	if (comp_vers("3.1.9", $current_db_version) == 1)
	{
		// add new hidden permission for admin
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admin_privileges ADD COLUMN is_hidden INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "admin_privileges ADD COLUMN is_hidden INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "admin_privileges ADD COLUMN is_hidden INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql = " SELECT privilege_id FROM  " . $table_prefix . "admin_privileges_settings WHERE block_name='admins_groups' AND permission=1 ";
		$db->query($sql);
		while ($db->next_record()) {
			$privilege_id = $db->f("privilege_id");
			$sql  = " INSERT INTO " . $table_prefix . "admin_privileges_settings (privilege_id, block_name, permission) VALUES (";
			$sql .= $db->tosql($privilege_id, INTEGER) . ", 'admins_hidden', 1)";
			$sqls[] = $sql;
		}
	}

	if (comp_vers("3.1.10", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN user_type_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN user_type_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN user_type_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "CREATE INDEX " . $table_prefix . "orders_user_type_id ON " . $table_prefix . "orders (user_type_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN user_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN user_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN user_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN user_type_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN user_type_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN user_type_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_user_id ON " . $table_prefix . "orders_items (user_id) ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_user_type_id ON " . $table_prefix . "orders_items (user_type_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN download_show_terms INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN download_show_terms INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN download_show_terms INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "access") {
			$sqls[] = " ALTER TABLE " . $table_prefix . "items ADD COLUMN download_terms_text LONGTEXT";
		} else {
			$sqls[] = " ALTER TABLE " . $table_prefix . "items ADD COLUMN download_terms_text TEXT";
		}
	}

	if (comp_vers("3.1.11", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN is_sms_allowed INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN is_sms_allowed INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN is_sms_allowed INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "users SET is_sms_allowed=1 ";
		$sqls[] = " UPDATE " . $table_prefix . "user_types SET is_sms_allowed=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN top_menu_type INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN top_menu_type INT4 default '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN top_menu_type INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "layouts SET top_menu_type=1 ";
		$sqls[] = " UPDATE " . $table_prefix . "layouts SET top_menu_type=3 WHERE layout_name LIKE '%Curved%' ";
	}

	if (comp_vers("3.1.12", $current_db_version) == 1)
	{
		// add new fields to the users table
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN nickname VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN msn_account VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN icq_number VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN user_site_url VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN last_visit_page VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN last_logged_ip VARCHAR(32) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN last_logged_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN last_logged_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN last_logged_date DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN is_hidden INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN is_hidden INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN is_hidden INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		// update users table
		$last_visit_date = va_timestamp() - 8640000;
		$sqls[] = " UPDATE " . $table_prefix . "users SET nickname=login ";
		$sqls[] = " UPDATE " . $table_prefix . "users SET is_hidden=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "users_nickname ON " . $table_prefix . "users (nickname)";
		$sqls[] = "CREATE INDEX " . $table_prefix . "users_last_visit_date ON " . $table_prefix . "users (last_visit_date)";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "allowed_cell_phones (";
		$mysql_sql .= "  `cell_phone_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `cell_phone_number` VARCHAR(32)";
		$mysql_sql .= "  ,KEY cell_phone_number (cell_phone_number)";
		$mysql_sql .= "  ,PRIMARY KEY (cell_phone_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "allowed_cell_phones START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "allowed_cell_phones (";
		$postgre_sql .= "  cell_phone_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "allowed_cell_phones'),";
		$postgre_sql .= "  cell_phone_number VARCHAR(32)";
		$postgre_sql .= "  ,PRIMARY KEY (cell_phone_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "allowed_cell_phones (";
		$access_sql .= "  [cell_phone_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [cell_phone_number] VARCHAR(32)";
		$access_sql .= "  ,PRIMARY KEY (cell_phone_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "allowed_cell_phones_cell_4 ON " . $table_prefix . "allowed_cell_phones (cell_phone_number)";
		}

		// forum_list
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN last_post_message_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN last_post_message_id INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN last_post_message_id INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		// forum changes
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN last_post_added DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN last_post_added TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN last_post_added DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN last_post_user_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN last_post_user_id INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN last_post_user_id INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN last_post_message_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN last_post_message_id INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN last_post_message_id INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		// forum_messages
		$sqls[] = " UPDATE " . $table_prefix . "forum_messages SET user_id=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "forum_messages_user_id ON " . $table_prefix . "forum_messages (user_id)";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_messages ADD COLUMN date_modified DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_messages ADD COLUMN date_modified TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_messages ADD COLUMN date_modified DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_messages ADD COLUMN admin_id_modified_by INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_messages ADD COLUMN admin_id_modified_by INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_messages ADD COLUMN admin_id_modified_by INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		// update support
		$sqls[] = "ALTER TABLE " . $table_prefix . "support ADD COLUMN affiliate_code VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support ADD COLUMN mail_cc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support ADD COLUMN mail_bcc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN mail_cc VARCHAR(255) ";
 		if ($db_type == "access") {
			$sqls[] = " ALTER TABLE " . $table_prefix . "support ADD COLUMN mail_headers LONGTEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "support ADD COLUMN mail_body_text LONGTEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "support ADD COLUMN mail_body_html LONGTEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN mail_headers LONGTEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN mail_body_text LONGTEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN mail_body_html LONGTEXT";
		} else {
			$sqls[] = " ALTER TABLE " . $table_prefix . "support ADD COLUMN mail_headers TEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "support ADD COLUMN mail_body_text TEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "support ADD COLUMN mail_body_html LONGTEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN mail_headers LONGTEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN mail_body_text LONGTEXT";
			$sqls[] = " ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN mail_body_html LONGTEXT";
		}

		// serial fields
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN serial_period INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN serial_period INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN serial_period INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items_serials ADD COLUMN serial_expiry DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items_serials ADD COLUMN serial_expiry TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items_serials ADD COLUMN serial_expiry DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

	}

	if (comp_vers("3.1.13", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN last_post_admin_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN last_post_admin_id INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN last_post_admin_id INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN last_post_admin_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN last_post_admin_id INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN last_post_admin_id INTEGER"
		);
		$sqls[] = $sql_types[$db_type];
	}

	if (comp_vers("3.1.14", $current_db_version) == 1)
	{
		$sqls[] = "ALTER TABLE " . $table_prefix . "admins ADD COLUMN nickname VARCHAR(32) ";
	}

	if (comp_vers("3.1.15", $current_db_version) == 1)
	{
		// add layout setting for Basket page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'basket', 'basket_block', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'basket', 'basket_recommended_block', 1, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'basket', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'basket', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'basket', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'basket', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'basket', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'basket', 'right_column_width', NULL, NULL)";

		// add layout setting for Sitemap page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'site_map', 'site_map_block', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'site_map', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'site_map', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'site_map', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'site_map', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'site_map', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0, 'site_map', 'right_column_width', NULL, NULL)";
	}

	if (is_array($sqls) && sizeof($sqls) > 0) {
		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2");
	}
?>