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
    * Página de processamento para a configuração de atributos dinâmicos
    * Data de Criação   : 02/03/2004

    * @author Cassiano de Vasconsellos Ferreira

    * @ignore

    * $Id: PRManterAtributo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.01
*/

/*
$Log$
Revision 1.4  2006/09/18 10:31:08  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GT_CIM_NEGOCIO."RAtributoCIM.class.php");
include(CAM_GT_CIM_NEGOCIO."RConfiguracao.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtributo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRAtributoCIM = new RAtributoCIM;
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
switch ($stAcao) {
    case "incluir":
        $obRAtributoCIM->setNomeAtributo    ( $_POST["stNomAtributo"] );
        $obRAtributoCIM->setMascara         ( $_POST["stMascara"] );
        $obRAtributoCIM->setCodTipo         ( $_POST["inCodTipoAtributo"] );
        $obRAtributoCIM->setNulo            ( $_POST["boNaoNulo"] );
        $obRAtributoCIM->setValorPadrao     ( $_POST["stValorPadrao"] );
        $obErro = $obRAtributoCIM->incluirAtributo();
        if (!$obErro->ocorreu()) {
            alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$_GET["pg"]."&pos=".$_GET["pos"],"Atributo CIM ".$_POST["stNomAtributo"],"incluir","aviso", Sessao::getId(), "../");
        } else {
            //alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$_GET["pg"]."&pos=".$_GET["pos"],urlencode($obErro->getDescricao()),"n_incluir","erro", Sessao::getId(), "../");
            exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $obRAtributoCIM->setNomeAtributo    ( $_POST["stNomAtributo"] );
        $obRAtributoCIM->setMascara         ( $_POST["stMascara"] );
        $obRAtributoCIM->setCodTipo         ( $_POST["inCodTipo"] );
        $obRAtributoCIM->setCodAtributo     ( $_POST["inCodAtributo"] );
        $obRAtributoCIM->setNulo            ( $_POST["boNaoNulo"] );
        $obRAtributoCIM->setValorPadrao     ( $_POST["stValorPadrao"] );
        $obErro = $obRAtributoCIM->alterarAtributo();
        if (!$obErro->ocorreu()) {
            alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$_GET["pg"]."&pos=".$_GET["pos"],"Atributo CIM ".$_POST["stNomAtributo"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            //alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$_GET["pg"]."&pos=".$_GET["pos"],urlencode($obErro->getDescricao()),"n_alterar","erro", Sessao::getId(), "../");
            exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
    case "excluir";
        $obRAtributoCIM->setCodTipo( $_GET["inCodTipo"] );
        $obRAtributoCIM->setCodAtributo( $_GET["inCodAtributo"] );
        $obErro = $obRAtributoCIM->excluirAtributo();
        if (!$obErro->ocorreu()) {
            alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$_GET["pg"]."&pos=".$_GET["pos"],"Atributo CIM ".$_POST["stNomAtributo"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$_GET["pg"]."&pos=".$_GET["pos"],urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
            //exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;
    case "alterarConfiguracao":
        $obRConfiguracao = new RConfiguracao;
        $obRConfiguracao->setNumeroInscricao     ( $_POST["boNumInscImob"] );
        $obRConfiguracao->setMascaraLote         ( $_POST["stMascaraLote"] );
        $obRConfiguracao->setMascaraInscricao    ( $_POST["stMascaraInscImob"] );
        $obRConfiguracao->setCodModulo           ( $_POST["inCodModulo"] );
        $obRConfiguracao->setExercicio           ( Sessao::getExercicio() );
        $obErro = $obRConfiguracao->salvaConfiguracao();
        if (!$obErro->ocorreu()) {
            alertaAviso("FMManterConfiguracao.php?".Sessao::getId()."&acao=".$_POST['acao'],"Configuracao CIM ","alterar","aviso", Sessao::getId(), "../");
        } else {
            //alertaAviso("FMManterConfiguracao.php?".Sessao::getId(),urlencode($obErro->getDescricao()),"n_alterar","erro", Sessao::getId(), "../");
            exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;
}
?>
