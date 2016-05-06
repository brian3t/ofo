<?php

function ads_compare($block_name)
{
	global $t, $db, $db_type, $table_prefix, $language_code;
	global $is_ssl, $settings, $page_settings, $currency;
	global $column_width, $items_number, $fields_values, $allowed_items_ids;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_ads_compare.html");
	$t->set_var("ads_details_href", "ads_details.php");

	$features = array();
	$items = get_param("items");
	$t->set_var("items_html", htmlspecialchars($items));
	$t->set_var("items_url", urlencode($items));

	$errors = "";
	if(!preg_match("/^(\d+)(,\d+)+$/", $items))	{
		$errors = COMPARE_PARAM_ERROR_MSG;
	}

	// preparing data
	$items_number = 0; $row = 0;
	if (!strlen($errors)) {
		$items_ids = VA_Ads::find_all_ids(array(
			"select" => "i.item_id",
			"where"  => "i.item_id IN (" . $db->tosql($items, INTEGERS_LIST) . ")"
		), VIEW_CATEGORIES_ITEMS_PERM);
		
		if ($items_ids) {
			$allowed_items_ids = VA_Ads::find_all_ids("i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ")", VIEW_ITEMS_PERM);
			
			$sql  = " SELECT i.item_id, i.item_title, i.price, i.image_small ";
			$sql .= " FROM " . $table_prefix . "ads_items i ";		
			$sql .= " WHERE i.item_id IN (" . $db->tosql($items_ids, INTEGERS_LIST) . ") ";
			
			$db->query($sql);
			while ($db->next_record()) {
				$items_number++;
	  
				$price = $db->f("price");
				$image_src = $db->f("image_small");
	  
				$fields_values["item_id"][$row] = $db->f("item_id");
				$fields_values["item_title"][$row] = get_translation($db->f("item_title"));
				$fields_values["price"][$row] = $price;
				
				$fields_values["image_src"][$row] = $image_src;
				$fields_values["image_width"][$row] = "";
				$fields_values["image_height"][$row] = "";
				if ($image_src)
				{
					$image_size = preg_match("/^http\:\/\//", $image_src) ? "" : @GetImageSize($image_src);
					if(is_array($image_size))
					{
						$fields_values["image_width"][$row] = "width=\"" . $image_size[0] . "\"";
						$fields_values["image_width"][$row] = "height=\"" . $image_size[1] . "\"";
					}
				}
	  
				$row++;
			}
	
			if ($items_number < 2) {
			 $errors = COMPARE_MIN_ALLOWED_MSG;
			} else if ($items_number > 5) {
			 $errors = COMPARE_MAX_ALLOWED_MSG;
			}
		} else {
			$errors = COMPARE_MIN_ALLOWED_MSG;
		}
	}

	if(!strlen($errors)) {

		for ($j = 0; $j < $items_number; $j++) {
			
			$item_id = $fields_values["item_id"][$j];
  
			// get all properties
			$sql  = " SELECT p.property_name, p.property_value  ";
			$sql .= " FROM " . $table_prefix . "ads_properties p ";
			$sql .= " WHERE p.item_id=" . intval($item_id);
			$sql .= " AND p.property_value IS NOT NULL ";
			$db->query($sql);
			while($db->next_record()) {
				$group_id = "options";
				$group_name = AD_PROPERTIES_MSG;
				$feature_name = $db->f("property_name");
				$feature_value= $db->f("property_value");
				$feature_groups[$group_id] = $group_name;
				if(isset($features[$group_id][$feature_name][$j])) {
					$features[$group_id][$feature_name][$j] .= "; " . $feature_value;
				} else {
					$features[$group_id][$feature_name][$j] = $feature_value;
				}
			}
  
			// get features list
			$sql  = " SELECT fg.group_id,fg.group_name,f.feature_name,f.feature_value ";
			$sql .= " FROM " . $table_prefix . "ads_features f, " . $table_prefix . "ads_features_groups fg ";
			$sql .= " WHERE f.group_id=fg.group_id ";
			$sql .= " AND f.item_id=" . intval($item_id);
			$sql .= " AND f.feature_value IS NOT NULL ";
			$sql .= " ORDER BY fg.group_order, f.feature_id ";
			$db->query($sql);
			while($db->next_record()) {
				$group_id = $db->f("group_id");
				$group_name = $db->f("group_name");
				$feature_name = $db->f("feature_name");
				$feature_value = $db->f("feature_value");
				$feature_groups[$group_id] = $group_name;
				if(isset($features[$group_id][$feature_name][$j])) {
					$features[$group_id][$feature_name][$j] .= "; " . $feature_value;
				} else {
					$features[$group_id][$feature_name][$j] = $feature_value;
				}
			}
		}

		$column_width = round(85 / $items_number);
		show_title();

		$t->set_var("column_width", $column_width . "%");
		$t->set_var("colspan", ($items_number + 1));

		foreach($features as $group_id => $group_features)
		{
			$t->set_var("features", "");
			foreach($group_features as $feature_name => $features_values)
			{		
				$t->set_var("features_values", "");
				for($p = 0; $p < $items_number; $p++) {
					$feature_value = isset($features_values[$p]) ? $features_values[$p] : "";
					$t->set_var("feature_value", $feature_value);
					$t->parse("features_values", true);
				}
				$t->set_var("feature_name", $feature_name);
				$t->parse("features", true);
			}

			$t->set_var("group_name", $feature_groups[$group_id]);
			$t->parse("features_groups", true);
		}

		$t->parse("compared", true);
		$t->set_var("errors_block", "");
	} else {

		$t->set_var("compared", "");
		$t->set_var("errors", $errors);
		$t->parse("errors_block", true);

	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);
}

function show_title()
{
	global $t, $settings, $currency;
	global $column_width, $items_number, $fields_values, $allowed_items_ids;

	if($items_number > 2) {
		for($i = 0; $i < $items_number; $i++) {
			$products = array();
			for($j = 0; $j < $items_number; $j++) {
				if($i != $j) { $products[] = $fields_values["item_id"][$j]; }
			}		
			$exclude_link = "ads_compare.php?items=" . urlencode(join($products, ","));
			$fields_values["exclude_link"][$i] = $exclude_link;
		}
	}

	for($j = 0; $j < $items_number; $j++) {
		$item_id = $fields_values["item_id"][$j];
		$item_title = $fields_values["item_title"][$j];
		$image_src = $fields_values["image_src"][$j];
		$image_width = $fields_values["image_width"][$j];
		$image_height = $fields_values["image_height"][$j];

		$t->set_var("item_id", $item_id);
		$t->set_var("item_title", $item_title);
		if (strlen($image_src)) {
			$t->set_var("image_src", $image_src);
			$t->set_var("image_width", $image_width);
			$t->set_var("image_height", $image_height);
    	$t->set_var("image_alt", htmlspecialchars($item_title));
			$t->parse("image_block", false);
		} else {
			$t->set_var("image_block", "");
		}
		
		if ($allowed_items_ids && in_array($item_id, $allowed_items_ids)) {
			$t->set_var("restricted_class", "");
			$t->set_var("restricted_image", "");
		} else {
			$t->set_var("restricted_class", " restrictedItem");
			$t->sparse("restricted_image", false);				
		}
		
		if($items_number > 2) {
			$t->set_var("exclude_href", $fields_values["exclude_link"][$j]);
			$t->parse("exclude_link", "");
		} else {
			$t->set_var("exclude_link", "");
		}

		$price = $fields_values["price"][$j];
		$t->set_var("price", currency_format($price));

		$t->parse("top_title", true);		
	}
}


?>