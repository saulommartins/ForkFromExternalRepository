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
    * Pagina de Lista de Imoveis para Consulta de Lotes de Pagamento
    * Data de Criação   : 20/12/2005

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: LSConsultarLote.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.8  2007/08/20 14:35:20  dibueno
Bug#9959#

Revision 1.7  2007/08/08 15:16:18  dibueno
Bug#9852#

Revision 1.6  2007/05/02 18:27:33  cercato
Bug #9138#

Revision 1.5  2006/09/15 11:04:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php"                                                );
include_once( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php"                                       );

//Define o nome dos arquivos PHP
$stPrograma = "ConsultarLote";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";

$link = Sessao::read( 'link' );
if ($_GET["pg"] and  $_GET["pos"]) {
    $stLink.= "&pg=".$_GET["pg"]."&pos=".$_GET["pos"];
    $link["pg"]  = $_GET["pg"];
    $link["pos"] = $_GET["pos"];
}

//USADO QUANDO EXISTIR FILTRO
//NA FL O VAR LINK DEVE SER RESETADA
if ( is_array($link) ) {
    $_REQUEST = $link;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $link[$key] = $valor;
    }

    Sessao::write( 'link', $link );
}

// instancia regra de lancamento
$obRARRCarne = new RARRCarne;
$obRARRPagamento = new RARRPagamento;
// constroi filtros
$obRARRPagamento->setExercicio ( $_REQUEST['stExercicio'] );
$obRARRPagamento->obRMONAgencia->obRMONBanco->setNumBanco ( $_REQUEST['inNumBanco'] );
//$obRARRPagamento->obRMONAgencia->obRMONBanco->consultarBanco();
$obRARRPagamento->obRMONAgencia->setNumAgencia      ( $_REQUEST['inNumAgencia'] );
//$obRARRPagamento->obRMONAgencia->consultarAgencia   ($rsAgencia);
$obRARRPagamento->obRMONAgencia->obRCGM->setNumCGM( $_REQUEST['inCodContribuinte'] );

$obRARRPagamento->setCodLote ( $_REQUEST['inCodLoteInicio'] );
$obRARRPagamento->setCodLoteFinal ( $_REQUEST['inCodLoteFinal'] );

if ($_REQUEST['dtInicio']) {
    $arDt = array();
    $arDt = explode ( '/', $_REQUEST['dtInicio'] );
    $dtTMP = $arDt[2].'-'.$arDt[1].'-'.$arDt[0];
    $obRARRPagamento->setDataLote ( $dtTMP );
}
if ($_REQUEST['dtFinal']) {

    $arDt = array();
    $arDt = explode ( '/', $_REQUEST['dtFinal']);
    $dtTMP = $arDt[2].'-'.$arDt[1].'-'.$arDt[0];
    $obRARRPagamento->setDataLoteFinal ( $dtTMP );
}

$obRARRPagamento->listarConsultaLote( $rsLotes );

//passa filtro pra sessao
Sessao::write( 'filtro', "&inCodLoteInicio=".$_REQUEST["inCodLoteInicio"]."&inCodLoteFinal=".$_REQUEST["inCodLoteFinal"]."&inCodContribuinte=".$_REQUEST["inCodContribuinte"]."&stExercicio=".$_REQUEST["stExercicio"]."&inNumBanco=".$_REQUEST["inNumBanco"]."&inNumAgencia=".$_REQUEST["inNumAgencia"]."&dtInicio=".$_REQUEST["dtInicio"]."&dtFinal=".$_REQUEST["dtFinal"]."" );

//MONTA LISTA DE IMOVEIS
$obLista = new Lista;
$obLista->setRecordSet( $rsLotes );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Lote");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Data do Lote");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Banco");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Agência");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Responsável");
$obLista->ultimoCabecalho->setWidth( 25 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Tipo");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$stTitleLanc = "<b>Valor Lançamento:</b><i>R$ [valor_lancamento]</i><br>
                <b>Número de Parcelas: </b><i>[num_parcelas]</i><br>
                <b>Número de Cotas Únicas: </b><i>[num_unicas]</i><br>
                <b>Valor Venal do Imóvel: </b><i>R$ [venal]</i><br>
                ";

$obLista->addDado();
$obLista->ultimoDado->setCampo( "cod_lote" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->ultimoDado->setTitle      ( $stTitleLanc );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "data_lote" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[num_banco] - [nom_banco]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[num_agencia] - [nom_agencia]" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "[numcgm] - [nom_cgm]" );
$obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_tipo" );
$obLista->ultimoDado->setAlinhamento( 'CENTRO' );
$obLista->commitDado();

// Define ACOES
$obLista->addAcao();
$stAcao = "consultar";
$obLista->ultimaAcao->setAcao   ( $stAcao );
$obLista->ultimaAcao->addCampo  ( "&inCodLote"          , "cod_lote"                );
$obLista->ultimaAcao->addCampo  ( "&dtDataLote"         , "data_lote"               );
$obLista->ultimaAcao->addCampo  ( "&dtDataBaixa"        , "data_baixa"              );
$obLista->ultimaAcao->addCampo  ( "&stNomTipo"          , "nom_tipo"                );
$obLista->ultimaAcao->addCampo  ( "&inNumBanco"         , "num_banco"               );
$obLista->ultimaAcao->addCampo  ( "&stNomBanco"         , "nom_banco"               );
$obLista->ultimaAcao->addCampo  ( "&inNumAgencia"       , "num_agencia"             );
$obLista->ultimaAcao->addCampo  ( "&stNomAgencia"       , "nom_agencia"             );
$obLista->ultimaAcao->addCampo  ( "&inNumCgm"           , "numcgm"                  );
$obLista->ultimaAcao->addCampo  ( "&inNomCgm"           , "nom_cgm"                 );
$obLista->ultimaAcao->addCampo  ( "&stNomArquivo"       , "nom_arquivo"             );
$obLista->ultimaAcao->addCampo  ( "&cgm_contribuinte"   , "cgm_contribuinte"        );
$obLista->ultimaAcao->addCampo  ( "&nom_contribuinte"   , "nom_contribuinte"        );
$obLista->ultimaAcao->addCampo  ( "&stExercicio"        , "exercicio"               );
$obLista->ultimaAcao->addCampo  ( "&inOcorrenciaPagamento", "ocorrencia_pagamento"  );
$obLista->ultimaAcao->addCampo  ( "&flValorLote"        , "soma"  );
$obLista->ultimaAcao->setLink($stCaminho.$pgForm."?".Sessao::getId()."&stAcao=".$stAcao."&stCtrl=consultar" );
$obLista->commitAcao();

$obLista->show();

?>
