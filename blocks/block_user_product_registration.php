<?php
	
	check_user_security("access_product_registration");
		
	$operation = get_param("operation");	
	$user_id   = get_session("session_user_id");
	$user_settings = array();
	$sql  = " SELECT setting_value FROM " . $table_prefix . "user_types_settings ";
	$sql .= " WHERE type_id=" . $db->tosql(get_session("session_user_type_id"), INTEGER);
	$sql .= " AND setting_name=" . $db->tosql("approve_product_registration", TEXT);
	$approve_product_registration = get_db_value($sql);
	
	$registration_settings = array();
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type=" . $db->tosql("registration", TEXT);
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while($db->next_record()) {
		$registration_settings[$db->f("setting_name")] = $db->f("setting_value");
	}
	
	$category_id_required = get_setting_value($registration_settings, "category_id_required");
	
	// prepare custom options
	$pp = array(); $pn = 0;
	$sql  = " SELECT * ";
	$sql .= " FROM " . $table_prefix . "registration_custom_properties ";
	if ($user_id) {
		$sql .= " WHERE property_show IN (1,3) ";
	} else {
		$sql .= " WHERE property_show IN (1,2) ";
	}
	if (isset($site_id)) {
		$sql .= " AND site_id=" . $db->tosql($site_id, INTEGER, true, false);
	} else {
		$sql .= " AND site_id=1 ";
	}
	$sql .= " ORDER BY property_order, property_id ";
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$pp[$pn]["property_id"] = $db->f("property_id");
			$pp[$pn]["property_order"] = $db->f("property_order");
			$pp[$pn]["property_name"] = $db->f("property_name");
			$pp[$pn]["property_description"] = $db->f("property_description");
			$pp[$pn]["default_value"] = $db->f("default_value");
			$pp[$pn]["property_style"] = $db->f("property_style");
			$pp[$pn]["section_id"] = $db->f("section_id");
			$pp[$pn]["control_type"] = $db->f("control_type");
			$pp[$pn]["control_style"] = $db->f("control_style");
			$pp[$pn]["control_code"] = $db->f("control_code");
			$pp[$pn]["onchange_code"] = $db->f("onchange_code");
			$pp[$pn]["onclick_code"] = $db->f("onclick_code");
			$pp[$pn]["required"] = $db->f("required");
			$pp[$pn]["before_name_html"] = $db->f("before_name_html");
			$pp[$pn]["after_name_html"] = $db->f("after_name_html");
			$pp[$pn]["before_control_html"] = $db->f("before_control_html");
			$pp[$pn]["after_control_html"] = $db->f("after_control_html");
			$pp[$pn]["validation_regexp"] = $db->f("validation_regexp");
			$pp[$pn]["regexp_error"] = $db->f("regexp_error");
			$pp[$pn]["options_values_sql"] = $db->f("options_values_sql");

			$pn++;
		} while ($db->next_record());
	}
	
	// prepare dates
	$purchased_months = $short_months;
	array_unshift($purchased_months, array("", MONTH_MSG));
	$start_year = date("Y");
	$purchased_years = array(array("", YEAR_MSG));
	for ($i = 0; $i<10; $i++, $start_year--) {
		$purchased_years[] = array($start_year, $start_year);
	}
	
	
	$t->set_file("block_body","block_user_product_registration.html");
	$t->set_var("user_product_registration_href","user_product_registration.php");
	
	// prepare and parse categories and products
	$category_id      = 0;
	$category_number  = 0;
	$next_category_id = 0;
	
	$t->parse("registration_categories_title");
	do {
		
		$categories = array();
		$category_id = $next_category_id;
		if (!get_setting_value($registration_settings, "show_empty_categories")) { 
			$sql  = " SELECT c.category_id,  COUNT(itc.item_id) AS items_count ";
			$sql .= " FROM ((" . $table_prefix . "registration_categories c ";
			$sql .= " LEFT JOIN " . $table_prefix . "registration_items_assigned itc ON c.category_id = itc.category_id) ";
			$sql .= " LEFT JOIN " . $table_prefix . "registration_items it ON it.item_id = itc.item_id) ";
			$sql .= " WHERE c.parent_category_id=" . $db->tosql($category_id, INTEGER, true, false);
			$sql .= " AND c.show_for_user=1";
			$sql .= " AND (it.show_for_user=1 OR it.show_for_user IS NULL)";
			$sql .= " GROUP BY c.category_id ";
			$db->query($sql);
			$cids = array();
			$cids_to_check = array();
			while($db->next_record()) {
				$items_count = $db->f("items_count");
				$cid = $db->f("category_id");
				if ($items_count) {
					$cids[] = $cid;
				} else {
					$cids_to_check[] = $cid;
				}				
			}
			
			$ic = count($cids_to_check);
			if ($ic) {
				for($i=0; $i<$ic; $i++) {
					$sql  = " SELECT c.category_id,  COUNT(itc.item_id) AS items_count ";
					$sql .= " FROM ((" . $table_prefix . "registration_categories c ";
					$sql .= " INNER JOIN " . $table_prefix . "registration_items_assigned itc ON c.category_id = itc.category_id) ";
					$sql .= " INNER JOIN " . $table_prefix . "registration_items it ON it.item_id = itc.item_id) ";
					$sql .= " WHERE c.category_path LIKE '%," . $db->tosql($cids_to_check[$i], INTEGER, true, false) . ",%' ";
					$sql .= " AND c.show_for_user=1";
					$sql .= " AND it.show_for_user=1";
					$sql .= " GROUP BY c.category_id ";
					$db->query($sql);
					while($db->next_record()) {
						$items_count = $db->f("items_count");
						$category_id = $db->f("category_id");
						if ($items_count) {
							$cids[] = $cids_to_check[$i];
						}
					}
				}
			}
			if (count($cids)) { 
				$sql  = " SELECT c.category_id, c.category_name ";
				$sql .= " FROM " . $table_prefix . "registration_categories c ";
				$sql .= " WHERE c.category_id IN (" . $db->tosql($cids, INTEGERS_LIST) . ")";
				$sql .= " ORDER BY c.category_order, c.category_id ";
				if ($category_number > 0) {
					$categories = get_db_values($sql, array(array("", SELECT_CATEGORY_MSG)));
				} elseif ($category_id_required) {
					$categories = get_db_values($sql, array());
				} else {
					$categories = get_db_values($sql, array(array("", TOP_CATEGORY_MSG)));
				}
			}
		} else {
			$sql  = " SELECT c.category_id, c.category_name ";
			$sql .= " FROM " . $table_prefix . "registration_categories c ";
			$sql .= " WHERE c.parent_category_id=" . $db->tosql($category_id, INTEGER, true, false);
			$sql .= " AND c.show_for_user=1";
			$sql .= " ORDER BY c.category_order, c.category_id ";
			if ($category_number > 0) {
				$categories = get_db_values($sql, array(array("", SELECT_CATEGORY_MSG)));
			} elseif ($category_id_required) {
				$categories = get_db_values($sql, array());
			} else {
				$categories = get_db_values($sql, array(array("", TOP_CATEGORY_MSG)));
			}
		}
		if (count($categories) > 1) {			
			$category_number++;
			$next_category_id = get_param("category_id_" . $category_number);
			set_options($categories, $next_category_id, "category_id");
			$t->set_var("category_number", $category_number);
			$t->parse("registration_category_block");
			$t->set_var("registration_categories_title", "");
		} else {
			break;
		}
	} while ($next_category_id);
	if ($category_number){
		$t->parse("registration_categories_block");
	}
	
	$sql  = " SELECT it.item_id, it.item_name FROM (" . $table_prefix . "registration_items_assigned itc ";
	$sql .= " LEFT JOIN " . $table_prefix . "registration_items it ON it.item_id = itc.item_id) ";
	$sql .= " WHERE itc.category_id=" . $db->tosql($category_id, INTEGER, true, false);
	$sql .= " AND it.show_for_user=1";
	$sql .= " ORDER BY it.item_order, it.item_id ";
	$products = get_db_values($sql, array());	
	
	$r = new VA_Record($table_prefix . "registration_list");
	if ($operation == "get_categories") {
		$r->redirect = false;
	}
	if(get_param("add_more")) {
		$r->return_page = "user_product_registration.php";
	} else {
		$r->return_page = "user_product_registrations.php";
	}
	$r->add_where("registration_id", INTEGER);
	$r->change_property("registration_id", USE_IN_INSERT, true);	
	$r->add_textbox("user_id", INTEGER);
	$r->add_textbox("date_added", DATETIME);
	$r->add_textbox("date_modified", DATETIME);
	$r->add_textbox("is_approved", INTEGER);
	$r->add_textbox("category_id", INTEGER, CATEGORY_MSG);
	if ($category_id_required) {
		$r->parameters["category_id"][REQUIRED] = true;	
	}
	if (get_setting_value($registration_settings, "show_item_id")) {
		if (!get_setting_value($registration_settings, "item_id_required")) {
			array_unshift($products, array("", SELECT_PRODUCT_MSG));
			$r->add_select("item_id", INTEGER, $products, PROD_NAME_MSG);
		} elseif ($products) {
			$r->add_select("item_id", INTEGER, $products, PROD_NAME_MSG);
			$r->parameters["item_id"][REQUIRED] = true;
		} else {
			$r->errors = NO_PRODUCTS_IN_CATEGORY_MSG . "<br/>";
		}
	}
	if (get_setting_value($registration_settings, "show_item_name")) {
		$r->add_textbox("item_name", TEXT, PROD_NAME_MSG);
		if (get_setting_value($registration_settings, "item_name_required")) {
			$r->parameters["item_name"][REQUIRED] = true;
		}
	}
	if (get_setting_value($registration_settings, "show_item_code")) {
		$r->add_textbox("item_code", TEXT, PROD_CODE_MSG);
		if (get_setting_value($registration_settings, "item_code_required")) {
			$r->parameters["item_code"][REQUIRED] = true;
		}
	}
	if (get_setting_value($registration_settings, "show_serial_number")) {
		$r->add_textbox("serial_number", TEXT, SERIAL_NUMBER_MSG);
		if (get_setting_value($registration_settings, "serial_number_required")) {
			$r->parameters["serial_number"][REQUIRED] = true;
		}
	}
	if (get_setting_value($registration_settings, "show_invoice_number")) {
		$r->add_textbox("invoice_number", TEXT, INVOICE_NUMBER_MSG);
		if (get_setting_value($registration_settings, "invoice_number_required")) {
			$r->parameters["invoice_number"][REQUIRED] = true;
		}
	}
	if (get_setting_value($registration_settings, "show_store_name")) {
		$r->add_textbox("store_name", TEXT, STORE_NAME_MSG);
		if (get_setting_value($registration_settings, "store_name_required")) {
			$r->parameters["store_name"][REQUIRED] = true;
		}
	}
	
	$show_purchase_date = false;
	$purchase_date_required = false;
	if (get_setting_value($registration_settings, "show_purchased_day")) {
		$r->add_textbox("purchased_day", INTEGER, DAY_OF_PURCHASE_MSG);
		if (get_setting_value($registration_settings, "purchased_day_required")) {
			$r->parameters["purchased_day"][REQUIRED] = true;
			$purchase_date_required = true;
		}
		$show_purchase_date = true;
	}
	if (get_setting_value($registration_settings, "show_purchased_month")) {
		$r->add_select("purchased_month", INTEGER, $purchased_months, MONTH_OF_PURCHASE_MSG);
		if (get_setting_value($registration_settings, "purchased_month_required")) {
			$r->parameters["purchased_month"][REQUIRED] = true;
			$purchase_date_required = true;
		}
		$show_purchase_date = true;
	}
	if (get_setting_value($registration_settings, "show_purchased_year")) {
		$r->add_select("purchased_year", INTEGER, $purchased_years, YEAR_OF_PURCHASE_MSG);
		if (get_setting_value($registration_settings, "purchased_year_required")) {
			$r->parameters["purchased_year"][REQUIRED] = true;
			$purchase_date_required = true;
		}
		$show_purchase_date = true;
	}
	
	
	// custom properties
	foreach ($pp as $id => $pp_row) {
		$control_type = $pp_row["control_type"];
		$param_name = "pp_" . $pp_row["property_id"];
		$param_title = $pp_row["property_name"];

		if ($control_type == "CHECKBOXLIST") {
			$r->add_checkboxlist($param_name, TEXT, "", $param_title);
		} elseif ($control_type == "RADIOBUTTON") {
			$r->add_radio($param_name, TEXT, "", $param_title);
		} elseif ($control_type == "LISTBOX") {
			$r->add_select($param_name, TEXT, "", $param_title);
		} else {
			$r->add_textbox($param_name, TEXT, $param_title);
		}
		if ($control_type == "CHECKBOXLIST" || $control_type == "RADIOBUTTON" || $control_type == "LISTBOX") {
			if ($pp_row["options_values_sql"]) {
				$sql = $pp_row["options_values_sql"];
			} else {
				$sql  = " SELECT property_value_id, property_value FROM " . $table_prefix . "registration_custom_values ";
				$sql .= " WHERE property_id=" . $db->tosql($pp_row["property_id"], INTEGER) . " AND hide_value=0";
				$sql .= " ORDER BY property_value_id ";
			}
			$r->change_property($param_name, VALUES_LIST, get_db_values($sql, ""));
		}
		if ($pp_row["required"] == 1) {
			$r->change_property($param_name, REQUIRED, true);
		}
		if ($pp_row["validation_regexp"]) {
			$r->change_property($param_name, REGEXP_MASK, $pp_row["validation_regexp"]);
			if ($pp_row["regexp_error"]) {
				$r->change_property($param_name, REGEXP_ERROR, $pp_row["regexp_error"]);
			}
		}
		$r->change_property($param_name, USE_IN_SELECT, false);
		$r->change_property($param_name, USE_IN_INSERT, false);
		$r->change_property($param_name, USE_IN_UPDATE, false);
		$r->change_property($param_name, SHOW, false);
	}
	
	$r->operations[INSERT_ALLOWED] = true;
	$r->operations[UPDATE_ALLOWED] = false;
	$r->operations[DELETE_ALLOWED] = false;
	$r->set_event(AFTER_REQUEST, "after_request");
	$r->set_event(AFTER_SELECT,  "after_request");	
	$r->set_event(BEFORE_SHOW,   "show_custom_properties", $pp);
	$r->set_event(AFTER_INSERT, "after_save", $pp);
	$r->set_event(AFTER_UPDATE, "after_save", $pp);
	
	$r->process();	
	
	if ($show_purchase_date) {
		if ($purchase_date_required) {
			$t->set_var("purchase_date_required", "*");
		}
		$t->parse("purchase_date_block");
	}
		
	$intro_text = get_setting_value($registration_settings, "intro_text");
	if ($intro_text) {
		$t->set_var("intro_text", get_translation($intro_text));
		$t->parse("intro_text_block");
	}
	
	$final_text = get_setting_value($registration_settings, "final_text");
	if ($final_text) {
		$t->set_var("final_text", get_translation($final_text));
		$t->parse("final_text_block");
	}	
	
	$t->parse("block_body", false);
	$t->parse($block_name, true);
	
	function after_request() {
		global $r, $db, $table_prefix, $user_id, $category_id, $approve_product_registration;
		
		$current_user_id = $r->get_value("user_id");
		$registration_id = $r->get_value("registration_id");
		if ($current_user_id && $current_user_id != $user_id) {
			header("Location: " . $r->get_return_url());
			exit;
		} elseif (!$current_user_id)  {
			$r->set_value("user_id", $user_id);
		}

		if ($approve_product_registration) {
			$r->set_value("is_approved", 1);
		} else {
			$r->set_value("is_approved", 0);
		}
		$r->set_value("date_added",    va_time());
		$r->set_value("date_modified", va_time());		
		if (!$registration_id) {
			$sql = " SELECT MAX(registration_id) FROM " . $table_prefix . "registration_list";
			$registration_id = get_db_value($sql) + 1;
			$r->set_value("registration_id", $registration_id);
		}
		$r->set_value("category_id", $category_id);
	}

	function after_save($pp) {
		global $r, $table_prefix, $db;		
		$registration_id = $r->get_value("registration_id");
						
		$sql  = " DELETE FROM " . $table_prefix . "registration_properties ";
		$sql .= " WHERE registration_id=" . $db->tosql($registration_id, INTEGER);
		$db->query($sql);
		
		foreach ($pp as $id => $data) {
			$property_id =$data["property_id"];
			$param_name = "pp_" . $property_id;
			$values = array();
			if ($r->get_property_value($param_name, CONTROL_TYPE) == CHECKBOXLIST) {
				$values = $r->get_value($param_name);
			} else {
				$values[] = $r->get_value($param_name);
			}
			if (is_array($values)) {
				for ($i = 0; $i < sizeof($values); $i++) {
					$property_value = $values[$i];
					if (strlen($property_value)) {
						$sql  = " INSERT INTO " . $table_prefix . "registration_properties ";
						$sql .= " (registration_id, property_id, property_value) VALUES (";
						$sql .= $db->tosql($registration_id, INTEGER) . ", ";
						$sql .= $db->tosql($property_id, INTEGER) . ", ";
						$sql .= $db->tosql($property_value, TEXT) . ") ";
						$db->query($sql);
					}
				}
			}
		}
		
		send_product_registration_emails($registration_id, $r->get_value("is_approved"), true);
	}
?>