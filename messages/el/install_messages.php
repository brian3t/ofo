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
	define("INSTALL_TITLE", "Εγκατάσταση ViArt SHOP");

	define("INSTALL_STEP_1_TITLE", "Εγκατάσταση Βήμα 1");
	define("INSTALL_STEP_1_DESC", "Σας ευχαριστούμε για την επιλογή του καταστήματος ViArt SHOP. <br>Παρακαλούμε συμπληρώστε όλες τις λεπτομέρειες που θα σας ζητηθούν.<br>Προσέξτε σαν πρώτο Βήμα να υπάρχει ήδη η βάση δεδομένων. Εάν χρησιμοποιείτε σαν βάση δεδομένων την ODBC, π.χ. MS Access  πρέπει πρώτα να δημιουργήσετε τα  dsn αλλιώς η Εγκατάσταση δεν θα προχωρήσει.");
	define("INSTALL_STEP_2_TITLE", "Εγκατάσταση Βήμα 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Εγκατάσταση Βήμα 3");
	define("INSTALL_STEP_3_DESC", "Επιλέξτε την εμφάνιση της ιστοσελίδας σας , αργότερα μπορείτε να αλλάξετε την επιλογή σας.");
	define("INSTALL_FINAL_TITLE", "Τέλος εγκατάστασης");
	define("SELECT_DATE_TITLE", "επιλέξτε πως θα εμφανίζετε η Ημερομηνία");

	define("DB_SETTINGS_MSG", "Ρυθμίσεις βάσης δεδομένων");
	define("DB_PROGRESS_MSG", "Populating database structure progress");
	define("SELECT_PHP_LIB_MSG", "επιλέξτε Βιβλιοθήκη PHP");
	define("SELECT_DB_TYPE_MSG", "επιλέξτε τύπο βάσης δεδομένων");
	define("ADMIN_SETTINGS_MSG", "Ρυθμίσεις διαχείρισης");
	define("DATE_SETTINGS_MSG", "Ρυθμίσεις ημερομηνίας");
	define("NO_DATE_FORMATS_MSG", "Δεν υπάρχουν διαθέσιμες επιλογές ημερομηνίας");
	define("INSTALL_FINISHED_MSG", "Σε αυτό το σημείο η Εγκατάσταση ολοκληρώθηκε , Παρακαλώ βεβαιωθείτε ότι Έχετε βάλει τις σωστές παραμέτρους");
	define("ACCESS_ADMIN_MSG", "Για πρόσβαση στο κοντρόλ πάνελ κάντε κλικ ΕΔΏ");
	define("ADMIN_URL_MSG", "URL Διαχειριστή");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", "ευχαριστούμε που επιλέξατε το <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Τύπος βάσης δεδομένων");
	define("DB_TYPE_DESC", "Please select the <b>type of database</b> that you are using. If you using SQL Server or Microsoft Access, please select ODBC.");
	define("DB_PHP_LIB_FIELD", "Βιβλιοθήκη PHP");
	define("DB_HOST_FIELD", "Hostname");
	define("DB_HOST_DESC", "Please enter the <b>name</b> or <b>IP address of the server</b> on which your ViArt database will run. If you are running your database on your local PC then you can probably just leave this as \"<b>localhost</b>\" and the port blank. If you using a database provided by your hosting company, please see your hosting company's documentation for the server settings.");
	define("DB_PORT_FIELD", "Port");
	define("DB_NAME_FIELD", "Όνομα βάσης δεδομένων / DSN");
	define("DB_NAME_DESC", "If you are using a database such as MySQL or PostgreSQL then please enter the <b>name of the database</b> where you would like ViArt to create its tables. This database must exist already. If you are just installing ViArt for testing purposes on your local PC then most systems have a \"<b>test</b>\" database you can use. If not, please create a database such as \"viart\" and use that. If you are using Microsoft Access or SQL Server then the Database Name should be the <b>name of the DSN</b> that you have set up in the Data Sources (ODBC) section of your Control Panel.");
	define("DB_USER_FIELD", "Όνομα χρήστη");
	define("DB_PASS_FIELD", "Κωδικός");
	define("DB_USER_PASS_DESC", "<b>Username</b> and <b>Password</b> - please enter the username and password you want to use to access the database. If you are using a local test installation the username is probably \"<b>root</b>\" and the password is probably blank. This is fine for testing, but please note that this is not secure on production servers.");
	define("DB_PERSISTENT_FIELD", "Persistent Connection");
	define("DB_PERSISTENT_DESC", "to use MySQL or Postgre persistent connections, tick this box. If you do not know what it means, then leaving it unticked is probably best.");
	define("DB_CREATE_DB_FIELD", "Create DB");
	define("DB_CREATE_DB_DESC", "to create database if possible, tick this box. Works only for MySQL and Postgre");
	define("DB_POPULATE_FIELD", "Γεμίστε την βάση δεδομένων");
	define("DB_POPULATE_DESC", "Τσεκάρετε εδώ για δημιουργήσετε την δομή της βάσης και να συμπληρώσετε τα δεδομένα της");
	define("DB_TEST_DATA_FIELD", "Test Data");
	define("DB_TEST_DATA_DESC", "to add some test data to your database tick the checkbox");
	define("ADMIN_EMAIL_FIELD", "E-mail Διαχειριστή");
	define("ADMIN_LOGIN_FIELD", "Είσοδος Διαχειριστή");
	define("ADMIN_PASS_FIELD", "Κωδικός Διαχειριστή");
	define("ADMIN_CONF_FIELD", "Ξαναγράψτε τον κωδικό");
	define("DATETIME_SHOWN_FIELD", "Εμφάνιση ημερομηνίας και ώρας(Στην ιστοσελίδα)");
	define("DATE_SHOWN_FIELD", "Εμφάνιση ημερομηνίας (στην ιστοσελίδα)");
	define("DATETIME_EDIT_FIELD", "Ημερομηνία και ώρα (για επεξεργασία)");
	define("DATE_EDIT_FIELD", "Ημερομηνία (για επεξεργασία)");
	define("DATE_FORMAT_COLUMN", "Εμφάνιση ημερομηνίας");
	define("CURRENT_DATE_COLUMN", "Τρέχουσα Ημερομηνία");

	define("DB_LIBRARY_ERROR", "PHP functions for {db_library} are not defined. Please check your database settings in your configuration file - php.ini.");
	define("DB_CONNECT_ERROR", "Δεν μπορώ να επικοινωνήσω με την βάση δεδομένων , Παρακαλώ ελξτε τις παραμέτρους");
	define("INSTALL_FINISHED_ERROR", "Η διαδικασία εγκατάστασης Έχει ήδη τελειώσει");
	define("WRITE_FILE_ERROR", "Δεν Έχετε άδεια να αλλάξετε αυτό το αρχείο <b>'includes/var_definition.php'</b>.Παρακαλώ αλλάξτε τις ιδιότητες του αρχείου πριν να συνεχίσετε");
	define("WRITE_DIR_ERROR", "Δεν Έχετε άδεια να αλλάξετε αυτόν τον φάκελο <b>'includes/'</b>.Παρακαλώ αλλάξτε τις ιδιότητες του φακέλου πριν να συνεχίσετε");
	define("DUMP_FILE_ERROR", "Το αρχείο '{file_name}' δεν βρέθηκε !");
	define("DB_TABLE_ERROR", "Ο πίνακας '{table_name} δεν βρέθηκε ! Παρακαλώ προσθέστε στην βάση δεδομένων τα απαραίτητα στοιχεία.");
	define("TEST_DATA_ERROR", "Check <b>{POPULATE_DB_FIELD}</b> before populating tables with test data");
	define("DB_HOST_ERROR", "The hostname that you specified could not be found.");
	define("DB_PORT_ERROR", "Can't connect to database server using specified port.");
	define("DB_USER_PASS_ERROR", "The username or password you specified is not correct.");
	define("DB_NAME_ERROR", "Login settings were correct, but the database '{db_name}' could not be found.");

	// upgrade messages
	define("UPGRADE_TITLE", "ViArt SHOP αναβάθμιση");
	define("UPGRADE_NOTE", "Σημείωση : Αποθηκεύστε την βάση δεδομένων πριν συνεχίσετε.");
	define("UPGRADE_AVAILABLE_MSG", "Είναι διαθέσιμη αναβάθμιση του προγράμματος");
	define("UPGRADE_BUTTON", "Αναβαθμίστε σε {version_number} Τώρα ");
	define("CURRENT_VERSION_MSG", "Έχεις ήδη εγκαταστήσει αυτήν την έκδοση");
	define("LATEST_VERSION_MSG", "έκδοση διαθέσιμη για εγκαταση");
	define("UPGRADE_RESULTS_MSG", "Αποτελέσματα αναβάθμισης");
	define("SQL_SUCCESS_MSG", "SQL ερωτήματα επιτυχή");
	define("SQL_FAILED_MSG", "SQL ερωτήματα λανθασμένα");
	define("SQL_TOTAL_MSG", "Όλα τα SQL ερωτήματα έχουν πραγματοποιηθεί");
	define("VERSION_UPGRADED_MSG", "Η έκδοση σας Έχει αναβαθμιστεί σε");
	define("ALREADY_LATEST_MSG", "Έχεις ήδη την πιο πρόσφατη έκδοση");
	define("DOWNLOAD_NEW_MSG", "The new version was detected");
	define("DOWNLOAD_NOW_MSG", "Download version {version_number} now");
	define("DOWNLOAD_FOUND_MSG", "We have detected that the new {version_number} version is available to download. Please click the link below to start downloading. After completing the download and replacing the files don't forget to run Upgrade routine again.");
	define("NO_XML_CONNECTION", "Warning! No connection to 'http://www.viart.com/' available!");

?>