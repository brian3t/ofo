<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_export.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	@set_time_limit (900);
	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/url.php");
	include_once ($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once ($root_folder_path . "messages/".$language_code."/download_messages.php");
	include_once("./admin_common.php");

	check_admin_security("import_export");

	$apply_translation = false;
	$eol = get_eol();
	$delimiters_symbols = array("comma" => ",", "tab" => "\t", "semicolon" => ";", "row" => "row", "space" => " ", "newline" => $eol);

	$delimiters = array(array("comma", COMMA_MSG), array("tab", TAB_MSG), array("semicolon", SEMICOLON_MSG));
	$related_delimiters = array(array("row", ROWS_MSG), array("comma", COMMA_MSG), array("tab", TAB_MSG), array("space", SPACE_MSG), array("semicolon", SEMICOLON_MSG), array("newline", NEWLINE_MSG));

	$errors = "";
	$sql_where = "";
	$rnd = get_param("rnd");
	$table = get_param("table");
	$csv_delimiter = get_param("csv_delimiter");
	$related_delimiter = get_param("related_delimiter");
	$delimiter_symbol = isset($delimiters_symbols[$csv_delimiter]) ? $delimiters_symbols[$csv_delimiter] : ",";
	$related_delimiter_symbol = isset($delimiters_symbols[$related_delimiter]) ? $delimiters_symbols[$related_delimiter] : "row";
	$operation = get_param("operation");
	$category_id = get_param("category_id");
	$session_rnd = get_session("session_rnd");
	$id = get_param("id");
	$ids = get_param("ids");
	$s_on = get_param("s_on"); // order number / users online
	$s_ne = get_param("s_ne");
	$s_kw = get_param("s_kw");
	$s_sd = get_param("s_sd"); // start date
	$s_ed = get_param("s_ed"); // end date
	$s_ad = get_param("s_ad"); // users address
	$s_ut = get_param("s_ut"); // user type
	$s_ap = get_param("s_ap"); // approved
	$s_os = get_param("s_os");
	$s_ci = get_param("s_ci");
	$s_si = get_param("s_si");
	$s_ex = get_param("s_ex");
	$s_cct = get_param("s_cct");
	$s_sti = get_param("s_sti");	
	$s_rn = get_param("s_rn"); // registration number
	$s_ap = get_param("s_ap"); // approved
	$s_pi = get_param("s_pi"); // product id

	$s = trim(get_param("s"));
	$sc = get_param("sc");
	$sl = get_param("sl");
	$ss = get_param("ss");
	$ap = get_param("ap");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_export.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_select_href",     "admin_select.php");
	$t->set_var("admin_export_href",     "admin_export.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_users_list_href", "admin_newsletter_users.php");

	$admin_export_custom_url = new VA_URL("admin_export_custom.php", true, array("table"));
	$admin_export_custom_url->add_parameter("table", CONSTANT, $table);
	$t->set_var("admin_export_custom_url", $admin_export_custom_url->get_url());

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($category_id);

	$is_export = true;
	if ($table == "items") {
		include_once("./admin_common.php");
		check_admin_security("products_export");
		include_once("./admin_table_items.php");
		$sql_join_before = "";
		$sql_join        = "";
		if (strlen($id)) {
			$sql_where = " WHERE i.item_id=" . $db->tosql($id, INTEGER);
		} else if (strlen($ids)) {
			$sql_where = " WHERE i.item_id IN (" . $db->tosql($ids, TEXT, false) . ")";
		} else {
			
			$category_id = get_param("category_id");
			$search = (strlen($s) || strlen($sl) || strlen($ss) || strlen($ap)) ? true : false;
			if ($sc) { $category_id = $sc; }
			
			$sa = "";
			
			$where = array();	
			if ($search && $category_id != 0) {
				$where[] = " c.category_id = ic.category_id ";
				$where[] = " (ic.category_id = " . $db->tosql($category_id, INTEGER)
						 . " OR c.category_path LIKE '" . $db->tosql($tree->get_path($category_id), TEXT, false) . "%')";
				$sql_join_before .=	" (( ";
				$sql_join  .= " LEFT JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id) ";
				$sql_join  .= " LEFT JOIN " . $table_prefix . "categories c ON c.category_id = ic.category_id)";
			} elseif (!$search && strlen($category_id)) {
				$where []= " ic.category_id = " . $db->tosql($category_id, INTEGER);
				$sql_join_before .=	" ( ";
				$sql_join  .= " LEFT JOIN " . $table_prefix . "items_categories ic ON i.item_id=ic.item_id) ";
			}
								
			if ($s) {
				$sa = split(" ", $s);
				for($si = 0; $si < sizeof($sa); $si++) {
					$sa[$si] = str_replace("%","\%",$sa[$si]);
					$where[] = " (i.item_name LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'"
							 .  " OR i.item_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%' "
							 .  " OR i.manufacturer_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%')";
				}
			}
			if (strlen($sl)) {
				if ($sl == 1) {
					$where[] = " (i.stock_level>0 OR i.stock_level IS NULL) ";
				} else {
					$where[] = " i.stock_level<1 ";
				}
			}
			if (strlen($ss)) {
				if ($ss == 1) {
					$where[] = " i.is_showing=1 ";
				} else {
					$where[] = " i.is_showing=0 ";
				}
			}
			if (strlen($ap)) {
				if ($ap == 1) {
					$where[] = " i.is_approved=1 ";
				} else {
					$where[] = " i.is_approved=0 ";
				}
			}
			
			if (count($where)) {
				$sql_where = " WHERE " . implode (" AND ", $where);				
			}
			
		}
	} else if ($table == "categories") {
		include_once("./admin_common.php");

		check_admin_security("categories_export");
		include_once("./admin_table_categories.php");
	} else if ($table == "registrations") {
		include_once("./admin_common.php");

		check_admin_security("admin_registration");
		include_once("./admin_table_registrations.php");
		
		$where = "";
		if (strlen($id)) {
			$where = " reg.registration_id=" . $db->tosql($id, INTEGER);
		} else if (strlen($ids)) {
			$where = " reg.registration_id IN (" . $db->tosql($ids, TEXT, false) . ")";
		} else {
			if ($s_rn) {
				if (preg_match("/^(\d+)(,\d+)*$/", $s_rn))	{
					$where  = " (reg.registration_id IN (" . $s_rn . ") ";
					$where .= " OR reg.invoice_number=" . $db->tosql($s_rn, TEXT);
					$where .= " OR reg.serial_number=" . $db->tosql($s_rn, TEXT) . ") ";
				} else {
					$where .= " (reg.invoice_number=" . $db->tosql($s_rn, TEXT);
					$where .= " OR reg.serial_number=" . $db->tosql($s_rn, TEXT) . ") ";
				}
			}
			
			if ($s_pi) {
				if (strlen($where)) { $where .= " AND "; }
				$where .= " reg.item_id=" . $db->tosql($s_pi, INTEGER);
			}
			
			if ($s_ne) {
				if (strlen($where)) { $where .= " AND "; }
				$s_ne_sql = $db->tosql($s_ne, TEXT, false);
				
				$where .= " (u.name LIKE '%" . $s_ne_sql . "%'";
				$name_parts = explode(" ", $s_ne, 2);
				if (sizeof($name_parts) == 1) {
					$where .= " OR u.first_name LIKE '%" . $s_ne_sql . "%'";
					$where .= " OR u.last_name LIKE '%" . $s_ne_sql . "%'";
				} else {
					$where .= " OR (u.first_name LIKE '%" . $db->tosql($name_parts[0], TEXT, false) . "%' ";
					$where .= " AND u.last_name LIKE '%" . $db->tosql($name_parts[1], TEXT, false) . "%') ";
				}
				$where .= ") ";	
			}
			
			if ($s_kw) {
				if (strlen($where)) { $where .= " AND "; }
				$s_kw_sql = $db->tosql($s_kw, TEXT, false);
				$where .= " (reg.item_name LIKE '%" . $s_kw_sql . "%'";
				$where .= " OR reg.item_code LIKE '%" . $s_kw_sql . "%'";
				$where .= " OR it.item_name  LIKE '%" . $s_kw_sql . "%'";
				$where .= " OR it.item_code  LIKE '%" . $s_kw_sql . "%')";
			}
			
			if ($s_sd) {
				if (strlen($where)) { $where .= " AND "; }
				$where .= " reg.date_added>=" . $db->tosql($s_sd, DATE);
			}
			if ($s_ed) {
				if (strlen($where)) { $where .= " AND "; }
				$day_after_end = mktime (0, 0, 0, $s_ed[MONTH], $s_ed[DAY] + 1, $s_ed[YEAR]);
				$where .= " reg.date_added<" . $db->tosql($day_after_end, DATE);
			}		
			
			if (strlen($s_ap)) {
				if (strlen($where)) { $where .= " AND "; }
				if ($s_ap) {
					$where .= " reg.is_approved=1";
				} else {
					$where .= " reg.is_approved=0";
				}
			}
		}
		if ($where) {
			$sql_where = " WHERE " . $where;				
		}
			
	} else if ($table == "users") {
		include_once("./admin_common.php");

		check_admin_security("export_users");
		include_once("./admin_table_users.php");
		if (strlen($id)) {
			$sql_where = " WHERE user_id>" . $db->tosql($id, INTEGER);
		} else if (strlen($ids)) {
			$sql_where = " WHERE user_id IN (" . $db->tosql($ids, TEXT, false) . ")";
		} else {
			if (strlen($s_ne)) {
				if (strlen($sql_where)) { $sql_where .= " AND "; }
				$s_ne_sql = $db->tosql($s_ne, TEXT, false);
				$sql_where .= " (u.email LIKE '%" . $s_ne_sql . "%'";
				$sql_where .= " OR u.login LIKE '%" . $s_ne_sql . "%'";
				$sql_where .= " OR u.name LIKE '%" . $s_ne_sql . "%'";
				$sql_where .= " OR u.first_name LIKE '%" . $s_ne_sql . "%'";
				$sql_where .= " OR u.last_name LIKE '%" . $s_ne_sql . "%'";
				$sql_where .= " OR u.company_name LIKE '%" . $s_ne_sql . "%')";
			}
	  
			if (strlen($s_ad)) {
				if (strlen($sql_where)) { $sql_where .= " AND "; }
				$sql_where .= " (u.address1 LIKE '%" . $db->tosql($s_ad, TEXT, false) . "%'";
				$sql_where .= " OR u.address2 LIKE '%" . $db->tosql($s_ad, TEXT, false) . "%'";
				$sql_where .= " OR u.city LIKE '%" . $db->tosql($s_ad, TEXT, false) . "%'";
				$sql_where .= " OR u.province LIKE '%" . $db->tosql($s_ad, TEXT, false) . "%'";
				$sql_where .= " OR u.state_id LIKE '%" . $db->tosql($s_ad, TEXT, false) . "%'";
				$sql_where .= " OR u.zip LIKE '%" . $db->tosql($s_ad, TEXT, false) . "%'";
				$sql_where .= " OR u.country_id LIKE '%" . $db->tosql($s_ad, TEXT, false) . "%'";
				$sql_where .= " OR s.state_name LIKE '%" . $db->tosql($s_ad, TEXT, false) . "%'";
				$sql_where .= " OR c.country_name LIKE '%" . $db->tosql($s_ad, TEXT, false) . "%')";
				$sql_join_before = " ((";
				$sql_join  = " LEFT JOIN " . $table_prefix . "countries c ON u.country_id=c.country_id) ";
				$sql_join .= " LEFT JOIN " . $table_prefix . "states s ON u.state_id=s.state_id)";
			}
	  
			if (strlen($s_sd)) {
				if (strlen($sql_where)) { $sql_where .= " AND "; }
				$s_sd_value = parse_date($s_sd, $date_edit_format, $date_errors);
				$sql_where .= " u.registration_date>=" . $db->tosql($s_sd_value, DATE);
			}
	  
			if (strlen($s_ed)) {
				if (strlen($sql_where)) { $sql_where .= " AND "; }
				$end_date = parse_date($s_ed, $date_edit_format, $date_errors);
				$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
				$sql_where .= " u.registration_date<" . $db->tosql($day_after_end, DATE);
			}
	  
			if (strlen($s_ut)) {
				if (strlen($sql_where)) { $sql_where .= " AND "; }
				$sql_where .= " u.user_type_id=" . $db->tosql($s_ut, INTEGER);
			}
	  
			if (strlen($s_ap)) {
				if (strlen($sql_where)) { $sql_where .= " AND "; }
				$sql_where .= ($s_ap == 1) ? " u.is_approved=1 " : " u.is_approved=0 ";
			}
	  
			if (strlen($s_on)) {
				$current_date = va_time();
				$cyear = $current_date[YEAR]; $cmonth = $current_date[MONTH]; $cday = $current_date[DAY];
				$online_ts = mktime ($current_date[HOUR], $current_date[MINUTE] - $online_time, $current_date[SECOND], $cmonth, $cday, $cyear);
				if (strlen($sql_where)) { $sql_where .= " AND "; }
				if ($s_on == 1) {
					$sql_where .= " u.last_visit_date>=" . $db->tosql($online_ts, DATETIME);
				} else {
					$sql_where .= " u.last_visit_date<" . $db->tosql($online_ts, DATETIME);
				}
			}
			if ($sql_where) { $sql_where = " WHERE " . $sql_where; }
		}
	} else if ($table == "newsletters_users") {
		include_once("./admin_common.php");

		//check_admin_security("export_users");
		include_once("./admin_table_emails.php");
		if (strlen($id)) {
			$sql_where = " WHERE email_id>" . $db->tosql($id, INTEGER);
		} else if (strlen($ids)) {
			$sql_where = " WHERE email_id IN (" . $db->tosql($ids, TEXT, false) . ")";
		}
	} else if ($table == "orders") {
		include_once("./admin_common.php");
		include_once("./admin_table_orders.php");

		check_admin_security("sales_orders");
		$sql_where .= " WHERE o.order_id=oi.order_id ";
		if (strlen($id)) {
			$sql_where .= " AND o.order_id>" . $db->tosql($id, INTEGER);
		} else if (strlen($ids)) {
			$sql_where .= " AND o.order_id IN (" . $db->tosql($ids, TEXT, false) . ")";
		} else {

			$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type='order_info' ";
			//$sql .= " AND setting_name LIKE '%country_code%'";
			if ($multisites_version) {
				$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id,INTEGER) . ") ";
				$sql .= " ORDER BY site_id ASC ";
			}
			$db->query($sql);
			while($db->next_record()) {
				$order_info[$db->f("setting_name")] = $db->f("setting_value");
			}

			if (preg_match("/^(\d+)(,\d+)*$/", $s_on))	{
				$sql_where .= " AND (o.order_id IN (" . $db->tosql($s_on, TEXT, false) . ") ";
				$sql_where .= " OR o.invoice_number=" . $db->tosql($s_on, TEXT);
				$sql_where .= " OR o.transaction_id=" . $db->tosql($s_on, TEXT) . ") ";
			} else if (strlen($s_on)) {
				$sql_where .= " AND (o.invoice_number=" . $db->tosql($s_on, TEXT);
				$sql_where .= " OR o.transaction_id=" . $db->tosql($s_on, TEXT) . ") ";
			}

			if(strlen($s_ne)) {
				$s_ne_sql = $db->tosql($s_ne, TEXT, false);
				$sql_where .= " AND (o.email LIKE '%" . $s_ne_sql . "%'";
				$sql_where .= " OR o.name LIKE '%" . $s_ne_sql . "%'";
				$sql_where .= " OR o.first_name LIKE '%" . $s_ne_sql . "%'";
				$sql_where .= " OR o.last_name LIKE '%" . $s_ne_sql . "%')";
			}

			if(strlen($s_kw)) {
				$sql_where .= " AND (oi.item_name LIKE '%" . $db->tosql($s_kw, TEXT, false) . "%'";
				$sql_where .= " OR oi.item_properties LIKE '%" . $db->tosql($s_kw, TEXT, false) . "%'";
				$sql_where .= " OR o.shipping_type_desc LIKE '%" . $db->tosql($s_kw, TEXT, false) . "%')";
			}

			if(strlen($s_sd)) {
				$s_sd_value = parse_date($s_sd, $date_edit_format, $date_errors);
				$sql_where .= " AND o.order_placed_date>=" . $db->tosql($s_sd_value, DATE);
			}

			if(strlen($s_ed)) {
				$end_date = parse_date($s_ed, $date_edit_format, $date_errors);
				$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
				$sql_where .= " AND o.order_placed_date<" . $db->tosql($day_after_end, DATE);
			}

			if(strlen($s_os)) {
				$sql_where .= " AND o.order_status=" . $db->tosql($s_os, INTEGER);
			}

			if(strlen($s_ci)) {
				if ($order_info["show_delivery_country_id"] == 1) {
					$sql_where .= " AND o.delivery_country_id=" . $db->tosql($s_ci, INTEGER);
				} else if ($order_info["show_country_id"] == 1) {
					$sql_where .= " AND o.country_id=" . $db->tosql($s_ci, INTEGER);
				}
			}
			if(strlen($s_si)) {
				if ($order_info["show_delivery_state_id"] == 1) {
					$sql_where .= " AND o.delivery_state_id=" . $db->tosql($s_si, INTEGER);
				} else if ($order_info["show_state_id"] == 1) {
					$sql_where .= " AND o.state_id=" . $db->tosql($s_si, INTEGER);
				}
			}
			if(strlen($s_sti)) {
				$sql_where .= " AND o.site_id=" . $db->tosql($s_sti, INTEGER);				
			}

			if (strlen($s_ex)) {
				$sql_where .= ($s_ex == 1) ? " AND o.is_exported=1 " : " AND (o.is_exported<>1 OR o.is_exported IS NULL) ";
			}

			if(strlen($s_cct)) {
				$sql_where .= " AND o.cc_type=" . $db->tosql($s_cct, INTEGER);
			}
		}

	} else {
		$table_name = "";
		$table_title = "";
		$errors = CANT_FIND_TABLE_MSG;
	}	

	if (strlen(!$errors)) {
		$admin_export_custom_url->add_parameter("table", CONSTANT, $table);

		$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type=" . $db->tosql($table, TEXT);
		if ($multisites_version) {
			$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id,INTEGER) . ") ";
			$sql .= " ORDER BY site_id ASC ";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$custom_field = $db->f("setting_name");
			$custom_value = $db->f("setting_value");
			$admin_export_custom_url->add_parameter("field", CONSTANT, $custom_field);

			$edit_link = " &nbsp; <a href=\"" . $admin_export_custom_url->get_url() . "\"><font color=blue size=1>Edit</font></a>";
			$db_columns[$custom_field]  = array($custom_field, TEXT, CUSTOM_FIELD, false, $custom_value, $edit_link);
		}
		if(isset($related_columns)) {
			$admin_export_custom_url->add_parameter("table", CONSTANT, $related_table);

			$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type=" . $db->tosql($related_table, TEXT);
			if ($multisites_version) {
				$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id,INTEGER) . ") ";
				$sql .= " ORDER BY site_id ASC ";
			}
			$db->query($sql);
			while ($db->next_record()) {
				$custom_field = $db->f("setting_name");
				$custom_value = $db->f("setting_value");
				$admin_export_custom_url->add_parameter("field", CONSTANT, $custom_field);
				$edit_link = " &nbsp; <a href=\"" . $admin_export_custom_url->get_url() . "\"><font color=blue size=1>Edit</font></a>";
				$related_columns[$custom_field]  = array($custom_field, TEXT, CUSTOM_FIELD, false, $custom_value, $edit_link);
			}
		}
	}

	$t->set_var("table", $table);
	$t->set_var("table_title", $table_title);

	if($operation == "export")
	{
		if(!strlen($errors)) {

			// prepare categories for items table
			$categories = array();
			if ($table == "items") {
				$sql = "SELECT category_id,category_name FROM " . $table_prefix . "categories ";
				$db->query($sql);
				while ($db->next_record()) {
					$category_id = $db->f("category_id");
					$category_name = $db->f("category_name");
					if ($apply_translation) {
						$category_name = get_translation($category_name);
					}
					$categories[$category_id] = $category_name;

				}
			}

			// connection for additional operations
			$dbh = new VA_SQL();
			$dbh->DBType       = $db->DBType;
			$dbh->DBDatabase   = $db->DBDatabase;
			$dbh->DBUser       = $db->DBUser;
			$dbh->DBPassword   = $db->DBPassword;
			$dbh->DBHost       = $db->DBHost;
			$dbh->DBPort       = $db->DBPort;
			$dbh->DBPersistent = $db->DBPersistent;

			$columns = array();
			$total_columns = get_param("total_columns");
			$columns_selected = 0;
			$db_column        = 0;
			$columns_list     = "";
			$csv_columns_list = "";
			$exported_fields  = "";

			// generate db columns list
			foreach ($db_columns as $column_name => $column_info) {
				if($column_info[2] != RELATED_DB_FIELD && $column_info[2] != CUSTOM_FIELD) {
					if (!preg_match("/^order_property_/", $column_name)
						&& !preg_match("/^item_property_/", $column_name)
						&& !preg_match("/^item_feature_/", $column_name)
						&& !preg_match("/^registration_property_/", $column_name)) {
						$db_column++;
						if($db_column > 1) {
							$columns_list .= ", ";
						}				
						$columns_list .= $table_alias . "." . $column_name;
					}
				}
			}
			
			// generate selected columns
			for($col = 1; $col <= $total_columns; $col++) {
				$column_name  = get_param("db_column_" . $col);
				$column_title = get_param("column_title_" . $col);
				if($column_name) {
					$columns_selected++;
					if($columns_selected > 1) {
						//$columns_list .= ",";
						$exported_fields .= ",";
						$csv_columns_list .= $delimiter_symbol;
					}
					//$columns_list .= $table_alias . "." . $column_name;
					$exported_fields .= $table_alias . "." . $column_name;
					$csv_columns_list .= $column_title;

					$columns[] = $column_name;
				}
			}

			//CUSTOM_FIELD

			if (isset($related_columns)) {
				// generate db columns list
				foreach ($related_columns as $column_name => $column_info) {
					if($column_info[2] != RELATED_DB_FIELD && $column_info[2] != CUSTOM_FIELD) {
						$column_alias = $related_table_alias."_".$column_name;
						if (!preg_match("/^order_item_property_/", $column_name)) {
							$columns_list .= ",";
							$columns_list .= $related_table_alias.".".$column_name . " AS " . $column_alias;
						}
					}
				}
			}

			//$exported_fields = $columns_list;
			$total_related = get_param("total_related");
			$related_selected = 0;
			for($col = 1; $col <= $total_related; $col++) {
				$column_name = get_param("related_column_" . $col);
				if ($column_name) {
					$related_selected++;
					$columns_selected++;
					if ($related_columns[$column_name][2] == CUSTOM_FIELD) {
						$column_alias = $column_name;
					} else {
						$column_alias = $related_table_alias."_".$column_name;
					}
					if (preg_match("/^order_item_property_/", $column_name)) {
						if ($columns_selected > 1) {
							$csv_columns_list .= $delimiter_symbol;
							$exported_fields .= ",";
						}
					} else {
						if ($columns_selected > 1) {
							//$columns_list .= ",";
							$csv_columns_list .= $delimiter_symbol;
							$exported_fields .= ",";
						}
						//$columns_list .= $related_table_alias.".".$column_name . " AS " . $column_alias;
					}
					$csv_columns_list .= $related_columns[$column_name][0];
					$exported_fields .= $column_alias;
					$columns[] = $column_alias;
					$selected_related_columns[$column_alias] = 1;
				}
			}

			$exported_fields .= ",csv_delimiter" . $csv_delimiter. "csv_delimiter";
			$exported_fields .= ",related_delimiter" . $related_delimiter . "related_delimiter";
			// update default columns list
			if ($table == "users") {
				$sql  = " UPDATE " . $table_prefix . "admins SET exported_user_fields=" . $db->tosql($exported_fields, TEXT);
				$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$db->query($sql);
			} else if ($table == "newsletters_users") {
				$sql  = " UPDATE " . $table_prefix . "admins SET exported_email_fields=" . $db->tosql($exported_fields, TEXT);
				$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$db->query($sql);
			} else if ($table == "orders") {
				$sql = " UPDATE " . $table_prefix . "admins SET exported_order_fields=" . $db->tosql($exported_fields, TEXT);
				$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$db->query($sql);
			} else if ($table == "items") {
				$sql = " UPDATE " . $table_prefix . "admins SET exported_item_fields=" . $db->tosql($exported_fields, TEXT);
				$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$db->query($sql);
			}

			$csv_filename = $table_name . ".csv";
			header("Pragma: private");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=" . $csv_filename);
			header("Content-Transfer-Encoding: binary");

			echo $csv_columns_list . $eol;

			$exported_user_id = 0; $exported_order_id = 0;
			if ($table == "users") {
				$sql  = " SELECT exported_user_id FROM " . $table_prefix . "admins ";
				$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$exported_user_id = get_db_value($sql);
			} else if ($table == "orders") {
				$sql  = " SELECT exported_order_id FROM " . $table_prefix . "admins ";
				$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$exported_order_id = get_db_value($sql);
			} else if ($table == "newsletters_users") {
				$sql  = " SELECT exported_email_id FROM " . $table_prefix . "admins ";
				$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$exported_email_id = get_db_value($sql);
			}
			$max_id = 0;
			$sql = "SELECT " . $columns_list . " FROM " . $table_name . " " . $table_alias;
			if ($table == "orders") {
				$sql  = " SELECT " . $columns_list . " FROM " . $table_name . " " . $table_alias;
				$sql .= " , " . $related_table_name . " " . $related_table_alias;
			} elseif (isset($sql_join) && $sql_join) {
				$sql  = " SELECT " . $columns_list ;
				$sql .= " FROM " . $sql_join_before . " " . $table_name . " " . $table_alias;
				$sql .= " " . $sql_join;				
			}
			if (isset($table_pk) && $table_pk) {
				if (strlen($table_alias)) {
					$order_by = " ORDER BY " . $table_alias . "." . $table_pk;
				} else {
					$order_by = " ORDER BY " . $table_pk;
				}
			} else {
				$order_by = "";
			}

			$db->query($sql . $sql_where . $order_by);
			if ($db->next_record()) {
				$row_data = array();
				$record_number = 0; $related_number = 0;
				$prev_id = $db->f($table_pk);
				do {
					$record_number++;
					$row_id = $db->f($table_pk);
					if ($row_id > $max_id) { $max_id = $row_id; }
					if ($prev_id != $row_id || ($record_number > 1 && $related_delimiter == "row" && $related_selected > 0)) {
						// output csv
						$csv_row = "";
						for($i = 0; $i < $columns_selected; $i++) {
							$column_name = $columns[$i];
							$field_value = $row_data[$column_name];
							if ($column_name == "oi_item_properties") {
								$field_value = preg_replace("/^\<br\>/", "", $field_value);
								$field_value = preg_replace("/\<br\>/", "; ", $field_value);
							}
							if (preg_match("/[,;\"\n\r\t\s]/", $field_value)) {
								$field_value = "\"" . str_replace("\"", "\"\"", $field_value) . "\"";
							}
							if($i > 0) {
								$csv_row .= $delimiter_symbol;
							}
							$csv_row .= $field_value;
						}
						echo $csv_row . $eol;
						// end output
						$related_number = 0;

						// update exported status
						if ($table_name == $table_prefix . "orders") {
							$dbh->query("UPDATE " . $table_prefix . "orders SET is_exported=1 WHERE order_id=" . $prev_id);
						}
					}
					$related_number++;

					// collect data for next step
					for($i = 0; $i < $columns_selected; $i++) {
						$column_name = $columns[$i];

						$field_value = "";
						if ($column_name == "item_category") {
							$item_id = $db->f("item_id");
							$sql  = " SELECT ic.category_id, c.category_path FROM " . $table_prefix . "items_categories ic ";
							$sql .= " LEFT JOIN " . $table_prefix . "categories c ON ic.category_id=c.category_id ";
							$sql .= " WHERE  ic.item_id=" . $db->tosql($item_id, INTEGER);
							$dbh->query($sql);
							while ($dbh->next_record()) {
								$category = "";
								$category_path = $dbh->f("category_path") . $dbh->f("category_id");
								// build full category path if available
								$categories_ids = explode(",", $category_path);
								for ($ci = 0; $ci < sizeof($categories_ids); $ci++) {
									$category_id = $categories_ids[$ci];
									if ($category_id > 0) {
										if (strlen($category)) { $category .= " > "; }
										$category .= $categories[$category_id];
									}
								}
								if (strlen($field_value)) { $field_value .= ";"; }
								// for top category use zero number
								if (!strlen($category)) { $category = 0; }
								$field_value .= $category;
							}

						} else if (preg_match("/^item_property_/", $column_name)) {
							$property_name = substr($column_name, 14);
							$item_id = $db->f("item_id");
							$sql  = " SELECT property_id, control_type,property_description FROM " . $table_prefix . "items_properties ";
							$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
							$sql .= " AND property_name=" . $db->tosql($property_name, TEXT);
							$dbh->query($sql);
							if ($dbh->next_record()) {
								$property_id = $dbh->f("property_id");
								$control_type = $dbh->f("control_type");
								if ($control_type == "LABEL" || $control_type == "TEXTBOX" || $control_type == "TEXTAREA") {
									if ($apply_translation) {
										$field_value = get_translation($dbh->f("property_description"));
									} else {
										$field_value = $dbh->f("property_description");
									}
								} else {
									$sql  = " SELECT property_value,additional_price FROM " . $table_prefix . "items_properties_values ";
									$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER);
									$dbh->query($sql);
									while($dbh->next_record()) {
										$option_value = $dbh->f("property_value");
										$additional_price = $dbh->f("additional_price");
										if (strlen($field_value)) { $field_value .= ";"; }
										$field_value .= $option_value;
										if (strlen($additional_price)) {
											$field_value .= "=".$additional_price;
										}
									}

								}
							}
						} else if (preg_match("/^item_feature_(\d+)_(.+)$/", $column_name, $matches)) {
							$group_id = $matches[1];
							$feature_name = $matches[2];
							$item_id = $db->f("item_id");
							$sql  = " SELECT fg.group_name, f.feature_name, f.feature_value ";
							$sql .= " FROM (" . $table_prefix . "features f ";
							$sql .= " INNER JOIN " . $table_prefix . "features_groups fg ON f.group_id=fg.group_id) ";
							$sql .= " WHERE f.item_id=" . $db->tosql($item_id, INTEGER);
							$sql .= " AND f.group_id=" . $db->tosql($group_id, INTEGER);
							$sql .= " AND f.feature_name=" . $db->tosql($feature_name, TEXT);
							$dbh->query($sql);
							if ($dbh->next_record()) {
								if ($apply_translation) {
									$field_value = get_translation($dbh->f("feature_value"));
								} else {
									$field_value = $dbh->f("feature_value");
								}
							}
						} else if (preg_match("/^order_property_/", $column_name)) {
							$property_id = substr($column_name, 15);
							$order_id = $db->f("order_id");
							$order_properties = array();
							$sql  = " SELECT op.property_id, op.property_type, op.property_name, op.property_value, ";
							$sql .= " op.property_price, op.property_points_amount, op.tax_free ";
							$sql .= " FROM " . $table_prefix . "orders_properties op ";
							$sql .= " WHERE op.order_id=" . $db->tosql($order_id, INTEGER);
							$sql .= " AND op.property_id=" . $db->tosql($property_id, INTEGER);
							$dbh->query($sql);
							while ($dbh->next_record()) {
								$property_value = $dbh->f("property_value");
								if (strlen($field_value)) { $field_value .= "; "; }
								if ($apply_translation) {
									$field_value .= get_translation($property_value);
								} else {
									$field_value .= $property_value;
								}
							}

						} else if (preg_match("/^registration_property_/", $column_name)) {
							$property_id = substr($column_name, strlen("registration_property_"));
							$registration_id = $db->f("registration_id");
							$sql  = " SELECT property_value FROM " . $table_prefix . "registration_properties ";
							$sql .= " WHERE registration_id=" . $db->tosql($registration_id, INTEGER);
							$sql .= " AND property_id=" . $db->tosql($property_id, INTEGER);
							$dbh->query($sql);
							$field_value_parts = array();
							while ($dbh->next_record()) {
								if ($apply_translation) {
									$field_value_parts[] = get_translation($dbh->f("property_value"));
								} else {
									$field_value_parts[] = $dbh->f("property_value");
								}
							}
							$control_type = $db_columns[$column_name]["control_type"];
							if(($control_type == "CHECKBOXLIST" ||  $control_type == "RADIOBUTTON" || $control_type == "LISTBOX")) {
								$field_value = "";
								foreach ($field_value_parts AS $field_value_part) {
									$sql  = " SELECT property_value FROM " . $table_prefix . "registration_custom_values ";
									$sql .= " WHERE property_value_id=" . $db->tosql($field_value_part, INTEGER);
									$dbh->query($sql);
									if ($dbh->next_record()) {
										if ($field_value) $field_value .= " / ";
										$field_value .= $dbh->f("property_value");						
									}
								}
							} else {
								$field_value = implode(" / ", $field_value_parts);
							}
						} else if (preg_match("/^oi_order_item_property_/", $column_name)) {
							$property_id = substr($column_name, 23);
							$order_item_id = $db->f("oi_order_item_id");
							$sql  = " SELECT property_value FROM " . $table_prefix . "orders_items_properties ";
							$sql .= " WHERE order_item_id=" . $order_item_id;
							$sql .= " AND (property_id=" . $db->tosql($property_id, INTEGER, true, false);
							$sql .= " OR property_name=" . $db->tosql($property_id, TEXT) . ") ";
							$dbh->query($sql);
							if ($dbh->next_record()) {
								if ($apply_translation) {
									$field_value = get_translation($dbh->f("property_value"));
								} else {
									$field_value = $dbh->f("property_value");
								}
							}
						} else if ($column_name == "manufacturer_name") {
							$manufacturer_id = $db->f("manufacturer_id");
							if (strlen($manufacturer_id)) {
								$sql  = " SELECT manufacturer_name FROM " . $table_prefix . "manufacturers ";
								$sql .= " WHERE manufacturer_id=" . $db->tosql($manufacturer_id, INTEGER);
								$dbh->query($sql);
								if ($dbh->next_record()) {
									if ($apply_translation) {
										$field_value = get_translation($dbh->f("manufacturer_name"));
									} else {
										$field_value = $dbh->f("manufacturer_name");
									}
								}
							}
						} else {
							if ((isset($db_columns[$column_name]) && $db_columns[$column_name][2] == CUSTOM_FIELD)) {
								$field_source = $db_columns[$column_name][4];
								$field_value  = get_field_value($field_source);
							} else if ((isset($related_columns) && isset($related_columns[$column_name]) && $related_columns[$column_name][2] == CUSTOM_FIELD)) {
								$field_source = $related_columns[$column_name][4];
								$field_value  = get_field_value($field_source);
							} else {
								$column_type = TEXT;
								if (isset($db_columns[$column_name])) {
									$column_type = $db_columns[$column_name][1];
								} else if (isset($related_table_alias) && $related_table_alias && preg_match("/^".$related_table_alias."_/", $column_name)) {
									$related_column_name = preg_replace("/^".$related_table_alias."_/", "", $column_name);
									if (isset($related_columns[$related_column_name])) {
										$column_type = $related_columns[$related_column_name][1];
									}
								}

								if ($column_type == DATE) {
									$field_value = $db->f($column_name, DATETIME);
									if (is_array($field_value)) {
										$field_value = va_date($date_edit_format, $field_value);
									}
								} else if ($column_type == DATETIME) {
									$field_value = $db->f($column_name, DATETIME);
									if (is_array($field_value)) {
										$field_value = va_date($datetime_edit_format, $field_value);
									}
								} else {
									$field_value = $db->f($column_name);
									if ($apply_translation) {
										$field_value = get_translation($field_value);
									}
								}
							}
						}
						if (isset($selected_related_columns[$column_name]) && $related_number > 1) {
							$row_data[$column_name] .= $related_delimiter_symbol . $field_value;
						} else {
							$row_data[$column_name] = $field_value;
						}
					}
					$prev_id = $row_id;

				} while ($db->next_record());

				// last row output csv
				$csv_row = "";
				for($i = 0; $i < $columns_selected; $i++) {
					$column_name = $columns[$i];
					$field_value = $row_data[$column_name];
					if ($column_name == "oi_item_properties") {
						$field_value = preg_replace("/^\<br\>/", "", $field_value);
						$field_value = preg_replace("/\<br\>/", "; ", $field_value);
					}
					if(preg_match("/[,;\"\n\r\t\s]/", $field_value)) {
						$field_value = "\"" . str_replace("\"", "\"\"", $field_value) . "\"";
					}
					if($i > 0) {
						$csv_row .= $delimiter_symbol;
					}
					$csv_row .= $field_value;
				}
				echo $csv_row . $eol;
				// end output

				// update exported status
				if ($table_name == $table_prefix . "orders") {
					$dbh->query("UPDATE " . $table_prefix . "orders SET is_exported=1 WHERE order_id=" . $prev_id);
				}
			}

			if ($table == "users") {
				if ($max_id > $exported_user_id) {
					$sql  = " UPDATE " . $table_prefix . "admins SET exported_user_id=" . $db->tosql($max_id, INTEGER);
					$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
					$db->query($sql);
				}
			} else if ($table == "newsletters_users") {
				if ($max_id > $exported_email_id) {
					$sql  = " UPDATE " . $table_prefix . "admins SET exported_email_id=" . $db->tosql($max_id, INTEGER);
					$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
					$db->query($sql);
				}
			} else if ($table == "orders") {
				if ($max_id > $exported_order_id) {
					$sql = " UPDATE " . $table_prefix . "admins SET exported_order_id=" . $db->tosql($max_id, INTEGER);
					$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
					$db->query($sql);
				}
			}

			exit;
		}
	}


	if(strlen($errors))
	{
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	}
	else
	{
		$t->set_var("errors", "");
	}

	$t->set_var("category_id", htmlspecialchars($category_id));
	$t->set_var("id", htmlspecialchars($id));
	$t->set_var("ids", htmlspecialchars($ids));
	$t->set_var("s_on", htmlspecialchars($s_on));
	$t->set_var("s_ne", htmlspecialchars($s_ne));
	$t->set_var("s_kw", htmlspecialchars($s_kw));
	$t->set_var("s_sd", htmlspecialchars($s_sd));
	$t->set_var("s_ed", htmlspecialchars($s_ed));
	$t->set_var("s_os", htmlspecialchars($s_os));
	$t->set_var("s_ad", htmlspecialchars($s_ad));
	$t->set_var("s_ut", htmlspecialchars($s_ut));
	$t->set_var("s_ap", htmlspecialchars($s_ap));
	$t->set_var("s_ci", htmlspecialchars($s_ci));
	$t->set_var("s_si", htmlspecialchars($s_si));
	$t->set_var("s_ex", htmlspecialchars($s_ex));
	$t->set_var("s_cct", htmlspecialchars($s_cct));
	
	$t->set_var("s_rn", htmlspecialchars($s_rn));
	$t->set_var("s_ap", htmlspecialchars($s_ap));
	$t->set_var("s_pi", htmlspecialchars($s_pi));
	
	$t->set_var("s", htmlspecialchars($s));
	$t->set_var("sc", htmlspecialchars($sc));
	$t->set_var("sl", htmlspecialchars($sl));
	$t->set_var("ss", htmlspecialchars($ss));
	$t->set_var("ap", htmlspecialchars($ap));

	$t->set_var("rnd", va_timestamp());

	if ($table_name == ($table_prefix . "items") || $table_name == ($table_prefix . "categories")) {
		$t->parse("products_path", false);
	} else if ($table == "orders") {
		$admin_orders_url = new VA_URL("admin_orders.php", false);
		$admin_orders_url->add_parameter("ids", REQUEST, "ids");
		$admin_orders_url->add_parameter("page", REQUEST, "page");
		$admin_orders_url->add_parameter("s_on", REQUEST, "s_on");
		$admin_orders_url->add_parameter("s_ne", REQUEST, "s_ne");
		$admin_orders_url->add_parameter("s_kw", REQUEST, "s_kw");
		$admin_orders_url->add_parameter("s_sd", REQUEST, "s_sd");
		$admin_orders_url->add_parameter("s_ed", REQUEST, "s_ed");
		$admin_orders_url->add_parameter("s_os", REQUEST, "s_os");
		$admin_orders_url->add_parameter("s_ci", REQUEST, "s_ci");
		$admin_orders_url->add_parameter("s_si", REQUEST, "s_si");
		$admin_orders_url->add_parameter("s_ex", REQUEST, "s_ex");
		$admin_orders_url->add_parameter("s_cct", REQUEST, "s_cct");
		$admin_orders_url->add_parameter("sort_ord", REQUEST, "sort_ord");
		$admin_orders_url->add_parameter("sort_dir", REQUEST, "sort_dir");

		$t->set_var("admin_orders_url", $admin_orders_url->get_url());

		$t->parse("orders_path", false);
	}

	$default_columns = "";
	if ($table == "users") {
		$sql  = " SELECT exported_user_fields FROM " . $table_prefix . "admins WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$default_columns = get_db_value($sql);
	} else if ($table == "newsletters_users") {
		$sql  = " SELECT exported_user_fields FROM " . $table_prefix . "admins WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$default_columns = get_db_value($sql);
		$t->parse("newsletters_path", false);
	} else if ($table == "orders") {
		$sql  = " SELECT exported_order_fields FROM " . $table_prefix . "admins WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$default_columns = get_db_value($sql);
	} else if ($table == "items") {
		$sql  = " SELECT exported_item_fields FROM " . $table_prefix . "admins WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$default_columns = get_db_value($sql);
	}
	$checked_columns = explode(",", $default_columns);

	// get default delimiters
	if(strpos($default_columns, "csv_delimiter")) {
		$start_delimiter = strpos($default_columns, "csv_delimiter");
		$end_delimiter = strpos($default_columns, "csv_delimiter", $start_delimiter + 13);
		$csv_delimiter = substr($default_columns, $start_delimiter + 13, $end_delimiter - $start_delimiter - 13);
	}
	if(strpos($default_columns, "related_delimiter")) {
		$start_delimiter = strpos($default_columns, "related_delimiter");
		$end_delimiter = strpos($default_columns, "related_delimiter", $start_delimiter + 17);
		$related_delimiter = substr($default_columns, $start_delimiter + 17, $end_delimiter - $start_delimiter - 17);
	}

	set_options($delimiters, $csv_delimiter, "delimiter");
	set_options($delimiters, $csv_delimiter, "delimiter_bottom");
	set_options($related_delimiters, $related_delimiter, "related_delimiter");
	set_options($related_delimiters, $related_delimiter, "related_delimiter_bottom");


	$total_columns = 0;
	$t->set_var("table_name", $table_name);

	foreach($db_columns as $column_name => $column_info) {
		if ($column_info[2] == RELATED_DB_FIELD) {
			if ($table == "items" && $column_name == "property_name") {
				$sql = " SELECT property_name FROM " . $table_prefix . "items_properties GROUP BY property_name ";
				$db->query($sql);
				while ($db->next_record()) {
					$property_name = $db->f("property_name");
					$column_name   = "item_property_" . $property_name;
					if ($apply_translation) {
						$property_name = get_translation($property_name);
					}
					$column_title  = $property_name;
					set_db_column($column_name, $column_title);
				}
			} else if ($table == "items" && $column_name == "feature_name") {
				$sql  = " SELECT fg.group_id, fg.group_name, f.feature_name FROM (" . $table_prefix . "features f ";
				$sql .= " INNER JOIN " . $table_prefix . "features_groups fg ON f.group_id=fg.group_id) ";
				$sql .= " GROUP BY fg.group_id, fg.group_name, f.feature_name ";
				$db->query($sql);
				while ($db->next_record()) {
					$group_id = $db->f("group_id");
					$group_name = $db->f("group_name");
					$feature_name = $db->f("feature_name");
					$column_name   = "item_feature_" . $group_id . "_" . $feature_name;
					if ($apply_translation) {
						$column_title  = get_translation($group_name) . " > " . get_translation($feature_name);
					} else {
						$column_title  = $group_name . " > " . $feature_name;
					}
					set_db_column($column_name, $column_title);
				}
			} else if ($table == "items" && $column_name == "category_name") {
				$column_title  = $db_columns[$column_name][0];
				set_db_column("item_category", $column_title);
			} else if ($table == "items" && $column_name == "manufacturer_name") {
				$column_title  = $db_columns[$column_name][0];
				set_db_column("manufacturer_name", $column_title);
			}

		} else if ($column_info[2] != HIDE_DB_FIELD) {
			$column_title = $column_info[0];
			$column_link = ($column_info[2] == CUSTOM_FIELD) ? $column_info[5] : "";
			set_db_column($column_name, $column_title, $column_link);
		}
	}
	if($total_columns % 2 != 0) {
		$t->parse("columns", true);
	}
	$t->set_var("total_columns", $total_columns);

	// if available some related data
	$total_related = 0;
	if(isset($related_columns)) {
		$admin_export_custom_url->remove_parameter("field");
		$admin_export_custom_url->add_parameter("table", CONSTANT, $related_table);
		$t->set_var("admin_export_custom_related_url", $admin_export_custom_url->get_url());
		$t->set_var("related_table", $related_table);

		foreach ($related_columns as $column_name => $column_info) {
			if($column_info[2] != HIDE_DB_FIELD && $column_info[2] != RELATED_DB_FIELD) {
				if ($column_info[2] == CUSTOM_FIELD) {
					$column_title = $column_info[0] . " " . $column_info[5];
					$column_checked = in_array($column_name, $checked_columns) ? " checked " : "";
				} else {
					$column_title = $column_info[0];
					$column_checked = in_array($related_table_alias."_".$column_name, $checked_columns) ? " checked " : "";
				}
				$total_related++;
				$t->set_var("col", $total_related);
				$t->set_var("column_name", htmlspecialchars($column_name));
				$t->set_var("column_checked", $column_checked);
				$t->set_var("column_title", $column_title);
				$t->parse("related_columns", true);
			}
		}
	}
	$t->set_var("total_related", $total_related);
	if($total_related > 0) {
		$t->parse("related_data", false);
	} else {
		$t->set_var("related_data", "");
	}


	$t->pparse("main");

	function get_field_value($field_source)
	{
		global $db, $db_columns, $related_columns, $related_table_alias, $apply_translation, $date_formats, $date_edit_format, $datetime_edit_format;

		if (preg_match_all("/\{(\w+)\}/i", $field_source, $matches)) {
			$field_value = $field_source;
			for($p = 0; $p < sizeof($matches[1]); $p++) {
				$f_source = $matches[1][$p];
				// get field type
				$column_type = TEXT; $column_name = ""; $column_format = "";
				if (isset($db_columns[$f_source])) {
					$column_type = $db_columns[$f_source][1];
					$column_name = $f_source;
				} else if (isset($related_table_alias) && $related_table_alias && preg_match("/^".$related_table_alias."_/", $f_source)) {
					$related_column_name = preg_replace("/^".$related_table_alias."_/", "", $f_source);
					if (isset($related_columns[$related_column_name])) {
						$column_type = $related_columns[$related_column_name][1];
						$column_name = $f_source;
					}
				} else {
					$date_formats_regexp = implode("|", $date_formats);
					if (preg_match("/".$date_formats_regexp."$/", $f_source, $format_match)) {
						$f_source_wf = preg_replace("/_".$format_match[0]."$/", "", $f_source);
						if (isset($db_columns[$f_source_wf]) && ($db_columns[$f_source_wf][1] == DATE || $db_columns[$f_source_wf][1] == DATETIME)) {
							$column_name = $f_source_wf;
							$column_type = $db_columns[$column_name][1];
							$column_format = $format_match[0];
						}
					}
				}

				if ($column_name) {
					if ($column_type == DATE) {
						$f_source_value = $db->f($column_name, DATETIME);
						if (is_array($f_source_value)) {
							if ($column_format) {
								$f_source_value = va_date(array($column_format), $f_source_value);
							} else {
								$f_source_value = va_date($date_edit_format, $f_source_value);
							}
						}
					} else if ($column_type == DATETIME) {
						$f_source_value = $db->f($column_name, DATETIME);
						if (is_array($f_source_value)) {
							if ($column_format) {
								$f_source_value = va_date(array($column_format), $f_source_value);
							} else {
								$f_source_value = va_date($datetime_edit_format, $f_source_value);
							}
						}
					} else {
						$f_source_value = $db->f($column_name);
						if ($apply_translation) {
							$f_source_value = get_translation($f_source_value);
						}
					}
					$field_value = str_replace("{".$f_source."}", $f_source_value, $field_value);
				}
			}
		} else {
			$field_value = $field_source;
		}

		return $field_value;
	}

	function set_db_column($column_name, $column_title, $column_link = "")
	{
		global $t, $db, $total_columns, $table_alias, $checked_columns;

		$total_columns++;
		$column_checked = in_array($table_alias.".".$column_name, $checked_columns) ? " checked " : "";
		$t->set_var("col", $total_columns);
		$t->set_var("column_name", htmlspecialchars($column_name));
		$t->set_var("column_link", $column_link);
		$t->set_var("column_checked", $column_checked);
		$t->set_var("column_title", htmlspecialchars($column_title));
		$t->parse("rows", true);
		if($total_columns % 2 == 0) {
			$t->parse("columns", true);
			$t->set_var("rows", "");
		}

	}

?>