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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 08/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GPC_TCEAL_NEGOCIO."RExportacaoTCEALArqUniOrcam.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoUnidadeOrcamentaria";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRExportacaoTCEALArqUniOrcam = new RExportacaoTCEALArqUniOrcam;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stAcao = 'incluir';
//echo $stAcao;
foreach ($_POST as $key=>$value) {
    if (strstr($key,"inIdentificador_")) {
        $arIdentificador = explode('_',$key);
        $inNumCGM = 'inNumCGM_'.$arIdentificador[3];
        //if (trim($$inNumCGM) <> "") {
        $obRExportacaoTCEALArqUniOrcam->addUniOrcam();
        $obRExportacaoTCEALArqUniOrcam->roUltimaUniOrcam->setIdentificador($value);
        $obRExportacaoTCEALArqUniOrcam->roUltimaUniOrcam->setNumeroUnidade($arIdentificador[2]);
        $obRExportacaoTCEALArqUniOrcam->roUltimaUniOrcam->setExercicio(Sessao::getExercicio());
        $obRExportacaoTCEALArqUniOrcam->roUltimaUniOrcam->obRCGMPessoaJuridica->setNumCGM( $_POST[$inNumCGM] );
        $obRExportacaoTCEALArqUniOrcam->roUltimaUniOrcam->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arIdentificador[1]);
        //}
    } elseif (strstr($key,"inIdentificadorConversao_")) {
        $arIdentificador = explode('_',$key);
        $inNumCGM = 'inNumCGMConversao_'.$arIdentificador[4];
        //if (trim($$inNumCGM) <> "") {
        $obRExportacaoTCEALArqUniOrcam->addUniOrcam();
        $obRExportacaoTCEALArqUniOrcam->roUltimaUniOrcam->setIdentificador($value);
        $obRExportacaoTCEALArqUniOrcam->roUltimaUniOrcam->setNumeroUnidade($arIdentificador[2]);
        $obRExportacaoTCEALArqUniOrcam->roUltimaUniOrcam->setExercicio($arIdentificador[3]);
        $obRExportacaoTCEALArqUniOrcam->roUltimaUniOrcam->obRCGMPessoaJuridica->setNumCGM( $_POST[$inNumCGM] );
        $obRExportacaoTCEALArqUniOrcam->roUltimaUniOrcam->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arIdentificador[1]);
        //}
    }
}
$obErro = $obRExportacaoTCEALArqUniOrcam->salvar();
if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Unidade Orçamentária incluídos/alterados ", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

SistemaLegado::LiberaFrames();
