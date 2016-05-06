<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_upgrade.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	@set_time_limit(600);
	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/install_messages.php");
	include_once($root_folder_path . "messages/" . $language_code . "/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("system_upgrade");

	$eol = get_eol();
	$operation = get_param("operation");

	// secondary connection to perform DB upgrade
	$dbs = new VA_SQL();
	$dbs->DBType      = $db_type;
	$dbs->DBDatabase  = $db_name;
	$dbs->DBUser      = $db_user;
	$dbs->DBPassword  = $db_password;
	$dbs->DBHost      = $db_host;
	$dbs->DBPort      = $db_port;
	$dbs->DBPersistent= $db_persistent;

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_upgrade.html");
	$t->set_var("admin_upgrade_href", "admin_upgrade.php");
	$upgrade_button = str_replace("{version_number}", VA_RELEASE, UPGRADE_BUTTON);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// $last_version is version from http://www.viart.com/viart_shop.xml
	$viart_xml = @fsockopen("www.viart.com", 80, $errno, $errstr, 12);

	if ($viart_xml)
	{
		fputs($viart_xml, "GET /viart_shop.xml HTTP/1.0\r\n");
		fputs($viart_xml, "Host: www.viart.com\r\n");
		fputs($viart_xml, "Referer: http://www.viart.com\r\n");
		fputs($viart_xml, "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n\r\n");

		$last_version = "";

		while (!feof($viart_xml)) {
			$line = fgets($viart_xml);
			if (strpos($line, "Program_Version")) {
				for ($i = 0; $i < strlen($line); $i++) if ((is_numeric($line[$i])) or ($line[$i] == ".")) $last_version .= $line[$i];
			}
		}

		fclose($viart_xml);
	} else {
		$last_version = VA_RELEASE;
		$t->parse("connection_error", false);
	}
	// end $last_version find out

	$download_found = str_replace("{version_number}", $last_version, DOWNLOAD_FOUND_MSG);
	$download_now = str_replace("{version_number}", $last_version, DOWNLOAD_NOW_MSG);

	$t->set_var("UPGRADE_BUTTON",        $upgrade_button);
	$t->set_var("DOWNLOAD_FOUND_MSG",    $download_found);
	$t->set_var("DOWNLOAD_NOW_MSG",      $download_now);

	$version = array();

	$sql  = " SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='version' ";
	$db->query($sql);

	while ($db->next_record()) {
		$setting_name = $db->f("setting_name");
		$setting_value = $db->f("setting_value");
		$version[$setting_name] = $setting_value;
	}

	if (isset($version["number"])) {
		$current_db_version = $version["number"];
	} elseif (defined("VA_RELEASE")) {
		$current_db_version = VA_RELEASE;
	} else {
		$current_db_version = "2.5";
	}

	if ($operation == "upgrade") 
	{
		$ct = get_param("ct");
		$session_ct = get_session("session_ct");
		if ($ct != $session_ct) {
			set_session("session_ct", $ct);
			$sqls = array();

			// output information step by step during upgrade process
			$t->set_var("latest_version", VA_RELEASE);
			$t->parse("upgrade_result", false);
			$t->pparse("main");
			// start upgrading process
			echo "<script language=\"JavaScript\" type=\"text/javascript\">".$eol."<!--".$eol."upgradingProcess();".$eol."//-->".$eol."</script>".$eol;
			flush();

			// prepare some variables
			$errors = "";
			$db->HaltOnError = "no";
			$queries_success = 0;
			$queries_failed  = 0;

			if (comp_vers("2.5", $current_db_version) == 1) {
				include_once("./admin_upgrade_sqls_2.5.php");
			}

			if (comp_vers("2.9", $current_db_version) == 1) {
				include_once("./admin_upgrade_sqls_2.9.php");
			}

			if (comp_vers("3.2", $current_db_version) == 1) {
				include_once("./admin_upgrade_sqls_3.2.php");
			}

			if (comp_vers("3.3", $current_db_version) == 1) {
				include_once("./admin_upgrade_sqls_3.3.php");
			}

			if (comp_vers("3.4", $current_db_version) == 1) {
				include_once("./admin_upgrade_sqls_3.4.php");
			}

			if (comp_vers("3.5", $current_db_version) == 1) {
				include_once("./admin_upgrade_sqls_3.5.php");
			}

			if (comp_vers("3.6", $current_db_version) == 1) {
				include_once("./admin_upgrade_sqls_3.6.php");
			}

			set_session("session_errors", $errors);
			set_session("session_queries_failed", $queries_failed);
			set_session("session_queries_success", $queries_success);
		} else {
			$errors = get_session("session_errors");
			$queries_failed = get_session("session_queries_failed");
			$queries_success = get_session("session_queries_success");
		}

		if ($queries_failed) {
			output_block_info($queries_failed, "queriesFailed");
		}
		if ($queries_success) {
			output_block_info($queries_success, "queriesSuccess");
		}

		echo "<script language=\"JavaScript\" type=\"text/javascript\">".$eol."<!--".$eol."databaseUpgraded();".$eol."//-->".$eol."</script>".$eol;
		flush();

		$t->pparse("page_end", false);
		return;
	} 
	elseif (comp_vers(VA_RELEASE, $current_db_version) == 1) 
	{
		$r = new VA_Record("", "upgrade_available");
		$r->add_hidden("operation", TEXT);
		$r->set_form_parameters();
		$t->set_var("ct", va_timestamp());
		$t->set_var("current_version", $current_db_version);
		$t->set_var("latest_version", VA_RELEASE);
		$t->parse("upgrade_available", false);

		if (comp_vers($last_version, VA_RELEASE) == 1) {
 			$t->set_var("last_version", $last_version);
			$t->parse("download_new", false);
		}
	} 
	elseif (comp_vers($last_version, VA_RELEASE) == 1) 
	{
 		$t->set_var("last_version", $last_version);
		$t->parse("download_new", false);
	} 
	else 
	{
		$t->set_var("current_version", $current_db_version);
		$t->set_var("latest_version", $last_version);
		$t->parse("no_upgrades", false);
	}

	$t->parse("page_end", false);
	$t->pparse("main");

	function run_queries(&$sqls, &$queries_success, &$queries_failed, &$errors, $version_number = "")
	{
		global $db, $table_prefix;

		if (is_array($sqls)) {
			for ($i = 0; $i < sizeof($sqls); $i++) {
				$sql = $sqls[$i];
				$db->query($sql);
				if ($db->Error) {
					$queries_failed++;
					$errors .= $db->Error . "<br>";
					$errors .= "SQL: " . $sql . "<br>";
					output_block_info($queries_failed, "queriesFailed");
					output_block_info($errors, "queriesErrors");
				} else {
					$queries_success++;
					output_block_info($queries_success, "queriesSuccess");
				}
			}
		}
		$sqls = array(); // empty array

		if ($version_number) {
			// set version information in database
			$sql = "DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='version' AND (setting_name='number' OR setting_name='upgraded') ";
			$db->query($sql);
			if ($db->Error) { $errors .= $db->Error . "<br>"; }
			$sql  = " INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES ";
			$sql .= " ('version', 'number', " . $db->tosql($version_number, TEXT) . ", 1)";
			$db->query($sql);
			if ($db->Error) { $errors .= $db->Error . "<br>"; }
			$sql  = " INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES ";
			$sql .= " ('version', 'upgraded', " . $db->tosql(va_timestamp(), TEXT) . ", 1)";
			$db->query($sql);
			if ($db->Error) { $errors .= $db->Error . "<br>"; }
		}
	}

	function output_block_info($message, $control_name) 
	{
		global $eol;
		$message = str_replace(array("'", "\n", "\r"), array("\\'", "\\n", "\\r"), $message);
		echo "<script language=\"JavaScript\" type=\"text/javascript\">".$eol."<!--".$eol."updateBlockInfo('".$message."','".$control_name."');".$eol."//-->".$eol."</script>".$eol;
		flush();
	}

?>