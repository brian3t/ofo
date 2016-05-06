<?php
include_once("./includes/products_functions.php");

function manufacturers($block_name)
{
	global $t, $db, $db_type, $table_prefix;
	global $settings, $page_settings, $language_code;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	
	$manufacturers_selection = get_setting_value($page_settings, "manufacturers_selection", 1);
	$manufacturers_image     = get_setting_value($page_settings, "manufacturers_image", 1);
	$manufacturers_desc      = get_setting_value($page_settings, "manufacturers_desc", 1);
	$manufacturers_order     = get_setting_value($page_settings, "manufacturers_order", 1);
	$manufacturers_direction = get_setting_value($page_settings, "manufacturers_direction", 1);
	
	
	$category_id = get_param("category_id");
	$search_category_id = get_param("search_category_id");
	$manf = get_param("manf");
	if ($search_category_id) { $category_id = $search_category_id; }

	$t->set_file("block_body", "block_manufacturers.html");
	$t->set_var("products_href", get_custom_friendly_url("products.php"));
	$t->set_var("category_id", htmlspecialchars($category_id));

	$list_page = get_custom_friendly_url("products.php");
	$manf_url = new VA_URL($list_page);
	$manf_url->add_parameter("category_id", CONSTANT, $category_id);

	$search_tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "categories", "tree", TOP_CATEGORY_MSG);
	
	$sql_fields = "";
	if ($manufacturers_selection == 1) {		
		if ($manufacturers_desc == 1) {
			$sql_fields .= ", short_description AS description ";
		} elseif ($manufacturers_desc == 2) {
			$sql_fields .= ", full_description AS description ";
		}
		if ($manufacturers_image == 2) {
			$sql_fields .= ", image_small_alt AS image_alt, image_small AS image ";
		} elseif ($manufacturers_image == 3) {
			$sql_fields .= ", image_large_alt AS image_alt, image_large AS image  ";
		}			
	}
	
	$manufacturers = array();
		
	$sql  = " SELECT manufacturer_id, manufacturer_name, friendly_url ";
	$sql .= $sql_fields;
	$sql .=	" FROM " . $table_prefix . "manufacturers  ";
	if ($manufacturers_order == 2)
		$sql .= " ORDER BY manufacturer_order ";
	else 
		$sql .= " ORDER BY manufacturer_name ";
	if ($manufacturers_direction == 2) 
		$sql .= " DESC ";
	else
		$sql .= " ASC ";
	$db->query($sql);
	while ($db->next_record()) {
		$manufacturers[] = array(
			$db->f("manufacturer_id"),
			get_translation($db->f("manufacturer_name")),
			$db->f("friendly_url"),
			get_translation($db->f("description")),
			get_translation($db->f("image_alt")),
			get_translation($db->f("image"))
		);
	}	
	if(!$manufacturers) return false;
	
	$sql_params = array();
	if ($category_id > 0) {
		$sql_params["brackets"] = "((";
		
		$sql_params["join"]  = " INNER JOIN " . $table_prefix . "items_categories ic ON ic.item_id=i.item_id) ";
		$sql_params["join"] .= " INNER JOIN " . $table_prefix . "categories c ON ic.category_id=c.category_id) ";		
		
		$sql_params["where"]  = " (c.category_id=" . $db->tosql($category_id, INTEGER);
		$sql_params["where"] .= " OR c.category_path LIKE '%" . $db->tosql($search_tree->get_path($category_id), TEXT, false) . "%')";
	}

	foreach ($manufacturers AS $manufacturer) {
		list($manufacturer_id, $manufacturer_name, $friendly_url, $description, $image_alt,	$image) = $manufacturer;
		
		if ($sql_params) {
			$where = $sql_params;		
			$where["where"] .= " AND i.manufacturer_id=" . $db->tosql($manufacturer_id, INTEGER);
		} else {
			$where = " i.manufacturer_id=" . $db->tosql($manufacturer_id, INTEGER);
		}
		$manufacturer_items_ids = VA_Products::find_all_ids($where, VIEW_CATEGORIES_ITEMS_PERM);
		$manufacturer_products  = count($manufacturer_items_ids);
		if (!$manufacturer_products) continue;
		if ($friendly_urls && $friendly_url) {
			$manf_url->remove_parameter("manf");
			$manufacturer_href = $manf_url->get_url($friendly_url. $friendly_extension);
		} else {
			$manf_url->add_parameter("manf", CONSTANT, $manufacturer_id);
			$manufacturer_href = $manf_url->get_url($list_page);
		}

		$manufacturer_selected = ($manf == $manufacturer_id) ? "selected" : "";

		$t->set_var("manufacturer_id", $manufacturer_id);
		$t->set_var("manufacturer_name", $manufacturer_name);
		$t->set_var("manufacturer_href", $manufacturer_href);
		$t->set_var("manufacturer_selected", $manufacturer_selected);
		$t->set_var("manufacturer_products", $manufacturer_products);
			
		if ($description) {
			$t->set_var("desc_text", $description);
			$t->sparse("desc", false);
		} else {
			$t->set_var("desc", "");
		}
		if ($image) {
			if (preg_match("/^http\:\/\//", $image)) {
				$image_size = "";
			} else {
				$image_size = @GetImageSize($image);					
			}
			if(is_array($image_size)) {
				$t->set_var("width", "width=\"" . $image_size[0] . "\"");
				$t->set_var("height", "height=\"" . $image_size[1] . "\"");
			} else {
				$t->set_var("width", "");
				$t->set_var("height", "");
			}
			$t->set_var("alt", $image_alt);
			$t->set_var("src", $image);
			$t->sparse("image", false);
		} else {
			$t->set_var("image", "");
		}			
		$t->sparse("manufacturers", true);
		$t->sparse("manufacturers_options", true);	
	}

	if ($manufacturers_selection == 2) {
		$t->sparse("manufacturers_select", false);
	} else {
		$t->sparse("manufacturers_list", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

?>