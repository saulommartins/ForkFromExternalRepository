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

    $Id: $

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
$boTransacao = new Transacao;

//seta o filtro na sessao e vice-versa
if ( !Sessao::read('paginando') ) {
    foreach ($_POST as $stCampo => $stValor) {
        $filtro[$stCampo] = $stValor;
    }
    Sessao::write('pg'  , ($_GET['pg'] ? $_GET['pg']  : 0));
    Sessao::write('pos' , ($_GET['pos']? $_GET['pos'] : 0));
    Sessao::write('paginando' , true);
} else {
    Sessao::write('pg'  , $_GET['pg']);
    Sessao::write('pos' , $_GET['pos']);
}

if ( Sessao::read('filtro') ) {
    foreach ( Sessao::read('filtro') as $key => $value ) {
        $_REQUEST[$key] = $value;
    }
}

Sessao::write('paginando' , true);

$obForm = new Form;
$obForm->setAction ( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$arFiltro = array();
$inNroNota = $request->get('inNumNota');

if ( is_numeric($inNroNota) && $inNroNota == 0) {
    $inNroNota = $inNroNota."0";
}
$stFiltroAux = "";
if ($inNroNota) {
    $arFiltro[] = " nro_nota   = " . $inNroNota ;
    $stFiltroAux .= "&inNumNota=".$inNroNota;
}
if ( $request->get('inNumSerie') ) {
    $arFiltro[] = " nro_serie  = '" . $request->get('inNumSerie')."'";
    $stFiltroAux .= "&inNumSerie=".$request->get('inNumSerie');
}
if ( $request->get('dtEmissao') ) {
    $arFiltro[] = " to_char(data_emissao, 'dd/mm/yyyy') = '". $request->get('dtEmissao') ."'";
    $stFiltroAux .= "&dtEmissao=".$request->get('dtEmissao');
}
if ( $request->get('stExercicio') ) {
    $arFiltro[] = " to_date(to_char(data_emissao, 'dd/mm/yyyy'), 'dd/mm/yyyy')
                        BETWEEN to_date('01/01/". $request->get('stExercicio') ."','dd/mm/yyyy')
                            AND to_date('31/12/". $request->get('stExercicio') ."','dd/mm/yyyy')";
    $stFiltroAux .= "&stExercicio=".$request->get('stExercicio');
}
if ( $request->get('numEmpenho') ) {
    $arFiltro[] = " nota_fiscal_empenho_liquidacao.cod_empenho = ".$request->get('numEmpenho');
    $stFiltroAux .= "&numEmpenho=".$request->get('numEmpenho');
}

if ( count( $arFiltro ) > 0 ) {
    $stFiltro = " where " .implode ( ' and ' , $arFiltro );
}

if (!Sessao::read('filtroAux')) {
    Sessao::write('filtroAux',$stFiltroAux);
} else {
    $stFiltroAux = Sessao::read('filtroAux');
}

include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGONotaFiscal.class.php" );
$obTTCMGONotaFiscal = new TTCMGONotaFiscal;

$stOrdem = " ORDER BY nota_fiscal.cod_nota, nota_fiscal.nro_nota ";

$obTTCMGONotaFiscal->recuperaNotasFiscais($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

$stLink .= '&stAcao=' . $request->get('stAcao')."&stExercicio=".$request->get('stExercicio').$stFiltroAux;

$obLista = new Lista;
$obLista->setRecordSet( $rsRecordSet );
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Número da Nota');
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Série da Nota');
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Número da AIDF');
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Data de Emissão');
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Valor da Nota');
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Ação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

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
$obLista->ultimoDado->setCampo( "aidf" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "data_emissao" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "vl_nota" );
$obLista->commitDado();

$obLista->addAcao();

$stAcao = $request->get('stAcao');

if ($_REQUEST['stAcao'] == "alterar") {
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setLink( $pgForm."?stAcao=$stAcao&".Sessao::getId().$stLink );
} elseif ($_REQUEST['stAcao'] == "excluir") {
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setLink( $pgProc."?stAcao=$stAcao&".Sessao::getId() );
}
$obLista->ultimaAcao->addCampo("&inCodNota"              , "cod_nota"            );
$obLista->ultimaAcao->addCampo("&inNumNota"              , "nro_nota"            );
$obLista->ultimaAcao->addCampo("&inNumInscricaoEstadual" , "inscricao_estadual"  );
$obLista->commitAcao();
$obLista->show();
