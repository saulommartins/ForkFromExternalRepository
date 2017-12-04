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
    * Página de Formulario de Oculto de Responsável Técnico
    * Data de Criação   : 15/07/2004

    * @author Desenvolvedor: Marcelo B. Paulino

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-17 13:55:43 -0300 (Seg, 17 Jul 2006) $

    * Casos de uso: uc-02.01.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_CGM_NEGOCIO."RResponsavelTecnico.class.php"   );

/**
    * Instância o OBJETO da regra de negócios RResponsavelTecnico
*/
$obRResponsavel = new RResponsavelTecnico;

/**
    * Define o nome dos arquivos PHP
*/
$stPrograma = "ResponsavelTecnico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

switch ($_POST["stCtrl"]) {
    case "buscaConselho":
        $obRResponsavel->obRProfissao->consultarProfissao();
        $obRResponsavel->obRProfissao->setCodigoProfissao( $_REQUEST["inCodigoProfissao"] );
        $obRResponsavel->obRProfissao->consultarProfissao();
        $stNomeConselho = $obRResponsavel->obRProfissao->obRConselho->getNomeConselho();

        $obLabelConselho = new Label;
        $obLabelConselho->setRotulo    ( 'Classe de Conselho' );
        $obLabelConselho->setValue     ( $stNomeConselho      );
        if ($stNomeConselho) {
            $obFormulario = new Formulario;
            $obFormulario->addComponente( $obLabelConselho );
            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\'","\\'",$stHTML );

        } else {
            $stHTML = "";
        }
        $js .= "d.getElementById('dadosConselho').innerHTML = '".$stHTML."';\n";

        $obRResponsavel->obRProfissao->setCodigoProfissao( $_REQUEST["inCodigoProfissao"] );
        $obRResponsavel->obRProfissao->consultarProfissao();
        $stNomeRegistro = $obRResponsavel->obRProfissao->obRConselho->getNomeRegistro();

        $obTxtRegistro = new TextBox;
        $obTxtRegistro->setName      ( "inCodigoRegistro" );
        $obTxtRegistro->setValue     ( $_REQUEST["inNumRegistro"] );
        $obTxtRegistro->setRotulo    ( $stNomeRegistro );
        $obTxtRegistro->setSize      ( "11" );
        $obTxtRegistro->setMaxLength ( "10" );
        $obTxtRegistro->setInteiro   ( true );
        $obTxtRegistro->setNull      ( false );

        if ($stNomeRegistro) {
            $obFormulario = new Formulario;
            $obFormulario->addComponente( $obTxtRegistro );
            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();

            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\'","\\'",$stHTML );

        } else {
            $stHTML = "";
        }
        $js .= "d.getElementById('dadosRegistro').innerHTML = '".$stHTML."';\n";

        if ($stAcao == "alterar") {
            $js .= 'd.getElementById("nom_cgm").innerHTML = "'.$stNomCGM.'";';
        }
        SistemaLegado::executaFrameOculto($js);

    break;

    case "buscaCGM":
        $obTCgm = new TCGM;
        if ($_POST["inNumCGM"] != "") {
            $stWhere = " WHERE numcgm = ".$_POST["inNumCGM"];
            $null = "&nbsp;";
            $obTCgm->recuperaTodos($rsCgm, $stWhere);
            $inNumLinhas = $rsCgm->getNumLinhas();
            if ($inNumLinhas <= 0) {
                $js .= 'f.inNumCGM.value = "";';
                $js .= 'f.inNumCGM.focus();';
                $js .= 'd.getElementById("campoInner").innerHTML = "'.$null.'";';
                $js .= "alertaAviso('@Valor inválido. (".$_POST["inNumCGM"].")','form','erro','".Sessao::getId()."');";
            } else {
                $stNomCgm = $rsCgm->getCampo("nom_cgm");
                $js .= 'd.getElementById("nom_cgm").innerHTML = "'.$stNomCgm.'";';
            }
            SistemaLegado::executaFrameOculto($js);
        }
    break;

    case "limpaTudo":
        $js = "d.getElementById('nom_cgm').innerHTML = '&nbsp;'\n";
        SistemaLegado::executaFrameOculto($js);

    break;

}
?>
