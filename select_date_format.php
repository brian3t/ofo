<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  select_date_format.php                                   ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	// version information
	$version_name = "shop";
	$version_type = "enterprise";
	$version_number = "3.6";

	session_start();

	set_magic_quotes_runtime(0);
	include_once("./includes/constants.php");
	include_once("./includes/common_functions.php");
	include_once("./includes/va_functions.php");
	$language_code = get_language("messages.php");
	include_once("./messages/".$language_code."/messages.php");
	include_once("./messages/".$language_code."/install_messages.php");
	include_once("./includes/date_functions.php");
	include_once("./includes/record.php");
	include_once("./includes/template.php");

	$t = new VA_Template("./templates/user/");
	$t->set_file("main", "select_date_format.html");
	$t->set_var("css_file", "styles/default.css");
	$t->set_var("CHARSET", CHARSET);
	$t->set_var("SELECT_DATE_TITLE",SELECT_DATE_TITLE);
	$t->set_var("SELECT_MSG",       SELECT_MSG);
	$t->set_var("CLOSE_WINDOW_MSG", CLOSE_WINDOW_MSG);
	$t->set_var("DATE_FORMAT_COLUMN", DATE_FORMAT_COLUMN);
	$t->set_var("CURRENT_DATE_COLUMN", CURRENT_DATE_COLUMN);
	$t->set_var("NO_DATE_FORMATS_MSG", NO_DATE_FORMATS_MSG);

	$format_type = get_param("format_type");
	$operation = get_param("operation");

	$t->set_var("format_type", $format_type);


	if ($format_type == "datetime_show") {
		$default_formats = array (
			"M/D/YY h:mm AM",        
			"M.D.YY h:mm AM",        
			"D MMM YYYY, h:mm AM",   
			"D MMMM YYYY, h:mm AM",   
			"MMM D, YYYY, h:mm AM", 
			"MMMM D, YYYY, h:mm AM",
			"YYYY, D MMMM, h:mm AM", 
			"DD/MM/YYYY HH:mm",      
			"DD.MM.YY H:mm",         
			"DD-MM-YY H:mm",         
			"YYYY-MM-DD HH:mm"
		);
	} elseif ($format_type == "date_show") {
		$default_formats = array (
			"M/D/YY",       
			"M.D.YY",       
			"D MMM YYYY",   
			"D MMMM YYYY",  
			"MMM D, YYYY", 
			"MMMM D, YYYY",
			"YYYY, D MMMM", 
			"DD/MM/YYYY",   
			"DD.MM.YYYY",   
			"DD-MM-YY",     
			"YYYY-MM-DD"
		);
	} elseif ($format_type == "datetime_edit") {
		$default_formats = array (
			"M/D/YY H:mm",         
			"M.D.YY H:mm",         
			"D/M/YY H:mm",         
			"D.M.YY H:mm",         
			"DD/MM/YYYY HH:mm",    
			"DD.MM.YYYY HH:mm",       
			"YYYY-MM-DD HH:mm:ss"
		);
	} elseif ($format_type == "date_edit") {
		$default_formats = array (
			"M/D/YY",     
			"M.D.YY",     
			"D/M/YY",     
			"D.M.YY",     
			"DD/MM/YYYY", 
			"DD.MM.YYYY", 
			"YYYY-MM-DD" 
		);
	} 

	$formats_number = isset($default_formats) ? sizeof($default_formats) : 0;
	if ($formats_number)
	{
		for ($i = 0; $i < sizeof($default_formats); $i++)
		{
			$date_format = parse_date_format($default_formats[$i]);
			$t->set_var("date_format", htmlspecialchars($default_formats[$i]));
			$t->set_var("current_date", va_date($date_format));
			$t->parse("format_row", true);
		}

		$t->set_var("no_formats", "");
		$t->parse("formats", false);

	}
	else
	{
		$t->parse("no_formats", false);
		$t->set_var("formats", "");
	}


	$t->pparse("main");

?>