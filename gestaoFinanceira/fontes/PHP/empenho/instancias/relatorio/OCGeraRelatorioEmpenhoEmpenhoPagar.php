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
    * Data de Criação   : 18/02/2005

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Rafael Almeida

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: lbbarreiro $
    $Date: 2008-01-14 11:48:49 -0200 (Seg, 14 Jan 2008) $

    * Casos de uso : uc-02.03.07
*/

/*
$Log$
Revision 1.8  2006/07/05 20:49:08  cleisson
Adicionada tag Log aos arquivos

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoRelatorioEmpenhoPagar.class.php"    );

$obRegra      = new REmpenhoRelatorioEmpenhoPagar;
$obPDF        = new ListaPDF( "L" );

$arFiltro = Sessao::read('filtroRelatorio');
$arFiltroNom = Sessao::read('filtroNomRelatorio');
$rsRecordSet = Sessao::read('rsRecordSet');

// Adicionar logo nos relatorios
if ( count( $arFiltro['inCodEntidade'] ) == 1 ) {
    $obRegra->obRRelatorio->setCodigoEntidade( $arFiltro['inCodEntidade'][0] );
    $obRegra->obRRelatorio->setExercicioEntidade ( Sessao::getExercicio() );
}

$obRegra->obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRegra->obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Empenhos - ". Sessao::getExercicio());
$obPDF->setTitulo            ( "Empenhos a Pagar" );
$obPDF->setSubTitulo             ( "Situação até: " . $arFiltro['stDataSituacao'] ."  ".$arFiltro['relatorio']   );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$obPDF->addRecordSet( $rsRecordSet );

foreach ($arFiltro['inCodEntidade'] as $inCodEntidade) {
    $arNomEntidade[] = $arFiltroNom['entidade'][$inCodEntidade];
}

switch ($arFiltro[ 'inOrdenacao' ]) {
    case 1:
        $stOrdenacao = "Empenho";
        break;
    case 2:
        $stOrdenacao = "Credor";
        break;
}

$obPDF->addFiltro( 'Entidades Relacionadas'             , $arNomEntidade                    );
$obPDF->addFiltro( 'Exercício'                          , $arFiltro[ 'inExercicio' ]  );

if ($arFiltro['inNumOrgao']) {
    $obPDF->addFiltro('Orgão', $arFiltroNom['orgao'][$arFiltro['inNumOrgao']]);
}

if($arFiltro['stDataInicial'])
    $obPDF->addFiltro( 'Periodicidade Emissão'              , $arFiltro[ 'stDataInicial' ] . " até " . $arFiltro[ 'stDataFinal' ] );

$obPDF->addFiltro( 'Situação Até'                       , $arFiltro[ 'stDataSituacao' ] );

if($arFiltro['inCodEmpenhoInicial'])
    $obPDF->addFiltro( 'Número do Empenho'                  , $arFiltro[ 'inCodEmpenhoInicial' ] . " até " . $arFiltro[ 'inCodEmpenhoFinal' ]);

$obPDF->addFiltro( 'Ordenação'                              , $stOrdenacao);

if ($arFiltro['inCodFornecedor']) {
    $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->setNumCGM($arFiltro['inCodFornecedor']);
    $obRegra->obREmpenhoEmpenho->obROrcamentoEntidade->obRCGM->consultar($rsCGMFiltro);
    $obPDF->addFiltro( 'Credor' , $arFiltro[ 'inCodFornecedor' ] . " - " . $rsCGMFiltro->getCampo( "nom_cgm" ));
}

$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("EMPENHO",12, 8);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("DATA", 8, 8);
$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho("CREDOR",25, 8);
$obPDF->setAlinhamento ( "C" );
$obPDF->addCabecalho("EMPENHADO",9, 8);
$obPDF->addCabecalho("LIQUIDADO", 9, 8);
$obPDF->addCabecalho("PAGO", 9, 8);
$obPDF->addCabecalho("A PAGAR", 9, 8);
$obPDF->addCabecalho("A PAGAR LIQUIDADO", 11, 8);
$obPDF->addQuebraLinha("nivel",2,5);

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("empenho", 8 );
$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo("data", 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo("credor", 8 );
$obPDF->setAlinhamento ( "R" );
$obPDF->addCampo("empenhado", 8 );
$obPDF->addCampo("liquidado", 8 );
$obPDF->addCampo("pago", 8 );
$obPDF->addCampo("apagar", 8 );
$obPDF->addCampo("apagarliquidado", 8 );

$arAssinaturas = Sessao::read('assinaturas');
if ( count($arAssinaturas['selecionadas']) > 0 ) {
    include_once( CAM_FW_PDF."RAssinaturas.class.php" );
    $obRAssinaturas = new RAssinaturas;
    $obRAssinaturas->setArAssinaturas( $arAssinaturas['selecionadas'] );
    $obPDF->setAssinaturasDefinidas( $obRAssinaturas->getArAssinaturas() );
    $obRAssinaturas->montaPDF( $obPDF );
}

$obPDF->show();
?>
