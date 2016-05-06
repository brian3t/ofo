<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_global_settings.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/friendly_functions.php");
	include_once("./admin_common.php");

	check_admin_security("site_settings");

	$va_license_code = va_license_code();
	$current_site_url = get_setting_value($settings, "site_url", "");
	$parsed_url = parse_url($current_site_url);
	$friendly_path = isset($parsed_url["path"]) ? $parsed_url["path"] : "/";
	$domain_start_regexp = "/^http(s)?:\\/\\/[a-z0-9]/i";

	// additional connection 
	$dbs = new VA_SQL();
	$dbs->DBType      = $db_type;
	$dbs->DBDatabase  = $db_name;
	$dbs->DBUser      = $db_user;
	$dbs->DBPassword  = $db_password;
	$dbs->DBHost      = $db_host;
	$dbs->DBPort      = $db_port;
	$dbs->DBPersistent= $db_persistent;

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_global_settings.html");
	include_once("./admin_header.php");

	$t->set_var("admin_global_settings_href", "admin_global_settings.php");
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_layout_scheme_href", "admin_layout_scheme.php");
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");
	$t->set_var("friendly_path", $friendly_path);

	
	$r = new VA_Record($table_prefix . "global_settings");

	// load data to listbox
	$countries = get_db_values("SELECT country_id,country_name FROM " . $table_prefix . "countries ORDER BY country_order ", array(array("", "")));
	$states = get_db_values("SELECT state_id, state_name FROM " . $table_prefix . "states ORDER BY state_name ", array(array("", "")));
	
	$records_per_page = 
		array( 
			array(5, 5), array(10, 10), array(15, 15),
			array(20, 20), array(25, 25), array(50, 50),
			array(75, 75), array(100, 100)
			);

	$validation_types = 
		array( 
			array(2, FOR_ALL_USERS_MSG), array(1, UNREGISTERED_USER_ONLY_MSG), array(0, NOT_USED_MSG)
		);

	$yes_no = 
		array( 
			array(1, YES_MSG), array(0, NO_MSG)
			);

	$password_encrypt = 
		array( 
			array(0, NONE_MSG), 
			array(1, USE_MD5_ENCRYPTION_MSG)
			);


	$html_editors = 
		array( 
			array(1, WYSIWYG_HTML_EDITOR_MSG),
			array(0, TEXTAREA_EDITOR_MSG)
			);

	$friendly_auto_options =
		array( 
			array(0, DONT_GENERATE_FRIENDLY_URL_MSG),
			array(1, ALWAYS_GENERATE_FRIENDLY_URL_MSG),
			array(2, GENERATE_FRIENDLY_URL_MANUALLY_MSG)
			);

	$friendly_extensions =
		array( 
			array("", "[without extension] &nbsp;"),
			array(".html", ".html &nbsp;"),
			array(".htm", ".htm &nbsp;"),
			array(".php", ".php &nbsp;"),
			);

	//SMS notifications for unregistered users:
	$sms_allowed_options =
		array( 
			array(0, SMS_NOTE_NOT_ALLOWED_MSG),
			array(1, SMS_NOTIFY_ALLOWED_MSG),
			array(2, SMS_NOTE_ALLOWED_LIST_MSG),
			);
			
	// multisites
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ",null);
		$r->add_select("param_site_id", TEXT, $sites, ADMIN_SITE_MSG);
	}			
	
	$param_site_id = get_session("session_site_id");
	$sql  = " SELECT lt.layout_id, lt.layout_name FROM " . $table_prefix . "layouts AS lt"; 
	$sql .= " LEFT JOIN " . $table_prefix . "layouts_sites AS st ON st.layout_id = lt.layout_id ";		
	$sql .= " WHERE (lt.sites_all=1 OR st.site_id=".$db->tosql($param_site_id, INTEGER).") ";	
	$admin_templates_dir_values = get_db_values($sql, "");
			
	// set up parameters
	$r->add_textbox("site_name", TEXT, SITE_NAME_MSG);
	$r->change_property("site_name", REQUIRED, true);
	$r->change_property("site_name", USE_IN_INSERT, false);
	$r->add_textbox("site_url", TEXT, SITE_URL_MSG);
	$r->change_property("site_url", REQUIRED, true);
	$r->change_property("site_url", REGEXP_MASK, $domain_start_regexp);
	$r->add_checkbox("full_image_url", INTEGER);
	$r->add_textbox("admin_email", TEXT);
	$r->change_property("admin_email", REQUIRED, true);
	$r->add_select("country_id", INTEGER, $countries);
	$r->add_select("state_id", INTEGER, $states);
	$r->add_select("layout_id", INTEGER, $admin_templates_dir_values);
	$r->add_radio("password_encrypt", INTEGER, $password_encrypt);
	$r->change_property("password_encrypt", BEFORE_SHOW_VALUE, "disable_password_encrypt");
	$r->add_radio("admin_password_encrypt", INTEGER, $password_encrypt);
	$r->change_property("admin_password_encrypt", BEFORE_SHOW_VALUE, "disable_admin_password_encrypt");
	$r->add_textbox("weight_measure", TEXT);
	$r->add_radio("html_editor", INTEGER, $html_editors);

	// run php code
	$r->add_checkbox("php_in_index_greetings", INTEGER);
	$r->add_checkbox("php_in_footer_body", INTEGER);
	$r->add_checkbox("php_in_custom_blocks", INTEGER);
	$r->add_checkbox("php_in_custom_pages", INTEGER);

	$r->add_textbox("tmp_dir", TEXT, TEMP_FOLDER_MSG);
	$r->change_property("tmp_dir", BEFORE_VALIDATE, "check_tmp_dir");

	// logo settings
	$r->add_textbox("logo_image", TEXT);
	$r->add_textbox("logo_image_alt", TEXT);
	$r->add_textbox("logo_image_width", INTEGER, WIDTH_MSG);
	$r->add_textbox("logo_image_height", INTEGER, HEIGHT_MSG);

	// text editor settings
	$r->add_checkbox("user_image_upload", INTEGER);
	$r->add_textbox("user_image_size", INTEGER);
	$r->add_textbox("user_image_width", INTEGER);
	$r->add_textbox("user_image_height", INTEGER);
	$r->add_checkbox("show_preview_image_admin", INTEGER);
	$r->add_checkbox("show_preview_image_client", INTEGER);

	$r->add_radio("is_sms_allowed", INTEGER, $sms_allowed_options);

	$r->add_textbox("secure_url", TEXT, SECURE_SITE_URL_MSG);
	$r->change_property("secure_url", REGEXP_MASK, $domain_start_regexp);
	$r->change_property("secure_url", BEFORE_VALIDATE, "check_secure_url");
	$r->add_checkbox("secure_user_login", INTEGER);
	$r->add_checkbox("secure_user_profile", INTEGER);

	if ($va_license_code & 1) {
		$r->add_checkbox("secure_order_profile", INTEGER);
		$r->add_checkbox("secure_payments", INTEGER);
		$r->add_checkbox("secure_merchant_order", INTEGER);
		$r->add_checkbox("ssl_admin_order_details", INTEGER);
		$r->add_checkbox("secure_admin_order_create", INTEGER);
		$r->add_checkbox("ssl_admin_orders_list", INTEGER);
		$r->add_checkbox("ssl_admin_orders_pages", INTEGER);
	}
	if ($va_license_code & 4) {
		$r->add_checkbox("secure_user_tickets", INTEGER);
		$r->add_checkbox("secure_user_ticket", INTEGER);
		$r->add_checkbox("ssl_admin_tickets", INTEGER);
		$r->add_checkbox("ssl_admin_ticket", INTEGER);
		$r->add_checkbox("ssl_admin_helpdesk", INTEGER);
	}

	$r->add_checkbox("secure_admin_login", INTEGER);
	$r->add_checkbox("secure_redirect", INTEGER);

	$r->add_checkbox("friendly_urls", INTEGER, ACTIVATE_FRIENDLY_URLS_MSG);
	$r->change_property("friendly_urls", BEFORE_VALIDATE, "check_friendly_htaccess");
	$r->add_checkbox("friendly_url_redirect", INTEGER);
	$r->add_radio("friendly_auto", INTEGER, $friendly_auto_options);
	$r->add_radio("friendly_extension", TEXT, $friendly_extensions);

	// tracking fields
	$r->add_textbox("online_time", INTEGER);
	$r->add_checkbox("tracking_visits", INTEGER);
	$r->add_checkbox("tracking_pages", INTEGER);
	$r->add_checkbox("google_analytics", INTEGER);
	$r->add_textbox("google_tracking_code", TEXT);
	$r->change_property("google_tracking_code", TRIM, TRUE);

	$r->add_textbox("min_rating", FLOAT);
	$r->add_textbox("min_votes", INTEGER);

	$r->add_textbox("index_title", TEXT);
	$r->add_textbox("index_description", TEXT);
	$r->add_textbox("index_keywords", TEXT);
	$r->add_textbox("html_on_index", TEXT);
	$r->add_textbox("html_below_footer", TEXT);

	// SMTP settings
	$r->add_checkbox("smtp_mail", INTEGER);
	$r->add_textbox("smtp_host", TEXT);
	$r->add_textbox("smtp_port", INTEGER);
	$r->add_textbox("smtp_timeout", INTEGER);
	$r->add_textbox("smtp_username", TEXT);
	$r->add_textbox("smtp_password", TEXT);
	
	// Email function settings
	$r->add_textbox("email_additional_headers", TEXT);
	$r->add_textbox("email_additional_parameters", TEXT);
	
	// PGP settings
	$r->add_textbox("pgp_binary", TEXT);
	$r->add_textbox("pgp_home", TEXT);
	$r->add_textbox("pgp_tmp", TEXT);
	$r->add_textbox("pgp_keyserver", TEXT);
	$r->add_textbox("pgp_proxy", TEXT);
	$r->add_checkbox("pgp_ascii", INTEGER);
	$r->change_property("pgp_keyserver", SHOW, false); 

	// site map settings	
	$r->add_checkbox("site_map_custom_pages", INTEGER);
	$r->add_textbox("site_map_folder", TEXT);
	if ($sitelist) {
		$r->change_property("site_map_folder", SHOW, true);
	} else {
		$r->change_property("site_map_folder", SHOW, false);
	}
	$r->add_textbox("site_map_records_per_page", INTEGER);
	if ($va_license_code & 1) {
		$r->add_checkbox("site_map_categories", INTEGER);
		$r->add_checkbox("site_map_items", INTEGER);
		$r->change_property("site_map_items", SHOW, true);
	} else {
		$r->change_property("site_map_items", SHOW, false);
	}
	if ($va_license_code & 8) {
		$r->add_checkbox("site_map_forums", INTEGER);
		$r->add_checkbox("site_map_forum_categories", INTEGER);
		$r->change_property("site_map_forum_categories", SHOW, true);
	} else {
		$r->change_property("site_map_forum_categories", SHOW, false);
	}
	if ($va_license_code & 16) {
		$r->add_checkbox("site_map_ad_categories", INTEGER);
		$r->add_checkbox("site_map_ads", INTEGER);
		$r->change_property("site_map_ads", SHOW, true);
	} else {
		$r->change_property("site_map_ads", SHOW, false);
	}
	if ($va_license_code & 32) {
		$r->add_checkbox("site_map_manuals", INTEGER);
		$r->add_checkbox("site_map_manual_articles", INTEGER);
		$r->add_checkbox("site_map_manual_categories", INTEGER);
		$r->change_property("site_map_manual_categories", SHOW, true);
	} else {
		$r->change_property("site_map_manual_categories", SHOW, false);
	}

	$articles_categories = array();
	if ($va_license_code & 2) {
		$sql  = " SELECT ac.category_id, ac.category_name ";
		$sql .= " FROM " . $table_prefix . "articles_categories ac ";
		$sql .= " LEFT JOIN " . $table_prefix . "articles_categories_sites AS st ON st.category_id = ac.category_id ";
		$sql .= " WHERE ac.parent_category_id=0 ";
		$sql .= " AND (ac.sites_all=1 OR st.site_id=".$db->tosql($param_site_id, INTEGER).") ";
		$sql .= " GROUP BY ac.category_id, ac.category_name";
		$db->query($sql);
		while ($db->next_record()) {
			$row_cat_id = $db->f("category_id");
			$row_cat_name = get_translation($db->f("category_name"), $language_code);
			$r->add_checkbox("site_map_articles_categories_" . $row_cat_id, INTEGER);
			$r->add_checkbox("site_map_articles_" . $row_cat_id, INTEGER);
			$articles_categories[$row_cat_id] = $row_cat_name;
		}
	}

	$r->get_form_values();

	$site_url = $r->get_value("site_url");
	if (strlen($site_url) && substr($site_url, strlen($site_url) - 1) != "/") {
		$site_url .= "/";
		$r->set_value("site_url", $site_url);
	}

	$secure_url = $r->get_value("secure_url");
	if (strlen($secure_url) && substr($secure_url, strlen($secure_url) - 1) != "/") {
		$secure_url .= "/";
		$r->set_value("secure_url", $secure_url);
	}

	$google_tracking_code = $r->get_value("google_tracking_code");
	if (preg_match("/^[\"'](.*)[\"']$/", $google_tracking_code, $match)) {
		$r->set_value("google_tracking_code", $match[1]);
	}

	$operation = get_param("operation");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }
	$return_page = get_param("rp");
	if (!strlen($return_page)) $return_page = "admin.php";
	
	$message_build_xml = "";

	$site_map_folder = $r->get_value("site_map_folder");
	if (!$site_map_folder) {
		$site_map_folder  = dirname (__FILE__) . "/../";
		$r->set_value("site_map_folder", $site_map_folder);
	}
	$filename = $site_map_folder . "/sitemap_index.xml";
	
	if (file_exists($filename)){
		$size = filesize($filename);
		$fp = @fopen($filename, "r");
		$contents = fread($fp, $size);
		@fclose($fp);
		if (preg_match_all("/<lastmod>(.*)\<\/lastmod>/Uis", $contents, $matches, PREG_SET_ORDER)){
			$datetime_loc_format = array("YYYY", "-", "MM", "-", "DD", "T", "HH", ":", "mm", ":", "ss", "+00:00");
			$date_modified_value = parse_date($matches[0][1], $datetime_loc_format, $date_errors);
			$date_modified = va_date($datetime_show_format, $date_modified_value);
			$message_build_xml = str_replace("{creation_date}", $date_modified, SM_LATEST_BUILD);
			$message_build_xml = str_replace("{filename}", "sitemap_index.xml", $message_build_xml);
		}

	}

	if ($operation == "build_xml"){
		include("./admin_site_map_xml_build.php");
		$operation = "";
	}
	if (strlen($operation))	{
		$tab = "general";
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $r->validate();

		if (!strlen($r->errors))
		{			
			// update site name 
			$sql  = " UPDATE " . $table_prefix . "sites ";
			$sql .= " SET site_name=" . $db->tosql($r->get_value("site_name"), TEXT);
			$sql .= " WHERE site_id=" . $db->tosql($param_site_id, INTEGER);
			$db->query($sql);

			// check password ecnryption for users
			$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type='global' AND setting_name='password_encrypt' ";
			$old_password_encrypt = get_db_value($sql);
			$new_password_encrypt = $r->get_value("password_encrypt");

			// check password ecnryption for admins
			$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type='global' AND setting_name='admin_password_encrypt' ";
			$old_admin_password_encrypt = get_db_value($sql);
			$new_admin_password_encrypt = $r->get_value("admin_password_encrypt");
			
			$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type='global' AND setting_name='friendly_auto' AND site_id=". $db->tosql($param_site_id, INTEGER);
			$old_friendly_auto = get_db_value($sql);

			$sql = "DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='global' AND site_id=". $db->tosql($param_site_id, INTEGER);
			$db->query($sql);
			$sql = "DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='site_map' AND site_id=". $db->tosql($param_site_id, INTEGER);
			$db->query($sql);
			foreach ($r->parameters as $key => $value)
			{				
				if ($r->get_property_value($key, USE_IN_INSERT)) {
					if (strpos($key, "site_map") === false) {					
						$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
						$sql .= "'global', '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . "," . $db->tosql($param_site_id, INTEGER) . ")";						
					} else {
						$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
						$sql .= "'site_map', '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . "," . $db->tosql($param_site_id, INTEGER) .  ")";
					}
					$db->query($sql);
					if ($key == "password_encrypt") {
						// use same value for all sites
						$sql  = " UPDATE " . $table_prefix . "global_settings SET setting_value = ".$db->tosql($value[CONTROL_VALUE], TEXT);
						$sql .= " WHERE setting_type='global' AND setting_name='password_encrypt'";
						$db->query($sql);
					} else if ($key == "admin_password_encrypt") {
						// use same value for all sites
						$sql  = " UPDATE " . $table_prefix . "global_settings SET setting_value = ".$db->tosql($value[CONTROL_VALUE], TEXT);
						$sql .= " WHERE setting_type='global' AND setting_name='admin_password_encrypt'";
						$db->query($sql);
					}
				}
			}
			set_session("session_settings", "");
			session_unregister("session_settings");

			// check if user password encrypt option was changed to md5
			if ($new_password_encrypt == 1 && $new_password_encrypt != $old_password_encrypt) {
				$sql  = " SELECT user_id, password FROM " . $table_prefix . "users ";
				$db->query($sql);
				while ($db->next_record()) {
					$user_id = $db->f("user_id");
					$password = $db->f("password");
					if (!preg_match("/[0-9a-f]{32}/i", $password)) {
						$sql  = " UPDATE " . $table_prefix . "users SET ";
						$sql .= " password=" . $db->tosql(md5($password), TEXT);
						$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
						$dbs->query($sql);
					}
				}
			}

			// check if admin password encrypt optin was changed to md5
			if ($new_admin_password_encrypt == 1 && $new_admin_password_encrypt != $old_admin_password_encrypt) {
				$sql  = " SELECT admin_id, password FROM " . $table_prefix . "admins ";
				$db->query($sql);
				while ($db->next_record()) {
					$admin_id = $db->f("admin_id");
					$password = $db->f("password");
					if (!preg_match("/[0-9a-f]{32}/i", $password)) {
						$sql  = " UPDATE " . $table_prefix . "admins SET ";
						$sql .= " password=" . $db->tosql(md5($password), TEXT);
						$sql .= " WHERE admin_id=" . $db->tosql($admin_id, INTEGER);
						$dbs->query($sql);
					}
				}
			}

			$friendly_urls = $r->get_value("friendly_urls");
			$friendly_auto = $r->get_value("friendly_auto");
			// check if friendly url functionality was turn on with automatic links generation
			if ($friendly_urls && ($friendly_auto == 1 || $friendly_auto == 2) && $friendly_auto != $old_friendly_auto) {

				foreach ($friendly_tables as $table_name => $table_info) {
					$key_field = $table_info[0];
					$title_field = $table_info[1];
					$sql  = " SELECT " . $key_field . ", " . $title_field . " FROM " . $table_name;
					$sql .= " WHERE friendly_url IS NULL OR friendly_url='' ";
					$dbs->query($sql);
					while ($dbs->next_record()) {
						$key_id = $dbs->f($key_field);
						$title_value = get_translation($dbs->f($title_field));
						$friendly_url = generate_friendly_url($title_value);
						$sql  = " UPDATE " . $table_name . " SET ";
						$sql .= " friendly_url=" . $db->tosql($friendly_url, TEXT);
						$sql .= " WHERE " . $key_field . "=" . $db->tosql($key_id, INTEGER);
						$db->query($sql);
					}
				}
			}

			header("Location: " . $return_page);
			exit;
		}
		else // parse errors
		{
			$t->set_var("errors_list", $r->errors);
			$t->parse("errors", false);
			foreach ($articles_categories as $row_cat_id => $row_cat_name)
			{
				$t->set_var("row_cat_id", $row_cat_id);
		
				if (defined("SM_SHOW_ARTICLES_CAT_MSG")) {
					$sm_show_articles_cat = str_replace("{row_cat_name}", $row_cat_name, SM_SHOW_ARTICLES_CAT_MSG);
					$sm_show_articles = str_replace("{row_cat_name}", $row_cat_name, SM_SHOW_ARTICLES_MSG);
				} else {
					$sm_show_articles_cat = $row_cat_name . " " . CATEGORIES_TITLE;
					$sm_show_articles = $row_cat_name  . " " . ARTICLES_TITLE;
				}
				$t->set_var("SM_SHOW_ARTICLES_CAT_MSG", $sm_show_articles_cat);
				$t->set_var("SM_SHOW_ARTICLES_MSG", $sm_show_articles);
				if ($r->get_value("site_map_articles_categories_" . $row_cat_id)) {
					$t->set_var("site_map_articles_categories", "checked");
				} else {
					$t->set_var("site_map_articles_categories", "");
				}
				if ($r->get_value("site_map_articles_" . $row_cat_id)) {
					$t->set_var("site_map_articles", "checked");
				} else {
					$t->set_var("site_map_articles", "");
				}
				$t->parse("map_articles_settings", true);
			}
		}
	}
	else // get global settings
	{			
		$sql  = "SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= "WHERE setting_type='global' ";
		$sql .= "AND ( site_id=" . $db->tosql($param_site_id, INTEGER)." OR site_id=1 ) ";
		$sql .= "ORDER BY site_id ASC";
		$db->query($sql);
		while ($db->next_record()) {
			$setting_name = $db->f("setting_name");
			$setting_value = $db->f("setting_value");
			if ($r->parameter_exists($setting_name)) {
				$r->set_value($setting_name, $setting_value);
			}
		}
		$sql  = " SELECT site_name FROM " . $table_prefix . "sites ";
		$sql .= " WHERE site_id=" . $db->tosql($param_site_id, INTEGER);
		$site_name = get_db_value($sql);
		$r->set_value("site_name", $site_name);

		// get site map settings
		$sql  = "SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= "WHERE setting_type='site_map' "; 
		if ($multisites_version) {
			$sql .= "AND ( site_id=" . $db->tosql($param_site_id, INTEGER)." OR site_id=1 ) ";
			$sql .= "ORDER BY site_id ASC";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$setting_name = $db->f("setting_name");
			$setting_value = $db->f("setting_value");
			if ($r->parameter_exists($setting_name)) {
				$r->set_value($setting_name, $setting_value);
			}
		}
		if ($sitelist) {
			$r->set_value("param_site_id", $param_site_id);
		}
		foreach ($articles_categories as $row_cat_id => $row_cat_name)
		{
			$t->set_var("row_cat_id", $row_cat_id);
	
			if (defined("SM_SHOW_ARTICLES_CAT_MSG")) {
				$sm_show_articles_cat = str_replace("{row_cat_name}", $row_cat_name, SM_SHOW_ARTICLES_CAT_MSG);
				$sm_show_articles = str_replace("{row_cat_name}", $row_cat_name, SM_SHOW_ARTICLES_MSG);
			} else {
				$sm_show_articles_cat = $row_cat_name . " " . CATEGORIES_TITLE;
				$sm_show_articles = $row_cat_name  . " " . ARTICLES_TITLE;
			}
			$t->set_var("SM_SHOW_ARTICLES_CAT_MSG",$sm_show_articles_cat);
			$t->set_var("SM_SHOW_ARTICLES_MSG",$sm_show_articles);
			if ($r->get_value("site_map_articles_categories_".$row_cat_id)) {
				$t->set_var("site_map_articles_categories", "checked");
			} else {
				$t->set_var("site_map_articles_categories", "");
			}
			if ($r->get_value("site_map_articles_".$row_cat_id)) {
				$t->set_var("site_map_articles", "checked");
			} else {
				$t->set_var("site_map_articles", "");
			}
			$t->parse("map_articles_settings", true);
		}
	}

	if (strlen($message_build_xml)) {
		$t->set_var("message_build_xml", $message_build_xml);
		$t->sparse("success_build_xml", false);
	} 

	$r->set_parameters();
	$t->set_var("rp", htmlspecialchars($return_page));

	$layout_scheme = "";
	$active_layout_id = $settings["layout_id"];
	$sql  = " SELECT lt.layout_name FROM " . $table_prefix . "layouts AS lt"; 
	$sql .= " LEFT JOIN " . $table_prefix . "layouts_sites AS st ON st.layout_id = lt.layout_id ";		
	$sql .= " WHERE lt.layout_id=" . $db->tosql($active_layout_id, INTEGER);
	$sql .= " AND (lt.sites_all=1 OR st.site_id=".$db->tosql($param_site_id, INTEGER).") ";
	$db->query($sql);
	if ($db->next_record()) {
		$layout_lc = strtolower($db->f("layout_name"));
		if ($dir = @opendir($root_folder_path . "styles")) 
		{
			$dir_values = array();
			while ($file = readdir($dir)) {
				if (preg_match("/^" . $layout_lc . "\_/", $file)) { 
					$dir_values[] = $file;
				}
			}
			closedir($dir);
			if (sizeof($dir_values) > 1) {
				$layout_scheme = "<a href=\"#\" onCLick=\"window.location='admin_layout_scheme.php'; return false;\">Change Active Scheme</a>";
			}
		}
	}
	$t->set_var("layout_scheme", $layout_scheme);

	$tabs = array("general" => ADMIN_GENERAL_MSG, "smtp" => "E-MAIL", "site_map" => SITE_MAP_TITLE, "pgp" => "PGP");
	foreach ($tabs as $tab_name => $tab_title) {
		$t->set_var("tab_id", "tab_" . $tab_name);
		$t->set_var("tab_name", $tab_name);
		$t->set_var("tab_title", $tab_title);
		if ($tab_name == $tab) {
			$t->set_var("tab_class", "adminTabActive");
			$t->set_var($tab_name . "_style", "display: block;");
		} else {
			$t->set_var("tab_class", "adminTab");
			$t->set_var($tab_name . "_style", "display: none;");
		}
		$t->parse("tabs", $tab_title);
	}
	$t->set_var("tab", $tab);

	include_once("./admin_footer.php");

	if ($sitelist) {
		$t->parse("sitelist");
	}
	
	$t->pparse("main");

	function disable_password_encrypt($parameters)
	{
		global $r, $t;
		$current_value = $parameters["current_value"];
		if ($r->get_value("password_encrypt") == 1) {
			if ($current_value == "1") {
				$t->set_var("password_encrypt_disabled", "");
			} else {
				$t->set_var("password_encrypt_disabled", "disabled");
			}
		} else {
			$t->set_var("password_encrypt_disabled", "");
		}
	}

	function disable_admin_password_encrypt($parameters)
	{
		global $r, $t;
		$current_value = $parameters["current_value"];
		if ($r->get_value("admin_password_encrypt") == 1) {
			if ($current_value == "1") {
				$t->set_var("admin_password_encrypt_disabled", "");
			} else {
				$t->set_var("admin_password_encrypt_disabled", "disabled");
			}
		} else {
			$t->set_var("admin_password_encrypt_disabled", "");
		}
	}

	function check_tmp_dir() 
	{
		global $r;

		$auto_tmp = false;
		$tmp_dir = $r->get_value("tmp_dir");
		if ($tmp_dir) {
			if (preg_match("/\//", $tmp_dir)) {
				if (!preg_match("/\/$/", $tmp_dir)) { $tmp_dir .= "/"; }
			} else if (preg_match("/\\\\/", $tmp_dir)) {
				if (!preg_match("/\\\\$/", $tmp_dir)) { $tmp_dir .= "\\"; }
			}
			$r->set_value("tmp_dir", $tmp_dir);
			if (!is_dir($tmp_dir)) {
				$r->errors .= FOLDER_DOESNT_EXIST_MSG . $tmp_dir;
			} else {
				$tmp_file = $tmp_dir . "tmp_" . md5(uniqid(rand(), true)) . ".txt";
				$fp = @fopen($tmp_file, "w");
				if ($fp === false) {
					$r->errors .= str_replace("{folder_name}", $tmp_dir, FOLDER_PERMISSION_MESSAGE);
				} else {
					fclose($fp);
					unlink($tmp_file);
				}
			}
		} else if ($auto_tmp) {
			// auto-update temporary folder
			if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
				$auto_tmp_dir = get_var("TEMP");
				if (!$auto_tmp_dir) { $auto_tmp_dir = get_var("TMP"); }
			} else {
				$auto_tmp_dir = "/tmp/";
			}
			if (preg_match("/\//", $auto_tmp_dir)) {
				if (!preg_match("/\/$/", $auto_tmp_dir)) { $auto_tmp_dir .= "/"; }
			} else if (preg_match("/\\\\/", $auto_tmp_dir)) {
				if (!preg_match("/\\\\$/", $auto_tmp_dir)) { $auto_tmp_dir .= "\\"; }
			}
			if (is_dir($auto_tmp_dir)) {
				$tmp_file = $auto_tmp_dir . "tmp_" . md5(uniqid(rand(), true)) . ".txt";
				$fp = @fopen($tmp_file, "w");
				if ($fp !== false) {
					fclose($fp);
					unlink($tmp_file);
					$r->set_value("tmp_dir", $auto_tmp_dir);
				}
			}
		}
	}

	function check_secure_url()
	{
		global $r, $domain_start_regexp, $va_license_code;

		$ssl_options_check = array(
			"secure_admin_login", 
		);
		if ($va_license_code & 1) {
			$ssl_options_check[] = "secure_order_profile";
			$ssl_options_check[] = "secure_merchant_order";
			$ssl_options_check[] = "ssl_admin_order_details";
			$ssl_options_check[] = "secure_admin_order_create";
			$ssl_options_check[] = "ssl_admin_orders_list";
			$ssl_options_check[] = "ssl_admin_orders_pages";
		}
		if ($va_license_code & 4) {
			$ssl_options_check[] = "secure_user_tickets";
			$ssl_options_check[] = "secure_user_ticket";
			$ssl_options_check[] = "ssl_admin_tickets";
			$ssl_options_check[] = "ssl_admin_ticket";
			$ssl_options_check[] = "ssl_admin_helpdesk";
		}

		$check_domain = false;
		for ($s = 0; $s < sizeof($ssl_options_check); $s++) {
			$ssl_option_name = $ssl_options_check[$s];
			$ssl_option = $r->get_value($ssl_option_name);
			if ($ssl_option) { $check_domain = true; break; }
		}

		$site_url = $r->get_value("site_url");
		$secure_url = $r->get_value("secure_url");
		if ($check_domain && preg_match($domain_start_regexp, $site_url) && preg_match($domain_start_regexp, $secure_url)) {
			$site_url_parsed = parse_url($site_url);
			$secure_url_parsed = parse_url($secure_url);
			$site_url_host = $site_url_parsed["host"];
			$secure_url_host = $secure_url_parsed["host"];
			if (!preg_match("/".preg_quote($site_url_host, "/")."$/i", $secure_url_host)) {
				$r->errors .= HOSTNAMES_SHOULDBE_SAME_MSG;
			}
		}
	}

	function check_friendly_htaccess()
	{
		global $r;
		$allow_friendly_urls = true;
		$friendly_urls = $r->get_value("friendly_urls");
		$server_software = get_var("SERVER_SOFTWARE");
		if ($friendly_urls && preg_match("/apache/i", $server_software)) {
			$htaccess_file = "../.htaccess";
			if (!file_exists($htaccess_file)) {
				$allow_friendly_urls = false;
			}
		}
		if (!$allow_friendly_urls) {
			$r->parameters["friendly_urls"][IS_VALID] = false;
			$r->parameters["friendly_urls"][ERROR_DESC] = "<b>".$r->parameters["friendly_urls"][CONTROL_DESC] . "</b>: " . FILE_DOESNT_EXIST_MSG . " <b>".$htaccess_file."</b>";
		}
	}

?>