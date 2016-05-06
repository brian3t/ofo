<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_products_report.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/shopping_cart.php");
	include_once ($root_folder_path . "messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("products_report");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_products_report.html");
	$t->set_var("admin_products_report_href", "admin_products_report.php");

	$category_id = "";
	// get search parameters
	$operation = get_param("operation");
	$s = trim(get_param("s"));
	$sc = get_param("sc");
	$sl = get_param("sl");
	if(strlen($sc)) { $category_id = $sc; }
	$search = (strlen($s) || strlen($sl) || strlen($category_id)) ? true : false;
	$sa = "";

	// additional connection to get item properties
	$dbs = new VA_SQL();
	$dbs->DBType      = $db_type;
	$dbs->DBDatabase  = $db_name;
	$dbs->DBHost      = $db_host;
	$dbs->DBPort      = $db_port;
	$dbs->DBUser      = $db_user;
	$dbs->DBPassword  = $db_password;
	$dbs->DBPersistent= $db_persistent;	
	$item_properties  = array();
					
	// Top categogy
	if ($operation == "filter") {
		$where = "";
		$join_prep = "";
		$join   = "";
		$group  = "";
		
		if(strlen($category_id)) {
			$where .= (!strlen($where)) ? " WHERE " : " AND ";
			$where .= "ic.category_id = " . $db->tosql($category_id, INTEGER);
		} else {
			$where .= (!strlen($where)) ? " WHERE " : " AND ";
			$where .= "ic.category_id = 0";
		}
		if($s) {
			$sa = split(" ", $s);
			for($si = 0; $si < sizeof($sa); $si++) {
				$where .= (!strlen($where)) ? " WHERE " : " AND ";
				$where .= " (i.item_name LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
				$where .= " OR i.item_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
				$where .= " OR i.manufacturer_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%')";
			}
		}
		
		if(strlen($sl)) {
			if ($sl == 2) {
				$join_prep .= "((";
				$join .= " INNER JOIN " . $table_prefix . "items_properties ip ON ip.item_id = i.item_id)";
				$join .= " INNER JOIN " . $table_prefix . "items_properties_values ipv ON ipv.property_id = ip.property_id)";
				$where .= (!strlen($where)) ? " WHERE " : " AND ";
				$where .= " ipv.stock_level<1 ";
				$group .= (!strlen($group)) ? " GROUP BY " : " , ";
				$group .= " i.item_id ";
			} elseif ($sl == 1) {
				$where .= (!strlen($where)) ? " WHERE " : " AND ";
				$where .= "(i.stock_level>0 OR i.stock_level IS NULL) ";
			} else {
				$where .= (!strlen($where)) ? " WHERE " : " AND ";
				$where .= "i.stock_level<1 ";
			}
		}
				
		$sql  = " SELECT i.item_id, i.item_code, i.manufacturer_code, i.item_name, i.price, i.is_sales, i.sales_price, i.stock_level, c.category_id";
 		$sql .= " FROM ((" . $join_prep . " " . $table_prefix . "items i LEFT JOIN " . $table_prefix . "items_categories ic ON ic.item_id = i.item_id)";
		$sql .= " LEFT JOIN " . $table_prefix . "categories c ON c.category_id = ic.category_id)";
			
		$sql .= $join . $where . $group;
		$db->query($sql);
		if ($db->num_rows()) {
			$item_number = 0;
			$t->set_var("records", "");
			$parse_block = false;
			while($db->next_record()){
				$item_number++;
				if ($item_number % 2) {
					$row_class = "reportRow1";
					$sub_row_class = "subRow1";
				} else {
					$row_class = "reportRow2";
					$sub_row_class = "subRow2";
				}
				$item_id = $db->f("item_id");
				$item_code = $db->f("item_code");
				$manufacturer_code = $db->f("manufacturer_code");
				$item_name = get_translation($db->f("item_name"));
				// higlight search text
				if(is_array($sa)) {
					for($si = 0; $si < sizeof($sa); $si++) {
						$item_code = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $item_code);
						$manufacturer_code = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $manufacturer_code);
						$item_name = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $item_name);
					}
				}
				$price = $db->f("price");
				$is_sales = $db->f("is_sales");
				$sales_price = $db->f("sales_price");
				$price = calculate_price($price, $is_sales, $sales_price);
				$stock_level = $db->f("stock_level");
				if ($stock_level < 0) {
					$stock_level = "<font color=red>" . $stock_level . "</font>";
				}
				$t->set_var("row_class", $row_class);
				$t->set_var("sub_row_class", $sub_row_class);
				build_items_properties($item_id);
				if ($db->f("category_id") == 0){
					$parse_block=true;
					show_items_properties($item_id, $t);
					$t->set_var("product_id",$item_id);
					$t->set_var("item_code",$item_code);
					$t->set_var("manufacturer_code",$manufacturer_code);
					$t->set_var("product_name",$item_name);
					$t->set_var("price", currency_format($price));
					$t->set_var("stock_level",$stock_level);
					$t->parse("records", true);
				}
			}
			if ($parse_block){
				$t->set_var("report_title", TOP_CATEGORY_MSG);
				$t->parse("category", true);
			}
		}
	}

	$categories = array();
	$sql  = " SELECT category_id, parent_category_id, category_name, category_path ";
	$sql .= " FROM " . $table_prefix . "categories ";
	$sql .= " ORDER BY category_order ";
	$db->query($sql);
	while ($db->next_record()) {
		$cur_category_id = $db->f("category_id");
		$category_name = get_translation($db->f("category_name"), $language_code);
		$parent_category_id = $db->f("parent_category_id");
		$category_path = $db->f("category_path");
		$categories[$cur_category_id]["category_name"] = $category_name;
		$categories[$cur_category_id]["category_path"] = $category_path;
		$categories[$parent_category_id]["subs"][] = $cur_category_id;
	}

	$sc_values[] = array("", SEARCH_IN_ALL_MSG);
	$sc_values[] = array("0", "Top");
	if (sizeof($categories) > 0)
	{
		set_sub_categories(0, 0);
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up search form parameters
	$stock_levels =
		array(
			array("", ALL_PRODUCTS_MSG), array(0, OUTOFSTOCK_PRODUCTS_MSG), array(1, INSTOCK_PRODUCTS_MSG),
			array(2, OUT_OF_STOCK_PRODUCT_OPTIONS_MSG)
		);
	set_options($stock_levels, $sl, "sl");

	set_options($sc_values, $sc, "sc");
	$t->set_var("s", $s);

	if($operation == "filter") {
		if (strlen($sl)) {
			$report_info = "<b>".get_array_value($sl, $stock_levels)."</b>";
		} else {
			$report_info = "<b>".$stock_levels[0][1]."</b>";
		}	
		$report_info .= "; ";
		if (strlen($sc)) {
			$report_info .= CATEGORY_MSG . ": ";
			$report_info .= "<b>".get_array_value($sc, $sc_values)."</b>";
		} else {
			$report_info .= "<b>".$sc_values[0][1]."</b>";
		}	
		if (strlen($s)) {
			$report_info .= ";" . KEYWORDS_MSG . ": ";
			$report_info .= "<b>".htmlspecialchars($s)."</b>";
		}
		$t->set_var("report_info", $report_info);
		$t->parse("report_results", false);
	}

	$t->parse("products_links", false);
	$t->pparse("main", false);

function set_sub_categories($top_id, $level)
{
	global $t, $categories, $category_number, $db, $table_prefix, $search, $category_id, $s, $sl;
	global $sc_values, $sa, $operation;

	$subs = $categories[$top_id]["subs"];
	$sub_indent = "";
	for ($i = 0; $i < $level; $i++) {
		$sub_indent .= "&bull;";
	}
	for ($i = 0; $i < sizeof($subs); $i++)
	{
		$category_number++;
		$show_category_id = $subs[$i];
		$category_name  = $categories[$show_category_id]["category_name"];

		$categories_path = "";
		foreach (explode(",", $categories[$show_category_id]["category_path"]) as $key => $value) {
			if ($value != "0"){
				if ($value == "") {$categories_path .= $category_name;}
				else {$categories_path .= $categories[$value]["category_name"]." > ";}
			}
		}
		$sc_values[]=array($show_category_id, $categories_path);

		if ($operation == "filter") {
			$where  = "";
			$join_prep = "";
			$join   = "";
			$group  = "";
			if(strlen($category_id)) {
				$where .= (!strlen($where)) ? " WHERE " : " AND ";
				$where .= "ic.category_id = " . $db->tosql($category_id, INTEGER);
			} else {
				$where .= (!strlen($where)) ? " WHERE " : " AND ";
				$where .= "ic.category_id = " . $db->tosql($show_category_id, INTEGER);
			}
			if($s) {
				$sa = split(" ", $s);
				for($si = 0; $si < sizeof($sa); $si++) {
					$where .= (!strlen($where)) ? " WHERE " : " AND ";
					$where .= " (i.item_name LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
					$where .= " OR i.item_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
					$where .= " OR i.manufacturer_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%')";
				}
			}
			if(strlen($sl)) {
				if ($sl == 2) {
					$join_prep .= "((";
					$join .= " INNER JOIN " . $table_prefix . "items_properties ip ON ip.item_id = i.item_id)";
					$join .= " INNER JOIN " . $table_prefix . "items_properties_values ipv ON ipv.property_id = ip.property_id)";
					$where .= (!strlen($where)) ? " WHERE " : " AND ";
					$where .= " ipv.stock_level<1 ";
					$group .= (!strlen($group)) ? " GROUP BY " : " , ";
					$group .= " i.item_id ";
				} elseif ($sl == 1) {
					$where .= (!strlen($where)) ? " WHERE " : " AND ";
					$where .= "(i.stock_level>0 OR i.stock_level IS NULL) ";
				} else {
					$where .= (!strlen($where)) ? " WHERE " : " AND ";
					$where .= "i.stock_level<1 ";
				}
			}
				
			$sql  = " SELECT i.item_id, i.item_code, i.manufacturer_code, i.item_name, i.price, i.is_sales, i.sales_price, i.stock_level, c.category_id";
 			$sql .= " FROM ((" . $join_prep . " " . $table_prefix . "items i LEFT JOIN " . $table_prefix . "items_categories ic ON ic.item_id = i.item_id)";
			$sql .= " LEFT JOIN " . $table_prefix . "categories c ON c.category_id = ic.category_id)";
			
			$sql .= $join . $where . $group;	
			$db->query($sql);
			if ($db->num_rows()) {
				$t->set_var("records", "");
				$item_number=0;
				$parse_block=false;
				while($db->next_record()){
					$item_number++;
					if ($item_number % 2) {
						$row_class = "reportRow1";
						$sub_row_class = "subRow1";
					} else {
						$row_class = "reportRow2";
						$sub_row_class = "subRow2";
					}
					$item_id = $db->f("item_id");
					$item_code = $db->f("item_code");
					$manufacturer_code = $db->f("manufacturer_code");
					$item_name = get_translation($db->f("item_name"));
					// higlight search text
					if(is_array($sa)) {
						for($si = 0; $si < sizeof($sa); $si++) {
							$item_code = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $item_code);
							$item_name = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $item_name);
							$manufacturer_code = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $manufacturer_code);
						}
					}
	  
					$price = $db->f("price");
					if ($db->f("is_sales")) {$price = $db->f("sales_price");}
					$stock_level = $db->f("stock_level");
					if ($stock_level < 0) {
						$stock_level = "<font color=red>" . $stock_level . "</font>";
					}
					$t->set_var("row_class", $row_class);
					$t->set_var("sub_row_class", $sub_row_class);
					build_items_properties($item_id);
					if ($db->f("category_id") == $show_category_id){
						$parse_block=true;
						show_items_properties($item_id, $t);
						$t->set_var("product_id",$item_id);
						$t->set_var("item_code",$item_code);
						$t->set_var("manufacturer_code",$manufacturer_code);
						$t->set_var("product_name",$item_name);
						$t->set_var("price", currency_format($price));
						$t->set_var("stock_level",$stock_level);
						$t->parse("records", true);
					}
				}
				if ($parse_block){
					$t->set_var("report_title", $categories_path);
					$t->parse("category", true);
				}
			}
		}

		if (isset($categories[$show_category_id]["subs"]) && is_array($categories[$show_category_id]["subs"])) {
			set_sub_categories($show_category_id, $level + 1);
		}

	}
}

function build_items_properties($item_id) {
	global $item_properties, $db, $dbs, $table_prefix;

	if (!isset($item_properties[$item_id])) {
		$sql  = " SELECT property_id, property_name, additional_price, quantity 	 FROM " . $table_prefix . "items_properties ";
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);			
		$item_properties[$item_id] = array();
		$dbs->query($sql);
		while($dbs->next_record()) {
			$ip_property_id       = $dbs->f("property_id");
			$ip_property_name     = get_translation($dbs->f("property_name"));
			$ip_price             = $dbs->f("additional_price");
			$ip_stock_level       = $dbs->f("quantity");
			if ($ip_stock_level < 0) {
				$ip_stock_level = "<font color=red>" . $ip_stock_level . "</font>";
			}
			$item_properties[$item_id][] = array(
					"property_id"      => $ip_property_id,
					"property_name"    => $ip_property_name,
					"price"            => $ip_price,
					"stock_level"      => $ip_stock_level
				);
			}

			for($ip=0, $ipc = count($item_properties[$item_id]); $ip < $ipc; $ip++) {
				$property_id = $item_properties[$item_id][$ip]["property_id"];						
				$sql  = " SELECT item_property_id, property_value, item_code, manufacturer_code, additional_price, stock_level ";
				$sql .= " FROM " . $table_prefix . "items_properties_values ";
				$sql .= " WHERE property_id=" . $db->tosql($property_id, INTEGER);						
				$dbs->query($sql);
				while($dbs->next_record()) {
					$ipv_item_property_id  = $dbs->f("item_property_id");
					$ipv_property_value    = get_translation($dbs->f("property_value"));
					$ipv_item_code         = $dbs->f("item_code");
					$ipv_manufacturer_code = $dbs->f("manufacturer_code");
					$ipv_price             = $dbs->f("additional_price");
					$ipv_stock_level       = $dbs->f("stock_level");
					if ($ipv_stock_level < 0) {
						$ipv_stock_level = "<font color=red>" . $ipv_stock_level . "</font>";
					}
					$item_properties[$item_id][$ip]["values"][] = array(
						"item_property_id"  => $ipv_item_property_id, 
						"property_value"    => $ipv_property_value, 
						"item_code"         => $ipv_item_code,
						"manufacturer_code" => $ipv_manufacturer_code,
						"price"             => $ipv_price,
						"stock_level"       => $ipv_stock_level
				);
			}
		}
	}
}
function show_items_properties($item_id) {
	global $t, $item_properties, $item_number;
	$value_number = 0;

	$t->set_var("properties", "");
	$t->set_var("properties_control", "");
	if (isset($item_properties[$item_id])) {
		$ipc = count($item_properties[$item_id]);
		if ($ipc > 0) {
			for($ip = 0; $ip < $ipc; $ip++) {				
				$value_number++;
				if ($item_number % 2) {
					$property_row_class = ($value_number % 2) ? "subRow1" : "subRow2";
				} else {
					$property_row_class = ($value_number % 2) ? "subRow3" : "subRow4";
				}

				$t->set_var("properties_values", "");
				if (($ip + 1) < $ipc) {
					$t->set_var("property_tree_image", "../images/tree_begin.gif");	
					$t->set_var("property_value_tree_image", "../images/tree_line.gif");	
				} else {
					$t->set_var("property_tree_image", "../images/tree_end.gif");	
					$t->set_var("property_value_tree_image", "../images/tree_space.gif");	
				}

				if (isset($item_properties[$item_id][$ip]["values"])) {
					for($ipv=0, $ipvc = count($item_properties[$item_id][$ip]["values"]); $ipv < $ipvc; $ipv++) {
						$value_number++;
						$ipv_item_property_id  = $item_properties[$item_id][$ip]["values"][$ipv]["item_property_id"];
						$ipv_property_value    = $item_properties[$item_id][$ip]["values"][$ipv]["property_value"];
						$ipv_item_code         = $item_properties[$item_id][$ip]["values"][$ipv]["item_code"];
						$ipv_manufacturer_code = $item_properties[$item_id][$ip]["values"][$ipv]["manufacturer_code"];
						$ipv_price             = $item_properties[$item_id][$ip]["values"][$ipv]["price"];
						$ipv_stock_level       = $item_properties[$item_id][$ip]["values"][$ipv]["stock_level"];
						if ($item_number % 2) {
							$value_row_class = ($value_number % 2) ? "subRow1" : "subRow2";
						} else {
							$value_row_class = ($value_number % 2) ? "subRow3" : "subRow4";
						}
						if (($ipv + 1) < $ipvc) {
							$t->set_var("value_tree_image", "../images/tree_begin.gif");	
						} else {
							$t->set_var("value_tree_image", "../images/tree_end.gif");	
						}
						$t->set_var("value_row_class", $value_row_class);
						$t->set_var("item_property_id", $ipv_item_property_id);
						$t->set_var("property_value", $ipv_property_value);
						$t->set_var("item_code", $ipv_item_code);
						$t->set_var("manufacturer_code", $ipv_manufacturer_code);
						if ($ipv_price > 0) {
							$t->set_var("price", currency_format($ipv_price));
						} else if ($ipv_price < 0) {
							$t->set_var("price", "- " . currency_format(abs($ipv_price)));
						} else {
							$t->set_var("price", "");
						}	
						$t->set_var("stock_level", $ipv_stock_level);								
						$t->parse("properties_values", true);								
					}
				}
				$ip_property_id      = $item_properties[$item_id][$ip]['property_id'];
				$ip_property_name    = $item_properties[$item_id][$ip]['property_name'];
				$ip_price            = $item_properties[$item_id][$ip]['price'];
				$ip_stock_level      = $item_properties[$item_id][$ip]['stock_level'];	

				$t->set_var("property_row_class", $property_row_class);
				$t->set_var("property_id", $ip_property_id);
				$t->set_var("property_name", $ip_property_name);
				if ($ip_price > 0) {
					$t->set_var("price", currency_format($ip_price));
				} else if ($ip_price < 0) {
					$t->set_var("price", "- " . currency_format(abs($ip_price)));
				} else {
					$t->set_var("price", "");
				}	
				$t->set_var("stock_level", $ip_stock_level);	
				$t->parse("properties", true);
			}
			$t->parse("properties_control", true);			
		}
	}
}
?>