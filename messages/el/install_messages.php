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
	define("INSTALL_TITLE", "����������� ViArt SHOP");

	define("INSTALL_STEP_1_TITLE", "����������� ���� 1");
	define("INSTALL_STEP_1_DESC", "��� ������������ ��� ��� ������� ��� ������������ ViArt SHOP. <br>����������� ����������� ���� ��� ������������ ��� �� ��� ��������.<br>�������� ��� ����� ���� �� ������� ��� � ���� ���������. ��� �������������� ��� ���� ��������� ��� ODBC, �.�. MS Access  ������ ����� �� ������������� ��  dsn ������ � ����������� ��� �� ����������.");
	define("INSTALL_STEP_2_TITLE", "����������� ���� 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "����������� ���� 3");
	define("INSTALL_STEP_3_DESC", "�������� ��� �������� ��� ����������� ��� , �������� �������� �� �������� ��� ������� ���.");
	define("INSTALL_FINAL_TITLE", "����� ������������");
	define("SELECT_DATE_TITLE", "�������� ��� �� ���������� � ����������");

	define("DB_SETTINGS_MSG", "��������� ����� ���������");
	define("DB_PROGRESS_MSG", "Populating database structure progress");
	define("SELECT_PHP_LIB_MSG", "�������� ���������� PHP");
	define("SELECT_DB_TYPE_MSG", "�������� ���� ����� ���������");
	define("ADMIN_SETTINGS_MSG", "��������� �����������");
	define("DATE_SETTINGS_MSG", "��������� �����������");
	define("NO_DATE_FORMATS_MSG", "��� �������� ���������� �������� �����������");
	define("INSTALL_FINISHED_MSG", "�� ���� �� ������ � ����������� ������������ , �������� ����������� ��� ����� ����� ��� ������ �����������");
	define("ACCESS_ADMIN_MSG", "��� �������� ��� ������� ����� ����� ���� �Ŀ");
	define("ADMIN_URL_MSG", "URL �����������");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", "������������ ��� ��������� �� <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "����� ����� ���������");
	define("DB_TYPE_DESC", "Please select the <b>type of database</b> that you are using. If you using SQL Server or Microsoft Access, please select ODBC.");
	define("DB_PHP_LIB_FIELD", "���������� PHP");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "Please enter the <b>name</b> or <b>IP address of the server</b> on which your ViArt database will run. If you are running your database on your local PC then you can probably just leave this as \"<b>localhost</b>\" and the port blank. If you using a database provided by your hosting company, please see your hosting company's documentation for the server settings.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "����� ����� ��������� / DSN");
	define("DB_NAME_DESC", "If you are using a database such as MySQL or PostgreSQL then please enter the <b>name of the database</b> where you would like ViArt to create its tables. This database must exist already. If you are just installing ViArt for testing purposes on your local PC then most systems have a \"<b>test</b>\" database you can use. If not, please create a database such as \"viart\" and use that. If you are using Microsoft Access or SQL Server then the Database Name should be the <b>name of the DSN</b> that you have set up in the Data Sources (ODBC) section of your Control Panel.");
	define("DB_USER_FIELD", "����� ������");
	define("DB_PASS_FIELD", "�������");
	define("DB_USER_PASS_DESC", "<b>Username</b> and <b>Password</b> - please enter the username and password you want to use to access the database. If you are using a local test installation the username is probably \"<b>root</b>\" and the password is probably blank. This is fine for testing, but please note that this is not secure on production servers.");
	define("DB_PERSISTENT_FIELD", "Persistent Connection");
	define("DB_PERSISTENT_DESC", "to use MySQL or Postgre persistent connections, tick this box. If you do not know what it means, then leaving it unticked is probably best.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "������� ��� ���� ���������");
	define("DB_POPULATE_DESC", "��������� ��� ��� ������������� ��� ���� ��� ����� ��� �� ������������ �� �������� ���");
	define("DB_TEST_DATA_FIELD", "Test Data");
	define("DB_TEST_DATA_DESC", "to add some test data to your database tick the checkbox");
	define("ADMIN_EMAIL_FIELD", "E-mail �����������");
	define("ADMIN_LOGIN_FIELD", "������� �����������");
	define("ADMIN_PASS_FIELD", "������� �����������");
	define("ADMIN_CONF_FIELD", "���������� ��� ������");
	define("DATETIME_SHOWN_FIELD", "�������� ����������� ��� ����(���� ����������)");
	define("DATE_SHOWN_FIELD", "�������� ����������� (���� ����������)");
	define("DATETIME_EDIT_FIELD", "���������� ��� ��� (��� �����������)");
	define("DATE_EDIT_FIELD", "���������� (��� �����������)");
	define("DATE_FORMAT_COLUMN", "�������� �����������");
	define("CURRENT_DATE_COLUMN", "�������� ����������");

	define("DB_LIBRARY_ERROR", "PHP functions for {db_library} are not defined. Please check your database settings in your configuration file - php.ini.");
	define("DB_CONNECT_ERROR", "��� ����� �� ������������ �� ��� ���� ��������� , �������� ����� ��� �����������");
	define("INSTALL_FINISHED_ERROR", "� ���������� ������������ ���� ��� ���������");
	define("WRITE_FILE_ERROR", "��� ����� ����� �� �������� ���� �� ������ <b>'includes/var_definition.php'</b>.�������� ������� ��� ��������� ��� ������� ���� �� ����������");
	define("WRITE_DIR_ERROR", "��� ����� ����� �� �������� ����� ��� ������ <b>'includes/'</b>.�������� ������� ��� ��������� ��� ������� ���� �� ����������");
	define("DUMP_FILE_ERROR", "�� ������ '{file_name}' ��� ������� !");
	define("DB_TABLE_ERROR", "� ������� '{table_name} ��� ������� ! �������� ��������� ���� ���� ��������� �� ���������� ��������.");
	define("TEST_DATA_ERROR", "Check <b>{POPULATE_DB_FIELD}</b> before populating tables with test data");
	define("DB_HOST_ERROR", "The hostname that you specified could not be found.");
	define("DB_PORT_ERROR", "Can't connect to database server using specified port.");
	define("DB_USER_PASS_ERROR", "The username or password you specified is not correct.");
	define("DB_NAME_ERROR", "Login settings were correct, but the database '{db_name}' could not be found.");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP ����������");
	define("UPGRADE_NOTE", "�������� : ����������� ��� ���� ��������� ���� ����������.");
	define("UPGRADE_AVAILABLE_MSG", "����� ��������� ���������� ��� ������������");
	define("UPGRADE_BUTTON", "����������� �� {version_number} ���� ");
	define("CURRENT_VERSION_MSG", "����� ��� ������������ ����� ��� ������");
	define("LATEST_VERSION_MSG", "������ ��������� ��� ��������");
	define("UPGRADE_RESULTS_MSG", "������������ �����������");
	define("SQL_SUCCESS_MSG", "SQL ��������� �������");
	define("SQL_FAILED_MSG", "SQL ��������� ����������");
	define("SQL_TOTAL_MSG", "��� �� SQL ��������� ����� ���������������");
	define("VERSION_UPGRADED_MSG", "� ������ ��� ���� ������������ ��");
	define("ALREADY_LATEST_MSG", "����� ��� ��� ��� �������� ������");
	define("DOWNLOAD_NEW_MSG", "The new version was detected");
	define("DOWNLOAD_NOW_MSG", "Download version {version_number} now");
	define("DOWNLOAD_FOUND_MSG", "We have detected that the new {version_number} version is available to download. Please click the link below to start downloading. After completing the download and replacing the files don't forget to run Upgrade routine again.");
	define("NO_XML_CONNECTION", "Warning! No connection to 'http://www.viart.com/' available!");

?>