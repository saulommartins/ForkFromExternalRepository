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
    * Pagina de Lista de Baixa Manual
    * Data de Criação   : 23/05/2006

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: LSEstornarBaixaManual.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.6  2007/06/13 14:01:41  cercato
Bug #9387#

Revision 1.5  2006/09/15 10:55:12  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_ARR_NEGOCIO."RARRPagamento.class.php"                                              );

//Define o nome dos arquivos PHP
$stPrograma = "EstornarBaixaManual";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "estornar";
}
//MANTEM FILTRO E PAGINACAO
$link = Sessao::read( "link" );
$stLink .= "&stAcao=".$stAcao;
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
}

Sessao::write( "link", $link );

$obRARRPagamento = new RARRPagamento();
$obRARRPagamento->setCodLote( $_REQUEST["inCodLote"] );
$obRARRPagamento->obRARRCarne->setNumeracao( $_REQUEST["inNumCarne"] );
$obRARRPagamento->obRMONAgencia->setNumAgencia( $_REQUEST["inNumAgencia"] );
$obRARRPagamento->obRMONBanco->setNumBanco( $_REQUEST["inNumBanco"] );
$obRARRPagamento->obRMONAgencia->obRCGM->setNumCGM( $_REQUEST["inNumCGM"] );
$obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->obRCIMImovel->setNumeroInscricao( $_REQUEST["inInscricaoImobiliaria"] );
$obRARRPagamento->obRARRCarne->obRARRParcela->roRARRLancamento->obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST["inInscricaoEconomica"] );

$obRARRPagamento->listarEstornoBaixaManual ($rsLista);

$rsLista->addFormatacao( "valor_parcela", "NUMERIC_BR" );

$obLista = new Lista;
$obLista->setRecordSet          ( $rsLista  );
$obLista->setTitulo             ( "Registros de Carnês de Pagamento"   );
//$obLista->setMostraPaginacao    ( false     );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Numeração do Carnê");
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Nome");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Inscrição");
$obLista->ultimoCabecalho->setWidth( 30 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Crédito ou Grupo de Crédito");
$obLista->ultimoCabecalho->setWidth( 8 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Parcela");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Valor");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();
$obLista->addCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setCampo          ( "num_carne" );
$obLista->ultimoDado->setAlinhamento    ( "CENTRO"                  );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo          ( "[numcgm] - [nomcgm]"     );
$obLista->ultimoDado->setAlinhamento    ( "CENTRO"                  );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo          ( "inscricao" );
$obLista->ultimoDado->setAlinhamento    ( "CENTRO"             );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo          ( "descricao_grupo_credito"   );
$obLista->ultimoDado->setAlinhamento    ( "CENTRO" );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo          ( "nr_parcela" );
$obLista->ultimoDado->setAlinhamento    ( "CENTRO"  );
$obLista->commitDado();
$obLista->addDado();
$obLista->ultimoDado->setCampo          ( "valor_parcela" );
$obLista->ultimoDado->setAlinhamento    ( "CENTRO"       );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao);
$obLista->ultimaAcao->addCampo( "&inNumeracao", "num_carne" );
$obLista->ultimaAcao->addCampo( "&stNomCgm", "nomcgm" );
$obLista->ultimaAcao->addCampo( "&inNumCgm", "numcgm" );
$obLista->ultimaAcao->addCampo( "&inInscricao", "inscricao" );
$obLista->ultimaAcao->addCampo( "&inOcorrencia", "ocorrencia_pagamento" );
$obLista->ultimaAcao->addCampo( "&inConvenio", "cod_convenio" );
$obLista->ultimaAcao->addCampo( "&inCodBanco", "cod_banco" );
$obLista->ultimaAcao->addCampo( "&inCodAgencia", "cod_agencia" );
$obLista->ultimaAcao->addCampo( "&inCodCalculo", "cod_calculo" );
$obLista->ultimaAcao->addCampo( "&inCodLote", "cod_lote" );
$obLista->ultimaAcao->addCampo( "&inExercicio", "exercicio" );

$obLista->ultimaAcao->setLink( $pgProc."?".Sessao::getId().$stLink );
$obLista->commitAcao();

$obLista->show();

?>
