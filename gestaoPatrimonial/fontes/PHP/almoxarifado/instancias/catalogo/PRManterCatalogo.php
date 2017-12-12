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
    * Página de Processamento Almoxarifado
    * Data de Criação   : 11/11/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

    * Casos de uso: uc-03.03.04

    $Id: PRManterCatalogo.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO. "RAlmoxarifadoCatalogo.class.php");

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterCatalogo";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";

$stFiltro = '';

if (Sessao::read('Valores')) {
    foreach ( Sessao::read('Valores') as $stCampo => $stValor ) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$obRegra = new RAlmoxarifadoCatalogo;

switch ($stAcao) {
    case "incluir":

        $obRegra->setCodigo             ( $_POST['inCodigo'] );
        $obRegra->setDescricao          ( $_POST['stDescricaoCatalogo'] );

        $arValores = Sessao::read('Valores');

        for ($inPosTransf = 0; $inPosTransf < count($arValores); $inPosTransf++) {
            $obRegra->addCatalogoNivel();

            $obRegra->roCatalogoNivel->setMascara($arValores[$inPosTransf]['mascara']);
            $obRegra->roCatalogoNivel->setDescricao($arValores[$inPosTransf]['descricao']);
        }

        $obErro = $obRegra->incluir();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm, $obRegra->inCodigo." - ".$obRegra->stDescricao,"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;
    case "alterar":

        $obRegra->setCodigo($_POST['inCodigo']);
        $obRegra->setDescricao($_POST['stDescricaoCatalogo']);

        $arValores = Sessao::read('Valores');

        for ($inPosTransf = 0; $inPosTransf < count($arValores); $inPosTransf++) {
            $obRegra->addCatalogoNivel();
            $obRegra->roCatalogoNivel->setNivel($arValores[$inPosTransf]['nivel']);
            $obRegra->roCatalogoNivel->setMascara($arValores[$inPosTransf]['mascara']);
            $obRegra->roCatalogoNivel->setDescricao($arValores[$inPosTransf]['descricao']);
        }
        $obErro = $obRegra->alterar();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList, $obRegra->inCodigo." - ".$obRegra->stDescricao,"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir";

        $obRegra->setCodigo($_REQUEST['inCodigo']);
        $obErro = $obRegra->excluir();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList,$obRegra->inCodigo." - ".$obRegra->getDescricao(),"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
        }
}

?>
