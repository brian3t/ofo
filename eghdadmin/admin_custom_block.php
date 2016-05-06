<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_custom_block.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("custom_blocks");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$site_url_path = $settings["site_url"] ? $settings["site_url"] : "../";
	$t->set_var("css_file", $site_url_path . "styles/" . $settings["style_name"] . ".css");
	$t->set_var("html_editor", get_setting_value($settings, "html_editor", 1));
	$t->set_file("main","admin_custom_block.html");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", CUSTOM_BLOCKS_MSG, CONFIRM_DELETE_MSG));

	$admin_custom_blocks_url = new VA_URL("admin_custom_blocks.php", false);
	$admin_custom_blocks_url->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_custom_blocks_url->add_parameter("sort_dir", REQUEST, "sort_dir");
	$admin_custom_blocks_url->add_parameter("page", REQUEST, "page");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_custom_blocks_href", "admin_custom_blocks.php");
	$t->set_var("admin_custom_block_href", "admin_custom_block.php");
	$t->set_var("admin_custom_blocks_url", $admin_custom_blocks_url->get_url());

	$r = new VA_Record($table_prefix . "custom_blocks");
	$r->return_page = "admin_custom_blocks.php";

	$r->add_where("block_id", INTEGER);
	$r->add_textbox("block_name", TEXT, BLOCK_NAME);
	$r->parameters["block_name"][REQUIRED] = true;
	$r->add_textbox("block_notes", TEXT, BLOCK_NOTES_MSG);
	$r->add_textbox("block_title", TEXT, BLOCK_TITLE_MSG);
	$r->add_textbox("block_path", TEXT, CONTENT_FILE_MSG);
	$r->add_textbox("block_desc", TEXT, BLOCK_CONTENT_MSG);
	$r->add_hidden("sort_ord", TEXT);
	$r->add_hidden("sort_dir", TEXT);
	$r->set_event(AFTER_DELETE, "remove_layouts_blocks");
	$r->set_event(AFTER_VALIDATE, "check_block_fields");

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function check_block_fields()
	{
		global $r, $db, $table_prefix;
		if ($r->is_empty("block_path") && $r->is_empty("block_desc")) {
			$r->data_valid = false;
			$r->errors .= FILED_REQUIRED_MSG . "<b>" . $r->parameters["block_path"][CONTROL_DESC] . "</b> or <b>" . $r->parameters["block_desc"][CONTROL_DESC] . "</b>";
		}
	}

	function remove_layouts_blocks()
	{
		global $r, $db, $table_prefix;
		$block_name = "custom_block_" . $r->get_value("block_id");
		$sql = " DELETE FROM " . $table_prefix . "page_settings WHERE setting_name=" . $db->tosql($block_name, TEXT);
		$db->query($sql);
	}

?>