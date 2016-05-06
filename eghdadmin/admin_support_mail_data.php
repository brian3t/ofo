<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_mail_data.php                              ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support");

	$type = get_param("type");
	$support_id = get_param("support_id");
	$message_id = get_param("message_id");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_support_mail_data.html");
	if ($support_id) {
		$sql  = " SELECT mail_headers, mail_body_html, mail_body_text FROM " . $table_prefix . "support ";
		$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
	} else {
		$sql  = " SELECT mail_headers, mail_body_html, mail_body_text FROM " . $table_prefix . "support_messages ";
		$sql .= " WHERE message_id=" . $db->tosql($message_id, INTEGER);
	}
	$db->query($sql);
	if ($db->next_record()) {
		$mail_data = "";
		if ($type == "header") {
			$mail_data = $db->f("mail_headers");
			$mail_data = nl2br(htmlspecialchars($mail_data));

			$t->set_var("mail_data_title", HEADERS_MSG);
			$t->set_var("mail_data", $mail_data);
		} else if ($type == "html") {
			$mail_data = $db->f("mail_body_html");
			$t->set_var("mail_data_title", ORIGINAL_HTML_MSG);
			$t->set_var("mail_data", $mail_data);
		} else if ($type == "text") {
			$mail_data = $db->f("mail_body_text");
			$t->set_var("mail_data_title", ORIGINAL_TEXT_MSG);
			$t->set_var("mail_data", nl2br(htmlspecialchars($mail_data)));
		}
	}

	$t->pparse("main");

?>