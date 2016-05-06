<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_products_copy_properties.php                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("products_categories");

	$sw = trim(get_param("sw"));
	$form_id = get_param("form_id");
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_products_copy_properties.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_products_copy_properties_href", "admin_products_copy_properties.php");
	$t->set_var("admin_href", $admin_site_url . "admin.php");
	$t->set_var("admin_property_href", "admin_property.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_product_href", "admin_product.php");
	$t->set_var("admin_item_type_href", "admin_item_type.php");
	$t->set_var("admin_item_types_href", "admin_item_types.php");
	$t->set_var("admin_properties_href", "admin_properties.php");
	
	$t->set_var("sw", htmlspecialchars($sw));
	$t->set_var("form_id", htmlspecialchars($form_id));
	
	$category_id = get_param("category_id");
	$product_id = get_param("item_id");
	$item_type_id = get_param("item_type_id");
	$options_ids = get_param("options_ids");
	
	$t->set_var("category_id_1", $category_id);
	$t->set_var("product_id", $product_id);
	$t->set_var("item_type_id", $item_type_id);
	$t->set_var("options_ids", $options_ids);
	
	if ($item_type_id > 0) {
		$t->set_var("types_or_products", PRODUCTS_TYPES_MSG);
	} else {
		$t->set_var("types_or_products", PRODUCTS_MSG);
	}
	$operation = get_param("operation");
	
	if ($item_type_id > 0) {
		$t->parse("err_product_types",false);
		$sql  = " SELECT item_type_name FROM " . $table_prefix . "item_types ";
		$sql .= " WHERE item_type_id=" . $db->tosql($item_type_id, INTEGER);
		$db->query($sql);
		if($db->next_record()) {
			$t->set_var("item_type_name", get_translation($db->f("item_type_name")));
		} else {
			die(str_replace("{item_type_id}", $item_id, PROD_TYPE_ID_NO_LONGER_EXISTS_MSG));
		}

		$t->parse("type_path");
	} else {
		$t->parse("err_products",false);
		$sql  = " SELECT item_name FROM " . $table_prefix . "items ";
		$sql .= " WHERE item_id=" . $db->tosql($product_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$t->set_var("item_name", get_translation($db->f("item_name")));
		} else {
			die(str_replace("{item_id}", $product_id, PRODUCT_ID_NO_LONGER_EXISTS_MSG));
		}

		$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
		$tree->show($category_id);

		$t->parse("product_path");
	}

	$sql  = " SELECT item_name FROM " . $table_prefix . "items ";
	$sql .= " WHERE item_id=" . $db->tosql($product_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$t->set_var("item_name_1", get_translation($db->f("item_name")));
	}
	
	if ($operation == "copy") {
		$items_ids = get_param("items_ids");
		
		$options_all = get_param("options_all");
		if (!strlen($options_all)) {
			$options_all = get_param("options_ids");
		}		
		$t->set_var("options_all", $options_all);
		
		options();
		
		$rnd = get_param("rnd");		
		if (get_session("options_rnd") != $rnd) 
		{
			set_session("options_rnd", $rnd);		
			$options_ids = split(",",$options_ids);
			$items_ids = split(",",$items_ids);
			$fields_items_properties = $db->get_fields($table_prefix . "items_properties"); // fields of table items_properties
			$fields_items_properties_values = $db->get_fields($table_prefix . "items_properties_values"); // fields of table items_properties_values
			$dbp = new VA_SQL(); // for insert to items_properties
			$dbp->DBType = $db->DBType;
			$dbp->DBDatabase = $db->DBDatabase;
			$dbp->DBHost = $db->DBHost;
			$dbp->DBPort = $db->DBPort;
			$dbp->DBUser = $db->DBUser;
			$dbp->DBPassword = $db->DBPassword;
			$dbp->DBPersistent = $db->DBPersistent;
			$dbpv = new VA_SQL(); // for insert to items_properties_values
			$dbpv->DBType = $db->DBType;
			$dbpv->DBDatabase = $db->DBDatabase;
			$dbpv->DBHost = $db->DBHost;
			$dbpv->DBPort = $db->DBPort;
			$dbpv->DBUser = $db->DBUser;
			$dbpv->DBPassword = $db->DBPassword;
			$dbpv->DBPersistent = $db->DBPersistent;
			for ($i=0; $i < count($options_ids); $i++) {
				$sql = "SELECT * FROM " . $table_prefix . "items_properties WHERE property_id = ".$db->tosql($options_ids[$i], INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					for ($y = 0; $y < count($items_ids); $y++) {
						$where = "";
						$sql1 = "";
						$sql = "INSERT INTO " . $table_prefix . "items_properties ( ";
						if (strlen($item_type_id)) {
							for ($c = 1; $c < count($fields_items_properties); $c++) {
								if (preg_match("/INT/", $fields_items_properties[$c]["type"]) || preg_match("/DOUBLE/", $fields_items_properties[$c]["type"])) { //  if fields is number
									if ($fields_items_properties[$c]["name"] == "item_type_id") { // for item types
										if (strlen($where)) { $where .= ", "; }
										$where .= $items_ids[$y];
										if (strlen($sql1)) { $sql1 .= ", "; }
										$sql1 .= $fields_items_properties[$c]["name"];
									} else if ($fields_items_properties[$c]["name"] == "item_id") { // for items
										if ($db->f($fields_items_properties[$c]["name"])) {
											if (strlen($where)) { $where .= ", "; }
											$where .= $db->f($fields_items_properties[$c]["name"]);
											if (strlen($sql1)) { $sql1 .= ", "; }
											$sql1 .= $fields_items_properties[$c]["name"];
										} else {
											if (strlen($where)) { $where .= ", "; }
											$where .= 0;
											if (strlen($sql1)) { $sql1 .= ", "; }
											$sql1 .= $fields_items_properties[$c]["name"];
										}
									} else {
										if ($db->f($fields_items_properties[$c]["name"])) {
											if (strlen($where)) { $where .= ", "; }
											$where .= $db->f($fields_items_properties[$c]["name"]);
											if (strlen($sql1)) { $sql1 .= ", "; }
											$sql1 .= $fields_items_properties[$c]["name"];
										} else {
											if (strlen($where)) { $where .= ", "; }
											$where .= "NULL";
											if (strlen($sql1)) { $sql1 .= ", "; }
											$sql1 .= $fields_items_properties[$c]["name"];
										}
									}
								} else { // if fields string or other, without number
									if ($db->f($fields_items_properties[$c]["name"])) {
										if (strlen($where)) {$where .= ", ";}
										$where .= "'".str_replace("'","\'", $db->f($fields_items_properties[$c]["name"]))."'";
										if (strlen($sql1)) {$sql1 .= ", ";}
										$sql1 .= $fields_items_properties[$c]["name"];
									} else {
										if ($fields_items_properties[$c]["name"] == "control_type") { // for item types
											if (strlen($where)) { $where .= ", "; }
											$where .= "''";
											if (strlen($sql1)) { $sql1 .= ", "; }
											$sql1 .= $fields_items_properties[$c]["name"];
										} else {
											if (strlen($where)) { $where .= ", "; }
											$where .= "NULL";
											if (strlen($sql1)) { $sql1 .= ", "; }
											$sql1 .= $fields_items_properties[$c]["name"];
										}
									}
								}
							}
						} else {
							for ($c = 1; $c < count($fields_items_properties); $c++) {
							if (preg_match("/INT/", $fields_items_properties[$c]["type"]) || preg_match("/DOUBLE/", $fields_items_properties[$c]["type"])) {
								if ($fields_items_properties[$c]["name"] == "item_id") {
									if (strlen($where)) { $where .= ", "; }
									$where .= $items_ids[$y];
									if (strlen($sql1)) { $sql1 .= ", "; }
									$sql1 .= $fields_items_properties[$c]["name"];
								} else {
									if ($db->f($fields_items_properties[$c]["name"])) {
										if (strlen($where)) { $where .= ", "; }
										$where .= $db->f($fields_items_properties[$c]["name"]);
										if (strlen($sql1)) { $sql1 .= ", "; }
										$sql1 .= $fields_items_properties[$c]["name"];
									}
								}
							} else {
								if ($db->f($fields_items_properties[$c]["name"])) {
									if (strlen($where)) { $where .= ", "; }
									$where .= "'" . str_replace("'","\'", $db->f($fields_items_properties[$c]["name"]))."'";
									if (strlen($sql1)) { $sql1 .= ", "; }
									$sql1 .= $fields_items_properties[$c]["name"];
								}
							}
						}
						}
						$sql .= $sql1 . " ) VALUES ( " . $where . " )";
						$dbp->query($sql); // insert copy data to items_properties
						if ($db->f("property_type_id") != 2) {
							$dbp->query("SELECT MAX(property_id) FROM " . $table_prefix . "items_properties");
							$dbp->next_record();
							$property_id = $dbp->f(0);
							$sql = "SELECT * FROM " . $table_prefix . "items_properties_values WHERE property_id = ".$db->tosql($options_ids[$i], INTEGER);
							$dbp->query($sql);
							if ($dbp->next_record()) {
								do {
									$where = "";
									$sql1 = "";
									$sql = "INSERT INTO " . $table_prefix . "items_properties_values ( ";
									for ($c = 1; $c < count($fields_items_properties_values); $c++) {
										if (preg_match("/INT/",$fields_items_properties_values[$c]["type"]) || preg_match("/DOUBLE/",$fields_items_properties_values[$c]["type"])) {
											if ($fields_items_properties_values[$c]["name"] == "property_id") {
												if (strlen($where)) { $where .= ", "; }
												$where .= $property_id;
												if (strlen($sql1)) { $sql1 .= ", "; }
												$sql1 .= $fields_items_properties_values[$c]["name"];
											} else {
												if ($dbp->f($fields_items_properties_values[$c]["name"])) {
													if (strlen($where)) { $where .= ", "; }
													$where .= $dbp->f($fields_items_properties_values[$c]["name"]);
													if (strlen($sql1)) { $sql1 .= ", "; }
													$sql1 .= $fields_items_properties_values[$c]["name"];
												}
											}
										} else {
											if ($dbp->f($fields_items_properties_values[$c]["name"])) {
												if (strlen($where)) { $where .= ", "; }
												$where .= "'" . str_replace("'","\'", $dbp->f($fields_items_properties_values[$c]["name"]))."'";
												if (strlen($sql1)) { $sql1 .= ", "; }
												$sql1 .= $fields_items_properties_values[$c]["name"];
											}
										}
									}
									$sql .= $sql1 . " ) VALUES ( " . $where . " )";
									$dbpv->query($sql); // insert data to table items_properties_values
								} while ($dbp->next_record());
							}
						}
					}
				}
			}
			$items_ids = get_param("items_ids");
			if ($item_type_id > 0){
				$t->set_var("message_copy_or_error", SUCCESSFULLY_COPIED_TO_PRODUCT_TYPES_MSG);
				$t->parse("add_item_types_top", false);
			} else {
				$t->set_var("message_copy_or_error", HAS_BEEN_SUCCESSFULLY_COPIED_MSG);
				$t->parse("add_items_top", false);
			}
			$t->parse("products_title_add", false);
			if (strlen($item_type_id)) { // for item types
				$sql = " SELECT DISTINCT item_type_id as item_id, item_type_name as item_name, '' as manufacturer_code, '' as price, '' as item_code, '' as category_id FROM " . $table_prefix . "item_types i";
				$sql.= " WHERE item_type_id IN (".$items_ids.")";
				$sql.= " GROUP BY item_type_id, item_type_name";
				$db->query($sql);
				while ($db->next_record()) {
					$price = $db->f("price");
					$t->set_var("product_copy", $db->f("item_name"));
					$t->set_var("category_id_2", $db->f("category_id"));
					$t->set_var("item_id_type", $db->f("item_id"));
					$t->set_var("manufacturer_code", $db->f("manufacturer_code"));
					$t->set_var("price", $price);
					$t->set_var("item_code", $db->f("item_code"));
					$t->parse("types_add", true);
				}

			} else { // for items
				$sql = " SELECT DISTINCT i.item_id, i.item_name, i.manufacturer_code, i.price, i.item_code, MAX(ic.category_id) as category_id FROM " . $table_prefix . "items i";
				$sql.= " LEFT JOIN " . $table_prefix . "items_categories ic ON i.item_id = ic.item_id";
				$sql.= " WHERE i.item_id IN (".$items_ids.")";
				$sql.= " GROUP BY i.item_id, i.item_name, i.manufacturer_code, i.price, i.item_code";
				$db->query($sql);
				while ($db->next_record()) {
					$price = $db->f("price");
					$t->set_var("product_copy", $db->f("item_name"));
					$t->set_var("category_id_2", $db->f("category_id"));
					$t->set_var("item_id", $db->f("item_id"));
					$t->set_var("manufacturer_code", $db->f("manufacturer_code"));
					$t->set_var("price", currency_format($price));
					$t->set_var("item_code", $db->f("item_code"));
					$t->parse("products_add", true);
				}
			}
			if ($item_type_id > 0){
				$t->set_var("copy_selected_types", "");
				$t->parse("copy_add_types", false);
			} else {
				$t->set_var("copy_selected", "");
				$t->parse("copy_add", false);
			}
			
		} else { // if duplicate
			$sw = "";
			$items_ids = get_param("items_ids");
			if ($item_type_id > 0){
				$t->set_var("message_copy_or_error", ALREADY_COPIED_TO_PRODUCT_TYPES_MSG);
				$t->parse("add_item_types_top", false);
			} else {
				$t->set_var("message_copy_or_error", ALREADY_COPIED_MSG);
				$t->parse("add_items_top", false);
			}
			$t->parse("products_title_add", false);
			
			if (strlen($item_type_id)) { // for item types
				$sql = " SELECT DISTINCT item_type_id as item_id, item_type_name as item_name, '' as manufacturer_code, '' as price, '' as item_code, '' as category_id FROM " . $table_prefix . "item_types i";
				$sql.= " WHERE item_type_id IN (" . $items_ids . ")";
				$sql.= " GROUP BY item_type_id, item_type_name ";
				$db->query($sql);
				while ($db->next_record()) {
					$price = $db->f("price");
					$t->set_var("product_copy", $db->f("item_name"));
					$t->set_var("category_id_2", $db->f("category_id"));
					$t->set_var("item_id_type", $db->f("item_id"));
					$t->set_var("manufacturer_code", $db->f("manufacturer_code"));
					$t->set_var("price", $price);
					$t->set_var("item_code", $db->f("item_code"));
					$t->parse("types_add", true);
				}

			} else { // for items
				$sql = " SELECT DISTINCT i.item_id, i.item_name, i.manufacturer_code, i.price, i.item_code, MAX(ic.category_id) as category_id FROM " . $table_prefix . "items i";
				$sql.= " LEFT JOIN " . $table_prefix . "items_categories ic ON i.item_id = ic.item_id";
				$sql.= " WHERE i.item_id IN (" . $items_ids . ")";
				$sql.= " GROUP BY i.item_id, i.item_name, i.manufacturer_code, i.price, i.item_code ";
				$db->query($sql);
				while ($db->next_record()) {
					$price = $db->f("price");
					$t->set_var("product_copy", $db->f("item_name"));
					$t->set_var("category_id_2", $db->f("category_id"));
					$t->set_var("item_id", $db->f("item_id"));
					$t->set_var("manufacturer_code", $db->f("manufacturer_code"));
					$t->set_var("price", currency_format($price));
					$t->set_var("item_code", $db->f("item_code"));
					$t->parse("products_add", true);
				}
			}
			if ($item_type_id > 0){
				$t->parse("error_copy_types", false);
			} else {
				$t->parse("error_copy", false);
			}
		}
	}
	else 
	{
		$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_products_copy_properties.php");
		$s->set_parameters(false, true, true, false);
		$s->set_sorter(ID_MSG, "sorter_item_id", "1", "item_id");
		if ($item_type_id > 0){
			$s->set_sorter(TYPE_NAME_MSG , "sorter_item_type_name", "2", "item_type_name");
			$t->parse("sorter_item_types_top", false);
		} else {
			$s->set_sorter(PROD_NAME_MSG, "sorter_item_name", "2", "item_name");
			$s->set_sorter(PROD_CODE_MSG, "sorter_item_code", "3", "item_code");
			$s->set_sorter(MANUFACTURER_CODE_MSG, "sorter_manufacturer_code", "4", "manufacturer_code");
			$s->set_sorter(PRICE_MSG, "sorter_price", "5", "price");
			$t->parse("sorter_items_top", false);
		}

		$t->parse("block_search", false);
		$t->parse("onload", false);
		
		$rnd = va_timestamp();
		$t->set_var("rnd", $rnd);
		if ($item_type_id > 0){
			$t->parse("copy_product_types",false);
		} else {
			$t->parse("copy_products",false);
		}
		
		$where = "";
		$sa = array();
		if (strlen($item_type_id)) {
			if ($sw) {
				$sa = split(" ", $sw);
				for($si = 0; $si < sizeof($sa); $si++) {
					if ($where) { $where .= " AND "; }
					else { $where .= " WHERE "; }
					$where .= " (item_type_name LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%')";
				}
			}
		} else {
			if ($sw) {
				$sa = split(" ", $sw);
				for($si = 0; $si < sizeof($sa); $si++) {
					if ($where) { $where .= " AND "; }
					else { $where .= " WHERE "; }
					$where .= " (item_name LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
					$where .= " OR item_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%' ";
					$where .= " OR manufacturer_code LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%')";
				}
			}
		}
		
		options();

		if (strlen($item_type_id)) {
			$sql = " SELECT COUNT(*) FROM " . $table_prefix . "item_types " . $where;
		} else {
			$sql = " SELECT COUNT(*) FROM " . $table_prefix . "items " . $where;
		}
		$db->query($sql);
		$db->next_record();
		$total_records = $db->f(0);
		
		// set up variables for navigator
		$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_products_copy_properties.php");
		$records_per_page = 25;
		$pages_number = 5;
		$page_number = $n->set_navigator("navigator", "page", MOVING, $pages_number, $records_per_page, $total_records, false);
		if (strlen($item_type_id)) {
			$sql  = " SELECT item_type_id as item_id, item_type_name AS item_name, '' AS item_code, '' AS manufacturer_code, '' AS price, '' AS is_sales, '' AS sales_price ";
			$sql .= " FROM " . $table_prefix . "item_types ";
		} else {
			$sql  = " SELECT item_id, item_name, item_code, manufacturer_code, price, is_sales, sales_price ";
			$sql .= " FROM " . $table_prefix . "items ";
		}
		$sql .= $where;
		$sql .= $s->order_by;
		$db->RecordsPerPage = $records_per_page;
		$db->PageNumber = $page_number;
		$db->query($sql);
		if ($db->next_record()) {
			$t->parse("products_sorters", false);
			$i = 0;
			do {
				$i++;
				$item_id = $db->f("item_id");
				$item_name = $db->f("item_name");
				$item_code = $db->f("item_code");
				$manufacturer_code = $db->f("manufacturer_code");
				$price = $db->f("price");
				$is_sales = $db->f("is_sales");
				$sales_price = $db->f("sales_price");
				if ($is_sales && $sales_price > 0) {
					$price = $sales_price;
				}
				$item_name_js = str_replace("'", "\\'", htmlspecialchars($item_name));

				if (is_array($sa)) {
					for ($si = 0; $si < sizeof($sa); $si++) {
						$item_code = preg_replace("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $item_code);					
						$item_name = preg_replace("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $item_name);					
						$manufacturer_code = preg_replace("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $manufacturer_code);
					}
				}

				$t->set_var("onpage_id", $i);
				$t->set_var("item_id", $item_id);
				$t->set_var("item_name", $item_name);
				$t->set_var("item_name_js", $item_name_js);

				$t->set_var("item_code", $item_code);
				$t->set_var("manufacturer_code", $manufacturer_code);

				$t->set_var("price_js", number_format($price, 2, 	".", ""));

				if ($item_type_id > 0){
					$t->set_var("price", $price);
					$t->parse("record_item_types_top", false);
				} else {
					$t->set_var("price_js", number_format($price, 2, 	".", ""));
					$t->parse("record_items_top", false);
				}
				$t->parse("products_copy", true);
			} while ($db->next_record());
			$t->set_var("onpage", $i);
			if ($item_type_id > 0){
				$t->parse("copy_selected_types", false);
			} else {
				$t->parse("copy_selected", false);
			}
		}

		if (strlen($sw)) {
			$found_message = str_replace("{found_records}", $total_records, FOUND_PRODUCTS_MSG);
			$found_message = str_replace("{search_string}", htmlspecialchars($sw), $found_message);
			$t->set_var("found_message", $found_message);
			$t->parse("search_results", false);
		}
	}

	$t->pparse("main");

	function options() 
	{
		global $db, $t, $table_prefix, $language_code, $operation;

		$options_all = get_param("options_all");
		if (!strlen($options_all)) {
			$options_all = get_param("options_ids");
		}
		$t->set_var("options_all", $options_all);

		if ($operation == "copy") {
			$options_all = get_param("options_ids");
		}

		$sql  = " SELECT property_id,property_name,property_order,property_type_id ";
		$sql .= " FROM " . $table_prefix . "items_properties ";
		$sql .= " WHERE property_id IN (" . $options_all .")";
		$db->query($sql);
		if ($db->next_record())
		{
			$i = 0;
			if ($operation == "copy") {
				$t->parse("options_title_add", false);
			} else {
				$t->parse("options_title_copy", false);
			}
			$t->set_var("no_records", "");

			do {
				$i++;
				$property_id = $db->f("property_id");
				$property_type_id = $db->f("property_type_id");
				$property_order = $db->f("property_order");

				if ($property_type_id == "3") {
					$property_type = SUBCOMPONENT_SELECTION_MSG;
				} elseif ($property_type_id == "2") {
					$property_type = SUBCOMPONENT_MSG;
				} else {
					$property_type = OPTION_MSG;
				}

				$t->set_var("onpage_id_opt", $i);
				$t->set_var("property_id", $property_id);
				$t->set_var("property_type", $property_type);
				$t->set_var("property_order", $property_order);
				$t->set_var("property_name", htmlspecialchars(get_translation($db->f("property_name"), $language_code)));

				if ($operation == "copy") {
					$t->parse("options_add", true);
				} else {
					$t->parse("options_copy", true);
				}
			} while($db->next_record());
			$t->set_var("onpage_options", $i);
		}
	}

?>