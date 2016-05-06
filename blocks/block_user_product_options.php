<?php

	check_user_security("access_products");

	$item_id = get_param("item_id");
	$sql  = " SELECT item_name FROM " . $table_prefix . "items ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$sql .= " AND user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$item_name = get_translation($db->f("item_name"));
	} else {
		$item_id = "";
	}

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
	$allow_options = get_setting_value($product_settings, "allow_options", 0);
	$allow_subcomponents = get_setting_value($product_settings, "allow_subcomponents", 0);
	$allow_subcomponents_selection = get_setting_value($product_settings, "allow_subcomponents_selection", 0);
	if (!$item_id || !($allow_options || $allow_subcomponents || $allow_subcomponents_selection)) {
		header("Location: " . get_custom_friendly_url("user_products.php"));
		exit;
	}

  $t->set_file("block_body","block_user_product_options.html");
	$t->set_var("user_home_href",  	get_custom_friendly_url("user_home.php"));
	$t->set_var("user_products_href",  get_custom_friendly_url("user_products.php"));
	$t->set_var("user_product_href",   get_custom_friendly_url("user_product.php"));

	$t->set_var("item_id", $item_id);
	$t->set_var("item_name", htmlspecialchars($item_name));

	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", get_custom_friendly_url("user_product_options.php"));
	$s->set_sorter(ID_MSG, "sorter_property_id", "1", "property_id");
	$s->set_sorter(NAME_MSG, "sorter_property_name", "2", "property_name");
	$s->set_sorter(TYPE_MSG, "sorter_property_type_id", "3", "property_type_id");

	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", get_custom_friendly_url("user_product_options.php"));

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "items_properties ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);

	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 20;
	$pages_number = 10;

	$user_property_url = new VA_URL(get_custom_friendly_url("user_product_option.php"), true);
	$t->set_var("user_property_new_url", $user_property_url->get_url());

	$user_component_single_url = new VA_URL(get_custom_friendly_url("user_product_subcomponent.php"), true);
	$t->set_var("user_component_single_url", $user_component_single_url->get_url());

	$user_component_selection_url = new VA_URL(get_custom_friendly_url("user_product_subcomponents.php"), true);
	$t->set_var("user_component_selection_url", $user_component_selection_url->get_url());

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT property_id,property_name,property_type_id FROM " . $table_prefix . "items_properties ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$sql .= $s->order_by;
	$db->query($sql);
	if($db->next_record())
	{
		$user_property_url->add_parameter("property_id", DB, "property_id");
		$user_component_single_url->add_parameter("property_id", DB, "property_id");
		$user_component_selection_url->add_parameter("property_id", DB, "property_id");
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$property_id = $db->f("property_id");
			$property_type_id = $db->f("property_type_id");
			if ($property_type_id == "3") {
				$property_type = SUBCOMPONENT_SELECTION_MSG;
				$user_property_edit_url = $user_component_selection_url->get_url();
			} else if ($property_type_id == "2") {
				$property_type = SUBCOMPONENT_MSG;
				$user_property_edit_url = $user_component_single_url->get_url();
			} else {
				$property_type = OPTION_MSG;
				$user_property_edit_url = $user_property_url->get_url();
			}

			$t->set_var("property_id", $property_id);
			$t->set_var("property_type", $property_type);
			$t->set_var("property_name", htmlspecialchars(get_translation($db->f("property_name"), $language_code)));

			$t->set_var("user_property_edit_url", $user_property_edit_url);

			$t->parse("records", true);
		} while($db->next_record());
	} else {
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	if ($allow_options) {
		$t->parse("add_option", false);
	}
	if ($allow_subcomponents) {
		$t->parse("add_subcomponent", false);
	}
	if ($allow_subcomponents_selection) {
		$t->parse("add_subcomponent_selection", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>