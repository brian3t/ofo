<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  sql_functions.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	class VA_Model {
		
		var $__db;
		var $__tablename;
		var $__orderby;
		var $__settings;
		
		function VA_Model($model_db = null, $build_setting = true) {
			global $db;
			if ($model_db === false) {
				$this->__db = false;
			} elseif (!$model_db) {
				$this->__db = new VA_SQL();
				$this->__db->DBType       = $db->DBType;
				$this->__db->DBDatabase   = $db->DBDatabase;
				$this->__db->DBUser       = $db->DBUser;
				$this->__db->DBPassword   = $db->DBPassword;
				$this->__db->DBHost       = $db->DBHost;
				$this->__db->DBPort       = $db->DBPort;
				$this->__db->DBPersistent = $db->DBPersistent;
			} else {
				$this->__db = $model_db;
			}
			if ($build_setting) {
				$this->__getSettings();
			}
			$this->__onInit();
		}
		function __findSQL() {
			global $table_prefix;
			$sql  = " SELECT * ";
			$sql .= " FROM " . $table_prefix . $this->__tablename;
			
			$object_vars = get_object_vars($this);
			$where = "";
			foreach ($this AS $key => $var) {
				if (strpos($key, "__") === 0 ) {
					continue;
				} elseif (is_array($var) && count($var)) {
					if ($where) $where .= " AND ";
					$where .= " " . $key . " IN (" . $this->__db->tosql($var, INTEGERS_LIST) . ")";
				} elseif (strlen($var)) {
					if ($where) $where .= " AND ";
					$where .= " " . $key . "=" . $this->__db->tosql($var, TEXT);
				}
			}
			if (strlen($where)) {
				$sql .= " WHERE " . $where;
			}
			if (strlen($this->__orderby)) {
				$sql .= " ORDER BY " . $this->__orderby;
			}
			return $sql;
		}
		function &__getOne() {
			$classname   = get_class($this);
			$object      = new $classname(false, false);			
			$object_vars = get_object_vars($this);
			foreach ($this AS $key => $var) {
				if (strpos($key, "__") === 0 ) {
					continue;
				} else {
					$object->$key = $this->__db->f($key);
				}
			}
			$object->__settings = &$this->__settings;
			$this->__onGet($object);
			return $object;
		}
		function findOne() {
			$this->__db->PageNumber     = 1;
			$this->__db->RecordsPerPage = 1;
			$this->__db->query($this->__findSQL());
			if ($this->__db->next_record()) {
				return $this->__getOne();
			} else {
				return false;
			}
		}
		function findAll() {
			$this->__db->query($this->_findSQL());
			$return = array();
			while ($this->__db->next_record()) {
				$return[] = $this->__getOne();
			}
			return $return;	
		}
		function showAll($block_name) {
			global $t;
			
			$t->set_var($block_name, "");
			$this->__db->query($this->__findSQL());
			$index = 0;
			while ($this->__db->next_record()) {
				$index ++;
				$object = $this->__getOne();
				$object->showOne($block_name, $index);				
			}
			return $index;
		}
		function showOne($block_name, $index = 0) {
			print_r($this);	
		}
		function __onInit() {}
		function __getSettings() {}
		function __onGet(&$object){}
	}
?>