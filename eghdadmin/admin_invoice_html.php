<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_invoice_html.php                                   ***
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
	include_once($root_folder_path . "includes/shopping_cart.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");

	$currency = get_currency();
	
	$order_id = get_param("order_id");	
	if ($sitelist) {
		$site_id = get_db_value("SELECT site_id FROM " . $table_prefix . "orders WHERE order_id=" . $db->tosql($order_id, INTEGER, true, false));
	}
	
	$invoice = array();
	$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='printable'";
	if ($sitelist) {
		$sql .= "AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= "ORDER BY site_id ASC";
	} else {
		$sql .= "AND site_id=1";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$invoice[$db->f("setting_name")] = $db->f("setting_value");
	}

	$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='order_info'";
	if ($sitelist) {
		$sql .= "AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= "ORDER BY site_id ASC";
	} else {
		$sql .= "AND site_id=1";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$order_info[$db->f("setting_name")] = $db->f("setting_value");
	}

	$permissions = get_permissions();

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_invoice_html.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_order_href", $order_details_site_url . "admin_order.php");

	$r = new VA_Record($table_prefix . "orders");
	$r->add_where("order_id", INTEGER);
	$r->set_value("order_id", $order_id);

	$items_text = show_order_items($order_id, true, "admin_invoice_html");

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
	$r->add_textbox("transaction_id", TEXT);
	$r->add_textbox("currency_code", TEXT);
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

	$sql  = " SELECT payment_name ";
	$sql .= " FROM " . $table_prefix . "payment_systems ";
	$sql .= " WHERE payment_id=" . $db->tosql($r->get_value("payment_id"), INTEGER);
	$payment_name = get_db_value($sql);
	$t->set_var("payment_name", $payment_name);
	if ($r->is_empty("transaction_id")) {
		$r->parameters["transaction_id"][SHOW] = false;
	}

	$r->set_value("company_id", get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("company_id"), INTEGER))));
	$r->set_value("state_id", get_translation(get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("state_id"), INTEGER))));
	$r->set_value("country_id", get_translation(get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("country_id"), INTEGER))));
	$r->set_value("delivery_company_id", get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("delivery_company_id"), INTEGER))));
	$r->set_value("delivery_state_id", get_translation(get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("delivery_state_id"), INTEGER))));
	$r->set_value("delivery_country_id", get_translation(get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("delivery_country_id"), INTEGER))));
	$r->set_value("cc_type", get_translation(get_db_value("SELECT credit_card_name FROM " . $table_prefix . "credit_cards WHERE credit_card_id=" . $db->tosql($r->get_value("cc_type"), INTEGER))));

	for ($i = 0; $i < sizeof($parameters); $i++)
	{                                    
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

	if ($personal_number > 0) {
		$t->parse("personal", false);
	}

	if ($delivery_number > 0) {
		$t->parse("delivery", false);
	}

	if (isset($invoice["invoice_header"])) {
		$t->set_var("invoice_header", nl2br($invoice["invoice_header"]));
	}
	if (isset($invoice["invoice_logo"]) && strlen($invoice["invoice_logo"])) {
		$image_path = $invoice["invoice_logo"];
		if (preg_match("/^http\:\/\//", $image_path)) {
			$image_size = "";
		} else {
			$image_size = @GetImageSize($image_path);
		}
		$t->set_var("image_path", htmlspecialchars($image_path));
		if(is_array($image_size)) {
			$t->set_var("image_width", "width=\"" . $image_size[0] . "\"");
			$t->set_var("image_height", "height=\"" . $image_size[1] . "\"");
		} else {
			$t->set_var("image_width", "");
			$t->set_var("image_height", "");
		}
		$t->parse("invoice_logo", false);
	}
	if (isset($invoice["invoice_footer"])) {
		$t->set_var("invoice_footer", nl2br($invoice["invoice_footer"]));
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>