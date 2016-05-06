<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_shipping_rule.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("shipping_rules");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_shipping_rule.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_shipping_rules_href", "admin_shipping_rules.php");
	$t->set_var("admin_shipping_rule_href", "admin_shipping_rule.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", SHIPPING_RULE_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "shipping_rules");
	$r->return_page = "admin_shipping_rules.php";
	$r->add_where("shipping_rule_id", INTEGER);
	$r->add_textbox("shipping_rule_desc", TEXT, SHIPPING_RULE_MSG);
	$r->change_property("shipping_rule_desc", REQUIRED, true);
	$r->add_checkbox("is_country_restriction", INTEGER);

	$r->events[BEFORE_INSERT] = "set_shipping_rule_id";
	$r->events[AFTER_INSERT] = "update_shipping_countries";
	$r->events[AFTER_UPDATE] = "update_shipping_countries";
	$r->events[AFTER_DELETE] = "delete_shipping_countries";

	$r->process();

	$t->set_var("countries", "");
	$t->set_var("selected_countries", "");

	$sql = " SELECT country_id, country_name FROM " . $table_prefix . "countries ORDER BY country_order, country_name ";
	$db->query($sql);
	while($db->next_record())
	{
		$t->set_var("country_id", strtoupper($db->f("country_id")));
		$t->set_var("country_name", str_replace("\"", "\\\"", $db->f("country_name")));
		$t->parse("countries");
	}

	$operation = get_param("operation");
	if ($operation == "save") {
		$countries = get_param("countries");
		if($countries) {
			$selected_countries = split(",", $countries);
			for($i = 0; $i < sizeof($selected_countries); $i++) {
				$t->set_var("country_id", $selected_countries[$i]);
				$t->parse("selected_countries");
			}
		}
	} else if($r->get_value("shipping_rule_id")) {
		$sql = " SELECT country_id FROM " . $table_prefix . "shipping_rules_countries WHERE shipping_rule_id=" . $db->tosql($r->get_value("shipping_rule_id"), INTEGER);
		$db->query($sql);
		while($db->next_record())
		{
			$t->set_var("country_id", $db->f("country_id"));
			$t->parse("selected_countries");
		}
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function set_shipping_rule_id()  {
		global $db, $table_prefix, $r;
		$sql = "SELECT MAX(shipping_rule_id) FROM " . $table_prefix . "shipping_rules";
		$db->query($sql);
		if($db->next_record()) {
			$shipping_rule_id = $db->f(0) + 1;
			$r->change_property("shipping_rule_id", USE_IN_INSERT, true);
			$r->set_value("shipping_rule_id", $shipping_rule_id);
		}	
	}

	function update_shipping_countries()  {
		global $db, $table_prefix, $r;

		$shipping_rule_id = $r->get_value("shipping_rule_id");
		$db->query("DELETE FROM " . $table_prefix . "shipping_rules_countries WHERE shipping_rule_id=" . $db->tosql($shipping_rule_id, INTEGER));

		$countries = get_param("countries");
		if (strlen($countries)) {
			$selected_countries = split(",", $countries);
			for($i = 0; $i < sizeof($selected_countries); $i++) {
				$db->query("INSERT INTO " . $table_prefix . "shipping_rules_countries (shipping_rule_id, country_id) VALUES (" . $db->tosql($shipping_rule_id, INTEGER) . "," . $db->tosql($selected_countries[$i], TEXT) . ")");
			}
		}
	}

	function delete_shipping_countries()  {
		global $db, $table_prefix, $r;
		$shipping_rule_id = $r->get_value("shipping_rule_id");
		$db->query("DELETE FROM " . $table_prefix . "shipping_rules_countries WHERE shipping_rule_id=" . $db->tosql($shipping_rule_id, INTEGER));
	}

?>