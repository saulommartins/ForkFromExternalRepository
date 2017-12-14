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
 * Página do Oculto que gera o relatório do Relatório Emitir Ordem de Pagamento
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Tonismar R. Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * $Id:$
 */

/* includes de sistema */
//include_once "../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

/* includes de classes */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php";
include_once CAM_GP_COM_MAPEAMENTO."TComprasFornecedorConta.class.php";

$arFiltro = Sessao::read('filtroRelatorio');

$preview = new PreviewBirt(2, 10, 7);
$preview->setTitulo('Emitir Ordem de Pagamento');
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);
$preview->setFormato('pdf');
$preview->setCodigoBarra(true);

$preview->addParametro('entidade', $arFiltro['inCodEntidade']);
$preview->addParametro('cod_acao_op', $arFiltro['acao']);
$preview->addParametro('acao_receita_extra', 1534);
$preview->addParametro('exercicio', Sessao::getExercicio());
$preview->addParametro('cod_ordem', $arFiltro['inCodOrdem']);

$preview->addParametro('cod_fornecedor', $arFiltro['inCodFornecedor']);

$obTContaBancaria = new TComprasFornecedorConta();
$obTContaBancaria->setDado('cgm_fornecedor', $arFiltro['inCodFornecedor']);
$stFiltro = " AND fc.padrao = true \n";
$obTContaBancaria->recuperaListaFornecedorConta($rsContaBancaria, $stFiltro);
if($rsContaBancaria->getNumLinhas()==1){
    $preview->addParametro('dados_bancario', 1);
}

$preview->addParametro('nome_funcionario', htmlentities(Sessao::read('nomCgm'), ENT_NOQUOTES, 'UTF-8'));

if (isset($arFiltro['stCodReciboExtra']) && $arFiltro['stCodReciboExtra'] != '') {
    $preview->addParametro('cod_recibo_extra', $arFiltro['stCodReciboExtra']);
} else {
    $preview->addParametro('cod_recibo_extra', '');
}

if (isset($arFiltro['stCodReceita']) && $arFiltro['stCodReceita'] != '') {
    $preview->addParametro('cod_receita', $arFiltro['stCodReceita']);
} else {
    $preview->addParametro('cod_receita', '');
}

if (isset($arFiltro['stCodLancamento']) && $arFiltro['stCodLancamento'] != '') {
    $preview->addParametro('cod_lancamento', $arFiltro['stCodLancamento']);
} else {
    $preview->addParametro('cod_lancamento', '');
}

$inTotal = count(explode(',', $arFiltro['stCodReciboExtra']));
$inTotal += count(explode(',', $arFiltro['stCodReceita']));

if ($inTotal > 2) {
    $preview->addParametro('total_pagina', 2);
} else {
    $preview->addParametro('total_pagina', 1);
}

$obRARRConfiguracao = new RARRConfiguracao;
$obRARRConfiguracao->setCodModulo(2);
$obRARRConfiguracao->consultar();
$preview->addParametro('cod_febraban', $obRARRConfiguracao->getCodFebraban());
$preview->addParametro('break_page', 0);

$stCodigoBarra  = '00000000'.str_pad($arFiltro['inCodOrdem'].Sessao::getExercicio(), 8, '0', STR_PAD_LEFT);
$stCodigoBarra .= str_pad($arFiltro['inCodEntidade'], 3, '0', STR_PAD_LEFT).'0';
$preview->addParametro('codigo_barra', $stCodigoBarra);
$preview->addParametro('mostrar_codigo', 'mostrar');
$preview->addParametro('mostrar_rodape', 'mostrar');
$preview->preview();
?>
