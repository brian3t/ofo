<?php

function subscribe_form($block_name)
{
	global $t, $db, $table_prefix;
	global $category_id;
	global $page_settings;

	if(get_setting_value($page_settings, $block_name . "_column_hide", 0)) {
		return;
	}

	$t->set_file("block_body", "block_subscribe.html");

	$error_desc   = "";
	$message_desc = "";
	$unsubscribe  = get_param("unsubscribe");

	$query_string = transfer_params("", true);
	if ($unsubscribe != 1) {
		$query_string .= ($query_string) ? "&" : "?";
		$query_string .= "unsubscribe=1";
	}
	
	$t->set_var("query_string", $query_string);

	$t->set_var("EMAIL_FIELD", EMAIL_FIELD);
	if ($unsubscribe == 1) {
		$unsubscribed_email = get_param("unsubscribed_email");
		$t->set_var("UNSUBSCRIBE_BUTTON", UNSUBSCRIBE_BUTTON);
		$t->set_var("UNSUBSCRIBE_TITLE",  UNSUBSCRIBE_TITLE);

		$unsubscribe_desc = str_replace("{button_name}", UNSUBSCRIBE_BUTTON, UNSUBSCRIBE_FORM_MSG);
		$t->set_var("UNSUBSCRIBE_FORM_MSG", $unsubscribe_desc);
		$t->set_var("SUBSCRIBE_LINK_MSG", SUBSCRIBE_LINK_MSG);

		if (strlen($unsubscribed_email)) {
			if(preg_match(EMAIL_REGEXP, $unsubscribed_email)) {
				$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "newsletters_users ";
				$sql .= " WHERE email=" . $db->tosql($unsubscribed_email, TEXT);
				$db->query($sql);
				$db->next_record();
				$email_count = $db->f(0);
				if($email_count > 0) {
					$sql  = " DELETE FROM " . $table_prefix . "newsletters_users ";
					$sql .= " WHERE email=" . $db->tosql($unsubscribed_email, TEXT);
					$db->query($sql);
					$message_desc = UNSUBSCRIBED_MSG;
				} else {
					$error_desc = UNSUBSCRIBED_ERROR_MSG;
				}
			} else {
				$error_desc = INVALID_EMAIL_MSG;
			}
		}
		if ($message_desc) {
			$t->set_var("message_desc", $message_desc);
			$t->parse("unsubscribe_message", false);
		}
		if ($error_desc) {
			$t->set_var("unsubscribed_email", htmlspecialchars($unsubscribed_email));
			$t->set_var("error_desc", $error_desc);
			$t->parse("unsubscribe_error", false);
		} else {
			$t->set_var("unsubscribed_email", "");
		}

		$t->parse("unsubscribe_form", false);
	} else {

		$subscribed_email = get_param("subscribed_email");

		$t->set_var("SUBSCRIBE_BUTTON", SUBSCRIBE_BUTTON);
		$t->set_var("SUBSCRIBE_TITLE",  SUBSCRIBE_TITLE);

		$subscribe_desc = str_replace("{button_name}", SUBSCRIBE_BUTTON, SUBSCRIBE_FORM_MSG);
		$t->set_var("SUBSCRIBE_FORM_MSG", $subscribe_desc);
		$t->set_var("UNSUBSCRIBE_LINK_MSG", UNSUBSCRIBE_LINK_MSG);

		if (strlen($subscribed_email)) {
			if(preg_match(EMAIL_REGEXP, $subscribed_email)) {
				$sql  = " SELECT COUNT(*) FROM " . $table_prefix . "newsletters_users ";
				$sql .= " WHERE email=" . $db->tosql($subscribed_email, TEXT);
				$db->query($sql);
				$db->next_record();
				$email_count = $db->f(0);
				if($email_count > 0) {
					$message_desc = ALREADY_SUBSCRIBED_MSG;
				} else {
					$sql  = " INSERT INTO " . $table_prefix . "newsletters_users (email, date_added) ";
					$sql .= " VALUES (";
					$sql .= $db->tosql($subscribed_email, TEXT) . ", ";
					$sql .= $db->tosql(va_time(), DATETIME) . ") ";
					$db->query($sql);
					$message_desc = SUBSCRIBED_MSG;
				}
			} else {
				$error_desc = INVALID_EMAIL_MSG;
			}
		}

		if ($message_desc) {
			$t->set_var("message_desc", $message_desc);
			$t->parse("subscribe_message", false);
		}
		if ($error_desc) {
			$t->set_var("subscribed_email", htmlspecialchars($subscribed_email));
			$t->set_var("error_desc", $error_desc);
			$t->parse("subscribe_error", false);
		} else {
			$t->set_var("subscribed_email", "");
		}

		$t->parse("subscribe_form", false);
	}

	$t->parse("block_body", false);
	$t->parse($block_name, true);

}

?>