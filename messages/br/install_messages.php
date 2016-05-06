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
	define("INSTALL_TITLE", "ViArt SHOP Installation");

	define("INSTALL_STEP_1_TITLE", "Instalação: Passo 1");
	define("INSTALL_STEP_1_DESC", "Obrigado por escolher ViArt SHOP. Para completar esta instalação, por favor preencha os detalhes abaixo. Por favor, verifique se o Bando de Dados que você selecionou já exista. Se você estiver instalando um banco de dados que use ODBC, e.g MS Access você deve criar um DSN para prosseguir");
	define("INSTALL_STEP_2_TITLE", "Instalação: Passo 2");
	define("INSTALL_STEP_2_DESC", " ");
	define("INSTALL_STEP_3_TITLE", "Instalação: Passo 3");
	define("INSTALL_STEP_3_DESC", "Por favor escolher um layout para seu site. Você poderá alterar o layout posterioramente.");
	define("INSTALL_FINAL_TITLE", "Instalação : Finalização");
	define("SELECT_DATE_TITLE", "Selecione o formato de data");

	define("DB_SETTINGS_MSG", "Parâmetros de Banco de Dados");
	define("DB_PROGRESS_MSG", "Carregando o Banco de Dados");
	define("SELECT_PHP_LIB_MSG", "Selecione biblioteca PHP");
	define("SELECT_DB_TYPE_MSG", "Selecione o tipo de Banco de Dados");
	define("ADMIN_SETTINGS_MSG", "Parâmetros Administrativo");
	define("DATE_SETTINGS_MSG", "Formato de datas");
	define("NO_DATE_FORMATS_MSG", "Formato de datas não disponivel");
	define("INSTALL_FINISHED_MSG", "A instalação básica está concluída. Por favor verifique as configurações na seção administrativa.");
	define("ACCESS_ADMIN_MSG", "Para acessar à seção de administração, clicar aqui");
	define("ADMIN_URL_MSG", "URL da administração");
	define("MANUAL_URL_MSG", "URL do manual");
	define("THANKS_MSG", "Obrigado por escolher <b>ViArt SHOP</b>.");

	define("DB_TYPE_FIELD", "Tipo Banco de Dados");
	define("DB_TYPE_DESC", "Por favor selecione o <b>tipo de banco de dados</b> que você esta utilizando. Para SQL Server ou Microsoft Access, por favor selecione ODBC.");
	define("DB_PHP_LIB_FIELD", "Biblioteca PHP");
	define("DB_HOST_FIELD", "Nome do Host");
	define("DB_HOST_DESC", "Por favor informar o <b>nome</b> ou <b>endereço IP do servidor</b> onde seu banco de dados ViArt será executado. Se você esta executando seu banco de dados no seu PC local, você provavelmente pode deixar esse campo como \"<b>localhost</b>\" e o campo Porta em branco.");
	define("DB_PORT_FIELD", "Porta");
	define("DB_NAME_FIELD", "Nome do Banco de Dados / DSN");
	define("DB_NAME_DESC", "Se você esta utilizando um banco de dados como MySQL ou PostgreSQL, por favor informar o <b>nome do banco de dados</b> onde você quer que ViArt crie as tabelas. Esse banco de dados deve existir. Se você esta utilizando Microsoft Access ou SQL Server, o nome do banco de dados deve ser o <b>nome do DSN</b> que você configurou na seção Data Sources (ODBC) do seu painel de controle.");
	define("DB_USER_FIELD", "Usuário");
	define("DB_PASS_FIELD", "Senha");
	define("DB_USER_PASS_DESC", "<b>Usuario</b> e <b>Senha</b> -  por favor informar o usuário e senha que você quer utilizar para esse banco de dados.");
	define("DB_PERSISTENT_FIELD", "Conexões persistentes");
	define("DB_PERSISTENT_DESC", "para utilizar conexões persistentes do MySQL e Postgre, marque essa caixa. Se você estiver na dúvida, deixar esse caixa desmarcada é provavelmente a melhor opção.");
	define("DB_CREATE_DB_FIELD", "Criar banco de dados");
	define("DB_CREATE_DB_DESC", "para criar o banco de dados, marque esse caixa. Funciona somente para MySQL e Postgre");
	define("DB_POPULATE_FIELD", "Carregar banco de dados");
	define("DB_POPULATE_DESC", "Para criar a estrutura de tabela do banco de dados e carregar os dados, marque essa caixa");
	define("DB_TEST_DATA_FIELD", "Testar banco de dados");
	define("DB_TEST_DATA_DESC", "para adicionar dados de teste para seu banco de dados, marque essa caixa");
	define("ADMIN_EMAIL_FIELD", "Email do administrator");
	define("ADMIN_LOGIN_FIELD", "Login do administrador");
	define("ADMIN_PASS_FIELD", "Senha do administrator");
	define("ADMIN_CONF_FIELD", "Confirmar senha");
	define("DATETIME_SHOWN_FIELD", "Formato de data e hora (exibido no site)");
	define("DATE_SHOWN_FIELD", "Formato de data (exibido no site)");
	define("DATETIME_EDIT_FIELD", "Formato de data e hora (para edição)");
	define("DATE_EDIT_FIELD", "Formato de data (para edição)");
	define("DATE_FORMAT_COLUMN", "Formato de data");
	define("CURRENT_DATE_COLUMN", "Data atual");

	define("DB_LIBRARY_ERROR", "Funções PHP para {db_library} não estão definidas. Por favor verifique seu arquivo de configuração - php.ini.");
	define("DB_CONNECT_ERROR", "Não foi possivel conectar-se ao banco de dados. Por favor verifique os parametros do seu banco de dados.");
	define("INSTALL_FINISHED_ERROR", "Processo de instalação finalizado");
	define("WRITE_FILE_ERROR", "Não tem permissão para gravar neste arquivo <b>'incluir/var_definition.php'</b>. Por favor altere as permissões antes de continuar.");
	define("WRITE_DIR_ERROR", "Não tem permissão para gravar nesta pasta <b>'incluir/'</b>. Por favor altere as permissões antes de continuar.");
	define("DUMP_FILE_ERROR", "Arquivo de Dump '{file_name}' não foi encontrado.");
	define("DB_TABLE_ERROR", "Tabela '{table_name}' não foi encontrada. Por favor enviar os dados necessários ao banco de dados.");
	define("TEST_DATA_ERROR", "Marcar <b>{POPULATE_DB_FIELD}</b> antes de enviar dados de teste para as tabelas");
	define("DB_HOST_ERROR", "O nome do host especificado não pôde ser encontrado");
	define("DB_PORT_ERROR", "Não foi possível conectar-se ao banco de dados utilizando a porta especificada.");
	define("DB_USER_PASS_ERROR", "O usuário ou senha especificada não é correto");
	define("DB_NAME_ERROR", "As informações de login estão corretas, mas o '{db_name}' do banco de dados não pôde ser encontrado");

	// upgrade messages
	define("UPGRADE_TITLE", "Atualização do ViArt SHOP ");
	define("UPGRADE_NOTE", "Nota: Recomendamos fazer um backup antes de continuar");
	define("UPGRADE_AVAILABLE_MSG", "Atualização de Banco de Dados disponivel");
	define("UPGRADE_BUTTON", "Atualizar  Banco de Dados para {version_number} agora");
	define("CURRENT_VERSION_MSG", "Versão atual instalada");
	define("LATEST_VERSION_MSG", "Versão disponivel para instalação");
	define("UPGRADE_RESULTS_MSG", "Resultado da atualização");
	define("SQL_SUCCESS_MSG", "Consultas SQL realizada com sucesso");
	define("SQL_FAILED_MSG", "Consultas SQL falharam");
	define("SQL_TOTAL_MSG", "Total de consultas SQL realizadas");
	define("VERSION_UPGRADED_MSG", "Seu banco de dados foi atualizado para ");
	define("ALREADY_LATEST_MSG", "Voce já está com a ultima versão");
	define("DOWNLOAD_NEW_MSG", "Uma nova versão foi encontrada");
	define("DOWNLOAD_NOW_MSG", "Baixar a versão {version_number} agora");
	define("DOWNLOAD_FOUND_MSG", "Nos detectamos que a nova versão {version_number} esta disponivel para download. Favor clique no link abaixo para iniciar o downloada. Depois de completar o download e substituir os arquivos, não esqueça de fazer a rotina de atualização novamente.");
	define("NO_XML_CONNECTION", "Aviso! A conexão para 'http://www.viart.com/' não pode ser estabelecida");

?>