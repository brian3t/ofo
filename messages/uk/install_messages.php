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


	// ����������� ����������
	define("INSTALL_TITLE", "I�������i� ViArt SHOP");

	define("INSTALL_STEP_1_TITLE", "I�������i�: ���� 1");
	define("INSTALL_STEP_1_DESC", "������ �� ���i� ViArt SHOP. ��� ����, ��� ���i����� ������ i�������i� ����-����� ������i�� ��i �����i��i ����i. ��������, �� ����, ��� �� ������ ��������������� ��� �� ���� ��������. ���� �� ������������� ����, �� ����������� ODBC, ��������� MS Access, ��� ������� ��� �����i��� �������� ����� DSN ��� ���� ��� ����������.");
	define("INSTALL_STEP_2_TITLE", "I�������i�: ���� 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "I�������i�: ���� 3");
	define("INSTALL_STEP_3_DESC", "����-����� ������� ������ �����. ����� �� ������� ���� ������ �� �����.");
	define("INSTALL_FINAL_TITLE", "I�������i�: ����������");
	define("SELECT_DATE_TITLE", "���i� ������� ����");

	define("DB_SETTINGS_MSG", "������������ ����");
	define("DB_PROGRESS_MSG", "������� ��������� ��������� ����");
	define("SELECT_PHP_LIB_MSG", "�����i�� PHP �i��i�����");
	define("SELECT_DB_TYPE_MSG", "�����i�� ��� ����");
	define("ADMIN_SETTINGS_MSG", "������������ ���i�i��������");
	define("DATE_SETTINGS_MSG", "������� ����");
	define("NO_DATE_FORMATS_MSG", "���� ������� ���������� ������� ����");
	define("INSTALL_FINISHED_MSG", "�� ����� ����i ���� ������ i�������i� ���������. ����-����� �����i��� ��i ���i ������������ � ���i�i�������i� ������i i ����i�� ��i �����i��i ��i��.");
	define("ACCESS_ADMIN_MSG", "��� ������� �� ���i�i��������� ������� ����-����� ������i�� ���");
	define("ADMIN_URL_MSG", "��������� �� ���i�i��������� �������");
	define("MANUAL_URL_MSG", "��������� �� ������������");
	define("THANKS_MSG", "������ �� ���i� <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "��� ����");
	define("DB_TYPE_DESC", "������� <b>��� ���� �����</b> �� ���������������. ���� ��������������� SQL Server ��� Microsoft Access, ������� ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP �i��i�����");
	define("DB_HOST_FIELD", "����� �����");
	define("DB_HOST_DESC", "������ <b>�����</b> ��� <b>IP ������ �������</b> �� ����� ���� ���� ����� ViArt  ���� ��������. ���� ���� ����� ���������� �� ���������� ����'����, ������ ������� ����� ������� \"<b>localhost</b>\", � �������� ����� �������. ���� ��������������� ���� ����� ������ ������, �������������� ������������ ������ ������ ��� ��������� �����������.");
	define("DB_PORT_FIELD", "����");
	define("DB_NAME_FIELD", "I�'� ���� / DSN");
	define("DB_NAME_DESC", "���� ��������������� ���� ����� ���� �� MySQL ��� PostgreSQL, ������ <b>����� ���� �����</b> �� ���� ����������� ������� ViArt. ���� ����� ������� ���� ��� ��������. ���� ViArt �������������� ���� ��� ���������� �� �������� �����, �������� ������ ����� ������� (\"<b>test</b>\") ���� �����, ��� �� ������ �����������. ���� �, ������� ���� �����, � ������ ��������� 'viart'. ���� ��������������� Microsoft Access ��� SQL Server ��� ����� ���� ����� ������� ���� <b>������ DSN</b> ��� ����������� � ������ Data Sources (ODBC) ����� ���������.");
	define("DB_USER_FIELD", "����������");
	define("DB_PASS_FIELD", "������");
	define("DB_USER_PASS_DESC", "<b>�'�� �����������</b> �� <b>������</b> - ������ ��'� �� ������ ��� ������� �� ���� �����. ���� ��������������� �������� ������� ���������� ��'� �� ������� �� \"<b>root</b>\", � ������ �� ������� �������. �� ������ ��� ����������, ����� ������ ������� �� �������������� ��� ����� �� ����� �� ���������� ��������.");
	define("DB_PERSISTENT_FIELD", "����i���� ��'����");
	define("DB_PERSISTENT_DESC", "��� ������������ ��������� �'������� � MySQL ��� Postgre �����. ���� �� �� ����� �� �� ������, ����� ������� �� �.");
	define("DB_CREATE_DB_FIELD", "�������� ���� �����");
	define("DB_CREATE_DB_DESC", "��� ��������� ���� ����� (����� MySQL �� Postgre) ��������� ��� ");
	define("DB_POPULATE_FIELD", "��������� ����");
	define("DB_POPULATE_DESC", "��� ���� ��� �������� ��������� ������� �� ��������� ��, �������� ���");
	define("DB_TEST_DATA_FIELD", "������ ���");
	define("DB_TEST_DATA_DESC", "�������� ���, ��� ������ ���� ������ ���");
	define("ADMIN_EMAIL_FIELD", "���������� ������ ���i�i��������");
	define("ADMIN_LOGIN_FIELD", "���i� ���i�i��������");
	define("ADMIN_PASS_FIELD", "������ ���i�i��������");
	define("ADMIN_CONF_FIELD", "�i����������� ������");
	define("DATETIME_SHOWN_FIELD", "������ ���� � ����� (�i���������� �� ����i)");
	define("DATE_SHOWN_FIELD", "������ ���� (�i���������� �� ����i)");
	define("DATETIME_EDIT_FIELD", "������ ���� � ����� (��� �����������)");
	define("DATE_EDIT_FIELD", "������ ���� (��� �����������)");
	define("DATE_FORMAT_COLUMN", "������ ���� ");
	define("CURRENT_DATE_COLUMN", "������� ����");

	define("DB_LIBRARY_ERROR", "PHP ������� ��� {db_library} �� ��������. �������� ������������ ���� ����� � ���� ������������ - php.ini.");
	define("DB_CONNECT_ERROR", "�� ����� ���������� ��'���� � �����. �����i��� ��������� ��'���� � �����.");
	define("INSTALL_FINISHED_ERROR", "������ i�������i� ��� ���������.");
	define("WRITE_FILE_ERROR", "���� ���� ��� ��i�� ����� <b>'includes/var_definition.php'</b>. ����-����� ��i�i�� ����� ������� ����� ��� �� ����������.");
	define("WRITE_DIR_ERROR", "���� ���� ��� ������ � ����� <b>'includes/'</b>. ����-����� ��i�i�� ����� ������� ����� ��� �� ����������.");
	define("DUMP_FILE_ERROR", "���� ���� '{file_name}' �� ��������.");
	define("DB_TABLE_ERROR", "�������� '{table_name}' �� ��������. ����-����� ������i�� ���� �����i���� i�������i��.");
	define("TEST_DATA_ERROR", "�������� �������� �������� <b>{POPULATE_DB_FIELD}</b>, ���� ������ ������ ������ ���");
	define("DB_HOST_ERROR", "�� �������� �������� ������.");
	define("DB_PORT_ERROR", "��������� ��'�������� �� ������� ���� ����� �� ��������� �������.");
	define("DB_USER_PASS_ERROR", "������ ��'� ����������� �� ������ �� ���");
	define("DB_NAME_ERROR", "�'�� �� ������ ���, ��� ���� '{db_name}' �� ��������.");

	// ����������� ��������
	define("UPGRADE_TITLE", "��������� ���� �����");
	define("UPGRADE_NOTE", "����i���: ����-����� �����i�� ���i� ���� ������ ����� ��������.");
	define("UPGRADE_AVAILABLE_MSG", "���� ����i� ��������");
	define("UPGRADE_BUTTON", "�������� �� {version_number}");
	define("CURRENT_VERSION_MSG", "���� ������� ����i�");
	define("LATEST_VERSION_MSG", "����i� �������� ��� i�������i�");
	define("UPGRADE_RESULTS_MSG", "���������� ����������");
	define("SQL_SUCCESS_MSG", "SQL �����i� ���i����");
	define("SQL_FAILED_MSG", "SQL �����i� � ���������");
	define("SQL_TOTAL_MSG", "������ SQL �����i� ��������");
	define("VERSION_UPGRADED_MSG", "���� ����i� ���� ��������� ��");
	define("ALREADY_LATEST_MSG", "� ��� ����� ������� �������� ����i�");
	define("DOWNLOAD_NEW_MSG", "�������� �������� �����");
	define("DOWNLOAD_NOW_MSG", "�������� ����� {version_number} �����");
	define("DOWNLOAD_FOUND_MSG", "�'������� �� ����� {version_number} �������� ��� ����������. �������� �� ��������� ��� ��������� ����������. ϳ��� ��������� ���������� �� ����� ����� �� �������� ����� ��������� ��������� ���������.");
	define("NO_XML_CONNECTION", "�����! ³����� �'������� � 'http://www.viart.com/'!");

?>