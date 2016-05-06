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
	define("INSTALL_TITLE", "C�i �t ViArt SHOP");

	define("INSTALL_STEP_1_TITLE", "C�i �t: B߾c 1");
	define("INSTALL_STEP_1_DESC", "C�m �n b�n �� ch�n ViArt SHOP. Ь ti�p t�c c�i �t, xin h�y �i�n chi  ti�t y�u c�u d߾i ��y. Xin l�u � r�ng database c�a b�n ph�i t�n t�i trong m�y ch�. N�u b�n ch�n c�i �t database b�ng MS Access v�i k�t n�i ODBC xin h�y t�o DSN �u ti�n tr߾c khi ti�p t�c qu� tr�nh n�y.");
	define("INSTALL_STEP_2_TITLE", "C�i �t: B߾c 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "C�i �t: B߾c 3");
	define("INSTALL_STEP_3_DESC", "Xin h�y ch�n ki�u hi�n th� trang web. B�n s� thay �i n� sau n�y.");
	define("INSTALL_FINAL_TITLE", "C�i �t: Ho�n t�t");
	define("SELECT_DATE_TITLE", "Ch�n �nh d�ng ng�y th�ng");

	define("DB_SETTINGS_MSG", "C�c thi�t l�p database");
	define("DB_PROGRESS_MSG", "T�o c�u tr�c database");
	define("SELECT_PHP_LIB_MSG", "Ch�n th� vi�n PHP");
	define("SELECT_DB_TYPE_MSG", "Ch�n ki�u database");
	define("ADMIN_SETTINGS_MSG", "C�c thi�t l�p v� qu�n tr�");
	define("DATE_SETTINGS_MSG", "C�c �nh d�ng ng�y th�ng");
	define("NO_DATE_FORMATS_MSG", "Kh�ng c� s�n c�c �nh d�ng ng�y th�ng");
	define("INSTALL_FINISHED_MSG", "T�i th�i �i�m n�y qu� tr�nh c�i �t c� b�n �� ho�n th�nh. Xin vui l�ng ki�m c�c thi�t l�p � khu v�c qu�n tr� v� nh�ng thay �i b�t bu�c ");
	define("ACCESS_ADMIN_MSG", "Ь truy c�p v�o khu v�c qu�n tr� ch�n � ��y");
	define("ADMIN_URL_MSG", "�߶ng d�n khu v�c qu�n tr�");
	define("MANUAL_URL_MSG", "T� v�o �߶ng d�n");
	define("THANKS_MSG", "C�m �n b�n �� ch�n <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Ki�u database");
	define("DB_TYPE_DESC", "Xin vui l�ng ch�n <b>ki�u database</b> m� b�n s� d�ng. N�u b�n s� d�ng SQL Server ho�c MS Access, xin vui l�ng ch�n ODBC");
	define("DB_PHP_LIB_FIELD", "Th� vi�n PHP");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "Xin vui l�ng �i�n v�o <b>t�n</b> ho�c <b>�a ch� IP c�a m�y ch�</b> m� database c�a ViArt s� ch�y. N�u b�n s� d�ng database tr�n m�y ch� n�i b� xin vui l�ng � tr�ng ho�c g� <b>localhost</b>. N�u b�n c�i �t tr�n m�y ch� hosting xin vui l�ng xem l�i t�i li�u ho�c li�n h� v�i b� ph�n h� tr� d�ch v� hosting.");
	define("DB_PORT_FIELD", "C�ng");
	define("DB_NAME_FIELD", "T�n database/DSN");
	define("DB_NAME_DESC", "N�u b�n s� d�ng database MySQL ho�c PostgreSQL xin vui l�ng g� v�o <b>t�n database</b>. Database n�y ph�i t�n t�i s�n. N�u b�n c�i �t ViArt SHOP v�i m�c ��ch test tr�n m�y n�i b� b�n c� th� �i�n v�o <b>test</b>. N�u kh�ng b�n c� th� g� v�o <b>Viart</b> v� s� d�ng n�. N�u b�n s� d�ng MS Access ho�c SQL Server xin h�y �i�n v�o <b>t�n DSN</b> m� b�n thi�t l�p trong k�t n�i ODBC � khu v�c Control Panel.");
	define("DB_USER_FIELD", "T�n truy c�p");
	define("DB_PASS_FIELD", "M�t m�");
	define("DB_USER_PASS_DESC", "<b>T�n truy c�p<b> v� <b>M�t m�</b> - Xin vui l�ng �i�n v�o t�n truy c�p v� m�t m� m� b�n s� d�ng � truy c�p ���c database. N�u b�n d�ng v�i m�c ��ch � test xin vui l�ng g� v�o \"<b>root</b>\" v� m�t m� � tr�ng. Xin l�u � ��ng d�ng n� cho m�c ��ch ��a l�n Internet v� n� kh�ng an to�n v� b�o m�t.");
	define("DB_PERSISTENT_FIELD", "Persistent Connection");
	define("DB_PERSISTENT_DESC", "� s� d�ng ki�u k�t n�i persistent v�i MySQL v� PostgresSQL, ch�n � � n�y. N�u b�n kh�ng ch�c ch�n, xin h�y b� qua.");
	define("DB_CREATE_DB_FIELD", "T�o DB");
	define("DB_CREATE_DB_DESC", "� t�o database, n�u ���c xin ch�n � n�y. Ch�c n�ng ch� ho�t �ng v�i ki�u d� li�u MySQL v� Postgre");
	define("DB_POPULATE_FIELD", "��a v�o d� li�u m�u");
	define("DB_POPULATE_DESC", "Ь t�o c�u tr�c database xin ch�n v�o � n�y");
	define("DB_TEST_DATA_FIELD", "Ki�m tra d� li�u");
	define("DB_TEST_DATA_DESC", "� th�m v�o m�t v�i d� li�u � ki�m tra xin ch�n � n�y");
	define("ADMIN_EMAIL_FIELD", "Email ng߶i qu�n tr�");
	define("ADMIN_LOGIN_FIELD", "T�n truy c�p admin");
	define("ADMIN_PASS_FIELD", "M�t m� c�a admin");
	define("ADMIN_CONF_FIELD", "X�c nh�n l�i m�t m�");
	define("DATETIME_SHOWN_FIELD", "иnh d�ng gi� (tr�n trang web)");
	define("DATE_SHOWN_FIELD", "иnh d�ng ng�y th�ng (tr�n trang web)");
	define("DATETIME_EDIT_FIELD", "иnh d�ng gi� (tr�n trang qu�n tr�)");
	define("DATE_EDIT_FIELD", "иnh d�ng ng�y th�ng (tr�n trang qu�n tr�)");
	define("DATE_FORMAT_COLUMN", "иnh d�ng ng�y");
	define("CURRENT_DATE_COLUMN", "Ng�y hi�n t�i");

	define("DB_LIBRARY_ERROR", "C�c h�m ch�c n�ng PHP cho {db_library} kh�ng ���c �nh ngh�a. Xin vui l�ng ki�m tra l�i thi�t l�p database trong t�p c�u h�nh php.ini.");
	define("DB_CONNECT_ERROR", "Kh�ng th� k�t n�i database. Xin vui l�ng ki�m tra tham s� database.");
	define("INSTALL_FINISHED_ERROR", "Qu� tr�nh c�i �t �� ho�n t�t.");
	define("WRITE_FILE_ERROR", "Kh�ng c� quy�n ghi t�p d� li�u <b>'includes/var_definition.php'</b>. Xin vui l�ng thay �i quy�n truy c�p tr߾c khi th�c hi�n b߾c n�y.");
	define("WRITE_DIR_ERROR", "Kh�ng c� quy�n ghi th� m�c <b>'includes/'</b>. Xin vui l�ng thay �i quy�n cho th� m�c tr߾c khi ti�p t�c.");
	define("DUMP_FILE_ERROR", "T�p '{file_name}' kh�ng t�m th�y.");
	define("DB_TABLE_ERROR", "Table '{table_name}' kh�ng t�m th�y. Xin vui l�ng t�o database v�i d� li�u c�n thi�t.");
	define("TEST_DATA_ERROR", "Ki�m tra <b>{POPULATE_DB_FIELD}</b> tr߾c khi t�o b�ng table khi ki�m tra d� li�u");
	define("DB_HOST_ERROR", "Hostname m� b�n m� t� kh�ng th� t�m th�y.");
	define("DB_PORT_ERROR", "Kh�ng th� k�t n�i m�y ch� database v�i c�ng hi�n t�i.");
	define("DB_USER_PASS_ERROR", "T�n truy c�p v� m�t m� b�n m� t� kh�ng ��ng.");
	define("DB_NAME_ERROR", "C�c thi�t l�p ��ng nh�p l� ��ng, nh�ng database '{db_name}' kh�ng th� t�m th�y.");

	// upgrade messages
	define("UPGRADE_TITLE", "N�ng c�p ViArt SHOP");
	define("UPGRADE_NOTE", "L�u �: Xin h�y l�u � r�ng ch�c ch�n backup database tr߾c khi ti�p t�c.");
	define("UPGRADE_AVAILABLE_MSG", "�� s�n s�ng c�p nh�t database");
	define("UPGRADE_BUTTON", "C�p nh�t database �n {version_number} ngay");
	define("CURRENT_VERSION_MSG", "Phi�n b�n hi�n t�i �� c�i �t");
	define("LATEST_VERSION_MSG", "Phi�n b�n m�i � c�i �t");
	define("UPGRADE_RESULTS_MSG", "K�t qu� n�ng c�p");
	define("SQL_SUCCESS_MSG", "Truy v�n SQL th�nh c�ng");
	define("SQL_FAILED_MSG", "Truy v�n SQL b� l�i");
	define("SQL_TOTAL_MSG", "T�t c� truy v�n SQL thi h�nh");
	define("VERSION_UPGRADED_MSG", "Database c�a b�n ���c n�ng c�p �n");
	define("ALREADY_LATEST_MSG", "B�n �� c�p nh�t phi�n b�n m�i nh�t");
	define("DOWNLOAD_NEW_MSG", "Phi�n b�n m�i ���c ki�m tra");
	define("DOWNLOAD_NOW_MSG", "T�i xu�ng phi�n b�n {version_number} ngay.");
	define("DOWNLOAD_FOUND_MSG", "Ch�ng t�i �� ki�m tra phi�n b�n {version_number} �� s�n s�ng cho vi�c t�i xu�ng. Nh�n ch�n �߶ng link d߾i ��y � t�i xu�ng. Sau khi ho�n t�t t�i xu�ng v� thay th� t�p d� li�u, nh� ��ng qu�n ch�y b�n c�p nh�t tr� l�i.");
	define("NO_XML_CONNECTION", "C�nh b�o! Kh�ng c� k�t n�is�n �n �a ch� 'http://www.viart.com/'!");

?>