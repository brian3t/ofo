<?php                           

function article_reviews($block_name, $category_id)
{
	global $t, $rr, $articles_reviews_settings, $article_info;
	global $db, $table_prefix;
	global $settings, $page_settings;
	global $datetime_show_format, $meta_description;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_articles_reviews.html");

	// set urls
	$reviews_url = new VA_URL("articles_reviews.php");
	$reviews_url->add_parameter("category_id", REQUEST, "category_id");
	$reviews_url->add_parameter("article_id", REQUEST, "article_id");
	$t->set_var("all_reviews_url", $reviews_url->get_url());
	$reviews_url->add_parameter("filter", CONSTANT, "1");
	$t->set_var("positive_reviews_url", $reviews_url->get_url());
	$reviews_url->add_parameter("filter", CONSTANT, "-1");
	$t->set_var("negative_reviews_url", $reviews_url->get_url());

	$user_id = get_session("session_user_id");
	$allowed_view = get_setting_value($articles_reviews_settings, "allowed_view", 0);
	$allowed_post = get_setting_value($articles_reviews_settings, "allowed_post", 0);
	$reviews_per_page = get_setting_value($articles_reviews_settings, "reviews_per_page", 10);
	$review_random_image = get_setting_value($articles_reviews_settings, "review_random_image", 1);

	if (($review_random_image == 2) || ($review_random_image == 1 && !strlen(get_session("session_user_id")))) { 
		$use_validation = true;
	} else {
		$use_validation = false;
	}

	$article_id = get_param("article_id");
	
	if (!VA_Articles::check_exists($article_id)) {
		$t->set_var("item", "");
		$t->set_var("NO_ARTICLE_MSG", NO_ARTICLE_MSG);
		$t->parse("no_item", false);
		$t->parse("block_body", false);
		$t->parse($block_name, true);
		return;
	}
	
	if (!VA_Articles::check_permissions($article_id, false, VIEW_ITEMS_PERM)) {
		header ("Location: " . get_custom_friendly_url("user_login.php") . "?type_error=2");
		exit;
	}

	$articles_reviews_href = "articles_reviews.php";

	$recommended = 
		array( 
			array(1, YES_MSG), array(-1, NO_MSG)
			);

	$rating = 
		array( 
			array("", ""), array(1, BAD_MSG), array(2, POOR_MSG), 
			array(3, AVERAGE_MSG), array(4, GOOD_MSG), array(5, EXCELLENT_MSG),
			);

	$rr = new VA_Record($table_prefix . "articles_reviews");
	// global data
	$rr->operations[INSERT_ALLOWED] = (($allowed_post == 1) || ($allowed_post == 2 && get_session("session_user_id")));
	$rr->operations[UPDATE_ALLOWED] = false;
	$rr->operations[DELETE_ALLOWED] = false;
	$rr->operations[SELECT_ALLOWED] = false;
	$rr->redirect = false;
	$rr->success_messages[INSERT_SUCCESS] = SUBMIT_REVIEW_MSG;

	// internal fields
	$rr->add_where("review_id", INTEGER);
	$rr->add_textbox("article_id", INTEGER);
	$rr->change_property("article_id", DEFAULT_VALUE, $article_id);
	$rr->add_textbox("user_id", INTEGER);
	$rr->change_property("user_id", USE_SQL_NULL, false);
	$rr->add_textbox("admin_id", INTEGER);
	$rr->change_property("admin_id", USE_SQL_NULL, false);
	$rr->add_textbox("date_added", DATETIME);
	$rr->add_textbox("remote_address", TEXT);
	$rr->add_textbox("approved", INTEGER);

	// predefined fields
	$rr->add_radio("recommended", INTEGER, $recommended, RECOMMEND_ARTICLE_MSG);
	$rr->add_select("rating", INTEGER, $rating, RATE_IT_MSG);
	$rr->add_textbox("user_name", TEXT, NAME_ALIAS_MSG);
	$rr->change_property("user_name", REQUIRED, true);
	$rr->change_property("user_name", REGEXP_MASK, NICKNAME_REGEXP);
	$rr->change_property("user_name", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	$rr->set_control_event("user_name", AFTER_VALIDATE, "check_content");
	$rr->add_textbox("user_email", TEXT, EMAIL_FIELD);
	$rr->change_property("user_email", REQUIRED, true);
	$rr->change_property("user_email", REGEXP_MASK, EMAIL_REGEXP);
	$rr->set_control_event("user_email", AFTER_VALIDATE, "check_content");
	$rr->add_textbox("summary", TEXT, ONE_LINE_SUMMARY_MSG);
	$rr->set_control_event("summary", AFTER_VALIDATE, "check_content");
	$rr->add_textbox("comments", TEXT, DETAILED_COMMENT_MSG);
	$rr->set_control_event("comments", AFTER_VALIDATE, "check_content");

	// check parameters properties
	$default_params = array(
		1 => "recommended", 2 => "rating", 
		3 => "user_name", 4 => "user_email", 
		5 => "summary", 6 => "comments");

	foreach ($default_params as $param_order => $param_name) {
		$param_order = get_setting_value($articles_reviews_settings, $param_name . "_order", $param_order);
		$show_param = get_setting_value($articles_reviews_settings, "show_".$param_name, $param_order);
		$param_required = get_setting_value($articles_reviews_settings, $param_name . "_required", $param_order);
		$rr->change_property($param_name, SHOW, $show_param);
		$rr->change_property($param_name, CONTROL_ORDER, $param_order);
		$rr->change_property($param_name, REQUIRED, $param_required);
		$rr->change_property($param_name, TRIM, true);
	}
	if ($user_id) {	
		$user_info = get_session("session_user_info");
		$user_nickname = get_setting_value($user_info, "nickname", "");
		$user_email = get_setting_value($user_info, "email", "");
		if (strlen($user_nickname)) {
			$rr->change_property("user_name", SHOW, false);
		}
		if (strlen($user_email)) {
			$rr->change_property("user_email", SHOW, false);
		}
	}

	$rr->add_textbox("validation_number", TEXT, VALIDATION_CODE_FIELD);
	$rr->change_property("validation_number", USE_IN_INSERT, false);
	$rr->change_property("validation_number", USE_IN_UPDATE, false);
	$rr->change_property("validation_number", USE_IN_SELECT, false);
	if ($use_validation) {
		$rr->change_property("validation_number", REQUIRED, true);
		$rr->change_property("validation_number", SHOW, true);
		$rr->change_property("validation_number", AFTER_VALIDATE, "check_validation_number");
	} else {
		$rr->change_property("validation_number", SHOW, false);
	}

	// set events
	$rr->set_event(ON_DOUBLE_SAVE, "review_double_save");
	$rr->set_event(BEFORE_INSERT, "before_insert_review");
	$rr->set_event(AFTER_INSERT, "after_insert_review");
	$rr->set_event(BEFORE_VALIDATE, "additional_review_checks");
	$rr->set_event(BEFORE_SHOW, "review_form_check");

function check_content($parameter)
{
	global $rr;
	$control_name = $parameter[CONTROL_NAME];
	if ($parameter[IS_VALID] && check_banned_content($parameter[CONTROL_VALUE])) {
		$rr->parameters[$control_name][IS_VALID] = false;
		$rr->parameters[$control_name][ERROR_DESC] = "<b>".$parameter[CONTROL_DESC]."</b>: ".BANNED_CONTENT_MSG;
	}
}

function additional_review_checks()
{
	global $rr;
	if (check_black_ip()) {
		$rr->errors = BLACK_IP_MSG."<br>";	
	} else if (!check_add_article_review($rr->get_value("article_id"))) {
		$rr->errors = ALREADY_REVIEW_MSG."<br>";
	}
}

function review_form_check()
{
	global $rr, $articles_reviews_settings;
	$allowed_post = get_setting_value($articles_reviews_settings, "allowed_post", 0);

	if (!$allowed_post) {
		$rr->record_show = false;	
		$rr->success_message = NOT_ALLOWED_ADD_REVIEW_MSG;
	} else if ($allowed_post == 2 && !get_session("session_user_id")) {
		$rr->record_show = false;	
		$rr->success_message = REGISTERED_USERS_ADD_REVIEWS_MSG;
	} else if (check_black_ip()) {
		$rr->record_show = false;	
		$rr->errors = BLACK_IP_MSG;	
	} else if (!check_add_article_review($rr->get_value("article_id"))) {
		$rr->record_show = false;	
		if (!$rr->success_message) {
			$rr->success_message = ALREADY_REVIEW_MSG;
		}
	}
}

function before_insert_review()
{
	global $rr, $db, $articles_reviews_settings;
	$auto_approve = get_setting_value($articles_reviews_settings, "auto_approve", 1);
	$approved = ($auto_approve == 1) ? 1 : 0;
	$rr->set_value("date_added", va_time());
	$rr->set_value("remote_address", get_ip());
	$rr->set_value("approved", $approved);
	$rr->set_value("user_id", get_session("session_user_id"));
	$user_id = get_session("session_user_id");
	if ($user_id) {	
		$user_info = get_session("session_user_info");
		$user_nickname = get_setting_value($user_info, "nickname", "");
		$user_email = get_setting_value($user_info, "email", "");
		if (strlen($user_nickname)) {
			$rr->set_value("user_name", $user_nickname);
		}
		if (strlen($user_email)) {
			$rr->set_value("user_email", $user_email);
		}
	}

	if ($db->DBType == "postgre") {
		$sql = " SELECT NEXTVAL('seq_" . $table_prefix . "articles_reviews ') ";
		$new_review_id = get_db_value($sql);
		$rr->set_value("review_id", $new_review_id);
		$rr->change_property("review_id", USE_IN_INSERT, true);
	}

}
function after_insert_review()
{
	global $rr, $db, $t, $settings, $articles_reviews_settings, $article_info, $datetime_show_format;

	// record was added clear validation variable
	set_session("session_validation_number", "");

	// if review was approved update it rating
	if ($rr->get_value("approved") == 1) {
		update_article_rating($rr->get_value("article_id"));
	}

	// get last review id
	if ($db->DBType == "mysql") {
		$sql = " SELECT LAST_INSERT_ID() ";
		$new_review_id = get_db_value($sql);
		$rr->set_value("review_id", $new_review_id);
	} else if ($db->DBType == "access") {
		$sql = " SELECT @@IDENTITY ";
		$new_review_id = get_db_value($sql);
		$rr->set_value("review_id", $new_review_id);
	} else if ($db->DBType == "db2") {
		$new_review_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "articles_reviews FROM " . $table_prefix . "articles_reviews ");
		$rr->set_value("review_id", $new_review_id);
	}

	// check settings to send notifications
	$eol = get_eol();
	$admin_notification = get_setting_value($articles_reviews_settings, "admin_notification", 0);
	$user_email = $rr->get_value("user_email");
	$user_notification = get_setting_value($articles_reviews_settings, "user_notification", 0);
	if ($admin_notification || ($user_notification && $user_email)) {
		// set variables for email notifications
		$t->set_vars($article_info);
		$t->set_var("review_id", $rr->get_value("review_id"));
		$t->set_var("user_id", $rr->get_value("user_id"));
		$date_added_formatted = va_date($datetime_show_format, $rr->get_value("date_added"));
		$t->set_var("date_added", $date_added_formatted);

		$t->set_var("remote_address", $rr->get_value("remote_address"));
		$approved = $rr->get_value("approved");
		if ($approved == 1) {
			$approved_desc = YES_MSG;
		} else {
			$approved_desc = NO_MSG;
		}
		$t->set_var("is_approved", $approved_desc);
		$t->set_var("approved", $approved_desc);

		$recommended = $rr->get_value("recommended");
		if ($recommended == 1) {
			$recommended_desc = YES_MSG;
		} else if ($recommended == -1) {
			$recommended_desc = NO_MSG;
		}
		$t->set_var("is_recommended", $recommended_desc);
		$t->set_var("recommended", $recommended_desc);
		$t->set_var("rating", $rr->get_value("rating"));
		$t->set_var("user_name", $rr->get_value("user_name"));
		$t->set_var("user_email", $rr->get_value("user_email"));
		$t->set_var("summary", $rr->get_value("summary"));
		$t->set_var("comments", $rr->get_value("comments"));
	}

	// send email notification to admin
	if ($admin_notification)
	{
		$t->set_block("admin_subject", $articles_reviews_settings["admin_subject"]);
		$t->set_block("admin_message", $articles_reviews_settings["admin_message"]);

		$mail_to = get_setting_value($articles_reviews_settings, "admin_email", $settings["admin_email"]);
		$mail_to = str_replace(";", ",", $mail_to);
		$email_headers = array();
		$email_headers["from"] = get_setting_value($articles_reviews_settings, "admin_mail_from", $settings["admin_email"]);
		$email_headers["cc"] = get_setting_value($articles_reviews_settings, "admin_mail_cc");
		$email_headers["bcc"] = get_setting_value($articles_reviews_settings, "admin_mail_bcc");
		$email_headers["reply_to"] = get_setting_value($articles_reviews_settings, "admin_mail_reply_to");
		$email_headers["return_path"] = get_setting_value($articles_reviews_settings, "admin_mail_return_path");
		$email_headers["mail_type"] = get_setting_value($articles_reviews_settings, "admin_message_type");

		$t->parse("admin_subject", false);
		$t->parse("admin_message", false);

		$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
		va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers);
	}

	// send email notification to user
	if ($user_notification && $user_email)
	{
		$t->set_block("user_subject", $articles_reviews_settings["user_subject"]);
		$t->set_block("user_message", $articles_reviews_settings["user_message"]);

		$email_headers = array();
		$email_headers["from"] = get_setting_value($articles_reviews_settings, "user_mail_from", $settings["admin_email"]);
		$email_headers["cc"] = get_setting_value($articles_reviews_settings, "user_mail_cc");
		$email_headers["bcc"] = get_setting_value($articles_reviews_settings, "user_mail_bcc");
		$email_headers["reply_to"] = get_setting_value($articles_reviews_settings, "user_mail_reply_to");
		$email_headers["return_path"] = get_setting_value($articles_reviews_settings, "user_mail_return_path");
		$email_headers["mail_type"] = get_setting_value($articles_reviews_settings, "user_message_type");

		$t->parse("user_subject", false);
		$t->parse("user_message", false);

		$user_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("user_message"));
		va_mail($user_email, $t->get_var("user_subject"), $user_message, $email_headers);
	}

	// clear values and set default
	$rr->empty_values();
	$rr->set_default_values();
}

function review_double_save()
{
	global $rr;
	$rr->operation = "double";
	$rr->success_message = SUBMIT_REVIEW_MSG;
	$rr->empty_values();
	$rr->set_default_values();
}

	// check if article exists
	$sql  = " SELECT a.* ";
	$sql .= " FROM " . $table_prefix . "articles a, " . $table_prefix . "articles_statuses st ";
	$sql .= " WHERE st.allowed_view=1 ";
	$sql .= " AND a.article_id = " . $db->tosql($article_id, INTEGER);
	$db->query($sql);
	if($db->next_record())
	{
		$article_info = $db->Record;
		$article_title = get_translation($db->f("article_title"));
		$t->set_var("article_title", $article_title);
		$meta_description = $article_title . " " . REVIEWS_MSG;
		$is_article = true;
	}
	else
	{
		$article_title = ERRORS_MSG;
		$rr->errors = NO_ARTICLE_MSG;
		$t->set_var("article_title", ERRORS_MSG);
		$is_article = false;
	}

	$t->set_var("rnd",           va_timestamp());
	$t->set_var("articles_reviews_href",  $articles_reviews_href);
	$t->set_var("article_id",  htmlspecialchars($article_id));
	//$t->set_var("recommend_msg", $recommend_msg);

	$filter = get_param("filter");
	$remote_address = get_ip();

	$rr->process();

	$sql = " SELECT COUNT(*) FROM " . $table_prefix . "articles_reviews WHERE approved=1 AND rating <> 0 AND article_id=" . $db->tosql($article_id, INTEGER);
	$total_rating_votes = get_db_value($sql);

	$average_rating_float = 0;
	$total_rating_sum = 0;
	if($total_rating_votes)
	{
		$sql = " SELECT SUM(rating) FROM " . $table_prefix . "articles_reviews WHERE approved=1 AND rating <> 0 AND article_id=" . $db->tosql($article_id, INTEGER);
		$total_rating_sum = get_db_value($sql);
		$average_rating_float = round($total_rating_sum / $total_rating_votes, 2);
	}

	$t->set_var("current_category_id", htmlspecialchars($category_id));
	$t->set_var("article_id", htmlspecialchars($article_id));

	if($is_article && ($allowed_view == 1 || ($allowed_view == 2 && strlen($user_id))))
	{
		$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $articles_reviews_href);
		// count recommended reviews
		$sql = " SELECT COUNT(*) FROM " . $table_prefix . "articles_reviews WHERE recommended=1 AND approved=1 AND article_id=" . $db->tosql($article_id, INTEGER);
		$commend = get_db_value($sql);

		// count discommend reviews
		$sql = " SELECT COUNT(*) FROM " . $table_prefix . "articles_reviews WHERE recommended=-1 AND approved=1 AND article_id=" . $db->tosql($article_id, INTEGER);
		$discommend = get_db_value($sql);

		$total_votes = $commend + $discommend;

		if ($filter == 1) {
			$t->parse("all_reviews_link", false);
			$t->parse("positive_reviews", false);
			$t->parse("negative_reviews_link", false);
		} else if ($filter == -1) {
			$t->parse("all_reviews_link", false);
			$t->parse("positive_reviews_link", false);
			$t->parse("negative_reviews", false);
		} else {
			$t->parse("all_reviews", false);
			$t->parse("positive_reviews_link", false);
			$t->parse("negative_reviews_link", false);
		}
		
		if($total_votes)
		{
			// parse summary statistic
			$t->set_var("commend_percent", round($commend / $total_votes * 100, 0));
			$t->set_var("discommend_percent", round($discommend / $total_votes * 100, 0));
			$t->set_var("total_votes", $total_votes);

			$average_rating = round($average_rating_float, 0);
			$average_rating_image = $average_rating ? "rating-" . $average_rating : "not-rated";
			$t->set_var("average_rating_image", $average_rating_image);
			$t->set_var("average_rating_alt", $average_rating_float);

			$t->parse("summary_statistic", false);

			$sql    = " SELECT COUNT(*) FROM " . $table_prefix . "articles_reviews ";
			$where  = " WHERE (summary IS NOT NULL OR comments IS NOT NULL) ";
			$where .= " AND approved=1 AND article_id=" . $db->tosql($article_id, INTEGER);
			if (strlen($filter)) {
				$where .= " AND recommended=" . $db->tosql($filter, INTEGER);
			}
		
			$total_records = get_db_value($sql . $where);
			$t->set_var("total_records", $total_records);
			

			$record_number = 0;
			$records_per_page = $reviews_per_page ? $reviews_per_page : 10;
			$pages_number = 5;
			$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
  
			$sql = " SELECT * FROM " . $table_prefix . "articles_reviews ";
			$order_by = " ORDER BY date_added DESC";  
			$db->RecordsPerPage = $records_per_page;
			$db->PageNumber = $page_number;
			$db->query($sql . $where . $order_by);
			if($db->next_record())
			{
				$latest_comments = $db->f("comments");
				if($latest_comments) {
					$meta_description = $latest_comments;
				}
				do 
				{
					$record_number++;
					if($record_number > 1) {
						$t->parse("delimiter", false);
					} else {
						$t->set_var("delimiter", "");
					}
					$review_user_id = $db->f("user_id");
					$review_user_name = htmlspecialchars($db->f("user_name"));
					if (!$review_user_id) {
						$review_user_name .= " (" . GUEST_MSG . ")";
					}
					$review_user_class = $review_user_id ? "forumUser" : "forumGuest";
      
					if ($db->f("recommended") == 1) {
						$recommended_image = "commend";
					} else if ($db->f("recommended") == -1) {
						$recommended_image = "discommend";
					} else {
						$recommended_image = "neutral";
					}
					$t->set_var("recommended_image", $recommended_image);
					$rating = round($db->f("rating"), 0);
					$rating_image = $rating ? "rating-" . $rating : "not-rated";
					$t->set_var("rating_image", $rating_image);
					$t->set_var("review_user_class", $review_user_class);
					$t->set_var("review_user_name", $review_user_name);
					$date_added = $db->f("date_added", DATETIME);
					$date_added_string = va_date($datetime_show_format, $date_added);
					$t->set_var("review_date_added", $date_added_string);
					$t->set_var("review_summary", htmlspecialchars($db->f("summary")));
					$t->set_var("review_comments", nl2br(htmlspecialchars($db->f("comments"))));
      
					$t->parse("reviews_list", true);
				} while ($db->next_record());
				$t->parse("reviews", false);
			}
			else
				$t->parse("no_reviews", false);
		}
		else
		{
			$t->set_var("total_records", 0);
			$t->parse("no_reviews", false);
		}
	
		$t->parse("reviews_block", false);
	}

	$t->parse("item");
	$t->set_var("no_item", "");
	
	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

function check_validation_number()
{
	global $db, $rr;
	if($rr->get_property_value("validation_number", IS_VALID)) {
		$validated_number = check_image_validation($rr->get_value("validation_number"));
		if (!$validated_number) {
			$error_message = str_replace("{field_name}", VALIDATION_CODE_FIELD, VALIDATION_MESSAGE);
			$rr->change_property("validation_number", IS_VALID, false);
			$rr->change_property("validation_number", ERROR_DESC, $error_message);
		} else {
			// saved validated number for following submits	and delete this value in case of success
			set_session("session_validation_number", $validated_number);
		}
	}
}

?>