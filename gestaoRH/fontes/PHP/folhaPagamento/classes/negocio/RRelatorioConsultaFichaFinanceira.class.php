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
    * Classe de regra de relatório para Consulta Ficha Financeira
    * Data de Criação: 14/12/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra de Relatório

    $Revision: 30896 $
    $Name$
    $Author: souzadl $
    $Date: 2007-11-20 13:30:07 -0200 (Ter, 20 Nov 2007) $

    * Casos de uso: uc-04.05.41
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                                );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalculoFolhaPagamento.class.php"                     );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalculoFolhaComplementar.class.php"                  );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

class RRelatorioConsultaFichaFinanceira extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRFolhaPagamentoCalculoFolhaPagamento;
/**
    * @var Object
    * @access Private
*/
var $obRFolhaPagamentoCalculoFolhaComplementar;

/**
     * @access Public
     * @param Object $valor
*/
function setRFolhaPagamentoCalculoFolhaPagamento($valor) { $this->obRFolhaPagamentoCalculoFolhaPagamento = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRFolhaPagamentoCalculoFolhaComplementar($valor) { $this->obRFolhaPagamentoCalculoFolhaComplementar = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getRFolhaPagamentoCalculoFolhaPagamento() { return $this->obRFolhaPagamentoCalculoFolhaPagamento;           }
/**
     * @access Public
     * @param Object $valor
*/
function getRFolhaPagamentoCalculoFolhaComplementar() { return $this->obRFolhaPagamentoCalculoFolhaComplementar;           }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioConsultaFichaFinanceira()
{
    $this->setRFolhaPagamentoCalculoFolhaPagamento( new RFolhaPagamentoCalculoFolhaPagamento() );
    $this->obRFolhaPagamentoCalculoFolhaPagamento->setRORFolhaPagamentoPeriodoMovimentacao( new RFolhaPagamentoPeriodoMovimentacao );
    $this->setRFolhaPagamentoCalculoFolhaComplementar( new RFolhaPagamentoCalculoFolhaComplementar( new RFolhaPagamentoFolhaComplementar( new RFolhaPagamentoPeriodoMovimentacao ) ) );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordset)
{
    $arFiltro = Sessao::read("filtroRelatorio")  ;

    include_once(CAM_GRH_PES_NEGOCIO."RPessoalContrato.class.php");
    $obRPessoalContrato = new RPessoalContrato();
    $obRPessoalContrato->listarCgmDoRegistro($rsCgmPrev,$arFiltro['inContrato']);

    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorPrevidencia.class.php");
        $obTPessoalContratoServidorPrevidencia = new TPessoalContratoServidorPrevidencia();
    $stFiltro  = " AND contrato_servidor_previdencia.cod_contrato = ".$rsCgmPrev->getCampo("cod_contrato");
    $stFiltro .= " AND previdencia_previdencia.tipo_previdencia = 'o'";
    $stFiltro .= " AND bo_excluido is false";
    $obTPessoalContratoServidorPrevidencia->recuperaPrevidencias($rsPrevidencia,$stFiltro);
    $inCodPrevidencia = ($rsPrevidencia->getNumLinhas() == 1) ? $rsPrevidencia->getCampo("cod_previdencia") : 0;

    if ($arFiltro['boFiltrarFolhaComplementar']) {
        $stMes = ( strlen($arFiltro['inCodMes']) == 1 ) ? '0'.$arFiltro['inCodMes'] : $arFiltro['inCodMes'];
        $this->obRFolhaPagamentoCalculoFolhaComplementar->roRFolhaPagamentoFolhaComplementar->setCodComplementar($arFiltro['inCodComplementar']);
        $this->obRFolhaPagamentoCalculoFolhaComplementar->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->setDtFinal($stMes."/".$arFiltro['inAno']);
        $this->obRFolhaPagamentoCalculoFolhaComplementar->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);

        $this->obRFolhaPagamentoCalculoFolhaComplementar->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoPeriodoContratoServidor();
        $this->obRFolhaPagamentoCalculoFolhaComplementar->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->setRegistro($arFiltro['inContrato']);
        $this->obRFolhaPagamentoCalculoFolhaComplementar->listarEventoFichaFinanceira($rsEventoCalculado,$boTransacao);
        $this->obRFolhaPagamentoCalculoFolhaComplementar->roRFolhaPagamentoFolhaComplementar->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->listarCgmDoRegistro($rsCgm,$arFiltro['inContrato']);
        $this->obRFolhaPagamentoCalculoFolhaComplementar->listarEventosBaseDescontoRelatorioFichaFinanceira($rsEventosBase,$rsCgm->getCampo('numcgm'),'B',$rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'), $inCodPrevidencia);
        $this->obRFolhaPagamentoCalculoFolhaComplementar->listarEventosBaseDescontoRelatorioFichaFinanceira($rsEventosDesconto,$rsCgm->getCampo('numcgm'),'D',$rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'), $inCodPrevidencia);

    } else {
        $rsEventoCalculado = new RecordSet;
        $rsEventosBase     = new RecordSet;
        $rsEventosDesconto = new RecordSet;
        switch ($arFiltro['inCodConfiguracao']) {
            case 1:
                $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoPeriodoContratoServidor();
                $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);

                if ( isset($arFiltro['inContrato']) ) {
                    $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->setRegistro( $arFiltro['inContrato'] );
                }
                if ( isset($arFiltro['inCodConfiguracao']) ) {
                    $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->addRFolhaPagamentoRegistroEvento();
                    $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->addConfiguracaoEvento();
                    $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao($arFiltro['inCodConfiguracao']);
                }
                $stDtFinal  = ( strlen($arFiltro['inCodMes']) == 1 )? '0'.$arFiltro['inCodMes'] : $arFiltro['inCodMes'];
                $stDtFinal .= "/".$arFiltro['inAno'];
                $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->setDtFinal($stDtFinal);
                $stOrdem = $arFiltro['stOrdenacao'];
                $this->obRFolhaPagamentoCalculoFolhaPagamento->listarEventoRelatorioFichaFinanceira($rsEventoCalculado,$stOrdem,$boTransacao);
                $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->listarCgmDoRegistro($rsCgm,$arFiltro['inContrato']);
                $this->obRFolhaPagamentoCalculoFolhaPagamento->listarEventosBaseDescontoRelatorioFichaFinanceira($rsEventosBase,$rsCgm->getCampo('numcgm'),'B',$rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'),$inCodPrevidencia);
                $this->obRFolhaPagamentoCalculoFolhaPagamento->listarEventosBaseDescontoRelatorioFichaFinanceira($rsEventosDesconto,$rsCgm->getCampo('numcgm'),'D',$rsUltimaMovimentacao->getCampo('cod_periodo_movimentacao'),$inCodPrevidencia);
            break;
            case 2:
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php");
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
                $obTFolhaPagamentoEventoFeriasCalculado = new TFolhaPagamentoEventoFeriasCalculado;
                $obTFolhaPagamentoPeriodoMovimentacao   = new TFolhaPagamentoPeriodoMovimentacao;
                $obTPessoalContrato                     = new TPessoalContrato;
                $stFiltro = " WHERE registro = ".$arFiltro['inContrato'];
                $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
                $stDtFinal  = ( strlen($arFiltro['inCodMes']) == 1 )? '0'.$arFiltro['inCodMes'] : $arFiltro['inCodMes'];
                $stDtFinal .= "/".$arFiltro['inAno'];
                $stFiltro = " WHERE to_char(dt_final,'mm/yyyy') = '".$stDtFinal."'";
                $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro);
                $stFiltro  = " AND cod_contrato = ".$rsContrato->getCampo("cod_contrato");
                $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $obTFolhaPagamentoEventoFeriasCalculado->recuperaConsultaFichaFinanceira($rsEventoCalculado,$stFiltro);

                $stFiltro = " AND contrato.registro = ".$arFiltro['inContrato'];
                $obTPessoalContrato->recuperaCgmDoRegistro($rsCgm,$stFiltro);

                $this->obRFolhaPagamentoCalculoFolhaPagamento->listarEventosBaseDescontoRelatorioFichaFinanceira($rsEventosBase,$rsCgm->getCampo('numcgm'),'B',$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'),$inCodPrevidencia);
                $this->obRFolhaPagamentoCalculoFolhaPagamento->listarEventosBaseDescontoRelatorioFichaFinanceira($rsEventosDesconto,$rsCgm->getCampo('numcgm'),'D',$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'),$inCodPrevidencia);

            break;
            case 3:
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php");
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
                $obTFolhaPagamentoEventoDecimoCalculado = new TFolhaPagamentoEventoDecimoCalculado;
                $obTFolhaPagamentoPeriodoMovimentacao   = new TFolhaPagamentoPeriodoMovimentacao;
                $obTPessoalContrato                     = new TPessoalContrato;
                $stFiltro = " WHERE registro = ".$arFiltro['inContrato'];
                $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
                $stDtFinal  = ( strlen($arFiltro['inCodMes']) == 1 )? '0'.$arFiltro['inCodMes'] : $arFiltro['inCodMes'];
                $stDtFinal .= "/".$arFiltro['inAno'];
                $stFiltro = " WHERE to_char(dt_final,'mm/yyyy') = '".$stDtFinal."'";
                $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro);
                $stFiltro  = " AND cod_contrato = ".$rsContrato->getCampo("cod_contrato");
                $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $obTFolhaPagamentoEventoDecimoCalculado->recuperaConsultaFichaFinanceira($rsEventoCalculado,$stFiltro);
            break;
            case 4:
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php");
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
                $obTFolhaPagamentoEventoRescisaoCalculado = new TFolhaPagamentoEventoRescisaoCalculado;
                $obTFolhaPagamentoPeriodoMovimentacao   = new TFolhaPagamentoPeriodoMovimentacao;
                $obTPessoalContrato                     = new TPessoalContrato;
                $stFiltro = " WHERE registro = ".$arFiltro['inContrato'];
                $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
                $stDtFinal  = ( strlen($arFiltro['inCodMes']) == 1 )? '0'.$arFiltro['inCodMes'] : $arFiltro['inCodMes'];
                $stDtFinal .= "/".$arFiltro['inAno'];
                $stFiltro = " WHERE to_char(dt_final,'mm/yyyy') = '".$stDtFinal."'";
                $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro);
                $stFiltro  = " AND cod_contrato = ".$rsContrato->getCampo("cod_contrato");
                $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $obTFolhaPagamentoEventoRescisaoCalculado->recuperaConsultaFichaFinanceira($rsEventoCalculado,$stFiltro);
                break;
        }
    }
    $arEventos                      = array();
    $arEventosBase                  = array();
    $arContrato                     = array();
    $arRecordSet                    = array();
    $arTempEvento                   = array();
    $inCount                        = 0;
    $inCountVariavel                = 0;
    $inCountProporcional            = 0;
    $nuSomaProventos                = 0;
    $nuSomaDescontos                = 0;
    $rsEventoCalculado->addFormatacao("quantidade","NUMERIC_BR");
    $rsEventoCalculado = $this->processarEventos($rsEventoCalculado);

    while ( !$rsEventoCalculado->eof() ) {
        $rsEventoCalculado->proximo();
        $inCodContratoProx = $rsEventoCalculado->getCampo('cod_contrato');
        $rsEventoCalculado->anterior();

        $arTempEvento['evento']                     = $rsEventoCalculado->getCampo('codigo');
        $arTempEvento['descricao']                  = $rsEventoCalculado->getCampo('descricao');
        $arTempEvento['desdobramento_texto']        = $rsEventoCalculado->getCampo('desdobramento_texto');
        $arTempEvento['quantidade']                 = $rsEventoCalculado->getCampo('quantidade');
        $arTempEvento['proporcional']               = $rsEventoCalculado->getCampo('proporcional');
        if ( $rsEventoCalculado->getCampo('proventos_descontos') == 'proventos' ) {
            $nuProventos = number_format($rsEventoCalculado->getCampo('valor'),2,',','.');
            $nuDescontos = '0,00';
        } else {
            $nuDescontos = number_format($rsEventoCalculado->getCampo('valor'),2,',','.');
            $nuProventos = '0,00';
        }
        $arTempEvento['proventos']                  = $nuProventos;
        $arTempEvento['descontos']                  = $nuDescontos;
        if ( $rsEventoCalculado->getCampo('natureza') == 'P' ) {
            $nuProventos = str_replace('.', '', $nuProventos);
            $nuProventos = str_replace(',', '.', $nuProventos);
            $nuSomaProventos += (float) $nuProventos;
        }
        if ( $rsEventoCalculado->getCampo('natureza') == 'D' ) {
            $nuDescontos = str_replace('.', '', $nuDescontos);
            $nuDescontos = str_replace(',', '.',$nuDescontos);
            $nuSomaDescontos += $nuDescontos;
        }
        if ( $rsEventoCalculado->getCampo('natureza') != 'B' and $rsEventoCalculado->getCampo('natureza') != 'I' ) {
            $arEventos[$inCount] = $arTempEvento;
        } else {
            $arEventosBase[count($arEventosBase)] = $arTempEvento;
        }
        $inCount++;
        if ( $inCodContratoProx != $rsEventoCalculado->getCampo('cod_contrato') ) {
//            $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoPeriodoContratoServidor();
//            $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->setCodContrato($rsEventoCalculado->getCampo('cod_contrato'));
//            $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->listarContratosServidorResumido($rsContratoServidor);
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
            $stFiltro = " AND contrato.cod_contrato = ".$rsEventoCalculado->getCampo('cod_contrato');
            $obTFolhaPagamentoEvento->recuperaInformacoesParaRelatorioRegistroEvento($rsCGM,$stFiltro);
            $arContrato[0]['campo1'] = 'Matrícula: ';
            $arContrato[0]['campo2'] = $rsCGM->getCampo('registro');
            $arContrato[1]['campo1'] = 'CGM: ';
            $arContrato[1]['campo2'] = $rsCGM->getCampo('numcgm')." - ". $rsCGM->getCampo('nom_cgm');

            $stCargo  = $rsCGM->getCampo('desc_cargo');
            $stCargo .= ( $rsCGM->getCampo('desc_especialidade_cargo') != "" ) ? " - ".$rsCGM->getCampo('desc_especialidade_cargo') : "";
            $stFuncao = $rsCGM->getCampo('desc_funcao');
            $stFuncao.= ( $rsCGM->getCampo('desc_especialidade_funcao') != "" )? " - ".$rsCGM->getCampo('desc_especialidade_funcao') : "";
            $arContrato[2]['campo1'] = 'Cargo/Função: ';
            $arContrato[2]['campo2'] = $stCargo." / ".$stFuncao;

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php");
            $obTPessoalContratoServidorOrgao = new TPessoalContratoServidorOrgao();
            $stFiltro = " AND contrato.cod_contrato = ".$rsEventoCalculado->getCampo('cod_contrato');
            $obTPessoalContratoServidorOrgao->recuperaRelacionamento($rsOrgao,$stFiltro);
            $arContrato[3]['campo1'] = 'Lotação: ';
            $arContrato[3]['campo2'] = $rsOrgao->getCampo("cod_orgao")." - ".$rsOrgao->getCampo("descricao");//$rsCGM->getCampo('cod_estrutural')." - ".$rsCGM->getCampo('desc_local');

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php");
            $obTPessoalContratoServidorLocal = new TPessoalContratoServidorLocal();
            $stFiltro = " AND cont_local.cod_contrato = ".$rsEventoCalculado->getCampo('cod_contrato')."  \n";
            $obTPessoalContratoServidorLocal->recuperaRelacionamento($rsLocal,$stFiltro);
            $stLocal =( $rsLocal->getCampo('cod_local') != "" ) ? $rsLocal->getCampo('cod_local')." - ".$rsLocal->getCampo('descricao'): "";
            $arContrato[4]['campo1'] = 'Local: ';
            $arContrato[4]['campo2'] = $stLocal;

            $arVazio['evento']     = '';
            $arVazio['descricao']  = '';
            $arVazio['quantidade'] = '';
            $arVazio['proventos']  = '';
            $arVazio['descontos']  = '';
            $arEventos[] = $arVazio;
            foreach ($arEventosBase as $arEventoBase) {
                $arEventos[] = $arEventoBase;
            }

            $arEventos[] = $arVazio;
            $arTotais['evento']     = '';
            $arTotais['descricao']  = 'Soma dos Proventos';
            $arTotais['quantidade'] = '';
            $arTotais['proventos']  = number_format($nuSomaProventos,2,',','.');
            $arTotais['descontos']  = '';
            $arEventos[] = $arTotais;
            $arTotais['evento']     = '';
            $arTotais['descricao']  = 'Soma dos Descontos';
            $arTotais['quantidade'] = '';
            $arTotais['proventos']  = '';
            $arTotais['descontos']  = number_format($nuSomaDescontos,2,',','.');
            $arEventos[] = $arTotais;
            $arTotais['evento']     = '';
            $arTotais['descricao']  = 'Líquido';
            $arTotais['quantidade'] = '';
            $arTotais['proventos']  = number_format($nuSomaProventos - $nuSomaDescontos,2,',','.');
            $arTotais['descontos']  = '';
            $arEventos[] = $arTotais;
            $arTemp                  = array();
            foreach ($arEventos as $arEvento) {
                $boIncluir = true;
                foreach ($arTemp as $arEventoTemp) {
                    if ($arEvento['evento'] == $arEventoTemp['evento']
                    and $arEvento['evento'] != ""
                    and $arEventoTemp['evento'] != ""
                    and $arEventoTemp['proporcional'] != $arEvento['proporcional']) {
                        $boIncluir = false;
                        break;
                    }
                }
                if ($boIncluir) {
                    $arTemp[] = $arEvento;
                }
            }

            $arEventosBase = (is_array($rsEventosBase->getElementos())) ? $rsEventosBase->getElementos() : array();

            foreach ($arEventosBase as $arEventoBase) {
                if ( !($arEventoBase['registro'] > $arFiltro['inContrato'] and $arEventoBase['cod_complementar'] == $arFiltro['inCodComplementar'] ) ) {
                    $arValoresEventosBase[$arEventoBase['codigo']]['valor']     += $arEventoBase['valor'];
                    $arValoresEventosBase[$arEventoBase['codigo']]['codigo']     = $arEventoBase['codigo'];
                    $arValoresEventosBase[$arEventoBase['codigo']]['descricao']  = $arEventoBase['descricao'];
                    $arRegistrosBase[] = $arEventoBase['registro']."#".$arEventoBase['cod_complementar'];
                }
            }
            $obRPessoalContrato = new RPessoalContrato;
            $obRPessoalContrato->setRegistro($arFiltro['inContrato']);
            $obRPessoalContrato->consultarContrato();

            $obRFolhaPagamentoPrevidencia = new RFolhaPagamentoPrevidencia;
            $arFiltros['inCodContrato']    = $obRPessoalContrato->getCodContrato();
            $arFiltros['stTipoPrevidencia']= 'o';
            $obRFolhaPagamentoPrevidencia->listarFaixasDescontosPrevidencias($rsFaixasDescontos,$arFiltros,'');
            $inCodPrevidencia = $rsFaixasDescontos->getCampo('cod_previdencia');
            $obRFolhaPagamentoPrevidencia->setCodPrevidencia($inCodPrevidencia);
            $obRFolhaPagamentoPrevidencia->listarPrevidenciaEvento($rsPrevidenciaEvento,$boTransacao,2);

            if ( is_array($arValoresEventosBase ) ) {

                foreach ($arValoresEventosBase as $arValorEventoBase) {

//                    if ( $arValorEventoBase['codigo'] == $rsPrevidenciaEvento->getCampo('codigo') ) {
//                        $flValorEncontrato = 0;
//                        $flMaiorValor      = 0;
//                        while (!$rsFaixasDescontos->eof()) {
//                            if( $rsFaixasDescontos->getCampo('valor_inicial') <= $arValorEventoBase['valor'] and
//                                $rsFaixasDescontos->getCampo('valor_final')   >= $arValorEventoBase['valor'] ){
//                                $flValorEncontrato = $arValorEventoBase['valor'];
//                            }
//                            if ( $rsFaixasDescontos->getCampo('valor_final') > $flMaiorValor ) {
//                                $flMaiorValor = $rsFaixasDescontos->getCampo('valor_final');
//                            }
//                            $rsFaixasDescontos->proximo();
//                        }
//                        if ($flValorEncontrato == 0) {
//                            $arValorEventoBase['valor'] = $flMaiorValor;
//                        }
//                    }

                    $arValorEventoBase['valor'] = number_format($arValorEventoBase['valor'],2,',','.');
                    $arBases[] = $arValorEventoBase;
                }
            }
            if ( is_array($arRegistrosBase) ) {
                $arRegistrosBase = array_unique($arRegistrosBase);
                foreach ($arRegistrosBase as $stRegistro) {
                    $arRegistro = explode("#",$stRegistro);
                    switch (TRUE) {
                        case $arRegistro[1] == "0":
                            $stComplemento = "(S)";
                            break;
                        case $arRegistro[1] == "-1":
                            $stComplemento = "(F)";
                            break;
                        case $arRegistro[1] >= 1:
                            $stComplemento = "(C".$arRegistro[1].")";
                            break;
                    }
                    $inRegistro = $arRegistro[0];
                    $stRegistrosBase .= $inRegistro.$stComplemento."/";
                }
                $stRegistrosBase = substr($stRegistrosBase,0,strlen($stRegistrosBase)-1);
            }
            $arTitulo1[] = array("campo1"=>"Valores Acumulados com o Cálculo da Matrícula");
            $arTitulo1[] = array("campo1"=>"Matrícula(s): ". $stRegistrosBase);

            $arEventosDesconto = (is_array($rsEventosDesconto->getElementos())) ? $rsEventosDesconto->getElementos() : array();
            foreach ($arEventosDesconto as $arEventoDesconto) {
                if( !($arEventoDesconto['registro'] == $arFiltro['inContrato'] and $arEventoDesconto['cod_complementar'] == $arFiltro['inCodComplementar'] ) and
                    !($arEventoDesconto['registro'] >  $arFiltro['inContrato'] and $arEventoDesconto['cod_complementar'] == $arFiltro['inCodComplementar'] ) ){

                    $arValoresEventosDesconto[$arEventoDesconto['codigo']]['valor']     += $arEventoDesconto['valor'];
                    $arValoresEventosDesconto[$arEventoDesconto['codigo']]['codigo']     = $arEventoDesconto['codigo'];
                    $arValoresEventosDesconto[$arEventoDesconto['codigo']]['descricao']  = $arEventoDesconto['descricao'];
                    $arRegistrosDesconto[] = $arEventoDesconto['registro']."#".$arEventoDesconto['cod_complementar'];
                }
            }
            if ( is_array( $arValoresEventosDesconto ) ) {
                foreach ($arValoresEventosDesconto as $arValorEventoDesconto) {
                    $arValorEventoDesconto['valor'] = number_format($arValorEventoDesconto['valor'],2,',','.');
                    $arDescontos[] = $arValorEventoDesconto;
                }
                $arRegistrosDesconto = array_unique($arRegistrosDesconto);
                foreach ($arRegistrosDesconto as $stRegistro) {
                    $arRegistroDesconto = explode("#",$stRegistro);
                    switch (TRUE) {
                        case $arRegistroDesconto[1] == "0" :
                            $stComplemento = "(S)";
                            break;
                        case $arRegistroDesconto[1] == "-1" :
                            $stComplemento = "(F)";
                            break;
                        case $arRegistroDesconto[1] >= 1 :
                            $stComplemento = "(C".$arRegistroDesconto[1].")";
                            break;
                    }
                    $inRegistro = $arRegistroDesconto[0];
                    $stRegistrosDesconto .= $inRegistro.$stComplemento."/";
                }
                $stRegistrosDesconto = substr($stRegistrosDesconto,0,strlen($stRegistrosDesconto)-1);
            } else {
                $stRegistrosDesconto = "Não há contratos calculados";
                $arDescontos[] = array();
            }

            $arTitulo2[] = array("campo1"=>"Valores Acumulados até o Cálculo da Matrícula");
            $arTitulo2[] = array("campo1"=>"Matrícula(s): ".$stRegistrosDesconto);

            $arEventos = $arTemp;
            $arTemp                  = array();
            $arTemp['contratos']     = $arContrato;
            $arTemp['eventos']       = $arEventos;
            $arTemp['eventosBase']   = $arEventosBase;
            $arTemp['titulo1']       = $arTitulo1;
            $arTemp['bases']         = $arBases;
            $arTemp['titulo2']       = $arTitulo2;
            $arTemp['descontos']     = $arDescontos;
            //$arTemp['totais']        = $arTotais;
            $arRecordSet[]           = $arTemp;
            $arEventos               = array();
            $arTotais                = array();
            $arContratos             = array();
            $inCount                 = 0;
            $nuSomaProventos         = 0;
            $nuSomaDescontos         = 0;
        }
        $rsEventoCalculado->proximo();
    }
    $rsRecordset = new RecordSet;
    $rsRecordset->preenche( $arRecordSet );

    return $obErro;
}

function processarEventos($rsEventos)
{
    $arEventos = ( $rsEventos->getNumLinhas() > 0 ) ? $rsEventos->getElementos() : array();
    $arTemp = array();
    foreach ($arEventos as $arEvento) {
        if ($arEvento['cod_evento'] != $inCodEvento or $arEvento['desdobramento'] != $stDedobramento) {
            $arTemp[] = $arEvento;
            $inCodEvento    = $arEvento['cod_evento'];
            $stDedobramento = $arEvento['desdobramento'];
        }
    }
    $rsEventos = new recordset;
    $rsEventos->preenche($arTemp);

    return $rsEventos;
}

}
