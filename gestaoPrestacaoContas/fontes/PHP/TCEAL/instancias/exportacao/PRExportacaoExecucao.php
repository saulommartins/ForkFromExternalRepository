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
    * Página de Filtro para exportação TCEAL - Arquivos Execução
    * Data de Criação   : 17/09/2013

    * @author Analista: Eduardo Schitz
    * @author Desenvolvedor: Carolina Schwaab Marçal
    
    $Id: PRExportacaoExecucao.php 65338 2016-05-12 21:26:51Z michel $

    * @ignore

    * Casos de uso: uc-06.01.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEAL_NEGOCIO."RExportacaoExecucao.class.php";

$stPrograma = "ExportacaoExecucao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

SistemaLegado::BloqueiaFrames();

$inBimestre  = $request->get('bimestre');
$stEntidades = $request->get('inCodEntidade');

$obTExportacaoExecucao = new TExportacaoExecucao();

if ($inBimestre > 0 && $inBimestre < 7) {
    SistemaLegado::periodoInicialFinalBimestre($dtInicial, $dtFinal, $inBimestre,Sessao::getExercicio( ));
} else {
    $obTExportacaoExecucao->setDado('stExercicio', Sessao::getExercicio());
    $obTExportacaoExecucao->setDado('inBimestre',$inBimestre);

    $dtInicial = '01/01/'.Sessao::getExercicio();
    $dtFinal   = '31/12/'.Sessao::getExercicio();
}

$obRExportacaoExecucao = new RExportacaoExecucao;
$obRExportacaoExecucao->setBimestre($inBimestre);
$obRExportacaoExecucao->setEntidades($stEntidades);

$arArquivos = $request->get('arArquivos');
foreach ($arArquivos as $stArquivo) {

    $stVersao="1.0";
    include $stArquivo.'.inc.php';

    $obRExportacaoExecucao->geraDocumentoXML($arResult, $stNomeArquivo, $stVersao);

    unset($obTMapeamento, $stNomeArquivo, $idCount);
    $arResult = null;
}

if (count($arArquivos) > 1) {
    $obRExportacaoExecucao->doZipArquivos();
}

SistemaLegado::mudaFramePrincipal($pgList);
SistemaLegado::LiberaFrames();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
