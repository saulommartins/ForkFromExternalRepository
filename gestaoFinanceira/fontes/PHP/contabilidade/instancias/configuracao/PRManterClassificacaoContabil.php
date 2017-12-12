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
    * Página de Processamento Classificação Contábil
    * Data de Criação   : 10/11/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2007-07-23 10:29:33 -0300 (Seg, 23 Jul 2007) $

    * Casos de uso: uc-02.02.01
*/

/*
$Log$
Revision 1.5  2007/07/23 13:29:33  vitor
Bug#9699#

Revision 1.4  2006/07/05 20:50:46  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_NEGOCIO. "RContabilidadeClassificacaoContabil.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterClassificacaoContabil";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RContabilidadeClassificacaoContabil;

switch ($stAcao) {

    case "incluir":

        $obRegra->setCodClassificacao    ( $_POST['inCodClassificacao'] );
        $obRegra->setNomClassificacao    ( $_POST['stNomClassificacao'] );
        $obRegra->setExercicio           ( Sessao::getExercicio()       );

        $obErro = $obRegra->incluir();

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgForm,"Classificação: ".$_POST['stNomClassificacao'],"incluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

    break;
    case "alterar":

        $obRegra->setCodClassificacao( $_POST['inCodClassificacao'] );
        $obRegra->setNomClassificacao( $_POST['stNomClassificacao'] );
        $obRegra->setExercicio       ( Sessao::getExercicio()       );

        $obErro = $obRegra->alterar();

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgFilt,"Classificação: ".$_POST['stNomClassificacao'],"alterar","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");

    break;
    case "excluir";
        $obRegra->setCodClassificacao( $_REQUEST['inCodClassificacao'] );
        $obRegra->setExercicio       ( Sessao::getExercicio()          );

        $obErro = $obRegra->excluir();

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgList,"Classificação: ".$_REQUEST['stNomClassificacao'],"excluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::alertaAviso($pgList,"Esta classificação já está sendo utilizada.","n_excluir","erro", Sessao::getId(), "../");

    break;
}

?>
