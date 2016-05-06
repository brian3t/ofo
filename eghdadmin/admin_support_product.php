<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_product.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support_static_data");
	
	$operation = get_param("operation");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_support_product.html");

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_product_href", "admin_support_product.php");
	$t->set_var("admin_support_products_href", "admin_support_products.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_PRODUCT_MSG, CONFIRM_DELETE_MSG));

	$product_id = get_param("product_id");
	
	$r = new VA_Record($table_prefix . "support_products");
	$r->return_page = "admin_support_products.php";

	$r->add_where("product_id", INTEGER);

	$r->add_textbox("product_name", TEXT, PROD_NAME_MSG);
	$r->parameters["product_name"][REQUIRED] = true;
	$r->add_checkbox("show_for_user", INTEGER);
	
	$r->add_checkbox("sites_all", INTEGER);
	$r->change_property("sites_all", DEFAULT_VALUE, 1);	
	
	if ($sitelist) {
		$selected_sites = array();
		if (strlen($operation)) {
			$sites = get_param("sites");
			if ($sites) {
				$selected_sites = split(",", $sites);
			}
		} elseif ($product_id) {
			$sql  = "SELECT site_id FROM " . $table_prefix . "support_products_sites ";
			$sql .= " WHERE product_id=" . $db->tosql($product_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$selected_sites[] = $db->f("site_id");
			}
		}
	}

	$r->set_event(BEFORE_INSERT, "set_db_values_before_changes");
	$r->set_event(BEFORE_UPDATE, "set_db_values_before_changes");
	$r->set_event(BEFORE_DELETE, "before_delete_product");
	$r->set_event(AFTER_UPDATE,  "save_other_values_after_save");
	$r->set_event(AFTER_INSERT,  "save_other_values_after_save");
	
	$r->process();
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	
	if ($sitelist) {
		$sites = array();
		$sql = " SELECT site_id, site_name FROM " . $table_prefix . "sites ";
		$db->query($sql);
		while ($db->next_record())	{
			$site_id   = $db->f("site_id");
			$site_name = $db->f("site_name");
			$sites[$site_id] = $site_name;
			$t->set_var("site_id", $site_id);
			$t->set_var("site_name", $site_name);
			if (in_array($site_id, $selected_sites)) {
				$t->parse("selected_sites", true);
			} else {
				$t->parse("available_sites", true);
			}
		}
		$r->set_value("sites_all", 1);
	}

	$tabs = array("general" => MENU_SUPPORT . ADMIN_PRODUCT_MSG);
	if ($sitelist) {
		$tabs["sites"] = 'Sites';
	}
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
	if ($sitelist) {
		$t->parse('sitelist');
	}	
	
	$t->pparse("main");
	
	function set_db_values_before_changes(){
		global $db, $table_prefix, $r, $sitelist;
		global $product_id;
		if (!$product_id) {
			$db->query("SELECT MAX(product_id) FROM " . $table_prefix . "support_products");
			$db->next_record();
			$product_id = $db->f(0) + 1;
			$r->set_value("product_id", $product_id);
		} 
		if (!$sitelist) {
			$r->set_value("sites_all", 1);
		}
	}
	
	function before_delete_product(){
		global $db, $table_prefix;
		global $product_id;
		$db->query("DELETE FROM " . $table_prefix . "support_products_sites WHERE product_id=" . $db->tosql($product_id, INTEGER));		
	}
	
	function save_other_values_after_save() {
		global $db, $table_prefix;
		global $product_id, $sitelist, $selected_sites;
		if ($sitelist) {
			$db->query("DELETE FROM " . $table_prefix . "support_products_sites WHERE product_id=" . $db->tosql($product_id, INTEGER));
			for ($st = 0; $st < sizeof($selected_sites); $st++) {
				$site_id = $selected_sites[$st];
				if (strlen($site_id)) {
					$sql  = " INSERT INTO " . $table_prefix . "support_products_sites (product_id, site_id) VALUES (";
					$sql .= $db->tosql($product_id, INTEGER) . ", ";
					$sql .= $db->tosql($site_id, INTEGER) . ") ";
					$db->query($sql);
				}
			}
		}	
	}

?>
