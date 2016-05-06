<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_quote_new.php                                      ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/

	include_once("./admin_config.php");
	include_once($root_folder_path."includes/common.php");
	include_once($root_folder_path . "includes/record.php");
	include_once($root_folder_path . "includes/sorter.php");
	
	include_once("./admin_common.php");

	check_admin_security();
	
	$quote_id = get_param("quote_id");
	$admin_id = get_session("session_admin_id");
	$date_added = date('Y-m-d');

	$features = array();
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_quote_new.html");
	
	$r1=  new VA_Record($table_prefix . "quotes");
	$r1->add_textbox("quoted_price", NUMBER);
	$r1->add_textbox("date_due", DATE);
	$r1->add_textbox("request_description", TEXT);	
	$r1->change_property("request_description", REQUIRED, true);
	$r1->add_hidden("changed_descr", TEXT);
	$r1->add_textbox("user_name", TEXT);
	$r1->change_property("user_name", REQUIRED, true);
	$r1->add_textbox("user_email", TEXT);
	$r1->change_property("user_email", REQUIRED, true);
	$r1->get_form_values();
	$user_name = $r1->get_value("user_name");
	$user_email = $r1->get_value("user_email");
	$quoted_price = $r1->get_value("quoted_price");
	$date_due = $r1->get_value("date_due");
	if (strlen($date_due)!=0)
	{
	$t1=strtotime($date_due);
	$date_due =  date ("Y-m-d", $t1);
	}
	$user_name = $r1->get_value("user_name");
	$user_email = $r1->get_value("user_email");
	$request_description = $r1->get_value("request_description");
	
	if(strlen($request_description))
	{
	$sql_quote ="INSERT into va_quotes (quote_id, user_name, user_email, date_due, date_added, quoted_price, request_description) VALUES (NULL, '$user_name', '$user_email', '$date_due', '$date_added', '$quoted_price', '$request_description')";
	$result_quote = mysql_query($sql_quote);
	header ("Location: admin_quotes.php");
	}
	
	$r = new VA_Record($table_prefix . "quotes_features");
	$r->add_textbox("feature_1", TEXT);
	$r->add_textbox("feature_2", TEXT);
	$r->add_textbox("feature_3", TEXT);
	$r->add_textbox("feature_4", TEXT);
	$r->add_textbox("price_1", NUMBER);
	$r->add_textbox("price_2", NUMBER);
	$r->add_textbox("price_3", NUMBER);
	$r->add_textbox("price_4", NUMBER);	
	$r->add_textbox("date_1", DATE);
	$r->add_textbox("date_2", DATE);
	$r->add_textbox("date_3", DATE);
	$r->add_textbox("date_4", DATE);
	$r->add_textbox("date_due", DATE);
	$r->get_form_values();
	$features[0] = $r->get_value("feature_1");
	$features[1] = $r->get_value("feature_2");
	$features[2] = $r->get_value("feature_3");
	$features[3] = $r->get_value("feature_4");

	$dates[0] = $r->get_value("date_1");
	$dates[1] = $r->get_value("date_2");
	$dates[2] = $r->get_value("date_3");
	$dates[3] = $r->get_value("date_4");
	$date_due = $r->get_value("date_due");
	
	$prices[0] = $r->get_value("price_1");
	$prices[1] = $r->get_value("price_2");
	$prices[2] = $r->get_value("price_3");
	$prices[3] = $r->get_value("price_4");
	
	$sql_id = "SELECT MAX(quote_id) from va_quotes";
	$result_id = mysql_query($sql_id);
	$vivod_id = mysql_fetch_array($result_id);
	$quote_id = $vivod_id[0];
	
		for ($i=0; $i<4; $i++)
		{
			if (strlen($features[$i]) != 0 )  
	  		{
	  		
				$t=strtotime($dates[$i]);
				$dates[$i] =  date ("Y-m-d", $t);
				$sql = "INSERT into va_quotes_features (feature_id, quote_id, feature_description, price, date_due) values (NULL, '$quote_id', '$features[$i]', '$prices[$i]', '$dates[$i]')";
				$result_sql = mysql_query($sql);
				header ("Location: admin_quotes.php");
			}
		}

	$t->parse("quote", false);
	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	$t->pparse("main");
?>