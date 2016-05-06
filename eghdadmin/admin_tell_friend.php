<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_tell_friend.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once("./admin_common.php");

	$type = get_param("type");
	$art_cat_id = get_param("art_cat_id");
	if ($type == "ads") {
		$setting_type = "ads_tell_friend";
		$security_type = "ads";
		$section_name = ADS_TITLE;
		$section_url = "admin_ads.php";
		$admin_help_url = "admin_ads_notify_help.php";
	} else if ($type == "products") {
		$setting_type = "products_tell_friend";
		$security_type = "tell_friend";
		$section_name = PRODUCTS_MSG;
		$section_url = "admin_items_list.php";
		$admin_help_url= "admin_product_help.php";
	} else if ($type == "articles") {
		$setting_type = "articles_" .$art_cat_id. "_tell_friend";
		$security_type = "articles";
		$section_name = ARTICLES_TITLE;
		$section_url = "admin_articles_top.php";
		$sql = "SELECT category_name FROM " . $table_prefix . "articles_categories WHERE category_id=" . intval($art_cat_id);
		$category_name = get_translation(get_db_value($sql));
		$admin_help_url = "admin_article_help.php";
	} else {
		header ("Location: admin.php");
		exit;
	}

	$validation_types =
		array(
			array(2, FOR_ALL_USERS_MSG), array(1, UNREGISTERED_USER_ONLY_MSG), array(0, NOT_USED_MSG)
		);

	include_once("./admin_common.php");

	check_admin_security($security_type);

	$message_types =
		array(
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_tell_friend.html");

	$t->set_var("admin_href",            "admin.php");
	$t->set_var("admin_section_name",    $section_name);
	$t->set_var("admin_section_url",     $section_url);
	$t->set_var("admin_help_url",        $admin_help_url);
	$t->set_var("admin_articles_href",   "admin_articles.php");
	$t->set_var("admin_email_help_href", "admin_email_help.php");
	$t->set_var("admin_tell_friend_href","admin_tell_friend.php");

	$r = new VA_Record($table_prefix . "global_settings");
	// set up default settings parameters
	$r->add_textbox("default_comment", TEXT);
	// set up email parameters
	$r->add_textbox("user_mail_from", TEXT);
	$r->add_textbox("user_mail_cc", TEXT);
	$r->add_textbox("user_mail_bcc", TEXT);
	$r->add_textbox("user_mail_reply_to", TEXT);
	$r->add_textbox("user_mail_return_path", TEXT);
	$r->add_textbox("user_subject", TEXT);
	$r->add_radio("user_message_type", TEXT, $message_types);
	$r->add_textbox("user_message", TEXT);
	$r->add_radio("use_random_image", TEXT, $validation_types);

	$r->get_form_values();

	$pr = new VA_Record($table_prefix . "global_settings");
	$pr->add_checkbox("tell_friend_param", NUMBER, TELL_FRIEND_PARAM_MSG);
	$pr->get_form_values();

	$param_site_id = get_session("session_site_id");
	$operation = get_param("operation");
	$return_page = get_param("rp");
	if(!strlen($return_page)) {
		if($type == "articles") {
			$return_page = "admin_articles.php?category_id=" . urlencode($art_cat_id);
		} else {
			$return_page = $section_url;
		}
	}
	$t->set_var("rp", htmlspecialchars($return_page));

	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		if (!function_exists('imagecreate') && (($r->get_value("use_random_image") == 2) || ($r->get_value("use_random_image") == 1 ))) {
		  $errors .= RANDOM_IMAGE_VALIDATION_ERROR_MSG;
			$r->set_value("use_random_image",0);
		}

		if(!strlen($r->errors))
		{
			$sql  = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
			$sql .= " AND site_id=" . $db->tosql($param_site_id,INTEGER);
			$db->query($sql);

			// delete only tell a friend products settings
			$setting_name_where = "";
			foreach($pr->parameters as $key => $value) {
				if ($setting_name_where) { $setting_name_where .= " OR "; }
				$setting_name_where .= "setting_name=" . $db->tosql($key, TEXT);
			}
			if ($setting_name_where) {
				$sql  =  "DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='products'";
				$sql .= " AND (" . $setting_name_where . ") ";
				$sql .= " AND site_id=" . $db->tosql($param_site_id,INTEGER);
				$db->query($sql);
			}

			foreach($r->parameters as $key => $value) {
				$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
				$sql .= $db->tosql($setting_type, TEXT) . ", '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
				$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				
				$db->query($sql);
			}
			// addded tell a friend products settings
			foreach($pr->parameters as $key => $value)
			{
				$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
				$sql .= "'products', '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
				$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
	}
	else // get user_profile settings
	{
		foreach($r->parameters as $key => $value)
		{
			$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT) . " AND setting_name='" . $key . "'";
			$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
			$sql .= " ORDER BY site_id DESC ";
			$r->set_value($key, get_db_value($sql));
		}
		foreach($pr->parameters as $key => $value)
		{
			$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type='products' AND setting_name='" . $key . "'";
			$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
			$sql .= " ORDER BY site_id DESC ";
			$pr->set_value($key, get_db_value($sql));
		}

	}

	$r->set_parameters();
	$pr->set_parameters();
	$t->set_var("art_cat_id", htmlspecialchars($art_cat_id));
	$t->set_var("type", htmlspecialchars($type));
	if($art_cat_id) {
		$t->set_var("category_name", $category_name);
		$t->parse("articles_category");
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// multisites
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ", "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}	
	
	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

?>