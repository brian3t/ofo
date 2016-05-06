<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  multisites_functions.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/
	
	function update_product_user_types_by_categories($item_id, $categories=null, $user_types_all=1, $user_types = null) {
		global $db, $table_prefix;
		
		if (!$categories) {
			$categories = array();
			$sql  = " SELECT category_id FROM  " . $table_prefix . "items_categories ";
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER, true, false) . " ";
			$db->query($sql);
			while ($db->next_record()) {
				$category_id = $db->f('category_id');
				$categories[] = $category_id;
			}			
		}
		
		$sql  = " DELETE FROM  " . $table_prefix . "items_user_types WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);

		if(!$categories) {
			$sql = "UPDATE " . $table_prefix . "items SET user_types_all=0 WHERE item_id=" . $db->tosql($item_id, INTEGER);
			$db->query($sql);
			return false;
		}
		
		$is_in_top_category = in_array(0,$categories);

		if (!$is_in_top_category) {
			$sql  = " SELECT MAX(user_types_all) FROM " . $table_prefix ."categories";
			$sql .= " WHERE category_id IN(" . $db->tosql($categories, INTEGERS_LIST, false) . ")";
			$user_types_all = get_db_value($sql);
		}
		
		$sql  = " UPDATE " . $table_prefix . "items ";
		$sql .= " SET user_types_all=" . $db->tosql($user_types_all, INTEGER);
		$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);
		
		if ( ($user_types_all) || ( !$user_types || !count($user_types) ) ) {
			return true;
		}

		$sql = false;		
		if (!$is_in_top_category) {
			$sql  = " SELECT user_type_id FROM  " . $table_prefix . "categories_user_types ";		
			$sql .= " WHERE category_id IN(" . $db->tosql($categories, INTEGERS_LIST, false) . ") ";				
			if ($user_types) {
				$sql .= " AND user_type_id IN (" . $db->tosql($user_types, INTEGERS_LIST) ." )";
			}
			$sql .= " GROUP BY user_type_id";
		} elseif ($user_types) {
			$sql  = " SELECT type_id FROM  " . $table_prefix . "user_types ";			 
			$sql .= " WHERE type_id IN (" . $db->tosql($user_types, INTEGERS_LIST) ." )";			
		}	
		if ($sql) {		
			$db->query($sql);			
			$user_types = array();
			while ($db->next_record()) {
				$type_id = $db->f(0);
				$user_types[] = $type_id;
			}			
					
			for ($i=0, $count = count($user_types); $i<$count; $i++) {
				$sql  = " INSERT INTO " . $table_prefix . "items_user_types (item_id, user_type_id) ";
				$sql .= " VALUES (" . $db->tosql($item_id, INTEGER) ."," . $db->tosql($user_types[$i], INTEGER) .")";
				$db->query($sql);
			}
		}
	}
	
	
	function update_product_sites_by_categories($item_id, $categories=null, $sites_all=1, $sites = null) {
		global $db, $table_prefix;
		
		if (!$categories) {
			$categories = array();
			$sql  = " SELECT category_id FROM  " . $table_prefix . "items_categories ";
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER, true, false) . " ";
			$db->query($sql);
			while ($db->next_record()) {
				$category_id = $db->f('category_id');
				$categories[] = $category_id;
			}			
		}
		
		$sql  = " DELETE FROM  " . $table_prefix . "items_sites WHERE item_id=" . $db->tosql($item_id, INTEGER);
		$db->query($sql);

		if(!$categories) {
			$sql = "UPDATE " . $table_prefix . "items SET sites_all=0 WHERE item_id=" . $db->tosql($item_id, INTEGER);
			$db->query($sql);
			return false;
		}		
		
		$is_in_top_category = in_array(0, $categories);
		
		if (!$is_in_top_category) {	
			$sql  = " SELECT MAX(sites_all) FROM " . $table_prefix ."categories";
			$sql .= " WHERE category_id IN(" . $db->tosql($categories, INTEGERS_LIST, false) . ")";
			$sites_all = get_db_value($sql);
		}
				
		if ( ($sites_all) && ( !$sites || !count($sites) ) ) {
			$sql  = " UPDATE " . $table_prefix . "items ";
			$sql .= " SET sites_all=" . $db->tosql($sites_all, INTEGER);
			$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
			$db->query($sql);
		
			return true;
		}
		$sql = false;
		if (!$sites_all && !$is_in_top_category) {		
			$sql  = " SELECT site_id FROM  " . $table_prefix . "categories_sites ";
			$sql .= " WHERE category_id IN(" . $db->tosql($categories, INTEGERS_LIST, false) . ") ";			
			if ($sites) {
				$sql .= " AND site_id IN (" . $db->tosql($sites, INTEGERS_LIST) ." )";
			}
			$sql .= " GROUP BY site_id";
		} elseif ($sites) {
			$sql  = " SELECT site_id FROM  " . $table_prefix . "sites ";
			$sql .= " WHERE site_id IN (" . $db->tosql($sites, INTEGERS_LIST) ." )";			
		}
		
		if ($sql) {			
			$db->query($sql);			
			$sites = array();
			while ($db->next_record()) {
				$site_id = $db->f('site_id');
				$sites[] = $site_id;
			}			
					
			for ($i=0, $count = count($sites); $i<$count; $i++) {
				$sql  = " INSERT INTO " . $table_prefix . "items_sites (item_id, site_id) ";
				$sql .= " VALUES (" . $db->tosql($item_id, INTEGER) ."," . $db->tosql($sites[$i], INTEGER) .")";
				$db->query($sql);			
			}
		}
	}
?>