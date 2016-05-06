<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_languages.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");
	include_once("./admin_common.php");

	check_admin_security("static_tables");

	$operation = get_param("operation");
	$language_edit = get_param("language_edit");
	
	if (strlen($operation) && $language_edit) {
		if (strtolower($operation) == "off") {
			$sql  = " UPDATE " . $table_prefix . "languages SET show_for_user=0 ";
			$sql .= " WHERE language_code=" . $db->tosql($language_edit, TEXT);
			$db->query($sql);
		} elseif (strtolower($operation) == "on") {
			$sql  = " UPDATE " . $table_prefix . "languages SET show_for_user=1 ";
			$sql .= " WHERE language_code=" . $db->tosql($language_edit, TEXT);
			$db->query($sql);
		}
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_languages.html");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_lookup_tables_href", "admin_lookup_tables.php");
	$t->set_var("admin_language_href", "admin_language.php");
	$t->set_var("admin_settings_list", "admin_settings_list.php");
	$t->set_var("admin_messages_href", "admin_messages.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_languages.php");
	$s->set_sorter(LANGUAGE_CODE_MSG, "sorter_language_code", 1, "language_code");
	$s->set_sorter(LANGUAGE_MSG, "sorter_language_name", 2, "language_name");
	$s->set_sorter(SHOW_FOR_USER_MSG, "sorter_show_for_user", 3, "show_for_user");
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_languages.php");

	$sp = trim(get_param("sp")); $where = "";
	if (strlen($sp)) {
		$where  = " WHERE language_name LIKE '%" . $db->tosql($sp, TEXT, false) . "%'";
		$where .= " OR language_code=" . $db->tosql($sp, TEXT);
	}

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "languages " . $where);
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = get_param("q") > 0 ? get_param("q") : 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$t->set_var("page", $page_number);

	$t->set_var("sort_ord", get_param("sort_ord"));
	$t->set_var("sort_dir", get_param("sort_dir"));
	$t->set_var("page", get_param("page"));
	$t->set_var("sp", htmlspecialchars($sp));
	$t->set_var("sp_url", urlencode($sp));

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query("SELECT * FROM " . $table_prefix . "languages " . $where . $s->order_by);
	if ($db->next_record())
	{
		$t->set_var("no_records", "");
		do {
			$t->set_var("language_code", $db->f("language_code"));
			$t->set_var("language_name", $db->f("language_name"));
			$show_for_user = $db->f("show_for_user");
			if ($show_for_user) {
				$show_for_user = "<font color=\"blue\"><b>" . YES_MSG . "</b></font>";
				$t->set_var("operation", "off");
			} else  {
				$show_for_user = "<font color=\"silver\">" . NO_MSG . "</font>";
				$t->set_var("operation", "on");
			} 
			$t->set_var("show_for_user", $show_for_user);

			$t->parse("records", true);
		} while ($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	$t->pparse("main");

?>