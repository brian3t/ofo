<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  articles_functions.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	class VA_Articles_Categories {
		
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
			
			$sql .= $table_prefix . "articles_categories c ";
			
			if ($use_sites && isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "articles_categories_sites AS cs ON cs.category_id=c.category_id)";
			}
			if ($use_acls) {
				if (strlen($user_id)) {
					$sql .= " LEFT JOIN " . $table_prefix . "articles_categories_types AS ut ON ut.category_id=c.category_id)";
				}
				if (strlen($subscription_ids)) {
					$sql .= " LEFT JOIN " . $table_prefix . "articles_categories_subscriptions AS sb ON sb.category_id=c.category_id)";
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
			
			if (strlen($order)) {
				$sql .= " " . $order;
			}
			
			return $sql;
		}
			
		function check_permissions($category_id, $access_level = VIEW_CATEGORIES_PERM) {
			global $db;
			$db->query(VA_Articles_Categories::_sql("c.category_id=" . $db->tosql($category_id, INTEGER), $access_level));
			return $db->next_record();
		}
		
		function check_exists($category_id) {
			global $db;
			$params["where"]   = " c.category_id=" . $db->tosql($category_id, INTEGER);
			$params["no_acls"] = true;
			$db->query(VA_Articles_Categories::_sql($params, 0));
			return $db->next_record();
		}
		
		function find_all_ids($params = "", $access_level = VIEW_CATEGORIES_PERM) {
			global $db;	
			$db->query(VA_Articles_Categories::_sql($params, $access_level));
			$ids = array();
			while ($db->next_record()) {
				$id = $db->f(0);
				if (!in_array($id, $ids)) {
					$ids[] = $id;
				}
			}
			return $ids;
		}
		
		function find_all($key_field = "c.category_id", $fields = array(), $params = "", $access_level = VIEW_CATEGORIES_PERM, $debug = false) {
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
				$sql = VA_Articles_Categories::_sql($params_prepared, $access_level);
				if ($db_type == "mysql") {
					echo sql_explain($sql);
				} else {
					echo $sql;
				}
			}
			$db->query(VA_Articles_Categories::_sql($params_prepared, $access_level));
			
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
		
		function get_min_top_id() {
			global $db, $table_prefix;
			
			$sql  = " SELECT c.category_id FROM ";
			if (isset($site_id)) {
				$sql .= "( ";
			}
			$sql .= $table_prefix . "articles_categories c ";
			if (isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "articles_categories_sites AS s ON s.category_id=c.category_id)";
			}
			if (isset($site_id)) {
				$sql .= " WHERE (c.sites_all=1 OR s.site_id=". $db->tosql($site_id, INTEGER, true, false) . ") ";
			} else {
				$sql .= " WHERE c.sites_all=1 ";
			}
			$sql .= " AND c.parent_category_id=0";
			$db->RecordsPerPage = 1;
			$db->query($sql);
			return $db->next_record();
		}
	}
	
	class VA_Articles {
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
				$sql .= " a.article_id ";
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
			
			$sql .= " ((( " . $table_prefix . "articles a ";
			$sql .= " LEFT JOIN " . $table_prefix . "articles_assigned ac ON a.article_id=ac.article_id)";
			$sql .= " LEFT JOIN " . $table_prefix . "articles_categories c ON ac.category_id=c.category_id)";
			$sql .= " LEFT JOIN " . $table_prefix . "articles_statuses st ON a.status_id=st.status_id)";
			
			if ($use_sites && isset($site_id)) {
				$sql .= " LEFT JOIN " . $table_prefix . "articles_categories_sites AS cs ON cs.category_id=c.category_id)";
			}
			if ($use_acls) {
				if (strlen($user_id)) {
					$sql .= " LEFT JOIN " . $table_prefix . "articles_categories_types AS ut ON ut.category_id=c.category_id)";
				}			
				if (strlen($subscription_ids)) {
					$sql .= " LEFT JOIN " . $table_prefix . "articles_categories_subscriptions AS sb ON sb.category_id=c.category_id)";
				}
			}
			if (strlen($join)) {
				$sql .= $join;
			}
			
			$sql .= " WHERE st.allowed_view=1 ";
			
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
					$sql .= " OR ( "  . format_binary_for_sql("ut.access_level", $access_level) . " AND ut.user_type_id=". $db->tosql($user_type_id, INTEGER, true, false) . ") ";
					$sql .= " OR ( "  . format_binary_for_sql("sb.access_level", $access_level) . " AND sb.subscription_id IN (". $db->tosql($subscription_id, INTEGERS_LIST) . ")) )";
				} elseif (strlen($user_id)) {
					$sql .= " AND ( " . format_binary_for_sql("c.access_level", $access_level);
					$sql .= " OR ( "  . format_binary_for_sql("ut.access_level", $access_level) . " AND ut.user_type_id=". $db->tosql($user_type_id, INTEGER, true, false) . ") )";
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
		
		function check_permissions($article_id, $category_id = 0, $access_level = VIEW_CATEGORIES_ITEMS_PERM) {
			global $db;
			$where = " a.article_id=" . $db->tosql($article_id, INTEGER);
			if ($category_id) {
				$where .= " AND c.category_id=" . $db->tosql($category_id, INTEGER);
			}			
			$db->query(VA_Articles::_sql($where, $access_level));
			return $db->next_record();
		}
		
		function check_exists($article_id, $category_id = false) {
			global $db;
			$params["where"] = " a.article_id=" . $db->tosql($article_id, INTEGER);
			if ($category_id) {
				$params["where"] .= " AND c.category_id=" . $db->tosql($category_id, INTEGER);
			}
			$params["no_acls"]  = true;
			$db->query(VA_Articles::_sql($params, 0));
			return $db->next_record();
		}
		
		function get_category_id($article_id, $access_level = VIEW_CATEGORIES_ITEMS_PERM) {
			global $db;
			$params = array();
			$params["select"] = "ac.category_id";
			$params["where"]  = "a.article_id=" . $db->tosql($article_id, INTEGER);
			$db->query(VA_Articles::_sql($params, $access_level));
			if ($db->next_record()) {
				return $db->f(0);
			} else {
				return 0;
			}
		}
		
		function get_top_id($article_id) {
			global $db;
			$params = array();
			$params["select"]    = "c.category_id, c.category_path";
			$params["where"]     = "a.article_id=" . $db->tosql($article_id, INTEGER);
			$params["no_sites"] = true;
			$params["no_acls"]  = true;
			$db->query(VA_Articles::_sql($params, 0));
			if ($db->next_record()) {
				$category_id = $db->f(0);
				$category_path = $db->f(1);
				$tmp = explode(",", $category_path);
				if (isset($tmp[1]) && $tmp[1]) {
					return $tmp[1];
				} else {
					return $category_id;
				}
			} else {
				return 0;
			}
		}
		
		function find_all_ids($params = "", $access_level = VIEW_CATEGORIES_ITEMS_PERM) {
			global $db;	
			$db->query(VA_Articles::_sql($params, $access_level));
			$ids = array();
			while ($db->next_record()) {
				$id = $db->f(0);
				if (!in_array($id, $ids)) {
					$ids[] = $id;
				}
			}
			return $ids;
		}

		function find_all($key_field = "a.article_id", $fields = array(), $params = "", $access_level = VIEW_CATEGORIES_ITEMS_PERM, $debug = false) {
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
				$sql = VA_Articles::_sql($params_prepared, $access_level);
				if ($db_type == "mysql") {
					echo sql_explain($sql);
				} else {
					echo $sql;
				}
			}
			$db->query(VA_Articles::_sql($params_prepared, $access_level));
			
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
				
		function delete($articles_ids) {
			global $db, $table_prefix;
			
			if (!strlen($articles_ids)) return false;
			
			$db->query("DELETE FROM " . $table_prefix . "articles_assigned WHERE article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")");
			$db->query("DELETE FROM " . $table_prefix . "articles_forum_topics WHERE article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")");
			$db->query("DELETE FROM " . $table_prefix . "articles_related WHERE article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")");
			$db->query("DELETE FROM " . $table_prefix . "articles_items_related WHERE article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")");
			$db->query("DELETE FROM " . $table_prefix . "articles_images WHERE article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")");
			$db->query("DELETE FROM " . $table_prefix . "articles_reviews WHERE article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")"); 
			$db->query("DELETE FROM " . $table_prefix . "articles WHERE article_id IN (" . $db->tosql($articles_ids, INTEGERS_LIST) . ")");
		}
	}

	function articles_import_rss($is_remote_rss, $remote_rss_url, $remote_rss_date_updated, $remote_rss_refresh_rate, $remote_rss_ttl)
	{
		global $db, $table_prefix, $category_id;

		$current_ts = va_timestamp();

		if ($remote_rss_refresh_rate) {
			$refresh_rate = $remote_rss_refresh_rate;
		} else if ($remote_rss_ttl) {
			$refresh_rate = $remote_rss_ttl;
		} else {
			$refresh_rate = 10;
		}
		
		$refresh_ts = ($refresh_rate * 60);
		if (is_array($remote_rss_date_updated)) {
			$refresh_ts += va_timestamp($remote_rss_date_updated);
		}

		if ($refresh_ts > $current_ts) {
			return false;
		}
  
		$article_order = 1;
		$feeds = '';

		$ch = curl_init();
		if ($ch){
			curl_setopt($ch, CURLOPT_URL, $remote_rss_url);
			// if use proxy server
			//curl_setopt($ch, CURLOPT_PROXY, "proxy_server:port");
			//curl_setopt($ch, CURLOPT_PROXYUSERPWD, "login:password");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_TIMEOUT, 30);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			set_curl_options($ch,1);

			$feeds = curl_exec($ch);
			curl_close($ch);
		} else {
			$feeds = false;
		}

		if ($feeds) {
	  
			if ($remote_rss_url && strlen($feeds)) {
				$feeds = trim($feeds);
				if (strlen ($feeds)) {
					$sql = "SELECT * FROM " . $table_prefix."articles_assigned WHERE category_id = " . $db->tosql($category_id, INTEGER, true, false);
					$db->query($sql);
					$articles_ids = "";
					if ($db->next_record()){
						$articles_ids = $db->f("article_id");
						do {
							$articles_ids .= "," . $db->f("article_id");
						} while ($db->next_record());
					}
					VA_Articles::delete($articles_ids);
					if (strpos($feeds,"<ttl>") && strpos($feeds,"<ttl>") < strpos($feeds,"<item>")){
						$ttl = substr($feeds, strpos($feeds, "<ttl>")+strlen("<ttl>"), strpos($feeds, "</ttl>")-strlen("<ttl>")-strpos($feeds, "<ttl>"));
						$sql = "UPDATE " . $table_prefix . "articles_categories SET remote_rss_date_updated=" . $db->tosql($current_ts, DATETIME) . ", remote_rss_ttl=" . $db->tosql($ttl,INTEGER) . " WHERE category_id=" . $db->tosql($category_id, INTEGER, true, false);
					} else {
						$sql = "UPDATE " . $table_prefix . "articles_categories SET remote_rss_date_updated=" . $db->tosql($current_ts, DATETIME) . ", remote_rss_ttl=NULL WHERE category_id=" . $db->tosql($category_id, INTEGER, true, false);
					}
					$db->query($sql);
	  
					$index = 0;
					$feeds = substr($feeds, strpos($feeds, "<item>"));
	  
					while (strpos($feeds, "</item>")) {
						$aryItems[$index]['title'] = substr($feeds, strpos($feeds, "<title>")+strlen("<title>"), strpos($feeds, "</title>")-strlen("<title>")-strpos($feeds, "<title>"));
						$aryItems[$index]['link'] = substr($feeds, strpos($feeds, "<link>")+strlen("<link>"), strpos($feeds, "</link>")-strlen("<link>")-strpos($feeds, "<link>"));
						$aryItems[$index]['description'] = substr($feeds, strpos($feeds, "<description>")+strlen("<description>"), strpos($feeds, "</description>")-strlen("<description>")-strpos($feeds, "<description>"));
						if (strpos($feeds,"<fulltext>")) {
							if (strpos($feeds,"<fulltext>") < strpos($feeds,"<pubDate>")){
								$aryItems[$index]['fulltext'] = substr($feeds, strpos($feeds, "<fulltext>")+strlen("<fulltext>"), strpos($feeds, "</fulltext>")-strlen("<fulltext>")-strpos($feeds, "<fulltext>"));
							} else {
								$aryItems[$index]['fulltext'] = '';
							}
						} else {
							$aryItems[$index]['fulltext'] = '';
						}
						$aryItems[$index]['pubDate'] = substr($feeds, strpos($feeds, "<pubDate>")+strlen("<pubDate>"), strpos($feeds, "</pubDate>")-strlen("<pubDate>")-strpos($feeds, "<pubDate>"));
						$aryItems[$index]['description'] = import_rss_clean($aryItems[$index]['description']);
						$aryItems[$index]['fulltext'] = import_rss_clean($aryItems[$index]['fulltext']);
						$aryItems[$index]['link'] = import_rss_clean($aryItems[$index]['link']);
						$aryItems[$index]['pubDate'] = import_rss_clean($aryItems[$index]['pubDate']);
						$aryItems[$index]['title'] = import_rss_clean($aryItems[$index]['title']);
						if ( substr_count($aryItems[$index]['description'], "<img") > 1 || substr_count($aryItems[$index]['description'], "<img") == 0) {
							$aryItems[$index]['description'] = strip_tags($aryItems[$index]['description']);
							$aryItems[$index]['image'] = '';
							$aryItems[$index]['alt'] = '';
						} else {
							$image = $aryItems[$index]['description'];
							$image = substr($image, strpos($image, "<img ")+4);
							$img = preg_match("/src\=\"(.*)\"{1}/i", $image, $images);
							$img = preg_replace("/src\=\"/i", "", $images[0]);
							$img = preg_replace("/\".*/i", "", $img);
							if (strpos($image,"alt=\"")){
								$alt = preg_replace("/.*alt\=\"/i", "", $image);
								$alt = preg_replace("/\".*/i", "", $alt);
							} else {
								$alt = "";
							}
							$aryItems[$index]['description'] = strip_tags($aryItems[$index]['description']);
							$aryItems[$index]['image'] = $img;
							$aryItems[$index]['alt'] = $alt;
						}
						$index++;
						$feeds = substr($feeds, strpos($feeds, "</item>") + strlen("</item>"));
					}
	  
					for ($i=0;$i<$index;$i++) {
						$db->query("SELECT MAX(article_id) FROM " . $table_prefix . "articles");
						$db->next_record();
						$article_id = $db->f(0) + 1;
	  
						$sql = "INSERT INTO " . $table_prefix . "articles (friendly_url, article_id, article_order, article_date, article_title, date_added, short_description, full_description, status_id, is_remote_rss, details_remote_url, image_small, image_small_alt) ";
						$sql .= "VALUES ('',";
						$sql .= $db->tosql($article_id, INTEGER) . ",";
						$sql .= $db->tosql($article_order, INTEGER) . ",";
						$sql .= $db->tosql(strtotime($aryItems[$i]['pubDate']), DATETIME) . ",";
						$sql .= $db->tosql($aryItems[$i]['title'], TEXT) . ",";
						$sql .= $db->tosql($current_ts, DATETIME) . ",";
						$sql .= $db->tosql($aryItems[$i]['description'], TEXT) . ",";
						$sql .= $db->tosql($aryItems[$i]['fulltext'], TEXT) . ",";
						$sql .= "1,1,";
						$sql .= $db->tosql($aryItems[$i]['link'], TEXT) . ",";
						$sql .= $db->tosql($aryItems[$i]['image'], TEXT, true, false) . ",";
						$sql .= $db->tosql($aryItems[$i]['alt'], TEXT, true, false) . ")";
						$db->query($sql);
	  
						$sql  = " INSERT INTO " . $table_prefix . "articles_assigned (article_id, category_id) VALUES (";
						$sql .= $db->tosql($article_id, INTEGER) . ",";
						$sql .= $db->tosql($category_id, INTEGER) . ")";
						$db->query($sql);
					}
				}
			} else {
				return false;
			}
	
			return $index;
		} else {
			return false;
		}
	}
	
	function import_rss_clean($string){
		$string = preg_replace("/\<\!\[CDATA\[/", "", $string);
		$string = preg_replace("/\]\]\>/", "", $string);
		$string = preg_replace("/\&lt;/", "<", $string);
		$string = preg_replace("/\&gt;/", ">", $string);
		return $string;
	}

?>