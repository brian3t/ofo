<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_products_add.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "includes/image_functions.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once($root_folder_path . "messages/" . $language_code . "/download_messages.php");
	include_once("./admin_common.php");

	check_admin_security("add_products");
	
	$category_id = get_param("category_id");
	if (!strlen($category_id)) { $category_id = "0"; }
	$return_page = "admin_items_list.php?category_id=" . $category_id;
	
	$images_root      = "../images/";
	$tiny_dir_suffix  = "tiny/";
	$small_dir_suffix = "small/";
	$big_dir_suffix   = "big/";
	$large_dir_suffix = "large/";
	$super_dir_suffix = "super/";
				
	$filepath = $images_root . $super_dir_suffix;						
	$resize_super_image = get_setting_value($settings, "resize_super_image", 0);
	
	$tiny_width    = get_setting_value($settings, "tiny_image_max_width", 32);
	$tiny_height   = get_setting_value($settings, "tiny_image_max_height", 32);
	$small_width   = get_setting_value($settings, "small_image_max_width", 100);
	$small_height  = get_setting_value($settings, "small_image_max_height", 100);
	$big_width     = get_setting_value($settings, "big_image_max_width", 300);
	$big_height    = get_setting_value($settings, "big_image_max_height", 300);
	if ($resize_super_image) {
		$super_width  = get_setting_value($settings, "super_image_max_width", 1024);
		$super_height = get_setting_value($settings, "super_image_max_height", 768);
	}
			
	$item_types    = get_db_values("SELECT item_type_id, item_type_name FROM " . $table_prefix . "item_types", array(array("", "")));
	$manufacturers = get_db_values("SELECT manufacturer_id,manufacturer_name FROM " . $table_prefix . "manufacturers ORDER BY manufacturer_name", array(array("", "")));
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_products_add.html");	
	$t->set_var("admin_products_add_href", "admin_products_add.php");
	$t->set_var("admin_product_href", "admin_product.php");
	
	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree");
	$tree->show($category_id);
	
	$r = new VA_Record($table_prefix . "items");

	$r->add_where("item_id", INTEGER);
	$r->change_property("item_id", USE_IN_INSERT, true);
	$r->add_textbox("user_id", INTEGER);
	$r->change_property("user_id", USE_SQL_NULL, false);
	$r->add_hidden("category_id", INTEGER);
	
	
	$r->add_select("item_type_id", INTEGER, $item_types, PROD_TYPE_MSG);
	$r->change_property("item_type_id", REQUIRED, true);
	$r->add_select("manufacturer_id", INTEGER, $manufacturers);	
	$r->add_textbox("item_code", TEXT);
	$r->change_property("item_code", USE_SQL_NULL, false);
	$r->add_textbox("item_name", TEXT, PROD_NAME_MSG);
	$r->change_property("item_name", REQUIRED, true);
	$r->add_textbox("manufacturer_code", TEXT);	
	$r->add_textbox("price", NUMBER, PROD_LIST_PRICE_MSG);
	$r->change_property("price", REQUIRED, true);
	$r->add_textbox("trade_price", NUMBER, PROD_TRADE_PRICE_MSG);
	$r->change_property("trade_price", USE_SQL_NULL, false);
	$r->add_textbox("buying_price", NUMBER, PROD_BUYING_PRICE_MSG);
	$r->change_property("buying_price", USE_SQL_NULL, false);

	$r->add_textbox("short_description", TEXT);
	$r->add_textbox("full_description", TEXT);
	$r->add_textbox("features", TEXT);
	$r->add_textbox("special_offer", TEXT);
	
	//auto fields!
	$r->add_textbox("sites_all", NUMBER);
	$r->add_textbox("guest_access_level", NUMBER);	
	$r->add_textbox("access_level", NUMBER);

	$r->add_checkbox("is_special_offer", INTEGER);
	$r->add_textbox("tiny_image", TEXT);
	$r->add_textbox("tiny_image_alt", TEXT);
	$r->add_textbox("small_image", TEXT);
	$r->add_textbox("small_image_alt", TEXT);
	$r->add_textbox("big_image", TEXT);
	$r->add_textbox("big_image_alt", TEXT);
	$r->add_textbox("super_image", TEXT);
	
	$number_records = 2;
	$eg = new VA_EditGrid($r, "items");
	$eg->get_form_values($number_records);		
	$eg->set_event(BEFORE_INSERT, "prepare_item");	
	$eg->set_event(AFTER_INSERT, "save_item");
	
	$operation = get_param("operation");
	if ($operation == "save" || $operation == "apply") {
		$is_valid = $eg->validate($number_records);
		if ($is_valid) {
			$eg->insert_all($number_records);
			if ($operation == "save") {
				header("Location:" . $return_page);
			}
		}
	}

	$eg->set_parameters_all($number_records);
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	
	$t->pparse("main");

	function save_item() {
		global $eg, $table_prefix, $db, $category_id;

		$sql  = " INSERT INTO " . $table_prefix . "items_categories (item_id,category_id) VALUES (";
		$sql .= $db->tosql($eg->record->get_value("item_id"), INTEGER) . ",";
		$sql .= $db->tosql($category_id, INTEGER) . ")";
		$db->query($sql);
	}
	function prepare_item() {
		global $eg, $table_prefix; 
		global $images_root, $tiny_dir_suffix, $small_dir_suffix, $big_dir_suffix, $large_dir_suffix, $super_dir_suffix, $filepath, $resize_super_image;
		global $tiny_width, $tiny_height, $small_width, $small_height, $big_width, $big_height, $super_width, $super_height;
		
		$file = false;
		$errors = "";
		if (isset($_FILES["file_" . $eg->record_number])) {
			$file = $_FILES["file_" . $eg->record_number];
		} elseif (isset($HTTP_POST_FILES["file_" . $eg->record_number])) {
			$file = $HTTP_POST_FILES["file_" . $eg->record_number];
		}
		if ($file) {			
			$tmp_name = $file["tmp_name"];
			$filename = $file["name"];
			$filesize = $file["size"];
			$upload_error = isset($file["error"]) ? $file["error"] : "";			
			$tmp = explode('.', $filename);
			if (count($tmp) > 1) {
				$filetype = array_pop($tmp);
			} else {
				$filetype = "";
			}
			
			if ($upload_error == 1) {
				$errors .= FILESIZE_DIRECTIVE_ERROR_MSG . "<br>\n";
			} elseif ($upload_error == 2) {
				$errors .= FILESIZE_PARAMETER_ERROR_MSG . "<br>\n";
			} elseif ($upload_error == 3) {
				$errors .= PARTIAL_UPLOAD_ERROR_MSG . "<br>\n";
			} elseif ($upload_error == 4) {
				$errors .= UPLOAD_SELECT_ERROR . "<br>\n";
			} elseif ($upload_error == 6) {
				$errors .= TEMPORARY_FOLDER_ERROR_MSG . ".<br>\n";
			} elseif ($upload_error == 7) {
				$errors .= FILE_WRITE_ERROR_MSG . "<br>\n";
			} elseif ($tmp_name == "none" || !strlen($tmp_name)) {
				$errors .= UPLOAD_SELECT_ERROR . "<br>\n";
			} elseif (!(preg_match("/((.gif)|(.jpg)|(.jpeg)|(.bmp)|(.tiff)|(.tif)|(.png)|(.ico)|(.doc)|(.txt)|(.rtf)|(.pdf)|(.swf)|(.flv)|(.avi)|(.asf)|(.wmv)|(.vma)|(.mpg)|(.mpeg))$/i", $filename)) ) {
				$errors .= UPLOAD_FORMAT_ERROR . "<br>\n";
			}
			if (!strlen($errors)) {
				//filename by item name
				$uploaded_filename = str_replace(" ", "_", $eg->record->get_value("item_name")) . "." . $filetype;
				if (!@move_uploaded_file($tmp_name, $filepath . $uploaded_filename)) {
					if (!is_dir($filepath)) {
						$errors .= FOLDER_DOESNT_EXIST_MSG . $filepath ;
					} elseif (!is_writable($filepath)) {
						$errors .= str_replace("{folder_name}", $filepath, FOLDER_PERMISSION_MESSAGE) . "<br>\n";
					} else {
						$errors .= UPLOAD_CREATE_ERROR ." <b>" . $filepath . $uploaded_filename . "</b><br>\n";
					}
				} else {
					@chmod($filepath . $uploaded_filename, 0666);
					$eg->record->set_value("super_image", $filepath . $uploaded_filename);
					
					$gd_loaded = true;
					
					if (@resize($uploaded_filename, $filepath, $images_root.$tiny_dir_suffix, $tiny_width, $tiny_height, $errors))	{
						@chmod($images_root . $tiny_dir_suffix . $uploaded_filename, 0666);
						$eg->record->set_value("tiny_image", $images_root . $tiny_dir_suffix . $filename);
					}
					
					if (@resize($uploaded_filename, $filepath, $images_root.$small_dir_suffix, $small_width, $small_height, $errors))	{
						@chmod($images_root . $small_dir_suffix . $uploaded_filename, 0666);
						$eg->record->set_value("small_image", $images_root . $small_dir_suffix . $filename);
					}
					
					if (@resize($uploaded_filename, $filepath, $images_root.$big_dir_suffix, $big_width, $big_height, $errors))	{
						@chmod($images_root.$big_dir_suffix.$uploaded_filename, 0766);
						$eg->record->set_value("big_image", $images_root . $big_dir_suffix . $filename);
					}
					if ($resize_super_image) {
						if (@resize($uploaded_filename, $filepath, $images_root.$super_dir_suffix, $super_width, $super_height, $errors))	{
							@chmod($images_root.$super_dir_suffix.$uploaded_filename, 0766);
						}
					}
				}
			}
		}
		
		$item_id = get_db_value("SELECT MAX(item_id) FROM " . $table_prefix . "items") + 1;
		$eg->record->set_value("item_id", $item_id);
		
		if (strlen($eg->record->get_value("special_offer"))) {
			$eg->record->set_value("is_special_offer", 1);
		} else {
			$eg->record->set_value("is_special_offer", 0);
		}
		$eg->record->set_value("sites_all", 1);
		$eg->record->set_value("guest_access_level", 7);
		$eg->record->set_value("access_level", 7);
		
		if (!strlen($errors)) {
			return true;
		} else {
			$eg->errors .= $errors;
			$eg->record->errors .= $errors;
			return false;
		}
	}
?>