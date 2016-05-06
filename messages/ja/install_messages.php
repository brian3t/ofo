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
	define("INSTALL_TITLE", "ViArt SHOP �̃C���X���[��");

	define("INSTALL_STEP_1_TITLE", "�C���X���[��: �X�e�b�v 1");
	define("INSTALL_STEP_1_DESC", "ViArt SHOP �������p�����������肪�Ƃ��������܂��B �C���X���[���𑱂���ɂ́A�ȉ��ɕK�v�ȏ������L�����������B �I�������f�[�^�x�[�X�͊��ɑ��݂��Ă���K�v������܂��B Microsoft Access �Ȃǂ� ODBC ���g�p����f�[�^�x�[�X���C���X���[������ꍇ�A�C���X���[���𑱂���O�� DNS ���쐬���Ă��������B");
	define("INSTALL_STEP_2_TITLE", "�C���X���[��: �X�e�b�v 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "�C���X���[��: �X�e�b�v 3");
	define("INSTALL_STEP_3_DESC", "�T�C���̃��C�A�E����I�����Ă��������B ��Ń��C�A�E����ύX������Ƃ��ł��܂��B");
	define("INSTALL_FINAL_TITLE", "�C���X���[��:�ŏI�X�e�b�v");
	define("SELECT_DATE_TITLE", "���t�t�H�[�}�b����I��");

	define("DB_SETTINGS_MSG", "�f�[�^�x�[�X�ݒ�");
	define("DB_PROGRESS_MSG", "�f�[�^�x�[�X�V�X�e���̃f�[�^���͂�i�߂�");
	define("SELECT_PHP_LIB_MSG", "PHP ���C�u�����[��I��");
	define("SELECT_DB_TYPE_MSG", "�f�[�^�x�[�X�̌`����I��");
	define("ADMIN_SETTINGS_MSG", "�Ǘ����̐ݒ�");
	define("DATE_SETTINGS_MSG", "���t�t�H�[�}�b��");
	define("NO_DATE_FORMATS_MSG", "�L���ȓ��t�t�H�[�}�b��������܂���");
	define("INSTALL_FINISHED_MSG", "�C���X���[���͊������܂����B �Ǘ������V�����̐ݒ���m�F���A�K�v�ȕύX���s���Ă��������B");
	define("ACCESS_ADMIN_MSG", "�Ǘ������V�����ɃA�����X����ɂ͂����������b�����Ă��������B");
	define("ADMIN_URL_MSG", "�Ǘ� URL");
	define("MANUAL_URL_MSG", "�}�j���A�� URL");
	define("THANKS_MSG", "<b>ViArt SHOP</b> �������p�����������肪�Ƃ��������܂��B");

	define("DB_TYPE_FIELD", "�f�[�^�x�[�X�̌`��");
	define("DB_TYPE_DESC", "���݁A�g�p���Ă��� <b>�f�[�^�x�[�X �^�C�v</b> ��I�����Ă��������B SQL Server �܂��� Microsoft Access �����g�p�̏ꍇ�́A ODBC ��I�����Ă�������");
	define("DB_PHP_LIB_FIELD", "PHP ���C�u����");
	define("DB_HOST_FIELD", "�z�X����");
	define("DB_HOST_DESC", "ViArt �̃f�[�^�x�[�X�����s���� <b>�T�[�o�[�̖��O</b> �܂��� <b>IP �A�h���X</b> ����͂��Ă��������B ���[���� PC �Ńf�[�^�x�[�X�����s����ꍇ�A����� �_"<b>localhost</b>�_" �̂܂܂Ń|�[���͋󗓂ɂ�����Ƃ��ł��܂��B �z�X�e�B���O��Ђ���񋟂��ꂽ�f�[�^�x�[�X���g�p����ꍇ�A�z�X�e�B���O��Ђ̃h�����������̃T�[�o�[�ݒ荀�ڂ��Q�Ƃ��Ă��������B");
	define("DB_PORT_FIELD", "�|�[��");
	define("DB_NAME_FIELD", "�f�[�^�x�[�X���܂��� DSN ��");
	define("DB_NAME_DESC", "MySQL �܂��� PostgreSQL �����g�p�̏ꍇ�A ViArt �̃e�[�u�����쐬������ <b>�f�[�^�x�[�X��</b> ����͂��Ă��������B ���̃f�[�^�x�[�X�͊��ɑ��݂��Ă���K�v������܂��B �e�X�����s�����߂� ViArt �����[���� PC �ɃC���X���[������ꍇ�A�V�X�e���ɂ�� �_"<b>test</b>�_" �f�[�^�x�[�X���񋟂���܂��B �����ł͂Ȃ��ꍇ�A �_"viart�_" �̂悤�ȃf�[�^�x�[�X���쐬���A������g�p���Ă��������B Microsoft Access �܂��� SQL Server ���g�p����ꍇ�A�f�[�^�x�[�X���̓��������[�� �p�l���� ODBC �����V�����Őݒ肵�� <b>DSN ��</b> �ł��B");
	define("DB_USER_FIELD", "���[�U�[��");
	define("DB_PASS_FIELD", "�p�X���[�h");
	define("DB_USER_PASS_DESC", "<b>���[�U�[��/b>��<b>�p�X���[�h</b> - �f�[�^�x�[�X�ɃA�����X���邽�߂̃��[�U�[���ƃp�X���[�h����͂��Ă��������B ���[�����e�X���C���X���[���������p�̏ꍇ�A���̏ꍇ���[�U�[���� �_"<b>root</b>�_" �A�p�X���[�h�͋󔒂ł��B �e�X���ł͖�肠��܂��񂪁A�{�Ԃ̃T�[�o�[�ł͈��S���Ɍ����邽�߁A�����ӂ��������B");
	define("DB_PERSISTENT_FIELD", "�����ڑ�");
	define("DB_PERSISTENT_DESC", "MySQL �܂��� Postgre �̎����ڑ����g�p����ꍇ�A���̃`�F�b���{�b���X���`�F�b�����Ă��������B �����Ȃ��ꍇ�́A�`�F�b�������Ȃ��ł��������B");
	define("DB_CREATE_DB_FIELD", "DB �쐬");
	define("DB_CREATE_DB_DESC", "�f�[�^�x�[�X���쐬����ɂ́A���̃`�F�b���{�b���X���`�F�b�����Ă��������B MySQL ����� Postgre �݂̂ɗL���ł��B");
	define("DB_POPULATE_FIELD", "DB �փf�[�^����");
	define("DB_POPULATE_DESC", "�f�[�^�x�[�X �e�[�u�� �X�������`���[���쐬���f�[�^����͂���ɂ̓`�F�b���{�b���X���`�F�b�����Ă��������B");
	define("DB_TEST_DATA_FIELD", "�e�X���f�[�^");
	define("DB_TEST_DATA_DESC", "�e�X���f�[�^���f�[�^�x�[�X�ɒǉ�����ɂ́A�`�F�b���{�b���X���`�F�b�����Ă��������B");
	define("ADMIN_EMAIL_FIELD", "�Ǘ��҃��[���A�h���X");
	define("ADMIN_LOGIN_FIELD", "�Ǘ��҃��O�C��");
	define("ADMIN_PASS_FIELD", "�Ǘ��҃p�X���[�h");
	define("ADMIN_CONF_FIELD", "�p�X���[�h�̊m�F");
	define("DATETIME_SHOWN_FIELD", "�����t�H�[�}�b�� (�T�C���\���p)");
	define("DATE_SHOWN_FIELD", "���t�t�H�[�}�b�� (�T�C���\���p)");
	define("DATETIME_EDIT_FIELD", "�����t�H�[�}�b�� (�ҏW�p)");
	define("DATE_EDIT_FIELD", "���t�t�H�[�}�b�� (�ҏW�p)");
	define("DATE_FORMAT_COLUMN", "���t�t�H�[�}�b��");
	define("CURRENT_DATE_COLUMN", "���݂̓��t");

	define("DB_LIBRARY_ERROR", "{db_library} ������PHP�@�\����`����Ă��܂���B php.ini �ݒ�t�@�C���̃f�[�^�x�[�X�ݒ���m�F���Ă��������B");
	define("DB_CONNECT_ERROR", "�f�[�^�x�[�X�ɐڑ��ł��܂���B �f�[�^�x�[�X�p�����[�^�[���m�F���Ă��������B");
	define("INSTALL_FINISHED_ERROR", "�C���X���[�����������܂����B");
	define("WRITE_FILE_ERROR", "<b>'includes/var_definition.php'</b> �t�@�C���ɏ������݌���������܂���B �t�@�C���̃A�����X������ύX���Ă��������B");
	define("WRITE_DIR_ERROR", "<b>'includes/'</b> �t�H���_�ɏ������݌���������܂���B �t�H���_�̃A�����X������ύX���Ă��������B");
	define("DUMP_FILE_ERROR", "�_���v�t�@�C�� '{file_name}' �͌�����܂���ł����B");
	define("DB_TABLE_ERROR", "{table_name}' �e�[�u���͌�����܂���ł����B �f�[�^�[�x�[�X�ɕK�v�ȃf�[�^����͂��Ă��������B");
	define("TEST_DATA_ERROR", "�e�X���f�[�^�Ńe�[�u�����쐬����O�� <b>{POPULATE_DB_FIELD}</b> ���m�F���Ă��������B");
	define("DB_HOST_ERROR", "�w�肳�ꂽ�z�X���l�[�����͌�����܂���ł����B");
	define("DB_PORT_ERROR", "�w�肳�ꂽ�|�[���Ńf�[�^�x�[�X�T�[�o�[�ɐڑ��ł��܂���B");
	define("DB_USER_PASS_ERROR", "�w�肳�ꂽ�p�X���[�h�܂��̓��[�U�[���͐���������܂���B");
	define("DB_NAME_ERROR", "���O�C���ݒ�͐���ł������A '{db_name}' �f�[�^�x�[�X�͌�����܂���ł����B");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt �V���b�v�A�b�v�O���[�h");
	define("UPGRADE_NOTE", "���� : ��֐i�ޑO�Ƀf�[�^�x�[�X�̃o�b���A�b�v���Ƃ��Ă��������B");
	define("UPGRADE_AVAILABLE_MSG", "�f�[�^�x�[�X�̃A�b�v�O���[�h���\�ł�");
	define("UPGRADE_BUTTON", "�������Ƀf�[�^�x�[�X�� {version_number} �ɃA�b�v�O���[�h");
	define("CURRENT_VERSION_MSG", "���݃C���X���[������Ă���o�[�W����");
	define("LATEST_VERSION_MSG", "�C���X���[���\�ȃo�[�W����");
	define("UPGRADE_RESULTS_MSG", "�A�b�v�O���[�h�̌���");
	define("SQL_SUCCESS_MSG", "SQL ���G���[�ɐ������܂���");
	define("SQL_FAILED_MSG", "SQL ���G���[�Ɏ��s���܂���");
	define("SQL_TOTAL_MSG", "���s���ꂽ�� SQ�k ���G���[��");
	define("VERSION_UPGRADED_MSG", "�f�[�^�x�[�X�͎��̃o�[�W�����ɃA�b�v�O���[�h����܂���:");
	define("ALREADY_LATEST_MSG", "�ŐV�o�[�W�����ɂȂ��Ă��܂�");
	define("DOWNLOAD_NEW_MSG", "�ŐV�o�[�W���������o����܂���");
	define("DOWNLOAD_NOW_MSG", "�������Ƀo�[�W���� {version_number} ���_�E�����[�h");
	define("DOWNLOAD_FOUND_MSG", "�V�����o�[�W���� {version_number} ���_�E�����[�h������Ƃ��ł��܂��B �_�E�����[�h���J�n����ɂ͉��̃������������b�����Ă��������B �_�E�����[�h��������A�A�b�v�O���[�h���[�`�����ċN�����Ă��������B");
	define("NO_XML_CONNECTION", "�x��! 'http://www.viart.com/' �ɐڑ��ł��܂���B");

?>