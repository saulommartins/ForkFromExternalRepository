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
* Classe de regra de relatório para Evento por contrato
* Data de Criação: 22/11/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Relatório

$Revision: 30896 $
$Name$
$Author: souzadl $
$Date: 2008-01-03 09:07:01 -0200 (Qui, 03 Jan 2008) $

* Casos de uso: uc-04.05.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                                );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoContratoServidor.class.php"                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

class RRelatorioEventoPorContrato extends PersistenteRelatorio
{
/**
    * @var Object
    * @access Private
*/
var $obRFolhaPagamentoPeriodoContratoServidor;

/**
     * @access Public
     * @param Object $valor
*/
function setRFolhaPagamentoPeriodoContratoServidor($valor) { $this->obRFolhaPagamentoPeriodoContratoServidor = $valor; }

/**
     * @access Public
     * @param Object $valor
*/
function getRFolhaPagamentoPeriodoContratoServidor() { return $this->obRFolhaPagamentoPeriodoContratoServidor;           }

/**
    * Método Construtor
    * @access Private
*/
function RRelatorioEventoPorContrato()
{
    $this->setRFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoContratoServidor( new RFolhaPagamentoPeriodoMovimentacao ) );
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordset)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    $this->obRFolhaPagamentoPeriodoContratoServidor->addRFolhaPagamentoRegistroEvento();
    if ( isset($arFiltro['inContrato']) ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");
        $obTPessoalContrato = new TPessoalContrato();
        $stFiltroContrato =  " WHERE registro = ".$arFiltro['inContrato'];
        $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltroContrato);
        $this->obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->roRFolhaPagamentoPeriodoContratoServidor->setCodContrato( $rsContrato->getCampo("cod_contrato") );
    }
    if ( isset($arFiltro['inNumCGM']) ) {
        $this->obRFolhaPagamentoPeriodoContratoServidor->roPessoalServidor->obRCGMPessoaFisica->setNumCGM( $arFiltro['inNumCGM'] );
    }
    if ( isset($arFiltro['inCodLotacaoSelecionados']) ) {
        $stOrgao = "";
        foreach ($arFiltro['inCodLotacaoSelecionados'] as $inCod=>$inCodOrgao) {
            $stOrgao .= "'".$inCodOrgao."'".',';
        }
        $stOrgao = substr($stOrgao,0,strlen($stOrgao)-1);
        $this->obRFolhaPagamentoPeriodoContratoServidor->obROrganogramaOrgao->setCodOrgao($stOrgao);
    }
    if ( isset($arFiltro['inCodLocalSelecionados']) ) {
        $stLocal = "";
        foreach ($arFiltro['inCodLocalSelecionados'] as $inCod=>$inCodLocal) {
            $stLocal .= "'".$inCodLocal."'".',';
        }
        $stLocal = substr($stLocal,0,strlen($stLocal)-1);
        $this->obRFolhaPagamentoPeriodoContratoServidor->obROrganogramaLocal->setCodLocal($stLocal);
    }
    $stDtFinal  = ( strlen($arFiltro['inCodMes']) == 1 )? '0'.$arFiltro['inCodMes'] : $arFiltro['inCodMes'];
    $stDtFinal .= "/".$arFiltro['inAno'];

    $this->obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoPeriodoMovimentacao->setDtFinal($stDtFinal);
    $this->obRFolhaPagamentoPeriodoContratoServidor->roRFolhaPagamentoRegistroEvento->listarRegistroEvento($rsRegistroEvento,$boTransacao);
    $this->obRFolhaPagamentoPeriodoContratoServidor->obROrganogramaOrgao->setCodOrgao("");
    $arRegistroEventoFixo           = array();
    $arRegistroEventoVariavel       = array();
    $arRegistroEventoProporcional   = array();
    $arContrato                     = array();
    $arRecordSet                    = array();
    $arTempEvento                   = array();
    $inCountFixo                    = 0;
    $inCountVariavel                = 0;
    $inCountProporcional            = 0;
    $rsRegistroEvento->addFormatacao("valor","NUMERIC_BR");
    $rsRegistroEvento->addFormatacao("quantidade","NUMERIC_BR");
    while ( !$rsRegistroEvento->eof() ) {
        $rsRegistroEvento->proximo();
        $inCodContratoProx = $rsRegistroEvento->getCampo('cod_contrato');
        $rsRegistroEvento->anterior();

        $arTempEvento['registro']                   = $rsRegistroEvento->getCampo('cod_registro');
        $arTempEvento['evento']                     = $rsRegistroEvento->getCampo('codigo');
        $arTempEvento['descricao']                  = $rsRegistroEvento->getCampo('descricao');
        $arTempEvento['quantidade']                 = $rsRegistroEvento->getCampo('quantidade');
        if ( $rsRegistroEvento->getCampo('proventos_descontos') == 'Proventos' ) {
            $nuProventos = $rsRegistroEvento->getCampo('valor');
            $nuDescontos = '0,00';
        } else {
            $nuProventos = '0,00';
            $nuDescontos = $rsRegistroEvento->getCampo('valor');
        }
        $arTempEvento['proventos']                  = $nuProventos;
        $arTempEvento['descontos']                  = $nuDescontos;
        $dtLimite = "";
        if ( $rsRegistroEvento->getCampo('parcela') != "" ) {
            $inQuantidadeParc = $rsRegistroEvento->getCampo('parcela');
            $dtTimestamp = explode(" ",$rsRegistroEvento->getCampo('timestamp'));
            $dtTimestamp = explode("-",$dtTimestamp[0]);
            $dtTimestamp = $dtTimestamp[2]."/".$dtTimestamp[1]."/".$dtTimestamp[0];
            $arDataFinal = explode("/",$dtTimestamp);
            $inResto            = (($arDataFinal[1]+$inQuantidadeParc)%12);
            $inInt              = intval((($arDataFinal[1]+$inQuantidadeParc)/12));
            if ($inResto) {
                $inAno = $arDataFinal[2] + $inInt;
            } else {
                $inAno = $arDataFinal[2] + $inInt-1;
            }
            $inMes = ( $inResto == 0 ) ? 12 : $inResto;
            $inMes = ( strlen($inMes) == 1 ) ? '0'.$inMes : $inMes;
            $dtLimite = $inMes ."/". $inAno;
        }
        $arTempEvento['limite']                     = $dtLimite;
        $arTempEvento['tipo']                       = $rsRegistroEvento->getCampo('tipo');
        $arTempEvento['proporcional']               = $rsRegistroEvento->getCampo('proporcional');
        if ( $rsRegistroEvento->getCampo('proporcional') == 't' ) {
            $arRegistroEventoProporcional[$inCountProporcional] = $arTempEvento;
            $inCountProporcional++;
        } elseif ( $rsRegistroEvento->getCampo('tipo') == 'F' ) {
            $arRegistroEventoFixo[$inCountFixo] = $arTempEvento;
            $inCountFixo++;
        } elseif ( $rsRegistroEvento->getCampo('tipo') == 'V' ) {
            $arRegistroEventoVariavel[$inCountVariavel] = $arTempEvento;
            $inCountVariavel++;
        }
        if ( $inCodContratoProx != $rsRegistroEvento->getCampo('cod_contrato') ) {
            $rsContratoServidor = new RecordSet();
            //$this->obRFolhaPagamentoPeriodoContratoServidor->setCodContrato($rsRegistroEvento->getCampo('cod_contrato'));
            //$this->obRFolhaPagamentoPeriodoContratoServidor->listarContratosServidorResumido($rsContratoServidor);
            include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php");
            $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento();
            $stFiltro = " AND contrato.cod_contrato = ".$rsRegistroEvento->getCampo('cod_contrato');
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
//            $stFiltro = " AND contrato.cod_contrato = ".$rsRegistroEvento->getCampo('cod_contrato');
//            $obTPessoalContratoServidorOrgao->recuperaRelacionamento($rsOrgao,$stFiltro);
            $arContrato[3]['campo1'] = 'Lotação: ';
            $arContrato[3]['campo2'] = $rsCGM->getCampo('cod_estrutural')." - ".$rsCGM->getCampo('desc_orgao');

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php");
            $obTPessoalContratoServidorLocal = new TPessoalContratoServidorLocal();
            $stFiltro = " AND cont_local.cod_contrato = ".$rsRegistroEvento->getCampo('cod_contrato');
            $obTPessoalContratoServidorLocal->recuperaRelacionamento($rsLocal,$stFiltro);
            $stLocal = ( $rsLocal->getCampo('cod_local') != "" ) ? $rsLocal->getCampo('cod_local')." - ".$rsLocal->getCampo('descricao'): "";
            $arContrato[4]['campo1'] = 'Local: ';
            $arContrato[4]['campo2'] = $stLocal;
            $arTemp                  = array();
            $arTemp['contratos']     = $arContrato;
            $arTemp['fixos']         = $arRegistroEventoFixo;
            $arTemp['variavel']      = $arRegistroEventoVariavel;
            $arTemp['proporcional']  = $arRegistroEventoProporcional;
            $arRecordSet[]           = $arTemp;
            $arRegistroEventoFixo    = array();
            $arRegistroEventoVariavel= array();
            $arRegistroEventoProporcional= array();
            $inCountFixo             = 0;
            $inCountVariavel         = 0;
            $inCountProporcional     = 0;
        }
        $rsRegistroEvento->proximo();
    }
    $rsRecordset = new RecordSet;
    $rsRecordset->preenche( $arRecordSet );

    return $obErro;
}

}
