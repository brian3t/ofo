<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_packing_pdf.php                                    ***
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
	include_once($root_folder_path . "includes/pdflib.php");
	include_once($root_folder_path . "includes/pdf.php");
	include_once($root_folder_path . "includes/barcode_functions.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");

	$currency = get_currency();

	// additional connection 
	$dbi = new VA_SQL();
	$dbi->DBType      = $db_type;
	$dbi->DBDatabase  = $db_name;
	$dbi->DBUser      = $db_user;
	$dbi->DBPassword  = $db_password;
	$dbi->DBHost      = $db_host;
	$dbi->DBPort      = $db_port;
	$dbi->DBPersistent= $db_persistent;

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

	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='order_info'";
	if ($multisites_version) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id,INTEGER) . ") ";
		$sql .= " ORDER BY site_id ASC ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$order_info[$db->f("setting_name")] = $db->f("setting_value");
	}

	$site_url	= get_setting_value($settings, "site_url", "");
	$secure_url	= get_setting_value($settings, "secure_url", "");
	$show_item_code = get_setting_value($packing, "item_code_packing", 0);
	$show_manufacturer_code = get_setting_value($packing, "manufacturer_code_packing", 0);
	$tmp_dir = get_setting_value($settings, "tmp_dir", "");
	$tmp_images = array();

	$item_name_width = 440;	
	$item_code_start = 0;	$manufacturer_code_start = 0;
	$item_code_width = 0; $manufacturer_code_width = 0;
	if ($show_item_code && $show_manufacturer_code) {
		$item_name_width = 140;	
		$item_code_start = 255; $manufacturer_code_start = 405;
		$item_code_width = 150; $manufacturer_code_width = 150;
	} elseif ($show_item_code == 1) {
		$item_name_width = 240;	
		$item_code_start = 355; $item_code_width = 200; 
	} elseif ($show_item_code == 2) {
		$item_name_width = 190;	
		$item_code_start = 305; $item_code_width = 250; 
	} elseif ($show_manufacturer_code == 1) {
		$item_name_width = 240;	
		$manufacturer_code_start = 355; $manufacturer_code_width = 200;
	} elseif ($show_manufacturer_code == 2) {
		$item_name_width = 190;	
		$manufacturer_code_start = 305; $manufacturer_code_width = 250;
	}

	$ids = get_param("ids");
	$order_id = get_param("order_id");
	if ($order_id) {
		$ids = $order_id;
	}

	$r = new VA_Record($table_prefix . "orders");
	$r->add_where("order_id", INTEGER);

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
	$r->add_textbox("currency_rate", TEXT);
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
	$r->add_textbox("shipping_type_desc", TEXT);
	
	// General PDF information
	$pdf_library = isset($packing["pdf_lib"]) ? $packing["pdf_lib"] : 1;
	if ($pdf_library == 2) {
		$pdf = new VA_PDFLib();
	} else {
		$pdf = new VA_PDF();
	}
	$pdf->set_creator("admin_packing_pdf.php");
	$pdf->set_author("ViArt Ltd");
	$pdf->set_title(PACKING_SLIP_NO_MSG . $ids);
	$pdf->set_font_encoding(CHARSET);
	$page_number = 0;

	$order_ids = explode(",", $ids);
	for ($i = 0; $i < sizeof($order_ids); $i++) 
	{
		$id = $order_ids[$i];
		$r->set_value("order_id", $id);
		$r->get_db_values();
  
		$order_currency_code = $r->get_value("currency_code");
		$order_currency_rate = $r->get_value("currency_rate");
		$currency = get_currency($order_currency_code);

		$order_placed_date = $r->get_value("order_placed_date");
		$order_date = va_date($date_show_format, $order_placed_date);
		//$t->set_var("order_date", $order_date);
  
		$r->set_value("company_id", get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("company_id"), INTEGER))));
		$r->set_value("state_id", get_translation(get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("state_id"), INTEGER))));
		$r->set_value("country_id", get_translation(get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("country_id"), INTEGER))));
		$r->set_value("delivery_company_id", get_translation(get_db_value("SELECT company_name FROM " . $table_prefix . "companies WHERE company_id=" . $db->tosql($r->get_value("delivery_company_id"), INTEGER))));
		$r->set_value("delivery_state_id", get_translation(get_db_value("SELECT state_name FROM " . $table_prefix . "states WHERE state_id=" . $db->tosql($r->get_value("delivery_state_id"), INTEGER))));
		$r->set_value("delivery_country_id", get_translation(get_db_value("SELECT country_name FROM " . $table_prefix . "countries WHERE country_id=" . $db->tosql($r->get_value("delivery_country_id"), INTEGER))));
		$r->set_value("cc_type", get_translation(get_db_value("SELECT credit_card_name FROM " . $table_prefix . "credit_cards WHERE credit_card_id=" . $db->tosql($r->get_value("cc_type"), INTEGER))));

		// parse properties
		$orders_properties = array(); $cart_properties = array(); $personal_properties = array();
		$delivery_properties = array(); $payment_properties = array();
		$properties_total = 0; $properties_taxable = 0;
		$sql  = " SELECT op.property_id, op.property_type, op.property_name, op.property_value, ";
		$sql .= " op.property_value, op.property_price, op.property_points_amount, op.tax_free, ocp.control_type ";
		$sql .= " FROM (" . $table_prefix . "orders_properties op ";
		$sql .= " INNER JOIN " . $table_prefix . "order_custom_properties ocp ON op.property_id=ocp.property_id)";
		$sql .= " WHERE op.order_id=" . $db->tosql($id, INTEGER);
		$sql .= " ORDER BY op.property_order, op.property_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$property_id = $db->f("property_id");
			$property_type = $db->f("property_type");
			$property_name = get_translation($db->f("property_name"));
			$property_value = get_translation($db->f("property_value"));
			$property_price = $db->f("property_price");
			$property_points_amount = $db->f("property_points_amount");
			$property_tax_free = $db->f("tax_free");
			$control_type = $db->f("control_type");
			$properties_total += $property_price;
			if ($property_tax_free != 1) {
				$properties_taxable += $property_price;
			}
  
			// check value description
			if (($control_type == "CHECKBOXLIST" ||  $control_type == "RADIOBUTTON" || $control_type == "LISTBOX") && is_numeric($property_value)) {
				$sql  = " SELECT property_value FROM " . $table_prefix . "order_custom_values ";
				$sql .= " WHERE property_value_id=" . $dbi->tosql($property_value, INTEGER);
				$dbi->query($sql);
				if ($dbi->next_record()) {
					$property_value = get_translation($dbi->f("property_value"));
				}
			}
			if (isset($orders_properties[$property_id])) {
				$orders_properties[$property_id]["value"] .= "; " . $property_value;
				$orders_properties[$property_id]["price"] += $property_price;
				$orders_properties[$property_id]["points_amount"] += $property_points_amount;
			} else {
				$orders_properties[$property_id] = array(
					"type" => $property_type, "name" => $property_name, "value" => $property_value, 
					"price" => $property_price, "points_amount" => $property_points_amount, "tax_free" => $property_tax_free,
				);
			}

			// save data by arrays
			if ($property_type == 1) {
			  $cart_properties[$property_id] = $orders_properties[$property_id];
			} elseif ($property_type == 2) {
				$personal_properties[$property_id] = $orders_properties[$property_id];
			} elseif ($property_type == 3) {
				$delivery_properties[$property_id] = $orders_properties[$property_id];
			} elseif ($property_type == 4) {
				$payment_properties[$property_id] = $orders_properties[$property_id];
			}
		}
  
  
		$packing_slips = array();
		$sql  = " SELECT oe.order_items FROM (" . $table_prefix . "orders_events oe ";
		$sql .= " LEFT JOIN " . $table_prefix . "order_statuses os ON os.status_id=oe.status_id) ";
		$sql .= " WHERE order_id=" . $db->tosql($id, INTEGER);
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
			begin_new_page();		
    
			set_packing_header();
			set_user_info();
			set_table_header();
  
			$order_items = $packing_slips[$ps];
			$sql  = " SELECT * FROM " . $table_prefix . "orders_items ";
			$sql .= " WHERE order_id=" . $db->tosql($id, INTEGER);
			if (strlen($order_items)) {
				$sql .= " AND order_item_id IN (" . $db->tosql($order_items, TEXT, false) . ") ";
			}
			$db->query($sql);
			while ($db->next_record()) {
				if ($height_position < 140) {
					$pdf->end_page();
					begin_new_page();		
					set_table_header();
				}
  
				$order_item_id = $db->f("order_item_id");
				$quantity = $db->f("quantity");
				$item_name = strip_tags(get_translation($db->f("item_name")));
				$item_properties = $db->f("item_properties");
				$item_code = $db->f("item_code");
				$manufacturer_code = $db->f("manufacturer_code");
  
				$pdf->setfont ("helvetica", "", 20);
				$fontsize = 10; 
  
				$item_height = $pdf->show_xy($item_name, 105, $height_position - 2, $item_name_width, 0);
				// add additional indent after each product
				$item_height += 6;
				$row_height = $item_height;
  
				$item_code_image_height = 0; $manufacturer_code_image_height = 0;
				$item_code_height = 0; $manufacturer_code_height = 0;
				$pdf->show_xy($quantity, 40, $height_position - 2, 60, 0, "center");
				if (strlen($item_code)) {
					if ($show_item_code == 1) {
						$item_code_height = $pdf->show_xy($item_code, $item_code_start, $height_position - 2, $item_code_width, 0, "center");
						$item_code_height += 6;
					} elseif ($show_item_code == 2) {
						$image_type = "png";
						if ($tmp_dir) {
							$tmp_dir = get_setting_value($settings, "tmp_dir", "");
							$item_code_image = $tmp_dir . "tmp_" . md5(uniqid(rand(), true)) . "." . $image_type;
							save_barcode ($item_code_image, $item_code, $image_type, "code128");
							$tmp_images[] = $item_code_image;
						} else {
							$item_code_image = $settings["site_url"] . "barcode_image.php?text=" . $item_code;
						}
		        $image_size = @GetImageSize($item_code_image);
						if (is_array($image_size)) {
							$image_width = $image_size[0];
							$item_code_image_height = $image_size[1];
						} else {
							$image_width = 100;
							$item_code_image_height = 100;
						}
						$item_code_image_height += 10; // additional pixels for better positioning
						if ($item_code_width > $image_width) {
							$image_shift = round(($item_code_width - $image_width) / 2);
						}	else {
							$image_shift = 0;
						}
					  $pdf->place_image($item_code_image, $item_code_start + $image_shift, $height_position - $item_code_image_height + 5, $image_type);
					}
				}
				if (strlen($manufacturer_code)) {
					if ($show_manufacturer_code == 1) {
						$manufacturer_code_height  = $pdf->show_xy($manufacturer_code, $manufacturer_code_start, $height_position - 2, $manufacturer_code_width, 0, "center");
						$manufacturer_code_height += 6;
					} elseif ($show_manufacturer_code == 2) {
						$image_type = "png";
						if ($tmp_dir) {
							$tmp_dir = get_setting_value($settings, "tmp_dir", "");
							$manufacturer_code_image = $tmp_dir . "tmp_" . md5(uniqid(rand(), true)) . "." . $image_type;
							save_barcode ($manufacturer_code_image, $manufacturer_code, $image_type, "code128");
							$tmp_images[] = $manufacturer_code_image;
						} else {
							$manufacturer_code_image = $settings["site_url"] . "barcode_image.php?text=" . $manufacturer_code;
						}
		        $image_size = @GetImageSize($manufacturer_code_image);
						if (is_array($image_size)) {
							$image_width = $image_size[0];
							$manufacturer_code_image_height = $image_size[1];
						} else {
							$image_width = 100;
							$manufacturer_code_image_height = 100;
						}
						$manufacturer_code_image_height += 10; // additional pixels for better positioning
						if ($manufacturer_code_width > $image_width) {
							$image_shift = round(($manufacturer_code_width - $image_width) / 2);
						}	else {
							$image_shift = 0;
						}
					  $pdf->place_image($manufacturer_code_image, $manufacturer_code_start + $image_shift, $height_position - $manufacturer_code_image_height + 5, $image_type);
					}
				}

  
				$pdf->setfont ("helvetica", "", 20);
				$properties_height = 0;
				$sql  = " SELECT property_name, property_value FROM " . $table_prefix . "orders_items_properties ";
				$sql .= " WHERE order_item_id=" . $db->tosql($order_item_id, INTEGER);
				$dbi->query($sql);
				while ($dbi->next_record()) {
					$property_name = strip_tags(get_translation($dbi->f("property_name")));
					$property_value = strip_tags(get_translation($dbi->f("property_value")));
					$property_line = $property_name . ": " . $property_value;
  
					$property_height = $pdf->show_xy($property_line, 115, $height_position - $item_height - $properties_height + 2, 280, 0);
					$properties_height += $property_height;
				}
				if ($properties_height > 0) {
					$row_height += ($properties_height + 2);
				}

				if ($item_code_image_height > $row_height) {
					$row_height = $item_code_image_height;
				}
				if ($manufacturer_code_image_height > $row_height) {
					$row_height = $manufacturer_code_image_height;
				}
				if ($item_code_height > $row_height) {
					$row_height = $item_code_height;
				}
				if ($manufacturer_code_height > $row_height) {
					$row_height = $manufacturer_code_height;
				}

				$height_position -= $row_height;
  
				/*
$pdf->setlinewidth(1.0);
				$pdf->rect(40, $height_position, 515, $row_height);
				$pdf->line(100, $height_position, 100, $height_position + $row_height);
				if ($show_item_code && $show_manufacturer_code) {
					$pdf->line($item_code_start, $height_position, $item_code_start, $height_position + $row_height);
					$pdf->line($manufacturer_code_start, $height_position, $manufacturer_code_start, $height_position + $row_height);
				} elseif ($show_item_code) {
					$pdf->line($item_code_start, $height_position, $item_code_start, $height_position + $row_height);
				} elseif ($show_manufacturer_code) {
					$pdf->line($manufacturer_code_start, $height_position, $manufacturer_code_start, $height_position + $row_height);
				}
*/
			}
  
			set_packing_footer();
			$pdf->end_page();
		}
	}

	$buffer = $pdf->get_buffer();
	$length = strlen($buffer);

	$pdf_filename = "packing_slip_" . str_replace(",", "_", $ids) . ".pdf";
	header("Pragma: private");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header("Content-Type: application/octet-stream");
	header("Content-Length: " . $length); 
	header("Content-Disposition: attachment; filename=" . $pdf_filename); 
	header("Content-Transfer-Encoding: binary"); 

	echo $buffer;

	// clearing temporary barcode images
	for ($t = 0; $t < sizeof($tmp_images); $t++) {
		unlink($tmp_images[$t]);
	}

	function begin_new_page()
	{
		global $pdf, $page_number, $height_position;
	
		$page_number++;
		$pdf->begin_page(595, 842);
		$height_position = 800;
	
		$pdf->setfont ("helvetica", "", 8);
		if ($page_number > 1) {
			//$pdf->show_xy("- " . $page_number . " -", 40, 20, 555, 0, "center");
		}
	}
	
	function set_table_header()
	{
		global $pdf, $height_position;
		global $helvetica, $helvetica_bold;
		global $item_name_width;
		global $show_item_code, $item_code_start, $item_code_width;
		global $show_manufacturer_code, $manufacturer_code_start, $manufacturer_code_width;
	
		$pdf->setlinewidth(1.0);
		
		$height_position -= 50;
	
		/*
$pdf->rect(40, $height_position - 36, 515, 36);
		$pdf->line(100, $height_position - 36, 100, $height_position);
		if ($show_item_code) {
			$pdf->line($item_code_start, $height_position - 36, $item_code_start, $height_position);
		}
		if ($show_manufacturer_code) {
			$pdf->line($manufacturer_code_start, $height_position - 36, $manufacturer_code_start, $height_position);
		}
*/
		$pdf->setfont ("helvetica", "B", 20);
		$pdf->show_xy (QTY_MSG, 40, $height_position - 6, 60, 0, "center");
		$pdf->show_xy (DESCRIPTION_MSG, 100, $height_position - 6, $item_name_width, 0, "center");
		if ($show_item_code) {
			$pdf->show_xy (PROD_CODE_MSG, $item_code_start + 10, $height_position - 6, $item_code_width - 20, 0, "center");
		}
		if ($show_manufacturer_code) {
			$pdf->show_xy (MANUFACTURER_CODE_MSG, $manufacturer_code_start + 10, $height_position - 6, $manufacturer_code_width - 20, 0, "center");
		}
	
		$height_position -= 36;
	}
	
	
	function set_user_info()
	{
		global $pdf, $height_position;
		global $r, $parameters, $personal_number, $delivery_number;
		global $packing, $helvetica, $helvetica_bold, $order_date;
		global $personal_properties, $delivery_properties, $currency;
	
		$pdf->setfont ("helvetica", "BU", 20);
		$height_position += 35;
		$line_height = 22;
		// $pdf->show_xy(INVOICE_TO_MSG.":", 40, $height_position, 250, 0, "left");
		if ($delivery_number > 0) {
			$pdf->show_xy(DELIVERY_TO_MSG.":", 40, $height_position, 250, 0, "left");
		}
	
		$personal_height = $height_position;
		$pdf->setfont("helvetica", "", 20);
	
		$name = "";
/*
		if ($r->parameters["name"][SHOW]) {
			$name = $r->get_value("name");
		}
		if ($r->parameters["first_name"][SHOW] && $r->get_value("first_name")) {
			if ($name) { $name .= " "; }
			$name .= $r->get_value("first_name");
		}
		if ($r->parameters["last_name"][SHOW] && $r->get_value("last_name")) {
			if ($name) { $name .= " "; }
			$name .= $r->get_value("last_name");
		}
	
		if (strlen($name)) {
			$personal_height -= 12;
			$pdf->show_xy($name, 40, $personal_height, 250, 0, "left");
		}
	
		if ($r->parameters["company_id"][SHOW] && $r->get_value("company_id")) {
			$personal_height -= 12;
			$pdf->show_xy($r->get_value("company_id"), 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["company_name"][SHOW] && $r->get_value("company_name")) {
			$personal_height -= 12;
			$pdf->show_xy($r->get_value("company_name"), 40, $personal_height, 250, 0, "left");
		}
	
		if ($r->parameters["address1"][SHOW] && $r->get_value("address1")) {
			$personal_height -= 12;
			$pdf->show_xy($r->get_value("address1"), 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["address2"][SHOW] && $r->get_value("address2")) {
			$personal_height -= 12;
			$pdf->show_xy($r->get_value("address2"), 40, $personal_height, 250, 0, "left");
		}
		$city = ""; $address_line = "";
		if ($r->parameters["city"][SHOW]) {
			$city = $r->get_value("city");
		}
		if ($r->parameters["province"][SHOW]) {
			$address_line = $r->get_value("province");
		}
		if ($r->parameters["state_id"][SHOW] && $r->get_value("state_id")) {
			if ($address_line) { $address_line .= " "; }
			$address_line .= $r->get_value("state_id");
		}
		if ($r->parameters["zip"][SHOW] && $r->get_value("zip")) {
			if ($address_line) { $address_line .= " "; }
			$address_line .= $r->get_value("zip");
		}
		if (strlen($city) && strlen($address_line)) {
			$address_line = $city . ", " . $address_line;
		} elseif (strlen($city)) {
			$address_line = $city;
		}
	
		if (strlen($address_line)) {
			$personal_height -= 12;
			$pdf->show_xy($address_line, 40, $personal_height, 250, 0, "left");
		}
	
		if ($r->parameters["country_id"][SHOW] && $r->get_value("country_id")) {
			$personal_height -= 12;
			$pdf->show_xy($r->get_value("country_id"), 40, $personal_height, 250, 0, "left");
		}
	
		if ($r->parameters["phone"][SHOW] && !$r->is_empty("phone")) {
			$personal_height -= 12;
			$pdf->show_xy(PHONE_FIELD.": ".$r->get_value("phone"), 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["daytime_phone"][HIDE] && !$r->is_empty("daytime_phone")) {
			$personal_height -= 12;
			$pdf->show_xy(DAYTIME_PHONE_FIELD.": ".$r->get_value("daytime_phone"), 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["evening_phone"][SHOW] && !$r->is_empty("evening_phone")) {
			$personal_height -= 12;
			$pdf->show_xy(EVENING_PHONE_FIELD.": ".$r->get_value("evening_phone"), 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["cell_phone"][SHOW] && !$r->is_empty("cell_phone")) {
			$personal_height -= 12;
			$pdf->show_xy(CELL_PHONE_FIELD.": ".$r->get_value("cell_phone"), 40, $personal_height, 250, 0, "left");
		}
		if ($r->parameters["fax"][SHOW] && !$r->is_empty("fax")) {
			$personal_height -= 12;
			$pdf->show_xy(FAX_FIELD.": ".$r->get_value("fax"), 40, $personal_height, 250, 0, "left");
		}
	
		if ($r->parameters["email"][HIDE] && strlen($r->get_value("email"))) {
			$personal_height -= 12;
			$pdf->show_xy(EMAIL_FIELD.": " . $r->get_value("email"), 40, $personal_height, 250, 0, "left");
		}
	
		foreach ($personal_properties as $property_id => $property_values) {
			$property_price = $property_values["price"];
			$property_tax = get_tax_amount("", 0, $property_price, $property_values["tax_free"], $property_tax_percent);
			if (floatval($property_price) != 0.0) {
				$property_price_text = " " . currency_format($property_price, $currency, $property_tax);
			} else {
				$property_price_text = "";
			}
			$property_height = $pdf->show_xy($property_values["name"] . ": " . $property_values["value"] . $property_price_text, 40, $personal_height - 12, 250, 0, "left");
			$personal_height -= $property_height;
		}
*/
	
		$delivery_height = $height_position;
	
		$delivery_name = "";
		if ($r->parameters["delivery_name"][SHOW]) {
			$delivery_name = $r->get_value("delivery_name");
		}
		if ($r->parameters["delivery_first_name"][SHOW] && $r->get_value("delivery_first_name")) {
			if ($delivery_name) { $delivery_name .= " "; }
			$delivery_name .= $r->get_value("delivery_first_name");
		}
		if ($r->parameters["delivery_last_name"][SHOW] && $r->get_value("delivery_last_name")) {
			if ($delivery_name) { $delivery_name .= " "; }
			$delivery_name .= $r->get_value("delivery_last_name");
		}
	
		if (strlen($delivery_name)) {
			$delivery_height -= $line_height;
			$pdf->show_xy($delivery_name, 40, $delivery_height, 500, 0, "left");
		}
	
		if ($r->parameters["delivery_company_id"][SHOW] && $r->get_value("delivery_company_id")) {
			$delivery_height -= $line_height;
			$pdf->show_xy($r->get_value("delivery_company_id"), 40, $delivery_height, 500, 0, "left");
		}
		if ($r->parameters["delivery_company_name"][SHOW] && $r->get_value("delivery_company_name")) {
			$delivery_height -= $line_height;
			$pdf->show_xy($r->get_value("delivery_company_name"), 40, $delivery_height, 500, 0, "left");
		}
	
		if ($r->parameters["delivery_address1"][SHOW] && $r->get_value("delivery_address1")) {
			$delivery_height -= $line_height;
			$pdf->show_xy($r->get_value("delivery_address1"), 40, $delivery_height, 500, 0, "left");
		}
		if ($r->parameters["delivery_address2"][SHOW] && $r->get_value("delivery_address2")) {
			$delivery_height -= $line_height;
			$pdf->show_xy($r->get_value("delivery_address2"), 40, $delivery_height, 500, 0, "left");
		}
		$delivery_city = ""; $delivery_address = "";
		if ($r->parameters["delivery_city"][SHOW]) {
			$delivery_city = $r->get_value("delivery_city");
		}
		if ($r->parameters["delivery_province"][SHOW]) {
			$delivery_address = $r->get_value("delivery_province");
		}
		if ($r->parameters["delivery_state_id"][SHOW] && $r->get_value("delivery_state_id")) {
			if ($delivery_address) { $delivery_address .= " "; }
			$delivery_address .= $r->get_value("delivery_state_id");
		}
		if ($r->parameters["delivery_zip"][SHOW] && $r->get_value("delivery_zip")) {
			if ($delivery_address) { $delivery_address .= " "; }
			$delivery_address .= $r->get_value("delivery_zip");
		}
		if (strlen($delivery_city) && strlen($delivery_address)) {
			$delivery_address = $delivery_city . ", " . $delivery_address;
		} elseif (strlen($delivery_city)) {
			$delivery_address = $delivery_city;
		}
	
		if (strlen($delivery_address)) {
			$delivery_height -= $line_height;
			$pdf->show_xy($delivery_address, 40, $delivery_height, 500, 0, "left");
		}
	
		if ($r->parameters["delivery_country_id"][SHOW] && $r->get_value("delivery_country_id")) {
			$delivery_height -= $line_height;
			$pdf->show_xy($r->get_value("delivery_country_id"), 40, $delivery_height, 500, 0, "left");
		}
	
		if ($r->parameters["delivery_phone"][SHOW] && !$r->is_empty("delivery_phone")) {
			$delivery_height -= $line_height;
			$pdf->show_xy(PHONE_FIELD.": ".$r->get_value("delivery_phone"), 40, $delivery_height, 500, 0, "left");
		}
		if ($r->parameters["delivery_daytime_phone"][SHOW] && !$r->is_empty("delivery_daytime_phone")) {
			$delivery_height -= $line_height;
			$pdf->show_xy(DAYTIME_PHONE_FIELD.": ".$r->get_value("delivery_daytime_phone"), 40, $delivery_height, 500, 0, "left");
		}
		if ($r->parameters["delivery_evening_phone"][SHOW] && !$r->is_empty("delivery_evening_phone")) {
			$delivery_height -= $line_height;
			$pdf->show_xy(EVENING_PHONE_FIELD.": ".$r->get_value("delivery_evening_phone"), 40, $delivery_height, 500, 0, "left");
		}
		if ($r->parameters["delivery_cell_phone"][SHOW] && !$r->is_empty("delivery_cell_phone")) {
			$delivery_height -= $line_height;
			$pdf->show_xy(CELL_PHONE_FIELD.": ".$r->get_value("delivery_cell_phone"), 40, $delivery_height, 500, 0, "left");
		}
		if ($r->parameters["delivery_fax"][SHOW] && !$r->is_empty("delivery_fax")) {
			$delivery_height -= $line_height;
			$pdf->show_xy(FAX_FIELD.": ".$r->get_value("delivery_fax"), 40, $delivery_height, 500, 0, "left");
		}
	
		if ($r->parameters["delivery_email"][SHOW] && strlen($r->get_value("delivery_email"))) {
			$delivery_height -= $line_height;
			$pdf->show_xy(EMAIL_FIELD.": " . $r->get_value("delivery_email"), 40, $delivery_height, 500, 0, "left");
		}
		// Egghead Ventures Add
		$delivery_height -= $line_height;
		$pdf->show_xy("Shipping Method: " . strip_tags($r->get_value("shipping_type_desc")), 40, $delivery_height, 600, 0, "left");
		// End
	
		foreach ($delivery_properties as $property_id => $property_values) {
			$property_price = $property_values["price"];
			$property_tax = get_tax_amount("", 0, $property_price, $property_values["tax_free"], $property_tax_percent);
			if (floatval($property_price) != 0.0) {
				$property_price_text = " " . currency_format($property_price, $currency, $property_tax);
			} else {
				$property_price_text = "";
			}
			$property_height = $pdf->show_xy($property_values["name"].": " . $property_values["value"] . $property_price_text, 300, $delivery_height - $line_height, 250, 0, "left");
			$delivery_height -= $property_height;
		}
	
		if ($personal_height > $delivery_height) {
			$height_position = $delivery_height;
		} else {
			$height_position = $personal_height;
		}
		$height_position -= $line_height;
	
	}
	
	function set_packing_header()
	{
		global $pdf, $height_position;
		global $packing, $helvetica, $helvetica_bold, $order_date, $id, $r;
		
		$order_id = $r->get_value("order_id");
		$invoice_number = $r->get_value("invoice_number");
		if (!$invoice_number) { $invoice_number = $order_id; }
	
		$image_height = 0;
		$start_position = $height_position;
		/*
if (isset($packing["packing_logo"]) && strlen($packing["packing_logo"])) {
			$image_path = $packing["packing_logo"];
			$image_size = @GetImageSize($image_path);
			$image_width = $image_size[0];
			$image_height = $image_size[1];
			if ($image_width > 0 && $image_height > 0) {
				if (preg_match("/((\.jpeg)|(\.jpg))$/i", $image_path)) {
					$image_type = "jpeg";
				} elseif (preg_match("/(\.gif)$/i", $image_path)) {
					$image_type = "gif";
				} elseif (preg_match("/((\.tif)|(\.tiff))$/i", $image_path)) {
					$image_type = "tiff";
				} elseif (preg_match("/(\.png)$/i", $image_path)) {
					$image_type = "png";
				}
			  $pdf->place_image($image_path, 555 - $image_width, $height_position - $image_height - 30, $image_type);
			}
		}
*/
	
		if (isset($packing["packing_header"])) {
			$packing_header = strip_tags($packing["packing_header"]);
			if (strlen($packing_header)) {
				$pdf->setfont("helvetica", "", 20);
				$header_lines = explode("\n", $packing_header);
				for ($i = 0; $i < sizeof($header_lines); $i++) {
					$header_line = $header_lines[$i];
					$line_height = $pdf->show_xy($header_line, 40, $height_position, 200, 0, "left");
					$height_position -= ($line_height + 2);
				}
			}
		}
	
		$height_position -= 50;
		$pdf->setfont("helvetica", "B", 20);
		$pdf->show_xy(INVOICE_DATE_MSG . ":", 300, $height_position + 140, 140, 0, "left");
		$pdf->setfont("helvetica", "", 20);
		$pdf->show_xy($order_date, 430, $height_position + 140, 300, 0, "left");
	
		$height_position -= $line_height + 10;
		$pdf->setfont("helvetica", "B", 35);
		$pdf->show_xy("Invoice:", 300, $height_position + 140, 140, 0, "left");
		$pdf->setfont("helvetica", "B", 35);
		$pdf->show_xy($invoice_number, 450, $height_position + 140, 300, 0, "left");
	
		if ($height_position > ($start_position - $image_height)) {
			$height_position = $start_position - $image_height;
		}
	
	}
	
	function set_packing_footer()
	{
		global $pdf, $height_position;
		global $packing, $helvetica, $helvetica_bold, $order_date, $id;
	
		/*
$pdf->setfont("helvetica", "", 8);
		if (isset($packing["packing_footer"])) {
			$packing_footer = strip_tags($packing["packing_footer"]);
			if (strlen($packing_footer)) {
				$footer_lines = explode("\n", $packing_footer);
				$height_position = 40 + sizeof($footer_lines) * 10;
				for ($i = 0; $i < sizeof($footer_lines); $i++) {
					$height_position -= 10;
					$footer_line = $footer_lines[$i];
					$pdf->show_xy($footer_line, 40, $height_position, 555, 0, "center");
				}
			}
		}
*/
	
	}

?>