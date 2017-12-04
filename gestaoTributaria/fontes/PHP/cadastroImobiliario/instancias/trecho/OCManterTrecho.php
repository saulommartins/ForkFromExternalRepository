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
    * Página de processamento oculto para o cadastro de trecho
    * Data de Criação   : 06/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @ignore

    * $Id: OCManterTrecho.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.06
*/

/*
$Log$
Revision 1.6  2006/09/18 10:31:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma          = "ManterTrecho";
$pgFilt              = "FL".$stPrograma.".php";
$pgList              = "LS".$stPrograma.".php";
$pgForm              = "FM".$stPrograma.".php";
$pgProc              = "PR".$stPrograma.".php";
$pgOcul              = "OC".$stPrograma.".php";
$pgJs                = "JS".$stPrograma.".js";

include_once( $pgJs );

switch ($_REQUEST ["stCtrl"]) {
    case "buscaLogradouro":
        $obRCIMTrecho     = new RCIMTrecho;
        $rsLogradouro  = new RecordSet;

        if ( empty( $_REQUEST["inNumLogradouro"] ) ) {
            $js .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
            $js .= "f.inCodSequencia.value = '';";
        } else {
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );
            if ( $rsLogradouro->eof() ) {
                $js .= 'f.inNumLogradouro.value = "";';
                $js .= 'f.inNumLogradouro.focus();';
                $js .= 'd.getElementById("campoInner").innerHTML = "&nbsp;";';
                $js .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouro"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
                $js .= "f.inCodSequencia.value = '".$rsLogradouro->getCampo("prox_sequencia")."';";
                $js .= "f.stNomeLogradouro.value = '$stNomeLogradouro';";
                $js .= 'd.getElementById("campoInner").innerHTML = "'.$stNomeLogradouro.'";';
            }
        }
        SistemaLegado::executaFrameOculto($js);
        break;

    case "buscaLogradouroFiltro":
        $obRCIMTrecho     = new RCIMTrecho;
        $rsLogradouro  = new RecordSet;
        if ($_REQUEST["inNumLogradouro"]) {
            $obRCIMTrecho->setCodigoLogradouro( $_REQUEST["inNumLogradouro"] ) ;
            $obRCIMTrecho->listarLogradourosTrecho( $rsLogradouro );
            if ( $rsLogradouro->eof() ) {
                $js .= 'f.inNumLogradouro.value = "";';
                $js .= 'f.inNumLogradouro.focus();';
                $js .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
                $js .= "alertaAviso('@Valor inválido. (".$_REQUEST["inNumLogradouro"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomeLogradouro = $rsLogradouro->getCampo ("tipo_nome");
                $js .= "f.stNomeLogradouro.value = '$stNomeLogradouro';";
                $js .= 'd.getElementById("campoInnerLogr").innerHTML = "'.$stNomeLogradouro.'";';
            }
        } else {
            $js .= 'd.getElementById("campoInnerLogr").innerHTML = "&nbsp;";';
        }

        SistemaLegado::executaFrameOculto($js);
        break;

    case "buscaLegalAliquota":
        include_once ( CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php" );
        $obRARRDesoneracao = new RARRDesoneracao;
        $obRARRDesoneracao->addNorma();
        $obRARRDesoneracao->roUltimaNorma->setCodNorma( $_REQUEST['inCodigoFundamentacaoAliquota'] );
        $obRARRDesoneracao->roUltimaNorma->listar( $rsNorma );

        if ( !$rsNorma->eof() ) {
            $stJs = "d.getElementById('stFundamentacaoAliquota').innerHTML = '".$rsNorma->getCampo( "nom_norma" )."';\n";
        } else {
            $stMsg = "Fundamentação inválida! ";
            $stJs = "alertaAviso('".$stMsg."(".$_REQUEST["inCodigoFundamentacaoAliquota"].")','form','erro','".Sessao::getId()."', '../');";

            $stJs .= "d.getElementById('stFundamentacaoAliquota').innerHTML = '&nbsp;';\n";
            $stJs .= 'f.inCodigoFundamentacaoAliquota.value = "";';
            $stJs .= 'f.inCodigoFundamentacaoAliquota.focus();';
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "buscaLegal":
        include_once ( CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php" );
        $obRARRDesoneracao = new RARRDesoneracao;
        $obRARRDesoneracao->addNorma();
        $obRARRDesoneracao->roUltimaNorma->setCodNorma( $_REQUEST['inCodigoFundamentacao'] );
        $obRARRDesoneracao->roUltimaNorma->listar( $rsNorma );

        if ( !$rsNorma->eof() ) {
            $stJs = "d.getElementById('stFundamentacao').innerHTML = '".$rsNorma->getCampo( "nom_norma" )."';\n";
        } else {
            $stMsg = "Fundamentação inválida! ";
            $stJs = "alertaAviso('".$stMsg."(".$_REQUEST["inCodigoFundamentacao"].")','form','erro','".Sessao::getId()."', '../');";

            $stJs .= "d.getElementById('stFundamentacao').innerHTML = '&nbsp;';\n";
            $stJs .= 'f.inCodigoFundamentacao.value = "";';
            $stJs .= 'f.inCodigoFundamentacao.focus();';
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;
}

?>
