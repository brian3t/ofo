<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads_images.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	check_admin_security("ads");

	$item_id = get_param("item_id");
	$category_id = get_param("category_id");
	$sql  = " SELECT item_title FROM " . $table_prefix . "ads_items ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$item_title = get_translation($db->f("item_title"));
	} else {
		die(OBJECT_NO_EXISTS_MSG);
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_ads_images.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_ads_href", "admin_ads.php");
	$t->set_var("admin_ads_edit_href", "admin_ads_edit.php");
	$t->set_var("admin_ads_image_href", "admin_ads_image.php");
	$t->set_var("admin_ads_images_href", "admin_ads_images.php");

	$t->set_var("item_id", $item_id);
	$t->set_var("category_id", $category_id);
	$t->set_var("item_title", $item_title);

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "ads_categories", "tree", "Ads");
	$tree->show($category_id);

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_ads_images.php");
	$s->set_sorter(ID_MSG, "sorter_image_id", "1", "image_id");
	$s->set_sorter(IMAGE_TITLE_MSG, "sorter_image_title", "2", "image_title");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_ads_images.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "ads_images ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT * FROM " . $table_prefix . "ads_images ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$sql .= $s->order_by;
	$db->query($sql);
	if ($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
			$t->set_var("image_id", $db->f("image_id"));
			$t->set_var("image_title", $db->f("image_title"));
			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>
