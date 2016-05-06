<?

	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "messages/".$language_code."/download_messages.php");
	include_once($root_folder_path."messages/".$language_code."/forum_messages.php");
	include_once("./admin_common.php");

	check_admin_security("forum");

	$atid = get_param("atid");
	$errors = "";

	if (strlen($atid)) {
		$sql  = " SELECT *  ";
		$sql .= " FROM " . $table_prefix . "forum_attachments ";
		$sql .= " WHERE attachment_id=" . $db->tosql($atid, INTEGER);
		$db->query($sql);
		if ($db->next_record()) {
			$attachment_id = $db->f("attachment_id");
			$filename = $db->f("file_name");
			$filepath = $db->f("file_path");
			$is_file_exists = file_exists($filepath);
			if (!$is_file_exists && file_exists("../".$filepath)) {
				$is_file_exists = true;
				$filepath = "../".$filepath;
			}
		} else {
			$errors = DOWNLOAD_WRONG_PARAM;
		}
	} else {
		$errors = DOWNLOAD_MISS_PARAM;
	}

	if (!$errors) {
		$fp = fopen($filepath, "rb");
		if(!$fp) {
			$errors = DOWNLOAD_PATH_ERROR;
		}
	}

	if ($errors) {
		echo $errors;
		exit;
	} else {
    $filesize = filesize ($filepath);

		if (ini_get("zlib.output_compression")) {
			ini_set("zlib.output_compression", "Off");
		}
		header("Pragma: private");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/octet-stream"); 
		if ($filesize) {
			header("Content-Length: " . $filesize); 
		}
		header("Content-Disposition: attachment; filename=$filename"); 
		header("Content-Transfer-Encoding: binary"); 

		// print the file to the output 
		while(!feof($fp)){
			//reset time limit for big files
			@set_time_limit(30);
			print(fread($fp,1024*8));
			flush(); 
		}
		fclose($fp);
	}

?>