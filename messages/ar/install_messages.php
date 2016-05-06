<?php
/*
  ****************************************************************************
  ***                                                                      ***
  ***      ViArt Shop 3.6                                                  ***
  ***      File:  install_messages.php                                     ***
  ***      Built: Wed Feb 18 13:08:18 2009                                 ***
  ***      http://www.viart.com                                            ***
  ***                                                                      ***
  ****************************************************************************
*/


	// installation messages
	define("INSTALL_TITLE", "·· ”Êﬁ ViArt  ‰’Ì» »—‰«„Ã");

	define("INSTALL_STEP_1_TITLE", "«· ‰’Ì»: «·„—Õ·Â «·√Ê·Ï");
	define("INSTALL_STEP_1_DESC", "·· ”Êﬁ. ·ﬂÌ  ﬂ„· ⁄„·Ì… «· ‰’Ì», «·—Ã«¡ „·∆ «·›—«€«  «·„ÿ·Ê»Â ›Ì «·√”›·. «·—Ã«¡ „·«ÕŸ… «‰Â ÌÃ» «‰  ﬂÊ‰ Â‰«ﬂ ﬁ«⁄œ… »Ì«‰«  „ÊÃÊœÂ ··≈” „—«—.  «–« ﬂ‰   —Ìœ ViArt ‘ﬂ—« ·ﬂ ·«Œ Ì«— »—‰«„Ã .·Â ﬁ»· «·≈” „—«— DSN ÌÃ» «‰  ‰‘¡ √Ê·« MS Access :„À· ODCB  ‰’Ì» «·»—‰«„Ã ⁄·Ï ﬁ«⁄œ… »Ì«‰«   ” Œœ„");
	define("INSTALL_STEP_2_TITLE", "«· ‰’Ì»: «·„—Õ·Â «·À«‰ÌÂ");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "«· ‰’Ì»: «·„—Õ·Â «·À«·ÀÂ");
	define("INSTALL_STEP_3_DESC", "«·—Ã«¡ «Œ Ì«— «·‘ﬂ· «·⁄«„ ··„Êﬁ⁄, Ì„ﬂ‰ﬂ  €ÌÌ—Â ·«Õﬁ«");
	define("INSTALL_FINAL_TITLE", "«· ‰’Ì»: «·„—Õ·Â «·√ŒÌ—Â");
	define("SELECT_DATE_TITLE", "«Œ — ‘ﬂ· ⁄—÷ «· «—ÌŒ");

	define("DB_SETTINGS_MSG", "ŒÌ«—«  ﬁ«⁄œ… «·»Ì«‰« ");
	define("DB_PROGRESS_MSG", "Populating database structure progress");
	define("SELECT_PHP_LIB_MSG", "PHP «Œ — ‰Ê⁄ „ﬂ »… «·‹");
	define("SELECT_DB_TYPE_MSG", "«Œ — ‰Ê⁄ ﬁ«⁄œ… «·»Ì«‰« ");
	define("ADMIN_SETTINGS_MSG", "ŒÌ«—«  «· Õﬂ„");
	define("DATE_SETTINGS_MSG", "«‘ﬂ«· ⁄—÷ «· «—ÌŒ");
	define("NO_DATE_FORMATS_MSG", "·«  ÊÃœ «‘ﬂ«· ·· «—ÌŒ");
	define("INSTALL_FINISHED_MSG", "«·¬‰ Ê ›Ì Â–Â «·„—Õ·Â, ·ﬁœ ﬁ„  » ‰’Ì» «·»—‰«„Ã »‰Ã«Õ. «·—Ã«¡ «·ÀÂ«» ·ﬁ”„ «· Õﬂ„ ··≈œ«—Â ··ﬁÌ«„ »√Ì…  ⁄œÌ·«  „ÿ·Ê»Â");
	define("ACCESS_ADMIN_MSG", "··–Â«» ··ﬁ”„ «·≈œ«—Ì «÷€ÿ Â‰«");
	define("ADMIN_URL_MSG", "—«»ÿ «·≈œ«—Â");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", ".·· ”Êﬁ <b>ViArt</b> ‘ﬂ—« ·ﬂ ·«Œ Ì«— »—‰«„Ã");

	define("DB_TYPE_FIELD", "‰Ê⁄ ﬁ«⁄œ… «·»Ì«‰« ");
	define("DB_TYPE_DESC", "Please select the <b>type of database</b> that you are using. If you using SQL Server or Microsoft Access, please select ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP „ﬂ »… «·‹");
	define("DB_HOST_FIELD", "(HostName) «·„” ÷Ì›");
	define("DB_HOST_DESC", "Please enter the <b>name</b> or <b>IP address of the server</b> on which your ViArt database will run. If you are running your database on your local PC then you can probably just leave this as \"<b>localhost</b>\" and the port blank. If you using a database provided by your hosting company, please see your hosting company's documentation for the server settings.");
	define("DB_PORT_FIELD", "(Port) «·„‰›–");
	define("DB_NAME_FIELD", "«”„ ﬁ«⁄œ… «·»Ì«‰« ");
	define("DB_NAME_DESC", "If you are using a database such as MySQL or PostgreSQL then please enter the <b>name of the database</b> where you would like ViArt to create its tables. This database must exist already. If you are just installing ViArt for testing purposes on your local PC then most systems have a \"<b>test</b>\" database you can use. If not, please create a database such as \"viart\" and use that. If you are using Microsoft Access or SQL Server then the Database Name should be the <b>name of the DSN</b> that you have set up in the Data Sources (ODBC) section of your Control Panel.");
	define("DB_USER_FIELD", "«”„ «·„” Œœ„");
	define("DB_PASS_FIELD", "ﬂ·„… «·„—Ê—");
	define("DB_USER_PASS_DESC", "<b>Username</b> and <b>Password</b> - please enter the username and password you want to use to access the database. If you are using a local test installation the username is probably \"<b>root</b>\" and the password is probably blank. This is fine for testing, but please note that this is not secure on production servers.");
	define("DB_PERSISTENT_FIELD", "«·≈ ’«· «·œ«∆„");
	define("DB_PERSISTENT_DESC", "to use MySQL or Postgre persistent connections, tick this box. If you do not know what it means, then leaving it unticked is probably best.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "‰‘— ﬁ«⁄œ… «·»Ì«‰« ");
	define("DB_POPULATE_DESC", "·≈‰‘«¡ ÿ«Ê·… «·„⁄·Ê„«  Ê ‰‘—Â« „⁄ «·»Ì«‰« , «‘— Â‰«");
	define("DB_TEST_DATA_FIELD", "Test Data");
	define("DB_TEST_DATA_DESC", "to add some test data to your database tick the checkbox");
	define("ADMIN_EMAIL_FIELD", "»—Ìœ «·≈œ«—Â");
	define("ADMIN_LOGIN_FIELD", "«”„ «·„” Œœ„ ··„œÌ—");
	define("ADMIN_PASS_FIELD", "ﬂ·„… «·„—Ê—");
	define("ADMIN_CONF_FIELD", " √ﬂÌœ ﬂ·„… «·„—Ê—");
	define("DATETIME_SHOWN_FIELD", "(‘ﬂ· ⁄—÷ «·Êﬁ (··„Êﬁ⁄");
	define("DATE_SHOWN_FIELD", "(‘ﬂ· ⁄—÷ «· «—ÌŒ (··„Êﬁ⁄");
	define("DATETIME_EDIT_FIELD", "(‘ﬂ· ⁄—÷ «·Êﬁ  (·· Õ—Ì—");
	define("DATE_EDIT_FIELD", "(‘ﬂ· ⁄—÷ «· «—ÌŒ (·· Õ—Ì—");
	define("DATE_FORMAT_COLUMN", "‘ﬂ· ⁄—÷ «· «—ÌŒ");
	define("CURRENT_DATE_COLUMN", "«· «—ÌŒ «·Õ«·Ì");

	define("DB_LIBRARY_ERROR", "PHP functions for {db_library} are not defined. Please check your database settings in your configuration file - php.ini.");
	define("DB_CONNECT_ERROR", "·„ ‰” ÿ⁄ «·≈ ’«· »ﬁ«⁄œ… «·»Ì«‰« , «·—Ã«¡ «· √ﬂœ „‰ „⁄·Ê„«  ﬁ«⁄œ… »Ì«‰« ﬂ");
	define("INSTALL_FINISHED_ERROR", ". „ «·≈‰ Â«¡ „‰ ⁄„·Ì… «· ‰’Ì» „”»ﬁ«");
	define("WRITE_FILE_ERROR", ".«·—Ã«¡  ⁄œÌ· «·’·«ÕÌ«  ﬁ»· «·«” „—«— .<b>'includes/var_definition.php'</b> ·«  „·ﬂ ’·«ÕÌ«  «·ﬂ «»Â ··„·›");
	define("WRITE_DIR_ERROR", ".«·—Ã«¡  ⁄œÌ· «·’·«ÕÌ«  ﬁ»· «·«” „—«— .<b>'includes/'</b> ·«  „·ﬂ ’·«ÕÌ«  «·ﬂ «»Â ··„Ã·œ");
	define("DUMP_FILE_ERROR", ".«·„—«œ «” Œ—«ÃÂ '{file_name}' ·„ Ì „ «ÌÃ«œ «·„·›");
	define("DB_TABLE_ERROR", ".«·—Ã«¡ ‰‘— ﬁ«⁄œ… «·»Ì«‰«  »«·„⁄·Ê„«  «·„ÿ·Ê»Â '{table_name}' ·„ Ì „ «ÌÃ«œ «·ÿ«Ê·Â");
	define("TEST_DATA_ERROR", "Check <b>{POPULATE_DB_FIELD}</b> before populating tables with test data");
	define("DB_HOST_ERROR", "The hostname that you specified could not be found.");
	define("DB_PORT_ERROR", "Can't connect to database server using specified port.");
	define("DB_USER_PASS_ERROR", "The username or password you specified is not correct.");
	define("DB_NAME_ERROR", "Login settings were correct, but the database '{db_name}' could not be found.");

	// upgrade messages
	define("UPGRADE_TITLE", "·· ”Êﬁ ViArt  ÕœÌÀ »—‰«„Ã");
	define("UPGRADE_NOTE", ".„·«ÕŸÂ: «·—Ã«¡ «Œ– ‰”ŒÂ «Õ Ì«ÿÌÂ „‰ ﬁ«⁄œ… «·»Ì«‰«  ﬁ»· «·«” „—«—");
	define("UPGRADE_AVAILABLE_MSG", "«· ÕœÌÀ „ÊÃÊœ");
	define("UPGRADE_BUTTON", "{version_number} «· ÕœÌÀ «·¬‰ «·Ï «·‰”ŒÂ");
	define("CURRENT_VERSION_MSG", "«·‰”ŒÂ «·„” Œœ„Â Õ«·Ì« ÂÌ");
	define("LATEST_VERSION_MSG", "«·‰”ŒÂ «·„ Ê›—Â ·· ‰’Ì» ÂÌ");
	define("UPGRADE_RESULTS_MSG", "‰ «∆Ã «· ÕœÌÀ");
	define("SQL_SUCCESS_MSG", "‰ÃÕ  SQL „⁄«„·«  «·‹");
	define("SQL_FAILED_MSG", "›‘·  SQL „⁄«„·«  «·‹");
	define("SQL_TOTAL_MSG", "ÂÊ SQL „Ã„Ê⁄ „«  „  ‰›Ì–Â „‰ „⁄«„·«  «·‹");
	define("VERSION_UPGRADED_MSG", "·ﬁœ  „  ÕœÌÀ ‰”Œ ﬂ «·Ï");
	define("ALREADY_LATEST_MSG", "«‰  «·¬‰  „·ﬂ ¬Œ— «’œ«—Â „ Ê›—Â Õ«·Ì«");
	define("DOWNLOAD_NEW_MSG", "The new version was detected");
	define("DOWNLOAD_NOW_MSG", "Download version {version_number} now");
	define("DOWNLOAD_FOUND_MSG", "We have detected that the new {version_number} version is available to download. Please click the link below to start downloading. After completing the download and replacing the files don't forget to run Upgrade routine again.");
	define("NO_XML_CONNECTION", "Warning! No connection to 'http://www.viart.com/' available!");

?>