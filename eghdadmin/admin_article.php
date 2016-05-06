<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_article.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/articles_functions.php");
	include_once($root_folder_path . "includes/friendly_functions.php");

	check_admin_security("articles");

	$html_editor = get_setting_value($settings, "html_editor", 1);
	$category_id = get_param("category_id");
	$article_id  = get_param("article_id");
	if (!strlen($category_id)) {
		header("Location: admin.php");
		exit;
	} else {
		$sql  = " SELECT category_path, parent_category_id, allowed_rate ";
		$sql .= " FROM " . $table_prefix . "articles_categories ";
		$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$category_allowed_rate = $db->f("allowed_rate");
			$parent_category_id = $db->f("parent_category_id");
			$category_path = $db->f("category_path");
			if ($parent_category_id == 0) {
				$parent_category_id = $category_id;
			} else {
				$categories_ids = explode(",", $category_path);
				$parent_category_id = $categories_ids[1];
			}
		}
	}
	$sql  = " SELECT article_list_fields,article_details_fields,article_required_fields ";
	$sql .= " FROM " . $table_prefix . "articles_categories ";
	$sql .= " WHERE category_id=" . $db->tosql($parent_category_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$shown_fields = ",," . $db->f("article_list_fields") . "," . $db->f("article_details_fields") . ",,";
		$required_fields = ",," . $db->f("article_required_fields") . ",,";
	}

	$content_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);
	
	$article_fields = array(
		"article_date", "date_end", "article_title", "author_name", "author_email", 
		"author_url", "link_url", "download_url", "short_description", "full_description",
		"image_small", "image_large", "stream_video","keywords", "notes"
	);

 	$t = new VA_Template($settings["admin_templates_dir"]);
 	$site_url_path = $settings["site_url"] ? $settings["site_url"] : "../";
	$t->set_var("css_file", $site_url_path . "styles/" . $settings["style_name"] . ".css");
 	$t->set_file("main", "admin_article.html");

	$t->set_var("admin_articles_top_href", "admin_articles_top.php");
	$t->set_var("admin_article_href", "admin_article.php");
	$t->set_var("admin_upload_href",  "admin_upload.php");
	$t->set_var("admin_select_href",  "admin_select.php");
	$t->set_var("admin_articles_href", "admin_articles.php");
	$t->set_var("datetime_format", join("", $datetime_edit_format));
	$t->set_var("html_editor", $html_editor);
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ARTICLE_MSG, CONFIRM_DELETE_MSG));

	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}

	$tree = new VA_Tree("category_id", "category_name", "parent_category_id", $table_prefix . "articles_categories", "tree", "");
	$tree->show($category_id);

	$r = new VA_Record($table_prefix . "articles");

	if (get_param("apply")) {
		$r->redirect = false;
	}
	
	$r->return_page = "admin_articles.php?category_id=" . $category_id;
	$r->add_where("article_id", INTEGER);
	$r->change_property("article_id", USE_IN_INSERT, true);
	$r->add_hidden("category_id", INTEGER);
	
	//Common info
	$r->add_textbox("article_order", INTEGER, ADMIN_ORDER_MSG);
	$r->change_property("article_order", REQUIRED, true);
	$r->add_textbox("total_views", INTEGER);
	$r->change_property("total_views", USE_IN_INSERT, false);
	$r->change_property("total_views", USE_IN_UPDATE, false);
	$r->add_textbox("article_title", TEXT, TITLE_MSG);
	$r->change_property("article_title", MAX_LENGTH, 255);
	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);
	$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
	$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	$r->add_textbox("article_date", DATETIME, DATE_MSG);
	$r->change_property("article_date", VALUE_MASK, $datetime_edit_format);
	$r->add_textbox("date_end", DATETIME, DATE_END_MSG);
	$r->change_property("date_end", VALUE_MASK, $datetime_edit_format);
	$r->add_textbox("language_code", TEXT, LANGUAGE_CODE_MSG);
	$r->change_property("language_code", MAX_LENGTH, 2);
	$r->change_property("language_code", USE_SQL_NULL, false);
	//$r->add_textbox("article_template", TEXT, "Article Template");
	//$r->change_property("article_template", MAX_LENGTH, 255);

	$r->add_checkbox("is_remote_rss", INTEGER);
	$r->add_textbox("details_remote_url", TEXT);
	// author information
	$r->add_textbox("author_name", TEXT, AUTHOR_NAME_MSG);
	$r->change_property("author_name", MAX_LENGTH, 255);
	$r->add_textbox("author_email", TEXT, AUTHOR_EMAIL_MSG);
	$r->change_property("author_email", MAX_LENGTH, 255);
	$r->change_property("author_email", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("author_url", TEXT, AUTHOR_URL_MSG);
	$r->change_property("author_url", MAX_LENGTH, 255);
	$r->add_hidden("author_remote_address", TEXT);
	$r->change_property("author_remote_address", USE_IN_INSERT, true);
	
	// statuses
	$statuses = get_db_values("SELECT * FROM " . $table_prefix . "articles_statuses WHERE is_shown=1", array(array("", "")));
	$r->add_select("status_id", INTEGER, $statuses, STATUS_MSG);
	$r->change_property("status_id", REQUIRED, true);
	$r->add_checkbox("allowed_rate", INTEGER);
	
	// links 
	$r->add_textbox("link_url", TEXT, LINK_URL_MSG);
	$r->add_textbox("download_url", TEXT, DOWNLOAD_URL_MSG);
	// $r->add_textbox("is_link_direct", TEXT, "Link is direct");

	// article editors and date edit
	$r->add_hidden("created_user_id", INTEGER);
	$r->change_property("created_user_id", USE_IN_INSERT, true);
	$r->add_hidden("updated_user_id", INTEGER);
	$r->change_property("updated_user_id", USE_IN_INSERT, true);
	$r->change_property("updated_user_id", USE_IN_UPDATE, true);
	$r->add_hidden("created_admin_id", INTEGER);
	$r->change_property("created_admin_id", USE_IN_INSERT, true);
	$r->add_hidden("updated_admin_id", INTEGER);
	$r->change_property("updated_admin_id", USE_IN_INSERT, true);
	$r->change_property("updated_admin_id", USE_IN_UPDATE, true);

	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", USE_IN_INSERT, true);
	$r->add_textbox("date_updated", DATETIME);
	$r->change_property("date_updated", USE_IN_INSERT, true);
	$r->change_property("date_updated", USE_IN_UPDATE, true);
	
	// images
	$r->add_textbox("image_small", TEXT, IMAGE_SMALL_MSG);
	$r->add_textbox("image_small_alt", TEXT);
	$r->add_textbox("image_large", TEXT, IMAGE_LARGE_MSG);
	$r->add_textbox("image_large_alt", TEXT);
	// video

	$r->add_textbox("stream_video", TEXT, STREAM_VIDEO_MSG);
	$r->add_textbox("stream_video_width", INTEGER, STREAM_VIDEO_WIDTH_MSG);
	$r->add_textbox("stream_video_height", INTEGER, STREAM_VIDEO_HEIGHT_MSG);
	$r->add_textbox("stream_video_preview", TEXT, PREVIEW_VIDEO_MSG);

	// descs
	$r->add_checkbox("is_hot", INTEGER, SHOWN_ON_MAIN_PAGE_NOTE);
	$r->add_textbox("hot_description", TEXT, HOT_DESCRIPTION_MSG);
	$r->add_textbox("short_description", TEXT, SHORT_DESCRIPTION_MSG);
	$r->add_radio("is_html", INTEGER, $content_types);
	if ($html_editor){
		$r->change_property("is_html", SHOW, false);
	}
	$r->add_textbox("full_description", TEXT, FULL_DESCRIPTION_MSG);
	$r->add_textbox("keywords", TEXT, KEYWORDS_MSG);
	$r->add_textbox("notes", TEXT, NOTES_MSG);

	// meta data
	$r->add_textbox("meta_title", TEXT);
	$r->add_textbox("meta_keywords", TEXT);
	$r->add_textbox("meta_description", TEXT);
	
	// stats 
	$r->add_hidden("total_votes",  INTEGER, TOTAL_VOTES_MSG);
	$r->change_property("total_votes", USE_IN_INSERT, true);
	$r->add_hidden("total_points", INTEGER, TOTAL_POINTS_MSG);
	$r->change_property("total_points", USE_IN_INSERT, true);
	$r->add_hidden("rating", INTEGER, ADMIN_RATING_MSG);
	$r->change_property("rating", USE_IN_INSERT, true);
	$r->add_hidden("total_clicks", INTEGER, TOTAL_CLICKS_MSG);
	$r->change_property("total_clicks", USE_IN_INSERT, true);

	if ($html_editor){
		$r->set_value("is_html", 1);
	}
	for ($i = 0; $i < sizeof($article_fields); $i++) {
		$field_name = $article_fields[$i];
		if (!strpos($shown_fields, "," . $field_name . ",")) {
			$r->change_property($field_name, SHOW, false);
			if ($field_name == "article_date") {
				$r->set_value($field_name, va_time());
			}
		}
		if (strpos($required_fields, "," . $field_name . ",")) {
			$r->change_property($field_name, REQUIRED, true);
		}
	}

	if (!$r->parameters["image_small"][SHOW]) {
		$r->change_property("image_small_alt", SHOW, false);
	}
	if (!$r->parameters["image_large"][SHOW]) {
		$r->change_property("image_large_alt", SHOW, false);
	}
	
	if (!$r->parameters["stream_video"][SHOW]) {
		$r->change_property("stream_video_width", SHOW, false);
		$r->change_property("stream_video_height", SHOW, false);
		$r->change_property("stream_video_preview", SHOW, false);
	}

	$r->set_event(BEFORE_INSERT, "before_insert");	
	$r->set_event(BEFORE_UPDATE, "before_update");
	$r->set_event(AFTER_INSERT,  "after_insert");
	$r->set_event(AFTER_DELETE,  "after_delete");
	$r->set_event(AFTER_DEFAULT, "after_default");
	$r->process();
	
	if (strpos($shown_fields, ",link_url,") || strpos($shown_fields, ",download_url,")) {
		$t->parse("links_title", false);
	} else {
		$t->set_var("links_title", "");
	}
	if (strpos($shown_fields, ",author_name,") || strpos($shown_fields, ",author_email,") || strpos($shown_fields, ",author_url,")) {
		$t->parse("author_title", false);
	} else {
		$t->set_var("author_title", "");
	}

	if (strlen($article_id)) {
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	} else {
		$t->set_var("save_button", ADD_NEW_MSG);
		$t->set_var("delete", "");	
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
	
	function before_update() {
		global $r, $html_editor;
		set_friendly_url();
		$r->set_value("date_updated", va_time());
		$r->set_value("updated_admin_id", get_session("session_admin_id"));
		if ($html_editor){
			$r->set_value("is_html", 1);
		}
	}
	
	function before_insert() {
		global $r, $table_prefix, $html_editor;
		set_friendly_url();
		$article_id     = get_db_value("SELECT MAX(article_id) FROM " . $table_prefix . "articles") + 1;
		$remote_address = get_ip("REMOTE_ADDR");
		$r->set_value("article_id", $article_id);
		$r->set_value("author_remote_address", $remote_address);
		if ($r->is_empty("total_views")) {
			$r->set_value("total_views", 0);
		}
		if ($html_editor){
			$r->set_value("is_html", 1);
		}
		$r->set_value("total_votes",  0);
		$r->set_value("total_points", 0);
		$r->set_value("rating", 0);
		$r->set_value("total_clicks", 0);
		$r->set_value("created_admin_id", get_session("session_admin_id"));
		$r->set_value("updated_admin_id", get_session("session_admin_id"));
		$r->set_value("date_added", va_time());
		$r->set_value("date_updated", va_time());
	}
	
	function after_insert() {
		global $r, $db, $table_prefix, $category_id;
		
		$sql  = " SELECT MAX(article_order) FROM " . $table_prefix . "articles_assigned ";
		$sql .= " WHERE category_id = " . $db->tosql($category_id, INTEGER);
		$article_order = get_db_value($sql) + 1;
		
		$article_id = $r->get_value("article_id");
		$sql  = " INSERT INTO " . $table_prefix . "articles_assigned ";
		$sql .= " (article_id, category_id, article_order) VALUES (";
		$sql .= $db->tosql($article_id, INTEGER) . ",";
		$sql .= $db->tosql($category_id, INTEGER) . ",";
		$sql .= $db->tosql($article_order, INTEGER) . ")";
		$db->query($sql);
					
		$db->query("UPDATE " . $table_prefix . "articles_categories SET total_articles = total_articles + 1 WHERE category_id = " . $category_id);
	}
	
	function after_delete() {
		global $r;		
		$article_id = $r->get_value("article_id");
		VA_Articles::delete($article_id);
	}
	
	function after_default() {
		global $r, $db, $table_prefix, $category_id,  $category_allowed_rate;		
		$sql  = " SELECT MAX(article_order) FROM " . $table_prefix . "articles_assigned ";
		$sql .= " WHERE category_id = " . $db->tosql($category_id, INTEGER);
		$article_order = get_db_value($sql) + 1;
		$r->set_value("article_order", $article_order);
		$r->set_value("article_date", va_time());
		$r->set_value("status_id", 1);
		$r->set_value("allowed_rate", $category_allowed_rate);
		$r->set_value("is_html", 0);		
	}

?>