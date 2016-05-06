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
	define("INSTALL_TITLE", "ViArt SHOP instal�cija");

	define("INSTALL_STEP_1_TITLE", "Instal�cija. Solis 1.");
	define("INSTALL_STEP_1_DESC", "Paldies, ka esat izv�l�ju�ies ViArt SHOP risin�jumu. Lai pabeigtu instal�ciju, l�dzu, aizpildiet zem�k piepras�to inform�ciju. L�dzu, atceraties, ka datub�zei j�b�t izveidotai pirms instal�cijas. Ja instal�jat datub�zi, kas izmanto ODBC, piem�ram, MS Access, l�dzu, izveidojiet DSN pirms turpiniet t�l�k.");
	define("INSTALL_STEP_2_TITLE", "Instal�cija. Solis 2.");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Instal�cija. Solis 3.");
	define("INSTALL_STEP_3_DESC", "L�dzu, izv�laties lapas izskatu. Jums b�s to iesp�ja v�l�k v�l kori��t.");
	define("INSTALL_FINAL_TITLE", "Instal�cija: Pabeigta");
	define("SELECT_DATE_TITLE", "Izv�laties datuma form�tu");

	define("DB_SETTINGS_MSG", "Datub�zes uzst�d�jumi");
	define("DB_PROGRESS_MSG", "Datub�zes strukt�ras izveides process");
	define("SELECT_PHP_LIB_MSG", "Izv�laties PHP Library");
	define("SELECT_DB_TYPE_MSG", "Izv�laties Datub�zes veidu");
	define("ADMIN_SETTINGS_MSG", "Administratora uzst�d�jumi");
	define("DATE_SETTINGS_MSG", "Datuma form�ts");
	define("NO_DATE_FORMATS_MSG", "Datuma form�ti nav pieejami");
	define("INSTALL_FINISHED_MSG", "�aj� br�d� J�su instal�cija ir pabeigta. L�zu, p�rliecinaties, k�di ir uzst�d�tie parametri administr�cijas sada�� un veiciet nepiecie�am�s izmai�as.");
	define("ACCESS_ADMIN_MSG", "Lai piek��tu Administratora sada�ai, spiediet �eit");
	define("ADMIN_URL_MSG", "Administratora adrese URL");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", "Paldies Jums, ka esat izv�l�ju�ies <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Datub�zes veids");
	define("DB_TYPE_DESC", "Please select the <b>type of database</b> that you are using. If you using SQL Server or Microsoft Access, please select ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP Library");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "Please enter the <b>name</b> or <b>IP address of the server</b> on which your ViArt database will run. If you are running your database on your local PC then you can probably just leave this as \"<b>localhost</b>\" and the port blank. If you using a database provided by your hosting company, please see your hosting company's documentation for the server settings.");
	define("DB_PORT_FIELD", "Ports");
	define("DB_NAME_FIELD", "Datub�zes nosaukums / DSN");
	define("DB_NAME_DESC", "If you are using a database such as MySQL or PostgreSQL then please enter the <b>name of the database</b> where you would like ViArt to create its tables. This database must exist already. If you are just installing ViArt for testing purposes on your local PC then most systems have a \"<b>test</b>\" database you can use. If not, please create a database such as \"viart\" and use that. If you are using Microsoft Access or SQL Server then the Database Name should be the <b>name of the DSN</b> that you have set up in the Data Sources (ODBC) section of your Control Panel.");
	define("DB_USER_FIELD", "Lietot�jv�rds");
	define("DB_PASS_FIELD", "Parole");
	define("DB_USER_PASS_DESC", "<b>Username</b> and <b>Password</b> - please enter the username and password you want to use to access the database. If you are using a local test installation the username is probably \"<b>root</b>\" and the password is probably blank. This is fine for testing, but please note that this is not secure on production servers.");
	define("DB_PERSISTENT_FIELD", "Stabils savienojums");
	define("DB_PERSISTENT_DESC", "to use MySQL or Postgre persistent connections, tick this box. If you do not know what it means, then leaving it unticked is probably best.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "Apstr�d�t datub�zi");
	define("DB_POPULATE_DESC", "izveidot datub�zes tabulas strukt�ru, ievietot datus tabul�s, atz�m�jiet ar �eks�ti");
	define("DB_TEST_DATA_FIELD", "Test Data");
	define("DB_TEST_DATA_DESC", "to add some test data to your database tick the checkbox");
	define("ADMIN_EMAIL_FIELD", "Administratora e-pasts");
	define("ADMIN_LOGIN_FIELD", "Administratora lietot�js");
	define("ADMIN_PASS_FIELD", "Administratora parole");
	define("ADMIN_CONF_FIELD", "Apstiprin�t paroli");
	define("DATETIME_SHOWN_FIELD", "Datuma un laiks form�ts (tiks r�d�ts m�jas lap�)");
	define("DATE_SHOWN_FIELD", "Datuma form�ts (tiks r�d�ts m�jas lap�)");
	define("DATETIME_EDIT_FIELD", "Datuma un laika form�ts (tiks r�d�ts Administratora lap�)");
	define("DATE_EDIT_FIELD", "Datuma form�ts (tiks r�d�ts Administratora lap�)");
	define("DATE_FORMAT_COLUMN", "Datuma form�ts");
	define("CURRENT_DATE_COLUMN", "Patreiz�jais datums");

	define("DB_LIBRARY_ERROR", "PHP functions for {db_library} are not defined. Please check your database settings in your configuration file - php.ini.");
	define("DB_CONNECT_ERROR", "Nevar piesl�gties datub�zei. L�dzu, p�rbaudiet datub�zes uzst�d�jumus.");
	define("INSTALL_FINISHED_ERROR", "Instal�cijas process ir pabeigts.");
	define("WRITE_FILE_ERROR", "Failam <b>'includes/var_definition.php'</b> nav uzst�d�tas visas lieto�anas ties�bas. Pirms turpiniet t�l�k, main�t uzst�d�tos faila parametrus.");
	define("WRITE_DIR_ERROR", "Folderim <b>'includes/'</b> nav uzst�d�tas visas lieto�anas ties�bas. Pirms turpin�t t�l�k, mainiet foldera uzst�d�tos pieejas parametrus");
	define("DUMP_FILE_ERROR", "Dump fails '{file_name}' nav atrasts.");
	define("DB_TABLE_ERROR", "Tabula '{table_name}' nav atrasta. L�dzu, papildiniet datub�zi ar nepiecie�amo inform�ciju.");
	define("TEST_DATA_ERROR", "Check <b>{POPULATE_DB_FIELD}</b> before populating tables with test data");
	define("DB_HOST_ERROR", "The hostname that you specified could not be found.");
	define("DB_PORT_ERROR", "Can't connect to database server using specified port.");
	define("DB_USER_PASS_ERROR", "The username or password you specified is not correct.");
	define("DB_NAME_ERROR", "Login settings were correct, but the database '{db_name}' could not be found.");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP Jaunin�jumi (Upgrade)");
	define("UPGRADE_NOTE", "Piez�me: Pirms turpin�t, l�dzu, izveidojiet datub�zes rezerves versiju.");
	define("UPGRADE_AVAILABLE_MSG", "Pieejamie jaunin�jumi (Upgrade)");
	define("UPGRADE_BUTTON", "Veikt jaunin�jumus uz {version_number} versiju");
	define("CURRENT_VERSION_MSG", "Patreiz�j� instal�cijas versija");
	define("LATEST_VERSION_MSG", "Pieejam� instal�cijas versija");
	define("UPGRADE_RESULTS_MSG", "Jaunin�jumu rezult�ti");
	define("SQL_SUCCESS_MSG", "SQL piepras�jumi izpild�ti");
	define("SQL_FAILED_MSG", "SQL piepras�jumi nav izpild�ti");
	define("SQL_TOTAL_MSG", "Kop� izpild�tie SQL piepras�jumi");
	define("VERSION_UPGRADED_MSG", "J�su programmas versija ir atjaunota uz");
	define("ALREADY_LATEST_MSG", "Jums ir pieejama jaun�k� programmas versija");
	define("DOWNLOAD_NEW_MSG", "The new version was detected");
	define("DOWNLOAD_NOW_MSG", "Download version {version_number} now");
	define("DOWNLOAD_FOUND_MSG", "We have detected that the new {version_number} version is available to download. Please click the link below to start downloading. After completing the download and replacing the files don't forget to run Upgrade routine again.");
	define("NO_XML_CONNECTION", "Warning! No connection to 'http://www.viart.com/' available!");

?>