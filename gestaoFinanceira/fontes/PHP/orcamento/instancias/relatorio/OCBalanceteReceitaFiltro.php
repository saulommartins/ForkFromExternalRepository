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
    * Data de Criação   : 17/02/2005

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.21
*/

/*
$Log$
Revision 1.6  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php"   );
include_once( CAM_GF_ORC_NEGOCIO."REmpenhoEmpenho.class.php");

include_once 'JSBalanceteReceita.js';

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
    case "verificaEmissao":
        $obFormularioEmissao = new Formulario;
        switch ($_POST['inCodTipoEmissao']) {
            case 1:
                $obTxtDataInicial = new Data;
                $obTxtDataInicial->setName   ( "stDataInicial" );
                $obTxtDataInicial->setNull   ( true );
                $obTxtDataInicial->setRotulo ( "Data" );
                $obTxtDataInicial->setTitle  ( "Informe a data para filtro" );
                $obTxtDataInicial->setValue  ( $stDataInicial );
                $obTxtDataInicial->setNull   ( false );
                $obTxtDataInicial->obEvento->setOnBlur  ( "validaAno();" );

                $obFormularioEmissao->addComponente( $obTxtDataInicial );
            break;

            case 2:
                $jsInterno = "window.parent.frames['telaPrincipal'].document.frm.inMes.value = '';\n";

                $rsExercicio                          = new RecordSet;
                $obRegra = new REmpenhoEmpenho;
                $obRegra->recuperaExercicios( $rsExercicio, $boTransacao, Sessao::getExercicio());

                $mes = date("n");
                $obCmbMesEmissao = new Select;
                $obCmbMesEmissao->setRotulo              ( "Mês"                );
                $obCmbMesEmissao->setName                ( "inMes"              );
                $obCmbMesEmissao->setValue               ( $mes   );
                $obCmbMesEmissao->setStyle               ( "width: 200px"       );
                $obCmbMesEmissao->setNull                ( false );
                $obCmbMesEmissao->setNull                ( false );
                $obCmbMesEmissao->addOption              ( "", "Selecione"      );
                for ($i=1; $i <= $mes; $i++) {
                    $obCmbMesEmissao->addOption              ( $i, verificaMes($i)      );
                }

                $obFormularioEmissao->addComponente( $obCmbMesEmissao );
            break;

            case 3:
                $obTxtDataInicial = new Data;
                $obTxtDataInicial->setName   ( "stDataInicial" );
                $obTxtDataInicial->setNull   ( true );
                $obTxtDataInicial->setRotulo ( "Data Inicial" );
                $obTxtDataInicial->setTitle  ( "Informe o início do período para filtro" );
                $obTxtDataInicial->setValue  ( $stDataInicial );
                $obTxtDataInicial->setNull   ( false );
                $obTxtDataInicial->obEvento->setOnBlur  ( "validaAno();" );

                $obTxtDataFinal = new Data;
                $obTxtDataFinal->setName   ( "stDataFinal" );
                $obTxtDataFinal->setRotulo ( "Data Final" );
                $obTxtDataFinal->setTitle  ( "Informe o final do período para filtro" );
                $obTxtDataFinal->setValue  ( $stDataFinal );
                $obTxtDataFinal->setNull   ( false );
                $obTxtDataFinal->obEvento->setOnBlur  ( "validaAno();" );

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

    case "validaData":
        if ($_POST['stDataInicial']) {
            if (substr($_POST['stDataInicial'],6,4) <> Sessao::getExercicio()) {
                SistemaLegado::exibeAviso(urlencode("A Data Inicial deve ser do ano '".Sessao::getExercicio() . "'!"),"","erro");
                $js = "f.stDataInicial.focus(); \n";
                SistemaLegado::executaFrameOculto($js);
            }
        }
        if ($_POST['stDataFinal']) {
            if (substr($_POST['stDataFinal'],6,4) <> Sessao::getExercicio()) {
                SistemaLegado::exibeAviso(urlencode("A Data Final deve ser do ano '".Sessao::getExercicio() . "'!"),"","erro");
                $js = "f.stDataFinal.focus(); \n";
                SistemaLegado::executaFrameOculto($js);
            }
        }
    break;

}

if($stJs)
    SistemaLegado::executaFrameOculto($stJs);
