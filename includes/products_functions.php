<?php
//http://localhost/c2s/software.html?sort_ord=3&sort_dir=asc

	class VA_Categories {
		/**
		 * Internal sql function, that builds query for categories search
		 *
		 * @param Array / String $params
		 * @param Constant $access_level: VIEW_CATEGORIES_PERM, VIEW_CATEGORIES_ITEMS_PERM, VIEW_ITEMS_PERM, ADD_ITEMS_PERM
		 * @return String
		 */
		static function _sql($params, $access_level) {
			global $table_prefix, $db, $site_id;
			
			$select = "";
			$where = "";
			$order = "";
			$join = "";
			$brackets = "";
			$use_sites = true;
			$use_acls  = true;
			
			if (is_array($params)) {
				$select = isset($params["select"]) ? $params["select"] : "";
				$where  = isset($params["where"]) ? $params["where"] : "";
				$order  = isset($params["order"]) ? $params["order"] : "";
				$join   = isset($params["join"])  ? $params["join"] : "";
				$brackets = isset($params["brackets"])  ? $params["brackets"] : "";
				if (isset($params["no_sites"])) $use_sites = false;
				if (isset($params["no_acls"]))  $use_acls = false;
			} else {
				$where = $params;
			}
			
			$access_level = (int) $access_level;
			if (!$access_level) $access_level = VIEW_CATEGORIES_PERM;
						
			$user_id         = get_session("session_user_id");
			$user_type_id    = get_session("session_user_type_id");
			$subscription_id = get_session("session_subscription_id");
			$subscription_ids = get_session("session_subscriptions_ids");
			
			$sql = " SELECT ";
			if (strlen($select)) {
				$sql .= $select;
			} else {
				$sql .= " c.category_id ";
			}
			
			$sql .= " FROM ";
			
			if ($use_sites && isset($site_id)) {
				$sql .= " (";
			};
			
			if ($use_acls) {
				if (strlen($user_id)) {
					$sql .= " (";
				};
				if (strlen($subscription_ids)) {
					$sql .= " (";
				}
			}
			
			if (strlen($brackets)) {
				$sql .= $brackets;
			}
			
			$sql .= $table_prefix . "categories c ";
			
			if ($use_sites && isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "categories_sites AS cs ON cs.category_id=c.category_id)";
			}
			if ($use_acls) {
				if (strlen($user_id)) {
					$sql .= " LEFT JOIN " . $table_prefix . "categories_user_types AS ut ON ut.category_id=c.category_id)";
				}
				if (strlen($subscription_ids)) {
					$sql .= " LEFT JOIN " . $table_prefix . "categories_subscriptions AS sb ON sb.category_id=c.category_id)";
				}
			}
			if (strlen($join)) {
				$sql .= $join;
			}
			
			$sql .= " WHERE c.is_showing=1";
						
			if ($use_sites) {
				if (isset($site_id)) {
					$sql .= " AND (c.sites_all=1 OR cs.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
				} else {
					$sql .= " AND c.sites_all=1 ";
				}
			}
			if ($use_acls) {
				if (strlen($user_id) && strlen($subscription_ids)) {				
					$sql .= " AND ( " . format_binary_for_sql("c.access_level", $access_level);					
					$sql .= " OR ("   . format_binary_for_sql("ut.access_level", $access_level) . " AND ut.user_type_id=". $db->tosql($user_type_id, INTEGER, true, false) . ") ";
					$sql .= " OR ("   . format_binary_for_sql("sb.access_level", $access_level) . " AND sb.subscription_id IN (". $db->tosql($subscription_ids, INTEGERS_LIST) . ")) )";
				} elseif (strlen($user_id)) {
					$sql .= " AND (" . format_binary_for_sql("c.access_level", $access_level) . " ";
					$sql .= " OR (" . format_binary_for_sql("ut.access_level", $access_level) . " AND ut.user_type_id=". $db->tosql($user_type_id, INTEGER, true, false) . ") )";
				} else {
					$sql .= " AND " . format_binary_for_sql("c.guest_access_level", $access_level);
				}
			}			
			
			if (strlen($where)) {
				$sql .= " AND " . $where;
			}
			
			return $sql;
		}
		/**
		 * Check if the category with this id is availiable with selected access level
		 *
		 * @param Integer $category_id
		 * @param Constant $access_level: VIEW_CATEGORIES_PERM, VIEW_CATEGORIES_ITEMS_PERM, VIEW_ITEMS_PERM, ADD_ITEMS_PERM
		 * @return Boolean
		 */
		function check_permissions($category_id, $access_level = VIEW_CATEGORIES_PERM) {
			global $db;
			$db->query(VA_Categories::_sql("c.category_id=" . $db->tosql($category_id, INTEGER), $access_level));
			return $db->next_record();
		}
		
		/**
		 * Check if the category with this id exists
		 *
		 * @param Integer $category_id
		 * @return Boolean
		 */
		function check_exists($category_id) {
			global $db;
			$params["where"] = " c.category_id=" . $db->tosql($category_id, INTEGER);
			$params["no_acls"]  = true;
			$db->query(VA_Categories::_sql($params, 0));
			return $db->next_record();
		}
		
		/**
		 * Find all categories availiable by selected access level
		 *
		 * @param String $where: please enter search that will be added to global search, c. - is abbr for the category
		 * @param Constant $access_level: VIEW_CATEGORIES_PERM, VIEW_CATEGORIES_ITEMS_PERM, VIEW_ITEMS_PERM, ADD_ITEMS_PERM
		 * @return Array
		 */
		static function find_all_ids($where = "", $access_level = VIEW_CATEGORIES_PERM) {
			global $db;
			
			$db->query(VA_Categories::_sql($where, $access_level));

			$ids = array();
			while ($db->next_record()) {
				$id = $db->f(0);
				if (!in_array($id, $ids)) {
					$ids[] = $id;
				}
			}
			
			return $ids;
		}
		
		function find_all($key_field = "c.category_id", $fields = array(), $params = "", $access_level = VIEW_CATEGORIES_PERM) {
			global $db;			
			if (is_array($params)) {
				$params_prepared = $params;
				$params_prepared["select"] = implode(",", $fields);
			} else {
				$params_prepared = array();
				$params_prepared["where"] = $params;
			}
			$params_prepared["select"] = "";
			if ($key_field) {
				$params_prepared["select"] .= $key_field . ",";
			}
			if ($fields) {
				$params_prepared["select"] .= implode(",", $fields);
			}
			
			$db->query(VA_Categories::_sql($params_prepared, $access_level));
			
			$results = array();
			if ($key_field) {
				while ($db->next_record()) {
					$key = $db->f(0);
					$result = array();
					foreach ($fields AS $number => $field) {
						$result[$field] = $db->f($number + 1);
					}
					$results[$key] = $result;
				}
			} else {
				while ($db->next_record()) {
					$result = array();
					foreach ($fields AS $number => $field) {
						$result[$field] = $db->f($number);
					}
					$results[] = $result;
				}
			}
			return $results;
		}
	}
	
	class VA_Products {		
		/**
		 * Internal function, that builds queries for products search
		 *
		 * @param String / Array $params: if string - than equals to normal where parameter, 
		 * if array - could be used for compplex requests,
		 * @param String $params["select"] - fields names, separated by comma
		 * @param String $params["where"]
		 * @param String $params["brackets"] - brackets for joins
		 * @param String $params["join"]  - join query part, if some subtables needed
		 * @param String $params["order"] - full order syntax, like "ORDER BY i.item_id", but also could has GROUP part if needed
		 * @param String $params["no_sites"] - dont include sites part in sql
		 * @param String $params["no_acls"] - dont include access levels part
		 * @param Constant $access_level: VIEW_CATEGORIES_ITEMS_PERM, VIEW_ITEMS_PERM
		 * @return String
		 */
		static function _sql($params, $access_level, $is_count = false) {
			global $table_prefix, $db, $site_id, $language_code;
			$select = ""; $where = ""; $distinct = ""; $group_by = ""; $order = "";
			$join = ""; $brackets = "";
			$use_sites = true;
			$use_acls  = true;
			
			if (is_array($params)) {
				$select = isset($params["select"]) ? $params["select"] : "";
				$where  = isset($params["where"]) ? $params["where"] : "";
				$order  = isset($params["order"]) ? $params["order"] : "";
				$group_by = isset($params["group"]) ? $params["group"] : "";
				$distinct = isset($params["distinct"]) ? $params["distinct"] : "";
				$join   = isset($params["join"])  ? $params["join"] : "";
				$brackets = isset($params["brackets"])  ? $params["brackets"] : "";
				if (isset($params["no_sites"])) $use_sites = false;
				if (isset($params["no_acls"]))  $use_acls = false;
			} else {
				$where = $params;
			}
			$access_level = (int) $access_level;
			if (!$access_level) $access_level = VIEW_ITEMS_PERM;
						
			$user_id         = get_session("session_user_id");
			$user_type_id    = get_session("session_user_type_id");
			$subscription_id = get_session("session_subscription_id");
			$subscription_ids = get_session("session_subscription_ids");
			
			
			$sql = " SELECT ";
			if ($is_count) {
				// build COUNT SQL
				if ($distinct) {
					if ($db->DBType == "access") {
						$sql .= " COUNT(*) ";
						$sql .= " FROM (SELECT DISTINCT " . $distinct . " ";
					} else {
						$sql .= " COUNT(DISTINCT " . $distinct . ") ";
					}
				} elseif ($group_by) {
					if ($db->DBType == "access") {
						$sql .= " COUNT(*) ";
						$sql .= " FROM (SELECT " . $group_by . " ";
					} else {
						$sql .= " COUNT(DISTINCT " . $group_by . ") ";
					}
				} else {
					$sql .= " COUNT(*) ";
				}
			} else {
				// build SELECT SQL
				if (strlen($select)) {
					$sql .= $select;
				} else {
					$sql .= " i.item_id ";
				}
			}
			
			$sql .= " FROM ";
			if ($use_sites && isset($site_id)) {
				$sql .= "(";
			}
			if ($use_acls) {
				if (strlen($user_id)) {				
					$sql .= "(";
				}
				if (strlen($subscription_ids)) {
					$sql .= "(";
				}
			}
			if (strlen($brackets)) {
				$sql .= $brackets;
			}
			
			$sql .= " " . $table_prefix . "items i ";
					
			if ($use_sites && isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "items_sites AS s ON s.item_id=i.item_id)";
			}
			if ($use_acls) {
				if (strlen($user_id)) {
					$sql .= " LEFT JOIN " . $table_prefix . "items_user_types AS ut ON ut.item_id=i.item_id)";
				}			
				if (strlen($subscription_ids)) {
					$sql .= " LEFT JOIN " . $table_prefix . "items_subscriptions AS sb ON sb.item_id=i.item_id)";
				}
			}
			if (strlen($join)) {
				$sql .= $join;
			}	
			
			$sql .= " WHERE i.is_showing=1 AND i.is_approved=1 ";
			$sql .= " AND ((i.hide_out_of_stock=1 AND i.stock_level > 0) OR i.hide_out_of_stock=0 OR i.hide_out_of_stock IS NULL)";
			$sql .= " AND (i.language_code IS NULL OR i.language_code='' OR i.language_code=" . $db->tosql($language_code, TEXT) . ")";
			
			if ($use_sites) {
				if (isset($site_id)) {
					$sql .= " AND (i.sites_all=1 OR s.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
				} else {
					$sql .= " AND i.sites_all=1 ";
				}
			}
			
			if ($use_acls) {				
				if (strlen($user_id) && strlen($subscription_ids)) {
					$sql .= " AND (" . format_binary_for_sql("i.access_level", $access_level);
					$sql .= " OR ("  . format_binary_for_sql("ut.access_level", $access_level) . "  AND ut.user_type_id=". $db->tosql($user_type_id, INTEGER, true, false) . " ) ";
					$sql .= " OR ("  . format_binary_for_sql("sb.access_level", $access_level) . " AND sb.subscription_id IN (". $db->tosql($subscription_ids, INTEGERS_LIST) . ")) )";
				} elseif (strlen($user_id)) {
					$sql .= " AND (" . format_binary_for_sql("i.access_level", $access_level);
					$sql .= " OR ("  . format_binary_for_sql("ut.access_level", $access_level) . " AND ut.user_type_id=". $db->tosql($user_type_id, INTEGER, true, false) . ") )";
				} else {
					$sql .= " AND " . format_binary_for_sql("i.guest_access_level", $access_level);
				}
			}	
		
			if (strlen($where)) {
				$sql .= " AND " . $where;
			}
			// add group by
			if ($is_count) {
				// build COUNT SQL
				if ($distinct) {
					if ($is_count) {
						if ($db->DBType == "access") {
							$sql .= " ) ";
						}
					}
				} elseif ($group_by) {
					if ($db->DBType == "access") {
						$sql .= " GROUP BY " . $group_by . ") ";
					}
				}
			} else {
				// build SELECT SQL
				if ($group_by) {
					$sql .= " GROUP BY " . $group_by . " ";
				}
				if (strlen($order)) {
					$sql .= " " . $order;
				}			
			}
			
			return $sql;
		}

		function count($params = "", $access_level = VIEW_CATEGORIES_ITEMS_PERM) {
			global $db;
			$count = 0;
			$sql = VA_Products::_sql($params, $access_level, true);
			$db->query($sql);
			if ($db->next_record()) {
				$count = $db->f(0);
			}
			return $count;
		}

		function data($params = "", $access_level = VIEW_CATEGORIES_ITEMS_PERM, $records_per_page = "", $page_number = "")
		{
			global $db;
			$data = array();
			$sql = VA_Products::_sql($params, $access_level);
			if ($records_per_page && $page_number) {
				$db->RecordsPerPage = $records_per_page;
				$db->PageNumber = $page_number;
			}
			$db->query($sql);
			while ($db->next_record()) {
				$data[] = $db->Record;
			}
			return $data;
		}

		/**
		 * Check if the item with this id exists
		 *
		 * @param Integer $item_id
		 * @return Boolean
		 */
		function check_exists($item_id) {
			global $db;
			$params["where"] = " i.item_id=" . $db->tosql($item_id, INTEGER);
			$params["no_acls"]  = true;
			$db->query(VA_Products::_sql($params, 0));
			return $db->next_record();
		}
		/**
		 * Check if the item with this id is availiable with selected access level
		 *
		 * @param Integer $item_id
		 * @param Constant $access_level: VIEW_CATEGORIES_ITEMS_PERM, VIEW_ITEMS_PERM
		 * @return Boolean
		 */		
		function check_permissions($item_id, $access_level = VIEW_ITEMS_PERM) {
			global $db;
			$db->query(VA_Products::_sql("i.item_id = ". $db->tosql($item_id, INTEGER), $access_level));
			return $db->next_record();
		}
		/**
		 * Find all availiable items ids
		 * @param String / Array $params: if string - than equals to normal where parameter, 
		 * if array - could be used for compplex requests,
		 * @param String $params["where"]
		 * @param String $params["brackets"] - brackets for joins
		 * @param String $params["join"]  - join query part, if some subtables needed
		 * @param String $params["order"] - full order syntax, like "ORDER BY i.item_id", but also could has GROUP part if needed
		 * @param Constant $access_level: VIEW_CATEGORIES_ITEMS_PERM, VIEW_ITEMS_PERM
		 * @param Boolean $debug - turn on debug output
		 * @return Array
		 */
		static function find_all_ids($params = "", $access_level = VIEW_CATEGORIES_ITEMS_PERM, $debug = false) {
			global $db;
			if ($debug) {
				$sql = VA_Products::_sql($params, $access_level);
				if ($db->DBType == "mysql") {
					echo sql_explain($sql);
				} else {
					echo $sql;
				}				
			}	
			$db->query(VA_Products::_sql($params, $access_level));
			$ids = array();
			while ($db->next_record()) {
				$id = $db->f(0);
				if (!in_array($id, $ids)) {
					$ids[] = $id;
				}
			}
			return $ids;
		}
		/**
		 * Find all availiable items with specified fields, keys of returned array are items ids
		 * @param String $key_field
		 * @param Array $fields
		 * @param String / Array $params: if string - than equals to normal where parameter, 
		 * if array - could be used for compplex requests,
		 * @param String $params["where"]
		 * @param String $params["brackets"] - brackets for joins
		 * @param String $params["join"]  - join query part, if some subtables needed
		 * @param String $params["order"] - full order syntax, like "ORDER BY i.item_id", but also could has GROUP part if needed
		 * @param Constant $access_level: VIEW_CATEGORIES_ITEMS_PERM, VIEW_ITEMS_PERM
		 * @param Boolean $debug - turn on debug output
		 * @return Array
		 */
		function find_all($key_field = "i.item_id", $fields = array(), $params = "", $access_level = VIEW_CATEGORIES_ITEMS_PERM, $debug = false) {
			global $db;			
			if (is_array($params)) {
				$params_prepared = $params;
				$params_prepared["select"] = implode(",", $fields);
			} else {
				$params_prepared = array();
				$params_prepared["where"] = $params;
			}
			$params_prepared["select"] = "";
			if ($key_field) {
				$params_prepared["select"] .= $key_field . ",";
			}
			if ($fields) {
				$params_prepared["select"] .= implode(",", $fields);
			}
			if ($debug) {
				$sql = VA_Products::_sql($params_prepared, $access_level);
				if ($db->DBType == "mysql") {
					echo sql_explain($sql);
				} else {
					echo $sql;
				}
			}
			$db->query(VA_Products::_sql($params_prepared, $access_level));
			
			$results = array();
			if ($key_field) {
				while ($db->next_record()) {
					$key = $db->f(0);
					$result = array();
					foreach ($fields AS $number => $field) {
						$result[$field] = $db->f($number + 1);
					}
					$results[$key] = $result;
				}
			} else {
				while ($db->next_record()) {
					$result = array();
					foreach ($fields AS $number => $field) {
						$result[$field] = $db->f($number);
					}
					$results[] = $result;
				}
			}
			return $results;
		}
		/**
		 * Find category id for selected item
		 * @param Integer $item_id
		 * @param Constant $access_level: VIEW_CATEGORIES_ITEMS_PERM, VIEW_ITEMS_PERM
		 * @return Integer
		 */
		function get_category_id($item_id, $access_level = VIEW_ITEMS_PERM) {
			global $db, $table_prefix;
			$params = array();
			$params["select"] = "c.category_id";
			$params["where"]  = "ic.item_id=" . $db->tosql($item_id, INTEGER);
			$params["brackets"]  = "(";
			$params["join"]  = "INNER JOIN " . $table_prefix . "items_categories ic ON ic.category_id = c.category_id)";
			$db->query(VA_Categories::_sql($params, $access_level));
			if ($db->next_record()) {
				return $db->f(0);
			} else {
				return 0;
			}		
		}		
	}
?>