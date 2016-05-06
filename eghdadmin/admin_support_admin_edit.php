<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_admin_edit.php                             ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");	check_admin_security("support_users");

	// get permissions
	$permissions = get_permissions();
	$admin_users = get_setting_value($permissions, "admin_users", 0);

	$default_department_id = get_param("default_department_id");
	$admin_id = get_param("admin_id");
	if ($admin_id && !$admin_users) {
		$sql  = " SELECT ap.support_privilege ";
		$sql .= " FROM " . $table_prefix . "admins a, " . $table_prefix . "admin_privileges ap ";
		$sql .= " WHERE a.privilege_id=ap.privilege_id ";
		$sql .= " AND a.admin_id=" . $db->tosql($admin_id, INTEGER);
		$support_privilege = get_db_value($sql);
		if ($support_privilege != 1) {
			header ("Location: admin_support_admins.php");
			exit;
		}
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_support_admin_edit.html");

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_admin_edit_href", "admin_support_admin_edit.php");
	$t->set_var("admin_support_admins_href", "admin_support_admins.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "admins");
	$r->return_page = "admin_support_admins.php";

	$deps = array(); 
	$department_values = array();
	$department_values[] = array("", SELECT_DEFAULT_DEP_MSG);
	$sql  = " SELECT dep_id, short_title ";
	$sql .= " FROM " . $table_prefix . "support_departments ";
	$sql .= " ORDER BY short_title ASC ";
	$db->query($sql);
	if ($db->next_record()) {
		$default_dept = "";
		$d = new VA_Record($table_prefix . "support_departments");
		do {
			$deps[$db->f("dep_id")] = $db->f("short_title");
			$department_values[] = array($db->f("dep_id"), $db->f("short_title"));
			$d->add_checkbox("department_".$db->f("dep_id"), INTEGER);
			if (!isset($admin_id)) $admin_id = 0;
		}
		while($db->next_record());
		$d->get_form_values();
	}	

	$r->add_where("admin_id", INTEGER);

	if(!$admin_id)
	{
		$r->add_textbox("login", TEXT, LOGIN_BUTTON);
		$r->parameters["login"][REQUIRED] = true;
		$r->parameters["login"][UNIQUE] = true;
		$r->parameters["login"][MIN_LENGTH] = 3;
		$r->add_textbox("password", TEXT, PASSWORD_FIELD);
		$r->parameters["password"][REQUIRED] = true;
		$r->parameters["password"][MIN_LENGTH] = 3;
		$r->add_textbox("confirm", TEXT, CONFIRM_PASS_FIELD);
		$r->change_property("confirm", USE_IN_SELECT, false);
		$r->change_property("confirm", USE_IN_INSERT, false);
		$r->change_property("confirm", USE_IN_UPDATE, false);
		$r->change_property("password", MATCHED, "confirm");
	}

	$r->add_textbox("admin_name", TEXT, USER_NAME_MSG);
	$r->change_property("admin_name", REQUIRED, true);
	$r->add_textbox("admin_alias", TEXT, INITIALS_OR_ALIAS_MSG);
	$r->change_property("admin_alias", UNIQUE, true);
	$r->change_property("admin_alias", REQUIRED, true);
	$r->add_select("default_department_id", INTEGER, $department_values, DEFAULT_DEPARTMENT_MSG);
	$r->change_property("default_department_id", USE_IN_SELECT, false);
	$r->change_property("default_department_id", USE_IN_INSERT, false);
	$r->change_property("default_department_id", USE_IN_UPDATE, false);

	$r->add_textbox("email", TEXT, EMAIL_MSG);
	$r->change_property("email", REQUIRED, true);
	$r->change_property("email", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("signature", TEXT, SIGNATURE_MSG);

	$sql  = " SELECT privilege_id, privilege_name FROM ".$table_prefix."admin_privileges ";
	if (!$admin_users) {
		$sql .= " WHERE support_privilege=1 ";
	}
	$privileges = get_db_values($sql, array(array("", SELECT_PRIVILEDGE_MSG)));
	if (sizeof($privileges) == 1) {
		$privileges = array(array("", PRIVILEDGES_NOT_AVAILABLE_MSG));
	}
	$r->add_select("privilege_id", INTEGER, $privileges, PRIVILEGE_MSG);
	$r->parameters["privilege_id"][REQUIRED] = true;
	
	$r->get_form_values();			

	$operation = get_param("operation");
	if (strlen($operation))	{
		if ($operation == "cancel")		{
			header("Location: " . $r->return_page);
			exit;
		} else if($operation == "delete" && $admin_id) {   // deleting user 
			$r->delete_record();
			$db->query("DELETE FROM " . $table_prefix . "support_users_departments WHERE admin_id = " .$db->tosql($admin_id, INTEGER));
			header("Location: " . $r->return_page);
			exit;
		}	else {

			$is_valid = $r->validate();
			if ($is_valid && !$admin_users) {
				$sql  = " SELECT support_privilege FROM ".$table_prefix."admin_privileges ";
				$sql .= " WHERE privilege_id=" . $db->tosql($r->get_value("privilege_id"), INTEGER);
				$support_privilege = get_db_value($sql);
				if ($support_privilege != 1) {
					$is_valid = false;
					$r->set_value("privilege_id", "");
					$r->validate();
				}
			}

			if($is_valid) {
				if(strlen($r->get_value("admin_id"))) {   // insert existing admin
					$r->update_record();
					$db->query("DELETE FROM " . $table_prefix ."support_users_departments WHERE admin_id = " . $db->tosql($admin_id, INTEGER));
				} else {
					$password_encrypt = get_setting_value($settings, "password_encrypt", 0);
					$admin_password_encrypt = get_setting_value($settings, "admin_password_encrypt", $password_encrypt);
					if ($admin_password_encrypt == 1) {
						$r->set_value("password", md5($r->get_value("password")));
					}
					$r->insert_record();                  // insert new admin
					$sql = "SELECT MAX(admin_id) FROM " . $table_prefix . "admins";
					$admin_id = get_db_value($sql);
					$r->set_value("admin_id", $admin_id);
				}
				foreach($deps as $dep_id => $dep_name)
				{
					$dep = "department_".$dep_id;
					if ($d->get_value($dep)) {
						$sql  = " INSERT INTO ".$table_prefix ."support_users_departments (admin_id, dep_id, is_default_dep) VALUES (";
						$sql .= $db->tosql($admin_id, INTEGER) . ", ";
						$sql .= $db->tosql($dep_id, INTEGER) . ", ";
						if ($dep_id == $default_department_id) {
							$sql .= $db->tosql("1", INTEGER) . ")";
						} else {
							$sql .= $db->tosql("0", INTEGER) . ")";
						}
						$db->query($sql);
					}
				}

				// add default department if it wasn't selected
				if ($default_department_id) {
					$sql  = " SELECT dep_id FROM ".$table_prefix ."support_users_departments ";
					$sql .= " WHERE dep_id=" . $db->tosql($default_department_id, INTEGER);
					$sql .= " AND admin_id=" . $db->tosql($admin_id, INTEGER);
					$db->query($sql);
					if (!$db->next_record()) {
						$sql  = " INSERT INTO ".$table_prefix ."support_users_departments (admin_id, dep_id, is_default_dep) VALUES (";
						$sql .= $db->tosql($admin_id, INTEGER) . ", ";
						$sql .= $db->tosql($default_department_id, INTEGER) . ", 1)";
						$db->query($sql);
					}
				}
				
 				header("Location: " . $r->return_page);
				exit;
			}
		}
	}
	else if(strlen($r->get_value("admin_id")))   	// show existing values 
	{
		$r->get_db_values();
		$default_department_id = get_db_value("SELECT dep_id FROM " . $table_prefix . "support_users_departments WHERE admin_id=" . $db->tosql($admin_id, INTEGER) . " AND is_default_dep=1 ");
		$r->set_value("default_department_id", $default_department_id);

		$dep_checked = array();
   	$sql  = " SELECT dep_id FROM ".$table_prefix ."support_users_departments ";
		$sql .= " WHERE admin_id = " . $db->tosql($admin_id, INTEGER);
		$db->query($sql);
		while($db->next_record()) {
			$dep_checked[$db->f("dep_id")] = 1;
		}
		foreach ($deps as $dep_id => $dep_name){
			$dep="department_".$dep_id;
			if (isset($dep_checked[$dep_id])) {
				if($dep_checked[$dep_id]) $d->set_value($dep, 1);
				else $d->set_value($dep, 0);
			}
			else $d->set_value($dep, 0);
		}	
	}

	foreach($deps as $dep_id => $dep_name) {
		$dep="department_".$dep_id;
		$dep_name_checked = $d->get_value($dep) ? "checked" : "";
		$dep_checkbox = "<input type=\"checkbox\" name=\"$dep\" $dep_name_checked value=\"$dep_id\">";
		$t->set_var("short_title", $dep_name);
		$t->set_var("dep_checkbox", $dep_checkbox);
		$t->parse ("dep_rows", true);
	}
	
	if($r->where_set)	
	{
		$t->set_var("save_button", UPDATE_BUTTON);
		if($t->block_exists("delete")) {
			$t->parse("delete", false);	
		}
	}
	else
	{
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");	
	}
	
	$r->set_form_parameters();

	if(!$admin_id) {
		$t->parse("login_information", false);
	}	
		
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

?>
