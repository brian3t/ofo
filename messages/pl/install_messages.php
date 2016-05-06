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


	// informacje dot. instalacji
	define("INSTALL_TITLE", "Instalacja ViArt SHOP");

	define("INSTALL_STEP_1_TITLE", "Instalacja: Krok 1");
	define("INSTALL_STEP_1_DESC", "Dziêkujemy za wybranie ViArt SHOP. Aby zakoñczyæ instalacjê, prosimy wype³nij podane ni¿ej szczegó³y. Prosimy zauwa¿yæ, ¿e baza danych, któr± Wybra³e¶/a¶ powinna ju¿ istnieæ. Je¶li instalujesz na istniej±cej ju¿ bazie danych ODBC np. MS Acces powiniene¶/a¶ najpierw utworzyæ DSN (Data Source Name) przed przyst±pieniem do instalacji.");
	define("INSTALL_STEP_2_TITLE", "Instalacja: Krok 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Instalacja: Krok 3");
	define("INSTALL_STEP_3_DESC", "Prosimy o wybranie uk³adu graficznego dla stron. Bêdziesz móg³/³a zmieniæ uk³ad pó¼niej.");
	define("INSTALL_FINAL_TITLE", "Instalacja: Koniec");
	define("SELECT_DATE_TITLE", "Wybierz format danych");

	define("DB_SETTINGS_MSG", "Ustawienia bazy danych");
	define("DB_PROGRESS_MSG", "Zape³nianie struktury bazy danych w toku.");
	define("SELECT_PHP_LIB_MSG", "Wybierz bibliotekê PHP");
	define("SELECT_DB_TYPE_MSG", "Wybierz rodzaj bazy danych");
	define("ADMIN_SETTINGS_MSG", "Ustawienia administracyjne");
	define("DATE_SETTINGS_MSG", "Formaty danych");
	define("NO_DATE_FORMATS_MSG", "Nie ma dostêpnych formatów danych");
	define("INSTALL_FINISHED_MSG", "W tym momencie Twoja podstawowa instalacja jest ju¿ kompletna. Prosimy sprawd¼ ustawienia w sekcji administracyjnej i wprowad¼ ewentualne wymagane zmiany.");
	define("ACCESS_ADMIN_MSG", "Aby dostaæ siê do sekcji administracyjnej kliknij tu");
	define("ADMIN_URL_MSG", "URL administracyjny");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", "Dziêkujemy za wybranie <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Rodzaj bazy danych");
	define("DB_TYPE_DESC", "Please select the <b>type of database</b> that you are using. If you using SQL Server or Microsoft Access, please select ODBC.");
	define("DB_PHP_LIB_FIELD", "Biblioteka PHP");
	define("DB_HOST_FIELD", "Nazwa hosta");
	define("DB_HOST_DESC", "Please enter the <b>name</b> or <b>IP address of the server</b> on which your ViArt database will run. If you are running your database on your local PC then you can probably just leave this as \"<b>localhost</b>\" and the port blank. If you using a database provided by your hosting company, please see your hosting company's documentation for the server settings.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Nazwa bazy danych / DSN ");
	define("DB_NAME_DESC", "If you are using a database such as MySQL or PostgreSQL then please enter the <b>name of the database</b> where you would like ViArt to create its tables. This database must exist already. If you are just installing ViArt for testing purposes on your local PC then most systems have a \"<b>test</b>\" database you can use. If not, please create a database such as \"viart\" and use that. If you are using Microsoft Access or SQL Server then the Database Name should be the <b>name of the DSN</b> that you have set up in the Data Sources (ODBC) section of your Control Panel.");
	define("DB_USER_FIELD", "U¿ytkownik");
	define("DB_PASS_FIELD", "Has³o");
	define("DB_USER_PASS_DESC", "<b>Username</b> and <b>Password</b> - please enter the username and password you want to use to access the database. If you are using a local test installation the username is probably \"<b>root</b>\" and the password is probably blank. This is fine for testing, but please note that this is not secure on production servers.");
	define("DB_PERSISTENT_FIELD", "Trwa³e po³±czenie");
	define("DB_PERSISTENT_DESC", "to use MySQL or Postgre persistent connections, tick this box. If you do not know what it means, then leaving it unticked is probably best.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "Zape³nianie bazy danych");
	define("DB_POPULATE_DESC", "aby utworzyæ tabelê bazy danych i jej strukturê oraz wype³niæ j± danymi zaznacz to pole");
	define("DB_TEST_DATA_FIELD", "Test Data");
	define("DB_TEST_DATA_DESC", "to add some test data to your database tick the checkbox");
	define("ADMIN_EMAIL_FIELD", "Email administratora");
	define("ADMIN_LOGIN_FIELD", "Login administratora");
	define("ADMIN_PASS_FIELD", "Has³o administratora");
	define("ADMIN_CONF_FIELD", "Potwierdzenie has³a");
	define("DATETIME_SHOWN_FIELD", "Format czasu dla danych (pokazany na stronach)");
	define("DATE_SHOWN_FIELD", "Format daty (pokazany na stronach)");
	define("DATETIME_EDIT_FIELD", "Format czasu dla danych (dla edycji)");
	define("DATE_EDIT_FIELD", "Format daty (dla edycji)");
	define("DATE_FORMAT_COLUMN", "Format danych");
	define("CURRENT_DATE_COLUMN", "Aktualna data");

	define("DB_LIBRARY_ERROR", "Funkcje PHP dla {db_library} nie zosta³y zdefiniowane. Prosimy o sprawdzenie ustawieñ bazy danych w pliku konfiguracyjnym - php.ini.");
	define("DB_CONNECT_ERROR", "Nie mo¿na po³±czyæ siê z baz± danych. Prosimy o sprawdzenie parametrów Twojej bazy danych.");
	define("INSTALL_FINISHED_ERROR", "Proces instalacji zakoñczy³ siê.");
	define("WRITE_FILE_ERROR", "Brak prawa do zapisu dla pliku <b>'includes/var_definition.php'</b>. Przed kontynuacj± prosimy zmieniæ prawa dostêpu do pliku.");
	define("WRITE_DIR_ERROR", "Brak prawa do zapisu dla katalogu <b>'includes/'</b>. Przed kontynuacj± prosimy zmieniæ prawa dostêpu do katalogu.");
	define("DUMP_FILE_ERROR", "Plik typu dump '{file_name}' nie zosta³ odnaleziony.");
	define("DB_TABLE_ERROR", "Tabela '{table_name}' nie zosta³a odnaleziona. Prosimy o wype³nienie bazy danych odpowiednimi danymi.");
	define("TEST_DATA_ERROR", "Check <b>{POPULATE_DB_FIELD}</b> before populating tables with test data");
	define("DB_HOST_ERROR", "The hostname that you specified could not be found.");
	define("DB_PORT_ERROR", "Can't connect to database server using specified port.");
	define("DB_USER_PASS_ERROR", "The username or password you specified is not correct.");
	define("DB_NAME_ERROR", "Login settings were correct, but the database '{db_name}' could not be found.");

	// informacje dot. uaktualnienia
	define("UPGRADE_TITLE", "Aktualizacja ViArt SHOP");
	define("UPGRADE_NOTE", "Uwaga: Prosimy o rozpatrzenie wykonania kopii zapasowej bazy danych przed przyst±pieniem do dzia³ania.");
	define("UPGRADE_AVAILABLE_MSG", "Dostêpna jest aktualizacja");
	define("UPGRADE_BUTTON", "Aktualizuj do {version_number} ");
	define("CURRENT_VERSION_MSG", "Twoja aktualnie zainstalowana wersja");
	define("LATEST_VERSION_MSG", "Werjsa dostêpna do instalacjii");
	define("UPGRADE_RESULTS_MSG", "Wyniki aktualizacji");
	define("SQL_SUCCESS_MSG", "Zapytania SQL powiod³y siê");
	define("SQL_FAILED_MSG", "Zapytania SQL nie powiod³y siê");
	define("SQL_TOTAL_MSG", "Wszystkich zapytañ SQL wykonano");
	define("VERSION_UPGRADED_MSG", "Twoja wersja zosta³a zaktualizowana do wersji");
	define("ALREADY_LATEST_MSG", "Ju¿ masz uaktualnion± ostatni± wersjê");
	define("DOWNLOAD_NEW_MSG", "The new version was detected");
	define("DOWNLOAD_NOW_MSG", "Download version {version_number} now");
	define("DOWNLOAD_FOUND_MSG", "We have detected that the new {version_number} version is available to download. Please click the link below to start downloading. After completing the download and replacing the files don't forget to run Upgrade routine again.");
	define("NO_XML_CONNECTION", "Warning! No connection to 'http://www.viart.com/' available!");

?>