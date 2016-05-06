<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_export_custom.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/".$language_code."/download_messages.php");
	include_once($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("import_export");

	$errors = "";
	$sql_where = "";
	$table = get_param("table");
	$operation = get_param("operation");
	$category_id = get_param("category_id");
	$id = get_param("id");
	$ids = get_param("ids");
	$s_on = get_param("s_on");
	$s_ne = get_param("s_ne");
	$s_kw = get_param("s_kw");
	$s_sd = get_param("s_sd");
	$s_ed = get_param("s_ed");
	$s_os = get_param("s_os");
	$s_cc = get_param("s_cc");
	$s_sc = get_param("s_sc");
	$s_ex = get_param("s_ex");
	$field = get_param("field");
	$field_title = get_param("field_title");
	$field_value = get_param("field_value");

	if ($table == "orders_items") {
		$main_table = "orders";
	} else {
		$main_table = $table;
	}
	$admin_export_url = new VA_URL("admin_export.php", false);
	$admin_export_url->add_parameter("table", CONSTANT, $main_table);
	$admin_export_url->add_parameter("category_id", REQUEST, "category_id");
	$admin_export_url->add_parameter("id", REQUEST, "id");
	$admin_export_url->add_parameter("ids", REQUEST, "ids");
	$admin_export_url->add_parameter("s_on", REQUEST, "s_on");
	$admin_export_url->add_parameter("s_ne", REQUEST, "s_ne");
	$admin_export_url->add_parameter("s_kw", REQUEST, "s_kw");
	$admin_export_url->add_parameter("s_sd", REQUEST, "s_sd");
	$admin_export_url->add_parameter("s_ed", REQUEST, "s_ed");
	$admin_export_url->add_parameter("s_os", REQUEST, "s_os");
	$admin_export_url->add_parameter("s_cc", REQUEST, "s_cc");
	$admin_export_url->add_parameter("s_sc", REQUEST, "s_sc");
	$admin_export_url->add_parameter("s_ex", REQUEST, "s_ex");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_export_custom.html");

	$t->set_var("admin_export_href",        "admin_export.php");
	$t->set_var("admin_export_url",         $admin_export_url->get_url());
	$t->set_var("admin_export_custom_href", "admin_export_custom.php");
	$t->set_var("admin_export_custom_help_href", "admin_export_custom_help.php");
	$t->set_var("admin_items_list_href",    "admin_items_list.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CUSTOM_FIELDS_MSG, CONFIRM_DELETE_MSG));

	if ($table == "items") {
		check_admin_security("products_categories");
		include_once("./admin_table_items.php");
	} else if ($table == "categories") {
		check_admin_security("products_categories");
		include_once("./admin_table_categories.php");
	} else if ($table == "users") {
		check_admin_security("site_users");
		include_once("./admin_table_users.php");
	} else if ($table == "newsletters_users") {
		check_admin_security("site_users");
		include_once("./admin_table_emails.php");
	} else if ($table == "orders" || $table == "orders_items") {
		check_admin_security("sales_orders");
		include_once("./admin_table_orders.php");
	} else {
		$table_name = "";
		$table_title = "";
		$errors = CANT_FIND_TABLE_IMPORT_MSG;
	}

	$t->set_var("table", $table);
	$t->set_var("table_title", $table_title);


	if ($operation == "save")	{
	  if (!strlen($field_title)) {
			$error_message = str_replace("{field_name}", FIELD_TITLE_MSG, REQUIRED_MESSAGE);
			$errors .= $error_message . "<br>";
		} else {
			$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type=" . $db->tosql($table, TEXT);
			$sql .= " AND setting_name=" . $db->tosql($field_title, TEXT);
			if (strlen($field)) {
				$sql .= " AND setting_name<>" . $db->tosql($field, TEXT);
			}
			$db->query($sql);
			if ($db->next_record() || isset($db_columns[$field_title]) || 
				(isset($related_columns) && isset($related_columns[$field_title]))) {
				$error_message = str_replace("{field_name}", FIELD_TITLE_MSG, UNIQUE_MESSAGE);
				$errors .= $error_message . "<br>";
			}
		}
	  if(!strlen($field_value)) { // possible to check field value if necessary
			//$error_message = str_replace("{field_name}", "Field Value", REQUIRED_MESSAGE);
			//$errors .= $error_message . "<br>";
		}
		
		if(!strlen($errors)) {
			if (strlen($field)) {
				$sql  = " UPDATE " . $table_prefix . "global_settings SET ";
				$sql .= " setting_name=" . $db->tosql($field_title, TEXT) . ", ";
				$sql .= " setting_value=" . $db->tosql($field_value, TEXT);
				$sql .= " WHERE setting_type=" . $db->tosql($table, TEXT);
				$sql .= " AND setting_name=" . $db->tosql($field, TEXT);
			} else {
				$sql  = " INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES (";
				$sql .= $db->tosql($table, TEXT) . ", ";
				$sql .= $db->tosql($field_title, TEXT) . ", ";
				$sql .= $db->tosql($field_value, TEXT) . ") ";
			}
			$db->query($sql);

			header("Location: " . $admin_export_url->get_url());
			exit;
		}
	} else if ($operation == "delete") {
		$sql  = " DELETE FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type=" . $db->tosql($table, TEXT);
		$sql .= " AND setting_name=" . $db->tosql($field, TEXT);
		$db->query($sql);

		header("Location: " . $admin_export_url->get_url());
		exit;
	} else if ($operation == "cancel") {
		header("Location: " . $admin_export_url->get_url());
		exit;
	} else if (strlen($field)) {
		$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type=" . $db->tosql($table, TEXT);
		$sql .= " AND setting_name=" . $db->tosql($field, TEXT);
		$db->query($sql);
		if ($db->next_record()) {
			$field_title = $field;
			$field_value = $db->f("setting_value");
		} else {
			$field = "";
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
	$t->set_var("s_cc", htmlspecialchars($s_cc));
	$t->set_var("s_sc", htmlspecialchars($s_sc));
	$t->set_var("s_ex", htmlspecialchars($s_ex));
	$t->set_var("field", htmlspecialchars($field));
	$t->set_var("field_title", htmlspecialchars($field_title));
	$t->set_var("field_value", htmlspecialchars($field_value));

	if (strlen($field)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);
	} else {
		$t->set_var("save_button", ADD_BUTTON);
	}

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($category_id);

	if ($table_name == ($table_prefix . "items") || $table_name == ($table_prefix . "categories")) {
		$t->parse("products_path", false);
	} else {
		$t->set_var("products_path", "");
	}


	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");


?>