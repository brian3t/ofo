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


	// mesaje instalare
	define("INSTALL_TITLE", "Instalare ViArt SHOP");

	define("INSTALL_STEP_1_TITLE", "Instalare: Pasul 1");
	define("INSTALL_STEP_1_DESC", "Multumim pentru ca ati ales ViArt SHOP. Pentru a continua instalarea, va rugam completati detaliile cerute mai jos. Va rugam retineti ca baza de date selectata ar trebui sa existe deja. Daca instalati intr-o baza de dare care foloseste ODBC, de ex. MS Access ar trebui sa creati mai intai un DSN inainte de a continua.");
	define("INSTALL_STEP_2_TITLE", "Instalare: Pasul 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Instalare: Pasul 3");
	define("INSTALL_STEP_3_DESC", "Va rugam selectati un design pentru site. Veti putea sa schimbati designul ulterior.");
	define("INSTALL_FINAL_TITLE", "Instalare: Final");
	define("SELECT_DATE_TITLE", "Selectare format data");

	define("DB_SETTINGS_MSG", "Setari baza de date");
	define("DB_PROGRESS_MSG", "Progres populare structura baza de date");
	define("SELECT_PHP_LIB_MSG", "Selectati biblioteca PHP");
	define("SELECT_DB_TYPE_MSG", "Selectati tipul bazei de date");
	define("ADMIN_SETTINGS_MSG", "Setari administrare");
	define("DATE_SETTINGS_MSG", "Formate data");
	define("NO_DATE_FORMATS_MSG", "Nu sunt disponibile formate data");
	define("INSTALL_FINISHED_MSG", "In acest moment instalarea de baza este finalizata. Va rugam asigurati-va ca ati verificat setarile in sectiunea administrare si faceti schimbarile necesare.");
	define("ACCESS_ADMIN_MSG", "Click aici pentru a accesa sectiunea administrare");
	define("ADMIN_URL_MSG", "URL administrare");
	define("MANUAL_URL_MSG", "URL manual");
	define("THANKS_MSG", "Va multumim ca ati ales <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Tip baza de date");
	define("DB_TYPE_DESC", "Va rugam selectati <b>tipul bazei de date</b> pe care il folositi. Daca folositi SQL Server sau Microsoft Access, va rugam selectati ODBC.");
	define("DB_PHP_LIB_FIELD", "Biblioteca PHP");
	define("DB_HOST_FIELD", "Nume host");
	define("DB_HOST_DESC", "Va rugam introduceti <b>numele</b> sau <b>IP-ul serverului</b> pe care va rula baza de date ViArt. Daca rulati baza de date pe calculatorul dumneavoastra local atunci probabil ca puteti lasa \"<b>localhost</b>\" si portul necompletat. Daca folositi baza de date pe care compania de hosting v-o pune la dispozitie, va rugam consultati documentatia companiei de hosting referitoare la setarile serverului.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Nume baza de date / DSN");
	define("DB_NAME_DESC", "Daca folositi o baza de date cum ar fi MySQL sau PostgreSQL va rugam introduceti <b>numele bazei de date</b> acolo unde ati dori ca ViArt sa isi creeze tabelele. Aceasta baza de date trebuie sa existe deja. Daca nu faceti decat sa instalati ViArt pentru teste pe calculatorul dumneavoastra local atunci majoritatea sistemelor au o baza de date \"<b>test</b>\" pe care o puteti folosi. Daca nu, va rugam creati o baza de date cum ar fi \"viart\" si folositi-o pe aceasta. Daca folositi Microsoft Access sau SQL Server atunci numele bazei de date ar trebui sa fie <b>numele DSN</b> pe care l-ati setat in sectiunea Data Sources(ODBC) din Control Panel.");
	define("DB_USER_FIELD", "Utilizator");
	define("DB_PASS_FIELD", "Parola");
	define("DB_USER_PASS_DESC", "<b>Utilizator</b> si <b>Parola</b> - va rugam introduceti utilizatorul si parola pe care doriti sa le folositi pentru a accesa aceasta baza de date. Daca folositi o instalatie locala de testare utilizatorul este probabil \"<b>root</b>\" si parola este probabil blank. Aceasta e in regula pentru testare dar nu este sigur pentru servere de productie.");
	define("DB_PERSISTENT_FIELD", "Conexiune persistenta");
	define("DB_PERSISTENT_DESC", "pentru a folosi conexiunile persistente MySQL sau Postgre, bifati aceasta casuta. Daca nu stiti ce semnifica aceasta, atunci cel mai probabil este mai bine sa lasati nebifat.");
	define("DB_CREATE_DB_FIELD", "Creati baza de date");
	define("DB_CREATE_DB_DESC", "pentru a crea baza de date va rugam bifati aici. Functioneaza numai pentru MySQL si Postgre");
	define("DB_POPULATE_FIELD", "Populati baza de date");
	define("DB_POPULATE_DESC", "pentru a crea structura de tabele a bazei de date si a o popula cu date bifati casuta");
	define("DB_TEST_DATA_FIELD", "Date test");
	define("DB_TEST_DATA_DESC", "pentru a adauga date test in baza dumneavoastra de date bifati casuta");
	define("ADMIN_EMAIL_FIELD", "Email administrator");
	define("ADMIN_LOGIN_FIELD", "Login administrator");
	define("ADMIN_PASS_FIELD", "Parola administrator");
	define("ADMIN_CONF_FIELD", "Confirma parola");
	define("DATETIME_SHOWN_FIELD", "Format data si timp (afisat pe site)");
	define("DATE_SHOWN_FIELD", "Format data (afisat pe site)");
	define("DATETIME_EDIT_FIELD", "Format data si timp (pentru modificare)");
	define("DATE_EDIT_FIELD", "Format data (pentru modificare)");
	define("DATE_FORMAT_COLUMN", "Format data");
	define("CURRENT_DATE_COLUMN", "Data curenta");

	define("DB_LIBRARY_ERROR", "Functiile PHP pentru {db_library} nu sunt definite. Va rugam verificati setarile bazei de date in fisierul de configurare - php.ini");
	define("DB_CONNECT_ERROR", "Nu ma pot conecta la baza de date. Va rugam verificati parametrii bazei de date ");
	define("INSTALL_FINISHED_ERROR", "Procesul de instalare deja finalizat.");
	define("WRITE_FILE_ERROR", "Nu aveti permisiuni de scriere a fisierului <b>'includes/var_definition.php'</b>. Va rugam schimbati permisiunile fisierului inainte de a continua.");
	define("WRITE_DIR_ERROR", "Nu aveti permisiuni de scriere a directorului <b>'includes/'</b>. Va rugam schimbati permisiunile directorului inainte de a continua.");
	define("DUMP_FILE_ERROR", "Fisierul de export '{file_name}' nu a fost gasit.");
	define("DB_TABLE_ERROR", "Tabelul '{table_name}' nu a fost gasit. Va rugam populati baza de date cu datele necesare.");
	define("TEST_DATA_ERROR", "Verificati <b>{POPULATE_DB_FIELD}</b> inainte de a popula tabelele cu date test");
	define("DB_HOST_ERROR", "Numele de host pe care l-ati specificat nu a putut fi gasit.");
	define("DB_PORT_ERROR", "Nu ma pot conecta la serverul bazei de date folosind portul specificat");
	define("DB_USER_PASS_ERROR", "Utilizatorul sau parola specificata nu sunt corecte.");
	define("DB_NAME_ERROR", "Setarile de login au fost corecte, dar baza de date '{db_name}' nu a putut fi gasita.");

	// mesaje actualizare
	define("UPGRADE_TITLE", "Actualizare ViArt SHOP");
	define("UPGRADE_NOTE", "Nota: Va sfatuim sa faceti backup la baza de date inainte de a continua ");
	define("UPGRADE_AVAILABLE_MSG", "Actualizare baza de date disponibila");
	define("UPGRADE_BUTTON", "Actualizati baza de date la {version_number} acum");
	define("CURRENT_VERSION_MSG", "Versiunea instalata");
	define("LATEST_VERSION_MSG", "Versiunea disponibila pentru instalare");
	define("UPGRADE_RESULTS_MSG", "Actualizare rezultate");
	define("SQL_SUCCESS_MSG", "Interogarile SQL au fost efectuate cu succes");
	define("SQL_FAILED_MSG", "Interogarile SQL au esuat");
	define("SQL_TOTAL_MSG", "Total interogari SQL executate");
	define("VERSION_UPGRADED_MSG", "Baza de date a fost actualizata la");
	define("ALREADY_LATEST_MSG", "Aveti deja ultima versiune");
	define("DOWNLOAD_NEW_MSG", "Noua versiune a fost detectata");
	define("DOWNLOAD_NOW_MSG", "Descarcati versiunea {version_number} acum");
	define("DOWNLOAD_FOUND_MSG", "Am detectat veriunea noua {version_number} ca fiind disponibila pentru descarcare. Va rugam dati click pe linkul de mai jos pentru a incepe descarcarea. Dupa finalizarea descarcarii si inlocuirea fisierelor nu uitati sa efectuati operatiunea de actualizare inca o data.");
	define("NO_XML_CONNECTION", "Atentie! Conexiunea la 'http://www.viart.com/' indisponibila!");

?>