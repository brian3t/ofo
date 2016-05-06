<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_pretypes.php                               ***
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
	include_once($root_folder_path . "messages/" . $language_code . "/support_messages.php");

	include_once("./admin_common.php");

	check_admin_security("support_predefined_reply");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_support_pretypes.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_prereplies_href", "admin_support_prereplies.php");
	$t->set_var("admin_support_pretype_href",    "admin_support_pretype.php");
	$t->set_var("admin_support_pretypes_href",   "admin_support_pretypes.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_support_pretypes.php");
	$s->set_parameters(false, true, true, false);
	$s->set_sorter(ID_MSG, "sorter_type_id", "1", "spt.type_id");
	$s->set_sorter(TYPE_MSG, "sorter_type_name", "2", "spt.type_name");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$admin_support_pretype = new VA_URL("admin_support_pretype.php");
	$admin_support_pretype->add_parameter("page", REQUEST, "page");
	$admin_support_pretype->add_parameter("sort_ord", REQUEST, "sort_ord");
	$admin_support_pretype->add_parameter("sort_dir", REQUEST, "sort_dir");
	$admin_support_pretype->add_parameter("s_ut", REQUEST, "s_ut");
	$admin_support_pretype->add_parameter("s_g", REQUEST, "s_g");
	$t->set_var("admin_support_pretype_new_url", $admin_support_pretype->get_url());

	// set up variables for navigator
	$where = "";
	$sql  = "SELECT COUNT(*) ";
	$sql .= " FROM " . $table_prefix . "support_predefined_types sg ";
	$sql .= $where;
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_support_pretypes.php");
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$sql  = " SELECT spt.type_id, spt.type_name ";
	$sql .= " FROM " . $table_prefix . "support_predefined_types spt ";
	$sql .= $where;
	$sql .= $s->order_by;
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql);
	if ($db->next_record())
	{
		$admin_support_pretype->add_parameter("type_id", DB, "type_id");
		$t->set_var("no_records", "");
		do {
			$t->set_var("type_id", $db->f("type_id"));
			$t->set_var("type_name", get_translation($db->f("type_name")));
			$t->set_var("admin_support_pretype_url", $admin_support_pretype->get_url());

			$t->parse("records", true);
		} while($db->next_record());
		$t->parse("titles", false);
	}
	else
	{
		$t->set_var("titles", "");
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>