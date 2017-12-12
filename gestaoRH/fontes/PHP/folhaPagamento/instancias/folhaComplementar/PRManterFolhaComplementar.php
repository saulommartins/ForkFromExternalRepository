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
    * Processamento de Abri/Fechar Folha Complementar
    * Data de Criação   : 16/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2008-02-13 09:27:53 -0200 (Qua, 13 Fev 2008) $

    * Casos de uso: uc-04.05.31
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                         );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterFolhaComplementar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoFolhaComplementar();

$obErro = new Erro();
$obTransacao = new Transacao();
$boFlagTransacao = false;
$obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

if (!$obErro->ocorreu()) {

    switch ($stAcao) {
    case "abrir":
        //SistemaLegado::BloqueiaFrames();
        $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao($request->get('inCodPeriodoMovimentacao'));
        $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->setCodComplementar($request->get('inCodComplementar'));
        $obErro = $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->abrirFolhaComplementar($boTransacao);
        $stData = date("d/m/Y",strtotime(substr($obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->getTimestamp(),0,19)));
        if ($_REQUEST['inCodComplementar'] != "") {
            $stMensagem = "Folha Complementar Reaberta em ".$stData;
        } else {
            $stMensagem = "Folha Complementar Aberta em ".$stData;
        }
        SistemaLegado::LiberaFrames();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "fechar":
        //SistemaLegado::BloqueiaFrames();
        $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao($request->get('inCodPeriodoMovimentacao'));        
        $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->setTransacao($obTransacao);
        $obErro = $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->fecharFolhaComplementar($boTransacao);
        $stData = date("d/m/Y",strtotime(substr($obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->getTimestamp(),0,19)));        
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Folha Complementar Fechada em ".$stData,"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir";
        //SistemaLegado::BloqueiaFrames();
        $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->setCodComplementar ( $_REQUEST['inCodComplementar'] );
        $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao( $_REQUEST['inCodPeriodoMovimentacao'] );
        $obErro = $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->excluirFolhaComplementar();
        SistemaLegado::LiberaFrames();
        if ( !$obErro->ocorreu() ) {            
            SistemaLegado::alertaAviso($pgForm,"Folha Complementar Excluída","incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    }

    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obRFolhaPagamentoPeriodoMovimentacao->obTFolhaPagamentoPeriodoMovimentacaoSituacao );
}else{
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
}

?>
