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
    * Classe de regra de relatório para Relatório da Folha Analítica/Sintética
    * Data de Criação: 23/03/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra de Relatório

    $Revision: 30896 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.50
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                                );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalculoFolhaComplementar.class.php"                  );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalculoFolhaPagamento.class.php"                     );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaComplementar.class.php"                         );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php"                                     );
//include_once ( CAM_GRH_PES_NEGOCIO."RPessoalContratoServidor.class.php"                                 );
//include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                         );

class RRelatorioFolhaAnaliticaSintetica extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRFolhaPagamentoPeriodoMovimentacao;

/**
    * @access Public
    * @param Object $valor
*/
function setRFolhaPagamentoPeriodoMovimentacao($valor) { $this->obRFolhaPagamentoPeriodoMovimentacao = $valor; }

/**
    * @access Public
    * @return Object
*/
function getRFolhaPagamentoPeriodoMovimentacao() { return $this->obRFolhaPagamentoPeriodoMovimentacao; }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioFolhaAnaliticaSintetica()
{
    $this->setRFolhaPagamentoPeriodoMovimentacao( new RFolhaPagamentoPeriodoMovimentacao );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordset)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    $stCompetencia = $arFiltro['inAno']."-".str_pad($arFiltro['inCodMes'], 2, "0", STR_PAD_LEFT);
    $this->obRFolhaPagamentoPeriodoMovimentacao->setDtFinal( $stCompetencia );
    $this->obRFolhaPagamentoPeriodoMovimentacao->listarPeriodoMovimentacao($rsMovimentacao);
    if ($arFiltro['boFiltrarFolhaComplementar']) {
        $stFolha = "Complementar";
    } else {
        switch ($arFiltro['inCodConfiguracao']) {
            case 1:
                $stFolha = "Salário";
                break;
            case 2:
                $stFolha = "Férias";
                break;
            case 3:
                $stFolha = "13º Salário";
                break;
        }
    }
    $stCompetencia = str_pad($arFiltro['inCodMes'], 2, "0", STR_PAD_LEFT)."/".$arFiltro['inAno'];

    $arContratos = array();
    $arRelatorio = array();
    $arTemp = array();
    $nuTotalPrevidencia = 0;
    $nuTotalDescontos   = 0;
    $nuTotalProventos   = 0;
    $nuTotalIRRF        = 0;
    $inTotalContratos   = 0;
    $stTipoFiltro = ( isset($arFiltro['stTipoFiltro']) ) ? $arFiltro['stTipoFiltro'] : $arFiltro['hdnTipoFiltro'];
    switch ($stTipoFiltro) {
        case 'contrato':
        case 'cgm_contrato':
            foreach (Sessao::read('arContratos') as $arContrato) {
                $arFiltros['arRegistros'][] = $arContrato['contrato'];
            }
        break;
        case 'geral':
            $arFiltros['arCargos']             = $arFiltro['inCodCargoSelecionados'];
            $arFiltros['arEspecialidades']     = $arFiltro['inCodEspecialidadeSelecionados'];
            $arFiltros['arFuncoes']            = $arFiltro['inCodFuncaoSelecionados'];
            $arFiltros['arEspecialidadesFunc'] = $arFiltro['inCodEspecialidadeSelecionadosFunc'];
            $arFiltros['arPadrao']             = $arFiltro['inCodPadraoSelecionados'];
            $arFiltros['arLotacao']            = $arFiltro['inCodLotacaoSelecionados'];
            $arFiltros['arLocal']              = $arFiltro['inCodLocalSelecionados'];
            $arFiltros['boAtivo']              = $arFiltro['boAtivo'];
            $arFiltros['boInativo']            = $arFiltro['boInativo'];
            $arFiltros['boPensionista']        = $arFiltro['boPensionista'];
            if ($arFiltro['stAlfNumLotacao'] == 'numerica') {
                $stOrdem = " cod_orgao,";
            } elseif ($arFiltro['stAlfNumLotacao'] == 'alfabetica') {
                $stOrdem = " descricao_lotacao,";
            }
            if ($arFiltro['stAlfNumLocal'] == 'numerica') {
                $stOrdem = " cod_local,";
            } elseif ($arFiltro['stAlfNumLocal'] == 'alfabetica') {
                $stOrdem = " descricao_local,";
            }
            if ($arFiltro['stAlfNumCgm'] == 'numerica') {
                $stOrdem = " numcgm,";
            } elseif ($arFiltro['stAlfNumCgm'] == 'alfabetica') {
                $stOrdem = " nom_cgm,";
            }
            $stOrdem = "nom_cgm,contrato.cod_contrato,";
        break;
    }
    if ($arFiltro['stOrdenacao'] == 'alfabetica') {
        $stOrdem = "nom_cgm,contrato.cod_contrato,";
    } elseif ($arFiltro['stOrdenacao'] == 'numérica') {
        $stOrdem = "registro,contrato.cod_contrato,";
    }
    if ($arFiltro['stOrdenacaoEventos'] == 'codigo') {
        $stOrdem .= "codigo,";
    } elseif ($arFiltro['stOrdenacaoEventos'] == 'sequencia') {
        $stOrdem .= "sequencia,";
    }
    if ( $rsMovimentacao->getNumLinhas() > 0 ) {
        $this->obRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoPeriodoContratoServidor();
        $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->addRFolhaPagamentoRegistroEvento();
        $arFiltros['cod_periodo_movimentacao'] = $rsMovimentacao->getCampo('cod_periodo_movimentacao');
        $this->obRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoCalculoFolhaPagamento();
        if ($arFiltro['boFiltrarFolhaComplementar']) {
            $arFiltros['cod_complementar'] =  $arFiltro['inCodComplementar'];
            $this->obRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoFolhaComplementar();
            $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->obRFolhaPagamentoCalculoFolhaComplementar->listarRelatorioFolhaAnaliticaSintetica($rsEventosCalculados,$arFiltros,$stOrdem);
        } else {
            switch ($arFiltro['inCodConfiguracao']) {
                case 1:
                    $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoCalculoFolhaPagamento->listarRelatorioFolhaAnaliticaSintetica($rsEventosCalculados,$arFiltros,$stOrdem);
                    break;
                case 2:
                    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php");
                    $obTFolhaPagamentoEventoFeriasCalculado = new TFolhaPagamentoEventoFeriasCalculado();
                    $stFiltro = $this->processaFiltro($arFiltros);
                    $stOrdem .= " contrato.cod_contrato";
                    $obTFolhaPagamentoEventoFeriasCalculado->recuperaRelatorioFolhaAnaliticaSintetica($rsEventosCalculados,$stFiltro,$stOrdem);
                    break;
                case 3:
                    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php");
                    $obTFolhaPagamentoEventoDecimoCalculado = new TFolhaPagamentoEventoDecimoCalculado();
                    $stFiltro = $this->processaFiltro($arFiltros);
                    $stOrdem .= " contrato.cod_contrato";
                    $obTFolhaPagamentoEventoDecimoCalculado->recuperaRelatorioFolhaAnaliticaSintetica($rsEventosCalculados,$stFiltro,$stOrdem);
                    break;
            }

        }
        switch ($arFiltro['stFolha']) {
            case 'sintética':
                $nuProventos   = 0;
                $nuDescontos   = 0;
                $nuPrevidencia = 0;
                $nuIRRF        = 0;
                $nuTotal       = 0;
                $arEventosPrevidencia = array();
                $inCodContrato = "";
                $arTemp['campo1'] = "Tipo da Folha:";
                $arTemp['campo2'] = $stFolha;
                $arTemp['campo3'] = "Competência:";
                $arTemp['campo4'] = $stCompetencia;
                $arTemp['campo5'] = "Período Movimentação:";
                $arTemp['campo6'] = $rsMovimentacao->getCampo('dt_inicial') ." até ". $rsMovimentacao->getCampo('dt_final');
                $arRelatorio['linha1'][]    = $arTemp;
                while ( !$rsEventosCalculados->eof() ) {
                    $rsEventosCalculados->proximo();
                    $inProxCodContrato = $rsEventosCalculados->getCampo("cod_contrato");
                    $rsEventosCalculados->anterior();
                    if ( $rsEventosCalculados->getCampo('cod_contrato') != $inAntCodContrato ) {
                        $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->setCodContrato( $rsEventosCalculados->getCampo('cod_contrato') );
                        $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->listarContratoServidorLotacao($rsLotacao);
                        $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->listarEventosDePrevidenciaPorContrato($rsEventosPrevidencia,$rsEventosCalculados->getCampo('cod_contrato'));
                        $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->listarEventosDeIRRFPorContrato($rsEventosIrrf,$rsEventosCalculados->getCampo('cod_contrato'));
                        $arEventosPrevidencia = array();
                        while ( !$rsEventosPrevidencia->eof() ) {
                            $arEventosPrevidencia[] = $rsEventosPrevidencia->getCampo('cod_evento');
                            $rsEventosPrevidencia->proximo();
                        }
                        $arEventosIrrf = array();
                        while ( !$rsEventosIrrf->eof() ) {
                            $arEventosIrrf[] = $rsEventosIrrf->getCampo('cod_evento');
                            $rsEventosIrrf->proximo();
                        }
                    }

                    if ( $rsEventosCalculados->getCampo('natureza') == 'P' ) {
                        $nuProventos += $rsEventosCalculados->getCampo('valor');
                    }
                    if ( $rsEventosCalculados->getCampo('natureza') == 'D' ) {
                        $nuDescontos += $rsEventosCalculados->getCampo('valor');
                    }
                    if ($arFiltro['inCodConfiguracao'] == 2 or $arFiltro['inCodConfiguracao'] == 3) {
                        $arCodEvento = explode("_",$rsEventosCalculados->getCampo('cod_evento'));
                        $inCodEvento = $arCodEvento[0];
                    } else {
                        $inCodEvento = $rsEventosCalculados->getCampo('cod_evento');
                    }
                    if ( in_array($inCodEvento,$arEventosPrevidencia) ) {
                        $nuPrevidencia += $rsEventosCalculados->getCampo('valor');
                    }
                    if ( in_array($inCodEvento,$arEventosIrrf) ) {
                        $nuIRRF += $rsEventosCalculados->getCampo('valor');
                    }
                    if ( $rsEventosCalculados->getCampo('cod_contrato') != $inProxCodContrato ) {
                        $arTemp['contrato']     = $rsEventosCalculados->getCampo('registro');
                        $arTemp['cgm']          = $rsEventosCalculados->getCampo('numcgm')."-".$rsEventosCalculados->getCampo('nom_cgm');
                        $arTemp['lotacao']      = $rsLotacao->getCampo('cod_orgao')."-".$rsLotacao->getCampo('descricao');
                        $arTemp['proventos']    = number_format ($nuProventos, 2, ",", ".");
                        $arTemp['descontos']    = number_format ($nuDescontos, 2, ",", ".");
                        $arTemp['previdencia']  = number_format ($nuPrevidencia, 2, ",", ".");
                        $arTemp['irrf']         = number_format ($nuIRRF, 2, ",", ".");
                        $arTemp['total']        = number_format ($nuProventos - $nuDescontos, 2, ",", ".");
                        $arContratos[]          = $arTemp;
                        $nuTotalPrevidencia     += $nuPrevidencia;
                        $nuTotalDescontos       += $nuDescontos;
                        $nuTotalIRRF            += $nuIRRF;
                        $nuTotalProventos       += $nuProventos;
                        $nuSalarioLiquido       += $nuProventos - $nuDescontos;

                        $inTotalContratos++;
                        $nuProventos   = 0;
                        $nuDescontos   = 0;
                        $nuPrevidencia = 0;
                        $nuIRRF        = 0;
                        $nuTotal       = 0;
                        $inCodContrato = "";
                    }
                    $inAntCodContrato = $rsEventosCalculados->getCampo('cod_contrato');
                    $rsEventosCalculados->proximo();
                }
                $arPadrao["contrato"] = "";
                $arPadrao["cgm"]      = "";
                $arPadrao["lotacao"]  = "Total Geral";
                $arPadrao["proventos"]= number_format ( $nuTotalProventos , 2, ",", ".");
                $arPadrao["descontos"]= number_format ( $nuTotalDescontos , 2, ",", ".");
                $arPadrao["previdencia"]= number_format ( $nuTotalPrevidencia , 2, ",", ".");
                $arPadrao["irrf"]     = number_format ( $nuTotalIRRF , 2, ",", ".");
                $arPadrao["total"]    = number_format ( $nuTotalProventos - $nuTotalDescontos , 2, ",", ".");
                $arContratos[]             = $arPadrao;

                $arTemp = array();
                $nuTotalProventosA = 0;
                $nuTotalDescontosA = 0;
                $nuTotalPrevidenciaA = 0;
                $nuTotalIRRFA      = 0;
                $nuTotalA          = 0;
                if ($arFiltro['boLotacao']) {
                    Sessao::write('boAgrupado',true);
                    //Agrupamento por lotação
                    $arContratosLotacao = array();
                    $arTemp             = array();
                    foreach ($arContratos as $arContrato) {
                        if ( strpos($arContrato['lotacao'],"Total") === 0 ) {
                            if ( count($arTemp) ) {
                                $arContratosLotacao = array_merge($arContratosLotacao,$this->agruparPor("lotacao","Lotação",$arTemp));
                            }
                            $arContratosLotacao = array_merge($arContratosLotacao,array($arContrato));
                            $arTemp = array();
                        } else {
                            $arTemp[] = $arContrato;
                        }
                    }
                    $arContratos = $arContratosLotacao;
                }
                if ($arFiltro['boCgm']) {
                    Sessao::write('boAgrupado',true);
                    //Agrupamento por cgm
                    $arContratosCGM = array();
                    $arTemp         = array();
                    $arContratosLotacao = $arContratos;
                    foreach ($arContratosLotacao as $arContratoLotacao) {
                        if ( strpos($arContratoLotacao['lotacao'],"Total") === 0 or $arContratoLotacao['lotacao'] == "" ) {
                            if ( count($arTemp) ) {
                                $arContratosCGM = array_merge($arContratosCGM,$this->agruparPor("cgm","CGM",$arTemp));
                            }
                            $arContratosCGM = array_merge($arContratosCGM,array($arContratoLotacao));
                            $arTemp = array();
                        } else {
                            $arTemp[] = $arContratoLotacao;
                        }
                    }
                    $arContratos = $arContratosCGM;
                }
                if ($arFiltro['boEmitirRelatorio']) {
                    $arContratos = $this->emitirApenasTotais($arContratos);
                }
                $arRelatorio['contratos'] = $arContratos;
                $arTotais = array();
                $arTemp   = array();
                $arTemp['campo1'] = 'Soma Valor Recolhido Previdência:';
                $arTemp['campo2'] = number_format($nuTotalPrevidencia,2,',','.');
                $arTemp['campo3'] = '';
                $arTemp['campo4'] = '';
                $arTotais[]       = $arTemp;

                $arTemp['campo1'] = 'Soma Valor Recolhido IRRF.......:';
                $arTemp['campo2'] = number_format($nuTotalIRRF,2,',','.');
                $arTemp['campo3'] = '';
                $arTemp['campo4'] = '';
                $arTotais[]       = $arTemp;

                $arTemp['campo1'] = '';
                $arTemp['campo2'] = '';
                $arTemp['campo3'] = '';
                $arTemp['campo4'] = '';
                $arTotais[]       = $arTemp;

                $arTemp['campo1'] = 'Soma dos Proventos..............:';
                $arTemp['campo2'] = number_format($nuTotalProventos,2,',','.');
                $arTemp['campo3'] = 'Soma dos Descontos..............:';
                $arTemp['campo4'] = number_format($nuTotalDescontos,2,',','.');
                $arTotais[]       = $arTemp;

                $arTemp['campo1'] = 'Salário Líquido.................:';
                $arTemp['campo2'] = number_format($nuSalarioLiquido,2,',','.');
                $arTemp['campo3'] = 'No. Servidores..................:';
                $arTemp['campo4'] = $inTotalContratos;
                $arTotais[]       = $arTemp;
                $arRelatorio['totais'] = $arTotais;
            break;
            case 'analítica_resumida':
                $arCodContratos = array();
                $arProventosDescontosTotal = array();
                $arProventosTotais         = array();
                $arDescontosTotais         = array();
                while ( !$rsEventosCalculados->eof()  ) {
                    $arCodContratos[] = $rsEventosCalculados->getCampo("cod_contrato");
                    $rsEventosCalculados->proximo();
                }
                $arCodContratos = array_unique($arCodContratos);
                $arTemp           = array();
                $arLinha1         = array();
                $arTemp['campo1'] = "Tipo da Folha:";
                $arTemp['campo2'] = $stFolha;
                $arTemp['campo3'] = "Competência:";
                $arTemp['campo4'] = $stCompetencia;
                $arTemp['campo5'] = "Período Movimentação:";
                $arTemp['campo6'] = $rsMovimentacao->getCampo('dt_inicial') ." até ". $rsMovimentacao->getCampo('dt_final');
                $arLinha1[]       = $arTemp;
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSalario.class.php");
                $obTPessoalContratoServidorSalario = new TPessoalContratoServidorSalario;
                $arVigencia = explode("/",$rsMovimentacao->getCampo("dt_final"));
                $dtVigencia = $arVigencia[2]."-".$arVigencia[1]."-".$arVigencia[0];
                $inTotalServidores = count($arCodContratos);
                foreach ($arCodContratos as $inCodContrato) {
                    $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoCalculoFolhaPagamento->listarRelatorioFolhaAnalitica($rsDadosContrato,$inCodContrato);
                    $stFiltro  = " AND salario.cod_contrato = ".$inCodContrato;
                    $stFiltro .= " AND salario.vigencia <= '".$dtVigencia."'";
                    $obTPessoalContratoServidorSalario->recuperaRelacionamento($rsSalario,$stFiltro);
                    $rsSalario->addFormatacao("salario","NUMERIC_BR");
                    $arTemp           = array();
                    $arContrato1      = array();
                    $arTemp["campo1"] = $rsDadosContrato->getCampo('registro');
                    $arTemp["campo2"] = $rsDadosContrato->getCampo('numcgm')."-".$rsDadosContrato->getCampo('nom_cgm');
                    $arTemp["campo3"] = $rsDadosContrato->getCampo('descricao_regime_funcao');
                    $arTemp["campo4"] = $rsDadosContrato->getCampo('descricao_funcao');
                    $arTemp["campo5"] = $rsDadosContrato->getCampo('descricao_padrao')."-".$rsSalario->getCampo("salario");
                    $arContrato1[]    = $arTemp;

                    if ( $rsDadosContrato->getCampo('cod_local') != "" ) {
                        $stLocal = $rsDadosContrato->getCampo("cod_local")."-".trim($rsDadosContrato->getCampo('descricao_local'));
                    } else {
                        $stLocal = "";
                    }
                    $obRConfiguracaoPessoal = new RConfiguracaoPessoal;
                    $obRConfiguracaoPessoal->Consultar();
                    $arContrato2      = array();
                    $arTemp["campo1"] = $rsDadosContrato->getCampo("cod_orgao")."-".trim($rsDadosContrato->getCampo('descricao_lotacao'));
                    $arTemp["campo2"] = $stLocal;
                    $arTemp["campo3"] = "";
                    switch ($obRConfiguracaoPessoal->getContagemInicial()) {
                        case "dtPosse":
                            $arTemp["campo4"] = $rsDadosContrato->getCampo('dt_posse');
                            break;
                        case "dtNomeacao":
                            $arTemp["campo4"] = $rsDadosContrato->getCampo('dt_nomeacao');
                            break;
                        case "dtAdmissao":
                            $arTemp["campo4"] = $rsDadosContrato->getCampo('dt_admissao');
                            break;
                    }
                    $arTemp["campo5"] = $rsDadosContrato->getCampo('horas_mensais');
                    $arContrato2[]     = $arTemp;

                    $arTemp            = array();
                    $arLinha2          = array();
                    $arTemp["campo1"]  = str_pad("_",176,"_");
                    $arLinha2[]        = $arTemp;

                    $arTemp               = array();
                    $arProventosDescontos = array();
                    $arBasesInformativos  = array();
                    $arProventos          = array();
                    $arDescontos          = array();
                    $arBases              = array();
                    $arInformativos       = array();
                    $arComplementares     = array();
                    $nuTotalProventos     = 0;
                    $nuTotalDescontos     = 0;
                    $rsEventosCalculados->setPrimeiroElemento();
                    $nuValorBasePrevidencia = 0;
                    $nuValorBaseIRRF        = 0;
                    $nuValorBaseFGTS        = 0;
                    $rsEventosCalculados->addFormatacao("descricao","SUBSTRING(0,20)");
                    while ( !$rsEventosCalculados->eof() ) {
                        if ( $rsEventosCalculados->getCampo('cod_contrato') == $inCodContrato ) {
                            if ( $rsEventosCalculados->getCampo('natureza') == "P" ) {
                                $inIndex = count($arProventos);
                                $arProventos[$inIndex]["campo1"] = $rsEventosCalculados->getCampo('codigo');
                                $arProventos[$inIndex]["campo2"] = number_format($rsEventosCalculados->getCampo('quantidade'),2,',','.');
                                $arProventos[$inIndex]["campo3"] = $rsEventosCalculados->getCampo('descricao');
                                $arProventos[$inIndex]["campo4"] = number_format($rsEventosCalculados->getCampo('valor'),2,',','.');
                                $nuTotalProventos += $rsEventosCalculados->getCampo('valor');
                                $arProventosTotais[$rsEventosCalculados->getCampo('cod_evento')] = array("campo1"    =>$rsEventosCalculados->getCampo('codigo'),
                                                                                                         "campo2"    =>$arProventosTotais[$rsEventosCalculados->getCampo('cod_evento')]["campo2"] + $rsEventosCalculados->getCampo('quantidade'),
                                                                                                         "campo3"    =>$rsEventosCalculados->getCampo('descricao'),
                                                                                                         "campo4"    =>"",
                                                                                                         "campo5"    =>$arProventosTotais[$rsEventosCalculados->getCampo('cod_evento')]["campo5"] + $rsEventosCalculados->getCampo('valor'));
                            } elseif ( $rsEventosCalculados->getCampo('natureza') == "D" ) {
                                $inIndex = count($arDescontos);
                                $arDescontos[$inIndex]["campo5"] = $rsEventosCalculados->getCampo('codigo');
                                $arDescontos[$inIndex]["campo6"] = number_format($rsEventosCalculados->getCampo('quantidade'),2,',','.');;
                                $arDescontos[$inIndex]["campo7"] = $rsEventosCalculados->getCampo('descricao');
                                $arDescontos[$inIndex]["campo8"] = number_format($rsEventosCalculados->getCampo('valor'),2,',','.');;
                                $nuTotalDescontos += $rsEventosCalculados->getCampo('valor');
                                $arDescontosTotais[$rsEventosCalculados->getCampo('cod_evento')] = array("campo6"    =>$rsEventosCalculados->getCampo('codigo'),
                                                                                                         "campo7"    =>$arDescontosTotais[$rsEventosCalculados->getCampo('cod_evento')]["campo7"] + $rsEventosCalculados->getCampo('quantidade'),
                                                                                                         "campo8"    =>$rsEventosCalculados->getCampo('descricao'),
                                                                                                         "campo9"    =>"",
                                                                                                         "campo10"   =>$arDescontosTotais[$rsEventosCalculados->getCampo('cod_evento')]["campo10"] + $rsEventosCalculados->getCampo('valor'));

                            }
                            if ( $rsEventosCalculados->getCampo('natureza') == 'B' ) {
                                $inIndex = count($arBasesInformativos);
                                $arBasesInformativos[$inIndex]["campo9"]  = $rsEventosCalculados->getCampo('codigo');
                                $arBasesInformativos[$inIndex]["campo10"] = $rsEventosCalculados->getCampo('descricao');
                                $arBasesInformativos[$inIndex]["campo11"] = number_format($rsEventosCalculados->getCampo('valor'),2,',','.');
                                $arBasesTotais[$rsEventosCalculados->getCampo('cod_evento')] = array("campo1"    =>$rsEventosCalculados->getCampo('codigo'),
                                                                                                     "campo2"    =>$rsEventosCalculados->getCampo('descricao'),
                                                                                                     "campo3"    =>$arBasesTotais[$rsEventosCalculados->getCampo('cod_evento')]["campo3"] + $rsEventosCalculados->getCampo('valor'));

                            } elseif ( $rsEventosCalculados->getCampo('natureza') == "I" ) {
                                $inIndex = count($arBasesInformativos);
                                $arBasesInformativos[$inIndex]["campo9"]  = $rsEventosCalculados->getCampo('codigo');
                                $arBasesInformativos[$inIndex]["campo10"] = $rsEventosCalculados->getCampo('descricao');
                                $arBasesInformativos[$inIndex]["campo11"] = number_format($rsEventosCalculados->getCampo('valor'),2,',','.');
                                $arInformativosTotais[$rsEventosCalculados->getCampo('cod_evento')] = array("campo4"    =>$rsEventosCalculados->getCampo('codigo'),
                                                                                                            "campo5"    =>$rsEventosCalculados->getCampo('descricao'),
                                                                                                            "campo6"    =>$arInformativosTotais[$rsEventosCalculados->getCampo('cod_evento')]["campo6"] + $rsEventosCalculados->getCampo('valor'));

                            }
                        }
                        $rsEventosCalculados->proximo();
                    }
                    $inCount = ( count($arProventos) > count($arDescontos) ) ? count($arProventos) : count($arDescontos);
                    $inCount = ( count($arBasesInformativos) > $inCount )    ? count($arBasesInformativos) : $inCount;
                    $arEventos = array();
                    for ($inIndex=0;$inIndex<$inCount;$inIndex++) {
                        $arMerge = array_merge($arProventos[$inIndex],$arDescontos[$inIndex],$arBasesInformativos[$inIndex]);
                        $arEventos[] = $arMerge;
                    }

                    $arTotalProventosDescontos['campo1'] = str_pad("TOTAL DE PROVENTOS", 70, ".");
                    $arTotalProventosDescontos['campo2'] = ":";
                    $arTotalProventosDescontos['campo3'] = number_format($nuTotalProventos,2,',','.');
                    $arTotalProventosDescontos['campo4'] = str_pad("TOTAL DE DESCONTOS", 70, ".");
                    $arTotalProventosDescontos['campo5'] = ":";
                    $arTotalProventosDescontos['campo6'] = number_format($nuTotalDescontos,2,',','.');
                    $arTotalProventosDescontos['campo7'] = str_pad("LÍQUIDO", 50, ".");
                    $arTotalProventosDescontos['campo8'] = ":";
                    $arTotalProventosDescontos['campo9'] = number_format($nuTotalProventos - $nuTotalDescontos,2,',','.');
                    $arTotalPorContrato                  = array();
                    $arTotalPorContrato[]                = $arTotalProventosDescontos;

                    $arContrato['contrato1']                  = $arContrato1;
                    $arContrato['contrato2']                  = $arContrato2;
                    $arContrato['eventos']                    = $arEventos;
                    $arContrato['total_por_contrato']         = $arTotalPorContrato;

                    $arContratos[]                            = $arContrato;
                }
                $arTemp           = array();
                $arTitulo5        = array();
                $arTemp["campo1"] = "PROVENTOS";
                $arTemp["campo2"] = "DESCONTOS";
                $arTemp["campo3"] = "BASES/INFORMATIVOS";
                $arTitulo5[]      = $arTemp;
                $arPagina['linha1']                     = $arLinha1;
                $arPagina['titulo5']                    = $arTitulo5;
                $arPagina['contratos']                  = $arContratos;
                $arRelatorio[]                          = $arPagina;

                $boProcessar = false;
                if ($arFiltro['boEmitirTotais'] or $arFiltro['boEmitirRelatorio']) {
                    if ($arFiltro['boLotacao']) {
                        $boProcessar = true;
                        $arRelatorio[0]['contratos'] = $this->agruparAnaliticaResumidaPor("LOTAÇÃO",$arRelatorio[0]['contratos']);
                    }
                    if ($arFiltro['boLocal']) {
                        $arRelatorio[0]['contratos'] = $this->separarRelatorioResumida("LOCAL",$boProcessar,$arRelatorio[0]['contratos']);
                    }
                    if ($arFiltro['boRegimedaFuncao']) {
                        $arRelatorio[0]['contratos'] = $this->separarRelatorioResumida("REGIME DA FUNÇÃO",$boProcessar,$arRelatorio[0]['contratos']);
                    }
                    if ($arFiltro['boFuncao']) {
                        $arRelatorio[0]['contratos'] = $this->separarRelatorioResumida("FUNÇÃO",$boProcessar,$arRelatorio[0]['contratos']);
                    }
                    if ($arFiltro['boCgm']) {
                        $arRelatorio[0]['contratos'] = $this->separarRelatorioResumida("CGM",$boProcessar,$arRelatorio[0]['contratos']);
                    }
                    if ($arFiltro['boEmitirRelatorio']) {
                        $arRelatorio[0]['contratos'] = $this->emitirSomenteTotais($arRelatorio[0]['contratos']);
                    }
                }
                if ( count($arRelatorio) ) {
                    $nuTotalProventos = 0;
                    $nuTotalDescontos = 0;
                    $arTemp           = array();
                    $arTitulo4        = array();
                    $arProventosDescontosTotal = array();
                    $inCount  = ( count($arProventosTotais) > count($arDescontosTotais) )  ? count($arProventosTotais) : count($arDescontosTotais);
                    $inCount2 = ( count($arBasesTotais)     > count($arInformativosTotais))? count($arBasesTotais)     : count($arInformativosTotais);
                    $inCount  = ( $inCount                  > $inCount2 )                  ? $inCount                  : $inCount2;
                    for ($inIndex=0;$inIndex<$inCount;$inIndex++) {
                        if ( is_array($arProventosTotais) ) {
                            $arTempProventos    = array_shift($arProventosTotais);
                        }
                        if ( is_array($arDescontosTotais) ) {
                            $arTempDescontos    = array_shift($arDescontosTotais);
                        }
                        if ( is_array($arBasesTotais) ) {
                            $arTempBases        = array_shift($arBasesTotais);
                        }
                        if ( is_array($arInformativosTotais) ) {
                            $arTempInformativos = array_shift($arInformativosTotais);
                        }
                        if ( is_array($arTempProventos) ) {
                            $nuTotalProventos += $arTempProventos["campo5"];
                            $arTempProventos["campo2"] = number_format($arTempProventos["campo2"],2,',','.');
                            $arTempProventos["campo5"] = number_format($arTempProventos["campo5"],2,',','.');
                        }
                        if ( is_array($arTempDescontos) ) {
                            $nuTotalDescontos += $arTempDescontos["campo10"];
                            $arTempDescontos["campo7"] = number_format($arTempDescontos["campo7"],2,',','.');
                            $arTempDescontos["campo10"] = number_format($arTempDescontos["campo10"],2,',','.');
                        }
                        if ( is_array($arTempBases) ) {
                            $arTempBasesInformativos["campo11"] = $arTempBases["campo1"];
                            $arTempBasesInformativos["campo12"] = $arTempBases["campo2"];
                            $arTempBasesInformativos["campo13"] = number_format($arTempBases["campo3"],2,',','.');
                        }
                        if ( is_array($arTempInformativos) ) {
                            $arTempBasesInformativos["campo11"] = $arTempInformativos["campo1"];
                            $arTempBasesInformativos["campo12"] = $arTempInformativos["campo2"];
                            $arTempBasesInformativos["campo13"] = number_format($arTempInformativos["campo3"],2,',','.');
                        }
                        if ( !is_array($arTempBases) and !is_array($arTempInformativos) ) {
                            $arTempBasesInformativos = "";
                        }
                        $arMerge = array_merge($arTempDescontos,$arTempProventos,$arTempBasesInformativos);
                        $arEventosTotal[] = $arMerge;
                    }
                    $arTemp             = array();
                    $arTemp['campo1']   = str_pad("TOTAL DE PROVENTOS", 70, ".");
                    $arTemp['campo2']   = ":";
                    $arTemp['campo3']   = number_format($nuTotalProventos,2,',','.');
                    $arTemp['campo4']   = str_pad("TOTAL DE DESCONTOS", 70, ".");
                    $arTemp['campo5']   = ":";
                    $arTemp['campo6']   = number_format($nuTotalDescontos,2,',','.');
                    $arTemp['campo7']   = str_pad("SALÁRIO LÍQUIDO", 50, ".");
                    $arTemp['campo8']   = ":";
                    $arTemp['campo9']   = number_format($nuTotalProventos - $nuTotalDescontos,2,',','.');
                    $arTotalGeral[]     = $arTemp;

                    $arTemp["campo1"] = "RESUMO GERAL";
                    $arTitulo4[]      = $arTemp;
                    $arPagina         = array();

                    $arTemp           = array();
                    $arTitulo5        = array();
                    $arTemp["campo1"] = "PROVENTOS";
                    $arTemp["campo2"] = "DESCONTOS";
                    $arTemp["campo3"] = "BASES/INFORMATIVOS";
                    $arTitulo5[]      = $arTemp;

                    $arPagina['boTotais']                   = true;
                    $arPagina['linha1']                     = $arLinha1;
                    $arPagina['titulo4']                    = $arTitulo4;
                    $arPagina['eventos_total']              = $arEventosTotal;
                    $arPagina['titulo5']                    = $arTitulo5;
                    $arPagina['total_geral']                = $arTotalGeral;
                    $arPagina['total_servidor'][]           = array("campo1"=>"Total de Servidores:","campo2"=>$inTotalServidores);
                    $arRelatorio[]                          = $arPagina;
                }

            break;
            case 'analítica':
                $arCodContratos = array();
                $arProventosDescontosTotal = array();
                $arProventosTotais         = array();
                $arDescontosTotais         = array();
                while ( !$rsEventosCalculados->eof()  ) {
                    $arCodContratos[] = $rsEventosCalculados->getCampo("cod_contrato");
                    $rsEventosCalculados->proximo();
                }
                $arCodContratos = array_unique($arCodContratos);
                foreach ($arCodContratos as $inCodContrato) {
                    $arTemp           = array();
                    $arLinha1         = array();
                    $arTemp['campo1'] = "Tipo da Folha:";
                    $arTemp['campo2'] = $stFolha;
                    $arTemp['campo3'] = "Competência:";
                    $arTemp['campo4'] = $stCompetencia;
                    $arTemp['campo5'] = "Período Movimentação:";
                    $arTemp['campo6'] = $rsMovimentacao->getCampo('dt_inicial') ." até ". $rsMovimentacao->getCampo('dt_final');
                    $arLinha1[]       = $arTemp;
                    $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoCalculoFolhaPagamento->listarRelatorioFolhaAnalitica($rsDadosContrato,$inCodContrato);
                    $arTemp           = array();
                    $arContrato       = array();
                    $arTemp["campo1"] = str_pad("Matrícula", 30, ".");
                    $arTemp["campo2"] = $rsDadosContrato->getCampo('registro');
                    $arTemp["campo3"] = str_pad("Regime", 30, ".");
                    $arTemp["campo4"] = $rsDadosContrato->getCampo('cod_regime_cargo')."-".$rsDadosContrato->getCampo('descricao_regime_cargo');
                    $arTemp["campo5"] = str_pad("Regime", 30, ".");
                    $arTemp["campo6"] = $rsDadosContrato->getCampo('cod_regime_funcao')."-".$rsDadosContrato->getCampo('descricao_regime_funcao');
                    $arTemp["campo7"] = str_pad("Padrão", 30, ".");
                    $arTemp["campo8"] = $rsDadosContrato->getCampo('descricao_padrao');
                    $arTemp["campo9"] = ":";
                    $arTemp["campo10"] = ":";
                    $arTemp["campo11"] = ":";
                    $arTemp["campo12"] = ":";
                    $arContrato[]     = $arTemp;
                    $arTemp["campo1"] = str_pad("CGM", 30, ".");
                    $arTemp["campo2"] = $rsDadosContrato->getCampo('numcgm')."-".$rsDadosContrato->getCampo('nom_cgm');
                    $arTemp["campo3"] = str_pad("Subdivisão", 30 , ".");
                    $arTemp["campo4"] = $rsDadosContrato->getCampo('cod_sub_divisao_cargo')."-".$rsDadosContrato->getCampo('descricao_sub_divisao_cargo');
                    $arTemp["campo5"] = str_pad("Subdivisão", 30, ".");
                    $arTemp["campo6"] = $rsDadosContrato->getCampo('cod_sub_divisao_funcao')."-".$rsDadosContrato->getCampo('descricao_sub_divisao_funcao');
                    $arTemp["campo7"] = str_pad("Progressão", 30, ".");
                    $arTemp["campo8"] = $rsDadosContrato->getCampo('descricao_nivel_padrao');
                    $arTemp["campo9"] = ":";
                    $arTemp["campo10"] = ":";
                    $arTemp["campo11"] = ":";
                    $arTemp["campo12"] = ":";
                    $arContrato[]     = $arTemp;
                    $arTemp["campo1"] = str_pad("Lotação", 30 ,".");
                    $arTemp["campo2"] = $rsDadosContrato->getCampo('cod_orgao')."-".$rsDadosContrato->getCampo('descricao_lotacao');
                    $arTemp["campo3"] = str_pad("Cargo", 30, ".");
                    $arTemp["campo4"] = $rsDadosContrato->getCampo('cod_cargo')."-".$rsDadosContrato->getCampo('descricao_cargo');
                    $arTemp["campo5"] = str_pad("Função", 30, ".");
                    $arTemp["campo6"] = $rsDadosContrato->getCampo('cod_funcao')."-".$rsDadosContrato->getCampo('descricao_funcao');
                    $arTemp["campo7"] = str_pad("Data[admissão/posse]", 30, ".");
                    $arTemp["campo8"] = $rsDadosContrato->getCampo('dt_nomeacao')." - ".$rsDadosContrato->getCampo('dt_posse');
                    $arTemp["campo9"] = ":";
                    $arTemp["campo10"] = ":";
                    $arTemp["campo11"] = ":";
                    $arTemp["campo12"] = ":";
                    $arContrato[]     = $arTemp;

                    if ( $rsDadosContrato->getCampo('cod_local') != "" ) {
                        $stLocal = $rsDadosContrato->getCampo('cod_local')."-".$rsDadosContrato->getCampo('descricao_local');
                    } else {
                        $stLocal = "";
                    }
                    if ( $rsDadosContrato->getCampo('cod_especialidade_cargo') != "" ) {
                        $stEspecialidadeCargo = $rsDadosContrato->getCampo('cod_especialidade_cargo')."-".$rsDadosContrato->getCampo('descricao_especialidade_cargo');
                    } else {
                        $stEspecialidadeCargo = "";
                    }
                    if ( $rsDadosContrato->getCampo('cod_especialidade_funcao') != "" ) {
                        $stEspecialidadeFuncao = $rsDadosContrato->getCampo('cod_especialidade_funcao')."-".$rsDadosContrato->getCampo('descricao_especialidade_funcao');
                    } else {
                        $stEspecialidadeFuncao = "";
                    }

                    $arTemp["campo1"] = str_pad("Local", 30, ".");
                    $arTemp["campo2"] = $stLocal;
                    $arTemp["campo3"] = str_pad("Especialidade", 30, ".");
                    $arTemp["campo4"] = $stEspecialidadeCargo;
                    $arTemp["campo5"] = str_pad("Especialidade", 30, ".");
                    $arTemp["campo6"] = $stEspecialidadeFuncao;
                    $arTemp["campo7"] = str_pad("Multiplos Vínculos", 30, ".");
                    $arTemp["campo8"] = $rsDadosContrato->getCampo('multiplos');
                    $arTemp["campo9"] = ":";
                    $arTemp["campo10"] = ":";
                    $arTemp["campo11"] = ":";
                    $arTemp["campo12"] = ":";
                    $arContrato[]     = $arTemp;
                    $arTemp["campo1"] = str_pad("C. Horária", 30, ".");
                    $arTemp["campo2"] = $rsDadosContrato->getCampo('horas_mensais');
                    $arTemp["campo3"] = str_pad("Situação", 30, ".");
                    $arTemp["campo4"] = $rsDadosContrato->getCampo('situacao');
                    $arTemp["campo5"] = str_pad("Prev. Oficial", 30, ".");
                    $arTemp["campo6"] = $rsDadosContrato->getCampo('descricao_previdencia');
                    $arTemp["campo7"] = "";
                    $arTemp["campo8"] = "";
                    $arTemp["campo9"] = ":";
                    $arTemp["campo10"] = ":";
                    $arTemp["campo11"] = ":";
                    $arTemp["campo12"] = "";
                    $arContrato[]     = $arTemp;

                    $arTemp            = array();
                    $arLinha2          = array();
                    $arTemp["campo1"]  = str_pad("_",176,"_");
                    $arLinha2[]        = $arTemp;

                    $arTemp           = array();
                    $arTitulo1        = array();
                    $arTemp["campo1"] = "PROVENTOS";
                    $arTemp["campo2"] = "DESCONTOS";
                    $arTitulo1[]      = $arTemp;

                    $arTemp               = array();
                    $arProventosDescontos = array();
                    $arBasesInformativos  = array();
                    $arProventos          = array();
                    $arDescontos          = array();
                    $arBases              = array();
                    $arInformativos       = array();
                    $arComplementares     = array();
                    $nuTotalProventos     = 0;
                    $nuTotalDescontos     = 0;
                    $rsEventosCalculados->setPrimeiroElemento();
                    $nuValorBasePrevidencia = 0;
                    $nuValorBaseIRRF        = 0;
                    $nuValorBaseFGTS        = 0;

                    while ( !$rsEventosCalculados->eof() ) {
                        if ( $rsEventosCalculados->getCampo('cod_contrato') == $inCodContrato ) {
                            if ( $rsEventosCalculados->getCampo('natureza') == "P" ) {
                                $inIndex = count($arProventos);
                                $arProventos[$inIndex]["campo1"] = $rsEventosCalculados->getCampo('codigo');
                                $arProventos[$inIndex]["campo2"] = number_format($rsEventosCalculados->getCampo('quantidade'),2,',','.');
                                $arProventos[$inIndex]["campo3"] = $rsEventosCalculados->getCampo('descricao');
                                $arProventos[$inIndex]["campo4"] = number_format($rsEventosCalculados->getCampo('valor'),2,',','.');
                                $nuTotalProventos += $rsEventosCalculados->getCampo('valor');
                                $arProventosTotais[$rsEventosCalculados->getCampo('cod_evento')] = array("campo1"    =>$rsEventosCalculados->getCampo('codigo'),
                                                                                                         "campo2"    =>$arProventosTotais[$rsEventosCalculados->getCampo('cod_evento')]["campo2"] + $rsEventosCalculados->getCampo('quantidade'),
                                                                                                         "campo3"    =>$rsEventosCalculados->getCampo('descricao'),
                                                                                                         "campo4"    =>"",
                                                                                                         "campo5"    =>$arProventosTotais[$rsEventosCalculados->getCampo('cod_evento')]["campo5"] + $rsEventosCalculados->getCampo('valor'));
                            } elseif ( $rsEventosCalculados->getCampo('natureza') == "D" ) {
                                $inIndex = count($arDescontos);
                                $arDescontos[$inIndex]["campo5"] = $rsEventosCalculados->getCampo('codigo');
                                $arDescontos[$inIndex]["campo6"] = number_format($rsEventosCalculados->getCampo('quantidade'),2,',','.');;
                                $arDescontos[$inIndex]["campo7"] = $rsEventosCalculados->getCampo('descricao');
                                $arDescontos[$inIndex]["campo8"] = number_format($rsEventosCalculados->getCampo('valor'),2,',','.');;
                                $nuTotalDescontos += $rsEventosCalculados->getCampo('valor');
                                $arDescontosTotais[$rsEventosCalculados->getCampo('cod_evento')] = array("campo6"    =>$rsEventosCalculados->getCampo('codigo'),
                                                                                                         "campo7"    =>$arDescontosTotais[$rsEventosCalculados->getCampo('cod_evento')]["campo7"] + $rsEventosCalculados->getCampo('quantidade'),
                                                                                                         "campo8"    =>$rsEventosCalculados->getCampo('descricao'),
                                                                                                         "campo9"    =>"",
                                                                                                         "campo10"   =>$arDescontosTotais[$rsEventosCalculados->getCampo('cod_evento')]["campo10"] + $rsEventosCalculados->getCampo('valor'));

                            }
                            if ( $rsEventosCalculados->getCampo('natureza') == 'B' ) {
                                if ($arFiltro['inCodConfiguracao'] == 2 or $arFiltro['inCodConfiguracao'] == 3) {
                                    $arCodEvento = explode("_",$rsEventosCalculados->getCampo('cod_evento'));
                                    $inCodEvento = $arCodEvento[0];
                                } else {
                                    $inCodEvento = $rsEventosCalculados->getCampo('cod_evento');
                                }
                                $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoCalculoFolhaPagamento->listarRelatorioFolhaAnaliticaEventoPrevidencia($rsEventoPrevidencia,$inCodContrato,$inCodEvento);
                                $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoCalculoFolhaPagamento->listarRelatorioFolhaAnaliticaEventoIRRF($rsEventoIRRF,$inCodEvento,7);
                                $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoCalculoFolhaPagamento->listarRelatorioFolhaAnaliticaEventoFGTS($rsEventoFGTS,$inCodEvento,3);
                                $boEntrar = true;
                                if ( $rsEventoPrevidencia->getNumLinhas() == 1 ) {
                                    $nuValorBasePrevidencia       = $rsEventosCalculados->getCampo('valor');
                                    $nuValorBasePrevidenciaTotal += $nuValorBasePrevidencia;
                                    $boEntrar = false;
                                }
                                if ( $rsEventoIRRF->getNumLinhas() == 1 ) {
                                    $nuValorBaseIRRF       = $rsEventosCalculados->getCampo('valor');
                                    $nuValorBaseIRRFTotal += $nuValorBaseIRRF;
                                    $boEntrar = false;
                                }
                                if ( $rsEventoFGTS->getNumLinhas() == 1 ) {
                                    $nuValorBaseFGTS       = $rsEventosCalculados->getCampo('valor');
                                    $nuValorBaseFGTSTotal += $nuValorBaseFGTS;
                                    $boEntrar = false;
                                }
                                if ($boEntrar) {
                                    $inIndex = count($arBases);
                                    $arBases[$inIndex]["campo1"] = $rsEventosCalculados->getCampo('codigo');
                                    $arBases[$inIndex]["campo2"] = $rsEventosCalculados->getCampo('descricao');
                                    $arBases[$inIndex]["campo3"] = number_format($rsEventosCalculados->getCampo('valor'),2,',','.');

                                    $arBasesTotais[$rsEventosCalculados->getCampo('cod_evento')] = array("campo1"    =>$rsEventosCalculados->getCampo('codigo'),
                                                                                                         "campo2"    =>$rsEventosCalculados->getCampo('descricao'),
                                                                                                         "campo3"    =>$arBasesTotais[$rsEventosCalculados->getCampo('cod_evento')]["campo3"] + $rsEventosCalculados->getCampo('valor'));

                                }
                            } elseif ( $rsEventosCalculados->getCampo('natureza') == "I" ) {
                                $inIndex = count($arInformativos);
                                $arInformativos[$inIndex]["campo4"] = $rsEventosCalculados->getCampo('codigo');
                                $arInformativos[$inIndex]["campo5"] = $rsEventosCalculados->getCampo('descricao');
                                $arInformativos[$inIndex]["campo6"] = number_format($rsEventosCalculados->getCampo('valor'),2,',','.');

                                $arInformativosTotais[$rsEventosCalculados->getCampo('cod_evento')] = array("campo4"    =>$rsEventosCalculados->getCampo('codigo'),
                                                                                                            "campo5"    =>$rsEventosCalculados->getCampo('descricao'),
                                                                                                            "campo6"    =>$arInformativosTotais[$rsEventosCalculados->getCampo('cod_evento')]["campo6"] + $rsEventosCalculados->getCampo('valor'));

                            }
                        }
                        $rsEventosCalculados->proximo();
                    }
                    $inCount = ( count($arProventos) > count($arDescontos) ) ? count($arProventos) : count($arDescontos);
                    for ($inIndex=0;$inIndex<$inCount;$inIndex++) {
                        $arMerge = array_merge($arProventos[$inIndex],$arDescontos[$inIndex]);
                        $arProventosDescontos[] = $arMerge;
                    }
                    $inCount = ( count($arBases) > count($arInformativos) ) ? count($arBases) : count($arInformativos);
                    for ($inIndex=0;$inIndex<$inCount;$inIndex++) {
                        $arMerge = array_merge($arBases[$inIndex],$arInformativos[$inIndex]);
                        $arBasesInformativos[] = $arMerge;
                    }

                    $arTotalProventosDescontos['campo1'] = "";
                    $arTotalProventosDescontos['campo2'] = "";
                    $arTotalProventosDescontos['campo3'] = str_pad("TOTAL DE PROVENTOS", 70, ".");
                    $arTotalProventosDescontos['campo4'] = number_format($nuTotalProventos,2,',','.');
                    $arTotalProventosDescontos['campo5'] = "";
                    $arTotalProventosDescontos['campo6'] = "";
                    $arTotalProventosDescontos['campo7'] = str_pad("TOTAL DE DESCONTOS", 70, ".");
                    $arTotalProventosDescontos['campo8'] = number_format($nuTotalDescontos,2,',','.');
                    $arTotalProventosDescontos['campo9'] = ":";
                    $arTotalProventosDescontos['campo10'] = ":";
                    $arProventosDescontos[]              = $arTotalProventosDescontos;
                    $arTotalProventosDescontos['campo1'] = "";
                    $arTotalProventosDescontos['campo2'] = "";
                    $arTotalProventosDescontos['campo3'] = str_pad("SALÁRIO LÍQUIDO", 70, ".");
                    $arTotalProventosDescontos['campo4'] = number_format($nuTotalProventos - $nuTotalDescontos,2,',','.');
                    $arTotalProventosDescontos['campo5'] = "";
                    $arTotalProventosDescontos['campo6'] = "";
                    $arTotalProventosDescontos['campo7'] = "";
                    $arTotalProventosDescontos['campo8'] = "";
                    $arTotalProventosDescontos['campo9'] = ":";
                    $arTotalProventosDescontos['campo10'] = "";
                    $arProventosDescontos[]              = $arTotalProventosDescontos;

                    $arTemp  = array();
                    $arBases = array();
                    $arTemp['campo1'] = "Base Previdência:";
                    $arTemp['campo2'] = number_format($nuValorBasePrevidencia,2,',','.');
                    $arTemp['campo3'] = "Base IRRF:";
                    $arTemp['campo4'] = number_format($nuValorBaseIRRF,2,',','.');
                    $arTemp['campo5'] = "Base FGTS:";
                    $arTemp['campo6'] = number_format($nuValorBaseFGTS,2,',','.');
                    $arBases[]        = $arTemp;

                    $arTemp           = array();
                    $arTitulo2        = array();
                    $arTemp["campo1"] = "EVENTOS DE BASE";
                    $arTemp["campo2"] = "EVENTO INFORMATIVOS";
                    $arTitulo2[]      = $arTemp;

                    $arTemp           = array();
                    $arTitulo3        = array();
                    $arTemp["campo1"] = "OUTRAS FOLHAS";
                    $arTitulo3[]      = $arTemp;

                    $this->obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoCalculoFolhaPagamento->listarRelatorioFolhaAnaliticaOutrasFolhas($rsComplementares,$inCodContrato,$rsMovimentacao->getCampo('cod_periodo_movimentacao'));
                    while ( !$rsComplementares->eof() ) {
                        $arComplementar["campo1"] = "Complementar ".$rsComplementares->getCampo('cod_complementar');
                        $arComplementar["campo2"] = "Base Previdência:";
                        $arComplementar["campo3"] = number_format($rsComplementares->getCampo('valor_base_previdencia'),2,',','.');
                        $arComplementar["campo4"] = "Desc. INSS:";
                        $arComplementar["campo5"] = number_format($rsComplementares->getCampo('valor_desconto_previdencia'),2,',','.');
                        $arComplementar["campo6"] = "Base IRRF:";
                        $arComplementar["campo7"] = number_format($rsComplementares->getCampo('valor_base_irrf'),2,',','.');
                        $arComplementar["campo8"] = "Desc. IRRF:";
                        $arComplementar["campo9"] = number_format($rsComplementares->getCampo('valor_desconto_irrf'),2,',','.');
                        $arComplementares[] = $arComplementar;
                        $arComplementar["campo1"] = "";
                        $arComplementar["campo2"] = "Base FGTS:";
                        $arComplementar["campo3"] = number_format($rsComplementares->getCampo('valor_base_fgts'),2,',','.');
                        $arComplementar["campo4"] = "Valor Recolhido de FGTS:";
                        $arComplementar["campo5"] = number_format($rsComplementares->getCampo('valor_recolhido_fgts'),2,',','.');
                        $arComplementar["campo6"] = "Valor Contribuição Social:";
                        $arComplementar["campo7"] = number_format($rsComplementares->getCampo('valor_contribuicao_social'),2,',','.');
                        $arComplementar["campo8"] = "";
                        $arComplementar["campo9"] = "";
                        $arComplementares[] = $arComplementar;

                        $arComplementarTotal["campo1"] = "Complementar ".$rsComplementares->getCampo('cod_complementar');
                        $arComplementarTotal["campo2"] = "Base Previdência:";
                        $arComplementarTotal["campo3"] = $arComplementaresTotal[$rsComplementares->getCampo('cod_complementar')."0"]["campo3"] + $rsComplementares->getCampo('valor_base_previdencia');
                        $arComplementarTotal["campo4"] = "Desc. INSS:";
                        $arComplementarTotal["campo5"] = $arComplementaresTotal[$rsComplementares->getCampo('cod_complementar')."0"]["campo5"] + $rsComplementares->getCampo('valor_desconto_previdencia');
                        $arComplementarTotal["campo6"] = "Base IRRF:";
                        $arComplementarTotal["campo7"] = $arComplementaresTotal[$rsComplementares->getCampo('cod_complementar')."0"]["campo7"] + $rsComplementares->getCampo('valor_base_irrf');
                        $arComplementarTotal["campo8"] = "Desc. IRRF:";
                        $arComplementarTotal["campo9"] = $arComplementaresTotal[$rsComplementares->getCampo('cod_complementar')."0"]["campo9"] + $rsComplementares->getCampo('valor_desconto_irrf');
                        $arComplementaresTotal[$rsComplementares->getCampo('cod_complementar')."0"] = $arComplementarTotal;
                        $arComplementarTotal["campo1"] = "";
                        $arComplementarTotal["campo2"] = "Base FGTS:";
                        $arComplementarTotal["campo3"] = $arComplementaresTotal[$rsComplementares->getCampo('cod_complementar')."1"]["campo3"] + $rsComplementares->getCampo('valor_base_fgts');
                        $arComplementarTotal["campo4"] = "Valor Recolhido de FGTS:";
                        $arComplementarTotal["campo5"] = $arComplementaresTotal[$rsComplementares->getCampo('cod_complementar')."1"]["campo5"] + $rsComplementares->getCampo('valor_recolhido_fgts');
                        $arComplementarTotal["campo6"] = "Valor Contribuição Social:";
                        $arComplementarTotal["campo7"] = $arComplementaresTotal[$rsComplementares->getCampo('cod_complementar')."1"]["campo7"] + $rsComplementares->getCampo('valor_contribuicao_social');
                        $arComplementarTotal["campo8"] = "";
                        $arComplementarTotal["campo9"] = "";
                        $arComplementaresTotal[$rsComplementares->getCampo('cod_complementar')."1"] = $arComplementarTotal;

                        $rsComplementares->proximo();
                    }

                    $arPagina['linha1']                     = $arLinha1;
                    $arPagina['contrato']                   = $arContrato;
                    $arPagina['linha2']                     = $arLinha2;
                    $arPagina['titulo1']                    = $arTitulo1;
                    $arPagina['proventos_descontos']        = $arProventosDescontos;
                    $arPagina['bases']                      = $arBases;
                    $arPagina['titulo2']                    = $arTitulo2;
                    $arPagina['bases_informativos']         = $arBasesInformativos;
                    $arPagina['titulo3']                    = $arTitulo3;
                    $arPagina['complementares']             = $arComplementares;
                    $arRelatorio[]                          = $arPagina;
                }
                $boProcessar = false;
                if ($arFiltro['boLotacao']) {
                    $boProcessar = true;
                    $arRelatorio = $this->agruparAnaliticaPor("LOTAÇÃO",$arRelatorio,$arLinha1);
                }
                if ($arFiltro['boLocal']) {
                    $arRelatorio = $this->separarRelatorio("LOCAL",$boProcessar,$arRelatorio,$arLinha1);
                }
                if ($arFiltro['boRegimedoCargo']) {
                    $arRelatorio = $this->separarRelatorio("REGIME DO CARGO",$boProcessar,$arRelatorio,$arLinha1);
                }
                if ($arFiltro['boSubdivisaodoCargo']) {
                    $arRelatorio = $this->separarRelatorio("SUBDIVISÃO DO CARGO",$boProcessar,$arRelatorio,$arLinha1);
                }
                if ($arFiltro['boCargo']) {
                    $arRelatorio = $this->separarRelatorio("CARGO",$boProcessar,$arRelatorio,$arLinha1);
                }
                if ($arFiltro['boEspecialidadedoCargo']) {
                    $arRelatorio = $this->separarRelatorio("ESPECIALIDADE DO CARGO",$boProcessar,$arRelatorio,$arLinha1);
                }
                if ($arFiltro['boRegimedaFuncao']) {
                    $arRelatorio = $this->separarRelatorio("REGIME DA FUNÇÃO",$boProcessar,$arRelatorio,$arLinha1);
                }
                if ($arFiltro['boSubdivisaodaFuncao']) {
                    $arRelatorio = $this->separarRelatorio("SUBDIVISÃO DA FUNÇÃO",$boProcessar,$arRelatorio,$arLinha1);
                }
                if ($arFiltro['boFuncao']) {
                    $arRelatorio = $this->separarRelatorio("FUNÇÃO",$boProcessar,$arRelatorio,$arLinha1);
                }
                if ($arFiltro['boEspecialidadedaFuncao']) {
                    $arRelatorio = $this->separarRelatorio("ESPECIALIDADE DA FUNÇÃO",$boProcessar,$arRelatorio,$arLinha1);
                }
                if ($arFiltro['boSituacao']) {
                    $arRelatorio = $this->separarRelatorio("SITUAÇÃO",$boProcessar,$arRelatorio,$arLinha1);
                }
                if ($arFiltro['boCgm']) {
                    $arRelatorio = $this->separarRelatorio("CGM",$boProcessar,$arRelatorio,$arLinha1);
                }
                if ($arFiltro['boEmitirRelatorio']) {
                    $arRelatorio = $this->emitirSomenteTotais($arRelatorio);
                }
                if ( count($arRelatorio) ) {
                    $nuTotalProventos = 0;
                    $nuTotalDescontos = 0;
                    $arTemp           = array();
                    $arTitulo4        = array();
                    $arProventosDescontosTotal = array();
                    $inCount = ( count($arProventosTotais) > count($arDescontosTotais) ) ? count($arProventosTotais) : count($arDescontosTotais);
                    for ($inIndex=0;$inIndex<$inCount;$inIndex++) {
                        $arTempProventos = array_shift($arProventosTotais);
                        $arTempDescontos = array_shift($arDescontosTotais);
                        if ( is_array($arTempProventos) ) {
                            $nuTotalProventos += $arTempProventos["campo5"];
                            $arTempProventos["campo2"] = number_format($arTempProventos["campo2"],2,',','.');
                            $arTempProventos["campo5"] = number_format($arTempProventos["campo5"],2,',','.');
                        }
                        if ( is_array($arTempDescontos) ) {
                            $nuTotalDescontos += $arTempDescontos["campo10"];
                            $arTempDescontos["campo7"] = number_format($arTempDescontos["campo7"],2,',','.');
                            $arTempDescontos["campo10"] = number_format($arTempDescontos["campo10"],2,',','.');
                        }
                        $arMerge = array_merge($arTempDescontos,$arTempProventos);
                        $arProventosDescontosTotal[] = $arMerge;
                    }

                    $arTotalProventosDescontosTotal['campo1'] = "";
                    $arTotalProventosDescontosTotal['campo2'] = "";
                    $arTotalProventosDescontosTotal['campo3'] = str_pad("TOTAL DE PROVENTOS", 70, ".");
                    $arTotalProventosDescontosTotal['campo4'] = ":";
                    $arTotalProventosDescontosTotal['campo5'] = number_format($nuTotalProventos,2,',','.');
                    $arTotalProventosDescontosTotal['campo6'] = "";
                    $arTotalProventosDescontosTotal['campo7'] = "";
                    $arTotalProventosDescontosTotal['campo8'] = str_pad("TOTAL DE DESCONTOS", 70, ".");
                    $arTotalProventosDescontosTotal['campo9'] = ":";
                    $arTotalProventosDescontosTotal['campo10']= number_format($nuTotalDescontos,2,',','.');
                    $arProventosDescontosTotal[]              = $arTotalProventosDescontosTotal;
                    $arTotalProventosDescontosTotal['campo1'] = "";
                    $arTotalProventosDescontosTotal['campo2'] = "";
                    $arTotalProventosDescontosTotal['campo3'] = str_pad("SALÁRIO LÍQUIDO", 70, ".");
                    $arTotalProventosDescontosTotal['campo4'] = ":";
                    $arTotalProventosDescontosTotal['campo5'] = number_format($nuTotalProventos - $nuTotalDescontos,2,',','.');
                    $arTotalProventosDescontosTotal['campo6'] = "";
                    $arTotalProventosDescontosTotal['campo7'] = "";
                    $arTotalProventosDescontosTotal['campo8'] = str_pad("No SERVIDORES", 70, ".");
                    $arTotalProventosDescontosTotal['campo9'] = ":";
                    $arTotalProventosDescontosTotal['campo10']= count($arCodContratos);
                    $arProventosDescontosTotal[]              = $arTotalProventosDescontosTotal;

                    $arTemp["campo1"] = "TOTAL GERAL";
                    $arTitulo4[]      = $arTemp;
                    $arPagina         = array();

                    $arTemp           = array();
                    $arTitulo5        = array();
                    $arTemp["campo1"] = "EVENTOS DE BASE";
                    $arTemp["campo2"] = "EVENTO INFORMATIVOS";
                    $arTitulo5[]      = $arTemp;

                    $arBasesTotais = is_array($arBasesTotais) ? $arBasesTotais : array();
                    $arInformativosTotais = is_array($arInformativosTotais) ? $arInformativosTotais : array();
                    $inCount = ( count($arBasesTotais) > count($arInformativosTotais) ) ? count($arBasesTotais) : count($arInformativosTotais);
                    for ($inIndex=0;$inIndex<$inCount;$inIndex++) {
                        $arTempBase        = array_shift($arBasesTotais);
                        $arTempInformativo = array_shift($arInformativosTotais);
                        if ( is_array($arTempBase) ) {
                            $arTempBase["campo3"] = number_format($arTempBase["campo3"],2,',','.');
                        }
                        if ( is_array($arTempInformativo) ) {
                            $arTempInformativo["campo6"] = number_format($arTempInformativo["campo6"],2,',','.');
                        }
                        $arMerge = array_merge($arTempBase,$arTempInformativo);
                        $arBasesInformativosTotal[] = $arMerge;
                    }

                    $arTemp           = array();
                    $arTitulo6        = array();
                    $arTemp["campo1"] = "OUTRAS FOLHAS";
                    $arTitulo6[]      = $arTemp;

                    $arTemp  = array();
                    $arBases = array();
                    $arTemp['campo1'] = "Base Previdência:";
                    $arTemp['campo2'] = number_format($nuValorBasePrevidenciaTotal,2,',','.');
                    $arTemp['campo3'] = "Base IRRF:";
                    $arTemp['campo4'] = number_format($nuValorBaseIRRFTotal,2,',','.');
                    $arTemp['campo5'] = "Base FGTS:";
                    $arTemp['campo6'] = number_format($nuValorBaseFGTSTotal,2,',','.');
                    $arBasesTotal[]   = $arTemp;

                    $arTemp = array();
                    if ( is_array($arComplementaresTotal) ) {
                        foreach ($arComplementaresTotal as $arComplementarTotal) {
                            $arComplementarTotal["campo3"] = number_format($arComplementarTotal["campo3"],2,',','.');
                            $arComplementarTotal["campo5"] = number_format($arComplementarTotal["campo5"],2,',','.');
                            $arComplementarTotal["campo7"] = number_format($arComplementarTotal["campo7"],2,',','.');
                            if ( is_float($arComplementarTotal["campo9"]) ) {
                                $arComplementarTotal["campo9"] = number_format($arComplementarTotal["campo9"],2,',','.');
                            }
                            $arTemp[] = $arComplementarTotal;
                        }
                    }
                    $arComplementaresTotal = $arTemp;

                    $arPagina['boTotais']                   = true;
                    $arPagina['linha1']                     = $arLinha1;
                    $arPagina['titulo4']                    = $arTitulo4;
                    $arPagina['proventos_descontos_total']  = $arProventosDescontosTotal;
                    $arPagina['bases_total']                = $arBasesTotal;
                    $arPagina['titulo5']                    = $arTitulo5;
                    $arPagina['bases_informativos_total']   = $arBasesInformativosTotal;
                    $arPagina['titulo6']                    = $arTitulo6;
                    $arPagina['complementares_total']       = $arComplementaresTotal;
                    $arRelatorio[]                          = $arPagina;
                }
            break;
        }
    }
    $rsRecordset->preenche( $arRelatorio );

    return $obErro;
}

function emitirApenasTotais($arContratos)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    $arTemp = array();
    foreach ($arContratos as $inIndex=>$arContrato) {
        if ( strpos($arContrato['lotacao'],"Total") === 0 or $arContrato['lotacao'] == "" ) {
            if ( strpos($arContrato['lotacao'],"CGM") ) {
                $arContrato['lotacao'] = "Total CGM: ".$arContratos[$inIndex-1]['cgm'];
            }
            if ( strpos($arContrato['lotacao'],"Lotação") ) {
                $inContador = $inIndex;
                $stLotacao  = "";
                do {
                    $inContador = $inContador - 1 ;
                    $stLotacao = $arContratos[$inContador]['lotacao'];
                    if ( strpos($arContratos[$inContador]['lotacao'],"Total") === 0 ) {
                        $stLotacao = "";
                    }
                } while ( $stLotacao == "" and $inContador > 0 );
                $arContrato['lotacao'] = "Total Lotação: ".$stLotacao;
            }

            $arTemp[] = $arContrato;
        }
    }

    return $arTemp;
}

function agruparPor($stCampo,$stRotulo,$arContratos)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    $arTemp2= array();
    $arOrdena = array();
    foreach ($arContratos as $arContrato) {
        $arOrdena[] = $arContrato[$stCampo];
    }
    $arOrdena = array_unique($arOrdena);
    foreach ($arOrdena as $stOrdena) {
        foreach ($arContratos as $arContrato) {
            if ($arContrato[$stCampo] == $stOrdena) {
                $nuTotalProventos   += (float) str_replace(",",".",str_replace(".","",$arContrato['proventos']));
                $nuTotalDescontos   += (float) str_replace(",",".",str_replace(".","",$arContrato['descontos']));
                $nuTotalPrevidencia += (float) str_replace(",",".",str_replace(".","",$arContrato['previdencia']));
                $nuTotalIrrf        += (float) str_replace(",",".",str_replace(".","",$arContrato['irrf']));
                $nuTotal            += (float) str_replace(",",".",str_replace(".","",$arContrato['total']));
                $arTemp2[] = $arContrato;
            }
        }
        if ($arFiltro['boEmitirTotais'] or $arFiltro['boEmitirRelatorio']) {
            $arPadrao['contrato']   = "";
            $arPadrao['cgm']        = "";
            $arPadrao['lotacao']    = "Total ".$stRotulo;
            $arPadrao['proventos']  = number_format ( $nuTotalProventos , 2, ",", ".");
            $arPadrao['descontos']  = number_format ( $nuTotalDescontos , 2, ",", ".");
            $arPadrao['previdencia']= number_format ( $nuTotalPrevidencia , 2, ",", ".");
            $arPadrao['irrf']       = number_format ( $nuTotalIrrf , 2, ",", ".");
            $arPadrao['total']      = number_format ( $nuTotal , 2, ",", ".");
            $arTemp2[]              = $arPadrao;
            $arPadrao['contrato']   = "";
            $arPadrao['cgm']        = "";
            $arPadrao['lotacao']    = "";
            $arPadrao['proventos']  = "";
            $arPadrao['descontos']  = "";
            $arPadrao['previdencia']= "";
            $arPadrao['irrf']       = "";
            $arPadrao['total']      = "";
            $arTemp2[]              = $arPadrao;
        }
        $nuTotalProventos   = 0;
        $nuTotalDescontos   = 0;
        $nuTotalPrevidencia = 0;
        $nuTotalIrrf        = 0;
        $nuTotal            = 0;
    }

    return $arTemp2;
}

function separarRelatorioResumida($stOpcao,&$boProcessar,$arRelatorio)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    if ($boProcessar) {
        $arPacote              = array();
        $arRelatorioProcessado = array();
        foreach ($arRelatorio as $arRelatorioTemp) {
            if (!$arRelatorioTemp['boTotais']) {
                $arPacote[] = $arRelatorioTemp;
            } else {
                if ( count($arPacote) ) {
                    $arPacotes = $this->agruparAnaliticaResumidaPor($stOpcao,$arPacote);
                    foreach ($arPacotes as $arPacote) {
                        $arRelatorioProcessado[] = $arPacote;
                    }
                    $arPacote = array();
                }
                $arRelatorioProcessado[] = $arRelatorioTemp;
            }
        }
        $arRelatorio = $arRelatorioProcessado;
    } else {
        $boProcessar = true;
        $arRelatorio = $this->agruparAnaliticaResumidaPor($stOpcao,$arRelatorio);
    }

    return $arRelatorio;
}

function separarRelatorio($stOpcao,&$boProcessar,$arRelatorio,$arLinha1)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    if ($boProcessar) {
        $arPacote              = array();
        $arRelatorioProcessado = array();
        foreach ($arRelatorio as $arRelatorioTemp) {
            if (!$arRelatorioTemp['boTotais']) {
                $arPacote[] = $arRelatorioTemp;
            } else {
                if ( count($arPacote) ) {
                    $arPacotes = $this->agruparAnaliticaPor($stOpcao,$arPacote,$arLinha1);
                    foreach ($arPacotes as $arPacote) {
                        $arRelatorioProcessado[] = $arPacote;
                    }
                    $arPacote = array();
                }
                $arRelatorioProcessado[] = $arRelatorioTemp;
            }
        }
        $arRelatorio = $arRelatorioProcessado;
    } else {
        $boProcessar = true;
        $arRelatorio = $this->agruparAnaliticaPor($stOpcao,$arRelatorio,$arLinha1);
    }

    return $arRelatorio;
}

function agruparAnaliticaResumidaPor($stRotulo,$arRelatorio)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    switch ($stRotulo) {
        case 'LOTAÇÃO':
            $stContrato     = "contrato2";
            $stCampo        = "campo1";
            $stOrdem        = $arFiltro['stAlfNumLotacao'];
            $inRotulo       = 0;
        break;
        case 'LOCAL':
            $stContrato     = "contrato2";
            $stCampo        = "campo2";
            $stOrdem        = $arFiltro['stAlfNumLocal'];
            $inRotulo       = 1;
        break;
        case 'REGIME DA FUNÇÃO':
            $stContrato     = "contrato1";
            $stCampo        = "campo3";
            $stOrdem        = $arFiltro['stAlfNumRegimedaFuncao'];
            $inRotulo       = 0;
        break;
        case 'FUNÇÃO':
            $stContrato     = "contrato1";
            $stCampo        = "campo4";
            $stOrdem        = $arFiltro['stAlfNumFuncao'];
            $inRotulo       = 1;
        break;
        case 'CGM':
            $stContrato     = "contrato1";
            $stCampo        = "campo2";
            $stOrdem        = $arFiltro['stAlfNumCgm'];
            $inRotulo       = 0;
        break;

    }
    $arAgrupamento = array();
    foreach ($arRelatorio as $arContrato) {
        if ( is_array($arContrato[$stContrato]) ) {
            $arCampo = explode("-",$arContrato[$stContrato][0][$stCampo]);
            $stDescricaoCampo = "";
            for ($inIndex=1;$inIndex<=count($arCampo);$inIndex++) {
                $stDescricaoCampo .= $arCampo[$inIndex]."-";
            }
            $stDescricaoCampo = substr($stDescricaoCampo,0,strlen($stDescricaoCampo)-2);
            $arAgrupamento[$arCampo[0]] = $stDescricaoCampo;
        }
    }
    if ($stOrdem == 'numérica') {
        ksort($arAgrupamento);
    } else {
        asort($arAgrupamento);
    }
    $arTemp = array();
    foreach ($arAgrupamento as $inCodigo=>$stDescricao) {
        if ($stDescricao != "") {
            $stValor = $inCodigo."-".$stDescricao;
        } else {
            $stValor = "";
        }
        $arProventos = array();
        $arDescontos = array();
        $arBasesInformativos     = array();
        $inContContratos = 0;
        for ($inRelatorio=0;$inRelatorio<count($arRelatorio);$inRelatorio++) {
            $arContrato = $arRelatorio[$inRelatorio];
            $stComparativo = $arContrato[$stContrato][0][$stCampo];
            if ($arContrato['boTotais'] == false and $stComparativo == $stValor) {
                if ($arFiltro['boEmitirTotais'] or $arFiltro['boEmitirRelatorio']) {
                    $inContContratos++;
                    foreach ($arContrato['eventos'] as $inEvento=>$arEvento) {
                        if ($arEvento['campo1'] != "") {
                            $nuValor = str_replace(".","",$arEvento['campo4']);
                            $nuValor = (float) str_replace(",",".",$nuValor);
                            $nuQuantidade = str_replace(".","",$arEvento['campo2']);
                            $nuQuantidade = (float) str_replace(",",".",$nuQuantidade);
                            $nuValor = $nuValor + $arProventos[$arEvento['campo1']][2];
                            $nuQuantidade = $nuQuantidade + $arProventos[$arEvento['campo1']][0];
                            $arProventos[$arEvento['campo1']] = array($nuQuantidade,
                                                                      $arEvento['campo3'],
                                                                      $nuValor);
                        }
                        if ($arEvento['campo5'] != "") {
                            $nuValor = str_replace(".","",$arEvento['campo8']);
                            $nuValor = (float) str_replace(",",".",$nuValor);
                            $nuQuantidade = str_replace(".","",$arEvento['campo6']);
                            $nuQuantidade = (float) str_replace(",",".",$nuQuantidade);
                            $nuValor = $nuValor + $arDescontos[$arEvento['campo5']][2];
                            $nuQuantidade = $nuQuantidade + $arDescontos[$arEvento['campo5']][0];
                            $arDescontos[$arEvento['campo5']] = array($nuQuantidade,
                                                                      $arEvento['campo7'],
                                                                      $nuValor);
                        }
                        if ($arEvento['campo9'] != "") {
                            $nuValor = str_replace(".","",$arEvento['campo11']);
                            $nuValor = (float) str_replace(",",".",$nuValor);
                            $nuValor = $nuValor + $arBasesInformativos[$arEvento['campo9']][1];
                            $arBasesInformativos[$arEvento['campo9']] = array($arEvento['campo10'],
                                                                              $nuValor);
                        }

                    }
                }
                $arTemp[] = $arContrato;
            }
            If( $arContrato['boTotais'] ){
                $arTemp[] = $this->emitirTotaisAgrupamentoResumida($stValor,$stRotulo,$arProventos,$arDescontos,$arBasesInformativos,$inContContratos);
                $arTemp[] = $arContrato;
                $arProventos = array();
                $arDescontos = array();
                $arBasesInformativos     = array();
                $boTotalJaExistente = true;
            }
        }
        if ( ($arFiltro['boEmitirTotais'] or $arFiltro['boEmitirRelatorio']) and !$boTotalJaExistente ) {
            $arTemp[] = $this->emitirTotaisAgrupamentoResumida($stValor,$stRotulo,$arProventos,$arDescontos,$arBasesInformativos,$inContContratos);
        }
    }

    return $arTemp;
}

function agruparAnaliticaPor($stRotulo,$arRelatorio,$arLinha1)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    switch ($stRotulo) {
        case 'LOTAÇÃO':
            $inIndexContrato = 2;
            $stCampo         = "campo2";
            $stOrdem         = $arFiltro['stAlfNumLotacao'];
        break;
        case 'LOCAL':
            $inIndexContrato = 3;
            $stCampo         = "campo2";
            $stOrdem         = $arFiltro['stAlfNumLocal'];
        break;
        case 'REGIME DO CARGO':
            $inIndexContrato = 0;
            $stCampo         = "campo4";
            $stOrdem         = $arFiltro['stAlfNumRegimedoCargo'];
        break;
        case 'SUBDIVISÃO DO CARGO':
            $inIndexContrato = 1;
            $stCampo         = "campo4";
            $stOrdem         = $arFiltro['stAlfNumSubdivisaodoCargo'];
        break;
        case 'CARGO':
            $inIndexContrato = 2;
            $stCampo         = "campo4";
            $stOrdem         = $arFiltro['stAlfNumCargo'];
        break;
        case 'ESPECIALIDADE DO CARGO':
            $inIndexContrato = 3;
            $stCampo         = "campo4";
            $stOrdem         = $arFiltro['stAlfNumEspecialidadedoCargo'];
        break;
        case 'REGIME DA FUNÇÃO':
            $inIndexContrato = 0;
            $stCampo         = "campo6";
            $stOrdem         = $arFiltro['stAlfNumRegimedaFuncao'];
        break;
        case 'SUBDIVISÃO DA FUNÇÃO':
            $inIndexContrato = 1;
            $stCampo         = "campo6";
            $stOrdem         = $arFiltro['stAlfNumSubdivisaodaFuncao'];
        break;
        case 'FUNÇÃO':
            $inIndexContrato = 2;
            $stCampo         = "campo6";
            $stOrdem         = $arFiltro['stAlfNumFuncao'];
        break;
        case 'ESPECIALIDADE DA FUNÇÃO':
            $inIndexContrato = 3;
            $stCampo         = "campo6";
            $stOrdem         = $arFiltro['stAlfNumEspecialidadedaFuncao'];
        break;
        case 'SITUAÇÃO':
            $inIndexContrato = 4;
            $stCampo         = "campo4";
            $stOrdem         = $arFiltro['stAlfNumSituacao'];
        break;
        case 'CGM':
            $inIndexContrato = 1;
            $stCampo         = "campo2";
            $stOrdem         = $arFiltro['stAlfNumCgm'];
        break;
    }
    $arAgrupamento = array();
    foreach ($arRelatorio as $arContrato) {
        if ( is_array($arContrato['contrato']) ) {
            $arCampo = explode("-",$arContrato['contrato'][$inIndexContrato][$stCampo]);
            $arAgrupamento[$arCampo[0]] = $arCampo[1];
        }
    }
    if ($stOrdem == 'numérica') {
        ksort($arAgrupamento);
    } else {
        asort($arAgrupamento);
    }
    $arTemp = array();
    foreach ($arAgrupamento as $inCodigo=>$stDescricao) {
        if ($stDescricao != "") {
            $stValor = $inCodigo."-".$stDescricao;
        } else {
            $stValor = "";
        }
        $arProventos = array();
        $arDescontos = array();
        $arBases     = array();
        $arInformativos = array();
        $arBasesTotal= array();
        $arComplementaresTotal = array();
        $inContContratos = 0;
        for ($inRelatorio=0;$inRelatorio<count($arRelatorio);$inRelatorio++) {
            $arContrato = $arRelatorio[$inRelatorio];
            if ($arContrato['boTotais'] == false and $arContrato['contrato'][$inIndexContrato][$stCampo] == $stValor) {
                if ($arFiltro['boEmitirTotais'] or $arFiltro['boEmitirRelatorio']) {
                    $inContContratos++;
                    $inUltimo    = count($arContrato['proventos_descontos'])-1;
                    $inPenultimo = count($arContrato['proventos_descontos'])-2;
                    foreach ($arContrato['proventos_descontos'] as $inProventosDescontos=>$arProventosDescontos) {
                        if ($inProventosDescontos != $inUltimo and
                            $inProventosDescontos != $inPenultimo and
                            $arProventosDescontos['campo1'] != "") {
                            $nuValor = str_replace(".","",$arProventosDescontos['campo4']);
                            $nuValor = (float) str_replace(",",".",$nuValor);
                            $nuQuantidade = str_replace(".","",$arProventosDescontos['campo2']);
                            $nuQuantidade = (float) str_replace(",",".",$nuQuantidade);
                            $nuValor = $nuValor + $arProventos[$arProventosDescontos['campo1']][2];
                            $nuQuantidade = $nuQuantidade + $arProventos[$arProventosDescontos['campo1']][0];
                            $arProventos[$arProventosDescontos['campo1']] = array($nuQuantidade,
                                                                                  $arProventosDescontos['campo3'],
                                                                                  $nuValor);
                        }
                        if ($inProventosDescontos != $inUltimo and
                            $inProventosDescontos != $inPenultimo and
                            $arProventosDescontos['campo5'] != "") {
                            $nuValor = str_replace(".","",$arProventosDescontos['campo8']);
                            $nuValor = (float) str_replace(",",".",$nuValor);
                            $nuQuantidade = str_replace(".","",$arProventosDescontos['campo6']);
                            $nuQuantidade = (float) str_replace(",",".",$nuQuantidade);
                            $nuValor = $nuValor + $arDescontos[$arProventosDescontos['campo5']][2];
                            $nuQuantidade = $nuQuantidade + $arDescontos[$arProventosDescontos['campo5']][0];
                            $arDescontos[$arProventosDescontos['campo5']] = array($nuQuantidade,
                                                                                  $arProventosDescontos['campo7'],
                                                                                  $nuValor);
                        }
                    }
                    foreach ($arContrato['bases'] as $arBasesTemp) {
                        $nuCampo2 = str_replace(".","",$arBasesTemp['campo2']);
                        $nuCampo2 = (float) str_replace(",",".",$nuCampo2);
                        $nuCampo4 = str_replace(".","",$arBasesTemp['campo4']);
                        $nuCampo4 = (float) str_replace(",",".",$nuCampo4);
                        $nuCampo6 = str_replace(".","",$arBasesTemp['campo6']);
                        $nuCampo6 = (float) str_replace(",",".",$nuCampo6);
                        $arBasesTotal[0]['campo1']  = $arBasesTemp['campo1'];
                        $arBasesTotal[0]['campo2'] += $nuCampo2;
                        $arBasesTotal[0]['campo3']  = $arBasesTemp['campo3'];
                        $arBasesTotal[0]['campo4'] += $nuCampo4;
                        $arBasesTotal[0]['campo5']  = $arBasesTemp['campo5'];
                        $arBasesTotal[0]['campo6'] += $nuCampo6;
                    }
                    foreach ($arContrato['bases_informativos'] as $inBasesInformativos=>$arBasesInformativos) {
                        if ($arBasesInformativos['campo1'] != "") {
                            $nuValor = str_replace(".","",$arBasesInformativos['campo3']);
                            $nuValor = (float) str_replace(",",".",$nuValor);
                            $nuValor = $nuValor + $arBases[$arBasesInformativos['campo1']][1];
                            $arBases[$arBasesInformativos['campo1']] = array($arBasesInformativos['campo2'],
                                                                             $nuValor);
                        }
                        if ($arBasesInformativos['campo4'] != "") {
                            $nuValor = str_replace(".","",$arBasesInformativos['campo6']);
                            $nuValor = (float) str_replace(",",".",$nuValor);
                            $nuValor = $nuValor + $arInformativos[$arBasesInformativos['campo4']][1];
                            $arInformativos[$arBasesInformativos['campo4']] = array( $arBasesInformativos['campo5'],
                                                                                     $nuValor);
                        }
                    }
                    foreach ($arContrato['complementares'] as $inComplementar=>$arComplementar) {
                        $nuCampo3 = str_replace(".","",$arComplementar['campo3']);
                        $nuCampo3 = (float) str_replace(",",".",$nuCampo3);
                        $nuCampo5 = str_replace(".","",$arComplementar['campo5']);
                        $nuCampo5 = (float) str_replace(",",".",$nuCampo5);
                        $nuCampo7 = str_replace(".","",$arComplementar['campo7']);
                        $nuCampo7 = (float) str_replace(",",".",$nuCampo7);
                        $nuCampo9 = str_replace(".","",$arComplementar['campo9']);
                        $nuCampo9 = (float) str_replace(",",".",$nuCampo9);
                        $arComplementaresTotal[$inComplementar]['campo1']  = $arComplementar['campo1'];
                        $arComplementaresTotal[$inComplementar]['campo2']  = $arComplementar['campo2'];
                        $arComplementaresTotal[$inComplementar]['campo3'] += $nuCampo3;
                        $arComplementaresTotal[$inComplementar]['campo4']  = $arComplementar['campo4'];
                        $arComplementaresTotal[$inComplementar]['campo5'] += $nuCampo5;
                        $arComplementaresTotal[$inComplementar]['campo6']  = $arComplementar['campo6'];
                        $arComplementaresTotal[$inComplementar]['campo7'] += $nuCampo7;
                        $arComplementaresTotal[$inComplementar]['campo8']  = $arComplementar['campo8'];
                        $arComplementaresTotal[$inComplementar]['campo9'] += $nuCampo9;
                    }
                }
                $arTemp[] = $arContrato;
            }
            If( $arContrato['boTotais'] ){
                $arTemp[] = $this->emitirTotaisAgrupamento($stValor,$stRotulo,$arLinha1,$arProventos,$arDescontos,$arBasesTotal,$arBases,$arInformativos,$arComplementaresTotal,$inContContratos);
                $arTemp[] = $arContrato;
                $arProventos = array();
                $arDescontos = array();
                $arBases     = array();
                $arInformativos = array();
                $arBasesTotal= array();
                $arComplementaresTotal = array();
                $inContContratos = 0;
                $boTotalJaExistente = true;
            }
        }

        if ( ($arFiltro['boEmitirTotais'] or $arFiltro['boEmitirRelatorio']) and !$boTotalJaExistente ) {
            $arTemp[] = $this->emitirTotaisAgrupamento($stValor,$stRotulo,$arLinha1,$arProventos,$arDescontos,$arBasesTotal,$arBases,$arInformativos,$arComplementaresTotal,$inContContratos);
        }
    }

    return $arTemp;
}

function emitirTotaisAgrupamentoResumida($stValor,$stRotulo,$arProventos=array(),$arDescontos=array(),$arBasesInformativos=array(),$inCountContratos=0)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    $arProventosTemp = array();
    $nuTotalProventos = 0;
    foreach ($arProventos as $inProvento=>$arProvento) {
        $nuTotalProventos += $arProvento[2];
        $inIndex = count($arProventosTemp);
        $arProventosTemp[$inIndex]['campo1'] = $inProvento;
        $arProventosTemp[$inIndex]['campo2'] = number_format($arProvento[0], 2, ",", ".");
        $arProventosTemp[$inIndex]['campo3'] = $arProvento[1];
        $arProventosTemp[$inIndex]['campo4'] = "";
        $arProventosTemp[$inIndex]['campo5'] = number_format($arProvento[2], 2, ",", ".");
    }
    $arProventos  = $arProventosTemp;
    $arDescontosTemp = array();
    $nuTotalDescontos = 0;
    foreach ($arDescontos as $inDesconto=>$arDesconto) {
        $nuTotalDescontos += $arDesconto[2];
        $inIndex = count($arDescontosTemp);
        $arDescontosTemp[$inIndex]['campo6'] = $inDesconto;
        $arDescontosTemp[$inIndex]['campo7'] = number_format($arDesconto[0], 2, ",", ".");
        $arDescontosTemp[$inIndex]['campo8'] = $arDesconto[1];
        $arDescontosTemp[$inIndex]['campo9'] = "";
        $arDescontosTemp[$inIndex]['campo10'] = number_format($arDesconto[2], 2, ",", ".");
    }
    $arDescontos  = $arDescontosTemp;
    $arBasesInformativosTemp = array();
    foreach ($arBasesInformativos as $inBaseInformetivo=>$arBaseInformativo) {
        $inIndex = count($arBasesInformativosTemp);
        $arBasesInformativosTemp[$inIndex]['campo11'] = $inBaseInformetivo;
        $arBasesInformativosTemp[$inIndex]['campo12'] = $arBaseInformativo[0];
        $arBasesInformativosTemp[$inIndex]['campo13'] = number_format($arBaseInformativo[1], 2, ",", ".");
    }
    $arBasesInformativos  = $arBasesInformativosTemp;
    $arEventos = array();
    $inCount = ( count($arProventos)             > count($arDescontos) ) ? count($arProventos)         : count($arDescontos);
    $inCount = ( count($arBasesInformativos)     > $inCount            ) ? count($arBasesInformativos) : $inCount;
    for ($inIndex=0;$inIndex<$inCount;$inIndex++) {
        $arMerge     = array_merge($arProventos[$inIndex],$arDescontos[$inIndex],$arBasesInformativos[$inIndex]);
        $arEventos[] = $arMerge;
    }

    $arTotalProventosDescontos['campo1'] = str_pad("TOTAL DE PROVENTOS", 70, ".");
    $arTotalProventosDescontos['campo2'] = ":";
    $arTotalProventosDescontos['campo3'] = number_format($nuTotalProventos,2,',','.');
    $arTotalProventosDescontos['campo4'] = str_pad("TOTAL DE DESCONTOS", 70, ".");
    $arTotalProventosDescontos['campo5'] = ":";
    $arTotalProventosDescontos['campo6'] = number_format($nuTotalDescontos,2,',','.');
    $arTotalProventosDescontos['campo7'] = str_pad("LÍQUIDO", 50, ".");
    $arTotalProventosDescontos['campo8'] = ":";
    $arTotalProventosDescontos['campo9'] = number_format($nuTotalProventos - $nuTotalDescontos,2,',','.');
    $arTotalGeralPor = array();
    $arTotalGeralPor[] = $arTotalProventosDescontos;

    $arTemp2            = array();
    $arTitulo4          = array();
    if ($stValor == "") {
        $stRotulo = $stRotulo.": SEM ".$stRotulo;
    } else {
        $stRotulo = $stRotulo.": ".$stValor;
    }
    $arTemp2["campo1"]  = "TOTAL POR ".$stRotulo;
    $arTitulo4[]        = $arTemp2;

    $arPagina['boTotais']                   = true;
    $arPagina['titulo4']                    = $arTitulo4;
    $arPagina['eventos']                    = $arEventos;
    $arPagina['total_geral_por']            = $arTotalGeralPor;
    $arPagina['total_servidor'][]           = array("campo1"=>"Total de Servidores:","campo2"=>$inCountContratos);

    return $arPagina;
}

function emitirTotaisAgrupamento($stValor,$stRotulo,$arLinha1,$arProventos,$arDescontos,$arBasesTotal,$arBases,$arInformativos,$arComplementaresTotal,$inContContratos)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    $arProventosTemp = array();
    $nuTotalProventos = 0;
    foreach ($arProventos as $inProvento=>$arProvento) {
        $nuTotalProventos += $arProvento[2];
        $inIndex = count($arProventosTemp);
        $arProventosTemp[$inIndex]['campo1'] = $inProvento;
        $arProventosTemp[$inIndex]['campo2'] = number_format($arProvento[0], 2, ",", ".");
        $arProventosTemp[$inIndex]['campo3'] = $arProvento[1];
        $arProventosTemp[$inIndex]['campo4'] = "";
        $arProventosTemp[$inIndex]['campo5'] = number_format($arProvento[2], 2, ",", ".");
    }
    $arProventos  = $arProventosTemp;
    $arDescontosTemp = array();
    $nuTotalDescontos = 0;
    foreach ($arDescontos as $inDesconto=>$arDesconto) {
        $nuTotalDescontos += $arDesconto[2];
        $inIndex = count($arDescontosTemp);
        $arDescontosTemp[$inIndex]['campo6'] = $inDesconto;
        $arDescontosTemp[$inIndex]['campo7'] = number_format($arDesconto[0], 2, ",", ".");
        $arDescontosTemp[$inIndex]['campo8'] = $arDesconto[1];
        $arDescontosTemp[$inIndex]['campo9'] = "";
        $arDescontosTemp[$inIndex]['campo10'] = number_format($arDesconto[2], 2, ",", ".");
    }
    $arDescontos  = $arDescontosTemp;
    $arProventosDescontosTotal = array();
    $inCount = ( count($arProventos) > count($arDescontos) ) ? count($arProventos) : count($arDescontos);
    for ($inIndex=0;$inIndex<$inCount;$inIndex++) {
        $arMerge = array_merge($arProventos[$inIndex],$arDescontos[$inIndex]);
        $arProventosDescontosTotal[] = $arMerge;
    }

    $arTotalProventosDescontosTotal['campo1'] = "";
    $arTotalProventosDescontosTotal['campo2'] = "";
    $arTotalProventosDescontosTotal['campo3'] = str_pad("TOTAL DE PROVENTOS", 70, ".");
    $arTotalProventosDescontosTotal['campo4'] = ":";
    $arTotalProventosDescontosTotal['campo5'] = number_format($nuTotalProventos,2,',','.');
    $arTotalProventosDescontosTotal['campo6'] = "";
    $arTotalProventosDescontosTotal['campo7'] = "";
    $arTotalProventosDescontosTotal['campo8'] = str_pad("TOTAL DE DESCONTOS", 70, ".");
    $arTotalProventosDescontosTotal['campo9'] = ":";
    $arTotalProventosDescontosTotal['campo10']= number_format($nuTotalDescontos,2,',','.');
    $arProventosDescontosTotal[]              = $arTotalProventosDescontosTotal;
    $arTotalProventosDescontosTotal['campo1'] = "";
    $arTotalProventosDescontosTotal['campo2'] = "";
    $arTotalProventosDescontosTotal['campo3'] = str_pad("SALÁRIO LÍQUIDO", 70, ".");
    $arTotalProventosDescontosTotal['campo4'] = ":";
    $arTotalProventosDescontosTotal['campo5'] = number_format($nuTotalProventos - $nuTotalDescontos,2,',','.');
    $arTotalProventosDescontosTotal['campo6'] = "";
    $arTotalProventosDescontosTotal['campo7'] = "";
    $arTotalProventosDescontosTotal['campo8'] = str_pad("No SERVIDORES", 70, ".");
    $arTotalProventosDescontosTotal['campo9'] = ":";
    $arTotalProventosDescontosTotal['campo10']= $inContContratos;
    $arProventosDescontosTotal[]              = $arTotalProventosDescontosTotal;

    $arBasesTotal[0]['campo2'] = number_format($arBasesTotal[0]['campo2'],2,',','.');
    $arBasesTotal[0]['campo4'] = number_format($arBasesTotal[0]['campo4'],2,',','.');
    $arBasesTotal[0]['campo6'] = number_format($arBasesTotal[0]['campo6'],2,',','.');

    $arBasesTemp = array();
    foreach ($arBases as $inBase=>$arBase) {
        $inIndex = count($arBasesTemp);
        $arBasesTemp[$inIndex]['campo1'] = $inBase;
        $arBasesTemp[$inIndex]['campo2'] = $arBase[0];
        $arBasesTemp[$inIndex]['campo3'] = number_format($arBase[1], 2, ",", ".");
    }
    $arBases  = $arBasesTemp;
    $arInformativosTemp = array();
    foreach ($arInformativos as $inInformativo=>$arInformativo) {
        $inIndex = count($arInformativosTemp);
        $arInformativosTemp[$inIndex]['campo4'] = $inInformativo;
        $arInformativosTemp[$inIndex]['campo5'] = $arInformativo[0];
        $arInformativosTemp[$inIndex]['campo6'] = number_format($arInformativo[1], 2, ",", ".");
    }
    $arInformativos  = $arInformativosTemp;
    $arBasesInformativosTotal = array();
    $inCount = ( count($arBases) > count($arInformativos) ) ? count($arBases) : count($arInformativos);
    for ($inIndex=0;$inIndex<$inCount;$inIndex++) {
        $arMerge = array_merge($arBases[$inIndex],$arInformativos[$inIndex]);
        $arBasesInformativosTotal[] = $arMerge;
    }

    foreach ($arComplementaresTotal as $inComplementar=>$arComplementar) {
        $arComplementaresTotal[$inComplementar]['campo3'] = number_format($arComplementar['campo3'], 2, ",", ".");
        $arComplementaresTotal[$inComplementar]['campo5'] = number_format($arComplementar['campo5'], 2, ",", ".");
        $arComplementaresTotal[$inComplementar]['campo7'] = number_format($arComplementar['campo7'], 2, ",", ".");
        if ( is_float($arComplementar['campo9']) ) {
            $arComplementaresTotal[$inComplementar]['campo9'] = number_format($arComplementar['campo9'], 2, ",", ".");
        }
    }

    $arTemp2            = array();
    $arTitulo4          = array();
    if ($stValor == "") {
        $stRotulo = $stRotulo.": SEM ".$stRotulo;
    } else {
        $stRotulo = $stRotulo.": ".$stValor;
    }
    $arTemp2["campo1"]  = "TOTAL POR ".$stRotulo;
    $arTitulo4[]        = $arTemp2;

    $arTemp2            = array();
    $arTitulo5          = array();
    $arTemp2["campo1"]  = "EVENTOS DE BASE";
    $arTemp2["campo2"]  = "EVENTO INFORMATIVOS";
    $arTitulo5[]        = $arTemp2;

    $arTemp2           = array();
    $arTitulo6         = array();
    $arTemp2["campo1"] = "OUTRAS FOLHAS";
    $arTitulo6[]       = $arTemp2;

    $arPagina['boTotais'] = true;
    $arPagina['linha1']                     = $arLinha1;
    $arPagina['titulo4']                    = $arTitulo4;
    $arPagina['proventos_descontos_total']  = $arProventosDescontosTotal;
    $arPagina['bases_total']                = $arBasesTotal;
    $arPagina['titulo5']                    = $arTitulo5;
    $arPagina['bases_informativos_total']   = $arBasesInformativosTotal;
    $arPagina['titulo6']                    = $arTitulo6;
    $arPagina['complementares_total']       = $arComplementaresTotal;
    //$arTemp[]           = $arPagina;
    return $arPagina;
}

function emitirSomenteTotais($arRelatorio)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    $arTemp = array();
    foreach ($arRelatorio  as $arContrato) {
        if ($arContrato['boTotais']) {
            $arTemp[] = $arContrato;
        }
    }

    return $arTemp;
}

function processaFiltro($arFiltros)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    if ( isset($arFiltros['cod_periodo_movimentacao']) ) {
        $stFiltro .= " AND contrato_servidor_periodo.cod_periodo_movimentacao = ".$arFiltros['cod_periodo_movimentacao'];
    }
    if ( isset($arFiltros['boAtivo']) ) {
        $stFiltro .= " AND ativo = true";
    }
    if ( isset($arFiltros['boInativo']) ) {
        $stFiltro .= " AND ativo = false";
    }
    if ( is_array($arFiltros['arRegistros']) ) {
        $stRegistros = "";
        foreach ($arFiltros['arRegistros'] as $inRegistros) {
            $stRegistros .= $inRegistros.",";
        }
        $stRegistros = substr($stRegistros,0,strlen($stRegistros)-1);
        $stFiltro .= " AND registro IN (".$stRegistros.")";
    }
    if ( is_array($arFiltros['arEspecialidades']) ) {
        $stEspecialidades = "";
        foreach ($arFiltros['arEspecialidades'] as $inEspecialidade) {
            $stEspecialidades .= $inEspecialidade.",";
        }
        $stEspecialidades = substr($stEspecialidades,0,strlen($stEspecialidades)-1);
        $stFiltro .= " AND contrato_servidor_especialidade_cargo.cod_especialidade IN (".$stEspecialidades.")";
    }
    if ( is_array($arFiltros['arCargos']) ) {
        $stCargos = "";
        foreach ($arFiltros['arCargos'] as $inCargo) {
            $stCargos .= $inCargo.",";
        }
        $stCargos = substr($stCargos,0,strlen($stCargos)-1);
        $stFiltro .= " AND contrato_servidor_especialidade_cargo.cod_cargo IN (".$stCargos.")";
    }
    if ( is_array($arFiltros['arFuncoes']) ) {
        $stCargos = "";
        foreach ($arFiltros['arFuncoes'] as $inCargo) {
            $stCargos .= $inCargo.",";
        }
        $stCargos = substr($stCargos,0,strlen($stCargos)-1);
        $stFiltro .= " AND contrato_servidor_especialidade_funcao.cod_funcao IN (".$stCargos.")";
    }
    if ( is_array($arFiltros['arEspecialidadesFunc']) ) {
        $stEspecialidades = "";
        foreach ($arFiltros['arEspecialidadesFunc'] as $inEspecialidade) {
            $stEspecialidades .= $inEspecialidade.",";
        }
        $stEspecialidades = substr($stEspecialidades,0,strlen($stEspecialidades)-1);
        $stFiltro .= " AND contrato_servidor_especialidade_funcao.cod_especialidade_funcao IN (".$stEspecialidades.")";
    }
    if ( is_array($arFiltros['arPadrao']) ) {
        $stPadrao = "";
        foreach ($arFiltros['arPadrao'] as $inPadrao) {
            $stPadrao .= $inPadrao.",";
        }
        $stPadrao = substr($stPadrao,0,strlen($stPadrao)-1);
        $stFiltro .= " AND cod_padrao IN (".$stPadrao.")";
    }
    if ( is_array($arFiltros['arLotacao']) ) {
        $stLotacao = "";
        foreach ($arFiltros['arLotacao'] as $inLotacao) {
            $stLotacao .= $inLotacao.",";
        }
        $stLotacao = substr($stLotacao,0,strlen($stLotacao)-1);
        $stFiltro .= " AND cod_orgao IN (".$stLotacao.")";
    }
    if ( is_array($arFiltros['arLocal']) ) {
        $stLocal = "";
        foreach ($arFiltros['arLocal'] as $inLocal) {
            $stLocal .= $inLocal.",";
        }
        $stLocal = substr($stLocal,0,strlen($stLocal)-1);
        $stFiltro .= " AND cod_local IN (".$stLocal.")";
    }

    return $stFiltro;
}

}
