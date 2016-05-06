<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  ads_functions.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	class VA_Ads_Categories {
		function _sql($params, $access_level) {
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
			$subscription_ids = get_session("session_subscription_ids");
			
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
			
			$sql .= $table_prefix . "ads_categories c ";
			
			if ($use_sites && isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "ads_categories_sites AS cs ON cs.category_id=c.category_id)";
			}
			if ($use_acls) {
				if (strlen($user_id)) {
					$sql .= " LEFT JOIN " . $table_prefix . "ads_categories_types AS ut ON ut.category_id=c.category_id)";
				}
				if (strlen($subscription_ids)) {
					$sql .= " LEFT JOIN " . $table_prefix . "ads_categories_subscriptions AS sb ON sb.category_id=c.category_id)";
				}
			}
			if (strlen($join)) {
				$sql .= $join;
			}
			
			$sql .= " WHERE 1=1";
						
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
		
		function find_all_ids($params = "", $access_level = VIEW_CATEGORIES_PERM) {
			global $db;	
			$db->query(VA_Ads_Categories::_sql($params, $access_level));
			$ids = array();
			while ($db->next_record()) {
				$id = $db->f(0);
				if (!in_array($id, $ids)) {
					$ids[] = $id;
				}
			}
			return $ids;
		}
		
		function find_all($key_field = "c.category_id", $fields = array(), $params = "", $access_level = VIEW_CATEGORIES_ITEMS_PERM) {
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
			$db->query(VA_Ads_Categories::_sql($params_prepared, $access_level));
			
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
		
		function check_permissions($category_id, $access_level = VIEW_CATEGORIES_PERM) {
			global $db;
			$db->query(VA_Ads_Categories::_sql("c.category_id=" . $db->tosql($category_id, INTEGER), $access_level));
			return $db->next_record();
		}
		
		function check_exists($category_id) {
			global $db;
			$params["where"]   = " c.category_id=" . $db->tosql($category_id, INTEGER);
			$params["no_acls"] = true;
			$db->query(VA_Ads_Categories::_sql($params, 0));
			return $db->next_record();
		}
	}
	
	class VA_Ads {
		function _sql($params, $access_level) {
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
			if (!$access_level) $access_level = VIEW_CATEGORIES_ITEMS_PERM;
					
			$user_id         = get_session("session_user_id");
			$user_type_id    = get_session("session_user_type_id");
			$subscription_id = get_session("session_subscription_id");
			$subscription_ids = get_session("session_subscription_ids");
			
			$sql = " SELECT ";
			if (strlen($select)) {
				$sql .= $select;
			} else {
				$sql .= " i.item_id ";
			}
			$sql .= " FROM ";
			
			if ($use_sites && isset($site_id)) {
				$sql .= " (";
			}
			if ($use_acls) {
				if (strlen($user_id)) {
					$sql .= " (";
				}
				if (strlen($subscription_ids)) {
					$sql .= " (";
				}
			}
			if (strlen($brackets)) {
				$sql .= $brackets;
			}
			
			$sql .= " (( " . $table_prefix . "ads_items i ";
			$sql .= " LEFT JOIN " . $table_prefix . "ads_assigned ac ON ac.item_id=i.item_id)";		
			$sql .= " LEFT JOIN " . $table_prefix . "ads_categories c ON c.category_id=ac.category_id)";
			
			if ($use_sites && isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "ads_categories_sites AS cs ON cs.category_id=c.category_id)";
			}
			if ($use_acls) {	
				if (strlen($user_id)) {
					$sql .= " LEFT JOIN " . $table_prefix . "ads_categories_types AS ut ON ut.category_id=c.category_id)";
				}			
				if (strlen($subscription_ids)) {
					$sql .= " LEFT JOIN " . $table_prefix . "ads_categories_subscriptions AS sb ON sb.category_id=c.category_id)";
				}
			}
			if (strlen($join)) {
				$sql .= $join;
			}
									
			$sql .= "  WHERE  i.is_approved=1 ";
			$sql .= " AND i.date_start<=" . $db->tosql(va_time(), DATETIME);
			$sql .= " AND i.date_end>" . $db->tosql(va_time(), DATETIME);
			
			if ($use_sites) {
				if (isset($site_id)) {
					$sql .= " AND (c.sites_all=1 OR cs.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
				} else {
					$sql .= " AND c.sites_all=1 ";
				}
			}
					
			if ($use_acls) {
				if (strlen($user_id) && strlen($subscription_ids)) {
					$sql .= " AND (" . format_binary_for_sql("c.access_level", $access_level);
					$sql .= " OR ("  . format_binary_for_sql("ut.access_level", $access_level) . " AND ut.user_type_id=". $db->tosql($user_type_id, INTEGER, true, false) . ") ";
					$sql .= " OR ("  . format_binary_for_sql("sb.access_level", $access_level) . " AND sb.subscription_id IN (". $db->tosql($subscription_id, INTEGERS_LIST) . ")) )";
				} elseif (strlen($user_id)) {
					$sql .= " AND (" . format_binary_for_sql("c.access_level", $access_level);
					$sql .= " OR ( " . format_binary_for_sql("ut.access_level", $access_level) . " AND ut.user_type_id=". $db->tosql($user_type_id, INTEGER, true, false) . ") )";
				} else {
					$sql .= " AND " . format_binary_for_sql("c.guest_access_level", $access_level);
				}
			}

						
			if (strlen($where)) {
				$sql .= " AND " . $where;
			}
			
			if (strlen($order)) {
				$sql .= " " . $order;
			}
			
			return $sql;
		}

		function credits($r, $existed_record, $subtract = false) 
		{
			global $db, $table_prefix;

			$item_id = $r->get_value("item_id");
			$user_id = $r->get_value("user_id");
			$current = array(
				"category_id" => "",
				"date_start" => "",
				"days_run" => "",
				"hot_date_start" => "",
				"hot_days_run" => "",
				"special_date_start" => "",
				"special_days_run" => "",
			);
			if ($existed_record) {
				// check current values
				$sql  = " SELECT * FROM " . $table_prefix . "ads_items ";
				$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
				$db->query($sql);
				if ($db->next_record()) {
					$current["date_start"] = $db->f("date_start", DATETIME);
					$current["days_run"] = $db->f("days_run");
					$current["hot_date_start"] = $db->f("hot_date_start", DATETIME);
					$current["hot_days_run"] = $db->f("hot_days_run");
					$current["special_date_start"] = $db->f("special_date_start", DATETIME);
					$current["special_days_run"] = $db->f("special_days_run");
				}
			}

			$credit_amount = 0;
			// check credits for category
			if (!$existed_record) {
				$category_id = get_param("category_id");
				$sql  = " SELECT publish_price FROM " . $table_prefix . "ads_categories ";
				$sql .= " WHERE category_id=" . $db->tosql($category_id, INTEGER);
				$publish_price = get_db_value($sql);
				if ($publish_price > 0) {
					$credit_amount += $publish_price;
				}
			}

			$days_run = $r->get_value("days_run");
			$date_start = $r->get_value("date_start");
			if ($days_run && ($days_run != $current["days_run"] || $date_start != $current["days_run"])) {
				$sql  = " SELECT publish_price FROM " . $table_prefix . "ads_days ";
				$sql .= " WHERE days_id=" . $db->tosql($days_run, INTEGER);
				$publish_price = get_db_value($sql);
				if ($publish_price > 0) {
					$credit_amount += $publish_price;
				}
			}

			$hot_days_run = $r->get_value("hot_days_run");
			$hot_date_start = $r->get_value("hot_date_start");
			if ($hot_days_run && ($hot_days_run != $current["hot_days_run"] || $hot_date_start != $current["hot_date_start"])) {
				$sql  = " SELECT publish_price FROM " . $table_prefix . "ads_hot_days ";
				$sql .= " WHERE days_id=" . $db->tosql($hot_days_run, INTEGER);
				$publish_price = get_db_value($sql);
				if ($publish_price > 0) {
					$credit_amount += $publish_price;
				}
			}

			$special_days_run = $r->get_value("special_days_run");
			$special_date_start = $r->get_value("special_date_start");
			if ($special_days_run && ($special_days_run != $current["special_days_run"] || $special_date_start != $current["special_date_start"])) {
				$sql  = " SELECT publish_price FROM " . $table_prefix . "ads_special_days ";
				$sql .= " WHERE days_id=" . $db->tosql($special_days_run, INTEGER);
				$publish_price = get_db_value($sql);
				if ($publish_price > 0) {
					$credit_amount += $publish_price;
				}
			}

			if ($subtract && $user_id && $credit_amount > 0) {
				$cdt = new VA_Record($table_prefix . "users_credits");
				$cdt->add_textbox("user_id", INTEGER);
				$cdt->add_textbox("order_id", INTEGER);
				$cdt->add_textbox("order_item_id", INTEGER);
				$cdt->add_textbox("credit_amount", NUMBER);
				$cdt->add_textbox("credit_action", INTEGER);
				$cdt->add_textbox("credit_type", INTEGER);
				$cdt->add_textbox("date_added", DATETIME);
		  
				// subtract or return credit amount from credit balance
				$cdt->set_value("user_id", $user_id);
				$cdt->set_value("order_id", 0);
				$cdt->set_value("order_item_id", 0);
				$cdt->set_value("credit_amount", $credit_amount);
				$cdt->set_value("credit_action", -1);
				$cdt->set_value("credit_type", 4);
				$cdt->set_value("date_added", va_time());
				$cdt->insert_record();
		  
				// update credit balance field in users table
				$sql  = " SELECT SUM(credit_action * credit_amount) ";
				$sql .= " FROM " . $table_prefix . "users_credits ";
				$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
				$total_credit_sum = get_db_value($sql);
		  
				$sql  = " UPDATE " . $table_prefix . "users ";
				$sql .= " SET credit_balance=" . $db->tosql($total_credit_sum, NUMBER);
				$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
				$db->query($sql);
		  
				// update user information in session if available
				$user_info = get_session("session_user_info");
				$session_user_id = get_setting_value($user_info, "user_id", 0);
				if ($session_user_id == $user_id) {
					$user_info["credit_balance"] = $total_credit_sum;
					set_session("session_user_info", $user_info);
				}
			}
			return $credit_amount;
		}
		
		function get_days_list($table_name) 
		{
			global $db;
			$days = array(array("", "", ""));
			$sql = " SELECT * FROM " . $table_name . " ORDER BY days_number ";
			$db->query($sql);
			while ($db->next_record()) {
				$days_id = $db->f("days_id");
				$days_number = $db->f("days_number");
				$days_title = $db->f("days_title");
				$publish_price = $db->f("publish_price");
				if (!$days_title) {
					$days_title = $days_number . " " . DAYS_MSG;
				}
				if ($publish_price > 0) {
					$days_title .= " (" . currency_format($publish_price) . ")";
				}
				$days[] = array($days_id, $days_title, $publish_price);
			}
			return $days;
		}

		function get_list_price($list, $list_id) 
		{
			$price = 0;
			foreach ($list as $list_key => $list_info) {
				$row_id = $list_info[0];
				$row_price = $list_info[2];
				if ($row_id == $list_id) {
					$price = $row_price;
					break;
				}
			}
			return $price;
		}

		function check_permissions($item_id, $category_id = 0, $access_level = VIEW_CATEGORIES_ITEMS_PERM) {
			global $db;
			$where = " i.item_id=" . $db->tosql($item_id, INTEGER);
			if ($category_id) {
				$where .= " AND c.category_id=" . $db->tosql($category_id, INTEGER);
			}			
			$db->query(VA_Ads::_sql($where, $access_level));
			return $db->next_record();
		}
						
		function check_exists($item_id, $category_id = false) {
			global $db;
			$params["where"] = " i.item_id=" . $db->tosql($item_id, INTEGER);
			if ($category_id) {
				$params["where"] .= " AND c.category_id=" . $db->tosql($category_id, INTEGER);
			}
			$params["no_acls"]  = true;
			$db->query(VA_Ads::_sql($params, 0));
			return $db->next_record();
		}
		
		function get_category_id($item_id, $access_level = VIEW_CATEGORIES_ITEMS_PERM) {
			global $db;
			$params = array();
			$params["select"] = "ac.category_id";
			$params["where"]  = "i.item_id=" . $db->tosql($item_id, INTEGER);
			$db->query(VA_Ads::_sql($params, $access_level));
			if ($db->next_record()) {
				return $db->f(0);
			} else {
				return 0;
			}			
		}
		
		function find_all_ids($params = "", $access_level = VIEW_CATEGORIES_ITEMS_PERM) {
			global $db;	
			$db->query(VA_Ads::_sql($params, $access_level));
			$ids = array();
			while ($db->next_record()) {
				$id = $db->f(0);
				if (!in_array($id, $ids)) {
					$ids[] = $id;
				}
			}
			return $ids;
		}

		function find_all($key_field = "i.item_id", $fields = array(), $params = "", $access_level = VIEW_CATEGORIES_ITEMS_PERM, $debug = false) {
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
				$sql = VA_Ads::_sql($params_prepared, $access_level);
				if ($db_type == "mysql") {
					echo sql_explain($sql);
				} else {
					echo $sql;
				}
			}
			$db->query(VA_Ads::_sql($params_prepared, $access_level));
			
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
?>