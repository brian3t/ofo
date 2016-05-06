<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_menu_item_edit.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("layouts");

	$menu_id = get_param("menu_id");
	$return_page = get_param("return_page");
	if ($return_page == "") {
		$return_page = "admin_layouts.php";
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_menu_item_edit.html");
	
	// Get menu_id and check if it exists
	// Get menu_id and check if it exists
	$sql  = " SELECT * ";
	$sql .= " FROM ".$table_prefix."menus ";
	$sql .= " WHERE menu_id = ".$db->tosql($menu_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$t->set_var("parent_menu_name", $db->f("menu_name"));
		$t->set_var("parent_menu_title", $db->f("menu_title"));
		$t->set_var("parent_menu_id", $menu_id);
	} else {
		header("Location: admin_custom_menus.php");
		exit;
	}

	$t->set_var("admin_href"      , "admin.php");
	$t->set_var("admin_layout_href", "admin_layout.php");
	$t->set_var("admin_menu_item_edit_href" , "admin_menu_item_edit.php");
	$t->set_var("admin_menu_href"  , "admin_menu_items.php?menu_id=".$menu_id);
	$t->set_var("admin_custom_menu_href", "admin_custom_menu.php");
	$t->set_var("admin_custom_menus_href", "admin_custom_menus.php");
	$t->set_var("admin_menu_items_href", "admin_menu_items.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_MENU_ITEM_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "menus_items");
	$r->return_page = "admin_menu_items.php?menu_id=" . $menu_id;
	
	$r->add_where("menu_item_id", INTEGER);
	$r->add_textbox("menu_id", INTEGER, MENU_ID_MSG);
	$r->add_textbox("menu_title", TEXT, MENU_TITLE_MSG);
	$r->add_textbox("menu_image", TEXT, MENU_IMAGE_MSG);
	$r->add_textbox("menu_image_active", TEXT, MENU_IMAGE_ACTIVE_MSG);
	$r->add_textbox("menu_url", TEXT, TARGET_URL_MSG);
	$r->add_textbox("menu_prefix", TEXT, PREFIX_IMAGE_MSG);
	$r->add_textbox("menu_prefix_active", TEXT, ACTIVE_PREFIX_IMAGE_MSG);
	$r->change_property("menu_url", REQUIRED, true);
	$r->add_checkbox("show_non_logged", INTEGER);
	$r->change_property("show_non_logged", DEFAULT_VALUE, 1);
	$r->add_checkbox("show_logged", INTEGER);
	$r->change_property("show_logged", DEFAULT_VALUE, 1);
	$r->add_textbox("menu_prefix_active", TEXT, ACTIVE_PREFIX_IMAGE_MSG);
	$r->add_textbox("menu_order", INTEGER, MENU_ORDER_MSG);
	
	$targets = array();
	$targets[] = array("_self", OPEN_IN_THE_SAME_MSg);
	$targets[] = array("_blank", OPEN_IN_NEW_WINDOW_MSG);
	
	$r->add_select("menu_target", TEXT, $targets, MENU_TARGET_MSG);	
	
	$menu = array();
	//-- parent items
	$sql  = " SELECT * FROM " . $table_prefix . "menus_items ";
	$sql .= " WHERE menu_id=" . $db->tosql($menu_id, INTEGER);
	$sql .= " ORDER BY menu_path, menu_order ";
	$db->query($sql);
	
	while($db->next_record()) {
		$list_id = $db->f("menu_item_id");
		$parent_menu_id = $db->f("parent_menu_item_id");

		if ($parent_menu_id == "") {
			$parent_menu_id = 0;
		}

		$list_title = $db->f("menu_title");
		if (defined($list_title)) {
			$list_title = constant($list_title);
		}

		$menu_values = array(
			"menu_title" => $list_title, 
			"menu_url" => $db->f("menu_url"), 
			"menu_path" => $db->f("menu_path")
		);
		$menu[$list_id] = $menu_values;
		
		$menu[$parent_menu_id]["subs"][] = $list_id;
	}

	// Generate js arrays with menus hierarchy structure
	$script_code = "";
	if (is_array($menu)) {
		foreach ($menu as $item_id => $info) {
			$script_code .= "menu_item_ids[".$item_id."] = Array();\r\n";
			$script_code .= "menu_item_titles[".$item_id."] = Array();\r\n";
			if (isset($info["subs"])) {
				$subitems = $info["subs"];
				if (!empty($subitems)) {
					$counter = 0;
					foreach ($subitems as $subitem_id) {
						$script_code .= "menu_item_ids[".$item_id."][".$counter.'] = "'.$subitem_id.'";'."\r\n";
						$script_code .= "menu_item_titles[".$item_id."][".$counter.'] = "'.str_replace("\"","\\\"",$menu[$subitem_id]["menu_title"]).'";'."\r\n";
						$counter++;
					}
				}
			}
		}
	}
	$t->set_var("script_code", $script_code);
	// -------------------------------------------------
	$items = array();
	build_menu(0);

	$r->add_select("parent_menu_item_id", TEXT, $items, PARENT_ITEM_MSG);
	$r->set_event(AFTER_INSERT, "build_menus_tree");
	$r->set_event(AFTER_UPDATE, "build_menus_tree");
	
	$r->set_event(BEFORE_UPDATE, "set_values_before_tbl_changes");
	$r->set_event(BEFORE_INSERT, "set_values_before_tbl_changes");
	$r->process();

	// $before_menu_item_id value defines which item is before current
	$sql = "SELECT menu_item_id FROM ".$table_prefix."menus_items ";
	$sql .= " WHERE menu_id = ".$db->tosql($r->get_value("menu_id"), INTEGER);
	$sql .= " AND parent_menu_item_id=".$db->tosql($r->get_value("parent_menu_item_id"), INTEGER);
	$sql .= " AND menu_order>".$db->tosql($r->get_value("menu_order"), INTEGER);
	$sql .= " ORDER BY menu_order ASC";
	$db->query($sql);
	
	if($db->next_record()) {
		$before_menu_item_id = $db->f("menu_item_id");
	} else {
		$before_menu_item_id = 0;
	}

	$t->set_var("before_menu_item_id", $before_menu_item_id);
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("menu_id", $menu_id);
	$t->pparse("main");

function spaces_level($level)
{
	$spaces = "";
	for ($i =1; $i <= $level; $i++) {
		$spaces .= "---";
	}
	return $spaces . " ";
}

/**
 * Set additional data (order of the item) before record would be saved.
 *
 */
function set_values_before_tbl_changes() {
	global $r, $db, $table_prefix, $menu_id;
	//var_dump($r->get_value("menu_item_prefix"));
	// Change order
	// Get id of the item, which has to be after current items
	$after_item_id = get_param("order_select");
	$parent_item_id = get_param("parent_menu_item_id");
	
	$before_menu_item_id = get_param("before_menu_item_id");
	// Check if before_menu_item_id is equal to menu_order, then user has not changed order, otherwise change it
	if ($before_menu_item_id != $after_item_id || intval($before_menu_item_id) == 0) {
		if ($after_item_id == "" || $after_item_id == 0) {
			// Get max order for current level
			$sql = "SELECT MAX(menu_order) AS max_order FROM ".$table_prefix."menus_items ";
			$sql .= "WHERE parent_menu_item_id=".$db->tosql($parent_item_id, INTEGER);
			$sql .= " AND menu_id=".$db->tosql($menu_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$max_order = $db->f("max_order");
		
				// Set menu_order as max_order + 1
				$r->set_value("menu_order", ++$max_order);
			} else {
				$r->set_value("menu_order", 0);
			}
		} else {
			// Get order value for selected item.
			// Current item has to be placed before selected.
			$sql = "SELECT menu_order FROM ".$table_prefix."menus_items ";
			$sql .= "WHERE menu_item_id=".$db->tosql($after_item_id, INTEGER);
			$db->query($sql);
			if ($db->next_record()) {
				$order = $db->f("menu_order");
				// Set menu_item_level as max_order + 1
				$r->set_value("menu_order", $order);
				// Increase menu_order of selected item and all other, which have
				// menu_item_order greater than selected and the same parent_id
				$increase_order_sql = "UPDATE ".$table_prefix."menus_items ";
				$increase_order_sql .= "SET menu_order=menu_order+1 ";
				$increase_order_sql .= "WHERE parent_menu_item_id = ".$db->tosql($parent_item_id, INTEGER);
				$increase_order_sql .= " AND menu_order >=".$db->tosql($order, INTEGER);
		
				$db->query($increase_order_sql);
			}
		}
	}
}

/**
 * Get menu items data from database.
 *
 */
function build_menus_tree()
{
	global $db, $table_prefix, $layout_id, $menu_id;

	// update menu links for new structure
	$items = array();
	$sql  = " SELECT menu_item_id, parent_menu_item_id FROM " . $table_prefix . "menus_items ";
	$sql .= " WHERE menu_id=" . $db->tosql($menu_id, INTEGER);
	$sql .= " ORDER BY menu_item_id ";
	$db->query($sql);
	while ($db->next_record()) {
		$menu_item_id = $db->f("menu_item_id");
		$parent_menu_item_id = $db->f("parent_menu_item_id");
		$items[$menu_item_id] = $parent_menu_item_id;
	}
	
	foreach ($items as $menu_item_id => $parent_menu_item_id) {
		if (!$parent_menu_item_id || $parent_menu_item_id == $menu_item_id) {
			$parent_menu_item_id = 0;
		}
		
		$menu_item_path = ""; 
		$current_parent_item_id = $parent_menu_item_id;

		while ($current_parent_item_id) {
			$menu_item_path = $current_parent_item_id.",".$menu_item_path;
			$parent_item_id = isset($items[$current_parent_item_id]) ? $items[$current_parent_item_id] : 0;
			if ($parent_item_id == $current_parent_item_id) {
				$current_parent_item_id = 0;
			} else {
				$current_parent_item_id = $parent_item_id;
			}
		}
		
		$sql  = " UPDATE " . $table_prefix . "menus_items SET ";
		$sql .= " parent_menu_item_id=" . $db->tosql($parent_menu_item_id, INTEGER) . ", ";
		$sql .= " menu_path=" . $db->tosql($menu_item_path, TEXT);
		$sql .= " WHERE menu_item_id=" . $db->tosql($menu_item_id, INTEGER);
		$db->query($sql);
	}	

}

/**
 * Build menu for item id hierarchy 
 *
 * @param integer $parent_id
 */
function build_menu($parent_id) {

	global $t, $menu, $items, $menu_id, $selected_menu_item_id;
	if (!empty($menu)) {
		$subs = $menu[$parent_id]["subs"];
		for ($m = 0; $m < sizeof($subs); $m++) {
			$item_id = $subs[$m];
			if ($item_id != $selected_menu_item_id) {	
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