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
    * Página de Processamento Almoxarifado
    * Data de Criação   : 28/10/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Er Galvão Abbott

    * @ignore

    $Revision: 16570 $
    $Name$
    $Autor: $
    $Date: 2006-10-09 12:02:48 -0300 (Seg, 09 Out 2006) $

    * Casos de uso: uc-03.03.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_ALM_NEGOCIO. "RAlmoxarifadoAlmoxarifado.class.php");

$stAcao = $request->get("stAcao");

//Define o nome dos arquivos PHP
$stPrograma = "ManterAlmoxarifado";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";

$obRegra = new RAlmoxarifadoAlmoxarifado;

switch ($stAcao) {
    case "incluir":

        $obRegra->obRCGMAlmoxarifado->setNumCGM ( $_POST['inCGMAlmoxarifado'] );
        $obRegra->obRCGMResponsavel->setNumCGM  ( $_POST['inCGMResponsavel']  );
        $obRegra->obRCGMAlmoxarifado->consultarCGM($rsCGM);

        $obErro = $obRegra->incluir();

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgForm, $obRegra->inCodigo." - ".$obRegra->obRCGMAlmoxarifado->getNomCGM(),"incluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

    break;
    case "alterar":

        $obRegra->setCodigo($_POST['inCodigo']);

        $obRegra->obRCGMAlmoxarifado->setNumCGM     ( $_POST['inCGMAlmoxarifado']);
        $obRegra->obRCGMResponsavel->setNumCGM      ( $_POST['inCGMResponsavel'] );
        $obRegra->obRCGMAlmoxarifado->consultarCGM($rsCGM);

        $obErro = $obRegra->alterar();

        if (!$obErro->ocorreu())
            SistemaLegado::alertaAviso($pgList,$obRegra->inCodigo." - ".$obRegra->obRCGMAlmoxarifado->getNomCGM(),"alterar","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");

    break;
    case "excluir";

        $obRegra->setCodigo($_REQUEST['inCodigo']);

        $obRegra->obRCGMAlmoxarifado->setNumCGM     ( $_REQUEST['inCGMAlmoxarifado']);
        $obRegra->obRCGMResponsavel->setNumCGM      ( $_REQUEST['inCGMResponsavel'] );

        $obRegra->obRCGMAlmoxarifado->consultarCGM($rsCGM);

        $obErro = $obRegra->excluir();

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgList,$obRegra->inCodigo." - ".trim($obRegra->obRCGMAlmoxarifado->getNomCGM()),"excluir","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir","O Almoxarifado ".$obRegra->inCodigo." já está sendo usado pelo sistema","n_excluir","erro", Sessao::getId(), "../");

    break;
}

?>
