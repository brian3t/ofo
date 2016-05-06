<?php

	$is_admin_path = true;
	include("../includes/common.php");
	include_once("../messages/".$language_code."/download_messages.php");
	include_once("../messages/".$language_code."/messages.php");

	$t = new VA_Template("./");
	$t->set_file("main","editor_upload.html");

	if ((get_session("session_user_id") == '') && (get_session("session_admin_id") == '')) {
		$t->set_var("upload", "");
		$t->set_var("errors_list", REGISTERED_ACCESS_MSG);
		$t->parse("errors", false);
		$t->pparse("main");
		exit;
	}
	if (get_session("session_user_id") != ''){
		$settings_image = array();
		$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
		$sql .= " WHERE setting_type=" . $db->tosql('global', TEXT) . " AND setting_name LIKE 'user_image_%'";
		$db->query($sql);
		while($db->next_record()) {
			$settings_image[$db->f("setting_name")] = $db->f("setting_value");
		}
		$user_image_upload = get_setting_value($settings_image, "user_image_upload", 0);
		$max_image_size = get_setting_value($settings_image, "user_image_size", 51200);
		$max_image_width = get_setting_value($settings_image, "user_image_width", 640);
		$max_image_height = get_setting_value($settings_image, "user_image_height", 480);
	}

	$operation = get_param("operation");
	$filetype = get_param("filetype");
	$t->set_var("filetype", htmlspecialchars($filetype));

	$current_index = 0;
	$errors = "";
	$filename = "";

	if($operation == "1")
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
		$filename = strip($filename);

		if ($upload_error == 1) {
			$errors = "The uploaded file exceeds the max filesize directive.";
		} else if ($upload_error == 2) {
			$errors = "The uploaded file exceeds the max filesize parameter.";
		} else if ($upload_error == 3) {
			$errors = "The uploaded file was only partially uploaded.";
		} else if ($upload_error == 4) {
			$errors = "No file was uploaded.";
		} else if ($upload_error == 6) {
			$errors = "Missing a temporary folder.";
		} else if ($upload_error == 7) {
			$errors = "Failed to write file to disk.";
		} else if ($tmp_name == "none" || !strlen($tmp_name)) {
			$errors = "No file was uploaded.";
		} else if (!(preg_match("/((.gif)|(.jpg)|(.jpeg)|(.bmp)|(.tiff)|(.tif)|(.png)|(.ico)|(.doc)|(.txt)|(.rtf)|(.pdf)|(.swf))$/i", $filename)) ) {
			$errors = "Only images files allowed.";
		}
		if ((get_session("session_user_id") != '') && (get_session("session_admin_id") == '')){
			if (!$user_image_upload) {
				$errors = (!strlen($errors)) ? UPLOAD_ACCESS_ERROR : $errors.'<br>'.UPLOAD_ACCESS_ERROR;
			} else {
				if (filesize($tmp_name) > $max_image_size) {
					$errors = (!strlen($errors)) ? "" : $errors.'<br>';
					$errors .= str_replace("{filesize}", intval($max_image_size / 1024) . "kb", UPLOAD_SIZE_ERROR);
				}
				$image_params = @getimagesize($tmp_name);
				if($image_params[0] > $max_image_width || $image_params[1] > $max_image_height) {
					$errors = (!strlen($errors)) ? "" : $errors.'<br>';
					$errors .= str_replace("{dimension}", $max_image_width . "x" . $max_image_height, UPLOAD_DIMENSION_ERROR);
				}
			}
		}

		if(!strlen($errors))
		{
			if ($filetype == "product_editor") {
				$file_path = "images/products/editor/";
				$filepath = "../images/products/editor/";
			} else if ($filetype == "article_editor") {
				$file_path = "images/articles/editor/";
				$filepath = "../images/articles/editor/";
			} else if ($filetype == "ad_editor") {
				$file_path = "images/ads/editor/";
				$filepath = "../images/ads/editor/";
			} else if ($filetype == "forum_editor") {
				$file_path = "images/forum/editor/";
				$filepath = "../images/forum/editor/";
			} else if ($filetype == "user_editor") {
				$file_path = "images/users/editor/";
				$filepath = "../images/users/editor/";
			} else {
				$file_path = "images/editor/";
				$filepath = "../images/editor/";
			}

			$filename = check_file_exists($filepath, $filename);
			if(!@move_uploaded_file($tmp_name, $filepath . $filename))
			{
				if (!is_dir($filepath))
				{
					$errors = "The folder '" . $filepath . "' doesn't exist.";
				}
				else if (!is_writable($filepath))
				{
					$errors = str_replace("{folder_name}", $filepath, FOLDER_PERMISSION_MESSAGE);
				}
				else
				{
					$errors = "System can't create the file <b>" . $filepath . $filename . "</b>";
				}
			}
      		else
			{
				chmod($filepath . $filename, 0766);
				$t->set_var("filename", $settings["site_url"].$file_path.$filename);
				$image_params = @getimagesize($filepath . $filename);
				$t->set_var("image_width", $image_params[0]);
				$t->set_var("image_height", $image_params[1]);

				if (get_session("session_user_id") && (get_session("session_admin_id") == '')) {
					$sql  = " INSERT INTO " . $table_prefix . "users_files (user_id, file_type, file_name, file_path) VALUES (";
					$sql .= $db->tosql(get_session("session_user_id"), INTEGER) . ", ";
					$sql .= $db->tosql($filetype, TEXT) . ", ";
					$sql .= $db->tosql($filename, TEXT) . ", ";
					$sql .= $db->tosql($file_path . $filename, TEXT) . ") ";
					$db->query($sql);
				}

				$errors = "";
			}
		}
	}

	if(strlen($errors))
	{
		$t->set_var("after_upload", "");
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	}
	else
	{
		$t->set_var("errors", "");
	}

	$t->parse("upload", false);
	$t->pparse("main");

function check_file_exists($filepath, $filename)
{
   $everything_ok = false;
   global $current_index;
   $new_filename = $filename;
   while (!$everything_ok)
   {
      if(file_exists($filepath . $new_filename))
      {
         $new_filename = get_new_file_name ($filepath, $filename);
	    }
	    else
	    {
         if ($filepath == "../images/small/" || $filepath == "../images/big/" || $filepath == "../images/super/")
         {
            if(file_exists("../images/small/" . $new_filename))
            {
               $new_filename = get_new_file_name ("../images/small/", $filename);
            }
            else if(file_exists("../images/big/" . $new_filename))
            {
               $new_filename = get_new_file_name ("../images/big/", $filename);
            }
            else if(file_exists("../images/super/" . $new_filename))
            {
               $new_filename = get_new_file_name ("../images/super/", $filename);
            }
            else
            {
               $everything_ok = true;
            }
         }
         else
         {
            $everything_ok = true;
         }
	    }
   }
   return $new_filename;
}

function get_new_file_name ($filepath, $filename)
{
   global $current_index;
   $new_filename = $filename;
   while (file_exists($filepath . $new_filename))
   {
      $current_index++;
      $delimiter_pos = strpos($filename, ".");
      if($delimiter_pos)
      {
         $new_filename = substr($filename, 0, $delimiter_pos) . "_" . $current_index . substr($filename, $delimiter_pos);
      }
      else
      {
         $new_filename = $index . "_" . $filename;
      }
   }
   return $new_filename;
}

?>