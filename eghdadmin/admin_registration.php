<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_registration.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("admin_registration");
	
	$permissions = get_permissions();
	$edit_reg_list_priv = get_setting_value($permissions, "edit_reg_list", 0);
	$edit_reg_categories_priv = get_setting_value($permissions, "edit_reg_categories", 0);
	$edit_reg_products_priv = get_setting_value($permissions, "edit_reg_products", 0);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_registration.html");
	$t->set_var("admin_registration_href", "admin_registration.php");
	$t->set_var("admin_registrations_href", "admin_registrations.php");
	$t->set_var("admin_registration_view_href", "admin_registration_view.php");
	$t->set_var("admin_registration_edit_href", "admin_registration_edit.php");
	$t->set_var("admin_registration_products_href", "admin_registration_products.php");
	$t->set_var("admin_registration_product_href", "admin_registration_product.php");
		
	
	$sql  = " SELECT i.item_id, i.item_code, i.item_name, i.date_added, a.admin_name ";
	$sql .= " FROM (" . $table_prefix . "registration_items i ";
	$sql .= " LEFT JOIN " . $table_prefix . "admins a ON a.admin_id = i.admin_id_added_by)";
	$sql .= " ORDER BY i.date_added DESC ";
	$db->PageNumber = 1;
	$db->RecordsPerPage = 5;
	$db->query($sql);	
	$registration_index = 0;
	if ($db->next_record())
	{
		$t->parse("prod_added_sorters", false);
		$t->set_var("prod_added_no_records", "");
		do
		{
			$registration_index++;
			$item_id    = $db->f("item_id");
			$item_code  = $db->f("item_code");
			$item_name  = get_translation($db->f("item_name"));
				
			$t->set_var("item_id", $item_id);
			$t->set_var("item_code", htmlspecialchars($item_code));
			$item_name = htmlspecialchars($item_name);
			$t->set_var("item_name", $item_name);
			$t->set_var("admin_name", $db->f("admin_name"));

			$date_added = $db->f("date_added", DATETIME);
			$date_added = va_date($datetime_show_format, $date_added);
			$t->set_var("date_added", $date_added);	
			if ($edit_reg_products_priv) {
				$t->parse("update_products_priv", false);
			} else {
				$t->set_var("update_products_priv", "");
			}
							
			$row_style = ($registration_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);
			
		$t->parse("prod_added_records", true);
		} while ($db->next_record());
	}
	else
	{
		$t->set_var("prod_added_sorters", "");
		$t->set_var("prod_added_records", "");
		$t->parse("prod_added_no_records", false);
	}
	
	$sql  = " SELECT i.item_id, i.item_code, i.item_name, i.date_modified, a.admin_name ";
	$sql .= " FROM (" . $table_prefix . "registration_items i ";
	$sql .= " LEFT JOIN " . $table_prefix . "admins a ON a.admin_id = i.admin_id_modified_by)";
	$sql .= " WHERE i.date_modified>i.date_added";
	$sql .= " ORDER BY i.date_modified DESC ";
	$db->PageNumber = 1;
	$db->RecordsPerPage = 5;
	$db->query($sql);	
	$registration_index = 0;
	if ($db->next_record())
	{
		$t->parse("prod_modified_sorters", false);
		do
		{
			$registration_index++;
			$item_id    = $db->f("item_id");
			$item_code  = $db->f("item_code");
			$item_name  = get_translation($db->f("item_name"));
				
			$t->set_var("item_id", $item_id);
			$t->set_var("item_code", htmlspecialchars($item_code));
			$item_name = htmlspecialchars($item_name);
			$t->set_var("item_name", $item_name);
			$t->set_var("admin_name", $db->f("admin_name"));

			$date_modified = $db->f("date_modified", DATETIME);
			$date_modified = va_date($datetime_show_format, $date_modified);
			$t->set_var("date_modified", $date_modified);	
			if ($edit_reg_products_priv) {
				$t->parse("update_products_priv", false);
			} else {
				$t->set_var("update_products_priv", "");
			}
							
			$row_style = ($registration_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);
			
		$t->parse("prod_modified_records", true);
		} while ($db->next_record());
		$t->parse("latest_prod_modified");
	}
	
	$sql  = " SELECT reg.registration_id, reg.is_approved, reg.date_added, u.name ";
	$sql .= " FROM ( " . $table_prefix . "registration_list reg ";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON u.user_id = reg.user_id) ";
	$sql .= " ORDER BY reg.date_added DESC ";
	$db->PageNumber = 1;
	$db->RecordsPerPage = 5;
	$db->query($sql);
	$registration_index = 0;
	if ($db->next_record())
	{
		$t->parse("reg_added_sorters", false);
		$t->set_var("reg_added_no_records", "");
		do
		{
			$registration_index++;
			$t->set_var("registration_index", $registration_index);
			$registration_id = $db->f("registration_id");			
			$t->set_var("registration_id", $registration_id);
			$t->set_var("username", $db->f("name"));
			$is_approved = $db->f("is_approved");
			if ($is_approved) {
				$t->set_var("is_approved", IS_APPROVED_MSG);
			} else {
				$t->set_var("is_approved", NOT_APPROVED_MSG);
			}						
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
			
		$t->parse("reg_added_records", true);
		} while ($db->next_record());
	}
	else
	{
		$t->set_var("reg_added_sorters", "");
		$t->set_var("reg_added_records", "");
		$t->parse("reg_added_no_records", false);
	}
	
	$sql  = " SELECT reg.registration_id, reg.is_approved, reg.date_modified, u.name ";
	$sql .= " FROM ( " . $table_prefix . "registration_list reg ";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON u.user_id = reg.user_id) ";
	$sql .= " WHERE reg.date_modified>reg.date_added";
	$sql .= " ORDER BY reg.date_modified DESC ";
	$db->PageNumber = 1;
	$db->RecordsPerPage = 5;
	$db->query($sql);
	$registration_index = 0;
	if ($db->next_record())
	{
		$t->parse("reg_modified_sorters", false);
		do
		{
			$registration_index++;
			$t->set_var("registration_index", $registration_index);
			$registration_id = $db->f("registration_id");			
			$t->set_var("registration_id", $registration_id);
			$t->set_var("username", $db->f("name"));
			$is_approved = $db->f("is_approved");
			if ($is_approved) {
				$t->set_var("is_approved", IS_APPROVED_MSG);
			} else {
				$t->set_var("is_approved", NOT_APPROVED_MSG);
			}						
			$date_modified = $db->f("date_modified", DATETIME);
			$date_modified = va_date($datetime_show_format, $date_modified);
			$t->set_var("date_modified", $date_modified);
			if ($edit_reg_list_priv) {
				$t->parse("update_list_priv", false);
			} else {
				$t->set_var("update_list_priv", "");
			}			
			$row_style = ($registration_index % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);
			
		$t->parse("reg_modified_records", true);
		} while ($db->next_record());
		$t->parse("latest_reg_modified");
	}
	
	if ($edit_reg_list_priv) {
		$t->parse("registration_add_block", false);
	}
		
	if ($edit_reg_products_priv) {
		$t->parse("products_edit_block", false);
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	
	$t->pparse("main");
?>