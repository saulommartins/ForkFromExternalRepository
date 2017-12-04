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
    * Página de Formulario para Manter Vinculo de Escalas
    * Data de Criação: 10/10/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscala.class.php"                                          );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoEscalaContrato.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ManterVinculo";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgDeta = "DT".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao      = $_REQUEST['stAcao'];
$stChave     = $_REQUEST['stChave'];
$stRetorno   = $_REQUEST['stRetorno'];
$arChave     = explode("_", $stChave);
$inCodEscala = $arChave[0];

Sessao::remove('arTurnos');

$jsOnload = "executaFuncaoAjax('preencherTurnos', '&inCodEscala=$inCodEscala');";

//Mantem filtro e paginacao para navegação na página anterior ou na próxima
$link = Sessao::read("link");
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
    Sessao::write("link",$link);
}
//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }
    Sessao::write("link",$link);
}
$stLink .= "&stAcao=".$stAcao;

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//

$rsEscala = new RecordSet();

$obTPontoEscala = new TPontoEscala();
$obTPontoEscala->setDado('cod_escala',$inCodEscala);
$obTPontoEscala->recuperaPorChave($rsEscala);

$obHdnAcao =  new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setId  ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "telaPrincipal" );

$obHdnCodEscala = new Hidden();
$obHdnCodEscala->setName("inCodEscala");
$obHdnCodEscala->setId("inCodEscala");
$obHdnCodEscala->setValue($inCodEscala);

$obHdnRetorno = new Hidden();
$obHdnRetorno->setName("stRetorno");
$obHdnRetorno->setId("stRetorno");
$obHdnRetorno->setValue($stRetorno);

$obHdnChave = new Hidden();
$obHdnChave->setName("stChave");
$obHdnChave->setId("stChave");
$obHdnChave->setValue($stChave);

$obLblDescricao = new Label();
$obLblDescricao->setRotulo ( "Descrição" );
if ($rsEscala->getCampo('descricao')) {
    $obLblDescricao->setValue( $rsEscala->getCampo('cod_escala')." - ".stripslashes($rsEscala->getCampo('descricao')) );
}

$obSpanTurnos = new Span();
$obSpanTurnos->setId('spnTurnos');

$obOkLista  = new Button;
$obOkLista->setValue("Ok/Lista");
$obOkLista->obEvento->setOnClick( " jQuery('#stAcao').val('redirecionarLista'); Salvar(); " );

$obOkFiltro  = new Button;
$obOkFiltro->setValue("Ok/Filtro");
$obOkFiltro->obEvento->setOnClick( " jQuery('#stAcao').val('redirecionarFiltro'); Salvar(); " );

$obImprimir  = new Button;
$obImprimir->setValue("Imprimir");
$obImprimir->obEvento->setOnClick( " jQuery('#stAcao').val('imprimir'); Salvar(); " );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                       );
$obFormulario->addTitulo			( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden            ( $obHdnAcao                    );
$obFormulario->addHidden            ( $obHdnCodEscala               );
$obFormulario->addHidden            ( $obHdnRetorno                 );
$obFormulario->addHidden            ( $obHdnChave                   );
$obFormulario->addTitulo			( "Dados da Escala de Horário"  );
$obFormulario->addComponente        ( $obLblDescricao               );
$obFormulario->addSpan              ( $obSpanTurnos                 );
$obFormulario->defineBarra(array($obOkLista, $obOkFiltro, $obImprimir));

$obFormulario->show();

include_once($pgJS);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
