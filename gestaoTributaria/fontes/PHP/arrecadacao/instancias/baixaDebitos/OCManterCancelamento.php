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
    * Página de Frame Oculto para Consulta de Arrecadacao
    * Data de Criação   : 26/07/2007

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCManterCancelamento.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.03.10
*/

/*
$Log$
Revision 1.1  2007/07/27 13:16:25  cercato
Bug#9762#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRPermissaoCancelamento.class.php" );

function montaListaCGM($rsLista)
{
    if ( $rsLista->getNumLinhas() > 0 ) {
        $obLista = new Lista;
        $obLista->setRecordSet ( $rsLista  );
        $obLista->setTitulo ( "Lista de Usuários" );
        $obLista->setMostraPaginacao ( false );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("CGM");
        $obLista->ultimoCabecalho->setWidth( 10 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Nome");
        $obLista->ultimoCabecalho->setWidth( 30 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo          ( "[numcgm]" );
        $obLista->ultimoDado->setAlinhamento    ( "CENTRO" );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo          ( "[nom_cgm]" );
        $obLista->ultimoDado->setAlinhamento    ( "CENTRO" );
        $obLista->commitDado();

        $obLista->addAcao ();
        $obLista->ultimaAcao->setAcao ( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao ( true );

        $obLista->ultimaAcao->setLink ( "JavaScript:excluirCGM();" );
        $obLista->ultimaAcao->addCampo ( "inIndice1","numcgm" );
        $obLista->commitAcao ();

        $obLista->montaHTML                    ();
        $stHTML =  $obLista->getHtml           ();
        $stHTML = str_replace                  ( "\n","",$stHTML );
        $stHTML = str_replace                  ( "  ","",$stHTML );
        $stHTML = str_replace                  ( "'","\\'",$stHTML );
    } else {
        $stHTML = "&nbsp";
    }

    $js = "d.getElementById('spnListaCGM').innerHTML = '".$stHTML."';\n";

    return $js;
}

switch ($_REQUEST["stCtrl"]) {
    case "IncluirCGM":
        if ($_REQUEST[ 'inCGM' ] != "") {
            $boIncluir = true;
            $arListaCGM = Sessao::read( 'listaCGM' );
            for ( $inX=0; $inX<count( $arListaCGM ); $inX++ ) {
                if ($arListaCGM[$inX]["numcgm"] == $_REQUEST[ 'inCGM' ]) {
                    $stJs = "alertaAviso('@CGM já está na lista. (".$_REQUEST[ 'inCGM' ].")', 'form','erro','".Sessao::getId()."');";
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $inX = count( $arListaCGM );
                $arListaCGM[$inX]["numcgm"] = $_REQUEST[ 'inCGM' ];
                $arListaCGM[$inX]["nom_cgm"] = $_REQUEST["stNomCGM"];

                Sessao::write( 'listaCGM', $arListaCGM );
                $rsListaCGM = new RecordSet;
                $rsListaCGM->preenche( $arListaCGM );
                $stJs = montaListaCGM( $rsListaCGM );

                $stJs .= "f.inCGM.value = '';";
                $stJs .= 'f.inCGM.focus();';
                $stJs .= 'd.getElementById("stNomCGM").innerHTML = "&nbsp;";';
            }

        } else {
            $stJs = "alertaAviso('@Campo CGM vazio.', 'form','erro','".Sessao::getId()."');";
        }

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "ExcluirCGM":
        $arTMPLista = array();
        $inY = 0;
        $arListaCGM = Sessao::read( 'listaCGM' );
        for ( $inX=0; $inX<count( $arListaCGM ); $inX++ ) {
            if ($arListaCGM[$inX]["numcgm"] != $_REQUEST[ 'inIndice1' ]) {
                $arTMPLista[$inY] = $arListaCGM[$inX];
                $inY++;
            }
        }

        Sessao::write( 'listaCGM', $arTMPLista );

        $rsListaCGM = new RecordSet;
        $rsListaCGM->preenche( $arTMPLista );
        $stJs = montaListaCGM( $rsListaCGM );
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "MostraLista":
        $obTARRPermissaoCancelamento = new TARRPermissaoCancelamento;
        $obTARRPermissaoCancelamento->recuperaListaCGMs( $rsListaCGM, "" );
        Sessao::write( 'listaCGM', $rsListaCGM->getElementos() );
        $stJs = montaListaCGM( $rsListaCGM );
        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "limparForm":
        Sessao::write( 'listaCGM', array() );
        $stJs = "f.inCGM.value = '';";
        $stJs .= 'f.inCGM.focus();';
        $stJs .= 'd.getElementById("stNomCGM").innerHTML = "&nbsp;";';
        $stJs .= "d.getElementById('spnListaCGM').innerHTML = '&nbsp;';\n";

        SistemaLegado::executaFrameOculto($stJs);
        break;

    case "limparCGM":
        $stJs = "f.inCGM.value = '';";
        $stJs .= 'f.inCGM.focus();';
        $stJs .= 'd.getElementById("stNomCGM").innerHTML = "&nbsp;";';
        SistemaLegado::executaFrameOculto($stJs);
        break;
}

?>
