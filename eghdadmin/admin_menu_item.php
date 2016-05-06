<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_menu_item.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("site_navigation");

	$menu_id = get_param("menu_id");
	$return_page = get_param("return_page");

	$match_types = array(
		array(0, DON’T_MATCH_WITH_ITEM_MSG),
		array(1, MATCH_PAGE_NAME_ONLY_MSG),
		array(2, MATCH_PAGE_NAME_PARAMETERS_MSG),
	);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_menu_item.html");

	$layouts = get_db_values("SELECT layout_id,layout_name FROM " . $table_prefix . "layouts ORDER BY layout_id ", array(array("0", ALL_MSG)));
	$current_layout = get_db_value("SELECT layout_id FROM " . $table_prefix . "header_links WHERE menu_id=" . $db->tosql($menu_id, INTEGER));
	$t->set_var("current_layout_id", $current_layout);

	$t->set_var("admin_href"      , "admin.php");
	$t->set_var("admin_pages_href", "admin_header_menus.php");
	$t->set_var("admin_page_href" , "admin_menu_item.php");
	$t->set_var("admin_layout_href", "admin_layout.php");
	$t->set_var("admin_menu_href"  , "admin_header_menus.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");

	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_MENU_ITEM_MSG, CONFIRM_DELETE_MSG));

	if (!$menu_id) {
		$sql = "SELECT MAX(menu_order) FROM " . $table_prefix . "header_links ";
		$menu_order = get_db_value($sql);
		$menu_order++;
	} else {
		$menu_order = 1;
	}

	$r = new VA_Record($table_prefix . "header_links");
	$r->return_page = "admin_header_menus.php";

	$r->add_where("menu_id", INTEGER);
	$r->add_select("layout_id", INTEGER, $layouts, LAYOUT_ID_MSG);

	$r->add_textbox("menu_order", INTEGER, MENU_ORDER_MSG);
	$r->change_property("menu_order", DEFAULT_VALUE, $menu_order);
	$r->add_textbox("menu_title", TEXT, MENU_TITLE_MSG);
	$r->add_textbox("menu_url", TEXT, ADMIN_URL_SHORT_MSG);
	$r->add_textbox("menu_page", TEXT);
	$r->add_textbox("menu_target", TEXT, ADMIN_TARGET_MSG);
	$r->add_textbox("submenu_style_name", TEXT);
	$r->add_radio("match_type", INTEGER, $match_types);
	$r->change_property("match_type", DEFAULT_VALUE, 2);

	$r->add_textbox("menu_image", TEXT, MENU_IMAGE_MSG);
	$r->add_textbox("menu_image_active", TEXT, MENU_IMAGE_ACTIVE_MSG);

	$r->change_property("menu_url", REQUIRED, true);
	$r->add_checkbox("show_non_logged", INTEGER);
	$r->parameters["show_non_logged"][DEFAULT_VALUE] = 1;
	$r->add_checkbox("show_logged", INTEGER);
	$r->parameters["show_logged"][DEFAULT_VALUE] = 1;

/*
	$current_layout_id = $r->get_value(layout_id);
	$current_layout = get_db_value("SELECT style_name FROM " . $table_prefix . "layouts WHERE layout_id=" . $db->tosql($layout_id, INTEGER));
echo $current_layout;
*/

	//-- parent items
	$sql  = " SELECT hl.*,l.layout_id, l.layout_name ";
	$sql .= " FROM (" . $table_prefix . "header_links hl ";
	$sql .= " LEFT JOIN " . $table_prefix . "layouts l ON hl.layout_id=l.layout_id) ";
	$sql .= " ORDER BY hl.menu_path, hl.menu_order ";
	$db->query($sql);
	while($db->next_record()) {
		$list_id = $db->f("menu_id");
		$layout_id = $db->f("layout_id");
		$layout_name = get_translation($db->f("layout_name"));
		if (!$layout_id) {
			$layout_name = ALL_MSG;
		}
		$parent_menu_id = $db->f("parent_menu_id");
		if ($parent_menu_id == $list_id) {
			$parent_menu_id = 0;
		}
		$list_title = $db->f("menu_title");
		if (defined($list_title)) {
			$list_title = constant($list_title);
		}
		$list_title .= " (" . $layout_name . ")";

		$menu_values = array(
			"menu_title" => $list_title, "menu_url" => $db->f("menu_url"), "menu_path" => $db->f("menu_path")
		);
		$menu[$list_id] = $menu_values;
		$menu[$parent_menu_id]["subs"][] = $list_id;
	}

	$items = array();
	build_menu(0);

	$r->add_select("parent_menu_id", TEXT, $items, PARENT_ITEM_MSG);

	$r->set_event(BEFORE_INSERT, "set_menu_page");
	$r->set_event(BEFORE_UPDATE, "set_menu_page");
	$r->set_event(AFTER_INSERT, "build_menus_tree");
	$r->set_event(AFTER_UPDATE, "build_menus_tree");
	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function set_menu_page()
	{
		global $r;

		$menu_url = $r->get_value("menu_url");
		$parsed_url = parse_url($menu_url);
		$menu_page = isset($parsed_url["path"]) ? $parsed_url["path"] : "/";

		$r->set_value("menu_page", $menu_page);
	}

function spaces_level($level)
{
	$spaces = "";
	for ($i =1; $i <= $level; $i++) {
		$spaces .= "---";
	}
	return $spaces . " ";
}


function build_menus_tree()
{
	global $db, $table_prefix;

	// update menu links for new structure
	$header_links = array();
	$sql  = " SELECT menu_id, parent_menu_id ";
	$sql .= " FROM " . $table_prefix . "header_links ";
	$sql .= " ORDER BY menu_id ";
	$db->query($sql);
	while ($db->next_record()) {
		$menu_id = $db->f("menu_id");
		$parent_menu_id = $db->f("parent_menu_id");
		$header_links[$menu_id] = $parent_menu_id;
	}
	foreach ($header_links as $menu_id => $parent_menu_id) {
		if (!$parent_menu_id || $parent_menu_id == $menu_id) {
			$parent_menu_id = 0;
		}
		$menu_path = ""; $current_parent_id = $parent_menu_id;
		while ($current_parent_id) {
			$menu_path = $current_parent_id.",".$menu_path;
			$parent_id = isset($header_links[$current_parent_id]) ? $header_links[$current_parent_id] : 0;
			if ($parent_id == $current_parent_id) {
				$current_parent_id = 0;
			} else {
				$current_parent_id = $parent_id;
			}
		}
		$sql  = " UPDATE " . $table_prefix . "header_links SET ";
		$sql .= " parent_menu_id=" . $db->tosql($parent_menu_id, INTEGER) . ", ";
		$sql .= " menu_path=" . $db->tosql($menu_path, TEXT);
		$sql .= " WHERE menu_id=" . $db->tosql($menu_id, INTEGER);
		$db->query($sql);
	}	

}

function build_menu($parent_id) {
	global $t, $menu, $items, $menu_id;
	if (isset($menu[$parent_id])) {
		$subs = $menu[$parent_id]["subs"];
		for ($m = 0; $m < sizeof($subs); $m++) {
			$item_id = $subs[$m];
			if ($menu_id != $item_id) {
				$menu_path = $menu[$item_id]["menu_path"];
				$menu_title = $menu[$item_id]["menu_title"];
				if (!$menu_title) {
					$menu_title = $menu[$item_id]["menu_url"];
				}
				$menu_level = preg_replace("/\d/", "", $menu_path);
				$spaces = spaces_level(strlen($menu_level));
        
				$items[] = array($item_id, $spaces.$menu_title);
        
				if (isset($menu[$item_id]["subs"])) {
					build_menu($item_id);
				}
			}
		}
	}
}

?>