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
* Página de Processamento do Conselho
* Data de Criação: 09/08/2005

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30860 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.04.42
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalConselho.class.php"   );

$arLink = Sessao::read('link');
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"].'&stFiltroSiglaConselho='.$_POST['stFiltroSiglaConselho'].'&stFiltroDescricaoConselho='.$_POST['stFiltroDescricaoConselho'];

//Define o nome dos arquivos PHP
$stPrograma = "ManterConselho";
$pgFilt     = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList     = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm     = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc     = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul     = "OC".$stPrograma.".php?stAcao=$stAcao";

$obRPessoalConselho  = new RPessoalConselho;

switch ($stAcao) {
    case "incluir":
        $obRPessoalConselho->setDescricao ( $_POST['stDescricaoConselho'] );
        $obRPessoalConselho->setSigla     ( $_POST['stSiglaConselho'    ] );
        $obErro = $obRPessoalConselho->incluirConselho($boTransacao);
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm,"Conselho: ".$_POST['stSiglaConselho'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $obRPessoalConselho->setCodConselho ( $_POST['inCodConselho'      ] );
        $obRPessoalConselho->setDescricao   ( $_POST['stDescricaoConselho'] );
    $obRPessoalConselho->setSigla       ( $_POST['stSiglaConselho'    ] );
        $obErro = $obRPessoalConselho->alterarConselho($boTransacao);
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList, "Conselho: ".$_POST['stSiglaConselho'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir":
        $obRPessoalConselho->setCodConselho ( $_GET['inCodConselho'] );
        $obErro = $obRPessoalConselho->excluirConselho($boTransacao);
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Conselho: ".$_GET['stSiglaConselho'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;
}
?>
