<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_forum_category.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/friendly_functions.php");
	include_once($root_folder_path."messages/".$language_code."/forum_messages.php");


	include_once("./admin_common.php");

	check_admin_security("forum");

 	$t = new VA_Template($settings["admin_templates_dir"]);
 	$t->set_file("main", "admin_forum_category.html");

	$t->set_var("admin_forum_href", "admin_forum.php");
	$t->set_var("admin_forum_category_href", "admin_forum_category.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CATEGORY_MSG, CONFIRM_DELETE_MSG));

	$category_id = get_param("category_id");

	$r = new VA_Record($table_prefix . "forum_categories");

	$r->add_where("category_id", INTEGER);
	$r->add_textbox("category_name", TEXT, CATEGORY_NAME_MSG);
	$r->change_property("category_name", REQUIRED, true);
	$r->change_property("category_name", MAX_LENGTH, 255);
	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);
	$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
	$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	$r->add_textbox("category_order", TEXT, CATEGORY_ORDER_MSG);
	$r->change_property("category_order", REQUIRED, true);

	$allowed_values = array(array("0", NOBODY_MSG), array("1", FOR_ALL_USERS_MSG));
	$r->add_radio("allowed_view", INTEGER, $allowed_values, ALLOW_VIEW_MSG);
	$r->add_textbox("short_description", TEXT);
	$r->add_textbox("full_description", TEXT);

	$r->add_checkbox("sites_all", INTEGER);

	$r->get_form_values();

	$operation = get_param("operation");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	$return_page = "admin_forum.php";


	if ($sitelist) {
		$selected_sites = array();
		if (strlen($operation)) {
			$sites = get_param("sites");
			if ($sites) {
				$selected_sites = split(",", $sites);
			}
		} elseif ($category_id) {
			$sql  = "SELECT site_id FROM " . $table_prefix . "forum_categories_sites ";
			$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
			$db->query($sql);
			while ($db->next_record()) {
				$selected_sites[] = $db->f("site_id");
			}
		}
	}

	if(strlen($operation))
	{
		if ($operation == "cancel") {
			header("Location: " . $return_page);
			exit;
		} else if($operation == "delete" && $r->get_value("category_id")) {
			$forums_count = get_db_value("SELECT COUNT(*) FROM " . $table_prefix . "forum_list WHERE category_id = " . $db->tosql($category_id, INTEGER));
			if ($forums_count > 0) {
				$r->errors .= FORUMS_ASSIGNED_CATEGORY_MSG;
			}
			else {
				$db->query("DELETE FROM " . $table_prefix . "forum_categories_sites WHERE category_id=" . $db->tosql($category_id, INTEGER));
				$r->delete_record();
				header("Location: " . $return_page);
				exit;
			}
		}

		$is_valid = $r->validate();

		if($is_valid)
		{
			if (!$sitelist) {
				$r->set_value("sites_all", 1);
			}
			if(strlen($r->get_value("category_id"))) {
				set_friendly_url();
				$record_updated = $r->update_record();
			}	else {
				set_friendly_url();
				$db->query("SELECT MAX(category_id) FROM " . $table_prefix . "forum_categories");
				$db->next_record();
				$category_id = $db->f(0) + 1;
				$r->set_value("category_id", $category_id);
				$record_updated = $r->insert_record();
			}
			// update sites
			if ($sitelist) {
				$db->query("DELETE FROM " . $table_prefix . "forum_categories_sites WHERE category_id=" . $db->tosql($category_id, INTEGER));
				for ($st = 0; $st < sizeof($selected_sites); $st++) {
					$site_id = $selected_sites[$st];
					if (strlen($site_id)) {
						$sql  = " INSERT INTO " . $table_prefix . "forum_categories_sites (category_id, site_id) VALUES (";
						$sql .= $db->tosql($category_id, INTEGER) . ", ";
						$sql .= $db->tosql($site_id, INTEGER) . ") ";
						$db->query($sql);
					}
				}
			}
			if ($record_updated) {
				header("Location: " . $return_page);
				exit;
			}
		}
	} else if(strlen($r->get_value("category_id"))) { // edit existing category
		$r->get_db_values();
	} else { // new category - set default values
		$category_order = get_db_value("SELECT MAX(category_order) FROM " . $table_prefix . "forum_categories");
		$category_order++;
		$r->set_value("category_order", $category_order);
		$r->set_value("allowed_view", 1);
		$r->set_value("sites_all", 1);
	}

	$r->set_parameters();

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

	if(strlen($category_id)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);
	}
	else {
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");
	}

	$tabs = array("general" => EDIT_CATEGORY_MSG);
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
		$t->parse("sitelist");
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>