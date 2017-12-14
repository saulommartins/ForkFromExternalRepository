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
    * Pagina de filtro para o moudlo RECIBO DESPESA EXTRA
    * Data de Criação   : 04/09/2006

    * @author Desenvolvedor: Bruce Cruz de Sena

    $Id: LSReciboDespesaExtra.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeUsuario.class.php'                        );
include_once ( CAM_GF_ORC_COMPONENTES.'IPopUpRecurso.class.php'                                        );
include_once ( CAM_GF_ORC_COMPONENTES.'IIntervaloPopUpDotacao.class.php'                               );
include_once ( CAM_GF_EMP_COMPONENTES.'IPopUpCredor.class.php'                                         );
include_once ( CAM_GF_CONT_COMPONENTES.'IPopUpContaAnalitica.class.php'                                );
include_once ( CAM_GF_ORC_COMPONENTES.'ISelectMultiploEntidadeUsuario.class.php'                       );
include_once ( CAM_FW_HTML."MontaOrgaoUnidade.class.php"                                               );
include_once ( CAM_GF_TES_MAPEAMENTO.'TTesourariaReciboExtra.class.php'                                );

//Define o nome dos arquivos PHP
$stPrograma = "ReciboDespesaExtra";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgCons       = "FMConsultar".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

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
    Sessao::write('pg',$inPg);
    Sessao::write('pos',$inPos);
    foreach ($arFiltro['filtro'] AS $stKey=>$stValue) {
        $_REQUEST[$stKey] = $stValue;
    }
}

$stAcao = $request->get('stAcao');

if ($stAcao == 'alterar') {
    $stAcao = 'imprimir';
}

$obTReciboExtra = new TTesourariaReciboExtra;

/**
 * Seta os filtros
 */
if ($_REQUEST ['inCodEntidade']) {
    $stEntidades = implode(',', $_REQUEST['inCodEntidade']);
    $obTReciboExtra->setDado('inCodEntidade',$stEntidades);
}
if ($_REQUEST['data']) {
    $obTReciboExtra->setDado('data_emissao',$_REQUEST['data']);
}
if ($_REQUEST['txtNumeroRecibo']) {
    $obTReciboExtra->setDado("inCodRecibo",$_REQUEST['txtNumeroRecibo']);
}
if ($_REQUEST['inCodContaDespesa']) {
    $obTReciboExtra->setDado('cod_plano',$_REQUEST['inCodContaDespesa']);
}

$obTReciboExtra->setDado('tipo_recibo','D');

$obTReciboExtra->setDado("stExercicio",Sessao::getExercicio());

if ($stAcao == "excluir" || $stAcao == "imprimir") {
    if ($stAcao == "imprimir") {
        $obTReciboExtra->setDado("boDemonstrarPagos", true);
    }
    $obTReciboExtra->recuperaReciboExtraDespesaParaAnulacao( $rsRecibos, $stFiltro );
} elseif ($stAcao == 'consultar') {
    $obTReciboExtra->recuperaReciboExtraConsulta($rsRecibos);
    $rsRecibos->addFormatacao('valor_pago','NUMERIC_BR');
    $rsRecibos->addFormatacao('valor_saldo','NUMERIC_BR');
} else {
    $obTReciboExtra->recuperaReciboExtraDespesa( $rsRecibos, $stFiltro );
}
$rsRecibos->addFormatacao('valor','NUMERIC_BR');

$obLista = new Lista;

$obLista->setRecordSet( $rsRecibos );

$obLista->setTitulo('Recibos extras');
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Entidade" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Recibo" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Conta de Despesa" );
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Valor" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

if ($stAcao == 'consultar') {
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Valor Pago" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Saldo" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();
}

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

///Entidade
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "cod_entidade" );
$obLista->commitDado();

/// Cod_Recibo/Exercicio
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "[cod_recibo_extra]/[exercicio]" );
$obLista->commitDado();

/// Data do recibo
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "dt_recibo" );
$obLista->commitDado();

/// Conta da Despesa
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
if ($stAcao == 'consultar') {
    $obLista->ultimoDado->setCampo( "[cod_plano_receita] - [nom_conta]" );
} else {
    $obLista->ultimoDado->setCampo( "[cod_plano_despesa] - [nom_conta]" );
}
$obLista->commitDado();

//Valor
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "valor" );
$obLista->commitDado();

if ($stAcao == 'consultar') {
    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "valor_pago" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "valor_saldo" );
    $obLista->commitDado();
}

// DEFININDO A AÇÃO DA LISTA  ( ANULAR = 'excluir'  OU REEMITIR = 'alterar' )
//// trocando alterar por imprimir quando for necessário

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo( "&inCodRecibo",  "cod_recibo_extra" );
$obLista->ultimaAcao->addCampo( "stExercicio",   "exercicio"        );
$obLista->ultimaAcao->addCampo( "inCodEntidade", "cod_entidade"     );
$obLista->ultimaAcao->addCampo( "stTipoRecibo",  "tipo_recibo"      );

$stCaminho   = CAM_GF_TES_INSTANCIAS."reciboDespesaExtra/";

if ($stAcao == "excluir") {
    $obLista->ultimaAcao->addCampo( "&stDescQuestao", "[cod_recibo_extra] / [exercicio]"  );
    $obLista->ultimaAcao->setLink( $stCaminho .$pgProc ."?stAcao=".$stAcao."&".Sessao::getId() );
} elseif ($stAcao == 'consultar') {
    $obLista->ultimaAcao->setLink( $pgCons ."?stAcao=".$stAcao."&".Sessao::getId() );
} else {
    $obLista->ultimaAcao->setLink( $pgProc ."?stAcao=".$stAcao."&".Sessao::getId() );
}
$obLista->commitAcao();

$obLista->show();

?>
