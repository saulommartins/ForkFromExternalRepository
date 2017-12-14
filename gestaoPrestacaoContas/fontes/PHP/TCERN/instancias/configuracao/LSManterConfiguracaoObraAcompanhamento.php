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
include_once(CAM_GPC_TCERN_MAPEAMENTO."TTCERNObraAcompanhamento.class.php");

$stPrograma = "ManterConfiguracaoObraAcompanhamento";
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
$obHdnAcao->setValue( "manter" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$arFiltro = array();

if ($_REQUEST['inCodObraInicial'] != '' && $_REQUEST['inCodObraFinal'] != '') {
    $arFiltro[] = " obra.num_obra >= ".$_REQUEST['inCodObraInicial']." and obra.num_obra <= ".$_REQUEST['inCodObraFinal'];

} elseif ($_REQUEST['inCodObraInicial'] != '') {
    $arFiltro[] = " obra.num_obra  = ".$_REQUEST['inCodObraInicial'];

} elseif ($_REQUEST['inCodObraFinal'] != '') {
    $arFiltro[] = " obra.num_obra  = ".$_REQUEST['inCodObraFinal'];
}

if ($_REQUEST['inCodEntidade'] != '') {
    $arFiltro[] = " obra.cod_entidade IN (".implode(",", $_REQUEST['inCodEntidade']).")";
}

if ( count( $arFiltro ) > 0 ) {
    $stFiltro = " where " .implode ( ' and ' , $arFiltro );
}

$obTTCERNObraAcompanhamento = new TTCERNObraAcompanhamento;
$obTTCERNObraAcompanhamento->recuperaListaAcompanhamento($rsRecordSet, $stFiltro);

$obLista = new Lista;
$obLista->setRecordSet( $rsRecordSet );
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('N° Obra');
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Obra');
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data do evento');
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Situação');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "num_obra" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "obra" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_evento" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->commitDado();

$obLista->addAcao();

$obLista->ultimaAcao->setAcao( "ALTERAR" );
$obLista->ultimaAcao->setLink( $pgForm."?stAcao=manter&".Sessao::getId() );

$obLista->ultimaAcao->addCampo("&id" , "id" );
$obLista->commitAcao();
$obLista->show();
