<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  icon_select.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");
	include_once("./includes/icons_functions.php");

	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main", "icon_select.html");
	$css_file = "";
	if (isset($settings["style_name"]) && $settings["style_name"]) {
		$css_file = "styles/" . $settings["style_name"];
		if (isset($settings["scheme_name"]) && $settings["scheme_name"]) {
			$css_file .= "_" . $settings["scheme_name"];
		}
		$css_file .= ".css";
	}
	$t->set_var("css_file", $css_file);
	$t->set_var("icon_select_href", "icon_select.php");

	// prepare icons to replace in the text
	prepare_icons($icons, $icons_codes, $icons_tags);

	// parse
	parse_icons("icons", 8, 0);

	$t->pparse("main");

?>