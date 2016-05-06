<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_header_submenus_order.php                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include("./admin_config.php");
	include($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include($root_folder_path . "includes/record.php");

	check_admin_security("site_navigation");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_header_submenus_order.html");
  $t->set_var("admin_header_menus_href", "admin_header_submenus_order.php");

	$menu_id = get_param("menu_id");
	
	$shown_header_menus = array();

	$operation = get_param("operation");
	$return_page = "admin_menu_submenus.php?menu_id=" . $menu_id;
		
	$sql = " SELECT h.menu_title, l.layout_name, h.layout_id ";
	$sql.= " FROM ".$table_prefix."header_links h INNER JOIN ".$table_prefix."layouts l ON (h.layout_id=l.layout_id) ";
	$sql.= " WHERE h.menu_id=".$db->tosql($menu_id, INTEGER);
	$db->query($sql);

	if($db->next_record()) {
		$menu_name = $db->f("menu_title");
		$layout_name = $db->f("layout_name");
		$layout_id = $db->f("layout_id");
		$t->set_var("menu_name", get_translation($menu_name));
		$t->set_var("layout_name", htmlspecialchars($layout_name));
		$t->set_var("layout_id", $layout_id);
		$t->set_var("menu_id", $menu_id);		
	} else {
		header("Location: " . $return_page);
		exit;
	}
	
	$t->set_var("admin_layout_href", "admin_layout.php");
	$t->set_var("admin_header_menus_href", "admin_header_menus.php");
	$t->set_var("admin_header_submenus_href", "admin_menu_submenus.php");

	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		$shown_list = get_param("shown_list");
		if($shown_list) {
			$left_array = split(",", $shown_list);
			for($i = 0; $i < sizeof($left_array); $i++) {
				$shown_header_menus[] = $left_array[$i];
			}
		}

		if($operation == "save")
		{
			for($i = 0; $i < sizeof($shown_header_menus); $i++) {
				$sql  = " UPDATE " . $table_prefix . "header_submenus SET submenu_order=" . intval($i + 1);				
				$sql .= " WHERE submenu_id=" . $shown_header_menus[$i];
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
	}
	else
	{
		$sql  = " SELECT submenu_id, submenu_title, submenu_order ";
		$sql .= " FROM " . $table_prefix . "header_submenus WHERE menu_id = " . $db->tosql($menu_id, INTEGER);
		$sql .= " ORDER BY submenu_order, submenu_id DESC ";
		$db->query($sql);
		while($db->next_record())
		{
			$menu_id = $db->f("submenu_id");
			$menu_order = $db->f("submenu_order");
			$menu_title = get_translation($db->f("submenu_title"), $language_code);
			$shown_header_menus[] = array($menu_id, $menu_title);
		}
	}

	set_options($shown_header_menus, "", "shown_header_menus");

	$t->set_var("errors", "");

	include("./admin_header.php");
	include("./admin_footer.php");

	$t->pparse("main");

?>