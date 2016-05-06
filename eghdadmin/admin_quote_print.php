<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_quote_print.php                                    ***
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

	$currency = get_currency();
	$flag_mail = 0;
	$dbi = new VA_SQL();
	$dbi->DBType      = $db_type;
	$dbi->DBDatabase  = $db_name;
	$dbi->DBUser      = $db_user;
	$dbi->DBPassword  = $db_password;
	$dbi->DBHost      = $db_host;
	$dbi->DBPort      = $db_port;
	$dbi->DBPersistent= $db_persistent;

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
	$t->set_file("main","admin_quote_print.html");

	$t->set_var("admin_href", "admin.php");
	$t->set_var("admin_orders_href", "admin_orders.php");
	$t->set_var("admin_order_href", $order_details_site_url . "admin_order.php");


	$quote_id = get_param("quote_id");
	$t->set_var("quote_id", $quote_id);
	$r = new VA_Record($table_prefix . "quotes");
	$r->add_hidden("flag_mail", INTEGER);
	$r->add_hidden("quote_id", INTEGER);
	$r->add_where("quote_id", INTEGER);
	$r->get_form_values();
	$flag_mail = $r->get_value("flag_mail");
//	var_dump($_POST);
	$r->set_value("quote_id", $quote_id);
//	echo "FLAG".$flag_mail;

	
	$sql  = " SELECT * FROM " . $table_prefix . "quotes ";
	$sql .= " WHERE quote_id=" . $db->tosql($quote_id, INTEGER);
	$db->query($sql);
	if ($db->next_record()) {
	 	do {
			$user_name = $db->f("user_name");
			$user_email = $db->f("user_email");
			$quoted_price = $db->f("quoted_price");
			$date_added = $db->f("date_added");
			
			
		} while ($db->next_record());
	}
	
	$t->set_var("user_name", $user_name);
	$t->set_var("user_email", $user_email);
	$t->set_var("quoted_price", $quoted_price);
	$t->set_var("date_added", $date_added);
	

	
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
			while ($db->next_record()) 
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

		$t->parse("packing", true);

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
	// $eol="\r\n";
	// $mime_boundary=md5(time());
	if ($flag_mail == 1)
	{
	  	$mail_to = $user_email;
	  	$mail_subject = ADMIN_QUOTE_MSG;
			$email_headers = array();
			$email_headers["from"] = get_setting_value($settings, "admin_email", "support@viart.com");
			$email_headers["mail_type"] = 1;
	  	$mail_message = $t->get_var("main");
		 /*	$filename = "C:/www/viart_shop_2.8/images/silver/logo.jpg";
		 
			$file_name = substr($filename, (strrpos($filename, "/")+1));
		       	$handle=fopen($filename, 'rb');
	       		$f_contents=fread($handle, filesize($filename));
	       		$f_contents=chunk_split(base64_encode($f_contents));    //Encode The Data For Transition using base64_encode();
	       		fclose($handle);
	       		
	       $mail_message = "--".$mime_boundary.$eol;
	       $mail_message .= "Content-Type: image"."; name=\"".$file_name."\"".$eol;
	      	$mail_message .= "Content-Transfer-Encoding: base64".$eol;
	       	$mail_message .= "Content-Disposition: attachment; filename=\"".$file_name."\"".$eol.$eol; // !! This line needs TWO end of lines
	      	$mail_message .= $f_contents.$eol.$eol;
	      	$mail_message .= $t->get_var("main");*/
		if (va_mail($mail_to, $mail_subject, $mail_message, $email_headers))
		{
		  echo "<center>" . QUOTE_WAS_SUCESSFULLY_SENT_MSG . "</center>";
		}
	}
?>