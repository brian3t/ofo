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


	// mensajes de la instalaci�n
	define("INSTALL_TITLE", "Instalaci�n de ViArt SHOP");

	define("INSTALL_STEP_1_TITLE", "Instalaci�n: Paso 1");
	define("INSTALL_STEP_1_DESC", "Gracias por elegir ViArt. Para completar esta instalaci�n por favor rellene los detalles necesarios. Tenga en cuenta que la base de datos en la cual usted hace la instalaci�n debe haber sido creada con antelaci�n. Si est� instalando en una base de datos que utiliza ODBC, por ejemplo MS Access, debe crear un DSN antes de continuar.");
	define("INSTALL_STEP_2_TITLE", "Instalaci�n: Paso 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Instalaci�n: Paso 3");
	define("INSTALL_STEP_3_DESC", "Por favor Seleccione el aspecto de su sitio. Podr� cambiar dicho aspecto posteriormente.");
	define("INSTALL_FINAL_TITLE", "Instalaci�n: Final");
	define("SELECT_DATE_TITLE", "Seleccione el formato de Fecha");

	define("DB_SETTINGS_MSG", "Par�metros de la base de datos");
	define("DB_PROGRESS_MSG", "Progreso del llenado de la estructura de la Base de Datos");
	define("SELECT_PHP_LIB_MSG", "Seleccionar la Biblioteca de PHP");
	define("SELECT_DB_TYPE_MSG", "Seleccionar el tipo de base de datos");
	define("ADMIN_SETTINGS_MSG", "Par�metros de administraci�n ");
	define("DATE_SETTINGS_MSG", "Formatos de la Fecha");
	define("NO_DATE_FORMATS_MSG", "Los formatos de la fecha no est�n disponibles");
	define("INSTALL_FINISHED_MSG", "En este punto ha terminado la instalaci�n b�sica. Por favor aseg�rese de que los par�metros sean correctos y haga los cambios necesarios en la secci�n de Administraci�n.");
	define("ACCESS_ADMIN_MSG", "Para entrar en la secci�n de Administraci�n pinche aqu�.");
	define("ADMIN_URL_MSG", "Direcci�n de la p�gina de Administraci�n");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", "Gracias por elegir <b>ViArt SHOP</b>. ");

	define("DB_TYPE_FIELD", "Tipo de base de datos");
	define("DB_TYPE_DESC", "Por favor seleccione el <b>tipo de base de datos</b> que est� utilizando. Si utiliza SQL Server o Microsoft Access, Por favor seleccione ODBC.");
	define("DB_PHP_LIB_FIELD", "Libreria de PHP");
	define("DB_HOST_FIELD", "Servidor");
	define("DB_HOST_DESC", "Por favor, introduzca la <b>Nombre</b> o <b>la direcci�n IP del servidor</b> en donde estan las bases de datos en las que ViArt se ejecutar�. Si est� ejecutando su base de datos en su PC, entonces probablemente puede dejar tal y como \\\"<b>localhost</b>\\\" Y el puerto en blanco. Si utiliza una base de datos proporcionada por su empresa de alojamiento, Por favor, consulte a su empresa de alojamiento de la documentaci�n para la configuraci�n del servidor.");
	define("DB_PORT_FIELD", "Puerto");
	define("DB_NAME_FIELD", "Nombre de la base de datos / DSN");
	define("DB_NAME_DESC", "Si est� utilizando una base de datos como MySQL o PostgreSQL a continuaci�n, por favor, introduzca el <b>nombre de la base de datos</b> En la que ViArt va a crear sus tablas. Esta base de datos debe estar creada ya. Si tan s�lo instala ViArt para prop�sitos de prueba en el equipo de PC local PC entonces la mayor�a de los sistemas tienen una Base de datos que puede utilizar \\\"<b>test</b>\\\" . Si no, por favor, crear una base de datos llamada \\\"viart\\\" . Si est� utilizando Microsoft Access o SQL Server, la base de datos debe ser el nombre <b>Nombre de la DSN</b> en que usted ha creado las fuentes de datos (ODBC) en la secci�n de su panel de control");
	define("DB_USER_FIELD", "Nombre de usuario");
	define("DB_PASS_FIELD", "Contrase�a");
	define("DB_USER_PASS_DESC", "<b>Nombre de usuario</b> y <b>Contrase�a</b> - Por favor, introduzca el nombre de usuario y la contrase�a que utiliza para acceder a la base de datos. Si est� usando una instalaci�n local de ensayo es, probablemente, el nombre de usuario \\<b>root</b>\\\" Y la contrase�a es probablemente en blanco. Esto es lo m�s recomendable para las pruebas, pero tenga en cuenta que esto no es seguro en los servidores de producci�n.");
	define("DB_PERSISTENT_FIELD", "Conexi�n constante");
	define("DB_PERSISTENT_DESC", "para usar conexiones persistentes en MySQL o Postgre, marque esta casilla. Si no sabe lo que significa, es probablemente mejor dejarla sin marcar.");
	define("DB_CREATE_DB_FIELD", "Crear DB");
	define("DB_CREATE_DB_DESC", "es posible crear la base de datos, Marque esta casilla. S�lo funciona para MySQL y Postgre");
	define("DB_POPULATE_FIELD", "Poblar la base de datos");
	define("DB_POPULATE_DESC", "Para crear la estructura de las tablas y llenarlas con datos marque la casilla.");
	define("DB_TEST_DATA_FIELD", "Datos de prueba");
	define("DB_TEST_DATA_DESC", "Para a�adir algunos datos de prueba a su base de datos, marque la casilla de verificaci�n");
	define("ADMIN_EMAIL_FIELD", "Correo electr�nico del Administrador");
	define("ADMIN_LOGIN_FIELD", "Nombre del Administrador");
	define("ADMIN_PASS_FIELD", "Contrase�a del administrador");
	define("ADMIN_CONF_FIELD", "Confirmar contrase�a");
	define("DATETIME_SHOWN_FIELD", "Formato de hora (mostrado en el sitio)");
	define("DATE_SHOWN_FIELD", "Formato de fecha (mostrado en el sitio)");
	define("DATETIME_EDIT_FIELD", "Formato de hora (para edici�n)");
	define("DATE_EDIT_FIELD", "El formato de la fecha (para edici�n)");
	define("DATE_FORMAT_COLUMN", "Formato de la fecha");
	define("CURRENT_DATE_COLUMN", "Fecha de hoy");

	define("DB_LIBRARY_ERROR", "Las funciones de PHP para {db_library} no est�n definidos. Por favor, compruebe su configuraci�n de la base de datos de su archivo de configuraci�n - php.ini.");
	define("DB_CONNECT_ERROR", "No es posible conectarse con la base de datos. Por favor verifique los par�metros de la base de datos.");
	define("INSTALL_FINISHED_ERROR", "El proceso de la instalaci�n ha terminado.");
	define("WRITE_FILE_ERROR", "No hay permiso de escritura para el archivo <b>'includes/var_definition.php'</b>. Por favor cambie el permiso de escritura del archivo antes de continuar.");
	define("WRITE_DIR_ERROR", "No hay permiso de escritura para entrar en la carpeta  <b>'includes/'</b>. Por favor cambie el permiso de la carpeta antes de continuar.");
	define("DUMP_FILE_ERROR", "El archivo de volcado '{file_name}' no se encuentra.");
	define("DB_TABLE_ERROR", "La tabla '{table_name}' no se encuentra. Por favor llene la base de datos con los datos necesarios.");
	define("TEST_DATA_ERROR", "Compruebe <b>{POPULATE_DB_FIELD}</b> Antes de rellenar las tablas con los datos de los ensayos");
	define("DB_HOST_ERROR", "El nombre del host que ha especificado no pudo ser encontrado.");
	define("DB_PORT_ERROR", "No se puede conectar al servidor de base de datos usando el puerto especificado.");
	define("DB_USER_PASS_ERROR", "El nombre de usuario o contrase�a especificada no es correcta.");
	define("DB_NAME_ERROR", "Las configuraciones de conexi�n son correctas, pero la base de datos '{db_name}' No se pudo encontrar.");

	// mensajes de actualizar
	define("UPGRADE_TITLE", "Actualizaci�n de ViArt SHOP");
	define("UPGRADE_NOTE", "Aviso: Por favor tenga en cuenta que es aconsejable hacer una copia de seguridad de la base de datos antes de proceder.");
	define("UPGRADE_AVAILABLE_MSG", "Actualizaci�n disponible");
	define("UPGRADE_BUTTON", "Actualizar a {version_number} ahora.");
	define("CURRENT_VERSION_MSG", "La versi�n instalada actualmente.");
	define("LATEST_VERSION_MSG", "La versi�n disponible para la instalaci�n.");
	define("UPGRADE_RESULTS_MSG", "Resultados de la actualizaci�n");
	define("SQL_SUCCESS_MSG", "Las instrucciones SQL han tenido �xito");
	define("SQL_FAILED_MSG", "Las instrucciones SQL han fallado");
	define("SQL_TOTAL_MSG", "Total de instrucciones SQL ejecutadas");
	define("VERSION_UPGRADED_MSG", "Su versi�n ha sido actualizada a");
	define("ALREADY_LATEST_MSG", "Usted ya posee la �ltima versi�n");
	define("DOWNLOAD_NEW_MSG", "Se detect� una nueva versi�n");
	define("DOWNLOAD_NOW_MSG", "Descargar la versi�n {version_number} ahora");
	define("DOWNLOAD_FOUND_MSG", "Hemos detectado que la nueva {version_number} Versi�n se encuentra disponible para descargar. Por favor, haga clic en el enlace para iniciar la descarga. Despu�s de completar la descarga y la sustituci�n de los archivos no se olvide de ejecutar de nuevo la rutina de actualizaci�n.");
	define("NO_XML_CONNECTION", "Advertencia! No hay conexi�n disponible a 'http://www.viart.com/' !");

?>