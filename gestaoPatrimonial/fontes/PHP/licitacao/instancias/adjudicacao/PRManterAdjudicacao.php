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
    * Página de Processamento de Manter Adjudicacao
    * Data de Criação: 23/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    * Casos de uso: uc-03.05.20

    $Id: PRManterAdjudicacao.php 63865 2015-10-27 13:55:57Z franver $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoAdjudicacao.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoHomologacao.class.php";
include_once CAM_GP_LIC_MAPEAMENTO."TLicitacaoAdjudicacaoAnulada.class.php";
include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoEdital.class.php" );

$stPrograma = "ManterAdjudicacao";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgGera     = "OCGeraDocumentoAdjudicacao.php";

//Define o nome dos arquivos PHP
Sessao::setTrataExcecao( true );

$stFiltro = "   AND ll.cod_licitacao = ".$_REQUEST["inCodLicitacao"]."
                AND le.cod_modalidade = ".$_REQUEST["inCodModalidade"]."
                AND le.cod_entidade = ".$_REQUEST["inCodEntidade"]."
                AND le.exercicio_licitacao = '".$_REQUEST["stExercicioLicitacao"]."'";

$obTLicitacaoEdital = new TLicitacaoEdital();
$obTLicitacaoEdital->recuperaListaEdital($rsEdital, $stFiltro);

$stMensagemErro = "";

if (SistemaLegado::comparaDatas(SistemaLegado::dataToBr($rsEdital->getCampo('dt_abertura_propostas')), $_REQUEST["stDtAdjudicacao"] )) {
    $stMensagemErro = "A data da Adjudicação deve ser maior ou igual à data de abertura de propostas.";
}

if (SistemaLegado::comparaDatas($_REQUEST["stDtAdjudicacao"], date('d/m/Y')) && $stMensagemErro == '') {
    $stMensagemErro = "A data da Adjudicação deve ser menor ou igual à data atual.";
}

if ($stMensagemErro == '') {
    $obTLicitacaoHomologacao = new TLicitacaoHomologacao;
    $obTLicitacaoHomologacao->obTLicitacaoAdjudicacao->obTLicitacaoCotacaoLicitacao->obTLicitacaoLicitacao->setDado('exercicio', $_REQUEST["stExercicioLicitacao"]);
    $obTLicitacaoHomologacao->obTLicitacaoAdjudicacao->setDado('cod_entidade', $_REQUEST["inCodEntidade"]);
    $obTLicitacaoHomologacao->obTLicitacaoAdjudicacao->setDado('cod_modalidade', $_REQUEST["inCodModalidade"]);
    $obTLicitacaoHomologacao->obTLicitacaoAdjudicacao->setDado('cod_licitacao', $_REQUEST["inCodLicitacao"]);
    $obTLicitacaoHomologacao->recuperaItensComStatus($rsItensHomologado);

    while (!$rsItensHomologado->eof()) {
        if ($rsItensHomologado->getCampo('homologado') == 't'
                && SistemaLegado::comparaDatas($_REQUEST["stDtAdjudicacao"],date('d/m/Y', strtotime($rsItensHomologado->getCampo('timestamp_homologacao'))))
                && $rsItensHomologado->getCampo('num_adjudicacao_anulada') == '') {
            $stMensagemErro = "A data da Adjudicação deve ser menor ou igual à data da Homologação.";
            break;
        }
        $rsItensHomologado->proximo();
    }
}

if ($stMensagemErro == '') {
    $obTLicitacaoAdjudicacao = new TLicitacaoAdjudicacao;
    $obTLicitacaoAdjudicacao->setComplementoChave( '' );
    $obTLicitacaoAdjudicacao->proximoCod( $inNumAdjudicacao );

    unset( $obTLicitacaoAdjudicacao );

    $boAdjudicado = false;
    $itensAdjudicacao = Sessao::read('itensAdjudicacao');
    foreach ($itensAdjudicacao as $item) {
        $obTLicitacaoAdjudicacao = new TLicitacaoAdjudicacao;

        $obTLicitacaoAdjudicacao->setDado( 'cod_entidade'       , $item['codEntidade'] );
        $obTLicitacaoAdjudicacao->setDado( 'cod_modalidade'     , $item['codModalidade'] );
        $obTLicitacaoAdjudicacao->setDado( 'cod_licitacao'      , $item['codLicitacao'] );
        $obTLicitacaoAdjudicacao->setDado( 'exercicio_licitacao', $item['licitacaoExercicio'] );
        $obTLicitacaoAdjudicacao->setDado( 'cod_item'           , $item['codItem'] );
        $obTLicitacaoAdjudicacao->setDado( 'cgm_fornecedor'     , $item['cgmFornecedor'] );
        $obTLicitacaoAdjudicacao->setDado( 'cod_cotacao'        , $item['codCotacao'] );
        $obTLicitacaoAdjudicacao->setDado( 'lote'               , $item['lote'] );
        $obTLicitacaoAdjudicacao->setDado( 'exercicio_cotacao'  , $item['cotacaoExercicio'] );
        switch ($item['status']) {
            case "Homologado":
            case "Adjudicado":
                $boAdjudicado = true;
                $obTLicitacaoAdjudicacao->setDado( 'adjudicado', true );
            break;

            default:
                $obTLicitacaoAdjudicacao->setDado( 'adjudicado', false );
            break;
        }
        $data = explode('/',$_REQUEST["stDtAdjudicacao"]);

        $timestamp = date("Y-m-d H:i:s", strtotime($data[2]."-".$data[1]."-".$data[0]." ".$_REQUEST["stHoraAdjudicacao"]));

        $obTLicitacaoAdjudicacao->setDado( 'cod_documento'     , 0 );
        $obTLicitacaoAdjudicacao->setDado( 'cod_tipo_documento', 0 );
        $obTLicitacaoAdjudicacao->setDado( 'timestamp', $timestamp );

        if ($item['numAdjudicacao'] == "") {
            $obTLicitacaoAdjudicacao->proximoCod( $inNumAdjudicacao );
            $obTLicitacaoAdjudicacao->setDado( 'num_adjudicacao'    , $inNumAdjudicacao );
            $obTLicitacaoAdjudicacao->inclusao();
        } else {
            $obTLicitacaoAdjudicacao->setDado( 'num_adjudicacao'    , $item['numAdjudicacao'] );
            $obTLicitacaoAdjudicacao->alteracao();
        }

        # Recupera o timestamp que será usado na emissão do documento.
        $obTLicitacaoAdjudicacao->recuperaPorChave($rsAdjudicacao);
        $_REQUEST['timestamp_adjudicacao'] = $rsAdjudicacao->getCampo('timestamp');

        if ( $item['status'] == "Anulado" && (!$item['boAnuladoBanco']) ) {
            $obTLicitacaoAdjudicacaoAnulada = new TLicitacaoAdjudicacaoAnulada;

            $obTLicitacaoAdjudicacaoAnulada->setDado( 'num_adjudicacao'      , $obTLicitacaoAdjudicacao->getDado( 'num_adjudicacao' ) );
            $obTLicitacaoAdjudicacaoAnulada->setDado( 'cod_entidade'         , $item['codEntidade'] );
            $obTLicitacaoAdjudicacaoAnulada->setDado( 'cod_modalidade'       , $item['codModalidade'] );
            $obTLicitacaoAdjudicacaoAnulada->setDado( 'cod_licitacao'        , $item['codLicitacao'] );
            $obTLicitacaoAdjudicacaoAnulada->setDado( 'exercicio_licitacao'  , $item['licitacaoExercicio'] );
            $obTLicitacaoAdjudicacaoAnulada->setDado( 'cod_item'             , $item['codItem'] );
            $obTLicitacaoAdjudicacaoAnulada->setDado( 'cgm_fornecedor'       , $item['cgmFornecedor'] );
            $obTLicitacaoAdjudicacaoAnulada->setDado( 'cod_cotacao'          , $item['codCotacao'] );
            $obTLicitacaoAdjudicacaoAnulada->setDado( 'lote'                 , $item['lote'] );
            $obTLicitacaoAdjudicacaoAnulada->setDado( 'exercicio_cotacao'    , $item['cotacaoExercicio'] );
            $obTLicitacaoAdjudicacaoAnulada->setDado( 'motivo'               , $item['justificativa_anulacao'] );

            $obTLicitacaoAdjudicacaoAnulada->inclusao();
            unset($obTLicitacaoAdjudicacaoAnulada);
        }
        unset( $obTLicitacaoAdjudicacao );
    }
}
SistemaLegado::LiberaFrames(true,false);
$stMensagem = '';
if ($_REQUEST['boGerarTermoAdjudicacao'] && !$boAdjudicado) {
    $stMensagem = " Não foi possível gerar o Termo de Adjudicação pois nenhum item foi adjudicado.";
}
if ($stMensagemErro != '') {
    SistemaLegado::exibeAviso($stMensagemErro , "n_incluir", "erro");
} else {
    SistemaLegado::alertaAviso($pgForm, "Licitação ".$_POST['inCodLicitacao']."/".$_POST['stExercicioLicitacao'].$stMensagem, "incluir", "aviso", Sessao::getId(), "../");
}

if ($_REQUEST['boGerarTermoAdjudicacao'] && $boAdjudicado) {
    $stValor = sistemalegado::pegaConfiguracao( "CGMPrefeito" );
    if (!$stValor) {
        Sessao::write('itensAdjudicacao', '');
        sistemaLegado::exibeAviso( "É preciso preencher o nome do prefeito em Administração :: Configuração, antes de gerar este documento.", "n_incluir", "erro");
    } else {
        Sessao::write('request', $_REQUEST);
        SistemaLegado::mudaFrameOculto($pgGera.'?'.Sessao::getId());
    }
}

Sessao::encerraExcecao();
?>
