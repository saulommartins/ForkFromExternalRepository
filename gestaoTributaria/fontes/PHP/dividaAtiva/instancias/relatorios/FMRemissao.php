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
    * Frame Relatorio de Remissão
    * Data de Criação: 06/10/2008

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: $

    * Casos de uso: uc-05.04.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

//Define o nome dos arquivos PHP
$stPrograma    = "Remissao";
$pgFilt        = "FL".$stPrograma.".php";

$stFiltro = '(';
//inInscIni INTEGER,stExerInscIni VARCHAR,
if ($_REQUEST["inCodInscricaoInicial"]) {
    $arCodInscricaoInicial = explode('/',$_REQUEST["inCodInscricaoInicial"]);
    $stFiltro .= (int) $arCodInscricaoInicial[0].",'".$arCodInscricaoInicial[1]."',";
} else {
    $stFiltro .= 'null,null,';
}
//inInscFim INTEGER,stExerInscFim VARCHAR,
if ($_REQUEST["inCodInscricaoFinal"]) {
    $arCodInscricaoFinal = explode('/',$_REQUEST["inCodInscricaoFinal"]);
    $stFiltro .= (int) $arCodInscricaoFinal[0].",'".$arCodInscricaoFinal[1]."',";
} else {
    $stFiltro .= 'null,null,';
}
//inInscImobIni INTEGER,inInscImobFim INTEGER,
$stFiltro .= $_REQUEST['inCodImovelInicial'] ? (int) $_REQUEST['inCodImovelInicial'].',' : 'null,';
$stFiltro .= $_REQUEST['inCodImovelFinal'] ? (int) $_REQUEST['inCodImovelFinal'].',' : 'null,';
//inInscEconIni INTEGER,inInscEconFim INTEGER,
$stFiltro .= $_REQUEST['inNumInscricaoEconomicaInicial'] ? (int) $_REQUEST['inNumInscricaoEconomicaInicial'].',' : 'null,';
$stFiltro .= $_REQUEST['inNumInscricaoEconomicaFinal'] ? (int) $_REQUEST['inNumInscricaoEconomicaFinal'].',' : 'null,';
//inNumCGMIni INTEGER,inNumCGMFim INTEGER,
$stFiltro .= $_REQUEST['inCodContribuinteInicial'] ? (int) $_REQUEST['inCodContribuinteInicial'].',' : 'null,';
$stFiltro .= $_REQUEST['inCodContribuinteFinal'] ? (int) $_REQUEST['inCodContribuinteFinal'].',' : 'null,';
//inCodNorma INTEGER,
$stFiltro .= $_REQUEST['inCodNorma'] ? (int) $_REQUEST['inCodNorma'].',' : 'null,';
//stCreditos VARCHAR
$arListaSessao = Sessao::read('arLista');
$inTotalNaListaGrupoCreditoSessao = count($arListaSessao);

if ($inTotalNaListaGrupoCreditoSessao > 0) {
    $stFiltro .= '\'';
    for ($inX=0; $inX<$inTotalNaListaGrupoCreditoSessao; $inX++) {
        if ($_REQUEST['boTipoLancamentoManual'] == 'grupo_credito') {
            $arDados = explode( "/", $arListaSessao[$inX]["stCodGrupo"] );
            $stFiltro .= $arDados[1].(int) $arDados[0].',';
        } elseif ($_REQUEST['boTipoLancamentoManual'] == 'credito') {
            $stFiltro .= $arListaSessao[$inX]['stExercicioLista'].$arListaSessao[$inX]['stCodCredito'].'-';
        }
    }

    $stFiltro = SUBSTR($stFiltro,0,-1).'\',';
} else {
    $stFiltro .= 'null,';
}

if ($_REQUEST['boTipoLancamentoManual'] == 'credito') {
    $stFiltro .= 'false,';
} elseif ($_REQUEST['boTipoLancamentoManual'] == 'grupo_credito') {
    $stFiltro .= 'true,';
}

if ($_REQUEST["stExercicioLista"]) {
    $stFiltro .= '\''.$_REQUEST["stExercicioLista"].'\'';
} else {
    $stFiltro .= 'null';
}

//remissaoAutomatica.rptdesign
$preview = new PreviewBirt( 5, 33, 3 );
$preview->setVersaoBirt('2.5.0');
$preview->setTitulo('Relatório de Remissão');
$preview->addParametro('stFiltro', urlencode($stFiltro) );
if ($_REQUEST['inCodNorma']) {
    $preview->addParametro('cod_norma', $_REQUEST['inCodNorma']);
} else {
    $preview->addParametro('cod_norma', 0);
}
$preview->preview();
?>
