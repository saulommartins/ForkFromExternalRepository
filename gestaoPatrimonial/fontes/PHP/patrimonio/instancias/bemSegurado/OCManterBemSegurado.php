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
    * Data de Criação: 17/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Id: OCManterBemSegurado.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.01.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioApolice.class.php' );
include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioBem.class.php' );
include_once( CAM_GP_PAT_MAPEAMENTO.'TPatrimonioApoliceBem.class.php' );

//Define o nome dos arquivos PHP
$stPrograma = "ManterBemSegurado";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

function montaListaBens($arBens)
{
    global $pgOcul;

    if ( !is_array($arBens) ) {
        $arBens = array();
    }

    $rsBens = new RecordSet();
    $rsBens->preenche( $arBens );

    $obTable = new Table();
    $obTable->setRecordset( $rsBens );
    $obTable->setSummary( 'Lista de Bens da Apólice' );

    $obTable->Head->addCabecalho( 'Código', 15 );
    $obTable->Head->addCabecalho( 'Classificação', 15 );
    $obTable->Head->addCabecalho( 'Descrição', 50 );

    $obTable->Body->addCampo( 'cod_bem', 'C' );
    $obTable->Body->addCampo( '[cod_natureza].[cod_grupo].[cod_especie]', 'C' );
    $obTable->Body->addCampo( 'descricao', 'C' );

    $obTable->Body->addAcao( 'excluir', "JavaScript:ajaxJavaScript(  '".CAM_GP_PAT_INSTANCIAS."bemSegurado/".$pgOcul."?".Sessao::getId()."&inCodBem=%s&inCodApolice=%s', 'excluirBem' );", array( 'cod_bem','cod_apolice' ) );

    $obTable->montaHTML( true );

    return "$('spnBem').innerHTML = '".$obTable->getHtml()."';";
}

switch ($stCtrl) {
    case 'preencheApolice' :
        $stJs.= "limpaSelect($('inCodApolice'),1);";
        if ($_REQUEST['inCodSeguradora'] != '') {
            $obTPatrimonioApolice = new TPatrimonioApolice();
            $obTPatrimonioApolice->setDado( 'numcgm', $_REQUEST['inCodSeguradora'] );
            $obTPatrimonioApolice->recuperaApoliceSeguradora( $rsApolices );

            $inCount = 1;
            while ( !$rsApolices->eof() ) {
                $stSelected = ( $_REQUEST['inCodApolice'] == $rsApolices->getCampo( 'cod_apolice' ) ) ? 'selected' : '';
                $stJs .= "$('inCodApolice').options[".$inCount."] = new Option( '".$rsApolices->getCampo( 'num_apolice' ).' - '.$rsApolices->getCampo( 'dt_vencimento' )."','".$rsApolices->getCampo( 'cod_apolice' )."', '".$stSelected."' );";
                $inCount++;
                $rsApolices->proximo();
            }

        } else {
            $stJs .= montaListaBens( array() );
        }
        break;
    case 'preencheBemPlaca' :
        if ($_REQUEST['stNumPlaca'] != '') {
            $obTPatrimonioBem = new TPatrimonioBem();
            $stFiltro = " WHERE num_placa = '".$_REQUEST['stNumPlaca']."' ";
            $obTPatrimonioBem->recuperaTodos( $rsBem, $stFiltro );
            if ( $rsBem->getNumLinhas() > 0 ) {
                $stJs .= "$('inCodBemInicio').value = '".$rsBem->getCampo('cod_bem')."'; ";
            } else {
                $stJs .= "$('inCodBemInicio').value = ''; ";
                $stJs .= "$('stNumPlaca').value = ''; ";
            }
        } else {
            $stJs .= "$('inCodBemInicio').value = '';";
            $stJs .= "$('stNumPlaca').value = ''; ";
        }
        break;
    case 'preencheLista' :
        $obTPatrimonioApoliceBem = new TPatrimonioApoliceBem();
        $obTPatrimonioApoliceBem->setDado('cod_apolice', $_REQUEST['inCodApolice'] );
        $obTPatrimonioApoliceBem->recuperaBemApolice( $rsBem );
        $inCount = 0;
        $arBem = array();
        while ( !$rsBem->eof() ) {
            $arBem[$_REQUEST['inCodApolice']][$inCount]['cod_apolice'] = $rsBem->getCampo('cod_apolice');
            $arBem[$_REQUEST['inCodApolice']][$inCount]['cod_bem'] = $rsBem->getCampo('cod_bem');
            $arBem[$_REQUEST['inCodApolice']][$inCount]['cod_natureza'] = $rsBem->getCampo('cod_natureza');
            $arBem[$_REQUEST['inCodApolice']][$inCount]['cod_grupo'] = $rsBem->getCampo('cod_grupo');
            $arBem[$_REQUEST['inCodApolice']][$inCount]['cod_especie'] = $rsBem->getCampo('cod_especie');
            $arBem[$_REQUEST['inCodApolice']][$inCount]['descricao'] = $rsBem->getCampo('descricao');
            $arBem[$_REQUEST['inCodApolice']][$inCount]['novo'] = false; //para verificar se o bem foi listado do banco ou incluido
            $inCount++;
            $rsBem->proximo();
        }

        Sessao::write('bens',$arBem);
        $stJs .= montaListaBens( $arBem[$_REQUEST['inCodApolice']] );
        break;
    case 'incluirBem' :
        if ($_REQUEST['inCodBemInicio'] == '') {
            $stMensagem = 'Informe o código inical do bem';
        }
        if ($_REQUEST['inCodApolice'] == '') {
            $stMensagem = 'Seleciona a apólice';
        }
        if ($_REQUEST['inCodSeguradora'] == '') {
            $stMensagem = 'Seleciona a seguradora';
        }

        if (!$stMensagem) {
            if ($_REQUEST['inCodBemFinal'] != '') {
                $stFiltro = " WHERE cod_bem BETWEEN ".$_REQUEST['inCodBemInicio']." AND ".$_REQUEST['inCodBemFinal']." ";
            } else {
                $stFiltro = " WHERE cod_bem = ".$_REQUEST['inCodBemInicio']." ";
            }
            $obTPatrimonioBem = new TPatrimonioBem();
            $obTPatrimonioBem->recuperaTodos( $rsBem, $stFiltro );
            $arBem = Sessao::read('bens');
            $inCount = count( $arBem[$_REQUEST['inCodApolice']] ) ;
            while ( !$rsBem->eof() ) {
                $boInclui = true;
                if ( count( $arBem[$_REQUEST['inCodApolice']] ) > 0 ) {
                    foreach ($arBem[$_REQUEST['inCodApolice']] as $arTemp) {
                        if ( $arTemp['cod_bem'] == $rsBem->getCampo('cod_bem') ) {
                            $boInclui = false;
                            break;
                        }
                    }
                }
                if ($boInclui) {
                    $arBem[$_REQUEST['inCodApolice']][$inCount]['cod_apolice'] = $_REQUEST['inCodApolice'];
                    $arBem[$_REQUEST['inCodApolice']][$inCount]['cod_bem'] = $rsBem->getCampo('cod_bem');
                    $arBem[$_REQUEST['inCodApolice']][$inCount]['cod_natureza'] = $rsBem->getCampo('cod_natureza');
                    $arBem[$_REQUEST['inCodApolice']][$inCount]['cod_grupo'] = $rsBem->getCampo('cod_grupo');
                    $arBem[$_REQUEST['inCodApolice']][$inCount]['cod_especie'] = $rsBem->getCampo('cod_especie');
                    $arBem[$_REQUEST['inCodApolice']][$inCount]['descricao'] = $rsBem->getCampo('descricao');
                    $arBem[$_REQUEST['inCodApolice']][$inCount]['novo'] = true;
                    $inCount++;
                }
                $rsBem->proximo();
            }
            Sessao::write('bens',$arBem);
            $stJs .= "$('inCodBemInicio').value = '';";
            $stJs .= "$('inCodBemFinal').value = '';";
            $stJs .= "$('stNumPlaca').value = '';";
            $stJs .= montaListaBens( $arBem[$_REQUEST['inCodApolice']] );
        } else {
            $stJs.= "alertaAviso('".$stMensagem.".','form','erro','".Sessao::getId()."');";
        }
        break;
    case 'excluirBem' :
        $arBensTemp = array();
        $arBem = Sessao::read('bens');
        $arBemExcluido = Sessao::read('bensExcluidos');
        foreach ($arBem[$_REQUEST['inCodApolice']] as $arTEMP) {
            if ($arTEMP['cod_bem'] != $_REQUEST['inCodBem']) {
                $arBensTemp[] = $arTEMP;
            } else {
                $inExcluido = count( $arBemExcluido[$_REQUEST['inCodApolice']]) + 1;
                $arBemExcluido[$_REQUEST['inCodApolice']][$inExcluido]['cod_bem'] = $arTEMP['cod_bem'];
            }
        }
        $arBem[$_REQUEST['inCodApolice']] = array();
        $arBem[$_REQUEST['inCodApolice']] = $arBensTemp;

        Sessao::write('bens',$arBem);
        Sessao::write('bensExcluidos',$arBemExcluido);

        $stJs .= montaListaBens( $arBem[$_REQUEST['inCodApolice']] );
        break;
}

echo $stJs;
