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
    * Data de Criação: 10/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCGeraRelatorioUtilizacao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.02.15
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

$preview = new PreviewBirt(3,7,4);
$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('relatorioUtilizacao');

$preview->addParametro( 'exercicio', Sessao::getExercicio() );

if ($_REQUEST['inCodVeiculo'] != '') {
    $preview->addParametro( 'inCodVeiculo', $_REQUEST['inCodVeiculo'] );
} else {
    $preview->addParametro( 'inCodVeiculo', null );
}

if ($_REQUEST['inCodMarca'] != '') {
    $preview->addParametro( 'inCodMarca', $_REQUEST['inCodMarca'] );
} else {
    $preview->addParametro( 'inCodMarca', null );
}

if ($_REQUEST['inCodModelo'] != '') {
    $preview->addParametro( 'inCodModelo', $_REQUEST['inCodModelo'] );
} else {
    $preview->addParametro( 'inCodModelo', null );
}

if ($_REQUEST['stTipoVeiculo'] != '') {
    $preview->addParametro( 'inCodTipoVeiculo', $_REQUEST['slTipoVeiculo'] );
} else {
    $preview->addParametro( 'inCodTipoVeiculo', null );
}

if ($_REQUEST['inCodCombustivelSelecionados'] != '') {
    $preview->addParametro( 'inCodCombustivel', implode(',',$_REQUEST['inCodCombustivelSelecionados']) );
} else {
    $preview->addParametro( 'inCodCombustivel', null );
}

if ($_REQUEST['inCGMResponsavel'] != '') {
    $preview->addParametro( 'inCodResponsavel', $_REQUEST['inCGMResponsavel'] );
} else {
    $preview->addParametro( 'inCodResponsavel', null );
}

if ($_REQUEST['stPlaca'] != '') {
    $preview->addParametro( 'stPlaca', str_replace('-','',$_REQUEST['stPlaca']) );
} else {
    $preview->addParametro( 'stPlaca', null );
}

if ($_REQUEST['stPrefixo'] != '') {
    $preview->addParametro( 'stPrefixo', $_REQUEST['stPrefixo'] );
} else {
    $preview->addParametro( 'stPrefixo', null );
}

if ($_REQUEST['inCodVeiculo'] != '') {
    $preview->addParametro( 'inCodVeiculo', $_REQUEST['inCodVeiculo'] );
} else {
    $preview->addParametro( 'inCodVeiculo', null );
}

if ($_REQUEST['inCodOrdenacao'] != '') {
    $preview->addParametro( 'stOrdenacao', $_REQUEST['inCodOrdenacao'] );
} else {
    $preview->addParametro( 'stOrdenacao', null );
}

if ($_REQUEST['stDataInicial'] != '') {
    $preview->addParametro( 'stDataInicial', $_REQUEST['stDataInicial'] );
    $preview->addParametro( 'stDataFinal', $_REQUEST['stDataFinal'] );
} else {
    $preview->addParametro( 'stDataInicial', null );
    $preview->addParametro( 'stDataFinal', null );
}

if ($_REQUEST['inCodEntidade'] != '') {
    $preview->addParametro( 'inCodEntidade', implode(',',$_REQUEST['inCodEntidade']) );
} else {
    $preview->addParametro( 'inCodEntidade', null );
}

if ($_REQUEST['inCodOrigem'] != '') {
    $preview->addParametro( 'inCodOrigem', $_REQUEST['inCodOrigem'] );
} else {
    $preview->addParametro( 'inCodOrigem', null );
}

$preview->preview();
