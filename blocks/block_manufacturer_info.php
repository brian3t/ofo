<?php

function manufacturer_info($block_name)
{
	global $t, $db, $table_prefix, $language_code;
	global $page_settings;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$manf = get_param("manf");

	if ($manf) {
		$desc_image = get_setting_value($page_settings, "manufacturer_info_image", 3);
		$desc_type = get_setting_value($page_settings, "manufacturer_info_type", 2);
  
		$sql  = " SELECT manufacturer_name, short_description, full_description, ";
		$sql .= " image_small, image_small_alt, image_large, image_large_alt ";
		$sql .= " FROM " . $table_prefix . "manufacturers WHERE manufacturer_id = " . $db->tosql($manf, INTEGER);
		$db->query($sql);
		if($db->next_record())
		{
			$manufacturer_name = get_translation($db->f("manufacturer_name"));
			$manufacturer_info = "";
			if ($desc_type == 2) {
				$manufacturer_info = get_translation($db->f("full_description"));
			} else if ($desc_type == 1) {
				$manufacturer_info = get_translation($db->f("short_description"));
			}
			$image = ""; $image_alt = "";
			if ($desc_image == 3) {
				$image      = $db->f("image_large");
				$image_alt  = get_translation($db->f("image_large_alt"));
			} else if ($desc_image == 2) {
				$image      = $db->f("image_small");
				$image_alt  = get_translation($db->f("image_small_alt"));
			}
			
			if (strlen($manufacturer_info) || strlen($image)) {
  
		    $t->set_file("block_body", "block_manufacturer_info.html");
  
				if (strlen($image)) {
					if (preg_match("/^http\:\/\//", $image)) {
						$image_size = "";
					} else {
		        $image_size = @GetImageSize($image);
					}
					if(!strlen($image_alt)) { $image_alt = $manufacturer_name; }
					$t->set_var("alt", htmlspecialchars($image_alt));
					$t->set_var("src", htmlspecialchars($image));
					if(is_array($image_size)) {
						$t->set_var("width", "width=\"" . $image_size[0] . "\"");
						$t->set_var("height", "height=\"" . $image_size[1] . "\"");
					} else {
						$t->set_var("width", "");
						$t->set_var("height", "");
					}
					$t->sparse("manufacturer_image", false);
				} else {
					$t->set_var("manufacturer_image", "");
				}
		  
				$t->set_var("manufacturer_name", $manufacturer_name);
				$t->set_var("manufacturer_info", $manufacturer_info);
		  
				$t->parse("block_body", false);
				$t->parse($block_name, true);
			}
		}
	}

}

?>