<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_db_query.php                                       ***
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

	check_admin_security("db_management");

	$operation = get_param("operation");
	$sql_query = trim(get_param("sql_query"));
	$errors = "";

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_db_query.html");
	$t->set_var("admin_db_query_href", "admin_db_query.php");
	$t->set_var("admin_dump_href", "admin_dump.php");

	$t->set_var("sql_query", htmlspecialchars($sql_query));

	if ($operation == "run" && $sql_query) {
		update_recent_queries($sql_query);

		if (!$errors) {
			$db->HaltOnError = "no";
			$time_start = microtime_float();
			$db->query($sql_query);
			$time_end = microtime_float();
			if(strlen($db->Error)) {
				$errors = $db->Error . "<br>";
			} 
		}

		if (!$errors) {
			$execution_time = ($time_end - $time_start);
			$execution_time = round($execution_time, 2);
			if ($execution_time == 0) {
				$t->set_var("execution_time", "0.00");
			} else {
				$t->set_var("execution_time", $execution_time);
			}
			$query_info = "";
			if ($db_type == "mysql") {
				$query_info = mysql_info();
			}
			if ($query_info) {
				$t->set_var("query_info", $query_info);
				$t->parse("query_info_block", true);
			}

			if ($db->next_record()) {
				$titles = array();
				foreach ($db->Record as $column_title => $column_value) {
					if (!is_numeric($column_title)) {
						$titles[] = $column_title;
						$t->set_var("column_title", $column_title);
						$t->parse("titles", true);
					}
				}
				do {
					for ($c = 0; $c < sizeof($titles); $c++) {
						$column_value = $db->f($titles[$c]);
						if (strlen($column_value)) {
							$t->set_var("column_value", htmlspecialchars($column_value));
						} else {
							$t->set_var("column_value", "&nbsp;");
						}
						$t->parse("cols", true);
					}
					$t->parse("rows", true);
					$t->set_var("cols", "");
				} while ($db->next_record());
				$t->parse("query_data", false);
			}
			$t->parse("query_result", false);
		}
		if ($errors) {
			$t->set_var("errors_list", $errors);
			$t->parse("errors_block", false);
		}
	}
	$recent_queries = get_session("session_recent_queries");
	if (is_array($recent_queries)) {
		$query_id = 0;
		foreach($recent_queries as $key => $recent_query) {
			$recent_query = str_replace("\\", "\\\\", $recent_query);
			$recent_query = str_replace("\"", "\\\"", $recent_query);
			$recent_query = str_replace("\r", "\\r", $recent_query);
			$recent_query = str_replace("\n", "\\n", $recent_query);
			$t->set_var("query_id", $query_id);
			$t->set_var("recent_query", $recent_query);
			$t->parse("queries", true);
			$query_id++;
		}
		$t->set_var("current_query_id", $query_id);
	} else {
		$t->set_var("query_id", 0);
		$t->set_var("current_query_id", 0);
		$t->set_var("prev_disabled", "disabled");
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");


function microtime_float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

function update_recent_queries($sql_query)
{
	$recent_records = 10;
	$recent_queries = get_session("session_recent_queries");
	if (!is_array($recent_queries)) {
		$recent_queries = array();
	} 
	foreach ($recent_queries as $key => $recent_query) {
		if ($recent_query == $sql_query) {
			unset($recent_queries[$key]);
		}
	}
	while (sizeof($recent_queries) >= $recent_records) {
		array_shift($recent_queries);
	}
	array_push($recent_queries, $sql_query);
	set_session("session_recent_queries", $recent_queries);
}

?>