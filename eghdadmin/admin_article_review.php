<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_article_review.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path."includes/record.php");
	include_once($root_folder_path."includes/reviews_functions.php");
	include_once($root_folder_path."messages/".$language_code."/reviews_messages.php");

	include_once("./admin_common.php");

	check_admin_security("articles_reviews");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_article_review.html");
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_article_review_href", "admin_article_review.php");
	$t->set_var("admin_articles_reviews_href", "admin_articles_reviews.php");
	$t->set_var("admin_articles_href", "admin_articles.php");
	$t->set_var("admin_articles_top_href", "admin_articles_top.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_REVIEW_MSG, CONFIRM_DELETE_MSG));

	$art_cat_id = get_param("art_cat_id");
	if (strlen($art_cat_id)) {
		$sql  = " SELECT category_name FROM " . $table_prefix . "articles_categories ";
		$sql .= " WHERE category_id=" . $db->tosql($art_cat_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$articles_category = $db->f("category_name");
		} else {
			$art_cat_id = "";
		}
	}

	$articles_reviews_url = "admin_articles_reviews.php";
	$r = new VA_Record($table_prefix . "articles_reviews");
	$r->return_page = $articles_reviews_url;

	$yes_no = 
		array( 
			array(0, NO_MSG), array(1, YES_MSG)
		);

	$rating_options = 
		array( 
			array("", ""), array(1, BAD_MSG), array(2, POOR_MSG), 
			array(3, AVERAGE_MSG), array(4, GOOD_MSG), array(5, EXCELLENT_MSG),
			);
	$recommended_options = 
		array( 
			array("", ""), array(1, YES_MSG), array(-1, NO_MSG), 
			);


	$r->add_where("review_id", INTEGER);
	$r->add_textbox("article_id", INTEGER);
	$r->change_property("article_id", USE_IN_UPDATE, false);
	$r->add_radio("approved", INTEGER, $yes_no);
	$r->add_textbox("date_added", DATETIME, REVIEW_DATE_MSG);
	$r->change_property("date_added", VALUE_MASK, $datetime_show_format);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	$r->add_textbox("summary", TEXT);
	$r->add_textbox("comments", TEXT);
	$r->add_textbox("user_name", TEXT, USER_NAME_MSG);
	$r->add_textbox("user_email", TEXT, EMAIL_MSG);
	$r->add_textbox("remote_address", TEXT);
	$r->change_property("remote_address", USE_IN_UPDATE, false);
	$r->add_radio("recommended", INTEGER, $recommended_options);
	$r->change_property("recommended", REQUIRED, true);
	$r->add_radio("rating", INTEGER, $rating_options);

	$r->get_form_values();

	$review_id = get_param("review_id");
	if(!strlen($review_id))	
	{
		header("Location: " . $r->return_page);
		exit;
	}

	$operation = get_param("operation");
	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $r->return_page);
			exit;
		}
		else if($operation == "delete" && $review_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "articles_reviews WHERE review_id=" . $db->tosql($review_id, INTEGER));		

			update_product_rating($r->get_value("item_id"));

			header("Location: " . $r->return_page);
			exit;
		}

		$is_valid = $r->validate();

		if($is_valid)
		{
			if (strlen($r->get_value("review_id"))) {
				$r->update_record();
			} else {
				//posibility to add review from admin
				//$r->insert_record();
			}
			update_product_rating($r->get_value("item_id"));

			header("Location: " . $r->return_page);
			exit;
		}
	}
	else if(strlen($r->get_value("review_id")))
	{
		$r->get_db_values();
	}

	$r->set_parameters();

	$sql  = " SELECT a.article_title,aa.category_id ";
	$sql .= " FROM (" . $table_prefix . "articles a ";
	$sql .= " INNER JOIN " . $table_prefix . "articles_assigned aa ON a.article_id=aa.article_id) ";
	$sql .= " WHERE a.article_id=" . $db->tosql($r->get_value("article_id"), INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$article_title = $db->f("article_title");
		$articles_type = "";
		$category_id = $db->f("category_id");
		$sql  = " SELECT ac.category_name, ac.parent_category_id, ac.category_path ";
		$sql .= " FROM " . $table_prefix . "articles_categories ac ";
		$sql .= " WHERE ac.category_id=" . $db->tosql($category_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$category_path = $db->f("category_path");
			$parent_category_id = $db->f("parent_category_id");
			if ($parent_category_id == 0) {
				$articles_type = $db->f("category_name");
			} else {
				$categories_ids = explode(",", $category_path);
				$top_id = $categories_ids[1];
				$sql  = " SELECT category_name ";
				$sql .= " FROM " . $table_prefix . "articles_categories ";
				$sql .= " WHERE category_id=" . $db->tosql($top_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$articles_type = $db->f("category_name");
				}
			}
		}
		$t->set_var("article_title", htmlspecialchars($article_title));	
		$t->set_var("articles_type", htmlspecialchars($articles_type));	
	}


	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("date_added_format", join("", $datetime_edit_format));
	$t->pparse("main");

?>