<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  admin_quotes.php                                         ***
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
	
	$t = new VA_Template($settings["admin_templates_dir"]);
	$t->set_file("main","admin_quotes.html");
	$t->set_var("admin_quotes_href", "admin_quotes.php");
	
	$close_quote = get_param("close_quote");
	$show_all = get_param("show_all");
	if ($show_all == 1)
	{
	  $show = "";
	}
	else 
	{
	  $show = "WHERE is_closed=0";
	}
	if ($close_quote == 1)
	{
	  	$quote_id=get_param("quote_id");
		$sql_close = "UPDATE va_quotes SET is_closed=1 WHERE quote_id=".$db->tosql($quote_id, INTEGER);
		$db->query($sql_close);	 
		header ("Location: admin_quotes.php");
		exit; 
	  
	}	
	
	$r = new VA_Record($table_prefix . "quotes");
	$r->add_textbox("s_kw", TEXT);
	$r->change_property("s_kw", TRIM, true);
	$r->get_form_parameters();
	$r->validate();
	$r->set_form_parameters();
	
	$where = "";
	if(!$r->errors) {
		if(!$r->is_empty("s_kw")) {
			$where = "( request_summary LIKE '%" . $db->tosql($r->get_value("s_kw"), TEXT, false) . "%'";
			$where .= " OR request_description LIKE '%" . $db->tosql($r->get_value("s_kw"), TEXT, false) . "%'";
			$where .= " OR user_name LIKE '%" . $db->tosql($r->get_value("s_kw"), TEXT, false) . "%'";
			$where .= " OR user_email LIKE '%" . $db->tosql($r->get_value("s_kw"), TEXT, false) . "%');";
		}
	}
	
	$where_sql = ""; 
	if (strlen($where)) {
		$where_sql = " WHERE " . $where;
	}
	
	$s = new VA_Sorter($settings["admin_templates_dir"], "sorter_img.html", "admin_quotes.php");
	$s->set_sorter(ID_MSG, "sorter_quote_id", "1", "quote_id");
	$s->set_sorter(SUPPORT_SUMMARY_COLUMN, "sorter_request_summary", "2", "request_summary");
	$s->set_sorter(STATUS_MSG, "sorter_quote_status_id", "3", "quote_status_id");
	$s->set_sorter(EMAIL_FIELD, "sorter_user_email", "4", "user_email");
	$s->set_sorter(PRICE_MSG, "sorter_quoted_price", "5", "quoted_price");
	$s->set_sorter(DATE_DUE_MSG, "sorter_date_due", "6", "date_due");
	
	$admin_id = get_session("session_admin_id");
	$bookmarks = array();
	$sql  = " SELECT * ";
	$sql .= " FROM " . $table_prefix . "quotes ";
	$sql .= $s->order_by;
//	$sql .= "WHERE is_closed!='NULL'";
		if ($where_sql)
			$sql .= $where_sql;
			else 
			{	
				 
				//	$sql .= "WHERE is_closed=0";
					$sql .= $show;
			}
	$db->query($sql);
		while ($db->next_record()) 
		{
			$quote_id = $db->f("quote_id");
			$quotes_values = array("quote_id" => $quote_id, "request_summary" => $db->f("request_summary"), "quote_status_id" => $db->f("quote_status_id"), "user_email" => $db->f("user_email"), "date_due" => $db->f("date_due"), "quoted_price" => $db->f("quoted_price"), "is_closed" => $db->f("is_closed"));
			$quotes[$quote_id][] = $quotes_values;
		}
		
	if(!empty($quotes))
	{
		foreach ($quotes as $quote_id => $quote) 
		{
			for ($m = 0; $m < sizeof($quote); $m++) 
			{
				$quote_id = $quote[$m]["quote_id"];
				$request_summary = $quote[$m]["request_summary"];
				$quote_status_id = $quote[$m]["quote_status_id"];
				$user_email = $quote[$m]["user_email"];
				$quoted_price = $quote[$m]["quoted_price"];
				$date_due = $quote[$m]["date_due"];
				$is_closed = $quote[$m]["is_closed"];
				
				$t->set_var("quote_id",  $quote_id);		  
  				$t->set_var("request_summary",  $request_summary);
//echo "-".$quote_status_id."-<br`>";
  				$t->set_var("quote_status_id", "");
					switch ($quote_status_id)
  				{
				    case 1: 
				    {
						$t->set_var("quote_status_id", NEW_MSG); 
						$t->set_var("quote_color", "red");
						break;
					}
					case 2:
					{
						$t->set_var("quote_status_id", QUOTED_MSG); 
						$t->set_var("quote_color", "blue");
						break;
					}
					case 3:
					{
						$t->set_var("quote_status_id", PAID_MSG); 
						$t->set_var("quote_color", "teal");
						break;
					}
					case 4:
					{
						$t->set_var("quote_status_id", INPROGRESS_MSG); 
						$t->set_var("quote_color", "purple");
						break;  	
					}
				}
	//				echo "-".$quote_status_id."-";
		   	 	$t->set_var("user_email", $user_email);
		   	 	$t->set_var("quoted_price", currency_format($quoted_price)); 
		   	 	if ($quote_status_id == 1) 
					{
					  //	echo "Price - ".$quoted_price; 
					  //	$string_price = "<a href='admin_quote.php?quote_id=".$quote_id."'>".ADMIN_QUOTE_MSG."</a>";
					  	
						$action_quote = "<a href='admin_quote.php?quote_id=".$quote_id."'>".ADMIN_QUOTE_MSG."</a>";	
						$t->set_var("action_quote", $action_quote);
					}
					else 
					{
						$action_quote = "<a href='admin_quote_view.php?quote_id=".$quote_id."'>".VIEW_MSG."</a>";
						$t->set_var("action_quote", $action_quote);
					}
					
				if ($is_closed==1)
				{
				  $t->parse("closed_quote", false);
				  $t->set_var("close_quote", "");
				}
				else 
				{
				 	$t->set_var("closed_quote", "");
				  	$t->parse("close_quote", false); 
				}
					
   	 		$t->set_var("date_due", $date_due);
				$t->parse("quotes", true);

			}
		}
//	$t->set_var("errors", "");
	$t->set_var("no_record_block", "");
	$t->set_var("no_record","");
	}
	
	elseif (!$r->is_empty("s_kw")){
	  
	  	$t->set_var("no_record",NO_RECORDS);
		  $t->parse("no_record_block");
			//	$t->set_var("error", "There are no records.");
			//	$t->parse("errors");
		}
		else
		{
			$t->set_var("no_record",NO_RECORDS);
		  $t->parse("no_record_block");
		}
	
	include_once("./admin_header.php");
	include_once("./admin_footer.php");
	$t->pparse("main");
?>