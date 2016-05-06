<?

	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once("./admin_common.php");

	check_admin_security("layouts");

	$layout_id   = 1;//get_param("layout_id");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_menus_list.html");
/*
	$sql = "SELECT layout_name FROM " . $table_prefix . "layouts WHERE layout_id=" . $db->tosql($layout_id, INTEGER);
	$db->query($sql);
	if($db->next_record()) {
		$layout_name = $db->f("layout_name");
		$t->set_var("layout_name", htmlspecialchars($layout_name));
	} else {
		header("Location: " . $return_page);
		exit;
	}
//*/

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_page_href", "admin_menu.php?layout_id=".$layout_id);
	$t->set_var("layout_id", $layout_id);
	$t->set_var("admin_layout_href", "admin_layout.php");
	$t->set_var("admin_menu_items", "admin_menu_items.php?layout_id=".$layout_id);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// Get menus
	$sql  = " SELECT * FROM " . $table_prefix . "menus WHERE layout_id=" . $db->tosql($layout_id, INTEGER);
	$sql .= " ORDER BY menu_title ";
	$db->query($sql);
	$menus = array();
	while($db->next_record()) {
		$menu_id = $db->f("menu_id");
		$menu_values = array(
			"menu_id" => $menu_id, 
			"menu_name" => $db->f("menu_name"), 
			"menu_title" => $db->f("menu_title"),
			"menu_notes" => $db->f("menu_notes"),
		);
		$menus[$menu_id] = $menu_values;
	}

	if (empty($menus)) {
		$t->parse("no_records", false);
	}
	else {
		show_menus_list();
	}
	$t->pparse("main");

	function show_menus_list() {
		global $t, $menus;
		$menu_count = 0;
		foreach ($menus as $menu_id => $menu) {
			++$menu_count;
			$t->set_var("menu_count"  , $menu_count);
			$t->set_var("menu_id"   , $menu['menu_id']);
			$t->set_var("menu_name"  , $menu['menu_name']);
			$t->set_var("menu_title", $menu['menu_title']);
			$t->set_var("menu_notes"  , $menu['menu_notes']);

			$t->parse("records", true);
		}
	}
?>