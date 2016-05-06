<?php

	@set_time_limit(600);
	@ini_set("auto_detect_line_endings", 1);
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/friendly_functions.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once($root_folder_path . "messages/" . $language_code . "/download_messages.php");
	include_once("./admin_common.php");
	
	check_admin_security("import_export");

	$errors = "";
	$max_columns = 9;
	$rnd = get_param("rnd");
	$table = get_param("table");
	$csv_delimiter = get_param("csv_delimiter");
	$csv_file_path = get_param("csv_file_path");
	$operation = get_param("operation");
	$category_id = get_param("category_id");
	$use_first_row = get_param("use_first_row");
	$insert_data = get_param("insert_data");
	$session_rnd = get_session("session_rnd");
	$is_file_path = false;
	$delimiter_char = ($csv_delimiter === "tab") ? "\t" : substr($csv_delimiter, 0, 1);
	$tmp_dir = get_setting_value($settings, "tmp_dir", "");
	$features_groups = array();
	
	$eol = get_eol();
	$import_related_table   = get_param("import_related_table");
	$csv_related_delimiter  = get_param("csv_related_delimiter", "comma");
	$delimiters_symbols     = array("comma" => ",", "tab" => "\t", "semicolon" => ";", "row" => "row", "space" => " ", "newline" => $eol);
	if ($csv_related_delimiter) {
		$related_delimiter_char = $delimiters_symbols[$csv_related_delimiter];
	}
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_import.html");
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_select_href", "admin_select.php");
	$t->set_var("admin_import_href", "admin_import.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_users_list_href", "admin_newsletter_users.php");
	
	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($category_id);
	
	if ($table == "items") {
		check_admin_security("products_import");
		include_once("./admin_table_items.php");
		// check addition options for table
		$match_item_code = get_setting_value($settings, "match_item_code", 0);
		if ($match_item_code) {
			$db_columns["item_code"][2] = WHERE_DB_FIELD;
		}
		$match_manufacturer_code = get_setting_value($settings, "match_manufacturer_code", 0);
		if ($match_manufacturer_code) {
			$db_columns["manufacturer_code"][2] = WHERE_DB_FIELD;
		}
	} elseif ($table == "categories") {
		check_admin_security("categories_import");
		include_once("./admin_table_categories.php");
	} elseif ($table == "users") {
		check_admin_security("import_users");
		include_once("./admin_table_users.php");
	} elseif ($table == "newsletters_users") {
		check_admin_security("import_users");
		include_once("./admin_table_emails.php");
	} elseif ($table == "orders") {
		check_admin_security("orders_import");
		include_once("./admin_table_orders.php");
	} else {
		$table_name = "";
		$table_title = "";
		$errors = CANT_FIND_TABLE_IMPORT_MSG;
	}
	
	if ($table == "orders") {		
		$sql  = " SELECT property_id, property_name FROM " . $table_prefix . "order_custom_properties ";
		$sql .= " WHERE payment_id=0 ";
		$sql .= " GROUP BY property_name, property_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$property_id = $db->f("property_id");
			$property_name = $db->f("property_name");
			$db_columns["order_property_" . $property_id] = array(get_translation($property_name), TEXT, 5, false, $table_prefix . "order_properties");
		}
	
		$related_columns["tax_percent"] = array(PRODUCT_TAX_MSG, NUMBER, 2, false);
		
		$sql = " SELECT property_name FROM " . $table_prefix . "items_properties GROUP BY property_name";
		$db->query($sql);
		while ($db->next_record()) {
			$related_columns["order_item_property_" . $property_name] = array(PRODUCT_OPTION_MSG ." (" . get_translation($property_name) . ")", TEXT, 5, false, $table_prefix . "order_items_properties");
		}
		$sql = " SELECT property_name FROM " . $table_prefix . "orders_items_properties GROUP BY property_name ";
		$db->query($sql);
		while ($db->next_record()) {
			$property_name = $db->f("property_name");
			$related_columns["order_item_property_" . $property_name] = array(PRODUCT_OPTION_MSG . " (" . get_translation($property_name) . ")", TEXT, 5, false, $table_prefix . "order_items_properties");
		}
	}
	
	$db_columns_options = array();
	$db_columns_options[] = array("", "");
	$db_columns_options[] = array("-1", "<-- " . IGNORE_COLUMN_MSG);
	if (!$errors) {
		foreach ($db_columns as $column_name => $column_info) {
			if ($column_info[2] != HIDE_DB_FIELD) {
				$db_columns_options[] = array($column_name, $column_info[0]);
			}
		}
		foreach ($db_columns as $column_name => $column_info) {
			$column_title = strtolower($column_info[0]);
			$db_aliases[$column_title] = $column_name;
		}
		if ($import_related_table) {
			$db_columns_options[] = array("", "--".RELATED_MSG."--");
			foreach ($related_columns as $column_name => $column_info) {
				if ($column_info[2] != HIDE_DB_FIELD) {
					$db_columns_options[] = array($column_name, $column_info[0]);
				}
			}
			foreach ($related_columns as $column_name => $column_info) {
				$column_title = strtolower($column_info[0]);
				$db_aliases[$column_title] = $column_name;
			}
		}
	}
	
	$t->set_var("table", $table);
	$t->set_var("table_title", $table_title);
		
	if ($operation == "upload")
	{
		if (strlen($csv_file_path)) {
			$is_file_path = true;
			if (file_exists($csv_file_path)) {
				$fp = fopen($csv_file_path, "r");
				if (!$fp) {
					$errors = CANT_OPEN_IMPORTED_MSG;
				}
			} else {
				$errors = FILE_DOESNT_EXIST_MSG . "<b>$csv_file_path</b>";
			}
		} else {
			if (isset($_FILES)) {
				$tmp_name = $_FILES["csv_file"]["tmp_name"];
				$filename = $_FILES["csv_file"]["name"];
				$filesize = $_FILES["csv_file"]["size"];
				$upload_error = isset($_FILES["csv_file"]["error"]) ? $_FILES["csv_file"]["error"] : "";
			} else {
				$tmp_name = $HTTP_POST_FILES["csv_file"]["tmp_name"];
				$filename = $HTTP_POST_FILES["csv_file"]["name"];
				$filesize = $HTTP_POST_FILES["csv_file"]["size"];
				$upload_error = isset($HTTP_POST_FILES["csv_file"]["error"]) ? $HTTP_POST_FILES["csv_file"]["error"] : "";
			}
	
			if ($upload_error == 1) {
				$errors = FILESIZE_DIRECTIVE_ERROR_MSG;
			} elseif ($upload_error == 2) {
				$errors = FILESIZE_PARAMETER_ERROR_MSG;
			} elseif ($upload_error == 3) {
				$errors = PARTIAL_UPLOAD_ERROR_MSG;
			} elseif ($upload_error == 4) {
				$errors = NO_FILE_UPLOADED_MSG;
			} elseif ($upload_error == 6) {
				$errors = TEMPORARY_FOLDER_ERROR_MSG;
			} elseif ($upload_error == 7) {
				$errors = FILE_WRITE_ERROR_MSG;
			} elseif ($tmp_name == "none" || !strlen($tmp_name)) {
				$errors = NO_FILE_UPLOADED_MSG;
			}
	
			if (!strlen($errors)) {
				if ($tmp_dir) {
					$tmp_filename = "tmp_" . md5(uniqid(rand(), true)) . ".csv";
					if (@move_uploaded_file($tmp_name, $tmp_dir. $tmp_filename)) {
						$csv_file_path = $tmp_dir . $tmp_filename;
					}
				}
	
				if (strlen($csv_file_path)) {
					$is_file_path = true;
					$fp = fopen($csv_file_path, "r");
				} else {
					$fp = fopen($tmp_name, "r");
				}
				if (!$fp) {
					$errors = CANT_OPEN_IMPORTED_MSG;
				}
			}
		}
	
		if (!strlen($errors)) {
			if ($columns_data = fgetcsv($fp, 4096, $delimiter_char)) {
				$columns_number = sizeof($columns_data);
				if ($columns_number < $min_column_allowed) {
					$errors = MIN_COLUMNS_ALLOWED_NOTE . $min_column_allowed . CHECK_CSV_SETTINGS_NOTE;
				}
			} else {
				$errors = CANT_PARSE_COLUMNS_MSG;
			}
		}
	
		if (!strlen($errors)) {
	
			$operation = "import";
			$row = 1; $column_number = 0;
			for ($i = 0; $i < $columns_number; $i++) {
				$column_number++;
				$column_title = trim($columns_data[$i]);
				$db_column = strtolower($column_title);
				if (isset($db_aliases[$db_column])) {
					$db_column = $db_aliases[$db_column];
				}
				$field_name = "f_" . $row . "_" . $column_number;
				$t->set_var("field_name", $field_name);
				$t->set_var("field_value", htmlspecialchars($column_title));
				$t->set_var("column_number", $column_number);
				$t->set_var("column_title", htmlspecialchars($column_title));
				set_options($db_columns_options, $db_column, "db_column");
				$t->parse("columns", true);
				$t->parse("fields", true);
			}
	
			if (!$is_file_path) {
				while ($data = fgetcsv($fp, 65536, $delimiter_char)) {
					if (sizeof($data) > 1 || $data[0]) {
						$row++;
						for ($i = 0; $i < $columns_number; $i++) {
							$col = ($i + 1);
							$field_name = "f_" . $row . "_" . $col;
							$field_value = isset($data[$i]) ? $data[$i] : "";
							$t->set_var("field_name", $field_name);
							$t->set_var("field_value", htmlspecialchars($field_value));
							$t->parse("fields", true);
						}
						$t->set_var("row", $row);
						$t->parse("rows", true);
					}
				}
			}
	
			$t->set_var("total_rows", $row);
			$t->set_var("total_columns", $columns_number);
			fclose($fp);
		}
	} elseif ($operation == "import") {
		// preview data or immediate import
		
		$operation = "insert";
		$total_rows = get_param("total_rows");
		$total_columns = get_param("total_columns");
		$total_columns_left = $total_columns;
		$total_colspan = ($total_columns + 1);
		$total_errors = 0;
		$is_where = false;
	
		$r = new VA_Record($table_name);
		if ($import_related_table) {
			$sub_r = new VA_Record($related_table_name);
			if ($related_table == "orders_items") {
				$sub_r->add_textbox("item_id", INTEGER);
				$sub_r->change_property("item_id", USE_SQL_NULL, false);
			}
		}
		$r_no = array();
		
		// initialize all fields available for selected table
		foreach ($db_columns as $column_name => $column_info) {
			if ($column_info[2] == 1) {
				$r->add_where($column_name, $column_info[1], $column_info[0]);
				$r->change_property($column_name, USE_IN_INSERT, true);
			} elseif ($column_info[2] > 1 && $column_info[3] == true) {
				$r->add_textbox($column_name, $column_info[1], $column_info[0]);
				$r->change_property($column_name, REQUIRED, true);
				if (isset($column_info[4]) && (is_array($column_info[4]) || strlen($column_info[4]))) {
					$r->set_value($column_name, $column_info[4]);
					$r->change_property($column_name, USE_IN_UPDATE, false);
				}
			} elseif ($column_info[2] == USUAL_DB_FIELD && $column_info[3] == false && isset($column_info[4])) {
				$r->add_textbox($column_name, $column_info[1], $column_info[0]);
				if (strlen($column_info[4])) {
					$r->set_value($column_name, $column_info[4]);
				} else {
					// don't put null values if there empty value
					$r->change_property($column_name, USE_SQL_NULL, false);
				}
			}
		}

		// add some additional fields
		add_imported_fields();

		if ($import_related_table) {
			foreach ($related_columns as $column_name => $column_info) {
				if ($column_info[2] == 1) {
					$related_where_column = $column_name;
					$sub_r->add_where($column_name, $column_info[1], $column_info[0]);
					$sub_r->change_property($related_where_column, USE_IN_INSERT, true);
				} elseif ($column_info[2] > 1 && $column_info[3] == true) {
					$sub_r->add_textbox($column_name, $column_info[1], $column_info[0]);
					$sub_r->change_property($column_name, REQUIRED, true);
					if (isset($column_info[4]) && (is_array($column_info[4]) || strlen($column_info[4]))) {
						$sub_r->set_value($column_name, $column_info[4]);
						$sub_r->change_property($column_name, USE_IN_UPDATE, false);
					}
				} elseif ($column_info[2] == HIDE_DB_FIELD || ($column_info[2] == USUAL_DB_FIELD && $column_info[3] == false && isset($column_info[4]))) {
					$sub_r->add_textbox($column_name, $column_info[1], $column_info[0]);
					if (isset($column_info[4]) && strlen($column_info[4])) {
						$sub_r->set_value($column_name, $column_info[4]);
					} else {
						$sub_r->change_property($column_name, USE_SQL_NULL, false);
					}
				}
			}
		}
	
		$column_number = 0; $related_fields = array(); $sub_related_fields = array();
		for ($col = 1; $col <= $total_columns; $col++) {
			$column_name = get_param("db_column_" . $col);
			$column_value = get_param("f_1_" . $col);
			if ($column_name == "-1" || $column_name == "") { // ignoring column
				//$total_columns_left--;
				$column_number++;
				$t->set_var("col", $column_number);
				$t->set_var("column_name", $column_name);
				$t->set_var("column_value", htmlspecialchars($column_value));
				$t->set_var("column_title", $column_title);
				$t->parse("columns_data", true);
			} else {
				$column_number++;
				if ($import_related_table && isset($related_columns[$column_name])) {
					if ($related_columns[$column_name][2] == RELATED_DB_FIELD) {
						$sub_related_fields[] = $col;
					} else {
						$sub_r->add_textbox($column_name, $related_columns[$column_name][1], $related_columns[$column_name][0]);
						if ($related_columns[$column_name][1] == DATE) {
							$sub_r->change_property($column_name, VALUE_MASK, $date_edit_format);
						} elseif ($related_columns[$column_name][1] == DATETIME) {
							$sub_r->change_property($column_name, VALUE_MASK, $datetime_edit_format);
						}
						$sub_r->change_property($column_name, USE_IN_UPDATE, true);
						if ($related_columns[$column_name][2] == USUAL_DB_FIELD && $related_columns[$column_name][3] == false &&
						isset($related_columns[$column_name][4])) {
							if (!strlen($related_columns[$column_name][4])) {
								$sub_r->change_property($column_name, USE_SQL_NULL, false);
							}
						}
					}
				}
				// update fields properties which are available for uploaded CSV file
				if (isset($db_columns[$column_name])) {
					if ($db_columns[$column_name][2] == RELATED_DB_FIELD) {
						$related_fields[] = $col;
						$column_title = $column_value;
					} elseif ($db_columns[$column_name][2] == WHERE_DB_FIELD) {
						$column_title = $db_columns[$column_name][0];
						$is_where = true;
					} else {
						$column_title = $db_columns[$column_name][0];
						// re-initiliaze selected fields
						$r->add_textbox($column_name, $db_columns[$column_name][1], $db_columns[$column_name][0]);
						if ($db_columns[$column_name][1] == DATE) {
							$r->change_property($column_name, VALUE_MASK, $date_edit_format);
						} elseif ($db_columns[$column_name][1] == DATETIME) {
							$r->change_property($column_name, VALUE_MASK, $datetime_edit_format);
						}
						$r->change_property($column_name, USE_IN_UPDATE, true);
						if ($db_columns[$column_name][3] == true) {
							$r->change_property($column_name, REQUIRED, true);
						}
						if ($db_columns[$column_name][2] == USUAL_DB_FIELD && $db_columns[$column_name][3] == false && isset($db_columns[$column_name][4])) {
							if (!strlen($db_columns[$column_name][4])) {
								$r->change_property($column_name, USE_SQL_NULL, false);
							}
						}
						// added additional checks for friendly url field
						if ($column_name == "friendly_url") {
							$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
							$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
						}
					}
				}
				$t->set_var("col", $column_number);
				$t->set_var("column_name", $column_name);
				$t->set_var("column_value", htmlspecialchars($column_value));
				$t->set_var("column_title", $column_title);
				$t->parse("columns_data", true);
				if ($col <= $max_columns) {
					$t->parse("columns_titles", true);
				}
			}
		}

		if ($total_columns_left > $max_columns) {
			$t->set_var("total_colspan", ($max_columns + 1));
		} else {
			$t->set_var("total_colspan", ($total_columns_left + 1));
		}
		$t->set_var("total_columns", $total_columns_left);
	
	
		if ($insert_data && $rnd == $session_rnd) {
			$records_error = get_session("session_records_error");
			$records_added = get_session("session_records_added");
			$records_updated = get_session("session_records_updated");
			$records_ignored = get_session("session_records_ignored");
	
			$operation = "result";
		} else {
			$records_error = 0; $records_added = 0; $records_updated = 0; $records_ignored = 0;
	
			// get max property_id
			$sql  = " SELECT MAX(property_id) FROM " . $table_prefix . "items_properties ";
			$max_property_id = get_db_value($sql);
	
			// start processing rows
			$is_next_row = false;
			$row = $use_first_row ? 1 : 2;
			if (strlen($csv_file_path)) {
				$is_file_path = true;
				$fp = fopen($csv_file_path, "r");
				if ($fp) {
					if (!$use_first_row) {
						$data = fgetcsv($fp, 65536, $delimiter_char);
					}
					$data = fgetcsv($fp, 65536, $delimiter_char);
					$is_next_row = is_array($data);
				}
			} else {
				$is_next_row = ($row <= $total_rows);
			}
			$prev_item_id = 0;
			while ($is_next_row) {
				$t->set_var("cols", "");
				$column_number = 0;
				foreach ($db_columns as $column_name => $column_info) {
					if ($column_info[2] > 1 && $column_info[3] == true) {
						if (isset($column_info[4]) && (is_array($column_info[4]) || strlen($column_info[4]))) {
							$r->set_value($column_name, $column_info[4]);
						}
					}
				}
				if ($import_related_table) {
					foreach ($related_columns as $column_name => $column_info) {
						if ($column_info[2] > 1 && $column_info[3] == true) {
							if (isset($column_info[4]) && (is_array($column_info[4]) || strlen($column_info[4]))) {
								$sub_r->set_value($column_name, $column_info[4]);
							}
						}
					}
				}
				$imported_fields  = "";
				$sub_r_fields     = array();
				$sub_r_fields_max = 0;
				for ($col = 1; $col <= $total_columns; $col++) {
					$column_name = get_param("db_column_" . $col);
					$imported_fields .= "," . $column_name;
					if ($column_name != "-1" && $column_name != "") {
						$column_number++;
						$param_name = "f_" . $row . "_" . $col;
						$field_name = "f_" . $row . "_" . $column_number;
						if ($is_file_path) {
							$field_value = isset($data[$col - 1]) ? $data[$col - 1] : "";
						} else {
							$field_value = get_param($param_name);
						}
						$strip_field_value = strip_tags($field_value);
						$short_field_value = (strlen($strip_field_value) > 15) ? substr($strip_field_value, 0, 15) . "..." : $strip_field_value;
						$column_name = get_param("db_column_" . $col);
	
						if ($import_related_table && isset($related_columns[$column_name])) {
							$new_field_value = trim(implode(',', array_unique( explode($related_delimiter_char, $field_value) ) ) );
							if (!strlen($new_field_value)) $new_field_value = NULL;
							if ($related_columns[$column_name][2] != RELATED_DB_FIELD) {
								$sub_r->set_value($column_name, $new_field_value);
								if ($related_delimiter_char != "row") {
									$field_value_exploded = explode($related_delimiter_char, $field_value);
									if ($sub_r_fields_max < count($field_value_exploded)) {
										$sub_r_fields_max = count($field_value_exploded);
									}
									$sub_r_fields[] = array ("name" => $column_name, "values" => $field_value_exploded);
								}
							}
							if (isset($db_columns[$column_name]) && ($db_columns[$column_name][2] != RELATED_DB_FIELD)) {
								if (!$r->get_value($column_name)) {
									$r->set_value($column_name, $new_field_value);
								}
							}
						} elseif ($db_columns[$column_name][2] != RELATED_DB_FIELD) {
							/* allow to replace wrong digit separator
							if ($db_columns[$column_name][1] == FLOAT || $db_columns[$column_name][1] == NUMBER) {
							$field_value = str_replace(",", ".", $field_value);
							$field_value = str_replace(";", ".", $field_value);
							}//*/
							$r->set_value($column_name, $field_value);
						}
	
						$t->set_var("short_field_value", htmlspecialchars($short_field_value));
						if (!$is_file_path) {
							$t->set_var("field_name", $field_name);
							$t->set_var("field_value", htmlspecialchars($field_value));
							$t->parse("fields_info", true);
						}
						if ($col <= $max_columns) {
							$t->parse("cols", true);
						}
					} else {
						$column_number++; // just increment column number for ignored columns
					}
				}

//echo "<br>item_id:".$r->get_value("item_id");
			  // check if some primary field can be ignored
				if ($table == "items" && ($match_item_code || $match_manufacturer_code)) {
//echo "<br>in";
					$r->change_property("item_code", USE_IN_WHERE, false);
					$r->change_property("manufacturer_code", USE_IN_WHERE, false);
					if (!$r->is_empty("item_id")) {
//echo "<br>ID";
						$r->change_property("item_id", USE_IN_WHERE, true);
					} else {
						$r->change_property("item_id", USE_IN_WHERE, false);
						if ($match_item_code && !$r->is_empty("item_code")) {
							$r->change_property("item_code", USE_IN_WHERE, true);
						}
						if ($match_manufacturer_code && !$r->is_empty("manufacturer_code")) {
							$r->change_property("manufacturer_code", USE_IN_WHERE, true);
						}
					}
				}
	
				$r->errors = "";
				if ($table == "orders") {
					before_orders_save();
				}
				if ($table == "users" || $table == "orders") {
					check_country_state();
				}
				$r->validate();
	
				if ($insert_data) {
					if ($r->errors) {
						$records_ignored++;
					} else {
						$is_exists = false;
						$is_where_set = ($is_where && $r->check_where());
						if ($is_where_set) {
							$sql  = " SELECT " . $table_pk . " FROM " . $table_name . "";
							$sql .= $r->get_where();
							$db->query($sql);
							if ($db->next_record()) {
								$is_exists = true;
							}
						}
	
						$inserted_id = ""; $updated_id = "";
	
						$new_item_id = $r->get_value($table_pk);
						
						if ($import_related_table && $new_item_id>0 && ($new_item_id == $prev_item_id)) {
							// do nothing
						} elseif ($is_exists) {
							import_friendly_url(); // function to check duplicated friendly urls
							if ($r->update_record()) {
								$records_updated++;
								$updated_id = $r->get_value($table_pk);
							} else {
								$records_error++;
							}
						} else {
							if ($r->is_empty($table_pk)) {
								$sql = "SELECT MAX(" . $table_pk . ") FROM " . $table_name ;
								$max_id = get_db_value($sql);
								$r->set_value($table_pk, ($max_id + 1));
							}
							import_friendly_url(); // function to check duplicated friendly urls
							if ($r->insert_record()) {
								$records_added++;
								$inserted_id = $r->get_value($table_pk);
							} else {
								$records_error++;
							}
						}
						
						if ($inserted_id || $updated_id) {							
							if ($table == "orders") {
								after_orders_save($inserted_id ? $inserted_id : $updated_id);
							}
						}
	
						$new_item_id = $r->get_value($table_pk);
	
						// save order products
						if ($import_related_table) {
							if (isset($related_columns_from_main)) {
								foreach ($related_columns_from_main AS $related_column_name) {
									if (!$r->is_empty($related_column_name)) {
										$sub_r->add_textbox($related_column_name, $db_columns[$related_column_name][1]);
										$sub_r->set_value($related_column_name, $r->get_value($related_column_name));
									}
								}
							}
							$sub_r->add_textbox($table_pk, INTEGER);
							$sub_r->set_value($table_pk, $new_item_id);
							if ($new_item_id != $prev_item_id) {
								$sql = " DELETE FROM " . $related_table_name . " WHERE " . $table_pk . "=" . $db->tosql($new_item_id, INTEGER);
								$db->query($sql);
								if ($table == "orders") {
									$sql = " DELETE FROM " . $table_prefix . "orders_items_properties WHERE order_id=" . $db->tosql($new_item_id, INTEGER);
									$db->query($sql);
								}
							}
							$sql = "SELECT MAX(" . $related_aliases["id"] . ") FROM " . $related_table_name;
							$max_sub_id = get_db_value($sql);
							if ($related_delimiter_char == "row" || !(isset($sub_r_fields)) || !($sub_r_fields_max>1)) {
								$max_sub_id++;
								$sub_r->set_value($related_aliases["id"], $max_sub_id);		
								if ($related_table == "orders_items") {
									before_orders_items_save();
								}					
								$sub_r->insert_record();
								if ($related_table == "orders_items") {
									after_orders_items_save();
								}													
								for ($ri = 0; $ri < sizeof($sub_related_fields); $ri++) {
									$related_col   = $sub_related_fields[$ri];
									$column_name   = get_param("db_column_" . $related_col);
									$column_value  = get_param("db_value_" . $related_col);
									$related_prop_table = $related_columns[$column_name][4];
									$related_prop_field = "f_" . $row . "_" . $related_col;
									if ($is_file_path) {
										$related_value = isset($data[$related_col - 1]) ? $data[$related_col - 1] : "";
									} else {
										$related_value = get_param($related_prop_field);
									}
									if ($related_prop_table == $table_prefix . "order_items_properties") {
										update_orders_items_properties($sub_r->get_value($related_aliases["id"]), $new_item_id, $related_value);
									}
								}
							} else {
								// products in one line separated by \t \s etc
								$sub_r_inserted_ids=array();
								for ($sb=0; $sb < $sub_r_fields_max; $sb++) {
									for ($sf=0; $sf < count($sub_r_fields); $sf++) {
										$column_name = $sub_r_fields[$sf]["name"];
										if (isset($sub_r_fields[$sf]["values"][$sb])) {
											$field_value = $sub_r_fields[$sf]["values"][$sb];
										} elseif (isset($sub_r_fields[$sf]["values"][0])) {
											$field_value = $sub_r_fields[$sf]["values"][0];
										} elseif (isset($related_columns[$column_name][4])) {
											$field_value = $related_columns[$column_name][4];
										} else {
											$field_value = NULL;
										}
										if ($related_columns[$column_name][2] != RELATED_DB_FIELD) {
											$sub_r->set_value($column_name, $field_value);
										}
									}
									$max_sub_id++;
									$sub_r->set_value($related_aliases["id"], $max_sub_id);								
									if ($related_table == "orders_items") {
										before_orders_items_save();
									}							
									$sub_r->insert_record();												
									$sub_r_inserted_ids[] = $sub_r->get_value($related_aliases["id"]);
									if ($related_table == "orders_items") {
										after_orders_items_save();
									}		
								}
								
								for ($ri = 0; $ri < sizeof($sub_related_fields); $ri++) {
									$related_col   = $sub_related_fields[$ri];
									$column_name   = get_param("db_column_" . $related_col);
									$column_value  = get_param("db_value_" . $related_col);
									$related_prop_table = $related_columns[$column_name][4];
									$related_prop_field = "f_" . $row . "_" . $related_col;
									if ($is_file_path) {
										$related_value = isset($data[$related_col - 1]) ? $data[$related_col - 1] : "";
									} else {
										$related_value = get_param($related_prop_field);
									}
									if ($related_prop_table == $table_prefix . "order_items_properties") {									
										$field_value_exploded = explode($related_delimiter_char, $related_value);								
										for ($sb=0; $sb<$sub_r_fields_max; $sb++){
											if (isset($field_value_exploded[$sb]) && $field_value_exploded[$sb]) {
												update_orders_items_properties($sub_r_inserted_ids[$sb], $new_item_id, $field_value_exploded[$sb]);
											}
										}
									}
								}
							}
						}
	
						$prev_item_id = $r->get_value($table_pk);
						// end save order products
	
						// added data to related tables
						if (strlen($inserted_id) || strlen($updated_id))
						{
							$property_order = 0; $category_column = false;
							for ($ri = 0; $ri < sizeof($related_fields); $ri++) {
								$related_col = $related_fields[$ri];
								$column_value = get_param("f_1_" . $related_col);
								$column_name = get_param("db_column_" . $related_col);
								$related_prop_table = $db_columns[$column_name][4];
								$related_prop_field = "f_" . $row . "_" . $related_col;
								if ($is_file_path) {
									$related_value = isset($data[$related_col - 1]) ? $data[$related_col - 1] : "";
								} else {
									$related_value = get_param($related_prop_field);
								}
	
								$item_id = strlen($inserted_id) ? $inserted_id : $updated_id;
								if ($related_prop_table == $table_prefix . "order_properties") {
									update_orders_properties($item_id, $related_value);
								} elseif ($related_prop_table == $table_prefix . "items_properties") {
									update_items_properties($item_id, $related_value);
								} elseif ($related_prop_table == $table_prefix . "categories") {
									$category_column = true;
									update_items_categories($item_id, $related_value);
								} elseif ($related_prop_table == $table_prefix . "manufacturers") {
									update_manufacturer($item_id, $related_value);
								} elseif ($related_prop_table == $table_prefix . "features") {
									update_items_features($item_id, $related_value);
								}
							}
	
							// added link to category
							if ($table_name == $table_prefix . "items" && !$category_column && strlen($inserted_id)) {
								$sql  = " INSERT INTO " . $table_prefix . "items_categories (item_id, category_id) ";
								$sql .= " VALUES (" . $db->tosql($inserted_id, INTEGER) . ", " . $db->tosql($category_id, INTEGER) . ") ";
								$db->query($sql);
							}
						}
	
	
					}
				} else {
					if ($r->errors) {
						$total_errors++;
						$t->set_var("status", "<font color=red>".ADMIN_ERROR_MSG."</font>");
						$t->set_var("error_value", "1");
						$t->set_var("row_class", "error");
						$t->set_var("errors_list", $r->errors);
						$t->parse("row_errors", false);
					} else {
						$is_exists = false;
						if ($table == "orders") {
							before_orders_search();
						}
						// check record only if it exists
						if ($is_where) {
							$is_exists = $r->get_db_values();
						}
						$status = $is_exists ? "<font color=green>".EXISTS_MSG."</font>" : "<font color=blue>".NEW_MSG."</font>";
						$t->set_var("status", $status);
						$t->set_var("is_exists", intval($is_exists));
						$t->set_var("error_value", "0");
						$t->set_var("row_class", "usual");
						$t->set_var("row_errors", "");
					}
	
					$t->set_var("row", $row);
					$t->parse("rows", true);
				}
				// check if there are more rows to process
				$row++;
				if ($is_file_path) {
					$data = fgetcsv($fp, 65536, $delimiter_char);
					$is_next_row = is_array($data);
				} else {
					$is_next_row = ($row <= $total_rows);
				}
			}
			// end processing rows
			if ($is_file_path) {
				fclose($fp);
			}

			// update imported fields
			if ($table == "users") {
				$sql  = " UPDATE " . $table_prefix . "admins SET imported_user_fields=" . $db->tosql($imported_fields, TEXT);
				$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$db->query($sql);
			} elseif ($table == "newsletters_users") {
				$sql  = " UPDATE " . $table_prefix . "admins SET imported_email_fields=" . $db->tosql($imported_fields, TEXT);
				$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$db->query($sql);
			} elseif ($table == "items") {
				$sql = " UPDATE " . $table_prefix . "admins SET imported_item_fields=" . $db->tosql($imported_fields, TEXT);
				$sql .= " WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
				$db->query($sql);
			}
	
			if ($insert_data)
			{
				if ($table_name == $table_prefix . "categories") {
					prepare_categories_list();
					update_categories_tree(0, "");
				}
	
				set_session("session_records_error", $records_error);
				set_session("session_records_added", $records_added);
				set_session("session_records_updated", $records_updated);
				set_session("session_records_ignored", $records_ignored);
	
				$operation = "result";
	
				// it's temporary file which can be deleted
				if (preg_match("/tmp_[0-9a-f]{32}\.csv/", $csv_file_path)) {
					@unlink($csv_file_path);
				}
			}
		}
	
		$total_rows = $row - 1;
		$t->set_var("total_rows", $total_rows);
	} elseif ($operation == "insert") {
		// import data after preview

		if ($rnd != $session_rnd) {
			set_session("session_rnd", $rnd);
			$total_rows = get_param("total_rows");
			$total_columns = get_param("total_columns");
	
			$r = new VA_Record($table_name);
			if ($import_related_table) {
				$sub_r = new VA_Record($related_table_name);
				if ($related_table == "orders_items") {
					$sub_r->add_textbox("item_id", INTEGER);
					$sub_r->change_property("item_id", USE_SQL_NULL, false);
				}
			}
			$r_no = array();
			
			foreach ($db_columns as $column_name => $column_info) {
				if ($column_info[2] == WHERE_DB_FIELD) {
					$r->add_where($column_name, $column_info[1], $column_info[0]);
					$r->change_property($column_name, USE_IN_INSERT, true);
				} elseif ($column_info[2] > 1 && $column_info[3] == true) {
					$r->add_textbox($column_name, $column_info[1], $column_info[0]);
					$r->change_property($column_name, REQUIRED, true);
					if (isset($column_info[4]) && (is_array($column_info[4]) || strlen($column_info[4]))) {
						$r->set_value($column_name, $column_info[4]);
						$r->change_property($column_name, USE_IN_UPDATE, false);
					}
				} elseif ($column_info[2] == USUAL_DB_FIELD && $column_info[3] == false && isset($column_info[4])) {
					$r->add_textbox($column_name, $column_info[1], $column_info[0]);
					if (strlen($column_info[4])) {
						$r->set_value($column_name, $column_info[4]);
					} else {
						// don't put null values if there empty value
						$r->change_property($column_name, USE_SQL_NULL, false);
					}
				}
			}

			// add some additional fields
			add_imported_fields();

			if ($import_related_table) {
				foreach ($related_columns as $column_name => $column_info) {
					if ($column_info[2] == 1) {
						$related_where_column = $column_name;
						$sub_r->add_where($column_name, $column_info[1], $column_info[0]);
						$sub_r->change_property($related_where_column, USE_IN_INSERT, true);
					} elseif ($column_info[2] > 1 && $column_info[3] == true) {
						$sub_r->add_textbox($column_name, $column_info[1], $column_info[0]);
						$sub_r->change_property($column_name, REQUIRED, true);
						if (isset($column_info[4]) && (is_array($column_info[4]) || strlen($column_info[4]))) {
							$sub_r->set_value($column_name, $column_info[4]);
							$sub_r->change_property($column_name, USE_IN_UPDATE, false);
						}
					} elseif ($column_info[2] == HIDE_DB_FIELD || ($column_info[2] == USUAL_DB_FIELD && $column_info[3] == false && isset($column_info[4]))) {
						$sub_r->add_textbox($column_name, $column_info[1], $column_info[0]);
						if (isset($column_info[4]) && strlen($column_info[4])) {
							$sub_r->set_value($column_name, $column_info[4]);
						} else {
							$sub_r->change_property($column_name, USE_SQL_NULL, false);
						}
					}
				}
			}
	
			$imported_fields = "";
			$is_where = false; $related_fields = array(); $sub_related_fields = array();
			for ($col = 1; $col <= $total_columns; $col++) {
				$column_name = get_param("db_column_" . $col);
				$column_value = get_param("db_value_" . $col);
				$imported_fields .= "," . $column_name;
				if ($import_related_table && isset($related_columns[$column_name])) {
					if ($related_columns[$column_name][2] == RELATED_DB_FIELD) {
						$sub_related_fields[] = $col;
					} else {
						$sub_r->add_textbox($column_name, $related_columns[$column_name][1], $related_columns[$column_name][0]);
						if ($related_columns[$column_name][1] == DATE) {
							$sub_r->change_property($column_name, VALUE_MASK, $date_edit_format);
						} elseif ($related_columns[$column_name][1] == DATETIME) {
							$sub_r->change_property($column_name, VALUE_MASK, $datetime_edit_format);
						}
						$sub_r->change_property($column_name, USE_IN_UPDATE, true);
						if ($related_columns[$column_name][2] == USUAL_DB_FIELD && $related_columns[$column_name][3] == false &&
						isset($related_columns[$column_name][4])) {
							if (!strlen($related_columns[$column_name][4])) {
								$sub_r->change_property($column_name, USE_SQL_NULL, false);
							}
						}
					}
				}
				if (isset($db_columns[$column_name])) {
					if ($db_columns[$column_name][2] == WHERE_DB_FIELD) {
						$is_where = true;
					} elseif ($db_columns[$column_name][2] == RELATED_DB_FIELD) {
						$related_fields[] = $col;
					} else {
						$r->add_textbox($column_name, $db_columns[$column_name][1], $db_columns[$column_name][0]);
						if ($db_columns[$column_name][1] == DATE) {
							$r->change_property($column_name, VALUE_MASK, $date_edit_format);
						} elseif ($db_columns[$column_name][1] == DATETIME) {
							$r->change_property($column_name, VALUE_MASK, $datetime_edit_format);
						}
						$r->change_property($column_name, USE_IN_UPDATE, true);
						if ($db_columns[$column_name][2] == USUAL_DB_FIELD && $db_columns[$column_name][3] == false && isset($db_columns[$column_name][4])) {
							if (!strlen($db_columns[$column_name][4])) {
								$r->change_property($column_name, USE_SQL_NULL, false);
							}
						}
						// added additional checks for friendly url field
						if ($column_name == "friendly_url") {
							$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
							$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
						}
					}
				}
			}
	
			// get max property_id
			$sql  = " SELECT MAX(property_id) FROM " . $table_prefix . "items_properties ";
			$max_property_id = get_db_value($sql);
	
			$records_error = 0;
			$records_added = 0;
			$records_updated = 0;
			$records_ignored = 0;
	
			// start processing rows
			$is_next_row = false;
			$row = $use_first_row ? 1 : 2;
			if (strlen($csv_file_path)) {
				$is_file_path = true;
				$fp = fopen($csv_file_path, "r");
				if ($fp) {
					if (!$use_first_row) {
						$data = fgetcsv($fp, 65536, $delimiter_char);
					}
					$data = fgetcsv($fp, 65536, $delimiter_char);
					$is_next_row = is_array($data);
				}
			} else {
				$is_next_row = ($row <= $total_rows);
			}

			// start processing rows
			$prev_item_id = 0;
			while ($is_next_row)
			{
				$sub_r_fields = array();
				$sub_r_fields_max = 0;
				$use_row = get_param("use_" . $row);
				$error_row = get_param("error_" . $row);
				if ($use_row == 1 && $error_row != 1) {
					$is_exists = get_param("is_exists_" . $row);
					$is_where_set = false;				
					for ($col = 1; $col <= $total_columns; $col++) {
						$field_name = "f_" . $row . "_" . $col;
						if ($is_file_path) {
							$field_value = isset($data[$col - 1]) ? $data[$col - 1] : "";
						} else {
							$field_value = get_param($field_name);
						}
						$column_name = get_param("db_column_" . $col);
						$column_value = get_param("db_value_" . $col);
						if ($column_name != -1) { // check if column not ignored
							if ($import_related_table && isset($related_columns[$column_name])) {
								$new_field_value = trim(implode(',', array_unique( explode($related_delimiter_char, $field_value) ) ) );
								if (!strlen($new_field_value)) $new_field_value = NULL;
								if ($related_columns[$column_name][2] != RELATED_DB_FIELD) {
									$sub_r->set_value($column_name, $new_field_value);
									if ($related_delimiter_char != "row") {
										$field_value_exploded = explode($related_delimiter_char, $field_value);
										if ($sub_r_fields_max < count($field_value_exploded)) {
											$sub_r_fields_max = count($field_value_exploded);
										}
										$sub_r_fields[] = array ("name" => $column_name, "values" => $field_value_exploded);
									}
								}
								if (isset($db_columns[$column_name]) && ($db_columns[$column_name][2] != RELATED_DB_FIELD)) {
									if (!$r->get_value($column_name)) {
										$r->set_value($column_name, $new_field_value);
									}
								}
							} elseif ($db_columns[$column_name][2] != RELATED_DB_FIELD) {
								/* allow to replace wrong digit separator
								if ($db_columns[$column_name][1] == FLOAT || $db_columns[$column_name][1] == NUMBER) {
								$field_value = str_replace(",", ".", $field_value);
								$field_value = str_replace(";", ".", $field_value);
								}//*/
								$r->set_value($column_name, $field_value);
							}
						}
					}

			    // check if some primary field can be ignored
					if ($table == "items" && ($match_item_code || $match_manufacturer_code)) {
						$r->change_property("item_code", USE_IN_WHERE, false);
						$r->change_property("manufacturer_code", USE_IN_WHERE, false);
						if (!$r->is_empty("item_id")) {
							$r->change_property("item_id", USE_IN_WHERE, true);
						} else {
							$r->change_property("item_id", USE_IN_WHERE, false);
							if ($match_item_code && !$r->is_empty("item_code")) {
								$r->change_property("item_code", USE_IN_WHERE, true);
							}
							if ($match_manufacturer_code && !$r->is_empty("manufacturer_code")) {
								$r->change_property("manufacturer_code", USE_IN_WHERE, true);
							}
						}
					}

					// check if all where parameters set
					$is_where_set = ($is_where && $r->check_where());

					$inserted_id = ""; $updated_id = "";
					$new_item_id = $r->get_value($table_pk);

					if ($import_related_table && $new_item_id && ($new_item_id == $prev_item_id)) {
						// do nothing
					} elseif ($is_exists) {
						if ($table == "orders") {
							before_orders_save();
						}
						if ($table == "users" || $table == "orders") {
							check_country_state();
						}
						import_friendly_url(); // function to check duplicated friendly urls
						if ($r->update_record()) {
							$records_updated++;
							$updated_id = $r->get_value($table_pk);
						} else {
							$records_error++;
						}
					} else {
						if ($r->is_empty($table_pk)) {
							$sql = "SELECT MAX(" . $table_pk . ") FROM " . $table_name ;
							$max_id = get_db_value($sql);
							$r->set_value($table_pk, ($max_id + 1));
						}
						if ($table == "orders") {
							before_orders_save();
						}
						if ($table == "users" || $table == "orders") {
							check_country_state();
						}

						import_friendly_url(); // function to check duplicated friendly urls
						if ($r->insert_record()) {
							$records_added++;
							$inserted_id = $r->get_value($table_pk);
						} else {
							$records_error++;
						}
					}
	
					if ($inserted_id || $updated_id) {
						if ($table == "orders") {
							after_orders_save($inserted_id ? $inserted_id : $updated_id);
						}
					}
						
					$new_item_id = $r->get_value($table_pk);
	
					// save order products
					if ($import_related_table) {
						if (isset($related_columns_from_main)) {
							foreach ($related_columns_from_main AS $related_column_name) {
								if (!$r->is_empty($related_column_name)) {
									$sub_r->add_textbox($related_column_name, $db_columns[$related_column_name][1]);
									$sub_r->set_value($related_column_name, $r->get_value($related_column_name));
								}
							}
						}
						$sub_r->add_textbox($table_pk, INTEGER);
						$sub_r->set_value($table_pk, $new_item_id);
						if ($new_item_id != $prev_item_id) {
							$sql = " DELETE FROM " . $related_table_name . " WHERE " . $table_pk . "=" . $db->tosql($new_item_id, INTEGER);
							$db->query($sql);
							if ($table == "orders") {
								$sql = " DELETE FROM " . $table_prefix . "orders_items_properties WHERE order_id=" . $db->tosql($new_item_id, INTEGER);
								$db->query($sql);
							}
						}
						$sql = "SELECT MAX(" . $related_aliases["id"] . ") FROM " . $related_table_name;
						$max_sub_id = get_db_value($sql);
						if ($related_delimiter_char == "row" || !(isset($sub_r_fields)) || !($sub_r_fields_max>1)) {
							$max_sub_id++;
							$sub_r->set_value($related_aliases["id"], $max_sub_id);
							if ($related_table == "orders_items") {
								before_orders_items_save();
							}							
							$sub_r->insert_record();
							if ($related_table == "orders_items") {
								after_orders_items_save();
							}				
							for ($ri = 0; $ri < sizeof($sub_related_fields); $ri++) {
								$related_col   = $sub_related_fields[$ri];
								$column_name   = get_param("db_column_" . $related_col);
								$column_value  = get_param("db_value_" . $related_col);
								$related_prop_table = $related_columns[$column_name][4];
								$related_prop_field = "f_" . $row . "_" . $related_col;
								if ($is_file_path) {
									$related_value = isset($data[$related_col - 1]) ? $data[$related_col - 1] : "";
								} else {
									$related_value = get_param($related_prop_field);
								}
								if ($related_prop_table == $table_prefix . "order_items_properties") {
									update_orders_items_properties($sub_r->get_value($related_aliases["id"]), $new_item_id, $related_value);
								}
							}
						} else {
							// products in one line separated by \t \s etc
							$sub_r_inserted_ids=array();
							for ($sb=0; $sb < $sub_r_fields_max; $sb++) {
								for ($sf=0; $sf < count($sub_r_fields); $sf++) {
									$column_name = $sub_r_fields[$sf]["name"];
									if (isset($sub_r_fields[$sf]["values"][$sb])) {
										$field_value = $sub_r_fields[$sf]["values"][$sb];
									} elseif (isset($sub_r_fields[$sf]["values"][0])) {
										$field_value = $sub_r_fields[$sf]["values"][0];
									} elseif (isset($related_columns[$column_name][4])) {
										$field_value = $related_columns[$column_name][4];
									} else {
										$field_value = NULL;
									}
									if ($related_columns[$column_name][2] != RELATED_DB_FIELD) {
										$sub_r->set_value($column_name, $field_value);
									}
								}
								$max_sub_id++;
								$sub_r->set_value($related_aliases["id"], $max_sub_id);
								if ($related_table == "orders_items") {
									before_orders_items_save();
								}							
								$sub_r->insert_record();
								$sub_r_inserted_ids[] = $sub_r->get_value($related_aliases["id"]);
								if ($related_table == "orders_items") {
									after_orders_items_save();
								}					
							}
							
							for ($ri = 0; $ri < sizeof($sub_related_fields); $ri++) {
								$related_col   = $sub_related_fields[$ri];
								$column_name   = get_param("db_column_" . $related_col);
								$column_value  = get_param("db_value_" . $related_col);
								$related_prop_table = $related_columns[$column_name][4];
								$related_prop_field = "f_" . $row . "_" . $related_col;
								if ($is_file_path) {
									$related_value = isset($data[$related_col - 1]) ? $data[$related_col - 1] : "";
								} else {
									$related_value = get_param($related_prop_field);
								}
								if ($related_prop_table == $table_prefix . "order_items_properties") {									
									$field_value_exploded = explode($related_delimiter_char, $related_value);								
									for ($sb=0; $sb<$sub_r_fields_max; $sb++){
										if (isset($field_value_exploded[$sb]) && $field_value_exploded[$sb]) {
											update_orders_items_properties($sub_r_inserted_ids[$sb], $new_item_id, $field_value_exploded[$sb]);
										}
									}
								}
							}
						}
					}
	
					$prev_item_id = $r->get_value($table_pk);
	
					// added data to related tables
					if (strlen($inserted_id) || strlen($updated_id))
					{
						$property_order = 0; $category_column = false;
						for ($ri = 0; $ri < sizeof($related_fields); $ri++) {
							$related_col = $related_fields[$ri];
							$column_name = get_param("db_column_" . $related_col);
							$column_value = get_param("db_value_" . $related_col);
							$related_prop_table = $db_columns[$column_name][4];
							$related_prop_field = "f_" . $row . "_" . $related_col;
							if ($is_file_path) {
								$related_value = isset($data[$related_col - 1]) ? $data[$related_col - 1] : "";
							} else {
								$related_value = get_param($related_prop_field);
							}
	
							$item_id = strlen($inserted_id) ? $inserted_id : $updated_id;
							if ($related_prop_table == $table_prefix . "order_properties") {
								update_orders_properties($item_id, $related_value);
							} elseif ($related_prop_table == $table_prefix . "items_properties") {
								update_items_properties($item_id, $related_value);
							} elseif ($related_prop_table == $table_prefix . "categories") {
								$category_column = true;
								update_items_categories($item_id, $related_value);
							} elseif ($related_prop_table == $table_prefix . "manufacturers") {
								update_manufacturer($item_id, $related_value);
							} elseif ($related_prop_table == $table_prefix . "features") {
								update_items_features($item_id, $related_value);
							}
						}
	
						// added link to category
						if ($table_name == $table_prefix . "items" && !$category_column && strlen($inserted_id)) {
							$sql  = " INSERT INTO " . $table_prefix . "items_categories (item_id, category_id) ";
							$sql .= " VALUES (" . $db->tosql($inserted_id, INTEGER) . ", " . $db->tosql($category_id, INTEGER) . ") ";
							$db->query($sql);
						}
					}
				}
				else
				{
					$records_ignored++;
				}
				// check if there are more rows to process
				$row++;
				if ($is_file_path) {
					$data = fgetcsv($fp, 65536, $delimiter_char);
					$is_next_row = is_array($data);
				} else {
					$is_next_row = ($row <= $total_rows);
				}
			}
			// end processing rows
			if ($is_file_path) {
				fclose($fp);
			}

			if ($table_name == $table_prefix . "categories") {
				prepare_categories_list();
				update_categories_tree(0, "");
			}
	
			set_session("session_records_error", $records_error);
			set_session("session_records_added", $records_added);
			set_session("session_records_updated", $records_updated);
			set_session("session_records_ignored", $records_ignored);
	
			$operation = "result";
	
			// it's temporary file which can be deleted
			if (preg_match("/tmp_[0-9a-f]{32}\.csv/", $csv_file_path)) {
				@unlink($csv_file_path);
			}
		} else {
			$operation = "result";
		}
	}
	
	if (strlen($errors))
	{
		//$t->set_var("after_upload", "");
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	}
	else
	{
		$t->set_var("errors", "");
	}
	
	$t->set_var("operation", $operation);
	$t->set_var("category_id", $category_id);
	$t->set_var("csv_file_path", htmlspecialchars($csv_file_path));
	$t->set_var("csv_delimiter", htmlspecialchars($csv_delimiter));
	
	$t->set_var("use_first_row", $use_first_row);
	$t->set_var("rnd", va_timestamp());
	
	$t->set_var("csv_related_delimiter", htmlspecialchars($csv_related_delimiter));
	$t->set_var("import_related_table", $import_related_table);
	
	$t->set_var("import_block", "");
	$t->set_var("insert_block", "");
	$t->set_var("upload_block", "");
	$t->set_var("result_block", "");
	if ($operation == "import") {
		$imported_fields = "";
		if ($table == "users") {
			$sql  = " SELECT imported_user_fields FROM " . $table_prefix . "admins WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
			$imported_fields = get_db_value($sql);
		} elseif ($table == "newsletters_users") {
			$sql  = " SELECT imported_email_fields FROM " . $table_prefix . "admins WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
			$imported_fields = get_db_value($sql);
		} elseif ($table == "items") {
			$sql  = " SELECT imported_item_fields FROM " . $table_prefix . "admins WHERE admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
			$imported_fields = get_db_value($sql);
		}
		if (strlen($imported_fields)) {
			$t->set_var("imported_fields", htmlspecialchars($imported_fields));
			$t->parse("match_option", false);
		} else {
			$t->set_var("match_option", "");
		}
		$t->parse("import_block", false);
	} elseif ($operation == "insert") {
		$t->parse("insert_block", false);
	} elseif ($operation == "result") {
		$records_error = get_session("session_records_error");
		$records_added = get_session("session_records_added");
		$records_updated = get_session("session_records_updated");
		$records_ignored = get_session("session_records_ignored");
	
		if ($records_error > 0) {
			$t->set_var("records_error", $records_error);
			$t->parse("db_errors", $records_error);
		}
		$t->set_var("records_added", $records_added);
		$t->set_var("records_updated", $records_updated);
		$t->set_var("records_ignored", $records_ignored);
	
		$t->parse("result_block", false);
	} else {
		$operation = "upload";
		$delimiters = array(array(",", COMMA_MSG), array("tab", TAB_MSG), array(";", SEMICOLON_MSG));
		$related_delimiters = array(array("row", ROWS_MSG), array("comma", COMMA_MSG), array("tab", TAB_MSG), array("space", SPACE_MSG), array("semicolon", SEMICOLON_MSG), array("newline", NEWLINE_MSG));
		set_options($delimiters, $csv_delimiter, "delimiter");
		if (isset($related_table)) {
			set_options($related_delimiters, $csv_related_delimiter, "related_delimiter");
			$t->parse("related_delimiter_block", false);
		}
		$t->parse("upload_block", false);
	}
	
	if ($table_name == ($table_prefix . "items") || $table_name == ($table_prefix . "categories")) {
		$t->parse("products_path", false);
	} else {
		$t->set_var("products_path", "");
	}
	
	if ($table_name == ($table_prefix . "newsletters_users")) {
		$t->parse("newsletters_path", false);
	}
	
	$t->pparse("main");
	
	function before_orders_search() {
		global $r;
		if ($r->parameter_exists("parent_order_id") && $r->is_empty("parent_order_id")) {
			$r->set_value("parent_order_id", 0);
		}
	}
	
	function before_orders_save()
	{
		global $r, $sub_r, $related_delimiter_char;
		global $r_no, $db, $table_prefix;
		
		if ($r->parameter_exists("parent_order_id") && $r->is_empty("parent_order_id")) {
			$r->set_value("parent_order_id", 0);
		}
		
		if (!$r->is_empty("order_id")) {
			$order_id = $r->get_value("order_id");
			if (!is_integer($order_id)) {			
				$order_id = substr(str_replace(array("-", " "), "", $order_id), 0, 6);
				$r->set_value("order_id", $order_id);				
			}
		}
		
		if ($r->is_empty("order_status") || isset($r_no["order_status"])) {
			$r->add_textbox("order_status", INTEGER);
			$r->set_value("order_status", 4);
			$r_no["order_status"] = 1;
		}
		if (isset($sub_r)) {
			if ($r->is_empty("total_quantity") || isset($r_no["total_quantity"])) {
				$r->add_textbox("total_quantity", INTEGER);
				$r->change_property("total_quantity", USE_SQL_NULL, false);
				$quantity = $sub_r->get_value("quantity");
				$quantity_exploded = explode($related_delimiter_char, $quantity);
				$r->set_value("total_quantity", array_sum($quantity_exploded));
				$r_no["total_quantity"] = 1;
			}
			if ($r->is_empty("goods_total") || isset($r_no["goods_total"])) {
				$r->add_textbox("goods_total", INTEGER);
				$r->change_property("goods_total", USE_SQL_NULL, false);
				$price = $sub_r->get_value("price");
				$price_exploded = explode($related_delimiter_char, $price);
				$quantity = $sub_r->get_value("quantity");
				$quantity_exploded = explode($related_delimiter_char, $quantity);
				$goods_total = 0;
				for ($i=0, $m=min(count($price_exploded),count($quantity_exploded)); $i<$m; $i++) {
					$goods_total = $price_exploded[$i]*$quantity_exploded[$i];
				}
				$r->set_value("goods_total", $goods_total);
				$r_no["goods_total"] = 1;
			}
			if ($r->is_empty("total_buying") || isset($r_no["total_buying"])) {
				$r->add_textbox("total_buying", INTEGER);
				$r->change_property("total_buying", USE_SQL_NULL, false);
				$quantity = $sub_r->get_value("quantity");
				$quantity_exploded = explode($related_delimiter_char, $quantity);
				$r->set_value("total_buying", array_sum($quantity_exploded));
				$r_no["total_buying"] = 1;
			}
		}
		if ((!$r->parameter_exists("order_total")) || $r->is_empty("order_total") || isset($r_no["order_total"])) {
			$r->add_textbox("order_total", NUMBER);
			$r->change_property("order_total", USE_SQL_NULL, false);			
			if ($r->parameter_exists("goods_total")) {
				$goods_total  = $r->get_value("goods_total");
			} else {
				$goods_total = 0;
			}
			if ($r->parameter_exists("shipping_cost")) {
				$shipping_cost = $r->get_value("shipping_cost");
			} else {
				$shipping_cost = 0;
			}
			$r->set_value("order_total", ($goods_total + $shipping_cost));
			$r_no["order_total"] = 1;
		}
		
		// parse dates, modified in excel
		if (!$r->is_empty("order_placed_date")) {
			$order_placed_date = $r->get_value("order_placed_date");
			$order_placed_date = before_orders_save_check_date($order_placed_date);
			$r->set_value("order_placed_date", $order_placed_date);
		} else {
			$r->set_value("order_placed_date", va_time());
		}
		
		if (!$r->is_empty("modified_date")) {
			$modified_date = $r->get_value("modified_date");
			$modified_date = before_orders_save_check_date($modified_date);
			$r->set_value("modified_date", $modified_date);
		} else {
			$r->set_value("modified_date", va_time());
		}
		
		if (!$r->is_empty("shipping_expecting_date")) {
			$shipping_expecting_date = $r->get_value("shipping_expecting_date");
			$shipping_expecting_date = before_orders_save_check_date($shipping_expecting_date);
			$r->set_value("shipping_expecting_date", $shipping_expecting_date);
		}
	}

	function check_country_state()
	{
		global $r;
		if ($r->parameter_exists("country_id") && $r->parameter_exists("country_code")) {
			if (!$r->is_empty("country_id") || !$r->is_empty("country_code")) {
				$country_id   = $r->get_value("country_id");
				$country_code = $r->get_value("country_code");
				before_orders_save_check_country($country_id, $country_code);
				$r->set_value("country_id", $country_id);
				$r->set_value("country_code", $country_code);			
			}		
		}
			
		if ($r->parameter_exists("state_id") && $r->parameter_exists("state_code")) {
			if (!$r->is_empty("state_id") || !$r->is_empty("state_code")) {
				$state_id = $r->get_value("state_id");
				$state_code = $r->get_value("state_code");
				$country_id = $r->parameter_exists("country_id") ? $r->get_value("country_id") : "";
				before_orders_save_check_state($state_id, $state_code, $country_id);
				$r->set_value("state_id", $state_id);
				$r->set_value("state_code", $state_code);
				$r->set_value("country_id", $country_id);
			}
		}
		
		if ($r->parameter_exists("delivery_country_id") && $r->parameter_exists("delivery_country_code")) {
			if (!$r->is_empty("delivery_country_id") || !$r->is_empty("delivery_country_code")) {
				$delivery_country_id = $r->get_value("delivery_country_id");
				$delivery_country_code = $r->get_value("delivery_country_code");
				before_orders_save_check_country($delivery_country_id, $delivery_country_code);
				$r->set_value("delivery_country_id", $delivery_country_id);
				$r->set_value("delivery_country_code", $delivery_country_code);
			}
		}
		
		if ($r->parameter_exists("delivery_state_id") && $r->parameter_exists("delivery_state_code")) {
			if (!$r->is_empty("delivery_state_id") || !$r->is_empty("delivery_state_code")) {
				$delivery_state_id = $r->get_value("delivery_state_id");
				$delivery_state_code = $r->get_value("delivery_state_code");
				$delivery_country_id = $r->parameter_exists("delivery_country_id") ? $r->get_value("delivery_country_id") : "";
				before_orders_save_check_state($delivery_state_id, $delivery_state_code, $delivery_country_id);
				$r->set_value("delivery_state_id", $delivery_state_id);
				$r->set_value("delivery_state_code", $delivery_state_code);
				$r->set_value("delivery_country_id", $delivery_country_id);
			}
		}
	}
	
	function before_orders_save_check_date($date) {
		if (is_string($date)) {
			$timestamp = strtotime($date);
			if ($timestamp) {
				return date("Y-m-d H:i:s", $timestamp);
			} else {
				return $date;
			}
		} else {
			return $date;
		}
	}
	
	function before_orders_save_check_country(&$country_id, &$country_code) {
		global $table_prefix, $db;
		$country_id = rtrim(trim($country_id));
		$country_code = rtrim(trim($country_code));
		if ($country_id) {
			if (intval($country_id) > 0) {
				$sql  = " SELECT country_id, country_code FROM " .  $table_prefix . "countries ";
				$sql .= " WHERE country_id=" . $db->tosql($country_id, INTEGER, true, false);
				$db->query($sql);
				if ($db->next_record()) {
					$country_code = $db->f("country_code");
				} else {
					$country_code = $country_id;
				}				
			} else {
				$old_id = strtolower($country_id);
				$sql  = " SELECT country_id, country_code FROM " .  $table_prefix . "countries ";
				$sql .= " WHERE LOWER(country_name)=" . $db->tosql($old_id, TEXT);
				$sql .= " OR LOWER(country_code)=" . $db->tosql($old_id, TEXT);
				$db->query($sql);
				if ($db->next_record()) {
					$country_id   = $db->f("country_id");
					$country_code = $db->f("country_code");
				} else {
					$country_code = $country_id;
				}
			}
		} elseif ($country_code) {			
			$old_code = strtolower($country_code);
			$sql  = " SELECT country_id, country_code FROM " .  $table_prefix . "countries ";
			$sql .= " WHERE LOWER(country_name)=" . $db->tosql($old_code, TEXT);
			$sql .= " OR LOWER(country_code)=" . $db->tosql($old_code, TEXT);
			$db->query($sql);
			if ($db->next_record()) {
				$country_id   = $db->f("country_id");
				$country_code = $db->f("country_code");
			} else {
				$country_code = $country_id;
			}
		}
	}
	
	function before_orders_save_check_state(&$state_id, &$state_code, $country_id) {
		global $table_prefix, $db;
		$state_id = rtrim(trim($state_id));
		$state_code = rtrim(trim($state_code));
		if (intval($state_id) > 0) {
			$sql  = " SELECT state_id, state_code, country_id FROM " . $table_prefix . "states ";
			$sql .= " WHERE state_id=" . $db->tosql($state_id, INTEGER);
			if ($country_id) {
				$sql .= " AND country_id=" .  $db->tosql($country_id, INTEGER);
			}
			$db->query($sql);
			if ($db->next_record()) {
				$state_id   = $db->f("state_id");
				$state_code = $db->f("state_code");
				$country_id = $db->f("country_id");
			} else {
				$state_id = get_db_value("SELECT MAX(state_id) FROM " .$table_prefix . "states") + 1;
				$sql  = " INSERT INTO " . $table_prefix . "states ";
				$sql .= " (state_id, country_id, state_code, state_name)";
				$sql .= " VALUES (";
				$sql .= $db->tosql($state_id, INTEGER) . ",";
				if ($country_id) {
					$sql .= $db->tosql($country_id, INTEGER) . ",";
				} else {
					$sql .= "0,";
				}
				if (strlen($state_code)) {
					$sql .= $db->tosql($state_code, TEXT). ",";
					$sql .= $db->tosql($state_code, TEXT). ")";
				} else {
					$state_code = substr($state_id, 0, 2);
					$sql .= $db->tosql($state_code, TEXT). ",";
					$sql .= $db->tosql($state_id, TEXT). ")";
				}
				$db->query($sql);
			}		
		} elseif ($country_id) {
			$old_id = strtolower($state_id);
			$old_code = strtolower($state_code);			
			$sql  = " SELECT state_id, state_code FROM " . $table_prefix . "states ";
			$sql .= " WHERE ( LOWER(state_name)=" . $db->tosql($old_id, TEXT);
			if ($state_code) {
				$sql .= " OR LOWER(state_code)=" .  $db->tosql($old_code, TEXT);			
				$sql .= " OR LOWER(state_code)=" .  $db->tosql($old_code, TEXT);
			}
			$sql .= " OR LOWER(state_code)=" .  $db->tosql($old_id, TEXT) . ") ";
			if ($country_id) {
				$sql .= " AND country_id=" .  $db->tosql($country_id, INTEGER);
			}
			$db->query($sql);
			if ($db->next_record()) {
				$state_id   = $db->f("state_id");
				$state_code = $db->f("state_code");
			} else {				
				$state_id = get_db_value("SELECT MAX(state_id) FROM " .$table_prefix . "states") + 1;				
				$sql  = " INSERT INTO " . $table_prefix . "states ";
				$sql .= " (state_id, country_id, state_code, state_name)";
				$sql .= " VALUES(";
				$sql .= $db->tosql($state_id, INTEGER) . ",";
				if ($country_id) {
					$sql .= $db->tosql($country_id, INTEGER) . ",";
				} else {
					$sql .= "0,";
				}
				if (strlen($state_code)) {
					$sql .= $db->tosql($state_code, TEXT). ",";
					$sql .= $db->tosql($state_code, TEXT). ")";
				} else {
					$state_code = substr($state_id, 0, 2);
					$sql .= $db->tosql($state_code, TEXT). ",";
					$sql .= $db->tosql("UNDEFINED", TEXT). ")";
				}
				$db->query($sql);				
			}
		}
	}

	function before_orders_items_save()
	{
		global $sub_r, $db, $table_prefix;
		$item_id = $sub_r->get_value("item_id");
		$quantity = $sub_r->get_value("quantity");
		
		if (!$item_id) {
			if (!$sub_r->is_empty("item_code")) {
				$item_code = $sub_r->get_value("item_code");
				$sql  = " SELECT item_id FROM " . $table_prefix . "items ";
				$sql .= " WHERE item_code=" . $db->tosql($item_code, TEXT, true, false);
				$item_id = get_db_value($sql);
			}
			if (!$item_id && !$sub_r->is_empty("manufacturer_code")) {
				$manufacturer_code = $sub_r->get_value("manufacturer_code");
				$sql  = " SELECT item_id FROM " . $table_prefix . "items ";
				$sql .= " WHERE manufacturer_code=" . $db->tosql($manufacturer_code, TEXT, true, false);
				$item_id = get_db_value($sql);
			}
			$sub_r->set_value("item_id", $item_id);
		}
	}
	
	function after_orders_items_save()
	{
		global $sub_r, $db, $table_prefix;
		$item_id = $sub_r->get_value("item_id");
		$quantity = $sub_r->get_value("quantity");
		
		if ($quantity && $item_id) {
			$sql  = " SELECT stock_level FROM " . $table_prefix . "items ";
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER, true, false);
			$old_stock = get_db_value($sql);
			
			$sql  = " UPDATE " . $table_prefix . "items SET stock_level=" . $db->tosql($old_stock - $quantity, INTEGER, true, false);
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER, true, false);
			$db->query($sql);
		}
		
		$sub_r->set_value("item_id", 0);
	}
	
	function update_orders_items_properties($order_item_id, $order_id, $related_value) 
	{
		global $db, $table_prefix, $column_name, $column_value;
		if (!$related_value) return false;
		$property_name   = substr($column_name, 20);	
		$property_id         = 0;
		$property_values_ids = array();	
		$additional_price    = 0;
		$additional_weight   = 0;
		
		$sql  = " SELECT property_id, additional_price FROM " . $table_prefix . "items_properties WHERE property_name=" . $db->tosql($property_name, TEXT);
		$db->query($sql);
		if ($db->next_record()) {
			$property_id      = $db->f('property_id');
			$additional_price = $db->f('additional_price');
			$tmp = explode (",", $related_value);
			foreach ($tmp AS $property_value) {
				$property_value = trim($property_value);
				$sql  = " SELECT item_property_id, additional_price, additional_weight FROM " . $table_prefix . "items_properties_values ";
				$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER, true, false). " AND property_value=" . $db->tosql($property_value, TEXT);
				$db->query($sql);
				if ($db->next_record()) {
					$property_values_ids[] = $db->f('item_property_id');
					$additional_price  += $db->f('additional_price');
					$additional_weight += $db->f('additional_weight');				
				}
			}
		}	
		
		$sql  = " INSERT INTO " . $table_prefix . "orders_items_properties ";
		$sql .= " (order_id, order_item_id, property_id, property_values_ids, property_name, property_value, additional_price, additional_weight) ";
		$sql .= " VALUES (" . $db->tosql($order_id, INTEGER) . ", " ;
		$sql .= $db->tosql($order_item_id, INTEGER) . ", " ;
		$sql .= $db->tosql($property_id, INTEGER, true, false) . ", " ;
		$sql .= $db->tosql(implode(',', $property_values_ids), TEXT, true, false) . ", " ;
		$sql .= $db->tosql($property_name, TEXT) . ", " ;
		$sql .= $db->tosql($related_value, TEXT) . ", ";
		$sql .= $db->tosql($additional_price,  FLOAT, true, false) . ", ";
		$sql .= $db->tosql($additional_weight, FLOAT, true, false) . ") ";
		$db->query($sql);
	}

	function after_orders_save($order_id) {
		global $db, $table_prefix;
		$sql  = " DELETE FROM " . $table_prefix . "orders_properties WHERE order_id=" . $db->tosql($order_id, INTEGER);
		$db->query($sql);		
	}
	
	function update_orders_properties($item_id, $related_value) 
	{
		global $db, $table_prefix, $column_name, $column_value;
		$property_id = substr($column_name, 15);
		
		$property_order  = 0;
		$property_type   = 1;
		$property_name   = $column_value;
		$property_price  = 0;
		$property_weight = 0;
		$property_tax_free = 0;
		$property_value_id = 0;
		$tmp = explode (";", $related_value);
		foreach ($tmp AS $property_value) {
			$property_value = trim($property_value);
			$sql  = " SELECT p.property_order,p.property_type,p.property_name,p.tax_free, ";
			$sql .= " pv.property_price, pv.property_weight, pv.property_value_id ";
			$sql .= " FROM ( " . $table_prefix . "order_custom_properties p ";
			$sql .= " LEFT JOIN " . $table_prefix . "order_custom_values pv ON pv.property_id=p.property_id) ";
			$sql .= " WHERE p.property_id=" . $db->tosql($property_id, INTEGER);
			$sql .= " AND ( pv.property_value=" . $db->tosql($property_value , TEXT);
			$sql .= " OR  pv.property_value_id=" . $db->tosql($property_value , TEXT) . ")";
			$db->query($sql);
			if ($db->next_record()) {
				$property_order    = $db->f('property_order');
				$property_type     = $db->f('property_type');
				$property_name     = $db->f('property_name');
				$property_price    += $db->f('property_price');
				$property_weight   += $db->f('property_weight');
				$property_tax_free = $db->f('tax_free');
				$property_value_id = $db->f('property_value_id');
			}
		}
		if ($property_value_id) {
			$related_value = $property_value_id;
		}
		$sql  = " INSERT INTO " . $table_prefix . "orders_properties ";
		$sql .= " (order_id, property_id, property_order, property_type, property_name, property_value,property_price,property_weight,tax_free) ";
		$sql .= " VALUES (" . $db->tosql($item_id, INTEGER) . ", " ;
		$sql .= $db->tosql($property_id, INTEGER) . ", " ;
		$sql .= $db->tosql($property_order, INTEGER, true, false) . ", " ;
		$sql .= $db->tosql($property_type, INTEGER, true, false) . ", " ;
		$sql .= $db->tosql($property_name, TEXT) . ", " ;
		$sql .= $db->tosql($related_value, TEXT) . ", ";
		$sql .= $db->tosql($property_price,  FLOAT, true, false) . ", ";
		$sql .= $db->tosql($property_weight, FLOAT, true, false) . ", " ;
		$sql .= $db->tosql($property_tax_free , INTEGER, true, false) . ") ";
		$db->query($sql);
	}
	
	
	function update_items_categories($item_id, $categories_info)
	{
		global $db, $table_prefix;
	
		$categories_info = trim($categories_info);
		$sql = " DELETE FROM " . $table_prefix . "items_categories WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
	
		$categories_list = explode(";", $categories_info);
		for ($i = 0; $i < sizeof($categories_list); $i++) {
			$category_info = $categories_list[$i];
			$categories_names = explode(">", $category_info);
	
			$last_category_id = 0; $category_path = "";
			for ($ci = 0; $ci < sizeof($categories_names); $ci++) {
				$category_name = trim($categories_names[$ci]);
				if (strval($category_name) == "0") {
					$category_path = "0,";
				} elseif (strlen($category_name)) {
					$category_path .= $last_category_id . ",";
					$sql  = " SELECT category_id FROM " . $table_prefix . "categories ";
					$sql .= " WHERE parent_category_id=" . $db->tosql($last_category_id, INTEGER);
					$sql .= " AND category_name=" . $db->tosql($category_name, TEXT);
					$db->query($sql);
					if ($db->next_record()) {
						$last_category_id = $db->f("category_id");
					} else {
						$parent_category_id = $last_category_id;
						$sql  = " SELECT MAX(category_id) FROM " . $table_prefix . "categories ";
						$last_category_id = get_db_value($sql) + 1;
	
						$sql  = " SELECT MAX(category_order) FROM " . $table_prefix . "categories ";
						$sql .= " WHERE parent_category_id=" . $db->tosql($parent_category_id, INTEGER);
						$category_order = get_db_value($sql) + 1;
	
						$sql  = " INSERT INTO " . $table_prefix . "categories ";
						$sql .= " (category_id, parent_category_id, category_path, category_name, category_order, is_showing) VALUES (";
						$sql .= $db->tosql($last_category_id, INTEGER) . ", ";
						$sql .= $db->tosql($parent_category_id, INTEGER) . ", ";
						$sql .= $db->tosql($category_path, TEXT) . ", ";
						$sql .= $db->tosql($category_name, TEXT) . ", ";
						$sql .= $db->tosql($category_order, INTEGER) . ", 1) ";
						$db->query($sql);
					}
				}
			}
	
			if (strlen($category_path)) {
				$sql  = " INSERT INTO " . $table_prefix . "items_categories (item_id, category_id) ";
				$sql .= " VALUES (" . $db->tosql($item_id, INTEGER) . ", " . $db->tosql($last_category_id, INTEGER) . ") ";
				$db->query($sql);
			}
		}
	}
	
	function update_items_properties($item_id, $properties_info)
	{
		global $db, $table_prefix, $column_value, $property_order, $max_property_id;
	
		$properties_info = trim($properties_info);
	
		$property_id = "";
		$sql  = " SELECT property_id FROM " . $table_prefix . "items_properties ";
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$sql .= " AND property_name=" . $db->tosql($column_value, TEXT);
		$db->query($sql);
		if ($db->next_record()) {
			$property_id = $db->f("property_id");
			$sql = " DELETE FROM " . $table_prefix . "items_properties_values WHERE property_id=" . $db->tosql($property_id, INTEGER);
			$db->query($sql);
		}
	
		if (strlen($properties_info)) {
			if (strpos($properties_info, ";") === false) {
				$control_type = "LABEL";
				$property_description = $properties_info;
			} else {
				$control_type = "LISTBOX";
				$property_description = "";
			}
			if (strlen($property_id)) {
				$sql  = " UPDATE " . $table_prefix . "items_properties SET ";
				$sql .= " property_description=" . $db->tosql($property_description, TEXT) . ", ";
				$sql .= " control_type=" . $db->tosql($control_type, TEXT);
				$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER);
				$db->query($sql);
			} else {
				$property_order++;
				$max_property_id++;
				$property_id = $max_property_id;
				$item_type_id = 0;
	
				$sql  = " INSERT INTO " . $table_prefix . "items_properties ";
				$sql .= " (property_id, item_id, item_type_id, property_order, property_name, property_description, control_type, required, use_on_list, use_on_details, use_on_checkout) VALUES (";
				$sql .= $db->tosql($property_id, INTEGER) . ", ";
				$sql .= $db->tosql($item_id, INTEGER) . ", ";
				$sql .= $db->tosql($item_type_id, INTEGER) . ", ";
				$sql .= $db->tosql($property_order, INTEGER) . ", ";
				$sql .= $db->tosql($column_value, TEXT) . ", ";
				$sql .= $db->tosql($property_description, TEXT) . ", ";
				$sql .= $db->tosql($control_type, TEXT) . ", ";
				$sql .= "0, 1, 1, 0) ";
				$db->query($sql);
			}

			if ($control_type == "LISTBOX") {
				$property_values = explode(";", $properties_info);
				for ($pv = 0; $pv < sizeof($property_values); $pv++) {
					$property_value = trim($property_values[$pv]);
					$additional_price = "";
					if (preg_match("/^(.+)=\s*([\-\+]?[\d]*\.?[\d]*)$/", $property_value, $matches)) {
						$property_value = $matches[1];
						$additional_price = $matches[2];
					}
					if (strlen($property_value)) {
						$sql  = " INSERT INTO " . $table_prefix . "items_properties_values ";
						$sql .= " (property_id, property_value, additional_price, hide_out_of_stock, hide_value) VALUES (";
						$sql .= $db->tosql($property_id, INTEGER) . ", ";
						$sql .= $db->tosql($property_value, TEXT) . ", ";
						$sql .= $db->tosql($additional_price, NUMBER) . ", ";
						$sql .= "0, 0) ";
						$db->query($sql);
					}
				}
			}
		} else {
			$sql = " DELETE FROM " . $table_prefix . "items_properties WHERE property_id=" . $db->tosql($property_id, INTEGER);
			$db->query($sql);
		}
	}
	
	function update_items_features($item_id, $feature_value)
	{
		global $db, $db_type, $features_groups, $table_prefix, $column_value;
	
		$feature_value = trim($feature_value);
		if (preg_match("/^(.+)>(.+)$/isU", $column_value, $matches)) {
			$group_name = trim($matches[1]);
			$feature_name = trim($matches[2]);
			if (isset($features_groups[$group_name])) {
				$group_id = $features_groups[$group_name];
			} else {
				$sql  = " SELECT group_id FROM " . $table_prefix . "features_groups ";
				$sql .= " WHERE group_name=" . $db->tosql($group_name, TEXT);
				$db->query($sql);
				if ($db->next_record()) {
					$group_id = $db->f("group_id");
				} else {
					// feature group doesn't exists - add new
					$sql = " SELECT MAX(group_order) FROM " . $table_prefix . "features_groups ";
					$group_order = get_db_value($sql);
					$group_order++;
	
					if ($db_type == "postgre") {
						$group_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "features_groups') ");
					}
	
					$sql  = " INSERT INTO " . $table_prefix . "features_groups (";
					if ($db_type == "postgre") { $sql .= " group_id, "; }
					$sql .= " group_order, group_name) VALUES (";
					if ($db_type == "postgre") { $sql .= $db->tosql($group_id, INTEGER) . ", "; }
					$sql .= $db->tosql($group_order, INTEGER) . ", ";
					$sql .= $db->tosql($group_name, TEXT) . ") ";
					$db->query($sql);
	
					if ($db_type == "mysql") {
						$group_id = get_db_value(" SELECT LAST_INSERT_ID() ");
					} elseif ($db_type == "access") {
						$group_id = get_db_value(" SELECT @@IDENTITY ");
					} elseif ($db_type == "db2") {
						$group_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "features_groups FROM " . $table_prefix . "features_groups");
					}
				}
				$features_groups[$group_name] = $group_id;
			}
	
			$feature_id = "";
			$sql  = " SELECT feature_id FROM " . $table_prefix . "features ";
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
			$sql .= " AND group_id=" . $db->tosql($group_id, INTEGER);
			$sql .= " AND feature_name=" . $db->tosql($feature_name, TEXT);
			$db->query($sql);
			if ($db->next_record()) {
				$feature_id = $db->f("feature_id");
			}
			if (strlen($feature_value)) {
				if (strlen($feature_id)) {
					$sql  = " UPDATE " . $table_prefix . "features SET ";
					$sql .= " feature_value=" . $db->tosql($feature_value, TEXT);
					$sql .= " WHERE feature_id=" . $db->tosql($feature_id, INTEGER);
					$db->query($sql);
				} else {
					$sql  = " INSERT INTO " . $table_prefix . "features ";
					$sql .= " (item_id, group_id, feature_name, feature_value) VALUES (";
					$sql .= $db->tosql($item_id, INTEGER) . ", ";
					$sql .= $db->tosql($group_id, INTEGER) . ", ";
					$sql .= $db->tosql($feature_name, TEXT) . ", ";
					$sql .= $db->tosql($feature_value, TEXT) . ") ";
					$db->query($sql);
				}
			} else {
				$sql = " DELETE FROM " . $table_prefix . "features WHERE feature_id=" . $db->tosql($feature_id, INTEGER);
				$db->query($sql);
			}
		}
	}
	
	function update_manufacturer($item_id, $manufacturer_name)
	{
		global $db, $table_prefix;
	
		$manufacturer_id = "";
		if (strlen($manufacturer_name)) {
			$sql  = " SELECT manufacturer_id FROM " . $table_prefix . "manufacturers ";
			$sql .= " WHERE manufacturer_name=" . $db->tosql($manufacturer_name, TEXT);
			$db->query($sql);
			if ($db->next_record()) {
				$manufacturer_id = $db->f("manufacturer_id");
			} else {
				$sql  = " SELECT MAX(manufacturer_id) FROM " . $table_prefix . "manufacturers ";
				$manufacturer_id = get_db_value($sql) + 1;
	
				$sql  = " INSERT INTO " . $table_prefix . "manufacturers ";
				$sql .= " (manufacturer_id, manufacturer_name) VALUES (";
				$sql .= $db->tosql($manufacturer_id, INTEGER) . ", ";
				$sql .= $db->tosql($manufacturer_name, TEXT) . ") ";
				$db->query($sql);
			}
		}
	
		$sql  = " UPDATE " . $table_prefix . "items SET manufacturer_id=" . $db->tosql($manufacturer_id, INTEGER);
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
	}
	
	function prepare_categories_list()
	{
		global $db, $table_prefix, $categories;
	
		//-- parent items
		$sql  = " SELECT category_id, parent_category_id FROM " . $table_prefix . "categories ";
		$db->query($sql);
		while ($db->next_record()) {
			$list_id = $db->f("category_id");
			$list_parent_id = $db->f("parent_category_id");
			$categories[$list_parent_id]["subs"][] = $list_id;
		}
	}
	
	function update_categories_tree($parent_category_id, $category_path)
	{
		global $db, $table_prefix, $categories;
	
		if (isset($categories[$parent_category_id]["subs"])) {
			$category_path .= $parent_category_id . ",";
	
			$subs = $categories[$parent_category_id]["subs"];
			for ($s = 0; $s < sizeof($subs); $s++) {
				$sub_id = $subs[$s];
	
				$sql  = " UPDATE " . $table_prefix . "categories SET ";
				$sql .= " category_path=" . $db->tosql($category_path, TEXT);
				$sql .= " WHERE category_id=" . $db->tosql($sub_id, INTEGER);
				$db->query($sql);
	
				if (isset($categories[$sub_id]["subs"])) {
					update_categories_tree($sub_id, $category_path);
				}
			}
		}	
	}

	function import_friendly_url()
	{
		global $r;
		if ($r->parameter_exists("friendly_url")) {
			$friendly_url = trim($r->get_value("friendly_url")); // trim friendly url value
			$r->set_value("friendly_url", $friendly_url); // set trimed value
			if (strlen($friendly_url)) {
				$is_unique = validate_friendly_url("", false); // check if existed value is unique
				if (!$is_unique) {
					$r->set_value("friendly_url", ""); // clear duplicated friendly value
				}
			}
			set_friendly_url(); // generate new friendly value from title
		}
	}

	function add_imported_fields()
	{
		global $r, $table;

		if ($table == "items") {
			// administrative information
			$r->add_textbox("admin_id_added_by", INTEGER);
			$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
			$r->add_textbox("admin_id_modified_by", INTEGER);
			$r->add_textbox("date_added", DATETIME);
			$r->change_property("date_added", USE_IN_UPDATE, false);
			$r->add_textbox("date_modified", DATETIME);
			$r->set_value("admin_id_added_by", get_session("session_admin_id"));
			$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
			$r->set_value("date_added", va_time());
			$r->set_value("date_modified", va_time());
		} elseif ($table == "orders") {
			$r->add_textbox("order_placed_date", DATETIME);
			$r->change_property("order_placed_date", USE_SQL_NULL, false);
			$r->set_value("order_placed_date", va_time());
			$r->add_textbox("modified_date", DATETIME);
			$r->change_property("modified_date", USE_SQL_NULL, false);
			$r->set_value("modified_date", va_time());
			$r->add_textbox("affiliate_code", TEXT);
			$r->change_property("affiliate_code", USE_SQL_NULL, false);
			$r->add_textbox("first_name", TEXT);
			$r->change_property("first_name", USE_SQL_NULL, false);
			$r->add_textbox("last_name", TEXT);
			$r->change_property("last_name", USE_SQL_NULL, false);
			$r->add_textbox("email", TEXT);
			$r->change_property("email", USE_SQL_NULL, false);
		}

		if ($table == "orders" || $table == "users") {
			$r->add_textbox("state_id", INTEGER);
			$r->change_property("state_id", USE_SQL_NULL, false);
			$r->add_textbox("country_id", INTEGER);
			$r->change_property("country_id", USE_SQL_NULL, false);
			$r->add_textbox("delivery_state_id", INTEGER);
			$r->change_property("delivery_state_id", USE_SQL_NULL, false);
			$r->add_textbox("delivery_country_id", INTEGER);
			$r->change_property("delivery_country_id", USE_SQL_NULL, false);	
		}
	}
?>