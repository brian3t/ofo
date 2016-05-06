<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support");

	$permissions = get_permissions();
	$allow_close = get_setting_value($permissions, "support_ticket_close", 0); 
	$admin_id    = get_session("session_admin_id");

	$admin_support_close_url = new VA_URL("admin_support_reply.php", true);
	$admin_support_close_url->add_parameter("support_id", DB, "support_id");
	$admin_support_close_url->add_parameter("operation", CONSTANT, "close");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_support.html");

	///deleting tickets
	$operation = get_param("operation");
	$items_ids = get_param("items_ids");
	if ($operation == "delete_items" && strlen($items_ids)) {
		$items_for_del = explode(",",$items_ids);
		if (isset($permissions["support_ticket_edit"]) && $permissions["support_ticket_edit"] == 1) 
		{
			foreach($items_for_del as $item_for_del)
			{
	 			delete_tickets($item_for_del); 
			}
		}
		else 
		{
		  $t->set_var("error_delete","<font color=red>".REMOVE_TICKET_NOT_ALLOWED_MSG."<br></font>");
		}
	
	}

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_reply_href", "admin_support_reply.php");
	$t->set_var("admin_support_request_href", "admin_support_request.php");
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_support_priorities_href", "admin_support_priorities.php?rp=admin_support.php");
	$t->set_var("admin_support_statuses_href", "admin_support_statuses.php?rp=admin_support.php");
	$t->set_var("admin_support_products_href", "admin_support_products.php?rp=admin_support.php");
	$t->set_var("admin_support_types_href", "admin_support_types.php?rp=admin_support.php");
	$t->set_var("admin_support_settings_href", "admin_support_settings.php");
	$t->set_var("admin_support_prereplies_href", "admin_support_prereplies.php");        
	$t->set_var("admin_support_departments_href", "admin_support_departments.php");        
	$t->set_var("admin_support_admins_href", "admin_support_admins.php");     
	$t->set_var("admin_support_static_tables_href", "admin_support_departments.php");        

	
	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_support.php");
	$s->set_parameters(false, true, true, false);
	if ($db->DBType == "db2"){
		$s->set_sorter(NO_MSG, "sorter_id", "1", "sp.support_id");
		$s->set_sorter(SUPPORT_SUMMARY_COLUMN, "sorter_summary", "2", "sp.summary");
		$s->set_sorter(STATUS_MSG, "sorter_status", "3", "sp.status_name");
		$s->set_sorter(TYPE_MSG, "sorter_type", "4", "sp.type_name");
		$s->set_sorter(EMAIL_FIELD, "sorter_user", "5", "sp.user_email");
		$s->set_sorter(ASSIGNED_MSG, "sorter_admin_alias", "7", "sp.admin_alias");
		$s->set_sorter(LAST_UPDATED_MSG, "sorter_modified", "6", "sp.date_modified");
		$s->set_sorter(SITE_NAME_MSG, "sorter_site", "7", "sp.site_id");
		if (!$s->order_by) {
			$s->order_by = " ORDER BY sp.priority_rank, sp.date_modified DESC ";
		}
	} else {
		$s->set_sorter(NO_MSG, "sorter_id", "1", "s.support_id");
		$s->set_sorter(SUPPORT_SUMMARY_COLUMN, "sorter_summary", "2", "s.summary");
		$s->set_sorter(STATUS_MSG, "sorter_status", "3", "ss.status_name");
		$s->set_sorter(TYPE_MSG, "sorter_type", "4", "st.type_name");
		$s->set_sorter(EMAIL_FIELD, "sorter_user", "5", "s.user_email");
		$s->set_sorter(ASSIGNED_MSG, "sorter_admin_alias", "7", "a.admin_alias");
		$s->set_sorter(LAST_UPDATED_MSG, "sorter_modified", "6", "s.date_modified");
		$s->set_sorter(SITE_NAME_MSG, "sorter_site", "7", "s.site_id");
		if (!$s->order_by) {
			$s->order_by = " ORDER BY sp.priority_rank, s.date_modified DESC ";
		}
	}

	$s_am = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_support.php", "sort_am");
	$s_am->set_parameters(false, true, true, false);
	if ($db->DBType == "db2"){
		$s_am->set_sorter(NO_MSG, "sorter_id_am", "1", "sp.support_id");
		$s_am->set_sorter(SUPPORT_SUMMARY_COLUMN, "sorter_summary_am", "2", "sp.summary");
		$s_am->set_sorter(STATUS_MSG, "sorter_status_am", "3", "sp.status_name");
		$s_am->set_sorter(TYPE_MSG, "sorter_type_am", "4", "sp.type_name");
		$s_am->set_sorter(EMAIL_FIELD, "sorter_user_am", "5", "sp.user_email");
		$s_am->set_sorter(LAST_UPDATED_MSG, "sorter_modified_am", "6", "sp.date_modified");
		$s->set_sorter(SITE_NAME_MSG, "sorter_site_am", "7", "sp.site_id");
		if (!$s_am->order_by) {
			$s_am->order_by = " ORDER BY sp.priority_rank, sp.date_modified DESC ";
		}
	} else {
		$s_am->set_sorter(NO_MSG, "sorter_id_am", "1", "s.support_id");
		$s_am->set_sorter(SUPPORT_SUMMARY_COLUMN, "sorter_summary_am", "2", "s.summary");
		$s_am->set_sorter(STATUS_MSG, "sorter_status_am", "3", "ss.status_name");
		$s_am->set_sorter(TYPE_MSG, "sorter_type_am", "4", "st.type_name");
		$s_am->set_sorter(EMAIL_FIELD, "sorter_user_am", "5", "s.user_email");
		$s_am->set_sorter(LAST_UPDATED_MSG, "sorter_modified_am", "6", "s.date_modified");
		$s->set_sorter(SITE_NAME_MSG, "sorter_site_am", "7", "s.site_id");
		if (!$s_am->order_by) {
			$s_am->order_by = " ORDER BY sp.priority_rank, s.date_modified DESC ";
		}
	}

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_support.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$department_id = get_param("department_id");
	// check departments available for administrator
	$admin_departments_ids = ""; $admin_departments = array(); $departments_values = array(array("",""));
	$selected_department_id = ""; $selected_department = "";
	$sql  = " SELECT sd.dep_id,sd.short_title,sd.full_title ";
	$sql .= " FROM (" . $table_prefix . "support_users_departments sud ";
	$sql .= " INNER JOIN " .$table_prefix. "support_departments sd ON sud.dep_id=sd.dep_id) ";
	$sql .= " WHERE admin_id=" . $db->tosql($admin_id, INTEGER);
	$db->query($sql);
	while ($db->next_record()) {
		if (strlen($admin_departments_ids)) { $admin_departments_ids .= ","; }
		$admin_dep_id = $db->f("dep_id");
		$admin_dep_title = $db->f("short_title");
		$full_title = $db->f("full_title");
		$admin_departments_ids .= $admin_dep_id;
		if ($department_id == $admin_dep_id) {
			$selected_department_id = $admin_dep_id;
			$selected_department = $admin_dep_title;
		}
		$admin_departments[$admin_dep_id] = array("title" => $admin_dep_title);
		$departments_values[] = array($admin_dep_id, $full_title);
	}

	$sql="SELECT status_id, status_name, is_internal FROM " . $table_prefix . "support_statuses ORDER BY status_name ASC";
	$support_statuses = array(array("",""));
	$statuses_stats = array();
	$db->query($sql);
	while($db->next_record()) {
		$statuses_stats[$db->f("status_id")] = $db->f("status_name"); 
		if ($db->f("is_internal") == "1") {
			$support_statuses[] = array($db->f("status_id"), $db->f("status_name") . " (Internal)");
		} else {
			$support_statuses[] = array($db->f("status_id"), $db->f("status_name"));
		}
	}
	
	// show statistics
	foreach($admin_departments as $dep_id => $dep_info) {
		$t->set_var("dep_id", $dep_id);
		$t->set_var("department_title", $dep_info["title"]);
		$t->parse("deps_titles", true);
	}

	foreach($statuses_stats as $status_id => $status_name) {
		$t->set_var("statuses_deps", "");
		$t->set_var("status_id", $status_id);
		$t->set_var("status_name", $status_name);
		foreach($admin_departments as $dep_id => $dep_info) {
			$sql  = " SELECT COUNT(*) FROM ".$table_prefix."support ";
			$sql .= " WHERE dep_id=". $db->tosql($dep_id, INTEGER);
			$sql .= " AND support_status_id=". $db->tosql($status_id, INTEGER);
			$tickets_number = get_db_value($sql);
			if($tickets_number > 0) {
				$t->set_var("tickets_number", "<a href=\"admin_support.php?status_id=".$status_id."&department_id=".$dep_id."&s_in=2\"><b>" . $tickets_number."</b></a>"); 
			} else {
				$t->set_var("tickets_number", "0"); 
			}
			if (isset($admin_departments[$dep_id]["total"])) {
				$admin_departments[$dep_id]["total"] += $tickets_number;
			} else {
				$admin_departments[$dep_id]["total"] = $tickets_number;
			}
			$t->parse("statuses_deps", true);
		}
		$t->parse("statuses_stats", true);
	}

	// show totals by departments
	foreach($admin_departments as $dep_id => $dep_info) {
		$t->set_var("tickets_total", $dep_info["total"]);
		$t->parse("stats_totals", true);
	}

	// stats by helpdesk managers
	if (strlen($admin_departments_ids)) {
		$sql  = " SELECT s.admin_id_assign_to, a.admin_name, a.login, COUNT(*) as tickets_number ";
		$sql .= " FROM (" . $table_prefix . "support s ";
		$sql .= " LEFT JOIN " . $table_prefix . "admins a ON a.admin_id = s.admin_id_assign_to)  ";
		$sql .= " WHERE s.dep_id IN (" . $admin_departments_ids . ") ";
		$sql .= " AND s.admin_id_assign_to IS NOT NULL AND s.admin_id_assign_to<>0 ";
		if ($db_type == "mysql") {
			$sql .= " GROUP BY s.admin_id_assign_to ";
		} else {
			$sql .= " GROUP BY s.admin_id_assign_to, a.admin_name, a.login ";
		}
		$db->query($sql);
		if ($db->next_record()) {
			$admin_tickets_total = 0;
			$admin_support_admin_url = new VA_URL("admin_support.php", false);
			$admin_support_admin_url->add_parameter("department_id", CONSTANT, $department_id);
			$admin_support_admin_url->add_parameter("s_at", DB, "admin_id_assign_to");
			$admin_support_admin_url->add_parameter("s_in", CONSTANT, 2);
			$admin_support_admin_url->add_parameter("s_sti", REQUEST, "s_sti");
			do {
				$admin_login = $db->f("login");
				$admin_name = $db->f("admin_name");
				if (!$admin_name) { 
					$admin_name = $admin_login; 
					if (!$admin_name) { 
						$admin_name = "[No Name]"; 
					}
				} 
				$tickets_number = $db->f("tickets_number");
				$admin_tickets_total += $tickets_number;
  
				$t->set_var("admin_name", $admin_name);
				$t->set_var("tickets_number", $tickets_number);
				$t->set_var("admin_support_admin_url", $admin_support_admin_url->get_url());
  
				$t->parse("admins_tickets", true);
			} while ($db->next_record());
  
			$t->set_var("admin_tickets_total", $admin_tickets_total);
			$t->parse("admins_stats", false);
		}
	}

	$search_options = array(array(0, ACTIVE_MSG), array(1, HIDDEN_MSG), array(2, ALL_MSG));
	$s_at_values = array();
	if ($selected_department_id || $admin_departments_ids) {
		$sql  = " SELECT a.admin_id, a.admin_name ";
		$sql .= " FROM (" . $table_prefix . "admins a ";
		$sql .= " INNER JOIN " . $table_prefix . "support_users_departments sud ON a.admin_id=sud.admin_id) ";
		if ($selected_department_id) {
			$sql .= " WHERE sud.dep_id=" . $db->tosql($selected_department_id, INTEGER);
		} else if ($admin_departments_ids) {
			$sql .= " WHERE sud.dep_id IN (" . $admin_departments_ids . ") ";
		}
		$sql .= " GROUP BY a.admin_id, a.admin_name ";
		$s_at_values = get_db_values($sql, array(array("", "")));
	}
	if ($sitelist) {
		$sites = get_db_values("SELECT site_id, site_name FROM " . $table_prefix . "sites ORDER BY site_id ", array(array("", "")));
	}

	$r = new VA_Record("");
	$r->add_textbox("support_id_search", TEXT);
	$r->change_property("support_id_search", TRIM, true);
	$r->add_textbox("summary_search", TEXT);
	$r->change_property("summary_search", TRIM, true);
	$r->add_textbox("s_ne", TEXT);
	$r->change_property("s_ne", TRIM, true);
	$r->add_textbox("keyword_search", TEXT);
	$r->change_property("keyword_search", TRIM, true);
	if (sizeof($s_at_values) > 1) {
		$r->add_select("s_at", INTEGER, $s_at_values);
	}
	if (sizeof($departments_values) > 2) {
		$r->add_select("department_id", INTEGER, $departments_values);
	}
	$r->add_select("status_id", INTEGER, $support_statuses);
	$r->add_radio("s_in", TEXT, $search_options);
	if ($sitelist) {
		$r->add_select("s_sti", TEXT, $sites);
	}
	$r->get_form_parameters();
	if ($r->get_value("s_in") == "") {$r->set_value("s_in", 0);}
	if (sizeof($departments_values) > 2) {
		$r->set_value("department_id", $selected_department_id); 
	}
	$r->set_form_parameters();
	$t->set_var("dep_id", $selected_department_id);

	// create new ticket link
	if (isset($permissions["support_ticket_new"]) && $permissions["support_ticket_new"] == 1) {
		$t->parse("create_ticket_link", false);
	}


	$statuses_is_list = get_db_values("SELECT status_id, is_list FROM " . $table_prefix . "support_statuses", "");
	$s_in = $r->get_value("s_in");
	$where = ""; $search = "";
	if (!$r->is_empty("status_id")) {
		$where .= " s.support_status_id=" . $db->tosql($r->get_value("status_id"), INTEGER);
		if ($search) $search .= " and ";
		$search .= WHERE_STATUS_IS_MSG . ": '<b>" . get_array_value($r->get_value("status_id"),$support_statuses) . "</b>'";
	}	else {
		if ($s_in == 1) {	
			$search .= "'<b>".HIDDEN_TICKETS_MSG."</b>'";
			$where .= " ss.is_list=0 ";
		} else if ($s_in == 2) {
			$search .= "'<b>".ALL_TICKETS_MSG."</b>'";
		} else {
			$where .= " (ss.is_list=1 OR ss.is_list IS NULL) ";
		}
	}
	if (!$r->is_empty("summary_search")) {
		if (strlen($where)) $where .= " AND ";
		$where .= " (s.summary LIKE '%" . $db->tosql($r->get_value("summary_search"), TEXT, false) . "%')";
		if ($search) $search .= " and ";
		$search .= BY_SUMMARY_MSG . ": '<b>" . $r->get_value("summary_search") . "</b>'";
	}
	if (!$r->is_empty("s_at")) {
		if (strlen($where)) $where .= " AND ";
		if ($search) $search .= " and ";
		$where .= " s.admin_id_assign_to=" . $db->tosql($r->get_value("s_at"), INTEGER);
		$search .= " " . ASSIGN_TO_MSG . ": '<b>" . get_array_value($r->get_value("s_at"), $s_at_values) . "</b>'";
	}

	if (!$r->is_empty("s_ne")) {
		if (strlen($where)) $where .= " AND ";
		$where .= " (s.user_email LIKE '%" . $db->tosql($r->get_value("s_ne"), TEXT, false) . "%'";
		$where .= " OR s.user_name LIKE '%" . $db->tosql($r->get_value("s_ne"), TEXT, false) . "%')";
		if ($search) $search .= " and ";
		$search .= BY_NAME_EMAIL_MSG . ": '<b>" . $r->get_value("s_ne") . "</b>'";
	}
	
	if (!$r->is_empty("s_sti")) {
		if (strlen($where)) { $where .= " AND "; }
		$s_sti = $r->get_value("s_sti");
		$where .= " s.site_id=" . $db->tosql($r->get_value("s_sti"), INTEGER);
	}
		
	$sql_join_b = "";
	$sql_join_e = "";
	if (!$r->is_empty("keyword_search")) {
		if (strlen($where)) $where .= " AND ";
		$where .= " (s.summary LIKE '%" . $db->tosql($r->get_value("keyword_search"), TEXT, false) . "%'";
		$where .= " OR s.description LIKE '%" . $db->tosql($r->get_value("keyword_search"), TEXT, false) . "%'";
		$where .= " OR sm.message_text LIKE '%" . $db->tosql($r->get_value("keyword_search"), TEXT, false) . "%')";
		$sql_join_b = "(";
		$sql_join_e = "LEFT JOIN " . $table_prefix . "support_messages sm ON sm.support_id = s.support_id) ";
		if ($search) $search .= " and ";
		$search .= BY_KEYWORD_MSG . ": '<b>" . $r->get_value("keyword_search") . "</b>'";
	}
	if (!$r->is_empty("support_id_search")) {
		if (strlen($where)) $where .= " AND ";
		$where .= " s.support_id = " . $db->tosql($r->get_value("support_id_search"), INTEGER);
		if ($search) $search .= " and ";
		$search .= BY_TICKET_NO_MSG . ": '<b>" . $r->get_value("support_id_search") . "</b>'";
	}
	
	if (strlen($selected_department_id)) {
		if (strlen($where)) $where .= " AND ";
		$where .= " s.dep_id = " . $db->tosql($selected_department_id, INTEGER);
	}
	if (strlen($admin_departments_ids)) {
		if (strlen($where)) $where .= " AND ";
		$where .= " s.dep_id IN (" . $admin_departments_ids . ")";
	}	

	if ($search && ($s_in == 0)) {$search = "'<b>".ACTIVE_TICKETS_MSG."</b>' and " . $search;}
	
	$t->set_var("search", $search);
	if ($search) $t->parse("search_results", false);

	// generate where condition to show tickets allocated to administrator
	if (strlen($where)) {
		$where_am =  "WHERE s.admin_id_assign_to = " . $db->tosql($admin_id, INTEGER) . " AND " . $where; 
	} else {
		$where_am =  "WHERE s.admin_id_assign_to = " . $db->tosql($admin_id, INTEGER); 
	}

	// don't show in the main list my tickets
	if (strlen($where)) {
		$where =  "WHERE s.admin_id_assign_to<>" . $db->tosql($admin_id, INTEGER) . " AND " . $where; 
	} else {
		$where =  "WHERE s.admin_id_assign_to<>" . $db->tosql($admin_id, INTEGER); 
	}
	//if (strlen($where)) { $where = " WHERE " . $where; }

	// get status_id where is_closed = 1
	$close_status_id = "";
	$sql  = "SELECT status_id FROM " . $table_prefix . "support_statuses WHERE is_closed = 1";
	$db->query($sql);
	if ($db->next_record()) {
		$close_status_id = $db->f("status_id");
	}

	// set array html
	$html_status = array();
	$sql="SELECT status_id, status_icon, html_start, html_end FROM " . $table_prefix . "support_statuses";
	$db->query($sql);
	if ($db->num_rows($sql)) {
		while ($db->next_record()) {
			$html_status[$db->f("status_id")] = array($db->f("status_icon"), $db->f("html_start"), $db->f("html_end"));
		}
	}

	// set up variables for navigator allocated to me
	$admin_records = 0;
	$sql  = " SELECT COUNT(*)  ";
	$sql .= " FROM " . $sql_join_b . "(" . $table_prefix . "support s ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_statuses ss ON ss.status_id = s.support_status_id) ";
	$sql .= $sql_join_e;
	$sql .= $where_am;
	$sql .= " GROUP BY s.support_id ";
	$db->query($sql);
	while ($db->next_record()) {
		$admin_records++;
	}
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;

	$page_number = $n->set_navigator("navigator_am", "page_am", SIMPLE, $pages_number, $records_per_page, $admin_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	
	$t->set_var("allocated_me", "");
	$t->set_var("records_am", "");
	$t->set_var("navigator_block_am", "");

	if (strlen($admin_departments_ids) && ($selected_department_id || !$department_id)) {
		if ($db->DBType == "db2"){
			$sql = " SELECT sp.support_id, sp.summary, sp.status_name, sp.status_id, sp.type_name, sp.user_email, sp.dep_id, s.admin_html, ";
			$sql .= " sp.priority_name, sp.date_modified, sp.short_title ";
			if ($sitelist) {
				$sql .= ", sp.site_name";
			}
			$sql .= " FROM (";
			$sql .= " SELECT s.support_id, s.summary, ss.status_name, ss.status_id, st.type_name, s.user_email, s.dep_id, ";
			$sql .= " sp.priority_name, s.date_modified, sd.short_title, s.support_priority_id, sp.priority_rank ";
			if ($sitelist) {
				$sql .= ", sti.site_name";
			}
			$sql .= " FROM " . $sql_join_b . "((((" . $table_prefix . "support s ";
			$sql .= " LEFT JOIN " . $table_prefix . "support_statuses ss ON ss.status_id = s.support_status_id) ";
			$sql .= " LEFT JOIN " . $table_prefix . "support_types st ON st.type_id = s.support_type_id) ";
			$sql .= " LEFT JOIN " . $table_prefix . "support_priorities sp ON sp.priority_id = s.support_priority_id) ";
			$sql .= " LEFT JOIN " . $table_prefix . "support_departments sd ON sd.dep_id = s.dep_id) ";
			if ($sitelist) {
				$sql .= " LEFT JOIN " . $table_prefix . "sites sti ON sti.site_id = s.site_id) ";
			}
			$sql .= $sql_join_e;
			$sql .= $where_am;
			$sql .= " GROUP BY s.support_id, s.summary, ss.status_name, ss.status_id, st.type_name, s.user_email, s.dep_id, sp.priority_name, ";			
			$sql .= " sp.priority_rank, s.date_modified, sd.short_title, s.support_priority_id ";
			if ($sitelist) {
				$sql .= ", sti.site_name";
			}
			$sql .= " ) AS sp, " . $table_prefix . "support_priorities AS s ";
			$sql .= " WHERE s.priority_id = sp.support_priority_id";
		} else {
			$sql  = " SELECT s.support_id, s.summary, ss.status_name, ss.status_id, st.type_name, s.user_email, s.dep_id, ";
			$sql .= " sp.priority_name, sp.admin_html, s.date_modified, sd.short_title ";
			if ($sitelist) {
				$sql .= ", sti.site_name";
			}
			$sql .= " FROM " . $sql_join_b . "((((";
			if ($sitelist) {
				$sql .= "(";
			}
			$sql .= $table_prefix . "support s ";
			$sql .= " LEFT JOIN " . $table_prefix . "support_statuses ss ON ss.status_id = s.support_status_id) ";
			$sql .= " LEFT JOIN " . $table_prefix . "support_types st ON st.type_id = s.support_type_id) ";
			$sql .= " LEFT JOIN " . $table_prefix . "support_priorities sp ON sp.priority_id = s.support_priority_id) ";
			$sql .= " LEFT JOIN " . $table_prefix . "support_departments sd ON sd.dep_id = s.dep_id) ";
			if ($sitelist) {
				$sql .= " LEFT JOIN " . $table_prefix . "sites sti ON sti.site_id = s.site_id) ";
			}
			$sql .= $sql_join_e;
			$sql .= $where_am;
			$sql .= " GROUP BY s.support_id, s.summary, ss.status_name, ss.status_id, st.type_name, s.user_email, s.dep_id, sp.priority_name, ";
			$sql .= " sp.admin_html, sp.priority_rank, s.date_modified, sd.short_title ";
			if ($sitelist) {
				$sql .= ", sti.site_name";
			}
		}
		$sql .= $s_am->order_by;
		$db->query($sql);
		if ($db->next_record()) {
			$admin_support_reply_url = new VA_URL("admin_support_reply.php", true);
			$admin_support_reply_url->add_parameter("support_id", DB, "support_id");
		
			if ($sitelist) {
				$t->parse("site_name_header_am");
			}
			$t->parse("sorters_am", false);
			$next = false;
			do {
				$status_id = $db->f("status_id");
				$t->set_var("support_id_am", $db->f("support_id"));
		
				$t->set_var("admin_support_reply_url_am", $admin_support_reply_url->get_url());
		
				$t->set_var("summary_am", htmlspecialchars($db->f("summary")));
				if (isset($html_status[$status_id]) && $html_status[$status_id][1]) {
					$t->set_var("html_start_am", $html_status[$db->f("status_id")][1]);
					$t->set_var("html_end_am", $html_status[$db->f("status_id")][2]);
				} else {
					$t->set_var("html_start_am", "");
					$t->set_var("html_end_am", "");
				}

				$status = strlen($db->f("status_name")) ? $db->f("status_name") : "";
				$t->set_var("status_am", $status);
				if (isset($html_status[$status_id]) && $html_status[$status_id][0]) {
					$t->set_var("status_icon_am", $html_status[$db->f("status_id")][0]);
					$t->parse("status_ico_am", false);
				} else {
					$t->set_var("status_ico_am", "");
				}

				$t->set_var("type_am", $db->f("type_name"));
				$t->set_var("user_email_am", $db->f("user_email"));
		
				$priority = "";
				$priority_name = $db->f("priority_name");
				$priority_html = $db->f("admin_html");
				$t->set_var("priority_html", $priority_html);
		
				if ($next) {
					$t->set_var("style_am","row1");
				} else {
					$t->set_var("style_am","row2");
				}
				$next = !$next;
		
				$date_modified = $db->f("date_modified", DATETIME);
				$date_modified_string = va_date($datetime_show_format, $date_modified);
				$t->set_var("date_modified_am", $date_modified_string);
				if ($sitelist) {
					$t->set_var("site_name", $db->f("site_name"));
					$t->parse("site_name_am", false);
				}
				if ($db->f("status_id") != $close_status_id) {
					if ($allow_close) {
						$t->set_var("close_ticket_am", $admin_support_close_url->get_url());
						$t->set_var("close_summary_am", CLOSE_TICKET_MSG);
						$t->parse("close_ticket_enable_am", false);
					}
					$t->set_var("close_ticket_disable_am", "");
				} else {
					$t->set_var("close_summary_am", TICKET_IS_CLOSED_MSG);
					$t->set_var("close_ticket_enable_am", "");
					$t->parse("close_ticket_disable_am", false);
				}

				$t->parse("records_am", true);
			} while($db->next_record());
			$t->parse("allocated_me", true);
		}
	}

	// set up variables for navigator
	$main_records = 0;
	$sql  = " SELECT COUNT(*)  ";
	$sql .= " FROM " . $sql_join_b . "(" . $table_prefix . "support s ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_statuses ss ON ss.status_id = s.support_status_id) ";
	$sql .= $sql_join_e;
	$sql .= $where;
	$sql .= " GROUP BY s.support_id ";
	$db->query($sql);
	while ($db->next_record()) {
		$main_records++;
	}
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $main_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	
	// main tickets list
	$item_index = 0;
	$short_title = "";
	if (strlen($admin_departments_ids)) {
		if ($department_id && $department_id != $selected_department_id) {
			$t->set_var("tickets_block", "<p><font color='red'><b>".NOT_ASSIGNED_THIS_DEP_MSG."</b></font>");
			$t->set_var("records", "");
			$t->set_var("navigator_block", "");
		} else {
			if ($db->DBType == "db2"){
				$sql = " SELECT sp.support_id, sp.summary, sp.status_name, "; 
				$sql .= " sp.status_id, sp.type_name, sp.user_email,  ";
				$sql .= " sp.admin_alias, sp.dep_id, sp.date_modified,  ";
				$sql .= " sp.short_title, sp.login, sp.priority_name, ";
				$sql .= " s.admin_html, sp.priority_rank ";
				if ($sitelist) {
					$sql .= ", sp.site_name ";
				}
				$sql .= " FROM (SELECT s.support_id, s.summary, ss.status_name,  ";
				$sql .= " ss.status_id, st.type_name, s.user_email,  ";
				$sql .= " a.admin_alias, s.dep_id, s.date_modified, sp.priority_rank,  ";
				$sql .= " sd.short_title, a.login, sp.priority_name, s.support_priority_id  ";
				if ($sitelist) {
					$sql .= ", sti.site_name ";
				}
				$sql .= " FROM ((((( ";
				if ($sitelist) {
					$sql .= "(";
				}
				$sql .= $table_prefix . "support s  ";
				$sql .= " LEFT JOIN " . $table_prefix . "support_statuses ss ON ss.status_id = s.support_status_id)  ";
				$sql .= " LEFT JOIN " . $table_prefix . "support_types st ON st.type_id = s.support_type_id)  ";
				$sql .= " LEFT JOIN " . $table_prefix . "support_priorities sp ON sp.priority_id = s.support_priority_id)  ";
				$sql .= " LEFT JOIN " . $table_prefix . "support_departments sd ON sd.dep_id = s.dep_id)  ";
				$sql .= " LEFT JOIN " . $table_prefix . "admins a ON a.admin_id = s.admin_id_assign_to)  ";
				if ($sitelist) {
					$sql .= " LEFT JOIN " . $table_prefix . "sites sti ON sti.site_id = s.site_id) ";
				}
				$sql .= " WHERE (ss.is_list=1 OR ss.is_list IS NULL) AND s.dep_id = 1 AND s.dep_id IN (1)  ";
				$sql .= " GROUP BY s.support_id, s.summary, ss.status_name,  ";
				$sql .= " ss.status_id, st.type_name, s.user_email,  ";
				$sql .= " a.admin_alias, s.dep_id, sp.priority_name, s.support_priority_id,  ";
				$sql .= " sp.priority_rank, s.date_modified, sd.short_title,  ";
				if ($sitelist) {
					$sql .= ", sti.site_name ";
				}
				$sql .= " a.login) sp, " . $table_prefix . "support_priorities s ";
				$sql .= " WHERE s.priority_id = sp.support_priority_id ";

			} else {
				$sql  = " SELECT s.support_id, s.summary, ss.status_name, ss.status_id, st.type_name, s.user_email, a.admin_alias, s.dep_id, ";
				$sql .= " s.date_modified, sd.short_title, a.login, ";
				$sql .= " sp.admin_html, sp.priority_name ";
				if ($sitelist) {
					$sql .= ", sti.site_name ";
				}
				$sql .= " FROM " . $sql_join_b . "(((((";
				if ($sitelist) {
					$sql .= "(";
				}
				$sql .= $table_prefix . "support s ";
				$sql .= " LEFT JOIN " . $table_prefix . "support_statuses ss ON ss.status_id = s.support_status_id) ";
				$sql .= " LEFT JOIN " . $table_prefix . "support_types st ON st.type_id = s.support_type_id) ";
				$sql .= " LEFT JOIN " . $table_prefix . "support_priorities sp ON sp.priority_id = s.support_priority_id) ";
				$sql .= " LEFT JOIN " . $table_prefix . "support_departments sd ON sd.dep_id = s.dep_id) ";
				$sql .= " LEFT JOIN " . $table_prefix . "admins a ON a.admin_id = s.admin_id_assign_to) ";
				if ($sitelist) {
					$sql .= " LEFT JOIN " . $table_prefix . "sites sti ON sti.site_id = s.site_id) ";
				}
				$sql .= $sql_join_e;
				$sql .= $where;
				$sql .= " GROUP BY s.support_id, s.summary, ss.status_name, ss.status_id, st.type_name, s.user_email, a.admin_alias, s.dep_id, sp.admin_html, ";
				$sql .= " sp.priority_name, sp.priority_rank, s.date_modified, sd.short_title, a.login";
				if ($sitelist) {
					$sql .= ", sti.site_name ";
				}
			}
			$sql .= $s->order_by;
			$db->query($sql);
			if ($db->next_record()) {				
			  if (isset($permissions["support_ticket_edit"]) && $permissions["support_ticket_edit"] == 1) {
			  	$t->parse("delete_tickets_link",false);
				}
				$admin_support_reply_url = new VA_URL("admin_support_reply.php", true);
				$admin_support_reply_url->add_parameter("support_id", DB, "support_id");
				$admin_support_request_url = new VA_URL("admin_support_request.php", true);
				$admin_support_request_url->add_parameter("support_id", DB, "support_id");
		
				$colspan = 9;
				if ($sitelist) {
					$t->parse("site_name_header");
					$colspan ++;
				}
				$t->parse("sorters", false);
				$t->set_var("items_number", $item_index);
				$t->set_var("colspan", $colspan);
				$next = false;
				do {
				  	$item_index++;
				  	$t->set_var("item_index", $item_index);
					$status_id = $db->f("status_id");
					$t->set_var("support_id", $db->f("support_id"));
		
					$t->set_var("admin_support_reply_url", $admin_support_reply_url->get_url());
					$t->set_var("admin_support_request_url", $admin_support_request_url->get_url());
		
					$t->set_var("summary", htmlspecialchars($db->f("summary")));
					if (isset($html_status[$status_id]) && $html_status[$status_id][1]) {
						$t->set_var("html_start", $html_status[$status_id][1]);
						$t->set_var("html_end", $html_status[$status_id][2]);
					} else {
						$t->set_var("html_start", "");
						$t->set_var("html_end", "");
					}

					$status = strlen($db->f("status_name")) ? $db->f("status_name") : "";
					$t->set_var("status", $status);
					if (isset($html_status[$status_id]) && $html_status[$status_id][0]) {
						$t->set_var("status_icon", $html_status[$db->f("status_id")][0]);
						$t->parse("status_ico", false);
					} else {
						$t->set_var("status_ico", "");
					}

					$t->set_var("type", get_translation($db->f("type_name")));
					$t->set_var("user_email", $db->f("user_email"));
					if ($db->f("admin_alias") != "") {$t->set_var("admin_alias", $db->f("admin_alias"));
					} else {
					$t->set_var("admin_alias", $db->f("login"));
					}
		
					$priority = "";
					$priority_name = $db->f("priority_name");
					$priority_html = $db->f("admin_html");
					$t->set_var("priority_html", $priority_html);
		
					if ($next) { 
						$t->set_var("style","row1"); 
					} else {
						$t->set_var("style","row2");	
					}
					$next = !$next;
		
					$date_modified = $db->f("date_modified", DATETIME);
					$date_modified_string = va_date($datetime_show_format, $date_modified);
					$t->set_var("date_modified", $date_modified_string);
					
					if ($sitelist) {
						$t->set_var("site_name", $db->f("site_name"));
						$t->parse("site_name_block", false);
					}
					if ($db->f("status_id") != $close_status_id) {
						if ($allow_close) {
							$t->set_var("close_ticket", $admin_support_close_url->get_url());
							$t->set_var("close_summary", CLOSE_TICKET_MSG);
							$t->parse("close_ticket_enable", false);
						}
						$t->set_var("close_ticket_disable", "");
					} else {
						$t->set_var("close_summary", TICKET_IS_CLOSED_MSG);
						$t->set_var("close_ticket_enable", "");
						$t->parse("close_ticket_disable", false);
					}
					
					$short_title = $db->Record["short_title"];
					$t->parse("records", true);

				} while($db->next_record());

				$t->set_var("items_number", $item_index);
				$t->set_var("dep_name", $short_title);
				$t->parse("tickets_block", true);
			} else {
				$t->set_var("tickets_block", "");
				$t->set_var("records", "");
				$t->set_var("navigator_block", "");
				if (!$admin_records) {
					if ($search) {
						$t->set_var("tickets_block", "<p><font color='red'><b>".NO_TICKETS_FOUND_MSG."</b></font>");
					} else {
						$t->set_var("tickets_block", "<p><font color='red'><b>".NO_TICKETS_FOUND_MSG."</b></font>");
					}
				}
			}
		}
	}	else {
		$t->set_var("tickets_block", "<p><font color='red'><b>".NOT_ASSIGNED_ANY_DEP_MSG."</b></font>");
		$t->set_var("records", "");
		$t->set_var("navigator_block", "");
	}

	if ($sitelist) {
		$t->parse("sitelist");
	}
	$t->pparse("main");

?>