<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_manual.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	define("MANUAL_ORDER_ARTICLE_ID", 1);
	define("MANUAL_ORDER_ARTICLE_TITLE", 2);
	define("MANUAL_ORDER_SECTION", 3);
	define("MANUAL_ORDER_MODIFIED", 4);

	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "messages/".$language_code."/manuals_messages.php");

	include_once("./admin_common.php");


	check_admin_security("manual");
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_manual.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_manual_href", "admin_manual.php");
	$t->set_var("admin_manual_thread_href", "admin_manual_thread.php");
	$t->set_var("admin_manual_article_href", "admin_manual_article.php");
	$t->set_var("admin_manual_settings_href", "admin_manual_settings.php");
	$t->set_var("admin_manual_edit_href", "admin_manual_edit.php");
	$t->set_var("admin_manual_category_href", "admin_manual_category.php");

	$s = new VA_Sorter(get_setting_value($settings, "admin_templates_dir"), "sorter_img.html", "admin_manual.php");
	$s->set_parameters(false, true, true, false);
	$s->set_default_sorting(MANUAL_ORDER_SECTION, "desc");
	$s->set_sorter(NO_MSG, "sorter_id", MANUAL_ORDER_ARTICLE_ID, "article_id");
	$s->set_sorter(ARTICLE_MSG, "sorter_article", MANUAL_ORDER_ARTICLE_TITLE, "article_title");
	$s->set_sorter(ADMIN_SECTION_MSG, "sorter_section_number", MANUAL_ORDER_SECTION, "section_number");
	$s->set_sorter(LAST_UPDATED_MSG, "sorter_modified", MANUAL_ORDER_MODIFIED, "date_modified");
	
	$sort_ord = get_param("sort_ord");
	$sort_dir = get_param("sort_dir");

	if ($sort_ord == "") {
		$sort_ord = MANUAL_ORDER_SECTION;
	}
	
	if ($sort_dir == "") {
		$sort_dir = "asc";
	}

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_manual.php");
	$n->set_parameters(true, true, true);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$manual_id = get_param("manual_id");
	$category_id = get_param("category_id");
	
	$active_manual_id = 0;

	// Show manual categories in the left
	$sql = "SELECT fc.category_id, fc.category_name, fl.manual_id, fl.manual_title ";
	$sql .= " FROM " . $table_prefix . "manuals_categories fc LEFT JOIN " . $table_prefix . "manuals_list fl ";
	$sql .= " ON fc.category_id = fl.category_id ";
	//$sql .= " LEFT JOIN ". $table_prefix . "manuals_assigned fa ON fc.category_id = fa.category_id";
	if ($category_id) {
		$sql .= " WHERE fc.category_id = " . $db->tosql($category_id, INTEGER);
	}
	$sql .= " ORDER BY fc.category_order, fc.category_id, fl.manual_order ";
	$db->query($sql);
	if ($db->next_record()) 
	{
		$current_category = "";
		do {
			if ($db->f("category_name") != $current_category) {
				$t->set_var("list_category_id", $db->f("category_id"));
				$t->set_var("list_category_name", htmlspecialchars($db->f("category_name")));
				$t->parse("list_category", false);
				$current_category = $db->f("category_name");
				if ($db->f("category_id") == $category_id) {
					$t->set_var("category_id", $category_id);
					$t->set_var("current_category", htmlspecialchars($current_category));
					$t->parse("current_category_block", false);
				}
				else {
					$t->set_var("current_category_block", "");
				}
			}
			else {
				$t->set_var("list_category", "");
			}
			if ($db->f("manual_id")) 
			{
				if (!$active_manual_id && !$manual_id) {
					$active_manual_id = $db->f("manual_id");
					$manual_title = $db->f("manual_title");
					$category_id = $db->f("category_id");
					$category_name = $db->f("category_name");
				} elseif (!$active_manual_id && $manual_id){
					$active_manual_id = $manual_id;
				}
				
				if ($active_manual_id == $db->f("manual_id")){
				    $manual_title = $db->f("manual_title");
					$category_id = $db->f("category_id");
					$category_name = $db->f("category_name");
				}
				
				$t->set_var("manual_id", $db->f("manual_id"));
				$t->set_var("manual_title", htmlspecialchars($db->f("manual_title")));
				$t->set_var("category_id", $db->f("category_id"));
				$t->parse("list_manual", false);
			}
			else 
			{
				$t->set_var("list_manual", "");
			}
			$t->parse("list_block", true);
		} while ($db->next_record());
		$t->parse("new_manual_link", false);
		$t->set_var("block_no_categories", "");
	}
	else 
	{
		$t->set_var("new_manual_link", "");
		$t->set_var("block_threads", "");
		$t->set_var("message_list", NO_CATEGORIES_MSG);
		$t->parse("block_message", false);
	}

	if (!$manual_id) {
		$manual_id = $active_manual_id;
	}

	// Set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "manuals_list WHERE manual_id = " . $db->tosql($manual_id, INTEGER));
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;

	// Template variables
	$t->set_var("admin_manual_article_url", "admin_manual_article.php");

	if ($manual_id) {
		/*$sql = "SELECT manual_title FROM ".$table_prefix . "manuals_list WHERE manual_id = " . $db->tosql($manual_id, INTEGER);
		$manual_title = get_db_value($sql);
		$sql = "SELECT category_id FROM ".$table_prefix . "manuals_list WHERE manual_id = " . $db->tosql($manual_id, INTEGER);
		$category_id = get_db_value($sql);
		$sql = "SELECT category_name FROM ".$table_prefix . "manuals_categories WHERE category_id = " . $db->tosql($category_id, INTEGER);
		$category_name = get_db_value($sql);*/

		$db->query("SELECT COUNT(*) FROM " . $table_prefix . "manuals_articles WHERE manual_id = " . $db->tosql($manual_id, INTEGER));
		$db->next_record();
		$total_records = $db->f(0);
		$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
		$pages_number = 5;
		$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
		
		// Proceed ordering by sections, it's needed to handle next situation 
		// (in common text ordering 10.1 goes before 2.2, but section goes 2.2 before 10.1)
		if ($sort_ord == MANUAL_ORDER_SECTION) {
			$sql = "SELECT article_id, article_title, parent_article_id, section_number, article_order, date_modified FROM ".$table_prefix."manuals_articles WHERE manual_id=".$db->tosql($manual_id, INTEGER);
			$sql .= " ORDER BY article_order";
			$db->query($sql);

			$hierarchy = array();
			$articles = array();
			
			if ($db->next_record()) {
				if ($total_records > 0) {
					do {
						$article_id = $db->Record["article_id"];
						$parent_article_id = $db->Record["parent_article_id"];
						$articles[$article_id] = $db->Record;
						$hierarchy[$parent_article_id][] = $article_id;
					} while ($db->next_record());
					
					$articles_list = array();
					build_articles_list(0);
					
					if ($sort_dir == "asc") {
						$article2show = array_slice($articles_list, ($page_number - 1)*$records_per_page, $records_per_page);
					}  else {
						$articles_list = array_reverse($articles_list);
						$article2show = array_slice($articles_list, ($page_number - 1)*$records_per_page, $records_per_page);
					}
					
					foreach ($article2show as $article) {
						$t->set_var("article_id", $article["article_id"]);
						$t->set_var("article_title", $article["article_title"]);
						$t->set_var("indent", "");
						$t->set_var("section_number", $article["section_number"]);
						$t->set_var("date_modified", $article["date_modified"]);
						$t->set_var("manual_id", $manual_id);
						$t->set_var("parent_article_id", $article["parent_article_id"]);
						$t->parse("records", true);
					}
					$t->parse("sorters", false);
				} else {
					$t->set_var("sorters", "");
				}
			}
		
		} else {
			$db->RecordsPerPage = $records_per_page;
			$db->PageNumber = $page_number;
			
			$sql = "SELECT * FROM ".$table_prefix."manuals_articles WHERE manual_id=".$db->tosql($manual_id, TEXT);
			$sql .= $s->order_by;
	
			$db->query($sql);

			if ($db->next_record()) {
				if ($total_records > 0) {
					do {
						$article_id = $db->Record["article_id"];
						$parent_article_id = $db->Record["parent_article_id"];
						$article_path = $db->Record["article_path"];
						$level = strlen(preg_replace("/\d/", "", $article_path));
						$t->set_var("article_id", $article_id);
						$t->set_var("article_title", $db->Record["article_title"]);
						$t->set_var("indent", "");
						$t->set_var("section_number", $db->Record["section_number"]);
						$t->set_var("date_modified", $db->Record["date_modified"]);
						$t->set_var("manual_id", $manual_id);
						$t->set_var("parent_article_id", $parent_article_id);
						$t->parse("records", true);
					} while ($db->next_record());
					$t->parse("sorters", false);
				} else {
					$t->set_var("sorters", "");
				}
			}
		}
		$t->set_var("title_block", $manual_title);
		$t->set_var("current_category", htmlspecialchars($category_name));
		$t->set_var("category_id", $category_id);
		$t->set_var("current_manual", htmlspecialchars($manual_title));
		$t->parse("current_category_block",false);
		$t->parse("current_manual_block",true);
		$t->set_var("manual_id", $manual_id);

		$t->parse("manual_links", false);
	} else {
		$t->set_var("title_block", NO_ACTIVE_MANUAL_MSG);
	}

	$t->set_var("title_left_block", MANUALS_TITLE);
	$t->set_var("title_left_search_block", SEARCH_TITLE);
	// Searching
	$search_string = get_param("search_string");
	$search_manual_id = get_param("manuals_select");
	$t->set_var("search_string", $search_string);
	// Add manuals in the select
	show_manuals_select($search_manual_id);
	
	if ($search_string != "") {
		show_searched_results($search_string, $manual_id);
	}
	// Go to 
	$t->set_var("title_left_goto_block", GO_TO_MSG);
	$t->pparse("main");

	/**
	 * Recursive function. Use global variable to generate articles list proper, according to
	 * the articles hierarchy
	 *
	 * @param integer $parent_artivle_id
	 */
	function build_articles_list($parent_artivle_id = 0) 
	{
		global $hierarchy, $articles, $articles_list;
		
		if (isset($hierarchy[$parent_artivle_id])) {
			foreach ($hierarchy[$parent_artivle_id] as $article_id) {
				$articles_list[] = $articles[$article_id];
				build_articles_list($article_id);
			}
		}
	}
	
	function show_searched_results($search_string, $manual_id) 
	{
		global $t, $db, $table_prefix;
		if ($search_string != "") {
			$sql = "SELECT article_id, parent_article_id, article_title, manual_id, short_description, friendly_url ";
			$sql .= "FROM ".$table_prefix."manuals_articles ";
			$sql .= "WHERE full_description LIKE \"%";
			$sql .= $search_string."%\"";
			if ($manual_id != 0) {
				$sql .= " AND manual_id = ";
				$sql .= $db->tosql($manual_id, INTEGER);
			}
			$db->query($sql);
			
			$counter = 0;
			if ($db->next_record()) {
				do {
					$counter++;
					$manual_id = $db->f("manual_id");
					$article_id = $db->f("article_id");
					$parent_article_id = $db->f("parent_article_id");
					$t->set_var("article_title", htmlspecialchars($db->f("article_title")));
					$article_href = "admin_manual_article.php?";
					$article_href .= "manual_id=".$manual_id;
					$article_href .= "&article_id=".$article_id;
					$article_href .= "&parent_article_id=".$parent_article_id;
					$t->set_var("article_href", $article_href);
					$t->set_var("counter", $counter);
					$t->parse("searched_record", true);
				} while ($db->next_record());
				$t->set_var("no_results", "");
				$t->parse("searched_results_block", false);
			} else {
				$t->set_var("searched_record", "");
				$t->parse("no_results", false);
				$t->parse("searched_results_block", false);
			}
		}		
	}
	
	function show_manuals_select($selected_manual_id) 
	{
		global $t, $db, $table_prefix;
		// Select manuals
		$sql = "SELECT * FROM " . $table_prefix . "manuals_list ";
		$sql .= "ORDER BY manual_title";
		$db->query($sql);
		if ($db->next_record()) {
			do {
				$t->set_var("option_value", $db->f("manual_id"));
				$t->set_var("option_description", $db->f("manual_title"));
				if ($selected_manual_id == $db->f("manual_id")){
					$t->set_var("option_selected", "selected");
				} else {
					$t->set_var("option_selected", "");
				}
				$t->parse("manuals_option", true);
			} while ($db->next_record());
		}
		
		$t->parse("manuals_select", false);
	}

?>