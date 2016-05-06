<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_newsletter_users.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("newsletter");
	
	$where = "";
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_newsletter_users.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_newsletter_users_edit_href",  "admin_newsletter_users_edit.php");
	$t->set_var("admin_newsletters_href", "admin_newsletters.php");
	$t->set_var("admin_newsletter_users_href", "admin_newsletter_users.php");
	$p = get_param('page');
	if ($p) {
		$a_p = "&page=".$p;
		$t->set_var("and_page",$a_p);
		$t->set_var("p",$p);
		$p = "?page=".$p;
		$t->set_var("page",$p);
	} else {
		$t->set_var("page", "");
		$t->set_var("and_page", "");
	}

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_newsletter_users.php");
	$s->set_default_sorting(1, "desc");
	$s->set_sorter(ID_MSG, "sorter_email_id", "1", "email_id");
	$s->set_sorter(EMAIL_FIELD, "sorter_email", "2", "email");
	$s->set_sorter(DATE_MSG, "sorter_date_added", "3", "date_added");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_newsletter_users.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("date_edit_format", join("", $date_edit_format));
	
	$r = new VA_Record($table_prefix . "newsletter_users");
	$r->add_textbox("s_ne", TEXT);
	$r->change_property("s_ne", TRIM, true);
	$r->add_textbox("s_sd", DATE, FROM_DATE_MSG);
	$r->change_property("s_sd", VALUE_MASK, $date_edit_format);
	$r->change_property("s_sd", TRIM, true);
	$r->add_textbox("s_ed", DATE, END_DATE_MSG);
	$r->change_property("s_ed", VALUE_MASK, $date_edit_format);
	$r->change_property("s_ed", TRIM, true);
	$r->get_form_parameters();
	$r->validate();
	$r->set_form_parameters();
	
	if (!$r->errors){
		if (!$r->is_empty("s_ne")) {
			if (strlen($where)) { $where .= " AND "; }
			$s_ne_sql = $db->tosql($r->get_value("s_ne"), TEXT, false);
			$where .= " email LIKE '%" . $s_ne_sql . "%'";
		}

		if (!$r->is_empty("s_sd")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " date_added>=" . $db->tosql($r->get_value("s_sd"), DATE);
		}

		if (!$r->is_empty("s_ed")) {
			if (strlen($where)) { $where .= " AND "; }
			$date_end_temp = $r->get_value("s_ed");
			$date_end_temp[2] = $date_end_temp[2] + 1;
			$where .= " date_added<" . $db->tosql($date_end_temp, DATE);
		}
		
	}
	
	$operation = get_param('operation');
	if (strlen($operation) && $operation == 'delete'){
		$ids = get_param('emails_ids');
		if (strlen($ids))
		$db->query("DELETE FROM " . $table_prefix . "newsletters_users WHERE email_id IN (" . $db->tosql($ids, TEXT, false) . ")");
		header("Location: " . 'admin_newsletter_users.php'.$p);
		exit;
	}

	// set up variables for navigator
	if (strlen($where)) $where = " WHERE " . $where;
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "newsletters_users " . $where);
	$db->next_record();
	$total_records = $db->f(0);
	if ($total_records != 0){
		$t->parse("count_null",false);
	} else {
		$t->set_vars("count_null","");
	}
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	if ($total_records > $records_per_page){
		$count = $records_per_page;
	} else {
	  	$count = $total_records;
	}
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT * FROM " . $table_prefix . "newsletters_users" . $where;
	$db->query($sql . $s->order_by);
	$i = 0;
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do {
		  	$i++;
			$newsletter_subject = $db->f("email");
			$newsletter_date = $db->f("date_added", DATETIME);
			$newsletter_date = va_date($datetime_show_format, $newsletter_date);
			$t->set_var("newsletter_id", $db->f("email_id"));
			$t->set_var("onpage_id", $i);
			$row_style = ($i % 2 == 0) ? "row1" : "row2";
			$t->set_var("row_style", $row_style);
			$t->set_var("count", $i);
			$t->set_var("newsletter_subject", $newsletter_subject);
			$t->set_var("newsletter_date", $newsletter_date);
			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("sorters", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}
	
		$t->pparse("main");

?>