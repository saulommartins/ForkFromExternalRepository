<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
    * Página  de Formulário para cadastramento de Monviemtação de SEFIP
    * Data de Criação: 06/02/2006

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.04.40

    $Id: FMManterMovimentacaoSEFIP.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO . 'RPessoalMovimentoSefipSaida.class.php' );

$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

$atLink =Sessao::read("link");
//Define o nome dos arquivos PHP
$stPrograma = "ManterMovimentacaoSEFIP";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$atLink["pg"]."&pos=".$atLink["pos"];
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

// está variavel sera usada para listar as categorias de FGTS no spanListaCategorias
Sessao::write('aCategorias',Array());

if ($stAcao == 'alterar') {
    $inCodSefip = $_GET['cod_sefip']? $_GET['cod_sefip'] : $_POST['cod_sefip'];
    Sessao::write("inCodSefip",$inCodSefip);

    $jsOnload = "executaFuncaoAjax('gerarSpanMovimentacaoAlterar');";

} else {
    $jsOnload = "executaFuncaoAjax('gerarSpanMovimentacao');";
}

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

//códIGO sEFIP
$obTextCodigoSEFIP = new TextBox;
$obTextCodigoSEFIP->setRotulo            ( 'Código SEFIP'                              );
$obTextCodigoSEFIP->setTitle             ( 'Informe o código de movimentação da SEFIP' );
$obTextCodigoSEFIP->setName              ( 'stCodigoSEFIP'                             );
$obTextCodigoSEFIP->setId                ( 'stCodigoSEFIP'                             );
$obTextCodigoSEFIP->setValue             ( $stCodigoSEFIP                              );
$obTextCodigoSEFIP->setSize              ( 3                                           );
$obTextCodigoSEFIP->setMaxLength         ( 3                                           );
$obTextCodigoSEFIP->setNull              ( false                                       );
$obTextCodigoSEFIP->setEspacosExtras     ( false                                       );
$obTextCodigoSEFIP->setCaracteresAceitos ( '[0-9a-zA-Z áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ/-]' );
$obTextCodigoSEFIP->setToUpperCase       ( true                                        );
if ($stAcao == "alterar") {
    $obTextCodigoSEFIP->setReadonly(true);
}

//fim código SEFIP

//descrição SEFIP
$obTextDescricaoSEFIP = new TextArea;
$obTextDescricaoSEFIP->setRows                 ( 2                                               );
$obTextDescricaoSEFIP->setMaxCaracteres        ( 200                                             );
$obTextDescricaoSEFIP->setRotulo               ( "Descrição"                                     );
$obTextDescricaoSEFIP->setTitle                ( "Informe a descrição da movimentação da SEFIP." );
$obTextDescricaoSEFIP->setName                 ( "stDescricao"                                   );
$obTextDescricaoSEFIP->setId                 ( "stDescricao"                                   );
$obTextDescricaoSEFIP->setValue                ( $stDescricao                                    );
$obTextDescricaoSEFIP->setNull                 ( false                                           );
$obTextDescricaoSEFIP->setStyle                ( "width: 600px"                                  );
$obTextDescricaoSEFIP->obEvento->setOnKeyPress ( "return validaExpressao( this, event, '[0-9a-zA-Z áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ/-]');" );
//Fim descrição SEFIP

//Movimentação da SEFIP (Afastamento ou Retorno)
//Opção Afastamento
$obRdoAfastamento = new Radio;
$obRdoAfastamento->setname               ( "stMovimentacao"                                              );
$obRdoAfastamento->setId               ( "stMovimentacao"                                              );
$obRdoAfastamento->setID                 ( 'stMovimentacao'                                              );
$obRdoAfastamento->setTitle              ( "Indique se a movimentação SEFIP é de afastamento ou Retorno." );
$obRdoAfastamento->setRotulo             ( "Movimentação SEFIP"                                          );
$obRdoAfastamento->setLabel              ( "Afastamento"                                                 );
$obRdoAfastamento->setValue              ( "A"                                                           );
$obRdoAfastamento->setChecked            ( true                      );
$obRdoAfastamento->obEvento->setOnChange ( "montaParametrosGET('gerarSpanMovimentacao','stMovimentacao');"                                    );
if ($stAcao == "alterar") {
    $obRdoAfastamento->setDisabled(true);
}

//opção Retorno
$obRdoRetorno = new Radio;
$obRdoRetorno->setname("stMovimentacao");
$obRdoRetorno->setId("stMovimentacao");
$obRdoRetorno->setTitle("Indique se a movimentação SEFIP é de afastamento ou Retorno.");
$obRdoRetorno->setRotulo("*Movimentação SEFIP");
$obRdoRetorno->setLabel("Retorno");
$obRdoRetorno->setValue("R");
$obRdoRetorno->obEvento->setOnChange ("montaParametrosGET('gerarSpanMovimentacao','stMovimentacao');");
if ($stAcao == "alterar") {
    $obRdoRetorno->setDisabled(true);
}

// Span Movimentação
$obspnSpanMovimentacao = new Span;
$obspnSpanMovimentacao->setID("spnSpanMovimentacao");

$obHdnMovimentacao = new HiddenEval();
$obHdnMovimentacao->setName("hdnMovimentacao");
$obHdnMovimentacao->setId("hdnMovimentacao");
// FIm Span MOvimentacao

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ($pgProc );
$obForm->setTarget ('oculto');

//Definição do Formulário
$obFormulario = new Formulario;

$obFormulario->addForm   ( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden ( $obHdnAcao            );
$obFormulario->addHidden ( $obHdnCtrl            );
$obFormulario->addTitulo ( "Informações da SEFIP" );
$obFormulario->addComponente ( $obTextCodigoSEFIP );
$obFormulario->addComponente ( $obTextDescricaoSEFIP );
$obFormulario->AgrupaComponentes( array($obRdoAfastamento,$obRdoRetorno) );
$obFormulario->addSpan ( $obspnSpanMovimentacao );
$obFormulario->addHidden($obHdnMovimentacao,true);
if ($stAcao == "incluir") {
    $obBtnClean = new Button;
    $obBtnClean->setName                    ( "btnClean"                 );
    $obBtnClean->setValue                   ( "Limpar"                   );
    $obBtnClean->setTipo                    ( "button"                   );
    $obBtnClean->obEvento->setOnClick       ( "document.frm.reset();executaFuncaoAjax('limpaForm');" );
    $obBtnOK = new Ok;
    $obFormulario->defineBarra(array ( $obBtnOK , $obBtnClean ) );
} else {
    $obFormulario->Cancelar( $pgList );
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
