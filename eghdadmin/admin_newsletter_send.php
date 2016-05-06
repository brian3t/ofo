<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_newsletter_send.php                                ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	
	check_admin_security("newsletter");
	
	$errors = "";
	$eol = get_eol();
	$operation = get_param("operation");
	$newsletter_id = get_param("newsletter_id");
	$emails_qty = get_param("emails_qty");
	$emails_delay = get_param("emails_delay");
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_newsletter_send.html");
	$t->set_var("admin_newsletter_send_href", "admin_newsletter_send.php");
	$t->set_var("newsletter_id", $newsletter_id);
	
	$t->pparse("newsletter_header");
	
	$sql  = " SELECT * FROM " . $table_prefix . "newsletters ";
	$sql .= " WHERE newsletter_id=" . $db->tosql($newsletter_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
		$mail_type = $db->f("mail_type");
		$mail_from = $db->f("mail_from");
		$mail_reply_to = $db->f("mail_reply_to");
		$mail_return_path = $db->f("mail_return_path");
		$newsletter_subject = $db->f("newsletter_subject");
		$newsletter_body = $db->f("newsletter_body");
		$is_active = $db->f("is_active");
		$is_sent = $db->f("is_sent");
		$is_prepared = $db->f("is_prepared");
		$emails_left = $db->f("emails_left");
		$emails_sent = $db->f("emails_sent");
		$users_recipients = $db->f("users_recipients");
		$admins_recipients = $db->f("admins_recipients");
		$orders_recipients = $db->f("orders_recipients");
		$subscribed_recipients = $db->f("subscribed_recipients");
	} else {
		$errors = NEWSLETTER_WASNT_FOUND_MSG;
	}
	
	if ($is_sent) {
		$errors = NEWSLETTER_SENT_MSG;
	} elseif (!$is_active) {
		$errors = NEWSLETTER_ISNT_ACTIVE_MSG;
	}
	
	if (!$is_prepared && !$is_sent) {
		if ($subscribed_recipients == "all") {
			$sql  = " INSERT INTO " . $table_prefix . "newsletters_emails (newsletter_id,user_email) ";
			$sql .= " SELECT " . $db->tosql($newsletter_id, INTEGER) . ",email FROM " . $table_prefix . "newsletters_users ";
			$sql .= " WHERE email IS NOT NULL AND email<>'' ";
			$sql .= " GROUP BY email ";
			$db->query($sql);
		}
		if ($users_recipients == "all") {
			$sql  = " INSERT INTO " . $table_prefix . "newsletters_emails (newsletter_id,user_email,user_name) ";
			$sql .= " SELECT " . $db->tosql($newsletter_id, INTEGER) . ",email,name FROM " . $table_prefix . "users ";
			$sql .= " WHERE email IS NOT NULL AND email<>'' ";
			$sql .= " AND u.is_approved=1 ";
			$sql .= " GROUP BY email,name ";
			$db->query($sql);
		} elseif (preg_match("/^(\d+)(,\d+)*$/", $users_recipients)) {
			$sql  = " INSERT INTO " . $table_prefix . "newsletters_emails (newsletter_id,user_email,user_name) ";
			$sql .= " SELECT " . $db->tosql($newsletter_id, INTEGER) . ",email,name ";
			$sql .= " FROM " . $table_prefix . "users u, ". $table_prefix . "user_types ut ";
			$sql .= " WHERE u.user_type_id=ut.type_id ";
			$sql .= " AND ut.type_id IN (" . $users_recipients . ") ";
			$sql .= " AND u.is_approved=1 ";
			$sql .= " AND u.email IS NOT NULL AND u.email<>'' ";
			$sql .= " GROUP BY u.email, u.name ";
			$db->query($sql);
		}
		if ($admins_recipients == "all") {
			$sql  = " INSERT INTO " . $table_prefix . "newsletters_emails (newsletter_id,user_email,user_name) ";
			$sql .= " SELECT " . $db->tosql($newsletter_id, INTEGER) . ",email,admin_name FROM " . $table_prefix . "admins ";
			$sql .= " WHERE email IS NOT NULL AND email<>'' ";
			$sql .= " GROUP BY email,admin_name ";
			$db->query($sql);
		} elseif (preg_match("/^(\d+)(,\d+)*$/", $admins_recipients)) {
			$sql  = " INSERT INTO " . $table_prefix . "newsletters_emails (newsletter_id,user_email,user_name) ";
			$sql .= " SELECT " . $db->tosql($newsletter_id, INTEGER) . ",a.email,a.admin_name ";
			$sql .= " FROM " . $table_prefix . "admins a, " . $table_prefix . "admin_privileges ap ";
			$sql .= " WHERE a.privilege_id=ap.privilege_id ";
			$sql .= " AND a.privilege_id IN (" . $admins_recipients . ") ";
			$sql .= " AND a.email IS NOT NULL AND a.email<>'' ";
			$sql .= " GROUP BY a.email,a.admin_name ";
			$db->query($sql);
		}
	
		if (preg_match("/^(\d+)(,\d+)*$/", $orders_recipients)) {
			$sql  = " INSERT INTO " . $table_prefix . "newsletters_emails (newsletter_id,user_email,user_name) ";
			$sql .= " SELECT " . $db->tosql($newsletter_id, INTEGER) . ",o.email,o.name ";
			$sql .= " FROM " . $table_prefix . "orders o, " . $table_prefix . "order_statuses os ";
			$sql .= " WHERE o.order_status=os.status_id ";
			$sql .= " AND o.order_status IN (" . $orders_recipients. ") ";
			$sql .= " AND o.email IS NOT NULL AND o.email<>'' ";
			$sql .= " GROUP BY o.email,o.name ";
			$db->query($sql);
		}
	
		// count emails
		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "newsletters_emails ";
		$sql .= " WHERE newsletter_id=" . $db->tosql($newsletter_id, INTEGER) . " AND is_sent=0";
		$db->query($sql);
		$db->next_record();
		$emails_left = $db->f(0);
	
		// update table with emails qty
		$sql  = " UPDATE " . $table_prefix . "newsletters ";
		$sql .= " SET emails_left=" . $db->tosql($emails_left, INTEGER);
		$sql .= " , is_prepared=1 ";
		$sql .= " WHERE newsletter_id=" . $db->tosql($newsletter_id, INTEGER);
		$db->query($sql);
	}
	
	if ($emails_left < 1 && !strlen($errors)) {
		$errors = NO_EMAILS_FOR_NEWSLETTER_MSG;
	}
	
	if(strlen($errors))	{
		$t->set_var("errors_list", $errors);
		$t->pparse("errors", false);
	}
	
	if ($operation == "send" && !strlen($errors)) {
		$t->pparse("newsletter_sending", false);
		flush();
	
		if ($emails_sent < 1) {
			// update mailing_start field
			$sql  = " UPDATE " . $table_prefix . "newsletters ";
			$sql .= " SET mailing_start=" . $db->tosql(va_time(), DATETIME);
			$sql .= " WHERE newsletter_id=" . $db->tosql($newsletter_id, INTEGER);
			$db->query($sql);
		}
		$emails_index = 0;
		$emails = array();
		$sql  = " SELECT * FROM " . $table_prefix . "newsletters_emails ";
		$sql .= " WHERE newsletter_id=" . $db->tosql($newsletter_id, INTEGER) . " AND is_sent=0";
		$db->RecordsPerPage = $emails_qty;
		$db->PageNumber = 1;
		$db->query($sql);
		while ($db->next_record()) {
			$emails[$emails_index]["user_email"] = $db->f("user_email");
			$emails[$emails_index]["user_name"] = $db->f("user_name");
			$emails[$emails_index]["email_id"] = $db->f("email_id");
			$emails_index++;
		}
	
		$email_headers = array();
		$email_headers["from"] = $mail_from;
		$email_headers["reply_to"] = $mail_reply_to;
		$email_headers["return_path"] = $mail_return_path;
		$email_headers["mail_type"] = $mail_type;
	
		echo "&nbsp;&nbsp;&nbsp;";
		$cycle_sent = 0;
		$total_errors = 0;
		for ($i = 0; $i < $emails_index; $i++) {
			$user_email = $emails[$i]["user_email"];
			$user_name = $emails[$i]["user_name"];
			$user_body = str_replace("{email}", $user_email, $newsletter_body);
			$user_body = str_replace("{name}", $user_name, $user_body);
			$user_body = preg_replace("/\r\n|\r|\n/", $eol, $user_body);
			$email_sent = va_mail($user_email, $newsletter_subject, $user_body, $email_headers);

			// delete email from newsletters_emails
			$sql  = " DELETE FROM " . $table_prefix . "newsletters_emails ";
			$sql .= " WHERE user_email = " . $db->tosql($user_email, TEXT);
			$sql .= " AND newsletter_id = " . $db->tosql($newsletter_id, INTEGER);
			$sql .= " AND is_custom = 0";
			$db->query($sql);

			// increment table by one
			if ($email_sent) {
				$sql  = " UPDATE " . $table_prefix . "newsletters ";
				$sql .= " SET emails_sent=emails_sent+1 ";
				$sql .= " WHERE newsletter_id=" . $db->tosql($newsletter_id, INTEGER);
				$db->query($sql);

				$sql  = " UPDATE " . $table_prefix . "newsletters_emails ";
				$sql .= " SET is_sent = 1 WHERE user_email = " . $db->tosql($user_email, TEXT);
				$sql .= " AND is_custom = 1";
				$db->query($sql);
				$cycle_sent++;
			} else {
				$total_errors++;
			}
			echo " . ";
			flush();
			if($emails_delay == 1000000) {
				sleep(1);
			} elseif ($emails_delay > 0) {
				usleep($emails_delay);
			}
			if ($i > 0 && $i % 50 == 0) {
				echo EMAILS_SENT_MSG . $i . "<br>";
				echo "&nbsp;&nbsp;&nbsp;";
			}
		}

		if ($i % 50 != 0 && $i > 0) {
			echo $i . EMAILS_SENT_MSG;
		}
	
		// update newsletter status
		// count remaining emails
		$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "newsletters_emails ";
		$sql .= " WHERE newsletter_id=" . $db->tosql($newsletter_id, INTEGER) . " AND is_sent=0";
		$db->query($sql);
		$db->next_record();
		$emails_left = $db->f(0);
	
		// update table with emails qty
		$sql  = " UPDATE " . $table_prefix . "newsletters ";
		$sql .= " SET emails_left=" . $db->tosql($emails_left, INTEGER);
		if($emails_left < 1) {
			$sql .= " , is_sent=1 ";
		}
		$sql .= " WHERE newsletter_id=" . $db->tosql($newsletter_id, INTEGER);
		$db->query($sql);
	
		if ($emails_left < 1) {
			// update mailing_end field
			$sql  = " UPDATE " . $table_prefix . "newsletters ";
			$sql .= " SET mailing_end=" . $db->tosql(va_time(), DATETIME);
			$sql .= " WHERE newsletter_id=" . $db->tosql($newsletter_id, INTEGER);
			$db->query($sql);
		}
	
		$t->set_var("emails_sent", $cycle_sent);
		$t->set_var("total_emails_sent", ($cycle_sent + $emails_sent));
		$t->set_var("emails_left", $emails_left);
		if ($total_errors) {
			$t->set_var("total_errors", $total_errors);
			$t->parse("emails_errors");
		}
	
		$t->pparse("newsletter_stats", false);
	
		if ($emails_left > 0) {
			$newsletter_href  = "admin_newsletter_send.php?operation=send";
			$newsletter_href .= "&newsletter_id=" . urlencode($newsletter_id);
			$newsletter_href .= "&emails_qty=" . urlencode($emails_qty);
			$newsletter_href .= "&emails_delay=" . urlencode($emails_delay);
			$t->set_var("admin_newsletter_send_href", $newsletter_href);
			$t->pparse("newsletter_refresh", false);
		} else {
	
		}
	} else {
		$t->set_var("mail_from", $mail_from);
		$t->set_var("mail_reply_to", $mail_reply_to);
		$t->set_var("mail_return_path", $mail_return_path);
		$t->set_var("newsletter_subject", $newsletter_subject);
		if (!$mail_type) {
			$newsletter_body = nl2br(htmlspecialchars($newsletter_body));
		}
		$t->set_var("newsletter_body", $newsletter_body);
	
		$t->set_var("emails_left", intval($emails_left));
		$t->set_var("emails_sent", intval($emails_sent));
		$t->pparse("newsletter_preview", false);
		$t->pparse("newsletter_form", false);
	}
	
	$t->pparse("newsletter_footer");
	
?>