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
	define("INSTALL_TITLE", "ViArt SHOP instalâcija");

	define("INSTALL_STEP_1_TITLE", "Instalâcija. Solis 1.");
	define("INSTALL_STEP_1_DESC", "Paldies, ka esat izvçlçjuðies ViArt SHOP risinâjumu. Lai pabeigtu instalâciju, lûdzu, aizpildiet zemâk pieprasîto informâciju. Lûdzu, atceraties, ka datubâzei jâbût izveidotai pirms instalâcijas. Ja instalçjat datubâzi, kas izmanto ODBC, piemçram, MS Access, lûdzu, izveidojiet DSN pirms turpiniet tâlâk.");
	define("INSTALL_STEP_2_TITLE", "Instalâcija. Solis 2.");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Instalâcija. Solis 3.");
	define("INSTALL_STEP_3_DESC", "Lûdzu, izvçlaties lapas izskatu. Jums bûs to iespçja vçlâk vçl koriìçt.");
	define("INSTALL_FINAL_TITLE", "Instalâcija: Pabeigta");
	define("SELECT_DATE_TITLE", "Izvçlaties datuma formâtu");

	define("DB_SETTINGS_MSG", "Datubâzes uzstâdîjumi");
	define("DB_PROGRESS_MSG", "Datubâzes struktûras izveides process");
	define("SELECT_PHP_LIB_MSG", "Izvçlaties PHP Library");
	define("SELECT_DB_TYPE_MSG", "Izvçlaties Datubâzes veidu");
	define("ADMIN_SETTINGS_MSG", "Administratora uzstâdîjumi");
	define("DATE_SETTINGS_MSG", "Datuma formâts");
	define("NO_DATE_FORMATS_MSG", "Datuma formâti nav pieejami");
	define("INSTALL_FINISHED_MSG", "Ðajâ brîdî Jûsu instalâcija ir pabeigta. Lûzu, pârliecinaties, kâdi ir uzstâdîtie parametri administrâcijas sadaïâ un veiciet nepiecieðamâs izmaiòas.");
	define("ACCESS_ADMIN_MSG", "Lai piekïûtu Administratora sadaïai, spiediet ðeit");
	define("ADMIN_URL_MSG", "Administratora adrese URL");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", "Paldies Jums, ka esat izvçlçjuðies <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Datubâzes veids");
	define("DB_TYPE_DESC", "Please select the <b>type of database</b> that you are using. If you using SQL Server or Microsoft Access, please select ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP Library");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "Please enter the <b>name</b> or <b>IP address of the server</b> on which your ViArt database will run. If you are running your database on your local PC then you can probably just leave this as \"<b>localhost</b>\" and the port blank. If you using a database provided by your hosting company, please see your hosting company's documentation for the server settings.");
	define("DB_PORT_FIELD", "Ports");
	define("DB_NAME_FIELD", "Datubâzes nosaukums / DSN");
	define("DB_NAME_DESC", "If you are using a database such as MySQL or PostgreSQL then please enter the <b>name of the database</b> where you would like ViArt to create its tables. This database must exist already. If you are just installing ViArt for testing purposes on your local PC then most systems have a \"<b>test</b>\" database you can use. If not, please create a database such as \"viart\" and use that. If you are using Microsoft Access or SQL Server then the Database Name should be the <b>name of the DSN</b> that you have set up in the Data Sources (ODBC) section of your Control Panel.");
	define("DB_USER_FIELD", "Lietotâjvârds");
	define("DB_PASS_FIELD", "Parole");
	define("DB_USER_PASS_DESC", "<b>Username</b> and <b>Password</b> - please enter the username and password you want to use to access the database. If you are using a local test installation the username is probably \"<b>root</b>\" and the password is probably blank. This is fine for testing, but please note that this is not secure on production servers.");
	define("DB_PERSISTENT_FIELD", "Stabils savienojums");
	define("DB_PERSISTENT_DESC", "to use MySQL or Postgre persistent connections, tick this box. If you do not know what it means, then leaving it unticked is probably best.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "Apstrâdât datubâzi");
	define("DB_POPULATE_DESC", "izveidot datubâzes tabulas struktûru, ievietot datus tabulâs, atzîmçjiet ar íeksîti");
	define("DB_TEST_DATA_FIELD", "Test Data");
	define("DB_TEST_DATA_DESC", "to add some test data to your database tick the checkbox");
	define("ADMIN_EMAIL_FIELD", "Administratora e-pasts");
	define("ADMIN_LOGIN_FIELD", "Administratora lietotâjs");
	define("ADMIN_PASS_FIELD", "Administratora parole");
	define("ADMIN_CONF_FIELD", "Apstiprinât paroli");
	define("DATETIME_SHOWN_FIELD", "Datuma un laiks formâts (tiks râdîts mâjas lapâ)");
	define("DATE_SHOWN_FIELD", "Datuma formâts (tiks râdîts mâjas lapâ)");
	define("DATETIME_EDIT_FIELD", "Datuma un laika formâts (tiks râdîts Administratora lapâ)");
	define("DATE_EDIT_FIELD", "Datuma formâts (tiks râdîts Administratora lapâ)");
	define("DATE_FORMAT_COLUMN", "Datuma formâts");
	define("CURRENT_DATE_COLUMN", "Patreizçjais datums");

	define("DB_LIBRARY_ERROR", "PHP functions for {db_library} are not defined. Please check your database settings in your configuration file - php.ini.");
	define("DB_CONNECT_ERROR", "Nevar pieslçgties datubâzei. Lûdzu, pârbaudiet datubâzes uzstâdîjumus.");
	define("INSTALL_FINISHED_ERROR", "Instalâcijas process ir pabeigts.");
	define("WRITE_FILE_ERROR", "Failam <b>'includes/var_definition.php'</b> nav uzstâdîtas visas lietoðanas tiesîbas. Pirms turpiniet tâlâk, mainît uzstâdîtos faila parametrus.");
	define("WRITE_DIR_ERROR", "Folderim <b>'includes/'</b> nav uzstâdîtas visas lietoðanas tiesîbas. Pirms turpinât tâlâk, mainiet foldera uzstâdîtos pieejas parametrus");
	define("DUMP_FILE_ERROR", "Dump fails '{file_name}' nav atrasts.");
	define("DB_TABLE_ERROR", "Tabula '{table_name}' nav atrasta. Lûdzu, papildiniet datubâzi ar nepiecieðamo informâciju.");
	define("TEST_DATA_ERROR", "Check <b>{POPULATE_DB_FIELD}</b> before populating tables with test data");
	define("DB_HOST_ERROR", "The hostname that you specified could not be found.");
	define("DB_PORT_ERROR", "Can't connect to database server using specified port.");
	define("DB_USER_PASS_ERROR", "The username or password you specified is not correct.");
	define("DB_NAME_ERROR", "Login settings were correct, but the database '{db_name}' could not be found.");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP Jauninâjumi (Upgrade)");
	define("UPGRADE_NOTE", "Piezîme: Pirms turpinât, lûdzu, izveidojiet datubâzes rezerves versiju.");
	define("UPGRADE_AVAILABLE_MSG", "Pieejamie jauninâjumi (Upgrade)");
	define("UPGRADE_BUTTON", "Veikt jauninâjumus uz {version_number} versiju");
	define("CURRENT_VERSION_MSG", "Patreizçjâ instalâcijas versija");
	define("LATEST_VERSION_MSG", "Pieejamâ instalâcijas versija");
	define("UPGRADE_RESULTS_MSG", "Jauninâjumu rezultâti");
	define("SQL_SUCCESS_MSG", "SQL pieprasîjumi izpildîti");
	define("SQL_FAILED_MSG", "SQL pieprasîjumi nav izpildîti");
	define("SQL_TOTAL_MSG", "Kopâ izpildîtie SQL pieprasîjumi");
	define("VERSION_UPGRADED_MSG", "Jûsu programmas versija ir atjaunota uz");
	define("ALREADY_LATEST_MSG", "Jums ir pieejama jaunâkâ programmas versija");
	define("DOWNLOAD_NEW_MSG", "The new version was detected");
	define("DOWNLOAD_NOW_MSG", "Download version {version_number} now");
	define("DOWNLOAD_FOUND_MSG", "We have detected that the new {version_number} version is available to download. Please click the link below to start downloading. After completing the download and replacing the files don't forget to run Upgrade routine again.");
	define("NO_XML_CONNECTION", "Warning! No connection to 'http://www.viart.com/' available!");

?>