<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_footer_link.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path."includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");

	check_admin_security("footer_links");

	$match_types = array(
		array(0, DON’T_MATCH_WITH_ITEM_MSG),
		array(1, MATCH_PAGE_NAME_ONLY_MSG),
		array(2, MATCH_PAGE_NAME_PARAMETERS_MSG),
	);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$site_url_path = $settings["site_url"] ? $settings["site_url"] : "../";
	$t->set_var("css_file", $site_url_path . "styles/" . $settings["style_name"] . ".css");
	$t->set_file("main","admin_footer_link.html");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", FOOTER_LINK_MSG, CONFIRM_DELETE_MSG));

	$admin_footer_links_url = new VA_URL("admin_footer_links.php", false);
	$admin_footer_links_url->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_footer_links_url->add_parameter("sort_dir", REQUEST, "sort_dir");
	$admin_footer_links_url->add_parameter("page", REQUEST, "page");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_footer_links_href", "admin_footer_links.php");
	$t->set_var("admin_footer_link_href", "admin_footer_link.php");
	$t->set_var("admin_footer_links_url", $admin_footer_links_url->get_url());

	$menu_id = get_param("menu_id");
	if (!$menu_id) {
		$sql = "SELECT MAX(menu_order) FROM " . $table_prefix . "header_links ";
		$menu_order = get_db_value($sql);
		$menu_order++;
	} else {
		$menu_order = 1;
	}

	$r = new VA_Record($table_prefix . "footer_links");
	$r->return_page = "admin_footer_links.php";

	$r->add_where("menu_id", INTEGER);

	$r->add_textbox("menu_order", INTEGER, MENU_ORDER_MSG);
	$r->change_property("menu_order", DEFAULT_VALUE, $menu_order);
	$r->add_textbox("menu_title", TEXT, MENU_TITLE_MSG);
	$r->change_property("menu_title", REQUIRED, true);
	$r->add_textbox("menu_url", TEXT, ADMIN_URL_SHORT_MSG);
	$r->change_property("menu_url", REQUIRED, true);

	//$r->add_textbox("menu_page", TEXT);
	$r->add_textbox("menu_target", TEXT, ADMIN_TARGET_MSG);
	$r->add_textbox("onclick_code", TEXT, ONCLICK_EVENT_MSG);
	$r->add_radio("match_type", INTEGER, $match_types);
	$r->change_property("match_type", DEFAULT_VALUE, 2);
	$r->change_property("match_type", SHOW, false);

	$r->add_checkbox("access_level", INTEGER);
	$r->parameters["access_level"][DEFAULT_VALUE] = 1;
	$r->add_checkbox("guest_access_level", INTEGER);
	$r->parameters["guest_access_level"][DEFAULT_VALUE] = 1;

	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>