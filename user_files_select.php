<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  user_files_select.php                                    ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./includes/common.php");
	include_once("./includes/sorter.php");
	include_once("./includes/navigator.php");
	include_once("./messages/".$language_code."/cart_messages.php");
	include_once("./messages/".$language_code."/download_messages.php");

	check_user_session();

	$sw = trim(get_param("sw"));
	$type = get_param("type");
	$item_id = get_param("item_id");
	$item_type_id = get_param("item_type_id");
	$form_name = get_param("form_name");
	$field_name = get_param("field_name");

	$item_id = get_param("item_id");
	$sql  = " SELECT item_name FROM " . $table_prefix . "items ";
	$sql .= " WHERE item_id=" . $db->tosql($item_id, INTEGER);
	$sql .= " AND user_id=" . $db->tosql(get_session("session_user_id"), INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$item_name = get_translation($db->f("item_name"));
	} else {
		exit;
	}

	$t = new VA_Template($settings["templates_dir"]);
  $t->set_file("main","user_files_select.html");
	$t->set_var("user_files_select_href", "user_files_select.php");
	$t->set_var("sw", htmlspecialchars($sw));
	$t->set_var("type", htmlspecialchars($type));
	$t->set_var("item_id", htmlspecialchars($item_id));
	$t->set_var("item_type_id", htmlspecialchars($item_type_id));
	$t->set_var("form_name", htmlspecialchars($form_name));
	$t->set_var("field_name", htmlspecialchars($field_name));
	$css_file = "";
	if (isset($settings["style_name"]) && $settings["style_name"]) {
		$css_file = "styles/" . $settings["style_name"];
		if (isset($settings["scheme_name"]) && $settings["scheme_name"]) {
			$css_file .= "_" . $settings["scheme_name"];
		}
		$css_file .= ".css";
	}
	$t->set_var("css_file", $css_file);

	$s = new VA_Sorter($settings["templates_dir"], "sorter_img.html", "user_files_select.php");
	$s->set_parameters(false, true, true, false);
	$s->set_sorter(ID_MSG, "sorter_file_id", "1", "f.file_id");
	$s->set_sorter(DOWNLOAD_TITLE_MSG, "sorter_file_title", "2", "f.download_title");
	$s->set_sorter(DOWNLOAD_PATH_MSG, "sorter_file_path", "3", "f.download_path");

	$where  = " WHERE f.item_id=" . $db->tosql($item_id, INTEGER, true, false);
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
	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", "user_files_select.php");
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