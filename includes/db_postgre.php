<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  db_postgre.php                                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


class VA_SQL 
{
	var $DBHost         = "";
	var $DBPort         = "";
	var $DBDatabase     = "";
	var $DBUser         = "";
	var $DBPassword     = "";
	var $DBPersistent   = false;
	
	/* 
	dates formats 
	*/
	var $DatetimeMask   = array("YYYY", "-", "MM", "-", "DD", " ", "HH", ":", "mm", ":", "ss", "GMT");
	var $DateMask       = array("YYYY", "-", "MM", "-", "DD");
	var $TimeMask       = array("HH", ":", "mm", ":", "ss");
	var $TimestampMask  = array("YYYY", "MM", "DD", "HH", "mm", "ss");
	
	var $AutoFree       = 0; 
	var $LinkID         = 0;
	var $QueryID        = 0;
	var $Offset         = 0;
	var $PageNumber     = 0;
	var $RecordsPerPage = 0;
	var $Record         = array();
	var $Row            = 0;
	
	var $Errno       = 0;
	var $Error       = "";
	var $HaltOnError = "yes"; // "yes", "no", "report"

	/* 
	public: constructor 
	*/
	function VA_SQL($query = "") {
		$this->RecordsPerPage = 0;
		$this->query($query);
	}

	function check_lib() {
		return function_exists("pg_connect");
	}

	function connect() {
		if (!$this->LinkID) {		
			$conn  = "dbname=" . $this->DBDatabase;
			$conn .= ($this->DBHost ? " host=" . $this->DBHost : "");
			$conn .= ($this->DBPort ? " port=" . $this->DBPort : "");
			$conn .= ($this->DBUser ? " user=" . $this->DBUser : "");
			$conn .= ($this->DBPassword ? " password=" . $this->DBPassword : "");
			
			if ($this->DBPersistent) {
				$this->LinkID = @pg_pconnect($conn);
			} else {
				$this->LinkID = @pg_connect($conn);
			}
			
			if (!is_resource($this->LinkID)) {
				$this->Error = "Unable to connect to PostgreSQL server";
				$this->Errno = 1;
				$this->halt("Connection failed: " . $this->describe_error($this->Errno, $this->Error));
				return 0;
			}
		}
		return $this->LinkID;
	}
	
	function query($query_string) {
		if ($query_string == "") {
			return 0;
		}
		
		$this->connect();
		
		if ($this->RecordsPerPage && $this->PageNumber) {
			$query_string .= " LIMIT " . $this->RecordsPerPage . " OFFSET " . (($this->PageNumber - 1) * $this->RecordsPerPage);
			$this->RecordsPerPage = 0;
			$this->PageNumber = 0;
		} else if ($this->RecordsPerPage) {
			$query_string .= " LIMIT " . $this->RecordsPerPage . " OFFSET " . $this->Offset;
			$this->Offset = 0;
			$this->RecordsPerPage = 0;
		} 

		
		$this->QueryID = @pg_query($this->LinkID, $query_string);
		$this->Row   = 0;
		
		if (!is_resource($this->LinkID)) {
			$this->Error = "Unable to connect to PostgreSQL server";
			$this->Errno = 1;
		} else {
			$this->Error = pg_last_error($this->LinkID);
			$this->Errno = ($this->Error == "") ? 0 : 1;
		}
		if (!$this->QueryID) {
			$this->halt("Invalid SQL: " . $query_string);
		}
		
		return $this->QueryID;
	}
  
	function next_record() {
		$this->Record = @pg_fetch_array($this->QueryID, $this->Row++);
		
		$this->Error = pg_last_error($this->LinkID);
		$this->Errno = ($this->Error == "") ? 0 : 1;
		
		$stat = is_array($this->Record);
		if (!$stat && $this->AutoFree) {
			$this->free_result();
		}
		return $stat;
	}

	function seek($pos) {
		$this->Row = $pos;
	}

	function lock($table, $mode = "write") {
		if ($mode == "write") {
			$result = pg_query($this->LinkID, "lock table $table");
		} else {
			$result = 1;
		}
		return $result;
	}
  
	function unlock() {
		return pg_query($this->LinkID, "commit");
	}
	
	function affected_rows() {
		return pg_affected_rows($this->QueryID);
	}
	
	function num_rows() {
		return pg_num_rows($this->QueryID);
	}
	
	function num_fields() {
		return pg_num_fields($this->QueryID);
	}

	function f($Name, $field_type = TEXT) {
		if (isset($this->Record[$Name]))	{
			$value = $this->Record[$Name];
			switch ($field_type) {
				case DATETIME:
					$value = parse_date($value, $this->DatetimeMask, $date_errors);
					break;
				case DATE:
					$value = parse_date($value, $this->DateMask, $date_errors);
					break;
				case TIME:
					$value = parse_date($value, $this->TimeMask, $date_errors);
					break;
			}
			return $value; 
		} else {
			return "";
		}
	}

	function free_result() {
		@pg_free_result($this->QueryID);
		$this->QueryID = 0;
	}

	function close() {
		if ($this->QueryID) {
			$this->free_result();
		}
		if ($this->LinkID != 0 && !$this->DBPersistent) {
			pg_close($this->LinkID);
			$this->LinkID = 0;
		}
	}  

  
	function halt($message) {
		if (!$this->Error) { $this->Error = $message; }
		
		if ($this->HaltOnError == "no") { return; }
		
		$eol = get_eol();
		print "<b>Database error:</b> " . $message . "<br>" . $eol;
		print "<b>PostgreSQL Error Message:</b> " . $this->Error . "<br>" . $eol;
		
		if ($this->HaltOnError != "report") {
			die("Session halted.");
		}
	}

	function tosql($value, $value_type, $is_delimiters = true, $use_null = true) {
		if (is_array($value) || strlen($value)) {
			switch ($value_type) {
				case NUMBER:
				case FLOAT:
					return preg_replace(array("/,/", "/[^0-9\.,\-]/"), array(".", ""), $value);
					break;
				case TEXT:
					$value = addslashes($value);
					break;
				case DATETIME:
					if (!is_array($value) && is_int($value)) { $value = va_time($value); }
					if (is_array($value)) { $value = va_date($this->DatetimeMask, $value); }
					else { return "NULL"; }
					break;
				case INTEGER:
					return intval($value);
					break;
				case DATE:
					if (!is_array($value) && is_int($value)) { $value = va_time($value); }
					if (is_array($value)) { $value = va_date($this->DateMask, $value); }
					else { return "NULL"; }
					break;
				case TIME:
					if (!is_array($value) && is_int($value)) { $value = va_time($value); }
					if (is_array($value)) { $value = va_date($this->TimeMask, $value); }
					else { return "NULL"; }
					break;
				case TIMESTAMP:
					if (!is_array($value) && is_int($value)) { $value = va_time($value); }
					if (is_array($value)) { $value = va_date($this->TimestampMask, $value); }
					else { return "NULL"; }
					break;
				case NUMBERS_LIST:
				case FLOATS_LIST:
					$values = (is_array($value)) ? $value : explode(",", $value);
					for ($v = 0; $v < sizeof($values); $v++) {
						$value = $values[$v];
						$value = preg_replace(array("/,/", "/[^0-9\.,\-]/"), array(".", ""), $value);
						if (!is_numeric($value)) {
							$value = 0;
						}
						$values[$v] = $value;
					}
					return implode(",", $values);
					break;
				case INTEGERS_LIST:
					$values = (is_array($value)) ? $value : explode(",", $value);
					for ($v = 0; $v < sizeof($values); $v++) {
						$values[$v] = intval($values[$v]);
					}
					return implode(",", $values);
					break;
				default:
					$value = addslashes($value);
					break;
			}
			if ($is_delimiters) {
				$value = "'" . $value . "'";
			}
		} elseif ($use_null) {
			$value = "NULL";
		} else {
			if ($value_type == INTEGER || $value_type == FLOAT || $value_type == NUMBER) {
				$value = 0;
			} elseif ($is_delimiters) {
				$value = "''";
			}
		} 
		return $value;
	}

	function describe_error($error_code, $error_msg) {
		$error_desc = "";
		switch ($error_code) {
			default:
				$error_desc = $error_msg;
		}
		return $error_desc;
	}

	function get_fields($table_name)
	{

		$sql  = " SELECT c.oid";
		$sql .= " FROM pg_catalog.pg_class c";
		$sql .= " WHERE c.relname ~ '^" . $table_name . "$'";
		$this->query($sql);
		if ($this->next_record()){
			$table_id = $this->f('oid');
		}
		$index_name = array();
		$sql  = " SELECT c2.relname, pg_catalog.pg_get_indexdef(i.indexrelid, 0, true) as index ";
		$sql .= " FROM pg_catalog.pg_class c, pg_catalog.pg_class c2, pg_catalog.pg_index i ";
		$sql .= " WHERE c.oid = '" . $table_id . "' AND c.oid = i.indrelid AND i.indexrelid = c2.oid";
		$this->query($sql);
		while ($this->next_record()) {
			if (preg_match("/^CREATE UNIQUE INDEX/i", $this->f('index'))) {
				preg_match("/\((.*)\)/is", $this->f('index'), $index_value);
				$field_name = split(",", $index_value[1]);
				foreach ($field_name as $key => $value) {
					$index_name[trim($value,' ,"')] = 'PRIMARY KEY';
				}
			} else {
				preg_match('/\((.*)\)/is', $this->f('index'), $index_value);
				$index_name[trim($index_value[1],'"')] = $this->f('relname');
			}
		}
		$fields = array();
		$meta = pg_meta_data($this->LinkID, $table_name);
		$this->Error = pg_last_error($this->LinkID);
		$this->Errno = ($this->Error == "") ? 0 : 1;
		foreach ($meta as $key => $value) {
			$name = $key;
			$type = strtoupper($value['type']);
			$null = ($value['not null'])? false: true;
			$auto_increment = false;
			$default = '';
			$sql  = " SELECT a.attname, pg_catalog.format_type(a.atttypid, a.atttypmod) as type,";
			$sql .= " (";
			$sql .= " SELECT pg_catalog.pg_get_expr(d.adbin, d.adrelid)";
			$sql .= " FROM pg_catalog.pg_attrdef d ";
			$sql .= " WHERE d.adrelid = a.attrelid AND d.adnum = a.attnum AND a.atthasdef";
			$sql .= " ) as default";
			$sql .= " FROM pg_catalog.pg_attribute a";
			$sql .= " WHERE a.attrelid = '" . $table_id . "' AND a.attname = '" . $name . "' AND a.attnum > 0 AND NOT a.attisdropped";
			$this->query($sql);
			if ($this->next_record()){
				if (preg_match("/^nextval/i", $this->f('default'))){
					$auto_increment = true;
				} elseif (preg_match("/^(.*)\::/is", $this->f('default'), $default_res)) {
						$default = $default_res[1];
				} else {
					$default = $this->f('default');
				}
				if ($type == 'VARCHAR') {
					if (preg_match("/\((.*)\)/is", $this->f('type'), $t_size)) {
						$type .= $t_size[0];
					}
				}
			}
			$primary = false;
			$index = '';
			if (isset($index_name[$name])){
				if ($index_name[$name] == 'PRIMARY KEY'){
					$primary = true;
				} else {
					$index = $index_name[$name];
				}
			}
			$field = array('name' => $name, 'type' => $type, 'null' => $null, 'primary' => $primary, 'auto_increment' => $auto_increment, 'default' => $default, 'index' => $index);
			$fields[] = $field;
		}

		return $fields;
	}

	function get_tables($perametr = 'ALL')
	{
		$tables = array();
		$sql  = "SELECT c.relname FROM pg_catalog.pg_class c";
		$sql .= " LEFT JOIN pg_catalog.pg_namespace n ON n.oid = c.relnamespace";
		$sql .= " WHERE c.relkind IN ('r','')";
		$sql .= " AND n.nspname NOT IN ('pg_catalog', 'pg_toast')";
		$sql .= " AND pg_catalog.pg_table_is_visible(c.oid)";
		$this->query($sql);
		while ($this->next_record()){
			$tables[] = $this->f(0);
		}
		return $tables;		
	}

	function create_database($db_name = "")
	{
		$resource_id = 0;
		if (strlen($db_name) == 0) {
			$db_name = $this->DBDatabase;
		}

		$conn  = "dbname=template1";
		$conn .= ($this->DBHost ? " host=" . $this->DBHost : "");
		$conn .= ($this->DBPort ? " port=" . $this->DBPort : "");
		$conn .= ($this->DBUser ? " user=" . $this->DBUser : "");
		$conn .= ($this->DBPassword ? " password=" . $this->DBPassword : "");
		
		if ($this->DBPersistent) {
			$resource_id = @pg_pconnect($conn);
		} else {
			$resource_id = @pg_connect($conn);
		}
		
		if (!is_resource($resource_id)) {
			$this->Error = "Unable to connect to PostgreSQL server";
			$this->Errno = 1;
			$this->halt("Connection failed: " . $this->describe_error($this->Errno, $this->Error));
			return 0;
		} else {
			if (@pg_query($resource_id, "CREATE DATABASE \"$db_name\"")) {
				return 1;
			} else {
				$this->Error = pg_last_error($resource_id);
				$this->Errno = ($this->Error == "") ? 0 : 1;
				$this->halt("Can't create database " . $db_name);
				return 0;
			}
		}
	}
}

?>