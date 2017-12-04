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
 * Página de Processamento - Configuração Unidade Orçamentária.
 * Data de Criação   : 16/01/2014

 * @author Analista: Eduardo Schitz
 * @author Desenvolvedor: Franver Sarmento de Moraes

 * @ignore

 * $Id: PRManterConfiguracaoUnidadeOrcamentaria.php 60484 2014-10-23 18:43:36Z lisiane $
 * $Name: $
 * $Revision: 60484 $
 * $Author: lisiane $
 * $Date: 2014-10-23 16:43:36 -0200 (Thu, 23 Oct 2014) $
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EXP_NEGOCIO."RExportacaoTCEMGArqUniOrcam.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoUnidadeOrcamentaria";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRExportacaoTCEMGArqUniOrcam = new RExportacaoTCEMGArqUniOrcam;

$stAcao = $request->get("stAcao");
$stAcao = 'incluir';
for($i=1;$i<($_REQUEST['hidden']+1);$i++){
    $aux="inNumCGM_".$i;
    $arOrdenador[$aux]=$_REQUEST[$aux];
}

foreach ($_POST as $key => $value) {

    if (strstr($key,"inIdentificador_")) {
        $arIdentificador = explode('_',$key);
        $inNumCGM = 'inNumCGM_'.$arIdentificador[3];

        $obRExportacaoTCEMGArqUniOrcam->addUniOrcam();
        $obRExportacaoTCEMGArqUniOrcam->roUltimaUniOrcam->setIdentificador($value);
        $obRExportacaoTCEMGArqUniOrcam->roUltimaUniOrcam->setNumeroUnidade($arIdentificador[2]);
        $obRExportacaoTCEMGArqUniOrcam->roUltimaUniOrcam->setExercicio(Sessao::getExercicio());
        $obRExportacaoTCEMGArqUniOrcam->roUltimaUniOrcam->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arIdentificador[1]);
        $obRExportacaoTCEMGArqUniOrcam->roUltimaUniOrcam->setOrdenador($arOrdenador[$inNumCGM]);

    } elseif (strstr($key,"inIdentificadorConversao_")) {
        $arIdentificador = explode('_',$key);
        $inNumCGM = 'inNumCGMConversao_'.$arIdentificador[4];
        $inNumCGMConversao = $_POST['inNumCGMConversao_'.$arIdentificador[1].'_'.$arIdentificador[2].'_'.$arIdentificador[3].'_'.$arIdentificador[4]];
        $arCodOrgaoAtual = explode('-',$_REQUEST['inCodOrgao_'.$arIdentificador[4]]);
        $arCodUnidadeAtual =  explode('-',$_REQUEST['inMontaCodUnidadeM_'.$arIdentificador[4]]);

        $obRExportacaoTCEMGArqUniOrcam->addUniOrcam();
        $obRExportacaoTCEMGArqUniOrcam->roUltimaUniOrcam->setIdentificador($value);
        $obRExportacaoTCEMGArqUniOrcam->roUltimaUniOrcam->setNumeroUnidade($arIdentificador[2]);
        $obRExportacaoTCEMGArqUniOrcam->roUltimaUniOrcam->setExercicio($arIdentificador[3]);
        $obRExportacaoTCEMGArqUniOrcam->roUltimaUniOrcam->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arIdentificador[1]);
        $obRExportacaoTCEMGArqUniOrcam->roUltimaUniOrcam->setOrdenador($inNumCGMConversao);
        $obRExportacaoTCEMGArqUniOrcam->roUltimaUniOrcam->setOrgaoAtual($arCodOrgaoAtual[1]);
        $obRExportacaoTCEMGArqUniOrcam->roUltimaUniOrcam->setUnidadeAtual($arCodUnidadeAtual[1]);
        $obRExportacaoTCEMGArqUniOrcam->roUltimaUniOrcam->setExercicioAtual($arCodUnidadeAtual[2]);

    }
}

$obErro = $obRExportacaoTCEMGArqUniOrcam->salvar();

if (!$obErro->ocorreu()) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Unidade Orçamentária incluídos/alterados ", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

SistemaLegado::LiberaFrames();

?>
