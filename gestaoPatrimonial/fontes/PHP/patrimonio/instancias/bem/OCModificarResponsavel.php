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
    * Data de Criação: 24/09/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Grasiele Torres

    $Id: $

    * Casos de uso: uc-03.01.24

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioBemResponsavel.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ModificarResponsavel";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function limparResponsavelAnterior()
{
    $stJs .= " document.getElementById('stNomResponsavelAnterior').innerHTML = '&nbsp;';\n ";
    $stJs .= " frm.inNumResponsavelAnterior.value = '';\n ";
    $stJs .= " frm.inNumResponsavelAnterior.focus(); ";

    return $stJs;
}

function limparResponsavelNovo()
{
    $stJs .= " document.getElementById('stNomResponsavelNovo').innerHTML = '&nbsp;';\n ";
    $stJs .= " frm.inNumResponsavelNovo.value = '';\n ";
    $stJs .= " frm.inNumResponsavelNovo.focus(); ";

    return $stJs;
}

function montaSpnDtInicio()
{
    $obTPatrimonioBemResponsavel = new TPatrimonioBemResponsavel();
    $obTPatrimonioBemResponsavel->recuperaMaxDtInicio( $dt_inicio );

    list($ano, $mes, $dia) = explode('-', $dt_inicio->getCampo('dt_inicio'));
    $dataInicio = ($dia."/".$mes."/".$ano);

    //cria um label para demonstrar a maior data de início do responsável anterior
    $obLblDataInicioResponsavelAnterior = new Label;
    $obLblDataInicioResponsavelAnterior->setRotulo ( 'Data de Início' );
    $obLblDataInicioResponsavelAnterior->setTitle  ( 'Data de Início do Responsável Anterior.' );
    $obLblDataInicioResponsavelAnterior->setValue( $dataInicio );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obLblDataInicioResponsavelAnterior );
    $obFormulario->montaInnerHTML();
    $stHtml .= $obFormulario->getHTML();

    $stHtml = str_replace( "\n", "", $stHtml);
    $stHtml = str_replace( "  ", "", $stHtml);
    $stHtml = str_replace( "'" , "\\'", $stHtml);

    $stJs .= "document.getElementById('spnDtInicio').innerHTML = '".$stHtml."';\n";

    return $stJs;
}

function limpaSpnDtInicio()
{
    $stJs = "document.getElementById('spnDtInicio').innerHTML = ''\n";

    return $stJs;
}

switch ($stCtrl) {
case 'verificaResponsavelBem':
    $obTPatrimonioBemResponsavel = new TPatrimonioBemResponsavel();
    $obTPatrimonioBemResponsavel->verificaResponsavelBem( $rsbens );

     if ($_REQUEST['inNumResponsavelAnterior'] == '' or $_REQUEST['inNumResponsavelAnterior'] == 0) {
        $stJs .= limpaSpnDtInicio();
     }

    if ($rsbens->getNumLinhas() < 0) {
        $stJs .= "alertaAviso('Este CGM (".$_REQUEST['inNumResponsavelAnterior'].") não está como responsável de bens patrimoniais.','form','erro','".Sessao::getId()."');";
        $stJs .= limparResponsavelAnterior();
        $stJs .= limpaSpnDtInicio();

    } else {
        if ($_REQUEST['inNumResponsavelAnterior'] != '' and $_REQUEST['inNumResponsavelAnterior'] != 0) {
            $stJs.= montaSpnDtInicio();
        }
    }

    break;

case 'verificaResponsavelDif':

    if ($_REQUEST['inNumResponsavelNovo'] != '') {
        if ($_REQUEST['inNumResponsavelNovo'] == $_REQUEST['inNumResponsavelAnterior']) {
            $stJs.= "alertaAviso('O novo CGM deve ser diferente do CGM anterior.','form','erro','".Sessao::getId()."');";
            $stJs .= limparResponsavelNovo();
        }
    }
    break;

}

echo $stJs;
