<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_forum_attachments.php                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
 	include_once($root_folder_path."messages/".$language_code."/forum_messages.php");
	include_once("./admin_common.php");
	include_once($root_folder_path."messages/".$language_code."/download_messages.php");

	check_admin_security("forum");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_forum_attachments.html");

	$t->set_var("admin_forum_attachments_href", "admin_forum_attachments.php");

	$forum_id = get_param("forum_id");
	$thread_id = get_param("thread_id");
	$message_id = get_param("message_id");
	$status = get_param("status");
	$operation = get_param("operation");
	if (!$thread_id && !$message_id) {
		$status = 0;
	}

	$current_index = 0;

	if ($message_id) {
		$sql  = " SELECT f.forum_id,fm.thread_id ";
		$sql .= " FROM (" . $table_prefix . "forum_messages fm ";
		$sql .= " INNER JOIN " . $table_prefix . "forum f ON fm.thread_id=f.thread_id) ";
		$sql .= " WHERE message_id=" . $db->tosql($message_id, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$forum_id = $db->f("forum_id");
			$thread_id = $db->f("thread_id");
		}
	} else if ($thread_id) {
		$sql  = " SELECT forum_id FROM " . $table_prefix . "forum ";
		$sql .= " WHERE thread_id=" . $db->tosql($thread_id, INTEGER);
		$forum_id = get_db_value($sql);
	}

	if (!$forum_id) {
		echo NO_FORUMS_MSG;
		exit;
	}

	$errors = "";

	// get forum settings
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

	if($operation == "upload")
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

		// get attachments dir
		$attachments_dir = get_setting_value($forum_settings, "attachments_dir", "images/forum/");
		$filepath = $attachments_dir;
		if (!is_dir($attachments_dir) && is_dir("../".$attachments_dir)) {
			$filepath = "../".$attachments_dir;
		}

		if ($upload_error == 1) {
			$errors = FILESIZE_DIRECTIVE_ERROR_MSG;
		} else if ($upload_error == 2) {
			$errors = FILESIZE_PARAMETER_ERROR_MSG;
		} else if ($upload_error == 3) {
			$errors = PARTIAL_UPLOAD_ERROR_MSG;
		} else if ($upload_error == 4) {
			$errors = NO_FILE_UPLOADED_MSG;
		} else if ($upload_error == 6) {
			$errors = TEMPORARY_FOLDER_ERROR_MSG;
		} else if ($upload_error == 7) {
			$errors = FILE_WRITE_ERROR_MSG;
		} else if ($tmp_name == "none" || !strlen($tmp_name)) {
			$errors = NO_FILE_UPLOADED_MSG;
		//} else if (!(preg_match("/((.gif)|(.jpg)|(.jpeg)|(.bmp)|(.tiff)|(.tif)|(.png)|(.ico)|(.doc)|(.txt)|(.rtf)|(.pdf)|(.swf))$/i", $filename)) ) {
			//$errors = "The file isn't allowed for uploading.";
		}

		if(!strlen($errors))
		{
			$new_filename = $filename;
			$file_index = 0;
			while (file_exists($filepath . $new_filename)) {
				$file_index++;
				$delimiter_pos = strpos($filename, ".");
				if($delimiter_pos) {
					$new_filename = substr($filename, 0, $delimiter_pos) . "_" . $file_index . substr($filename, $delimiter_pos);
				} else {
					$new_filename = $index . "_" . $filename;
				}
			}

			if(!@move_uploaded_file($tmp_name, $filepath . $new_filename)) {
				if (!is_dir($filepath)) {
					$errors = FOLDER_DOESNT_EXIST_MSG . $filepath ;
				} else if (!is_writable($filepath)) {
					$errors = str_replace("{folder_name}", $filepath, FOLDER_PERMISSION_MESSAGE);
				} else {
					$errors = UPLOAD_CREATE_ERROR . " <b>" . $filepath . $filename . "</b>";
				}
			} else {
				chmod($filepath . $new_filename, 0766);

				// save attachment in the database
				$sql  = " INSERT INTO " . $table_prefix . "forum_attachments ";
				$sql .= " (forum_id, thread_id, message_id, admin_id, attachment_status, file_name, file_path, date_added) VALUES (";
				$sql .= $db->tosql($forum_id, INTEGER, true, false) . ", ";
				$sql .= $db->tosql($thread_id, INTEGER, true, false) . ", ";
				$sql .= $db->tosql($message_id, INTEGER, true, false) . ", ";
				$sql .= $db->tosql(get_session("session_admin_id"), INTEGER) . ", ";
				$sql .= $db->tosql($status, INTEGER, true, false) . ", ";
				$sql .= $db->tosql($filename, TEXT) . ", ";
				$sql .= $db->tosql($attachments_dir . $new_filename, TEXT) . ", ";
				$sql .= $db->tosql(va_time(), DATETIME) . ") ";
				$db->query($sql);

				$errors = "";
			}
		}
	} else if ($operation == "remove") {
		$atid = get_param("atid");
		$sql  = " SELECT file_path ";
		$sql .= " FROM " . $table_prefix . "forum_attachments ";
		$sql .= " WHERE forum_id=" . $db->tosql($forum_id, INTEGER);
		$sql .= " AND admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$sql .= " AND thread_id=" . $db->tosql($thread_id, INTEGER, true, false); 
		$sql .= " AND message_id=" . $db->tosql($message_id, INTEGER, true, false); 
		$sql .= " AND attachment_id=" . $db->tosql($atid, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$file_path = $db->f("file_path");
			$is_file_exists = file_exists($file_path);
			if (!$is_file_exists && file_exists("../".$file_path)) {
				$is_file_exists = true;
				$file_path = "../".$file_path;
			}
			@unlink($file_path);
			$sql  = " DELETE FROM " . $table_prefix . "forum_attachments ";
			$sql .= " WHERE attachment_id=" . $db->tosql($atid, INTEGER);
			$db->query($sql);
		}
	}

	$t->set_var("forum_id", $forum_id);
	$t->set_var("thread_id", $thread_id);
	$t->set_var("message_id", $message_id);
	$t->set_var("status", $status);
	
	$attachments_files = "";
	$sql  = " SELECT attachment_id, file_name, file_path ";
	$sql .= " FROM " . $table_prefix . "forum_attachments ";
	$sql .= " WHERE forum_id=" . $db->tosql($forum_id, INTEGER);
	$sql .= " AND thread_id=" . $db->tosql($thread_id, INTEGER, true, false); 
	$sql .= " AND message_id=" . $db->tosql($message_id, INTEGER, true, false); 
	if ($status) {
		$sql .= " AND attachment_status=1 ";
	} else {
		$sql .= " AND admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$sql .= " AND attachment_status=0 ";
	}
	$sql .= " ORDER BY attachment_id ";
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$attachment_id = $db->f("attachment_id");
			$filename = $db->f("file_name");
			$filepath = $db->f("file_path");
			$is_file_exists = file_exists($filepath);
			if (!$is_file_exists && file_exists("../".$filepath)) {
				$is_file_exists = true;
				$filepath = "../".$filepath;
			}
			$filesize = get_nice_bytes(filesize($filepath));
			if ($attachments_files) { $attachments_files .= "; "; }
			$attachments_files .= "<a href=&quot;admin_forum_attachment.php?atid=" .$attachment_id. "&quot; target=_blank>" . $filename . "</a> (" . $filesize . ")";
  
			$t->set_var("attachment_id", $attachment_id);
			$t->set_var("filename", $filename);
			$t->set_var("filesize", $filesize);
			$t->parse("attachments", true);
		} while ($db->next_record());
		$t->parse("attachments_block", false);
	}

	if(strlen($errors)) {
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	}	else {
		$t->set_var("errors", "");
	}

	$t->set_var("attachments_files", $attachments_files);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>