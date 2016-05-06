<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_quote_view.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once ("./admin_config.php");
	include_once ($root_folder_path . "includes/common.php");
	include_once ($root_folder_path . "includes/record.php");
	include_once ($root_folder_path . "includes/order_items.php");
	include_once ($root_folder_path . "includes/parameters.php");
	include_once ("../messages/".$language_code."/cart_messages.php");

	include_once("./admin_common.php");

	check_admin_security();
	$admin_id = get_session("session_admin_id");
	$sql = "SELECT email FROM va_admins WHERE admin_id=".$db->tosql($admin_id, INTEGER);
	$db->query($sql);
	if ($db->next_record())
	$admin_email = $db->f("email");
	else 
	$admin_email = "";
//	echo "mail - ".$admin_email;
//	echo $admin_id;
	$packing = array();
	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='printable'";
	if ($multisites_version) {
		$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($site_id,INTEGER). ") ";
		$sql .= "ORDER BY site_id ASC ";
	}
	$db->query($sql);
	while($db->next_record()) {
		$packing[$db->f("setting_name")] = $db->f("setting_value");
	}

	$sql = "SELECT setting_name,setting_value FROM " . $table_prefix . "global_settings WHERE setting_type='order_info'";
	if ($multisites_version) {
		$sql .= "AND ( site_id=1 OR  site_id=" . $db->tosql($site_id,INTEGER). ") ";
		$sql .= "ORDER BY site_id ASC ";
	}
	$db->query($sql);
	while($db->next_record()) {
		$order_info[$db->f("setting_name")] = $db->f("setting_value");
	}

	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_quote_view.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_order_href", $order_details_site_url . "admin_order.php");

	$quote_id = get_param("quote_id");
	$operation = get_param("operation");
	$t->set_var("quote_id", $quote_id);
	
	$r = new VA_Record($table_prefix . "quotes");
//	$r->add_hidden("flag_mail", INTEGER);
//	$r->add_checkbox("send_mail", INTEGER);
	$r->add_hidden("quote_id", INTEGER);
	$r->add_where("quote_id", INTEGER);
	$quote_statuses = get_db_values("SELECT * FROM " . $table_prefix . "quotes_statuses", "");
//	var_dump($quote_statuses);
	$r->add_select("quote_status_id", INTEGER, $quote_statuses);

//	echo "stat - ".$quote_status;
	$flag_mail = get_param("send_mail");
	//echo "flag".$flag_mail;
//	var_dump($_POST);
	$r->set_value("quote_id", $quote_id);
//	echo "FLAG".$flag_mail;
	
	
	$sql  = " SELECT * FROM " . $table_prefix . "quotes ";
	$sql .= " WHERE quote_id=" . $db->tosql($quote_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
	  
	  //$t->set_var("error", "");
	 	do {
			$user_name = $db->f("user_name");
			$user_email = $db->f("user_email");
			$quoted_price = $db->f("quoted_price");
			$date_added = $db->f("date_added");
			$status_id = $db->f("quote_status_id");
			
		} while ($db->next_record());
		$t->set_var("user_name", $user_name);
	$t->set_var("user_email", $user_email);
	$t->set_var("quoted_price", $quoted_price);
	$t->set_var("date_added", $date_added);
	
		$t->parse("info_block", false);
		$t->set_var("error2", "");
		$t->set_var("error1", "");
	}
	else {
	 $t->parse("error2");	
	 $t->set_var("error1", "");	
	$t->set_var("info_block","");
	 
	}
		
	

	
	if (isset($packing["packing_header"])) {
		$t->set_var("packing_header", nl2br($packing["packing_header"]));
	}
	if (isset($packing["packing_logo"]) && strlen($packing["packing_logo"])) {
		$image_path = $packing["packing_logo"];
		if (preg_match("/^http\:\/\//", $image_path)) {
			$image_size = "";
		} else {
      $image_size = @GetImageSize($image_path);
		}
    $t->set_var("image_path", htmlspecialchars($image_path));
		if(is_array($image_size))
		{
      $t->set_var("image_width", "width=\"" . $image_size[0] . "\"");
	    $t->set_var("image_height", "height=\"" . $image_size[1] . "\"");
		}
		else
		{
      $t->set_var("image_width", "");
	    $t->set_var("image_height", "");
		}
		$t->parse("packing_logo", false);
	}
	if (isset($packing["packing_footer"])) {
		$t->set_var("packing_footer", nl2br($packing["packing_footer"]));
	}
		$counter= 1;
		$sql  = " SELECT * FROM " . $table_prefix . "quotes_features ";
		$sql .= " WHERE quote_id=" . $db->tosql($quote_id, INTEGER);
		$db->query($sql);
		if ($db->next_record())
		{
			do 
			{
				$feature_description = $db->f("feature_description");
				$price = $db->f("price");
				$date_due = $db->f("date_due");
				
				$t->set_var("feature_description", $feature_description);
				$t->set_var("price", $price);
				$t->set_var("date_due", $date_due);
				$t->set_var("counter", $counter);

				$t->parse("items", true);
				$counter++;
		    }
		    	while ($db->next_record());
		    	$t->parse("items_table", false);
		    	$t->parse("items_footer", false);
		    	$t->parse("for_letter", false);
		    	$t->set_var("error1", "");
		}
		else 
		{
		  $t->set_var("items_table", "");
		  $t->set_var("items_footer", "");
		  $t->set_var("for_letter", "");
		  $t->parse("error1");
		}
		

	$sql = "SELECT qh.*, a.admin_name FROM va_quotes_history qh, va_admins a WHERE a.admin_id=qh.admin_id AND quote_id='$quote_id'";
	$db->query($sql);
	if ($db->next_record())
	{
	  $t->parse("quote_history_header", false);
	  do
	  {
			$t->set_var("old_status", $db->f("quote_status_id_old"));
			$t->set_var("new_status", $db->f("quote_status_id_new"));
			$t->set_var("date_added", $db->f("date_added"));
			$t->set_var("admin_name", $db->f("admin_name"));
			$t->parse("quote_history", true);
		}
		while($db->next_record());
	}
	
	if ($operation == "save")
	{
	  $r->get_form_parameters();
	  	$quote_status = $r->get_value("quote_status_id");
	//  echo " - ".$admin_id;
		$today = date('Y-m-d');
	  		$sql = "UPDATE va_quotes SET quote_status_id='".$db->tosql($quote_status, INTEGER)."' WHERE quote_id='$quote_id'";
				$db->query($sql);
				if ($status_id!=$quote_status)
		{
		  $sql2 = "INSERT INTO va_quotes_history (quote_id, quote_status_id_old, quote_status_id_new, date_added, admin_id) VALUES ('$quote_id', '$status_id', '$quote_status', '$today', '$admin_id')";
		$db->query($sql2);
		}
				if ($flag_mail == 1)
				{
	  		//	$mail_to = $user_email;
	  		$mail_subject = ADMIN_QUOTE_MSG;
	  		$mail_to = 	$user_email;
				$email_headers = array();
				$email_headers["from"] = $admin_email;
				$email_headers["mail_type"] = 1;
	  		$mail_message = $t->get_var("for_letter");
					if (va_mail($mail_to, $mail_subject, $mail_message, $headers))
					{
						// echo "<center>Quote was successfully sent!";
	 					header("Location: admin_quotes.php");
					}
					else 
					{
		 				echo SOME_PROBLEMS_EMAILS_MSG;
					}
				}	
			header("Location: admin_quotes.php");
	} else {
	  $r->set_value("quote_id", $quote_id);
  	$r->get_db_values();
	}
	$r->set_parameters();
	$t->parse("packing", true);
	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
	// $eol="\r\n";
	// $mime_boundary=md5(time());
?>