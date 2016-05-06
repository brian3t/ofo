<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_messages.php                                       ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/sorter.php");
	include_once ($root_folder_path . "includes/navigator.php");
	include_once ($root_folder_path . "includes/record.php");

	include_once("./admin_common.php");

	check_admin_security("static_messages");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_messages.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_messages_href", "admin_messages.php");
	$t->set_var("admin_message_href", "admin_message.php");

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	// see ../includes/var_definition.php for changing global default language
	$default_language_code = $default_language; 
	
	$dir = $root_folder_path . "messages/"; 

	$section = get_param("section");
	$any_chars = array (".", "..", "/", "\\");
	$section = str_replace($any_chars, "", $section);

	$language_code = get_param("language_code");
	$message_search = get_param ("message_search");

	// getting languages list

	$sql = " SELECT language_code, language_name, language_image FROM " . $table_prefix . "languages WHERE show_for_user=1 ";
	$db->query($sql);
	while ($db->next_record()) {
		$row_language_code = $db->f("language_code");
		$row_language_name = $db->f("language_name");
		$language_image = $db->f("language_image");
		$t->set_var("language_code", $row_language_code);
		$t->set_var("language_name", $row_language_name);	

		if ($language_image) {
			if ($section){
				$language_href = "?section=" . $section ."&";
			} else {
				$language_href = "?";
			}
			if (file_exists($root_folder_path . $language_image)) {
    		$image_size = preg_match("/^http\:\/\//", $language_image) ? "" : @GetImageSize($root_folder_path.$language_image);
        $src = $root_folder_path . htmlspecialchars($language_image);
				if (is_array($image_size)) {
          $image_width = "width=\"" . $image_size[0] . "\"";
		      $image_height = "height=\"" . $image_size[1] . "\"";
				} else {
          $image_width = "";
		      $image_height = "";
				}
	      $t->set_var("language_image", "<img border='0' src='$src' $image_width $image_height alt='$row_language_name' title='$row_language_name'>");
			}	else {
				$t->set_var("language_image", "<b>$row_language_code</b>" );
			}	      
			$language_href .= "language_code=" . $row_language_code; 
			$t->set_var("language_href", $language_href);
		 
			$t->parse("languages_images", true);
		}
	}

	// getting current language 

	if (!$language_code) $language_code = $default_language_code;
	$sql = " SELECT language_name, language_image FROM " . $table_prefix . "languages ";
	$sql.= " WHERE language_code='$language_code'";
	$db->query($sql);
	if ($db->next_record()) {
	  $current_language_name = $db->f("language_name");
		$current_language_image = $db->f("language_image");
		$t->set_var("current_language", $current_language_name);
		$t->set_var("current_language_code", $language_code);      

		if (file_exists($root_folder_path . $current_language_image)) {
  		$image_size = preg_match("/^http\:\/\//", $current_language_image) ? "" : @GetImageSize($root_folder_path.$current_language_image);
      $src = $root_folder_path . htmlspecialchars($current_language_image);
			if (is_array($image_size)) {
        $image_width = "width=\"" . $image_size[0] . "\"";
	      $image_height = "height=\"" . $image_size[1] . "\"";
			} else {
        $image_width = "";
	      $image_height = "";
			}
      $t->set_var("current_language_image", "<img border='0' src='$src' $image_width $image_height alt='$current_language_image'>");
		}	else {
			$t->set_var("current_language_image", "<b>$row_language_code</b>" );
		}

	} 

	// getting files list

	$message_dir = $dir . $language_code;
	if (is_dir($message_dir)) {
		if ($handle = opendir($message_dir)) {
		  $message_count = 0; $row_count = 0;
      while (false !== ($file = readdir($handle))) { 

        if ($file != "." && $file != ".." && $file != "CVS") { 
    
	// section search

					if ($message_search && file_exists($message_dir . "/" . $file)) {
						$file_content = file_get_contents($message_dir . "/" . $file);
						$message_regexp = prepare_regexp($message_search);
	  
						if(preg_match("/$message_regexp/i", $file_content)) {
						  $message_count++;
							$section_title = str_replace(".php", "", $file);
							if (is_writeable($message_dir . "/" . $file)) {
								$t->set_var("is_readonly", "");
							} else {
								$t->set_var("is_readonly", "&nbsp;<font style='font-size:8pt; color:red;'>".READONLY_MSG."</font>");
 							}
							$t->set_var("section_title", $section_title);
							if ($language_code){
								$section_href = "?language_code=" . $language_code . "&";
							} else {
								$section_href = "?";
							}
							$section_href .= "section=" . $section_title ;
							$t->set_var("section_href", $section_href);
							$t->parse("sections",true);
	  
							preg_match_all("/define\(\"(.*)\"/Uis", $file_content, $name_array, PREG_PATTERN_ORDER);
							preg_match_all("/define\(\".*\"\, \"(.*)\"\)\;/Uis", $file_content, $mess_array, PREG_PATTERN_ORDER);
	  
							$total_records = count($name_array[1]);                 		
							if($total_records) {	

								for ($i=0; $i<$total_records; $i++) {
									$name_string = $name_array[1][$i];
									$mess_string = $mess_array[1][$i];
									if(preg_match("/$message_regexp/i", $name_string) || preg_match("/$message_regexp/i", $mess_string)) {
										$row_count++;
										$row_style = ($row_count % 2 == 0) ? "row1" : "row2";
          					$t->set_var("row_style", $row_style);
									  $t->set_var("constant_name",$name_string);
										$t->set_var("message_string",$mess_string);
										if (is_writable($message_dir . "/" . $file)) {
											$search_href = "?language_code=$language_code&section=$section_title&constant_name=$name_string&message_search=$message_search";
											$message_edit_href = "<a href='admin_message.php$search_href'>Edit</a>";
										} else {
 											$message_edit_href = "<font style='font-size:8pt; color:red;'>Readonly</font>";
										}
										$t->set_var("message_edit_href", $message_edit_href);
 										$t->parse("message_details",true);
									}      
								}
								$t->set_var("current_message","Search Result");
								$t->set_var("add_new_block","");	
 								$t->parse("message_block",false);
							}						
						} 
	  
					} else { // no any search message
						$section_title = str_replace(".php", "", $file);
						if (!is_writeable($message_dir . "/" . $file)) {
							$t->set_var("is_readonly", "&nbsp;<font style='font-size:8pt; color:red;'>(Readonly)</font>");
						} else {
							$t->set_var("is_readonly", "");
						}
						$t->set_var("section_title", $section_title);
						if ($language_code){
							$section_href = "?language_code=" . $language_code . "&";
						} else {
							$section_href = "?";
						}
						$section_href .= "section=" . $section_title ;
						$t->set_var("section_href", $section_href);
						$t->parse("sections",true);
					}
        } 
      } 
    
      if ($message_search && $message_count == 0) $t->set_var("sections","<tr><td colspan=4>No messages have found</td></tr>");
      closedir($handle); 
      $is_message_dir = true;
 	 		$t->set_var("message_search", $message_search); 	 		
		} else {
			$errors_list = " No messages ";
		}	
	} else {
		$errors_list = " No messages  ";
	}	

	// getting section content

	if ($section && isset($is_message_dir) && !$message_search) {
		$current_file_name = $section . ".php";
		if (file_exists($message_dir . "/" . $current_file_name)) {
			if ($file_content = file_get_contents($message_dir . "/" . $current_file_name)) {
				preg_match_all("/define\(\"(.*)\"/Uis", $file_content, $name_array, PREG_PATTERN_ORDER);
				preg_match_all("/define\(\".*\"\, \"(.*)\"\)\;/Uis", $file_content, $mess_array, PREG_PATTERN_ORDER);
				$total_records = count($name_array[1]);                 		
				if($total_records) {	
					for ($i=0; $i<$total_records; $i++) {
						$name_string = $name_array[1][$i];
						$mess_string = $mess_array[1][$i];
						$row_style = ($i % 2 == 0) ? "row1" : "row2";
						$t->set_var("row_style", $row_style);
					  $t->set_var("constant_name",$name_string);
						$t->set_var("message_string",$mess_string);
						if (is_writable($message_dir . "/" . $current_file_name)) {
							$search_href = "?language_code=$language_code&section=$section&constant_name=$name_string";
							$message_edit_href = "<a href='admin_message.php$search_href'>Edit</a>";
							$add_new_href= "<a href='admin_message.php?language_code=$language_code&section=$section&add_new=1'>Add New</a>";
						} else {
							$message_edit_href = "<font style='font-size:8pt; color:red;'>Readonly</font>";
							$add_new_href = "";
						}
						$t->set_var("message_edit_href", $message_edit_href);
						$t->parse("message_details",true);
					}
				}
			}
			$t->set_var("current_message",$section);
			$t->set_var("add_new_href",$add_new_href);
			$t->parse("add_new_block",false);
 			$t->parse("message_block",false);
		} else {
			$errors_list = FILE_DOESNT_EXIST_MSG . $current_file_name ;
		}
	} 
	if (isset($errors_list) && strlen($errors_list)) {  
		$t->set_var("errors_list", $errors_list);		
		$t->parse("errors", false);
	}

	$t->pparse("main");

?>