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
    * Página de Processamento da Inclusao/Alteracao de Detalhamento de Destinação de Recursos
    * Data de Criação   : 31/10/2007

    * @author Desenvolvedor: Anderson cAko Konze

    $Id: PRDetalhamentoDestinacoes.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.38

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include CAM_FW_INCLUDE . 'cabecalho.inc.php';
include CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoDetalhamentoDestinacaoRecurso.class.php';
include CAM_GF_PPA_NEGOCIO . 'RPPAManterPPA.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "DetalhamentoDestinacoes";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obTOrcamentoDetalhamentoDestinacaoRecurso = new TOrcamentoDetalhamentoDestinacaoRecurso;

$stAcao = $request->get('stAcao');

$obErro = new Erro();

//recupera os exercicios do ppa para propagar o recurso pelos exercicios do ppa
$obRPPAManterPPA = new RPPAManterPPA();
$obRPPAManterPPA->stExercicio = Sessao::getExercicio();
$obRPPAManterPPA->listByExercicio($rsRecordSet);

$stExercicio      = (int) Sessao::getExercicio();
$stExercicioFinal = (int) $rsRecordSet->getCampo('ano_final');

switch ($stAcao) {
case "incluir":
    $obTOrcamentoDetalhamentoDestinacaoRecurso->setDado('cod_detalhamento', $_POST['inCodDetalhamento']);
    $obTOrcamentoDetalhamentoDestinacaoRecurso->setDado('descricao', $_POST['stDescricao']);

    //faz o insert para cada ano até o ano_final do ppa
    for ($stExercicio; $stExercicio <= $stExercicioFinal; $stExercicio++) {
        if (!$obErro->ocorreu()) {
            $obTOrcamentoDetalhamentoDestinacaoRecurso->setDado ('exercicio', $stExercicio);
            $obErro = $obTOrcamentoDetalhamentoDestinacaoRecurso->inclusao($boTransacao);
        }
    }

    if (!$obErro->ocorreu()) {
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];
        SistemaLegado::alertaAviso($pgForm."?".$stFiltro,"Detalhamento ".$_POST['inCodDetalhamento']." - ".$_POST['stDescricao'],"incluir","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode("Já existe um Detalhamento de Destinações com o código informado (".$_POST['inCodDetalhamento'].")"),"n_incluir","erro");
    }

    break;
case "alterar":

    $obTOrcamentoDetalhamentoDestinacaoRecurso->setDado ('cod_detalhamento', $_POST['inCodDetalhamento'] );
    $obTOrcamentoDetalhamentoDestinacaoRecurso->setDado ('descricao', $_POST['stDescricao']);

    //faz o insert para cada ano até o ano_final do ppa
    for ($stExercicio; $stExercicio <= $stExercicioFinal; $stExercicio++) {
        if (!$obErro->ocorreu()) {
            $obTOrcamentoDetalhamentoDestinacaoRecurso->setDado('exercicio', $stExercicio);
            $boTransacao = $obTOrcamentoDetalhamentoDestinacaoRecurso->alteracao($boTransacao);
        }
    }

    if (!$obErro->ocorreu()) {
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];
        SistemaLegado::alertaAviso($pgList."?".$stFiltro,"Grupo ".$_POST['inCodDetalhamento']." - ".$_POST['stDescricao'],"alterar","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
    }

    break;
case "excluir":
    $obTOrcamentoDetalhamentoDestinacaoRecurso->setDado ('cod_detalhamento', $_REQUEST['cod_detalhamento'] );
    //faz o insert para cada ano até o ano_final do ppa
    for ($stExercicio; $stExercicio <= $stExercicioFinal; $stExercicio++) {
        if (!$obErro->ocorreu()) {
            $obTOrcamentoDetalhamentoDestinacaoRecurso->setDado ('exercicio', $stExercicio);
            $obErro = $obTOrcamentoDetalhamentoDestinacaoRecurso->exclusao($boTransacao);
        }
    }

    if (!$obErro->ocorreu()) {
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];
        SistemaLegado::alertaAviso($pgList."?stAcao=excluir&".$stFiltro,"Detalhamento ".$_REQUEST['cod_detalhamento']." - ".$_REQUEST['stDescricao'],"excluir","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
    }

    break;
}

?>
