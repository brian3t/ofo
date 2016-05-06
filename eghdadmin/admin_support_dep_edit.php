<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_dep_edit.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support_departments");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_support_dep_edit.html");

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_dep_edit_href", "admin_support_dep_edit.php");
	$t->set_var("admin_support_departments_href", "admin_support_departments.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", SUPPORT_DEPARTMENT_FIELD, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "support_departments");
	$r->return_page = "admin_support_departments.php";

	$admins =array();
	$sql = " SELECT admin_id,admin_name FROM ".$table_prefix ."admins ORDER BY admin_id ASC";
	$db->query($sql);
	if ($db->next_record()) {
		$usr = new VA_Record($table_prefix . "admins");
		do {
			$admins[$db->f("admin_id")] = $db->f("admin_name");
			$usr->add_checkbox("admin_".$db->f("admin_id"), INTEGER);
		}
		while($db->next_record());
	}	

	$r->get_form_values();
	$usr->get_form_values();

	$r->add_where("dep_id", INTEGER);
	$r->add_checkbox("show_for_user", INTEGER);
	$r->add_textbox("short_title", TEXT, DEPARTMENT_SHORT_TITLE_MSG);
	$r->parameters["short_title"][REQUIRED] = true;
	$r->add_textbox("full_title", TEXT, DEPARTMENT_FULL_TITLE_MSG);
	$r->parameters["full_title"][REQUIRED] = true;
	$r->add_textbox("attachments_dir", TEXT, ATTACHMENTS_DIRECTORY_MSG);
	$r->add_textbox("attachments_mask", TEXT, FILES_ALLOWED_MSG);
	$r->add_textbox("incoming_account", TEXT, INCOMING_ACCOUNT_MSG);

	$support_products = get_db_values("SELECT product_id, product_name FROM " . $table_prefix . "support_products", array(array("", SELECT_PRODUCT_MSG)));
	$r->add_select("incoming_product_id", INTEGER, $support_products, INCOMING_PRODUCT_MSG);

	$support_types = get_db_values("SELECT * FROM " . $table_prefix . "support_types", array(array("", SELECT_TYPE_MSG)));
	$r->add_select("incoming_type_id", INTEGER, $support_types, INCOMING_TYPE_MSG);

	$r->add_textbox("outgoing_account", TEXT, OUTGOING_ACCOUNT_MSG);
	$r->add_textbox("signature", TEXT, SIGNATURE_MSG);

	$r->add_checkbox("sites_all", INTEGER);	
	
	$r->get_form_values();
	$operation = get_param("operation");
	$dep_id = get_param("dep_id");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	
	if ($sitelist) {
		$selected_sites = array();
		if (strlen($operation)) {
			$sites = get_param("sites");
			if ($sites) {
				$selected_sites = split(",", $sites);
			}
		} elseif ($dep_id) {
			$sql  = "SELECT site_id FROM " . $table_prefix . "support_departments_sites  ";
			$sql .= " WHERE dep_id=" . $db->tosql($dep_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$selected_sites[] = $db->f("site_id");
			}
		}
	}
	
	if(strlen($operation))	{
		$is_valid=true;
		if($operation == "cancel")		{
			header("Location: " . $r->return_page);
			exit;
		}
		else if($operation == "delete" && $dep_id)    // deleting department
		{
			$r->delete_record();
			$db->query("DELETE FROM ".$table_prefix ."support_users_departments WHERE dep_id=".$db->tosql($dep_id, INTEGER));
			$db->query("DELETE FROM ".$table_prefix ."support_departments_sites WHERE dep_id=".$db->tosql($dep_id, INTEGER));	
			header("Location: " . $r->return_page);
			exit;
		}	
		if($is_valid) {
			$is_valid = $r->validate();
		}
		if($is_valid)	{
			if (!$sitelist) {
				$r->set_value("sites_all", 1);
			}
			if(strlen($r->get_value("dep_id"))) {   // insert existing department
				$record_updated = $r->update_record();
			} else {
				$record_updated = $r->insert_record(); // insert new department
				if ($record_updated) {
					$sql = "SELECT MAX(dep_id) FROM " . $table_prefix . "support_departments";
					$dep_id = get_db_value($sql);
					$r->set_value("dep_id", $dep_id);
				}
			}
			if ($record_updated) {
				
				// update sites
				if ($sitelist) {
					$db->query("DELETE FROM " . $table_prefix . "support_departments_sites WHERE dep_id=" . $db->tosql($dep_id, INTEGER));
					for ($st = 0; $st < sizeof($selected_sites); $st++) {
						$site_id = $selected_sites[$st];
						if (strlen($site_id)) {
							$sql  = " INSERT INTO " . $table_prefix . "support_departments_sites (dep_id, site_id) VALUES (";
							$sql .= $db->tosql($dep_id, INTEGER) . ", ";
							$sql .= $db->tosql($site_id, INTEGER) . ") ";
							$db->query($sql);
						}
					}
				}							
				
				foreach($admins as $admin_id => $admin_name)
				{
					$admin="admin_".$admin_id;
					if ($usr->get_value($admin)) {
						$sql  = " SELECT dep_id FROM ".$table_prefix ."support_users_departments ";
						$sql .= " WHERE dep_id=" . $db->tosql($dep_id, INTEGER);
						$sql .= " AND admin_id=" . $db->tosql($admin_id, INTEGER);
						$db->query($sql);
						if (!$db->next_record()) {
							$sql = " SELECT COUNT(*) FROM ".$table_prefix ."support_users_departments WHERE admin_id=" . $db->tosql($admin_id, INTEGER);
							$admin_departments = get_db_value($sql);
							$sql  = " INSERT INTO ".$table_prefix ."support_users_departments (admin_id, dep_id, is_default_dep) VALUES (";
							$sql .= $db->tosql($admin_id, INTEGER) . ", ";
							$sql .= $db->tosql($dep_id, INTEGER) . ", ";
							if ($admin_departments) {
								$sql .= "0) ";
							} else {
								$sql .= "1) ";
							}
							$db->query($sql);
						}
					} else {
						$sql  = " DELETE FROM ".$table_prefix ."support_users_departments ";
						$sql .= " WHERE dep_id=" . $db->tosql($dep_id, INTEGER);
						$sql .= " AND admin_id=" . $db->tosql($admin_id, INTEGER);
						$db->query($sql);
					}
				}
 				header("Location: " . $r->return_page);
				exit;
			}
		}
	}
	else if(strlen($r->get_value("dep_id")))   	// show existing values 
	{
		$r->get_db_values();
		$admin_checked = array();
    $sql  = " SELECT admin_id FROM ".$table_prefix ."support_users_departments ";
		$sql .= " WHERE dep_id = " . $db->tosql($dep_id, INTEGER);
		$db->query($sql);
		while($db->next_record()) {
			$admin_checked[$db->f("admin_id")] = 1;
		}
		foreach ($admins as $admin_id => $admin_name){
			$admin="admin_".$admin_id;
			if (isset($admin_checked[$admin_id])) {
				if($admin_checked[$admin_id]) $usr->set_value($admin, 1);
				else $usr->set_value($admin, 0);
			}
			else $usr->set_value($admin, 0);
		}	
	} else {
		$r->set_value("show_for_user", 1);
		$r->set_value("sites_all", 1);
	}

	foreach($admins as $admin_id => $admin_name) {
		$admin="admin_".$admin_id;
		$admin_name_checked = $usr->get_value($admin) ? "checked" : "";
		$admin_checkbox = "<input type=\"checkbox\" name=\"$admin\" $admin_name_checked value=\"$admin_id\">";
		$t->set_var("admin_name", $admin_name);
		$t->set_var("admin_checkbox", $admin_checkbox);
		$t->parse ("admin_rows", true);
	}

	$r->set_parameters();
	if($r->get_value("dep_id")) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	}	else {
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");	
	}

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
	}
	$tabs = array("general" => EDIT_SUPPORT_DEPARTMENT_MSG);
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
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

?>