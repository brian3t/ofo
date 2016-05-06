<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  forums_functions.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	class VA_Forum_Categories {
		
		/**
		 * Check if the category with this id exists
		 *
		 * @param Integer $category_id
		 * @return Boolean
		 */
		function check_exists($category_id) {
			global $db, $table_prefix, $site_id;
			
			$sql  = " SELECT c.category_id FROM ";
			if (isset($site_id)) {
				$sql .= "( ";
			}
			$sql .= $table_prefix . "forum_categories c ";
			if (isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "forum_categories_sites AS s ON s.category_id=c.category_id)";
			}
			if (isset($site_id)) {
				$sql .= " WHERE (c.sites_all=1 OR s.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
			} else {
				$sql .= " WHERE c.sites_all=1 ";
			}
			$sql .= " AND c.category_id=" . $db->tosql($category_id, INTEGER);
			$db->query($sql);
			return $db->next_record();
		}
	}
	
	class VA_Forums {		
		
		/**
		 * Internal function, that builds queries for forums search
		 *
		 * @param String / Array $params: if string - than equals to normal where parameter, 
		 * if array - could be used for compplex requests,
		 * @param String $params["select"] - fields names, separated by comma
		 * @param String $params["where"]
		 * @param String $params["brackets"] - brackets for joins
		 * @param String $params["join"]  - join query part, if some subtables needed
		 * @param String $params["order"] - full order syntax, like "ORDER BY i.item_id", but also could has GROUP part if needed
		 * @param Constant $access_level: VIEW_FORUM_PERM, VIEW_TOPICS_PERM, VIEW_TOPIC_PERM, POST_TOPICS_PERM, POST_REPLIES_PERM, POST_ATTACHMENTS_PERM
		 * @return String
		 */
		function _sql($params, $access_level) {
			global $table_prefix, $db, $site_id, $language_code;
			$select = "";
			$where = "";
			$order = "";
			$join = "";
			$brackets = "";
			if (is_array($params)) {
				$select = isset($params["select"]) ? $params["select"] : "";
				$where  = isset($params["where"]) ? $params["where"] : "";
				$order  = isset($params["order"]) ? $params["order"] : "";
				$join   = isset($params["join"])  ? $params["join"] : "";
				$brackets = isset($params["brackets"])  ? $params["brackets"] : "";
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
			if (strlen($select)) {
				$sql .= $select;
			} else {
				$sql .= "fl.forum_id ";
			}
			
			$sql .= " FROM ( ";
			if (isset($site_id)) {
				$sql .= "(";
			};
			if (strlen($user_id)) {
				$sql .= "(";
			};
			if (strlen($subscription_ids)) {
				$sql .= "(";
			}
			if (strlen($brackets)) {
				$sql .= $brackets;
			};
			
			$sql .= " " . $table_prefix . "forum_list fl ";	
			$sql .= " INNER JOIN " . $table_prefix . "forum_categories c ON c.category_id=fl.category_id)";
			if (isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "forum_categories_sites AS s ON s.category_id=c.category_id)";	
			}
			if (strlen($user_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "forum_user_types AS ut ON ut.forum_id=fl.forum_id)";
			}
			if (strlen($subscription_ids)) {
				$sql .= " LEFT JOIN " . $table_prefix . "forum_subscriptions AS sb ON sb.forum_id=fl.forum_id)";
			}
			if (strlen($join)) {
				$sql .= $join;
			};	
			
			if (isset($site_id)) {
				$sql .= " WHERE (c.sites_all=1 OR s.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
			} else {
				$sql .= " WHERE c.sites_all=1 ";
			}			
			if (strlen($user_id) && strlen($subscription_ids)) {
				$sql .= " AND ( " . format_binary_for_sql("fl.access_level", $access_level);
				$sql .= " OR (  " . format_binary_for_sql("ut.access_level", $access_level) . " AND ut.user_type_id=". $db->tosql($user_type_id, INTEGER, true, false) . ") ";
				$sql .= " OR (  " . format_binary_for_sql("sb.access_level", $access_level) . " AND sb.subscription_id IN (". $db->tosql($subscription_id, INTEGERS_LIST) . ")) )";
			} elseif (strlen($user_id)) {
				$sql .= " AND ( " . format_binary_for_sql("fl.access_level", $access_level);
				$sql .= " OR (  " . format_binary_for_sql("ut.access_level", $access_level) . " AND ut.user_type_id=". $db->tosql($user_type_id, INTEGER, true, false) . ") )";
			} else {
				$sql .= " AND " . format_binary_for_sql("fl.guest_access_level", $access_level);
			}
			
			$sql .= " AND c.allowed_view = 1 ";
		
			if (strlen($where)) {
				$sql .= " AND " . $where;
			}
			
			if (strlen($order)) {
				$sql .= " " . $order;
			}
			
			return $sql;
		}
		
		/**
		 * Find all availiable forums ids
		 * @param String / Array $params: if string - than equals to normal where parameter, 
		 * if array - could be used for compplex requests,
		 * @param String $params["where"]
		 * @param String $params["brackets"] - brackets for joins
		 * @param String $params["join"]  - join query part, if some subtables needed
		 * @param String $params["order"] - full order syntax, like "ORDER BY i.item_id", but also could has GROUP part if needed
		 * @param Constant $access_level: VIEW_FORUM_PERM, VIEW_TOPICS_PERM, VIEW_TOPIC_PERM, POST_TOPICS_PERM, POST_REPLIES_PERM, POST_ATTACHMENTS_PERM
		 * @param Boolean $debug - turn on debug output
		 * @return Array
		 */
		function find_all_ids($params = "", $access_level = VIEW_FORUM_PERM, $debug = false) {
			global $db, $db_type;
			if ($debug) {
				$sql = VA_Forums::_sql($params, $access_level);
				if ($db_type == "mysql") {
					echo sql_explain($sql);
				} else {
					echo $sql;
				}
			}
			$db->query(VA_Forums::_sql($params, $access_level));
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
		 * Find all availiable forums with specified fields, keys of returned array are items ids
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
		function find_all($key_field = "fl.forum_id", $fields = array(), $params = "", $access_level = VIEW_CATEGORIES_ITEMS_PERM, $debug = false) {
			global $db, $db_type;			
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
				$sql = VA_Forums::_sql($params_prepared, $access_level);
				if ($db_type == "mysql") {
					echo sql_explain($sql);
				} else {
					echo $sql;
				}
			}
			$db->query(VA_Forums::_sql($params_prepared, $access_level));
			
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
		 * Check if the forum with this id is availiable with selected access level
		 *
		 * @param Integer $forum_id
		 * @param Constant $access_level: VIEW_FORUM_PERM, VIEW_TOPICS_PERM, VIEW_TOPIC_PERM, POST_TOPICS_PERM, POST_REPLIES_PERM, POST_ATTACHMENTS_PERM
		 * @return Boolean
		 */
		function check_permissions($forum_id, $access_level = VIEW_FORUM_PERM) {
			global $db;
			$db->query(VA_Forums::_sql("fl.forum_id=" . $db->tosql($forum_id, INTEGER), $access_level));
			return $db->next_record();
		}
	}

?>