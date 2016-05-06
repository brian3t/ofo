<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_packing_html.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/order_items.php");
	include_once($root_folder_path . "includes/parameters.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");

	$packing = array();
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='printable'";
	if ($multisites_version) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id,INTEGER) . ") ";
		$sql .= " ORDER BY site_id ASC ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$packing[$db->f("setting_name")] = $db->f("setting_value");
	}

	$site_url	= get_setting_value($settings, "site_url", "");
	$secure_url	= get_setting_value($settings, "secure_url", "");
	$show_item_code = get_setting_value($packing, "item_code_packing", 0);
	$show_manufacturer_code = get_setting_value($packing, "manufacturer_code_packing", 0);

	$item_code_width = "0";	$manufacturer_code_width = "0";
	if ($show_item_code && $show_manufacturer_code) {
		$item_code_width = "15%";
		$manufacturer_code_width = "15%";
	} elseif ($show_item_code) {
		$item_code_width = "30%";
	} elseif ($show_manufacturer_code) {
		$manufacturer_code_width = "30%";
	}

	$currency = get_currency();

	$dbi = new VA_SQL();
	$dbi->DBType      = $db_type;
	$dbi->DBDatabase  = $db_name;
	$dbi->DBUser      = $db_user;
	$dbi->DBPassword  = $db_password;
	$dbi->DBHost      = $db_host;
	$dbi->DBPort      = $db_port;
	$dbi->DBPersistent= $db_persistent;


	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='order_info'";
	if ($multisites_version) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id,INTEGER) . ") ";
		$sql .= " ORDER BY site_id ASC ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$order_info[$db->f("setting_name")] = $db->f("setting_value");
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_packing_html.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_order_href", $order_details_site_url . "admin_order.php");

	if ($show_item_code) {
		$t->set_var("item_code_width", $item_code_width);
		$t->sparse("item_code_title", false);
	}
	if ($show_manufacturer_code) {
		$t->set_var("manufacturer_code_width", $manufacturer_code_width);
		$t->sparse("manufacturer_code_title", false);
	}
	

	$order_id = get_param("order_id");
	$r = new VA_Record($table_prefix . "orders");
	$r->add_where("order_id", INTEGER);
	$r->set_value("order_id", $order_id);


	$personal_number = 0;
	$delivery_number = 0;
	for ($i = 0; $i < sizeof($parameters); $i++)
	{                                    
		$personal_param = "show_" . $parameters[$i];
		$delivery_param = "show_delivery_" . $parameters[$i];
		$r->add_textbox($parameters[$i], TEXT);
		$r->add_textbox("delivery_" . $parameters[$i], TEXT);
		if (isset($order_info[$personal_param]) && $order_info[$personal_param] == 1) {
			$personal_number++;
		} else {
			$r->parameters[$parameters[$i]][SHOW] = false;
		}
		if (isset($order_info[$delivery_param]) && $order_info[$delivery_param] == 1) {
			$delivery_number++;
		} else {
			$r->parameters["delivery_" . $parameters[$i]][SHOW] = false;
		}
	}

	$r->add_textbox("invoice_number", TEXT);
	$r->add_textbox("user_id", INTEGER);
	$r->add_textbox("payment_id", INTEGER);
	$r->add_textbox("order_placed_date", DATETIME);
	$r->add_textbox("currency_code", TEXT);
	$r->add_textbox("currency_rate", NUMBER);
	$r->add_textbox("shipping_tracking_id", TEXT);
	$r->add_textbox("remote_address", TEXT);
	$r->add_textbox("cc_name", TEXT);
	$r->add_textbox("cc_first_name", TEXT);
	$r->add_textbox("cc_last_name", TEXT);
	$r->add_textbox("cc_number", TEXT);
	$r->add_textbox("cc_start_date", DATETIME);
	$r->change_property("cc_start_date", VALUE_MASK, array("MM", " / ", "YYYY"));
	$r->add_textbox("cc_expiry_date", DATETIME);
	$r->change_property("cc_expiry_date", VALUE_MASK, array("MM", " / ", "YYYY"));
	$r->add_textbox("cc_type", INTEGER);
	$r->add_textbox("cc_issue_number", INTEGER);
	$r->add_textbox("cc_security_code", TEXT);
	$r->add_textbox("pay_without_cc", TEXT);

	$r->get_db_values();

	$order_currency_code = $r->get_value("currency_code");
	$order_currency_rate = $r->get_value("currency_rate");
	$currency = get_currency($order_currency_code);
	$order_currency_left = $currency["left"];
	$order_currency_right = $currency["right"];

	$order_placed_date = $r->get_value("order_placed_date");
	$order_date = va_date($date_show_format, $order_placed_date);
	$t->set_var("order_date", $order_date);

	$r->set_value("company_id", get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("company_id"), INTEGER))));
	$r->set_value("state_id", get_translation(get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("state_id"), INTEGER))));
	$r->set_value("country_id", get_translation(get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("country_id"), INTEGER))));
	$r->set_value("delivery_company_id", get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("delivery_company_id"), INTEGER))));
	$r->set_value("delivery_state_id", get_translation(get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("delivery_state_id"), INTEGER))));
	$r->set_value("delivery_country_id", get_translation(get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("delivery_country_id"), INTEGER))));
	$r->set_value("cc_type", get_translation(get_db_value("SELECT credit_card_name FROM " . $table_prefix . "credit_cards WHERE credit_card_id=" . $db->tosql($r->get_value("cc_type"), INTEGER))));

	for ($i = 0; $i < sizeof($parameters); $i++) {                                    
		$personal_param = $parameters[$i];
		$delivery_param = "delivery_" . $parameters[$i];
		if ($r->is_empty($personal_param)) {
			$r->parameters[$personal_param][SHOW] = false;
		}
		if ($r->is_empty($delivery_param)) {
			$r->parameters[$delivery_param][SHOW] = false;
		}
	}
	
	$r->set_parameters();

	// parse properties
	$cart_properties = 0; $personal_properties = 0;
	$delivery_properties = 0; $payment_properties = 0;
	$properties_total = 0; $properties_taxable = 0;
	$sql  = " SELECT op.property_id, op.property_type, op.property_name, op.property_value, ";
	$sql .= "  op.property_price, op.property_points_amount, op.tax_free, ocp.control_type ";
	$sql .= " FROM (" . $table_prefix . "orders_properties op ";
	$sql .= " INNER JOIN " . $table_prefix . "order_custom_properties ocp ON op.property_id=ocp.property_id)";
	$sql .= " WHERE op.order_id=" . $db->tosql($order_id, INTEGER);
	$sql .= " ORDER BY op.property_order, op.property_id ";
	$db->query($sql);
	while ($db->next_record()) {
		$property_id   = $db->f("property_id");
		$property_type = $db->f("property_type");
		$property_name = get_translation($db->f("property_name"));
		$property_value = get_translation($db->f("property_value"));
		$property_price = $db->f("property_price");
		$property_points_amount = $db->f("property_points_amount");
		$tax_free = $db->f("tax_free");
		$control_type = $db->f("control_type");

		// check value description
		if(($control_type == "CHECKBOXLIST" ||  $control_type == "RADIOBUTTON" || $control_type == "LISTBOX") && is_numeric($property_value)) {
			$sql  = " SELECT property_value FROM " . $table_prefix . "order_custom_values ";
			$sql .= " WHERE property_value_id=" . $dbi->tosql($property_value, INTEGER);
			$dbi->query($sql);
			if ($dbi->next_record()) {
				$property_value = get_translation($dbi->f("property_value"));
			}
		}

		$properties_total += $property_price;
		if ($tax_free != 1) {
			$properties_taxable += $property_price;
		}

		$t->set_var("property_name", $property_name);
		$t->set_var("property_value", $property_value);
		if ($property_price == 0) {
			$t->set_var("property_price", "");
		} else {
			$t->set_var("property_price", $order_currency_left . number_format($property_price * $order_currency_rate, $currency["decimals"], $currency["point"], $currency["separator"]) . $order_currency_right);
		}
		if ($property_type == 1) {
		  $cart_properties++;
			$t->sparse("cart_properties", true);
		} elseif ($property_type == 2) {
			$personal_properties++;
			$t->sparse("personal_properties", true);
		} elseif ($property_type == 3) {
			$delivery_properties++;
			$t->sparse("delivery_properties", true);
		} elseif ($property_type == 4) {
			$payment_properties++;
			$t->sparse("payment_properties", true);
		}
	}


	if ($personal_number > 0 || $personal_properties) {
		$t->parse("personal", false);
	}

	if ($delivery_number > 0 || $delivery_properties) {
		$t->parse("delivery", false);
	}

	if (isset($packing["packing_header"])) {
		$t->set_var("packing_header", nl2br($packing["packing_header"]));
	}
	if (isset($packing["packing_logo"]) && strlen($packing["packing_logo"])) {
		$image_path = $packing["packing_logo"];
		if (preg_match("/^http\:\/\//", $image_path)) {
			$image_size = "";
		} else {
			$image_size = @GetImageSize($image_path);
		}
		$t->set_var("image_path", htmlspecialchars($image_path));
		if (is_array($image_size)) {
			$t->set_var("image_width", "width=\"" . $image_size[0] . "\"");
			$t->set_var("image_height", "height=\"" . $image_size[1] . "\"");
		} else {
			$t->set_var("image_width", "");
			$t->set_var("image_height", "");
		}
		$t->parse("packing_logo", false);
	}
	if (isset($packing["packing_footer"])) {
		$t->set_var("packing_footer", nl2br($packing["packing_footer"]));
	}

	$packing_slips = array();
	$sql  = " SELECT oe.order_items FROM (" . $table_prefix . "orders_events oe ";
	$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON os.status_id=oe.status_id) ";
	$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
	$sql .= " AND os.is_dispatch=1 ";
	$db->query($sql);
	if ($db->next_record()) {
	 	do {
			$packing_slips[] = $db->f("order_items");
		} while ($db->next_record());
	} else {
		$packing_slips[] = "";
	}

	for ($ps = 0; $ps < sizeof($packing_slips); $ps++) 
	{
		$order_items = $packing_slips[$ps];
		$t->set_var("items", "");
		$sql  = " SELECT * FROM " . $table_prefix . "orders_items ";
		$sql .= " WHERE order_id=" . $db->tosql($order_id, INTEGER);
		if (strlen($order_items)) {
			$sql .= " AND order_item_id IN (" . $db->tosql($order_items, TEXT, false) . ") ";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$order_item_id = $db->f("order_item_id");
			$price = $db->f("price");
			$quantity = $db->f("quantity");
			$item_code = $db->f("item_code");
			$manufacturer_code = $db->f("manufacturer_code");
			$item_name = get_translation($db->f("item_name"));

			$item_properties = "";
			$sql  = " SELECT property_name, property_value FROM " . $table_prefix . "orders_items_properties ";
			$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
			$dbi->query($sql);
			while ($dbi->next_record()) {
				$item_properties .= "<br>" . get_translation($dbi->f("property_name")) . ": " . get_translation($dbi->f("property_value"));
			}


			$t->set_var("quantity", $quantity);
			$t->set_var("item_name", $item_name);
			$t->set_var("item_properties", $item_properties);

			if ($show_item_code) {
				if (strlen($item_code)) {
					if ($show_item_code == 1) {
						$t->set_var("item_code", $item_code);
						$t->sparse("item_code_text", false);
					} elseif ($show_item_code == 2) {
						$item_code_barcode_url = $site_url . "barcode_image.php?text=" . $item_code;
						$t->set_var("item_code_barcode_url", $item_code_barcode_url);
						$t->sparse("item_code_barcode", false);
					}
				} else {
					$t->set_var("item_code_text", "");
					$t->set_var("item_code_barcode", "");
				}
				$t->sparse("item_code_cell", false);
			}
			if ($show_manufacturer_code) {
				if (strlen($manufacturer_code)) {
					if ($show_manufacturer_code == 1) {
						$t->set_var("manufacturer_code", $manufacturer_code);
						$t->sparse("manufacturer_code_text", false);
					} elseif ($show_manufacturer_code == 2) {
						$manufacturer_code_barcode_url = $site_url . "barcode_image.php?text=" . $manufacturer_code;
						$t->set_var("manufacturer_code_barcode_url", $manufacturer_code_barcode_url);
						$t->sparse("manufacturer_code_barcode", false);
					}
				} else {
					$t->set_var("manufacturer_code_text", "");
					$t->set_var("manufacturer_code_barcode", "");
				}

				$t->sparse("manufacturer_code_cell", false);
			}

			$t->parse("items", true);
		}

		if ($ps > 0) {
			$t->parse("page_break", false);
		} else {
			$t->set_var("page_break", "");
		}

		$t->parse("packing", true);
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>