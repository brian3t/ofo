<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_manual_edit.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/friendly_functions.php");
	include_once ($root_folder_path . "messages/".$language_code."/manuals_messages.php");

	include_once("./admin_common.php");

	check_admin_security("manual");
	
	$manual_id = get_param("manual_id");
	$category_id = get_param("category_id");
	$saved_category_id = get_param("saved_category_id");
	$operation = get_param("operation");
	$order = "";
	
	if ($category_id == "" && $saved_category_id != "") {
		$category_id = $saved_category_id;
	} elseif ($category_id != "") {
		$saved_category_id = $category_id;
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_manual_edit.html");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", TYPE_MSG, CONFIRM_DELETE_MSG));
	$t->set_var("admin_upload_href", "admin_upload.php");
	$t->set_var("admin_select_href", "admin_select.php");
	$t->set_var("admin_manual_href", "admin_manual.php");
	$t->set_var("admin_manual_edit_href", "admin_manual_edit.php");
	$full_image_url = get_setting_value($settings, "full_image_url", 0);
	$site_url_path = get_setting_value($settings, "site_url", "");
	if ($full_image_url){
		$t->set_var("site_url", $site_url_path);					
	} else {
		$t->set_var("site_url", "");					
	}
	
	$html_editor = get_setting_value($settings, "html_editor", 1);
	$t->set_var("html_editor", $html_editor);

	$r = new VA_Record($table_prefix . "manuals_list");
	$r->return_page = "admin_manual.php";
	
	$r->set_event(BEFORE_INSERT, "set_db_values_before_changes");
	$r->set_event(BEFORE_UPDATE, "set_db_values_before_changes");
	$r->set_event(AFTER_DELETE, "actions_before_delete_manual");
	$r->set_event(BEFORE_SHOW, "set_values_before_show");

	$r->add_where("manual_id", INTEGER);
	// Get orders of categories
	$orders = array();

	$sql = "SELECT * FROM ".$table_prefix."manuals_list ";
	$sql .= " ORDER BY category_id, manual_order";

	$db->query($sql);

	$counters = array();
	
	$ordersList = new OrdersList();
	while ($db->next_record()) {
		$cat_id = $db->f("category_id");
		$m_id = $db->f("manual_id");
		$manual_order = $db->f("manual_order");
		if ($m_id == $manual_id) {
			$is_selected = true;
		} else {
			$is_selected = false;
		}
		$ordersList->addOrder($cat_id, $manual_order, $is_selected);
	}
	
	$r->add_textbox("manual_title", TEXT, MANUAL_TITLE_MSG);
	$r->change_property("manual_title", REQUIRED, true);
	$r->change_property("manual_title", MAX_LENGTH, 255);
	$r->add_textbox("friendly_url", TEXT, FRIENDLY_URL_MSG);
	$r->change_property("friendly_url", USE_SQL_NULL, false);
	$r->change_property("friendly_url", BEFORE_VALIDATE, "validate_friendly_url");
	$r->change_property("friendly_url", REGEXP_MASK, FRIENDLY_URL_REGEXP);
	$r->change_property("friendly_url", REGEXP_ERROR, ALPHANUMERIC_ALLOWED_ERROR);
	$r->add_textbox("short_description", TEXT);
	$r->add_textbox("full_description", TEXT);
	$allowed_values = array(array("0", NOBODY_MSG), array("1", FOR_ALL_USERS_MSG));
	$r->add_radio("allowed_view", INTEGER, $allowed_values, ALLOW_VIEW_MSG);
	// Set allow to view all by default
	if (strlen($operation) == 0) {
		$r->set_value("allowed_view", 1);
	}

	$sql = "SELECT category_id, category_name FROM " . $table_prefix . "manuals_categories ORDER BY category_order";
	$categories = get_db_values($sql, array(array("", SELECT_CATEGORY_MSG)));
	if (count($categories) == 1) {
		$categories = array(array("", NO_CATEGORIES_MSG));
	} else if (is_array($categories)) {
		foreach ($categories as $array) {
			$cat_id = $array[0];
			if ($cat_id != $category_id) {
				$is_selected = true;
				$ordersList->addOrder($cat_id, 0, $is_selected);
			}
		}
	}

	// -----   Set orders list   -----
	$category = get_param("category_id");

	if ($ordersList->isEmpty()) {
		$options_list = array(array(1, 1));
		$order = 1;
	} elseif ($category != "") {
		$category_id = $category;
	}
	$options_list = $ordersList->getListByCategory($category_id);

	$r->add_select("manual_order", INTEGER, $options_list, MANUAL_ORDER_MSG);
	$r->change_property("manual_order", REQUIRED, true);
	if ($manual_id == "") {
		$r->set_value("manual_order", $order);
	}
	// -------------------------------

	$r->change_property("manual_order", REQUIRED, true);
	
	$r->add_select("category_id", INTEGER, $categories, CATEGORY_MSG);
	$r->change_property("category_id", REQUIRED, true);
	
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
	
	$r->add_textbox("date_added", DATETIME, DATE_ADDED_MSG);
	$r->change_property("date_added", USE_IN_UPDATE, false);

	$r->add_textbox("date_modified", DATETIME, MODIFICATION_DATE_MSG);
	$r->change_property("date_modified", USE_IN_UPDATE, true);
	
	//$r->get_form_values();
	//$operation = get_param("operation");
	$manual_id = get_param("manual_id");

	$r->process();
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// Add js to the page
	//var_dump($ordersList->getList());
	$t->set_var("options_js", array2js($ordersList->getList(), "options"));

	$t->set_var("saved_category_id", $saved_category_id);
	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

	function set_db_values_before_changes() {
		global $r;
		global $table_prefix;
		global $db;
		
		$admin_id = get_session("session_admin_id");
		$r->set_value("date_modified", va_time());
		$r->set_value("date_added", va_time());
		
		$admin_id = get_session("session_admin_id");
		$r->set_value("admin_id_modified_by", $admin_id);
		$r->set_value("admin_id_added_by", $admin_id);

		$selected_order = $r->get_value("manual_order");
		$saved_order = get_param("saved_manual_order");
		$category_id = $r->get_value("category_id");
		$saved_category_id = get_param("saved_category_id");
		
		if ($saved_order == 0 || $saved_order != $selected_order) {
			// Increase menu_order of selected item and all other, which have
			// menu_item_order greater than selected and the same parent_id
			if ($saved_order > $selected_order || 
				$saved_order == 0 || 
				$saved_category_id != $category_id) 
			{
				$increase_order_sql = "UPDATE ".$table_prefix."manuals_list ";
				$increase_order_sql .= "SET manual_order = manual_order + 1 ";
				$increase_order_sql .= "WHERE ";
				$increase_order_sql .= "category_id=".$db->tosql($category_id, INTEGER);
				$increase_order_sql .= " AND ";
				$increase_order_sql .= "manual_order >=".$db->tosql($selected_order, INTEGER);
				
				$r->set_value("manual_order", $selected_order);
			} else {
				$increase_order_sql = "UPDATE ".$table_prefix."manuals_list ";
				$increase_order_sql .= "SET manual_order = manual_order + 2 ";
				$increase_order_sql .= "WHERE ";
				$increase_order_sql .= "category_id=".$db->tosql($category_id, INTEGER);
				$increase_order_sql .= " AND ";
				$increase_order_sql .= "manual_order >".$db->tosql($selected_order, INTEGER);
				
				$r->set_value("manual_order", $selected_order + 1);
			}
			$db->query($increase_order_sql);
		}
		
	}

	/**
	 * Before removing manual, remove all articles
	 *
	 */
	function actions_before_delete_manual() {
		global $db, $manual_id, $table_prefix;
		// Remove articles
		$sql = "DELETE FROM " . $table_prefix . "manuals_articles WHERE manual_id = " . $db->tosql($manual_id, INTEGER);
		$db->query($sql);
//		$sql = "DELETE FROM " . $table_prefix . "manuals_assigned WHERE manual_id = " . $db->tosql($manual_id, INTEGER);
//		$db->query($sql);
	}
	
	/**
	 * Function calls before form showing.
	 * Assignes additional parameters
	 *
	 */
	function set_values_before_show() {
		global $r;
		global $t;

		$t->set_var("saved_manual_order", $r->get_value("manual_order"));
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
			$js_string .= "{\n";
			$arr_elem = array();
			$indent_begin = "";
			$indent_end = "";
			for ($i = 0; $i < $level; $i++){
				$indent_begin .= "\t";
			}
			for ($i = 0; $i < $level - 1; $i++){
				$indent_end .= "\t";
			}
			
			foreach ($array as $key => $element) {
				if (is_array($element)) {
					$element_str = array2js($element, $name, false, ++$level);
					$arr_elem[] = $indent_begin . '"' . $key . '": '.$element_str;
				} else {
					$arr_elem[] = $indent_begin . '"' . $key . '": "'.$element.'"';
				}
				
			}
			$js_string .= implode(", \n", $arr_elem);
			$js_string .= "\n" . $indent_end ."}";
			if ($with_initializing) {
				$js_string .= ";\n";
			}
		}		

		return $js_string;
	}
	
	class OrdersList {
		var $orders_list;
		var $counters;
		var $orders;
		
		function OrdersList() {
			$this->orders_list = array();
			$this->counters = array();
			$this->orders = array();
		}
		
		function addCategory($category_id) {

			if (!isset($this->orders_list[$category_id])) {
				$this->orders_list[$category_id] = array();
				$this->counters[$category_id] = 0;
				$this->orders[$category_id] = 0;
			}
		}
		
		function addOrder($category_id, $order = 0, $is_selected = false) {
			$this->addCategory($category_id);
			$this->counters[$category_id] = $this->counters[$category_id] + 1;
			if ($order != 0) {
				$this->orders[$category_id] = $order;
			} else {
				$order = $this->orders[$category_id] + 1;
			}
			$this->orders_list[$category_id][] = array($order, $this->counters[$category_id], $is_selected);
		}
		
		function getList(){
			return $this->orders_list;
		}
		
		function getListByCategory($category_id) {
			if (isset($this->orders_list[$category_id])) {
				return $this->orders_list[$category_id];
			} else {
				return array();
			}
		}
		
		function isEmpty() {
			if (empty($this->orders_list)) {
				return true;
			}
			return false;
		}
	}
?>