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
    * Data de Criação   : 19/09/2006

    * @author Analista: Cleisson Barbosa
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore

    * $Id: LSManterObra.php 32939 2008-09-03 21:14:50Z domluc $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$arLink = Sessao::read('link');

$obForm = new Form;
$obForm->setAction ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$arFiltro = array();

if ($_REQUEST['inNumContrato']) {
    $arFiltro[] = " nro_contrato = " . $_REQUEST['inNumContrato'] ;
}
if ($_REQUEST['dtPublicacao']) {
    $arFiltro[] = " data_publicacao  = to_date('" .$_REQUEST['dtPublicacao'] ."','dd/mm/yyyy')";
}
if ($_REQUEST['dtInicial']) {
    $arFiltro[] = " data_inicio = to_date('". $_REQUEST['dtInicial'] ."','dd/mm/yyyy')";
}
if ($_REQUEST['dtFinal']) {
    $arFiltro[] = " data_final  = to_date('". $_REQUEST['dtFinal'] ."','dd/mm/yyyy')";
}
if ($_REQUEST['stObjContrato']) {
    $arFiltro[] = " objeto_contrato  ilike '%". $_REQUEST['stObjContrato'] ."%'";
}

if ( count( $arFiltro ) > 0 ) {
    $stFiltro = " where " .implode ( ' and ' , $arFiltro );
}

include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOContrato.class.php" );
$obTTCMGOContrato = new TTCMGOContrato;
$obTTCMGOContrato->recuperaTodos($rsRecordSet, $stFiltro);
$rsRecordSet->addFormatacao( 'vl_contrato', 'NUMERIC_BR');

$obLista = new Lista;
$obLista->setRecordSet( $rsRecordSet );
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Contrato');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data Início');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data Fim');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Valor Contrato');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Objeto Contrato');
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "nro_contrato" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "data_inicio" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "data_final" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "vl_contrato" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "objeto_contrato" );
$obLista->commitDado();

$obLista->addAcao();
if ($stAcao == "alterar") {
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setLink( $pgForm."?stAcao=$stAcao&".Sessao::getId() );
} elseif ($stAcao == "excluir") {
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setLink( $pgProc."?stAcao=$stAcao&".Sessao::getId() );
}
$obLista->ultimaAcao->addCampo("&inNumContrato"       , "nro_contrato" );
$obLista->ultimaAcao->addCampo("&stExercicioContrato" , "exercicio"    );
$obLista->ultimaAcao->addCampo("&inCodEntidade"       , "cod_entidade" );
$obLista->ultimaAcao->addCampo("&inCodContrato"       , "cod_contrato" );
$obLista->commitAcao();
$obLista->show();
