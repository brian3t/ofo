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
	define("INSTALL_TITLE", "ViArt SHOP Instalace");

	define("INSTALL_STEP_1_TITLE", "Instalace: Krok 1");
	define("INSTALL_STEP_1_DESC", "Dìujeme, že jste si vybrali ViArt SHOP. Pro dokonèení instalace, vyplòte nasledovný formuláø. Nezapomeòte, že databáze musí již existovat. Pokud instalujete na databázi, která využívá ODBC jako napøíklad MS Access, mìli by jste njdøív vytvoøit DSN, teprv pak pokraèovat");
	define("INSTALL_STEP_2_TITLE", "Instalace: Krok 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Instalace: Krok 3");
	define("INSTALL_STEP_3_DESC", "Prosím vyberte si vzhled stránky. Pozdìji ho mùžete zmìnit.");
	define("INSTALL_FINAL_TITLE", "Instalace: Poslední krok");
	define("SELECT_DATE_TITLE", "Vyberte formát datumu");

	define("DB_SETTINGS_MSG", "Nastavení databáze");
	define("DB_PROGRESS_MSG", "Stav naplnìní struktury databáze");
	define("SELECT_PHP_LIB_MSG", "Vyberte PHP knižnici");
	define("SELECT_DB_TYPE_MSG", "Vyberte typ databáze");
	define("ADMIN_SETTINGS_MSG", "Administraèní nastavení");
	define("DATE_SETTINGS_MSG", "Formáty datumu");
	define("NO_DATE_FORMATS_MSG", "Žádné formáty datumu nejsou k dispozci");
	define("INSTALL_FINISHED_MSG", "V tomhle okamžiku je Vaše instalace ukonèená. Prosím zkontrolujte nastavení v administraèních nastaveních a udìlejte potøebná nastavení.");
	define("ACCESS_ADMIN_MSG", "Pro pøístup k administraèním nastavením kliknìte sem");
	define("ADMIN_URL_MSG", "Administraèní URL");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", "Dìkujeme, že jste si vybrali <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Typ databáze");
	define("DB_TYPE_DESC", "Please select the <b>type of database</b> that you are using. If you using SQL Server or Microsoft Access, please select ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP knižnice");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "Please enter the <b>name</b> or <b>IP address of the server</b> on which your ViArt database will run. If you are running your database on your local PC then you can probably just leave this as \"<b>localhost</b>\" and the port blank. If you using a database provided by your hosting company, please see your hosting company's documentation for the server settings.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Jméno databáze / DSN");
	define("DB_NAME_DESC", "If you are using a database such as MySQL or PostgreSQL then please enter the <b>name of the database</b> where you would like ViArt to create its tables. This database must exist already. If you are just installing ViArt for testing purposes on your local PC then most systems have a \"<b>test</b>\" database you can use. If not, please create a database such as \"viart\" and use that. If you are using Microsoft Access or SQL Server then the Database Name should be the <b>name of the DSN</b> that you have set up in the Data Sources (ODBC) section of your Control Panel.");
	define("DB_USER_FIELD", "Užívatelské jméno");
	define("DB_PASS_FIELD", "Heslo");
	define("DB_USER_PASS_DESC", "<b>Username</b> and <b>Password</b> - please enter the username and password you want to use to access the database. If you are using a local test installation the username is probably \"<b>root</b>\" and the password is probably blank. This is fine for testing, but please note that this is not secure on production servers.");
	define("DB_PERSISTENT_FIELD", "Permanetní pøipojení");
	define("DB_PERSISTENT_DESC", "to use MySQL or Postgre persistent connections, tick this box. If you do not know what it means, then leaving it unticked is probably best.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "Naplnit databázi");
	define("DB_POPULATE_DESC", "Pro vytvoøení tabulky struktury databáze a její naplnìní datama zakliknìte tohle políèko");
	define("DB_TEST_DATA_FIELD", "Test Data");
	define("DB_TEST_DATA_DESC", "to add some test data to your database tick the checkbox");
	define("ADMIN_EMAIL_FIELD", "Email administrátora");
	define("ADMIN_LOGIN_FIELD", "Administrátorovo uživatelské jméno");
	define("ADMIN_PASS_FIELD", "Administrátorovo heslo");
	define("ADMIN_CONF_FIELD", "Potvrdit heslo");
	define("DATETIME_SHOWN_FIELD", "Formát èasu (zobrazený na stránce)");
	define("DATE_SHOWN_FIELD", "Formát datumu (zobrazený na stránce)");
	define("DATETIME_EDIT_FIELD", "Formát èasu (pro úpravy)");
	define("DATE_EDIT_FIELD", "Formát datumu (pro úpravy)");
	define("DATE_FORMAT_COLUMN", "Formát datumu");
	define("CURRENT_DATE_COLUMN", "Dnešní datum");

	define("DB_LIBRARY_ERROR", "PHP funkce pro {db_library} nejsou definovány. Prosím zkontrolujte nastavení databáze v konfiguraèním souboru – php.ini.");
	define("DB_CONNECT_ERROR", "Nemùžu se pøipojit k databázi. Zkontrolujte parametry databáze.");
	define("INSTALL_FINISHED_ERROR", "Instalaèní proces byl již ukonèen.");
	define("WRITE_FILE_ERROR", "Nemám právo pro zápis do souboru <b>'includes/var_definition.php'</b>. Pøed pokraèováním zkontrolujte pøístupová práva.");
	define("WRITE_DIR_ERROR", "Nemám práva pro zápis do složky <b>'includes/'</b>. Pøed pokraèováním zkontrolujte pøístupová práva.");
	define("DUMP_FILE_ERROR", "Dump soubor '{file_name}' nebyl nalezen.");
	define("DB_TABLE_ERROR", "Tabulka '{table_name}' nebyla nalezena. Prosím naplòte databázi pøíslušnýma datama.");
	define("TEST_DATA_ERROR", "Check <b>{POPULATE_DB_FIELD}</b> before populating tables with test data");
	define("DB_HOST_ERROR", "The hostname that you specified could not be found.");
	define("DB_PORT_ERROR", "Can't connect to database server using specified port.");
	define("DB_USER_PASS_ERROR", "The username or password you specified is not correct.");
	define("DB_NAME_ERROR", "Login settings were correct, but the database '{db_name}' could not be found.");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP Aktualizace");
	define("UPGRADE_NOTE", "Poznámka: Zvažte prosím vytvoøení zálohy databáze pøed pokraèováním.");
	define("UPGRADE_AVAILABLE_MSG", "Aktualizace k dispozici");
	define("UPGRADE_BUTTON", "Aktualizovat na verzi {version_number} teï");
	define("CURRENT_VERSION_MSG", "Vaše aktuálnì nainstalovaná verze");
	define("LATEST_VERSION_MSG", "Verze dostupná pro instalaci");
	define("UPGRADE_RESULTS_MSG", "Výsledky aktualizace");
	define("SQL_SUCCESS_MSG", "SQL dotaz úspìšný");
	define("SQL_FAILED_MSG", "SQL dotaz neúspìšný");
	define("SQL_TOTAL_MSG", "Spolu vykonaných SQL dotazù");
	define("VERSION_UPGRADED_MSG", "Vaše verze byla aktualizována na");
	define("ALREADY_LATEST_MSG", "Máte nainstalovanou nejaktuálnìjší verzi");
	define("DOWNLOAD_NEW_MSG", "The new version was detected");
	define("DOWNLOAD_NOW_MSG", "Download version {version_number} now");
	define("DOWNLOAD_FOUND_MSG", "We have detected that the new {version_number} version is available to download. Please click the link below to start downloading. After completing the download and replacing the files don't forget to run Upgrade routine again.");
	define("NO_XML_CONNECTION", "Warning! No connection to 'http://www.viart.com/' available!");

?>