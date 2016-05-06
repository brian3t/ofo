<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_settings.php                               ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once($root_folder_path."messages/".$language_code."/cart_messages.php");
	include_once("./admin_common.php");

	check_admin_security("support_settings");

	$message_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$submit_tickets =
		array(
			array(0, FOR_ALL_USERS_MSG),
			array(1, ONLY_LOGGED_IN_USERS_MSG),
			);

	$validation_types = 
		array( 
			array(2, FOR_ALL_USERS_MSG), array(1, UNREGISTERED_USER_ONLY_MSG), array(0, NOT_USED_MSG)
		);

	$attachments_allowed_values = 
		array(
			array(0, POINTS_NOT_ALLOWED_MSG),
			array(1, ONLY_FOR_LOGGED_IN_USERS_MSG),
			);

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_support_settings.html");

	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_property_href", "admin_support_property.php");
	$t->set_var("admin_support_settings_href", "admin_support_settings.php");
	$t->set_var("admin_support_help_href", "admin_support_help.php");
	$t->set_var("admin_email_help_href", "admin_email_help.php");

	$r = new VA_Record($table_prefix . "global_settings");
	
//begin changes related to knowledge base settings
  
	$sql  = " SELECT category_id, category_name, category_path, parent_category_id ";
	$sql .= " FROM " . $table_prefix . "articles_categories";
	$sql .= " ORDER BY parent_category_id, category_order ";
	$db->query($sql);
	if($db->next_record())
	{
		do
		{
			$row_category_id = $db->f("category_id");
			$row_category_name = get_translation($db->f("category_name"));
			$row_category_path = $db->f("category_path");
			$row_parent_category_id = $db->f("parent_category_id");
      $array_knowledge_cat[] = array($row_category_id, $row_category_name, $row_category_path, $row_parent_category_id);
 		} while ($db->next_record());
	}

	$array_almost_complete_cat = array();
	function order_cat($p, &$k)   {
     global $array_knowledge_cat;
     global $array_almost_complete_cat;
	   for ($i=0; $i < sizeof($array_knowledge_cat); $i++)  {
        $cat_name = $array_knowledge_cat[$i];
	      if ($cat_name[3] == $p)  	{
           $array_almost_complete_cat[$k] = $cat_name;
           $k++;
           order_cat($cat_name[0], $k);

        }
     }
  }

  $k = 0;
  order_cat(0,$k);


	$k = 0;
	$array_complete_cat = array();
	$array_complete_cat[$k][0] = "";
	$array_complete_cat[$k][1] = "      ";
	$k++;
	foreach($array_almost_complete_cat as $cat_name) {
		$array_cat = explode(",", $cat_name[2]);
		for($i = 1; $i < count($array_cat); $i++) {
			if(!isset($array_complete_cat[$k][1])) $array_complete_cat[$k][1] = "";
			if ($i > 1)
			   $array_complete_cat[$k][1] .= " - ";
		}
		if(!isset($array_complete_cat[$k][0])) $array_complete_cat[$k][0] = "";
		$array_complete_cat[$k][0] .= $cat_name[0];
		$array_complete_cat[$k][1] .= $cat_name[1];
		$k++;
	}

	$r->add_select("knowledge_category", INTEGER, $array_complete_cat, KNOWLEDGE_CATEGORY_MSG);
	
	$statuses = get_db_values("SELECT status_id, status_name FROM " . $table_prefix . "articles_statuses WHERE is_shown=1", array(array("", "")));
	$r->add_select("knowledge_article_status", INTEGER, $statuses, KNOWLEDGE_ARTICLE_STATUS_MSG);
	
	$r->add_select("submit_tickets", TEXT, $submit_tickets);
	$r->add_radio("use_random_image", TEXT, $validation_types);

	// attachments settings
	$r->add_textbox("attachments_dir", TEXT);
	$r->add_radio("attachments_users_allowed", TEXT, $attachments_allowed_values);
	$r->add_textbox("attachments_users_mask", TEXT);

	$r->add_textbox("intro_text", TEXT);

	// set up html form parameters
	$r->add_checkbox("admin_notification", INTEGER);
	$r->add_textbox("admin_email", TEXT);
	$r->change_property("admin_email", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("admin_mail_from", TEXT);
	$r->change_property("admin_mail_from", REGEXP_MASK, EMAIL_REGEXP);
	$r->add_textbox("cc_emails", TEXT);
	$r->add_textbox("admin_mail_bcc", TEXT);
	$r->add_textbox("admin_mail_reply_to", TEXT);
	$r->add_textbox("admin_mail_return_path", TEXT);
	$r->add_textbox("admin_subject", TEXT);
	$r->add_radio("admin_message_type", TEXT, $message_types);
	$r->add_textbox("admin_message", TEXT);

	//$r->add_checkbox("user_notification", INTEGER);
	$r->add_textbox("user_mail_from", TEXT);
	$r->add_textbox("user_mail_cc", TEXT);
	$r->add_textbox("user_mail_bcc", TEXT);
	$r->add_textbox("user_mail_reply_to", TEXT);
	$r->add_textbox("user_mail_return_path", TEXT);
	$r->add_textbox("user_subject", TEXT);
	$r->add_radio("user_message_type", TEXT, $message_types);
	$r->add_textbox("user_message", TEXT);

	$r->get_form_values();
	
	$param_site_id = get_session("session_site_id");
	
	$operation = get_param("operation");
	$tab = get_param("tab");
	if (!$tab) { $tab = "general"; }	
	$return_page = get_param("rp");
	if (!strlen($return_page)) {
		$return_page = "admin_support.php";
	}

	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		if (!function_exists('imagecreate') && (($r->get_value("use_random_image") == 2) || ($r->get_value("use_random_image") == 1 ))) {	
		  $r->errors .= RANDOM_IMAGE_VALIDATION_ERROR_MSG;
			$r->set_value("use_random_image",0);
		} 

		if(!strlen($r->errors))
		{
			$sql = "DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='support'";
			if ($multisites_version) {
				$sql .= " AND site_id=" . $db->tosql($param_site_id,INTEGER);
			}
			$db->query($sql);
			foreach($r->parameters as $key => $value)
			{
				if ($multisites_version) {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
					$sql .= "'support', '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
					$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				} else {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES (";
					$sql .= "'support', '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ")";
				}
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
	}
	else // get order_info settings
	{
		foreach($r->parameters as $key => $value)
		{
			$sql  = "SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= "WHERE setting_type='support' AND setting_name='" . $key . "'";
			if ($multisites_version) {
				$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
				$sql .= "ORDER BY site_id DESC ";
			}
			$r->set_value($key, get_db_value($sql));
		}
	}

	$sql  = " SELECT property_id, property_name, property_order, property_show, control_type ";
	$sql .= " FROM " . $table_prefix . "support_custom_properties upp ";
	$sql .= " ORDER BY property_order ";
	$db->query($sql);
	if ($db->next_record()) {
		$t->parse("name_properties", false);
		$show_options = array(0 => DONT_SHOW_MSG, 1 => FOR_ALL_USERS_MSG, 2 => NEW_USERS_ONLY_MSG, 3 => REGISTERED_USERS_ONLY_MSG);
		$controls = array(
			"CHECKBOXLIST" => CHECKBOXLIST_MSG, "LABEL" => LABEL_MSG, "LISTBOX" => LISTBOX_MSG,
			"RADIOBUTTON" => RADIOBUTTON_MSG, "TEXTAREA" => TEXTAREA_MSG, "TEXTBOX" => TEXTBOX_MSG);

		do {
			$property_id = $db->f("property_id");
			$property_name = $db->f("property_name");
			$property_order = $db->f("property_order");
			$property_show = $db->f("property_show");
			$control_type = $db->f("control_type");

			$section_name = get_translation($db->f("section_name"));

			$t->set_var("property_id",   $property_id);
			$t->set_var("property_name", $property_name);
			$t->set_var("property_order", $property_order);
			$t->set_var("property_show", $show_options[$property_show]);
			$t->set_var("control_type", $controls[$control_type]);

			$t->set_var("section_name",  ALL_MSG);

			$t->parse("properties", true);
		} while ($db->next_record());
	} else {
		$t->parse("no_properties", false);
	}
	
	$r->set_parameters();
	$t->set_var("rp", htmlspecialchars($return_page));

	// multisites
	if ($sitelist) {
		$sql = " SELECT site_id, site_name FROM " . $table_prefix . "sites ORDER BY site_id ";
		$sites = get_db_values($sql, array());
		set_options($sites, $param_site_id, "param_site_id");		
		$t->parse("sitelist");
	}
	
	$tabs = array(
		"general" => array("title" => ADMIN_GENERAL_MSG), 
		"notification_email" => array("title" => NOTIFICATION_EMAIL_MSG),
		"custom_fields" => array("title" => CUSTOM_FIELDS_MSG)
	);	
	parse_admin_tabs($tabs, $tab, 6);
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

?>
