<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  index.php                                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once("./admin_common.php");

	if (!strlen(get_session("session_admin_id")) || !strlen(get_session("session_admin_privilege_id"))) {
		// admin is not logged in, redirect him to login form
		header ("Location: admin_login.php");
		exit;
	}
	
	$current_version = va_version();
	// bookmarks are available only from version 2.8.1
	if (comp_vers($current_version, "2.8.1") <= 1) {
		$sql  = " SELECT url";
		$sql .= " FROM " . $table_prefix . "bookmarks ";
		$sql .= " WHERE is_start_page = 1 AND admin_id = " . $db->tosql(get_session("session_admin_id"), INTEGER, true,false);
		$db->query($sql);
		if ($db->next_record()) {
			$url = $db->f("url");
			header("Location: " . $url);
			exit;
		} else { 
			header("Location: admin.php"); 
			exit;
		}
	}

	header("Location: admin.php");
	exit;

?>