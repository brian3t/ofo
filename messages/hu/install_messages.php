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


	//telep�t�si �zenetek
	define("INSTALL_TITLE", " ViArtshop Telep�t�s");

	define("INSTALL_STEP_1_TITLE", "Telep�t�s: L�p�s 1");
	define("INSTALL_STEP_1_DESC", "K�sz�net a ViArt SHOP v�laszt�s��rt. A telep�t�s folytat�s�hoz ki kell t�lteni a  lenti mez�ket. Figyelem: az adatb�zis kiv�laszt�s�hoz m�r el�bb l�tre kell hozni egy adatb�zist. Ha olyan adatb�zist telep�tesz ami  ODBC t  haszn�l, mint a Microsoft Access , tov�bb halad�s el�tt  l�tre kell hozni DNS t.");
	define("INSTALL_STEP_2_TITLE", "Telep�t�s: L�p�s 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Telep�t�s: L�p�s 3");
	define("INSTALL_STEP_3_DESC", "K�rem a website megjelen�s mint�k k�z�l v�lasztani. K�s�bb lesz lehet�s�g a v�ltoztat�sra.");
	define("INSTALL_FINAL_TITLE", "Telep�t�s: befejez�s");
	define("SELECT_DATE_TITLE", "V�lassz d�tumot form�tumot");

	define("DB_SETTINGS_MSG", "Adatb�zis be�ll�t�sok");
	define("DB_PROGRESS_MSG", "N�pszer� adatb�zis strukt�ra elj�r�s");
	define("SELECT_PHP_LIB_MSG", "V�lassz PHP K�nyvt�rat");
	define("SELECT_DB_TYPE_MSG", "V�lassz adatb�zis t�pust");
	define("ADMIN_SETTINGS_MSG", "Adminisztrat�v be�ll�t�sok");
	define("DATE_SETTINGS_MSG", "D�tum form�tumok");
	define("NO_DATE_FORMATS_MSG", "Nincs el�rhet� d�tum form�tum");
	define("INSTALL_FINISHED_MSG", "Enn�l pont az alap telep�t�sed  teljes. K�rem persze ellen�rizni a be�ll�t�sokat az adminisztr�ci�ban �s v�grehajtani a sz�ks�ge v�ltoztat�sokat.");
	define("ACCESS_ADMIN_MSG", "Az adminisztr�ci� r�sz el�r�s�hez kattint�s itt");
	define("ADMIN_URL_MSG", "Adminisztr�ci� URL");
	define("MANUAL_URL_MSG", "Haszn�lat utas�t�s URL");
	define("THANKS_MSG", "K�sz�nj�k, hogy a <b>ViArt SHOP</b>t v�lasztotta.   ");

	define("DB_TYPE_FIELD", "Adatb�zis T�pus");
	define("DB_TYPE_DESC", "K�rem kiv�lasztani <b>adatb�zis t�pusa</b> amelyiket haszn�lod. Ha  SQL Servert vagy Microsoft Access,akkor v�laszd: ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP K�nyvt�r");
	define("DB_HOST_FIELD", "Hostn�v");
	define("DB_HOST_DESC", "K�rem be�rni  <b>szervern�v</b>vagy <b>szerver IP c�me</b>ahol a Viart adatb�zis futni fog. Ha az adatb�zis lok�lis PC-�n fut akkor val�sz�n�leg j� a \"<b>localhost</b>\" �s hagyd a portot �resen. Ha egy szolg�ltat�d �ltal biztos�tott adatb�zist haszn�lsz , akkor tanulm�nyozd �t a szolg�ltat� szerver be�ll�t�sainak a dokument�ci�j�t.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Adatb�zis N�v/ DSN");
	define("DB_NAME_DESC", "Ha olyan adatb�zist haszn�lsz , mint a  MySQL vagy PostgreSQL akkor �rd be <b>adatb�zis neve</b> ahol szeretn�d , hogy a ViArt l�trehozza a t�bl�it Ennek az adatb�zisnak l�tez�nek kell lenni. Ha tesztel�s c�lj�b�l telep�ted a Viart lok�lis pc-re, akkor a legt�bb g�pnek van egy <b>test</b>\" adatb�zisa , amit haszn�lhatsz. Ha nem , akkor k�sz�ts egy adatb�zist \"viart\" n�ven , �s azt haszn�ld. Ha  Microsoft Access-t vagy r SQL Servert haszn�lsz akkor az adatb�zis nev�nek  <b>DSN neve</b> kell lenni , amit be�ll�tott�l az adatforr�sokn�l(ODBC)  a kontrol panelen.");
	define("DB_USER_FIELD", "Felhaszn�l�n�v");
	define("DB_PASS_FIELD", "Jelsz�");
	define("DB_USER_PASS_DESC", "<b>felhaszn�l�n�v</b> �s <b>jelsz�</b> -k�rem be�rni a felhaszn�l�nevet �s a jelsz�t amit haszn�lni akarsz az adatb�zis el�r�s�hez. Ha egy lok�lis g�pen teszt install�l�st haszn�lsz, a lehets�ges felhaszn�l�n�v \"<b>root</b>\" �s nincs jelsz�. Ez �gy kiv�l� tesztel�sre , de nem biztons�gos egy nyilv�nos szerveren.");
	define("DB_PERSISTENT_FIELD", "�lland� Kapcsolat");
	define("DB_PERSISTENT_DESC", "�lland� MySQL//Postgre kapcsolat eset�n klikkeld be ezt a dobozt, ha nem tudod , hogy mit jelent , ink�bb hagyd �resen.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "n�pszer� DB");
	define("DB_POPULATE_DESC", "Az adatb�zis t�bl�zat elk�sz�t�s�hez klikkelj a dobozba.");
	define("DB_TEST_DATA_FIELD", "Pr�ba adat");
	define("DB_TEST_DATA_DESC", "N�h�ny pr�ba adat hozz�ad�s�hoz klikkel a dobozba.");
	define("ADMIN_EMAIL_FIELD", "�gyint�z� Email");
	define("ADMIN_LOGIN_FIELD", "�gyint�z� azonos�t�");
	define("ADMIN_PASS_FIELD", "�gyint�z� jelsz�");
	define("ADMIN_CONF_FIELD", "Ism�teld meg a jelsz�t");
	define("DATETIME_SHOWN_FIELD", "Id� form�tum (webhelyen l�tszik)");
	define("DATE_SHOWN_FIELD", "D�tum form�tum (webhelyen l�tszik)");
	define("DATETIME_EDIT_FIELD", "Id� form�tum (szerkeszt�skor)");
	define("DATE_EDIT_FIELD", "D�tum form�tum (szerkeszt�skor)");
	define("DATE_FORMAT_COLUMN", "D�tum form�tum");
	define("CURRENT_DATE_COLUMN", "Mai d�tum");

	define("DB_LIBRARY_ERROR", "PHP funkci�k nincsenek defini�lva a {db_library} sz�m�ra. K�rem ellen�rizze az adatb�zis be�ll�t�sait a konfigur�ci�ban. F�jl:  php.ini.");
	define("DB_CONNECT_ERROR", "Nem lehet csatlakozni az adatb�zishoz. K�rem ellen�rizze az adatb�zis param�tereit.");
	define("INSTALL_FINISHED_ERROR", " A telep�t�s folyamat m�r befejezett.");
	define("WRITE_FILE_ERROR", "Nincs �r�si enged�lye a <b>'includes/var_definition.php'</b> f�jlhoz. Folytat�s el�tt meg kell v�ltozatni.");
	define("WRITE_DIR_ERROR", "Nem rendelkezik �r�si enged�llyel a <b>'includes/'</b> mapp�hoz. K�rem megv�ltoztatni a mappa enged�lyeket.");
	define("DUMP_FILE_ERROR", "Dump f�jl '{file_name}' nem tal�lhat�.");
	define("DB_TABLE_ERROR", "T�bla 'table_name' nem tal�lhat�. K�rem felt�lteni az adatb�zist a sz�ks�ges adattal.");
	define("TEST_DATA_ERROR", "Ellen�rizd a <b>{POPULATE_DB_FIELD}</b> miel�tt k�z�teszel t�bl�kat teszt adatokkal.");
	define("DB_HOST_ERROR", "A hostn�v amit meghat�rozt�l, nem tal�lhat�.");
	define("DB_PORT_ERROR", "MySQL szerver meghat�rozott portj�hoz nem lehet csatlakozni.");
	define("DB_USER_PASS_ERROR", "A meghat�rozott felhaszn�l�n�v jelsz� helytelen.");
	define("DB_NAME_ERROR", "Login be�ll�t�sok rendben vannak, de az adatb�zis '{db_name}'  nem tal�lhat�.");

	//friss�t�s �zenetek
	define("UPGRADE_TITLE", " ViArt SHOP friss�t�s");
	define("UPGRADE_NOTE", "Megjegyez�s: K�rem k�sz�tsen ment�st az adatb�zisr�l, miel�tt friss�tene!");
	define("UPGRADE_AVAILABLE_MSG", "Adatb�zis friss�tett v�ltozat el�rhet�");
	define("UPGRADE_BUTTON", "Friss�t�s a  {version_number} verzi�ra ");
	define("CURRENT_VERSION_MSG", "Jelenleg telep�tett v�ltozatot");
	define("LATEST_VERSION_MSG", "El�rhet� telep�thet� v�ltozat ");
	define("UPGRADE_RESULTS_MSG", "Friss�t�s eredm�nye");
	define("SQL_SUCCESS_MSG", "SQL lek�rdez�s sikeres");
	define("SQL_FAILED_MSG", "SQL lek�rdez�s nem siker�lt");
	define("SQL_TOTAL_MSG", "Teljes SQL lek�rdez�s megt�rt�nt");
	define("VERSION_UPGRADED_MSG", "Az adatb�zisod friss�tett a");
	define("ALREADY_LATEST_MSG", "M�r a leg�jabb v�ltozattal rendelkezel.");
	define("DOWNLOAD_NEW_MSG", "�j v�ltozatot tal�ltunk.");
	define("DOWNLOAD_NOW_MSG", " {version_number} verzi� let�lt�se most.");
	define("DOWNLOAD_FOUND_MSG", "�rz�kelt�k, hogy az �j {version_number} verzi� let�lthet�. Az al�bbi linkre kattintva lehet elkezdeni a let�lt�st. A let�lt�s ut�n �s a f�jlok cser�l�se ne felejtsd  friss�t�st �jra futatni.");
	define("NO_XML_CONNECTION", "Figyelmeztet�s! Nincs kapcsolat 'HTTP:www.viart.com/' ! ");

?>