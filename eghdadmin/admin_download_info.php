<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_download_info.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("downloadable_products");

	$message_types =
		array(
			array(1, HTML_MSG), array(0, PLAIN_TEXT_MSG)
		);

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_download_info.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_download_info_href", "admin_download_info.php");
	$t->set_var("admin_order_help_href", "admin_order_help.php");
	$t->set_var("admin_email_help_href", "admin_email_help.php");

	$r = new VA_Record($table_prefix . "global_settings");

	$r->add_textbox("max_downloads", TEXT);
	$r->add_textbox("downloads_admins_dir", TEXT);
	$r->change_property("downloads_admins_dir", BEFORE_VALIDATE, "check_admins_dir");
	$r->add_textbox("downloads_admins_mask", TEXT);
	$r->add_textbox("downloads_users_dir", TEXT);
	$r->change_property("downloads_users_dir", BEFORE_VALIDATE, "check_users_dir");
	$r->add_textbox("downloads_users_mask", TEXT);

	$r->add_textbox("links_from", TEXT);
	$r->add_textbox("links_cc", TEXT);
	$r->add_textbox("links_bcc", TEXT);
	$r->add_textbox("links_reply_to", TEXT);
	$r->add_textbox("links_return_path", TEXT);
	$r->add_textbox("links_subject", TEXT);
	$r->add_radio("links_message_type", TEXT, $message_types);
	$r->add_textbox("links_message", TEXT);

	$r->add_textbox("serials_from", TEXT);
	$r->add_textbox("serials_cc", TEXT);
	$r->add_textbox("serials_bcc", TEXT);
	$r->add_textbox("serials_reply_to", TEXT);
	$r->add_textbox("serials_return_path", TEXT);
	$r->add_textbox("serials_subject", TEXT);
	$r->add_radio("serials_message_type", TEXT, $message_types);
	$r->add_textbox("serials_message", TEXT);

	$r->add_textbox("vouchers_from", TEXT);
	$r->add_textbox("vouchers_cc", TEXT);
	$r->add_textbox("vouchers_bcc", TEXT);
	$r->add_textbox("vouchers_reply_to", TEXT);
	$r->add_textbox("vouchers_return_path", TEXT);
	$r->add_textbox("vouchers_subject", TEXT);
	$r->add_radio("vouchers_message_type", TEXT, $message_types);
	$r->add_textbox("vouchers_message", TEXT);

	$r->get_form_values();

	$param_site_id = get_session("session_site_id");
	$operation = get_param("operation");
	$return_page = get_param("rp");
	if(!strlen($return_page)) $return_page = "admin.php";
	$errors = "";

	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $r->validate();

		if(!strlen($r->errors))
		{
			$sql  = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type='download_info'";
			$sql .= " AND site_id=" . $db->tosql($param_site_id, INTEGER);
			$db->query($sql);
			foreach($r->parameters as $key => $value)
			{
				$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
				$sql .= $db->tosql("download_info", TEXT) . ", '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
				$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
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
			$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type='download_info' AND setting_name='" . $key . "'";
			$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
			$sql .= " ORDER BY site_id DESC ";
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
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");

	function check_admins_dir() 
	{
		check_dir("downloads_admins_dir");
	}

	function check_users_dir() 
	{
		check_dir("downloads_users_dir");
	}

	function check_dir($param_name) 
	{
		global $r;

		$dir_name = $r->get_value($param_name);
		if ($dir_name) {
			if (preg_match("/\//", $dir_name)) {
				if (!preg_match("/\/$/", $dir_name)) { $dir_name .= "/"; }
			} else if (preg_match("/\\\\/", $dir_name)) {
				if (!preg_match("/\\\\$/", $dir_name)) { $dir_name .= "\\"; }
			}
			$r->set_value($param_name, $dir_name);
			if (!is_dir($dir_name)) {
				$r->errors .= FOLDER_DOESNT_EXIST_MSG . $dir_name . "<br>";
			} else {
				$tmp_file = $dir_name . "tmp_" . md5(uniqid(rand(), true)) . ".txt";
				$fp = @fopen($tmp_file, "w");
				if ($fp === false) {
					$r->errors .= str_replace("{folder_name}", $dir_name, FOLDER_PERMISSION_MESSAGE) . "<br>";
				} else {
					fclose($fp);
					unlink($tmp_file);
				}
			}
		}
	}

?>