<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_poll.php                                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/editgrid.php");

	include_once("./admin_common.php");

	check_admin_security("polls");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_poll.html");

	$t->set_var("admin_poll_href", "admin_poll.php");
	$t->set_var("admin_polls_href", "admin_polls.php");
	$t->set_var("date_edit_format", join("", $date_edit_format));
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", POLL_TITLE, CONFIRM_DELETE_MSG));

	$poll_types = 
		array(			
			array("1", SINGLE_CHOICE_MSG),
			array("2", MULTIPLE_CHOICES_MSG)
			);

	// set up html form parameters
	$r = new VA_Record($table_prefix . "polls");
	$r->add_where("poll_id", INTEGER);
	$r->change_property("poll_id", USE_IN_INSERT, true);
	$r->add_textbox("question", TEXT, POLL_QUESTION_MSG);
	$r->parameters["question"][REQUIRED] = true;
	$r->add_select("poll_type", INTEGER, $poll_types, POLL_TYPE_MSG);
	$r->parameters["poll_type"][REQUIRED] = true;
	$r->add_checkbox("is_active", INTEGER);
	$r->add_textbox("date_added", DATETIME);
	$r->change_property("date_added", REQUIRED, true);
	$r->change_property("date_added", VALUE_MASK, $date_edit_format);
	$r->change_property("date_added", DEFAULT_VALUE, va_time());
	$r->add_textbox("total_votes", INTEGER);
	$r->change_property("total_votes", USE_IN_UPDATE, false);

	$r->add_hidden("category_id", "category_id");

	$r->get_form_values();

	$ipv = new VA_Record($table_prefix . "polls_options", "options");
	$ipv->add_where("poll_option_id", INTEGER);
	$ipv->add_hidden("poll_id", INTEGER);
	$ipv->change_property("poll_id", USE_IN_INSERT, true);

	$ipv->add_textbox("option_description", TEXT, POLL_CHOICES_MSG);
	$ipv->parameters["option_description"][REQUIRED] = true;

	$ipv->add_checkbox("is_default_value", INTEGER);
	
	$poll_id = get_param("poll_id");

	$more_options = get_param("more_options");
	$number_options = get_param("number_options");

	$eg = new VA_EditGrid($ipv, "options");
	$eg->get_form_values($number_options);

	$operation = get_param("operation");

	$return_page = "admin_polls.php";

	if(strlen($operation) && !$more_options)
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		else if($operation == "delete" && $poll_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "polls WHERE poll_id=" . $db->tosql($poll_id, INTEGER));		
			$db->query("DELETE FROM " . $table_prefix . "polls_options WHERE poll_id=" . $db->tosql($poll_id, INTEGER));		
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $r->validate();
		$is_valid = ($eg->validate() && $is_valid); 

		if($is_valid)
		{
			if(strlen($poll_id))
			{
				$r->update_record();
				$eg->set_values("poll_id", $poll_id);
				$eg->update_all($number_options);
			}
			else
			{
				$db->query("SELECT MAX(poll_id) FROM " . $table_prefix . "polls");
				$db->next_record();
				$poll_id = $db->f(0) + 1;
				$r->set_value("poll_id", $poll_id);
				$r->set_value("total_votes", 0);
				$r->insert_record();
				$eg->set_values("poll_id", $poll_id);
				$eg->insert_all($number_options);
			}
			header("Location: " . $return_page);
			exit;
		}
	}
	else if(strlen($poll_id) && !$more_options)
	{
		$r->get_db_values();
		$eg->set_value("poll_id", $poll_id);
		$eg->change_property("poll_option_id", USE_IN_SELECT, true);
		$eg->change_property("poll_option_id", USE_IN_WHERE, false);
		$eg->change_property("poll_id", USE_IN_WHERE, true);
		$eg->change_property("poll_id", USE_IN_SELECT, true);
		$number_options = $eg->get_db_values();
		if($number_options == 0)
			$number_options = 5;
	}
	else if($more_options)
	{
		$number_options += 5;
	}
	else
	{
		$number_options = 5;
		$r->set_value("poll_type", "1");
		$r->set_value("date_added", va_time());
	}

/*
	if(strlen($errors))
	{
		$t->set_var("errors_list", $errors);
		$t->parse("errors", false);
	}
	else
	{
		$t->set_var("errors", "");
	}
*/

	$t->set_var("number_options", $number_options);

	$eg->set_parameters_all($number_options);
	$r->set_parameters();

	if(strlen($poll_id))	
	{
		$t->set_var("save_button", UPDATE_BUTTON);
		$t->parse("delete", false);	
	}
	else
	{
		$t->set_var("save_button", ADD_POLL_MSG);
		$t->set_var("delete", "");	
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

?>