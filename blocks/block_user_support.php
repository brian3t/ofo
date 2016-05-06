<?php

	check_user_session();

	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");
	$secure_redirect = get_setting_value($settings, "secure_redirect", 0);
	$secure_user_ticket = get_setting_value($settings, "secure_user_ticket", 0);
	$secure_user_tickets = get_setting_value($settings, "secure_user_tickets", 0);
	if ($secure_user_ticket) {
		$support_url = $secure_url . get_custom_friendly_url("support.php");
		$support_messages_url = $secure_url . get_custom_friendly_url("support_messages.php");
	} else {
		$support_url = $site_url . get_custom_friendly_url("support.php");
		$support_messages_url = $site_url . get_custom_friendly_url("support_messages.php");
	}
	if ($secure_user_tickets) {
		$user_support_url = $secure_url . get_custom_friendly_url("user_support.php");
	} else {
		$user_support_url = $site_url . get_custom_friendly_url("user_support.php");
	}
	$user_home_url = $site_url . get_custom_friendly_url("user_home.php");
	if (!$is_ssl && $secure_user_tickets && $secure_redirect && preg_match("/^https/i", $secure_url)) {
		header("Location: " . $user_support_url);
		exit;
	}

	$t->set_file("block_body","block_user_support.html");
	                                                                 
	$t->set_var("user_support_href", $user_support_url);
	$t->set_var("support_href", $support_url);
	$t->set_var("support_messages_href", $support_messages_url);
	$t->set_var("user_home_href", $user_home_url);

	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", $user_support_url);
	$s->set_default_sorting(4, "desc");
	$s->set_sorter(SUPPORT_SUMMARY_COLUMN, "sorter_summary", 1, "summary");
	$s->set_sorter(STATUS_MSG, "sorter_status", 2, "ss.status_name");
	$s->set_sorter(SUPPORT_TYPE_COLUMN, "sorter_type", 3, "st.type_name");
	$s->set_sorter(SUPPORT_UPDATED_COLUMN, "sorter_modified", 4, "s.date_modified");

	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $user_support_url);

	// set up variables for navigator
	$sql  = " SELECT COUNT(*) FROM ((";
	if (isset($site_id)) {
		$sql .= "((";
	}
	$sql .= $table_prefix . "support s ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_departments sd ON sd.dep_id=s.dep_id)";
	$sql .= " LEFT JOIN " . $table_prefix . "support_products sp ON sp.product_id=s.support_product_id)";
	if (isset($site_id)) {
		$sql .= " LEFT JOIN " . $table_prefix . "support_departments_sites sds ON (sds.dep_id=sd.dep_id AND sd.sites_all=0))";
		$sql .= " LEFT JOIN " . $table_prefix . "support_products_sites sps ON (sps.product_id=sp.product_id AND sp.sites_all=0))";
	}
	$sql .= " WHERE s.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	if (isset($site_id)) {
		$sql .= " AND (sd.sites_all=1 OR sds.site_id= " . $db->tosql($site_id, INTEGER, true, false). " ) ";	
		$sql .= " AND (sp.sites_all=1 OR s.support_product_id=0 OR sps.site_id= " . $db->tosql($site_id, INTEGER, true, false). " ) ";	
	} else {
		$sql .= " AND sd.sites_all=1 ";	
		$sql .= " AND (sp.sites_all=1 OR s.support_product_id=0) ";	
	}
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT s.support_id, s.summary, ss.status_name, st.type_name, s.date_added, s.date_modified ";
	$sql .= " FROM ((((";
	if (isset($site_id)) {
		$sql .= "((";
	}
	$sql .= $table_prefix . "support s ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_statuses ss ON ss.status_id=s.support_status_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_types st ON st.type_id=s.support_type_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_departments sd ON sd.dep_id=s.dep_id)";
	$sql .= " LEFT JOIN " . $table_prefix . "support_products sp ON sp.product_id=s.support_product_id)";
	if (isset($site_id)) {
		$sql .= " LEFT JOIN " . $table_prefix . "support_departments_sites sds ON (sds.dep_id=sd.dep_id AND sd.sites_all=0))";
		$sql .= " LEFT JOIN " . $table_prefix . "support_products_sites sps ON (sps.product_id=sp.product_id AND sp.sites_all=0))";
	}
	$sql .= " WHERE s.user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	if (isset($site_id)) {
		$sql .= " AND (sd.sites_all=1 OR sds.site_id= " . $db->tosql($site_id, INTEGER, true, false). " ) ";	
		$sql .= " AND (sp.sites_all=1 OR s.support_product_id=0 OR sps.site_id= " . $db->tosql($site_id, INTEGER, true, false). " ) ";	
	} else {
		$sql .= " AND sd.sites_all=1 ";	
		$sql .= " AND (sp.sites_all=1 OR s.support_product_id=0) ";	
	}

	$db->query($sql . $s->order_by);
	if($db->next_record())
	{
		$t->parse("sorters", false);
		$t->set_var("no_records", "");
		do
		{
			$support_id = $db->f("support_id");
			$date_added = $db->f("date_added", DATETIME);
			$vc = md5($support_id . $date_added[3].$date_added[4].$date_added[5]);
			$t->set_var("support_id", $support_id);
			$t->set_var("vc", $vc);

			$t->set_var("summary", htmlspecialchars($db->f("summary")));
			$status_name = strlen($db->f("status_name")) ? $db->f("status_name") : SUPPORT_STATUS_NEW_MSG;
			$t->set_var("status_name", $status_name);
			$t->set_var("type_name", $db->f("type_name"));

			$date_modified = $db->f("date_modified", DATETIME);
			$t->set_var("date_modified", va_date($datetime_show_format, $date_modified));


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

	$t->parse("block_body", false);
	$t->parse($block_name, true);

?>