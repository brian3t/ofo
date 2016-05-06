<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_dump_apply.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	@set_time_limit(900);

	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/install_messages.php");

	check_admin_security("db_management");

	$file_name = get_param("file_name");
	$return_page = "admin_dump.php";

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_dump_apply.html");
	$t->set_var("admin_dump_apply_href", "admin_dump_apply.php");
	$t->set_var("admin_dump_href", "admin_dump.php");

	$dump_warning_message = str_replace(array("\n", "\r", "\""), array("\\n", "\\r", "\\\""), DUMP_APPLY_WARN_MSG);
	$t->set_var("dump_warning_message", $dump_warning_message);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$eol = get_eol();
	$operation = get_param("operation");
	if ($operation == "cancel"){
		header("Location: " . $return_page);
		exit;
	}
	flush();

	$r = new VA_Record("", "");
	$r->add_hidden("operation", TEXT);
	$r->add_textbox("sql_file_name", TEXT);
	$r->change_property("sql_file_name", DEFAULT_VALUE, $file_name);
	$r->add_checkbox("is_apply", INTEGER, APPLY_DUMP_MSG);
	$r->change_property("is_apply", REQUIRED, true);

	if (!$r->errors)
	{
		if ($operation == "apply") {
			$r->get_form_parameters();
			$is_valid = $r->validate();
			if ($is_valid) {
				$r->empty_values();

				$db->HaltOnError = "no";
				if (!$db->connect()) {
					$r->errors  = DB_CONNECT_ERROR . "<br>";
					if ($db->Error) {
						$r->errors .= $db->Error;
					}
				} else {
					$is_access = ($db->DBType == "access");
					$db_filename = get_param("sql_file_name");
					$dump_sql = "../db/" . $db_filename;
					$sql = "";
					if (!strlen($db_filename))
					{
						$r->errors = ENTER_SQL_FILENAME_MSG . "<br>";
						$dump_sql="";
					}
					elseif (file_exists($dump_sql))
					{
						// output information step by step during upgrade process
						$file_applied_message = str_replace("{filename}", basename($dump_sql), DUMP_FILE_APPLIED_MSG);
						$file_applied_message = str_replace("\"", "\\\"", $file_applied_message);

						$t->set_var("file_applied_message", $file_applied_message);
						$t->parse("apply_result", false);
						$t->pparse("main");
						// start upgrading process
						echo "<script language=\"JavaScript\" type=\"text/javascript\">".$eol."<!--".$eol."applyingProcess();".$eol."//-->".$eol."</script>".$eol;
						flush();
		      
						// prepare some variables
						$errors = "";
						$db->HaltOnError = "no";
						$queries_success = 0; $queries_failed  = 0;

						$symbols_replace = false;
						if ($db_type == "access" || $db_type == "db2") {
							$symbols_replace = true;
						}
						// read file and run queries
						$fp = fopen($dump_sql, "rb");
						while (!feof($fp)) {
							$sql_string = fgets($fp);
							if (preg_match ("/\;\s*$/i", $sql_string)) {
								$sql_string = preg_replace("/;\s*$/i", "", $sql_string);
								$sql .= $sql_string;
								if (preg_match("/^\s*DROP\s+/i", $sql)) {
									$drop_table_syntax = true;
								} else {
									$drop_table_syntax = false;
								}
								$sql = trim($sql);
								if ($symbols_replace) {
									$sql = str_replace("\\n", "\n", $sql);
									$sql = str_replace("\\t", "\t", $sql);
									$sql = str_replace("\\r", "\r", $sql);
									$sql = str_replace("\\\"", "\"", $sql);
									$sql = str_replace("\\'", "''", $sql);
								}
								$db->query($sql);
								if (!$db->Error) {
									// there is no errors query successed
									$queries_success++;
									if ($queries_success <= 20 || ($queries_success <= 200 && $queries_success % 5 == 0) 
										|| ($queries_success <= 1000 && $queries_success % 10 == 0) 
										|| ($queries_success > 1000 && $queries_success % 25 == 0) 
									) {
										output_block_info($queries_success, "queriesSuccess");
									}
								} else if (!$drop_table_syntax) {
									// if there is an error occurred and it's not drop syntax show error
									$queries_failed++;
									$errors .= "<b>".ADMIN_ERROR_MSG."</b>: " . htmlspecialchars($db->Error) . "<br>";
									$errors .= "<b>SQL</b>: " . htmlspecialchars($sql) . "<br><br>";
									output_block_info($queries_failed, "queriesFailed", false);
									output_block_info($errors, "queriesErrors", true);
								}
								$sql = "";
							} else {
								$sql .= $sql_string;
							}
						}
						fclose($fp);

						if ($queries_success) { 
							output_block_info($queries_success, "queriesSuccess");
						}

						echo "<script language=\"JavaScript\" type=\"text/javascript\">".$eol."<!--".$eol."dumpApplied();".$eol."//-->".$eol."</script>".$eol;
						flush();
	        
						$t->pparse("page_end", false);
						return;

					} else {
						$dump_file_error = str_replace("{file_name}", $dump_sql, DUMP_FILE_ERROR);
						$r->errors .= $dump_file_error;
					}
				}
			}
		} else {
			$r->set_default_values();
		}
	}

	$r->set_parameters();

	$t->parse("dump_apply_form", true);

	$t->parse("page_end", false);
	$t->pparse("main");

	function output_block_info($message, $control_name) 
	{
		global $eol;
		$message = str_replace(array("\\", "'", "\n", "\r"), array("\\\\", "\\'", "\\n", "\\r"), $message);
		echo "<script language=\"JavaScript\" type=\"text/javascript\">".$eol."<!--".$eol."updateBlockInfo('".$message."','".$control_name."');".$eol."//-->".$eol."</script>".$eol;
		flush();
	}

?>