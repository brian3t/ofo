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
	define("INSTALL_TITLE", "Installation De Magasin De ViArt");

	define("INSTALL_STEP_1_TITLE", "Installation : Étape 1");
	define("INSTALL_STEP_1_DESC", "Merci de choisir le magasin de ViArt. Afin d'accomplir ceci installez complètent svp les détails demandés ci-dessous. Veuillez noter que la base de données que vous installez sur devrait déjà exister. Si vous installez sur une base de données qui emploie ODBC, par exemple MME. accès vous devriez d'abord créer un DSN pour lui avant la marche à suivre.");
	define("INSTALL_STEP_2_TITLE", "Installation : Étape 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Installation : Étape 3");
	define("INSTALL_STEP_3_DESC", "Veuillez choisir la disposition d'emplacement. Vous pourrez changer la disposition après.");
	define("INSTALL_FINAL_TITLE", "Installation : Final");
	define("SELECT_DATE_TITLE", "Choisissez Le Format De Date");

	define("DB_SETTINGS_MSG", "Arrangements De Base de données");
	define("DB_PROGRESS_MSG", "Populating database structure progress");
	define("SELECT_PHP_LIB_MSG", "Choisissez La Bibliothèque de PHP");
	define("SELECT_DB_TYPE_MSG", "Choisissez Le Type De Base de données");
	define("ADMIN_SETTINGS_MSG", "Arrangements Administratifs");
	define("DATE_SETTINGS_MSG", "Formats De Date");
	define("NO_DATE_FORMATS_MSG", "Aucuns formats de date disponibles");
	define("INSTALL_FINISHED_MSG", "En ce moment votre installation de base est complète. Veuillez être sûr de vérifier les arrangements dans la pièce d'administration et de faire tous les changements requis.");
	define("ACCESS_ADMIN_MSG", "Pour accéder au clic de pièce d'administration ici");
	define("ADMIN_URL_MSG", "URL D'Administration");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", "Merci de choisir le magasin de ViArt.");

	define("DB_TYPE_FIELD", "Type De Base de données");
	define("DB_TYPE_DESC", "Please select the <b>type of database</b> that you are using. If you using SQL Server or Microsoft Access, please select ODBC.");
	define("DB_PHP_LIB_FIELD", "Bibliothèque de PHP");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "Please enter the <b>name</b> or <b>IP address of the server</b> on which your ViArt database will run. If you are running your database on your local PC then you can probably just leave this as \"<b>localhost</b>\" and the port blank. If you using a database provided by your hosting company, please see your hosting company's documentation for the server settings.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Nom De Base de données/DSN");
	define("DB_NAME_DESC", "If you are using a database such as MySQL or PostgreSQL then please enter the <b>name of the database</b> where you would like ViArt to create its tables. This database must exist already. If you are just installing ViArt for testing purposes on your local PC then most systems have a \"<b>test</b>\" database you can use. If not, please create a database such as \"viart\" and use that. If you are using Microsoft Access or SQL Server then the Database Name should be the <b>name of the DSN</b> that you have set up in the Data Sources (ODBC) section of your Control Panel.");
	define("DB_USER_FIELD", "Username");
	define("DB_PASS_FIELD", "Mot de passe");
	define("DB_USER_PASS_DESC", "<b>Username</b> and <b>Password</b> - please enter the username and password you want to use to access the database. If you are using a local test installation the username is probably \"<b>root</b>\" and the password is probably blank. This is fine for testing, but please note that this is not secure on production servers.");
	define("DB_PERSISTENT_FIELD", "Raccordement Persistant");
	define("DB_PERSISTENT_DESC", "to use MySQL or Postgre persistent connections, tick this box. If you do not know what it means, then leaving it unticked is probably best.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "Peuplez le DB");
	define("DB_POPULATE_DESC", "pour créer des tables structurez-et peuplez-l'avec le coutil de données le checkbox");
	define("DB_TEST_DATA_FIELD", "Test Data");
	define("DB_TEST_DATA_DESC", "to add some test data to your database tick the checkbox");
	define("ADMIN_EMAIL_FIELD", "Email D'Administrateur");
	define("ADMIN_LOGIN_FIELD", "Ouverture D'Administrateur");
	define("ADMIN_PASS_FIELD", "Mot de passe D'Administrateur");
	define("ADMIN_CONF_FIELD", "Confirmez Le Mot de passe");
	define("DATETIME_SHOWN_FIELD", "Format de Datetime (montré sur l'emplacement)");
	define("DATE_SHOWN_FIELD", "Format de date (montré sur l'emplacement)");
	define("DATETIME_EDIT_FIELD", "Format de Datetime (pour éditer)");
	define("DATE_EDIT_FIELD", "Format de date (pour éditer)");
	define("DATE_FORMAT_COLUMN", "Format De Date");
	define("CURRENT_DATE_COLUMN", "Current Date");

	define("DB_LIBRARY_ERROR", "PHP functions for {db_library} are not defined. Please check your database settings in your configuration file - php.ini.");
	define("DB_CONNECT_ERROR", "Can't connect to database. Please check your database parameters.");
	define("INSTALL_FINISHED_ERROR", "Installation process already finished.");
	define("WRITE_FILE_ERROR", "Don't have writable permission to file <b>'includes/var_definition.php'</b>. Please change file permissions before you continue.");
	define("WRITE_DIR_ERROR", "Don't have writable permission to folder <b>'includes/'</b>. Please change folder permissions before you continue.");
	define("DUMP_FILE_ERROR", "Dump file '{file_name}' wasn't found.");
	define("DB_TABLE_ERROR", "Table '{table_name}' wasn't found. Please populate the database with the necessary data.");
	define("TEST_DATA_ERROR", "Check <b>{POPULATE_DB_FIELD}</b> before populating tables with test data");
	define("DB_HOST_ERROR", "The hostname that you specified could not be found.");
	define("DB_PORT_ERROR", "Can't connect to database server using specified port.");
	define("DB_USER_PASS_ERROR", "The username or password you specified is not correct.");
	define("DB_NAME_ERROR", "Login settings were correct, but the database '{db_name}' could not be found.");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP Upgrade");
	define("UPGRADE_NOTE", "Note: Please consider making a database backup before proceeding.");
	define("UPGRADE_AVAILABLE_MSG", "Database upgrade available");
	define("UPGRADE_BUTTON", "Upgrade database to {version_number} now");
	define("CURRENT_VERSION_MSG", "Currently installed version");
	define("LATEST_VERSION_MSG", "Version available to install");
	define("UPGRADE_RESULTS_MSG", "Upgrade results");
	define("SQL_SUCCESS_MSG", "SQL queries succed");
	define("SQL_FAILED_MSG", "SQL queries failed");
	define("SQL_TOTAL_MSG", "Total SQL queries executed");
	define("VERSION_UPGRADED_MSG", "Your database has been upgraded to");
	define("ALREADY_LATEST_MSG", "You already have the latest version");
	define("DOWNLOAD_NEW_MSG", "The new version was detected");
	define("DOWNLOAD_NOW_MSG", "Download version {version_number} now");
	define("DOWNLOAD_FOUND_MSG", "We have detected that the new {version_number} version is available to download. Please click the link below to start downloading. After completing the download and replacing the files don't forget to run Upgrade routine again.");
	define("NO_XML_CONNECTION", "Warning! No connection to 'http://www.viart.com/' available!");

?>