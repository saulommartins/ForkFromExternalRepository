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
    * Processamento
    * Data de Criação: 16/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: PRManterRegistroEvento.php 65906 2016-06-28 20:42:02Z evandro $

    * Casos de uso: uc-04.05.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculadoDependente.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoContratoServidorPeriodo.class.php";

$link = Sessao::read("link");
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=alterar";
//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEvento";
$pgFilt = "FL".$stPrograma.".php?stAcao=alterar";
$pgList = "LS".$stPrograma.".php?".$stLink;
$pgForm = "FM".$stPrograma.".php?";
$pgProc = "PR".$stPrograma.".php?";
$pgOcul = "OC".$stPrograma.".php?";
$pgJS   = "JS".$stPrograma.".js";
$stAcao = $request->get("stAcao");

$obRFolhaPagamentoPeriodoContratoServidor = new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao );
switch ($stAcao) {
    case "incluir":
        Sessao::setTrataExcecao(true);
        $obTPessoalContrato = new TPessoalContrato;
        $stFiltro = " WHERE registro = ".Sessao::read('inContrato');
        $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);

        Sessao::getTransacao()->setMapeamento( $obTPessoalContrato );
        $obTPessoalContrato->setDado('cod_contrato', $rsContrato->getCampo("cod_contrato"));

        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsUltimaMovimentacao);

        $obTFolhaPagamentoContratoServidorPeriodo = new TFolhaPagamentoContratoServidorPeriodo;
        $obTFolhaPagamentoRegistroEventoPeriodo   = new TFolhaPagamentoRegistroEventoPeriodo;
        $obTFolhaPagamentoRegistroEventoPeriodo->obTFolhaPagamentoContratoServidorPeriodo = &$obTFolhaPagamentoContratoServidorPeriodo;
        $obTFolhaPagamentoRegistroEvento        = new TFolhaPagamentoRegistroEvento;
        $obTFolhaPagamentoRegistroEvento->obTFolhaPagamentoRegistroEventoPeriodo = &$obTFolhaPagamentoRegistroEventoPeriodo;
        $obTFolhaPagamentoUltimoRegistroEvento  = new TFolhaPagamentoUltimoRegistroEvento;
        $obTFolhaPagamentoUltimoRegistroEvento->obTFolhaPagamentoRegistroEvento = &$obTFolhaPagamentoRegistroEvento;
        $obTFolhaPagamentoRegistroEventoParcela = new TFolhaPagamentoRegistroEventoParcela;
        $obTFolhaPagamentoRegistroEventoParcela->obTFolhaPagamentoUltimoRegistroEvento = &$obTFolhaPagamentoUltimoRegistroEvento;
        $obTFolhaPagamentoLogErroCalculo = new TFolhaPagamentoLogErroCalculo;
        $obTFolhaPagamentoLogErroCalculo->obTFolhaPagamentoUltimoRegistroEvento = &$obTFolhaPagamentoUltimoRegistroEvento;
        $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado;
        $obTFolhaPagamentoEventoCalculado->obTFolhaPagamentoUltimoRegistroEvento = &$obTFolhaPagamentoUltimoRegistroEvento;
        $obTFolhaPagamentoEventoCalculadoDependente = new TFolhaPagamentoEventoCalculadoDependente;
        $obTFolhaPagamentoEventoCalculadoDependente->obTFolhaPagamentoEventoCalculado = &$obTFolhaPagamentoEventoCalculado;

        $stFiltro  = "   AND cod_contrato = ".$rsContrato->getCampo("cod_contrato");
        $stFiltro .= "   AND cod_periodo_movimentacao = ".$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao");
        $obTFolhaPagamentoUltimoRegistroEvento->recuperaRelacionamento($rsRegistroEventoPeriodo,$stFiltro);
        while (!$rsRegistroEventoPeriodo->eof()) {
            $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_registro",$rsRegistroEventoPeriodo->getCampo("cod_registro"));
            $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_evento",$rsRegistroEventoPeriodo->getCampo("cod_evento"));
            $obTFolhaPagamentoUltimoRegistroEvento->setDado("desdobramento",$rsRegistroEventoPeriodo->getCampo("desdobramento"));
            $obTFolhaPagamentoUltimoRegistroEvento->setDado("timestamp",$rsRegistroEventoPeriodo->getCampo("timestamp"));
            $obTFolhaPagamentoUltimoRegistroEvento->deletarUltimoRegistroEvento();
            $rsRegistroEventoPeriodo->proximo();
        }
        $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");

        $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_contrato",$rsContrato->getCampo("cod_contrato"));
        $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obTFolhaPagamentoContratoServidorPeriodo->recuperaPorChave($rsContratoServidorPeriodo);
        if ( $rsContratoServidorPeriodo->getNumLinhas() < 0 ) {
            $obTFolhaPagamentoContratoServidorPeriodo->inclusao();
        }

        //Inclusão de eventos fixos
        $arEventosFixos = Sessao::read("eventosFixos");
        if (is_array($arEventosFixos)) {
            foreach ($arEventosFixos as $arEvento) {
                $stFiltro = " WHERE codigo = '".$arEvento["inCodigo"]."'";
                $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
                $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

                $nuValor        = ( $arEvento['nuValor']        != "" ) ? $arEvento['nuValor']      : 0;
                $nuQuantidade   = ( $arEvento['nuQuantidade']   != "" ) ? $arEvento['nuQuantidade'] : 0;

                $obTFolhaPagamentoRegistroEvento->setDado("cod_evento"  ,$rsEvento->getCampo("cod_evento"));
                $obTFolhaPagamentoRegistroEvento->setDado("valor"       ,$nuValor);
                $obTFolhaPagamentoRegistroEvento->setDado("quantidade"  ,$nuQuantidade);
                $obTFolhaPagamentoRegistroEvento->setDado("proporcional",false);
                $obTFolhaPagamentoRegistroEventoPeriodo->inclusao();
                $obTFolhaPagamentoRegistroEvento->inclusao();
                $obTFolhaPagamentoUltimoRegistroEvento->inclusao();
                $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");
            }
        }

        //Inclusão de eventos variáveis
        $arEventosVariaveis = Sessao::read("eventosVariaveis");
        if (is_array($arEventosVariaveis)) {
            foreach ($arEventosVariaveis as $arEvento) {
                $stFiltro = " WHERE codigo = '".$arEvento["inCodigo"]."'";
                $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
                $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

                $nuValor        = ( $arEvento['nuValor']        != "" ) ? $arEvento['nuValor']      : 0;
                $nuQuantidade   = ( $arEvento['nuQuantidade']   != "" ) ? $arEvento['nuQuantidade'] : 0;

                $obTFolhaPagamentoRegistroEvento->setDado("cod_evento"  ,$rsEvento->getCampo("cod_evento"));
                $obTFolhaPagamentoRegistroEvento->setDado("valor"       ,$nuValor);
                $obTFolhaPagamentoRegistroEvento->setDado("quantidade"  ,$nuQuantidade);
                $obTFolhaPagamentoRegistroEvento->setDado("proporcional",false);
                $obTFolhaPagamentoRegistroEventoPeriodo->inclusao();
                $obTFolhaPagamentoRegistroEvento->inclusao();
                $obTFolhaPagamentoUltimoRegistroEvento->inclusao();
                if ($arEvento['inQuantidadeParc'] != "") {
                    $inMesCarencia  = ( $arEvento['inMesCarencia']   != "" ) ? $arEvento['inMesCarencia'] : 0;

                    $obTFolhaPagamentoRegistroEventoParcela->setDado("parcela"      , $arEvento['inQuantidadeParc']);
                    $obTFolhaPagamentoRegistroEventoParcela->setDado("mes_carencia" , $inMesCarencia);
                    $obTFolhaPagamentoRegistroEventoParcela->inclusao();
                }
                $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");
            }
        }

        //Inclusão de eventos proporcionais
        $arEventosProporcionais = Sessao::read("eventosProporcionais");
        if (is_array($arEventosProporcionais)) {
            foreach ($arEventosProporcionais as $arEvento) {
                $stFiltro = " WHERE codigo = '".$arEvento["inCodigo"]."'";
                $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
                $obTFolhaPagamentoEvento->recuperaTodos($rsEvento,$stFiltro);

                $nuValor        = ( $arEvento['nuValor']        != "" ) ? $arEvento['nuValor']      : 0;
                $nuQuantidade   = ( $arEvento['nuQuantidade']   != "" ) ? $arEvento['nuQuantidade'] : 0;

                $obTFolhaPagamentoRegistroEvento->setDado("cod_evento"  ,$rsEvento->getCampo("cod_evento"));
                $obTFolhaPagamentoRegistroEvento->setDado("valor"       ,$nuValor);
                $obTFolhaPagamentoRegistroEvento->setDado("quantidade"  ,$nuQuantidade);
                $obTFolhaPagamentoRegistroEvento->setDado("proporcional",true);
                $obTFolhaPagamentoRegistroEventoPeriodo->inclusao();
                $obTFolhaPagamentoRegistroEvento->inclusao();
                $obTFolhaPagamentoUltimoRegistroEvento->inclusao();

                if ($arEvento['inQuantidadeParc'] != "") {
                    $inMesCarencia  = ( $arEvento['inMesCarencia']   != "" ) ? $arEvento['inMesCarencia'] : 0;

                    $obTFolhaPagamentoRegistroEventoParcela->setDado("parcela"  ,$arEvento['inQuantidadeParc']);
                    $obTFolhaPagamentoRegistroEventoParcela->setDado("mes_carencia" , $inMesCarencia);
                    $obTFolhaPagamentoRegistroEventoParcela->inclusao();
                }
                $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");
            }
        }

        //Para funcionamento correto dessa PL, foi inserido no registro de evento uma verificação
        //que identifica se o contrato possui registros de eventos, caso não possua, é excluído
        //o dado da tabela folhapagamento.deducao_dependente que identifica a utilização de valor
        //de dedução de dependente.
        if (count($arEventosFixos) == 0 AND count($arEventosVariaveis) == 0 AND count($arEventosProporcionais) == 0) {
            $stFiltro = " AND contrato.registro = ".Sessao::read('inContrato');
            $obTPessoalContrato->recuperaCgmDoRegistro($rsCGM,$stFiltro);

            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoDeducaoDependente.class.php");
            $obTFolhaPagamentoDeducaoDependente = new TFolhaPagamentoDeducaoDependente();
            $obTFolhaPagamentoDeducaoDependente->setDado("numcgm",$rsCGM->getCampo("numcgm"));
            $obTFolhaPagamentoDeducaoDependente->setDado("cod_periodo_movimentacao",$rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTFolhaPagamentoDeducaoDependente->setDado("cod_tipo",2);
            $obTFolhaPagamentoDeducaoDependente->exclusao();
        }

        Sessao::encerraExcecao();        
        $stMensagem = "Necessário recalcular a folha. Matrícula: ".Sessao::read('inContrato');
        if ($request->get('stOkRetorno') == "lista") {
            sistemaLegado::alertaAviso($pgList,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgFilt,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        }
    break;
}
?>
