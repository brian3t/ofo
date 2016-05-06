<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  user_forum_attachments.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");
	include_once("./includes/forums_functions.php");
	include_once("./messages/" . $language_code . "/forum_messages.php");
	include_once("./messages/" . $language_code . "/download_messages.php");	

	// make some checks
	$user_id = get_session("session_user_id");
	if (!$user_id) $user_id = 0; //guest attachments!
	$thread_id = get_param("thread_id");
	$forum_id  = get_param("forum_id");
	$vc        = get_param("vc");
	$operation = get_param("operation");

	$allowed_attachments = false;
	if ($thread_id) {		
		$sql  = " SELECT forum_id FROM " . $table_prefix ."forum WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$forum_id = $db->f(0);
			if (VA_Forums::check_permissions($forum_id, POST_ATTACHMENTS_PERM)) {
				$allowed_attachments = true;
			}
		} else {
			echo NO_TOPICS_MSG;
			exit;
		}
	} elseif ($forum_id) {
		if (VA_Forums::check_permissions($forum_id, POST_ATTACHMENTS_PERM)) {
			$allowed_attachments = true;			
		} else {
			$sql  = " SELECT forum_id FROM " . $table_prefix ."forum WHERE forum_id=" . $db->tosql($forum_id, INTEGER);
			$db->query($sql);
			if (!$db->next_record()) {
				echo NO_FORUMS_MSG;
				exit;
			}
		}
	} else {
		echo NO_FORUMS_MSG;
		exit;
	}	

	if (!$allowed_attachments) {
		check_user_session();
		echo "You are not allowed add attachments to this forum.";
		exit;
	}

	$forum_settings = array();
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='forum'";
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while ($db->next_record()) {
		$forum_settings[$db->f("setting_name")] = $db->f("setting_value");
	}

	$default_files_mask = "*.gif,*.jpg,*.jpeg,*.bmp,*.tiff,*.tif,*.png,*.ico,*.doc,*.txt,*.rtf,*.pdf,*.xls";
	$attachments_dir = get_setting_value($forum_settings, "attachments_dir", "images/forum/");
	$attachments_users_mask = get_setting_value($forum_settings, "attachments_users_mask", $default_files_mask);

	if (!preg_match("/\/|\\$/", $attachments_dir)) { $attachments_dir.= "/"; }

	$attachments_regexp = preg_replace("/\s/", "", $attachments_users_mask);
	$attachments_regexp = preg_quote($attachments_regexp, "/");
	$attachments_regexp = str_replace(array(",", ";", "\\*", "\\?"), array(")|(", ")|(", ".*", "."), $attachments_regexp);
	$attachments_regexp = "/^((" . $attachments_regexp . "))$/i";

	$t = new VA_Template($settings["templates_dir"]);
	$t->set_file("main","user_forum_attachments.html");
	$t->set_var("user_forum_attachments_href", "user_forum_attachments.php");

	$css_file = "";
	if (isset($settings["style_name"]) && $settings["style_name"]) {
		$css_file = "styles/" . $settings["style_name"];
		if (isset($settings["scheme_name"]) && $settings["scheme_name"]) {
			$css_file .= "_" . $settings["scheme_name"];
		}
		$css_file .= ".css";
	}
	$t->set_var("css_file", $css_file);

	$errors = "";

	if ($operation == "upload")
	{
		if (isset($_FILES)) {
			$tmp_name = $_FILES["newfile"]["tmp_name"];
			$filename = $_FILES["newfile"]["name"];
			$filesize = $_FILES["newfile"]["size"];
			$upload_error = isset($_FILES["newfile"]["error"]) ? $_FILES["newfile"]["error"] : "";
		} else {
			$tmp_name = $HTTP_POST_FILES["newfile"]["tmp_name"];
			$filename = $HTTP_POST_FILES["newfile"]["name"];
			$filesize = $HTTP_POST_FILES["newfile"]["size"];
			$upload_error = isset($HTTP_POST_FILES["newfile"]["error"]) ? $HTTP_POST_FILES["newfile"]["error"] : "";
		}

		if ($upload_error == 1) {
			$errors = "The uploaded file exceeds the max filesize directive.";
		} elseif ($upload_error == 2) {
			$errors = "The uploaded file exceeds the max filesize parameter.";
		} elseif ($upload_error == 3) {
			$errors = "The uploaded file was only partially uploaded.";
		} elseif ($upload_error == 4) {
			$errors = UPLOAD_SELECT_ERROR;
		} elseif ($upload_error == 6) {
			$errors = "Missing a temporary folder.";
		} elseif ($upload_error == 7) {
			$errors = "Failed to write file to disk.";
		} elseif ($tmp_name == "none" || !strlen($tmp_name)) {
			$errors = UPLOAD_SELECT_ERROR;
		} elseif (!(preg_match($attachments_regexp, $filename)) ) {
			$errors = UPLOAD_FORMAT_ERROR;
		}


		if (!strlen($errors))
		{
			// get attachments dir
			$filepath = $attachments_dir;

			$new_filename = $filename;
			$file_index = 0;
			while (file_exists($filepath . $new_filename)) {
				$file_index++;
				$delimiter_pos = strpos($filename, ".");
				if ($delimiter_pos) {
					$new_filename = substr($filename, 0, $delimiter_pos) . "_" . $file_index . substr($filename, $delimiter_pos);
				} else {
					$new_filename = $index . "_" . $filename;
				}
			}

			if(!@move_uploaded_file($tmp_name, $filepath . $new_filename)) {
				if (!is_dir($filepath)) {
					$errors = "The folder '" . $filepath . "' doesn't exist.";
				} elseif (!is_writable($filepath)) {
					$errors = str_replace("{folder_name}", $filepath, FOLDER_PERMISSION_MESSAGE);
				} else {
					$errors = "System can't create the file <b>" . $filepath . $filename . "</b>";
				}
			} else {
				chmod($filepath . $new_filename, 0766);
				
				// save attachment in the database
				$sql  = " INSERT INTO " . $table_prefix . "forum_attachments ";
				$sql .= " (user_id, forum_id, thread_id, message_id, attachment_status, file_name, file_path, ";
				if (!$user_id) {
					$sql .= "session_id, ";
				}
				$sql .= "date_added ) VALUES (";
				$sql .= $db->tosql($user_id, INTEGER) . ", ";
				$sql .= $db->tosql($forum_id, INTEGER) . ", ";
				$sql .= $db->tosql($thread_id, INTEGER, true, false) . ", ";
				$sql .= "0, 0, ";
				$sql .= $db->tosql($filename, TEXT) . ", ";
				$sql .= $db->tosql($filepath . $new_filename, TEXT) . ", ";
				if (!$user_id) {
					$sql .= $db->tosql(session_id(), TEXT) . ", ";
				}
				$sql .= $db->tosql(va_time(), DATETIME) . ") ";
				$db->query($sql);
				
				$errors = "";
			}
		}
	} elseif ($operation == "remove") {
		$atid = get_param("atid");
		$sql  = " SELECT file_path ";
		$sql .= " FROM " . $table_prefix . "forum_attachments ";
		$sql .= " WHERE forum_id=" . $db->tosql($forum_id, INTEGER);
		$sql .= " AND thread_id=" . $db->tosql($thread_id, INTEGER, true, false);
		$sql .= " AND user_id=" . $db->tosql($user_id, INTEGER);
		if (!$user_id) {
			$sql .= " AND session_id=" . $db->tosql(session_id(), TEXT);
		}
		$sql .= " AND message_id=0 ";
		$sql .= " AND attachment_status=0 ";
		$sql .= " AND attachment_id=" . $db->tosql($atid, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$file_path = $db->f("file_path");
			@unlink($file_path);
			$sql  = " DELETE FROM " . $table_prefix . "forum_attachments ";
			$sql .= " WHERE attachment_id=" . $db->tosql($atid, INTEGER);
			$db->query($sql);
		}		
	}

	$t->set_var("forum_id", $forum_id);
	$t->set_var("thread_id", $thread_id);
	
	$attachments_files = "";
	$sql  = " SELECT attachment_id, file_name, file_path, date_added ";
	$sql .= " FROM " . $table_prefix . "forum_attachments ";
	$sql .= " WHERE forum_id=" . $db->tosql($forum_id, INTEGER);
	$sql .= " AND thread_id=" . $db->tosql($thread_id, INTEGER, true, false);
	$sql .= " AND user_id=" . $db->tosql($user_id, INTEGER);
	if (!$user_id) {
		$sql .= " AND session_id=" . $db->tosql(session_id(), TEXT);
	}
	$sql .= " AND message_id=0 ";
	$sql .= " AND attachment_status=0 ";
	$sql .= " ORDER BY attachment_id ";
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$attachment_id = $db->f("attachment_id");
			$filename = $db->f("file_name");
			$filepath = $db->f("file_path");
			$date_added = $db->f("date_added", DATETIME);
			$attachment_vc = md5($attachment_id . $date_added[3].$date_added[4].$date_added[5]);
			$filesize = get_nice_bytes(filesize($filepath));
			if ($attachments_files) { $attachments_files .= "; "; }
			$attachments_files .= "<a href=&quot;forum_attachment.php?atid=" .$attachment_id. "&vc=".$attachment_vc."&quot; target=_blank>" . $filename . "</a> (" . $filesize . ")";
  
			$t->set_var("attachment_id", $attachment_id);
			$t->set_var("filename", $filename);
			$t->set_var("filesize", $filesize);
			$t->parse("attachments", true);
		} while ($db->next_record());
		
		$t->parse("attachments_block", false);
	}

	if (strlen($errors)) {
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	}	else {
		$t->set_var("errors", "");
	}

	$t->set_var("attachments_files", $attachments_files);

	$t->pparse("main");

?>