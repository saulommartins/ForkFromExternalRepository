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
    * Página de Processo do Relatorio de Protocolo de Entrega
    * Data de Criação : 04/04/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30860 $
    $Name$
    $Autor: $
    $Date: 2008-03-10 14:03:21 -0300 (Seg, 10 Mar 2008) $

    * Casos de uso: uc-04.04.47
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioProtocoloEntrega";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCodigos = "";
switch ($_POST["stTipoFiltro"]) {
    case "contrato":
    case "cgm_contrato":
        foreach (Sessao::read('arContratos') as $array ) {
            $stCodigos .=  $array['cod_contrato'].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        break;
    case "lotacao_grupo";
        $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
        break;
    case "local_grupo":
        $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
        break;
}

if ($_POST["inSequencia"]) {
    $inSequencia = $_POST["inSequencia"];
    $boSequencia = "true";
} else {
    $inSequencia = 0;
    $boSequencia = "false";
}
$stValores = "";
$boArray = "false";
if ($_POST['inCodAtributo']!="") {
    $stNomeCampo = "Atributo_".$_REQUEST["inCodAtributo"]."_".$_REQUEST["inCodCadastro"];
    if (is_array($_REQUEST[$stNomeCampo."_Selecionados"])) {
        $stValores = implode(",",$_POST[$stNomeCampo."_Selecionados"]);
        $boArray = "true";
    } else {
        $stValores = $_POST[$stNomeCampo];
    }
}

$preview = new PreviewBirt(4,22,3);
$preview->addParametro ('entidade', Sessao::getCodEntidade($boTransacao));
$preview->setVersaoBirt('2.5.0');
$preview->setReturnURL( CAM_GRH_PES_INSTANCIAS."relatorio/FLRelatorioProtocoloEntrega.php");
$preview->setTitulo('Relatório de Protocolo de Entrega');
$preview->setNomeArquivo('protocoloentrega');
$preview->setExportaExcel(true);
$preview->addParametro("stDescAtributo", $_REQUEST["stDescCadastro"]);
$preview->addParametro("stEntidade", Sessao::getEntidade());
$preview->addParametro("stTipoFiltro",$_POST["stTipoFiltro"]);
$preview->addParametro( "stCodigos",$stCodigos);
$preview->addParametro("boAgrupar",$_POST["boAgrupar"]);
$preview->addParametro("boQuebrar",$_POST["boQuebrar"]);
$preview->addParametro("inSequencia", $inSequencia);
$preview->addParametro("boSequencia",$boSequencia);
$preview->addParametro("txtComplementoTitulo", $_REQUEST["txtComplementoTitulo"]);
$preview->addParametro("inCodAtributo",$_POST["inCodAtributo"]);
$preview->addParametro("stValores",$stValores);
$preview->addParametro("boArray",$boArray);
$preview->preview();
?>
