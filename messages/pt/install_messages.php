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
	define("INSTALL_TITLE", "Instalação ViArt SHOP");

	define("INSTALL_STEP_1_TITLE", "Instalação: 1º Passo");
	define("INSTALL_STEP_1_DESC", "Obrigado por escolher a ViArt SHOP. Para continuar a instalação, por favor, preencha os dados pedidos abaixo. Por favor, certifique-se de que a base de dados que seleccionar já existe. Se instalar numa base de dados que utilize ODBC (ex.: MS Access), primeiro, deverá criar um DSN para prosseguir.");
	define("INSTALL_STEP_2_TITLE", "Instalação: 2º Passo");
	define("INSTALL_STEP_2_DESC", " ");
	define("INSTALL_STEP_3_TITLE", "Instalação: 3º Passo");
	define("INSTALL_STEP_3_DESC", "Por favor, seleccione um layout do site. Terá a possibilidade de alterar o layout posteriormente.");
	define("INSTALL_FINAL_TITLE", "Fim da Instalação");
	define("SELECT_DATE_TITLE", "Seleccionar o Formato da Data");

	define("DB_SETTINGS_MSG", "Definições da Base de Dados");
	define("DB_PROGRESS_MSG", "A carregar o progresso da estrutura da base de dados");
	define("SELECT_PHP_LIB_MSG", "Seleccionar a Biblioteca PHP");
	define("SELECT_DB_TYPE_MSG", "Seleccionar o Tipo de Base de Dados");
	define("ADMIN_SETTINGS_MSG", "Definições Administrativas");
	define("DATE_SETTINGS_MSG", "Formatos da Data");
	define("NO_DATE_FORMATS_MSG", "Não há formatos da data disponíveis");
	define("INSTALL_FINISHED_MSG", "Até este ponto, a instalação básica está concluída. Por favor, verifique as definições na secção de administração e efectue as necessárias modificações.");
	define("ACCESS_ADMIN_MSG", "Para ter acesso à secção da administração, clique aqui");
	define("ADMIN_URL_MSG", "URL da Administração");
	define("MANUAL_URL_MSG", "URL do Manual");
	define("THANKS_MSG", "Obrigado por ter escolhido <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Tipo de Base de Dados");
	define("DB_TYPE_DESC", "Por favor, seleccione <b>type of database</b> que está a utilizar. Se estiver a utilizar o SQL Server ou o Microsoft Access, por favor, seleccione ODBC.");
	define("DB_PHP_LIB_FIELD", "Biblioteca PHP");
	define("DB_HOST_FIELD", "Nome do Host");
	define("DB_HOST_DESC", "Por favor, digite o <b>name</b> ou <b>IP address of the server</b> no qual a sua base de dados ViArt irá correr. Se estiver a correr a sua base de dados no seu PC local, então poderá deixar apenas como \"<b>localhost</b>\" e a porta em branco. Se estiver a utilizar uma base de dados proveniente do servidor da empresa, por favor, veja a documentação do servidor da sua empresa para as definições do servidor.");
	define("DB_PORT_FIELD", "Número da Porta");
	define("DB_NAME_FIELD", "Nome da Base de Dados / DSN");
	define("DB_NAME_DESC", "Se estiver a utilizar uma base de dados como o MySQL ou o PostgreSQL, por favor, digite <b>name of the database</b> onde gostasse que o ViArt criasse as suas tabelas. Esta base de dados já deverá existir. Se está a instalar o ViArt apenas com o propósito de o testar no seu PC local, então a maioria dos sistemas possui \"<b>test</b>\" base de dados que pode utilizar. Senão, por favor, crie uma base de dados similar à \"viart\" e use-a. Se estiver a utilizar o Microsoft Access ou o SQL Server, o nome da base de dados deverá ser <b>name of the DSN</b> que tinha ajustado na secção Data Sources (ODBC) do seu Painel de Controlo.");
	define("DB_USER_FIELD", "Nome do Utilizador");
	define("DB_PASS_FIELD", "Senha");
	define("DB_USER_PASS_DESC", "<b>Username</b> e <b>Password</b> - por favor, digite o Nome de Utilizador e a Senha que pretende utilizar para ter acesso à base de dados. Se estiver a utilizar um teste de instalação local, o Nome de Utilizador é, provavelmente, \"<b>root</b>\" e a Senha fica, provavelmente, em branco. Mas repare que, apesar de ser bom testar, não é seguro em servidores de produção.");
	define("DB_PERSISTENT_FIELD", "Conexão persistente");
	define("DB_PERSISTENT_DESC", "to use MySQL or Postgre persistent connections, tick this box. If you do not know what it means, then leaving it unticked is probably best.");
	define("DB_CREATE_DB_FIELD", "Criar Base de Dados");
	define("DB_CREATE_DB_DESC", "para criar base de dados clique nesta caixa. Funciona apenas para o MySQL e o Postgre.");
	define("DB_POPULATE_FIELD", "Carregar a Base de Dados");
	define("DB_POPULATE_DESC", "para criar a estrutura tabelar da base de dados e carregá-la com dados, assinale na caixa");
	define("DB_TEST_DATA_FIELD", "Testar Dados");
	define("DB_TEST_DATA_DESC", "para adicionar alguns dados de teste à sua base de dados, assinale na caixa");
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

	define("DB_LIBRARY_ERROR", "As funções de PHP para {db_library} não estão definidas. Por favor, verifique as definições da sua base de dados no seu ficheiro de configuração - php.ini.");
	define("DB_CONNECT_ERROR", "Não conecta à base de dados. Por favor, verifique os parâmetros da sua base de dados.");
	define("INSTALL_FINISHED_ERROR", "Processo de instalação finalizado.");
	define("WRITE_FILE_ERROR", "Não tem permissão para gravar no arquivo <b>'includes/var_definition.php'</b>. Por favor, altere as permissões de arquivo antes de continuar.");
	define("WRITE_DIR_ERROR", "Não tem permissão para gravar na pasta <b>'includes/'</b>. Por favor, altere as permissões de pasta antes de continuar.");
	define("DUMP_FILE_ERROR", "O ficheiro Dump '{file_name}' não foi encontrado.");
	define("DB_TABLE_ERROR", "A tabela '{table_name}' não foi encontrada. Por favor, construa a base de dados com os dados necessários.");
	define("TEST_DATA_ERROR", "Verificar <b>{POPULATE_DB_FIELD}</b> antes de preencher as tabelas com dados de teste");
	define("DB_HOST_ERROR", "O nome do host que especificou não foi encontrado.");
	define("DB_PORT_ERROR", "Não conecta ao servidor da base de dados, utilizando a porta especificada.");
	define("DB_USER_PASS_ERROR", "O Nome do Utilizador e/ou a Senha que especificou não estão os correctos.");
	define("DB_NAME_ERROR", "As definições de login estavam correctas, mas a base de dados '{db_name}' não foi encontrada.");

	// upgrade messages
	define("UPGRADE_TITLE", "Actualização ViArt SHOP");
	define("UPGRADE_NOTE", "Nota: por favor, faça um backup da base de dados antes de continuar");
	define("UPGRADE_AVAILABLE_MSG", "Upgrade da base de dados disponível");
	define("UPGRADE_BUTTON", "Actualizar a base de dados para {version_number}, agora");
	define("CURRENT_VERSION_MSG", "Versão actualmente instalada");
	define("LATEST_VERSION_MSG", "Versão disponível para instalação");
	define("UPGRADE_RESULTS_MSG", "Resultados da actualização");
	define("SQL_SUCCESS_MSG", "Consulta SQL bem sucedida");
	define("SQL_FAILED_MSG", "Consulta SQL mal sucedida");
	define("SQL_TOTAL_MSG", "Total de consultas SQL executadas");
	define("VERSION_UPGRADED_MSG", "A sua base de dados foi actualizada para");
	define("ALREADY_LATEST_MSG", "Já possui a última versão");
	define("DOWNLOAD_NEW_MSG", "Uma nova versão foi encontrada");
	define("DOWNLOAD_NOW_MSG", "Download da versão {version_number}, agora");
	define("DOWNLOAD_FOUND_MSG", "Detectámos que a nova versão {version_number} está disponível para download. Por favor, clique no link abaixo para iniciar o download. Depois de concluir o download e substitutir os ficheiros, não se esqueça de correr o Upgrade novamente.");
	define("NO_XML_CONNECTION", "Aviso! A conexão para 'http://www.viart.com/' não pode ser estabelecida");

?>