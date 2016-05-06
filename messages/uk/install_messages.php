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


	// повідомлення інсталяції
	define("INSTALL_TITLE", "Iнсталяцiя ViArt SHOP");

	define("INSTALL_STEP_1_TITLE", "Iнсталяцiя: Крок 1");
	define("INSTALL_STEP_1_DESC", "Дякуємо за вибiр ViArt SHOP. Для того, щоб закiнчити процес iнсталяцiї будь-ласка заповнiть всi необхiднi даннi. Зауважте, що база, яку ви будете використовувати вже має бути створена. Якщо ви використовуєте базу, що використовує ODBC, наприклад MS Access, для початку вам необхiдно створити запис DSN для того щоб продовжити.");
	define("INSTALL_STEP_2_TITLE", "Iнсталяцiя: Крок 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Iнсталяцiя: Крок 3");
	define("INSTALL_STEP_3_DESC", "Будь-ласка виберіть дизайн сайту. Надалі ви зможете його змінити на інший.");
	define("INSTALL_FINAL_TITLE", "Iнсталяцiя: Завершення");
	define("SELECT_DATE_TITLE", "Вибiр формату дати");

	define("DB_SETTINGS_MSG", "Налаштування бази");
	define("DB_PROGRESS_MSG", "Прогрес наповення структури бази");
	define("SELECT_PHP_LIB_MSG", "Виберiть PHP бiблiотеку");
	define("SELECT_DB_TYPE_MSG", "Виберiть тип бази");
	define("ADMIN_SETTINGS_MSG", "Налаштування адмiнiстратора");
	define("DATE_SETTINGS_MSG", "Формати дати");
	define("NO_DATE_FORMATS_MSG", "Немає жодного доступного формату дати");
	define("INSTALL_FINISHED_MSG", "На цьому кроцi ваша базова iнсталяцiя завершена. Будь-ласка перевiрте всi вашi налаштування в адмiнiстативнiй частинi i зробiть всi необхiднi змiни.");
	define("ACCESS_ADMIN_MSG", "Для доступу до адмiнiстративної частини будь-ласка натиснiть тут");
	define("ADMIN_URL_MSG", "Посилання на адмiнiстративну частину");
	define("MANUAL_URL_MSG", "Посилання на документацію");
	define("THANKS_MSG", "Дякуємо за вибiр <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Тип бази");
	define("DB_TYPE_DESC", "Виберіть <b>тип бази даних</b> що використовується. Якщо використовується SQL Server або Microsoft Access, виберіть ODBC.");
	define("DB_PHP_LIB_FIELD", "PHP бiблiотека");
	define("DB_HOST_FIELD", "Назва хосту");
	define("DB_HOST_DESC", "Введіть <b>назву</b> або <b>IP адресу серверу</b> на якому Ваша база даних ViArt  буде запущена. Якщо база даних виконується на локальному комп'ютері, просто залиште назву сервера \"<b>localhost</b>\", а значення порту порожнім. Якщо використовується база даних хостінг компанії, використовуйте документацію хостінг компанії для серверних налаштувань.");
	define("DB_PORT_FIELD", "Порт");
	define("DB_NAME_FIELD", "Iм'я бази / DSN");
	define("DB_NAME_DESC", "Якщо використовується база даних така як MySQL або PostgreSQL, введіть <b>Назву бази даних</b> де буде розташовано таблиці ViArt. База даних повинна бути вже створена. Якщо ViArt встановлюється лише для тестування на локальній машині, більшість систем мають тестову (\"<b>test</b>\") базу даних, яку Ви можете використати. Якщо ні, створіть базу даних, з назвою наприклад 'viart'. Якщо використовується Microsoft Access або SQL Server тоді Назва Бази даних повинна бути <b>назвою DSN</b> яку встановлено в секції Data Sources (ODBC) Панелі Керування.");
	define("DB_USER_FIELD", "Користувач");
	define("DB_PASS_FIELD", "Пароль");
	define("DB_USER_PASS_DESC", "<b>І'мя користувача</b> та <b>Пароль</b> - введіть ім'я та пароль для доступу до бази даних. Якщо використовується локальна тестова інсталяція ім'я як правило це \"<b>root</b>\", а пароль як правило порожній. Це зручно для тестування, однак заради безпеки не використовуйте такі імена та паролі на виробничих серверах.");
	define("DB_PERSISTENT_FIELD", "Постiйний зв'язок");
	define("DB_PERSISTENT_DESC", "Для використання постійного з'єднання з MySQL або Postgre базою. Якщо ви не знаєте що це означає, краще залиште як є.");
	define("DB_CREATE_DB_FIELD", "Створити базу даних");
	define("DB_CREATE_DB_DESC", "для створення бази даних (тільки MySQL та Postgre) натисність тут ");
	define("DB_POPULATE_FIELD", "Заповнити базу");
	define("DB_POPULATE_DESC", "для того щоб створити структуру таблиць та заповнити їх, натисніть тут");
	define("DB_TEST_DATA_FIELD", "Тестові дані");
	define("DB_TEST_DATA_DESC", "натисніть тут, щоб додати деякі тестові дані");
	define("ADMIN_EMAIL_FIELD", "Електронна адреса адмiнiстратора");
	define("ADMIN_LOGIN_FIELD", "Логiн адмiнiстратора");
	define("ADMIN_PASS_FIELD", "Пароль адмiнiстратора");
	define("ADMIN_CONF_FIELD", "Пiдтвердження паролю");
	define("DATETIME_SHOWN_FIELD", "Формат дати з часом (вiдображення на сайтi)");
	define("DATE_SHOWN_FIELD", "Формат дати (вiдображення на сайтi)");
	define("DATETIME_EDIT_FIELD", "Формат дати з часом (для редагування)");
	define("DATE_EDIT_FIELD", "Формат дати (для редагування)");
	define("DATE_FORMAT_COLUMN", "Формат дати ");
	define("CURRENT_DATE_COLUMN", "Поточна дата");

	define("DB_LIBRARY_ERROR", "PHP функції для {db_library} не визначені. Перевірте налаштування бази даних у файлі конфігурацій - php.ini.");
	define("DB_CONNECT_ERROR", "Не можна встановити зв'язок з базою. Перевiрте параметри зв'язку з базою.");
	define("INSTALL_FINISHED_ERROR", "Процес iнсталяцiї вже завершено.");
	define("WRITE_FILE_ERROR", "Немає прав для змiни файлу <b>'includes/var_definition.php'</b>. Будь-ласка змiнiть права доступу перед тим як продовжити.");
	define("WRITE_DIR_ERROR", "Немає прав для запису в папки <b>'includes/'</b>. Будь-ласка змiнiть права доступу перед тим як продовжити.");
	define("DUMP_FILE_ERROR", "Файл бази '{file_name}' не знайдено.");
	define("DB_TABLE_ERROR", "Табличку '{table_name}' не знайдено. Будь-ласка наповнiть базу необхiдною iнформацiєю.");
	define("TEST_DATA_ERROR", "Спочатку поставте позначку <b>{POPULATE_DB_FIELD}</b>, якщо бажаєте додати тестові дані");
	define("DB_HOST_ERROR", "Не знайдено вказаний сервер.");
	define("DB_PORT_ERROR", "Неможливо під'єднатися до сервера бази даних по вказаному портому.");
	define("DB_USER_PASS_ERROR", "Вказані ім'я користувача та пароль не вірні");
	define("DB_NAME_ERROR", "І'мя та пароль вірні, але базу '{db_name}' не знайдено.");

	// повідомлення оновлень
	define("UPGRADE_TITLE", "Оновлення бази даних");
	define("UPGRADE_NOTE", "Примiтка: будь-ласка створiть копiю бази данних перед початком.");
	define("UPGRADE_AVAILABLE_MSG", "Нова версiя доступна");
	define("UPGRADE_BUTTON", "Поновити до {version_number}");
	define("CURRENT_VERSION_MSG", "Ваша поточна версiя");
	define("LATEST_VERSION_MSG", "Версiя доступна для iнсталяцiї");
	define("UPGRADE_RESULTS_MSG", "Результати поновлення");
	define("SQL_SUCCESS_MSG", "SQL запитiв успiшних");
	define("SQL_FAILED_MSG", "SQL запитiв з помилками");
	define("SQL_TOTAL_MSG", "Всього SQL запитiв виконано");
	define("VERSION_UPGRADED_MSG", "Ваша версiя була поновлена до");
	define("ALREADY_LATEST_MSG", "У вас стоїть остання доступна версiя");
	define("DOWNLOAD_NEW_MSG", "Знайдено оновлену версію");
	define("DOWNLOAD_NOW_MSG", "Скачайте версію {version_number} зараз");
	define("DOWNLOAD_FOUND_MSG", "З'ясовано що версія {version_number} доступна для скачування. Натисніть на посилання щоб розпочати скачування. Після закінчення скачування та заміни файлів не забудьте знову запустити процедуру Оновлення.");
	define("NO_XML_CONNECTION", "Увага! Відсутнє з'єднання з 'http://www.viart.com/'!");

?>