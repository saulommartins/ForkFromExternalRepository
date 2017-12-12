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
* Arquivo de instância para tipo de normas
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 18742 $
$Name$
$Author: cassiano $
$Date: 2006-12-13 09:43:08 -0200 (Qua, 13 Dez 2006) $

Casos de uso: uc-01.04.01
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include(CAM_GA_NORMAS_NEGOCIO."RTipoNorma.class.php");

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".Sessao::read('link_pg')."&pos=".Sessao::read('link_pos');

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoNorma";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRegra = new RTipoNorma;

//mensagem de erro
$stExcluir = "Não é possível excluir este tipo de norma, provavelmente já está relacionado com uma norma.";
$inCodAtributosSelecionados = $_REQUEST["inCodAtributosSelecionados"];

switch ($stAcao) {

    case "incluir":
        for ($inCount=0; $inCount<count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRegra->obRCadastroDinamico->addAtributosDinamicos($inCodAtributo);
        }
        $obRegra->setNomeTipoNorma ( $_POST['stNomeTipoNorma'] );
        $obErro = $obRegra->salvar();

        if ( !$obErro->ocorreu() )
            sistemaLegado::alertaAviso($pgForm,"Tipo de Norma: ".$_POST['stNomeTipoNorma'],"incluir","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");

    break;
    case "alterar":
        for ($inCount=0; $inCount<count($inCodAtributosSelecionados); $inCount++) {
            $inCodAtributo = $inCodAtributosSelecionados[ $inCount ];
            $obRegra->obRCadastroDinamico->addAtributosDinamicos($inCodAtributo);
        }
        $obRegra->setCodTipoNorma  ( $_POST['inCodTipoNorma'] );
        $obRegra->setNomeTipoNorma ( $_POST['stNomeTipoNorma'] );
        $obErro = $obRegra->salvar();

        if ( !$obErro->ocorreu() )
            sistemaLegado::alertaAviso($pgList,"Tipo de Norma: ".$_POST['stNomeTipoNorma'],"alterar","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");

    break;
    case "excluir";
        $obRegra->setCodTipoNorma  ( $_GET['inCodTipoNorma'] );
        $obRegra->consultar( $rsTipoNorma );
        $obErro = $obRegra->excluir();

        if ( !$obErro->ocorreu() )
            sistemaLegado::alertaAviso($pgList,"Tipo de Norma: ".$obRegra->getNomeTipoNorma(),"excluir","aviso", Sessao::getId(), "../");
        else
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");

    break;
}

?>
