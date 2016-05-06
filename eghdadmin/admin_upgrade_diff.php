<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_upgrade_diff.php                                   ***
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
	include_once("./admin_common.php");

	function read_dir() {
		$path    = "..";
		$folders = array();
		$folders[0]["path"] = "admin";
		$folders[0]["name"] = "admin";  
		$folders[1]["path"] = "docs";
		$folders[1]["name"] = "docs";
		$folders[2]["path"] = "images";
		$folders[2]["name"] = "images";
		$folders[3]["path"] = "includes";
		$folders[3]["name"] = "includes";
		$folders[4]["path"] = "js";
		$folders[4]["name"] = "js";
		$folders[5]["path"] = "messages";
		$folders[5]["name"] = "messages";
		$folders[6]["path"] = "payments";
		$folders[6]["name"] = "payments";
		$folders[7]["path"] = "shipping";
		$folders[7]["name"] = "shipping";
		$folders[8]["path"] = "sms";
		$folders[8]["name"] = "sms";
		$folders[9]["path"] = "styles";
		$folders[9]["name"] = "styles";
		$folders[10]["path"] = "templates";
		$folders[10]["name"] = "templates";
		$i = 11;
		for ( $u = 0; $u < $i; $u++ ) {
			if ($handle = @opendir($path . "/" . $folders[$u]["path"])) {
				while ( false !== ($file = @readdir($handle))) {
					if (is_dir($path . "/" . $folders[$u]["path"]."/".$file)) {
						if ($file != '..' && $file != '.' && $file != '' && (substr($file, 0, 1) != '.')) {
							$folders[$i]["path"] = $folders[$u]["path"] . "/".$file;
							$folders[$i]["name"] = $file;
							$i++;
						}
					}
				}
				closedir($handle);
			}
		}
		return $folders;
	}

	function read_cur_files($folders) {
		$path = "..";
		$i = 0;
		$content_cur_file = array();
		foreach($folders as $v) {
			$root_dir = $v["path"];
			$content_cur_file[$i]["name"] = $v["name"];
			$content_cur_file[$i]["path"] = $root_dir;
			$i++;
			if(is_dir($path . "/" . $root_dir)) {
				if ($handle = opendir("./" . $path . "/" . $root_dir)) {
					while(false !== ($file = readdir($handle))) {
						if($file != ".." && $file != ".") {
							$content_cur_file[$i]["name"] = $file;
							$content_cur_file[$i]["path"] = $root_dir . "/" . $file;
							$i++;
						}
					}
				}
				closedir($handle);
			}
		}
		return $content_cur_file;
	}
	function read_root_files() {
		$i = 0;
		if ($handle = opendir("..")) {
			while(false !== ($file = readdir($handle))) {
				if($file != ".." && $file != ".") {
					$content_cur_file[$i] = $file;
					$i++;
				}
			}
		}
		closedir($handle);
		return $content_cur_file;
	}

	function get_ext_file($file_name) {
		$parse_file = explode('.', $file_name);
		if(count($parse_file) > 1)
			return $parse_file[count($parse_file)-1];
		else
			return "";
	}

	include_once("./admin_common.php");

	check_admin_security("site_settings");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_upgrade_diff.html");
	$t->set_var("admin_upgrade_href", "admin_upgrade_diff.php");
	$r = new VA_Record("");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// $last_version is version from http://www.viart.com/viart_shop.xml
	$viart_xml = @fsockopen("www.viart.com", 80, $errno, $errstr, 12);

	if ($viart_xml) {

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
		$xml_opened = true;
		fclose($viart_xml);
	}
	else {
		$last_version = VA_RELEASE;
		$xml_opened = false;
	}
	// end $last_version find out


	$t->set_var("UPGRADE_TITLE",         UPGRADE_TITLE);
	$version_number = get_db_value("SELECT setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='version' AND setting_name='number'");
	if (!$version_number) $version_number = VA_RELEASE;
	$t->set_var("version_number", $version_number);
	$t->set_var("last_version", $last_version);
	$array_dir_tree = read_dir();
	$array_dir_tree = read_cur_files($array_dir_tree);
	foreach($array_dir_tree as $res) {
		 $sortAux[] = $res['path'];
	}
	array_multisort($sortAux, SORT_ASC, $array_dir_tree);
	$root_files = read_root_files();
	$num_files = count($array_dir_tree);
	foreach($root_files as $v) {
		$array_dir_tree[$num_files]["name"] = $v;
		$array_dir_tree[$num_files]["path"] = $v;
		$num_files++;

	}
	$k = 2;
	$minus = "";
	foreach($array_dir_tree as $dir_name) {
		$array_dir = explode("/", $dir_name["path"]);
		$num_array = count($array_dir);
		for($i = 1; $i < $num_array; $i++) {
			$minus .= " - ";		
		}
		$t->set_var("file_name", $minus . $dir_name["name"]);
		$t->set_var("file_path", $minus . $dir_name["path"]);
		$t->set_var("file_id", $dir_name["path"]);
		$t->set_var("parent_file_id", 0);
		$t->parse("files", true);
		$minus = "";
		$k++;
	}

	if (!$xml_opened) $t->set_var("NO_XML_CONNECTION", NO_XML_CONNECTION);
	else $t->set_var("NO_XML_CONNECTION", "");

	$error = get_param("error");
	if(strlen($error) > 0) { 
		$t->set_var("error_msg", $error);
		$t->parse("err_block", false);
	}

	$t->pparse("main", false);
?>