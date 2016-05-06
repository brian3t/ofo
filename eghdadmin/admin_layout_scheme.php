<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_layout_scheme.php                                  ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");
	include_once($root_folder_path . "includes/sorter.php");
	include_once($root_folder_path . "includes/navigator.php");

	include_once("./admin_common.php");

	check_admin_security("layouts");

	$eol = get_eol();
  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_layout_scheme.html");

	$t->set_var("admin_layout_scheme_href", "admin_layout_scheme.php");
	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_layouts_href", "admin_layouts.php");
	$t->set_var("admin_layout_href", "admin_layout.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$operation = get_param("operation");
	$layout_id = get_param("layout_id");
	if(!$layout_id) {
		$layout_id = $settings["layout_id"];
	}


	$return_page = get_param("rp");
	if(!strlen($return_page)) $return_page = "admin_layouts.php";
	$errors = "";

	$sql = "SELECT layout_name,style_name FROM " . $table_prefix . "layouts WHERE layout_id=" . $db->tosql($layout_id, INTEGER);
	$db->query($sql);
	if($db->next_record()) {
		$layout_name = $db->f("layout_name");
		$style_name = $db->f("style_name");
		$t->set_var("layout_name", htmlspecialchars($layout_name));
	} else {
		header("Location: " . $return_page);
		exit;
	}


	$style_lc = strtolower($style_name);
	$layout_schemes[] = array("", "--- ".SELECT_FROM_LIST_MSG." ---");
	if ($dir = @opendir($root_folder_path . "styles")) 
	{
	  while($file = readdir($dir)) {
			if ( preg_match("/^" . $style_lc . "_(.)+\.css$/", $file) ) { 
				$scheme_prefix = strlen($style_lc) + 1;
				$scheme_size = strlen($file) - $scheme_prefix - 4;
				$scheme_name = substr($file, $scheme_prefix, $scheme_size);
				$layout_schemes[] = array($scheme_name, ucwords($scheme_name));
	    } 
	  }  
  	closedir($dir);
	}

	if (!strlen(get_param("layout_id"))){
		$_GET["layout_id"] = $layout_id;
	}
	
	$r = new VA_Record($table_prefix . "layouts");
	$r->add_where("layout_id", INTEGER);
	$r->add_select("scheme_name", TEXT, $layout_schemes, LAYOUT_SCHEME_MSG);
	$r->change_property("scheme_name", REQUIRED, true);
	if(sizeof($layout_schemes) < 2) {
		$r->errors = ONLY_ONE_SCHEME_IN_USE_MSG;
	}

	$r->get_form_parameters();

	if(strlen($operation))
	{
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}

		$is_valid = $r->validate();

		if($is_valid)
		{

			$r->update_record();
			/*
			$layout_scheme_cur = $root_folder_path . "styles/" . $style_lc . ".css";
			$layout_scheme_new = $root_folder_path . "styles/" . $style_lc . "_" . $layout_scheme . ".css";
			
			// copy css file  
			if ( file_exists($layout_scheme_cur) ) {
				if ( !is_writable ($layout_scheme_cur) ) {
					$r->errors .= str_replace("{file_name}", $layout_scheme_cur, FILE_PERMISSION_MESSAGE) . "<br>" . $eol;
				} else {
					copy($layout_scheme_new, $layout_scheme_cur);
				}
			} else if ( !is_writable ($root_folder_path . "styles/") ) {
				$r->errors .= str_replace("{folder_name}", $root_folder_path . "styles/", FOLDER_PERMISSION_MESSAGE) . "<br>" . $eol;
			} else {
				copy($layout_scheme_new, $layout_scheme_cur);
			}

			// copy images files
			$images_folder_cur = $root_folder_path . "images/" . $style_lc . "/";
			$images_folder_new = $root_folder_path . "images/" . $style_lc . "/" . $layout_scheme . "/";
			if(is_dir($images_folder_new)) {
				if ($images_dir = @opendir($images_folder_new)) 
				{
				  while($file = readdir($images_dir)) {
						if ($file != "." && $file != ".." && is_file($images_folder_new . $file)) 
						{
							// copy image file
							if ( file_exists($images_folder_cur . $file) ) {
								if ( !is_writable ($images_folder_cur . $file) ) {
									$r->errors .= str_replace("{file_name}", $images_folder_cur . $file, FILE_PERMISSION_MESSAGE) . "<br>" . $eol;
								} else {
									copy($images_folder_new . $file, $images_folder_cur . $file);
								}
							} else if ( !is_writable ($images_folder_cur) ) {
								$r->errors .= str_replace("{folder_name}", $images_folder_cur, FOLDER_PERMISSION_MESSAGE) . "<br>" . $eol;
							} else {
								copy($images_folder_new . $file, $images_folder_cur . $file);
							}

				    } 
				  }  
        	closedir($images_dir);
				}
			}//*/

			if( ! $r->errors ) {
				header("Location: " . $return_page);
				exit;
			}
		}
	}
	else
	{
		$r->get_db_values();
	}

	$r->set_form_parameters();
	$t->set_var("layout_id", $layout_id);
	$t->set_var("rp", htmlspecialchars($return_page));

	$t->pparse("main");

?>