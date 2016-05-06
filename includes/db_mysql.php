<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  db_mysql.php                                             ***
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
	var $DatetimeMask   = array("YYYY", "-", "MM", "-", "DD", " ", "HH", ":", "mm", ":", "ss");
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
	function VA_SQL($query = "") 
	{
		$this->RecordsPerPage = 0;
		$this->query($query);
	}

	function check_lib() 
	{
		return function_exists("mysql_connect");
	}

	function connect($new_link = false) 
	{
		if (!$this->LinkID) {
			$server = ($this->DBPort != "") ? $this->DBHost . ":" . $this->DBPort : $this->DBHost;
		
			if ($this->DBPersistent) {
				$this->LinkID = @mysql_pconnect($server, $this->DBUser, $this->DBPassword);
			} else {
				$this->LinkID = @mysql_connect($server, $this->DBUser, $this->DBPassword, $new_link);
			}

			if (!$this->LinkID) {		
				$this->halt("Connect failed: " . $this->describe_error(mysql_errno(), mysql_error()));
				return 0;
			}
		
			if (!mysql_select_db($this->DBDatabase, $this->LinkID)) {
				$this->LinkID = 0;
				$this->halt($this->describe_error(mysql_errno(), mysql_error()));
				return 0;
			}
		}
		
		return $this->LinkID;
	}

	function free_result()
	{
		if ($this->QueryID && !is_bool($this->QueryID)) {
			@mysql_free_result($this->QueryID);
			$this->QueryID = 0;
		}
	}

	function close() 
	{
		if ($this->QueryID) {
			$this->free_result();
		}
		if ($this->LinkID != 0 && !$this->DBPersistent) {
			@mysql_close($this->LinkID);
			$this->LinkID = 0;
		}
	}

	function query($query_string) 
	{
		if ($query_string == "") {
			return 0;
		}
	
		if (!$this->connect()) {
			return 0; 
		};
	
		if ($this->QueryID) {
			$this->free_result();
		}
	
		if ($this->RecordsPerPage && $this->PageNumber) {
			$query_string .= " LIMIT " . (($this->PageNumber - 1) * $this->RecordsPerPage) . ", " . $this->RecordsPerPage;
			$this->RecordsPerPage = 0;
			$this->PageNumber = 0;
		} else if ($this->RecordsPerPage) {
			$query_string .= " LIMIT " . $this->Offset . ", " . $this->RecordsPerPage;
			$this->Offset = 0;
			$this->RecordsPerPage = 0;
		}
	
		$this->QueryID = @mysql_query($query_string, $this->LinkID);
		$this->Row   = 0;
		$this->Errno = mysql_errno();
		$this->Error = mysql_error();
		if (!$this->QueryID) {
			$this->halt("Invalid SQL: " . $query_string);
		}
		
		return $this->QueryID;
	}

	function next_record() 
	{
		if (!$this->QueryID) {
			$this->halt("next_record called with no query pending.");
			return 0;
		}
	
		$this->Record = @mysql_fetch_array($this->QueryID);
		$this->Row   += 1;
		$this->Errno  = mysql_errno();
		$this->Error  = mysql_error();
		
		$stat = is_array($this->Record);
		if (!$stat && $this->AutoFree) {
			$this->free_result();
		}
		return $stat;
	}

	function seek($pos = 0) 
	{
		$status = @mysql_data_seek($this->QueryID, $pos);
		if ($status) {
			$this->Row = $pos;
		} else {
			$this->halt("seek($pos) failed: result has " . $this->num_rows() . " rows");
		
			@mysql_data_seek($this->QueryID, $this->num_rows());
			$this->Row = $this->num_rows;
			return 0;
		}
		
		return 1;
	}

	function affected_rows() 
	{
		return @mysql_affected_rows($this->LinkID);
	}

	function num_rows() 
	{
		return @mysql_num_rows($this->QueryID);
	}

	function num_fields() 
	{
		return @mysql_num_fields($this->QueryID);
	}

	function f($Name, $field_type = TEXT) 
	{
		if (isset($this->Record[$Name])) {
			$value = $this->Record[$Name];
			switch($field_type) {
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

	function halt($message) 
	{
		global $t, $is_admin_path, $settings;
	
		if (!$this->Error) {
			$this->Error = $message;
		}
	
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
		$error_message .= "<b>MySQL Error:</b> " . $this->Error . "<br>" . $eol;
		
		// to get notification about errors change email address and uncomment mail line below
		$recipients     = "db_error_email@domain_name";
		$subject        = "DB ERROR " . $this->Errno;
		$message        = strip_tags($error_message);
		$email_headers = array();
		$email_headers["from"] = "db_error_email@domain_name";
		$email_headers["mail_type"] = 0;
		//va_mail($recipients, $subject, $message, $email_headers);
		
		// print warning page 
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

	function tosql($value, $value_type, $is_delimiters = true, $use_null = true) 
	{
		if (is_array($value) || strlen($value)) {
			switch ($value_type) {
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
					$value = addslashes($value);
					break;
			}
			if ($is_delimiters) {
				$value = "'" . $value . "'";
			}
		} elseif ($use_null) {
			$value = "NULL";
		} else {
			if ($value_type == INTEGER || $value_type == FLOAT || $value_type == NUMBER 
				|| $value_type == NUMBERS_LIST || $value_type == FLOATS_LIST || $value_type == INTEGERS_LIST) {
				$value = 0;
			} elseif ($is_delimiters) {
				$value = "''";
			}
		} 
		return $value;
	}
	
	function describe_error($error_code, $error_msg) 
	{
		if (!$error_msg) {
			if ($error_code == 2005) {
				// Unknown MySQL Server Host '...' (11001)
				$error_msg = DB_HOST_ERROR;
			} else if ($error_code == 2003) {
				// Can't connect to MySQL server on '...' (10061)
				$error_msg = DB_PORT_ERROR;
			} else if ($error_code == 1044) {
				// Access denied for user: '...' to database '...'
				$error_msg = DB_USER_PASS_ERROR;
			} else if ($error_code == 1045) {
				// Access denied for user: '...' (Using password: YES)
				$error_msg = DB_USER_PASS_ERROR;
			} else if ($error_code == 1049) {
				// Unknown database '...'
				$error_msg = str_replace('{db_name}', $this->DBDatabase, DB_NAME_ERROR);
			}
		}
		return $error_msg;
	}

	function get_fields($table_name)
	{
		$sql = "SHOW COLUMNS FROM `" . $table_name . "`";
		$this->query($sql);
		$fields = array();
		while ($row = mysql_fetch_assoc($this->QueryID)) {
			if (isset($row['Field'])){
				$name = $row['Field'];
			} else {
				$name = '';
			}
			if (isset($row['Type'])){
				$type = strtoupper($row['Type']);
			} else {
				$type = '';
			}
			if (isset($row['Null']) && (strtoupper($row['Null']) == 'YES')){
				$null = true;
			} else {
				$null = false;
			}
			if (isset($row['Key']) && (strtoupper($row['Key']) == 'PRI')){
				$primary = true;
			} else {
				$primary = false;
			}
			if (isset($row['Key']) && (strtoupper($row['Key']) == 'MUL')){
				$index = true;
			} else {
				$index = false;
			}
			if (isset($row['Extra']) && (strtolower($row['Extra']) == 'auto_increment')){
				$auto_increment = true;
			} else {
				$auto_increment = false;
			}
			if (isset($row['Default'])){
				$default = $row['Default'];
			} else {
				$default = '';
			}
			$field = array('name' => $name, 'type' => $type, 'null' => $null, 'primary' => $primary, 'auto_increment' => $auto_increment, 'default' => $default, 'index' => $index);
			$fields[] = $field;
		}
		return $fields;
	}

	function get_tables($perametr = 'ALL')
	{
		$tables = array();
		$sql  = "SHOW TABLES";
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

		$server = ($this->DBPort != "") ? $this->DBHost . ":" . $this->DBPort : $this->DBHost;
	
		if ($this->DBPersistent) {
			$resource_id = @mysql_pconnect($server, $this->DBUser, $this->DBPassword);
		} else {
			$resource_id = @mysql_connect($server, $this->DBUser, $this->DBPassword);
		}

		if (!$resource_id) {		
			$this->halt("Connect failed: " . $this->describe_error(mysql_errno(), mysql_error()));
			return 0;
		} else {
			if (mysql_query("CREATE DATABASE `$db_name`", $resource_id)) {
				return 1;
			} else {
				$this->halt($this->describe_error(mysql_errno(), mysql_error()));
				return 0;
			}
		}
	}
}

?>