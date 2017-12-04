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
    * Página de Processamento de manutenção de datas
    * Data de Criação   : 07/06/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Cleisson Barboza
    * @author Desenvolvedor: Diego Victoria

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.03.16
*/

/*
$Log$
Revision 1.4  2006/07/05 20:48:49  cleisson
Adicionada tag Log aos arquivos

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenhoAutorizacao.class.php" );
include( CAM_GF_EMP_NEGOCIO."REmpenhoManutencaoDatas.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManutencaoDatas";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRegra = new REmpenhoManutencaoDatas();

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

    $obErro = $obRegra->salvar();
    if ( !$obErro->ocorreu() ) {
        //SistemaLegado::alertaAviso($pgList.'?'.Sessao::getId()."&stAcao=".$stAcao.$stFiltro, $_REQUEST['inCodEmpenho']."/".$_REQUEST['stExercicio'] , "incluir", "aviso", Sessao::getId(), "../");
        echo "<script>";
        echo "window.parent.location = '$pgList?".Sessao::getId()."&stAcao=$stAcao$stFiltro".$_REQUEST['inCodEmpenho']."/".$_REQUEST['stExercicio']."';";
        echo "</script>";
    } else {
        //SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        echo "<script>";
        echo "sPag='../../../includes/mensagem.php?".Sessao::getId()."&tipo=unica&chamada=erro&obj=Erro ao alterar Datas do Empenho ".$_REQUEST['inCodEmpenho']."/".$_REQUEST['stExercicio']." - ".urlencode($obErro->getDescricao())."';";
        echo "window.parent.frames['telaMensagem'].location.replace(sPag);";
        //echo "window.parent.location = '$pgList?".Sessao::getId()."&stAcao=$stAcao$stFiltro".$_REQUEST['inCodEmpenho']."/".$_REQUEST['stExercicio']."';";
        echo "</script>";
    }
    break;
?>
