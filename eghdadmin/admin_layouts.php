<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_layouts.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once("./admin_common.php");

	check_admin_security("layouts");

	$va_license_code = va_license_code();

	// additional connection 
	$dbs = new VA_SQL();
	$dbs->DBType      = $db_type;
	$dbs->DBDatabase  = $db_name;
	$dbs->DBUser      = $db_user;
	$dbs->DBPassword  = $db_password;
	$dbs->DBHost      = $db_host;
	$dbs->DBPort      = $db_port;
	$dbs->DBPersistent= $db_persistent;

	$param_site_id = get_session("session_site_id");
	// get default site layout
	$sql  = " SELECT setting_value FROM " . $table_prefix . "global_settings ";
	$sql .= " WHERE setting_type='global' AND setting_name='layout_id' AND site_id=" . $db->tosql($param_site_id, INTEGER);
	$default_layout_id = get_db_value($sql);

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_layouts.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_cms_href", "admin_cms.php");
	$t->set_var("admin_layout_href", "admin_layout.php");
	$t->set_var("admin_layout_header_href", "admin_layout_header.php");
	$t->set_var("admin_header_menus_href", "admin_header_menus.php");
	$t->set_var("admin_layout_page_href", "admin_layout_page.php");
	$t->set_var("admin_items_list_href", "admin_items_list.php");
	$t->set_var("admin_articles_href", "admin_articles.php");
	$t->set_var("admin_layouts_url", urlencode("admin_layouts.php"));
	$t->set_var("admin_custom_menus_href", "admin_custom_menus.php");
	$t->set_var("admin_custom_blocks_href", "admin_custom_blocks.php");

	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_layouts.php");
	$s->set_sorter(ID_MSG, "sorter_layout_id", "1", "layout_id");
	$s->set_sorter(LAYOUT_NAME_MSG, "sorter_layout_name", "2", "layout_name");

	$n = new VA_Navigator($settings["admin_templates_dir"], "navigator.html", "admin_layouts.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// set up variables for navigator
	$db->query("SELECT COUNT(*) FROM " . $table_prefix . "layouts");
	$db->next_record();
	$total_records = $db->f(0);
	$records_per_page = 25;
	$pages_number = 5;
	$page_number = $n->set_navigator("navigator", "page", SIMPLE, $pages_number, $records_per_page, $total_records, false);

	$db->RecordsPerPage = $records_per_page;
	$db->PageNumber = $page_number;
	
	$sql  = " SELECT lt.* ";
	$sql .= " FROM " . $table_prefix . "layouts AS lt"; 
	$sql .= $s->order_by;	
	$db->query($sql);
	if($db->next_record())
	{
		$t->set_var("no_records", "");
		do
		{
			$layout_id = $db->f("layout_id");
			$layout_name = $db->f("layout_name");
			$style_name = $db->f("style_name");
			$sites_all = $db->f("sites_all");

			$t->set_var("layout_id", $layout_id);

			$style_lc = strtolower($style_name);
			if ($dir = @opendir($root_folder_path . "styles")) 
			{
				$dir_values = array();
				while($file = readdir($dir)) {
					if ( preg_match("/^" . $style_lc . "\_/", $file) ) { 
						$dir_values[] = $file;
			    } 
			  }  
				closedir($dir);
				if(sizeof($dir_values) > 1) {
					$layout_name .= " :: <a href=\"admin_layout_scheme.php?layout_id=" . $layout_id . "\">" . CHANGE_ACTIVE_SCHEME_MSG . "</a>";
				}
			}

			$is_site_layout = false;
			if ($sites_all) {
				$is_site_layout = true;
			} else {
				// check if layout available for current site
				$sql  = " SELECT site_id FROM " . $table_prefix . "layouts_sites ";
				$sql .= " WHERE layout_id=" . $db->tosql($layout_id, INTEGER);
				$sql .= " AND site_id=" . $db->tosql($param_site_id, INTEGER, true, false);
				$dbs->query($sql);
				if ($dbs->next_record()) {
					$is_site_layout = true;
				}
			}

			if ($default_layout_id == $layout_id) {
				$layout_status = "<b>".ACTIVE_MSG."</b>";
				$t->set_var("active_style", "");
			} else if ($is_site_layout) {
				$layout_status = "<a class=\"small\" href=\"admin_layout.php?set_default_layout_id=" . $layout_id . "\">" . MAKE_ACTIVE_MSG . "</a>";
				$t->set_var("active_style", "nonactive");
			} else {
				$layout_status = "<span class=\"nonactive\">".NOT_AVAILABLE_MSG."</span>";
				$t->set_var("active_style", "nonactive");
			}

			$t->set_var("layout_name", $layout_name);
			$t->set_var("layout_status", $layout_status);
			
			$t->parse("records", true);
		} while($db->next_record());
	}
	else
	{
		$t->set_var("records", "");
		$t->set_var("navigator", "");
		$t->parse("no_records", false);
	}

	// multisites
	if ($sitelist) {
		$sites   = get_db_values("SELECT site_id,site_name FROM " . $table_prefix . "sites ORDER BY site_id ", "");
		set_options($sites, $param_site_id, "param_site_id");
		$t->parse("sitelist", false);
	}	
	
	$t->pparse("main");

?>