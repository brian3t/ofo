<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_currencysource.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	$root_folder_path = "../";
	include_once ($root_folder_path . "includes/var_definition.php");
	include_once ($root_folder_path . "includes/constants.php");
	include_once ($root_folder_path . "includes/common_functions.php");
	include_once ($root_folder_path . "includes/va_functions.php");
	include_once ($root_folder_path . "includes/db_$db_lib.php");

	// Database Initialize
	$db = new VA_SQL();
	$db->DBType      = $db_type;
	$db->DBDatabase  = $db_name;
	$db->DBHost      = $db_host;
	$db->DBPort      = $db_port;
	$db->DBUser      = $db_user;
	$db->DBPassword  = $db_password;
	$db->DBPersistent= $db_persistent;

	check_admin_security("static_tables");

	$error_message = ""; $success_message = "";

	$sql  = " SELECT * FROM " . $table_prefix . "currencies ";
	$sql .= " WHERE is_default=1 ";
	$db->query($sql);
	if ($db->next_record()) {
		$default_currency = strtoupper($db->f("currency_code"));
		$currency_host = "feeds.currencysource.com";
		$currency_path = "/" . $default_currency . ".xml";
		$currency_url  = "http://" . $currency_host . $currency_path;
		
		// get rss file with rates
		$xml_rss = "";
		$fp = @fsockopen($currency_host, 80, $errno, $errstr, 5);
		if ($fp) {
    
			fputs($fp, "GET " . $currency_path . " HTTP/1.0\r\n");
			fputs($fp, "Host: " . $currency_host . "\r\n");
			fputs($fp, "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n\r\n");
			
			while (!feof($fp)) {
				$line = fgets($fp);
				$xml_rss .= $line;
			}
			fclose($fp);
		} else {
			$error_message = CANT_CONNECT_REMOTE_MSG . $currency_host;
			return;
		} 
		
		if (!$xml_rss) {
			$error_message = EMPTY_RESPONSE_MSG . "<a href=\"" . $currency_url . "\">" . $currency_url . "</a>";
			return;
		} else if (preg_match("/^HTTP\/1\.[01]\s+404/i", trim($xml_rss))) {
			$error_message = PAGE_CANT_BE_FOUND_MSG . "<a href=\"" . $currency_url . "\">" . $currency_url . "</a>";
			return;
		}

		$currencies = array();
		$sql  = " SELECT currency_id, currency_code FROM " . $table_prefix . "currencies ";
		$sql .= " WHERE is_default<>1 OR is_default IS NULL ";
		$db->query($sql);
		while ($db->next_record()) {
			$currency_id = $db->f("currency_id");
			$currency_code = strtoupper($db->f("currency_code"));
			$currencies[$currency_id] = $currency_code;
		}

		$updated_codes = ""; $error_codes = "";
		foreach ($currencies as $currency_id => $currency_code) {
			if (preg_match ("/\<title\>\s*1\s*" . $default_currency . "\s*=\s*" . $currency_code . "\s*\(([\d\.]+)\)\s*\<\/title\>/i", $xml_rss, $matches) ) {
				if ($updated_codes) { $updated_codes .= ", "; }
				$updated_codes .= $currency_code;
				$exchange_rate = $matches[1];
				$sql  = " UPDATE " . $table_prefix . "currencies SET ";
				$sql .= " exchange_rate=" . $db->tosql($exchange_rate, NUMBER);
				$sql .= " WHERE currency_id=" . $db->tosql($currency_id, INTEGER);
				$db->query($sql);
			} else {
				if ($error_codes) { $error_codes .= ", "; }
				$error_codes .= $currency_code;
			}
		}
		
		if ($updated_codes) {
			$success_message = CURRENCIES_UPDATED_MSG . $updated_codes;
		}
		if ($error_codes) {
			$error_message = CANT_FIND_RATES_MSG . $error_codes;
		}

	} else {
		$error_message = CANT_FIND_DEF_CURRENCYMSG;
		return;
	}
	
?>