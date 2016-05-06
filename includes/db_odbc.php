<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  db_odbc.php                                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


define("ODBC_MAX_READ_LEN", 63536);

class VA_SQL 
{
	var $DBType         = "";
	var $DBHost         = "";
	var $DBPort         = "";
	var $DBDatabase     = "";
	var $DBUser         = "";
	var $DBPassword     = "";
	var $DBPersistent   = false;

	/* 
	dates formats 
	*/
	var $DatetimeMask   = array("YYYY", "-", "MM", "-", "DD", " ", "HH", ":", "mm", ":", "ss");
	var $DateMask       = array("YYYY", "-", "MM", "-", "DD", " ", "HH", ":", "mm", ":", "ss");
	var $TimeMask       = array("HH", ":", "mm", ":", "ss");
	var $TimestampMask  = array("YYYY", "MM", "DD", "HH", "mm", "ss");

	var $UseODBCCursor  =  0;
	var $Uppercase      =  false;
	var $AutoFree       =  0;     
	var $LinkID         =  0;
	var $QueryID        =  0;
	var $Offset         = 0;
	var $PageNumber     =  0;
	var $RecordsPerPage =  0;
	var $RecordsShown   = -1;
	var $Record         =  array();
	var $Row            =  0;

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
		return function_exists("odbc_connect");
	}

	function connect() {
		if (!$this->LinkID) {
			if ($this->DBPersistent) {
				$this->LinkID = @odbc_pconnect($this->DBDatabase, $this->DBUser, $this->DBPassword, $this->UseODBCCursor);
			} else {
				$this->LinkID = @odbc_connect($this->DBDatabase, $this->DBUser, $this->DBPassword, $this->UseODBCCursor);
			}
			
			if (!$this->LinkID) {
				$this->Errno = @odbc_error(); 
				$this->Error = @odbc_errormsg();
				$this->halt("Connect failed: " . $this->describe_error($this->Errno, $this->Error));
				return 0;
			}
		}
		return $this->LinkID;
	}
  
	function query($query_string) {
		if ($query_string == "") {
			return 0;
		}
		
		$this->RecordsShown = -1;
		$this->connect();
		
		$this->QueryID = @odbc_exec($this->LinkID, $query_string);
		$this->Row = 0;
		odbc_binmode($this->QueryID, ODBC_BINMODE_PASSTHRU);
		odbc_longreadlen($this->QueryID, ODBC_MAX_READ_LEN);
		
		if (!$this->QueryID) {
			$this->Errno = odbc_error($this->LinkID); 
			$this->Error = odbc_errormsg($this->LinkID);
			$this->halt("Invalid SQL: " . $query_string);
		} else {
			$this->Errno = 0;
			$this->Error = "";
		}
		if ($this->RecordsPerPage) {
			if ($this->PageNumber) {
				$ShiftRecords = (($this->PageNumber - 1) * $this->RecordsPerPage);
			} else {
				$ShiftRecords = $this->Offset;
			}
			$RecordsPerPage = $this->RecordsPerPage;
			while ($ShiftRecords != 0) {
				$ShiftRecords--;
				$this->next_record();
			}
			$this->RecordsShown = $RecordsPerPage;
			$this->Offset = 0;
			$this->PageNumber = 0;
			$this->RecordsPerPage = 0;
		}
		return $this->QueryID;
	}
  
	function next_record() {
		if ($this->RecordsShown != -1) {
			if ($this->RecordsShown) {
				$this->RecordsShown--;
			} else {
				$this->RecordsShown = -1;
				return false;
			}
		} 
		$this->Record = array();
		$stat = @odbc_fetch_row($this->QueryID);
		if (!$stat) {
			if ($this->AutoFree) {
				$this->free_result();
			}
		} else {
			$this->Row++;
			$count = odbc_num_fields($this->QueryID);
			for ($i = 1; $i <= $count; $i++) {
				$field_value = odbc_result($this->QueryID, $i);
				$fieldname = ($this->Uppercase) ? strtoupper(odbc_field_name($this->QueryID, $i)) : odbc_field_name($this->QueryID, $i);
				$fieldname = strtolower($fieldname);
				$this->Record[$fieldname] = $field_value;
				$this->Record[$i-1] = $field_value;
			}
		}
		return $stat;
	}

	function seek($pos) {
		$i = 0;
		while ($i < $pos && @odbc_fetch_row($this->QueryID)) {
			$i++;
		}
		$this->Row += $i;
	}

	function affected_rows() {
		return odbc_num_rows($this->QueryID);
	}
  
	function num_rows() {
		$num_rows = odbc_num_rows($this->QueryID);
		return $num_rows;
	}
  
	function num_fields() {
		return count($this->Record)/2;
	}

	function f($Name, $field_type = TEXT) {
		if ($this->Uppercase) $Name = strtoupper($Name);
		if (isset($this->Record[$Name])) {
			$value = $this->Record[$Name];
			if ($this->DBType == "db2") {
				$value1 = preg_replace("/^[0-9]{1}[\.\,]{1}[0-9]{14}E[\+\-]{1}[0-9]{3}\$/i", "digit", $value);
				if ($value1 == 'digit'){
					$value_mas = preg_split("/E/",$value);
					$value_mas[0] = preg_replace ("/\,/",".",$value_mas[0]);
					$value = $value_mas[0]*pow(10,$value_mas[1]);
				}
			}
			switch ($field_type)	{
				case DATETIME:
					$value = preg_replace("/\.000000/i","",$value);
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
		@odbc_free_result($this->QueryID);
		$this->QueryID = 0;
	}

	function close() {
		if ($this->QueryID) {
			$this->free_result();
		}
		if ($this->LinkID != 0) {
			odbc_close($this->LinkID);
			$this->LinkID = 0;
		}
	}

	function close_all() {
		odbc_close_all();
	}
  
	function halt($message) {
		global $t, $is_admin_path, $settings;
	
		if (!$this->Error) { $this->Error = $message; }
	
		if ($this->HaltOnError == "no") {
			return;
		}

		$eol = get_eol();
		$request_uri = get_var("REQUEST_URI");
		$http_host = get_var("HTTP_HOST");
		$http_referer = get_var("HTTP_REFERER");

		$protocol = (strtoupper(get_var("HTTPS")) == "ON") ? "https://" : "http://";
		$page_url = $protocol . $http_host . $request_uri;

		$error_message  = "<b>Page URL:</b> <a href=\"" . $page_url . "\">" . $page_url . "</a><br>" . $eol;
		$error_message .= "<b>Referrer URL:</b> <a href=\"" . $http_referer . "\">" . $http_referer . "</a><br>" . $eol;
		$error_message .= "<b>Database error:</b> " . $message . "<br>" . $eol;
		$error_message .= "<b>ODBC Error:</b> " . $this->Error . "<br>" . $eol;

		// to get notify change email address and uncomment mail line below
		$recipients     = "db_error_email@domain_name";
		$subject        = "DB ERROR " . $this->Errno;
		$message        = strip_tags($error_message);
		$email_headers = array();
		$email_headers["from"] = "db_error_email@domain_name";
		$email_headers["mail_type"] = 0;
		//va_mail($recipients, $subject, $message, $email_headers);

		// print error page 
		if (!isset($t)) {
			if ($is_admin_path) {
				$templates_dir = isset($settings["admin_templates_dir"]) ? $settings["admin_templates_dir"] : "../templates/admin";
			} else {
				$templates_dir = isset($settings["templates_dir"]) ? $settings["templates_dir"] : "./templates/default";
			}
			if (class_exists("VA_Template")) {
				$t = new VA_Template($templates_dir);
			}
		} else {
			$templates_dir = $t->get_template_path();
		}

		if ($is_admin_path) {
			$template_exists = file_exists($templates_dir . "/" . "admin_error_db.html");
		} else {
			$template_exists = file_exists($templates_dir . "/" . "error_db.html");
		}
		
		if (isset($t) && $template_exists) {
			if ($is_admin_path) {
				$t->set_file("header",   "admin_header.html");
				$t->set_file("footer",   "admin_footer.html");
				$t->set_file("error_db", "admin_error_db.html");
			} else {
				$t->set_file("header",   "header.html");
				$t->set_file("footer",   "footer.html");
				$t->set_file("error_db", "error_db.html");
			}
			$t->set_var("error_message", $error_message);
			$t->set_var("error_number", $this->Errno);
	  
			$subject = str_replace("+", "%20", urlencode($subject));
			$message = str_replace("+", "%20", urlencode($message));
			$t->set_var("subject", $subject);
			$t->set_var("body", $message);
	  
			$t->parse("header", false);
			$t->parse("footer", false);
			$t->pparse("error_db", false);
		} else {
			echo $error_message;
		}

		if ($this->HaltOnError != "report") {
			exit;
		}
	}

	function tosql($value, $value_type, $is_delimiters = true, $use_null = true) {
		if (is_array($value) || strlen($value)) {
			switch ($value_type)	{
				case NUMBER:
				case FLOAT:
					return preg_replace(array("/,/", "/[^0-9\.,\-]/"), array(".", ""), $value);
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
					if ($this->DBType == "mysql") {
						$value = addslashes($value);
					} else {
						$value = str_replace("'", "''", $value);
					}
					break;
			}
			if ($is_delimiters) {
				if ($this->DBType == "access" && ($value_type == DATETIME || $value_type == DATE || $value_type == TIME)) {
					$value = "#" . $value . "#";
				} else {
					$value = "'" . $value . "'";
				}
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
	//	$rs = odbc_primarykeys( $this->LinkID, "database", "dbo", $table_name);
	//	$rs = odbc_columns( $this->LinkID);
		$rs = odbc_tableprivileges($this->LinkID);
		odbc_result_all($rs);
	
		$mstmt="select * from SYSIBM.SYSFUNCTIONS"; 
		$b = odbc_exec($this->LinkID,$mstmt); 
		odbc_result_all($b); 

		$fields = array();
		$sql = "SELECT * FROM ".$table_name."";
		$this->query($sql);
		$i = 1;
		while (@odbc_field_name($this->QueryID, $i)) {
			$name = odbc_field_name($this->QueryID, $i);
			$type = (odbc_field_type($this->QueryID, $i)=='VARCHAR') ? odbc_field_type($this->QueryID, $i) . '(' . odbc_field_len($this->QueryID, $i) . ')' : odbc_field_type($this->QueryID, $i);
			$null = '';
			$primary = '';
			$auto_increment = '';
			$default = '';
			$index = '';
			$i++;
			$field = array('name' => $name, 'type' => $type, 'null' => $null, 'primary' => $primary, 'auto_increment' => $auto_increment, 'default' => $default, 'index' => $index);
			$fields[] = $field;
		}
		return $fields;
	}

	function get_tables($perametr = 'ALL')
	{
		$tables = array();
		$this->QueryID = odbc_tables($this->LinkID);
		while ($this->next_record()){
			if ($this->f('TABLE_TYPE') == 'TABLE'){
				$tables[] = $this->f('TABLE_NAME');
			}
		}
		return $tables;		
	}
	
	function create_database($db_name = "")
	{
		$resource_id = 0;
		if (strlen($db_name) == 0) {
			$db_name = $this->DBDatabase;
		}

		if (!$resource_id) {
			$this->halt($this->describe_error(1, "Database creation is not supported for this type of connection."));
			return 0;
		} else {
			return 1;
		}
	}

}

?>