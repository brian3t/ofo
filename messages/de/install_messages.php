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
	define("INSTALL_TITLE", "ViArt SHOP Installation");

	define("INSTALL_STEP_1_TITLE", "Installation: Schritt 1");
	define("INSTALL_STEP_1_DESC", "Vielen Dank, dass Sie sich für ViArt SHOP entschieden haben. Damit die Installation abgeschlossen werden kann, machen Sie bitte die folgenden Angaben. Vergewissern Sie sich, dass bereits eine Datenbank existiert. Wenn Sie in eine ODBC-Datenbank installieren, z.B. MS Access, sollten Sie dafür zunächst ein DSN erzeugen.");
	define("INSTALL_STEP_2_TITLE", "Installation: Schritt 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Installation: Schritt 3");
	define("INSTALL_STEP_3_DESC", "Bitte wählen Sie ein Seitenlayout. Sie können das Layout später ändern.");
	define("INSTALL_FINAL_TITLE", "Installation: Abschluss");
	define("SELECT_DATE_TITLE", "Datumsformat auswählen");

	define("DB_SETTINGS_MSG", "Datenbank-Einstellungen");
	define("DB_PROGRESS_MSG", "Fortschritt Datenbankstruktur bestücken");
	define("SELECT_PHP_LIB_MSG", "PHP-Bibliothek wählen");
	define("SELECT_DB_TYPE_MSG", "Datenbank-Typ wählen");
	define("ADMIN_SETTINGS_MSG", "Administrations-Einstellungen");
	define("DATE_SETTINGS_MSG", "Datumsformate");
	define("NO_DATE_FORMATS_MSG", "Keine Datumsformate verfügbar");
	define("INSTALL_FINISHED_MSG", "Die Basisinstallation ist nun abgeschlossen. Überprüfen Sie nun die Einstellungen im Administrationsbereich und nehmen Sie ggf. die notwendigen Anpassungen vor.");
	define("ACCESS_ADMIN_MSG", "Um in den Administrationsbereich zu gelangen, klicken Sie bitte hier.");
	define("ADMIN_URL_MSG", "Administration-URL");
	define("MANUAL_URL_MSG", "Manual-URL");
	define("THANKS_MSG", "Danke, dass Sie <b>ViArt SHOP</b> gewählt haben.");

	define("DB_TYPE_FIELD", "Datenbank-Typ");
	define("DB_TYPE_DESC", "Bitte wählen Sie den Datenbanktyp, den Sie benutzen. Wenn Sie SQL Server oder Microsoft Access benutzen, wählen Sie bitte ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP-Bibliothek");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "Geben Sie bitte die IP-Adresse des Servers ein, auf dem Ihre ViArt-Datenbank läuft. Wenn die Datenbank auf Ihrem lokalen PC läuft, können Sie die Einstellung bei \"localhost\" mit leerer Portnummer belassen. Wenn Sie eine Datenbank Ihres Hosters benutzen, entnehmen Sie bitte die Servereinstellungen der Dokumentation Ihres Hosters.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Datenbank-Name/DSN");
	define("DB_NAME_DESC", "Wenn Sie eine Datenbank wie MySQL oder PostgreSQL  verwenden, geben Sie bitte den Namen der Datenbank ein, in der ViArt seine Tabellen anlegen soll. Diese Datenbank muss bereits existieren. Wenn Sie ViArt zu Testzwecken auf Ihrem lokalen PC installieren: Viele Systeme haben bereits eine \"test\"-Datenbank, die Sie benutzen können. Wenn nicht, legen Sie bitte eine Datenbank an, z.B. \"viart\", und verwenden Sie diese. Wenn Sie Microsoft Access oder SQL Server benutzen, sollte der Datenbank-Name dem DSN entsprechen, den Sie in der ODBC-Sektion Ihrer Systemsteuerung angelegt haben.");
	define("DB_USER_FIELD", "Benutzername");
	define("DB_PASS_FIELD", "Passwort");
	define("DB_USER_PASS_DESC", "Benutzername und Passwort - geben Sie bitte Benutzername und Passwort für den Datenbankzugriff an. Wenn Sie eine lokale Testinstallation benutzen, wird der Benutzername vermutlich \"root\" sein mit leerem Passwort. Zu Testzwecken ist dies ausreichend. Auf Produktionsservern ist dies jedoch sehr unsicher.");
	define("DB_PERSISTENT_FIELD", "Persistente Verbindung");
	define("DB_PERSISTENT_DESC", "Um persistente MySQL//Postgre-Verbindungen zu benutzen, markieren Sie bitte die Checkbox. Wenn Sie nicht wissen, was das bedeutet, lassen Sie die Box am besten unmarkiert.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "DB bestücken");
	define("DB_POPULATE_DESC", "um die Datenbank-Tabellenstruktur zu erzeugen und mit Daten zu bestücken, markieren Sie die Checkbox.");
	define("DB_TEST_DATA_FIELD", "Testdaten");
	define("DB_TEST_DATA_DESC", "um Testdaten zur Datenbank hinzuzufügen, markieren Sie bitte die Checkbox");
	define("ADMIN_EMAIL_FIELD", "Administrator E-Mail");
	define("ADMIN_LOGIN_FIELD", "Administrator Anmeldung");
	define("ADMIN_PASS_FIELD", "Administrator Passwort");
	define("ADMIN_CONF_FIELD", "Passwort bestätigen");
	define("DATETIME_SHOWN_FIELD", "Datum/Zeit-Format (auf Seite angezeigt)");
	define("DATE_SHOWN_FIELD", "Datumsformat (auf Seite angezeigt)");
	define("DATETIME_EDIT_FIELD", "Datum/Zeit-Format (bei Bearbeitung)");
	define("DATE_EDIT_FIELD", "Datumsformat (bei Bearbeitung)");
	define("DATE_FORMAT_COLUMN", "Datumsformat");
	define("CURRENT_DATE_COLUMN", "Aktuelles Datum");

	define("DB_LIBRARY_ERROR", "PHP-Funktionen für {db_library} sind nicht definiert. Bitte überprüfen Sie die Datenbank-Einstellung in der Konfigurationsdatei php.ini.");
	define("DB_CONNECT_ERROR", "Kann nicht mit der Datenbank verbinden. Bitte überprüfen Sie Ihre Datenbank-Parameter.");
	define("INSTALL_FINISHED_ERROR", "Installation bereits abgeschlossen.");
	define("WRITE_FILE_ERROR", "Kein Schreibrechte auf Datei <b>'includes/var_definition.php'</b>. Zum Fortfahren ändern Sie bitte die Berechtigungen.");
	define("WRITE_DIR_ERROR", "Kein Schreibrechte auf Ordner <b>'includes/'</b>. Zum Fortfahren ändern Sie bitte die Berechtigungen.");
	define("DUMP_FILE_ERROR", "Dump-Datei '{file_name}' wurde nicht gefunden.");
	define("DB_TABLE_ERROR", "Tabelle '{table_name}' wurde nicht gefunden. Bitte bestücken Sie die Datenbank mit den notwendigen Daten.");
	define("TEST_DATA_ERROR", "Überprüfen Sie <b>{POPULATE_DB_FIELD}</b> bevor Sie die Tabellen mit Testdaten bestücken.");
	define("DB_HOST_ERROR", "Der angegebene Hostname wurde nicht gefunden.");
	define("DB_PORT_ERROR", "Kann nicht mit dem MySQL-Server auf dem angegeben Port verbinden.");
	define("DB_USER_PASS_ERROR", "Angebener Benutzername oder Passwort nicht korrekt.");
	define("DB_NAME_ERROR", "Die Anmeldedaten sind korrekt, aber die Datenbank '{db_name}' wurde nicht gefunden.");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP Aktualisierung");
	define("UPGRADE_NOTE", "Hinweis: Bitte führen Sie eine Datensicherung Ihrer Datenbank durch, bevor Sie fortfahren.");
	define("UPGRADE_AVAILABLE_MSG", "Datenbank-Aktualisierung verfügbar");
	define("UPGRADE_BUTTON", "Aktualisiere Datenbank nun auf {version_number}");
	define("CURRENT_VERSION_MSG", "Aktuell installierte Version");
	define("LATEST_VERSION_MSG", "Version verfügbar zur Installation");
	define("UPGRADE_RESULTS_MSG", "Aktualisierungs-Ergebnisse");
	define("SQL_SUCCESS_MSG", "SQL-Abfragen erfolgreich");
	define("SQL_FAILED_MSG", "SQL-Abfrage gescheitert");
	define("SQL_TOTAL_MSG", "SQL-Abfragen insgesamt ausgeführt");
	define("VERSION_UPGRADED_MSG", "Ihre Datenbank wurde aktualisiert auf");
	define("ALREADY_LATEST_MSG", "Sie haben bereits die neueste Version");
	define("DOWNLOAD_NEW_MSG", "Die neue Version wurde gefunden");
	define("DOWNLOAD_NOW_MSG", "Laden Sie Version {version_number} nun herunter");
	define("DOWNLOAD_FOUND_MSG", "Es wurde festgestellt, dass die neue Version {version_number} zum Download bereit steht. Bitte klicken Sie auf den Link unten um den Download zu starten. Nach Abschluss des Downloads und Ersetzen der Dateien vergessen Sie bitte nicht, die Upgrade-Routine noch einmal auszuführen.");
	define("NO_XML_CONNECTION", "Warnung! Keine Verbindung zu 'http://www.viart.com' verfügbar");

?>