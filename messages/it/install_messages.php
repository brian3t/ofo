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
	define("INSTALL_TITLE", "Installazione di ViArt SHOP");

	define("INSTALL_STEP_1_TITLE", "Installazione: Primo Passo");
	define("INSTALL_STEP_1_DESC", "Grazie per aver scelta ViArt SHOP. Per contiuare l'installazione riempire tutti i dettagli sottostanti. Attenzione: il database selezionato deve esistere. Se installate il database usando ODBC, es. MS Access e' neccessario creare il DSN prima di procedere.");
	define("INSTALL_STEP_2_TITLE", "Installazione: Secondo Passo");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Installazione: Terzo Passo");
	define("INSTALL_STEP_3_DESC", "Selezionare un layout per il sito. Potra' comunque essere cambiato successivamente.");
	define("INSTALL_FINAL_TITLE", "Installazione: Passo Finale");
	define("SELECT_DATE_TITLE", "Seleziona il formato Data");

	define("DB_SETTINGS_MSG", "Settaggi Database");
	define("DB_PROGRESS_MSG", "Avanzamento del popolamento del database");
	define("SELECT_PHP_LIB_MSG", "Selezionare PHP library");
	define("SELECT_DB_TYPE_MSG", "Selezionare il tipo di Database");
	define("ADMIN_SETTINGS_MSG", "Impostazioni d'amministrazione");
	define("DATE_SETTINGS_MSG", "Formati Data");
	define("NO_DATE_FORMATS_MSG", "Nessun formato data disponibile");
	define("INSTALL_FINISHED_MSG", "L'installazione di base e' completa. Assicuratevi di aver controllato tutte le opzioni nella sezione di amministrazione e fate tutti i cambiamenti necessari.");
	define("ACCESS_ADMIN_MSG", "Per accedere alla sezione di amministrazione premere qui");
	define("ADMIN_URL_MSG", "URL per l'amministrazione");
	define("MANUAL_URL_MSG", "URL del Manuale");
	define("THANKS_MSG", "Grazie per aver scelto <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Tipo Database");
	define("DB_TYPE_DESC", "Selezionare <b>il tipo di database</b> che tu stai usando. Se usi SQL Server o Microsoft Access, seleziona ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP Library");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "Inserisci <b>il nome</b> o <b>indirizzo IP del server</b> sul quale il database ViArt funzionera'. Se il database funzionera' sul tuo PC locale , puoi lasciare \"<b>localhost</b>\" e il valore port in bianco. Se il database sara' fornito da terze parti, contattare l'amministratore di tale sistema.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Nome del database / DSN");
	define("DB_NAME_DESC", "Se stai usando un database come MySQL o PostgreSQL allora inserisci il <b>nome del database</b> dove desideri che ViArt crei le tabelle. Tale database deve gia' esistere Se stai installando ViArt in ambiente di test sul tuo PC locale, esiste un database di \"<b>test</b>\"  che puoi usare. Altrimenti crea un database vuoto chiamato ViArt. Se stai usando Microsoft Access o SQL Server allora il Nome del Database dovra' essere il <b>nome del DSN</b> che hai specificato nella sezione Data Sources (ODBC) del tuo Pannello di Controllo.");
	define("DB_USER_FIELD", "Nome Utente");
	define("DB_PASS_FIELD", "Password");
	define("DB_USER_PASS_DESC", "<b>Username</b> and <b>Password</b> - Inserire nome utente e password per accedere al database. Se state usando una installazione di test locale il nome utente sara' probabilmente \"<b>root</b>\" e la password probabilmente sara' vuota. Questo va bene per i test ma non e' sicuro su server in produzione.");
	define("DB_PERSISTENT_FIELD", "Persistent Connection");
	define("DB_PERSISTENT_DESC", "Per usre MySQL/Postgre persistent connections, selezionate questa casella. Se non sapete cosa significa, allora lasciarlo non selezionato e' la soluzione migliore.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "Popolare il DB");
	define("DB_POPULATE_DESC", "per creare la struttura delle tabelle del database e popolarle selezionare la checkbox");
	define("DB_TEST_DATA_FIELD", "Dati di prova");
	define("DB_TEST_DATA_DESC", "per aggiungere i dati di prova al database selezionare la checkbox");
	define("ADMIN_EMAIL_FIELD", "Indirizzo Email Amministratore");
	define("ADMIN_LOGIN_FIELD", "Nome Utente Amministratore");
	define("ADMIN_PASS_FIELD", "Password Amministratore");
	define("ADMIN_CONF_FIELD", "Conferma password");
	define("DATETIME_SHOWN_FIELD", "Formato Ora (mostrata sul sito)");
	define("DATE_SHOWN_FIELD", "Formato Data (mostrata sul sito)");
	define("DATETIME_EDIT_FIELD", "Formato Ora (per modifica)");
	define("DATE_EDIT_FIELD", "Formato Data (per modifica)");
	define("DATE_FORMAT_COLUMN", "Formato Data");
	define("CURRENT_DATE_COLUMN", "Data Attuale");

	define("DB_LIBRARY_ERROR", "Funzioni PHP per {db_library} non definite. Controlla nel file di configurazione php.ini i settaggi del database.");
	define("DB_CONNECT_ERROR", "Impossibile contattare il database. Controllare i parametri di configurazione.");
	define("INSTALL_FINISHED_ERROR", "Processo di installazione gia' completato");
	define("WRITE_FILE_ERROR", "Non hai i permessi di scrittura sul file <b>'includes/var_definition.php'</b>. Modificare i permessi prima di procedere.");
	define("WRITE_DIR_ERROR", "Non hai il permesso di scrittura nella cartella <b>'includes/'</b>. Cambiare i permessi della cartella prima di continuare.");
	define("DUMP_FILE_ERROR", "Dump file '{file_name}' non e' stato trovato.");
	define("DB_TABLE_ERROR", "La tabella '{table_name}' non e' stata trovata. Popolare il database con i dati necessari.");
	define("TEST_DATA_ERROR", "Controllare <b>{POPULATE_DB_FIELD}</b> prima di popolare le tabelle con il dati di prova.");
	define("DB_HOST_ERROR", "L'hostname specificato non e' stato trovato.");
	define("DB_PORT_ERROR", "Impossibile contattare MySQL server alla porta specificata.");
	define("DB_USER_PASS_ERROR", "Il nome utente o la password specificate non sono corrette.");
	define("DB_NAME_ERROR", "I dati di Login sono corretti, ma il database  '{db_name}' non esiste.");

	// upgrade messages
	define("UPGRADE_TITLE", "Aggiornamento di ViArt SHOP");
	define("UPGRADE_NOTE", "Nota: Si consiglia di eseguire un backup del database prima di procedere.");
	define("UPGRADE_AVAILABLE_MSG", "Aggiornamento database disponibile");
	define("UPGRADE_BUTTON", "Aggiorna il database alla {version_number} now");
	define("CURRENT_VERSION_MSG", "Versione attualmente installata");
	define("LATEST_VERSION_MSG", "Versione disponibile per l'installazione");
	define("UPGRADE_RESULTS_MSG", "Risultati dell'aggiornamento");
	define("SQL_SUCCESS_MSG", "SQL queries corretta");
	define("SQL_FAILED_MSG", "SQL queries fallita");
	define("SQL_TOTAL_MSG", "Totale SQL queries eseguite");
	define("VERSION_UPGRADED_MSG", "Il database e' stato aggiornato a");
	define("ALREADY_LATEST_MSG", "Hai gia' l'ultima versione");
	define("DOWNLOAD_NEW_MSG", "Una nuova versione e' stata rilevata");
	define("DOWNLOAD_NOW_MSG", "Scarica la versione {version_number} ora");
	define("DOWNLOAD_FOUND_MSG", "Rilevata nuova {version_number} versione disponibile per il download. Cliccare il link sottostante per iniziare il download. Dopo aver completato il download e rimpiazzato i file non dimenticare di rilanciare la routine di Upgrade.");
	define("NO_XML_CONNECTION", "Attenzione! Nessuna connessione a 'http://www.viart.com' disponibile!");

?>