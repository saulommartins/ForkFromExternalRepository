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
* Arquivo de instância para manutenção de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 23167 $
$Name$
$Author: leandro.zis $
$Date: 2007-06-11 17:02:52 -0300 (Seg, 11 Jun 2007) $

Casos de uso: uc-01.04.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once( CAM_GA_NORMAS_NEGOCIO . "RNorma.class.php"    );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php" );
$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_REQUEST['stCtrl'];

$obRegra = new RNorma;
$rsAtributos = new RecordSet;

// Acoes por pagina
switch ($stCtrl) {
    case "PreencheNorma":
        $obTNorma = new TNorma;
        if ($_GET["inCodNorma"]) {
            $stFiltro = " WHERE cod_norma = ".$_GET["inCodNorma"];
            $obTNorma->recuperaNormas( $rsNorma, $stFiltro );
            if ( $rsNorma->eof() ) {
                $stJs = "f.inCodNorma.value ='';\n";
                $stJs .= "f.inCodNorma.focus();\n";
                $stJs .= "d.getElementById('stNorma').innerHTML = '&nbsp;';\n";
                if( $_GET["boExibeDataNorma"] )
                    $stJs .= "d.getElementById('stDataNorma').innerHTML = '&nbsp;';\n";
                if( $_GET["boExibeDataPublicacao"] )
                    $stJs .= "d.getElementById('stDataPublicacao').innerHTML = '&nbsp;';\n";
                $stJs .= "alertaAviso('@Código informado não existe. (".$_GET["inCodNorma"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stJs = "d.getElementById('stNorma').innerHTML = '".$rsNorma->getCampo( "nom_norma" )."';\n";
                if( $_GET["boExibeDataNorma"] )
                    $stJs .= "d.getElementById('stDataNorma').innerHTML = '".$rsNorma->getCampo( "dt_assinatura_formatado" )."';\n";
                if( $_GET["boExibeDataPublicacao"] )
                    $stJs .= "d.getElementById('stDataPublicacao').innerHTML = '".$rsNorma->getCampo( "dt_publicacao" )."';\n";
            }
        } else {
            $stJs = "d.getElementById('stNorma').innerHTML = '&nbsp;';\n";
            if( $_GET["boExibeDataNorma"] )
                $stJs .= "d.getElementById('stDataNorma').innerHTML = '&nbsp;';\n";
            if( $_GET["boExibeDataPublicacao"] )
                $stJs .= "d.getElementById('stDataPublicacao').innerHTML = '&nbsp;';\n";
        }
        echo $stJs;
        break;

    case "limpaLink":
        $stLink = "";
        #sessao->transf4["stLink"] = "";
        Sessao::remove('stNormaLink');
        $js .= "window.parent.frames['telaPrincipal'].document.getElementById('spnlink').innerHTML = '&nbsp;'";
        sistemaLegado::executaFrameOculto($js);
    break;

    //monta HTML com os ATRIBUTOS relativos ao TIPO DE NORMA selecionado
    case "MontaAtributos":
        if ($_REQUEST["inCodTipoNorma"] != "") {
            $inCodTipoNorma = $_REQUEST["inCodTipoNorma"];
            $inCodNorma = $_REQUEST["inCodNorma"];
            $obRegra->obRTipoNorma->setCodTipoNorma( $inCodTipoNorma );
            if(!$inCodNorma)
                $inCodNorma = 0;
            $obRegra->obRTipoNorma->obRCadastroDinamico->setChavePersistenteValores( array("cod_tipo_norma"=>$inCodTipoNorma, "cod_norma"=>$inCodNorma) );
            $obRegra->obRTipoNorma->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
            if ( $rsAtributos->eof() ) {
                $obRegra->obRTipoNorma->obRCadastroDinamico->recuperaAtributosSelecionados( $rsAtributos );
            }

            // define novo OBJETO para ATRIBUTOS
            $obMontaAtributos = new MontaAtributos;
            $obMontaAtributos->setName ("Atributo_");

            $obFormulario = new Formulario;
            $obMontaAtributos->setRecordSet( $rsAtributos );
            $obMontaAtributos->recuperaValores();
            $obMontaAtributos->geraFormulario( $obFormulario );

            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();

            $obFormulario->obJavaScript->montaJavaScript();
            $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
            $stEval = str_replace("\n","",$stEval);
        } else {
            $stHTML = " ";
            $stEval = " ";
        }
        $js .= "f.stEval.value = '$stEval'; \n";
        $js .= "d.getElementById('spanAtributos').innerHTML = '".$stHTML."';";
        sistemaLegado::executaIFrameOculto($js);
    break;

    case "Anexos":
        $file = trim(CAM_NORMAS."anexos/".$_REQUEST['cod_norma']."_".$anexo);

        header('Content-Description: File Transfer');
        header('Content-Type: application/force-download');
        header('Content-Length: ' . filesize($file));
        header('Content-Disposition: attachment; filename='.$anexo );
        readfile($file);

        sistemaLegado::executaFrameOculto($js);
    break;

}

?>
