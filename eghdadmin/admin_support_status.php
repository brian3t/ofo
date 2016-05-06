<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_support_status.php                                 ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path . "includes/common.php");
	include_once($root_folder_path . "includes/record.php");                              
	include_once($root_folder_path."messages/".$language_code."/support_messages.php");
	include_once("./admin_common.php");
	check_admin_security("support_static_data");                                     
                                                                                   
	$t = new VA_Template($settings["admin_templates_dir"]);                          
	$t->set_file("main","admin_support_status.html");                                                                                   
	$t->set_var("admin_href", "admin.php");                                          
	$t->set_var("admin_support_href", "admin_support.php");
	$t->set_var("admin_support_status_href", "admin_support_status.php");
	$t->set_var("admin_support_statuses_href", "admin_support_statuses.php");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", STATUS_MSG, CONFIRM_DELETE_MSG));

	$r = new VA_Record($table_prefix . "support_statuses");
	$r->return_page = "admin_support_statuses.php";        
                                                         
	$r->add_where("status_id", INTEGER);                   
	$r->add_textbox("status_name", TEXT, STATUS_NAME_MSG);   
	$r->parameters["status_name"][REQUIRED] = true;        
	$r->add_textbox("status_caption", TEXT, STATUS_CAPTION_MSG);
	$r->parameters["status_caption"][REQUIRED] = true;        
	$r->add_checkbox("is_user_new", INTEGER);                 
	$r->add_checkbox("is_user_reply", INTEGER);               
	$r->add_checkbox("is_closed", INTEGER);                   
	$r->add_checkbox("is_operation", INTEGER);                
	$r->add_checkbox("is_reassign", INTEGER);                 
	$r->add_checkbox("is_internal", INTEGER);                 
	$r->add_checkbox("is_list", INTEGER);                     	
	$r->add_checkbox("is_add_knowledge", INTEGER);            
	$r->add_checkbox("show_for_user", INTEGER);               
	$r->add_textbox("html_start", TEXT);                      
	$r->add_textbox("html_end", TEXT);                        
	$r->add_textbox("status_icon", TEXT);                     

	$r->set_event(BEFORE_VALIDATE, "check_status_closed");    
	$r->process();
                                                            
	include_once("./admin_header.php");                            
	include_once("./admin_footer.php");                            

	$t->pparse("main");                                       

	function check_status_closed()                            
	{                                                         
		global $db, $r, $table_prefix;                          

		if ($r->get_value("is_closed")) {                       
			$sql = "SELECT status_id, status_name FROM " . $table_prefix . "support_statuses WHERE is_closed = 1";
			$db->query($sql);                                                                                     
			if ($db->next_record()) {                                                                             
				if ($r->get_value("status_id") != $db->f("status_id")) {                                            
					$r->errors = "<b>Set this status when manager close the ticket</b> is already set in status <b>'" . $db->f("status_name") . "'</b>.<br>\n";
				}
			}  
		}    
	}      

?>
