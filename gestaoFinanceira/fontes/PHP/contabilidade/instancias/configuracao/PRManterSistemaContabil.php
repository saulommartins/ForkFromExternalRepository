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
    * Página de Processamento Sistema Contábil
    * Data de Criação   : 10/11/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.01
*/

/*
$Log$
Revision 1.4  2006/07/05 20:50:46  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_CONT_NEGOCIO. "RContabilidadeSistemaContabil.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterSistemaContabil";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";

$obRegra = new RContabilidadeSistemaContabil;

switch ($stAcao) {

    case "incluir":

        $obRegra->setCodSistema    ( $_POST['inCodSistema'] );
        $obRegra->setNomSistema    ( $_POST['stNomSistema'] );
        $obRegra->setExercicio           ( Sessao::getExercicio()       );

        $obErro = $obRegra->incluir();

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgForm,"Sistema: ".$_POST['stNomSistema'],"incluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

    break;
    case "alterar":
        $obRegra->setCodSistema( $_POST['inCodSistema'] );
        $obRegra->setNomSistema( $_POST['stNomSistema'] );
        $obRegra->setExercicio       ( Sessao::getExercicio()       );

        $obErro = $obRegra->alterar();

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgFilt,"Sistema: ".$_POST['stNomSistema'],"alterar","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");

    break;
    case "excluir";
        $obRegra->setCodSistema( $_REQUEST['inCodSistema'] );
        $obRegra->setExercicio       ( Sessao::getExercicio()          );

        $obErro = $obRegra->excluir();

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgList,"Sistema: ".$_REQUEST['stNomSistema'],"excluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::alertaAviso($pgList,"Sistema: ".urlencode( $obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");

    break;
}

?>
