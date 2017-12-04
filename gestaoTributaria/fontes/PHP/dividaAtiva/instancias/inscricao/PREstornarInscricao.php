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
    * Página de Formulario de PRocessamento para exclusão de Dívida Ativa

    * Data de Criação   : 02/08/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: PREstornarInscricao.php 63839 2015-10-22 18:08:07Z franver $

    *Casos de uso: uc-05.04.02

*/

/*
$Log$
Revision 1.6  2007/08/15 21:19:07  cercato
alterando funcionamento do estorno.

Revision 1.5  2007/08/14 17:58:03  dibueno
*** empty log message ***

Revision 1.4  2007/08/14 15:14:53  cercato
adicionando exercicio em funcao de alteracao na base de dados.

Revision 1.3  2007/08/10 15:45:37  cercato
correcao do estorno.

Revision 1.2  2007/08/08 14:08:44  cercato
cancelando parcelas do ultimo parcelamento.

Revision 1.1  2007/08/02 19:37:10  cercato
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaCancelada.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaEstorno.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATProcessoEstorno.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelamento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcela.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarneDevolucao.class.php" );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

//Define o nome dos arquivos PHP
$pgForm = "FMEstornarInscricao.php";

Sessao::remove('link');
Sessao::remove('stLink');

$arInscricao = explode ( '/', $_REQUEST['inCodInscricao'] );
$inCodInscricao = $arInscricao[0];
$inExercicio    = $arInscricao[1];
$stMotivo       = $_REQUEST['stMotivo'];

$stFiltro = "where a.cod_acao = '".Sessao::read('acao')."'";
$obTModeloDocumento = new TAdministracaoModeloDocumento;
$obTModeloDocumento->recuperaRelacionamento($rsDocumentos, $stFiltro);

$stFiltro = " WHERE cod_inscricao = ".$inCodInscricao." AND exercicio = '".$inExercicio."'";
$obTDividaCancelada = new TDATDividaCancelada;
$obTDividaCancelada->recuperaTodos( $rsInscricao, $stFiltro );
if ( !$rsInscricao->Eof() ) {
    SistemaLegado::alertaAviso( $pgForm."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'], "Inscrição de Dívida Ativa (".$inCodInscricao.") já estava cancelada.", "n_erro", "erro", Sessao::getId(), "../" );
    exit;
}

$obTDATDividaEstorno = new TDATDividaEstorno;
$obTDATDividaEstorno->recuperaTodos( $rsInscricao, $stFiltro );
if ( !$rsInscricao->Eof() ) {
    SistemaLegado::alertaAviso( $pgForm."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'], "Inscrição de Dívida Ativa (".$inCodInscricao.") já estava estornada.", "n_erro", "erro", Sessao::getId(), "../" );
    exit;
}

$obTDATDividaEstorno->RecuperaInscricoesPagas( $rsInscricao, $inCodInscricao, $inExercicio );
$boTemPagas = false;
while ( !$rsInscricao->Eof() ) {
    $boTodasCanceladas = false;
    if ( $rsInscricao->getCampo("total_paga") > 0 ) {
        $boTemPagas = true;
        break;
    }

    if ( $rsInscricao->getCampo("total_cancelada") >= $rsInscricao->getCampo("total_geral") ) {
        $boTodasCanceladas = true;
    }

    $rsInscricao->proximo();
}

if ($boTemPagas) {
    SistemaLegado::alertaAviso( $pgForm."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'], "Inscrição de Dívida Ativa (".$inCodInscricao.") contém pagamentos. Não pode ser estornada!", "n_erro", "erro", Sessao::getId(), "../" );
    exit;
}

if (!$boTodasCanceladas) {
    SistemaLegado::alertaAviso( $pgForm."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'], "Inscrição de Dívida Ativa (".$inCodInscricao.") possuí cobranças em aberto. Não pode ser estornada!", "n_erro", "erro", Sessao::getId(), "../" );
    exit;
}

$obTDATDividaEstorno->RecuperaNumeracaoCarnesCanceladosDivida( $rsCarnes, $inCodInscricao, $inExercicio );

$stFiltro = " WHERE cod_inscricao = ".$inCodInscricao." AND exercicio = '".$inExercicio."'";
$obTDATDividaParcelamento = new TDATDividaParcelamento;
$obTDATDividaParcelamento->recuperaTodos( $rsListaNumeracao, $stFiltro, " num_parcelamento ASC LIMIT 1 " );
$inNumeroParcelamento = $rsListaNumeracao->getCampo( "num_parcelamento" );

$obTDATDividaParcelamento->recuperaTodos( $rsListaNumeracao, $stFiltro, " num_parcelamento DESC LIMIT 1 " );
$inNumeroUltimoParcelamento = $rsListaNumeracao->getCampo( "num_parcelamento" );

$stFiltraParcelas = " WHERE num_parcelamento = ".$inNumeroUltimoParcelamento." AND paga = FALSE AND cancelada = FALSE ";
$obTDATDividaParcela = new TDATDividaParcela;
$obTDATDividaParcela->recuperaTodos( $rsListaParcelas, $stFiltraParcelas );

/* VERIFICA SE A INSCRICAO ESTÁ COM DOCUMENO DE COBRANÇA JUDICIAL */
/**********************                                           */
$stFiltro = "\n WHERE \n ";
$stFiltro .=" ddp.cod_inscricao = ".$inCodInscricao." \n";
$stFiltro .=" AND ddp.exercicio = '".$inExercicio."' \n";
$stFiltro .=" AND ddp.num_parcelamento = ".$inNumeroUltimoParcelamento." \n";
$stFiltro .=" AND ddoc.cod_tipo_documento = 3 \n";
$obTDividaDocumento = new TDATDividaDocumento;
$obTDividaDocumento->recuperaTipoDocumentoUltimoParcelamento( $rsInscricao, $stFiltro );
if ( !$rsInscricao->Eof() && !$rsListaParcelas->Eof() ) {
    sistemaLegado::alertaAviso( $pgForm."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'], "Inscrição de Dívida Ativa (".$inCodInscricao.") em Cobrança Judicial.", "n_erro", "erro", Sessao::getId(), "../" );
    exit;
}

/* VERIFICA SE A INSCRICAO ESTÁ COM DOCUMENO DE COBRANÇA JUDICIAL */

#exit;

$obTARRCarneDevolucao = new TARRCarneDevolucao;

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTDividaCancelada );

    $obTDATDividaEstorno->setDado ('exercicio',      $inExercicio        );
    $obTDATDividaEstorno->setDado ('cod_inscricao',  $inCodInscricao     );
    $obTDATDividaEstorno->setDado ('numcgm',         Sessao::read('numCgm')     );
    $obTDATDividaEstorno->setDado ('motivo',         $stMotivo           );
    $obTDATDividaEstorno->inclusao();

    if ($_REQUEST["inProcesso"]) {
        $arProcesso = explode( "/", $_REQUEST["inProcesso"] );
        $obTDATProcessoEstorno = new TDATProcessoEstorno;
        $obTDATProcessoEstorno->setDado( 'cod_inscricao', $inCodInscricao );
        $obTDATProcessoEstorno->setDado( 'exercicio', $inExercicio );
        $obTDATProcessoEstorno->setDado( 'cod_processo', $arProcesso[0] );
        $obTDATProcessoEstorno->setDado( 'ano_exercicio', $arProcesso[1] );
        $obTDATProcessoEstorno->inclusao();
    }

    while ( !$rsListaParcelas->Eof() ) {
        $obTDATDividaParcela->setDado( 'num_parcelamento', $rsListaParcelas->getCampo("num_parcelamento") );
        $obTDATDividaParcela->setDado( 'num_parcela', $rsListaParcelas->getCampo("num_parcela") );
        $obTDATDividaParcela->setDado( 'cancelada', true );
        $obTDATDividaParcela->alteracao();
        $rsListaParcelas->proximo();
    }

    while ( !$rsCarnes->Eof() ) {

        $obTARRCarneDevolucao->setDado( 'numeracao', $rsCarnes->getCampo("numeracao") );
        $obTARRCarneDevolucao->setDado( 'cod_convenio', "-1" );
        $obTARRCarneDevolucao->exclusao();
        $rsCarnes->proximo();
    }

    $obTDATDividaDocumento = new TDATDividaDocumento;
    $arDocumentos = array();
    $inTotalDocumentos = 0;
    while ( !$rsDocumentos->Eof() ) {
        $obTDATDividaDocumento->setDado( "num_parcelamento", $inNumeroParcelamento );
        $obTDATDividaDocumento->setDado( "cod_documento", $rsDocumentos->getCampo("cod_documento") );
        $obTDATDividaDocumento->setDado( "cod_tipo_documento", $rsDocumentos->getCampo("cod_tipo_documento") );
        $obTDATDividaDocumento->inclusao();

        $inTotalDocumentos++;
        $rsDocumentos->proximo();
    }

Sessao::encerraExcecao();

if ($_REQUEST["boEmissaoDocumento"] == "on") { //boEmissaoDocumento
    $stCaminho = CAM_GT_DAT_INSTANCIAS."emissao/LSManterEmissao.php";
    $stParametros = "&stTipoModalidade=emissao";
    $stParametros .= "&stCodAcao=".Sessao::read('acao');
    $stParametros .= "&stOrigemFormulario=cancelamento_divida";
    $stParametros .= "&inNumeroParcelamento=".$inNumeroParcelamento;

    sistemaLegado::alertaAviso( $stCaminho."?".Sessao::getId().$stParametros."&stAcao=".$_REQUEST['stAcao'],"Inscrição de Dívida Ativa (".$inCodInscricao.") Estornada.", $_REQUEST['stAcao'],"aviso", Sessao::getId(), "../");
} else {
    sistemaLegado::alertaAviso( $pgForm."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'],"Inscrição de Dívida Ativa (".$inCodInscricao.") Estornada.", $_REQUEST['stAcao'],"aviso", Sessao::getId(), "../" );
}
