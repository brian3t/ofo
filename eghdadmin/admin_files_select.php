<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_files_select.php                                   ***
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
	include_once("../messages/".$language_code."/download_messages.php");
	include_once("./admin_common.php");

	check_admin_security("products_categories");

	$sw = trim(get_param("sw"));
	$type = get_param("type");
	$item_id = get_param("item_id");
	$item_type_id = get_param("item_type_id");
	$form_name = get_param("form_name");
	$field_name = get_param("field_name");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_files_select.html");
	$t->set_var("admin_files_select_href", "admin_files_select.php");
	$t->set_var("sw", htmlspecialchars($sw));
	$t->set_var("type", htmlspecialchars($type));
	$t->set_var("item_id", htmlspecialchars($item_id));
	$t->set_var("item_type_id", htmlspecialchars($item_type_id));
	$t->set_var("form_name", htmlspecialchars($form_name));
	$t->set_var("field_name", htmlspecialchars($field_name));

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

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


	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_files_select.php");
	$s->set_parameters(false, true, true, false);
	$s->set_sorter(ID_MSG, "sorter_file_id", "1", "f.file_id");
	$s->set_sorter(DOWNLOAD_TITLE_MSG, "sorter_file_title", "2", "f.download_title");
	$s->set_sorter(DOWNLOAD_PATH_MSG, "sorter_file_path", "3", "f.download_path");

	$where  = " WHERE f.item_id=" . $db->tosql($item_id, INTEGER, true, false);
	$where .= " AND f.item_type_id=" . $db->tosql($item_type_id, INTEGER, true, false);
	$sa = array();
	if ($sw) {
		$sa = split(" ", $sw);
		for($si = 0; $si < sizeof($sa); $si++) {
			if ($where) { $where .= " AND "; }
			else { $where .= " WHERE "; }

			$sw_sql = $db->tosql($sa[$si], TEXT, false);
			$where .= " (f.download_title LIKE '%" . $sw_sql . "%'";
			$where .= " OR f.download_path LIKE '%" . $sw_sql . "%')";
		}
	}

	$sql = " SELECT COUNT(*) FROM " . $table_prefix . "items_files f " . $where;
	$db->query($sql);
	$db->next_record();
	$total_records = $db->f(0);

	// set up variables for navigator
	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_files_select.php");
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", MOVING, $pages_number, $records_per_page, $total_records, false);

	$sql  = " SELECT file_id, download_title, download_path ";
	$sql .= "	FROM " . $table_prefix . "items_files f ";
	$sql .= $where;
	$sql .= $s->order_by;
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$db->query($sql);
	if ($db->next_record()) {
		$t->parse("sorters", false);
		do {
			$file_id = $db->f("file_id");
			$file_title = $db->f("download_title");
			$file_path = $db->f("download_path");
			if (!$file_title) {
				$file_title = basename($file_path);
			}

			$file_title_js = str_replace("'", "\\'", htmlspecialchars($file_title));

			if(is_array($sa)) {
				for($si = 0; $si < sizeof($sa); $si++) {
					$file_title = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $file_title);					
					$file_path = preg_replace ("/(" . $sa[$si] . ")/i", "<font color=blue><b>\\1</b></font>", $file_path);					
				}
			}

			$t->set_var("file_id", $file_id);
			$t->set_var("file_title", $file_title);
			$t->set_var("file_path", $file_path);
			$t->set_var("file_title_js", $file_title_js);

			$t->parse("files", true);
		} while ($db->next_record());
	}

	if (strlen($sw)) {
		$found_message = str_replace("{found_records}", $total_records, FOUND_FILES_MSG);
		$found_message = str_replace("{search_string}", htmlspecialchars($sw), $found_message);
		$t->set_var("found_message", $found_message);
		$t->parse("search_results", false);
	}

	$t->pparse("main");
?>