<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  bc.php                                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	session_start();

	// include common files
	include_once("./includes/var_definition.php");
	include_once("./includes/constants.php");
	include_once("./includes/common_functions.php");
	include_once("./includes/va_functions.php");
	include_once("./includes/db_$db_lib.php");
	$language_code = get_language("messages.php");
	include_once("./messages/".$language_code."/messages.php");
	include_once("./includes/date_functions.php");

	// Database Initialize
	$db = new VA_SQL();
	$db->DBType      = $db_type;
	$db->DBDatabase  = $db_name;
	$db->DBHost      = $db_host;
	$db->DBPort      = $db_port;
	$db->DBUser      = $db_user;
	$db->DBPassword  = $db_password;
	$db->DBPersistent= $db_persistent;

	$b = get_param("b");
	$session_start = get_session("session_start");
	if (strlen($b)) {
		$sql  = " SELECT target_url ";
		$sql .= " FROM " . $table_prefix . "banners ";
		$sql .= " WHERE banner_id=" . $db->tosql($b, INTEGER);
		$sql .= " AND is_active=1 ";
		$db->query($sql);
		if ($db->next_record()) {
			$target_url = $db->f("target_url");
			$session_bc = get_session("session_bc");
			$user_id = get_session("session_user_id");
			if (!strlen($user_id)) { $user_id = 0; }

			if ($session_start && !isset($session_bc[$b])) {
				// add click information
				$sql  = " INSERT INTO " . $table_prefix . "banners_clicks ";
				$sql .= " (banner_id, user_id, remote_address, click_date) VALUES (";
				$sql .= $db->tosql($b, INTEGER) . ", ";
				$sql .= $db->tosql($user_id, INTEGER) . ", ";
				$sql .= $db->tosql(get_ip(), TEXT) . ", ";
				$sql .= $db->tosql(va_time(), DATETIME) . ") ";
				$db->query($sql);

				// add one click for banner
				$sql  = " UPDATE " . $table_prefix . "banners ";
				$sql .= " SET total_clicks=total_clicks+1 ";
				$sql .= " WHERE banner_id=" . $db->tosql($b, INTEGER);
				$db->query($sql);
			}
			// save click in session
			$session_bc[$b] = 1;
			set_session("session_bc", $session_bc);
			
			// redirect to target url
			header("Location: " . $target_url);
			exit;

		} else {
			echo "Wrong URL";
			exit;
		}
	} else {
		echo "Wrong URL";
		exit;
	}

?>