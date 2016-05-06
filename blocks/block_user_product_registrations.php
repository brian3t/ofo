<?php
		
	check_user_security("my_product_registrations");

	$sql  = " SELECT setting_value ";
	$sql .= " FROM " . $table_prefix . "user_types_settings ";
	$sql .= " WHERE type_id=" . $db->tosql(get_session("session_user_type_id"), INTEGER);
	$sql .= " AND setting_name=" . $db->tosql("access_product_registration", TEXT);
	$add_product_registrations = get_db_value($sql);
	
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
	
	$t->set_file("block_body","block_user_product_registrations.html");
	$t->set_var("user_product_registrations_href", get_custom_friendly_url("user_product_registrations.php"));
	$t->set_var("user_product_registration_href", get_custom_friendly_url("user_product_registration.php"));
	$t->set_var("user_home_href",   get_custom_friendly_url("user_home.php"));

	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", get_custom_friendly_url("user_product_registrations.php"));
	$s->set_default_sorting(1, "desc");
	$s->set_sorter(REGISTRATION_NUMBER_MSG, "sorter_id", "1", "registration_id");
	$s->set_sorter(STATUS_MSG, "sorter_status", "2", "is_approved");
	$s->set_sorter(CATEGORY_MSG, "sorter_category_id", "3", "c.category_name");
	$s->set_sorter(PROD_TITLE_COLUMN, "sorter_item_id", "4", "it.item_name");
	$s->set_sorter(PROD_CODE_MSG, "sorter_item_code", "5", "item_code");
	$s->set_sorter(PROD_NAME_MSG, "sorter_item_name", "6", "item_name");
	$s->set_sorter(SERIAL_NUMBER_MSG, "sorter_serial_number", "7", "serial_number");
	$s->set_sorter(INVOICE_NUMBER_MSG, "sorter_invoice_number", "8", "invoice_number");
	$s->set_sorter(STORE_NAME_MSG, "sorter_store_name", "9", "store_name");
	$s->set_sorter(DAY_OF_PURCHASE_MSG, "sorter_purchased_day", "10", "purchased_day");
	$s->set_sorter(MONTH_OF_PURCHASE_MSG, "sorter_purchased_month", "11", "purchased_month");
	$s->set_sorter(YEAR_OF_PURCHASE_MSG, "sorter_purchased_year", "12", "purchased_year");
	
	$total_cols = 2;
	$onlist_category_id     = get_setting_value($registration_settings, "onlist_category_id");
	$onlist_item_id         = get_setting_value($registration_settings, "onlist_item_id");
	$onlist_item_code       = get_setting_value($registration_settings, "onlist_item_code");
	$onlist_item_name       = get_setting_value($registration_settings, "onlist_item_name");
	$onlist_serial_number   = get_setting_value($registration_settings, "onlist_serial_number");
	$onlist_invoice_number  = get_setting_value($registration_settings, "onlist_invoice_number");
	$onlist_store_name      = get_setting_value($registration_settings, "onlist_store_name");
	$onlist_purchased_day   = get_setting_value($registration_settings, "onlist_purchased_day");
	$onlist_purchased_month = get_setting_value($registration_settings, "onlist_purchased_month");
	$onlist_purchased_year  = get_setting_value($registration_settings, "onlist_purchased_year");
	
	if ($onlist_category_id) {
		$t->parse("category_id_title");
		$total_cols++;
	}
	if ($onlist_item_id) {
		$t->parse("item_id_title");
		$total_cols++;
	}
	if ($onlist_item_code) {
		$t->parse("item_code_title");
		$total_cols++;
	}
	if ($onlist_item_name) {
		$t->parse("item_name_title");
		$total_cols++;
	}
	if ($onlist_serial_number) {
		$t->parse("serial_number_title");
		$total_cols++;
	}
	if ($onlist_invoice_number) {
		$t->parse("invoice_number_title");
		$total_cols++;
	}
	if ($onlist_store_name) {
		$t->parse("store_name_title");
		$total_cols++;
	}
	if ($onlist_purchased_day) {
		$t->parse("purchased_day_title");
		$total_cols++;
	}
	if ($onlist_purchased_month) {
		$t->parse("purchased_month_title");
		$total_cols++;
	}
	if ($onlist_purchased_year) {
		$t->parse("purchased_year_title");
		$total_cols++;
	}
	$t->set_var("total_cols", $total_cols);
	
	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", get_custom_friendly_url("user_product_registrations.php"));

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "registration_list ";
	$sql .= " WHERE user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$total_records = get_db_value($sql);
	$records_per_page = 25;
	$pages_number = 5;

	
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT reg.* ";
	if ($onlist_category_id) {
		$sql .= ", c.category_name";
	}
	if ($onlist_item_id) {
		$sql .= ", it.item_name AS item_id_name ";
	}
	$sql .= " FROM ";
	if ($onlist_category_id) {
		$sql .= "(";
	}
	if ($onlist_item_id) {
		$sql .= "(";
	}
	$sql .=  $table_prefix . "registration_list reg ";
	if ($onlist_category_id) {
		$sql .= " LEFT JOIN " . $table_prefix . "registration_categories c ON c.category_id = reg.category_id) ";
	}
	if ($onlist_item_id) {
		$sql .= " LEFT JOIN " . $table_prefix . "registration_items it ON it.item_id = reg.item_id) ";
	}
	$sql .= " WHERE reg.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$db->query($sql . $s->order_by);
	if ($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$registration_id = $db->f("registration_id");
			$is_approved     = $db->f("is_approved");
			$t->set_var("registration_id", $registration_id);
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
			$t->set_var("item_code", $db->f("item_code"));
			$t->set_var("item_name", $db->f("item_name"));
			$t->set_var("serial_number", $db->f("serial_number"));
			$t->set_var("invoice_number", $db->f("invoice_number"));
			$t->set_var("store_name", $db->f("store_name"));
			$t->set_var("purchased_day", $db->f("purchased_day"));
			$t->set_var("purchased_month", $db->f("purchased_month"));
			$t->set_var("purchased_year", $db->f("purchased_year"));
			
			if ($onlist_category_id) {				
				$t->parse("category_id_block", false);
			}
			if ($onlist_item_id) {
				$t->parse("item_id_block", false);
			}
			if ($onlist_item_code) {
				$t->parse("item_code_block", false);
			}
			if ($onlist_item_name) {
				$t->parse("item_name_block", false);
			}
			if ($onlist_serial_number) {
				$t->parse("serial_number_block", false);
			}
			if ($onlist_invoice_number) {
				$t->parse("invoice_number_block", false);
			}
			if ($onlist_store_name) {
				$t->parse("store_name_block", false);
			}
			if ($onlist_purchased_day) {
				$t->parse("purchased_day_block", false);
			}
			if ($onlist_purchased_month) {
				$t->parse("purchased_month_block", false);
			}
			if ($onlist_purchased_year) {
				$t->parse("purchased_year_block", false);
			}
			$t->parse("records", true);
		} while ($db->next_record());
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	if ($add_product_registrations) {
		$t->parse("add_product_registration_block");
	}
	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>