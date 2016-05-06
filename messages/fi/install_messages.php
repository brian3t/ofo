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
	define("INSTALL_STEP_1_DESC", "Kiitos kun asennat ViArt ostoskortin. Ole hyvä ja täydennä alla olevat tiedot. Huomio että tietokannan tulisi jo olla olemassa. Jos asennat käyttäen ODBC/Microsoft Access, luo ensin DNS");
	define("INSTALL_STEP_2_TITLE", "Asennus: Vaihe 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Asennus: Vaihe 3");
	define("INSTALL_STEP_3_DESC", "Valitse sivun teema, tätä asetusta voit muuttaa myöhemmin");
	define("INSTALL_FINAL_TITLE", "Asennus: Valmis");
	define("SELECT_DATE_TITLE", "Valitse päiväyksen näyttö");

	define("DB_SETTINGS_MSG", "Tietokanta-asetukset");
	define("DB_PROGRESS_MSG", "Tietokannan luominen");
	define("SELECT_PHP_LIB_MSG", "Valitse PHP kirjasto");
	define("SELECT_DB_TYPE_MSG", "Valitse tietokannan tyyppi");
	define("ADMIN_SETTINGS_MSG", "Hallinnalliset asetukset");
	define("DATE_SETTINGS_MSG", "Aika/Päiväasetukset");
	define("NO_DATE_FORMATS_MSG", "Ei saatavilla");
	define("INSTALL_FINISHED_MSG", "Tässä vaiheessa perusasennus on valmis. Ole hyvä ja käytä hallinta-asetuksia muuttaaksesi muita toimintoja");
	define("ACCESS_ADMIN_MSG", "Päästäksesi hallintaosioon, paina tästä");
	define("ADMIN_URL_MSG", "Hallinnan osoite");
	define("MANUAL_URL_MSG", "Manuaaliosoite (URL)");
	define("THANKS_MSG", "Kiitos kun valitsit <b>ViArt SHOP:in</b>.");

	define("DB_TYPE_FIELD", "Tietokannan tyyppi");
	define("DB_TYPE_DESC", "Valitse <b>tietokannan tyyppi</b> jota käytät. Jos käytät SQL Serveriä tai Microsoft Accessia, valitse ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP Kirjasto");
	define("DB_HOST_FIELD", "Isäntänimi");
	define("DB_HOST_DESC", "Anna <b>nimi</b> tai <b>serverin IP osoite</b> jossa ViArt tietokanta on. Jos serveri on omalla koneellasti, luultavasti \"<b>localhost</b>\" ja jätä portti tyhjäksi. Tarvittaessa kysy palveluntarjoajaltasi lisätietoja tietokannoista.");
	define("DB_PORT_FIELD", "Portti");
	define("DB_NAME_FIELD", "Tietokannan nimi / DSN");
	define("DB_NAME_DESC", "Jos käytät tietokantaa kuten MySQL tai PostgreSQL ole hyvä ja anna <b>tietokannan nimi</b> mihin haluat ViArtin luovan taulukot. Tämän tietokannan pitää olla jo olemassa. Jos olet vain asentamassa testitarkoituksessa, yleensä on olemassa \"<b>test</b>\" tietokanta jota voit käyttää. Jos ei, ole hyvä ja luo esim tietokanta 'viart'. ");
	define("DB_USER_FIELD", "Käyttäjätunnus");
	define("DB_PASS_FIELD", "Salasana");
	define("DB_USER_PASS_DESC", "<b>Käyttäjätunnus</b> ja <b>Salasana</b> -Ole hyvä ja anna käyttäjätunnus ja salasana tietokannalle.Jos käytät paikallista konetta, käyttäjätunnus on tod.näk \"<b>root</b>\" ja salasana tyhjä. Tämä on ok testauksessa, mutta ei varsinaisessa asennuksessa!");
	define("DB_PERSISTENT_FIELD", "Jatkuva yhteys");
	define("DB_PERSISTENT_DESC", "MYSQL/Postgre jatkuva yhteys. Jos et tiedä mitä se tarkoittaa, on parempi olla koskematta tähän kohtaan");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "Luo tietokanta");
	define("DB_POPULATE_DESC", "Luo tietokanta ja rakenne");
	define("DB_TEST_DATA_FIELD", "Testidata");
	define("DB_TEST_DATA_DESC", "Tekee testidataa taulukkoon");
	define("ADMIN_EMAIL_FIELD", "Hallinnan sähköposti");
	define("ADMIN_LOGIN_FIELD", "Hallinan sisäänkirjaus");
	define("ADMIN_PASS_FIELD", "Hallinan salasana");
	define("ADMIN_CONF_FIELD", "Vahvista salasana");
	define("DATETIME_SHOWN_FIELD", "Päivämäärän näyttö (sivustolla)");
	define("DATE_SHOWN_FIELD", "Päiväyksen näyttö (sivustolla)");
	define("DATETIME_EDIT_FIELD", "Päiväyksen näyttö (muokatessa)");
	define("DATE_EDIT_FIELD", "Päivämäärän näyttö (muokatessa)");
	define("DATE_FORMAT_COLUMN", "Päiväysmuoto");
	define("CURRENT_DATE_COLUMN", "Nykyinen päivä");

	define("DB_LIBRARY_ERROR", "PHP toiminnot {db_library} eivät ole määriteltyinä. Tarkista tietokannan määritykset tiedostosta - php.ini.");
	define("DB_CONNECT_ERROR", "En saa yhteyttä tietokantaan, tarkista asetukset");
	define("INSTALL_FINISHED_ERROR", "Asennus on jo tehty!");
	define("WRITE_FILE_ERROR", "Tiedostoon<b>'includes/var_definition.php'</b>. Ei voi kirjoittaa. Vaihda asetukset ennen jatkamista");
	define("WRITE_DIR_ERROR", "Kansioon <b>'includes/'</b>. Ei voi kirjoittaa. Vaihda asetuksia ennen jatkamista");
	define("DUMP_FILE_ERROR", "Dumppitiedostoa '{file_name}' ei löytynyt");
	define("DB_TABLE_ERROR", "Taulukkoa '{table_name}' ei löytynyt. Tee tarvittavat muutokset tietokantaan.");
	define("TEST_DATA_ERROR", "Tarkia<b>{POPULATE_DB_FIELD}</b> ennen testitaulukkojen tekemistä");
	define("DB_HOST_ERROR", "Isäntänimeä jonka annoit ei löydy");
	define("DB_PORT_ERROR", "Yhteyttä tähän porttiin (MYSQL) ei voitu luoda");
	define("DB_USER_PASS_ERROR", "Salasana tai käyttäjätunnus ovat väärin");
	define("DB_NAME_ERROR", "Sisäänkirjaus onnistui, mutta tietokantaa '{db_name}' ei löytynyt");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP päivitys");
	define("UPGRADE_NOTE", "HUOM! Tee varmuuskopiot ennen jatkamista");
	define("UPGRADE_AVAILABLE_MSG", "Tietokannan päivitys saatavilla");
	define("UPGRADE_BUTTON", "Päivitä tietokanta versioon {version_number} nyt");
	define("CURRENT_VERSION_MSG", "Nyt asennettu versio");
	define("LATEST_VERSION_MSG", "Versio saatavilla");
	define("UPGRADE_RESULTS_MSG", "Päivityksen tulos");
	define("SQL_SUCCESS_MSG", "SQL kyselyt onnistuivat");
	define("SQL_FAILED_MSG", "SQL kyselyt epäonnistuivat");
	define("SQL_TOTAL_MSG", "SQL kyselyitä suoritettu");
	define("VERSION_UPGRADED_MSG", "Tietokantasi on päivitetty");
	define("ALREADY_LATEST_MSG", "Sinulla on jo viimeisin versio");
	define("DOWNLOAD_NEW_MSG", "Uusin versio havaittu");
	define("DOWNLOAD_NOW_MSG", "Lataa versio {version_number} nyt");
	define("DOWNLOAD_FOUND_MSG", "Huomasimme, että uusi versio {version_number} on saatavilla. Klikkaa linkkiä aloittaaksesi latauksen.ladattuasi ja päivitettyäsi, muista käynnistää päivitystoiminto uudelleen");
	define("NO_XML_CONNECTION", "Varoitus! Yhteyttä osoitteeseen 'http://www.viart.com/' ei voida luoda!");

?>