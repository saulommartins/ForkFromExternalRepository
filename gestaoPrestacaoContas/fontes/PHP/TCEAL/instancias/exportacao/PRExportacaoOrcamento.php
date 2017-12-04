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

    * Página de Filtro para exportação de ITBI/IPTU
    * Data de Criação: 23/10/2013
    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carlos Adriano Vernieri
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEAL_NEGOCIO."RExportacaoOrcamento.class.php";

$stPrograma = "ExportacaoOrcamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

SistemaLegado::BloqueiaFrames(true, false);

$inBimestre = $_REQUEST['bimestre'];
$stEntidades = $_REQUEST['inCodEntidade'];

$obTExportacaoOrcamento = new TExportacaoOrcamento();

if ($inBimestre > 0 && $inBimestre < 7) {
    $obTExportacaoOrcamento->setDado('stExercicio', Sessao::getExercicio());
    $obTExportacaoOrcamento->setDado('inBimestre', $inBimestre);
    $obTExportacaoOrcamento->buscaDatas($rsData);

    $dtInicial= explode('=', $rsData->getCampo('bimestre'));
    $dtInicial= explode(',', $dtInicial[1]);
    $dtInicial =substr($dtInicial[0], 1);

    $dtFinal = explode('=', $rsData->getCampo('bimestre'));
    $dtFinal = explode(',', $dtFinal[1]);
    $dtFinal = substr($dtFinal[1], 0, -1);

} else { 
    $dtInicial= '01/01/'.Sessao::getExercicio();
    $dtFinal= '31/12/'.Sessao::getExercicio();
}

//Essas duas datas são para quando precisar saber a data desde o início do ano até a data do bimestre escolhido, pois dtInicial e dtFinal são apenas do bimestre escolhido
$dtAnoInicial = '01/01/'.Sessao::getExercicio();
$dtAnoFinal = $dtFinal;

$obRExportacaoOrcamento = new RExportacaoOrcamento;
$obRExportacaoOrcamento->setBimestre($inBimestre);
$obRExportacaoOrcamento->setEntidades($stEntidades);

foreach ($_REQUEST['arArquivos'] as $stArquivo) {
    $stVersao="1.0";
    
    include $stArquivo.'.inc.php';
    
    $obRExportacaoOrcamento->setVersao($stVersao);    
    $obRExportacaoOrcamento->geraDocumentoXML($arResult, $stNomeArquivo);

    unset($stNomeArquivo, $idCount);
    $arResult = null;
}

if (count($_REQUEST['arArquivos']) > 1) {
    $obRExportacaoOrcamento->doZipArquivos();
}

SistemaLegado::mudaFramePrincipal($pgList);
SistemaLegado::LiberaFrames(true, false);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
