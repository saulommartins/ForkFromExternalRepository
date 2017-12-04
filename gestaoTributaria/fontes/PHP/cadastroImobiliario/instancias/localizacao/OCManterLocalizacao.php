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
    * Página de processamento oculto para o cadastro de localização
    * Data de Criação   : 24/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    * $Id: OCManterLocalizacao.php 63887 2015-10-29 18:10:14Z evandro $

    * Casos de uso: uc-05.01.03
*/

/*
$Log$
Revision 1.9  2006/09/18 10:30:48  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLocalizacao.class.php" );
include_once ( CAM_GT_CIM_COMPONENTES."MontaLocalizacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterLocalizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obMontaLocalizacao = new MontaLocalizacao;
$obMontaLocalizacao->setCodigoVigencia ( $_REQUEST["inCodigoVigencia"] );

switch ($_REQUEST["stCtrl"]) {
    case "SetarMascaraLocalizacao":
        $obRCIMLocalizacao = new RCIMLocalizacao;
        $obRCIMLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"] );
        $obRCIMLocalizacao->geraMascara( $stMascara );

        $obTxtLocalizacao = new TextBox;
        $obTxtLocalizacao->setName      ( "stValorComposto" );
        $obTxtLocalizacao->setId        ( "stValorComposto" );
        $obTxtLocalizacao->setRotulo    ( "Localização" );
        $obTxtLocalizacao->setMaxLength ( strlen( $stMascara ) );
        $obTxtLocalizacao->setMinLength ( strlen( $stMascara ) );
        $obTxtLocalizacao->setSize      ( strlen( $stMascara ) );
        $obTxtLocalizacao->obEvento->setOnKeyUp("mascaraDinamico('".$stMascara."', this, event);");
        $obTxtLocalizacao->setNull(true);

        $obTxtNomeLocalizacao = new TextBox;
        $obTxtNomeLocalizacao->setName      ( "stNomeLocalizacao" );
        $obTxtNomeLocalizacao->setRotulo    ( "Nome" );
        $obTxtNomeLocalizacao->setMaxLength ( 80 );
        $obTxtNomeLocalizacao->setSize      ( 40 );

        $obFormulario = new Formulario;
        $obFormulario->addComponente( $obTxtLocalizacao );
        $obFormulario->addComponente( $obTxtNomeLocalizacao );
        $obFormulario->montaInnerHTML();
        $js = "d.getElementById('spnLocalizacao').innerHTML = '". $obFormulario->getHTML(). "';\n";
        $js .= "f.stValorComposto.focus();";
        sistemaLegado::executaFrameOculto ( $js );
        break;

    case "BuscaNiveis":
        $obRCIMLocalizacao = new RCIMLocalizacao;
        $obRCIMLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"] );
        $obRCIMLocalizacao->listarNiveis( $rsNivel );
        $js = "limpaSelect(f.cmbNivel,1); \n";
        $js .= "f.cmbNivel[0] = new Option('Selecione','', 'selected');\n";
        $inContador = 1;
        while ( !$rsNivel->eof() ) {
            $inCodNivel = $rsNivel->getCampo( "cod_nivel" );
            $stNomNivel = $rsNivel->getCampo( "nom_nivel" );
            $js .= "f.cmbNivel.options[$inContador] = new Option('".$stNomNivel."','".$inCodNivel."'); \n";
            $inContador++;
            $rsNivel->proximo();
        }

        sistemaLegado::executaFrameOculto ( $js );
        break;

    case "preencheProxCombo":
        $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 1);
        $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
        $inPosicao = $_REQUEST["inPosicao"];
        if ( empty( $stChaveLocal ) and $_REQUEST["inPosicao"] > 2 ) {
            $stNomeComboLocalizacao = "inCodLocalizacao_".( $_REQUEST["inPosicao"] - 2);
            $stChaveLocal = $_REQUEST[$stNomeComboLocalizacao];
            $inPosicao = $_REQUEST["inPosicao"] - 1;
        }
        $arChaveLocal = explode("-" , $stChaveLocal );
        $obMontaLocalizacao->setCodigoVigencia    ( $_REQUEST["inCodigoVigencia"] );
        $obMontaLocalizacao->setCodigoNivel       ( $arChaveLocal[0] );
        $obMontaLocalizacao->setCodigoLocalizacao ( $arChaveLocal[1] );
        $obMontaLocalizacao->setValorReduzido     ( $arChaveLocal[3] );
        $obMontaLocalizacao->preencheProxCombo( $inPosicao , $_REQUEST["inNumNiveis"] );
    break;
    case "preencheCombos":
        $obMontaLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"]   );
        $obMontaLocalizacao->setCodigoNivel   ( $_REQUEST["inCodigoNivel"]      );
        $obMontaLocalizacao->setValorReduzido ( $_REQUEST["stChaveLocalizacao"] );
        $obMontaLocalizacao->preencheCombos();
    break;

    case "buscaLocalizacao":
        $vazio = '&nbsp;';
        if ($_REQUEST['stChaveLocalizacao']) {
            $obRCIMLocalizacao = new RCIMLocalizacao;

            $obRCIMLocalizacao->setCodigoVigencia( $_REQUEST["inCodigoVigencia"] );
            $obRCIMLocalizacao->setValorReduzido( $_REQUEST['stChaveLocalizacao'] );
            $obRCIMLocalizacao->setCodigoNivel  ( $_REQUEST["inCodigoNivel"]-1 );
            $obRCIMLocalizacao->listarNomLocalizacao( $rsLocalizacao );

            if ( $rsLocalizacao->getNumLinhas() > 0 ) {
                $stDescricao = $rsLocalizacao->getCampo("nom_localizacao");
                $stJs .= "d.getElementById('stNomeChaveLocalizacao').innerHTML = '". $stDescricao ."';\n";
            } else {
                $stJs .= "d.getElementById('stNomeChaveLocalizacao').innerHTML = '". $vazio ."';\n";
                $stJs .= "f.stChaveLocalizacao.value= '';";
                $stJs .= "f.stChaveLocalizacao.focus();";
                $stJs .= "alertaAviso('@Localização inválida. (".$_REQUEST["stChaveLocalizacao"].")', 'form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "d.getElementById('stNomeChaveLocalizacao').innerHTML = '". $vazio ."';\n";
        }
        sistemaLegado::executaFrameOculto ( $stJs );
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
sistemaLegado::LiberaFrames();
?>
