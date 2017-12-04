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
* Classe de regra de relatório para calculo de folha de pagamento
* Data de Criação: 08/12/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Bruce Cruz de Sena

* @package URBEM
* @subpackage Regra de Relatório

* Casos de uso: uc-04.05.52
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                                );

class RRelatorioCreditosBanco extends PersistenteRelatorio
{
function geraRecordSet(&$rsRecordSet)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    include_once ( CAM_GRH_FOL_MAPEAMENTO . 'TFolhaPagamentoEvento.class.php' );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao;
    $stCompetencia  = ($arFiltro['inCodMes'] > 9) ? $arFiltro['inCodMes'] : '0'.$arFiltro['inCodMes'];
    $stCompetencia .= "/".$arFiltro['inAno'];
    $stFiltro = " WHERE to_char(dt_final,'mm/yyyy') = '".$stCompetencia."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro);

    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
    $obTFolhaPagamentoEvento->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));

    $stFiltro = "";
    if ($arFiltro['boSalário'] != "" || $arFiltro["inCodConfiguracao"] == 1) {
        $obTFolhaPagamentoEvento->setDado("boSalario",true);
        $stTipoFolha .= "Salário,";
    }
    if ($arFiltro['boFiltrarFolhaComplementar'] == 'on') {
        $stTipoFolha .= "Complementar,";
        $obTFolhaPagamentoEvento->setDado("boComplementar",true);

        $inCodComplementar = $arFiltro["inCodComplementar"];
        $obTFolhaPagamentoEvento->setDado("cod_complementar", $inCodComplementar);
    }
    if ($arFiltro['boFérias'] != "" || $arFiltro["inCodConfiguracao"] == 2) {
        $stTipoFolha .= "Férias,";
        $obTFolhaPagamentoEvento->setDado("boFerias",true);
    }
    if ($arFiltro['bo13ºSalário'] != "" || $arFiltro["inCodConfiguracao"] == 3) {
        $stTipoFolha .= "13º Salário,";
        $obTFolhaPagamentoEvento->setDado("boDecimo",true);
    }
    if ($arFiltro['boRescisao'] != "" || $arFiltro["inCodConfiguracao"] == 4) {
        $stTipoFolha .= "Rescisão,";
        $obTFolhaPagamentoEvento->setDado("boRescisao",true);
    }
    if ( count($arFiltro['inCodLotacaoSelecionados']) ) {
        foreach ($arFiltro['inCodLotacaoSelecionados'] as $inCodLotacao) {
            $stCodLotacoes .= $inCodLotacao.",";
        }
        $stCodLotacoes = substr($stCodLotacoes,0,strlen($stCodLotacoes)-1);
        $stFiltro = " AND orgao.cod_orgao IN (".$stCodLotacoes.") \n";
    }
    if ( count($arFiltro['inCodLocalSelecionados']) ) {
        foreach ($arFiltro['inCodLocalSelecionados'] as $inCodLocal) {
            $stCodLocal .= $inCodLocal.",";
        }
        $stCodLocal = substr($stCodLocal,0,strlen($stCodLocal)-1);
        $stFiltro .= " AND cod_local IN (".$stCodLocal.") \n";
    }
    if ( count($arFiltro['inCodBancoSelecionados']) ) {
        foreach ($arFiltro['inCodBancoSelecionados'] as $inCodBanco) {
            $stCodBanco .= $inCodBanco.",";
        }
        $stCodBanco = substr($stCodBanco,0,strlen($stCodBanco)-1);
        $stFiltro .= " AND banco.cod_banco IN (".$stCodBanco.") \n";
    }
    if ( count($arFiltro['inCodAgenciaSelecionados']) ) {
        foreach ($arFiltro['inCodAgenciaSelecionados'] as $inCodAgencia) {
            $stCodAgencia .= $inCodAgencia.",";
        }
        $stCodAgencia = substr($stCodAgencia,0,strlen($stCodAgencia)-1);
        $stFiltro .= " AND agencia.cod_agencia IN (".$stCodAgencia.") \n";
    }
    $stOrdem  = " ORDER BY banco.nom_banco,agencia.nom_agencia,lotacao,local,sw_cgm.nom_cgm";

    $arRelatorio = array();
    if ($rsPeriodoMovimentacao->getNumLinhas() > 0) {
        $obTFolhaPagamentoEvento->recuperaCreditosbanco ( $rsCreditosBanco, $stFiltro, $stOrdem);
        $arRelatorio  = $this->processarIndices($rsCreditosBanco,$rsPeriodoMovimentacao,$stTipoFolha);
    }

    $rsRecordSet= new RecordSet;
    $rsRecordSet->preenche($arRelatorio);
}

function processarIndices($rsRecordset,$rsPeriodoMovimentacao,$stTipoFolha)
{
    $arFiltro = Sessao::read("filtroRelatorio");
    $arIndices      = array();
    $arCabecalho    = array();
    $arTotalLocal   = array();
    $arTotalLotacao = array();
    $arTotalAgencia = array();
    $arTotalBanco   = array();
    $arTotais       = array();
    $arTotaisContratos = array();
    $stTipoFolha    = substr($stTipoFolha,0,strlen($stTipoFolha)-1);
    $arRecordSet = $rsRecordset->getElementos();
    while (!$rsRecordset->eof()) {
        $inCodBanco      = $rsRecordset->getCampo("cod_banco");
        $inCodAgencia    = $rsRecordset->getCampo("cod_agencia");
        $inCodOrgao      = $rsRecordset->getCampo("cod_orgao");
        $inCodLocal      = $rsRecordset->getCampo("cod_local");
        $stIndiceBanco   = $inCodBanco;
        $stIndiceAgencia = $stIndiceBanco."-".$inCodAgencia;
        $stIndiceLotacao = $stIndiceAgencia."-".$inCodOrgao;
        $stIndice1       = $stIndiceLotacao."-".$inCodLocal;
        $arTotais[$stIndice1]                   += $rsRecordset->getCampo("valor");
        $arTotaisContratos[$stIndice1]          += 1;
        $arTotais[$stIndiceBanco]               += $rsRecordset->getCampo("valor");
        $arTotaisContratos[$stIndiceBanco]      += 1;
        $arTotais[$stIndiceAgencia]             += $rsRecordset->getCampo("valor");
        $arTotaisContratos[$stIndiceAgencia]    += 1;
        $arTotais[$stIndiceLotacao]             += $rsRecordset->getCampo("valor");
        $arTotaisContratos[$stIndiceLotacao]    += 1;

        $arTemp    = $arRecordSet[$rsRecordset->getCorrente()-1];
        $inIndex   = count($arContratos);
        $arContratos[$inIndex]["registro"] = $arTemp["registro"];
        $arContratos[$inIndex]["cgm"]      = $arTemp["numcgm"]."-".$arTemp["nom_cgm"];
        $arContratos[$inIndex]["cpf"]      = $arTemp["cpf"];
        $arContratos[$inIndex]["nr_conta"] = $arTemp["nr_conta"];
        $arContratos[$inIndex]["valor"]    = number_format($arTemp["valor"],2,',','.');

        $arCabecalho = array();
        if ( $rsRecordset->getCorrente() == 1 ) {
            $arCabecalho[0]["campo1"]          = "Tipo da Folha: ";
            $arCabecalho[0]["campo2"]          = $stTipoFolha;
            $arCabecalho[0]["campo3"]          = "Competência: ";
            $arCabecalho[0]["campo4"]          = substr($rsPeriodoMovimentacao->getCampo("dt_final"),3,10);
            $arCabecalho[0]["campo5"]          = "Período Movimentação: ";
            $arCabecalho[0]["campo6"]          = $rsPeriodoMovimentacao->getCampo("dt_inicial")." até ".$rsPeriodoMovimentacao->getCampo("dt_final");

            $arCabecalho[1]["campo1"]          = "Banco: ";
            $arCabecalho[1]["campo2"]          = $arTemp["num_banco"]."-".$arTemp["nom_banco"];
            $arCabecalho[1]["campo3"]          = "Lotação: ";
            $arCabecalho[1]["campo4"]          = $arTemp["cod_estrutural"]."-".$arTemp["lotacao"];
            $arCabecalho[1]["campo5"]          = "";
            $arCabecalho[1]["campo6"]          = "";

            $arCabecalho[2]["campo1"]          = "Agência: ";
            $arCabecalho[2]["campo2"]          = $arTemp["num_agencia"]."-".$arTemp["nom_agencia"];
                $arCabecalho[2]["campo3"]          = "Local: ";
            $arCabecalho[2]["campo4"]          = $arTemp["local"];
            $arCabecalho[2]["campo5"]          = "";
            $arCabecalho[2]["campo6"]          = "";
        } else {
            $arCabecalho[0]["campo1"]          = "Banco: ";
            $arCabecalho[0]["campo2"]          = $arTemp["num_banco"]."-".$arTemp["nom_banco"];
            $arCabecalho[0]["campo3"]          = "Lotação: ";
            $arCabecalho[0]["campo4"]          = $arTemp["cod_estrutural"]."-".$arTemp["lotacao"];
            $arCabecalho[0]["campo5"]          = "";
            $arCabecalho[0]["campo6"]          = "";

            $arCabecalho[1]["campo1"]          = "Agência: ";
            $arCabecalho[1]["campo2"]          = $arTemp["num_agencia"]."-".$arTemp["nom_agencia"];
            $arCabecalho[1]["campo3"]          = "Local: ";
            $arCabecalho[1]["campo4"]          = $arTemp["local"];
            $arCabecalho[1]["campo5"]          = "";
            $arCabecalho[1]["campo6"]          = "";
        }
        $rsRecordset->proximo();
        $stIndice2 = $rsRecordset->getCampo("cod_banco")."-".$rsRecordset->getCampo("cod_agencia")."-".$rsRecordset->getCampo("cod_orgao")."-".$rsRecordset->getCampo("cod_local");
        if ($stIndice1 != $stIndice2) {
            $arTotalLocal[0] = array("campo1"=>"Total de Servidores do Local: ".$arTotaisContratos[$stIndice1],
                                     "campo2"=>"Total do Local: ".number_format($arTotais[$stIndice1],2,',','.'));
            $arTotalLotacao[0] = array("campo1"=>"Total de Servidores da Lotação: ".$arTotaisContratos[$stIndiceLotacao],
                                     "campo2"=>"Total da Lotação: ".number_format($arTotais[$stIndiceLotacao],2,',','.'));
            $arTotalAgencia[0] = array("campo1"=>"Total de Servidores da Agência: ".$arTotaisContratos[$stIndiceAgencia],
                                     "campo2"=>"Total da Agência: ".number_format($arTotais[$stIndiceAgencia],2,',','.'));
            $arTotalBanco[0] = array("campo1"=>"Total de Servidores do Banco: ".$arTotaisContratos[$stIndiceBanco],
                                     "campo2"=>"Total do Banco: ".number_format($arTotais[$stIndiceBanco],2,',','.'));
            $arPagina = array();
            $arPagina["arCabecalho"]    = $arCabecalho;
            $arPagina["arContratos"]    = $arContratos;
            $arPagina["arTotalLocal"]   = $arTotalLocal;
            if ( $stIndiceLotacao != $rsRecordset->getCampo("cod_banco")."-".$rsRecordset->getCampo("cod_agencia")."-".$rsRecordset->getCampo("cod_orgao") ) {
                $arPagina["arTotalLotacao"] = $arTotalLotacao;
            }
            if ( $stIndiceAgencia != $rsRecordset->getCampo("cod_banco")."-".$rsRecordset->getCampo("cod_agencia") ) {
                $arPagina["arTotalAgencia"] = $arTotalAgencia;
            }
            if ( $inCodBanco != $rsRecordset->getCampo("cod_banco") ) {
                $arPagina["arTotalBanco"]   = $arTotalBanco;
            }
            $arIndices[] = $arPagina;
            $arContratos = array();
        }
    }

    return $arIndices;
}

}
