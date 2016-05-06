<?php

function ads_category($block_name, $category_id, $category_name, $category_description, $category_image)
{
	global $t, $db, $table_prefix;
	global $page_settings, $restrict_categories_images;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	if(strlen($category_description) || $category_image) {
		$t->set_file("block_body", "block_category_description.html");
		if (strlen($category_image)) {
			if (preg_match("/^http\:\/\//", $category_image)) {
				$image_size = "";
			} else {
				$image_size = @GetImageSize($category_image);
			}
			$t->set_var("alt", htmlspecialchars($category_name));
			$t->set_var("src", htmlspecialchars($category_image));
			if(is_array($image_size)) {
				$t->set_var("width", "width=\"" . $image_size[0] . "\"");
				$t->set_var("height", "height=\"" . $image_size[1] . "\"");
			} else {
				$t->set_var("width", "");
				$t->set_var("height", "");
			}
			$t->sparse("image_large_block", false);
		} else {
			$t->set_var("image_large_block", "");
		}

		$t->set_var("category_name", $category_name);
		$t->set_var("full_description", $category_description);

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>