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
* Página de Processamento do Periodo de Movimentação
* Data de Criação: 26/10/2005

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2008-02-13 13:27:10 -0200 (Qua, 13 Fev 2008) $

* Casos de uso: uc-04.05.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalculoFolhaPagamento.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalcularFolhas.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPensaoEvento.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFeriasEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDecimoEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaPrevidencia.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSalarioFamilia.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrf.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgts.class.php";

$stAcao = $request->get("stAcao");

//Define o nome dos arquivos PHP
$stPrograma = "ManterPeriodoMovimentacao";
$pgForm     = "FM".$stPrograma.".php?stAcao=mensagem$stAcao";
$pgProc     = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgList     = "LS".$stPrograma.".php?stAcao=$stAcao";
$pgOcul     = "OC".$stPrograma.".php";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoCalculoFolhaPagamento = new RFolhaPagamentoCalculoFolhaPagamento;
$obErro = new Erro();

switch ($stAcao) {
    case "incluir":
        
        if ($request->get('hdnNovaDataInicial') != '') {
            $dataInicial = $request->get('hdnNovaDataInicial');
        } else {
            $dataInicial = $request->get('stNovaDataInicial');
        }
        
        if ( SistemaLegado::comparaDatas($dataInicial,$request->get('stNovaDataFinal')) ) {
            $obErro->setDescricao("A data final deve ser posterior a data inicial.");
        }

        $arDataInicial = explode("/",$dataInicial);
        $dtUltimoDia = SistemaLegado::retornaUltimoDiaMes($arDataInicial[1],$arDataInicial[2]);

        if (!(SistemaLegado::comparaDatas($dtUltimoDia, $request->get('stNovaDataFinal'), true))) {
            $obErro->setDescricao("A data final não pode ser maior do que ".$dtUltimoDia."!");
        }

        if ( !$obErro->ocorreu() ) {
            $obRFolhaPagamentoCalculoFolhaPagamento->listarLogErroCalculo($rsLogErro);

            if ( $rsLogErro->getNumLinhas() > 0 ) {
                $obErro->setDescricao('O contrato '.$rsLogErro->getCampo('registro').' possui um erro de cálculo, é necessário corrigir o cálculo para abrir um novo período.');
            }
        }

        if ( !$obErro->ocorreu() ) {
            $obRFolhaPagamentoPeriodoMovimentacao->setDtInicial($dataInicial);
            $obRFolhaPagamentoPeriodoMovimentacao->setDtFinal($request->get('stNovaDataFinal'));
            $obErro = $obRFolhaPagamentoPeriodoMovimentacao->abrirPeriodoMovimentacao($boTransacao);

            //Seta filtro geral para calculo completo da folha de pagamento
            $obRFolhaPagamentoCalcularFolhas = new RFolhaPagamentoCalcularFolhas();
            $obRFolhaPagamentoCalcularFolhas->setTipoFiltro('geral');
            
            //Executa abertura da folha
            //Procedimento identico ao utilizado no Calcular Salário
            $obRFolhaPagamentoCalcularFolhas->procedimentoCalculo($boTransacao);

            if ( !$obErro->ocorreu() ) {
                SistemaLegado::LiberaFrames(true, true);
                SistemaLegado::alertaAviso($pgForm,"Data Inicial: ".$dataInicial." e Data Final: ".$request->get('stNovaDataFinal'),"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::LiberaFrames(true, true);
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } else {
            SistemaLegado::LiberaFrames(true, true);
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "excluir":

        $obErro = $obRFolhaPagamentoPeriodoMovimentacao->cancelarPeriodoMovimentacao($boTransacao);

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::LiberaFrames(true, true);
            sistemaLegado::alertaAviso($pgForm,"Periodo Movimentação: ".$request->get("inCodPeriodoMovimentacao"),"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::LiberaFrames(true, true);
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;
}

?>
