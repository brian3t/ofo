<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_ads_images_settings.php                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once("./admin_common.php");
	include_once($root_folder_path . "includes/record.php");

	check_admin_security("ads");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_ads_images_settings.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_ads_href", "admin_ads.php");
	$t->set_var("admin_ads_images_settings_href", "admin_ads_images_settings.php");

	$setting_type = "ads_images";
	$r = new VA_Record($table_prefix . "global_settings");

	// set up images parameters
	$r->add_textbox("image_small_size", INTEGER, ADMIN_SIZE_MSG);
	$r->add_textbox("image_small_width", INTEGER, WIDTH_MSG);
	$r->add_textbox("image_small_height", INTEGER, HEIGHT_MSG);
	$r->add_checkbox("image_small_resize", INTEGER);
	$r->add_textbox("image_large_size", INTEGER, ADMIN_SIZE_MSG);
	$r->add_textbox("image_large_width", INTEGER, WIDTH_MSG);
	$r->add_textbox("image_large_height", INTEGER, HEIGHT_MSG);
	$r->add_checkbox("image_large_resize", INTEGER);
	$r->add_textbox("images_number", INTEGER, ADDITIONAL_PICTURES_NUMBER_MSG);

	$r->get_form_values();

	$param_site_id = get_session("session_site_id");
	$operation = get_param("operation");
	$return_page = get_param("rp");
	if(!strlen($return_page)) $return_page = "admin_ads.php";
	$t->set_var("rp", htmlspecialchars($return_page));

	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		$r->validate();

		if(!strlen($r->errors))
		{
			$sql  = " DELETE FROM " . $table_prefix . "global_settings WHERE setting_type=" . $db->tosql($setting_type, TEXT);
			$sql .= " AND site_id=" . $db->tosql($param_site_id, INTEGER);
			$db->query($sql);
			foreach($r->parameters as $key => $value)
			{
				$sql  = "INSERT INTO " . $table_prefix . "global_settings (setting_type, setting_name, setting_value, site_id) VALUES (";
				$sql .= $db->tosql($setting_type, TEXT) . ", '" . $key . "'," . $db->tosql($value[CONTROL_VALUE], TEXT) . ",";
				$sql .= $db->tosql($param_site_id,INTEGER) . ") ";
				$db->query($sql);
			}

			header("Location: " . $return_page);
			exit;
		}
	}
	else // get user_profile settings
	{
		foreach($r->parameters as $key => $value)
		{
			$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
			$sql .= " WHERE setting_type=" . $db->tosql($setting_type, TEXT) . " AND setting_name='" . $key . "'";
			$sql .= " AND ( site_id=1 OR  site_id=" . $db->tosql($param_site_id,INTEGER). ") ";
			$sql .= " ORDER BY site_id DESC ";
			$r->set_value($key, get_db_value($sql));
		}
	}

	$r->set_parameters();
	
	// multisites
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ", "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}	

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->set_var("admin_href", "admin.php");
	$t->pparse("main");

?>