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
 * Página de Oculto para Gerar o Relatório de Retenções de Ordens de Pagamentos
 *
 * @category   Urbem
 * @package    Empenho
 * @author     Analista Tonismar R. Bernardo <tonismar.bernardo@cnm.org.br>
 * @author     Desenvolvedor Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * $Id:$
 * Casos de uso: uc-02.03.40
 */

/* includes do sistema */
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/* includes de classes */
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";

/* Inicializa a chamada do previwe do birt */
$preview = new PreviewBirt(2, 10, 5);
$preview->setTitulo('Retenções de Ordem de Pagamento');
$preview->setVersaoBirt('2.5.0');
$preview->setExportaExcel(true);

// Adiciona os parametros referentes as entidades da retenção
$stCodEntidades = implode(', ', $_REQUEST['inCodEntidade']);
$preview->addParametro('entidade_retencao', $stCodEntidades);

// Faz as verificações para o codigo da entidade que deve ser passado para montar o cabeçalho
if (count($_REQUEST['inCodEntidade']) > 1) {
    $stWhere = "where exercicio= '".$_REQUEST['stAno']."' and parametro='cod_entidade_prefeitura'";
    $inCodEntidade = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stWhere);
    $preview->addParametro('entidade', $inCodEntidade);
} else {
    $preview->addParametro('entidade', $_REQUEST['inCodEntidade'][0]);
}

// Faz a verificação dos nomes das entidades para que apareça nos filtros selecionados
$obTOrcamentoEntidade = new TOrcamentoEntidade;
$obTOrcamentoEntidade->setDado('exercicio', $_REQUEST['stAno']);

if (is_array($_REQUEST['inCodEntidade'])) {
    foreach ($_REQUEST['inCodEntidade'] as $inCodEntidade) {
        $obTOrcamentoEntidade->setDado('cod_entidade', $inCodEntidade);
        $obTOrcamentoEntidade->recuperaRelacionamentoNomes($rsEntidades);
        $arNomeEntidade[] = $rsEntidades->getCampo('entidade');
    }
} else {
    $obTOrcamentoEntidade->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
    $obTOrcamentoEntidade->recuperaRelacionamentoNomes($rsEntidades);
    $arNomeEntidade[] = $rsEntidades->getCampo('entidade');
}

$stNomeEntidade = implode("<br/>", $arNomeEntidade);
$preview->addParametro('nome_entidade', $stNomeEntidade);

// Adiciona outros parametros obrigatórios, por isso não é feita uma validação dos dados
if ( $_REQUEST['stAno'] == '') {
    $arTemp = explode("/", $_REQUEST['stDataFinal']);
    $stExercicio  = $arTemp[2];
}else{
    $stExercicio = $_REQUEST['stAno'];
}

$preview->addParametro('exercicio', $_REQUEST['stAno']);
$preview->addParametro('exercicio_retencao', $stExercicio);
$preview->addParametro('data_inicial', $_REQUEST['stDataInicial']);
$preview->addParametro('data_final', $_REQUEST['stDataFinal']);

// Adiciona o parametro do numero de empenho, é feita a validação se os dados estão vazios para poder passar ou o valor real ou um valor vazio
if ($_REQUEST['inCodEmpenhoInicial'] != "") {
    $preview->addParametro('cod_empenho_inicial', $_REQUEST['inCodEmpenhoInicial']);
} else {
    $preview->addParametro('cod_empenho_inicial', '');
}
if ($_REQUEST['inCodEmpenhoFinal'] != "") {
    $preview->addParametro('cod_empenho_final', $_REQUEST['inCodEmpenhoFinal']);
} else {
    $preview->addParametro('cod_empenho_final', '');
}

// Adiciona o parametro da ordem de empenho, é feita a validação se os dados estão vazios para poder passar ou o valor real ou um valor vazio
if ($_REQUEST['inCodOrdemInicial'] != "") {
    $preview->addParametro('cod_ordem_inicial', $_REQUEST['inCodOrdemInicial']);
} else {
    $preview->addParametro('cod_ordem_inicial', '');
}
if ($_REQUEST['inCodOrdemFinal'] != "") {
    $preview->addParametro('cod_ordem_final', $_REQUEST['inCodOrdemFinal']);
} else {
    $preview->addParametro('cod_ordem_final', '');
}

// Adiciona o parametro do codigo do credor, é feita a validação se os dados estão vazios para poder passar ou o valor real ou um valor vazio
if ($_REQUEST['inCodCredor'] != "") {
    $preview->addParametro('cod_credor', $_REQUEST['inCodCredor']);
    $stNomeCredor = SistemaLegado::pegaDado('nom_cgm', 'sw_cgm', 'WHERE numcgm = '.$_REQUEST['inCodCredor']);
    $preview->addParametro('nome_credor', $stNomeCredor);
} else {
    $preview->addParametro('cod_credor', '');
    $preview->addParametro('nome_credor', '');
}

// Adiciona o parametro da situacao do relatorio, é feita a validação se os dados estão vazios para poder passar ou o valor real ou um valor vazio
if ($_REQUEST['inSituacao'] != "") {
    $preview->addParametro('situacao', $_REQUEST['inSituacao']);
} else {
    $preview->addParametro('situacao', '');
}

// Adiciona o parametro de ordenacao do relatorio, é feita a validação se os dados estão vazios para poder passar ou o valor real ou um valor vazio
if ($_REQUEST['stOrdenacao'] != "") {
    $preview->addParametro('ordenacao', $_REQUEST['stOrdenacao']);
} else {
    $preview->addParametro('ordenacao', '');
}

// Adiciona o parametro de receita do relatorio, é feita a validação se os dados estão vazios para poder passar ou o valor real ou um valor vazio
if ($_REQUEST['stTipoReceita'] != "") {
    $preview->addParametro('receita', $_REQUEST['stTipoReceita']);
} else {
    $preview->addParametro('receita', '');
}

// Adiciona o parametro do codigo inicial da receita do relatorio, é feita a validação se os dados estão vazios para poder passar ou o valor real ou um valor vazio
if ($_REQUEST['inReceitaInicial'] != "") {
    $preview->addParametro('cod_receita_inicial', $_REQUEST['inReceitaInicial']);
} else {
    $preview->addParametro('cod_receita_inicial', '');
}

// Adiciona o parametro do codigo final da receita do relatorio, é feita a validação se os dados estão vazios para poder passar ou o valor real ou um valor vazio
if ($_REQUEST['inReceitaFinal'] != "") {
    $preview->addParametro('cod_receita_final', $_REQUEST['inReceitaFinal']);
} else {
    $preview->addParametro('cod_receita_final', '');
}

if ( $_REQUEST['boDataPagamento'] == 'S' ) {    
    $preview->addParametro('data_pagamento','true' );
}else{
    $preview->addParametro('data_pagamento','false' );
}

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();

?>
