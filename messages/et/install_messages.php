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
	define("INSTALL_TITLE", "ViArt SHOP installeerimine");

	define("INSTALL_STEP_1_TITLE", "Installeerimine: Samm 1");
	define("INSTALL_STEP_1_DESC", "Täname, et valisid ViArt SHOP'i. Installeerimise jätkamiseks, täida palun allolevad vajalikud andmed. Pane tähele, et valiksid juba olemasoleva andmebaasi. Kui installeerid andmebaasi, mis kasutab ODBC'd, näiteks MC Access, peaksid enne jätkamist looma sellele DSN'i.");
	define("INSTALL_STEP_2_TITLE", "Installeerimine: Samm 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Installeerimine: Samm 3");
	define("INSTALL_STEP_3_DESC", "Palun vali veebilehe paigutuse. Sa võid pärast paigutust muuta.");
	define("INSTALL_FINAL_TITLE", "Installeerimine: Lõpp");
	define("SELECT_DATE_TITLE", "Vali kuupäeva formaat");

	define("DB_SETTINGS_MSG", "Andmebaasi seaded");
	define("DB_PROGRESS_MSG", "Andmebaasi struktuuri loomise protsess");
	define("SELECT_PHP_LIB_MSG", "Vali PHP Library ");
	define("SELECT_DB_TYPE_MSG", "Vali andmebaasi tüüp");
	define("ADMIN_SETTINGS_MSG", "Administratiivsed seaded");
	define("DATE_SETTINGS_MSG", "Kuupäeva formaadid");
	define("NO_DATE_FORMATS_MSG", "Kuupäeva formaadid ei ole saadaval");
	define("INSTALL_FINISHED_MSG", "Nüüdseks on lõppenud sinu põhiinstalleerimine. Palun kontrolli administreerimise sektsiooni seadeid ning tee vajalikud muudatused.");
	define("ACCESS_ADMIN_MSG", "Administreerimise sektsiooni sisenemiseks kliki siia");
	define("ADMIN_URL_MSG", "Administratsiooni URL");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", "Täname, et valisid <b>ViArt SHOP</b>");

	define("DB_TYPE_FIELD", "Andmebaasi tüüp");
	define("DB_TYPE_DESC", "Palun vali <b>andmebaasi tüüp</b>, mida sa kasutad. Kui kasutad SQL serverit või Microsoft Access'i, vali palun ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP Library ");
	define("DB_HOST_FIELD", "Host'i nimi");
	define("DB_HOST_DESC", "Palun sisesta <b>nimi</b> või <b>serveri IP aadress</b>, millel hakkab töötama sinu ViArt andmebaas. Kui sinu andmebaas töötab sinu lokaalsel personaalarvutil, siis võid tõenäoliselt selle jätta kui \"<b>localhost</b>\" ja port tühjaks. Kui kasutad andmebaasi, mida pakub sulle hosting firma, vaata palun oma hosting firma dokumentatsioonist serveri seadeid.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Andmebaasi nimi / DSN");
	define("DB_NAME_DESC", "Kui kasutad MySQL või PostgreSQL sarnast andmebaasi, siis sisesta palun <b>andmebaasi nimi</b> , kuhu sa tahad, et ViArt loob oma tabelid. See andmebaas peab juba eksisteerima. Kui installeerid ViArt'i oma lokaalsesse personaalarvutisse testimise eesmärgil, siis enamikel süsteemidel on \"<b>test</b>\" andmebaas, mida saad kasutada. Kui mitte, loo andmebaas  viart  ja kasuta seda. Kui kasutad Microsoft Access'i või SQL serverit, siis andmebaasi nimi peaks olema <b>DSN'i nimi</b>, mille oled seadnud Data Sources (ODBC)sektsioonis oma arvuti Control Panel'is.");
	define("DB_USER_FIELD", "Kasutajanimi");
	define("DB_PASS_FIELD", "Parool");
	define("DB_USER_PASS_DESC", "<b>Kasutajanimi</b> ja <b>Parool</b> - palun sisesta kasutajanimi ja parool, mida tahad kasutada andmebaasi sisenemiseks. Kui kasutad lokaalset test installeerimist, on kasutajanimi tõenäoliselt \"<b>root</b>\" ja parool tühi. See sobib testimiseks, kuid pane tähele, et see ei ole ohutu tootmise serverites.");
	define("DB_PERSISTENT_FIELD", "Püsiühendus");
	define("DB_PERSISTENT_DESC", "Et kasutada MySQL või Postgre püsiühendusi, tee märge sellesse ruutu. Kui sa ei tea, mida see tähendab, siis parem oleks jätta see ruut tühjaks.");
	define("DB_CREATE_DB_FIELD", "Loo andmebaas");
	define("DB_CREATE_DB_DESC", "Andmebaasi loomiseks märgi võimalusel ära see ruut. Töötab ainult MySQL ja Postgre puhul.");
	define("DB_POPULATE_FIELD", "Asusta andmebaas");
	define("DB_POPULATE_DESC", "Andmebaasi tabeli struktuuri loomiseks ja selle asustamiseks andmetega tee märkeruutu märge");
	define("DB_TEST_DATA_FIELD", "Testandmed");
	define("DB_TEST_DATA_DESC", "Testandmete lisamiseks oma andmebaasi tee märkeruutu märge");
	define("ADMIN_EMAIL_FIELD", "Administrator Email");
	define("ADMIN_LOGIN_FIELD", "Administraatori kasutajanimi");
	define("ADMIN_PASS_FIELD", "Administraatori parool");
	define("ADMIN_CONF_FIELD", "Kinnita parool");
	define("DATETIME_SHOWN_FIELD", "Kuupäeva ja aja formaat (näidatud veebilehel)");
	define("DATE_SHOWN_FIELD", "Kuupäeva formaat (näidatud veebilehel)");
	define("DATETIME_EDIT_FIELD", "Kuupäeva ja aja formaat (muutmiseks)");
	define("DATE_EDIT_FIELD", "Kuupäea formaat (muutmiseks)");
	define("DATE_FORMAT_COLUMN", "Kuupäeva formaat");
	define("CURRENT_DATE_COLUMN", "Tänane kuupäev");

	define("DB_LIBRARY_ERROR", "{db_library} PHP funktsioonid ei ole määratletud. Palun kontrolli oma andmebaasi seadeid konfiguratsiooni failis   php.ini.");
	define("DB_CONNECT_ERROR", "Ei saa ühendada andmebaasiga. Palun kontrolli oma andmebaasi parameetreid.");
	define("INSTALL_FINISHED_ERROR", "Installeerimise protsess juba lõppenud.");
	define("WRITE_FILE_ERROR", "Ei ole kirjalikku luba failile <b>'includes/var_definition.php'</b>. Palun muuda faili luba enne jätkamist.");
	define("WRITE_DIR_ERROR", "Ei ole kirjalikku luba kaustale <b>'includes/'</b>. Palun muuda kausta luba enne jätkamist.");
	define("DUMP_FILE_ERROR", "Dump faili '{file_name}' ei leitud.");
	define("DB_TABLE_ERROR", "Tabelit '{table_name}' ei leitud. Palun sisesta andmebaasi vajalikud andmed.");
	define("TEST_DATA_ERROR", "Kontrolli <b>{POPULATE_DB_FIELD}</b> enne testandmete sisestamist andmebaasi");
	define("DB_HOST_ERROR", "Ei leitud sinu täpsustatud host'i nime.");
	define("DB_PORT_ERROR", "Ei saa ühendada andmebaasi serveriga kasutades antud porti.");
	define("DB_USER_PASS_ERROR", "Antud kasutajanimi või parool ei ole õige.");
	define("DB_NAME_ERROR", "Login seaded olid õiged, kuid andmebaasi '{db_name}'  ei leitud.");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP'i uuendamine");
	define("UPGRADE_NOTE", "Teade: Palun kaalu andmebaasi varukoopia tegemise võimalust enne jätkamist.");
	define("UPGRADE_AVAILABLE_MSG", "Andmebaasi uuendus on saadaval");
	define("UPGRADE_BUTTON", "Uuenda andmebaas {version_number} versiooniks nüüd");
	define("CURRENT_VERSION_MSG", "Preagu installeeritud versioon");
	define("LATEST_VERSION_MSG", "Versioon installeerimiseks saadaval");
	define("UPGRADE_RESULTS_MSG", "Uuendamise tulemused");
	define("SQL_SUCCESS_MSG", "SQL päringud õnnestusid");
	define("SQL_FAILED_MSG", "SQL päringud ebaõnnestusid");
	define("SQL_TOTAL_MSG", "Kõik SQL päringud lõpetatud");
	define("VERSION_UPGRADED_MSG", "Sinu andmebaas on uuendatud");
	define("ALREADY_LATEST_MSG", "Sul juba on uusim versioon");
	define("DOWNLOAD_NEW_MSG", "Avastatud on uus versioon");
	define("DOWNLOAD_NOW_MSG", "Lae alla  {version_number} versioon nüüd");
	define("DOWNLOAD_FOUND_MSG", "Oleme avastanud, et uus {version_number} versioon on saadaval allalaadimiseks. Palun kliki allolevale lingile, et alustada allalaadimist. Pärast allalaadimise lõppemist ning failide asendamist ära unusta läbi teha uuendamist.");
	define("NO_XML_CONNECTION", "Hoiatus! Ühendus 'http://www.viart.com/' ei ole saadaval!");

?>