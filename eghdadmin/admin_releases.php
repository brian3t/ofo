<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_releases.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("product_releases");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_releases.html");

	$t->set_var("admin_release_href", "admin_release.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_product_href", "admin_product.php");
	$t->set_var("admin_release_changes_href", "admin_release_changes.php");

	$item_id = get_param("item_id");
	$category_id = get_param("category_id");
	if(!strlen($category_id)) $category_id = "0";

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($category_id);

	$t->set_var("item_id", $item_id);
	$t->set_var("category_id", $category_id);

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_releases.php");
	$s->set_default_sorting(2, "desc");
	$s->set_sorter(ID_MSG, "sorter_release_id", 1, "release_id");
	$s->set_sorter(RELEASE_DATE_MSG, "sorter_release_date", 2, "release_date");
	$s->set_sorter(RELEASE_TITLE_MSG, "sorter_release_title", 3, "release_title");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_releases.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");


	$sql  = " SELECT item_name FROM " . $table_prefix . "items ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$db->query($sql);
	if($db->next_record())
		$t->set_var("item_name", get_translation($db->f("item_name")));
	else
		die(str_replace("{item_id}", $item_id, PRODUCT_ID_NO_LONGER_EXISTS_MSG));



	// set up variables for navigator
	$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "releases ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);

	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 20;
	$pages_number = 10;

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT release_id,release_date, release_title FROM " . $table_prefix . "releases ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$sql .= $s->order_by;
	$db->query($sql);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$t->set_var("release_id", $db->f("release_id"));

			$release_date = $db->f("release_date", DATETIME);
			$t->set_var("release_date", va_date($date_show_format, $release_date));

			$t->set_var("release_title", $db->f("release_title"));

			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>