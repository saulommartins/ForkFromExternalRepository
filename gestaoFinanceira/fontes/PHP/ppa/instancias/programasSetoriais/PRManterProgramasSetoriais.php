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
 *  Script de processamento dos programas setoriais
 *
 * @category    Urbem
 * @package     PPA
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id: $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_PPA_MAPEAMENTO . 'TPPAProgramaSetorial.class.php';

$obTransacao = new Transacao();
$obTPPAProgramaSetorial = new TPPAProgramaSetorial;

$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

switch ($_REQUEST['stAcao']) {
case 'incluir':
    //verifica se nao existe um programa setorial com a mesma descricao
    $stFiltro = "
        WHERE cod_macro = " . $_REQUEST['inCodMacroObjetivo'] . "
          AND descricao = '" . $_REQUEST['stDescricao'] . "'
    ";
    $obTPPAProgramaSetorial->recuperaTodos($rsProgramaSetorial,$stFiltro,'',$boTransacao);

    if ($rsProgramaSetorial->getNumLinhas() > 0) {
        $obErro->setDescricao('Já existe um Programa Setorial com este nome para este Macro Objetivo');
    }

    if (!$obErro->ocorreu()) {
        $obTPPAProgramaSetorial->proximoCod($inCodSetorial,$boTransacao);
        $obTPPAProgramaSetorial->setDado('cod_macro',$_REQUEST['inCodMacroObjetivo']);
        $obTPPAProgramaSetorial->setDado('cod_setorial',$inCodSetorial);
        $obTPPAProgramaSetorial->setDado('descricao',$_REQUEST['stDescricao']);

        $obErro = $obTPPAProgramaSetorial->inclusao($boTransacao);

        SistemaLegado::alertaAviso('FMManterProgramasSetoriais.php?stAcao=' . $_REQUEST['stAcao'], $inCodSetorial, 'incluir', 'aviso', Sessao::getId(), '../');
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()).'!',"n_incluir","erro");
    }

    break;
case 'alterar':
    //verifica se nao existe um programa setorial com a mesma descricao
    $stFiltro = "
        WHERE cod_macro    = " . $_REQUEST['inCodMacroObjetivo'] . "
          AND cod_setorial <> " . $_REQUEST['inCodSetorial'] . "
          AND descricao    = '" . $_REQUEST['stDescricao'] . "'
    ";
    $obTPPAProgramaSetorial->recuperaTodos($rsProgramaSetorial,$stFiltro,'',$boTransacao);

    if ($rsProgramaSetorial->getNumLinhas() > 0) {
        $obErro->setDescricao('Já existe um Programa Setorial com este nome para este Macro Objetivo');
    }

    if (!$obErro->ocorreu()) {
        $obTPPAProgramaSetorial->setDado('cod_macro',$_REQUEST['inCodMacroObjetivo']);
        $obTPPAProgramaSetorial->setDado('cod_setorial',$_REQUEST['inCodSetorial']);
        $obTPPAProgramaSetorial->setDado('descricao',$_REQUEST['stDescricao']);

        $obErro = $obTPPAProgramaSetorial->alteracao($boTransacao);

        SistemaLegado::alertaAviso('LSManterProgramasSetoriais.php?stAcao=' . $_REQUEST['stAcao'], $_REQUEST['inCodSetorial'], 'alterar', 'aviso', Sessao::getId(), '../');
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()).'!',"n_alterar","erro");
    }

    break;
case 'excluir':
    $obTPPAProgramaSetorial->setDado('cod_setorial', $_REQUEST['inCodSetorial']);
    $obErro = $obTPPAProgramaSetorial->exclusao($boTransacao);

    if (!$obErro->ocorreu()) {
        SistemaLegado::alertaAviso('LSManterProgramasSetoriais.php?stAcao=' . $_REQUEST['stAcao'], $_REQUEST['inCodSetorial'], 'excluir', 'aviso', Sessao::getId(), '../');
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()).'!',"n_excluir","erro");
    }

    break;
}

$obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTPPAProgramaSetorial);

?>
