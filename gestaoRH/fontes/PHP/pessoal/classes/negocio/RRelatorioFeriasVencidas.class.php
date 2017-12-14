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
    * Classe de regra de relatório para Evento
    * Data de Criação:26/06/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Regra de Relatório

    * Casos de uso: uc-04.04.46
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO                                                            );
include_once ( CAM_GRH_PES_MAPEAMENTO . 'TPessoalFerias.class.php'                                  );
include_once ( CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoPeriodoMovimentacao.class.php'                   );
include_once ( CAM_GRH_FOL_NEGOCIO.'RFolhaPagamentoEvento.class.php'                                );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                     );

class RRelatorioFeriasVencidas extends PersistenteRelatorio
{
function montaFiltro()
{
    //Filtros da sessão
    $arFiltro  = Sessao::read('filtroRelatorio');

    //filtro por contratos
    $arContratos = Sessao::read('arContratos');

    if ( count($arContratos) and is_array($arContratos) ) {
        $stFiltro .= " AND contrato.registro in (";
        foreach ($arContratos as $arContrato) {
            $stContratos .= $arContrato['inContrato'] .',';
        }
        $stContratos = substr($stContratos,0,strlen($stContratos)-1);
        $stFiltro .= $stContratos.")";
    }

    // filtro por lotações
    if ( count($arFiltro['inCodLotacaoSelecionados']) ) {
        $stFiltro .= " AND orgao.cod_orgao in (";
        foreach ($arFiltro['inCodLotacaoSelecionados'] as $stLotacao) {
            $stLotacoes .= "'". $stLotacao ."',";
        }
        $stLotacoes = substr($stLotacoes,0,strlen($stLotacoes)-1);
        $stFiltro .= $stLotacoes.")";
    }

    // filtro por locais
    if ( count($arFiltro['inCodLocalSelecionados']) ) {
        $stFiltro .= " AND contrato_servidor_local.cod_local in (";
        foreach ($arFiltro['inCodLocalSelecionados'] as $inCodLocal) {
            $stLocais .= $inCodLocal .",";
        }
        $stLocais = substr($stLocais,0,strlen($stLocais)-1);
        $stFiltro .= $stLocais.")";
    }

    // filtro por Regime
    if ( count($arFiltro['inCodRegimeSelecionados']) ) {
        $stFiltro .= " AND contrato_servidor_regime_funcao.cod_regime in (";
        foreach ($arFiltro['inCodRegimeSelecionados'] as $inCodRegime) {
            $stRegimes .= $inCodRegime .",";
        }
        $stRegimes = substr($stRegimes,0,strlen($stRegimes)-1);
        $stFiltro .= $stRegimes.")";
    }

    // Filtro por SubDivisões
    if ( count($arFiltro['inCodSubDivisaoSelecionados']) ) {
        $stFiltro .= " AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao in (";
        foreach ($arFiltro['inCodSubDivisaoSelecionados'] as $inCodSubDivisao) {
            $stSubDivisoes .= $inCodSubDivisao .",";
        }
        $stSubDivisoes = substr($stSubDivisoes,0,strlen($stSubDivisoes)-1);
        $stFiltro .= $stSubDivisoes.")";
    }

    return $stFiltro;
}

function montaOrdem()
{
    //Filtros da sessão
    $arFiltro  = Sessao::read('filtroRelatorio');

    if ($arFiltro['boOrdenacaoLotacao'] == 't') {
        if ($arFiltro['stOrdenacaoLotacao'] == 'A') {
            $stOrdem .= " descricao_orgao,";
        } else {
            $stOrdem .= " vw_orgao_nivel.orgao,";
        }
    }
    if ($arFiltro['boOrdenacaoRegime'] == 't') {
        if ($arFiltro['stOrdenacaoRegime'] == 'A') {
            $stOrdem .= " regime.descricao,";
        } else {
            $stOrdem .= " regime.cod_regime,";
        }
    }
    if ($arFiltro['boOrdenacaoCGM'] == 't') {
        if ($arFiltro['stOrdenacaoCGM'] == 'A') {
            $stOrdem .= " sw_cgm.nom_cgm,";
        } else {
            $stOrdem .= " servidor.numcgm,";
        }
    }
    $stOrdem = substr($stOrdem,0,strlen($stOrdem)-1);

    return $stOrdem;
}

function geraRecordSet(&$rsRelatorio)
{
    //Filtros da sessão
    $arFiltro  = Sessao::read('filtroRelatorio');

    $RFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
    $obTPessoalFerias   = new TPessoalFerias;
    $obErro = $obTPessoalFerias->recuperaDadosDoContrato($rsContratos,$this->montaFiltro(),$this->montaOrdem());
    $arContratos = array();
    $dtDataVencimento = $arFiltro['dtDataVencimento'];
    $arDataVencimento = explode("/",$dtDataVencimento);
    Sessao::write('inTotalServidores', ($rsContratos->getNumLinhas() > 0)?$rsContratos->getNumLinhas():0);

    while (!$rsContratos->eof()) {
        $arContrato = array();
        $arFerias   = array();
        $arTemp['campo1'] = $rsContratos->getCampo("registro");
        $arTemp['campo2'] = substr($rsContratos->getCampo("numcgm") ."-". $rsContratos->getCampo("nom_cgm") , 0, 43);
        $arTemp['campo3'] = substr($rsContratos->getCampo("descricao_regime")."-".$rsContratos->getCampo("descricao_funcao") , 0, 43);
        $arTemp['campo4'] = substr(( $rsContratos->getCampo("descricao_local") != "" ) ? $rsContratos->getCampo("descricao_orgao")."-".$rsContratos->getCampo("descricao_local") : $rsContratos->getCampo("descricao_orgao") , 0, 43);
        $arContrato[] = $arTemp;
        $stFiltro = " AND cod_contrato  = ".$rsContratos->getCampo("cod_contrato");
        $obTPessoalFerias->recuperaRelacionamento( $rsFeriasCadastradas , $stFiltro , $stOrder );
        $rsFeriasCadastradas->setUltimoElemento();
        if ( $rsFeriasCadastradas->getNumLinhas() > 0 ) {
            $stPeriodoAquisitivoAnterior = $rsFeriasCadastradas->getCampo("dt_inicial_aquisitivo") ." a ". $rsFeriasCadastradas->getCampo("dt_final_aquisitivo");
            $arData                      = explode("/",$rsFeriasCadastradas->getCampo("dt_final_aquisitivo"));
        } else {
            $obRConfiguracaoPessoal = new RConfiguracaoPessoal;
            $obRConfiguracaoPessoal->Consultar();
            switch ( $obRConfiguracaoPessoal->getContagemInicial() ) {
                case "dtPosse":
                    $dtPosseNomeacao = $rsContratos->getCampo("dt_posse");
                    break;
                case "dtNomeacao":
                    $dtPosseNomeacao = $rsContratos->getCampo("dt_nomeacao");
                    break;
                case "dtAdmissao":
                    $dtPosseNomeacao = $rsContratos->getCampo("dt_admissao");
                    break;
            }
            $arData                      = explode("/",$dtPosseNomeacao);
        }
        do {
            $dtInicial                   = date("d/m/Y",mktime(0,0,0,$arData[1],$arData[0]+1,$arData[2]));
            $dtFinal                     = date("d/m/Y",mktime(0,0,0,$arData[1],$arData[0],$arData[2]+1));
            $dtFinal                     = ( sistemalegado::comparaDatas($dtFinal,$dtDataVencimento) ) ? $dtDataVencimento : $dtFinal;
            $arDataInicial               = explode("/",$dtInicial);
            $arData                      = explode("/",$dtFinal);
            $stPeriodoAquisitivoAnterior = $arDataInicial[0]."/".$arDataInicial[1]."/".($arDataInicial[2]+1)." a ".$arData[0]."/".$arData[1]."/".($arData[2]+1);
            $stPeriodoAquisitivoAtual    = $dtInicial." a ".$dtFinal;
            $inDias                      = $arDataVencimento[0] - $arData[0];
            $inMeses                     = $arDataVencimento[1] - $arData[1];
            $inAnos                      = $arDataVencimento[2] - $arData[2];
            $stQuantDiasVencidos         = "$inAnos Ano(s) - $inMeses Mês(es) - $inDias Dia(s)";
            $arTemp['campo1'] = '';
            $arTemp['campo2'] = $stPeriodoAquisitivoAtual;
            //verifica se a data do periodo aquisitivo está fechado comparando com as datas iniciais do período concessivo
            //($arData[0]+1) por listar um dia antes d da data inicial
            if ($arDataInicial[0] == ($arData[0]+1) && $arDataInicial[1] == $arData[1]) {
                $arTemp['campo3'] = $stPeriodoAquisitivoAnterior;
            } else {
                $arTemp['campo3'] = " - ";
            }
            $arTemp['campo4'] = $stQuantDiasVencidos;
            $arFerias[] = $arTemp;
        } while (sistemalegado::comparaDatas($dtDataVencimento,$dtFinal));
        $arContratos[] = array("contrato"=>$arContrato,"ferias"=>$arFerias);
        $rsContratos->proximo();
    }
    $rsRelatorio->preenche($arContratos);

    return $obErro;

}
}
