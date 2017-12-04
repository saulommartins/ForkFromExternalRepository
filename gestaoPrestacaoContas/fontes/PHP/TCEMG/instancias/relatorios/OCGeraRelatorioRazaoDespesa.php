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
 * Página de Relatório RGF Anexo1
 * Data de Criação   : 08/10/2007

 * @author Tonismar Régis Bernardo

 * @ignore

 * $Id: OCGeraRelatorioRazaoDespesa.php 64774 2016-03-30 22:01:05Z michel $

 * Casos de uso : uc-06.01.20
 */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGRelatorioRazaoDespesa.class.php";
include_once CAM_GF_EMP_MAPEAMENTO.'FRelatorioPagamentoOrdemNotaEmpenho.class.php';

include_once CAM_FW_LEGADO."funcoesLegado.lib.php";
include_once CLA_MPDF;

$arFiltro = Sessao::read('filtroRelatorio');
if(count($arFiltro)>0)
    $request = new Request($arFiltro);

$arData = $arEstrutural = $arDataReceita = $registros = array();
$rsData = new Recordset;

$obTTCEMGRelatorioRazaoDespesa = new TTCEMGRelatorioRazaoDespesa;
$obTTCEMGRelatorioRazaoDespesa->setDado('dt_inicial'    , $request->get('stDataInicial'));
$obTTCEMGRelatorioRazaoDespesa->setDado('dt_final'      , $request->get('stDataFinal'));
$obTTCEMGRelatorioRazaoDespesa->setDado('tipo_relatorio', $request->get('stTipoRelatorio'));
$obTTCEMGRelatorioRazaoDespesa->setDado('num_orgao'     , $request->get('inNumOrgao'));
$obTTCEMGRelatorioRazaoDespesa->setDado('num_unidade'   , $request->get('inNumUnidade'));
$obTTCEMGRelatorioRazaoDespesa->setDado('num_pao'       , $request->get('inCodPao'));
$obTTCEMGRelatorioRazaoDespesa->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEMGRelatorioRazaoDespesa->setDado('entidade'      , implode(',', $request->get('inCodEntidade')));
$obTTCEMGRelatorioRazaoDespesa->setDado('cod_recurso'   , $request->get('inCodRecurso') ? implode(',', $request->get('inCodRecurso')) : null);
$obTTCEMGRelatorioRazaoDespesa->setDado('situacao'      , $request->get('inSituacao'));

$stExercicioEmpenho = Sessao::getExercicio();

$inCodRecurso = $request->get('inCodRecurso');

switch($request->get('stTipoRelatorio')) {
    case 'educacao_despesa_extra_orcamentaria':
        $stTipoRelatorio = 'Educação - Despesa Extra Orçamentária';
    break;

    case 'fundeb_60':
        $stTipoRelatorio = 'FUNDEB 60%';
        $inCodRecurso[] = 118;
    break;

    case 'fundeb_40':
        $stTipoRelatorio = 'FUNDEB 40%';
        $inCodRecurso[] = 119;
    break;

    case 'ensino_fundamental':
        $stTipoRelatorio = 'Ensino Fundamental';
        $stFiltro = " AND despesa.cod_subfuncao = 361";
    break;

    case 'gasto_25':
        $stTipoRelatorio = 'Gasto com 25%';
        $inCodRecurso[] = 101;
        $stFiltro  = " AND despesa.cod_subfuncao NOT IN ( 362,363,364 )";
    break;

    case 'saude':
        $stTipoRelatorio = 'Saúde';
        $inCodRecurso[] = 102;
    break;

    case 'diversos':
        $stTipoRelatorio = 'Diversos';
        $inCodRecurso[] = 100;
    break;

    case 'restos_pagar':
        $stTipoRelatorio = 'Restos a Pagar';
        $stFiltro = " AND pagamento.exercicio_empenho < '".Sessao::getExercicio()."'";
        $stExercicioEmpenho = "";
    break;
}

$stFiltro .= " AND ( pagamento.timestamp_pagamento::DATE BETWEEN to_date('".$request->get('stDataInicial')."','dd/mm/yyyy') AND to_date('".$request->get('stDataFinal')."','dd/mm/yyyy') )";

if($request->get('inNumOrgao'))
   $stFiltro .= " AND despesa.num_orgao = ".$request->get('inNumOrgao');

if($request->get('inNumUnidade'))
   $stFiltro .= " AND despesa.num_unidade = ".$request->get('inNumUnidade');

if($request->get('inCodPao'))
   $stFiltro .= " AND despesa.num_pao = ".$request->get('inCodPao');

if(count($inCodRecurso)>0)
    $request->set('inCodRecurso', $inCodRecurso);

if($request->get('inCodRecurso'))
    $stFiltro .= " AND despesa.cod_recurso IN (".implode(',', $request->get('inCodRecurso')).")";

if($request->get('inCodDespesa'))
   $stFiltro .= " AND despesa.cod_despesa = ".$request->get('inCodDespesa');

switch($request->get('inSituacao')) {
    case '1':
        $stNomeRelatorio = "Empenhados";
    break;

    case '2':
        $stNomeRelatorio = "Pagos";
    break;

    case '3':
        $stNomeRelatorio = "Liquidados";
    break;
}

$obFRelatorioPagamentoOrdemNotaEmpenho = new FRelatorioPagamentoOrdemNotaEmpenho;
$obFRelatorioPagamentoOrdemNotaEmpenho->setDado('exercicio'     , Sessao::getExercicio());
$obFRelatorioPagamentoOrdemNotaEmpenho->setDado('stEntidade'    , implode(',', $request->get('inCodEntidade')));
$obFRelatorioPagamentoOrdemNotaEmpenho->setDado('exercicio_empenho' , $stExercicioEmpenho);

//Relatórios Educação - Despesa Extra Orçamentária, segue padrão anterior
if($request->get('stTipoRelatorio') == 'educacao_despesa_extra_orcamentaria'){
    $obTTCEMGRelatorioRazaoDespesa->recuperaDadosDespesaExtraOrcamentaria($rsRecordSet);

    $arDia      = array();
    $arTotalDia = array();
    $arTotal    = array();

    foreach($rsRecordSet->getElementos() as $registro) {
        $arDia[$registro['dt_pagamento']][] = $registro;

        $arTotalDia[$registro['dt_pagamento']]['valor']           += $registro['valor'];
        $arTotalDia[$registro['dt_pagamento']]['valor_estornado'] += $registro['valor_estornado'];
        $arTotalDia[$registro['dt_pagamento']]['valor_liquido']   += ( $registro['valor'] - $registro['valor_estornado'] );

        $arTotal['valor']           += $registro['valor'];
        $arTotal['valor_estornado'] += $registro['valor_estornado'];
        $arTotal['valor_liquido']   += ( $registro['valor'] - $registro['valor_estornado'] );
    }

    $arDados = array(
        'arDia'               => $arDia,
        'stTipoRelatorio'     => $stTipoRelatorio,
        'arTotalDia'          => $arTotalDia,
        'arTotal'             => $arTotal
    );
}else{
    //Padrão Razão Pagamento, Ticket #23593
    $obFRelatorioPagamentoOrdemNotaEmpenho->recuperaRazaoTCEMG($rsRecordSet, $stFiltro);

    $arOrgaoUnidade      = array();
    $arNomOrgaoUnidade   = array();
    $arTotal             = array();
    $arTotalOrgao        = array();
    $arTotalOrgaoUnidade = array();
    $arTotalDotacao      = array();

    foreach($rsRecordSet->getElementos() as $registro) {
        $arOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']][$registro['dotacao'].' - '.$registro['descricao_despesa'].' - '.$registro['nom_recurso']][] = $registro;

        $arNomOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']]['num_orgao']   = $registro['num_orgao'];
        $arNomOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']]['nom_orgao']   = $registro['nom_orgao'];
        $arNomOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']]['num_unidade'] = $registro['num_unidade'];
        $arNomOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']]['nom_unidade'] = $registro['nom_unidade'];

        $vlAnulado = $registro['vl_anulado'];
        $vlAnuladoRetencao = 0;
        if($registro['vl_retencao'] > 0)
            $vlAnuladoRetencao = $vlAnulado;
        $vlAnuladoLiquido = $vlAnulado - $vlAnuladoRetencao;

        $vlTotalPago = $registro['vl_pago'] - $vlAnulado;
        $vlTotalRetencao = $registro['vl_retencao'] - $vlAnuladoRetencao;
        $vlTotalLiquido = $registro['vl_liquido'] - $vlAnuladoLiquido;

        $arTotalDotacao[$registro['num_orgao']][$registro['num_unidade']][$registro['dotacao'].' - '.$registro['descricao_despesa'].' - '.$registro['nom_recurso']]['vl_pago']     += $registro['vl_pago'];
        $arTotalDotacao[$registro['num_orgao']][$registro['num_unidade']][$registro['dotacao'].' - '.$registro['descricao_despesa'].' - '.$registro['nom_recurso']]['vl_retencao'] += $registro['vl_retencao'];
        $arTotalDotacao[$registro['num_orgao']][$registro['num_unidade']][$registro['dotacao'].' - '.$registro['descricao_despesa'].' - '.$registro['nom_recurso']]['vl_liquido']  += $registro['vl_liquido'];

        $arTotalDotacao[$registro['num_orgao']][$registro['num_unidade']][$registro['dotacao'].' - '.$registro['descricao_despesa'].' - '.$registro['nom_recurso']]['vl_anulado_p'] += $vlAnulado;
        $arTotalDotacao[$registro['num_orgao']][$registro['num_unidade']][$registro['dotacao'].' - '.$registro['descricao_despesa'].' - '.$registro['nom_recurso']]['vl_anulado_r'] += $vlAnuladoRetencao;
        $arTotalDotacao[$registro['num_orgao']][$registro['num_unidade']][$registro['dotacao'].' - '.$registro['descricao_despesa'].' - '.$registro['nom_recurso']]['vl_anulado_l'] += $vlAnuladoLiquido;

        $arTotalDotacao[$registro['num_orgao']][$registro['num_unidade']][$registro['dotacao'].' - '.$registro['descricao_despesa'].' - '.$registro['nom_recurso']]['vl_total_p'] += $vlTotalPago;
        $arTotalDotacao[$registro['num_orgao']][$registro['num_unidade']][$registro['dotacao'].' - '.$registro['descricao_despesa'].' - '.$registro['nom_recurso']]['vl_total_r'] += $vlTotalRetencao;
        $arTotalDotacao[$registro['num_orgao']][$registro['num_unidade']][$registro['dotacao'].' - '.$registro['descricao_despesa'].' - '.$registro['nom_recurso']]['vl_total_l'] += $vlTotalLiquido;

        $arTotalOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']]['vl_pago']     += $registro['vl_pago'];
        $arTotalOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']]['vl_retencao'] += $registro['vl_retencao'];
        $arTotalOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']]['vl_liquido']  += $registro['vl_liquido'];

        $arTotalOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']]['vl_anulado_p'] += $vlAnulado;
        $arTotalOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']]['vl_anulado_r'] += $vlAnuladoRetencao;
        $arTotalOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']]['vl_anulado_l'] += $vlAnuladoLiquido;

        $arTotalOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']]['vl_total_p'] += $vlTotalPago;
        $arTotalOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']]['vl_total_r'] += $vlTotalRetencao;
        $arTotalOrgaoUnidade[$registro['num_orgao']][$registro['num_unidade']]['vl_total_l'] += $vlTotalLiquido;

        $arTotalOrgao[$registro['num_orgao']]['vl_pago']     += $registro['vl_pago'];
        $arTotalOrgao[$registro['num_orgao']]['vl_retencao'] += $registro['vl_retencao'];
        $arTotalOrgao[$registro['num_orgao']]['vl_liquido']  += $registro['vl_liquido'];

        $arTotalOrgao[$registro['num_orgao']]['vl_anulado_p'] += $vlAnulado;
        $arTotalOrgao[$registro['num_orgao']]['vl_anulado_r'] += $vlAnuladoRetencao;
        $arTotalOrgao[$registro['num_orgao']]['vl_anulado_l'] += $vlAnuladoLiquido;

        $arTotalOrgao[$registro['num_orgao']]['vl_total_p'] += $vlTotalPago;
        $arTotalOrgao[$registro['num_orgao']]['vl_total_r'] += $vlTotalRetencao;
        $arTotalOrgao[$registro['num_orgao']]['vl_total_l'] += $vlTotalLiquido;

        $arTotal['vl_pago']     += $registro['vl_pago'];
        $arTotal['vl_retencao'] += $registro['vl_retencao'];
        $arTotal['vl_liquido']  += $registro['vl_liquido'];

        $arTotal['vl_anulado_p'] += $vlAnulado;
        $arTotal['vl_anulado_r'] += $vlAnuladoRetencao;
        $arTotal['vl_anulado_l'] += $vlAnuladoLiquido;

        $arTotal['vl_total_p'] += $vlTotalPago;
        $arTotal['vl_total_r'] += $vlTotalRetencao;
        $arTotal['vl_total_l'] += $vlTotalLiquido;
    }

    $arDados = array(
        'arOrgaoUnidade'      => $arOrgaoUnidade,
        'stTipoRelatorio'     => $stTipoRelatorio,
        'arNomOrgaoUnidade'   => $arNomOrgaoUnidade,
        'arTotalDotacao'      => $arTotalDotacao,
        'arTotalOrgaoUnidade' => $arTotalOrgaoUnidade,
        'arTotalOrgao'        => $arTotalOrgao,
        'arTotal'             => $arTotal
    );
}

// Switch necessário para selecionar template do relatório. Embora parecidos, há campos que constam num que não constam no outro.
switch($request->get('stTipoRelatorio')) {
    case 'educacao_despesa_extra_orcamentaria':
        #LHTCEMGRelatorioRazaoDespesaDespesaExtraOrc.php
        $obMPDF = new FrameWorkMPDF(6,55,12);
    break;

    default:
        #LHTCEMGRelatorioRazaoDespesa.php
        $obMPDF = new FrameWorkMPDF(6,55,10);
    break;
}

$obMPDF->setCodEntidades(implode(',', $request->get('inCodEntidade')));
$obMPDF->setDataInicio($request->get("stDataInicial"));
$obMPDF->setDataFinal($request->get("stDataFinal"). " - ".  $stNomeRelatorio);
$obMPDF->setNomeRelatorio("Razao da Despesa");
$obMPDF->setFormatoFolha("A4-L");
$obMPDF->setConteudo($arDados);
$obMPDF->gerarRelatorio();
