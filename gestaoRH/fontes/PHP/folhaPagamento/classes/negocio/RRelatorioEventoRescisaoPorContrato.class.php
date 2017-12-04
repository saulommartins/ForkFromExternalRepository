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
* Classe de regra de relatório para Evento Rescisão por contrato
* Data de Criação: 17/10/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Relatório

$Revision: 30896 $
$Name$
$Author: souzadl $
$Date: 2006-10-18 08:20:03 -0300 (Qua, 18 Out 2006) $

* Casos de uso: uc-04.05.54
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                                );
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoRescisao.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoBase.class.php");
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php");

class RRelatorioEventoRescisaoPorContrato extends PersistenteRelatorio
{
/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordset)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    $obTPessoalContrato = new TPessoalContrato();
    $stFiltro = " WHERE registro = ".$arFiltro['inContrato'];
    $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $dtCompetencia  = ( count($arFiltro['inCodMes']) == 1 ) ? "0".$arFiltro['inCodMes'] : $arFiltro['inCodMes'];
    $dtCompetencia .= "/".$arFiltro['inAno'];
    $stFiltro = " WHERE to_char(dt_final,'mm/yyyy') = '".$dtCompetencia."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro);
    $rsPeriodoMovimentacao->setUltimoElemento();
    $arRecordSet = array();
    $obTFolhaPagamentoRegistroEventoRescisao = new TFolhaPagamentoRegistroEventoRescisao;
    $stFiltro  = " AND cod_contrato = ".$rsContrato->getCampo("cod_contrato");
    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
    $obTFolhaPagamentoRegistroEventoRescisao->recuperaRelacionamento($rsRegistroEvento,$stFiltro,$stOrdem);
    $rsRegistroEvento->addFormatacao("valor","NUMERIC_BR");
    $rsRegistroEvento->addFormatacao("quantidade","NUMERIC_BR");
    $arRegistroEventoCadastrados = array();
    $arRegistroEventoBase = array();
    $inCount = 0;
    $inCountBase = 0;
    while ( !$rsRegistroEvento->eof() ) {
        $rsRegistroEvento->proximo();
        $inCodContratoProx = $rsRegistroEvento->getCampo('cod_contrato');
        $rsRegistroEvento->anterior();

        $arTempEvento['codigo']                     = $rsRegistroEvento->getCampo('codigo');
        $arTempEvento['descricao']                  = $rsRegistroEvento->getCampo('descricao');
        switch ( $rsRegistroEvento->getCampo('desdobramento') ) {
            case "S":
                $arTempEvento['desdobramento']      = "Saldo Salário";
                break;
            case "P":
                $arTempEvento['desdobramento']      = "Aviso Prévio";
                break;
            case "V":
                $arTempEvento['desdobramento']      = "Férias Vencidas";
                break;
            case "P":
                $arTempEvento['desdobramento']      = "Férias Porporcionais";
                break;
            case "D":
                $arTempEvento['desdobramento']      = "13º Salário";
                break;
        }
        $arTempEvento['valor']                      = $rsRegistroEvento->getCampo('valor');
        $arTempEvento['quantidade']                 = $rsRegistroEvento->getCampo('quantidade');
        $arTempEvento['automatico']                 = $rsRegistroEvento->getCampo('automatico');

        $obTFolhaPagamentoEventoBase = new TFolhaPagamentoEventoBase;
        $stFiltro  = " AND evento_base.cod_evento = ".$rsRegistroEvento->getCampo("cod_evento");
        $stFiltro .= " AND evento_base.cod_configuracao = 4";
        $stFiltro .= " AND registro_evento_rescisao.cod_contrato = ".$rsContrato->getCampo("cod_contrato");
        $stFiltro .= " AND registro_evento_rescisao.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
        $obTFolhaPagamentoEventoBase->recuperaEventoBaseDesdobramentoRescisao($rsEventosBase,$stFiltro);
        while (!$rsEventosBase->eof()) {
            $arTempBase['codigo']       = $rsEventosBase->getCampo("codigo_base");
            $arTempBase['descricao']    = $rsEventosBase->getCampo("descricao_base");
            $arTempBase['desdobramento']= $rsEventosBase->getCampo("desdobramento_texto");
            $arTempBase['valor']        = '0,00';
            $arTempBase['valor']        = '0,00';
            $arTempBase['quantidade']   = '0,00';
            $arTempBase['automatico']   = 'Sim';
            $arRegistroEventoBase[$inCountBase]    = $arTempBase;
            $inCountBase++;
            $rsEventosBase->proximo();
        }

        $arRegistroEventoCadastrados[$inCount] = $arTempEvento;
        $inCount++;
        if ( $inCodContratoProx != $rsRegistroEvento->getCampo('cod_contrato') ) {
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

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php");
            $obTPessoalContratoServidorOrgao = new TPessoalContratoServidorOrgao();
            $stFiltro = " AND contrato.cod_contrato = ".$rsRegistroEvento->getCampo('cod_contrato');
            $obTPessoalContratoServidorOrgao->recuperaRelacionamento($rsOrgao,$stFiltro);
            $arContrato[3]['campo1'] = 'Lotação: ';
            $arContrato[3]['campo2'] = $rsOrgao->getCampo('cod_estrutural')." - ".$rsOrgao->getCampo('descricao');

            include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorLocal.class.php");
            $obTPessoalContratoServidorLocal = new TPessoalContratoServidorLocal();
            $stFiltro = " AND cont_local.cod_contrato = ".$rsRegistroEvento->getCampo('cod_contrato');
            $obTPessoalContratoServidorLocal->recuperaRelacionamento($rsLocal,$stFiltro);
            $stLocal = ( $rsLocal->getCampo('cod_local') != "" ) ? $rsLocal->getCampo('cod_local')." - ".$rsLocal->getCampo('descricao'): "";
            $arContrato[4]['campo1'] = 'Local: ';
            $arContrato[4]['campo2'] = $stLocal;
            $arTemp                  = array();
            $arTemp['contratos']     = $arContrato;
            $arTemp['eventos_cadastrados'] = $arRegistroEventoCadastrados;
            $arTemp['eventos_base']  = $arRegistroEventoBase;
            $arRecordSet[]           = $arTemp;
            $arRegistroEventoCadastrados    = array();
            $arRegistroEventoBase         = array();
            $inCount                 = 0;
            $inCountBase             = 0;
        }
        $rsRegistroEvento->proximo();
    }
    $rsRecordset = new RecordSet;
    $rsRecordset->preenche( $arRecordSet );

    return $obErro;
}

}
