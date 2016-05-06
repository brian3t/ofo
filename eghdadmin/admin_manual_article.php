<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_manual_article.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "messages/".$language_code."/manuals_messages.php");

	include_once("./admin_common.php");

	check_admin_security("manual");
	// Proceed go to
	$section_goto = get_param("section_goto");
	$manual_id = get_param("manual_id");
	$article_id = 0;
	$operation = get_param("operation");

	if ($section_goto != "") {
		$sql = "SELECT article_id, parent_article_id FROM ".$table_prefix."manuals_articles ";
		$sql .= "WHERE section_number = ".$db->tosql($section_goto, TEXT);
		$sql .= " AND manual_id = ".$db->tosql($manual_id, INTEGER);
		$db->query($sql);
		
		if ($db->next_record()) {
			$article_id = $db->f("article_id");
			$parent_article_id = $db->f("parent_article_id");
			
			$_POST["article_id"] = $article_id;
			$_POST["parent_article_id"] = $parent_article_id;
		} else {
			header("Location: admin_manual.php?manual_id=" . $manual_id);
			exit;
		}
	} else {
		$article_id = get_param("article_id");
		$parent_article_id = get_param("parent_article_id");
	}

	$current_article_id = $article_id;
	$return_page = "admin_manual.php?manual_id=".$manual_id;

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_manual_article.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_manual_article_href", "admin_manual_article.php");
	$t->set_var("admin_manual_href", "admin_manual.php?manual_id=".$manual_id);
	$t->set_var("current_manual_name", MANUAL_MSG);
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ARTICLE_MSG, CONFIRM_DELETE_MSG));

	$html_editor = get_setting_value($settings, "html_editor", 1);
	$t->set_var("html_editor", $html_editor);
	
	// Get default parent_article_ids
	$manual_articles = get_articles($manual_id);
	
	$tree = array();
	$parent_id = 0;

	if (is_array($manual_articles)) {
		foreach ($manual_articles as $i => $record) {
			$tree[$record["parent_article_id"]][] = $record["article_id"];
			if ($record["article_id"] == $article_id) {
				$parent_id = $record["parent_article_id"];
			}
		}
	}

	$parent_articles = array();
	$alias_articles = array();
	get_parent_articles($manual_id);
	//$parent_articles = 
	// Article record
	$r = new VA_Record($table_prefix . "manuals_articles");
	$r->return_page = $return_page;

	$r->set_event(BEFORE_INSERT, "set_db_values_before_changes");
	$r->set_event(BEFORE_UPDATE, "set_db_values_before_changes");

	$r->set_event(AFTER_INSERT, "build_menus_tree");
	$r->set_event(AFTER_UPDATE, "build_menus_tree");

	$r->add_where("article_id", INTEGER);
	$r->add_textbox("manual_id", INTEGER, ID_MSG);
	//$r->add_select("manual_id", INTEGER, $manuals, "Manual");

	if ($manual_id) {
		$r->set_value("manual_id", $manual_id);
	}
	
	if ($article_id) {
		$r->set_value("article_id", $article_id);
	}

	$r->add_select("parent_article_id", INTEGER, $parent_articles, PARENT_ARTICLE_MSG);
	
	//$r->add_select("alias_article_id", INTEGER, $alias_articles, "Parent Article");
	$r->add_textbox("alias_article_id", INTEGER, ARTICLE_ALIAS_MSG);
	// ---------    Order options    ---------
	$article_order = 0;
	$parent_id = 0;
	$options = array();
	$counters = array();
	$orders = array();

	if (is_array($tree)) {
		foreach ($tree as $parent => $articles) {
			foreach ($articles as $article) {
				$is_selected = false;
				
				if (!isset($options[$parent])) {
					$options[$parent] = array();
					$counters[$parent] = 1;
					$orders[$parent] = 0;
				}
				if (!isset($options[$article])) {
					$options[$article] = array();
					$counters[$article] = 1;
					$orders[$article] = 0;
				}
				$article_record = $manual_articles[$article];
				if ($article == $article_id) {
					$is_selected = true;
				} else {
					$is_selected = false;
				}
				$options[$parent][] = array($article_record["article_order"], $article_record["section_number"], $is_selected);
				$orders[$parent] = $article_record["article_order"];
				$counters[$parent]++;
			}
		}

		foreach ($options as $parent => $article_options) {
			$prefix = "";
			if ($parent != 0 && isset($manual_articles[$parent])) {
				$paren_section = $manual_articles[$parent]["section_number"];
				$prefix = $paren_section . ".";
			}
			
			$is_selected = false;
			if ($article_id == "") {
				$is_selected = true;
				
			}
			$current_order = ++$orders[$parent];
			if ($parent == $parent_id) {
				$article_order = $current_order;
			}
			$options[$parent][] = array($current_order, $prefix . $counters[$parent], $is_selected);
		}
	}
	// Proceed different situations
	$options_list = array();
	
	if (empty($options)) {
		// First article in the manual
		$options_list = array(array(1, 1));
		
	} else {
		// Check if any error occured and have to set options of selected parent not default
		$parent = get_param("parent_article_id");
		if ($parent != "") {
			$parent_id = $parent;
		}
		if (isset($options[$parent_id])) {
			$options_list = $options[$parent_id];
		}
	}

	$r->add_select("article_order", INTEGER, $options_list, ARTICLE_ORDER_MSG);
	$r->change_property("article_order", BEFORE_SHOW, "set_values_before_show");
	// Add article at the end by default
	
	if ($article_id == "") {
		$r->set_value("article_order", $article_order);
	}
	// ----------------------------------------
	
	$r->add_textbox("article_path", TEXT, ARTICLE_PATH_MSG);
	$r->change_property("article_path", USE_SQL_NULL, false);
	$r->change_property("article_path", USE_IN_UPDATE, false);
	
	$r->add_textbox("article_title", TEXT, ARTICLE_TITLE_MSG);
	$r->change_property("article_title", REQUIRED, true);
	
	$r->add_textbox("section_number", TEXT, SECTION_NUMBER_MSG);
	$r->add_textbox("short_description", TEXT, SHORT_DESCRIPTION_MSG);
	//$r->change_property("short_description", REQUIRED, true);
	$r->add_textbox("full_description", TEXT, FULL_DESCRIPTION_MSG);
	
	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	
	// Images
	$r->add_textbox("image_small", TEXT, IMAGE_SMALL_MSG);
	$r->add_textbox("image_small_alt", TEXT, IMAGE_SMALL_ALT_MSG);
	$r->add_textbox("image_large", TEXT, IMAGE_LARGE_MSG);
	$r->add_textbox("image_large_alt", TEXT, IMAGE_LARGE_ALT_MSG);
	
	$r->add_checkbox("allowed_view", INTEGER, ALLOW_VIEW_MSG);
		
	if (strlen($operation) == 0 ) {
		$r->set_value("allowed_view", 1);
	}
	
	$r->add_checkbox("shown_in_contents", INTEGER, ALLOW_VIEW_MSG);

	$r->add_textbox("meta_title", TEXT, META_TITLE_MSG);
	$r->change_property("meta_title", MAX_LENGTH, 255);
	
	$r->add_textbox("meta_keywords", TEXT, ADMIN_META_KEYWORD_MSG);
	$r->change_property("meta_keywords", MAX_LENGTH, 255);
	
	$r->add_textbox("meta_description", TEXT, META_DESCRIPTION_MSG);
	$r->change_property("meta_description", MAX_LENGTH, 255);
	
	$r->add_textbox("admin_id_added_by", INTEGER, ADMIN_ID_ADDED_BY_MSG);
	$r->change_property("admin_id_added_by", USE_IN_INSERT, true);
	$r->change_property("admin_id_added_by", USE_IN_UPDATE, false);
	
	$r->add_textbox("admin_id_modified_by", INTEGER, ADMIN_ID_MODIFIED_BY_MSG);
	$r->change_property("admin_id_modified_by", USE_IN_INSERT, true);
	$r->change_property("admin_id_modified_by", USE_IN_UPDATE, true);

	$r->add_textbox("date_added", DATETIME, MODIFICATION_DATE_MSG);
	$r->change_property("date_added", USE_IN_INSERT, true);
	$r->change_property("date_added", USE_IN_UPDATE, false);
	
	$r->add_textbox("date_modified", DATETIME, MODIFICATION_DATE_MSG);
	$r->change_property("date_modified", USE_IN_INSERT, true);
	$r->change_property("date_modified", USE_IN_UPDATE, true);
	
	$r->set_event(BEFORE_VALIDATE, "before_save_handler");
	
	$r->process();

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("options_js", array2js($options, "options"));
	$t->set_var("rp", htmlspecialchars($return_page));

	$t->pparse("main");
	

	/**
	 * Check if friendly_url is already exists
	 *
	 */
	function before_save_handler() {
		global $db;
		global $r;
		global $table_prefix;
		global $article_id;
		
		$friendly_url = $r->get_value("friendly_url");
		$manual_id = $r->get_value("manual_id");

		if ($friendly_url != "") {
			$sql = "SELECT COUNT(*) AS number FROM ".$table_prefix."manuals_articles ";
			$sql .= "WHERE manual_id = ".$db->tosql($manual_id, INTEGER);
			$sql .= " AND friendly_url = ".$db->tosql($friendly_url, TEXT);
			$sql .= " AND article_id <> ".$db->tosql($article_id, INTEGER);
			
			$db->query($sql);

			if ($db->next_record()) {
				
				if ($db->f("number") > 0) {
					$r->errors .= FRIENDLY_URL_EXISTS_MSG ."<br>";
				}
			}
		}
	}
	
	/**
	 * Execute code before VA_Record makes changes in db.
	 * Set admin ids and dates fields.
	 * Set article_order. Check if order changed. if changed, make changes.
	 *
	 */
	function set_db_values_before_changes() {
		global $r;
		global $table_prefix;
		global $db;

		$parent_article_id = $r->get_value("parent_article_id");
		$article_order = $r->get_value("article_order");
		
		$admin_id = get_session("session_admin_id");
		$parent_article_id = $r->get_value("parent_article_id");
		$r->set_value("admin_id_modified_by", $admin_id);
		$r->set_value("admin_id_added_by", $admin_id);
		$r->set_value("date_modified", va_time());
		$r->set_value("date_added", va_time());

		$selected_order = $r->get_value("article_order");
		$saved_article_order = get_param("saved_article_order");
		$saved_parent_article_id = get_param("saved_parent_article_id");
		
		// Previous we saved parent_article_id and article_order
		// Here if parent_article_id or article_order changed update article_order ot articles 
		// which have thje same parent.
		// There are two variants: first if parent changed, it's new article or order 
		// changed from higher to lower, second variant if order changed from lower to higher
		
		if ($saved_article_order == 0 || 
			$saved_article_order != $selected_order || 
			$saved_parent_article_id != $parent_article_id) 
		{
			// Increase menu_order of selected item and all other, which have
			// menu_item_order greater than selected and the same parent_id
			if ($saved_article_order > $selected_order || 
				$saved_article_order == 0 || 
				$saved_parent_article_id != $parent_article_id) 
			{
				$increase_order_sql = "UPDATE ".$table_prefix."manuals_articles ";
				$increase_order_sql .= "SET article_order = article_order + 1 ";
				$increase_order_sql .= "WHERE parent_article_id = ".$db->tosql($parent_article_id, INTEGER);
				$increase_order_sql .= " AND article_order >=".$db->tosql($selected_order, INTEGER);
				$r->set_value("article_order", $article_order);
			} else {
				$increase_order_sql = "UPDATE ".$table_prefix."manuals_articles ";
				$increase_order_sql .= "SET article_order = article_order + 2 ";
				$increase_order_sql .= "WHERE parent_article_id = ".$db->tosql($parent_article_id, INTEGER);
				$increase_order_sql .= " AND article_order >".$db->tosql($selected_order, INTEGER);
				$r->set_value("article_order", $article_order + 1);
			}
						
			$db->query($increase_order_sql);
		}
	}
	
	/**
	 * Return articles of manual
	 *
	 * @param integer $manual_id
	 * @return array for the form select
	 */
	function get_articles($manual_id) {
		global $db, $table_prefix;
/*		
		$r = new VA_Record($table_prefix . "manuals_articles");
		$r->set_event(BEFORE_INSERT, "set_db_values_before_changes");
		$r->set_event(BEFORE_UPDATE, "set_db_values_before_changes");
		$r->add_where("manual_id", INTEGER);
		$r->set_value("manual_id", $manual_id);
		$r->add_textbox("article_id", INTEGER, ID_MSG);
		$r->add_textbox("parent_article_id", INTEGER, PARENT_ARTICLE_MSG);

		$r->add_textbox("article_path", TEXT, ARTICLE_PATH_MSG);
		$r->change_property("article_path", USE_IN_ORDER, ORDER_ASC);
		
		$r->add_textbox("article_order", INTEGER, ARTICLE_ORDER_MSG);
		$r->change_property("article_order", USE_IN_ORDER, ORDER_ASC);
		
		$r->add_textbox("article_title", TEXT, ARTICLE_TITLE_MSG);
		$r->add_textbox("article_level", INTEGER, ARTICLE_TITLE_MSG);
		$r->change_property("article_level", USE_IN_INSERT, false);
		$r->change_property("article_level", USE_IN_UPDATE, false);
		$r->change_property("article_level", USE_IN_SELECT, false);
		$r->change_property("article_title", REQUIRED, true);
		
		$r->add_textbox("section_number", TEXT, SECTION_NUMBER_MSG);
		
		// Get parent articles
		$grid = new VA_EditGrid($r, "articles");
	
		$articles_num = $grid->get_db_values();
//*/
		$sql = "SELECT manual_id, article_id, parent_article_id, article_path, ";
		$sql .= "article_order, article_title, section_number ";
		$sql .= "FROM ".$table_prefix."manuals_articles ";
		$sql .= "WHERE manual_id = ".$db->tosql($manual_id, INTEGER);
		$sql .= " ORDER BY article_path ASC, article_order ASC";
		
		$db->query($sql);
		$articles = array();
		
		while ($db->next_record()) {
			$article_id = $db->f("article_id");
			$article_path = $db->f("article_path");
			$articles[$article_id] = $db->Record;
			$articles[$article_id]["article_level"] = strlen(preg_replace("/\d/", "", $article_path));
		}
/*
		$articles = array();
		
		if ($articles_num > 0) {
			for($i = 1; $i <= $articles_num; $i++) {
				$grid->set_record($i);
				$article_id = $grid->record->get_value("article_id");
				$article_path = $grid->record->get_value("article_path");
				
				$level = strlen(preg_replace("/\d/", "", $article_path));
				$grid->record->set_value("article_level", $level);
				$articles_objs[$article_id] = $grid->record;
			}
		}
//*/
		return $articles;
	}
	/**
	 * Return articles for alias select
	 *
	 */
	function get_alias_articles() {
		global $manual_articles;
		global $tree;
		global $alias_articles;
		$alias_articles[] = array(0, SELECT_ALIAS_ARTICLE_MSG);
		
		if (is_array($manual_articles)) {
			foreach ($manual_articles as $record) {
				if (is_object($record)) {
					$alias_articles[] = array($record["article_id"], $record["article_title"]);
				}
			}
		}
	}
	
	/**
	 * Assignes array with possible articles for current article
	 *
	 * @param integer $manual_id
	 * @param integer $article_id
	 */
	function get_parent_articles($manual_id, $article_id = 0) {
		global $manual_articles;
		global $parent_articles;
		global $alias_articles;
		global $tree;
		global $current_article_id;
		if ($article_id == 0) {
			$parent_articles[] = array(0, SELECT_PARENT_ARTICLE_MSG);
			$alias_articles[] = array(0, SELECT_ALIAS_ARTICLE_MSG);
		}

		if (isset($tree[$article_id]) && is_array($tree[$article_id])) {
			foreach ($tree[$article_id] as $id) {
				if (isset($manual_articles[$id])) {
					$record = $manual_articles[$id];
					if (is_array($record)) {
						$regular_exp = "/";
						$regular_exp .= "^". $current_article_id. "\,";
						$regular_exp .= "|";
						$regular_exp .= "\,". $current_article_id. "\,";
						$regular_exp .= "/";

						$level = $record["article_level"];
						$text =  get_indent($level) . $record["article_title"];
						
						if ($current_article_id == 0 || (
							preg_match($regular_exp, $record["article_path"]) == 0 &&
							$record["article_id"] != $current_article_id))
						{
							$parent_articles[] = array($record["article_id"], $text);
						}
						$text =  get_indent($level) . $record["article_title"];
						$alias_articles[] = array($record["article_id"], $text);
					}
				}
				get_parent_articles($manual_id, $id);
			}
		}
		
	}
	/**
	 * Return indent according to article level in hierarchy.
	 *
	 * @param integer $level
	 * @return string
	 */
	function get_indent($level = 0) {
		$res = "";
		for($i = 0; $i < $level; $i++) {
			$res .= "--";
		}
		return $res;
	}
	
	/**
	 * Get menu items data from database.
	 *
	 */
	function build_menus_tree() {
		change_article_path();
		change_section_numbers();
	}
	
	/**
	 * Change articles path of manual, article belongs to.
	 *
	 */
	function change_article_path() {
		global $db, $table_prefix, $manual_id;
	
		// update menu links for new structure
		$items = array();
		$section_counters = array();
		$section_counters[0] = 0;
		$section_numbers = array();
		
		$sql  = " SELECT article_id, parent_article_id, section_number FROM " . $table_prefix . "manuals_articles ";
		$sql .= " WHERE manual_id=" . $db->tosql($manual_id, INTEGER);
		$sql .= " ORDER BY article_path, article_order";
		$db->query($sql);
		while ($db->next_record()) {
			$article_id = $db->f("article_id");
			$parent_article_id = $db->f("parent_article_id");
			$items[$article_id] = $parent_article_id;
			
			$section_counters[$article_id] = 0;
		}
		
		foreach ($items as $article_id => $parent_article_id) {
			if (!isset($section_counters[$parent_article_id])) {
				$section_counters[$parent_article_id] = 0;
			}
			if (!isset($section_number_in_level[$parent_article_id])) {
				$section_number_in_level[$parent_article_id] = 0;
			}
			$section_number_in_level[$article_id] = ++$section_counters[$parent_article_id];
		}

		foreach ($items as $article_id => $parent_article_id) {
			$parent_id = $parent_article_id;
			if (!$parent_article_id || $parent_article_id == $article_id) {
				$parent_article_id = 0;
			}

			$article_path = "";
			$section_number_str = $section_number_in_level[$article_id];
			$current_parent_article_id = $parent_article_id;
			
			while ($current_parent_article_id) {
				$article_path = $current_parent_article_id.",".$article_path;
				$section_number_str = $section_number_in_level[$current_parent_article_id].".".$section_number_str;
				$parent_article_id = isset($items[$current_parent_article_id]) ? $items[$current_parent_article_id] : 0;
				if ($parent_article_id == $current_parent_article_id) {
					$current_parent_article_id = 0;
				} else {
					$current_parent_article_id = $parent_article_id;
				}
			}

			$sql  = " UPDATE " . $table_prefix . "manuals_articles SET ";
			$sql .= " article_path=" . $db->tosql($article_path, TEXT, true, false);
			$sql .= " WHERE article_id=" . $db->tosql($article_id, INTEGER);
			
			$db->query($sql); 
		}	
	}
	/**
	 * Function change section number each article in selected manual after user saved article.
	 *
	 */
	function change_section_numbers() {
		global $db, $table_prefix, $manual_id;
	
		// update menu links for new structure
		$items = array();
		$section_counters = array();
		$section_counters[0] = 0;
		$section_numbers = array();
		
		$sql  = " SELECT article_id, parent_article_id, section_number FROM " . $table_prefix . "manuals_articles ";
		$sql .= " WHERE manual_id=" . $db->tosql($manual_id, INTEGER);
		$sql .= " ORDER BY article_path, article_order";
		$db->query($sql);
		while ($db->next_record()) {
			$article_id = $db->f("article_id");
			$parent_article_id = $db->f("parent_article_id");
			$items[$article_id] = $parent_article_id;
			
			$section_counters[$article_id] = 0;
		}
		
		foreach ($items as $article_id => $parent_article_id) {
			$parent_id = $parent_article_id;
			if (!$parent_article_id || $parent_article_id == $article_id) {
				$parent_article_id = 0;
			}

			if ($parent_article_id == 0) {
				$section_number_str = ++$section_counters[$parent_article_id];
			} else {
				if (isset($section_numbers[$parent_article_id])) {
					$section_number_str = $section_numbers[$parent_article_id]."." . ++$section_counters[$parent_article_id];
				}
			}
			$section_numbers[$article_id] = $section_number_str;

			$sql  = " UPDATE " . $table_prefix . "manuals_articles SET ";
			$sql .= "section_number=".$db->tosql($section_number_str, TEXT);
			$sql .= " WHERE article_id=" . $db->tosql($article_id, INTEGER);
			$db->query($sql); 
		}	
	}
	
	/**
	 * Function calls before form showing.
	 * Assignes additional parameters
	 *
	 */
	function set_values_before_show() {
		global $r;
		global $t;
		global $db;
		global $table_prefix;

		$t->set_var("saved_article_order", $r->get_value("article_order"));
		$t->set_var("saved_parent_article_id", $r->get_value("parent_article_id"));
		$alias_article_id = $r->get_value("alias_article_id");
		$manual_id = $r->get_value("manual_id", INTEGER);
		if ($alias_article_id != "") {
			// get alias article title and its manual
			$sql = "SELECT a.article_title, a.section_number, m.manual_title FROM " . $table_prefix . "manuals_articles a";
			$sql .= " LEFT JOIN " . $table_prefix."manuals_list m ON a.manual_id = m.manual_id";
			$sql .= " WHERE a.article_id = " . $db->tosql($alias_article_id, INTEGER);
			//$sql .= " AND m.manual_id = " . $db->tosql($manual_id, INTEGER);
			
			$db->query($sql);
			
			if ($db->next_record()) {
				$alias_article_title = $db->f("section_number");
				$alias_article_title .= "&nbsp;" . $db->f("article_title");
				$alias_article_title .= "(" . $db->f("manual_title") . ")";
				$t->set_var("alias_article_title", $alias_article_title);
			}
		} else {
			$t->set_var("alias_article_title", "");
		}
	}
	
	/**
	 * Return string with javascript initialized array
	 *
	 * @param array $array php array
	 * @param string $name js array string
	 * @param boolean $with_initializing defines add var <variable name> = Object and ending ';'
	 * @param integer $level 
	 * @return string
	 */
	function array2js($array, $name = "items", $with_initializing = true, $level = 1) {
		if (is_array($array)) {
			$name = str_replace(" ", "_", $name);
			$js_string = "";
			if ($with_initializing) {
				$js_string .= "var " . $name . " = Object();\n";
				$js_string .= $name . " = ";
			}
			$js_string .= "{";
			$arr_elem = array();
			$indent_begin = "";
			$indent_end = "";

			foreach ($array as $key => $element) {
				if (is_array($element)) {
					$element_str = array2js($element, $name, false, ++$level);
					$arr_elem[] = $indent_begin . '"' . $key . '": '.$element_str;
				} else {
					$arr_elem[] = $indent_begin . '"' . $key . '": "'.$element.'"';
				}
				
			}
			$js_string .= implode(", ", $arr_elem);
			$js_string .= $indent_end ."}";
			if ($with_initializing) {
				$js_string .= ";";
			}
		}		

		return $js_string;
	}
?>