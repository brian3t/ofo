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
	define("INSTALL_TITLE", "Cài ð£t ViArt SHOP");

	define("INSTALL_STEP_1_TITLE", "Cài ð£t: Bß¾c 1");
	define("INSTALL_STEP_1_DESC", "Cám ½n bÕn ðã ch÷n ViArt SHOP. Ð¬ tiªp tøc cài ð£t, xin hãy ði«n chi  tiªt yêu c¥u dß¾i ðây. Xin lßu ý r¢ng database cüa bÕn phäi t°n tÕi trong máy chü. Nªu bÕn ch÷n cài ð£t database b¢ng MS Access v¾i kªt n¯i ODBC xin hãy tÕo DSN ð¥u tiên trß¾c khi tiªp tøc quá trình này.");
	define("INSTALL_STEP_2_TITLE", "Cài ð£t: Bß¾c 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Cài ð£t: Bß¾c 3");
	define("INSTALL_STEP_3_DESC", "Xin hãy ch÷n ki¬u hi¬n th¸ trang web. BÕn s¨ thay ð±i nó sau này.");
	define("INSTALL_FINAL_TITLE", "Cài ð£t: Hoàn t¤t");
	define("SELECT_DATE_TITLE", "Ch÷n ð¸nh dÕng ngày tháng");

	define("DB_SETTINGS_MSG", "Các thiªt l§p database");
	define("DB_PROGRESS_MSG", "TÕo c¤u trúc database");
	define("SELECT_PHP_LIB_MSG", "Ch÷n thß vi®n PHP");
	define("SELECT_DB_TYPE_MSG", "Ch÷n ki¬u database");
	define("ADMIN_SETTINGS_MSG", "Các thiªt l§p v« quän tr¸");
	define("DATE_SETTINGS_MSG", "Các ð¸nh dÕng ngày tháng");
	define("NO_DATE_FORMATS_MSG", "Không có sÇn các ð¸nh dÕng ngày tháng");
	define("INSTALL_FINISHED_MSG", "TÕi th¶i ði¬m này quá trình cài ð£t c½ bän ðã hoàn thành. Xin vui lòng ki¬m các thiªt l§p · khu vñc quän tr¸ và nhæng thay ð±i b¡t buµc ");
	define("ACCESS_ADMIN_MSG", "Ð¬ truy c§p vào khu vñc quän tr¸ ch÷n · ðây");
	define("ADMIN_URL_MSG", "Ðß¶ng dçn khu vñc quän tr¸");
	define("MANUAL_URL_MSG", "Tñ vào ðß¶ng dçn");
	define("THANKS_MSG", "Cám ½n bÕn ðã ch÷n <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Ki¬u database");
	define("DB_TYPE_DESC", "Xin vui lòng ch÷n <b>ki¬u database</b> mà bÕn sØ døng. Nªu bÕn sØ døng SQL Server ho£c MS Access, xin vui lòng ch÷n ODBC");
	define("DB_PHP_LIB_FIELD", "Thß vi®n PHP");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "Xin vui lòng ði«n vào <b>tên</b> ho£c <b>ð¸a chï IP cüa máy chü</b> mà database cüa ViArt s¨ chÕy. Nªu bÕn sØ døng database trên máy chü nµi bµ xin vui lòng ð¬ tr¯ng ho£c gõ <b>localhost</b>. Nªu bÕn cài ð£t trên máy chü hosting xin vui lòng xem lÕi tài li®u ho£c liên h® v¾i bµ ph§n h² trþ d¸ch vø hosting.");
	define("DB_PORT_FIELD", "C±ng");
	define("DB_NAME_FIELD", "Tên database/DSN");
	define("DB_NAME_DESC", "Nªu bÕn sØ døng database MySQL ho£c PostgreSQL xin vui lòng gõ vào <b>tên database</b>. Database này phäi t°n tÕi sÇn. Nªu bÕn cài ð£t ViArt SHOP v¾i møc ðích test trên máy nµi bµ bÕn có th¬ ði«n vào <b>test</b>. Nªu không bÕn có th¬ gõ vào <b>Viart</b> và sØ døng nó. Nªu bÕn sØ døng MS Access ho£c SQL Server xin hãy ði«n vào <b>tên DSN</b> mà bÕn thiªt l§p trong kªt n¯i ODBC · khu vñc Control Panel.");
	define("DB_USER_FIELD", "Tên truy c§p");
	define("DB_PASS_FIELD", "M§t mã");
	define("DB_USER_PASS_DESC", "<b>Tên truy c§p<b> và <b>M§t mã</b> - Xin vui lòng ði«n vào tên truy c§p và m§t mã mà bÕn sØ døng ð¬ truy c§p ðßþc database. Nªu bÕn dùng v¾i møc ðích ð¬ test xin vui lòng gõ vào \"<b>root</b>\" và m§t mã ð¬ tr¯ng. Xin lßu ý ð×ng dùng nó cho møc ðích ðßa lên Internet vì nó không an toàn v« bäo m§t.");
	define("DB_PERSISTENT_FIELD", "Persistent Connection");
	define("DB_PERSISTENT_DESC", "ð¬ sØ døng ki¬u kªt n¯i persistent v¾i MySQL và PostgresSQL, ch÷n · ô này. Nªu bÕn không ch¡c ch¡n, xin hãy bö qua.");
	define("DB_CREATE_DB_FIELD", "TÕo DB");
	define("DB_CREATE_DB_DESC", "ð¬ tÕo database, nªu ðßþc xin ch÷n ô này. ChÑc nång chï hoÕt ðµng v¾i ki¬u dæ li®u MySQL và Postgre");
	define("DB_POPULATE_FIELD", "Ðßa vào dæ li®u mçu");
	define("DB_POPULATE_DESC", "Ð¬ tÕo c¤u trúc database xin ch÷n vào ô này");
	define("DB_TEST_DATA_FIELD", "Ki¬m tra dæ li®u");
	define("DB_TEST_DATA_DESC", "ð¬ thêm vào mµt vài dæ li®u ð¬ ki¬m tra xin ch÷n ô này");
	define("ADMIN_EMAIL_FIELD", "Email ngß¶i quän tr¸");
	define("ADMIN_LOGIN_FIELD", "Tên truy c§p admin");
	define("ADMIN_PASS_FIELD", "M§t mã cüa admin");
	define("ADMIN_CONF_FIELD", "Xác nh§n lÕi m§t mã");
	define("DATETIME_SHOWN_FIELD", "Ð¸nh dÕng gi¶ (trên trang web)");
	define("DATE_SHOWN_FIELD", "Ð¸nh dÕng ngày tháng (trên trang web)");
	define("DATETIME_EDIT_FIELD", "Ð¸nh dÕng gi¶ (trên trang quän tr¸)");
	define("DATE_EDIT_FIELD", "Ð¸nh dÕng ngày tháng (trên trang quän tr¸)");
	define("DATE_FORMAT_COLUMN", "Ð¸nh dÕng ngày");
	define("CURRENT_DATE_COLUMN", "Ngày hi®n tÕi");

	define("DB_LIBRARY_ERROR", "Các hàm chÑc nång PHP cho {db_library} không ðßþc ð¸nh nghîa. Xin vui lòng ki¬m tra lÕi thiªt l§p database trong t®p c¤u hình php.ini.");
	define("DB_CONNECT_ERROR", "Không th¬ kªt n¯i database. Xin vui lòng ki¬m tra tham s¯ database.");
	define("INSTALL_FINISHED_ERROR", "Quá trình cài ð£t ðã hoàn t¤t.");
	define("WRITE_FILE_ERROR", "Không có quy«n ghi t®p dæ li®u <b>'includes/var_definition.php'</b>. Xin vui lòng thay ð±i quy«n truy c§p trß¾c khi thñc hi®n bß¾c này.");
	define("WRITE_DIR_ERROR", "Không có quy«n ghi thß møc <b>'includes/'</b>. Xin vui lòng thay ð±i quy«n cho thß møc trß¾c khi tiªp tøc.");
	define("DUMP_FILE_ERROR", "T®p '{file_name}' không tìm th¤y.");
	define("DB_TABLE_ERROR", "Table '{table_name}' không tìm th¤y. Xin vui lòng tÕo database v¾i dæ li®u c¥n thiªt.");
	define("TEST_DATA_ERROR", "Ki¬m tra <b>{POPULATE_DB_FIELD}</b> trß¾c khi tÕo bäng table khi ki¬m tra dæ li®u");
	define("DB_HOST_ERROR", "Hostname mà bÕn mô tä không th¬ tìm th¤y.");
	define("DB_PORT_ERROR", "Không th¬ kªt n¯i máy chü database v¾i c±ng hi®n tÕi.");
	define("DB_USER_PASS_ERROR", "Tên truy c§p và m§t mã bÕn mô tä không ðúng.");
	define("DB_NAME_ERROR", "Các thiªt l§p ðång nh§p là ðúng, nhßng database '{db_name}' không th¬ tìm th¤y.");

	// upgrade messages
	define("UPGRADE_TITLE", "Nâng c¤p ViArt SHOP");
	define("UPGRADE_NOTE", "Lßu ý: Xin hãy lßu ý r¢ng ch¡c ch¡n backup database trß¾c khi tiªp tøc.");
	define("UPGRADE_AVAILABLE_MSG", "Ðã sÇn sàng c§p nh§t database");
	define("UPGRADE_BUTTON", "C§p nh§t database ðªn {version_number} ngay");
	define("CURRENT_VERSION_MSG", "Phiên bän hi®n tÕi ðã cài ð£t");
	define("LATEST_VERSION_MSG", "Phiên bän m¾i ð¬ cài ð£t");
	define("UPGRADE_RESULTS_MSG", "Kªt quä nâng c¤p");
	define("SQL_SUCCESS_MSG", "Truy v¤n SQL thành công");
	define("SQL_FAILED_MSG", "Truy v¤n SQL b¸ l²i");
	define("SQL_TOTAL_MSG", "T¤t cä truy v¤n SQL thi hành");
	define("VERSION_UPGRADED_MSG", "Database cüa bÕn ðßþc nâng c¤p ðªn");
	define("ALREADY_LATEST_MSG", "BÕn ðã c§p nh§t phiên bän m¾i nh¤t");
	define("DOWNLOAD_NEW_MSG", "Phiên bän m¾i ðßþc ki¬m tra");
	define("DOWNLOAD_NOW_MSG", "Täi xu¯ng phiên bän {version_number} ngay.");
	define("DOWNLOAD_FOUND_MSG", "Chúng tôi ðã ki¬m tra phiên bän {version_number} ðã sÇn sàng cho vi®c täi xu¯ng. Nh¤n ch÷n ðß¶ng link dß¾i ðây ð¬ täi xu¯ng. Sau khi hoàn t¤t täi xu¯ng và thay thª t®p dæ li®u, nh¾ ð×ng quên chÕy bän c§p nh§t tr· lÕi.");
	define("NO_XML_CONNECTION", "Cänh báo! Không có kªt n¯isÇn ðªn ð¸a chï 'http://www.viart.com/'!");

?>