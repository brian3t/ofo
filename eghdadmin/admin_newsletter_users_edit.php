<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_newsletter_users_edit.php                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");	
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once("./admin_common.php");

	check_admin_security("newsletter");

	$operation = get_param("operation");
	$ids = get_param("emails_ids");
	$ids_email = split(",", $ids);
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_newsletter_users_edit.html");
	
	$t->set_var("emails_ids", $ids);
	$t->set_var("admin_href", "admin.php");
	$t->set_var("edit_newsletter_emails",  NEWSLETTER_USERS_MSG);
	$t->set_var("admin_newsletter_users_edit_href", "admin_newsletter_users_edit.php");

	$return_page = get_param("rp");
	$page = get_param("page");
	if (!strlen($return_page)) {
		$return_page = "admin_newsletter_users.php?page=" . urlencode($page);
	}

	$r = new VA_Record($table_prefix . "newsletters_users", "emails");

	$r->add_where("email_id", INTEGER);
	$r->add_textbox("email", TEXT, EMAIL_FIELD);
	$r->change_property("email", REQUIRED, true);
	//$r->change_property("email", UNIQUE, true);
	$r->change_property("email", REGEXP_MASK, EMAIL_REGEXP);
	$r->change_property("email", USE_SQL_NULL, false);
	$r->add_hidden("date_added", DATETIME, DATE_MSG);
	$r->change_property("date_added", USE_IN_INSERT, true);

	$number_emails = get_param("number_emails");
	$more_emails = get_param("more_emails");

	$eg = new VA_EditGrid($r, "emails");
	$eg->get_form_values($number_emails);

	if (strlen($operation) && !$more_emails)
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $eg->validate();
		if ($is_valid)
		{
			$eg->set_values("date_added", va_time());
			$eg->update_all($number_emails);
			header("Location: " . $return_page);
			exit;
		}
	}
	elseif (strlen($ids) && !$more_emails)
	{
		$number_emails = $eg->get_db_values();
		$sql  = " SELECT email_id, email, date_added ";
		$sql .= " FROM " . $table_prefix . "newsletters_users ";
		$sql .= " WHERE email_id IN (" . $db->tosql($ids, TEXT, false) . ")";
	
		$db->query($sql);
		while ($db->next_record()) {
			$number_emails++;
			$eg->values[$number_emails]["email_id"] = $db->f("email_id");
			$eg->values[$number_emails]["email"] = $db->f("email");
			$eg->values[$number_emails]["date_added"] = $db->f("date_added");
		}
		if ($number_emails == 0) {
			$number_emails = 5;
		}
	}
	elseif ($more_emails)
	{
		$number_emails += 5;
	}
	else // set default values
	{
		$number_emails = 5;
	}

	$t->set_var("number_emails", $number_emails);
	$eg->set_parameters_all($number_emails);

	if (strlen($ids)) {
		$t->set_var("save_button", " " . UPDATE_BUTTON . " ");
	} else {
		$t->set_var("save_button", " " . ADD_BUTTON . " ");
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");	

?>