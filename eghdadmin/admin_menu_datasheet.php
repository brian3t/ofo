<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_menu_datasheet.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once("./admin_common.php");

	check_admin_security("layouts");
	$operation = get_param("operation");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_menu_datasheet.html");

	// Get menu_id and check if it exists
	$menu_id = get_param("menu_id");

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
	
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_menu_datasheet_href", "admin_menu_datasheet.php");
	$t->set_var("admin_custom_menu_href", "admin_custom_menu.php");
	$t->set_var("admin_custom_menus_href", "admin_custom_menus.php");
	$t->set_var("admin_layouts_href", "admin_layouts.php");
	$t->set_var("admin_layout_href", "admin_layout.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$return_page = get_param("rp");
	if(!strlen($return_page)) {
		$return_page = "admin_custom_menus.php";
	}
	
	$errors = "";
	//-- parent items
	$sql  = " SELECT * FROM " . $table_prefix . "menus_items ";
	$sql .= " WHERE menu_id=" . $db->tosql($menu_id, INTEGER);
	$sql .= " ORDER BY menu_path, menu_order ";
	$db->query($sql);
	
	$menu = array();
	
	while($db->next_record()) {
		$list_id = $db->f("menu_item_id");
		$parent_menu_item_id = $db->f("parent_menu_item_id");

		if ($parent_menu_item_id == "") {
			$parent_menu_item_id = 0;
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
		$menu[$parent_menu_item_id]["subs"][] = $list_id;
		
		if (!isset($menu[$list_id]["subs"]) || !is_array($menu[$list_id]["subs"])) {
			$menu[$list_id]["subs"] = array();
		}
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
						$script_code .= "menu_item_titles[".$item_id."][".$counter.'] = "'.$menu[$subitem_id]["menu_title"].'";'."\r\n";
						$counter++;
					}
				}
			}
		}
	}
	$t->set_var("script_code", $script_code);
	// -------------------------------------------------

	// set up html form parameters
	$r = new VA_Record($table_prefix . "menus_items", "links");
	$r->add_textbox("menu_id", INTEGER, MENU_ID_MSG);
	$r->add_where("menu_id", INTEGER);
	$r->change_property("menu_id", BEFORE_SHOW, "set_menu_id_event");
	$r->set_value("menu_id", $menu_id);
	
	$r->add_where("menu_item_id", INTEGER);
	
	//$r->add_textbox("menu_item_id", INTEGER, "Menu Item ID");
	$r->add_textbox("menu_title", TEXT, MENU_TITLE_MSG);
	$r->add_textbox("menu_url", TEXT, MENU_URL_MSG);
	$r->parameters["menu_url"][REQUIRED] = true;
	$r->add_textbox("menu_image", TEXT);
	$r->add_textbox("menu_image_active", TEXT);
	$r->add_textbox("menu_order", INTEGER, ITEM_ORDER_MSG);
	$r->change_property("menu_order", USE_IN_INSERT, false);
	$r->change_property("menu_order", USE_IN_UPDATE, false);
	$r->change_property("menu_order", USE_IN_SELECT, true);
	$r->add_hidden("menu_path", TEXT);
	$r->change_property("menu_path", USE_IN_INSERT, true);
	$r->change_property("menu_path", USE_IN_UPDATE, false);
	$r->change_property("menu_path", USE_SQL_NULL, false);
	$r->add_checkbox("show_non_logged", INTEGER);
	$r->add_checkbox("show_logged", INTEGER);
	
	$r->add_select("parent_menu_item_id", INTEGER, array(), PARENT_ITEM_MSG);
	$r->change_property("parent_menu_item_id", BEFORE_SHOW, "set_parent_select_values");
	
	$r->add_select("order_select", INTEGER, array(), MENU_ORDER_MSG);
	$r->change_property("order_select", BEFORE_SHOW, "set_order_values");
	$r->change_property("order_select", USE_IN_UPDATE, false);
	$r->change_property("order_select", USE_IN_INSERT, false);
	$r->change_property("order_select", USE_IN_SELECT, false);
	
	$r->set_event(BEFORE_UPDATE, "set_values_before_tbl_changes");
	$r->set_event(BEFORE_INSERT, "set_values_before_tbl_changes");
	
	$r->set_event(AFTER_INSERT, "build_menus_tree");
	$r->set_event(AFTER_UPDATE, "build_menus_tree");
	
	$more_links = get_param("more_links");
	$number_links = get_param("number_links");

	$eg = new VA_EditGrid($r, "links");
	$eg->get_form_values($number_links);

	if(strlen($operation) && !$more_links)
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		else if($operation == "delete")
		{
			$sql = "DELETE FROM " . $table_prefix . "menus_items WHERE menu_id=" . $db->tosql($menu_id, INTEGER); 
			$db->query($sql);
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $eg->validate(); 

		if($is_valid)
		{
			$eg->update_all($number_links);
			// Update paths and orders for current menu
			build_menus_tree();
			header("Location: " . $return_page);
			exit;
		}
	}
	else if(!$more_links)
	{
		$eg->change_property("menu_item_id", USE_IN_SELECT, true);
		$eg->change_property("menu_item_id", USE_IN_WHERE, false);
		$number_links = $eg->get_db_values();
		if($number_links == 0) 
		{
			$number_links = 5;
		}
	}
	else if($more_links)
	{
		$number_links += 5;
	}
	else
	{
		$number_links = 5;
	}

	$t->set_var("number_links", $number_links);
	// Next code initializes additional values of edit grid.
	if ($more_links) {
		for($i = 0; $i < $number_links; $i++) {
			$eg->values[] = array();
		}
	}
	
	// Set menu_id to the all records
	$eg->set_values("menu_id", $menu_id);
	$eg->set_parameters_all($number_links);

	$t->set_var("rp", htmlspecialchars($return_page));
	$t->set_var("menu_id", $menu_id);
	$t->pparse("main");
	
	/**
	 * Set menu_id before show form
	 *
	 */
	function set_menu_id_event() {
		global $eg, $menu_id;
		$eg->record->change_property("menu_id", CONTROL_VALUE, $menu_id);
	}

	/**
	 * Build menu for item id hierarchy 
	 *
	 * @param integer $parent_id
	 */
	function build_menu($parent_id, $selected_menu_item_id, &$items) {
	
		global $t, $menu, $menu_id;
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
						build_menu($item_id, $selected_menu_item_id, $items);
					}
				}
			}
		}
	}
	
	/**
	 * Before record shows, modify parent_menu_item_id select list
	 *
	 */
	function set_parent_select_values() {
		global $eg;
		$items = array();
		$selected_item_id = $eg->record->get_value("menu_id");
		build_menu(0, $selected_item_id, $items);
		$eg->record->change_property("parent_menu_item_id", VALUES_LIST, $items);
	}
	
	/**
	 * Get form menu items orders values and create array [item_id] => <item, which is before item_id>
	 *
	 * @return array
	 */
	function get_selected_orders() {
		$new_orders = array();
		$link_number = get_param("link_number");
		
		for($i = 1; $i <= $link_number; $i++) {
			$menu_item_id = get_param("menu_item_id_".$i);
			$after_item_id = get_param("order_select_".$i);
			$parent_item_id = get_param("parent_menu_item_id_".$i);
			$before_menu_item_id = get_param("before_menu_item_id_".$i);

			// Add only items with changed order
			if ($menu_item_id !== "" && 
				$before_menu_item_id !== "" && 
				$before_menu_item_id != $after_item_id) 
			{
				$new_orders[$menu_item_id] = $after_item_id;
			}
		}
		return $new_orders;
	}

	/**
	 * Set order_select options before form showing
	 *
	 */
	function set_order_values() {
		global $db, $r, $t, $table_prefix, $eg, $menu;
		// Set to menu_item_order items of current parent_id
		
		$op_list = array();
		
		$current_item_parent_id = $eg->record->get_value("parent_menu_item_id");
		$current_item_order = $eg->record->get_value("menu_order");
		$current_item_id = $eg->record->get_value("menu_item_id");
		
		$items = array();

		if ($current_item_parent_id != "" && $current_item_id != "" && !empty($menu)) {
			$subitems = $menu[$current_item_parent_id]["subs"];
	
			// $before_menu_item_id value defines which item is before current
			$sql = "SELECT menu_item_id FROM ".$table_prefix."menus_items ";
			$sql .= " WHERE menu_id = ".$db->tosql($r->get_value("menu_id"), INTEGER);
			$sql .= " AND parent_menu_item_id=".$db->tosql($current_item_parent_id, INTEGER);
			$sql .= " AND menu_order>".$db->tosql($current_item_order, INTEGER);
			$sql .= " ORDER BY menu_order ASC LIMIT 1";
			$db->query($sql);
			
			if($db->next_record()) {
				$before_menu_item_id = $db->f("menu_item_id");
			} else {
				$before_menu_item_id = 0;
			}
		
			$t->set_var("before_menu_item_id", $before_menu_item_id);
			// Fulfill order_select
			
			if (is_array($subitems)) {
				
				foreach ($subitems as $subitem_id) {
					if ($subitem_id != $current_item_id) {
						$items[] = array($subitem_id, BEFORE_MSG.$menu[$subitem_id]["menu_title"]);
					}
				}
			}
		} else {
			$before_menu_item_id = 0;
		}
		$items[] = array(0, AT_THE_END_MSG);
		$eg->record->change_property("order_select", VALUES_LIST, $items);
		$eg->record->set_value("order_select", $before_menu_item_id);
	}

	function spaces_level($level)
	{
		$spaces = "";
		for ($i =1; $i <= $level; $i++) {
			$spaces .= "---";
		}
		return $spaces . " ";
	}
	
	/**
	 * According to the new items order change its order values
	 *
	 * @param integer $item_id
	 * @param integer $before_item_id
	 * @param array $hierarchy
	 * @return array
	 */
	function exchange_menu_subitems($item_id, $before_item_id, &$hierarchy)
	{
		$item_key = array_search($item_id, $hierarchy);
		$before_item_key = array_search($before_item_id, $hierarchy);
		//var_dump($before_item_id)."<br>";
		if ($before_item_id == 0) {
			//$tmp_item_id = $item_id;
			// Move elements between item id and before id (inclusive) to the right
			for ($i = $item_key; $i < count($hierarchy) - 1; $i++) {
				$hierarchy[$i] = $hierarchy[$i + 1];
			}
			$hierarchy[count($hierarchy) - 1] = $item_id;
		} else if ($item_key !== false && $before_item_key !== false) {
			// Item id places on the right of before item id
			if ($item_key > $before_item_key) {
				//$tmp_item_id = $item_id;
				// Move elements between item id and before id (inclusive) to the right
				for ($i = $item_key - 1; $i >= $before_item_key; $i--) {
					$hierarchy[$i + 1] = $hierarchy[$i];
				}
				$hierarchy[$before_item_key] = $item_id;
			} else {
				//$tmp_item_id = $item_id;
				// Move elements between item id and before id to the left
				for ($i = $item_key; $i < $before_item_key; $i++) {
					$hierarchy[$i] = $hierarchy[$i + 1];
				}
				$hierarchy[$before_item_key - 1] = $item_id;
			}
		}

		return false;
	}
	
	/**
	 * Get menu items data from database and change menu_item_path and menu_item_order (according to form data).
	 *
	 */
	function build_menus_tree()
	{
		global $db, $table_prefix, $layout_id, $menu_id;

		$new_orders = get_selected_orders();
		// update menu links for new structure
		$items = array();
		$sql  = " SELECT menu_item_id, parent_menu_item_id, menu_order FROM " . $table_prefix . "menus_items ";
		$sql .= " WHERE menu_id=" . $db->tosql($menu_id, INTEGER);
		$sql .= " ORDER BY menu_order";
		$db->query($sql);
		while ($db->next_record()) {
			$menu_item_id = $db->f("menu_item_id");
			$parent_menu_item_id = $db->f("parent_menu_item_id");
			$items[$menu_item_id] = array(
				"id" => $db->f("menu_item_id"),
				"parent_id" => $db->f("parent_menu_item_id"),
				"order" => $db->f("menu_order")
			);
			$hierarchy[$items[$menu_item_id]["parent_id"]][] = $items[$menu_item_id]["id"];
		}
		
		if (is_array($new_orders) && !empty($new_orders)) {
			foreach ($new_orders as $item_id => $before_item_id) {
				$item_parent_id = $items[$item_id]["parent_id"];
				exchange_menu_subitems($item_id, $before_item_id, $hierarchy[$item_parent_id]);
			}
		}
		
		// Transform $hierarchy array
		$hierarchy_transformed = array();
		if (isset($hierarchy) && is_array($hierarchy)) {
			foreach ($hierarchy as $parent_id => $subitems) {
				if (is_array($subitems)) {
					foreach ($subitems as $key => $subitem_id) {
						$hierarchy_transformed[$parent_id][$subitem_id] = $key;
					}
				}
			}
		}

		foreach ($items as $menu_item_id => $item_arr) {
			$parent_menu_item_id = $item_arr["parent_id"];
			$menu_item_id = $item_arr["id"];
			if (!$parent_menu_item_id || $parent_menu_item_id == $menu_item_id) {
				$parent_menu_item_id = 0;
			}
			$menu_item_path = ""; 
			$current_parent_item_id = $items[$menu_item_id]["parent_id"];
			
			while ($current_parent_item_id) {
				$menu_item_path = $current_parent_item_id.",".$menu_item_path;
				
				$parent_item_id = isset($items[$current_parent_item_id]["parent_id"]) ? $items[$current_parent_item_id]["parent_id"] : 0;
				if ($parent_item_id == $current_parent_item_id) {
					$current_parent_item_id = 0;
				} else {
					$current_parent_item_id = $parent_item_id;
				}
			}
			$order = "";
			// Change order only if it was changed in the from
			if (isset($hierarchy_transformed[$parent_menu_item_id][$menu_item_id]) && !empty($new_orders)) {
				$order = $hierarchy_transformed[$parent_menu_item_id][$menu_item_id];
			}
			
			$sql  = " UPDATE " . $table_prefix . "menus_items SET ";
			$sql .= " parent_menu_item_id=" . $db->tosql($parent_menu_item_id, INTEGER) . ", ";
			$sql .= " menu_path=" . $db->tosql($menu_item_path, TEXT);
			if ($order !== "") {
				$sql .= ", ";
				$sql .= " menu_order=".$db->tosql($order, INTEGER);
			}
			$sql .= " WHERE menu_item_id=" . $db->tosql($menu_item_id, INTEGER);
			$db->query($sql);
		}	
	}
?>