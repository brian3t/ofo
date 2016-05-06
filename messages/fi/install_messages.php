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
	define("INSTALL_TITLE", "Viart Shop asennus");

	define("INSTALL_STEP_1_TITLE", "Asennus: Vaihe 1");
	define("INSTALL_STEP_1_DESC", "Kiitos kun asennat ViArt ostoskortin. Ole hyv� ja t�ydenn� alla olevat tiedot. Huomio ett� tietokannan tulisi jo olla olemassa. Jos asennat k�ytt�en ODBC/Microsoft Access, luo ensin DNS");
	define("INSTALL_STEP_2_TITLE", "Asennus: Vaihe 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Asennus: Vaihe 3");
	define("INSTALL_STEP_3_DESC", "Valitse sivun teema, t�t� asetusta voit muuttaa my�hemmin");
	define("INSTALL_FINAL_TITLE", "Asennus: Valmis");
	define("SELECT_DATE_TITLE", "Valitse p�iv�yksen n�ytt�");

	define("DB_SETTINGS_MSG", "Tietokanta-asetukset");
	define("DB_PROGRESS_MSG", "Tietokannan luominen");
	define("SELECT_PHP_LIB_MSG", "Valitse PHP kirjasto");
	define("SELECT_DB_TYPE_MSG", "Valitse tietokannan tyyppi");
	define("ADMIN_SETTINGS_MSG", "Hallinnalliset asetukset");
	define("DATE_SETTINGS_MSG", "Aika/P�iv�asetukset");
	define("NO_DATE_FORMATS_MSG", "Ei saatavilla");
	define("INSTALL_FINISHED_MSG", "T�ss� vaiheessa perusasennus on valmis. Ole hyv� ja k�yt� hallinta-asetuksia muuttaaksesi muita toimintoja");
	define("ACCESS_ADMIN_MSG", "P��st�ksesi hallintaosioon, paina t�st�");
	define("ADMIN_URL_MSG", "Hallinnan osoite");
	define("MANUAL_URL_MSG", "Manuaaliosoite (URL)");
	define("THANKS_MSG", "Kiitos kun valitsit <b>ViArt SHOP:in</b>.");

	define("DB_TYPE_FIELD", "Tietokannan tyyppi");
	define("DB_TYPE_DESC", "Valitse <b>tietokannan tyyppi</b> jota k�yt�t. Jos k�yt�t SQL Serveri� tai Microsoft Accessia, valitse ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP Kirjasto");
	define("DB_HOST_FIELD", "Is�nt�nimi");
	define("DB_HOST_DESC", "Anna <b>nimi</b> tai <b>serverin IP osoite</b> jossa ViArt tietokanta on. Jos serveri on omalla koneellasti, luultavasti \"<b>localhost</b>\" ja j�t� portti tyhj�ksi. Tarvittaessa kysy palveluntarjoajaltasi lis�tietoja tietokannoista.");
	define("DB_PORT_FIELD", "Portti");
	define("DB_NAME_FIELD", "Tietokannan nimi / DSN");
	define("DB_NAME_DESC", "Jos k�yt�t tietokantaa kuten MySQL tai PostgreSQL ole hyv� ja anna <b>tietokannan nimi</b> mihin haluat ViArtin luovan taulukot. T�m�n tietokannan pit�� olla jo olemassa. Jos olet vain asentamassa testitarkoituksessa, yleens� on olemassa \"<b>test</b>\" tietokanta jota voit k�ytt��. Jos ei, ole hyv� ja luo esim tietokanta 'viart'. ");
	define("DB_USER_FIELD", "K�ytt�j�tunnus");
	define("DB_PASS_FIELD", "Salasana");
	define("DB_USER_PASS_DESC", "<b>K�ytt�j�tunnus</b> ja <b>Salasana</b> -Ole hyv� ja anna k�ytt�j�tunnus ja salasana tietokannalle.Jos k�yt�t paikallista konetta, k�ytt�j�tunnus on tod.n�k \"<b>root</b>\" ja salasana tyhj�. T�m� on ok testauksessa, mutta ei varsinaisessa asennuksessa!");
	define("DB_PERSISTENT_FIELD", "Jatkuva yhteys");
	define("DB_PERSISTENT_DESC", "MYSQL/Postgre jatkuva yhteys. Jos et tied� mit� se tarkoittaa, on parempi olla koskematta t�h�n kohtaan");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "Luo tietokanta");
	define("DB_POPULATE_DESC", "Luo tietokanta ja rakenne");
	define("DB_TEST_DATA_FIELD", "Testidata");
	define("DB_TEST_DATA_DESC", "Tekee testidataa taulukkoon");
	define("ADMIN_EMAIL_FIELD", "Hallinnan s�hk�posti");
	define("ADMIN_LOGIN_FIELD", "Hallinan sis��nkirjaus");
	define("ADMIN_PASS_FIELD", "Hallinan salasana");
	define("ADMIN_CONF_FIELD", "Vahvista salasana");
	define("DATETIME_SHOWN_FIELD", "P�iv�m��r�n n�ytt� (sivustolla)");
	define("DATE_SHOWN_FIELD", "P�iv�yksen n�ytt� (sivustolla)");
	define("DATETIME_EDIT_FIELD", "P�iv�yksen n�ytt� (muokatessa)");
	define("DATE_EDIT_FIELD", "P�iv�m��r�n n�ytt� (muokatessa)");
	define("DATE_FORMAT_COLUMN", "P�iv�ysmuoto");
	define("CURRENT_DATE_COLUMN", "Nykyinen p�iv�");

	define("DB_LIBRARY_ERROR", "PHP toiminnot {db_library} eiv�t ole m��riteltyin�. Tarkista tietokannan m��ritykset tiedostosta - php.ini.");
	define("DB_CONNECT_ERROR", "En saa yhteytt� tietokantaan, tarkista asetukset");
	define("INSTALL_FINISHED_ERROR", "Asennus on jo tehty!");
	define("WRITE_FILE_ERROR", "Tiedostoon<b>'includes/var_definition.php'</b>. Ei voi kirjoittaa. Vaihda asetukset ennen jatkamista");
	define("WRITE_DIR_ERROR", "Kansioon <b>'includes/'</b>. Ei voi kirjoittaa. Vaihda asetuksia ennen jatkamista");
	define("DUMP_FILE_ERROR", "Dumppitiedostoa '{file_name}' ei l�ytynyt");
	define("DB_TABLE_ERROR", "Taulukkoa '{table_name}' ei l�ytynyt. Tee tarvittavat muutokset tietokantaan.");
	define("TEST_DATA_ERROR", "Tarkia<b>{POPULATE_DB_FIELD}</b> ennen testitaulukkojen tekemist�");
	define("DB_HOST_ERROR", "Is�nt�nime� jonka annoit ei l�ydy");
	define("DB_PORT_ERROR", "Yhteytt� t�h�n porttiin (MYSQL) ei voitu luoda");
	define("DB_USER_PASS_ERROR", "Salasana tai k�ytt�j�tunnus ovat v��rin");
	define("DB_NAME_ERROR", "Sis��nkirjaus onnistui, mutta tietokantaa '{db_name}' ei l�ytynyt");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP p�ivitys");
	define("UPGRADE_NOTE", "HUOM! Tee varmuuskopiot ennen jatkamista");
	define("UPGRADE_AVAILABLE_MSG", "Tietokannan p�ivitys saatavilla");
	define("UPGRADE_BUTTON", "P�ivit� tietokanta versioon {version_number} nyt");
	define("CURRENT_VERSION_MSG", "Nyt asennettu versio");
	define("LATEST_VERSION_MSG", "Versio saatavilla");
	define("UPGRADE_RESULTS_MSG", "P�ivityksen tulos");
	define("SQL_SUCCESS_MSG", "SQL kyselyt onnistuivat");
	define("SQL_FAILED_MSG", "SQL kyselyt ep�onnistuivat");
	define("SQL_TOTAL_MSG", "SQL kyselyit� suoritettu");
	define("VERSION_UPGRADED_MSG", "Tietokantasi on p�ivitetty");
	define("ALREADY_LATEST_MSG", "Sinulla on jo viimeisin versio");
	define("DOWNLOAD_NEW_MSG", "Uusin versio havaittu");
	define("DOWNLOAD_NOW_MSG", "Lataa versio {version_number} nyt");
	define("DOWNLOAD_FOUND_MSG", "Huomasimme, ett� uusi versio {version_number} on saatavilla. Klikkaa linkki� aloittaaksesi latauksen.ladattuasi ja p�ivitetty�si, muista k�ynnist�� p�ivitystoiminto uudelleen");
	define("NO_XML_CONNECTION", "Varoitus! Yhteytt� osoitteeseen 'http://www.viart.com/' ei voida luoda!");

?>