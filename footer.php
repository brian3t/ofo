<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  footer.php                                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
	include_once("./blocks/block_categories_list.php");

	$friendly_urls = get_setting_value($settings, "friendly_urls", 0);
	$friendly_extension = get_setting_value($settings, "friendly_extension", "");

	$user_id = get_session("session_user_id");		
	$user_info = get_session("session_user_info");
	$user_type_id = get_setting_value($user_info, "user_type_id", "");
	
	$t->set_template_path("./js");
	$t->set_file("footer_js", "footer.js");
	$t->parse("footer_js", false);
	
	

	$t->set_template_path($settings["templates_dir"]);
	
	//** EGGHEAD ADD - separate footer file for home page
	if($request_uri_path == '/') {
		$t->set_file("footer", "footerHome.html");
	} else {
		$t->set_file("footer", "footer.html");
	}
	//** END
	
	//$t->set_file("footer", "footer.html");
	
	$t->set_var("site_url", $settings["site_url"]);

	$t->set_var("index_href", get_custom_friendly_url("index.php"));
	$t->set_var("products_href", get_custom_friendly_url("products.php"));
	$t->set_var("basket_href", get_custom_friendly_url("basket.php"));
	$t->set_var("user_profile_href", get_custom_friendly_url("user_profile.php"));
	$t->set_var("admin_href", "admin.php");
	$footer_where = get_session("session_user_id") ? " access_level=1 " : " guest_access_level=1 ";
	$sql  = " SELECT fl.menu_title, fl.menu_url, fl.menu_target, fl.onclick_code ";
	$sql .= " FROM " . $table_prefix . "footer_links fl ";
	$sql .= " WHERE " . $footer_where ;
	$sql .= " ORDER BY fl.menu_order ";
	$db->query($sql);
	if ($db->next_record()) {
		$t->set_var("page_separator", "");
		do {
			$menu_title = get_translation($db->f("menu_title"));
			$menu_url = $db->f("menu_url");
			$menu_friendly_url = get_custom_friendly_url($menu_url);
			$menu_target = $db->f("menu_target");
			$onclick_code = $db->f("onclick_code");

			if ($menu_url == "index.php") {
				$menu_url = $site_url;
			} if (preg_match("/^\//", $menu_url)) {
				$menu_url = preg_replace("/^".preg_quote($site_path, "/")."/i", "", $menu_url);
				$menu_url = $site_url . get_custom_friendly_url($menu_url);
			} else if (!preg_match("/^http\:\/\//", $menu_url) && !preg_match("/^https\:\/\//", $menu_url) && !preg_match("/^javascript\:/", $menu_url)) {
				$menu_url = $site_url . $menu_friendly_url;
			}

			$t->set_var("menu_title", $menu_title);
			$t->set_var("menu_url", $menu_url);
			$t->set_var("menu_target", $menu_target);
			$t->set_var("onclick_code", $onclick_code);

			$t->sparse("footer_links");
			$t->sparse("page_separator", false);
		} while($db->next_record());
	} else {
		$t->set_var("custom_pages", "");
	}
	if ($settings["html_below_footer"]) {
		$html_below_footer = get_translation($settings["html_below_footer"]);
		if (get_setting_value($settings, "php_in_footer_body", 0)) {
			eval_php_code($html_below_footer);
		}
		$t->set_block("footer_html", $html_below_footer);
		$t->parse("footer_html", false);

		if ($t->block_exists("footer_block")) {
			$t->parse("footer_block", false);
		}
	} else {
		$t->set_var("footer_block", "");
	}

	$google_analytics = get_setting_value($settings, "google_analytics", 0);
	$google_tracking_code = get_setting_value($settings, "google_tracking_code", "");
	if ($google_analytics && $google_tracking_code) {
		if ($is_ssl) {
			$google_analytics_js = "https://ssl.google-analytics.com/urchin.js";
		} else {
			$google_analytics_js = "http://www.google-analytics.com/urchin.js";
		}
		$t->set_var("google_analytics_js", $google_analytics_js);
		$t->set_var("google_tracking_code", $google_tracking_code);
		$t->sparse("google_analytics", false);
	}

	if (isset($debug_mode) && $debug_mode) {
		$t->set_var("debug_buffer", $debug_buffer);
	}
	$categories = "";
	categories("footer", "test", "block_categories_footer.html");
	//$t->parse("footer");
	$t->set_var("footer", get_currency_message($t->get_var("footer"), $currency));

?>