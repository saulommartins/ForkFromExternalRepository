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
    * Oculto da tela de Filtro do relatório Relação de Despesa Orçamentária
    * Data de Criação   : 02/04/2009

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Pacuslki Schitz

    * @package URBEM
    * @subpackage

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"            );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                     );
include_once( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                         );

$obROrcamentoDespesa        = new ROrcamentoDespesa;

$obROrcamentoUnidadeOrcamentaria    = new ROrcamentoUnidadeOrcamentaria;
$obROrcamentoClassificacaoDespesa   = new ROrcamentoClassificacaoDespesa;

switch ($_REQUEST['stCtrl']) {
    case "MontaUnidade":
        if ($_REQUEST["inCodOrgao"]) {
            $stCombo  = "inCodUnidade";
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
            $stJs .= "f.".$stCombo."Txt.value='$stSelecionado';\n";

            $obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST['inCodOrgao']);
            $obROrcamentoUnidadeOrcamentaria->listar($rsCombo);
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
        }

        $stJs .= $js;
        SistemaLegado::executaFrameOculto( $stJs );
    break;

    case "mascaraClassificacao":
        //monta mascara da RUBRICA DE DESPESA
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodDespesa'] );
        $js .= "f.inCodDespesa.value = '".$arMascClassificacao[1]."'; \n";

        //busca DESCRICAO DA RUBRICA DE DESPESA
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascara          ( $_POST['stMascClassificacao'] );
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
        if ($stDescricao != "") {
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'";';
        } else {
            $null = "&nbsp;";
            $js .= 'f.inCodDespesa.value = "";';
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
            $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case 'buscaDotacao':
        include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioSituacaoEmpenho.class.php";
        $obRegra = new REmpenhoRelatorioSituacaoEmpenho;
        $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setCodDespesa( $_REQUEST["inCodDotacao"] );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->setExercicio( Sessao::getExercicio() );
        $obRegra->obREmpenhoEmpenho->obREmpenhoAutorizacaoEmpenho->obROrcamentoDespesa->obROrcamentoEntidade->setCodigoEntidade( implode(',',$_REQUEST['inCodEntidade']) );
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

}
?>
