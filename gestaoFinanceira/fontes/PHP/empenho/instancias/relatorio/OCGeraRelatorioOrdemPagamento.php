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
    * Página de Formulario de Seleção de Impressora para Relatorio
    * Data de Criação   : 13/05/2005

    * @author Desenvolvedor: João Rafael Tissot

    * @ignore

    * $Id: OCGeraRelatorioOrdemPagamento.php 61013 2014-11-28 16:52:04Z michel $
'
    * Casos de uso: uc-02.03.12
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkBirt.inc.php';
include_once CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioOrdensPagamento.class.php";

$preview = new PreviewBirt(2,10,2);
$preview->setTitulo('Relatório do Birt');
$preview->setVersaoBirt( '2.5.0' );
$preview->setExportaExcel( true );

//ENTIDADE
$stCodEntidades = implode(', ', $_REQUEST['inCodEntidade']);
$preview->addParametro('entidade_empenho', $stCodEntidades);

if (count($_REQUEST['inCodEntidade']) > 1) {
    $stWhere = "where exercicio='".Sessao::getExercicio()."' and parametro='cod_entidade_prefeitura'";
    $inCodEntidade = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stWhere);

    $preview->addParametro('entidade', $inCodEntidade);
} else {
    $preview->addParametro('entidade', $_REQUEST['inCodEntidade'][0]);
}

$arEntidadesAux = array();
$arEntidades = Sessao::read('filtroNomRelatorio');
$arEntidades = $arEntidades['entidade'];
foreach ($arEntidades as $inChave => $stEntidade) {
    if (array_search($inChave, $_REQUEST['inCodEntidade']) !== false) {
        $arEntidadesAux[] = $stEntidade;
    }
}

$stNomeEntidade = implode("<br/>", $arEntidadesAux);
$preview->addParametro('nome_entidade', $stNomeEntidade);

//EXERCICIO
$preview->addParametro('exercicio', Sessao::getExercicio());
$preview->addParametro('exercicio_empenho', $_REQUEST['stExercicioEmpenho']);
$preview->addParametro('exercicio_ordem', $_REQUEST['stAnoMes']);

//COD ORDEM
if ($_REQUEST['cod_ordem_inicio'] != "") {
    $preview->addParametro('cod_ordem_inicial', $_REQUEST['cod_ordem_inicio']);
} else {
    $preview->addParametro('cod_ordem_inicial', '');
}
if ($_REQUEST['cod_ordem_final'] != "") {
    $preview->addParametro('cod_ordem_final', $_REQUEST['cod_ordem_final']);
} else {
    $preview->addParametro('cod_ordem_final', '');
}


//cgm beneficiario
if ($_REQUEST['cgm_beneficiario'] != "") {
    $preview->addParametro('numcgm', $_REQUEST['cgm_beneficiario']);
} else {
    $preview->addParametro('numcgm', '');
}

//COD EMPENHO
if ($_REQUEST['cod_empenho_inicio'] != "") {
    $preview->addParametro('cod_empenho_inicial', $_REQUEST['cod_empenho_inicio']);
} else {
    $preview->addParametro('cod_empenho_inicial', '');
}
if ($_REQUEST['cod_empenho_final'] != "") {
    $preview->addParametro('cod_empenho_final', $_REQUEST['cod_empenho_final']);
} else {
    $preview->addParametro('cod_empenho_final', '');
}

//PERIODO
if ($_REQUEST['stDataInicial'] == "") 
    $_REQUEST['stDataInicial'] = '01/01/'.Sessao::getExercicio();

if ($_REQUEST['stDataFinal'] == "") 
    $_REQUEST['stDataFinal'] = '31/12/'.Sessao::getExercicio();
    
$preview->addParametro('dt_inicial', $_REQUEST['stDataInicial']);
$preview->addParametro('dt_final', $_REQUEST['stDataFinal']);

if ($_REQUEST['stDataInicial'] != $_REQUEST['stDataFinal']) {
    $preview->addParametro('periodo_cabecalho', $_REQUEST['stDataInicial']." até ".$_REQUEST['stDataFinal']);
} else {
    $preview->addParametro('periodo_cabecalho', $_REQUEST['stDataInicial']);
}

//PERIODO DATA PAGAMENTO
$preview->addParametro('dt_inicial_pagamento', $_REQUEST['stDataInicialDtPagamento']);
$preview->addParametro('dt_final_pagamento'  , $_REQUEST['stDataFinalDtPagamento']  );

//COD RECURSO
if (trim($_REQUEST['inCodRecurso']) != "") {
    $preview->addParametro('cod_recurso', trim($_REQUEST['inCodRecurso']));
    $preview->addParametro('descricao_recurso', $_REQUEST['stDescricaoRecurso']);
} else {
    $preview->addParametro('cod_recurso', '');
    $preview->addParametro('descricao_recurso', '');
}

if ($_REQUEST['inCodDetalhamento'] != "") {
    $preview->addParametro('cod_detalhamento', $_REQUEST['inCodDetalhamento']);
} else {
    $preview->addParametro('cod_detalhamento', '');
}

if ($_REQUEST['inCodUso'] && $_REQUEST['inCodDestinacao'] && $_REQUEST['inCodEspecificacao']) {
    $preview->addParametro('masc_recurso_red', $_REQUEST['inCodUso'].".".$_REQUEST['inCodDestinacao'].".".$_REQUEST['inCodEspecificacao']);
} else {
    $preview->addParametro('masc_recurso_red', '');
}

if ($_REQUEST['situacao'] != "") {
    $preview->addParametro('situacao', $_REQUEST['situacao']);
} else {
    $preview->addParametro('situacao', '');
}

//Mostra Coluna Docto Fiscal - TCE MG, quando for municipio de MG
if (SistemaLegado::pegaConfiguracao('cod_uf', 2, Sessao::getExercicio()) == '11'){
    $preview->addParametro('tcemg', 'true');
}else{
    $preview->addParametro('tcemg', 'false');
}

if ($_REQUEST['ordenacao'] == "op") {
   $stOrdem = " cod_ordem, dt_emissao, dt_pagamento, credor ";
}
if ($_REQUEST['ordenacao'] == "emissao") {
   $stOrdem = " dt_emissao, cod_ordem, dt_pagamento, credor";
}
if ($_REQUEST['ordenacao'] == "pagamento") {
   $stOrdem = " dt_pagamento, dt_emissao, cod_ordem,  credor";
}
if ($_REQUEST['ordenacao'] == "credor") {
   $stOrdem = " credor, cod_ordem, dt_emissao, dt_pagamento";
}

$preview->addParametro('ordenacao', $_REQUEST['ordenacao']);

$preview->addAssinaturas(Sessao::read('assinaturas'));
$preview->preview();

?>
