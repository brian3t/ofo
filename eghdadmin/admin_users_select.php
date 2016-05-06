<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_users_select.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once("../messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("site_users");

	$sw = trim(get_param("sw"));
	$form_name = get_param("form_name");
	$field_name = get_param("field_name");
	$selection_type = get_param("selection_type");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_users_select.html");
	$t->set_var("admin_users_select_href", "admin_users_select.php");
	$t->set_var("sw", htmlspecialchars($sw));
	$t->set_var("form_name", htmlspecialchars($form_name));
	$t->set_var("field_name", htmlspecialchars($field_name));
	$t->set_var("selection_type", htmlspecialchars($selection_type));

	$sql = " SELECT country_id, country_code FROM " . $table_prefix . "countries ";
	$countries = array();
	$db->query($sql);
	while ($db->next_record()) {
		$countries[$db->f("country_id")] = $db->f("country_code");
	}

	$sql = " SELECT state_id, state_code FROM " . $table_prefix . "states ";
	$states = array();
	$db->query($sql);
	while ($db->next_record()) {
		$states[$db->f("state_id")] = $db->f("state_code");
	}


	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_users_select.php");
	$s->set_parameters(false, true, true, false);
	$s->set_sorter(ID_MSG, "sorter_user_id", "1", "user_id");
	$s->set_sorter(USER_LOGIN_MSG, "sorter_login", "2", "login");
	$s->set_sorter(USER_NAME_MSG, "sorter_name", "3", "name, first_name, last_name, nickname", "name DESC, first_name DESC, last_name DESC, nickname DESC");
	$s->set_sorter(USER_EMAIL_MSG, "sorter_email", "4", "email");

	$where = "";
	$sa = array();
	if ($sw) {
		$sa = split(" ", $sw);
		for($si = 0; $si < sizeof($sa); $si++) {
			if ($where) { $where .= " AND "; }
			else { $where .= " WHERE "; }

			$sw_sql = $db->tosql($sa[$si], TEXT, false);
			$where .= " (u.email LIKE '%" . $sw_sql . "%'";
			$where .= " OR u.login LIKE '%" . $sw_sql . "%'";
			$where .= " OR u.name LIKE '%" . $sw_sql . "%'";
			$where .= " OR u.first_name LIKE '%" . $sw_sql . "%'";
			$where .= " OR u.last_name LIKE '%" . $sw_sql . "%'";
			$where .= " OR u.company_name LIKE '%" . $sw_sql . "%')";
		}
	}

	$sql = " SELECT COUNT(*) FROM " . $table_prefix . "users u " . $where;
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);

	// set up variables for navigator
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_users_select.php");
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", MOVING, $pages_number, $records_per_page, $total_records, false);

	$sql  = " SELECT user_id, login, email, name, first_name, last_name, nickname, company_name, country_id, state_id ";
	$sql .= "	FROM " . $table_prefix . "users u ";
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
			$user_name = $db->f("name");
			$user_email = $db->f("email");
			$country_id = $db->f("country_id");
			$state_id = $db->f("state_id");
			if (!strlen($user_name)) {
				$user_name = trim($db->f("first_name") . " " . $db->f("last_name"));
			}
			if (!strlen($user_name)) {
				$user_name = trim($db->f("nickname"));
			}
			if (!strlen($user_name)) {
				$user_name = $db->f("company_name");
			}

			$user_name_js = str_replace("'", "\\'", htmlspecialchars($user_name));

			if(is_array($sa)) {
				for($si = 0; $si < sizeof($sa); $si++) {
					$login = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $login);					
					$user_name = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $user_name);					
					$user_email = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $user_email);					
				}
			}
			$from = get_setting_value($countries, $country_id, "");
			$from_state = get_setting_value($states , $state_id, "");
			if ($from_state && $from) {
				$from .= ", ";
			}
			$from .= $from_state;

			$t->set_var("user_id", $user_id);
			$t->set_var("login", $login);
			$t->set_var("user_name", $user_name);
			$t->set_var("user_name_js", $user_name_js);
			$t->set_var("user_email", $user_email);
			$t->set_var("from", $from);

			$t->parse("users", true);
		} while ($db->next_record());
	}

	if (strlen($sw)) {
		$found_message = str_replace("{found_records}", $total_records, FOUND_USERS_MSG);
		$found_message = str_replace("{search_string}", htmlspecialchars($sw), $found_message);
		$t->set_var("found_message", $found_message);
		$t->parse("search_results", false);
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");


?>