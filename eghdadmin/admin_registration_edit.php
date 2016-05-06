<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_registration_edit.php                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/registration_functions.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");
	
	check_admin_security("edit_reg_list");
	
	$registration_id = get_param("registration_id");
	$operation = get_param("operation");
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_registration_edit.html");	
	$t->set_var("admin_registration_href", "admin_registration.php");
	$t->set_var("admin_registrations_href", "admin_registrations.php");
	$t->set_var("admin_registration_view_href", "admin_registration_view.php");
	$t->set_var("admin_registration_edit_href", "admin_registration_edit.php");	
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CATEGORY_MSG, CONFIRM_DELETE_MSG));
	
	$t->set_var("user_select_block", "");
	if ($operation == "get_users") {
		$user_name = get_param("user_name");
		$sql  = " SELECT user_id, login, name, first_name, last_name FROM " . $table_prefix . "users";
		if ($user_name) {
			$sql .= " WHERE login LIKE '%" . $db->tosql($user_name, TEXT, false) . "%' ";
			$sql .= " OR name LIKE '%" . $db->tosql($user_name, TEXT, false) . "%' ";
			$sql .= " OR first_name LIKE '%" . $db->tosql($user_name, TEXT, false) . "%' ";
			$sql .= " OR last_name LIKE '%" . $db->tosql($user_name, TEXT, false) . "%' ";
		}
		$sql .= " ORDER BY login";
		$db->query($sql);
		$user_index = 0;
		while ($db->next_record()) {
			$user_index++;
			$t->set_var("user_id", get_translation($db->f("user_id")));
			$t->set_var("login", get_translation($db->f("login")));
			$t->set_var("name", get_translation($db->f("name")));
			$t->set_var("first_name", get_translation($db->f("first_name")));
			$t->set_var("last_name", get_translation($db->f("last_name")));
			$bgcolor = ($user_index % 2 == 0) ? "#eeeeee" : "#fffeee";			
			$t->set_var("bgcolor", $bgcolor);
			$t->parse("user_name_row");
		}
		if (!$user_index) {
			$t->set_var("user_name_row", NO_USERS_MSG);
		}
		$t->parse("user_select_block");
		
		if( get_param("is_ajax") || (isset($_SERVER["CONTENT_TYPE"]) && ($_SERVER["CONTENT_TYPE"] == "application/ajax+html"))) {
			echo $t->get_var("user_select_block");
			exit;
		}
	}
		
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
	$sql .= " WHERE property_show IN (1,2,3) ";	
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

	// prepare and parse categories and products
	$category_id = get_param("category_id");
	if(!$category_id && $registration_id) {
		$sql  = " SELECT category_id FROM " . $table_prefix . "registration_list";
		$sql .= " WHERE registration_id=" . $registration_id;
		$category_id = get_db_value($sql);
	}
	$categories_path = "";
	if ($category_id) {
		$sql  = " SELECT category_path FROM " . $table_prefix . "registration_categories";
		$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
		$categories_path = get_db_value($sql);
		$categories_path = explode(",", $categories_path);
		if ($categories_path) {
			$categories_path[count($categories_path) - 1] = $category_id;
		}
	}
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
			if (!$next_category_id && $categories_path) {
				$next_category_id = $categories_path[$category_number];
			}
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
	$sql .= " ORDER BY it.item_order, it.item_id ";
	$products = get_db_values($sql, array());
	
	$yes_no =
	array(
		array(1, IS_APPROVED_MSG), array(0, NOT_APPROVED_MSG)
	);
	
	$r = new VA_Record($table_prefix . "registration_list");
	if ($operation == "get_categories") {
		$r->redirect = false;
	}
	$r->return_page = "admin_registrations.php";		
	if (get_param("apply")) {
		$r->redirect = false;
	}
	$r->add_where("registration_id", INTEGER, REGISTRATION_NUMBER_MSG);
	$r->change_property("registration_id", USE_IN_INSERT, true);
	
	$r->add_textbox("user_id", INTEGER, USER_ID_MSG);
	$r->change_property("user_id", USE_IN_INSERT, true);
	$r->change_property("user_id", USE_IN_UPDATE, true);
	
	$r->add_textbox("user_name", TEXT, USER_NAME_MSG);
	$r->change_property("user_name", USE_IN_UPDATE, false);
	$r->change_property("user_name", USE_IN_INSERT, false);
	$r->change_property("user_name", USE_IN_SELECT, false);
	$r->change_property("user_name", SHOW, false);			
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_INSERT, true);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("date_modified", DATETIME);
	$r->add_textbox("admin_id_added_by", INTEGER);
	$r->change_property("admin_id_added_by", USE_IN_INSERT, true);
	$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
	$r->add_textbox("admin_id_modified_by", INTEGER);
	  	
	$r->add_select("is_approved", INTEGER, $yes_no);
	
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
	
	$show_purchased_date = false;
	$purchased_date_required = false;
	if (get_setting_value($registration_settings, "show_purchased_day")) {
		$r->add_textbox("purchased_day", INTEGER, DAY_OF_PURCHASE_MSG);
		if (get_setting_value($registration_settings, "purchased_day_required")) {
			$r->parameters["purchased_day"][REQUIRED] = true;
			$purchased_date_required = true;
		}
		$show_purchased_date = true;
	}
	if (get_setting_value($registration_settings, "show_purchased_month")) {
		$r->add_select("purchased_month", INTEGER, $purchased_months, MONTH_OF_PURCHASE_MSG);
		if (get_setting_value($registration_settings, "purchased_month_required")) {
			$r->parameters["purchased_month"][REQUIRED] = true;
			$purchased_date_required = true;
		}
		$show_purchased_date = true;
	}
	if (get_setting_value($registration_settings, "show_purchased_year")) {
		$r->add_select("purchased_year", INTEGER, $purchased_years, YEAR_OF_PURCHASE_MSG);
		if (get_setting_value($registration_settings, "purchased_year_required")) {
			$r->parameters["purchased_year"][REQUIRED] = true;
			$purchased_date_required = true;
		}
		$show_purchased_date = true;
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
	if (!strlen($operation)) {
		$sql  = " SELECT property_id, property_value ";
		$sql .= " FROM " . $table_prefix . "registration_properties ";
		$sql .= " WHERE registration_id=" . $db->tosql($registration_id, INTEGER);
		$db->query($sql);
		while ($db->next_record()) {
			$property_id    = $db->f("property_id");
			$property_value = $db->f("property_value");
			$param_name = "pp_" . $property_id;
			$r->set_value($param_name, $property_value);
		}
	}
	
	$r->set_event(AFTER_REQUEST, "after_request");
	$r->set_event(AFTER_SELECT,  "after_request");	
	$r->set_event(AFTER_INSERT, "after_save", array($pp, true));
	$r->set_event(AFTER_UPDATE, "after_save", array($pp, false));
	$r->set_event(BEFORE_SHOW,   "before_show", $pp);
	$r->set_event(BEFORE_DELETE, "before_delete");
	$r->set_event(BEFORE_INSERT, "before_save");
	$r->set_event(BEFORE_UPDATE, "before_save");

	$r->process();	
	
	if ($show_purchased_date) {
		if ($purchased_date_required) {
			$t->set_var("purchased_date_required", "*");
		}
		$t->parse("purchased_date_block");
	}
		
	if ($registration_id) {
		$t->set_var("save_button", UPDATE_BUTTON);
	} else {
		$t->set_var("save_button", ADD_BUTTON);		
	}
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	
	$t->pparse("main");
		
	function before_save() {
		global $r, $db, $table_prefix, $prev_is_approved;
		
		$prev_is_approved = false;
		
		$registration_id = $r->get_value("registration_id");
		if (!$registration_id) {
			$sql = " SELECT MAX(registration_id) FROM " . $table_prefix . "registration_list";
			$registration_id = get_db_value($sql) + 1;
			$r->set_value("registration_id", $registration_id);
		} else {
			$sql  = " SELECT is_approved FROM " . $table_prefix . "registration_list ";
			$sql .= " WHERE registration_id=" . $db->tosql("registration_id", INTEGER);
			$prev_is_approved = get_db_value($sql);
		}
		
		$r->set_value("date_added", va_time());
		$r->set_value("date_modified", va_time());
		$r->set_value("admin_id_added_by", get_session("session_admin_id"));
		$r->set_value("admin_id_modified_by", get_session("session_admin_id"));	
	}
	
	function before_delete() {
		global $r, $table_prefix, $db;		
		$registration_id = $r->get_value("registration_id");
		$sql  = " DELETE FROM " . $table_prefix . "registration_properties ";
		$sql .= " WHERE registration_id=" . $db->tosql($registration_id, INTEGER);
	}	
	
	function before_show($pp) {
		global $r, $db, $table_prefix, $action;
		
		$registration_id = $r->get_value("registration_id");
		$user_id         = $r->get_value("user_id");
		if (!$user_id || !$registration_id) {			
			$user_name = $r->get_value("user_name");
			$r->change_property("user_name", SHOW, true);
			$sql  = " SELECT login, name, first_name, last_name FROM " . $table_prefix . "users";
			$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$user_name = get_translation($db->f("name"));
				if (!$user_name) {
					$first_name = get_translation($db->f("first_name"));
					$last_name  = get_translation($db->f("last_name"));
					if ($last_name) {
						$user_name = $first_name . " " . $first_name;
					} else {
						$user_name = get_translation($db->f("login"));
					}
				}					
				$r->set_value("user_name", $user_name);
			}
		}
		show_custom_properties($pp, $registration_id);
	}
	
	function after_request() {
		global $r, $category_id;
		$r->set_value("category_id", $category_id);		
	}

	function after_save($params) {
		global $r, $table_prefix, $db,  $prev_is_approved;
		list($pp, $just_placed) = $params;
		
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
		
		if (($prev_is_approved != $r->get_value("is_approved")) || $just_placed) {
			send_product_registration_emails($registration_id, $r->get_value("is_approved"), $just_placed);
		}
	}
	
?>