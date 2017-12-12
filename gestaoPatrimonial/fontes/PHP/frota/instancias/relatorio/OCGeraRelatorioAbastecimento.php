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
    * Data de Criação: 13/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * $Id: OCGeraRelatorioAbastecimento.php 61605 2015-02-12 16:04:02Z diogo.zarpelon $

    * Casos de uso: uc-03.02.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once ( CAM_GP_FRO_MAPEAMENTO."TFrotaMarca.class.php" );
include_once ( CAM_GP_FRO_MAPEAMENTO."TFrotaModelo.class.php" );
include_once ( CAM_GP_FRO_MAPEAMENTO."TFrotaTipoVeiculo.class.php" );
include_once ( CAM_GP_FRO_MAPEAMENTO."TFrotaCombustivel.class.php" );
include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );

$preview = new PreviewBirt(3,7,6);
$preview->setVersaoBirt( '2.5.0' );
$preview->setTitulo('Relatório do Birt');

$preview->setNomeArquivo('relatorioAbastecimento');

$preview->addParametro( 'exercicio', Sessao::getExercicio() );

if ($_REQUEST['inCodVeiculo'] != '') {
    $preview->addParametro( 'inCodVeiculo', $_REQUEST['inCodVeiculo'] );
} else {
    $preview->addParametro( 'inCodVeiculo', null );
}

if ($_REQUEST['inCodMarca'] != '') {
    $preview->addParametro( 'inCodMarca', $_REQUEST['inCodMarca'] );

    //recupera a marca
    $obTFrotaMarca = new TFrotaMarca();
    $obTFrotaMarca->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
    $obTFrotaMarca->recuperaPorChave( $rsMarca );
    $preview->addParametro( 'stFiltroMarca', $rsMarca->getCampo('nom_marca') );
} else {
    $preview->addParametro( 'inCodMarca', null );
    $preview->addParametro( 'stFiltroMarca', null );
}

if ($_REQUEST['inCodModelo'] != '') {
    $preview->addParametro( 'inCodModelo', $_REQUEST['inCodModelo'] );

    //recupera o modelo
    $obTFrotaModelo = new TFrotaModelo();
    $obTFrotaModelo->setDado( 'cod_marca', $_REQUEST['inCodMarca'] );
    $obTFrotaModelo->setDado( 'cod_modelo', $_REQUEST['inCodModelo'] );
    $obTFrotaModelo->recuperaPorChave( $rsModelo );
    $preview->addParametro( 'stFiltroModelo', $rsModelo->getCampo('nom_modelo') );
} else {
    $preview->addParametro( 'inCodModelo', null );
    $preview->addParametro( 'stFiltroModelo', null );
}

if ($_REQUEST['slTipoVeiculo'] != '') {
    $preview->addParametro( 'inCodTipoVeiculo', $_REQUEST['slTipoVeiculo'] );

    //recupera o tipo do veiculo
    $obTFrotaTipoVeiculo = new TFrotaTipoVeiculo();
    $obTFrotaTipoVeiculo->setDado('cod_tipo', $_REQUEST['slTipoVeiculo'] );
    $obTFrotaTipoVeiculo->recuperaPorChave( $rsTipoVeiculo );
    $preview->addParametro( 'stFiltroTipoVeiculo', $rsTipoVeiculo->getCampo('nom_tipo') );
} else {
    $preview->addParametro( 'inCodTipoVeiculo', null );
    $preview->addParametro( 'stFiltroTipoVeiculo', null );
}

if ($_REQUEST['inCodCombustivelSelecionados'] != '') {
    $preview->addParametro( 'inCodCombustivel', implode(',',$_REQUEST['inCodCombustivelSelecionados']) );

    //recupera os combustiveis
    $obTFrotaCombustivel = new TFrotaCombustivel();
    $obTFrotaCombustivel->recuperaTodos( $rsCombustivel, ' WHERE cod_combustivel IN ('.implode(',',$_REQUEST['inCodCombustivelSelecionados']).') ' );

    while ( !$rsCombustivel->eof() ) {
        $stCombustivel .= $rsCombustivel->getCampo('nom_combustivel').', ';
        $rsCombustivel->proximo();
    }
    $preview->addParametro( 'stFiltroCombustivel', substr($stCombustivel,0,-2) );
} else {
    $preview->addParametro( 'inCodCombustivel', null );
    $preview->addParametro( 'stFiltroCombustivel', null );
}

if ($_REQUEST['inCGMResponsavel'] != '') {
    $preview->addParametro( 'inCodResponsavel', $_REQUEST['inCGMResponsavel'] );

    //recupera o responsavel
    $obTCGM = new TCGM();
    $obTCGM->setDado( 'numcgm', $_REQUEST['inCGMResponsavel'] );
    $obTCGM->recuperaPorChave( $rsResponsavel );
    $preview->addParametro( 'stFiltroResponsavel', $rsResponsavel->getCampo('nom_cgm') );
} else {
    $preview->addParametro( 'inCodResponsavel', null );
    $preview->addParametro( 'stFiltroResponsavel', null );
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
    $preview->addParametro( 'stDataInicial', '01/01/'.Sessao::getExercicio() );
    $preview->addParametro( 'stDataFinal', '31/12/'.Sessao::getExercicio() );
}

if ($_REQUEST['inCodVeiculoBaixado'] != '') {
     $preview->addParametro( 'inCodVeiculoBaixado', $_REQUEST['inCodVeiculoBaixado'] );
} else {
    $preview->addParametro( 'inCodVeiculoBaixado', null );
}

if ($_REQUEST['inCodOrigem'] != '') {
    $preview->addParametro( 'inCodOrigem', $_REQUEST['inCodOrigem'] );
} else {
    $preview->addParametro( 'inCodOrigem', null );
}

if ($_REQUEST['inCodEntidade'] != '') {
    $preview->addParametro( 'inCodEntidade', implode(',',$_REQUEST['inCodEntidade']) );
} else {
    $preview->addParametro( 'inCodEntidade', null );
}

$preview->addParametro( 'inTipoRelatorio', $_REQUEST['slTipoRelatorio'] );

$preview->preview();
