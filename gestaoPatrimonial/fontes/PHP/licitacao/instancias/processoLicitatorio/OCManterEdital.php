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
    * Pagina de Oculto para Incluir Edital
    * Data de Criação   : 25/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 28786 $
    $Name$
    $Autor: $
    $Date: 2008-03-26 14:36:23 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-03.05.16
*/

/*

$Log:

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterEdital";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js" ;

switch ($_REQUEST['stCtrl']) {
    case "preencheAlteracao":
        $stJs = "d.getElementById('stNomCGM').innerHTML = '".$_REQUEST['stNomCGM']."';\n";
        $stJs.= "d.getElementById('stProcesso').innerHTML = '" .str_pad( $_REQUEST['inCodProcesso'], 5, "0", STR_PAD_LEFT ) ."/". $_REQUEST['stExercicioProcesso'] ."';\n";
        $stJs.= "f.inCodEntidade.value = ". $_REQUEST['inCodEntidade'] .";\n";
        $stJs.= "f.stNomEntidade.value = '". $_REQUEST['stNomEntidade'] ."';\n";
        $stJs.= "f.inCodModalidade.value = ". $_REQUEST['inCodModalidade'] .";\n";
        $stJs.= "f.inNumEdital.value = ".$_REQUEST['inNumEdital'].";\n";
        $stJs.= "f.inCodLicitacao.value = ".$_REQUEST['inCodLicitacao'].";\n";
        //$stJs.= "f.inCodLicitacao.options[1] = new Option('".$_REQUEST['inCodLicitacao']."','".$_REQUEST['inCodLicitacao']."','selected','selected');\n";
    break;

    case "carregaLabel":
        if ($_REQUEST['stNumEdital']) {
            include_once ( CAM_GP_LIC_COMPONENTES. "ILabelNumeroLicitacao.class.php" );
            $obForm = new Form();

            $arEdital = explode('/', $_REQUEST['stNumEdital']);

            $obLblNumeroLicitacao = new ILabelNumeroLicitacao( $obForm );
            $obLblNumeroLicitacao->setExercicio( $arEdital[1] );
            $obLblNumeroLicitacao->setNumEdital( $arEdital[0] );

            $obFormulario = new Formulario($obForm);
            $obLblNumeroLicitacao->geraFormulario( $obFormulario );
            $obFormulario->montaInnerHTML();

            $stJs = "d.getElementById('inNumLicitacao').innerHTML = '". $obFormulario->getHTML() ."';\n";
        }
    break;

}

echo $stJs;
