<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_order_user_select.php                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once ($root_folder_path . "includes/parameters.php");
	include_once("./admin_common.php");

	check_admin_security("create_orders");

	$sw = trim(get_param("sw"));

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_order_user_select.html");
	$t->set_var("admin_order_user_select_href", "admin_order_user_select.php");
	$t->set_var("sw", htmlspecialchars($sw));

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_order_user_select.php");
	$s->set_parameters(false, true, true, false);
	$s->set_default_sorting("1", "desc");
	$s->set_sorter(ID_MSG, "sorter_user_id", "1", "user_id");
	$s->set_sorter(LOGIN_FIELD, "sorter_login", "2", "login");
	$s->set_sorter(NAME_MSG, "sorter_name", "3", "name");
	$s->set_sorter(FIRST_NAME_FIELD, "sorter_first_name", "4", "first_name");
	$s->set_sorter(LAST_NAME_FIELD, "sorter_last_name", "5", "last_name");
	$s->set_sorter(PHONE_FIELD, "sorter_phone", "6", "phone");

	// additional connection to get CC details for user
	$db_cc = new VA_SQL();
	$db_cc->DBType      = $db_type;
	$db_cc->DBDatabase  = $db_name;
	$db_cc->DBHost      = $db_host;
	$db_cc->DBPort      = $db_port;
	$db_cc->DBUser      = $db_user;
	$db_cc->DBPassword  = $db_password;
	$db_cc->DBPersistent= $db_persistent;

	$where = "";
	$sa = array();
	if ($sw) {
		$sa = split(" ", $sw);
		for ($si = 0; $si < sizeof($sa); $si++) {
			if ($where) {
				$where .= " AND "; 
			} else {
				$where .= " WHERE ";
			}
			$where .= " (login LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
			$where .= " OR name LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
			$where .= " OR first_name LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
			$where .= " OR last_name LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%'";
			$where .= " OR phone LIKE '%" . $db->tosql($sa[$si], TEXT, false) . "%')";
		}
	}

	$sql = "SELECT COUNT(*) FROM " . $table_prefix . "users " . $where;
	$total_records = get_db_value($sql);

	// set up variables for navigator
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_order_user_select.php");
	$records_per_page = 15;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", MOVING, $pages_number, $records_per_page, $total_records, false);
	
	// get payment_id used for Call Center
	$sql = "SELECT payment_id FROM " . $table_prefix . "payment_systems WHERE is_call_center = 1";
	$payment_id = intval(get_db_value($sql));
	
	// set CC settings
	$cc_info = array();
	if ($payment_id) {
		$setting_type = "credit_card_info_" . $payment_id;
		$sql = "SELECT setting_name, setting_value FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
		if ($multisites_version) {
			$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id,INTEGER,true,false) . ") ";
			$sql .= " ORDER BY site_id ASC ";
		}
		$db->query($sql);
		while ($db->next_record()) {
			$cc_info[$db->f("setting_name")] = $db->f("setting_value");
		}
	}
	$cc_number_security = get_setting_value($cc_info, "cc_number_security", 0);
	$cc_code_security = get_setting_value($cc_info, "cc_code_security", 0);

	$sql  = "SELECT * FROM " . $table_prefix . "users ";
	$sql .= $where;
	$sql .= $s->order_by;
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql);
	if ($db->next_record()) {
		$t->parse("users_sorters", false);
		do {
			$user_id = $db->f("user_id");
			$login = $db->f("login");
			$name = $db->f("name");
			$first_name = $db->f("first_name");
			$last_name = $db->f("last_name");
			$phone = $db->f("phone");

			if (is_array($sa)) {
				for ($si = 0; $si < sizeof($sa); $si++) {
					$login = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $login);
					$name = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $name);
					$first_name = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $first_name);
					$last_name = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $last_name);
					$phone = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $phone);
				}
			}

			$t->set_var("user_id", $user_id);
			$t->set_var("login", $login);
			$t->set_var("name", $name);
			$t->set_var("first_name", $first_name);
			$t->set_var("last_name", $last_name);
			$t->set_var("phone", $phone);

			$t->parse("users", true);
			
			// Set users' info
			$t->parse("users_info", true);
			$params_count = sizeof($call_center_user_parameters);
			for ($i = 0; $i < $params_count; $i++) { 
				$param_name = $call_center_user_parameters[$i];
				$t->set_var("param_name", $param_name);
				$param_value = str_replace("'", "\\'", htmlspecialchars($db->f($param_name)));
				$t->set_var("param_value", $param_value);
				$t->parse("user_info", true);
			}
			$db_cc->RecordsPerPage = 1;
			if ($db_type == 'mysql') {				
				$sql  = " SELECT cc_name, cc_first_name, cc_last_name, cc_number, YEAR(cc_start_date) as cc_start_year, MONTH(cc_start_date) as cc_start_month, ";
				$sql .= " YEAR(cc_expiry_date) as cc_expiry_year, MONTH(cc_expiry_date) as cc_expiry_month, cc_type, cc_issue_number, cc_security_code, pay_without_cc ";
				$sql .= " FROM " . $table_prefix . "orders ";
				$sql .= " WHERE user_id = " . $db_cc->tosql($user_id, INTEGER) . " AND payment_id = " . $payment_id;
				$sql .= " ORDER BY order_id DESC ";
				$db_cc->query($sql);
				if ($db_cc->next_record()) {
					$params_count = sizeof($cc_parameters);
					for ($i = 0; $i < $params_count; $i++) { 
						$param_name = $cc_parameters[$i];
						$t->set_var("param_name", $param_name);
						$param_value = $db_cc->f($param_name);
						if (($param_name == "cc_number" && $cc_number_security > 0) || ($param_name == "cc_security_code" && $cc_code_security > 0)) {
							$param_value = va_decrypt($param_value);
						}
						$param_value = str_replace("'", "\\'", htmlspecialchars($param_value));
						$t->set_var("param_value", $param_value);
						$t->parse("user_info", true);
					}
					$t->set_var("param_name", "cc_start_year");
					$t->set_var("param_value", $db_cc->f("cc_start_year"));
					$t->parse("user_info", true);
					$t->set_var("param_name", "cc_start_month");
					$t->set_var("param_value", $db_cc->f("cc_start_month"));
					$t->parse("user_info", true);
					$t->set_var("param_name", "cc_expiry_year");
					$t->set_var("param_value", $db_cc->f("cc_expiry_year"));
					$t->parse("user_info", true);
					$t->set_var("param_name", "cc_expiry_month");
					$t->set_var("param_value", $db_cc->f("cc_expiry_month"));
					$t->parse("user_info", true);
				}				
			} else {				
				$sql  = " SELECT cc_name, cc_first_name, cc_last_name, cc_number, cc_start_date, cc_expiry_date, cc_type, cc_issue_number, cc_security_code, pay_without_cc ";
				$sql .= " FROM " . $table_prefix . "orders ";
				$sql .= " WHERE user_id = " . $db_cc->tosql($user_id, INTEGER) . " AND payment_id = " . $payment_id;
				$sql .= " ORDER BY order_id DESC ";
				$db_cc->query($sql);
				if ($db_cc->next_record()) {
					$params_count = sizeof($cc_parameters);
					for ($i = 0; $i < $params_count; $i++) { 
						$param_name = $cc_parameters[$i];
						$t->set_var("param_name", $param_name);
						$param_value = $db_cc->f($param_name);
						if (($param_name == "cc_number" && $cc_number_security > 0) || ($param_name == "cc_security_code" && $cc_code_security > 0)) {
							$param_value = va_decrypt($param_value);
						}
						$param_value = str_replace("'", "\\'", htmlspecialchars($param_value));
						$t->set_var("param_value", $param_value);
						$t->parse("user_info", true);
					}
					
					$tmp = explode('-', $db->f('cc_start_date'));
					$cc_start_year = ''; $cc_start_month ='';
					if ($tmp && count($tmp)>1) {
						$cc_start_year  = $tmp[0];
						$cc_start_month = $tmp[1];
					}
					
					$tmp = explode('-', $db->f('cc_expiry_date'));
					$cc_expiry_year = ''; $cc_expiry_month ='';
					if ($tmp && count($tmp)>1) {
						$cc_expiry_year  = $tmp[0];
						$cc_expiry_month = $tmp[1];
					}
					$t->set_var("param_name", "cc_start_year");
					$t->set_var("param_value", $cc_start_year);
					$t->parse("user_info", true);
					$t->set_var("param_name", "cc_start_month");
					$t->set_var("param_value", $cc_start_month);
					$t->parse("user_info", true);
					$t->set_var("param_name", "cc_expiry_year");
					$t->set_var("param_value", $cc_expiry_year);
					$t->parse("user_info", true);
					$t->set_var("param_name", "cc_expiry_month");
					$t->set_var("param_value", $cc_expiry_month);
					$t->parse("user_info", true);
				}
			}
			// end users' info

		} while ($db->next_record());
	}

	if (strlen($sw)) {
		$found_message = str_replace("{found_records}", $total_records, USERS_MATCHING_TERMS_MSG);
		$found_message = str_replace("{search_string}", htmlspecialchars($sw), $found_message);
		$t->set_var("found_message", $found_message);
		$t->parse("search_results", false);
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>