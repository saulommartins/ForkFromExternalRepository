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
* Página de Processamento de Empresa
* Data de Criação   :  07/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30880 $
$Name$
$Author: melo $
$Date: 2006-12-21 08:48:08 -0200 (Qui, 21 Dez 2006) $

* Casos de uso: uc-04.06.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioFornecedorValeTransporte.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterFornecedor";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRBeneficioFornecedorValeTransporte  = new RBeneficioFornecedorValeTransporte;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "incluir":
        $obRBeneficioFornecedorValeTransporte->setNumCGM ( $_POST['inNumCGM'] );
        $obErro = $obRBeneficioFornecedorValeTransporte->incluirFornecedorValeTransporte();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm,"Empresa fornecedora: ".$_POST['inNumCGM'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso('CGM já incluído como fornecedor.',"n_incluir","erro");
        }
    break;

    case "excluir":
        $obRBeneficioFornecedorValeTransporte->setNumCGM( $_GET['inNumCGM'] );
        $obErro = $obRBeneficioFornecedorValeTransporte->excluirFornecedorValeTransporte();
        $stFiltro  = "pg=".$arSessaoLink['pg']."&";
        $stFiltro .= "pos=".$arSessaoLink['pos']."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList."?".$stFiltro,"Empresa fornecedora: ".$_GET['inNumCGM'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList."?".$stFiltro,urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;

}
?>
