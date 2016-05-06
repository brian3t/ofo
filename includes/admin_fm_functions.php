<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_fm_functions.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	
function get_perm_file($file_name) {

	if(!is_dir($file_name) && !file_exists($file_name)) {
		return false;
	}
	$perms = fileperms($file_name);

	if (($perms & 0xC000) == 0xC000) $info = 's';
	elseif (($perms & 0xA000) == 0xA000) $info = 'l';
	elseif (($perms & 0x8000) == 0x8000) $info = '-';
	elseif (($perms & 0x6000) == 0x6000) $info = 'b';
	elseif (($perms & 0x4000) == 0x4000) $info = 'd';
	elseif (($perms & 0x2000) == 0x2000) $info = 'c';
	elseif (($perms & 0x1000) == 0x1000) $info = 'p';
	else $info = 'u';
// Owner
	$info .= (($perms & 0x0100) ? 'r' : '-');
	$info .= (($perms & 0x0080) ? 'w' : '-');
	$info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));
// Group
	$info .= (($perms & 0x0020) ? 'r' : '-');
	$info .= (($perms & 0x0010) ? 'w' : '-');
	$info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));
// World
	$info .= (($perms & 0x0004) ? 'r' : '-');
	$info .= (($perms & 0x0002) ? 'w' : '-');
	$info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));
	return $info;
}

function chmodnum($mode) {
	$realmode = "";
	$legal =  array("","w","r","x","-");
	$attarray = preg_split("//",$mode);
	for($i = 0; $i < count($attarray); $i++) {
		if ($key = array_search($attarray[$i],$legal)){
		   $realmode .= $legal[$key];
		}
	}
	$mode = str_pad($realmode,9,'-');
	$trans = array('-'=>'0','r'=>'4','w'=>'2','x'=>'1');
	$mode = strtr($mode,$trans);
	$newmode = '';
	$newmode .= $mode[0]+$mode[1]+$mode[2];
	$newmode .= $mode[3]+$mode[4]+$mode[5];
	$newmode .= $mode[6]+$mode[7]+$mode[8];
	return $newmode;
}

function rm($fileglob) {
	if (is_string($fileglob)) {
		if (is_file($fileglob)) {
			return @unlink($fileglob);
		}
		else {
			if(is_dir($fileglob)) {
				$ok = rm("$fileglob/*");
				if (! $ok) {
					return false;
				}
				return @rmdir($fileglob);
			} 
			else {
				$matching = glob($fileglob);
				if ($matching === false) {
					return false;
				}
				$rcs = array_map('rm', $matching);
				if (in_array(false, $rcs)) {
					return false;
				}
			}
		}
	}
	else {
		if (is_array($fileglob)) {
			$rcs = array_map('rm', $fileglob);
			if (in_array(false, $rcs)) {
				return false;
			}
		}
		else {
			return false;
		}
	}
	return true;
}

function read_dir($root, $array_dirs) {
	$path = $root;
	$i = 0;
	if ($handle = @opendir("./".$path)) {
		while ( false !== ($file = @readdir($handle))) {
			if($file != "." && $file != "..")
				if (is_dir($path."/".$file)) {
					if ($file != '..' && $file != '.' && $file != '' && (substr($file, 0, 1) != '.') && in_array($file, $array_dirs)) {
						$folders[$i]["path"] = $path."/".$file;
						$folders[$i]["name"] = $file;
						$i++;
					}
				}
		}
		closedir($handle);
		for ( $u = 0; $u < $i; $u++ ) {
			if ($handle = @opendir($folders[$u]["path"])) {
				while ( false !== ($file = @readdir($handle))) {
					if (is_dir("./" . $folders[$u]["path"]."/".$file)) {
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
	}
	return $folders;
}

function get_ext_file($file_name) {
	$parse_file = explode('.', $file_name);
	$parse_file_small = strtolower($parse_file[count($parse_file)-1]);
	return $parse_file_small;
}

function get_size($path, $conf_array_files_ext) {
	global $conf_root_dir, $conf_array_dirs;
	
	$size = 0;
	if(!is_dir($path) && file_exists($path)) {
		$size = filesize($path);
	}
	else {
		$atom_root_dir = explode("/", $path);
		if(!isset($atom_root_dir[1])) $atom_root_dir[1] = "";
		if(is_dir($path)) {
			$dir = @opendir($path);
			while($file = @readdir($dir)) {
				if($file != ".." && $file != ".") {
					if(is_readable($path . "/" . $file) && is_file($path . "/" . $file) && in_array(get_ext_file($file), $conf_array_files_ext) && ($atom_root_dir[0] == $conf_root_dir) && in_array($atom_root_dir[1], $conf_array_dirs))  {
						$size += filesize($path . "/" . $file);
					}
					elseif(is_readable($path . "/" . $file) && (is_dir($path . "/" . $file)) && $file != '..' && $file != '.' && $file != '' && (substr($file, 0, 1) != '.')) {
						$size +=get_size($path . "/" . $file, $conf_array_files_ext);
					}
				}
			}
		}
	}
	return $size;
}

//-- get_nice_bytes
function get_people_size($bytes) {
	if ($bytes >= 1024 && $bytes < 1048576) { 
		return floor($bytes / 1024) . " Kb";
	} else if ($bytes >= 1048576) { 
		return floor($bytes / 1048576) . " Mb";
	} else { 
		return $bytes." bytes";
	}
}

function get_num_files($path) {
	global $conf_array_files_ext, $conf_root_dir, $conf_array_dirs;
	$num_files = 0;
	if(is_dir($path)) {
		$dir = @opendir($path);

		$atom_root_dir = explode("/", $path);
		if(!isset($atom_root_dir[1])) $atom_root_dir[1] = "";

		while($file = @readdir($dir)) {
			if($file != ".." && $file != ".") {
				if(is_readable($path . "/" . $file) && is_file($path . "/" . $file) && in_array(get_ext_file($file), $conf_array_files_ext) && ($atom_root_dir[0] == $conf_root_dir) && in_array($atom_root_dir[1], $conf_array_dirs)) {
					$num_files++;
				}
				elseif(is_readable($path . "/" . $file) && (is_dir($path . "/" . $file)) && $file != '..' && $file != '.' && $file != '' && (substr($file, 0, 1) != '.')) {
					$num_files += get_num_files($path . "/" . $file);
				}
			}
		}
	}
	return	$num_files;
}

function read_current_dir($root_dir, $conf_array_dirs, $conf_root_dir)  {
	$content_cur_dir = "";
	if(is_dir($root_dir)) {
		$content_cur_dir[0]["dir_name"] =  "..";
		$content_cur_dir[0]["dir_delete"] =  "";
		$content_cur_dir[0]["dir_path"] = "";
		$content_cur_dir[0]["delete"] = "";
		$content_cur_dir[0]["rename"] = "";
		if ($handle = @opendir($root_dir)) {
			$i = 1;
			while(false !== ($file = readdir($handle))) {
				$bool = false;
				
				if ($file != '..' && $file != '.' && $file != '' && (substr($file, 0, 1) != '.')) {
					if($root_dir == $conf_root_dir) {
						if(in_array($file, $conf_array_dirs)) {
							$bool = true;
						}
						else {
							$bool = false;
						}
					}
					else {
						$bool = true;
					}
					if ((is_dir($root_dir."/".$file)) && $bool) {
						$content_cur_dir[$i]["dir_name"] =  $file;
						$content_cur_dir[$i]["dir_delete"] =  "javascript:del('" . $file . "', '" . $root_dir . "')";
						$content_cur_dir[$i]["dir_path"] = $root_dir;
						$content_cur_dir[$i]["delete"] = DELETE_BUTTON;
						$content_cur_dir[$i]["rename"] = RENAME_MSG;
						$i++;
					}
				}
			}
		}
		closedir($handle);
	}
	return $content_cur_dir;
}

function read_cur_files($root_dir, $conf_array_files_ext, $conf_root_dir) {
	$i = 0;
	$array_text_file = array("html", "htm", "css");
	$content_cur_file = array();
	if(is_dir($root_dir)) {
		if ($handle = @opendir($root_dir)) {
			while(false !== ($file = readdir($handle))) {
				$ext = get_ext_file($file);
				if(in_array($ext, $conf_array_files_ext)) {
					$content_cur_file[$i]["file_id"] = $file;
					$content_cur_file[$i]["perm"] = get_perm_file($root_dir . "/" . $file);
					$content_cur_file[$i]["dperm"] = 0 . chmodnum($content_cur_file[$i]["perm"]);
					$content_cur_file[$i]["view"] = VIEW_MSG;
					$content_cur_file[$i]["delete"] = DELETE_BUTTON;
					$content_cur_file[$i]["rename"] = RENAME_MSG;
					$content_cur_file[$i]["download"] = DOWNLOAD_TITLE;
					$content_cur_file[$i]["byte_size"] = filesize($root_dir . "/" . $file);
					$content_cur_file[$i]["file_size"] = get_people_size($content_cur_file[$i]["byte_size"]);
					if(in_array($ext, $array_text_file)) {
						$content_cur_file[$i]["edit"] = EDIT_MSG;
					}
					else {
						$content_cur_file[$i]["edit"] = "";
					}
					$content_cur_file[$i]["file_href"] = "javascript:del('" . $file . "', '" . $root_dir . "')";
					$content_cur_file[$i]["file_name"] = $file;
					$content_cur_file[$i]["dir_path"] = $root_dir;
					$i++;
				}
			}
		}
		closedir($handle);
	}
	return $content_cur_file;
}

function terminator($string) {
	return trim(strip_tags($string));
}

function check_dir_path($path_dir) {
	global $conf_root_dir, $conf_array_dirs;

	$path_dir = terminator($path_dir);
	$path_dir = str_replace("/..", "", $path_dir);
	$atom_root_dir = array();
	$atom_root_dir = explode("/", $path_dir);
	if(!isset($atom_root_dir[1])) $atom_root_dir[1] = "";
	if(($atom_root_dir[0] != $conf_root_dir) || !in_array($atom_root_dir[1], $conf_array_dirs) || !is_dir($path_dir)) {
		$path_dir = $conf_root_dir;
	}
	return $path_dir;
}

function check_error_files($path) {
	global $conf_array_files_ext, $conf_root_dir, $conf_array_dirs;
	
	$files = "";	
	if(!is_dir($path) && file_exists($path)) {
		return false;
	}
	else {
		$atom_root_dir = explode("/", $path);
		if(!isset($atom_root_dir[1])) $atom_root_dir[1] = "";
		if(is_dir($path)) {
			$dir = @opendir($path);
			while($file = readdir($dir)) {
				if(is_readable($path . "/" . $file) && is_file($path . "/" . $file) && !in_array(get_ext_file($file), $conf_array_files_ext) && ($atom_root_dir[0] == $conf_root_dir) && in_array($atom_root_dir[1], $conf_array_dirs))  {
					$files .= $file . ", ";
				}
				elseif(is_readable($path . "/" . $file) && (is_dir($path . "/" . $file)) && $file != '..' && $file != '.' && $file != '' && (substr($file, 0, 1) != '.')) {
					$files .= check_error_files($path . "/" . $file);
				}
			}
		}
	}
	return $files;
}

function check_dir_and_file($path, $file, $action, $file2 = "") {
	global $conf_array_files_ext, $conf_array_dirs, $array_text_files, $array_image_file, $array_download_file, $array_archive_file;
	
	$error = "";
	
	switch ($action){
		case "upload" :
			if (!in_array(get_ext_file($file), $conf_array_files_ext)){
				$error .= fm_errors(101,$file);
			}
			if ($path == ".."){
				$error .= fm_errors(102);
			}
			break;
		case "view" :
			if (!in_array(get_ext_file($file), $conf_array_files_ext)){
				$error .= fm_errors(101,$file);
			}
			if ($path == ".."){
				$error .= fm_errors(102);
			}
			break;
		case "edit" :
			if (!in_array(get_ext_file($file), $array_text_files)){
				$error .= fm_errors(101,$file);
			}
			if ($path == ".."){
				$error .= fm_errors(102);
			}
			break;
		case "new" :
			if (!in_array(get_ext_file($file), $array_text_files)){
				$error .= fm_errors(103,$file);
			}
			if ($path == ".."){
				$error .= fm_errors(104);
			}
			break;
		case "download":
			if (!in_array(get_ext_file($file), $conf_array_files_ext)){
				$error .= fm_errors(105,$file);
			}
			if ($path == ".."){
				$error .= fm_errors(102);
			}
			break;
		case "rename" :
			if (!in_array(get_ext_file($file), $conf_array_files_ext)){
				if (!in_array(get_ext_file($file2), $conf_array_files_ext)){
					$error .= fm_errors(107,$file2,$file);
				} else {
					$error .= fm_errors(106,$file);
				}
			} else {
				if (!in_array(get_ext_file($file2), $conf_array_files_ext)){
					$error .= fm_errors(106,$file2);
				}
			}
			if ($path == ".."){
				$error .= fm_errors(102);
			}
			break;
		case "chmod" :
			if ($path == ".."){
				$error .= fm_errors(102);
			}
			break;
	}
	return $error;
}

function fm_errors($code, $parameter = "", $parameter2 = ""){
	switch ($code) {
		case 101 :
			$error = "File " . $parameter . " was not opened - invalid file extension.<br>";
			break;
		case 102 :
			$error = "Directory was not opened - It may be because the folder doesn't exists or you do not have the permissions.";
			break;
		case 103 :
			$error = "Can't create file " . $parameter . " - invalid file extension or file name.<br>";
			break;
		case 104 :
			$error = "Can't create file in this dir. This dir don't exists.<br>";
			break;
		case 105 :
			$error = "File " . $parameter . " was not downloads - invalid file extension or file name.<br>";
			break;
		case 106 :
			$error = "Can't rename file " . $parameter . " - invalid file extension or file name.<br>";
			break;
		case 107 :
			$error = "Can't rename file " . $parameter . " to " . $parameter2 . " - invalid file extension.<br>";
			break;
		case 108 :
			$error = "Can't rename file " . $parameter . " to " . $parameter2 . " - invalid file extension.<br>";
			break;
		case 109 :
			$error = "File " . $parameter . " was not rename to " . $parameter2 . ". It may be because the file doesn't exists or you do not have the permissions.";
			break;
		case 110 :
			$error = "Can't create directory " . $parameter . " in this dir. It may be because this dir exists or you do not have the permissions.";
			break;
		case 111 :
			$error = "Can't change chmod for file " . $parameter . " in directory " . $parameter2 . ". It may be because this dir exists or you do not have the permissions.";
			break;
		case 112 :
			$error = "Can't change chmod for directory " . $parameter . ". It may be because you do not have the permissions.";
			break;
		case 113 :
			$error = "Can't remove file or directory " . $parameter . ". It may be because you do not have the permissions.";
			break;
		case 114 :
			$error = "Can't upload file " . $parameter . " please check the path or permissions";;
			break;
		case 115 :
			$error = "Can't upload file " . $parameter . " invalid file extension.";
			break;
		case 116 :
			$error = "File " . $parameter . " was not saved. It may be because the file doesn't exists or you do not have the permissions to save.";
			break;
	}
	return $error;
}
?>