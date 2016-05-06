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
	define("INSTALL_TITLE", "ViArts webbutik - Installation");

	define("INSTALL_STEP_1_TITLE", "Installation: Steg 1");
	define("INSTALL_STEP_1_DESC", "Tack f�r att du har valt att installera ViArts webbutik. F�r att komma ig�ng med installationen beh�ver du fylla i nedanst�ende obligatoriska f�lt. V�nligen se till att databasen du valt redan finns. Om du installerar till en databas som anv�nder ODBC eller MS Access, s� beh�ver du f�rst skapa en DSN-koppling till den innan du forts�tter.");
	define("INSTALL_STEP_2_TITLE", "Installation: Steg 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Installation: Steg 3");
	define("INSTALL_STEP_3_DESC", "V�nligen v�lj en webbsides-layout. Du kan �ndra layouten senare.");
	define("INSTALL_FINAL_TITLE", "Installation: Sista Steget");
	define("SELECT_DATE_TITLE", "V�lj Datumformat");

	define("DB_SETTINGS_MSG", "Databasinst�llningar");
	define("DB_PROGRESS_MSG", "Skapar databasstrukturen");
	define("SELECT_PHP_LIB_MSG", "V�lj PHP-bibliotek");
	define("SELECT_DB_TYPE_MSG", "V�lj databastyp");
	define("ADMIN_SETTINGS_MSG", "Administrativa inst�llningar");
	define("DATE_SETTINGS_MSG", "Datumformat");
	define("NO_DATE_FORMATS_MSG", "Inget datumformat tillg�ngligt");
	define("INSTALL_FINISHED_MSG", "Grundinstallationen �r genomf�rd. Var v�nlig se till att alla inst�llningar i adminstrationssektionen �r som de ska.");
	define("ACCESS_ADMIN_MSG", "F�r att komma �t administrationen, klicka h�r");
	define("ADMIN_URL_MSG", "Administrations-URL");
	define("MANUAL_URL_MSG", "Manual-URL");
	define("THANKS_MSG", "Tack f�r att du valt <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Databastyp");
	define("DB_TYPE_DESC", "Var v�nlig v�lj vilken <b>typ av databas</b> som du anv�nder. Om du anv�nder SQL Server eller Microsoft Access, var v�nlig v�lj ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP-bibliotek");
	define("DB_HOST_FIELD", "Dom�n");
	define("DB_HOST_DESC", "Var v�nlig ange <b>namn</b> eller <b>IP-adress f�r servern</b> d�r din ViArt databas kommer k�ras. Om du k�r din databas p� din lokala PC s� kan du troligtvis enbart l�ta det st� \"<b>localhost</b>\" och l�mna f�ltet f�r porten tomt. Om du anv�nder en databas hos din webhost, var v�nlig kontrollera uppgifterna f�r serverinst�llningarna med dem.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Databasnamn / DSN");
	define("DB_NAME_DESC", "Om du anv�nder en databas som t ex MySQL eller PostgreSQL var v�nlig ange <b>namnet p� databasen</b> d�r du vill att ViArt ska skapa tabeller. Det m�ste vara en befintlig databas. Om du bara installerar ViArt i pr�vosyfte p� din lokala PC s� har de flesta system en \"<b>test</b>\"-databas som du kan anv�nda. Om det inte finns n�gon s�dan s� b�r du skapa en databas som du t ex d�per till \"viart\" och sedan anv�nda den. Om du anv�nder Microsoft Access eller SQL Server s� b�r databasnamnet vara <b>DSN-namnet</b> som du har angett i Data Sources (ODBC) delen av din kontrollpanel.");
	define("DB_USER_FIELD", "Anv�ndarnamn");
	define("DB_PASS_FIELD", "L�senord");
	define("DB_USER_PASS_DESC", "<b>Anv�ndarnamn</b> och <b>l�senord</b> - var v�nlig ange anv�ndarnamn och l�senord som du vill anv�nda f�r �tkomst av databasen. Om du anv�nde ren lokal testinstallation s� �r anv�ndarnamnet troligtvis \"<b>root</b>\" och l�senordsf�ltet l�mnas tomt. Det �r helt okej n�r man testar, men var v�nlig notera att det inte �r s�kert p� produktionsservrar.");
	define("DB_PERSISTENT_FIELD", "Persistent Connection");
	define("DB_PERSISTENT_DESC", "F�r att anv�nda MySQL eller Postgre persistent connections, bocka i denna ruta. Om du inte vet vad det inneb�r s� �r det s�kerligen b�st att l�mna rutan oklickad.");
	define("DB_CREATE_DB_FIELD", "Skapa databas");
	define("DB_CREATE_DB_DESC", "F�r att om m�jligt skapa en databas, bocka i rutan. Fungerar bara f�r MySQL och Postgre");
	define("DB_POPULATE_FIELD", "Fyll databasen");
	define("DB_POPULATE_DESC", "F�r att skapa databasens tabellstruktur och fylla den med data bocka i denna rutan.");
	define("DB_TEST_DATA_FIELD", "Testdata");
	define("DB_TEST_DATA_DESC", "F�r att l�gga till lite testdata i din databas bocka i rutan.");
	define("ADMIN_EMAIL_FIELD", "Administrationsepost");
	define("ADMIN_LOGIN_FIELD", "Administration anv�ndarnamn");
	define("ADMIN_PASS_FIELD", "Administration l�senord");
	define("ADMIN_CONF_FIELD", "Bekr�fta l�senord");
	define("DATETIME_SHOWN_FIELD", "Tidsformat (visad p� webbplatsen)");
	define("DATE_SHOWN_FIELD", "Datumformat (visad p� webbplatsen)");
	define("DATETIME_EDIT_FIELD", "Tidsformat (f�r �ndring)");
	define("DATE_EDIT_FIELD", "Datumformat (f�r �ndring)");
	define("DATE_FORMAT_COLUMN", "Datumformat");
	define("CURRENT_DATE_COLUMN", "Dagens datum");

	define("DB_LIBRARY_ERROR", "PHP-funktioner f�r {db_library} �r inte angivna. Var v�nlig kontrollera dina databasinst�llningar i din konfigurationsfil - php.ini.");
	define("DB_CONNECT_ERROR", "Kan inte koppla mot databasen. V�nligen kolla dina databas-parametrar.");
	define("INSTALL_FINISHED_ERROR", "Installationsprocessen �r f�rdig.");
	define("WRITE_FILE_ERROR", "Har inte skrivr�ttigheter till filen <b>'includes/var_definition.php'</b>. V�nligen g�r �ndringar f�r skrivr�ttigheter p� filen innan du forts�tter.");
	define("WRITE_DIR_ERROR", "Har inte skrivr�ttigheter till katalogen <b>'includes/'</b>. V�nligen �ndra katalogens r�ttigheter innan du forts�tter.");
	define("DUMP_FILE_ERROR", "Dump-filen '{file_name}' hittades inte.");
	define("DB_TABLE_ERROR", "Tabellen '{table_name}' hittades inte. V�nligen fyll databasen med n�dv�ndig information.");
	define("TEST_DATA_ERROR", "Klicka i <b>{POPULATE_DB_FIELD}</b> innan du fyller tabeller med testdata");
	define("DB_HOST_ERROR", "Hostnamnet som du angav kan inte hittas.");
	define("DB_PORT_ERROR", "Kan inte ansluta till databasservern via den angivna porten.");
	define("DB_USER_PASS_ERROR", "Anv�ndarnamnet eller l�senordet som du har angett �r inte r�tt.");
	define("DB_NAME_ERROR", "Inloggningsuppgifterna var r�tt, men databasen '{db_name}' kunde inte hittas.");

	// upgrade messages
	define("UPGRADE_TITLE", "ViaArt Shop - Uppgradering");
	define("UPGRADE_NOTE", "OBS: V�nligen t�nk p� att g�ra en backup av databasen innan du forts�tter med uppgraderingen.");
	define("UPGRADE_AVAILABLE_MSG", "Uppgradering tillg�nglig");
	define("UPGRADE_BUTTON", "Uppgradera till {version_number} nu");
	define("CURRENT_VERSION_MSG", "Din nuvarande version");
	define("LATEST_VERSION_MSG", "Version tillg�nglig att installera");
	define("UPGRADE_RESULTS_MSG", "Uppgraderingsresultat");
	define("SQL_SUCCESS_MSG", "SQL-queries - lyckade");
	define("SQL_FAILED_MSG", "SQL-queries - misslyckade");
	define("SQL_TOTAL_MSG", "Totalt antal SQL-queries k�rda");
	define("VERSION_UPGRADED_MSG", "Din version har uppgraderas till");
	define("ALREADY_LATEST_MSG", "Du har redan den senaste versionen");
	define("DOWNLOAD_NEW_MSG", "Den nya versionen hittades");
	define("DOWNLOAD_NOW_MSG", "Ladda ner version nr {version_number} nu");
	define("DOWNLOAD_FOUND_MSG", "Vi har uppt�ckt att den nya {version_number} versionen �r tillg�glig f�r nedladdning. Var v�nlig klicka p� l�nken nedanf�r f�r att starta nedladdningen. Efter att nedladdningen �r klar och filerna �r utbytta, gl�m inte att k�ra uppgraderingsrutinen igen.");
	define("NO_XML_CONNECTION", "Varning! Ingen uppkoppling till 'http://www.viart.com/' �r tillg�nglig!");

?>