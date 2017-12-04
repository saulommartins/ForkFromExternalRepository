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

    * Página de Filtro para Relatório de Etiquetas
    * Data de Criação   : 15/10/2007

    * @author Analista: Lucas
    * @author Desenvolvedor: Rodrigo Soares Rodrigues

    * @ignore

    * Casos de uso : uc-01.06.98

    $Id: etiquetas.php 62581 2015-05-21 14:05:03Z michel $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

if ($_POST['stFormatoEtiqueta'] == 'A4') {
    $preview = new PreviewBirt(1,5,1);
} else {
    $preview = new PreviewBirt(1,5,2);
}

$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo ( 'Etiquetas de processos' );

if (($_REQUEST['codProcesso'] != "") && ($_REQUEST['anoExercicio'] != "")) {
    $preview->addParametro( 'prmProcesso', $_REQUEST['codProcesso'] );
    $preview->addParametro( 'prmAnoExercicio', $_REQUEST['anoExercicio'] );
} else {
    $preview->addParametro( 'prmProcesso', '' );
    $preview->addParametro( 'prmAnoExercicio', '' );
}

if (($_REQUEST['codProcessoInicial'] != "") && ($_REQUEST['codProcessoFinal'] == 0)) {
    $arProcessoInicial = explode("/", $_REQUEST['codProcessoInicial']);
    $arCodProcessoInicial = $arProcessoInicial[0];
    $arAnoExercicioInicial = $arProcessoInicial[1];
    $preview->addParametro( 'prmProcessoInicial', $arCodProcessoInicial );
    $preview->addParametro( 'prmExeInicial', $arAnoExercicioInicial);
} else {
    $preview->addParametro( 'prmProcessoInicial', '' );
    $preview->addParametro( 'prmExeInicial', '' );
}

if (($_REQUEST['codProcessoInicial'] == 0) && ($_REQUEST['codProcessoFinal'] != "")) {
    $arProcessoFinal = explode("/", $_REQUEST['codProcessoFinal']);
    $arCodProcessoFinal = $arProcessoFinal[0];
    $arAnoExercicioFinal = $arProcessoFinal[1];
    $preview->addParametro( 'prmProcessoFinal', $arCodProcessoFinal );
    $preview->addParametro( 'prmExeFinal', $arAnoExercicioFinal);
} else {
    $preview->addParametro( 'prmProcessoFinal', '' );
    $preview->addParametro( 'prmExeFinal', '' );
}

if (($_REQUEST['codProcessoInicial'] != "") && ($_REQUEST['codProcessoFinal'] != "")) {
    $arProcessoInicial = explode("/", $_REQUEST['codProcessoInicial']);
    $arProcessoFinal = explode("/", $_REQUEST['codProcessoFinal']);
    $arCodProcessoInicial = $arProcessoInicial[0];
    $arAnoExercicioInicial = $arProcessoInicial[1];
    $arCodProcessoFinal = $arProcessoFinal[0];
    $arAnoExercicioFinal = $arProcessoFinal[1];
    $preview->addParametro( 'prmProcessoIni', $arCodProcessoInicial );
    $preview->addParametro( 'prmProcessoFim', $arCodProcessoFinal );
    $preview->addParametro( 'prmExercicioInicial', $arAnoExercicioInicial);
    $preview->addParametro( 'prmExercicioFinal', $arAnoExercicioFinal);
} else {
    $preview->addParametro( 'prmProcessoIni', '' );
    $preview->addParametro( 'prmExercicioInicial', '' );
    $preview->addParametro( 'prmProcessoFim', '' );
    $preview->addParametro( 'prmExercicioFinal', '' );
}

if ($_REQUEST['resumo'] != "") {
    $preview->addParametro( 'prmResumo', $_REQUEST['resumo'] );
} else {
    $preview->addParametro( 'prmResumo', '' );
}

if ($_REQUEST['codClassificacao'] != "xxx" && $_REQUEST['codClassificacao'] != "") {
    $preview->addParametro( 'prmCodClassificacao', $_REQUEST['codClassificacao'] );
} else {
    $preview->addParametro( 'prmCodClassificacao', '' );
}

if ($_REQUEST['codAssunto'] != "xxx" && $_REQUEST['codAssunto'] != "") {
    $preview->addParametro( 'prmCodAssunto', $_REQUEST['codAssunto'] );
} else {
    $preview->addParametro( 'prmCodAssunto', '' );
}

if ($_REQUEST['codMasSetor'] != "") {
    $arSetor = explode(".", $_REQUEST['codMasSetor']);
    $arSetorOrg = $arSetor[0];
    $arSetorUni = $arSetor[1];
    $arSetorDep = $arSetor[2];
    $arSetorSetorAno = $arSetor[3];
    $arSetor1 = explode("/", $arSetorSetorAno);
    $arSetorSetor = $arSetor1[0];
    $arSetorAno = $arSetor1[1];

    $preview->addParametro( 'prmCodOrgao', $arSetorOrg );
    $preview->addParametro( 'prmCodUnidade', $arSetorUni );
    $preview->addParametro( 'prmCodDepartamento', $arSetorDep );
    $preview->addParametro( 'prmCodSetor', $arSetorSetor);
    $preview->addParametro( 'prmAnoExercicioSetor', $arSetorAno);
} else {
    $preview->addParametro( 'prmCodOrgao', '' );
    $preview->addParametro( 'prmCodUnidade', '' );
    $preview->addParametro( 'prmCodDepartamento', '' );
    $preview->addParametro( 'prmCodSetor', '');
    $preview->addParametro( 'prmAnoExercicioSetor', '');
}

if (strlen($_REQUEST['numCgm']) > 0) {
    $preview->addParametro( 'prmNumCgm', $_REQUEST['numCgm']);
} else {
    $preview->addParametro( 'prmNumCgm', '');
}

if ((strlen($_REQUEST['dataInicial']) > 0) && (strlen($_REQUEST['dataFinal']) == 0)) {
    $arrData = explode("/",$_REQUEST['dataInicial']);
    $dtInicial = $arrData[2]."-".$arrData[1]."-".$arrData[0];
    $dtFinal = $arrData[2]."-".$arrData[1]."-".$arrData[0]." 23:59:59.999";
    $preview->addParametro( 'prmDtIni', $dtInicial);
    $preview->addParametro( 'prmDtFim', $dtFinal);
} else {
    $preview->addParametro( 'prmDtIni', '');
    $preview->addParametro( 'prmDtFim', '');
}

if ((strlen($_REQUEST['dataInicial']) == 0) && (strlen($_REQUEST['dataFinal']) > 0)) {
    $arrData = explode("/",$_REQUEST['dataFinal']);
    $dtInicial = $arrData[2]."-".$arrData[1]."-".$arrData[0];
    $dtFinal = $arrData[2]."-".$arrData[1]."-".$arrData[0]." 23:59:59.999";
    $preview->addParametro( 'prmDtInicial', $dtInicial);
    $preview->addParametro( 'prmDtFinal', $dtFinal);
} else {
    $preview->addParametro( 'prmDtInicial', '');
    $preview->addParametro( 'prmDtFinal', '');
}

if ((strlen($_REQUEST['dataInicial']) > 0) && (strlen($_REQUEST['dataFinal']) > 0)) {
    $arrData = explode("/",$_REQUEST['dataInicial']);
    $dtInicial = $arrData[2]."-".$arrData[1]."-".$arrData[0];
    $arrData1 = explode("/",$_REQUEST['dataFinal']);
    $dtFinal = $arrData1[2]."-".$arrData1[1]."-".$arrData1[0]." 23:59:59.999";
    $preview->addParametro( 'prmDataIni', $dtInicial);
    $preview->addParametro( 'prmDataFim', $dtFinal);
} else {
    $preview->addParametro( 'prmDataIni', '');
    $preview->addParametro( 'prmDataFim', '');
}

$preview->addParametro ('centroCusto', SistemaLegado::pegaConfiguracao("centro_custo", 5));

$preview->addAssinaturas(Sessao::read('assinaturas'));

$preview->preview();
