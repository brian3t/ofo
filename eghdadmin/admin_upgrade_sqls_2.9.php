<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_upgrade_sqls_2.9.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	check_admin_security("system_upgrade");

	if (comp_vers("2.5.1", $current_db_version) == 1) {
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN reset_password_code VARCHAR(64) ";
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN reset_password_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN reset_password_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN reset_password_date DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN order_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN order_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN order_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN order_item_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN order_item_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN order_item_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN failure_action INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN failure_action INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN failure_action INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "items_prices (";
		$mysql_sql  .= "  `price_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `item_id` INT(11) default '0',";
		$mysql_sql  .= "  `is_active` INT(11) default '0',";
		$mysql_sql  .= "  `min_quantity` INT(11) default '1',";
		$mysql_sql  .= "  `max_quantity` INT(11) default '1',";
		$mysql_sql  .= "  `price` DOUBLE(16,2) default '0',";
		$mysql_sql  .= "  `discount_action` INT(11) default '0'";
		$mysql_sql  .= "  ,KEY item_id (item_id)";
		$mysql_sql  .= "  ,PRIMARY KEY (price_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "shipping_modules_parameters START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "items_prices (";
		$postgre_sql .= "  price_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "items_prices'),";
		$postgre_sql .= "  item_id INT4 default '0',";
		$postgre_sql .= "  is_active INT4 default '0',";
		$postgre_sql .= "  min_quantity INT4 default '1',";
		$postgre_sql .= "  max_quantity INT4 default '1',";
		$postgre_sql .= "  price FLOAT4 default '0',";
		$postgre_sql .= "  discount_action INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (price_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "items_prices (";
		$access_sql .= "  [price_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [item_id] INTEGER,";
		$access_sql .= "  [is_active] INTEGER,";
		$access_sql .= "  [min_quantity] INTEGER,";
		$access_sql .= "  [max_quantity] INTEGER,";
		$access_sql .= "  [price] FLOAT,";
		$access_sql .= "  [discount_action] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (price_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "items_prices_item_id ON " . $table_prefix . "items_prices (item_id)";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_list INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_list INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_list INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_details INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_details INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN hide_add_details INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "pages ADD COLUMN meta_title VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "pages ADD COLUMN meta_description VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "pages ADD COLUMN meta_keywords VARCHAR(255) ";
	}

	if (comp_vers("2.5.3", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_times ADD COLUMN availability_time INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_times ADD COLUMN availability_time INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_times ADD COLUMN availability_time INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN shipping_order INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN shipping_order INT4 default '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN shipping_order INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN shipping_time INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN shipping_time INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN shipping_time INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "UPDATE " . $table_prefix . "shipping_types SET shipping_order=1 ";


		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN processing_time INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN processing_time INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN processing_time INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN shipping_expecting_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN shipping_expecting_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN shipping_expecting_date DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN shipping_tracking_id VARCHAR(255) ";
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN shipping_expecting_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN shipping_expecting_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN shipping_expecting_date DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN is_rss INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN is_rss INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN is_rss INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN rss_limit INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN rss_limit INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN rss_limit INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN rss_on_breadcrumb INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN rss_on_breadcrumb INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN rss_on_breadcrumb INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN rss_on_list INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN rss_on_list INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN rss_on_list INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN admin_id_added_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN admin_id_added_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN admin_id_added_by INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN admin_id_modified_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN admin_id_modified_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN admin_id_modified_by INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN date_added DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN date_added TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN date_added DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN date_modified DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN date_modified TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN date_modified DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN admin_id_added_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN admin_id_added_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN admin_id_added_by INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN admin_id_modified_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN admin_id_modified_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN admin_id_modified_by INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN date_added DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN date_added TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN date_added DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN date_modified DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN date_modified TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN date_modified DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_priorities ADD COLUMN priority_rank INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_priorities ADD COLUMN priority_rank INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_priorities ADD COLUMN priority_rank INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_priorities ADD COLUMN is_default INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_priorities ADD COLUMN is_default INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_priorities ADD COLUMN is_default INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_priorities ADD COLUMN admin_html TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_priorities ADD COLUMN admin_html TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "support_priorities ADD COLUMN admin_html LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "UPDATE " . $table_prefix . "support_priorities SET priority_rank=2 ";
		$sqls[] = "UPDATE " . $table_prefix . "support SET support_priority_id=2 WHERE support_priority_id IS NULL ";
		$sqls[] = "UPDATE " . $table_prefix . "support_priorities SET is_default=0, priority_rank=1, admin_html='<img src=\"../images/high.gif\" width=\"8\" height=\"12\" border=\"0\">' WHERE priority_id=1 ";
		$sqls[] = "UPDATE " . $table_prefix . "support_priorities SET is_default=1, priority_rank=2 WHERE priority_id=2 ";
		$sqls[] = "UPDATE " . $table_prefix . "support_priorities SET is_default=0, priority_rank=3, admin_html='<img src=\"../images/low.gif\" width=\"8\" height=\"12\" border=\"0\">' WHERE priority_id=3 ";


		$mysql_sql   = "CREATE TABLE " . $table_prefix . "banners (";
		$mysql_sql  .= "  `banner_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `banner_rank` INT(11) default '1',";
		$mysql_sql  .= "  `banner_title` VARCHAR(255),";
		$mysql_sql  .= "  `show_title` INT(11) default '0',";
		$mysql_sql  .= "  `image_src` VARCHAR(255),";
		$mysql_sql  .= "  `image_alt` VARCHAR(255),";
		$mysql_sql  .= "  `html_text` TEXT,";
		$mysql_sql  .= "  `target_url` VARCHAR(255),";
		$mysql_sql  .= "  `is_new_window` INT(11) default '0',";
		$mysql_sql  .= "  `is_active` INT(11) default '0',";
		$mysql_sql  .= "  `show_on_ssl` INT(11) default '0',";
		$mysql_sql  .= "  `max_impressions` INT(11) default '0',";
		$mysql_sql  .= "  `max_clicks` INT(11) default '0',";
		$mysql_sql  .= "  `expiry_date` DATETIME,";
		$mysql_sql  .= "  `total_impressions` INT(11) default '0',";
		$mysql_sql  .= "  `total_clicks` INT(11) default '0'";
		$mysql_sql  .= "  ,KEY is_active (is_active)";
		$mysql_sql  .= "  ,KEY max_clicks (max_clicks)";
		$mysql_sql  .= "  ,KEY max_impressions (max_impressions)";
		$mysql_sql  .= "  ,PRIMARY KEY (banner_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "banners START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "banners (";
		$postgre_sql .= "  banner_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "banners'),";
		$postgre_sql .= "  banner_rank INT4 default '1',";
		$postgre_sql .= "  banner_title VARCHAR(255),";
		$postgre_sql .= "  show_title INT4 default '0',";
		$postgre_sql .= "  image_src VARCHAR(255),";
		$postgre_sql .= "  image_alt VARCHAR(255),";
		$postgre_sql .= "  html_text TEXT,";
		$postgre_sql .= "  target_url VARCHAR(255),";
		$postgre_sql .= "  is_new_window INT4 default '0',";
		$postgre_sql .= "  is_active INT4 default '0',";
		$postgre_sql .= "  show_on_ssl INT4 default '0',";
		$postgre_sql .= "  max_impressions INT4 default '0',";
		$postgre_sql .= "  max_clicks INT4 default '0',";
		$postgre_sql .= "  expiry_date TIMESTAMP,";
		$postgre_sql .= "  total_impressions INT4 default '0',";
		$postgre_sql .= "  total_clicks INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (banner_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "banners (";
		$access_sql .= "  [banner_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [banner_rank] INTEGER,";
		$access_sql .= "  [banner_title] VARCHAR(255),";
		$access_sql .= "  [show_title] INTEGER,";
		$access_sql .= "  [image_src] VARCHAR(255),";
		$access_sql .= "  [image_alt] VARCHAR(255),";
		$access_sql .= "  [html_text] LONGTEXT,";
		$access_sql .= "  [target_url] VARCHAR(255),";
		$access_sql .= "  [is_new_window] INTEGER,";
		$access_sql .= "  [is_active] INTEGER,";
		$access_sql .= "  [show_on_ssl] INTEGER,";
		$access_sql .= "  [max_impressions] INTEGER,";
		$access_sql .= "  [max_clicks] INTEGER,";
		$access_sql .= "  [expiry_date] DATETIME,";
		$access_sql .= "  [total_impressions] INTEGER,";
		$access_sql .= "  [total_clicks] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (banner_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "banners_is_active ON " . $table_prefix . "banners (is_active)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "banners_max_clicks ON " . $table_prefix . "banners (max_clicks)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "banners_max_impressions ON " . $table_prefix . "banners (max_impressions)";
		}


		$mysql_sql   = "CREATE TABLE " . $table_prefix . "banners_assigned (";
		$mysql_sql  .= "  `banner_id` INT(11) NOT NULL default '0',";
		$mysql_sql  .= "  `group_id` INT(11) NOT NULL default '0'";
		$mysql_sql  .= "  ,PRIMARY KEY (banner_id,group_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "banners_assigned START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "banners_assigned (";
		$postgre_sql .= "  banner_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  group_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (banner_id,group_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "banners_assigned (";
		$access_sql .= "  [banner_id] INTEGER NOT NULL,";
		$access_sql .= "  [group_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (banner_id,group_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];


		$mysql_sql   = "CREATE TABLE " . $table_prefix . "banners_groups (";
		$mysql_sql  .= "  `group_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `group_name` VARCHAR(128),";
		$mysql_sql  .= "  `group_desc` TEXT,";
		$mysql_sql  .= "  `is_active` INT(11) default '0'";
		$mysql_sql  .= "  ,KEY is_active (is_active)";
		$mysql_sql  .= "  ,PRIMARY KEY (group_id))";

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "banners_groups_is_active ON " . $table_prefix . "banners_groups (is_active)";
		}

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "banners_groups START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "banners_groups (";
		$postgre_sql .= "  group_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "banners_groups'),";
		$postgre_sql .= "  group_name VARCHAR(128),";
		$postgre_sql .= "  group_desc TEXT,";
		$postgre_sql .= "  is_active INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (group_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "banners_groups (";
		$access_sql .= "  [group_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [group_name] VARCHAR(128),";
		$access_sql .= "  [group_desc] LONGTEXT,";
		$access_sql .= "  [is_active] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (group_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "banners_clicks (";
		$mysql_sql  .= "  `click_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `banner_id` INT(11) default '0',";
		$mysql_sql  .= "  `user_id` INT(11) default '0',";
		$mysql_sql  .= "  `remote_address` VARCHAR(50),";
		$mysql_sql  .= "  `click_date` DATETIME NOT NULL";
		$mysql_sql  .= "  ,KEY banner_id (banner_id)";
		$mysql_sql  .= "  ,KEY click_date (click_date)";
		$mysql_sql  .= "  ,PRIMARY KEY (click_id)";
		$mysql_sql  .= "  ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "banners_clicks START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "banners_clicks (";
		$postgre_sql .= "  click_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "banners_clicks'),";
		$postgre_sql .= "  banner_id INT4 default '0',";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  remote_address VARCHAR(50),";
		$postgre_sql .= "  click_date TIMESTAMP NOT NULL";
		$postgre_sql .= "  ,PRIMARY KEY (click_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "banners_clicks (";
		$access_sql .= "  [click_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [banner_id] INTEGER,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [remote_address] VARCHAR(50),";
		$access_sql .= "  [click_date] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (click_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "banners_clicks_banner_id ON " . $table_prefix . "banners_clicks (banner_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "banners_clicks_click_date ON " . $table_prefix . "banners_clicks (click_date)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "banners_clicks_user_id ON " . $table_prefix . "banners_clicks (user_id)";
		}

		$mysql_sql   = "CREATE TABLE " . $table_prefix . "support_users_priorities (";
		$mysql_sql  .= "  `user_priority_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql  .= "  `priority_id` INT(11) default '0',";
		$mysql_sql  .= "  `user_id` INT(11) default '0',";
		$mysql_sql  .= "  `user_email` VARCHAR(255)";
		$mysql_sql  .= "  ,PRIMARY KEY (user_priority_id)";
		$mysql_sql  .= "  ,KEY priority_id (priority_id)";
		$mysql_sql  .= "  ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "support_users_priorities START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "support_users_priorities (";
		$postgre_sql .= "  user_priority_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "support_users_priorities'),";
		$postgre_sql .= "  priority_id INT4 default '0',";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  user_email VARCHAR(255)";
		$postgre_sql .= "  ,PRIMARY KEY (user_priority_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "support_users_priorities (";
		$access_sql .= "  [user_priority_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [priority_id] INTEGER,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [user_email] VARCHAR(255)";
		$access_sql .= "  ,PRIMARY KEY (user_priority_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "support_users_priorities_31 ON " . $table_prefix . "support_users_priorities (priority_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "support_users_priorities_32 ON " . $table_prefix . "support_users_priorities (user_id)";
		}

		$sqls[] = "ALTER TABLE " . $table_prefix . "categories ADD COLUMN list_template VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "categories ADD COLUMN details_template VARCHAR(128) ";

		// update home page layout
		$layout_id = get_setting_value($settings, "layout_id");
		if (!strlen($layout_id)) {
			$sql = " SELECT MIN(layout_id) FROM " . $table_prefix . "layouts ";
			$db->query($sql);
			if ($db->next_record()) {
				$layout_id = $db->f("layout_id");
			}
		}
		$sql = "SELECT * FROM " . $table_prefix . "page_settings WHERE page_name='index' AND layout_id=" . $db->tosql($layout_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$setting_name = $db->f("setting_name");
			$setting_order = $db->f("setting_order");
			$setting_value = $db->f("setting_value");
			$sql  = " INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'index', ";
			$sql .= $db->tosql($setting_name, TEXT, true, false) . ",";
			$sql .= $db->tosql($setting_order, INTEGER) . ",";
			$sql .= $db->tosql($setting_value, TEXT, true, false) . ")";
			$sqls[] = $sql;
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN buying_price DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN buying_price FLOAT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN buying_price FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN `use_stock_level` INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN use_stock_level INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN [use_stock_level] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "UPDATE " . $table_prefix . "items_properties_values SET hide_out_of_stock=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN `pending_status_id` INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN pending_status_id INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN [pending_status_id] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
	}

	if (comp_vers("2.5.4", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_prices ADD COLUMN user_type_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_prices ADD COLUMN user_type_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_prices ADD COLUMN user_type_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "items_prices SET user_type_id=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN item_type_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN item_type_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN item_type_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN incoming_type_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN incoming_type_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN incoming_type_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN incoming_product_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN incoming_product_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN incoming_product_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN file_path VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN attachments_dir VARCHAR(255) ";
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN attachments_mask TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN attachments_mask TEXT ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_departments ADD COLUMN attachments_mask LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "languages ADD COLUMN language_order INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "languages ADD COLUMN language_order INT4 default '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "languages ADD COLUMN language_order INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "languages SET language_order=1 ";

	}

	if (comp_vers("2.5.5", $current_db_version) == 1) {

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support ADD COLUMN admin_id_modified_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support ADD COLUMN admin_id_modified_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support ADD COLUMN admin_id_modified_by INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support ADD COLUMN date_viewed DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support ADD COLUMN date_viewed TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "support ADD COLUMN date_viewed DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN date_modified DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN date_modified TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN date_modified DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN admin_id_modified_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN admin_id_modified_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN admin_id_modified_by INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "custom_blocks ADD COLUMN block_name VARCHAR(255) ";
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "custom_blocks ADD COLUMN block_notes TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "custom_blocks ADD COLUMN block_notes TEXT ",
			"access"  => "ALTER TABLE " . $table_prefix . "custom_blocks ADD COLUMN block_notes LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];
		$sql = " SELECT * FROM " . $table_prefix . "custom_blocks ";
		$db->query($sql);
		while ($db->next_record()) {
			$block_id = $db->f("block_id");
			$block_name = strip_tags(get_translation($db->f("block_title")));
			if (!$block_name) {
				$block_name = trim(strip_tags(get_translation($db->f("block_desc"))));
			}
			$words = explode(" ", $block_name);
			if (sizeof($words) > 5) {
				$block_name = "";
				for ($i = 0; $i < 5; $i++) {
					$block_name .= $words[$i] . " ";
				}
			}
			$sqls[] = "UPDATE " . $table_prefix . "custom_blocks SET block_name=" . $db->tosql($block_name, TEXT) . " WHERE block_id=" . $db->tosql($block_id, INTEGER);
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN show_sub_products INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN show_sub_products INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN show_sub_products INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_prices ADD COLUMN properties_discount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_prices ADD COLUMN properties_discount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_prices ADD COLUMN properties_discount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN use_on_second INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN use_on_second INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN use_on_second INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items_properties SET use_on_second=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN `is_list` INT(11) default '1' ",
			"postgre"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN is_list INT4 default '1' ",
			"access" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN [is_list] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET is_list=1 ";


		// update menu links for new structure
		$header_links = array();
		$sql = "SELECT * FROM " . $table_prefix . "header_links ";
		$db->query($sql);
		while ($db->next_record()) {
			$menu_id = $db->f("menu_id");
			$parent_menu_id = $db->f("parent_menu_id");
			$header_links[$menu_id] = $parent_menu_id;
		}
		foreach ($header_links as $menu_id => $parent_menu_id) {
			if (!$parent_menu_id || $parent_menu_id == $menu_id) {
				$parent_menu_id = 0;
			}
			$menu_path = ""; $current_parent_id = $parent_menu_id;
			while ($current_parent_id) {
				$menu_path = $current_parent_id.",".$menu_path;
				$sql = "SELECT parent_menu_id FROM " . $table_prefix . "header_links WHERE menu_id=".$db->tosql($current_parent_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$parent_id = $db->f("parent_menu_id");
					if ($parent_id == $current_parent_id) {
						$current_parent_id = 0;
					} else {
						$current_parent_id = $parent_id;
					}
				} else {
					$current_parent_id = 0;
				}
			}
			$sql  = " UPDATE " . $table_prefix . "header_links SET ";
			$sql .= " parent_menu_id=" . $db->tosql($parent_menu_id, INTEGER) . ", ";
			$sql .= " menu_path=" . $db->tosql($menu_path, TEXT);
			$sql .= " WHERE menu_id=" . $db->tosql($menu_id, INTEGER);
			$sqls[] = $sql;
		}


		//update for forum

		//create table forum_list
	  $mysql_sql = "CREATE TABLE `" . $table_prefix . "forum_list` (";
	  $mysql_sql .= "`forum_id` int(11) NOT NULL auto_increment,";
	  $mysql_sql .= "`category_id` int(11) NOT NULL,";
	  $mysql_sql .= "`forum_name` varchar(255) NOT NULL,";
	  $mysql_sql .= "`forum_order` int(11) default '0',";
	  $mysql_sql .= "`short_description` text,";
	  $mysql_sql .= "`full_description` text,";
	  $mysql_sql .= "`small_image` varchar(255),";
	  $mysql_sql .= "`large_image` varchar(255),";
	  $mysql_sql .= "`date_added` datetime NOT NULL default '0000-00-00 00:00:00',";
	  $mysql_sql .= "`threads_number` int(11) NOT NULL default '0',";
	  $mysql_sql .= "`messages_number` int(11) NOT NULL default '0',";
	  $mysql_sql .= "`last_post_added` datetime,";
	  $mysql_sql .= "`last_post_user_id` int(11) default '0',";
	  $mysql_sql .= "`last_post_thread_id` int(11) default '0',";
	  $mysql_sql .= "`allowed_view` int(11) default '0',";
	  $mysql_sql .= "PRIMARY KEY (`forum_id`),";
	  $mysql_sql .= "KEY `category_id` (`category_id`))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "forum_list START 1";
		}
	  $postgre_sql = "CREATE TABLE `" . $table_prefix . "forum_list` (";
	  $postgre_sql .= "`forum_id` INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "forum_list'),";
	  $postgre_sql .= "`category_id` INT4 NOT NULL,";
	  $postgre_sql .= "`forum_name` VARCHAR(255) NOT NULL,";
	  $postgre_sql .= "`forum_order` INT4 default '0',";
	  $postgre_sql .= "`short_description` TEXT,";
	  $postgre_sql .= "`full_description` TEXT,";
	  $postgre_sql .= "`small_image` VARCHAR(255),";
	  $postgre_sql .= "`large_image` VARCHAR(255),";
	  $postgre_sql .= "`date_added` TIMESTAMP NOT NULL,";
	  $postgre_sql .= "`threads_number` INT4 NOT NULL default '0',";
	  $postgre_sql .= "`messages_number` INT4 NOT NULL default '0',";
	  $postgre_sql .= "`last_post_added` TIMESTAMP ,";
	  $postgre_sql .= "`last_post_user_id` INT4 default '0',";
	  $postgre_sql .= "`last_post_thread_id` INT4 default '0',";
	  $postgre_sql .= "`allowed_view` INT4 default '0',";
	  $postgre_sql .= "PRIMARY KEY (`forum_id`))";

	  $access_sql = "CREATE TABLE " . $table_prefix . "forum_list (";
	  $access_sql .= "[forum_id] COUNTER NOT NULL,";
	  $access_sql .= "[category_id] INTEGER NOT NULL,";
	  $access_sql .= "[forum_name] VARCHAR(255) NOT NULL,";
	  $access_sql .= "[forum_order] INTEGER NOT NULL,";
	  $access_sql .= "[short_description] LONGTEXT,";
	  $access_sql .= "[full_description] LONGTEXT,";
	  $access_sql .= "[small_image] VARCHAR(255),";
	  $access_sql .= "[large_image] VARCHAR(255),";
	  $access_sql .= "[date_added] DATETIME NOT NULL,";
	  $access_sql .= "[threads_number] INTEGER NOT NULL,";
	  $access_sql .= "[messages_number] INTEGER NOT NULL,";
	  $access_sql .= "[last_post_added] DATETIME ,";
	  $access_sql .= "[last_post_user_id] INTEGER,";
	  $access_sql .= "[last_post_thread_id] INTEGER,";
	  $access_sql .= "[allowed_view] INTEGER NOT NULL,";
	  $access_sql .= "PRIMARY KEY (forum_id))";

	  $sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
	  $sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "forum_list_category_id ON " . $table_prefix . "forum_list (category_id)";
		}

		//create table forum_categories
	  $mysql_sql = "CREATE TABLE `" . $table_prefix . "forum_categories` (";
	  $mysql_sql .= "`category_id` int(11) NOT NULL auto_increment,";
	  $mysql_sql .= "`category_name` varchar(255) NOT NULL,";
	  $mysql_sql .= "`category_order` int(11) default '0',";
	  $mysql_sql .= "`short_description` text,";
	  $mysql_sql .= "`full_description` text,";
	  $mysql_sql .= "`allowed_view` int(11) default '0',";
	  $mysql_sql .= "PRIMARY KEY (`category_id`))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "forum_categories START 1";
		}
	  $postgre_sql = "CREATE TABLE `" . $table_prefix . "forum_categories` (";
	  $postgre_sql .= "`category_id` INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "forum_categories'),";
	  $postgre_sql .= "`category_name` VARCHAR(255) NOT NULL,";
	  $postgre_sql .= "`category_order` INT4 default '0',";
	  $postgre_sql .= "`short_description` TEXT,";
	  $postgre_sql .= "`full_description` TEXT,";
	  $postgre_sql .= "`allowed_view` INT4 default '0',";
	  $postgre_sql .= "PRIMARY KEY (`category_id`))";

	  $access_sql  = "CREATE TABLE `" . $table_prefix . "forum_categories` (";
	  $access_sql .= "[category_id] COUNTER  NOT NULL,";
	  $access_sql .= "[category_name] VARCHAR(255) NOT NULL,";
	  $access_sql .= "[category_order] INTEGER NOT NULL,";
	  $access_sql .= "[short_description] LONGTEXT,";
	  $access_sql .= "[full_description] LONGTEXT,";
	  $access_sql .= "[allowed_view] INTEGER NOT NULL,";
	  $access_sql .= "PRIMARY KEY (category_id))";

	  $sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
	  $sqls[] = $sql_types[$db_type];

		//create table forum_moderators
	  $mysql_sql  = "CREATE TABLE " . $table_prefix . "forum_moderators (";
	  $mysql_sql .= " `admin_id` INT(11) NOT NULL default '0',";
	  $mysql_sql .= " `forum_id` INT(11) NOT NULL default '0',";
	  $mysql_sql .= " `is_default_forum` INT(11) default '0'";
	  $mysql_sql .= " ,PRIMARY KEY (admin_id,forum_id))";

	  $postgre_sql  = "CREATE TABLE " . $table_prefix . "forum_moderators (";
	  $postgre_sql .= "  admin_id INT4 NOT NULL default '0',";
	  $postgre_sql .= "  forum_id INT4 NOT NULL default '0',";
	  $postgre_sql .= "  is_default_forum INT4 default '0'";
	  $postgre_sql .= "  ,PRIMARY KEY (admin_id,forum_id))";

	  $access_sql  = "CREATE TABLE " . $table_prefix . "forum_moderators (";
	  $access_sql .= "  [admin_id] INTEGER NOT NULL,";
	  $access_sql .= "  [forum_id] INTEGER NOT NULL,";
	  $access_sql .= "  [is_default_forum] INTEGER";
	  $access_sql .= "  ,PRIMARY KEY (admin_id,forum_id))";

	  $sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
	  $sqls[] = $sql_types[$db_type];

		//add field forum_id
	  $sql_types = array(
	  	"mysql"   => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN forum_id INT(11) NOT NULL",
	  	"postgre" => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN forum_id INT4 NOT NULL",
	  	"access"  => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN forum_id INTEGER "
	  );
	  $sqls[] = $sql_types[$db_type];

		//add field user_id
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_messages ADD COLUMN user_id INT(11) NULL ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_messages ADD COLUMN user_id INT4 NULL",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_messages ADD COLUMN user_id INTEGER "
		);
	  $sqls[] = $sql_types[$db_type];

		//make content for forum
		$sqls[] = "INSERT INTO " . $table_prefix . "forum_categories (category_name, category_order, short_description, full_description, allowed_view) VALUES ('General', 1, 'General category', 'General category', 1)";

		$date_added = va_time();
		$sql = "SELECT count(*) FROM " . $table_prefix . "forum";
		$threads_number = get_db_value($sql);
		$sql = "SELECT count(*) FROM " . $table_prefix . "forum_messages";
		$messages_number = get_db_value($sql);
		$sql = " SELECT thread_id, date_added, user_id FROM " . $table_prefix . "forum ORDER BY date_added DESC ";
		$db->RecordsPerPage = 1;
		$db->PageNumber = 1;
		if ($db->query($sql)) {
			if ($db->next_record()) {
				$last_post_added = $db->f("date_added");
				$last_post_user_id = $db->f("user_id");
				$last_post_thread_id = $db->f("thread_id");
			} else {
				$last_post_added = va_date();
				$last_post_user_id = 0;
				$last_post_thread_id = 0;
			}
		}

		$sql  = " INSERT INTO " . $table_prefix . "forum_list (category_id, forum_name, forum_order, date_added, threads_number, messages_number, last_post_added, last_post_user_id, last_post_thread_id, allowed_view) VALUES ";
		$sql .= " (1, 'Forum', 1, " . $db->tosql($date_added, DATETIME)  . ", " . $threads_number . ", " . $messages_number . ", " . $db->tosql($last_post_added, DATETIME) . ", " . $last_post_user_id . ", " . $last_post_thread_id . ", 1)";
		$sqls[] = $sql;

		$sqls[] = "UPDATE " . $table_prefix . "forum SET forum_id = 1";


		// create forum_topic list layout
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topic', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topic', 'left_column_width', NULL, '')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topic', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topic', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topic', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topic', 'right_column_width', NULL, '')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topic', 'forum_view_topic', 0, 'middle')";

		// create forum_list list layout
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_list', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_list', 'left_column_width', NULL, '')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_list', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_list', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_list', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_list', 'right_column_width', NULL, '')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_list', 'forum_list', 0, 'middle')";

		// create forum_topics list layout
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topics', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topics', 'left_column_width', NULL, '')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topics', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topics', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topics', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topics', 'right_column_width', NULL, '')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'forum_topics', 'forum_topics_block', 0, 'middle')";

		$sqls[] = " UPDATE " . $table_prefix . "header_links SET menu_url='forums.php' WHERE menu_url='forum.php'";
		//end update for forum

		//begin changes related to Knowledge Base settings
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN is_add_knowledge INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN is_add_knowledge INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN is_add_knowledge INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "INSERT INTO " . $table_prefix . "support_statuses (status_name, status_caption, is_internal, is_add_knowledge) VALUES ('Added to Knowledge Base', 'Add to KB', 1, 1)";
		$sql  = " INSERT INTO " . $table_prefix . "articles_categories (category_path, category_name, category_order, allowed_view, allowed_rate, article_list_fields, article_details_fields, article_required_fields) VALUES ";
		$sql .= " ('0,', 'Knowledge Base', 1, 1, 1, 'article_date,article_title,short_description', 'article_date,article_title,full_description', 'article_date,article_title,full_description')";
		$sqls[] = $sql;

		$sql = "SELECT max(category_id) FROM " . $table_prefix . "articles_categories";
		$category_id = get_db_value($sql) + 1;
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('support', 'knowledge_category', '$category_id')";

		$sql = "SELECT min(status_id) FROM " . $table_prefix . "articles_statuses";
		$article_status_id = get_db_value($sql);
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('support', 'knowledge_article_status', '$article_status_id')";
		//end changes related to Knowledge Base settings
	}


	if (comp_vers("2.6", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_price_edit INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_price_edit INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_price_edit INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN issue_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN issue_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN issue_date DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN properties_price DOUBLE(16,2) default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN properties_price FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN properties_price FLOAT"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items SET properties_price=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN processing_fee DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN processing_fee FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN processing_fee FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fee_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fee_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fee_type INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN processing_fee DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN processing_fee FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN processing_fee FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " ALTER TABLE ". $table_prefix . "black_ips DROP COLUMN adress_action ";
		$sqls[] = " ALTER TABLE ". $table_prefix . "black_ips DROP COLUMN adress_notes ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "black_ips ADD COLUMN address_action INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "black_ips ADD COLUMN address_action INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "black_ips ADD COLUMN address_action INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "black_ips ADD COLUMN address_notes TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "black_ips ADD COLUMN address_notes TEXT ",
			"access"  => "ALTER TABLE " . $table_prefix . "black_ips ADD COLUMN address_notes LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "black_ips SET address_action=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN property_type_id INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN property_type_id INT4 default '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN property_type_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items_properties SET property_type_id=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN sub_item_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN sub_item_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN sub_item_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN additional_price DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN additional_price FLOAT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN additional_price FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN sub_item_id INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN sub_item_id INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN sub_item_id INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE ".$table_prefix."banned_contents (";
		$mysql_sql .= "  `content_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `content_text` VARCHAR(255) NOT NULL";
		$mysql_sql .= "  ,PRIMARY KEY (content_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "banned_contents START 1";
		}
		$postgre_sql  = "CREATE TABLE ".$table_prefix."banned_contents (";
		$postgre_sql .= "  content_id INT4 NOT NULL DEFAULT nextval('seq_".$table_prefix."banned_contents'),";
		$postgre_sql .= "  content_text VARCHAR(255) NOT NULL";
		$postgre_sql .= "  ,PRIMARY KEY (content_id))";

		$access_sql  = "CREATE TABLE ".$table_prefix."banned_contents (";
		$access_sql .= "  [content_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [content_text] VARCHAR(255)";
		$access_sql .= "  ,PRIMARY KEY (content_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN is_bundle INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN is_bundle INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN is_bundle INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "item_types SET is_bundle=0 ";
		$sqls[] = " INSERT INTO " . $table_prefix . "item_types (item_type_name, is_gift_voucher, is_bundle) VALUES ('Bundle', 0, 1) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN parent_item_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN parent_item_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN parent_item_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN sms_notify INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN sms_notify INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN sms_notify INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN sms_recipient VARCHAR(255) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN sms_recipient VARCHAR(255) ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN sms_recipient VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN sms_originator VARCHAR(64) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN sms_originator VARCHAR(64) ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN sms_originator VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN sms_message TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN sms_message TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN sms_message LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "page_settings SET setting_name='prod_offers_recs' WHERE page_name='index' AND setting_name='products_per_page'";
		$sqls[] = " UPDATE " . $table_prefix . "page_settings SET setting_name='prod_offers_cols' WHERE page_name='index' AND setting_name='products_columns'";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN edited_item_fields TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN edited_item_fields TEXT ",
			"access"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN edited_item_fields LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_modules ADD COLUMN tracking_url VARCHAR(255) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_modules ADD COLUMN tracking_url VARCHAR(255) ",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_modules ADD COLUMN tracking_url VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "custom_blocks ADD COLUMN block_path VARCHAR(255) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "custom_blocks ADD COLUMN block_path VARCHAR(255) ",
			"access"  => "ALTER TABLE " . $table_prefix . "custom_blocks ADD COLUMN block_path VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN preview_url VARCHAR(255) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN preview_url VARCHAR(255) ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN preview_url VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN preview_width INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN preview_width INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN preview_width INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN preview_height INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN preview_height INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN preview_height INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items_properties ADD COLUMN property_values_ids TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items_properties ADD COLUMN property_values_ids TEXT ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items_properties ADD COLUMN property_values_ids LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN currency_image VARCHAR(255) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN currency_image VARCHAR(255) ",
			"access"  => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN currency_image VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];
	}

	if (comp_vers("2.7", $current_db_version) == 1)
  {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN is_generate_small_image INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN is_generate_small_image INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN is_generate_small_image INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN is_generate_big_image INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN is_generate_big_image INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN is_generate_big_image INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='products' ";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'resize_small_image', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'resize_big_image', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'resize_super_image', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'small_image_max_width', '100')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'small_image_max_height', '100')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'big_image_max_width', '300')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'big_image_max_height', '300')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'super_image_max_width', '800')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'super_image_max_height', '600')";

		$sqls[] = "ALTER TABLE " . $table_prefix . "countries ADD COLUMN country_iso_number VARCHAR(4) ";

		$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='global' ";
		$sc_sql = " AND setting_name IN ('product_quantity', 'quantity_control', 'confirm_add', 'redirect_to_cart', 'coupons_enable', 'user_registration', 'display_products', 'default_tax', 'default_tax_note', 'logout_cart_clear', 'reviews_availability') ";
		$db->query($sql . $sc_sql);
		while ($db->next_record()) {
			$setting_name = $db->f("setting_name");
			$setting_value = $db->f("setting_value");
			if ($setting_name == "default_tax_note") {
				$setting_name = "tax_note";
			} elseif ($setting_name == "default_tax") {
				$setting_name = "tax_prices";
				if ($setting_value) {
					$setting_value = 1;
				}
			}

			$sql  = " INSERT INTO " . $table_prefix . "global_settings (setting_type,setting_name,setting_value) VALUES (";
			$sql .= $db->tosql("products", TEXT) . ",";
			$sql .= $db->tosql($setting_name, TEXT) . ", ";
			$sql .= $db->tosql($setting_value, TEXT) . ") ";
			$sqls[] = $sql;
		}

		$sqls[] = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='global' " . $sc_sql;

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "tax_rates_items (";
		$mysql_sql .= "  `tax_item_id` INT(11) NOT NULL auto_increment,";
		$mysql_sql .= "  `tax_id` INT(11) default '0',";
		$mysql_sql .= "  `item_type_id` INT(11) default '0',";
		$mysql_sql .= "  `tax_percent` DOUBLE(16,3)";
		$mysql_sql .= "  ,KEY item_type_id (item_type_id)";
		$mysql_sql .= "  ,PRIMARY KEY (tax_item_id)";
		$mysql_sql .= "  ,KEY tax_id (tax_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "tax_rates_items START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "tax_rates_items (";
		$postgre_sql .= "  tax_item_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "tax_rates_items'),";
		$postgre_sql .= "  tax_id INT4 default '0',";
		$postgre_sql .= "  item_type_id INT4 default '0',";
		$postgre_sql .= "  tax_percent FLOAT4";
		$postgre_sql .= "  ,PRIMARY KEY (tax_item_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "tax_rates_items (";
		$access_sql .= "  [tax_item_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [tax_id] INTEGER,";
		$access_sql .= "  [item_type_id] INTEGER,";
		$access_sql .= "  [tax_percent] FLOAT";
		$access_sql .= "  ,PRIMARY KEY (tax_item_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "tax_rates_items_item_type_id ON " . $table_prefix . "tax_rates_items (item_type_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tax_rates_items_tax_id ON " . $table_prefix . "tax_rates_items (tax_id)";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN tax_percent DOUBLE(16,3) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN tax_percent FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN tax_percent FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_discount_tax DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_discount_tax FLOAT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_discount_tax FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN initial_ip VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN cookie_ip VARCHAR(32) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN discount_tax_free INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN discount_tax_free INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN discount_tax_free INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN registration_ip VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN modified_ip VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN last_visit_ip VARCHAR(32) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN item_notify INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN item_notify INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN item_notify INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN `mail_notify` INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN mail_notify INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN [mail_notify] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN mail_to VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN mail_from VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN mail_cc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN mail_bcc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN mail_reply_to VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN mail_return_path VARCHAR(64) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN `mail_type` INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN mail_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN [mail_type] INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN mail_subject VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN mail_body TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN mail_body TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN mail_body LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sms_notify INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sms_notify INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sms_notify INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN sms_recipient VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN sms_originator VARCHAR(64) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sms_message TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sms_message TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN sms_message LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];


		$mysql_sql  = "CREATE TABLE " . $table_prefix . "items_serials (";
		$mysql_sql .= "  `serial_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `item_id` INT(11) default '0',";
		$mysql_sql .= "  `serial_number` VARCHAR(128),";
		$mysql_sql .= "  `used` INT(11) default '0'";
		$mysql_sql .= "  ,KEY item_id (item_id)";
		$mysql_sql .= "  ,PRIMARY KEY (serial_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "items_serials START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "items_serials (";
		$postgre_sql .= "  serial_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "items_serials'),";
		$postgre_sql .= "  item_id INT4 default '0',";
		$postgre_sql .= "  serial_number VARCHAR(128),";
		$postgre_sql .= "  used INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (serial_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "items_serials (";
		$access_sql .= "  [serial_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [item_id] INTEGER,";
		$access_sql .= "  [serial_number] VARCHAR(128),";
		$access_sql .= "  [used] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (serial_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "items_serials_item_id ON " . $table_prefix . "items_serials (item_id)";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_view_topics INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_view_topics INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_view_topics INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_view_topic INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_view_topic INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_view_topic INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_post_topics INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_post_topics INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_post_topics INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_post_replies INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_post_replies INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN allowed_post_replies INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET allowed_view_topics=1 ";
		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET allowed_view_topic=1 ";
		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET allowed_post_topics=1 ";
		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET allowed_post_replies=1 ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "admins ADD COLUMN personal_image VARCHAR(255) ";

		// update categories settings
		$sql  = " SELECT * FROM " . $table_prefix . "page_settings ";
		$sql .= " WHERE (setting_name='categories_type' OR setting_name='ads_categories_type' OR setting_name LIKE 'a_cats_type_%') ";
		$sql .= " AND (setting_value='1' OR setting_value='2') ";
		$db->query($sql);
		while ($db->next_record()) {
			$page_name = $db->f("page_name");
			$setting_name = $db->f("setting_name");
			if ($setting_name == "categories_type") {
				$sql  = " UPDATE " . $table_prefix . "page_settings SET setting_name='subcategories_type' ";
				$sql .= " WHERE page_name=" . $db->tosql($page_name, TEXT);
				$sql .= " AND setting_name='categories_type'";
				$sqls[] = $sql;
				$sql  = " UPDATE " . $table_prefix . "page_settings SET setting_name='subcategories_columns' ";
				$sql .= " WHERE page_name=" . $db->tosql($page_name, TEXT);
				$sql .= " AND setting_name='categories_columns'";
				$sqls[] = $sql;
				$sql  = " UPDATE " . $table_prefix . "page_settings SET setting_name='subcategories_subs' ";
				$sql .= " WHERE page_name=" . $db->tosql($page_name, TEXT);
				$sql .= " AND setting_name='categories_subs'";
				$sqls[] = $sql;
			} elseif ($setting_name == "ads_categories_type") {
				$sql  = " UPDATE " . $table_prefix . "page_settings SET setting_name='ads_subcategories_type' ";
				$sql .= " WHERE page_name=" . $db->tosql($page_name, TEXT);
				$sql .= " AND setting_name='ads_categories_type'";
				$sqls[] = $sql;
				$sql  = " UPDATE " . $table_prefix . "page_settings SET setting_name='ads_subcategories_columns' ";
				$sql .= " WHERE page_name=" . $db->tosql($page_name, TEXT);
				$sql .= " AND setting_name='ads_categories_columns'";
				$sqls[] = $sql;
				$sql  = " UPDATE " . $table_prefix . "page_settings SET setting_name='ads_subcategories_subs' ";
				$sql .= " WHERE page_name=" . $db->tosql($page_name, TEXT);
				$sql .= " AND setting_name='ads_categories_subs'";
				$sqls[] = $sql;
			} elseif (preg_match("/^a_cats_type_(\d+)$/", $setting_name, $matches)) {
				$top_id = $matches[1];
				$sql  = " UPDATE " . $table_prefix . "page_settings ";
				$sql .= " SET setting_name=" . $db->tosql("a_subcats_type_" . $top_id, TEXT);
				$sql .= " WHERE page_name=" . $db->tosql($page_name, TEXT);
				$sql .= " AND setting_name=" . $db->tosql("a_cats_type_" . $top_id, TEXT);
				$sqls[] = $sql;
				$sql  = " UPDATE " . $table_prefix . "page_settings ";
				$sql .= " SET setting_name=" . $db->tosql("a_subcats_cols_" . $top_id, TEXT);
				$sql .= " WHERE page_name=" . $db->tosql($page_name, TEXT);
				$sql .= " AND setting_name=" . $db->tosql("a_cats_cols_" . $top_id, TEXT);
				$sqls[] = $sql;
				$sql  = " UPDATE " . $table_prefix . "page_settings ";
				$sql .= " SET setting_name=" . $db->tosql("a_subcats_subs_" . $top_id, TEXT);
				$sql .= " WHERE page_name=" . $db->tosql($page_name, TEXT);
				$sql .= " AND setting_name=" . $db->tosql("a_cats_subs_" . $top_id, TEXT);
				$sqls[] = $sql;
			}
		}

	}

	if (comp_vers("2.7.1", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN message_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN message_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN message_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN admin_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN admin_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN admin_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN attachment_status INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN attachment_status INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN attachment_status INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "support_attachments SET attachment_status=1, message_id=0, admin_id=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN admin_id_added_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN admin_id_added_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN admin_id_added_by INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN admin_id_modified_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN admin_id_modified_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN admin_id_modified_by INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "mysql") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders MODIFY COLUMN state_code VARCHAR(8) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders MODIFY COLUMN delivery_state_code VARCHAR(8) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users MODIFY COLUMN state_code VARCHAR(8) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users MODIFY COLUMN delivery_state_code VARCHAR(8) ";
		} elseif ($db_type == "access") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders ALTER COLUMN state_code VARCHAR(8) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders ALTER COLUMN delivery_state_code VARCHAR(8) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users ALTER COLUMN state_code VARCHAR(8) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users ALTER COLUMN delivery_state_code VARCHAR(8) ";
		}

	}

	if (comp_vers("2.8", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_price DOUBLE(16,2) NOT NULL default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_price FLOAT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_price FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_sales DOUBLE(16,2) NOT NULL default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_sales FLOAT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_sales FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "UPDATE " . $table_prefix . "items SET trade_price=0, trade_sales=0 ";

		$sqls[] = "CREATE INDEX " . $table_prefix . "items_trade_price ON " . $table_prefix . "items (trade_price) ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "items_trade_sales ON " . $table_prefix . "items (trade_sales) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN price_type INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN price_type INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN price_type INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "UPDATE " . $table_prefix . "user_types SET price_type=0 ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "categories ADD COLUMN friendly_url VARCHAR(255) NOT NULL ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN friendly_url VARCHAR(255) NOT NULL ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN friendly_url VARCHAR(255) NOT NULL ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN friendly_url VARCHAR(255) NOT NULL ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "articles ADD COLUMN friendly_url VARCHAR(255) NOT NULL ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "forum_categories ADD COLUMN friendly_url VARCHAR(255) NOT NULL ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN friendly_url VARCHAR(255) NOT NULL ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "forum ADD COLUMN friendly_url VARCHAR(255) NOT NULL ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN friendly_url VARCHAR(255) NOT NULL ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN friendly_url VARCHAR(255) NOT NULL ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN friendly_url VARCHAR(255) NOT NULL ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "pages ADD COLUMN friendly_url VARCHAR(255) NOT NULL ";

		$sqls[] = "CREATE INDEX " . $table_prefix . "categories_friendly_url ON " . $table_prefix . "categories (friendly_url) ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "items_friendly_url ON " . $table_prefix . "items (friendly_url) ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "manufacturers_friendly_url ON " . $table_prefix . "manufacturers (friendly_url) ";

		$sqls[] = "CREATE INDEX " . $table_prefix . "articles_categories_friendly_url ON " . $table_prefix . "articles_categories (friendly_url) ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "articles_friendly_url ON " . $table_prefix . "articles (friendly_url) ";

		$sqls[] = "CREATE INDEX " . $table_prefix . "forum_categories_friendly_url ON " . $table_prefix . "forum_categories (friendly_url) ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "forum_list_friendly_url ON " . $table_prefix . "forum_list (friendly_url) ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "forum_friendly_url ON " . $table_prefix . "forum (friendly_url) ";

		$sqls[] = "CREATE INDEX " . $table_prefix . "ads_categories_friendly_url ON " . $table_prefix . "ads_categories (friendly_url) ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "ads_items_friendly_url ON " . $table_prefix . "ads_items (friendly_url) ";

		$sqls[] = "CREATE INDEX " . $table_prefix . "users_friendly_url ON " . $table_prefix . "users (friendly_url) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN short_description TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN short_description TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN short_description LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN full_description TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN full_description TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "manufacturers ADD COLUMN full_description LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fee_min_goods DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fee_min_goods FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fee_min_goods FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fee_max_goods DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fee_max_goods FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fee_max_goods FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		// affiliate and merchants commissions fields
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN affiliate_code VARCHAR(64) NOT NULL ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "users_affiliate_code ON " . $table_prefix . "users (affiliate_code) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN short_description TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN short_description TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN short_description LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN full_description TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN full_description TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN full_description LONGTEXT"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN paypal_account VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN tax_id VARCHAR(128) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN user_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN user_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN user_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "items SET user_id=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN item_user_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN item_user_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN item_user_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "orders_items SET item_user_id=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN commission_action INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN commission_action INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN commission_action INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN affiliate_commission DOUBLE(16,2) NOT NULL default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN affiliate_commission FLOAT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN affiliate_commission FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN merchant_commission DOUBLE(16,2) NOT NULL default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN merchant_commission FLOAT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN merchant_commission FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "UPDATE " . $table_prefix . "orders_items SET affiliate_commission=0, merchant_commission=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN affiliate_commission_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN affiliate_commission_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN affiliate_commission_type INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN affiliate_commission_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN affiliate_commission_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN affiliate_commission_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN merchant_fee_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN merchant_fee_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN merchant_fee_type INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN merchant_fee_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN merchant_fee_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN merchant_fee_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN affiliate_commission_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN affiliate_commission_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN affiliate_commission_type INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN affiliate_commission_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN affiliate_commission_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN affiliate_commission_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN merchant_fee_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN merchant_fee_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN merchant_fee_type INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN merchant_fee_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN merchant_fee_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN merchant_fee_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];


		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN affiliate_commission_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN affiliate_commission_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN affiliate_commission_type INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN affiliate_commission_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN affiliate_commission_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN affiliate_commission_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN merchant_fee_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN merchant_fee_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN merchant_fee_type INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN merchant_fee_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN merchant_fee_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN merchant_fee_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN affiliate_commission_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN affiliate_commission_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN affiliate_commission_type INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN affiliate_commission_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN affiliate_commission_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN affiliate_commission_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN merchant_fee_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN merchant_fee_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN merchant_fee_type INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN merchant_fee_amount DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN merchant_fee_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN merchant_fee_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "users_commissions (";
		$mysql_sql .= "  `commission_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `payment_id` INT(11) default '0',";
		$mysql_sql .= "  `user_id` INT(11) default '0',";
		$mysql_sql .= "  `order_id` INT(11) default '0',";
		$mysql_sql .= "  `order_item_id` INT(11) default '0',";
		$mysql_sql .= "  `commission_amount` DOUBLE(16,2) default '0',";
		$mysql_sql .= "  `commission_action` INT(11) default '0',";
		$mysql_sql .= "  `commission_type` INT(11) default '0',";
		$mysql_sql .= "  `date_added` DATETIME NOT NULL";
		$mysql_sql .= "  ,KEY date_added (date_added)";
		$mysql_sql .= "  ,KEY order_id (order_id)";
		$mysql_sql .= "  ,KEY order_item_id (order_item_id)";
		$mysql_sql .= "  ,KEY payment_id (payment_id)";
		$mysql_sql .= "  ,PRIMARY KEY (commission_id)";
		$mysql_sql .= "  ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "users_commissions START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "users_commissions (";
		$postgre_sql .= "  commission_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "users_commissions'),";
		$postgre_sql .= "  payment_id INT4 default '0',";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  order_id INT4 default '0',";
		$postgre_sql .= "  order_item_id INT4 default '0',";
		$postgre_sql .= "  commission_amount FLOAT4 default '0',";
		$postgre_sql .= "  commission_action INT4 default '0',";
		$postgre_sql .= "  commission_type INT4 default '0',";
		$postgre_sql .= "  date_added TIMESTAMP NOT NULL";
		$postgre_sql .= "  ,PRIMARY KEY (commission_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "users_commissions (";
		$access_sql .= "  [commission_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [payment_id] INTEGER,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [order_id] INTEGER,";
		$access_sql .= "  [order_item_id] INTEGER,";
		$access_sql .= "  [commission_amount] FLOAT,";
		$access_sql .= "  [commission_action] INTEGER,";
		$access_sql .= "  [commission_type] INTEGER,";
		$access_sql .= "  [date_added] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (commission_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_commissions_date_added ON " . $table_prefix . "users_commissions (date_added)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_commissions_order_id ON " . $table_prefix . "users_commissions (order_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_commissions_order__39 ON " . $table_prefix . "users_commissions (order_item_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_commissions_payment_id ON " . $table_prefix . "users_commissions (payment_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_commissions_user_id ON " . $table_prefix . "users_commissions (user_id)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "users_payments (";
		$mysql_sql .= "  `payment_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `user_id` INT(11) default '0',";
		$mysql_sql .= "  `is_paid` INT(11) default '0',";
		$mysql_sql .= "  `transaction_id` VARCHAR(128),";
		$mysql_sql .= "  `payment_total` DOUBLE(16,2) default '0',";
		$mysql_sql .= "  `payment_name` VARCHAR(255),";
		$mysql_sql .= "  `payment_notes` TEXT,";
		$mysql_sql .= "  `date_added` DATETIME,";
		$mysql_sql .= "  `date_modified` DATETIME,";
		$mysql_sql .= "  `date_paid` DATETIME,";
		$mysql_sql .= "  `admin_id_added_by` INT(11) default '0',";
		$mysql_sql .= "  `admin_id_modified_by` INT(11) default '0'";
		$mysql_sql .= "  ,KEY date_paid (date_paid)";
		$mysql_sql .= "  ,KEY is_paid (is_paid)";
		$mysql_sql .= "  ,PRIMARY KEY (payment_id)";
		$mysql_sql .= "  ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "users_payments START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "users_payments (";
		$postgre_sql .= "  payment_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "users_payments'),";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  is_paid INT4 default '0',";
		$postgre_sql .= "  transaction_id VARCHAR(128),";
		$postgre_sql .= "  payment_total FLOAT4 default '0',";
		$postgre_sql .= "  payment_name VARCHAR(255),";
		$postgre_sql .= "  payment_notes TEXT,";
		$postgre_sql .= "  date_added TIMESTAMP,";
		$postgre_sql .= "  date_modified TIMESTAMP,";
		$postgre_sql .= "  date_paid TIMESTAMP,";
		$postgre_sql .= "  admin_id_added_by INT4 default '0',";
		$postgre_sql .= "  admin_id_modified_by INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (payment_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "users_payments (";
		$access_sql .= "  [payment_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [is_paid] INTEGER,";
		$access_sql .= "  [transaction_id] VARCHAR(128),";
		$access_sql .= "  [payment_total] FLOAT,";
		$access_sql .= "  [payment_name] VARCHAR(255),";
		$access_sql .= "  [payment_notes] LONGTEXT,";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [date_modified] DATETIME,";
		$access_sql .= "  [date_paid] DATETIME,";
		$access_sql .= "  [admin_id_added_by] INTEGER,";
		$access_sql .= "  [admin_id_modified_by] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (payment_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_payments_date_paid ON " . $table_prefix . "users_payments (date_paid)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_payments_is_paid ON " . $table_prefix . "users_payments (is_paid)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_payments_user_id ON " . $table_prefix . "users_payments (user_id)";
		}

		// menus tables
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "menus (";
		$mysql_sql .= "  `menu_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `menu_title` VARCHAR(255),";
		$mysql_sql .= "  `menu_name` VARCHAR(128),";
		$mysql_sql .= "  `menu_notes` TEXT";
		$mysql_sql .= "  ,PRIMARY KEY (menu_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "menus START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "menus (";
		$postgre_sql .= "  menu_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "menus'),";
		$postgre_sql .= "  menu_title VARCHAR(255),";
		$postgre_sql .= "  menu_name VARCHAR(128),";
		$postgre_sql .= "  menu_notes TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (menu_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "menus (";
		$access_sql .= "  [menu_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [menu_title] VARCHAR(255),";
		$access_sql .= "  [menu_name] VARCHAR(128),";
		$access_sql .= "  [menu_notes] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (menu_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "menus_items (";
		$mysql_sql .= "  `menu_item_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `menu_id` INT(11) default '0',";
		$mysql_sql .= "  `parent_menu_item_id` INT(11) default '0',";
		$mysql_sql .= "  `menu_path` VARCHAR(255),";
		$mysql_sql .= "  `menu_title` VARCHAR(255),";
		$mysql_sql .= "  `menu_url` VARCHAR(255),";
		$mysql_sql .= "  `menu_target` VARCHAR(32),";
		$mysql_sql .= "  `menu_image` VARCHAR(255),";
		$mysql_sql .= "  `menu_image_active` VARCHAR(255),";
		$mysql_sql .= "  `menu_prefix` VARCHAR(255),";
		$mysql_sql .= "  `menu_prefix_active` VARCHAR(255),";
		$mysql_sql .= "  `menu_order` INT(11) default '0',";
		$mysql_sql .= "  `show_non_logged` INT(11) default '0',";
		$mysql_sql .= "  `show_logged` INT(11) default '0'";
		$mysql_sql .= "  ,KEY menu_id (menu_id)";
		$mysql_sql .= "  ,KEY menu_item_id (menu_item_id)";
		$mysql_sql .= "  ,KEY parent_menu_item_id (parent_menu_item_id)";
		$mysql_sql .= "  ,PRIMARY KEY (menu_item_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "menus_items START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "menus_items (";
		$postgre_sql .= "  menu_item_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "menus_items'),";
		$postgre_sql .= "  menu_id INT4 default '0',";
		$postgre_sql .= "  parent_menu_item_id INT4 default '0',";
		$postgre_sql .= "  menu_path VARCHAR(255),";
		$postgre_sql .= "  menu_title VARCHAR(255),";
		$postgre_sql .= "  menu_url VARCHAR(255),";
		$postgre_sql .= "  menu_target VARCHAR(32),";
		$postgre_sql .= "  menu_image VARCHAR(255),";
		$postgre_sql .= "  menu_image_active VARCHAR(255),";
		$postgre_sql .= "  menu_prefix VARCHAR(255),";
		$postgre_sql .= "  menu_prefix_active VARCHAR(255),";
		$postgre_sql .= "  menu_order INT4 default '0',";
		$postgre_sql .= "  show_non_logged INT4 default '0',";
		$postgre_sql .= "  show_logged INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (menu_item_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "menus_items (";
		$access_sql .= "  [menu_item_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [menu_id] INTEGER,";
		$access_sql .= "  [parent_menu_item_id] INTEGER,";
		$access_sql .= "  [menu_path] VARCHAR(255),";
		$access_sql .= "  [menu_title] VARCHAR(255),";
		$access_sql .= "  [menu_url] VARCHAR(255),";
		$access_sql .= "  [menu_target] VARCHAR(32),";
		$access_sql .= "  [menu_image] VARCHAR(255),";
		$access_sql .= "  [menu_image_active] VARCHAR(255),";
		$access_sql .= "  [menu_prefix] VARCHAR(255),";
		$access_sql .= "  [menu_prefix_active] VARCHAR(255),";
		$access_sql .= "  [menu_order] INTEGER,";
		$access_sql .= "  [show_non_logged] INTEGER,";
		$access_sql .= "  [show_logged] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (menu_item_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "menus_items_menu_id ON " . $table_prefix . "menus_items (menu_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "menus_items_parent_menu__15 ON " . $table_prefix . "menus_items (parent_menu_item_id)";
		}

		// add tracking tables
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "tracking_visits (";
		$mysql_sql .= "  `visit_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `parent_visit_id` INT(11) default '0',";
		$mysql_sql .= "  `visit_number` INT(11) default '1',";
		$mysql_sql .= "  `ip_long` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `ip_text` VARCHAR(32) NOT NULL,";
		$mysql_sql .= "  `forwarded_ips` VARCHAR(50),";
		$mysql_sql .= "  `affiliate_code` VARCHAR(64),";
		$mysql_sql .= "  `keywords` VARCHAR(255),";
		$mysql_sql .= "  `user_agent` VARCHAR(255),";
		$mysql_sql .= "  `request_uri` VARCHAR(255),";
		$mysql_sql .= "  `request_page` VARCHAR(255),";
		$mysql_sql .= "  `referer` VARCHAR(255),";
		$mysql_sql .= "  `referer_host` VARCHAR(255),";
		$mysql_sql .= "  `referer_engine_id` INT(11) default '0',";
		$mysql_sql .= "  `robot_engine_id` INT(11) default '0',";
		$mysql_sql .= "  `date_added` DATETIME NOT NULL,";
		$mysql_sql .= "  `year_added` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `month_added` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `week_added` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `day_added` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `hour_added` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,KEY affiliate_code (affiliate_code)";
		$mysql_sql .= "  ,KEY date_added (date_added)";
		$mysql_sql .= "  ,KEY day_added (day_added)";
		$mysql_sql .= "  ,KEY hour_added (hour_added)";
		$mysql_sql .= "  ,KEY keyword_phrase (keywords)";
		$mysql_sql .= "  ,KEY month_added (month_added)";
		$mysql_sql .= "  ,KEY week_added (week_added)";
		$mysql_sql .= "  ,KEY parent_visit_id (parent_visit_id)";
		$mysql_sql .= "  ,PRIMARY KEY (visit_id)";
		$mysql_sql .= "  ,KEY referer_engine_id (referer_engine_id)";
		$mysql_sql .= "  ,KEY referer_host (referer_host)";
		$mysql_sql .= "  ,KEY remote_ip_long (ip_long)";
		$mysql_sql .= "  ,KEY robot_engine_id (robot_engine_id)";
		$mysql_sql .= "  ,KEY site_id (site_id)";
		$mysql_sql .= "  ,KEY year_added (year_added))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "tracking_visits START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "tracking_visits (";
		$postgre_sql .= "  visit_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "tracking_visits'),";
		$postgre_sql .= "  parent_visit_id INT4 default '0',";
		$postgre_sql .= "  visit_number INT4 default '1',";
		$postgre_sql .= "  ip_long INT4 NOT NULL default '0',";
		$postgre_sql .= "  ip_text VARCHAR(32) NOT NULL,";
		$postgre_sql .= "  forwarded_ips VARCHAR(50),";
		$postgre_sql .= "  affiliate_code VARCHAR(64),";
		$postgre_sql .= "  keywords VARCHAR(255),";
		$postgre_sql .= "  user_agent VARCHAR(255),";
		$postgre_sql .= "  request_uri VARCHAR(255),";
		$postgre_sql .= "  request_page VARCHAR(255),";
		$postgre_sql .= "  referer VARCHAR(255),";
		$postgre_sql .= "  referer_host VARCHAR(255),";
		$postgre_sql .= "  referer_engine_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  robot_engine_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  date_added TIMESTAMP NOT NULL,";
		$postgre_sql .= "  year_added INT4 NOT NULL default '0',";
		$postgre_sql .= "  month_added INT4 NOT NULL default '0',";
		$postgre_sql .= "  week_added INT4 NOT NULL default '0',";
		$postgre_sql .= "  day_added INT4 NOT NULL default '0',";
		$postgre_sql .= "  hour_added INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (visit_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "tracking_visits (";
		$access_sql .= "  [visit_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [parent_visit_id] INTEGER,";
		$access_sql .= "  [visit_number] INTEGER,";
		$access_sql .= "  [ip_long] INTEGER,";
		$access_sql .= "  [ip_text] VARCHAR(32),";
		$access_sql .= "  [forwarded_ips] VARCHAR(50),";
		$access_sql .= "  [affiliate_code] VARCHAR(64),";
		$access_sql .= "  [keywords] VARCHAR(255),";
		$access_sql .= "  [user_agent] VARCHAR(255),";
		$access_sql .= "  [request_uri] VARCHAR(255),";
		$access_sql .= "  [request_page] VARCHAR(255),";
		$access_sql .= "  [referer] VARCHAR(255),";
		$access_sql .= "  [referer_host] VARCHAR(255),";
		$access_sql .= "  [referer_engine_id] INTEGER,";
		$access_sql .= "  [robot_engine_id] INTEGER,";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [year_added] INTEGER,";
		$access_sql .= "  [month_added] INTEGER,";
		$access_sql .= "  [week_added] INTEGER,";
		$access_sql .= "  [day_added] INTEGER,";
		$access_sql .= "  [hour_added] INTEGER,";
		$access_sql .= "  [site_id] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (visit_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];


		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_affiliat_40 ON " . $table_prefix . "tracking_visits (affiliate_code)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_date_added ON " . $table_prefix . "tracking_visits (date_added)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_day_added ON " . $table_prefix . "tracking_visits (day_added)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_hour_added ON " . $table_prefix . "tracking_visits (hour_added)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_keyword__41 ON " . $table_prefix . "tracking_visits (keywords)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_month_added ON " . $table_prefix . "tracking_visits (month_added)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_week_added ON " . $table_prefix . "tracking_visits (week_added)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_parent_v_42 ON " . $table_prefix . "tracking_visits (parent_visit_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_referer__47 ON " . $table_prefix . "tracking_visits (referer_engine_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_referer_host ON " . $table_prefix . "tracking_visits (referer_host)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_remote_i_43 ON " . $table_prefix . "tracking_visits (ip_long)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_robot_en_49 ON " . $table_prefix . "tracking_visits (robot_engine_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_site_id ON " . $table_prefix . "tracking_visits (site_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_visits_year_added ON " . $table_prefix . "tracking_visits (year_added)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "tracking_pages (";
		$mysql_sql .= "  `page_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `visit_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `ip_long` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `ip_text` VARCHAR(32),";
		$mysql_sql .= "  `forwarded_ips` VARCHAR(255),";
		$mysql_sql .= "  `request_uri` VARCHAR(255),";
		$mysql_sql .= "  `request_page` VARCHAR(255),";
		$mysql_sql .= "  `date_added` DATETIME NOT NULL,";
		$mysql_sql .= "  `year_added` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `month_added` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `day_added` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `hour_added` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,KEY date_added (date_added)";
		$mysql_sql .= "  ,KEY day_added (day_added)";
		$mysql_sql .= "  ,KEY hour_added (hour_added)";
		$mysql_sql .= "  ,KEY ip_long (ip_long)";
		$mysql_sql .= "  ,KEY month_added (month_added)";
		$mysql_sql .= "  ,PRIMARY KEY (page_id)";
		$mysql_sql .= "  ,KEY site_id (site_id)";
		$mysql_sql .= "  ,KEY visit_id (visit_id)";
		$mysql_sql .= "  ,KEY year_added (year_added))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "tracking_pages START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "tracking_pages (";
		$postgre_sql .= "  page_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "tracking_pages'),";
		$postgre_sql .= "  visit_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  ip_long INT4 NOT NULL default '0',";
		$postgre_sql .= "  ip_text VARCHAR(32),";
		$postgre_sql .= "  forwarded_ips VARCHAR(255),";
		$postgre_sql .= "  request_uri VARCHAR(255),";
		$postgre_sql .= "  request_page VARCHAR(255),";
		$postgre_sql .= "  date_added TIMESTAMP NOT NULL,";
		$postgre_sql .= "  year_added INT4 NOT NULL default '0',";
		$postgre_sql .= "  month_added INT4 NOT NULL default '0',";
		$postgre_sql .= "  day_added INT4 NOT NULL default '0',";
		$postgre_sql .= "  hour_added INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (page_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "tracking_pages (";
		$access_sql .= "  [page_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [visit_id] INTEGER,";
		$access_sql .= "  [ip_long] INTEGER,";
		$access_sql .= "  [ip_text] VARCHAR(32),";
		$access_sql .= "  [forwarded_ips] VARCHAR(255),";
		$access_sql .= "  [request_uri] VARCHAR(255),";
		$access_sql .= "  [request_page] VARCHAR(255),";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [year_added] INTEGER,";
		$access_sql .= "  [month_added] INTEGER,";
		$access_sql .= "  [day_added] INTEGER,";
		$access_sql .= "  [hour_added] INTEGER,";
		$access_sql .= "  [site_id] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (page_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_pages_date_added ON " . $table_prefix . "tracking_pages (date_added)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_pages_day_added ON " . $table_prefix . "tracking_pages (day_added)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_pages_hour_added ON " . $table_prefix . "tracking_pages (hour_added)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_pages_ip_long ON " . $table_prefix . "tracking_pages (ip_long)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_pages_month_added ON " . $table_prefix . "tracking_pages (month_added)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_pages_site_id ON " . $table_prefix . "tracking_pages (site_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_pages_visit_id ON " . $table_prefix . "tracking_pages (visit_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "tracking_pages_year_added ON " . $table_prefix . "tracking_pages (year_added)";
		}

		// Call Center changes
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_call_center INT(11) default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_call_center INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_call_center INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_custom_properties ADD COLUMN property_show INT(11) default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_custom_properties ADD COLUMN property_show INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_custom_properties ADD COLUMN property_show INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		// Currency active image
		$sqls[] = "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN currency_image_active VARCHAR(255)";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN visit_id INT(11) default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN visit_id INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN visit_id INTEGER"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "orders SET visit_id=0 ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN keywords VARCHAR(255)";

		// create support page layout
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'support_new', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'support_new', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'support_new', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'support_new', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'support_new', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'support_new', 'right_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'support_new', 'support_block', 0, 'middle')";

		// distributors/merchants fields
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_approved INT(11) NOT NULL default '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_approved INT4 NOT NULL default '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_approved INTEGER NOT NULL "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "items SET is_approved=1 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "items_is_approved ON " . $table_prefix . "items (is_approved) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN allowed_post INT(11) default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN allowed_post INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN allowed_post INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN affiliate_user_id INT(11) NOT NULL default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN affiliate_user_id INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN affiliate_user_id INTEGER NOT NULL "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "orders SET affiliate_user_id=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "orders_affiliate_user_id ON " . $table_prefix . "orders (affiliate_user_id) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "header_links ADD COLUMN menu_target VARCHAR(32)";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_merchants_commission DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_merchants_commission FLOAT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_merchants_commission FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_affiliate_commission DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_affiliate_commission FLOAT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_affiliate_commission FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "orders SET total_merchants_commission=0, total_affiliate_commission=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN affiliate_user_id INT(11) NOT NULL default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN affiliate_user_id INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN affiliate_user_id INTEGER NOT NULL "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "orders_items SET affiliate_user_id=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_affiliate_user ON " . $table_prefix . "orders_items (affiliate_user_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN show_for_user INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN show_for_user INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN show_for_user INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET show_for_user=1, commission_action=1 WHERE status_name LIKE '%Paid%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET show_for_user=1, commission_action=-1 WHERE status_name LIKE '%Cancelled%' ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN is_user INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN is_user INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN is_user INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "item_types SET is_user=1 WHERE item_type_name LIKE '%Product%' OR item_type_name LIKE '%Accessory%' ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN is_active INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN is_active INT4 default '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN is_active INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN show_for_user INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN show_for_user INT4 default '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN show_for_user INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "user_types SET is_active=1, show_for_user=1  ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN scheme_name VARCHAR(64)";


		$mysql_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `category_name` VARCHAR(255),";
		$mysql_sql .= "  `friendly_url` VARCHAR(255),";
		$mysql_sql .= "  `category_order` INT(11) default '0',";
		$mysql_sql .= "  `short_description` TEXT,";
		$mysql_sql .= "  `full_description` TEXT,";
		$mysql_sql .= "  `allowed_view` INT(11) default '0',";
		$mysql_sql .= "  `meta_title` VARCHAR(255),";
		$mysql_sql .= "  `meta_keywords` VARCHAR(255),";
		$mysql_sql .= "  `meta_description` VARCHAR(255),";
		$mysql_sql .= "  `date_added` DATETIME,";
		$mysql_sql .= "  `date_modified` DATETIME,";
		$mysql_sql .= "  `admin_id_added_by` INT(11) default '0',";
		$mysql_sql .= "  `admin_id_modified_by` INT(11) default '0'";
		$mysql_sql .= "  ,KEY friendly_url (friendly_url)";
		$mysql_sql .= "  ,PRIMARY KEY (category_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "manuals_categories START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories (";
		$postgre_sql .= "  category_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "manuals_categories'),";
		$postgre_sql .= "  category_name VARCHAR(255),";
		$postgre_sql .= "  friendly_url VARCHAR(255),";
		$postgre_sql .= "  category_order INT4 default '0',";
		$postgre_sql .= "  short_description TEXT,";
		$postgre_sql .= "  full_description TEXT,";
		$postgre_sql .= "  allowed_view INT4 default '0',";
		$postgre_sql .= "  meta_title VARCHAR(255),";
		$postgre_sql .= "  meta_keywords VARCHAR(255),";
		$postgre_sql .= "  meta_description VARCHAR(255),";
		$postgre_sql .= "  date_added TIMESTAMP,";
		$postgre_sql .= "  date_modified TIMESTAMP,";
		$postgre_sql .= "  admin_id_added_by INT4 default '0',";
		$postgre_sql .= "  admin_id_modified_by INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories (";
		$access_sql .= "  [category_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [category_name] VARCHAR(255),";
		$access_sql .= "  [friendly_url] VARCHAR(255),";
		$access_sql .= "  [category_order] INTEGER,";
		$access_sql .= "  [short_description] LONGTEXT,";
		$access_sql .= "  [full_description] LONGTEXT,";
		$access_sql .= "  [allowed_view] INTEGER,";
		$access_sql .= "  [meta_title] VARCHAR(255),";
		$access_sql .= "  [meta_keywords] VARCHAR(255),";
		$access_sql .= "  [meta_description] VARCHAR(255),";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [date_modified] DATETIME,";
		$access_sql .= "  [admin_id_added_by] INTEGER,";
		$access_sql .= "  [admin_id_modified_by] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (category_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "manuals_categories_frien_17 ON " . $table_prefix . "manuals_categories (friendly_url)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "manuals_list (";
		$mysql_sql .= "  `manual_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `category_id` INT(11) default '0',";
		$mysql_sql .= "  `alias_manual_id` INT(11) default '0',";
		$mysql_sql .= "  `manual_order` INT(11) default '0',";
		$mysql_sql .= "  `manual_title` VARCHAR(255),";
		$mysql_sql .= "  `friendly_url` VARCHAR(255),";
		$mysql_sql .= "  `short_description` TEXT,";
		$mysql_sql .= "  `full_description` TEXT,";
		$mysql_sql .= "  `allowed_view` INT(11) default '0',";
		$mysql_sql .= "  `meta_title` VARCHAR(255),";
		$mysql_sql .= "  `meta_keywords` VARCHAR(255),";
		$mysql_sql .= "  `meta_description` VARCHAR(255),";
		$mysql_sql .= "  `date_added` DATETIME,";
		$mysql_sql .= "  `date_modified` DATETIME,";
		$mysql_sql .= "  `admin_id_added_by` INT(11) default '0',";
		$mysql_sql .= "  `admin_id_modified_by` INT(11) default '0'";
		$mysql_sql .= "  ,KEY alias_manual_id (alias_manual_id)";
		$mysql_sql .= "  ,KEY category_id (category_id)";
		$mysql_sql .= "  ,KEY friendly_url (friendly_url)";
		$mysql_sql .= "  ,PRIMARY KEY (manual_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "manuals_list START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "manuals_list (";
		$postgre_sql .= "  manual_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "manuals_list'),";
		$postgre_sql .= "  category_id INT4 default '0',";
		$postgre_sql .= "  alias_manual_id INT4 default '0',";
		$postgre_sql .= "  manual_order INT4 default '0',";
		$postgre_sql .= "  manual_title VARCHAR(255),";
		$postgre_sql .= "  friendly_url VARCHAR(255),";
		$postgre_sql .= "  short_description TEXT,";
		$postgre_sql .= "  full_description TEXT,";
		$postgre_sql .= "  allowed_view INT4 default '0',";
		$postgre_sql .= "  meta_title VARCHAR(255),";
		$postgre_sql .= "  meta_keywords VARCHAR(255),";
		$postgre_sql .= "  meta_description VARCHAR(255),";
		$postgre_sql .= "  date_added TIMESTAMP,";
		$postgre_sql .= "  date_modified TIMESTAMP,";
		$postgre_sql .= "  admin_id_added_by INT4 default '0',";
		$postgre_sql .= "  admin_id_modified_by INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (manual_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "manuals_list (";
		$access_sql .= "  [manual_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [category_id] INTEGER,";
		$access_sql .= "  [alias_manual_id] INTEGER,";
		$access_sql .= "  [manual_order] INTEGER,";
		$access_sql .= "  [manual_title] VARCHAR(255),";
		$access_sql .= "  [friendly_url] VARCHAR(255),";
		$access_sql .= "  [short_description] LONGTEXT,";
		$access_sql .= "  [full_description] LONGTEXT,";
		$access_sql .= "  [allowed_view] INTEGER,";
		$access_sql .= "  [meta_title] VARCHAR(255),";
		$access_sql .= "  [meta_keywords] VARCHAR(255),";
		$access_sql .= "  [meta_description] VARCHAR(255),";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [date_modified] DATETIME,";
		$access_sql .= "  [admin_id_added_by] INTEGER,";
		$access_sql .= "  [admin_id_modified_by] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (manual_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "manuals_list_alias_manual_id ON " . $table_prefix . "manuals_list (alias_manual_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "manuals_list_category_id ON " . $table_prefix . "manuals_list (category_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "manuals_list_friendly_url ON " . $table_prefix . "manuals_list (friendly_url)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "manuals_articles (";
		$mysql_sql .= "  `article_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `manual_id` INT(11) default '0',";
		$mysql_sql .= "  `parent_article_id` INT(11) default '0',";
		$mysql_sql .= "  `alias_article_id` INT(11) default '0',";
		$mysql_sql .= "  `article_path` VARCHAR(255) NOT NULL,";
		$mysql_sql .= "  `article_title` VARCHAR(255),";
		$mysql_sql .= "  `friendly_url` VARCHAR(255),";
		$mysql_sql .= "  `article_order` INT(11) default '0',";
		$mysql_sql .= "  `section_number` VARCHAR(255),";
		$mysql_sql .= "  `image_small` VARCHAR(255),";
		$mysql_sql .= "  `image_small_alt` VARCHAR(255),";
		$mysql_sql .= "  `image_large` VARCHAR(255),";
		$mysql_sql .= "  `image_large_alt` VARCHAR(255),";
		$mysql_sql .= "  `short_description` TEXT,";
		$mysql_sql .= "  `full_description` TEXT,";
		$mysql_sql .= "  `allowed_view` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `shown_in_contents` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `meta_title` VARCHAR(255),";
		$mysql_sql .= "  `meta_keywords` VARCHAR(255),";
		$mysql_sql .= "  `meta_description` VARCHAR(255),";
		$mysql_sql .= "  `date_added` DATETIME,";
		$mysql_sql .= "  `date_modified` DATETIME,";
		$mysql_sql .= "  `admin_id_added_by` INT(11) default '0',";
		$mysql_sql .= "  `admin_id_modified_by` INT(11) default '0'";
		$mysql_sql .= "  ,KEY alias_article_id (alias_article_id)";
		$mysql_sql .= "  ,KEY article_path (article_path)";
		$mysql_sql .= "  ,KEY friendly_url (friendly_url)";
		$mysql_sql .= "  ,KEY manual_id (manual_id)";
		$mysql_sql .= "  ,KEY parent_article_id (parent_article_id)";
		$mysql_sql .= "  ,PRIMARY KEY (article_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "manuals_articles START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "manuals_articles (";
		$postgre_sql .= "  article_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "manuals_articles'),";
		$postgre_sql .= "  manual_id INT4 default '0',";
		$postgre_sql .= "  parent_article_id INT4 default '0',";
		$postgre_sql .= "  alias_article_id INT4 default '0',";
		$postgre_sql .= "  article_path VARCHAR(255) NOT NULL,";
		$postgre_sql .= "  article_title VARCHAR(255),";
		$postgre_sql .= "  friendly_url VARCHAR(255),";
		$postgre_sql .= "  article_order INT4 default '0',";
		$postgre_sql .= "  section_number VARCHAR(255),";
		$postgre_sql .= "  image_small VARCHAR(255),";
		$postgre_sql .= "  image_small_alt VARCHAR(255),";
		$postgre_sql .= "  image_large VARCHAR(255),";
		$postgre_sql .= "  image_large_alt VARCHAR(255),";
		$postgre_sql .= "  short_description TEXT,";
		$postgre_sql .= "  full_description LONGTEXT,";
		$postgre_sql .= "  allowed_view INT4 NOT NULL default '0',";
		$postgre_sql .= "  shown_in_contents INT4 NOT NULL default '0',";
		$postgre_sql .= "  meta_title VARCHAR(255),";
		$postgre_sql .= "  meta_keywords VARCHAR(255),";
		$postgre_sql .= "  meta_description VARCHAR(255),";
		$postgre_sql .= "  date_added TIMESTAMP,";
		$postgre_sql .= "  date_modified TIMESTAMP,";
		$postgre_sql .= "  admin_id_added_by INT4 default '0',";
		$postgre_sql .= "  admin_id_modified_by INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (article_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "manuals_articles (";
		$access_sql .= "  [article_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [manual_id] INTEGER,";
		$access_sql .= "  [parent_article_id] INTEGER,";
		$access_sql .= "  [alias_article_id] INTEGER,";
		$access_sql .= "  [article_path] VARCHAR(255),";
		$access_sql .= "  [article_title] VARCHAR(255),";
		$access_sql .= "  [friendly_url] VARCHAR(255),";
		$access_sql .= "  [article_order] INTEGER,";
		$access_sql .= "  [section_number] VARCHAR(255),";
		$access_sql .= "  [image_small] VARCHAR(255),";
		$access_sql .= "  [image_small_alt] VARCHAR(255),";
		$access_sql .= "  [image_large] VARCHAR(255),";
		$access_sql .= "  [image_large_alt] VARCHAR(255),";
		$access_sql .= "  [short_description] LONGTEXT,";
		$access_sql .= "  [full_description] LONGTEXT,";
		$access_sql .= "  [allowed_view] INTEGER,";
		$access_sql .= "  [shown_in_contents] INTEGER,";
		$access_sql .= "  [meta_title] VARCHAR(255),";
		$access_sql .= "  [meta_keywords] VARCHAR(255),";
		$access_sql .= "  [meta_description] VARCHAR(255),";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [date_modified] DATETIME,";
		$access_sql .= "  [admin_id_added_by] INTEGER,";
		$access_sql .= "  [admin_id_modified_by] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (article_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "manuals_articles_alias_a_15 ON " . $table_prefix . "manuals_articles (alias_article_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "manuals_articles_article_path ON " . $table_prefix . "manuals_articles (article_path)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "manuals_articles_friendly_url ON " . $table_prefix . "manuals_articles (friendly_url)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "manuals_articles_manual_id ON " . $table_prefix . "manuals_articles (manual_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "manuals_articles_parent__16 ON " . $table_prefix . "manuals_articles (parent_article_id)";
		}


		$mysql_sql  = "CREATE TABLE " . $table_prefix . "search_engines (";
		$mysql_sql .= "  `engine_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `engine_name` VARCHAR(64),";
		$mysql_sql .= "  `keywords_parameter` VARCHAR(32),";
		$mysql_sql .= "  `referer_regexp` TEXT,";
		$mysql_sql .= "  `user_agent_regexp` TEXT,";
		$mysql_sql .= "  `ip_regexp` TEXT";
		$mysql_sql .= "  ,PRIMARY KEY (engine_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "search_engines START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "search_engines (";
		$postgre_sql .= "  engine_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "search_engines'),";
		$postgre_sql .= "  engine_name VARCHAR(64),";
		$postgre_sql .= "  keywords_parameter VARCHAR(32),";
		$postgre_sql .= "  referer_regexp TEXT,";
		$postgre_sql .= "  user_agent_regexp TEXT,";
		$postgre_sql .= "  ip_regexp TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (engine_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "search_engines (";
		$access_sql .= "  [engine_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [engine_name] VARCHAR(64),";
		$access_sql .= "  [keywords_parameter] VARCHAR(32),";
		$access_sql .= "  [referer_regexp] LONGTEXT,";
		$access_sql .= "  [user_agent_regexp] LONGTEXT,";
		$access_sql .= "  [ip_regexp] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (engine_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "users_files (";
		$mysql_sql .= "  `file_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `user_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `file_type` VARCHAR(32),";
		$mysql_sql .= "  `file_name` VARCHAR(255),";
		$mysql_sql .= "  `file_path` VARCHAR(255)";
		$mysql_sql .= "  ,PRIMARY KEY (file_id)";
		$mysql_sql .= "  ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "users_files START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "users_files (";
		$postgre_sql .= "  file_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "users_files'),";
		$postgre_sql .= "  user_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  file_type VARCHAR(32),";
		$postgre_sql .= "  file_name VARCHAR(255),";
		$postgre_sql .= "  file_path VARCHAR(255)";
		$postgre_sql .= "  ,PRIMARY KEY (file_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "users_files (";
		$access_sql .= "  [file_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [file_type] VARCHAR(32),";
		$access_sql .= "  [file_name] VARCHAR(255),";
		$access_sql .= "  [file_path] VARCHAR(255)";
		$access_sql .= "  ,PRIMARY KEY (file_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_files_user_id ON " . $table_prefix . "users_files (user_id)";
		}

		// Copy products_list page settings to new products_search page
		$sql = "SELECT * FROM " . $table_prefix . "page_settings WHERE page_name='products_list'";
		$db->query($sql);
		while ($db->next_record()) {
			$setting_name = $db->f("setting_name");
			$setting_order = $db->f("setting_order");
			$setting_value = $db->f("setting_value");
			$sql  = " INSERT INTO " . $table_prefix . "page_settings (layout_id, page_name, setting_name, setting_order, setting_value) VALUES (0, 'products_search', ";
			$sql .= $db->tosql($setting_name, TEXT, true, false) . ",";
			$sql .= $db->tosql($setting_order, INTEGER) . ",";
			$sql .= $db->tosql($setting_value, TEXT, true, false) . ")";
			$sqls[] = $sql;
		}

		if ($db_type == "mysql") {
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (1, 'Google', 'q', '/http:\\\\/\\\\/www\\\\.google\\\\.\\\\w{2,4}(\\\\.\\\\w{2})?\\\\//i', '/Googlebot.*google\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (2, 'Yahoo', 'p', '/search\\\\.yahoo\\\\.com\\\\//i', '/Yahoo.*Slurp.*yahoo\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (3, 'MSN', 'q', '/search(\\\\.\\\\w+)?\\\\.msn\\\\.\\\\w{2,4}(\\\\.\\\\w{2})?\\\\//i', '/msnbot.*msn\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (4, 'Ask.com', 'q', '/\\\\.ask\\\\.com\\\\//i', '/Ask.*Jeeves\\\\/Teoma.*ask\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (5, 'Gigablast', 'q', '/gigablast\\\\.com\\\\//i', '/Gigabot.*gigablast\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (6, 'Exalead', 'q', '/exalead\\\\.com\\\\//i', '/Exabot/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (7, 'LookSmart', 'qt', '/looksmart\\\\.(com|net)\\\\//i', '/ZyBorg.*looksmart.*WISEnutbot\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (8, 'Zibb', 'q', '/zibb\\\\.com\\\\//i', '/Zibber.*zibb\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (9, 'Entireweb', 'q', '/entireweb\\\\.com\\\\//i', '/Speedy.*Spider.*entireweb\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (10, 'Seekport', 'query', '/seekport\\\\.\\\\w{2,4}(\\\\.\\\\w{2})?\\\\//i', '/Seekbot.*Seekbot\\\\.net/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (11, 'Become', 'q', '/become\\\\.com\\\\//i', '/BecomeBot.*become\\\\.com/i')";
		} elseif ($db_type == "postgre") {
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (1, 'Google', 'q', '/http:\\\\/\\\\/www\\\\.google\\\\.\\\\w{2,4}(\\\\.\\\\w{2})?\\\\//i', '/Googlebot.*google\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (2, 'Yahoo', 'p', '/search\\\\.yahoo\\\\.com\\\\//i', '/Yahoo.*Slurp.*yahoo\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (3, 'MSN', 'q', '/search(\\\\.\\\\w+)?\\\\.msn\\\\.\\\\w{2,4}(\\\\.\\\\w{2})?\\\\//i', '/msnbot.*msn\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (4, 'Ask.com', 'q', '/\\\\.ask\\\\.com\\\\//i', '/Ask.*Jeeves\\\\/Teoma.*ask\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (5, 'Gigablast', 'q', '/gigablast\\\\.com\\\\//i', '/Gigabot.*gigablast\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (6, 'Exalead', 'q', '/exalead\\\\.com\\\\//i', '/Exabot/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (7, 'LookSmart', 'qt', '/looksmart\\\\.(com|net)\\\\//i', '/ZyBorg.*looksmart.*WISEnutbot\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (8, 'Zibb', 'q', '/zibb\\\\.com\\\\//i', '/Zibber.*zibb\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (9, 'Entireweb', 'q', '/entireweb\\\\.com\\\\//i', '/Speedy.*Spider.*entireweb\\\\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (10, 'Seekport', 'query', '/seekport\\\\.\\\\w{2,4}(\\\\.\\\\w{2})?\\\\//i', '/Seekbot.*Seekbot\\\\.net/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (11, 'Become', 'q', '/become\\\\.com\\\\//i', '/BecomeBot.*become\\\\.com/i')";
		} elseif ($db_type == "access") {
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (1, 'Google', 'q', '/http:\/\/www\.google\.\w{2,4}(\.\w{2})?\//i', '/Googlebot.*google\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (2, 'Yahoo', 'p', '/search\.yahoo\.com\//i', '/Yahoo.*Slurp.*yahoo\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (3, 'MSN', 'q', '/search(\.\w+)?\.msn\.\w{2,4}(\.\w{2})?\//i', '/msnbot.*msn\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (4, 'Ask.com', 'q', '/\.ask\.com\//i', '/Ask.*Jeeves\/Teoma.*ask\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (5, 'Gigablast', 'q', '/gigablast\.com\//i', '/Gigabot.*gigablast\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (6, 'Exalead', 'q', '/exalead\.com\//i', '/Exabot/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (7, 'LookSmart', 'qt', '/looksmart\.(com|net)\//i', '/ZyBorg.*looksmart.*WISEnutbot\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (8, 'Zibb', 'q', '/zibb\.com\//i', '/Zibber.*zibb\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (9, 'Entireweb', 'q', '/entireweb\.com\//i', '/Speedy.*Spider.*entireweb\.com/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (10, 'Seekport', 'query', '/seekport\.\w{2,4}(\.\w{2})?\//i', '/Seekbot.*Seekbot\.net/i')";
			$sqls[] = " INSERT INTO " . $table_prefix . "search_engines (engine_id, engine_name, keywords_parameter, referer_regexp, user_agent_regexp) VALUES (11, 'Become', 'q', '/become.com\//i', '/BecomeBot.*become\.com/i')";
		}

		// Add default parameters for manuals pages
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_article_details' , 'left_column_hide' , NULL , '0' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_article_details' , 'left_column_width' , NULL , '25%' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_article_details' , 'manuals_article_details' , 0 , 'middle' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_article_details' , 'manuals_search' , 0 , 'left' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_article_details' , 'middle_column_hide' , NULL , '0' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_article_details' , 'middle_column_width' , NULL , '75%' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_article_details' , 'right_column_hide' , NULL , '1' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_articles' , 'left_column_hide' , NULL , '0' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_articles' , 'left_column_width' , NULL , '25%' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_articles' , 'manuals_articles' , 0 , 'middle' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_articles' , 'manuals_search' , 0 , 'left' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_articles' , 'middle_column_hide' , NULL , '0' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_articles' , 'middle_column_width' , NULL , '75%' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_articles' , 'right_column_hide' , NULL , '1' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_articles' , 'right_column_width' , NULL , '0%' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_list' , 'left_column_hide' , NULL , '0' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_list' , 'left_column_width' , NULL , '25%' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_list' , 'manuals_list' , 0 , 'middle' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_list' , 'manuals_search' , 0 , 'left' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_list' , 'middle_column_hide' , NULL , '0' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_list' , 'middle_column_width' , NULL , '75%' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_list' , 'right_column_hide' , NULL , '1' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_search' , 'left_column_hide' , NULL , '0' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_search' , 'left_column_width' , NULL , '25%' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_search' , 'manuals_search' , 0 , 'left' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_search' , 'manuals_search_results' , 0 , 'middle' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_search' , 'middle_column_hide' , NULL , '0' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_search' , 'middle_column_width' , NULL , '75%' )";
		$sqls[] = " INSERT INTO " . $table_prefix . "page_settings (layout_id,page_name,setting_name,setting_order,setting_value) VALUES (0 , 'manuals_search' , 'right_column_hide' , NULL , '1' )";

		// add new permission for admin
		$permissions = array("admins_groups", "admins_login", "add_admins", "update_admins", "remove_admins");
		$sql = " SELECT privilege_id FROM  " . $table_prefix . "admin_privileges_settings WHERE block_name='admin_users' AND permission=1 ";
		$db->query($sql);
		while ($db->next_record()) {
			$privilege_id = $db->f("privilege_id");
			for ($i = 0; $i < sizeof($permissions); $i++) {
				$sql  = " INSERT INTO " . $table_prefix . "admin_privileges_settings (privilege_id, block_name, permission) VALUES (";
				$sql .= $db->tosql($privilege_id, INTEGER) . ", '" . $permissions[$i] . "',1)";
				$sqls[] = $sql;
			}
		}

		// add new permission for customer
		$user_permissions = array("my_orders", "my_details", "my_support", "my_forum");
		$sql = " SELECT type_id FROM  " . $table_prefix . "user_types ";
		$db->query($sql);
		while ($db->next_record()) {
			$type_id = $db->f("type_id");
			for ($i = 0; $i < sizeof($user_permissions); $i++) {
				$sql  = " INSERT INTO " . $table_prefix . "user_types_settings (type_id, setting_name, setting_value) VALUES (";
				$sql .= $db->tosql($type_id, INTEGER) . ", '" . $user_permissions[$i] . "','1')";
				$sqls[] = $sql;
			}
		}
	}

	if (comp_vers("2.8.1", $current_db_version) == 1)
	{
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "bookmarks (";
		$mysql_sql .= "  `bookmark_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `title` VARCHAR(50) NOT NULL,";
		$mysql_sql .= "  `url` VARCHAR(255) NOT NULL,";
		$mysql_sql .= "  `admin_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `image_path` VARCHAR(255),";
		$mysql_sql .= "  `is_start_page` INT(11) default '0',";
		$mysql_sql .= "  `is_popup` INT(11) default '0',";
		$mysql_sql .= "  `notes` VARCHAR(255)";
		$mysql_sql .= "  ,PRIMARY KEY (bookmark_id)";
		$mysql_sql .= "  ,KEY admin_id (admin_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "bookmarks START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "bookmarks (";
		$postgre_sql .= "  bookmark_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "bookmarks'),";
		$postgre_sql .= "  title VARCHAR(50) NOT NULL,";
		$postgre_sql .= "  url VARCHAR(255) NOT NULL,";
		$postgre_sql .= "  admin_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  image_path VARCHAR(255),";
		$postgre_sql .= "  is_start_page INT4 default '0',";
		$postgre_sql .= "  is_popup INT4 default '0',";
		$postgre_sql .= "  notes VARCHAR(255)";
		$postgre_sql .= "  ,PRIMARY KEY (bookmark_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "bookmarks (";
		$access_sql .= "  [bookmark_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [title] VARCHAR(50),";
		$access_sql .= "  [url] VARCHAR(255),";
		$access_sql .= "  [admin_id] INTEGER,";
		$access_sql .= "  [image_path] VARCHAR(255),";
		$access_sql .= "  [is_start_page] INTEGER,";
		$access_sql .= "  [is_popup] INTEGER,";
		$access_sql .= "  [notes] VARCHAR(255)";
		$access_sql .= "  ,PRIMARY KEY (bookmark_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "bookmarks_admin_id ON " . $table_prefix . "bookmarks (admin_id)";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN total_views INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN total_views INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN total_views INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN total_views INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN total_views INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN total_views INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN total_views INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN total_views INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN total_views INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN total_views INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN total_views INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN total_views INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN total_views INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN total_views INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN total_views INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "INSERT INTO " . $table_prefix . "issue_numbers (issue_number) VALUES (0)";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN tax_free INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN tax_free INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN tax_free INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN tax_free INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN tax_free INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN tax_free INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "mysql") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_items_properties MODIFY COLUMN property_value TEXT DEFAULT NULL ";
		} elseif ($db_type == "access") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_items_properties ALTER COLUMN property_value LONGTEXT ";
		}

		if ($db_type == "mysql") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders MODIFY COLUMN country_code VARCHAR(8) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders MODIFY COLUMN delivery_country_code VARCHAR(8) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users MODIFY COLUMN country_code VARCHAR(8) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users MODIFY COLUMN delivery_country_code VARCHAR(8) ";
		} elseif ($db_type == "access") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders ALTER COLUMN country_code VARCHAR(8) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders ALTER COLUMN delivery_country_code VARCHAR(8) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users ALTER COLUMN country_code VARCHAR(8) ";
			$sqls[] = "ALTER TABLE " . $table_prefix . "users ALTER COLUMN delivery_country_code VARCHAR(8) ";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_rules ADD COLUMN is_country_restriction INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_rules ADD COLUMN is_country_restriction INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_rules ADD COLUMN is_country_restriction INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "shipping_rules_countries (";
		$mysql_sql .= "  `shipping_rule_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `country_code` VARCHAR(4)";
		$mysql_sql .= "  ,KEY country_code (country_code)";
		$mysql_sql .= "  ,KEY shipping_rule_id (shipping_rule_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "shipping_rules_countries (";
		$postgre_sql .= "  shipping_rule_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  country_code VARCHAR(4))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "shipping_rules_countries (";
		$access_sql .= "  [shipping_rule_id] INTEGER,";
		$access_sql .= "  [country_code] VARCHAR(4))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "shipping_rules_countries_34 ON " . $table_prefix . "shipping_rules_countries (country_code)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "shipping_rules_countries_35 ON " . $table_prefix . "shipping_rules_countries (shipping_rule_id)";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN recurring_method INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN recurring_method INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN recurring_method INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_recurring INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_recurring INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_recurring INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_period INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_period INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_period INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_payments_total INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_payments_total INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_payments_total INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN parent_order_item_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN parent_order_item_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN parent_order_item_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_recurring INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_recurring INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_recurring INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_period INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_period INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_period INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_payments_total INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_payments_total INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_payments_total INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_last_payment DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_last_payment TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_last_payment DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_next_payment DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_next_payment TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_next_payment DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_recurring INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_recurring INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_recurring INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN parent_order_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN parent_order_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN parent_order_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];


		// add new permission for admin
		$permissions = array("product_types", "manufacturers", "shipping_methods", "shipping_times", "shipping_rules");
		$sql = " SELECT privilege_id FROM  " . $table_prefix . "admin_privileges_settings WHERE block_name='static_tables' AND permission=1 ";
		$db->query($sql);
		while ($db->next_record()) {
			$privilege_id = $db->f("privilege_id");
			for ($i = 0; $i < sizeof($permissions); $i++) {
				$sql  = " INSERT INTO " . $table_prefix . "admin_privileges_settings (privilege_id, block_name, permission) VALUES (";
				$sql .= $db->tosql($privilege_id, INTEGER) . ", '" . $permissions[$i] . "',1)";
				$sqls[] = $sql;
			}
		}
	}

	if (comp_vers("2.8.2", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN percentage_price DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN percentage_price FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN percentage_price FLOAT"
		);
		$sqls[] = $sql_types[$db_type];
	}

	if (comp_vers("2.8.3", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN thread_updated DATETIME default '0000-00-00 00:00:00' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN thread_updated TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "forum ADD COLUMN thread_updated DATETIME "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "forum SET thread_updated=date_modified ";
	}


	if (comp_vers("2.8.4", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN author_id INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN author_id INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN author_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "authors (";
		$mysql_sql .= "  `author_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `author_name` VARCHAR(255),";
		$mysql_sql .= "  `friendly_url` VARCHAR(255),";
		$mysql_sql .= "  `short_description` TEXT,";
		$mysql_sql .= "  `full_description` TEXT";
		$mysql_sql .= "  ,KEY friendly_url (friendly_url)";
		$mysql_sql .= "  ,PRIMARY KEY (author_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "authors START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "authors (";
		$postgre_sql .= "  author_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "authors'),";
		$postgre_sql .= "  author_name VARCHAR(255),";
		$postgre_sql .= "  friendly_url VARCHAR(255),";
		$postgre_sql .= "  short_description TEXT,";
		$postgre_sql .= "  full_description TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (author_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "authors (";
		$access_sql .= "  [author_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [author_name] VARCHAR(255),";
		$access_sql .= "  [friendly_url] VARCHAR(255),";
		$access_sql .= "  [short_description] LONGTEXT,";
		$access_sql .= "  [full_description] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (author_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "authors_friendly_url ON " . $table_prefix . "authors (friendly_url)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "saved_carts (";
		$mysql_sql .= "  `cart_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `user_id` INT(11) default '0',";
		$mysql_sql .= "  `cart_name` VARCHAR(255),";
		$mysql_sql .= "  `cart_total` DOUBLE(16,2) default '0',";
		$mysql_sql .= "  `cart_added` DATETIME";
		$mysql_sql .= "  ,PRIMARY KEY (cart_id)";
		$mysql_sql .= "  ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "saved_carts START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "saved_carts (";
		$postgre_sql .= "  cart_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "saved_carts'),";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  cart_name VARCHAR(255),";
		$postgre_sql .= "  cart_total FLOAT4 default '0',";
		$postgre_sql .= "  cart_added TIMESTAMP";
		$postgre_sql .= "  ,PRIMARY KEY (cart_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "saved_carts (";
		$access_sql .= "  [cart_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [cart_name] VARCHAR(255),";
		$access_sql .= "  [cart_total] FLOAT,";
		$access_sql .= "  [cart_added] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (cart_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "saved_carts_user_id ON " . $table_prefix . "saved_carts (user_id)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "saved_items (";
		$mysql_sql .= "  `cart_item_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `item_id` INT(11) default '0',";
		$mysql_sql .= "  `cart_id` INT(11) default '0',";
		$mysql_sql .= "  `user_id` INT(11) default '0',";
		$mysql_sql .= "  `item_name` VARCHAR(255),";
		$mysql_sql .= "  `quantity` INT(11) default '0',";
		$mysql_sql .= "  `price` INT(11) default '0',";
		$mysql_sql .= "  `date_added` DATETIME";
		$mysql_sql .= "  ,KEY cart_id (cart_id)";
		$mysql_sql .= "  ,KEY item_id (item_id)";
		$mysql_sql .= "  ,PRIMARY KEY (cart_item_id)";
		$mysql_sql .= "  ,KEY user_id (user_id))";
		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "saved_items START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "saved_items (";
		$postgre_sql .= "  cart_item_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "saved_items'),";
		$postgre_sql .= "  item_id INT4 default '0',";
		$postgre_sql .= "  cart_id INT4 default '0',";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  item_name VARCHAR(255),";
		$postgre_sql .= "  quantity INT4 default '0',";
		$postgre_sql .= "  price INT4 default '0',";
		$postgre_sql .= "  date_added TIMESTAMP";
		$postgre_sql .= "  ,PRIMARY KEY (cart_item_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "saved_items (";
		$access_sql .= "  [cart_item_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [item_id] INTEGER,";
		$access_sql .= "  [cart_id] INTEGER,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [item_name] VARCHAR(255),";
		$access_sql .= "  [quantity] INTEGER,";
		$access_sql .= "  [price] INTEGER,";
		$access_sql .= "  [date_added] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (cart_item_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "saved_items_cart_id ON " . $table_prefix . "saved_items (cart_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "saved_items_item_id ON " . $table_prefix . "saved_items (item_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "saved_items_user_id ON " . $table_prefix . "saved_items (user_id)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "saved_items_properties (";
		$mysql_sql .= "  `item_property_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `cart_item_id` INT(11) default '0',";
		$mysql_sql .= "  `cart_id` INT(11) default '0',";
		$mysql_sql .= "  `property_id` INT(11) default '0',";
		$mysql_sql .= "  `property_value` TEXT,";
		$mysql_sql .= "  `property_values_ids` TEXT";
		$mysql_sql .= "  ,KEY bakset_id (cart_item_id)";
		$mysql_sql .= "  ,KEY bakset_id1 (cart_id)";
		$mysql_sql .= "  ,PRIMARY KEY (item_property_id)";
		$mysql_sql .= "  ,KEY property_id (property_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "saved_items_properties START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "saved_items_properties (";
		$postgre_sql .= "  item_property_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "saved_items_properties'),";
		$postgre_sql .= "  cart_item_id INT4 default '0',";
		$postgre_sql .= "  cart_id INT4 default '0',";
		$postgre_sql .= "  property_id INT4 default '0',";
		$postgre_sql .= "  property_value TEXT,";
		$postgre_sql .= "  property_values_ids TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (item_property_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "saved_items_properties (";
		$access_sql .= "  [item_property_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [cart_item_id] INTEGER,";
		$access_sql .= "  [cart_id] INTEGER,";
		$access_sql .= "  [property_id] INTEGER,";
		$access_sql .= "  [property_value] LONGTEXT,";
		$access_sql .= "  [property_values_ids] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (item_property_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "saved_items_properties_b_33 ON " . $table_prefix . "saved_items_properties (cart_item_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "saved_items_properties_b_34 ON " . $table_prefix . "saved_items_properties (cart_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "saved_items_properties_p_35 ON " . $table_prefix . "saved_items_properties (property_id)";
		}


		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_interval INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_interval INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_interval INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_start_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_start_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_start_date DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_end_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_end_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_end_date DATETIME "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_interval INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_interval INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_interval INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_payments_made INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_payments_made INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_payments_made INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_end_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_end_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_end_date DATETIME "
		);
		$sqls[] = $sql_types[$db_type];


	}

	if (comp_vers("2.8.5", $current_db_version) == 1)
	{
		// begin quotes
		$mysql_sql  = "  CREATE TABLE " . $table_prefix . "quotes (";
		$mysql_sql .= "  `quote_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `user_id` INT(11) default '0',";
		$mysql_sql .= "  `user_name` VARCHAR(255),";
		$mysql_sql .= "  `user_email` VARCHAR(255),";
		$mysql_sql .= "  `order_id` INT(11) default '0',";
		$mysql_sql .= "  `quoted_price` DOUBLE(16,2) default '0',";
		$mysql_sql .= "  `quote_status_id` INT(11) default '0',";
		$mysql_sql .= "  `quoted_by_admin_id` INT(11) default '0',";
		$mysql_sql .= "  `is_paid` INT(11) default '0',";
		$mysql_sql .= "  `is_closed` INT(11) default '0',";
		$mysql_sql .= "  `date_added` DATETIME,";
		$mysql_sql .= "  `date_due` DATETIME,";
		$mysql_sql .= "  `request_summary` TEXT,";
		$mysql_sql .= "  `request_description` TEXT";
		$mysql_sql .= "  ,PRIMARY KEY (quote_id)";
		$mysql_sql .= "  ,KEY is_paid (is_paid)";
		$mysql_sql .= "  ,KEY order_id (order_id)";
		$mysql_sql .= "  ,KEY quote_status_id (quote_status_id)";
		$mysql_sql .= "  ,KEY quoted_by_admin_id (quoted_by_admin_id)";
		$mysql_sql .= "  ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "quotes START 1";
		}

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "quotes (";
		$postgre_sql .= "  quote_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "quotes'),";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  user_name VARCHAR(255),";
		$postgre_sql .= "  user_email VARCHAR(255),";
		$postgre_sql .= "  order_id INT4 default '0',";
		$postgre_sql .= "  quoted_price FLOAT4 default '0',";
		$postgre_sql .= "  quote_status_id INT4 default '0',";
		$postgre_sql .= "  quoted_by_admin_id INT4 default '0',";
		$postgre_sql .= "  is_paid INT4 default '0',";
		$postgre_sql .= "  is_closed INT4 default '0',";
		$postgre_sql .= "  date_added TIMESTAMP,";
		$postgre_sql .= "  date_due TIMESTAMP,";
		$postgre_sql .= "  request_summary TEXT,";
		$postgre_sql .= "  request_description TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (quote_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "quotes (";
		$access_sql .= "  [quote_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [user_name] VARCHAR(255),";
		$access_sql .= "  [user_email] VARCHAR(255),";
		$access_sql .= "  [order_id] INTEGER,";
		$access_sql .= "  [quoted_price] FLOAT,";
		$access_sql .= "  [quote_status_id] INTEGER,";
		$access_sql .= "  [quoted_by_admin_id] INTEGER,";
		$access_sql .= "  [is_paid] INTEGER,";
		$access_sql .= "  [is_closed] INTEGER,";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [date_due] DATETIME,";
		$access_sql .= "  [request_summary] LONGTEXT,";
		$access_sql .= "  [request_description] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (quote_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_is_paid ON " . $table_prefix . "quotes (is_paid)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_order_id ON " . $table_prefix . "quotes (order_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_quote_status_id ON " . $table_prefix . "quotes (quote_status_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_quoted_by_admin_id ON " . $table_prefix . "quotes (quoted_by_admin_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_user_id ON " . $table_prefix . "quotes (user_id)";
		}
		// end quotes

		// begin quotes_features
		$mysql_sql  = "  CREATE TABLE " . $table_prefix . "quotes_features (";
		$mysql_sql .= "  `feature_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `quote_id` INT(11) default '0',";
		$mysql_sql .= "  `feature_description` TEXT,";
		$mysql_sql .= "  `price` DOUBLE(16,2) default '0',";
		$mysql_sql .= "  `date_due` DATETIME";
		$mysql_sql .= "  ,PRIMARY KEY (feature_id)";
		$mysql_sql .= "  ,KEY quote_id (quote_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "quotes_features START 1";
		}

		$postgre_sql  = "  CREATE TABLE " . $table_prefix . "quotes_features (";
		$postgre_sql .= " feature_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "quotes_features'),";
		$postgre_sql .= " quote_id INT4 default '0',";
		$postgre_sql .= " feature_description TEXT,";
		$postgre_sql .= " price FLOAT4 default '0',";
		$postgre_sql .= " date_due TIMESTAMP";
		$postgre_sql .= " ,PRIMARY KEY (feature_id))";

		$access_sql  = "  CREATE TABLE " . $table_prefix . "quotes_features (";
		$access_sql .= " [feature_id]  COUNTER  NOT NULL,";
		$access_sql .= " [quote_id] INTEGER,";
		$access_sql .= " [feature_description] LONGTEXT,";
		$access_sql .= " [price] FLOAT,";
		$access_sql .= " [date_due] DATETIME";
		$access_sql .= " ,PRIMARY KEY (feature_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_features_quote_id ON " . $table_prefix . "quotes_features (quote_id)";
		}
		// end quotes_features

		// begin quotes_history
		$mysql_sql  = " CREATE TABLE " . $table_prefix . "quotes_history (";
		$mysql_sql .= " `quote_id` INT(11) default '0',";
		$mysql_sql .= " `quote_status_id_old` INT(11) default '0',";
		$mysql_sql .= " `quote_status_id_new` INT(11) default '0',";
		$mysql_sql .= " `action` VARCHAR(255),";
		$mysql_sql .= " `date_added` DATETIME,";
		$mysql_sql .= " `admin_id` INT(11) default '0'";
		$mysql_sql .= " ,KEY admin_id (admin_id)";
		$mysql_sql .= " ,KEY quote_id (quote_id))";

		$postgre_sql  = "  CREATE TABLE " . $table_prefix . "quotes_history (";
		$postgre_sql .= " quote_id INT4 default '0',";
		$postgre_sql .= " quote_status_id_old INT4 default '0',";
		$postgre_sql .= " quote_status_id_new INT4 default '0',";
		$postgre_sql .= " action VARCHAR(255),";
		$postgre_sql .= " date_added TIMESTAMP,";
		$postgre_sql .= " admin_id INT4 default '0')";

		$access_sql  = "  CREATE TABLE " . $table_prefix . "quotes_history (";
		$access_sql .= " [quote_id] INTEGER,";
		$access_sql .= " [quote_status_id_old] INTEGER,";
		$access_sql .= " [quote_status_id_new] INTEGER,";
		$access_sql .= " [action] VARCHAR(255),";
		$access_sql .= " [date_added] DATETIME,";
		$access_sql .= " [admin_id] INTEGER)";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_history_admin_id ON " . $table_prefix . "quotes_history (admin_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_history_quote_id ON " . $table_prefix . "quotes_history (quote_id)";
		}
		// end quotes_history

		// begin quotes_statuses
		$mysql_sql  = "  CREATE TABLE ". $table_prefix . "quotes_statuses (";
		$mysql_sql .= "  `quote_status_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `quote_status` VARCHAR(255),";
		$mysql_sql .= "  `start_tag` VARCHAR(255),";
		$mysql_sql .= "  `end_tag` VARCHAR(255),";
		$mysql_sql .= "  `image_path` VARCHAR(255),";
		$mysql_sql .= "  `notes` TEXT";
		$mysql_sql .= "  ,PRIMARY KEY (quote_status_id)";
		$mysql_sql .= "  ,KEY quote_status_id (quote_status_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "quotes_statuses START 1";
		}

		$postgre_sql  = "  CREATE TABLE ". $table_prefix . "quotes_statuses (";
		$postgre_sql .= " quote_status_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "quotes_statuses'),";
		$postgre_sql .= " quote_status VARCHAR(255),";
		$postgre_sql .= " start_tag VARCHAR(255),";
		$postgre_sql .= " end_tag VARCHAR(255),";
		$postgre_sql .= " image_path VARCHAR(255),";
		$postgre_sql .= " notes TEXT";
		$postgre_sql .= " ,PRIMARY KEY (quote_status_id))";

		$access_sql  = "  CREATE TABLE ". $table_prefix . "quotes_statuses (";
		$access_sql .= " [quote_status_id]  COUNTER  NOT NULL,";
		$access_sql .= " [quote_status] VARCHAR(255),";
		$access_sql .= " [start_tag] VARCHAR(255),";
		$access_sql .= " [end_tag] VARCHAR(255),";
		$access_sql .= " [image_path] VARCHAR(255),";
		$access_sql .= " [notes] LONGTEXT";
		$access_sql .= " ,PRIMARY KEY (quote_status_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_statuses_quote_st_33 ON " . $table_prefix . "quotes_statuses (quote_status_id)";
		}

		$sqls[] = " INSERT INTO " . $table_prefix . "quotes_statuses (quote_status_id, quote_status) VALUES ('1', 'New') ";
		$sqls[] = " INSERT INTO " . $table_prefix . "quotes_statuses (quote_status_id, quote_status) VALUES ('2', 'Quoted') ";
		$sqls[] = " INSERT INTO " . $table_prefix . "quotes_statuses (quote_status_id, quote_status) VALUES ('3', 'Paid') ";
		$sqls[] = " INSERT INTO " . $table_prefix . "quotes_statuses (quote_status_id, quote_status) VALUES ('4', 'In progress') ";
		// end quotes_statuses

		// begin Call Center fields
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN is_call_center INT(11) default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN is_call_center INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN is_call_center INTEGER"
		);
		$sqls[] = $sql_types[$db_type];
		// end Call Center fields

		// recurring fields
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_payments_failed INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_payments_failed INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_payments_failed INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_plan_payment DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_plan_payment TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_plan_payment DATETIME "
		);
		$sqls[] = $sql_types[$db_type];
	}

	if (comp_vers("2.8.7", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN paid_status INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN paid_status INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN paid_status INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql  = " UPDATE " . $table_prefix . "order_statuses SET paid_status=1 WHERE status_name LIKE '%Paid%' ";
		$sql .= " OR status_name LIKE '%shipped%' OR status_name LIKE '%Closed%' ";
		$sql .= " OR status_name LIKE '%Validated%' OR status_name LIKE '%Dispatched%' ";
		$sqls[] = $sql;
	}

	if (is_array($sqls) && sizeof($sqls) > 0) {
		run_queries($sqls, $queries_success, $queries_failed, $errors, "2.9");
	}

?>