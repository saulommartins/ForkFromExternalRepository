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
    * Classe de regra de relatório para Ficha Financeira
    * Data de Criação: 14/12/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Regra de Relatório

    $Revision: 30896 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                                );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoCalculoFolhaPagamento.class.php"                     );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

class RRelatorioFichaFinanceira extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRFolhaPagamentoCalculoFolhaPagamento;

/**
     * @access Public
     * @param Object $valor
*/
function setRFolhaPagamentoCalculoFolhaPagamento($valor) { $this->obRFolhaPagamentoCalculoFolhaPagamento = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getRFolhaPagamentoCalculoFolhaPagamento() { return $this->obRFolhaPagamentoCalculoFolhaPagamento;           }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioFichaFinanceira()
{
    $this->setRFolhaPagamentoCalculoFolhaPagamento( new RFolhaPagamentoCalculoFolhaPagamento() );
    $this->obRFolhaPagamentoCalculoFolhaPagamento->setRORFolhaPagamentoPeriodoMovimentacao( new RFolhaPagamentoPeriodoMovimentacao );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordset)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoPeriodoContratoServidor();
    if ( isset($arFiltro['inContrato']) ) {
        $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->setRegistro( $arFiltro['inContrato'] );
    }
    if ( isset($arFiltro['inCodEspecialidade']) and $arFiltro['inCodEspecialidade'] != "" ) {
        $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->addEspecialidade();
        $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->roUltimoEspecialidade->setCodEspecialidade($arFiltro['inCodEspecialidade']);
    } else {
        if ( isset($arFiltro['inCodCargo']) ) {
            $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obRPessoalCargo->setCodCargo($arFiltro['inCodCargo']);
        }
    }
    if ( isset($arFiltro['inCodLotacaoSelecionados']) ) {
        $stOrgao = "";
        foreach ($arFiltro['inCodLotacaoSelecionados'] as $inCod=>$inCodOrgao) {
            $stOrgao .= "'".$inCodOrgao."'".',';
        }
        $stOrgao = substr($stOrgao,0,strlen($stOrgao)-1);
        $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaOrgao->setCodOrgao($stOrgao);
    }
    if ( isset($arFiltro['inCodLocalSelecionados']) ) {
        $stLocal = "";
        foreach ($arFiltro['inCodLocalSelecionados'] as $inCod=>$inCodLocal) {
            $stLocal .= "'".$inCodLocal."'".',';
        }
        $stLocal = substr($stLocal,0,strlen($stLocal)-1);
        $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->obROrganogramaLocal->setCodLocal($stLocal);
    }
    if ( isset($arFiltro['inCodConfiguracao']) ) {
        $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->addRFolhaPagamentoRegistroEvento();
        $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->addConfiguracaoEvento();
        $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->obRFolhaPagamentoEvento->roUltimoConfiguracaoEvento->setCodConfiguracao($arFiltro['inCodConfiguracao']);
    }
    if ( isset($arFiltro['inCodComplementar']) ) {
        $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoFolhaComplementar();
        $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->setCodComplementar($arFiltro['inCodComplementar']);
    }
    if ( isset($arFiltro['stOrdenacao']) ) {
        $stOrdem = $arFiltro['stOrdenacao'];
    }
    if ($arFiltro['stOrdenacaoEventos'] == 'codigo') {
        $stOrdem .= ",codigo";
    } else {
        $stOrdem .= ",sequencia";
    }
    $stDtFinal  = ( strlen($arFiltro['inCodMes']) == 1 )? '0'.$arFiltro['inCodMes'] : $arFiltro['inCodMes'];
    $stDtFinal .= "/".$arFiltro['inAno'];
    $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->setDtFinal($stDtFinal);
    if ($arFiltro['boFiltrarFolhaComplementar']) {
        $this->obRFolhaPagamentoCalculoFolhaPagamento->roRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->obRFolhaPagamentoCalculoFolhaComplementar->listarEventoFichaFinanceira($rsEventoCalculado,"",$stOrdem,$boTransacao);
    } else {
        $this->obRFolhaPagamentoCalculoFolhaPagamento->listarEventoRelatorioFichaFinanceira($rsEventoCalculado,$stOrdem,$boTransacao);
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
    while ( !$rsEventoCalculado->eof() ) {
        $rsEventoCalculado->proximo();
        $inCodContratoProx = $rsEventoCalculado->getCampo('cod_contrato');
        $rsEventoCalculado->anterior();

        $arTempEvento['evento']                     = $rsEventoCalculado->getCampo('codigo');
        $arTempEvento['descricao']                  = $rsEventoCalculado->getCampo('descricao');
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

//            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php");
//            $obTPessoalContratoServidorOrgao = new TPessoalContratoServidorOrgao();
//            $stFiltro = " AND contrato.cod_contrato = ".$rsEventoCalculado->getCampo('cod_contrato');
//            $obTPessoalContratoServidorOrgao->recuperaRelacionamento($rsOrgao,$stFiltro);
            $arContrato[3]['campo1'] = 'Lotação: ';
            $arContrato[3]['campo2'] = $rsCGM->getCampo('cod_estrutural')." - ".$rsCGM->getCampo('desc_orgao');

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php");
            $obTPessoalContratoServidorLocal = new TPessoalContratoServidorLocal();
            $stFiltro = " AND cont_local.cod_contrato = ".$rsEventoCalculado->getCampo('cod_contrato');
            $obTPessoalContratoServidorLocal->recuperaRelacionamento($rsLocal,$stFiltro);
            $stLocal = ( $rsLocal->getCampo('cod_local') != "" ) ? $rsLocal->getCampo('cod_local')." - ".$rsLocal->getCampo('descricao'): "";
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
            $arEventos = $arTemp;
            $arTemp                  = array();
            $arTemp['contratos']     = $arContrato;
            $arTemp['eventos']       = $arEventos;
            $arTemp['eventosBase']   = $arEventosBase;
            $arTemp['titulo1']       = $arTitulo1;
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

}
