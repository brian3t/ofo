<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_dump_create.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	@set_time_limit(900);

	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/db_functions.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "messages/" . $language_code . "/install_messages.php");

	check_admin_security("db_management");
	$operation = get_param("operation");
	$eol = get_eol();

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_dump_create.html");
	$t->set_var("admin_dump_create_href", "admin_dump_create.php");
	$t->set_var("admin_dump_href", "admin_dump.php");

	$return_page = "admin_dump.php";
	$current_index = 0;
	$date_dump_format = array("YYYY", "_", "M", "_", "D", "_", "H", "_", "mm","_","ss");
	$date_dump = va_date($date_dump_format);
	$dump_path = "../db/";
	$dump_file_name = check_file_exists($dump_path, "dump_" . $date_dump . ".sql");

	if ($db_type != 'mysql') {
		$t->set_var("db_structure", "");
	} else {
		$t->parse("db_structure", true);
	}
	if ($db_type == 'access') {
		$db_type_list = array(array('access','access'));
	} else {
		$db_type_list = array(array('mysql', 'mysql'), array('postgre', 'postgre'), array('access', 'access'));
		$db_type_list = array(array('mysql', 'mysql'));
	}
	$tables = array();
	$tables = $db->get_tables();
	foreach ($tables as $key => $table_name) {
		$t->set_var("table_name", $table_name);
		$t->set_var("table_id", $key);
		$t->parse("tables", true);
	}

	$r = new VA_Record("");
	$r->add_textbox("dump_file_name", TEXT, DUMP_FILENAME_MSG);
	$r->change_property("dump_file_name", REQUIRED, true);
	$r->add_hidden("tables_select", TEXT, SELECTED_TABLES_MSG);
	$r->change_property("tables_select", REQUIRED, true);
	$r->add_checkbox("use_structure", INTEGER);
	$r->add_select("db_type", TEXT, $db_type_list, DB_TYPE_FIELD);
	$r->get_form_values();
	if ($operation == "cancel") {
		header("Location: " . $return_page);
		exit;
	}
	if (!strlen($operation)) {
		$r->set_value("dump_file_name", $dump_file_name);
		$r->set_value("use_structure", 0);
		$r->set_value("db_type", $db_type);
	} else {
		if ($r->validate()) {
			$tables_select = $r->get_value("tables_select");
			$dump_file_name = $r->get_value("dump_file_name");
			$use_structure = $r->get_value("use_structure");
			$db_type_select = $r->get_value("db_type");
			$tables = split(",", $tables_select);
			$file_permission_message = str_replace("{file_name}", $dump_path . $dump_file_name, FILE_PERMISSION_MESSAGE);
			$folder_permission_message = str_replace("{folder_name}", $dump_path, FOLDER_PERMISSION_MESSAGE);
			if (file_exists($dump_path . $dump_file_name) && !is_writable ($dump_path . $dump_file_name)) {
				$r->errors = $file_permission_message;
			} elseif ( !is_writable ($dump_path) ) {
				$r->errors = $folder_permission_message;
			} else {
				$fp = fopen($dump_path . $dump_file_name, "w");
				if (!$fp) {
					$r->errors = $file_permission_message;
				}
			}
			if (!strlen($r->errors)) {

				// parse initial page information before output any data when creating dump
				$t->set_var("dump_file_name", $dump_file_name);
				$t->parse("dump_creation", true);
				include_once("./admin_header.php");
				include_once("./admin_footer.php");
				$t->pparse("main");
				flush();

				$filesize = 0; $last_output = 0;

				foreach ($tables as $table_name) {
					if ($use_structure) {
						$data = $eol . get_table_structure($table_name, $db_type_select);
						$filesize += strlen($data);
						fwrite($fp, $data); // mysql | postgre | access
						output_size_js($filesize, $last_output);
					}
					$data = $eol . "DELETE FROM `" . $table_name . "`;" . $eol;
					$filesize += strlen($data);
					fwrite($fp, $data); 
					output_size_js($filesize, $last_output);
					$fields = array();
					$fields = $db->get_fields($table_name);
					$columns_list = '';
					foreach ($fields as $field_value) {
						$field_value['name'];
						$columns_list .= (strlen($columns_list)) ? ', '.$field_value['name'] : $field_value['name'];
					}
					$sql = "SELECT " . $columns_list . " FROM `" . $table_name . "`";
					$db->query($sql);
					while ($db->next_record()) {
						$values_list = '';
						foreach ($fields as $field_value) {
							if (preg_match("/INT/i", $field_value['type']) || preg_match("/COUNTER/i", $field_value['type'])) {
								$value = $db->tosql($db->f($field_value['name']),INTEGER);
							} elseif (preg_match("/DOUBLE/i", $field_value['type']) || preg_match("/FLOAT/i", $field_value['type'])) {
								$value = $db->tosql($db->f($field_value['name']),FLOAT);
							} elseif (preg_match("/TIME/i", $field_value['type'])) {
								$value = $db->tosql($db->f($field_value['name'],DATETIME),DATETIME);
							} else {
								$value = $db->tosql($db->f($field_value['name']),TEXT);
								$value = str_replace(array("\t", "\r", "\n"), array("\\t", "\\r", "\\n"), $value);
							}
							if ($value == 'NULL') {
								if ($field_value['null'] || preg_match("/TIME/i", $field_value['type'])){
									$value == 'NULL';
								} elseif (strlen($field_value['default'])){
									if (preg_match("/INT/i", $field_value['type']) || preg_match("/COUNTER/i", $field_value['type'])) {
										$value = $db->tosql($field_value['default'],INTEGER);
									} elseif (preg_match("/DOUBLE/i", $field_value['type']) || preg_match("/FLOAT/i", $field_value['type'])) {
										$value = $db->tosql($field_value['default'],FLOAT);
									} else {
										$value = $db->tosql($db->f($field_value['default']),TEXT);
										$value = str_replace(array("\t", "\r", "\n"), array("\\t", "\\r", "\\n"), $value);
									}
								} else {
									$value = "''";
								}
							}
							$values_list .= (strlen($values_list))? ', '.$value: $value;
						}
						$data = "INSERT INTO `" . $table_name . "` (" . $columns_list . ") VALUES (" . $values_list . ");" . $eol;
						$filesize += strlen($data);
						fwrite($fp, $data); 
						output_size_js($filesize, $last_output);
					}
				}
				fclose($fp);
				output_size_js($filesize, $last_output, true);

				$t->pparse("page_end");
				return;
			}
		}
	}
	$r->set_parameters();
	$t->parse("dump_create", true);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->parse("page_end", false);
	$t->pparse("main");

	function check_file_exists($filepath, $filename)
	{
		global $current_index;

		$everything_ok = false;
		$new_filename = $filename;
		while (!$everything_ok){
			if (file_exists($filepath . $new_filename)){
				$new_filename = get_new_file_name ($filepath, $filename);
			} else {
				$everything_ok = true;
			}
		}
		return $new_filename;
	}

	function get_new_file_name($filepath, $filename)
	{
		global $current_index;

		$new_filename = $filename;
		while (file_exists($filepath . $new_filename)){
			$current_index++;
			$delimiter_pos = strpos($filename, ".");
			if ($delimiter_pos){
				$new_filename = substr($filename, 0, $delimiter_pos) . "_" . $current_index . substr($filename, $delimiter_pos);
			} else {
				$new_filename = $index . "_" . $filename;
			}
		}
		return $new_filename;
	}

	function output_size_js($filesize, &$last_output, $final = false) 
	{
		global $eol;
		if (($filesize - $last_output) > 16384 || $final) {
			$last_output = $filesize;
			$final_var = $final ? 1 : 0;
			echo "<script language=\"JavaScript\" type=\"text/javascript\">".$eol."<!--".$eol."updateFilesize(".$filesize.",".$final_var.");".$eol."//-->".$eol."</script>".$eol;
			flush();
		}
	}


?>