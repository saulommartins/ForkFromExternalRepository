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
    * Página de Processamento de Lancamento
    * Data de Criação   : 16/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    * $Id: PRManterLancamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.04
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_CONT_NEGOCIO."RContabilidadeLancamentoValor.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterLancamento";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRContabilidadeLancamentoValor = new RContabilidadeLancamentoValor;

//Trecho de código do filtro
$stFiltro = "&pos=".Sessao::read('pos');
$stFiltro .= "&pg=".Sessao::read('pg');
$stFiltro .= "&paginando=".Sessao::read('paginando');
$filtro = Sessao::read('filtro');
if (isset($filtro)) {
    foreach ($filtro as $stCampo => $stValor) {
        if (is_array($stValor)) {
            foreach ($stValor as $stCampo2 => $stValor2) {
                if (is_array($stValor2) ) {
                    $stFiltro .= "&".$stCampo2."=".implode( ',' , $stValor2 );
                } else {
                    $stFiltro .= "&".$stCampo2."=".urlencode( $stValor2 );
                }
            }
        } else {
            $stFiltro .= "&".$stCampo."=".urlencode( $stValor );
        }
    }
}

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "incluir":

        if ($_POST['inCodContaDebito'] == $_POST['inCodContaCredito']) {
           SistemaLegado::exibeAviso("As contas a débito e a crédito devem ser diferentes.","n_incluir","erro");
           break;
        }

        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $_POST['inCodLote'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setNomLote( $_POST['stNomLote'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setDtLote( $_POST['stDtLote'] );
        $obRContabilidadeLancamentoValor->setContaDebito( $_POST['inCodContaDebito'] );
        $obRContabilidadeLancamentoValor->setContaCredito( $_POST['inCodContaCredito'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( $_POST['inCodHistorico'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->setBoComplemento( $_POST['boComplemento'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->setComplemento( $_POST['stComplemento'] );
        $nuValor = str_replace('.','',$_POST['nuValor']);
        $nuValor = str_replace(',','.',$nuValor);
        $obRContabilidadeLancamentoValor->setValor(  $nuValor );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );

        if ($_POST['inCodContaCredito'] && $_POST['inCodContaDebito']) {
            $obErro = $obRContabilidadeLancamentoValor->buscaSistemaContabilCreditoDebito( $rsSistemaContabil );
            if ( !$obErro->ocorreu() ) {
                if ( $rsSistemaContabil->getCampo("cod_sistema_credito") == $rsSistemaContabil->getCampo("cod_sistema_debito") ) {
                    $obErro = $obRContabilidadeLancamentoValor->incluir();
                } else {
                    $obErro->setDescricao("As contas Débito e Crédito devem pertencer o mesmo Sistema Contábil.");
                }
            }
        } else $obErro = $obRContabilidadeLancamentoValor->incluir();

        if ( !$obErro->ocorreu() ) {
            $pgForm .= "?".Sessao::getId();
            $pgForm .= "&inCodEntidade=".$_POST['inCodEntidade'];
            $pgForm .= "&inCodLote=".$_POST['inCodLote'];
            $pgForm .= "&stNomLote=".$_POST['stNomLote'];
            $pgForm .= "&stDtLote=".$_POST['stDtLote'];
            SistemaLegado::alertaAviso($pgForm, $_POST['inCodLote']." - ".$obRContabilidadeLancamentoValor->obRContabilidadeLancamento->getSequencia(), "incluir", "aviso", Sessao::getId(), "../");
        } else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    break;
    case "alterar":

        if ($_POST['inCodContaDebito'] == $_POST['inCodContaCredito']) {
           SistemaLegado::exibeAviso("As contas a débito e a crédito devem ser diferentes.","n_incluir","erro");
           break;
        }

        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_POST['inCodEntidade'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->setSequencia( $_POST['inSequencia'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $_POST['inCodLote'] );
        $obRContabilidadeLancamentoValor->setContaDebito( $_POST['inCodContaDebito'] );
        $obRContabilidadeLancamentoValor->setContaCredito( $_POST['inCodContaCredito'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( $_POST['inCodHistorico'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->setComplemento( $_POST['stComplemento'] );
        $nuValor = str_replace('.','',$_POST['nuValor']);
        $nuValor = str_replace(',','.',$nuValor);
        $obRContabilidadeLancamentoValor->setValor( $nuValor );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
        $obErro = $obRContabilidadeLancamentoValor->buscaSistemaContabilCreditoDebito( $rsSistemaContabil );

        if ( !$obErro->ocorreu() ) {
            if ( $rsSistemaContabil->getCampo("cod_sistema_credito") == $rsSistemaContabil->getCampo("cod_sistema_debito") ) {
                $obErro = $obRContabilidadeLancamentoValor->alterar();
            } else {
                $obErro->setDescricao("As contas Débito e Crédito devem pertencer o mesmo Sistema Contábil.");
            }
        }
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".$stFiltro, $_POST['inCodLote']." - ".$_POST['inSequencia'], "alterar", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir":
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->setSequencia( $_GET['inSequencia'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setCodLote( $_GET['inCodLote'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeHistoricoPadrao->setCodHistorico( $_GET['inCodHistorico'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setExercicio( Sessao::getExercicio() );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $_GET['inCodEntidade'] );
        $obRContabilidadeLancamentoValor->setTipoValor( $_GET['stTipoValor'] );
        $obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->setTipo( $_GET['stTipo'] );

        $obErro = $obRContabilidadeLancamentoValor->excluir();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso( $pgList."?stAcao=excluir&".$stFiltro, $_GET['inCodLote'] .' - '.$_GET['inSequencia'],"excluir","aviso",Sessao::getId(),"../");
        } else {
            SistemaLegado::alertaAviso( $pgList."?stAcao=excluir&".$stFiltro, urlencode($obErro->getDescricao()), "n_excluir","erro",Sessao::getId(),"../" );
        }
    // */
    break;
}
?>
