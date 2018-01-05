<?php

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_NEGOCIO . "RContabilidadeFundo.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ExtinguirFundo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

$obRContabilidadeFundo  = new RContabilidadeFundo;
$obRContabilidadeFundo->setExercicio(Sessao::getExercicio());

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" ); //oculto - telaPrincipal

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto TEXT para armazenar o NUMERO DO ORGAO NO ORCAMENTO
$obTxtNumUnidade = new TextBox;
$obTxtNumUnidade->setName     ( "cod_fundo" );
$obTxtNumUnidade->setValue    ( null );
$obTxtNumUnidade->setRotulo   ( "Código do Fundo" );
$obTxtNumUnidade->setNull     ( true );
$obTxtNumUnidade->setTitle    ( 'Informe o código do fundo municipal.' );
$obTxtNumUnidade->setInteiro  ( true );
$obTxtNumUnidade->setMaxLength( 10 );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm            );
$obFormulario->addHidden    ( $obHdnAcao         );
$obFormulario->addTitulo    ( "Dados para Filtro");
$obFormulario->addComponente( $obTxtNumUnidade   );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
