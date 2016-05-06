<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  constants.php                                            ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	// common regular expressions
	define("EMAIL_REGEXP", "/^[_a-z0-9-\\'\\+]+(\\.[_a-z0-9-\\'\\+]+)*@[a-z0-9-]+(\\.[a-z0-9-]+)*(\\.[a-z]{2,4})\$/i");
	define("ALPHANUMERIC_REGEXP", "/^[_a-z0-9\\-\\.]+\$/i");
	define("NICKNAME_REGEXP", "/^[_a-z0-9\\-\\.\s]+\$/i");
	define("FRIENDLY_URL_REGEXP", "/^[\\/_a-z0-9\\-\\.\s]+\$/i");
	define("PHONE_REGEXP", "/^[0-9\\+\\-\\.\s\\(\\)]+\$/i");
	define("MAX_INTEGER", 2147483647);

	// Sources types
	define ("CONSTANT",    1);
	define ("GET",         2);
	define ("POST",        3);
	define ("REQUEST",     4);
	define ("SESSION",     5);
	define ("COOKIE",      6);
	define ("APPLICATION", 7);
	define ("DB",          8);
	
	// Data types
	define("NUMBER",        1);
	define("TEXT",          2);
	define("DATETIME",      3);
	define("FLOAT",         4);
	define("INTEGER",       5);
	define("DATE",          6);
	define("TIME",          7);
	define("TIMESTAMP",     8);
	define("NUMBERS_LIST",  9);
	define("FLOATS_LIST",  10);
	define("INTEGERS_LIST",11);

	// Control types
	define("HIDDEN",       1);
	define("TEXTBOX",      2);
	define("TEXTAREA",     3);
	define("CHECKBOX",     4);
	define("LISTBOX",      5);
	define("RADIOBUTTON",  6);
	define("CHECKBOXLIST", 7);
	define("TEXTBOXLIST",  8);
	define("SUBMITBUTTON", 9);
	define("BUTTON",       10);

	// Date indexes
	define("YEAR",        0);
	define("MONTH",       1);
	define("DAY",         2);
	define("HOUR",        3);
	define("MINUTE",      4);
	define("SECOND",      5);
	define("SHORTYEAR",   6);
	define("FULLMONTH",   7);
	define("SHORTMONTH",  8);
	define("AMPMHOUR",    9);
	define("AMPM",       10);
	define("GMT",        11);

	// Parameter indexes
	define("CONTROL_NAME",    0);
	define("CONTROL_DESC",    1);
	define("CONTROL_TYPE",    2);
	define("CONTROL_VALUE",   3);
	define("VALUE_TYPE",      4);
	define("VALUE_MASK",      5);
	define("VALUES_LIST",     6);
	define("PARSE_NAME",      7);
	define("COLUMN_NAME",     8);
	define("HTML",            9);
	define("SHOW",           10);
	define("USE_IN_SELECT",  11);
	define("USE_IN_INSERT",  12);
	define("USE_IN_UPDATE",  13);
	define("USE_IN_WHERE",   14);
	define("USE_SQL_NULL",   15);
	define("SQL_DELIMITERS", 16);
	define("TRANSFER",       17);
	define("RELATED_TABLE",  18);
	define("RELATED_WHERE",  19);

	define("DEFAULT_VALUE",  20);
	define("REQUIRED",       21);
	define("REGEXP_MASK",    22);
	define("REGEXP_ERROR",   23);
	define("UNIQUE",         24);
	define("MIN_VALUE",      25);
	define("MAX_VALUE",      26);
	define("MIN_LENGTH",     27);
	define("MAX_LENGTH",     28);
	define("MATCHED",        29);
	                        
	define("SELECT_SQL",     30);
	define("INSERT_SQL",     31);
	define("UPDATE_SQL",     32);
	define("DELETE_SQL",     33);

	define("TRIM",           34);
	define("LTRIM",          35);
	define("RTRIM",          36);
	define("UCASE",          37);
	define("LCASE",          38);
	define("UCWORDS",        39);

	define("USE_IN_ORDER",   40);
	define("ORDER_ASC",      41);
	define("ORDER_DESC",     42);

	define("INSERT_ALLOWED", 43);
	define("UPDATE_ALLOWED", 44);
	define("DELETE_ALLOWED", 45);
	define("SELECT_ALLOWED", 46);

	define("IS_VALID",       47);
	define("ERROR_DESC",     48);
	define("CONTROL_ORDER",  49);

	define("INSERT_SUCCESS", 50);
	define("UPDATE_SUCCESS", 51);
	define("DELETE_SUCCESS", 52);
	
	// events list
	define("BEFORE_INSERT",    101);
	define("AFTER_INSERT",     102);
	define("BEFORE_UPDATE",    103);
	define("AFTER_UPDATE",     104);
	define("BEFORE_DELETE",    105);
	define("AFTER_DELETE",     106);
	define("BEFORE_VALIDATE",  107);
	define("AFTER_VALIDATE",   108);
	define("BEFORE_SELECT",    109);
	define("AFTER_SELECT",     110);
	define("BEFORE_DEFAULT",   111);
	define("AFTER_DEFAULT",    112);
	define("BEFORE_REQUEST",   113);
	define("AFTER_REQUEST",    114);
	define("BEFORE_SHOW",      115);
	define("AFTER_SHOW",       116);
	define("BEFORE_SHOW_VALUE",117);
	define("AFTER_SHOW_VALUE", 118);
	define("ON_CANCEL_OPERATION", 119);
	define("ON_CUSTOM_OPERATION", 120);
	define("ON_DOUBLE_SAVE",      121);
	define("BEFORE_PROCESS",      122);
	define("AFTER_PROCESS",       123);

	// permisssions for access lists
	define("VIEW_CATEGORIES_PERM",       1);
	define("VIEW_CATEGORIES_ITEMS_PERM", 2);
	define("VIEW_ITEMS_PERM",            4);
	define("ADD_ITEMS_PERM",             8);
	define("SEARCH_ITEMS_PERM",          16);
	
	define("VIEW_FORUM_PERM",        1);
	define("VIEW_TOPICS_PERM",       2);
	define("VIEW_TOPIC_PERM",        4);
	define("POST_TOPICS_PERM",       8);
	define("POST_REPLIES_PERM",     16);
	define("POST_ATTACHMENTS_PERM", 32);
?>