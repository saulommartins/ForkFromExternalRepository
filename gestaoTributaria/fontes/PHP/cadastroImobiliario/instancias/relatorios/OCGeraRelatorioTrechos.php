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
 * Página de processamento oculto para o relatório de trechos
 * Data de Criação   : 31/03/2005

 * @author Analista: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Marcelo Boezio Paulino

 * @ignore

 * $Id: OCGeraRelatorioTrechos.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.22
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_FW_PDF."ListaPDF.class.php";

$obRRelatorio = new RRelatorio;
$filtroRelatorio = Sessao::read('filtroRelatorio');
$arCabecalho = Sessao::read('arCabecalho');
$inCountCabecalho = count( $arCabecalho );

if ($inCountCabecalho == 0) {
    $widthCodigo     = 6;
    $widthSequencia  = 4;
    $widthExtensao   = 4;
    $widthLogradouro = 38;
    $widthVigencia   = 10;
    $obPDF           = new ListaPDF();
} else {
    $widthCodigo     = 6;
    $widthSequencia  = 4;
    $widthExtensao   = 4;
    $widthLogradouro = 38;
    $widthVigencia   = 8;
    $obPDF           = new ListaPDF("L");
}

$obRRelatorio->setExercicio  ( Sessao::getExercicio() );
$obRRelatorio->recuperaCabecalho( $arConfiguracao );
$obPDF->setModulo            ( "Relatorio:"   );
$obPDF->setTitulo            ( "Trechos:" );
$obPDF->setSubTitulo         ( "Exercício - ".Sessao::getExercicio() );
$obPDF->setUsuario           ( Sessao::getUsername() );
$obPDF->setEnderecoPrefeitura( $arConfiguracao );

$rsTrechos = Sessao::read('rsTrechos');
$obPDF->addRecordSet($rsTrechos);

$obPDF->setAlinhamento ( "L" );
$obPDF->addCabecalho   ( "CÓD."     , $widthCodigo     , 10 );
$obPDF->addCabecalho   ( "SEQ."  , $widthSequencia  , 10 );
$obPDF->addCabecalho   ( "EXT."   , $widthExtensao   , 10 );
$obPDF->addCabecalho   ( "LOGRADOURO" , $widthLogradouro , 10 );

if (Sessao::read('boRSMD') == true ) {
    $obPDF->addCabecalho   ( "Vlr.M² Terr."    ,8, 10 );
    $obPDF->addCabecalho   ( "Vlr.M² Pred."    ,8, 10 );
}
if ( Sessao::read('boAliquota') == true ) {
    $obPDF->addCabecalho   ( "Aliq. Terr."    ,8, 10 );
    $obPDF->addCabecalho   ( "Aliq. Pred."    ,8, 10 );
}

$obPDF->addCabecalho   ( "VIGÊNCIA", $widthVigencia, 10);

//monta cabecalhos dinamicos
$inWidth     = $arCabecalho['width'];
unset( $arCabecalho['width'] );
foreach ($arCabecalho as $key => $valor) {
    $valor = strtr($valor, "çãáóé", "ÇÂÁÉÓ");
    $inNomCabecalho = strtoupper( $valor );
    $obPDF->addCabecalho   ( $inNomCabecalho , $inWidth, 10  );
}

$obPDF->setAlinhamento ( "C" );
$obPDF->addCampo       ( "trecho"     , 8 );
$obPDF->addCampo       ( "sequencia"  , 8 );
$obPDF->addCampo       ( "extensao"   , 8 );
$obPDF->setAlinhamento ( "L" );
$obPDF->addCampo       ( "logradouro" , 8 );
$obPDF->setAlinhamento ( "C" );

if (Sessao::read('boRSMD') == true ) {
    $obPDF->addCampo       ( "valor_m2_territorial" , 8 );
    $obPDF->addCampo       ( "valor_m2_predial"     , 8 );
}

if (Sessao::read('boAliquota') == true ) {
    $obPDF->addCampo       ( "aliquota_territorial" , 8 );
    $obPDF->addCampo       ( "aliquota_predial"     , 8 );
}

$obPDF->addCampo       ( "vigencia", 8);
foreach ($arCabecalho as $key => $valor) {
    $obPDF->addCampo( $valor , 8 );
}

$arFiltro = Sessao::read('arFiltro');
$arNomAtributo = array();

if (!empty( $filtroRelatorio['inCodAtributosSelecionados'])) {
    foreach ($filtroRelatorio['inCodAtributosSelecionados'] as $inCodAtributo) {
        $arNomAtributo[] = $arFiltro['nomFiltro']['atributo'][$inCodAtributo];
    }
}

$obPDF->addFiltro( 'Código Sequencia Inicial'  , $filtroRelatorio['inCodInicio']            );
$obPDF->addFiltro( 'Código Sequencia Final'    , $filtroRelatorio['inCodTermino']           );
$obPDF->addFiltro( 'Código Logradouro Inicial' , $filtroRelatorio['inCodInicioLogradouro']  );
$obPDF->addFiltro( 'Código Logradouro Final'   , $filtroRelatorio['inCodTerminoLogradouro'] );
$obPDF->addFiltro( 'Tipo de Relatório'         , $arFiltro['nomFiltro']['tipo_relatorio'][$filtroRelatorio['stTipoRelatorio']]);
$obPDF->addFiltro( 'Atributos Relacionados'    , $arNomAtributo );
$obPDF->addFiltro( 'Ordenação'                 , $arFiltro['nomFiltro']['ordenacao'][$filtroRelatorio[ 'stOrder']] );

$rsDados = Sessao::read('rsTrechos');

$rsTotais = new RecordSet;
$arTeste  = array();
$inTotalRegs = $rsDados->getNumLinhas();

if ($inTotalRegs < 0) {
    $inTotalRegs = 0;
}

$arTeste[0]["total"] = $inTotalRegs;
$rsTotais->preenche( $arTeste );

$obPDF->addRecordSet ($rsTotais);

$obPDF->setQuebraPaginaLista ( false         );
$obPDF->setAlinhamento ( "L"           );
$obPDF->addCabecalho   ( "Total de registros", 100, 10  );

$obPDF->setAlinhamento ( "L"           );
$obPDF->addCampo       ( "total",  8  );

$obPDF->show();

?>
