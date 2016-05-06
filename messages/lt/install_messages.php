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


	// diegimo �inut�s
	define("INSTALL_TITLE", "ViArt parduotuv�s diegimas");

	define("INSTALL_STEP_1_TITLE", "Diegimas: �ingsnis 1");
	define("INSTALL_STEP_1_DESC", "D�kojame u� ViArt SHOP pasirinkim�. Tam kad baigti �� diegim�, pra�ome u�pildyti visas reikiamas detales �emiau. Pra�ome atkreipti d�mes� kad duombaz� kuri� renkat�s turi jau b�ti sukurta. Jei j�s diegiate � duombaz� kuri naudoja ODBC, pvz. MS Access j�s pirmiau turite sukurti DSN jai prie� t�siant.");
	define("INSTALL_STEP_2_TITLE", "Diegimas: �ingsnis 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Diegimas: �ingsnis 3");
	define("INSTALL_STEP_3_DESC", "Pra�ome rinktis svetain�s i�d�stym�. J�s gal�site keisti i�d�stym� v�liau.");
	define("INSTALL_FINAL_TITLE", "Diegimas: Galas");
	define("SELECT_DATE_TITLE", "Rinkit�s datos format�");

	define("DB_SETTINGS_MSG", "Duombaz�s nustatymai");
	define("DB_PROGRESS_MSG", "Duombaz�s strukt�ros u�pildymas t�siamas");
	define("SELECT_PHP_LIB_MSG", "Rinkit�s PHP bibliotek�");
	define("SELECT_DB_TYPE_MSG", "Rinkit�s duombaz�s tip�");
	define("ADMIN_SETTINGS_MSG", "Valdymo nustatymai");
	define("DATE_SETTINGS_MSG", "Datos formatai");
	define("NO_DATE_FORMATS_MSG", "N�ra joki� datos format�");
	define("INSTALL_FINISHED_MSG", "Iki �io ta�ko pagrindinis diegimas baigtas. Pra�ome tikrai patikrinti nustatymus valdymo skyriuje ir atlikti reikiamus keitimus.");
	define("ACCESS_ADMIN_MSG", "Kad patekti � valdymo skyri� spauskite �ia");
	define("ADMIN_URL_MSG", "Valdymo nuoroda");
	define("MANUAL_URL_MSG", "Rankinis URL nuoroda");
	define("THANKS_MSG", "D�kojame jums u� pasirinkt� <b>ViArt parduotuv�</b>.");

	define("DB_TYPE_FIELD", "Duombaz�s tipas");
	define("DB_TYPE_DESC", "Pra�ome rinktis <b>type of database</b> kuri� naudojate. Jei j�s naudojate SQL Server� arba Microsoft Access, pra�ome rinktis ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP biblioteka");
	define("DB_HOST_FIELD", "�eimininko vardas");
	define("DB_HOST_DESC", "Pra�ome �vesti <b>name</b> ar <b>IP address of the server</b> ant kurio j�s� ViArt duomen� baz� suksis. Jei j�s sukate j�s� duombaz� ant savo vietinio PC, tai j�s tikriausiai galite palikti tai kaip yra \"<b>localhost</b>\" ir prievad� tu��i�. Jei j�s naudojate duombaz� suteikt� j�s� talpinimo firmos, pra�ome skaityti j�s� talpinimo firmos dokumentacij� serverio nustatymams.");
	define("DB_PORT_FIELD", "Prievadas");
	define("DB_NAME_FIELD", "Duombaz�s vardas / DSN");
	define("DB_NAME_DESC", "Jei j�s naudojate duombaz� toki� kaip MySQL ar PostgreSQL tai pra�ome �vesti <b>name of the database</b> kur j�s norite kad ViArt sukurt� savo lenteles. �i duombaz� jau turi b�ti sukurta. Jei j�s tik diegiate ViArt testavimo tikslams ant savo vietinio PC tai daugiausia sistem� turi \"<b>test</b>\" duombaz� kuri� galite naudoti. Jei ne, pra�ome sukurti duombaz� toki� kaip \"viart\" ir j� naudoti. Jei j�s naudojate Microsoft Access ar SQL Server�, tai Duombaz�s Vardas turi b�ti <b>name of the DSN</b> kur� j�s suk�r�te Data Sources (ODBC) skyriuke j�s� Valdymo Skydelyje.");
	define("DB_USER_FIELD", "Vartotojo vardas");
	define("DB_PASS_FIELD", "Slapta�odis");
	define("DB_USER_PASS_DESC", "<b>Username</b> ir <b>Password</b> - pra�ome �vesti vartotojo vard� ir slapta�od� kur� norite naudoti duombaz�s pri�jimui. Jei j�s naudojate vietin� testavimo diegim� vartotojo vardas tikriausiai yra \"<b>root</b>\" ir slapta�odis tikriausiai yra tu��ias. Tai gerai testavimui, bet u�si�ym�kite, kad tai n�ra saugu ant veikian�i� gamybini� serveri�.");
	define("DB_PERSISTENT_FIELD", "I�liekantis pasijungimas");
	define("DB_PERSISTENT_DESC", "Tam kad naudoti MySQL i�liekan�ius pasijungimus, u�d�ktie varnel� �ioje d��ut�je. Jei j�s ne�inote kas tai yra , tai palikti j� neat�ym�t� bus geriausia.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "U�pildyk DB");
	define("DB_POPULATE_DESC", "Kad sukurti duombaz�s lenteli� strukt�r� ir u�pildyti jas duomenimis spustelkite �ymi� d��ut�");
	define("DB_TEST_DATA_FIELD", "Bandym� duomenys");
	define("DB_TEST_DATA_DESC", "kad prid�ti �iek tiek bandym� duomen� � j�s� duombaz� pa�ym�kite varnele");
	define("ADMIN_EMAIL_FIELD", "Valdytojo e-pa�tas");
	define("ADMIN_LOGIN_FIELD", "Valdytojo �sijungimas");
	define("ADMIN_PASS_FIELD", "Valdytojo slapta�odis");
	define("ADMIN_CONF_FIELD", "Patvirtinkite slapta�od�");
	define("DATETIME_SHOWN_FIELD", "Datos-laiko formatas (rodomas svetain�je)");
	define("DATE_SHOWN_FIELD", "Datos formatas (rodomas svetain�je)");
	define("DATETIME_EDIT_FIELD", "Datos-laiko formatas (redagavimui)");
	define("DATE_EDIT_FIELD", "Datos formatas (redagavimui)");
	define("DATE_FORMAT_COLUMN", "Datos formatas");
	define("CURRENT_DATE_COLUMN", "Dabartin� data");

	define("DB_LIBRARY_ERROR", "PHP funkcijos {db_library} n�ra apibr��tos. Pra�ome patikrinti duombaz�s nustatymus j�s� konfig�racijos byloje - php.ini.");
	define("DB_CONNECT_ERROR", "Negaliu pasijungti prie duombaz�s. Pra�ome tikrinti j�s� duombaz�s parametrus.");
	define("INSTALL_FINISHED_ERROR", "Diegimo eiga jau baig�si.");
	define("WRITE_FILE_ERROR", "Neturiu ra�ymo teisi� bylai <b>'includes/var_definition.php'</b>. Pra�ome pakeisti bylos teises prie� t�siant.");
	define("WRITE_DIR_ERROR", "Neturiu ra�ymo teisi� aplankui  <b>'includes/'</b>. Pra�ome pakeisti aplanko teises prie� t�siant.");
	define("DUMP_FILE_ERROR", "I�krovimo byla '{file_name}' nerasta.");
	define("DB_TABLE_ERROR", "Lentel� '{table_name}' nerasta. Pra�ome u�pildyti duombaz� reikalingais duomenimis.");
	define("TEST_DATA_ERROR", "Patikrink <b>{POPULATE_DB_FIELD}</b> prie� u�pildant lenteles su bandym� duomenimis");
	define("DB_HOST_ERROR", "Mazgo vardas kur� nurod�te nerastas.");
	define("DB_PORT_ERROR", "Negaliu pasijungti prie MySQL serverio naudojant nurodyt� prievad�.");
	define("DB_USER_PASS_ERROR", "Vartotojo vardas ir slapta�odis kur� nurod�te neteisingi.");
	define("DB_NAME_ERROR", "�sijungimo nustatymai buvo teisingi, bet duombaz� '{db_name}' nerasta.");

	// atnaujinimo prane�imai
	define("UPGRADE_TITLE", "ViArt Parduotuv�s Naujinimas");
	define("UPGRADE_NOTE", "Pastaba: Pra�ome pagalvoti apie duombaz�s kopij� prie� t�siant.");
	define("UPGRADE_AVAILABLE_MSG", "Duombaz�s naujinimas teikiamas");
	define("UPGRADE_BUTTON", "Naujinti duombaz� iki {version_number} dabar");
	define("CURRENT_VERSION_MSG", "Dabar �diegta versija");
	define("LATEST_VERSION_MSG", "Versija tiekiama diegimui");
	define("UPGRADE_RESULTS_MSG", "Naujinimo pasekm�s");
	define("SQL_SUCCESS_MSG", "SQL u�klausa pavyko");
	define("SQL_FAILED_MSG", "SQL u�klausa nepavyko");
	define("SQL_TOTAL_MSG", "Viso SQL u�klaus� �vykdyta");
	define("VERSION_UPGRADED_MSG", "J�s� duombaz� buvo atnaujinta iki");
	define("ALREADY_LATEST_MSG", "J�s jau turite naujausi� versij�");
	define("DOWNLOAD_NEW_MSG", "Nauja versija buvo aptikta");
	define("DOWNLOAD_NOW_MSG", "Nusisi�skite versij� {version_number} dabar");
	define("DOWNLOAD_FOUND_MSG", "Mes aptikome kad nauja {version_number} versija tiekiama nusisiuntimui. Pra�ome spausti nuorod� �emiau kad prad�ti nusisiuntim�. Po siuntimosi baigimo ir byl� pakeitimo nepamir�kite leisti Naujinimo paprogram� v�l.");
	define("NO_XML_CONNECTION", "�sp�jimas! N�ra ry�io iki 'http://www.viart.com/' prieinama!");

?>