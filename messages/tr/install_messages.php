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

	define("INSTALL_STEP_1_TITLE", "Kurulum: Ad�m 1");
	define("INSTALL_STEP_1_DESC", "ViArt SHOP 'u se�ti�iniz i�in te�ekk�r ederiz. Kurulum i�lemini tamamlamak i�in l�tfen a�a��da istenen bilgileri doldurunuz. Unutmay�n�z ki se�ti�iniz veri taban�  �nceden kurulmu� olmal�d�r.  MS Access gibi bir ODBC kullanan veri taban� kuruyorsan�z , i�leme devam etmeden, �ncelikle bir DSN olu�turman�z gerekir.");
	define("INSTALL_STEP_2_TITLE", "Kurulum: Ad�m 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Kurulum: Ad�m 3");
	define("INSTALL_STEP_3_DESC", "L�tfen bir site plan� se�iniz, sonradan plan� de�i�tirebilirsiniz.");
	define("INSTALL_FINAL_TITLE", "Kurulum: Son Ad�m");
	define("SELECT_DATE_TITLE", "Tarih Format�n� Se�");

	define("DB_SETTINGS_MSG", "Veri taban� Ayarlar�");
	define("DB_PROGRESS_MSG", "Database yap�land�rma i�lemi.");
	define("SELECT_PHP_LIB_MSG", "PHP Kitapl��� Se�");
	define("SELECT_DB_TYPE_MSG", "Veritaban� Tipi Se�");
	define("ADMIN_SETTINGS_MSG", "Y�netim Ayarlar�");
	define("DATE_SETTINGS_MSG", "Tarih Formatlar�");
	define("NO_DATE_FORMATS_MSG", "Mevcut tarih format� yok");
	define("INSTALL_FINISHED_MSG", "Bu noktada ana kurulumunuz tamamlanm�� bulunuyor. L�tfen y�netim b�l�m� ayarlar�n� kontrol etti�inizden ve gerekli de�i�iklikleri yapt���n�zdan emin olun.");
	define("ACCESS_ADMIN_MSG", "Y�netim b�l�m�ne ula�mak i�in buraya t�klay�n�z.");
	define("ADMIN_URL_MSG", "Y�netim URL");
	define("MANUAL_URL_MSG", "K�lavuz URL");
	define("THANKS_MSG", "<b>ViArt SHOP</b> u se�ti�iniz i�in te�ekk�r ederiz.");

	define("DB_TYPE_FIELD", "Veri taban� tipi");
	define("DB_TYPE_DESC", "Kulland���n�z <b>veritaban�n�n</b>tipini se�iniz. SQL Server veya Microsoft Access kullan�yorsan�z ODBC'yi se�in.");
	define("DB_PHP_LIB_FIELD", "PHP Kitapl���");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "ViArt veritaban�n�z�n �al��aca�� sunucunun <b>ad</b>n� veya <b>IP adresi</b>ni giriniz. Veritaban�n�z� yerel PC'nizde �al��t�yorsan�z, bunu \"<b>localhost</b>\" �eklinde ve portu da bo� olarak b�rakabilirsiniz. Hosting firmas�n�n veritaban�n� kullanman�z halinde, sunucu ayarlar� i�in firman�n belgelerine ba�vurun.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Veritaban� Ad� / DSN");
	define("DB_NAME_DESC", "MySQL veya PostgreSQL gibi bir veritaban� kullan�yorsan�z, ViArt'�n tablolar� olu�turmas�n� istedi�iniz <b>veritaban�n�n ad�</>n� girin. Bu veritaban� mutlaka mevcut olmal�d�r. E�er ViArt'� yerel PC'nize deneme amac�yla kuruyorsan�z, �o�u sistemde haz�r bulunan bir \"<b>test</b>\" veritaban�n�n olmamas� durumunda, \"viart\" ad�yla bir tane olu�turun. Ama e�er Microsoft Access veya SQL Server kullan�yorsan�z, bu durumda Veritaban�n�n Ad�, Kontrol Paneli'nin Veri kaynaklar� (ODBC) b�l�m�nde kurmu� oldu�unuz <b>DSN'nin ad�</b> olmal�d�r.");
	define("DB_USER_FIELD", "Kullan�c� Ad�");
	define("DB_PASS_FIELD", "�ifre");
	define("DB_USER_PASS_DESC", "Veritaban� <b>Kullan�c� ad�</b> ve <b>�ifresini</b> giriniz.");
	define("DB_PERSISTENT_FIELD", "Kal�c� (persistent) ba�lant�");
	define("DB_PERSISTENT_DESC", "MySQL yahut Postgre gibi s�rekli ba�lant� kullanmak i�in kutuyu i�aretleyin. Bu konuda bilginiz yoksa, kutuyu bo� b�rak�n.");
	define("DB_CREATE_DB_FIELD", "Veritaban� Olu�tur");
	define("DB_CREATE_DB_DESC", "Veritaban� olu�turmak m�mk�nse, bu kutuyu i�aretleyin. Sadece MySQL ve PostgreSQL i�in ge�erlidir.");
	define("DB_POPULATE_FIELD", "Populate DB");
	define("DB_POPULATE_DESC", "veritaban� tablo yap�s�n� olu�turmak ve i�ine veri yazmak i�in kutuyu i�aretleyin");
	define("DB_TEST_DATA_FIELD", "Test Data");
	define("DB_TEST_DATA_DESC", "veritaban�n�za test verisi eklemek i�in kutuyu i�aretleyin");
	define("ADMIN_EMAIL_FIELD", "Y�netici Eposta");
	define("ADMIN_LOGIN_FIELD", "Y�netici giri�i");
	define("ADMIN_PASS_FIELD", "Admin �ifre");
	define("ADMIN_CONF_FIELD", "�ifre Do�rula");
	define("DATETIME_SHOWN_FIELD", "Tarih Zaman Format� (sitede g�r�lecek)");
	define("DATE_SHOWN_FIELD", "Tarih Format� (sitede g�r�lecek)");
	define("DATETIME_EDIT_FIELD", "Tarih Zaman Format� (for editing)");
	define("DATE_EDIT_FIELD", "Tarih Format� (for editing)");
	define("DATE_FORMAT_COLUMN", "Tarih Format�");
	define("CURRENT_DATE_COLUMN", "Ge�erli Tarih");

	define("DB_LIBRARY_ERROR", "PHP fonksiyonlar� {db_library} i�in belirlenmemi�. L�tfen configuration file - php.ini database ayarlar�n� kontrol ediniz.");
	define("DB_CONNECT_ERROR", "Database 'e ula��lam�yor. L�tfen database parametrelerini kontrol edip tekrar deneyiniz.");
	define("INSTALL_FINISHED_ERROR", "Kurulum tamamland�.");
	define("WRITE_FILE_ERROR", "<b>'includes/var_definition.php'</b> dosyas� yaz�labilir de�il. Devam etmeden �nce CHMOD ayarlar�n� d�zeltiniz.");
	define("WRITE_DIR_ERROR", "<b>'includes'</b> klas�r� yaz�labilir de�il. Devam etmeden �nce CHMOD ayarlar�n� d�zeltiniz.");
	define("DUMP_FILE_ERROR", "{file_name}' bulunamad�.");
	define("DB_TABLE_ERROR", "{table_name}' bulunamad�. L�tfen veritaban�na gerekli veriyi yerle�tiriniz.");
	define("TEST_DATA_ERROR", "Test verileri olan tablolar� yay�nlamadan �nce <b>{POPULATE_DB_FIELD}</b>'yi kontrol edin");
	define("DB_HOST_ERROR", "Vermi� oldu�unuz host adresi bulunamad�");
	define("DB_PORT_ERROR", "Belirtilen porttaki veritaban� sunucusuna ba�lan�lamad�");
	define("DB_USER_PASS_ERROR", "Belirtilen kullan�c� ad� veya �ifre hatal�");
	define("DB_NAME_ERROR", "Ba�lant� ayarlar� do�ru fakat '{db_name}' veritaban� bulunamad�");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP Upgrade");
	define("UPGRADE_NOTE", "Uyar�: L�tfen i�leme ba�lamadan �nce veritaban�n�n yede�ini ald���n�zdan emin olunuz.");
	define("UPGRADE_AVAILABLE_MSG", "Upgrade Available");
	define("UPGRADE_BUTTON", "{version_number} a y�kselt");
	define("CURRENT_VERSION_MSG", "Kurulu Versiyon");
	define("LATEST_VERSION_MSG", "Kurulabilir Version");
	define("UPGRADE_RESULTS_MSG", "Y�kseltme Sonu�lar�");
	define("SQL_SUCCESS_MSG", "SQL sorgusu ba�ar�l�");
	define("SQL_FAILED_MSG", "SQL sorgusu ba�ar�s�z");
	define("SQL_TOTAL_MSG", "Y�r�t�len toplam SQL sorgusu");
	define("VERSION_UPGRADED_MSG", "�r�n versiyonu y�kseltilmi�tir.");
	define("ALREADY_LATEST_MSG", "�r�n�n son versiyonu zaten y�kl�.");
	define("DOWNLOAD_NEW_MSG", "The new version was detected");
	define("DOWNLOAD_NOW_MSG", "{version_number} '� indir");
	define("DOWNLOAD_FOUND_MSG", "Y�klenebilir yeni bir versiyon var {version_number}. L�tfen indirmek i�in t�klay�n�z. Dosyalar� indirip sunucunuza y�kledikten sonra y�kseltme butonuna (upgrade) t�klmay� unutmay�n.");
	define("NO_XML_CONNECTION", "Dikkat 'http://www.viart.com/' ba�lan�lam�yor!");

?>