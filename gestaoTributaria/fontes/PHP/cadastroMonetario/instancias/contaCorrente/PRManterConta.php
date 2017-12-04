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
    * Página de Processamento para Cadastro de Conta Corrente

    * Data de Criação   : 04/11/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Lizandro Kirst da Silva
    * @ignore

    * $Id: PRManterConta.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.03

*/

/*
$Log$
Revision 1.7  2006/09/15 14:57:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONAgencia.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONContaCorrente.class.php" );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterConta";
$pgFilt      = "FL".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgList      = "LS".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'].$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgProc      = "PR".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgOcul      = "OC".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgJS        = "JS".$stPrograma.".js";
$pgFormBaixa = "FM".$stPrograma.".php";

$obRMONContaCorrente = new RMONContaCorrente;
$obRMONAgencia = new RMONAgencia;
$obErro      = new Erro;
switch ($_REQUEST['stAcao']) {

    case "incluir":
        $obRMONContaCorrente->setNumeroConta                          (trim($_REQUEST["stNumeroConta"]));
        $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setNumBanco (trim($_REQUEST["inCodBancoTxt"])   );
        $obRMONContaCorrente->obRMONAgencia->setNumAgencia            (trim($_REQUEST["stNumAgencia"]) );
        $obRMONContaCorrente->setDataCriacao                          (trim($_REQUEST["dtDataCriacao"]));
        $obRMONContaCorrente->setCodigoTipoConta                      ($_REQUEST["inCodTipoConta"]);
        $obErro = $obRMONContaCorrente->incluirContaCorrente();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Conta-Corrente ".$_REQUEST['stNumeroConta'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgForm, urlencode($obErro->getDescricao()),"n_incluir", "erro", Sessao::getId(), "../");
        }
    break;

    case "alterar":
        $obRMONContaCorrente->obRMONAgencia->setCodAgencia ( trim($_REQUEST["inCodAgencia"]) );
        $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco ( trim($_REQUEST["inCodBanco"])   );
        $obRMONContaCorrente->setCodigoConta(trim($_REQUEST["inCodigoConta"]));
        $obRMONContaCorrente->setNumeroConta(trim($_REQUEST["stNumeroConta"]));
        $obRMONContaCorrente->setDataCriacao(trim($_REQUEST["dtDataCriacao"]));
        $obRMONContaCorrente->setCodigoTipoConta($_REQUEST["inCodTipoConta"]);
        $obErro = $obRMONContaCorrente->alterarContaCorrente();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Conta-Corrente ".$_REQUEST['stNumeroConta'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_alterar", "erro", Sessao::getId(), "../");
        }
    break;

    case "excluir":
        $obRMONContaCorrente->obRMONAgencia->setCodAgencia  ( trim($_REQUEST["inCodAgencia"]) );
        $obRMONContaCorrente->obRMONAgencia->obRMONBanco->setCodBanco ( trim($_REQUEST["inCodBanco"])   );
        $obRMONContaCorrente->setCodigoConta ( trim($_REQUEST["inCodigoConta"]) );
        $obErro = $obRMONContaCorrente->excluirContaCorrente();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Conta-Corrente ".$_REQUEST["stNumeroConta"],"excluir","aviso", Sessao::getId(), "../");

        } else {
            sistemaLegado::alertaAviso( $pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'], urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;

}
