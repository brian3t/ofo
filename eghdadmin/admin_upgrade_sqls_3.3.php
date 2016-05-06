<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_upgrade_sqls_3.3.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	check_admin_security("system_upgrade");

	if (comp_vers("3.2.1", $current_db_version) == 1)
	{
		if ($db_type == "mysql") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_parameters MODIFY COLUMN parameter_source TEXT DEFAULT NULL";
		} elseif ($db_type == "access") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "payment_parameters ALTER COLUMN parameter_source LONGTEXT";
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.1");
	}

	if (comp_vers("3.2.2", $current_db_version) == 1)
	{
		// new libraries to handle capture, refund and void requests
		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN capture_php_lib VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN refund_php_lib VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN void_php_lib VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN status_type VARCHAR(16) ";

		// new orders authorization fields
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN authorization_code VARCHAR(128) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN avs_response_code VARCHAR(64) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN avs_message VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN avs_address_match VARCHAR(16) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN avs_zip_match VARCHAR(16) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN cvv2_match VARCHAR(16) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN secure_3d_check VARCHAR(16) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN secure_3d_status VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN secure_3d_eci VARCHAR(16) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN secure_3d_cavv VARCHAR(128) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN secure_3d_xid VARCHAR(128) ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN shipping_type_code VARCHAR(64) ";

		// restrictions by user types
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN user_types_all INT(11) DEFAULT '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN user_types_all INT4 DEFAULT '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN user_types_all INTEGER"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "payment_systems SET user_types_all=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN user_types_all INT(11) DEFAULT '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN user_types_all INT4 DEFAULT '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "shipping_types ADD COLUMN user_types_all INTEGER"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "shipping_types SET user_types_all=1 ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "payment_user_types (";
		$mysql_sql .= "  `payment_id` INT(11) default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) default '0'";
		$mysql_sql .= "  ,KEY payment_id (payment_id)";
		$mysql_sql .= "  ,KEY user_type_id (user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "payment_user_types (";
		$postgre_sql .= "  payment_id INT4 default '0',";
		$postgre_sql .= "  user_type_id INT4 default '0')";

		$access_sql  = "CREATE TABLE " . $table_prefix . "payment_user_types (";
		$access_sql .= "  [payment_id] INTEGER,";
		$access_sql .= "  [user_type_id] INTEGER)";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "payment_user_types_payment_id ON " . $table_prefix . "payment_user_types (payment_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "payment_user_types_user__34 ON " . $table_prefix . "payment_user_types (user_type_id)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_users (";
		$mysql_sql .= "  `shipping_type_id` INT(11) default '0',";
		$mysql_sql .= "  `user_type_id` INT(11) default '0'";
		$mysql_sql .= "  ,KEY shipping_type_id (shipping_type_id)";
		$mysql_sql .= "  ,KEY user_type_id (user_type_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_users (";
		$postgre_sql .= "  shipping_type_id INT4 default '0',";
		$postgre_sql .= "  user_type_id INT4 default '0')";

		$access_sql  = "CREATE TABLE " . $table_prefix . "shipping_types_users (";
		$access_sql .= "  [shipping_type_id] INTEGER,";
		$access_sql .= "  [user_type_id] INTEGER)";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "shipping_types_users_shi_46 ON " . $table_prefix . "shipping_types_users (shipping_type_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "shipping_types_users_use_47 ON " . $table_prefix . "shipping_types_users (user_type_id)";
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.2");
	}

	if (comp_vers("3.2.3", $current_db_version) == 1)
	{
		$sqls[] = "ALTER TABLE " . $table_prefix . "orders ADD COLUMN secure_3d_md VARCHAR(255) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.3");
	}

	if (comp_vers("3.2.4", $current_db_version) == 1)
	{
		$mysql_sql  = "CREATE TABLE " . $table_prefix . "icons (";
		$mysql_sql .= "  `icon_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `is_active` INT(11) default '1',";
		$mysql_sql .= "  `show_for_user` INT(11) default '1',";
		$mysql_sql .= "  `icon_order` INT(11) default '1',";
		$mysql_sql .= "  `icon_code` VARCHAR(32),";
		$mysql_sql .= "  `icon_image` VARCHAR(255),";
		$mysql_sql .= "  `icon_width` INT(11),";
		$mysql_sql .= "  `icon_height` INT(11),";
		$mysql_sql .= "  `icon_name` VARCHAR(255)";
		$mysql_sql .= "  ,PRIMARY KEY (icon_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "icons START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "icons (";
		$postgre_sql .= "  icon_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "icons'),";
		$postgre_sql .= "  is_active INT4 default '1',";
		$postgre_sql .= "  show_for_user INT4 default '1',";
		$postgre_sql .= "  icon_order INT4 default '1',";
		$postgre_sql .= "  icon_code VARCHAR(32),";
		$postgre_sql .= "  icon_image VARCHAR(255),";
		$postgre_sql .= "  icon_width INT4,";
		$postgre_sql .= "  icon_height INT4,";
		$postgre_sql .= "  icon_name VARCHAR(255)";
		$postgre_sql .= "  ,PRIMARY KEY (icon_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "icons (";
		$access_sql .= "  [icon_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [is_active] INTEGER,";
		$access_sql .= "  [show_for_user] INTEGER,";
		$access_sql .= "  [icon_order] INTEGER,";
		$access_sql .= "  [icon_code] VARCHAR(32),";
		$access_sql .= "  [icon_image] VARCHAR(255),";
		$access_sql .= "  [icon_width] INTEGER,";
		$access_sql .= "  [icon_height] INTEGER,";
		$access_sql .= "  [icon_name] VARCHAR(255)";
		$access_sql .= "  ,PRIMARY KEY (icon_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.4");
	}

	if (comp_vers("3.2.5", $current_db_version) == 1)
	{
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN tiny_image VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "items ADD COLUMN tiny_image_alt VARCHAR(255) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.5");
	}

	if (comp_vers("3.2.6", $current_db_version) == 1)
	{
		
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN stream_video VARCHAR(255) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN stream_video VARCHAR(255) ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN stream_video VARCHAR(255)"
		);
		$sqls[] = $sql_types[$db_type];
		
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN stream_video_width INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN stream_video_width INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN stream_video_width INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN stream_video_height INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN stream_video_height INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN stream_video_height INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.6");
	}

	if (comp_vers("3.2.7", $current_db_version) == 1)
	{
		// payment system images
		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN small_image VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN small_image_alt VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN big_image VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN big_image_alt VARCHAR(255) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.7");
	}

	if (comp_vers("3.2.8", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN quantity INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN quantity INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties ADD COLUMN quantity INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN quantity INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN quantity INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN quantity INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN coupon_tax_free INT(11) DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN coupon_tax_free INT4 DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN coupon_tax_free INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN order_tax_free INT(11) DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN order_tax_free INT4 DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "coupons ADD COLUMN order_tax_free INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "coupons SET order_tax_free=tax_free ";
		$sqls[] = " ALTER TABLE " . $table_prefix . "coupons DROP COLUMN tax_free ";

		if ($db_type == "mysql") {
			$sqls[] = "ALTER TABLE " . $table_prefix . "items MODIFY COLUMN use_stock_level INT(11) DEFAULT '0'";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "orders_coupons (";
		$mysql_sql .= "  `order_coupon_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `order_id` INT(11) default '0',";
		$mysql_sql .= "  `coupon_id` INT(11) default '0',";
		$mysql_sql .= "  `coupon_code` VARCHAR(64),";
		$mysql_sql .= "  `coupon_title` VARCHAR(255),";
		$mysql_sql .= "  `discount_amount` DOUBLE(16,2) default '0',";
		$mysql_sql .= "  `discount_tax_amount` DOUBLE(16,2) default '0'";
		$mysql_sql .= "  ,KEY coupon_id (coupon_id)";
		$mysql_sql .= "  ,KEY order_id (order_id)";
		$mysql_sql .= "  ,PRIMARY KEY (order_coupon_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "orders_coupons START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "orders_coupons (";
		$postgre_sql .= "  order_coupon_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "orders_coupons'),";
		$postgre_sql .= "  order_id INT4 default '0',";
		$postgre_sql .= "  coupon_id INT4 default '0',";
		$postgre_sql .= "  coupon_code VARCHAR(64),";
		$postgre_sql .= "  coupon_title VARCHAR(255),";
		$postgre_sql .= "  discount_amount FLOAT4 default '0',";
		$postgre_sql .= "  discount_tax_amount FLOAT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (order_coupon_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "orders_coupons (";
		$access_sql .= "  [order_coupon_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [order_id] INTEGER,";
		$access_sql .= "  [coupon_id] INTEGER,";
		$access_sql .= "  [coupon_code] VARCHAR(64),";
		$access_sql .= "  [coupon_title] VARCHAR(255),";
		$access_sql .= "  [discount_amount] FLOAT,";
		$access_sql .= "  [discount_tax_amount] FLOAT";
		$access_sql .= "  ,PRIMARY KEY (order_coupon_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_coupons_coupon_id ON " . $table_prefix . "orders_coupons (coupon_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "orders_coupons_order_id ON " . $table_prefix . "orders_coupons (order_id)";
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.8");
	}

	if (comp_vers("3.2.9", $current_db_version) == 1)
	{
		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN image_small VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN image_small_alt VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN image_large VARCHAR(255) ";
		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN image_large_alt VARCHAR(255) ";

		$sqls[] = " UPDATE " . $table_prefix . "payment_systems SET image_small=small_image, image_small_alt=small_image_alt, image_large=big_image, image_large_alt=big_image_alt ";

		$sqls[] = " ALTER TABLE " . $table_prefix . "payment_systems DROP COLUMN small_image ";
		$sqls[] = " ALTER TABLE " . $table_prefix . "payment_systems DROP COLUMN small_image_alt ";
		$sqls[] = " ALTER TABLE " . $table_prefix . "payment_systems DROP COLUMN big_image ";
		$sqls[] = " ALTER TABLE " . $table_prefix . "payment_systems DROP COLUMN big_image_alt ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.9");
	}

	if (comp_vers("3.2.10", $current_db_version) == 1)
	{
		// add new permissions for admin
		$permissions = array("products_settings", "downloadable_products", "advanced_search", "products_report", "product_prices", "product_images", "product_properties", "product_features", "product_related", "product_categories", "product_accessories", "product_releases", "products_order", "products_export", "products_import", "products_export_froogle", "features_groups", "tell_friend", "categories_export", "categories_import", "categories_order", "view_categories", "view_products", "add_categories", "update_categories", "remove_categories", "add_products", "update_products", "remove_products", "duplicate_products", "approve_products");
		$sql = " SELECT privilege_id FROM  " . $table_prefix . "admin_privileges_settings WHERE block_name='products_categories' AND permission=1 ";
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

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.10");
	}

	if (comp_vers("3.2.11", $current_db_version) == 1)
	{
		// add new fields for export/import subscribe email list
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN `exported_email_id` int(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN exported_email_id INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN [exported_email_id] INTEGER "
		);
		$sqls[] = $sql_types[$db_type];


		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN `imported_email_fields` TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN imported_email_fields TEXT ",
			"access"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN [imported_email_fields] LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN `exported_email_fields` TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN exported_email_fields TEXT ",
			"access"  => "ALTER TABLE " . $table_prefix . "admins ADD COLUMN [exported_email_fields] LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.11");
	}

	if (comp_vers("3.2.12", $current_db_version) == 1)
	{
		// re-create quotes structure
		$sqls[] = " DROP TABLE " . $table_prefix . "quotes ";
		$sqls[] = " DROP TABLE " . $table_prefix . "quotes_features ";
		$sqls[] = " DROP TABLE " . $table_prefix . "quotes_history ";
		$sqls[] = " DROP TABLE " . $table_prefix . "quotes_statuses ";

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "quotes (";
		$mysql_sql .= "  `quote_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `support_id` INT(11) default '0',";
		$mysql_sql .= "  `user_id` INT(11) default '0',";
		$mysql_sql .= "  `user_name` VARCHAR(255),";
		$mysql_sql .= "  `user_email` VARCHAR(255),";
		$mysql_sql .= "  `quote_price` DOUBLE(16,2) default '0',";
		$mysql_sql .= "  `quote_status_id` INT(11) default '0',";
		$mysql_sql .= "  `is_paid` INT(11) default '0',";
		$mysql_sql .= "  `minutes_spent` INT(11) default '0',";
		$mysql_sql .= "  `date_due` DATETIME,";
		$mysql_sql .= "  `summary` VARCHAR(255),";
		$mysql_sql .= "  `description` TEXT,";
		$mysql_sql .= "  `admin_id_added_by` INT(11) default '0',";
		$mysql_sql .= "  `admin_id_modified_by` INT(11) default '0',";
		$mysql_sql .= "  `date_added` DATETIME,";
		$mysql_sql .= "  `date_modified` DATETIME";
		$mysql_sql .= "  ,KEY is_paid (is_paid)";
		$mysql_sql .= "  ,PRIMARY KEY (quote_id)";
		$mysql_sql .= "  ,KEY quote_id (quote_id)";
		$mysql_sql .= "  ,KEY quote_status_id (quote_status_id)";
		$mysql_sql .= "  ,KEY support_id (support_id)";
		$mysql_sql .= "  ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "quotes START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "quotes (";
		$postgre_sql .= "  quote_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "quotes'),";
		$postgre_sql .= "  support_id INT4 default '0',";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  user_name VARCHAR(255),";
		$postgre_sql .= "  user_email VARCHAR(255),";
		$postgre_sql .= "  quote_price FLOAT4 default '0',";
		$postgre_sql .= "  quote_status_id INT4 default '0',";
		$postgre_sql .= "  is_paid INT4 default '0',";
		$postgre_sql .= "  minutes_spent INT4 default '0',";
		$postgre_sql .= "  date_due TIMESTAMP,";
		$postgre_sql .= "  summary VARCHAR(255),";
		$postgre_sql .= "  description TEXT,";
		$postgre_sql .= "  admin_id_added_by INT4 default '0',";
		$postgre_sql .= "  admin_id_modified_by INT4 default '0',";
		$postgre_sql .= "  date_added TIMESTAMP,";
		$postgre_sql .= "  date_modified TIMESTAMP";
		$postgre_sql .= "  ,PRIMARY KEY (quote_id))";


		$access_sql  = "CREATE TABLE " . $table_prefix . "quotes (";
		$access_sql .= "  [quote_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [support_id] INTEGER,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [user_name] VARCHAR(255),";
		$access_sql .= "  [user_email] VARCHAR(255),";
		$access_sql .= "  [quote_price] FLOAT,";
		$access_sql .= "  [quote_status_id] INTEGER,";
		$access_sql .= "  [is_paid] INTEGER,";
		$access_sql .= "  [minutes_spent] INTEGER,";
		$access_sql .= "  [date_due] DATETIME,";
		$access_sql .= "  [summary] VARCHAR(255),";
		$access_sql .= "  [description] LONGTEXT,";
		$access_sql .= "  [admin_id_added_by] INTEGER,";
		$access_sql .= "  [admin_id_modified_by] INTEGER,";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [date_modified] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (quote_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_is_paid ON " . $table_prefix . "quotes (is_paid)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_quote_id ON " . $table_prefix . "quotes (quote_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_quote_status_id ON " . $table_prefix . "quotes (quote_status_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_support_id ON " . $table_prefix . "quotes (support_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_user_id ON " . $table_prefix . "quotes (user_id)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "quotes_events (";
		$mysql_sql .= "  `event_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `quote_id` INT(11) default '0',";
		$mysql_sql .= "  `feature_id` INT(11) default '0',";
		$mysql_sql .= "  `admin_id` INT(11) default '0',";
		$mysql_sql .= "  `status_id_old` INT(11) default '0',";
		$mysql_sql .= "  `status_id_new` INT(11) default '0',";
		$mysql_sql .= "  `event_date` DATETIME,";
		$mysql_sql .= "  `event_name` VARCHAR(255),";
		$mysql_sql .= "  `event_description` TEXT";
		$mysql_sql .= "  ,KEY admin_id (admin_id)";
		$mysql_sql .= "  ,KEY feature_id (feature_id)";
		$mysql_sql .= "  ,PRIMARY KEY (event_id)";
		$mysql_sql .= "  ,KEY quote_id (quote_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "quotes_events START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "quotes_events (";
		$postgre_sql .= "  event_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "quotes_events'),";
		$postgre_sql .= "  quote_id INT4 default '0',";
		$postgre_sql .= "  feature_id INT4 default '0',";
		$postgre_sql .= "  admin_id INT4 default '0',";
		$postgre_sql .= "  status_id_old INT4 default '0',";
		$postgre_sql .= "  status_id_new INT4 default '0',";
		$postgre_sql .= "  event_date TIMESTAMP,";
		$postgre_sql .= "  event_name VARCHAR(255),";
		$postgre_sql .= "  event_description TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (event_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "quotes_events (";
		$access_sql .= "  [event_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [quote_id] INTEGER,";
		$access_sql .= "  [feature_id] INTEGER,";
		$access_sql .= "  [admin_id] INTEGER,";
		$access_sql .= "  [status_id_old] INTEGER,";
		$access_sql .= "  [status_id_new] INTEGER,";
		$access_sql .= "  [event_date] DATETIME,";
		$access_sql .= "  [event_name] VARCHAR(255),";
		$access_sql .= "  [event_description] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (event_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_events_admin_id ON " . $table_prefix . "quotes_events (admin_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_events_feature_id ON " . $table_prefix . "quotes_events (feature_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_events_quote_id ON " . $table_prefix . "quotes_events (quote_id)";
		}


		$mysql_sql  = "CREATE TABLE " . $table_prefix . "quotes_features (";
		$mysql_sql .= "  `feature_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `quote_id` INT(11) default '0',";
		$mysql_sql .= "  `feature_description` TEXT,";
		$mysql_sql .= "  `feature_price` DOUBLE(16,2) default '0',";
		$mysql_sql .= "  `feature_status_id` INT(11) default '0',";
		$mysql_sql .= "  `feature_paid` INT(11) default '0',";
		$mysql_sql .= "  `minutes_spent` INT(11) default '0',";
		$mysql_sql .= "  `date_due` DATETIME,";
		$mysql_sql .= "  `admin_id_added_by` INT(11) default '0',";
		$mysql_sql .= "  `admin_id_modified_by` INT(11) default '0',";
		$mysql_sql .= "  `date_added` DATETIME,";
		$mysql_sql .= "  `date_modified` DATETIME";
		$mysql_sql .= "  ,KEY feature_paid (feature_paid)";
		$mysql_sql .= "  ,KEY feature_status_id (feature_status_id)";
		$mysql_sql .= "  ,PRIMARY KEY (feature_id)";
		$mysql_sql .= "  ,KEY quote_id (quote_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "quotes_features START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "quotes_features (";
		$postgre_sql .= "  feature_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "quotes_features'),";
		$postgre_sql .= "  quote_id INT4 default '0',";
		$postgre_sql .= "  feature_description TEXT,";
		$postgre_sql .= "  feature_price FLOAT4 default '0',";
		$postgre_sql .= "  feature_status_id INT4 default '0',";
		$postgre_sql .= "  feature_paid INT4 default '0',";
		$postgre_sql .= "  minutes_spent INT4 default '0',";
		$postgre_sql .= "  date_due TIMESTAMP,";
		$postgre_sql .= "  admin_id_added_by INT4 default '0',";
		$postgre_sql .= "  admin_id_modified_by INT4 default '0',";
		$postgre_sql .= "  date_added TIMESTAMP,";
		$postgre_sql .= "  date_modified TIMESTAMP";
		$postgre_sql .= "  ,PRIMARY KEY (feature_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "quotes_features (";
		$access_sql .= "  [feature_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [quote_id] INTEGER,";
		$access_sql .= "  [feature_description] LONGTEXT,";
		$access_sql .= "  [feature_price] FLOAT,";
		$access_sql .= "  [feature_status_id] INTEGER,";
		$access_sql .= "  [feature_paid] INTEGER,";
		$access_sql .= "  [minutes_spent] INTEGER,";
		$access_sql .= "  [date_due] DATETIME,";
		$access_sql .= "  [admin_id_added_by] INTEGER,";
		$access_sql .= "  [admin_id_modified_by] INTEGER,";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [date_modified] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (feature_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_features_feature_paid ON " . $table_prefix . "quotes_features (feature_paid)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_features_feature__39 ON " . $table_prefix . "quotes_features (feature_status_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_features_quote_id ON " . $table_prefix . "quotes_features (quote_id)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "quotes_orders (";
		$mysql_sql .= "  `quote_order_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `quote_id` INT(11) default '0',";
		$mysql_sql .= "  `feature_id` INT(11) default '0',";
		$mysql_sql .= "  `order_id` INT(11) default '0',";
		$mysql_sql .= "  `order_item_id` INT(11) default '0'";
		$mysql_sql .= "  ,KEY feature_id (feature_id)";
		$mysql_sql .= "  ,KEY order_id (order_id)";
		$mysql_sql .= "  ,KEY order_item_id (order_item_id)";
		$mysql_sql .= "  ,PRIMARY KEY (quote_order_id)";
		$mysql_sql .= "  ,KEY quote_id (quote_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "quotes_orders START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "quotes_orders (";
		$postgre_sql .= "  quote_order_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "quotes_orders'),";
		$postgre_sql .= "  quote_id INT4 default '0',";
		$postgre_sql .= "  feature_id INT4 default '0',";
		$postgre_sql .= "  order_id INT4 default '0',";
		$postgre_sql .= "  order_item_id INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (quote_order_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "quotes_orders (";
		$access_sql .= "  [quote_order_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [quote_id] INTEGER,";
		$access_sql .= "  [feature_id] INTEGER,";
		$access_sql .= "  [order_id] INTEGER,";
		$access_sql .= "  [order_item_id] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (quote_order_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_orders_feature_id ON " . $table_prefix . "quotes_orders (feature_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_orders_order_id ON " . $table_prefix . "quotes_orders (order_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_orders_order_item_id ON " . $table_prefix . "quotes_orders (order_item_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_orders_quote_id ON " . $table_prefix . "quotes_orders (quote_id)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "quotes_statuses (";
		$mysql_sql .= "  `status_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `status_name` VARCHAR(255),";
		$mysql_sql .= "  `status_icon` VARCHAR(255),";
		$mysql_sql .= "  `html_start` VARCHAR(255),";
		$mysql_sql .= "  `html_end` VARCHAR(255),";
		$mysql_sql .= "  `notes` TEXT";
		$mysql_sql .= "  ,PRIMARY KEY (status_id)";
		$mysql_sql .= "  ,KEY quote_status_id (status_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "quotes_statuses START 5";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "quotes_statuses (";
		$postgre_sql .= "  status_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "quotes_statuses'),";
		$postgre_sql .= "  status_name VARCHAR(255),";
		$postgre_sql .= "  status_icon VARCHAR(255),";
		$postgre_sql .= "  html_start VARCHAR(255),";
		$postgre_sql .= "  html_end VARCHAR(255),";
		$postgre_sql .= "  notes TEXT";
		$postgre_sql .= "  ,PRIMARY KEY (status_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "quotes_statuses (";
		$access_sql .= "  [status_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [status_name] VARCHAR(255),";
		$access_sql .= "  [status_icon] VARCHAR(255),";
		$access_sql .= "  [html_start] VARCHAR(255),";
		$access_sql .= "  [html_end] VARCHAR(255),";
		$access_sql .= "  [notes] LONGTEXT";
		$access_sql .= "  ,PRIMARY KEY (status_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "quotes_statuses_quote_st_40 ON " . $table_prefix . "quotes_statuses (status_id)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_items (";
		$mysql_sql .= "  `category_id` INT(11) default '0',";
		$mysql_sql .= "  `item_id` INT(11) default '0',";
		$mysql_sql .= "  `related_order` INT(11) NOT NULL default '1'";
		$mysql_sql .= "  ,KEY category_id (category_id)";
		$mysql_sql .= "  ,KEY item_id (item_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_items (";
		$postgre_sql .= "  category_id INT4 default '0',";
		$postgre_sql .= "  item_id INT4 default '0',";
		$postgre_sql .= "  related_order INT4 NOT NULL default '1')";

		$access_sql  = "CREATE TABLE " . $table_prefix . "articles_categories_items (";
		$access_sql .= "  [category_id] INTEGER,";
		$access_sql .= "  [item_id] INTEGER,";
		$access_sql .= "  [related_order] INTEGER)";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "articles_categories_item_8 ON " . $table_prefix . "articles_categories_items (category_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "articles_categories_item_9 ON " . $table_prefix . "articles_categories_items (item_id)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "articles_items_related (";
		$mysql_sql .= "  `article_id` INT(11) default '0',";
		$mysql_sql .= "  `item_id` INT(11) default '0',";
		$mysql_sql .= "  `related_order` INT(11) NOT NULL default '1'";
		$mysql_sql .= "  ,KEY article_id (article_id)";
		$mysql_sql .= "  ,KEY item_id (item_id))";

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "articles_items_related (";
		$postgre_sql .= "  article_id INT4 default '0',";
		$postgre_sql .= "  item_id INT4 default '0',";
		$postgre_sql .= "  related_order INT4 NOT NULL default '1')";

		$access_sql  = "CREATE TABLE " . $table_prefix . "articles_items_related (";
		$access_sql .= "  [article_id] INTEGER,";
		$access_sql .= "  [item_id] INTEGER,";
		$access_sql .= "  [related_order] INTEGER)";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "articles_items_related_a_10 ON " . $table_prefix . "articles_items_related (article_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "articles_items_related_i_11 ON " . $table_prefix . "articles_items_related (item_id)";
		}

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "header_submenus (";
		$mysql_sql .= "  `submenu_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `menu_id` INT(11) default '0',";
		$mysql_sql .= "  `show_for_user` INT(11) default '1',";
		$mysql_sql .= "  `match_type` INT(11) default '1',";
		$mysql_sql .= "  `submenu_title` VARCHAR(255),";
		$mysql_sql .= "  `submenu_url` VARCHAR(255),";
		$mysql_sql .= "  `submenu_page` VARCHAR(128),";
		$mysql_sql .= "  `submenu_target` VARCHAR(32),";
		$mysql_sql .= "  `submenu_image` VARCHAR(255),";
		$mysql_sql .= "  `submenu_image_active` VARCHAR(255),";
		$mysql_sql .= "  `submenu_order` INT(11) default '0'";
		$mysql_sql .= "  ,KEY menu_id (menu_id)";
		$mysql_sql .= "  ,PRIMARY KEY (submenu_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "header_submenus START 1";
		}

		$postgre_sql  = "CREATE TABLE " . $table_prefix . "header_submenus (";
		$postgre_sql .= "  submenu_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "header_submenus'),";
		$postgre_sql .= "  menu_id INT4 default '0',";
		$postgre_sql .= "  show_for_user INT4 default '1',";
		$postgre_sql .= "  match_type INT4 default '1',";
		$postgre_sql .= "  submenu_title VARCHAR(255),";
		$postgre_sql .= "  submenu_url VARCHAR(255),";
		$postgre_sql .= "  submenu_page VARCHAR(128),";
		$postgre_sql .= "  submenu_target VARCHAR(32),";
		$postgre_sql .= "  submenu_image VARCHAR(255),";
		$postgre_sql .= "  submenu_image_active VARCHAR(255),";
		$postgre_sql .= "  submenu_order INT4 default '0'";
		$postgre_sql .= "  ,PRIMARY KEY (submenu_id))";


		$access_sql  = "CREATE TABLE " . $table_prefix . "header_submenus (";
		$access_sql .= "  [submenu_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [menu_id] INTEGER,";
		$access_sql .= "  [show_for_user] INTEGER,";
		$access_sql .= "  [match_type] INTEGER,";
		$access_sql .= "  [submenu_title] VARCHAR(255),";
		$access_sql .= "  [submenu_url] VARCHAR(255),";
		$access_sql .= "  [submenu_page] VARCHAR(128),";
		$access_sql .= "  [submenu_target] VARCHAR(32),";
		$access_sql .= "  [submenu_image] VARCHAR(255),";
		$access_sql .= "  [submenu_image_active] VARCHAR(255),";
		$access_sql .= "  [submenu_order] INTEGER";
		$access_sql .= "  ,PRIMARY KEY (submenu_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "header_submenus_menu_id ON " . $table_prefix . "header_submenus (menu_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "header_submenus_submenu_page ON " . $table_prefix . "header_submenus (submenu_page)";
		}

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.12");
	}

	if (comp_vers("3.2.13", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "header_submenus ADD COLUMN parent_submenu_id INT(11) DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "header_submenus ADD COLUMN parent_submenu_id INT4 DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "header_submenus ADD COLUMN parent_submenu_id INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "header_submenus SET parent_submenu_id=0 ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "header_links ADD COLUMN submenu_style_name VARCHAR(64) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.13");
	}

	if (comp_vers("3.2.14", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN layout_id INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN layout_id INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN layout_id INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN show_for_user INT(11) DEFAULT '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN show_for_user INT4 DEFAULT '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN show_for_user INTEGER"
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "layouts SET show_for_user=0 ";

		$sqls[] = "ALTER TABLE " . $table_prefix . "layouts ADD COLUMN user_layout_name VARCHAR(255) ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN item_code VARCHAR(64) DEFAULT '' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN item_code VARCHAR(64) DEFAULT '' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN item_code VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "items SET item_code='' ";

		$sqls[] = "CREATE INDEX " . $table_prefix . "items_item_code ON " . $table_prefix . "items (item_code)";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.14");
	}

	if (comp_vers("3.2.15", $current_db_version) == 1)
	{
		$sqls[] = "ALTER TABLE " . $table_prefix . "header_submenus ADD COLUMN submenu_path VARCHAR(255) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.15");
	}


	if (comp_vers("3.2.16", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN item_code VARCHAR(64) DEFAULT '' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN item_code VARCHAR(64) DEFAULT '' ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN item_code VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];
		$sqls[] = " UPDATE " . $table_prefix . "orders_items SET item_code='' ";

		$sqls[] = "CREATE INDEX " . $table_prefix . "orders_items_item_code ON " . $table_prefix . "orders_items (item_code)";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN item_code VARCHAR(64) DEFAULT '' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN item_code VARCHAR(64) DEFAULT '' ",
			"access"  => "ALTER TABLE " . $table_prefix . "items_properties_values ADD COLUMN item_code VARCHAR(64) "
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.16");
	}
	
	if (comp_vers("3.2.17", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN stream_video_preview VARCHAR(255) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN stream_video_preview VARCHAR(255) ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN stream_video_preview VARCHAR(255)"
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'basket_item_name', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'basket_item_price', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'basket_item_quantity', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'basket_item_price_total', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'basket_item_tax_total', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'basket_item_price_incl_tax_total', '1')";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'checkout_item_name', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'checkout_item_price', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'checkout_item_quantity', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'checkout_item_price_total', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'checkout_item_tax_total', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'checkout_item_price_incl_tax_total', '1')";

		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'invoice_item_name', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'invoice_item_price', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'invoice_item_quantity', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'invoice_item_price_total', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'invoice_item_tax_total', '1')";
		$sqls[] = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES ('products', 'invoice_item_price_incl_tax_total', '1')";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.17");
	}

	if (comp_vers("3.2.18", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_points_price INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_points_price INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN is_points_price INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN points_price DOUBLE(16,4) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN points_price FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN points_price FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN reward_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN reward_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN reward_type INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "items ADD COLUMN reward_amount DOUBLE(16,4) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "items ADD COLUMN reward_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "items ADD COLUMN reward_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN total_points DOUBLE(16,4) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN total_points FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN total_points FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN reward_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN reward_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN reward_type INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "users ADD COLUMN reward_amount DOUBLE(16,4) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "users ADD COLUMN reward_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "users ADD COLUMN reward_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN reward_points DOUBLE(16,4) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN reward_points FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN reward_points FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN points_price DOUBLE(16,4) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN points_price FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_items ADD COLUMN points_price FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_points_amount DOUBLE(16,4) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_points_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_points_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_reward_points DOUBLE(16,4) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_reward_points FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_reward_points FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN shipping_points_amount DOUBLE(16,4) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN shipping_points_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN shipping_points_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN properties_points_amount DOUBLE(16,4) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN properties_points_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN properties_points_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN reward_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN reward_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN reward_type INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN reward_amount DOUBLE(16,4) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN reward_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "user_types ADD COLUMN reward_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN reward_type INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN reward_type INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN reward_type INTEGER"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN reward_amount DOUBLE(16,4) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN reward_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "item_types ADD COLUMN reward_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders_properties ADD COLUMN property_points_amount DOUBLE(16,4) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders_properties ADD COLUMN property_points_amount FLOAT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "orders_properties ADD COLUMN property_points_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN stock_level_action INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN stock_level_action INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN stock_level_action INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN points_action INT(11) default '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN points_action INT4 default '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN points_action INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='NEW' WHERE status_name LIKE '%New%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='PAYMENT_INFO' WHERE status_name LIKE '%CC%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='CONFIRMED' WHERE status_name LIKE '%Confirm%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='PAID' WHERE status_name LIKE '%Paid%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='SHIPPED' WHERE status_name LIKE '%Ship%' OR status_name LIKE '%Deliver%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='PENDING' WHERE status_name LIKE '%Pending%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='CLOSED' WHERE status_name LIKE '%Close%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='DECLINED' WHERE status_name LIKE '%Decline%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='FAILED' WHERE status_name LIKE '%Fail%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='DISPATCHED' WHERE status_name LIKE '%Dispatch%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='CANCELLED' WHERE status_name LIKE '%Cancel%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='REFUNDED' WHERE status_name LIKE '%Refund%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='CAPTURED' WHERE status_name LIKE '%Capture%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='VOIDED' WHERE status_name LIKE '%Void%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='AUTHORIZED' WHERE status_name LIKE '%Authorize%' ";
		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_type='OTHER' WHERE status_type='' OR status_type IS NULL ";

		$sql  = " UPDATE " . $table_prefix . "order_statuses SET stock_level_action=1, points_action=1 ";
		$sql .= " WHERE status_type='NEW' OR status_type='PAYMENT_INFO' OR status_type='CONFIRMED' ";
		$sql .= " OR status_type='PAID' OR status_type='SHIPPED' OR status_type='PENDING' ";
		$sql .= " OR status_type='CLOSED' OR status_type='VALIDATED' OR status_type='DISPATCHED' ";
		$sql .= " OR status_type='CAPTURED' OR status_type='AUTHORIZED' OR status_type='OTHER' ";
		$sqls[] = $sql;

		$sql  = " UPDATE " . $table_prefix . "order_statuses SET stock_level_action=-1, points_action=-1 ";
		$sql .= " WHERE status_type='DECLINED' OR status_type='FALIED' OR status_type='CANCELLED' ";
		$sql .= " OR status_type='REFUNDED' OR status_type='VOIDED' ";
		$sqls[] = $sql;

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN status_order INT(11) default '1' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN status_order INT4 default '1' ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN status_order INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
 		$sqls[] = " UPDATE " . $table_prefix . "order_statuses SET status_order=1 ";

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN final_message TEXT ",
			"postgre" => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN final_message TEXT ",
			"access"  => "ALTER TABLE " . $table_prefix . "order_statuses ADD COLUMN final_message LONGTEXT "
		);
		$sqls[] = $sql_types[$db_type];

		$mysql_sql  = "CREATE TABLE " . $table_prefix . "users_points (";
		$mysql_sql .= "  `points_id` INT(11) NOT NULL AUTO_INCREMENT,";
		$mysql_sql .= "  `user_id` INT(11) default '0',";
		$mysql_sql .= "  `order_id` INT(11) default '0',";
		$mysql_sql .= "  `order_item_id` INT(11) default '0',";
		$mysql_sql .= "  `points_amount` DOUBLE(16,4) default '0',";
		$mysql_sql .= "  `points_action` INT(11) default '0',";
		$mysql_sql .= "  `points_type` INT(11) default '0',";
		$mysql_sql .= "  `admin_id_added_by` INT(11) default '0',";
		$mysql_sql .= "  `admin_id_modified_by` INT(11) default '0',";
		$mysql_sql .= "  `date_added` DATETIME,";
		$mysql_sql .= "  `date_modified` DATETIME";
		$mysql_sql .= "  ,KEY date_added (date_added)";
		$mysql_sql .= "  ,KEY order_id (order_id)";
		$mysql_sql .= "  ,KEY order_item_id (order_item_id)";
		$mysql_sql .= "  ,PRIMARY KEY (points_id)";
		$mysql_sql .= "  ,KEY user_id (user_id))";

		if ($db_type == "postgre") {
			$sqls[] = "CREATE SEQUENCE seq_" . $table_prefix . "users_points START 1";
		}
		$postgre_sql  = "CREATE TABLE " . $table_prefix . "users_points (";
		$postgre_sql .= "  points_id INT4 NOT NULL DEFAULT nextval('seq_" . $table_prefix . "users_points'),";
		$postgre_sql .= "  user_id INT4 default '0',";
		$postgre_sql .= "  order_id INT4 default '0',";
		$postgre_sql .= "  order_item_id INT4 default '0',";
		$postgre_sql .= "  points_amount FLOAT4 default '0',";
		$postgre_sql .= "  points_action INT4 default '0',";
		$postgre_sql .= "  points_type INT4 default '0',";
		$postgre_sql .= "  admin_id_added_by INT4 default '0',";
		$postgre_sql .= "  admin_id_modified_by INT4 default '0',";
		$postgre_sql .= "  date_added TIMESTAMP,";
		$postgre_sql .= "  date_modified TIMESTAMP";
		$postgre_sql .= "  ,PRIMARY KEY (points_id))";

		$access_sql  = "CREATE TABLE " . $table_prefix . "users_points (";
		$access_sql .= "  [points_id]  COUNTER  NOT NULL,";
		$access_sql .= "  [user_id] INTEGER,";
		$access_sql .= "  [order_id] INTEGER,";
		$access_sql .= "  [order_item_id] INTEGER,";
		$access_sql .= "  [points_amount] FLOAT,";
		$access_sql .= "  [points_action] INTEGER,";
		$access_sql .= "  [points_type] INTEGER,";
		$access_sql .= "  [admin_id_added_by] INTEGER,";
		$access_sql .= "  [admin_id_modified_by] INTEGER,";
		$access_sql .= "  [date_added] DATETIME,";
		$access_sql .= "  [date_modified] DATETIME";
		$access_sql .= "  ,PRIMARY KEY (points_id))";

		$sql_types = array("mysql" => $mysql_sql, "postgre" => $postgre_sql, "access" => $access_sql);
		$sqls[] = $sql_types[$db_type];

		if ($db_type == "postgre" || $db_type == "access") {
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_points_date_added ON " . $table_prefix . "users_points (date_added)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_points_order_id ON " . $table_prefix . "users_points (order_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_points_order_item_id ON " . $table_prefix . "users_points (order_item_id)";
			$sqls[] = "CREATE INDEX " . $table_prefix . "users_points_user_id ON " . $table_prefix . "users_points (user_id)";
		}

		$sqls[] = "ALTER TABLE " . $table_prefix . "payment_systems ADD COLUMN user_payment_name VARCHAR(255) ";

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.18");
	}

	if (comp_vers("3.2.19", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN is_remote_rss INT(11) DEFAULT 0 ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN is_remote_rss INT4 DEFAULT 0 ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN is_remote_rss INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN remote_rss_url VARCHAR(255) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN remote_rss_url VARCHAR(255) ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN remote_rss_url VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];
		
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN remote_rss_date_updated DATETIME ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN remote_rss_date_updated TIMESTAMP ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN remote_rss_date_updated DATETIME "
		);
		$sqls[] = $sql_types[$db_type];
		
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN remote_rss_ttl INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN remote_rss_ttl INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN remote_rss_ttl INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN remote_rss_refresh_rate INT(11) ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN remote_rss_refresh_rate INT4 ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles_categories ADD COLUMN remote_rss_refresh_rate INTEGER "
		);
		$sqls[] = $sql_types[$db_type];
		
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN is_remote_rss INT(11) DEFAULT '0' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN is_remote_rss INT4 DEFAULT '0' ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN is_remote_rss INTEGER "
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN details_remote_url VARCHAR(255) DEFAULT '' ",
			"postgre" => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN details_remote_url VARCHAR(255) DEFAULT '' ",
			"access"  => "ALTER TABLE " . $table_prefix . "articles ADD COLUMN details_remote_url VARCHAR(255) "
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.19");
	}

	if (comp_vers("3.2.20", $current_db_version) == 1)
	{
		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_buying_tax DOUBLE(16,2) DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_buying_tax FLOAT4 DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN total_buying_tax FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN goods_tax DOUBLE(16,2) DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN goods_tax FLOAT4 DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN goods_tax FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		$sql_types = array(
			"mysql"   => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN goods_points_amount DOUBLE(16,4) DEFAULT '0'",
			"postgre" => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN goods_points_amount FLOAT4 DEFAULT '0'",
			"access"  => "ALTER TABLE " . $table_prefix . "orders ADD COLUMN goods_points_amount FLOAT"
		);
		$sqls[] = $sql_types[$db_type];

		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.2.20");
	}

	if (comp_vers("3.3", $current_db_version) == 1)
	{
		run_queries($sqls, $queries_success, $queries_failed, $errors, "3.3");
	}

?>