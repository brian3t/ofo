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


	// mensajes de la instalación
	define("INSTALL_TITLE", "Instalación de ViArt SHOP");

	define("INSTALL_STEP_1_TITLE", "Instalación: Paso 1");
	define("INSTALL_STEP_1_DESC", "Gracias por elegir ViArt. Para completar esta instalación por favor rellene los detalles necesarios. Tenga en cuenta que la base de datos en la cual usted hace la instalación debe haber sido creada con antelación. Si está instalando en una base de datos que utiliza ODBC, por ejemplo MS Access, debe crear un DSN antes de continuar.");
	define("INSTALL_STEP_2_TITLE", "Instalación: Paso 2");
	define("INSTALL_STEP_2_DESC", "");
	define("INSTALL_STEP_3_TITLE", "Instalación: Paso 3");
	define("INSTALL_STEP_3_DESC", "Por favor Seleccione el aspecto de su sitio. Podrá cambiar dicho aspecto posteriormente.");
	define("INSTALL_FINAL_TITLE", "Instalación: Final");
	define("SELECT_DATE_TITLE", "Seleccione el formato de Fecha");

	define("DB_SETTINGS_MSG", "Parámetros de la base de datos");
	define("DB_PROGRESS_MSG", "Progreso del llenado de la estructura de la Base de Datos");
	define("SELECT_PHP_LIB_MSG", "Seleccionar la Biblioteca de PHP");
	define("SELECT_DB_TYPE_MSG", "Seleccionar el tipo de base de datos");
	define("ADMIN_SETTINGS_MSG", "Parámetros de administración ");
	define("DATE_SETTINGS_MSG", "Formatos de la Fecha");
	define("NO_DATE_FORMATS_MSG", "Los formatos de la fecha no están disponibles");
	define("INSTALL_FINISHED_MSG", "En este punto ha terminado la instalación básica. Por favor asegúrese de que los parámetros sean correctos y haga los cambios necesarios en la sección de Administración.");
	define("ACCESS_ADMIN_MSG", "Para entrar en la sección de Administración pinche aquí.");
	define("ADMIN_URL_MSG", "Dirección de la página de Administración");
	define("MANUAL_URL_MSG", "Manual URL");
	define("THANKS_MSG", "Gracias por elegir <b>ViArt SHOP</b>. ");

	define("DB_TYPE_FIELD", "Tipo de base de datos");
	define("DB_TYPE_DESC", "Por favor seleccione el <b>tipo de base de datos</b> que está utilizando. Si utiliza SQL Server o Microsoft Access, Por favor seleccione ODBC.");
	define("DB_PHP_LIB_FIELD", "Libreria de PHP");
	define("DB_HOST_FIELD", "Servidor");
	define("DB_HOST_DESC", "Por favor, introduzca la <b>Nombre</b> o <b>la dirección IP del servidor</b> en donde estan las bases de datos en las que ViArt se ejecutará. Si está ejecutando su base de datos en su PC, entonces probablemente puede dejar tal y como \\\"<b>localhost</b>\\\" Y el puerto en blanco. Si utiliza una base de datos proporcionada por su empresa de alojamiento, Por favor, consulte a su empresa de alojamiento de la documentación para la configuración del servidor.");
	define("DB_PORT_FIELD", "Puerto");
	define("DB_NAME_FIELD", "Nombre de la base de datos / DSN");
	define("DB_NAME_DESC", "Si está utilizando una base de datos como MySQL o PostgreSQL a continuación, por favor, introduzca el <b>nombre de la base de datos</b> En la que ViArt va a crear sus tablas. Esta base de datos debe estar creada ya. Si tan sólo instala ViArt para propósitos de prueba en el equipo de PC local PC entonces la mayoría de los sistemas tienen una Base de datos que puede utilizar \\\"<b>test</b>\\\" . Si no, por favor, crear una base de datos llamada \\\"viart\\\" . Si está utilizando Microsoft Access o SQL Server, la base de datos debe ser el nombre <b>Nombre de la DSN</b> en que usted ha creado las fuentes de datos (ODBC) en la sección de su panel de control");
	define("DB_USER_FIELD", "Nombre de usuario");
	define("DB_PASS_FIELD", "Contraseña");
	define("DB_USER_PASS_DESC", "<b>Nombre de usuario</b> y <b>Contraseña</b> - Por favor, introduzca el nombre de usuario y la contraseña que utiliza para acceder a la base de datos. Si está usando una instalación local de ensayo es, probablemente, el nombre de usuario \\<b>root</b>\\\" Y la contraseña es probablemente en blanco. Esto es lo más recomendable para las pruebas, pero tenga en cuenta que esto no es seguro en los servidores de producción.");
	define("DB_PERSISTENT_FIELD", "Conexión constante");
	define("DB_PERSISTENT_DESC", "para usar conexiones persistentes en MySQL o Postgre, marque esta casilla. Si no sabe lo que significa, es probablemente mejor dejarla sin marcar.");
	define("DB_CREATE_DB_FIELD", "Crear DB");
	define("DB_CREATE_DB_DESC", "es posible crear la base de datos, Marque esta casilla. Sólo funciona para MySQL y Postgre");
	define("DB_POPULATE_FIELD", "Poblar la base de datos");
	define("DB_POPULATE_DESC", "Para crear la estructura de las tablas y llenarlas con datos marque la casilla.");
	define("DB_TEST_DATA_FIELD", "Datos de prueba");
	define("DB_TEST_DATA_DESC", "Para añadir algunos datos de prueba a su base de datos, marque la casilla de verificación");
	define("ADMIN_EMAIL_FIELD", "Correo electrónico del Administrador");
	define("ADMIN_LOGIN_FIELD", "Nombre del Administrador");
	define("ADMIN_PASS_FIELD", "Contraseña del administrador");
	define("ADMIN_CONF_FIELD", "Confirmar contraseña");
	define("DATETIME_SHOWN_FIELD", "Formato de hora (mostrado en el sitio)");
	define("DATE_SHOWN_FIELD", "Formato de fecha (mostrado en el sitio)");
	define("DATETIME_EDIT_FIELD", "Formato de hora (para edición)");
	define("DATE_EDIT_FIELD", "El formato de la fecha (para edición)");
	define("DATE_FORMAT_COLUMN", "Formato de la fecha");
	define("CURRENT_DATE_COLUMN", "Fecha de hoy");

	define("DB_LIBRARY_ERROR", "Las funciones de PHP para {db_library} no están definidos. Por favor, compruebe su configuración de la base de datos de su archivo de configuración - php.ini.");
	define("DB_CONNECT_ERROR", "No es posible conectarse con la base de datos. Por favor verifique los parámetros de la base de datos.");
	define("INSTALL_FINISHED_ERROR", "El proceso de la instalación ha terminado.");
	define("WRITE_FILE_ERROR", "No hay permiso de escritura para el archivo <b>'includes/var_definition.php'</b>. Por favor cambie el permiso de escritura del archivo antes de continuar.");
	define("WRITE_DIR_ERROR", "No hay permiso de escritura para entrar en la carpeta  <b>'includes/'</b>. Por favor cambie el permiso de la carpeta antes de continuar.");
	define("DUMP_FILE_ERROR", "El archivo de volcado '{file_name}' no se encuentra.");
	define("DB_TABLE_ERROR", "La tabla '{table_name}' no se encuentra. Por favor llene la base de datos con los datos necesarios.");
	define("TEST_DATA_ERROR", "Compruebe <b>{POPULATE_DB_FIELD}</b> Antes de rellenar las tablas con los datos de los ensayos");
	define("DB_HOST_ERROR", "El nombre del host que ha especificado no pudo ser encontrado.");
	define("DB_PORT_ERROR", "No se puede conectar al servidor de base de datos usando el puerto especificado.");
	define("DB_USER_PASS_ERROR", "El nombre de usuario o contraseña especificada no es correcta.");
	define("DB_NAME_ERROR", "Las configuraciones de conexión son correctas, pero la base de datos '{db_name}' No se pudo encontrar.");

	// mensajes de actualizar
	define("UPGRADE_TITLE", "Actualización de ViArt SHOP");
	define("UPGRADE_NOTE", "Aviso: Por favor tenga en cuenta que es aconsejable hacer una copia de seguridad de la base de datos antes de proceder.");
	define("UPGRADE_AVAILABLE_MSG", "Actualización disponible");
	define("UPGRADE_BUTTON", "Actualizar a {version_number} ahora.");
	define("CURRENT_VERSION_MSG", "La versión instalada actualmente.");
	define("LATEST_VERSION_MSG", "La versión disponible para la instalación.");
	define("UPGRADE_RESULTS_MSG", "Resultados de la actualización");
	define("SQL_SUCCESS_MSG", "Las instrucciones SQL han tenido éxito");
	define("SQL_FAILED_MSG", "Las instrucciones SQL han fallado");
	define("SQL_TOTAL_MSG", "Total de instrucciones SQL ejecutadas");
	define("VERSION_UPGRADED_MSG", "Su versión ha sido actualizada a");
	define("ALREADY_LATEST_MSG", "Usted ya posee la última versión");
	define("DOWNLOAD_NEW_MSG", "Se detectó una nueva versión");
	define("DOWNLOAD_NOW_MSG", "Descargar la versión {version_number} ahora");
	define("DOWNLOAD_FOUND_MSG", "Hemos detectado que la nueva {version_number} Versión se encuentra disponible para descargar. Por favor, haga clic en el enlace para iniciar la descarga. Después de completar la descarga y la sustitución de los archivos no se olvide de ejecutar de nuevo la rutina de actualización.");
	define("NO_XML_CONNECTION", "Advertencia! No hay conexión disponible a 'http://www.viart.com/' !");

?>