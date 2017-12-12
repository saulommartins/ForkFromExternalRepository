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
    * Lista para Empenho - Ordem de Pagamento
    * Data de Criação   : 29/03/2005

    * @author Analista: Diego B. Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2006-11-25 13:11:21 -0200 (Sáb, 25 Nov 2006) $

    * Casos de uso: uc-02.03.23
*/

/*
$Log$
Revision 1.9  2006/11/25 15:11:21  cleisson
Bug #7594#

Revision 1.8  2006/09/28 09:51:34  eduardo
Bug #7060#

Revision 1.7  2006/07/14 16:05:30  jose.eduardo
Bug #5994#

Revision 1.6  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterEstornoPagamentoOrdem";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";
//include_once( $pgJs );

$stCaminho = CAM_GF_EMP_INSTANCIAS."ordemPagamento/";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

if ($_GET["pg"] and  $_GET["pos"]) {
    $inPg = $_GET["pg"];
    $inPos = $_GET["pos"];
    Sessao::write('pg', $inPg);
    Sessao::write('pos', $inPos);
    $arLink = array('pg' => $inPg, 'pos' => $inPos);
}
if ( is_array($sessao->link) ) {
    $_GET = $arLink;
    $_REQUEST = $arLink;
} else {
    foreach ($_REQUEST as $key => $valor) {
        $arLink[$key] = $valor;
    }
    Sessao::write('pg', $arLink['pg']);
    Sessao::write('pos', $arLink['pos']);
}

//DEFINE LISTA
$obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
$rsLista                  = new RecordSet;

//DEFINICAO DO FILTRO PARA CONSULTA
$stLink = "";
if ($_REQUEST["inCodigoEntidade"]) {
    $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodigoEntidade"] );
    $stLink .= "&inCodigoEntidade=".$_REQUEST["inCodigoEntidade"];
}
if ($_REQUEST["inCodigoOrdemPagamento"]) {
    $obREmpenhoOrdemPagamento->setCodigoOrdem( $_REQUEST["inCodigoOrdemPagamento"] );
    $stLink .= "&inCodigoOrdemPagamento=".$_REQUEST["inCodigoOrdemPagamento"];

}
if ($_REQUEST["stDtVencimento"]) {
    $obREmpenhoOrdemPagamento->setDataVencimento( $_REQUEST["stDtVencimento"] );
    $stLink .= "&stDtVencimento=".$_REQUEST["stDtVencimento"];

}
if ($_REQUEST["inCodigoEmpenho"]) {
    $obREmpenhoOrdemPagamento->setCodigoEmpenho( $_REQUEST["inCodigoEmpenho"] );
    $stLink .= "&inCodEmpenhoInicial=".$_REQUEST["inCodigoEmpenho"];
    $stLink .= "&inCodEmpenhoFinal=".$_REQUEST["inCodigoEmpenho"];
}
if ($_REQUEST["stExercicioEmpenho"]) {
    $obREmpenhoOrdemPagamento->obREmpenhoEmpenho->setExercicio( $_REQUEST["stExercicioEmpenho"] );
    $stLink .= "&stExercicioEmpenho=".$_REQUEST["stExercicioEmpenho"];
}
if ($_REQUEST["inCodigoNotaLiquidacao"]) {
    $obREmpenhoOrdemPagamento->setNotaLiquidacao( $_REQUEST["inCodigoNotaLiquidacao"] );
    $stLink .= "&inCodigoNotaLiquidacao=".$_REQUEST["inCodigoNotaLiquidacao"];
}
if ($_REQUEST["inCodFornecedor"]) {
    $obREmpenhoOrdemPagamento->setFornecedor( $_REQUEST["inCodFornecedor"] );
    $stLink .= "&inCodFornecedor=".$_REQUEST["inCodFornecedor"];
}
    $obREmpenhoOrdemPagamento->setListarPaga( true );

$obREmpenhoOrdemPagamento->listarDadosEstornoPagamento( $rsLista );
$stLink .= "&stAcao=".$stAcao;

//DEFINICAO DA LISTA
$obLista = new Lista;
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;"     );
$obLista->ultimoCabecalho->setWidth   ( 5            );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ordem"      );
$obLista->ultimoCabecalho->setWidth   ( 10           );
$obLista->commitCabecalho();
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Nota/Empenho" );
$obLista->ultimoCabecalho->setWidth   ( 40           );
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "&nbsp;"     );
$obLista->ultimoCabecalho->setWidth   ( 5            );
$obLista->commitCabecalho();
$obLista->addDado();
$obLista->ultimoDado->setCampo      ( "[cod_ordem]/[exercicio]"   );
$obLista->ultimoDado->setAlinhamento( "DIREITA"     );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo      ( "nota_empenho" );
$obLista->ultimoDado->setAlinhamento( "CENTRO"       );
$obLista->commitDado();

// Define ACOES
if ($stAcao == "estornar") {
    $obLista->addAcao();
    $stAcao = "estornar";
    $obLista->ultimaAcao->setAcao( $stAcao );
    $obLista->ultimaAcao->addCampo("&inCodigoOrdem"    , "cod_ordem"        );
    $obLista->ultimaAcao->addCampo("&stExercicio"      , "exercicio"        );
    $obLista->ultimaAcao->addCampo("&stExercicioNota"  , "exercicio_nota"   );
    $obLista->ultimaAcao->addCampo("&stExercicioEmpenho","exercicio_empenho");
    $obLista->ultimaAcao->addCampo("&inCodigoEntidade" , "cod_entidade"     );
    $obLista->ultimaAcao->addCampo("&stNomeEntidade"   , "entidade"         );
    $obLista->ultimaAcao->addCampo("&inNumCGM"         , "cgm_beneficiario" );
    $obLista->ultimaAcao->addCampo("&stNomeCGM"        , "beneficiario"     );
    $obLista->ultimaAcao->addCampo("&dtDataVencimento" , "dt_vencimento"    );
    $obLista->ultimaAcao->addCampo("&inCodigoEmpenho"  , "cod_empenho"      );
    $obLista->ultimaAcao->addCampo("&flValorTotal"     , "valor_pagamento"  );
    $obLista->ultimaAcao->addCampo("&flValorAnulado"   , "valor_anulada"    );
    $obLista->ultimaAcao->addCampo("&boImplantado"     , "implantado"       );
    $obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink."&stAcao=".$stAcao );
    $obLista->commitAcao();
}
$obLista->show();

// DEFINE BOTOES
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

//DEFINE FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addHidden ($obHdnAcao          );
$obFormulario->show();
?>
