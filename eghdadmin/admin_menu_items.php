<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_menu_items.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once("./admin_common.php");

	check_admin_security("layouts");

	$menu_id = get_param("menu_id");
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_menu_items.html");

	// Get menu_id and check if it exists
	$sql  = " SELECT * ";
	$sql .= " FROM ".$table_prefix."menus ";
	$sql .= " WHERE menu_id = ".$db->tosql($menu_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$t->set_var("parent_menu_name", $db->f("menu_name"));
		$t->set_var("parent_menu_title", $db->f("menu_title"));
		$t->set_var("parent_menu_id", $menu_id);
		$t->set_var("menu_id", $menu_id);
	} else {
		header("Location: admin_custom_menus.php");
		exit;
	}

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_layout_href", "admin_layout.php");
	$t->set_var("admin_item_edit", "admin_menu_item_edit.php?menu_id=".$menu_id);
	$t->set_var("admin_custom_menu_href", "admin_custom_menu.php");
	$t->set_var("admin_custom_menus_href", "admin_custom_menus.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	
	$menus_items_tbl_name = $table_prefix . "menus_items";
	
	$sql = " SELECT * FROM ";
	$sql .= $menus_items_tbl_name." ";
	$sql .= "WHERE menu_id=" . $db->tosql($menu_id, INTEGER);
	$sql .= " ORDER BY menu_path, menu_order ";
	$db->query($sql);
	$menus = array();
	
	while($db->next_record()) {
		$item_id = $db->f("menu_item_id");
		$parent_item_id = $db->f("parent_menu_item_id");
		if ($item_id == $parent_item_id || $parent_item_id == "") {
			$parent_item_id = 0;
		}
		$menu_values = array(
			"menu_item_id" => $item_id, 
			"parent_menu_item_id" => $parent_item_id, 
			"menu_path" => $db->f("menu_path"), 
			"menu_title" => $db->f("menu_title"), 
			"menu_url" => $db->f("menu_url"), 
		);
		$menus[$item_id] = $menu_values;
		$menus[$parent_item_id]["subs"][] = $item_id;
	}

	$menu_count = 0;
	if (!empty($menus)) {
		show_menu(0);
	}
	else {
		$t->parse("no_records", false);
	}

	$t->pparse("main");

	function show_menu($parent_id) {
		global $t, $menus, $menu_count;
		$subs = $menus[$parent_id]["subs"];
		for ($m = 0; $m < sizeof($subs); $m++) {
			$menu_count++;
			$menu_id = $subs[$m];
			$menu_path = $menus[$menu_id]["menu_path"];
			$menu_title = $menus[$menu_id]["menu_title"];
			$menu_title = get_translation($menu_title);
			if (defined($menu_title)) {
				$menu_title = constant($menu_title);
			}
			$menu_url = $menus[$menu_id]["menu_url"];
			$menu_level = preg_replace("/\d/", "", $menu_path);
			$spaces = spaces_level(strlen($menu_level));

			$t->set_var("menu_count", $menu_count);
			$t->set_var("menu_item_id"   , $menu_id);
			$t->set_var("menu_title", $spaces . $menu_title);
			$t->set_var("menu_url"  , htmlspecialchars($menu_url));
			$t->parse("records", true);

			if (isset($menus[$menu_id]["subs"])) {
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