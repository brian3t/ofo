<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  ads_print.php                                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");
	include_once("./includes/ads_properties.php");
	include_once("./includes/ads_functions.php");
	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main", "ads_print.html");

	$item_id     = get_param("item_id");	
	$category_id = get_param("category_id");
	
	if (!VA_Ads::check_exists($item_id)) {
		$t->set_var("item", "");
		$t->set_var("NO_AD_MSG", NO_AD_MSG);
		$t->parse("no_item", false);		
		$t->pparse("main", false);
		exit;
	}
	
	if (!strlen($category_id) && strlen($item_id)) {
		$category_id = VA_Ads::get_category_id($item_id, VIEW_ITEMS_PERM);
	}
	
	if (!VA_Ads::check_permissions($item_id, $category_id, VIEW_ITEMS_PERM)) {
		header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
		exit;
	}
	
	$t->set_var("category_id", $category_id);
	$t->set_var("item_id", $item_id);

	$sql  = " SELECT i.item_id, it.type_name, i.item_title, i.short_description, i.full_description, ";
	$sql .= " i.image_large, i.price, i.quantity, i.availability, ";
	$sql .= " i.is_compared, i.user_id, ut.type_name AS user_type_name, ";	
	$sql .= " u.login, u.name AS user_name, u.first_name, u.last_name, ";
	$sql .= " i.location_city, s.state_name, c.country_name, i.location_info, ";
	$sql .= " i.date_start, i.date_end, i.date_added, i.date_updated, i.image_large ";
	$sql .= " FROM (((((" . $table_prefix . "ads_items i ";
	$sql .= " INNER JOIN " . $table_prefix . "ads_types it ON i.type_id= it.type_id)";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON i.user_id=u.user_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "user_types ut ON ut.type_id=u.user_type_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "states s ON s.state_code=i.location_state) ";
	$sql .= " LEFT JOIN " . $table_prefix . "countries c ON c.country_code=i.location_country) ";
	$sql .= " WHERE i.item_id = " . $db->tosql($item_id, INTEGER);
	$db->query($sql);

	if ($db->next_record())
	{
		$tabs = array("desc");

		$item_id = $db->f("item_id");
		$type_name = $db->f("type_name");
		$item_title = get_translation($db->f("item_title"));
		$short_description = get_translation($db->f("short_description"));
		$full_description = get_translation($db->f("full_description"));
		$availability = get_translation($db->f("availability"));
		$price = $db->f("price");
		$quantity = $db->f("quantity");
		$is_compared = $db->f("is_compared");
		$user_id = $db->f("user_id");
		$user_type_name = $db->f("user_type_name");
		$name = $db->f("name");
		$login = $db->f("login");
		$first_name = $db->f("first_name");
		$last_name = $db->f("last_name");
		if (strlen($name)) {
			$user_name = $name;
		} elseif (strlen($first_name) || strlen($last_name)) {
			$user_name = $first_name." ".$last_name;
		} else {
			$user_name = $login;
		}
		$seller_id     = $user_id;
		$seller_name   = $user_name;

		$date_start = $db->f("date_start", DATETIME);
		$date_end = $db->f("date_end", DATETIME);
		$date_start_ts = mktime(0,0,0, $date_start[MONTH], $date_start[DAY], $date_start[YEAR]);
		$date_end_ts = mktime(0,0,0, $date_end[MONTH], $date_end[DAY], $date_end[YEAR]);
		$time_to_run = $date_end_ts - $date_start_ts;
		$days_to_run = round($time_to_run / (60 * 60 * 24));
		$date_added = $db->f("date_added", DATETIME);
		$date_updated = $db->f("date_updated", DATETIME);

		if (!$full_description) { $full_description = $short_description; }
		if (strlen($short_description)) {
			$meta_description = $short_description;
		} elseif (strlen($full_description)) {
			$meta_description = $full_description;
		} else {
			$meta_description = $item_title;
		}
		$t->set_var("meta_description", get_meta_desc($meta_description));

		$location_city = get_translation($db->f("location_city"));
		$state_name    = get_translation($db->f("state_name"));
		$country_name  = get_translation($db->f("country_name"));
		$location_info = get_translation($db->f("location_info"));
		$location = $location_city;
		if (strlen($location) && strlen($state_name)) {
			$location .= ", ";
		}
		$location .= $state_name;
		if (strlen($location) && strlen($country_name)) {
			$location .= ", ";
		}
		$location .= $country_name;
		if (strlen($location) && strlen($location_info)) {
			$location .= ", ";
		}
		$location .= $location_info;
	
		if (!$full_description) { $full_description = $short_description; }
		if (strlen($short_description)) {
			$meta_description = $short_description;
		} elseif (strlen($full_description)) {
			$meta_description = $full_description;
		} else {
			$meta_description = $item_title;
		}

		$properties = show_ads_properties($item_id);

		$t->set_var("item_id",    $item_id);
		$t->set_var("item_title", $item_title);
		$t->set_var("product_title", $item_title);
		$t->set_var("ads_details_href", $settings["site_url"]."ads_details.php");
		$t->set_var("type_name", $type_name);
		if (strlen($user_id)) {
			$t->set_var("user_id", htmlspecialchars($user_id));
			$t->set_var("user_type_name", htmlspecialchars($user_type_name));
			$t->set_var("user_name", htmlspecialchars($user_name));
			$t->global_parse("user_block", false, false, true);
		} else {
			$t->set_var("user_block", "");
		}
		$t->set_var("price", currency_format($price));
		if (strlen($availability)) {
			$t->set_var("availability", htmlspecialchars($availability));
			$t->global_parse("availability_block", false, false, true);
		} else {
			$t->set_var("availability_block", "");
		}
		if (strlen($quantity)) {
			$t->set_var("quantity", htmlspecialchars($quantity));
			$t->global_parse("quantity_block", false, false, true);
		} else {
			$t->set_var("quantity_block", "");
		}
		if (strlen($location)) {
			$t->set_var("location", htmlspecialchars($location));
			$t->global_parse("location_block", false, false, true);
		} else {
			$t->set_var("location_block", "");
		}

		$big_image = $db->f("image_large");
		if ($big_image)
		{
			$image_size = preg_match("/^http\:\/\//", $big_image) ? "" : @GetImageSize($big_image);
			$t->set_var("alt", htmlspecialchars($item_title));
			$t->set_var("src", htmlspecialchars($big_image));
			if (is_array($image_size))
			{
				$t->set_var("width", "width=\"" . $image_size[0] . "\"");
				$t->set_var("height", "height=\"" . $image_size[1] . "\"");
			}
			else
			{
				$t->set_var("width", "");
				$t->set_var("height", "");
			}
			$t->parse("big_image", false);
		}
		else
		{
			$t->set_var("big_image", "");
		}

		$price = $db->f("price");
		$t->set_var("price", currency_format($price));

		// description block
		$t->set_var("description_block", "");

		if ($full_description) {
			$full_description = nl2br(htmlspecialchars($full_description));
			$t->set_var("full_description", $full_description);
			$t->parse("description", false);
			$t->parse("description_block", false);
		} else {
			$t->set_var("description", "");
		}

		// specification details
		$t->set_var("specification", "");
		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "ads_features ";
		$sql .= " WHERE item_id=" . intval($item_id);
		$sql .= " AND feature_value IS NOT NULL ";
		$db->query($sql);
		$db->next_record();
		$total_spec = $db->f(0);
		if ($total_spec > 0) 
		{

			$sql  = " SELECT fg.group_id,fg.group_name,f.feature_name,f.feature_value ";
			$sql .= " FROM " . $table_prefix . "ads_features f, " . $table_prefix . "ads_features_groups fg ";
			$sql .= " WHERE f.group_id=fg.group_id ";
			$sql .= " AND f.item_id=" . intval($item_id);
			$sql .= " AND feature_value IS NOT NULL ";
			$sql .= " ORDER BY fg.group_order, f.feature_id ";
			$db->query($sql);
			if ($db->next_record()) 
			{
				$last_group_id = $db->f("group_id");
				do
				{
					$group_id = $db->f("group_id");
					$feature_name = $db->f("feature_name");
					$feature_value = $db->f("feature_value");
					if ($group_id != $last_group_id) 
					{
						$t->set_var("group_name", $last_group_name);
						$t->parse("groups", true);
						$t->set_var("features", "");
					}
					$t->set_var("feature_name", $feature_name);
					$t->set_var("feature_value", $feature_value);
					$t->parse("features", true);
      
					$last_group_id = $group_id;
					$last_group_name = $db->f("group_name");
				} while ($db->next_record());
				$t->set_var("group_name", $last_group_name);
				$t->parse("groups", true);
				$t->parse("specification", false);
			} 
		}
		// end specification


		// product images 
		$t->set_var("images", "");
		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "ads_images ";
		$sql .= " WHERE item_id=" . intval($item_id);
		$sql .= " AND image_small IS NOT NULL ";
		$db->query($sql);
		$db->next_record();
		$total_images = $db->f(0);
		if ($total_images > 0) 
		{
			$image_number = 0;
			$sql  = " SELECT image_title, image_small, image_large, image_description  ";
			$sql .= " FROM " . $table_prefix . "ads_images ";
			$sql .= " WHERE item_id=" . intval($item_id);
			$sql .= " AND image_small IS NOT NULL ";
			$db->query($sql);
			while ($db->next_record()) 
			{
				$image_number++;
	    		$image_title = $db->f("image_title");
				$image_small = $db->f("image_small");
				$image_large = $db->f("image_large");
				if (!strlen($image_large)) 
				{
					$image_large = $image_small;
				}
				$image_description = $db->f("image_description");
      
				$t->set_var("image_title", $image_title);
				$t->set_var("image_small", $image_small);
				$t->set_var("image_width", "");
				$t->set_var("image_height", "");
				$t->set_var("image_large", $image_large);
				$t->set_var("image_description", $image_description);
				$t->parse("images_cols", true);
				if ($image_number % 2 == 0)
				{
					$t->parse("images_rows", true);
					$t->set_var("images_cols", "");
				}
			}	    
			if ($image_number % 2 != 0)
			{
				$t->parse("images_rows", true);
			}

			$t->parse("images", false);
		}
		// end images 

		$t->parse("item");
		$t->set_var("no_item", "");

		// parse tabs

		$t->set_var("seller_block", "");
		$t->set_var("title_seller", "");
		$t->set_var("seller_desc", "");
		$t->parse("seller_desc", false);
		$t->parse("title_seller", false);
	}
	else
	{
		$t->set_var("item", "");
		$t->set_var("NO_AD_MSG", NO_AD_MSG);
		$t->parse("no_item", false);
	}

	$t->pparse("main", false);

?>