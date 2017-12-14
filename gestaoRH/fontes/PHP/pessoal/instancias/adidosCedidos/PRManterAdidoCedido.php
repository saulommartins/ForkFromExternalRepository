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
    * Página de Processamento do Acidos Cedidos
    * Data de Criação: 27/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30860 $
    $Name$
    $Author: souzadl $
    $Date: 2006-09-28 11:23:41 -0300 (Qui, 28 Set 2006) $

    * Casos de uso: uc-04.04.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAdidoCedido.class.php"                                   );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAdidoCedidoLocal.class.php"                              );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAdidoCedidoExcluido.class.php"                           );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                                      );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php"                                              );

$arLink = Sessao::read('link');
$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterAdidoCedido";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgJS   = "JS".$stPrograma.".js";

$obTPessoalAdidoCedido = new TPessoalAdidoCedido();
$obTPessoalAdidoCedidoLocal = new TPessoalAdidoCedidoLocal();
$obTPessoalAdidoCedidoExcluido = new TPessoalAdidoCedidoExcluido();
$obTPessoalAdidoCedidoLocal->obTPessoalAdidoCedido = &$obTPessoalAdidoCedido;
$obTPessoalContrato = new TPessoalContrato();
$obTNorma = new TNorma();

switch ($stAcao) {
    case "incluir":
        Sessao::setTrataExcecao(true);
        $stFiltro = " WHERE registro = ".$_POST['inContrato'];
        $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);

        $arNorma = explode("/",$_POST['stNrNormaTxt']);
        $inNumNorma = $arNorma[0];
        $inExercicio= $arNorma[1];
        $stFiltro  = " WHERE cod_tipo_norma        = ".$_POST['inCodTipoNormaTxt'];
        $stFiltro .= "   AND lpad(num_norma,6,'0') = '".$inNumNorma."'";
        $stFiltro .= "   AND exercicio             = '".$inExercicio."'";
        $obTNorma->recuperaNormas($rsNorma, $stFiltro);

        $stTipoCedencia = ( $_POST['stTipoCedencia'] == "adido" ) ? "a" : "c";
        $stIndicativoOnus = ( $_POST['stIndicativoOnus'] == "cedente" ) ? "c" : "e";

        $obTPessoalAdidoCedido->setDado("cod_contrato"          ,$rsContrato->getCampo("cod_contrato"));
        $obTPessoalAdidoCedido->setDado("cod_norma"             ,$rsNorma->getCampo("cod_norma"));
        $obTPessoalAdidoCedido->setDado("cgm_cedente_cessionario",$_POST['inCGM']);
        $obTPessoalAdidoCedido->setDado("dt_inicial"            ,$_POST['dtDataInicialAto']);
        $obTPessoalAdidoCedido->setDado("dt_final"              ,$_POST['dtDataFinalAto']);
        $obTPessoalAdidoCedido->setDado("tipo_cedencia"         ,$stTipoCedencia);
        $obTPessoalAdidoCedido->setDado("indicativo_onus"       ,$stIndicativoOnus);
        $obTPessoalAdidoCedido->setDado("num_convenio"          ,$_POST['inCodConvenioTxt']);
        $obTPessoalAdidoCedido->inclusao();
        if ($_POST['inCodLocal'] != "") {
            $obTPessoalAdidoCedidoLocal->setDado("cod_local",$_POST['inCodLocal']);
            $obTPessoalAdidoCedidoLocal->inclusao();
        }
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgForm,"Inclusão da movimentação para o contrato ".$_POST['inContrato'],"incluir","aviso", Sessao::getId(), "../");
    break;
    case "alterar":
        Sessao::setTrataExcecao(true);

        $arNorma = explode("/",$_POST['stNrNormaTxt']);
        $inNumNorma = $arNorma[0];
        $inExercicio= $arNorma[1];
        $stFiltro  = " WHERE cod_tipo_norma        = ".$_POST['inCodTipoNormaTxt'];
        $stFiltro .= "   AND lpad(num_norma,6,'0') = '".$inNumNorma."'";
        $stFiltro .= "   AND exercicio             = '".$inExercicio."'";
        $obTNorma->recuperaNormas($rsNorma, $stFiltro);

        $stTipoCedencia = ( $_POST['stTipoCedencia'] == "adido" ) ? "a" : "c";
        $stIndicativoOnus = ( $_POST['stIndicativoOnus'] == "cedente" ) ? "c" : "e";

        $obTPessoalAdidoCedido->setDado("cod_contrato"          ,$_POST['inCodContrato']);
        $obTPessoalAdidoCedido->setDado("cod_norma"             ,$rsNorma->getCampo("cod_norma"));
        $obTPessoalAdidoCedido->setDado("cgm_cedente_cessionario",$_POST['inCGMOrgaoEntidade']);
        $obTPessoalAdidoCedido->setDado("dt_inicial"            ,$_POST['dtDataInicialAto']);
        $obTPessoalAdidoCedido->setDado("dt_final"              ,$_POST['dtDataFinalAto']);
        $obTPessoalAdidoCedido->setDado("tipo_cedencia"         ,$stTipoCedencia);
        $obTPessoalAdidoCedido->setDado("indicativo_onus"       ,$stIndicativoOnus);
        $obTPessoalAdidoCedido->setDado("num_convenio"          ,$_POST['inCodConvenioTxt']);
        $obTPessoalAdidoCedido->inclusao();
        if ($_POST['inCodLocal'] != "") {
            $obTPessoalAdidoCedidoLocal->setDado("cod_local",$_POST['inCodLocal']);
            $obTPessoalAdidoCedidoLocal->inclusao();
        }
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList."&inContrato=".$_POST['inRegistro'],"Alteração da movimentação para o contrato ".$_POST['inRegistro'],"alterar","aviso", Sessao::getId(), "../");
    break;
    case "excluir":
        Sessao::setTrataExcecao(true);
        $obTPessoalAdidoCedidoExcluido->setDado("cod_contrato"          ,$_GET['inCodContrato']);
        $obTPessoalAdidoCedidoExcluido->setDado("cod_norma"             ,$_GET['inCodNorma']);
        $obTPessoalAdidoCedidoExcluido->setDado("timestamp_cedido_adido"      ,$_GET['stTimestamp']);
        $obTPessoalAdidoCedidoExcluido->inclusao();
        Sessao::encerraExcecao();
        sistemaLegado::alertaAviso($pgList,"Exclusão da movimentação para o contrato ".$_POST['inRegistro']."!" ,"excluir","aviso", Sessao::getId(), "../");
    break;
}

?>
