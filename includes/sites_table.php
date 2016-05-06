<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  sites_table.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	class VA_Sites_Table {
		var $t;
		var $table_name, $sites_table_name, $field_name, $field_value, $recursion_field;
		
		var $available_sites, $selected_sites;
		var $sites_all = 1;		
		var $field_id = "sites";
		
		var $message = USE_CATEGORY_ALL_SITES_MSG;
		
		function VA_Sites_Table($template_path, $filename, $field_id = ""){
			$this->t = new VA_Template($template_path);
			$this->t->set_file("sites_table", $filename);
			if ($field_id) $this->field_id = $field_id;
			$this->t->set_var("field_id", $this->field_id);		
		}
		
		function set_tables($table_name, $sites_table_name, $field_name, $recursion_field, $field_value, $sql = false) {
			global $db, $table_prefix;
			
			$this->table_name       = $table_name;
			$this->sites_table_name = $sites_table_name;
			
			$this->field_name  = $field_name;
			$this->field_value = $field_value;
			$this->recursion_field = $recursion_field;			
						
			$this->available_sites = array();
			if (!$sql) {
				$sql = " SELECT site_id, site_name FROM " . $table_prefix . "sites ";
			}
			$db->query($sql);
			while ($db->next_record())	{
				$site_id   = $db->f("site_id");
				$site_name = $db->f("site_name");
				$this->available_sites[$site_id] = $site_name;
			}
			
			$operation = get_param("operation");			
			$sites     = get_param("sites");			
			$this->selected_sites = array();
			$sql = "";		
			if ($operation) {
				if ($sites) {
					$sql  = " SELECT site_id FROM " . $table_prefix . "sites";
					$sql .= " WHERE site_id IN (" . $db->tosql($sites, INTEGERS_LIST) . ")";
				}						
			} else {
				$sql  = " SELECT site_id FROM " . $table_prefix . $this->sites_table_name;
				$sql .= " WHERE " . $this->field_name . "=" . $db->tosql($this->field_value, INTEGER);
			}
			if ($sql) {
				$db->query($sql);
				while ($db->next_record()) {
					$this->selected_sites[] = $db->f("site_id");
				}
			}
		}
		
		function parse($var, $sites_all = 1){
			global $t, $db, $table_prefix;
			
			$this->sites_all = $sites_all;
			if ($this->sites_all) {
				$this->t->set_var("sites_all", "checked");
			} else {
				$this->t->set_var("sites_all", "");
			}
			if ($this->available_sites) {
				foreach ($this->available_sites AS $site_id => $site_name) {
					$this->t->set_var("site_id", $site_id);
					$this->t->set_var("site_name", $site_name);
					if ($this->selected_sites && in_array($site_id, $this->selected_sites)) {
						$this->t->parse("selected_sites", true);
					} else {
						$this->t->parse("available_sites", true);
					}
				}
			}
			$this->t->set_var("message", $this->message);
			$this->t->parse("table");
			$t->set_var($var, $this->t->get_var("table"));
			$t->sparse($var . "_block", false);
		}
		
		function save_values_recursive($field_value) {
			global $db, $table_prefix;			
			$nested = array();							
			$sql  = " SELECT " . $this->field_name;
			$sql .= " FROM "   . $table_prefix . 	$this->table_name;
			$sql .= " WHERE "  . $this->recursion_field . " LIKE '%," . $db->tosql($field_value, INTEGER, false, false) . ",%'";			
			$db->query($sql);
			while ($db->next_record()) {
				$nested[] = $db->f($this->field_name);
			}

			if ($nested) {
				$sql  = " DELETE FROM " . $table_prefix . $this->sites_table_name;
				$sql .= " WHERE " . $this->field_name . " IN (" . $db->tosql($nested, INTEGERS_LIST) . ")";
				$db->query($sql);
				
				$sql  = " UPDATE " . $table_prefix .  $this->table_name;
				$sql .= " SET sites_all= " . $db->tosql($this->sites_all, INTEGER, true, false);
				$sql .= " WHERE " . $this->field_name . " IN (" . $db->tosql($nested, INTEGERS_LIST) . ")";
				$db->query($sql);
					
				foreach ($nested AS $field_value) {				
					foreach ($this->selected_sites AS $site_id) {
						$sql  = " INSERT INTO " . $table_prefix . $this->sites_table_name;
						$sql .=  " (" . $this->field_name . ", site_id) VALUES (";
						$sql .= $db->tosql($field_value, INTEGER, true, false) . ", ";
						$sql .= $db->tosql($site_id, INTEGER) . ") ";
						$db->query($sql);
					}
				}
			}
		}
		
		function save_values($field_value = null, $sites_all = 1, $recursive = false) {
			global $db, $table_prefix;
			
			$this->sites_all = $sites_all;
			if ($field_value) $this->field_value = $field_value;			
			
			$sql  = " DELETE FROM " . $table_prefix . $this->sites_table_name;
			$sql .= " WHERE " . $this->field_name . "=" . $db->tosql($this->field_value, INTEGER);
			$db->query($sql);
			array_unique($this->selected_sites);
			foreach ($this->selected_sites AS $site_id) {
				$sql  = " INSERT INTO " . $table_prefix . $this->sites_table_name;
				$sql .=  " (" . $this->field_name . ", site_id) VALUES (";
				$sql .= $db->tosql($this->field_value, INTEGER, true, false) . ", ";
				$sql .= $db->tosql($site_id, INTEGER) . ") ";
				$db->query($sql);
			}
			
			if ($recursive && $this->recursion_field) {				
				$this->save_values_recursive($this->field_value);
			}			
		}
		
		function save_array_values($array, $sites_all = 1) {
			global $db, $table_prefix;
			if (!$array) return false;
			$this->sites_all = $sites_all;
			
			$sql  = " UPDATE " . $table_prefix . $this->table_name;
			$sql .= " SET sites_all = " . $db->tosql($this->sites_all, INTEGER);
			$sql .= " WHERE " . $this->field_name . " IN (" . $db->tosql($array, INTEGERS_LIST) . ")";
			$db->query($sql);
			
			$sql  = " DELETE FROM " . $table_prefix . $this->sites_table_name;
			$sql .= " WHERE " . $this->field_name . " IN (" . $db->tosql($array, INTEGERS_LIST) . ")";
			$db->query($sql);			
			
			array_unique($this->selected_sites);
			array_unique($array);
			foreach ($array AS $value) {
				foreach ($this->selected_sites AS $site_id) {
					$sql  = " INSERT INTO " . $table_prefix . $this->sites_table_name;
					$sql .=  " (" . $this->field_name . ", site_id) VALUES (";
					$sql .= $db->tosql($value, INTEGER, true, false) . ", ";
					$sql .= $db->tosql($site_id, INTEGER) . ") ";
					$db->query($sql);
				}
			}			
		}
	}
?>