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
 * Página de Processamento de Macro Objetivos
 * Data de Criação   : 06/05/2009

 * @author Analista      Tonismar Régis Bernardo
 * @author Desenvolvedor Eduardo Paculski Schitz

 * @package URBEM
 * @subpackage

 $Id:$

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_PPA_MAPEAMENTO."TPPAMacroObjetivo.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterMacroobjetivos";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obTPPAMacroObjetivo = new TPPAMacroObjetivo;
$obErro = new Erro;

$stAcao = $request->get('stAcao');

$stFiltro = "";
$arFiltro = Sessao::read('filtro');
if ($arFiltro) {
    foreach ($arFiltro as $stCampo => $stValor) {
        $stFiltro .= $stCampo."=".$stValor."&";
    }
}
$stFiltro .= "pg=".Sessao::read('pg')."&";
$stFiltro .= "pos=".Sessao::read('pos')."&";
$stFiltro .= "stAcao=".$stAcao;

switch ($stAcao) {
    case "incluir":
        if (strlen($_REQUEST['stDescricao']) <= 450) {
            $obTPPAMacroObjetivo->proximoCod($inCodMacro);
            $obTPPAMacroObjetivo->setDado('cod_macro', $inCodMacro);
            $obTPPAMacroObjetivo->setDado('cod_ppa', $_REQUEST['inCodPPA']);
            $obTPPAMacroObjetivo->setDado('descricao', $_REQUEST['stDescricao']);
            $obErro = $obTPPAMacroObjetivo->inclusao();
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgForm."?inCodPPA=".$_REQUEST['inCodPPA'],"Macro Objetivo número: ".$inCodMacro,"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        } else {
            SistemaLegado::exibeAviso("Campo descrição tem tamanho maior que permitido (450).","n_incluir","erro");
        }
    break;

    case "alterar":
        if (strlen($_REQUEST['stDescricao']) <= 450) {
            $obTPPAMacroObjetivo->setDado('cod_ppa', $_REQUEST['inCodPPA']);
            $obTPPAMacroObjetivo->setDado('cod_macro', $_REQUEST['inCodMacro']);
            $obTPPAMacroObjetivo->setDado('descricao', $_REQUEST['stDescricao']);
            $obErro = $obTPPAMacroObjetivo->alteracao();
            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgList."?".$stFiltro,"Macro Objetivo número: ".$_REQUEST['inCodMacro'],"alterar","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            }
        } else {
            SistemaLegado::exibeAviso("Campo descrição tem tamanho maior que permitido (450).","n_incluir","erro");
        }

    break;

    case "excluir":
        $obTPPAMacroObjetivo->setDado('cod_ppa', $_REQUEST['inCodPPA']);
        $obTPPAMacroObjetivo->setDado('cod_macro', $_REQUEST['inCodMacro']);

        $obErro = $obTPPAMacroObjetivo->exclusao();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".$stFiltro,"Macro Objetivo número:".$_REQUEST['inCodMacro'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?".$stFiltro, urlencode($obErro->getDescricao()), "n_excluir", "erro", Sessao::getId(), "../");
        }
    break;

}
?>
