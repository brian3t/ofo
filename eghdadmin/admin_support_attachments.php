<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_attachments.php                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
 	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
 	include_once($root_folder_path."messages/".$language_code."/download_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_support_attachments.html");

	$t->set_var("admin_support_attachments_href", "admin_support_attachments.php");

	$support_id = get_param("support_id");
	if (!$support_id) {
		$support_id = 0;
	}
	$dep_id = get_param("dep_id");
	$operation = get_param("operation");
	$current_index = 0;

	$errors = "";

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
			// get attachments dir
			$attachments_dir = "";
			if ($dep_id) {
				$sql  = " SELECT attachments_dir ";
				$sql .= " FROM " . $table_prefix . "support_departments sd ";
				$sql .= " WHERE dep_id=" . $db->tosql($dep_id, INTEGER);
				$attachments_dir = get_db_value($sql);
			}
			
			if (!$attachments_dir) {
			  $sql  = "SELECT setting_value FROM " . $table_prefix . "global_settings ";
				$sql .= "WHERE setting_type='support' AND setting_name='attachments_dir'";
				if ($multisites_version) {
					$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($root_site_id,INTEGER). ") ";
					$sql .= "ORDER BY site_id DESC ";
				}
				$attachments_dir = get_db_value($sql);
			}

			$filepath = $attachments_dir;

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
					$errors = UPLOAD_CREATE_ERROR . $filepath . $filename . "</b>";
				}
			} else {
				chmod($filepath . $new_filename, 0766);

				// save attachment in the database
				$sql  = " INSERT INTO " . $table_prefix . "support_attachments ";
				$sql .= " (support_id, admin_id, message_id, attachment_status, file_name, file_path, date_added) VALUES (";
				$sql .= $db->tosql($support_id, INTEGER) . ", ";
				$sql .= $db->tosql(get_session("session_admin_id"), INTEGER) . ", ";
				$sql .= "0, 0, ";
				$sql .= $db->tosql($filename, TEXT) . ", ";
				$sql .= $db->tosql($filepath . $new_filename, TEXT) . ", ";
				$sql .= $db->tosql(va_time(), DATETIME) . ") ";
				$db->query($sql);

				$errors = "";
			}
		}
	} else if ($operation == "remove") {
		$atid = get_param("atid");
		$sql  = " SELECT file_path ";
		$sql .= " FROM " . $table_prefix . "support_attachments ";
		$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
		$sql .= " AND admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
		$sql .= " AND message_id=0 ";
		$sql .= " AND attachment_status=0 ";
		$sql .= " AND attachment_id=" . $db->tosql($atid, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$file_path = $db->f("file_path");
			@unlink($file_path);
			$sql  = " DELETE FROM " . $table_prefix . "support_attachments ";
			$sql .= " WHERE attachment_id=" . $db->tosql($atid, INTEGER);
			$db->query($sql);
		}
	}

	$t->set_var("support_id", $support_id);
	$t->set_var("dep_id", $dep_id);
	
	$attachments_files = "";
	$sql  = " SELECT attachment_id, file_name, file_path ";
	$sql .= " FROM " . $table_prefix . "support_attachments ";
	$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
	$sql .= " AND admin_id=" . $db->tosql(get_session("session_admin_id"), INTEGER);
	$sql .= " AND message_id=0 ";
	$sql .= " AND attachment_status=0 ";
	$sql .= " ORDER BY attachment_id ";
	$db->query($sql);
	if ($db->next_record()) {
		do {
			$attachment_id = $db->f("attachment_id");
			$filename = $db->f("file_name");
			$filepath = $db->f("file_path");
			$filesize = get_nice_bytes(filesize($filepath));
			if ($attachments_files) { $attachments_files .= "; "; }
			$attachments_files .= "<a href=&quot;admin_support_attachment.php?atid=" .$attachment_id. "&quot; target=_blank>" . $filename . "</a> (" . $filesize . ")";
  
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