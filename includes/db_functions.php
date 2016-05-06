<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  db_functions.php                                         ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


function get_table_structure($table_name, $db_type)
{
	global $db, $db_lib;
	$sql_create = '';
	switch ($db_type) { // mysql | postgre | access
	case 'mysql':
		$sql_create  = 'DROP TABLE IF EXISTS `' . $table_name . "`;\n";
		$sql_create .= 'CREATE TABLE `' . $table_name . "`(\n";
		$sql_create_key = '';
		$sql_create_index = '';
		$fields = array();
		$fields = $db->get_fields($table_name);
		foreach ($fields as $key => $field_value) {
			if ($db_type == $db_lib){
				$field_type = $field_value['type'];
			} else {
				if (preg_match("/INT/i", $field_value['type']) || preg_match("/COUNTER/i", $field_value['type'])){
					$field_type = 'INT(11)';
				} elseif (preg_match("/DOUBLE/i", $field_value['type']) || preg_match("/FLOAT/i", $field_value['type'])){
					$field_type = 'DOUBLE(16,2)';
				} elseif (preg_match("/TIME/i", $field_value['type'])){
					$field_type = 'DATETIME';
				} elseif (preg_match("/VARCHAR/i", $field_value['type'])){
					$field_type = $field_value['type'];
				} else {
					$field_type = 'TEXT';
				}
			}
			$sql_create .= ' `' . $field_value['name'] . '` ' . $field_type;
			if (!$field_value['null']){
				$sql_create .= ' NOT NULL';
			}
			if ($field_value['auto_increment']){
				$sql_create .= ' AUTO_INCREMENT';
			}
			if (strlen($field_value['default'])){
				$sql_create .= " default '" . $field_value['default'] . "'";
			}
			$sql_create .= ((sizeof($fields)-1) != $key) ? ",\n" : "\n";
			if ($field_value['primary']){
				$sql_create_key .= (strlen($sql_create_key)) ? " ," . $field_value['name'] : $field_value['name'];
			}
			if ($field_value['index']){
				$sql_create_index .= "  ,KEY " . $field_value['name'] . " (" . $field_value['name'] . ")\n";
			}
		}
		if (strlen($sql_create_key)){
			$sql_create_key = "  ,PRIMARY KEY (" . $sql_create_key . ")\n";
		}
		$sql_create .= $sql_create_key . $sql_create_index . ");\n";
		break;
	case 'postgre':
		$sql_create  = 'DROP SEQUENCE seq_' . $table_name . ";\n";
		$sql_create .= 'DROP TABLE ' . $table_name . ";\n";
		$sql_create .= 'CREATE SEQUENCE seq_' . $table_name . " START 2;\n";
		$sql_create .= 'CREATE TABLE ' . $table_name . "(\n";
		$sql_create_key = '';
		$sql_create_index = '';
		$all_index_name = array();
		$fields = array();
		$fields = $db->get_fields($table_name);
		foreach ($fields as $key => $field_value) {
			if (preg_match("/INT/i", $field_value['type']) || preg_match("/COUNTER/i", $field_value['type'])){
				$field_type = 'INT4';
			} elseif (preg_match("/DOUBLE/i", $field_value['type']) || preg_match("/FLOAT/i", $field_value['type'])){
				$field_type = 'FLOAT4';
			} elseif (preg_match("/TIME/i", $field_value['type'])){
				$field_type = 'TIMESTAMP';
			} elseif (preg_match("/VARCHAR/i", $field_value['type'])){
				$field_type = $field_value['type'];
			} else {
				$field_type = 'TEXT';
			}
			$sql_create .= ' ' . $field_value['name'] . ' ' . $field_type;
			if (!$field_value['null']){
				$sql_create .= ' NOT NULL';
			}
			if ($field_value['auto_increment']){
				$sql_create .= " DEFAULT nextval('seq_" . $table_name . "'),";
			}
			if (strlen($field_value['default'])){
				$sql_create .= " default '" . $field_value['default'] . "'";
			}
			$sql_create .= ((sizeof($fields)-1) != $key) ? ",\n" : "\n";
			if ($field_value['primary']){
				$sql_create_key .= (strlen($sql_create_key)) ? " ," . $field_value['name'] : $field_value['name'];
			}
			if ($field_value['index']){
				if (is_string($field_value['index'])){
					$index_name = $field_value['index'];
				} else {
					$index_name = $table_name.'_'.$field_value['name'];
				}
				$index_found = false;
				$index_name_count = 1;
				if (strlen($index_name) > 32) {
					$index_name = substr($index_name, 0 , 28) . '_';
					$index_found = true;
				}
				if (!$index_found && in_array($index_name, $all_index_name)){
					$index_found = true;
				}
				while ($index_found) {
					if (!in_array($index_name.$index_name_count, $all_index_name)){
						$index_name = $index_name . $index_name_count;
						$index_found = false;
					}
					$index_name_count++;
				}
				$all_index_name[] = $index_name;
				$sql_create_index .= "CREATE INDEX " . $index_name . " ON " . $table_name . " (" . $field_value['name'] . ");\n";
			}
		}
		if (strlen($sql_create_key)){
			$sql_create_key = "  ,PRIMARY KEY (" . $sql_create_key . ")\n";
		}
		$sql_create .= $sql_create_key . ");\n" . $sql_create_index;
		break;
	case 'access':
		$sql_create  = 'DROP TABLE ' . $table_name . ";\n";
		$sql_create .= 'CREATE TABLE ' . $table_name . "(\n";
		$sql_create_key = '';
		$sql_create_index = '';
		$all_index_name = array();
		$fields = array();
		$fields = $db->get_fields($table_name);
		foreach ($fields as $key => $field_value) {
			if (preg_match("/INT/i", $field_value['type']) || preg_match("/COUNTER/i", $field_value['type'])){
				if ($field_value['auto_increment']) {
					$field_type = 'COUNTER NOT NULL';
				} else {
					$field_type = 'INTEGER';
				}
			} elseif (preg_match("/DOUBLE/i", $field_value['type']) || preg_match("/FLOAT/i", $field_value['type'])){
				$field_type = 'FLOAT';
			} elseif (preg_match("/TIME/i", $field_value['type'])){
				$field_type = 'DATETIME';
			} elseif (preg_match("/VARCHAR/i", $field_value['type'])){
				$field_type = $field_value['type'];
			} else {
				$field_type = 'LONGTEXT';
			}
			$sql_create .= ' [' . $field_value['name'] . '] ' . $field_type;
			if (!$field_value['null'] && !$field_value['auto_increment']){
				$sql_create .= ' NOT NULL';
			}
			$sql_create .= ((sizeof($fields)-1) != $key) ? ",\n" : "\n";
			if ($field_value['primary']){
				$sql_create_key .= (strlen($sql_create_key)) ? " ," . $field_value['name'] : $field_value['name'];
			}
			if ($field_value['index']){
				if (is_string($field_value['index'])){
					$index_name = $field_value['index'];
				} else {
					$index_name = $table_name . '_' . $field_value['name'];
				}
				$index_found = false;
				$index_name_count = 1;
				if (strlen($index_name) > 32){
					$index_name = substr($index_name, 0 , 28) . '_';
					$index_found = true;
				}
				if(!$index_found && in_array($index_name, $all_index_name)){
					$index_found = true;
				}
				while ($index_found) {
					if(!in_array($index_name.$index_name_count, $all_index_name)){
						$index_name = $index_name . $index_name_count;
						$index_found = false;
					}
					$index_name_count++;
				}
				$all_index_name[]= $index_name;
				$sql_create_index .= "CREATE INDEX " . $index_name . " ON " . $table_name . " (" . $field_value['name'] . ");\n";
			}
		}
		if (strlen($sql_create_key)){
			$sql_create_key = "  ,PRIMARY KEY (" . $sql_create_key . ")\n";
		}
		$sql_create .= $sql_create_key . ");\n" . $sql_create_index;
		break;
	}
	return $sql_create;
}

?>