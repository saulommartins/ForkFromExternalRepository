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
    * Data de Criação   : 05/12/2005

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    * $Id: OCGeraRelatorioResumoDespesa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.16
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF();

$arDados  = Sessao::read('arDados');
$arFiltro = Sessao::read('filtroRelatorio');
$arEntidades = Sessao::read('arEntidades');

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodigoEntidadesSelecionadas'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodigoEntidadesSelecionadas'][0]);
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio" );
$obPDF->setTitulo            ( "Dados para Resumo das Despesas" );
$obPDF->setSubTitulo         ( "Periodicidade: ".$arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal']);
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$stBoletim = "";

$rsBoletim = new RecordSet;

$rsBoletim = $arDados[1];

foreach ($rsBoletim->getElementos() as $key => $valor) {

    foreach ($valor as $k => $v) {
        $stBoletim .= $v . "#";
    }
}

$arBoletim = explode("#",$stBoletim);

if ($arBoletim[0] == $arBoletim[1]) {
    $stBoletim = $arBoletim[0];
} else {
    $stBoletim = $arBoletim[0] . " a " .$arBoletim[1];
}

$obPDF->addFiltro( 'Boletim(ns): '    , $stBoletim );

foreach ($arFiltro['inCodigoEntidadesSelecionadas'] as $inCodEntidade) {
    $arNomEntidade[] = $arEntidades[$inCodEntidade];
}

$obPDF->addFiltro( 'Entidades Relacionadas'             , $arNomEntidade );

$obPDF->addFiltro( 'Exercicio: '    ,  $arFiltro['stExercicio'] );

if ($arFiltro['stDataInicial']) {
    $obPDF->addFiltro( 'Periodicidade: '    ,  $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
}

switch ($arFiltro['stTipoRelatorio']) {

    case "B":
            $tipoRelatorio = "Total de Despesas por Banco";
            break;
    case "R":
            $tipoRelatorio = "Total de Despesas por Recurso";
            break;
    case "E":
            $tipoRelatorio = "Total de Despesas por Entidade";
            break;
    default:
            $tipoRelatorio = "Total de Despesas";

}
$obPDF->addFiltro( 'Tipo de Relatório: '    ,  $tipoRelatorio );

if (($arFiltro['inDespesaInicial'] != 0) || ($arFiltro['inDespesaFinal'] != 0)) {
    $obPDF->addFiltro( 'Despesa: '    ,  $arFiltro['inDespesaInicial']." até ".$arFiltro['inDespesaFinal'] );
}
if (($arFiltro['inContaBancoInicial'] != 0) || ($arFiltro['inContaBancoFinal'] != 0)) {
    $obPDF->addFiltro( 'Conta Banco: '    ,  $arFiltro['inContaBancoInicial']." até ".$arFiltro['inContaBancoFinal'] );
}
if ($arFiltro['inCodRecurso'] != "") {
    $obPDF->addFiltro( 'Recurso: '    ,  $arFiltro['inCodRecurso']." - ".$arFiltro['stDescricaoRecurso'] );
}

if ( isset($arFiltro['inCodUso']) != "" && $arFiltro['inCodDestinacao'] != "" && $arFiltro['inCodEspecificacao'] != "") {
    $stDescricao = SistemaLegado::pegaDado('descricao', 'orcamento.destinacao_recurso', "WHERE exercicio='".$arFiltro['stExercicio']."' AND cod_destinacao=".$arFiltro['inCodDestinacao']);
    $stDescricaoDestinacao = $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao']." - ".$stDescricao;
    $obPDF->addFiltro('Destinação de Recurso', $stDescricaoDestinacao);
}

$obPDF->addRecordSet( $arDados[0] );

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 8, 8);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 52, 8);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 4, 8);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 12, 8);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 12, 8);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 12, 8);
$obPDF->addQuebraPagina("pagina",1);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("despesa", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("descricao", 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("tipo_despesa", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("pago", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("estornado", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("total", 8 );

$arAssinaturas = Sessao::read('assinaturas');

if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
}

$obPDF->show();

?>
