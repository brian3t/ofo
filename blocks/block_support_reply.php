<?php

function support_reply($block_name)
{
	global $t, $db, $db_type, $table_prefix, $currency, $language_code;
	global $is_ssl, $settings, $page_settings, $site_id, $datetime_show_format;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_support_reply.html");

	$eol = get_eol();
	$support_id = get_param("support_id");
	$vc = get_param("vc");
	$action = get_param("action");
	$rnd = get_param("rnd");
	$user_id = get_session("session_user_id");		
	$user_type_id = get_session("session_user_type_id");

	$site_url = get_setting_value($settings, "site_url", "");
	$secure_url = get_setting_value($settings, "secure_url", "");
	$secure_redirect = get_setting_value($settings, "secure_redirect", 0);
	$secure_user_ticket = get_setting_value($settings, "secure_user_ticket", 0);
	$secure_user_tickets = get_setting_value($settings, "secure_user_tickets", 0);
	if ($secure_user_ticket) {
		$support_url = $secure_url . get_custom_friendly_url("support.php");
		$support_messages_url = $secure_url . get_custom_friendly_url("support_messages.php");
		$support_attachment_url = $secure_url . get_custom_friendly_url("support_attachment.php");
		$user_support_attachments_url = $secure_url . get_custom_friendly_url("user_support_attachments.php");
	} else {
		$support_url = $site_url . get_custom_friendly_url("support.php");
		$support_messages_url = $site_url . get_custom_friendly_url("support_messages.php");
		$support_attachment_url = $site_url . get_custom_friendly_url("support_attachment.php");
		$user_support_attachments_url = $site_url . get_custom_friendly_url("user_support_attachments.php");
	}
	if ($secure_user_tickets) {
		$user_support_url = $secure_url . get_custom_friendly_url("user_support.php");
	} else {
		$user_support_url = $site_url . get_custom_friendly_url("user_support.php");
	}
	$user_home_url = $site_url . get_custom_friendly_url("user_home.php");
	if (!$is_ssl && $secure_user_ticket && $secure_redirect && preg_match("/^https/i", $secure_url)) {
		header("Location: " . $support_messages_url . "?support_id=" . urlencode($support_id) . "&vc=" . urlencode($vc));
		exit;
	}

	$support_settings = array();
	$sql  = " SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='support'";
	if (isset($site_id)) {
		$sql .= " AND (site_id=1 OR site_id=" . $db->tosql($site_id, INTEGER, true, false) . ")";
		$sql .= " ORDER BY site_id ASC ";
	} else {
		$sql .= " AND site_id=1 ";
	}
	$db->query($sql);
	while($db->next_record()) {
		$support_settings[$db->f("setting_name")] = $db->f("setting_value");
	}
	$attachments_users_allowed = get_setting_value($support_settings, "attachments_users_allowed", 0);

	// connection for support attachemnts 
	$dba = new VA_SQL();
	$dba->DBType       = $db->DBType;
	$dba->DBDatabase   = $db->DBDatabase;
	$dba->DBUser       = $db->DBUser;
	$dba->DBPassword   = $db->DBPassword;
	$dba->DBHost       = $db->DBHost;
	$dba->DBPort       = $db->DBPort;
	$dba->DBPersistent = $db->DBPersistent;

	$t->set_var("user_support_href", $user_support_url);
	$t->set_var("user_home_href", $user_home_url);
	$t->set_var("support_messages_href", $support_messages_url);
	$t->set_var("user_support_attachments_url", $user_support_attachments_url);
	$t->set_var("rnd", va_timestamp());

	$errors = "";

	if (!strlen($support_id)) {
		$errors = SUPPORT_MISS_ID_ERROR;
	} else if(!strlen($vc)) {
		$errors = SUPPORT_MISS_CODE_ERROR;
	}

	$return_page = $support_messages_url . "?support_id=" . $support_id . "&vc=" . $vc;

	$sql  = " SELECT s.dep_id, s.user_id, s.user_name, s.user_email, s.remote_address, s.identifier, ";
	$sql .= " s.environment, p.product_name, st.type_name, s.summary, s.description,   ";
	$sql .= " ss.status_name, sp.priority_name, s.date_added, s.date_viewed, s.date_modified, ";
	$sql .= " aa.admin_name as assign_to ";
	$sql .= " FROM (((((" . $table_prefix . "support s ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_products p ON p.product_id=s.support_product_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_statuses ss ON ss.status_id=s.support_status_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_types st ON st.type_id=s.support_type_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_priorities sp ON sp.priority_id=s.support_priority_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "admins aa ON aa.admin_id=s.admin_id_assign_to) ";
	$sql .= " WHERE s.support_id=" . $db->tosql($support_id, INTEGER);

	$db->query($sql);
	if($db->next_record() && !strlen($errors))
	{
		$dep_id = $db->f("dep_id");
		$t->set_var("user_id", $db->f("user_id"));
		$user_name = $db->f("user_name");
		$user_email = $db->f("user_email");
		$request_posted_by = $user_name . " <" . $user_email . ">";
		$summary = $db->f("summary");
		$identifier = $db->f("identifier");
		$environment = $db->f("environment");
		$description = $db->f("description");

		$t->set_var("user_name", htmlspecialchars($user_name));
		$t->set_var("user_email", $user_email);
		$t->set_var("identifier", htmlspecialchars($identifier));
		$t->set_var("environment", htmlspecialchars($environment));
		$t->set_var("remote_address", $db->f("remote_address"));
		$t->set_var("product_name", $db->f("product_name"));
		$t->set_var("product", $db->f("product_name"));
		$t->set_var("assign_to", $db->f("assign_to"));

		$t->set_var("type", $db->f("type_name"));
		$current_status = strlen($db->f("status_name")) ? $db->f("status_name") : "New";
		$t->set_var("current_status", $current_status);
		$priority = strlen($db->f("priority_name")) ? $db->f("priority_name") : "Normal";
		$t->set_var("priority", $priority);
		$date_modified = $db->f("date_modified", DATETIME);
		$date_modified_string = va_date($datetime_show_format, $date_modified);
		$t->set_var("date_modified", $date_modified_string);

		$date_added = $db->f("date_added", DATETIME);
		$request_added_string = va_date($datetime_show_format, $date_added);
		$date_added_string = $request_added_string;
		$t->set_var("request_added", $request_added_string);

		$request_viewed = $db->f("date_viewed", DATETIME);

		$t->set_var("summary", htmlspecialchars($summary));
		$t->set_var("request_description", nl2br(htmlspecialchars($description)));

		$last_message = $description;

		$sql  = " UPDATE " . $table_prefix . "support_messages SET date_viewed=" . $db->tosql(va_time(), DATETIME);
		$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
		$sql .= " AND internal=0 AND admin_id_assign_by IS NOT NULL AND admin_id_assign_by<>0 AND date_viewed IS NULL";
		$db->query($sql);

		//$vc = md5($support_id . $date_added[3].$date_added[4].$date_added[5]);
	}	else if(!strlen($errors)) {
		$errors = SUPPORT_WRONG_ID_ERROR;
	}

	if(!strlen($errors) && $vc != md5($support_id . $date_added[3].$date_added[4].$date_added[5])) {
		$errors = SUPPORT_WRONG_CODE_ERROR;
	}

	if(strlen($errors))
	{
		$t->set_var("errors_list", $errors);
		$t->parse("global_errors", false);
		$t->parse("block_body", false);
		$t->parse($block_name, true);
		return;
	}


	$r = new VA_Record($table_prefix . "support_messages");
	$r->add_where("message_id", INTEGER);
	$r->add_textbox("support_id", INTEGER);
	$r->add_textbox("dep_id", INTEGER);
	$r->add_textbox("internal", INTEGER);
	$r->add_textbox("support_status_id", INTEGER);
	//$r->add_textbox("admin_id_assign_by", INTEGER);
	$r->add_textbox("message_text", TEXT, SUPPORT_MESSAGE_FIELD);
	$r->change_property("message_text", PARSE_NAME, "response_message");
	$r->change_property("message_text", REQUIRED, true);
	$r->change_property("message_text", TRIM, true);
	$r->add_textbox("date_added", DATETIME);

	$r->get_form_values();

	$session_rnd = get_session("session_rnd");
	$action = get_param("action");
	$rnd = get_param("rnd");

	if($action && $rnd != $session_rnd)
	{
		$r->validate();

		if(!strlen($r->errors))
		{
			// get status for reply message
			$sql = " SELECT status_id,status_name,status_caption FROM " . $table_prefix . "support_statuses WHERE is_user_reply=1 ";
			$db->query($sql);
			if($db->next_record()) {
				$r->set_value("support_status_id", $db->f("status_id"));	
				$status_name = $db->f("status_name");
				$status_caption = $db->f("status_caption");
			} else {
				$sql = " SELECT status_id,status_name,status_caption FROM " . $table_prefix . "support_statuses WHERE is_user_new=1 ";
				$db->query($sql);
				if($db->next_record()) {
					$r->set_value("support_status_id", $db->f("status_id"));	
					$status_name = $db->f("status_name");
					$status_caption = $db->f("status_caption");
				} else {
					$sql = " SELECT status_id,status_name,status_caption FROM " . $table_prefix . "support_statuses ORDER BY status_id ";
					$db->query($sql);
					if($db->next_record()) {
						$r->set_value("support_status_id", $db->f("status_id"));	
						$status_name = $db->f("status_name");
						$status_caption = $db->f("status_caption");
					}
				}
			}

			$date_added = va_time();
			$r->set_value("dep_id", $dep_id);
			$r->set_value("internal", 0);
			$r->set_value("date_added", $date_added);
			if ($db_type == "postgre") {
				$sql = " SELECT NEXTVAL('seq_" . $table_prefix . "support_messages') ";
				$message_id = get_db_value($sql);
				$r->set_value("message_id", $message_id);
				$r->change_property("message_id", USE_IN_INSERT, true);
			}

			if($r->insert_record())
			{ 
				if ($db_type == "mysql") {
					$sql = " SELECT LAST_INSERT_ID() ";
					$message_id = get_db_value($sql);
				} else if ($db_type == "access") {
					$sql = " SELECT @@IDENTITY ";
					$message_id = get_db_value($sql);
				} else if ($db_type == "db2") {
					$message_id = get_db_value(" SELECT PREVVAL FOR seq_" . $table_prefix . "support_messages FROM " . $table_prefix . "support_messages");
				}
				$r->set_value("message_id", $message_id);

				// update attachments
				$sql  = " UPDATE " . $table_prefix . "support_attachments ";
				$sql .= " SET message_id=" . $db->tosql($message_id, INTEGER);
				$sql .= " , attachment_status=1 ";
				$sql .= " WHERE user_id=" . $db->tosql($user_id, INTEGER);
				$sql .= " AND support_id=" . $db->tosql($support_id, INTEGER);
				$sql .= " AND message_id=0 ";
				$sql .= " AND attachment_status=0 ";
				$db->query($sql);

				// check attachments
				$attachments = array();
				if ($user_id) {
					$sql  = " SELECT attachment_id, file_name, file_path FROM " . $table_prefix . "support_attachments ";
					$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
					$sql .= " AND user_id=" . $db->tosql($user_id, INTEGER);
					$sql .= " AND message_id=" . $db->tosql($message_id, INTEGER);;
					$sql .= " AND attachment_status=1 ";
					$db->query($sql);
					while ($db->next_record()) {
						$filename = $db->f("file_name");
						$filepath = $db->f("file_path");
						$attachments[] = array($filename, $filepath);
					}
				}

				// send email notification to admin
				if($support_settings["admin_notification"])
				{
					$t->set_block("admin_subject", $support_settings["admin_subject"]);
					$t->set_block("admin_message", $support_settings["admin_message"]);

					$date_added_string = va_date($datetime_show_format, $date_added);
					$ticket_url = $support_messages_url . "?support_id=" . $support_id . "&vc=" . $vc;
			  
					$r->set_parameters();
					$t->set_var("vc", $vc);
					$t->set_var("support_url", $ticket_url);
					$t->set_var("ticket_url", $ticket_url);
					$t->set_var("status", $status_name);
					$t->set_var("status_name", $status_name);
					$t->set_var("status_caption", $status_caption);
					$t->set_var("message_added", $date_added_string);
					$t->set_var("date_added", $date_added_string);
					$t->set_var("date_modified", $date_added_string);
					$t->set_var("summary", $summary);
					$t->set_var("user_name", $user_name);
					$t->set_var("identifier", $identifier);
					$t->set_var("environment", $environment);
					$t->set_var("description", $description);
					$t->set_var("message_text", $r->get_value("message_text"));
			  
					$mail_to = get_setting_value($support_settings, "admin_email", $settings["admin_email"]);
					$mail_to = str_replace(";", ",", $mail_to);
					$mail_type = get_setting_value($support_settings, "admin_message_type", 0);
					$email_headers = array();
					$email_headers["from"] = get_setting_value($support_settings, "admin_mail_from", $settings["admin_email"]);
					$email_headers["cc"] = get_setting_value($support_settings, "cc_emails");
					$email_headers["bcc"] = get_setting_value($support_settings, "admin_mail_bcc");
					$email_headers["reply_to"] = get_setting_value($support_settings, "admin_mail_reply_to");
					$email_headers["return_path"] = get_setting_value($support_settings, "admin_mail_return_path");
					$email_headers["mail_type"] = $mail_type;

					$t->parse("admin_subject", false);
					if ($mail_type) {
						$t->set_var("summary", htmlspecialchars($summary));
						$t->set_var("description", nl2br(htmlspecialchars($description)));
						$t->set_var("message_text", nl2br(htmlspecialchars($r->get_value("message_text"))));
						$t->set_var("user_name", htmlspecialchars($user_name));
						$t->set_var("identifier", htmlspecialchars($identifier));
						$t->set_var("environment", htmlspecialchars($environment));
					}
					$t->parse("admin_message", false);

					$admin_message = preg_replace("/\r\n|\r|\n/", $eol, $t->get_var("admin_message"));
					va_mail($mail_to, $t->get_var("admin_subject"), $admin_message, $email_headers, $attachments);
				}

        // update support request info
				$sql  = " UPDATE " . $table_prefix . "support SET ";
				$sql .= " admin_id_assign_to=0, admin_id_assign_by=0, ";
				$sql .= " support_status_id=" . $db->tosql($r->get_value("support_status_id"), INTEGER);
				$sql .= " , date_modified=" . $db->tosql(va_time(), DATETIME);
				$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
		else
		{
			//$errors .= "Please provide information in the sections with red, italicized headings, then click 'Submit'.<br>";	
			set_session("session_rnd", "");
		}
	}
	else // new page (set default values)
	{
		//$r->set_value("is_showing", "1");
	}

	// set ticket information
	$t->set_var("summary", htmlspecialchars($summary));
	$t->set_var("description", htmlspecialchars($description));
	$t->set_var("user_name", htmlspecialchars($user_name));
	$t->set_var("identifier", htmlspecialchars($identifier));
	$t->set_var("environment", htmlspecialchars($environment));

	$n = new VA_Navigator($settings["templates_dir"], "navigator.html", $support_messages_url);

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "support_messages WHERE support_id=" . $db->tosql($support_id, INTEGER) . " AND internal=0 ");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;

	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);
	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	$sql  = " SELECT sm.message_id, sm.admin_id_assign_by,a.admin_name,ss.status_id,ss.status_name, ";
	$sql .= " sm.message_text, sm.date_added, sm.date_viewed ";
	$sql .= " FROM ((" . $table_prefix . "support_messages sm ";
	$sql .= " LEFT JOIN " . $table_prefix . "support_statuses ss ON ss.status_id=sm.support_status_id) ";
	$sql .= " LEFT JOIN " . $table_prefix . "admins a ON a.admin_id=sm.admin_id_assign_by) ";
	$sql .= " WHERE sm.support_id=" . $db->tosql($support_id, INTEGER);
	$sql .= " AND internal=0 ";
	$sql .= " ORDER BY sm.date_added DESC ";

	$db->query($sql);
	if($db->next_record())
	{
		$last_message = $db->f("message_text");

		do
		{
			$message_id = $db->f("message_id");
			$status = strlen($db->f("status_id") > 0) ? $db->f("status_name") : SUPPORT_STATUS_NEW_MSG;
			$t->set_var("status", $status);

			if($db->f("admin_id_assign_by"))
			{
				$posted_by = $db->f("admin_name");
				$viewed_by = SUPPORT_VIEWED_BY_USER_MSG;
			}
			else 
			{
				$posted_by = strlen($user_name) ? $user_name . " <" . $user_email . ">" : $user_email;
				$viewed_by = SUPPORT_VIEWED_BY_ADMIN_MSG;
			}
			$t->set_var("posted_by", htmlspecialchars($posted_by));
			$t->set_var("viewed_by", $viewed_by);

			$date_added = $db->f("date_added", DATETIME);
			$date_added_string = va_date($datetime_show_format, $date_added);
			$t->set_var("date_added", $date_added_string);

			$date_viewed = $db->f("date_viewed", DATETIME);
			if(is_array($date_viewed)) {
				$date_viewed_string = va_date($datetime_show_format, $date_viewed);
				$t->set_var("date_viewed", "<font color=\"blue\">" . $date_viewed_string . "</font>");
			} else {
				$t->set_var("date_viewed", "<font color=\"red\">" . SUPPORT_NOT_VIEWED_MSG . "</font>");
			}

			//-- check for attachments
			$attach_no = 0; $attachments_files = ""; 
			$sql  = " SELECT * FROM " . $table_prefix . "support_attachments ";
			$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
			$sql .= " AND message_id=" . $db->tosql($message_id, INTEGER);
			$sql .= " AND attachment_status=1 ";
			$dba->query($sql);
			if ($dba->next_record()) {
				do {
					$attachment_id = $dba->Record["attachment_id"];
					$attachment_date = $dba->f("date_added", DATETIME);
					$file_name     = $dba->Record["file_name"];
					$file_path     = $dba->Record["file_path"];
					if (file_exists($file_path)) {
						$attach_no++;
						$size	         = get_nice_bytes(filesize($file_path));
						$attachment_vc = md5($attachment_id . $attachment_date[3].$attachment_date[4].$attachment_date[5]);
						$attachments_files  .= $attach_no . ". <a target=\"_blank\" href=\"" . $support_attachment_url . "?atid=" . $attachment_id . "&vc=" . $attachment_vc . "\">" . $file_name . "</a> (" . $size . ")&nbsp;&nbsp;";
					}
				} while ($dba->next_record());
			}
			if ($attach_no > 0) {
				$t->set_var("attachments_files", $attachments_files);
				$t->parse("message_attachments",false);
			} else { 
				$t->set_var("message_attachments","");
			}

			$message_text = $db->f("message_text");
			$message_text = process_message($message_text);
			split_long_words($message_text);
			$t->set_var("message_text", $message_text);

			$t->parse("records", true);
		} while($db->next_record());

	}
	else
	{
		$t->set_var("records", "");
	}


	// parse initial request on the last page
	if ($page_number == ceil($total_records / $records_per_page)) {
  
		$sql = " SELECT status_id,status_name,status_caption FROM " . $table_prefix . "support_statuses WHERE is_user_new=1 ";
		$db->query($sql);
		if ($db->next_record()) {
			$request_new_status = $db->f("status_name");
		} else {
			$request_new_status = "New";
		}
		$t->set_var("status", $request_new_status);
		$t->set_var("posted_by", htmlspecialchars($request_posted_by));
		$t->set_var("date_added", $request_added_string);

		$request_added_string = va_date($datetime_show_format, $date_added);
		$date_added_string = $request_added_string;
		$t->set_var("request_added", $request_added_string);

		$viewed_by = SUPPORT_VIEWED_BY_ADMIN_MSG;
		$t->set_var("viewed_by", $viewed_by);
		if(is_array($request_viewed)) {
			$request_viewed_string = va_date($datetime_show_format, $request_viewed);
			$t->set_var("date_viewed", "<font color=\"blue\">" . $request_viewed_string . "</font>");
		} else {
			$t->set_var("date_viewed", "<font color=\"red\">" . SUPPORT_NOT_VIEWED_MSG . "</font>");
		}

		//-- check for attachments
		$attach_no = 0; $attachments_files = ""; 
		$sql  = " SELECT * FROM " . $table_prefix . "support_attachments ";
		$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
		$sql .= " AND message_id=0 AND attachment_status=1 ";
		$db->query($sql);
		if ($db->next_record()) {
			do {
				$attachment_id = $db->Record["attachment_id"];
				$attachment_date = $db->f("date_added", DATETIME);
				$file_name     = $db->Record["file_name"];
				$file_path     = $db->Record["file_path"];
				if (file_exists($file_path)) {
					$attach_no++;
					$size	         = get_nice_bytes(filesize($file_path));
					$attachment_vc = md5($attachment_id . $attachment_date[3].$attachment_date[4].$attachment_date[5]);
					$attachments_files .= $attach_no . ". <a target=\"_blank\" href=\"" . $support_attachment_url . "?atid=" . $attachment_id . "&vc=" . $attachment_vc . "\">" . $file_name . "</a> (" . $size . ")&nbsp;&nbsp;";
				}
			} while ($db->next_record());
		}
		if ($attach_no > 0) {
			$t->set_var("attachments_files", $attachments_files);
			$t->parse("message_attachments",false);
		} else { 
			$t->set_var("message_attachments","");
		}

  
		$t->set_var("message_text", process_message($description));
  
		$t->parse("records", true);
	}

	if(!strlen($action)) // (set default message text for reply)
	{
		//set last message by default 
		//$last_message = ">" . str_replace("\n", "\n>", $last_message);
		//$r->set_value("message_text", $last_message);
	}


	// check attachments
	$attachments_files = "";
	if ($attachments_users_allowed && $user_id) {
		$sql  = " SELECT attachment_id, file_name, file_path, date_added ";
		$sql .= " FROM " . $table_prefix . "support_attachments ";
		$sql .= " WHERE support_id=" . $db->tosql($support_id, INTEGER);
		$sql .= " AND user_id=" . $db->tosql($user_id, INTEGER);
		$sql .= " AND message_id=0 ";
		$sql .= " AND attachment_status=0 ";
		$db->query($sql);
		while ($db->next_record()) {
			$attachment_id = $db->f("attachment_id");
			$filename = $db->f("file_name");
			$filepath = $db->f("file_path");
			$date_added = $db->f("date_added", DATETIME);
			$attachment_vc = md5($attachment_id . $date_added[3].$date_added[4].$date_added[5]);
			$filesize = filesize($filepath);
			if ($attachments_files) { $attachments_files .= "; "; }
			$attachments_files .= "<a href=\"support_attachment.php?atid=" .$attachment_id. "&vc=".$attachment_vc."\" target=\"_blank\">" . $filename . "</a> (" . get_nice_bytes($filesize) . ")";
		}
		if ($attachments_files) {
			$t->set_var("attached_files", $attachments_files);
			$t->set_var("attachments_class", "display: block;");
		} else {
			$t->set_var("attachments_class", "display: none;");
		}
		$t->parse("attachments_block", false);
	}

	$r->set_parameters();
	$t->set_var("page", $page_number);
	$t->set_var("vc", $vc);
	$t->parse("reply_form", false);
	$t->parse("request_info", false);

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>