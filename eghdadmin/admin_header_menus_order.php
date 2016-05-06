<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_header_menus_order.php                             ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once("./admin_common.php");

	check_admin_security("site_navigation");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_header_menus_order.html");
  $t->set_var("admin_header_menus_href", "admin_header_menus_order.php");

	$shown_header_menus = array();

	$operation = get_param("operation");
	$return_page = "admin_header_menus.php";

	$t->set_var("admin_layout_href", "admin_layout.php");
	$t->set_var("admin_header_menus_href", "admin_header_menus.php");

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
				$sql  = " UPDATE " . $table_prefix . "header_links SET menu_order=" . intval($i + 1);				
				$sql .= " WHERE menu_id=" . $shown_header_menus[$i];
				$db->query($sql);
			}
			header("Location: " . $return_page);
			exit;
		}
	}
	else
	{
		$sql  = " SELECT hl.menu_id, hl.menu_title, hl.menu_order,l.layout_id, l.layout_name ";
		$sql .= " FROM (" . $table_prefix . "header_links hl ";
		$sql .= " LEFT JOIN " . $table_prefix . "layouts l ON hl.layout_id=l.layout_id) ";
		$sql .= " ORDER BY hl.menu_order, hl.menu_id DESC ";
		$db->query($sql);
		while($db->next_record())
		{
			$menu_id = $db->f("menu_id");
			$menu_order = $db->f("menu_order");
			$layout_name = $db->f("layout_name");
			$layout_id = $db->f("layout_id");
			if (!$layout_id) {
				$layout_name = ALL_MSG;
			}
			$menu_title = get_translation($db->f("menu_title"));
			$menu_title .= " (" . $layout_name . ")";

			$shown_header_menus[] = array($menu_id, $menu_title);
		}
	}

	set_options($shown_header_menus, "", "shown_header_menus");

	$t->set_var("errors", "");

	include("./admin_header.php");
	include("./admin_footer.php");

	$t->pparse("main");

?>