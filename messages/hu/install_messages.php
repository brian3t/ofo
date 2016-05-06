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


	//telepítési üzenetek
	define("INSTALL_TITLE", " ViArtshop Telepítés");

	define("INSTALL_STEP_1_TITLE", "Telepítés: Lépés 1");
	define("INSTALL_STEP_1_DESC", "Köszönet a ViArt SHOP választásáért. A telepítés folytatásához ki kell tölteni a  lenti mezõket. Figyelem: az adatbázis kiválasztásához már elõbb létre kell hozni egy adatbázist. Ha olyan adatbázist telepítesz ami  ODBC t  használ, mint a Microsoft Access , tovább haladás elõtt  létre kell hozni DNS t.");
	define("INSTALL_STEP_2_TITLE", "Telepítés: Lépés 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Telepítés: Lépés 3");
	define("INSTALL_STEP_3_DESC", "Kérem a website megjelenés minták közül választani. Késõbb lesz lehetõség a változtatásra.");
	define("INSTALL_FINAL_TITLE", "Telepítés: befejezés");
	define("SELECT_DATE_TITLE", "Válassz dátumot formátumot");

	define("DB_SETTINGS_MSG", "Adatbázis beállítások");
	define("DB_PROGRESS_MSG", "Népszerû adatbázis struktúra eljárás");
	define("SELECT_PHP_LIB_MSG", "Válassz PHP Könyvtárat");
	define("SELECT_DB_TYPE_MSG", "Válassz adatbázis típust");
	define("ADMIN_SETTINGS_MSG", "Adminisztratív beállítások");
	define("DATE_SETTINGS_MSG", "Dátum formátumok");
	define("NO_DATE_FORMATS_MSG", "Nincs elérhetõ dátum formátum");
	define("INSTALL_FINISHED_MSG", "Ennél pont az alap telepítésed  teljes. Kérem persze ellenõrizni a beállításokat az adminisztrációban és végrehajtani a szüksége változtatásokat.");
	define("ACCESS_ADMIN_MSG", "Az adminisztráció rész eléréséhez kattintás itt");
	define("ADMIN_URL_MSG", "Adminisztráció URL");
	define("MANUAL_URL_MSG", "Használat utasítás URL");
	define("THANKS_MSG", "Köszönjük, hogy a <b>ViArt SHOP</b>t választotta.   ");

	define("DB_TYPE_FIELD", "Adatbázis Típus");
	define("DB_TYPE_DESC", "Kérem kiválasztani <b>adatbázis típusa</b> amelyiket használod. Ha  SQL Servert vagy Microsoft Access,akkor válaszd: ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP Könyvtár");
	define("DB_HOST_FIELD", "Hostnév");
	define("DB_HOST_DESC", "Kérem beírni  <b>szervernév</b>vagy <b>szerver IP címe</b>ahol a Viart adatbázis futni fog. Ha az adatbázis lokális PC-én fut akkor valószínûleg jó a \"<b>localhost</b>\" és hagyd a portot üresen. Ha egy szolgáltatód által biztosított adatbázist használsz , akkor tanulmányozd át a szolgáltató szerver beállításainak a dokumentációját.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Adatbázis Név/ DSN");
	define("DB_NAME_DESC", "Ha olyan adatbázist használsz , mint a  MySQL vagy PostgreSQL akkor írd be <b>adatbázis neve</b> ahol szeretnéd , hogy a ViArt létrehozza a tábláit Ennek az adatbázisnak létezõnek kell lenni. Ha tesztelés céljából telepíted a Viart lokális pc-re, akkor a legtöbb gépnek van egy <b>test</b>\" adatbázisa , amit használhatsz. Ha nem , akkor készíts egy adatbázist \"viart\" néven , és azt használd. Ha  Microsoft Access-t vagy r SQL Servert használsz akkor az adatbázis nevének  <b>DSN neve</b> kell lenni , amit beállítottál az adatforrásoknál(ODBC)  a kontrol panelen.");
	define("DB_USER_FIELD", "Felhasználónév");
	define("DB_PASS_FIELD", "Jelszó");
	define("DB_USER_PASS_DESC", "<b>felhasználónév</b> és <b>jelszó</b> -kérem beírni a felhasználónevet és a jelszót amit használni akarsz az adatbázis eléréséhez. Ha egy lokális gépen teszt installálást használsz, a lehetséges felhasználónév \"<b>root</b>\" és nincs jelszó. Ez így kiváló tesztelésre , de nem biztonságos egy nyilvános szerveren.");
	define("DB_PERSISTENT_FIELD", "Állandó Kapcsolat");
	define("DB_PERSISTENT_DESC", "Állandó MySQL//Postgre kapcsolat esetén klikkeld be ezt a dobozt, ha nem tudod , hogy mit jelent , inkább hagyd üresen.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "népszerû DB");
	define("DB_POPULATE_DESC", "Az adatbázis táblázat elkészítéséhez klikkelj a dobozba.");
	define("DB_TEST_DATA_FIELD", "Próba adat");
	define("DB_TEST_DATA_DESC", "Néhány próba adat hozzáadásához klikkel a dobozba.");
	define("ADMIN_EMAIL_FIELD", "Ügyintézõ Email");
	define("ADMIN_LOGIN_FIELD", "Ügyintézõ azonosító");
	define("ADMIN_PASS_FIELD", "Ügyintézõ jelszó");
	define("ADMIN_CONF_FIELD", "Ismételd meg a jelszót");
	define("DATETIME_SHOWN_FIELD", "Idõ formátum (webhelyen látszik)");
	define("DATE_SHOWN_FIELD", "Dátum formátum (webhelyen látszik)");
	define("DATETIME_EDIT_FIELD", "Idõ formátum (szerkesztéskor)");
	define("DATE_EDIT_FIELD", "Dátum formátum (szerkesztéskor)");
	define("DATE_FORMAT_COLUMN", "Dátum formátum");
	define("CURRENT_DATE_COLUMN", "Mai dátum");

	define("DB_LIBRARY_ERROR", "PHP funkciók nincsenek definiálva a {db_library} számára. Kérem ellenõrizze az adatbázis beállításait a konfigurációban. Fájl:  php.ini.");
	define("DB_CONNECT_ERROR", "Nem lehet csatlakozni az adatbázishoz. Kérem ellenõrizze az adatbázis paramétereit.");
	define("INSTALL_FINISHED_ERROR", " A telepítés folyamat már befejezett.");
	define("WRITE_FILE_ERROR", "Nincs írási engedélye a <b>'includes/var_definition.php'</b> fájlhoz. Folytatás elõtt meg kell változatni.");
	define("WRITE_DIR_ERROR", "Nem rendelkezik írási engedéllyel a <b>'includes/'</b> mappához. Kérem megváltoztatni a mappa engedélyeket.");
	define("DUMP_FILE_ERROR", "Dump fájl '{file_name}' nem található.");
	define("DB_TABLE_ERROR", "Tábla 'table_name' nem található. Kérem feltölteni az adatbázist a szükséges adattal.");
	define("TEST_DATA_ERROR", "Ellenõrizd a <b>{POPULATE_DB_FIELD}</b> mielõtt közéteszel táblákat teszt adatokkal.");
	define("DB_HOST_ERROR", "A hostnév amit meghatároztál, nem található.");
	define("DB_PORT_ERROR", "MySQL szerver meghatározott portjához nem lehet csatlakozni.");
	define("DB_USER_PASS_ERROR", "A meghatározott felhasználónév jelszó helytelen.");
	define("DB_NAME_ERROR", "Login beállítások rendben vannak, de az adatbázis '{db_name}'  nem található.");

	//frissítés üzenetek
	define("UPGRADE_TITLE", " ViArt SHOP frissítés");
	define("UPGRADE_NOTE", "Megjegyezés: Kérem készítsen mentést az adatbázisról, mielõtt frissítene!");
	define("UPGRADE_AVAILABLE_MSG", "Adatbázis frissített változat elérhetõ");
	define("UPGRADE_BUTTON", "Frissítés a  {version_number} verzióra ");
	define("CURRENT_VERSION_MSG", "Jelenleg telepített változatot");
	define("LATEST_VERSION_MSG", "Elérhetõ telepíthetõ változat ");
	define("UPGRADE_RESULTS_MSG", "Frissítés eredménye");
	define("SQL_SUCCESS_MSG", "SQL lekérdezés sikeres");
	define("SQL_FAILED_MSG", "SQL lekérdezés nem sikerült");
	define("SQL_TOTAL_MSG", "Teljes SQL lekérdezés megtörtént");
	define("VERSION_UPGRADED_MSG", "Az adatbázisod frissített a");
	define("ALREADY_LATEST_MSG", "Már a legújabb változattal rendelkezel.");
	define("DOWNLOAD_NEW_MSG", "Új változatot találtunk.");
	define("DOWNLOAD_NOW_MSG", " {version_number} verzió letöltése most.");
	define("DOWNLOAD_FOUND_MSG", "Érzékeltük, hogy az új {version_number} verzió letölthetõ. Az alábbi linkre kattintva lehet elkezdeni a letöltést. A letöltés után és a fájlok cserélése ne felejtsd  frissítést újra futatni.");
	define("NO_XML_CONNECTION", "Figyelmeztetés! Nincs kapcsolat 'HTTP:www.viart.com/' ! ");

?>