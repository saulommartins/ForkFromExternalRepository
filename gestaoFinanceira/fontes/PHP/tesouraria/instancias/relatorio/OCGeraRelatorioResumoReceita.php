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
    * Data de Criação   : 15/12/2005

    * @author Analista: Lucas Leusin Oiagen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @ignore

    * $Id: OCGeraRelatorioResumoReceita.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.04.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF();

$arFiltro          = Sessao::read('filtroRelatorio');
$arFiltroEntidades = Sessao::read('filtroEntidades');
$arDados           = Sessao::read('arDados');

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodigoEntidadesSelecionadas'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodigoEntidadesSelecionadas'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio     (Sessao::getExercicio());
$obRRelatorio->recuperaCabecalho($arConfiguracao);
$obPDF->setModulo               ("Relatorio");
$obPDF->setTitulo               ("Dados para Resumo das Receitas");
$obPDF->setSubTitulo            ("Periodicidade: ".$arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal']);
$obPDF->setUsuario              (Sessao::getUsername());
$obPDF->setEnderecoPrefeitura   ($arConfiguracao);

foreach ($arFiltro['inCodigoEntidadesSelecionadas'] as $inCodEntidade) {
    $arNomEntidade[] = $arFiltroEntidades['entidade'][$inCodEntidade];
}

$obPDF->addFiltro('Entidades Relacionadas', $arNomEntidade);
$obPDF->addFiltro('Exercicio', $arFiltro['stExercicio']);

if ($arFiltro['stDataInicial']) {
    $obPDF->addFiltro('Periodicidade', $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal']);
}

switch ($arFiltro['stTipoRelatorio']) {
case "B":
    $tipoRelatorio = "Total de Receitas por Banco";
    break;
case "R":
    $tipoRelatorio = "Total de Receitas por Recurso";
    break;
case "E":
    $tipoRelatorio = "Total de Receitas por Entidade";
    break;
default:
    $tipoRelatorio = "Total de Receitas";

}
$obPDF->addFiltro('Tipo de Relatório', $tipoRelatorio);

if (($arFiltro['inReceitaInicial'] != 0) || ($arFiltro['inReceitaFinal'] != 0)) {
    $obPDF->addFiltro('Receita', $arFiltro['inReceitaInicial']." até ".$arFiltro['inReceitaFinal']);
}

if (($arFiltro['inContaBancoInicial'] != 0) || ($arFiltro['inContaBancoFinal'] != 0)) {
    $obPDF->addFiltro('Conta Banco', $arFiltro['inContaBancoInicial']." até ".$arFiltro['inContaBancoFinal']);
}

if ($arFiltro['inCodRecurso'] != "") {
    $obPDF->addFiltro('Recurso',  $arFiltro['inCodRecurso']." - ".$arFiltro['stDescricaoRecurso']);
}

if ($arFiltro['inCodUso'] != "" && $arFiltro['inCodDestinacao'] != "" && $arFiltro['inCodEspecificacao'] != "") {
    $stDescricao = SistemaLegado::pegaDado('descricao', 'orcamento.destinacao_recurso', "WHERE exercicio='".$arFiltro['stExercicio']."' AND cod_destinacao=".$arFiltro['inCodDestinacao']);
    $stDescricaoDestinacao = $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao']." - ".$stDescricao;
    $obPDF->addFiltro('Destinação de Recurso', $stDescricaoDestinacao);
}

$obPDF->addRecordSet($arDados[0]);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 8, 10);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 48, 10);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 4, 10);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 13, 10);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 13, 10);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("", 13, 10);
$obPDF->addQuebraPagina("pagina",1);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("receita", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("descricao", 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("tipo", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("arrecadado", 8 );
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
