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
    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNContratoAditivo.class.php");

$stPrograma = "ManterConfiguracaoContratoAditivo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$arLink = Sessao::read('link');

$obForm = new Form;
$obForm->setAction ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( "manter" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$arFiltro = array();

###CONTRATO
if ($_REQUEST['inCodConvenioInicial'] != '' && $_REQUEST['inCodConvenioFinal'] != '') {
    $arFiltro[] = " contrato_aditivo.num_convenio >= ".$_REQUEST['inCodConvenioInicial']." and contrato_aditivo.num_convenio <= ".$_REQUEST['inCodConvenioFinal'];

} elseif ($_REQUEST['inCodConvenioInicial'] != '') {
    $arFiltro[] = " contrato_aditivo.num_convenio >= ".$_REQUEST['inCodConvenioInicial'];

} elseif ($_REQUEST['inCodConvenioFinal'] != '') {
    $arFiltro[] = " contrato_aditivo.num_convenio <= ".$_REQUEST['inCodConvenioFinal'];
}

###CONVÊNIO
if ($_REQUEST['inCodContratoAditivoInicial'] != '' && $_REQUEST['inCodContratoAditivoFinal'] != '') {
    $arFiltro[] = " contrato_aditivo.num_contrato_aditivo >= ".$_REQUEST['inCodContratoAditivoInicial']." and contrato_aditivo.num_contrato_aditivo <= ".$_REQUEST['inCodContratoAditivoFinal'];

} elseif ($_REQUEST['inCodContratoAditivoInicial'] != '') {
    $arFiltro[] = " contrato_aditivo.num_contrato_aditivo = ".$_REQUEST['inCodContratoAditivoInicial'];

} elseif ($_REQUEST['inCodContratoAditivoFinal'] != '') {
    $arFiltro[] = " contrato_aditivo.num_contrato_aditivo = ".$_REQUEST['inCodContratoAditivoFinal'];
}

if ($_REQUEST['inCodEntidade'] != '') {
    $arFiltro[] = " contrato_aditivo.cod_entidade IN (".implode(",", $_REQUEST['inCodEntidade']).")";
}

if ( count( $arFiltro ) > 0 ) {
    $stFiltro = " WHERE " .implode ( ' and ' , $arFiltro );
}

$obTTCERNContrato = new TTCERNContratoAditivo;
$obTTCERNContrato->recuperaAditivo($rsRecordSet, $stFiltro);

$obLista = new Lista;
$obLista->setRecordSet( $rsRecordSet );
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Exercício');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Convênio');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Bimestre');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('N° Aditivo');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Valor do aditivo');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "exercicio" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "num_convenio" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "bimestre" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "num_contrato_aditivo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "valor_aditivo" );
$obLista->commitDado();

$obLista->addAcao();

$obLista->ultimaAcao->setAcao( "ALTERAR" );
$obLista->ultimaAcao->setLink( $pgForm."?stAcao=manter&".Sessao::getId() );

$obLista->ultimaAcao->addCampo("&inNumConvenio"        , "num_convenio"       );
$obLista->ultimaAcao->addCampo("&stExercicio"          , "exercicio"          );
$obLista->ultimaAcao->addCampo("&inCodEntidade"        , "cod_entidade"       );
$obLista->ultimaAcao->addCampo("&stExercicioAditivo"   , "exercicio_aditivo"   );
$obLista->ultimaAcao->addCampo("&inNumContratoAditivo" , "num_contrato_aditivo" );

$obLista->commitAcao();
$obLista->show();

?>
