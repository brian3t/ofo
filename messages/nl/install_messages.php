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
	define("INSTALL_TITLE", "ViArt SHOP Installatie");

	define("INSTALL_STEP_1_TITLE", "Installatie: Stap 1");
	define("INSTALL_STEP_1_DESC", "Bedankt dat u voor ViArt SHOP heeft gekozen. Vul a.u.b. onderstaande gegevens in om de installatie succesvol af te kunnen ronden . Let er op dat de database die u installeert ook daadwerkelijk bestaat. Als u naar een database installeert die gebruik maakt");
	define("INSTALL_STEP_2_TITLE", "Installatie: Stap 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Installatie: Stap 3");
	define("INSTALL_STEP_3_DESC", "Selecteer a.u.b. een site-layout. Deze kunt u later altijd nog wijzigen.");
	define("INSTALL_FINAL_TITLE", "Installatie: Afsluiting");
	define("SELECT_DATE_TITLE", "Selecteer Datumformaat");

	define("DB_SETTINGS_MSG", "Database Instellingen");
	define("DB_PROGRESS_MSG", "Databse wordt aangemaakt.");
	define("SELECT_PHP_LIB_MSG", "Selecteer PHP bibliotheek");
	define("SELECT_DB_TYPE_MSG", "Selecteer database type");
	define("ADMIN_SETTINGS_MSG", "Administratieve instellingen");
	define("DATE_SETTINGS_MSG", "Datumformaten");
	define("NO_DATE_FORMATS_MSG", "Geen datumformaten beschikbaar");
	define("INSTALL_FINISHED_MSG", "Op dit punt is uw basisinsstallatie gereed. Controleer a.u.b. nu uw instellingen in het administratiedeel en doe de noodzakelijke aanpassingen.");
	define("ACCESS_ADMIN_MSG", "Voor toegang tot het administratiedeel klik hier");
	define("ADMIN_URL_MSG", "Administratie URL");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", "Bedankt voor het kiezen van<b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Database Type");
	define("DB_TYPE_DESC", "Please select the <b>type of database</b> that you are using. If you using SQL Server or Microsoft Access, please select ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP Library");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "Please enter the <b>name</b> or <b>IP address of the server</b> on which your ViArt database will run. If you are running your database on your local PC then you can probably just leave this as \"<b>localhost</b>\" and the port blank. If you using a databas");
	define("DB_PORT_FIELD", "Poort");
	define("DB_NAME_FIELD", "Database Naam / DSN");
	define("DB_NAME_DESC", "If you are using a database such as MySQL or PostgreSQL then please enter the <b>name of the database</b> where you would like ViArt to create its tables. This database must exist already. If you are just installing ViArt for testing purposes on your loca");
	define("DB_USER_FIELD", "Username");
	define("DB_PASS_FIELD", "Paswoord");
	define("DB_USER_PASS_DESC", "<b>Username</b> and <b>Paswoord</b> -geef de  gebruikersbenaming en het wachtwoord in dat u wilt gebruiken om tot het gegevensbestand toegang te hebben. Als u een lokale testinstallatie gebruikt is de gebruikersbenaming waarschijnlijk \" root\" en het wachtwoord is waarschijnlijk leeg. Dit is fijn voor test.");
	define("DB_PERSISTENT_FIELD", "Persistent Connection");
	define("DB_PERSISTENT_DESC", "to use MySQL or Postgre persistent connections, tick this box. If you do not know what it means, then leaving it unticked is probably best.");
	define("DB_CREATE_DB_FIELD", "Database aanmaken");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "Database voorzien van gegevens");
	define("DB_POPULATE_DESC", "Om de database aan te maken , en te vullen vink deze checkbox.");
	define("DB_TEST_DATA_FIELD", "Test Data");
	define("DB_TEST_DATA_DESC", "Om testdate toetevoegen aaan de database vink deze checkbox");
	define("ADMIN_EMAIL_FIELD", "Administrator Email");
	define("ADMIN_LOGIN_FIELD", "Administrator Login");
	define("ADMIN_PASS_FIELD", "Administrator Paswoord");
	define("ADMIN_CONF_FIELD", "Bevestig Paswoord");
	define("DATETIME_SHOWN_FIELD", "Datumtijd Formaat (getoond op site)");
	define("DATE_SHOWN_FIELD", "Datum Formaat (getoond op site)");
	define("DATETIME_EDIT_FIELD", "Datumtijd Formaat (bij bewerkingen)");
	define("DATE_EDIT_FIELD", "Datum Formaat (bij bewerkingen)");
	define("DATE_FORMAT_COLUMN", "Datum Formaat");
	define("CURRENT_DATE_COLUMN", "Huidige Datum");

	define("DB_LIBRARY_ERROR", "PHP functies voor {db_library} zijn niet gedefinieerd. Controleer de database instellingen van de configuratie file - php.ini.");
	define("DB_CONNECT_ERROR", "Kan geen verbinding maken met de database. Controleer de database instellingen.");
	define("INSTALL_FINISHED_ERROR", "Instalatie proces is al uitgevoerd.");
	define("WRITE_FILE_ERROR", "U heeft geen schrijfrechten op de file: <b>'includes/var_definition.php'</b>. Wijzig de schrijfrechten voordat u verder gaat.");
	define("WRITE_DIR_ERROR", "U heeft geen schrijfrechten op de folder: <b>'includes/'</b>. Wijzig de schrijfrechten voordat u verder gaat.");
	define("DUMP_FILE_ERROR", "Dump file '{file_name}' is niet gevonden.");
	define("DB_TABLE_ERROR", "Table '{table_name}' wasn't found. Please populate the database with the necessary data.");
	define("TEST_DATA_ERROR", "Check <b>{POPULATE_DB_FIELD}</b> before populating tables with test data");
	define("DB_HOST_ERROR", "The hostname that you specified could not be found.");
	define("DB_PORT_ERROR", "Can't connect to MySQL server using specified port.");
	define("DB_USER_PASS_ERROR", "The username or password you specified is not correct.");
	define("DB_NAME_ERROR", "Login settings were correct, but the database '{db_name}' could not be found.");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP Upgrade");
	define("UPGRADE_NOTE", "Tip: Aub denk eraan om een database backup te maken voordat U ver gaat. ");
	define("UPGRADE_AVAILABLE_MSG", "Database upgrade voorhanden");
	define("UPGRADE_BUTTON", "Upgrade  nu database naar  {version_number}.");
	define("CURRENT_VERSION_MSG", "huidige geinstalleerde versie");
	define("LATEST_VERSION_MSG", "Versie beschikbaar vooor installatie");
	define("UPGRADE_RESULTS_MSG", "Upgrade resultaat");
	define("SQL_SUCCESS_MSG", "SQL queries geslaagd");
	define("SQL_FAILED_MSG", "SQL queries mislukt");
	define("SQL_TOTAL_MSG", "Totaal SQL queries uitgevoerd");
	define("VERSION_UPGRADED_MSG", "Uw database is geupdated naar");
	define("ALREADY_LATEST_MSG", "U heeft al de laatse versie");
	define("DOWNLOAD_NEW_MSG", "Een nieuwe versie is beschikbaar");
	define("DOWNLOAD_NOW_MSG", "Download nu versie {version_number} ");
	define("DOWNLOAD_FOUND_MSG", "We hebben gedetecteerd dat de nieuwe  {version_number} versie beschikbaar is om te downloaden. Aub klik op de link om het downloaden te starten. Na de download, en het vervangen van de file's, moet U op deze pagina de Upgrade routine nog uitvoeren.");
	define("NO_XML_CONNECTION", "Warning! Geen verbinding met 'http://www.viart.com/' mogelijk!");

?>