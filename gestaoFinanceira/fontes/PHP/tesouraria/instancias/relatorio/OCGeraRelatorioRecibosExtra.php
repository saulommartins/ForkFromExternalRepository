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
    * Data de Criação   : 01/09/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Revision: 31732 $
    $Name$
    $Autor:$
    $Date: 2008-01-07 17:46:49 -0200 (Seg, 07 Jan 2008) $

    * Casos de uso: uc-02.04.32
*/

/*
$Log$
Revision 1.3  2007/03/30 21:04:56  luciano
#8852#

Revision 1.2  2006/11/28 22:11:44  cleisson
Bug #7583#

Revision 1.1  2006/09/04 17:20:21  fernando
formulário oculto de geração do relatório de recibos extra

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );

$obRRelatorio = new RRelatorio;
$obPDF        = new ListaPDF();

$arFiltro = Sessao::read('filtroRelatorio');

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio" );
$obPDF->setTitulo            ( "Dados para Resumo das Receitas" );
$obPDF->setSubTitulo         ( "Periodicidade: ".$arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal']);
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    $arNomEntidade[] = $arFiltro['entidade'][$inCodEntidade];
}

$obPDF->addFiltro( 'Entidades Relacionadas'             , $arNomEntidade );

$obPDF->addFiltro( 'Exercícioi: '    ,  $arFiltro['stExercicio'] );

if ($arFiltro['stDataInicial']) {
    $obPDF->addFiltro( 'Periodicidade: '    ,  $arFiltro['stDataInicial']." até ".$arFiltro['stDataFinal'] );
}
if ($arFiltro['inCodCredor'] != "") {
    $obPDF->addFiltro( 'Credor: '    ,  $arFiltro['inCodCredor']." - ".$arFiltro['stNomCredor'] );
}
if ($arFiltro['inCodRecurso'] != "") {
    $obPDF->addFiltro( 'Recurso: '    ,  $arFiltro['inCodRecurso']." - ".$arFiltro['stDescricaoRecurso'] );
}
if ($arFiltro['inCodContaBanco'] != 0) {
    $obPDF->addFiltro( 'Conta Caixa/Banco: ' ,  $arFiltro['inCodContaBanco']." - ". $arFiltro['stNomContaBanco']);
}
if ($arFiltro['inCodContaAnalitica'] != 0) {
    $obPDF->addFiltro( 'Conta de Receita/Despesa: '    ,  $arFiltro['inCodContaAnalitica']." - ". $arFiltro['stNomContaAnalitica']);
}
if ($arFiltro['stTipoDemonstracao'] != '') {
    $stTipoDemonstracao = $arFiltro['stTipoDemonstracao'];
    if (stripslashes($stTipoDemonstracao) == "'R'") {
        $stTipoDemonstracao = "Somente Receitas";
    } else {
        $stTipoDemonstracao = stripslashes($stTipoDemonstracao) == "'D'" ? "Somente Despesas" : "Receitas e Despesas";
    }
    $obPDF->addFiltro('Demonstrar:', $stTipoDemonstracao );
}

$arDados = Sessao::read('arDados');
$arDados->addFormatacao('valor','NUMERIC_BR');

$obPDF->addRecordSet( $arDados );

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("Emissão"      , 8, 9);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("Credor"       ,33, 9);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("Caixa/Banco"  ,12, 9);
$obPDF->addCabecalho("Conta"        , 6, 9);
$obPDF->addCabecalho("Cod Recibo"   ,10, 9);
$obPDF->addCabecalho("Tipo"         , 5, 9);
$obPDF->addCabecalho("Recurso"      , 8, 9);
$obPDF->addCabecalho("Valor (R$)"   ,10, 9);
$obPDF->addCabecalho("Autentic."    , 8, 9);

$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("data", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("credor", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("caixa", 8 );
$obPDF->addCampo("conta", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("cod_recibo", 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("tipo_recibo", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("recurso", 8 );
$obPDF->addCampo("valor", 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("autenticado", 8 );

$arAssinaturas = Sessao::read('assinaturas');

if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
}

$obPDF->show();
