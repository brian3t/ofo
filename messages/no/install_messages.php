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


	// installeringsmeldinger
	define("INSTALL_TITLE", "ViArt SHOP installering");

	define("INSTALL_STEP_1_TITLE", "Installering: Steg 1");
	define("INSTALL_STEP_1_DESC", "Takk for at du har valgt ViArt SHOP. For å fortsette installeingen, vennligst fyll ut de påkrevde opplysningene. Databasen du velger må allerede ha blitt opprettet. Hvis du installerer i en database som bruker ODBC eller MS Access (o. l.) bør du opprette en DSN før du fortsetter.  ");
	define("INSTALL_STEP_2_TITLE", "Installering: Steg 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Installering: Steg 3");
	define("INSTALL_STEP_3_DESC", "Vennligst velg et sideoppsett. Du kan forandre oppsettet senere.");
	define("INSTALL_FINAL_TITLE", "Installering: Fullført");
	define("SELECT_DATE_TITLE", "Velg datoformat");

	define("DB_SETTINGS_MSG", "Database innstillinger");
	define("DB_PROGRESS_MSG", "Database strukturframgang");
	define("SELECT_PHP_LIB_MSG", "Velg PHP Library");
	define("SELECT_DB_TYPE_MSG", "Velg database type");
	define("ADMIN_SETTINGS_MSG", "Administrative innstillinger");
	define("DATE_SETTINGS_MSG", "Datoformat");
	define("NO_DATE_FORMATS_MSG", "Ingen datoformat er tilgjengelig");
	define("INSTALL_FINISHED_MSG", "Nå er dine grunnleggende innstillinger fullført. Husk å fullføre innstillingene i administrasjonssiden etter eget ønske.");
	define("ACCESS_ADMIN_MSG", "Trykk her for å få tilgang til administrasjonssiden.");
	define("ADMIN_URL_MSG", "URL til administrasjonen.");
	define("MANUAL_URL_MSG", "Manuell URL");
	define("THANKS_MSG", "Takk for at du valgte <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Databasetype");
	define("DB_TYPE_DESC", "Vennligst velg <b>databasetype</b> som du bruker. Hvis du bruker SQL Server eller Microsoft Access, så velg ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP Library");
	define("DB_HOST_FIELD", "Hostname / Vertsnavn");
	define("DB_HOST_DESC", "Vennligst tast inn <b>navn</b> eller <b>IP addresse til serveren</b> som du vil ViArt databasen skal bruke. Hvis databasen kjører fra din egen PC så kan du la den være \"<b>localhost</b>\" og resten kan stå blank. Hvis du benytter deg av en database fra en host eller vert, så må du henvende deg til det selskapets retningslinjer for serverinnstillinger.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Databasenavn / DSN");
	define("DB_NAME_DESC", "Hvis du bruker en database som MySQL eller PostgreSQL,  så må du oppgi <b>navnet på databasen</b> som du vil ViArt skal opprette sine tabeller i. Denne databasen må være opprettet på forhånd. Hvis du installerer ViArt kun for å teste det på din PC, så har de fleste systemene en \"<b>test</b>\"database du kan bruke. Hvis ikke, så kan du opprette en database som for eksempel \"viart\"og bruke den. Hvis du bruker  Microsoft Access eller SQL Server så bør databasenavnet være det samme som <b>name of the DSN</b> som du har satt opp i Data Sources (datakilder) (ODBC) delen i kontrollpanelet (Control Panel) ditt.");
	define("DB_USER_FIELD", "Brukernavn");
	define("DB_PASS_FIELD", "Passord");
	define("DB_USER_PASS_DESC", "<b>Brukernavn</b> og <b>Passord</b> - vennligst tast inn brukernavnet og passordet du vil bruke for å få tilgang til databasen. Hvis du benytter deg av lokal prøveinstallering så er brukernavnet sannsynligvis \"<b>root</b>\" og det er sannsynligvis ingen passord. Dette går greit under testingen, men husk at det ikke er trygt å bruke disse innstillingene på en server.");
	define("DB_PERSISTENT_FIELD", "Varig forbindelse");
	define("DB_PERSISTENT_DESC", "Trykk her for å bruke MySQL eller Postgre varig forbindelse. Hvis du ikke vet hva det betyr, er det best å la denne boksen stå umerket.");
	define("DB_CREATE_DB_FIELD", "Opprett DB");
	define("DB_CREATE_DB_DESC", "Merk denne boksen for å opprette en database hvis det er mulig. Virker kun for MySQL og Postgre ");
	define("DB_POPULATE_FIELD", "Fyll DB");
	define("DB_POPULATE_DESC", "Trykk denne boksen for å opprette tabellstrukturen til databasen og lagre data i den");
	define("DB_TEST_DATA_FIELD", "Prøvedata");
	define("DB_TEST_DATA_DESC", "Trykk denne boksen for å lagre prøvedata i databasen din");
	define("ADMIN_EMAIL_FIELD", "E-mail til administrator");
	define("ADMIN_LOGIN_FIELD", "Innlogging for administrator");
	define("ADMIN_PASS_FIELD", "Administrator passord");
	define("ADMIN_CONF_FIELD", "Bekreft passord");
	define("DATETIME_SHOWN_FIELD", "Datotid format (vist på siden)");
	define("DATE_SHOWN_FIELD", "Datoformat (vist på siden)");
	define("DATETIME_EDIT_FIELD", "Datotid format (for redigering)");
	define("DATE_EDIT_FIELD", "Datoformat (for redigering)");
	define("DATE_FORMAT_COLUMN", "Datoformat");
	define("CURRENT_DATE_COLUMN", "Dato (nåværende)");

	define("DB_LIBRARY_ERROR", "PHP funksjonene for {db_library} er ikke definert. Vennligst sjekk databaseinntillingene i konfigurasjonsfilen - php.ini.");
	define("DB_CONNECT_ERROR", "Kan ikke koble til databasen. Vennligst sjekk databaseparametrene dine.");
	define("INSTALL_FINISHED_ERROR", "Installeringsprosessen er allerede fullført.");
	define("WRITE_FILE_ERROR", "Har ikke tillatelse til å forandre filen <b>'includes/var_definition.php'</b>. Vennligst juster innstillingene før du fortsetter.");
	define("WRITE_DIR_ERROR", "Har ikke tillatelse til å forandre mappen <b>'includes/'</b>. Vennligst juster mappeinnstillingene før du fortsetter.");
	define("DUMP_FILE_ERROR", "Dumpingsfilnavnet'{file_name}' ble ikke funnet.");
	define("DB_TABLE_ERROR", "Tabellen '{table_name}' ble ikke funnet. Vennligst fyll inn nødvendig data i tabellen.");
	define("TEST_DATA_ERROR", "Sjekk <b>{POPULATE_DB_FIELD}</b> før du skriver inn prøvedata i tabellen");
	define("DB_HOST_ERROR", "Vertsnavnet (hostname) som du spesifiserte kan ikke bli funnet.");
	define("DB_PORT_ERROR", "Kan ikke koble til databaseserveren via denne proten.");
	define("DB_USER_PASS_ERROR", "Brukernavnet eller passordet som du oppga er ikke korrekt.");
	define("DB_NAME_ERROR", "Innloggingsjusteringene er riktige, men databasenavnet '{db_name}' kan ikke bli funnet.");

	// oppgraderingsmeldinger
	define("UPGRADE_TITLE", "ViArt SHOP oppgradering");
	define("UPGRADE_NOTE", "Merk: Du bør ta en sikkerhetskopi av databasen før du fortsetter.");
	define("UPGRADE_AVAILABLE_MSG", "Databaseoppgradering er tilgjengelig");
	define("UPGRADE_BUTTON", "Oppgrader databasen til '{db_name}' nå");
	define("CURRENT_VERSION_MSG", "Installert vesjon");
	define("LATEST_VERSION_MSG", "Versjon tilgjengelig for installering");
	define("UPGRADE_RESULTS_MSG", "Oppgraderingsresultater");
	define("SQL_SUCCESS_MSG", "SQL forespørsel vellykket");
	define("SQL_FAILED_MSG", "SQL forespørsel mislykket");
	define("SQL_TOTAL_MSG", "Alle SQL forespørsler som er foretatt");
	define("VERSION_UPGRADED_MSG", "Databasen din har blitt oppgradert til");
	define("ALREADY_LATEST_MSG", "Du har allerede den nyeste versjonen");
	define("DOWNLOAD_NEW_MSG", "Den nye versjonen har blitt funnet");
	define("DOWNLOAD_NOW_MSG", "Last ned versjon {version_number} nå");
	define("DOWNLOAD_FOUND_MSG", "Den nye versjonen {version_number} er nå tilgjengelig for nedlasting. Trykk på lenken under for å starte nedlastingen. Ikke glem å kjøre \"Upgrade routine\" etter at nedlastingen er komplett og filene har blitt erstattet. ");
	define("NO_XML_CONNECTION", "Advarsel! Ingen forbindelse til 'http://www.viart.com/' er tilgjengelig!");

?>