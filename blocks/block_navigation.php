<?php

/**
 * Method, which is invoked, when it's needed to show menu
 *
 * @param string $block_name
 * @param integer $menu_id
 */
function navigation_menu($block_name, $menu_id) 
{
	global $t, $db, $table_prefix, $language_code;
	global $category_id;
	global $page_settings;
	global $selected_item_id;
	global $selected_item;
	global $record;
	global $marked_item_ids;
	global $first_menu_ids;
	global $last_menu_ids;
	global $navigation_max_depth_level;

	$first_menu_ids = array();
	$last_menu_ids = array();
	$show_menu = 0;
	$menu_title = "";
	$navigation_max_depth_level = 1;
	
	$sql = "SELECT * FROM " . $table_prefix . "menus WHERE menu_id = " . $db->tosql($menu_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$show_menu = $db->f("show_title");
		$menu_title = get_translation($db->f("menu_title"));
	}

	// If no record, but menu exists, show two levels by default
	$visible_depth_level = get_setting_value($page_settings, "navigation_visible_depth_level_" . $menu_id, 2);

	if ($block_name) {
		$t->set_file("block_body", "block_navigation.html");
		$t->set_var("item_block", "");
	}
	
	//$script_name = get_var("SCRIPT_NAME");

	// Get curren request uri to check what menu item selected
	// For example taken http://anyhost.com/anydir/anysubdir/sensiblename?alotofunsensibleparameters
	// but we get only sensiblename
	$string = trim(get_var("REQUEST_URI", "/"));

	// Read from session parameters previously saved real requestr uri
	// Real request uri is a script name and parameters as a line, not a specified in browser line 
	// (rewrited request uri)
	//set_session("navigation_saved_menu_real_url_".$menu_id, "");
	$saved_real_request_uri = get_session("navigation_saved_menu_real_url_".$menu_id);

	// Get current request uri
	$current_real_request_uri = get_request_uri();
	// Parse request uris to array

	$parsed_saved_uri = parse_real_request_uri($saved_real_request_uri);
	$parsed_current_uri = parse_real_request_uri($current_real_request_uri);

	// Menu items
	$items = menu2tree($menu_id, 1);

	// If selected 0, then depth level set to max level (show all hierarchy). 
	// navigation_max_depth_level defines in menu2tree function
	if ($visible_depth_level == 0) {
		$visible_depth_level = $navigation_max_depth_level + 1;
	}

	$selected_item = get_current_menu_item($items, $string);

	// Compare real uris. If they are same get menu_url (to find selected item) from the session.
	$ruc = new RealURLSComparator($parsed_saved_uri, $parsed_current_uri);
	$ruc->compare();

	// Not found selected item, but determine, urls is the same as before 
	// (user click for example on currency changing)
	if ($selected_item == "" && ($ruc->isResembled() || $ruc->isSame())) {
		$selected_item_id = get_session("navigation_saved_menu_item_id_".$menu_id);

		$string = get_session("navigation_saved_menu_url_".$menu_id);
		$real_request_uri_to_save = $saved_real_request_uri;
		if (isset($items[$selected_item_id])) {
			$selected_item = $items[$selected_item_id];
		}
	} else {
		// Else define values
		$real_request_uri_to_save = $current_real_request_uri;
		if (isset($selected_item["data"]) && isset($selected_item["data"]["id"])) {
			$selected_item_id = $selected_item["data"]["id"];
		}
	}
	
	
	$marked_item_ids = array();

	if ($selected_item == "") {
		//$items = menu2tree($menu_id, 1);
		
		$selected_item_id = 0;
		$parent_selected_id = 0;
	} else {
		//$items = menu2tree($menu_id, 1, $item);
		//$selected_item_id = $selected_item["id"];
		if (is_array($selected_item) && isset($selected_item["data"])) {
			$parent_selected_id = $selected_item["data"]["parent_id"];
			$path = $selected_item["data"]["path"];
		} else {
			$parent_selected_id = 0;
			$path = "";
		}

		$decimals = array();
		$decimals = explode(",", $path);
		
		foreach ($decimals as $element) {
			if ($element != "") {
				$marked_item_ids[intval($element)] = intval($element);
			}
		}
		
		// Add to marked items array all hierarchy of selected item
		$marked_item_ids[intval($selected_item_id)] = intval($selected_item_id);
	}

	build_menu($items, 0, --$visible_depth_level);
	
	if ($block_name) {
		if ($show_menu) {
			$t->set_var("menu_title", $menu_title);
			$t->parse("menu_with_title_block", false);
			$t->set_var("menu_without_title_block", "");
		} else {
			$t->parse("menu_without_title_block", false);
			$t->set_var("menu_with_title_block", "");
		}
		$t->parse("block_body", false);
		$t->parse($block_name, true);
	}

	if ($string != "") {
		set_session("navigation_saved_menu_item_id_".$menu_id, $selected_item_id);
		set_session("navigation_saved_menu_real_url_".$menu_id, $real_request_uri_to_save);
		set_session("navigation_saved_menu_url_".$menu_id, $string);
	} else {
		set_session("navigation_saved_menu_item_id_".$menu_id, "");
//		set_session("navigation_saved_script_name_".$menu_id, $script_name);
	}
}

/**
 * Parse real request uri (not rewrited url). Returned structure
 * array(
 * 	["path"] => <path>,
 * 	["query"] => <params as string>,
 * 	["params"] => array([<param_name>] => <param_value>, ...)
 * )
 *
 * @param string $real_request_uri
 * @return array
 */
function parse_real_request_uri($real_request_uri) {
	global $settings;

	if (!preg_match("/^http/i", $real_request_uri)) {
		$host_name = get_var("HTTP_HOST");
		if (preg_match("/^\\//", $real_request_uri)) {
			$real_request_uri = "http://".$host_name.$real_request_uri;
		} else {
			$real_request_uri = "http://".$host_name."/".$real_request_uri;
		}
	}

	$prev_parsed = parse_url($real_request_uri);
	if (is_array($prev_parsed)) {
		
		if (isset($prev_parsed["query"])) {
			$query = $prev_parsed["query"];
			$params_str = explode("&", $query);

			if (is_array($params_str)) {
				foreach ($params_str as $line) {
					$exploded = explode("=", $line);
					if (isset($exploded[0])) {
						if (isset($exploded[1])) {
							$prev_parsed["params"][$exploded[0]] =  $exploded[1];
						} else {
							$prev_parsed["params"][$exploded[0]] = "";
						}
					}
				}
			}
		}		
		// Change path to handle next problem
		// If site url consists not only from host and protocol, but also containes path
		// http://localhost/c2s for example
		if (isset($prev_parsed["path"])) {
			$site_url_value = get_setting_value($settings, "site_url");
			$parsed_site_url = parse_url($site_url_value);
			
			if (isset($parsed_site_url["path"])) {
				$prev_parsed["path"] = ltrim($prev_parsed["path"], $parsed_site_url["path"]);
				$prev_parsed["path"] = trim($prev_parsed["path"], "/");
			}
		}
	}
	return $prev_parsed;
}

/**
 * Create string from $_GET elements
 *
 * @return string
 */
function GETParams2string()  {
	$get_vars = isset($_GET) ? $_GET : $HTTP_GET_VARS;
	$string = "";
	if (is_array($get_vars)) {
		foreach ($get_vars as $param_name => $param_value) {
			$string .= $param_name."=".$param_value."&";
		}
		$string = rtrim($string, "&");
	}
	return $string;
}


/**
 * Method return selected menu item info, search it by request uri
 *
 * @param integer $menu_id
 * @param string $string it's a part of the request uri
 * @return array
 */
function get_current_menu_item(&$items, $current_url) 
{
	global $db, $table_prefix;
	
	$selected_item = array();
	
	$parsed_current_url = parse_real_request_uri($current_url);

	$selected_item_id = "";
	$same_params_number = -1;
	
	if ($current_url != "" && is_array($items)) {
		foreach ($items as $item_id => $item) {
			if (isset($item["data"])) {
				$item_url = $item["data"]["url"];
				$parsed_item_url = parse_real_request_uri($item_url);
				
//				$comparing_result = compare_real_request_uri($parsed_item_url, $parsed_current_url);
				
				$ruc = new RealURLSComparator($parsed_item_url, $parsed_current_url);
				$ruc->compare();

				// Requests are same
				if ($ruc->isSame()) {
					return $items[$item_id];
				} else if ($ruc->isResembled() && $ruc->getCommonParamsNumber() > $same_params_number) {
					$selected_item_id = $item_id;
					$same_params_number = $ruc->getCommonParamsNumber();
				}

			}
		}
		if ($selected_item_id !== "") {
			$selected_item = $items[$selected_item_id];
		}
	}
	return $selected_item;
}

function is_real_urls_equal($first, $second) {
	
}

/**
 * Build menu according to tree array and selected item in this array. 
 *
 * @param array $items
 * @param integer $current_item_id
 * @see menu2tree function returns
 */
function build_menu(&$items, $current_item_id, $depth_level = 1) {
	global $t;
	global $settings;
	global $selected_item;
	global $marked_item_ids;
	global $first_menu_ids;
	global $last_menu_ids;

	if (is_array($items)) {
		if ($current_item_id == 0) {
			if (isset($items[$current_item_id]["subs"])) {
				foreach ($items[$current_item_id]["subs"] as $subitem_id) {
					build_menu($items, $subitem_id, $depth_level);
				}
			}
		}
		else {
			$selected_item_map = "";
			if (is_array($selected_item) && isset($selected_item["data"])) {
				$selected_item_map = $selected_item["data"];
			}
			if (is_include($items[$current_item_id]["data"], $selected_item_map, $depth_level)) {
				$level = $items[$current_item_id]["data"]["level"] + 1;
				$title = $items[$current_item_id]["data"]["title"];
				$url = $items[$current_item_id]["data"]["url"];
				$target = $items[$current_item_id]["data"]["target"];
				$image = $items[$current_item_id]["data"]["image"];
				$active_image = $items[$current_item_id]["data"]["active_image"];
				$parent_id = $items[$current_item_id]["data"]["parent_id"];

				if (!preg_match("/^http\:\/\//", $url) && !preg_match("/^https\:\/\//", $url)) {
					$url = $settings["site_url"] . $url;
				}
	
				$t->set_var("title", $title);
				$t->set_var("url", $url);
				if ($target != "") {
					$t->set_var("target", 'target="'.$target.'"');
				} else {
					$t->set_var("target", "");
				}
				$t->set_var("level", $level);
				$t->set_var("margin_left", get_indent($level));
	
				$img_tag = get_img_tag($items[$current_item_id]["data"]);
				
				// Set css styles according to levels
				if (is_array($selected_item) && 
					isset($selected_item["data"]["id"]) && 
					$selected_item["data"]["id"] == $current_item_id) 
				{
					//$t->set_var("is_active", "-a");
					$td_id = "id=\"" . "active" . $level . "\"";
					$t->set_var("td_id", $td_id);
					$t->set_var("is_active", " a");
				} elseif (in_array($current_item_id, $marked_item_ids)) {
					$td_id = "id=\"" . "parent" . $level . "\"";
					$t->set_var("td_id", $td_id);
					$t->set_var("is_active", "");
				}
				else {
					$t->set_var("is_active", "");
					$t->set_var("td_id", "");
				}

				$table_class = "";

				if ($level > 1) {
//					if (!$first_menu_ids[$parent_id]) {
					if (intval($current_item_id) == intval($first_menu_ids[$parent_id])) {
						$table_class = "first" .$level;
					}

					if ($current_item_id == $last_menu_ids[$parent_id]) {
//					if (!$last_menu_ids[$parent_id]) {
						if ($table_class != "") {
							$table_class .= " last" . $level;
						} else {
							$table_class = "last" . $level;
						}
					}
				}

				
				$t->set_var("table_class", $table_class);

				if ($img_tag != "") {
					$t->set_var("img_file", $img_tag);
					$t->parse("prefix_block", false);
				} else {
					$t->set_var("prefix_block", "");
				}

				$t->set_var("title_tag", get_title_tag($items[$current_item_id]["data"]));
				$t->set_var("item_block_active", "");
				$t->parse("item_block", true);

				if ( is_array($items[$current_item_id]["subs"]) && !empty($items[$current_item_id]["subs"]) ) {
					// 
					reset($items[$current_item_id]["subs"]);
					$first_menu_ids[$current_item_id] = current($items[$current_item_id]["subs"]);
					end($items[$current_item_id]["subs"]);
					$last_menu_ids[$current_item_id] = current($items[$current_item_id]["subs"]);
					foreach ($items[$current_item_id]["subs"] as $subitem_id) {
						build_menu($items, $subitem_id, $depth_level);
					}
				} else {
					$first_menu_ids[$current_item_id] = 0;
					$last_menu_ids[$current_item_id] = 0;
				}
			}
		}
	}
}

/**
 * Return indent vlue. For main menu it's empty
 *
 * @param integer $level
 * @return string
 */
function get_indent($level) {
	return intval($level*10);
}

/**
 * Check if item marked. Now if item is selected or ia a parent of selected
 *
 */
function is_item_marked($item_id) {
	global $marked_item_ids;
	if (is_array($marked_item_ids) && isset($marked_item_ids[$item_id])) {
		return true;
	}
	
	return false;
}
/**
 * Return image tag
 *
 * @param integer $level item level in the items hierarchy
 * @return string
 */
function get_img_tag($item) {
	global $t;
	
	$result = "";
	if (is_array($item)) {
		$id = $item["id"];
		$is_item_marked = is_item_marked($id);
		if (isset($item["prefix_active"]) && $is_item_marked) {
			$result = $item["prefix_active"];
		}
		else if (isset($item["prefix"])) {
			$result = $item["prefix"];
		}
	}

	return $result;
}

/**
 * Return title string for template
 *
 * @param array $item
 * @return string
 */
function get_title_tag($item) {
	global $selected_item_id;
	
	$id = $item["id"];
	$level = $item["level"];
	$image = $item["image"];
	$active_image = $item["active_image"];
	$title = $item["title"];
	$is_item_marked = is_item_marked($id);
	if ($image != "") {
		if (!$is_item_marked || ($is_item_marked && $active_image == "")) {
			return '<img src="' . $image . '"border="0" alt="' . $title . '">';
		}
		else {
			return '<img src="' . $active_image . '"border="0" alt="' . $title . '">';
		}
		
	} else {
		return $title;
	}
}


/**
 * Create structure with information about menu tree.
 * STRUCTURE:
 * array(
 * 	[0] => array(
 * 		"subs" => array(<item_id1>, <item_id2>, ...),
 * 		"data" = > array()
 * 	),
 * [<item_id1>] => array(
 * 		"subs" => array(<item_id11>, <item_id12>, ...),
 * 		"data" => array("id" => "<item_id1>", ...)
 * ),
 * ...
 * )
 *
 * @param integer $side_menu_id
 * @return array
 */
function menu2tree($side_menu_id, $depth_level = 1, $selected_menu_item = '') 
{
	global $db, $table_prefix, $navigation_max_depth_level;

	// Array with items info
	$items = array();
	$depth_level_str = "/";
	if ($depth_level > 0) {
		for ($i = 0; $i < $depth_level; $i++) {
			$depth_level_str .= "\d,";
		}
	}

	$hidden_elements = array();


	$depth_level_str .= "/";
	$sql = "SELECT * FROM " . $table_prefix . "menus_items WHERE ";
	$sql .= "menu_id = " . $db->tosql($side_menu_id, INTEGER);
	$sql .= " ORDER BY menu_order";
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$non_logged = $db->f("show_non_logged");
			$logged = $db->f("show_logged");
			$id = $db->f("menu_item_id");
			$parent_id = $db->f("parent_menu_item_id");

			// User logged and item marked as show_logged
			if (get_session("session_user_id") == "" && !$non_logged) {
				$hidden_elements[$id] = true;
				// User nonlogged and item marked as show_non_logged
			} elseif (get_session("session_user_id") != "" && !$logged) {
				$hidden_elements[$id] = true;
			}
			// Hide element if its parent is hidden
			if (isset($hidden_elements[$parent_id]) && !$hidden_elements[$parent_id]) {
				$hidden_elements[$id] = true;
			}

			if (!isset($hidden_elements[$id]) || !$hidden_elements[$id]) {
				$item_map = array();
				$item_map["id"] = $db->f("menu_item_id");
				$item_map["title"] = get_translation($db->f("menu_title"));
				$item_map["parent_id"] = $db->f("parent_menu_item_id");
				$item_map["url"] = $db->f("menu_url");
				$item_map["path"] = $db->f("menu_path");
				$item_map["level"] = strlen(preg_replace("/\d/", "", $item_map["path"]));
				$item_map["image"] = $db->f("menu_image");
				$item_map["active_image"] = $db->f("menu_image_active");
				$item_map["show_non_logged"] = $db->f("show_non_logged");
				$item_map["show_logged"] = $db->f("show_logged");
				$item_map["prefix"] = $db->f("menu_prefix");
				$item_map["prefix_active"] = $db->f("menu_prefix_active");
				$item_map["target"] = $db->f("menu_target");

				$items[$item_map["parent_id"]]['subs'][] = $item_map["id"];

				$items[$item_map["id"]]['data'] = $item_map;

				if (!isset($items[$item_map["id"]]['subs'])) {
					$items[$item_map["id"]]['subs'] = array();
				}
				
				if ($navigation_max_depth_level < $item_map["level"]) {
					$navigation_max_depth_level = $item_map["level"];
				}
			}
		} while ($db->next_record());
	}
	
	return $items;
}

/**
 * Function return true if item_map has too be included in showing
 * hierarchy, false otherwise
 *
 * @param array $item_map menu item map array
 * @param array $selected_item selected menu item map array
 * @param integer $depth_level
 * @return boolean
 */
function is_include($item_map, $selected_item, $depth_level) 
{
	$result = false;
	// Show all items with specified depth level
	if ($item_map["level"] <= $depth_level) {
		$result = true;
	}
	// User logged and item marked as show_logged
	if (get_session("session_user_id") != "" && !$item_map["show_logged"]) {
		return false;
	// User nonlogged and item marked as show_non_logged
	} else if (get_session("session_user_id") == "" && !$item_map["show_non_logged"]) {
		return false;
	}
	// If user selected menu item

	if ($selected_item != '') {
		// get array with items above in the hierarchy
		$path = $selected_item["path"];
		$path = str_replace(" ", "", $path);
		$path = rtrim($path, ",");
		$path_arr = explode(",", $path);

		$path2compare = "";
		$level = $item_map["level"];
		// Create string to compare with path of $item_map
		//if ($level > $depth_level) {
			for($i = 0; $i < $level; $i++) {//item_map["level"]
				if (isset($path_arr[$i]) && $path_arr[$i] != "") {
					$path2compare .= $path_arr[$i].",";
				}
			}
		//}
		$id = $selected_item["id"];

		if ($item_map["path"] == $selected_item["path"] || 
			$item_map["path"] == $path2compare || 
			$item_map["path"] == $path2compare.$id.",") 
		{
			$result = true;
		}
	}
	
	return $result;
}

class RealURLSComparator 
{
	var $firstURLMap;
	var $secondURLMap;
	
	var $isSame;
	var $commonParamsNum;
	
	function RealURLSComparator($first, $second) {
		$this->firstURLMap = $first;
		$this->secondURLMap = $second;
		$this->isSame = false;
		$this->commonParamsNum = 0;
		$this->isDiffer = true;
		$this->isResembled = false;
	}
	
	function compare() {
		if (isset($this->firstURLMap) && isset($this->secondURLMap)) {
			$first = $this->firstURLMap;
			$second = $this->secondURLMap;

			if (is_array($first) && is_array($second)) {
				// Comparte path
				// If path of one url is empty, urls are different
				if (isset($first["path"]) 
					&& isset($second["path"]) 
					&& ($first["path"] !== $second["path"]
					|| $first["path"] == ""
					|| $second["path"] == ""))
				{
					$this->isSame = false;
					$this->isResembled = false;
					$this->isDiffer = true;
					return;
				} else {
					if (!isset($first["params"]) && !isset($second["params"])) {
						$this->isSame = true;
						$this->isResembled = false;
						$this->isDiffer = false;
						return;
					} else {
						$this->isSame = false;
						$this->isResembled = true;
						$this->isDiffer = false;
					}
				}
		
				if (isset($first["params"]) && isset($second["params"])) {
					foreach ($first["params"] as $param_name => $param_value) {
						if (isset($second["params"][$param_name])) { 
							if ($first["params"][$param_name] !== $second["params"][$param_name]) {
								$this->isDiffer = true;
								$this->isResembled = false;
								$this->isSame = false;
								return;
							} else {
								$this->commonParamsNum++;
							}
						}
					}
		
					if (count($first["params"]) == count($second["params"])) {
						$this->isSame = true;
						$this->isResembled = false;
						$this->isDiffer = false;
					}
				}
			}
			
		}
	}
	
	function getCommonParamsNumber() {
		if (!isset($this)) {
			return false;
		}
		return $this->commonParamsNum;
	}
	
	function isResembled() {
		if (!isset($this)) {
			return false;
		}
		return $this->isResembled;
	}
	
	
	function isDiffer() {
		if (!isset($this)) {
			return false;
		}
		return $this->isDiffer;
	}
	
	function isSame() {
		if (!isset($this)) {
			return false;
		}
		return $this->isSame;
	}
}

?>