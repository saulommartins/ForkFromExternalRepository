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
    * Página de Processamento de Comissao de Avaliacao
    * Data de Criação   : 14/07/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.01.03
*/

/*
$Log$
Revision 1.4  2006/07/05 20:42:33  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoPrograma.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "Programa";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obROrcamentoPrograma  = new ROrcamentoPrograma;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

switch ($stAcao) {
    case "incluir":
        $obROrcamentoPrograma->setCodPrograma ( $_POST['inNumeroPrograma'] );
        $obROrcamentoPrograma->setDescricao   ( $_POST['stDescricao']      );
        $obErro = $obROrcamentoPrograma->incluir();
        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Programa: ".$obROrcamentoPrograma->getCodPrograma(),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $arCodPrograma = explode( "/" , $_POST['inCodPrograma'] );
        $obROrcamentoPrograma->setCodPrograma( $arCodPrograma[0]     );
        $obROrcamentoPrograma->setExercicio  ( $arCodPrograma[1]     );
        $obROrcamentoPrograma->setDescricao  ( $_POST['stDescricao'] );
        $obErro = $obROrcamentoPrograma->alterar();

        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');
        foreach ($arFiltro as $stCampo => $stValor) {
            $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
        }
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?".$stFiltro,"Programa: ".$obROrcamentoPrograma->getCodPrograma(),"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "excluir":
        $obROrcamentoPrograma->setCodPrograma( $_GET['inCodPrograma'] );
        $obROrcamentoPrograma->setExercicio  ( $_GET['stExercicio']   );
        $obErro = $obROrcamentoPrograma->excluir();

        $stFiltro = "";
        $arFiltro = Sessao::read('filtro');
        foreach ($arFiltro as $stCampo => $stValor) {
            $stFiltro .= $stCampo."=".urlencode( $stValor )."&";
        }
        $stFiltro .= "pg=".Sessao::read('pg')."&";
        $stFiltro .= "pos=".Sessao::read('pos')."&";
        $stFiltro .= "stAcao=".$_REQUEST['stAcao'];

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir&".$stFiltro,"Programa: ".$_GET['inCodPrograma']."/".$_GET['stExercicio'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList."?stAcao=excluir&".$stFiltro,urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;

}
?>
