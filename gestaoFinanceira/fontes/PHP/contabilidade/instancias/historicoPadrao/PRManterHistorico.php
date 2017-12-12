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
    * Página de Processamento de Norma
    * Data de Criação   : 28/05/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Diego Barbosa Victoria
    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @ignore

    * $Id: PRManterHistorico.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_NEGOCIO. "RContabilidadeHistoricoPadrao.class.php");

$stAcao = $request->get("stAcao");

$stPrograma = "ManterHistorico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RContabilidadeHistoricoPadrao;

if ($_POST['boComplemento'] == "Sim") {
    $boComplemento = true;
} else {
    $boComplemento = false;
}

//Trecho de código do filtro
$stFiltro = "&pos=".Sessao::read('pos');
$stFiltro .= "&pg=".Sessao::read('pg');
$stFiltro .= "&paginando=".Sessao::read('paginando');
$filtro = Sessao::read('filtro');
if ( is_array($filtro) ) {
    foreach ($filtro as $stCampo => $stValor) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                if (is_array($stValor2)) {
                    foreach ($stValor2 as $stCampo3 => $stValor3) {
                        $stFiltro .= "&".$stCampo3."=".urlencode( $stValor3 );
                    }
                } else {
                    $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
               }
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

switch ($stAcao) {

    case "incluir":

        $obErro = new Erro;

        if ($_POST['inCodHistoricoInclusao'] >= 800 || $_POST['inCodHistoricoInclusao'] == 0) {
            if ($_POST['inCodHistoricoInclusao'] == 0) {
                $obErro->setDescricao("Este código não pode ser cadastrado!");
            } else if ($_POST['inCodHistoricoInclusao'] <= 899) {
                $obErro->setDescricao("O intervalo 800 a 899 é para uso interno do sistema para os lançamentos de encerramento do exercício");
            } else if ($_POST['inCodHistoricoInclusao'] >= 900 && $_POST['inCodHistoricoInclusao'] <= 999) {
                $obErro->setDescricao("O intervalo de 900 a 999 é para uso interno do sistema para os lançamentos de execução orçamentária");
            }
        } else {
            $obRegra->setCodHistoricoInclusao( $_POST['inCodHistoricoInclusao'] );
            $obRegra->setNomHistorico        ( $_POST['stNomHistorico'] );
            $obRegra->setExercicio           ( Sessao::getExercicio()       );
            $obRegra->setComplemento         ( $boComplemento           );

            $obErro = $obRegra->salvar();
        }

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgForm,"Histórico: ".$_POST['stNomHistorico'],"incluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

    break;
    case "alterar":

        $obRegra->setCodHistorico    ( $_POST['inCodHistorico'] );
        $obRegra->setNomHistorico    ( $_POST['stNomHistorico'] );
        $obRegra->setExercicio       ( Sessao::getExercicio()       );
        $obRegra->setComplemento     ( $boComplemento           );

        $obErro = $obRegra->salvar();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList.$stFiltro,"Histórico: ".$_POST['stNomHistorico'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;
    case "excluir";
        $obRegra->setCodHistorico    ( $_REQUEST['inCodHistorico'] );
        $obRegra->setExercicio       ( Sessao::getExercicio()          );

        $obErro = $obRegra->excluir();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList.$stFiltro,"Histórico: ".$_REQUEST['stNomHistorico'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList,urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
        }

    break;
}

?>
