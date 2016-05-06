<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_upgrade_diff_file.php                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/date_functions.php");
	include_once ($root_folder_path . "messages/".$language_code."/install_messages.php");
	include_once ($root_folder_path . "includes/class_diff.php");

	
	include_once("./admin_common.php");

	check_admin_security("site_settings");
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_upgrade_diff_file.html");
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$ver_type = get_param("ver_type");
	$diff_file = get_param("diff_file");
	$t->set_var("diff_file", $diff_file);
	$lines1 = array();
	$lines2 = array();
	$error_msg = "";

	$lines1 = @file("../" . $diff_file);
	foreach($lines1 as $k => $v) $lines1[$k] = htmlspecialchars($v);
	
	if(strlen($user_path) > 0) {

		if(file_exists($user_path . "/" . $diff_file)) {
			$lines2 = @file($user_path . "/" . $diff_file);
			foreach($lines2 as $k => $v) $lines2[$k] = htmlspecialchars($v);
		}
		else {
			$error_msg = FILE_DOESNT_EXIST_MSG . $user_path . "/" . $diff_file ;
		}
	}
	else {
		$viart_file = @fsockopen("www.viart.com", 80, $errno, $errstr, 12);
		if ($viart_file) {
			$diff_file = str_replace("../", " ", $diff_file);
			fputs($viart_file, "GET /products/" . $diff_file . ".txt HTTP/1.0\r\n");
			fputs($viart_file, "Host: www.viart.com\r\n");
			fputs($viart_file, "Referer: http://www.viart.com\r\n");
			fputs($viart_file, "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n\r\n");
			$last_version = "";
			$b = false;
			while (!feof($viart_file)) {
				$str = fgets($viart_file);
				if(!$b) {
					if(strstr($str, "404 Not Found")) {
						$error_msg = FILE_DOESNT_EXIST_MSG . $diff_file ;
						break;
					}
					if(strstr($str, "Content-Type: text/plain; charset=iso-8859-1")) {
						$b = true;
						fgets($viart_file);
					}
				}
				else	{
					$lines2[] = htmlspecialchars($str);
				}
			}
			fclose($viart_file);
			
		} else {
			$error_msg = CANNOT_CONNECT_SERVER_MSG;
		}
	}
	if(strlen($error_msg) == 0) {
		$diff = new Text_Diff($lines1, $lines2);
		$k = 1;
		foreach($diff->_edits as $text_block) {
			switch (strtolower(get_class($text_block))) {
			case 'text_diff_op_copy':
				foreach($text_block->orig as $line) {
					$t->set_var("current_line", $line);
					$t->set_var("ver_line", $line);
					$t->set_var("class_status", "usual");
					$t->set_var("line_number", $k);
					$t->parse("lines", true);
					$k++;
				}
				break;
			case 'text_diff_op_add':
				foreach($text_block->final as $line) {
					$t->set_var("current_line", "");
					$t->set_var("ver_line", $line);
					$t->set_var("class_status", "diff_delete");
					$t->set_var("line_number", $k);
					$t->parse("lines", true);
					$k++;
				}
				break;

			case 'text_diff_op_delete':
				foreach($text_block->orig as $line) {
					$t->set_var("current_line", $line);
					$t->set_var("ver_line", "");
					$t->set_var("class_status", "diff_delete");
					$t->set_var("line_number", $k);
					$t->parse("lines", true);
					$k++;
				}
				break;

			case 'text_diff_op_change':
				foreach($text_block->final as $m => $line) {
				if(!isset($text_block->orig[$m])) $text_block->orig[$m] = "";
					$t->set_var("current_line", $text_block->orig[$m]);
					$t->set_var("ver_line", $line);
					$t->set_var("class_status", "diff_change");
					$t->set_var("line_number", $k);
					$t->parse("lines", true);
					$k++;
				}
				break;
			}
		}
		$t->parse("diff_file", false);
		$t->set_var("err", "");
	}
	else {
		$t->set_var("error_msg", $error_msg);
		$t->parse("err", false);
		$t->set_var("diff_file", "");
	}

	$t->pparse("main", false);
?>