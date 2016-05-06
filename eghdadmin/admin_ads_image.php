<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads_image.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

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
	$t->set_file("main", "admin_ads_image.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_ads_href", "admin_ads.php");
	$t->set_var("admin_ads_edit_href", "admin_ads_edit.php");
	$t->set_var("admin_ads_image_href", "admin_ads_image.php");
	$t->set_var("admin_ads_images_href", "admin_ads_images.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", IMAGE_MSG, CONFIRM_DELETE_MSG));

	$t->set_var("item_id", $item_id);
	$t->set_var("category_id", $category_id);
	$t->set_var("item_title", $item_title);

	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "ads_categories", "tree", "Ads");
	$tree->show($category_id);

	$r = new VA_Record($table_prefix . "ads_images");
	$r->return_page = "admin_ads_images.php";

	$r->add_where("image_id", INTEGER);
	$r->add_textbox("item_id", INTEGER);
	$r->change_property("item_id", DEFAULT_VALUE, $item_id);
	$r->change_property("item_id", TRANSFER, true);
	$r->add_textbox("image_title", TEXT, IMAGE_TITLE_MSG);
	$r->change_property("image_title", REQUIRED, true);
	$r->add_textbox("image_small", TEXT, IMAGE_SMALL_MSG);
	$r->change_property("image_small", REQUIRED, true);
	$r->add_textbox("image_large", TEXT, IMAGE_LARGE_MSG);
	$r->add_textbox("image_description", TEXT);

	$r->add_hidden("category_id", INTEGER);

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>
