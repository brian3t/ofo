<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_ranks.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path."includes/sorter.php");
	include_once($root_folder_path."includes/navigator.php");
	include_once($root_folder_path."includes/record.php");
	include_once($root_folder_path."includes/url.php");
	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support_users_priorities");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_support_ranks.html");

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_rank_href",  "admin_support_rank.php");
	$t->set_var("admin_support_ranks_href", "admin_support_ranks.php");
	$t->set_var("date_edit_format", join("", $date_edit_format));
	
	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_support_ranks.php");
	$s->set_sorter(ID_MSG, "sorter_user_priority_id", "1", "sup.user_priority_id");
	$s->set_sorter(USER_NAME_MSG, "sorter_user_name", "2", "u.name, u.first_name, u.last_name, u.login");
	$s->set_sorter(CUSTOMER_EMAIL_MSG, "sorter_user_email", "3", "sup.user_email");
	$s->set_sorter(PRIORITY_MSG, "sorter_priority_name", "4", "sp.priority_name");
	$s->set_sorter(EXPIRY_DATE_MSG, "sorter_priority_expiry", "5", "sup.priority_expiry");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_support_ranks.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$support_priorities = get_db_values("SELECT priority_id, priority_name FROM " . $table_prefix . "support_priorities", array(array("", "")));
		
	$r = new VA_Record($table_prefix . "users");
	$r->add_textbox("s_ne", TEXT);
	$r->add_select("s_pt", INTEGER, $support_priorities);
	$r->change_property("s_ne", TRIM, true);
	$r->add_textbox("s_ed", DATE, END_DATE_MSG);
	$r->change_property("s_ed", VALUE_MASK, $date_edit_format);
	$r->change_property("s_ed", TRIM, true);
	$r->get_form_parameters();
	$r->validate();
	$r->set_form_parameters();
	
	$where = "";
	$from_b = "";
	$from = "";
	if (!$r->errors)
	{
		if (!$r->is_empty("s_ne")) {
			if (strlen($where)) { $where .= " AND "; }
			$s_ne_sql = $db->tosql($r->get_value("s_ne"), TEXT, false);
			$where .= " (u.email LIKE '%" . $s_ne_sql . "%'";
			$where .= " OR u.login LIKE '%" . $s_ne_sql . "%'";
			$where .= " OR u.name LIKE '%" . $s_ne_sql . "%'";
			$where .= " OR u.first_name LIKE '%" . $s_ne_sql . "%'";
			$where .= " OR u.last_name LIKE '%" . $s_ne_sql . "%'";
			$where .= " OR u.company_name LIKE '%" . $s_ne_sql . "%')";
			$from_b .= "(";
			$from .= " LEFT JOIN " . $table_prefix . "users u ON u.user_id=sup.user_id) ";
		}
		if (!$r->is_empty("s_pt")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " sup.priority_id=" . $db->tosql($r->get_value("s_pt"), INTEGER);
		}
		if (!$r->is_empty("s_ed")) {
			if (strlen($where)) { $where .= " AND "; }
			$end_date = $r->get_value("s_ed");
			$day_after_end = mktime (0, 0, 0, $end_date[MONTH], $end_date[DAY] + 1, $end_date[YEAR]);
			$where .= " sup.priority_expiry<" . $db->tosql($day_after_end, DATE);
		}
	}
	$where_sql = ""; $where_and_sql = "";
	if (strlen($where)) {
		$where_sql = " WHERE " . $where;
		$where_and_sql = " AND " . $where;
	}

	$admin_support_rank = new VA_URL("admin_support_rank.php");
	$admin_support_rank->add_parameter("page", REQUEST, "page");
	$admin_support_rank->add_parameter("s_ne", REQUEST, "s_ne");
	$admin_support_rank->add_parameter("s_pt", REQUEST, "s_pt");
	$admin_support_rank->add_parameter("s_ed", REQUEST, "s_ed");
	$t->set_var("admin_support_rank_new_url", $admin_support_rank->get_url());
	
	// set up variables for navigator
	$sql  = "SELECT COUNT(*) FROM " . $from_b . $table_prefix . "support_users_priorities sup " . $from;
	$sql .= $where_sql;
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT sup.user_priority_id, u.name, u.first_name, u.last_name, u.login, sup.user_email, ";
	$sql .= " sp.priority_name, sup.priority_expiry ";
	$sql .= " FROM ((" . $table_prefix . "support_users_priorities sup ";
	$sql .= " LEFT JOIN " . $table_prefix . "users u ON u.user_id=sup.user_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_priorities sp ON sp.priority_id=sup.priority_id) ";
	$sql .= $where_sql;
	$sql .= $s->order_by;
	$db->query($sql);
	if($db->next_record())
	{
		$admin_support_rank->add_parameter("user_priority_id", DB, "user_priority_id");

		$current_ts = va_timestamp();
		$t->set_var("no_records", "");
		$t->parse("sorters", false);
		do {
			$user_name = $db->f("name");
			$priority_expiry = $db->f("priority_expiry", DATETIME);

			if (!$user_name) {
				$user_name = trim($db->f("first_name") . " " . $db->f("last_name"));
			}
			if (!$user_name) {
				$user_name = $db->f("login");
			}
			$t->set_var("user_priority_id", $db->f("user_priority_id"));
			$t->set_var("user_name", $user_name);
			$t->set_var("user_email", $db->f("user_email"));
			$t->set_var("priority_name", $db->f("priority_name"));
			$t->set_var("priority_expiry", "");
			if (is_array($priority_expiry)) {
				$priority_expiry_ts = va_timestamp($priority_expiry); 
				$expiry_formatted = va_date($date_show_format, $priority_expiry);
				if ($priority_expiry_ts < $current_ts) {
					$t->set_var("priority_expiry", "<font color=\"red\">".$expiry_formatted."</font>");
				} else {
					$t->set_var("priority_expiry", "<font color=\"blue\">".$expiry_formatted."</font>");
				}
			}
			$t->set_var("admin_support_rank_url", $admin_support_rank->get_url());

			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

?>
