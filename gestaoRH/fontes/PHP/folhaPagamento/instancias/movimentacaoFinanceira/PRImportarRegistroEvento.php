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
* Página processamento de Registrar/Importar Evento
* Data de: Criação   : 08/06/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

  $Id: PRImportarRegistroEvento.php 65310 2016-05-11 20:29:34Z evandro $

* Casos de uso: uc-04.05.49
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPeriodoMovimentacao.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoContratoServidorPeriodo.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoRegistroEventoPeriodo.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoRegistroEvento.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoUltimoRegistroEvento.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoRegistroEventoParcela.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEventoCalculado.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEventoCalculadoDependente.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoLogErroCalculo.class.php';
include_once CAM_GRH_PES_MAPEAMENTO.'TPessoalContrato.class.php';

$obTFolhaPagamentoRegistroEvento            = new TFolhaPagamentoRegistroEvento();
$obTFolhaPagamentoUltimoRegistroEvento      = new TFolhaPagamentoUltimoRegistroEvento();
$obTFolhaPagamentoRegistroEventoParcela     = new TFolhaPagamentoRegistroEventoParcela();
$obTFolhaPagamentoRegistroEventoPeriodo     = new TFolhaPagamentoRegistroEventoPeriodo();
$obTFolhaPagamentoEventoCalculado           = new TFolhaPagamentoEventoCalculado();
$obTFolhaPagamentoEventoCalculadoDependente = new TFolhaPagamentoEventoCalculadoDependente();
$obTFolhaPagamentoLogErroCalculo            = new TFolhaPagamentoLogErroCalculo();
$obTFolhaPagamentoContratoServidorPeriodo   = new TFolhaPagamentoContratoServidorPeriodo();
$obTPessoalContrato                         = new TPessoalContrato();
$obTFolhaPagamentoPeriodoMovimentacao       = new TFolhaPagamentoPeriodoMovimentacao();

$obTFolhaPagamentoContratoServidorPeriodo->obTFolhaPagamentoPeriodoMovimentacao   = &$obTFolhaPagamentoPeriodoMovimentacao;
$obTFolhaPagamentoRegistroEventoPeriodo->obTFolhaPagamentoContratoServidorPeriodo = &$obTFolhaPagamentoContratoServidorPeriodo;
$obTFolhaPagamentoRegistroEvento->obTFolhaPagamentoRegistroEventoPeriodo          = &$obTFolhaPagamentoRegistroEventoPeriodo;
$obTFolhaPagamentoUltimoRegistroEvento->obTFolhaPagamentoRegistroEvento           = &$obTFolhaPagamentoRegistroEvento;
$obTFolhaPagamentoRegistroEventoParcela->obTFolhaPagamentoUltimoRegistroEvento    = &$obTFolhaPagamentoUltimoRegistroEvento;
$obTFolhaPagamentoEventoCalculado->obTFolhaPagamentoUltimoRegistroEvento          = &$obTFolhaPagamentoUltimoRegistroEvento;
$obTFolhaPagamentoLogErroCalculo->obTFolhaPagamentoUltimoRegistroEvento           = &$obTFolhaPagamentoUltimoRegistroEvento;
$obTFolhaPagamentoEventoCalculadoDependente->obTFolhaPagamentoEventoCalculado     = &$obTFolhaPagamentoEventoCalculado;

//Define o nome dos arquivos PHP
$stPrograma = "ImportarRegistroEvento";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obErro = new Erro();
$obTransacao = new Transacao();
$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

switch ($request->get("stOpcao")) {
    case "lote_evento":
        if (count(Sessao::read("arLoteEventos")) == 0) {
            $obErro->setDescricao("@Deve haver pelo menos uma Matrícula para um evento na lista de Matrículas.");
        }
    break;
    case "lote_matricula":
        if (count(Sessao::read("arLoteMatriculas")) == 0) {
            $obErro->setDescricao("@Deve haver pelo menos um evento para uma Matrícula na lista de eventos.");
        }
    break;
    case "importar":
        if (count(Sessao::read("EventosCadastrados")) == 0) {
            $obErro->setDescricao("@Deve haver pelo menos um evento para uma Matrícula na lista de eventos.");
        }
    break;
}

if (!$obErro->ocorreu()) {
    $obErro = $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao,"","",$boTransacao);

    if (!$obErro->ocorreu()) {
        if ($request->get("stOpcao") == "lote_evento") {
            $arLotes = Sessao::read("arLoteEventos");
            $stMensagem = "Lote de Eventos incluído com sucesso!";
        }

        if ($request->get("stOpcao") == "lote_matricula") {
            $arLotes = Sessao::read("arLoteMatriculas");
            $stMensagem = "Lote de Matrículas incluído com sucesso!";
        }

        if ($request->get("stOpcao") == "importar") {
            $arLotes = Sessao::read("EventosCadastrados");
            $stMensagem = "Importação de matrículas concluída com sucesso!";
        }

        foreach ($arLotes as $arLote) {
            if ($arLote["stSituacao"] == "Ok" or !isset($arLote["stSituacao"])) {
                $stFiltro = " WHERE registro = ".$arLote["registro"];
                $obErro = $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);

                if (!$obErro->ocorreu()){
                    $boPorporcional = ($arLote["proporcional"] == "Sim") ? true : false;
                    $obTFolhaPagamentoRegistroEvento->setDado("cod_evento",$arLote["cod_evento"]);
                    $obTFolhaPagamentoRegistroEvento->setDado("valor",$arLote["valor"]);
                    $obTFolhaPagamentoRegistroEvento->setDado("quantidade",$arLote["quantidade"]);
                    $obTFolhaPagamentoRegistroEvento->setDado("proporcional",$boPorporcional);
                    $obTFolhaPagamentoPeriodoMovimentacao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                    $obTFolhaPagamentoContratoServidorPeriodo->setDado("cod_contrato",$rsContrato->getCampo("cod_contrato"));
                    //boExcluirDisabled é a variável que controla se o registro de evento já existe
                    //No caso do boExcluirDisabled igual a true, significa que não existe registro de evento para o contrato e evento

                    if ($arLote["boExcluirDisabled"] == "true") {
                        $obErro = $obTFolhaPagamentoContratoServidorPeriodo->recuperaPorChave($rsContratoServidorPeriodo,$boTransacao);

                        if (!$obErro->ocorreu() && $rsContratoServidorPeriodo->getNumLinhas() == -1) {
                            $obErro = $obTFolhaPagamentoContratoServidorPeriodo->inclusao($boTransacao);
                        }

                        if (!$obErro->ocorreu()) {
                            $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");
                            $obErro = $obTFolhaPagamentoRegistroEventoPeriodo->inclusao($boTransacao);

                            if (!$obErro->ocorreu()) {
                                $obErro = $obTFolhaPagamentoRegistroEvento->inclusao($boTransacao);
                            }

                            if (!$obErro->ocorreu()) {
                                $obErro = $obTFolhaPagamentoUltimoRegistroEvento->inclusao($boTransacao);
                            }

                            if ($arLote["parcelas"] > 0) {
                                $obTFolhaPagamentoRegistroEventoParcela->setDado("parcela",$arLote["parcelas"]);
                                $obTFolhaPagamentoRegistroEventoParcela->setDado("mes_carencia",$arLote["mes_carencia"]);
                                $obErro = $obTFolhaPagamentoRegistroEventoParcela->inclusao($boTransacao);
                            }
                        }
                    } else {
                        $stFiltro  = " AND registro_evento_periodo.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
                        $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                        $stFiltro .= " AND registro_evento.cod_evento = ".$arLote["cod_evento"];
                        $stProporcional = ( $arLote["proporcional"] == "Sim" ) ? "TRUE" : "FALSE";
                        $stFiltro .= " AND proporcional IS ".$stProporcional;
                        $obErro = $obTFolhaPagamentoRegistroEvento->recuperaRegistrosDeEventos($rsRegistroEvento,$stFiltro,"",$boTransacao);

                        if (!$obErro->ocorreu()) {
                            if($rsRegistroEvento->getNumLinhas() > 0){
                                $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_evento",$rsRegistroEvento->getCampo("cod_evento"));
                                $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_registro",$rsRegistroEvento->getCampo("cod_registro"));
                                $obTFolhaPagamentoUltimoRegistroEvento->setDado("timestamp",$rsRegistroEvento->getCampo("timestamp"));
                            }

                            if ($arLote["boExcluir"] == "sim" && $rsRegistroEvento->getNumLinhas() > 0) {
                                $obErro = $obTFolhaPagamentoEventoCalculadoDependente->exclusao($boTransacao);

                                if (!$obErro->ocorreu()) {
                                    $obErro = $obTFolhaPagamentoEventoCalculado->exclusao($boTransacao);
                                }

                                if (!$obErro->ocorreu()) {
                                    $obErro = $obTFolhaPagamentoLogErroCalculo->exclusao($boTransacao);
                                }

                                if (!$obErro->ocorreu()) {
                                    $obErro = $obTFolhaPagamentoRegistroEventoParcela->exclusao($boTransacao);
                                }

                                if (!$obErro->ocorreu()) {
                                    $obErro = $obTFolhaPagamentoUltimoRegistroEvento->exclusao($boTransacao);
                                }
                            } else {
                                if($rsRegistroEvento->getNumLinhas() > 0){
                                    $obErro = $obTFolhaPagamentoEventoCalculadoDependente->exclusao($boTransacao);

                                    if (!$obErro->ocorreu()) {
                                        $obErro = $obTFolhaPagamentoEventoCalculado->exclusao($boTransacao);
                                    }

                                    if (!$obErro->ocorreu()) {
                                        $obErro = $obTFolhaPagamentoLogErroCalculo->exclusao($boTransacao);
                                    }

                                    if (!$obErro->ocorreu()) {
                                        $obErro = $obTFolhaPagamentoRegistroEventoParcela->exclusao($boTransacao);
                                    }

                                    if (!$obErro->ocorreu()) {
                                        $obErro = $obTFolhaPagamentoUltimoRegistroEvento->exclusao($boTransacao);
                                    }
                                }

                                if (!$obErro->ocorreu()) {
                                    $obTFolhaPagamentoRegistroEventoPeriodo->setDado("cod_registro","");
                                    $obErro = $obTFolhaPagamentoRegistroEventoPeriodo->inclusao($boTransacao);
                                }

                                if (!$obErro->ocorreu()) {
                                    $obErro = $obTFolhaPagamentoRegistroEvento->inclusao($boTransacao);
                                }

                                if (!$obErro->ocorreu()) {
                                    $obErro = $obTFolhaPagamentoUltimoRegistroEvento->inclusao($boTransacao);
                                }

                                if (!$obErro->ocorreu() && $arLote["parcelas"] > 0) {
                                    $obTFolhaPagamentoRegistroEventoParcela->setDado("parcela",$arLote["parcelas"]);
                                    $obTFolhaPagamentoRegistroEventoParcela->setDado("mes_carencia",$arLote["mes_carencia"]);
                                    $obErro = $obTFolhaPagamentoRegistroEventoParcela->inclusao($boTransacao);
                                }
                            }
                        }
                    }
                }

                if ($obErro->ocorreu()) {
                    break;
                }
            }
        }
    }
}

if ($obErro->ocorreu())
    SistemaLegado::exibeAviso(urlencode("Erro ao Registrar/Importar Lote Eventos/Matrículas (".$obErro->getDescricao().")"),"","erro");
else{
    Sessao::write("arLoteEventos", array());
    Sessao::write("arLoteMatriculas", array());
    sistemaLegado::alertaAviso($pgForm,$stMensagem,"importar","aviso", Sessao::getId(), "../");
}

$obErro = $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTFolhaPagamentoRegistroEvento );

?>
