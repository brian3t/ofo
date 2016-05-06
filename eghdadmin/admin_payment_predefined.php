<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_payment_predefined.php                             ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("payment_systems");

	$va_name = defined("VA_PRODUCT") ? strtolower(VA_PRODUCT) : "shop";

	$errors = "";
	$db_file = "../db/".$db_type."_viart_".$va_name.".sql";
	$operation = get_param("operation");
	$payment_id = get_param("payment_id");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_payment_predefined.html");
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_payment_systems_href", "admin_payment_systems.php");
	$t->set_var("admin_payment_system_href",  "admin_payment_system.php");
	$t->set_var("admin_payment_help_href",    "admin_payment_help.php");
	$t->set_var("admin_payment_predefined_href", "admin_payment_predefined.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$payment_sql = "";
	$payment_systems = array(array("", ""));
	// check for predefined systems
	if (file_exists($db_file)) {
		$fp = fopen($db_file, "r");
		while (!feof($fp)) {
			$sql_string = fgets($fp);
			if (preg_match("/^insert\s+into\s+\[?va_payment_systems\]?\s+\([^)]+\)\s+values\s+\(\s*(\d+)\s*,\s*'([^']+)'/i", $sql_string, $matches)) {
				$row_payment_id = $matches[1];
				if ($operation == "apply" && $row_payment_id == $payment_id) {
					$payment_sql = $sql_string;
				}
				$payment_systems[] = array($row_payment_id, $matches[2]);
			}
		}
		fclose($fp);
	} else {
		$errors = FILE_DOESNT_EXIST_MSG . " " . $db_file;
	}

	// set up html form parameters
	$r = new VA_Record($table_prefix . "payment_systems");
	$r->add_select("payment_id", INTEGER, $payment_systems);
	$r->change_property("payment_id", REQUIRED, true);
	$r->set_form_parameters();

	if (!$errors && $operation == "apply") {
		if ($payment_id) {
			if ($db_type == "postgre") {
				$new_payment_id = get_db_value(" SELECT NEXTVAL('seq_" . $table_prefix . "payment_systems') ");
				$payment_sql = preg_replace("/insert\s+into\s+va_payment_systems\s+(\([^)]+\))\s+values\s+\(\s*(\d+)\s*,/i", "INSERT INTO ".$table_prefix."payment_systems \\1 VALUES (".$new_payment_id.",", $payment_sql);
			} else {
				// remove payment id from SQL
				$payment_sql = preg_replace("/insert\s+into\s+va_payment_systems\s+\((\s*[`\[]?payment_id[`\]]?\s*,\s*)([^)]+\))\s+values\s+\((\s*\d+\s*,)/i", "INSERT INTO ".$table_prefix."payment_systems (\\2 VALUES (", $payment_sql);
			}
			// remove semicolon and space symbols in the end
			$payment_sql = preg_replace("/[;\s]+$/", "", $payment_sql);
			// add predefined payment system as new system and new id
			if ($db->query($payment_sql)) {
				if ($db_type == "mysql") {
					$new_payment_id = get_db_value(" SELECT LAST_INSERT_ID() ");
				} else if ($db_type == "access") {
					$new_payment_id = get_db_value(" SELECT @@IDENTITY ");
				} else if ($db_type == "db2") {
					$new_payment_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "payment_systems FROM " . $table_prefix . "payment_systems ");
				}
				// start adding related data
				$fp = fopen($db_file, "r");
				while (!feof($fp)) {
					$sql_string = fgets($fp);
					if (preg_match("/^INSERT\s+INTO\s+va_payment_parameters\s+\([^)]+\)\s+VALUES\s+\(\s*(\d+)\s*,\s*".$payment_id."\s*,/i", $sql_string, $matches)) {
						// remove payment id from SQL
						$sql = preg_replace("/INSERT\s+INTO\s+va_payment_parameters\s+\((\s*[`\[]?parameter_id[`\]]?\s*,\s*)([^)]+\))\s+VALUES\s+\((\s*\d+\s*,\s*\d+\s*,)/i", "INSERT INTO ".$table_prefix."payment_parameters (\\2 VALUES (".$new_payment_id.",", $sql_string);
						$db->query($sql);
					} else if (preg_match("/^INSERT\s+INTO\s+va_global_settings\s+\([^)]+\)\s+VALUES\s+\(\s*(\d+)\s*,\s*'(credit_card_info_|order_final_|recurring_)".$payment_id."'\s*,/i", $sql_string, $matches)) {
						$sql = preg_replace("/'(credit_card_info|order_final|recurring)_".$payment_id."'/i", "'\\1_".$new_payment_id."'", $sql_string);
						$db->query($sql);
					}
				}
				fclose($fp);

				// redirect to newly added payment system
				header("Location: admin_payment_system.php?payment_id=".$new_payment_id);
				exit;
			}
		} else {
			$errors = str_replace("{field_name}", PREDEFINED_PS_MSG, REQUIRED_MESSAGE);
		}
	}

	if ($errors) {
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	}

	$tabs = array("general" => ADMIN_GENERAL_MSG,);
	foreach ($tabs as $tab_name => $tab_title) {
		$t->set_var("tab_id", "tab_" . $tab_name);
		$t->set_var("tab_name", $tab_name);
		$t->set_var("tab_title", $tab_title);		
		if ($tab_name == $tab) {
			$t->set_var("tab_class", "adminTabActive");
			$t->set_var($tab_name . "_style", "display: block;");
		} else {
			$t->set_var("tab_class", "adminTab");
			$t->set_var($tab_name . "_style", "display: none;");
		}
		$t->parse("tabs", $tab_title);
	}
	$t->set_var("tab", $tab);
	

	$t->pparse("main");

?>