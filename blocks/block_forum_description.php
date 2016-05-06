<?php
function forum_description($block_name, $forum_id, $forum_name, $forum_description, $forum_image, $forum_image_alt = "")
{
	global $t, $page_settings, $restrict_categories_images;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	if(strlen($forum_description) || $forum_image)
	{
	  $t->set_file("block_body", "block_forum_description.html");

		if (strlen($forum_image)) {
			if (preg_match("/^http\:\/\//", $forum_image)) {
				$image_size = "";
			} else {
				$image_size = @GetImageSize($forum_image);
				if (isset($restrict_forum_images) && $restrict_forum_images) { 
					$forum_image = "image_show.php?forum_id=".$forum_id."&type=large"; 
				}
			}
			if (!strlen($forum_image_alt)) { $forum_image_alt = $forum_name; }
				$t->set_var("alt", htmlspecialchars($forum_image_alt));
				$t->set_var("src", htmlspecialchars($forum_image));
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

		$t->set_var("forum_name", $forum_name);
		$t->set_var("full_description", $forum_description);

		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>