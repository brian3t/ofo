<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_layout_header.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once("./admin_common.php");

	check_admin_security("layouts");
	$operation   = get_param("operation");
	$layout_id   = get_param("layout_id");

	// always use menu list
	header("Location: admin_header_menus.php?layout_id=" . $layout_id);
	exit;

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_layout_header.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_layouts_href", "admin_layouts.php");
	$t->set_var("admin_layout_href", "admin_layout.php");
	$t->set_var("admin_layout_header_href", "admin_layout_header.php");
	$t->set_var("admin_header_menus_href", "admin_header_menus.php");

	$return_page = get_param("rp");
	if (!strlen($return_page)) { $return_page = "admin_layouts.php"; }
	$errors = "";

	$sql = "SELECT layout_name FROM " . $table_prefix . "layouts WHERE layout_id=" . $db->tosql($layout_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$layout_name = $db->f("layout_name");
		$t->set_var("layout_name", htmlspecialchars($layout_name));
	} else {
		header("Location: " . $return_page);
		exit;
	}


	// set up html form parameters
	$r = new VA_Record($table_prefix . "header_links", "links");
	$r->add_where("menu_id", INTEGER);
	$r->add_hidden("layout_id", INTEGER);
	$r->change_property("layout_id", USE_IN_INSERT, true);

	$r->add_textbox("menu_title", TEXT, MENU_TITLE_MSG);
	$r->add_textbox("menu_url", TEXT, MENU_URL_MSG);
	$r->parameters["menu_url"][REQUIRED] = true;
	$r->add_textbox("menu_image", TEXT);
	$r->add_textbox("menu_image_active", TEXT);
	$r->add_hidden("menu_order", INTEGER);
	$r->change_property("menu_order", USE_IN_INSERT, true);
	$r->change_property("menu_order", USE_IN_UPDATE, false);
	$r->add_hidden("menu_path", TEXT);
	$r->change_property("menu_path", USE_IN_INSERT, true);
	$r->change_property("menu_path", USE_IN_UPDATE, false);
	$r->change_property("menu_path", USE_SQL_NULL, false);
	$r->add_checkbox("show_non_logged", INTEGER);
	$r->add_checkbox("show_logged", INTEGER);
	// sorting
	$r->change_property("menu_order", USE_IN_ORDER, ORDER_ASC);

	$more_links = get_param("more_links");
	$number_links = get_param("number_links");

	$eg = new VA_EditGrid($r, "links");
	$eg->get_form_values($number_links);

	if (strlen($operation) && !$more_links)
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		elseif ($operation == "delete" && $layout_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "header_links WHERE layout_id=" . $db->tosql($layout_id, INTEGER));		
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $eg->validate(); 

		if ($is_valid)
		{
			$eg->set_values("layout_id", $layout_id);
			$eg->update_all($number_links);
			
			$sql = "UPDATE ".$table_prefix."header_links SET parent_menu_id=menu_id WHERE parent_menu_id=0 OR parent_menu_id IS NULL";
			$db->query($sql);

			header("Location: " . $return_page);
			exit;
		}
	}
	elseif (strlen($layout_id) && !$more_links)
	{
		$eg->set_value("layout_id", $layout_id);
		$eg->change_property("menu_id", USE_IN_SELECT, true);
		$eg->change_property("menu_id", USE_IN_WHERE, false);
		$eg->change_property("layout_id", USE_IN_WHERE, true);
		$eg->change_property("layout_id", USE_IN_SELECT, true);
		$number_links= $eg->get_db_values();
		if ($number_links == 0) {
			$number_links = 5;
		}
	}
	elseif ($more_links)
	{
		$number_links += 5;
	}
	else
	{
		$number_links = 5;
	}

	$t->set_var("number_links", $number_links);

	$eg->set_parameters_all($number_links);

	$t->set_var("layout_id", $layout_id);
	$t->set_var("rp", htmlspecialchars($return_page));

	$t->pparse("main");

?>