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
	define("INSTALL_TITLE", "ViArt SHOP のインス�栫[ル");

	define("INSTALL_STEP_1_TITLE", "インス�栫[ル: ステップ 1");
	define("INSTALL_STEP_1_DESC", "ViArt SHOP をご利用いただ��ありがとうございます。 インス�栫[ルを続��るには、以下に必要な情報をご記入��ださい。 選択したデータベースは既に存在している必要があります。 Microsoft Access などの ODBC を使用するデータベースをインス�栫[ルする場合、インス�栫[ルを続��る前に DNS を作成して��ださい。");
	define("INSTALL_STEP_2_TITLE", "インス�栫[ル: ステップ 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "インス�栫[ル: ステップ 3");
	define("INSTALL_STEP_3_DESC", "サイ�桙ﾌレイアウ�桙�選択して��ださい。 後でレイアウ�桙�変更する��ともで��ます。");
	define("INSTALL_FINAL_TITLE", "インス�栫[ル:最終ステップ");
	define("SELECT_DATE_TITLE", "日付フォーマッ�桙�選択");

	define("DB_SETTINGS_MSG", "データベース設定");
	define("DB_PROGRESS_MSG", "データベースシステムのデータ入力を進める");
	define("SELECT_PHP_LIB_MSG", "PHP ライブラリーを選択");
	define("SELECT_DB_TYPE_MSG", "データベースの形式を選択");
	define("ADMIN_SETTINGS_MSG", "管理情報の設定");
	define("DATE_SETTINGS_MSG", "日付フォーマッ��");
	define("NO_DATE_FORMATS_MSG", "有効な日付フォーマッ�桙ｪありません");
	define("INSTALL_FINISHED_MSG", "インス�栫[ルは完了しました。 管理�怎刄Vョンの設定を確認し、必要な変更を行って��ださい。");
	define("ACCESS_ADMIN_MSG", "管理�怎刄Vョンにア�刄怎Xするには����を�刄潟b�凾ｵて��ださい。");
	define("ADMIN_URL_MSG", "管理 URL");
	define("MANUAL_URL_MSG", "マニュアル URL");
	define("THANKS_MSG", "<b>ViArt SHOP</b> をご利用いただ��ありがとうございます。");

	define("DB_TYPE_FIELD", "データベースの形式");
	define("DB_TYPE_DESC", "現在、使用している <b>データベース タイプ</b> を選択して��ださい。 SQL Server または Microsoft Access をご使用の場合は、 ODBC を選択して��ださい");
	define("DB_PHP_LIB_FIELD", "PHP ライブラリ");
	define("DB_HOST_FIELD", "ホス�椁ｼ");
	define("DB_HOST_DESC", "ViArt のデータベースを実行する <b>サーバーの名前</b> または <b>IP アドレス</b> を入力して��ださい。 ロー�翼� PC でデータベースを実行する場合、��れを ＼"<b>localhost</b>＼" のままでポー�桙ﾍ空欄にする��とがで��ます。 ホスティング会社��ら提供されたデータベースを使用する場合、ホスティング会社のド��ュメン�桙ﾌサーバー設定項目を参照して��ださい。");
	define("DB_PORT_FIELD", "ポー��");
	define("DB_NAME_FIELD", "データベース名または DSN 名");
	define("DB_NAME_DESC", "MySQL または PostgreSQL をご使用の場合、 ViArt のテーブルを作成したい <b>データベース名</b> を入力して��ださい。 ��のデータベースは既に存在している必要があります。 テス�桙�行うために ViArt をロー�翼� PC にインス�栫[ルする場合、システムにより ＼"<b>test</b>＼" データベースが提供されます。 そうではない場合、 ＼"viart＼" のようなデータベースを作成し、それを使用して��ださい。 Microsoft Access または SQL Server を使用する場合、データベース名は�寃塔档香[ル パネルの ODBC �怎刄Vョンで設定した <b>DSN 名</b> です。");
	define("DB_USER_FIELD", "ユーザー名");
	define("DB_PASS_FIELD", "パスワード");
	define("DB_USER_PASS_DESC", "<b>ユーザー名/b>と<b>パスワード</b> - データベースにア�刄怎Xするためのユーザー名とパスワードを入力して��ださい。 ロー�翼泣eス�档Cンス�栫[ルをご利用の場合、大抵の場合ユーザー名は ＼"<b>root</b>＼" 、パスワードは空白です。 テス�桙ﾅは問題ありませんが、本番のサーバーでは安全性に欠��るため、ご注意��ださい。");
	define("DB_PERSISTENT_FIELD", "持続接続");
	define("DB_PERSISTENT_DESC", "MySQL または Postgre の持続接続を使用する場合、��のチェッ�刄{ッ�刄Xをチェッ�凾ｵて��ださい。 わ��らない場合は、チェッ�凾�しないで��ださい。");
	define("DB_CREATE_DB_FIELD", "DB 作成");
	define("DB_CREATE_DB_DESC", "データベースを作成するには、��のチェッ�刄{ッ�刄Xをチェッ�凾ｵて��ださい。 MySQL および Postgre のみに有効です。");
	define("DB_POPULATE_FIELD", "DB へデータ入力");
	define("DB_POPULATE_DESC", "データベース テーブル ス�档宴刄`ャーを作成しデータを入力するにはチェッ�刄{ッ�刄Xをチェッ�凾ｵて��ださい。");
	define("DB_TEST_DATA_FIELD", "テス�档fータ");
	define("DB_TEST_DATA_DESC", "テス�档fータをデータベースに追加するには、チェッ�刄{ッ�刄Xをチェッ�凾ｵて��ださい。");
	define("ADMIN_EMAIL_FIELD", "管理者メールアドレス");
	define("ADMIN_LOGIN_FIELD", "管理者ログイン");
	define("ADMIN_PASS_FIELD", "管理者パスワード");
	define("ADMIN_CONF_FIELD", "パスワードの確認");
	define("DATETIME_SHOWN_FIELD", "日時フォーマッ�� (サイ�桾\示用)");
	define("DATE_SHOWN_FIELD", "日付フォーマッ�� (サイ�桾\示用)");
	define("DATETIME_EDIT_FIELD", "日時フォーマッ�� (編集用)");
	define("DATE_EDIT_FIELD", "日付フォーマッ�� (編集用)");
	define("DATE_FORMAT_COLUMN", "日付フォーマッ��");
	define("CURRENT_DATE_COLUMN", "現在の日付");

	define("DB_LIBRARY_ERROR", "{db_library} 向��にPHP機能が定義されていません。 php.ini 設定ファイルのデータベース設定を確認して��ださい。");
	define("DB_CONNECT_ERROR", "データベースに接続で��ません。 データベースパラメーターを確認して��ださい。");
	define("INSTALL_FINISHED_ERROR", "インス�栫[ルが完了しました。");
	define("WRITE_FILE_ERROR", "<b>'includes/var_definition.php'</b> ファイルに書��込み権限がありません。 ファイルのア�刄怎X権限を変更して��ださい。");
	define("WRITE_DIR_ERROR", "<b>'includes/'</b> フォルダに書��込み権限がありません。 フォルダのア�刄怎X権限を変更して��ださい。");
	define("DUMP_FILE_ERROR", "ダンプファイル '{file_name}' は見つ��りませんでした。");
	define("DB_TABLE_ERROR", "{table_name}' テーブルは見つ��りませんでした。 データーベースに必要なデータを入力して��ださい。");
	define("TEST_DATA_ERROR", "テス�档fータでテーブルを作成する前に <b>{POPULATE_DB_FIELD}</b> を確認して��ださい。");
	define("DB_HOST_ERROR", "指定されたホス�档lーム名は見つ��りませんでした。");
	define("DB_PORT_ERROR", "指定されたポー�桙ﾅデータベースサーバーに接続で��ません。");
	define("DB_USER_PASS_ERROR", "指定されたパスワードまたはユーザー名は正し��ありません。");
	define("DB_NAME_ERROR", "ログイン設定は正常でしたが、 '{db_name}' データベースは見つ��りませんでした。");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt ショップアップグレード");
	define("UPGRADE_NOTE", "注意 : 先へ進む前にデータベースのバッ�刄Aップをとって��ださい。");
	define("UPGRADE_AVAILABLE_MSG", "データベースのアップグレードが可能です");
	define("UPGRADE_BUTTON", "今すぐにデータベースを {version_number} にアップグレード");
	define("CURRENT_VERSION_MSG", "現在インス�栫[ルされているバージョン");
	define("LATEST_VERSION_MSG", "インス�栫[ル可能なバージョン");
	define("UPGRADE_RESULTS_MSG", "アップグレードの結果");
	define("SQL_SUCCESS_MSG", "SQL �刄Gリーに成功しました");
	define("SQL_FAILED_MSG", "SQL �刄Gリーに失敗しました");
	define("SQL_TOTAL_MSG", "実行された総 SQＬ �刄Gリー数");
	define("VERSION_UPGRADED_MSG", "データベースは次のバージョンにアップグレードされました:");
	define("ALREADY_LATEST_MSG", "最新バージョンになっています");
	define("DOWNLOAD_NEW_MSG", "最新バージョンが検出されました");
	define("DOWNLOAD_NOW_MSG", "今すぐにバージョン {version_number} をダウンロード");
	define("DOWNLOAD_FOUND_MSG", "新しいバージョン {version_number} をダウンロードする��とがで��ます。 ダウンロードを開始するには下のリン�凾��刄潟b�凾ｵて��ださい。 ダウンロードを完了後、アップグレードルーチンを再起動して��ださい。");
	define("NO_XML_CONNECTION", "警告! 'http://www.viart.com/' に接続で��ません。");

?>