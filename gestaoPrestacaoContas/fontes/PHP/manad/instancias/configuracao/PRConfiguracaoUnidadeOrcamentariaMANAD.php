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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
//include_once(CAM_GPC_MANAD_MAPEAMENTO."TConfiguracaoUnidadeOrcamentariaMANAD.class.php");
//include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php");

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_EXP_NEGOCIO."RExportacaoMANADArqUniOrcam.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = 'ConfiguracaoUnidadeOrcamentariaMANAD';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

$obRExportacaoMANADArqUniOrcam = new RExportacaoMANADArqUniOrcam;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stAcao = 'incluir';
//echo $stAcao;
foreach ($_POST as $key=>$value) {
    if (strstr($key,"inIdentificador_")) {
        $arIdentificador = explode('_',$key);
        $inNumCGM = 'inNumCGM_'.$arIdentificador[3];
        //if (trim($$inNumCGM) <> "") {
        $obRExportacaoMANADArqUniOrcam->addUniOrcam();
        $obRExportacaoMANADArqUniOrcam->roUltimaUniOrcam->setIdentificador($value);
        $obRExportacaoMANADArqUniOrcam->roUltimaUniOrcam->setNumeroUnidade($arIdentificador[2]);
        $obRExportacaoMANADArqUniOrcam->roUltimaUniOrcam->setExercicio(Sessao::getExercicio());
        $obRExportacaoMANADArqUniOrcam->roUltimaUniOrcam->obRCGMPessoaJuridica->setNumCGM( $_POST[$inNumCGM] );
        $obRExportacaoMANADArqUniOrcam->roUltimaUniOrcam->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arIdentificador[1]);
        //}
    } elseif (strstr($key,"inIdentificadorConversao_")) {
        $arIdentificador = explode('_',$key);
        $inNumCGM = 'inNumCGMConversao_'.$arIdentificador[4];
        //if (trim($$inNumCGM) <> "") {
        $obRExportacaoMANADArqUniOrcam->addUniOrcam();
        $obRExportacaoMANADArqUniOrcam->roUltimaUniOrcam->setIdentificador($value);
        $obRExportacaoMANADArqUniOrcam->roUltimaUniOrcam->setNumeroUnidade($arIdentificador[2]);
        $obRExportacaoMANADArqUniOrcam->roUltimaUniOrcam->setExercicio($arIdentificador[3]);
        $obRExportacaoMANADArqUniOrcam->roUltimaUniOrcam->obRCGMPessoaJuridica->setNumCGM( $_POST[$inNumCGM] );
        $obRExportacaoMANADArqUniOrcam->roUltimaUniOrcam->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arIdentificador[1]);
        //}
    }
}
$obErro = $obRExportacaoMANADArqUniOrcam->salvar();
if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Unidade Orçamentária incluídos/alterados ", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

SistemaLegado::LiberaFrames();

?>
