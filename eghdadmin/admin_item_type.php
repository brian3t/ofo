<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_item_type.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("product_types");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_item_type.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_item_types_href", "admin_item_types.php");
	$t->set_var("admin_item_type_href", "admin_item_type.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", PROD_TYPE_MSG, CONFIRM_DELETE_MSG));
	
	$operation = get_param("operation");
	$duplicate_options = get_param("duplicate_options");
	$duplicate_features = get_param("duplicate_features");
	
	if ($operation == "duplicate"){
		$item_type_id = get_param("item_type_id");
		$fields_item_types = $db->get_fields($table_prefix . "item_types");
		
		/*for($i=0;$i<count($fields_item_types);$i++){
			$fields_item_type[] = $fields_item_types[$i]["name"];
		}*/
		
		//var_dump($fields_item_type);
		//exit;
		
		// copy type
		
		$sql = "SELECT * FROM ".$table_prefix . "item_types WHERE item_type_id = ".$db->tosql($item_type_id,INTEGER);
		$db->query($sql);
		
		if ($db->next_record()){
			$sql = "INSERT INTO " . $table_prefix . "item_types ( ";
			$sql1 = "";
			$where1 = "";
			for($i=0;$i<count($fields_item_types);$i++){
				if ($fields_item_types[$i]["name"] != "item_type_id"){
					if (strlen($where1)){$where1 .= ",";}
					if (strlen($sql1)){$sql1 .= ",";}
					if (preg_match("/INT/", $fields_item_types[$i]["type"]) || preg_match("/DOUBLE/", $fields_item_types[$i]["type"])) {
						if ($fields_item_types[$i]["name"] == "google_base_type_id") { // for items
							if ($db->f($fields_item_types[$i]["name"])) {
								$where1 .= $db->f($fields_item_types[$i]["name"]);
							} else {
								$where1 .= 0;
							}
						} else {
							if ($db->f($fields_item_types[$i]["name"])) {
								$where1 .= $db->f($fields_item_types[$i]["name"]);
							} else {
								$where1 .= "NULL";
							}
						}
					} else {
						if ($fields_item_types[$i]["name"] == "item_type_name"){
							$where1 .= "'".$db->f($fields_item_types[$i]["name"])." (Duplicate)'";
						} else {
							if ($db->f($fields_item_types[$i]["name"])) {
								$where1 .= "'".str_replace("'","\'", $db->f($fields_item_types[$i]["name"]))."'";
							} else {
								$where1 .= "NULL";
							}
						}
					}
					$sql1 .= $fields_item_types[$i]["name"];
				}
			}
			$sql .= $sql1 . " ) VALUES ( " . $where1 . " )";
			$db->query($sql);
			
			$sql = "SELECT max(item_type_id) FROM ".$table_prefix . "item_types";
			$db->query($sql);
			$db->next_record();
			$item_type_id_new = $db->f(0);
			
			// copy features
			
			$fields_features_default = $db->get_fields($table_prefix . "features_default");
			
			$sql = " SELECT * FROM ".$table_prefix . "features_default WHERE item_type_id = ".$db->tosql($item_type_id,INTEGER);
			$db->query($sql);
			if ($db->next_record() && $duplicate_features){
				$c = 0;
				do {
					for($i=0;$i<count($fields_features_default);$i++){
						$features[$c][$fields_features_default[$i]["name"]] = $db->f($fields_features_default[$i]["name"]);
					}
					$c++;
				} while ($db->next_record());
				
				for($c=0;$c<count($features);$c++){
					$sql = " INSERT INTO ".$table_prefix . "features_default (";
					$sql1 = "";
					$where1 = "";
					for($i=0;$i<count($fields_features_default);$i++){
						//$features[$c][$fields_features_default[$i]["name"]]
					
						if ($fields_features_default[$i]["name"] != "feature_id"){
							if (strlen($where1)){$where1 .= ",";}
							if (strlen($sql1)){$sql1 .= ",";}
							if (preg_match("/INT/", $fields_features_default[$i]["type"]) || preg_match("/DOUBLE/", $fields_features_default[$i]["type"])) {
								if ($fields_features_default[$i]["name"] == "google_base_attribute_id") { // for items
									if ($features[$c][$fields_features_default[$i]["name"]]) {
										$where1 .= $features[$c][$fields_features_default[$i]["name"]];
									} else {
										$where1 .= 0;
									}
								} else if($fields_features_default[$i]["name"] == "item_type_id") {
									$where1 .= $item_type_id_new;
								} else {
									if ($features[$c][$fields_features_default[$i]["name"]]) {
										$where1 .= $features[$c][$fields_features_default[$i]["name"]];
									} else {
										$where1 .= "NULL";
									}
								}
							} else {
								if ($features[$c][$fields_features_default[$i]["name"]]) {
									$where1 .= "'".str_replace("'","\'", $features[$c][$fields_features_default[$i]["name"]])."'";
								} else {
									$where1 .= "NULL";
								}
							}
							$sql1 .= $fields_features_default[$i]["name"];
						}
						
					}
					$sql .= $sql1 . " ) VALUES ( " . $where1 . " )";
					$db->query($sql);
				}
				
			}
			
			// copy subcomponent
			
			$sql = " SELECT property_id FROM ".$table_prefix . "items_properties WHERE item_type_id = ".$db->tosql($item_type_id,INTEGER);
			$db->query($sql);
			$options_ids = "";
			if($db->next_record()){
				do {
					if (strlen($options_ids)){$options_ids .= ",";}
					$options_ids .= $db->f("property_id");
				} while ($db->next_record());
			}
			
			if (strlen($options_ids) && $duplicate_options) {
				$options_ids = split(",",$options_ids);
				$items_ids = split(",",$item_type_id_new);
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
				$j1 = 0;
				for ($i=0; $i < count($options_ids); $i++) {
					$sql = "SELECT * FROM " . $table_prefix . "items_properties WHERE property_id = ".$db->tosql($options_ids[$i], INTEGER);
					$db->query($sql);
					if ($db->next_record()) {
						$properties_ids[$i]["from"] = $options_ids[$i];
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
								$properties_ids[$i]["to"] = $dbp->f(0);
								$sql = "SELECT * FROM " . $table_prefix . "items_properties_values WHERE property_id = ".$db->tosql($options_ids[$i], INTEGER);
								$dbp->query($sql);
								if ($dbp->next_record()) {
									do {
										$properties_values[$j1]["from"] = $dbp->f("item_property_id");
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
										
										$dbpv->query("SELECT MAX(item_property_id) FROM " . $table_prefix . "items_properties_values");
										$dbpv->next_record();
										$properties_values[$j1]["to"] = $dbpv->f(0);
										$j1++;
									} while ($dbp->next_record());
								}
							} else {
								$dbp->query("SELECT MAX(property_id) FROM " . $table_prefix . "items_properties");
								$dbp->next_record();
								$properties_ids[$i]["to"] = $dbp->f(0);
							}
						}
					}
				}
			}
		}
		if (isset($properties_ids) && isset($properties_values)){
			//var_dump($properties_values);
			for ($i=0;$i<count($properties_ids);$i++){//$properties_ids[$i]["to"]
				$sql = " SELECT parent_property_id, parent_value_id FROM ".$table_prefix."items_properties WHERE property_id = ".$properties_ids[$i]["to"];
				$db->query($sql);
				if ($db->next_record()){
					$parent_property_id = $db->f("parent_property_id");
					$parent_value_id = $db->f("parent_value_id");
					if (strlen($parent_property_id)){
						for($j = 0;$j<count($properties_ids);$j++){
							if ($properties_ids[$j]["from"] == $parent_property_id){
								$p_id = $properties_ids[$j]["to"];
								$j = count($properties_ids);
							} else {
								$p_id = NULL;
							}
						}
					} else {
						$p_id = NULL;
					}
					if (strlen($parent_value_id)){
						for($j = 0;$j<count($properties_values);$j++){
							if ($properties_values[$j]["from"] == $parent_value_id){
								$pv_id = $properties_values[$j]["to"];
								$j = count($properties_values);
							} else {
								$pv_id = NULL;
							}
						}
					} else {
						$pv_id = NULL;
					}
					$sql = " UPDATE ".$table_prefix."items_properties SET parent_property_id = ".$db->tosql($p_id,INTEGER)." , parent_value_id = ".$db->tosql($pv_id,INTEGER);
					$sql.= " WHERE property_id = ".$properties_ids[$i]["to"];
					$dbp->query($sql);
				}
			}
		}
		header("Location: " . "admin_item_types.php");
		exit;
	}

	$commission_types = array(
		array("", ""), array(0, NOT_AVAILABLE_MSG), array(1, PERCENT_PER_PROD_FULL_PRICE_MSG),
		array(2, FIXED_AMOUNT_PER_PROD_MSG), array(3, PERCENT_PER_PROD_SELL_PRICE_MSG),
		array(4, PERCENT_PER_PROD_SELL_BUY_MSG)
	);

	$r = new VA_Record($table_prefix . "item_types");
	$r->return_page = "admin_item_types.php";
	
	$r->add_where("item_type_id", INTEGER);

	$r->add_textbox("item_type_name", TEXT, TYPE_NAME_MSG );
	$r->change_property("item_type_name", REQUIRED, true);
	$r->add_checkbox("is_gift_voucher", INTEGER);
	$r->add_checkbox("is_bundle", INTEGER);
	$r->add_checkbox("is_user", INTEGER);
	
	$google_base_product_types = get_db_values ("SELECT type_id, type_name FROM " . $table_prefix . "google_base_types ORDER BY type_name", array(array(-1, NOT_EXPORTED_MSG), array(0, USE_GLOBAL_MSG)));
	$r->add_select("google_base_type_id", INTEGER, $google_base_product_types);
	
	// commissions
	$r->add_select("merchant_fee_type", INTEGER, $commission_types);
	$r->add_textbox("merchant_fee_amount", NUMBER, MERCHANT_FEE_AMOUNT_MSG);
	$r->add_select("affiliate_commission_type", INTEGER, $commission_types);
	$r->add_textbox("affiliate_commission_amount", NUMBER, AFFILIATE_COMMISSION_AMOUNT_MSG);
	$r->add_select("reward_type", INTEGER, $commission_types, REWARD_POINTS_TYPE_MSG);
	$r->add_textbox("reward_amount", NUMBER, REWARD_POINTS_AMOUNT_MSG);
	$r->add_select("credit_reward_type", INTEGER, $commission_types, REWARD_CREDITS_TYPE_MSG);
	$r->add_textbox("credit_reward_amount", NUMBER, REWARD_CREDITS_AMOUNT_MSG);

	$r->events[AFTER_DELETE] = "delete_type_data";

	$r->process();
	
	$item_type_id_dupl = get_param("item_type_id");
	if ($item_type_id_dupl){
	
		$duplicate_options    = ($duplicate_options == 1) ? " checked " : "";
		$duplicate_features = ($duplicate_features == 1) ? " checked " : "";
				
		$t->set_var("duplicate_options",    $duplicate_options);
		$t->set_var("duplicate_features", $duplicate_features);
	
		$t->set_var("duplicate_button", "Duplicate");
		$t->parse("duplicate", false);
		
	} else {
		$t->set_var("duplicate", "");
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
	
	function delete_type_data()
	{
		global $r, $db, $table_prefix;

		$item_type_id = $r->get_value("item_type_id");

		// {DELETE_ALL_BUTTON} properties
		$properties_ids = "";
		$sql = " SELECT property_id FROM " . $table_prefix ."items_properties WHERE item_type_id=" . $db->tosql($item_type_id, INTEGER); 
		$db->query($sql);
		while ($db->next_record()) {
			if(strlen($properties_ids)) { $properties_ids .= ","; }
			$properties_ids .= $db->f("property_id");
		}
		if (strlen($properties_ids)) {
			$db->query("DELETE FROM " . $table_prefix . "items_properties_values WHERE property_id IN (" . $db->tosql($properties_ids, TEXT, false) . ") ");
			$db->query("DELETE FROM " . $table_prefix . "items_properties WHERE item_type_id=" . $db->tosql($item_type_id, INTEGER)); 
		}

		// delete predefined specification
		$db->query("DELETE FROM " . $table_prefix . "features_default WHERE item_type_id=" . $db->tosql($item_type_id, INTEGER));		
	}

?>