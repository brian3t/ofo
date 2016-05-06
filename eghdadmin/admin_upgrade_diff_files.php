<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_upgrade_diff_files.php                             ***
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

	set_time_limit("3000");
	function get_ext_file($file_name) {
		$parse_file = explode('.', $file_name);
		if(count($parse_file) > 1)
			return $parse_file[count($parse_file)-1];
		else
			return "";
	}

	function htmlpath() {
		$realpath = getcwd();
		$realpath = str_replace('\\', '/', $realpath);
		$htmlpath=str_replace($_SERVER['DOCUMENT_ROOT'],'',$realpath);
		return $htmlpath;
	}

	function read_dir($path) {

		$folders = array();
		$folders[0] = "admin";
		$folders[1] = "CVS";
		$folders[2] = "db";
		$folders[3] = "docs";
		$folders[4] = "images";
		$folders[5] = "includes";
		$folders[6] = "js";
		$folders[7] = "messages";
		$folders[8] = "payments";
		$folders[9] = "shipping";
		$folders[10] = "sms";
		$folders[11] = "styles";
		$folders[12] = "templates";
		$i = 13;
		for ( $u = 0; $u < $i; $u++ ) {
			if ($handle = @opendir($path . "/" . $folders[$u])) {
				while ( false !== ($file = @readdir($handle))) {
					if(is_dir($path . "/" .  $folders[$u] . "/" . $file)) {
						if ($file != '..' && $file != '.' && $file != '' && (substr($file, 0, 1) != '.')) {
							$folders[$i] = $folders[$u] . "/" . $file;
							$i++;
						}
					}
				}
				closedir($handle);
			}
		}
		$folders[$i] = $path;
		return $folders;
	}

	function read_cur_files($folders, $file_ext, $path) {
		$i = 0;
		$content_cur_file = array();
		foreach($folders as $v) {
			if($v == $path) $root_dir = $path;
			else $root_dir = $path . "/" . $v;
			$i++;
			if(is_dir($root_dir)) {
				if ($handle = @opendir($root_dir)) {
					while(false !== ($file = readdir($handle))) {
						if($file != ".." && $file != "." && in_array(get_ext_file($file), $file_ext) && (substr($file, 0, 1) != '.') && !is_dir($root_dir . "/" . $file)) {
							if($root_dir == $path)	$content_cur_file[$i] = $file;
							else	$content_cur_file[$i] = $v . "/" . $file;
							$i++;
						}
					}
				}
				closedir($handle);
			}
		}
		return $content_cur_file;
	}

	function get_files_info($user_path) {
		$array_files_info = array();
		if(strlen($user_path) == 0) {
			$array_files_info = array();
			$viart_file = @fsockopen("www.viart.com", 80, $errno, $errstr, 12);
			if ($viart_file) {
				fputs($viart_file, "GET /products/md5sums.txt HTTP/1.0\r\n");
				fputs($viart_file, "Host: www.viart.com\r\n");
				fputs($viart_file, "Referer: http://www.viart.com\r\n");
				fputs($viart_file, "User-Agent: Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)\r\n\r\n");
				$b = false;
				while (!feof($viart_file)) {
					$str = fgets($viart_file);
					if(!$b) {
						if(strstr($str, "Content-Type: text/plain; charset=iso-8859-1")) {
							$b = true;
							fgets($viart_file);
						}
					}
					else	{
						$line = explode(";", $str);
						$array_files_info[$line[0]] = $line;
					}
				}
				fclose($viart_file);
			} else {
				$error_msg = CANNOT_CONNECT_SERVER_MSG;
			}
		}
		else {
			$array_dir_tree = read_dir($user_path);
			$i = 0;
			foreach($array_dir_tree as $v) {
				$root_dir = $v;
				$i++;
				
				if($root_dir == $user_path) {
					$file_path = $user_path;
				}
				else {
					$file_path = $user_path . "/" . $root_dir;
				}
				if(is_dir($file_path)) {
				
					if ($handle = opendir($file_path)) {
						while(false !== ($file = readdir($handle))) {
							if($file != ".." && $file != "." && (substr($file, 0, 1) != '.') && !is_dir($user_path . "/" . $root_dir . "/" . $file)) {
								if($root_dir == $user_path) {
									$array_files_info[$file][0] = $file;
									$array_files_info[$file][1] = @md5_file($file_path . "/" . $file);
									$array_files_info[$file][2] = get_ext_file($file);
								}
								else {
									$array_files_info[$root_dir . "/" . $file][0] = $root_dir . "/" . $file;
									$array_files_info[$root_dir . "/" . $file][1] = @md5_file($file_path . "/" . $file);
									$array_files_info[$root_dir . "/" . $file][2] = get_ext_file($file);
								}
								
								$i++;
							}
						}
					}
					closedir($handle);
				}
			}
		}
		return $array_files_info;
	}

	function get_ver_files($file_ext, $switch, $user_path) {
		$array_ver_files = array();
		$array_file_line = array();
		$array_file_line = get_files_info($user_path);
		if($switch == 1) {
			foreach($array_file_line as $file_info) {
				if(in_array(trim($file_info[2]), $file_ext)) $array_ver_files[] = $file_info[0];
			}
		}
		if($switch == 2) {
			$file_ext = explode(",", $file_ext);
			foreach($array_file_line as $file_info) {
				if(in_array(trim($file_info[0]), $file_ext)) $array_ver_files[] = $file_info[0];

			}
		}			
		return $array_ver_files;
	}
	function join_files($array_dir_tree, $array_new_files, $array_del_files, $array_files_info, $user_path) {
		$i = 0;
		if(is_array($array_dir_tree) && count($array_dir_tree) > 0) {
			foreach($array_dir_tree as $v) {
				$array_join_files[$i]["path_name_s"] = $v;
				$array_join_files[$i]["m_time_s"] = date("d.m.y H:i", @filemtime("../" . $v));
				$array_join_files[$i]["checksum_s"] = @md5_file("../" . $v);
				$array_join_files[$i]["path_name_v"] = $v;
				$array_join_files[$i]["checksum_v"] = $array_files_info[$v][1];

				if($array_join_files[$i]["checksum_v"] === $array_join_files[$i]["checksum_s"]) $array_join_files[$i]["compare"] = ADMIN_NOT_CHANGED_MSG;
				else $array_join_files[$i]["compare"] = "<a href='admin_upgrade_diff_file.php?diff_file=" . $v . "&user_path=" . $user_path . "' class=error target='_blank'>".ADMIN_CHANGED_MSG."</a>";
				$i++;
			}
		}
		if(is_array($array_del_files) && count($array_del_files) > 0) {
			foreach($array_del_files as $v) {
				$array_join_files[$i]["path_name_s"] = $v;
				$array_join_files[$i]["checksum_s"] = $array_files_info[$v][1];
				$array_join_files[$i]["compare"] = "<DIV class=message>".NEW_FILE_MSG."</DIV>";
				$array_join_files[$i]["path_name_v"] = $v;
				$array_join_files[$i]["checksum_v"] = $array_files_info[$v][1];
				$array_join_files[$i]["m_time_s"] = "";
				$i++;
			}
		}
		if(is_array($array_new_files) && count($array_new_files) > 0) {
			foreach($array_new_files as $v) {
				$array_join_files[$i]["path_name_s"] = $v;
				$array_join_files[$i]["checksum_s"] = @md5_file("../" . $v);
				$array_join_files[$i]["path_name_v"] = $v;
				$array_join_files[$i]["checksum_v"] =  @md5_file("../" . $v);
				$array_join_files[$i]["compare"] = "<DIV class=comment>".UNKNOWN_MSG."</DIV>";
				$array_join_files[$i]["m_time_s"] = date("d.m.y H:i", @filemtime("../" . $v));
				$i++;
			}
		}
		return $array_join_files;
	}
	include_once("./admin_common.php");

	check_admin_security("site_settings");
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_upgrade_diff_files.html");
	$t->set_var("admin_upgrade_href", "admin_upgrade_diff.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	$rf0 = get_param("rf0");
	$array_files_info = array();
	$compare_type = get_param("compare_type");
	$user_path = get_param("folder_name");
	$error = "";
	if(strlen($user_path) > 0 ) {
		$user_path = str_replace("\\", "/", $user_path);
		$user_path = str_replace("../", "", $user_path);
		$array_user_path = explode("/", $user_path);
		$num_dirs = count($array_user_path);
		$html_path = htmlpath();
		$array_html_path = explode("/", $html_path);
		$num_html_dirs = count($array_html_path);
		$new_path = "";
		if($num_dirs < $num_html_dirs) {
			for($i = 1; $i <= abs($num_html_dirs - $num_dirs); $i++) $new_path .= "../";
			$user_path = $new_path . $user_path;
		}
		else {
			for($i = 1; $i < $num_html_dirs; $i++) $new_path .= "../";
			$user_path = $new_path . $user_path;
		}
		if(!is_dir($user_path)) {
			$error = INVALID_PATH_TO_FOLDER_MSG;
			header("Location:admin_upgrade_diff.php?error=" . $error);
			exit;
		}
	}
	if($compare_type == 1 || $compare_type == 3) {
		$array_files_info = get_files_info($user_path);
	}
	if($compare_type == 2) {
		$version = get_param("version");
	}
	$default_dir = "..";
	if($rf0 == 1) {
		$array_dir_tree = read_dir($default_dir);
		asort($array_dir_tree);
		$rf1 = get_param("rf1");
		$rf2 = get_param("rf2");
		$rf3 = get_param("rf3");
		if($rf1)	$array_files_ext[] = "php";
		if($rf2)	$array_files_ext[] = "html";
		$array_dir_tree = read_cur_files($array_dir_tree, $array_files_ext, $default_dir);
		$array_ver_files = get_ver_files($array_files_ext, 1, $user_path);
	}
	elseif($rf0 == 2) {
		$selected_files = get_param("files");
		$array_ver_files = get_ver_files($selected_files, 2, $user_path);
		$array_dir_tree = explode(",", $selected_files);
		foreach($array_dir_tree as $k =>  $v) {
			if(is_dir($v) || $v == ".." || $v == ".") {
				unset($array_dir_tree[$k]);
			}
		}
	}
	$array_new_files = array_diff($array_dir_tree, $array_ver_files);
	$array_dir_tree = array_diff($array_dir_tree, $array_new_files);
	$array_del_files = array_diff($array_ver_files, $array_dir_tree);
	$array_join_files = join_files($array_dir_tree, $array_new_files, $array_del_files, $array_files_info, $user_path);
	$nbsp = "";
	foreach($array_join_files as $k => $file) {
		$t->set_var("file_name", $file["path_name_s"]);
		$t->set_var("ver_file_name", $file["path_name_v"]);
		$t->set_var("compare_files", $file["compare"]);
		$t->set_var("m_time", $file["m_time_s"]);
		$t->parse("files", true);
	}
	$t->pparse("main", false);
?>