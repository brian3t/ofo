<?php
include_once("./includes/products_functions.php");

function products_top_viewed($block_name)
{
	global $t, $db, $table_prefix;
	global $settings, $page_settings, $language_code;

	if (get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");
	$top_viewed_image = get_setting_value($page_settings, "top_viewed_image",  0);
	$top_viewed_desc = get_setting_value($page_settings, "top_viewed_desc", 0);
	
	$restrict_products_images = get_setting_value($settings, "restrict_products_images", "");
	product_image_fields($top_viewed_image, $image_type_name, $image_field, $image_alt_field, $watermark, $product_no_image);

	$php_in_desc = 0; $desc_field = "";
	if ($top_viewed_desc == 1) {
		$desc_field = "short_description";
		$php_in_desc = get_setting_value($settings, "php_in_products_short_desc", 0);
	} elseif ($top_viewed_desc == 2) {
		$desc_field = "full_description";
		$php_in_desc = get_setting_value($settings, "php_in_products_full_desc", 0);
	} elseif ($top_viewed_desc == 3) {
		$desc_field = "features";
		$php_in_desc = get_setting_value($settings, "php_in_products_features", 0);
	} elseif ($top_viewed_desc == 4) {
		$desc_field = "special_offer";
		$php_in_desc = get_setting_value($settings, "php_in_products_hot_desc", 0);
	}

	// new product settings	
	$new_product_enable = get_setting_value($settings, "new_product_enable", 0);
	$new_product_order  = get_setting_value($settings, "new_product_order", 0);
	
	$records_per_page   = get_setting_value($page_settings, "prod_top_viewed_recs", 10);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber     = 1;
	
	$items_ids = VA_Products::find_all_ids(
		array(
			"order" => " ORDER BY i.total_views DESC "
		),
		VIEW_CATEGORIES_ITEMS_PERM
	);
	if (!$items_ids) return;
	
	$allowed_items_ids = VA_Products::find_all_ids("i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);
	
	$t->set_file("block_body", "block_products_top_viewed.html");
	$t->set_var("top_viewed_rows", "");
	$t->set_var("top_viewed_rows", "");

	$sql  = " SELECT item_id, item_name, friendly_url, total_views ";
	// new product db
	if ($new_product_enable) {
		switch ($new_product_order) {
			case 0:
				$sql .= ", issue_date AS new_product_date ";
			break;
			case 1:
				$sql .= ", date_added AS new_product_date ";
			break;
			case 2:
				$sql .= ", date_modified AS new_product_date ";
			break;
		}		
	}
	if ($image_field) { $sql .= " , " . $image_field; }
	if ($image_alt_field) { $sql .= " , " . $image_alt_field; }
	if ($desc_field) { $sql .= " , " . $desc_field; }
	$sql .= " FROM " . $table_prefix . "items ";
	$sql .= " WHERE item_id IN(" . $db->tosql($items_ids, INTEGERS_LIST) . ")";
	$sql .= " ORDER BY total_views DESC ";

	$db->query($sql);
	if ($db->next_record())
	{
		$top_columns = get_setting_value($page_settings, "prod_top_viewed_cols", 1);
		$t->set_var("top_viewed_column", (100 / $top_columns) . "%");
		$top_number = 0;
		do
		{
			$top_number++;
			$item_id = $db->f("item_id");
			$item_name = get_translation($db->f("item_name"));
			$friendly_url = $db->f("friendly_url");
			$total_views = $db->f("total_views");
			$item_image = ""; $item_image_alt = ""; $item_desc = "";
			$image_exists = false;
			if ($image_field) {
				$item_image = $db->f($image_field);	
				$item_image_alt = get_translation($db->f($image_alt_field));	
				if (!strlen($item_image)) {
					$item_image = $product_no_image;
				} elseif (!image_exists($item_image)) {
					$item_image = $product_no_image;
				} else {
					$image_exists = true;
				}
			}
			if ($desc_field) {
				$item_desc = get_translation($db->f($desc_field));	
			}

			if ($friendly_urls && $friendly_url) {
				$t->set_var("product_url", $friendly_url . $friendly_extension);
			} else {
				$t->set_var("product_url", "product_details.php?item_id=" . $item_id);
			}
		
			if ($new_product_enable) {
				$new_product_date = $db->f("new_product_date");			
				$is_new_product   = is_new_product($new_product_date);
			} else {
				$is_new_product = false;
			}
			if ($is_new_product) {
				$t->set_var("product_new_class", " newProduct");
				$t->sparse("product_new_image", false);			
			} else {
				$t->set_var("product_new_class", "");
				$t->set_var("product_new_image", "");
			}
			
			if (!$allowed_items_ids || !in_array($item_id, $allowed_items_ids)) {
				$t->set_var("restricted_class", " restrictedItem");
				$t->sparse("restricted_image", false);
			} else {
				$t->set_var("restricted_class", "");
				$t->set_var("restricted_image", "");
			}
			
			$t->set_var("top_position", $top_number);
			$t->set_var("item_id", $item_id);
			$t->set_var("item_name", $item_name);
			$t->set_var("total_views", $total_views);

			if ($item_image)
			{
				if (preg_match("/^http\:\/\//", $item_image)) {
					$image_size = "";
				} else {
					$image_size = @getimagesize($item_image);
					if ($image_exists && ($watermark || $restrict_products_images)) {
						$item_image = "image_show.php?item_id=".$item_id."&type=".$image_type_name."&vc=".md5($item_image);
					}
				}
				if (!strlen($item_image_alt)) { $item_image_alt = $item_name; }
				$t->set_var("alt", htmlspecialchars($item_image_alt));
				$t->set_var("src", htmlspecialchars($item_image));
				if (is_array($image_size)) {
					$t->set_var("width", "width=\"" . $image_size[0] . "\"");
					$t->set_var("height", "height=\"" . $image_size[1] . "\"");
				} else {
					$t->set_var("width", "");
					$t->set_var("height", "");
				}
				$t->sparse("top_image", false);
			} else {
				$t->set_var("top_image", "");
			}
			if ($item_desc) {
				if ($php_in_desc) {
					eval_php_code($item_desc);
				}
				$t->set_var("desc_text", $item_desc);
				$t->sparse("top_desc", false);
			} else {
				$t->set_var("top_desc", "");
			}

			$t->parse("top_viewed_cols");
			if ($top_number % $top_columns == 0)
			{
				$t->parse("top_viewed_rows");
				$t->set_var("top_viewed_cols", "");
			}
			
		} while ($db->next_record());

		if ($top_number % $top_columns != 0) {
			$t->parse("top_viewed_rows");
		}

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}
}

?>