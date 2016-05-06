<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_menu_submenus.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/sorter.php");
	include_once ($root_folder_path . "includes/navigator.php");
	include_once("./admin_common.php");

	check_admin_security("site_navigation");

	$menu_id = get_param("menu_id");

	$sql  = " SELECT l.layout_id, l.layout_name, hl.menu_title ";
	$sql .= " FROM (" . $table_prefix . "header_links hl ";
	$sql .= " LEFT JOIN " . $table_prefix . "layouts l ON hl.layout_id=l.layout_id) ";
	$sql .= " WHERE hl.menu_id=" . $db->tosql($menu_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$layout_id = $db->f("layout_id");
		$layout_name = $db->f("layout_name");
		$menu_title = $db->f("menu_title");
	} else {
		header("Location: admin_header_menus.php");
		exit;
	}

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_menu_submenus.html");
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_layout_href", "admin_layout.php");
	$t->set_var("admin_layouts_href", "admin_layouts.php");
	$t->set_var("admin_menu_item_href", "admin_menu_item.php");
	$t->set_var("admin_menu_submenus_href", "admin_menu_submenus.php");
	$t->set_var("admin_menu_submenu_href", "admin_menu_submenu.php");
	$t->set_var("admin_header_menus_href", "admin_header_menus.php");
	$t->set_var("admin_header_submenus_order_href", "admin_header_submenus_order.php?menu_id=".$menu_id);
	$t->set_var("menu_id", $menu_id);
	$t->set_var("menu_title", get_translation($menu_title));
	$t->set_var("layout_id", $layout_id);
	$t->set_var("layout_name", $layout_name);

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_authors.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$sql  = " SELECT * FROM " . $table_prefix . "header_submenus WHERE menu_id=" . $db->tosql($menu_id, INTEGER);
	$sql .= " ORDER BY submenu_path, submenu_order ";
	$db->query($sql);
	while ($db->next_record()) {
		$submenu_id = $db->f("submenu_id");
		$parent_submenu_id = $db->f("parent_submenu_id");
		if ($submenu_id == $parent_submenu_id) {
			$parent_submenu_id = 0;
		}
		$submenu_values = array(
			"submenu_id" => $submenu_id, "submenu_url" => $db->f("submenu_url"), 
			"submenu_title" => $db->f("submenu_title"), "submenu_path" => $db->f("submenu_path")
		);
		$submenu[$submenu_id] = $submenu_values;
		$submenu[$parent_submenu_id]["subs"][] = $submenu_id;
	}

	$submenu_count = 0;
	show_submenu(0);

	$t->pparse("main");

	function show_submenu($parent_id) {
		global $t, $submenu, $submenu_count;
		$subs = $submenu[$parent_id]["subs"];
		for ($m = 0; $m < sizeof($subs); $m++) {
			$submenu_count++;
			$submenu_id = $subs[$m];
			$submenu_path = $submenu[$submenu_id]["submenu_path"];
			$submenu_title = $submenu[$submenu_id]["submenu_title"];
			$submenu_title = get_translation($submenu_title);
			$submenu_url = $submenu[$submenu_id]["submenu_url"];
			$submenu_level = preg_replace("/\d/", "", $submenu_path);
			$spaces = spaces_level(strlen($submenu_level));

			$t->set_var("submenu_count", $submenu_count);
			$t->set_var("submenu_id"   , $submenu_id);
			$t->set_var("submenu_title", $spaces . $submenu_title);
			$t->set_var("submenu_url"  , htmlspecialchars($submenu_url));

			$t->parse("records", true);

			if (isset($submenu[$submenu_id]["subs"])) {
				show_submenu($submenu_id);
			}
		}
	}

    
	function spaces_level($level)
	{
		$spaces = "";
		for ($i = 0; $i < $level; $i++) {
			$spaces .= " &nbsp; &nbsp; &nbsp; ";
		}
		$spaces .= "<font style='font-size:".(12-$level)."'>";
		return $spaces;
	}

?>