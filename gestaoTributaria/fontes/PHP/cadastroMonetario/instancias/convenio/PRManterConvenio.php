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
    * Página de Processamento de Inclusao/Alteracao de Convenio
    * Data de Criação: 04/10/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: PRManterConvenio.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.04

*/

/*
$Log$
Revision 1.10  2007/02/07 15:57:26  cercato
alteracoes para o convenio trabalhar com numero de variacao.

Revision 1.9  2006/09/15 14:57:44  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONConvenio.class.php"   );
include_once ( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );

$stAcao = $request->get('stAcao');

$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterConvenio";
$pgFilt      = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList      = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc      = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul      = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS        = "JS".$stPrograma.".js";
$pgFormBaixa = "FM".$stPrograma.".php";

$obRMONConvenio = new RMONConvenio;
$obErro         = new Erro;

switch ($stAcao) {
    case "incluir":
        $obRMONAgencia = new RMONAgencia;
        $obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inCodBancoTxt"] );
        $obRMONAgencia->setNumAgencia( $_REQUEST['stNumAgencia'] );
        $obRMONAgencia->consultarAgencia( $rsAgencia );
        $CodBancoAtual = $obRMONAgencia->obRMONBanco->getCodBanco();
        $CodAgenciaAtual = $obRMONAgencia->getCodAgencia();

        $contas = Sessao::read('contas');

        $obRMONContaCorrente = new RMONContaCorrente;
        $obRMONContaCorrente->obRMONAgencia->setCodAgencia( $CodAgenciaAtual );
        $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $CodBancoAtual );

        for ($inX=0; $inX<count($contas); $inX++) {
            $obRMONContaCorrente->setCodigoConta( $contas[$inX]["cod_conta_corrente"] );
            $obRMONContaCorrente->listarContaCorrente( $rsListaCC );
            if ( $rsListaCC->Eof() ) {
                SistemaLegado::exibeAviso("Conta Corrente ".$contas[$inX]['num_conta_corrente']." não pertence ao Banco e Agência selecionados!", "n_incluir","erro",Sessao::getId(), "../" );
                exit;
            }
        }

        $obRMONConvenio->setNumeroConvenio  ( trim($_REQUEST["inNumConvenio"]) );
        $obRMONConvenio->setTipoConvenio    ( trim($_REQUEST["cmbTipoConvenio"]) );
        $obRMONConvenio->setTaxaBancaria    ( trim($_REQUEST["flTaxaBancaria"]) );
        $obRMONConvenio->setCedente         ( trim($_REQUEST["flCedente"]) );
        $obRMONConvenio->setCodigoBanco     ( $CodBancoAtual );
        $obRMONConvenio->setCodigoAgencia   ( $CodAgenciaAtual );
        $obRMONConvenio->setContas  ( $contas );
        if ( count ($contas) < 1 ) {
            $obErro->setDescricao ('O Convênio precisa de pelo menos uma conta corrente vinculada');
            sistemaLegado::exibeAviso( urlencode($obErro->getDescricao()), "n_incluir", "erro", Sessao::getId(), "../");
            exit;
        } else {
            $obErro = $obRMONConvenio->incluirConvenio();
            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgForm . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Convênio ".$_REQUEST["inNumConvenio"],"incluir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()), "n_incluir", "erro", Sessao::getId(), "../");
                exit;
            }
        }//fim do IF count
        break;

    case "alterar":
        $obRMONAgencia = new RMONAgencia;
        $obRMONAgencia->obRMONBanco->setNumBanco( $_REQUEST["inCodBancoTxt"] );
        $obRMONAgencia->setNumAgencia( $_REQUEST['stNumAgencia'] );
        $obRMONAgencia->consultarAgencia( $rsAgencia );
        $CodBancoAtual = $obRMONAgencia->obRMONBanco->getCodBanco();
        $CodAgenciaAtual = $obRMONAgencia->getCodAgencia();

        $contas = Sessao::read('contas');

        $obRMONContaCorrente = new RMONContaCorrente;
        $obRMONContaCorrente->obRMONAgencia->setCodAgencia( $CodAgenciaAtual );
        $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco( $CodBancoAtual );

        if ( count ($contas) < 1 ) {
            $obErro->setDescricao ('O Convênio precisa de pelo menos uma conta corrente vinculada');
            sistemaLegado::exibeAviso( urlencode($obErro->getDescricao()), "n_incluir", "erro", Sessao::getId(), "../");
            exit;
        }

        for ($inX=0; $inX<count($contas); $inX++) {
            $obRMONContaCorrente->setCodigoConta( $contas[$inX]["cod_conta_corrente"] );
            $obRMONContaCorrente->listarContaCorrente( $rsListaCC );
            if ( $rsListaCC->Eof() ) {
                SistemaLegado::exibeAviso("Conta Corrente ".$contas[$inX]['num_conta_corrente']." não pertence ao Banco e Agência selecionados!", "n_alterar","erro",Sessao::getId(), "../" );
                exit;
            }
        }

        $obRMONConvenio->setCodigoConvenio  ( trim($_REQUEST["inCodConvenio"]) );
        $obRMONConvenio->setNumeroConvenio  ( trim($_REQUEST["inNumConvenio"]) );
        $obRMONConvenio->setTipoConvenio    ( trim($_REQUEST["inCodTipo"]) );
        $obRMONConvenio->setTaxaBancaria    ( trim($_REQUEST["flTaxaBancaria"]) );
        $obRMONConvenio->setCedente         ( trim($_REQUEST["flCedente"]) );
        $obRMONConvenio->setCodigoBanco     ( $CodBancoAtual );
        $obRMONConvenio->setCodigoAgencia   ( $CodAgenciaAtual );
        $obRMONConvenio->setContas  ( $contas );

        $obErro = $obRMONConvenio->alterarConvenio();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Convênio ".$_REQUEST["inNumConvenio"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_alterar", "erro", Sessao::getId(), "../");
        }
        break;

    case "excluir":
        $obRMONConvenio->setCodigoConvenio  ( trim($_REQUEST["inCodConvenio"]) );
        $obErro = $obRMONConvenio->excluirConvenio();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Convênio ".$_REQUEST["inNumConvenio"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
        break;
}
?>
