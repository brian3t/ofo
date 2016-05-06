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
	define("INSTALL_STEP_1_DESC", "D�ujeme, �e jste si vybrali ViArt SHOP. Pro dokon�en� instalace, vypl�te nasledovn� formul��. Nezapome�te, �e datab�ze mus� ji� existovat. Pokud instalujete na datab�zi, kter� vyu��v� ODBC jako nap��klad MS Access, m�li by jste njd��v vytvo�it DSN, teprv pak pokra�ovat");
	define("INSTALL_STEP_2_TITLE", "Instalace: Krok 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Instalace: Krok 3");
	define("INSTALL_STEP_3_DESC", "Pros�m vyberte si vzhled str�nky. Pozd�ji ho m��ete zm�nit.");
	define("INSTALL_FINAL_TITLE", "Instalace: Posledn� krok");
	define("SELECT_DATE_TITLE", "Vyberte form�t datumu");

	define("DB_SETTINGS_MSG", "Nastaven� datab�ze");
	define("DB_PROGRESS_MSG", "Stav napln�n� struktury datab�ze");
	define("SELECT_PHP_LIB_MSG", "Vyberte PHP kni�nici");
	define("SELECT_DB_TYPE_MSG", "Vyberte typ datab�ze");
	define("ADMIN_SETTINGS_MSG", "Administra�n� nastaven�");
	define("DATE_SETTINGS_MSG", "Form�ty datumu");
	define("NO_DATE_FORMATS_MSG", "��dn� form�ty datumu nejsou k dispozci");
	define("INSTALL_FINISHED_MSG", "V tomhle okam�iku je Va�e instalace ukon�en�. Pros�m zkontrolujte nastaven� v administra�n�ch nastaven�ch a ud�lejte pot�ebn� nastaven�.");
	define("ACCESS_ADMIN_MSG", "Pro p��stup k administra�n�m nastaven�m klikn�te sem");
	define("ADMIN_URL_MSG", "Administra�n� URL");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", "D�kujeme, �e jste si vybrali <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Typ datab�ze");
	define("DB_TYPE_DESC", "Please select the <b>type of database</b> that you are using. If you using SQL Server or Microsoft Access, please select ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP kni�nice");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "Please enter the <b>name</b> or <b>IP address of the server</b> on which your ViArt database will run. If you are running your database on your local PC then you can probably just leave this as \"<b>localhost</b>\" and the port blank. If you using a database provided by your hosting company, please see your hosting company's documentation for the server settings.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Jm�no datab�ze / DSN");
	define("DB_NAME_DESC", "If you are using a database such as MySQL or PostgreSQL then please enter the <b>name of the database</b> where you would like ViArt to create its tables. This database must exist already. If you are just installing ViArt for testing purposes on your local PC then most systems have a \"<b>test</b>\" database you can use. If not, please create a database such as \"viart\" and use that. If you are using Microsoft Access or SQL Server then the Database Name should be the <b>name of the DSN</b> that you have set up in the Data Sources (ODBC) section of your Control Panel.");
	define("DB_USER_FIELD", "U��vatelsk� jm�no");
	define("DB_PASS_FIELD", "Heslo");
	define("DB_USER_PASS_DESC", "<b>Username</b> and <b>Password</b> - please enter the username and password you want to use to access the database. If you are using a local test installation the username is probably \"<b>root</b>\" and the password is probably blank. This is fine for testing, but please note that this is not secure on production servers.");
	define("DB_PERSISTENT_FIELD", "Permanetn� p�ipojen�");
	define("DB_PERSISTENT_DESC", "to use MySQL or Postgre persistent connections, tick this box. If you do not know what it means, then leaving it unticked is probably best.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "Naplnit datab�zi");
	define("DB_POPULATE_DESC", "Pro vytvo�en� tabulky struktury datab�ze a jej� napln�n� datama zaklikn�te tohle pol��ko");
	define("DB_TEST_DATA_FIELD", "Test Data");
	define("DB_TEST_DATA_DESC", "to add some test data to your database tick the checkbox");
	define("ADMIN_EMAIL_FIELD", "Email administr�tora");
	define("ADMIN_LOGIN_FIELD", "Administr�torovo u�ivatelsk� jm�no");
	define("ADMIN_PASS_FIELD", "Administr�torovo heslo");
	define("ADMIN_CONF_FIELD", "Potvrdit heslo");
	define("DATETIME_SHOWN_FIELD", "Form�t �asu (zobrazen� na str�nce)");
	define("DATE_SHOWN_FIELD", "Form�t datumu (zobrazen� na str�nce)");
	define("DATETIME_EDIT_FIELD", "Form�t �asu (pro �pravy)");
	define("DATE_EDIT_FIELD", "Form�t datumu (pro �pravy)");
	define("DATE_FORMAT_COLUMN", "Form�t datumu");
	define("CURRENT_DATE_COLUMN", "Dne�n� datum");

	define("DB_LIBRARY_ERROR", "PHP funkce pro {db_library} nejsou definov�ny. Pros�m zkontrolujte nastaven� datab�ze v konfigura�n�m souboru � php.ini.");
	define("DB_CONNECT_ERROR", "Nem��u se p�ipojit k datab�zi. Zkontrolujte parametry datab�ze.");
	define("INSTALL_FINISHED_ERROR", "Instala�n� proces byl ji� ukon�en.");
	define("WRITE_FILE_ERROR", "Nem�m pr�vo pro z�pis do souboru <b>'includes/var_definition.php'</b>. P�ed pokra�ov�n�m zkontrolujte p��stupov� pr�va.");
	define("WRITE_DIR_ERROR", "Nem�m pr�va pro z�pis do slo�ky <b>'includes/'</b>. P�ed pokra�ov�n�m zkontrolujte p��stupov� pr�va.");
	define("DUMP_FILE_ERROR", "Dump soubor '{file_name}' nebyl nalezen.");
	define("DB_TABLE_ERROR", "Tabulka '{table_name}' nebyla nalezena. Pros�m napl�te datab�zi p��slu�n�ma datama.");
	define("TEST_DATA_ERROR", "Check <b>{POPULATE_DB_FIELD}</b> before populating tables with test data");
	define("DB_HOST_ERROR", "The hostname that you specified could not be found.");
	define("DB_PORT_ERROR", "Can't connect to database server using specified port.");
	define("DB_USER_PASS_ERROR", "The username or password you specified is not correct.");
	define("DB_NAME_ERROR", "Login settings were correct, but the database '{db_name}' could not be found.");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP Aktualizace");
	define("UPGRADE_NOTE", "Pozn�mka: Zva�te pros�m vytvo�en� z�lohy datab�ze p�ed pokra�ov�n�m.");
	define("UPGRADE_AVAILABLE_MSG", "Aktualizace k dispozici");
	define("UPGRADE_BUTTON", "Aktualizovat na verzi {version_number} te�");
	define("CURRENT_VERSION_MSG", "Va�e aktu�ln� nainstalovan� verze");
	define("LATEST_VERSION_MSG", "Verze dostupn� pro instalaci");
	define("UPGRADE_RESULTS_MSG", "V�sledky aktualizace");
	define("SQL_SUCCESS_MSG", "SQL dotaz �sp�n�");
	define("SQL_FAILED_MSG", "SQL dotaz ne�sp�n�");
	define("SQL_TOTAL_MSG", "Spolu vykonan�ch SQL dotaz�");
	define("VERSION_UPGRADED_MSG", "Va�e verze byla aktualizov�na na");
	define("ALREADY_LATEST_MSG", "M�te nainstalovanou nejaktu�ln�j�� verzi");
	define("DOWNLOAD_NEW_MSG", "The new version was detected");
	define("DOWNLOAD_NOW_MSG", "Download version {version_number} now");
	define("DOWNLOAD_FOUND_MSG", "We have detected that the new {version_number} version is available to download. Please click the link below to start downloading. After completing the download and replacing the files don't forget to run Upgrade routine again.");
	define("NO_XML_CONNECTION", "Warning! No connection to 'http://www.viart.com/' available!");

?>