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
    * Página de Formulario de Oculto de Previsão Despesa
    * Data de Criação   : 27/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 30824 $
    $Name$
    $Author: luciano $
    $Date: 2007-07-26 17:29:07 -0300 (Qui, 26 Jul 2007) $

    * Casos de uso: uc-02.01.06
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php"                     );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php"       );
include_once( CAM_GF_ORC_COMPONENTES."MontaDotacaoOrcamentaria.class.php" );

$obROrcamentoDespesa                 = new ROrcamentoDespesa;
$obRConfiguracaoOrcamento   = new ROrcamentoConfiguracao;
$obMascara                  = new Mascara;
$obMontaDotacaoOrcamentaria = new MontaDotacaoOrcamentaria;

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
switch ($stCtrl) {
    case "buscaOrgaoUnidade":
        $obMontaDotacaoOrcamentaria->buscaValoresUnidade();
    break;

    case "preencheUnidade":
        $obMontaDotacaoOrcamentaria->preencheUnidade();
    break;

    case "preencheMascara":
        $obMontaDotacaoOrcamentaria->preencheMascara();
    break;

    case "mascaraClassificacaoFiltro":
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodClassificacao'] );
        $js .= "f.inCodClassificacao.value = '".$arMascClassificacao[1]."'; \n";
        SistemaLegado::executaFrameOculto( $js );
    break;

    case "mascaraClassificacao":
        //monta mascara da RUBRICA DE DESPESA

        if (! $_GET['inCodDespesa']) {
               $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "&nbsp;";';
               //$js .= 'd.getElementById("stDotacaoOrcamentaria").innerHTML = "&nbsp;";';
        } else {
            $arMascClassificacao = Mascara::validaMascaraDinamica( $_REQUEST['stMascClassificacao'] , $_REQUEST['inCodDespesa'] );
            $js .= "f.inCodDespesa.value = '".$arMascClassificacao[1]."'; \n";

            $stDescricao = '';
            //busca DESCRICAO DA RUBRICA DE DESPESA
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascara          ( $_REQUEST['stMascClassificacao'] );
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
            if ($stDescricao != "") {
                $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$stDescricao.'";';
            } else {
                $null = "&nbsp;";
                $js .= 'f.inCodDespesa.value = "";';
                $js .= 'f.inCodDespesa.focus();';
                $js .= 'd.getElementById("stDescricaoDespesa").innerHTML = "'.$null.'";';
                $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
            }

            //monta MASCARA COMPLETA DA DOTACAO ORCAMENTARIA
            $stDespesaDesmascarado = $arMascClassificacao[1];
            $obMascara->desmascaraDado( $stDespesaDesmascarado );
            $obRConfiguracaoOrcamento->consultarConfiguracao();
            $stMascaraDotacao = $obRConfiguracaoOrcamento->getMascDespesa();
            if (!$_REQUEST['stDotacaoOrcamentaria']) {
                $arMascDotacao    = preg_split( "/[^a-zA-Z0-9]/", $stMascaraDotacao  );
                foreach ($arMascDotacao as $key => $valor) {
                    $arMascDotacao[$key] = 0;
                }
            } else {
                $arMascDotacao = preg_split( "/[^a-zA-Z0-9]/", $_REQUEST['stDotacaoOrcamentaria'] );
            }

            if ($stDescricao != "") {
                $arMascDotacao[6] = $stDespesaDesmascarado;
            } else {
                $arMascDotacao[6] = 0;
            }

            foreach ($arMascDotacao as $key => $valor) {
                $stMascDotacao .= $valor.".";
            }
            $stMascDotacao = substr( $stMascDotacao, 0, strlen($stMascDotacao) - 1 );
            $arMascDotacao = Mascara::validaMascaraDinamica( $stMascaraDotacao , $stMascDotacao );
            $js .= "f.stDotacaoOrcamentaria.value = '".$arMascDotacao[1]."'; \n";
        }
        echo $js ;
    break;

    case 'buscaRecurso':
        if ( strlen(trim($_POST['inCodRecurso'])) > 0 ) {
            $obRConfiguracaoOrcamento->consultarConfiguracao();
            $arMascRecurso = Mascara::validaMascaraDinamica( $obRConfiguracaoOrcamento->getMascRecurso() , $_POST['inCodRecurso'] );

            $obROrcamentoDespesa->obROrcamentoRecurso->setCodRecurso( $_POST['inCodRecurso'] );
            $obROrcamentoDespesa->obROrcamentoRecurso->listar( $rsRecurso );
            if ( $rsRecurso->getNumLinhas() > -1 ) {
               $js .= 'f.inCodRecurso.value = "'.$arMascRecurso[1].'";';
               $js .= 'd.getElementById("stDescricaoRecurso").innerHTML = "'.$rsRecurso->getCampo("nom_recurso").'";';
            } else {
               $null = "&nbsp;";
               $js .= 'f.inCodRecurso.value = "";';
               $js .= 'f.inCodRecurso.focus();';
               $js .= 'd.getElementById("stDescricaoRecurso").innerHTML = "'.$null.'";';
               $js .= "alertaAviso('@Valor inválido. (".$_POST['inCodRecurso'].")','form','erro','".Sessao::getId()."');";
            }
        } else {
            $null = "&nbsp;";
            $js .= 'f.inCodRecurso.value = "";';
            $js .= 'd.getElementById("stDescricaoRecurso").innerHTML = "'.$null.'";';
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case "codigoReduzido":
        if ($_POST['inCodReduzido']) {
            $obROrcamentoDespesa->setCodDespesa( $_POST['inCodReduzido'] );
            $obROrcamentoDespesa->listarDespesa( $rsCodReduzido );

            if ( $rsCodReduzido->getNumLinhas() > -1 ) {
                $js .= 'd.getElementById("stDescricaoReduzido").innerHTML = "'.$rsCodReduzido->getCampo("descricao").'";';
            } else {
                $null = "&nbsp;";
                $js .= 'f.inCodReduzido.value = "";';
                $js .= 'f.inCodReduzido.focus();';
                $js .= 'd.getElementById("stDescricaoReduzido").innerHTML = "'.$null.'";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST['inCodReduzido'].")','form','erro','".Sessao::getId()."');";
            }
        } else {
            $null = "&nbsp;";
            $js .= 'f.inCodReduzido.value = "";';
            $js .= 'd.getElementById("stDescricaoReduzido").innerHTML = "'.$null.'";';
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case 'preencheInner':
        $obMontaDotacaoOrcamentaria->setExecutaFrame( false );
        $js .= $obMontaDotacaoOrcamentaria->preencheUnidade();
        $js .= 'd.getElementById("stDescricaoRecurso").innerHTML  = "'.$_POST["stDescRecurso"].'";';
        SistemaLegado::executaFrameOculto($js);
    case 'calculaPorcentagem':
        $arVlPeriodo = Sessao::read('arVlPeriodo');
        $vlTotal = str_replace(".","",$_REQUEST['nuValorOriginal']);
        $vlTotal = str_replace(",",".",$vlTotal);
        $vlSomatorioPeriodo = 0;
        if ($vlTotal > 0.00) {
            for ($x = 1; $x <= $_REQUEST['inNumCampos']; $x++) {
                $vlPeriodo = str_replace(".","",$arVlPeriodo[$x]);
                $vlPeriodo = str_replace(",",".",$vlPeriodo);

                $flPorcentagem = ( $vlPeriodo * 100 ) / $vlTotal;
                $vlSomatorioPeriodo += $vlPeriodo;

                $js .= 'f.vlValor_'.$x.'.value = "'.$arVlPeriodo[$x].'";';
                $js .= 'if (document.getElementById(\'lblvlValor_'.$x.'\')) { ';
                $js .= '     document.getElementById(\'lblvlValor_'.$x.'\').innerHTML = \''.$arVlPeriodo[$x].'\'; ';
                $js .= '}';
                $js .= 'f.vlPorcentagem_'.$x.'.value = "'.number_format($flPorcentagem, 2, ",", ".").'";';
                $js .= 'if (document.getElementById(\'lblvlPorcentagem_'.$x.'\')) { ';
                $js .= '     document.getElementById(\'lblvlPorcentagem_'.$x.'\').innerHTML = \''.number_format($flPorcentagem, 2, ",", ".").'%\'; ';
                $js .= '}';
            }
            $js .= 'f.TotalValor.value = "'.number_format($vlSomatorioPeriodo,2, ",", ".").'";';
            $js .= 'document.getElementById(\'lblTotalValor\').innerHTML = "'.number_format($vlSomatorioPeriodo,2, ",", ".").'";';
            $js .= 'f.TotalPorcento.value = "'.$vlSomatorioPeriodo ? "100" : "0".'";';
            $js .= 'document.getElementById(\'lvlTotalPorcento\').innerHTML = "'.$vlSomatorioPeriodo ? "100" : "0".'%";';
            SistemaLegado::executaFrameOculto($js);
        }

    break;
    case 'preencheMetas':
        $arVlPeriodo = Sessao::read('arVlPeriodo');
        $js = "";
        for ($x = 1; $x <= $_GET['inNumCampos']; $x++) {
            $vlValor = $arVlPeriodo[$x];
            if ($vlValor != '') {
                $js .= 'f.vlValor_'.$x.'.value = \''.$vlValor.'\'; ';
                $js .= 'if (document.getElementById(\'lblvlValor_'.$x.'\')) { ';
                $js .= '    document.getElementById(\'lblvlValor_'.$x.'\').innerHTML = \''.$vlValor.'\'; ';
                $js .= '}';
            } else {
                $js .= "f.vlValor_".$x.".value = '0.00';";
                $js .= 'if (document.getElementById(\'lblvlValor_'.$x.'\')) { ';
                $js .= '    document.getElementById(\'lblvlValor_'.$x.'\').innerHTML = \'0,00\'; ';
                $js .= '}';
            }
        }
        echo $js;

    break;
    case 'mudaPorcentagem':
        $arVlPeriodo = Sessao::Read('arVlPeriodo');
        $js = "";
        $vlTotal = str_replace(".","",$_GET['nuValorOriginal']);
        $vlTotal = str_replace(",",".",$vlTotal);

        if ($vlTotal > 0.00) {
            $vlSomatorioPeriodo = 0;
            $vlSomatorioPercentual = 0;
            for ($x = 1; $x <= $_GET['inNumCampos']; $x++) {
                $vlValor = $_GET["vlValor_$x"] ? $_GET["vlValor_$x"] : $arVlPeriodo[$x];
                $vlPeriodo = str_replace(".","",$vlValor);
                $vlPeriodo = str_replace(",",".",$vlPeriodo);

                $flPorcentagem = bcdiv(bcmul($vlPeriodo,100,6),$vlTotal,6);
                $vlSomatorioPeriodo = bcadd($vlPeriodo,$vlSomatorioPeriodo,6);
                $vlSomatorioPercentual = bcadd($flPorcentagem,$vlSomatorioPercentual,6);

                $js .= 'f.vlPorcentagem_'.$x.'.value = "'.number_format($flPorcentagem, 2, ",", ".").'";';
                $js .= 'if (document.getElementById(\'lblvlPorcentagem_'.$x.'\')) { ';
                $js .= '     document.getElementById(\'lblvlPorcentagem_'.$x.'\').innerHTML = \''.number_format($flPorcentagem, 2, ",", ".").'%\'; ';
                $js .= '}';
                if ($_GET["vlValor_$x"] != '') {
                    $js .= 'f.vlValor_'.$x.'.value = "'.$_GET["vlValor_$x"].'";';
                    $js .= 'if (document.getElementById(\'lblvlValor_'.$x.'\')) { ';
                    $js .= '     document.getElementById(\'lblvlValor_'.$x.'\').innerHTML = \''.$_GET["vlValor_$x"].'\'; ';
                    $js .= '}';
                } else {
                    $js .= "f.vlValor_".$x.".value = '0.00';";
                    $js .= 'if (document.getElementById(\'lblvlValor_'.$x.'\')) { ';
                    $js .= '     document.getElementById(\'lblvlValor_'.$x.'\').innerHTML = \'0,00\'; ';
                    $js .= '}';
                }
            }
            $js .= 'f.TotalValor.value = "'.number_format($vlSomatorioPeriodo,2, ",", ".").'";';
            $js .= 'document.getElementById(\'lblTotalValor\').innerHTML = "'.number_format($vlSomatorioPeriodo,2, ",", ".").'";';
            $js .= 'f.TotalPorcento.value = "'.number_format($vlSomatorioPercentual, 2, ",", ".").'";';
            $js .= 'document.getElementById(\'lblTotalPorcento\').innerHTML = "'.number_format($vlSomatorioPercentual, 2, ",", ".").'%";';
        } else {
            for ($x = 1; $x <= $_GET['inNumCampos']; $x++) {
                $js .= 'f.vlValor_'.$x.'.value = "0,00";';
                $js .= 'if (document.getElementById(\'lblvlValor_'.$x.'\')) { ';
                $js .= '     document.getElementById(\'lblvlValor_'.$x.'\').innerHTML = \'0,00\'; ';
                $js .= '}';
                $js .= 'f.vlPorcentagem_'.$x.'.value = "0,00";';
                $js .= 'if (document.getElementById(\'lblvlPorcentagem_'.$x.'\')) { ';
                $js .= '     document.getElementById(\'lblvlPorcentagem_'.$x.'\').innerHTML = \'0,00%\'; ';
                $js .= '}';
            }
            $js .= 'f.TotalValor.value = "0,00";';
            $js .= 'document.getElementById(\'lblTotalValor\').innerHTML = "0,00";';
            $js .= 'f.TotalPorcento.value = "0,00";';
            $js .= 'document.getElementById(\'lblTotalPorcento\').innerHTML = "0,00%";';
        }
        echo $js;
    break;
    case 'mudaValor':
        $js = "";
        $vlTotal = str_replace(".","",$_GET['nuValorOriginal']);
        $vlTotal = str_replace(",",".",$vlTotal);
        if ($vlTotal > 0.00) {
            $vlSomatorioPeriodo = 0;
            $vlSomatorioPercentual = 0;
            for ($x = 1; $x <= $_GET['inNumCampos']; $x++) {
                $vlPorcentagem = str_replace(".","",$_GET["vlPorcentagem_".$x.""]);
                $vlPorcentagem = str_replace(",",".",$vlPorcentagem);

                $vlPeriodo = bcdiv(bcmul($vlTotal,$vlPorcentagem,6),100,6);
                $vlSomatorioPeriodo = bcadd($vlSomatorioPeriodo,$vlPeriodo,6);
                $vlSomatorioPercentual = bcadd($vlSomatorioPercentual,$vlPorcentagem,6);

                if (!$_GET["hdnBlock_".$x.""]) { // Se o período ainda não fechou.
                    $js .= 'f.vlValor_'.$x.'.value = "'.number_format($vlPeriodo, 2, ",", ".").'";';
                    $js .= 'if (document.getElementById(\'lblvlValor_'.$x.'\')) { ';
                    $js .= '     document.getElementById(\'lblvlValor_'.$x.'\').innerHTML = \''.number_format($vlPeriodo, 2, ",", ".").'\'; ';
                    $js .= '}';
                    if ($_GET["vlPorcentagem_".$x.""] != '') {
                        $js .= 'f.vlPorcentagem_'.$x.'.value = "'.$_GET["vlPorcentagem_".$x.""].'";';
                        $js .= 'if (document.getElementById(\'lblvlPorcentagem_'.$x.'\')) { ';
                        $js .= '     document.getElementById(\'lblvlPorcentagem_'.$x.'\').innerHTML = \''.$_GET["vlPorcentagem_".$x.""].'%\'; ';
                        $js .= '}';
                    } else {
                        $js .= "f.vlPorcentagem_".$x.".value = '0.00';";
                        $js .= 'if (document.getElementById(\'lblvlPorcentagem_'.$x.'\')) { ';
                        $js .= '     document.getElementById(\'lblvlPorcentagem_'.$x.'\').innerHTML = \'0,00%\'; ';
                        $js .= '}';
                    }
                }
            }
            $js .= 'f.TotalValor.value = "'.number_format($vlSomatorioPeriodo,2, ",", ".").'";';
            $js .= 'document.getElementById(\'lblTotalValor\').innerHTML = "'.number_format($vlSomatorioPeriodo,2, ",", ".").'";';
            $js .= 'f.TotalPorcento.value = "'.number_format($vlSomatorioPercentual, 2, ",", ".").'";';
            $js .= 'document.getElementById(\'lblTotalPorcento\').innerHTML = "'.number_format($vlSomatorioPercentual, 2, ",", ".").'%";';
        } else {
            for ($x = 1; $x <= $_GET['inNumCampos']; $x++) {
                $js .= 'f.vlValor_'.$x.'.value = "0,00";';
                $js .= 'if (document.getElementById(\'lblvlValor_'.$x.'\')) { ';
                $js .= '     document.getElementById(\'lblvlValor_'.$x.'\').innerHTML = \'0,00\'; ';
                $js .= '}';
                $js .= 'f.vlPorcentagem_'.$x.'.value = "0,00";';
                $js .= 'if (document.getElementById(\'lblvlPorcentagem_'.$x.'\')) { ';
                $js .= '     document.getElementById(\'lblvlPorcentagem_'.$x.'\').innerHTML = \'0,00%\'; ';
                $js .= '}';
            }
            $js .= 'f.TotalValor.value = "0,00";';
            $js .= 'document.getElementById(\'lblTotalValor\').innerHTML = "0,00";';
            $js .= 'f.TotalPorcento.value = "0,00";';
            $js .= 'document.getElementById(\'lblTotalPorcento\').innerHTML = "0,00%";';
        }
        echo $js;
    break;
}
?>
