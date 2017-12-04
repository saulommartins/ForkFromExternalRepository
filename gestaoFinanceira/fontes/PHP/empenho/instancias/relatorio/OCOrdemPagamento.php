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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 13/05/2005

    * @author Desenvolvedor: João Rafael Tissot

    * @ignore

    * $Id: OCOrdemPagamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.03.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioOrdensPagamento.class.php"  );
include_once( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                 );

$arFiltro = Sessao::read('filtroRelatorio');
$obRegra            = new REmpenhoRelatorioOrdensPagamento();
$obRCGM = new RCGM;

switch ($_REQUEST['stCtrl']) {

    case 'buscaFornecedor':
    if ($_POST["cgm_beneficiario"] != "") {
        $obRCGM->setNumCGM( $_POST["cgm_beneficiario"] );
        $obRCGM->listar( $rsCGM );
        $stCredor = $rsCGM->getCampo( "nom_cgm" );
        if (!$stCredor) {
            $js .= 'f.stCredor.value = "";';
            $js .= 'f.stCredor.focus();';
            $js .= 'd.getElementById("stCredor").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["cgm_beneficiario"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stCredor").innerHTML = "'.$stCredor.'";';
        }
    } else $js .= 'd.getElementById("stCredor").innerHTML = "&nbsp;";';
    SistemaLegado::executaFrameOculto($js);
    break;

    case "carregaOrdenacao":
        if ($_REQUEST['situacao'] == 2  && $_REQUEST['tipo'] == 'analitico') {
            $stJs .= "limpaSelect(f.ordenacao,0); \n";
            $stJs .= "f.ordenacao.options[0] = new Option('Por Nro de OP','op');\n";
            $stJs .= "f.ordenacao.options[1] = new Option('Por Data de emissão','emissao');\n";
            $stJs .= "f.ordenacao.options[2] = new Option('Por Data de Pagamento','pagamento','selected');\n";
            $stJs .= "f.ordenacao.options[3] = new Option('Por Credor','credor');\n";
        } else {
            $stJs .= "limpaSelect(f.ordenacao,0); \n";
            $stJs .= "f.ordenacao.options[0] = new Option('Por Nro de OP','op');\n";
            $stJs .= "f.ordenacao.options[1] = new Option('Por Data de emissão','emissao');\n";
            $stJs .= "f.ordenacao.options[2] = new Option('Por Credor','credor');\n";
        }
    echo $stJs;
    break;

    /*default:

    //ENTIDADE
    if ($arFiltro['inCodEntidade'] != "") {
        foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
            $stCodEntidades .= $valor.",";
        }
        $stCodEntidades = substr( $stCodEntidades, 0, strlen($stCodEntidades)-1);
        $obRegra->setCodEntidade( $stCodEntidades );
    }

    //EXERCICIO
    $obRegra->setExercicioEmpenho ( $arFiltro['stExercicio'] );

    //COD ORDEM
    $obRegra->setCodOrdemInicial ( $arFiltro['cod_ordem_inicio'] );
    $obRegra->setCodOrdemFinal   ( $arFiltro['cod_ordem_final']  );

    //cgm beneficiario
    $obRegra->setCodCredor ( $arFiltro['cgm_beneficiario'] );

    //COD EMPENHO
    $obRegra->setCodEmpenhoInicial( $arFiltro['cod_empenho_inicio'] );
    $obRegra->setCodEmpenhoFinal  ( $arFiltro['cod_empenho_final'] );

    //PERIODO
    $obRegra->setDtInicial(str_replace("\'", "", $arFiltro['stDataInicial']));
    $obRegra->setDtFinal(str_replace("\'", "", $arFiltro['stDataFinal']));

    //COD RECURSO
    $obRegra->setCodRecurso( trim($arFiltro['inCodRecurso']) );

    if($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao'])
        $obRegra->setDestinacaoRecurso ( $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );

    $obRegra->setCodDetalhamento( $arFiltro['inCodDetalhamento'] );

    //Tipo
    $obRegra->setTipo( trim($arFiltro['tipo']) );

    if ($arFiltro['situacao'] != "") {

        $obRegra->setSituacao( $arFiltro['situacao']);

        if ($arFiltro['situacao']=="1") {
            $stFiltro .= " AND situacao = 'A Pagar' ";
            }

        if ($arFiltro['situacao']=="2") {
            $stFiltro .= " AND situacao = 'Paga' ";
        }

        if ($arFiltro['situacao']=="3") {
            $stFiltro .= " AND situacao = 'Anulada' ";
        }

    }

    if ($arFiltro['ordenacao'] == "op") {
       $stOrdem = " ORDER BY cod_ordem, dt_emissao, dt_pagamento, credor ";
    }
    if ($arFiltro['ordenacao'] == "emissao") {
       $stOrdem = " ORDER BY dt_emissao, cod_ordem, dt_pagamento, credor";
    }
    if ($arFiltro['ordenacao'] == "pagamento") {
       $stOrdem = " ORDER BY dt_pagamento, dt_emissao, cod_ordem,  credor";
    }
    if ($arFiltro['ordenacao'] == "credor") {
       $stOrdem = " ORDER BY credor, cod_ordem, dt_emissao, dt_pagamento";
    }

    $obRegra->geraRecordSet( $rsOrdemPagamento, $stFiltro, $stOrdem );
    Sessao::write('rsRecordSet', $rsOrdemPagamento);
    $obRegra->obRRelatorio->executaFrameOculto( "OCGeraRelatorioOrdemPagamento.php" );
    break;*/
}
?>
