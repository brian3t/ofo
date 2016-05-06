<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_site.php                                           ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ("./admin_common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "messages/".$language_code."/download_messages.php");

	check_admin_security("admin_sites");

	$param_site_id 	   = get_param("param_site_id");
	$permissions   = get_permissions();
	$add_sites     = get_setting_value($permissions, "add_sites", 0);
	$update_sites  = get_setting_value($permissions, "update_sites", 0);
	// at least one site
	$remove_sites  = (get_setting_value($permissions, "remove_sites", 0) && ($param_site_id!=1));
	$return_page = "admin_sites.php";

	$operation = get_param("operation");
	if (strlen($operation)&& ($operation == "delete") && ($param_site_id>1))
	{
		
		$db->query("DELETE FROM " . $table_prefix . "ads_categories_sites WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "articles_categories_sites WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "categories_sites WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "coupons_sites WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "forum_categories_sites WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "items_sites WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "layouts_sites WHERE site_id=" . $db->tosql($param_site_id, INTEGER));	
		$db->query("DELETE FROM " . $table_prefix . "manuals_categories_sites WHERE site_id=" . $db->tosql($param_site_id, INTEGER));		
		$db->query("DELETE FROM " . $table_prefix . "pages_sites WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "payment_systems_sites WHERE site_id=" . $db->tosql($param_site_id, INTEGER));		
		$db->query("DELETE FROM " . $table_prefix . "shipping_types_sites WHERE site_id=" . $db->tosql($param_site_id, INTEGER));		
		$db->query("DELETE FROM " . $table_prefix . "support_departments_sites  WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "support_products_sites  WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "user_types_sites WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "sites WHERE site_id=" . $db->tosql($param_site_id, INTEGER));		
		$db->query("DELETE FROM " . $table_prefix . "global_settings WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
		$db->query("DELETE FROM " . $table_prefix . "page_settings WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
		header("Location: " . $return_page);
		exit;
		
	} elseif  (strlen($operation)&& ($operation == "clear") && ($param_site_id>1)) {
		
		$clear_global_settings = get_param('clear_global_settings');
		$clear_page_settings   = get_param('clear_page_settings');
	
		if($clear_global_settings) {
			$db->query("DELETE FROM " . $table_prefix . "global_settings WHERE site_id=" . $db->tosql($param_site_id, INTEGER));	
		}		
		if($clear_page_settings) {
			$db->query("DELETE FROM " . $table_prefix . "page_settings WHERE site_id=" . $db->tosql($param_site_id, INTEGER));			
		}		
		header("Location: " . $return_page);
		exit;
	
	} elseif  (strlen($operation)&& ($operation == "duplicate") ) {
		
		$duplicate_global_settings = get_param('duplicate_global_settings');
		$duplicate_page_settings   = get_param('duplicate_page_settings');
		$duplicate_site_id         = get_param('duplicate_site_id');

		if($duplicate_global_settings) {
			$sql  = " SELECT setting_type, setting_name, setting_value FROM " . $table_prefix . "global_settings ";
			if($param_site_id>1) {
				$sql .= " WHERE (site_id=1 OR site_id=" . $db->tosql($duplicate_site_id, INTEGER) . ") ";
				$sql .= " ORDER BY site_id ASC";
			} else {
				$sql .= " WHERE site_id=" . $db->tosql($duplicate_site_id, INTEGER);
			}
			$db->query($sql);			
			$tmp_settings = array();
			while ($db->next_record())	{
				$duplicate_setting_type  = $db->f("setting_type");
				$duplicate_setting_name  = $db->f("setting_name");
				$duplicate_setting_value = $db->f("setting_value");
				$tmp_settings[$duplicate_setting_type][$duplicate_setting_name]=$duplicate_setting_value;
			}
			if($tmp_settings) {
				$db->query("DELETE FROM " . $table_prefix . "global_settings WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
				foreach ($tmp_settings AS $duplicate_setting_type=>$tmp2){
					foreach ($tmp2 AS $duplicate_setting_name=>$duplicate_setting_value){
						$sql  = " INSERT INTO " . $table_prefix . "global_settings ";
						$sql .= " (setting_type,setting_name,setting_value,site_id) VALUES ( ";						
						$sql .= $db->tosql($duplicate_setting_type, TEXT) . ",";
						$sql .= $db->tosql($duplicate_setting_name, TEXT) . ",";
						$sql .= $db->tosql($duplicate_setting_value, TEXT) . ",";
						$sql .= $db->tosql($param_site_id, INTEGER) . ")";
						$db->query($sql);
					}
				}
			}			
		}		
		if($duplicate_page_settings) {
			@set_time_limit(60);
			$sql  = " SELECT layout_id, page_name, setting_name, setting_order, setting_value FROM " . $table_prefix . "page_settings ";
			if($param_site_id>1) {
				$sql .= " WHERE (site_id=1 OR site_id=" . $db->tosql($duplicate_site_id, INTEGER) . ") ";
				$sql .= " ORDER BY site_id ASC";
			} else {
				$sql .= " WHERE site_id=" . $db->tosql($duplicate_site_id, INTEGER);
			}
			$db->query($sql);			
			$tmp_settings = array();
			while ($db->next_record())	{
				$duplicate_layout_id     = $db->f("layout_id");
				$duplicate_page_name     = $db->f("page_name");
				$duplicate_setting_name  = $db->f("setting_name");
				$duplicate_setting_order = $db->f("setting_order");
				$duplicate_setting_value = $db->f("setting_value");
				$tmp_settings[$duplicate_layout_id][$duplicate_page_name][$duplicate_setting_name]=
					array("value"=>$duplicate_setting_value,"order"=>$duplicate_setting_order);
			}
			if($tmp_settings) {
				$db->query("DELETE FROM " . $table_prefix . "page_settings WHERE site_id=" . $db->tosql($param_site_id, INTEGER));
				foreach ($tmp_settings AS $duplicate_layout_id=>$tmp2) {
					foreach ($tmp2 AS $duplicate_page_name =>$tmp3) {
						foreach ($tmp3 AS $duplicate_setting_name=>$tmp4) {
							$sql = " INSERT INTO " . $table_prefix . "page_settings ";
							$sql .= " (layout_id, page_name, setting_name, setting_order, setting_value, site_id) VALUES ( ";						
							$sql .= $db->tosql($duplicate_layout_id, INTEGER) . ",";
							$sql .= $db->tosql($duplicate_page_name, TEXT) . ",";
							$sql .= $db->tosql($duplicate_setting_name, TEXT) . ",";
							$sql .= $db->tosql($tmp4['order'], TEXT) . ",";
							$sql .= $db->tosql($tmp4['value'], TEXT) . ",";
							$sql .= $db->tosql($param_site_id, INTEGER) . "); \n";
							$db->query($sql);
						}
					}
				}
					
			}	
		}			
		header("Location: " . $return_page);
		exit;		
	} else {
		$t = new VA_Template($settings["admin_templates_dir"]);
		$t->set_file("main","admin_site.html");

		$t->set_var("admin_href", "admin.php");
		$t->set_var("admin_site_href", "admin_site.php");
		$t->set_var("admin_sites_href", "admin_sites.php");
		$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_SITE_MSG, CONFIRM_DELETE_MSG));

		$r = new VA_Record($table_prefix . "sites");
		$r->return_page = "admin_sites.php";
		$r->add_where("param_site_id", INTEGER);
		$r->change_property("param_site_id", COLUMN_NAME, "site_id");

		$r->add_textbox("site_name", TEXT, SITE_NAME_MSG);
		$r->parameters["site_name"][REQUIRED] = true;
		$r->parameters["site_name"][UNIQUE] = true;
		$r->parameters["site_name"][MIN_LENGTH] = 3;

		$r->add_textbox("site_description", TEXT, SITE_DESCRIPTION_MSG);


		
		$r->operations[INSERT_ALLOWED] = $add_sites;
		$r->operations[UPDATE_ALLOWED] = $update_sites;
		$r->operations[DELETE_ALLOWED] = $remove_sites;
		$r->process();

		include_once("./admin_header.php");
		include_once("./admin_footer.php");
		
		// multisites
		if ($sitelist) {
			$sites = array();
			$sql  = " SELECT site_id, site_name FROM " . $table_prefix . "sites ";
			$sql .= " WHERE site_id<>" . $db->tosql($param_site_id, INTEGER);
			$sql .= " ORDER BY site_id ";
			$db->query($sql);
			while ($db->next_record())	{
				$duplicate_site_id   = $db->f("site_id");
				$duplicate_site_name = $db->f("site_name");
				$duplicate_site_selected = ($param_site_id==$duplicate_site_id)?"selected":"";
				$t->set_var("duplicate_site_id", $duplicate_site_id);
				$t->set_var("duplicate_site_name",$duplicate_site_name);
				$t->set_var("duplicate_site_selected",$duplicate_site_selected);
				$t->parse("duplicate_site_option");
			}
			$t->parse("duplicate");
		}
		if($param_site_id>1){
			$t->parse("clear");
			
		}
			

		$t->pparse("main");
	}

?>