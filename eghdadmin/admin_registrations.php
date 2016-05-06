<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_registrations.php                                  ***
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
	
	check_admin_security("admin_registration");
		
	$permissions = get_permissions();
	$edit_reg_list_priv = get_setting_value($permissions, "edit_reg_list", 0);
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_registrations.html");	
	$t->set_var("admin_registration_href", "admin_registration.php");
	$t->set_var("admin_registrations_href", "admin_registrations.php");
	$t->set_var("admin_registration_view_href", "admin_registration_view.php");
	$t->set_var("admin_registration_edit_href", "admin_registration_edit.php");
	$t->set_var("admin_export_href", "admin_export.php");
	
	$t->set_var("date_edit_format", join("", $date_edit_format));
		
	$registrations_ids = get_param("registrations_ids");
	$is_approved_status = get_param("is_approved_status");
	$operation = get_param("operation");
	
	if ($operation == "update_status") {
		if ($edit_reg_list_priv) {
			if (strlen($registrations_ids) && strlen($is_approved_status)) {
				$ids = explode(",", $registrations_ids);
				for ($i = 0; $i < sizeof($ids); $i++) {
					update_registration_status($ids[$i], $is_approved_status);
				}
			}
		} else {
			$orders_errors .= NOT_ALLOWED_UPDATE_ORDERS_MSG;
		}
	} elseif ($operation == "remove_registrations") {
		if ($edit_reg_list_priv) {
			remove_registrations($registrations_ids);
		} else {
			$orders_errors .= NOT_ALLOWED_REMOVE_ORDERS_MSG;
		}
	}
	
	$yes_no =
		array(
			array(1, IS_APPROVED_MSG), array(0, NOT_APPROVED_MSG)
		);
	$yes_no_all =
		array(
			array("", ALL_MSG), array(1, IS_APPROVED_MSG), array(0, NOT_APPROVED_MSG)
		);

			
	$r = new VA_Record($table_prefix . "registration_list");
	$r->add_textbox("s_rn", TEXT, REGISTRATION_NUMBER_MSG);
	$r->change_property("s_rn", TRIM, true);
	$r->add_textbox("s_pi", TEXT);
	$r->add_textbox("s_ne", TEXT);
	$r->change_property("s_ne", TRIM, true);
	$r->add_textbox("s_kw", TEXT);
	$r->change_property("s_kw", TRIM, true);
	$r->add_textbox("s_sd", DATE, FROM_DATE_MSG);
	$r->change_property("s_sd", VALUE_MASK, $date_edit_format);
	$r->change_property("s_sd", TRIM, true);
	$r->add_textbox("s_ed", DATE, END_DATE_MSG);
	$r->change_property("s_ed", VALUE_MASK, $date_edit_format);
	$r->change_property("s_ed", TRIM, true);
	$r->add_select("s_ap", TEXT, $yes_no_all);
	$r->get_form_parameters();
	$r->validate();
	$r->set_form_parameters();
	set_options($yes_no, "is_approved_status", "is_approved_status");
	
	$where = ""; $product_search = false;
	if (!$r->errors) {
		if (!$r->is_empty("s_rn")) {
			$s_rn = $r->get_value("s_rn");
			if (preg_match("/^(\d+)(,\d+)*$/", $s_rn))	{
				$where  = " (reg.registration_id IN (" . $s_rn . ") ";
				$where .= " OR reg.invoice_number=" . $db->tosql($s_rn, TEXT);
				$where .= " OR reg.serial_number=" . $db->tosql($s_rn, TEXT) . ") ";
			} else {
				$where .= " (reg.invoice_number=" . $db->tosql($s_rn, TEXT);
				$where .= " OR reg.serial_number=" . $db->tosql($s_rn, TEXT) . ") ";
			}
		}
		
		if (!$r->is_empty("s_pi")) {
			if (strlen($where)) { $where .= " AND "; }
			$s_pi = $r->get_value("s_pi");
			$where .= " reg.item_id=" . $db->tosql($s_pi, INTEGER);
		}
		
		if (!$r->is_empty("s_ne")) {
			if (strlen($where)) { $where .= " AND "; }
			$s_ne = $r->get_value("s_ne");
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
		
		if (!$r->is_empty("s_kw")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " (reg.item_name LIKE '%" . $db->tosql($r->get_value("s_kw"), TEXT, false) . "%'";
			$where .= " OR reg.item_code LIKE '%" . $db->tosql($r->get_value("s_kw"), TEXT, false) . "%'";
			$where .= " OR it.item_name  LIKE '%" . $db->tosql($r->get_value("s_kw"), TEXT, false) . "%'";
			$where .= " OR it.item_code  LIKE '%" . $db->tosql($r->get_value("s_kw"), TEXT, false) . "%')";
		}
		
		if (!$r->is_empty("s_sd")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " reg.date_added>=" . $db->tosql($r->get_value("s_sd"), DATE);
		}
		if (!$r->is_empty("s_ed")) {
			if (strlen($where)) { $where .= " AND "; }
			$end_date = $r->get_value("s_ed");
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$where .= " reg.date_added<" . $db->tosql($day_after_end, DATE);
		}
		
		if (!$r->is_empty("s_ap")) {
			if (strlen($where)) { $where .= " AND "; }
			if ($r->get_value("s_ap")) {
				$where .= " reg.is_approved=1";
			} else {
				$where .= " reg.is_approved=0";
			}
		}
	}
	
	$where_sql = ""; 
	if (strlen($where)) {
		$where_sql = " WHERE " . $where;
	}

		
	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_registrations.php");
	$s->set_default_sorting(1, "desc");
	$s->set_sorter(ORDER_NUMBER_COLUMN, "sorter_id", "1", "reg.registration_id");
	$s->set_sorter(CUSTOMER_NAME_MSG, "sorter_username", "2", "u.name");
	$s->set_sorter(STATUS_MSG, "sorter_is_approved", "3", "reg.is_approved");
	$s->set_sorter(CATEGORY_MSG, "sorter_category_name", "4", "c.category_name");
	$s->set_sorter(PROD_TITLE_COLUMN, "sorter_item_id_name", "5", "it.item_name");
	$s->set_sorter(DATE_ADDED_MSG, "sorter_date_added", "6", "reg.date_added");

	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", "admin_registrations.php");

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) FROM ((" . $table_prefix . "registration_list reg ";
	$sql .= " LEFT JOIN " . $table_prefix . "registration_items it ON it.item_id = reg.item_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON u.user_id = reg.user_id) ";
	$total_records = get_db_value($sql . $where_sql);
	$records_per_page = 25;
	$pages_number = 5;
	
	
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT reg.registration_id, reg.is_approved, reg.date_added, ";
	$sql .= " c.category_name, c.category_id, it.item_name AS item_id_name, u.name ";
	$sql .= " FROM ((( " . $table_prefix . "registration_list reg ";
	$sql .= " LEFT JOIN " . $table_prefix . "registration_categories c ON c.category_id = reg.category_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "registration_items it ON it.item_id = reg.item_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON u.user_id = reg.user_id) ";
	$sql .= $where_sql;
	$db->query($sql . $s->order_by);
	
	$registration_index = 0;
	if ($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$registration_index++;
			$t->set_var("registration_index", $registration_index);
			$registration_id = $db->f("registration_id");			
			$t->set_var("registration_id", $registration_id);
			$t->set_var("username", $db->f("name"));
			$is_approved     = $db->f("is_approved");
			if ($is_approved) {
				$t->set_var("is_approved", IS_APPROVED_MSG);
			} else {
				$t->set_var("is_approved", NOT_APPROVED_MSG);
			}
			
			if ($db->f("category_id")) {
				$t->set_var("category_name", get_translation($db->f("category_name")));
			} else {
				$t->set_var("category_name", TOP_CATEGORY_MSG);
			}
			$t->set_var("item_id_name", get_translation($db->f("item_id_name")));			
			
			$date_added = $db->f("date_added", DATETIME);
			$date_added = va_date($datetime_show_format, $date_added);
			$t->set_var("date_added", $date_added);
			
			if ($edit_reg_list_priv) {
				$t->parse("update_list_priv", false);
			} else {
				$t->set_var("update_list_priv", "");
			}
			
			$row_style = ($registration_index % 2 == 0) ? "row1" : "row2";			
			$t->set_var("row_style", $row_style);
			
		$t->parse("records", true);
		} while ($db->next_record());
		$t->set_var("registrations_number", $registration_index);
		
		$t->set_var("page", $page_number);
		$t->set_var("s_ap_search", $r->get_value("s_ap"));
		if ($edit_reg_list_priv) {
			$t->parse("update_status_button", false);
			$t->parse("remove_registrations_button", false);
		}
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}
	
	if ($edit_reg_list_priv) {
		$t->parse("registration_add_block", false);
	}
	
	
	if (strlen($where) && $total_records > 0) {
		$admin_export_filtered_url = new VA_URL("admin_export.php", true);
		$admin_export_filtered_url->add_parameter("table", CONSTANT, "registrations");
		$t->set_var("admin_export_filtered_url", $admin_export_filtered_url->get_url());
		$t->set_var("total_filtered", $total_records);
		$t->parse("export_filtered", false);
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	
	$t->pparse("main");
	
	function update_registration_status($id, $is_approved) {
		global $db, $table_prefix;
		
		$sql  = " SELECT registration_id FROM " . $table_prefix . "registration_list";
		$sql .= " WHERE registration_id=" . $db->tosql($id, INTEGER);
		$sql .= " AND NOT(is_approved=" . $db->tosql($is_approved, INTEGER, true, false) . ")";
		$db->query($sql);
		if ($db->next_record()) {
			$r = new VA_Record($table_prefix . "registration_list");
			$r->add_where("registration_id", INTEGER);
			$r->add_textbox("is_approved", INTEGER);
			$r->add_textbox("admin_id_modified_by", INTEGER);
			$r->add_textbox("date_modified", DATETIME);
			$r->set_value("registration_id", $id);
			$r->set_value("is_approved", $is_approved);
			$r->set_value("admin_id_modified_by", get_session("session_admin_id"));
			$r->set_value("date_modified", va_time());
			$r->update_record();
			
			send_product_registration_emails($id, $is_approved);
		}
	}
	
	function remove_registrations($ids) {
		global $db, $table_prefix;
		
		$sql  = " DELETE FROM " . $table_prefix . "registration_properties ";
		$sql .= " WHERE registration_id IN (" . $db->tosql($ids, INTEGERS_LIST) . ")";
		$db->query($sql);
		
		$sql  = " DELETE FROM " . $table_prefix . "registration_list ";
		$sql .= " WHERE registration_id IN (" . $db->tosql($ids, INTEGERS_LIST) . ")";
		$db->query($sql);
	}
?>