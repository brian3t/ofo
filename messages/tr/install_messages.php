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


	// kurulum iletileri
	define("INSTALL_TITLE", "ViArt SHOP Kurulumu");

	define("INSTALL_STEP_1_TITLE", "Kurulum: Adým 1");
	define("INSTALL_STEP_1_DESC", "ViArt SHOP 'u seçtiðiniz için teþekkür ederiz. Kurulum iþlemini tamamlamak için lütfen aþaðýda istenen bilgileri doldurunuz. Unutmayýnýz ki seçtiðiniz veri tabaný  önceden kurulmuþ olmalýdýr.  MS Access gibi bir ODBC kullanan veri tabaný kuruyorsanýz , iþleme devam etmeden, öncelikle bir DSN oluþturmanýz gerekir.");
	define("INSTALL_STEP_2_TITLE", "Kurulum: Adým 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Kurulum: Adým 3");
	define("INSTALL_STEP_3_DESC", "Lütfen bir site planý seçiniz, sonradan planý deðiþtirebilirsiniz.");
	define("INSTALL_FINAL_TITLE", "Kurulum: Son Adým");
	define("SELECT_DATE_TITLE", "Tarih Formatýný Seç");

	define("DB_SETTINGS_MSG", "Veri tabaný Ayarlarý");
	define("DB_PROGRESS_MSG", "Database yapýlandýrma iþlemi.");
	define("SELECT_PHP_LIB_MSG", "PHP Kitaplýðý Seç");
	define("SELECT_DB_TYPE_MSG", "Veritabaný Tipi Seç");
	define("ADMIN_SETTINGS_MSG", "Yönetim Ayarlarý");
	define("DATE_SETTINGS_MSG", "Tarih Formatlarý");
	define("NO_DATE_FORMATS_MSG", "Mevcut tarih formatý yok");
	define("INSTALL_FINISHED_MSG", "Bu noktada ana kurulumunuz tamamlanmýþ bulunuyor. Lütfen yönetim bölümü ayarlarýný kontrol ettiðinizden ve gerekli deðiþiklikleri yaptýðýnýzdan emin olun.");
	define("ACCESS_ADMIN_MSG", "Yönetim bölümüne ulaþmak için buraya týklayýnýz.");
	define("ADMIN_URL_MSG", "Yönetim URL");
	define("MANUAL_URL_MSG", "Kýlavuz URL");
	define("THANKS_MSG", "<b>ViArt SHOP</b> u seçtiðiniz için teþekkür ederiz.");

	define("DB_TYPE_FIELD", "Veri tabaný tipi");
	define("DB_TYPE_DESC", "Kullandýðýnýz <b>veritabanýnýn</b>tipini seçiniz. SQL Server veya Microsoft Access kullanýyorsanýz ODBC'yi seçin.");
	define("DB_PHP_LIB_FIELD", "PHP Kitaplýðý");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "ViArt veritabanýnýzýn çalýþacaðý sunucunun <b>ad</b>ný veya <b>IP adresi</b>ni giriniz. Veritabanýnýzý yerel PC'nizde çalýþtýyorsanýz, bunu \"<b>localhost</b>\" þeklinde ve portu da boþ olarak býrakabilirsiniz. Hosting firmasýnýn veritabanýný kullanmanýz halinde, sunucu ayarlarý için firmanýn belgelerine baþvurun.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Veritabaný Adý / DSN");
	define("DB_NAME_DESC", "MySQL veya PostgreSQL gibi bir veritabaný kullanýyorsanýz, ViArt'ýn tablolarý oluþturmasýný istediðiniz <b>veritabanýnýn adý</>ný girin. Bu veritabaný mutlaka mevcut olmalýdýr. Eðer ViArt'ý yerel PC'nize deneme amacýyla kuruyorsanýz, çoðu sistemde hazýr bulunan bir \"<b>test</b>\" veritabanýnýn olmamasý durumunda, \"viart\" adýyla bir tane oluþturun. Ama eðer Microsoft Access veya SQL Server kullanýyorsanýz, bu durumda Veritabanýnýn Adý, Kontrol Paneli'nin Veri kaynaklarý (ODBC) bölümünde kurmuþ olduðunuz <b>DSN'nin adý</b> olmalýdýr.");
	define("DB_USER_FIELD", "Kullanýcý Adý");
	define("DB_PASS_FIELD", "Þifre");
	define("DB_USER_PASS_DESC", "Veritabaný <b>Kullanýcý adý</b> ve <b>Þifresini</b> giriniz.");
	define("DB_PERSISTENT_FIELD", "Kalýcý (persistent) baðlantý");
	define("DB_PERSISTENT_DESC", "MySQL yahut Postgre gibi sürekli baðlantý kullanmak için kutuyu iþaretleyin. Bu konuda bilginiz yoksa, kutuyu boþ býrakýn.");
	define("DB_CREATE_DB_FIELD", "Veritabaný Oluþtur");
	define("DB_CREATE_DB_DESC", "Veritabaný oluþturmak mümkünse, bu kutuyu iþaretleyin. Sadece MySQL ve PostgreSQL için geçerlidir.");
	define("DB_POPULATE_FIELD", "Populate DB");
	define("DB_POPULATE_DESC", "veritabaný tablo yapýsýný oluþturmak ve içine veri yazmak için kutuyu iþaretleyin");
	define("DB_TEST_DATA_FIELD", "Test Data");
	define("DB_TEST_DATA_DESC", "veritabanýnýza test verisi eklemek için kutuyu iþaretleyin");
	define("ADMIN_EMAIL_FIELD", "Yönetici Eposta");
	define("ADMIN_LOGIN_FIELD", "Yönetici giriþi");
	define("ADMIN_PASS_FIELD", "Admin Þifre");
	define("ADMIN_CONF_FIELD", "Þifre Doðrula");
	define("DATETIME_SHOWN_FIELD", "Tarih Zaman Formatý (sitede görülecek)");
	define("DATE_SHOWN_FIELD", "Tarih Formatý (sitede görülecek)");
	define("DATETIME_EDIT_FIELD", "Tarih Zaman Formatý (for editing)");
	define("DATE_EDIT_FIELD", "Tarih Formatý (for editing)");
	define("DATE_FORMAT_COLUMN", "Tarih Formatý");
	define("CURRENT_DATE_COLUMN", "Geçerli Tarih");

	define("DB_LIBRARY_ERROR", "PHP fonksiyonlarý {db_library} için belirlenmemiþ. Lütfen configuration file - php.ini database ayarlarýný kontrol ediniz.");
	define("DB_CONNECT_ERROR", "Database 'e ulaþýlamýyor. Lütfen database parametrelerini kontrol edip tekrar deneyiniz.");
	define("INSTALL_FINISHED_ERROR", "Kurulum tamamlandý.");
	define("WRITE_FILE_ERROR", "<b>'includes/var_definition.php'</b> dosyasý yazýlabilir deðil. Devam etmeden önce CHMOD ayarlarýný düzeltiniz.");
	define("WRITE_DIR_ERROR", "<b>'includes'</b> klasörü yazýlabilir deðil. Devam etmeden önce CHMOD ayarlarýný düzeltiniz.");
	define("DUMP_FILE_ERROR", "{file_name}' bulunamadý.");
	define("DB_TABLE_ERROR", "{table_name}' bulunamadý. Lütfen veritabanýna gerekli veriyi yerleþtiriniz.");
	define("TEST_DATA_ERROR", "Test verileri olan tablolarý yayýnlamadan önce <b>{POPULATE_DB_FIELD}</b>'yi kontrol edin");
	define("DB_HOST_ERROR", "Vermiþ olduðunuz host adresi bulunamadý");
	define("DB_PORT_ERROR", "Belirtilen porttaki veritabaný sunucusuna baðlanýlamadý");
	define("DB_USER_PASS_ERROR", "Belirtilen kullanýcý adý veya þifre hatalý");
	define("DB_NAME_ERROR", "Baðlantý ayarlarý doðru fakat '{db_name}' veritabaný bulunamadý");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP Upgrade");
	define("UPGRADE_NOTE", "Uyarý: Lütfen iþleme baþlamadan önce veritabanýnýn yedeðini aldýðýnýzdan emin olunuz.");
	define("UPGRADE_AVAILABLE_MSG", "Upgrade Available");
	define("UPGRADE_BUTTON", "{version_number} a yükselt");
	define("CURRENT_VERSION_MSG", "Kurulu Versiyon");
	define("LATEST_VERSION_MSG", "Kurulabilir Version");
	define("UPGRADE_RESULTS_MSG", "Yükseltme Sonuçlarý");
	define("SQL_SUCCESS_MSG", "SQL sorgusu baþarýlý");
	define("SQL_FAILED_MSG", "SQL sorgusu baþarýsýz");
	define("SQL_TOTAL_MSG", "Yürütülen toplam SQL sorgusu");
	define("VERSION_UPGRADED_MSG", "Ürün versiyonu yükseltilmiþtir.");
	define("ALREADY_LATEST_MSG", "Ürünün son versiyonu zaten yüklü.");
	define("DOWNLOAD_NEW_MSG", "The new version was detected");
	define("DOWNLOAD_NOW_MSG", "{version_number} 'ý indir");
	define("DOWNLOAD_FOUND_MSG", "Yüklenebilir yeni bir versiyon var {version_number}. Lütfen indirmek için týklayýnýz. Dosyalarý indirip sunucunuza yükledikten sonra yükseltme butonuna (upgrade) týklmayý unutmayýn.");
	define("NO_XML_CONNECTION", "Dikkat 'http://www.viart.com/' baðlanýlamýyor!");

?>