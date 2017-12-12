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
    * Página de Formulario de Oculto de Previsão Receita Dedutora
    * Data de Criação   : 19/10/2007

    * @author Anderson cAko Konze

    * Casos de uso: uc-02.01.06

    $Id: OCDedutora.php 60012 2014-09-25 17:09:54Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoReceita.class.php"               );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );

$obROrcamentoReceita               = new ROrcamentoReceita;
$obRConfiguracaoOrcamento = new ROrcamentoConfiguracao;
$obMascara                = new Mascara;

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

switch ($stCtrl) {

    case "mascaraClassificacaoFiltro":
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodClassificacao'] );
        $js .= "f.inCodClassificacao.value = '".$arMascClassificacao[1]."'; \n";
        SistemaLegado::executaFrameOculto( $js );
    break;

    case "mascaraClassificacao":
        //monta mascara da RUBRICA DE DESPESA
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodReceita'] );
        $js .= "f.inCodReceita.value = '".$arMascClassificacao[1]."'; \n";

        //busca DESCRICAO DA RUBRICA DE RECEITA
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascara          ( $_POST['stMascClassificacao'] );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $arMascClassificacao[1]       );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->recuperaDescricaoReceita( $stDescricao );
        if ($stDescricao != "") {
            $js .= 'd.getElementById("stDescricaoReceita").innerHTML = "'.$stDescricao.'";';
        } else {
            $null = "&nbsp;";
            $js .= "f.inCodReceita.value = ''; \n ";
            $js .= "f.inCodReceita.focus(); \n ";
            $js .= "d.getElementById('stDescricaoReceita').innerHTML = '".$null."'; \n ";
            $js .= "SistemaLegado::alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."'); \n ";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case "codigoReduzido":
        if ($_POST['inCodReduzido']) {
        $obROrcamentoReceita->setCodReceita( $_POST['inCodReduzido'] );
        $obROrcamentoReceita->listarReceita( $rsCodReduzido );

            if ( $rsCodReduzido->getNumLinhas() > -1 ) {
                $js .= 'd.getElementById("stDescricaoReduzido").innerHTML = "'.$rsCodReduzido->getCampo("descricao").'";';
            } else {
                $null = "&nbsp;";
                $js .= "f.inCodReduzido.value = ''; \n ";
                $js .= "f.inCodReduzido.focus(); \n ";
                $js .= "d.getElementById('tDescricaoReduzido').innerHTML = '".$null."'; \n ";
                $js .= "alertaAviso('@Valor inválido. (".$_POST['inCodReduzido'].")','form','erro','".Sessao::getId()."'); \n ";
            }
        } else {
            $null = "&nbsp;";
            $js .= 'f.inCodReduzido.value = "";';
            $js .= 'd.getElementById("stDescricaoReduzido").innerHTML = "'.$null.'";';
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case 'buscaRecurso':
        if ( strlen( trim($_POST['inCodRecurso']) ) > 0 ) {
            $obRConfiguracaoOrcamento->setExercicio(Sessao::getExercicio());
            $obRConfiguracaoOrcamento->consultarConfiguracao();
            $arMascRecurso = Mascara::validaMascaraDinamica( $obRConfiguracaoOrcamento->getMascRecurso() , $_POST['inCodRecurso'] );

            $obROrcamentoReceita->obROrcamentoRecurso->setCodRecurso( $_POST['inCodRecurso'] );
            $obROrcamentoReceita->obROrcamentoRecurso->setExercicio( Sessao::getExercicio() );
            $obROrcamentoReceita->obROrcamentoRecurso->listar( $rsRecurso );
            if ( $rsRecurso->getNumLinhas() > -1 ) {
                $js .= 'f.inCodRecurso.value = "'.$arMascRecurso[1].'";';
                $js .= 'd.getElementById("stDescricaoRecurso").innerHTML = "'.$rsRecurso->getCampo("nom_recurso").'";';
            } else {
                $null = "&nbsp;";
                $js .= "f.inCodRecurso.value = ''; \n ";
                $js .= "f.inCodRecurso.focus(); \n ";
                $js .= "d.getElementById('stDescricaoRecurso').innerHTML = '".$null."'; \n ";
                $js .= "alertaAviso('@Valor inválido. (".$_POST['inCodRecurso'].")','form','erro','".Sessao::getId()."'); \n ";
            }
        } else {
            $null = "&nbsp;";
            $js .= 'f.inCodRecurso.value = "";';
            $js .= 'd.getElementById("stDescricaoRecurso").innerHTML = "'.$null.'";';
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case 'preencheInner':
        //busca DESCRICAO DA RUBRICA DE DESPESA
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->setMascClassificacao( $_POST['inCodReceita'] );
        $obROrcamentoReceita->obROrcamentoClassificacaoReceita->consultar( $rsRubrica );
        if ( $rsRubrica->getNumLinhas() > -1 ) {
            $js .= 'd.getElementById("stDescricaoReceita").innerHTML = "'.$rsRubrica->getCampo("descricao").'";';
        }

        $js .= 'd.getElementById("stDescricaoRecurso").innerHTML  = "'.$_POST["stDescricaoRecurso"].'";';
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
                $js .= 'f.vlPorcentagem_'.$x.'.value = "'.number_format($flPorcentagem, 2, ",", ".").'";';
            }
            $js .= 'f.TotalValor.value = "'.number_format($vlSomatorioPeriodo,2, ",", ".").'";';
            $js .= 'f.TotalPorcento.value = "'.$vlSomatorioPeriodo ? "100" : "0".'";';
            SistemaLegado::executaFrameOculto($js);
        }
    break;
    case 'preencheMetas':
        $arVlPeriodo = Sessao::read('arVlPeriodo');
        $js = "";
        for ($x = 1; $x <= $_GET['inNumCampos']; $x++) {
            $vlValor = $arVlPeriodo[$x];
            if ($vlValor != '') {
                $js .= 'f.vlValor_'.$x.'.value = \''.$vlValor.'\';';
            } else $js .= "f.vlValor_".$x.".value = '0.00';";
        }
        echo $js;

    break;
    case 'mudaPorcentagem':
        $arVlPeriodo = Sessao::read('arVlPeriodo');
        $js = "";
        $vlTotal = str_replace(".","",$_GET['nuValorOriginal']);
        $vlTotal = str_replace(",",".",$vlTotal);

        if ($vlTotal != 0.00) {
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
                if ($_GET["vlValor_$x"] != '') {
                    $js .= 'f.vlValor_'.$x.'.value = "'.$_GET["vlValor_$x"].'";';
                } else $js .= "f.vlValor_".$x.".value = '0.00';";

            }
            $js .= 'f.TotalValor.value = "'.number_format($vlSomatorioPeriodo,2, ",", ".").'";';
            $js .= 'f.TotalPorcento.value = "'.number_format($vlSomatorioPercentual, 2, ",", ".").'";';
        } else {
            for ($x = 1; $x <= $_GET['inNumCampos']; $x++) {
                $js .= 'f.vlValor_'.$x.'.value = "0,00";';
                $js .= 'f.vlPorcentagem_'.$x.'.value = "0,00";';
            }
            $js .= 'f.TotalValor.value = "0,00";';
            $js .= 'f.TotalPorcento.value = "0,00";';
        }
        echo $js;
    break;
    case 'mudaValor':
        $js = "";
        $vlTotal = str_replace(".","",$_GET['nuValorOriginal']);
        $vlTotal = str_replace(",",".",$vlTotal);
        if ($vlTotal != 0.00) {
            $vlSomatorioPeriodo = 0;
            $vlSomatorioPercentual = 0;
            for ($x = 1; $x <= $_GET['inNumCampos']; $x++) {
                $vlPorcentagem = str_replace(".","",$_GET["vlPorcentagem_".$x.""]);
                $vlPorcentagem = str_replace(",",".",$vlPorcentagem);

                $vlPeriodo = bcdiv(bcmul($vlTotal,$vlPorcentagem,6),100,6);
                $vlSomatorioPeriodo = bcadd($vlSomatorioPeriodo,$vlPeriodo,6);
                $vlSomatorioPercentual = bcadd($vlSomatorioPercentual,$vlPorcentagem,6);

                $js .= 'f.vlValor_'.$x.'.value = "'.number_format($vlPeriodo, 2, ",", ".").'";';
                if ($_GET["vlPorcentagem_".$x.""] != '') {
                    $js .= 'f.vlPorcentagem_'.$x.'.value = "'.$_GET["vlPorcentagem_".$x.""].'";';
                } else $js .= "f.vlPorcentagem_".$x.".value = '0.00';";
            }
            $js .= 'f.TotalValor.value = "'.number_format($vlSomatorioPeriodo,2, ",", ".").'";';
            $js .= 'f.TotalPorcento.value = "'.number_format($vlSomatorioPercentual, 2, ",", ".").'";';
        } else {
            for ($x = 1; $x <= $_GET['inNumCampos']; $x++) {
                $js .= 'f.vlValor_'.$x.'.value = "0,00";';
                $js .= 'f.vlPorcentagem_'.$x.'.value = "0,00";';
            }
            $js .= 'f.TotalValor.value = "0,00";';
            $js .= 'f.TotalPorcento.value = "0,00";';
        }
        echo $js;
    break;

    case 'buscaDedutora':
        include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoReceita.class.php" );                
        if ($_GET['inCodReceita']) {

            if ($_POST['stMascClassificacao']) {
                $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST[ $_GET['stNomCampoCod'] ] );
                $_POST[ $_GET['stNomCampoCod'] ] = $arMascClassificacao[1];
                $js = "if( f.inCodReceita ) f.inCodReceita.value = '".$arMascClassificacao[1]."'; \n";
            }

            $obROrcamentoClassificacaoReceita = new ROrcamentoClassificacaoReceita;
            $obROrcamentoClassificacaoReceita->setDedutora ( true );

            $obROrcamentoClassificacaoReceita->setExercicio( Sessao::getExercicio() );
            $obROrcamentoClassificacaoReceita->setMascClassificacao( $_GET['inCodReceita'] );
            $obROrcamentoClassificacaoReceita->setListarAnaliticas('true');
            $obROrcamentoClassificacaoReceita->consultarReceitaAnalitica( $rsLista );
            if ( $rsLista->getCampo("tipo_nivel_conta") == 'A' ) {
                $stDescricao = $rsLista->getCampo("descricao");
            } else {
                $js  = ' alertaAviso("Classificação informada inválida, informe uma classificação de dedutora analítica!('.$_GET['inCodReceita'].')","","","'.Sessao::getId().'");';                                
                $js .= ' f.inCodReceita.value = "";';
                $js .= ' d.getElementById("stDescricaoReceita").innerHTML = "&nbsp;";';
                echo $js;
            }            
        }
    break;

}

?>
