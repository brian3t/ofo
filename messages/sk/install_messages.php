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
	define("INSTALL_TITLE", "ViArt SHOP In�tal�cia");

	define("INSTALL_STEP_1_TITLE", "In�tal�cia: Krok 1");
	define("INSTALL_STEP_1_DESC", "�akujeme, �e ste si vybrali ViArt SHOP. Aby ste mohli dokon�i� in�tal�ciu, vypl�te nasledovn� formul�r. Nezabudnite, �e datab�za mus� u� existova�. Ak in�talujete na datab�zu, ktor� pou��va ODBC ako napr�klad MS Access, mali by ste najprv vytvori� DSN, a� potom pokra�ova�");
	define("INSTALL_STEP_2_TITLE", "In�tal�cia: Krok 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "In�tal�cia: Krok 3");
	define("INSTALL_STEP_3_DESC", "Pros�m vyberte si vzh�ad str�nky. Nesk�r ho m��ete zmeni�.");
	define("INSTALL_FINAL_TITLE", "In�tal�cia: Posledn� krok");
	define("SELECT_DATE_TITLE", "Vyberte form�t d�tumu");

	define("DB_SETTINGS_MSG", "Nastavenia datab�zy");
	define("DB_PROGRESS_MSG", "Stav nap��ania �trukt�ry datab�zy");
	define("SELECT_PHP_LIB_MSG", "Vyberte PHP kni�nicu");
	define("SELECT_DB_TYPE_MSG", "Vyberte typ datab�zy");
	define("ADMIN_SETTINGS_MSG", "Administra�n� nastavenia");
	define("DATE_SETTINGS_MSG", "Form�ty d�tumu");
	define("NO_DATE_FORMATS_MSG", "�iadne form�ty d�tumu nie s� k dispoz�cii");
	define("INSTALL_FINISHED_MSG", "V tomto okamihu je Va�a in�tal�cia ukon�en�. Pros�m skontrolujte nastavenia v administra�n�ch nastaveniach a urobte potrebn� nastavenia.");
	define("ACCESS_ADMIN_MSG", "Pre pr�stup k administra�n�m nastaveniam kliknite sem");
	define("ADMIN_URL_MSG", "Administra�n� URL");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", "�akujeme, �e ste si vybrali <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Typ datab�zy");
	define("DB_TYPE_DESC", "Please select the <b>type of database</b> that you are using. If you using SQL Server or Microsoft Access, please select ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP kni�nica");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "Please enter the <b>name</b> or <b>IP address of the server</b> on which your ViArt database will run. If you are running your database on your local PC then you can probably just leave this as \"<b>localhost</b>\" and the port blank. If you using a database provided by your hosting company, please see your hosting company's documentation for the server settings.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Meno datab�zy / DSN");
	define("DB_NAME_DESC", "If you are using a database such as MySQL or PostgreSQL then please enter the <b>name of the database</b> where you would like ViArt to create its tables. This database must exist already. If you are just installing ViArt for testing purposes on your local PC then most systems have a \"<b>test</b>\" database you can use. If not, please create a database such as \"viart\" and use that. If you are using Microsoft Access or SQL Server then the Database Name should be the <b>name of the DSN</b> that you have set up in the Data Sources (ODBC) section of your Control Panel.");
	define("DB_USER_FIELD", "U��vate�sk� meno");
	define("DB_PASS_FIELD", "Heslo");
	define("DB_USER_PASS_DESC", "<b>Username</b> and <b>Password</b> - please enter the username and password you want to use to access the database. If you are using a local test installation the username is probably \"<b>root</b>\" and the password is probably blank. This is fine for testing, but please note that this is not secure on production servers.");
	define("DB_PERSISTENT_FIELD", "Permanetn� pripojenie");
	define("DB_PERSISTENT_DESC", "to use MySQL or Postgre persistent connections, tick this box. If you do not know what it means, then leaving it unticked is probably best.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "Naplni� datab�zu");
	define("DB_POPULATE_DESC", "Pre vytvorenie tabu�ky �trukt�ry datab�zy a jej naplnenie d�tami zakliknite toto pol��ko");
	define("DB_TEST_DATA_FIELD", "Test Data");
	define("DB_TEST_DATA_DESC", "to add some test data to your database tick the checkbox");
	define("ADMIN_EMAIL_FIELD", "Administr�torov email");
	define("ADMIN_LOGIN_FIELD", "Administr�torove u��vate�sk� meno");
	define("ADMIN_PASS_FIELD", "Administr�torove heslo");
	define("ADMIN_CONF_FIELD", "Potvrdi� heslo");
	define("DATETIME_SHOWN_FIELD", "Form�t �asu (zobrazen� na str�nke)");
	define("DATE_SHOWN_FIELD", "Form�t d�tumu (zobrazen� na str�nke)");
	define("DATETIME_EDIT_FIELD", "Form�t �asu (pre �pravy)");
	define("DATE_EDIT_FIELD", "Form�t d�tumu (pre �pravy)");
	define("DATE_FORMAT_COLUMN", "Form�t d�tumu");
	define("CURRENT_DATE_COLUMN", "Dne�n� d�tum");

	define("DB_LIBRARY_ERROR", "PHP funkcie pre {db_library} nie s� definovan�. Pros�m skontrolujte nastavenia datab�zy v konfigura�nom s�bore - php.ini.");
	define("DB_CONNECT_ERROR", "Nem��em sa pripoji� k datab�ze. Skontrolujte parametre datab�zy.");
	define("INSTALL_FINISHED_ERROR", "In�tala�n� proces bol u� ukon�en�.");
	define("WRITE_FILE_ERROR", "Nem�m pr�va na z�pis do s�boru <b>'includes/var_definition.php'</b>. Pred pokra�ovan�m skontrolujte pr�stupov� pr�va.");
	define("WRITE_DIR_ERROR", "Nem�m pr�va na z�pis do adres�ra <b>'includes/'</b>. Pred pokra�ovan�m skontrolujte pr�stupov� pr�va.");
	define("DUMP_FILE_ERROR", "Dump s�bor '{file_name}' nebol n�jden�.");
	define("DB_TABLE_ERROR", "Tabu�ka '{table_name}' nebola n�jden�. Pros�m napl�te datab�zu pr�slu�n�mi d�tami.");
	define("TEST_DATA_ERROR", "Check <b>{POPULATE_DB_FIELD}</b> before populating tables with test data");
	define("DB_HOST_ERROR", "The hostname that you specified could not be found.");
	define("DB_PORT_ERROR", "Can't connect to database server using specified port.");
	define("DB_USER_PASS_ERROR", "The username or password you specified is not correct.");
	define("DB_NAME_ERROR", "Login settings were correct, but the database '{db_name}' could not be found.");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP Aktualiz�cia");
	define("UPGRADE_NOTE", "Pozn�mka: Zv�te pros�m vytvorenie z�lohy datab�zy pred pokra�ovan�m.");
	define("UPGRADE_AVAILABLE_MSG", "Aktualiz�cia k dispoz�cii");
	define("UPGRADE_BUTTON", "Aktualizova� na verziu {version_number} teraz");
	define("CURRENT_VERSION_MSG", "Va�a aktu�lne nain�talovan� verzia");
	define("LATEST_VERSION_MSG", "Verzia dostupn� na in�tal�ciu");
	define("UPGRADE_RESULTS_MSG", "V�sledky aktualiz�cie");
	define("SQL_SUCCESS_MSG", "SQL dotaz �spe�n�");
	define("SQL_FAILED_MSG", "SQL dotaz ne�spe�n�");
	define("SQL_TOTAL_MSG", "Spolu vykonan�ch SQL dotazov");
	define("VERSION_UPGRADED_MSG", "Va�a verzia bola aktualizovan� na");
	define("ALREADY_LATEST_MSG", "M�te nain�talovan� najaktu�lnej�iu verziu");
	define("DOWNLOAD_NEW_MSG", "The new version was detected");
	define("DOWNLOAD_NOW_MSG", "Download version {version_number} now");
	define("DOWNLOAD_FOUND_MSG", "We have detected that the new {version_number} version is available to download. Please click the link below to start downloading. After completing the download and replacing the files don't forget to run Upgrade routine again.");
	define("NO_XML_CONNECTION", "Warning! No connection to 'http://www.viart.com/' available!");

?>