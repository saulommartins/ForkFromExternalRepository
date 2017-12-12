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
    * Página de Listagem de Anulacao de Reserva de Saldos
    * Data de Criação   : 05/05/2005

    * @author Analista: Diego Barbosa Victória
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 31000 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.08
*/

/*
$Log$
Revision 1.10  2007/09/12 14:19:35  luciano
Ticket#10080#

Revision 1.9  2007/08/14 14:42:00  bruce
Bug#9908#

Revision 1.8  2006/07/05 20:43:33  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReservaSaldos.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ReservaSaldos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgCons = "FMConsultarEmpenho.php";

$stCaminho   = CAM_GF_ORC_INSTANCIAS."reservaSaldos/";

$obROrcamentoReservaSaldos = new ROrcamentoReservaSaldos;

$arFiltro = Sessao::read('filtro');
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
    foreach ($arFiltro['filtro'] AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
}

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "anular";
}

//Define arquivos PHP para cada acao
switch ($stAcao) {
    case 'anular'   : $pgProx = $pgForm; break;
    case 'consultar': $pgProx = $pgForm; break;
    DEFAULT         : $pgProx = $pgForm;
}

if ($_REQUEST['inCodigoEntidade']) {
    foreach ($_REQUEST['inCodigoEntidade'] as $value) {
        $stCodEntidade .= $value . " , ";
    }
    $stCodEntidade = substr($stCodEntidade,0,strlen($stCodEntidade)-2);
}

$stExercicio = ( $_REQUEST['stExercicio'] ) ? $_REQUEST['stExercicio'] : Sessao::getExercicio();

$obROrcamentoReservaSaldos->setExercicio                        ( Sessao::getExercicio()                );
$obROrcamentoReservaSaldos->obROrcamentoDespesa->setCodDespesa  ( $_REQUEST['inCodDespesa']            );
$obROrcamentoReservaSaldos->setCodReserva                       ( $_REQUEST['inCodReserva']            );
$obROrcamentoReservaSaldos->setDtValidadeInicial                ( $_REQUEST['stDtInicial']             );
$obROrcamentoReservaSaldos->setDtValidadeFinal                  ( $_REQUEST['stDtFinal']               );
$obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso ( $_REQUEST['inCodRecurso']            );
if($_REQUEST['inCodUso'] && $_REQUEST['inCodDestinacao'] && $_REQUEST['inCodEspecificacao'])
        $obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoRecurso->setDestinacaoRecurso( $_REQUEST['inCodUso'].".".$_REQUEST['inCodDestinacao'].".".$_REQUEST['inCodEspecificacao'] );

$obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoRecurso->setCodDetalhamento( $_REQUEST['inCodDetalhamento'] );

if ($_REQUEST['stReservas']=='manuais') {
    $obROrcamentoReservaSaldos->setTipo                             ( "M"                               );
} elseif ($_REQUEST['stReservas']=='automaticas') {
    $obROrcamentoReservaSaldos->setTipo                             ( "A"                               );
}
$obROrcamentoReservaSaldos->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( $stCodEntidade     );
$obROrcamentoReservaSaldos->setSituacao                             ( $_REQUEST['stSituacao']           );

if ($stAcao == 'anular') {
    $obROrcamentoReservaSaldos->setAnular( true );
}
$obROrcamentoReservaSaldos->listarReservaSaldos( $rsLista );
$rsLista->addFormatacao('vl_reserva','NUMERIC_BR');

$obLista = new Lista;
$obLista->setAjuda ( 'UC-02.01.08' );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Entidade");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Número da Reserva");
$obLista->ultimoCabecalho->setWidth(10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Dotação");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Recurso");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

if ($stAcao == "anular") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data Reserva");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data Validade");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
} elseif ($stAcao == "consultar") {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Situação");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
}
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor da Reserva");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_reserva" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_despesa" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "masc_recurso_red" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();

if ($stAcao == "anular") {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_validade_inicial" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_validade_final" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
} elseif ($stAcao == "consultar") {
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "situacao" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();
}
$obLista->addDado();
$obLista->ultimoDado->setCampo( "vl_reserva" );
$obLista->ultimoDado->setAlinhamento( 'DIREITA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );

if ($stAcao == "anular") {
    $obLista->ultimaAcao->addCampo( "&inCodReserva"     , "cod_reserva"     );
    $obLista->ultimaAcao->addCampo( "&inCodRecurso"     , "cod_recurso"     );
    $obLista->ultimaAcao->addCampo( "&inCodEntidade"    , "cod_entidade"    );
    $obLista->ultimaAcao->addCampo( "&dtDataReserva"    , "dt_validade_inicial" );
    $obLista->ultimaAcao->addCampo( "&inCodDespesa"     , "cod_despesa"     );
    $obLista->ultimaAcao->addCampo( "&dtDataValidade"   , "dt_validade_final"   );
    $obLista->ultimaAcao->addCampo( "&flValorReserva"   , "vl_reserva"      );
    $obLista->ultimaAcao->addCampo( "&stMotivo"         , "motivo"          );
} elseif ($stAcao == "consultar") {
    $obLista->ultimaAcao->addCampo( "&inCodReserva"     , "cod_reserva"     );
    $obLista->ultimaAcao->addCampo( "&inCodRecurso"     , "cod_recurso"     );
    $obLista->ultimaAcao->addCampo( "&stExercicio"      , "exercicio"       );
    $obLista->ultimaAcao->addCampo( "&inCodEntidade"    , "cod_entidade"    );
    $obLista->ultimaAcao->addCampo( "&inCodDespesa"     , "cod_despesa"     );
    $obLista->ultimaAcao->addCampo( "&dtDataReserva"    , "dt_validade_inicial" );
    $obLista->ultimaAcao->addCampo( "&dtDataValidade"   , "dt_validade_final"   );
    $obLista->ultimaAcao->addCampo( "&stMotivo"         , "motivo"          );
    $obLista->ultimaAcao->addCampo( "&flValorReserva"   , "vl_reserva"      );
    $obLista->ultimaAcao->addCampo( "&dtDataAnulacao"   , "dt_anulacao"     );
    $obLista->ultimaAcao->addCampo( "&stMotivoAnulacao" , "motivo_anulacao" );
}
    //$obLista->ultimaAcao->setLink( $pgProx."?stAcao=".$stAcao."&".Sessao::getId().$stLink );
    $obLista->ultimaAcao->setLink( $pgProx."?stAcao=".$stAcao."&".Sessao::getId().$stLink );

$obLista->commitAcao();
$obLista->show();
?>
