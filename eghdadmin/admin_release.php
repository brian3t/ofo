<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_release.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("products_categories");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_release.html");

	$t->set_var("admin_property_href", "admin_property.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_product_href", "admin_product.php");
	$t->set_var("admin_releases_href", "admin_releases.php");
	$t->set_var("admin_release_href", "admin_release.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_RELEASE_MSG, CONFIRM_DELETE_MSG));
	
	$item_id = get_param("item_id");
	$sql  = " SELECT item_name FROM " . $table_prefix . "items ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);

	$category_id = get_param("category_id");
	if(!strlen($category_id)) $category_id = "0";

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($category_id);

	$db->query($sql);
	if($db->next_record())
		$t->set_var("item_name", get_translation($db->f("item_name")));
	else
		die(str_replace("{item_id}", $item_id, PRODUCT_ID_NO_LONGER_EXISTS_MSG));


	$release_types = get_db_values("SELECT * FROM " . $table_prefix . "release_types", array(array("", SELECT_TYPE_MSG)));

	$download_types = 
		array( 
			array(1, ALL_USERS_CAN_DOWNLOAD_MSG), array(2, ONLY_REGISTERED_USERS_MSG), array(3, ONLY_USERS_ORDERED_MSG)
		);

	// set up html form parameters
	$r = new VA_Record($table_prefix . "releases");
	$r->return_page = "admin_releases.php";

	$r->add_hidden("category_id", INTEGER);
	$r->add_where("release_id", INTEGER);
	$r->add_textbox("item_id", TEXT, PRODUCT_ID_MSG);
	$r->parameters["item_id"][REQUIRED] = true;
	$r->parameters["item_id"][DEFAULT_VALUE] = $item_id;
	$r->parameters["item_id"][TRANSFER] = true;
	$r->add_textbox("release_date", DATETIME, RELEASE_DATE_MSG);
	$r->parameters["release_date"][REQUIRED] = true;
	$r->parameters["release_date"][VALUE_MASK] = $date_edit_format;
	$r->parameters["release_date"][DEFAULT_VALUE] = va_time();
	$r->add_select("release_type_id", INTEGER, $release_types, RELEASE_TYPE_MSG);
	$r->parameters["release_type_id"][REQUIRED] = true;
	$r->add_textbox("release_title", TEXT, TITLE_MSG);
	$r->parameters["release_title"][REQUIRED] = true;
	$r->add_textbox("version", TEXT);
	$r->add_textbox("release_desc", TEXT);
	$r->add_select("download_type", INTEGER, $download_types, DOWNLOAD_TYPE_MSG);
	$r->parameters["download_type"][REQUIRED] = true;
	$r->add_textbox("path_to_file", TEXT);
	$r->add_checkbox("is_showing", INTEGER);
	$r->parameters["is_showing"][DEFAULT_VALUE] = 1;
	$r->add_checkbox("show_on_index", INTEGER);

	$r->process();

	$t->set_var("release_date_format", join("", $date_edit_format));

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>