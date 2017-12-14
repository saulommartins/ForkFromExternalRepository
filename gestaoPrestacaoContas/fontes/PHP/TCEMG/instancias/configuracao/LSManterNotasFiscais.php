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
    * Página de Listagem de Notas Fiscais
    * Data de Criação   : 05/02/2014

    * @author Analista      Sergio Luiz dos Santos
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: LSManterNotasFiscais.php 62088 2015-03-28 18:44:40Z arthur $
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

$stCaminho = CAM_GPC_TCEMG_INSTANCIAS."configuracao/";

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
    $arFiltro[] = " NF.nro_nota = '" . $inNroNota."'" ;
    $stFiltroAux .= "&inNumNota=".$inNroNota;
}
if ( $request->get('inNumSerie') ) {
    $arFiltro[] = " NF.nro_serie = '" . $request->get('inNumSerie')."'";
    $stFiltroAux .= "&inNumSerie=".$request->get('inNumSerie');
}
if ( $request->get('dtEmissao') ) {
    $arFiltro[] = " to_char(NF.data_emissao, 'dd/mm/yyyy') = '". $request->get('dtEmissao') ."'";
    $stFiltroAux .= "&dtEmissao=".$request->get('dtEmissao');
}
if ( $request->get('stExercicioNota') ) {
    $arFiltro[] = " NF.exercicio = '". $request->get('stExercicioNota') ."'";
    $stFiltroAux .= "&stExercicioNota=".$request->get('stExercicioNota');
}
if ( $request->get('stExercicioEmpenho') ) {
    $arFiltro[] = " NFEL.exercicio_empenho = '". $request->get('stExercicioEmpenho') ."'";
    $stFiltroAux .= "&stExercicioEmpenho=".$request->get('stExercicioEmpenho');
}
if ( $request->get('numEmpenho') ) {
    $arFiltro[] = " NFEL.cod_empenho = ".$request->get('numEmpenho');
    $stFiltroAux .= "&numEmpenho=".$request->get('numEmpenho');
}
if ( $request->get('inCodEntidade') ) {
    $arFiltro[] = " NF.cod_entidade = ".$request->get('inCodEntidade');
    $stFiltroAux .= "&inCodEntidade=".$request->get('inCodEntidade');
}

if ( count( $arFiltro ) > 0 ) {
    $stFiltro = " WHERE " .implode ( ' AND ' , $arFiltro );
    $stFiltro = Sessao::write('stFiltro',$stFiltro);
}

if (!Sessao::read('filtroAux')) {
    Sessao::write('filtroAux',$stFiltroAux);
} else {
    $stFiltroAux = Sessao::read('filtroAux');
}

include_once(CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscal.class.php");
$obTTCEMGNotaFiscal = new TTCEMGNotaFiscal;

$stOrdem = "";

$obTTCEMGNotaFiscal->recuperaNotasFiscais($rsRecordSet, Sessao::read('stFiltro'), $stOrdem, $boTransacao);

$rsRecordSet->addFormatacao( 'vl_nota', 'NUMERIC_BR' );

$stLink .= '&stAcao=' . $request->get('stAcao');

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
$obLista->ultimoCabecalho->addConteudo('Chave de Acesso');
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
$obLista->ultimoDado->setCampo( "chave_acesso" );
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
    $obLista->ultimaAcao->setAcao( "alterar" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgForm."?stAcao=$stAcao".Sessao::getId().$stLink.$stFiltroAux );
} elseif ($_REQUEST['stAcao'] == "excluir") {
    $obLista->ultimaAcao->setAcao( "excluir" );
    $obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?stAcao=$stAcao".Sessao::getId().$stFiltroAux );
}

$obLista->ultimaAcao->addCampo("&inCodNota"             , "cod_nota"                   );
$obLista->ultimaAcao->addCampo("inNumNota"              , "nro_nota"                   );
$obLista->ultimaAcao->addCampo("cod_entidade"           , "cod_entidade"               );
$obLista->ultimaAcao->addCampo("exercicio"              , "exercicio"                  );
$obLista->commitAcao();
$obLista->show();

?>