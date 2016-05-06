<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  icons_functions.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


function parse_icons($block_prefix = "icons", $icons_cols = 4, $icons_limit = 16)
{
	global $t, $settings, $icons;

	$users_icons = array();
	if (is_array($icons)) {
		for ($i = 0; $i < sizeof($icons); $i++) {
			$show_for_user = $icons[$i]["show_for_user"];
			if ($show_for_user) {
				$users_icons[] = $icons[$i];
			}
		}
	}

	$site_url = get_setting_value($settings, "site_url", "");

	if (sizeof($users_icons) > 0) {
		$total_icons = sizeof($users_icons);
		$icon_index = 0;
		if ($icons_limit < 1 || $icons_limit > $total_icons) {
			$icons_limit = $total_icons;
		}
		for ($icon_index = 0; $icon_index < $icons_limit; $icon_index++) {

			$icon_code = $users_icons[$icon_index]["code"];
			$icon_image = $users_icons[$icon_index]["image"];
			$icon_width = $users_icons[$icon_index]["width"];
			$icon_height = $users_icons[$icon_index]["height"];
			$icon_name = $users_icons[$icon_index]["name"];
			if ($icon_width > 0 && $icon_height > 0) {
				$icon_size = "width=\"" . $icon_width . "\" height=\""  . $icon_height . "\"";
			} else {
				$icon_size = "";
			}

			$icon_code_js = str_replace("\\", "\\\\", $icon_code);
			$icon_code_js = str_replace("'", "\\'", $icon_code_js);
			$t->set_var("icon_code", htmlspecialchars($icon_code));
			$t->set_var("icon_code_js", htmlspecialchars($icon_code_js));
			$t->set_var("icon_src",  $icon_image);
			$t->set_var("icon_alt",  htmlspecialchars($icon_name));
			$t->set_var("icon_size",  $icon_size);

			$t->parse($block_prefix . "_cols");
			if (($icon_index + 1) % $icons_cols == 0) {
				$t->parse($block_prefix . "_rows");
				$t->set_var($block_prefix . "_cols", "");
			}
		} 

		if ($icon_index % $icons_cols != 0) {
			$t->parse($block_prefix . "_rows");
		}
		if ($total_icons > $icons_limit) {
			$t->parse($block_prefix . "_more_link");
		}

		$t->parse($block_prefix . "_block", false);
	}
}

	function prepare_icons(&$icons, &$icons_codes, &$icons_tags)
	{
		global $db, $table_prefix, $icons, $settings, $is_ssl;

		$site_url = get_setting_value($settings, "site_url", "");
		if ($is_ssl) {
			$site_url = get_setting_value($settings, "secure_url", $site_url);
		}
		$icons = array();
		$sql = " SELECT * FROM " . $table_prefix . "icons WHERE is_active=1 ORDER BY icon_order ";
		$db->query($sql);
		while ($db->next_record()) {
			$show_for_user = $db->f("show_for_user");
			$icon_code = $db->f("icon_code");
			$icon_image = $db->f("icon_image");
			if (!preg_match("/^http\:\/\//i", $icon_image)) {
				$icon_image = $site_url . $icon_image;
			}
			$icon_width = $db->f("icon_width");
			$icon_height = $db->f("icon_height");
			$icon_name = $db->f("icon_name");
			$icon_tag = "<img align=\"absmiddle\" src=\"" . $icon_image . "\" width=\"" . $icon_width . "\" height=\"" . $icon_height . "\" alt=\"" . $icon_name . "\" border=\"0\">";
			$icons[] = array("show_for_user" => $show_for_user, "code" => $icon_code, "image" => $icon_image, "width" => $icon_width, "height" => $icon_height, "name" => $icon_name);
			$icons_codes[] = $icon_code;
			$icons_tags[] = $icon_tag;
		}
	}


?>