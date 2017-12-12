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
    * Data de Criação   : 12/08/2004

    * @author Analista: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Marcelo

    * @ignore

    * $Id: OCBalanceteDespesa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.01.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioBalanceteDespesa.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioBalanceteDespesaDetalhadoOrcamento.class.php";

$obRConfiguracao = new ROrcamentoConfiguracao;
$obRConfiguracao->consultarConfiguracao($boTransacao ="");
//$obRConfiguracao->getFormaExecucaoOrcamento();

$obRRelatorio = new RRelatorio;

//Instancia Regra referente ao tipo de execucão
$arFiltro = Sessao::read('filtroRelatorio');
if ($obRConfiguracao->getFormaExecucaoOrcamento() == 1) {
   $obROrcamentoBalanceteDespesa = new ROrcamentoRelatorioBalanceteDespesa;
   $obROrcamentoBalanceteDespesa->setControleDetalhado ('');
   if ($arFiltro['boDemonstrarDesdobramentos'] == "N") {
        $obROrcamentoBalanceteDespesa->setDemonstrarDesdobramentos( "N" );
   } else $obROrcamentoBalanceteDespesa->setDemonstrarDesdobramentos( "S" );
   //Execucao Detalhado na Execução
} else {
   $obROrcamentoBalanceteDespesa = new ROrcamentoRelatorioBalanceteDespesaDetalhadoOrcamento;
   $obROrcamentoBalanceteDespesa->setControleDetalhado ('1');
   //Execucao Detalhado no Orcamento
}

//seta elementos do filtro
$stFiltro = "";

//seta elementos do filtro para ENTIDADE
if ($arFiltro['inCodEntidade'] != "") {
    $obROrcamentoBalanceteDespesa->obREntidade->setCodigoEntidade($arFiltro['inCodEntidade']);
    $stFiltro .= " AND od.cod_entidade IN  (";
    foreach ($arFiltro['inCodEntidade'] as $key => $valor) {
        $stFiltro .= $valor.",";
    }
    $stFiltro = substr( $stFiltro, 0, strlen($stFiltro) - 1 ) . ")";
} else {
    $stFiltro .= $arFiltro['stTodasEntidades'];
}
if ($arFiltro['inCodRecurso'] != "") {
    $stFiltro .= " AND od.cod_recurso = " . $arFiltro['inCodRecurso'];
}

if ($arFiltro['inCodFuncao'] != "") {
    $stFiltro .= " AND od.cod_funcao = " . $arFiltro['inCodFuncao'];
}

if ($arFiltro['inCodSubFuncao'] != "") {
    $stFiltro .= " AND od.cod_subfuncao = " . $arFiltro['inCodSubFuncao'];
}

if ($arFiltro['inCodPrograma'] != "") {
    //$stFiltro .= " AND od.cod_programa = " . $arFiltro['inCodPrograma'];
    $stFiltro .= ' AND ppa.programa.num_programa = '.$arFiltro['inCodPrograma'];
}

if ($arFiltro['inCodPao'] != "") {
    //$stFiltro .= " AND od.num_pao = " . $arFiltro['inCodPao'];
    $stFiltro .= ' AND ppa.acao.num_acao = '.$arFiltro['inCodPao'];
}

if ($arFiltro['inCodUso'] != '' && $arFiltro['inCodDestinacao'] != '' && $arFiltro['inCodEspecificacao'] != '') {
    $stFiltro .= " AND oru.masc_recurso_red like \'".$arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao']."%\' ";
}

if ($arFiltro['inCodDetalhamento'] != '') {
    $stFiltro .= " AND oru.cod_detalhamento = ".$arFiltro['inCodDetalhamento'];
}

$obROrcamentoBalanceteDespesa->setFiltro                 ( $stFiltro );

if ($arFiltro['inAno']) {
   $obROrcamentoBalanceteDespesa->setExercicio              ( $arFiltro['inAno'] );
} else {
  $obROrcamentoBalanceteDespesa->setExercicio              ( Sessao::getExercicio() );
}

$obROrcamentoBalanceteDespesa->setCodReduzidoInicial     ( $arFiltro['inCodDotacaoInicial'] );
$obROrcamentoBalanceteDespesa->setCodReduzidoFinal       ( $arFiltro['inCodDotacaoFinal'] );
$obROrcamentoBalanceteDespesa->setCodEstruturalInicial   ( $arFiltro['stCodEstruturalInicial'] );
$obROrcamentoBalanceteDespesa->setCodEstruturalFinal     ( $arFiltro['stCodEstruturalFinal'] );

$obROrcamentoBalanceteDespesa->setDataInicial            ( $arFiltro['stDataInicial'] );
$obROrcamentoBalanceteDespesa->setDataFinal              ( $arFiltro['stDataFinal'] );
$obROrcamentoBalanceteDespesa->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($arFiltro["inNumOrgao"]);
$obROrcamentoBalanceteDespesa->obROrcamentoUnidade->setNumeroUnidade($arFiltro["inNumUnidade"]);

$obROrcamentoBalanceteDespesa->geraRecordSet( $rsBalanceteDespesa,$rsCabecalho,$rsTotalFinal );

Sessao::write('rsResumoRecurso',$obROrcamentoBalanceteDespesa->getRsResumoRecurso());
Sessao::write('rsTotalFinal',$rsTotalFinal);
Sessao::write('rsCabecalho',$rsCabecalho);
Sessao::write('rsBalanceteDespesa',$rsBalanceteDespesa);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioBalanceteDespesa.php" );

?>
