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
	define("INSTALL_STEP_1_DESC", "Tack för att du har valt att installera ViArts webbutik. För att komma igång med installationen behöver du fylla i nedanstående obligatoriska fält. Vänligen se till att databasen du valt redan finns. Om du installerar till en databas som använder ODBC eller MS Access, så behöver du först skapa en DSN-koppling till den innan du fortsätter.");
	define("INSTALL_STEP_2_TITLE", "Installation: Steg 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Installation: Steg 3");
	define("INSTALL_STEP_3_DESC", "Vänligen välj en webbsides-layout. Du kan ändra layouten senare.");
	define("INSTALL_FINAL_TITLE", "Installation: Sista Steget");
	define("SELECT_DATE_TITLE", "Välj Datumformat");

	define("DB_SETTINGS_MSG", "Databasinställningar");
	define("DB_PROGRESS_MSG", "Skapar databasstrukturen");
	define("SELECT_PHP_LIB_MSG", "Välj PHP-bibliotek");
	define("SELECT_DB_TYPE_MSG", "Välj databastyp");
	define("ADMIN_SETTINGS_MSG", "Administrativa inställningar");
	define("DATE_SETTINGS_MSG", "Datumformat");
	define("NO_DATE_FORMATS_MSG", "Inget datumformat tillgängligt");
	define("INSTALL_FINISHED_MSG", "Grundinstallationen är genomförd. Var vänlig se till att alla inställningar i adminstrationssektionen är som de ska.");
	define("ACCESS_ADMIN_MSG", "För att komma åt administrationen, klicka här");
	define("ADMIN_URL_MSG", "Administrations-URL");
	define("MANUAL_URL_MSG", "Manual-URL");
	define("THANKS_MSG", "Tack för att du valt <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Databastyp");
	define("DB_TYPE_DESC", "Var vänlig välj vilken <b>typ av databas</b> som du använder. Om du använder SQL Server eller Microsoft Access, var vänlig välj ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP-bibliotek");
	define("DB_HOST_FIELD", "Domän");
	define("DB_HOST_DESC", "Var vänlig ange <b>namn</b> eller <b>IP-adress för servern</b> där din ViArt databas kommer köras. Om du kör din databas på din lokala PC så kan du troligtvis enbart låta det stå \"<b>localhost</b>\" och lämna fältet för porten tomt. Om du använder en databas hos din webhost, var vänlig kontrollera uppgifterna för serverinställningarna med dem.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Databasnamn / DSN");
	define("DB_NAME_DESC", "Om du använder en databas som t ex MySQL eller PostgreSQL var vänlig ange <b>namnet på databasen</b> där du vill att ViArt ska skapa tabeller. Det måste vara en befintlig databas. Om du bara installerar ViArt i prövosyfte på din lokala PC så har de flesta system en \"<b>test</b>\"-databas som du kan använda. Om det inte finns någon sådan så bör du skapa en databas som du t ex döper till \"viart\" och sedan använda den. Om du använder Microsoft Access eller SQL Server så bör databasnamnet vara <b>DSN-namnet</b> som du har angett i Data Sources (ODBC) delen av din kontrollpanel.");
	define("DB_USER_FIELD", "Användarnamn");
	define("DB_PASS_FIELD", "Lösenord");
	define("DB_USER_PASS_DESC", "<b>Användarnamn</b> och <b>lösenord</b> - var vänlig ange användarnamn och lösenord som du vill använda för åtkomst av databasen. Om du använde ren lokal testinstallation så är användarnamnet troligtvis \"<b>root</b>\" och lösenordsfältet lämnas tomt. Det är helt okej när man testar, men var vänlig notera att det inte är säkert på produktionsservrar.");
	define("DB_PERSISTENT_FIELD", "Persistent Connection");
	define("DB_PERSISTENT_DESC", "För att använda MySQL eller Postgre persistent connections, bocka i denna ruta. Om du inte vet vad det innebär så är det säkerligen bäst att lämna rutan oklickad.");
	define("DB_CREATE_DB_FIELD", "Skapa databas");
	define("DB_CREATE_DB_DESC", "För att om möjligt skapa en databas, bocka i rutan. Fungerar bara för MySQL och Postgre");
	define("DB_POPULATE_FIELD", "Fyll databasen");
	define("DB_POPULATE_DESC", "För att skapa databasens tabellstruktur och fylla den med data bocka i denna rutan.");
	define("DB_TEST_DATA_FIELD", "Testdata");
	define("DB_TEST_DATA_DESC", "För att lägga till lite testdata i din databas bocka i rutan.");
	define("ADMIN_EMAIL_FIELD", "Administrationsepost");
	define("ADMIN_LOGIN_FIELD", "Administration användarnamn");
	define("ADMIN_PASS_FIELD", "Administration lösenord");
	define("ADMIN_CONF_FIELD", "Bekräfta lösenord");
	define("DATETIME_SHOWN_FIELD", "Tidsformat (visad på webbplatsen)");
	define("DATE_SHOWN_FIELD", "Datumformat (visad på webbplatsen)");
	define("DATETIME_EDIT_FIELD", "Tidsformat (för ändring)");
	define("DATE_EDIT_FIELD", "Datumformat (för ändring)");
	define("DATE_FORMAT_COLUMN", "Datumformat");
	define("CURRENT_DATE_COLUMN", "Dagens datum");

	define("DB_LIBRARY_ERROR", "PHP-funktioner för {db_library} är inte angivna. Var vänlig kontrollera dina databasinställningar i din konfigurationsfil - php.ini.");
	define("DB_CONNECT_ERROR", "Kan inte koppla mot databasen. Vänligen kolla dina databas-parametrar.");
	define("INSTALL_FINISHED_ERROR", "Installationsprocessen är färdig.");
	define("WRITE_FILE_ERROR", "Har inte skrivrättigheter till filen <b>'includes/var_definition.php'</b>. Vänligen gör ändringar för skrivrättigheter på filen innan du fortsätter.");
	define("WRITE_DIR_ERROR", "Har inte skrivrättigheter till katalogen <b>'includes/'</b>. Vänligen ändra katalogens rättigheter innan du fortsätter.");
	define("DUMP_FILE_ERROR", "Dump-filen '{file_name}' hittades inte.");
	define("DB_TABLE_ERROR", "Tabellen '{table_name}' hittades inte. Vänligen fyll databasen med nödvändig information.");
	define("TEST_DATA_ERROR", "Klicka i <b>{POPULATE_DB_FIELD}</b> innan du fyller tabeller med testdata");
	define("DB_HOST_ERROR", "Hostnamnet som du angav kan inte hittas.");
	define("DB_PORT_ERROR", "Kan inte ansluta till databasservern via den angivna porten.");
	define("DB_USER_PASS_ERROR", "Användarnamnet eller lösenordet som du har angett är inte rätt.");
	define("DB_NAME_ERROR", "Inloggningsuppgifterna var rätt, men databasen '{db_name}' kunde inte hittas.");

	// upgrade messages
	define("UPGRADE_TITLE", "ViaArt Shop - Uppgradering");
	define("UPGRADE_NOTE", "OBS: Vänligen tänk på att göra en backup av databasen innan du fortsätter med uppgraderingen.");
	define("UPGRADE_AVAILABLE_MSG", "Uppgradering tillgänglig");
	define("UPGRADE_BUTTON", "Uppgradera till {version_number} nu");
	define("CURRENT_VERSION_MSG", "Din nuvarande version");
	define("LATEST_VERSION_MSG", "Version tillgänglig att installera");
	define("UPGRADE_RESULTS_MSG", "Uppgraderingsresultat");
	define("SQL_SUCCESS_MSG", "SQL-queries - lyckade");
	define("SQL_FAILED_MSG", "SQL-queries - misslyckade");
	define("SQL_TOTAL_MSG", "Totalt antal SQL-queries körda");
	define("VERSION_UPGRADED_MSG", "Din version har uppgraderas till");
	define("ALREADY_LATEST_MSG", "Du har redan den senaste versionen");
	define("DOWNLOAD_NEW_MSG", "Den nya versionen hittades");
	define("DOWNLOAD_NOW_MSG", "Ladda ner version nr {version_number} nu");
	define("DOWNLOAD_FOUND_MSG", "Vi har upptäckt att den nya {version_number} versionen är tillgäglig för nedladdning. Var vänlig klicka på länken nedanför för att starta nedladdningen. Efter att nedladdningen är klar och filerna är utbytta, glöm inte att köra uppgraderingsrutinen igen.");
	define("NO_XML_CONNECTION", "Varning! Ingen uppkoppling till 'http://www.viart.com/' är tillgänglig!");

?>