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
    * Página de Processamento - Parâmetros do Arquivo UNIORCAM.
    * Data de Criação   : 11/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.05
*/

/*
$Log$
Revision 1.5  2006/07/05 20:46:25  cleisson
Adicionada tag Log aos arquivos

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include( CAM_GF_EXP_NEGOCIO."RExportacaoTCERSArqUniOrcam.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterUniOrcam";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRExportacaoTCERSArqUniOrcam = new RExportacaoTCERSArqUniOrcam;

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$stAcao = 'incluir';
//echo $stAcao;
foreach ($_POST as $key=>$value) {
    if (strstr($key,"inIdentificador_")) {
        $arIdentificador = explode('_',$key);
        $inNumCGM = 'inNumCGM_'.$arIdentificador[3];
        //if (trim($$inNumCGM) <> "") {
        $obRExportacaoTCERSArqUniOrcam->addUniOrcam();
        $obRExportacaoTCERSArqUniOrcam->roUltimaUniOrcam->setIdentificador($value);
        $obRExportacaoTCERSArqUniOrcam->roUltimaUniOrcam->setNumeroUnidade($arIdentificador[2]);
        $obRExportacaoTCERSArqUniOrcam->roUltimaUniOrcam->setExercicio(Sessao::getExercicio());
        $obRExportacaoTCERSArqUniOrcam->roUltimaUniOrcam->obRCGMPessoaJuridica->setNumCGM( $$inNumCGM );
        $obRExportacaoTCERSArqUniOrcam->roUltimaUniOrcam->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arIdentificador[1]);
        //}
    } elseif (strstr($key,"inIdentificadorConversao_")) {
        $arIdentificador = explode('_',$key);
        $inNumCGM = 'inNumCGMConversao_'.$arIdentificador[4];
        //if (trim($$inNumCGM) <> "") {
        $obRExportacaoTCERSArqUniOrcam->addUniOrcam();
        $obRExportacaoTCERSArqUniOrcam->roUltimaUniOrcam->setIdentificador($value);
        $obRExportacaoTCERSArqUniOrcam->roUltimaUniOrcam->setNumeroUnidade($arIdentificador[2]);
        $obRExportacaoTCERSArqUniOrcam->roUltimaUniOrcam->setExercicio($arIdentificador[3]);
        $obRExportacaoTCERSArqUniOrcam->roUltimaUniOrcam->obRCGMPessoaJuridica->setNumCGM( $$inNumCGM );
        $obRExportacaoTCERSArqUniOrcam->roUltimaUniOrcam->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arIdentificador[1]);
        //}
    }
}
$obErro = $obRExportacaoTCERSArqUniOrcam->salvar();
if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Unidade Orçamentária incluídos/alterados ", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

SistemaLegado::LiberaFrames();

?>
