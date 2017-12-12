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
    * Página Oculta de Filtro de Pesquisa
    * Data de Criação   : 21/02/2005

    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 30762 $
    $Name$
    $Autor: $
    $Date: 2006-10-27 16:10:02 -0300 (Sex, 27 Out 2006) $

    * Casos de uso: uc-02.01.22
*/

/*
$Log$
Revision 1.9  2006/10/27 19:10:02  hboaventura
bug #7160#

Revision 1.8  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioBalanceteDespesa.class.php"   );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php");
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php"                     );

include_once 'JSBalanceteDespesa.js';
$obRegra = new ROrcamentoRelatorioBalanceteDespesa();
$obROrcamentoRecurso = new ROrcamentoRecurso;

function verificaMes($mesInformado)
{
    switch ($mesInformado) {
        case 1:
            $mesExtenso = "Janeiro";
        break;
        case 2:
            $mesExtenso = "Fevereiro";
        break;
        case 3:
            $mesExtenso = "Março";
        break;
        case 4:
            $mesExtenso = "Abril";
        break;
        case 5:
            $mesExtenso = "Maio";
        break;
        case 6:
            $mesExtenso = "Junho";
        break;
        case 7:
            $mesExtenso = "Julho";
        break;
        case 8:
            $mesExtenso = "Agosto";
        break;
        case 9:
            $mesExtenso = "Setembro";
        break;
        case 10:
            $mesExtenso = "Outubro";
        break;
        case 11:
            $mesExtenso = "Novembro";
        break;
        case 12:
            $mesExtenso = "Dezembro";
        break;
    }
return $mesExtenso;
}

switch ($_REQUEST["stCtrl"]) {
    case "MontaUnidade":
        if ($_REQUEST["inNumOrgao"]) {
            $stCombo  = "inNumUnidade";
            $stComboTxt  = "inNumUnidadeTxt";
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stComboTxt.value=''; \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

            $obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setExercicio(Sessao::getExercicio());
            $obRegra->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inNumOrgao"]);
            $obRegra->obROrcamentoUnidade->consultar($rsCombo, $stFiltro,"", $boTransacao );
            $arNomFiltro = Sessao::read('filtroNomRelatorio');
            while ( !$rsCombo->eof() ) {
                $arNomFiltro['unidade'][$rsCombo->getCampo( 'num_unidade' )] = $rsCombo->getCampo( 'nom_unidade' );
                $rsCombo->proximo();
            }
            $rsCombo->setPrimeiroElemento();
            Sessao::write('filtroNomRelatorio', $arNomFiltro);

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

    case "verificaEmissao":
        $obFormularioEmissao = new Formulario;
        $arFiltro = Sessao::read('filrotRelatorio');
        $arFiltro ['inCodTipoEmissao'] = $_POST['inCodTipoEmissao'];
        Sessao::write('filtro',$arFiltro);
        switch ($_POST['inCodTipoEmissao']) {
            case 1:
                $obTxtDataInicial = new Data;
                $obTxtDataInicial->setName   ( "stDataInicial" );
                $obTxtDataInicial->setNull   ( true );
                $obTxtDataInicial->setRotulo ( "Data" );
                $obTxtDataInicial->setTitle  ( "Informe a data para filtro" );
                $obTxtDataInicial->setValue  ( $stDataInicial );
                $obTxtDataInicial->setNull   ( false );

                $obFormularioEmissao->addComponente( $obTxtDataInicial );
            break;

            case 2:
                $jsInterno = "window.parent.frames['telaPrincipal'].document.frm.inMes.value = '';\n";

                $rsExercicio                          = new RecordSet;
                $obRegra = new REmpenhoEmpenho;
                $obRegra->recuperaExercicios( $rsExercicio, $boTransacao, Sessao::getExercicio());

                $obCmbAnoEmissao = new Select;
                $obCmbAnoEmissao->setRotulo              ( "Exercício / Mês"                );
                $obCmbAnoEmissao->setName                ( "inAno"              );
                $obCmbAnoEmissao->setValue               ( Sessao::getExercicio()   );
                $obCmbAnoEmissao->setStyle               ( "width: 120px"       );
                $obCmbAnoEmissao->setNull                ( false );
                $obCmbAnoEmissao->setCampoID             ( "exercicio"            );
                $obCmbAnoEmissao->setCampoDesc           ( "exercicio"            );
                $obCmbAnoEmissao->addOption              ( "", "Selecione"             );
                $obCmbAnoEmissao->preencheCombo          ( $rsExercicio                );
                $obCmbAnoEmissao->obEvento->setOnChange  ( "buscaValor('montaMensal');" );

                $mes = date("n");
                $obCmbMesEmissao = new Select;
                $obCmbMesEmissao->setRotulo              ( "Exercício / Mês"                );
                $obCmbMesEmissao->setName                ( "inMes"              );
                $obCmbMesEmissao->setValue               ( $mes   );
                $obCmbMesEmissao->setStyle               ( "width: 200px"       );
                $obCmbMesEmissao->setNull                ( false );
                $obCmbMesEmissao->setNull                ( false );
                $obCmbMesEmissao->addOption              ( "", "Selecione"      );
                for ($i=1; $i <= $mes; $i++) {
                    $obCmbMesEmissao->addOption              ( $i, verificaMes($i)      );
                }

                $obFormularioEmissao->agrupaComponentes( array($obCmbAnoEmissao, $obCmbMesEmissao) );
            break;

            case 3:
                $obTxtDataInicial = new Data;
                $obTxtDataInicial->setName   ( "stDataInicial" );
                $obTxtDataInicial->setNull   ( true );
                $obTxtDataInicial->setRotulo ( "Data Inicial" );
                $obTxtDataInicial->setTitle  ( "Informe o início do período para filtro" );
                $obTxtDataInicial->setValue  ( $stDataInicial );
                $obTxtDataInicial->setNull   ( false );

                $obTxtDataFinal = new Data;
                $obTxtDataFinal->setName   ( "stDataFinal" );
                $obTxtDataFinal->setRotulo ( "Data Final" );
                $obTxtDataFinal->setTitle  ( "Informe o final do período para filtro" );
                $obTxtDataFinal->setValue  ( $stDataFinal );
                $obTxtDataFinal->setNull   ( false );

                $obFormularioEmissao->addComponente( $obTxtDataInicial );
                $obFormularioEmissao->addComponente( $obTxtDataFinal );
            break;
        }
       $obFormularioEmissao->obJavaScript->montaJavaScript();
       $stEval = $obFormularioEmissao->obJavaScript->getInnerJavaScript();
       $stEval = str_replace("\n","",$stEval);

       $obFormularioEmissao->montaInnerHTML();
       $js = "";
       $js.= "f.stEval.value = '$stEval'; \n";
       $js.= "d.getElementById('spnEmissao').innerHTML = '".$obFormularioEmissao->getHTML()."';";
       $js.= $jsInterno;
       SistemaLegado::executaFrameOculto($js);
    break;

    case "montaMensal":
        $stCombo  = "inMes";
        $stJs .= "limpaSelect(f.$stCombo,0); \n";
        $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";

        if ($_POST['inAno'] < Sessao::getExercicio()) {
            $mes = 12;
        } else {
            $mes = date('n');
            $mesSel = date('n');
        }
        for ($i=1; $i <= $mes; $i++) {
            $stDesc = verificaMes($i);
            if( $i == $mesSel )
                $stSelected = 'selected';
            else
                $stSelected = '';

            $stJs .= "f.$stCombo.options[$i] = new Option('".$stDesc."','".$i."','".$stSelected."'); \n";
        }
    break;

    case "mascaraClassificacaoFiltroInicial":
        if ( trim( $_POST['stCodEstruturalInicial'] ) != "" ) {
            $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] ,$_POST['stCodEstruturalInicial'] );
            $js .= "f.stCodEstruturalInicial.value = '".$arMascClassificacao[1]."'; \n";
            SistemaLegado::executaFrameOculto( $js );
        }
    break;

    case "mascaraClassificacaoFiltroFinal":
        if ( trim( $_POST['stCodEstruturalFinal'] ) != "" ) {
            $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] ,$_POST['stCodEstruturalFinal'] );
            $js .= "f.stCodEstruturalFinal.value = '".$arMascClassificacao[1]."'; \n";
            SistemaLegado::executaFrameOculto( $js );
        }
    break;

   case 'buscaRecurso':
        $obROrcamentoRecurso->setCodRecurso( $_POST['inCodRecurso'] );
        $obROrcamentoRecurso->listar( $rsRecurso );
        $obROrcamentoRecurso->recuperaMascaraRecurso( $stMascaraRecurso );
        $arMascara = Mascara::validaMascaraDinamica($stMascaraRecurso,$_POST['inCodRecurso']);
        if ( $rsRecurso->getNumLinhas() > -1 and $_POST['inCodRecurso'] ) {
            $js .= 'f.inCodRecurso.value = "'.$arMascara[1].'";';
            $js .= 'd.getElementById("stDescricaoRecurso").innerHTML = "'.$rsRecurso->getCampo("nom_recurso").'";';
            $js .= 'f.stDescricaoRecurso.value = "'.$rsRecurso->getCampo("nom_recurso").'";';
        } else {
            $null = "&nbsp;";
            $js .= 'f.inCodRecurso.value = "";';
            $js .= 'f.inCodRecurso.focus();';
            $js .= 'd.getElementById("stDescricaoRecurso").innerHTML = "'.$null.'";';
            if( $_POST['inCodRecurso'] )
                $js .= "alertaAviso('@Valor inválido. (".$_POST['inCodRecurso'].")','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto( $js );
    break;

    case "mascaraClassificacaoInicial":
        include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";
        $obROrcamentoDespesa = new ROrcamentoDespesa;
        //monta mascara da RUBRICA DE DESPESA
        if ($_POST['stCodEstruturalInicial'] != '') {
            $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['stCodEstruturalInicial'] );
            $js .= "f.stCodEstruturalInicial.value = '".$arMascClassificacao[1]."'; \n";

            //busca DESCRICAO DA RUBRICA DE DESPESA
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascara          ( $_POST['stMascClassificacao'] );
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
            if ($stDescricao != "") {
                $js .= 'd.getElementById("stDescricaoDespesaInicial").innerHTML = "'.$stDescricao.'";';
            } else {
                $null = "&nbsp;";
                $js .= 'f.stCodEstruturalInicial.value = "";';
                $js .= 'd.getElementById("stDescricaoDespesaInicial").innerHTML = "'.$null.'";';
                $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
            }
            SistemaLegado::executaFrameOculto( $js );
        } else {
            $null = "&nbsp;";
            $js = 'd.getElementById("stDescricaoDespesaInicial").innerHTML = "'.$null.'";';
            echo SistemaLegado::executaFrameOculto($js);
        }
    break;

    case "mascaraClassificacaoFinal":
        include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";
        $obROrcamentoDespesa = new ROrcamentoDespesa;
        //monta mascara da RUBRICA DE DESPESA
        if ($_POST['stCodEstruturalFinal'] != '') {
            $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['stCodEstruturalFinal'] );
            $js .= "f.stCodEstruturalFinal.value = '".$arMascClassificacao[1]."'; \n";

            //busca DESCRICAO DA RUBRICA DE DESPESA
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascara          ( $_POST['stMascClassificacao'] );
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setMascClassificacao( $arMascClassificacao[1]       );
            $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $stDescricao );
            if ($stDescricao != "") {
                $js .= 'd.getElementById("stDescricaoDespesaFinal").innerHTML = "'.$stDescricao.'";';
            } else {
                $null = "&nbsp;";
                $js .= 'f.stCodEstruturalFinal.value = "";';
                $js .= 'd.getElementById("stDescricaoDespesaFinal").innerHTML = "'.$null.'";';
                $js .= "alertaAviso('@Valor inválido. (".$arMascClassificacao[1].")','form','erro','".Sessao::getId()."');";
            }
            SistemaLegado::executaFrameOculto( $js );
        } else {
            $null = "&nbsp;";
            $js = 'd.getElementById("stDescricaoDespesaFinal").innerHTML = "'.$null.'";';
            echo SistemaLegado::executaFrameOculto($js);
        }
    break;

}

if($stJs)
    SistemaLegado::executaFrameOculto($stJs);
