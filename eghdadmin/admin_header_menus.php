<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_header_menus.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");	
	include_once("./admin_common.php");

	check_admin_security("site_navigation");

	$set_default = get_param("set_default"); //-- set this view by default

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_header_menus.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_menu_submenus_href","admin_menu_submenus.php");
	$t->set_var("admin_header_menus_order_href", "admin_header_menus_order.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_layout_href", "admin_layout.php");

	$admin_menu_item_url = new VA_URL("admin_menu_item.php");
	$admin_menu_item_url->add_parameter("layout_id", REQUEST, "layout_id");
	$t->set_var("admin_menu_item_new_url", $admin_menu_item_url->get_url());

	$admin_menus_order = new VA_URL("admin_header_menus_order.php");
	$admin_menus_order->add_parameter("layout_id", REQUEST, "layout_id");
	$t->set_var("admin_header_menus_order_url", $admin_menus_order->get_url());

	$admin_menu_submenus= new VA_URL("admin_menu_submenus.php");
	$admin_menu_submenus->add_parameter("layout_id", REQUEST, "layout_id");

	$layout_id = 0;
	$sql  = " SELECT hl.*,l.layout_id, l.layout_name ";
	$sql .= " FROM (" . $table_prefix . "header_links hl ";
	$sql .= " LEFT JOIN " . $table_prefix . "layouts l ON hl.layout_id=l.layout_id) ";
	$sql .= " ORDER BY hl.menu_path, hl.menu_order ";
	$db->query($sql);
	while ($db->next_record()) {
		$menu_id = $db->f("menu_id");
		$parent_menu_id = $db->f("parent_menu_id");
		$layout_name = $db->f("layout_name");
		$layout_id = $db->f("layout_id");
		if (!$layout_id) {
			$layout_name = ALL_MSG;
		}
		if ($menu_id == $parent_menu_id) {
			$parent_menu_id = 0;
		}
		$menu_values = array(
			"menu_id" => $menu_id, "menu_url" => $db->f("menu_url"), 
			"menu_title" => $db->f("menu_title"), "menu_path" => $db->f("menu_path"),
			"layout_name" => $layout_name,
		);
		$menu[$menu_id] = $menu_values;
		$menu[$parent_menu_id]["subs"][] = $menu_id;
	}

	$menu_count = 0;
	show_menu(0);

	$t->pparse("main");

	function show_menu($parent_id) 
	{
		global $t, $menu, $menu_count, $admin_menu_item_url, $admin_menu_submenus;

		$subs = $menu[$parent_id]["subs"];
		for ($m = 0; $m < sizeof($subs); $m++) {
			$menu_count++;
			$menu_id = $subs[$m];
			$menu_path = $menu[$menu_id]["menu_path"];
			$menu_title = $menu[$menu_id]["menu_title"];
			$layout_name = $menu[$menu_id]["layout_name"];
			$menu_title = get_translation($menu_title);
			if (defined($menu_title)) {
				$menu_title = constant($menu_title);
			}
			$menu_url = $menu[$menu_id]["menu_url"];
			$menu_level = preg_replace("/\d/", "", $menu_path);
			$spaces = spaces_level(strlen($menu_level));
			$admin_menu_item_url->add_parameter("menu_id", CONSTANT, $menu_id);
			$admin_menu_submenus->add_parameter("menu_id", CONSTANT, $menu_id);

			$t->set_var("menu_count", $menu_count);
			$t->set_var("menu_id"   , $menu_id);
			$t->set_var("menu_title", $spaces . $menu_title);
			$t->set_var("layout_name", $layout_name);
			$t->set_var("menu_url"  , htmlspecialchars($menu_url));
			$t->set_var("admin_menu_item_url", $admin_menu_item_url->get_url());
			$t->set_var("admin_menu_submenus_url", $admin_menu_submenus->get_url());


			if ($parent_id) {
				$t->set_var("submenu_link", "");
			} else {
				$t->parse("submenu_link", false);
			}
			$t->parse("records", true);

			if (isset($menu[$menu_id]["subs"])) {
				show_menu($menu_id);
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