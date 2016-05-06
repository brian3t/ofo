<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_upgrade_sqls_2.5.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	check_admin_security("system_upgrade");

	if (comp_vers("2.1.5", $current_db_version) == "first") {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN start_html TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN start_html TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN start_html LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN middle_html TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN middle_html TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN middle_html LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN end_html TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN end_html TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN end_html LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];
	}
	if (comp_vers("2.1.6", $current_db_version) == 1) {
		$mysql_sql   = "CREATE TABLE " . $table_prefix . "items_related (";
		$mysql_sql  .= "`item_id` INT(11) NOT NULL default '0', ";
		$mysql_sql  .= "`related_id` INT(11) NOT NULL default '0', ";
		$mysql_sql  .= "`related_order` INT(11) NOT NULL default '1', ";
		$mysql_sql  .= "KEY item_id (item_id),";
		$mysql_sql  .= "KEY related_id (related_id))";
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "items_related (";
		$postgre_sql .= "item_id INT4 NOT NULL default '0', ";
		$postgre_sql .= "related_id INT4 NOT NULL default '0', ";
		$postgre_sql .= "related_order INT4 NOT NULL default '1') ";
		$access_sql  = "CREATE TABLE " . $table_prefix . "items_related (";
		$access_sql .= "[item_id] INTEGER, [related_id] INTEGER, [related_order] INTEGER) ";
		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];


		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN signature TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN signature TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN signature LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];
		if (VA_TYPE == "enterprise") {

			$mysql_sql   = "CREATE TABLE " . $table_prefix . "support_attachments (";
			$mysql_sql  .= "  `attachment_id` INT(11) NOT NULL AUTO_INCREMENT,";
			$mysql_sql  .= "  `support_id` INT(11) NOT NULL default '0',";
			$mysql_sql  .= "  `file_name` VARCHAR(255),";
			$mysql_sql  .= "  `date_added` DATETIME";
			$mysql_sql  .= "  ,PRIMARY KEY (attachment_id)";
			$mysql_sql  .= "  ,KEY support_id (support_id))";
			if ($db_type == "postgre") {
				$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "support_attachments START 1";
			}
			$postgre_sql  = "CREATE TABLE " . $table_prefix . "support_attachments (";
			$postgre_sql .= "  attachment_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "support_attachments'),";
			$postgre_sql .= "  support_id INT4 NOT NULL default '0',";
			$postgre_sql .= "  file_name VARCHAR(255),";
			$postgre_sql .= "  date_added TIMESTAMP";
			$postgre_sql .= "  ,PRIMARY KEY (attachment_id))";
			$access_sql  = "CREATE TABLE " . $table_prefix . "support_attachments (";
			$access_sql .= "  [attachment_id]  COUNTER  NOT NULL,";
			$access_sql .= "  [support_id] INTEGER,";
			$access_sql .= "  [file_name] VARCHAR(255),";
			$access_sql .= "  [date_added] DATETIME";
			$access_sql .= "  ,PRIMARY KEY (attachment_id))";
			$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
			$sqls[] = $sql_types[$db_type];


			$mysql_sql   = "CREATE TABLE " . $table_prefix . "support_departments (";
			$mysql_sql  .= "  `dep_id` INT(11) NOT NULL AUTO_INCREMENT,";
			$mysql_sql  .= "  `short_title` VARCHAR(255),";
			$mysql_sql  .= "  `full_title` VARCHAR(255),";
			$mysql_sql  .= "  `signature` TEXT,";
			$mysql_sql  .= "  `incoming_account` TEXT,";
			$mysql_sql  .= "  `outgoing_account` VARCHAR(50),";
			$mysql_sql  .= "  `show_for_user` INT(11) NOT NULL default '1'";
			$mysql_sql  .= "  ,PRIMARY KEY (dep_id))";
			if ($db_type == "postgre") {
				$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "support_departments START 1";
			}
			$postgre_sql  = "CREATE TABLE " . $table_prefix . "support_departments (";
			$postgre_sql .= "  dep_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "support_departments'),";
			$postgre_sql .= "  short_title VARCHAR(255),";
			$postgre_sql .= "  full_title VARCHAR(255),";
			$postgre_sql .= "  signature TEXT,";
			$postgre_sql .= "  incoming_account TEXT,";
			$postgre_sql .= "  outgoing_account VARCHAR(50),";
			$postgre_sql .= "  show_for_user INT4 NOT NULL default '1'";
			$postgre_sql .= "  ,PRIMARY KEY (dep_id))";
			$access_sql  = "CREATE TABLE " . $table_prefix . "support_departments (";
			$access_sql .= "  [dep_id]  COUNTER  NOT NULL,";
			$access_sql .= "  [short_title] VARCHAR(255),";
			$access_sql .= "  [full_title] VARCHAR(255),";
			$access_sql .= "  [signature] LONGTEXT,";
			$access_sql .= "  [incoming_account] LONGTEXT,";
			$access_sql .= "  [outgoing_account] VARCHAR(50),";
			$access_sql .= "  [show_for_user] INTEGER ";
			$access_sql .= "  ,PRIMARY KEY (dep_id))";
			$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
			$sqls[] = $sql_types[$db_type];


			$mysql_sql   = "CREATE TABLE " . $table_prefix . "support_predefined (";
			$mysql_sql  .= "  `reply_id` INT(11) NOT NULL AUTO_INCREMENT,";
			$mysql_sql  .= "  `subject` VARCHAR(255),";
			$mysql_sql  .= "  `body` TEXT,";
			$mysql_sql  .= "  `reply_type` VARCHAR(255)";
			$mysql_sql  .= "  ,PRIMARY KEY (reply_id))";
			if ($db_type == "postgre") {
				$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "support_predefined START 1";
			}
			$postgre_sql  = "CREATE TABLE " . $table_prefix . "support_predefined (";
			$postgre_sql .= "  reply_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "support_predefined'),";
			$postgre_sql .= "  subject VARCHAR(255),";
			$postgre_sql .= "  body TEXT,";
			$postgre_sql .= "  reply_type VARCHAR(255)";
			$postgre_sql .= "  ,PRIMARY KEY (reply_id))";
			$access_sql  = "CREATE TABLE " . $table_prefix . "support_predefined (";
			$access_sql .= "  [reply_id]  COUNTER  NOT NULL,";
			$access_sql .= "  [subject] VARCHAR(255),";
			$access_sql .= "  [body] LONGTEXT,";
			$access_sql .= "  [reply_type] VARCHAR(255)";
			$access_sql .= "  ,PRIMARY KEY (reply_id))";
			$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
			$sqls[] = $sql_types[$db_type];


			$mysql_sql   = "CREATE TABLE " . $table_prefix . "support_time_report (";
			$mysql_sql  .= "  `report_id` INT(11) NOT NULL AUTO_INCREMENT,";
			$mysql_sql  .= "  `admin_id` INT(11) NOT NULL default '0',";
			$mysql_sql  .= "  `support_id` INT(11) NOT NULL default '0',";
			$mysql_sql  .= "  `started_date` DATETIME,";
			$mysql_sql  .= "  `report_date` DATETIME,";
			$mysql_sql  .= "  `spent_hours` INT(11) default '0'";
			$mysql_sql  .= "  ,PRIMARY KEY (report_id)";
			$mysql_sql  .= "  ,KEY admin_id (admin_id)";
			$mysql_sql  .= "  ,KEY support_id (support_id))";
			if ($db_type == "postgre") {
				$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "support_time_report START 1";
			}
			$postgre_sql  = "CREATE TABLE " . $table_prefix . "support_time_report (";
			$postgre_sql .= "  report_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "support_time_report'),";
			$postgre_sql .= "  admin_id INT4 NOT NULL default '0',";
			$postgre_sql .= "  support_id INT4 NOT NULL default '0',";
			$postgre_sql .= "  started_date TIMESTAMP,";
			$postgre_sql .= "  report_date TIMESTAMP,";
			$postgre_sql .= "  spent_hours INT4 default '0'";
			$postgre_sql .= "  ,PRIMARY KEY (report_id))";
			$access_sql  = "CREATE TABLE " . $table_prefix . "support_time_report (";
			$access_sql .= "  [report_id]  COUNTER  NOT NULL,";
			$access_sql .= "  [admin_id] INTEGER,";
			$access_sql .= "  [support_id] INTEGER,";
			$access_sql .= "  [started_date] DATETIME,";
			$access_sql .= "  [report_date] DATETIME,";
			$access_sql .= "  [spent_hours] INTEGER";
			$access_sql .= "  ,PRIMARY KEY (report_id))";
			$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
			$sqls[] = $sql_types[$db_type];


			$mysql_sql   = "CREATE TABLE " . $table_prefix . "support_users_departments (";
			$mysql_sql  .= "  `admin_id` INT(11) NOT NULL default '0',";
			$mysql_sql  .= "  `dep_id` INT(11) NOT NULL default '0',";
			$mysql_sql  .= "  `is_default_dep` INT(11) default '0'";
			$mysql_sql  .= "  ,KEY admin_id (admin_id)";
			$mysql_sql  .= "  ,KEY dep_id (dep_id)";
			$mysql_sql  .= "  ,PRIMARY KEY (admin_id,dep_id))";
			$postgre_sql  = "CREATE TABLE " . $table_prefix . "support_users_departments (";
			$postgre_sql .= "  admin_id INT4 NOT NULL default '0',";
			$postgre_sql .= "  dep_id INT4 NOT NULL default '0',";
			$postgre_sql .= "  is_default_dep INT4 default '0'";
			$postgre_sql .= "  ,PRIMARY KEY (admin_id,dep_id))";
			$access_sql  = "CREATE TABLE " . $table_prefix . "support_users_departments (";
			$access_sql .= "  [admin_id] INTEGER NOT NULL,";
			$access_sql .= "  [dep_id] INTEGER NOT NULL,";
			$access_sql .= "  [is_default_dep] INTEGER";
			$access_sql .= "  ,PRIMARY KEY (admin_id,dep_id))";
			$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
			$sqls[] = $sql_types[$db_type];


			$sql_types = array(
				"mysql"   => "ALTER TABLE " . $table_prefix . "support ADD COLUMN `dep_id` INT(11) NOT NULL default '0' AFTER support_type_id ",
				"postgre"  => "ALTER TABLE " . $table_prefix . "support ADD COLUMN dep_id INT4 NOT NULL default '0'",
				"access" => "ALTER TABLE " . $table_prefix . "support ADD COLUMN [dep_id] INTEGER"
			);
			$sqls[] = $sql_types[$db_type];


			$sql_types = array(
				"mysql"   => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN `dep_id` INT(11) NOT NULL default '0' AFTER support_id",
				"postgre"  => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN dep_id INT4 NOT NULL default '0'",
				"access" => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN [dep_id] INTEGER"
			);
			$sqls[] = $sql_types[$db_type];


			$sql_types = array(
				"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN status_caption VARCHAR(255)",
				"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN status_caption VARCHAR(255)",
				"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN status_caption VARCHAR(255)"
			);
			$sqls[] = $sql_types[$db_type];


			$sql_types = array(
				"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN `is_user_new` INT(11) NOT NULL default '0' ",
				"postgre"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN is_user_new INT4 NOT NULL default '0'",
				"access" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN [is_user_new] INTEGER"
			);
			$sqls[] = $sql_types[$db_type];


			$sql_types = array(
				"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN `is_user_reply` INT(11) NOT NULL default '0' ",
				"postgre"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN is_user_reply INT4 NOT NULL default '0'",
				"access" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN [is_user_reply] INTEGER"
			);
			$sqls[] = $sql_types[$db_type];


			$sql_types = array(
				"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN `is_operation` INT(11) NOT NULL default '0' ",
				"postgre"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN is_operation INT4 NOT NULL default '0'",
				"access" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN [is_operation] INTEGER"
			);
			$sqls[] = $sql_types[$db_type];


			$sql_types = array(
				"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN `is_reassign` INT(11) NOT NULL default '0' ",
				"postgre"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN is_reassign INT4 NOT NULL default '0'",
				"access" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN [is_reassign] INTEGER"
			);
			$sqls[] = $sql_types[$db_type];


			$sql_types = array(
				"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN `is_internal` INT(11) NOT NULL default '0' ",
				"postgre"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN is_internal INT4 NOT NULL default '0'",
				"access" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN [is_internal] INTEGER"
			);
			$sqls[] = $sql_types[$db_type];


			$sql_types = array(
				"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN `is_list` INT(11) NOT NULL default '0' ",
				"postgre"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN is_list INT4 NOT NULL default '0'",
				"access" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN [is_list] INTEGER"
			);
			$sqls[] = $sql_types[$db_type];

			$sqls[] = " INSERT INTO " . $table_prefix . "support_departments (dep_id, short_title, full_title, show_for_user) VALUES (1, 'Support', 'Support', 1)";
			$sqls[] = " UPDATE " . $table_prefix . "support SET dep_id=1";
			$sqls[] = " UPDATE " . $table_prefix . "support_messages SET dep_id=1";
			$sqls[] = " UPDATE " . $table_prefix . "support_statuses SET status_caption=status_name ";
			$sqls[] = " UPDATE " . $table_prefix . "support_statuses SET is_user_new=1 WHERE status_id=1";
			$sqls[] = " UPDATE " . $table_prefix . "support_statuses SET is_operation=1 ";
			$sqls[] = " UPDATE " . $table_prefix . "support_statuses SET is_list=1 ";
		}

	}

	if (comp_vers("2.1.7", $current_db_version) == 1) {
		$mysql_sql   = "CREATE TABLE " . $table_prefix . "coupons (";
		$mysql_sql  .= "  `coupon_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `coupon_code` VARCHAR(64),";
		$mysql_sql  .= "  `coupon_title` VARCHAR(255),";
		$mysql_sql  .= "  `is_active` INT(11) default '1',";
		$mysql_sql  .= "  `discount_type` INT(11) default '0',";
		$mysql_sql  .= "  `discount_amount` DOUBLE(10,2) default '0',";
		$mysql_sql  .= "  `free_postage` INT(11) default '0',";
		$mysql_sql  .= "  `tax_free` INT(11) default '0',";
		$mysql_sql  .= "  `items_all` INT(11) default '1',";
		$mysql_sql  .= "  `items_ids` TEXT,";
		$mysql_sql  .= "  `users_ids` TEXT,";
		$mysql_sql  .= "  `minimum_amount` DOUBLE(10,2) default '0',";
		$mysql_sql  .= "  `expiry_date` DATETIME,";
		$mysql_sql  .= "  `is_exclusive` INT(11) default '0',";
		$mysql_sql  .= "  `quantity_limit` INT(11) default '1',";
		$mysql_sql  .= "  `coupon_uses` INT(11) default '0'";
		$mysql_sql  .= "  ,KEY coupon_code (coupon_code)";
		$mysql_sql  .= "  ,KEY coupon_id (coupon_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (coupon_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "coupons START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "coupons (";
		$postgre_sql .= "  coupon_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "coupons'),";
		$postgre_sql .= "  coupon_code VARCHAR(64),";
		$postgre_sql .= "  coupon_title VARCHAR(255),";
		$postgre_sql .= "  is_active INT4 default '1',";
		$postgre_sql .= "  discount_type INT4 default '0',";
		$postgre_sql .= "  discount_amount FLOAT4 default '0',";
		$postgre_sql .= "  free_postage INT4 default '0',";
		$postgre_sql .= "  tax_free INT4 default '0',";
		$postgre_sql .= "  items_all INT4 default '1',";
		$postgre_sql .= "  items_ids TEXT,";
		$postgre_sql .= "  users_ids TEXT,";
		$postgre_sql .= "  minimum_amount FLOAT4 default '0',";
		$postgre_sql .= "  expiry_date TIMESTAMP,";
		$postgre_sql .= "  is_exclusive INT4 default '0',";
		$postgre_sql .= "  quantity_limit INT4 default '1',";
		$postgre_sql .= "  coupon_uses INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (coupon_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "coupons (";
		$access_sql .= "  [coupon_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [coupon_code] VARCHAR(64),";
		$access_sql .= "  [coupon_title] VARCHAR(255),";
		$access_sql .= "  [is_active] INTEGER,";
		$access_sql .= "  [discount_type] INTEGER,";
		$access_sql .= "  [discount_amount] FLOAT,";
		$access_sql .= "  [free_postage] INTEGER,";
		$access_sql .= "  [tax_free] INTEGER,";
		$access_sql .= "  [items_all] INTEGER,";
		$access_sql .= "  [items_ids] LONGTEXT,";
		$access_sql .= "  [users_ids] LONGTEXT,";
		$access_sql .= "  [minimum_amount] FLOAT,";
		$access_sql .= "  [expiry_date] DATETIME,";
		$access_sql .= "  [is_exclusive] INTEGER,";
		$access_sql .= "  [quantity_limit] INTEGER,";
		$access_sql .= "  [coupon_uses] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (coupon_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN coupons_ids TEXT AFTER affiliate_code",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN coupons_ids TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN coupons_ids LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN coupons_ids TEXT AFTER item_type_id",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN coupons_ids TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN coupons_ids LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN real_price DOUBLE(16,2) default '0' AFTER buying_price",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN real_price FLOAT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN real_price FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN discount_amount DOUBLE(16,2) default '0' AFTER real_price",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN discount_amount FLOAT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN discount_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql  = " INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) ";
		$sql .= " VALUES ('global', 'coupons_enable', '1') ";
		$sqls[] = $sql;

	}

	if (comp_vers("2.1.8", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_parameters ADD COLUMN `not_passed` INT(11) NOT NULL default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_parameters ADD COLUMN not_passed INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_parameters ADD COLUMN [not_passed] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN `is_advanced` INT(11) NOT NULL default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN is_advanced INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN [is_advanced] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN `advanced_url` VARCHAR(255) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN advanced_url VARCHAR(255) ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN [advanced_url] VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN `response_type` VARCHAR(64) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN response_type VARCHAR(64) ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN [response_type] VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN `response_delimiter` VARCHAR(64) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN response_delimiter VARCHAR(64) ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN [response_delimiter] VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN `status_parameter` VARCHAR(64) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN status_parameter VARCHAR(64) ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN [status_parameter] VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN `status_success_regexp` VARCHAR(255) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN status_success_regexp VARCHAR(255) ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN [status_success_regexp] VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN `success_status_id` INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN success_status_id INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN [success_status_id] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN `failure_status_id` INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN failure_status_id INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN [failure_status_id] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN `status_message_parameter` VARCHAR(64) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN status_message_parameter VARCHAR(64) ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN [status_message_parameter] VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];
	}

	if (comp_vers("2.1.9", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "polls ADD COLUMN `total_votes` INT(11) NOT NULL default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "polls ADD COLUMN total_votes INT4 NOT NULL default '0'",
			"access" => "ALTER TABLE " . $table_prefix . "polls ADD COLUMN [total_votes] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "polls_options ADD COLUMN `is_default_value` INT(11) NOT NULL default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "polls_options ADD COLUMN is_default_value INT4 NOT NULL default '0'",
			"access" => "ALTER TABLE " . $table_prefix . "polls_options ADD COLUMN [is_default_value] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];
	}

	if (comp_vers("2.1.10", $current_db_version) == 1) {
		$mysql_sql   = "CREATE TABLE " . $table_prefix . "currencies (";
		$mysql_sql  .= "  `currency_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `is_default` INT(11) default '0',";
		$mysql_sql  .= "  `currency_title` VARCHAR(64),";
		$mysql_sql  .= "  `currency_code` VARCHAR(4),";
		$mysql_sql  .= "  `exchange_rate` DOUBLE(16,8) NOT NULL default '1',";
		$mysql_sql  .= "  `symbol_left` VARCHAR(32),";
		$mysql_sql  .= "  `symbol_right` VARCHAR(32)";
		$mysql_sql  .= "  ,KEY currency_code (currency_code)";
		$mysql_sql  .= "  ,PRIMARY KEY (currency_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "currencies START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "currencies (";
		$postgre_sql .= "  currency_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "currencies'),";
		$postgre_sql .= "  is_default INT4 default '0',";
		$postgre_sql .= "  currency_title VARCHAR(64),";
		$postgre_sql .= "  currency_code VARCHAR(4),";
		$postgre_sql .= "  exchange_rate FLOAT4 NOT NULL default '1',";
		$postgre_sql .= "  symbol_left VARCHAR(32),";
		$postgre_sql .= "  symbol_right VARCHAR(32)";
		$postgre_sql .= "  ,PRIMARY KEY (currency_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "currencies (";
		$access_sql .= "  [currency_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [is_default] INTEGER,";
		$access_sql .= "  [currency_title] VARCHAR(64),";
		$access_sql .= "  [currency_code] VARCHAR(4),";
		$access_sql .= "  [exchange_rate] FLOAT,";
		$access_sql .= "  [symbol_left] VARCHAR(32),";
		$access_sql .= "  [symbol_right] VARCHAR(32)";
		$access_sql .= "  ,PRIMARY KEY (currency_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$currency_sign = ""; $currency_position = 1;
		$sql = "SELECT setting_value FROM " . $table_prefix . "global_settings WHERE setting_name='currency_sign'";
		$db->query($sql);
		if ($db->next_record()) {
			$currency_sign = $db->f("setting_value");
			$sql = "SELECT setting_value FROM " . $table_prefix . "global_settings WHERE setting_name='currency_position'";
			$db->query($sql);
			if ($db->next_record()) {
				$currency_position = $db->f("setting_value");
			}
		}
		$symbol_left = ""; $symbol_right = "";
		if ($currency_position == 2) {
			$symbol_right = $currency_sign;
		} else {
			$symbol_left = $currency_sign;
		}

		$sql  = " INSERT INTO " . $table_prefix . "currencies (is_default, currency_title, ";
		$sql .= " currency_code, exchange_rate, symbol_left, symbol_right) VALUES (";
		$sql .= "1, 'Default Currency', 'DEF', 1,";
		$sql .= $db->tosql($symbol_left, TEXT) . ", ";
		$sql .= $db->tosql($symbol_right, TEXT) . ") ";
		$sqls[] = $sql;
	}

	if (comp_vers("2.2", $current_db_version) == 1) {
		if (VA_TYPE == "enterprise" || VA_TYPE == "standard") {
			$mysql_sql   = "CREATE TABLE " . $table_prefix . "articles (";
			$mysql_sql  .= "  `article_id` INT(11) NOT NULL AUTO_INCREMENT,";
			$mysql_sql  .= "  `language_code` VARCHAR(2),";
			$mysql_sql  .= "  `article_date` DATETIME,";
			$mysql_sql  .= "  `date_end` DATETIME,";
			$mysql_sql  .= "  `article_title` VARCHAR(255),";
			$mysql_sql  .= "  `article_template` VARCHAR(255),";
			$mysql_sql  .= "  `article_order` INT(11) default '0',";
			$mysql_sql  .= "  `author_name` VARCHAR(255),";
			$mysql_sql  .= "  `author_email` VARCHAR(255),";
			$mysql_sql  .= "  `author_url` VARCHAR(255),";
			$mysql_sql  .= "  `author_remote_address` VARCHAR(255),";
			$mysql_sql  .= "  `created_user_id` INT(11) default '0',";
			$mysql_sql  .= "  `created_admin_id` INT(11) default '0',";
			$mysql_sql  .= "  `updated_user_id` INT(11) default '0',";
			$mysql_sql  .= "  `updated_admin_id` INT(11) default '0',";
			$mysql_sql  .= "  `status_id` INT(11) default '0',";
			$mysql_sql  .= "  `is_html` INT(11) default '0',";
			$mysql_sql  .= "  `allowed_rate` VARCHAR(50),";
			$mysql_sql  .= "  `is_hot` INT(11) default '0',";
			$mysql_sql  .= "  `link_url` VARCHAR(255),";
			$mysql_sql  .= "  `download_url` VARCHAR(255),";
			$mysql_sql  .= "  `is_link_direct` VARCHAR(255),";
			$mysql_sql  .= "  `date_added` VARCHAR(255),";
			$mysql_sql  .= "  `date_updated` VARCHAR(255),";
			$mysql_sql  .= "  `keywords` TEXT,";
			$mysql_sql  .= "  `hot_description` TEXT,";
			$mysql_sql  .= "  `short_description` TEXT,";
			$mysql_sql  .= "  `full_description` TEXT,";
			$mysql_sql  .= "  `notes` TEXT,";
			$mysql_sql  .= "  `total_views` INT(11) default '0',";
			$mysql_sql  .= "  `total_votes` INT(11) default '0',";
			$mysql_sql  .= "  `total_points` INT(11) default '0',";
			$mysql_sql  .= "  `rating` DOUBLE(16,2) default '0',";
			$mysql_sql  .= "  `total_clicks` INT(11) default '0',";
			$mysql_sql  .= "  `image_small` VARCHAR(255),";
			$mysql_sql  .= "  `image_large` VARCHAR(255)";
			$mysql_sql  .= "  ,KEY article_id (article_id)";
			$mysql_sql  .= "  ,KEY language_code (language_code)";
			$mysql_sql  .= "  ,PRIMARY KEY (article_id))";

			if ($db_type == "postgre") {
				$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "articles START 1";
			}
			$postgre_sql  = "CREATE TABLE " . $table_prefix . "articles (";
			$postgre_sql .= "  article_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "articles'),";
			$postgre_sql .= "  language_code VARCHAR(2),";
			$postgre_sql .= "  article_date TIMESTAMP,";
			$postgre_sql .= "  date_end TIMESTAMP,";
			$postgre_sql .= "  article_title VARCHAR(255),";
			$postgre_sql .= "  article_template VARCHAR(255),";
			$postgre_sql .= "  article_order INT4 default '0',";
			$postgre_sql .= "  author_name VARCHAR(255),";
			$postgre_sql .= "  author_email VARCHAR(255),";
			$postgre_sql .= "  author_url VARCHAR(255),";
			$postgre_sql .= "  author_remote_address VARCHAR(255),";
			$postgre_sql .= "  created_user_id INT4 default '0',";
			$postgre_sql .= "  created_admin_id INT4 default '0',";
			$postgre_sql .= "  updated_user_id INT4 default '0',";
			$postgre_sql .= "  updated_admin_id INT4 default '0',";
			$postgre_sql .= "  status_id INT4 default '0',";
			$postgre_sql .= "  is_html INT4 default '0',";
			$postgre_sql .= "  allowed_rate VARCHAR(50),";
			$postgre_sql .= "  is_hot INT4 default '0',";
			$postgre_sql .= "  link_url VARCHAR(255),";
			$postgre_sql .= "  download_url VARCHAR(255),";
			$postgre_sql .= "  is_link_direct VARCHAR(255),";
			$postgre_sql .= "  date_added VARCHAR(255),";
			$postgre_sql .= "  date_updated VARCHAR(255),";
			$postgre_sql .= "  keywords TEXT,";
			$postgre_sql .= "  hot_description TEXT,";
			$postgre_sql .= "  short_description TEXT,";
			$postgre_sql .= "  full_description TEXT,";
			$postgre_sql .= "  notes TEXT,";
			$postgre_sql .= "  total_views INT4 default '0',";
			$postgre_sql .= "  total_votes INT4 default '0',";
			$postgre_sql .= "  total_points INT4 default '0',";
			$postgre_sql .= "  rating FLOAT4 default '0',";
			$postgre_sql .= "  total_clicks INT4 default '0',";
			$postgre_sql .= "  image_small VARCHAR(255),";
			$postgre_sql .= "  image_large VARCHAR(255)";
			$postgre_sql .= "  ,PRIMARY KEY (article_id))";

			$access_sql  = "CREATE TABLE " . $table_prefix . "articles (";
			$access_sql .= "  [article_id]  COUNTER  NOT NULL,";
			$access_sql .= "  [language_code] VARCHAR(2),";
			$access_sql .= "  [article_date] DATETIME,";
			$access_sql .= "  [date_end] DATETIME,";
			$access_sql .= "  [article_title] VARCHAR(255),";
			$access_sql .= "  [article_template] VARCHAR(255),";
			$access_sql .= "  [article_order] INTEGER,";
			$access_sql .= "  [author_name] VARCHAR(255),";
			$access_sql .= "  [author_email] VARCHAR(255),";
			$access_sql .= "  [author_url] VARCHAR(255),";
			$access_sql .= "  [author_remote_address] VARCHAR(255),";
			$access_sql .= "  [created_user_id] INTEGER,";
			$access_sql .= "  [created_admin_id] INTEGER,";
			$access_sql .= "  [updated_user_id] INTEGER,";
			$access_sql .= "  [updated_admin_id] INTEGER,";
			$access_sql .= "  [status_id] INTEGER,";
			$access_sql .= "  [is_html] INTEGER,";
			$access_sql .= "  [allowed_rate] VARCHAR(50),";
			$access_sql .= "  [is_hot] INTEGER,";
			$access_sql .= "  [link_url] VARCHAR(255),";
			$access_sql .= "  [download_url] VARCHAR(255),";
			$access_sql .= "  [is_link_direct] VARCHAR(255),";
			$access_sql .= "  [date_added] VARCHAR(255),";
			$access_sql .= "  [date_updated] VARCHAR(255),";
			$access_sql .= "  [keywords] LONGTEXT,";
			$access_sql .= "  [hot_description] LONGTEXT,";
			$access_sql .= "  [short_description] LONGTEXT,";
			$access_sql .= "  [full_description] LONGTEXT,";
			$access_sql .= "  [notes] LONGTEXT,";
			$access_sql .= "  [total_views] INTEGER,";
			$access_sql .= "  [total_votes] INTEGER,";
			$access_sql .= "  [total_points] INTEGER,";
			$access_sql .= "  [rating] FLOAT,";
			$access_sql .= "  [total_clicks] INTEGER,";
			$access_sql .= "  [image_small] VARCHAR(255),";
			$access_sql .= "  [image_large] VARCHAR(255)";
			$access_sql .= "  ,PRIMARY KEY (article_id))";

			$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
			$sqls[] = $sql_types[$db_type];

			$mysql_sql   = "CREATE TABLE " . $table_prefix . "articles_assigned (";
			$mysql_sql  .= "  `article_id` INT(11) NOT NULL default '0',";
			$mysql_sql  .= "  `category_id` INT(11) NOT NULL default '0'";
			$mysql_sql  .= "  ,PRIMARY KEY (article_id,category_id))";

			$postgre_sql  = "CREATE TABLE " . $table_prefix . "articles_assigned (";
			$postgre_sql .= "  article_id INT4 NOT NULL default '0',";
			$postgre_sql .= "  category_id INT4 NOT NULL default '0'";
			$postgre_sql .= "  ,PRIMARY KEY (article_id,category_id))";

			$access_sql  = "CREATE TABLE " . $table_prefix . "articles_assigned (";
			$access_sql .= "  [article_id] INTEGER NOT NULL,";
			$access_sql .= "  [category_id] INTEGER NOT NULL";
			$access_sql .= "  ,PRIMARY KEY (article_id,category_id))";

			$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
			$sqls[] = $sql_types[$db_type];

			$mysql_sql   = "CREATE TABLE " . $table_prefix . "articles_categories (";
			$mysql_sql  .= "  `category_id` INT(11) NOT NULL AUTO_INCREMENT,";
			$mysql_sql  .= "  `language_code` VARCHAR(2),";
			$mysql_sql  .= "  `parent_category_id` INT(11) default '0',";
			$mysql_sql  .= "  `alias_category_id` INT(11) default '0',";
			$mysql_sql  .= "  `category_path` VARCHAR(255) NOT NULL DEFAULT '',";
			$mysql_sql  .= "  `category_name` VARCHAR(255),";
			$mysql_sql  .= "  `category_order` INT(11) default '0',";
			$mysql_sql  .= "  `articles_list_template` VARCHAR(255),";
			$mysql_sql  .= "  `articles_details_template` VARCHAR(255),";
			$mysql_sql  .= "  `articles_order_column` VARCHAR(50),";
			$mysql_sql  .= "  `articles_order_direction` VARCHAR(50),";
			$mysql_sql  .= "  `article_list_fields` TEXT,";
			$mysql_sql  .= "  `article_details_fields` TEXT,";
			$mysql_sql  .= "  `article_required_fields` TEXT,";
			$mysql_sql  .= "  `moderators_ids` VARCHAR(255),";
			$mysql_sql  .= "  `is_hot` INT(11) default '0',";
			$mysql_sql  .= "  `allowed_view` INT(11) default '0',";
			$mysql_sql  .= "  `allowed_post` INT(11) default '0',";
			$mysql_sql  .= "  `allowed_rate` INT(11) default '0',";
			$mysql_sql  .= "  `short_description` TEXT,";
			$mysql_sql  .= "  `full_description` TEXT,";
			$mysql_sql  .= "  `image_small` VARCHAR(255),";
			$mysql_sql  .= "  `image_large` VARCHAR(255),";
			$mysql_sql  .= "  `total_articles` INT(11) default '0',";
			$mysql_sql  .= "  `total_subcategories` INT(11) default '0'";
			$mysql_sql  .= "  ,KEY category_path (category_path)";
			$mysql_sql  .= "  ,KEY parent_category_id (parent_category_id)";
			$mysql_sql  .= "  ,PRIMARY KEY (category_id))";

			if ($db_type == "postgre") {
				$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "articles_categories START 1";
			}
			$postgre_sql  = "CREATE TABLE " . $table_prefix . "articles_categories (";
			$postgre_sql .= "  category_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "articles_categories'),";
			$postgre_sql .= "  language_code VARCHAR(2),";
			$postgre_sql .= "  parent_category_id INT4 default '0',";
			$postgre_sql .= "  alias_category_id INT4 default '0',";
			$postgre_sql .= "  category_path VARCHAR(255) NOT NULL DEFAULT '',";
			$postgre_sql .= "  category_name VARCHAR(255),";
			$postgre_sql .= "  category_order INT4 default '0',";
			$postgre_sql .= "  articles_list_template VARCHAR(255),";
			$postgre_sql .= "  articles_details_template VARCHAR(255),";
			$postgre_sql .= "  articles_order_column VARCHAR(50),";
			$postgre_sql .= "  articles_order_direction VARCHAR(50),";
			$postgre_sql .= "  article_list_fields TEXT,";
			$postgre_sql .= "  article_details_fields TEXT,";
			$postgre_sql .= "  article_required_fields TEXT,";
			$postgre_sql .= "  moderators_ids VARCHAR(255),";
			$postgre_sql .= "  is_hot INT4 default '0',";
			$postgre_sql .= "  allowed_view INT4 default '0',";
			$postgre_sql .= "  allowed_post INT4 default '0',";
			$postgre_sql .= "  allowed_rate INT4 default '0',";
			$postgre_sql .= "  short_description TEXT,";
			$postgre_sql .= "  full_description TEXT,";
			$postgre_sql .= "  image_small VARCHAR(255),";
			$postgre_sql .= "  image_large VARCHAR(255),";
			$postgre_sql .= "  total_articles INT4 default '0',";
			$postgre_sql .= "  total_subcategories INT4 default '0'";
			$postgre_sql .= "  ,PRIMARY KEY (category_id))";

			$access_sql  = "CREATE TABLE " . $table_prefix . "articles_categories (";
			$access_sql .= "  [category_id]  COUNTER  NOT NULL,";
			$access_sql .= "  [language_code] VARCHAR(2),";
			$access_sql .= "  [parent_category_id] INTEGER,";
			$access_sql .= "  [alias_category_id] INTEGER,";
			$access_sql .= "  [category_path] VARCHAR(255),";
			$access_sql .= "  [category_name] VARCHAR(255),";
			$access_sql .= "  [category_order] INTEGER,";
			$access_sql .= "  [articles_list_template] VARCHAR(255),";
			$access_sql .= "  [articles_details_template] VARCHAR(255),";
			$access_sql .= "  [articles_order_column] VARCHAR(50),";
			$access_sql .= "  [articles_order_direction] VARCHAR(50),";
			$access_sql .= "  [article_list_fields] LONGTEXT,";
			$access_sql .= "  [article_details_fields] LONGTEXT,";
			$access_sql .= "  [article_required_fields] LONGTEXT,";
			$access_sql .= "  [moderators_ids] VARCHAR(255),";
			$access_sql .= "  [is_hot] INTEGER,";
			$access_sql .= "  [allowed_view] INTEGER,";
			$access_sql .= "  [allowed_post] INTEGER,";
			$access_sql .= "  [allowed_rate] INTEGER,";
			$access_sql .= "  [short_description] LONGTEXT,";
			$access_sql .= "  [full_description] LONGTEXT,";
			$access_sql .= "  [image_small] VARCHAR(255),";
			$access_sql .= "  [image_large] VARCHAR(255),";
			$access_sql .= "  [total_articles] INTEGER,";
			$access_sql .= "  [total_subcategories] INTEGER";
			$access_sql .= "  ,PRIMARY KEY (category_id))";

			$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
			$sqls[] = $sql_types[$db_type];

			if ($db_type == "postgre" || $db_type == "access") {
				$sqls[] = "CREATE INDEX " . $table_prefix . "articles_categories_parent_category_id ON " . $table_prefix . "articles_categories (parent_category_id)";
				$sqls[] = "CREATE INDEX " . $table_prefix . "articles_categories_category_path ON " . $table_prefix . "articles_categories (category_path)";
			}

			$mysql_sql   = "CREATE TABLE " . $table_prefix . "articles_images (";
			$mysql_sql  .= "  `image_id` INT(11) NOT NULL AUTO_INCREMENT,";
			$mysql_sql  .= "  `article_id` INT(11) default '0',";
			$mysql_sql  .= "  `image_name` VARCHAR(255),";
			$mysql_sql  .= "  `image_title` VARCHAR(255),";
			$mysql_sql  .= "  `image_width` VARCHAR(255),";
			$mysql_sql  .= "  `image_height` VARCHAR(255),";
			$mysql_sql  .= "  `image_alt` VARCHAR(255),";
			$mysql_sql  .= "  `image_align` VARCHAR(255),";
			$mysql_sql  .= "  `date_added` DATETIME";
			$mysql_sql  .= "  ,KEY article_id (article_id)";
			$mysql_sql  .= "  ,PRIMARY KEY (image_id))";

			if ($db_type == "postgre") {
				$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "articles_images START 1";
			}
			$postgre_sql  = "CREATE TABLE " . $table_prefix . "articles_images (";
			$postgre_sql .= "  image_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "articles_images'),";
			$postgre_sql .= "  article_id INT4 default '0',";
			$postgre_sql .= "  image_name VARCHAR(255),";
			$postgre_sql .= "  image_title VARCHAR(255),";
			$postgre_sql .= "  image_width VARCHAR(255),";
			$postgre_sql .= "  image_height VARCHAR(255),";
			$postgre_sql .= "  image_alt VARCHAR(255),";
			$postgre_sql .= "  image_align VARCHAR(255),";
			$postgre_sql .= "  date_added TIMESTAMP";
			$postgre_sql .= "  ,PRIMARY KEY (image_id))";

			$access_sql  = "CREATE TABLE " . $table_prefix . "articles_images (";
			$access_sql .= "  [image_id]  COUNTER  NOT NULL,";
			$access_sql .= "  [article_id] INTEGER,";
			$access_sql .= "  [image_name] VARCHAR(255),";
			$access_sql .= "  [image_title] VARCHAR(255),";
			$access_sql .= "  [image_width] VARCHAR(255),";
			$access_sql .= "  [image_height] VARCHAR(255),";
			$access_sql .= "  [image_alt] VARCHAR(255),";
			$access_sql .= "  [image_align] VARCHAR(255),";
			$access_sql .= "  [date_added] DATETIME";
			$access_sql .= "  ,PRIMARY KEY (image_id))";

			$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
			$sqls[] = $sql_types[$db_type];

			if ($db_type == "postgre" || $db_type == "access") {
				$sqls[] = "CREATE INDEX " . $table_prefix . "articles_images_article_id ON " . $table_prefix . "articles_images (article_id)";
			}

			$mysql_sql   = "CREATE TABLE " . $table_prefix . "articles_related (";
			$mysql_sql  .= "  `article_id` INT(11) NOT NULL default '0',";
			$mysql_sql  .= "  `related_id` INT(11) NOT NULL default '0',";
			$mysql_sql  .= "  `related_order` INT(11) NOT NULL default '1'";
			$mysql_sql  .= "  ,PRIMARY KEY (article_id,related_id))";

			$postgre_sql  = "CREATE TABLE " . $table_prefix . "articles_related (";
			$postgre_sql .= "  article_id INT4 NOT NULL default '0',";
			$postgre_sql .= "  related_id INT4 NOT NULL default '0',";
			$postgre_sql .= "  related_order INT4 NOT NULL default '1'";
			$postgre_sql .= "  ,PRIMARY KEY (article_id,related_id))";

			$access_sql  = "CREATE TABLE " . $table_prefix . "articles_related (";
			$access_sql .= "  [article_id] INTEGER NOT NULL,";
			$access_sql .= "  [related_id] INTEGER NOT NULL,";
			$access_sql .= "  [related_order] INTEGER";
			$access_sql .= "  ,PRIMARY KEY (article_id,related_id))";

			$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
			$sqls[] = $sql_types[$db_type];

			$mysql_sql   = "CREATE TABLE " . $table_prefix . "articles_reviews (";
			$mysql_sql  .= "  `review_id` INT(11) NOT NULL AUTO_INCREMENT,";
			$mysql_sql  .= "  `article_id` INT(11) NOT NULL default '0',";
			$mysql_sql  .= "  `recommended` INT(11) default '0',";
			$mysql_sql  .= "  `approved` INT(11) default '0',";
			$mysql_sql  .= "  `rating` INT(11) default '0',";
			$mysql_sql  .= "  `summary` TEXT,";
			$mysql_sql  .= "  `user_name` VARCHAR(255),";
			$mysql_sql  .= "  `remote_address` VARCHAR(255),";
			$mysql_sql  .= "  `date_added` DATETIME,";
			$mysql_sql  .= "  `comments` TEXT";
			$mysql_sql  .= "  ,KEY article_id (article_id)";
			$mysql_sql  .= "  ,PRIMARY KEY (review_id))";

			if ($db_type == "postgre") {
				$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "articles_reviews START 1";
			}
			$postgre_sql  = "CREATE TABLE " . $table_prefix . "articles_reviews (";
			$postgre_sql .= "  review_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "articles_reviews'),";
			$postgre_sql .= "  article_id INT4 NOT NULL default '0',";
			$postgre_sql .= "  recommended INT4 default '0',";
			$postgre_sql .= "  approved INT4 default '0',";
			$postgre_sql .= "  rating INT4 default '0',";
			$postgre_sql .= "  summary TEXT,";
			$postgre_sql .= "  user_name VARCHAR(255),";
			$postgre_sql .= "  remote_address VARCHAR(255),";
			$postgre_sql .= "  date_added TIMESTAMP,";
			$postgre_sql .= "  comments TEXT";
			$postgre_sql .= "  ,PRIMARY KEY (review_id))";

			$access_sql  = "CREATE TABLE " . $table_prefix . "articles_reviews (";
			$access_sql .= "  [review_id]  COUNTER  NOT NULL,";
			$access_sql .= "  [article_id] INTEGER,";
			$access_sql .= "  [recommended] INTEGER,";
			$access_sql .= "  [approved] INTEGER,";
			$access_sql .= "  [rating] INTEGER,";
			$access_sql .= "  [summary] LONGTEXT,";
			$access_sql .= "  [user_name] VARCHAR(255),";
			$access_sql .= "  [remote_address] VARCHAR(255),";
			$access_sql .= "  [date_added] DATETIME,";
			$access_sql .= "  [comments] LONGTEXT";
			$access_sql .= "  ,PRIMARY KEY (review_id))";

			$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
			$sqls[] = $sql_types[$db_type];

			if ($db_type == "postgre" || $db_type == "access") {
				$sqls[] = "CREATE INDEX " . $table_prefix . "articles_reviews_article_id ON " . $table_prefix . "articles_reviews (article_id)";
			}

			$mysql_sql   = "CREATE TABLE " . $table_prefix . "articles_statuses (";
			$mysql_sql  .= "  `status_id` INT(11) NOT NULL AUTO_INCREMENT,";
			$mysql_sql  .= "  `status_name` VARCHAR(255),";
			$mysql_sql  .= "  `status_description` VARCHAR(255),";
			$mysql_sql  .= "  `is_shown` INT(11) default '0',";
			$mysql_sql  .= "  `status_icon` VARCHAR(255),";
			$mysql_sql  .= "  `status_style` VARCHAR(255),";
			$mysql_sql  .= "  `allowed_view` INT(11) default '0'";
			$mysql_sql  .= "  ,PRIMARY KEY (status_id))";

			if ($db_type == "postgre") {
				$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "articles_statuses START 5";
			}
			$postgre_sql  = "CREATE TABLE " . $table_prefix . "articles_statuses (";
			$postgre_sql .= "  status_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "articles_statuses'),";
			$postgre_sql .= "  status_name VARCHAR(255),";
			$postgre_sql .= "  status_description VARCHAR(255),";
			$postgre_sql .= "  is_shown INT4 default '0',";
			$postgre_sql .= "  status_icon VARCHAR(255),";
			$postgre_sql .= "  status_style VARCHAR(255),";
			$postgre_sql .= "  allowed_view INT4 default '0'";
			$postgre_sql .= "  ,PRIMARY KEY (status_id))";

			$access_sql  = "CREATE TABLE " . $table_prefix . "articles_statuses (";
			$access_sql .= "  [status_id]  COUNTER  NOT NULL,";
			$access_sql .= "  [status_name] VARCHAR(255),";
			$access_sql .= "  [status_description] VARCHAR(255),";
			$access_sql .= "  [is_shown] INTEGER,";
			$access_sql .= "  [status_icon] VARCHAR(255),";
			$access_sql .= "  [status_style] VARCHAR(255),";
			$access_sql .= "  [allowed_view] INTEGER";
			$access_sql .= "  ,PRIMARY KEY (status_id))";

			$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
			$sqls[] = $sql_types[$db_type];

			$sqls[] = "INSERT INTO " . $table_prefix . "articles_statuses (status_id,status_name,status_description,is_shown,status_icon,status_style,allowed_view) VALUES (1 , 'New' , NULL , 1 , NULL , NULL , 1 )";
			$sqls[] = "INSERT INTO " . $table_prefix . "articles_statuses (status_id,status_name,status_description,is_shown,status_icon,status_style,allowed_view) VALUES (2 , 'Published' , NULL , 1 , NULL , NULL , 1 )";
			$sqls[] = "INSERT INTO " . $table_prefix . "articles_statuses (status_id,status_name,status_description,is_shown,status_icon,status_style,allowed_view) VALUES (3 , 'Pending' , NULL , 1 , NULL , NULL , 0 )";
			$sqls[] = "INSERT INTO " . $table_prefix . "articles_statuses (status_id,status_name,status_description,is_shown,status_icon,status_style,allowed_view) VALUES (4 , 'Hidden' , NULL , 1 , NULL , NULL , 0 )";

			// create news, events and FAQ categories
			$sqls[] = "INSERT INTO " . $table_prefix . "articles_categories (category_id, parent_category_id, category_path, category_name, category_order, articles_order_column, articles_order_direction, article_list_fields, article_details_fields, article_required_fields, allowed_view, allowed_rate) VALUES (1, 0, '0,', 'News', 1, 'article_date', 'DESC', 'article_date,article_title,image_small,short_description', 'article_date,article_title,image_large,full_description', 'article_date,article_title', 1, 0)";
			$sqls[] = "INSERT INTO " . $table_prefix . "articles_categories (category_id, parent_category_id, category_path, category_name, category_order, articles_order_column, articles_order_direction, article_list_fields, article_details_fields, article_required_fields, allowed_view, allowed_rate) VALUES (2, 0, '0,', 'Events', 2, 'article_date', 'DESC', 'article_date,date_end,article_title,image_small,short_description','article_date,date_end,article_title,image_large,full_description', 'article_date,date_end,article_title', 1, 0)";
			$sqls[] = "INSERT INTO " . $table_prefix . "articles_categories (category_id, parent_category_id, category_path, category_name, category_order, articles_order_column, articles_order_direction, article_list_fields, article_details_fields, article_required_fields, allowed_view, allowed_rate) VALUES (3, 0, '0,', 'FAQ', 3, 'article_order', 'ASC', 'article_title,full_description', 'article_title,full_description', 'article_title,full_description', 1, 1)";

			// create news list layout
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_1', 'left_column_hide', NULL, '1')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_1', 'left_column_width', NULL, '')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_1', 'middle_column_hide', NULL, '0')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_1', 'middle_column_width', NULL, '100%')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_1', 'right_column_hide', NULL, '1')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_1', 'right_column_width', NULL, '')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_1', 'a_list_1', 0, 'middle')";

			// create news details layout
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_1', 'left_column_hide', NULL, '1')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_1', 'left_column_width', NULL, '')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_1', 'middle_column_hide', NULL, '0')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_1', 'middle_column_width', NULL, '100%')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_1', 'right_column_hide', NULL, '1')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_1', 'right_column_width', NULL, '')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_1', 'a_details_1', 0, 'middle')";

			// create events list layout
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_2', 'left_column_hide', NULL, '1')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_2', 'left_column_width', NULL, '')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_2', 'middle_column_hide', NULL, '0')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_2', 'middle_column_width', NULL, '100%')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_2', 'right_column_hide', NULL, '1')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_2', 'right_column_width', NULL, '')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_2', 'a_list_2', 0, 'middle')";

			// create events details layout
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_2', 'left_column_hide', NULL, '1')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_2', 'left_column_width', NULL, '')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_2', 'middle_column_hide', NULL, '0')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_2', 'middle_column_width', NULL, '100%')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_2', 'right_column_hide', NULL, '1')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_2', 'right_column_width', NULL, '')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_2', 'a_details_2', 0, 'middle')";

			// create FAQ list layout
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_3', 'left_column_hide', NULL, '1')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_3', 'left_column_width', NULL, '')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_3', 'middle_column_hide', NULL, '0')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_3', 'middle_column_width', NULL, '100%')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_3', 'right_column_hide', NULL, '1')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_3', 'right_column_width', NULL, '')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_3', 'a_content_3', 0, 'middle')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_list_3', 'a_list_3', 1, 'middle')";

			// create FAQ details layout
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_3', 'left_column_hide', NULL, '1')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_3', 'left_column_width', NULL, '')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_3', 'middle_column_hide', NULL, '0')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_3', 'middle_column_width', NULL, '100%')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_3', 'right_column_hide', NULL, '1')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_3', 'right_column_width', NULL, '')";
			$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'a_details_3', 'a_details_3', 0, 'middle')";

			$article_id = 0;
			$article_order = 0;
			$author_remote_address = get_ip("REMOTE_ADDR");
			$session_admin_id = get_session("session_admin_id");
			$date_added = va_time();
			// import News
			$sql = "SELECT * FROM " . $table_prefix . "news ";
			$db->query($sql);
			while ($db->next_record()) {
				$article_id++;
				$article_order++;
				$article_date = $db->f("news_date", DATETIME);
				$article_title = $db->f("news_title");
				$author_name = $db->f("author");
				$is_html = $db->f("is_html");
				$status_id = ($db->f("is_showing") == 1) ? 2 : 4;
				$is_hot = ($db->f("show_on_index") == 1) ? 1 : 0;
				$short_description = $db->f("preview_text");
				$full_description = $db->f("news_body");

				$sql  = " INSERT INTO " . $table_prefix . "articles (article_id,article_order,article_date,article_title,author_name,author_remote_address,is_html,status_id,is_hot,short_description,full_description,total_views,total_votes,total_points,rating,total_clicks,created_admin_id,updated_admin_id,date_added,date_updated) VALUES (";
				$sql .= $db->tosql($article_id, INTEGER) . ", ";
				$sql .= $db->tosql($article_order, INTEGER) . ", ";
				$sql .= $db->tosql($article_date, DATETIME) . ", ";
				$sql .= $db->tosql($article_title, TEXT) . ", ";
				$sql .= $db->tosql($author_name, TEXT) . ", ";
				$sql .= $db->tosql($author_remote_address, TEXT) . ", ";
				$sql .= $db->tosql($is_html, INTEGER) . ", ";
				$sql .= $db->tosql($status_id, INTEGER) . ", ";
				$sql .= $db->tosql($is_hot, INTEGER) . ", ";
				$sql .= $db->tosql($short_description, TEXT) . ", ";
				$sql .= $db->tosql($full_description, TEXT) . ", ";
				$sql .= "0,0,0,0,0, ";
				$sql .= $db->tosql($session_admin_id, INTEGER) . ", ";
				$sql .= $db->tosql($session_admin_id, INTEGER) . ", ";
				$sql .= $db->tosql($date_added, DATETIME) . ", ";
				$sql .= $db->tosql($date_added, DATETIME) . ") ";

				$sqls[] = $sql;
				$sqls[] = " INSERT INTO " . $table_prefix . "articles_assigned (article_id, category_id) VALUES (" . $db->tosql($article_id, INTEGER) . ", 1)";
			}

			// import Events
			$article_order = 0;
			$sql = "SELECT * FROM " . $table_prefix . "events ";
			$db->query($sql);
			while ($db->next_record()) {
				$article_id++;
				$article_order++;
				$article_date = $db->f("event_date", DATETIME);
				$date_end = $article_date;
				$article_title = $db->f("event_title");
				$is_html = $db->f("is_html");
				$status_id = ($db->f("is_showing") == 1) ? 2 : 4;
				$is_hot = ($db->f("show_on_index") == 1) ? 1 : 0;
				$short_description = $db->f("preview_text");
				$full_description = $db->f("event_body");

				$sql  = " INSERT INTO " . $table_prefix . "articles (article_id,article_order,article_date,date_end,article_title,author_remote_address,is_html,status_id,is_hot,short_description,full_description,total_views,total_votes,total_points,rating,total_clicks,created_admin_id,updated_admin_id,date_added,date_updated) VALUES (";
				$sql .= $db->tosql($article_id, INTEGER) . ", ";
				$sql .= $db->tosql($article_order, INTEGER) . ", ";
				$sql .= $db->tosql($article_date, DATETIME) . ", ";
				$sql .= $db->tosql($date_end, DATETIME) . ", ";
				$sql .= $db->tosql($article_title, TEXT) . ", ";
				$sql .= $db->tosql($author_remote_address, TEXT) . ", ";
				$sql .= $db->tosql($is_html, INTEGER) . ", ";
				$sql .= $db->tosql($status_id, INTEGER) . ", ";
				$sql .= $db->tosql($is_hot, INTEGER) . ", ";
				$sql .= $db->tosql($short_description, TEXT) . ", ";
				$sql .= $db->tosql($full_description, TEXT) . ", ";
				$sql .= "0,0,0,0,0, ";
				$sql .= $db->tosql($session_admin_id, INTEGER) . ", ";
				$sql .= $db->tosql($session_admin_id, INTEGER) . ", ";
				$sql .= $db->tosql($date_added, DATETIME) . ", ";
				$sql .= $db->tosql($date_added, DATETIME) . ") ";

				$sqls[] = $sql;
				$sqls[] = " INSERT INTO " . $table_prefix . "articles_assigned (article_id, category_id) VALUES (" . $db->tosql($article_id, INTEGER) . ", 2)";
			}

			// import FAQ
			$sql = "SELECT * FROM " . $table_prefix . "faq ";
			$db->query($sql);
			while ($db->next_record()) {
				$article_id++;
				$article_order = $db->f("faq_order");
				$article_date = $date_added;
				$article_title = $db->f("question");
				$is_html = 0;
				$status_id = 2;
				$is_hot = 0;
				$short_description = "";
				$full_description = $db->f("answer");

				$sql  = " INSERT INTO " . $table_prefix . "articles (article_id,article_order,article_date,article_title,author_remote_address,is_html,status_id,is_hot,short_description,full_description,total_views,total_votes,total_points,rating,total_clicks,created_admin_id,updated_admin_id,date_added,date_updated) VALUES (";
				$sql .= $db->tosql($article_id, INTEGER) . ", ";
				$sql .= $db->tosql($article_order, INTEGER) . ", ";
				$sql .= $db->tosql($article_date, DATETIME) . ", ";
				$sql .= $db->tosql($article_title, TEXT) . ", ";
				$sql .= $db->tosql($author_remote_address, TEXT) . ", ";
				$sql .= $db->tosql($is_html, INTEGER) . ", ";
				$sql .= $db->tosql($status_id, INTEGER) . ", ";
				$sql .= $db->tosql($is_hot, INTEGER) . ", ";
				$sql .= $db->tosql($short_description, TEXT) . ", ";
				$sql .= $db->tosql($full_description, TEXT) . ", ";
				$sql .= "0,0,0,0,0, ";
				$sql .= $db->tosql($session_admin_id, INTEGER) . ", ";
				$sql .= $db->tosql($session_admin_id, INTEGER) . ", ";
				$sql .= $db->tosql($date_added, DATETIME) . ", ";
				$sql .= $db->tosql($date_added, DATETIME) . ") ";

				$sqls[] = $sql;
				$sqls[] = " INSERT INTO " . $table_prefix . "articles_assigned (article_id, category_id) VALUES (" . $db->tosql($article_id, INTEGER) . ", 3)";
			}

			// update header links
			$sqls[] = " UPDATE " . $table_prefix . "header_links SET menu_url='articles.php?category_id=1' WHERE menu_url='news.php'";
			$sqls[] = " UPDATE " . $table_prefix . "header_links SET menu_url='articles.php?category_id=2' WHERE menu_url='events.php'";
			$sqls[] = " UPDATE " . $table_prefix . "header_links SET menu_url='articles.php?category_id=3' WHERE menu_url='faq.php'";

			// update news and events blocks
			$sqls[] = " UPDATE " . $table_prefix . "page_settings SET setting_name='a_hot_1' WHERE setting_name='news_block'";
			$sqls[] = " UPDATE " . $table_prefix . "page_settings SET setting_name='a_hot_2' WHERE setting_name='events_block'";
		}

		// update products list layouts
		$layout_id = get_setting_value($settings, "layout_id");
		if (!strlen($layout_id)) {
			$sql = " SELECT MIN(layout_id) FROM " . $table_prefix . "layouts ";
			$db->query($sql);
			if ($db->next_record()) {
				$layout_id = $db->f("layout_id");
			}
		}
		$sql = "SELECT * FROM " . $table_prefix . "page_settings WHERE page_name='products' AND layout_id=" . $db->tosql($layout_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$setting_name = $db->f("setting_name");
			$setting_order = $db->f("setting_order");
			$setting_value = $db->f("setting_value");
			$sql  = " INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'products_list', ";
			$sql .= $db->tosql($setting_name, TEXT, true, false) . ",";
			$sql .= $db->tosql($setting_order, INTEGER) . ",";
			$sql .= $db->tosql($setting_value, TEXT, true, false) . ")";
			$sqls[] = $sql;
		}

		$sql = "SELECT * FROM " . $table_prefix . "page_settings WHERE page_name='details' AND layout_id=" . $db->tosql($layout_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$setting_name = $db->f("setting_name");
			$setting_order = $db->f("setting_order");
			$setting_value = $db->f("setting_value");
			$sql  = " INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'products_details', ";
			$sql .= $db->tosql($setting_name, TEXT, true, false) . ",";
			$sql .= $db->tosql($setting_order, INTEGER) . ",";
			$sql .= $db->tosql($setting_value, TEXT, true, false) . ")";
			$sqls[] = $sql;
		}

	}

	if (comp_vers("2.2.1", $current_db_version) == 1) {

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "features (";
		$mysql_sql  .= "  `feature_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `item_id` INT(11) default '0',";
		$mysql_sql  .= "  `group_id` INT(11) default '0',";
		$mysql_sql  .= "  `feature_name` VARCHAR(255),";
		$mysql_sql  .= "  `feature_value` VARCHAR(255)";
		$mysql_sql  .= "  ,KEY group_id (group_id)";
		$mysql_sql  .= "  ,KEY item_id (item_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (feature_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "features START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "features (";
		$postgre_sql .= "  feature_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "features'),";
		$postgre_sql .= "  item_id INT4 default '0',";
		$postgre_sql .= "  group_id INT4 default '0',";
		$postgre_sql .= "  feature_name VARCHAR(255),";
		$postgre_sql .= "  feature_value VARCHAR(255)";
		$postgre_sql .= "  ,PRIMARY KEY (feature_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "features (";
		$access_sql .= "  [feature_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [item_id] INTEGER,";
		$access_sql .= "  [group_id] INTEGER,";
		$access_sql .= "  [feature_name] VARCHAR(255),";
		$access_sql .= "  [feature_value] VARCHAR(255)";
		$access_sql .= "  ,PRIMARY KEY (feature_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "features_group_id ON " . $table_prefix . "features (group_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "features_item_id ON " . $table_prefix . "features (item_id)";
		}

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "features_default (";
		$mysql_sql  .= "  `feature_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `item_type_id` INT(11) default '0',";
		$mysql_sql  .= "  `group_id` INT(11) default '0',";
		$mysql_sql  .= "  `feature_name` VARCHAR(50),";
		$mysql_sql  .= "  `feature_value` TEXT";
		$mysql_sql  .= "  ,KEY group_id (group_id)";
		$mysql_sql  .= "  ,KEY item_type_id (item_type_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (feature_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "features_default START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "features_default (";
		$postgre_sql .= "  feature_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "features_default'),";
		$postgre_sql .= "  item_type_id INT4 default '0',";
		$postgre_sql .= "  group_id INT4 default '0',";
		$postgre_sql .= "  feature_name VARCHAR(50),";
		$postgre_sql .= "  feature_value TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (feature_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "features_default (";
		$access_sql .= "  [feature_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [item_type_id] INTEGER,";
		$access_sql .= "  [group_id] INTEGER,";
		$access_sql .= "  [feature_name] VARCHAR(50),";
		$access_sql .= "  [feature_value] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (feature_id));";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "features_default_group_id ON " . $table_prefix . "features_default (group_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "features_default_item_type_id ON " . $table_prefix . "features_default (item_type_id)";
		}

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "features_groups (";
		$mysql_sql  .= "  `group_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `group_order` INT(11) NOT NULL default '1',";
		$mysql_sql  .= "  `group_name` VARCHAR(255)";
		$mysql_sql  .= "  ,PRIMARY KEY (group_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "features_groups (";
		$postgre_sql .= "  group_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "features_groups'),";
		$postgre_sql .= "  group_order INT4 NOT NULL default '1',";
		$postgre_sql .= "  group_name VARCHAR(255)";
		$postgre_sql .= "  ,PRIMARY KEY (group_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "features_groups (";
		$access_sql .= "  [group_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [group_order] INTEGER,";
		$access_sql .= "  [group_name] VARCHAR(255)";
		$access_sql .= "  ,PRIMARY KEY (group_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "INSERT INTO " . $table_prefix . "features_groups (group_id,group_order,group_name) VALUES (1, 1, 'General' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "features_groups (group_id,group_order,group_name) VALUES (2, 2, 'Sizes' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "features_groups (group_id,group_order,group_name) VALUES (3, 3, 'Accessories' )";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN `is_compared` INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_compared INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN [is_compared] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
	}

	if (comp_vers("2.2.2", $current_db_version) == 1) {

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "items_accessories (";
		$mysql_sql  .= "  `item_id` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `accessory_id` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `accessory_order` INT(11) NOT NULL default '1'";
		$mysql_sql  .= "  ,PRIMARY KEY (item_id,accessory_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "items_accessories (";
		$postgre_sql .= "  item_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  accessory_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  accessory_order INT4 NOT NULL default '1'";
		$postgre_sql .= "  ,PRIMARY KEY (item_id,accessory_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "items_accessories (";
		$access_sql .= "  [item_id] INTEGER NOT NULL,";
		$access_sql .= "  [accessory_id] INTEGER NOT NULL,";
		$access_sql .= "  [accessory_order] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (item_id,accessory_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];


		$mysql_sql   = "CREATE TABLE " . $table_prefix . "items_images (";
		$mysql_sql  .= "  `image_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `item_id` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `image_small` VARCHAR(255) NOT NULL,";
		$mysql_sql  .= "  `small_width` INT(11),";
		$mysql_sql  .= "  `small_height` INT(11),";
		$mysql_sql  .= "  `image_large` VARCHAR(255),";
		$mysql_sql  .= "  `image_title` VARCHAR(255),";
		$mysql_sql  .= "  `image_description` TEXT";
		$mysql_sql  .= "  ,KEY item_id (item_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (image_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "items_images START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "items_images (";
		$postgre_sql .= "  image_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "items_images'),";
		$postgre_sql .= "  item_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  image_small VARCHAR(255) NOT NULL,";
		$postgre_sql .= "  small_width INT4,";
		$postgre_sql .= "  small_height INT4,";
		$postgre_sql .= "  image_large VARCHAR(255),";
		$postgre_sql .= "  image_title VARCHAR(255),";
		$postgre_sql .= "  image_description TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (image_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "items_images (";
		$access_sql .= "  [image_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [item_id] INTEGER,";
		$access_sql .= "  [image_small] VARCHAR(255),";
		$access_sql .= "  [small_width] INTEGER,";
		$access_sql .= "  [small_height] INTEGER,";
		$access_sql .= "  [image_large] VARCHAR(255),";
		$access_sql .= "  [image_title] VARCHAR(255),";
		$access_sql .= "  [image_description] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (image_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "items_images_item_id ON " . $table_prefix . "items_images (item_id)";
		}

	}

	if (comp_vers("2.2.3", $current_db_version) == 1) {

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "newsletters (";
		$mysql_sql  .= "  `newsletter_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `newsletter_date` DATETIME NOT NULL,";
		$mysql_sql  .= "  `newsletter_subject` VARCHAR(255) NOT NULL,";
		$mysql_sql  .= "  `newsletter_body` TEXT,";
		$mysql_sql  .= "  `mail_type` INT(11) default 0,";
		$mysql_sql  .= "  `mail_from` VARCHAR(128),";
		$mysql_sql  .= "  `mail_reply_to` VARCHAR(128),";
		$mysql_sql  .= "  `mail_return_path` VARCHAR(128),";
		$mysql_sql  .= "  `mailing_start` DATETIME,";
		$mysql_sql  .= "  `mailing_end` DATETIME,";
		$mysql_sql  .= "  `emails_left` INT(11) default '0',";
		$mysql_sql  .= "  `emails_sent` INT(11) default '0',";
		$mysql_sql  .= "  `is_active` INT(11) default '0',";
		$mysql_sql  .= "  `is_sent` INT(11) default '0',";
		$mysql_sql  .= "  `is_prepared` INT(11) default '0',";
		$mysql_sql  .= "  `users_recipients` TEXT,";
		$mysql_sql  .= "  `admins_recipients` TEXT,";
		$mysql_sql  .= "  `subscribed_recipients` TEXT,";
		$mysql_sql  .= "  `added_by` INT(11) default '0',";
		$mysql_sql  .= "  `added_date` DATETIME,";
		$mysql_sql  .= "  `edited_by` INT(11) default '0',";
		$mysql_sql  .= "  `edited_date` DATETIME";
		$mysql_sql  .= "  ,PRIMARY KEY (newsletter_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "newsletters START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "newsletters (";
		$postgre_sql .= "  newsletter_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "newsletters'),";
		$postgre_sql .= "  newsletter_date TIMESTAMP NOT NULL,";
		$postgre_sql .= "  newsletter_subject VARCHAR(255) NOT NULL,";
		$postgre_sql .= "  newsletter_body TEXT,";
		$postgre_sql .= "  mail_type INT4 default '0',";
		$postgre_sql .= "  mail_from VARCHAR(128),";
		$postgre_sql .= "  mail_reply_to VARCHAR(128),";
		$postgre_sql .= "  mail_return_path VARCHAR(128),";
		$postgre_sql .= "  mailing_start TIMESTAMP,";
		$postgre_sql .= "  mailing_end TIMESTAMP,";
		$postgre_sql .= "  emails_left INT4 default '0',";
		$postgre_sql .= "  emails_sent INT4 default '0',";
		$postgre_sql .= "  is_active INT4 default '0',";
		$postgre_sql .= "  is_sent INT4 default '0',";
		$postgre_sql .= "  is_prepared INT4 default '0',";
		$postgre_sql .= "  users_recipients TEXT,";
		$postgre_sql .= "  admins_recipients TEXT,";
		$postgre_sql .= "  subscribed_recipients TEXT,";
		$postgre_sql .= "  added_by INT4 default '0',";
		$postgre_sql .= "  added_date TIMESTAMP,";
		$postgre_sql .= "  edited_by INT4 default '0',";
		$postgre_sql .= "  edited_date TIMESTAMP";
		$postgre_sql .= "  ,PRIMARY KEY (newsletter_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "newsletters (";
		$access_sql .= "  [newsletter_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [newsletter_date] DATETIME,";
		$access_sql .= "  [newsletter_subject] VARCHAR(255),";
		$access_sql .= "  [newsletter_body] LONGTEXT,";
		$access_sql .= "  [mail_type] INTEGER,";
		$access_sql .= "  [mail_from] VARCHAR(128),";
		$access_sql .= "  [mail_reply_to] VARCHAR(128),";
		$access_sql .= "  [mail_return_path] VARCHAR(128),";
		$access_sql .= "  [mailing_start] DATETIME,";
		$access_sql .= "  [mailing_end] DATETIME,";
		$access_sql .= "  [emails_left] INTEGER,";
		$access_sql .= "  [emails_sent] INTEGER,";
		$access_sql .= "  [is_active] INTEGER,";
		$access_sql .= "  [is_sent] INTEGER,";
		$access_sql .= "  [is_prepared] INTEGER,";
		$access_sql .= "  [users_recipients] LONGTEXT,";
		$access_sql .= "  [admins_recipients] LONGTEXT,";
		$access_sql .= "  [subscribed_recipients] LONGTEXT,";
		$access_sql .= "  [added_by] INTEGER,";
		$access_sql .= "  [added_date] DATETIME,";
		$access_sql .= "  [edited_by] INTEGER,";
		$access_sql .= "  [edited_date] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (newsletter_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "newsletters_emails (";
		$mysql_sql  .= "  `email_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `newsletter_id` INT(11) default '0',";
		$mysql_sql  .= "  `user_email` VARCHAR(128) NOT NULL,";
		$mysql_sql  .= "  `user_name` VARCHAR(128)";
		$mysql_sql  .= "  ,KEY newsletter_id (newsletter_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (email_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "newsletters_emails START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "newsletters_emails (";
		$postgre_sql .= "  email_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "newsletters_emails'),";
		$postgre_sql .= "  newsletter_id INT4 default '0',";
		$postgre_sql .= "  user_email VARCHAR(128) NOT NULL,";
		$postgre_sql .= "  user_name VARCHAR(128)";
		$postgre_sql .= "  ,PRIMARY KEY (email_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "newsletters_emails (";
		$access_sql .= "  [email_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [newsletter_id] INTEGER,";
		$access_sql .= "  [user_email] VARCHAR(128),";
		$access_sql .= "  [user_name] VARCHAR(128)";
		$access_sql .= "  ,PRIMARY KEY (email_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "newsletters_emails_newsletter_id ON " . $table_prefix . "newsletters_emails (newsletter_id)";
		}

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "newsletters_users (";
		$mysql_sql  .= "  `email_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `email` VARCHAR(50),";
		$mysql_sql  .= "  `date_added` DATETIME";
		$mysql_sql  .= "  ,KEY email (email)";
		$mysql_sql  .= "  ,PRIMARY KEY (email_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "newsletters_users START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "newsletters_users (";
		$postgre_sql .= "  email_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "newsletters_users'),";
		$postgre_sql .= "  email VARCHAR(50),";
		$postgre_sql .= "  date_added TIMESTAMP";
		$postgre_sql .= "  ,PRIMARY KEY (email_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "newsletters_users (";
		$access_sql .= "  [email_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [email] VARCHAR(50),";
		$access_sql .= "  [date_added] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (email_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "newsletters_users_email ON " . $table_prefix . "newsletters_users (email)";
		}

	}

	if (comp_vers("2.2.4", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN `advanced_php_lib` VARCHAR(255) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN advanced_php_lib VARCHAR(255) ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN [advanced_php_lib] VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN `is_active` INT(11) NOT NULL default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN is_active INT4 NOT NULL default '0'",
			"access" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN [is_active] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		// update products list layouts
		$payment_id = get_setting_value($settings, "payment_id");
		$sqls[] = " UPDATE " . $table_prefix . "payment_systems SET is_active=1 WHERE payment_id=" . $db->tosql($payment_id, INTEGER);

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN `payment_id` INT(11) NOT NULL default '0' AFTER user_id",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN payment_id INT4 NOT NULL default '0'",
			"access" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN [payment_id] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

	}

	if (comp_vers("2.2.5", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN remote_address VARCHAR(32) AFTER payment_id ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN remote_address VARCHAR(32) ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN remote_address VARCHAR(32) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN shipping_tracking_id VARCHAR(32) AFTER shipping_cost",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN shipping_tracking_id VARCHAR(32) ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN shipping_tracking_id VARCHAR(32) "
		);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "orders_notes (";
		$mysql_sql  .= "  `note_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `order_id` INT(11) default '0',";
		$mysql_sql  .= "  `note_title` VARCHAR(255),";
		$mysql_sql  .= "  `note_details` TEXT,";
		$mysql_sql  .= "  `show_for_user` INT(11) default '0',";
		$mysql_sql  .= "  `date_added` DATETIME,";
		$mysql_sql  .= "  `date_updated` DATETIME";
		$mysql_sql  .= "  ,KEY order_id (order_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (note_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "orders_notes START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "orders_notes (";
		$postgre_sql .= "  note_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "orders_notes'),";
		$postgre_sql .= "  order_id INT4 default '0',";
		$postgre_sql .= "  note_title VARCHAR(255),";
		$postgre_sql .= "  note_details TEXT,";
		$postgre_sql .= "  show_for_user INT4 default '0',";
		$postgre_sql .= "  date_added TIMESTAMP,";
		$postgre_sql .= "  date_updated TIMESTAMP";
		$postgre_sql .= "  ,PRIMARY KEY (note_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "orders_notes (";
		$access_sql .= "  [note_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [order_id] INTEGER,";
		$access_sql .= "  [note_title] VARCHAR(255),";
		$access_sql .= "  [note_details] LONGTEXT,";
		$access_sql .= "  [show_for_user] INTEGER,";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [date_updated] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (note_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_notes_order_id ON " . $table_prefix . "orders_notes (order_id)";
		}

	}

	if (comp_vers("2.2.6", $current_db_version) == 1) {

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "user_types (";
		$mysql_sql  .= "  `type_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `type_name` VARCHAR(128),";
		$mysql_sql  .= "  `is_default` INT(11) default '0'";
		$mysql_sql  .= "  ,PRIMARY KEY (type_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "user_types START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "user_types (";
		$postgre_sql .= "  type_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "user_types'),";
		$postgre_sql .= "  type_name VARCHAR(128),";
		$postgre_sql .= "  is_default INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "user_types (";
		$access_sql .= "  [type_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [type_name] VARCHAR(128),";
		$access_sql .= "  [is_default] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];


		$mysql_sql   = "CREATE TABLE " . $table_prefix . "user_types_settings (";
		$mysql_sql  .= "  `type_id` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `setting_name` VARCHAR(64) NOT NULL,";
		$mysql_sql  .= "  `setting_value` VARCHAR(50)";
		$mysql_sql  .= "  ,PRIMARY KEY (type_id,setting_name))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "user_types_settings START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "user_types_settings (";
		$postgre_sql .= "  type_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  setting_name VARCHAR(64) NOT NULL,";
		$postgre_sql .= "  setting_value VARCHAR(50)";
		$postgre_sql .= "  ,PRIMARY KEY (type_id,setting_name))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "user_types_settings (";
		$access_sql .= "  [type_id] INTEGER NOT NULL,";
		$access_sql .= "  [setting_name] VARCHAR(64) NOT NULL,";
		$access_sql .= "  [setting_value] VARCHAR(50)";
		$access_sql .= "  ,PRIMARY KEY (type_id,setting_name))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "INSERT INTO " . $table_prefix . "user_types (type_id,type_name,is_default) VALUES (1, 'Customer', 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "user_types_settings (type_id,setting_name,setting_value) VALUES (1, 'new_profile', 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "user_types_settings (type_id,setting_name,setting_value) VALUES (1, 'edit_profile', 1)";
		$sqls[] = "INSERT INTO " . $table_prefix . "user_types_settings (type_id,setting_name,setting_value) VALUES (1, 'approve_profile', 1)";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN `company_id` INT(11) default '0' AFTER last_name ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN company_id INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN [company_id] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN `delivery_company_id` INT(11) default '0' AFTER last_name ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN delivery_company_id INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN [delivery_company_id] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN `company_id` INT(11) default '0' AFTER last_name ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN company_id INT4 default '0'",
			"access" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN [company_id] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN `delivery_company_id` INT(11) default '0' AFTER last_name ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN delivery_company_id INT4 default '0'",
			"access" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN [delivery_company_id] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN `user_type_id` INT(11) default '0' AFTER user_id ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN user_type_id INT4 default '0'",
			"access" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN [user_type_id] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN `is_approved` INT(11) default '0' AFTER user_type_id ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN is_approved INT4 default '0'",
			"access" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN [is_approved] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "UPDATE " . $table_prefix . "users SET user_type_id=1,is_approved=1 ";
		$sqls[] = "UPDATE " . $table_prefix . "global_settings SET setting_type='user_profile_1' WHERE setting_type='user_profile'";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN `shipping_taxable` INT(11) default '1' AFTER shipping_cost ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN shipping_taxable INT4 default '1'",
			"access" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN [shipping_taxable] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "orders SET shipping_taxable=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN `is_taxable` INT(11) default '1' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN is_taxable INT4 default '1'",
			"access" => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN [is_taxable] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "shipping_types SET is_taxable=1 ";

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "companies (";
		$mysql_sql  .= "  `company_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `company_name` VARCHAR(255) NOT NULL,";
		$mysql_sql  .= "  `image_small` VARCHAR(50),";
		$mysql_sql  .= "  `image_large` VARCHAR(50),";
		$mysql_sql  .= "  `address_info` TEXT,";
		$mysql_sql  .= "  `phone_number` VARCHAR(32),";
		$mysql_sql  .= "  `fax_number` VARCHAR(32),";
		$mysql_sql  .= "  `site_url` VARCHAR(128),";
		$mysql_sql  .= "  `contact_email` VARCHAR(128),";
		$mysql_sql  .= "  `short_description` TEXT,";
		$mysql_sql  .= "  `full_description` TEXT";
		$mysql_sql  .= "  ,PRIMARY KEY (company_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "companies START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "companies (";
		$postgre_sql .= "  company_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "companies'),";
		$postgre_sql .= "  company_name VARCHAR(255) NOT NULL,";
		$postgre_sql .= "  image_small VARCHAR(50),";
		$postgre_sql .= "  image_large VARCHAR(50),";
		$postgre_sql .= "  address_info TEXT,";
		$postgre_sql .= "  phone_number VARCHAR(32),";
		$postgre_sql .= "  fax_number VARCHAR(32),";
		$postgre_sql .= "  site_url VARCHAR(128),";
		$postgre_sql .= "  contact_email VARCHAR(128),";
		$postgre_sql .= "  short_description TEXT,";
		$postgre_sql .= "  full_description TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (company_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "companies (";
		$access_sql .= "  [company_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [company_name] VARCHAR(255),";
		$access_sql .= "  [image_small] VARCHAR(50),";
		$access_sql .= "  [image_large] VARCHAR(50),";
		$access_sql .= "  [address_info] LONGTEXT,";
		$access_sql .= "  [phone_number] VARCHAR(32),";
		$access_sql .= "  [fax_number] VARCHAR(32),";
		$access_sql .= "  [site_url] VARCHAR(128),";
		$access_sql .= "  [contact_email] VARCHAR(128),";
		$access_sql .= "  [short_description] LONGTEXT,";
		$access_sql .= "  [full_description] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (company_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "ads_assigned (";
		$mysql_sql  .= "  `item_id` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `category_id` INT(11) NOT NULL default '0'";
		$mysql_sql  .= "  ,PRIMARY KEY (item_id,category_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_assigned (";
		$postgre_sql .= "  item_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (item_id,category_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_assigned (";
		$access_sql .= "  [item_id] INTEGER NOT NULL,";
		$access_sql .= "  [category_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (item_id,category_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "ads_categories (";
		$mysql_sql  .= "  `category_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `language_code` VARCHAR(2),";
		$mysql_sql  .= "  `parent_category_id` INT(11) default '0',";
		$mysql_sql  .= "  `alias_category_id` INT(11) default '0',";
		$mysql_sql  .= "  `category_path` VARCHAR(255) NOT NULL,";
		$mysql_sql  .= "  `category_name` VARCHAR(255),";
		$mysql_sql  .= "  `category_order` INT(11) default '0',";
		$mysql_sql  .= "  `allowed_view` INT(11) default '0',";
		$mysql_sql  .= "  `allowed_post` INT(11) default '0',";
		$mysql_sql  .= "  `short_description` TEXT,";
		$mysql_sql  .= "  `full_description` TEXT,";
		$mysql_sql  .= "  `image_small` VARCHAR(255),";
		$mysql_sql  .= "  `image_large` VARCHAR(255),";
		$mysql_sql  .= "  `total_ads` INT(11) default '0',";
		$mysql_sql  .= "  `total_subcategories` INT(11) default '0'";
		$mysql_sql  .= "  ,KEY category_path (category_path)";
		$mysql_sql  .= "  ,KEY parent_category_id (parent_category_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (category_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_categories START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_categories (";
		$postgre_sql .= "  category_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "ads_categories'),";
		$postgre_sql .= "  language_code VARCHAR(2),";
		$postgre_sql .= "  parent_category_id INT4 default '0',";
		$postgre_sql .= "  alias_category_id INT4 default '0',";
		$postgre_sql .= "  category_path VARCHAR(255) NOT NULL,";
		$postgre_sql .= "  category_name VARCHAR(255),";
		$postgre_sql .= "  category_order INT4 default '0',";
		$postgre_sql .= "  allowed_view INT4 default '0',";
		$postgre_sql .= "  allowed_post INT4 default '0',";
		$postgre_sql .= "  short_description TEXT,";
		$postgre_sql .= "  full_description TEXT,";
		$postgre_sql .= "  image_small VARCHAR(255),";
		$postgre_sql .= "  image_large VARCHAR(255),";
		$postgre_sql .= "  total_ads INT4 default '0',";
		$postgre_sql .= "  total_subcategories INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_categories (";
		$access_sql .= "  [category_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [language_code] VARCHAR(2),";
		$access_sql .= "  [parent_category_id] INTEGER,";
		$access_sql .= "  [alias_category_id] INTEGER,";
		$access_sql .= "  [category_path] VARCHAR(255),";
		$access_sql .= "  [category_name] VARCHAR(255),";
		$access_sql .= "  [category_order] INTEGER,";
		$access_sql .= "  [allowed_view] INTEGER,";
		$access_sql .= "  [allowed_post] INTEGER,";
		$access_sql .= "  [short_description] LONGTEXT,";
		$access_sql .= "  [full_description] LONGTEXT,";
		$access_sql .= "  [image_small] VARCHAR(255),";
		$access_sql .= "  [image_large] VARCHAR(255),";
		$access_sql .= "  [total_ads] INTEGER,";
		$access_sql .= "  [total_subcategories] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (category_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "ads_categories_category_path ON " . $table_prefix . "ads_categories (category_path)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "ads_categories_parent_category_id ON " . $table_prefix . "ads_categories (parent_category_id)";
		}

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "ads_features (";
		$mysql_sql  .= "  `feature_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `item_id` INT(11) default '0',";
		$mysql_sql  .= "  `group_id` INT(11) default '0',";
		$mysql_sql  .= "  `feature_name` VARCHAR(255),";
		$mysql_sql  .= "  `feature_value` VARCHAR(255)";
		$mysql_sql  .= "  ,KEY group_id (group_id)";
		$mysql_sql  .= "  ,KEY item_id (item_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (feature_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_features START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_features (";
		$postgre_sql .= "  feature_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "ads_features'),";
		$postgre_sql .= "  item_id INT4 default '0',";
		$postgre_sql .= "  group_id INT4 default '0',";
		$postgre_sql .= "  feature_name VARCHAR(255),";
		$postgre_sql .= "  feature_value VARCHAR(255)";
		$postgre_sql .= "  ,PRIMARY KEY (feature_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_features (";
		$access_sql .= "  [feature_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [item_id] INTEGER,";
		$access_sql .= "  [group_id] INTEGER,";
		$access_sql .= "  [feature_name] VARCHAR(255),";
		$access_sql .= "  [feature_value] VARCHAR(255)";
		$access_sql .= "  ,PRIMARY KEY (feature_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "ads_features_group_id ON " . $table_prefix . "ads_features (group_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "ads_features_item_id ON " . $table_prefix . "ads_features (item_id)";
		}


		$mysql_sql   = "CREATE TABLE " . $table_prefix . "ads_features_default (";
		$mysql_sql  .= "  `feature_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `type_id` INT(11) default '0',";
		$mysql_sql  .= "  `group_id` INT(11) default '0',";
		$mysql_sql  .= "  `feature_name` VARCHAR(255),";
		$mysql_sql  .= "  `feature_value` VARCHAR(255)";
		$mysql_sql  .= "  ,KEY group_id (group_id)";
		$mysql_sql  .= "  ,KEY type_id (type_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (feature_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_features_default START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_features_default (";
		$postgre_sql .= "  feature_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "ads_features_default'),";
		$postgre_sql .= "  type_id INT4 default '0',";
		$postgre_sql .= "  group_id INT4 default '0',";
		$postgre_sql .= "  feature_name VARCHAR(50),";
		$postgre_sql .= "  feature_value TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (feature_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_features_default (";
		$access_sql .= "  [feature_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [type_id] INTEGER,";
		$access_sql .= "  [group_id] INTEGER,";
		$access_sql .= "  [feature_name] VARCHAR(50),";
		$access_sql .= "  [feature_value] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (feature_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "ads_features_default_group_id ON " . $table_prefix . "ads_features_default (group_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "ads_features_default_item_type_id ON " . $table_prefix . "ads_features_default (type_id)";
		}


		$mysql_sql   = "CREATE TABLE " . $table_prefix . "ads_features_groups (";
		$mysql_sql  .= "  `group_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `group_order` INT(11) NOT NULL default '1',";
		$mysql_sql  .= "  `group_name` VARCHAR(255)";
		$mysql_sql  .= "  ,PRIMARY KEY (group_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_features_groups START 4";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_features_groups (";
		$postgre_sql .= "  group_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "ads_features_groups'),";
		$postgre_sql .= "  group_order INT4 NOT NULL default '1',";
		$postgre_sql .= "  group_name VARCHAR(255)";
		$postgre_sql .= "  ,PRIMARY KEY (group_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_features_groups (";
		$access_sql .= "  [group_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [group_order] INTEGER,";
		$access_sql .= "  [group_name] VARCHAR(255)";
		$access_sql .= "  ,PRIMARY KEY (group_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "INSERT INTO " . $table_prefix . "ads_features_groups (group_id,group_order,group_name) VALUES (1 , 1 , 'General' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_features_groups (group_id,group_order,group_name) VALUES (2 , 2 , 'Sizes' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_features_groups (group_id,group_order,group_name) VALUES (3 , 3 , 'Accessories' )";

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "ads_images (";
		$mysql_sql  .= "  `image_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `item_id` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `image_small` VARCHAR(255),";
		$mysql_sql  .= "  `small_width` INT(11),";
		$mysql_sql  .= "  `small_height` INT(11),";
		$mysql_sql  .= "  `image_large` VARCHAR(255),";
		$mysql_sql  .= "  `image_title` VARCHAR(255),";
		$mysql_sql  .= "  `image_description` TEXT";
		$mysql_sql  .= "  ,KEY item_id (item_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (image_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_images START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_images (";
		$postgre_sql .= "  image_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "ads_images'),";
		$postgre_sql .= "  item_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  image_small VARCHAR(255),";
		$postgre_sql .= "  small_width INT4,";
		$postgre_sql .= "  small_height INT4,";
		$postgre_sql .= "  image_large VARCHAR(255),";
		$postgre_sql .= "  image_title VARCHAR(255),";
		$postgre_sql .= "  image_description TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (image_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_images (";
		$access_sql .= "  [image_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [item_id] INTEGER,";
		$access_sql .= "  [image_small] VARCHAR(255),";
		$access_sql .= "  [small_width] INTEGER,";
		$access_sql .= "  [small_height] INTEGER,";
		$access_sql .= "  [image_large] VARCHAR(255),";
		$access_sql .= "  [image_title] VARCHAR(255),";
		$access_sql .= "  [image_description] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (image_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "ads_images_item_id ON " . $table_prefix . "ads_images (item_id)";
		}

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "ads_items (";
		$mysql_sql  .= "  `item_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `type_id` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `language_code` VARCHAR(2),";
		$mysql_sql  .= "  `item_title` VARCHAR(50) NOT NULL,";
		$mysql_sql  .= "  `item_order` INT(11) default '0',";
		$mysql_sql  .= "  `user_id` INT(11) default '0',";
		$mysql_sql  .= "  `admin_id` INT(11) default '0',";
		$mysql_sql  .= "  `date_start` DATETIME,";
		$mysql_sql  .= "  `date_end` DATETIME,";
		$mysql_sql  .= "  `date_added` DATETIME,";
		$mysql_sql  .= "  `date_updated` DATETIME,";
		$mysql_sql  .= "  `image_small` VARCHAR(255),";
		$mysql_sql  .= "  `image_large` VARCHAR(255),";
		$mysql_sql  .= "  `is_hot` INT(11) default '0',";
		$mysql_sql  .= "  `hot_description` TEXT,";
		$mysql_sql  .= "  `short_description` TEXT,";
		$mysql_sql  .= "  `full_description` TEXT,";
		$mysql_sql  .= "  `price` DOUBLE(16,2) default '0',";
		$mysql_sql  .= "  `quantity` INT(11) default '0',";
		$mysql_sql  .= "  `availability` VARCHAR(255),";
		$mysql_sql  .= "  `location_info` TEXT,";
		$mysql_sql  .= "  `location_city` VARCHAR(128),";
		$mysql_sql  .= "  `location_state` VARCHAR(8),";
		$mysql_sql  .= "  `location_country` VARCHAR(4),";
		$mysql_sql  .= "  `is_approved` INT(11) default '0',";
		$mysql_sql  .= "  `is_compared` INT(11) default '0'";
		$mysql_sql  .= "  ,KEY item_title (item_title)";
		$mysql_sql  .= "  ,PRIMARY KEY (item_id)";
		$mysql_sql  .= "  ,KEY type_id (type_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_items START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_items (";
		$postgre_sql .= "  item_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "ads_items'),";
		$postgre_sql .= "  type_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  language_code VARCHAR(2),";
		$postgre_sql .= "  item_title VARCHAR(50) NOT NULL,";
		$postgre_sql .= "  item_order INT4 default '0',";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  admin_id INT4 default '0',";
		$postgre_sql .= "  date_start TIMESTAMP,";
		$postgre_sql .= "  date_end TIMESTAMP,";
		$postgre_sql .= "  date_added TIMESTAMP,";
		$postgre_sql .= "  date_updated TIMESTAMP,";
		$postgre_sql .= "  image_small VARCHAR(255),";
		$postgre_sql .= "  image_large VARCHAR(255),";
		$postgre_sql .= "  is_hot INT4 default '0',";
		$postgre_sql .= "  hot_description TEXT,";
		$postgre_sql .= "  short_description TEXT,";
		$postgre_sql .= "  full_description TEXT,";
		$postgre_sql .= "  price FLOAT4 default '0',";
		$postgre_sql .= "  quantity INT4 default '0',";
		$postgre_sql .= "  availability VARCHAR(255),";
		$postgre_sql .= "  location_info TEXT,";
		$postgre_sql .= "  location_city VARCHAR(128),";
		$postgre_sql .= "  location_state VARCHAR(8),";
		$postgre_sql .= "  location_country VARCHAR(4),";
		$postgre_sql .= "  is_approved INT4 default '0',";
		$postgre_sql .= "  is_compared INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (item_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_items (";
		$access_sql .= "  [item_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [type_id] INTEGER,";
		$access_sql .= "  [language_code] VARCHAR(2),";
		$access_sql .= "  [item_title] VARCHAR(50),";
		$access_sql .= "  [item_order] INTEGER,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [admin_id] INTEGER,";
		$access_sql .= "  [date_start] DATETIME,";
		$access_sql .= "  [date_end] DATETIME,";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [date_updated] DATETIME,";
		$access_sql .= "  [image_small] VARCHAR(255),";
		$access_sql .= "  [image_large] VARCHAR(255),";
		$access_sql .= "  [is_hot] INTEGER,";
		$access_sql .= "  [hot_description] LONGTEXT,";
		$access_sql .= "  [short_description] LONGTEXT,";
		$access_sql .= "  [full_description] LONGTEXT,";
		$access_sql .= "  [price] FLOAT,";
		$access_sql .= "  [quantity] INTEGER,";
		$access_sql .= "  [availability] VARCHAR(255),";
		$access_sql .= "  [location_info] LONGTEXT,";
		$access_sql .= "  [location_city] VARCHAR(128),";
		$access_sql .= "  [location_state] VARCHAR(8),";
		$access_sql .= "  [location_country] VARCHAR(4),";
		$access_sql .= "  [is_approved] INTEGER,";
		$access_sql .= "  [is_compared] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (item_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "ads_items_item_title ON " . $table_prefix . "ads_items (item_title)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "ads_items_type_id ON " . $table_prefix . "ads_items (type_id)";
		}

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "ads_properties (";
		$mysql_sql  .= "  `property_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `item_id` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `property_name` VARCHAR(255) NOT NULL,";
		$mysql_sql  .= "  `property_value` TEXT";
		$mysql_sql  .= "  ,KEY item_id (item_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (property_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_properties START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_properties (";
		$postgre_sql .= "  property_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "ads_properties'),";
		$postgre_sql .= "  item_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  property_name VARCHAR(255) NOT NULL,";
		$postgre_sql .= "  property_value TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (property_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_properties (";
		$access_sql .= "  [property_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [item_id] INTEGER,";
		$access_sql .= "  [property_name] VARCHAR(255),";
		$access_sql .= "  [property_value] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (property_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "ads_properties_item_id ON " . $table_prefix . "ads_properties (item_id)";
		}


		$mysql_sql   = "CREATE TABLE " . $table_prefix . "ads_properties_default (";
		$mysql_sql  .= "  `property_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `type_id` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `property_name` VARCHAR(255) NOT NULL,";
		$mysql_sql  .= "  `property_value` TEXT";
		$mysql_sql  .= "  ,KEY item_id (type_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (property_id))";
		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_properties START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_properties_default (";
		$postgre_sql .= "  property_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "ads_properties'),";
		$postgre_sql .= "  type_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  property_name VARCHAR(255) NOT NULL,";
		$postgre_sql .= "  property_value TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (property_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_properties_default (";
		$access_sql .= "  [property_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [type_id] INTEGER,";
		$access_sql .= "  [property_name] VARCHAR(255),";
		$access_sql .= "  [property_value] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (property_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "ads_properties_default_item_id ON " . $table_prefix . "ads_properties_default (type_id)";
		}


		$mysql_sql   = "CREATE TABLE " . $table_prefix . "ads_types (";
		$mysql_sql  .= "  `type_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `type_name` VARCHAR(50)";
		$mysql_sql  .= "  ,PRIMARY KEY (type_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_types START 3";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_types (";
		$postgre_sql .= "  type_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "ads_types'),";
		$postgre_sql .= "  type_name VARCHAR(50)";
		$postgre_sql .= "  ,PRIMARY KEY (type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_types (";
		$access_sql .= "  [type_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [type_name] VARCHAR(50)";
		$access_sql .= "  ,PRIMARY KEY (type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "INSERT INTO " . $table_prefix . "ads_types (`type_id`,`type_name`) VALUES (1 , 'Product' )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_types (`type_id`,`type_name`) VALUES (2 , 'Accessory' )";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN payment_info TEXT AFTER payment_name",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN payment_info TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN payment_info LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN payment_notes TEXT AFTER payment_info",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN payment_notes TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN payment_notes LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$cc_settings = array();
		$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='credit_card_info' ";
		$db->query($sql);
		while ($db->next_record()) {
			$cc_settings[$db->f("setting_name")] = $db->f("setting_value");
		}

		$sql = "SELECT payment_id FROM " . $table_prefix . "payment_systems ";
		$db->query($sql);
		while ($db->next_record()) {
			$payment_id = $db->f("payment_id");
			foreach ($cc_settings as $setting_name => $setting_value) {
				$sql  = " INSERT INTO " . $table_prefix . "global_settings (setting_type,setting_name,setting_value) VALUES (";
				$sql .= $db->tosql("credit_card_info_" . $payment_id, TEXT) . ",";
				$sql .= $db->tosql($setting_name, TEXT) . ", ";
				$sql .= $db->tosql($setting_value, TEXT) . ") ";
				$sqls[] = $sql;
			}
		}
		$sqls[] = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='credit_card_info' ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN state_code VARCHAR(8) AFTER tax_name ",
			"postgre" => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN state_code VARCHAR(8) ",
			"access"  => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN state_code VARCHAR(8) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN currency_code VARCHAR(4) AFTER coupons_ids ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN currency_code VARCHAR(4) ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN currency_code VARCHAR(4) "
		);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "orders_events (";
		$mysql_sql  .= "  `event_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `order_id` INT(11) default '0',";
		$mysql_sql  .= "  `admin_id` INT(11) default '0',";
		$mysql_sql  .= "  `event_date` DATETIME,";
		$mysql_sql  .= "  `event_name` VARCHAR(64),";
		$mysql_sql  .= "  `event_description` TEXT";
		$mysql_sql  .= "  ,KEY admin_id (admin_id)";
		$mysql_sql  .= "  ,KEY order_id (order_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (event_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "orders_events START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "orders_events (";
		$postgre_sql .= "  event_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "orders_events'),";
		$postgre_sql .= "  order_id INT4 default '0',";
		$postgre_sql .= "  admin_id INT4 default '0',";
		$postgre_sql .= "  event_date TIMESTAMP,";
		$postgre_sql .= "  event_name VARCHAR(64),";
		$postgre_sql .= "  event_description TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (event_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "orders_events (";
		$access_sql .= "  [event_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [order_id] INTEGER,";
		$access_sql .= "  [admin_id] INTEGER,";
		$access_sql .= "  [event_date] DATETIME,";
		$access_sql .= "  [event_name] VARCHAR(64),";
		$access_sql .= "  [event_description] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (event_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_events_admin_id ON " . $table_prefix . "orders_events (admin_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_events_order_id ON " . $table_prefix . "orders_events (order_id)";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN `last_order_id` INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN last_order_id INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN [last_order_id] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN `exported_order_id` INT(11) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN exported_order_id INT4 ",
			"access" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN [exported_order_id] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN `last_user_id` INT(11) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN last_user_id INT4 ",
			"access" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN [last_user_id] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN `exported_user_id` INT(11) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN exported_user_id INT4 ",
			"access" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN [exported_user_id] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN `download_notify` INT(11) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN download_notify INT4 ",
			"access" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN [download_notify] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN `mail_notify` INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN mail_notify INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN [mail_notify] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN `mail_from` VARCHAR(64) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN mail_from VARCHAR(64) ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN [mail_from] VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN `mail_cc` VARCHAR(128) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN mail_cc VARCHAR(128) ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN [mail_cc] VARCHAR(128) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN `mail_bcc` VARCHAR(128) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN mail_bcc VARCHAR(128) ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN [mail_bcc] VARCHAR(128) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN `mail_reply_to` VARCHAR(64) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN mail_reply_to VARCHAR(64) ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN [mail_reply_to] VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN `mail_return_path` VARCHAR(64) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN mail_return_path VARCHAR(64) ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN [mail_return_path] VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN `mail_type` INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN mail_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN [mail_type] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN `mail_subject` VARCHAR(255) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN mail_subject VARCHAR(255) ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN [mail_subject] VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN mail_body TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN mail_body TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN mail_body LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

	}

	if (comp_vers("2.2.7", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN `exported_order_fields` TEXT ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN exported_order_fields TEXT ",
			"access" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN [exported_order_fields] LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN `exported_user_fields` TEXT ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN exported_user_fields TEXT ",
			"access" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN [exported_user_fields] LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN `imported_user_fields` TEXT ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN imported_user_fields TEXT ",
			"access" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN [imported_user_fields] LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN `exported_item_fields` TEXT ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN exported_item_fields TEXT ",
			"access" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN [exported_item_fields] LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN `imported_item_fields` TEXT ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN imported_item_fields TEXT ",
			"access" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN [imported_item_fields] LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN last_visit_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN last_visit_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN last_visit_date DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN `personal_image` VARCHAR(255) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN personal_image VARCHAR(255) ",
			"access" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN [personal_image] VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN last_visit_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN last_visit_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN last_visit_date DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN `tare_weight` DOUBLE(16,4) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN tare_weight FLOAT4 ",
			"access" => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN [tare_weight] FLOAT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN `property_order` INT(11) default '1' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN property_order INT4 default '1' ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN [property_order] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN `control_code` TEXT ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN control_code TEXT ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN [control_code] LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN `onchange_code` TEXT ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN onchange_code TEXT ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN [onchange_code] LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN `onclick_code` TEXT ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN onclick_code TEXT ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN [onclick_code] LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN `manufacturer_code` VARCHAR(64) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN manufacturer_code VARCHAR(64) ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN [manufacturer_code] VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN `download_period` INT(11) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN download_period INT4 ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN [download_period] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN `download_path` VARCHAR(255) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN download_path VARCHAR(255) ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN [download_path] VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN `stock_level` INT(11) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN stock_level INT4 ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN [stock_level] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN `hide_out_of_stock` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN hide_out_of_stock INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN [hide_out_of_stock] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN `manufacturer_code` VARCHAR(255) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN manufacturer_code VARCHAR(255) ",
			"access" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN [manufacturer_code] VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "items_properties SET property_order=1 ";
	}

	if (comp_vers("2.2.8", $current_db_version) == 1) {

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "languages (";
		$mysql_sql  .= "  `language_code` VARCHAR(2) NOT NULL,";
		$mysql_sql  .= "  `language_name` VARCHAR(255) NOT NULL,";
		$mysql_sql  .= "  `show_for_user` INT(11) default '0',";
		$mysql_sql  .= "  `language_image` VARCHAR(128),";
		$mysql_sql  .= "  `currency_code` VARCHAR(4)";
		$mysql_sql  .= "  ,PRIMARY KEY (language_code))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "languages (";
		$postgre_sql .= "  language_code VARCHAR(2) NOT NULL,";
		$postgre_sql .= "  language_name VARCHAR(255) NOT NULL,";
		$postgre_sql .= "  show_for_user INT4 default '0',";
		$postgre_sql .= "  language_image VARCHAR(128),";
		$postgre_sql .= "  currency_code VARCHAR(4)";
		$postgre_sql .= "  ,PRIMARY KEY (language_code))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "languages (";
		$access_sql .= "  [language_code] VARCHAR(2) NOT NULL,";
		$access_sql .= "  [language_name] VARCHAR(255),";
		$access_sql .= "  [show_for_user] INTEGER,";
		$access_sql .= "  [language_image] VARCHAR(128),";
		$access_sql .= "  [currency_code] VARCHAR(4)";
		$access_sql .= "  ,PRIMARY KEY (language_code))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "INSERT INTO " . $table_prefix . "languages (language_code,language_name,show_for_user,language_image) VALUES ('en', 'English', 1, 'images/flags/gb.gif')";
		$sqls[] = "INSERT INTO " . $table_prefix . "languages (language_code,language_name,show_for_user,language_image) VALUES ('es', 'Spanish', 1, 'images/flags/es.gif')";
		$sqls[] = "INSERT INTO " . $table_prefix . "languages (language_code,language_name,show_for_user,language_image) VALUES ('nl', 'Dutch', 1, 'images/flags/nl.gif')";
		$sqls[] = "INSERT INTO " . $table_prefix . "languages (language_code,language_name,show_for_user,language_image) VALUES ('el', 'Greek', 1, 'images/flags/gr.gif')";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN `transaction_id` VARCHAR(32) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN transaction_id VARCHAR(32) ",
			"access" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN [transaction_id] VARCHAR(32) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN `order_error` VARCHAR(255) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN order_error VARCHAR(255) ",
			"access" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN [order_error] VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "countries ADD COLUMN `currency_code` VARCHAR(4) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "countries ADD COLUMN currency_code VARCHAR(4) ",
			"access" => "ALTER TABLE " . $table_prefix . "countries ADD COLUMN [currency_code] VARCHAR(4) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN `use_on_list` INT(11) default '1' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN use_on_list INT4 default '1' ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN [use_on_list] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN `use_on_details` INT(11) default '1' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN use_on_details INT4 default '1' ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN [use_on_details] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items_properties SET use_on_list=1, use_on_details=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN `style_name` VARCHAR(64) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN style_name VARCHAR(64) ",
			"access" => "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN [style_name] VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "layouts SET style_name='default' WHERE layout_name LIKE 'Default%'";
		$sqls[] = " UPDATE " . $table_prefix . "layouts SET style_name='curved' WHERE layout_name LIKE 'Curved%'";
		$sqls[] = " UPDATE " . $table_prefix . "layouts SET style_name='silver' WHERE layout_name LIKE 'Silver%'";

		$final_settings = array();
		$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='order_confirmation' AND setting_name<>'intro_text'";
		$db->query($sql);
		while ($db->next_record()) {
			$final_settings[$db->f("setting_name")] = $db->f("setting_value");
		}
		$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='order_final' AND setting_name='final_message'";
		$db->query($sql);
		if ($db->next_record()) {
			$final_settings["success_message"] = $db->f("setting_value");
		}
		$final_settings["failure_message"] = "<div>An error: \"{error_desc}\" occurred while processing your order. We are sorry for any inconvenient caused.</div>";

		$sql = "SELECT payment_id FROM " . $table_prefix . "payment_systems ";
		$db->query($sql);
		while ($db->next_record()) {
			$payment_id = $db->f("payment_id");
			foreach ($final_settings as $setting_name => $setting_value) {
				$sql  = " INSERT INTO " . $table_prefix . "global_settings (setting_type,setting_name,setting_value) VALUES (";
				$sql .= $db->tosql("order_final_" . $payment_id, TEXT) . ",";
				$sql .= $db->tosql($setting_name, TEXT) . ", ";
				$sql .= $db->tosql($setting_value, TEXT) . ") ";
				$sqls[] = $sql;
			}
		}
		$sqls[] = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='order_confirmation' AND setting_name<>'intro_text' ";
		$sqls[] = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='order_final' AND setting_name='final_message' ";
		$sqls[] = " UPDATE " . $table_prefix . "global_settings SET setting_type='download_info' WHERE setting_type='order_final' ";
	}

	if (comp_vers("2.2.9", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN `error_message` VARCHAR(255) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN error_message VARCHAR(255) ",
			"access" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN [error_message] VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN `pending_message` VARCHAR(255) ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN pending_message VARCHAR(255) ",
			"access" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN [pending_message] VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "orders SET error_message=order_error ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders DROP COLUMN `order_error` ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders DROP COLUMN COLUMN order_error ",
			"access" => "ALTER TABLE " . $table_prefix . "orders DROP COLUMN [order_error] "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN `is_exported` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_exported INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN [is_exported] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN `currency_rate` DOUBLE(16,8) default '1' AFTER currency_code ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN currency_rate FLOAT4 default '1' AFTER currency_code ",
			"access" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN [currency_rate] FLOAT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql  = "CREATE TABLE " . $table_prefix . "black_ips (";
		$sql .= "  ip_address VARCHAR(32) NOT NULL,";
		$sql .= "  adress_action VARCHAR(32),";
		$sql .= "  adress_notes VARCHAR(255)";
		$sql .= "  ,PRIMARY KEY (ip_address))";
		$sqls[] = $sql;

	}

	if (comp_vers("2.2.10", $current_db_version) == 1) {

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "orders_items_serials (";
		$mysql_sql  .= "  `serial_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `order_id` INT(11) default '0',";
		$mysql_sql  .= "  `user_id` INT(11) default '0',";
		$mysql_sql  .= "  `order_item_id` INT(11) default '0',";
		$mysql_sql  .= "  `item_id` INT(11) default '0',";
		$mysql_sql  .= "  `serial_number` VARCHAR(128),";
		$mysql_sql  .= "  `activated` INT(11) default '0',";
		$mysql_sql  .= "  `activations_number` INT(11) default '0',";
		$mysql_sql  .= "  `serial_added` DATETIME";
		$mysql_sql  .= "  ,KEY item_id (item_id)";
		$mysql_sql  .= "  ,KEY order_id (order_id)";
		$mysql_sql  .= "  ,KEY order_item_id (order_item_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (serial_id)";
		$mysql_sql  .= "  ,KEY serial_number (serial_number)";
		$mysql_sql  .= "  ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "orders_items_serials START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "orders_items_serials (";
		$postgre_sql .= "  serial_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "orders_items_serials'),";
		$postgre_sql .= "  order_id INT4 default '0',";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  order_item_id INT4 default '0',";
		$postgre_sql .= "  item_id INT4 default '0',";
		$postgre_sql .= "  serial_number VARCHAR(128),";
		$postgre_sql .= "  activated INT4 default '0',";
		$postgre_sql .= "  activations_number INT4 default '0',";
		$postgre_sql .= "  serial_added TIMESTAMP";
		$postgre_sql .= "  ,PRIMARY KEY (serial_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "orders_items_serials (";
		$access_sql .= "  [serial_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [order_id] INTEGER,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [order_item_id] INTEGER,";
		$access_sql .= "  [item_id] INTEGER,";
		$access_sql .= "  [serial_number] VARCHAR(128),";
		$access_sql .= "  [activated] INTEGER,";
		$access_sql .= "  [activations_number] INTEGER,";
		$access_sql .= "  [serial_added] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (serial_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_serials_item_id ON " . $table_prefix . "orders_items_serials (item_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_serials_order_id ON " . $table_prefix . "orders_items_serials (order_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_serials_ord_16 ON " . $table_prefix . "orders_items_serials (order_item_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_serials_ser_17 ON " . $table_prefix . "orders_items_serials (serial_number)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_serials_user_id ON " . $table_prefix . "orders_items_serials (user_id)";
		}

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "orders_serials_activations (";
		$mysql_sql  .= "  `activation_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `serial_id` INT(11) default '0',";
		$mysql_sql  .= "  `order_id` INT(11) default '0',";
		$mysql_sql  .= "  `generation_key` VARCHAR(255),";
		$mysql_sql  .= "  `date_added` DATETIME";
		$mysql_sql  .= "  ,KEY order_id (order_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (activation_id)";
		$mysql_sql  .= "  ,KEY serial_id (serial_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "orders_serials_activations START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "orders_serials_activations (";
		$postgre_sql .= "  activation_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "orders_serials_activations'),";
		$postgre_sql .= "  serial_id INT4 default '0',";
		$postgre_sql .= "  order_id INT4 default '0',";
		$postgre_sql .= "  generation_key VARCHAR(255),";
		$postgre_sql .= "  date_added TIMESTAMP";
		$postgre_sql .= "  ,PRIMARY KEY (activation_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "orders_serials_activations (";
		$access_sql .= "  [activation_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [serial_id] INTEGER,";
		$access_sql .= "  [order_id] INTEGER,";
		$access_sql .= "  [generation_key] VARCHAR(255),";
		$access_sql .= "  [date_added] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (activation_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_serials_activatio_18 ON " . $table_prefix . "orders_serials_activations (order_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_serials_activatio_19 ON " . $table_prefix . "orders_serials_activations (serial_id)";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN `property_style` TEXT ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN property_style TEXT ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN [property_style] LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN `before_control_html` TEXT ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN before_control_html TEXT ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN [before_control_html] LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN `after_control_html` TEXT ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN after_control_html TEXT ",
			"access" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN [after_control_html] LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "items_properties SET after_control_html=end_html ";
		$sqls[] = " UPDATE " . $table_prefix . "items_properties SET end_html=NULL ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN `generate_serial` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN generate_serial INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN [generate_serial] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN `activations_number` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN activations_number INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN [activations_number] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "admins ADD COLUMN admin_alias VARCHAR(16) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admin_privileges ADD COLUMN `support_privilege` INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "admin_privileges ADD COLUMN support_privilege INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "admin_privileges ADD COLUMN [support_privilege] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN currency_value VARCHAR(16) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN success_message VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN `is_placed` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_placed INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN [is_placed] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN `allow_user_cancel` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN allow_user_cancel INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN [allow_user_cancel] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN `is_user_cancel` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN is_user_cancel INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN [is_user_cancel] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN template_name VARCHAR(255) ";

		if (VA_TYPE == "enterprise") {
			$sql_types = array(
				"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN `is_closed` INT(11) default '0' ",
				"postgre"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN is_closed INT4 default '0' ",
				"access" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN [is_closed] INTEGER "
			);
			$sqls[] = $sql_types[$db_type];

			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN status_icon VARCHAR(128) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN html_start VARCHAR(255) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN html_end VARCHAR(255) ";

		}

	}

	if (comp_vers("2.2.11", $current_db_version) == 1) {
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN language_code VARCHAR(2) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN `tax_free` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN tax_free INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN [tax_free] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN goods_taxable DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN goods_taxable FLOAT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN goods_taxable FLOAT"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "orders SET goods_taxable=goods_total ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN `tax_free` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN tax_free INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN [tax_free] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN `is_default` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN is_default INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN [is_default] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
	}

	if (comp_vers("2.2.12", $current_db_version) == 1) {
		$mysql_sql   = "CREATE TABLE " . $table_prefix . "order_custom_properties (";
		$mysql_sql  .= "  `property_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `payment_id` INT(11) default '0',";
		$mysql_sql  .= "  `property_order` INT(11) NOT NULL default '1',";
		$mysql_sql  .= "  `property_name` VARCHAR(255) NOT NULL,";
		$mysql_sql  .= "  `property_description` TEXT,";
		$mysql_sql  .= "  `default_value` TEXT,";
		$mysql_sql  .= "	`property_style` VARCHAR(255),";
		$mysql_sql  .= "  `property_type` INT(11) default '0',";
		$mysql_sql  .= "  `tax_free` INT(11) default '0',";
		$mysql_sql  .= "  `control_type` VARCHAR(16) NOT NULL,";
		$mysql_sql  .= "  `control_style` VARCHAR(255),";
		$mysql_sql  .= "  `control_code` TEXT,";
		$mysql_sql  .= "  `onchange_code` TEXT,";
		$mysql_sql  .= "  `onclick_code` TEXT,";
		$mysql_sql  .= "  `required` INT(11),";
		$mysql_sql  .= "  `before_name_html` TEXT,";
		$mysql_sql  .= "  `after_name_html` TEXT,";
		$mysql_sql  .= "	`before_control_html` TEXT,";
		$mysql_sql  .= "	`after_control_html` TEXT";
		$mysql_sql  .= "  ,PRIMARY KEY (property_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "order_custom_properties START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "order_custom_properties (";
		$postgre_sql .= "  property_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "order_custom_properties'),";
		$postgre_sql .= "  payment_id INT4 default '0',";
		$postgre_sql .= "  property_order INT4 default '0',";
		$postgre_sql .= "  property_name VARCHAR(255),";
		$postgre_sql .= "  property_description TEXT,";
		$postgre_sql .= "  default_value TEXT,";
		$postgre_sql .= "  property_style VARCHAR(255),";
		$postgre_sql .= "  property_type INT4 default '0',";
		$postgre_sql .= "  tax_free INT4 default '0',";
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

		$access_sql  = "CREATE TABLE " . $table_prefix . "order_custom_properties (";
		$access_sql .= "  [property_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [payment_id] INTEGER,";
		$access_sql .= "  [property_order] INTEGER,";
		$access_sql .= "  [property_name] VARCHAR(255),";
		$access_sql .= "  [property_description] LONGTEXT,";
		$access_sql .= "  [default_value] LONGTEXT,";
		$access_sql .= "  [property_style] VARCHAR(255),";
		$access_sql .= "  [property_type] INTEGER,";
		$access_sql .= "  [tax_free] INTEGER,";
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

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "order_custom_values (";
		$mysql_sql  .= "  `property_value_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `property_id` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `property_value` VARCHAR(255) NOT NULL,";
		$mysql_sql  .= "  `property_price` FLOAT (10,2),";
		$mysql_sql  .= "  `property_weight` FLOAT (10,4),";
		$mysql_sql  .= "  `hide_value` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `is_default_value` INT(11) default '0'";
		$mysql_sql  .= "  ,KEY hide_value (hide_value)";
		$mysql_sql  .= "  ,PRIMARY KEY (property_value_id)";
		$mysql_sql  .= "  ,KEY property_id (property_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "order_custom_values START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "order_custom_values (";
		$postgre_sql .= "  property_value_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "order_custom_values'),";
		$postgre_sql .= "  property_id INT4 default '0',";
		$postgre_sql .= "  property_value VARCHAR(255),";
		$postgre_sql .= "  property_price FLOAT4 default '0',";
		$postgre_sql .= "  property_weight FLOAT4 default '0',";
		$postgre_sql .= "  hide_value INT4 default '0',";
		$postgre_sql .= "  is_default_value INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (property_value_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "order_custom_values (";
		$access_sql .= "  [property_value_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [property_id] INTEGER,";
		$access_sql .= "  [property_value] VARCHAR(255),";
		$access_sql .= "  [property_price] FLOAT,";
		$access_sql .= "  [property_weight] FLOAT,";
		$access_sql .= "  [hide_value] INTEGER,";
		$access_sql .= "  [is_default_value] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (property_value_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "order_custom_values_pro_13 ON " . $table_prefix . "order_custom_values (property_id)";
		}

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "orders_properties (";
		$mysql_sql  .= "  `order_property_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `order_id` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `property_id` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `property_order` INT(11) NOT NULL default '1',";
		$mysql_sql  .= "  `property_type` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `property_name` VARCHAR(255) NOT NULL,";
		$mysql_sql  .= "  `property_value` TEXT,";
		$mysql_sql  .= "  `property_price` FLOAT (10,2),";
		$mysql_sql  .= "  `property_weight` FLOAT (10,4),";
		$mysql_sql  .= "  `tax_free` INT(11) default '0'";
		$mysql_sql  .= "  ,PRIMARY KEY (order_property_id)";
		$mysql_sql  .= "  ,KEY order_id (order_id)";
		$mysql_sql  .= "  ,KEY property_id (property_id)";
		$mysql_sql  .= "  ,KEY property_name (property_name))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "orders_properties START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "orders_properties (";
		$postgre_sql .= "  order_property_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "orders_properties'),";
		$postgre_sql .= "  order_id INT4 default '0',";
		$postgre_sql .= "  property_id INT4 default '0',";
		$postgre_sql .= "  property_order INT4 default '1',";
		$postgre_sql .= "  property_type INT4 default '0',";
		$postgre_sql .= "  property_name VARCHAR(255) NOT NULL,";
		$postgre_sql .= "  property_value TEXT,";
		$postgre_sql .= "  property_price FLOAT4 default '0',";
		$postgre_sql .= "  property_weight FLOAT4 default '0',";
		$postgre_sql .= "  tax_free INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (order_property_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "orders_properties (";
		$access_sql .= "  [order_property_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [order_id] INTEGER,";
		$access_sql .= "  [property_id] INTEGER,";
		$access_sql .= "  [property_order] INTEGER,";
		$access_sql .= "  [property_type] INTEGER,";
		$access_sql .= "  [property_name] VARCHAR(255),";
		$access_sql .= "  [property_value] LONGTEXT,";
		$access_sql .= "  [property_price] FLOAT,";
		$access_sql .= "  [property_weight] FLOAT,";
		$access_sql .= "  [tax_free] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (order_property_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_properties_order_id ON " . $table_prefix . "orders_properties (order_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_properties_order__19 ON " . $table_prefix . "orders_properties (order_property_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_properties_property_id ON " . $table_prefix . "orders_properties (property_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_properties_proper_20 ON " . $table_prefix . "orders_properties (property_name)";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN properties_total DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN properties_total FLOAT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN properties_total FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN properties_taxable DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN properties_taxable FLOAT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN properties_taxable FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "orders SET properties_total=0, properties_taxable=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN `disable_out_of_stock` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN disable_out_of_stock INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN [disable_out_of_stock] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN `is_active` INT(11) default '1' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN is_active INT4 default '1' ",
			"access" => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN [is_active] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "shipping_types SET is_active=1 ";

	}

	if (comp_vers("2.3", $current_db_version) == 1) {

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "newsletters ADD COLUMN orders_recipients TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "newsletters ADD COLUMN orders_recipients TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "newsletters ADD COLUMN orders_recipients LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "orders_serials_activations ADD COLUMN remote_address VARCHAR(32) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_serials_activations ADD COLUMN activation_key TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_serials_activations ADD COLUMN activation_key TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_serials_activations ADD COLUMN activation_key LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

	}


	if (comp_vers("2.4", $current_db_version) == 1) {

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_events ADD COLUMN order_items TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_events ADD COLUMN order_items TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_events ADD COLUMN order_items LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_events ADD COLUMN `status_id` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "orders_events ADD COLUMN status_id INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "orders_events ADD COLUMN [status_id] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN `is_dispatch` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN is_dispatch INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN [is_dispatch] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "credit_cards ADD COLUMN credit_card_code VARCHAR(32) ";
		$sqls[] = "UPDATE " . $table_prefix . "credit_cards SET credit_card_code='MC' WHERE credit_card_name='Mastercard' AND (credit_card_code='' OR credit_card_code IS NULL) ";
		$sqls[] = "UPDATE " . $table_prefix . "credit_cards SET credit_card_code='AMEX' WHERE credit_card_name='American Express' AND (credit_card_code='' OR credit_card_code IS NULL) ";
		$sqls[] = "UPDATE " . $table_prefix . "credit_cards SET credit_card_code='Visa' WHERE credit_card_name='VISA Electron' AND (credit_card_code='' OR credit_card_code IS NULL) ";
		$sqls[] = "UPDATE " . $table_prefix . "credit_cards SET credit_card_code=credit_card_name WHERE credit_card_code='' OR credit_card_code IS NULL ";

		$sqls[] = " UPDATE " . $table_prefix . "global_settings SET setting_type='printable' WHERE setting_type='invoice' ";


		$mysql_sql   = "CREATE TABLE " . $table_prefix . "shipping_modules (";
		$mysql_sql  .= "  `shipping_module_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `shipping_module_name` VARCHAR(255),";
		$mysql_sql  .= "  `module_notes` TEXT,";
		$mysql_sql  .= "  `is_external` INT(11) default '1',";
		$mysql_sql  .= "  `php_external_lib` VARCHAR(255),";
		$mysql_sql  .= "  `external_url` VARCHAR(255),";
		$mysql_sql  .= "  `is_active` INT(11) default '0'";
		$mysql_sql  .= "  ,PRIMARY KEY (shipping_module_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "shipping_modules START 2";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "shipping_modules (";
		$postgre_sql .= "  shipping_module_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "shipping_modules'),";
		$postgre_sql .= "  shipping_module_name VARCHAR(255),";
		$postgre_sql .= "  module_notes TEXT,";
		$postgre_sql .= "  is_external INT4 default '1',";
		$postgre_sql .= "  php_external_lib VARCHAR(255),";
		$postgre_sql .= "  external_url VARCHAR(255),";
		$postgre_sql .= "  is_active INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (shipping_module_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "shipping_modules (";
		$access_sql .= "  [shipping_module_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [shipping_module_name] VARCHAR(255),";
		$access_sql .= "  [module_notes] LONGTEXT,";
		$access_sql .= "  [is_external] INTEGER,";
		$access_sql .= "  [php_external_lib] VARCHAR(255),";
		$access_sql .= "  [external_url] VARCHAR(255),";
		$access_sql .= "  [is_active] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (shipping_module_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "shipping_modules_parameters (";
		$mysql_sql  .= "  `parameter_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `shipping_module_id` INT(11) default '0',";
		$mysql_sql  .= "  `parameter_name` VARCHAR(255),";
		$mysql_sql  .= "  `parameter_source` TEXT,";
		$mysql_sql  .= "  `not_passed` INT(11) default '0'";
		$mysql_sql  .= "  ,PRIMARY KEY (parameter_id)";
		$mysql_sql  .= "  ,KEY shipping_module_id (shipping_module_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "shipping_modules_parameters START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "shipping_modules_parameters (";
		$postgre_sql .= "  parameter_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "shipping_modules_parameters'),";
		$postgre_sql .= "  shipping_module_id INT4 default '0',";
		$postgre_sql .= "  parameter_name VARCHAR(255),";
		$postgre_sql .= "  parameter_source TEXT,";
		$postgre_sql .= "  not_passed INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (parameter_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "shipping_modules_parameters (";
		$access_sql .= "  [parameter_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [shipping_module_id] INTEGER,";
		$access_sql .= "  [parameter_name] VARCHAR(255),";
		$access_sql .= "  [parameter_source] LONGTEXT,";
		$access_sql .= "  [not_passed] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (parameter_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "shipping_modules_paramet_25 ON " . $table_prefix . "shipping_modules_parameters (shipping_module_id)";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN `shipping_module_id` INT(11) default '0' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN shipping_module_id INT4 default '0' ",
			"access" => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN [shipping_module_id] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql  = " INSERT INTO " . $table_prefix . "shipping_modules (shipping_module_id, shipping_module_name, is_external, is_active) ";
		$sql .= " VALUES (1, 'Custom Shipping', 0, 1) ";
		$sqls[] = $sql;

		$sqls[] = " UPDATE " . $table_prefix . "shipping_types SET shipping_module_id=1 ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN shipping_type_code VARCHAR(64) ";

	}


	if (comp_vers("2.5", $current_db_version) == 1) {

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN discount_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN discount_type INT4  ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN discount_type INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN discount_amount DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN discount_amount FLOAT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN discount_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN coupons_ids TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN coupons_ids TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN coupons_ids LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN discount_type INT(11)  ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN discount_type INT4  ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN discount_type INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN discount_amount DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN discount_amount FLOAT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN discount_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN coupons_ids TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN coupons_ids TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN coupons_ids LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN super_image VARCHAR(255) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "header_links ADD COLUMN menu_path VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "header_links ADD COLUMN parent_menu_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "header_links ADD COLUMN parent_menu_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "header_links ADD COLUMN parent_menu_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "UPDATE " . $table_prefix . "header_links SET parent_menu_id=menu_id ";
		$sqls[] = "UPDATE " . $table_prefix . "header_links SET menu_path='/' ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN small_image_alt VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN big_image_alt VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN meta_title VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN meta_description VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN meta_keywords VARCHAR(255) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "categories ADD COLUMN image_alt VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "categories ADD COLUMN image_large VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "categories ADD COLUMN image_large_alt VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "categories ADD COLUMN meta_title VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "categories ADD COLUMN meta_description VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "categories ADD COLUMN meta_keywords VARCHAR(255) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "articles ADD COLUMN image_small_alt VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "articles ADD COLUMN image_large_alt VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "articles ADD COLUMN meta_title VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "articles ADD COLUMN meta_description VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "articles ADD COLUMN meta_keywords VARCHAR(255) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN image_small_alt VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN image_large_alt VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN meta_title VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN meta_description VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN meta_keywords VARCHAR(255) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN cc_first_name VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN cc_last_name VARCHAR(64) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN is_gift_voucher INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN is_gift_voucher INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN is_gift_voucher INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
	}

	if (is_array($sqls) && sizeof($sqls) > 0) {
		run_queries($sqls, $queries_success, $queries_failed, $errors, "2.5");
	}

?>