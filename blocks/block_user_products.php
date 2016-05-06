<?php

	check_user_security("access_products");

	// get product settings
	$setting_type = "user_product_" . get_session("session_user_type_id");
	$product_settings = array();
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT);
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while($db->next_record()) {
		$product_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$options_block = false; $options_title = "";
	$allow_options = get_setting_value($product_settings, "allow_options", 0);
	$allow_subcomponents = get_setting_value($product_settings, "allow_subcomponents", 0);
	$allow_subcomponents_selection = get_setting_value($product_settings, "allow_subcomponents_selection", 0);
	if ($allow_options && ($allow_subcomponents || $allow_subcomponents_selection)) {
		$options_block = true;
		$options_title = OPTIONS_AND_COMPONENTS_MSG;
	} else if ($allow_options) {
		$options_block = true;
		$options_title = PROD_OPTIONS_MSG;
	} else if ($allow_subcomponents || $allow_subcomponents_selection) {
		$options_block = true;
		$options_title = PROD_SUBCOMPONENTS_MSG;
	}

	$t->set_file("block_body","block_user_products.html");
	$t->set_var("user_products_href",  get_custom_friendly_url("user_products.php"));
	$t->set_var("user_product_href",   get_custom_friendly_url("user_product.php"));
	$t->set_var("user_product_options_href", get_custom_friendly_url("user_product_options.php"));
	$t->set_var("user_home_href", get_custom_friendly_url("user_home.php"));
	$t->set_var("options_title", $options_title);


	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", get_custom_friendly_url("user_products.php"));
	$s->set_default_sorting(1, "desc");
	$s->set_sorter(ID_MSG, "sorter_id", "1", "i.item_id");
	$s->set_sorter(PROD_TITLE_COLUMN, "sorter_title", "2", "i.item_name");
	$s->set_sorter(PROD_PRICE_COLUMN, "sorter_price", "3", "i.price");
	$s->set_sorter(PROD_QTY_COLUMN,   "sorter_qty", "4", "i.stock_level");
	$s->set_sorter(STATUS_MSG, "sorter_status", "5", "i.is_approved");
	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", get_custom_friendly_url("user_products.php"));

	$user_allow_select_sites  = get_setting_value($product_settings, "user_allow_select_sites", "");
	// set up variables for navigator
	if ($user_allow_select_sites) {
		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "items ";
		$sql .= " WHERE user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	} else {
		$sql  = " SELECT COUNT(*) FROM ";
		if (isset($site_id)) {
			$sql .= "(";
		}
		$sql .= $table_prefix . "items i";
		if (isset($site_id)) {
			$sql .= " LEFT JOIN ". $table_prefix . "items_sites s ON s.item_id=i.item_id) ";
		}		
		$sql .= " WHERE i.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
		if (isset($site_id)) {
			$sql .= " AND (i.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		} else {
			$sql .= " AND i.sites_all=1 ";
		}
	}
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	
	if ($user_allow_select_sites) {
		$sql  = " SELECT item_id, item_name, price, stock_level, is_showing, is_approved FROM " . $table_prefix . "items ";
		$sql .= " WHERE user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	} else {
		$sql  = " SELECT i.item_id, i.item_name, i.price, i.stock_level, i.is_showing, i.is_approved FROM ";
		if (isset($site_id)) {
			$sql .= "(";
		}
		$sql .= $table_prefix . "items i";
		if (isset($site_id)) {
			$sql .= " LEFT JOIN ". $table_prefix . "items_sites s ON (s.item_id=i.item_id AND i.sites_all=0)) ";
		}		
		$sql .= " WHERE i.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
		if (isset($site_id)) {
			$sql .= " AND (i.sites_all=1 OR s.site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		} else {
			$sql .= " AND i.sites_all=1 ";
		}
	}
	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$item_id = $db->f("item_id");
			$item_name = $db->f("item_name");
			$price = $db->f("price");
			$quantity = $db->f("stock_level");

			$t->set_var("item_id", $item_id);
			$t->set_var("item_name", $item_name);
			$t->set_var("price", currency_format($price));
			$t->set_var("quantity", $quantity);

			$is_approved = $db->f("is_approved");
			$is_showing = $db->f("is_showing");
			if ($is_approved != 1) {
				$status = PROD_NOT_APPROVED_MSG;
			}	else if ($is_showing != 1) {
				$status = HIDDEN_MSG;
			} else {
				$status = PROD_ACTIVE_MSG;
			}
			$t->set_var("status", $status);

			if ($options_block) {
				$t->parse("options_block", false);
			}


			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>