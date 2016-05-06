<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_message.php                                        ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/parameters.php");

	include_once("./admin_common.php");

	check_admin_security("static_messages");

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_message.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_messages_href", "admin_messages.php");
	$t->set_var("admin_message_href", "admin_message.php");

	$operation 			= get_param("operation");
	$section 				= get_param("section");
	$language_code 	= get_param("language_code");
	$constant_name 	= get_param("constant_name");
	$constant_text 	= get_param("constant_text");
	$message_search	= get_param("message_search");
	$add_new				= get_param("add_new");
	$message_text 	= get_param("message_text"); 
//	$message_text 	= str_replace ("\"","&quot;", get_param("message_text")); 

	if (strlen($message_text)) {
		// 092=backslash ascii ; 034=quotes ascii
		$slash_quote = chr("092").chr("034");
		$double_slash = chr("092").chr("092");

		for ($i = 0; $i < strlen($message_text); $i++) {
			if 	(($message_text[$i] == chr("034")) && (($i==0) || ($message_text[$i-1]!= chr("092")))) { //nonslashed quotes
				$message_text = substr_replace ($message_text, $slash_quote, $i, 1);	 //insert a slash before a quotes
				$i++ ;
			}			
			else if (($message_text[$i] == chr("034")) && ($message_text[$i-1] == chr("092"))) {  //slashed quotes
				$count = 1;
				$j = $i-1;
				while (($j>0) && ($message_text[$j-1] == chr("092"))){				
					$count++;
					$j--;
				}
				if (!($count % 2)) { //even
 		  		$message_text = substr_replace ($message_text, $slash_quote, $i, 1);
 		  		$i++ ; 
				} 
			}
			else if (($i == strlen($message_text)-1) && ($message_text[$i] == chr("092"))) {
				$count = 1;
				$j = $i;
				while (($j>0) && ($message_text[$j-1] == chr("092"))){				
					$count++;
					$j--;
				}
				if (($count % 2)) { //odd
 		  		$message_text = substr_replace ($message_text, $double_slash, $i, 1);
 		  		$i++ ; 
				} 
			}
    }
	}

	$constant_text 	= preg_replace ("/([^a-zA-Z1234567890_]+)/i", "", $constant_text);

	$message_dir = $root_folder_path . "messages/" . $language_code;
	$return_page = "admin_messages.php?language_code=$language_code&section=$section&message_search=$message_search";	

	if (is_dir($message_dir)) {
    $is_message_dir = true;
    if ($section && $constant_name) {
			$current_file_name = $section . ".php";
			if (file_exists($message_dir . "/" . $current_file_name)) {
				if ($file_content = file_get_contents($message_dir . "/" . $current_file_name)) {
				  $constant_name = prepare_regexp($constant_name);
					if (preg_match("/\"$constant_name\"\, \"(.*)\"\)\;/Uis", $file_content, $mess_array))	{
						$message = $mess_array[1];                 		
					}
				} else $errors_list = CANT_GET_CONTENT_MSG . $current_file_name;
			} else $errors_list = FILE_DOESNT_EXIST_MSG . $current_file_name;
		}
	} else $errors_list = FOLDER_DOESNT_EXIST_MSG . $message_dir;

	if($operation == "cancel") {
		header("Location: " . $return_page);
		exit;
	}       

	if(strlen($operation) && strlen($constant_text) && strlen($message_text) && !isset($errors_list)) {
	  $constant_text = prepare_regexp($constant_text);
		if ($section && isset($is_message_dir)) {
    	$current_file_name = $section . ".php";
			if (file_exists($message_dir . "/" . $current_file_name) && is_writable($message_dir . "/" . $current_file_name)) {
				if ($file_content = file_get_contents($message_dir . "/" . $current_file_name)) {
				  if (strlen($add_new)) {
					  if (preg_match("/$constant_text\"\, \"(.*)\"\)\;/Uis", $file_content, $mess_array)){
   		  			$file_content = preg_replace("/$constant_text\"\, \"(\w+)([^\"]*\")/Uis", "$constant_text\", \"$message_text\"", $file_content);
   		  		} else {                                              
   		  		  $new_message = "\tdefine(\"$constant_text\", \"$message_text\");";
//						$file_content = preg_replace("/\<\?php\r\n\r\n/Uis", "<?php\r\n\r\n$new_message\r\n", $file_content);
							$file_content = preg_replace("/\r\n\?\>/Uis", $new_message."\r\n\r\n?>", $file_content);
						}
					} else {
//					$message = prepare_regexp ($message);
						$file_content = str_replace ($constant_name."\", \"". $message , $constant_text."\", \"". $message_text, $file_content);
//   	 			$file_content = preg_replace("/$constant_name\"\, \"$message/Uis", "$constant_text\", \"$message_text", $file_content);
					}				
		  
	  	  if (!$handle = fopen($message_dir . "/" . $current_file_name, 'w')) {
  	      $errors_list = CANNOT_OPEN_FILE_MSG . $current_file_name;
					header("Location: " . $return_page);
				  exit;
	  	  }
  	    if (fwrite($handle, $file_content) === FALSE) {
          $errors_list = CANNOT_WRITE_FILE_MSG . $current_file_name;
					header("Location: " . $return_page);
				  exit;
      	}                                                    
      	fclose($handle);
						
				header("Location: " . $return_page);
				exit;
			  
				}
			} else {
				$errors_list = CANT_EDIT_FILE_MSG . $current_file_name . CHECK_PERMIS_MSG;
			}
		} else  {
			$errors_list = NOT_ENOUGH_PARAMETERS_MSG;
		}

	}         

	if (isset($errors_list)) {
		$t->set_var("errors_list", $errors_list);
	  $t->parse("errors", false);	
	}

	$t->set_var("constant_name", $constant_name);
	$t->set_var("constant_text", $constant_name);
	$t->set_var("language_code", $language_code);
	$t->set_var("add_new", $add_new);
	$t->set_var("message_search", $message_search);
	$t->set_var("section", $section);

	if (isset($message)) 	$t->set_var("message_text", $message);
	else $t->set_var("message_text", htmlspecialchars($message_text));

	$t->set_var("return_page", htmlspecialchars($return_page));

	if ($add_new || $constant_name) {
  	$t->parse("block_edit_message", false); 
  } else {
		$t->set_var("block_edit_message", "<div align='center' class='error'>" . SELECT_SECTION_FILE_MSG . "</div>");  
  }

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");	

?>