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
    * Página de Formulario de Filtro do Relatório de Pagamentos Orçamentários
    * Data de Criação   : 31/07/2007

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: tonismar $
    $Date: 2007-08-30 16:18:25 -0300 (Qui, 30 Ago 2007) $

    * Casos de uso: uc-02.04.35
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';

// verificando se o ano da periodicidade pertence ao exercicio corrente
list($dia, $mes, $ano ) = explode('/',$_REQUEST['stDataInicial']);

if ( $ano == Sessao::getExercicio() ) {

    $preview = new PreviewBirt(2,30,3);
    $preview->setTitulo('Relatório do Birt');

    $preview->setNomeArquivo('relacaoPagamentosOrcamentarios');

    // filtro de periodicidade
    if (( $_REQUEST['stDataInicial'] ) && ( $_REQUEST['stDataFinal'] )) {
        $preview->addParametro( 'data_emissao', "and ordem_pagamento.dt_emissao between to_date('".$_REQUEST['stDataInicial']."', 'dd-mm-yyyy') and to_date('".$_REQUEST['stDataFinal']."', 'dd-mm-yyyy')" );
        $preview->addParametro( 'f_data_emissao', $_REQUEST['stDataInicial'].' até '.$_REQUEST['stDataFinal'] );
    } elseif ($_REQUEST['stDataInicial']) {
        $preview->addParametro( 'data_emissao', " and ordem_pagamento.dt_emissao = to_date('".$_REQUEST['stDataInicial']."', 'dd-mm-yyyy')" );
        $preview->addParametro( 'f_data_emissao', 'A partir de '.$_REQUEST['stDataInicial'] );
    } elseif ($_REQUEST['stDataFinal']) {
        $preview->addParametro( 'data_emissao', " and ordem_pagamento.dt_emissao = to_date('".$_REQUEST['stDataFinal']."', 'dd-mm-yyyy')" );
        $preview->addParametro( 'f_data_emissao', 'Até '.$_REQUEST['stDataFinal'] );
    }

    // filtro de fornecedor
    if ($_REQUEST['inCGM']) {
        $preview->addParametro( 'num_cgm', 'and pre_empenho.cgm_beneficiario = '.$_REQUEST['inCGM'].' ');
        $preview->addParametro( 'f_num_cgm' , $_REQUEST['inCGM'].' - '.$_REQUEST['stNomCGM'] );
    } else {
        $preview->addParametro( 'num_cgm', '' );
        $preview->addParametro( 'f_num_cgm', '' );
    }

    // filtro de empenho
    if (( $_REQUEST['inCodEmpenhoInicial'] ) && ( $_REQUEST['inCodEmpenhoFinal'] )) {
        $preview->addParametro( 'empenho', ' and empenho.cod_empenho between '.$_REQUEST['inCodEmpenhoInicial'].' and '.$_REQUEST['inCodEmpenhoFinal'].' ');
        $preview->addParametro( 'f_empenho', $_REQUEST['inCodEmpenhoInicial'].' até '.$_REQUEST['inCodEmpenhoFinal'] );
    } elseif ($_REQUEST['inCodEmpenhoInicial']) {
        $preview->addParametro( 'empenho', ' and empenho.cod_empenho = '.$_REQUEST['inCodEmpenhoInicial'].' ');
        $preview->addParametro( 'f_empenho', 'A partir de '.$_REQUEST['inCodEmpenhoInicial'] );
    } elseif ($_REQUEST['inCodEmpenhoFinal']) {
        $preview->addParametro( 'empenho', ' and empenho.cod_empenho = '.$_REQUEST['inCodEmpenhoFinal'].' ');
        $preview->addParametro( 'f_empenho', 'Até '.$_REQUEST['inCodEmpenhoFinal'] );
    } else {
        $preview->addParametro( 'empenho', '' );
        $preview->addParametro( 'f_empenho', '' );
    }

    // filtro de despesa
    if (( $_REQUEST['inCodDotacaoInicial'] ) && ( $_REQUEST['inCodDotacaoFinal'] )) {
        $preview->addParametro( 'despesa', ' and despesa.cod_despesa between '.$_REQUEST['inCodDotacaoInicial'].' and '.$_REQUEST['inCodDotacaoFinal'].' ');
        $preview->addParametro( 'f_despesa', $_REQUEST['inCodDotacaoInicial'].' até '.$_REQUEST['inCodDotacaoFinal'] );
    } elseif ($_REQUEST['inCodDotacaoInicial']) {
        $preview->addParametro( 'despesa', ' and despesa.cod_despesa = '.$_REQUEST['inCodDotacaoInicial'].' ');
        $preview->addParametro( 'f_despesa', 'A partir de '.$_REQUEST['inCodDotacaoInicial'] );
    } elseif ($_REQUEST['inCodDotacaoFinal']) {
        $preview->addParametro( 'despesa', ' and despesa.cod_despesa = '.$_REQUEST['inCodDotacaoFinal'].' ');
        $preview->addParametro( 'f_despesa', 'Até '.$_REQUEST['inCodDotacaoFinal'] );
    } else {
        $preview->addParametro( 'despesa', '' );
        $preview->addParametro( 'f_despesa', '' );
    }

    $preview->preview();
} else {
    SistemaLegado::alertaAviso('FLRelacaoPagamentosOrcamentarios.php', 'Erro ao emitir Relação Pagamentos Orçamentários! (Exercício selecionado na <b><i>periodicidade</i></b> não pertence ao exercício corrente.)', '', 'erro', Sessao::getId(), '../' );
}
?>
