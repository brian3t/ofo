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
	define("INSTALL_TITLE", "Instala��o ViArt SHOP");

	define("INSTALL_STEP_1_TITLE", "Instala��o: 1� Passo");
	define("INSTALL_STEP_1_DESC", "Obrigado por escolher a ViArt SHOP. Para continuar a instala��o, por favor, preencha os dados pedidos abaixo. Por favor, certifique-se de que a base de dados que seleccionar j� existe. Se instalar numa base de dados que utilize ODBC (ex.: MS Access), primeiro, dever� criar um DSN para prosseguir.");
	define("INSTALL_STEP_2_TITLE", "Instala��o: 2� Passo");
	define("INSTALL_STEP_2_DESC", " ");
	define("INSTALL_STEP_3_TITLE", "Instala��o: 3� Passo");
	define("INSTALL_STEP_3_DESC", "Por favor, seleccione um layout do site. Ter� a possibilidade de alterar o layout posteriormente.");
	define("INSTALL_FINAL_TITLE", "Fim da Instala��o");
	define("SELECT_DATE_TITLE", "Seleccionar o Formato da Data");

	define("DB_SETTINGS_MSG", "Defini��es da Base de Dados");
	define("DB_PROGRESS_MSG", "A carregar o progresso da estrutura da base de dados");
	define("SELECT_PHP_LIB_MSG", "Seleccionar a Biblioteca PHP");
	define("SELECT_DB_TYPE_MSG", "Seleccionar o Tipo de Base de Dados");
	define("ADMIN_SETTINGS_MSG", "Defini��es Administrativas");
	define("DATE_SETTINGS_MSG", "Formatos da Data");
	define("NO_DATE_FORMATS_MSG", "N�o h� formatos da data dispon�veis");
	define("INSTALL_FINISHED_MSG", "At� este ponto, a instala��o b�sica est� conclu�da. Por favor, verifique as defini��es na sec��o de administra��o e efectue as necess�rias modifica��es.");
	define("ACCESS_ADMIN_MSG", "Para ter acesso � sec��o da administra��o, clique aqui");
	define("ADMIN_URL_MSG", "URL da Administra��o");
	define("MANUAL_URL_MSG", "URL do Manual");
	define("THANKS_MSG", "Obrigado por ter escolhido <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Tipo de Base de Dados");
	define("DB_TYPE_DESC", "Por favor, seleccione <b>type of database</b> que est� a utilizar. Se estiver a utilizar o SQL Server ou o Microsoft Access, por favor, seleccione ODBC.");
	define("DB_PHP_LIB_FIELD", "Biblioteca PHP");
	define("DB_HOST_FIELD", "Nome do Host");
	define("DB_HOST_DESC", "Por favor, digite o <b>name</b> ou <b>IP address of the server</b> no qual a sua base de dados ViArt ir� correr. Se estiver a correr a sua base de dados no seu PC local, ent�o poder� deixar apenas como \"<b>localhost</b>\" e a porta em branco. Se estiver a utilizar uma base de dados proveniente do servidor da empresa, por favor, veja a documenta��o do servidor da sua empresa para as defini��es do servidor.");
	define("DB_PORT_FIELD", "N�mero da Porta");
	define("DB_NAME_FIELD", "Nome da Base de Dados / DSN");
	define("DB_NAME_DESC", "Se estiver a utilizar uma base de dados como o MySQL ou o PostgreSQL, por favor, digite <b>name of the database</b> onde gostasse que o ViArt criasse as suas tabelas. Esta base de dados j� dever� existir. Se est� a instalar o ViArt apenas com o prop�sito de o testar no seu PC local, ent�o a maioria dos sistemas possui \"<b>test</b>\" base de dados que pode utilizar. Sen�o, por favor, crie uma base de dados similar � \"viart\" e use-a. Se estiver a utilizar o Microsoft Access ou o SQL Server, o nome da base de dados dever� ser <b>name of the DSN</b> que tinha ajustado na sec��o Data Sources (ODBC) do seu Painel de Controlo.");
	define("DB_USER_FIELD", "Nome do Utilizador");
	define("DB_PASS_FIELD", "Senha");
	define("DB_USER_PASS_DESC", "<b>Username</b> e <b>Password</b> - por favor, digite o Nome de Utilizador e a Senha que pretende utilizar para ter acesso � base de dados. Se estiver a utilizar um teste de instala��o local, o Nome de Utilizador �, provavelmente, \"<b>root</b>\" e a Senha fica, provavelmente, em branco. Mas repare que, apesar de ser bom testar, n�o � seguro em servidores de produ��o.");
	define("DB_PERSISTENT_FIELD", "Conex�o persistente");
	define("DB_PERSISTENT_DESC", "to use MySQL or Postgre persistent connections, tick this box. If you do not know what it means, then leaving it unticked is probably best.");
	define("DB_CREATE_DB_FIELD", "Criar Base de Dados");
	define("DB_CREATE_DB_DESC", "para criar base de dados clique nesta caixa. Funciona apenas para o MySQL e o Postgre.");
	define("DB_POPULATE_FIELD", "Carregar a Base de Dados");
	define("DB_POPULATE_DESC", "para criar a estrutura tabelar da base de dados e carreg�-la com dados, assinale na caixa");
	define("DB_TEST_DATA_FIELD", "Testar Dados");
	define("DB_TEST_DATA_DESC", "para adicionar alguns dados de teste � sua base de dados, assinale na caixa");
	define("ADMIN_EMAIL_FIELD", "E-mail do Administrador");
	define("ADMIN_LOGIN_FIELD", "Login do Administrador");
	define("ADMIN_PASS_FIELD", "Senha do Administrator");
	define("ADMIN_CONF_FIELD", "Confirmar a Senha");
	define("DATETIME_SHOWN_FIELD", "Formato da Data e da Hora (mostrado no site)");
	define("DATE_SHOWN_FIELD", "Formato da Data (mostrado no site)");
	define("DATETIME_EDIT_FIELD", "Formato da Data e da Hora (para editar)");
	define("DATE_EDIT_FIELD", "Formato da Data (para editar)");
	define("DATE_FORMAT_COLUMN", "Formato da Data");
	define("CURRENT_DATE_COLUMN", "Data Actual");

	define("DB_LIBRARY_ERROR", "As fun��es de PHP para {db_library} n�o est�o definidas. Por favor, verifique as defini��es da sua base de dados no seu ficheiro de configura��o - php.ini.");
	define("DB_CONNECT_ERROR", "N�o conecta � base de dados. Por favor, verifique os par�metros da sua base de dados.");
	define("INSTALL_FINISHED_ERROR", "Processo de instala��o finalizado.");
	define("WRITE_FILE_ERROR", "N�o tem permiss�o para gravar no arquivo <b>'includes/var_definition.php'</b>. Por favor, altere as permiss�es de arquivo antes de continuar.");
	define("WRITE_DIR_ERROR", "N�o tem permiss�o para gravar na pasta <b>'includes/'</b>. Por favor, altere as permiss�es de pasta antes de continuar.");
	define("DUMP_FILE_ERROR", "O ficheiro Dump '{file_name}' n�o foi encontrado.");
	define("DB_TABLE_ERROR", "A tabela '{table_name}' n�o foi encontrada. Por favor, construa a base de dados com os dados necess�rios.");
	define("TEST_DATA_ERROR", "Verificar <b>{POPULATE_DB_FIELD}</b> antes de preencher as tabelas com dados de teste");
	define("DB_HOST_ERROR", "O nome do host que especificou n�o foi encontrado.");
	define("DB_PORT_ERROR", "N�o conecta ao servidor da base de dados, utilizando a porta especificada.");
	define("DB_USER_PASS_ERROR", "O Nome do Utilizador e/ou a Senha que especificou n�o est�o os correctos.");
	define("DB_NAME_ERROR", "As defini��es de login estavam correctas, mas a base de dados '{db_name}' n�o foi encontrada.");

	// upgrade messages
	define("UPGRADE_TITLE", "Actualiza��o ViArt SHOP");
	define("UPGRADE_NOTE", "Nota: por favor, fa�a um backup da base de dados antes de continuar");
	define("UPGRADE_AVAILABLE_MSG", "Upgrade da base de dados dispon�vel");
	define("UPGRADE_BUTTON", "Actualizar a base de dados para {version_number}, agora");
	define("CURRENT_VERSION_MSG", "Vers�o actualmente instalada");
	define("LATEST_VERSION_MSG", "Vers�o dispon�vel para instala��o");
	define("UPGRADE_RESULTS_MSG", "Resultados da actualiza��o");
	define("SQL_SUCCESS_MSG", "Consulta SQL bem sucedida");
	define("SQL_FAILED_MSG", "Consulta SQL mal sucedida");
	define("SQL_TOTAL_MSG", "Total de consultas SQL executadas");
	define("VERSION_UPGRADED_MSG", "A sua base de dados foi actualizada para");
	define("ALREADY_LATEST_MSG", "J� possui a �ltima vers�o");
	define("DOWNLOAD_NEW_MSG", "Uma nova vers�o foi encontrada");
	define("DOWNLOAD_NOW_MSG", "Download da vers�o {version_number}, agora");
	define("DOWNLOAD_FOUND_MSG", "Detect�mos que a nova vers�o {version_number} est� dispon�vel para download. Por favor, clique no link abaixo para iniciar o download. Depois de concluir o download e substitutir os ficheiros, n�o se esque�a de correr o Upgrade novamente.");
	define("NO_XML_CONNECTION", "Aviso! A conex�o para 'http://www.viart.com/' n�o pode ser estabelecida");

?>