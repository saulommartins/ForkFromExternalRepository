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
    * Data de Criação   : 12/05/2005

    * @author Analista: Dieine da Silva
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    * $Id: OCSituacaoEmpenho.php 64470 2016-03-01 13:12:50Z jean $

    * Casos de uso: uc-02.03.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioSituacaoEmpenho.class.php";

$obRegra = new REmpenhoRelatorioSituacaoEmpenho;
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     (Sessao::read('numCgm'));
$obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->listarUsuariosEntidade($rsTotalEntidades, " ORDER BY cod_entidade");

$arFiltro = Sessao::read('filtroRelatorio');
$arFiltroNom = Sessao::read('filtroNomRelatorio');

//seta elementos do filtro para ENTIDADE
if ($arFiltro['inCodEntidade'] != "") {
    $inCount = 0;
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stEntidade .= $valor.",";
        $inCount++;
    }
    $stEntidade = substr( $stEntidade, 0, strlen($stEntidade) - 1 );
} else {
    $stEntidade .= $arFiltro['stTodasEntidades'];
}

if ( $rsTotalEntidades->getNumLinhas() == $inCount ) {
   $arFiltro['relatorio'] = "(Consolidado)";
} else {
   $arFiltro['relatorio'] = "";
}

switch ($_REQUEST['stCtrl']) {
case "MontaUnidade":
    if ($_REQUEST["inNumOrgao"]) {
        $stCombo  = "inNumUnidade";
        $stComboTxt  = "inNumUnidadeTxt";
        $stJs .= "limpaSelect(f.$stCombo,0); \n";
        $stJs .= "f.$stComboTxt.value=''; \n";
        $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setExercicio($_REQUEST["inExercicio"]);
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inNumOrgao"]);
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->consultar( $rsCombo, $stFiltro,"", $boTransacao );
        while ( !$rsCombo->eof() ) {
            $arFiltroNom['unidade'][$rsCombo->getCampo( 'num_unidade' )] = $rsCombo->getCampo( 'nom_unidade' );
            $rsCombo->proximo();
        }
        $rsCombo->setPrimeiroElemento();

        $inCount = 0;
        while (!$rsCombo->eof()) {
            $inCount++;
            $inId   = $rsCombo->getCampo("num_unidade");
            $stDesc = $rsCombo->getCampo("nom_unidade");
            if( $stSelecionado == $inId )
                $stSelected = 'selected';
            else
                $stSelected = '';
            $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
            $rsCombo->proximo();
        }
        Sessao::write('filtroNomRelatorio', $arFiltroNom);
    }

    $stJs .= $js;
    SistemaLegado::executaFrameOculto( $stJs );
    break;

case 'buscaFornecedorDiverso':
    if ($_POST["inCodFornecedor"] != "") {
        $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM( $_POST["inCodFornecedor"] );
        $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->listar( $rsCGM );
        $stNomFornecedor = $rsCGM->getCampo( "nom_cgm" );
        if (!$stNomFornecedor) {
            $js .= 'f.inCodFornecedor.value = "";';
            $js .= 'f.inCodFornecedor.focus();';
            $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
            $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodFornecedor"].")','form','erro','".Sessao::getId()."');";
        } else {
            $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
        }
    } else $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
    SistemaLegado::executaFrameOculto($js);
    break;

case "mascaraClassificacao":
    //monta mascara da RUBRICA DE DESPESA
    $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodDespesa'] );
    $js .= "f.inCodDespesa.value = '".$arMascClassificacao[1]."'; \n";

    //busca DESCRICAO DA RUBRICA DE DESPESA
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascara          ( $_POST['stMascClassificacao'] );
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
    if ($stDescricao != "") {
        $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'";';
    } else {
        $null = "&nbsp;";
        $js .= 'f.inCodDespesa.value = "";';
        $js .= 'f.inCodDespesa.focus();';
        $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
        $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
    }
    SistemaLegado::executaFrameOculto( $js );
    break;

case 'buscaDotacao':
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST["inCodDotacao"] );
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->listarDespesaUsuario( $rsDespesa );

    $stNomDespesa = $rsDespesa->getCampo( "descricao" );
    if (!$stNomDespesa) {
        $js .= 'f.inCodDotacao.value = "";';
        $js .= 'f.inCodDotacao.focus();';
        $js .= 'd.getElementById("stNomDotacao").innerHTML = "&nbsp;";';
        $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodDotacao"].")','form','erro','".Sessao::getId()."');";
    } else {
        $stNomDespesa = $rsDespesa->getCampo( "descricao" );
        $js .= 'd.getElementById("stNomDotacao").innerHTML = "'.$stNomDespesa.'";';
    }

    SistemaLegado::executaFrameOculto($js);
    break;

case "validaData":
    $validaDataInicial = explode("/",$_REQUEST["stDataInicial"]);
    $validaDataFinal = explode("/",$_REQUEST["stDataFinal"]);
    if ($_POST['stDataInicial']) {
        if (substr($_POST['stDataInicial'],6,4) <> $_REQUEST["stExercicio"]) {
            SistemaLegado::exibeAviso(urlencode("A Data Inicial deve ser do ano '".$_REQUEST["stExercicio"] . "'!"),"","erro");
            $js .="f.stDataInicial.value = '' ;\n" ;
            $js .= "f.stDataInicial.focus(); \n";
            SistemaLegado::executaFrameOculto($js);
        }
        if ($validaDataInicial[1] > 12) {
            SistemaLegado::exibeAviso(urlencode("O mês deve ser inferior a 12  !"),"","erro");
            $js .="f.stDataInicial.value = '' ;\n" ;
            $js .= "f.stDataInicial.focus(); \n";
            SistemaLegado::executaFrameOculto($js);
        }
        if ($validaDataInicial[0] > 31) {
            SistemaLegado::exibeAviso(urlencode("O dia deve ser inferior a 31  !"),"","erro");
            $js .="f.stDataInicial.value = '' ;\n" ;
            $js .= "f.stDataInicial.focus(); \n";
            SistemaLegado::executaFrameOculto($js);
        }
    }
    if ($_POST['stDataFinal']) {
        if (substr($_POST['stDataFinal'],6,4) <> $_REQUEST["stExercicio"]) {
            SistemaLegado::exibeAviso(urlencode("A Data Final deve ser do ano '".$_REQUEST["stExercicio"] . "'!"),"","erro");
            $js .="f.stDataFinal.value = '' ;\n" ;
            $js .= "f.stDataFinal.focus(); \n";
            SistemaLegado::executaFrameOculto($js);
        }
        if ($validaDataInicial[1] == $validaDataFinal[1]) {
            if ($validaDataInicial[0] > $validaDataFinal[0]) {
                SistemaLegado::exibeAviso(urlencode("A Data final deve ser maior que a data inicial dia, ".$validaDataFinal[0]."/".$validaDataFinal[1]."/".$validaDataFinal[2]." é menor que ".$validaDataInicial[0]."/".$validaDataInicial[1]."/".$validaDataInicial[2]."  !"),"","erro");
                $js .="f.stDataFinal.value = '' ;\n" ;
                $js .= "f.stDataFinal.focus(); \n";
                SistemaLegado::executaFrameOculto($js);
            }
        }
        if ($validaDataInicial[1] > $validaDataFinal[1]) {
            SistemaLegado::exibeAviso(urlencode("A Data final deve ser maior que a data inicial dia, ".$validaDataFinal[0]."/".$validaDataFinal[1]."/".$validaDataFinal[2]." é menor que ".$validaDataInicial[0]."/".$validaDataInicial[1]."/".$validaDataInicial[2]."  !"),"","erro");
            $js .="f.stDataFinal.value = '' ;\n" ;
            $js .= "f.stDataFinal.focus(); \n";
            SistemaLegado::executaFrameOculto($js);
        }
        if ($validaDataFinal[1] > 12) {
            SistemaLegado::exibeAviso(urlencode("O mês deve ser inferior a 12  !"),"","erro");
            $js .="f.stDataFinal.value = '' ;\n" ;
            $js .= "f.stDataFinal.focus(); \n";
            SistemaLegado::executaFrameOculto($js);
        }
        if ($validaDataFinal[0] > 31) {
            SistemaLegado::exibeAviso(urlencode("O dia deve ser inferior a 31  !"),"","erro");
            $js .="f.stDataFinal.value = '' ;\n" ;
            $js .= "f.stDataFinal.focus(); \n";
            SistemaLegado::executaFrameOculto($js);
        }

    }
    break;

default:
    $obRegra->setCodEntidade($stEntidade);
    $obRegra->obREmpenhoEmpenho->setExercicio       ($arFiltro['inExercicio']);
    $obRegra->obREmpenhoEmpenho->setDtEmpenhoInicial($arFiltro['stDataInicialEmissao']);
    $obRegra->obREmpenhoEmpenho->setDtEmpenhoFinal  ($arFiltro['stDataFinalEmissao']);

    $stDataInicial = "01/01/".$arFiltro['inExercicio'];

    $obRegra->setDataInicialAnulacao         ($stDataInicial);
    $obRegra->setDataFinalAnulacao           ($arFiltro['stDataSituacao']);
    $obRegra->setDataInicialLiquidacao       ($stDataInicial);
    $obRegra->setDataFinalLiquidacao         ($arFiltro['stDataSituacao']);
    $obRegra->setDataInicialEstornoLiquidacao($stDataInicial);
    $obRegra->setDataFinalEstornoLiquidacao  ($arFiltro['stDataSituacao']);
    $obRegra->setDataInicialPagamento        ($stDataInicial);
    $obRegra->setDataFinalPagamento          ($arFiltro['stDataSituacao']);
    $obRegra->setDataInicialEstornoPagamento ($stDataInicial);
    $obRegra->setDataFinalEstornoPagamento   ($arFiltro['stDataSituacao']);
    $obRegra->setTipoEmpenho                 ($arFiltro['inCodTipoEmpenho']);

    if (Sessao::getExercicio() > '2015') {
        $obRegra->setCentroCusto             ($arFiltro['inCentroCusto']);
    }

    $obRegra->obREmpenhoEmpenho->setCodEmpenhoInicial($arFiltro['inCodEmpenhoInicial']);
    $obRegra->obREmpenhoEmpenho->setCodEmpenhoFinal  ($arFiltro['inCodEmpenhoFinal']);
    $obRegra->obREmpenhoEmpenho->setCodDespesa       ($arFiltro['inCodDotacao']);
    $obRegra->obREmpenhoEmpenho->obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setCodEstrutural($arFiltro['inCodDespesa']);
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arFiltro['inNumOrgao']);
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->setNumeroUnidade($arFiltro['inNumUnidade']);
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso($arFiltro['inCodRecurso']);

    if ($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao']) {
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setDestinacaoRecurso($arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao']);
    }
    $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoRecurso->setCodDetalhamento($arFiltro['inCodDetalhamento']);
    $obRegra->setOrdenacao                        ($arFiltro['inOrdenacao']);
    $obRegra->obREmpenhoEmpenho->obRCGM->setNumCGM($arFiltro['inCodFornecedor']);
    $obRegra->setSituacao                         ($arFiltro['inSituacao']);

    $obRegra->geraRecordSet($rsSituacaoEmpenho);

    Sessao::write('rsRecordSet', $rsSituacaoEmpenho);
    $obRegra->obRRelatorio->executaFrameOculto("OCGeraRelatorioSituacaoEmpenho.php");
    break;
}

?>
