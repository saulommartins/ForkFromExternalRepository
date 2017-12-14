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
 * @author Desenvolvedor: Lisiane Morais

 * @ignore

 * $Id: PRManterConfiguracaoUnidadeOrcamentaria.php 59612 2014-09-02 12:00:51Z gelson $
 * $Name: $
 * $Revision:  $
 * $Author:  $
 * $Date:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_EXP_NEGOCIO."RExportacaoTCEPBArqUniOrcam.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoUnidadeOrcamentaria";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obRExportacaoTCEPBArqUniOrcam = new RExportacaoTCEPBArqUniOrcam;

$stAcao = $request->get("stAcao");
$stAcao = 'incluir';

for($i=1;$i<($_REQUEST['hidden']+1);$i++){
    $aux="inNaturezaJuridica_".$i;
    $arNaturezaJuridica[$aux]=$_POST[$aux];
}

foreach ($_POST as $key => $value) {
    if (strstr($key,"inNumCGM_") ) {
        if (strpos($key,"Hdn") === false){            
            $arNumCGM = explode('_',$key);
            
            $inNaturezaJuridica = 'inNaturezaJuridica_'.$arNumCGM[3];
            $inNaturezaJuridica = $arNaturezaJuridica[$inNaturezaJuridica];
            
            $obRExportacaoTCEPBArqUniOrcam->addUniOrcam();
            $obRExportacaoTCEPBArqUniOrcam->roUltimaUniOrcam->setNumeroUnidade($arNumCGM[2]);
            $obRExportacaoTCEPBArqUniOrcam->roUltimaUniOrcam->setExercicio(Sessao::getExercicio());
            $obRExportacaoTCEPBArqUniOrcam->roUltimaUniOrcam->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arNumCGM[1]);
            $obRExportacaoTCEPBArqUniOrcam->roUltimaUniOrcam->setOrdenador($value);
            $obRExportacaoTCEPBArqUniOrcam->roUltimaUniOrcam->setNaturezaJuridica($inNaturezaJuridica);
        }
    }
}

$obErro = $obRExportacaoTCEPBArqUniOrcam->salvar();

if (!$obErro->ocorreu()) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Unidade Orçamentária incluídos/alterados ", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

SistemaLegado::LiberaFrames();

?>
