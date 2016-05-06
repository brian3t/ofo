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


	// diegimo þinutës
	define("INSTALL_TITLE", "ViArt parduotuvës diegimas");

	define("INSTALL_STEP_1_TITLE", "Diegimas: Þingsnis 1");
	define("INSTALL_STEP_1_DESC", "Dëkojame uþ ViArt SHOP pasirinkimà. Tam kad baigti ðá diegimà, praðome uþpildyti visas reikiamas detales þemiau. Praðome atkreipti dëmesá kad duombazë kurià renkatës turi jau bûti sukurta. Jei jûs diegiate á duombazæ kuri naudoja ODBC, pvz. MS Access jûs pirmiau turite sukurti DSN jai prieð tæsiant.");
	define("INSTALL_STEP_2_TITLE", "Diegimas: Þingsnis 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Diegimas: Þingsnis 3");
	define("INSTALL_STEP_3_DESC", "Praðome rinktis svetainës iðdëstymà. Jûs galësite keisti iðdëstymà vëliau.");
	define("INSTALL_FINAL_TITLE", "Diegimas: Galas");
	define("SELECT_DATE_TITLE", "Rinkitës datos formatà");

	define("DB_SETTINGS_MSG", "Duombazës nustatymai");
	define("DB_PROGRESS_MSG", "Duombazës struktûros uþpildymas tæsiamas");
	define("SELECT_PHP_LIB_MSG", "Rinkitës PHP bibliotekà");
	define("SELECT_DB_TYPE_MSG", "Rinkitës duombazës tipà");
	define("ADMIN_SETTINGS_MSG", "Valdymo nustatymai");
	define("DATE_SETTINGS_MSG", "Datos formatai");
	define("NO_DATE_FORMATS_MSG", "Nëra jokiø datos formatø");
	define("INSTALL_FINISHED_MSG", "Iki ðio taðko pagrindinis diegimas baigtas. Praðome tikrai patikrinti nustatymus valdymo skyriuje ir atlikti reikiamus keitimus.");
	define("ACCESS_ADMIN_MSG", "Kad patekti á valdymo skyriø spauskite èia");
	define("ADMIN_URL_MSG", "Valdymo nuoroda");
	define("MANUAL_URL_MSG", "Rankinis URL nuoroda");
	define("THANKS_MSG", "Dëkojame jums uþ pasirinktà <b>ViArt parduotuvæ</b>.");

	define("DB_TYPE_FIELD", "Duombazës tipas");
	define("DB_TYPE_DESC", "Praðome rinktis <b>type of database</b> kurià naudojate. Jei jûs naudojate SQL Serverá arba Microsoft Access, praðome rinktis ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP biblioteka");
	define("DB_HOST_FIELD", "Ðeimininko vardas");
	define("DB_HOST_DESC", "Praðome ávesti <b>name</b> ar <b>IP address of the server</b> ant kurio jûsø ViArt duomenø bazë suksis. Jei jûs sukate jûsø duombazæ ant savo vietinio PC, tai jûs tikriausiai galite palikti tai kaip yra \"<b>localhost</b>\" ir prievadà tuðèià. Jei jûs naudojate duombazæ suteiktà jûsø talpinimo firmos, praðome skaityti jûsø talpinimo firmos dokumentacijà serverio nustatymams.");
	define("DB_PORT_FIELD", "Prievadas");
	define("DB_NAME_FIELD", "Duombazës vardas / DSN");
	define("DB_NAME_DESC", "Jei jûs naudojate duombazæ tokià kaip MySQL ar PostgreSQL tai praðome ávesti <b>name of the database</b> kur jûs norite kad ViArt sukurtø savo lenteles. Ði duombazë jau turi bûti sukurta. Jei jûs tik diegiate ViArt testavimo tikslams ant savo vietinio PC tai daugiausia sistemø turi \"<b>test</b>\" duombazæ kurià galite naudoti. Jei ne, praðome sukurti duombazæ tokià kaip \"viart\" ir jà naudoti. Jei jûs naudojate Microsoft Access ar SQL Serverá, tai Duombazës Vardas turi bûti <b>name of the DSN</b> kurá jûs sukûrëte Data Sources (ODBC) skyriuke jûsø Valdymo Skydelyje.");
	define("DB_USER_FIELD", "Vartotojo vardas");
	define("DB_PASS_FIELD", "Slaptaþodis");
	define("DB_USER_PASS_DESC", "<b>Username</b> ir <b>Password</b> - praðome ávesti vartotojo vardà ir slaptaþodá kurá norite naudoti duombazës priëjimui. Jei jûs naudojate vietiná testavimo diegimà vartotojo vardas tikriausiai yra \"<b>root</b>\" ir slaptaþodis tikriausiai yra tuðèias. Tai gerai testavimui, bet uþsiþymëkite, kad tai nëra saugu ant veikianèiø gamybiniø serveriø.");
	define("DB_PERSISTENT_FIELD", "Iðliekantis pasijungimas");
	define("DB_PERSISTENT_DESC", "Tam kad naudoti MySQL iðliekanèius pasijungimus, uþdëktie varnelæ ðioje dëþutëje. Jei jûs neþinote kas tai yra , tai palikti jà neatþymëtà bus geriausia.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "Uþpildyk DB");
	define("DB_POPULATE_DESC", "Kad sukurti duombazës lenteliø struktûrà ir uþpildyti jas duomenimis spustelkite þymiø dëþutæ");
	define("DB_TEST_DATA_FIELD", "Bandymø duomenys");
	define("DB_TEST_DATA_DESC", "kad pridëti ðiek tiek bandymø duomenø á jûsø duombazæ paþymëkite varnele");
	define("ADMIN_EMAIL_FIELD", "Valdytojo e-paðtas");
	define("ADMIN_LOGIN_FIELD", "Valdytojo ásijungimas");
	define("ADMIN_PASS_FIELD", "Valdytojo slaptaþodis");
	define("ADMIN_CONF_FIELD", "Patvirtinkite slaptaþodá");
	define("DATETIME_SHOWN_FIELD", "Datos-laiko formatas (rodomas svetainëje)");
	define("DATE_SHOWN_FIELD", "Datos formatas (rodomas svetainëje)");
	define("DATETIME_EDIT_FIELD", "Datos-laiko formatas (redagavimui)");
	define("DATE_EDIT_FIELD", "Datos formatas (redagavimui)");
	define("DATE_FORMAT_COLUMN", "Datos formatas");
	define("CURRENT_DATE_COLUMN", "Dabartinë data");

	define("DB_LIBRARY_ERROR", "PHP funkcijos {db_library} nëra apibrëþtos. Praðome patikrinti duombazës nustatymus jûsø konfigûracijos byloje - php.ini.");
	define("DB_CONNECT_ERROR", "Negaliu pasijungti prie duombazës. Praðome tikrinti jûsø duombazës parametrus.");
	define("INSTALL_FINISHED_ERROR", "Diegimo eiga jau baigësi.");
	define("WRITE_FILE_ERROR", "Neturiu raðymo teisiø bylai <b>'includes/var_definition.php'</b>. Praðome pakeisti bylos teises prieð tæsiant.");
	define("WRITE_DIR_ERROR", "Neturiu raðymo teisiø aplankui  <b>'includes/'</b>. Praðome pakeisti aplanko teises prieð tæsiant.");
	define("DUMP_FILE_ERROR", "Iðkrovimo byla '{file_name}' nerasta.");
	define("DB_TABLE_ERROR", "Lentelë '{table_name}' nerasta. Praðome uþpildyti duombazæ reikalingais duomenimis.");
	define("TEST_DATA_ERROR", "Patikrink <b>{POPULATE_DB_FIELD}</b> prieð uþpildant lenteles su bandymø duomenimis");
	define("DB_HOST_ERROR", "Mazgo vardas kurá nurodëte nerastas.");
	define("DB_PORT_ERROR", "Negaliu pasijungti prie MySQL serverio naudojant nurodytà prievadà.");
	define("DB_USER_PASS_ERROR", "Vartotojo vardas ir slaptaþodis kurá nurodëte neteisingi.");
	define("DB_NAME_ERROR", "Ásijungimo nustatymai buvo teisingi, bet duombazë '{db_name}' nerasta.");

	// atnaujinimo praneðimai
	define("UPGRADE_TITLE", "ViArt Parduotuvës Naujinimas");
	define("UPGRADE_NOTE", "Pastaba: Praðome pagalvoti apie duombazës kopijà prieð tæsiant.");
	define("UPGRADE_AVAILABLE_MSG", "Duombazës naujinimas teikiamas");
	define("UPGRADE_BUTTON", "Naujinti duombazæ iki {version_number} dabar");
	define("CURRENT_VERSION_MSG", "Dabar ádiegta versija");
	define("LATEST_VERSION_MSG", "Versija tiekiama diegimui");
	define("UPGRADE_RESULTS_MSG", "Naujinimo pasekmës");
	define("SQL_SUCCESS_MSG", "SQL uþklausa pavyko");
	define("SQL_FAILED_MSG", "SQL uþklausa nepavyko");
	define("SQL_TOTAL_MSG", "Viso SQL uþklausø ávykdyta");
	define("VERSION_UPGRADED_MSG", "Jûsø duombazë buvo atnaujinta iki");
	define("ALREADY_LATEST_MSG", "Jûs jau turite naujausià versijà");
	define("DOWNLOAD_NEW_MSG", "Nauja versija buvo aptikta");
	define("DOWNLOAD_NOW_MSG", "Nusisiøskite versijà {version_number} dabar");
	define("DOWNLOAD_FOUND_MSG", "Mes aptikome kad nauja {version_number} versija tiekiama nusisiuntimui. Praðome spausti nuorodà þemiau kad pradëti nusisiuntimà. Po siuntimosi baigimo ir bylø pakeitimo nepamirðkite leisti Naujinimo paprogramæ vël.");
	define("NO_XML_CONNECTION", "Áspëjimas! Nëra ryðio iki 'http://www.viart.com/' prieinama!");

?>