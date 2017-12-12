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
/*
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: OCRelacaoEmpenho.php 59612 2014-09-02 12:00:51Z gelson $
*
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"           );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"            );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                     );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"       );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioEmpenhadoPagoLiquidado.class.php"  );
include_once( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                         );

$obROrcamentoDespesa        = new ROrcamentoDespesa;
$obRConfiguracaoOrcamento   = new ROrcamentoConfiguracao;

$obREntidade                = new ROrcamentoEntidade;
$obREntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
$obREntidade->listarUsuariosEntidade( $rsTotalEntidades , " ORDER BY cod_entidade" );
$obRRelatorio                       = new RRelatorio;
$obREmpenhoEmpenhadoPagoLiquidado   = new REmpenhoRelatorioEmpenhadoPagoLiquidado;
$obROrcamentoUnidadeOrcamentaria    = new ROrcamentoUnidadeOrcamentaria;
$obROrcamentoRecurso                = new ROrcamentoRecurso;
$obROrcamentoClassificacaoDespesa   = new ROrcamentoClassificacaoDespesa;

switch ($_REQUEST['stCtrl']) {
    case "MontaUnidade":
        if ($_REQUEST["inCodOrgao"]) {
            $stCombo  = "inCodUnidade";
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
            $stJs .= "f.".$stCombo."Txt.value='$stSelecionado';\n";
            $obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST['inCodOrgao']);
            $obROrcamentoUnidadeOrcamentaria->consultar( $rsCombo , $stFiltro,"", $boTransacao);
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
//            $js .= 'f.inCodDespesa.focus();';
            $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
            $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case 'buscaFornecedor':
        if ($_POST["inCodFornecedor"] != "") {
            $obRCGM = new RCGM;
            $obRCGM->setNumCGM( $_POST["inCodFornecedor"] );
            $obRCGM->listar( $rsCGM );
            $stNomFornecedor = $rsCGM->getCampo( "nom_cgm" );
            if (!$stNomFornecedor) {
                $js .= 'f.inCodFornecedor.value = "";';
//                $js .= 'f.inCodFornecedor.focus();';
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inCodFornecedor"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
            }
        } else $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
        SistemaLegado::executaFrameOculto($js);
    break;
    case 'mostraSpanContaBanco':
        if ($_POST["inSituacaoTxt"] == "2") {
            include_once( CAM_GF_CONT_COMPONENTES."IPopUpContaBancoEntidades.class.php");

            $obIPopUpContaBancoEntidades = new IPopUpContaBancoEntidades(Sessao::read('obCmbEntidades'));

            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obIPopUpContaBancoEntidades );

            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );
        } else {
            $stHTML = "";
        }
        SistemaLegado::executaFrameOculto("d.getElementById('spnContaBanco').innerHTML = '".$stHTML."';");
    break;

    case 'buscaDotacao':
        include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioSituacaoEmpenho.class.php";
        $obRegra = new REmpenhoRelatorioSituacaoEmpenho;
        $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM     ( Sessao::read('numCgm') );
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

}
?>
