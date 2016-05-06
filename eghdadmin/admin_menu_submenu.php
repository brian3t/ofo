<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_menu_submenu.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once("./admin_common.php");

	check_admin_security("site_navigation");

	$menu_id = get_param("menu_id");
	$submenu_id = get_param("submenu_id");

	if ($menu_id) {
		$sql  = " SELECT l.layout_id, l.layout_name, hl.menu_id, hl.menu_title ";
		$sql .= " FROM (" . $table_prefix . "header_links hl ";
		$sql .= " LEFT JOIN " . $table_prefix . "layouts l ON hl.layout_id=l.layout_id) ";
		$sql .= " WHERE hl.menu_id=" . $db->tosql($menu_id, INTEGER);
	} else {
		$sql  = " SELECT l.layout_id, l.layout_name, hl.menu_id, hl.menu_title ";
		$sql .= " FROM ((" . $table_prefix . "header_links hl ";
		$sql .= " INNER JOIN " . $table_prefix . "header_submenus hs ON hs.menu_id=hl.menu_id) ";
		$sql .= " LEFT JOIN " . $table_prefix . "layouts l ON hl.layout_id=l.layout_id) ";
		$sql .= " WHERE hs.submenu_id=" . $db->tosql($submenu_id, INTEGER);
	}
	$db->query($sql);
	if ($db->next_record()) {
		$layout_id = $db->f("layout_id");
		$layout_name = $db->f("layout_name");
		$menu_id = $db->f("menu_id");
		$menu_title = $db->f("menu_title");
	} else {
		header("Location: admin_layouts.php");
		exit;
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_menu_submenu.html");
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_layout_href", "admin_layout.php");
	$t->set_var("admin_layouts_href", "admin_layouts.php");
	$t->set_var("admin_menu_item_href", "admin_menu_item.php");
	$t->set_var("admin_menu_submenus_href", "admin_menu_submenus.php");
	$t->set_var("admin_menu_submenu_href", "admin_menu_submenu.php");
	$t->set_var("admin_header_menus_href", "admin_header_menus.php");
	$t->set_var("menu_id", $menu_id);
	$t->set_var("menu_title", get_translation($menu_title));
	$t->set_var("layout_id", $layout_id);
	$t->set_var("layout_name", $layout_name);
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_MENU_ITEM_MSG, CONFIRM_DELETE_MSG));

	$show_values = array(
		array(0, DONT_SHOW_MSG),
		array(1, SHOW_FOR_ALL_USERS_MSG),
		array(2, SHOW_FOR_REGISTERED_USERS_MSG),
		array(3, SHOW_FOR_UNREGISTERED_USERS_MSG),
	);

	$match_types = array(
		array(0, DON’T_MATCH_WITH_ITEM_MSG),
		array(1, MATCH_PAGE_NAME_ONLY_MSG),
		array(2, MATCH_PAGE_NAME_PARAMETERS_MSG),
	);

	if (!$submenu_id) {
		$sql = "SELECT MAX(submenu_order) FROM " . $table_prefix . "header_submenus WHERE menu_id=" . $db->tosql($menu_id, INTEGER);
		$submenu_order = get_db_value($sql);
		$submenu_order++;
	} else {
		$submenu_order = 1;
	}

	//-- parent items
	$sql  = " SELECT * FROM " . $table_prefix . "header_submenus ";
	$sql .= " WHERE menu_id=" . $db->tosql($menu_id, INTEGER);
	$sql .= " ORDER BY submenu_path, submenu_order ";
	$db->query($sql);
	while($db->next_record()) {
		$list_id = $db->f("submenu_id");
		$parent_menu_id = $db->f("parent_submenu_id");
		if ($parent_menu_id == $list_id) {
			$parent_menu_id = 0;
		}
		$list_title = get_translation($db->f("submenu_title"));

		$submenu_values = array(
			"submenu_title" => $list_title, "submenu_url" => $db->f("submenu_url"), "submenu_path" => $db->f("submenu_path")
		);
		$submenu[$list_id] = $submenu_values;
		$submenu[$parent_menu_id]["subs"][] = $list_id;
	}

	$submenu_items = array();
	build_submenu(0);


	$r = new VA_Record($table_prefix . "header_submenus");
	$r->return_page = "admin_menu_submenus.php?menu_id=" . $menu_id;

	$r->add_where("submenu_id", INTEGER);
	//$r->add_textbox("parent_submenu_id", INTEGER);
	$r->add_select("parent_submenu_id", TEXT, $submenu_items, PARENT_ITEM_MSG);
	$r->add_textbox("menu_id", INTEGER);
	$r->change_property("menu_id", DEFAULT_VALUE, $menu_id);
	$r->add_textbox("submenu_order", INTEGER, SUBMENU_ORDER_MSG);
	$r->change_property("submenu_order", DEFAULT_VALUE, $submenu_order);
	$r->change_property("submenu_order", REQUIRED, true);

	$r->add_textbox("submenu_title", TEXT, TITLE_MSG);
	$r->change_property("submenu_title", REQUIRED, true);
	$r->add_textbox("submenu_url", TEXT, ADMIN_URL_SHORT_MSG);
	$r->change_property("submenu_url", REQUIRED, true);
	$r->add_textbox("submenu_target", TEXT, ADMIN_TARGET_MSG);
	$r->add_textbox("submenu_page", TEXT);

	$r->add_radio("show_for_user", INTEGER, $show_values);
	$r->change_property("show_for_user", DEFAULT_VALUE, 1);
	$r->add_radio("match_type", INTEGER, $match_types);
	$r->change_property("match_type", DEFAULT_VALUE, 2);

	$r->add_textbox("submenu_image", TEXT, MENU_IMAGE_MSG);
	$r->add_textbox("submenu_image_active", TEXT, MENU_IMAGE_ACTIVE_MSG);

	$r->set_event(BEFORE_INSERT, "set_submenu_page");
	$r->set_event(BEFORE_UPDATE, "set_submenu_page");
	$r->set_event(AFTER_INSERT, "build_submenus_tree");
	$r->set_event(AFTER_UPDATE, "build_submenus_tree");
	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function set_submenu_page()
	{
		global $r;

		$submenu_url = $r->get_value("submenu_url");
		$parsed_url = parse_url($submenu_url);
		$submenu_page = isset($parsed_url["path"]) ? $parsed_url["path"] : "/";

		$r->set_value("submenu_page", $submenu_page);
		//$r->set_value("parent_submenu_id", 0);
	}


	function spaces_level($level)
	{
		$spaces = "";
		for ($i =1; $i <= $level; $i++) {
			$spaces .= "---";
		}
		return $spaces . " ";
	}


	function build_submenus_tree()
	{
		global $db, $table_prefix, $menu_id;
  
		// update submenu links for new structure
		$header_submenus = array();
		$sql  = " SELECT submenu_id, parent_submenu_id FROM " . $table_prefix . "header_submenus ";
		$sql .= " WHERE menu_id=" . $db->tosql($menu_id, INTEGER);
		$sql .= " ORDER BY submenu_id ";
		$db->query($sql);
		while ($db->next_record()) {
			$submenu_id = $db->f("submenu_id");
			$parent_submenu_id = $db->f("parent_submenu_id");
			$header_submenus[$submenu_id] = $parent_submenu_id;
		}
		foreach ($header_submenus as $submenu_id => $parent_submenu_id) {
			if (!$parent_submenu_id || $parent_submenu_id == $submenu_id) {
				$parent_submenu_id = 0;
			}
			$submenu_path = ""; $current_parent_id = $parent_submenu_id;
			while ($current_parent_id) {
				$submenu_path = $current_parent_id.",".$submenu_path;
				$parent_id = isset($header_submenus[$current_parent_id]) ? $header_submenus[$current_parent_id] : 0;
				if ($parent_id == $current_parent_id) {
					$current_parent_id = 0;
				} else {
					$current_parent_id = $parent_id;
				}
			}
			$sql  = " UPDATE " . $table_prefix . "header_submenus SET ";
			$sql .= " parent_submenu_id=" . $db->tosql($parent_submenu_id, INTEGER) . ", ";
			$sql .= " submenu_path=" . $db->tosql($submenu_path, TEXT);
			$sql .= " WHERE submenu_id=" . $db->tosql($submenu_id, INTEGER);
			$db->query($sql);
		}	
	}

	function build_submenu($parent_id) 
	{
		global $t, $submenu, $submenu_items, $submenu_id;
		if (isset($submenu[$parent_id])) {
			$subs = $submenu[$parent_id]["subs"];
			for ($m = 0; $m < sizeof($subs); $m++) {
				$sub_id = $subs[$m];
				if ($submenu_id != $sub_id) {
					$submenu_path = $submenu[$sub_id]["submenu_path"];
					$submenu_title = $submenu[$sub_id]["submenu_title"];
					$submenu_level = preg_replace("/\d/", "", $submenu_path);
					$spaces = spaces_level(strlen($submenu_level));
          
					$submenu_items[] = array($sub_id, $spaces . $submenu_title);
          
					if (isset($submenu[$sub_id]["subs"])) {
						build_submenu($sub_id);
					}
				}
			}
		}
	}


?>