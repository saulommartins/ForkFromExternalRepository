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
* Página de Processamento do CID
* Data de Criação: 04/01/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30865 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.04.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCID.class.php"   );

$link = Sessao::read("link");
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"].'&stFiltroSigla='.$_POST['stFiltroSigla'].'&stFiltroDescricao='.$_POST['stFiltroDescricao'];

//Define o nome dos arquivos PHP
$stPrograma = "ManterCID";
$pgFilt     = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList     = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm     = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc     = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul     = "OC".$stPrograma.".php?stAcao=$stAcao";

$obRPessoalCID  = new RPessoalCID;

switch ($stAcao) {
    case "incluir":
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalTipoDeficiencia.class.php");
        $obTPessoalTipoDeficiencia = new TPessoalTipoDeficiencia();
        $stFiltro = " WHERE num_deficiencia = ".$_POST["inNumTipoDeficiencia"];
        $obTPessoalTipoDeficiencia->recuperaTodos($rsCid,$stFiltro,"",$boTransacao);

        $obRPessoalCID->setSigla     ( $_POST['stSigla'] );
        $obRPessoalCID->setDescricao ( $_POST['stDescricao'] );
        $obRPessoalCID->setCodTipoDeficiencia ( $rsCid->getCampo("cod_tipo_deficiencia") );
        $obErro = $obRPessoalCID->incluir($boTransacao);
         if ( !$obErro->ocorreu() ) {
             sistemaLegado::alertaAviso($pgForm,"CID: ".$_POST['stSigla'],"incluir","aviso", Sessao::getId(), "../");
         } else {
             sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
         }
    break;
    case "alterar":
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalTipoDeficiencia.class.php");
        $obTPessoalTipoDeficiencia = new TPessoalTipoDeficiencia();
        $stFiltro = " WHERE num_deficiencia = ".$_POST["inNumTipoDeficiencia"];
        $obTPessoalTipoDeficiencia->recuperaTodos($rsCid,$stFiltro,"",$boTransacao);

        $obRPessoalCID->setCodCID    ( $_POST['inCodCID']    );
        $obRPessoalCID->setSigla     ( $_POST['stSigla']     );
        $obRPessoalCID->setDescricao ( $_POST['stDescricao'] );
        $obRPessoalCID->setCodTipoDeficiencia ( $rsCid->getCampo("cod_tipo_deficiencia") );
        $obErro = $obRPessoalCID->alterar($boTransacao);
         if ( !$obErro->ocorreu() ) {
             sistemaLegado::alertaAviso($pgList,"CID: ".$_POST['stSigla'],"incluir","aviso", Sessao::getId(), "../");
         } else {
             sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
         }
    break;
    case "excluir":
        $inCodCid = $_GET['inCodCid'];
        $obRPessoalCID->setCodCID ( $inCodCid );
        $obErro = $obRPessoalCID->excluir($boTransacao);
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"CID: ".$_GET['stSigla'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro",Sessao::getId(), "../");
        }
    break;
}
?>
