<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_release_changes.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("products_categories");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_release_changes.html");

	$t->set_var("admin_property_href", "admin_property.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_product_href", "admin_product.php");
	$t->set_var("admin_releases_href", "admin_releases.php");
	$t->set_var("admin_release_changes_href", "admin_release_changes.php");
	
	$item_id = get_param("item_id");
	$sql  = " SELECT item_name FROM " . $table_prefix . "items ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);

	$category_id = get_param("category_id");
	if(!strlen($category_id)) $category_id = "0";

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($category_id);

	$db->query($sql);
	if($db->next_record())
		$t->set_var("item_name", $db->f("item_name"));
	else
		die(str_replace("{item_id}", $item_id, PRODUCT_ID_NO_LONGER_EXISTS_MSG));

	$change_types = get_db_values("SELECT * FROM " . $table_prefix . "change_types", array(array("", SELECT_TYPE_MSG)));


	// set up html form parameters
	$r = new VA_Record($table_prefix . "release_changes", "changes");
	$r->add_where("change_id", INTEGER);
	$r->add_hidden("release_id", INTEGER);
	$r->change_property("release_id", USE_IN_INSERT, true);
	$r->add_checkbox("is_showing", INTEGER);
	$r->add_select("change_type_id", INTEGER, $change_types, TYPE_MSG);
	$r->parameters["change_type_id"][REQUIRED] = true;
	$r->add_textbox("change_date", DATETIME, DATE_MSG);
	$r->parameters["change_date"][REQUIRED] = true;
	$r->parameters["change_date"][VALUE_MASK] = $date_edit_format;
	$r->add_textbox("change_desc", TEXT);
	
	$release_id = get_param("release_id");
	$item_id = get_param("item_id");
	$category_id = get_param("category_id");

	$more_changes = get_param("more_changes");
	$number_changes = get_param("number_changes");

	$eg = new VA_EditGrid($r, "changes");
	$eg->get_form_values($number_changes);

	$operation = get_param("operation");
	$return_page = "admin_releases.php?item_id=" . $item_id . "&category_id=" . $category_id;

	if(strlen($operation) && !$more_changes)
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		else if($operation == "delete" && $release_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "release_changes WHERE release_id=" . $db->tosql($release_id, INTEGER));		
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $eg->validate(); 

		if($is_valid)
		{
			$eg->set_values("release_id", $release_id);
			$eg->update_all($number_changes);
			header("Location: " . $return_page);
			exit;
		}
	}
	else if(strlen($release_id) && !$more_changes)
	{
		$eg->set_value("release_id", $release_id);
		$eg->change_property("change_id", USE_IN_SELECT, true);
		$eg->change_property("change_id", USE_IN_WHERE, false);
		$eg->change_property("release_id", USE_IN_WHERE, true);
		$eg->change_property("release_id", USE_IN_SELECT, true);
		$number_changes = $eg->get_db_values();
		if($number_changes == 0)
			$number_changes = 5;
	}
	else if($more_changes)
	{
		$number_changes += 5;
	}
	else
	{
		$number_changes = 5;
	}

	$t->set_var("number_changes", $number_changes);

	$eg->set_parameters_all($number_changes);

	$t->set_var("change_date_format", join("", $date_edit_format));
	$t->set_var("item_id", $item_id);
	$t->set_var("release_id", $release_id);
	$t->set_var("category_id", $category_id);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>