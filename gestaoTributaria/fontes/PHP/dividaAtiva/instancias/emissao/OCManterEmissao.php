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
    * Página de Frame Oculto de Emissao
    * Data de Criação   : 26/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCManterEmissao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaDocumento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATEmissaoDocumento.class.php" );
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );

$arDadosSessao = Sessao::read('dados');
switch ($_REQUEST['stCtrl']) {
    case "buscaTipoDocumento":
        $stDescricao = '&nbsp;';
        $stNomCampo = $_GET["stNomCampoCod"];
        if ($_GET[$stNomCampo]) {
            $obTDATDividaDocumento = new TDATDividaDocumento;
            $stFiltro = " WHERE ded.num_documento = ".$_GET[$stNomCampo];
            $obTDATDividaDocumento->recuperaListaDocumento( $rsRecordSet, $stFiltro );
            if ( !$rsRecordSet->Eof() ) {
                $stDescricao = $rsRecordSet->getCampo("nome_documento");
                $stJs = "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stDescricao."');";
            } else {
                $stJs = "jQuery('#".$_GET["stNomCampoCod"]."').val('');";
                $stJs .= "jQuery('#".$_GET['stIdCampoDesc']."').html('&nbsp;');\n";
                $stJs .= "alertaAviso('@Código documento inválido (".$_GET[$stNomCampo] .").','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "jQuery('#".$_GET['stIdCampoDesc']."').html('&nbsp;');\n";
            if ($_GET[$stNomCampo] == '0') {
                $stJs .= "jQuery('#".$_GET["stNomCampoCod"]."').val('');";
                $stJs .= "alertaAviso('@Código documento inválido (".$_GET[$stNomCampo] .").','form','erro','".Sessao::getId()."');";
            }
        }

        echo $stJs;
        break;
    case "buscaNumeroDocumento":
        $stDescricao = '&nbsp;';
       $stNomCampoCombo = $_GET['stNomCampoCombo'];
       $stNomCampoCod   = $_GET['stNomCampoCod'];
       $stIdCampoDesc   = $_GET['stIdCampoDesc'];

        if ($_GET[$stNomCampoCod]) {
            $obTDATDividaDocumento = new TDATDividaDocumento;
            $stFiltro = " WHERE ded.num_documento = ".$_GET[$stNomCampoCod];
            $stFiltro.= " AND ded.cod_tipo_documento=".$_GET[$stNomCampoCombo];

            $obTDATDividaDocumento->recuperaListaDocumento( $rsRecordSet, $stFiltro );
            if ( !$rsRecordSet->Eof() ) {
                $stDescricao = $rsRecordSet->getCampo("nome_documento");
                $stJs = "retornaValorBscInner( '".$stNomCampoCod."', '".$stIdCampoDesc."', 'frm', '".$stDescricao."');";
            } else {
                $stJs = "jQuery('#".$stNomCampoCod."').val('');";
                $stJs .= "jQuery('#".$stIdCampoDesc."').html('&nbsp;');\n";
                $stJs .= "alertaAviso('@Código documento inválido (".$_GET[$stNomCampoCod] .").','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "jQuery('#".$_GET['stIdCampoDesc']."').html('&nbsp;');\n";
            if ($_GET[$stNomCampo] == '0') {
                $stJs .= "jQuery('#".$_GET["stNomCampoCod"]."').val('');";
                $stJs .= "alertaAviso('@Código documento inválido (".$_GET[$stNomCampoCod] .").','form','erro','".Sessao::getId()."');";
            }
        }

        echo $stJs;
        break;

    case "buscaDocumento":
        $stDados = str_replace( "/", "", $_GET["stNumDocumento"] );
        $inTamanhoOrigem = strlen( $stDados );
        if ($inTamanhoOrigem >= 5) {
            $stAno = substr( $stDados, $inTamanhoOrigem-4, 4 );
            $stCodigo = substr( $stDados, 0, $inTamanhoOrigem-4 );
            $obTDATEmissaoDocumento = new TDATEmissaoDocumento;
            $stFiltro = " WHERE exercicio = ".$stAno." AND num_documento = ".$stCodigo." LIMIT 1";
            $obTDATEmissaoDocumento->recuperaTodos( $rsDocumento, $stFiltro );
            if ( $rsDocumento->Eof() ) {
                $stJs = "alertaAviso('@Documento inválido (".$_GET["stNumDocumento"].").','form','erro','".Sessao::getId()."');";
                $stJs .= "jQuery('#stNumDocumento').val('');";
            } else {
                $stJs = "jQuery('#stNumDocumento').val('".$stCodigo."/".$stAno."');";
            }
        }

        echo $stJs;
        break;

    case "tipoEmissao":
        if ($_GET["stTipoModalidade"] == "emissao") {
            echo "jQuery('#spnReemissao').html('&nbsp;');";
        } else {
            $obTDATEmissaoDocumento = new TDATEmissaoDocumento;
            $obTDATEmissaoDocumento->recuperaNumeroDocumento( $rsMaxNumeracao );
            $inTamanho = strlen( $rsMaxNumeracao->getCampo("valor") );
            $stMascara = "";
            for ( $inX=0; $inX<$inTamanho; $inX++ )
                $stMascara .= "9";

            $stMascara .= "/9999";

            $obTxtDocumento = new TextBox ;
            $obTxtDocumento->setName ( "stNumDocumento" );
            $obTxtDocumento->setId ( "stNumDocumento" );
            $obTxtDocumento->setMascara ( $stMascara );
            $obTxtDocumento->setRotulo ( "Número do Documento" );
            $obTxtDocumento->setTitle ( "Informe o número do documento." );
            $obTxtDocumento->setNull ( true );

            $pgOcul = "'OCManterEmissao.php?".Sessao::getId()."&stNumDocumento='+this.value";

            $obTxtDocumento->obEvento->setOnChange ( "ajaxJavaScriptSincrono(".$pgOcul.",'buscaDocumento' );" );

            $obFormulario = new Formulario;
            $obFormulario->addComponente( $obTxtDocumento );
            $obFormulario->montaInnerHTML();
            echo "jQuery('#spnReemissao').html('".$obFormulario->getHTML()."');";
        }
        break;

    case "Download":
        $inArquivo      = $_REQUEST["HdnQual"];
        $content_type   = 'application/sxw';
        $stDocumento    = $arDadosSessao[$inArquivo]["nome_arquivo_tmp"];
        $download       = $arDadosSessao[$inArquivo]["nome_arquivo"];
        $download .= ".odt";
        header ("Content-Length: " . filesize( $stDocumento ));
        header("Content-type: $content_type");
        header("Content-Disposition: attachment; filename=\"$download\"");
        readfile( $stDocumento );
        break;

    case "PreencheCGM":
        if ($_GET["inCGM"]) {
            $obTCGM = new TCGM;
            $obTCGM->setDado( "numcgm", $_GET["inCGM"] );
            $obTCGM->recuperaPorChave( $rsCGM );
            if ( $rsCGM->Eof() ) {
                $stJs = 'jQuery("#inCGM").val("");';
                $stJs .= 'jQuery("#nCGM").focus();';
                $stJs .= 'jQuery("#stCGM").html("&nbsp;")';
                $stJs .= "alertaAviso('@CGM não encontrado. (".$_GET["inCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCGM->getCampo("nom_cgm");
                $stJs = 'd.getElementById("stCGM").innerHTML = "'.$stNomCgm.'";';
            }
        } else {
                $stJs = 'jQuery("#stCGM").html("&nbsp;")';
        }

        echo $stJs;
        break;

    case "buscaDocumento":
        $stDescricao = '&nbsp;';
        $stNomCampo = $_GET["stNomCampoCod"];
        if ($stNomCampo) {
            $obTDATDividaDocumento = new TDATDividaDocumento;
            $stFiltro = " WHERE ded.num_documento = ".$_GET[$stNomCampo];
            $obTDATDividaDocumento->recuperaListaDocumento( $rsRecordSet, $stFiltro );
            if ( !$rsRecordSet->Eof() ) {
                $stDescricao = $rsRecordSet->getCampo("nome_documento");
                $stJs = "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stDescricao."');";
            } else {
                $stJs = 'jQuery("#'.$_GET["stNomCampoCod"].'").val("");';
                $stJs .= 'jQuery("#'.$_GET['stIdCampoDesc'].'").html("&nbsp;")';
                $stJs .= "alertaAviso('@Código documento inválido (".$_GET[$stNomCampo] .").','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= 'jQuery("#'.$_GET['stIdCampoDesc'].'").html("&nbsp;")';
            if ($_GET[$stNomCampo] == '0') {
                $stJs .= 'jQuery("#'.$_GET["stNomCampoCod"].'").val("");';
                $stJs .= "alertaAviso('@Código documento inválido (".$_GET[$stNomCampo] .").','form','erro','".Sessao::getId()."');";
            }
        }

        echo $stJs;
        break;
    case "verificaHabilitaDocumento"://verifica se o campo Documento deve ser habilitado
        $stJs ='';
        $stNomCampoCombo = $_GET['stNomCampoCombo'];
        if ($_GET[$stNomCampoCombo] == '') {
            $stJs .= 'jQuery("#'.$_GET["stNomCampoCod"].'").val("");';
            $stJs .= 'jQuery("#'.$_GET["stNomCampoCod"].'").attr("disabled","disabled");';
            $stJs .= 'jQuery("#'.$_GET['stIdCampoDesc'].'").html("&nbsp;")';
        } else {
            $stJs .= 'jQuery("#'.$_GET["stNomCampoCod"].'").removeAttr("disabled");';
            $stJs .= 'jQuery("#'.$_GET["stNomCampoCod"].'").focus();';
        }
        echo $stJs;
        break;
}
