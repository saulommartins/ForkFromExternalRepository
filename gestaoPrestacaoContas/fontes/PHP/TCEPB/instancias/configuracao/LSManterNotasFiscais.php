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
    * Página de Listagem de Mapa de Compras
    * Data de Criação   : 23/09/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * $Id: LSManterObra.php 32939 2008-09-03 21:14:50Z domluc $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterNotasFiscais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$arLink = Sessao::read('link');

if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $arFiltro['filtro'][$stCampo] = $stValor;
    }
    $inPg = $_GET['pg'] ? $_GET['pg'] : 0;
    $inPos = $_GET['pos']? $_GET['pos'] : 0;
    $boPaginando = true;

    Sessao::write('filtro',$arFiltro);
    Sessao::write('pg',$inPg);
    Sessao::write('pos',$inPos);
    Sessao::write('paginando',$boPaginando);
} else {
    $inPg = $_GET['pg'];
    $inPos = $_GET['pos'];
    $arFiltro = Sessao::read('filtro');
    foreach ($arFiltro['filtro'] AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
}

$obForm = new Form;
$obForm->setAction ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$arFiltro = array();

if ($_REQUEST['inNumNota']) {
    $arFiltro[] = " nro_nota   = " . $_REQUEST['inNumNota'] ;
}
if ($_REQUEST['inNumSerie']) {
    $arFiltro[] = " nro_serie  = '" . $_REQUEST['inNumSerie']."'";
}
if ($_REQUEST['dtEmissao']) {
    $arFiltro[] = " to_char(data_emissao, 'dd/mm/yyyy') = '".$_REQUEST['dtEmissao']."'";
}

if ( count( $arFiltro ) > 0 ) {
    $stFiltro = " where " .implode ( ' and ' , $arFiltro );
}

include_once( CAM_GPC_TPB_MAPEAMENTO."TCEPBNotaFiscal.class.php" );
$obTTCMGONotaFiscal = new TCEPBNotaFiscal;
$obTTCMGONotaFiscal->recuperaTodos($rsRecordSet, $stFiltro);

$rsRecordSet->addFormatacao('vl_associado','NUMERIC_BR');

$obLista = new Lista;
$obLista->setRecordSet( $rsRecordSet );
$obLista->obPaginacao->setFiltro("&stLink=".$stLink."&stAcao=alterar" );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Empenho');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Nota da Liquidação');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data da Nota');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Valor Associado');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Número da Nota');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Série da Nota');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data da Emissão');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_empenho" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_nota_liquidacao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "dt_liquidacao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "vl_associado" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "nro_nota" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "nro_serie" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "data_emissao" );
$obLista->commitDado();

$obLista->addAcao();

$stAcao = $request->get('stAcao');

if ($_REQUEST['stAcao'] == "alterar") {
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setLink( $pgForm."?stAcao=alterar".Sessao::getId() );
} elseif ($_REQUEST['stAcao'] == "excluir") {
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setLink( $pgProc."?stAcao=excluir&".Sessao::getId() );
}
$obLista->ultimaAcao->addCampo("&inCodNota"             , "cod_nota_liquidacao");
$obLista->ultimaAcao->addCampo("&inNumNota"             , "nro_nota");
$obLista->ultimaAcao->addCampo("&inCodEntidade"         , "cod_entidade");
$obLista->ultimaAcao->addCampo("&inCodEmpenho"          , "cod_empenho");
$obLista->ultimaAcao->addCampo("&stExercicio"           , "exercicio");
$obLista->ultimaAcao->addCampo("&dtLiquidacao"          , "dt_liquidacao");
$obLista->ultimaAcao->addCampo("&dtEmissao"             , "data_emissao");
$obLista->ultimaAcao->addCampo("&inNroSerie"            , "nro_serie");
$obLista->ultimaAcao->addCampo("&inCodNotaLiquidacao"   , "cod_nota_liquidacao");
$obLista->ultimaAcao->addCampo("&inNumInscricaoEstadual", "inscricao_estadual");
$obLista->commitAcao();
$obLista->show();
