<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_orders_bom_pdf.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/pdflib.php");
	include_once($root_folder_path . "includes/pdf.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("sales_orders");

	$currency = get_currency();

	// get bom settings
	$bom = array();
	$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE (setting_type='bom' OR setting_type='bom_column') ";
	if ($multisites_version) {
		$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($site_id,INTEGER). ") ";
		$sql .= "ORDER BY site_id DESC ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$bom[$db->f("setting_name")] = $db->f("setting_value");
	}

	$table_width = 515; $start_x = 40;
	$predefined_columns = array(
		"item_name" => array("order" => 1, "width" => 20, "title" => PROD_NAME_MSG),
		"manufacturer_code" => array("order" => 2, "width" => 10, "title" => MANUFACTURER_CODE_MSG),
		"item_code" => array("order" => 3, "width" => 10, "title" => PROD_CODE_MSG),
		"selling_price" => array("order" => 4, "width" => 9, "title" => SELLING_PRICE_MSG),
		"buying_price" => array("order" => 5, "width" => 9, "title" => PROD_BUYING_PRICE_MSG),
		"quantity" => array("order" => 6, "width" => 7, "title" => QTY_MSG),
		"selling_total" => array("order" => 7, "width" => 9, "title" => TOTAL_SELLING_MSG),
		"buying_total" => array("order" => 8, "width" => 10, "title" => TOTAL_BUYING_MSG),
	);
	
	$columns = array(); $orders = array(); $total_width = 0;
	// check predefined columns
	foreach ($predefined_columns as $column_name => $column_info) {
		$show_column = get_setting_value($bom, "show_".$column_name, 0);
		if ($show_column) {
			$column_order = get_setting_value($bom, $column_name."_order", $column_info["order"]);
			$columns[$column_name] = array(
				"width" => $column_info["width"], "title" => $column_info["title"], "order" => $column_order, "column_type" => "db"
			);
			$total_width += $column_info["width"];
			$orders[$column_name] = $column_order;
		}
	}
	// check custom columns
	foreach ($bom as $name => $value) {
		if (preg_match("/^column_id_(\d+)$/", $name, $matches)) {
			$column_id = $matches[1];
			$column_show = get_setting_value($bom, "column_show_".$column_id);
			if ($column_show) {
				$column_name = get_translation(get_setting_value($bom, "column_name_".$column_id));
				$column_order = get_setting_value($bom, "column_order_".$column_id);
				$column_width = get_setting_value($bom, "column_width_".$column_id, 10);
				$source_type = get_setting_value($bom, "source_type_".$column_id);
				$source_name = get_setting_value($bom, "source_name_".$column_id);
				$columns["column_id_".$column_id] = array(
					"width" => $column_width, "title" => $column_name, "order" => $column_order, 
					"column_type" => "custom", 
					"source_type" => $source_type, "source_name" => $source_name
				);
				$total_width += $column_width;
				$orders["column_id_".$column_id] = $column_order;
			}
		}
	}
	array_multisort($orders, $columns);

	// calculate width in pixels and apply start points
	foreach ($columns as $name => $info) {
		$width_px = intval($info["width"] * ($table_width / $total_width));
		$columns[$name]["start"] = $start_x;
		$columns[$name]["width"] = $width_px;
		$start_x += $width_px;
	}

	// additional connection 
	$dbi = new VA_SQL();
	$dbi->DBType      = $db_type;
	$dbi->DBDatabase  = $db_name;
	$dbi->DBUser      = $db_user;
	$dbi->DBPassword  = $db_password;
	$dbi->DBHost      = $db_host;
	$dbi->DBPort      = $db_port;
	$dbi->DBPersistent= $db_persistent;


	$ids = get_param("ids");
	$order_id = get_param("order_id");
	if ($order_id) {
		$ids = $order_id;
	}

	// General PDF information
	$pdf_library = isset($packing["pdf_lib"]) ? $packing["pdf_lib"] : 1;
	if ($pdf_library == 2) {
		$pdf = new VA_PDFLib();
	} else {
		$pdf = new VA_PDF();
	}
	$pdf->set_creator("admin_order_bom.php");
	$pdf->set_author("ViArt Ltd");
	$pdf->set_title(BILL_OF_MATERIALS_MSG . $ids);
	$pdf->set_font_encoding(CHARSET);
	$page_number = 0;

	begin_new_page();		
	set_page_header($ids);

	$sql  = " SELECT oi.item_id, oi.item_name, oi.manufacturer_code, oi.item_code, ";
	$sql .= " oi.price, oi.buying_price, SUM(oi.quantity) AS total_quantity, ";
	$sql .= " m.manufacturer_id, m.manufacturer_name ";
	$sql .= " FROM ((" . $table_prefix . "orders_items oi ";
	$sql .= " LEFT JOIN " . $table_prefix . "items i ON oi.item_id=i.item_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "manufacturers m ON m.manufacturer_id=i.manufacturer_id) ";
	$sql .= " WHERE oi.order_id IN (" . $db->tosql($ids, TEXT, false) . ") ";
	$sql .= " GROUP BY oi.item_id, oi.item_name, oi.manufacturer_code, oi.item_code, ";
	$sql .= " oi.price, oi.buying_price, ";
	$sql .= " m.manufacturer_id, m.manufacturer_name ";
	$sql .= " ORDER BY m.manufacturer_id ";
	$db->query($sql);
	if ($db->next_record()) {
		$manufacturer_id = $db->f("manufacturer_id");
		$manufacturer_name = $db->f("manufacturer_name");
		$last_manufacturer_id = $manufacturer_id;

		set_manufacturer_header($manufacturer_name);
		set_table_header();

		$man_total_quantity = 0; $man_total_selling = 0; $man_total_buying = 0;
		do {
			if ($height_position < 140) {
				$pdf->end_page();
				begin_new_page();		
				set_table_header();
			}
  
			$item_id = $db->f("item_id");
			$price = $db->f("price");
			$buying_price = $db->f("buying_price");
			$quantity = $db->f("total_quantity");
			$item_name = strip_tags(get_translation($db->f("item_name")));
			$item_properties = $db->f("item_properties");
			$item_code = $db->f("item_code");
			$manufacturer_code = $db->f("manufacturer_code");
			$total_selling = $price * $quantity;
			$total_buying = $buying_price * $quantity;

			$manufacturer_id = $db->f("manufacturer_id");
			$manufacturer_name = $db->f("manufacturer_name");
			if ($manufacturer_id != $last_manufacturer_id) {
				set_table_footer($man_total_quantity, $man_total_selling, $man_total_buying);
				set_manufacturer_header($manufacturer_name);
				set_table_header();
				$man_total_quantity = 0; $man_total_selling = 0; $man_total_buying = 0;
			}

			$man_total_quantity += $quantity;
			$man_total_selling += $total_selling; 
			$man_total_buying += $total_buying;
  
			foreach($columns as $name => $column) {
				if ($name == "item_name") {
					$columns["item_name"]["height"] = $pdf->show_xy($item_name, $columns["item_name"]["start"] + 2, $height_position - 2, $columns["item_name"]["width"] - 4, 0);
				} elseif ($name == "item_code") {
					$columns["item_code"]["height"] = $pdf->show_xy ($item_code, $columns["item_code"]["start"] + 2, $height_position - 2, $columns["item_code"]["width"] - 4, 0, "center");
				} elseif ($name == "manufacturer_code") {
					$columns["manufacturer_code"]["height"] = $pdf->show_xy ($manufacturer_code, $columns["manufacturer_code"]["start"] + 2, $height_position - 2, $columns["manufacturer_code"]["width"] - 4, 0, "center");
				} elseif ($name == "selling_price") {
					$columns["selling_price"]["height"] = $pdf->show_xy (currency_format($price), $columns["selling_price"]["start"] + 2, $height_position - 2, $columns["selling_price"]["width"] - 4, 0, "right");
				} elseif ($name == "buying_price") {
					$columns["buying_price"]["height"] = $pdf->show_xy (currency_format($buying_price), $columns["buying_price"]["start"] + 2, $height_position - 2, $columns["buying_price"]["width"] - 4, 0, "right");
				} elseif ($name == "quantity") {
					$columns["quantity"]["height"] = $pdf->show_xy ($quantity, $columns["quantity"]["start"] + 2, $height_position - 2, $columns["quantity"]["width"] - 4, 0, "center");
				} elseif ($name == "selling_total") {
					$columns["selling_total"]["height"] = $pdf->show_xy (currency_format($total_selling), $columns["selling_total"]["start"] + 2, $height_position - 2, $columns["selling_total"]["width"] - 4, 0, "right");
				} elseif ($name == "buying_total") {
					$columns["buying_total"]["height"] = $pdf->show_xy (currency_format($total_buying), $columns["buying_total"]["start"] + 2, $height_position - 2, $columns["buying_total"]["width"] - 4, 0, "right");
				} elseif (preg_match("/^column_id_(\d+)$/", $name)) {
					$source_type = $column["source_type"];
					$source_name = $column["source_name"];
					$column_value = "";
					if ($source_type == 1) {
						$sql  = " SELECT property_description FROM " . $table_prefix . "items_properties ";
						$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
						$sql .= " AND property_name=" . $db->tosql($source_name, TEXT);
					} else {
						$sql  = " SELECT feature_value FROM " . $table_prefix . "features ";
						$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
						$sql .= " AND feature_name=" . $db->tosql($source_name, TEXT);
					}
					$dbi->query($sql);
					if ($dbi->next_record()) {
						$column_value = $dbi->f(0);
					}

					$columns[$name]["height"] = $pdf->show_xy ($column_value, $columns[$name]["start"] + 2, $height_position - 2, $columns[$name]["width"] - 4, 0, "left");
				}
			}

			//$row_height = $columns["item_name"]["height"];
			$row_height = 0;
			foreach($columns as $name => $column) {
				$height = $column["height"];
				if ($height > $row_height) { $row_height = $height; }
			}
			// add additional indent after each item
			$row_height += 6;
  
			$pdf->rect(40, $height_position - $row_height, 515, $row_height);
			foreach($columns as $name => $column) {
				$pdf->line($column["start"], $height_position - $row_height, $column["start"], $height_position);
			}
  
			$height_position -= $row_height;
			$last_manufacturer_id = $manufacturer_id;

		} while ($db->next_record());
		set_table_footer($man_total_quantity, $man_total_selling, $man_total_buying);
	}

	set_page_footer();
	$pdf->end_page();

	$buffer = $pdf->get_buffer();
	$length = strlen($buffer);

	$pdf_filename = "bill_of_materials_" . str_replace(",", "_", $ids) . ".pdf";
	header("Pragma: private");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Cache-Control: private", false);
	header("Content-Type: application/octet-stream");
	header("Content-Length: " . $length); 
	header("Content-Disposition: attachment; filename=" . $pdf_filename); 
	header("Content-Transfer-Encoding: binary"); 

	echo $buffer;

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

	function set_manufacturer_header($manufacturer_name)
	{
		global $pdf, $height_position;
		global $helvetica, $helvetica_bold;
		global $columns;

		$pdf->setfont ("helvetica", "B", 9);
		$manufacturer_info = ITEMS_FOR_MANUFACTURER_MSG . $manufacturer_name;
		$line_height = $pdf->show_xy ($manufacturer_info, 40, $height_position, 515, 0);
		$height_position -= ($line_height + 6);
	}
	
	function set_table_header()
	{
		global $pdf, $height_position;
		global $helvetica, $helvetica_bold;
		global $columns;
	
		$pdf->setlinewidth(1.0);
		
		$pdf->rect(40, $height_position - 24, 515, 24);
		foreach($columns as $name => $column) {
			$pdf->line($column["start"], $height_position - 24, $column["start"], $height_position);
		}

		$pdf->setfont ("helvetica", "", 8);
		foreach($columns as $name => $column) {
			$pdf->show_xy ($column["title"], $column["start"] + 2, $height_position - 2, $column["width"] - 4, 0, "center");
		}
	
		$height_position -= 24;
	}

	function set_table_footer($man_total_quantity, $man_total_selling, $man_total_buying)
	{
		global $pdf, $height_position;
		global $helvetica, $helvetica_bold;
		global $columns;

		$pdf->rect(40, $height_position - 2, 515, 2);
		$height_position -= 2;
		$pdf->rect(40, $height_position - 14, 515, 14);

		if (isset($columns["item_name"])) {
			$pdf->setfont ("helvetica", "B", 8);
			$pdf->show_xy (strtoupper(TOTAL_MSG), $columns["item_name"]["start"] + 2, $height_position - 2, $columns["item_name"]["width"] - 4, 0, "left");
		}
		if (isset($columns["quantity"])) {
			$pdf->line($columns["quantity"]["start"], $height_position - 14, $columns["quantity"]["start"], $height_position);
			$pdf->line($columns["quantity"]["start"] + $columns["quantity"]["width"], $height_position - 14, $columns["quantity"]["start"] + $columns["quantity"]["width"], $height_position);
			$pdf->show_xy ($man_total_quantity, $columns["quantity"]["start"] + 2, $height_position - 2, $columns["quantity"]["width"] - 4, 0, "center");
		}
		if (isset($columns["selling_total"])) {
			$pdf->line($columns["selling_total"]["start"], $height_position - 14, $columns["selling_total"]["start"], $height_position);
			$pdf->line($columns["selling_total"]["start"] + $columns["selling_total"]["width"], $height_position - 14, $columns["selling_total"]["start"] + $columns["selling_total"]["width"], $height_position);
			$pdf->show_xy (currency_format($man_total_selling), $columns["selling_total"]["start"] + 2, $height_position - 2, $columns["selling_total"]["width"] - 4, 0, "right");
		}
		if (isset($columns["buying_total"])) {
			$pdf->line($columns["buying_total"]["start"], $height_position - 14, $columns["buying_total"]["start"], $height_position);
			$pdf->line($columns["buying_total"]["start"] + $columns["buying_total"]["width"], $height_position - 14, $columns["buying_total"]["start"] + $columns["buying_total"]["width"], $height_position);
			$pdf->show_xy (currency_format($man_total_buying), $columns["buying_total"]["start"] + 2, $height_position - 2, $columns["buying_total"]["width"] - 4, 0, "right");
		}

		//indent after table
		$height_position -= 30;
	}

	
	function set_page_header($ids)
	{
		global $pdf, $height_position;
		global $packing, $helvetica, $helvetica_bold, $order_date, $id, $r;

		$pdf->setfont ("helvetica", "B", 10);
		$ids = str_replace(",", ", ", $ids);
		$manufacturer_info = ITEMS_FOR_ORDERS_MSG . $ids;
		$line_height = $pdf->show_xy ($manufacturer_info, 40, $height_position, 515, 0);
		$height_position -= ($line_height + 18);
	}
	
	function set_page_footer()
	{
		global $pdf, $height_position;
		global $packing, $helvetica, $helvetica_bold, $order_date, $id;
	}

?>