<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_prereplies.php                             ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once($root_folder_path . "includes/record.php");

	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support");

	$reply_id = get_param("reply_id");
	$populate = get_param("populate");
	$is_popup = get_param("is_popup");
	$s_type = get_param("s_type");

	$t = new VA_Template($settings["admin_templates_dir"]);
	if ($is_popup) {
		$records_per_page = 10;
		$t->set_file("main", "admin_support_prereplies_popup.html");
	} else {
		$records_per_page = 20;
		$t->set_file("main", "admin_support_prereplies.html");
	}

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_prereplies_href", "admin_support_prereplies.php");
	$t->set_var("admin_support_prereply_href", "admin_support_prereply.php");
	$t->set_var("is_popup", htmlspecialchars($is_popup));

	if($t->block_exists("types_filter")) {
		$filter_prereplies = new VA_URL("admin_support_prereplies.php", false);
		$filter_prereplies->add_parameter("s_type", DB, "type_id");
		$filter_prereplies->add_parameter("is_popup", REQUEST, "is_popup");

		$sql  = " SELECT spt.type_id, spt.type_name, COUNT(*) AS type_replies ";
		$sql .= " FROM (" . $table_prefix . "support_predefined sp ";
		$sql .= " INNER JOIN " . $table_prefix . "support_predefined_types spt ON sp.type_id=spt.type_id) ";
		$sql .= " GROUP BY spt.type_id, spt.type_name ";
		//$sql .= " ORDER BY type_replies ";
		$db->query($sql);
		if ($db->next_record()) {
			$col_recs = 5;
			$recs_number = 0;
			do {
				$recs_number++;
				$type_id = $db->f("type_id");
				$type_name = get_translation($db->f("type_name"));
				$type_replies = $db->f("type_replies");
				$type_style = ($s_type == $type_id) ? "font-weight: bold;" : "";
				$t->set_var("type_name", $type_name);
				$t->set_var("type_replies", $type_replies);
				$t->set_var("type_style", $type_style);
				$t->set_var("filter_prereplies_url", $filter_prereplies->get_url());
				$t->parse("types_recs", true);
				if ($recs_number % $col_recs == 0) {
					$t->parse("types_cols", true);
					$t->set_var("types_recs", "");
				}
			} while ($db->next_record());

			if ($recs_number % $col_recs != 0) {
				$t->parse("types_cols", true);
				$t->set_var("types_recs", "");
			}

			$t->parse("types_filter", false);
		}


		$sql = " SELECT ";
		$t->set_var("admin_support_prereply_new_url", $filter_prereplies->get_url());

	}

	$sql  = " SELECT type_id, type_name FROM " . $table_prefix . "support_predefined_types ";
	$sql .= " ORDER BY type_name ";
	$reply_types = get_db_values($sql, array(array("", "")));

	$r = new VA_Record($table_prefix . "orders");
	$r->add_textbox("s_kw", TEXT);
	$r->change_property("s_kw", TRIM, true);
	$r->add_select("s_type", TEXT, $reply_types);
	$r->get_form_parameters();
	$r->validate();
	$r->set_form_parameters();

	$where = ""; $product_search = false;
	if(!$r->errors) {
		if(!$r->is_empty("s_kw")) {
			$sw = split(" ", $r->get_value("s_kw"));
			for($si = 0; $si < sizeof($sw); $si++) {
				$sw[$si] = str_replace("%","\%",$sw[$si]);
				if (strlen($where)) { $where .= " AND "; }
				$where .= " (sp.subject LIKE '%" . $db->tosql($sw[$si], TEXT, false) . "%'";
				$where .= " OR sp.body LIKE '%" . $db->tosql($sw[$si], TEXT, false) . "%')";
			}
		}

		if (!$r->is_empty("s_type")) {
			if (strlen($where)) { $where .= " AND "; }
			$where .= " sp.type_id=" . $db->tosql($r->get_value("s_type"), INTEGER);
		}
	}
	$where_sql = ""; 
	if (strlen($where)) {
		$where_sql = " WHERE " . $where;
	}

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_support_prereplies.php");
	$s->set_parameters(false, true, true, false);
	$s->set_sorter(ID_MSG, "sorter_reply_id", "1", "sp.reply_id");
	$s->set_sorter(ADMIN_TITLE_MSG, "sorter_subject", "2", "sp.subject");
	$s->set_sorter(ADMIN_RATING_MSG, "sorter_uses", "3", "sp.total_uses");
	$s->set_sorter(TYPE_MSG, "sorter_type", "4", "spt.type_name");
	$s->set_sorter(SUPPORT_ADDED_BY_FIELD, "sorter_added_by", "5", "a.admin_alias");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_support_prereplies.php");

	if (!$is_popup) {
		include_once("./admin_header.php");
		include_once("./admin_footer.php");
 	}	

	$admin_support_prereply = new VA_URL("admin_support_prereply.php", false);
	$admin_support_prereply->add_parameter("s_kw", REQUEST, "s_kw");
	$admin_support_prereply->add_parameter("s_type", REQUEST, "s_type");
	$admin_support_prereply->add_parameter("is_popup", REQUEST, "is_popup");
	$admin_support_prereply->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_support_prereply->add_parameter("sort_dir", REQUEST, "sort_dir");
	$admin_support_prereply->add_parameter("page", REQUEST, "page");
	$t->set_var("admin_support_prereply_new_url", $admin_support_prereply->get_url());

	// set up variables for navigator
	$sql = "SELECT COUNT(*) FROM " . $table_prefix . "support_predefined sp " . $where_sql;
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", MOVING, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT sp.reply_id, sp.subject, sp.total_uses, sp.body, spt.type_name, a.admin_name, a.admin_alias ";
	$sql .= " FROM ((" . $table_prefix . "support_predefined sp ";
	$sql .= " INNER JOIN " . $table_prefix . "support_predefined_types spt ON sp.type_id=spt.type_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "admins a ON sp.admin_id_added_by=a.admin_id) ";
	$sql .= $where_sql;
	$sql .= $s->order_by;
	$db->query($sql);
	if ($db->next_record())
	{
		$t->set_var("no_records", "");
		$t->sparse("sorters", false);

		$admin_support_prereply->add_parameter("reply_id", DB, "reply_id");

		$admin_support_prereplies = new VA_URL("admin_support_prereplies.php", false);
		$admin_support_prereplies->add_parameter("reply_id", DB, "reply_id");
		$admin_support_prereplies->add_parameter("populate", CONSTANT, "1");

		do {
			$body = $db->f("body");
			if (strlen($body) > 255){
				$spacepos = strpos($body, " ", 225); 
				$reply_preview = $spacepos ? substr($body, 0, $spacepos) . "\n..." : substr($body, 0, 255) . "...";
			}	else {
				$reply_preview = $body;
			}

			$t->set_var("reply_id", $db->f("reply_id"));
			$t->set_var("subject", $db->f("subject"));
			$t->set_var("total_uses", $db->f("total_uses"));
			$t->set_var("type_name", $db->f("type_name"));
			$t->set_var("added_by", $db->f("admin_alias"));

			$t->set_var("reply_preview", nl2br(htmlspecialchars($reply_preview)));
			$t->set_var("reply_body", htmlspecialchars($body));

			$t->set_var("admin_support_prereply_edit_url", $admin_support_prereply->get_url());
			$t->set_var("admin_support_prereply_insert_url", $admin_support_prereplies->get_url());

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