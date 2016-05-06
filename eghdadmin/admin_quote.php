<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_quote.php                                          ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/editgrid.php");

	include_once("./admin_common.php");

	check_admin_security("products_categories");

  $t = new VA_Template($settings["admin_templates_dir"]);
  $t->set_file("main","admin_quote.html");

	$quote_id = get_param("quote_id");
	$t->set_var("CONFIRM_DELETE_JS", str_replace("{record_name}", ADMIN_QUOTE_MSG, CONFIRM_DELETE_MSG));	
	
	$r = new VA_Record($table_prefix . "quotes");
	$r->add_where("quote_id", INTEGER);
	$r->change_property("quote_id", USE_IN_INSERT, true);
	$r->add_textbox("request_description", TEXT);
	$r->add_textbox("quoted_price", NUMBER);
	$r->add_textbox("date_due", TEXT);
//	$r->add_hidden("quote_status_id", INTEGER);
	$r->change_property("request_description", USE_IN_SELECT, true);
//	$r->return_page = "admin_quote.php";
	
	$r->get_form_values();	
		
//	$r->process();
//	echo "111".$quote_id;exit;
if ($quote_id) {
	$sql  = " SELECT * ";
	$sql .= " FROM " . $table_prefix . "quotes ";
	$sql .= " WHERE quote_id = ".$db->tosql($quote_id, INTEGER);
	$db->query($sql);
		if ($db->next_record()) 
		{
		  	$user_name = $db->f("user_name");
		  	$user_email = $db->f("user_email");
		//	$request_summary = $db->f("request_summary");
			$request_description = $db->f("request_description");
			$quote_status_id = $db->f("quote_status_id");
			$date_due = $db->f("date_due");
			$quoted_price = $db->f("quoted_price");
			$date_added = $db->f("date_added");
			
			$t->set_var("quote_id",  $quote_id);		  
  		//	$t->set_var("request_description",  $request_description);
  			$t->set_var("copy_descr",  $request_description);
		   	$t->set_var("user_email", $user_email);
			$t->set_var("user_name", $user_name);
			$t->set_var("date_due", $date_due);
			$t->set_var("date_added", $date_added);
			$t->set_var("quoted_price", $quoted_price);
			
		//	$t->parse("quote");

}
	else {
	  	header("Location: admin_quotes.php");
			exit;
	}
}

	else 
	{
	  //$r->add_textbox("user_name", TEXT);
	  //$r->add_textbox("user_email", TEXT);
//	  $r->set_value("quote_status_id", "1");
		$t->set_var("user_email", "<input type=\"text\" name=\"user_email\" id=\"user_email\">");
		$t->set_var("user_name", "<input type=\"text\" name=\"user_name\" id=\"user_name\">");
	//	$t->set_var("date_due", $date_due);
		$t->set_var("date_added", date('Y-m-d'));
	}
	
	$ipv = new VA_Record($table_prefix . "quotes_features", "features");
	$ipv->add_where("feature_id", INTEGER);
	$ipv->add_hidden("quote_id", INTEGER);
	$ipv->change_property("quote_id", USE_IN_INSERT, true);
	$ipv->add_textbox("feature_description", TEXT, DESCRIPTION_MSG);
	$ipv->change_property("feature_description", REQUIRED, true);
	$ipv->add_textbox("price", TEXT);
	$ipv->add_textbox("date_due", TEXT);
	
	//$ipv->get_form_values();	
	
	$more_features = get_param("more_features");
	$number_features = get_param("number_features");

	$eg = new VA_EditGrid($ipv, "features");
	$eg->get_form_values($number_features);
	
	$operation = get_param("operation");

	$return_page = "admin_quotes.php";

	if(strlen($operation) && !$more_features)
	{
		
		if($operation == "cancel")
		{
			header("Location: " . $return_page);
			exit;
		}
		
		else if($operation == "delete" && $quote_id)
		{
			$db->query("DELETE FROM " . $table_prefix . "quotes WHERE quote_id=" . $db->tosql($quote_id, INTEGER));		
			$db->query("DELETE FROM " . $table_prefix . "quotes_features WHERE quote_id=" . $db->tosql($quote_id, INTEGER));		
			header("Location: " . $return_page);
			exit;
		}
		
		
		$is_valid = $r->validate();
		$is_valid = ($eg->validate() && $is_valid); 
		
		if($is_valid)
		{
			if(strlen($quote_id))
			{
			//	echo "update $quote_id";
				$r->update_record();
				$eg->set_values("quote_id", $quote_id);
				$eg->update_all($number_features);
				header("Location: admin_quote_view.php?quote_id=$quote_id");
				exit;
			}
			else
			{
				$user_name = get_param("user_name");
				$user_email = get_param("user_email");
				$db->query("SELECT MAX(quote_id) FROM " . $table_prefix . "quotes");
				$db->next_record();
				$quote_id = $db->f(0) + 1;
			//	echo $quote_id."<br>";
				//$r->set_value("feature_id", $feature_id);
				$r->set_value("quote_id", $quote_id);
				$r->set_value("request_description", htmlspecialchars($r->get_value("request_description")));
			//	$r->set_value("item_type_id", $item_type_id);
				$r->insert_record();
				$eg->set_values("quote_id", $quote_id);
				$eg->insert_all($number_features);
				$sql = "UPDATE va_quotes SET quote_status_id='1', user_name=".$db->tosql($user_name, TEXT).", user_email=".$db->tosql($user_email, TEXT)." WHERE quote_id=".$db->tosql($quote_id, INTEGER);
				$db->query($sql);
				header("Location: admin_quotes.php");
				exit;
			//		echo "111"; exit;
			}
		/*	}
				header("Location: admin_quote_view.php?quote_id=$quote_id");
				exit;
		*/}
	}
	else if(strlen($quote_id) && !$more_features) // run first
	{
		$r->get_db_values();
		$eg->set_value("quote_id", $quote_id);// устанавливаем для того чтобы потом при вызове ф-ции check_where() в функции update_all вернулось значени true. В противном случае у нас будем вместо апдейта делаться инсерт
		$eg->change_property("feature_id", USE_IN_SELECT, true);
		$eg->change_property("feature_id", USE_IN_WHERE, false);
		$eg->change_property("quote_id", USE_IN_WHERE, true);
		$eg->change_property("quote_id", USE_IN_SELECT, true);
		$number_features = $eg->get_db_values();
	//	echo "111"; exit;
		if($number_features == 0)
			$number_features = 5;
	}
	else if($more_features)
	{
		$number_features += 5;
	}
		
	else // set default values
	{
		$number_features = 5;
		
	}
	$t->set_var("number_features", $number_features);

	$eg->set_parameters_all($number_features);
	$r->set_parameters();

/*	if ($item_type_id > 0) {
		$t->parse("type_path");
	} else {
		$t->parse("product_path");
	}

*/
	if(strlen($quote_id))	
	{
		$t->parse("buttons");
		$t->set_var("add_button", "");
	}
	else
	{
	  $t->set_var("buttons", "");
	  $t->set_var("add_button", "<input class=\"submit\" type=\"submit\" value=\"".ADD_BUTTON."\" onCLick=\"document.record.operation.value='save';\">");
	}

	include_once("./admin_header.php");
	include_once("./admin_footer.php");

	$t->pparse("main");
/*else {
  echo NO_SUCH_QUOTE_MSG;
}*/
?>