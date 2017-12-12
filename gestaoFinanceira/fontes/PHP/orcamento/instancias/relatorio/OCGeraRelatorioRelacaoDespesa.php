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
    * Data de Criação   : 13/08/2004

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @ignore

    $Revision: 31801 $
    $Name$
    $Autor: $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.01.18
*/

/*
$Log$
Revision 1.9  2006/11/27 20:06:48  cleisson
Bug #7553#

Revision 1.8  2006/07/05 20:43:28  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../config.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF( "L" );

// Adicionar logo nos relatorios
$arFiltro = Sessao::read('filtroRelatorio');
$arNomFiltro = Sessao::read('filtroNomRelatorio');
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio" );
$obPDF->setTitulo            ( "Relação de Despesa" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsRelacaoDespesa = Sessao::read('rsRelacaoDespesa');
$obPDF->addRecordSet( $rsRelacaoDespesa );

foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    $arNomEntidade[] = $arNomFiltro['entidade'][$inCodEntidade];
}
$obPDF->addFiltro( 'Entidades Relacionadas'             , $arNomEntidade                                               );

if($arFiltro['stDescricaoRecurso'])
    $obPDF->addFiltro( 'Recurso: '    ,  $arFiltro['inCodRecurso']. " - " . $arFiltro['stDescricaoRecurso'] );

if($arFiltro['inNumOrgao'])
    $obPDF->addFiltro( 'Órgão Orçamentário'    , $arFiltro['inNumOrgao'] . " - " . $arNomFiltro['orgao'][$arFiltro[ 'inNumOrgao' ]] );
if($arFiltro['inNumUnidade'])
    $obPDF->addFiltro( 'Unidade Orçamentária'  , $arFiltro['inNumUnidade'] . " - " . $arNomFiltro['unidade'][$arFiltro[ 'inNumUnidade' ]] );

if($arFiltro['inCodFuncao'])
    $obPDF->addFiltro('Função', $arFiltro['inCodFuncao']." - ".$arNomFiltro['funcao'][$arFiltro['inCodFuncao']]);
if($arFiltro['inCodSubFuncao'])
    $obPDF->addFiltro('Subfunção', $arFiltro['inCodSubFuncao']." - ".$arNomFiltro['subfuncao'][$arFiltro['inCodSubFuncao']]);
if($arFiltro['inCodPrograma'])
    $obPDF->addFiltro('Programa', $arFiltro['inCodPrograma']." - ".$arNomFiltro['programa'][$arFiltro['inCodPrograma']]);
if($arFiltro['inCodPao'])
    $obPDF->addFiltro('PAO', $arFiltro['inCodPao']." - ".$arNomFiltro['pao'][$arFiltro['inCodPao']]);
if($arFiltro['inCodDespesa'])
    $obPDF->addFiltro('Elemento de Despesa', $arFiltro['inCodDespesa']);

if($arFiltro['inCodUso'] && $arFiltro['inCodDestinacao'] && $arFiltro['inCodEspecificacao'])
    $obPDF->addFiltro( 'Destinação de Recursos', $arFiltro['inCodUso'].".".$arFiltro['inCodDestinacao'].".".$arFiltro['inCodEspecificacao'] );

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("DOTAÇÃO", 13, 10);
$obPDF->addCabecalho("", 47, 10);

include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php"        );
$obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
$obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
$obTOrcamentoConfiguracao->setDado("parametro","recurso_destinacao");
$obTOrcamentoConfiguracao->consultar();

if($obTOrcamentoConfiguracao->getDado("valor") == 'true') // Recurso com Destinação de Recurso || 2008 em diante
    $obPDF->addCabecalho ( "DEST. RECURSO", 14, 10);
else 
    $obPDF->addCabecalho ( "RECURSO", 15, 10);

$obPDF->setAlinhamento  ( "C" );
$obPDF->addCabecalho    ( "COD. REDUZ"      ,6 ,10 );
$obPDF->addCabecalho    ( "DOTAÇÃO INICIAL" ,10,10 );
$obPDF->addCabecalho    ( "SALDO DISPONÍVEL",10,10 );
$obPDF->addIndentacao   ( "nivel","[classificacao]  [descricao_despesa]","    " );
$obPDF->addQuebraLinha  ( "nivel" ,0 ,5 );
$obPDF->addQuebraPagina ( "pagina" ,1 );

$obPDF->setAlinhamento  ( "L" );
$obPDF->addCampo        ( "classificacao"    , 8 );
$obPDF->addCampo        ( "descricao_despesa", 8 );
$obPDF->addCampo        ( "[cod_recurso]  [nom_recurso]", 8 );
$obPDF->setAlinhamento  (  "R" );
$obPDF->addCampo        ( "cod_despesa"     , 8 );
$obPDF->addCampo        ( "valor_previsto"  , 8 );
$obPDF->addCampo        ( "saldo_disponivel", 8 );

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
}

$obPDF->show();

?>
