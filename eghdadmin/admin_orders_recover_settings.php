<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_orders_recover_settings.php                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once("./admin_common.php");

	check_admin_security("orders_recover");	

	$setting_type = "order_recover";
	$message_types = 
		array( 
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main", "admin_orders_recover_settings.html");
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_order_final_href", "admin_order_final.php");
	$t->set_var("admin_order_help_href", "admin_order_help.php");
	$t->set_var("admin_email_help_href", "admin_email_help.php");
	$t->set_var("admin_orders_recover_href", "admin_orders_recover.php");
	$t->set_var("admin_orders_recover_settings_href", "admin_orders_recover_settings.php");

	$r = new VA_Record($table_prefix . "global_settings");
	
	$r->add_textbox("lookback_days", INTEGER);
	$r->add_textbox("email_live_days", INTEGER);
	$r->add_checkbox("ignore_active_customers", INTEGER);	
	$r->add_textbox("user_mail_from", TEXT);
	$r->add_textbox("user_mail_cc", TEXT);
	$r->add_textbox("user_mail_bcc", TEXT);
	$r->add_textbox("user_mail_reply_to", TEXT);
	$r->add_textbox("user_mail_return_path", TEXT);
	$r->add_checkbox("user_mail_pdf_invoice", INTEGER);
	$r->add_textbox("user_subject", TEXT);
	$r->add_radio("user_message_type", TEXT, $message_types);
	$r->add_textbox("user_message", TEXT);
	$r->get_form_values();
	
	$param_site_id = get_session("session_site_id");
	$operation = get_param("operation");
	$return_page = get_param("rp");
	if (!strlen($return_page)) $return_page = "admin_orders_recover_settings.php";

	if (strlen($operation))
	{
		if ($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		$is_valid = $r->validate();

		if (!strlen($r->errors))
		{
			$sql = "DELETE FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
			if ($multisites_version) {
				$sql .= " AND site_id=" . $db->tosql($param_site_id,INTEGER);
			}
			$db->query($sql);
			foreach ($r->parameters as $key => $value)
			{
				if ($multisites_version) {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
					$sql .= $db->tosql($setting_type, TEXT) . ", '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
					$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				} else {
					$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value) VALUES (";
					$sql .= $db->tosql($setting_type, TEXT) . ", '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ")";
				}
				
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
	}
	else
	{
		foreach ($r->parameters as $key => $value)
		{
			$sql  = "SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= "WHERE setting_type=" . $db->tosql($setting_type, TEXT) . " AND setting_name='" . $key . "'";
			if ($multisites_version) {
				$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
				$sql .= "ORDER BY site_id DESC ";
			}
			$r->set_value($key, get_db_value($sql));
		}
	}
	$r->set_parameters();
	$t->set_var("rp", htmlspecialchars($return_page));

	// multisites
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ", "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}	

	$t->pparse("main");

?>