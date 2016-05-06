<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  ajax_list_tree.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

/**
 * 
 * @example Usage Example (admin_item_related.php and admin_item_related.html)
 * 1) init ajax tree list and set it as ajax requests listener
 *	require($root_folder_path . "includes/ajax_list_tree.php");
 *	$list = new VA_Ajax_List_Tree($settings["admin_templates_dir"], "ajax_list_tree.html");
 *	$list->set_branches('categories', 'category_id', 'category_name', 'parent_category_id');
 *	$list->set_leaves('items', 'item_id', 'item_name', 'items_categories');
 *	$list->ajax_listen('products_ajax_tree', 'admin_item_related.php');
 * 	
 *	2) show root tree in your template 
 *	$list->parse_root_tree('products_ajax_tree', 'admin_item_related.php');
 * 
 *  3) add in template <script language="JavaScript" type= "text/javascript" src="../js/ajax_list_tree.js"></script>
 *  and {products_ajax_tree}
 */
include_once($root_folder_path . "includes/navigator.php");
	
class VA_Ajax_List_Tree {
	var $t;
	var $branches_table_name, $branches_id_field, $branches_name_field, $branches_recursion_field;
	var $branches_group, $branches_where,  $branches_order;
	
	var $leaves_table_name,   $leaves_id_field,   $leaves_name_field,   $leaves_external_table_name;
	var $leaves_where, $leaves_order, $leaves_before_join, $leaves_after_join;
	
	var $topbranches_table_name, $topbranches_id_field, $topbranches_name_field;
	
	var $template_path, $filename, $navigator_per_page, $navigator_pages_number;
	
	var $action_object_id, $action_object_type, $leaf_action_type;
	

	function VA_Ajax_List_Tree($template_path, $filename){
		$this->t = new VA_Template($template_path);
		$this->t->set_file("ajax_list_tree", $filename);
		
		$this->template_path            = $template_path; 
		$this->filename                  = $filename;
		$this->navigator_per_page        = 25;
		$this->navigator_pages_number    = 5;
	}
	function set_branches($table_name, $id_field, $name_field, $recursion_field = null, $where = null, $group = null, $order = null){
		$this->branches_table_name      = $table_name;
		$this->branches_id_field        = $id_field;
		$this->branches_name_field      = $name_field; 
		$this->branches_recursion_field = $recursion_field;
		
		if ($where && is_array($where)) {
			$where = implode(', ', $where);
		}
		if ($order && is_array($order)) {
			$order = implode(', ', $order);
		}
		if ($group && is_array($group)) {
			$group = implode(', ', $group);
		}
		$this->branches_where = $where;
		$this->branches_order = $order;
		$this->branches_group = $group;
	}
	function set_topbranches($table_name, $id_field, $name_field){
		$this->topbranches_table_name      = $table_name;
		$this->topbranches_id_field        = $id_field;
		$this->topbranches_name_field      = $name_field; 
	}
	function set_leaves($table_name, $id_field, $name_field, $external_table_name=null, $where = null, $order = null ){
		$this->leaves_table_name          = $table_name;
		$this->leaves_id_field            = $id_field;
		$this->leaves_name_field          = $name_field; 
		$this->leaves_external_table_name = $external_table_name;
		
		if ($where && is_array($where)) {
			$where = implode(', ', $where);
		}
		if ($order && is_array($order)) {
			$order = implode(', ', $order);
		}
		$this->leaves_where = $where;
		$this->leaves_order = $order;
	}
	function set_actions($action_object_id, $action_object_type = 'select', $leaf_action_type = 'leaftostock') {
		$this->action_object_id   = $action_object_id;
		$this->action_object_type = $action_object_type;
		$this->leaf_action_type   = $leaf_action_type;		
	}
	function get_branches_sql ($root_id){
		global $db, $table_prefix;
		
		$sql  = " SELECT b." . $this->branches_id_field . ", b." .  $this->branches_name_field;
		if ($this->branches_recursion_field) {
			$sql .= ", COUNT(bb." . $this->branches_id_field . ") AS branches_count ";
		}
		$sql .= " FROM ";
		if ($this->branches_recursion_field) {
			$sql .= "(";
		}
		$sql .= $table_prefix . $this->branches_table_name . " AS b ";
		if ($this->branches_recursion_field) {
			$sql .= " LEFT JOIN " . $table_prefix . $this->branches_table_name . " AS bb ON bb." . $this->branches_recursion_field . " = b." . $this->branches_id_field . ")";		
			$sql .= " WHERE b." . $this->branches_recursion_field . "=" . $db->tosql($root_id,INTEGER, true, false);
			if ($this->branches_where) {
				$sql .= " AND " . $this->branches_where;
			}
					
			$sql .= " GROUP BY ";
			$sql .= " b." . $this->branches_id_field . ", b." .  $this->branches_name_field;
			if ($this->branches_group) {
				 $sql .= ", " . $this->branches_group;
			}
		} elseif ($this->branches_where) {
			$sql .= " WHERE " . $this->branches_where;
		} elseif($this->topbranches_table_name) {
			return "";
		}
	
		$sql .= " ORDER BY ";
		if ($this->branches_order) {
			$sql .= $this->branches_order;
		} else {
			$sql .= " b." . $this->branches_name_field;
		}
		
		return $sql;
	}
	
	function get_topbranches_sql(){
		global $db, $table_prefix;
		
		$sql  = " SELECT tb." . $this->topbranches_id_field . ", tb." .  $this->topbranches_name_field;
		if ($this->branches_table_name) {
			$sql .= ", COUNT(b." . $this->branches_id_field . ") AS branches_count ";
		}
		$sql .= " FROM ";
		if ($this->branches_table_name) {
			$sql .= "(";
		}
		$sql .= $table_prefix . $this->topbranches_table_name . " AS tb ";
		if ($this->branches_table_name) {
			$sql .= " LEFT JOIN " . $table_prefix . $this->branches_table_name . " AS b ON b." . $this->topbranches_id_field . " = tb." . $this->topbranches_id_field . ")";			
			$sql .= " GROUP BY tb." . $this->topbranches_id_field . ", tb." .  $this->topbranches_name_field;
		}	
		$sql .= " ORDER BY tb." . $this->topbranches_name_field;
		return $sql;
	}
	function get_leaves_sql ($root_id, &$ids){
		global $db, $table_prefix;
		
		$sql  = " SELECT lv." . $this->leaves_id_field . " FROM ";
		if ($this->leaves_external_table_name) {
			$sql .= "(";
		}
		$sql .= $this->leaves_before_join . $this->leaves_before_join . $table_prefix . $this->leaves_table_name . " lv ";
		if ($this->leaves_external_table_name) {
			$sql .= " LEFT JOIN " . $table_prefix . $this->leaves_external_table_name . " AS b ON b." . $this->leaves_id_field . " = lv." . $this->leaves_id_field . ")";
		}
		$sql .= $this->leaves_after_join; 
		if ($this->leaves_external_table_name) {
			$sql .= " WHERE b." . $this->branches_id_field . "=" . $db->tosql($root_id,INTEGER, true, false);
		} else {
			$sql .= " WHERE lv." . $this->branches_id_field . "=" . $db->tosql($root_id,INTEGER, true, false);
		}		
		if ($this->leaves_where) {
			$sql .= " AND " . $this->leaves_where;
		}		
		$ids = array();
		$db->query($sql);		
		while($db->next_record()) {	
			$ids[] = $db->f($this->leaves_id_field);
		}
		if ($ids) {
			$sql  = " SELECT lv." . $this->leaves_id_field . ", lv." .  $this->leaves_name_field;
			$sql .= " FROM " . $table_prefix . $this->leaves_table_name . " lv ";
			$sql .= " WHERE " . $this->leaves_id_field . " IN (" . $db->tosql($ids, INTEGERS_LIST) . ")";
			$sql .= " ORDER BY ";
			if ($this->leaves_order) {
				$sql .= $this->leaves_order;
			} else {
				$sql .= " lv." . $this->leaves_name_field;
			}
		} else {
			return false;
		}
		return $sql;
	}
	function main($tree_name, $response_url, $root_id, $full = false, $marked = null){
		global $db, $table_prefix, $t;
		global $db_type, $db_name, $db_host, $db_port, $db_user, $db_password, $db_persistent;
		
		$db2 = new VA_SQL();
		$db2->DBType      = $db_type;
		$db2->DBDatabase  = $db_name;
		$db2->DBHost      = $db_host;
		$db2->DBPort      = $db_port;
		$db2->DBUser      = $db_user;
		$db2->DBPassword  = $db_password;
		$db2->DBPersistent= $db_persistent;
		
		$this->t->set_var("tree_name", $tree_name);	
		$this->t->set_var("response_url", $response_url);
		
		$show_leafes = true;
		if ($this->topbranches_table_name) {
			if(strpos($root_id, "tb") === 0) {
				$root_id = substr($root_id, 2);
				$this->branches_where = $this->topbranches_id_field . "=" . $db->tosql($root_id, INTEGER);
				$show_leafes = false;
			} elseif ($root_id === 0) {
				$db->query($this->get_topbranches_sql());
				while($db->next_record()) {			
					$id    = $db->f($this->topbranches_id_field);
					$name  = get_translation($db->f($this->topbranches_name_field));
					$branches_count = $db->f("branches_count");						
					$this->t->set_var("id", "tb" . $id);
					$this->t->set_var("name", str_replace(array("\"", "'"), "&quot;", $name));	
					$this->t->set_var("branches_count", $branches_count);
					$this->t->set_var("leaves_count", "?");
					$this->t->parse("branch", true);	
				}
				$this->branches_where = $this->topbranches_id_field . "=" . $db->tosql($root_id, INTEGER);
				$show_leafes = false;
			}
		}
		
		$sql = $this->get_branches_sql($root_id);
		if ($sql) {
			$db->query($sql);
			
			while($db->next_record()) {			
				$id    = $db->f($this->branches_id_field);
				$name  = get_translation($db->f($this->branches_name_field));
				if ($this->branches_recursion_field) {
					$branches_count = $db->f("branches_count");
				} else {
					$branches_count = '';
				}
				
				$sql = " SELECT COUNT(" . $this->leaves_id_field . ") ";
				if ($this->leaves_external_table_name) {
					$sql .= " FROM " . $table_prefix . $this->leaves_external_table_name;							
				} else if ($this->leaves_table_name) {
					$sql .= " FROM " . $table_prefix . $this->leaves_table_name;	
				}
				$sql .= " WHERE " . $this->branches_id_field . " = " . $db->tosql($id, INTEGER, false);
				$sql .= " GROUP BY " . $this->branches_id_field;		
				$db2->query($sql);
				if ($db2->next_record()) {
					$leaves_count = $db2->f(0);
				} else {
					$leaves_count = '';
				}
					
				$this->t->set_var("id", $id);
				$this->t->set_var("name", str_replace(array("\"", "'"), "&quot;", $name));	
				$this->t->set_var("branches_count", $branches_count);
				$this->t->set_var("leaves_count", $leaves_count);			
				$this->t->parse("branch", true);	
			}
		}	

		if ($show_leafes) {
			$leafes_ids = array();
			$sql = $this->get_leaves_sql($root_id, $leafes_ids);
			if ($sql) {
				if (!$t) {
					$t = new VA_Template($this->template_path);
				}
				$n = new VA_Navigator($this->template_path, "ajax_list_tree_navigator.html", "admin_items_list.php");
				$n->t->set_var("id",           $root_id);
				$n->t->set_var("tree_name",    $tree_name);
				$n->t->set_var("response_url", $response_url);				
				$page_number = $n->set_navigator("ajax_list_tree_navigator", "page_number", MOVING, $this->navigator_pages_number, $this->navigator_per_page, count($leafes_ids), false);
				$this->t->set_var("navigator", $t->get_var("ajax_list_tree_navigator"));
				$db->RecordsPerPage = $this->navigator_per_page;
				$db->PageNumber     = $page_number;
				$db->query($sql);
				while($db->next_record()) {
					$id    = $db->f($this->leaves_id_field);
					$name  = get_translation($db->f($this->leaves_name_field));
					$this->t->set_var("id", $id);
					$this->t->set_var("name", str_replace(array("\"", "'"), "&quot;", $name));
					if($id != $marked) {
						$this->t->parse($this->leaf_action_type."_action", false);
						$this->t->set_var("current", '');
					} else {
						$this->t->set_var($this->leaf_action_type."_action", '');
						$this->t->parse("current", false);
					}
					$this->t->parse("leaf", true);
				}
				
				$this->t->set_var("id", $root_id);				
			}
		}
		$this->t->set_var("action_object_id", $this->action_object_id);
		$this->t->set_var("action_object_type", $this->action_object_type);
		if ($full) {	
			$this->t->parse("treeinit_block");
			$this->t->parse($this->leaf_action_type."_block");	
		}
		$this->t->parse("tree", false);	
	}
	function parse_root_tree($tree_name, $response_url, $root_id = 0, $marked  = null){
		global $t;
		$this->main($tree_name, $response_url, $root_id, true, $marked );
		$t->set_var($tree_name, "<div class='tree_outer' name='" . $tree_name . "_branch_0' id='" . $tree_name . "_branch_0' >" . $this->t->get_var("tree") . "</div>");
		$t->sparse($tree_name . "_block", false);
	}
	function ajax_listen($tree_name, $response_url, $marked = null){
		if (!(isset($_SERVER["CONTENT_TYPE"]) && $_SERVER["CONTENT_TYPE"]=='application/ajax+html'))
			return 0;
			
		header("Pragma: no-cache");
		header("Expires: 0");
		header("Cache-Control: no-cache, must-revalidate");
		header("Content-Type: text/html; charset=" . CHARSET);
	
		$branch_id = get_param('branch_id');
		$this->main($tree_name, $response_url, $branch_id, false, $marked);
		echo $this->t->get_var("tree");
		exit;
	}
	function parse_plain($tree_name, $marked = null){
		global $t, $db, $table_prefix;
	
		$this->t->set_var("tree_name", $tree_name);	
		
		if ($this->leaves_after_join) {
			$sql  = " SELECT lv." . $this->leaves_id_field;
			$sql .= " FROM ";			
			$sql .= $this->leaves_before_join;
			$sql .= $table_prefix . $this->leaves_table_name . " lv ";
			$sql .= $this->leaves_after_join;			
			if ($this->leaves_where) {
				$sql .= " WHERE " . $this->leaves_where;
			}			
			$ids = array();
			$db->query($sql);		
			while($db->next_record()) {	
				$ids[] = $db->f($this->leaves_id_field);
			}
			
			if ($ids) {
				$sql  = " SELECT lv." . $this->leaves_id_field . ", lv." .  $this->leaves_name_field;
				$sql .= " FROM " . $table_prefix . $this->leaves_table_name . " lv ";
				$sql .= " WHERE " . $this->leaves_id_field . " IN (" . $db->tosql($ids, INTEGERS_LIST) . ")";				
				$sql .= " ORDER BY ";
				if ($this->leaves_order) {
					$sql .= $this->leaves_order;
				} else {
					$sql .= " lv." . $this->leaves_name_field;
				}				
			} else {
				$sql = false;
			}			
		} else {
			$sql  = " SELECT lv." . $this->leaves_id_field . ", lv." .  $this->leaves_name_field;
			$sql .= " FROM " . $table_prefix . $this->leaves_table_name . " lv ";			
			if ($this->leaves_where) {
				$sql .= " WHERE " . $this->leaves_where;
			}
			$sql .= " ORDER BY ";
			if ($this->leaves_order) {
				$sql .= $this->leaves_order;
			} else {
				$sql .= " lv." . $this->leaves_name_field;
			}
		}
		
		if ($sql) {
			$db->query($sql);
			while($db->next_record()) {
				$id    = $db->f($this->leaves_id_field);
				$name  = get_translation($db->f($this->leaves_name_field));
				$this->t->set_var("id", $id);
				$this->t->set_var("name", str_replace(array("\"", "'"), "&quot;", $name));	
				if($id != $marked) {
					$this->t->parse($this->leaf_action_type."_action", false);
					$this->t->set_var("current", '');
				} else {
					$this->t->set_var($this->leaf_action_type."_action", '');
					$this->t->parse("current", false);
				}
				$this->t->parse("leaf", true);	
			}
		}
		$this->t->set_var("action_object_id", $this->action_object_id);
		$this->t->set_var("action_object_type", $this->action_object_type);
		$this->t->parse("treeinit_block");
		$this->t->parse($this->leaf_action_type."_block");
		$this->t->parse("tree", false);	
		$t->set_var($tree_name, "<div class='tree_outer' name='" . $tree_name . "_branch_0' id='" . $tree_name . "_branch_0' >" . $this->t->get_var("tree") . "</div>");
		$t->sparse($tree_name . "_block", false);
	}
}
?>