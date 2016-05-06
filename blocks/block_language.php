<?php

function language_form($block_name, $language_selection, $block_prefix = "", $page_friendly_url = "", $page_friendly_params = array())
{
	global $t, $db, $table_prefix, $settings, $language_code, $current_page;

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	if ($block_name) {
		$t->set_file("block_body", "block_language.html");
	}

	$remove_parameters = array();
	if ($friendly_urls && $page_friendly_url) {
		$current_page = $page_friendly_url . $friendly_extension;
		$query_string = transfer_params($page_friendly_params, true);
	} else {
		$query_string = transfer_params("", true);
	}
	$t->set_var("current_href", $current_page);

	$sql  = " SELECT language_code, language_name, language_image, language_image_active ";
	$sql .= " FROM " . $table_prefix . "languages WHERE show_for_user=1 ORDER BY language_order, language_code ";
	$db->query($sql);
	while ($db->next_record()) {
		$row_language_code = $db->f("language_code");
		$row_language_name = get_translation($db->f("language_name"));
		$language_image = $db->f("language_image");
		$language_image_active = $db->f("language_image_active");
		$language_selected = ($language_code == $row_language_code) ? "selected" : "";
		$t->set_var("language_selected", $language_selected);
		$t->set_var("language_code", $row_language_code);
		$t->set_var("language_name", $row_language_name);
		if ($language_selection != 2 && $language_image) {
			// If current row language is a selected by user, make it "highlighted" use active image if it's not empty
			if ($language_code == $row_language_code && $language_image_active != "") {
				$language_image = $language_image_active;
			}
			$image_size = preg_match("/^http\:\/\//", $language_image) ? "" : @GetImageSize($language_image);
			$t->set_var("src", htmlspecialchars($language_image));
			if (is_array($image_size)) {
				$t->set_var("width", "width=\"" . $image_size[0] . "\"");
				$t->set_var("height", "height=\"" . $image_size[1] . "\"");
			} else {
				$t->set_var("width", "");
				$t->set_var("height", "");
			}

			$language_query = $query_string;
			if ($language_query) {
				$language_query .= "&";
			} else {
				$language_query .= "?";
			}
 			$language_query .= "language_code=" . $row_language_code; 
			$language_url = $current_page . $language_query;
			$t->set_var("language_query", $language_query);
			$t->set_var("language_url", $language_url);

			$t->parse($block_prefix . "languages_images", true);
		} elseif ($language_selection == 2) {
			$t->parse($block_prefix . "languages", true);
		}
	}

	if ($language_selection == 2) {
		$t->set_var($block_prefix . "languages_images", "");
		$t->parse($block_prefix . "select_languages", false);
	}

	if ($block_name) {
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

}

?>