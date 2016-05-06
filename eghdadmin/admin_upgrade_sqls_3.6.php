<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_upgrade_sqls_3.6.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	check_admin_security("system_upgrade");

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	if (comp_vers("3.5.1", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN is_active TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN is_active SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN is_active BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN is_active SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.1");
	}

	if (comp_vers("3.5.2", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN shipping_tax_percent DOUBLE(16,3) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN shipping_tax_percent FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN shipping_tax_percent FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "tax_rates ADD COLUMN shipping_tax_percent DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "orders_taxes (
      `order_tax_id` INT(11) NOT NULL AUTO_INCREMENT,
      `order_id` INT(11) default '0',
      `tax_id` INT(11) default '0',
      `tax_name` VARCHAR(50),
      `tax_percent` DOUBLE(16,3) default '0',
      `shipping_tax_percent` DOUBLE(16,3)
      ,KEY order_id (order_id)
      ,PRIMARY KEY (order_tax_id)
      ,KEY tax_id (tax_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "orders_taxes START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "orders_taxes (
      order_tax_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "orders_taxes'),
      order_id INT4 default '0',
      tax_id INT4 default '0',
      tax_name VARCHAR(50),
      tax_percent FLOAT4 default '0',
      shipping_tax_percent FLOAT4
      ,PRIMARY KEY (order_tax_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "orders_taxes (
      [order_tax_id]  COUNTER  NOT NULL,
      [order_id] INTEGER,
      [tax_id] INTEGER,
      [tax_name] VARCHAR(50),
      [tax_percent] FLOAT,
      [shipping_tax_percent] FLOAT
      ,PRIMARY KEY (order_tax_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "orders_taxes (
      order_tax_id INTEGER NOT NULL,
      order_id INTEGER default 0,
      tax_id INTEGER default 0,
      tax_name VARCHAR(50),
      tax_percent DOUBLE default 0,
      shipping_tax_percent DOUBLE
      ,PRIMARY KEY (order_tax_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_taxes_order_id ON " . $table_prefix . "orders_taxes (order_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_taxes_tax_id ON " . $table_prefix . "orders_taxes (tax_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "orders_taxes AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "orders_taxes NO CASCADE BEFORE INSERT ON " . $table_prefix . "orders_taxes REFERENCING NEW AS newr_" . $table_prefix . "orders_taxes FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "orders_taxes.order_tax_id IS NULL ) begin atomic set newr_" . $table_prefix . "orders_taxes.order_tax_id = nextval for seq_" . $table_prefix . "orders_taxes; end";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "orders_items_taxes (
      `tax_item_id` INT(11) NOT NULL AUTO_INCREMENT,
      `order_tax_id` INT(11) default '0',
      `item_type_id` INT(11) default '0',
      `tax_percent` DOUBLE(16,3)
      ,KEY item_type_id (item_type_id)
      ,KEY order_tax_id (order_tax_id)
      ,PRIMARY KEY (tax_item_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "orders_items_taxes START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "orders_items_taxes (
      tax_item_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "orders_items_taxes'),
      order_tax_id INT4 default '0',
      item_type_id INT4 default '0',
      tax_percent FLOAT4
      ,PRIMARY KEY (tax_item_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "orders_items_taxes (
      [tax_item_id]  COUNTER  NOT NULL,
      [order_tax_id] INTEGER,
      [item_type_id] INTEGER,
      [tax_percent] FLOAT
      ,PRIMARY KEY (tax_item_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "orders_items_taxes (
        tax_item_id INTEGER NOT NULL,
        order_tax_id INTEGER default 0,
        item_type_id INTEGER default 0,
        tax_percent DOUBLE
        ,PRIMARY KEY (tax_item_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_taxes_item__43 ON " . $table_prefix . "orders_items_taxes (item_type_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_taxes_order_44 ON " . $table_prefix . "orders_items_taxes (order_tax_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "orders_items_taxes AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "orders_i_75 NO CASCADE BEFORE INSERT ON " . $table_prefix . "orders_items_taxes REFERENCING NEW AS newr_" . $table_prefix . "orders_items_taxes FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "orders_items_taxes.tax_item_id IS NULL ) begin atomic set newr_" . $table_prefix . "orders_items_taxes.tax_item_id = nextval for seq_" . $table_prefix . "orders_items_taxes; end";
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.2");
	}

	if (comp_vers("3.5.3", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_reminder_send TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_reminder_send SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_reminder_send BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN is_reminder_send SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN reminder_send_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN reminder_send_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN reminder_send_date DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN reminder_send_date TIMESTAMP ",
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.3");
	}

	if (comp_vers("3.5.4", $current_db_version) == 1)
	{
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "registration_categories (
      `category_id` INT(11) NOT NULL AUTO_INCREMENT,
      `parent_category_id` INT(11) default '0',
      `category_order` INT(11) default '1',
      `category_path` VARCHAR(255),
      `category_name` VARCHAR(255),
      `show_for_user` TINYINT NOT NULL default '1',
      `admin_id_added_by` INT(11) default '0',
      `admin_id_modified_by` INT(11) default '0',
      `date_added` DATETIME,
      `date_modified` DATETIME
      ,KEY category_order (category_order)
      ,KEY category_path (category_path)
      ,KEY parent_category_id (parent_category_id)
      ,PRIMARY KEY (category_id)
      ,KEY show_for_user (show_for_user))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "registration_categories START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "registration_categories (
      category_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "registration_categories'),
      parent_category_id INT4 default '0',
      category_order INT4 default '1',
      category_path VARCHAR(255),
      category_name VARCHAR(255),
      show_for_user SMALLINT NOT NULL default '1',
      admin_id_added_by INT4 default '0',
      admin_id_modified_by INT4 default '0',
      date_added TIMESTAMP,
      date_modified TIMESTAMP
      ,PRIMARY KEY (category_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "registration_categories (
      [category_id]  COUNTER  NOT NULL,
      [parent_category_id] INTEGER,
      [category_order] INTEGER,
      [category_path] VARCHAR(255),
      [category_name] VARCHAR(255),
      [show_for_user] BYTE,
      [admin_id_added_by] INTEGER,
      [admin_id_modified_by] INTEGER,
      [date_added] DATETIME,
      [date_modified] DATETIME
      ,PRIMARY KEY (category_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "registration_categories (
      category_id INTEGER NOT NULL,
      parent_category_id INTEGER default 0,
      category_order INTEGER default 1,
      category_path VARCHAR(255),
      category_name VARCHAR(255),
      show_for_user SMALLINT NOT NULL default 1,
      admin_id_added_by INTEGER default 0,
      admin_id_modified_by INTEGER default 0,
      date_added TIMESTAMP,
      date_modified TIMESTAMP
      ,PRIMARY KEY (category_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_categories__50 ON " . $table_prefix . "registration_categories (category_order)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_categories__51 ON " . $table_prefix . "registration_categories (category_path)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_categories__52 ON " . $table_prefix . "registration_categories (parent_category_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_categories__53 ON " . $table_prefix . "registration_categories (show_for_user)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "registration_categories AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "registra_88 NO CASCADE BEFORE INSERT ON " . $table_prefix . "registration_categories REFERENCING NEW AS newr_" . $table_prefix . "registration_categories FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "registration_categories.category_id IS NULL ) begin atomic set newr_" . $table_prefix . "registration_categories.category_id = nextval for seq_" . $table_prefix . "registration_categories; end";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "registration_items (
      `item_id` INT(11) NOT NULL AUTO_INCREMENT,
      `item_code` VARCHAR(64),
      `item_name` VARCHAR(255),
      `item_order` INT(11) default '1',
      `show_for_user` TINYINT default '1',
      `admin_id_added_by` INT(11) default '0',
      `admin_id_modified_by` INT(11) default '0',
      `date_added` DATETIME,
      `date_modified` DATETIME
      ,KEY item_code (item_code)
      ,KEY item_order (item_order)
      ,PRIMARY KEY (item_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "registration_items START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "registration_items (
      item_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "registration_items'),
      item_code VARCHAR(64),
      item_name VARCHAR(255),
      item_order INT4 default '1',
      show_for_user SMALLINT default '1',
      admin_id_added_by INT4 default '0',
      admin_id_modified_by INT4 default '0',
      date_added TIMESTAMP,
      date_modified TIMESTAMP
      ,PRIMARY KEY (item_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "registration_items (
      [item_id]  COUNTER  NOT NULL,
      [item_code] VARCHAR(64),
      [item_name] VARCHAR(255),
      [item_order] INTEGER,
      [show_for_user] BYTE,
      [admin_id_added_by] INTEGER,
      [admin_id_modified_by] INTEGER,
      [date_added] DATETIME,
      [date_modified] DATETIME
      ,PRIMARY KEY (item_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "registration_items (
      item_id INTEGER NOT NULL,
      item_code VARCHAR(64),
      item_name VARCHAR(255),
      item_order INTEGER default 1,
      show_for_user SMALLINT default 1,
      admin_id_added_by INTEGER default 0,
      admin_id_modified_by INTEGER default 0,
      date_added TIMESTAMP,
      date_modified TIMESTAMP
      ,PRIMARY KEY (item_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_items_item_code ON " . $table_prefix . "registration_items (item_code)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_items_item_order ON " . $table_prefix . "registration_items (item_order)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "registration_items AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "registra_89 NO CASCADE BEFORE INSERT ON " . $table_prefix . "registration_items REFERENCING NEW AS newr_" . $table_prefix . "registration_items FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "registration_items.item_id IS NULL ) begin atomic set newr_" . $table_prefix . "registration_items.item_id = nextval for seq_" . $table_prefix . "registration_items; end";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "registration_items_assigned (
      `item_id` INT(11) NOT NULL default '0',
      `category_id` INT(11) NOT NULL default '0'
      ,PRIMARY KEY (item_id,category_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "registration_items_assigned (
      item_id INT4 NOT NULL default '0',
      category_id INT4 NOT NULL default '0'
      ,PRIMARY KEY (item_id,category_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "registration_items_assigned (
      [item_id] INTEGER NOT NULL,
      [category_id] INTEGER NOT NULL
      ,PRIMARY KEY (item_id,category_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "registration_items_assigned (
      item_id INTEGER NOT NULL default 0,
      category_id INTEGER NOT NULL default 0
      ,PRIMARY KEY (item_id,category_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "registration_list (
      `registration_id` INT(11) NOT NULL AUTO_INCREMENT,
      `user_id` INT(11) default '0',
      `is_approved` TINYINT default '0',
      `category_id` INT(11) default '0',
      `item_id` INT(11) default '0',
      `item_code` VARCHAR(64),
      `item_name` VARCHAR(255),
      `serial_number` VARCHAR(128),
      `invoice_number` VARCHAR(128),
      `store_name` VARCHAR(128),
      `purchased_day` TINYINT default '0',
      `purchased_month` TINYINT default '0',
      `purchased_year` INT(11) default '0',
      `admin_id_added_by` INT(11) default '0',
      `admin_id_modified_by` INT(11) default '0',
      `date_added` DATETIME,
      `date_modified` DATETIME
      ,KEY category_id (category_id)
      ,KEY invoice_number (invoice_number)
      ,KEY item_code (item_code)
      ,KEY item_id (item_id)
      ,PRIMARY KEY (registration_id)
      ,KEY purchased_day (purchased_day)
      ,KEY purchased_month (purchased_month)
      ,KEY purchased_year (purchased_year)
      ,KEY serial_number (serial_number)
      ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "registration_list START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "registration_list (
      registration_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "registration_list'),
      user_id INT4 default '0',
      is_approved SMALLINT default '0',
      category_id INT4 default '0',
      item_id INT4 default '0',
      item_code VARCHAR(64),
      item_name VARCHAR(255),
      serial_number VARCHAR(128),
      invoice_number VARCHAR(128),
      store_name VARCHAR(128),
      purchased_day SMALLINT default '0',
      purchased_month SMALLINT default '0',
      purchased_year INT4 default '0',
      admin_id_added_by INT4 default '0',
      admin_id_modified_by INT4 default '0',
      date_added TIMESTAMP,
      date_modified TIMESTAMP
      ,PRIMARY KEY (registration_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "registration_list (
      [registration_id] COUNTER NOT NULL,
      [user_id] INTEGER,
      [is_approved] BYTE,
      [category_id] INTEGER,
      [item_id] INTEGER,
      [item_code] VARCHAR(64),
      [item_name] VARCHAR(255),
      [serial_number] VARCHAR(128),
      [invoice_number] VARCHAR(128),
      [store_name] VARCHAR(128),
      [purchased_day] BYTE,
      [purchased_month] BYTE,
      [purchased_year] INTEGER,
      [admin_id_added_by] INTEGER,
      [admin_id_modified_by] INTEGER,
      [date_added] DATETIME,
      [date_modified] DATETIME
      ,PRIMARY KEY (registration_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "registration_list (
      registration_id INTEGER NOT NULL,
      user_id INTEGER default 0,
      is_approved SMALLINT default 0,
      category_id INTEGER default 0,
      item_id INTEGER default 0,
      item_code VARCHAR(64),
      item_name VARCHAR(255),
      serial_number VARCHAR(128),
      invoice_number VARCHAR(128),
      store_name VARCHAR(128),
      purchased_day SMALLINT default 0,
      purchased_month SMALLINT default 0,
      purchased_year INTEGER default 0,
      admin_id_added_by INTEGER default 0,
      admin_id_modified_by INTEGER default 0,
      date_added TIMESTAMP,
      date_modified TIMESTAMP
      ,PRIMARY KEY (registration_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_list_category_id ON " . $table_prefix . "registration_list (category_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_list_invoic_54 ON " . $table_prefix . "registration_list (invoice_number)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_list_item_code ON " . $table_prefix . "registration_list (item_code)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_list_item_id ON " . $table_prefix . "registration_list (item_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_list_purcha_55 ON " . $table_prefix . "registration_list (purchased_day)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_list_purcha_56 ON " . $table_prefix . "registration_list (purchased_month)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_list_purcha_57 ON " . $table_prefix . "registration_list (purchased_year)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_list_serial_58 ON " . $table_prefix . "registration_list (serial_number)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_list_user_id ON " . $table_prefix . "registration_list (user_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "registration_list AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "registra_91 NO CASCADE BEFORE INSERT ON " . $table_prefix . "registration_list REFERENCING NEW AS newr_" . $table_prefix . "registration_list FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "registration_list.registration_id IS NULL ) begin atomic set newr_" . $table_prefix . "registration_list.registration_id = nextval for seq_" . $table_prefix . "registration_list; end";
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.4");
	}

	if (comp_vers("3.5.5", $current_db_version) == 1)
	{
		$sqls[] = " UPDATE " . $table_prefix . "page_settings SET setting_name='basket_fast_checkout' WHERE setting_name='basket_google_checkout'";
		$sqls[] = " UPDATE " . $table_prefix . "page_settings SET setting_name='fast_checkout_country_show' WHERE setting_name='google_checkout_country_show'";
		$sqls[] = " UPDATE " . $table_prefix . "page_settings SET setting_name='fast_checkout_country_required' WHERE setting_name='google_checkout_country_required'";
		$sqls[] = " UPDATE " . $table_prefix . "page_settings SET setting_name='fast_checkout_state_show' WHERE setting_name='google_checkout_state_show'";
		$sqls[] = " UPDATE " . $table_prefix . "page_settings SET setting_name='fast_checkout_state_required' WHERE setting_name='google_checkout_state_required'";
		$sqls[] = " UPDATE " . $table_prefix . "page_settings SET setting_name='fast_checkout_postcode_show' WHERE setting_name='google_checkout_postcode_show'";
		$sqls[] = " UPDATE " . $table_prefix . "page_settings SET setting_name='fast_checkout_postcode_required' WHERE setting_name='google_checkout_postcode_required'";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_active TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_active SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_active BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_active SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_image VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_alt VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_width INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_width INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_width INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_width INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_height INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_height INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_height INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN fast_checkout_height INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		// update google checkout fast checkout parameters
		$sql  = " SELECT ps.payment_id FROM ". $table_prefix . "payment_systems ps";
		$sql .= " WHERE ps.payment_url LIKE '%google_process.php%'";
		$db->query($sql);
		if ($db->next_record()) {
			$payment_id = $db->f("payment_id");
			$sql  = " UPDATE " . $table_prefix . "payment_systems SET ";
			$sql .= " fast_checkout_active=1, ";
			$sql .= " fast_checkout_image='http://checkout.google.com/buttons/checkout.gif?merchant_id={merchant_id}&w=160&h=43&style=white&variant=text&loc=en_US', ";
			$sql .= " fast_checkout_width=160, ";
			$sql .= " fast_checkout_height=43, ";
			$sql .= " fast_checkout_alt='Google Checkout' ";
			$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
			$sqls[] = $sql;
		}

		// update paypal fast checkout parameters
		$sql  = " SELECT ps.payment_id FROM ". $table_prefix . "payment_systems ps";
		$sql .= " WHERE ps.payment_url LIKE '%paypal_checkout.php%'";
		$db->query($sql);
		if ($db->next_record()) {
			$payment_id = $db->f("payment_id");
			$sql  = " UPDATE " . $table_prefix . "payment_systems SET ";
			$sql .= " fast_checkout_active=1, ";
			$sql .= " fast_checkout_image='https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif', ";
			$sql .= " fast_checkout_width=145, ";
			$sql .= " fast_checkout_height=42, ";
			$sql .= " fast_checkout_alt='PayPal is the safer, easier way to pay.' ";
			$sql .= " WHERE payment_id=" . $db->tosql($payment_id, INTEGER);
			$sqls[] = $sql;
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.5");
	}

	if (comp_vers("3.5.6", $current_db_version) == 1)
	{
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders_events ADD COLUMN event_type VARCHAR(32) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_events MODIFY COLUMN event_name VARCHAR(255) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_events ALTER COLUMN event_name TYPE VARCHAR(255) ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_events ALTER COLUMN event_name VARCHAR(255) ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_events ALTER COLUMN event_name SET DATA TYPE VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "orders_events SET event_type='notification_sent' WHERE event_name='Notification sent' OR event_name=" . $db->tosql(NOTIFICATION_SENT_MSG, TEXT);
		$sqls[] = " UPDATE " . $table_prefix . "orders_events SET event_type='update_order' WHERE event_name='Update' OR event_name=" . $db->tosql(UPDATE_BUTTON, TEXT);
		$sqls[] = " UPDATE " . $table_prefix . "orders_events SET event_type='update_product' WHERE event_name='Update Product' OR event_name=" . $db->tosql(UPDATE_PRODUCT_MSG, TEXT);
		$sqls[] = " UPDATE " . $table_prefix . "orders_events SET event_type='email_sent' WHERE event_name='Send Email Message' OR event_name=" . $db->tosql(SEND_EMAIL_MESSAGE_MSG, TEXT);
		$sqls[] = " UPDATE " . $table_prefix . "orders_events SET event_type='sms_sent' WHERE event_name='Send SMS message' OR event_name=" . $db->tosql(SEND_SMS_MESSAGE_MSG, TEXT);
		$sqls[] = " UPDATE " . $table_prefix . "orders_events SET event_type='links_sent' WHERE event_name='Links sent' OR event_name=" . $db->tosql(LINKS_SENT_MSG, TEXT);
		$sqls[] = " UPDATE " . $table_prefix . "orders_events SET event_type='serials_sent' WHERE event_name='Serial Numbers sent' OR event_name=" . $db->tosql(SERIAL_NUMBERS_SENT_MSG, TEXT);
		$sqls[] = " UPDATE " . $table_prefix . "orders_events SET event_type='vouchers_sent' WHERE event_name='Gift Vouchers sent' OR event_name=" . $db->tosql(GIFT_VOUCHERS_SENT_MSG, TEXT);
		$sqls[] = " UPDATE " . $table_prefix . "orders_events SET event_type='activation_added' WHERE event_name='Activation added' OR event_name=" . $db->tosql(ACTIVATION_ADDED_MSG, TEXT);
		$sqls[] = " UPDATE " . $table_prefix . "orders_events SET event_type='activation_updated' WHERE event_name='Activation updated' OR event_name=" . $db->tosql(ACTIVATION_UPDATED_MSG, TEXT);
		$sqls[] = " UPDATE " . $table_prefix . "orders_events SET event_type='activation_removed' WHERE event_name='Activation removed' OR event_name=" . $db->tosql(ACTIVATION_REMOVED_MSG, TEXT);
		$sqls[] = " UPDATE " . $table_prefix . "orders_events SET event_name=event_description, event_description='' WHERE event_type IS NOT NULL AND event_type<>'' ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users MODIFY COLUMN login VARCHAR(64) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ALTER COLUMN login TYPE VARCHAR(64) ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ALTER COLUMN login VARCHAR(64) ",
			"db2"     => "ALTER TABLE " . $table_prefix . "users ALTER COLUMN login SET DATA TYPE VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.6");
	}

	if (comp_vers("3.5.7", $current_db_version) == 1)
	{
		if ($db_type == "mysql") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_items MODIFY COLUMN reward_credits DOUBLE(16,4) default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_items MODIFY COLUMN affiliate_commission DOUBLE(16,4) default '0'";
			$sqls[] = "ALTER TABLE " . $table_prefix . "orders_items MODIFY COLUMN merchant_commission DOUBLE(16,4) default '0'";
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_properties_price DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_properties_price FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_properties_price FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_properties_price DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_properties_price_id INT(11) default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_properties_price_id INT4 default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_properties_price_id INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN trade_properties_price_id INTEGER default 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "CREATE INDEX " . $table_prefix . "items_trade_properties_id ON " . $table_prefix . "items (trade_properties_price_id)";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.7");
	}

	if (comp_vers("3.5.8", $current_db_version) == 1)
	{
		$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type='global' AND setting_name='password_encrypt' ";
		$password_encrypt = get_db_value($sql);

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'global', 'admin_password_encrypt', ".$db->tosql($password_encrypt, TEXT).")";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN tax_round_type TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN tax_round_type SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN tax_round_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN tax_round_type SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN cc_encrypt_type TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN cc_encrypt_type SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN cc_encrypt_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN cc_encrypt_type SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "orders SET tax_round_type=1, cc_encrypt_type=1 ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.8");
	}

	if (comp_vers("3.5.9", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN users_all TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN users_all SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN users_all BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN users_all SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN users_use_limit INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN users_use_limit INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN users_use_limit INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN users_use_limit INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_period TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_period SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_period BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_period SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_interval INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_interval INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_interval INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_interval INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_min_goods DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_min_goods FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_min_goods FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_min_goods DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_max_goods DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_max_goods FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_max_goods FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN orders_max_goods DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_discount_type TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_discount_type SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_discount_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_discount_type SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_all TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_all SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_all BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_all SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_ids TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_ids TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_ids LONGTEXT",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_ids LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_period TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_period SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_period BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_period SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_interval INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_interval INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_interval INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_interval INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_min_goods DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_min_goods FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_min_goods FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_min_goods DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_max_goods DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_max_goods FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_max_goods FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_max_goods DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN start_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN start_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN start_date DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN start_date TIMESTAMP ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN admin_id_added_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN admin_id_added_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN admin_id_added_by INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN admin_id_added_by INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN admin_id_modified_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN admin_id_modified_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN admin_id_modified_by INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN admin_id_modified_by INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN date_added DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN date_added TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN date_added DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN date_added TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN date_modified DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN date_modified TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN date_modified DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN date_modified TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "coupons SET users_all=1, friends_discount_type=0, friends_all=1 ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.9");
	}


	if (comp_vers("3.5.10", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN apply_order INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN apply_order INT4 default '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN apply_order INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN apply_order INTEGER DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "coupons SET apply_order=1 ";

 		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN items_types_ids TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN items_types_ids TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN items_types_ids LONGTEXT",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN items_types_ids LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

 		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN cart_items_types_ids TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN cart_items_types_ids TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN cart_items_types_ids LONGTEXT",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN cart_items_types_ids LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

 		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN users_types_ids TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN users_types_ids TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN users_types_ids LONGTEXT",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN users_types_ids LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

 		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_types_ids TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_types_ids TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_types_ids LONGTEXT",
			"db2"     => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN friends_types_ids LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.10");
	}
	
	
	
	if(comp_vers("3.5.11", $current_db_version) == 1) {
		// custom fields for registration form
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "registration_custom_properties (";
		$mysql_sql .= "  `property_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `site_id` INT(11) default '1',";
		$mysql_sql .= "  `property_order` INT(11) default '0',";
		$mysql_sql .= "  `property_name` VARCHAR(255),";
		$mysql_sql .= "  `property_description` TEXT,";
		$mysql_sql .= "  `default_value` TEXT,";
		$mysql_sql .= "  `property_style` VARCHAR(255),";
		$mysql_sql .= "  `property_show` TINYINT default '0',";
		$mysql_sql .= "  `control_type` VARCHAR(16),";
		$mysql_sql .= "  `control_style` VARCHAR(255),";
		$mysql_sql .= "  `control_code` TEXT,";
		$mysql_sql .= "  `onchange_code` TEXT,";
		$mysql_sql .= "  `onclick_code` TEXT,";
		$mysql_sql .= "  `required` TINYINT default '0',";
		$mysql_sql .= "  `before_name_html` TEXT,";
		$mysql_sql .= "  `after_name_html` TEXT,";
		$mysql_sql .= "  `before_control_html` TEXT,";
		$mysql_sql .= "  `after_control_html` TEXT,";
		$mysql_sql .= "  `validation_regexp` TEXT,";
		$mysql_sql .= "  `regexp_error` TEXT,";
		$mysql_sql .= "  `options_values_sql` TEXT";
		$mysql_sql .= "  ,KEY payment_id (site_id)";
		$mysql_sql .= "  ,PRIMARY KEY (property_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "registration_custom_properties START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "registration_custom_properties (";
		$postgre_sql .= "  property_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "registration_custom_properties'),";
		$postgre_sql .= "  site_id INT4 default '1',";
		$postgre_sql .= "  property_order INT4 default '0',";
		$postgre_sql .= "  property_name VARCHAR(255),";
		$postgre_sql .= "  property_description TEXT,";
		$postgre_sql .= "  default_value TEXT,";
		$postgre_sql .= "  property_style VARCHAR(255),";
		$postgre_sql .= "  property_show SMALLINT default '0',";
		$postgre_sql .= "  control_type VARCHAR(16),";
		$postgre_sql .= "  control_style VARCHAR(255),";
		$postgre_sql .= "  control_code TEXT,";
		$postgre_sql .= "  onchange_code TEXT,";
		$postgre_sql .= "  onclick_code TEXT,";
		$postgre_sql .= "  required SMALLINT default '0',";
		$postgre_sql .= "  before_name_html TEXT,";
		$postgre_sql .= "  after_name_html TEXT,";
		$postgre_sql .= "  before_control_html TEXT,";
		$postgre_sql .= "  after_control_html TEXT,";
		$postgre_sql .= "  validation_regexp TEXT,";
		$postgre_sql .= "  regexp_error TEXT,";
		$postgre_sql .= "  options_values_sql TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (property_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "registration_custom_properties (";
		$access_sql .= "  [property_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [site_id] INTEGER,";
		$access_sql .= "  [property_order] INTEGER,";
		$access_sql .= "  [property_name] VARCHAR(255),";
		$access_sql .= "  [property_description] LONGTEXT,";
		$access_sql .= "  [default_value] LONGTEXT,";
		$access_sql .= "  [property_style] VARCHAR(255),";
		$access_sql .= "  [property_show] BYTE,";
		$access_sql .= "  [control_type] VARCHAR(16),";
		$access_sql .= "  [control_style] VARCHAR(255),";
		$access_sql .= "  [control_code] LONGTEXT,";
		$access_sql .= "  [onchange_code] LONGTEXT,";
		$access_sql .= "  [onclick_code] LONGTEXT,";
		$access_sql .= "  [required] BYTE,";
		$access_sql .= "  [before_name_html] LONGTEXT,";
		$access_sql .= "  [after_name_html] LONGTEXT,";
		$access_sql .= "  [before_control_html] LONGTEXT,";
		$access_sql .= "  [after_control_html] LONGTEXT,";
		$access_sql .= "  [validation_regexp] LONGTEXT,";
		$access_sql .= "  [regexp_error] LONGTEXT,";
		$access_sql .= "  [options_values_sql] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (property_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "registration_custom_properties (";
		$db2_sql .= "  property_id INTEGER NOT NULL,";
		$db2_sql .= "  site_id INTEGER default 1,";
		$db2_sql .= "  property_order INTEGER default 0,";
		$db2_sql .= "  property_name VARCHAR(255),";
		$db2_sql .= "  property_description LONG VARCHAR,";
		$db2_sql .= "  default_value LONG VARCHAR,";
		$db2_sql .= "  property_style VARCHAR(255),";
		$db2_sql .= "  property_show SMALLINT default 0,";
		$db2_sql .= "  control_type VARCHAR(16),";
		$db2_sql .= "  control_style VARCHAR(255),";
		$db2_sql .= "  control_code LONG VARCHAR,";
		$db2_sql .= "  onchange_code LONG VARCHAR,";
		$db2_sql .= "  onclick_code LONG VARCHAR,";
		$db2_sql .= "  required SMALLINT default 0,";
		$db2_sql .= "  before_name_html LONG VARCHAR,";
		$db2_sql .= "  after_name_html LONG VARCHAR,";
		$db2_sql .= "  before_control_html LONG VARCHAR,";
		$db2_sql .= "  after_control_html LONG VARCHAR,";
		$db2_sql .= "  validation_regexp LONG VARCHAR,";
		$db2_sql .= "  regexp_error LONG VARCHAR,";
		$db2_sql .= "  options_values_sql LONG VARCHAR";
		$db2_sql .= "  ,PRIMARY KEY (property_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_custom_propertie_59 ON " . $table_prefix . "support_custom_properties (site_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "registration_custom_properties AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "_149 NO CASCADE BEFORE INSERT ON " . $table_prefix . "registration_custom_properties REFERENCING NEW AS newr_" . $table_prefix . "registration_custom_properties FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "registration_custom_properties.property_id IS NULL ) begin atomic set newr_" . $table_prefix . "registration_custom_properties.property_id = nextval for seq_" . $table_prefix . "registration_custom_properties; end";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "registration_custom_values (";
		$mysql_sql .= "  `property_value_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `property_id` INT(11) default '0',";
		$mysql_sql .= "  `property_value` VARCHAR(255),";
		$mysql_sql .= "  `hide_value` TINYINT default '0',";
		$mysql_sql .= "  `is_default_value` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (property_value_id)";
		$mysql_sql .= "  ,KEY property_id (property_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "registration_custom_values START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "registration_custom_values (";
		$postgre_sql .= "  property_value_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "registration_custom_values'),";
		$postgre_sql .= "  property_id INT4 default '0',";
		$postgre_sql .= "  property_value VARCHAR(255),";
		$postgre_sql .= "  hide_value SMALLINT default '0',";
		$postgre_sql .= "  is_default_value SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (property_value_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "registration_custom_values (";
		$access_sql .= "  [property_value_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [property_id] INTEGER,";
		$access_sql .= "  [property_value] VARCHAR(255),";
		$access_sql .= "  [hide_value] BYTE,";
		$access_sql .= "  [is_default_value] BYTE";
		$access_sql .= "  ,PRIMARY KEY (property_value_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "registration_custom_values (";
		$db2_sql .= "  property_value_id INTEGER NOT NULL,";
		$db2_sql .= "  property_id INTEGER default 0,";
		$db2_sql .= "  property_value VARCHAR(255),";
		$db2_sql .= "  hide_value SMALLINT default 0,";
		$db2_sql .= "  is_default_value SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (property_value_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_custom_values_pr_60 ON " . $table_prefix . "registration_custom_values (property_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "registration_custom_values AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "150 NO CASCADE BEFORE INSERT ON " . $table_prefix . "registration_custom_values REFERENCING NEW AS newr_" . $table_prefix . "registration_custom_values FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "registration_custom_values.property_value_id IS NULL ) begin atomic set newr_" . $table_prefix . "registration_custom_values.property_value_id = nextval for seq_" . $table_prefix . "registration_custom_values; end";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "registration_properties (";
		$mysql_sql .= "  `registration_property_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `registration_id` INT(11) default '0',";
		$mysql_sql .= "  `property_id` INT(11) default '0',";
		$mysql_sql .= "  `property_value` TEXT";
		$mysql_sql .= "  ,PRIMARY KEY (registration_property_id)";
		$mysql_sql .= "  ,KEY property_id (property_id)";
		$mysql_sql .= "  ,KEY registration_id (registration_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "registration_properties START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "registration_properties (";
		$postgre_sql .= "  registration_property_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "registration_properties'),";
		$postgre_sql .= "  registration_id INT4 default '0',";
		$postgre_sql .= "  property_id INT4 default '0',";
		$postgre_sql .= "  property_value TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (registration_property_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "registration_properties (";
		$access_sql .= "  [registration_property_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [registration_id] INTEGER,";
		$access_sql .= "  [property_id] INTEGER,";
		$access_sql .= "  [property_value] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (registration_property_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "registration_properties (";
		$db2_sql .= "  registration_property_id INTEGER NOT NULL,";
		$db2_sql .= "  registration_id INTEGER default 0,";
		$db2_sql .= "  property_id INTEGER default 0,";
		$db2_sql .= "  property_value LONG VARCHAR";
		$db2_sql .= "  ,PRIMARY KEY (registration_property_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_properties_order_id ON " . $table_prefix . "registration_properties (registration_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "registration_properties_prope_61 ON " . $table_prefix . "registration_properties (property_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "registration_properties AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "151 NO CASCADE BEFORE INSERT ON " . $table_prefix . "registration_properties REFERENCING NEW AS newr_" . $table_prefix . "support_properties FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "support_properties.support_property_id IS NULL ) begin atomic set newr_" . $table_prefix . "support_properties.support_property_id = nextval for seq_" . $table_prefix . "support_properties; end";
		}				
		// end registration custom fields changes
		
		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.11");
	}

	if (comp_vers("3.5.12", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_price DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_price FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_price FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN recurring_price DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_price DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_price FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_price FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN recurring_price DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN friend_user_id INT(11) NOT NULL default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN friend_user_id INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN friend_user_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN friend_user_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "orders SET friend_user_id=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "orders_friend_user_id ON " . $table_prefix . "orders (friend_user_id) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN friend_code VARCHAR(64) ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "orders_friend_code ON " . $table_prefix . "orders (friend_code) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.12");
	}

	if (comp_vers("3.5.13", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN friend_user_id INT(11) NOT NULL default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN friend_user_id INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN friend_user_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN friend_user_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "orders_items SET friend_user_id=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_friend_user ON " . $table_prefix . "orders_items (friend_user_id) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.13");
	}

	if (comp_vers("3.5.14", $current_db_version) == 1)
	{
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "items_files (
      `file_id` INT(11) NOT NULL AUTO_INCREMENT,
      `item_id` INT(11) default '0',
      `item_type_id` INT(11) default '0',
      `download_type` TINYINT default '1',
      `download_title` VARCHAR(255),
      `download_path` VARCHAR(255),
      `download_period` TINYINT,
      `download_interval` INT(11),
      `download_limit` INT(11),
      `preview_type` TINYINT default '0',
      `preview_title` VARCHAR(255),
      `preview_path` VARCHAR(255),
      `preview_image` VARCHAR(255)
      ,KEY item_id (item_id)
		  ,KEY item_type_id (item_type_id)
		  ,PRIMARY KEY (file_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "items_files START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "items_files (
      file_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "items_files'),
      item_id INT4 default '0',
      item_type_id INT4 default '0',
      download_title VARCHAR(255),
      download_type SMALLINT default '1',
      download_path VARCHAR(255),
      download_period SMALLINT,
      download_interval INT4,
      download_limit INT4,
      preview_type SMALLINT default '0',
      preview_title VARCHAR(255),
      preview_path VARCHAR(255),
      preview_image VARCHAR(255)
      ,PRIMARY KEY (file_id))";

		$access_sql = "CREATE TABLE " . $table_prefix . "items_files (
      [file_id]  COUNTER  NOT NULL,
      [item_id] INTEGER,
      [item_type_id] INTEGER,
      [download_title] VARCHAR(255),
      [download_type] BYTE,
      [download_path] VARCHAR(255),
      [download_period] BYTE,
      [download_interval] INTEGER,
      [download_limit] INTEGER,
      [preview_type] BYTE,
      [preview_title] VARCHAR(255),
      [preview_path] VARCHAR(255),
      [preview_image] VARCHAR(255)
      ,PRIMARY KEY (file_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "items_files (
      file_id INTEGER NOT NULL,
      item_id INTEGER default 0,
      item_type_id INTEGER default 0,
      download_title VARCHAR(255),
      download_type SMALLINT default 1,
      download_path VARCHAR(255),
      download_period SMALLINT,
      download_interval INTEGER,
      download_limit INTEGER,
      preview_type SMALLINT default 0,
      preview_title VARCHAR(255),
      preview_path VARCHAR(255),
      preview_image VARCHAR(255)
      ,PRIMARY KEY (file_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "items_files_item_id ON " . $table_prefix . "items_files (item_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "items_files_item_type_id ON " . $table_prefix . "items_files (item_type_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "items_files AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "items_files NO CASCADE BEFORE INSERT ON " . $table_prefix . "items_files REFERENCING NEW AS newr_" . $table_prefix . "items_files FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "items_files.file_id IS NULL ) begin atomic set newr_" . $table_prefix . "items_files.file_id = nextval for seq_" . $table_prefix . "items_files; end";
		}				

 		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN download_files_ids TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN download_files_ids TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN download_files_ids LONGTEXT",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN download_files_ids LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_downloads ADD COLUMN download_limit INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_downloads ADD COLUMN download_limit INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_downloads ADD COLUMN download_limit INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_downloads ADD COLUMN download_limit INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$itf = new VA_Record($table_prefix . "items_files");
		$itf->add_textbox("file_id", INTEGER);
		$itf->add_textbox("item_id", INTEGER);
		$itf->add_textbox("item_type_id", INTEGER);
		$itf->set_value("item_type_id", 0);
		$itf->add_textbox("download_title", TEXT);
		$itf->add_textbox("download_type", INTEGER);
		$itf->add_textbox("download_path", TEXT);
		$itf->add_textbox("download_period", INTEGER);
		$itf->set_value("download_period", 1);
		$itf->add_textbox("download_interval", INTEGER);
		$itf->add_textbox("preview_type", INTEGER);
		$itf->set_value("preview_type", 0);

		$file_index = 0;
		$sql = " SELECT item_id, downloadable, download_period, download_path FROM " . $table_prefix . "items ";
		$db->query($sql);
		while($db->next_record()) {
			$item_id = $db->f("item_id");
			$downloadable = $db->f("downloadable");
			$download_period = $db->f("download_period");
			$download_path = $db->f("download_path");
			$download_paths = explode(";", $download_path);
			for ($d = 0; $d < sizeof($download_paths); $d++) {
				$file_path = trim($download_paths[$d]);
				if (strlen($file_path)) {
					$file_index++;
					$download_title = basename($file_path);
					$itf->set_value("file_id", $file_index);
					$itf->set_value("item_id", $item_id);
					$itf->set_value("download_title", $download_title);
					$itf->set_value("download_type", intval($downloadable));
					$itf->set_value("download_path", $file_path);
					$itf->set_value("download_interval", $download_period);
					$sqls[] = $itf->get_sql(INSERT_SQL);
				}
			}
		}

		$sql  = " SELECT ipv.item_property_id, ip.item_id, ip.item_type_id, ipv.download_period, ipv.download_path ";
		$sql .= " FROM (" . $table_prefix . "items_properties_values ipv ";
		$sql .= " INNER JOIN " . $table_prefix . "items_properties ip ON ipv.property_id=ip.property_id) ";
		$sql .= " ORDER BY ipv.item_property_id ";
		$db->query($sql);
		while ($db->next_record()) {
			
			$item_property_id = $db->f("item_property_id");
			$item_id = $db->f("item_id");
			$item_type_id = $db->f("item_type_id");
			$download_period = $db->f("download_period");
			$download_path = $db->f("download_path");
			$download_paths = explode(";", $download_path);
			$download_files_ids = "";
			for ($d = 0; $d < sizeof($download_paths); $d++) {
				$file_path = trim($download_paths[$d]);
				if (strlen($file_path)) {
					$file_index++;
					$download_title = basename($file_path);
					$itf->set_value("file_id", $file_index);
					$itf->set_value("item_id", $item_id);
					$itf->set_value("item_type_id", $item_type_id);
					$itf->set_value("download_title", $download_title);
					$itf->set_value("download_type", 2);
					$itf->set_value("download_path", $file_path);
					$itf->set_value("download_interval", $download_period);
					$sqls[] = $itf->get_sql(INSERT_SQL);
					if ($download_files_ids) {$download_files_ids .= ","; }
					$download_files_ids .= $file_index;
				}
			}
			if ($download_files_ids) {
				$sql  = " UPDATE " . $table_prefix . "items_properties_values ";
				$sql .= " SET download_files_ids=" . $db->tosql($download_files_ids, TEXT);
				$sql .= " WHERE item_property_id=" . $db->tosql($item_property_id, INTEGER);
				$sqls[] = $sql;
			}
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "header_links ADD COLUMN match_type TINYINT DEFAULT '2'",
			"postgre" => "ALTER TABLE " . $table_prefix . "header_links ADD COLUMN match_type SMALLINT DEFAULT '2'",
			"access"  => "ALTER TABLE " . $table_prefix . "header_links ADD COLUMN match_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "header_links ADD COLUMN match_type SMALLINT DEFAULT 2"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "header_links ADD COLUMN menu_page VARCHAR(128) ";
		$sqls[] = "UPDATE " . $table_prefix . "header_links SET match_type=2 ";

		$sql = " SELECT menu_id, menu_url FROM " . $table_prefix . "header_links ";
		$db->query($sql);
		while($db->next_record()) {
			$menu_id = $db->f("menu_id");
			$menu_url = $db->f("menu_url");
			$parsed_url = parse_url($menu_url);
			$menu_page = isset($parsed_url["path"]) ? $parsed_url["path"] : "/";
			$sql  = " UPDATE " . $table_prefix . "header_links SET menu_page=" . $db->tosql($menu_page, TEXT);
			$sql .= " WHERE menu_id=" . $db->tosql($menu_id, INTEGER);
			$sqls[] = $sql;

		}
		$sqls[] = "CREATE INDEX " . $table_prefix . "header_links_menu_page ON " . $table_prefix . "header_links (menu_page) ";

		// order_statuses supplier notification fields
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_notify TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_notify SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_notify BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_notify SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_to VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_from VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_cc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_bcc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_reply_to VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_return_path VARCHAR(64) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_mail_type TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_mail_type SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_mail_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_mail_type SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_subject VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_body TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_body TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_body LONGTEXT",
			"db2"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_body LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_sms_notify TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_sms_notify SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_sms_notify BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_sms_notify SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_sms_recipient VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_sms_originator VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_sms_message TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_sms_message TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_sms_message LONGTEXT",
			"db2"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN supplier_sms_message LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN supplier_id INT(11) NOT NULL default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN supplier_id INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN supplier_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN supplier_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "items SET supplier_id=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "items_supplier_id ON " . $table_prefix . "items (supplier_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN supplier_id INT(11) NOT NULL default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN supplier_id INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN supplier_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN supplier_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "orders_items SET supplier_id=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_supplier_id ON " . $table_prefix . "orders_items (supplier_id) ";

		$sqls[] = "UPDATE " . $table_prefix . "items SET manufacturer_id=0 WHERE manufacturer_id IS NULL ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "items_manufacturer_id ON " . $table_prefix . "items (manufacturer_id) ";


		$mysql_sql  = "CREATE TABLE " . $table_prefix . "suppliers (
      `supplier_id` INT(11) NOT NULL AUTO_INCREMENT,
      `supplier_order` INT(11) default '1',
      `supplier_name` VARCHAR(255),
      `supplier_email` VARCHAR(255),
      `short_description` TEXT,
      `full_description` TEXT,
      `image_small` VARCHAR(255),
      `image_small_alt` VARCHAR(255),
      `image_large` VARCHAR(255),
      `image_large_alt` VARCHAR(255)
      ,PRIMARY KEY (supplier_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "suppliers START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "suppliers (
      supplier_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "suppliers'),
      supplier_order INT4 default '1',
      supplier_name VARCHAR(255),
      supplier_email VARCHAR(255),
      short_description TEXT,
      full_description TEXT,
      image_small VARCHAR(255),
      image_small_alt VARCHAR(255),
      image_large VARCHAR(255),
      image_large_alt VARCHAR(255)
      ,PRIMARY KEY (supplier_id))";

		$access_sql = "CREATE TABLE " . $table_prefix . "suppliers (
      [supplier_id]  COUNTER  NOT NULL,
      [supplier_order] INTEGER,
      [supplier_name] VARCHAR(255),
      [supplier_email] VARCHAR(255),
      [short_description] LONGTEXT,
      [full_description] LONGTEXT,
      [image_small] VARCHAR(255),
      [image_small_alt] VARCHAR(255),
      [image_large] VARCHAR(255),
      [image_large_alt] VARCHAR(255)
      ,PRIMARY KEY (supplier_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "suppliers (
      supplier_id INTEGER NOT NULL,
      supplier_order INTEGER default 1,
      supplier_name VARCHAR(255),
      supplier_email VARCHAR(255),
      short_description LONG VARCHAR,
      full_description LONG VARCHAR,
      image_small VARCHAR(255),
      image_small_alt VARCHAR(255),
      image_large VARCHAR(255),
      image_large_alt VARCHAR(255)
      ,PRIMARY KEY (supplier_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "suppliers AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "suppliers NO CASCADE BEFORE INSERT ON " . $table_prefix . "suppliers REFERENCING NEW AS newr_" . $table_prefix . "suppliers FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "suppliers.supplier_id IS NULL ) begin atomic set newr_" . $table_prefix . "suppliers.supplier_id = nextval for seq_" . $table_prefix . "suppliers; end";
		}				

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.14");
	}
	if (comp_vers("3.5.15", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_modules ADD COLUMN cost_add_percent DOUBLE(16,2) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_modules ADD COLUMN cost_add_percent FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_modules ADD COLUMN cost_add_percent FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "shipping_modules ADD COLUMN cost_add_percent DOUBLE ",
		);
		$sqls[] = $sql_types[$db_type];
		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.15");
	}
	
	if (comp_vers("3.5.16", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_files ADD COLUMN preview_position TINYINT(1) DEFAULT 1",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_files ADD COLUMN preview_position SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "items_files ADD COLUMN preview_position BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_files ADD COLUMN preview_position SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.16");
	}
	
	if (comp_vers("3.5.17", $current_db_version) == 1) {
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "articles_forum_topics (
      `article_id` INT(11) NOT NULL default '0',
      `thread_id` INT(11) NOT NULL default '0',
      `article_order` INT(11) default '1',
      `thread_order` INT(11) default '1'
      ,PRIMARY KEY (article_id,thread_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "articles_forum_topics (
      article_id INT4 NOT NULL default '0',
      thread_id INT4 NOT NULL default '0',
      article_order INT4 default '1',
      thread_order INT4 default '1'
      ,PRIMARY KEY (article_id,thread_id))";

		$access_sql = "CREATE TABLE " . $table_prefix . "articles_forum_topics (
      [article_id] INTEGER NOT NULL,
      [thread_id] INTEGER NOT NULL,
      [article_order] INTEGER,
      [thread_order] INTEGER
      ,PRIMARY KEY (article_id,thread_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "articles_forum_topics (
      article_id INTEGER NOT NULL default 0,
      thread_id INTEGER NOT NULL default 0,
      article_order INTEGER default 1,
      thread_order INTEGER default 1
      ,PRIMARY KEY (article_id,thread_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "items_forum_topics (
      `item_id` INT(11) NOT NULL default '0',
      `thread_id` INT(11) NOT NULL default '0',
      `item_order` INT(11) default '1',
      `thread_order` INT(11) default '1'
      ,PRIMARY KEY (item_id,thread_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "items_forum_topics (
      item_id INT4 NOT NULL default '0',
      thread_id INT4 NOT NULL default '0',
      item_order INT4 default '1',
      thread_order INT4 default '1'
      ,PRIMARY KEY (item_id,thread_id))";

		$access_sql = "CREATE TABLE " . $table_prefix . "items_forum_topics (
      [item_id] INTEGER NOT NULL,
      [thread_id] INTEGER NOT NULL,
      [item_order] INTEGER,
      [thread_order] INTEGER
      ,PRIMARY KEY (item_id,thread_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "items_forum_topics (
      item_id INTEGER NOT NULL default 0,
      thread_id INTEGER NOT NULL default 0,
      item_order INTEGER default 1,
      thread_order INTEGER default 1
      ,PRIMARY KEY (item_id,thread_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.17");
	}

	if (comp_vers("3.5.18", $current_db_version) == 1) {

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_id INT(11) NOT NULL default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_id INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "orders_items SET subscription_id=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_subscription_id ON " . $table_prefix . "orders_items (subscription_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN subscription_id INT(11) NOT NULL default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN subscription_id INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN subscription_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "users ADD COLUMN subscription_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "users SET subscription_id=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "users_subscription_id ON " . $table_prefix . "users (subscription_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_encrypt_type TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_encrypt_type SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_encrypt_type BYTE ",           
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_encrypt_type SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_name VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_first_name VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_last_name VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_company_id VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_company_name VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_email VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_address1 VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_address2 VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_city VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_province VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_state_id VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_zip VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_country_id VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_phone VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_daytime_phone VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_evening_phone VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_cell_phone VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_fax VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_cc_number VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_cc_start_date VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_cc_expiry_date VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_cc_type VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_cc_issue_number VARCHAR(16) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN bill_cc_cvv2 VARCHAR(16) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_encrypt_type TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_encrypt_type SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_encrypt_type BYTE ",           
			"db2"     => "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_encrypt_type SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_name VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_first_name VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_last_name VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_company_id VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_company_name VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_email VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_address1 VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_address2 VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_city VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_province VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_state_id VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_zip VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_country_id VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_phone VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_daytime_phone VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_evening_phone VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_cell_phone VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_fax VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_cc_number VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_cc_start_date VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_cc_expiry_date VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_cc_type VARCHAR(32) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_cc_issue_number VARCHAR(16) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "users ADD COLUMN bill_cc_cvv2 VARCHAR(16) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.18");
	}

	if (comp_vers("3.5.19", $current_db_version) == 1) {

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_start_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_start_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_start_date DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_start_date TIMESTAMP ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_expiry_date DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_expiry_date TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_expiry_date DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN subscription_expiry_date TIMESTAMP ",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN packages_number INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN packages_number INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN packages_number INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN packages_number INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN packages_number INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN packages_number INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN packages_number INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN packages_number INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.19");
	}	

	if (comp_vers("3.5.20", $current_db_version) == 1) {

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN show_for_user TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN show_for_user SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN show_for_user BYTE ",           
			"db2"     => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN show_for_user SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "currencies SET show_for_user=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_items_related ADD COLUMN item_order INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_items_related ADD COLUMN item_order INT4 default '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_items_related ADD COLUMN item_order INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_items_related ADD COLUMN item_order INTEGER DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_items_related ADD COLUMN article_order INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_items_related ADD COLUMN article_order INT4 default '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_items_related ADD COLUMN article_order INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_items_related ADD COLUMN article_order INTEGER DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "articles_items_related SET item_order=related_order,article_order=1 ";

		$sqls[] = " ALTER TABLE ". $table_prefix . "articles_items_related DROP COLUMN related_order ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN payment_order INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN payment_order INT4 default '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN payment_order INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN payment_order INTEGER DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "payment_systems SET payment_order=1 ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.20");
	}	

	if (comp_vers("3.5.21", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN vouchers_amount DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN vouchers_amount FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN vouchers_amount FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN vouchers_amount DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN vouchers_ids TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN vouchers_ids TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN vouchers_ids LONGTEXT",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN vouchers_ids LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN goods_incl_tax DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN goods_incl_tax FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN goods_incl_tax FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN goods_incl_tax DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "orders SET vouchers_amount=0, goods_incl_tax=0  ";

		$sqls[] = " ALTER TABLE ". $table_prefix . "orders DROP COLUMN goods_taxable ";

		// apply multi-sites for banners
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "banners_sites (";
		$mysql_sql .= "  `banner_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (banner_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "banners_sites (";
		$postgre_sql .= "  banner_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (banner_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "banners_sites (";
		$access_sql .= "  [banner_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (banner_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "banners_sites (";
		$db2_sql .= "  banner_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (banner_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "banners ADD COLUMN sites_all TINYINT NOT NULL DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "banners ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "banners ADD COLUMN sites_all BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "banners ADD COLUMN sites_all SMALLINT NOT NULL DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "banners SET sites_all=1 ";

		// added subscriptions tables
		// ads categories
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "ads_categories_subscriptions (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `subscription_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `access_level` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_categories_subscriptions (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  subscription_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  access_level SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_categories_subscriptions (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [subscription_id] INTEGER NOT NULL,";
		$access_sql .= "  [access_level] BYTE";
		$access_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "ads_categories_subscriptions (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  subscription_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  access_level SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN subscription_access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN subscription_access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN subscription_access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN subscription_access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "ads_categories SET subscription_access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "ads_categories_subscript_2 ON " . $table_prefix . "ads_categories (subscription_access_level) ";

		// articles categories
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_subscriptions (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `subscription_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `access_level` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_subscriptions (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  subscription_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  access_level SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_subscriptions (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [subscription_id] INTEGER NOT NULL,";
		$access_sql .= "  [access_level] BYTE";
		$access_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_subscriptions (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  subscription_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  access_level SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN subscription_access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN subscription_access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN subscription_access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN subscription_access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "articles_categories SET subscription_access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "articles_categories_subs_9 ON " . $table_prefix . "articles_categories (subscription_access_level) ";

		// products categories 
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "categories_subscriptions (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `subscription_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `access_level` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "categories_subscriptions (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  subscription_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  access_level SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "categories_subscriptions (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [subscription_id] INTEGER NOT NULL,";
		$access_sql .= "  [access_level] BYTE";
		$access_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "categories_subscriptions (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  subscription_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  access_level SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN subscription_access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN subscription_access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN subscription_access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN subscription_access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "categories SET subscription_access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "categories_subscription__16 ON " . $table_prefix . "categories (subscription_access_level) ";

		// forum 
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "forum_subscriptions (";
		$mysql_sql .= "  `forum_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `subscription_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `access_level` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (forum_id,subscription_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "forum_subscriptions (";
		$postgre_sql .= "  forum_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  subscription_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  access_level SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (forum_id,subscription_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "forum_subscriptions (";
		$access_sql .= "  [forum_id] INTEGER NOT NULL,";
		$access_sql .= "  [subscription_id] INTEGER NOT NULL,";
		$access_sql .= "  [access_level] BYTE";
		$access_sql .= "  ,PRIMARY KEY (forum_id,subscription_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "forum_subscriptions (";
		$db2_sql .= "  forum_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  subscription_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  access_level SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (forum_id,subscription_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN subscription_access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN subscription_access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN subscription_access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN subscription_access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET subscription_access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "forum_list_subscription__20 ON " . $table_prefix . "forum_list (subscription_access_level) ";

		// manuals categories
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories_subscriptions (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `subscription_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `access_level` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories_subscriptions (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  subscription_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  access_level SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories_subscriptions (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [subscription_id] INTEGER NOT NULL,";
		$access_sql .= "  [access_level] BYTE";
		$access_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories_subscriptions (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  subscription_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  access_level SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,subscription_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN subscription_access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN subscription_access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN subscription_access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN subscription_access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "manuals_categories SET subscription_access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "manuals_categories_subsc_35 ON " . $table_prefix . "manuals_categories (subscription_access_level) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.21");
	}	

	if (comp_vers("3.5.22", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN is_default_show TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN is_default_show SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN is_default_show BYTE NOT ",
			"db2"     => "ALTER TABLE " . $table_prefix . "currencies ADD COLUMN is_default_show SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "currencies SET is_default_show=is_default ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_modules ADD COLUMN is_call_center TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_modules ADD COLUMN is_call_center SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_modules ADD COLUMN is_call_center BYTE NOT ",
			"db2"     => "ALTER TABLE " . $table_prefix . "shipping_modules ADD COLUMN is_call_center SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "shipping_modules SET is_call_center=is_active ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN is_call_center TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN is_call_center SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN is_call_center BYTE NOT ",
			"db2"     => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN is_call_center SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "shipping_types SET is_call_center=is_active ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.22");
	}	


	if (comp_vers("3.5.23", $current_db_version) == 1) {
		// ads categories user types
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "ads_categories_types (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `access_level` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_categories_types (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  access_level SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_categories_types (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL,";
		$access_sql .= "  [access_level] BYTE";
		$access_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "ads_categories_types (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  access_level SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "ads_categories SET access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "ads_categories_access ON " . $table_prefix . "ads_categories (access_level) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "articles_categories SET access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "articles_categories_access ON " . $table_prefix . "articles_categories (access_level) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "categories SET access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "categories_access ON " . $table_prefix . "categories (access_level) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "forum_list_access ON " . $table_prefix . "forum_list (access_level) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_categories ADD COLUMN item_order INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_categories ADD COLUMN item_order INT4 default '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_categories ADD COLUMN item_order INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_categories ADD COLUMN item_order INTEGER DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "items_categories SET item_order=1 ";

		// items subscriptions
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "items_subscriptions (";
		$mysql_sql .= "  `item_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `subscription_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `access_level` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (item_id,subscription_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "items_subscriptions (";
		$postgre_sql .= "  item_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  subscription_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  access_level SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (item_id,subscription_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "items_subscriptions (";
		$access_sql .= "  [item_id] INTEGER NOT NULL,";
		$access_sql .= "  [subscription_id] INTEGER NOT NULL,";
		$access_sql .= "  [access_level] BYTE";
		$access_sql .= "  ,PRIMARY KEY (item_id,subscription_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "items_subscriptions (";
		$db2_sql .= "  item_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  subscription_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  access_level SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (item_id,subscription_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "manuals_categories SET access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "manuals_categories_access ON " . $table_prefix . "manuals_categories (access_level) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_assigned ADD COLUMN article_order INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_assigned ADD COLUMN article_order INT4 default '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_assigned ADD COLUMN article_order INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_assigned ADD COLUMN article_order INTEGER DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "articles_assigned SET article_order=1 ";

		// subscriptions
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "subscriptions (
      `subscription_id` INT(11) NOT NULL AUTO_INCREMENT,
      `group_id` INT(11) default '0',
      `user_type_id` INT(11) default '0',
      `subscription_name` VARCHAR(255),
      `is_active` TINYINT default '1',
      `is_default` TINYINT default '0',
      `is_subscription_recurring` TINYINT default '1',
      `subscription_fee` DOUBLE(16,2) default '0',
      `subscription_period` INT(11),
      `subscription_interval` INT(11),
      `subscription_suspend` INT(11) default '0',
      `subscription_points_type` TINYINT,
      `subscription_points_amount` DOUBLE(16,4),
      `subscription_credits_type` TINYINT,
      `subscription_credits_amount` DOUBLE(16,2),
      `subscription_affiliate_type` TINYINT,
      `subscription_affiliate_amount` INT(11)
      ,PRIMARY KEY (subscription_id)
      ,KEY user_type_id (user_type_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "subscriptions START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "subscriptions (
      subscription_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "subscriptions'),
      group_id INT4 default '0',
      user_type_id INT4 default '0',
      subscription_name VARCHAR(255),
      is_active SMALLINT default '1',
      is_default SMALLINT default '0',
      is_subscription_recurring SMALLINT default '1',
      subscription_fee FLOAT4 default '0',
      subscription_period INT4,
      subscription_interval INT4,
      subscription_suspend INT4 default '0',
      subscription_points_type SMALLINT,
      subscription_points_amount FLOAT4,
      subscription_credits_type SMALLINT,
      subscription_credits_amount FLOAT4,
      subscription_affiliate_type SMALLINT,
      subscription_affiliate_amount INT4
      ,PRIMARY KEY (subscription_id))";

		$access_sql = "CREATE TABLE " . $table_prefix . "subscriptions (
      [subscription_id]  COUNTER  NOT NULL,
      [group_id] INTEGER,
      [user_type_id] INTEGER,
      [subscription_name] VARCHAR(255),
      [is_active] BYTE,
      [is_default] BYTE,
      [is_subscription_recurring] BYTE,
      [subscription_fee] FLOAT,
      [subscription_period] INTEGER,
      [subscription_interval] INTEGER,
      [subscription_suspend] INTEGER,
      [subscription_points_type] BYTE,
      [subscription_points_amount] FLOAT,
      [subscription_credits_type] BYTE,
      [subscription_credits_amount] FLOAT,
      [subscription_affiliate_type] BYTE,
      [subscription_affiliate_amount] INTEGER
      ,PRIMARY KEY (subscription_id))";


		$db2_sql  = "CREATE TABLE " . $table_prefix . "subscriptions (
      subscription_id INTEGER NOT NULL,
      group_id INTEGER default 0,
      user_type_id INTEGER default 0,
      subscription_name VARCHAR(255),
      is_active SMALLINT default 1,
      is_default SMALLINT default 0,
      is_subscription_recurring SMALLINT default 1,
      subscription_fee DOUBLE default 0,
      subscription_period INTEGER,
      subscription_interval INTEGER,
      subscription_suspend INTEGER default 0,
      subscription_points_type SMALLINT,
      subscription_points_amount DOUBLE,
      subscription_credits_type SMALLINT,
      subscription_credits_amount DOUBLE,
      subscription_affiliate_type SMALLINT,
      subscription_affiliate_amount INTEGER
      ,PRIMARY KEY (subscription_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "subscriptions_group_id ON " . $table_prefix . "subscriptions (group_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "subscriptions_95 ON " . $table_prefix . "subscriptions (user_type_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "subscriptions AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "subscrip_133 NO CASCADE BEFORE INSERT ON " . $table_prefix . "subscriptions REFERENCING NEW AS newr_" . $table_prefix . "subscriptions FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "subscriptions.subscription_id IS NULL ) begin atomic set newr_" . $table_prefix . "subscriptions.subscription_id = nextval for seq_" . $table_prefix . "subscriptions; end";
		}				

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "subscriptions_groups (
      `group_id` INT(11) NOT NULL AUTO_INCREMENT,
      `group_name` VARCHAR(255),
      `is_active` TINYINT default '1'
      ,PRIMARY KEY (group_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "subscriptions_groups START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "subscriptions_groups (
      group_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "subscriptions_groups'),
      group_name VARCHAR(255),
      is_active SMALLINT default '1'
      ,PRIMARY KEY (group_id))";

		$access_sql = "CREATE TABLE " . $table_prefix . "subscriptions_groups (
      [group_id]  COUNTER  NOT NULL,
      [group_name] VARCHAR(255),
      [is_active] BYTE
      ,PRIMARY KEY (group_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "subscriptions_groups (
      group_id INTEGER NOT NULL,
      group_name VARCHAR(255),
      is_active SMALLINT default 1
      ,PRIMARY KEY (group_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "subscriptions_groups AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "subscrip_118 NO CASCADE BEFORE INSERT ON " . $table_prefix . "subscriptions_groups REFERENCING NEW AS newr_" . $table_prefix . "subscriptions_groups FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "subscriptions_groups.group_id IS NULL ) begin atomic set newr_" . $table_prefix . "subscriptions_groups.group_id = nextval for seq_" . $table_prefix . "subscriptions_groups; end";
		}				

		// move old subscriptions to new table
		$sql = " SELECT * FROM " . $table_prefix . "user_types WHERE is_subscription=1 ";
		$db->query($sql);
		while ($db->next_record()) {
			$sql  = " INSERT INTO " . $table_prefix . "subscriptions ";
			$sql .= " (group_id, user_type_id, subscription_name, is_subscription_recurring, is_active, is_default, ";
			$sql .= " subscription_fee, subscription_period, subscription_interval, subscription_suspend, ";
			$sql .= " subscription_points_type, subscription_points_amount, subscription_credits_type, subscription_credits_amount, ";
			$sql .= " subscription_affiliate_type, subscription_affiliate_amount) VALUES (";
			$sql .= $db->tosql(0, INTEGER) . ", ";
			$sql .= $db->tosql($db->f("type_id"), INTEGER) . ", ";
			$sql .= $db->tosql($db->f("subscription_name"), TEXT) . ", ";
			$sql .= $db->tosql($db->f("is_active"), INTEGER) . ", ";
			$sql .= $db->tosql(1, INTEGER) . ", ";
			$sql .= $db->tosql($db->f("is_subscription_recurring"), INTEGER) . ", ";
			$sql .= $db->tosql($db->f("subscription_fee"), NUMBER) . ", ";
			$sql .= $db->tosql($db->f("subscription_period"), INTEGER) . ", ";
			$sql .= $db->tosql($db->f("subscription_interval"), INTEGER) . ", ";
			$sql .= $db->tosql($db->f("subscription_suspend"), INTEGER) . ", ";
			$sql .= $db->tosql($db->f("subscription_points_type"), INTEGER) . ", ";
			$sql .= $db->tosql($db->f("subscription_points_amount"), NUMBER) . ", ";
			$sql .= $db->tosql($db->f("subscription_credits_type"), INTEGER) . ", ";
			$sql .= $db->tosql($db->f("subscription_credits_amount"), NUMBER) . ", ";
			$sql .= $db->tosql($db->f("subscription_affiliate_type"), INTEGER) . ", ";
			$sql .= $db->tosql($db->f("subscription_affiliate_amount"), NUMBER) . ") ";
			$sqls[] = $sql;
		}

		$sqls[] = "ALTER TABLE " . $table_prefix . "categories ADD COLUMN a_title VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN a_title VARCHAR(255) ";

		// add new subscriptions permissions for admin
		$permissions = array("subscriptions", "subscriptions_groups");
		$sql = " SELECT privilege_id FROM  " . $table_prefix . "admin_privileges_settings WHERE block_name='users_groups' AND permission=1 ";
		$db->query($sql);
		while ($db->next_record()) {
			$privilege_id = $db->f("privilege_id");
			for ($i = 0; $i < sizeof($permissions); $i++) {
				$sql  = " DELETE FROM " . $table_prefix . "admin_privileges_settings ";
				$sql .= " WHERE privilege_id=" . $db->tosql($privilege_id, INTEGER);
				$sql .= " AND block_name=" . $db->tosql($permissions[$i], TEXT);
				$sqls[] = $sql;
				$sql  = " INSERT INTO " . $table_prefix . "admin_privileges_settings (privilege_id, block_name, permission) VALUES (";
				$sql .= $db->tosql($privilege_id, INTEGER) . ", '" . $permissions[$i] . "',1)";
				$sqls[] = $sql;
			}
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.23");
	}	

	if (comp_vers("3.5.24", $current_db_version) == 1) {

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "admins_login_stats (
      `stat_id` INT(11) NOT NULL AUTO_INCREMENT,
      `admin_id` INT(11) default '0',
      `login_status` TINYINT default '0',
      `ip_address` VARCHAR(32),
      `forwarded_ips` VARCHAR(255),
      `date_added` DATETIME
      ,KEY admin_id (admin_id)
      ,PRIMARY KEY (stat_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "admins_login_stats START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "admins_login_stats (
      stat_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "admins_login_stats'),
      admin_id INT4 default '0',
      login_status SMALLINT default '0',
      ip_address VARCHAR(32),
      forwarded_ips VARCHAR(255),
      date_added TIMESTAMP
      ,PRIMARY KEY (stat_id))";

		$access_sql = "CREATE TABLE " . $table_prefix . "admins_login_stats (
      [stat_id]  COUNTER  NOT NULL,
      [admin_id] INTEGER,
      [login_status] BYTE,
      [ip_address] VARCHAR(32),
      [forwarded_ips] VARCHAR(255),
      [date_added] DATETIME
      ,PRIMARY KEY (stat_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "admins_login_stats (
      stat_id INTEGER NOT NULL,
      admin_id INTEGER default 0,
      login_status SMALLINT default 0,
      ip_address VARCHAR(32),
      forwarded_ips VARCHAR(255),
      date_added TIMESTAMP
      ,PRIMARY KEY (stat_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type != "mysql") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "admins_login_stats_admin_id ON " . $table_prefix . "admins_login_stats (admin_id)";
		}

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "admins_login_stats AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "admins_l_3 NO CASCADE BEFORE INSERT ON " . $table_prefix . "admins_login_stats REFERENCING NEW AS newr_" . $table_prefix . "admins_login_stats FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "admins_login_stats.stat_id IS NULL ) begin atomic set newr_" . $table_prefix . "admins_login_stats.stat_id = nextval for seq_" . $table_prefix . "admins_login_stats; end";
		}				

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "support_predefined_types (
      `type_id` INT(11) NOT NULL AUTO_INCREMENT,
      `type_name` VARCHAR(64)
      ,PRIMARY KEY (type_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "support_predefined_types START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "support_predefined_types (
      type_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "support_predefined_types'),
      type_name VARCHAR(64)
      ,PRIMARY KEY (type_id))";

		$access_sql = "CREATE TABLE " . $table_prefix . "support_predefined_types (
      [type_id]  COUNTER  NOT NULL,
      [type_name] VARCHAR(64)
      ,PRIMARY KEY (type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "support_predefined_types (
      type_id INTEGER NOT NULL,
      type_name VARCHAR(64)
      ,PRIMARY KEY (type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "support_predefined_types AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "support__127 NO CASCADE BEFORE INSERT ON " . $table_prefix . "support_predefined_types REFERENCING NEW AS newr_" . $table_prefix . "support_predefined_types FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "support_predefined_types.type_id IS NULL ) begin atomic set newr_" . $table_prefix . "support_predefined_types.type_id = nextval for seq_" . $table_prefix . "support_predefined_types; end";
		}				


		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN type_id INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN type_id INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN type_id INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN type_id INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN total_uses INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN total_uses INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN total_uses INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN total_uses INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN admin_id_added_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN admin_id_added_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN admin_id_added_by INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN admin_id_added_by INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN admin_id_modified_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN admin_id_modified_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN admin_id_modified_by INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN admin_id_modified_by INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN date_added DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN date_added TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN date_added DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN date_added TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN date_modified DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN date_modified TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN date_modified DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN date_modified TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN last_updated DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN last_updated TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN last_updated DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_predefined ADD COLUMN last_updated TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];

		$type_id = 0;
		$sql = " SELECT reply_type FROM " . $table_prefix . "support_predefined GROUP BY reply_type ";
		$db->query($sql);
		while ($db->next_record()) {
			$type_id++;
			$type_name = $db->f("reply_type");
			$sql  = " INSERT INTO " . $table_prefix . "support_predefined_types (type_id, type_name) VALUES (";
			$sql .= $db->tosql($type_id, INTEGER) . ", " . $db->tosql($type_name, TEXT) . ")";
			$sqls[] = $sql;

			$sql  = " UPDATE " . $table_prefix . "support_predefined ";
			$sql .= " SET type_id=" . $db->tosql($type_id, INTEGER);
			$sql .= " WHERE reply_type=" . $db->tosql($type_name, TEXT);
			$sqls[] = $sql;
		}

		$sqls[] = " ALTER TABLE ". $table_prefix . "support_predefined DROP COLUMN reply_type ";
		$sqls[] = " UPDATE " . $table_prefix . "support_predefined SET last_updated=" . $db->tosql(va_time(), DATETIME);

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.24");
	}

	if (comp_vers("3.5.25", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN guest_access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN guest_access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN guest_access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN guest_access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "ads_categories SET guest_access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "ads_categories_guest ON " . $table_prefix . "ads_categories (guest_access_level) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN guest_access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN guest_access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN guest_access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN guest_access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "articles_categories SET guest_access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "articles_categories_guest ON " . $table_prefix . "articles_categories (guest_access_level) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories_types ADD COLUMN access_level TINYINT UNSIGNED DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories_types ADD COLUMN access_level SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories_types ADD COLUMN access_level BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_categories_types ADD COLUMN access_level SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN guest_access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN guest_access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN guest_access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "categories ADD COLUMN guest_access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "categories SET guest_access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "categories_guest ON " . $table_prefix . "categories (guest_access_level) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "categories_user_types ADD COLUMN access_level TINYINT UNSIGNED DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "categories_user_types ADD COLUMN access_level SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "categories_user_types ADD COLUMN access_level BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "categories_user_types ADD COLUMN access_level SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN guest_access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN guest_access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN guest_access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "forum_list ADD COLUMN guest_access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "forum_list SET guest_access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "forum_list_guest ON " . $table_prefix . "forum_list (guest_access_level) ";

		// forum user types
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "forum_user_types (";
		$mysql_sql .= "  `forum_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `access_level` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "forum_user_types (";
		$postgre_sql .= "  forum_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  access_level SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "forum_user_types (";
		$access_sql .= "  [forum_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL,";
		$access_sql .= "  [access_level] BYTE";
		$access_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "forum_user_types (";
		$db2_sql .= "  forum_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  access_level SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (forum_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN guest_access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN guest_access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN guest_access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "manuals_categories ADD COLUMN guest_access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "manuals_categories SET guest_access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "manuals_categories_guest ON " . $table_prefix . "manuals_categories (guest_access_level) ";

		// manuals categories user types
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories_types (";
		$mysql_sql .= "  `category_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `access_level` TINYINT default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories_types (";
		$postgre_sql .= "  category_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  user_type_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  access_level SMALLINT default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories_types (";
		$access_sql .= "  [category_id] INTEGER NOT NULL,";
		$access_sql .= "  [user_type_id] INTEGER NOT NULL,";
		$access_sql .= "  [access_level] BYTE";
		$access_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "manuals_categories_types (";
		$db2_sql .= "  category_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  user_type_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  access_level SMALLINT default 0";
		$db2_sql .= "  ,PRIMARY KEY (category_id,user_type_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items SET access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "items_access_level ON " . $table_prefix . "items (access_level) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN guest_access_level TINYINT UNSIGNED NOT NULL DEFAULT '7'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN guest_access_level SMALLINT NOT NULL DEFAULT '7'",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN guest_access_level BYTE NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items ADD COLUMN guest_access_level SMALLINT NOT NULL DEFAULT 7"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items SET guest_access_level=7 ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "items_guest ON " . $table_prefix . "items (guest_access_level) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_user_types ADD COLUMN access_level TINYINT UNSIGNED DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_user_types ADD COLUMN access_level SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "items_user_types ADD COLUMN access_level BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_user_types ADD COLUMN access_level SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "footer_links (
      `menu_id` INT(11) NOT NULL AUTO_INCREMENT,
      `menu_title` VARCHAR(255),
      `match_type` TINYINT default '1',
      `menu_url` VARCHAR(50),
      `menu_target` VARCHAR(32),
      `menu_order` INT(11) default '1',
      `onclick_code` TEXT,
      `access_level` TINYINT default '1',
      `guest_access_level` TINYINT default '1'
      ,PRIMARY KEY (menu_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "footer_links START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "footer_links (
      menu_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "footer_links'),
      menu_title VARCHAR(255),
      match_type SMALLINT default '1',
      menu_url VARCHAR(50),
      menu_target VARCHAR(32),
      menu_order INT4 default '1',
      onclick_code TEXT,
      access_level SMALLINT default '1',
      guest_access_level SMALLINT default '1'
      ,PRIMARY KEY (menu_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "footer_links (
      [menu_id]  COUNTER  NOT NULL,
      [menu_title] VARCHAR(255),
      [match_type] BYTE,
      [menu_url] VARCHAR(50),
      [menu_target] VARCHAR(32),
      [menu_order] INTEGER,
      [onclick_code] LONGTEXT,
      [access_level] BYTE,
      [guest_access_level] BYTE
      ,PRIMARY KEY (menu_id))";


		$db2_sql  = "CREATE TABLE " . $table_prefix . "footer_links (
      menu_id INTEGER NOT NULL,
      menu_title VARCHAR(255),
      match_type SMALLINT default 1,
      menu_url VARCHAR(50),
      menu_target VARCHAR(32),
      menu_order INTEGER default 1,
      onclick_code LONG VARCHAR,
      access_level SMALLINT default 1,
      guest_access_level SMALLINT default 1
      ,PRIMARY KEY (menu_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "footer_links AS INTEGER START WITH 1 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "footer_links NO CASCADE BEFORE INSERT ON " . $table_prefix . "footer_links REFERENCING NEW AS newr_" . $table_prefix . "footer_links FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "footer_links.menu_id IS NULL ) begin atomic set newr_" . $table_prefix . "footer_links.menu_id = nextval for seq_" . $table_prefix . "footer_links; end";
		}				
		
		$menu_order = 0;
		$sql = " SELECT * FROM " . $table_prefix . "pages WHERE link_in_footer=1 ";
		$db->query($sql);
		while ($db->next_record()) {
			$menu_order++;
			$page_url = $db->f("page_url");
			$is_showing = $db->f("is_showing");
			$friendly_url = $db->f("friendly_url");
			$menu_title = $db->f("page_title");
			if ($friendly_urls && strlen($friendly_url)) {
				$menu_url = $friendly_url . $friendly_extension;
			} else if (strlen($page_url)) {
				$menu_url = $page_url;
			} else {
				$menu_url = get_custom_friendly_url("page.php") . "?page=" . urlencode($db->f("page_code"));
			}
			$onclick_code = ($db->f("page_type") == 2) ? "return openPopup('" . $menu_url. "', 600, 450);" : "";
			$access_level = $is_showing;
			$guest_access_level = $is_showing;

			$sql  = " INSERT INTO " . $table_prefix . "footer_links ";
			$sql .= " (menu_title, match_type, menu_url, menu_order, onclick_code, ";
			$sql .= " access_level, guest_access_level) VALUES (";
			$sql .= $db->tosql($menu_title, TEXT) . ", ";
			$sql .= $db->tosql(1, INTEGER) . ", ";
			$sql .= $db->tosql($menu_url, TEXT) . ", ";
			$sql .= $db->tosql($menu_order, INTEGER) . ", ";
			$sql .= $db->tosql($onclick_code, TEXT) . ", ";
			$sql .= $db->tosql($access_level, INTEGER) . ", ";
			$sql .= $db->tosql($guest_access_level, INTEGER) . ") ";
			$sqls[] = $sql;
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.25");
	}

	if (comp_vers("3.5.26", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_account_subscription TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_account_subscription SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_account_subscription BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN is_account_subscription SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "orders_items SET is_account_subscription=1 ";

		$sqls[] = " CREATE INDEX " . $table_prefix . "orders_items_subscription ON " . $table_prefix . "orders_items (is_subscription) ";
		$sqls[] = " CREATE INDEX " . $table_prefix . "orders_subscription_expiry ON " . $table_prefix . "orders_items (subscription_expiry_date) ";

		// add layout for subscriptions page
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'products_options', 'subscriptions', 0, 'middle')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'subscriptions', 'left_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'subscriptions', 'left_column_width', NULL, NULL)";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'subscriptions', 'middle_column_hide', NULL, '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'subscriptions', 'middle_column_width', NULL, '100%')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'subscriptions', 'right_column_hide', NULL, '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "page_settings (site_id,layout_id,page_name,setting_name,setting_order,setting_value) VALUES (1, 0, 'subscriptions', 'right_column_width', NULL, NULL)";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.26");
	}

	if (comp_vers("3.5.27", $current_db_version) == 1) {
		// new settings for reviews
		$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type='products' AND setting_name='reviews_availability' ";
		$products_reviews_allowed = get_db_value($sql);

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'allowed_view', ".$db->tosql($products_reviews_allowed, TEXT).")";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'allowed_view', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'allowed_post', ".$db->tosql($products_reviews_allowed, TEXT).")";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'allowed_post', '1')";

		$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type='global' AND setting_name='approve_review' ";
		$approve_review = get_db_value($sql);
		$auto_approve = ($approve_review == 1) ? 0 : 1; 

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'auto_approve', ".$db->tosql($auto_approve, TEXT).")";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'auto_approve', ".$db->tosql($auto_approve, TEXT).")";

		$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type='global' AND setting_name='reviews_per_page' ";
		$reviews_per_page = get_db_value($sql);

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'reviews_per_page', ".$db->tosql($reviews_per_page, TEXT).")";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'reviews_per_page', ".$db->tosql($reviews_per_page, TEXT).")";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'reviews_per_user', ".$db->tosql(1, TEXT).")";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'reviews_per_user', ".$db->tosql(1, TEXT).")";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'reviews_interval', ".$db->tosql(1, TEXT).")";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'reviews_interval', ".$db->tosql(1, TEXT).")";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'reviews_period', ".$db->tosql(2, TEXT).")";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'reviews_period', ".$db->tosql(2, TEXT).")";


		$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type='global' AND setting_name='review_random_image' ";
		$review_random_image = get_db_value($sql);

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'review_random_image', ".$db->tosql($review_random_image, TEXT).")";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'review_random_image', ".$db->tosql($review_random_image, TEXT).")";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'show_recommended', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'recommended_required', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'recommended_order', '1')";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'show_rating', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'rating_required', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'rating_order', '2')";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'show_user_name', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'user_name_required', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'user_name_order', '3')";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'show_user_email', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'user_email_required', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'user_email_order', '4')";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'show_summary', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'summary_required', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'summary_order', '5')";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'show_comments', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'comments_required', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'products_reviews', 'comments_order', '6')";

		// articles reviews
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'show_recommended', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'recommended_required', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'recommended_order', '1')";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'show_rating', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'rating_required', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'rating_order', '2')";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'show_user_name', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'user_name_required', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'user_name_order', '3')";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'show_user_email', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'user_email_required', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'user_email_order', '4')";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'show_summary', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'summary_required', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'summary_order', '5')";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'show_comments', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'comments_required', '0')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (site_id, setting_type, setting_name, setting_value) VALUES (1, 'articles_reviews', 'comments_order', '6')";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.27");
	}

	if (comp_vers("3.5.28", $current_db_version) == 1) {

		// product reviews
		$sqls[] = "UPDATE " . $table_prefix . "reviews SET recommended=-1 WHERE recommended=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN user_id INT(11) NOT NULL default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN user_id INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN user_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN user_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "reviews SET user_id=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "reviews_user_id ON " . $table_prefix . "reviews (user_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN admin_id INT(11) NOT NULL default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN admin_id INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN admin_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN admin_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "reviews SET admin_id=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "reviews_admin_id ON " . $table_prefix . "reviews (admin_id) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN user_email VARCHAR(128) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN admin_id_approved_by INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN admin_id_approved_by INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN admin_id_approved_by INTEGER  ",
			"db2"     => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN admin_id_approved_by INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN admin_id_modified_by INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN admin_id_modified_by INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN admin_id_modified_by INTEGER  ",
			"db2"     => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN admin_id_modified_by INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN date_modified DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN date_modified TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN date_modified DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN date_modified TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN date_approved DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN date_approved TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN date_approved DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "reviews ADD COLUMN date_approved TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];

		// articles reviews
		$sqls[] = "UPDATE " . $table_prefix . "articles_reviews SET recommended=-1 WHERE recommended=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN user_id INT(11) NOT NULL default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN user_id INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN user_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN user_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "articles_reviews SET user_id=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "articles_reviews_user_id ON " . $table_prefix . "articles_reviews (user_id) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN admin_id INT(11) NOT NULL default '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN admin_id INT4 NOT NULL default '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN admin_id INTEGER NOT NULL ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN admin_id INTEGER NOT NULL DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "articles_reviews SET admin_id=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "articles_reviews_admin_id ON " . $table_prefix . "articles_reviews (admin_id) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN user_email VARCHAR(128) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN admin_id_approved_by INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN admin_id_approved_by INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN admin_id_approved_by INTEGER  ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN admin_id_approved_by INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN admin_id_modified_by INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN admin_id_modified_by INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN admin_id_modified_by INTEGER  ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN admin_id_modified_by INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN date_modified DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN date_modified TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN date_modified DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN date_modified TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN date_approved DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN date_approved TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN date_approved DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "articles_reviews ADD COLUMN date_approved TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.28");
	}

	if (comp_vers("3.5.29", $current_db_version) == 1) {
		
		$sql  = " SELECT category_id, user_types_all, allowed_post, allowed_post_types_all";
		$sql .= " FROM " . $table_prefix . "categories ";
		$db->query($sql);
		while ($db->next_record()) {
			$category_id = $db->f("category_id");
			$user_types_all = $db->f("user_types_all");
			
			$access_level = 0;
			$guest_access_level = 0;
			if ($user_types_all) {
				$access_level = 7;
				$guest_access_level = 7;
			}
			
			$allowed_post = $db->f("allowed_post");
			$allowed_post_types_all = $db->f("allowed_post_types_all");
			if ($allowed_post && $allowed_post_types_all) {
				$access_level += 8;
			}

			$sql  = " UPDATE  ". $table_prefix . "categories ";
			$sql .= " SET guest_access_level=" . $db->tosql($guest_access_level, INTEGER);
			$sql .= ", access_level=" . $db->tosql($access_level, INTEGER);
			$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
			$sqls[] = $sql;
		}		
		
		$acl_tables = array(
			7  => "categories_user_types",
			8  => "categories_post_types"
		);
		
		$acl_pairs = array();
		
		foreach ($acl_tables AS $acl_value => $acl_table) {
			$sql = " SELECT category_id, user_type_id FROM " . $table_prefix . $acl_table;
			$db->query($sql);
			while ($db->next_record()) {
				$category_id     = $db->f("category_id");
				$user_type_id = $db->f("user_type_id");
				if (isset($acl_pairs[$user_type_id]) && isset($acl_pairs[$user_type_id][$category_id])) {
					$acl_pairs[$user_type_id][$category_id] += $acl_value;
				} else {
					$acl_pairs[$user_type_id][$category_id] = $acl_value;
				}
			}
		}
		$sqls[] = " DELETE FROM " . $table_prefix . "categories_user_types";
		$sqls[] = " DROP TABLE " . $table_prefix . "categories_post_types";
		
		foreach ($acl_pairs AS $user_type_id => $tmp) {
			foreach ($tmp AS $category_id => $access_level) {
				$sql  = " INSERT INTO  ". $table_prefix . "categories_user_types ";
				$sql .= "(user_type_id, category_id, access_level) VALUES (";
				$sql .= $db->tosql($user_type_id, INTEGER) . ",";
				$sql .= $db->tosql($category_id, INTEGER) . ",";
				$sql .= $db->tosql($access_level, INTEGER) . ")";
				$sqls[] = $sql;
			}
		}		
		
		$sql  = " SELECT category_id, allowed_view, allowed_post ";
		$sql .= " FROM " . $table_prefix . "ads_categories ";
		$db->query($sql);
		while ($db->next_record()) {
			$category_id = $db->f("category_id");
			$allowed_view = $db->f("allowed_view");
			$allowed_post = $db->f("allowed_post");
			
			$access_level = 0;
			$guest_access_level = 0;
			if ($allowed_view) {
				$access_level = 7;
				$guest_access_level = 7;
			}			
			if ($allowed_post) {
				$access_level += 8;
			}

			$sql  = " UPDATE  ". $table_prefix . "ads_categories ";
			$sql .= " SET guest_access_level=" . $db->tosql($guest_access_level, INTEGER);
			$sql .= ", access_level=" . $db->tosql($access_level, INTEGER);
			$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
			$sqls[] = $sql;
		}
				
		$sql  = " SELECT forum_id, allowed_view, allowed_view_topics, allowed_view_topic, allowed_post_topics, allowed_post_replies, allowed_attachments, ";
		$sql .= " view_forum_types_all, view_topics_types_all, view_topic_types_all, post_topics_types_all, post_replies_types_all, attachments_types_all";
		$sql .= " FROM " . $table_prefix . "forum_list ";
		$db->query($sql);
		while ($db->next_record()) {
			$access_level = 0;
			$guest_access_level = 0;
			$forum_id = $db->f("forum_id");
			$forums_ids[] = $forum_id;	
			$allowed_view         = $db->f("allowed_view");
			$view_forum_types_all = $db->f("view_forum_types_all");
			if ($allowed_view == 1 ) {
				$access_level += 1;
				$guest_access_level += 1;
			} elseif ($allowed_view == 2 && $view_forum_types_all ) {
				$access_level += 1;
			}
			
			$allowed_view_topics  = $db->f("allowed_view_topics");
			$view_topics_types_all = $db->f("view_topics_types_all");
			if ($allowed_view_topics == 1 ) {
				$access_level += 2;
				$guest_access_level += 2;
			} elseif ($allowed_view_topics == 2 && $view_topics_types_all ) {
				$access_level += 2;
			}
			
			$allowed_view_topic  = $db->f("allowed_view_topic");
			$view_topic_types_all = $db->f("view_topic_types_all");
			if ($allowed_view_topic == 1 ) {
				$access_level += 4;
				$guest_access_level += 4;
			} elseif ($allowed_view_topic == 2 && $view_topic_types_all ) {
				$access_level += 4;
			}
			
			$allowed_post_topics  = $db->f("allowed_post_topics");
			$post_topics_types_all = $db->f("post_topics_types_all");
			if ($allowed_post_topics == 1 ) {
				$access_level += 8;
				$guest_access_level += 8;
			} elseif ($allowed_post_topics == 2 && $post_topics_types_all ) {
				$access_level += 8;
			}
			
			$allowed_post_replies   = $db->f("allowed_post_replies");
			$post_replies_types_all = $db->f("post_replies_types_all");
			if ($allowed_post_replies == 1 ) {
				$access_level += 16;
				$guest_access_level += 16;
			} elseif ($allowed_post_replies == 2 && $post_replies_types_all) {
				$access_level += 16;
			}
			
			$allowed_attachments   = $db->f("allowed_attachments");
			$attachments_types_all = $db->f("attachments_types_all");
			if ($allowed_attachments == 1 && $attachments_types_all) {				
				$access_level += 16;
			}
			
			$sql  = " UPDATE  ". $table_prefix . "forum_list ";
			$sql .= " SET guest_access_level=" . $db->tosql($guest_access_level, INTEGER);
			$sql .= ", access_level=" . $db->tosql($access_level, INTEGER);
			$sql .= " WHERE forum_id=" . $db->tosql($forum_id, INTEGER);
			$sqls[] = $sql;
		}
				
		$acl_tables = array(
			1  => "forum_view_types",
			2  => "forum_view_topics",
			4  => "forum_view_topic",
			8  => "forum_post_replies",
			16 => "forum_post_topics",
			32 => "forum_attachments_types"
		);
		
		$acl_pairs = array();
		
		foreach ($acl_tables AS $acl_value => $acl_table) {
			$sql = " SELECT forum_id, user_type_id FROM " . $table_prefix . $acl_table;
			$db->query($sql);
			while ($db->next_record()) {
				$forum_id     = $db->f("forum_id");
				$user_type_id = $db->f("user_type_id");
				if (isset($acl_pairs[$user_type_id]) && isset($acl_pairs[$user_type_id][$forum_id])) {
					$acl_pairs[$user_type_id][$forum_id] += $acl_value;
				} else {
					$acl_pairs[$user_type_id][$forum_id] = $acl_value;
				}
			}
			
		}
		foreach ($acl_pairs AS $user_type_id => $tmp) {
			foreach ($tmp AS $forum_id => $access_level) {
				$sql  = " INSERT INTO  ". $table_prefix . "forum_user_types ";
				$sql .= "(user_type_id, forum_id, access_level) VALUES (";
				$sql .= $db->tosql($user_type_id, INTEGER) . ",";
				$sql .= $db->tosql($forum_id, INTEGER) . ",";
				$sql .= $db->tosql($access_level, INTEGER) . ")";
				$sqls[] = $sql;
			}
		}
		
		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.29");
	}
	
	
	if (comp_vers("3.5.30", $current_db_version) == 1) {
		$sqls[] = " ALTER TABLE ". $table_prefix . "categories DROP COLUMN user_types_all";
		$sqls[] = " ALTER TABLE ". $table_prefix . "categories DROP COLUMN allowed_post";
		$sqls[] = " ALTER TABLE ". $table_prefix . "categories DROP COLUMN allowed_post_types_all";	
		$sqls[] = " ALTER TABLE ". $table_prefix . "items DROP COLUMN user_types_all";
		
		$sqls[] = " ALTER TABLE ". $table_prefix . "ads_categories DROP COLUMN allowed_view";
		$sqls[] = " ALTER TABLE ". $table_prefix . "ads_categories DROP COLUMN allowed_post";
			
		$sqls[] = " ALTER TABLE ". $table_prefix . "forum_list DROP COLUMN allowed_view";
		$sqls[] = " ALTER TABLE ". $table_prefix . "forum_list DROP COLUMN allowed_view_topics";
		$sqls[] = " ALTER TABLE ". $table_prefix . "forum_list DROP COLUMN allowed_view_topic";
		$sqls[] = " ALTER TABLE ". $table_prefix . "forum_list DROP COLUMN allowed_post_topics";
		$sqls[] = " ALTER TABLE ". $table_prefix . "forum_list DROP COLUMN allowed_post_replies";
		$sqls[] = " ALTER TABLE ". $table_prefix . "forum_list DROP COLUMN allowed_attachments";
		
		$sqls[] = " ALTER TABLE ". $table_prefix . "forum_list DROP COLUMN view_forum_types_all";
		$sqls[] = " ALTER TABLE ". $table_prefix . "forum_list DROP COLUMN view_topics_types_all";
		$sqls[] = " ALTER TABLE ". $table_prefix . "forum_list DROP COLUMN view_topic_types_all";
		$sqls[] = " ALTER TABLE ". $table_prefix . "forum_list DROP COLUMN post_topics_types_all";
		$sqls[] = " ALTER TABLE ". $table_prefix . "forum_list DROP COLUMN post_replies_types_all";		
		$sqls[] = " ALTER TABLE ". $table_prefix . "forum_list DROP COLUMN attachments_types_all";
		
		$sqls[] = " DROP TABLE " . $table_prefix . "forum_view_types";
		$sqls[] = " DROP TABLE " . $table_prefix . "forum_view_topics";
		$sqls[] = " DROP TABLE " . $table_prefix . "forum_view_topic";
		$sqls[] = " DROP TABLE " . $table_prefix . "forum_post_replies";
		$sqls[] = " DROP TABLE " . $table_prefix . "forum_post_topics";
		$sqls[] = " DROP TABLE " . $table_prefix . "forum_attachments_types";
		
		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.30");
	}
	
	if (comp_vers("3.5.31", $current_db_version) == 1) {
		// update subscriptions for current users and orders items
		$sql = " SELECT * FROM " . $table_prefix . "subscriptions ";
		$db->query($sql);
		while ($db->next_record()) {
			$subscription_id = $db->f("subscription_id");
			$user_type_id = $db->f("user_type_id");
			$sql  = " UPDATE " . $table_prefix . "users ";
			$sql .= " SET subscription_id=" . $db->tosql($subscription_id, INTEGER);
			$sql .= " WHERE user_type_id=" . $db->tosql($user_type_id, INTEGER);
			$sqls[] = $sql;

			$sql  = " UPDATE " . $table_prefix . "orders_items ";
			$sql .= " SET subscription_id=" . $db->tosql($subscription_id, INTEGER);
			$sql .= " WHERE user_type_id=" . $db->tosql($user_type_id, INTEGER);
			$sql .= " AND is_subscription=1 ";
			$sqls[] = $sql;
		}
		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.31");
	}

	if (comp_vers("3.5.32", $current_db_version) == 1) {
		// add new articles permissions for admin
		$permissions = array("articles_statuses", "articles_reviews", "articles_reviews_settings");
		$sql = " SELECT privilege_id FROM  " . $table_prefix . "admin_privileges_settings WHERE block_name='articles' AND permission=1 ";
		$db->query($sql);
		while ($db->next_record()) {
			$privilege_id = $db->f("privilege_id");
			for ($i = 0; $i < sizeof($permissions); $i++) {
				$sql  = " DELETE FROM " . $table_prefix . "admin_privileges_settings ";
				$sql .= " WHERE privilege_id=" . $db->tosql($privilege_id, INTEGER);
				$sql .= " AND block_name=" . $db->tosql($permissions[$i], TEXT);
				$sqls[] = $sql;
				$sql  = " INSERT INTO " . $table_prefix . "admin_privileges_settings (privilege_id, block_name, permission) VALUES (";
				$sql .= $db->tosql($privilege_id, INTEGER) . ", '" . $permissions[$i] . "',1)";
				$sqls[] = $sql;
			}
		}

		// add new navigation permissions for admin
		$permissions = array("site_navigation", "footer_links");
		$sql = " SELECT privilege_id FROM  " . $table_prefix . "admin_privileges_settings WHERE block_name='layouts' AND permission=1 ";
		$db->query($sql);
		while ($db->next_record()) {
			$privilege_id = $db->f("privilege_id");
			for ($i = 0; $i < sizeof($permissions); $i++) {
				$sql  = " DELETE FROM " . $table_prefix . "admin_privileges_settings ";
				$sql .= " WHERE privilege_id=" . $db->tosql($privilege_id, INTEGER);
				$sql .= " AND block_name=" . $db->tosql($permissions[$i], TEXT);
				$sqls[] = $sql;
				$sql  = " INSERT INTO " . $table_prefix . "admin_privileges_settings (privilege_id, block_name, permission) VALUES (";
				$sql .= $db->tosql($privilege_id, INTEGER) . ", '" . $permissions[$i] . "',1)";
				$sqls[] = $sql;
			}
		}

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN quantity_action TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN quantity_action SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN quantity_action BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN quantity_action SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items_properties SET quantity_action=1 ";


		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.32");
	}

	if (comp_vers("3.5.33", $current_db_version) == 1) {
		// ads changes
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN publish_price DOUBLE(16,2) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN publish_price FLOAT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN publish_price FLOAT",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_categories ADD COLUMN publish_price DOUBLE default 0",
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "ads_categories SET publish_price=0 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN is_shown TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN is_shown SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN is_shown BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN is_shown SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "ads_items SET is_shown=1 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "ads_items_is_shown ON " . $table_prefix . "ads_items (is_shown) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN is_shown_internal TINYINT DEFAULT '1'",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN is_shown_internal SMALLINT DEFAULT '1'",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN is_shown_internal BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN is_shown_internal SMALLINT DEFAULT 1"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "ads_items SET is_shown_internal=1 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "ads_items_is_shown_internal ON " . $table_prefix . "ads_items (is_shown_internal) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN days_run INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN days_run INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN days_run INTEGER  ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN days_run INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN is_special TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN is_special SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN is_special BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN is_special SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "UPDATE " . $table_prefix . "ads_items SET is_special=0 ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "ads_items_is_special ON " . $table_prefix . "ads_items (is_special) ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "ads_items_is_hot ON " . $table_prefix . "ads_items (is_hot) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_description TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_description TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_description LONGTEXT",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_description LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_days_run INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_days_run INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_days_run INTEGER  ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_days_run INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_date_start DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_date_start TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_date_start DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_date_start TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "CREATE INDEX " . $table_prefix . "ads_items_special_start ON " . $table_prefix . "ads_items (special_date_start) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_date_end DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_date_end TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_date_end DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN special_date_end TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "CREATE INDEX " . $table_prefix . "ads_items_special_end ON " . $table_prefix . "ads_items (special_date_end) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN hot_days_run INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN hot_days_run INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN hot_days_run INTEGER  ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN hot_days_run INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN hot_date_start DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN hot_date_start TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN hot_date_start DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN hot_date_start TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "CREATE INDEX " . $table_prefix . "ads_items_hot_start ON " . $table_prefix . "ads_items (hot_date_start) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN hot_date_end DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN hot_date_end TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN hot_date_end DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "ads_items ADD COLUMN hot_date_end TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = "CREATE INDEX " . $table_prefix . "ads_items_hot_end ON " . $table_prefix . "ads_items (hot_date_end) ";

		// ads days tables
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "ads_days (
      `days_id` INT(11) NOT NULL AUTO_INCREMENT,
      `days_number` INT(11),
      `days_title` VARCHAR(64),
      `publish_price` DOUBLE(16,2)
      ,PRIMARY KEY (days_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_days START 9";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_days (
      days_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "ads_days'),
      days_number INT4,
      days_title VARCHAR(64),
      publish_price FLOAT4
      ,PRIMARY KEY (days_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_days (
      [days_id]  COUNTER  NOT NULL,
      [days_number] INTEGER,
      [days_title] VARCHAR(64),
      [publish_price] FLOAT
      ,PRIMARY KEY (days_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "ads_days (
      days_id INTEGER NOT NULL,
      days_number INTEGER,
      days_title VARCHAR(64),
      publish_price DOUBLE
      ,PRIMARY KEY (days_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_days AS INTEGER START WITH 9 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "ads_days NO CASCADE BEFORE INSERT ON " . $table_prefix . "ads_days REFERENCING NEW AS newr_" . $table_prefix . "ads_days FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "ads_days.days_id IS NULL ) begin atomic set newr_" . $table_prefix . "ads_days.days_id = nextval for seq_" . $table_prefix . "ads_days; end";
		}

		$sqls[] = "INSERT INTO " . $table_prefix . "ads_days (days_id,days_number,days_title,publish_price) VALUES (1 , 1 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_days (days_id,days_number,days_title,publish_price) VALUES (2 , 3 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_days (days_id,days_number,days_title,publish_price) VALUES (3 , 7 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_days (days_id,days_number,days_title,publish_price) VALUES (4 , 14 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_days (days_id,days_number,days_title,publish_price) VALUES (5 , 30 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_days (days_id,days_number,days_title,publish_price) VALUES (6 , 60 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_days (days_id,days_number,days_title,publish_price) VALUES (7 , 90 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_days (days_id,days_number,days_title,publish_price) VALUES (8 , 180 , NULL , NULL )";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "ads_hot_days (
      `days_id` INT(11) NOT NULL AUTO_INCREMENT,
      `days_number` INT(11),
      `days_title` VARCHAR(64),
      `publish_price` DOUBLE(16,2)
      ,PRIMARY KEY (days_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_hot_days START 6";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_hot_days (
      days_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "ads_hot_days'),
      days_number INT4,
      days_title VARCHAR(64),
      publish_price FLOAT4
      ,PRIMARY KEY (days_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_hot_days (
      [days_id]  COUNTER  NOT NULL,
      [days_number] INTEGER,
      [days_title] VARCHAR(64),
      [publish_price] FLOAT
      ,PRIMARY KEY (days_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "ads_hot_days (
      days_id INTEGER NOT NULL,
      days_number INTEGER,
      days_title VARCHAR(64),
      publish_price DOUBLE
      ,PRIMARY KEY (days_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_hot_days AS INTEGER START WITH 6 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "ads_hot_days NO CASCADE BEFORE INSERT ON " . $table_prefix . "ads_hot_days REFERENCING NEW AS newr_" . $table_prefix . "ads_hot_days FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "ads_hot_days.days_id IS NULL ) begin atomic set newr_" . $table_prefix . "ads_hot_days.days_id = nextval for seq_" . $table_prefix . "ads_hot_days; end";
		}

		$sqls[] = "INSERT INTO " . $table_prefix . "ads_hot_days (days_id,days_number,days_title,publish_price) VALUES (1 , 1 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_hot_days (days_id,days_number,days_title,publish_price) VALUES (2 , 3 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_hot_days (days_id,days_number,days_title,publish_price) VALUES (3 , 7 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_hot_days (days_id,days_number,days_title,publish_price) VALUES (4 , 14 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_hot_days (days_id,days_number,days_title,publish_price) VALUES (5 , 30 , NULL , NULL )";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "ads_special_days (
      `days_id` INT(11) NOT NULL AUTO_INCREMENT,
      `days_number` INT(11),
      `days_title` VARCHAR(64),
      `publish_price` DOUBLE(16,2)
      ,PRIMARY KEY (days_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_special_days START 9";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "ads_special_days (
      days_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "ads_special_days'),
      days_number INT4,
      days_title VARCHAR(64),
      publish_price FLOAT4
      ,PRIMARY KEY (days_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "ads_special_days (
      [days_id]  COUNTER  NOT NULL,
      [days_number] INTEGER,
      [days_title] VARCHAR(64),
      [publish_price] FLOAT
      ,PRIMARY KEY (days_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "ads_special_days (
      days_id INTEGER NOT NULL,
      days_number INTEGER,
      days_title VARCHAR(64),
      publish_price DOUBLE
      ,PRIMARY KEY (days_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "db2") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "ads_special_days AS INTEGER START WITH 6 INCREMENT BY 1 NO CACHE NO CYCLE";
			$sqls[] = "CREATE TRIGGER tr_" . $table_prefix . "ads_spec_12 NO CASCADE BEFORE INSERT ON " . $table_prefix . "ads_special_days REFERENCING NEW AS newr_" . $table_prefix . "ads_special_days FOR EACH ROW MODE DB2SQL WHEN (newr_" . $table_prefix . "ads_special_days.days_id IS NULL ) begin atomic set newr_" . $table_prefix . "ads_special_days.days_id = nextval for seq_" . $table_prefix . "ads_special_days; end";
		}

		$sqls[] = "INSERT INTO " . $table_prefix . "ads_special_days (days_id,days_number,days_title,publish_price) VALUES (1 , 1 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_special_days (days_id,days_number,days_title,publish_price) VALUES (2 , 3 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_special_days (days_id,days_number,days_title,publish_price) VALUES (3 , 7 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_special_days (days_id,days_number,days_title,publish_price) VALUES (4 , 14 , NULL , NULL )";
		$sqls[] = "INSERT INTO " . $table_prefix . "ads_special_days (days_id,days_number,days_title,publish_price) VALUES (5 , 30 , NULL , NULL )";

		// support changes
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN user_rating TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN user_rating SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN user_rating BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN user_rating SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN user_rating_comments VARCHAR(128) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN user_rating_id INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN user_rating_id INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN user_rating_id INTEGER  ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN user_rating_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN user_rating_ip VARCHAR(32) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN admin_rating TINYINT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN admin_rating SMALLINT ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN admin_rating BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN admin_rating SMALLINT "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN admin_rating_comments VARCHAR(128) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN admin_rating_id INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN admin_rating_id INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN admin_rating_id INTEGER  ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN admin_rating_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_messages ADD COLUMN admin_rating_ip VARCHAR(32) ";


		// support_statuses notification fields
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_notify TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_notify SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_notify BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_notify SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_to VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_from VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_cc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_bcc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_reply_to VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_return_path VARCHAR(64) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_mail_type TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_mail_type SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_mail_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_mail_type SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_subject VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_body TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_body TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_body LONGTEXT",
			"db2"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN admin_body LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_notify TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_notify SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_notify BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_notify SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_to VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_from VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_cc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_bcc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_reply_to VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_return_path VARCHAR(64) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_mail_type TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_mail_type SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_mail_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_mail_type SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_subject VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_body TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_body TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_body LONGTEXT",
			"db2"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_by_body LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_notify TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_notify SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_notify BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_notify SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_to VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_from VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_cc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_bcc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_reply_to VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_return_path VARCHAR(64) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_mail_type TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_mail_type SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_mail_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_mail_type SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_subject VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_body TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_body TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_body LONGTEXT",
			"db2"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN manager_to_body LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_notify TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_notify SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_notify BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_notify SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_to VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_from VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_cc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_bcc VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_reply_to VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_return_path VARCHAR(64) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_mail_type TINYINT DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_mail_type SMALLINT DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_mail_type BYTE ",
			"db2"     => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_mail_type SMALLINT DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_subject VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_body TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_body TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_body LONGTEXT",
			"db2"  => "ALTER TABLE " . $table_prefix . "support_statuses ADD COLUMN user_body LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.33");
	}
	
	if (comp_vers("3.5.34", $current_db_version) == 1) {
		$sql  = " SELECT category_id, allowed_view, allowed_post ";
		$sql .= " FROM " . $table_prefix . "articles_categories ";
		$db->query($sql);
		while ($db->next_record()) {
			$category_id = $db->f("category_id");
			$allowed_view = $db->f("allowed_view");
			$allowed_post = $db->f("allowed_post");
			
			$access_level = 0;
			$guest_access_level = 0;
			if ($allowed_view) {
				$access_level = 7;
				$guest_access_level = 7;
			}			
			if ($allowed_post) {
				$access_level += 8;
			}

			$sql  = " UPDATE  ". $table_prefix . "articles_categories ";
			$sql .= " SET guest_access_level=" . $db->tosql($guest_access_level, INTEGER);
			$sql .= ", access_level=" . $db->tosql($access_level, INTEGER);
			$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
			$sqls[] = $sql;
		}
		
		$sql  = " SELECT category_id, allowed_view ";
		$sql .= " FROM " . $table_prefix . "manuals_categories ";
		$db->query($sql);
		while ($db->next_record()) {
			$category_id = $db->f("category_id");
			$allowed_view = $db->f("allowed_view");
			
			$access_level = 0;
			$guest_access_level = 0;
			if ($allowed_view) {
				$access_level = 7;
				$guest_access_level = 7;
			}

			$sql  = " UPDATE  ". $table_prefix . "manuals_categories ";
			$sql .= " SET guest_access_level=" . $db->tosql($guest_access_level, INTEGER);
			$sql .= ", access_level=" . $db->tosql($access_level, INTEGER);
			$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
			$sqls[] = $sql;
		}
		$sqls[] = " ALTER TABLE ". $table_prefix . "articles_categories DROP COLUMN allowed_view";
		$sqls[] = " ALTER TABLE ". $table_prefix . "articles_categories DROP COLUMN allowed_post";
		$sqls[] = " ALTER TABLE ". $table_prefix . "manuals_categories DROP COLUMN allowed_view";
		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.34");
	}

	if (comp_vers("3.5.35", $current_db_version) == 1) {
		$sqls[] = " DROP TABLE " . $table_prefix . "banners_sites ";

		// re-apply multi-sites for banners
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "banners_sites (";
		$mysql_sql .= "  `banner_id` INT(11) NOT NULL default '0',";
		$mysql_sql .= "  `site_id` INT(11) NOT NULL default '0'";
		$mysql_sql .= "  ,PRIMARY KEY (banner_id,site_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "banners_sites (";
		$postgre_sql .= "  banner_id INT4 NOT NULL default '0',";
		$postgre_sql .= "  site_id INT4 NOT NULL default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (banner_id,site_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "banners_sites (";
		$access_sql .= "  [banner_id] INTEGER NOT NULL,";
		$access_sql .= "  [site_id] INTEGER NOT NULL";
		$access_sql .= "  ,PRIMARY KEY (banner_id,site_id))";

		$db2_sql  = "CREATE TABLE " . $table_prefix . "banners_sites (";
		$db2_sql .= "  banner_id INTEGER NOT NULL default 0,";
		$db2_sql .= "  site_id INTEGER NOT NULL default 0";
		$db2_sql .= "  ,PRIMARY KEY (banner_id,site_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql, "db2" => $db2_sql);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.35");
	}

	if (comp_vers("3.5.36", $current_db_version) == 1) {
		$sqls[] = "ALTER TABLE " . $table_prefix . "forum_attachments ADD COLUMN session_id VARCHAR(32) ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "forum_attachments_session ON " . $table_prefix . "forum_attachments (session_id) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "support_attachments ADD COLUMN session_id VARCHAR(32) ";
		$sqls[] = "CREATE INDEX " . $table_prefix . "support_attachments_session ON " . $table_prefix . "support_attachments (session_id) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.36");
	}

	if (comp_vers("3.5.37", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN user_notes TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN user_notes TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN user_notes LONGTEXT",
			"db2"     => "ALTER TABLE " . $table_prefix . "users ADD COLUMN user_notes LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN admin_notes TEXT",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN admin_notes TEXT",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN admin_notes LONGTEXT",
			"db2"     => "ALTER TABLE " . $table_prefix . "users ADD COLUMN admin_notes LONG VARCHAR"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.37");
	}


	if (comp_vers("3.5.38", $current_db_version) == 1) {

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_properties ADD COLUMN property_value_id INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_properties ADD COLUMN property_value_id INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_properties ADD COLUMN property_value_id INTEGER  ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders_properties ADD COLUMN property_value_id INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		run_queries($sqls, $queries_success, $queries_failed, $errors);

		// move value ids to new column
		$sql  = " SELECT op.order_property_id, op.property_id, op.property_value ";
		$sql .= " FROM (" . $table_prefix . "orders_properties op ";
		$sql .= " INNER JOIN " . $table_prefix . "order_custom_properties ocp ON op.property_id=ocp.property_id)";
		$sql .= " WHERE (ocp.control_type='CHECKBOXLIST' OR ocp.control_type='RADIOBUTTON' OR ocp.control_type='LISTBOX') ";
		$sql .= " AND (op.property_value_id IS NULL OR op.property_value_id=0) ";
		$dbs->query($sql);
		while ($dbs->next_record()) {
			$order_property_id = $dbs->f("order_property_id");
			$property_value_id = $dbs->f("property_value");
			if (is_numeric($property_value_id)) {
				$sql  = " SELECT property_value FROM " . $table_prefix . "order_custom_values ";
				$sql .= " WHERE property_value_id=" . $db->tosql($property_value_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$property_value = $db->f("property_value");
					$sql  = " UPDATE " . $table_prefix . "orders_properties ";
					$sql .= " SET property_value_id=" . $db->tosql($property_value_id, INTEGER);
					$sql .= " , property_value=" . $db->tosql($property_value, TEXT);
					$sql .= " WHERE order_property_id=" . $db->tosql($order_property_id, INTEGER);
					$sqls[] = $sql;

					run_queries($sqls, $queries_success, $queries_failed, $errors);
				}
			}
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.38");
	}


	if (comp_vers("3.5.39", $current_db_version) == 1) {
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN admin_id_added_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN admin_id_added_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN admin_id_added_by INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN admin_id_added_by INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN admin_id_modified_by INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN admin_id_modified_by INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN admin_id_modified_by INTEGER ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN admin_id_modified_by INTEGER DEFAULT 0"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN date_modified DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN date_modified TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN date_modified DATETIME ",
			"db2"     => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN date_modified TIMESTAMP"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.5.39");
	}

	if (comp_vers("3.6", $current_db_version) == 1)
	{
		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.6");
	}

	
?>